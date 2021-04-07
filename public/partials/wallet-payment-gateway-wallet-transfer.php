<?php
/**
 * Exit if accessed directly
 *
 * @package wallet-payment-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class='content active'>
    <h2><?php esc_html_e( 'Wallet Transfer', 'wallet-system-for-woocommerce' ); ?></h2>
    <form method="post" action="" id="mwb_wallet_transfer_form">
        <p class="mwb-wallet-field-container form-row form-row-wide">
            <label for="mwb_wallet_transfer_user_id"><?php esc_html_e( 'Select whom to transfer (Email)', 'wallet-system-for-woocommerce' ); ?></label>
            <select name="mwb_wallet_transfer_user_id" class="mwb-wallet-userselect2" required="">
            </select>
        </p>
        <p class="mwb-wallet-field-container form-row form-row-wide">
            <label for="mwb_wallet_transfer_amount"><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></label>
            <input type="number" step="0.01" min="0" id="mwb_wallet_transfer_amount" name="mwb_wallet_transfer_amount" required="">
        </p>
        <p class="error" style="color:red"></p>
        <!-- <p class="mwb-wallet-field-container form-row form-row-wide">
            <label for="mwb_wallet_transfer_note"><?php esc_html_e( 'What\'s this for', 'wallet-system-for-woocommerce' ); ?></label>
            <textarea name="mwb_wallet_transfer_note"></textarea>
        </p> -->
        <p class="mwb-wallet-field-container form-row">
            <input type="hidden" name="current_user_id" value="<?php esc_attr_e( $user_id, 'wallet-system-for-woocommerce' ); ?>">
            <input type="submit" class="button" id="mwb_proceed_transfer" name="mwb_proceed_transfer" value="Proceed to transfer">
        </p>
    </form>
</div>