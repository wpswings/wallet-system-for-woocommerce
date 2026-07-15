<?php
/**
 * POS REST API — wallet balance/deduct/refund routes.
 *
 * Requires the access_wallet_pos capability, held by administrators and by
 * staff authenticated via /pos/v1/staff/login (which issues an Application
 * Password used as Basic Auth on these requests).
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
 * Registers the pos/v1 wallet routes.
 */
class Wallet_System_For_Woocommerce_Pos_Rest_Api {

	const NAMESPACE_V1 = 'pos/v1';

	/**
	 * Registers all POS wallet routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/wallet/balance/(?P<user_id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_balance' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
				'args'                => array(
					'user_id' => array(
						'validate_callback' => function ( $value ) {
							return is_numeric( $value );
						},
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE_V1,
			'/wallet/deduct',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'deduct' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::NAMESPACE_V1,
			'/wallet/refund',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'refund' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);
	}

	/**
	 * Permission gate — any authenticated POS staff member or administrator.
	 * Staff authenticate via /pos/v1/staff/login, which issues an
	 * Application Password used as Basic Auth on every request here.
	 *
	 * @return bool
	 */
	public static function permission_check() {
		return Wallet_System_For_Woocommerce_Pos_Auth::current_user_is_pos_staff();
	}

	/**
	 * GET /pos/v1/wallet/balance/{user_id}
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function get_balance( $request ) {
		$user_id = absint( $request['user_id'] );

		return new WP_REST_Response(
			array(
				'user_id' => $user_id,
				'balance' => wallet_get_balance( $user_id ),
			),
			200
		);
	}

	/**
	 * POST /pos/v1/wallet/deduct
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function deduct( $request ) {
		$result = wallet_pos_deduct(
			absint( $request->get_param( 'user_id' ) ),
			floatval( $request->get_param( 'amount' ) ),
			$request->get_param( 'order_id' ),
			$request->get_param( 'register_session_id' )
		);

		return self::respond( $result );
	}

	/**
	 * POST /pos/v1/wallet/refund
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function refund( $request ) {
		$result = wallet_pos_refund(
			absint( $request->get_param( 'user_id' ) ),
			$request->get_param( 'order_id' ),
			floatval( $request->get_param( 'amount' ) ),
			$request->get_param( 'register_session_id' )
		);

		return self::respond( $result );
	}

	/**
	 * Converts a wallet_pos_* result into a REST response.
	 *
	 * @param array|WP_Error $result Function result.
	 * @return WP_REST_Response
	 */
	private static function respond( $result ) {
		if ( is_wp_error( $result ) ) {
			return new WP_REST_Response(
				array( 'error' => $result->get_error_message() ),
				400
			);
		}

		return new WP_REST_Response( $result, 200 );
	}
}
