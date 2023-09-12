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

if ( isset( $_POST['wsfw_button_wallet_promotions_tab'] ) ) {
	$nonce = ( isset( $_POST['updatenoncewallet_action'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_action'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Pro_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$wsfw_plugin_admin->wps_wsfw_admin_save_tab_settings_for_wallet_promotions_tab();

	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}

$wsfw_wallet_action_comment_settings      = apply_filters( 'wsfw_wallet_action_settings_promotions_tab_array', array() );

$wps_wallet_bookie_array = get_option( 'wallet_promotions_data_title' );

$wps_wallet_bookie_cashback_array = get_option( 'wallet_promotions_data_content' );

if ( ! empty( $wps_wallet_bookie_array ) && is_array( $wps_wallet_bookie_array ) ) {
	if ( '' == $wps_wallet_bookie_array[0] ) {
		$wps_wallet_bookie_array = array();
	}
} else {
	$wps_wallet_bookie_array = array();
}
if ( ! empty( $wps_wallet_bookie_cashback_array ) && is_array( $wps_wallet_bookie_cashback_array ) ) {
	if ( '' == $wps_wallet_bookie_cashback_array[0] ) {
		$wps_wallet_bookie_cashback_array = array();
	}
} else {
	$wps_wallet_bookie_cashback_array = array();
}



?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
  
	<div class="wps-wsfw-text">
	<div class="wsfw-secion-daily-visit">
	  <span><b><?php esc_html_e( 'Wallet Promotions', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			$wsfw_wallet_action_promotions_enable_settings      = apply_filters( 'wsfw_wallet_action_promotions_enable_settings_org', array() );

			$wsfw_wallet_action_promotions_enable_settings = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_promotions_enable_settings );

		if ( ! empty( $wsfw_wallet_action_promotions_enable_settings ) ) {
			echo wp_kses_post( $wsfw_wallet_action_promotions_enable_settings );
		}
		?>
	  <div class="wsfw-secion-table-label-wrap">
		<table border='2' id="wps_wallet_promotions_table" >
			<tr>
				<th><?php esc_html_e( 'Wallet Promotions Title', 'wallet-system-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Wallet Promotions Content', 'wallet-system-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Action', 'wallet-system-for-woocommerce' ); ?></th>
			</tr>
			<tr>
				<td>
					<div class="wps-form-group__control">
						<label class="mdc-text-field mdc-text-field--outlined">
							<span class="mdc-notched-outline">
								<span class="mdc-notched-outline__leading"></span>
								<span class="mdc-notched-outline__notch">
									<!-- dynamic inline style will be added -->
									<span class="mdc-floating-label" id="my-label-id" style=""><?php esc_html_e( 'Enter Wallet Amount', 'wallet-system-for-woocommerce' ); ?></span>	
								</span>
								<span class="mdc-notched-outline__trailing"></span>
							</span>
							<input
							class="mdc-text-field__input wsfw-text-class wps-wallet-text" 
							name="wallet_promotions_data_title[]"
							id="wallet_promotions_data_title[]"
							type="text"
							value="<?php echo esc_attr( '' ); ?>"
							placeholder="<?php echo esc_attr( 'Enter Promotion Title' ); ?>"
							>
						</label><br>
						<div class="mdc-text-field-helper-line">
							<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
						</div>
					</div>
				</td>
				<td>
					<div class="wps-form-group__control">
						<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea mdc-text-field--no-label">
							<span class="mdc-notched-outline">
								<span class="mdc-notched-outline__leading"></span>
								<span class="mdc-notched-outline__trailing"></span>
							</span>
							<span class="mdc-text-field__resizer">
								<textarea class="mdc-text-field__input" name="wallet_promotions_data_content[]" id="wallet_promotions_data_content[]" 	placeholder="<?php echo esc_attr( ' Enter Promotion Data ' ); ?>"
							rows="4" cols="40" aria-label="Label"></textarea>
							</span>
						</label><br>
						<div class="mdc-text-field-helper-line">
							<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
						</div>
					</div>

				</td>
				<td><input type="button" id="wps_wallet_bookie" class="wps_pro_settings" value="PRO"></td>
			</tr>
		</table>
	
	</div>	
	<?php
	include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/wallet-system-for-woocommerce-go-pro-data.php';
	?>
	<input type="hidden" id="updatenoncewallet_action" name="updatenoncewallet_action" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>
