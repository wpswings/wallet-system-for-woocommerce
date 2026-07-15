<?php
/**
 * POS staff identity: role/capability registration and PIN-based auth helpers.
 *
 * @link       https://wpswings.com/
 * @since      2.8.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Staff for POS are ordinary wp_users carrying either the pos_cashier role
 * or the access_wallet_pos capability, authenticated at the terminal via a
 * hashed PIN (not their WP password).
 */
class Wallet_System_For_Woocommerce_Pos_Auth {

	const CAPABILITY   = 'access_wallet_pos';
	const ROLE         = 'pos_cashier';
	const MAX_ATTEMPTS = 5;
	const LOCKOUT_TIME = 15 * MINUTE_IN_SECONDS;

	/**
	 * User id resolved by force_application_password_for_pos(), read back by
	 * restore_application_password_user() later in the same request. Not
	 * using WordPress's own $wp_rest_application_password_status global here
	 * since that's only populated by wp_authenticate_application_password(),
	 * which we deliberately bypass (see force_application_password_for_pos).
	 *
	 * @var int|null
	 */
	private static $resolved_user_id = null;

	/**
	 * Registers the POS cashier role and grants POS access to administrators.
	 * Idempotent — safe to call on every request.
	 *
	 * @return void
	 */
	public static function register_role() {
		if ( ! get_role( self::ROLE ) ) {
			add_role(
				self::ROLE,
				__( 'POS Cashier', 'wallet-system-for-woocommerce' ),
				array(
					'read'           => true,
					self::CAPABILITY => true,
				)
			);
		}

		$admin_role = get_role( 'administrator' );
		if ( $admin_role && ! $admin_role->has_cap( self::CAPABILITY ) ) {
			$admin_role->add_cap( self::CAPABILITY );
		}
	}

	/**
	 * Works around a second, more fundamental WordPress core interaction:
	 * wp_validate_application_password() (wp-includes/user.php) has a
	 * "don't authenticate twice" guard — if a logged-in cookie has already
	 * resolved a current user (e.g. an admin's active wp-admin session in
	 * the same browser used to manage the POS Registers page), it skips
	 * validating the Application Password in the Authorization header
	 * entirely and just keeps the cookie's user. That means our staff
	 * member's Basic Auth credentials are silently ignored whenever the
	 * browser also happens to be logged into wp-admin.
	 *
	 * This runs after both the cookie validators and WordPress's own
	 * wp_validate_application_password (both hooked on determine_current_user
	 * at priority 20), so it can force our own Application Password check to
	 * actually run and override the cookie-resolved user — but only for
	 * pos/v1 requests, so this doesn't change auth precedence anywhere else.
	 *
	 * @param int|false $input_user User id already resolved, or false.
	 * @return int|false
	 */
	public static function force_application_password_for_pos( $input_user ) {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( false === strpos( $request_uri, '/pos/v1/' ) ) {
			return $input_user;
		}

		if ( ! isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
			return $input_user;
		}

		// Not using wp_authenticate_application_password() here: it gates on
		// the REST_REQUEST constant, which WordPress defines later in the
		// request lifecycle (inside rest_api_loaded()) than this filter can
		// run — its own source comment admits this check "may happen too
		// early for the constant to be available". We already know this is
		// our API via the URL match above, so we validate directly against
		// WP_Application_Passwords instead of depending on that timing.
		$user = get_user_by( 'login', $_SERVER['PHP_AUTH_USER'] );
		if ( ! $user && is_email( $_SERVER['PHP_AUTH_USER'] ) ) {
			$user = get_user_by( 'email', $_SERVER['PHP_AUTH_USER'] );
		}

		if ( ! $user ) {
			return $input_user;
		}

		$raw_password     = preg_replace( '/[^a-z\d]/i', '', $_SERVER['PHP_AUTH_PW'] );
		$stored_passwords = WP_Application_Passwords::get_user_application_passwords( $user->ID );

		foreach ( $stored_passwords as $item ) {
			if ( WP_Application_Passwords::check_password( $raw_password, $item['password'] ) ) {
				WP_Application_Passwords::record_application_password_usage( $user->ID, $item['uuid'] );
				self::$resolved_user_id = $user->ID;
				return $user->ID;
			}
		}

		return $input_user;
	}

	/**
	 * Works around a WordPress core interaction: if the browser making a
	 * pos/v1 request also carries a valid logged-in cookie (e.g. an admin's
	 * active wp-admin session in the same browser used to manage the POS
	 * Registers page) and the request has no REST nonce — which ours never
	 * do, since staff authenticate via Application Password, not cookies —
	 * WordPress core (wp-includes/rest-api.php, rest_cookie_check_errors())
	 * forces the current user back to anonymous as a CSRF safeguard, even
	 * though the Application Password had already authenticated correctly.
	 * This restores that user, but only for our own pos/v1 routes, so it
	 * doesn't weaken nonce/CSRF protection anywhere else on the site.
	 *
	 * Both this and force_application_password_for_pos() above are needed
	 * together: that one makes sure the Application Password actually gets
	 * validated despite a cookie being present; this one makes sure the
	 * result of that validation survives WordPress's later cookie-nonce
	 * safety check.
	 *
	 * @param mixed $result The authentication error result being filtered.
	 * @return mixed
	 */
	public static function restore_application_password_user( $result ) {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( false === strpos( $request_uri, '/pos/v1/' ) ) {
			return $result;
		}

		if ( null !== self::$resolved_user_id ) {
			wp_set_current_user( self::$resolved_user_id );
		}

		return $result;
	}

	/**
	 * Validates and stores a hashed PIN for a staff user.
	 *
	 * @param int    $user_id WP user id.
	 * @param string $pin     4-6 digit numeric PIN.
	 * @return true|WP_Error
	 */
	public static function set_staff_pin( $user_id, $pin ) {
		if ( ! preg_match( '/^\d{4,6}$/', (string) $pin ) ) {
			return new WP_Error( 'wsfw_pos_invalid_pin', __( 'PIN must be 4 to 6 digits.', 'wallet-system-for-woocommerce' ) );
		}

		update_user_meta( $user_id, 'wps_wsfw_pos_pin_hash', wp_hash_password( $pin ) );
		self::reset_attempts( $user_id );

		return true;
	}

	/**
	 * Verifies a staff PIN, enforcing a lockout after repeated failures.
	 *
	 * @param int    $user_id WP user id.
	 * @param string $pin     PIN entered at the terminal.
	 * @return true|WP_Error
	 */
	public static function verify_staff_pin( $user_id, $pin ) {
		if ( self::is_locked_out( $user_id ) ) {
			return new WP_Error( 'wsfw_pos_locked_out', __( 'Too many incorrect attempts. Try again later.', 'wallet-system-for-woocommerce' ) );
		}

		$stored_hash = get_user_meta( $user_id, 'wps_wsfw_pos_pin_hash', true );

		if ( empty( $stored_hash ) || ! wp_check_password( $pin, $stored_hash, $user_id ) ) {
			self::record_failed_attempt( $user_id );
			return new WP_Error( 'wsfw_pos_invalid_credentials', __( 'Incorrect PIN.', 'wallet-system-for-woocommerce' ) );
		}

		if ( ! user_can( $user_id, self::CAPABILITY ) ) {
			return new WP_Error( 'wsfw_pos_no_access', __( 'This user does not have POS access.', 'wallet-system-for-woocommerce' ) );
		}

		self::reset_attempts( $user_id );

		return true;
	}

	/**
	 * Whether the current request's authenticated user is POS staff (or an
	 * administrator). Shared permission gate for all pos/v1 REST routes
	 * except the public staff/login route.
	 *
	 * @return bool
	 */
	public static function current_user_is_pos_staff() {
		return current_user_can( self::CAPABILITY );
	}

	/**
	 * Renders a POS PIN field on the user profile screen. Admin-managed only.
	 *
	 * @param WP_User $user The user being edited.
	 * @return void
	 */
	public static function render_profile_field( $user ) {
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		wp_nonce_field( 'wsfw_pos_save_pin_' . $user->ID, 'wsfw_pos_pin_nonce' );
		$has_pin = (bool) get_user_meta( $user->ID, 'wps_wsfw_pos_pin_hash', true );
		?>
		<h2><?php esc_html_e( 'POS Access', 'wallet-system-for-woocommerce' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><label for="wsfw_pos_pin"><?php esc_html_e( 'POS PIN', 'wallet-system-for-woocommerce' ); ?></label></th>
				<td>
					<input type="password" name="wsfw_pos_pin" id="wsfw_pos_pin" class="regular-text" autocomplete="new-password" maxlength="6"
						placeholder="<?php echo esc_attr( $has_pin ? __( 'PIN is set — leave blank to keep it', 'wallet-system-for-woocommerce' ) : __( 'Not set', 'wallet-system-for-woocommerce' ) ); ?>" />
					<p class="description"><?php esc_html_e( '4 to 6 digit PIN used to log into the POS terminal. Leave blank to keep the current PIN unchanged.', 'wallet-system-for-woocommerce' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Saves the POS PIN field from the user profile screen.
	 *
	 * @param int $user_id User id being saved.
	 * @return void
	 */
	public static function save_profile_field( $user_id ) {
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		if ( ! isset( $_POST['wsfw_pos_pin_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wsfw_pos_pin_nonce'] ) ), 'wsfw_pos_save_pin_' . $user_id ) ) {
			return;
		}

		if ( empty( $_POST['wsfw_pos_pin'] ) ) {
			return;
		}

		self::set_staff_pin( $user_id, sanitize_text_field( wp_unslash( $_POST['wsfw_pos_pin'] ) ) );
	}

	/**
	 * Builds the transient key used to track failed PIN attempts for a user.
	 *
	 * @param int $user_id WP user id.
	 * @return string
	 */
	private static function attempts_key( $user_id ) {
		return 'wps_wsfw_pos_pin_attempts_' . absint( $user_id );
	}

	/**
	 * Whether a user is currently locked out of PIN login.
	 *
	 * @param int $user_id WP user id.
	 * @return bool
	 */
	private static function is_locked_out( $user_id ) {
		return (int) get_transient( self::attempts_key( $user_id ) ) >= self::MAX_ATTEMPTS;
	}

	/**
	 * Records one failed PIN attempt, extending the lockout window.
	 *
	 * @param int $user_id WP user id.
	 * @return void
	 */
	private static function record_failed_attempt( $user_id ) {
		$key      = self::attempts_key( $user_id );
		$attempts = (int) get_transient( $key );
		set_transient( $key, $attempts + 1, self::LOCKOUT_TIME );
	}

	/**
	 * Clears failed attempt tracking after a successful PIN entry or reset.
	 *
	 * @param int $user_id WP user id.
	 * @return void
	 */
	private static function reset_attempts( $user_id ) {
		delete_transient( self::attempts_key( $user_id ) );
	}
}
