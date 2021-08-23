<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );
$wallet_bal = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
$wallet_bal = apply_filters( 'mwb_wsfw_show_converted_price', $wallet_bal );

?>


<div class='content active'>

	<?php
	$disable_withdrawal_request = get_user_meta( $user_id, 'disable_further_withdrawal_request', true );
	if ( $disable_withdrawal_request ) {
		show_message_on_form_submit( esc_html__( 'Your wallet\'s withdrawal request is in pending.', 'wallet-system-for-woocommerce' ), 'woocommerce-info' );
		$args               = array(
			'numberposts' => -1,
			'post_type'   => 'wallet_withdrawal',
			'orderby'     => 'ID',
			'order'       => 'DESC',
			'post_status' => array( 'any' ),
		);
		$withdrawal_request = get_posts( $args );
		?>
		<div class="mwb-wallet-transaction-container">
			<table class="mwb-wallet-field-table dt-responsive" id="transactions_table" >
				<thead>
					<tr>
						<th>#</th>
						<th><?php esc_html_e( 'ID', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					foreach ( $withdrawal_request as $key => $pending ) {
						$request_id = $pending->ID;
						$userid     = get_post_meta( $request_id, 'wallet_user_id', true );
						if ( $userid == $user_id ) {
							$date = date_create( $pending->post_date );
							if ( 'pending1' === $pending->post_status ) {
								$withdrawal_status = esc_html__( 'pending', 'wallet-system-for-woocommerce' );
							} else {
								$withdrawal_status = $pending->post_status;
							}
							$withdrawal_balance = apply_filters( 'mwb_wsfw_show_converted_price', get_post_meta( $request_id, 'mwb_wallet_withdrawal_amount', true ) );
							echo '<tr>
							<td>' . esc_html( $i ) . '</td>
                            <td>' . esc_html( $request_id ) . '</td>
                            <td>' . wc_price( $withdrawal_balance, array( 'currency' => $current_currency ) ) . '</td>
                            <td>' . esc_html( $withdrawal_status ) . '</td>
                            <td>' . esc_html( get_post_meta( $request_id, 'mwb_wallet_note', true ) ) . '</td>
                            <td>' . esc_html( date_format( $date, 'd/m/Y' ) ) . '</td>
                            </tr>';
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	} else {
		if ( $wallet_bal > 0 ) {
			?>
		<form method="post" action="" id="mwb_wallet_transfer_form">
			<p class="mwb-wallet-field-container form-row form-row-wide">
				<label for="mwb_wallet_withdrawal_amount"><?php echo esc_html__( 'Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol( $current_currency ) ) . ')'; ?></label>
				<input type="number" step="0.01" min="0" data-max="<?php echo esc_attr( $wallet_bal ); ?>" id="mwb_wallet_withdrawal_amount" name="mwb_wallet_withdrawal_amount" required="">
			</p>
			<p class="error"></p>
			<p class="mwb-wallet-field-container form-row form-row-wide">
				<label for="mwb_wallet_note"><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></label>
				<textarea id="mwb_wallet_note" name="mwb_wallet_note" required></textarea>
				<?php
				$show_withdrawal_message = apply_filters( 'mwb_wsfw_show_withdrawal_message', '' );
				if ( ! empty( $show_withdrawal_message ) ) {
					echo '<span class="show-message" >' . $show_withdrawal_message . '</span>';
				}
				?>
			</p>
			<p class="mwb-wallet-field-container form-row">
				<input type="hidden" name="wallet_user_id" value="<?php echo esc_attr( $user_id ); ?>">
				<input type="submit" class="mwb-btn__filled button" id="mwb_withdrawal_request" name="mwb_withdrawal_request" value="<?php esc_html_e( 'Request For Withdrawal', 'wallet-system-for-woocommerce' ); ?>" >
			</p>
		</form>
			<?php
		} else {
			show_message_on_wallet_form_submit( esc_html__( 'Your wallet amount is 0, you cannot withdraw money from wallet.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		}
	}
	?>

</div>
