<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_currency = apply_filters( 'mwb_wsfw_get_current_currency', get_woocommerce_currency() );
$user_id          = get_current_user_id();
$wallet_bal       = get_user_meta( $user_id, 'mwb_wallet', true );
$wallet_bal       = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
$wallet_bal       = apply_filters( 'mwb_wsfw_show_converted_price', $wallet_bal );
// phpcs:ignore
$http_host   = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
// phpcs:ignore
$request_url = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
$current_url = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $http_host . $request_url;

if ( ! function_exists( 'show_message_on_wallet_form_submit' ) ) {
	/**
	 * Show message on form submit
	 *
	 * @param string $wpg_message message to be shown on form submission.
	 * @param string $type error type.
	 * @return void
	 */
	function show_message_on_wallet_form_submit( $wpg_message, $type = 'error' ) {
		$wpg_notice = '<div class="woocommerce"><p class="' . esc_attr( $type ) . '">' . $wpg_message . '</p>	</div>';
		echo wp_kses_post( $wpg_notice );
	}
}

if ( wc_post_content_has_shortcode( 'MWB_WITHDRAWAL_REQUEST' ) ) {
	?>
	<div class='content mwb_wallet_shortcodes'>
	<h3><?php echo esc_html__( 'Wallet Withdrawal Request', 'wallet-system-for-woocommerce' ); ?></h3>
	<?php
	$disable_withdrawal_request = get_user_meta( $user_id, 'disable_further_withdrawal_request', true );
	if ( $disable_withdrawal_request ) {
		show_message_on_wallet_form_submit( 'Your wallet\'s withdrawal request is in pending.', 'woocommerce-info' );
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
								$withdrawal_status = 'pending';
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
				<input type="submit" class="mwb-btn__filled button" id="mwb_withdrawal_request" name="mwb_withdrawal_request" value="Request For Withdrawal" >
			</p>
		</form>
			<?php
		} else {
		show_message_on_wallet_form_submit( 'Your wallet amount is 0, you cannot withdraw money from wallet.', 'woocommerce-error' );
		}
	}
	?>

</div>
	<?php
	wp_enqueue_style( 'mwb-datatable', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables/media/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
	wp_enqueue_script( 'mwb-datatable', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables/media/js/jquery.dataTables.min.js', array(), $this->version, true );
	wp_enqueue_script( 'mwb-public-min', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/js/mwb-public.min.js', array(), $this->version, 'all' );
}
