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
        $search = $this->input->get('search');
        if (!empty($search)) {
            $where['name'] = new MongoDB\BSON\Regex($search, 'i');
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
            'cron_id' => '_id',
            'updated_at' => 'updated_at',
            'created_at' => 'created_at',
            'last_executed' => 'last_executed' // handled later in PHP
        ];
        if (isset($allowedFields[$sortField]) && $sortField !== 'last_executed') {
            $order_by = [$allowedFields[$sortField] => $sortDir];
        } else {
            // Default sorting by newest entry
            $order_by = ['_id' => -1];
        }

		$q = $this->mongo_db->where($where)->order_by($order_by)->limit($limit)->offset(($page - 1) * $limit)->get('active_crons');
        $count = $this->mongo_db->where($where)->count('active_crons');
        $ret = [];
        
        foreach($q as $row){
            $row->cronToDisplay = $this->cronToDisplay($row->schedule);
            // Fetch latest execution time for this cron
            $lastExec = $this->mongo_db->where(['cron_id' => $row->cron_id])->order_by(['executed_at' => -1])->limit(1)->get('cron_executions');
            foreach($lastExec as $lastExecRes){
                $row->last_executed = $lastExecRes->executed_at ?? null;
            }
            
            $ret[] = $row;
        }

        // PHP-level sorting for last_executed (comes from a different collection)
        if ($sortField === 'last_executed') {
            usort($ret, function($a, $b) use ($sortDir) {
                $aVal = $a->last_executed ?? '';
                $bVal = $b->last_executed ?? '';
                return $sortDir === 1 ? strcmp($aVal, $bVal) : strcmp($bVal, $aVal);
            });
        }
        
        $data = [
            'crons' => $ret,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
            'search' => $search,
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
            $logFile = !empty($row->log_file) ? $row->log_file : strtolower(str_replace(' ', '_', $row->name));
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

            $run_id = 'RUN_' . strtoupper(substr(md5(uniqid()), 0, 10));
            $start_time = date('Y-m-d H:i:s');
            $start_ts = microtime(true);

            // Set lock status in active_crons
            $this->mongo_db->where(['cron_id' => $id])->set(['is_locked' => true])->update('active_crons');

            // Insert running execution log
            $execution = [
                'run_id' => $run_id,
                'cron_id' => $id,
                'cron_name' => $row->name,
                'controller' => $controller,
                'method' => $method,
                'status' => 'running',
                'start_time' => $start_time,
                'end_time' => '',
                'duration' => 0.0,
                'server' => gethostname(),
                'pid' => getmypid(),
                'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
                'error_message' => '',
                'raw_log' => '',
                'is_locked' => true
            ];
            $this->mongo_db->insert('cron_executions', $execution);

            log_message('error', "Running Command {$fullCmd}");
            $output = [];
            $status = 0;
            exec($fullCmd, $output, $status);

            $end_time = date('Y-m-d H:i:s');
            $duration = round(microtime(true) - $start_ts, 2);
            $memory = round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB';

            // Release lock
            $this->mongo_db->where(['cron_id' => $id])->set(['is_locked' => false])->update('active_crons');

            if ($status === 0) {
                $this->session->set_flashdata('success', 'Cron executed successfully.');
            } else {
                log_message('error', "Cron execution failed. Command: {$fullCmd} Exit Code: {$status}");
                $this->session->set_flashdata('error', 'Cron execution failed. Exit Code: ' . $status);
            }

            $status_str = $status === 0 ? 'success' : 'failed';
            $error_msg = $status === 0 ? '' : 'Execution failed with code ' . $status;
            $raw_log_content = file_exists($logPath) ? file_get_contents($logPath) : '';

            // Update execution record
            $this->mongo_db->where(['run_id' => $run_id])->set([
                'status' => $status_str,
                'end_time' => $end_time,
                'duration' => $duration,
                'memory_usage' => $memory,
                'error_message' => $error_msg,
                'raw_log' => $raw_log_content,
                'is_locked' => false
            ])->update('cron_executions');

            $log = [
                'executed_at' => date('Y-m-d H:i:s'),
                'status'      => $status_str,
                'output'      => $raw_log_content,
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
        $minute_gap = $_POST['minute_gap'] ?? 1;
        $hour_gap = $_POST['hour_gap'] ?? 1;

        $scheduleExpr = $this->get_schedule($schedule, $cron_time, $cron_day, $cron_dom, $minute_gap, $hour_gap);

        // DB command (used for matching later)
        $commandKey = $controller . " " . $function;

        $cronId = 'VFCR' . getuniqnumid("active_crons");
        
        $cron_log_file_name = trim($_POST['cron_log_file_name'] ?? '');
        $logFile = $cron_log_file_name !== '' ? $cron_log_file_name : strtolower(str_replace(" ", "_", $cron_name));

        $insert = [
            'cron_id' => $cronId,
            'name' => $cron_name,
            'log_file' => $logFile,
            'schedule' => $scheduleExpr,
            'command' => $commandKey,
            'status' => 'active',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $this->mongo_db->insert('active_crons', $insert);

        // Build cron line (KEEP IT SIMPLE - no escapeshellarg confusion)
        $cronLine =
            $scheduleExpr . " php " .
            $this->config->item('cron_execution_index_path') . " " .
            $controller . " " . $function .
            " >> /var/log/{$logFile}.log 2>&1";

        // Load existing crontab
        $existing = shell_exec("sudo -n /usr/bin/crontab -u root -l 2> /dev/null");
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
        exec("sudo -n /usr/bin/crontab -u root " . escapeshellarg($tmpFile) . " 2>&1", $out, $statusCode);

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }

        if ($statusCode !== 0) {
            log_message('error', 'Failed to install crontab: ' . implode("\n", $out));
            $this->redirect_message("error", "Failed to add cron to system crontab. Reason: " . implode(" | ", $out));
        }
        log_message('info', 'Cron added to system crontab successfully.');

        $this->redirect_message("success", "Cron added successfully");
    }

    public function sync_crontab() {
        exec("sudo -n /usr/bin/crontab -u root -l 2>/dev/null", $output, $returnVar);
        if ($returnVar !== 0 || empty($output)) {
            $this->redirect_message("error", "No crontab found or unable to read crontab.");
        }

        $added_count = 0;
        foreach ($output as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Detect status
            $is_commented = (strpos($line, '#') === 0);
            $status = $is_commented ? 'inactive' : 'active';

            // Clean the line for parsing
            $clean_line = ltrim($line, '# ');
            
            // Normalize spaces
            $clean_line = preg_replace('/\s+/', ' ', $clean_line);
            $parts = explode(' ', $clean_line);

            // Locate index.php
            $index_idx = -1;
            foreach ($parts as $idx => $part) {
                if (strpos($part, 'index.php') !== false) {
                    $index_idx = $idx;
                    break;
                }
            }

            if ($index_idx === -1 || !isset($parts[$index_idx + 1]) || !isset($parts[$index_idx + 2])) {
                continue; // Not a CodeIgniter cron line or malformed
            }

            $controller = $parts[$index_idx + 1];
            $method = $parts[$index_idx + 2];
            
            // Skip redirects, pipes, or flags if they somehow match
            if (strpos($controller, '-') === 0 || strpos($method, '-') === 0 || strpos($controller, '>') === 0 || strpos($method, '>') === 0) {
                continue;
            }

            $command = $controller . ' ' . $method;

            // Extract schedule (first 5 fields of clean_line)
            $schedule = implode(' ', array_slice($parts, 0, 5));

            // Check if already exists in DB
            $exists = $this->mongo_db->where(['command' => $command])->count('active_crons');
            if ($exists > 0) {
                continue;
            }

            // Determine Name and Log File
            $name = '';
            $logFile = '';
            if (preg_match('/\/var\/log\/([a-zA-Z0-9_\-]+)\.log/', $line, $matches)) {
                $logFile = $matches[1];
                $name = ucwords(str_replace('_', ' ', $matches[1]));
            } else {
                $name = ucwords(str_replace(['_', '-'], ' ', $controller . ' ' . $method));
                $logFile = strtolower(str_replace(" ", "_", $name));
            }

            // Generate Unique Cron ID
            $cronId = 'VFCR' . getuniqnumid("active_crons");

            $insert = [
                'cron_id' => $cronId,
                'name' => $name,
                'log_file' => $logFile,
                'schedule' => $schedule,
                'command' => $command,
                'status' => $status,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];

            $this->mongo_db->insert('active_crons', $insert);
            $added_count++;
        }

        if ($added_count > 0) {
            $this->redirect_message("success", "Successfully synced {$added_count} crons from crontab to database.");
        } else {
            $this->redirect_message("info", "No new crons found to sync from crontab.");
        }
    }

    public function update_schedule($post_data)
    {
        $id = trim($post_data['edit_cron_id'] ?? '');
        if ($id === '') {
            $this->redirect_message("error", "Invalid Cron ID.");
        }

        $schedule = trim($post_data['edit_cron_schedule'] ?? '');
        if ($schedule === '' || $schedule === '#') {
            $this->redirect_message("error", "Schedule cannot be blank.");
        }

        $cron_time = $post_data['edit_cron_time'] ?? null;
        $cron_day = $post_data['edit_cron_day'] ?? null;
        $cron_dom = $post_data['edit_cron_day_of_the_month'] ?? null;
        $minute_gap = $post_data['edit_minute_gap'] ?? 1;
        $hour_gap = $post_data['edit_hour_gap'] ?? 1;

        $newScheduleExpr = $this->get_schedule($schedule, $cron_time, $cron_day, $cron_dom, $minute_gap, $hour_gap);

        if (!$newScheduleExpr) {
            $this->redirect_message("error", "Invalid schedule format.");
        }

        $edit_cron_log_file_name = trim($post_data['edit_cron_log_file_name'] ?? '');

        // Fetch existing cron
        $q = $this->mongo_db->where(['cron_id' => $id])->get('active_crons');
        foreach ($q as $row) {
            $oldSchedule = $row->schedule;
            $cron_name = $row->name;
            $command = $row->command;
            $status = $row->status;
            
            $logFile = $edit_cron_log_file_name !== '' ? $edit_cron_log_file_name : (!empty($row->log_file) ? $row->log_file : strtolower(str_replace(" ", "_", $cron_name)));

            list($controller, $function) = explode(' ', $command);

            // Update database
            $this->mongo_db->where(['cron_id' => $id])->set([
                'schedule' => $newScheduleExpr,
                'log_file' => $logFile,
                'updated_at' => date("Y-m-d H:i:s")
            ])->update('active_crons');

            // Add log
            $log = [
                'executed_at' => date('Y-m-d H:i:s'),
                'status'      => 'success',
                'output'      => "Schedule updated from '{$oldSchedule}' to '{$newScheduleExpr}'",
                'triggered_by'=> 'system_edit',
                'command'     => 'Edit Schedule'
            ];
            $this->mongo_db->where(['cron_id' => $id])->push(['logs' => $log])->update('active_crons');

            // Update Crontab
            $this->updateCronInCrontab($controller, $function, $newScheduleExpr, $cron_name, $status, $logFile);

            $this->redirect_message("success", "Cron updated successfully.");
        }

        $this->redirect_message("error", "Cron not found.");
    }

    public function updateCronInCrontab($controller, $function, $newScheduleExpr, $cron_name, $status, $logFile = '')
    {
        log_message('info', "Updating crontab entry for {$controller} {$function}");
        $identifier = trim($controller . " " . $function);

        exec("sudo -n /usr/bin/crontab -u root -l 2>/dev/null", $output, $returnVar);
        $output = $output ?: [];

        $updated = [];
        $found = false;

        if ($logFile === '') {
            $logFile = strtolower(str_replace(" ", "_", $cron_name));
        }
        $newCronLine =
            $newScheduleExpr . " php " .
            $this->config->item('cron_execution_index_path') . " " .
            $controller . " " . $function .
            " >> /var/log/{$logFile}.log 2>&1";

        foreach ($output as $line) {
            $originalLine = $line;
            $cleanLine = preg_replace('/^#\s*/', '', $originalLine);

            if (strpos($cleanLine, $identifier) !== false) {
                $found = true;
                // Preserve active/inactive comment status
                if ($status === 'inactive') {
                    $originalLine = "# " . $newCronLine;
                } else {
                    $originalLine = $newCronLine;
                }
            }
            $updated[] = rtrim($originalLine);
        }

        // If not found in crontab for some reason, append it (if status is active/inactive accordingly)
        if (!$found) {
            if ($status === 'inactive') {
                $updated[] = "# " . $newCronLine;
            } else {
                $updated[] = $newCronLine;
            }
        }

        // Write to temporary file
        $tmpFile = __DIR__ . "/../../tmp/vf_crontab_" . time() . ".txt";
        $tmpDir = dirname($tmpFile);
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        file_put_contents($tmpFile, implode("\n", $updated) . PHP_EOL);

        exec("sudo -n /usr/bin/crontab -u root " . escapeshellarg($tmpFile) . " 2>&1", $out, $statusCode);

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }

        if ($statusCode !== 0) {
            log_message('error', 'Crontab update failed during edit: ' . implode("\n", $out));
            $this->redirect_message("error", "Crontab update failed during edit. Reason: " . implode(" | ", $out));
            return false;
        } else {
            log_message('info', 'Crontab updated successfully during edit');
            return true;
        }
    }

    public function get_schedule($add_cron_schedule, $cron_time = null, $cron_day = null, $cron_day_of_the_month = null, $minute_gap = 1, $hour_gap = 1) {
        switch ($add_cron_schedule) {

            case 'every_minute':
                if ($minute_gap > 1) {
                    return '*/' . $minute_gap . ' * * * *';
                }
                return '* * * * *';

            case 'hourly':
                if ($hour_gap > 1) {
                    return '0 */' . $hour_gap . ' * * *';
                }
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

        exec("sudo -n /usr/bin/crontab -u root -l 2>/dev/null", $output, $returnVar);

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

        exec("sudo -n /usr/bin/crontab -u root " . escapeshellarg($tmpFile) . " 2>&1", $out, $statusCode);

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }

        if ($statusCode !== 0) {
            log_message('error', 'Crontab update failed: ' . implode("\n", $out));
            $this->redirect_message("error", "Crontab update failed. Reason: " . implode(" | ", $out));
            return false;
        } else {
            log_message('info', 'Crontab updated successfully');
            return true;
        }
    }

    public function delete_cron($id) {
        $q = $this->mongo_db->where(['cron_id' => $id])->get('active_crons');
        foreach ($q as $row) {
            $command = $row->command;
            list($controller, $function) = explode(' ', $command);
            
            log_message('info', "Deleting crontab entry for {$controller} {$function}");
            $identifier = trim($controller . " " . $function);

            exec("sudo -n /usr/bin/crontab -u root -l 2>/dev/null", $output, $returnVar);
            $output = $output ?: [];

            $updated = [];
            foreach ($output as $line) {
                $cleanLine = preg_replace('/^#\s*/', '', $line);
                if (strpos($cleanLine, $identifier) === false) {
                    $updated[] = rtrim($line);
                }
            }

            $tmpFile = __DIR__ . "/../../tmp/vf_crontab_" . time() . ".txt";
            $tmpDir = dirname($tmpFile);
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }
            file_put_contents($tmpFile, implode("\n", $updated) . PHP_EOL);

            exec("sudo -n /usr/bin/crontab -u root " . escapeshellarg($tmpFile) . " 2>&1", $out, $statusCode);

            if (file_exists($tmpFile)) {
                unlink($tmpFile);
            }

            if ($statusCode === 0) {
                $this->mongo_db->where(['cron_id' => $id])->delete('active_crons');
                $this->redirect_message("success", "Cron deleted successfully.");
            } else {
                log_message('error', 'Crontab delete failed: ' . implode("\n", $out));
                $this->redirect_message("error", "Failed to remove cron from system crontab. Reason: " . implode(" | ", $out));
            }
        }
        $this->redirect_message("error", "Cron not found.");
    }

    public function seed_sample_data() {}

    public function logs_list() {
        $wheres = ['_id' => ['$exists' => true]];
        
        $cron_filter = $this->input->get('cron');
        if (!empty($cron_filter)) {
            $wheres['cron_id'] = $cron_filter;
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

        $q = $this->mongo_db->where($wheres)->order_by(['start_time' => -1])->limit($limit)->offset($offset)->get('cron_executions');
        $count = $this->mongo_db->where($wheres)->count('cron_executions');

        $active_crons = $this->mongo_db->where(['_id' => ['$exists' => true]])->get('active_crons');

        $data = [
            'logs' => $q,
            'count' => $count,
            'active_crons' => $active_crons,
            'page' => $page,
            'limit' => $limit,
            'filters' => [
                'cron' => $cron_filter,
                'status' => $status_filter,
                'run_id' => $run_id_filter,
                'date_range' => $date_range
            ]
        ];

        $this->load->view('dashboard/logs', $data);
    }

    public function log_details($run_id) {
        $q = $this->mongo_db->where(['run_id' => $run_id])->get('cron_executions');
        if (empty($q)) {
            show_error('Execution log not found');
        }

        $execution = null;
        foreach ($q as $row) {
            $execution = $row;
            break;
        }
        $cron_id = $execution->cron_id;

        $previous_runs = $this->mongo_db->where(['cron_id' => $cron_id, 'run_id' => ['$ne' => $run_id]])->order_by(['start_time' => -1])->limit(5)->get('cron_executions');

        $last_success = null;
        $last_success_q = $this->mongo_db->where(['cron_id' => $cron_id, 'status' => 'success'])->order_by(['start_time' => -1])->limit(1)->get('cron_executions');
        foreach ($last_success_q as $row) {
            $last_success = $row;
            break;
        }

        $last_failed = null;
        $last_failed_q = $this->mongo_db->where(['cron_id' => $cron_id, 'status' => 'failed'])->order_by(['start_time' => -1])->limit(1)->get('cron_executions');
        foreach ($last_failed_q as $row) {
            $last_failed = $row;
            break;
        }

        $data = [
            'execution' => $execution,
            'previous_runs' => $previous_runs,
            'last_success' => $last_success,
            'last_failed' => $last_failed
        ];

        $this->load->view('dashboard/log_details', $data);
    }

    public function retry_execution($run_id) {
        $q = $this->mongo_db->where(['run_id' => $run_id])->get('cron_executions');
        if (empty($q)) {
            $this->redirect_message("error", "Execution log not found.");
        }
        foreach ($q as $row) {
            $this->run_now($row->cron_id);
            break;
        }
    }

    public function view_raw_log($run_id) {
        $q = $this->mongo_db->where(['run_id' => $run_id])->get('cron_executions');
        if (empty($q)) {
            show_error('Execution log not found');
        }
        foreach ($q as $row) {
            header('Content-Type: text/plain');
            echo $row->raw_log;
            exit;
        }
    }

    public function analytics() {
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');

        $today_runs = $this->mongo_db->where([
            'start_time' => ['$gte' => $today_start, '$lte' => $today_end]
        ])->count('cron_executions');

        $today_success = $this->mongo_db->where([
            'start_time' => ['$gte' => $today_start, '$lte' => $today_end],
            'status' => 'success'
        ])->count('cron_executions');

        $today_failed = $this->mongo_db->where([
            'start_time' => ['$gte' => $today_start, '$lte' => $today_end],
            'status' => 'failed'
        ])->count('cron_executions');

        $today_timeout = $this->mongo_db->where([
            'start_time' => ['$gte' => $today_start, '$lte' => $today_end],
            'status' => 'timeout'
        ])->count('cron_executions');

        $success_rate = $today_runs > 0 ? round(($today_success / $today_runs) * 100, 2) : 0;

        $all_runs = $this->mongo_db->where(['_id' => ['$exists' => true]])->get('cron_executions');
        $total_duration = 0;
        $success_runs_count = 0;
        $durations = [];
        $run_counts = [];
        
        foreach ($all_runs as $run) {
            $name = $run->cron_name;
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
        $slowest_cron = !empty($avg_durations) ? key($avg_durations) . ' (' . round(current($avg_durations), 2) . 's avg)' : 'N/A';

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
            ])->count('cron_executions');

            $daily_success[] = $this->mongo_db->where([
                'start_time' => ['$gte' => $day_start, '$lte' => $day_end],
                'status' => 'success'
            ])->count('cron_executions');

            $daily_failed[] = $this->mongo_db->where([
                'start_time' => ['$gte' => $day_start, '$lte' => $day_end],
                'status' => 'failed'
            ])->count('cron_executions');
        }

        $top_executed_labels = array_slice(array_keys($run_counts), 0, 10);
        $top_executed_values = array_slice(array_values($run_counts), 0, 10);

        $top_slow_labels = array_slice(array_keys($avg_durations), 0, 10);
        $top_slow_values = array_map(function($v) { return round($v, 2); }, array_slice(array_values($avg_durations), 0, 10));

        $data = [
            'today_runs' => $today_runs,
            'today_success' => $today_success,
            'today_failed' => $today_failed,
            'today_timeout' => $today_timeout,
            'success_rate' => $success_rate,
            'avg_duration' => $avg_duration,
            'most_executed' => $most_executed,
            'slowest_cron' => $slowest_cron,
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

        $this->load->view('dashboard/analytics', $data);
    }

    public function health() {
        $running = $this->mongo_db->where(['status' => 'running'])->get('cron_executions');
        
        $stuck = [];
        $one_hour_ago = date('Y-m-d H:i:s', time() - 3600);
        foreach ($running as $run) {
            if ($run->start_time < $one_hour_ago) {
                $stuck[] = $run;
            }
        }

        $timeouts = $this->mongo_db->where(['status' => 'timeout'])->order_by(['start_time' => -1])->limit(20)->get('cron_executions');

        $yesterday = date('Y-m-d H:i:s', time() - 86400);
        $failed = $this->mongo_db->where([
            'status' => 'failed',
            'start_time' => ['$gte' => $yesterday]
        ])->order_by(['start_time' => -1])->get('cron_executions');

        $locked_crons = $this->mongo_db->where(['is_locked' => true])->get('active_crons');

        $data = [
            'running' => $running,
            'stuck' => $stuck,
            'timeouts' => $timeouts,
            'failed' => $failed,
            'locked_crons' => $locked_crons
        ];

        $this->load->view('dashboard/health', $data);
    }

    public function release_lock($cron_id) {
        $this->mongo_db->where(['cron_id' => $cron_id])->set(['is_locked' => false])->update('active_crons');
        
        $this->mongo_db->where(['cron_id' => $cron_id, 'status' => 'running'])->set([
            'status' => 'failed',
            'error_message' => 'Lock manually released by Admin.',
            'end_time' => date('Y-m-d H:i:s')
        ])->update('cron_executions');

        $this->session->set_flashdata('success', 'Lock released successfully.');
        redirect('crons/health');
    }

    public function mark_timeout($run_id) {
        $q = $this->mongo_db->where(['run_id' => $run_id])->get('cron_executions');
        foreach ($q as $row) {
            $this->mongo_db->where(['run_id' => $run_id])->set([
                'status' => 'timeout',
                'error_message' => 'Execution manually marked as timeout.',
                'end_time' => date('Y-m-d H:i:s')
            ])->update('cron_executions');

            $this->mongo_db->where(['cron_id' => $row->cron_id])->set(['is_locked' => false])->update('active_crons');
            break;
        }

        $this->session->set_flashdata('success', 'Execution marked as timeout.');
        redirect('crons/health');
    }

}
