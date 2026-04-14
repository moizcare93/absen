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

    public function leave_types($active_only = FALSE)
    {
        $this->db
            ->select('id, config_key, config_value, updated_at')
            ->from('tb_konfigurasi')
            ->where('kategori', 'cuti_jenis')
            ->order_by('config_key', 'ASC');

        $rows = $this->db->get()->result_array();
        $types = array();

        foreach ($rows as $row) {
            $decoded = json_decode($row['config_value'], TRUE);
            if (!is_array($decoded)) {
                continue;
            }

            $item = array(
                'id' => (int) $row['id'],
                'config_key' => $row['config_key'],
                'kode' => isset($decoded['kode']) ? $decoded['kode'] : strtoupper($row['config_key']),
                'nama' => isset($decoded['nama']) ? $decoded['nama'] : $row['config_key'],
                'jatah' => isset($decoded['jatah']) ? (int) $decoded['jatah'] : 0,
                'aktif' => !isset($decoded['aktif']) || (int) $decoded['aktif'] === 1,
                'potong_kuota' => !empty($decoded['potong_kuota']) ? 1 : 0,
                'keterangan' => isset($decoded['keterangan']) ? $decoded['keterangan'] : '',
            );

            if ($active_only && !$item['aktif']) {
                continue;
            }

            $types[] = $item;
        }

        return $types;
    }

    public function leave_type_options()
    {
        $options = array();
        foreach ($this->leave_types(TRUE) as $type) {
            $options[$type['kode']] = $type;
        }

        return $options;
    }

    public function leave_balance_by_type($pegawai_id, $year = NULL)
    {
        $year = $year ?: date('Y');
        $types = $this->leave_type_options();
        $balances = array();

        foreach ($types as $type) {
            $used = $this->db
                ->select('COALESCE(SUM(DATEDIFF(tgl_selesai, tgl_mulai) + 1), 0) AS hari', FALSE)
                ->from('tb_cuti')
                ->where('pegawai_id', (int) $pegawai_id)
                ->where('jenis_cuti', $type['kode'])
                ->where_in('status', array('PENDING', 'APPROVED_UNIT', 'APPROVED_HR'))
                ->where('YEAR(tgl_mulai) =', (int) $year, FALSE)
                ->where('deleted_at', NULL)
                ->get()
                ->row_array();

            $used_days = (int) $used['hari'];
            $balances[] = array(
                'kode' => $type['kode'],
                'nama' => $type['nama'],
                'jatah' => (int) $type['jatah'],
                'terpakai' => $used_days,
                'sisa' => max((int) $type['jatah'] - $used_days, 0),
                'potong_kuota' => (int) $type['potong_kuota'],
                'keterangan' => $type['keterangan'],
            );
        }

        return $balances;
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
        $type = $this->find_leave_type($data['jenis_cuti']);
        if (!$type) {
            return FALSE;
        }

        $days = ((strtotime($data['tgl_selesai']) - strtotime($data['tgl_mulai'])) / 86400) + 1;
        if ($days <= 0) {
            return FALSE;
        }

        if (!empty($type['potong_kuota']) && (int) $type['jatah'] > 0) {
            $balance = $this->leave_balance_by_type((int) $data['pegawai_id'], date('Y', strtotime($data['tgl_mulai'])));
            foreach ($balance as $row) {
                if ($row['kode'] === $data['jenis_cuti'] && $days > (int) $row['sisa']) {
                    return FALSE;
                }
            }
        }

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

    public function save_leave_type(array $data, array $viewer)
    {
        $payload = array(
            'kode' => strtoupper($data['kode']),
            'nama' => $data['nama'],
            'jatah' => (int) $data['jatah'],
            'aktif' => (int) $data['aktif'],
            'potong_kuota' => (int) $data['potong_kuota'],
            'keterangan' => $data['keterangan'],
        );

        $config_key = 'leave_type_' . strtolower(preg_replace('/[^a-z0-9]+/i', '_', $payload['kode']));
        $existing = $this->db
            ->from('tb_konfigurasi')
            ->where('kategori', 'cuti_jenis')
            ->where('id', !empty($data['id']) ? (int) $data['id'] : 0)
            ->get()
            ->row_array();

        if ($existing) {
            return $this->db->where('id', (int) $existing['id'])->update('tb_konfigurasi', array(
                'config_key' => $config_key,
                'config_value' => json_encode($payload),
                'updated_by' => (int) $viewer['id'],
            ));
        }

        return $this->db->insert('tb_konfigurasi', array(
            'config_key' => $config_key,
            'config_value' => json_encode($payload),
            'kategori' => 'cuti_jenis',
            'updated_by' => (int) $viewer['id'],
        ));
    }

    public function delete_leave_type($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->where('kategori', 'cuti_jenis')
            ->delete('tb_konfigurasi');
    }

    public function find_leave_type($kode)
    {
        foreach ($this->leave_types(FALSE) as $type) {
            if ($type['kode'] === $kode) {
                return $type;
            }
        }

        return NULL;
    }
}
