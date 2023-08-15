<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              garidium.com
 * @since             1.0.0
 * @package           G_ffl_Cockpit
 *
 * @wordpress-plugin
 * Plugin Name:       g-FFL Cockpit
 * Plugin URI:        garidium.com/g-ffl-cockput
 * Description:       g-FFL Cockpit
 * Version:           1.2.9
 * WC requires at least: 3.0.0
 * WC tested up to:   4.0
 * Author:            Garidium LLC
 * Author URI:        garidium.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       g-ffl-cockpit
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if ( ! defined('ABSPATH')) exit;  // if direct access


/**
 * Check if WooCommerce is active
 **/
if (! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('G_FFL_COCKPIT_VERSION', '1.2.9');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-g-ffl-cockpit-activator.php
 */
function activate_g_ffl_cockpit()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-g-ffl-cockpit-activator.php';
    G_ffl_Cockpit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-g-ffl-cockpit-deactivator.php
 */
function deactivate_g_ffl_cockpit()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-g-ffl-cockpit-deactivator.php';
    G_ffl_Cockpit_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_g_ffl_cockpit');
register_deactivation_hook(__FILE__, 'deactivate_g_ffl_cockpit');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-g-ffl-cockpit.php';
require plugin_dir_path(__FILE__) . 'includes/fulfillment_options.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_g_ffl_cockpit()
{
    $plugin = new G_Ffl_Cockpit();
    $plugin->run();
}

run_g_ffl_cockpit();
