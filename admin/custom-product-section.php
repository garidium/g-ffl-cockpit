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
