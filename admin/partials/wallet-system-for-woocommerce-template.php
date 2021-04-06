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
$wsfw_template_settings = apply_filters( 'wsfw_template_settings_array', array() );
?>
<!--  template file for admin settings. -->
<div class="wsfw-section-wrap">
	<?php
		$wsfw_template_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_template_settings );
		echo esc_html( $wsfw_template_html );
	?>
</div>
