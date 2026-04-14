<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_level(array(1, 2, 3));
        $this->load->model('Pegawai_model');
    }

    public function index()
    {
        $user = $this->current_user();
        $edit_id = (int) $this->input->get('edit');

        $this->render('pegawai/index', array(
            'page_title' => 'Pegawai',
            'employees' => $this->Pegawai_model->all($user),
            'roles' => $this->Pegawai_model->roles($user),
            'units' => $this->Pegawai_model->units($user),
            'editing' => $edit_id ? $this->Pegawai_model->find($edit_id, $user) : NULL,
        ));
    }

    public function save()
    {
        if ($this->input->method(TRUE) !== 'POST') {
            redirect('pegawai');
            return;
        }

        $user = $this->current_user();
        $id = (int) $this->input->post('id');

        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('role_id', 'Role', 'required|integer');
        $this->form_validation->set_rules('unit_id', 'Unit', 'required|integer');
        $this->form_validation->set_rules('tanggal_masuk', 'Tanggal masuk', 'required');
        if (!$id) {
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        }

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors(' ', ' '));
            redirect($id ? 'pegawai?edit=' . $id : 'pegawai');
            return;
        }

        $payload = array(
            'id' => $id,
            'unit_id' => (int) $this->input->post('unit_id'),
            'nip' => trim((string) $this->input->post('nip', TRUE)),
            'nama' => trim((string) $this->input->post('nama', TRUE)),
            'email' => strtolower(trim((string) $this->input->post('email', TRUE))),
            'no_hp' => trim((string) $this->input->post('no_hp', TRUE)),
            'jabatan' => trim((string) $this->input->post('jabatan', TRUE)),
            'tipe_kerja' => $this->input->post('tipe_kerja', TRUE) === 'SHIFT' ? 'SHIFT' : 'NON_SHIFT',
            'status' => $this->input->post('status', TRUE),
            'tanggal_masuk' => $this->input->post('tanggal_masuk', TRUE),
            'role_id' => (int) $this->input->post('role_id'),
            'is_active' => $this->input->post('is_active', TRUE) ? 1 : 0,
            'password' => trim((string) $this->input->post('password', FALSE)),
        );

        if ((int) $user['level'] === 3) {
            $payload['unit_id'] = (int) $user['unit_id'];
        }

        if ($this->Pegawai_model->save($payload, $user)) {
            $this->session->set_flashdata('success', $id ? 'Data pegawai diperbarui.' : 'Pegawai baru berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Data pegawai gagal disimpan.');
        }

        redirect('pegawai');
    }

    public function delete($id)
    {
        if ($this->input->method(TRUE) !== 'POST') {
            show_404();
        }

        if ($this->Pegawai_model->soft_delete((int) $id, $this->current_user())) {
            $this->session->set_flashdata('success', 'Pegawai berhasil dinonaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Pegawai tidak ditemukan atau gagal dihapus.');
        }

        redirect('pegawai');
    }
}
