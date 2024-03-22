<?php
/**
 * Provide a admin area view for show wallet orders
 *
 * This file is used to show wallet orders.
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


$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
if ( ! $id_nonce_verified ) {
	wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
}
require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/class-wallet-orders-list.php';
$wallet_orders = new Wallet_Orders_List();

// message on applying bulk action.
if ( ! empty( $_REQUEST['bulk_action'] ) && ( 'trash' !== $_REQUEST['bulk_action'] && 'untrash' !== $_REQUEST['bulk_action'] && 'delete' !== $_REQUEST['bulk_action'] ) ) {
	$changed = ( isset( $_REQUEST['changed'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['changed'] ) ) : '';
	printf(
		'<div id="message" class="updated notice is-dismissable"><p>' . esc_html(
			/* translators: %d: Status Change. */
			_n(
				'%d order status changed.',
				'%d orders status changed.',
				esc_html( $changed ),
				'wallet-system-for-woocommerce'
			)
		) . '</p></div>',
		esc_html( $changed )
	);
}
if ( ! empty( $_REQUEST['bulk_action'] ) && ( 'trash' === $_REQUEST['bulk_action'] ) ) {
	$changed = ( isset( $_REQUEST['changed'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['changed'] ) ) : '';
	printf(
		'<div id="message" class="updated notice is-dismissable"><p>' . esc_html(
			/* translators: %d: Status Change. */
			_n(
				'%d order moved to trash.',
				'%d orders moved to trash.',
				esc_html( $changed ),
				'wallet-system-for-woocommerce'
			)
		) . '</p></div>',
		esc_html( $changed )
	);
}
if ( ! empty( $_REQUEST['bulk_action'] ) && ( 'untrash' === $_REQUEST['bulk_action'] ) ) {
	$changed = ( isset( $_REQUEST['changed'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['changed'] ) ) : '';
	printf(
		'<div id="message" class="updated notice is-dismissable"><p>' . esc_html(
			/* translators: %d: Status Change. */
			_n(
				'%d order restored from the Trash.',
				'%d orders restored from the Trash.',
				esc_html( $changed ),
				'wallet-system-for-woocommerce'
			)
		) . '</p></div>',
		esc_html( $changed )
	);
}
if ( ! empty( $_REQUEST['bulk_action'] ) && ( 'delete' === $_REQUEST['bulk_action'] ) ) {
	$changed = ( isset( $_REQUEST['changed'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['changed'] ) ) : '';
	printf(
		'<div id="message" class="updated notice is-dismissable"><p>' . esc_html(
			/* translators: %d: Status Change. */
			_n(
				'%d order permanently deleted.',
				'%d orders permanently deleted.',
				esc_html( $changed ),
				'wallet-system-for-woocommerce'
			)
		) . '</p></div>',
		esc_html( $changed )
	);
}

?>
<div class="wrap">

	<h1 class="wp-heading-inline"> <?php esc_html_e( 'Wallet Recharge Orders', 'wallet-system-for-woocommerce' ); ?></h1>
	<div id="wrapper" class="wps_wcb_all_trans_container meta-box-sortables ui-sortable wallet_shop_order">
		<form action="" method="POST">
		
			<?php
			$wallet_orders->display_header();
			$wallet_orders->views();

			if ( isset( $_GET['s'] ) ) {

				$wallet_orders->prepare_items( sanitize_text_field( wp_unslash( $_GET['s'] ) ) );

			} else {
				$wallet_orders->prepare_items();
			}

			// Table of elements.
			$wallet_orders->display();
			?>
			
		</form>

		<?php
		// including datepicker jquery in custom order wp list table.
		wp_enqueue_script( 'datepicker', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js', array(), '1.11.2', true );
		?>

	</div>
</div>


