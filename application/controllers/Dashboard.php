<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct() {
		parent::__construct();
		if (!$this->session->userdata('user_id')) {
            redirect('login');
			return;
        }
		$this->load->model('dashboardmodel');
	}
	
	public function index()
	{
		if (!$this->session->userdata('user_id')) {
            redirect('login');
			return;
        } 
		
		$data = $this->dashboardmodel->get_stats();
       	$this->load->view('dashboard/main', $data);
	}
}
