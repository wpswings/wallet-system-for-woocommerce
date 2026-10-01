<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<div class='content active'>
	<div class="wps-wallet-transaction-container">
		<table class="wps-wsfw-wallet-field-table " id="transactions_table">
			<form method="POST" class="wps_form_get_export_pdf">
			<?php
			$is_pro_plugin = false;
			$is_pro_plugin = apply_filters( 'wsfw_check_pro_plugin_common', $is_pro_plugin );
			if ( $is_pro_plugin ) {
				?>
				<div class="wps_wsfw_pdf_user_outer_class">
				<input type="submit" class="btn button" name= "wps_wsfw_export_pdf_user" id="wps_wsfw_export_pdf_user" value="<?php esc_html_e( 'Download Transaction', 'wallet-system-for-woocommerce' ); ?>">
				<input type="hidden" id="updatenoncewallet_user_pdf_dwnload" name="updatenoncewallet_user_pdf_dwnload" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
				</div>
				<?php
			}
			?>
						</form>
			<thead>
				<tr>
					<th>#</th>
					<th><?php esc_html_e( 'Transaction Id', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Details', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Method', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					<?php if ( function_exists( 'wps_wsfwrpa_get_ledger_table' ) ) : ?>
					<th><?php esc_html_e( 'Expiry Date', 'wallet-system-for-woocommerce' ); ?></th>
					<?php endif; ?>
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
						$transaction_amount_bal = apply_filters( 'wps_wsfw_show_converted_price', $transaction->amount );
						$user           = get_user_by( 'id', $transaction->user_id );
						$transaction_id = $transaction->id;
						$tranasction_symbol = '';
						if ( 'credit' == $transaction->transaction_type_1 ) {
							$tranasction_symbol = '+';
						} elseif ( 'debit' == $transaction->transaction_type_1 ) {
							$tranasction_symbol = '-';
						}
						?>
						<tr>
							<td><?php echo esc_html( $i ); ?></td>
							<td>
							<?php
								$date = date_create( $transaction->date );
								echo esc_html( $date->getTimestamp() . $transaction->id );

							?>
							</td>
							<td class='wps_wallet_<?php echo esc_attr( $transaction->transaction_type_1 ); ?>' ><?php echo esc_html( $tranasction_symbol ) . wp_kses_post( wc_price( $transaction_amount_bal, array( 'currency' => $transaction->currency ) ) ); ?></td>
							<td class="details" ><?php echo wp_kses_post( html_entity_decode( $transaction->transaction_type ) ); ?></td>
							<td>
							<?php
							$payment_methods = WC()->payment_gateways->payment_gateways();
							foreach ( $payment_methods as $key => $payment_method ) {
								if ( $key == $transaction->payment_method ) {
									$method = esc_html__( 'Online Payment', 'wallet-system-for-woocommerce' );
								} else {
									$method = $transaction->payment_method;
								}
								break;
							}
							echo esc_html( $method );
							?>
							</td>
							<td>
							<?php
							$date_format = get_option( 'date_format', 'm/d/Y' );
							$date        = date_create( $transaction->date );
							$wps_wsfw_time_zone = get_option( 'timezone_string' );
							if ( ! empty( $wps_wsfw_time_zone ) ) {
								$date = date_create( $transaction->date );
								echo esc_html( date_format( $date, $date_format ) );
								// extra code.( need validation if require).
								$date->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );
								// extra code.
								echo ' ' . esc_html( date_format( $date, 'H:i:s' ) );
							} else {

								$date_format = get_option( 'date_format', 'm/d/Y' );
								$date        = date_create( $transaction->date );
								echo esc_html( date_format( $date, $date_format ) );
								echo ' ' . esc_html( date_format( $date, 'H:i:s' ) );
							}
							?>
							</td>
							<?php if ( function_exists( 'wps_wsfwrpa_get_ledger_table' ) ) : ?>
							<td class="wps-expiry-date-cell" data-transaction-id="<?php echo esc_attr( $transaction->id ); ?>">
								<?php
								// Get expiry date from ledger table for credit transactions
								if ( 'credit' === $transaction->transaction_type_1 ) {
									$ledger_table = wps_wsfwrpa_get_ledger_table();

									// Try to find by order_id first
									$expiry_info = null;
									if ( ! empty( $transaction->transaction_id ) && is_numeric( $transaction->transaction_id ) ) {
										// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
										$expiry_info = $wpdb->get_row(
											$wpdb->prepare(
												"SELECT expiry_date, status FROM {$ledger_table}
												WHERE user_id = %d AND order_id = %d AND status = 'active'
												LIMIT 1",
												$transaction->user_id,
												$transaction->transaction_id
											)
										);
									}

									// If not found, try to match by amount and date
									if ( ! $expiry_info ) {
										// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
										$expiry_info = $wpdb->get_row(
											$wpdb->prepare(
												"SELECT expiry_date, status FROM {$ledger_table}
												WHERE user_id = %d
												AND amount = %f
												AND status = 'active'
												AND ABS(TIMESTAMPDIFF(SECOND, credited_date, %s)) < 60
												ORDER BY credited_date DESC
												LIMIT 1",
												$transaction->user_id,
												$transaction->amount,
												$transaction->date
											)
										);
									}

									if ( $expiry_info && ! empty( $expiry_info->expiry_date ) && 'NULL' !== $expiry_info->expiry_date ) {
										$expiry_time = strtotime( $expiry_info->expiry_date );
										$now = current_time( 'timestamp' );
										$days_left = floor( ( $expiry_time - $now ) / DAY_IN_SECONDS );

										$expiry_date_formatted = date_i18n( get_option( 'date_format' ), $expiry_time );

										if ( $expiry_time < $now ) {
											echo '<span style="color: #dc2626; font-weight: 600;">' . esc_html( $expiry_date_formatted ) . '</span>';
											echo '<br><small style="color: #dc2626;">' . esc_html__( 'Expired', 'wallet-system-for-woocommerce' ) . '</small>';
										} else {
											echo '<span style="color: #2ea44f;">' . esc_html( $expiry_date_formatted ) . '</span>';
											if ( $days_left <= 7 ) {
												echo '<br><small style="color: #f59e0b;">' . esc_html( $days_left ) . ' ' . esc_html__( 'days left', 'wallet-system-for-woocommerce' ) . '</small>';
											} else {
												echo '<br><small style="color: #2ea44f;">' . esc_html( $days_left ) . ' ' . esc_html__( 'days left', 'wallet-system-for-woocommerce' ) . '</small>';
											}
										}
									} else {
										echo '<span style="color: #999;">—</span>';
									}
								} else {
									echo '<span style="color: #999;">—</span>';
								}
								?>
							</td>
							<?php endif; ?>
						</tr>
						<?php
						$i++;
					}
				}

				?>
			</tbody>
		</table>
	</div>

	<?php
	// including regular expression jquery.
	wp_enqueue_script( 'anchor-tag', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/wallet-system-for-woocommerce-anchor.js', array(), $this->version, 'all' );
	?>

	<!-- removing the anchor tag href attibute using regular expression -->	
	<script>
	jQuery( "#transactions_table tr td" ).each(function( index ) {
		var details = jQuery( this ).html();
		var patt = new RegExp("<a");
		var res = patt.test(details);
		if ( res ) {
			jQuery(this).children('a').removeAttr("href");
		}
	});
	</script>
</div>   

