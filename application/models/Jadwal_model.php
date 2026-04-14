<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_model extends CI_Model
{
    public function monthly($pegawai_id)
    {
        $start = date('Y-m-01');
        $end = date('Y-m-t');

        return $this->db
            ->select('tb_jadwal.tanggal, tb_jadwal.status, tb_shift.nama_shift, tb_shift.jam_masuk, tb_shift.jam_keluar')
            ->from('tb_jadwal')
            ->join('tb_shift', 'tb_shift.id = tb_jadwal.shift_id')
            ->where('tb_jadwal.pegawai_id', $pegawai_id)
            ->where('tb_jadwal.tanggal >=', $start)
            ->where('tb_jadwal.tanggal <=', $end)
            ->order_by('tb_jadwal.tanggal', 'ASC')
            ->get()
            ->result_array();
    }
}
