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
    <form method="post" action="" id="mwb_wallet_transfer_form">
        <p class="mwb-wallet-field-container form-row form-row-wide">
            <label for="mwb_wallet_recharge_amount"><?php esc_html_e( 'Enter Amount ($):', 'wallet-system-for-woocommerce' ); ?></label>
            <input type="number" id="mwb_wallet_recharge" step="0.01" min="0" name="mwb_wallet_recharge_amount" required="">
        </p>
        <p class="error" style="color:red"></p>
        <p class="mwb-wallet-field-container form-row">
            <input type="hidden" name="user_id" value="<?php esc_attr_e( $user_id, 'wallet-system-for-woocommerce' ); ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id ?>">
            <input type="submit" class="mwb-btn mwb-btn__filled button" id="mwb_recharge_wallet" name="mwb_recharge_wallet" value="Proceed">
        </p>
    </form>
</div>