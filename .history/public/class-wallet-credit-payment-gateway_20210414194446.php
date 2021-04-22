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

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
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
function mwb_wsfw_wallet_gateway( $gateways ) {
	$gateways[] = 'Wallet_Credit_Payment_Gateway';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'mwb_wsfw_wallet_gateway', 10, 1 );

/**
 * Wallet Payment Gateway
 *
 * Provides an Offline Payment Gateway; mainly for testing purposes.
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class Cashback_Wallet_Gateway
 * @extends WC_Payment_Gateway
 * @version 1.0.0
 * @package WooCommerce/Classes/Payment
 */
function mwb_wsfw_wallet_payment_gateway_init() {

	/**
	 * Class to create wallet payment gateway.
	 */
	// @codingStandardsIgnoreLine
	class Wallet_Credit_Payment_Gateway extends WC_Payment_Gateway {
		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {

			$this->id                 = 'mwb_wsfw_wallet_payment_gateway';
			$this->icon               = apply_filters( 'woocommerce_wallet_gateway_icon', '' );
			$this->has_fields         = false;
			$this->method_title       = __( 'Wallet Payment', 'wallet-system-for-woocommerce' );
			$this->method_description = __( 'This payment method is used for user who want to make payment from their Wallet.', 'wallet-system-for-woocommerce' );

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
			
			// Define user set variables.
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', $this->description );
			$this->enabled = $this->get_option( 'enabled' );

			// Actions.
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		}

		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {

			$this->form_fields = apply_filters(
				'cashback_wallet_gateway_form_fields',
				array(
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
						'default'     => __( 'Your amount is deducted from your wallet.', 'wallet-system-for-woocommerce' ),
						'desc_tip'    => true,
					),

					'instructions' => array(
						'title'       => __( 'Instructions', 'wallet-system-for-woocommerce' ),
						'type'        => 'textarea',
						'description' => __( 'Instructions that will be added to the thank you page and emails.', 'wallet-system-for-woocommerce' ),
						'default'     => '',
						'desc_tip'    => true,
					),
				)
			);
		}

		/**
		 * Current Wallet Balance.
		 */
		public function get_icon() {
			$customer_id = get_current_user_id();
			if ( $customer_id > 0 ) {
				$walletamount = get_user_meta( $customer_id, 'mwb_wallet', true );
				return '<b>' . __( '[Your Amount :', 'wallet-system-for-woocommerce' ) . ' ' . wc_price( $walletamount ) . ']</b>';
			}
		}

		/**
		 * Output for the order received page.
		 */
		public function thankyou_page() {
			if ( $this->instructions ) {
				$allowed_html = array(
					'p' => array(
						'class' => '',
					),
				);
				echo wp_kses( wpautop( wptexturize( $this->instructions ) ), $allowed_html );
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
			if ( 'mwb_wsfw_wallet_payment_gateway' === $payment_method ) {
				$payment_method = 'Wallet Payment';
			}
			$order_total = $order->get_total();
			if ( $order_total < 0 ) {
				$order_total = 0;
			}
			$customer_id = get_current_user_id();
			if ( $customer_id > 0 ) {
				$walletamount = get_user_meta( $customer_id, 'mwb_wallet', true );
				if ( $order_total <= $walletamount ) {
					
					$walletamount -= $order_total;
					update_user_meta( $customer_id, 'mwb_wallet', abs( $walletamount ) );
					$transaction_type = 'Wallet debited through purchasing <a href="' . admin_url('post.php?post='.$order_id.'&action=edit') . '" >#' . $order_id . '</a>';
					$transaction_data = array(
						'user_id'          => $customer_id,
						'amount'           => $order_total,
						'payment_method'   => $payment_method,
						'transaction_type' => htmlentities( $transaction_type ),
						'order_id'         => $order_id,
						'note'             => '',
					);
					$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
					$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
				}

				
			}

			// Mark as on-hold (we're awaiting the payment).
			$order->update_status( 'processing', __( 'Awaiting Wallet payment', 'wallet-system-for-woocommerce' ) );

			// Reduce stock levels.
			$order->reduce_order_stock();

			// Remove cart.
			WC()->cart->empty_cart();

			// Return thankyou redirect.
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}
	}
}
add_action( 'plugins_loaded', 'mwb_wsfw_wallet_payment_gateway_init' );
