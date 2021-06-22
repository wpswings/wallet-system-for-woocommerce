<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );
$wallet_bal = apply_filters( 'mwb_wsfw_show_converted_price', $wallet_bal );

?>

<div class='content active'>
	<?php
	if ( $wallet_bal > 0 ) {
		global $wp_session;
		if ( ! empty( $wp_session['mwb_wallet_transfer_user_email'] ) ) {
			$useremail = $wp_session['mwb_wallet_transfer_user_email'];
		} else {
			$useremail = '';
		}
		if ( ! empty( $wp_session['mwb_wallet_transfer_amount'] ) ) {
			$transfer_amount = $wp_session['mwb_wallet_transfer_amount'];
		} else {
			$transfer_amount = 0;
		}
		$show_additional_content = apply_filters( 'mwb_wsfw_show_additional_content', '', $user_id, $useremail, $transfer_amount );
		if ( ! empty( $show_additional_content ) ) {
			echo $show_additional_content;
		}
		?>
	<form method="post" action="" id="mwb_wallet_transfer_form">
		<p class="mwb-wallet-field-container form-row form-row-wide">
			<label for="mwb_wallet_transfer_user_email"><?php esc_html_e( 'Transfer to', 'wallet-system-for-woocommerce' ); ?></label>
			<input type="email" class="mwb-wallet-userselect" id="mwb_wallet_transfer_user_email" name="mwb_wallet_transfer_user_email" required="">
		</p>
		<p class="mwb-wallet-field-container form-row form-row-wide">
			<label for="mwb_wallet_transfer_amount"><?php echo esc_html__( 'Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol( $current_currency ) ) . ')'; ?></label>
			<input type="number" step="0.01" min="0" data-max="<?php echo esc_attr( $wallet_bal ); ?>" id="mwb_wallet_transfer_amount" name="mwb_wallet_transfer_amount" required="">
		</p>
		<p class="error"></p>
		<p class="mwb-wallet-field-container form-row form-row-wide">
			<label for="mwb_wallet_transfer_note"><?php esc_html_e( 'What\'s this for', 'wallet-system-for-woocommerce' ); ?></label>
			<textarea name="mwb_wallet_transfer_note"></textarea>
		</p>
		<?php
		$show_additional_form_content = apply_filters( 'mwb_wsfw_show_additional_form_content', '' );
		if ( ! empty( $show_additional_form_content ) ) {
			echo $show_additional_form_content;
		}
		?>
		<p class="mwb-wallet-field-container form-row">
			<input type="hidden" name="current_user_id" value="<?php echo esc_attr( $user_id ); ?>">
			<input type="submit" class="mwb-btn__filled button" id="mwb_proceed_transfer" name="mwb_proceed_transfer" value="Proceed">
		</p>
	</form>
		<?php
	} else {
		show_message_on_form_submit( 'Your wallet amount is 0, you cannot transfer money.', 'woocommerce-error' );
	}
	?>
</div>


