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
 <div class="mwb-wsfw-gen-section-form mwb-wsfw-wallet-system-rest-api"> 
    <h4><?php esc_html_e( 'REST API keys' , 'wallet-system-for-woocommerce' ); ?></h4> 
    <form action="" method="POST" > 
        <div class="wpg-secion-wrap">
            <div class="mwb-form-group">
                <div class="mwb-form-group__control">
                    <input type="submit" class="mwb-btn mwb-btn__filled" name="import_wallets" id="import_wallets" value="Generate API key">
                </div>
            </div>
        </div>
    </form>
</div>