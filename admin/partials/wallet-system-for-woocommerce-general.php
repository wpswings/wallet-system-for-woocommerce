<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
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

if ( isset( $_POST['wsfw_button_demo'] ) ) {
	$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );
	$wsfw_plugin_admin->wsfw_admin_save_tab_settings();
}

$wsfw_genaral_settings = apply_filters( 'wsfw_general_settings_array', array() );
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="mwb-wsfw-gen-section-form">
	<div class="wsfw-secion-wrap">
		<?php
		$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_genaral_settings );
		echo esc_html( $wsfw_general_html );
		?>
	</div>
</form>