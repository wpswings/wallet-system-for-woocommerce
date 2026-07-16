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

if ( isset( $_POST['wsfw_button_cashback'] ) ) {
	$nonce = ( isset( $_POST['updatenoncecashback'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncecashback'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );
		$wsfw_plugin_admin->wsfw_admis_save_tab_settings_for_cashback();
	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}
$wsfw_cashback_settings = apply_filters( 'wsfw_cashback_settings_array', array() );

$wsfw_cashback_submit_button_array = array();
if ( is_array( $wsfw_cashback_settings ) && ! empty( $wsfw_cashback_settings ) ) {
	$wsfw_cashback_submit_button_array = array( array_pop( $wsfw_cashback_settings ) );
}

$wsfw_cashback_field_pool = $wsfw_cashback_settings;

$wsfw_cashback_group_enable = wsfw_cashback_extract_group(
	$wsfw_cashback_field_pool,
	array( 'wps_wsfw_enable_cashback', 'wps_wsfw_cashback_rule', 'wps_wsfw_multiselect_category_rule', 'wps_wsfw_multiselect_category' )
);

$wsfw_cashback_group_amount = wsfw_cashback_extract_group(
	$wsfw_cashback_field_pool,
	array( 'wps_wsfw_cashback_type', 'wps_wsfw_cashback_amount', 'wps_wsfw_cart_amount_min', 'wps_wsfw_cashback_amount_max' )
);

$wsfw_cashback_group_restrictions = wsfw_cashback_extract_group(
	$wsfw_cashback_field_pool,
	array( 'wps_wsfw_multiselect_cashback_restrict', 'wps_wsfw_enable_user_role_wise_cashback', 'wps_wsfw_user_role_cashback_restrict' )
);

$wsfw_cashback_group_messaging = wsfw_cashback_extract_group(
	$wsfw_cashback_field_pool,
	array( 'wps_wsfw_cashback_wallet_recharge', 'wps_wsfw_Gateway_Restriction_message_checkout', 'wps_wsfw_hide_cashback_cart', 'wps_wsfw_hide_cashback_checkout' )
);

// Anything left over (e.g. added later via the wsfw_cashback_extra_settings_array filter) still renders, just ungrouped.
$wsfw_cashback_group_additional = $wsfw_cashback_field_pool;
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
		<?php if ( ! empty( $wsfw_cashback_group_enable ) ) : ?>
		<div class="wsfw-secion-cashback-enable">
			<span><b><?php esc_html_e( 'Enable & Apply Cashback', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_cashback_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_cashback_group_enable );
			if ( ! empty( $wsfw_cashback_html ) ) {
				echo wp_kses_post( $wsfw_cashback_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_cashback_group_amount ) ) : ?>
		<div class="wsfw-secion-cashback-amount">
			<span><b><?php esc_html_e( 'Cashback Amount & Limits', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_cashback_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_cashback_group_amount );
			if ( ! empty( $wsfw_cashback_html ) ) {
				echo wp_kses_post( $wsfw_cashback_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_cashback_group_restrictions ) ) : ?>
		<div class="wsfw-secion-cashback-restrictions">
			<span><b><?php esc_html_e( 'Restrictions & Eligibility', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_cashback_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_cashback_group_restrictions );
			if ( ! empty( $wsfw_cashback_html ) ) {
				echo wp_kses_post( $wsfw_cashback_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_cashback_group_messaging ) ) : ?>
		<div class="wsfw-secion-cashback-messaging">
			<span><b><?php esc_html_e( 'Cashback Messaging & Recharge', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_cashback_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_cashback_group_messaging );
			if ( ! empty( $wsfw_cashback_html ) ) {
				echo wp_kses_post( $wsfw_cashback_html );
			}
			?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $wsfw_cashback_group_additional ) ) : ?>
		<div class="wsfw-secion-cashback-additional">
			<span><b><?php esc_html_e( 'Additional Settings', 'wallet-system-for-woocommerce' ); ?></b></span>
			<?php
			$wsfw_cashback_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_cashback_group_additional );
			if ( ! empty( $wsfw_cashback_html ) ) {
				echo wp_kses_post( $wsfw_cashback_html );
			}
			?>
		</div>
		<?php endif; ?>

	<div class="wps-wallet-action-wrap">
		<?php
			$wsfw_cashback_submit_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_cashback_submit_button_array );
		if ( ! empty( $wsfw_cashback_submit_html ) ) {
			echo wp_kses_post( $wsfw_cashback_submit_html );
		}
		?>
	</div>
		<input type="hidden" id="updatenoncecashback" name="updatenoncecashback" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
	</div>
</form>
<?php
/**
 * Pull fields matching the given names out of the pool array, preserving order.
 *
 * @param array $pool  Field array, passed by reference; matched entries are removed.
 * @param array $names Field 'name' values to extract into this group.
 * @return array Extracted fields, in pool order.
 */
function wsfw_cashback_extract_group( array &$pool, array $names ) {
	$group = array();
	foreach ( $pool as $key => $field ) {
		if ( isset( $field['name'] ) && in_array( $field['name'], $names, true ) ) {
			$group[] = $field;
			unset( $pool[ $key ] );
		}
	}
	return $group;
}
