<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model
{
    public function today($pegawai_id)
    {
        return $this->db
            ->select('tanggal, jam_masuk, jam_keluar, status, latitude_masuk, longitude_masuk, latitude_keluar, longitude_keluar, foto_masuk, foto_keluar, catatan')
            ->where('pegawai_id', $pegawai_id)
            ->where('tanggal', date('Y-m-d'))
            ->get('tb_absensi')
            ->row_array();
    }

    public function reference_location($pegawai_id)
    {
        $location = $this->db
            ->select('tb_lokasi_absensi.id, tb_lokasi_absensi.nama_lokasi, tb_lokasi_absensi.latitude, tb_lokasi_absensi.longitude, tb_lokasi_absensi.radius_meter')
            ->from('tb_pegawai')
            ->join('tb_lokasi_absensi', 'tb_lokasi_absensi.unit_id = tb_pegawai.unit_id AND tb_lokasi_absensi.is_active = 1', 'left')
            ->where('tb_pegawai.id', $pegawai_id)
            ->limit(1)
            ->get()
            ->row_array();

        if (!empty($location['id'])) {
            return $location;
        }

        $fallback = $this->db
            ->select('id, nama_lokasi, latitude, longitude, radius_meter')
            ->from('tb_lokasi_absensi')
            ->where('is_active', 1)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()
            ->row_array();

        return $fallback ?: NULL;
    }

    public function active_schedule($pegawai_id, $date)
    {
        return $this->db
            ->select('tb_jadwal.tanggal, tb_shift.nama_shift, tb_shift.jam_masuk, tb_shift.jam_keluar, tb_shift.toleransi_menit')
            ->from('tb_jadwal')
            ->join('tb_shift', 'tb_shift.id = tb_jadwal.shift_id')
            ->where('tb_jadwal.pegawai_id', $pegawai_id)
            ->where('tb_jadwal.tanggal', $date)
            ->where('tb_jadwal.deleted_at IS NULL', NULL, FALSE)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function create_checkin($pegawai_id, array $payload)
    {
        $data = array(
            'pegawai_id' => $pegawai_id,
            'tanggal' => $payload['tanggal'],
            'jam_masuk' => $payload['waktu'],
            'foto_masuk' => $payload['foto'],
            'latitude_masuk' => $payload['latitude'],
            'longitude_masuk' => $payload['longitude'],
            'status' => $payload['status'],
            'catatan' => $payload['catatan'],
        );

        return $this->db->insert('tb_absensi', $data);
    }

    public function create_checkout($pegawai_id, array $payload)
    {
        return $this->db
            ->where('pegawai_id', $pegawai_id)
            ->where('tanggal', $payload['tanggal'])
            ->update('tb_absensi', array(
                'jam_keluar' => $payload['waktu'],
                'foto_keluar' => $payload['foto'],
                'latitude_keluar' => $payload['latitude'],
                'longitude_keluar' => $payload['longitude'],
                'catatan' => $payload['catatan'],
            ));
    }
}
