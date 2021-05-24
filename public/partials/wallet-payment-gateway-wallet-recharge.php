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
			<label for="mwb_wallet_recharge_amount"><?php echo esc_html__( 'Enter Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol() ) . '):'; ?></label>
			<input type="number" id="mwb_wallet_recharge" step="0.01" min="0" name="mwb_wallet_recharge_amount" required="">
		</p>
		<p class="error"></p>
		<p class="mwb-wallet-field-container form-row">
			<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">
			<input type="hidden" id="verifynonce" name="verifynonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
			<input type="submit" class="mwb-btn__filled button" id="mwb_recharge_wallet" name="mwb_recharge_wallet" value="Proceed">
		</p>
	</form>
</div>
