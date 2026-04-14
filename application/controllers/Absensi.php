<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends Auth_Controller
{
    protected $photo_directory;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Absensi_model');
        $this->photo_directory = FCPATH . 'uploads/foto_absensi/';
    }

    public function index()
    {
        $user = $this->current_user();
        $reference_location = $this->Absensi_model->reference_location((int) $user['pegawai_id']);
        $month = $this->input->get('bulan', TRUE) ?: date('Y-m');
        $pegawai_id = (int) $this->input->get('pegawai_id');

        $this->render('absensi/index', array(
            'page_title' => 'Absensi',
            'attendance_today' => $this->Absensi_model->today((int) $user['pegawai_id']),
            'reference_location' => $reference_location,
            'allowed_radius' => !empty($reference_location['radius_meter']) ? $reference_location['radius_meter'] : $this->config->item('default_attendance_radius'),
            'office_latitude' => !empty($reference_location['latitude']) ? $reference_location['latitude'] : $this->config->item('office_latitude'),
            'office_longitude' => !empty($reference_location['longitude']) ? $reference_location['longitude'] : $this->config->item('office_longitude'),
            'schedule_today' => $this->Absensi_model->active_schedule((int) $user['pegawai_id'], date('Y-m-d')),
            'history_month' => $month,
            'selected_pegawai_id' => $pegawai_id,
            'attendance_history' => $this->Absensi_model->history($user, $month, $pegawai_id),
            'attendance_summary' => $this->Absensi_model->summary($user, $month, $pegawai_id),
            'employees' => $this->Absensi_model->employees($user),
        ));
    }

    public function checkin()
    {
        $this->handle_submission('masuk');
    }

    public function checkout()
    {
        $this->handle_submission('keluar');
    }

    protected function handle_submission($type)
    {
        if ($this->input->method(TRUE) !== 'POST') {
            redirect('absensi');
            return;
        }

        $user = $this->current_user();
        $pegawai_id = (int) $user['pegawai_id'];
        $attendance = $this->Absensi_model->today($pegawai_id);

        if ($type === 'masuk' && !empty($attendance['jam_masuk'])) {
            $this->session->set_flashdata('error', 'Absensi masuk hari ini sudah tercatat.');
            redirect('absensi');
            return;
        }

        if ($type === 'keluar' && empty($attendance['jam_masuk'])) {
            $this->session->set_flashdata('error', 'Absensi keluar hanya bisa dilakukan setelah check-in.');
            redirect('absensi');
            return;
        }

        if ($type === 'keluar' && !empty($attendance['jam_keluar'])) {
            $this->session->set_flashdata('error', 'Absensi keluar hari ini sudah tercatat.');
            redirect('absensi');
            return;
        }

        $latitude = $this->normalize_coordinate($this->input->post('latitude', TRUE));
        $longitude = $this->normalize_coordinate($this->input->post('longitude', TRUE));
        $photo_data = (string) $this->input->post('photo_data', FALSE);
        $catatan = trim((string) $this->input->post('catatan', TRUE));

        if ($latitude === NULL || $longitude === NULL) {
            $this->session->set_flashdata('error', 'Lokasi GPS belum tersedia. Aktifkan izin lokasi lalu coba lagi.');
            redirect('absensi');
            return;
        }

        if ($photo_data === '') {
            $this->session->set_flashdata('error', 'Foto absensi wajib diambil sebelum dikirim.');
            redirect('absensi');
            return;
        }

        $reference_location = $this->Absensi_model->reference_location($pegawai_id);
        $target_latitude = !empty($reference_location['latitude']) ? (float) $reference_location['latitude'] : (float) $this->config->item('office_latitude');
        $target_longitude = !empty($reference_location['longitude']) ? (float) $reference_location['longitude'] : (float) $this->config->item('office_longitude');
        $allowed_radius = !empty($reference_location['radius_meter']) ? (int) $reference_location['radius_meter'] : (int) $this->config->item('default_attendance_radius');
        $distance = $this->distance_in_meters($latitude, $longitude, $target_latitude, $target_longitude);

        if ($distance > $allowed_radius) {
            $this->session->set_flashdata('error', 'Lokasi Anda berada di luar radius absensi. Jarak terdeteksi ' . (int) round($distance) . ' meter.');
            redirect('absensi');
            return;
        }

        $photo_path = $this->store_photo($pegawai_id, $type, $photo_data);
        if ($photo_path === NULL) {
            $this->session->set_flashdata('error', 'Foto absensi tidak valid atau gagal disimpan.');
            redirect('absensi');
            return;
        }

        $now = date('Y-m-d H:i:s');
        $payload = array(
            'tanggal' => date('Y-m-d'),
            'waktu' => $now,
            'foto' => $photo_path,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'catatan' => $catatan !== '' ? $catatan : NULL,
        );

        if ($type === 'masuk') {
            $payload['status'] = $this->resolve_attendance_status($pegawai_id, $now);
            $this->Absensi_model->create_checkin($pegawai_id, $payload);
            $this->session->set_flashdata('success', 'Absensi masuk berhasil disimpan.');
            redirect('absensi');
            return;
        }

        $this->Absensi_model->create_checkout($pegawai_id, $payload);
        $this->session->set_flashdata('success', 'Absensi keluar berhasil disimpan.');
        redirect('absensi');
    }

    protected function resolve_attendance_status($pegawai_id, $checkin_at)
    {
        $schedule = $this->Absensi_model->active_schedule($pegawai_id, date('Y-m-d'));
        if (empty($schedule['jam_masuk'])) {
            return 'HADIR';
        }

        $deadline = strtotime(date('Y-m-d') . ' ' . $schedule['jam_masuk'] . ' +' . (int) $schedule['toleransi_menit'] . ' minutes');

        return strtotime($checkin_at) > $deadline ? 'TERLAMBAT' : 'HADIR';
    }

    protected function store_photo($pegawai_id, $type, $photo_data)
    {
        if (!preg_match('#^data:image/(png|jpeg);base64,#', $photo_data, $matches)) {
            return NULL;
        }

        $binary = base64_decode(substr($photo_data, strpos($photo_data, ',') + 1), TRUE);
        if ($binary === FALSE) {
            return NULL;
        }

        if (!is_dir($this->photo_directory) && !mkdir($concurrentDirectory = $this->photo_directory, 0755, TRUE) && !is_dir($concurrentDirectory)) {
            return NULL;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $filename = sprintf('%s_%d_%s.%s', $type, $pegawai_id, date('Ymd_His'), $extension);
        $absolute_path = $this->photo_directory . $filename;

        if (file_put_contents($absolute_path, $binary) === FALSE) {
            return NULL;
        }

        return 'uploads/foto_absensi/' . $filename;
    }

    protected function normalize_coordinate($value)
    {
        if ($value === NULL || $value === '') {
            return NULL;
        }

        return round((float) $value, 7);
    }

    protected function distance_in_meters($latitude_from, $longitude_from, $latitude_to, $longitude_to)
    {
        $earth_radius = 6371000;
        $lat_from = deg2rad($latitude_from);
        $lon_from = deg2rad($longitude_from);
        $lat_to = deg2rad($latitude_to);
        $lon_to = deg2rad($longitude_to);

        $lat_delta = $lat_to - $lat_from;
        $lon_delta = $lon_to - $lon_from;
        $angle = 2 * asin(sqrt(
            pow(sin($lat_delta / 2), 2) +
            cos($lat_from) * cos($lat_to) * pow(sin($lon_delta / 2), 2)
        ));

        return $angle * $earth_radius;
    }
}
