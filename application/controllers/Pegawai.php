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
        $this->render('pegawai/index', array(
            'page_title' => 'Pegawai',
            'employees' => $this->Pegawai_model->all(),
        ));
    }
}
