<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_model extends CI_Model
{
    public function monthly($pegawai_id, $month = NULL)
    {
        $month = $month ?: date('Y-m');
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        return $this->db
            ->select('tb_jadwal.id, tb_jadwal.tanggal, tb_jadwal.status, tb_shift.nama_shift, tb_shift.jam_masuk, tb_shift.jam_keluar')
            ->from('tb_jadwal')
            ->join('tb_shift', 'tb_shift.id = tb_jadwal.shift_id')
            ->where('tb_jadwal.pegawai_id', $pegawai_id)
            ->where('tb_jadwal.tanggal >=', $start)
            ->where('tb_jadwal.tanggal <=', $end)
            ->where('tb_jadwal.deleted_at', NULL)
            ->order_by('tb_jadwal.tanggal', 'ASC')
            ->get()
            ->result_array();
    }

    public function all_monthly(array $viewer, $month, $pegawai_id = NULL)
    {
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        $this->db
            ->select('tb_jadwal.id, tb_jadwal.tanggal, tb_jadwal.status, tb_pegawai.nama, tb_pegawai.nip, tb_units.nama_unit, tb_shift.nama_shift, tb_shift.jam_masuk, tb_shift.jam_keluar')
            ->from('tb_jadwal')
            ->join('tb_pegawai', 'tb_pegawai.id = tb_jadwal.pegawai_id')
            ->join('tb_units', 'tb_units.id = tb_pegawai.unit_id', 'left')
            ->join('tb_shift', 'tb_shift.id = tb_jadwal.shift_id')
            ->where('tb_jadwal.tanggal >=', $start)
            ->where('tb_jadwal.tanggal <=', $end)
            ->where('tb_jadwal.deleted_at', NULL);

        if ((int) $viewer['level'] >= 4) {
            $this->db->where('tb_jadwal.pegawai_id', (int) $viewer['pegawai_id']);
        } elseif ((int) $viewer['level'] === 3) {
            $this->db->where('tb_pegawai.unit_id', (int) $viewer['unit_id']);
        }

        if ($pegawai_id) {
            $this->db->where('tb_jadwal.pegawai_id', (int) $pegawai_id);
        }

        return $this->db->order_by('tb_jadwal.tanggal', 'ASC')->order_by('tb_pegawai.nama', 'ASC')->get()->result_array();
    }

    public function monthly_leave_requests(array $viewer, $month, $pegawai_id = NULL)
    {
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        $this->db
            ->select('tb_cuti.id, tb_cuti.jenis_cuti, tb_cuti.tgl_mulai, tb_cuti.tgl_selesai, tb_cuti.status, tb_cuti.catatan, tb_pegawai.nama, tb_pegawai.nip, tb_units.nama_unit')
            ->from('tb_cuti')
            ->join('tb_pegawai', 'tb_pegawai.id = tb_cuti.pegawai_id')
            ->join('tb_units', 'tb_units.id = tb_pegawai.unit_id', 'left')
            ->where('tb_cuti.deleted_at', NULL)
            ->group_start()
                ->where('tb_cuti.tgl_mulai <=', $end)
                ->where('tb_cuti.tgl_selesai >=', $start)
            ->group_end();

        if ((int) $viewer['level'] >= 4) {
            $this->db->where('tb_cuti.pegawai_id', (int) $viewer['pegawai_id']);
        } elseif ((int) $viewer['level'] === 3) {
            $this->db->where('tb_pegawai.unit_id', (int) $viewer['unit_id']);
        }

        if ($pegawai_id) {
            $this->db->where('tb_cuti.pegawai_id', (int) $pegawai_id);
        }

        return $this->db->order_by('tb_cuti.tgl_mulai', 'ASC')->order_by('tb_pegawai.nama', 'ASC')->get()->result_array();
    }

    public function employees(array $viewer)
    {
        $this->db
            ->select('id, nama, nip')
            ->from('tb_pegawai')
            ->where('deleted_at', NULL)
            ->where('status', 'AKTIF');

        if ((int) $viewer['level'] === 3) {
            $this->db->where('unit_id', (int) $viewer['unit_id']);
        }

        return $this->db->order_by('nama', 'ASC')->get()->result_array();
    }

    public function shifts()
    {
        return $this->db->select('id, nama_shift, jam_masuk, jam_keluar')->from('tb_shift')->order_by('nama_shift', 'ASC')->get()->result_array();
    }

    public function save_assignment(array $data, array $viewer)
    {
        $employee = $this->db->select('id, unit_id')->from('tb_pegawai')->where('id', (int) $data['pegawai_id'])->where('deleted_at', NULL)->get()->row_array();
        if (!$employee) {
            return FALSE;
        }

        if ((int) $viewer['level'] === 3 && (int) $employee['unit_id'] !== (int) $viewer['unit_id']) {
            return FALSE;
        }

        $existing = $this->db
            ->select('id')
            ->from('tb_jadwal')
            ->where('pegawai_id', (int) $data['pegawai_id'])
            ->where('tanggal', $data['tanggal'])
            ->get()
            ->row_array();

        $payload = array(
            'pegawai_id' => (int) $data['pegawai_id'],
            'shift_id' => (int) $data['shift_id'],
            'tanggal' => $data['tanggal'],
            'status' => $data['status'],
            'deleted_at' => NULL,
        );

        if ($existing) {
            return $this->db->where('id', (int) $existing['id'])->update('tb_jadwal', $payload);
        }

        return $this->db->insert('tb_jadwal', $payload);
    }
}
