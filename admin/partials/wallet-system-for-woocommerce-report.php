<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to show license tab content
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce_Pro
 * @subpackage Wallet_System_For_Woocommerce_Pro/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$user_id = isset( $_GET['report_userid'] ) ? sanitize_text_field( wp_unslash( $_GET['report_userid'] ) ) : null;
$nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : null;


if ( isset( $user_id, $nonce ) && wp_verify_nonce( $nonce, 'view_report_' . $user_id ) ) {
	$user_id = isset( $_GET['report_userid'] ) ? sanitize_text_field( wp_unslash( $_GET['report_userid'] ) ) : null;
}


?>
<div class="wps-wpg-gen-section-form-container">
	<div class="wpg-secion-wrap">
		<h3><?php esc_html_e( 'Wallet Report', 'wallet-system-for-woocommerce' ); ?></h3>
		<div id="react-app"></div>
	</div>
	<input type="hidden" id="report_userid" name="report_userid" value="<?php echo esc_attr( $user_id ); ?>"  >     

</div>  


