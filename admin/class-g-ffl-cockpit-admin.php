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
class g_ffl_Cockpit_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Ffl_Cockpit_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Ffl_Cockpit_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/g-ffl-cockpit-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('jsoneditor-style', plugin_dir_url(__FILE__) . 'css/jsoneditor.min.css', array(), $this->version, 'all');
        wp_enqueue_style('table-style', plugin_dir_url(__FILE__) . 'css/table.min.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in g_Ffl_Cockpit_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The g_Ffl_Cockpit_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/g-ffl-cockpit-admin.js', array('jquery'), $this->version, true);
        wp_enqueue_script('jsoneditor', plugin_dir_url(__FILE__) . 'js/jsoneditor.min.js');
        wp_enqueue_script('table_component', plugin_dir_url(__FILE__) . 'js/table.min.js');
        wp_enqueue_style('forms');

    }

    public function ffl_load_menu()
    {
        $custom_plugin_name = (get_option('g_ffl_cockpit_plugin_name') != ''? get_option('g_ffl_cockpit_plugin_name') : 'g-FFL Cockpit');
        add_menu_page('g-FFL Cockpit Settings Page', $custom_plugin_name, 'manage_options', 'g-ffl-cockpit-settings', array($this, 'g_ffl_cockpit_settings_page'), 'dashicons-database-import', 70);
        add_action('admin_init', array($this, 'register_g_ffl_cockpit_settings'));
    }

    function register_g_ffl_cockpit_settings()
    {
        //register our settings
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_key');
        //register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_configuration');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_plugin_name');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_plugin_logo_url');
    }

    function g_ffl_cockpit_settings_page()
    {
        $gFFLCockpitKey = get_option('g_ffl_cockpit_key');
        echo '<script type="text/javascript">
                let gFFLCockpitKey = "' . esc_attr($gFFLCockpitKey) . '"
              </script>';
        ?>

        <div class="wrap">
            <img src="<?php echo esc_attr(get_option('g_ffl_cockpit_plugin_logo_url') != '' ? get_option('g_ffl_cockpit_plugin_logo_url') : plugin_dir_url(__FILE__) . 'images/ffl-cockpit-logo.png');?>">
            <br><br>
            <!-- Tab links -->
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'configuration')" id="defaultOpen">Configuration</button>
                <button class="tablinks" onclick="openTab(event, 'product_feed');product_grid.render(document.getElementById('product_feed_table'));">Product Feed</button>
                <button class="tablinks" onclick="openTab(event, 'fulfillment');of_grid.render(document.getElementById('order_fulfillment_table'));">Fulfillment</button>
                <button class="tablinks" onclick="openTab(event, 'logs');log_grid.render(document.getElementById('log_table'));">Logs</button>
                <button class="tablinks" onclick="openTab(event, 'help_center');load_help_videos();">Help Center</button>
            </div>
            <!-- Tab content -->
            <div id="configuration" class="tabcontent">
            <!--<h3>Configuration</h3>-->
                <div class="postbox" style="padding: 10px;margin-top: 10px">
                        <?php settings_fields('g-ffl-cockpit-settings'); ?>
                        <table class="form-table">
                        <tr>
                                <td style="vertical-align:top;width:150px;font-weight:bold;" scope="row">g-FFL Cockpit Key:</td>
                                <td style="padding:5px;vertical-align:top;">
                                    <div class="user-pass-wrap">
                                        <div class="wp-pwd">
                                            <form method="post" action="options.php">
                                                <?php settings_fields('g-ffl-cockpit-settings'); ?>
                                                <input oninput="document.getElementById('set_key_form').style.display='';" type="password" style="width: 350px;" name="g_ffl_cockpit_key" id="g_ffl_cockpit_key" 
                                                    aria-describedby="login_error" class="input password-input" size="20"
                                                    value="<?php echo esc_attr($gFFLCockpitKey); ?>"/>
                                                    <a class="button alt" onclick="get_and_set_cockpit_configuration(document.getElementById('g_ffl_cockpit_key').value, false);">Load Config</a>
                                                    <span id="set_key_form" style="display:none;"><?php submit_button('Set Key', 'primary', 'submit-button'); ?></span>
                                            </form>
                                            
                                            </div>
                                    </div>
                                </td>
                                <td>
                                    <div id="g-ffl-admin-buttons" align="right" style="margin:5px;display:none;">
                                        <b>Admin Functions:&nbsp;</b>
                                        <a class="button alt" onclick="get_and_set_cockpit_configuration(document.getElementById('g_ffl_cockpit_key').value, true);document.getElementById('admin_current_editing_key').innerHTML = 'Editing: ' + document.getElementById('g_ffl_cockpit_key').value;document.getElementById('admin_current_editing_key').style.display='';document.getElementById('save_cockpit_configuration_button').style.display='none';">Load Config</a>
                                        <a class="button alt" onclick="setConfig(document.getElementById('g_ffl_cockpit_key').value);">Save</a>
                                        <br><br><span style="padding:10px;color:red;display:none;" id="admin_current_editing_key"></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3>If you have any questions about FFL Cockpit configuration, send an email to support@garidium.com detailing your questions. We'll get back as soon as we can. Include your name and your website URL so we can look up your configuration. Also, make to review our <a target=_blank href="https://garidium.com/category/help-center/">Help Center</a> for tips on using Cockpit.</td>
                            </tr>

                            <tr valign="top">
                                <td colspan=3>
                                <div id="jsoneditor" style="width: 100%; height: 500px;"></div>
                                <input type="hidden" name="g_ffl_cockpit_configuration" id="g_ffl_cockpit_configuration">
                                <script>
                                    function get_and_set_cockpit_configuration(api_key, isAdminRequest){
                                 
                                        try {
                                            if (api_key != null && api_key.length > 0){
                                                var admin_key = api_key;
                                                if (isAdminRequest){
                                                    admin_key = "<?php echo esc_attr($gFFLCockpitKey);?>";
                                                }
                                                fetch("https://ffl-api.garidium.com", {
                                                    method: "POST",
                                                    headers: {
                                                    "Accept": "application/json",
                                                    "Content-Type": "application/json",
                                                    "x-api-key": admin_key,
                                                    },
                                                    body: JSON.stringify({"action": "get_subscription", "data": {"api_key": api_key}})
                                                })
                                                .then(response=>response.json())
                                                .then(data=>{
                                                    try{
                                                        cockpit_configuration = JSON.parse(data[0].cockpit_configuration);
                                                        editor.set(cockpit_configuration);
                                                    } catch (error) {
                                                        alert(error);
                                                        alert("No configuration found for this key, setting to default.");
                                                    }
                                                });
                                            }else{
                                                alert("No API Key Configured");
                                            }
                                        }catch(error){
                                            alert("Please validate key, no configuration was found");
                                            console.log(error);
                                        }
                                    }
                                </script>
                                <script>
                                    var editor = new JSONEditor(document.getElementById("jsoneditor"));
                                    editor.set({"Loading Configuration": "Please wait..."});
                                    window.onload = function(){
                                        fetch("https://ffl-api.garidium.com", {
                                            method: "POST",
                                            headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey);?>",
                                            },
                                            body: JSON.stringify({"action": "get_configuration_schema", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey);?>"}})
                                        })
                                        .then(response=>response.json())
                                        .then(data=>{ 
                                            // Get the dropdown element
                                            if (data!=null){
                                                // now that we have the schema, lets build the grid
                                                build_grid(data.schema);
                                             }
                                        });
                                    }
                            
                                    function build_grid(config_schema){
                                        var options = {
                                            modes: ['text','tree'],
                                            mode: 'tree',
                                            ace: ace,
                                            schema: config_schema
                                        }
                                        editor.destroy();
                                        editor = new JSONEditor(document.getElementById("jsoneditor"), options);
                                        editor.set({"Loading Configuration": "Please wait..."});
                                        get_and_set_cockpit_configuration("<?php echo esc_attr($gFFLCockpitKey);?>", false);
                                        if (window.location.host == 'garidium.com' || window.location.host == 'localhost:8000'){
                                            document.getElementById('g-ffl-admin-buttons').style.display = '';
                                        }
                                     }
                                </script>
                                </td>
                            </tr>
                            <tr valign="top" id="white_label_settings_name" style="display:none;">
                                <th scope="row">Plugin Name:</th>
                                <td>
                                    <input type="text" style="width: 30%" name="g_ffl_cockpit_plugin_name" maxlength=20
                                            value="<?php echo esc_attr(get_option('g_ffl_cockpit_plugin_name') != '' ? get_option('g_ffl_cockpit_plugin_name') : 'g-FFL Cockpit'); ?>"/>
                                </td>
                            </tr>
                            <tr valign="top" id="white_label_settings_url" style="display:none;">
                                <th scope="row">Plugin Logo URL:</th>
                                <td>
                                    <input type="text" style="width: 500px;" name="g_ffl_cockpit_plugin_logo_url"
                                            value="<?php echo esc_attr(get_option('g_ffl_cockpit_plugin_logo_url') != '' ? get_option('g_ffl_cockpit_plugin_logo_url') : plugin_dir_url(__FILE__) . 'images/ffl-cockpit-logo.png');?>"/>
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <div>
                                        <button class="button alt" id="save_cockpit_configuration_button">Save Changes</button>
                                        <script type="text/javascript">
                                            document.getElementById("save_cockpit_configuration_button").addEventListener("click", function(){
                                                document.getElementById("save_cockpit_configuration_button").disabled = true;
                                                document.getElementById('save_cockpit_configuration_button').innerText = 'Please Wait...';
                                                event.preventDefault();
                                                setConfig(gFFLCockpitKey);
                                                document.getElementById("save_cockpit_configuration_button").disabled = false;
                                                document.getElementById('save_cockpit_configuration_button').innerText = 'Save Changes';
                                            });
                                        </script>
                                    </div>
                                </td>
                                <td align="right">
                                    <div>
                                        <button class="button alt" id="send_test_fulfillment_emails_button">Send Test Fulfillment Emails</button>
                                        <script>
                                            document.getElementById("send_test_fulfillment_emails_button").addEventListener("click", function(){
                                                document.getElementById("send_test_fulfillment_emails_button").disabled = true;
                                                document.getElementById('send_test_fulfillment_emails_button').innerText = 'Please Wait...';
                                                    fetch("https://ffl-api.garidium.com", {
                                                            method: "POST",
                                                            headers: {
                                                            "Accept": "application/json",
                                                            "Content-Type": "application/json",
                                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                                            },
                                                            body: JSON.stringify({"action": "send_test_emails", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>"}})
                                                        })
                                                        .then(response=>response.json())
                                                        .then(data=>{ 
                                                            document.getElementById("send_test_fulfillment_emails_button").disabled = false; 
                                                            document.getElementById('send_test_fulfillment_emails_button').innerText = 'Send Test Fulfillment Emails';     
                                                        });
                                                });
                                        </script>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table style="border: solid black 1px;">
                            <tr style="background-color:#EEEEEE;weight:bold;font-style:italic;"><td colspan=2>Product Class Code Reference</td></tr>
                            <tr><td><b>AC</b></td><td>Accessories</td></tr>
                            <tr><td><b>AG</b></td><td>Air Guns & Accessories</td></tr>
                            <tr><td><b>AO</b></td><td>Ammunition</td></tr>
                            <tr><td><b>AP</b></td><td>Apparel</td></tr>
                            <tr><td><b>AR</b></td><td>Archery</td></tr>
                            <tr><td><b>BP</b></td><td>Black Powder Firearms</td></tr>
                            <tr><td><b>BS</b></td><td>Binoculars & Spotting</td></tr>
                            <tr><td><b>FA</b></td><td>Firearms</td></tr>
                            <tr><td><b>FI</b></td><td>Fishing</td></tr>
                            <tr><td><b>FP</b></td><td>Firearms Parts</td></tr>
                            <tr><td><b>HS</b></td><td>Holsters</td></tr>
                            <tr><td><b>HT</b></td><td>Hunting</td></tr>
                            <tr><td><b>HZ</b></td><td>Hazardous</td></tr>
                            <tr><td><b>KN</b></td><td>Knives</td></tr>
                            <tr><td><b>LL</b></td><td>Lights & Lasers</td></tr>
                            <tr><td><b>MG</b></td><td>Magazines</td></tr>
                            <tr><td><b>MZ</b></td><td>Muzzleloading</td></tr>
                            <tr><td><b>OP</b></td><td>Optics</td></tr>
                            <tr><td><b>OT</b></td><td>Other</td></tr>
                            <tr><td><b>RC</b></td><td>Range Bags & Cases</td></tr>
                            <tr><td><b>RL</b></td><td>Reloading</td></tr>
                            <tr><td><b>SF</b></td><td>Safes</td></tr>
                            <tr><td><b>SO</b></td><td>SOT</td></tr>
                        </table>
                        <a style="cursor:pointer;" onclick="document.getElementById('white_label_settings_name').style.display='';document.getElementById('white_label_settings_url').style.display='';">&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;</a>
                </div>
            </div>
            <div id="product_feed" class="tabcontent">
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <!-- <p>The Product Feed is based on your Configuration. The synchronization process will run every 15-minutes, at which point any changes you make to your configuration will be applied. This list will show items from all distributors configured, and with quantities less than your minimum listing quantity. We list one product per UPC, based on availability and price.</p> -->
                    <div id="product_feed_table"></div>
                    <div style="padding:5px;"><button id="download_inventory_button" class="button alt" data-marker-id="">Download Inventory</button></div>
                    <script>
                        const window_height = (window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight) * 0.8;
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
                            }else{
                                return "https://garidium.s3.amazonaws.com/ffl-api/plugin/images/distributor_logo_" + code + ".png";
                            }
                            return "";
                        }
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        var product_grid = new gridjs.Grid({
                            columns: [
                                //formatter: (_, row) => `${row.cells[0].data?row.cells[2].data + '*':row.cells[2].data}`
                                //{sort: false, name: "List", width: '50px', formatter: (cell) => `${cell?"Y":"N"}`}, 
                                {name: 'Dist', width: '60px',
                                    formatter: (_, row) => gridjs.html(`<img align="center" width="50px" src="${get_distributor_logo(row.cells[0].data)}">`)
                                },
                                {name: 'SKU'}, 
                                {sort: false, name: 'Product Image',
                                    formatter: (_, row) => gridjs.html(`<a style="cursor:pointer;" onclick="load_product_data('${row.cells[3].data.replace("\"","&quot;") + "','" + row.cells[0].data + "','" + row.cells[1].data + "','" + row.cells[2].data[0]['src']}')"><img style="max-height:40px;max-width:100px;height:auto;width:auto;" src="${row.cells[2].data[0]['src']}"></a>`)
                                },
                                {name: 'Name', width: '200px'}, 
                                {name: "UPC"},
                                {name: "MFG"},
                                {name: "MPN"},
                                {name: "Qty", width: '55px'},
                                {name: 'Cost', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                //{name: 'MAP', width: '80px', formatter: (cell) => `${(cell==null || cell == 0)?'':'$'+cell.toFixed(2)}`}, 
                                {name: 'Ship', width: '60px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                {name: 'Total', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                //{name: "MPN", width: '150px'},
                                //{name: "Category", width: '120px'},
                                //{name: 'Price', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`},   
                                //{name: 'DS', width: '50px'}
                            ],
                            sort: {
                                multiColumn: false,
                                server: {
                                    url: (prev, columns) => {
                                        if (!columns.length) return prev;
                                        const col = columns[0];
                                        const dir = col.direction === 1 ? 'asc' : 'desc';
                                        let colName = ['distid', 'distsku', 'distsku', 'name', 'upc', 'mfg_name', 'mpn', 'qty_on_hand', 'unit_price', 'shipping_cost', 'total_cost'][col.index];
                                        let sortUrl = `${prev}&order_column=${colName}&order_direction=${dir}`;
                                        return sortUrl;
                                    }
                                }
                            },
                            search: {
                                server: {
                                    url: (prev, keyword) => `${prev}&search=${keyword}`
                                }
                            },
                            resizable: true,
                            pagination: {
                                limit: 100,
                                server: {
                                    url: (prev, page, limit) => `${prev}&limit=${limit}&offset=${page * limit}`
                                }
                            },
                            fixedHeader: true,
                            //height: window_height,
                            //width: '1500px',
                            style: {
                                td: {
                                    'padding': '3px'
                                },
                                th: {
                                    'padding': '3px'
                                }
                            },
                            server: {
                                url: 'https://ffl-api.garidium.com/product?action=get_filtered_catalog',
                                method: 'GET',
                                headers: {
                                    "Accept": "application/json",
                                    "Content-Type": "application/json",
                                    "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
			                    },
                                then: data => JSON.parse(data).products.map(product => [//product.is_best_item,
                                                                   product.distid, 
                                                                   product.distsku,
                                                                   JSON.parse(product.images), // # change to image
                                                                   product.name,
                                                                   product.upc, 
                                                                   product.mfg_name, //+ ' (' + product.mpn + ")",
                                                                   product.mpn,
                                                                   product.qty_on_hand, 
                                                                   product.unit_price,  
                                                                   product.shipping_cost,
                                                                   product.total_cost]),  
                                                                   //product.drop_ship_flg,
                                                                   //product.map_price, 
                                                                   //product.item_cat,                                          
                                                                   //product.price])                                                            
                                                                   //product.drop_ship_flg])
                                total: data => JSON.parse(data).count
                            } 
                        });
                    
                        document.getElementById("download_inventory_button").addEventListener("click", function(){
                            document.getElementById("download_inventory_button").disabled = true;
                            document.getElementById('download_inventory_button').innerText = 'Please Wait...';
                            fetch("https://ffl-api.garidium.com/download", {
                                method: "POST",
                                headers: {
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                },
                                body: JSON.stringify({"action": "download_inventory"})
                            })
                            .then(response=>response.json())
                            .then(data=>{ 
                                window.open(data);  
                                document.getElementById("download_inventory_button").disabled = false; 
                                document.getElementById('download_inventory_button').innerText = 'Download Inventory';     
                            });
                        });
                    </script>
                                    <!-- The Modal -->
                    <div id="myModal" class="cockpit-modal">
                        <!-- Modal content -->
                        <div class="cockpit-modal-content">
                            <span class="close">&times;</span>
                            <div align="center" id="product_detail_div"></div>
                        </div>
                        <script>
                            // Get the modal
                            var modal = document.getElementById("myModal");

                            // Get the button that opens the modal
                            var btn = document.getElementById("myBtn");

                            // Get the <span> element that closes the modal
                            var span = document.getElementsByClassName("close")[0];

                            function load_product_data(title, distributor, sku, img_url){
                                //alert(data);
                                modal.style.display = "block";
                                document.getElementById("product_detail_div").innerHTML = "<h3>" + title + "</h3><br><img width='75%' src='" + img_url + "'/><br><img width=75 src='" + get_distributor_logo(distributor) + "'/><br>" + sku;
                            }

                            // When the user clicks on <span> (x), close the modal
                            span.onclick = function() {
                                modal.style.display = "none";
                            }

                            // When the user clicks anywhere outside of the modal, close it
                            window.onclick = function(event) {
                                if (event.target == modal) {
                                    modal.style.display = "none";
                                }
                            }
                        </script>
                    </div>

                </div>
            </div>
            <div id="fulfillment" class="tabcontent">
                <h3>Fulfillment History</h3>
                <div class="postbox" style="padding: 10px;margin-top: 10px;">
                    <!-- <p>The Product Feed is based on your Configuration. The synchronization process will run every 15-minutes, at which point any changes you make to your configuration will be applied. This list will show items from all distributors configured, and with quantities less than your minimum listing quantity. We list one product per UPC, based on availability and price.</p> -->
                    <div id="order_fulfillment_table"></div>
                    <div style="padding:5px;display: flex;width: 100%;justify-content: space-between;align-items: flex-start;">
                        <div style="flex: 1;">
                            <button id="download_fulfillment_history_button" class="button alt" data-marker-id="">Download Fulfillment History</button>
                        </div>
                        <div id="gunbroker_processor_section" style="display:none;">
                            <table style="width:100%;" align="right">
                                <tr>
                                    <td align="right">
                                        Gunbroker Orders (Beta):
                                        <select id="gunbroker_order_id" class="input-input"></select>
                                        <button id="process_gunbroker_order_button" class="button alt" data-marker-id="">Fulfill</button>
                                        <button id="import_gunbroker_order_button" class="button alt" data-marker-id="">Import</button>
                                    </td>
                                </tr>
                            </table>
                         </div>
                    </div>
                    <script>
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        var of_grid = new gridjs.Grid({
                            columns: [
                                {name: 'Source', width: '90px',
                                    formatter: (_, row) => {
                                        if (row.cells[0].data == "gunbroker"){
                                            return gridjs.html(`Gunbroker`);
                                        }else if (row.cells[0].data == "woocommerce"){
                                            return gridjs.html(`Website`);
                                        }
                                    }
                                }, 
                                {name: 'Order', width: '90px',
                                    formatter: (_, row) => {
                                        if (row.cells[0].data == "gunbroker"){
                                            return gridjs.html(`<a target=_blank href="https://www.gunbroker.com/order?orderid=${row.cells[1].data}">${row.cells[1].data}</a>`);
                                        }else{
                                            return gridjs.html(`<a target=_blank href="/wp-admin/post.php?post=${row.cells[1].data}&action=edit">${row.cells[1].data}</a>`);
                                        }
                                    }
                                },
                                {name: 'Dist', width: '60px',
                                    formatter: (_, row) => gridjs.html(`<img align="center" width="50px" src="${get_distributor_logo(row.cells[2].data)}">`)
                                },
                                {name: 'Dist. Order', width: '85px',
                                    formatter: (_, row) => {
                                        if (row.cells[12].data == null){
                                            return gridjs.html(row.cells[3].data + (row.cells[13].data?"&nbsp;<img title=\"Shipped to Store\" width=\"20\" src=\"https://garidium.s3.amazonaws.com/images/store.png\"/>":""));
                                        }else{
                                            return gridjs.html(`<a target=_blank href="${row.cells[12].data}">${row.cells[3].data}</a>` + (row.cells[13].data?"&nbsp;<img title=\"Shipped to Store\" width=\"20\" src=\"https://garidium.s3.amazonaws.com/images/store.png\"/>":""));
                                        }
                                    }
                                },
                                {name: 'Status', width: '100px'}, 
                                {name: 'Customer Name'}, 
                                {name: 'Phone', width: '120px',
                                    formatter: (cell) => `${cell.replace(/\D/g, '').slice(-10).replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3')}`
                                },
                                {name: 'Email', width: '60px',
                                    formatter: (_, row) => gridjs.html(`<a href="mailto:${row.cells[7].data}">Email</a>`)
                                },
                                {name: 'Ordered', width: '125px',
                                    formatter: (cell) => `${new Date(new Date(cell.replace(" ", "T") + "Z").getTime() - (new Date(cell.replace(" ", "T") + "Z").getTimezoneOffset() * 60000)).toISOString().split('T')[0]}`
                                },
                                {name: 'Shipped', width: '100px',
                                    formatter: (cell) => `${cell!=null?new Date(new Date(cell.replace(" ", "T") + "Z").getTime() - (new Date(cell.replace(" ", "T") + "Z").getTimezoneOffset() * 60000)).toISOString().split('T')[0]:""}`
                                },
                                {name: "Ship Status",
                                    formatter: (_, row) => {
                                        if (row.cells[11].data == "delivered"){
                                            return gridjs.html(`<a target=_blank href="${row.cells[10].data}"><span style="color:green;">Delivered</span></a>`);
                                        }else if (row.cells[11].data == "return_to_sender"){
                                            return gridjs.html(`<a target=_blank href="${row.cells[10].data}"><span style="color:red;">Return to Sender</span></a>`);
                                        }else{
                                            if (row.cells[10].data != null) {
                                                return gridjs.html(`<a target=_blank href="${row.cells[10].data}">${row.cells[11].data==null?"In Transit":row.cells[11].data}</a>`);
                                            }else{
                                                return gridjs.html(row.cells[11].data);
                                            }
                                        }
                                    }
                                },
                                {name: "Order URL", hidden: true},
                                {name: "Tracking URL", hidden: true},
                                {name: "Ship-to-Store", hidden: true},
                                
                            ],
                            sort: {
                                multiColumn: false,
                                server: {
                                    url: (prev, columns) => {
                                        if (!columns.length) return prev;
                                        const col = columns[0];
                                        const dir = col.direction === 1 ? 'asc' : 'desc';
                                        let colName = ["order_source","order_id", "distid", "distributor_order_id", "order_status", "customer_name", "customer_phone", "customer_email", "order_date", "ship_date", "ship_status"][col.index];
                                        let sortUrl = `${prev}&order_column=${colName}&order_direction=${dir}`;
                                        return sortUrl;
                                    }
                                }
                            },
                            search: {
                                server: {
                                    url: (prev, keyword) => `${prev}&search=${keyword}`
                                }
                            },
                            resizable: true,
                            pagination: {
                                limit: 100,
                                server: {
                                    url: (prev, page, limit) => `${prev}&limit=${limit}&offset=${page * limit}`
                                }
                            },
                            fixedHeader: true,
                            style: {
                                td: {
                                    'padding': '3px'
                                },
                                th: {
                                    'padding': '3px'
                                }
                            },
                            server: {
                                url: 'https://ffl-api.garidium.com/product?action=get_order_history',
                                method: 'GET',
                                headers: {
                                    "Accept": "application/json",
                                    "Content-Type": "application/json",
                                    "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
			                    },                                
                                then: data => JSON.parse(data).orders.map(order => [
                                                                   order.order_source,
                                                                   order.order_id, 
                                                                   order.distid, 
                                                                   order.distributor_order_id,
                                                                   order.order_status,
                                                                   order.customer_name,
                                                                   order.customer_phone,
                                                                   order.customer_email,
                                                                   order.order_date,
                                                                   order.ship_date, 
                                                                   order.ship_tracking_url,
                                                                   order.ship_status,
                                                                   order.order_url,
                                                                   order.ship_to_store
                                                                ]),  
                                                                 
                                total: data => JSON.parse(data).count
                            } 
                        });
                  
                        document.getElementById("download_fulfillment_history_button").addEventListener("click", function(){
                            if (window.confirm("This action will attempt fulfillment of gunbroker order#" + document.getElementById("gunbroker_order_id").value + ", do you want to proceed?")){                 
                                document.getElementById("download_fulfillment_history_button").disabled = true;
                                document.getElementById('download_fulfillment_history_button').innerText = 'Please Wait...';
                                fetch("https://ffl-api.garidium.com/download", {
                                    method: "POST",
                                    headers: {
                                    "Accept": "application/json",
                                    "Content-Type": "application/json",
                                    "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                    },
                                    body: JSON.stringify({"action": "download_fulfillment_history"})
                                })
                                .then(response=>response.json())
                                .then(data=>{ 
                                    window.open(data);  
                                    document.getElementById("download_fulfillment_history_button").disabled = false; 
                                    document.getElementById('download_fulfillment_history_button').innerText = 'Download Fulfillment History';     
                                });
                            }
                        });
                        
                        fetch("https://ffl-api.garidium.com", {
                            method: "POST",
                            headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                            },
                            body: JSON.stringify({"action": "list_gunbroker_orders", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>"}})
                        })
                        .then(response=>response.json())
                        .then(data=>{ 
                            // Get the dropdown element
                            const dropdown = document.getElementById('gunbroker_order_id');
                            if (data.order_data && data.order_data.length > 0){
                                // Loop through the data and create an option for each item
                                data.order_data.forEach(item => {
                                    const option = document.createElement('option');
                                    option.value = item.orderID;
                                    timestamp = new Date(item.orderDateUTC).toLocaleString('en-US', {timeZone: 'UTC'});
                                    option.text = item.orderID + " - " + timestamp + " - " + item.billToName;
                                    dropdown.appendChild(option);
                                });

                                document.getElementById("gunbroker_processor_section").style.display="";
                                document.getElementById("process_gunbroker_order_button").addEventListener("click", function(){
                                    if (window.confirm("This will place a new distributor order, fulfilling GunBroker Order#" + document.getElementById("gunbroker_order_id").value + ". Are you sure you need a new order placed, and do you want to proceed?")){
                                        document.getElementById("process_gunbroker_order_button").disabled = true;
                                        document.getElementById('process_gunbroker_order_button').innerText = 'Please Wait...';
                                        fetch("https://ffl-api.garidium.com", {
                                            method: "POST",
                                            headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                            },
                                            body: JSON.stringify({"action": "place_distributor_order", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>" , "order_source": "gunbroker", "order_id": document.getElementById("gunbroker_order_id").value}})
                                        })
                                        .then(response=>response.json())
                                        .then(data=>{ 
                                            document.getElementById("process_gunbroker_order_button").disabled = false; 
                                            document.getElementById('process_gunbroker_order_button').innerText = 'Fulfill';     
                                            if (data.Error != null && data.Error != undefined){
                                                alert(data.Error);
                                            }else{
                                                of_grid.forceRender();
                                            }
                                        });
                                    }
                                });

                                document.getElementById("import_gunbroker_order_button").addEventListener("click", function(){
                                    if (window.confirm("This will import GunBroker Order#" + document.getElementById("gunbroker_order_id").value + ", creating an equivalent WooCommerce Order. The order will come over with the email and selected FFL in the order comments section, so that the customer isn't emailed. This is a beta feature in testing, Are you sure you want to proceed?")){
                                        document.getElementById("import_gunbroker_order_button").disabled = true;
                                        document.getElementById('import_gunbroker_order_button').innerText = 'Please Wait...';
                                        fetch("https://ffl-api.garidium.com", {
                                            method: "POST",
                                            headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                                            },
                                            body: JSON.stringify({"action": "import_gunbroker_order", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey); ?>" , "gunbroker_order_id": document.getElementById("gunbroker_order_id").value}})
                                        })
                                        .then(response=>response.json())
                                        .then(data=>{ 
                                            document.getElementById("import_gunbroker_order_button").disabled = false; 
                                            document.getElementById('import_gunbroker_order_button').innerText = 'Import';     
                                            if (data.Error != null && data.Error != undefined){
                                                alert(data.Error);
                                            }else{
                                                alert("Gunbroker Order Imported, go to the WooCommerce order page to view Order.");
                                            }
                                        });
                                    }
                                });
                            }
                        });
                        
                    </script>
                </div>
            </div>         
            <div id="logs" class="tabcontent">
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <a class="button alt" onclick="log_grid.forceRender();">Refresh</a>                 
                    <div id="log_table"></div>
                    <a class="button alt" onclick="log_grid.forceRender();">Refresh</a>                 
                    <script>
                        const window_height2 = (window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight) * 0.8;
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        var log_grid = new gridjs.Grid({
                            columns: [
                                {name: 'Timestamp', width: '160px', formatter: (cell) => `${new Date(cell).toLocaleString()}`}, 
                                {name: "Message"}
                            ],
                            resizable: true,
                            fixedHeader: true,
                            style: {
                                td: {
                                    'padding': '3px',
                                    'background': 'black',
                                    'color': '#a1e89f',
                                    'border': 'solid #353333 1px'
                                },
                                th: {
                                    'padding': '3px',
                                    'background': 'black',
                                    'color': '#a1e89f',
                                    'border': 'solid #353333 1px'
                                }
                            },
                            server: {
                                url: 'https://ffl-api.garidium.com',
                                method: 'POST',
                                headers: {
                                    "Accept": "application/json",
                                    "Content-Type": "application/json",
                                    "x-api-key": "<?php echo esc_attr($gFFLCockpitKey);?>",
			                    },
                                body: JSON.stringify({"action": "get_logs", "data": {"api_key": "<?php echo esc_attr($gFFLCockpitKey);?>", "log_count": 2}}),
                                then: data => JSON.parse(data).logs.map(log => [
                                                                   log.timestamp, 
                                                                   log.message])
                            } 
                        })
                    </script>
                </div>
            </div>
            <div id="help_center" class="tabcontent">
                <script>
                    function load_help_videos(){
                        fetch("https://ffl-api.garidium.com", {
                            method: "POST",
                            headers: {
                            "Accept": "application/json",
                            "Content-Type": "application/json",
                            "x-api-key": "<?php echo esc_attr($gFFLCockpitKey); ?>",
                            },
                            body: JSON.stringify({"action": "get_training_videos"})
                        })
                        .then(response=>response.json())
                        .then(data=>{ 
                            var video_div = document.getElementById("training_videos");
                            video_div.innerHTML = '';
                            for (var i = 0, l = data.length; i < l; i++) {
                                var innerDiv = document.createElement("div");
                                innerDiv.innerHTML = '<div style="width:390px;"><iframe width="370" height="208" src="' + data[i].url + '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe><br><span style="width:99%;">' + data[i].title + '</span><br><span style="color:#a29f9f !important;width:99%;height:50px;">' + data[i].description + '</span></div>';
                                video_div.appendChild(innerDiv);
                            }
                        });
                    }
                </script>
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <div class="video_grid" id="training_videos"></div>
                </div>
            </div>    
        </div>

    <?php }
}
