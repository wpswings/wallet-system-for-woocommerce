<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for showing wallet withdrawal setting
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
global $wsfw_wps_wsfw_obj;
$currency  = get_woocommerce_currency();

if ( isset( $_POST['update_wallet'] ) && ! empty( $_POST['update_wallet'] ) ) {
	$nonce = ( isset( $_POST['verifynonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['verifynonce'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		unset( $_POST['update_wallet'] );
		$update = true;
		if ( empty( $_POST['wallet_amount'] ) ) {
			$msfw_wpg_error_text = esc_html__( 'Please enter any amount', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			$update = false;
		}
		if ( $_POST['wallet_amount'] < 0 ) {
			$msfw_wpg_error_text = esc_html__( 'Please enter amount in positive value.', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			$update = false;
		}
		if ( empty( $_POST['action_type'] ) ) {
			$msfw_wpg_error_text = esc_html__( 'Please select any action', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			$update = false;
		}
		if ( empty( $_POST['user_id'] ) ) {
			$msfw_wpg_error_text = esc_html__( 'User Id is not given', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			$update = false;
		}
		if ( $update ) {

			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();

			$updated_amount = sanitize_text_field( wp_unslash( $_POST['wallet_amount'] ) );
			$wallet_action  = sanitize_text_field( wp_unslash( $_POST['action_type'] ) );
			$user_id        = sanitize_text_field( wp_unslash( $_POST['user_id'] ) );
			$wallet         = get_user_meta( $user_id, 'wps_wallet', true );
			$wallet         = ( ! empty( $wallet ) ) ? $wallet : 0;
			if ( 'credit' === $wallet_action ) {
				$wallet          += $updated_amount;
				$wallet           = update_user_meta( $user_id, 'wps_wallet', $wallet );
				$balance_mail = $currency . ' ' . $updated_amount;
				$transaction_type = __( 'Credited by admin', 'wallet-system-for-woocommerce' );
				$mail_message     = __( 'Merchant has credited your wallet by ', 'wallet-system-for-woocommerce' ) . $balance_mail;
				if ( key_exists( 'wps_wswp_wallet_credit', WC()->mailer()->emails ) ) {
					$customer_email = WC()->mailer()->emails['wps_wswp_wallet_credit'];
					if ( ! empty( $customer_email ) ) {
						$user       = get_user_by( 'id', $user_id );
						$balance_mail = $currency . ' ' . $updated_amount;
						$user_name       = $user->first_name . ' ' . $user->last_name;
						$customer_email->trigger( $user_id, $user_name, $balance_mail, '' );
					}
				}
			} elseif ( 'debit' === $wallet_action ) {
				if ( $wallet < $updated_amount ) {
					$wallet = 0;
				} else {
					$wallet -= $updated_amount;
				}
				$wallet           = update_user_meta( $user_id, 'wps_wallet', abs( $wallet ) );
				$transaction_type = __( 'Debited by admin', 'wallet-system-for-woocommerce' );
				$balance_mail   = $currency . ' ' . $net_balance;
				$mail_message     = __( 'Merchant has deducted ', 'wallet-system-for-woocommerce' ) . $balance_mail . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
				if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) ) {

					$customer_email = WC()->mailer()->emails['wps_wswp_wallet_debit'];
					if ( ! empty( $customer_email ) ) {
						$user       = get_user_by( 'id', $user_id );
						$currency  = get_woocommerce_currency();
						$balance_mail = $currency . ' ' . $updated_amount;
						$user_name       = $user->first_name . ' ' . $user->last_name;
						$customer_email->trigger( $user_id, $user_name, $balance_mail, '' );
					}
				}
			}

			$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
			$customer_email_credit = '';
			$customer_email_debit = '';
			if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) || key_exists( 'wps_wswp_wallet_credit', WC()->mailer()->emails ) ) {

				$customer_email_credit = WC()->mailer()->emails['wps_wswp_wallet_credit'];
				$customer_email_debit = WC()->mailer()->emails['wps_wswp_wallet_debit'];
			}
			if ( empty( $customer_email_credit ) || empty( $customer_email_debit ) ) {

				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
					$user       = get_user_by( 'id', $user_id );
					$name       = $user->first_name . ' ' . $user->last_name;
					$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . ",\r\n";
					$mail_text .= $mail_message;
					$to         = $user->user_email;
					$from       = get_option( 'admin_email' );

					$subject  = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
					$headers .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to . "\r\n";

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
			}

			$transaction_data = array(
				'user_id'          => $user_id,
				'amount'           => $updated_amount,
				'currency'         => get_woocommerce_currency(),
				'payment_method'   => esc_html__( 'Manually By Admin', 'wallet-system-for-woocommerce' ),
				'transaction_type' => $transaction_type,
				'transaction_type_1' => $wallet_action,
				'order_id'         => '',
				'note'             => '',

			);

			$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
			if ( $result ) {
				$msfw_wpg_error_text = esc_html__( 'Updated wallet of user', 'wallet-system-for-woocommerce' );
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'success' );
			} else {
				$msfw_wpg_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			}
		}
	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}

if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
	$user_id = sanitize_text_field( wp_unslash( $_GET['id'] ) );
}
$user       = get_user_by( 'id', $user_id );
$wallet_bal = get_user_meta( $user_id, 'wps_wallet', true );
$wallet_bal = empty( $wallet_bal ) ? 0 : $wallet_bal;
?>
<div class="wrap edit-user-wallet">
	<h2>
		<?php
		echo esc_html__( 'Edit User Wallet: ', 'wallet-system-for-woocommerce' ) . esc_html( $user->user_login ) . '(' . esc_html( $user->user_email ) . ')';
		?>
		<a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>"><span class="dashicons dashicons-editor-break"></span></a>
	</h2>
	<p>
	<?php
	esc_html_e( 'Current wallet balance: ', 'wallet-system-for-woocommerce' );
	echo wp_kses_post( wc_price( $wallet_bal ) );
	?>
	</p>
	<form method="post">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="wallet_amount"><?php echo esc_html__( 'Amount ( ', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol() ) . ' )'; ?></label>
					</th>
					<td>
						<input type="number" id="wallet_amount" step="0.01" name="wallet_amount" class="regular-text" required>
						<p class="description"><?php esc_html_e( 'Enter amount you want to credit/debit', 'wallet-system-for-woocommerce' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="action_type"><?php esc_html_e( 'Action', 'wallet-system-for-woocommerce' ); ?></label></th>
					<td>
						<select class="regular-text" name="action_type" id="action_type" required>
							<option value="credit"><?php esc_html_e( 'Credit', 'wallet-system-for-woocommerce' ); ?></option>
							<option value="debit"><?php esc_html_e( 'Debit', 'wallet-system-for-woocommerce' ); ?></option>
						</select>
						<p class="description"><?php esc_html_e( 'Whether want to add amount or deduct it from wallet', 'wallet-system-for-woocommerce' ); ?></p></p>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">
		<input type="hidden" id="verifynonce" name="verifynonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
		<p class="submit"><input type="submit" name="update_wallet" class="button button-primary wps_wallet-update" value="Update Wallet"></p>
	</form>
</div>
