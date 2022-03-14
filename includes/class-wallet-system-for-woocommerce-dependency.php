<?php
/**
 * Functions for plugin dependency
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if function exists.
if ( ! function_exists( 'wps_wsfw_update_user_wallet_balance' ) ) {
	/**
	 * Update the user's wallet balance.
	 *
	 * @param int $user_id user id.
	 * @param int $amount amount.
	 * @param int $order_id order id.
	 * @return boolean
	 */
	function wps_wsfw_update_user_wallet_balance( $user_id, $amount, $order_id = '' ) {
		$wallet_balance = get_user_meta( $user_id, 'wps_wallet', true );
		if ( ! empty( $wallet_balance ) ) {
			if ( $wallet_balance < $amount ) {
				$wallet_balance = 0;
			} else {
				$wallet_balance -= $amount;
			}
			$update_wallet          = update_user_meta( $user_id, 'wps_wallet', $wallet_balance );
			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
			$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
			if ( $update_wallet ) {
				$payment_method   = esc_html__( 'Manually done', 'wallet-system-for-woocommerce' );
				$currency         = get_woocommerce_currency();
				$transaction_type = esc_html__( 'Wallet is debited', 'wallet-system-for-woocommerce' );
				if ( ! empty( $order_id ) ) {
					$order = wc_get_order( $order_id );
					if ( $order ) {
						$payment_method = $order->get_payment_method();
						if ( 'wps_wcb_wallet_payment_gateway' === $payment_method || 'wallet' === $payment_method ) {
							$payment_method = esc_html__( 'Wallet Payment', 'wallet-system-for-woocommerce' );
						}
						$currency         = $order->get_currency();
						$transaction_type = __( 'Wallet debited through purchasing ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
					} else {
						$order_id = '';
					}
				}
				$transaction_data = array(
					'user_id'          => $user_id,
					'amount'           => $amount,
					'currency'         => $currency,
					'payment_method'   => $payment_method,
					'transaction_type' => htmlentities( $transaction_type ),
					'order_id'         => $order_id,
					'note'             => '',
				);
				$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
					$user       = get_user_by( 'id', $user_id );
					$name       = $user->first_name . ' ' . $user->last_name;
					$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
					$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount, array( 'currency' => $currency ) ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
					$to         = $user->user_email;
					$from       = get_option( 'admin_email' );
					$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
					$headers    = 'MIME-Version: 1.0' . "\r\n";
					$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
					$headers   .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to . "\r\n";
					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
				return true;
			} else {
				return false;
			}
		}

	}
}
