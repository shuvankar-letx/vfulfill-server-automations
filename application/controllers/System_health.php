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
		if (!$this->session->userdata('user_id')) {
            redirect('login');
			return;
        }

		// 1. CPU Load
		$load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0.0, 0.0, 0.0];

		// 2. Disk Space (in GB)
		$disk_total = disk_total_space('/');
		$disk_free = disk_free_space('/');
		$disk_used = $disk_total - $disk_free;

		$disk_total_gb = round($disk_total / (1024 * 1024 * 1024), 2);
		$disk_free_gb = round($disk_free / (1024 * 1024 * 1024), 2);
		$disk_used_gb = round($disk_used / (1024 * 1024 * 1024), 2);
		$disk_percentage = $disk_total_gb > 0 ? round(($disk_used_gb / $disk_total_gb) * 100, 2) : 0;

		// 3. Database connection check
		$mongo_status = false;
		try {
			// Try checking if mongo is connected
			if (isset($this->mongo_db)) {
				$mongo_status = true;
			}
		} catch (Exception $e) {
			$mongo_status = false;
		}

		$data = [
			'cpu_load' => $load,
			'disk_total' => $disk_total_gb,
			'disk_free' => $disk_free_gb,
			'disk_used' => $disk_used_gb,
			'disk_percentage' => $disk_percentage,
			'mongo_status' => $mongo_status
		];
		
       	$this->load->view('dashboard/system_health', $data);
	}
}
