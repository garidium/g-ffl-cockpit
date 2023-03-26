<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       garidium.com
 * @since      1.0.0
 *
 * @package    G_ffl_Cockpit
 * @subpackage G_ffl_Cockpit/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    G_ffl_Cockpit
 * @subpackage G_ffl_Cockpit/includes
 * @author     Big G <sales@garidium.com>
 */
class G_ffl_Cockpit_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'g-ffl-cockpit',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
