<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workers extends CI_Controller {

	function __construct() {
		parent::__construct();
		if (!$this->session->userdata('user_id')) {
            redirect('login');
			return;
        }
		$this->load->model('workersmodel');
	}

	public function index()
	{
		$this->workersmodel->index();
	}

	public function add_worker() {
		$this->session->set_flashdata('info', 'Add Worker functionality is UI-only for now.');
		redirect('workers');
	}

	public function sync_workers() {
		$this->session->set_flashdata('info', 'Sync Workers functionality is UI-only for now.');
		redirect('workers');
	}

	public function edit_worker() {
		$this->session->set_flashdata('info', 'Edit Worker functionality is UI-only for now.');
		redirect('workers');
	}

	public function sync_worker($id) {
		$this->session->set_flashdata('info', 'Sync Worker functionality is UI-only for now.');
		redirect('workers');
	}

	public function toggle_status($id) {
		echo json_encode(['success' => true, 'status' => 'toggled']);
		exit;
	}

	public function view_error_log($id) {
		$this->session->set_flashdata('info', 'View Error Log functionality is UI-only for now.');
		redirect('workers');
	}

	public function view_output_log($id) {
		$this->session->set_flashdata('info', 'View Output Log functionality is UI-only for now.');
		redirect('workers');
	}

	public function logs() {
		$this->workersmodel->logs_list();
	}

	public function log_details($run_id) {
		$this->workersmodel->log_details($run_id);
	}

	public function analytics() {
		$this->workersmodel->analytics();
	}

	public function health() {
		$this->workersmodel->health();
	}

	public function release_lock($id) {
		$this->workersmodel->release_lock($id);
	}

	public function mark_timeout($run_id) {
		$this->workersmodel->mark_timeout($run_id);
	}
}
