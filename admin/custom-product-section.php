<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       garidium.com
 * @since      1.0.0
 *
 * @package    g_Ffl_Cockpit
 * @subpackage g_Ffl_Cockpit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    g_Ffl_Cockpit
 * @subpackage g_Ffl_Cockpit/admin
 * @author     Big G <sales@garidium.com>
 */


 // List of websites where the function should run
$warehouse_option_websites = array(
    'https://firearmsdirectclub.ffl-charlie.com',
    'https://firearmsdirectclub.com',
    'http://localhost:8000',
    // Add more URLs as needed
);

// Get the current website URL
$current_site_url = home_url();

if (in_array($current_site_url, $warehouse_option_websites)) {

    // Display radio buttons on the product page
    add_action('woocommerce_before_add_to_cart_button', 'add_custom_product_options');
    // Display radio buttons on the product page
    function add_custom_product_options() {
        global $product;

        // Get the product SKU and split it by the pipe symbol
        $sku = $product->get_sku();
        $distid = '';
        if (!empty($sku)) {
            $sku_parts = explode('|', $sku);
            if (!empty($sku_parts)) {
                $distid = $sku_parts[0]; // The first part of the SKU
            }
        }

        // Get the UPC attribute from the product attributes
        $attributes = $product->get_attributes();
        $upc = '';

        foreach ($attributes as $attribute) {
            if ($attribute->get_name() === 'pa_upc') { // Adjust the attribute name if needed
                $option_id = $attribute->get_options()[0]; // Assuming the UPC is the first option
                $term = get_term($option_id);

                if (!is_wp_error($term) && !empty($term)) {
                    $upc = (string)$term->name; // Get the name of the term and cast it to a string
                    break;
                }
            }
        }

        if (empty($upc)) {
            return; // Exit if no UPC attribute is found
        }

        // Prepare the payload for the API call
        //$api_key = "kTyrtuwuav8HUkodH9QcI5MoE4sfAXJJ2EMVzTJM";
        $api_key = get_option('g_ffl_cockpit_key');
        $payload = json_encode(array(
            'action' => 'get_warehouse_options',
            'data' => array(
                'upc' => $upc,
                'api_key' => $api_key
            )
        ));

        // Get the site's base URL for the Origin header
        $site_url = home_url();
        // Make the API call
        $response = wp_remote_post('https://ffl-api.garidium.com', array(
            'body' => $payload,
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key' => $api_key,
                'Origin' => $site_url
            )
        ));

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
    
        // Check for errors
        if (is_wp_error($response)) {
            echo 'Error retrieving warehouse options';
            return;
        }
        
        // Custom CSS added to woocommerce for FirearmsDirectClub
        /*
                .fr-single-product-alpha__cart form.cart:not(.variations_form), .fr-single-product-alpha__cart form.cart .woocommerce-variation-add-to-cart {
                    display: flex;
                    flex-direction: column;
                    gap: var(--product-gap);
                }

        */
        // Output the custom styles
        echo '<style>
                .custom-product-options table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .custom-product-options th, .custom-product-options td {
                    border: 1px solid #ddd;
                    padding: 8px;
                }
                .custom-product-options th {
                    background-color: #f2f2f2;
                    text-align: center;
                }
                .custom-product-options td {
                    text-align: left;
                }
                .custom-product-options td.center {
                    text-align: center;
                }
                .custom-product-options tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .custom-product-options tr:nth-child(odd) {
                    background-color: #ffffff;
                }
                .custom-product-options h4 {
                    margin-bottom: 10px;
                }
                .warehouse_shipping_costs {
                    font-size:9pt;
                    color:#9aabd4;
                }
            </style>';

        // Display the options in a table with custom styles
        echo '<div class="custom-product-options">';
        echo '<h4>Select a Warehouse Option:</h4>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Select</th>';
        echo '<th>Warehouse</th>';
        echo '<th>Stock</th>';
        echo '<th>Price</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        if (empty($data) || array_key_exists('Error', $data)) {
            if (array_key_exists('Error', $data)) {
                $errorMessage = 'No warehouse options available ' . $data['Error'];
            } else {
                $errorMessage = 'No warehouse options available';
            }
            echo '<tr><td colspan="4" class="center">' . htmlspecialchars($errorMessage) . '</td></tr>';
        } else {
            $default_selected = false;
            $row_index = 0;
            foreach ($data as $option) {
                $is_default = $option['distid'] === $distid;
                if ($is_default) {
                    $default_selected = true;
                }
                echo '<tr>';
                echo '<td class="center"><input type="radio" id="' . esc_attr($option['distid']) . '" name="custom_product_option" value="' . esc_attr($option['warehouse_id']) . '" data-sku="' . esc_attr($option['sku']) . '" data-price="' . esc_attr($option['price']) . '" data-shipping-class="' . esc_attr($option['shipping_class']) . '"' . ($is_default ? ' checked' : '') . '></td>';
                echo '<td>Warehouse ' . esc_html($option['warehouse_id']) . '</td>';
                echo '<td class="center">' . esc_html($option['qty']) . '</td>';
                echo '<td>$' . esc_html(number_format($option['price'], 2)) . '<br><span class="warehouse_shipping_costs">' . ($option['shipping_cost'] == 0 ? 'Free Shipping Available' : '+ $' . esc_html(number_format($option['shipping_cost'], 2)) . ' shipping') . '</span></td>';
                echo '</tr>';
                $row_index++;
            }
            // Select the first radio button if no match to the SKU was found
            if (!$default_selected && !empty($data)) {
                echo '<script>
                        document.getElementsByName("custom_product_option")[0].checked = true;
                    </script>';
            }
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        // Add hidden fields to capture the selected option's price and shipping class
        echo '<input type="hidden" id="custom_product_option_price" name="custom_product_option_price" value="">';
        echo '<input type="hidden" id="custom_product_option_shipping_class" name="custom_product_option_shipping_class" value="">';
        echo '<input type="hidden" id="custom_product_option_sku" name="custom_product_option_sku" value="">';

        // Add JavaScript to update hidden fields and show alert on form submit
        echo '<script>
            jQuery(document).ready(function($) {
                $("input[name=\'custom_product_option\']").change(function() {
                    var selectedOption = $(this).val();
                    var selectedPrice = $(this).data("price");
                    var selectedSku = $(this).data("sku");
                    var selectedShippingClass = $(this).data("shipping-class");
                    var selectShippingCosts = $(this).data("shipping-class");

                    $("#custom_product_option_price").val(selectedPrice);
                    $("#custom_product_option_sku").val(selectedSku);
                    $("#custom_product_option_shipping_class").val(selectedShippingClass);

                    //alert("Selected SKU: " + selectedSku + "\\nSelected Price: " + selectedPrice + "\\nSelected Shipping Class: " + selectedShippingClass);
                });

                // Trigger change event on page load to set initial values
                $("input[name=\'custom_product_option\']:checked").trigger("change");
            });
        </script>';
    }

    // Add selected option as metadata to the cart item
    function add_custom_option_to_cart_item( $cart_item_data, $product_id ) {
        if ( isset( $_POST['custom_product_option'] ) ) {
            $selected_option = sanitize_text_field($_POST['custom_product_option']);
            $price = isset($_POST['custom_product_option_price']) ? floatval($_POST['custom_product_option_price']) : 0;
            $shipping_class_name = isset($_POST['custom_product_option_shipping_class']) ? sanitize_text_field($_POST['custom_product_option_shipping_class']) : '';
            $sku = isset($_POST['custom_product_option_sku']) ? sanitize_text_field($_POST['custom_product_option_sku']) : '';

            // Get the shipping class ID by name
            $shipping_class = get_term_by('name', $shipping_class_name, 'product_shipping_class');

            if (!$shipping_class) {
                // Shipping class doesn't exist, create it
                $new_shipping_class = wp_insert_term($shipping_class_name, 'product_shipping_class');

                if (!is_wp_error($new_shipping_class) && isset($new_shipping_class['term_id'])) {
                    $shipping_class_id = $new_shipping_class['term_id'];
                } else {
                    // Handle the error if the term could not be created
                    $shipping_class_id = 0;
                }
            } else {
                $shipping_class_id = $shipping_class->term_id;
            }

            $cart_item_data['custom_product_option'] = $selected_option;
            $cart_item_data['custom_product_option_price'] = $price;
            $cart_item_data['custom_product_option_sku'] = $sku;
            $cart_item_data['custom_product_option_shipping_class'] = $shipping_class_id;
        }
        return $cart_item_data;
    }
    add_filter('woocommerce_add_cart_item_data', 'add_custom_option_to_cart_item', 10, 2);

    // Update cart item price and shipping class
    function update_cart_item_price_shipping_class($cart_item) {
        if (isset($cart_item['custom_product_option_sku']) && isset($cart_item['custom_product_option_price']) && isset($cart_item['custom_product_option_shipping_class'])) {
            $cart_item['data']->set_sku($cart_item['custom_product_option_sku']);
            $cart_item['data']->set_price($cart_item['custom_product_option_price']);
            $cart_item['data']->set_shipping_class_id($cart_item['custom_product_option_shipping_class']);
        }
        return $cart_item;
    }
    #add_filter('woocommerce_add_cart_item', 'update_cart_item_price_shipping_class');
    add_filter('woocommerce_get_cart_item_from_session', 'update_cart_item_price_shipping_class', 10, 2);

    // Display the custom option in the cart
    function display_custom_option_in_cart( $item_data, $cart_item ) {
        if ( isset( $cart_item['custom_product_option'] ) ) {
            $item_data[] = array(
                'key'   => __( 'Warehouse', 'custom-product-options' ),
                'value' => wc_clean( $cart_item['custom_product_option'] ),
            );
        }
        return $item_data;
    }
    add_filter('woocommerce_get_item_data', 'display_custom_option_in_cart', 10, 2);

    // Save custom option to order item meta
    // Save custom option to order item meta
    function save_custom_option_to_order_item_meta( $item, $cart_item_key, $values, $order ) {
        if ( isset( $values['custom_product_option'] ) ) {
            // Add the custom product options as meta data
            $item->add_meta_data( __( 'Warehouse', 'custom-product-options' ), $values['custom_product_option'], true);
            $item->add_meta_data( __( '_SKU', 'custom-product-options' ), $values['custom_product_option_sku'], true);
            $item->add_meta_data( __( '_Price', 'custom-product-options' ), $values['custom_product_option_price'], true);
           
            // Retrieve and add the shipping class name as meta data
            $shipping_class_id = $values['custom_product_option_shipping_class'];
            $shipping_class = get_term($shipping_class_id, 'product_shipping_class');
            
            if ( ! is_wp_error( $shipping_class ) && ! empty( $shipping_class ) ) {
                $shipping_class_name = $shipping_class->name;
                $item->add_meta_data( __( '_ShippingClass', 'custom-product-options' ), $shipping_class_name, true);
            }
        }
    }
    add_action('woocommerce_checkout_create_order_line_item', 'save_custom_option_to_order_item_meta', 10, 4);

   
}



// Hook to add custom section to product additional information tab
add_filter('woocommerce_product_tabs', 'custom_product_section_tab');

function custom_product_section_tab($tabs) {
    // Check if the current user is an admin
    if (current_user_can('administrator')) {
        // Add a new tab with the title 'Custom Section'
        $tabs['custom_section'] = array(
            'title'    => __('Admin', 'textdomain'),
            'priority' => 80,
            'callback' => 'custom_product_section_content',
        );
    }

    return $tabs;
}

// Callback function to display content of the custom section
function custom_product_section_content() {
    global $product;

    // Get the UPC attribute value
    $upc = $product->get_attribute('upc');
    $product_id = $product->get_id();
    $gFFLCockpitKey = get_option('g_ffl_cockpit_key');

    // Add a dropdown
    echo '<span style="color:red;font-style:italic;">The "Admin" section will only appear to logged in admin users of your site. Your customer will not see the "Admin" section.</span><br><b>Product Category Change:</b> This will update the product category on your site AND the product category this product is associated to for <u>All Cockpit Fed Sites</u>. So please make sure you are making changes that help everyone. <u>Thank you</u> for your contribution, Gary<br><br>';
    echo '<select style="width:250px !important;"id="ffl-cockpit-categories"></select>';
    echo '<button id="ffl-cockpit-recategorize-button">Apply Category Update</button>';
    echo '
    <script>
        jQuery(document).ready(function ($) {
            // Add your JavaScript/jQuery code here
            $("#ffl-cockpit-recategorize-button").on("click", function () {
                var category_id = document.getElementById("ffl-cockpit-categories").value;
                if (category_id == 0){
                    alert("You must select a valid category");
                    return;
                }
                
                document.getElementById("ffl-cockpit-recategorize-button").disabled = true;
                document.getElementById("ffl-cockpit-recategorize-button").innerText = "Updating Category...";
                var product_id = "'.esc_attr($product_id).'";
                var upc = "'.esc_attr($upc).'";

                fetch("https://ffl-api.garidium.com", {
                    method: "POST",
                    headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "x-api-key": "'.esc_attr($gFFLCockpitKey).'",
                    },
                    body: JSON.stringify({"action": "update_product_category", "data": {"product_id": product_id, "category_id": category_id , "upc": upc , "api_key": "'.esc_attr($gFFLCockpitKey).'"}})
                })
                .then(response=>response.json())
                .then(data=>{ 
                    document.getElementById("ffl-cockpit-recategorize-button").disabled = false;
                    document.getElementById("ffl-cockpit-recategorize-button").innerText = "Apply Category Update";
                
                    if (data.success){
                        location.reload();
                    }else{
                        alert("There was a problem in recategorization");
                    }
                });
                });

            fetch("https://ffl-api.garidium.com", {
                method: "POST",
                headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "x-api-key": "'.esc_attr($gFFLCockpitKey).'",
                },
                body: JSON.stringify({"action": "get_category_list", "data": {"api_key": "'.esc_attr($gFFLCockpitKey).'"}})
            })
            .then(response=>response.json())
            .then(data=>{ 
                // Get the dropdown element
                const dropdown = document.getElementById("ffl-cockpit-categories");
                if (data!=null && data.categories.length > 0){
                    // Loop through the data and create a category option for each item
                    const option = document.createElement("option");
                    option.value = 0;
                    option.text = "Select a category...";
                    dropdown.appendChild(option);
                
                    data.categories.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.text = item.name;
                        dropdown.appendChild(option);
                    });
                }
            });
        });
    </script>';

}
