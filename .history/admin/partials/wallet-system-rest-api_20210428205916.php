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

if ( isset( $_POST['generate_api_key'] ) && ! empty( $_POST['generate_api_key'] ) ) {
    
    $api_keys = array();
    for ($i = 0; $i < 2; $i++) {
        $random = rand();
        $api_keys[] = md5( $random );
    }
    $wallet_api_keys['consumer_key']    = $api_keys[0];
    $wallet_api_keys['consumer_secret'] = $api_keys[1];
    $result = update_option( 'mwb_wsfw_wallet_rest_api_keys', $wallet_api_keys );  
    if ( $result ) {
        $msfw_wpg_error_text = esc_html__( 'API Key generated successfully.', 'wallet-system-for-woocommerce' );
        $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'success' );
    } else {
        $msfw_wpg_error_text = esc_html__( 'API Key is not created', 'wallet-system-for-woocommerce' );
        $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
    }
		
}


?>
 <div class="mwb-wsfw-gen-section-form mwb-wsfw-wallet-system-rest-api"> 
    <h4><?php esc_html_e( 'REST API keys' , 'wallet-system-for-woocommerce' ); ?></h4> 
    <?php 
    $rest_api_keys = get_option( 'mwb_wsfw_wallet_rest_api_keys', '' );
    if ( empty( $rest_api_keys )  || ! is_array( $rest_api_keys ) ){ ?>
    <form action="" method="POST" > 
        <div class="wpg-secion-wrap">
            <div class="mwb-form-group">
                <div class="mwb-form-group__control">

        <p><?php esc_html_e( 'REST API allows external apps to view and manage wallet. Access is granted only to those with valid API keys.' , 'wallet-system-for-woocommerce' ); ?></p>
                    <input type="submit" class="mwb-btn mwb-btn__filled" name="generate_api_key"  value="Generate API key">
                </div>
            </div>
        </div>
    </form>
    <?php
    } else { ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <label><?php esc_html_e( 'Consumer key', 'wallet-system-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="consumer_key" class="wsfw-number-class" value="<?php esc_attr_e( $rest_api_keys['consumer_key'], 'wallet-system-for-woocommerce' ); ?>" >
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?php esc_html_e( 'Consumer secret', 'wallet-system-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="consumer_secret" class="wsfw-number-class" >
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="delete_api_keys"  class="mwb-btn mwb-btn__filled"  value="Delete API Keys"></p> 
    <?php }
    ?>
    
</div>
