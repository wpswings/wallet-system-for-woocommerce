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
	<div class="mwb-wallet-transaction-container">
		<table class="mwb-wallet-field-table dt-responsive" id="transactions_table">
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
				$table_name   = $wpdb->prefix . 'mwb_wsfw_wallet_transaction';
				$transactions = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'mwb_wsfw_wallet_transaction WHERE user_id = %s ORDER BY `Id` DESC', $user_id ) );
				if ( ! empty( $transactions ) && is_array( $transactions ) ) {
					$i = 1;
					foreach ( $transactions as $transaction ) {
						$user           = get_user_by( 'id', $transaction->user_id );
						$transaction_id = $transaction->id;
						?>
						<tr>
							<td><?php echo esc_html( $i ); ?></td>
							<td><?php echo esc_html( $transaction_id ); ?></td>
							<td><?php echo wc_price( $transaction->amount, array( 'currency' => $transaction->currency ) ); ?></td>
							<td class="details" ><?php wp_kses( _e( html_entity_decode( $transaction->transaction_type ), 'wallet-system-for-woocommerce' ), $allowed_html ); _e( html_entity_decode( $transaction->transaction_type ), 'wallet-system-for-woocommerce' ); ?></td>
							<td>
							<?php
							$payment_methods = WC()->payment_gateways->payment_gateways();
							foreach ( $payment_methods as $key => $payment_method ) {
								if ( $key == $transaction->payment_method ) {
									$method = esc_html__( 'Online Payment', 'wallet-system-for-woocommerce' );
								} else {
									$method = esc_html__( $transaction->payment_method, 'wallet-system-for-woocommerce' );
								}
								break;
							}
							echo $method;
							?>
							</td>
							<td>
							<?php
							$date_format = get_option( 'date_format', 'm/d/Y' );
							$date        = date_create( $transaction->date );
							echo esc_html( date_format( $date, $date_format ) );
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

