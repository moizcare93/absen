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

        $this->render('jadwal/index', array(
            'page_title' => 'Jadwal',
            'schedules' => $this->Jadwal_model->monthly((int) $user['pegawai_id']),
        ));
    }
}
