

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
?>
<div class="wps-wpg-gen-section-form-container">
	<div class="wpg-secion-wrap">
		<h3><?php esc_html_e( 'Wallet Report', 'wallet-system-for-woocommerce-pro' ); ?></h3>
        <div id="react-app"></div>
	</div>
    <input type="hidden" id="report_userid" name="report_userid" value="<?php echo  isset( $_GET['report_userid'] ) ? sanitize_text_field( wp_unslash( $_GET['report_userid'] ) ) : null; ?>"  >     

</div>  


