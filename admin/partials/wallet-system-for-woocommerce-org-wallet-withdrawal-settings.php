<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wsfw_wps_wsfw_obj;

if ( isset( $_POST['wsfw_button_wallet_withdrawal_paypal_tab'] ) ) {
	$nonce = ( isset( $_POST['updatenoncewallet_paypal'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_paypal'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Pro_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$wsfw_plugin_admin->wps_wsfw_admin_save_tab_settings_for_wallet_promotions_tab();

	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}

$wsfw_wallet_withdrawal_settings      = apply_filters( 'wsfw_wallet_withrwaral_settings_tab_array', array() );



?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
  
	<div class="wps-wsfw-text">
		

	
	<div class="wsfw-secion-daily-visit">
	  <span><b><?php esc_html_e( 'Wallet Withdrawal Settings', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			$wsfw_wallet_action_promotions_enable_settings      = apply_filters( 'wsfw_wallet_action_withdrawal_settings', array() );

			$wsfw_wallet_action_promotions_enable_settings = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_promotions_enable_settings );

		if ( ! empty( $wsfw_wallet_action_promotions_enable_settings ) ) {
			echo wp_kses_post( $wsfw_wallet_action_promotions_enable_settings );
		}


		?>


	<div class="wsfw-secion-daily-visit">
		<?php
			$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_withdrawal_settings );
		if ( ! empty( $wsfw_wallet_action_html ) ) {
			echo wp_kses_post( $wsfw_wallet_action_html );
		}
		?>
	</div>
		<input type="hidden" id="updatenoncewallet_paypal" name="updatenoncewallet_paypal" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>
