<?php

/**
 * Fired during plugin activation
 *
 * @link       garidium.com
 * @since      1.0.0
 *
 * @package    G_ffl_Cockpit
 * @subpackage G_ffl_Cockpit/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    G_ffl_Cockpit
 * @subpackage G_ffl_Cockpit/includes
 * @author     Big G <sales@garidium.com>
 */
class G_ffl_Cockpit_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        if (get_option('woocommerce_ship_to_destination') != 'shipping' ) {
            update_option('woocommerce_ship_to_destination', 'shipping');
        }
	}

}
