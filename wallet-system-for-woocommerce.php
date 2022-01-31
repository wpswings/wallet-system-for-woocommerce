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
 * Plugin URI:        https://wordpress.org/plugins/wallet-system-for-woocommerce/
 * Description:       Wallet System For WooCommerce is the plugin that facilitates WooCommerce store owners to provide e-wallet functionalities.
 * Version:           2.1.3
 * Author:            WP Swings
 * Author URI:        https://wpswings.com/?utm_source=wpswings-wallet-org&utm_medium=wallet-org-backend&utm_campaign=official
 * Text Domain:       wallet-system-for-woocommerce
 * Domain Path:       /languages
 *
 * WC Requires at least: 4.6
 * WC tested up to: 6.1.0
 * WP Requires at least: 5.1.0
 * WP tested up to: 5.8.3
 * Requires PHP: 7.2 or Higher
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
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

		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_VERSION', '2.1.3' );
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
		$mwb_wsfw_active_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_wsfw_active_plugin ) && ! empty( $mwb_wsfw_active_plugin ) ) {
			$mwb_wsfw_active_plugin['wallet-system-for-woocommerce'] = array(
				'plugin_name' => __( 'Wallet System for WooCommerce', 'wallet-system-for-woocommerce' ),
				'active'      => '1',
			);
		} else {
			$mwb_wsfw_active_plugin = array();
			$mwb_wsfw_active_plugin['wallet-system-for-woocommerce'] = array(
				'plugin_name' => __( 'Wallet System for WooCommerce', 'wallet-system-for-woocommerce' ),
				'active'      => '1',
			);
		}
		update_option( 'mwb_all_plugins_active', $mwb_wsfw_active_plugin );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-wallet-system-for-woocommerce-deactivator.php
	 */
	function deactivate_wallet_system_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wallet-system-for-woocommerce-deactivator.php';
		Wallet_System_For_Woocommerce_Deactivator::wallet_system_for_woocommerce_deactivate();
		$mwb_wsfw_deactive_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_wsfw_deactive_plugin ) && ! empty( $mwb_wsfw_deactive_plugin ) ) {
			foreach ( $mwb_wsfw_deactive_plugin as $mwb_wsfw_deactive_key => $mwb_wsfw_deactive ) {
				if ( 'wallet-system-for-woocommerce' === $mwb_wsfw_deactive_key ) {
					$mwb_wsfw_deactive_plugin[ $mwb_wsfw_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'mwb_all_plugins_active', $mwb_wsfw_deactive_plugin );
	}

	register_activation_hook( __FILE__, 'activate_wallet_system_for_woocommerce' );
	register_deactivation_hook( __FILE__, 'deactivate_wallet_system_for_woocommerce' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-wallet-system-for-woocommerce.php';

	// Upgrade notice.
	add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), 'mwb_wsfw_upgrade_notice', 0, 3 );


	/**
	 * Undocumented function
	 *
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data.
	 * @param string $status Status filter currently applied to the plugin list.
	 * @return void
	 */
	function mwb_wsfw_upgrade_notice( $plugin_file, $plugin_data, $status ) {

		?>

<tr class="plugin-update-tr active notice-warning notice-alt">
	<td colspan="4" class="plugin-update colspanchange">
		<div class="notice notice-success inline update-message notice-alt">
			<div class='wps-notice-title wps-notice-section'>
				<p><strong>IMPORTANT NOTICE:</strong></p>
			</div>
			<div class='wps-notice-content wps-notice-section'>
				<p>From this update <strong>Version 2.1.3</strong> onwards, the plugin and its support will be handled by <strong>WP Swings</strong>.</p><p><strong>WP Swings</strong> is just our improvised and rebranded version with all quality solutions and help being the same, so no worries at your end.
				Please connect with us for all setup, support, and update related queries without hesitation.</p>
			</div>
		</div>
	</td>
</tr>
<style>
	.wps-notice-section > p:before {
		content: none;
	}
</style>

		<?php

	}//end mwb_wsfw_upgrade_notice()

	add_action( 'admin_notices', 'mwb_wsfw_plugin_upgrade_notice', 20 );


	/**
	 * Displays notice to upgrade for Wallet.
	 *
	 * @return void
	 */
	function mwb_wsfw_plugin_upgrade_notice() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'wp-swings_page_wallet_system_for_woocommerce_menu' === $screen->id ) {
			?>
		
		<tr class="plugin-update-tr active notice-warning notice-alt">
	<td colspan="4" class="plugin-update colspanchange">
		<div class="notice notice-success inline update-message notice-alt">
			<div class='wps-notice-title wps-notice-section'>
				<p><strong>IMPORTANT NOTICE:</strong></p>
			</div>
			<div class='wps-notice-content wps-notice-section'>
				<p>From this update <strong>Version 2.1.3</strong> onwards, the plugin and its support will be handled by <strong>WP Swings</strong>.</p><p><strong>WP Swings</strong> is just our improvised and rebranded version with all quality solutions and help being the same, so no worries at your end.
				Please connect with us for all setup, support, and update related queries without hesitation.</p>
			</div>
		</div>
	</td>
</tr>
<style>
	.wps-notice-section > p:before {
		content: none;
	}
</style>
		
			<?php
		}
	}

	/**
	 * Creating table whenever a new blog is created
	 *
	 * @param object $new_site New site object.
	 * @return void
	 */
	function mwb_wsfw_on_create_blog( $new_site ) {
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
	add_action( 'wp_initialize_site', 'mwb_wsfw_on_create_blog', 900 );

	/**
	 * Deleting the table whenever a blog is deleted.
	 *
	 * @param array $tables tables.
	 * @return array
	 */
	function mwb_wsfw_on_delete_blog( $tables ) {
		global $wpdb;
		$tables[] = $wpdb->prefix . 'mwb_wsfw_wallet_transaction';
		return $tables;
	}
	add_filter( 'wpmu_drop_tables', 'mwb_wsfw_on_delete_blog' );

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

		$wsfw_plugin_standard = new Wallet_System_For_Woocommerce();
		$wsfw_plugin_standard->wsfw_run();
		$GLOBALS['wsfw_mwb_wsfw_obj'] = $wsfw_plugin_standard;

	}
	run_wallet_system_for_woocommerce();

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
			$links_array[] = '<a href="https://demo.wpswings.com/wallet-system-for-woocommerce-pro/?utm_source=wpswings-wallet-demo&utm_medium=wallet-org-backend&utm_campaign=wallet-demo" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Demo.svg" class="mwb-info-img" alt="Demo image">' . __( 'Demo', 'wallet-system-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://docs.wpswings.com/wallet-system-for-woocommerce/?utm_source=wpswings-wallet-doc&utm_medium=wallet-org-backend&utm_campaign=wallet-doc" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Documentation.svg" class="mwb-info-img" alt="documentation image">' . __( 'Documentation', 'wallet-system-for-woocommerce' ) . '</a>';
			$links_array[] = '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-wallet-query&utm_medium=wallet-org-backend&utm_campaign=submit-query" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Support.svg" class="mwb-info-img" alt="support image">' . __( 'Support', 'wallet-system-for-woocommerce' ) . '</a>';
		}
		return $links_array;
	}
	add_filter( 'plugin_row_meta', 'wallet_system_for_woocommerce_custom_settings_at_plugin_tab', 10, 2 );

} else {
	// To deactivate plugin if woocommerce is not installed.
	add_action( 'admin_init', 'mwb_wsfw_plugin_deactivate' );

	/**
	 * Call Admin notices
	 *
	 * @name mwb_wsfw_plugin_deactivate()
	 */
	function mwb_wsfw_plugin_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ), true );
		unset( $_GET['activate'] );
		add_action( 'admin_notices', 'mwb_wsfw_plugin_error_notice' );
	}

	/**
	 * Show warning message if woocommerce is not install
	 *
	 * @name mwb_wsfw_plugin_error_notice()
	 */
	function mwb_wsfw_plugin_error_notice() {
		?>
		<div class="error notice is-dismissible">
			<p>
				<?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to install Wallet System For WooCommerce.', 'wallet-system-for-woocommerce' ); ?>
			</p>
		</div>
		<?php
	}
}
