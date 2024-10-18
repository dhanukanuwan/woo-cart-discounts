<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://hashcodeab.se
 * @since      1.0.0
 *
 * @package    Hashcode_Custom_Woo
 * @subpackage Hashcode_Custom_Woo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Hashcode_Custom_Woo
 * @subpackage Hashcode_Custom_Woo/admin
 * @author     Dhanuka Gunarathna <dhanuka@hashcodeab.se>
 */
class Hashcode_Custom_Woo_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name       The name of this plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Aapplies a 50% discount to products in the cart if the customer has previously purchased.
	 *
	 * @since    1.0.0
	 * @param array $cart  .
	 */
	public function hashcode_customer_post_purchase_discount( $cart ) {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$customer_orders = wc_get_orders(
			array(
				'customer_id' => get_current_user_id(),
				'status'      => array( 'completed' ),
			)
		);

		if ( empty( $customer_orders ) || is_wp_error( $customer_orders ) ) {
			return;
		}

		$purchased_product_ids = array();

		foreach ( $customer_orders as $customer_order ) {

			$order_items = $customer_order->get_items();

			if ( empty( $order_items ) || is_wp_error( $order_items ) ) {
				continue;
			}

			foreach ( $order_items as $order_item ) {

				$order_product_id = (int) $order_item->get_product_id();

				if ( ! empty( $order_product_id ) && ! in_array( $order_product_id, $purchased_product_ids, true ) ) {
					$purchased_product_ids[] = $order_product_id;
				}
			}
		}

		if ( empty( $purchased_product_ids ) ) {
			return;
		}

		$excluded_products = WC_Admin_Settings::get_option( 'hashcode_discount_excluded_products' );

		if ( empty( $excluded_products ) || ! is_array( $excluded_products ) ) {
			$excluded_products = array();
		} else {
			// Convert array value to int values.
			$excluded_products = array_map( 'intval', $excluded_products );
		}

		$cart_items = $cart->get_cart();

		if ( empty( $cart_items ) ) {
			return;
		}

		foreach ( $cart_items as $cart_item_key => $cart_item ) {

			$cart_product_id = (int) $cart_item['product_id'];

			// Skip to the next product if current product is excluded.
			if ( in_array( $cart_product_id, $excluded_products, true ) ) {
				continue;
			}

			if ( in_array( $cart_product_id, $purchased_product_ids, true ) ) {
				$discounted_price = $cart_item['data']->get_price() / 2;
				$cart_item['data']->set_price( $discounted_price );
				$cart_item['data']->set_sale_price( $discounted_price );
			}
		}
	}

	/**
	 * Display discounted price in the cart.
	 *
	 * @since    1.0.0
	 * @param int   $price  .
	 * @param array $values  .
	 */
	public function hashcode_display_sale_price_in_cart( $price, $values ) {

		$slashed_price = $values['data']->get_price_html();
		$is_on_sale    = $values['data']->is_on_sale();

		if ( $is_on_sale ) {
			$price = $slashed_price;
		}

		return $price;
	}

	/**
	 * Cross-sells settings tab.
	 *
	 * @since 1.0.0
	 * @param array $tabs .
	 */
	public function hashcode_product_discount_settings_tab( $tabs ) {

		$tabs['hashcode_product_discount'] = __( '50% discount settings', 'hashcode-custom-woo' );

		return $tabs;
	}

	/**
	 * Cross-sells settings tab.
	 *
	 * @since 1.0.0
	 */
	public function hashcode_product_discount_settings_tab_content() {

		WC_Admin_Settings::output_fields( $this->hashcode_product_discount_settings() );
	}

	/**
	 * Cross-sells settings tab.
	 *
	 * @since 1.0.0
	 */
	public function hashcode_product_discount_settings_tab_save() {

		WC_Admin_Settings::save_fields( $this->hashcode_product_discount_settings() );
	}

	/**
	 * Cross-sells settings.
	 *
	 * @since 1.0.0
	 */
	private function hashcode_product_discount_settings() {

		$products = get_posts(
			array(
				'post_type'      => 'product',
				'posts_per_page' => -1,
			)
		);

		$product_options = array();

		if ( ! empty( $products ) && ! is_wp_error( $products ) ) {
			foreach ( $products as $product ) {
				$product_options[ $product->ID ] = $product->post_title;
			}
		}

		$settings = array(
			array(
				'name' => __( 'Exclude products from automatic 50% discount', 'hashcode-custom-woo' ),
				'type' => 'title',
			),
			array(
				'name'              => __( 'Excluded products list', 'hashcode-custom-woo' ),
				'desc_tip'          => __( 'Please select all products you want to be excluded from automatic 50% discount', 'hashcode-custom-woo' ),
				'id'                => 'hashcode_discount_excluded_products',
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'css'               => 'min-width: 350px;',
				'options'           => $product_options,
				'custom_attributes' => array(
					'placeholder' => __( 'Select products...', 'hashcode-custom-woo' ),
				),
			),
			array(
				'type' => 'sectionend',
			),
		);

		return $settings;
	}
}
