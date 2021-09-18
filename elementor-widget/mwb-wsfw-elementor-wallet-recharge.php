<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$current_currency       = apply_filters( 'mwb_wsfw_get_current_currency', get_woocommerce_currency() );
$enable_wallet_recharge = get_option( 'wsfw_enable_wallet_recharge', '' );
$product_id             = get_option( 'mwb_wsfw_rechargeable_product_id', '' );
$user_id                = get_current_user_id();
$wallet_bal             = get_user_meta( $user_id, 'mwb_wallet', true );
$wallet_bal             = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
$wallet_bal             = apply_filters( 'mwb_wsfw_show_converted_price', $wallet_bal );
if ( wc_post_content_has_shortcode( 'MWB_WALLET_RECHARGE' ) ) {
	$wsfw_min_max_value = apply_filters( 'wsfw_min_max_value_for_wallet_recharge', array() );
	if ( is_array( $wsfw_min_max_value ) ) {
		if ( ! empty( $wsfw_min_max_value['min_value'] ) ) {
			$min_value = $wsfw_min_max_value['min_value'];
			$min_value = apply_filters( 'mwb_wsfw_show_converted_price', $min_value );
		} else {
			$min_value = 0;
		}
		if ( ! empty( $wsfw_min_max_value['max_value'] ) ) {
			$max_value = $wsfw_min_max_value['max_value'];
			$max_value = apply_filters( 'mwb_wsfw_show_converted_price', $max_value );
		} else {
			$max_value = '';
		}
	}
	if ( ! empty( $product_id ) && ! empty( $enable_wallet_recharge ) ) {
		echo '<div class="content mwb_wallet_shortcodes">
			<h3>' . esc_html__( 'Wallet Recharge', 'wallet-system-for-woocommerce' ) . '</h3>
			<form method="post" action="" id="mwb_wallet_transfer_form">
				<p class="mwb-wallet-field-container form-row form-row-wide">
					<label for="mwb_wallet_recharge_amount">' . esc_html__( 'Enter Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol( $current_currency ) ) . '):' . '</label>
					<input type="number" id="mwb_wallet_recharge" step="0.01" data-min="' . esc_attr( $min_value ) . '" data-max="' . esc_attr( $max_value ) . '" name="mwb_wallet_recharge_amount" required="">
				</p>
				<p class="error"></p>
				<p class="mwb-wallet-field-container form-row">
					<input type="hidden" name="user_id" value="' . esc_attr( $user_id ) . '">
					<input type="hidden" name="product_id" value="' . esc_attr( $product_id ) . '">
					<input type="hidden" id="verifynonce" name="verifynonce" value="' . esc_attr( wp_create_nonce() ) . '" />
					<input type="submit" class="mwb-btn__filled button" id="mwb_recharge_wallet" name="mwb_recharge_wallet" value="' . esc_html__( 'Proceed', 'wallet-system-for-woocommerce' ) . '">
				</p>
			</form>
		</div>';
	}
}
