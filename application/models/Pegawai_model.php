<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai_model extends CI_Model
{
    public function all(array $viewer)
    {
        $this->db
            ->select('tb_pegawai.id, tb_pegawai.unit_id, tb_pegawai.nip, tb_pegawai.nama, tb_pegawai.email, tb_pegawai.no_hp, tb_pegawai.jabatan, tb_pegawai.tipe_kerja, tb_pegawai.status, tb_pegawai.tanggal_masuk, tb_units.nama_unit, tb_users.id AS user_id, tb_users.email AS user_email, tb_users.role_id, tb_users.is_active, tb_roles.nama_role')
            ->from('tb_pegawai')
            ->join('tb_units', 'tb_units.id = tb_pegawai.unit_id', 'left')
            ->join('tb_users', 'tb_users.pegawai_id = tb_pegawai.id', 'left')
            ->join('tb_roles', 'tb_roles.id = tb_users.role_id', 'left')
            ->where('tb_pegawai.deleted_at', NULL)
            ->order_by('tb_pegawai.nama', 'ASC');

        if ((int) $viewer['level'] === 3) {
            $this->db->where('tb_pegawai.unit_id', (int) $viewer['unit_id']);
        }

        return $this->db->get()->result_array();
    }

    public function find($id, array $viewer)
    {
        $this->db
            ->select('tb_pegawai.*, tb_users.role_id, tb_users.is_active')
            ->from('tb_pegawai')
            ->join('tb_users', 'tb_users.pegawai_id = tb_pegawai.id', 'left')
            ->where('tb_pegawai.id', $id)
            ->where('tb_pegawai.deleted_at', NULL);

        if ((int) $viewer['level'] === 3) {
            $this->db->where('tb_pegawai.unit_id', (int) $viewer['unit_id']);
        }

        return $this->db->get()->row_array();
    }

    public function units(array $viewer)
    {
        $this->db->select('id, nama_unit')->from('tb_units')->where('deleted_at', NULL)->order_by('nama_unit', 'ASC');

        if ((int) $viewer['level'] === 3) {
            $this->db->where('id', (int) $viewer['unit_id']);
        }

        return $this->db->get()->result_array();
    }

    public function roles(array $viewer)
    {
        $this->db->select('id, nama_role, level')->from('tb_roles')->order_by('level', 'ASC');

        if ((int) $viewer['level'] === 2) {
            $this->db->where('level >=', 2);
        } elseif ((int) $viewer['level'] === 3) {
            $this->db->where('level >=', 3);
        }

        return $this->db->get()->result_array();
    }

    public function save(array $data, array $viewer)
    {
        $employee = !empty($data['id']) ? $this->find((int) $data['id'], $viewer) : NULL;
        $pegawai = array(
            'unit_id' => $data['unit_id'],
            'nip' => $data['nip'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'jabatan' => $data['jabatan'],
            'tipe_kerja' => $data['tipe_kerja'],
            'status' => $data['status'],
            'tanggal_masuk' => $data['tanggal_masuk'],
        );

        $this->db->trans_start();

        if ($employee) {
            $this->db->where('id', (int) $employee['id'])->update('tb_pegawai', $pegawai);
            $pegawai_id = (int) $employee['id'];
        } else {
            $this->db->insert('tb_pegawai', $pegawai);
            $pegawai_id = (int) $this->db->insert_id();
            $this->db->insert('tb_saldo_cuti', array(
                'pegawai_id' => $pegawai_id,
                'tahun' => date('Y'),
                'saldo_tahunan' => 12,
                'terpakai_tahunan' => 0,
            ));
        }

        $user_data = array(
            'pegawai_id' => $pegawai_id,
            'role_id' => $data['role_id'],
            'email' => $data['email'],
            'is_active' => $data['is_active'],
        );

        if (!empty($data['password'])) {
            $user_data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (!empty($employee['role_id'])) {
            $this->db->where('pegawai_id', $pegawai_id)->update('tb_users', $user_data);
        } else {
            if (empty($user_data['password'])) {
                $user_data['password'] = password_hash('Admin@12345', PASSWORD_BCRYPT);
            }

            $this->db->insert('tb_users', $user_data);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function soft_delete($id, array $viewer)
    {
        $employee = $this->find($id, $viewer);
        if (!$employee) {
            return FALSE;
        }

        $this->db->trans_start();
        $this->db->where('id', (int) $id)->update('tb_pegawai', array('deleted_at' => date('Y-m-d H:i:s'), 'status' => 'NONAKTIF'));
        $this->db->where('pegawai_id', (int) $id)->update('tb_users', array('is_active' => 0));
        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
