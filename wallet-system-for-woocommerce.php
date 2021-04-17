<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           Wallet_System_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Wallet System for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/wallet-system-for-woocommerce
 * Description:       Wallet System For WooCommerce is the plugin that facilitates WooCommerce store owners to provide e-wallet functionalities.
 * Version:           2.0.0
 * Author:            makewebbetter
 * Author URI:        https://makewebbetter.com/
 * Text Domain:       wallet-system-for-woocommerce
 * Domain Path:       /languages
 *
 * Requires at least: 4.6
 * Tested up to:      4.9.5
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$activated = true;
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$activated = false;
}

if ( $activated ) {
	/**
	 * Define plugin constants.
	 *
	 * @since             1.0.0
	 */
	function define_wallet_system_for_woocommerce_constants() {

		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_VERSION', '2.0.0' );
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH', plugin_dir_path( __FILE__ ) );
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL', plugin_dir_url( __FILE__ ) );
		wallet_system_for_woocommerce_constants( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_SERVER_URL', 'https://makewebbetter.com' );
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
	 */
	function activate_wallet_system_for_woocommerce() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wallet-system-for-woocommerce-activator.php';
		Wallet_System_For_Woocommerce_Activator::wallet_system_for_woocommerce_activate();
		$mwb_wsfw_active_plugin = get_option( 'mwb_all_plugins_active', false );
		if ( is_array( $mwb_wsfw_active_plugin ) && ! empty( $mwb_wsfw_active_plugin ) ) {
			$mwb_wsfw_active_plugin['wallet-system-for-woocommerce'] = array(
				'plugin_name' => __( 'Wallet System for WooCommerce', 'wallet-system-for-woocommerce' ),
				'active' => '1',
			);
		} else {
			$mwb_wsfw_active_plugin = array();
			$mwb_wsfw_active_plugin['wallet-system-for-woocommerce'] = array(
				'plugin_name' => __( 'Wallet System for WooCommerce', 'wallet-system-for-woocommerce' ),
				'active' => '1',
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
			$links_array[] = '<a href="#" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Demo.svg" class="mwb-info-img" alt="Demo image">'.__( 'Demo', 'wallet-system-for-woocommerce' ).'</a>';
			$links_array[] = '<a href="https://docs.makewebbetter.com/wallet-system-for-woocommerce/" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Documentation.svg" class="mwb-info-img" alt="documentation image">'.__( 'Documentation', 'wallet-system-for-woocommerce' ).'</a>';
			$links_array[] = '<a href="https://makewebbetter.com/contact-us/" target="_blank"><img src="' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/Support.svg" class="mwb-info-img" alt="support image">'.__( 'Support', 'wallet-system-for-woocommerce' ).'</a>';
		}
		return $links_array;
	}
	add_filter( 'plugin_row_meta', 'wallet_system_for_woocommerce_custom_settings_at_plugin_tab', 10, 2 );

}  else {
	// To deactivate plugin if woocommerce is not installed.
	add_action( 'admin_init', 'mwb_wsc_plugin_deactivate' );

	/**
	 * Call Admin notices
	 *
	 * @name mwb_wsc_plugin_deactivate()
	 */
	function mwb_wsc_plugin_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ), true );
		unset( $_GET['activate'] );
		add_action( 'admin_notices', 'mwb_wsc_plugin_error_notice' );
	}

	/**
	 * Show warning message if woocommerce is not install
	 *
	 * @name mwb_wsc_plugin_error_notice()
	 */
	function mwb_wsc_plugin_error_notice() {
		?>
		<div class="error notice is-dismissible">
			<p>
				<?php esc_html_e( 'Woocommerce is not activated, Please activate Woocommerce first to install Wallet Payment Gateway for Woocommerce.', 'wallet-system-for-woocommerce' ); ?>
			</p>
		</div>
		<?php
	}
}

