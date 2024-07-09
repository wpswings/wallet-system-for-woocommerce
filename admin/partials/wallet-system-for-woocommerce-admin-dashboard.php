<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}
$secure_nonce      = wp_create_nonce( 'wps-wallet-general-dashboard-nonce' );
$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-general-dashboard-nonce' );
if ( ! $id_nonce_verified ) {
	wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
}
global $wsfw_wps_wsfw_obj;
$wsfw_active_tab   = isset( $_GET['wsfw_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['wsfw_tab'] ) ) : 'wallet-system-for-woocommerce-general';
$wsfw_default_tabs = $wsfw_wps_wsfw_obj->wps_wsfw_plug_default_tabs();
$show_additional_section = apply_filters( 'wps_wsfw_show_additional_section', '' );
$wallet_payment_enable = get_option( 'woocommerce_wps_wcb_wallet_payment_gateway_settings' );

// phpcs:ignore

?>
	<div class="wps-header-container wps-bg-white wps-r-8">
		<h1 class="wps-header-title">
			<?php
			if ( ! $wallet_payment_enable || 'no' == $wallet_payment_enable['enabled'] ) {
				?>
			<p>
				<?php printf( esc_html__( 'Please configure your Wallet Payment Gateway settings.', 'wallet-system-for-woocommerce' ) ); ?>

			</p>
				<?php
			}
			?>
			<p>
				<?php printf( esc_html__( "Kindly refrain from removing the Wallet Recharge Product, as its deletion could have a significant impact on the entire Plugin's functionality.", 'wallet-system-for-woocommerce' ) ); ?>

			</p>
			
		</h1>
	</div>
	
<header>
	<div class="wps-header-container wps-bg-white wps-r-8">
		<?php
		$check = false;
		$check  = apply_filters( 'wsfw_check_pro_plugin', $check );

		if ( $check ) {
			?>
				<h1  class="wps-header-title"><?php echo esc_attr__( 'WALLET SYSTEM FOR WOOCOMMERCE PRO', 'wallet-system-for-woocommerce' ); ?></h1>
				<a href="https://docs.wpswings.com/wallet-system-for-woocommerce-pro/?utm_source=wallet-pro-backend&utm_medium=referral&utm_campaign=wallet-pro-doc" target="_blank" class="wps-link"><?php esc_html_e( 'Documentation', 'wallet-system-for-woocommerce' ); ?></a>
				<span>|</span>
				<a href="https://youtu.be/mnMfoSL0aZc" target="_blank" class="wps-link"><?php esc_html_e( 'Video', 'wallet-system-for-woocommerce' ); ?></a>
				<span>|</span>
				<a href="https://wpswings.com/contact-us/" target="_blank" class="wps-link"><?php esc_html_e( 'Support', 'wallet-system-for-woocommerce' ); ?></a>
	
			<?php

		} else {
			?>
				<h1  class="wps-header-title"><?php echo esc_attr( strtoupper( str_replace( '-', ' ', $wsfw_wps_wsfw_obj->wsfw_get_plugin_name() ) ) ); ?></h1>
				<a href="https://docs.wpswings.com/wallet-system-for-woocommerce/?utm_source=wpswings-wallet-doc&utm_medium=wallet-org-backend&utm_campaign=wallet-doc" target="_blank" class="wps-link"><?php esc_html_e( 'Documentation', 'wallet-system-for-woocommerce' ); ?></a>
				<span>|</span>
				<a href="https://youtu.be/C5mwA5kttRU" target="_blank" class="wps-link"><?php esc_html_e( 'Video', 'wallet-system-for-woocommerce' ); ?></a>
				<span>|</span>
				<a href="https://wpswings.com/contact-us/" target="_blank" class="wps-link"><?php esc_html_e( 'Support', 'wallet-system-for-woocommerce' ); ?></a>
	
			<?php
		}

		?>
		</div>
</header>

<main class="wps-main wps-r-8">
	<nav class="wps-navbar">
		<ul class="wps-navbar__items">
			<?php
			if ( is_array( $wsfw_default_tabs ) && ! empty( $wsfw_default_tabs ) ) {

				foreach ( $wsfw_default_tabs as $wsfw_tab_key => $wsfw_default_tabs ) {

					$wsfw_tab_classes = 'wps-link ';

					if ( ! empty( $wsfw_active_tab ) && $wsfw_active_tab === $wsfw_tab_key ) {
						$wsfw_tab_classes .= 'active';
					}
					?>
					<li class="wps_class_li_<?php echo esc_attr( $wsfw_tab_key ); ?>">
						<a id="<?php echo esc_attr( $wsfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ) . '&wsfw_tab=' . esc_attr( $wsfw_tab_key ) ); ?>" class="<?php echo esc_attr( $wsfw_tab_classes ); ?>"><?php echo esc_html( $wsfw_default_tabs['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>

	<section class="wps-section <?php echo esc_html( $wsfw_active_tab ); ?>" >
		<div>
			<?php
			do_action( 'wps_wsfw_before_general_settings_form' );
			// if submenu is directly clicked on woocommerce.
			if ( empty( $wsfw_active_tab ) ) {
				$wsfw_active_tab = 'wps_wsfw_plug_general';
			}

			// look for the path based on the tab id in the admin templates.

			$wsfw_tab_content_path = 'admin/partials/' . $wsfw_active_tab . '.php';
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_load_template( $wsfw_tab_content_path );

			do_action( 'wps_wsfw_after_general_settings_form' );
			?>
		</div>
	</section>
</main>
