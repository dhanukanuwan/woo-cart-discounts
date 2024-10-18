<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://hashcodeab.se
 * @since      1.0.0
 *
 * @package    Hashcode_Custom_Woo
 * @subpackage Hashcode_Custom_Woo/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Hashcode_Custom_Woo
 * @subpackage Hashcode_Custom_Woo/includes
 * @author     Dhanuka Gunarathna <dhanuka@hashcodeab.se>
 */
class Hashcode_Custom_Woo {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hashcode_Custom_Woo_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'HASHCODE_CUSTOM_WOO_VERSION' ) ) {
			$this->version = HASHCODE_CUSTOM_WOO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'hashcode-custom-woo';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hashcode_Custom_Woo_Loader. Orchestrates the hooks of the plugin.
	 * - Hashcode_Custom_Woo_i18n. Defines internationalization functionality.
	 * - Hashcode_Custom_Woo_Admin. Defines all hooks for the admin area.
	 * - Hashcode_Custom_Woo_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hashcode-custom-woo-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hashcode-custom-woo-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hashcode-custom-woo-admin.php';

		$this->loader = new Hashcode_Custom_Woo_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Hashcode_Custom_Woo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hashcode_Custom_Woo_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Hashcode_Custom_Woo_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_admin, 'hashcode_customer_post_purchase_discount', 999, 1 );
		$this->loader->add_action( 'woocommerce_settings_hashcode_product_discount', $plugin_admin, 'hashcode_product_discount_settings_tab_content' );
		$this->loader->add_action( 'woocommerce_settings_save_hashcode_product_discount', $plugin_admin, 'hashcode_product_discount_settings_tab_save' );

		$this->loader->add_filter( 'woocommerce_cart_item_price', $plugin_admin, 'hashcode_display_sale_price_in_cart', 30, 2 );
		$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin, 'hashcode_product_discount_settings_tab', 50 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hashcode_Custom_Woo_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
