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
        $edit_id = (int) $this->input->get('edit');
        $edit_type_id = (int) $this->input->get('edit_type');

        $this->render('cuti/index', array(
            'page_title' => 'Cuti',
            'leave_summary' => $this->Cuti_model->summary((int) $user['pegawai_id']),
            'leave_type_balances' => $this->Cuti_model->leave_balance_by_type((int) $user['pegawai_id']),
            'leave_types' => $this->Cuti_model->leave_types(TRUE),
            'leave_type_admin' => (int) $user['level'] === 1 ? $this->Cuti_model->leave_types(FALSE) : array(),
            'leave_requests' => $this->Cuti_model->requests((int) $user['pegawai_id']),
            'pending_approvals' => (int) $user['level'] <= 3 ? $this->Cuti_model->pending_approvals($user) : array(),
            'editing_request' => $edit_id ? $this->Cuti_model->find_request($edit_id, (int) $user['pegawai_id']) : NULL,
            'editing_type' => $edit_type_id ? $this->find_type_by_id($edit_type_id) : NULL,
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

        $request_id = (int) $this->input->post('id');
        $payload = array(
            'pegawai_id' => (int) $this->current_user()['pegawai_id'],
            'jenis_cuti' => $this->input->post('jenis_cuti', TRUE),
            'tgl_mulai' => $this->input->post('tgl_mulai', TRUE),
            'tgl_selesai' => $this->input->post('tgl_selesai', TRUE),
            'catatan' => trim((string) $this->input->post('catatan', TRUE)),
        );

        $saved = $request_id
            ? $this->Cuti_model->update_request($request_id, $payload)
            : $this->Cuti_model->submit_request($payload);

        $this->session->set_flashdata($saved ? 'success' : 'error', $saved ? ($request_id ? 'Pengajuan cuti berhasil diperbarui.' : 'Pengajuan cuti berhasil dikirim.') : 'Pengajuan cuti gagal atau melebihi jatah kategori.');
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

    public function save_type()
    {
        $this->require_level(array(1));

        if ($this->input->method(TRUE) !== 'POST') {
            redirect('cuti');
            return;
        }

        $this->form_validation->set_rules('kode', 'Kode cuti', 'required|alpha_dash');
        $this->form_validation->set_rules('nama', 'Nama cuti', 'required|trim');
        $this->form_validation->set_rules('jatah', 'Jatah cuti', 'required|integer');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors(' ', ' '));
            redirect('cuti');
            return;
        }

        $saved = $this->Cuti_model->save_leave_type(array(
            'id' => (int) $this->input->post('id'),
            'kode' => strtoupper(trim((string) $this->input->post('kode', TRUE))),
            'nama' => trim((string) $this->input->post('nama', TRUE)),
            'jatah' => (int) $this->input->post('jatah'),
            'aktif' => $this->input->post('aktif', TRUE) ? 1 : 0,
            'potong_kuota' => $this->input->post('potong_kuota', TRUE) ? 1 : 0,
            'keterangan' => trim((string) $this->input->post('keterangan', TRUE)),
        ), $this->current_user());

        $this->session->set_flashdata($saved ? 'success' : 'error', $saved ? 'Master jenis cuti berhasil disimpan.' : 'Master jenis cuti gagal disimpan.');
        redirect('cuti');
    }

    public function delete_type($id)
    {
        $this->require_level(array(1));

        if ($this->input->method(TRUE) !== 'POST') {
            show_404();
        }

        $deleted = $this->Cuti_model->delete_leave_type((int) $id);
        $this->session->set_flashdata($deleted ? 'success' : 'error', $deleted ? 'Jenis cuti berhasil dihapus.' : 'Jenis cuti gagal dihapus.');
        redirect('cuti');
    }

    public function delete_request($id)
    {
        if ($this->input->method(TRUE) !== 'POST') {
            show_404();
        }

        $deleted = $this->Cuti_model->delete_request((int) $id, (int) $this->current_user()['pegawai_id']);
        $this->session->set_flashdata($deleted ? 'success' : 'error', $deleted ? 'Pengajuan cuti berhasil dihapus.' : 'Pengajuan cuti tidak bisa dihapus lagi.');
        redirect('cuti');
    }

    protected function find_type_by_id($id)
    {
        foreach ($this->Cuti_model->leave_types(FALSE) as $type) {
            if ((int) $type['id'] === (int) $id) {
                return $type;
            }
        }

        return NULL;
    }
}
