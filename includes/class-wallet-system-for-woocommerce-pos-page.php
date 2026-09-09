<?php
/**
 * Serves the POS app on a real WordPress page instead of a raw static URL.
 *
 * When POS is enabled from admin, a page (e.g. /wallet-pos/) is created.
 * Visiting that page — or any sub-path under it, so client-side routes like
 * /wallet-pos/wallet-pos-sales survive a refresh — bypasses the theme
 * entirely and injects the built POS app instead. This is the same pattern
 * WooCommerce uses for its "My Account" page and its rewrite endpoints.
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
 * Creates/serves the POS app page.
 */
class Wallet_System_For_Woocommerce_Pos_Page {

	const OPTION_ENABLED = 'wps_wsfw_pos_enabled';
	const OPTION_PAGE_ID = 'wps_wsfw_pos_page_id';
	const PAGE_META_KEY  = '_wsfw_pos_app_page';
	const DEFAULT_SLUG   = 'wallet-pos';

	/**
	 * Whether the POS terminal is currently enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return 'on' === get_option( self::OPTION_ENABLED );
	}

	/**
	 * Enables POS: creates the app page if needed and registers its rewrite
	 * rule so sub-paths under it also resolve here.
	 *
	 * @return void
	 */
	public static function enable() {
		$page_id = self::get_or_create_page();
		update_option( self::OPTION_ENABLED, 'on' );
		update_option( self::OPTION_PAGE_ID, $page_id );
		self::register_rewrite_rule();
		flush_rewrite_rules();
	}

	/**
	 * Disables POS. The page itself is left in place (not deleted) so
	 * re-enabling doesn't create a second page or lose its URL/SEO history.
	 *
	 * @return void
	 */
	public static function disable() {
		update_option( self::OPTION_ENABLED, '' );
		flush_rewrite_rules();
	}

	/**
	 * Registers the wildcard rewrite rule on every request, so it's in
	 * place even after a rewrite flush from an unrelated plugin/event.
	 * Only takes effect while POS is enabled.
	 *
	 * @return void
	 */
	public static function register_rewrite_rule() {
		if ( ! self::is_enabled() ) {
			return;
		}

		$page_id = get_option( self::OPTION_PAGE_ID );
		if ( ! $page_id ) {
			return;
		}

		$slug = get_post_field( 'post_name', $page_id );
		if ( ! $slug ) {
			return;
		}

		add_rewrite_rule( '^' . preg_quote( $slug, '/' ) . '(/.*)?/?$', 'index.php?page_id=' . absint( $page_id ), 'top' );
	}

	/**
	 * Finds or creates the POS app page, returning its id.
	 *
	 * @return int
	 */
	private static function get_or_create_page() {
		$existing_id = get_option( self::OPTION_PAGE_ID );
		if ( $existing_id && 'page' === get_post_type( $existing_id ) ) {
			return (int) $existing_id;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Wallet POS', 'wallet-system-for-woocommerce' ),
				'post_name'    => self::DEFAULT_SLUG,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			)
		);

		update_post_meta( $page_id, self::PAGE_META_KEY, '1' );

		return (int) $page_id;
	}

	/**
	 * Hooked on template_redirect. If the current request matches the POS
	 * page, renders the app shell instead of the theme and stops.
	 *
	 * @return void
	 */
	public static function maybe_render_app() {
		if ( ! self::is_enabled() ) {
			return;
		}

		$page_id = get_option( self::OPTION_PAGE_ID );
		if ( ! $page_id || ! is_page( (int) $page_id ) ) {
			return;
		}

		self::render_app_shell( (int) $page_id );
		exit;
	}

	/**
	 * Outputs a minimal HTML shell loading the built POS app, with no
	 * theme/header/footer — this is a full-screen kiosk app, not a themed
	 * page.
	 *
	 * @param int $page_id The POS page id.
	 * @return void
	 */
	private static function render_app_shell( $page_id ) {
		$dist_url  = WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'pos-app/dist';
		$version   = self::asset_version();
		$permalink = get_permalink( $page_id );
		$basename  = untrailingslashit( (string) wp_parse_url( $permalink, PHP_URL_PATH ) );
		?>
<!doctype html>
<html lang="<?php echo esc_attr( str_replace( '_', '-', get_bloginfo( 'language' ) ) ); ?>">
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	<title><?php echo esc_html__( 'Wallet POS', 'wallet-system-for-woocommerce' ); ?></title>
	<link rel="stylesheet" href="<?php echo esc_url( $dist_url . '/assets/index.css?ver=' . rawurlencode( $version ) ); ?>" />
	<script>
		window.WSFW_POS_BASENAME = <?php echo wp_json_encode( $basename ); ?>;
		window.WSFW_POS_API_BASE = <?php echo wp_json_encode( rest_url( 'pos/v1' ) ); ?>;
	</script>
</head>
<body>
	<div id="root"></div>
	<script type="module" src="<?php echo esc_url( $dist_url . '/assets/index.js?ver=' . rawurlencode( $version ) ); ?>"></script>
</body>
</html>
		<?php
	}

	/**
	 * Cache-busting value for the built assets. Uses the JS bundle's own
	 * mtime rather than the plugin version, since the plugin version is
	 * static across many `npm run build:pos` calls during development —
	 * browsers would otherwise keep serving a stale cached bundle after
	 * every rebuild until the plugin version itself changed.
	 *
	 * @return string
	 */
	private static function asset_version() {
		$js_path = WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'pos-app/dist/assets/index.js';

		return file_exists( $js_path ) ? (string) filemtime( $js_path ) : WALLET_SYSTEM_FOR_WOOCOMMERCE_VERSION;
	}
}
