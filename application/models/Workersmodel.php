<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workersmodel extends CI_Model {

    public function index() {
        $where = [
            'status' => [
                '$exists' => true
            ]
        ];
        
        $search = $this->input->get('search');
        if (!empty($search)) {
            $where['worker_name'] = new MongoDB\BSON\Regex($search, 'i');
        }
        
        $limit_input = $this->input->get('limit');
        $limit = $limit_input ? max(5, min(100, (int)$limit_input)) : 10;
        
        $page_input = $this->input->get('page');
        $page = $page_input ? max(1, (int)$page_input) : 1;
        
        // Determine sorting parameters
        $sortField = $this->input->get('sort');
        $sortDir = strtolower($this->input->get('dir')) === 'asc' ? 1 : -1; // 1 for ASC, -1 for DESC
        
        // Map allowed sort fields to database fields
        $allowedFields = [
            'worker_id'  => '_id',
            'added_on'   => 'added_on',
            'updated_on' => 'updated_on',
            'status'     => 'status'
        ];
        
        if (isset($allowedFields[$sortField])) {
            $order_by = [$allowedFields[$sortField] => $sortDir];
        } else {
            // Default sorting by newest entry
            $order_by = ['_id' => -1];
        }

        // Seed mock data if collection is completely empty, so user can see it
        $this->seed_mock_workers_if_empty();

        $q = $this->mongo_db->where($where)->order_by($order_by)->limit($limit)->offset(($page - 1) * $limit)->get('active_workers');
        
        $count = $this->mongo_db->where($where)->count('active_workers');
        
        $ret = [];
        foreach($q as $row){
            $ret[] = $row;
        }
        
        $data = [
            'workers' => $ret,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
            'search' => $search,
            'controllers' => $this->find_all_controllers()
        ];
        
        $this->load->view('dashboard/workers', $data);
    }

    private function seed_mock_workers_if_empty() {
        $count = $this->mongo_db->where(['_id' => ['$exists' => true]])->count('active_workers');
        if ($count == 0) {
            $mock_workers = [
                [
                    'worker_id' => 'VFWR1001',
                    'worker_name' => 'jobs',
                    'controller' => 'workers',
                    'controller_function' => 'jobs',
                    'error_logfile_path' => '/var/log/jobs.err.log',
                    'stdout_logfile_path' => '/var/log/jobs.out.log',
                    'autostart' => false,
                    'autorestart' => true,
                    'status' => 'stopped',
                    'added_on' => date('Y-m-d H:i:s'),
                    'updated_on' => date('Y-m-d H:i:s')
                ],
                [
                    'worker_id' => 'VFWR1002',
                    'worker_name' => 'stripe_processing',
                    'controller' => 'stripe',
                    'controller_function' => 'process',
                    'error_logfile_path' => '/var/log/stripe_processing.err.log',
                    'stdout_logfile_path' => '/var/log/stripe_processing.out.log',
                    'autostart' => true,
                    'autorestart' => true,
                    'status' => 'running',
                    'added_on' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'updated_on' => date('Y-m-d H:i:s')
                ]
            ];
            foreach ($mock_workers as $worker) {
                $this->mongo_db->insert('active_workers', $worker);
            }
        }
    }

    public function find_all_controllers(){
        $controllerPath = $this->config->item('worker_controller_path');
        $controllers = [];
        if ($controllerPath && is_dir($controllerPath)) {
            foreach (glob($controllerPath . '*.php') as $file) {
                $controllers[] = basename($file, '.php');
            }
        }
        return $controllers;
    }
}
