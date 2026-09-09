<?php
/**
 * POS REST API — register open/close/session routes.
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
 * Registers the pos/v1 register routes. The cashier for a session is always
 * the authenticated user (never a client-supplied id), so one cashier can't
 * open or close a session as someone else.
 */
class Wallet_System_For_Woocommerce_Pos_Register_Rest_Api {

	const NAMESPACE_V1 = 'pos/v1';

	/**
	 * Registers all register routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/register/open',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'open_register' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::NAMESPACE_V1,
			'/register/close',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'close_register' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::NAMESPACE_V1,
			'/register/session/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_session' ),
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
	 * POST /pos/v1/register/open
	 * Body: { register_id? , register_name?, location?, opening_cash }
	 * Either register_id (existing register) or register_name (found, or
	 * created if it doesn't exist yet) must be supplied.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function open_register( $request ) {
		global $wpdb;
		$registers_table = $wpdb->prefix . 'wps_wsfw_pos_registers';
		$sessions_table  = $wpdb->prefix . 'wps_wsfw_pos_register_sessions';

		$register_id   = absint( $request->get_param( 'register_id' ) );
		$register_name = sanitize_text_field( (string) $request->get_param( 'register_name' ) );
		$location      = sanitize_text_field( (string) $request->get_param( 'location' ) );
		$opening_cash  = floatval( $request->get_param( 'opening_cash' ) );
		$cashier_id    = get_current_user_id();

		if ( ! $register_id && '' === $register_name ) {
			return new WP_REST_Response( array( 'error' => __( 'Provide either register_id or register_name.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		if ( $register_id ) {
			$register = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$registers_table} WHERE id = %d", $register_id ) );
			if ( ! $register ) {
				return new WP_REST_Response( array( 'error' => __( 'Register not found.', 'wallet-system-for-woocommerce' ) ), 404 );
			}
		} else {
			$register = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$registers_table} WHERE register_name = %s", $register_name ) );

			if ( ! $register ) {
				$wpdb->insert(
					$registers_table,
					array(
						'register_name' => $register_name,
						'location'      => $location,
						'status'        => 'closed',
						'opening_cash'  => 0,
					)
				);
				$register_id = $wpdb->insert_id;
				$register    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$registers_table} WHERE id = %d", $register_id ) );
			} else {
				$register_id = (int) $register->id;
			}
		}

		if ( 'open' === $register->status ) {
			// Opening is now called automatically right after login (no manual
			// "Open Register" screen), so re-opening the same register just
			// resumes its existing session instead of erroring — the caller
			// can't know in advance whether a prior session was left open.
			$existing_session = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$sessions_table} WHERE register_id = %d AND status = 'open' ORDER BY id DESC LIMIT 1",
					$register_id
				)
			);

			if ( $existing_session ) {
				return new WP_REST_Response(
					array(
						'session_id'      => (int) $existing_session->id,
						'register_id'     => $register_id,
						'register_name'   => $register->register_name,
						'cashier_id'      => (int) $existing_session->cashier_id,
						'opening_balance' => floatval( $existing_session->opening_balance ),
						'status'          => 'open',
						'opened_at'       => $existing_session->opened_at,
						'resumed'         => true,
					),
					200
				);
			}

			return new WP_REST_Response( array( 'error' => __( 'This register is already open.', 'wallet-system-for-woocommerce' ) ), 409 );
		}

		$now = current_time( 'mysql' );

		$wpdb->insert(
			$sessions_table,
			array(
				'register_id'        => $register_id,
				'cashier_id'         => $cashier_id,
				'opening_balance'    => $opening_cash,
				'cash_sales_total'   => 0,
				'wallet_sales_total' => 0,
				'card_sales_total'   => 0,
				'status'             => 'open',
				'opened_at'          => $now,
			)
		);
		$session_id = $wpdb->insert_id;

		$wpdb->update(
			$registers_table,
			array(
				'status'       => 'open',
				'opened_by'    => $cashier_id,
				'opened_at'    => $now,
				'opening_cash' => $opening_cash,
				'closed_at'    => null,
				'closing_cash' => null,
			),
			array( 'id' => $register_id )
		);

		return new WP_REST_Response(
			array(
				'session_id'      => $session_id,
				'register_id'     => $register_id,
				'register_name'   => $register->register_name,
				'cashier_id'      => $cashier_id,
				'opening_balance' => $opening_cash,
				'status'          => 'open',
				'opened_at'       => $now,
			),
			200
		);
	}

	/**
	 * POST /pos/v1/register/close
	 * Body: { session_id, closing_cash, notes? }
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function close_register( $request ) {
		global $wpdb;
		$sessions_table  = $wpdb->prefix . 'wps_wsfw_pos_register_sessions';
		$registers_table = $wpdb->prefix . 'wps_wsfw_pos_registers';

		$session_id   = absint( $request->get_param( 'session_id' ) );
		$closing_cash = floatval( $request->get_param( 'closing_cash' ) );
		$notes        = sanitize_textarea_field( (string) $request->get_param( 'notes' ) );

		$session = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$sessions_table} WHERE id = %d", $session_id ) );

		if ( ! $session ) {
			return new WP_REST_Response( array( 'error' => __( 'Session not found.', 'wallet-system-for-woocommerce' ) ), 404 );
		}

		if ( 'open' !== $session->status ) {
			return new WP_REST_Response( array( 'error' => __( 'This session is already closed.', 'wallet-system-for-woocommerce' ) ), 409 );
		}

		if ( (int) $session->cashier_id !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			return new WP_REST_Response( array( 'error' => __( 'Only the cashier who opened this session, or an admin, can close it.', 'wallet-system-for-woocommerce' ) ), 403 );
		}

		$now           = current_time( 'mysql' );
		$expected_cash = floatval( $session->opening_balance ) + floatval( $session->cash_sales_total );
		$discrepancy   = $closing_cash - $expected_cash;

		$wpdb->update(
			$sessions_table,
			array(
				'closing_balance' => $closing_cash,
				'notes'           => $notes,
				'status'          => 'closed',
				'closed_at'       => $now,
			),
			array( 'id' => $session_id )
		);

		$wpdb->update(
			$registers_table,
			array(
				'status'       => 'closed',
				'closed_at'    => $now,
				'closing_cash' => $closing_cash,
			),
			array( 'id' => $session->register_id )
		);

		return new WP_REST_Response(
			array(
				'session_id'    => $session_id,
				'expected_cash' => $expected_cash,
				'closing_cash'  => $closing_cash,
				'discrepancy'   => $discrepancy,
				'status'        => 'closed',
				'closed_at'     => $now,
			),
			200
		);
	}

	/**
	 * GET /pos/v1/register/session/{id}
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function get_session( $request ) {
		global $wpdb;
		$sessions_table  = $wpdb->prefix . 'wps_wsfw_pos_register_sessions';
		$registers_table = $wpdb->prefix . 'wps_wsfw_pos_registers';

		$session_id = absint( $request['id'] );

		$session = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT s.*, r.register_name, r.location
				FROM {$sessions_table} s
				LEFT JOIN {$registers_table} r ON r.id = s.register_id
				WHERE s.id = %d",
				$session_id
			)
		);

		if ( ! $session ) {
			return new WP_REST_Response( array( 'error' => __( 'Session not found.', 'wallet-system-for-woocommerce' ) ), 404 );
		}

		$expected_cash = floatval( $session->opening_balance ) + floatval( $session->cash_sales_total );

		return new WP_REST_Response(
			array(
				'session_id'         => (int) $session->id,
				'register_id'        => (int) $session->register_id,
				'register_name'      => $session->register_name,
				'location'           => $session->location,
				'cashier_id'         => (int) $session->cashier_id,
				'opening_balance'    => floatval( $session->opening_balance ),
				'closing_balance'    => null !== $session->closing_balance ? floatval( $session->closing_balance ) : null,
				'cash_sales_total'   => floatval( $session->cash_sales_total ),
				'wallet_sales_total' => floatval( $session->wallet_sales_total ),
				'card_sales_total'   => floatval( $session->card_sales_total ),
				'expected_cash'      => $expected_cash,
				'status'             => $session->status,
				'notes'              => $session->notes,
				'opened_at'          => $session->opened_at,
				'closed_at'          => $session->closed_at,
			),
			200
		);
	}
}
