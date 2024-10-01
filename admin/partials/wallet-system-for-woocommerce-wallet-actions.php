<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}








global $wsfw_wps_wsfw_obj;

if ( isset( $_POST['wsfw_button_wallet_action'] ) ) {
	$nonce = ( isset( $_POST['updatenoncewallet_action'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_action'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$wsfw_plugin_admin->wsfw_admis_save_tab_settings_for_wallet_action();

	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}
$wsfw_wallet_action_auto_topup_settings = apply_filters( 'wsfw_wallet_action_settings_auto_topup_array', array() );
$wsfw_wallet_action_registration_settings = apply_filters( 'wsfw_wallet_action_settings_registration_array', array() );
$wsfw_wallet_action_daily_visit_settings  = apply_filters( 'wsfw_wallet_action_settings_daily_visit_array', array() );
$wsfw_wallet_action_payment_gateway_charge_settings  = apply_filters( 'wsfw_wallet_action_settings_payment_gateway_charge_array', array() );
$wsfw_wallet_action_comment_settings      = apply_filters( 'wsfw_wallet_action_settings_comment_array', array() );
$wsfw_wallet_action_settings_submit_button_array      = apply_filters( 'wsfw_wallet_action_settings_submit_button_array', array() );
$wsfw_wallet_action_html = '';
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
	
	<div class="wsfw-secion-wallet-topup">
	<span><b><?php esc_html_e( 'Wallet Auto Top up', 'wallet-system-for-woocommerce' ); ?></b></span>
	<?php
		do_action( 'wsfw_general_settings_before' );
		$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_auto_topup_settings );
	if ( ! empty( $wsfw_wallet_action_html ) ) {
		echo wp_kses_post( $wsfw_wallet_action_html );
	}

	?>
	  </br>
  </div>
  <hr>
  <div class="wsfw-secion-payment-gateway-charge">
	  <span><b><?php esc_html_e( 'Payment Gateway charge for Wallet Recharge', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_payment_gateway_charge_settings );
		if ( ! empty( $wsfw_wallet_action_html ) ) {
			echo wp_kses_post( $wsfw_wallet_action_html );
		}
		?>
	</div>
	<hr>
	<div class="wsfw-secion-daily-visit">
	  <span><b><?php esc_html_e( 'Credit Amount On User Daily Visit', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_daily_visit_settings );
		if ( ! empty( $wsfw_wallet_action_html ) ) {
			echo wp_kses_post( $wsfw_wallet_action_html );
		}
		?>
	</div>
	<hr>
	<div class="wsfw-secion-registration">
	  <span><b><?php esc_html_e( 'Credit Amount On New User Registration', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_registration_settings );
		if ( ! empty( $wsfw_wallet_action_html ) ) {
			echo wp_kses_post( $wsfw_wallet_action_html );
		}
		?>
	</div>
	<hr>
	<div class="wsfw-secion-daily-visit">
	  <span><b><?php esc_html_e( 'Credit Amount On Comment', 'wallet-system-for-woocommerce' ); ?></b></span>
		<?php
			$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_comment_settings );
		if ( ! empty( $wsfw_wallet_action_html ) ) {
			echo wp_kses_post( $wsfw_wallet_action_html );
		}
		?>
	</div>
	<hr>
	<?php
	$is_pro = false;
	$is_pro = apply_filters( 'wsfw_check_pro_plugin', $is_pro );
	if ( $is_pro ) {
		$wsfw_wallet_action_settings_fee_setting = '';
		$wsfw_wallet_action_settings_fee_setting = apply_filters( 'wsfw_wallet_action_settings_fee_setting', $wsfw_wallet_action_settings_fee_setting );
		if ( ! empty( $wsfw_wallet_action_settings_fee_setting ) ) {
			echo wp_kses_post( $wsfw_wallet_action_settings_fee_setting );
		}
	} else {
			$wsfw_wallet_action_withdrawal_fee_settings = apply_filters( 'wsfwp_wallet_action_settings_withdrawal_array', array() );
			$wsfw_wallet_action_transfer_fee_settings = apply_filters( 'wsfwp_wallet_action_settings_transfer_array', array() );
			$wsfw_wallet_action_refer_friend_settings      = apply_filters( 'wsfw_wallet_action_settings_refer_friend_array', array() );
			$wsfw_wallet_action_different_layout_settings      = apply_filters( 'wsfw_wallet_action_different_layout_settings_array', array() );
			global $wsfwp_wps_wsfwp_obj;
			$wallet_id          = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		?>

		<div class="wsfw-secion-refer-customize-wallet">
				<span><b><?php esc_html_e( 'Customize Your Wallet Rechargeable Product', 'wallet-system-for-woocommerce' ); ?></b></span>
				<div class="wps-form-group wps_pro_settings_tag">
					<div class="wps-form-group__label ">
						<label for="" class="wps-form-label"><?php esc_html_e( 'Wallet Recharge Product', 'wallet-system-for-woocommerce' ); ?></label>
					</div>
					<div class="wps-form-group__control">
						<a  href="#"><div class="wps_pro_settings"> <?php esc_html_e( 'Click Here', 'wallet-system-for-woocommerce' ); ?></div></a>
					</div>
				</div>
			</div>
			<hr>

			<div class="wsfw-secion-refer-withdrawal-fees">
				<span><b><?php esc_html_e( 'Wallet Withdrawal Fee Setting', 'wallet-system-for-woocommerce' ); ?></b></span>
				<?php
					$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_withdrawal_fee_settings );

				if ( ! empty( $wsfw_wallet_action_html ) ) {
					echo wp_kses_post( $wsfw_wallet_action_html );
				}
				?>
				</br>
			</div>
			<hr>
			<div class="wsfw-secion-refer-transfer-fees">
			<span><b><?php esc_html_e( 'Wallet Transfer Fee Setting', 'wallet-system-for-woocommerce' ); ?></b></span>
				<?php
					$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_transfer_fee_settings );
				if ( ! empty( $wsfw_wallet_action_html ) ) {
					echo wp_kses_post( $wsfw_wallet_action_html );
				}
				?>
				</br>
			</div>
			<hr>
			<div class="wsfw-secion-refer-friend">
			  <span><b><?php esc_html_e( 'Credit Amount On Refer A Friend', 'wallet-system-for-woocommerce' ); ?></b></span>
				<?php
					  $wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_refer_friend_settings );
				if ( ! empty( $wsfw_wallet_action_html ) ) {
					echo wp_kses_post( $wsfw_wallet_action_html );
				}
				?>
			</div>
			<hr>
			<div class="wsfw-secion-refer-friend">
			  <span><b><?php esc_html_e( 'Wallet Layout Settings', 'wallet-system-for-woocommerce' ); ?></b></span>
				<?php
					  $wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_different_layout_settings );
				if ( ! empty( $wsfw_wallet_action_html ) ) {
					echo wp_kses_post( $wsfw_wallet_action_html );
				}
				?>
			</div>
		<hr>
		<?php
	}
	?>
<div class="wps-wallet-action-wrap">
	<?php

		$wsfw_wallet_action_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_wallet_action_settings_submit_button_array );
	if ( ! empty( $wsfw_wallet_action_html ) ) {
		echo wp_kses_post( $wsfw_wallet_action_html );
	}
	?>
	</div>

		<input type="hidden" id="updatenoncewallet_action" name="updatenoncewallet_action" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>
