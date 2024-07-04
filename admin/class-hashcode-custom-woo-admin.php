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

		$cart_items = $cart->get_cart();

		if ( empty( $cart_items ) ) {
			return;
		}

		foreach ( $cart_items as $cart_item_key => $cart_item ) {

			$cart_product_id = (int) $cart_item['product_id'];

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
}
