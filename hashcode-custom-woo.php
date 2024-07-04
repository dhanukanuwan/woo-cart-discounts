<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://hashcodeab.se
 * @since             1.0.0
 * @package           Hashcode_Custom_Woo
 *
 * @wordpress-plugin
 * Plugin Name:       Hashcode Custom WooCommerce
 * Plugin URI:        https://hashcodeab.se
 * Description:       Custom Woocommerce functions developed by Hashcode AB
 * Version:           1.0.0
 * Author:            Dhanuka Gunarathna
 * Author URI:        https://hashcodeab.se/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hashcode-custom-woo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HASHCODE_CUSTOM_WOO_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hashcode-custom-woo-activator.php
 */
function activate_hashcode_custom_woo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hashcode-custom-woo-activator.php';
	Hashcode_Custom_Woo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hashcode-custom-woo-deactivator.php
 */
function deactivate_hashcode_custom_woo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hashcode-custom-woo-deactivator.php';
	Hashcode_Custom_Woo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hashcode_custom_woo' );
register_deactivation_hook( __FILE__, 'deactivate_hashcode_custom_woo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hashcode-custom-woo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_hashcode_custom_woo() {

	$plugin = new Hashcode_Custom_Woo();
	$plugin->run();
}
run_hashcode_custom_woo();
