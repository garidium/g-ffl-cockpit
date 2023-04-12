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
        add_menu_page('g-FFL Cockpit Settings Page', 'g-FFL Cockpit', 'manage_options', 'g-ffl-cockpit-settings', array($this, 'g_ffl_cockpit_settings_page'), 'dashicons-location-alt', 70);
        add_action('admin_init', array($this, 'register_g_ffl_cockpit_settings'));
    }

    function register_g_ffl_cockpit_settings()
    {
        //register our settings
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_key');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_configuration');
    }

    function g_ffl_cockpit_settings_page()
    {
        $gFFLCheckoutKey = get_option('g_ffl_cockpit_key');
        echo '<script type="text/javascript">
                let gFFLCheckoutKey = "' . esc_attr($gFFLCheckoutKey) . '"
              </script>';

        ?>
        
        <div class="wrap">
            <a href="https://garidium.com" target="_blank" style="display: inline-block">
                <img src="<?php echo plugin_dir_url(__FILE__) . 'images/ffl-cockpit-logo.png'?>">
            </a>
            <br><br>
            <!-- Tab links -->
            <div class="tab">
                <button class="tablinks" onclick="openTab(event, 'configuration')" id="defaultOpen">Configuration</button>
                <button class="tablinks" onclick="openTab(event, 'product_feed')">Product Feed</button>
                <button class="tablinks" onclick="openTab(event, 'fulfillment')">Fulfillment</button>
                <button class="tablinks" onclick="openTab(event, 'logs')">Logs</button>
                <button class="tablinks" onclick="openTab(event, 'instructions')">Help Center</button>
            </div>
            <!-- Tab content -->
            <div id="configuration" class="tabcontent">
            <h3>Configuration</h3>
                <div class="postbox" style="padding: 10px;margin-top: 10px">
                    <form method="post" action="options.php" onSubmit="return setConfig('<?php echo esc_attr($gFFLCheckoutKey);?>');">
                        <?php settings_fields('g-ffl-cockpit-settings'); ?>
                        <table class="form-table">
                        <tr>
                                <td style="vertical-align:top;width:150px;font-weight:bold;" scope="row">g-FFL Cockpit Key:</td>
                                <td style="padding:5px;vertical-align:top;">
                                    <div class="user-pass-wrap">
                                        <div class="wp-pwd">
                                            <input type="password" style="width: 350px;" name="g_ffl_cockpit_key" id="g_ffl_cockpit_key" 
                                                aria-describedby="login_error" class="input password-input" size="20"
                                                value="<?php echo esc_attr($gFFLCheckoutKey); ?>"/>
                                        </div>
                                        <p>Email sales@garidium.com to get a key, or if your key has expired.</p>
                                    </div>
                                </td>
                                <td>
                                    <div id="g-ffl-admin-buttons" align="right" style="margin:5px;display:none;">
                                        <b>Admin Functions:&nbsp;</b>
                                        <a class="button alt" onclick="get_and_set_cockpit_configuration(document.getElementById('g_ffl_cockpit_key').value);document.getElementById('admin_current_editing_key').style.display='';">Load Config</a>
                                        <a class="button alt" onclick="setConfig(document.getElementById('g_ffl_cockpit_key').value);">Save</a>
                                        <br><br><span style="padding:10px;color:red;display:none;" id="admin_current_editing_key"></span>
                                        <script>document.getElementById("admin_current_editing_key").innerHTML = 'Editing: ' + document.getElementById('g_ffl_cockpit_key').value;</script>
                                    </div>
                                </td>
                            </tr>

                            <tr valign="top">
                                <td colspan=3>
                                <div id="jsoneditor" style="width: 100%; height: 500px;"></div>
                                <input type="hidden" name="g_ffl_cockpit_configuration" id="g_ffl_cockpit_configuration">
                                <script>
                                    function get_and_set_cockpit_configuration(api_key){
                                        fetch("https://ffl-api.garidium.com", {
                                            method: "POST",
                                            headers: {
                                            "Accept": "application/json",
                                            "Content-Type": "application/json",
                                            "x-api-key": "<?php echo esc_attr($gFFLCheckoutKey);?>",
                                            },
                                            body: JSON.stringify({"action": "get_subscription", "data": {"api_key": api_key}})
                                        })
                                        .then(response=>response.json())
                                        .then(data=>{
                                            try{
                                                cockpit_configuration = JSON.parse(data[0].cockpit_configuration);
                                                editor.set(cockpit_configuration);
                                            } catch (error) {
                                                console.error("No configuration found for this key, setting to default.");
                                            }
                                        });
                                    }
                                </script>
                                <script>
                                    // create the editor
                                    var options = {
                                        modes: ['text', 'code', 'tree', 'form', 'view'],
                                        mode: 'tree',
                                        ace: ace
                                    }
                                    var editor = new JSONEditor(document.getElementById("jsoneditor"), options);
                                    window.onload = function(){
                                        get_and_set_cockpit_configuration("<?php echo esc_attr($gFFLCheckoutKey);?>");
                                        if (window.location.host == 'garidium.com'){
                                            document.getElementById('g-ffl-admin-buttons').style.display = '';
                                        }
                                    }
                                </script>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>
                    </form>
                </div>
            </div>
            <div id="product_feed" class="tabcontent">
                <h3>Product Feed</h3>
                <div class="postbox" style="padding: 10px;margin-top: 10px;overflow-x:scroll;">
                    <p>The Product Feed is based on your Configuration. The synchronization process will run every 15-minutes, at which point any changes you make to your configuration will be applied. This list will show items from all distributors configured, and with quantities less than your minimum listing quantity. We list one product per UPC, based on availability and price.</p>
                    <div id="product_feed_table"></div>
                    <div style="padding:5px;"><button id="download_inventory_button" class="button alt" data-marker-id="">Download Inventory</button></div>
                    <script>
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        new gridjs.Grid({
                            columns: [
                                //formatter: (_, row) => `${row.cells[0].data?row.cells[2].data + '*':row.cells[2].data}`
                                //{sort: false, name: "List", width: '50px', formatter: (cell) => `${cell?"Y":"N"}`}, 
                                {name: "Dist", width: '60px'},
                                {name: 'SKU', width: '150px'}, 
                                {name: "UPC", width: '120px'},
                                {name: "MFG", width: '175px'},
                                {name: "MPN", width: '175px'},
                                {name: "Qty", width: '55px'},
                                {name: 'Cost', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                //{name: 'MAP', width: '80px', formatter: (cell) => `${(cell==null || cell == 0)?'':'$'+cell.toFixed(2)}`}, 
                                {name: 'Ship', width: '60px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                {name: 'Total', width: '80px', formatter: (cell) => `$${cell.toFixed(2)}`}, 
                                {name: 'Name'}, 
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
                                        let colName = ['distid', 'distsku', 'upc', 'mfg_name', 'mpn', 'qty_on_hand', 'unit_price', 'shipping_cost', 'total_cost',  'name'][col.index];
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
                                limit: 25,
                                server: {
                                    url: (prev, page, limit) => `${prev}&limit=${limit}&offset=${page * limit}`
                                }
                            },
                            fixedHeader: true,
                            //height: '400px',
                            //width: '1500px',
                            style: {
                                table: { 
                                    'white-space': 'nowrap',
                                },
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
                                    "x-api-key": "<?php echo esc_attr($gFFLCheckoutKey); ?>",
			                    },
                                then: data => JSON.parse(data).products.map(product => [//product.is_best_item,
                                                                   product.distid, 
                                                                   product.distsku,
                                                                   product.upc, 
                                                                   product.mfg_name, //+ ' (' + product.mpn + ")",
                                                                   product.mpn,
                                                                   product.qty_on_hand, 
                                                                   product.unit_price,  
                                                                   product.shipping_cost,
                                                                   product.total_cost,
                                                                   product.name]),  
                                                                   //product.drop_ship_flg,
                                                                   //product.map_price, 
                                                                   //product.item_cat,                                          
                                                                   //product.price])                                                            
                                                                   //product.drop_ship_flg])
                                total: data => JSON.parse(data).count
                            } 
                        }).render(document.getElementById("product_feed_table"));
                    
                        document.getElementById("download_inventory_button").addEventListener("click", function(){
                            document.getElementById("download_inventory_button").disabled = true;
                            document.getElementById('download_inventory_button').innerText = 'Please Wait...';
                            fetch("https://ffl-api.garidium.com/download", {
                                method: "POST",
                                headers: {
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "x-api-key": "<?php echo esc_attr($gFFLCheckoutKey); ?>",
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
                </div>
            </div>
            <div id="fulfillment" class="tabcontent">
                <h3>Fulfillment</h3>
                <div class="postbox" style="padding: 10px;margin-top: 10px">
                    <p>Coming Soon! Automated Fulfillment Orders will be reported on here</p>
                </div>
            </div>            
            <div id="logs" class="tabcontent">
                <h3>Logs</h3>
                <div class="postbox" style="padding: 10px;margin-top: 10px">
                    <p>Coming Soon! View Process Logs to Monitor Feeds</p>
                </div>
            </div>  
            <div id="instructions" class="tabcontent">
                <h3>Help Center</h3>
                <div class="postbox" style="padding: 10px;margin-top: 10px">
                    <p>This is where the help documentation and videos will go. In the meantime, email sales@garidium.com with any questions.</p>
                </div>
            </div>
        </div>
    <?php }
}
