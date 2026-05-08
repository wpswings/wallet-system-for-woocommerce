<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to show overview content
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

?>

<div class="wps-overview__wrapper wps-wallet-overview">
	<section class="wps-wallet-overview-hero">
		<div class="wps-wallet-overview-brand"><?php esc_html_e( 'Wallet System For WooCommerce', 'wallet-system-for-woocommerce' ); ?></div>
		<span class="wps-wallet-overview-kicker"><?php esc_html_e( 'Overview', 'wallet-system-for-woocommerce' ); ?></span>
		<h2><?php esc_html_e( 'Store credit and wallet workflows built for WooCommerce teams', 'wallet-system-for-woocommerce' ); ?></h2>
		<p><?php esc_html_e( 'Wallet System for WooCommerce centralizes customer balances, wallet top-ups, wallet payments, transfers, withdrawals, and transaction visibility so your store can manage digital wallet commerce with fewer manual steps.', 'wallet-system-for-woocommerce' ); ?></p>
	</section>

	<section class="wps-wallet-overview-features" aria-labelledby="wps-wallet-overview-features-title">
		<h3 id="wps-wallet-overview-features-title"><?php esc_html_e( 'Top features of this plugin', 'wallet-system-for-woocommerce' ); ?></h3>
		<div class="wps-wallet-overview-feature-grid">
			<div class="wps-wallet-overview-feature-card">
				<span class="material-icons" aria-hidden="true">account_balance_wallet</span>
				<h4><?php esc_html_e( 'Customer wallet balances', 'wallet-system-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Let registered customers maintain a store wallet and use available balance during WooCommerce purchases.', 'wallet-system-for-woocommerce' ); ?></p>
			</div>
			<div class="wps-wallet-overview-feature-card">
				<span class="material-icons" aria-hidden="true">payments</span>
				<h4><?php esc_html_e( 'Wallet top-up payments', 'wallet-system-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Customers can recharge wallet funds through the payment methods already enabled on your store.', 'wallet-system-for-woocommerce' ); ?></p>
			</div>
			<div class="wps-wallet-overview-feature-card">
				<span class="material-icons" aria-hidden="true">receipt_long</span>
				<h4><?php esc_html_e( 'Transaction history', 'wallet-system-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Review wallet debit, credit, recharge, transfer, and payment activity from organized admin records.', 'wallet-system-for-woocommerce' ); ?></p>
			</div>
			<div class="wps-wallet-overview-feature-card">
				<span class="material-icons" aria-hidden="true">swap_horiz</span>
				<h4><?php esc_html_e( 'Wallet transfers', 'wallet-system-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Allow customers to send wallet credit to other users while keeping a visible activity trail.', 'wallet-system-for-woocommerce' ); ?></p>
			</div>
			<div class="wps-wallet-overview-feature-card">
				<span class="material-icons" aria-hidden="true">notifications_active</span>
				<h4><?php esc_html_e( 'Wallet notifications', 'wallet-system-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Keep customers informed about wallet credit, debit, top-up, and deduction events with email notifications.', 'wallet-system-for-woocommerce' ); ?></p>
			</div>
			<div class="wps-wallet-overview-feature-card">
				<span class="material-icons" aria-hidden="true">settings</span>
				<h4><?php esc_html_e( 'Store controls and shortcodes', 'wallet-system-for-woocommerce' ); ?></h4>
				<p><?php esc_html_e( 'Configure wallet behavior, expose the customer wallet with shortcode support, and manage core wallet operations from one place.', 'wallet-system-for-woocommerce' ); ?></p>
			</div>
		</div>
	</section>

	<section class="wps-wallet-overview-support">
		<div>
			<h3><?php esc_html_e( 'Facing issues?', 'wallet-system-for-woocommerce' ); ?></h3>
			<p><?php esc_html_e( 'We are ready to help you align wallet operations, payment behavior, customer communication, and store credit workflows.', 'wallet-system-for-woocommerce' ); ?></p>
		</div>
		<a href="https://wpswings.com/contact-us/" target="_blank"><?php esc_html_e( 'Contact Support', 'wallet-system-for-woocommerce' ); ?></a>
	</section>
</div>
