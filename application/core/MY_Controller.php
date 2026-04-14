<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $layout = 'layouts/app';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url', 'form', 'security', 'text'));
        $this->load->library(array('session', 'form_validation'));
    }

    protected function current_user()
    {
        return $this->session->userdata('auth_user');
    }

    protected function is_admin_user()
    {
        $user = $this->current_user();

        return $user && (int) $user['level'] <= 3;
    }

    protected function render($view, $data = array())
    {
        $data['current_user'] = $this->current_user();
        $data['app_name'] = $this->config->item('app_name');
        $data['current_route'] = $this->uri->uri_string();
        $data['page_title'] = isset($data['page_title']) ? $data['page_title'] : $this->config->item('app_name');
        $data['content_view'] = $view;
        $this->load->view($this->layout, $data);
    }

    protected function json_response($payload, $code = 200)
    {
        return $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}

class Guest_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->current_user()) {
            redirect('dashboard');
        }
    }
}

class Auth_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->current_user()) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth/login');
        }
    }

    protected function require_level($allowed_levels = array())
    {
        $user = $this->current_user();

        if (!$user || !in_array((int) $user['level'], $allowed_levels, TRUE)) {
            show_error('Anda tidak memiliki akses ke halaman ini.', 403, 'Akses Ditolak');
        }
    }
}
