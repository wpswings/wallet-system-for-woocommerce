<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Wallet_System_For_Woocommerce_Wcmp' ) ) {
	/**
	 * Class to show wallet as payment gateway.
	 */
	class Wallet_System_For_Woocommerce_WCMp {
		/**
		 * Constructor for class.
		 */
		public function __construct() {
			add_filter( 'automatic_payment_method', array( $this, 'wps_wsfw_add_wallet_payment_method' ) );
			add_filter( 'wcmp_vendor_payment_mode', array( $this, 'wps_wsfw_add_vendor_payment_mode' ) );
			add_filter( 'wcmp_payment_gateways', array( $this, 'wps_wsfw_add_wallet_payment_gateway' ) );
		}

		/**
		 * Show wallet as payment method in payment setting page.
		 *
		 * @since 1.0.0
		 * @param  array $payment_methods payment methods.
		 * @return array
		 */
		public function wps_wsfw_add_wallet_payment_method( $payment_methods ) {
			if ( is_array( $payment_methods ) ) {
				$payment_methods['wps_wallet'] = __( 'Wallet', 'wallet-system-for-woocommerce' );
			}
			return $payment_methods;
		}

		/**
		 * Show wallet as payment method in vendor page if enabled.
		 *
		 * @since 1.0.0
		 * @param  array $vendor_payment_methods vendor payment methods.
		 * @return array
		 */
		public function wps_wsfw_add_vendor_payment_mode( $vendor_payment_methods ) {
			if ( is_array( $vendor_payment_methods ) ) {
				$payment_admin_settings = get_option( 'wcmp_payment_settings_name' );
				if ( isset( $payment_admin_settings['payment_method_wps_wallet'] ) && 'Enable' === $payment_admin_settings['payment_method_wps_wallet'] ) {
					$vendor_payment_methods['wps_wallet'] = __( 'Wallet', 'wallet-system-for-woocommerce' );
				}
			}
			return $vendor_payment_methods;
		}

		/**
		 * Add wallet as payment gatway for wc marketplace.
		 *
		 * @since 1.0.0
		 * @param  array $load_gateways payment gateways.
		 * @return array
		 */
		public function wps_wsfw_add_wallet_payment_gateway( $load_gateways ) {
			if ( is_array( $load_gateways ) ) {
				$load_gateways[] = 'WCMp_Gateway_Wps_Wallet';
			}
			return $load_gateways;
		}

	}
}
$wsfw_wcmp = new Wallet_System_For_Woocommerce_WCMp();
