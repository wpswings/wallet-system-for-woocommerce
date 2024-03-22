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


}
