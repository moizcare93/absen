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
        ));
    }
}
