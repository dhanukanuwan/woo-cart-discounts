<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://hashcodeab.se
 * @since      1.0.0
 *
 * @package    Hashcode_Custom_Woo
 * @subpackage Hashcode_Custom_Woo/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Hashcode_Custom_Woo
 * @subpackage Hashcode_Custom_Woo/includes
 * @author     Dhanuka Gunarathna <dhanuka@hashcodeab.se>
 */
class Hashcode_Custom_Woo_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'hashcode-custom-woo',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
