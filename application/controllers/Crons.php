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

	public function toggle_status($id="VFCR1045"){
		$this->cronsmodel->toggle_status($id);
	}

	public function get_controller_functions(){
		echo json_encode($this->cronsmodel->get_controller_functions());
	}

	public function add_cron(){
		// $this->session->set_flashdata('success', 'Cron added successfully');
		// redirect('crons');
		if($this->input->post()) {
			$data = $this->input->post();
			$this->cronsmodel->insert($data);
			$this->session->set_flashdata('success', 'Cron added successfully');
		}

		redirect('crons');
	}

	public function sync_crontab(){
		$this->cronsmodel->sync_crontab();
	}

	public function edit_cron(){
		if($this->input->post()) {
			$data = $this->input->post();
			$this->cronsmodel->update_schedule($data);
		}
		redirect('crons');
	}

	public function logs(){
		$this->cronsmodel->logs_list();
	}

	public function log_details($run_id){
		$this->cronsmodel->log_details($run_id);
	}

	public function retry_execution($run_id){
		$this->cronsmodel->retry_execution($run_id);
	}

	public function view_raw_log($run_id){
		$this->cronsmodel->view_raw_log($run_id);
	}

	public function analytics(){
		$this->cronsmodel->analytics();
	}

	public function health(){
		$this->cronsmodel->health();
	}

	public function release_lock($cron_id){
		$this->cronsmodel->release_lock($cron_id);
	}

	public function mark_timeout($run_id){
		$this->cronsmodel->mark_timeout($run_id);
	}
}
