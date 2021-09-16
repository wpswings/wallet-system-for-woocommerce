<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_currency = apply_filters( 'mwb_wsfw_get_current_currency', get_woocommerce_currency() );
$user_id          = get_current_user_id();
$logged_in_user   = wp_get_current_user();
if ( ! empty( $logged_in_user ) ) {
	$current_user_email = $logged_in_user->user_email ? $logged_in_user->user_email : '';
} else {
	$current_user_email = '';
}
$wallet_bal       = get_user_meta( $user_id, 'mwb_wallet', true );
$wallet_bal       = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
$wallet_bal       = apply_filters( 'mwb_wsfw_show_converted_price', $wallet_bal );
if ( ! function_exists( 'show_message_on_wallet_form_submit' ) ) {
	/**
	 * Show message on form submit
	 *
	 * @param string $wpg_message message to be shown on form submission.
	 * @param string $type error type.
	 * @return void
	 */
	function show_message_on_wallet_form_submit( $wpg_message, $type = 'error' ) {
		$wpg_notice = '<div class="woocommerce"><p class="' . esc_attr( $type ) . '">' . $wpg_message . '</p>	</div>';
		echo wp_kses_post( $wpg_notice );
	}
}

?>
<?php
if ( wc_post_content_has_shortcode( 'MWB_WALLET_TRANSFER' ) ) {
	?>
	<div class='content mwb_wallet_shortcodes'>
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
		<h3><?php echo esc_html__( 'Wallet Transfer', 'wallet-system-for-woocommerce' ); ?></h3>
		<form method="post" action="" id="mwb_wallet_transfer_form">
			<p class="mwb-wallet-field-container form-row form-row-wide">
				<label for="mwb_wallet_transfer_user_email"><?php esc_html_e( 'Transfer to', 'wallet-system-for-woocommerce' ); ?></label>
				<input type="email" class="mwb-wallet-userselect" id="mwb_wallet_transfer_user_email" name="mwb_wallet_transfer_user_email" data-current-email="<?php echo esc_attr( $current_user_email ); ?>" required="">
			</p>
			<p class="transfer-error"></p>
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
				<input type="submit" class="mwb-btn__filled button" id="mwb_proceed_transfer" name="mwb_proceed_transfer" value="<?php esc_html_e( 'Proceed', 'wallet-system-for-woocommerce' ); ?>">
			</p>
		</form>
			<?php
		} else {
			show_message_on_wallet_form_submit( esc_html__( 'Your wallet amount is 0, you cannot transfer money.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		}
		?>
	</div>
	<?php
}
