<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wallet_System_For_Woocommerce_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function wallet_system_for_woocommerce_deactivate() {
		$product_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		if ( ! empty( $product_id ) ) {
			delete_option( 'wps_wsfw_rechargeable_product_id' );
			wp_delete_post( $product_id, true );
		}
		$wallet_payment_enable = get_option( 'woocommerce_wps_wcb_wallet_payment_gateway_settings' );
		if ( $wallet_payment_enable ) {
			$wallet_payment_enable['enabled'] = 'no';
			update_option( 'woocommerce_wps_wcb_wallet_payment_gateway_settings', $wallet_payment_enable );
		}
	}

}
