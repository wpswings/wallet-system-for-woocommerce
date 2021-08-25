<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WCMp_Gateway_Mwb_Wallet' ) && class_exists( 'WCMp_Payment_Gateway' ) ) {
	/**
	 * Class to create wallet as payment gateway.
	 */
	class WCMp_Gateway_Mwb_Wallet extends WCMp_Payment_Gateway {

		public $id;
		public $message = array();
		public $gateway_title;
		public $payment_gateway;

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
			$this->id              = 'mwb_wallet';
			$this->gateway_title   = __( 'Wallet Payment', 'wallet-system-for-woocommerce' );
			$this->payment_gateway = $this->id;
			$this->enabled         = get_wcmp_vendor_settings( 'payment_method_mwb_wallet', 'payment' );
		}

		/**
		 * Process the payment and return the result.
		 *
		 * @param  object $vendor vendor.
		 * @param  array  $commissions commissions.
		 * @param  string $transaction_mode transaction mode.
		 * @return array
		 */
		public function process_payment( $vendor, $commissions = array(), $transaction_mode = 'auto' ) {
			$this->vendor           = $vendor;
			$this->commissions      = $commissions;
			$this->currency         = get_woocommerce_currency();
			$this->transaction_mode = $transaction_mode;
			if ( $this->validate_request() ) {
				if ( $this->process_wallet_payment() ) {
					$this->record_transaction();
					if ( $this->transaction_id ) {
						return array(
							'message'        => __( 'New transaction has been initiated', 'wallet-system-for-woocommerce' ),
							'type'           => 'success',
							'transaction_id' => $this->transaction_id,
						);
					}
				} else {
					return $this->message;
				}
			} else {
				return $this->message;
			}
		}

		/**
		 * Validate request.
		 *
		 * @return boolean
		 */
		public function validate_request() {
			global $WCMp;
			if ( $this->enabled != 'Enable' ) {
				$this->message[] = array(
					'message' => __( 'Invalid payment method', 'wallet-system-for-woocommerce' ),
					'type'    => 'error',
				);
				return false;
			}
			if ( $this->transaction_mode != 'admin' ) {
				/* handel thesold time */
				$threshold_time = isset( $WCMp->vendor_caps->payment_cap['commission_threshold_time'] ) && ! empty( $WCMp->vendor_caps->payment_cap['commission_threshold_time'] ) ? $WCMp->vendor_caps->payment_cap['commission_threshold_time'] : 0;
				if ( $threshold_time > 0 ) {
					foreach ( $this->commissions as $index => $commission ) {
						if ( intval( ( date( 'U' ) - get_the_date( 'U', $commission ) ) / ( 3600 * 24 ) ) < $threshold_time ) {
							unset( $this->commissions[ $index ] );
						}
					}
				}
				/* handel thesold amount */
				$thesold_amount = isset( $WCMp->vendor_caps->payment_cap['commission_threshold'] ) && ! empty( $WCMp->vendor_caps->payment_cap['commission_threshold'] ) ? $WCMp->vendor_caps->payment_cap['commission_threshold'] : 0;
				if ( $this->get_transaction_total() > $thesold_amount ) {
					return true;
				} else {
					$this->message[] = array(
						'message' => __( 'Minimum threshold amount for commission withdrawal is ' . $thesold_amount, 'wallet-system-for-woocommerce' ),
						'type'    => 'error',
					);
					return false;
				}
			}
			return parent::validate_request();
		}

		/**
		 * Process the wallet.
		 *
		 * @return boolean
		 */
		private function process_wallet_payment() {
			$amount_to_pay   = round( $this->get_transaction_total() - $this->transfer_charge( $this->transaction_mode ) - $this->gateway_charge(), 2 );
			$for_commissions = implode( ',', $this->commissions );
			$credited_amount = apply_filters( 'mwb_wsfw_update_wallet_to_base_price', $amount_to_pay, $this->currency );
			$vendor_id       = $this->vendor->id;
			echo $credited_amount;
			echo $this->currency;
			die;
			if ( $vendor_id > 0 ) {
				$walletamount = get_user_meta( $vendor_id, 'mwb_wallet', true );
				$walletamount = empty( $walletamount ) ? 0 : $walletamount;

				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$walletamount          += $credited_amount;
				$update_wallet          = update_user_meta( $vendor_id, 'mwb_wallet', abs( $walletamount ) );

				if ( $update_wallet ) {
					$send_email_enable = get_option( 'mwb_wsfw_enable_email_notification_for_wallet_update', '' );
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$user       = get_user_by( 'id', $vendor_id );
						$name       = $user->first_name . ' ' . $user->last_name;
						$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
						$mail_text .= __( 'Wallet credited through Commission by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount_to_pay, array( 'currency' => $this->currency ) );
						$to         = $user->user_email;
						$from       = get_option( 'admin_email' );
						$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
						$headers    = 'MIME-Version: 1.0' . "\r\n";
						$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers   .= 'From: ' . $from . "\r\n" .
							'Reply-To: ' . $to . "\r\n";
						$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );

					}
					$transaction_type = __( 'Wallet credited through Commission received from commission id ', 'wallet-system-for-woocommerce' ) . $for_commissions;
					$transaction_data = array(
						'user_id'          => $vendor_id,
						'amount'           => $amount_to_pay,
						'currency'         => $this->currency,
						'payment_method'   => esc_html__( 'Manually By Admin', 'wallet-system-for-woocommerce' ),
						'transaction_type' => $transaction_type,
						'order_id'         => $for_commissions,
						'note'             => '',
					);

					$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
					return true;
				} else {
					return false;
				}
			}
			return false;
		}

	}
}
