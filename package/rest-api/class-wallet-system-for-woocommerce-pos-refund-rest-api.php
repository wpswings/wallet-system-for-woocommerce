<?php
/**
 * POS REST API — refunds.
 *
 * Every refund creates a real WooCommerce refund record (via
 * wc_create_refund()) regardless of refund_to, so order totals/reports stay
 * accurate. When refund_to is 'wallet' the amount is also credited back via
 * wallet_pos_refund(). If that refund is fully refunding the order, WC's own
 * status transition to 'refunded' will fire the plugin's existing
 * cashback-clawback logic automatically — no extra code needed here.
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
 * Registers the pos/v1 refund route.
 */
class Wallet_System_For_Woocommerce_Pos_Refund_Rest_Api {

	const NAMESPACE_V1 = 'pos/v1';

	/**
	 * Registers the refund route.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/refund',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'refund' ),
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
	 * POST /pos/v1/refund
	 * Body: { order_id, amount, refund_to: wallet|cash|card, register_session_id?, reason? }
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function refund( $request ) {
		$order_id            = absint( $request->get_param( 'order_id' ) );
		$amount              = floatval( $request->get_param( 'amount' ) );
		$refund_to           = sanitize_key( (string) $request->get_param( 'refund_to' ) );
		$register_session_id = absint( $request->get_param( 'register_session_id' ) );
		$reason              = sanitize_text_field( (string) $request->get_param( 'reason' ) );

		if ( ! in_array( $refund_to, array( 'wallet', 'cash', 'card' ), true ) ) {
			return new WP_REST_Response( array( 'error' => __( 'refund_to must be one of wallet, cash, or card.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		if ( $amount <= 0 ) {
			return new WP_REST_Response( array( 'error' => __( 'Refund amount must be greater than zero.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return new WP_REST_Response( array( 'error' => __( 'Order not found.', 'wallet-system-for-woocommerce' ) ), 404 );
		}

		$remaining = $order->get_remaining_refund_amount();
		if ( $amount > $remaining + 0.01 ) {
			return new WP_REST_Response(
				array(
					/* translators: %s: remaining refundable amount */
					'error' => sprintf( __( 'Refund amount exceeds the remaining refundable amount (%s).', 'wallet-system-for-woocommerce' ), $remaining ),
				),
				400
			);
		}

		$customer_id = $order->get_customer_id();

		if ( 'wallet' === $refund_to ) {
			if ( ! $customer_id ) {
				return new WP_REST_Response( array( 'error' => __( 'This order has no registered customer to refund to a wallet.', 'wallet-system-for-woocommerce' ) ), 400 );
			}

			$wallet_result = wallet_pos_refund( $customer_id, $order_id, $amount, $register_session_id ? $register_session_id : null );

			if ( is_wp_error( $wallet_result ) ) {
				return new WP_REST_Response( array( 'error' => $wallet_result->get_error_message() ), 400 );
			}
		}

		$wc_refund = wc_create_refund(
			array(
				'order_id'       => $order_id,
				'amount'         => $amount,
				/* translators: %s: refund_to value (wallet, cash, or card) */
				'reason'         => $reason ? $reason : sprintf( __( 'POS refund to %s', 'wallet-system-for-woocommerce' ), $refund_to ),
				'refund_payment' => false,
			)
		);

		if ( is_wp_error( $wc_refund ) ) {
			return new WP_REST_Response( array( 'error' => $wc_refund->get_error_message() ), 500 );
		}

		return new WP_REST_Response(
			array(
				'order_id'       => $order_id,
				'refund_id'      => $wc_refund->get_id(),
				'amount'         => $amount,
				'refund_to'      => $refund_to,
				'wallet_balance' => $customer_id ? wallet_get_balance( $customer_id ) : null,
				'order_status'   => wc_get_order( $order_id )->get_status(),
			),
			200
		);
	}
}
