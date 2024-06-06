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
			<thead>
				<tr>
					<th>#</th>
					<th><?php esc_html_e( 'Transaction Id', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Details', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Method', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
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

