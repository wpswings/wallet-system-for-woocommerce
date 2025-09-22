<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wallet_bal = get_user_meta( $user_id, 'wps_wallet', true );
$wallet_bal = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
$wallet_bal = apply_filters( 'wps_wsfw_show_converted_price', $wallet_bal );

?>

<div class='content active'>
	<?php

	$wsfw_restrict_wallet_transfer_kyc   = get_option( 'wsfw_restrict_wallet_transfer_kyc' );
	$wsfw_enable_wallet_kyc = get_option( 'wsfw_enable_wallet_kyc' );
	$kyc_not_approved = false;
	if ( 'on' === $wsfw_restrict_wallet_transfer_kyc && 'on' == $wsfw_enable_wallet_kyc ) {
		$wps_wallet_kyc_status    = get_user_meta( $user_id, 'key_verification_status', true );
		if ( 'pending' == $wps_wallet_kyc_status || 'rejected' == $wps_wallet_kyc_status || '' == $wps_wallet_kyc_status ) {
			$kyc_not_approved = true;
		}
	}
	if ( $kyc_not_approved ) {
		show_message_on_form_submit( esc_html__( 'You must complete KYC verification to transfer funds from your wallet.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	} elseif ( $wallet_bal > 0 ) {
			global $wp_session;
		if ( ! empty( $wp_session['wps_wallet_transfer_user_email'] ) ) {
			$useremail = $wp_session['wps_wallet_transfer_user_email'];
		} else {
			$useremail = '';
		}
		if ( ! empty( $wp_session['wps_wallet_transfer_amount'] ) ) {
			$transfer_amount = $wp_session['wps_wallet_transfer_amount'];
		} else {
			$transfer_amount = 0;
		}
			$show_additional_content = apply_filters( 'wps_wsfw_show_additional_content', '', $user_id, $useremail, $transfer_amount );
		if ( ! empty( $show_additional_content ) ) {
			echo wp_kses_post( $show_additional_content ); // phpcs:ignore
		}
			$is_pro_plugin = false;
			$wsfwp_min_wallet_transfer_amount = 0;
			$wsfwp_max_wallet_transfer_amount = 0;
			$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
		if ( $is_pro_plugin ) {
			$wps_wsfwp_wallet_transfer_restriction_enable = get_option( 'wps_wsfwp_wallet_transfer_restriction_enable' );
			if ( 'on' == $wps_wsfwp_wallet_transfer_restriction_enable ) {
				$wsfwp_min_wallet_transfer_amount = get_option( 'wsfwp_min_wallet_transfer_amount' );
				$wsfwp_max_wallet_transfer_amount = get_option( 'wsfwp_max_wallet_transfer_amount' );
			}
		}
		?>
			<span  id="wps_wallet_transfer_form">
			<!-- <p class="wps-wallet-field-container form-row form-row-wide">
				<label for="wps_wallet_transfer_user_email"><?php esc_html_e( 'Transfer to', 'wallet-system-for-woocommerce' ); ?></label>
				<input type="email" class="wps-wallet-userselect" id="wps_wallet_transfer_user_email" name="wps_wallet_transfer_user_email" data-current-email="<?php echo esc_attr( $current_user_email ); ?>" required="">
			</p> -->
			<p class="wps-wallet-field-container form-row form-row-wide">
				<label for="wps_wallet_transfer_method"><?php esc_html_e( 'Select Transfer Method', 'wallet-system-for-woocommerce' ); ?></label>
				<select id="wps_wallet_transfer_method" name="wps_wallet_transfer_method" class="wps-wallet-method">
					<option value="email" selected><?php esc_html_e( 'User Email', 'wallet-system-for-woocommerce' ); ?></option>
					<option value="wallet_id"><?php esc_html_e( 'User Wallet ID', 'wallet-system-for-woocommerce' ); ?></option>
				</select>
			</p>

			<!-- Transfer to (Email field - default visible) -->
			<p class="wps-wallet-field-container form-row form-row-wide" id="wps_wallet_transfer_email_wrap">
				<label for="wps_wallet_transfer_user_email"><?php esc_html_e( 'Transfer to (Email)', 'wallet-system-for-woocommerce' ); ?></label>
				<input type="email" class="wps-wallet-userselect" id="wps_wallet_transfer_user_email" name="wps_wallet_transfer_user_email" data-current-email="<?php echo esc_attr( $current_user_email ); ?>" required="">
			</p>

			<!-- Transfer to (Wallet ID field - hidden by default) -->
			<p class="wps-wallet-field-container form-row form-row-wide" id="wps_wallet_transfer_walletid_wrap" style="display:none;">
				<label for="wps_wallet_transfer_user_walletid"><?php esc_html_e( 'Transfer to (Wallet ID)', 'wallet-system-for-woocommerce' ); ?></label>
				<input type="text" class="wps-wallet-walletid" id="wps_wallet_transfer_user_walletid" name="wps_wallet_transfer_user_walletid">
			</p>
			<p class="transfer-error"></p>
			<p class="wps-wallet-field-container form-row form-row-wide">
				<label for="wps_wallet_transfer_amount"><?php echo esc_html__( 'Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol( $current_currency ) ) . ')'; ?></label>	
				<input type="number" step="0.01" min="0" data-mintransfer="<?php echo esc_attr( $wsfwp_min_wallet_transfer_amount ); ?>"data-maxtransfer="<?php echo esc_attr( $wsfwp_max_wallet_transfer_amount ); ?>"data-max="<?php echo esc_attr( $wallet_bal ); ?>" id="wps_wallet_transfer_amount" name="wps_wallet_transfer_amount" required="">
			</p>
			<p class="error"></p>
			<?php
			$wallet_transfer_fee_html = apply_filters( 'wps_wsfw_show_wallet_transfer_fee_content', '' );
			if ( ! empty( $wallet_transfer_fee_html ) ) {
				echo wp_kses_post( $wallet_transfer_fee_html ); // phpcs:ignore
			}
			?>
			<p class="wps-wallet-field-container form-row form-row-wide">
				<label for="wps_wallet_transfer_note"><?php esc_html_e( 'What\'s this for', 'wallet-system-for-woocommerce' ); ?></label>
				<textarea name="wps_wallet_transfer_note"></textarea>
			</p>
			<?php
			$show_additional_form_content = apply_filters( 'wps_wsfw_show_additional_form_content', '' );
			if ( ! empty( $show_additional_form_content ) ) {
				echo wp_kses_post( $show_additional_form_content ); // phpcs:ignore
			}
			?>
			<p class="wps-wallet-field-container form-row">
				<input type="hidden" name="current_user_id" value="<?php echo esc_attr( $user_id ); ?>">
				<input type="hidden" name="wps_current_user_email" value="<?php echo esc_attr( $current_user_email ); ?>">
				<input type="submit" class="wps-btn__filled button" id="wps_proceed_transfer" name="wps_proceed_transfer" value="<?php esc_html_e( 'Proceed', 'wallet-system-for-woocommerce' ); ?>">
			</p>
		</span>
			<?php
	} else {
		show_message_on_form_submit( esc_html__( 'Your wallet amount is 0, you cannot transfer money.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	}

	?>
</div>


