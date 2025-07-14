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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                                    if (data.Error != null && data.Error != undefined){
                                        message += "\nError: " + data.Error;
                                    }
                                    alert(message);
                                }else{
                                    alert("Order has been sent, refresh the page to reload fulfillment status.");
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

            function hideOrder(distid, distributor_order_id){
                if (window.confirm("Are you sure you want to hide this order, disassociating it from this WooCommerce Order? It will be erased from your view (but still in the database). It will be ignored from Cockpit from now until the end of time. This action WILL NOT CANCEL the order with the distributor.")){
                    if (window.confirm("Are you REALLY SURE?? We will not take requests to unhide an order because you did it by mistake..., and again.. this WILl NOT CANCEL the order with your distributor.")){
                        try{
                            fetch("https://ffl-api.garidium.com", {
                                method: "POST",
                                headers: {
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "x-api-key": "',esc_attr($aKey),'",
                                },
                                body: JSON.stringify({"action": "hide_distributor_order", "data": {"api_key": "',esc_attr($aKey),'", "distid": distid, "order_id": ',esc_attr($orderId),', "distributor_order_id": distributor_order_id}})
                            })
                            .then(response=>response.json())
                            .then(data=>{  
                                if (!data.success){
                                    alert("Failed to Hide the Order, contact support@garidium.com so we can address the problem ASAP.");
                                }  
                                load_order_grid(data.fulfillment_orders); 
                            });
                        } catch (error) {
                            console.error(error);
                        }
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

            function load_fulfillment_options_grid(data) {
                if (data.order == null || data.order == "undefined") {
                    // Clear the fulfillment and distributor order sections
                    document.getElementById("product_fulfillment_table").innerHTML = "";
                    
                    // Show a styled error message in the fulfillment section
                    document.getElementById("product_fulfillment_table").innerHTML = `
                        <div style="padding: 30px; margin: 20px 0; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; font-size: 1.1em; text-align: center;">
                            <strong>There was a problem retrieving Order Data.</strong><br>
                            Please refresh and try again.<br>
                            Contact <a href="mailto:support@garidium.com">support@garidium.com</a> if this error persists.
                        </div>
                    `;
                    return;
                }
                var fulfillment_options = data.fulfillment_options;
                var order_details = JSON.parse(data.order);
                var distributor_orders = data.fulfillment_orders;
                let table = document.createElement("table");
                table.style.cssText = "width:100%;border-collapse: collapse;";

                // Adding the entire table to the body tag
                document.getElementById("product_fulfillment_table").appendChild(table);

                let columns = ["Dist", "Name", "SKU", "UPC", "Qty Avail", "Cost", "Ship", "Total", "Qty", "Order"];
                let fields = ["distid", "name", "distsku", "upc", "qty_on_hand", "unit_price", "shipping_cost", "total_cost"];

                // Parse fulfillment options if needed
                if (fulfillment_options != null && fulfillment_options != "undefined") {
                    fulfillment_options = JSON.parse(fulfillment_options);
                }

                // Parse distributor orders if needed
                if (distributor_orders != null && distributor_orders != "undefined") {
                    distributor_orders = JSON.parse(distributor_orders);
                }

                // Iterate over each line_item in order_details
                for (let item of order_details.line_items) {
                    let quantity = item.quantity;
                    let line_item_sku = item.order_line_item_sku;

                    let matchingOptions = []
                    // Create a new tbody for the new UPC group
                    let tbody = document.createElement("tbody");
                    table.appendChild(tbody);
                    let fulfilledQty = 0;
                    let fulfillmentStatus = "No";
                    let currentUPC = "Not Found";
                    let statusColor = "#ffffff"; 
                    let item_fulfilled = false;
                    let item_has_fulfillment_options = false;

                    if (fulfillment_options != null && fulfillment_options != "undefined") {
                        // Find the first fulfillment option that matches the SKU to get the UPC
                        let matchingOption = fulfillment_options.find(option => option.order_line_item_sku === item.sku);
                        currentUPC = matchingOption ? matchingOption.upc : null;

                        // Check if there are any matching fulfillment options for this UPC
                        matchingOptions = fulfillment_options.filter(option => option.upc === currentUPC);

                        // Sort matching options by total_cost in ascending order
                        matchingOptions.sort((a, b) => a.total_cost - b.total_cost);

                        if (matchingOptions.length > 0) {
                            item_has_fulfillment_options = true;
                        }

                        // Check if the UPC is fulfilled
                        var needs_validation = false;
                        for (let order of distributor_orders) {
                            let distOrderItems = JSON.parse(order.dist_order_items);
                            for (let distItem of distOrderItems) {
                                if (distItem.upc === currentUPC) {
                                    if (order.order_status.includes("Pending") || order.order_status.includes("Not Fulfilled") || order.order_status.includes("Partially Fulfilled")){
                                        needs_validation = true;
                                    }else{
                                        fulfilledQty += distItem.qty;
                                        if (fulfilledQty == quantity){
                                            item_fulfilled = true;
                                        }
                                    }   
                                }
                            }
                        }

                        if (needs_validation && !item_fulfilled){
                            fulfillmentStatus = "Validate with Distributor";
                            statusColor = "yellow"; 
                        }else{
                            if (fulfilledQty == 0) {
                                fulfillmentStatus = "No";
                                statusColor = "red"; 
                            } else if (fulfilledQty >= quantity) {
                                fulfillmentStatus = "Yes";
                                statusColor = "#00ff00"; 
                            } else if (fulfilledQty > 0 && fulfilledQty < quantity) {
                                fulfillmentStatus = "Partially";
                                let statusColor = "orange"; 
                            }   
                        }
                    }
                    // Add a single product information header row with darker background
                    let product_info_row = document.createElement("tr");
                    let product_info_cell = document.createElement("td");
                    product_info_cell.colSpan = columns.length;
                    
                    if (item_has_fulfillment_options){
                        product_info_cell.innerHTML = `
                            <strong>Line Item SKU:</strong> ${item.sku==null?"Not Found":item.sku} &nbsp; | &nbsp;
                            <strong>UPC:</strong> ${currentUPC} &nbsp; | &nbsp;
                            <strong>Requested:</strong> ${quantity} &nbsp; | &nbsp;
                            <strong>Fulfilled:</strong> ${fulfilledQty} &nbsp; | &nbsp;
                            <strong>Line Item Fulfilled:</strong> <span style="color:${statusColor}">${fulfillmentStatus}</span>
                        `;
                    }else{
                        product_info_cell.innerHTML = `
                            <strong>Line Item SKU:</strong> ${item.sku==null?"Not Found":item.sku} &nbsp; | &nbsp;
                            <strong>UPC:</strong> ${currentUPC} &nbsp; | &nbsp;
                            <strong>Requested:</strong> Unknown | &nbsp;
                            <strong>Fulfilled:</strong> Unknown &nbsp; | &nbsp;
                            <strong>Line Item Fulfilled:</strong> <span style="color:yellow">Review existing Distributor Orders</span>
                        `;
                    }

                    product_info_cell.style.cssText = "padding: 5px; background-color: #4a4a4a; color: #ffffff; border: 1px solid #e5e7eb;";
                    product_info_row.appendChild(product_info_cell);
                    tbody.appendChild(product_info_row);

                    // Add the colored line underneath the product information
                    let status_line_row = document.createElement("tr");
                    let status_line_cell = document.createElement("td");
                    status_line_cell.colSpan = columns.length;
                    status_line_cell.style.cssText = `height: 3px; background-color: ${statusColor};`;
                    status_line_row.appendChild(status_line_cell);
                    tbody.appendChild(status_line_row);

                    // Create and append a new header row for the new UPC group
                    let header_row = document.createElement("tr");
                    for (var j = 0; j < columns.length; j++) {
                        let heading = document.createElement("th");
                        heading.innerHTML = columns[j];
                        heading.style.cssText = "background:#eeeeee;color:#6b7280;text-align:left;border: 1px solid #e5e7eb;";
                        header_row.append(heading);
                    }
                    tbody.appendChild(header_row);
                    
                    if (matchingOptions.length > 0) {
                        item_has_fulfillment_options = true;
                        // Iterate over the matching fulfillment options
                        for (var i = 0; i < matchingOptions.length; i++) {
                            let row = document.createElement("tr");
                            let row_key = matchingOptions[i].distid + "|" + matchingOptions[i].distsku + "|" + matchingOptions[i].upc + "|" + matchingOptions[i].ffl_req;
                            row.id = row_key;
                            for (var c = 0; c < fields.length; c++) {
                                let col = document.createElement("td");
                                if (fields[c] == "distid") {
                                    col.innerHTML = "<img width=\"40\" src=\"" + get_distributor_logo(matchingOptions[i][fields[c]]) + "\"/>";
                                    col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                                } else if (fields[c] == "unit_price" || fields[c] == "shipping_cost" || fields[c] == "total_cost") {
                                    col.innerHTML = "$" + matchingOptions[i][fields[c]].toFixed(2);
                                    col.style.cssText = "width:70px;text-align:left;border: 1px solid #e5e7eb;";
                                } else if (fields[c] == "qty_on_hand") {
                                    col.innerHTML = matchingOptions[i][fields[c]];
                                    col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                                } else {
                                    col.innerHTML = matchingOptions[i][fields[c]];
                                    col.style.cssText = "text-align:left;border: 1px solid #e5e7eb;";
                                }
                                row.appendChild(col);
                            }

                            // Add quantity box
                            let qty_col = document.createElement("td");
                            qty_col.innerHTML = "<input style=\"padding:5px;width:47px;height:25px;\" id=\"qty_" + row_key + "\" type=\"number\" min=\"1\" value=\"" + quantity + "\">";
                            qty_col.style.cssText = "width:50px;text-align:left;border: 1px solid #e5e7eb;";
                            row.appendChild(qty_col);

                            // Add selection box
                            let col = document.createElement("td");
                            if (matchingOptions[i].qty_on_hand > 0 && matchingOptions[i].cockpit_fulfillable) {
                                col.innerHTML = "<input id=\"check_" + row_key + "\" type=\"checkbox\" value=\"" + row_key + "\">";
                                col.addEventListener("click", function(e) {
                                    let row = document.getElementById(row_key);
                                    let checkbox = document.getElementById("check_" + row_key);
                                    if (checkbox.checked) {
                                        row.style.cssText = "background:#e8f8e6;";
                                        document.getElementById("qty_" + row_key).style.background = "#cddfca";
                                    } else {
                                        row.style.cssText = "";
                                        document.getElementById("qty_" + row_key).style.background = "#ffffff";
                                    }
                                    showHideOrderButton();
                                }, false);
                                col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                            } else {
                                col.innerHTML = "<span>--</span>";
                                col.style.cssText = "text-align:center;border: 1px solid #e5e7eb;";
                            }
                            row.appendChild(col);

                            tbody.appendChild(row);
                        }
                    } else {
                        // If no matching fulfillment options, show a "No Fulfillment Options Available" row
                        let row = document.createElement("tr");
                        let col = document.createElement("td");
                        col.colSpan = columns.length;
                        col.innerHTML = "No Fulfillment Options Available";
                        col.style.cssText = "font-style:italic;color:#bbbbbb;text-align:center;border: 1px solid #e5e7eb;";
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
                                    oi_innerHTML += "<table style=\'align:center;font-size:8pt;width:95%;margin:5px;border: 1px solid #e5e7eb;\'><tr style=\'background:#dddddd;\'><td>SKU</td><td>UPC</td><td>QTY</td></tr>";
                                    let order_items = JSON.parse(orders[i].dist_order_items);
                                    for (var oi = 0; oi < order_items.length; oi++) {
                                        oi_innerHTML += "<tr><td>" + order_items[oi].distsku + "</td><td>" + order_items[oi].upc + "</td><td>" + order_items[oi].qty +  "</td></tr>";
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
                                        col.innerHTML = "";
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
                        col.innerHTML += ` | <a style="cursor:pointer; float:right;" onclick="hideOrder(\'${orders[i].distid}\', \'${orders[i].distributor_order_id}\')">
                                            <i class="fas fa-eye-slash" style="color:gray;"></i>
                                        </a>`;     
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
                load_fulfillment_options_grid(data);
                load_order_grid(data.fulfillment_orders); 
                document.getElementById("fulfillment_options_overlay").style.display="none";
            });

        </script>'; 
}
