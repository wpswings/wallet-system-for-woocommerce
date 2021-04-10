<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for showing wallet withdrawal setting
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wsfw_mwb_wsfw_obj;

if ( isset( $_POST['save_withdrawn_settings'] ) && ! empty( $_POST['save_withdrawn_settings'] ) ) {
	unset( $_POST['save_withdrawn_settings'] );
	if ( empty( $_POST['wallet_withdraw_methods'] ) ) {
		$mwb_wsfw_error_text = esc_html__( 'Select any payment method.', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
	} else {
		foreach ( $_POST as $key => $value ) {
			if ( 'wallet_withdraw_methods' === $key ) {
				$method = array();
				foreach( $value as $method_key => $method_value ) {
					
					$method[$method_key]['name'] = $method_value;
					$method[$method_key]['value'] = 1;
				}
				
				update_option( $key, $method );
			} else {
				update_option( $key, $value );
			}
			
		}
		$mwb_wsfw_error_text = esc_html__( 'Save settings.', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
	}
	
}

$wsfw_withdrawal_settings = apply_filters( 'wsfw_wallet_withdrawal_array', array() );


?>
<!--  template file for admin settings. -->

<form action="" method="POST" class="mwb-wpg-gen-section-form">
	<div class="wpg-secion-wrap">
    <h3><?php esc_html_e( 'Wallet Withdrawal Setting' , 'wallet-system-for-woocommerce' ); ?></h3>
		<?php
		$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_withdrawal_settings );
		echo esc_html( $wsfw_general_html );
		?>
	</div>
</form>
