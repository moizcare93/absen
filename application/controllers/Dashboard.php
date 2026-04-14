<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
    }

    public function index()
    {
        $user = $this->current_user();
        $snapshot = $this->Dashboard_model->snapshot((int) $user['pegawai_id']);

        $this->render('dashboard/index', array(
            'page_title' => 'Dashboard',
            'snapshot' => $snapshot,
            'admin_snapshot' => $this->Dashboard_model->admin_snapshot($user),
        ));
    }
}
