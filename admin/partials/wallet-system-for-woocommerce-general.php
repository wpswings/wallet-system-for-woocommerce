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

if ( isset( $_POST['wsfw_button_demo'] ) ) {
	$nonce = ( isset( $_POST['updatenonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenonce'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );
		$wsfw_plugin_admin->wsfw_admin_save_tab_settings();
	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}

$wsfw_genaral_settings = apply_filters( 'wsfw_general_settings_array', array() );

$wsfw_general_submit_button_array = array();
if ( is_array( $wsfw_genaral_settings ) && ! empty( $wsfw_genaral_settings ) ) {
	$wsfw_general_submit_button_array = array( array_pop( $wsfw_genaral_settings ) );
}

$wsfw_general_field_pool = $wsfw_genaral_settings;

$wsfw_general_group_wallet_recharge = wsfw_general_extract_group(
	$wsfw_general_field_pool,
	array( 'wps_wsfw_enable', 'wsfw_enable_wallet_recharge', 'wps_wsfw_multiselect_wallet_recharge_restrict', 'wsfw_enable_wallet_recharge_tax_free' )
);

$wsfw_general_group_order_payment = wsfw_general_extract_group(
	$wsfw_general_field_pool,
	array(
		'wsfw_wallet_payment_order_status_checkout',
		'wsfw_wallet_payment_refund_order_payment',
		'wsfw_wallet_payment_checkout_field_checkout',
		'wsfw_wallet_partial_payment_method_enabled',
		'wsfw_wallet_partial_payment_method_options',
	)
);

$wsfw_general_group_notifications = wsfw_general_extract_group(
	$wsfw_general_field_pool,
	array( 'wps_wsfw_enable_email_notification_for_wallet_update', 'wps_wsfw_enable_email_address_value_for_wallet_amount' )
);

$wsfw_general_group_shortcodes = wsfw_general_extract_group(
	$wsfw_general_field_pool,
	array( 'wsfw_wallet_script_for_account_enabled', 'wsfw_wallet_shortcode' )
);

$wsfw_general_group_recharge_automation = wsfw_general_extract_group(
	$wsfw_general_field_pool,
	array(
		'wsfw_wallet_recharge_order_status_checkout',
		'wps_wsfw_wallet_order_auto_process',
		'wsfwp_withdrawal_page_message',
		'wsfwp_withdrawal_admin_withdrawal_request_email',
	)
);

// Anything left over (e.g. added later via a general-settings filter) still renders, just ungrouped.
$wsfw_general_group_additional = $wsfw_general_field_pool;
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
		<?php if ( ! empty( $wsfw_general_group_wallet_recharge ) ) : ?>
		<div class="wsfw-secion-general-wallet-recharge">
			<span><b><?php esc_html_e( 'Wallet & Recharge', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_general_group_wallet_recharge );
			if ( ! empty( $wsfw_general_html ) ) {
				echo wp_kses_post( $wsfw_general_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_general_group_order_payment ) ) : ?>
		<div class="wsfw-secion-general-order-payment">
			<span><b><?php esc_html_e( 'Order & Payment Behavior', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_general_group_order_payment );
			if ( ! empty( $wsfw_general_html ) ) {
				echo wp_kses_post( $wsfw_general_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_general_group_notifications ) ) : ?>
		<div class="wsfw-secion-general-notifications">
			<span><b><?php esc_html_e( 'Notifications', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_general_group_notifications );
			if ( ! empty( $wsfw_general_html ) ) {
				echo wp_kses_post( $wsfw_general_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_general_group_shortcodes ) ) : ?>
		<div class="wsfw-secion-general-shortcodes">
			<span><b><?php esc_html_e( 'My Account & Shortcodes', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_general_group_shortcodes );
			if ( ! empty( $wsfw_general_html ) ) {
				echo wp_kses_post( $wsfw_general_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_general_group_recharge_automation ) ) : ?>
		<div class="wsfw-secion-general-recharge-automation">
			<span><b><?php esc_html_e( 'Recharge Automation & Withdrawal Messaging', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_general_group_recharge_automation );
			if ( ! empty( $wsfw_general_html ) ) {
				echo wp_kses_post( $wsfw_general_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_general_group_additional ) ) : ?>
		<div class="wsfw-secion-general-additional">
			<span><b><?php esc_html_e( 'Additional Settings', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_general_group_additional );
			if ( ! empty( $wsfw_general_html ) ) {
				echo wp_kses_post( $wsfw_general_html );
			}
			?>
		</div>
		<?php endif; ?>

	<div class="wps-wallet-action-wrap">
		<?php
			$wsfw_general_submit_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_general_submit_button_array );
		if ( ! empty( $wsfw_general_submit_html ) ) {
			echo wp_kses_post( $wsfw_general_submit_html );
		}
		?>
	</div>
		<input type="hidden" id="updatenonce" name="updatenonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>
<?php
/**
 * Pull fields matching the given names (falling back to id when name is absent) out of the pool array, preserving order.
 *
 * @param array $pool  Field array, passed by reference; matched entries are removed.
 * @param array $names Field 'name' (or 'id', if 'name' is absent) values to extract into this group.
 * @return array Extracted fields, in pool order.
 */
function wsfw_general_extract_group( array &$pool, array $names ) {
	$group = array();
	foreach ( $pool as $key => $field ) {
		$identifier = ! empty( $field['name'] ) ? $field['name'] : ( $field['id'] ?? null );
		if ( $identifier && in_array( $identifier, $names, true ) ) {
			$group[] = $field;
			unset( $pool[ $key ] );
		}
	}
	return $group;
}
