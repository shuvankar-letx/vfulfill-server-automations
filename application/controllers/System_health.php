<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_health extends CI_Controller {

	function __construct() {
		parent::__construct();
		if (!$this->session->userdata('user_id')) {
            redirect('login');
			return;
        }
	}

	public function index()
	{
		
       	$this->load->view('dashboard/main');
	}
}
