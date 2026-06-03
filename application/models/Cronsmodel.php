<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Lorisleiva\CronTranslator\CronTranslator;
class Cronsmodel extends CI_Model {

	

	public function index() {
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
            $command = $row->command;
            list($controller, $method) = explode(' ', $command);
            $cronPath = $this->config->item('cron_execution_index_path');
            // Verify the cron execution script exists
            if (!file_exists($cronPath)) {
                log_message('error', "Cron execution script not found: {$cronPath}");
                $this->session->set_flashdata('error', "Cron execution failed: script not found.");
                redirect('crons');
                exit;
            }
            // Determine log file path for this cron execution
            $logFile = strtolower(str_replace(' ', '_', $row->name));
            $logPath = "/var/log/{$logFile}.log";
            // Ensure the log file exists with correct permissions
            if (!file_exists($logPath)) {
                // Create the file and set permissions to 664 (rw-rw-r--)
                $handle = fopen($logPath, 'a');
                if ($handle !== false) {
                    fclose($handle);
                }
                @chmod($logPath, 0664);
            }
            // Build command without redirection
            $phpCommand = sprintf(
                'php %s %s %s',
                escapeshellarg($cronPath),
                escapeshellarg($controller),
                escapeshellarg($method)
            );
            // Execute the command and capture output & status, redirect logs to file
            $fullCmd = $phpCommand . ' >> "' . $logPath . '" 2>&1';

            log_message('error', "Running Command {$fullCmd}");
            $output = [];
            $status = 0;
            exec($fullCmd, $output, $status);
            if ($status === 0) {
                $this->session->set_flashdata('success', 'Cron executed successfully.');
            } else {
                log_message('error', "Cron execution failed. Command: {$fullCmd} Exit Code: {$status}");
                $this->session->set_flashdata('error', 'Cron execution failed. Exit Code: ' . $status);
            }
            if ($status === 0) {
                $this->session->set_flashdata('success', 'Cron executed successfully.');
            } else {
                log_message('error', "Cron execution failed. Command: {$phpCommand} Exit Code: {$status}");
                $this->session->set_flashdata('error', 'Cron execution failed. Exit Code: '.$status);
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
            exit;
        }
        show_error('Cron not found');
    }

    public function insert($data)
    {
        $cron_name = trim($_POST['cron_name'] ?? '');

        if ($cron_name === '') {
            $this->redirect_message("error", "Name cannot be blank.");
        }

        if ($this->mongo_db->where(['name' => $cron_name])->count('active_crons') > 0) {
            $this->redirect_message("error", "Name already exists.");
        }

        $controller = trim($_POST['add_cron_controller'] ?? '');
        if ($controller === '') {
            $this->redirect_message("error", "Controller cannot be blank.");
        }

        $function = trim($_POST['add_cron_function_name'] ?? '');
        if ($function === '') {
            $this->redirect_message("error", "Function cannot be blank.");
        }

        $schedule = trim($_POST['add_cron_schedule'] ?? '');
        if ($schedule === '') {
            $this->redirect_message("error", "Schedule cannot be blank.");
        }

        $cron_time = $_POST['cron_time'] ?? null;
        $cron_day = $_POST['cron_day'] ?? null;
        $cron_dom = $_POST['cron_day_of_the_month'] ?? null;

        $scheduleExpr = $this->get_schedule($schedule, $cron_time, $cron_day, $cron_dom);

        // DB command (used for matching later)
        $commandKey = $controller . " " . $function;

        $cronId = 'VFCR' . getuniqnumid("active_crons");

        $insert = [
            'cron_id' => $cronId,
            'name' => $cron_name,
            'schedule' => $scheduleExpr,
            'command' => $commandKey,
            'status' => 'active',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $this->mongo_db->insert('active_crons', $insert);

        // Build cron line (KEEP IT SIMPLE - no escapeshellarg confusion)
        $logFile = strtolower(str_replace(" ", "_", $cron_name));

        $cronLine =
            $scheduleExpr . " php " .
            $this->config->item('cron_execution_index_path') . " " .
            $controller . " " . $function .
            " >> /var/log/{$logFile}.log 2>&1";

        // Load existing crontab
        $existing = shell_exec("crontab -l 2> /dev/null");
        $existing = $existing ?: "";

        // Prevent duplicate insert
        if (strpos($existing, $controller . " " . $function) !== false) {
            $this->redirect_message("error", "Cron already exists in system crontab.");
        }

        $newCrons = $existing . PHP_EOL . $cronLine . PHP_EOL;

        // Use project tmp directory for temporary crontab file
        $tmpDir = realpath(__DIR__ . "/../../tmp");
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        $tmpFile = $tmpDir . "/vf_crontab_" . time() . ".txt";
        file_put_contents($tmpFile, trim($newCrons) . PHP_EOL);

        // Install new crontab
        exec("/usr/bin/crontab " . escapeshellarg($tmpFile) . " 2>&1", $out, $statusCode);
        if ($statusCode !== 0) {
            log_message('error', 'Failed to install crontab: ' . implode("\n", $out));
            $this->redirect_message("error", "Failed to add cron to system crontab.");
        }
        log_message('info', 'Cron added to system crontab successfully.');

        $this->redirect_message("success", "Cron added successfully");
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

    public function toggle_status($id){
        $q = $this->mongo_db->where([
            'cron_id' => $id
        ])->get('active_crons');
        foreach($q as $row){
            if($row->status == "active"){
                $newStatus = "inactive";
            }else{
                $newStatus = "active";
            }
            $this->mongo_db->where(['cron_id' => $id])->set(['status' => $newStatus,'updated_at' => date("Y-m-d H:i:s")])->update('active_crons');
            $command = explode(" ",$row->command);
            $this->toggleCronInCrontab($command[0],$command[1],$newStatus);
            echo json_encode(['success' => true, 'status' => $newStatus]);
            exit;
        }

        // redirect('crons');
        // exit;
        
    }

    public function toggleCronInCrontab($controller, $function, $newStatus)
    {
        log_message('info', "Toggling crontab entry for {$controller} {$function} to {$newStatus}");
        $identifier = trim($controller . " " . $function);

        exec("crontab -l 2>/dev/null", $output, $returnVar);

        if ($returnVar !== 0) {
            log_message('error', 'Unable to read crontab');
            return false;
        }

        $updated = [];

        foreach ($output as $line) {
            $originalLine = $line;
            // Remove leading comment for matching purposes
            $cleanLine = preg_replace('/^#\s*/', '', $originalLine);

            if (strpos($cleanLine, $identifier) !== false) {
                if ($newStatus === "inactive") {
                    // Ensure the line is commented out
                    if (strpos(trim($originalLine), '#') !== 0) {
                        $originalLine = "# " . $originalLine;
                    }
                } elseif ($newStatus === "active") {
                    // Remove leading comment to activate
                    $originalLine = preg_replace('/^#\s*/', '', $originalLine);
                }
            }
            $updated[] = rtrim($originalLine);
        }

        // Write updated crontab to a temporary file inside project workspace
        $tmpFile = __DIR__ . "/../../tmp/vf_crontab_" . time() . ".txt";
        // Ensure temporary directory exists
        $tmpDir = dirname($tmpFile);
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        file_put_contents($tmpFile, implode("\n", $updated) . PHP_EOL);

        exec("/usr/bin/crontab " . escapeshellarg($tmpFile) . " 2>&1", $out, $statusCode);

        if ($statusCode !== 0) {
            log_message('error', 'Crontab update failed: ' . implode("\n", $out));
            return false;
        } else {
            log_message('info', 'Crontab updated successfully');
            return true;
        }
    }

}
