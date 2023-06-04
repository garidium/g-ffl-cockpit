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
        add_menu_page('g-FFL Cockpit Settings Page', $custom_plugin_name, 'manage_options', 'g-ffl-cockpit-settings', array($this, 'g_ffl_cockpit_settings_page'), 'dashicons-location-alt', 70);
        add_action('admin_init', array($this, 'register_g_ffl_cockpit_settings'));
    }

    function register_g_ffl_cockpit_settings()
    {
        //register our settings
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_key');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_configuration');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_plugin_name');
        register_setting('g-ffl-cockpit-settings', 'g_ffl_cockpit_plugin_logo_url');
    }

    function g_ffl_cockpit_settings_page()
    {
        $gFFLCheckoutKey = get_option('g_ffl_cockpit_key');
        echo '<script type="text/javascript">
                let gFFLCheckoutKey = "' . esc_attr($gFFLCheckoutKey) . '"
              </script>';

        ?>

        <div class="wrap">
            <img src="<?php echo esc_attr(get_option('g_ffl_cockpit_plugin_logo_url') != '' ? get_option('g_ffl_cockpit_plugin_logo_url') : plugin_dir_url(__FILE__) . 'images/ffl-cockpit-logo.png');?>">
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
            <!--<h3>Configuration</h3>-->
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
                                                <a class="button alt" onclick="get_and_set_cockpit_configuration(document.getElementById('g_ffl_cockpit_key').value);">Load Config</a>
                                        </div>
                                        <p>Email sales@garidium.com to get a key, or if your key has expired.</p>
                                    </div>
                                </td>
                                <td>
                                    <div id="g-ffl-admin-buttons" align="right" style="margin:5px;display:none;">
                                        <b>Admin Functions:&nbsp;</b>
                                        <a class="button alt" onclick="get_and_set_cockpit_configuration(document.getElementById('g_ffl_cockpit_key').value);document.getElementById('admin_current_editing_key').innerHTML = 'Editing: ' + document.getElementById('g_ffl_cockpit_key').value;document.getElementById('admin_current_editing_key').style.display='';document.getElementById('submit_button_div').style.display='none';">Load Config</a>
                                        <a class="button alt" onclick="setConfig(document.getElementById('g_ffl_cockpit_key').value);">Save</a>
                                        <br><br><span style="padding:10px;color:red;display:none;" id="admin_current_editing_key"></span>
                                    </div>
                                </td>
                            </tr>

                            <tr valign="top">
                                <td colspan=3>
                                <div id="jsoneditor" style="width: 100%; height: 500px;"></div>
                                <input type="hidden" name="g_ffl_cockpit_configuration" id="g_ffl_cockpit_configuration">
                                <script>
                                    function get_and_set_cockpit_configuration(api_key){
                                        if (api_key != null && api_key.length > 0){
                                            fetch("https://ffl-api.garidium.com", {
                                                method: "POST",
                                                headers: {
                                                "Accept": "application/json",
                                                "Content-Type": "application/json",
                                                "x-api-key": api_key,
                                                },
                                                body: JSON.stringify({"action": "get_subscription", "data": {"api_key": api_key}})
                                            })
                                            .then(response=>response.json())
                                            .then(data=>{
                                                try{
                                                    cockpit_configuration = JSON.parse(data[0].cockpit_configuration);
                                                    editor.set(cockpit_configuration);
                                                } catch (error) {
                                                    alert("No configuration found for this key, setting to default.");
                                                }
                                            });
                                        }else{
                                            alert("No API Key Configured");
                                        }
                                    }
                                </script>
                                <script>
                                    // create the editor
                                    var options = {
                                        modes: ['text', 'code', 'tree', 'form', 'view'],
                                        mode: 'tree',
                                        ace: ace,
                                        schema: {
                                            "$schema": "http://json-schema.org/draft-04/schema#",
                                            "title": "g-FFL Cockpit Configuration",
                                            "description": "Configuration file for the g-FFL Cockpit WooCommerce Plugin",
                                            "type": "object",
                                            "required": ["distributors", "max_distributor_cost",
                                                         "max_listing_count", "min_distributor_cost",
                                                         "min_quantity_to_list", "notification_email",
                                                         "pricing", "product_restrictions"],
                                            "properties": {
                                                "max_listing_count": {
                                                    "description": "Maximum Listing Count",
                                                    "type": "integer",
                                                    "exclusiveMinimum": true,
                                                    "minimum":0
                                                },
                                                "notification_email": {
                                                    "description": "Email for System Notifications and Alerts",
                                                    "type": "string"
                                                },
                                                "max_distributor_cost": {
                                                    "description": "Maximum Distributor Cost for a Listed Item",
                                                    "type": "number"
                                                },
                                                "min_distributor_cost": {
                                                    "description": "Minimum Distributor Cost for a Listed Item",
                                                    "type": "number"
                                                },
                                                "min_quantity_to_list": {
                                                    "description": "Minimum In-Stock Quantity to List an Item",
                                                    "type": "integer"
                                                },
                                                "product_restrictions": {
                                                    "description": "Product Restrictions",
                                                    "$ref": "#/definitions/product_restrictions"
                                                },
                                                "pricing": {
                                                    "title": "Pricing",
                                                    "description": "Pricing and Margin Configuration",
                                                    "$ref": "#/definitions/pricing"
                                                },
                                                "distributors": {
                                                    "description": "Firearms and Accessories Distributors",
                                                    "type": "object",
                                                    "properties": {
                                                        "Lipseys": {
                                                            "description": "Lipseys Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_lipseys"
                                                        },
                                                        "Zanders": {
                                                            "description": "Zanders Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_zanders"
                                                        },
                                                        "Davidsons": {
                                                            "description": "Davidsons Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_davidsons"
                                                        },
                                                        "RSR Group": {
                                                            "description": "RSR Group Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_rsr"
                                                        },
                                                        "2nd Amendment Wholesale": {
                                                            "description": "2nd Amendment Wholesale Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_twoaw"
                                                        },
                                                        "Chatanooga Shooting Supplies": {
                                                            "description": "Chatanooga Shooting Supplies Product Feed Configuration",
                                                            "$ref": "#/definitions/distributor_cssi"
                                                        }
                                                    },
                                                    "anyOf": [
                                                        {
                                                            "required": [
                                                                "Chatanooga Shooting Supplies"
                                                            ]
                                                        },                 
                                                        {
                                                            "required": [
                                                                "2nd Amendment Wholesale"
                                                            ]
                                                        },                {
                                                            "required": [
                                                                "RSR Group"
                                                            ]
                                                        },                {
                                                            "required": [
                                                                "Zanders"
                                                            ]
                                                        },                {
                                                            "required": [
                                                                "Davidsons"
                                                            ]
                                                        },                {
                                                            "required": [
                                                                "Lipseys"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "targets": {
                                                    "title": "Targets",
                                                    "description": "Targets to import products to, such as woocommerce, ammoseek or gunbroker",
                                                    "type": "object",
                                                    "additionalProperties": false,
                                                    "properties": {
                                                        "woo": {
                                                            "description": "WooCommerce Product Feed Settings",
                                                            "$ref": "#/definitions/target_woocommerce"
                                                        },
                                                        "ammoseek": {
                                                            "description": "AmmoSeek Product Feed Settings",
                                                            "$ref": "#/definitions/target_rss"
                                                        },
                                                        "gun.deals": {
                                                            "description": "Gun.Deals Product Feed Settings",
                                                            "$ref": "#/definitions/target_rss"
                                                        },
                                                        "gunbroker": {
                                                            "description": "Gunbroker Product Feed Settings",
                                                            "$ref": "#/definitions/target_gunbroker"
                                                        }
                                                    }
                                                },
                                            },
                                            "definitions":
                                            {
                                                "product_restrictions":{
                                                    "properties": {
                                                        "sku": {
                                                            "properties": {
                                                                "exclude": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                },
                                                                "include": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        "upc": {
                                                            "properties": {
                                                                "exclude": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                },
                                                                "include": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        "brand": {
                                                            "properties": {
                                                                "exclude": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                },
                                                                "include": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        "category": {
                                                            "properties": {
                                                                "exclude": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                },
                                                                "include": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        "product_class": {
                                                            "properties": {
                                                                "exclude": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                },
                                                                "include": {
                                                                    "type": "array",
                                                                    "items": {
                                                                        "type": "string"
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    },
                                                    "anyOf": [
                                                        {
                                                            "required": [
                                                                "product_class"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "sku"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "category"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "brand"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "upc"
                                                            ]
                                                        } 
                                                    ]
                                                },
                                                "pricing": {
                                                    "properties": {
                                                        "margin": {
                                                            "properties": {
                                                                "default": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "firearms": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "ammunition": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "accessories": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom1": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom2": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom3": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom4": {
                                                                    "$ref": "#/definitions/margin"
                                                                },
                                                                "custom5": {
                                                                    "$ref": "#/definitions/margin"
                                                                }
                                                            }
                                                        },
                                                        "sales_tax_assumption": {
                                                            "type": "number",
                                                            "minimum":0,
                                                            "maximum":0.99
                                                        },
                                                        "credit_card_fee_percent": {
                                                            "type": "number",
                                                            "minimum": 0,
                                                            "maximum": 0.99
                                                        },
                                                        "round_to_nearest_dollar": {
                                                            "type": "boolean"
                                                        },
                                                        "include_shipping_in_price": {
                                                            "type": "boolean"
                                                        },
                                                        "credit_card_fee_transaction": {
                                                            "type": "number"
                                                        },
                                                        "include_credit_card_fees_in_price": {
                                                            "type": "boolean"
                                                        }
                                                    },
                                                    "required": [
                                                        "credit_card_fee_percent",
                                                        "credit_card_fee_transaction",
                                                        "include_credit_card_fees_in_price",
                                                        "include_shipping_in_price",
                                                        "margin",
                                                        "round_to_nearest_dollar",
                                                        "sales_tax_assumption"
                                                    ]
                                                },
                                                "margin": {
                                                    "properties": {
                                                        "sku": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "upc": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "brand": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "category": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "product_class": {
                                                            "type": "array",
                                                            "items": {
                                                                "type": "string"
                                                            }
                                                        },
                                                        "margin_dollar": {
                                                            "type": "number"
                                                        },
                                                        "margin_percentage": {
                                                            "type": "number",
                                                            "minimum": 0,
                                                            "maximum": 0.99
                                                        }
                                                    },
                                                    "anyOf": [
                                                        {
                                                            "required": [
                                                                "margin_dollar"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "margin_percentage"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "flat_rate_shipping": {
                                                    "title": "Flat Rate Shipping",
                                                    "description": "Flat Rate Shipping Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "cost": {
                                                            "type": "number"
                                                        },
                                                        "shipping_class": {
                                                            "type": "string"
                                                        }
                                                    },
                                                    "required": [
                                                        "cost",
                                                        "shipping_class"
                                                    ]
                                                },
                                                "price_based_shipping_levels": {
                                                    "type": "array",
                                                    "items": {
                                                        "$ref": "#/definitions/price_based_shipping"
                                                    }
                                                },
                                                "price_based_shipping": {
                                                    "title": "Price-based Shipping",
                                                    "description": "Price-based Shipping Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "flat_rate": {
                                                            "$ref": "#/definitions/flat_rate_shipping"
                                                        },
                                                        "max_unit_cost": {
                                                            "type": "integer"
                                                        },
                                                        "min_unit_cost": {
                                                            "type": "integer"
                                                        }
                                                    },
                                                    "required": [
                                                        "flat_rate",
                                                        "min_unit_cost"
                                                    ]
                                                },
                                                "shipping": {
                                                    "title": "Shipping",
                                                    "description": "Shipping Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "price_based_shipping_levels": {
                                                            "$ref": "#/definitions/price_based_shipping_levels"
                                                        },
                                                        "flat_rate": {
                                                            "$ref": "#/definitions/flat_rate_shipping"
                                                        }
                                                    },
                                                    "oneOf": [
                                                        {
                                                            "required": [
                                                                "price_based_shipping"
                                                            ]
                                                        },
                                                        {
                                                            "required": [
                                                                "flat_rate"
                                                            ]
                                                        }
                                                    ]
                                                },
                                                "target_rss": {
                                                    "title": "RSS Feed",
                                                    "description": "RSS Feed Listing Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "listed_products": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "guns": {
                                                                    "description": "Guns Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"        
                                                                },
                                                                "brass": {
                                                                    "description": "Brass Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "powder": {
                                                                    "description": "Powder Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "bullets": {
                                                                    "description": "Bullets Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "primers": {
                                                                    "description": "Primers Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "magazines": {
                                                                    "description": "Magazine Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "ammunition": {
                                                                    "description": "Ammunition Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "reloading": {
                                                                    "description": "Reloading Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "reloading_misc": {
                                                                    "description": "Miscellaneous Reloading Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "parts": {
                                                                    "description": "Parts Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                },
                                                                "other": {
                                                                    "description": "Other Product Restrictions",
                                                                    "$ref": "#/definitions/product_restrictions"
                                                                }
                                                            },
                                                            "anyOf": [
                                                                {
                                                                    "required": [
                                                                        "guns"
                                                                    ]
                                                                },                 
                                                                {
                                                                    "required": [
                                                                        "brass"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "powder"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "bullets"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "primers"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "magazines"
                                                                    ]
                                                                },                {
                                                                    "required": [
                                                                        "ammunition"
                                                                    ]
                                                                },
                                                                {
                                                                    "required": [
                                                                        "reloading"
                                                                    ]
                                                                },
                                                                {
                                                                    "required": [
                                                                        "reloading_misc"
                                                                    ]
                                                                },
                                                                {
                                                                    "required": [
                                                                        "parts"
                                                                    ]
                                                                },
                                                                {
                                                                    "required": [
                                                                        "other"
                                                                    ]
                                                                }
                                                            ],
                                                            "title": "Listed Products"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "listed_products"
                                                    ]
                                                },
                                                "target_woocommerce": {
                                                    "title": "WooCommerce",
                                                    "description": "WooCommerce Listing Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "url": {
                                                            "type": "string",
                                                            "format": "uri",
                                                            "qt-uri-protocols": [
                                                                "https", "http"
                                                            ]
                                                        },
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "pricing": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "margin_adjustment_dollar": {
                                                                    "type": "number"
                                                                },
                                                                "margin_adjustment_percent": {
                                                                    "type": "number",
                                                                    "minimum": 0,
                                                                    "maximum": 0.99
                                                                }
                                                            }
                                                        },
                                                        "consumer-key": {
                                                            "type": "string"
                                                        },
                                                        "consumer-secret": {
                                                            "type": "string"
                                                        },
                                                        "load_batch_count": {
                                                            "description": "Number of products to load in each thread, suggested at 10.",
                                                            "type": "integer"
                                                        },
                                                        "product_restrictions": {
                                                            "description": "Product Restrictions",
                                                            "$ref": "#/definitions/product_restrictions"
                                                        },
                                                        "manage_product_categories": {
                                                            "type": "boolean"
                                                        },
                                                        "manage_product_attributes": {
                                                            "type": "boolean"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "consumer-key",
                                                        "consumer-secret",
                                                        "load_batch_count",
                                                        "manage_product_categories",
                                                        "manage_product_attributes",
                                                        "pricing",
                                                        "url"
                                                    ]
                                                },
                                                "target_gunbroker": {
                                                    "title": "Gunbroker",
                                                    "description": "Gunbroker Listing Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "key": {
                                                            "type": "string",
                                                            "format": "uuid"
                                                        },
                                                        "url": {
                                                            "type": "string",
                                                            "format": "uri",
                                                            "qt-uri-protocols": [
                                                                "https"
                                                            ]
                                                        },
                                                        "fees": {
                                                            "description": "Gunbroker Fees",
                                                            "title": "Fees",
                                                            "type": "object",
                                                            "properties": {
                                                                "compliance": {
                                                                    "type": "number"
                                                                },
                                                                "tier_1_dollar": {
                                                                    "type": "integer"
                                                                },
                                                                "tier_1_percent": {
                                                                    "type": "number",
                                                                    "minimum": 0,
                                                                    "maximum": 0.99
                                                                },
                                                                "tier_2_percent": {
                                                                    "type": "number",
                                                                    "minimum": 0,
                                                                    "maximum": 0.99
                                                                }
                                                            },
                                                            "required": [
                                                                "compliance",
                                                                "tier_1_dollar",
                                                                "tier_1_percent",
                                                                "tier_2_percent"
                                                            ]
                                                        },
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "pricing": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "margin_adjustment_dollar": {
                                                                    "type": "number"
                                                                },
                                                                "margin_adjustment_percent": {
                                                                    "type": "number",
                                                                    "minimum": 0,
                                                                    "maximum": 0.99
                                                                }
                                                            }
                                                        },
                                                        "password": {
                                                            "type": "string"
                                                        },
                                                        "username": {
                                                            "type": "string"
                                                        },
                                                        "payment_methods": {
                                                            "title": "PaymentMethods",
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "COD": {
                                                                    "type": "boolean"
                                                                },
                                                                "Amex": {
                                                                    "type": "boolean"
                                                                },
                                                                "check": {
                                                                    "type": "boolean"
                                                                },
                                                                "Escrow": {
                                                                    "type": "boolean"
                                                                },
                                                                "PayPal": {
                                                                    "type": "boolean"
                                                                },
                                                                "Discover": {
                                                                    "type": "boolean"
                                                                },
                                                                "MoneyOrder": {
                                                                    "type": "boolean"
                                                                },
                                                                "FreedomCoin": {
                                                                    "type": "boolean"
                                                                },
                                                                "SeeItemDesc": {
                                                                    "type": "boolean"
                                                                },
                                                                "CertifiedCheck": {
                                                                    "type": "boolean"
                                                                },
                                                                "USPSMoneyOrder": {
                                                                    "type": "boolean"
                                                                },
                                                                "VisaMastercard": {
                                                                    "type": "boolean"
                                                                }
                                                            },
                                                            "required": [
                                                                "Amex",
                                                                "COD",
                                                                "CertifiedCheck",
                                                                "Discover",
                                                                "Escrow",
                                                                "FreedomCoin",
                                                                "MoneyOrder",
                                                                "PayPal",
                                                                "SeeItemDesc",
                                                                "USPSMoneyOrder",
                                                                "VisaMastercard",
                                                                "check"
                                                            ]
                                                        },
                                                        "from_postal_code": {
                                                            "type": "string"
                                                        },
                                                        "listing_duration": {
                                                            "type": "integer"
                                                        },
                                                        "picture_flairing": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "active": {
                                                                    "type": "boolean"
                                                                },
                                                                "border": {
                                                                    "type": "object",
                                                                    "additionalProperties": false,
                                                                    "properties": {
                                                                        "color": {
                                                                            "type": "string"
                                                                        },
                                                                        "active": {
                                                                            "type": "boolean"
                                                                        },
                                                                        "pixel_width": {
                                                                            "type": "integer"
                                                                        }
                                                                    },
                                                                    "required": [
                                                                        "active",
                                                                        "color",
                                                                        "pixel_width"
                                                                    ],
                                                                    "title": "Border"
                                                                }
                                                            },
                                                            "required": [
                                                                "active"
                                                            ],
                                                            "title": "Picture Flairing"
                                                        },
                                                        "standard_text_id": {
                                                            "type": "integer"
                                                        },
                                                        "inspection_period": {
                                                            "type": "integer"
                                                        },
                                                        "product_restrictions": {
                                                            "description": "Product Restrictions",
                                                            "$ref": "#/definitions/product_restrictions"
                                                        },
                                                        "standard_description_html": {
                                                            "type": "string",
                                                            "description": "Text that appears in all Gunbroker Listings"
                                                        },
                                                        "drive_customers_to_website": {
                                                            "type": "object",
                                                            "additionalProperties": false,
                                                            "properties": {
                                                                "active": {
                                                                    "type": "boolean"
                                                                },
                                                                "message": {
                                                                    "description": "This message will appear above the listing to prompt the user to go to your website.",
                                                                    "type": "string"
                                                                },
                                                                "website_banner": {
                                                                    "description": "This is a place to put a image banner area, or some other branding material.",
                                                                    "type": "string"
                                                                },
                                                                "product_qr_codes": {
                                                                    "type": "object",
                                                                    "additionalProperties": false,
                                                                    "properties": {
                                                                        "active": {
                                                                            "type": "boolean"
                                                                        },
                                                                        "icon_url": {
                                                                            "type": "string",
                                                                            "format": "uri",
                                                                            "qt-uri-protocols": [
                                                                                "https", "http"
                                                                            ]
                                                                        }
                                                                    },
                                                                    "required": [
                                                                        "active",
                                                                        "icon_url"
                                                                    ],
                                                                    "title": "Product QR Codes"
                                                                }
                                                            },
                                                            "required": [
                                                                "active"
                                                            ],
                                                            "title": "DriveCustomersToWebsite"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "fees",
                                                        "from_postal_code",
                                                        "inspection_period",
                                                        "key",
                                                        "listing_duration",
                                                        "password",
                                                        "payment_methods",
                                                        "pricing",
                                                        "standard_description_html",
                                                        "standard_text_id",
                                                        "url",
                                                        "username"
                                                    ]
                                                },
                                                "distributor_cssi": {
                                                    "title": "Chatanooga Shooting Supplies",
                                                    "description": "Chatanooga Shooting Supplies Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "CSSI"
                                                        },
                                                        "api_sid": {
                                                            "type": "string"
                                                        },
                                                        "api_token": {
                                                            "type": "string"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_davidsons": {
                                                    "title": "Davidsons",
                                                    "description": "Davidsons Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "DAV"
                                                        },
                                                        "ftp_user": {
                                                            "type": "string"
                                                        },
                                                        "ftp_password": {
                                                            "type": "string"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_lipseys": {
                                                    "title": "Lipseys",
                                                    "description": "Lipseys Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "LIP"
                                                        },
                                                        "username": {
                                                            "type": "string"
                                                        },
                                                        "password": {
                                                            "type": "string"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_rsr": {
                                                    "title": "RSR Group",
                                                    "description": "RSR Group Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "RSR"
                                                        },
                                                        "ftp_user": {
                                                            "type": "string"
                                                        },
                                                        "ftp_password": {
                                                            "type": "string"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_twoaw": {
                                                    "title": "2nd Amendment Wholesale",
                                                    "description": "2nd Amendment Wholesale Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "2AW"
                                                        },
                                                        "username": {
                                                            "type": "string"
                                                        },
                                                        "password": {
                                                            "type": "string"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                },
                                                "distributor_zanders": {
                                                    "title": "Zanders",
                                                    "description": "Zanders Product Feed Configuration",
                                                    "type": "object",
                                                    "properties": {
                                                        "active": {
                                                            "type": "boolean"
                                                        },
                                                        "distid": {
                                                            "type": "string",
                                                            "const": "ZND"
                                                        },
                                                        "ftp_user": {
                                                            "type": "string"
                                                        },
                                                        "ftp_password": {
                                                            "type": "string"
                                                        },
                                                        "shipping": {
                                                            "$ref": "#/definitions/shipping"
                                                        },
                                                        "drop_ship_only_items": {
                                                            "type": "boolean"
                                                        },
                                                        "product_restrictions": {
                                                            "$ref": "#/definitions/product_restrictions"
                                                        }
                                                    },
                                                    "required": [
                                                        "active",
                                                        "distid",
                                                        "drop_ship_only_items",
                                                        "product_restrictions",
                                                        "shipping"
                                                    ]
                                                }
                                            }
                                        }
                                    }
                                    var editor = new JSONEditor(document.getElementById("jsoneditor"), options);
                                    editor.set({"Loading Configuration": "Please wait..."});
                                    window.onload = function(){
                                        get_and_set_cockpit_configuration("<?php echo esc_attr($gFFLCheckoutKey);?>");
                                        if (window.location.host == 'garidium.com'){
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
                        <div id="submit_button_div">
                            <?php submit_button(); ?>
                        </div>
                        <br>
                        <a style="cursor:pointer;" onclick="document.getElementById('white_label_settings_name').style.display='';document.getElementById('white_label_settings_url').style.display='';">&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;</a>
                    </form>
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
                            }
                            return "";
                        }
                        // https://unpkg.com/browse/gridjs@5.1.0/dist/
                        new gridjs.Grid({
                            columns: [
                                //formatter: (_, row) => `${row.cells[0].data?row.cells[2].data + '*':row.cells[2].data}`
                                //{sort: false, name: "List", width: '50px', formatter: (cell) => `${cell?"Y":"N"}`}, 
                                {name: 'Dist', width: '60px',
                                    formatter: (_, row) => gridjs.html(`<img align="center" width="50px" src="${get_distributor_logo(row.cells[0].data)}">`)
                                },
                                {name: 'SKU'}, 
                                {sort: false, name: 'Product Image', 
                                    formatter: (_, row) => gridjs.html(`<a style="cursor:pointer;" onclick="load_product_data('${row.cells[3].data.replace("\"","&quot;") + "','" + row.cells[0].data + "','" + row.cells[1].data + "','" + row.cells[2].data[0]['src']}')"><img width="100px" src="${row.cells[2].data[0]['src']}"></a>`)
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
                            height: window_height,
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
                                    "x-api-key": "<?php echo esc_attr($gFFLCheckoutKey); ?>",
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
                                    <!-- The Modal -->
                <div id="myModal" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content">
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
