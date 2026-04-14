<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    public function attempt_login($email, $password)
    {
        $this->db->select('
            tb_users.id,
            tb_users.email,
            tb_users.password,
            tb_roles.nama_role,
            tb_roles.level,
            tb_pegawai.id AS pegawai_id,
            tb_pegawai.nama,
            tb_pegawai.tipe_kerja,
            tb_units.nama_unit
        ');
        $this->db->from('tb_users');
        $this->db->join('tb_roles', 'tb_roles.id = tb_users.role_id');
        $this->db->join('tb_pegawai', 'tb_pegawai.id = tb_users.pegawai_id', 'left');
        $this->db->join('tb_units', 'tb_units.id = tb_pegawai.unit_id', 'left');
        $this->db->where('tb_users.email', $email);
        $this->db->where('tb_users.is_active', 1);
        $user = $this->db->get()->row_array();

        if (!$user || !password_verify($password, $user['password'])) {
            return NULL;
        }

        unset($user['password']);
        return $user;
    }
}
