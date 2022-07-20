<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$wsfw_min_max_value = apply_filters( 'wsfw_min_max_value_for_wallet_recharge', array() );
if ( is_array( $wsfw_min_max_value ) ) {
	if ( ! empty( $wsfw_min_max_value['min_value'] ) ) {
		$min_value = $wsfw_min_max_value['min_value'];
		$min_value = apply_filters( 'wps_wsfw_show_converted_price', $min_value );
	} else {
		$min_value = 0;
	}
	if ( ! empty( $wsfw_min_max_value['max_value'] ) ) {
		$max_value = $wsfw_min_max_value['max_value'];
		$max_value = apply_filters( 'wps_wsfw_show_converted_price', $max_value );
	} else {
		$max_value = '';
	}
}

?>

<div class='content active'>
	<form method="post" action="" id="wps_wallet_transfer_form">
		<p class="wps-wallet-field-container form-row form-row-wide">
			<label for="wps_wallet_recharge_amount"><?php echo esc_html__( 'Enter Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol( $current_currency ) ) . '):'; ?></label>
			<input type="number" id="wps_wallet_recharge" step="0.01" data-min="<?php echo esc_attr( $min_value ); ?>" data-max="<?php echo esc_attr( $max_value ); ?>" name="wps_wallet_recharge_amount" required="">
		</p>
		<p class="error"></p>
		<?php
		do_action( 'wsfw_make_wallet_recharge_subscription' );
		?>
		<p class="wps-wallet-field-container form-row">
			<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">
			<input type="hidden" id="verifynonce" name="verifynonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
			<input type="submit" class="wps-btn__filled button" id="wps_recharge_wallet" name="wps_recharge_wallet" value="<?php esc_html_e( 'Proceed', 'wallet-system-for-woocommerce' ); ?>">
		</p>
	</form>
</div>
