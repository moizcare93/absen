<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    public function snapshot($pegawai_id)
    {
        $today = date('Y-m-d');

        $attendance = $this->db
            ->where('pegawai_id', $pegawai_id)
            ->where('tanggal', $today)
            ->get('tb_absensi')
            ->row_array();

        $next_schedule = $this->db
            ->select('tb_jadwal.tanggal, tb_shift.nama_shift, tb_shift.jam_masuk, tb_shift.jam_keluar')
            ->from('tb_jadwal')
            ->join('tb_shift', 'tb_shift.id = tb_jadwal.shift_id')
            ->where('tb_jadwal.pegawai_id', $pegawai_id)
            ->where('tb_jadwal.tanggal >=', $today)
            ->order_by('tb_jadwal.tanggal', 'ASC')
            ->limit(5)
            ->get()
            ->result_array();

        $leave_balance = $this->db
            ->select('saldo_tahunan, terpakai_tahunan')
            ->where('pegawai_id', $pegawai_id)
            ->get('tb_saldo_cuti')
            ->row_array();

        return array(
            'attendance' => $attendance,
            'next_schedule' => $next_schedule,
            'leave_balance' => $leave_balance,
        );
    }
}
