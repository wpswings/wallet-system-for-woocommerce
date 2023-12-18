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
			'dependencies' => array(),
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
			// 'description' => $this->get_setting( 'description' ),
				'supports'    => array_filter( $this->gateway->supports, array( $this->gateway, 'supports' ) ),
		);
	}
}
