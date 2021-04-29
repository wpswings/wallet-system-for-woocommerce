<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to enable wallet, set min and max value for recharging wallet 
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wsfw_mwb_wsfw_obj;




?>
<div class="mwb-wpg-gen-section-form-container">
	<div class="wpg-secion-wrap">
    	<h3><?php esc_html_e( 'Credit/Debit amount from user\'s wallet' , 'wallet-system-for-woocommerce' ); ?></h3>
	</div>
	<div class="mwb-wpg-gen-section-form-wrapper">
		<form action="" method="POST" class="mwb-wpg-gen-section-form" id="form_update_wallet"> 
			<div class="wpg-secion-wrap">
				<h3><?php esc_html_e( 'Edit wallet of all users at once' , 'wallet-system-for-woocommerce' ); ?></h3>
				<?php
				$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_update_wallet );
				echo esc_html( $wsfw_general_html );
				?>
			</div>
			<div class="mwb_wallet-update--popupwrap">
				<div class="mwb_wallet-update-popup">
					<h3><?php esc_html_e( 'Are you sure to update wallet of all users?' , 'wallet-system-for-woocommerce' ); ?></h3>
					<div class="mwb_wallet-update-popup-btn">
						<input type="submit" class="mwb-btn mwb-btn__filled" name="confirm_updatewallet" id="confirm_updatewallet" value="<?php esc_html_e( 'Yes, I\'m Sure' , 'wallet-system-for-woocommerce' ); ?>" >
						<a href="javascript:void(0);" id="cancel_walletupdate" ><?php esc_html_e( 'Not now' , 'wallet-system-for-woocommerce' ); ?></a>
					</div>
				</div>
			</div>
		</form>

		<button class="mdc-ripple-upgraded" id="export_user_wallet" > <img src="<?php echo WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL; ?>admin/image/down-arrow.png" title="Download CSV file" >
		</button>

		<form action="" method="POST" class="mwb-wpg-gen-section-form" enctype="multipart/form-data">
			<div class="wpg-secion-wrap">
				<h3><?php esc_html_e( 'Import wallets for user' , 'wallet-system-for-woocommerce' ); ?></h3>
				<?php
				$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_import_settings );
				echo esc_html( $wsfw_general_html );
				?>
			</div>
		</form>
	</div>
</div>

