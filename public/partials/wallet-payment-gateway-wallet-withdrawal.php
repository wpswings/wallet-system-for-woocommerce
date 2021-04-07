<?php
/**
 * Exit if accessed directly
 *
 * @package wallet-payment-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$min = get_option( 'wallet_minimum_withdrawn_amount', 0 );
$max = get_option( 'wallet_maximum_withdrawn_amount', '' );

?>


<div class='content active'>
    <h2><?php esc_html_e( 'Wallet Withdrawal Request', 'wallet-system-for-woocommerce' ); ?></h2>
    
    <?php
    $disable_withdrawal_request = get_user_meta( $user_id, 'disable_further_withdrawal_request', true );
    if ( $disable_withdrawal_request ) {
        show_message_on_widthdrawal_requesting( 'Your wallet\'s withdrawal request is in pending.', 'woocommerce-info' ); 
        $args = array( 
            'numberposts' => -1,
            'post_type'	  => 'wallet_withdrawal', 
            'orderby' 	  => 'date',
            'order' 	  => 'ASC', 
            'post_status' => 'pending'
        );
        $withdrawal_request = get_posts($args);
        ?>
        <table>
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Method', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $withdrawal_request as $key => $pending ) {
                    $request_id = $pending->ID;
                    $userid = get_post_meta( $request_id , 'wallet_user_id' , true );
                    if ( $userid == $user_id ) {
                        echo '<tr>
                        <td>'. wc_price( get_post_meta( $request_id , 'mwb_wallet_withdrawal_amount' , true ) ) .'</td>
                        <td>'. get_post_meta( $request_id , 'withdrawal_request_status' , true ) .'</td>
                        <td>'. get_post_meta( $request_id , 'wallet_payment_method' , true ) .'</td>
                        <td>'. $pending->post_date .'</td>
                        </tr>';
                    }
                }
                ?>
            
            </tbody>
        </table>
    <?php 
    } else { 
    if ( $min > $wallet_bal )  {
        show_message_on_widthdrawal_requesting( 'Your wallet amount is less than minimum amount of withdrawing money(' .wc_price( $min ).') from wallet.', 'woocommerce-error' ); 
    } else { ?>
        <form method="post" action="" id="mwb_wallet_transfer_form">
            <p class="mwb-wallet-field-container form-row form-row-wide">
                <label for="mwb_wallet_withdrawal_amount"><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></label>
                <input type="number" step="0.01" min="<?php esc_attr_e( $min, 'wallet-system-for-woocommerce' ); ?>" max="<?php esc_attr_e( $max, 'wallet-system-for-woocommerce' ); ?>" id="mwb_wallet_withdrawal_amount" name="mwb_wallet_withdrawal_amount" required="">
            </p>
            <?php
            if ( ! empty( $wallet_methods ) && is_array( $wallet_methods ) ) { ?>
                <p class="mwb-wallet-field-container form-row form-row-wide">
                    <label for="wallet_payment_method"><?php esc_html_e( 'Select Payment Method', 'wallet-system-for-woocommerce' ); ?></label>
                    <select name="wallet_payment_method" id="wallet_payment_method" required="">
                        <option value="Select method"><?php esc_html_e( 'Select method', 'wallet-system-for-woocommerce' ); ?></option>
                <?php
                    foreach ( $wallet_methods as $key => $method ) { ?>
                        <option value="<?php esc_attr_e( $method['name'], 'wallet-system-for-woocommerce' ); ?>"><?php esc_html_e( $method['name'], 'wallet-system-for-woocommerce' ); ?></option>
                    <?php } ?>
                    </select>
                </p>
            
            <?php } ?>

            <p class="mwb-wallet-field-container form-row form-row-wide show-on-bank-transfer">
                <label for="mwb_wallet_bank_account_name"><?php esc_html_e( 'Account Name', 'wallet-system-for-woocommerce' ); ?></label>
                <input type="text" id="mwb_wallet_bank_account_name" name="mwb_wallet_bank_account_name" >
            </p>
            <p class="mwb-wallet-field-container form-row form-row-wide  show-on-bank-transfer">
                <label for="mwb_wallet_bank_account_no"><?php esc_html_e( 'Bank Account No.', 'wallet-system-for-woocommerce' ); ?></label>
                <input type="text" id="mwb_wallet_bank_account_no" name="mwb_wallet_bank_account_no" >
            </p>
            <p class="mwb-wallet-field-container form-row form-row-wide show-on-bank-transfer">
                <label for="mwb_wallet_bank_sort_code"><?php esc_html_e( 'Sort Code', 'wallet-system-for-woocommerce' ); ?></label>
                <input type="text" id="mwb_wallet_bank_sort_code" name="mwb_wallet_bank_sort_code" minlength="6" maxlength="6" pattern="[0-9]{6}" title="Code should be of 6 digits, Only numbers are allowed" >
            </p>

            <p class="mwb-wallet-field-container form-row form-row-wide show-on-paypal">
                <label for="mwb_wallet_paypal_email"><?php esc_html_e( 'PayPal Email', 'wallet-system-for-woocommerce' ); ?></label>
                <input type="email" id="mwb_wallet_paypal_email" name="mwb_wallet_paypal_email" >
            </p>

            <p class="error" style="color:red"></p>

            <p class="mwb-wallet-field-container form-row">
                <input type="hidden" name="wallet_user_id" value="<?php esc_attr_e( $user_id, 'wallet-system-for-woocommerce' ); ?>">
                <input type="submit" class="button" id="mwb_withdrawal_request" name="mwb_withdrawal_request" value="Request For Withdrawal" disabled >
            </p>
        </form>
    <?php }
    ?>
    
    <?php } ?>

</div>