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
global $wsfw_wps_wsfw_obj, $wsfwp_wps_wsfwp_obj;
$wsfw_active_tab   = isset( $_GET['wsfw_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['wsfw_tab'] ) ) : 'wallet-system-for-woocommerce-general';
$wsfw_default_tabs = $wsfw_wps_wsfw_obj->wps_wsfw_plug_default_tabs();
$show_additional_section = apply_filters( 'wps_wsfw_show_additional_section', '' );
$wallet_payment_enable = get_option( 'woocommerce_wps_wcb_wallet_payment_gateway_settings' );
$check = false;
$check = apply_filters( 'wsfw_check_pro_plugin', $check );
$wsfw_plugin_title = $check ? __( 'Wallet System For WooCommerce Pro', 'wallet-system-for-woocommerce' ) : __( 'Wallet System For WooCommerce', 'wallet-system-for-woocommerce' );
$wsfw_docs_url = 'https://docs.wpswings.com/wallet-system-for-woocommerce/?utm_source=wpswings-wallet-doc&utm_medium=wallet-org-backend&utm_campaign=wallet-doc';
$wsfw_plugin_version = $wsfw_wps_wsfw_obj->wsfw_get_version();
$wsfw_plugin_edition = __( 'Lite', 'wallet-system-for-woocommerce' );

if ( $check ) {
	$wsfw_plugin_edition = __( 'Pro', 'wallet-system-for-woocommerce' );
	if ( isset( $wsfwp_wps_wsfwp_obj ) && is_object( $wsfwp_wps_wsfwp_obj ) && method_exists( $wsfwp_wps_wsfwp_obj, 'wsfwp_get_version' ) ) {
		$wsfw_plugin_version = $wsfwp_wps_wsfwp_obj->wsfwp_get_version();
	} elseif ( defined( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_PRO_VERSION' ) ) {
		$wsfw_plugin_version = WALLET_SYSTEM_FOR_WOOCOMMERCE_PRO_VERSION;
	}
}

$wsfw_plugin_version_label = sprintf(
	/* translators: 1: plugin version, 2: Lite or Pro edition. */
	__( 'v%1$s %2$s', 'wallet-system-for-woocommerce' ),
	$wsfw_plugin_version,
	$wsfw_plugin_edition
);

$wsfw_tab_title = isset( $wsfw_default_tabs[ $wsfw_active_tab ]['title'] ) ? $wsfw_default_tabs[ $wsfw_active_tab ]['title'] : __( 'Settings', 'wallet-system-for-woocommerce' );
if ( 'wallet-system-for-woocommerce-pro-license' === $wsfw_active_tab ) {
	$wsfw_tab_title = __( 'License', 'wallet-system-for-woocommerce' );
}
$wsfw_tab_intro_descriptions = array(
	'wallet-system-for-woocommerce-overview'      => __( 'Review the wallet plugin capabilities, available workflows, and key store tools.', 'wallet-system-for-woocommerce' ),
	'wallet-system-for-woocommerce-general'       => __( 'Control the base wallet behavior, customer access, emails, recharge flow, and storefront settings.', 'wallet-system-for-woocommerce' ),
	'wallet-system-for-woocommerce-wallet'        => __( 'Manage wallet balances, user wallet actions, imports, exports, and customer wallet controls.', 'wallet-system-for-woocommerce' ),
	'class-wallet-transaction-list-table'         => __( 'Review wallet transaction history, filter activity, and export transaction records.', 'wallet-system-for-woocommerce' ),
	'wallet-system-withdrawal-request'            => __( 'Review customer withdrawal requests and keep payout workflows organized.', 'wallet-system-for-woocommerce' ),
	'wallet-system-withdrawal-setting'            => __( 'Configure withdrawal behavior, request rules, limits, and admin notifications.', 'wallet-system-for-woocommerce' ),
	'wallet-system-wallet-regulation'             => __( 'Define customer wallet restrictions, compliance settings, and usage controls.', 'wallet-system-for-woocommerce' ),
	'wallet-system-for-woocommerce-wallet-actions' => __( 'Configure wallet rewards, dashboard layout, fees, notifications, and customer action rules.', 'wallet-system-for-woocommerce' ),
	'wallet-system-for-woocommerce-report'        => __( 'View wallet balance reports and compare credit, debit, cashback, withdrawal, and current wallet totals.', 'wallet-system-for-woocommerce' ),
	'wallet-system-for-woocommerce-pro-license'   => __( 'Validate the pro license, unlock premium capabilities, and keep the wallet commerce toolset active.', 'wallet-system-for-woocommerce' ),
);
$wsfw_tab_intro_description = isset( $wsfw_tab_intro_descriptions[ $wsfw_active_tab ] ) ? $wsfw_tab_intro_descriptions[ $wsfw_active_tab ] : sprintf(
	/* translators: %s: tab title. */
	__( 'Manage %s settings and keep the wallet experience aligned with your store workflow.', 'wallet-system-for-woocommerce' ),
	$wsfw_tab_title
);

// phpcs:ignore

?>
<div class="wps-wallet-admin-shell">
	<div class="wps-wallet-announcement">
		<span class="wps-wallet-announcement__badge"><?php esc_html_e( 'NEW LAYOUT', 'wallet-system-for-woocommerce' ); ?></span>
		<div class="wps-wallet-announcement__content">
			<h2><?php esc_html_e( 'Aurora Luxe is now available across your wallet flows', 'wallet-system-for-woocommerce' ); ?></h2>
			<p><?php esc_html_e( 'You can now manage wallet settings, customer controls, payment actions, reports, and layout options from this refreshed admin experience.', 'wallet-system-for-woocommerce' ); ?></p>
		</div>
		<button type="button" class="wps-wallet-announcement__dismiss"><?php esc_html_e( 'Dismiss', 'wallet-system-for-woocommerce' ); ?></button>
	</div>

	<header class="wps-wallet-hero">
		<span class="wps-wallet-badge"><?php echo esc_html( $check ? __( 'PRO ACTIVE', 'wallet-system-for-woocommerce' ) : __( 'FREE ACTIVE', 'wallet-system-for-woocommerce' ) ); ?></span>
		<h1><?php echo esc_html( $wsfw_plugin_title ); ?></h1>
	</header>

	<div class="wps-wallet-alert-stack">
		<?php if ( ! is_array( $wallet_payment_enable ) || 'no' === ( $wallet_payment_enable['enabled'] ?? 'no' ) ) { ?>
			<div class="wps-wallet-alert"><?php esc_html_e( 'Please configure your Wallet Payment Gateway settings.', 'wallet-system-for-woocommerce' ); ?></div>
		<?php } ?>
		<div class="wps-wallet-alert"><?php esc_html_e( "Kindly refrain from removing the Wallet Recharge Product, as its deletion could have a significant impact on the entire plugin's functionality.", 'wallet-system-for-woocommerce' ); ?></div>
	</div>

	<main class="wps-main wps-wallet-panel wps-r-8">
	<nav class="wps-navbar">
		<ul class="wps-navbar__items">
			<li class="wps-wallet-version"><?php echo esc_html( $wsfw_plugin_version_label ); ?></li>
			<?php
			if ( is_array( $wsfw_default_tabs ) && ! empty( $wsfw_default_tabs ) ) {
				$wsfw_visible_tabs  = $wsfw_default_tabs;
				$wsfw_overflow_tabs = array();

				if ( count( $wsfw_default_tabs ) > 9 ) {
					$wsfw_visible_tabs  = array_slice( $wsfw_default_tabs, 0, 8, true );
					$wsfw_overflow_tabs = array_slice( $wsfw_default_tabs, 8, null, true );
				}

				foreach ( $wsfw_visible_tabs as $wsfw_tab_key => $wsfw_tab_data ) {

					$wsfw_tab_classes = 'wps-link ';

					if ( ! empty( $wsfw_active_tab ) && $wsfw_active_tab === $wsfw_tab_key ) {
						$wsfw_tab_classes .= 'active';
					}
					?>
					<li class="wps_class_li_<?php echo esc_attr( $wsfw_tab_key ); ?>">
						<a id="<?php echo esc_attr( $wsfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ) . '&wsfw_tab=' . esc_attr( $wsfw_tab_key ) ); ?>" class="<?php echo esc_attr( $wsfw_tab_classes ); ?>"><?php echo esc_html( $wsfw_tab_data['title'] ); ?></a>
					</li>
					<?php
				}

				if ( ! empty( $wsfw_overflow_tabs ) ) {
					$wsfw_more_active = array_key_exists( $wsfw_active_tab, $wsfw_overflow_tabs );
					?>
					<li class="wps-wallet-more-menu <?php echo esc_attr( $wsfw_more_active ? 'is-active' : '' ); ?>">
						<button type="button" class="wps-link wps-wallet-more-toggle <?php echo esc_attr( $wsfw_more_active ? 'active' : '' ); ?>" aria-haspopup="true" aria-expanded="false">
							<?php esc_html_e( 'More', 'wallet-system-for-woocommerce' ); ?>
							<span aria-hidden="true">▾</span>
						</button>
						<ul class="wps-wallet-more-list">
							<?php foreach ( $wsfw_overflow_tabs as $wsfw_tab_key => $wsfw_tab_data ) { ?>
								<li class="wps_class_li_<?php echo esc_attr( $wsfw_tab_key ); ?>">
									<a id="<?php echo esc_attr( $wsfw_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ) . '&wsfw_tab=' . esc_attr( $wsfw_tab_key ) ); ?>" class="wps-wallet-more-link <?php echo esc_attr( $wsfw_active_tab === $wsfw_tab_key ? 'active' : '' ); ?>"><?php echo esc_html( $wsfw_tab_data['title'] ); ?></a>
								</li>
							<?php } ?>
						</ul>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>

	<div class="wps-wallet-content-grid">
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
			?>
			<div class="wps-wallet-tab-intro">
				<div>
					<span><?php echo esc_html( strtoupper( $wsfw_tab_title ) ); ?></span>
					<h2><?php echo esc_html( $wsfw_tab_title ); ?></h2>
					<p><?php echo esc_html( $wsfw_tab_intro_description ); ?></p>
				</div>
				<a href="<?php echo esc_url( $wsfw_docs_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Read Documentation', 'wallet-system-for-woocommerce' ); ?></a>
			</div>
			<?php
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_load_template( $wsfw_tab_content_path );

			do_action( 'wps_wsfw_after_general_settings_form' );
			?>
			</div>
		</section>

		<aside class="wps-wallet-sidebar" aria-label="<?php esc_attr_e( 'Plugin help and services', 'wallet-system-for-woocommerce' ); ?>">
			<div class="wps-wallet-side-card">
				<h2><?php esc_html_e( 'Need help with this plugin?', 'wallet-system-for-woocommerce' ); ?></h2>
				<a href="https://youtu.be/C5mwA5kttRU" target="_blank"><?php esc_html_e( 'Watch Video', 'wallet-system-for-woocommerce' ); ?></a>
				<a href="<?php echo esc_url( $wsfw_docs_url ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'wallet-system-for-woocommerce' ); ?></a>
				<a href="https://wpswings.com/contact-us/" target="_blank"><?php esc_html_e( 'Support', 'wallet-system-for-woocommerce' ); ?></a>
			</div>
			<?php Wallet_System_For_Woocommerce_Talk_To_Expert_Form::get_instance()->render_sidebar_card(); ?>
			<div class="wps-wallet-side-card">
				<h2><?php esc_html_e( 'Explore more plugins', 'wallet-system-for-woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Discover additional commerce and automation plugins from the same product family.', 'wallet-system-for-woocommerce' ); ?></p>
				<a href="https://wpswings.com/woocommerce-plugins/" target="_blank" class="wps-wallet-side-button"><?php esc_html_e( 'View More Plugins', 'wallet-system-for-woocommerce' ); ?></a>
			</div>
		</aside>
	</div>
	</main>
	<?php Wallet_System_For_Woocommerce_Talk_To_Expert_Form::get_instance()->render_modal(); ?>
</div>
