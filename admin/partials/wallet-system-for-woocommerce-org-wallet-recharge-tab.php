<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce_pro
 * @subpackage Wallet_System_For_Woocommerce_pro/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wsfw_wps_wsfw_obj;


$wsfw_wallet_action_settings_add_bookie_array = apply_filters( 'wsfw_wallet_action_settings_add_bookie_array', array() );

$wsfw_wallet_action_comment_settings      = apply_filters( 'wsfw_wallet_action_settings_recharge_tab_array', array() );

$wps_wallet_recharge_tab_array = get_option( 'wps_wallet_action_recharge_tab_array' );


if ( ! empty( $wps_wallet_recharge_tab_array ) && is_array( $wps_wallet_recharge_tab_array ) ) {
	if ( '' == $wps_wallet_recharge_tab_array[0] ) {
		$wps_wallet_recharge_tab_array = array();
	}
} else {
	$wps_wallet_recharge_tab_array = array();
}


?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
  
	<div class="wps-form-group-recharge-tab">
		<div class="wsfw-secion-daily-visit">
		<h4>  <?php esc_html_e( 'Wallet Recharge', 'wallet-system-for-woocommerce' ); ?>:</h4>
		<div class="wsfw-secion-refer-customize-wallet">
			<?php
			$wsfw_wallet_action_recharge_enable_settings      = apply_filters( 'wsfw_wallet_action_recharge_enable_settings_org', array() );

			$wsfw_wallet_action_recharge_enable_settings = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_recharge_enable_settings );
			if ( ! empty( $wsfw_wallet_action_recharge_enable_settings ) ) {
				echo wp_kses_post( $wsfw_wallet_action_recharge_enable_settings );

			}


			?>
	  <div class="wsfw-secion-table-label-wrap">
	  
		<table border='2' id="wps_wallet_action_recharge_tab_table" >
			<tr>
				<th><?php esc_html_e( 'Wallet Amount', 'wallet-system-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Action', 'wallet-system-for-woocommerce' ); ?></th>
			</tr>
			<tr>
				<td>
				<div class="wps-form-group__control-wps">
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
							class="mdc-text-field__input wsfw-text-class" 
							name="wps_wallet_action_bookie_array[]"
							id="wps_wallet_action_bookie_array[]"
							type="text"
							value="<?php echo esc_attr( '' ); ?>"
							placeholder="<?php echo esc_attr( ' Enter Recharge Amount ' ); ?>">
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
	<div class="wps_wallet_lite_go_pro_popup_wrap ">
		<!-- Go pro popup main start. -->
		<div class="wps_wallet_lite_go_pro_popup">
			<!-- Main heading. -->
			<div class="wps_wallet_lite_go_pro_popup_head">
				<h2><?php _e( 'Unlock Seamless Payments With Wallet System for WooCommerce Pro!', 'wallet-system-for-woocommerce' ) ?></h2>
				<!-- Close button. -->
				<a href="javascript:void(0)" class="wps_wallet_lite_go_pro_popup_close">
					<span>×</span>
				</a>
			</div>  

			<!-- Notice icon. -->
			<div class="wps_wallet_lite_go_pro_popup_head"><img class="wps_go_pro_images" src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/go-pro.png' ); ?>">
			</div>
			
				
			<!-- Notice. -->
			<div class="wps_wallet_lite_go_pro_popup_content">
				<p class="wps_wallet_lite_go_pro_popup_text">
				<?php _e( 'Upgrade now to enjoy advanced features like full or partial payment methods, QR code payments, loyalty credits, cashback rewards, quick recharge buttons, & promotional offers. 
					Stucked with Limited Gateway access? Unlock your power to explore more.', 'wallet-system-for-woocommerce' ) ?>			</p>
					
					<p class="wps_wallet_lite_go_pro_popup_text">
					
					<?php _e( 'Manage funds in bulk, view transaction history, send email notifications, & offer refunds directly to customer wallets. Elevate the digital payment experience.', 'wallet-system-for-woocommerce' ) ?>			

				</div>

			<!-- Go pro button. -->
			<div class="wps_wallet_lite_go_pro_popup_button">
				<a class="button wps_ubo_lite_overview_go_pro_button" target="_blank" href="https://wpswings.com/product/one-click-upsell-funnel-for-woocommerce-pro/?utm_source=wpswings-upsell-funnel-pro&amp;utm_medium=upsell-funnel-org-backend&amp;utm_campaign=WPS-upsell-funnel-pro">	<?php _e( 'Upgrade To Premium today!', 'wallet-system-for-woocommerce' ) ?> </p>
			<span class="dashicons dashicons-arrow-right-alt"></span></a>
			</div>
		</div>
		<!-- Go pro popup main end. -->
	</div>
		<input type="hidden" id="updatenoncewallet_action" name="updatenoncewallet_action" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>
