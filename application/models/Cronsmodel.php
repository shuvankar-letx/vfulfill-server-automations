<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Lorisleiva\CronTranslator\CronTranslator;
class Cronsmodel extends CI_Model {

	

	public function index()
	{
		$where = [
            'status' => [
                '$exists' => true
            ]
        ];
        $limit = 100;
        $page = 1;
        $order_by = [
            '_id' => -1
        ];
		$q = $this->mongo_db->where($where)->order_by($order_by)->limit($limit)->offset(($page - 1) * $limit)->get('active_crons');
        $count = $this->mongo_db->where($where)->count('active_crons');
        $ret = [];
        
        foreach($q as $row){
            $row->cronToDisplay = $this->cronToDisplay($row->schedule);
            $ret[] = $row;
        }
        
        
        $data = [
            'crons' => $ret,
            'count' => 0,
            'controllers' => $this->find_all_controllers()
        ];
        $this->load->view('dashboard/crons',$data);
	}

    public function find_all_controllers(){
        
        $controllerPath = $this->config->item('worker_controller_path');

        $controllers = [];

        foreach (glob($controllerPath . '*.php') as $file) {

            $controllers[] = basename($file, '.php');

        }
        // print_r($controllers);
        return $controllers;
    }

    public function get_controller_functions(){
        $controller = $this->input->post('controller');
        $functions = $this->find_all_function_names_in_controller($controller);

        return $functions;
    }

    public function find_all_function_names_in_controller($controller_name){
        // $controller_name = "";
        if($controller_name == ""){
            $controllers = $this->find_all_controllers();
            if(empty($controllers)) return [];
            $controller_name = $controllers[0];
        }

        $worker_path = "/var/www/vfulfill-workers-2.0/";

        $controllerPath = $worker_path . 'html/application/controllers/';
        $file = $controllerPath . $controller_name . '.php';

        if (!file_exists($file)) {

            return [];

        }
        
        $content = file_get_contents($file);

        preg_match_all(

            '/function\s+([a-zA-Z0-9_]+)\s*\(/',

            $content,

            $matches

        );

        $ignore_functions = [

            '__construct'

        ];

        $functions = [];

        foreach ($matches[1] as $function_name) {

            if (in_array($function_name, $ignore_functions)) {

                continue;

            }

            $functions[] = $function_name;

        }
        $all_functions = array_values(array_unique($functions));
        // print_r($all_functions);
        return $all_functions;
    }

    function cronToDisplay($cron){
        $parts = preg_split('/\s+/', trim($cron));

        if (count($parts) !== 5) {
            return [
                'time' => '-',
                'frequency' => 'Custom'
            ];
        }

        list($minute, $hour, $dom, $month, $dow) = $parts;

        $days = [
            '0'   => 'Sunday',
            '1'   => 'Monday',
            '2'   => 'Tuesday',
            '3'   => 'Wednesday',
            '4'   => 'Thursday',
            '5'   => 'Friday',
            '6'   => 'Saturday',
            '7'   => 'Sunday',

            'SUN' => 'Sunday',
            'MON' => 'Monday',
            'TUE' => 'Tuesday',
            'WED' => 'Wednesday',
            'THU' => 'Thursday',
            'FRI' => 'Friday',
            'SAT' => 'Saturday'
        ];

        // Every N Minutes (*/5 * * * *)
        if (preg_match('/^\*\/(\d+)$/', $minute, $m) && $hour === '*') {
            return [
                'time' => 'Every '.$m[1].' Minutes',
                'frequency' => 'Daily'
            ];
        }

        // Every N Hours (0 */6 * * *)
        if ($minute === '0' && preg_match('/^\*\/(\d+)$/', $hour, $m)) {
            return [
                'time' => 'Every '.$m[1].' Hours',
                'frequency' => 'Daily'
            ];
        }

        // Every Hour (0 * * * *)
        if ($minute === '0' && $hour === '*') {
            return [
                'time' => 'Every Hour',
                'frequency' => 'Daily'
            ];
        }

        // Fixed Time Cron
        if (is_numeric($hour) && is_numeric($minute)) {

            $time = date(
                'h:i A',
                strtotime(sprintf('%02d:%02d', $hour, $minute))
            );

            // Weekly (00 11 * * SUN)
            if ($dow !== '*') {

                $dowKey = strtoupper($dow);

                return [
                    'time' => $time,
                    'frequency' => 'Every '.($days[$dowKey] ?? $dow)
                ];
            }

            // Monthly (0 13 17 * *)
            if ($dom !== '*') {
                return [
                    'time' => $time,
                    'frequency' => 'Day '.$dom.' of Every Month'
                ];
            }

            // Yearly (0 13 17 3 *)
            if ($month !== '*') {
                $monthName = date('F', mktime(0, 0, 0, (int)$month, 1));

                return [
                    'time' => $time,
                    'frequency' => 'Every '.$dom.' '.$monthName
                ];
            }

            // Daily
            return [
                'time' => $time,
                'frequency' => 'Daily'
            ];
        }

        return [
            'time' => $cron,
            'frequency' => 'Custom'
        ];
    }

    public function run_now($id){
        $q = $this->mongo_db->where([
            'cron_id' => $id
        ])->get('active_crons');
        foreach($q as $row){
            $fixed_path = "";
            $command = $row->command;
            list($controller, $method) = explode(' ', $command);
            $phpCommand = sprintf(
                'php %s %s %s',
                escapeshellarg($this->config->item('cron_execution_index_path')),
                escapeshellarg($controller),
                escapeshellarg($method)
            );
            $return = exec($phpCommand . ' 2>&1', $output, $status);
			
            if ($status === 0) {

				$this->session->set_flashdata('success', 'Cron executed successfully.');

			} else {

				$this->session->set_flashdata(

					'error',

					'Cron execution failed. Exit Code: '.$status

				);

			}
			$log = [

				'executed_at' => date('Y-m-d H:i:s'),

				'status'      => $status === 0 ? 'success' : 'failed',

				'output'      => implode("\n", $output),

				'triggered_by'=> 'manual',
				'command' => $phpCommand

			];
			$this->mongo_db->where(['cron_id' => $id])->push(['logs' => $log])->update('active_crons');

            redirect('crons');
        }
        show_error('Cron not found');
    }

    public function insert($data){
        print_r($data);
        return;
    }
}
