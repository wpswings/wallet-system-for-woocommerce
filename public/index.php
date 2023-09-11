<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://wpswings.com/product/wallet-system-for-woocommerce-pro/
 * @since             1.0.0
 * @package           Wsfwa
 *
 * @wordpress-plugin
 * Plugin Name:       Wallet System For WooCommerce Addon
 * Plugin URI:        https://https://wpswings.com/product/wallet-system-for-woocommerce-pro/
 * Description:       Addon Plugin For Your Custom Work on Wallet System For WooCommerce Plugin
 * Version:           1.0.0
 * Author:            wpswings
 * Author URI:        https://https://wpswings.com/product/wallet-system-for-woocommerce-pro/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wsfwa
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WSFWA_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wsfwa-activator.php
 */
function activate_wsfwa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wsfwa-activator.php';
	Wsfwa_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wsfwa-deactivator.php
 */
function deactivate_wsfwa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wsfwa-deactivator.php';
	Wsfwa_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wsfwa' );
register_deactivation_hook( __FILE__, 'deactivate_wsfwa' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wsfwa.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wsfwa() {

	$plugin = new Wsfwa();
	$plugin->run();

}
run_wsfwa();

add_filter('wsfwp_wallet_action_settings_user_currency_array', 'wps_wsfws_user_currency_extra_setting_page' , 10);

function wps_wsfws_user_currency_extra_setting_page(){
	$wsfw_settings_template = array(

		array(
			'title'       => __( 'Select which type of setting work for Wallet user currency', 'wallet-system-for-woocommerce-pro' ),
			'type'        => 'radio',
			'description' => __( 'This is switch field demo follow same structure for further use.', 'wallet-system-for-woocommerce-pro' ),
			'name'        => 'wps_wsfwp_wallet_user_currency_setting',
			'id'          => 'wps_wsfwp_wallet_user_currency_setting',
			'value'       => get_option( 'wps_wsfwp_wallet_user_currency_setting' ),
			'class'       => 'wsfw-radio-switch-class',
			'options'     => array(
				'yes' => __( 'Work For User latest orderwise For Currency', 'wallet-system-for-woocommerce-pro' ),
				'no'  => __( 'Work For User Geolocation For Currency', 'wallet-system-for-woocommerce-pro' ),
			),
		),
	);

	$wsfw_settings_template   = apply_filters( 'wsfwp_wallet_action_auto_withdrawal_settings_array', $wsfw_settings_template );
	return $wsfw_settings_template;
}


//custom.
add_action( 'woocommerce_checkout_order_processed', 'wps_wsfwp_process_checkout_order_currency', 99, 2 );

function wps_wsfwp_process_checkout_order_currency( $order_id, $posted_data ){

	$wps_wsfwp_wallet_user_currency_setting = get_option( 'wps_wsfwp_wallet_user_currency_setting' );
	
	if ( 'yes' == $wps_wsfwp_wallet_user_currency_setting ){

		$order = wc_get_order( $order_id );
		$user_id = $order->get_user_id();
		$currency = '';

		$currency      = $order->get_currency();
		update_user_meta( $user_id, 'wps_wallet_last_order_currency', $currency );
			 
	}

}
add_action( 'woocommerce_checkout_order_processed', 'wps_wsfwp_process_checkout_order_currency_on_geolocation', 99, 2 );

function wps_wsfwp_process_checkout_order_currency_on_geolocation( $order_id, $posted_data ){

	$wps_wsfwp_wallet_user_currency_setting = get_option( 'wps_wsfwp_wallet_user_currency_setting' );

	if ( 'no' == $wps_wsfwp_wallet_user_currency_setting ){

			$ipAddress = '';
			if (! empty($_SERVER['HTTP_CLIENT_IP']) && $this->isValidIpAddress($_SERVER['HTTP_CLIENT_IP'])) {
				// check for shared ISP IP
				$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
			} else if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				// check for IPs passing through proxy servers
				// check if multiple IP addresses are set and take the first one
				$ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				foreach ($ipAddressList as $ip) {
					if ($this->isValidIpAddress($ip)) {
						$ipAddress = $ip;
						break;
					}
				}
			} else if (! empty($_SERVER['HTTP_X_FORWARDED']) && $this->isValidIpAddress($_SERVER['HTTP_X_FORWARDED'])) {
				$ipAddress = $_SERVER['HTTP_X_FORWARDED'];
			} else if (! empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->isValidIpAddress($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
				$ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
			} else if (! empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->isValidIpAddress($_SERVER['HTTP_FORWARDED_FOR'])) {
				$ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
			} else if (! empty($_SERVER['HTTP_FORWARDED']) && $this->isValidIpAddress($_SERVER['HTTP_FORWARDED'])) {
				$ipAddress = $_SERVER['HTTP_FORWARDED'];
			} else if (! empty($_SERVER['REMOTE_ADDR']) && $this->isValidIpAddress($_SERVER['REMOTE_ADDR'])) {
				$ipAddress = $_SERVER['REMOTE_ADDR'];
			}
	
		$code = getLocation( $ipAddress );
		$country_code = $code['currency_code'];

		$order = wc_get_order( $order_id );
		$user_id = $order->get_user_id();
		if( !empty( $country_code ) ){

			update_user_meta( $user_id, 'wps_wallet_order_geolocation_currency', $country_code );
		}

	}

}
function isValidIpAddress($ip)
{
	if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
		return false;
	}
	return true;
}

function getLocation($ip)
{
	$ch = curl_init('http://ipwhois.app/json/' . $ip);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$json = curl_exec($ch);
	curl_close($ch);
	// Decode JSON response
	$ipWhoIsResponse = json_decode($json, true);
	// Country code output, field "country_code"
	return $ipWhoIsResponse;
}

add_filter( 'wps_check_order_currency_custom_work', 'wps_check_order_currency_custom_work_callback' ,10, 1 );
function wps_check_order_currency_custom_work_callback( $valid ){

	$valid = false;
	return $valid

}
//custom.