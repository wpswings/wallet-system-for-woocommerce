<?php
/**
 * POS REST API — checkout.
 *
 * Deviates from the original spec's separate cart/add + cart/apply-discount
 * endpoints: the POS frontend accumulates the cart client-side and submits
 * the full item list in one checkout call. There's no cross-device/session
 * resume requirement for the cart, so a server-side cart subsystem would add
 * a table and endpoints without adding real capability. Discounts are not
 * supported yet in this pass — deferred until asked for.
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
 * Registers the pos/v1 checkout route.
 */
class Wallet_System_For_Woocommerce_Pos_Checkout_Rest_Api {

	const NAMESPACE_V1 = 'pos/v1';

	/**
	 * Registers the checkout route.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/checkout',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'checkout' ),
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
	 * POST /pos/v1/checkout
	 * Body: { customer_id, items: [{product_id, quantity}], payment_split: {wallet, cash, card}, register_session_id }
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function checkout( $request ) {
		global $wpdb;

		$customer_id          = absint( $request->get_param( 'customer_id' ) );
		$items                = $request->get_param( 'items' );
		$payment_split        = (array) $request->get_param( 'payment_split' );
		$register_session_id  = absint( $request->get_param( 'register_session_id' ) );

		if ( ! $customer_id || ! get_user_by( 'id', $customer_id ) ) {
			return new WP_REST_Response( array( 'error' => __( 'A valid customer_id is required.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		if ( empty( $items ) || ! is_array( $items ) ) {
			return new WP_REST_Response( array( 'error' => __( 'At least one item is required.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		if ( ! $register_session_id ) {
			return new WP_REST_Response( array( 'error' => __( 'register_session_id is required.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		$sessions_table = $wpdb->prefix . 'wps_wsfw_pos_register_sessions';
		$session        = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$sessions_table} WHERE id = %d", $register_session_id ) );

		if ( ! $session || 'open' !== $session->status ) {
			return new WP_REST_Response( array( 'error' => __( 'This register session is not open.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		$wallet_amount = isset( $payment_split['wallet'] ) ? floatval( $payment_split['wallet'] ) : 0;
		$cash_amount   = isset( $payment_split['cash'] ) ? floatval( $payment_split['cash'] ) : 0;
		$card_amount   = isset( $payment_split['card'] ) ? floatval( $payment_split['card'] ) : 0;

		if ( $wallet_amount < 0 || $cash_amount < 0 || $card_amount < 0 ) {
			return new WP_REST_Response( array( 'error' => __( 'Payment amounts cannot be negative.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		if ( $wallet_amount > 0 && wallet_get_balance( $customer_id ) < $wallet_amount ) {
			return new WP_REST_Response( array( 'error' => __( 'Insufficient wallet balance.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		$order = wc_create_order( array( 'customer_id' => $customer_id ) );

		if ( is_wp_error( $order ) ) {
			return new WP_REST_Response( array( 'error' => $order->get_error_message() ), 500 );
		}

		foreach ( $items as $item ) {
			$product_id = isset( $item['product_id'] ) ? absint( $item['product_id'] ) : 0;
			$quantity   = isset( $item['quantity'] ) ? max( 1, absint( $item['quantity'] ) ) : 1;
			$product    = $product_id ? wc_get_product( $product_id ) : false;

			if ( ! $product ) {
				$order->delete( true );
				return new WP_REST_Response(
					/* translators: %d: product id */
					array( 'error' => sprintf( __( 'Product %d not found.', 'wallet-system-for-woocommerce' ), $product_id ) ),
					400
				);
			}

			$order->add_product( $product, $quantity );
		}

		$order->set_created_via( 'pos' );
		$order->update_meta_data( '_wsfw_pos_register_session_id', $register_session_id );
		$order->calculate_totals();
		$order->save();

		$order_total = (float) $order->get_total();
		$paid_total  = round( $wallet_amount + $cash_amount + $card_amount, 2 );

		if ( abs( $paid_total - round( $order_total, 2 ) ) > 0.01 ) {
			$order->delete( true );
			return new WP_REST_Response(
				array(
					'error'       => __( 'Payment split does not match the order total.', 'wallet-system-for-woocommerce' ),
					'order_total' => $order_total,
					'paid_total'  => $paid_total,
				),
				400
			);
		}

		if ( $wallet_amount > 0 ) {
			$deduct_result = wallet_pos_deduct( $customer_id, $wallet_amount, $order->get_id(), $register_session_id );

			if ( is_wp_error( $deduct_result ) ) {
				$order->delete( true );
				return new WP_REST_Response( array( 'error' => $deduct_result->get_error_message() ), 400 );
			}
		}

		$order->set_payment_method( 'pos' );
		$order->set_payment_method_title( __( 'POS Sale', 'wallet-system-for-woocommerce' ) );
		$order->update_meta_data(
			'_wsfw_pos_payment_split',
			array(
				'wallet' => $wallet_amount,
				'cash'   => $cash_amount,
				'card'   => $card_amount,
			)
		);
		$order->save();

		$order->update_status( 'completed', __( 'POS sale completed.', 'wallet-system-for-woocommerce' ) );

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$sessions_table}
				SET cash_sales_total = cash_sales_total + %f, wallet_sales_total = wallet_sales_total + %f, card_sales_total = card_sales_total + %f
				WHERE id = %d",
				$cash_amount,
				$wallet_amount,
				$card_amount,
				$register_session_id
			)
		);

		return new WP_REST_Response(
			array(
				'order_id'       => $order->get_id(),
				'order_total'    => $order_total,
				'payment_split'  => array(
					'wallet' => $wallet_amount,
					'cash'   => $cash_amount,
					'card'   => $card_amount,
				),
				'wallet_balance' => wallet_get_balance( $customer_id ),
				'status'         => $order->get_status(),
			),
			200
		);
	}
}
