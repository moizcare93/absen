<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai_model extends CI_Model
{
    public function all()
    {
        return $this->db
            ->select('tb_pegawai.nip, tb_pegawai.nama, tb_pegawai.tipe_kerja, tb_pegawai.status, tb_units.nama_unit')
            ->from('tb_pegawai')
            ->join('tb_units', 'tb_units.id = tb_pegawai.unit_id', 'left')
            ->where('tb_pegawai.deleted_at', NULL)
            ->order_by('tb_pegawai.nama', 'ASC')
            ->get()
            ->result_array();
    }
}
