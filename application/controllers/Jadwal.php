<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Jadwal_model');
    }

    public function index()
    {
        $user = $this->current_user();
        $month = $this->input->get('bulan', TRUE) ?: date('Y-m');
        $pegawai_id = (int) $this->input->get('pegawai_id');

        $this->render('jadwal/index', array(
            'page_title' => 'Jadwal',
            'month' => $month,
            'selected_pegawai_id' => $pegawai_id,
            'schedules' => $this->Jadwal_model->monthly((int) $user['pegawai_id'], $month),
            'all_schedules' => $this->Jadwal_model->all_monthly($user, $month, $pegawai_id),
            'employees' => $this->Jadwal_model->employees($user),
            'shifts' => $this->Jadwal_model->shifts(),
        ));
    }

    public function save()
    {
        $user = $this->current_user();
        $this->require_level(array(1, 2, 3));

        if ($this->input->method(TRUE) !== 'POST') {
            redirect('jadwal');
            return;
        }

        $this->form_validation->set_rules('pegawai_id', 'Pegawai', 'required|integer');
        $this->form_validation->set_rules('shift_id', 'Shift', 'required|integer');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors(' ', ' '));
            redirect('jadwal');
            return;
        }

        $saved = $this->Jadwal_model->save_assignment(array(
            'pegawai_id' => (int) $this->input->post('pegawai_id'),
            'shift_id' => (int) $this->input->post('shift_id'),
            'tanggal' => $this->input->post('tanggal', TRUE),
            'status' => $this->input->post('status', TRUE),
        ), $user);

        if ($saved) {
            $this->session->set_flashdata('success', 'Jadwal berhasil disimpan.');
        } else {
            $this->session->set_flashdata('error', 'Jadwal gagal disimpan.');
        }

        redirect('jadwal');
    }
}
