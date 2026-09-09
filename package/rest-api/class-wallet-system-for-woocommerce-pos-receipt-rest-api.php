<?php
/**
 * POS REST API — receipt.
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
 * Registers the pos/v1 receipt route.
 */
class Wallet_System_For_Woocommerce_Pos_Receipt_Rest_Api {

	const NAMESPACE_V1 = 'pos/v1';

	/**
	 * Registers the receipt route.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/receipt/(?P<order_id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_receipt' ),
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
	 * GET /pos/v1/receipt/{order_id}
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function get_receipt( $request ) {
		$order_id = absint( $request['order_id'] );
		$order    = wc_get_order( $order_id );

		if ( ! $order ) {
			return new WP_REST_Response( array( 'error' => __( 'Order not found.', 'wallet-system-for-woocommerce' ) ), 404 );
		}

		$items = array();
		foreach ( $order->get_items() as $item ) {
			$items[] = array(
				'name'     => $item->get_name(),
				'quantity' => $item->get_quantity(),
				'subtotal' => (float) $item->get_subtotal(),
				'total'    => (float) $item->get_total(),
			);
		}

		$customer_id          = $order->get_customer_id();
		$payment_split        = $order->get_meta( '_wsfw_pos_payment_split', true );
		$register_session_id  = $order->get_meta( '_wsfw_pos_register_session_id', true );
		$date_created         = $order->get_date_created();

		return new WP_REST_Response(
			array(
				'order_id'            => $order->get_id(),
				'order_number'        => $order->get_order_number(),
				'date'                => $date_created ? $date_created->date( 'Y-m-d H:i:s' ) : null,
				'status'              => $order->get_status(),
				'items'               => $items,
				'subtotal'            => (float) $order->get_subtotal(),
				'tax_total'           => (float) $order->get_total_tax(),
				'shipping_total'      => (float) $order->get_shipping_total(),
				'total'               => (float) $order->get_total(),
				'total_refunded'      => (float) $order->get_total_refunded(),
				'currency'            => $order->get_currency(),
				'payment_split'       => $payment_split ? $payment_split : null,
				'register_session_id' => $register_session_id ? absint( $register_session_id ) : null,
				'customer'            => array(
					'user_id' => $customer_id,
					'name'    => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
					'email'   => $order->get_billing_email(),
				),
				'wallet_balance'      => $customer_id ? wallet_get_balance( $customer_id ) : null,
			),
			200
		);
	}
}
