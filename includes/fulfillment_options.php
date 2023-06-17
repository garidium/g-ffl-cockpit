<?php
// no outside access
if (!defined('WPINC')) die('No access outside of wordpress.');

add_action('add_meta_boxes_shop_order', 'g_ffl_checkout_fulfillment_options_box');
function g_ffl_checkout_fulfillment_options_box()
{
    add_meta_box(
        'g_ffl_checkout_fulfillment_options_box',
        __('g-FFL Cockpit Fulfillment Options'),
        'g_ffl_checkout_fulfillment_options_html',
        'shop_order',
        'normal',
        'high'
    );
}

function g_ffl_checkout_fulfillment_options_html()
{
    global $post_id;
    $order = new WC_Order( $post_id );
    $aKey = get_option('g_ffl_cockpit_key');
    $orderId = $order->get_id();
    //$aKey = "kTyrtuwuav8HUkodH9QcI5MoE4sfAXJJ2EMVzTJM";
    //$orderId = "156142";

    echo '<div id="g-ffl-cockpit-fulfillment-options">
            <div id="product_fulfillment_table"></div>
          </div>';
    echo '<script>
            function get_distributor_logo(code){
                if (code == "ZND"){
                    return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_zanders.jpeg";
                }else if (code == "2AW"){
                    return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_2aw.png";
                }else if (code == "CSSI"){
                    return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_cssi.png";
                }else if (code == "DAV"){
                    return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_davidsons.jpeg";
                }else if (code == "LIP"){
                    return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_lipseys.jpeg";
                }else if (code == "RSR"){
                    return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_rsr.png";
                }
                return "";
            }
            var product_fulfillment_grid = new gridjs.Grid({
                columns: [
                    {name: "Dist", width: "60px",
                        formatter: (_, row) => gridjs.html(`<img align="center" width="50px" src="${get_distributor_logo(row.cells[0].data)}">`)
                    },
                    {name: "SKU"}, 
                    {name: "Name"}, 
                    {name: "UPC", width: "120px"},
                    {name: "Qty", width: "55px"},
                    {name: "Cost", width: "80px", formatter: (cell) => `$${cell.toFixed(2)}`}, 
                    {name: "Ship", width: "60px", formatter: (cell) => `$${cell.toFixed(2)}`}, 
                    {name: "Total", width: "80px", formatter: (cell) => `$${cell.toFixed(2)}`}, 
                ],
                resizable: true,
                fixedHeader: true,
                style: {
                    td: {
                        "padding": "3px"
                    },
                    th: {
                        "padding": "3px"
                    }
                },
                server: {
                    url: "https://ffl-api.garidium.com",
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                        "x-api-key": "',esc_attr($aKey),'",
                    },
                    body: JSON.stringify({"action": "get_order_fulfillment_status", "data": {"api_key": "',esc_attr($aKey),'" , "order_id": "',esc_attr($orderId),'"}}),
                    then: data => data.fulfillment_options.map(product => [//product.is_best_item,
                                                    product.distid, 
                                                    product.distsku,
                                                    product.name,
                                                    product.upc,
                                                    product.qty_on_hand, 
                                                    product.unit_price,  
                                                    product.shipping_cost,
                                                    product.total_cost])
                } 
            });
            product_fulfillment_grid.render(document.getElementById("product_fulfillment_table"));
        </script>'; 
}
