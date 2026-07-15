<?php
/**
 * POS REST API — customer search and wallet transaction history.
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
 * Registers the pos/v1 customer search and wallet history routes.
 */
class Wallet_System_For_Woocommerce_Pos_Customer_Rest_Api {

	const NAMESPACE_V1  = 'pos/v1';
	const RESULT_LIMIT  = 20;

	/**
	 * Registers all customer/wallet lookup routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/customer/search',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'search' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::NAMESPACE_V1,
			'/wallet/history/(?P<user_id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'wallet_history' ),
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
	 * GET /pos/v1/customer/search?query=
	 * Matches by email, username, display name (via WP_User_Query's built-in
	 * search) and by billing phone (via a separate meta query, since phone
	 * isn't a core user-table column). A scanned QR code is expected to
	 * encode one of these — typically the user's email or login.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function search( $request ) {
		$query = trim( (string) $request->get_param( 'query' ) );

		if ( '' === $query ) {
			return new WP_REST_Response( array( 'error' => __( 'A search query is required.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		$found = array();

		$by_field = new WP_User_Query(
			array(
				'search'         => '*' . $query . '*',
				'search_columns' => array( 'user_login', 'user_email', 'display_name' ),
				'number'         => self::RESULT_LIMIT,
			)
		);
		foreach ( $by_field->get_results() as $user ) {
			$found[ $user->ID ] = $user;
		}

		$by_phone = new WP_User_Query(
			array(
				'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => 'billing_phone',
						'value'   => $query,
						'compare' => 'LIKE',
					),
				),
				'number'     => self::RESULT_LIMIT,
			)
		);
		foreach ( $by_phone->get_results() as $user ) {
			$found[ $user->ID ] = $user;
		}

		$customers = array();
		foreach ( array_slice( $found, 0, self::RESULT_LIMIT, true ) as $user ) {
			$customers[] = array(
				'user_id' => $user->ID,
				'name'    => $user->display_name,
				'email'   => $user->user_email,
				'phone'   => get_user_meta( $user->ID, 'billing_phone', true ),
				'balance' => wallet_get_balance( $user->ID ),
			);
		}

		return new WP_REST_Response( array( 'customers' => array_values( $customers ) ), 200 );
	}

	/**
	 * GET /pos/v1/wallet/history/{user_id}
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function wallet_history( $request ) {
		global $wpdb;
		$table   = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
		$user_id = absint( $request['user_id'] );

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, amount, currency, transaction_type, payment_method, transaction_type_1, source, transaction_id AS order_id, note, date
				FROM {$table} WHERE user_id = %d ORDER BY id DESC LIMIT %d",
				$user_id,
				self::RESULT_LIMIT
			)
		);

		return new WP_REST_Response(
			array(
				'user_id'      => $user_id,
				'balance'      => wallet_get_balance( $user_id ),
				'transactions' => $rows,
			),
			200
		);
	}
}
