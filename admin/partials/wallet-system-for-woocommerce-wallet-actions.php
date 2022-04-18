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
$wsfw_wallet_action_settings = apply_filters( 'wsfw_wallet_action_settings_array', array() );

if ( isset( $_POST['wsfw_button_wallet_action'] ) ) {
	$nonce = ( isset( $_POST['updatenoncewallet_action'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_action'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );
		$wsfw_plugin_admin->wsfw_admis_save_tab_settings_for_wallet_action();
	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
		<?php

		$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_settings );
		echo esc_html( $wsfw_wallet_action_html );
		?>
		<input type="hidden" id="updatenoncewallet_action" name="updatenoncewallet_action" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>

<!-- Trigger/Open The Modal -->
<button id="myBtn">Open Modal</button>

 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
            <p>
                <?php
                    $wsfw_wallet_action_new_registration_settings = apply_filters( 'wsfw_wallet_action_new_registration_settings_array', array() );

                    $wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_new_registration_settings );
		            echo esc_html( $wsfw_wallet_action_html );
                ?>

            </p>
        </div>
       
      </div>
    </div>
  </div>


