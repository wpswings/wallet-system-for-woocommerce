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
    
    <form action="" method="POST"  class="mwb-wsfw-gen-section-form" > 
        <div class="wpg-secion-wrap">
            <h3><?php esc_html_e( 'Edit wallet of all users at once' , 'wallet-system-for-woocommerce' ); ?></h3>
            <?php
            $wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_update_wallet );
            echo esc_html( $wsfw_general_html );
            ?>
        </div>
    </form>
