<?php
class FFLCockpit_Sync_Endpoint {

    private static $lock_file = FFLC_PATH . 'process.lock';
    private static $stop_file = FFLC_PATH . 'stop.signal';
    private static $status_file = FFLC_PATH . 'status.json';
    private static $queue_file = FFLC_PATH . 'queue.json';
    private static array $fflckey = ['My4yMTIuMTg1LjE4Nw=='];
    private static $version = "1.4.32";

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
        
        register_rest_route('fflcockpit/v1', '/export', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_export'],
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

    public static function handle_export(WP_REST_Request $request) {
        try {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            $timeout = 900;
            @set_time_limit($timeout);
            @ini_set('max_execution_time', $timeout);
            
            if (!self::fflcockpit_is_allowed(self::$fflckey)) {
                return new WP_REST_Response(['error' => 'Not authorized'], 401);
            }

            // Create a temporary file
            $temp_file = tempnam(sys_get_temp_dir(), 'fflc_export_');
            $fp = fopen($temp_file, 'w');
            
            fwrite($fp, '{"products":[');
            
            // Get total count more efficiently
            $total_count = wc_get_products([
                'limit' => 1,
                'status' => 'publish',
                'return' => 'ids',
                'paginate' => true,
            ])->total;
            
            $batch_size = 500; // Increased from 20
            $processed = 0;
            $is_first = true;
            
            // Process products in optimized batches
            for ($offset = 0; $offset < $total_count; $offset += $batch_size) {
                $products = wc_get_products([
                    'limit' => $batch_size,
                    'offset' => $offset,
                    'status' => 'publish',
                    'return' => 'objects'
                ]);
                
                foreach ($products as $product) {
                    if (!$product || !is_object($product)) continue;
                    
                    // Get only required product data
                    $product_data = [
                        'id' => $product->get_id(),
                        'upc' => get_post_meta($product->get_id(), 'upc', true) ?: '',
                        'sku' => $product->get_sku(),
                        'name' => $product->get_name(),
                        'type' => $product->get_type(),
                        'status' => $product->get_status(),
                        'description' => $product->get_description(),
                        'short_description' => $product->get_short_description(),
                        'regular_price' => $product->get_regular_price(),
                        'sale_price' => $product->get_sale_price(),
                        'stock_quantity' => $product->get_stock_quantity(),
                        'stock_status' => $product->get_stock_status(),
                        'manage_stock' => $product->get_manage_stock(),
                        'shipping_class' => $product->get_shipping_class(),
                        'permalink' => get_permalink($product->get_id())
                    ];

            
                    // Add product attributes as a list
                    $attributes = [];
                    foreach ($product->get_attributes() as $attribute) {
                        if ($attribute->is_taxonomy()) {
                            // Get taxonomy attribute values
                            $terms = wp_get_post_terms($product->get_id(), $attribute->get_name());
                            $attribute_values = [];
                            if (!is_wp_error($terms)) {
                                foreach ($terms as $term) {
                                    $attribute_values[] = $term->name;
                                }
                            }
                            // Get taxonomy attribute ID
                            $taxonomy = $attribute->get_name();
                            $tax_object = get_taxonomy($taxonomy);
                            $attribute_id = $tax_object ? wc_attribute_taxonomy_id_by_name($tax_object->name) : 0;
                        } else {
                            // Get custom attribute values
                            $attribute_values = $attribute->get_options();
                            $attribute_id = 0; // Custom attributes don't have IDs
                        }

                        $attributes[] = [
                            'id' => $attribute_id,
                            'name' => $attribute->get_name(),
                            'label' => wc_attribute_label($attribute->get_name()),
                            'options' => $attribute_values,
                            'visible' => $attribute->get_visible(),
                            'variation' => $attribute->get_variation()
                        ];
                    }
                    $product_data['attributes'] = $attributes;

                    // Get product images
                    $image_data = [];
                    $attachment_id = $product->get_image_id();
                    if ($attachment_id) {
                        $image_data[] = [
                            'id' => $attachment_id,
                            'src' => wp_get_attachment_url($attachment_id),
                            'name' => get_the_title($attachment_id),
                            'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
                            'position' => 0
                        ];
                    }
                    
                    $gallery_image_ids = $product->get_gallery_image_ids();
                    foreach ($gallery_image_ids as $position => $gallery_image_id) {
                        $image_data[] = [
                            'id' => $gallery_image_id,
                            'src' => wp_get_attachment_url($gallery_image_id),
                            'name' => get_the_title($gallery_image_id),
                            'alt' => get_post_meta($gallery_image_id, '_wp_attachment_image_alt', true),
                            'position' => $position + 1
                        ];
                    }
                    $product_data['images'] = $image_data;

                    // Get all meta data
                    $all_meta = $product->get_meta_data();
                    $meta_data = [];
                    foreach ($all_meta as $meta) {
                        $key = $meta->key;
                        if (strpos($key, '_') === 0) {
                            $key = ltrim($key, '_');
                        }
                        $meta_data[$key] = $meta->value;
                    }
                    $product_data['meta_data'] = $meta_data;

                    // Get specific meta keys we know we need and handle boolean/numeric conversions
                    $meta_keys = ['upc', 'distid', 'unit_price', 'product_class'];
                    foreach ($meta_keys as $key) {
                        $product_data[$key] = $meta_data[$key];
                    }
                    
                    // Handle automated_listing conversion
                    $product_data['drop_ship_flg'] = $meta_data['drop_ship_flg'] === '1' ? true : false;

                    // Handle automated_listing conversion
                    $product_data['automated_listing'] = $meta_data['automated_listing'] === 'True' ? true : false;
                    
                    // Handle ffl_req based on firearm_product
                    $product_data['ffl_req'] = $meta_data['firearm_product'] === 'yes' ? true : false;

                    // Handle map_price as numeric value
                    $product_data['map_price'] = isset($meta_data['map_price']) && is_numeric($meta_data['map_price']) ? 
                    $meta_data['map_price'] : 
                    null;

                    // Get taxonomies efficiently
                    $product_data['brands'] = self::get_taxonomy_terms($product->get_id(), 'product_brand');
                    $product_data['categories'] = self::get_taxonomy_terms($product->get_id(), 'product_cat');
                    $product_data['tags'] = self::get_taxonomy_terms($product->get_id(), 'product_tag');
                    
                    if (!$is_first) {
                        fwrite($fp, ',');
                    }
                    fwrite($fp, json_encode($product_data));
                    $is_first = false;
                    
                    $processed++;
                }
                
                wp_cache_flush();
                gc_collect_cycles();
            }
            
            fwrite($fp, '],"count":' . $processed . ',"plugin_version":"' . self::$version . '","export_time":"' . gmdate('Y-m-d\TH:i:s\Z') . '"}');
            
            fclose($fp);
            
            $json_content = file_get_contents($temp_file);
            $compressed_data = gzencode($json_content, 9);
            
            unlink($temp_file);
            
            header('Content-Type: application/gzip');
            header('Content-Encoding: gzip');
            header('Content-Disposition: attachment; filename="fflcockpit-products-export-' . date('Y-m-d') . '.json.gz"');
            header('Content-Length: ' . strlen($compressed_data));
            echo $compressed_data;
            exit;
            
        } catch (Exception $e) {
            if (isset($temp_file) && file_exists($temp_file)) {
                unlink($temp_file);
            }
            
            if (!headers_sent()) {
                return new WP_REST_Response([
                    'error' => 'Export failed',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }
            exit;
        }
    }

    private static function get_taxonomy_terms($product_id, $taxonomy) {
        $terms = get_the_terms($product_id, $taxonomy);
        if (!$terms || is_wp_error($terms)) return [];
        
        return array_map(function($term) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug
            ];
        }, $terms);
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
