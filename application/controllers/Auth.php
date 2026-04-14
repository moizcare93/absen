<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends Guest_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    public function login()
    {
        if ($this->input->method(TRUE) === 'POST') {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');

            if ($this->form_validation->run()) {
                $email = strtolower($this->input->post('email', TRUE));
                $password = $this->input->post('password', FALSE);
                $user = $this->Auth_model->attempt_login($email, $password);

                if ($user) {
                    $this->session->set_userdata('auth_user', $user);
                    redirect('dashboard');
                    return;
                }

                $this->session->set_flashdata('error', 'Email atau password tidak valid.');
                redirect('auth/login');
                return;
            }
        }

        $this->render('auth/login', array(
            'page_title' => 'Login',
        ));
    }

    public function logout()
    {
        $this->session->unset_userdata('auth_user');
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
