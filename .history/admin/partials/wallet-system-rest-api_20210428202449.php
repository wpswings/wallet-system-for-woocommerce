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
    if ( empty( $rest_api_keys ) ) { ?>
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
                    <th scope="row"><label for="wallet_amount"><?php esc_html_e( 'Consumer key', 'wallet-system-for-woocommerce' ); ?></label></th>
                    <td>
                        <input type="number" id="wallet_amount" step="0.01" name="wallet_amount" class="regular-text" required>
                        <p class="description"><?php esc_html_e( 'Enter amount you want to credit/debit', 'wallet-system-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="action_type"><?php esc_html_e( 'Action', 'wallet-system-for-woocommerce' ); ?></label></th>
                    <td>
                        <select class="regular-text" name="action_type" id="action_type" required>
                            <option value="credit"><?php esc_html_e( 'Credit', 'wallet-system-for-woocommerce' ); ?></option>
                            <option value="debit"><?php esc_html_e( 'Debit', 'wallet-system-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Whether want to add amount or deduct it from wallet', 'wallet-system-for-woocommerce' ); ?></p></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="hidden" name="user_id" value="<?php esc_attr_e( $user_id, 'walllet-payment-gateway' ); ?>">
        <p class="submit"><input type="submit" name="update_wallet" class="button button-primary mwb_wallet-update" value="Update Wallet"></p> 
    <?php }
    ?>
    
</div>
