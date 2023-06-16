<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WCMp_Gateway_Wps_Wallet' ) && class_exists( 'WCMp_Payment_Gateway' ) ) {
	/**
	 * Class to create wallet as payment gateway.
	 */
	class WCMp_Gateway_Wps_Wallet extends WCMp_Payment_Gateway {

		/**
		 * Payment gateway id.
		 *
		 * @var string
		 */
		public $id;
		/**
		 * Message on payment through wallet.
		 *
		 * @var array
		 */
		public $message = array();
		/**
		 * Payment gateway title.
		 *
		 * @var string
		 */
		public $gateway_title;
		/**
		 * Payment gateway.
		 *
		 * @var string
		 */
		public $payment_gateway;

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
			$this->id              = 'wps_wallet';
			$this->gateway_title   = __( 'Wallet Payment', 'wallet-system-for-woocommerce' );
			$this->payment_gateway = $this->id;
			$this->enabled         = get_wcmp_vendor_settings( 'payment_method_wps_wallet', 'payment' );
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
			global $WCMp; // phpcs:ignore due to compatibility issue.
			if ( 'Enable' != $this->enabled ) {
				$this->message[] = array(
					'message' => __( 'Invalid payment method', 'wallet-system-for-woocommerce' ),
					'type'    => 'error',
				);
				return false;
			}
			// phpcs:ignore
			if ( 'admin' != $this->transaction_mode ) {
				/* handle thesold time */
				$threshold_time = isset( $WCMp->vendor_caps->payment_cap['commission_threshold_time'] ) && ! empty( $WCMp->vendor_caps->payment_cap['commission_threshold_time'] ) ? $WCMp->vendor_caps->payment_cap['commission_threshold_time'] : 0; // phpcs:ignore due to compatibility issue.
				if ( $threshold_time > 0 ) {
					foreach ( $this->commissions as $index => $commission ) {
						if ( intval( ( gmdate( 'U' ) - get_the_date( 'U', $commission ) ) / ( 3600 * 24 ) ) < $threshold_time ) {
							unset( $this->commissions[ $index ] );
						}
					}
				}
				/* handle thesold amount */
				$thesold_amount = isset( $WCMp->vendor_caps->payment_cap['commission_threshold'] ) && ! empty( $WCMp->vendor_caps->payment_cap['commission_threshold'] ) ? $WCMp->vendor_caps->payment_cap['commission_threshold'] : 0; // phpcs:ignore due to compatibility issue.
				if ( $this->wps_get_transaction_total() > $thesold_amount ) {
					return true;
				} else {
					$this->message[] = array(
						'message' => __( 'Minimum threshold amount for commission withdrawal is ', 'wallet-system-for-woocommerce' ) . esc_html( $thesold_amount ),
						'type'    => 'error',
					);
					return false;
				}
			}
			return parent::validate_request();
		}

		/**
		 * Get total transaction amount.
		 *
		 * @return float
		 */
		public function wps_get_transaction_total() {
			$transaction_total = 0;
			$order_currency    = get_woocommerce_currency();
			if ( is_array( $this->commissions ) ) {
				foreach ( $this->commissions as $commission ) {
					$commission_id       = $commission;
					$commission_order_id = get_post_meta( $commission_id, '_commission_order_id', true );
					if ( ! empty( $commission_order_id ) ) {
						$order = wc_get_order( $commission_order_id );
						if ( $order ) {
							$order_currency = $order->get_currency();
						}
					}
					$commission_amount = WCMp_Commission::commission_totals( $commission, 'edit' );
					$credited_amount    = apply_filters( 'wps_wsfw_common_update_wallet_to_base_price', $commission_amount, $order_currency );
					$transaction_total += (float) $credited_amount;
				}
			}
			return $transaction_total;
		}


		/**
		 * Process the wallet.
		 *
		 * @return boolean
		 */
		private function process_wallet_payment() {
			$amount_to_pay   = round( $this->wps_get_transaction_total() - $this->transfer_charge( $this->transaction_mode ) - $this->wps_gateway_charge(), 2 );
			$vendor_id       = $this->vendor->id;
			$for_commissions = implode( ',', $this->commissions );
			if ( $vendor_id > 0 ) {

				if ( $amount_to_pay < 0 ) {
					$amount_to_pay = 0;
				}

				$walletamount = get_user_meta( $vendor_id, 'wps_wallet', true );
				$walletamount = empty( $walletamount ) ? 0 : $walletamount;

				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();

				$walletamount += $amount_to_pay;
				update_user_meta( $vendor_id, 'wps_wallet', abs( $walletamount ) );

				$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
				$balance   = $this->currency . ' ' . $amount_to_pay;
				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
					$user       = get_user_by( 'id', $vendor_id );
					$name       = $user->first_name . ' ' . $user->last_name;
					$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . ",\r\n";
					$mail_text .= __( 'Wallet credited through Commission by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance );
					$to         = $user->user_email;
					$from       = get_option( 'admin_email' );
					$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
					$headers    = 'MIME-Version: 1.0' . "\r\n";
					$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
					$headers   .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to . "\r\n";

					if ( key_exists( 'wps_wswp_wallet_credit', WC()->mailer()->emails ) ) {

						$customer_email = WC()->mailer()->emails['wps_wswp_wallet_credit'];
						if ( ! empty( $customer_email ) ) {
							$user       = get_user_by( 'id', $vendor_id );
							$currency  = get_woocommerce_currency();
							$balance_mail = $balance;
							$user_name       = $user->first_name . ' ' . $user->last_name;
							$customer_email->trigger( $vendor_id, $user_name, $balance_mail, '' );
						}
					} else {

						$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
					}
				}
				$transaction_type = __( 'Wallet credited through Commission received from commission id ', 'wallet-system-for-woocommerce' ) . $for_commissions;
				$transaction_data = array(
					'user_id'          => $vendor_id,
					'amount'           => $amount_to_pay,
					'currency'         => $this->currency,
					'payment_method'   => esc_html__( 'Manually By Admin', 'wallet-system-for-woocommerce' ),
					'transaction_type' => $transaction_type,
					'transaction_type_1' => 'credit',
					'order_id'         => $for_commissions,
					'note'             => '',
				);

				$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

				return true;
			}
			return false;
		}

		/**
		 * Order amount.
		 *
		 * @return array
		 */
		public function wps_vendor_wise_order_total() {
			$vendor_wise_order_total = array();
			$order_currency          = get_woocommerce_currency();
			if ( is_array( $this->commissions ) ) {
				foreach ( $this->commissions as $commission ) {
					$order_id      = get_post_meta( $commission, '_commission_order_id', true );
					$order_charges = wcmp_get_vendor_specific_order_charge( $order_id );
					$order         = wc_get_order( $order_id );
					if ( $order ) {
						$order_currency = $order->get_currency();
					}
					$order_total                          = apply_filters( 'wps_wsfw_common_update_wallet_to_base_price', $order_charges['order_total'], $order_currency );
					$vendor_total                         = apply_filters( 'wps_wsfw_common_update_wallet_to_base_price', $order_charges[ $this->vendor->id ], $order_currency );
					$vendor_wise_order_total[ $order_id ] = array(
						'order_total'     => $order_total,
						'vendor_total'    => $vendor_total,
						'order_marchants' => $order_charges['order_marchants'],
					);
				}
			}
			return $vendor_wise_order_total;
		}

		/**
		 * Return gateway charges.
		 *
		 * @return float
		 */
		public function wps_gateway_charge() {
			$gateway_charge           = 0;
			$is_enable_gateway_charge = get_wcmp_vendor_settings( 'payment_gateway_charge', 'payment' );
			$order_totals             = $this->wps_vendor_wise_order_total();
			if ( 'Enable' == $is_enable_gateway_charge ) {
				$payment_gateway_charge_type = get_wcmp_vendor_settings( 'payment_gateway_charge_type', 'payment', '', 'percent' );
				$gateway_charge_amount       = floatval( get_wcmp_vendor_settings( "gateway_charge_{$this->payment_gateway}", 'payment' ) );
				$carrier                     = get_wcmp_vendor_settings( 'gateway_charges_cost_carrier', 'payment', '', 'vendor' );
				if ( $gateway_charge_amount ) {
					foreach ( $order_totals as $order_id => $details ) {
						$order_gateway_charge = 0;
						$vendor_ratio         = ( $details['vendor_total'] / $details['order_total'] );
						if ( 'percent' === $payment_gateway_charge_type ) {
							$parcentize_charges   = ( $details['order_total'] * $gateway_charge_amount ) / 100;
							$order_gateway_charge = ( $vendor_ratio ) ? $vendor_ratio * $parcentize_charges : $parcentize_charges;
						} elseif ( 'fixed_with_percentage' === $payment_gateway_charge_type ) {
							$gateway_fixed_charge_amount = floatval( get_wcmp_vendor_settings( "gateway_charge_fixed_with_{$this->payment_gateway}", 'payment' ) );
							$parcentize_charges          = ( ( $details['order_total'] * $gateway_charge_amount ) / 100 );
							$fixed_charges               = floatval( $gateway_fixed_charge_amount ) / count( $details['order_marchants'] );
							$order_gateway_charge        = ( $vendor_ratio ) ? ( $vendor_ratio * $parcentize_charges ) + $fixed_charges : ( $parcentize_charges + $fixed_charges );
						} else {
							$fixed_charges        = floatval( $gateway_charge_amount ) / count( $details['order_marchants'] );
							$order_gateway_charge = $fixed_charges;
						}
						$gateway_charge += $order_gateway_charge;
					}
					if ( 'separate' === $carrier ) {
						if ( 'percent' === $payment_gateway_charge_type ) {
							$gateway_charge = ( $this->wps_get_transaction_total() * $gateway_charge_amount ) / 100;
						} elseif ( 'fixed_with_percentage' === $payment_gateway_charge_type ) {
							$gateway_fixed_charge_amount = floatval( get_wcmp_vendor_settings( "gateway_charge_fixed_with_{$this->payment_gateway}", 'payment' ) );
							$gateway_charge              = ( ( $this->wps_get_transaction_total() * $gateway_charge_amount ) / 100 ) + floatval( $gateway_fixed_charge_amount );
						} else {
							$gateway_charge = floatval( $gateway_charge_amount );
						}
					}
					if ( 'admin' === $carrier ) {
						$gateway_charge = 0;
					}
					return $gateway_charge;
				}
			}

		}

	}
}
