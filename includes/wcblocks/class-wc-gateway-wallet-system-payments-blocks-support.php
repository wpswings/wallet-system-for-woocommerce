<?php
/**
 * Provide a common view for the plugin
 *
 * This file is used to markup the common aspects of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/common/partials
 */

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Extend the RMA Wallet system
 */
final class WC_Gateway_Wallet_System_Payments_Blocks_Support extends AbstractPaymentMethodType {

	/**
	 * The gateway instance.
	 *
	 * @var Wallet_Credit_Payment_Gateway
	 */
	private $gateway;

	/**
	 * Payment method name/id/slug.
	 *
	 * @var string
	 */
	protected $name = 'wps_wsfw_wallet';


	/**
	 * Extend the RMA Wallet system function
	 *
	 * @return void
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_wallet_gateway_settings', array() );
		$this->gateway  = new Wallet_Credit_Payment_Gateway( false );
	}
	/**
	 * Extend the RMA Wallet system function
	 *
	 * @return boolean
	 */
	public function is_active() {
		return $this->gateway->is_available();
	}

	/**
	 * Extend the RMA Wallet system function
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$script_path       = '/blockassets/js/frontend/wps-wsfw-blocks.js';
		$script_asset_path = WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'blockassets/js/frontend/wps-wsfw-blocks.asset.php';

		$script_asset      = file_exists( $script_asset_path )
		? require $script_asset_path
		: array(
			'dependencies' => array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities', 'wp-i18n' ),
			'version'      => '1.2.0',
		);
		$script_url        = WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . $script_path;

		wp_register_script(
			'wallet-system-payments-blocks',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
		wp_enqueue_script( 'wallet-system-payments-blocks' );

		// wallet instant feature.
		$wsfw_wallet_instant_discount_wallet = get_option( 'wsfw_wallet_instant_discount_wallet' );
		$description = '';
		$is_pro_plugin = false;
		$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
		$wps_wsfw_wallet_instant_discount_description = get_option( 'wps_wsfw_wallet_instant_discount_description' );
		if ( 'on' == $wsfw_wallet_instant_discount_wallet ) {
			if ( $wps_wsfw_wallet_instant_discount_description && $is_pro_plugin ) {
				$description = '( ' . $wps_wsfw_wallet_instant_discount_description . ' )';
			} else {
				$description = '( Enjoy an instant discount when you pay using a wallet. )';
			}
		}
		// wallet instant feature.

		wp_localize_script(
			'wallet-system-payments-blocks',
			'CustomGatewayData',
			array(
				'title'       => __( 'Wallet Payment', 'wallet-system-for-woocommerce' ),
				'description' => $description,
				'supports'    => array_filter( $this->gateway->supports, array( $this->gateway, 'supports' ) ),
			)
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wallet-system-payments-blocks', 'wallet-system-for-woocommerce', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'languages/' );
		}

		return array( 'wallet-system-payments-blocks' );
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return array(
			'title'       => $this->get_setting( 'title' ),
			'description' => $this->get_setting( 'description' ),
			'supports'    => array_filter( $this->gateway->supports, array( $this->gateway, 'supports' ) ),
		);
	}
}
