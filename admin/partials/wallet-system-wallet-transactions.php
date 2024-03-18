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

<div class="wps-wpg-transcation-section-search">

	<table>
			<tbody>
				<tr class='wps_wallet_transaction_search'>
					<th><?php esc_html_e( 'Search ', 'wallet-system-for-woocommerce' ); ?></td>
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


<div class="wps-wpg-gen-section-table-wrap wps-wpg-transcation-section-table">
	<h4><?php esc_html_e( 'Transactions', 'wallet-system-for-woocommerce' ); ?> </h4>
	<form method="GET">
	<input type="submit" class="btn button" name= "wps_wsfw_export_pdf" id="wps_wsfw_export_pdf" value="<?php esc_html_e( 'Export Pdf', 'wallet-system-for-woocommerce' ); ?>">
	
</form>

	<div class="wps-wpg-gen-section-table-container">
		<table id="wps-wpg-gen-table_trasa_custom_wallet_table" class="wps-wpg-gen-section-table dt-responsive wps-wpg-gen-table-all-transaction">
			<thead>
				<tr>
				<th class = "all">#</th>
					<th class = "all"><?php esc_html_e( 'Name', 'wallet-system-for-woocommerce' ); ?></th>
					<th class = "all"><?php esc_html_e( 'Email', 'wallet-system-for-woocommerce' ); ?></th>
					<th class = "all"><?php esc_html_e( 'Role', 'wallet-system-for-woocommerce' ); ?></th>
					<th class = "all"><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
					<th class = "all"><?php esc_html_e( 'Payment Method', 'wallet-system-for-woocommerce' ); ?></th>
					<th class = "all"><?php esc_html_e( 'Details', 'wallet-system-for-woocommerce' ); ?></th>
					<th class = "all"><?php esc_html_e( 'Transaction ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th class = "all"><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					<th class = "all"><?php esc_html_e( 'Action', 'wallet-system-for-woocommerce' ); ?></th>
					<th class="hide_date" ><?php esc_html_e( 'Date1', 'wallet-system-for-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				global $wpdb;
				$limit_for_transaction = '10';




				if ( ! empty( $transactions_total_count ) ) {
					$transactions_total_count_data = $transactions_total_count[0]->total_count;
				}

				if ( ! empty( $transactions ) && is_array( $transactions ) ) {
					$i = 1;
					foreach ( $transactions as $transaction ) {

						$user = get_user_by( 'id', $transaction->user_id );
						if ( $user ) {
							$display_name = $user->display_name;
							$useremail    = $user->user_email;
							$user_role = '';
							if ( is_array( $user->roles ) && ! empty( $user->roles ) ) {
								$user_role    = $user->roles[0];
							}
						} else {
							$display_name = '';
							$useremail    = '';
							$user_role    = '';
						}

						$tranasction_symbol = '';
						if ( 'credit' == $transaction->transaction_type_1 ) {
							$tranasction_symbol = '+';
						} elseif ( 'debit' == $transaction->transaction_type_1 ) {
							$tranasction_symbol = '-';
						}
						?>
						<tr class='wps_wallet_tr_<?php echo esc_attr( $transaction->transaction_type_1 ); ?>'>
						<td><?php echo esc_html( $i ); ?></td>
							<td><?php echo ! empty( esc_html( $display_name ) ) ? esc_html( $display_name ) : 'Guest#(' . esc_html( $transaction->user_id ) . ')'; ?></td>
							<td><?php echo ! empty( esc_html( $useremail ) ) ? esc_html( $useremail ) : '---'; ?></td>
							<td><?php echo esc_html( $user_role ); ?></td>
							<td class='wps_wallet_<?php echo esc_attr( $transaction->transaction_type_1 ); ?>'><?php echo esc_html( $tranasction_symbol ) . wp_kses_post( wc_price( $transaction->amount, array( 'currency' => $transaction->currency ) ) ); ?></td>
							<td><?php echo wp_kses_post( $transaction->payment_method ); ?></td>
							<td><?php echo wp_kses_post( html_entity_decode( $transaction->transaction_type ) ); ?></td>
							<td>
							<?php
							 esc_html( $transaction->id );
							$date = date_create( $transaction->date );
							echo esc_html( $date->getTimestamp() . $transaction->id );
							?>
							</td>
							<td>
							<?php
							$date_format = get_option( 'date_format', 'm/d/Y' );


							$wps_wsfw_time_zone = get_option( 'timezone_string' );
							if ( ! empty( $wps_wsfw_time_zone ) ) {
								$date = date_create( $transaction->date );
								echo esc_html( date_format( $date, $date_format ) );
								// extra code.( need validation if require).
								$date->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );
								// extra code.
								echo ' ' . esc_html( date_format( $date, 'H:i:s' ) );
							} else {

								echo esc_html( date_format( $date, $date_format ) );
								echo ' ' . esc_html( date_format( $date, 'H:i:s' ) );
							}
							?>
							</td>
							<?php
								$is_pro = false;
								$is_pro = apply_filters( 'wsfw_check_pro_plugin', $is_pro );
							if ( ! $is_pro ) {
								?>
									<td class="wps_wallet_delete_action wps_pro_settings" ><?php esc_html_e( 'Delete', 'wallet-system-for-woocommerce' ); ?></td>
									<?php
							} else {
								?>
									<td class="wps_wallet_delete_action" onclick="wps_wallet_delete_function(<?php echo esc_attr( $transaction->id ); ?>)"><?php esc_html_e( 'Delete', 'wallet-system-for-woocommerce' ); ?></td>
									<?php
							}

							?>
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
	<div class="bottom">
		<div class="dataTables_length_wallet_custom_table" id="wps-wsfw-wallet-trabsacstion-numbers">
			<label><?php esc_html_e( 'Rows per page', 'wallet-system-for-woocommerce' ); ?>
				<select name="wps-wsfw-wallet-trabsacstion-numbers-drodown" id ="wps-wsfw-wallet-trabsacstion-numbers-drodown" aria-controls="wps-wpg-gen-table_trasa" >
					<option value="10" <?php echo ( '10' == $limit_for_transaction ? 'selected="selected"' : '' ); ?>>10</option>
					<option value="25" <?php echo ( '25' == $limit_for_transaction ? 'selected="selected"' : '' ); ?>>25</option>
					<option value="50" <?php echo ( '50' == $limit_for_transaction ? 'selected="selected"' : '' ); ?>>50</option>
					<option value="100" <?php echo ( '100' == $limit_for_transaction ? 'selected="selected"' : '' ); ?>>100</option>
				</select>
			</label>
		</div>
		<form method="POST">
	
		<div class="dataTables_info_wallet_custom_data" id="wps-wpg-gen-table_trasa_info" role="status" aria-live="polite">1 - 10 of <?php echo esc_html( $transactions_total_count_data ); ?></div>
		<div class="dataTables_paginate_wallet_custom_data paging_simple_numbers" id="wps-wpg-gen-table_trasa_paginate">
			<a class="paginate_button previous" id="previous_button_wallet_transaction" name="previous_button_wallet_transaction" aria-controls="wps-wpg-gen-table_trasa" data-dt-idx="0" tabindex="0" id="wps-wpg-gen-table_trasa_previous"><svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.00016 12L7.41016 10.59L2.83016 6L7.41016 1.41L6.00016 -1.23266e-07L0.000156927 6L6.00016 12Z" fill="#8E908F"></path></svg></a>
			<span><a class="paginate_button current" id="current_button_wallet_transaction" name="current_button_wallet_transaction"   aria-controls="wps-wpg-gen-table_trasa" data-dt-idx="1" tabindex="0">   1       </a></span><a class="paginate_button next" id="next_button_wallet_transaction" name="next_button_wallet_transaction" aria-controls="wps-wpg-gen-table_trasa" data-dt-idx="2" tabindex="0" id="wps-wpg-gen-table_trasa_next"><svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.99984 0L0.589844 1.41L5.16984 6L0.589844 10.59L1.99984 12L7.99984 6L1.99984 0Z" fill="#8E908F"></path></svg></a></div></div>	
			<input type="submit"  class="btn button" name= "wps_wsfw_data_number" id="wps_wsfw_data_number" value="" >
			<input type="hidden" id="hidden_transaction_number" name="hidden_transaction_number" value=""/>
			<input type="hidden" id="hidden_transaction_current_number" name="hidden_transaction_current_number" value="1"/>
			<input type="hidden" id="hidden_transaction_next_number" name="hidden_transaction_next_number" value="2"/>
			<input type="hidden" id="hidden_transaction_previous_number" name="hidden_transaction_previous_number" value="0"/>

		</form>
</div>

<?php
include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/wallet-system-for-woocommerce-go-pro-data.php';

// including datepicker jquery for input tag.
wp_enqueue_script( 'datepicker', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js', array(), '1.11.2', true );


$check = false;
$check = apply_filters( 'wsfw_check_pro_plugin', $check );
if ( false == $check ) {
	wp_enqueue_script( 'wps-admin-all-transaction-table', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-all-transaction-table.js', array( 'jquery' ), $this->version, false );
}


?>
