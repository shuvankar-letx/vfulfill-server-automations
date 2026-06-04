<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboardmodel extends CI_Model {

    public function get_stats() {
        $yesterday = date('Y-m-d H:i:s', time() - 86400);

        // 1. Active counts
        $active_crons = $this->mongo_db->where(['status' => 'active'])->count('active_crons');
        $active_workers = $this->mongo_db->where(['status' => [
            '$in' => ['active', 'running']
        ]])->count('active_workers');

        // 2. Fatal / Failed / Timeout counts in the last 24 hours
        $failed_crons_24h = $this->mongo_db->where([
            'status' => ['$in' => ['failed', 'timeout']],
            'start_time' => ['$gte' => $yesterday]
        ])->count('cron_executions');

        $failed_workers_24h = $this->mongo_db->where([
            'status' => ['$in' => ['failed', 'timeout']],
            'start_time' => ['$gte' => $yesterday]
        ])->count('worker_executions');

        // 3. Most Time Taking & Fastest
        // Crons
        $slowest_cron_doc = $this->mongo_db->where(['status' => 'success'])->order_by(['duration' => -1])->limit(1)->get('cron_executions');
        $slowest_cron = !empty($slowest_cron_doc) ? reset($slowest_cron_doc) : null;

        $fastest_cron_doc = $this->mongo_db->where([
            'status' => 'success',
            'duration' => ['$gt' => 0.0]
        ])->order_by(['duration' => 1])->limit(1)->get('cron_executions');
        $fastest_cron = !empty($fastest_cron_doc) ? reset($fastest_cron_doc) : null;

        // Workers
        $slowest_worker_doc = $this->mongo_db->where(['status' => 'success'])->order_by(['duration' => -1])->limit(1)->get('worker_executions');
        $slowest_worker = !empty($slowest_worker_doc) ? reset($slowest_worker_doc) : null;

        $fastest_worker_doc = $this->mongo_db->where([
            'status' => 'success',
            'duration' => ['$gt' => 0.0]
        ])->order_by(['duration' => 1])->limit(1)->get('worker_executions');
        $fastest_worker = !empty($fastest_worker_doc) ? reset($fastest_worker_doc) : null;

        // 4. Memory consumption comparison
        // Let's grab the last 100 executions and parse memory values in PHP to find min/max
        $cron_execs = $this->mongo_db->where(['_id' => ['$exists' => true]])->order_by(['start_time' => -1])->limit(100)->get('cron_executions');
        $worker_execs = $this->mongo_db->where(['_id' => ['$exists' => true]])->order_by(['start_time' => -1])->limit(100)->get('worker_executions');

        $cron_mem = $this->parse_memory_extremes($cron_execs, 'cron_name');
        $worker_mem = $this->parse_memory_extremes($worker_execs, 'worker_name');

        return [
            'active_crons' => $active_crons,
            'active_workers' => $active_workers,
            'failed_crons_24h' => $failed_crons_24h,
            'failed_workers_24h' => $failed_workers_24h,
            'slowest_cron' => $slowest_cron,
            'fastest_cron' => $fastest_cron,
            'slowest_worker' => $slowest_worker,
            'fastest_worker' => $fastest_worker,
            'cron_mem' => $cron_mem,
            'worker_mem' => $worker_mem
        ];
    }

    private function parse_memory_extremes($executions, $name_key) {
        $max_val = -1;
        $max_name = 'N/A';
        $min_val = 999999;
        $min_name = 'N/A';

        foreach ($executions as $exec) {
            $mem_str = $exec->memory_usage ?? '';
            // Parse float value from string like "4.2 MB" or "4096 KB"
            if (preg_match('/([\d\.]+)\s*(MB|KB|GB)?/i', $mem_str, $matches)) {
                $val = (float)$matches[1];
                $unit = strtoupper($matches[2] ?? 'MB');
                if ($unit === 'KB') {
                    $val = $val / 1024;
                } elseif ($unit === 'GB') {
                    $val = $val * 1024;
                }
                
                if ($val > 0) {
                    if ($val > $max_val) {
                        $max_val = $val;
                        $max_name = $exec->$name_key . ' (' . round($val, 2) . ' MB)';
                    }
                    if ($val < $min_val) {
                        $min_val = $val;
                        $min_name = $exec->$name_key . ' (' . round($val, 2) . ' MB)';
                    }
                }
            }
        }

        return [
            'highest' => $max_name,
            'lowest' => $min_val === 999999 ? 'N/A' : $min_name
        ];
    }
}
