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

if ( isset( $_POST['wsfw_button_wallet_kyc_tab_option'] ) ) {
	$nonce = ( isset( $_POST['updatenoncewallet_kyc'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_kyc'] ) ) : '';

	if ( wp_verify_nonce( $nonce ) ) {

		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$wsfw_plugin_admin->wsfw_admis_save_tab_settings_for_kyc();

	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}

$wsfw_wallet_kyc_enable_settings      = apply_filters( 'wsfw_wallet_kyc_notification_settings', array() );



?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
  
	<div class="wps-wsfw-text">
		

	
	<div class="wsfw-secion-kyc-outer-settings">
		<div class="wsfw-secion-kyc-title-link">
			<h4><?php esc_html_e( 'Wallet KYC Settings', 'wallet-system-for-woocommerce' ); ?></h4>
			<?php echo wsfw_get_kyc_request( wp_get_current_user() ); ?>
		</div>
		<?php

			$wsfw_wallet_kyc_enable_settings = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_kyc_enable_settings );

		if ( ! empty( $wsfw_wallet_kyc_enable_settings ) ) {
			echo wp_kses_post( $wsfw_wallet_kyc_enable_settings );
		}


		?>

		<input type="hidden" id="updatenoncewallet_kyc" name="updatenoncewallet_kyc" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>

<?php

/**
 * This function is to show user wallet report.
 *
 * @param object $user user.
 * @return string
 */
function wsfw_get_kyc_request( $user ) {
	$wallet_bal = get_user_meta( $user->ID, 'wps_wallet', true );
	$wallet_bal = ! empty( $wallet_bal ) ? $wallet_bal : 0;
	$nonce = wp_create_nonce( 'view_report_' . $user->ID ); // Create nonce.
	$url_report = esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ) . '&wsfw_tab=wallet-system-for-woocommerce-kyc-request&report_userid=' . $user->ID . '&nonce=' . $nonce );

	$data  = '';
	$data .= '<a href="' . $url_report . '" title="View Kyc Request" >View Kyc Request</a>';

	return $data;
}
?>