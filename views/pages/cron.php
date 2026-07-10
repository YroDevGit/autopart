<?php
define('DB_HOST', env('dbhost'));
define('DB_NAME', env('database'));
define('DB_USER', env('dbuser'));
define('DB_PASS', env('dbpass'));
define('DB_CHARSET', env('dbcharset'));

define('BASE_PATH', dirname(__DIR__));
define('CTRX_CRON_TABLE', 'ctrx_cron');
define('CTRX_CRON_LOGS_TABLE', 'ctrx_cron_logs');

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $dbport = env('dbport');
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=$dbport;dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->initTables();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    private function initTables()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `" . CTRX_CRON_TABLE . "` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL UNIQUE,
                `description` TEXT,
                `file_path` VARCHAR(255) NOT NULL,
                `schedule_formats` TEXT NOT NULL,
                `schedule_type` VARCHAR(50) DEFAULT 'multiple',
                `schedule_data` TEXT,
                `cron_expressions` TEXT,
                `status` ENUM('active', 'inactive') DEFAULT 'active',
                `last_run` DATETIME DEFAULT NULL,
                `next_run` DATETIME DEFAULT NULL,
                `last_status` ENUM('success', 'failed', 'running', 'never') DEFAULT 'never',
                `last_output` TEXT,
                `cron_pass` VARCHAR(255) NOT NULL,
                `run_count` INT(11) DEFAULT 0,
                `fail_count` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `status_index` (`status`),
                KEY `next_run_index` (`next_run`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `" . CTRX_CRON_LOGS_TABLE . "` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `cron_id` INT(11) NOT NULL,
                `start_time` DATETIME NOT NULL,
                `end_time` DATETIME DEFAULT NULL,
                `status` ENUM('running', 'success', 'failed', 'timeout') DEFAULT 'running',
                `output` TEXT,
                `error` TEXT,
                `execution_time` FLOAT DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `cron_id_index` (`cron_id`),
                FOREIGN KEY (`cron_id`) REFERENCES `" . CTRX_CRON_TABLE . "` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getPassword()
    {
        $stmt = $this->get(CTRX_CRON_TABLE, []);
        return $stmt['cron_pass'] ?? null;
    }

    public function insert($table, $data)
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO `$table` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";
        $this->query($sql, array_values($data));
        $lastId = $this->pdo->lastInsertId();
        if (isset($data['cron_pass'])) {
            $this->query("update " . CTRX_CRON_TABLE . " set cron_pass = " . $data['cron_pass'] . " where 1");
        }
        return $lastId;
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $set = [];
        $params = [];
        foreach ($data as $key => $value) {
            $set[] = "`$key` = ?";
            $params[] = $value;
        }
        $sql = "UPDATE `$table` SET " . implode(', ', $set) . " WHERE $where";
        $this->query($sql, array_merge($params, $whereParams));
    }

    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM `$table` WHERE $where";
        $this->query($sql, $params);
    }

    public function get($table, $where = null, $params = [])
    {
        $sql = "SELECT * FROM `$table`";
        if ($where) {
            $sql = "SELECT * FROM `$table` WHERE $where";
        }
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function getAll($table, $where = '1', $params = [], $order = 'id DESC')
    {
        $sql = "SELECT * FROM `$table` WHERE $where ORDER BY $order";
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function getPastDueJobs()
    {
        return \Classes\Ctrx::getPastDueCronJobs();
    }
}

class ScheduleParser
{

    public static function parseMultiple($scheduleText)
    {
        $lines = array_filter(array_map('trim', explode("\n", $scheduleText)));
        $results = [];
        $validCount = 0;
        $cronExpressions = [];
        $scheduleData = [];
        $types = [];
        $nextRuns = [];

        foreach ($lines as $line) {
            $parsed = self::parseSingle($line);
            if ($parsed['type'] !== 'invalid') {
                $validCount++;
                $cronExpressions[] = $parsed['cron'];
                $scheduleData[] = $parsed['data'];
                $types[] = $parsed['type'];
                $results[] = $parsed;

                if ($parsed['cron']) {
                    $nextRun = CronExpressionParser::getNextRunTime($parsed['cron']);
                    if ($nextRun) {
                        $nextRuns[] = date('Y-m-d H:i:s', $nextRun);
                    }
                }
            }
        }

        if ($validCount === 0) {
            return ['type' => 'invalid', 'data' => [], 'cron' => null, 'results' => [], 'next_runs' => []];
        }

        $overallType = count(array_unique($types)) === 1 ? $types[0] : 'multiple';

        return [
            'type' => $overallType,
            'data' => $scheduleData,
            'cron' => implode("\n", $cronExpressions),
            'cron_array' => $cronExpressions,
            'results' => $results,
            'count' => $validCount,
            'next_runs' => $nextRuns
        ];
    }

    public static function parseSingle($scheduleText)
    {
        $scheduleText = trim($scheduleText);

        if (preg_match('/^(\d{4}-\d{2}-\d{2})\s+(\d{2}:\d{2})$/', $scheduleText, $matches)) {
            return [
                'type' => 'once',
                'data' => ['date' => $matches[1], 'time' => $matches[2]],
                'cron' => self::dateToCron($matches[1], $matches[2]),
                'display' => "Once on {$matches[1]} at {$matches[2]}"
            ];
        }

        if (preg_match('/^(\d{2}-\d{2})\s+(\d{2}:\d{2})$/', $scheduleText, $matches)) {
            return [
                'type' => 'yearly',
                'data' => ['month_day' => $matches[1], 'time' => $matches[2]],
                'cron' => self::annualToCron($matches[1], $matches[2]),
                'display' => "Yearly on {$matches[1]} at {$matches[2]}"
            ];
        }

        if (preg_match('/^(\d{1,2})\s+(\d{2}:\d{2})$/', $scheduleText, $matches)) {
            return [
                'type' => 'monthly',
                'data' => ['day' => $matches[1], 'time' => $matches[2]],
                'cron' => self::monthlyToCron($matches[1], $matches[2]),
                'display' => "Monthly on day {$matches[1]} at {$matches[2]}"
            ];
        }

        if (preg_match('/^([A-Z]{3}(?:\|[A-Z]{3})+)\s+(\d{2}:\d{2})$/', $scheduleText, $matches)) {
            return [
                'type' => 'weekly_days',
                'data' => ['days' => explode('|', $matches[1]), 'time' => $matches[2]],
                'cron' => self::weeklyDaysToCron($matches[1], $matches[2]),
                'display' => "Weekly on " . str_replace('|', ', ', $matches[1]) . " at {$matches[2]}"
            ];
        }

        if (preg_match('/^([A-Z]{3}(?:,[A-Z]{3})+)\s+(\d{2}:\d{2})$/', $scheduleText, $matches)) {
            return [
                'type' => 'weekly_days',
                'data' => ['days' => explode(',', $matches[1]), 'time' => $matches[2]],
                'cron' => self::weeklyDaysToCron(str_replace(',', '|', $matches[1]), $matches[2]),
                'display' => "Weekly on " . str_replace(',', ', ', $matches[1]) . " at {$matches[2]}"
            ];
        }

        if (preg_match('/^([A-Z]{3})\s+(\d{2}:\d{2})$/', $scheduleText, $matches)) {
            return [
                'type' => 'weekly',
                'data' => ['day' => $matches[1], 'time' => $matches[2]],
                'cron' => self::weeklyToCron($matches[1], $matches[2]),
                'display' => "Weekly on {$matches[1]} at {$matches[2]}"
            ];
        }

        if (preg_match('/^(\d{2}:\d{2})$/', $scheduleText, $matches)) {
            return [
                'type' => 'daily',
                'data' => ['time' => $matches[1]],
                'cron' => self::dailyToCron($matches[1]),
                'display' => "Daily at {$matches[1]}"
            ];
        }

        if (preg_match('/^(\d{2}:\d{2}(?:,\d{2}:\d{2})+)$/', $scheduleText, $matches)) {
            $times = explode(',', $matches[1]);
            $cronParts = [];
            foreach ($times as $time) {
                list($hour, $minute) = explode(':', $time);
                $cronParts[] = "$minute $hour * * *";
            }
            return [
                'type' => 'daily_times',
                'data' => ['times' => $times],
                'cron' => implode('|', $cronParts),
                'display' => "Daily at " . implode(', ', $times)
            ];
        }

        if (preg_match('/^(every\s+)?(\d+)\s*(?:min|mins|minute|minutes|hour|hours|hr|hrs)?$/i', $scheduleText, $matches)) {
            $interval = intval($matches[2]);
            if ($interval > 0 && $interval < 60) {
                return [
                    'type' => 'interval',
                    'data' => ['interval' => $interval],
                    'cron' => "*/{$interval} * * * *",
                    'display' => "Every {$interval} minutes"
                ];
            }
        }

        if (preg_match('/^(\d+)$/', $scheduleText, $matches)) {
            $interval = intval($matches[1]);
            if ($interval > 0 && $interval < 60) {
                return [
                    'type' => 'interval',
                    'data' => ['interval' => $interval],
                    'cron' => "*/{$interval} * * * *",
                    'display' => "Every {$interval} minutes"
                ];
            }
        }

        return [
            'type' => 'invalid',
            'data' => [],
            'cron' => null,
            'display' => "Invalid: {$scheduleText}"
        ];
    }

    private static function dateToCron($date, $time)
    {
        list($year, $month, $day) = explode('-', $date);
        list($hour, $minute) = explode(':', $time);
        return "$minute $hour $day $month *";
    }

    private static function annualToCron($monthDay, $time)
    {
        list($month, $day) = explode('-', $monthDay);
        list($hour, $minute) = explode(':', $time);
        return "$minute $hour $day $month *";
    }

    private static function monthlyToCron($day, $time)
    {
        list($hour, $minute) = explode(':', $time);
        return "$minute $hour $day * *";
    }

    private static function weeklyDaysToCron($days, $time)
    {
        $dayMap = ['SUN' => 0, 'MON' => 1, 'TUE' => 2, 'WED' => 3, 'THU' => 4, 'FRI' => 5, 'SAT' => 6];
        $dayNumbers = [];
        foreach (explode('|', $days) as $day) {
            if (isset($dayMap[$day])) {
                $dayNumbers[] = $dayMap[$day];
            }
        }
        list($hour, $minute) = explode(':', $time);
        $dayString = implode(',', $dayNumbers);
        return "$minute $hour * * $dayString";
    }

    private static function weeklyToCron($day, $time)
    {
        $dayMap = ['SUN' => 0, 'MON' => 1, 'TUE' => 2, 'WED' => 3, 'THU' => 4, 'FRI' => 5, 'SAT' => 6];
        $dayNumber = $dayMap[$day] ?? 0;
        list($hour, $minute) = explode(':', $time);
        return "$minute $hour * * $dayNumber";
    }

    private static function dailyToCron($time)
    {
        list($hour, $minute) = explode(':', $time);
        return "$minute $hour * * *";
    }
}

class CronExpressionParser
{
    public static function isDue($expression, $timestamp = null)
    {
        $timestamp = $timestamp ?? time();

        if (strpos($expression, "\n") !== false) {
            $expressions = array_filter(array_map('trim', explode("\n", $expression)));
            foreach ($expressions as $expr) {
                if (self::isDueSingle($expr, $timestamp)) {
                    return true;
                }
            }
            return false;
        }

        if (strpos($expression, '|') !== false) {
            $expressions = explode('|', $expression);
            foreach ($expressions as $expr) {
                if (self::isDueSingle($expr, $timestamp)) {
                    return true;
                }
            }
            return false;
        }

        return self::isDueSingle($expression, $timestamp);
    }

    private static function isDueSingle($expression, $timestamp)
    {
        $parts = preg_split('/\s+/', trim($expression));

        if (count($parts) !== 5) {
            return false;
        }

        list($minute, $hour, $day, $month, $weekday) = $parts;
        $date = getdate($timestamp);

        return self::checkField($minute, $date['minutes']) &&
            self::checkField($hour, $date['hours']) &&
            self::checkField($day, $date['mday']) &&
            self::checkField($month, $date['mon']) &&
            self::checkField($weekday, $date['wday']);
    }

    private static function checkField($field, $value)
    {
        if ($field === '*') return true;
        if (strpos($field, ',') !== false) {
            return in_array($value, array_map('intval', explode(',', $field)));
        }
        if (strpos($field, '-') !== false) {
            list($start, $end) = explode('-', $field);
            return $value >= intval($start) && $value <= intval($end);
        }
        if (strpos($field, '/') !== false) {
            list($base, $step) = explode('/', $field);
            if ($base === '*') {
                return $value % intval($step) === 0;
            }
            if (strpos($base, '-') !== false) {
                list($start, $end) = explode('-', $base);
                if ($value >= intval($start) && $value <= intval($end)) {
                    return ($value - intval($start)) % intval($step) === 0;
                }
                return false;
            }
        }
        return intval($field) === $value;
    }

    public static function getNextRunTime($expression, $fromTime = null)
    {
        $fromTime = $fromTime ?? time();
        $timestamp = $fromTime + 60;
        $maxAttempts = 10000;

        while ($maxAttempts > 0) {
            if (self::isDue($expression, $timestamp)) {
                return $timestamp;
            }
            $timestamp += 60;
            $maxAttempts--;
        }
        return null;
    }
}

class CronManager
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($status = null)
    {
        $where = '1';
        $params = [];
        if ($status) {
            $where = 'status = ?';
            $params = [$status];
        }
        return $this->db->getAll(CTRX_CRON_TABLE, $where, $params, 'id DESC');
    }

    public function getById($id)
    {
        return $this->db->get(CTRX_CRON_TABLE, 'id = ?', [$id]);
    }

    public function create($data)
    {
        $parsed = ScheduleParser::parseMultiple($data['schedule_formats']);
        if ($parsed['type'] === 'invalid') {
            throw new Exception('No valid schedule formats found');
        }

        $data['schedule_type'] = $parsed['type'];
        $data['schedule_data'] = json_encode($parsed['data']);
        $data['cron_expressions'] = $parsed['cron'];
        $data['created_at'] = date('Y-m-d H:i:s');

        if (!empty($parsed['next_runs'])) {
            $data['next_run'] = min($parsed['next_runs']);
        }

        return $this->db->insert(CTRX_CRON_TABLE, $data);
    }

    public function update($id, $data)
    {
        $parsed = ScheduleParser::parseMultiple($data['schedule_formats']);
        if ($parsed['type'] === 'invalid') {
            throw new Exception('No valid schedule formats found');
        }

        $data['schedule_type'] = $parsed['type'];
        $data['schedule_data'] = json_encode($parsed['data']);
        $data['cron_expressions'] = $parsed['cron'];
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!empty($parsed['next_runs'])) {
            $data['next_run'] = min($parsed['next_runs']);
        }

        $this->db->update(CTRX_CRON_TABLE, $data, 'id = ?', [$id]);

        if (isset($data['cron_pass'])) {
            $this->db->query("update " . CTRX_CRON_TABLE . " set cron_pass = " . $data['cron_pass'] . " where 1");
        }
    }

    public function delete($id)
    {
        $this->db->delete(CTRX_CRON_TABLE, 'id = ?', [$id]);
    }

    public function toggleStatus($id, $status)
    {
        $this->db->update(CTRX_CRON_TABLE, ['status' => $status], 'id = ?', [$id]);
    }

    public function runNow($id)
    {
        $cron = $this->getById($id);
        if (!$cron) {
            return ['success' => false, 'message' => 'Cron job not found'];
        }

        try {
            $startTime = microtime(true);
            $output ="";
            $executionTime = microtime(true) - $startTime;

            $parsed = ScheduleParser::parseMultiple($cron['schedule_formats']);
            $nextRun = !empty($parsed['next_runs']) ? min($parsed['next_runs']) : null;

            $this->db->update(CTRX_CRON_TABLE, [
                'last_run' => date('Y-m-d H:i:s'),
                'last_status' => 'success',
                'last_output' => is_string($output) ? $output : json_encode($output),
                'run_count' => $cron['run_count'] + 1,
                'next_run' => $nextRun
            ], 'id = ?', [$id]);

            $this->logExecution($id, 'success', $output, $executionTime);

            return ['success' => true, 'message' => 'Cron executed successfully', 'output' => $output];
        } catch (Exception $e) {
            $this->db->update(CTRX_CRON_TABLE, [
                'last_run' => date('Y-m-d H:i:s'),
                'last_status' => 'failed',
                'last_output' => $e->getMessage(),
                'fail_count' => $cron['fail_count'] + 1
            ], 'id = ?', [$id]);

            $this->logExecution($id, 'failed', null, null, $e->getMessage());

            return ['success' => false, 'message' => 'Execution failed: ' . $e->getMessage()];
        }
    }

    public function processPastDueJobs()
    {
        $pastDueJobs = $this->db->getPastDueJobs();
        $results = [];

        foreach ($pastDueJobs as $job) {
            //$result = $this->runNow($job['id']);
            $results[] = [
                'id' => $job['id'],
                'name' => $job['name'],
                'url' => $job['file_path'],
                'password' => $job['cron_pass'],
                'past' => $pastDueJobs,
                'result' => ['success' => true, 'message' => 'Cron executed successfully', 'output' => []],
            ];
        }

        return $results;
    }

    private function executeFile($filePath)
    {
        return [];
        $baseUrl = rtrim(env('rootpath'), '/');
        $apiPath = ltrim($filePath, '/');

        if (!str_ends_with($apiPath, '.php')) {
            $apiPath = $apiPath . '.php';
        }

        $url = $baseUrl . '/' . $apiPath;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            throw new Exception("CURL error: $error");
        }

        if ($httpCode >= 400) {
            throw new Exception("HTTP error $httpCode: $response");
        }

        return $response ?: 'File executed successfully (no output)';
    }

    private function logExecution($cronId, $status, $output = null, $executionTime = null, $error = null)
    {
        $this->db->insert(CTRX_CRON_LOGS_TABLE, [
            'cron_id' => $cronId,
            'start_time' => date('Y-m-d H:i:s', strtotime('-1 second')),
            'end_time' => date('Y-m-d H:i:s'),
            'status' => $status,
            'output' => $output,
            'error' => $error,
            'execution_time' => $executionTime
        ]);
    }

    public function getLogs($cronId, $limit = 50)
    {
        $sql = "SELECT * FROM " . CTRX_CRON_LOGS_TABLE . " WHERE cron_id = ? ORDER BY id DESC LIMIT ?";
        $stmt = $this->db->query($sql, [$cronId, $limit]);
        return $stmt->fetchAll();
    }

    public function getStatistics()
    {
        $stats = [];

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM " . CTRX_CRON_TABLE);
        $stats['total'] = $stmt->fetch()['total'];

        $stmt = $this->db->query("SELECT COUNT(*) as active FROM " . CTRX_CRON_TABLE . " WHERE status = 'active'");
        $stats['active'] = $stmt->fetch()['active'];

        $stmt = $this->db->query("
            SELECT last_status, COUNT(*) as count 
            FROM " . CTRX_CRON_TABLE . " 
            WHERE last_status != 'never' 
            GROUP BY last_status
        ");
        $stats['statuses'] = $stmt->fetchAll();

        $stmt = $this->db->query("
            SELECT COUNT(*) as today 
            FROM " . CTRX_CRON_LOGS_TABLE . " 
            WHERE DATE(start_time) = CURDATE()
        ");
        $stats['today_runs'] = $stmt->fetch()['today'];

        $pastDue = $this->db->getPastDueJobs();
        $stats['past_due'] = count($pastDue);

        return $stats;
    }
}

function timeAgo($timestamp)
{
    $now = time();
    $diff = $timestamp - $now;

    if ($diff < 0) {
        return 'past due ⚡';
    }

    $minutes = floor($diff / 60);
    if ($minutes < 1) return 'less than a minute';
    if ($minutes < 60) return $minutes . 'm';

    $hours = floor($minutes / 60);
    if ($hours < 24) return $hours . 'h';

    $days = floor($hours / 24);
    if ($days < 7) return $days . 'd';

    $weeks = floor($days / 7);
    if ($weeks < 4) return $weeks . 'w';

    $months = floor($days / 30);
    if ($months < 12) return $months . 'mo';

    $years = floor($days / 365);
    return $years . 'y';
}

function getTimeStatusClass($timestamp)
{
    $now = time();
    if ($timestamp < $now) {
        return 'past-due';
    }
    $diff = $timestamp - $now;
    if ($diff < 3600) {
        return 'soon';
    }
    return 'normal';
}

$dbd = Database::getInstance();
$password = $dbd->getPassword();

$cronManager = new CronManager();
$pastDueResults = $cronManager->processPastDueJobs();
$pastDueCount = 0;

print_r(($pastDueResults));exit;

if(isset($_GET['runNow']) && isset($_GET['runId'])){
    $res = $cronManager->runNow($_GET['runId']);
    echo json_encode($res);exit;
}

if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $cronManager = new CronManager();
    $response = ['success' => false, 'message' => 'Invalid action'];

    try {
        switch ($_POST['action']) {
            case 'add':
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'cron_pass' => $_POST['cron_pass'],
                    'file_path' => $_POST['file_path'],
                    'schedule_formats' => $_POST['schedule_formats'],
                    'status' => $_POST['status'] ?? 'active'
                ];
                $id = $cronManager->create($data);
                $response = ['success' => true, 'message' => 'Cron added successfully', 'id' => $id];
                break;

            case 'update':
                $id = $_POST['id'];
                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'file_path' => $_POST['file_path'],
                    'schedule_formats' => $_POST['schedule_formats'],
                    'status' => $_POST['status'] ?? 'active'
                ];
                if (isset($_POST['cron_pass'])) {
                    $data['cron_pass'] = $_POST['cron_pass'];
                }
                $cronManager->update($id, $data);
                $response = ['success' => true, 'message' => 'Cron updated successfully'];
                break;

            case 'delete':
                $id = $_POST['id'];
                $cronManager->delete($id);
                $response = ['success' => true, 'message' => 'Cron deleted successfully'];
                break;

            case 'toggle':
                $id = $_POST['id'];
                $status = $_POST['status'];
                $cronManager->toggleStatus($id, $status);
                $response = ['success' => true, 'message' => 'Status updated successfully'];
                break;

            case 'run':
                $id = $_POST['id'];
                $result = $cronManager->runNow($id);
                $response = $result;
                break;

            case 'get_logs':
                $id = $_POST['id'];
                $logs = $cronManager->getLogs($id);
                $response = ['success' => true, 'logs' => $logs];
                break;

            case 'get_stats':
                $stats = $cronManager->getStatistics();
                $response = ['success' => true, 'stats' => $stats];
                break;

            case 'get_cron':
                $id = $_POST['id'];
                $cron = $cronManager->getById($id);
                if ($cron) {
                    $response = ['success' => true, 'cron' => $cron];
                } else {
                    $response = ['success' => false, 'message' => 'Cron not found'];
                }
                break;

            case 'parse_schedules':
                $scheduleText = $_POST['schedule'];
                $parsed = ScheduleParser::parseMultiple($scheduleText);
                $response = [
                    'success' => $parsed['type'] !== 'invalid',
                    'type' => $parsed['type'],
                    'cron' => $parsed['cron'],
                    'count' => $parsed['count'] ?? 0,
                    'results' => $parsed['results'] ?? [],
                    'next_runs' => $parsed['next_runs'] ?? [],
                    'message' => $parsed['type'] === 'invalid' ? 'No valid schedules found' : "{$parsed['count']} schedules parsed successfully"
                ];
                break;

            case 'process_past_due':
                $results = $cronManager->processPastDueJobs();
                $response = [
                    'success' => true,
                    'processed' => count($results),
                    'results' => $results,
                    'hasData' => empty($results) ? false : true,
                    'message' => 'Processed ' . count($results) . ' past due jobs'
                ];
                break;
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }

    echo json_encode($response);
    exit;
}

$crons = $cronManager->getAll();
$stats = $cronManager->getStatistics();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTRX Cron Manager</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #ffffff;
            color: #222222;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            border-bottom: 2px solid #e0e0e0;
            padding: 20px 0 15px 0;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #222;
        }

        .header h1 small {
            font-size: 14px;
            font-weight: 400;
            color: #666;
            margin-left: 10px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: 1px solid #ccc;
            background: #fafafa;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: #222;
            transition: all 0.2s;
            border-radius: 3px;
        }

        .btn:hover {
            background: #f0f0f0;
            border-color: #999;
        }

        .btn-primary {
            background: #222;
            color: #fff;
            border-color: #222;
        }

        .btn-primary:hover {
            background: #000;
            border-color: #000;
        }

        .btn-success {
            background: #28a745;
            color: #fff;
            border-color: #28a745;
        }

        .btn-success:hover {
            background: #218838;
            border-color: #218838;
        }

        .btn-warning {
            background: #ffc107;
            color: #222;
            border-color: #ffc107;
        }

        .btn-warning:hover {
            background: #e0a800;
            border-color: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
            border-color: #c82333;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
            border-color: #5a6268;
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 12px;
        }

        .btn-icon {
            padding: 5px 10px;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            border: 1px solid #e0e0e0;
            padding: 15px;
            text-align: center;
            background: #fafafa;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 600;
            color: #222;
            display: block;
        }

        .stat-card .label {
            color: #666;
            font-size: 13px;
            margin-top: 4px;
        }

        .stat-card .number.past-due {
            color: #dc3545;
        }

        .table-container {
            border: 1px solid #e0e0e0;
            padding: 0;
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-header h2 {
            font-size: 18px;
            color: #222;
            font-weight: 600;
        }

        .table-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .table-filters select,
        .table-filters input {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 13px;
            background: #fafafa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f5f5f5;
        }

        th {
            padding: 10px 15px;
            text-align: left;
            font-weight: 600;
            color: #222;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e0e0e0;
        }

        td {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            font-size: 14px;
        }

        tr:hover {
            background: #f8f8f8;
        }

        .status-badge {
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid #ccc;
            display: inline-block;
        }

        .status-active {
            border-color: #28a745;
            color: #28a745;
        }

        .status-inactive {
            border-color: #dc3545;
            color: #dc3545;
        }

        .status-success {
            border-color: #28a745;
            color: #28a745;
        }

        .status-failed {
            border-color: #dc3545;
            color: #dc3545;
        }

        .status-running {
            border-color: #ffc107;
            color: #856404;
        }

        .status-never {
            border-color: #999;
            color: #666;
        }

        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .schedule-line {
            font-size: 13px;
            color: #222;
            padding: 2px 0;
            font-family: 'Courier New', monospace;
        }

        .schedule-type-badge {
            font-size: 11px;
            border: 1px solid #ccc;
            padding: 1px 8px;
            color: #666;
        }

        .count-badge {
            background: #f0f0f0;
            color: #222;
            padding: 1px 8px;
            font-size: 11px;
            border: 1px solid #ddd;
            margin-left: 3px;
        }

        .file-path {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #222;
        }

        .next-run-time {
            font-size: 13px;
            color: #222;
            font-family: 'Courier New', monospace;
        }

        .next-run-time.past-due {
            color: #dc3545;
            font-weight: 600;
        }

        .next-run-label {
            font-size: 11px;
            color: #666;
            display: block;
        }

        .next-run-label.past-due {
            color: #dc3545;
            font-weight: 600;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            padding: 30px;
            max-width: 750px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            border: 1px solid #ccc;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-header h3 {
            font-size: 20px;
            color: #222;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .modal-close:hover {
            color: #222;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 4px;
            color: #222;
            font-size: 13px;
        }

        .form-group label .required {
            color: #dc3545;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 14px;
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: #666;
            background: #fff;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.5;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }

        .help-text code {
            background: #f5f5f5;
            padding: 2px 6px;
            font-size: 11px;
            font-family: monospace;
            border: 1px solid #eee;
        }

        .example-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 4px;
            margin-top: 8px;
        }

        .example-item {
            font-size: 12px;
            padding: 3px 8px;
            background: #f5f5f5;
            border: 1px solid #eee;
            cursor: pointer;
            font-family: monospace;
            color: #222;
        }

        .example-item:hover {
            background: #e8e8e8;
        }

        .schedule-preview {
            background: #f8f8f8;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            margin-top: 10px;
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }

        .schedule-preview .header {
            font-weight: 600;
            color: #222;
            margin-bottom: 5px;
            border-bottom: none;
            padding: 0;
        }

        .schedule-preview .line {
            font-size: 13px;
            padding: 3px 0;
            border-bottom: 1px solid #eee;
            font-family: monospace;
        }

        .schedule-preview .line .valid {
            color: #28a745;
        }

        .schedule-preview .line .invalid {
            color: #dc3545;
        }

        .schedule-preview .line .type {
            color: #666;
            font-size: 11px;
        }

        .schedule-preview .line .next-run {
            color: #666;
            font-size: 11px;
            margin-left: 10px;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            padding: 12px 18px;
            color: #fff;
            font-weight: 500;
            border: 1px solid #ccc;
            min-width: 280px;
            background: #222;
        }

        .toast-success {
            background: #28a745;
            border-color: #28a745;
        }

        .toast-error {
            background: #dc3545;
            border-color: #dc3545;
        }

        .toast-info {
            background: #17a2b8;
            border-color: #17a2b8;
        }

        .log-viewer {
            background: #f8f8f8;
            color: #222;
            padding: 15px;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #e0e0e0;
            margin-top: 10px;
        }

        .log-viewer .log-entry {
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }

        .log-viewer .log-time {
            color: #666;
            margin-right: 10px;
        }

        .log-viewer .log-success {
            color: #28a745;
        }

        .log-viewer .log-failed {
            color: #dc3545;
        }

        .auto-run-notice {
            background: #f8f8f8;
            border: 1px solid #e0e0e0;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: <?= $pastDueCount > 0 ? 'block' : 'none' ?>;
        }

        .auto-run-notice .count {
            font-weight: 600;
            color: #dc3545;
        }

        .auto-run-notice .details {
            margin-top: 5px;
            font-size: 13px;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            color: #222;
            margin-bottom: 8px;
        }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #ccc;
            border-top: 2px solid #222;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .example-grid {
                grid-template-columns: 1fr 1fr;
            }

            .table-container {
                padding: 0;
            }

            .actions {
                flex-direction: column;
            }

            .modal-content {
                padding: 20px;
                margin: 10px;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <div>
                <h1>⏰ CTRX Cron Manager <small>Auto-Run</small></h1>
                <p style="color:#666; margin-top:4px;">Run PHP files with multiple schedule formats</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openAddModal()">+ Add Cron Job</button>
                <button class="btn" onclick="refreshData()">↻ Refresh</button>
            </div>
        </div>

        <?php if ($pastDueCount > 0): ?>
            <div class="auto-run-notice">
                <strong>⚡ Auto-Run Executed:</strong>
                <span class="count"><?= $pastDueCount ?></span> past due job(s) were automatically executed.
                <div class="details">
                    <?php foreach ($pastDueResults as $result): ?>
                        <?= htmlspecialchars($result['name']) ?>:
                        <?= $result['result']['success'] ? '✅ Success' : '❌ ' . $result['result']['message'] ?>
                        <br>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <span class="number" id="statTotal"><?= $stats['total'] ?? 0 ?></span>
                <div class="label">Total Jobs</div>
            </div>
            <div class="stat-card">
                <span class="number" id="statActive"><?= $stats['active'] ?? 0 ?></span>
                <div class="label">Active Jobs</div>
            </div>
            <div class="stat-card">
                <span class="number" id="statToday"><?= $stats['today_runs'] ?? 0 ?></span>
                <div class="label">Executions Today</div>
            </div>
            <div class="stat-card">
                <span class="number <?= ($stats['past_due'] ?? 0) > 0 ? 'past-due' : '' ?>" id="statPastDue">
                    <?= $stats['past_due'] ?? 0 ?>
                </span>
                <div class="label">Past Due Jobs</div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2>📋 Scheduled Jobs</h2>
                <div class="table-filters">
                    <select id="filterStatus" onchange="applyFilter()">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <input type="text" id="filterSearch" placeholder="Search jobs..." onkeyup="applyFilter()">
                </div>
            </div>

            <div id="cronTableContainer">
                <?php if (empty($crons)): ?>
                    <div class="empty-state">
                        <div class="icon">📭</div>
                        <h3>No cron jobs yet</h3>
                        <p>Click "Add Cron Job" to create your first scheduled task.</p>
                    </div>
                <?php else: ?>
                    <table id="cronTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>File Path</th>
                                <th>Schedules</th>
                                <th>Status</th>
                                <th>Last Run</th>
                                <th>Next Run</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cronTableBody">
                            <?php foreach ($crons as $cron): ?>
                                <?php
                                $isPastDue = false;
                                if ($cron['next_run']) {
                                    $nextTimestamp = strtotime($cron['next_run']);
                                    $isPastDue = $nextTimestamp < time();
                                }
                                ?>
                                <tr id="cron-<?= $cron['id'] ?>">
                                    <td>
                                        <strong><?= htmlspecialchars($cron['name']) ?></strong>
                                        <?php if ($cron['description']): ?>
                                            <br><small style="color:#666;"><?= htmlspecialchars($cron['description']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="file-path"><?= htmlspecialchars($cron['file_path']) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $formats = array_filter(array_map('trim', explode("\n", $cron['schedule_formats'])));
                                        $displayCount = min(count($formats), 3);
                                        $hiddenCount = count($formats) - $displayCount;
                                        ?>
                                        <?php for ($i = 0; $i < $displayCount; $i++): ?>
                                            <div class="schedule-line"><?= htmlspecialchars($formats[$i]) ?></div>
                                        <?php endfor; ?>
                                        <?php if ($hiddenCount > 0): ?>
                                            <div class="schedule-line" style="color:#666;">+ <?= $hiddenCount ?> more</div>
                                        <?php endif; ?>
                                        <div style="margin-top:4px;">
                                            <span class="schedule-type-badge"><?= ucfirst($cron['schedule_type']) ?></span>
                                            <span class="count-badge"><?= count($formats) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $cron['status'] ?>">
                                            <?= ucfirst($cron['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($cron['last_run']): ?>
                                            <?= date('M d, H:i', strtotime($cron['last_run'])) ?>
                                            <br>
                                            <span class="status-badge status-<?= $cron['last_status'] ?>" style="font-size:10px;">
                                                <?= ucfirst($cron['last_status']) ?>
                                            </span>
                                            <br><small style="color:#666;">#<?= $cron['run_count'] ?></small>
                                        <?php else: ?>
                                            <span style="color:#666;">Never</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($cron['next_run']): ?>
                                            <?php $nextTimestamp = strtotime($cron['next_run']); ?>
                                            <span class="next-run-time <?= $isPastDue ? 'past-due' : '' ?>">
                                                <?= date('M d, H:i:s', $nextTimestamp) ?>
                                            </span>
                                            <span class="next-run-label <?= $isPastDue ? 'past-due' : '' ?>">
                                                <?= $isPastDue ? '⚠️ PAST DUE - Auto-Ran' : 'in ' . timeAgo($nextTimestamp) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color:#666;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn btn-success btn-sm btn-icon" onclick="runCron('<?= $cron['file_path'] ?>', true, '<?=$cron['name']?>' ?? null)" title="Run now">▶</button>
                                            <button class="btn btn-warning btn-sm btn-icon" onclick="editCron(<?= $cron['id'] ?>)" title="Edit">✎</button>
                                            <button class="btn btn-secondary btn-sm btn-icon" onclick="viewLogs(<?= $cron['id'] ?>)" title="View logs">📋</button>
                                            <button class="btn btn-sm btn-icon <?= $cron['status'] === 'active' ? 'btn-danger' : 'btn-success' ?>"
                                                onclick="toggleStatus(<?= $cron['id'] ?>, '<?= $cron['status'] === 'active' ? 'inactive' : 'active' ?>')"
                                                title="<?= $cron['status'] === 'active' ? 'Disable' : 'Enable' ?>">
                                                <?= $cron['status'] === 'active' ? '⏸' : '▶' ?>
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-icon" onclick="deleteCron(<?= $cron['id'] ?>)" title="Delete">✕</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal" id="cronModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add Cron Job</h3>
                <button class="modal-close" onclick="closeModal('cronModal')">&times;</button>
            </div>
            <form id="cronForm" onsubmit="saveCron(event)">
                <input type="hidden" name="id" id="cronId">
                <input type="hidden" name="action" id="formAction" value="add">

                <div class="form-group">
                    <label>Job Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="name" id="jobName" placeholder="e.g., daily_backup" required>
                </div>

                <div class="form-group">
                    <label>apikey <span class="required">*</span></label>
                    <input type="text" class="form-control" name="cron_pass" id="cron_pass" placeholder="e.g., daily_backup" value="<?= $password ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="description" id="jobDescription" placeholder="What does this job do?" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label>API Path <span class="required">*</span></label>
                    <input type="text" class="form-control" name="file_path" id="jobFilePath" placeholder="e.g., user/notify" required>
                    <div class="help-text">API endpoint path (will be called via AJAX)</div>
                </div>

                <div class="form-group">
                    <label>Schedule Formats <span class="required">*</span></label>
                    <textarea class="form-control" name="schedule_formats" id="jobSchedule"
                        placeholder="2026-10-05 14:33&#10;MON 13:03&#10;5&#10;09:00,13:00,17:00"
                        rows="6"
                        oninput="previewSchedules()" required></textarea>
                    <div class="help-text">
                        <strong>One schedule per line. Examples:</strong>
                    </div>
                    <div class="example-grid">
                        <span class="example-item" onclick="addExample('2026-10-05 14:33')">📅 Once</span>
                        <span class="example-item" onclick="addExample('03-14 00:00')">📅 Yearly</span>
                        <span class="example-item" onclick="addExample('23 21:04')">📅 Monthly</span>
                        <span class="example-item" onclick="addExample('MON 13:03')">📅 Weekly</span>
                        <span class="example-item" onclick="addExample('MON|WED|FRI 01:46')">📅 Weekdays</span>
                        <span class="example-item" onclick="addExample('03:00')">📅 Daily</span>
                        <span class="example-item" onclick="addExample('5')">⏱️ Every 5m</span>
                        <span class="example-item" onclick="addExample('10')">⏱️ Every 10m</span>
                        <span class="example-item" onclick="addExample('09:00,13:00,17:00')">📅 Multiple times</span>
                    </div>
                    <div class="schedule-preview" id="schedulePreview">
                        <div class="header">📋 Schedule Preview:</div>
                        <div id="previewContent"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="jobStatus">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal('cronModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveButton">Save Cron</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="logsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📋 Execution Logs</h3>
                <button class="modal-close" onclick="closeModal('logsModal')">&times;</button>
            </div>
            <div id="logsContent">
                <div style="text-align:center; padding:20px; color:#666;">
                    <div class="spinner"></div>
                    <p>Loading logs...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        function ajax(data, callback) {
            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showToast(result.message, 'success');
                    } else {
                        showToast(result.message || 'An error occurred', 'error');
                    }
                    if (callback) callback(result);
                })
                .catch(error => {
                    showToast('Network error: ' + error.message, 'error');
                    if (callback) callback({
                        success: false,
                        message: error.message
                    });
                });
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        function openModal(id) {
            document.getElementById(id).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = '';
        }

        function previewSchedules() {
            const schedule = document.getElementById('jobSchedule').value;
            if (schedule.trim().length < 1) {
                document.getElementById('schedulePreview').style.display = 'none';
                return;
            }

            ajax({
                action: 'parse_schedules',
                schedule: schedule
            }, function(result) {
                const preview = document.getElementById('schedulePreview');
                const content = document.getElementById('previewContent');

                if (result.success && result.results) {
                    preview.style.display = 'block';
                    let html = `<div style="margin-bottom:5px;color:#666;">${result.count} schedule(s) parsed</div>`;
                    result.results.forEach((r, index) => {
                        const status = r.type !== 'invalid' ? '✓' : '✗';
                        const cls = r.type !== 'invalid' ? 'valid' : 'invalid';
                        const nextRun = result.next_runs && result.next_runs[index] ?
                            new Date(result.next_runs[index]).toLocaleString() :
                            'N/A';
                        html += `<div class="line">
                            <span class="${cls}">${status}</span>
                            ${r.display || r.type}
                            ${r.cron ? ` <span style="color:#666;font-size:11px;">(${r.cron})</span>` : ''}
                            <span class="next-run">→ ${nextRun}</span>
                        </div>`;
                    });
                    content.innerHTML = html;
                } else {
                    preview.style.display = 'block';
                    content.innerHTML = `<div class="line invalid">✗ No valid schedules found</div>`;
                }
            });
        }

        function addExample(value) {
            const textarea = document.getElementById('jobSchedule');
            const current = textarea.value;
            if (current) {
                textarea.value = current + '\n' + value;
            } else {
                textarea.value = value;
            }
            previewSchedules();
        }

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Cron Job';
            document.getElementById('formAction').value = 'add';
            document.getElementById('cronForm').reset();
            document.getElementById('cronId').value = '';
            document.getElementById('cron_pass').value = '<?= $password ?>';
            console.log('<?= $password ?>');
            document.getElementById('saveButton').textContent = 'Save Cron';
            document.getElementById('schedulePreview').style.display = 'none';
            openModal('cronModal');
        }

        function editCron(id) {
            ajax({
                action: 'get_cron',
                id: id
            }, function(result) {
                if (result.success) {
                    const cron = result.cron;
                    document.getElementById('modalTitle').textContent = 'Edit Cron Job';
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('cronId').value = cron.id;
                    document.getElementById('jobName').value = cron.name;
                    document.getElementById('jobDescription').value = cron.description || '';
                    document.getElementById('jobFilePath').value = cron.file_path;
                    document.getElementById('jobSchedule').value = cron.schedule_formats;
                    document.getElementById('jobStatus').value = cron.status;
                    document.getElementById('saveButton').textContent = 'Update Cron';
                    previewSchedules();
                    openModal('cronModal');
                }
            });
        }

        function saveCron(event) {
            event.preventDefault();
            const form = document.getElementById('cronForm');
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            if (!data.action) data.action = document.getElementById('formAction').value;

            const isUpdate = data.action === 'update';
            const submitBtn = document.getElementById('saveButton');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Saving...';
            ajax(data, function(result) {
                submitBtn.disabled = false;
                submitBtn.textContent = isUpdate ? 'Update Cron' : 'Save Cron';
                if (result.success) {
                    closeModal('cronModal');
                    setTimeout(refreshData, 500);
                }
            });
        }

        function deleteCron(id) {
            if (!confirm('Are you sure you want to delete this cron job?')) {
                return;
            }
            ajax({
                action: 'delete',
                id: id
            }, function(result) {
                if (result.success) {
                    document.getElementById('cron-' + id)?.remove();
                    refreshStats();
                }
            });
        }

        function toggleStatus(id, status) {
            const action = status === 'active' ? 'Enable' : 'Disable';
            if (!confirm(`Are you sure you want to ${action.toLowerCase()} this cron job?`)) {
                return;
            }
            ajax({
                action: 'toggle',
                id: id,
                status: status
            }, function(result) {
                if (result.success) {
                    refreshData();
                }
            });
        }

        function runCron(id, conf = true, name = null) {
            if (conf) {
                if (!confirm('Run this cron job now?')) {
                    return;
                }
            }
            const row = document.getElementById('cron-' + id);
            const actions = row?.querySelector('.actions');
            const originalHTML = actions?.innerHTML;
            if (actions) {
                actions.innerHTML = '<span class="spinner"></span>';
            }

            fetch("api/" + id, {
                    method: "POST"
                })
                .then(response => response.json())
                .then(data => {
                    if(data.code == 200){
                        showToast(`${name}: ${data.message ?? 'Executed'}`);
                    }else{
                        showToast(`${name}: ${data.message ?? 'Executed'}`, "error");
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }

        function viewLogs(id) {
            openModal('logsModal');
            document.getElementById('logsContent').innerHTML = `
                <div style="text-align:center; padding:20px; color:#666;">
                    <div class="spinner"></div>
                    <p>Loading logs...</p>
                </div>
            `;

            ajax({
                action: 'get_logs',
                id: id
            }, function(result) {
                if (result.success && result.logs) {
                    let html = '<div class="log-viewer">';
                    if (result.logs.length === 0) {
                        html += '<div style="color:#666; text-align:center; padding:20px;">No logs found for this job</div>';
                    } else {
                        result.logs.forEach(log => {
                            const statusClass = log.status === 'success' ? 'log-success' : 'log-failed';
                            html += `
                                <div class="log-entry">
                                    <span class="log-time">${new Date(log.start_time).toLocaleString()}</span>
                                    <span class="${statusClass}">${log.status.toUpperCase()}</span>
                                    ${log.execution_time ? ` - ${log.execution_time.toFixed(2)}s` : ''}
                                    ${log.output ? `<br><span style="color:#666;font-size:11px;">${log.output.substring(0, 200)}</span>` : ''}
                                </div>
                            `;
                        });
                    }
                    html += '</div>';
                    document.getElementById('logsContent').innerHTML = html;
                } else {
                    document.getElementById('logsContent').innerHTML = `
                        <div style="text-align:center; padding:20px; color:#dc3545;">
                            <p>Failed to load logs</p>
                        </div>
                    `;
                }
            });
        }

        function refreshData() {
            window.location.reload();
        }

        function fc() {
            ajax({
                action: 'process_past_due'
            }, function(result) {
                if (result.success && result.hasData) {
                    let data = result.results ?? [];
                    for (let x in data) {
                        let row = data[x];
                        let url = row.url ?? undefined;
                        if (url) {
                            runCron(url, false, row.name);
                        }
                    }
                }
            });
        }

        fc();

        function refreshStats() {
            ajax({
                action: 'get_stats'
            }, function(result) {
                if (result.success && result.stats) {
                    document.getElementById('statTotal').textContent = result.stats.total || 0;
                    document.getElementById('statActive').textContent = result.stats.active || 0;
                    document.getElementById('statToday').textContent = result.stats.today_runs || 0;

                    const pastDue = result.stats.past_due || 0;
                    const el = document.getElementById('statPastDue');
                    el.textContent = pastDue;
                    if (pastDue > 0) {
                        el.className = 'number past-due';
                    } else {
                        el.className = 'number';
                    }
                }
            });
        }

        function applyFilter() {
            const status = document.getElementById('filterStatus').value;
            const search = document.getElementById('filterSearch').value.toLowerCase();
            const rows = document.querySelectorAll('#cronTableBody tr');

            rows.forEach(row => {
                let show = true;

                if (status) {
                    const statusCell = row.querySelector('.status-badge');
                    if (statusCell && !statusCell.textContent.toLowerCase().includes(status)) {
                        show = false;
                    }
                }

                if (search && show) {
                    const text = row.textContent.toLowerCase();
                    if (!text.includes(search)) {
                        show = false;
                    }
                }

                row.style.display = show ? '' : 'none';
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(modal => {
                    closeModal(modal.id);
                });
            }
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                openAddModal();
            }
        });
    </script>

</body>

</html>