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
    <?php if( $wallet_bal > 0 ) { ?>
    <form method="post" action="" id="mwb_wallet_transfer_form">
        <p class="mwb-wallet-field-container form-row form-row-wide">
            <label for="mwb_wallet_transfer_user_email"><?php esc_html_e( 'Transfer to :', 'wallet-system-for-woocommerce' ); ?></label>
            <input type="email" class="mwb-wallet-userselect" id="mwb_wallet_transfer_user_email" name="mwb_wallet_transfer_user_email" required="">
        </p>
        <p class="mwb-wallet-field-container form-row form-row-wide">
            <label for="mwb_wallet_transfer_amount"><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></label>
            <input type="number" step="0.01" min="0" max="<?php esc_attr_e( $wallet_bal, 'wallet-system-for-woocommerce' ); ?>" id="mwb_wallet_transfer_amount" name="mwb_wallet_transfer_amount" required="">
        </p>
        <p class="error" style="color:red"></p>
        <p class="mwb-wallet-field-container form-row form-row-wide">
            <label for="mwb_wallet_transfer_note"><?php esc_html_e( 'What\'s this for', 'wallet-system-for-woocommerce' ); ?></label>
            <textarea name="mwb_wallet_transfer_note"></textarea>
        </p>
        <p class="mwb-wallet-field-container form-row">
            <input type="hidden" name="current_user_id" value="<?php esc_attr_e( $user_id, 'wallet-system-for-woocommerce' ); ?>">
            <input type="submit" class="mwb-btn__filled button" id="mwb_proceed_transfer" name="mwb_proceed_transfer" value="Proceed">
        </p>
    </form>
    <?php } else {
        show_message_on_form_submit( 'Your wallet amount is 0, you cannot transfer money.', 'woocommerce-error' );
    } ?>
</div>


