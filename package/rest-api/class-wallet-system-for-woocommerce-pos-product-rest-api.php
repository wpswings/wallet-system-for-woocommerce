<?php
/**
 * POS REST API — product search for the sales screen.
 *
 * @link       https://wpswings.com/
 * @since      2.8.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/package/rest-api
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the pos/v1 product search route.
 */
class Wallet_System_For_Woocommerce_Pos_Product_Rest_Api {

	const NAMESPACE_V1 = 'pos/v1';
	const RESULT_LIMIT = 20;

	/**
	 * Registers the product search route.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/products/search',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'search' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);
	}

	/**
	 * Permission gate — any authenticated POS staff member or administrator.
	 *
	 * @return bool
	 */
	public static function permission_check() {
		return Wallet_System_For_Woocommerce_Pos_Auth::current_user_is_pos_staff();
	}

	/**
	 * GET /pos/v1/products/search?query=
	 * Matches by name or SKU (WooCommerce's own product search).
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function search( $request ) {
		$query = trim( (string) $request->get_param( 'query' ) );

		$args = array(
			'status' => 'publish',
			'limit'  => self::RESULT_LIMIT,
		);

		if ( '' !== $query ) {
			$args['s'] = $query;
		}

		$products = wc_get_products( $args );
		$results  = array();

		foreach ( $products as $product ) {
			$results[] = array(
				'product_id'   => $product->get_id(),
				'name'         => $product->get_name(),
				'sku'          => $product->get_sku(),
				// Tax-inclusive: get_price() returns whatever the admin typed,
				// which is pre-tax under "prices entered exclusive of tax" —
				// the POS cart/payment screen needs the real customer-facing
				// price so its total matches what checkout's calculate_totals()
				// requires, or every taxed sale fails the payment-split check.
				'price'        => (float) wc_get_price_including_tax( $product ),
				'stock_status' => $product->get_stock_status(),
				'image'        => wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ),
			);
		}

		return new WP_REST_Response( array( 'products' => $results ), 200 );
	}
}
