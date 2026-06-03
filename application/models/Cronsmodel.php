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

    public function redirect_message($type,$msg){
        $this->session->set_flashdata($type, $msg);
        redirect('crons');
        exit;
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

    public function cronToDisplay($cron) {
        $parts = preg_split('/\s+/', trim($cron));

        if (count($parts) !== 5) {
            return [
                'time' => $cron,
                'frequency' => 'Custom'
            ];
        }

        [$minute, $hour, $dom, $month, $dow] = $parts;

        $days = [
            '0' => 'Sunday',
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday'
        ];

        // Every Minute
        if ($cron === '* * * * *') {
            return [
                'time' => 'Every Minute',
                'frequency' => 'Daily'
            ];
        }

        // Every X Minutes
        if (preg_match('/^\*\/(\d+)$/', $minute, $match) && $hour === '*') {
            return [
                'time' => 'Every ' . $match[1] . ' Minutes',
                'frequency' => 'Daily'
            ];
        }

        // Every Hour
        if ($cron === '0 * * * *') {
            return [
                'time' => 'Every Hour',
                'frequency' => 'Daily'
            ];
        }

        // Every X Hours
        if ($minute === '0' && preg_match('/^\*\/(\d+)$/', $hour, $match)) {
            return [
                'time' => 'Every ' . $match[1] . ' Hours',
                'frequency' => 'Daily'
            ];
        }

        // Fixed Time Jobs
        if (is_numeric($hour) && is_numeric($minute)) {

            $time = date(
                'h:i A',
                strtotime(sprintf('%02d:%02d', $hour, $minute))
            );

            // Weekly
            if ($dow !== '*') {
                return [
                    'time' => $time,
                    'frequency' => 'Every ' . ($days[$dow] ?? $dow)
                ];
            }

            // Monthly
            if ($dom !== '*') {
                return [
                    'time' => $time,
                    'frequency' => 'Day ' . $dom . ' of Every Month'
                ];
            }

            // Yearly
            if ($month !== '*') {
                return [
                    'time' => $time,
                    'frequency' => date('d F', mktime(0, 0, 0, (int)$month, (int)$dom))
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
        $cron_name = trim($_POST['cron_name'] ?? '');

        if ($cron_name === '')  $this->redirect_message("error",'Name cannot be blank.');

        if ($this->mongo_db->where(['name' => $cron_name])->count('active_crons') > 0) $this->redirect_message("error",'Name already exists.');

        $add_cron_controller = trim($_POST['add_cron_controller'] ?? '');

        if ($add_cron_controller === '') $this->redirect_message("error",'Controller cannot be blank.');

        $add_cron_function_name = trim($_POST['add_cron_function_name'] ?? '');

        if ($add_cron_function_name === '') $this->redirect_message("error",'Controller function cannot be blank.');

        $add_cron_schedule = trim($_POST['add_cron_schedule'] ?? '');
        $cron_time = null;
        $cron_day = null;
        $cron_day_of_the_month = null;

        if ($add_cron_schedule === '') $this->redirect_message("error",'Schedule cannot be blank.');

        if (in_array($add_cron_schedule, ['daily', 'weekly', 'monthly'])) {

            $cron_time = trim($_POST['cron_time'] ?? '');

            if ($cron_time === '') $this->redirect_message("error",'Time cannot be blank.');

            if ($add_cron_schedule === 'weekly') {

                $cron_day = trim($_POST['cron_day'] ?? '');

                if ($cron_day === '') $this->redirect_message("error",'Week Day cannot be blank.');

            }

            if ($add_cron_schedule === 'monthly') {

                $cron_day_of_the_month = trim($_POST['cron_day_of_the_month'] ?? '');

                if ($cron_day_of_the_month === '') $this->redirect_message("error",'Day of month cannot be blank.');

            }

        }
       
        $insert = [
            'cron_id' => 'VFCR'.getuniqnumid("active_crons"),
            'name' => $cron_name,
            'schedule' => $this->get_schedule($add_cron_schedule,$cron_time,$cron_day,$cron_day_of_the_month),
            'command' => $add_cron_controller." ".$add_cron_function_name,
            'created_by' => new MongoDB\BSON\ObjectId($_SESSION['user_id']),
            'created_at' => date("Y-m-d H:i:s"),
            'logs' => [],
            'updated_at' => date("Y-m-d H:i:s"),
            'status' => 'active'
        ];

        $this->mongo_db->insert('active_crons',$insert);
        $content = "";
        $command = sprintf(
            "%s php %s %s >> /var/log/%s.log 2>&1",

            $insert['schedule'],
            escapeshellarg($this->config->item('cron_execution_index_path')),

            $insert['command'],

            strtolower($insert['name'])

        );

        $currentCrons = shell_exec('crontab -l 2>/dev/null');
        $currentCrons .= $command . PHP_EOL;
        $tmpFile = '/tmp/vf_crontab.txt';
        file_put_contents($tmpFile, $currentCrons);

        exec("crontab {$tmpFile}");
        
        $this->redirect_message('success', 'Cron added successfully');
    }

    public function get_schedule($add_cron_schedule, $cron_time = null, $cron_day = null, $cron_day_of_the_month = null) {
        switch ($add_cron_schedule) {

            case 'every_minute':
                return '* * * * *';

            case 'hourly':
                return '0 * * * *';

            case 'daily':
                $time = DateTime::createFromFormat('g:iA', strtoupper($cron_time));
                return $time->format('i H') . ' * * *';

            case 'weekly':
                $days = [
                    'sunday'    => 0,
                    'monday'    => 1,
                    'tuesday'   => 2,
                    'wednesday' => 3,
                    'thursday'  => 4,
                    'friday'    => 5,
                    'saturday'  => 6
                ];

                $time = DateTime::createFromFormat('g:iA', strtoupper($cron_time));

                return $time->format('i H') . ' * * ' . $days[strtolower($cron_day)];

            case 'monthly':
                $time = DateTime::createFromFormat('g:iA', strtoupper($cron_time));

                return $time->format('i H') . ' ' . (int)$cron_day_of_the_month . ' * *';

            default:
                return false;
        }
    }
}
