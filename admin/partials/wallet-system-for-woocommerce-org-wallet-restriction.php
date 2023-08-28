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

if ( isset( $_POST['wsfw_button_wallet_restriction'] ) ) {
	$nonce = ( isset( $_POST['updatenoncewallet_restriction'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_restriction'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Pro_Admin( $wsfwp_wps_wsfwp_obj->wsfwp_get_plugin_name(), $wsfwp_wps_wsfwp_obj->wsfwp_get_version() );

		$wsfw_plugin_admin->wsfw_admis_save_tab_settings_for_wallet_restriction();

	} else {
		$wsfwp_wps_wsfwp_obj->wps_wsfwp_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce-pro' ), 'error' );
	}
}
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
	<div class="wsfw-secion-wallet-restrictions">
		<span><b><?php esc_html_e( 'Amount Restriction For Wallet Recharge', 'wallet-system-for-woocommerce-pro' ); ?></b></span>
		<?php
			global $wsfwp_wps_wsfwp_obj;
			$wsfw_wallet_withdrawal_transfer_settings  = apply_filters( 'wsfw_wallet_restriction_recharge_array', array() );
			$wsfw_wallet_action_html =  $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_withdrawal_transfer_settings );
			if ( ! empty( $wsfw_wallet_action_html ) ) {
				echo wp_kses_post( $wsfw_wallet_action_html );
			} 
		 
		?>
		</div>
		<hr>
		<div class="wsfw-secion-wallet-restrictions">
		<span><b><?php esc_html_e( 'Amount Restriction For Wallet Transfer', 'wallet-system-for-woocommerce-pro' ); ?></b></span>
		<?php
			global $wsfwp_wps_wsfwp_obj;
			$wsfw_wallet_withdrawal_transfer_settings  = apply_filters( 'wsfw_wallet_restriction_transfer_array', array() );
			$wsfw_wallet_action_html =  $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_withdrawal_transfer_settings );
			if ( ! empty( $wsfw_wallet_action_html ) ) {
				echo wp_kses_post( $wsfw_wallet_action_html );
			}
		?>
		</div>
		<hr>
		<div class="wsfw-secion-wallet-restrictions">
		<span><b><?php esc_html_e( 'Amount Restriction For Wallet Withdrawal', 'wallet-system-for-woocommerce-pro' ); ?></b></span>
		<?php
			global $wsfwp_wps_wsfwp_obj;
			$wsfw_wallet_withdrawal_restriction_settings  = apply_filters( 'wsfw_wallet_restriction_withdrawal_array', array() );
			$wsfw_wallet_action_html =  $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_withdrawal_restriction_settings );
			if ( ! empty( $wsfw_wallet_action_html ) ) {
				echo wp_kses_post( $wsfw_wallet_action_html );
			}
		?>
		</div>
		<input type="hidden" id="updatenoncewallet_restriction" name="updatenoncewallet_restriction" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
	
</form>
