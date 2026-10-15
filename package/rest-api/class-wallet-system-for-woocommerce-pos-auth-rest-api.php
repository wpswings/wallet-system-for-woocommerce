<?php
/**
 * POS REST API — staff PIN login.
 *
 * On successful PIN verification this issues a real WP Application Password
 * for the staff user, which the POS frontend then uses as Basic Auth for
 * every other pos/v1 request — no custom token system needed.
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
 * Registers the pos/v1 staff login route.
 */
class Wallet_System_For_Woocommerce_Pos_Auth_Rest_Api {

	const NAMESPACE_V1 = 'pos/v1';

	/**
	 * Registers the staff login route.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE_V1,
			'/staff/login',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'login' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * POST /pos/v1/staff/login
	 * Body: { login, pin, terminal_id? }
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public static function login( $request ) {
		$login       = sanitize_text_field( (string) $request->get_param( 'login' ) );
		$pin         = (string) $request->get_param( 'pin' );
		$terminal_id = sanitize_text_field( (string) $request->get_param( 'terminal_id' ) );

		if ( '' === $login || '' === $pin ) {
			return new WP_REST_Response( array( 'error' => __( 'Login and PIN are required.', 'wallet-system-for-woocommerce' ) ), 400 );
		}

		$user = get_user_by( 'login', $login );
		if ( ! $user ) {
			$user = get_user_by( 'email', $login );
		}

		if ( ! $user ) {
			return new WP_REST_Response( array( 'error' => __( 'Incorrect PIN.', 'wallet-system-for-woocommerce' ) ), 401 );
		}

		$verified = Wallet_System_For_Woocommerce_Pos_Auth::verify_staff_pin( $user->ID, $pin );

		if ( is_wp_error( $verified ) ) {
			return new WP_REST_Response( array( 'error' => $verified->get_error_message() ), 401 );
		}

		// Every login mints a new Application Password and the app never revokes
		// one on logout, so retire this user's POS-issued passwords that are
		// more than a day old instead of letting them stay valid forever.
		foreach ( WP_Application_Passwords::get_user_application_passwords( $user->ID ) as $item ) {
			if ( 0 === strpos( (string) $item['name'], 'POS terminal ' ) && ! empty( $item['created'] ) && ( time() - (int) $item['created'] ) > DAY_IN_SECONDS ) {
				WP_Application_Passwords::delete_application_password( $user->ID, $item['uuid'] );
			}
		}

		$app_password_name = sprintf(
			'POS terminal %s (%s)',
			$terminal_id ? $terminal_id : 'unknown',
			current_time( 'mysql' )
		);

		$created = WP_Application_Passwords::create_new_application_password( $user->ID, array( 'name' => $app_password_name ) );

		if ( is_wp_error( $created ) ) {
			return new WP_REST_Response( array( 'error' => $created->get_error_message() ), 500 );
		}

		list( $new_password, $new_item ) = $created;

		return new WP_REST_Response(
			array(
				'user_id'         => $user->ID,
				'username'        => $user->user_login,
				'password'        => $new_password,
				'uuid'            => $new_item['uuid'],
				// get_woocommerce_currency_symbol() returns HTML-entity-encoded
				// symbols (e.g. &#36;) meant for markup; decode to a plain
				// character since this is rendered as plain text in the app.
				'currency_symbol' => html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ),
			),
			200
		);
	}
}
