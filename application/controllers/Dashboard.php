<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct() {
		parent::__construct();
		if (!$this->session->userdata('user_id')) {
            redirect('login');
			return;
        }
	}
	
	public function index()
	{
		if (!$this->session->userdata('user_id')) {
            redirect('login');
			return;
        } 
       	$this->load->view('dashboard/main');
	}
}
