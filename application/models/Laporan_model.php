<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model
{
    public function employees(array $viewer)
    {
        $this->db->select('id, nama')->from('tb_pegawai')->where('deleted_at', NULL)->where('status', 'AKTIF');

        if ((int) $viewer['level'] === 3) {
            $this->db->where('unit_id', (int) $viewer['unit_id']);
        } elseif ((int) $viewer['level'] >= 4) {
            $this->db->where('id', (int) $viewer['pegawai_id']);
        }

        return $this->db->order_by('nama', 'ASC')->get()->result_array();
    }

    public function attendance_report(array $viewer, $month, $pegawai_id = NULL)
    {
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        $this->db
            ->select("tb_pegawai.nama, COUNT(tb_absensi.id) AS total_hari, SUM(CASE WHEN tb_absensi.status = 'HADIR' THEN 1 ELSE 0 END) AS hadir, SUM(CASE WHEN tb_absensi.status = 'TERLAMBAT' THEN 1 ELSE 0 END) AS terlambat, SUM(CASE WHEN tb_absensi.status = 'CUTI' THEN 1 ELSE 0 END) AS cuti, SUM(CASE WHEN tb_absensi.status = 'IZIN' THEN 1 ELSE 0 END) AS izin", FALSE)
            ->from('tb_pegawai')
            ->join('tb_absensi', 'tb_absensi.pegawai_id = tb_pegawai.id AND tb_absensi.deleted_at IS NULL AND tb_absensi.tanggal >= "'.$start.'" AND tb_absensi.tanggal <= "'.$end.'"', 'left')
            ->where('tb_pegawai.deleted_at', NULL)
            ->group_by('tb_pegawai.id')
            ->order_by('tb_pegawai.nama', 'ASC');

        $this->apply_scope($viewer, $pegawai_id);

        return $this->db->get()->result_array();
    }

    public function leave_report(array $viewer, $month, $pegawai_id = NULL)
    {
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        $this->db
            ->select("tb_pegawai.nama, COUNT(tb_cuti.id) AS total_pengajuan, SUM(CASE WHEN tb_cuti.status = 'PENDING' THEN 1 ELSE 0 END) AS pending, SUM(CASE WHEN tb_cuti.status = 'APPROVED_UNIT' THEN 1 ELSE 0 END) AS approved_unit, SUM(CASE WHEN tb_cuti.status = 'APPROVED_HR' THEN 1 ELSE 0 END) AS approved_hr, SUM(CASE WHEN tb_cuti.status = 'DITOLAK' THEN 1 ELSE 0 END) AS ditolak", FALSE)
            ->from('tb_pegawai')
            ->join('tb_cuti', 'tb_cuti.pegawai_id = tb_pegawai.id AND tb_cuti.deleted_at IS NULL AND tb_cuti.tgl_mulai <= "'.$end.'" AND tb_cuti.tgl_selesai >= "'.$start.'"', 'left')
            ->where('tb_pegawai.deleted_at', NULL)
            ->group_by('tb_pegawai.id')
            ->order_by('tb_pegawai.nama', 'ASC');

        $this->apply_scope($viewer, $pegawai_id);

        return $this->db->get()->result_array();
    }

    public function schedule_report(array $viewer, $month, $pegawai_id = NULL)
    {
        $start = $month . '-01';
        $end = date('Y-m-t', strtotime($start));

        $this->db
            ->select('tb_pegawai.nama, COUNT(tb_jadwal.id) AS total_shift')
            ->from('tb_pegawai')
            ->join('tb_jadwal', 'tb_jadwal.pegawai_id = tb_pegawai.id AND tb_jadwal.deleted_at IS NULL AND tb_jadwal.tanggal >= "'.$start.'" AND tb_jadwal.tanggal <= "'.$end.'"', 'left')
            ->where('tb_pegawai.deleted_at', NULL)
            ->group_by('tb_pegawai.id')
            ->order_by('tb_pegawai.nama', 'ASC');

        $this->apply_scope($viewer, $pegawai_id);

        return $this->db->get()->result_array();
    }

    protected function apply_scope(array $viewer, $pegawai_id = NULL)
    {
        if ((int) $viewer['level'] === 3) {
            $this->db->where('tb_pegawai.unit_id', (int) $viewer['unit_id']);
        } elseif ((int) $viewer['level'] >= 4) {
            $this->db->where('tb_pegawai.id', (int) $viewer['pegawai_id']);
        }

        if ($pegawai_id) {
            $this->db->where('tb_pegawai.id', (int) $pegawai_id);
        }
    }
}
