<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cuti extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cuti_model');
    }

    public function index()
    {
        $user = $this->current_user();

        $this->render('cuti/index', array(
            'page_title' => 'Cuti',
            'leave_summary' => $this->Cuti_model->summary((int) $user['pegawai_id']),
            'leave_requests' => $this->Cuti_model->requests((int) $user['pegawai_id']),
            'pending_approvals' => (int) $user['level'] <= 3 ? $this->Cuti_model->pending_approvals($user) : array(),
        ));
    }

    public function submit()
    {
        if ($this->input->method(TRUE) !== 'POST') {
            redirect('cuti');
            return;
        }

        $this->form_validation->set_rules('jenis_cuti', 'Jenis cuti', 'required');
        $this->form_validation->set_rules('tgl_mulai', 'Tanggal mulai', 'required');
        $this->form_validation->set_rules('tgl_selesai', 'Tanggal selesai', 'required');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors(' ', ' '));
            redirect('cuti');
            return;
        }

        $saved = $this->Cuti_model->submit_request(array(
            'pegawai_id' => (int) $this->current_user()['pegawai_id'],
            'jenis_cuti' => $this->input->post('jenis_cuti', TRUE),
            'tgl_mulai' => $this->input->post('tgl_mulai', TRUE),
            'tgl_selesai' => $this->input->post('tgl_selesai', TRUE),
            'catatan' => trim((string) $this->input->post('catatan', TRUE)),
        ));

        $this->session->set_flashdata($saved ? 'success' : 'error', $saved ? 'Pengajuan cuti berhasil dikirim.' : 'Pengajuan cuti gagal.');
        redirect('cuti');
    }

    public function action($id)
    {
        $this->require_level(array(1, 2, 3));

        if ($this->input->method(TRUE) !== 'POST') {
            show_404();
        }

        $action = $this->input->post('approval_action', TRUE);
        $updated = $this->Cuti_model->update_request_status((int) $id, $action, $this->current_user());

        $this->session->set_flashdata($updated ? 'success' : 'error', $updated ? 'Status cuti berhasil diperbarui.' : 'Status cuti gagal diperbarui.');
        redirect('cuti');
    }
}
