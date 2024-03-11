<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/package/rest-api/version1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Wallet_System_For_Woocommerce_Api_Process' ) ) {

	/**
	 * The plugin API class.
	 *
	 * This is used to define the functions and data manipulation for custom endpoints.
	 *
	 * @since      1.0.0
	 * @package    Wallet_System_For_Woocommerce
	 * @subpackage Wallet_System_For_Woocommerce/package/rest-api/version1
	 * @author     WP Swings <webmaster@wpswings.com>
	 */
	class Wallet_System_For_Woocommerce_Api_Process {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

		}

		/**
		 * Returns all user details
		 *
		 * @return array
		 */
		public function wps_wsfw_get_users() {
			$wps_wsfw_rest_response = array();
			$user_details           = array();
			$users                  = get_users( 'orderby=id' );
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					$wallet_bal     = get_user_meta( $user->ID, 'wps_wallet', true );
					$user_details[] = array(
						'user_id'        => $user->ID,
						'user_name'      => esc_html( $user->display_name ),
						'user_email'     => esc_html( $user->user_email ),
						'user_role'      => esc_html( $user->roles[0] ),
						'wallet_balance' => $wallet_bal,
					);
				}
				$wps_wsfw_rest_response['data'] = $user_details;
			}
			$wps_wsfw_rest_response['status'] = 200;

			return $wps_wsfw_rest_response;
		}

		/**
		 * Returns user's wallet balance
		 *
		 * @param int $user_id user id of user.
		 * @return array
		 */
		public function get_wallet_balance( $user_id ) {
			$wps_wsfw_rest_response = array();

			if ( ! empty( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user ) {
					$wallet_bal = get_user_meta( $user_id, 'wps_wallet', true );
					if ( empty( $wallet_bal ) ) {
						$wallet_bal = '0.00';
					}
					$wps_wsfw_rest_response['data'] = $wallet_bal;
				} else {
					$wps_wsfw_rest_response['data'] = esc_html__( 'User does not exist', 'wallet-system-for-woocommerce' );
				}
			}
			$wps_wsfw_rest_response['status'] = 200;

			return $wps_wsfw_rest_response;
		}

		/**
		 * Edit user wallet( credit/debit )
		 *
		 * @param array $request containing all the value passes for updating wallet.
		 * @return array
		 */
		public function update_wallet_balance( $request ) {
			$wps_wsfw_rest_response = array();

			$user_id = $request['id'];
			if ( ! empty( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user ) {
					$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
					$updated_amount         = sanitize_text_field( $request['amount'] );
					$wallet_action          = sanitize_text_field( $request['action'] );
					$transaction_detail     = sanitize_text_field( $request['transaction_detail'] );
					$payment_method         = ! empty( $request['payment_method'] ) ? sanitize_text_field( $request['payment_method'] ) : '';
					$note                   = ! empty( $request['note'] ) ? sanitize_text_field( $request['note'] ) : '';
					$order_id               = ! empty( $request['order_id'] ) ? sanitize_text_field( $request['order_id'] ) : '';
					$wallet                 = get_user_meta( $user_id, 'wps_wallet', true );
					$wallet                 = empty( $wallet ) ? 0 : $wallet;
					if ( 'credit' === $wallet_action ) {
						$wallet       += $updated_amount;
						$update_wallet = update_user_meta( $user_id, 'wps_wallet', $wallet );
						$mail_message  = __( 'Your wallet has credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $updated_amount );
						if ( key_exists( 'wps_wswp_wallet_credit', WC()->mailer()->emails ) ) {
							$customer_email = WC()->mailer()->emails['wps_wswp_wallet_credit'];
							if ( ! empty( $customer_email ) ) {
								$user       = get_user_by( 'id', $user_id );
								$currency  = get_woocommerce_currency();
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
						$update_wallet = update_user_meta( $user_id, 'wps_wallet', abs( $wallet ) );
						$currency  = get_woocommerce_currency();
						$balance   = $currency . ' ' . $updated_amount;
						$mail_message  = __( 'Your wallet has been debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance );
						if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) ) {

							$customer_email = WC()->mailer()->emails['wps_wswp_wallet_debit'];
							if ( ! empty( $customer_email ) ) {
								$user       = get_user_by( 'id', $user_id );
								$balance_mail = $balance;
								$user_name       = $user->first_name . ' ' . $user->last_name;
								$customer_email->trigger( $user_id, $user_name, $balance_mail, '' );
							}
						}
					}
					if ( $update_wallet ) {
						$wallet            = get_user_meta( $user_id, 'wps_wallet', true );
						$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
						$customer_email_credit = '';
						$customer_email_debit = '';
						if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) || key_exists( 'wps_wswp_wallet_credit', WC()->mailer()->emails ) ) {

							$customer_email_credit = WC()->mailer()->emails['wps_wswp_wallet_credit'];
							$customer_email_debit = WC()->mailer()->emails['wps_wswp_wallet_debit'];
						}
						if ( empty( $customer_email_credit ) || empty( $customer_email_debit ) ) {

							if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
								$name       = $user->first_name . ' ' . $user->last_name;
								$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . ",\r\n";
								$mail_text .= $mail_message;
								$to         = $user->user_email;
								$from       = get_option( 'admin_email' );
								$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
								$headers    = 'MIME-Version: 1.0' . "\r\n";
								$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
								$headers   .= 'From: ' . $from . "\r\n" .
									'Reply-To: ' . $to . "\r\n";

								$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
							}
						}

						$transaction_data = array(
							'user_id'          => $user_id,
							'amount'           => $updated_amount,
							'currency'         => get_woocommerce_currency(),
							'payment_method'   => $payment_method,
							'transaction_type' => $transaction_detail,
							'transaction_type_1' => $wallet_action,
							'order_id'         => $order_id,
							'note'             => $note,

						);

						$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
						if ( $result ) {
							$data = array(
								'response'       => 'success',
								'balance'        => $wallet,
								'transaction_id' => $result,
							);
						}
					}
					$wps_wsfw_rest_response['data'] = $data;
				} else {
					$wps_wsfw_rest_response['data'] = esc_html__( 'User does not exist', 'wallet-system-for-woocommerce' );
				}
			}
			$wps_wsfw_rest_response['status'] = 200;

			return $wps_wsfw_rest_response;
		}

		/**
		 * Returns user's all wallet transaction details
		 *
		 * @param int $user_id user id.
		 * @return array
		 */
		public function get_user_wallet_transactions( $user_id ) {
			$wps_wsfw_rest_response = array();

			if ( ! empty( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user ) {
					global $wpdb;
					$transactions = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction WHERE user_id = %s ORDER BY Id', $user_id ) );
					if ( ! empty( $transactions ) && is_array( $transactions ) ) {
						$wps_wsfw_rest_response['data'] = $transactions;
					} else {
						$wps_wsfw_rest_response['data'] = array();
					}
				} else {
					$wps_wsfw_rest_response['data'] = esc_html__( 'User does not exist', 'wallet-system-for-woocommerce' );
				}
			}
			$wps_wsfw_rest_response['status'] = 200;

			return $wps_wsfw_rest_response;
		}

	}
}
