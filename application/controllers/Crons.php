<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crons extends CI_Controller {

	function __construct() {
		parent::__construct();
		if (!$this->session->userdata('user_id')) {
            redirect('login');
			return;
        }
		$this->load->model('cronsmodel');
	}

	public function index()
	{
		$this->cronsmodel->index();
       	// $this->load->view('dashboard/crons');
	}

	public function run_now($id){
		$this->cronsmodel->run_now($id);
	}

	public function get_controller_functions(){
		echo json_encode($this->cronsmodel->get_controller_functions());
	}
}
