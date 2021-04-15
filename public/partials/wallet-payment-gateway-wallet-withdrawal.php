<?php
/**
 * Exit if accessed directly
 *
 * @package wallet-payment-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );

?>


<div class='content active'>
    
    <?php
    $disable_withdrawal_request = get_user_meta( $user_id, 'disable_further_withdrawal_request', true );
    if ( $disable_withdrawal_request ) {
        show_message_on_form_submit( 'Your wallet\'s withdrawal request is in pending.', 'woocommerce-info' ); 
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
                    <th><?php esc_html_e( 'ID', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $withdrawal_request as $key => $pending ) {
                    $request_id = $pending->ID;
                    $userid = get_post_meta( $request_id , 'wallet_user_id' , true );
                    if ( $userid == $user_id ) {
                        $date = date_create($pending->post_date);
                        echo '<tr>
                        <td>'. $request_id .'</td>
                        <td>'. wc_price( get_post_meta( $request_id , 'mwb_wallet_withdrawal_amount' , true ) ) .'</td>
                        <td>'. $pending->post_status .'</td>
                        <td>'. get_post_meta( $request_id , 'mwb_wallet_note' , true ) .'</td>
                        <td>'. esc_html__( date_format( $date,"d/m/Y"), 'wallet-system-for-woocommerce' ) .'</td>
                        </tr>';
                    }
                }
                ?>
            
            </tbody>
        </table>
    <?php 
    } else {
        if( $wallet_bal > 0 ) { 
        ?>
        <form method="post" action="" id="mwb_wallet_transfer_form">
            <p class="mwb-wallet-field-container form-row form-row-wide">
                <label for="mwb_wallet_withdrawal_amount"><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></label>
                <input type="number" step="0.01" min="0" max="<?php esc_attr_e( $wallet_bal, 'wallet-system-for-woocommerce' ); ?>" id="mwb_wallet_withdrawal_amount" name="mwb_wallet_withdrawal_amount" required="">
            </p>

            <p class="mwb-wallet-field-container form-row form-row-wide">
                <label for="mwb_wallet_note"><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></label>
                <textarea id="mwb_wallet_note" name="mwb_wallet_note" required></textarea>
            </p>

            <p class="error" style="color:red"></p>

            <p class="mwb-wallet-field-container form-row">
                <input type="hidden" name="wallet_user_id" value="<?php esc_attr_e( $user_id, 'wallet-system-for-woocommerce' ); ?>">
                <input type="submit" class="mwb-btn__filled button" id="mwb_withdrawal_request" name="mwb_withdrawal_request" value="Request For Withdrawal" >
            </p>
        </form>
        <?php } else {
            show_message_on_form_submit( 'Your wallet amount is 0, you cannot withdraw money from wallet.', 'woocommerce-error' );
        } 
    } ?>

</div>