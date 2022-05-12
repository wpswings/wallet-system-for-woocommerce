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

if ( isset( $_POST['wsfw_button_cashback'] ) ) {
	$nonce = ( isset( $_POST['updatenoncecashback'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncecashback'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );
		$wsfw_plugin_admin->wsfw_admis_save_tab_settings_for_cashback();
	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}
$wsfw_cashback_settings = apply_filters( 'wsfw_cashback_settings_array', array() );
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
		<?php
		$wsfw_cashback_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_cashback_settings );
		echo esc_html( $wsfw_cashback_html );
		?>
		<input type="hidden" id="updatenoncecashback" name="updatenoncecashback" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>
