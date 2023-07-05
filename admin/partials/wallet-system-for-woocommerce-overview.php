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

<div class="wps-overview__wrapper">
	<div class="wps-overview__banner">


	<?php
		$check = false;
		$check  = apply_filters( 'wsfw_check_pro_plugin', $check );

	if ( $check ) {
		?>
				<img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>admin/image/Wallet-System-for-WooCommerce-Pro-banner-1200x389.webp" alt="Overview banner image">
			<?php

	} else {
		?>
				<img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>admin/image/org-banner.jpg" alt="Overview banner image">
			<?php
	}
	?>
<?php
		$additional_content = apply_filters( 'wps_wsfw_overview_additional_content', '' );
?>
	</div>
	<div class="wps-overview__content">
		<div class="wps-overview__content-description">
			<h2><?php esc_html_e( 'What is Wallet System for WooCommerce Plugin? ', 'wallet-system-for-woocommerce' ); ?></h2>
			<p>
				<?php
				esc_html_e(
					'Wallet System for WooCommerce is a digital wallet plugin. It allows your registered customers to create a digital wallet on your WooCommerce store. Customers can purchase your products and services using the digital wallet amount. The customers can add money to their WooCommerce wallet through the available payment methods. And also, see the list of Transactions made using the wallet money.',
					'wallet-system-for-woocommerce'
				);
				?>
			</p>
			<div class="wps-wsfsw-iframe-box">

				<iframe width="560" height="315" src="https://www.youtube.com/embed/C5mwA5kttRU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
				</div>
			<h3><?php esc_html_e( 'With our Wallet System for WooCommerce, You Can:', 'wallet-system-for-woocommerce' ); ?></h3>
			<ul class="wps-overview__features">
				<li><?php esc_html_e( 'Add or remove funds to the wallets of your customers in bulk', 'wallet-system-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'Notify customers on every wallet transaction, wallet top-up, and wallet amount deduction through email notifications.', 'wallet-system-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'View the wallet transaction history and wallet balance of your customers.', 'wallet-system-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'View all wallet recharge orders (top-up by customers) in a separate order list.', 'wallet-system-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'Allow your customers to transfer their wallet amount into other customers’ wallets.', 'wallet-system-for-woocommerce' ); ?></li>
				<li>
				<?php
				esc_html_e( 'Have compatibility with the ', 'wallet-system-for-woocommerce' );
				echo '<a href="https://wordpress.org/plugins/woocommerce-currency-switcher/" target="blank" >FOX – Currency Switcher Professional for WooCommerce</a>.';
				?>
				</li>
				<li>
				<?php
				esc_html_e( 'Have compatibility with the ', 'wallet-system-for-woocommerce' );
				echo '<a href="https://wordpress.org/plugins/woo-gift-cards-lite/" target="blank" >Ultimate Gift Cards for WooCommerce </a>.';
				?>
				</li>
				<li>
				<?php
				esc_html_e( 'Have compatibility with the ', 'wallet-system-for-woocommerce' );
				echo '<a href="https://wpswings.com/product/gift-cards-for-woocommerce-pro/?utm_source=wpswings-giftcards-pro&utm_medium=wallet-org-backend&utm_campaign=giftcards-pro" target="blank" >Gift Cards For WooCommerce Pro </a>.';
				?>
				</li>
				<li>
				<?php
				esc_html_e( 'Compatible with ', 'wallet-system-for-woocommerce' );
				echo '<a href="https://wordpress.org/plugins/subscriptions-for-woocommerce/" target="blank" >Subscriptions For WooCommerce</a> plugin.';
				?>
				</li>
				<li><?php esc_html_e( 'Compatible with the WPML plugin.', 'wallet-system-for-woocommerce' ); ?></li>
				<li><?php esc_html_e( 'Supports WordPress multisite network.', 'wallet-system-for-woocommerce' ); ?></li>
				<li>
				<?php
				esc_html_e( 'Use the shortcode ', 'wallet-system-for-woocommerce' );
				echo '<strong>[wps-wallet]</strong>';
				esc_html_e( ' to display the user wallet on any page.', 'wallet-system-for-woocommerce' );
				?>
				</li>
			</ul>
		</div> 
		<h2> <?php esc_html_e( 'The Free Plugin Benefits', 'wallet-system-for-woocommerce' ); ?></h2>
		<div class="wps-overview__keywords">
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Top-up_Payment_methods.jpg' ); ?>" alt="Top-up Payment methods">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php esc_html_e( 'Top-up Payment methods', 'wallet-system-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e(
								'Your customers can top-up funds into their WooCommerce wallets using any payment method allowed on your WooCommerce store. It provides flexibility to your customers as they can recharge their wallets using different payment methods.',
								'wallet-system-for-woocommerce'
							);
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Wallet_Transaction_History_for_Customers.jpg' ); ?>" alt="Wallet Transaction">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php esc_html_e( 'Wallet Transaction History for Customers', 'wallet-system-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description"><?php esc_html_e( 'The wallet system is secure and transparent. Customers can see their transactions made using the wallet. The transaction list contains debit and credit details. It allows your customers to track their spending and helps them check if any unauthorized transactions are made from their wallets. The wallet system is secure and transparent.', 'wallet-system-for-woocommerce' ); ?></p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Wallet_as_Payment_Method.jpg' ); ?>" alt="Wallet as a Payment Method">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php esc_html_e( 'Wallet as a Payment Method', 'wallet-system-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e(
								'Your Customers’ Wallet will work as a payment method only if the wallet amount is greater than the total order value. It will show in the payment method selection. It provides your customers a smooth shopping experience and reminds them to keep their wallets topped up.',
								'wallet-system-for-woocommerce'
							);
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card wps-card-support">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Wallet_as_Discount.jpg' ); ?>" alt="Wallet as a Discount">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php esc_html_e( 'Wallet as a Discount', 'wallet-system-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e(
								'The wallet system provides benefits to customers even if their wallet amount is low. If your customers’ wallet amount is less than the total order value, then it will appear in the order details sections during the checkout, and customers can use it to get discounts.',
								'wallet-system-for-woocommerce'
							);
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Wallet_Amount_Withdrawal.jpg' ); ?>" alt="Wallet Amount Withdrawal">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php esc_html_e( 'Wallet Amount Withdrawal', 'wallet-system-for-woocommerce' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e(
								'Customers can withdraw their wallet amount into their bank account. They have to file a withdrawal request and provide you their account details.',
								'wallet-system-for-woocommerce'
							);
							?>
						</p>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
