<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cuti_model extends CI_Model
{
    public function summary($pegawai_id)
    {
        return $this->db
            ->where('pegawai_id', $pegawai_id)
            ->get('tb_saldo_cuti')
            ->row_array();
    }

    public function requests($pegawai_id)
    {
        return $this->db
            ->select('tb_cuti.id, tb_cuti.jenis_cuti, tb_cuti.tgl_mulai, tb_cuti.tgl_selesai, tb_cuti.status, tb_cuti.catatan, approver.nama AS approver_nama')
            ->from('tb_cuti')
            ->join('tb_users approver_user', 'approver_user.id = tb_cuti.approver_id', 'left')
            ->join('tb_pegawai approver', 'approver.id = approver_user.pegawai_id', 'left')
            ->where('tb_cuti.pegawai_id', $pegawai_id)
            ->where('tb_cuti.deleted_at', NULL)
            ->order_by('tgl_mulai', 'DESC')
            ->get()
            ->result_array();
    }

    public function pending_approvals(array $viewer)
    {
        $this->db
            ->select('tb_cuti.id, tb_cuti.jenis_cuti, tb_cuti.tgl_mulai, tb_cuti.tgl_selesai, tb_cuti.status, tb_cuti.catatan, tb_pegawai.nama, tb_units.nama_unit')
            ->from('tb_cuti')
            ->join('tb_pegawai', 'tb_pegawai.id = tb_cuti.pegawai_id')
            ->join('tb_units', 'tb_units.id = tb_pegawai.unit_id', 'left')
            ->where('tb_cuti.deleted_at', NULL)
            ->where_in('tb_cuti.status', array('PENDING', 'APPROVED_UNIT'))
            ->order_by('tb_cuti.created_at', 'DESC');

        if ((int) $viewer['level'] === 3) {
            $this->db->where('tb_pegawai.unit_id', (int) $viewer['unit_id']);
        }

        return $this->db->get()->result_array();
    }

    public function submit_request(array $data)
    {
        return $this->db->insert('tb_cuti', array(
            'pegawai_id' => (int) $data['pegawai_id'],
            'jenis_cuti' => $data['jenis_cuti'],
            'tgl_mulai' => $data['tgl_mulai'],
            'tgl_selesai' => $data['tgl_selesai'],
            'status' => 'PENDING',
            'catatan' => $data['catatan'],
        ));
    }

    public function update_request_status($id, $status, array $viewer)
    {
        $leave = $this->db
            ->select('tb_cuti.*, tb_pegawai.unit_id')
            ->from('tb_cuti')
            ->join('tb_pegawai', 'tb_pegawai.id = tb_cuti.pegawai_id')
            ->where('tb_cuti.id', (int) $id)
            ->where('tb_cuti.deleted_at', NULL)
            ->get()
            ->row_array();

        if (!$leave) {
            return FALSE;
        }

        if ((int) $viewer['level'] === 3 && (int) $leave['unit_id'] !== (int) $viewer['unit_id']) {
            return FALSE;
        }

        $final_status = $status;
        if ($status === 'APPROVE') {
            $final_status = (int) $viewer['level'] <= 2 ? 'APPROVED_HR' : 'APPROVED_UNIT';
        }

        $this->db->trans_start();
        $this->db->where('id', (int) $id)->update('tb_cuti', array(
            'status' => $final_status,
            'approver_id' => (int) $viewer['id'],
        ));

        if ($final_status === 'APPROVED_HR' && $leave['jenis_cuti'] === 'TAHUNAN') {
            $year = date('Y', strtotime($leave['tgl_mulai']));
            $days = ((strtotime($leave['tgl_selesai']) - strtotime($leave['tgl_mulai'])) / 86400) + 1;

            $balance = $this->db
                ->from('tb_saldo_cuti')
                ->where('pegawai_id', (int) $leave['pegawai_id'])
                ->where('tahun', $year)
                ->get()
                ->row_array();

            if (!$balance) {
                $this->db->insert('tb_saldo_cuti', array(
                    'pegawai_id' => (int) $leave['pegawai_id'],
                    'tahun' => $year,
                    'saldo_tahunan' => 12,
                    'terpakai_tahunan' => 0,
                ));
            }

            $this->db->set('terpakai_tahunan', 'terpakai_tahunan + ' . (int) $days, FALSE)
                ->where('pegawai_id', (int) $leave['pegawai_id'])
                ->where('tahun', $year)
                ->update('tb_saldo_cuti');
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
