<?php
// no outside access
if (!defined('WPINC')) die('No access outside of wordpress.');

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
add_action('add_meta_boxes', 'g_ffl_checkout_fulfillment_options_box');
function g_ffl_checkout_fulfillment_options_box()
{
    $screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
    ? wc_get_page_screen_id( 'shop-order' )
    : 'shop_order';

    add_meta_box(
        'g_ffl_checkout_fulfillment_options_box',
        __('FFL Cockpit'),
        'g_ffl_checkout_fulfillment_options_html',
        $screen,
        'normal',
        'high'
    );
}

function g_ffl_checkout_fulfillment_options_html($post_or_order_object)
{
    $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
    $aKey = get_option('g_ffl_cockpit_key');
    $orderId = $order->get_id();
  
    echo '
        <div class="table-container" style="position: relative;">
            <div class="overlay" id="fulfillment_options_overlay" style="display:flex;position: absolute;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(255, 255, 255, 0.7);justify-content: center;align-items: center;">
                <div class="loader" style="border: 8px solid #f3f3f3;border-top: 8px solid #3498db;border-radius: 50%;width: 50px;height: 50px;animation: spin 1s linear infinite;"></div>
            </div>
            <table style="width:100%;">
                <tr>
                    <td align="right">
                        <div style="width:99%;padding:5px;" id="fulfillment_status"></div>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">
                        Fulfillment Options
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <div class="fulfillment_grid" id="product_fulfillment_table"></div>  
                    </td>
                </tr>
                <tr>
                    <td align="right">    
                        <table>  
                            <tr>
                                <td><button style="background-color:gray;color:white;display:none;float: right;" id="ship_to_store_order_button" class="button alt" onclick="createOrder(true);">Create Order (Ship-to-Store)</button></td>
                                <td><button style="display:none;float:right;" id="create_order_button" class="button alt" onclick="createOrder(false);">Create Order (Drop-Ship)</button></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td style="font-weight:bold;">
                        Distributor Orders
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <div class="order_grid" id="distributor_order_table"></div>  
                    </td>
                </tr>
            </table>
        </div>';
    echo '
        <script>
            function addFFlToHoldOrder(distid, distributor_order_id){
                if (window.confirm("Have you uploaded a copy of the FFL within the FFL Information section of this page? If so, hit Ok. Otherwise upload the FFL first, then try again.")){
                    try{
                        fetch("https://ffl-api.garidium.com", {
                            method: "POST",
                            headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "x-api-key": "',esc_attr($aKey),'",
                            },
                            body: JSON.stringify({"action": "add_ffl_to_hold_order", "data": {"api_key": "',esc_attr($aKey),'" , "order_id": ',esc_attr($orderId),', "distributor_order_id": distributor_order_id, "distid": distid}})
                        })
                        .then(response=>response.json())
                        .then(data=>{
                            if (!data.success["status"]){
                                alert("Failed to add FFL to hold-order, please make sure to upload the FFL documentation and try again. If this still fails, email the FFL documentation to your distributor sales rep and contact support@garidium.com so we can address the problem ASAP.");
                            }   
                            load_order_grid(data.fulfillment_orders); 
                        });
                    } catch (error) {
                        console.error(error);
                    }
                }
            }

            function createOrder(ship_to_store){
                document.getElementById("create_order_button").disabled = true;
                document.getElementById("ship_to_store_order_button").disabled = true;
                var checkedCbs = document.querySelectorAll(\'#product_fulfillment_table input[type="checkbox"]:checked\');
                var order_json = "[";
                var has_items = false;
                for (var i = 0; i < checkedCbs.length; i++){
                    if (i>0) order_json += ", ";
                    qty = document.getElementById(checkedCbs[i].id.replace("check","qty")).value;
                    var order_items = checkedCbs[i].value.split("|");
                    order_json += "{\"distid\": \"" + order_items[0] + "\", \"distsku\": \"" + order_items[1] + "\",  \"upc\": \"" + order_items[2] + "\",  \"ffl_req\": " + order_items[3] + ",  \"qty\": " + qty + "}";
                    has_items = true;
                }
                order_json += "]";
                order_json = JSON.parse(order_json);

                if (has_items){
                    if (window.confirm((ship_to_store?"Ship-To-Store":"Drop Ship") + " Order Assembled: Please Review the Order details and Click Ok to send it to the Distributor:\n\n" + JSON.stringify(order_json))){
                        if (ship_to_store){
                            document.getElementById("ship_to_store_order_button").innerHTML = "Sending Order, Please wait...";
                        }else{
                            document.getElementById("create_order_button").innerHTML = "Sending Order, Please wait...";
                        }
                        try{
                            fetch("https://ffl-api.garidium.com", {
                                method: "POST",
                                headers: {
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "x-api-key": "',esc_attr($aKey),'",
                                },
                                body: JSON.stringify({"action": "place_distributor_order", "data": {"api_key": "',esc_attr($aKey),'" , "order_source": "woocommerce", "order_id": ',esc_attr($orderId),', "items": order_json, "ship_to_store": ship_to_store}})
                            })
                            .then(response=>response.json())
                            .then(data=>{  
                                load_order_grid(data.fulfillment_orders); 
                                if (!data.status){
                                    var message = "Order Creation Failed";
                                    message += "\nError: " + data.error;
                                    alert(message);
                                }
                                document.getElementById("create_order_button").disabled = false;
                                document.getElementById("ship_to_store_order_button").disabled = false;
                                if (ship_to_store){
                                    document.getElementById("ship_to_store_order_button").innerHTML = "Create Order (Ship-To-Store)";
                                }else{
                                    document.getElementById("create_order_button").innerHTML = "Create Order (Drop-Ship)";
                                }
                            });
                        } catch (error) {
                            console.error(error);
                        }
                    }else{
                        document.getElementById("create_order_button").disabled = false;
                        document.getElementById("ship_to_store_order_button").disabled = false;
                        return;
                    }
                }
            }

            function cancelOrder(distid, distributor_order_id){
                if (window.confirm("Are you sure you want to cancel order: " + distributor_order_id)){
                    try{
                        fetch("https://ffl-api.garidium.com", {
                            method: "POST",
                            headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "x-api-key": "',esc_attr($aKey),'",
                            },
                            body: JSON.stringify({"action": "cancel_distributor_order", "data": {"api_key": "',esc_attr($aKey),'", "distid": distid, "order_id": ',esc_attr($orderId),', "distributor_order_id": distributor_order_id}})
                        })
                        .then(response=>response.json())
                        .then(data=>{  
                            if (!data.success){
                                alert("Failed to Cancel the Order, please try again, and if it continues to fail email the cancellation request to your distributor sales rep and contact support@garidium.com so we can address the problem ASAP.");
                            }  
                            load_order_grid(data.fulfillment_orders); 
                        });
                    } catch (error) {
                        console.error(error);
                    }
                }
            }

            function showHideOrderButton(){
                var checkedCbs = document.querySelectorAll(\'#product_fulfillment_table input[type="checkbox"]:checked\');
                var ids = [];
                var hasChecks = false;
                for (var i = 0; i < checkedCbs.length; i++) 
                    hasChecks = true;
                if (hasChecks){
                    document.getElementById("create_order_button").style.display="";
                    document.getElementById("ship_to_store_order_button").style.display="";
                }else{
                    document.getElementById("create_order_button").style.display="none";
                    document.getElementById("ship_to_store_order_button").style.display="none";
                }
            }

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
                }else if (code == "TSW"){
                    return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_sports_south.png";
                }else {
                    return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_" + code + ".png";
                }
                return "";
            }

            function load_fulfillment_options_grid(fulfillment_options){
                let table = document.createElement("table");
                let thead = document.createElement("thead");
                let tbody = document.createElement("tbody");
                table.style.cssText = "width:100%;border-collapse: collapse;";

                table.appendChild(thead);
                table.appendChild(tbody);

                // Adding the entire table to the body tag
                document.getElementById("product_fulfillment_table").appendChild(table);
                let columns = ["Dist", "Name", "SKU", "UPC", "Qty Avail", "Cost", "Ship", "Total", "Qty", "Order"];
                let fields = ["distid", "name", "distsku", "upc", "qty_on_hand", "unit_price", "shipping_cost", "total_cost"];
                let header_row = document.createElement("tr");     
                for (var i = 0; i < columns.length; i++) {
                    heading = document.createElement("th");
                    heading.innerHTML = columns[i];
                    heading.style.cssText = "background:#eeeeee;color:#6b7280;text-align:left;border: 1px solid #e5e7eb;";
                    header_row.append(heading);
                }
                thead.appendChild(header_row);

                // now go through the fulfillment options
                if (fulfillment_options != null && fulfillment_options != "undefined"){
                    fulfillment_options = JSON.parse(fulfillment_options);
                }
                if (fulfillment_options == null || fulfillment_options.length == 0){
                    let row = document.createElement("tr");
                    let col = document.createElement("td");
                    col.colSpan = columns.length;
                    col.innerHTML = "No Fulfillment Options Available";
                    col.style.cssText = "font-style:italic;color:#bbbbbb;text-align:center;";
                    row.appendChild(col);
                    tbody.appendChild(row);
                }else{
                    for (var i = 0; i < fulfillment_options.length; i++) {
                        let row = document.createElement("tr");
                        let row_key = fulfillment_options[i].distid + "|" + fulfillment_options[i].distsku + "|" + fulfillment_options[i].upc + "|" + fulfillment_options[i].ffl_req;
                        row.id = row_key
                        for (var c = 0; c < fields.length; c++) {
                            let col = document.createElement("td");
                            if (fields[c] == "distid"){
                                col.innerHTML = "<img width=\"40\" src=\"" + get_distributor_logo(fulfillment_options[i][fields[c]]) + "\"/>";
                                col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                            }else if (fields[c] == "unit_price" || fields[c] == "shipping_cost" || fields[c] == "total_cost"){
                                col.innerHTML = "$" + fulfillment_options[i][fields[c]].toFixed(2);
                                col.style.cssText = "width:70px;text-align:left;border: 1px solid #e5e7eb;";
                            }else if (fields[c] == "qty_on_hand"){
                                col.innerHTML = fulfillment_options[i][fields[c]];
                                col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                            }else{
                                col.innerHTML = fulfillment_options[i][fields[c]];
                                col.style.cssText = "text-align:left;border: 1px solid #e5e7eb;";
                            }
                            row.appendChild(col);
                        }
                        // add quantity box
                        let qty_col = document.createElement("td");
                        qty_col.innerHTML = "<input style=\"padding:5px;width:47px;height:25px;\" id=\"qty_" + row_key + "\" type=\"number\" min=\"1\" value=\"1\">";
                        qty_col.style.cssText = "width:50px;text-align:left;border: 1px solid #e5e7eb;";
                        row.appendChild(qty_col);

                        // add selection box
                        let col = document.createElement("td");
                        if (fulfillment_options[i].qty_on_hand > 0 && fulfillment_options[i].cockpit_fulfillable){
                            col.innerHTML = "<input id=\"check_" + row_key + "\" type=\"checkbox\" value=\"" + row_key + "\">";
                            if (fulfillment_options[i].ffl_req){
                                col.innerHTML += "<br><span style=\"font-size:9pt;font-style:italic;color:gray;\">FFL Req</span>";
                            }
                            col.addEventListener("click", function(e) {
                                let row = document.getElementById(row_key);
                                let checkbox = document.getElementById("check_" + row_key);
                                if (checkbox.checked){
                                    row.style.cssText = "background:#e8f8e6;";
                                    document.getElementById("qty_" + row_key).style.background = "#cddfca";
                                }else{
                                    row.style.cssText = "";
                                    document.getElementById("qty_" + row_key).style.background = "#ffffff";
                                }
                                showHideOrderButton();
                            }, false);
                            col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                        }else{
                            col.innerHTML = "<span>--</span>";
                            col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                        }    
                        row.appendChild(col);
                        tbody.appendChild(row);
                    }
                }
            }

            function load_order_grid(orders){
                let table = document.createElement("table");
                let thead = document.createElement("thead");
                let tbody = document.createElement("tbody");
                table.style.cssText = "width:100%;border-collapse: collapse;";

                table.appendChild(thead);
                table.appendChild(tbody);

                // Adding the entire table to the body tag
                document.getElementById("distributor_order_table").innerHTML = "";
                document.getElementById("distributor_order_table").appendChild(table);

                let columns = ["Dist", "Order ID", "Order Details", "Ordered", "Status", "Shipped", "Ship Status", "Actions"];
                let fields = ["distid", "distributor_order_id", "dist_order_items", "order_date", "order_status", "ship_date", "ship_status"];
                let header_row = document.createElement("tr");     
                for (var i = 0; i < columns.length; i++) {
                    heading = document.createElement("th");
                    heading.innerHTML = columns[i];
                    heading.style.cssText = "background:#eeeeee;color:#6b7280;text-align:left;border: 1px solid #e5e7eb;";
                    header_row.append(heading);
                }
                thead.appendChild(header_row);
                
                if (orders != null && orders != "undefined"){
                    orders = JSON.parse(orders);
                }
                if (orders == null || orders == "undefined" || orders.length == 0){
                    let row = document.createElement("tr");
                    let col = document.createElement("td");
                    col.colSpan = columns.length;
                    col.innerHTML = "No Distributor Orders Created";
                    col.style.cssText = "font-style:italic;color:#bbbbbb;text-align:center;";
                    row.appendChild(col);
                    tbody.appendChild(row);
                }else{
                    for (var i = 0; i < orders.length; i++) {
                        let row = document.createElement("tr");
                        let row_key = orders[i].order_id + "|" + orders[i].distributor_order_id;
                        row.id = row_key
                        for (var c = 0; c < fields.length; c++) {
                            let col = document.createElement("td");
                            if (fields[c] == "distributor_order_id"){
                                col.innerHTML = orders[i].distributor_order_id + (orders[i].ship_to_store?"&nbsp;<img title=\"Shipped to Store\" width=\"20\" src=\"https://garidium.s3.amazonaws.com/images/store.png\"/>":"");
                                col.style.cssText = "text-align:left;vertical-align:middle;border: 1px solid #e5e7eb;";
                            }else if (fields[c] == "distid"){
                                col.innerHTML = "<img width=\"40\" src=\"" + get_distributor_logo(orders[i][fields[c]]) + "\"/>";
                                col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                            }else if (fields[c] == "dist_order_items"){
                                let oi_innerHTML = "<span style=\'font-size:8pt;\'>PO: " + orders[i].po_number + "</span>";
                                if (orders[i].dist_order_items != null){
                                    oi_innerHTML += "<table style=\'align:center;font-size:8pt;width:95%;margin:5px;border: 1px solid #e5e7eb;\'>";
                                    let order_items = JSON.parse(orders[i].dist_order_items);
                                    for (var oi = 0; oi < order_items.length; oi++) {
                                        oi_innerHTML += "<tr style=\'background:#dddddd;\'><td>SKU</td><td>UPC</td><td>QTY</td></tr><tr><td>" + order_items[oi].distsku + "</td><td>" + order_items[oi].upc + "</td><td>" + order_items[oi].qty +  "</td></tr>";
                                    }
                                    oi_innerHTML += "</table>";
                                }
                                col.innerHTML = oi_innerHTML;
                                col.style.cssText = "border: 1px solid #e5e7eb;";
                            }else if (fields[c] == "order_status"){
                                col.innerHTML = orders[i].order_status;
                                if (orders[i].distid == "ZND" && orders[i].order_status != null && orders[i].order_status == "FFL Hold"){
                                    col.innerHTML = "<span style=\"color:red;font-weight:bold;\">" + orders[i].order_status + "</span> | <a style=\"text-decoration:underline;cursor:pointer;\" onclick=\"addFFlToHoldOrder(\'" + orders[i].distid + "\', \'" + orders[i].distributor_order_id + "\');\">Update</a>"; 
                                }
                                col.style.cssText = "text-align:left;border: 1px solid #e5e7eb;";
                            }else if (fields[c] == "ship_date"){
                                col.innerHTML = "";
                                if (orders[i].ship_date != null){
                                    col.innerHTML = orders[i].ship_date.substring(0,10);
                                }
                                col.style.cssText = "text-align:left;border: 1px solid #e5e7eb;";
                            }else if (fields[c] == "ship_status"){
                                col.innerHTML = "";
                                if (orders[i].ship_status!=null){
                                    if (orders[i].ship_status == "delivered"){
                                        col.innerHTML = "<a target=_blank href=\"" + orders[i].ship_tracking_url + "\"><span style=\"color:green;\">Delivered</span></a>";
                                    }else if (orders[i].ship_status == "return_to_sender"){
                                        col.innerHTML = "<a target=_blank href=\"" + orders[i].ship_tracking_url + "\"><span style=\"color:red;\">Return to Sender</span></a>";
                                    }else{
                                        if (orders[i].ship_tracking_url!=null){
                                            col.innerHTML = "<a target=_blank href=\"" + orders[i].ship_tracking_url + "\">In-Transit</a>";
                                        }else{
                                            col.innerHTML = "In-Transit";
                                        }
                                    }
                                }else{
                                    if (orders[i].ship_tracking_url!=null){
                                        col.innerHTML = "<a target=_blank href=\"" + orders[i].ship_tracking_url + "\">In-Transit</a>";
                                    }else{
                                        col.innerHTML = "In-Transit";
                                    }
                                }
                                col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                            }else{
                                col.innerHTML = orders[i][fields[c]];
                                col.style.cssText = "text-align:left;border: 1px solid #e5e7eb;";
                            }
                            row.appendChild(col);
                        }

                        // add actions column
                        let col = document.createElement("td");
                        col.innerHTML = "<a target=_blank href=\"" + orders[i].order_url + "\">View</a>";
                        if (orders[i].ship_date == null && (orders[i].distid == "ZND")){
                            col.innerHTML += " | <a style=\"text-decoration:underline;cursor:pointer;\" onclick=\"cancelOrder(\'" + orders[i].distid + "\', \'" + orders[i].distributor_order_id + "\');\">Cancel</a>";
                        }
                        col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                        row.appendChild(col);
                        tbody.appendChild(row);
                    }    
                }
            }

            fetch("https://ffl-api.garidium.com", {
                method: "POST",
                headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "x-api-key": "',esc_attr($aKey),'",
                },
                body: JSON.stringify({"action": "get_fulfillment_summary", "data": {"api_key": "',esc_attr($aKey),'" , "order_id": "',esc_attr($orderId),'"}})
            })
            .then(response=>response.json())
            .then(data=>{  
                if ("status" in data){
                    document.getElementById("fulfillment_status").innerHTML = "Status: <span style=\"font-weight:bold;\">" + data.status + "</span>";
                }
                load_fulfillment_options_grid(data.fulfillment_options);
                load_order_grid(data.fulfillment_orders); 
                document.getElementById("fulfillment_options_overlay").style.display="none";
            });

        </script>'; 
}
