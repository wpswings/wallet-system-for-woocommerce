<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for showing user's wallet transactions
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
if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
	$user_id = sanitize_text_field( wp_unslash( $_GET['id'] ) );
}
$user = get_user_by( 'id', $user_id );

?>

<div class="wps-wpg-transcation-section-search">
	<table>
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Search', 'wallet-system-for-woocommerce' ); ?></th>
					<td><input type="text" id="search_in_table" placeholder="Enter your Keyword"></td>
				</tr>
				<tr>
					<td><input name="min" id="min" type="text" placeholder="From" autocomplete="off"></td>
				</tr>
				<tr>
					<td><input name="max" id="max" type="text" placeholder="To" autocomplete="off"></td>
				</tr>
				<tr>
					<td><span id="clear_table" ><?php esc_html_e( 'Clear', 'wallet-system-for-woocommerce' ); ?></span></td>
				</tr>
			</tbody>
		</table>

</div>

<div class="wps-wpg-gen-section-table-wrap wps-wpg-transcation-section-table">
	<h4>
	<?php
	echo esc_html__( 'Wallet Transactions: ', 'wallet-system-for-woocommerce' ) . esc_html( $user->user_login ) . '(' . esc_html( $user->user_email ) . ')';
	?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=class-wallet-user-table' ) ); ?>"><span class="dashicons dashicons-editor-break" ></span></a>
	</h4>
	<div class="wps-wpg-gen-section-table-container">
		<table id="wps-wpg-gen-table" class="wps-wpg-gen-section-table wps-wpg-user-transaction-table dt-responsive">
			<thead>
				<tr>
					<th><?php esc_html_e( '#', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Transaction ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Payment Method', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Details', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					<th class="hide_date" ><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				global $wpdb;
				$table_name   = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
				$transactions = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction WHERE user_id = %s ORDER BY `Id` DESC', $user_id ) );
				if ( ! empty( $transactions ) && is_array( $transactions ) ) {
					$i = 1;
					foreach ( $transactions as $transaction ) {
						?>
						<tr>
							<td><img src="<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>admin/image/eva_close-outline.svg"><?php echo esc_html( $i ); ?></td>
							<td><?php echo esc_html( $transaction->id ); ?></td>
							<td><?php echo wp_kses_post( wc_price( $transaction->amount, array( 'currency' => $transaction->currency ) ) ); ?></td>
							<td><?php echo wp_kses_post( $transaction->payment_method ); ?></td>
							<td><?php echo wp_kses_post( html_entity_decode( $transaction->transaction_type ) ); ?></td>
							<td>
							<?php
							$date_format = get_option( 'date_format', 'm/d/Y' );
							$date        = date_create( $transaction->date );
							echo esc_html( date_format( $date, $date_format ) );
							?>
							</td>
							<td class="hide_date" >
							<?php
							$date = date_create( $transaction->date );
							echo esc_html( date_format( $date, 'm/d/Y' ) );
							?>
							</td>
						</tr>
						<?php
						$i++;
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>
<?php
// enqueue datepicker js.
wp_enqueue_script( 'datepicker', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js', array(), '1.11.2', true );
wp_enqueue_script( 'wps-admin-user-transaction-table', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-user-transaction-table.js', array( 'jquery' ), $this->version, false );
?>
