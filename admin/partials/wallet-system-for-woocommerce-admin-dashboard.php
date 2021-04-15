<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

global $wsfw_mwb_wsfw_obj;
$wsfw_active_tab   = isset( $_GET['wsfw_tab'] ) ? sanitize_key( $_GET['wsfw_tab'] ) : 'wallet-system-for-woocommerce-general';
$wsfw_default_tabs = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_default_tabs();
?>
<header>
	<div class="mwb-header-container mwb-bg-white mwb-r-8">
		<h1 class="mwb-header-title"><?php echo esc_attr( strtoupper( str_replace( '-', ' ', $wsfw_mwb_wsfw_obj->wsfw_get_plugin_name() ) ) ); ?></h1>
		<a href="https://docs.makewebbetter.com/" target="_blank" class="mwb-link"><?php esc_html_e( 'Documentation', 'wallet-system-for-woocommerce' ); ?></a>
		<span>|</span>
		<a href="https://makewebbetter.com/contact-us/" target="_blank" class="mwb-link"><?php esc_html_e( 'Support', 'invoice-system-for-woocommerce' ); ?></a>
	</div>
</header>

<main class="mwb-main mwb-r-8">
	<nav class="mwb-navbar">
		<ul class="mwb-navbar__items">
			<?php
			if ( is_array( $wsfw_default_tabs ) && ! empty( $wsfw_default_tabs ) ) {

				foreach ( $wsfw_default_tabs as $wsfw_tab_key => $wsfw_default_tabs ) {

					$wsfw_tab_classes = 'mwb-link ';

					if ( ! empty( $wsfw_active_tab ) && $wsfw_active_tab === $wsfw_tab_key ) {
						$wsfw_tab_classes .= 'active';
					}
					?>
					<li>
						<a id="<?php echo esc_attr( $wsfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ) . '&wsfw_tab=' . esc_attr( $wsfw_tab_key ) ); ?>" class="<?php echo esc_attr( $wsfw_tab_classes ); ?>"><?php echo esc_html( $wsfw_default_tabs['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>

	<section class="mwb-section">
		<div>
			<?php 
				do_action( 'mwb_wsfw_before_general_settings_form' );
						// if submenu is directly clicked on woocommerce.
				if ( empty( $wsfw_active_tab ) ) {
					$wsfw_active_tab = 'mwb_wsfw_plug_general';
				}

						// look for the path based on the tab id in the admin templates.
				$wsfw_tab_content_path = 'admin/partials/' . $wsfw_active_tab . '.php';

				$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_load_template( $wsfw_tab_content_path );

				do_action( 'mwb_wsfw_after_general_settings_form' ); 
			?>
		</div>
	</section>
</main>
