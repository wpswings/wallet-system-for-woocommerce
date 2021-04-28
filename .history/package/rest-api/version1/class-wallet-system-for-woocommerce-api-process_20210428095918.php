<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
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
	 * @package    Hydroshop_Api_Management
	 * @subpackage Hydroshop_Api_Management/includes
	 * @author     MakeWebBetter <makewebbetter.com>
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
		 * Define the function to process data for custom endpoint.
		 *
		 * @since    1.0.0
		 * @param   Array $wsfw_request  data of requesting headers and other information.
		 * @return  Array $mwb_wsfw_rest_response    returns processed data and status of operations.
		 */
		public function mwb_wsfw_default_process( $wsfw_request ) {
			$mwb_wsfw_rest_response = array();

			// Write your custom code here.

			$mwb_wsfw_rest_response['status'] = 200;
			//$mwb_wsfw_rest_response['data'] = $wsfw_request->get_headers();
			$mwb_wsfw_rest_response['data'] = 'hello';
			return $mwb_wsfw_rest_response;
		}

		*
		/**
		 * Returns user's wallet balance
		 *
		 * @param int $user_id
		 * @return array
		 */
		public function get_wallet_balance( $user_id ) {
			$mwb_wsfw_rest_response = array();

			if ( ! empty( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user ) {
					$wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );
					if ( empty( $wallet_bal ) ) {
						$wallet_bal = "0.00";
					}
					$mwb_wsfw_rest_response['data'] = $wallet_bal;
				} else {
					$mwb_wsfw_rest_response['data'] = esc_html__( 'User does not exist', 'wallet-system-for-woocommerce' );
				}
            }
			$mwb_wsfw_rest_response['status'] = 200;
		
			return $mwb_wsfw_rest_response;
		}

		/**
		 * Edit user wallet( credit/debit )
		 *
		 * @param array $request
		 * @return array
		 */
		public function update_wallet_balance( $request ) {
			$mwb_wsfw_rest_response = array();

			$user_id = $request['id'];
			if ( ! empty( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user ) {
					$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
					$updated_amount     = sanitize_text_field( $request['amount'] );
					$wallet_action      = sanitize_text_field( $request['action'] );
					$transaction_detail = sanitize_text_field( $request['transaction_details'] );
					$payment_method     = ! empty( $request['note'] )? sanitize_text_field( $request['payment_method'] ) : '';
					$note               = ! empty( $request['note'] )? sanitize_text_field( $request['note'] ) : '';
					$order_id           = ! empty( $request['order_id'] )? sanitize_text_field( $request['order_id'] ) : '';
					$wallet           = get_user_meta( $user_id, 'mwb_wallet', true );

					if ( 'credit' == $wallet_action ) { 
						$wallet += $updated_amount;
						$update_wallet = update_user_meta( $user_id, 'mwb_wallet', $wallet );
						$transaction_type = 'Credited by admin';
						$mail_message     = __( 'Merchant has credited your wallet by '. wc_price( $updated_amount ), 'wallet-system-for-woocommerce' );

					} elseif ( 'debit' == $wallet_action ) { 
						if ( $wallet < $updated_amount ) {
							$wallet = 0;
						} else {
							$wallet -= $updated_amount;
						}
						$update_wallet = update_user_meta( $user_id, 'mwb_wallet', abs($wallet) );
						$transaction_type = 'Debited by admin';
						$mail_message     = __( 'Merchant has deducted '. wc_price( $updated_amount ). ' from your wallet.', 'wallet-system-for-woocommerce' );
						
					}
					if ( $update_wallet ) {
						$wallet         = get_user_meta( $user_id, 'mwb_wallet', true );
						$send_email_enable = get_option( 'mwb_wsfw_enable_email_notification_for_wallet_update', '' );
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$name = $user->first_name . ' ' . $user->last_name;
							$mail_text = sprintf( "Hello %s,<br/>", $name );
							$mail_text .= $mail_message;
							$to = $user->user_email;
							$from = get_option( 'admin_email' );
							
							$subject  = "Wallet updating notification";
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers  .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							$headers  .= 'From: '. $from . "\r\n" .
								'Reply-To: ' . $to . "\r\n";
				
							$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers ); 
						}
				
						$transaction_data = array(
							'user_id'          => $user_id,
							'amount'           => $updated_amount,
							'payment_method'   => $payment_method,
							'transaction_type' => $transaction_type,
							'order_id'         => $order_id,
							'note'             => $note,
				
						);
				
						$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
						if ( $result ) {
							$data = array( 'response' => 'success', 'balance' => $wallet, 'transaction_id' => $result );
						}
					}
					$mwb_wsfw_rest_response['data'] = $data;
				} else {
					$mwb_wsfw_rest_response['data'] = esc_html__( 'User does not exist', 'wallet-system-for-woocommerce' );
				}
            }
			$mwb_wsfw_rest_response['status'] = 200;
		
			return $mwb_wsfw_rest_response;
		}

		/**
		 * Returns user's all wallet transaction details
		 *
		 * @param int $user_id
		 * @return array
		 */
		public function get_user_wallet_transactions( $user_id ) {
			$mwb_wsfw_rest_response = array();

			if ( ! empty( $user_id ) ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user ) {
					global $wpdb;
					$table_name = $wpdb->prefix . 'mwb_wsfw_wallet_transaction';
					$transactions = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY Id" );
					if ( ! empty( $transactions ) && is_array($transactions ) ) {
						$mwb_wsfw_rest_response['data'] = $transactions;
					} else {
						$mwb_wsfw_rest_response['data'] = array();
					}
				} else {
					$mwb_wsfw_rest_response['data'] = esc_html__( 'User does not exist', 'wallet-system-for-woocommerce' );
				}
            }
			$mwb_wsfw_rest_response['status'] = 200;
		
			return $mwb_wsfw_rest_response;
		}

	}
}
