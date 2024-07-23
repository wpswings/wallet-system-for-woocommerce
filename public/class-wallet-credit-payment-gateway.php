<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Make sure WooCommerce is active.
$active_plugins = (array) get_option( 'active_plugins', array() );
if ( is_multisite() ) {
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
}
if ( ! ( array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) || in_array( 'woocommerce/woocommerce.php', $active_plugins ) ) ) {
	return;
}

/**
 * Add the gateway to WC Available Gateways
 *
 * @since 1.0.0
 *
 * @param array $gateways all available WC gateways.
 * @return array $gateways all WC gateways + Wallet gateway
 */
function wps_wsfw_wallet_gateway( $gateways ) {
	$customer_id = get_current_user_id();
	if ( $customer_id > 0 ) {
		$gateways[] = 'Wallet_Credit_Payment_Gateway';
	}
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'wps_wsfw_wallet_gateway', 10, 1 );

/**
 * Wallet Payment Gateway
 *
 * Provides an Offline Payment Gateway; mainly for testing purposes.
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class Cashback_Wallet_Gateway
 * @extends WC_Payment_Gateway
 * @version 1.0.0
 * @package Wallet_System_For_Woocommerce
 * @throws Exception Excption.
 */
function wps_wsfw_wallet_payment_gateway_init() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	/**
	 * Class to create wallet payment gateway.
	 */
	class Wallet_Credit_Payment_Gateway extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 *
		 * @param boolean $is_block is the variable for block checkout.
		 */
		public function __construct( $is_block = true ) {

			$this->id                 = 'wps_wcb_wallet_payment_gateway';
			$this->icon               = apply_filters( 'woocommerce_wallet_gateway_icon', '' );
			$this->has_fields         = false;
			$this->method_title       = __( 'Wallet Payment', 'wallet-system-for-woocommerce' );
			$this->method_description = __( 'This payment method is used for user who want to make payment from their Wallet.', 'wallet-system-for-woocommerce' );

			// Load the settings.
			$this->init_form_fields();

			// Define user set variables.
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );

			$this->enabled      = $this->get_option( 'enabled' );

			// Actions.
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'enabled'      => array(
					'title'   => __( 'Enable/Disable', 'wallet-system-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Wallet Payment', 'wallet-system-for-woocommerce' ),
					'default' => 'yes',
				),

				'title'        => array(
					'title'       => __( 'Title', 'wallet-system-for-woocommerce' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wallet-system-for-woocommerce' ),
					'default'     => __( 'Wallet Payment', 'wallet-system-for-woocommerce' ),
					'desc_tip'    => true,
				),

				'description'  => array(
					'title'       => __( 'Description', 'wallet-system-for-woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'wallet-system-for-woocommerce' ),
					'default'     => '',
					'desc_tip'    => true,
				),

			);
		}

		/**
		 * Current Wallet Balance.
		 */
		public function get_icon() {
			$customer_id = get_current_user_id();
			if ( $customer_id > 0 ) {
				$walletamount = get_user_meta( $customer_id, 'wps_wallet', true );
				$walletamount = empty( $walletamount ) ? 0 : $walletamount;
				$walletamount = apply_filters( 'wps_wsfw_show_converted_price', $walletamount );
				echo '<b>' . esc_html__( '[Your Amount :', 'wallet-system-for-woocommerce' ) . ' ' . wp_kses_post( wc_price( $walletamount ) ) . ']</b>';
				$order_number = get_user_meta( $customer_id, 'wsfw_enable_wallet_negative_balance_limit_order', true );
				$order_limit = get_option( 'wsfw_enable_wallet_negative_balance_limit_order' );

				if ( intval( $order_number ) >= intval( $order_limit ) ) {
					do_action( 'wps_wsfw_for_limit_negative_balance' );
				}
			}
		}

		  /**
		   * Process a refund if supported.
		   *
		   * @param  int    $order_id Order ID.
		   * @param  float  $amount Refund amount.
		   * @param  string $reason Refund reason.
		   * @throws Exception Exception.
		   * @return bool|WP_Error
		   */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			$order = wc_get_order( $order_id );
			$refund_reason = $reason ? $reason : __( 'Wallet refund #', 'wallet-system-for-woocommerce' ) . $order->get_order_number();
			$transaction_id = '';
			if ( ! $transaction_id ) {
				throw new Exception( esc_html__( 'Refund not credited to customer', 'wallet-system-for-woocommerce' ) );
			}
			do_action( 'wps_wallet_order_refund_actioned', $order, $amount, $transaction_id );
			return true;
		}

		/**
		 * Process the payment and return the result.
		 *
		 * @param int $order_id order id.
		 * @return void
		 */
		public function process_payment_manual( $order_id ) {

			$order       = wc_get_order( $order_id );
			$payment_method = $order->get_payment_method();
			if ( 'wps_wcb_wallet_payment_gateway' === $payment_method ) {
				$payment_method = esc_html__( 'Wallet Payment', 'wallet-system-for-woocommerce' );
			}
			$order_total = $order->get_total();
			if ( $order_total < 0 ) {
				$order_total = 0;
			}
			$debited_amount   = apply_filters( 'wps_wsfw_convert_to_base_price', $order_total );
			$current_currency = apply_filters( 'wps_wsfw_get_current_currency', $order->get_currency() );
			$customer_id      = $order->get_user_id();
			$is_auto_complete = get_option( 'wsfw_wallet_payment_order_status_checkout', '' );
			$is_auto_complete_bool = true;
			$walletamount = get_user_meta( $customer_id, 'wps_wallet', true );
			$walletamount = empty( $walletamount ) ? 0 : $walletamount;
			$is_condition_true = false;

			if ( 'on' == get_option( 'wsfw_enable_wallet_negative_balance' ) ) {

				$limit = get_option( 'wsfw_enable_wallet_negative_balance_limit' );
				$order_number = get_user_meta( $customer_id, 'wsfw_enable_wallet_negative_balance_limit_order', true );
				$order_limit = get_option( 'wsfw_enable_wallet_negative_balance_limit_order' );
				$is_pro = false;
				$is_pro = apply_filters( 'wsfw_check_pro_plugin', $is_pro );

				if ( $is_pro ) {

					if ( intval( $order_number ) > intval( $order_limit ) ) {

						$is_condition_true = true;
					}

					if ( ( intval( $walletamount ) ) <= intval( $limit ) ) {

						$total_balance = intval( $walletamount ) + intval( $limit );

						if ( $total_balance >= $order_total ) {

							$is_condition_true = true;
						}
					} elseif ( ( intval( $walletamount ) ) >= ( intval( $limit ) ) ) {
						$total_balance = intval( $walletamount ) + intval( $limit );

						if ( $total_balance >= $order_total ) {
							$is_condition_true = true;
						}
					}
				} else {

					if ( $walletamount >= $order_total ) {
						$is_condition_true = true;
					}
				}
			} else {
				if ( $debited_amount <= $walletamount ) {
					$is_condition_true = true;
				}
			}

			if ( $is_condition_true ) {

				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();

				if ( $walletamount < 0 ) {
					$walletamount = ( ( $walletamount ) - ( $debited_amount ) );

				} else {

					$walletamount = abs( $walletamount ) - abs( $debited_amount );
				}

				$update_wallet          = update_user_meta( $customer_id, 'wps_wallet', ( $walletamount ) );

				if ( $update_wallet ) {
					$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
					$balance   = $current_currency . ' ' . $order_total;
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$user       = get_user_by( 'id', $customer_id );
						$name       = $user->first_name . ' ' . $user->last_name;
						$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . ",\r\n";
						$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' from your wallet through purchasing.', 'wallet-system-for-woocommerce' );
						$to         = $user->user_email;
						$from       = get_option( 'admin_email' );
						$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
						$headers    = 'MIME-Version: 1.0' . "\r\n";
						$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers   .= 'From: ' . $from . "\r\n" .
							'Reply-To: ' . $to . "\r\n";

						if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) ) {

							$customer_email = WC()->mailer()->emails['wps_wswp_wallet_debit'];
							if ( ! empty( $customer_email ) ) {
								$user       = get_user_by( 'id', $customer_id );
								$currency  = get_woocommerce_currency();
								$balance_mail = $balance;
								$user_name       = $user->first_name . ' ' . $user->last_name;
								$email_status = $customer_email->trigger( $customer_id, $user_name, $balance_mail, '' );
							}
						} else {

							$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
						}
					}
				}

				$transaction_type = __( 'Wallet debited through purchasing ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
				$transaction_data = array(
					'user_id'          => $customer_id,
					'amount'           => $order_total,
					'currency'         => $current_currency,
					'payment_method'   => $payment_method,
					'transaction_type' => htmlentities( $transaction_type ),
					'transaction_type_1' => 'debit',
					'order_id'         => $order_id,
					'note'             => '',
				);
				$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
				if ( isset( $is_auto_complete ) && 'on' == $is_auto_complete ) {

					// Mark as on-hold (we're awaiting the payment).
					$order->update_status( 'completed', __( 'Wallet payment completed', 'wallet-system-for-woocommerce' ) );

					$is_auto_complete_bool = false;

				}

				if ( $is_auto_complete_bool ) {
					// Mark as on-hold (we're awaiting the payment).
					$order->update_status( 'processing', __( 'Awaiting Wallet payment', 'wallet-system-for-woocommerce' ) );

				}
			} else {
				$order->update_status( 'failed', __( 'Do not have sufficient amount in wallet.', 'wallet-system-for-woocommerce' ) );

			}

		}

		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id order id.
		 * @return array
		 */
		public function process_payment( $order_id ) {

			$order       = wc_get_order( $order_id );
			$payment_method = $order->payment_method;
			if ( 'wps_wcb_wallet_payment_gateway' === $payment_method ) {
				$payment_method = esc_html__( 'Wallet Payment', 'wallet-system-for-woocommerce' );
			}
			$order_total = $order->get_total();
			if ( $order_total < 0 ) {
				$order_total = 0;
			}
			$debited_amount   = apply_filters( 'wps_wsfw_convert_to_base_price', $order_total );
			$current_currency = apply_filters( 'wps_wsfw_get_current_currency', $order->get_currency() );
			$customer_id      = $order->get_user_id();
			$is_auto_complete = get_option( 'wsfw_wallet_payment_order_status_checkout', '' );
			$is_auto_complete_bool = true;
			$walletamount = get_user_meta( $customer_id, 'wps_wallet', true );
			$walletamount = empty( $walletamount ) ? 0 : $walletamount;
			$is_condition_true = false;

			if ( 'on' == get_option( 'wsfw_enable_wallet_negative_balance' ) ) {

				$is_condition_true = true;

			} else {
				if ( $debited_amount <= $walletamount ) {
					$is_condition_true = true;
				}
			}

			if ( $is_condition_true ) {

				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();

				if ( $walletamount < 0 ) {
					$walletamount = ( ( $walletamount ) - ( $debited_amount ) );

				} else {

					$walletamount = abs( $walletamount ) - abs( $debited_amount );
				}

				$update_wallet          = update_user_meta( $customer_id, 'wps_wallet', ( $walletamount ) );

				if ( $update_wallet ) {
					$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
					$balance   = $current_currency . ' ' . $order_total;
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$user       = get_user_by( 'id', $customer_id );
						$name       = $user->first_name . ' ' . $user->last_name;
						$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . ",\r\n";
						$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' from your wallet through purchasing.', 'wallet-system-for-woocommerce' );
						$to         = $user->user_email;
						$from       = get_option( 'admin_email' );
						$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
						$headers    = 'MIME-Version: 1.0' . "\r\n";
						$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers   .= 'From: ' . $from . "\r\n" .
							'Reply-To: ' . $to . "\r\n";

						if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) ) {

							$customer_email = WC()->mailer()->emails['wps_wswp_wallet_debit'];
							if ( ! empty( $customer_email ) ) {
								$user       = get_user_by( 'id', $customer_id );
								$currency  = get_woocommerce_currency();
								$balance_mail = $balance;
								$user_name       = $user->first_name . ' ' . $user->last_name;
								$email_status = $customer_email->trigger( $customer_id, $user_name, $balance_mail, '' );
							}
						} else {

							$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
						}
					}
				}

				$transaction_type = __( 'Wallet debited through purchasing ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
				$transaction_data = array(
					'user_id'          => $customer_id,
					'amount'           => $order_total,
					'currency'         => $current_currency,
					'payment_method'   => $payment_method,
					'transaction_type' => htmlentities( $transaction_type ),
					'transaction_type_1' => 'debit',
					'order_id'         => $order_id,
					'note'             => '',
				);
				$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
				if ( isset( $is_auto_complete ) && 'on' == $is_auto_complete ) {

					// Mark as on-hold (we're awaiting the payment).
					$order->update_status( 'completed', __( 'Wallet payment completed', 'wallet-system-for-woocommerce' ) );

					// Remove cart.
					WC()->cart->empty_cart();
					$is_auto_complete_bool = false;

				}

				if ( $is_auto_complete_bool ) {
					// Mark as on-hold (we're awaiting the payment).
					$order->update_status( 'processing', __( 'Awaiting Wallet payment', 'wallet-system-for-woocommerce' ) );

					// Remove cart.
					if ( ! empty( WC()->cart ) ) {
						WC()->cart->empty_cart();

					}
				}
			} else {
				$order->update_status( 'failed', __( 'Do not have sufficient amount in wallet.', 'wallet-system-for-woocommerce' ) );

			}
			// Return thankyou redirect.
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}
	}
}
add_action( 'plugins_loaded', 'wps_wsfw_wallet_payment_gateway_init' );

