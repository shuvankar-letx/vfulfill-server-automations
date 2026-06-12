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
		if($this->input->post()) {
			$this->workersmodel->insert($this->input->post());
		} else {
			redirect('workers');
		}
	}

	public function sync_workers() {
		$synced = $this->workersmodel->sync_workers();
		$this->session->set_flashdata('success', "Synced statuses for {$synced} workers.");
		redirect('workers');
	}

	public function edit_worker() {
		if($this->input->post()) {
			$this->workersmodel->update($this->input->post());
		} else {
			redirect('workers');
		}
	}

	public function sync_worker($id) {
		$this->workersmodel->sync_workers($id);
		$this->session->set_flashdata('success', 'Worker synced successfully.');
		redirect('workers');
	}

	public function toggle_status($id) {
		$this->workersmodel->toggle_status($id);
	}

	public function view_error_log($id) {
		$data = [
			'log_content' => $this->workersmodel->get_log_tail($id, 'error'),
			'type' => 'Error',
			'worker_id' => $id
		];
		$this->load->view('dashboard/worker_log_viewer', $data);
	}

	public function view_output_log($id) {
		$data = [
			'log_content' => $this->workersmodel->get_log_tail($id, 'stdout'),
			'type' => 'Output',
			'worker_id' => $id
		];
		$this->load->view('dashboard/worker_log_viewer', $data);
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

	public function watchdog() {
		$this->workersmodel->watchdog();
		echo "Watchdog executed successfully.\n";
	}
}
