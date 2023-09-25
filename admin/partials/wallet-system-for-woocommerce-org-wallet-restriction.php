<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce_Pro
 * @subpackage Wallet_System_For_Woocommerce_Pro/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wsfw_wps_wsfw_obj;
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
	<div class="wsfw-secion-wallet-restrictions">
		<span><b><?php esc_html_e( 'Amount Restriction For Wallet Recharge', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			global $wsfwp_wps_wsfwp_obj;
			$wsfw_wallet_withdrawal_transfer_settings  = apply_filters( 'wsfw_wallet_restriction_recharge_array_org', array() );
			$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_withdrawal_transfer_settings );
		if ( ! empty( $wsfw_wallet_action_html ) ) {
			echo wp_kses_post( $wsfw_wallet_action_html );
		}

		?>
		</div>
		<hr>
		<div class="wsfw-secion-wallet-restrictions">
		<span><b><?php esc_html_e( 'Amount Restriction For Wallet Transfer', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			global $wsfwp_wps_wsfwp_obj;
			$wsfw_wallet_withdrawal_transfer_settings  = apply_filters( 'wsfw_wallet_restriction_transfer_array_org', array() );
			$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_withdrawal_transfer_settings );
		if ( ! empty( $wsfw_wallet_action_html ) ) {
			echo wp_kses_post( $wsfw_wallet_action_html );
		}
		?>
		</div>
		<hr>
		<div class="wsfw-secion-wallet-restrictions">
		<span><b><?php esc_html_e( 'Amount Restriction For Wallet Withdrawal', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			global $wsfwp_wps_wsfwp_obj;
			$wsfw_wallet_withdrawal_restriction_settings  = apply_filters( 'wsfw_wallet_restriction_withdrawal_array_org', array() );
			$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_withdrawal_restriction_settings );
		if ( ! empty( $wsfw_wallet_action_html ) ) {
			echo wp_kses_post( $wsfw_wallet_action_html );
		}
		?>
		</div>
		<input type="hidden" id="updatenoncewallet_restriction" name="updatenoncewallet_restriction" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
	
</form>
