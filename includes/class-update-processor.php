<?php
class FFLCockpit_Update_Processor {

    /**
     * Processes a product based on the specified action.
     *
     * @param array  $data   The product data.
     * @param string $action The action to perform: 'insert', 'update', or 'delete'.
     * @return array The result of the processing.
     */
    public static function process_product($data, $action) {
        try {
            // Validate action
            if (!in_array($action, ['insert', 'update', 'delete'])) {
                throw new Exception('Invalid action specified.');
            }

            // Handle product deletion
            if ($action === 'delete') {
                if (empty($data['id'])) {
                    throw new Exception('Product ID is required for deletion.');
                }
            
                // Get associated image IDs BEFORE deleting the product
                $attachment_ids = [];
                try {
                    $thumbnail_id = get_post_thumbnail_id($data['id']);
                    if ($thumbnail_id) {
                        $attachment_ids[] = $thumbnail_id;
                    }

                    $gallery_ids = get_post_meta($data['id'], '_product_image_gallery', true);
                    if (!empty($gallery_ids)) {
                        $gallery_ids_array = explode(',', $gallery_ids);
                        $attachment_ids = array_merge($attachment_ids, $gallery_ids_array);
                    }
                } catch (Throwable $e) {
                    error_log("Image processing failed: " . $e->getMessage());
                }

                // Attempt to delete the product
                $deleted = wp_delete_post($data['id'], true);
            
                // Now remove all associated images
                if ($deleted) {
                    try {
                        foreach ($attachment_ids as $attachment_id) {
                            if (get_post_type($attachment_id) === 'attachment') {
                                wp_delete_attachment($attachment_id, true);
                            }
                        }
                    } catch (Throwable $e) {
                        error_log("Image processing failed: " . $e->getMessage());
                    }
                    return ['status' => 'deleted'];
                }
            
                return ['status' => 'Product not found or could not be deleted'];
            }

            // Handle product insertion or update
            $product = ($action === 'insert') ? new WC_Product_Simple() : wc_get_product($data['id']);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            // Set product properties
            if (isset($data['sku'])) $product->set_sku($data['sku']);
            if (isset($data['name'])) $product->set_name($data['name']);
            if (isset($data['description'])) $product->set_description($data['description']);
            if (isset($data['short_description'])) $product->set_short_description($data['short_description']);
            if (isset($data['regular_price'])) $product->set_regular_price($data['regular_price']);
            if (isset($data['sale_price'])) $product->set_sale_price($data['sale_price']);
            if (isset($data['stock_quantity'])) {
                $product->set_manage_stock(true);
                $product->set_stock_quantity($data['stock_quantity']);
            }

            // Handle shipping class
            if (!empty($data['shipping_class'])) {
                self::assign_shipping_class($product, $data['shipping_class']);
            }

            // Handle product attributes
            if (!empty($data['attributes']) && is_array($data['attributes'])) {
                self::assign_product_attributes($product, $data['attributes']);
            }

            if (isset($data['meta_data'])) {
                foreach ($data['meta_data'] as $meta) {
                    $product->update_meta_data($meta['key'], $meta['value']);
                }
            }
            
            // Handle images array (either JSON string or already-decoded array)
            if (!empty($data['images'])) {
                $images = is_string($data['images']) ? json_decode($data['images'], true) : $data['images'];

                if (json_last_error() === JSON_ERROR_NONE && is_array($images)) {
                    $image_ids = [];

                    foreach ($images as $index => $image_data) {
                        if (!isset($image_data['src'])) continue;

                        try {
                            $attachment_id = self::upload_image_from_url($image_data['src']);
                            if ($attachment_id && is_numeric($attachment_id)) {
                                if ($index === 0) {
                                    $product->set_image_id($attachment_id);
                                } else {
                                    $image_ids[] = $attachment_id;
                                }
                            }
                        } catch (Throwable $e) {
                            error_log("Image processing failed: " . $e->getMessage());
                            continue;
                        }
                    }

                    if (!empty($image_ids)) {
                        $product->set_gallery_image_ids($image_ids);
                    }
                } elseif (is_string($data['images'])) {
                    error_log("Invalid images JSON: " . $data['images']);
                }
            }
            
            // Save the product
            if (!empty($data['status'])) {
                $product->set_status($data['status']);
            } elseif ($action === 'insert') {
                $product->set_status('publish');
            }

            # Save the product
            $product_id = $product->save();

            # Now do the updates post-save
            if ($action === 'insert') {
                // Now update the slug, if provided
                if (isset($data['slug'])) {
                    wp_update_post([
                        'ID' => $product_id,
                        'post_name' => sanitize_title($data['slug']),
                    ]);
                }
            }

            // Now apply categorie if needed
            // Set or update product categories by ID
            if (!empty($data['categories']) && is_array($data['categories'])) {
                $category_ids = [];

                foreach ($data['categories'] as $cat) {
                    if (isset($cat['id']) && is_numeric($cat['id'])) {
                        $category_ids[] = (int) $cat['id'];
                    }
                }

                if (!empty($category_ids)) {
                    wp_set_object_terms($product_id, $category_ids, 'product_cat');
                }
            }

            // Set or update product tags by ID
            if (!empty($data['tags']) && is_array($data['tags'])) {
                $tag_ids = [];

                foreach ($data['tags'] as $tag) {
                    if (isset($tag['id']) && is_numeric($tag['id'])) {
                        $tag_ids[] = (int) $tag['id'];
                    }
                }

                if (!empty($tag_ids)) {
                    wp_set_object_terms($product_id, $tag_ids, 'product_tag');
                }
            }
            
            return ['status' => 'success', 'product_id' => $product_id];

        } catch (Exception $e) {
            error_log("Product processing failed: " . $e->getMessage() . ' | Data: ' . json_encode($data));
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Assigns a shipping class to a product.
     *
     * @param WC_Product $product        The product object.
     * @param string     $shipping_class The shipping class name.
     */
    private static function assign_shipping_class($product, $shipping_class) {
        $slug = sanitize_title($shipping_class);
        $term = get_term_by('slug', $slug, 'product_shipping_class');

        if (!$term || is_wp_error($term)) {
            $result = wp_insert_term($shipping_class, 'product_shipping_class', ['slug' => $slug]);
            if (!is_wp_error($result) && isset($result['term_id'])) {
                $term_id = $result['term_id'];
            } else {
                error_log("Failed to create shipping class: " . $shipping_class);
                return;
            }
        } else {
            $term_id = $term->term_id;
        }

        if (!empty($term_id)) {
            $product->set_shipping_class_id($term_id);
        }
    }

    /**
     * Assigns attributes to a product.
     *
     * @param WC_Product $product   The product object.
     * @param array      $attributes The attributes to assign.
     */
    private static function assign_product_attributes($product, $attributes) {
        $product_attributes = [];

        foreach ($attributes as $attr) {
            if (empty($attr['name']) || empty($attr['options'])) continue;

            $attribute = new WC_Product_Attribute();
            $attribute->set_name($attr['name']);
            $attribute->set_options($attr['options']);
            $attribute->set_visible(!empty($attr['visible']));
            $attribute->set_variation(!empty($attr['variation']));
            if (isset($attr['position'])) {
                $attribute->set_position((int)$attr['position']);
            }

            $product_attributes[] = $attribute;
        }

        $product->set_attributes($product_attributes);
    }

    private static function upload_image_from_url($url) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp = download_url($url);
        if (is_wp_error($tmp)) return false;

        $file_array = [
            'name'     => basename($url),
            'tmp_name' => $tmp
        ];

        $attachment_id = media_handle_sideload($file_array, 0);
        if (is_wp_error($attachment_id)) {
            @unlink($tmp);
            return false;
        }

        return $attachment_id;
    }
    /*
    //COMPRESSED IMAGE VERSION
    private static function upload_image_from_url($url) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    
        $tmp = download_url($url);
        if (is_wp_error($tmp)) return false;
    
        $file_array = [
            'name'     => basename($url),
            'tmp_name' => $tmp
        ];
    
        $attachment_id = media_handle_sideload($file_array, 0);
        if (is_wp_error($attachment_id)) {
            @unlink($tmp);
            return false;
        }
    
        // Recompress the image
        $image_path = get_attached_file($attachment_id);
        $image = wp_get_image_editor($image_path);
        if (!is_wp_error($image)) {
            $image->set_quality(75); // Adjust compression level
            $image->save($image_path); // Overwrites the original image with compressed version
        }
    
        return $attachment_id;
    }
    */
}
