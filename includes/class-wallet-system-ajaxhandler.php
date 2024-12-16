<?php
/**
 * Handles all admin ajax requests.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 */

/**
 * Handles all admin ajax requests.
 *
 * All the functions required for handling admin ajax requests
 * required by the plugin.
 *
 * @since      1.0.0
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wallet_System_AjaxHandler {

	/**
	 * Construct.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_calculate_amount_after_wallet', array( &$this, 'calculate_amount_after_wallet' ) );
		add_action( 'wp_ajax_unset_wallet_session', array( &$this, 'unset_wallet_session' ) );
		add_action( 'wp_ajax_calculate_amount_total_after_wallet', array( &$this, 'calculate_amount_total_after_wallet' ) );
		add_action( 'wp_ajax_change_wallet_fund_request_status', array( &$this, 'change_wallet_fund_request_status_callback' ) );
	}

	/**
	 * Set the session when partial payment is enabled
	 *
	 * @return void
	 */
	public function calculate_amount_after_wallet() {

		if ( is_user_logged_in() ) {
			check_ajax_referer( 'ajax-nonce', 'nonce' );
			$message = array();
			if ( isset( $_POST['checked'] ) && 'true' === $_POST['checked'] ) {
				WC()->session->set( 'is_wallet_partial_payment', 'true' );
			} else {
				WC()->session->set( 'is_wallet_partial_payment', 'false' );
			}
			$wallet_amount = empty( $_POST['wallet_amount'] ) ? 0 : sanitize_text_field( wp_unslash( $_POST['wallet_amount'] ) );
			$amount        = empty( $_POST['amount'] ) ? 0 : sanitize_text_field( wp_unslash( $_POST['amount'] ) );
			if ( '' == $amount || $amount <= 0 ) {
				$message['status']  = false;
				$message['message'] = esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' );
				wp_send_json( $message );
			}
			if ( $wallet_amount >= $amount ) {
				$wallet_amount     -= $amount;
				$total_amount = WC()->cart->get_total( 'edit' );
				$total_amount_partial = floatval( $total_amount ) - floatval( $amount );

				$message['status']  = true;
				$message['message'] = esc_html__( 'Wallet balance after using amount from it: ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet_amount );
				$message['price']   = wc_price( $amount );
				WC()->session->set( 'custom_fee', $amount );
				WC()->session->set( 'is_wallet_partial_payment_checkout', 'true' );
				WC()->session->set( 'is_wallet_partial_payment_block', $amount );
				WC()->session->set( 'is_wallet_partial_payment_cart_total_value', $total_amount_partial );
				wp_send_json( $message );
			} else {
				$message['status']  = false;
				$message['message'] = esc_html__( 'Please enter amount less than or equal to wallet balance', 'wallet-system-for-woocommerce' );
			}

			wp_send_json( $message );
		}
	}

	/**
	 * This function is used to Pay total payment throught partial payment method.
	 *
	 * @return void
	 */
	public function calculate_amount_total_after_wallet() {
		if ( is_user_logged_in() ) {
			check_ajax_referer( 'ajax-nonce', 'nonce' );
			$message = array();

			if ( isset( $_POST['checked'] ) && 'true' === $_POST['checked'] ) {
				WC()->session->set( 'is_wallet_partial_payment', 'true' );
			} else {
				WC()->session->set( 'is_wallet_partial_payment', 'false' );
			}

			$wallet_amount = empty( $_POST['wallet_amount'] ) ? 0 : sanitize_text_field( wp_unslash( $_POST['wallet_amount'] ) );

			if ( ! empty( $wallet_amount ) ) {
				$message['status']  = true;
				$message['message'] = esc_html__( 'Wallet amount used successfully: ', 'wallet-system-for-woocommerce' );
				$total_amount = WC()->cart->get_total( 'edit' );
				$total_amount_partial = floatval( $total_amount ) - floatval( $wallet_amount );

				WC()->session->set( 'custom_fee', $wallet_amount );
				WC()->session->set( 'is_wallet_partial_payment_checkout', 'true' );
				WC()->session->set( 'is_wallet_partial_payment_block', $wallet_amount );
				WC()->session->set( 'is_wallet_partial_payment_cart_total_value', $total_amount_partial );

			} else {
				$message['status']  = false;
				$message['message'] = esc_html__( 'Wallet amount is empty: ', 'wallet-system-for-woocommerce' );
			}
			wp_send_json( $message );
			wp_die();
		}
	}

	/**
	 * Unset the session on disabling partial payment
	 *
	 * @return void
	 */
	public function unset_wallet_session() {
		WC()->session->__unset( 'custom_fee' );
		WC()->session->__unset( 'is_wallet_partial_payment' );
		WC()->session->__unset( 'is_wallet_partial_payment_checkout' );
		WC()->session->__unset( 'is_wallet_partial_payment_block' );

		echo 'true';
		wp_die();
	}

	/**
	 * Wallet Fund Request status changed. function
	 *
	 * @return void
	 */
	public function change_wallet_fund_request_status_callback() {
		if ( is_user_logged_in() ) {
			check_ajax_referer( 'ajax-nonce', 'nonce' );

			$request_id = empty( $_POST['request_id'] ) ? 0 : sanitize_text_field( wp_unslash( $_POST['request_id'] ) );

			$requesting_user_id = empty( $_POST['requesting_user_id'] ) ? 0 : sanitize_text_field( wp_unslash( $_POST['requesting_user_id'] ) );

			$status = ( isset( $_POST['status'] ) ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

			$withdrawal_balance = empty( $_POST['withdrawal_balance'] ) ? 0 : sanitize_text_field( wp_unslash( $_POST['withdrawal_balance'] ) );
			$withdrawal_balance = (float) $withdrawal_balance;

			$user_id                = get_current_user_id();
			$current_currency = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );

			$withdrawal_request = get_post( $request_id );

			if ( 'approved' == $status ) {

				$requesting_user_wallet = get_user_meta( $requesting_user_id, 'wps_wallet', true );
				$requesting_user_wallet = (float) $requesting_user_wallet;
				$user_wallet = get_user_meta( $user_id, 'wps_wallet', true );
				$user_wallet = (float) $user_wallet;

				if ( $user_wallet >= $withdrawal_balance ) {
					$requesting_user_wallet += $withdrawal_balance;
					$returnid = update_user_meta( $requesting_user_id, 'wps_wallet', $requesting_user_wallet );

					if ( $returnid ) {
						$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
						$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
						// first user.
						$user1 = get_user_by( 'id', $requesting_user_id );
						$name1 = $user1->first_name . ' ' . $user1->last_name;

						$user2 = get_user_by( 'id', $user_id );
						$name2 = $user2->first_name . ' ' . $user2->last_name;
						$balance   = $current_currency . ' ' . $withdrawal_balance;
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {

							$mail_text1  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name1 ) . ",\r\n";
							$mail_text1 .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through wallet fund request by ', 'wallet-system-for-woocommerce' ) . $name2;
							$to1         = $user1->user_email;
							$from        = get_option( 'admin_email' );
							$subject     = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
							$headers1    = 'MIME-Version: 1.0' . "\r\n";
							$headers1   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
							$headers1   .= 'From: ' . $from . "\r\n" .
							'Reply-To: ' . $to1 . "\r\n";

							if ( key_exists( 'wps_wswp_wallet_credit', WC()->mailer()->emails ) ) {

								$customer_email = WC()->mailer()->emails['wps_wswp_wallet_credit'];
								if ( ! empty( $customer_email ) ) {
									$user       = get_user_by( 'id', $requesting_user_id );
									$currency  = get_woocommerce_currency();
									$balance_mail = $balance;
									$user_name       = $user->first_name . ' ' . $user->last_name;
									$email_status = $customer_email->trigger( $requesting_user_id, $user_name, $balance_mail, '' );
								}
							} else {

								$wallet_payment_gateway->send_mail_on_wallet_updation( $to1, $subject, $mail_text1, $headers1 );
							}
						}

						$transaction_type     = __( 'Wallet credited by user ', 'wallet-system-for-woocommerce' ) . $user2->user_email . __( ' to user ', 'wallet-system-for-woocommerce' ) . $user1->user_email;
						$wallet_transfer_data = array(
							'user_id'          => $requesting_user_id,
							'amount'           => $withdrawal_balance,
							'currency'         => $current_currency,
							'payment_method'   => __( 'Wallet Fund Request', 'wallet-system-for-woocommerce' ),
							'transaction_type' => $transaction_type,
							'transaction_type_1' => 'credit',
							'order_id'         => '',
							'note'             => '',

						);

						$wallet_payment_gateway->insert_transaction_data_in_table( $wallet_transfer_data );

						$user_wallet -= $withdrawal_balance;
						$update_user = update_user_meta( $user_id, 'wps_wallet', abs( $user_wallet ) );
						if ( $update_user ) {
							$balance   = $current_currency . ' ' . $withdrawal_balance;
							if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
								$mail_text2  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name2 ) . ",\r\n";
								$mail_text2 .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through wallet fund request to ', 'wallet-system-for-woocommerce' ) . $name1;
								$to2         = $user2->user_email;
								$headers2    = 'MIME-Version: 1.0' . "\r\n";
								$headers2   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
								$headers2   .= 'From: ' . $from . "\r\n" .
								'Reply-To: ' . $to2 . "\r\n";
								if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) ) {

									$customer_email = WC()->mailer()->emails['wps_wswp_wallet_debit'];
									if ( ! empty( $customer_email ) ) {
										$user       = get_user_by( 'id', $user_id );
										$currency  = get_woocommerce_currency();
										$balance_mail = $balance;
										$user_name       = $user->first_name . ' ' . $user->last_name;
										$customer_email->trigger( $user_id, $user_name, $balance_mail, '' );
									}
								} else {

									$wallet_payment_gateway->send_mail_on_wallet_updation( $to2, $subject, $mail_text2, $headers2 );
								}
							}

							$transaction_type = __( 'Wallet debited from user ', 'wallet-system-for-woocommerce' ) . $user2->user_email . __( ' wallet, transferred to user ', 'wallet-system-for-woocommerce' ) . $user1->user_email;
							$transaction_data = array(
								'user_id'          => $user_id,
								'amount'           => $withdrawal_balance,
								'currency'         => $current_currency,
								'payment_method'   => __( 'Wallet Fund Request', 'wallet-system-for-woocommerce' ),
								'transaction_type' => $transaction_type,
								'transaction_type_1' => 'debit',
								'order_id'         => '',
								'note'             => '',

							);

							$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
							$withdrawal_request->post_status = 'approved';
							wp_update_post( $withdrawal_request );
							$wps_wsfw_error_text = esc_html__( 'Wallet fund request is approved for user #', 'wallet-system-for-woocommerce' ) . $requesting_user_id;
							$message             = array(
								'msg'     => $wps_wsfw_error_text,
								'msgType' => 'success',
							);
						} else {
							$wps_wsfw_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
									$message             = array(
										'msg'     => $wps_wsfw_error_text,
										'msgType' => 'error',
									);
						}
					}
				} else {
					$wps_wsfw_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
					$message             = array(
						'msg'     => $wps_wsfw_error_text,
						'msgType' => 'error',
					);
				}
			}
			if ( 'rejected' == $status ) {
				if ( $user_id ) {

					$withdrawal_request->post_status = 'rejected';
					wp_update_post( $withdrawal_request );
					$wps_wsfw_error_text = esc_html__( 'Wallet fund request is rejected for user #', 'wallet-system-for-woocommerce' ) . $requesting_user_id;
					$message             = array(
						'msg'     => $wps_wsfw_error_text,
						'msgType' => 'success',
					);
				}
			}
			if ( 'pending1' === $status ) {

				if ( $user_id ) {
					$withdrawal_request->post_status = 'pending1';
					wp_update_post( $withdrawal_request );
					$wps_wsfw_error_text = esc_html__( 'Wallet withdrawal request status is changed to pending for user #', 'wallet-system-for-woocommerce' ) . $user_id;
					$message             = array(
						'msg'     => $wps_wsfw_error_text,
						'msgType' => 'success',
					);
				};
			}

			wp_send_json( $message );
		}
		wp_die();
	}
}
