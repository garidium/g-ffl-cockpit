<?php
class FFLCockpit_Sync_Endpoint {

    private static $lock_file = FFLC_PATH . 'process.lock';
    private static $stop_file = FFLC_PATH . 'stop.signal';
    private static $status_file = FFLC_PATH . 'status.json';
    private static array $fflckey = ['My4yMTIuMTg1LjE4Nw=='];

    public static function fflcockpit_is_allowed(array $fflckey): bool {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($forwarded_ips[0]);
        }

        // Decode obfuscated IPs and check against actual IP
        $fflckey = array_map('base64_decode', $fflckey);
        return in_array(trim($ip), $fflckey, true);
    }
    
    public static function register_routes() {
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

        register_rest_route('fflcockpit/v1', '/status', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'get_status'],
            'permission_callback' => fn() => self::fflcockpit_is_allowed(self::$fflckey),
        ]);
    }

    public static function handle_process(WP_REST_Request $request) {
        // Record the start time
        $start_time = microtime(true);
        // Record start time in UTC
        $start_time_utc = gmdate('Y-m-d\TH:i:s\Z', time());

        // Attempt to acquire lock
        $lock_handle = fopen(self::$lock_file, 'w+');
        if (!$lock_handle || !flock($lock_handle, LOCK_EX | LOCK_NB)) {
            if ($lock_handle) {
                fclose($lock_handle);
            }
            return new WP_REST_Response(['error' => 'Process is already running.'], 409);
        }
    
        // Reset stop signal
        if (file_exists(self::$stop_file)) {
            unlink(self::$stop_file);
        }
    
        // Process initialization
        $body = $request->get_json_params();
        $action = $body['action'] ?? '';
        $timeout = $body['timeout'] ?? 900;
        $products = $body['products'] ?? [];
    
        @set_time_limit($timeout);
        @ini_set('max_execution_time', $timeout);
    
        if (!is_array($products) || empty($products)) {
            flock($lock_handle, LOCK_UN);
            fclose($lock_handle);
            return new WP_REST_Response(['error' => 'No products found'], 400);
        }

        // Initialize status
        $total_products = count($products);
        $status = [
            'start_time_utc' => $start_time_utc,
            'total' => $total_products,
            'processed' => 0,
            'remaining' => $total_products,
            'successful_updates' => 0,
            'failed_updates' => 0,
            'deleted_ids' => [],
            'delete_failed_ids' => [],
            'successful_deletes' => 0,
            'failed_deletes' => 0,
            'status' => 'processing'
        ];
        file_put_contents(self::$status_file, json_encode($status));
    
        // Processing loop
        foreach ($products as $product) {
            // Check for stop signal
            if (file_exists(self::$stop_file)) {
                unlink(self::$stop_file); // Clean up stop file
                $status['status'] = 'stopped';
                // Calculate total runtime
                $status['total_runtime'] = microtime(true) - $start_time;
                $status['end_time_utc'] = gmdate('Y-m-d\TH:i:s\Z', time());
                file_put_contents(self::$status_file, json_encode($status));
                flock($lock_handle, LOCK_UN);
                fclose($lock_handle);
                return new WP_REST_Response($status, 200);
            }

            // enforce the timeout
            if (microtime(true) - $start_time > $timeout){
                $status['status'] = 'stopped due to timeout';
                $status['total_runtime'] = microtime(true) - $start_time;
                $status['end_time_utc'] = gmdate('Y-m-d\TH:i:s\Z', time());
                file_put_contents(self::$status_file, json_encode($status));
                flock($lock_handle, LOCK_UN);
                fclose($lock_handle);
                return new WP_REST_Response($status, 200);
            }

            // Process the product
            $result = FFLCockpit_Update_Processor::process_product($product, $action);

            // Update status based on action and result
            if ($action === 'delete') {
                if ($result['status'] === 'deleted' && !empty($product['id'])) {
                    $status['deleted_ids'][] = ['id' => (int)$product['id']];
                    $status['successful_deletes']++;
                } else {
                    $status['delete_failed_ids'][] = ['id' => (int)$product['id']];
                    $status['failed_deletes']++;
                }
            } elseif ($action === 'update') {
                if ($result['status'] === 'success' && !empty($product['id'])) {
                    $status['successful_updates']++;
                } else {
                    $status['failed_updates']++;
                }
            }

            // Update processed and remaining counts
            $status['processed']++;
            $status['remaining'] = $status['total'] - $status['processed'];
            $status['total_runtime'] = round(microtime(true) - $start_time);
            $status['end_time_utc'] = gmdate('Y-m-d\TH:i:s\Z', time());

            // Write updated status to file
            file_put_contents(self::$status_file, json_encode($status));
        }

        // Finalize status
        $status['status'] = 'completed';
        file_put_contents(self::$status_file, json_encode($status));

        // Release lock and close handle
        flock($lock_handle, LOCK_UN);
        fclose($lock_handle);

        return new WP_REST_Response($status, 200);
    }
    

    public static function handle_stop(WP_REST_Request $request) {
        // Create stop signal file
        file_put_contents(self::$stop_file, 'stop');
        return new WP_REST_Response(['status' => 'stop signal issued'], 200);
    }

    public static function get_status(WP_REST_Request $request) {
        if (!file_exists(self::$status_file)) {
            return new WP_REST_Response(['status' => 'No status available.'], 404);
        }

        $status = json_decode(file_get_contents(self::$status_file), true);
        return new WP_REST_Response($status, 200);
    }
}
?>
