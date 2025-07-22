<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to show overview content
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

if ( isset( $_POST['wsfw_button_wallet_withdrawal_wbnpl_tab_option'] ) ) {
	$nonce = ( isset( $_POST['updatenoncewallet_bnpl'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_bnpl'] ) ) : '';

	if ( wp_verify_nonce( $nonce ) ) {

		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$wsfw_plugin_admin->wsfw_admis_save_tab_settings_for_bnpl();

	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}

$wsfw_wallet_bnpl_enable_settings      = apply_filters( 'wsfw_wallet_bnpl_notification_settings', array() );



?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
  
	<div class="wps-wsfw-text">
		

	
	<div class="wsfw-secion-daily-visit">
	  <span><b><?php esc_html_e( 'Wallet Buy Now Pay Later Settings', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			$wsfw_wallet_bnpl_enable_settings = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_bnpl_enable_settings );

		if ( ! empty( $wsfw_wallet_bnpl_enable_settings ) ) {
			echo wp_kses_post( $wsfw_wallet_bnpl_enable_settings );
		}


		?>

		<input type="hidden" id="updatenoncewallet_bnpl" name="updatenoncewallet_bnpl" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>