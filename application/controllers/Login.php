<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	
	public function index()
	{
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        } 
        $this->load->view('login/login');
        
	}

    public function submit(){

        $this->form_validation->set_rules('email','Email','required|trim');
        $this->form_validation->set_rules('password','Password','required');

        if (!$this->form_validation->run()) {
            $this->load->view('login/index');
            return;
        }

        $email = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);

        $q = $this->mongo_db->where(['user_email' => $email,'user_pass' => md5($password)])->get('admins');
        foreach($q as $row){
            $this->session->set_userdata([
                'user_id' => (string)$row->_id
            ]);
            redirect('dashboard');
        }
        $this->load->view('login/index');
        return;
    }

    public function logout()

    {

        $this->session->sess_destroy();

        redirect('login');

    }
}
