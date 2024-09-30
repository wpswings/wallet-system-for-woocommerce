<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wpswings.com/
 * @since             1.0.0
 * @package           Wallet_System_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Wallet System For WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/wallet-system-for-woocommerce
 * Description:       <code><strong>Wallet System for WooCommerce</strong></code> is a digital wallet plugin where users can add or delete balances in bulk, give refunds and earn cashback. <a href="https://wpswings.com/woocommerce-plugins/?utm_source=wpswings-wallet-shop&utm_medium=wallet-org-backend&utm_campaign=shop-page" target="_blank"> Elevate your e-commerce store by exploring more on <strong> WP Swings </strong></a>.
 * Version:           2.5.17
 * Author:            WP Swings
 * Author URI:        https://wpswings.com/?utm_source=wpswings-wallet-official&utm_medium=wallet-org-backend&utm_campaign=official
 * Text Domain:       wallet-system-for-woocommerce
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 * WC Requires at least: 5.5.0
 * WC tested up to: 9.3.3
 * WP Requires at least: 5.5.0
 * WP tested up to: 6.6.2
 * Requires PHP: 7.2.24
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
include_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'woocommerce-wallet-system/woocommerce-wallet-system.php' ) ) {
	$plug = get_plugins();
	if ( isset( $plug['woocommerce-wallet-system/woocommerce-wallet-system.php'] ) ) {
		if ( $plug['woocommerce-wallet-system/woocommerce-wallet-system.php']['Version'] < '1.0.5' ) {
			unset( $_GET['activate'] );
			deactivate_plugins( plugin_basename( 'woocommerce-wallet-system/woocommerce-wallet-system.php' ) );
		}
	}
}

$active_plugins = (array) get_option( 'active_plugins', array() );
if ( is_multisite() ) {
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
}
$activated = true;
if ( ! ( array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) || in_array( 'woocommerce/woocommerce.php', $active_plugins ) ) ) {
	$activated = false;
}
if ( $activated ) {
	/**
	 * Define plugin constants.
	 *
	 * @since             1.0.0
	 */
	function define_wallet_system_for_woocommerce_constants() {

		$wp_upload = wp_upload_dir();
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_UPLOAD_DIR', $wp_upload['basedir'] );
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_VERSION', '2.5.17' );
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL', plugin_dir_url( __FILE__ ) );
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_SERVER_URL', 'https://wpswings.com' );
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_ITEM_REFERENCE', 'Wallet System for WooCommerce' );
	}

	/**
	 * Callable function for defining plugin constants.
	 *
	 * @param   String $key    Key for contant.
	 * @param   String $value   value for contant.
	 * @since             1.0.0
	 */
	function wallet_system_for_woocommerce_constants( $key, $value ) {

		if ( ! defined( $key ) ) {

			define( $key, $value );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-wallet-system-for-woocommerce-activator.php
	 *
	 * @param boolean $network_wide networkwide activate.
	 * @return void
	 */
	function activate_wallet_system_for_woocommerce( $network_wide ) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wallet-system-for-woocommerce-activator.php';
		Wallet_System_For_Woocommerce_Activator::wallet_system_for_woocommerce_activate( $network_wide );
		$wps_wsfw_active_plugin = get_option( 'wps_all_plugins_active', false );
		if ( is_array( $wps_wsfw_active_plugin ) && ! empty( $wps_wsfw_active_plugin ) ) {
			$wps_wsfw_active_plugin['wallet-system-for-woocommerce'] = array(
				'plugin_name' => __( 'Wallet System for WooCommerce', 'wallet-system-for-woocommerce' ),
				'active'      => '1',
			);
		} else {
			$wps_wsfw_active_plugin = array();
			$wps_wsfw_active_plugin['wallet-system-for-woocommerce'] = array(
				'plugin_name' => __( 'Wallet System for WooCommerce', 'wallet-system-for-woocommerce' ),
				'active'      => '1',
			);
		}
		update_option( 'wps_all_plugins_active', $wps_wsfw_active_plugin );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-wallet-system-for-woocommerce-deactivator.php
	 */
	function deactivate_wallet_system_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wallet-system-for-woocommerce-deactivator.php';
		Wallet_System_For_Woocommerce_Deactivator::wallet_system_for_woocommerce_deactivate();
		$wps_wsfw_deactive_plugin = get_option( 'wps_all_plugins_active', false );
		if ( is_array( $wps_wsfw_deactive_plugin ) && ! empty( $wps_wsfw_deactive_plugin ) ) {
			foreach ( $wps_wsfw_deactive_plugin as $wps_wsfw_deactive_key => $wps_wsfw_deactive ) {
				if ( 'wallet-system-for-woocommerce' === $wps_wsfw_deactive_key ) {
					$wps_wsfw_deactive_plugin[ $wps_wsfw_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'wps_all_plugins_active', $wps_wsfw_deactive_plugin );
		wp_clear_scheduled_hook( 'wps_wgm_check_for_notification_update' );
	}

	register_activation_hook( __FILE__, 'activate_wallet_system_for_woocommerce' );
	register_deactivation_hook( __FILE__, 'deactivate_wallet_system_for_woocommerce' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-wallet-system-for-woocommerce.php';

	add_action( 'admin_notices', 'wps_wsfw_show_deactivation_notice_for_pro', 20 );

	/**
	 * This function is used to show deactivation notice.
	 *
	 * @return void
	 */
	function wps_wsfw_show_deactivation_notice_for_pro() {

		$plug = get_plugins();
		if ( isset( $plug['woocommerce-wallet-system/woocommerce-wallet-system.php'] ) ) {
			?>
			<div class="notice notice-error is-dismissible">
					<p><strong><?php esc_html_e( 'The WooCommerce Wallet System ', 'wallet-system-for-woocommerce' ); ?></strong><?php esc_html_e( 'has been renamed as ', 'wallet-system-for-woocommerce' ); ?><strong><?php esc_html_e( 'Wallet System for WooCommerce Pro', 'wallet-system-for-woocommerce' ); ?></strong><?php esc_html_e( ". Please update the plugin's latest version ", 'wallet-system-for-woocommerce' ); ?><strong><?php esc_html_e( '1.0.5.', 'wallet-system-for-woocommerce' ); ?></strong></p>
				</div>
			<?php
		}
	}

	/**
	 * Creating table whenever a new blog is created
	 *
	 * @param object $new_site New site object.
	 * @return void
	 */
	function wps_wsfw_on_create_blog( $new_site ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		if ( is_plugin_active_for_network( 'wallet-system-for-woocommerce/wallet-system-for-woocommerce.php' ) ) {
			$blog_id = $new_site->blog_id;
			switch_to_blog( $blog_id );
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-wallet-system-for-woocommerce-activator.php';
			Wallet_System_For_Woocommerce_Activator::create_table_and_product();
			restore_current_blog();
		}
	}
	add_action( 'wp_initialize_site', 'wps_wsfw_on_create_blog', 900 );

	/**
	 * Deleting the table whenever a blog is deleted.
	 *
	 * @param array $tables tables.
	 * @return array
	 */
	function wps_wsfw_on_delete_blog( $tables ) {
		global $wpdb;
		$tables[] = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
		return $tables;
	}
	add_filter( 'wpmu_drop_tables', 'wps_wsfw_on_delete_blog' );


	/**
	 * This function is used to escpe html.
	 *
	 * @return array
	 */
	function wps_wsfw_lite_allowed_html() {
		// Return the complete html elements defined by us.
		$allowed_html = array(

			'path' => array(
				'd'               => array(),
				'stroke'          => array(),
				'stroke-width'    => array(),
				'stroke-linecap'  => array(),
				'stroke-linejoin' => array(),
			),

			'circle' => array(
				'cx'           => array(),
				'cy'           => array(),
				'r'            => array(),
				'stroke'       => array(),
				'stroke-width' => array(),
			),
		);
		return $allowed_html;
	}

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_wallet_system_for_woocommerce() {
		define_wallet_system_for_woocommerce_constants();

		$wsfw_wsfw_plugin_standard = new Wallet_System_For_Woocommerce();
		$wsfw_wsfw_plugin_standard->wsfw_run();
		$GLOBALS['wsfw_wps_wsfw_obj'] = $wsfw_wsfw_plugin_standard;
	}
	run_wallet_system_for_woocommerce();


	add_action( 'admin_enqueue_scripts', 'wps_wsfw_admin_enqueue_styles' );
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @name mfw_admin_enqueue_styles.
	 */
	function wps_wsfw_admin_enqueue_styles() {
		$screen = get_current_screen();

		if ( isset( $screen->id ) || isset( $screen->post_type ) ) {

			$screen = get_current_screen();
			if ( isset( $screen->id ) && 'plugins' == $screen->id ) {
				wp_enqueue_style( 'wallet-system-for-woocommerce-admin-global', plugin_dir_url( __FILE__ ) . '/admin/src/scss/wallet-system-for-woocommerce-go-pro.css', array(), time(), 'all' );

			}
		}
	}

	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wallet_system_for_woocommerce_settings_link' );

	/**
	 * Settings link.
	 *
	 * @since    1.0.0
	 * @param   Array $links    Settings link array.
	 */
	function wallet_system_for_woocommerce_settings_link( $links ) {

		$my_link = array(
			'<a href="' . admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ) . '">' . __( 'Settings', 'wallet-system-for-woocommerce' ) . '</a>',
		);
		$mfw_plugins = get_plugins();
		if ( ! isset( $mfw_plugins['wallet-system-for-woocommerce-pro/wallet-system-for-woocommerce-pro.php'] ) ) {

			$my_link['goPro'] = '<a class="wps-wsfw-go-pro" target="_blank" href="https://wpswings.com/product/wallet-system-for-woocommerce-pro/?utm_source=wpswings-wallet-pro&utm_medium=wallet-org-backend&utm_campaign=go-pro">' . esc_html__( 'GO PRO', 'wallet-system-for-woocommerce' ) . '</a>';
		}
		return array_merge( $my_link, $links );
	}

	/**
	 * Adding custom setting links at the plugin activation list.
	 *
	 * @param array  $links_array array containing the links to plugin.
	 * @param string $plugin_file_name plugin file name.
	 * @return array
	 */
	function wallet_system_for_woocommerce_custom_settings_at_plugin_tab( $links_array, $plugin_file_name ) {
		if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
			$links_array[] = '<a href="https://demo.wpswings.com/wallet-system-for-woocommerce-pro/?utm_source=wpswings-wallet-demo&utm_medium=wallet-org-backend&utm_campaign=wallet-demo" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Demo.svg" class="wps-info-img" alt="Demo image">' . __( 'Demo', 'wallet-system-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://docs.wpswings.com/wallet-system-for-woocommerce/?utm_source=wpswings-wallet-doc&utm_medium=wallet-org-backend&utm_campaign=wallet-doc" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Documentation.svg" class="wps-info-img" alt="documentation image">' . __( 'Documentation', 'wallet-system-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://www.youtube.com/watch?v=C5mwA5kttRU&feature=youtu.be" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/YouTube.png" class="wps-info-img" alt="video image">' . __( 'Video', 'wallet-system-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-wallet-query&utm_medium=wallet-org-backend&utm_campaign=submit-query" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Support.svg" class="wps-info-img" alt="support image">' . __( 'Support', 'wallet-system-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/woocommerce-services/?utm_source=wpswings-wallet-services&utm_medium=wallet-org-backend&utm_campaign=woocommerce-services" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Services.svg" class="wps-info-img" alt="support image">' . __( 'Services', 'wallet-system-for-woocommerce' ) . '</a>';
		}
		return $links_array;
	}
	add_filter( 'plugin_row_meta', 'wallet_system_for_woocommerce_custom_settings_at_plugin_tab', 10, 2 );

	add_filter( 'woocommerce_data_stores', 'wsfw_admin_woocommerce_data_store_file' );
	/**
	 * This is used to assign wallet order data store.
	 *
	 * @param [type] $data_stores data stores.
	 * @return array
	 */
	function wsfw_admin_woocommerce_data_store_file( $data_stores ) {
		if ( ! empty( $data_stores ) ) {

			require_once plugin_dir_path( __FILE__ ) . 'admin/partials/class-wc-wallet-shop-order-data-store.php';
			return array_merge(
				$data_stores,
				array(
					'wallet_shop_order'    => 'WC_Wallet_Shop_Order_Data_Store',

				)
			);

		}
	}
} else {
	// To deactivate plugin if woocommerce is not installed.
	add_action( 'admin_init', 'wps_wsfw_plugin_deactivate' );

	/**
	 * Call Admin notices
	 *
	 * @name wps_wsfw_plugin_deactivate()
	 */
	function wps_wsfw_plugin_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ), true );
		unset( $_GET['activate'] );
		add_action( 'admin_notices', 'wps_wsfw_plugin_error_notice' );
	}

	/**
	 * Show warning message if woocommerce is not install
	 *
	 * @name wps_wsfw_plugin_error_notice()
	 */
	function wps_wsfw_plugin_error_notice() {
		?>
		<div class="error notice is-dismissible">
			<p>
				<?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to install Wallet System For WooCommerce.', 'wallet-system-for-woocommerce' ); ?>
			</p>
		</div>
		<?php
	}
}



add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);
add_action( 'admin_notices', 'wps_banner_notification_plugin_html' );
if ( ! function_exists( 'wps_banner_notification_plugin_html' ) ) {
	/**
	 * Common Function To show banner image.
	 *
	 * @return void
	 */
	function wps_banner_notification_plugin_html() {

		$screen = get_current_screen();
		if ( isset( $screen->id ) ) {
			$pagescreen = $screen->id;
		}
		if ( ( isset( $pagescreen ) && 'plugins' === $pagescreen ) || ( 'wp-swings_page_home' == $pagescreen ) ) {
			$banner_id = get_option( 'wps_wgm_notify_new_banner_id', false );
			if ( isset( $banner_id ) && '' !== $banner_id ) {
				$hidden_banner_id            = get_option( 'wps_wgm_notify_hide_baneer_notification', false );
				$banner_image = get_option( 'wps_wgm_notify_new_banner_image', '' );
				$banner_url = get_option( 'wps_wgm_notify_new_banner_url', '' );
				if ( isset( $hidden_banner_id ) && $hidden_banner_id < $banner_id ) {

					if ( '' !== $banner_image && '' !== $banner_url ) {

						?>
							<div class="wps-offer-notice notice notice-warning is-dismissible">
								<div class="notice-container">
									<a href="<?php echo esc_url( $banner_url ); ?>" target="_blank"><img src="<?php echo esc_url( $banner_image ); ?>" alt="Wallet cards"/></a>
								</div>
								<button type="button" class="notice-dismiss dismiss_banner" id="dismiss-banner"><span class="screen-reader-text">Dismiss this notice.</span></button>
							</div>
							
						<?php
					}
				}
			}
		}
	}
}
add_action( 'admin_notices', 'wps_wsfw_banner_notification_html' );
/**
 * Function to show banner image based on wallet.
 *
 * @return void
 */
function wps_wsfw_banner_notification_html() {
	$screen = get_current_screen();
	if ( isset( $screen->id ) ) {
		$pagescreen = $screen->id;
	}
	$secure_nonce      = wp_create_nonce( 'wps-wallet-thankyou-order-nonce' );
	$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-thankyou-order-nonce' );
	if ( ! $id_nonce_verified ) {
		wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
	}
	if ( ( isset( $_GET['page'] ) && 'wallet_system_for_woocommerce_menu' === $_GET['page'] ) ) {
		$banner_id = get_option( 'wps_wgm_notify_new_banner_id', false );
		if ( isset( $banner_id ) && '' !== $banner_id ) {
			$hidden_banner_id            = get_option( 'wps_wgm_notify_hide_baneer_notification', false );
			$banner_image = get_option( 'wps_wgm_notify_new_banner_image', '' );
			$banner_url = get_option( 'wps_wgm_notify_new_banner_url', '' );
			if ( isset( $hidden_banner_id ) && $hidden_banner_id < $banner_id ) {

				if ( '' !== $banner_image && '' !== $banner_url ) {

					?>
							<div class="wps-offer-notice notice notice-warning is-dismissible">
								<div class="notice-container">
									<a href="<?php echo esc_url( $banner_url ); ?>"target="_blank"><img src="<?php echo esc_url( $banner_image ); ?>" alt="Wallet cards"/></a>
								</div>
								<button type="button" class="notice-dismiss dismiss_banner" id="dismiss-banner"><span class="screen-reader-text">Dismiss this notice.</span></button>
							</div>
							
						<?php
				}
			}
		}
	}
}
