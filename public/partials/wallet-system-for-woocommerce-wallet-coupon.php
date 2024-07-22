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


<?php do_action( 'wsfw_coupon__redeem' ); ?>
<div class='content active'>
	<span id="wps_wallet_transfer_form">
		<p class="wps-wallet-field-container form-row form-row-wide">
			<label for="wps_wallet_recharge_amount"><?php echo esc_html__( 'Enter Coupon Code :', 'wallet-system-for-woocommerce' ); ?></label>
				<input type="text" name="wps_wsfw_coupon_code" class="input-text" id="wps_wsfw_coupon_code" value="" placeholder="<?php echo esc_html__( 'Coupon Code :', 'wallet-system-for-woocommerce' ); ?>">
		</p>
		<p class="error"></p>
		<p class="wps-wallet-field-container form-row">
			<input type="hidden" name="wps_current_user_id" id="wps_current_user_id" value="<?php echo esc_attr( $user_id ); ?>">
			<div class="wsfw_loader__wrapper">
				<input type="submit" class="wps-btn__filled button" id="wps_coupon_wallet" name="wps_coupon_wallet" value="<?php esc_html_e( 'Redeem Coupon', 'wallet-system-for-woocommerce' ); ?>">
				<?php do_action( 'wsfw_before_redeem_loader' ); ?>
				<img class="loading_image" style="display: none;" src="<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>common/images/loader.gif">
			</div>
			<p class="wsfw__redeem_form_message" style="display: none;"></p>
			<?php do_action( 'wsfw_after_redeem_loader' ); ?>
		</p>
</span>
</div>

