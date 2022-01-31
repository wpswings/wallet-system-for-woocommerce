<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to show wallet transactions.
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

<div class="mwb-wpg-transcation-section-search">

	<table>
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Search', 'wallet-system-for-woocommerce' ); ?></td>
					<td><input type="text" id="search_in_table" placeholder="Enter your Keyword"></td>
				</tr>
				<tr>
					<td><input name="min" id="min" type="text" placeholder="From"  autocomplete="off"></td>
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


<div class="mwb-wpg-gen-section-table-wrap mwb-wpg-transcation-section-table">
	<h4><?php esc_html_e( 'Transactions', 'wallet-system-for-woocommerce' ); ?> </h4>
	<div class="mwb-wpg-gen-section-table-container">
		<table id="mwb-wpg-gen-table" class="mwb-wpg-gen-section-table dt-responsive mwb-wpg-gen-table-all-transaction">
			<thead>
				<tr>
					<th>#</th>
					<th><?php esc_html_e( 'Name', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Email', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Role', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Payment Method', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Details', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Transaction ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					<th class="hide_date" ><?php esc_html_e( 'Date1', 'wallet-system-for-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				global $wpdb;
				$table_name   = $wpdb->prefix . 'mwb_wsfw_wallet_transaction';
				$transactions = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_wsfw_wallet_transaction ORDER BY Id DESC' );
				if ( ! empty( $transactions ) && is_array( $transactions ) ) {
					$i = 1;
					foreach ( $transactions as $transaction ) {
						$user = get_user_by( 'id', $transaction->user_id );
						if ( $user ) {
							$display_name = $user->display_name;
							$useremail    = $user->user_email;
							$user_role    = $user->roles[0];
						} else {
							$display_name = '';
							$useremail    = '';
							$user_role    = '';
						}
						?>
						<tr>
							<td><img src="<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>admin/image/eva_close-outline.svg"><?php echo esc_html( $i ); ?></td>
							<td><?php echo esc_html( $display_name ); ?></td>
							<td><?php echo esc_html( $useremail ); ?></td>
							<td><?php echo esc_html( $user_role ); ?></td>
							<td><?php echo wp_kses_post( wc_price( $transaction->amount, array( 'currency' => $transaction->currency ) ) ); ?></td>
							<td><?php echo $transaction->payment_method; // phpcs:ignore ?></td>
							<td><?php echo html_entity_decode( $transaction->transaction_type ); // phpcs:ignore ?></td>
							<td><?php echo esc_html( $transaction->id ); ?></td>
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
// including datepicker jquery for input tag.
wp_enqueue_script( 'datepicker', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js', array(), '1.11.2', true );
wp_enqueue_script( 'mwb-admin-all-transaction-table', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-all-transaction-table.js', array( 'jquery' ), $this->version, false );
?>
