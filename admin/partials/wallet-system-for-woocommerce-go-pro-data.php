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

?>


<div class="wps_wallet_lite_go_pro_popup_wrap ">
		<!-- Go pro popup main start. -->
		<div class="wps_wsfw_popup_shadow"></div>
		<div class="wps_wallet_lite_go_pro_popup">
			<!-- Main heading. -->
			<div class="wps_wallet_lite_go_pro_popup_head">
				<h2><?php esc_html_e( 'Unlock Seamless Payments With Wallet System for WooCommerce Pro!', 'wallet-system-for-woocommerce' ); ?></h2>
				<!-- Close button. -->
				<a href="javascript:void(0)" class="wps_wallet_lite_go_pro_popup_close">
					<span>×</span>
				</a>
			</div>  

			<!-- Notice icon. -->
			<div class="wps_wallet_lite_go_pro_popup_head"><img height="200" class="wps_go_pro_images" src="<?php echo esc_attr( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/go-pro.png' ); ?>">
			</div>
			
				
			<!-- Notice. -->
			<div class="wps_wallet_lite_go_pro_popup_content">
				<p class="wps_wallet_lite_go_pro_popup_text">
				<?php
				esc_html_e(
					'Upgrade now to enjoy advanced features like full or partial payment methods, QR code payments, loyalty credits, cashback rewards, quick recharge buttons, & promotional offers. 
					Stucked with Limited Gateway access? Unlock your power to explore more.',
					'wallet-system-for-woocommerce'
				)
				?>
							</p>
					
					<p class="wps_wallet_lite_go_pro_popup_text">
					
					<?php esc_html_e( 'Manage funds in bulk, view transaction history, send email notifications, & offer refunds directly to customer wallets. Elevate the digital payment experience.', 'wallet-system-for-woocommerce' ); ?>			

				</div>

			<!-- Go pro button. -->
			<div class="wps_wallet_lite_go_pro_popup_button">
				<a class="button wps_ubo_lite_overview_go_pro_button" target="_blank" href="https://wpswings.com/product/wallet-system-for-woocommerce-pro/?utm_source=wpswings-wallet-pro&utm_medium=wallet-org-backend-page&utm_campaign=wallet-pro">	<?php esc_html_e( 'Upgrade To Premium today!', 'wallet-system-for-woocommerce' ); ?> </p>
			<span class="dashicons dashicons-arrow-right-alt"></span></a>
			</div>
		</div>
		<!-- Go pro popup main end. -->
	</div>
