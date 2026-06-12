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

    public function logs_list() {
        $wheres = ['_id' => ['$exists' => true]];
        
        $worker_filter = $this->input->get('worker');
        if (!empty($worker_filter)) {
            $wheres['worker_id'] = $worker_filter;
        }

        $status_filter = $this->input->get('status');
        if (!empty($status_filter)) {
            $wheres['status'] = $status_filter;
        }

        $run_id_filter = $this->input->get('run_id');
        if (!empty($run_id_filter)) {
            $wheres['run_id'] = $run_id_filter;
        }

        $date_range = $this->input->get('date_range');
        if (!empty($date_range)) {
            $parts = explode(' to ', $date_range);
            if (count($parts) === 2) {
                $start = $parts[0];
                $end = $parts[1];
                if (!empty($start) && !empty($end)) {
                    $wheres['start_time'] = [
                        '$gte' => date('Y-m-d H:i:s', strtotime($start . ' 00:00:00')),
                        '$lte' => date('Y-m-d H:i:s', strtotime($end . ' 23:59:59'))
                    ];
                }
            }
        }

        $limit_input = $this->input->get('limit');
        $limit = $limit_input ? max(5, min(100, (int)$limit_input)) : 10;
        $page_input = $this->input->get('page');
        $page = $page_input ? max(1, (int)$page_input) : 1;
        $offset = ($page - 1) * $limit;



        $q = $this->mongo_db->where($wheres)->order_by(['start_time' => -1])->limit($limit)->offset($offset)->get('worker_executions');
        $count = $this->mongo_db->where($wheres)->count('worker_executions');

        $active_workers = $this->mongo_db->where(['_id' => ['$exists' => true]])->get('active_workers');

        $data = [
            'logs' => $q,
            'count' => $count,
            'active_workers' => $active_workers,
            'page' => $page,
            'limit' => $limit,
            'filters' => [
                'worker' => $worker_filter,
                'status' => $status_filter,
                'run_id' => $run_id_filter,
                'date_range' => $date_range
            ]
        ];

        $this->load->view('dashboard/worker_logs', $data);
    }

    public function log_details($run_id) {
        $q = $this->mongo_db->where(['run_id' => $run_id])->get('worker_executions');
        if (empty($q)) {
            show_error('Execution log not found');
        }

        $execution = null;
        foreach ($q as $row) {
            $execution = $row;
            break;
        }

        $history = $this->mongo_db->where([
            'worker_id' => $execution->worker_id,
            'run_id' => ['$ne' => $run_id]
        ])->order_by(['start_time' => -1])->limit(5)->get('worker_executions');

        $data = [
            'execution' => $execution,
            'history' => $history
        ];

        $this->load->view('dashboard/worker_log_details', $data);
    }

    public function analytics() {


        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');

        $today_runs = $this->mongo_db->where([
            'start_time' => ['$gte' => $today_start, '$lte' => $today_end]
        ])->count('worker_executions');

        $today_success = $this->mongo_db->where([
            'start_time' => ['$gte' => $today_start, '$lte' => $today_end],
            'status' => 'success'
        ])->count('worker_executions');

        $today_failed = $this->mongo_db->where([
            'start_time' => ['$gte' => $today_start, '$lte' => $today_end],
            'status' => 'failed'
        ])->count('worker_executions');

        $today_timeout = $this->mongo_db->where([
            'start_time' => ['$gte' => $today_start, '$lte' => $today_end],
            'status' => 'timeout'
        ])->count('worker_executions');

        $success_rate = $today_runs > 0 ? round(($today_success / $today_runs) * 100, 2) : 0;

        $all_runs = $this->mongo_db->where(['_id' => ['$exists' => true]])->get('worker_executions');
        $total_duration = 0;
        $success_runs_count = 0;
        $durations = [];
        $run_counts = [];
        
        foreach ($all_runs as $run) {
            $name = $run->worker_name;
            if (!isset($run_counts[$name])) {
                $run_counts[$name] = 0;
            }
            $run_counts[$name]++;

            if ($run->status === 'success') {
                $total_duration += (float)$run->duration;
                $success_runs_count++;
                if (!isset($durations[$name])) {
                    $durations[$name] = [];
                }
                $durations[$name][] = (float)$run->duration;
            }
        }

        $avg_duration = $success_runs_count > 0 ? round($total_duration / $success_runs_count, 2) : 0;

        arsort($run_counts);
        $most_executed = !empty($run_counts) ? key($run_counts) . ' (' . current($run_counts) . ' runs)' : 'N/A';

        $avg_durations = [];
        foreach ($durations as $name => $d_list) {
            $avg_durations[$name] = array_sum($d_list) / count($d_list);
        }
        arsort($avg_durations);
        $slowest_worker = !empty($avg_durations) ? key($avg_durations) . ' (' . round(current($avg_durations), 2) . 's avg)' : 'N/A';

        $daily_trend = [];
        $daily_success = [];
        $daily_failed = [];
        $days_labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $day_start = date('Y-m-d 00:00:00', strtotime("-{$i} days"));
            $day_end = date('Y-m-d 23:59:59', strtotime("-{$i} days"));
            $days_labels[] = date('d M', strtotime("-{$i} days"));

            $daily_trend[] = $this->mongo_db->where([
                'start_time' => ['$gte' => $day_start, '$lte' => $day_end]
            ])->count('worker_executions');

            $daily_success[] = $this->mongo_db->where([
                'start_time' => ['$gte' => $day_start, '$lte' => $day_end],
                'status' => 'success'
            ])->count('worker_executions');

            $daily_failed[] = $this->mongo_db->where([
                'start_time' => ['$gte' => $day_start, '$lte' => $day_end],
                'status' => 'failed'
            ])->count('worker_executions');
        }

        $top_executed_labels = array_slice(array_keys($run_counts), 0, 10);
        $top_executed_values = array_slice(array_values($run_counts), 0, 10);

        $top_slow_labels = array_slice(array_keys($avg_durations), 0, 10);
        $top_slow_values = array_slice(array_values($avg_durations), 0, 10);

        $data = [
            'today_runs' => $today_runs,
            'today_success' => $today_success,
            'today_failed' => $today_failed,
            'today_timeout' => $today_timeout,
            'success_rate' => $success_rate,
            'avg_duration' => $avg_duration,
            'most_executed' => $most_executed,
            'slowest_worker' => $slowest_worker,
            'charts' => [
                'labels' => $days_labels,
                'total_runs' => $daily_trend,
                'success_runs' => $daily_success,
                'failed_runs' => $daily_failed,
                'top_executed_labels' => $top_executed_labels,
                'top_executed_values' => $top_executed_values,
                'top_slow_labels' => $top_slow_labels,
                'top_slow_values' => $top_slow_values
            ]
        ];

        $this->load->view('dashboard/worker_analytics', $data);
    }

    public function health() {


        $running = $this->mongo_db->where(['status' => 'running'])->get('worker_executions');
        
        $stuck = [];
        $one_hour_ago = date('Y-m-d H:i:s', time() - 3600);
        foreach ($running as $run) {
            if ($run->start_time < $one_hour_ago) {
                $stuck[] = $run;
            }
        }

        $timeouts = $this->mongo_db->where(['status' => 'timeout'])->order_by(['start_time' => -1])->limit(20)->get('worker_executions');

        $yesterday = date('Y-m-d H:i:s', time() - 86400);
        $failed = $this->mongo_db->where([
            'status' => 'failed',
            'start_time' => ['$gte' => $yesterday]
        ])->order_by(['start_time' => -1])->get('worker_executions');

        $locked_workers = $this->mongo_db->where(['is_locked' => true])->get('active_workers');

        $data = [
            'running' => $running,
            'stuck' => $stuck,
            'timeouts' => $timeouts,
            'failed' => $failed,
            'locked_workers' => $locked_workers
        ];

        $this->load->view('dashboard/worker_health', $data);
    }

    public function release_lock($worker_id) {
        $this->mongo_db->where(['worker_id' => $worker_id])->set(['is_locked' => false])->update('active_workers');
        
        $this->mongo_db->where(['worker_id' => $worker_id, 'status' => 'running'])->set([
            'status' => 'failed',
            'error_message' => 'Lock manually released by Admin.',
            'end_time' => date('Y-m-d H:i:s')
        ])->update('worker_executions');

        $this->session->set_flashdata('success', 'Lock released successfully.');
        redirect('workers/health');
    }

    public function mark_timeout($run_id) {
        $q = $this->mongo_db->where(['run_id' => $run_id])->get('worker_executions');
        foreach ($q as $row) {
            $this->mongo_db->where(['run_id' => $run_id])->set([
                'status' => 'timeout',
                'error_message' => 'Execution manually marked as timeout.',
                'end_time' => date('Y-m-d H:i:s')
            ])->update('worker_executions');

            $this->mongo_db->where(['worker_id' => $row->worker_id])->set(['is_locked' => false])->update('active_workers');
        }

        $this->session->set_flashdata('success', 'Execution marked as timeout.');
        redirect('workers/health');
    }

    private function seed_mock_worker_executions_if_empty() {
        $count = $this->mongo_db->where(['_id' => ['$exists' => true]])->count('worker_executions');
        if ($count > 0) {
            return;
        }

        $workers = [
            [
                'worker_id' => 'VFWR1001',
                'worker_name' => 'jobs',
                'controller' => 'workers',
                'method' => 'jobs',
            ],
            [
                'worker_id' => 'VFWR1002',
                'worker_name' => 'stripe_processing',
                'controller' => 'stripe',
                'method' => 'process',
            ]
        ];

        // Seed logs for the last 7 days
        for ($i = 7; $i >= 0; $i--) {
            foreach ($workers as $w) {
                // Generate 2-5 logs per day per worker
                $num_logs = rand(2, 5);
                for ($j = 0; $j < $num_logs; $j++) {
                    $start_hour = rand(0, 23);
                    $start_minute = rand(0, 59);
                    $start_time = date('Y-m-d H:i:s', strtotime("-{$i} days") + ($start_hour * 3600) + ($start_minute * 60));
                    
                    $duration = round(rand(10, 500) / 10, 2);
                    $end_time = date('Y-m-d H:i:s', strtotime($start_time) + (int)$duration);
                    
                    $status_rand = rand(1, 10);
                    $status = 'success';
                    $error_msg = '';
                    if ($status_rand == 9) {
                        $status = 'failed';
                        $error_msg = 'Error parsing job payload';
                    } elseif ($status_rand == 10) {
                        $status = 'timeout';
                        $error_msg = 'Execution exceeded max execution time';
                    }

                    $exec = [
                        'run_id' => 'WRUN_' . strtoupper(substr(md5(uniqid()), 0, 10)),
                        'worker_id' => $w['worker_id'],
                        'worker_name' => $w['worker_name'],
                        'controller' => $w['controller'],
                        'method' => $w['method'],
                        'status' => $status,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'duration' => $duration,
                        'server' => gethostname(),
                        'pid' => rand(1000, 9999),
                        'memory_usage' => round(rand(15, 60) / 10, 2) . ' MB',
                        'error_message' => $error_msg,
                        'raw_log' => "Initializing worker...\nRunning loop...\nCompleted with status: {$status}",
                        'is_locked' => false
                    ];
                    $this->mongo_db->insert('worker_executions', $exec);
                }
            }
        }
    }

    public function write_supervisor_config() {
        $workers = $this->mongo_db->get('active_workers');
        $conf = "";
        
        $indexPath = $this->config->item('cron_execution_index_path');
        if (empty($indexPath)) {
             $indexPath = FCPATH . 'index.php';
        }

        foreach ($workers as $worker) {
            $conf .= "[program:{$worker->worker_id}]\n";
            $conf .= "command=php {$indexPath} {$worker->controller} {$worker->controller_function}\n";
            $conf .= "autostart=" . ($worker->autostart ? "true" : "false") . "\n";
            $conf .= "autorestart=" . ($worker->autorestart ? "true" : "false") . "\n";
            $conf .= "stdout_logfile={$worker->stdout_logfile_path}\n";
            $conf .= "stderr_logfile={$worker->error_logfile_path}\n";
            $conf .= "redirect_stderr=false\n\n";
        }

        $tmpFile = __DIR__ . "/../../../tmp/vf_supervisor_" . time() . ".conf";
        $tmpDir = dirname($tmpFile);
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        file_put_contents($tmpFile, $conf);

        exec("sudo -n cp " . escapeshellarg($tmpFile) . " /etc/supervisor/conf.d/workers.conf 2>&1", $out, $ret);
        if ($ret !== 0) {
            $err = 'Supervisor config copy failed: ' . implode(" ", $out);
            log_message('error', $err);
            $this->session->set_flashdata('error', $err);
            return false;
        }

        exec("sudo -n supervisorctl reread 2>&1", $out2, $ret2);
        if ($ret2 !== 0) {
            $this->session->set_flashdata('error', 'Supervisor reread failed: ' . implode(" ", $out2));
            return false;
        }

        exec("sudo -n supervisorctl update 2>&1", $out3, $ret3);
        
        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }

        if ($ret3 !== 0) {
            $this->session->set_flashdata('error', 'Supervisor update failed: ' . implode(" ", $out3));
            return false;
        }
        
        return true;
    }

    public function insert($data) {
        $worker_name = trim($data['worker_name'] ?? '');
        $controller = trim($data['controller'] ?? '');
        $controller_function = trim($data['controller_function'] ?? '');
        $error_logfile_path = trim($data['error_logfile_path'] ?? '');
        $stdout_logfile_path = trim($data['stdout_logfile_path'] ?? '');
        $autostart = isset($data['autostart']) ? true : false;
        $autorestart = isset($data['autorestart']) ? true : false;

        if (empty($worker_name) || empty($controller) || empty($controller_function)) {
            $this->session->set_flashdata('error', 'Please fill all required fields.');
            redirect('workers');
        }

        if (empty($error_logfile_path)) {
            $error_logfile_path = '/var/log/' . strtolower(str_replace(' ', '_', $worker_name)) . '.err.log';
        }
        if (empty($stdout_logfile_path)) {
            $stdout_logfile_path = '/var/log/' . strtolower(str_replace(' ', '_', $worker_name)) . '.out.log';
        }

        $this->load->helper('url');
        $worker_id = 'VFWR' . getuniqnumid("active_workers");

        $insert_data = [
            'worker_id' => $worker_id,
            'worker_name' => $worker_name,
            'controller' => $controller,
            'controller_function' => $controller_function,
            'error_logfile_path' => $error_logfile_path,
            'stdout_logfile_path' => $stdout_logfile_path,
            'autostart' => $autostart,
            'autorestart' => $autorestart,
            'status' => 'stopped',
            'pid' => null,
            'added_on' => date('Y-m-d H:i:s'),
            'updated_on' => date('Y-m-d H:i:s')
        ];

        $this->mongo_db->insert('active_workers', $insert_data);
        $this->write_supervisor_config();

        if ($autostart) {
            $this->start_worker($worker_id);
        }

        $this->session->set_flashdata('success', 'Worker added successfully.');
        redirect('workers');
    }

    public function update($data) {
        $worker_id = trim($data['worker_id'] ?? '');
        $error_logfile_path = trim($data['error_logfile_path'] ?? '');
        $stdout_logfile_path = trim($data['stdout_logfile_path'] ?? '');
        $autostart = isset($data['autostart']) ? true : false;
        $autorestart = isset($data['autorestart']) ? true : false;

        $q = $this->mongo_db->where(['worker_id' => $worker_id])->get('active_workers');
        if (empty($q)) {
            $this->session->set_flashdata('error', 'Worker not found.');
            redirect('workers');
        }
        $worker = $q[0];

        $update_data = [
            'error_logfile_path' => $error_logfile_path,
            'stdout_logfile_path' => $stdout_logfile_path,
            'autostart' => $autostart,
            'autorestart' => $autorestart,
            'updated_on' => date('Y-m-d H:i:s')
        ];

        $this->mongo_db->where(['worker_id' => $worker_id])->set($update_data)->update('active_workers');
        $this->write_supervisor_config();

        if ($worker->status === 'running') {
            // Because config was updated, supervisorctl update might have restarted it or we might need to manually restart
            $this->stop_worker($worker_id);
            $this->start_worker($worker_id);
        }

        $this->session->set_flashdata('success', 'Worker updated successfully.');
        redirect('workers');
    }

    public function start_worker($worker_id) {
        exec("sudo -n supervisorctl start " . escapeshellarg($worker_id) . " 2>&1", $out, $ret);
        if ($ret === 0 || strpos(implode(" ", $out), 'already started') !== false) {
            $this->mongo_db->where(['worker_id' => $worker_id])->set([
                'status' => 'running',
                'updated_on' => date('Y-m-d H:i:s')
            ])->update('active_workers');
            return true;
        }
        return false;
    }

    public function stop_worker($worker_id) {
        exec("sudo -n supervisorctl stop " . escapeshellarg($worker_id) . " 2>&1", $out, $ret);
        if ($ret === 0 || strpos(implode(" ", $out), 'not running') !== false) {
            $this->mongo_db->where(['worker_id' => $worker_id])->set([
                'status' => 'stopped',
                'updated_on' => date('Y-m-d H:i:s')
            ])->update('active_workers');
            return true;
        }
        return false;
    }

    public function toggle_status($id) {
        $q = $this->mongo_db->where(['worker_id' => $id])->get('active_workers');
        if (empty($q)) {
            echo json_encode(['success' => false, 'message' => 'Worker not found']);
            return;
        }
        $worker = $q[0];

        if ($worker->status == 'running') {
            $success = $this->stop_worker($id);
            $newStatus = 'stopped';
        } else {
            $success = $this->start_worker($id);
            $newStatus = 'running';
        }

        echo json_encode(['success' => $success, 'status' => $newStatus]);
    }

    public function sync_workers($id = null) {
        $where = [];
        if ($id) {
            $where['worker_id'] = $id;
        }
        $workers = $this->mongo_db->where($where)->get('active_workers');

        // Regenerate the supervisor configuration block
        $this->write_supervisor_config();

        exec("sudo -n supervisorctl status 2>&1", $out, $ret);
        $statusMap = [];
        foreach ($out as $line) {
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) >= 2) {
                $statusMap[$parts[0]] = strtolower($parts[1]);
            }
        }

        $synced = 0;
        foreach ($workers as $worker) {
            $sup_status = $statusMap[$worker->worker_id] ?? 'stopped';
            
            $new_status = 'stopped';
            if ($sup_status === 'running' || $sup_status === 'starting') $new_status = 'running';
            if ($sup_status === 'fatal' || $sup_status === 'backoff') $new_status = 'failed';

            if ($worker->status !== $new_status) {
                $this->mongo_db->where(['worker_id' => $worker->worker_id])->set([
                    'status' => $new_status,
                    'updated_on' => date('Y-m-d H:i:s')
                ])->update('active_workers');
                $synced++;
            }
        }
        
        return $synced;
    }

    public function watchdog() {
        // Now handled natively by Supervisor
    }

    public function get_log_tail($worker_id, $type) {
        $q = $this->mongo_db->where(['worker_id' => $worker_id])->get('active_workers');
        if (empty($q)) {
            return "Worker not found.";
        }
        $worker = $q[0];
        
        $path = $type === 'error' ? $worker->error_logfile_path : $worker->stdout_logfile_path;
        
        if (empty($path)) {
            return "Log file path is not set.";
        }

        if (!file_exists($path)) {
            return "Log file does not exist: $path";
        }

        $lines = 100;
        $out = [];
        exec("tail -n " . $lines . " " . escapeshellarg($path) . " 2>&1", $out);
        return implode("\n", $out);
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
