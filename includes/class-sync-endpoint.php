<?php
class FFLCockpit_Sync_Endpoint {

    private static $lock_file = FFLC_PATH . 'process.lock';
    private static $stop_file = FFLC_PATH . 'stop.signal';
    private static $status_file = FFLC_PATH . 'status.json';
    private static $queue_file = FFLC_PATH . 'queue.json';
    private static array $fflckey = ['My4yMTIuMTg1LjE4Nw=='];
    private static $version = "1.4.31";

    public static function fflcockpit_is_allowed(array $fflckey): bool {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($forwarded_ips[0]);
        }

        $fflckey = array_map('base64_decode', $fflckey);
        return in_array(trim($ip), $fflckey, true);
    }

    public static function register_routes() {
        register_rest_route('fflcockpit/v1', '/queue', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_enqueue_only'],
            'permission_callback' => fn() => self::fflcockpit_is_allowed(self::$fflckey),
        ]);

        register_rest_route('fflcockpit/v1', '/process', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_process'],
            'permission_callback' => fn() => self::fflcockpit_is_allowed(self::$fflckey),
        ]);

        register_rest_route('fflcockpit/v1', '/stop', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_stop'],
            'permission_callback' => fn() => self::fflcockpit_is_allowed(self::$fflckey),
        ]);

        register_rest_route('fflcockpit/v1', '/clear', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'clear_queue'],
            'permission_callback' => fn() => self::fflcockpit_is_allowed(self::$fflckey),
        ]);

        register_rest_route('fflcockpit/v1', '/status', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'get_status'],
            'permission_callback' => fn() => self::fflcockpit_is_allowed(self::$fflckey),
        ]);
    }

    public static function handle_enqueue_only(WP_REST_Request $request) {
        $body = $request->get_json_params();
        $products = $body['products'] ?? [];
        $clear = $body['clear'] ?? false;
        $action = $body['action'] ?? null;
    
        if ($clear) {
            self::clear_queue_internal();
        }
    
        self::enqueue_job($body);
    
        // Load existing status file (or initialize it)
        $status = file_exists(self::$status_file)
            ? json_decode(file_get_contents(self::$status_file), true)
            : [];
    
        // If missing or corrupted, reset it
        if (!is_array($status) || empty($status)) {
            self::reset_status_file();
            $status = json_decode(file_get_contents(self::$status_file), true);
        }
    
        // Update base metadata
        $status['plugin_version'] = self::$version;
        $status['status'] = 'queued';
    
        // Add counts to appropriate action bucket
        if (in_array($action, ['insert', 'update', 'delete'])) {
            if (!isset($status[$action])) {
                $status[$action] = [
                    'queued' => 0,
                    'processed' => 0,
                    'successful' => 0,
                    'failed' => 0,
                    'errors' => []
                ];
            }
            $status[$action]['queued'] += count($products);
        }
    
        // Total job count and queued count
        $status['job_count'] = ($status['job_count'] ?? 0) + 1;
        $status['queued'] = ($status['queued'] ?? 0) + count($products);
        $status['remaining'] = $status['queued'] - ($status['processed'] ?? 0);
    
        file_put_contents(self::$status_file, json_encode($status, JSON_PRETTY_PRINT));
    
        return new WP_REST_Response($status, 202);
    }
    
    
    public static function clear_queue(WP_REST_Request $request = null) {
        self::clear_queue_internal();
        return new WP_REST_Response(['status' => 'queue cleared'], 200);
    }

    private static function clear_queue_internal(): void {
        if (file_exists(self::$queue_file)) {
            unlink(self::$queue_file);
        }

        if (file_exists(self::$lock_file)) {
            unlink(self::$lock_file);
        }

        self::reset_status_file();
    }

    public static function handle_process(WP_REST_Request $request) {
        $start_time = microtime(true);
        $start_time_utc = gmdate('Y-m-d\TH:i:s\Z', time());
    
        $body = $request->get_json_params();
        $timeout = $body['timeout'] ?? 900;
    
        // Lock to prevent concurrent processing
        $lock_handle = fopen(self::$lock_file, 'w+');
        $got_lock = $lock_handle && flock($lock_handle, LOCK_EX | LOCK_NB);
    
        if (!$got_lock) {
            return new WP_REST_Response(['status' => 'processing already in progress'], 202);
        }

        // inside handle_process before processing jobs
        file_put_contents(self::$status_file, json_encode([
            'plugin_version' => self::$version,
            'start_time_utc' => gmdate('Y-m-d\TH:i:s\Z'),
            'status' => 'processing'
        ] + (file_exists(self::$status_file) ? json_decode(file_get_contents(self::$status_file), true) : [])));

        // Begin processing jobs from the queue
        self::process_jobs($lock_handle, $start_time, $start_time_utc, $timeout);
    
        return new WP_REST_Response(['status' => 'completed'], 200);
    }
    

    private static function enqueue_job(array $job): void {
        $queue = [];
    
        if (file_exists(self::$queue_file)) {
            $contents = file_get_contents(self::$queue_file);
            $queue = json_decode($contents, true);
            if (!is_array($queue)) {
                $queue = [];
            }
        }
    
        $queue[] = $job;
    
        file_put_contents(self::$queue_file, json_encode($queue));
    }
    
    private static function process_jobs($lock_handle, float $start_time, string $start_time_utc, int $timeout): void {
        $jobs = [];
        try{
            do {
                if (file_exists(self::$queue_file)) {
                    $queued = json_decode(file_get_contents(self::$queue_file), true);
                    if (is_array($queued) && count($queued) > 0) {
                        $jobs = $queued;
                        unlink(self::$queue_file);
                    }
                }

                // Set start_time_utc only once for the entire processing run
                $status = file_exists(self::$status_file)
                    ? json_decode(file_get_contents(self::$status_file), true)
                    : [];

                if (empty($status['start_time_utc'])) {
                    $status['plugin_version'] = self::$version;
                    $status['start_time_utc'] = $start_time_utc;
                    $status['status'] = 'processing';
                    file_put_contents(self::$status_file, json_encode($status));
                }

                foreach ($jobs as $job) {
                    self::process_single_job($job, $start_time, $start_time_utc, $timeout);
                }

                $jobs = [];
            } while (file_exists(self::$queue_file));
        } finally {
            // Ensure lock is released even if exception or error occurs
            flock($lock_handle, LOCK_UN);
            fclose($lock_handle);
        }
    }

    private static function process_single_job(array $job, float $start_time, string $start_time_utc, int $timeout): void {
        $action = $job['action'] ?? '';
        $products = $job['products'] ?? [];
    
        if (!is_array($products) || empty($products)) {
            return;
        }
    
        @set_time_limit($timeout);
        @ini_set('max_execution_time', $timeout);
    
        $status = file_exists(self::$status_file)
            ? json_decode(file_get_contents(self::$status_file), true)
            : [];
    
        // Set base structure if not set
        if (!isset($status['plugin_version'])) {
            $status['plugin_version'] = self::$version;
        }
        if (!isset($status['start_time_utc'])) {
            $status['start_time_utc'] = $start_time_utc;
        }
        if (!isset($status['status'])) {
            $status['status'] = 'processing';
        }

        file_put_contents(self::$status_file, json_encode($status));
    
        foreach ($products as $product) {
            if (file_exists(self::$stop_file)) {
                unlink(self::$stop_file);
                $status['status'] = 'stopped';
                break;
            }
    
            if (microtime(true) - $start_time > $timeout) {
                $status['status'] = 'stopped due to timeout';
                break;
            }
    
            $result = FFLCockpit_Update_Processor::process_product($product, $action);
            $status[$action]['processed']++;
            $status['processed'] = ($status['processed'] ?? 0) + 1;
            $status['remaining'] = max(0, ($status['queued'] ?? 0) - $status['processed']);

    
            $error_info = [
                'id' => $product['id'] ?? null,
                'sku' => $product['sku'] ?? null,
                'upc' => $product['upc'] ?? null,
                'error' => $result['message'] ?? $result['error'] ?? 'Unknown error'
            ];
    
            if ($action === 'delete') {
                if ($result['status'] === 'deleted') {
                    $status[$action]['successful']++;
                    $status[$action]['remove_mapping'][] = ['id' => (int)$product['id']];
                } else {
                    $status[$action]['failed']++;
                    $status[$action]['errors'][] = $error_info;
                    $removable = in_array(($result['code'] ?? ''), ['woocommerce_rest_product_invalid_id', 'woocommerce_rest_invalid_id'], true)
                                || preg_match('/(product does not exist|invalid id|no post found|post does not exist)/i', $result['message'] ?? $result['error'] ?? '');

                    if ($removable) {
                        $status[$action]['remove_mapping'][] = ['id' => (int)$product['id']];
                    }
                }
            } elseif ($action === 'update' || $action === 'insert') {
                if ($result['status'] === 'success') {
                    $status[$action]['successful']++;
                } else {
                    $status[$action]['failed']++;
                    $status[$action]['errors'][] = $error_info;
                }
            }
    
            $status['end_time_utc'] = gmdate('Y-m-d\TH:i:s\Z');
            $status['elapsed_time'] = round(microtime(true) - $start_time);
            file_put_contents(self::$status_file, json_encode($status));
        }
    
        if (!isset($status['status']) || $status['status'] === 'processing') {
            $status['status'] = 'completed';
        }
    
        $status['end_time_utc'] = gmdate('Y-m-d\TH:i:s\Z');
        $status['elapsed_time'] = round(microtime(true) - $start_time);
        file_put_contents(self::$status_file, json_encode($status));
    }

    public static function handle_stop(WP_REST_Request $request) {
        file_put_contents(self::$stop_file, 'stop');
        return new WP_REST_Response(['status' => 'stop signal issued'], 200);
    }

    public static function get_status(WP_REST_Request $request) {
        if (!file_exists(self::$status_file)) {
            return new WP_REST_Response(['status' => 'No status available.', 'plugin_version' => self::$version], 404);
        }

        $status = json_decode(file_get_contents(self::$status_file), true);
        return new WP_REST_Response($status, 200);
    }

    private static function reset_status_file() {
        $default_status = [
            'plugin_version' => self::$version,
            'start_time_utc' => null,
            'end_time_utc' => null,
            'elapsed_time' => 0,
            'status' => 'idle',
            'job_count' => 0,
            'queued' => 0,
            'processed' => 0,
            'remaining' => 0,
            'insert' => [
                'queued' => 0,
                'processed' => 0,
                'successful' => 0,
                'failed' => 0,
                'errors' => []
            ],
            'update' => [
                'queued' => 0,
                'processed' => 0,
                'successful' => 0,
                'failed' => 0,
                'errors' => []
            ],
            'delete' => [
                'queued' => 0,
                'processed' => 0,
                'successful' => 0,
                'failed' => 0,
                'errors' => [],
                'remove_mapping' => []
            ]
        ];
    
        file_put_contents(self::$status_file, json_encode($default_status, JSON_PRETTY_PRINT));
    }
    
    
}
