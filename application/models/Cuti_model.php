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
            ->select('jenis_cuti, tgl_mulai, tgl_selesai, status, catatan')
            ->where('pegawai_id', $pegawai_id)
            ->order_by('tgl_mulai', 'DESC')
            ->get('tb_cuti')
            ->result_array();
    }
}
