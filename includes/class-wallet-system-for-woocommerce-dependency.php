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
		$wallet_balance = intval( $wallet_balance );
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
				'transaction_type_1' => 'debit',
				'order_id'         => $order_id,
				'note'             => '',
			);
			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
			$balance   = $currency . ' ' . $amount;

			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user       = get_user_by( 'id', $user_id );
				$name       = $user->first_name . ' ' . $user->last_name;
				$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . ",\r\n";
				$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
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
						$user       = get_user_by( 'id', $user_id );
						$currency  = get_woocommerce_currency();
						$balance_mail = $balance;
						$user_name       = $user->first_name . ' ' . $user->last_name;
						$customer_email->trigger( $user_id, $user_name, $balance_mail, '' );
					}
				} else {

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
			}
			return true;
		} else {
			return false;
		}


	}
}

// Check if function exists.
if ( ! function_exists( 'wps_wsfw_credit_user_wallet_balance' ) ) {
	/**
	 * Update the user's wallet balance.
	 *
	 * @param int $user_id user id.
	 * @param int $amount amount.
	 * @param int $order_id order id.
	 * @return boolean
	 */
	function wps_wsfw_credit_user_wallet_balance( $user_id, $amount, $order_id = '' ) {
		$wallet_balance = get_user_meta( $user_id, 'wps_wallet', true );
		$wallet_balance = floatval( $wallet_balance );
		$wallet_balance += floatval( $amount );

		$update_wallet          = update_user_meta( $user_id, 'wps_wallet', $wallet_balance );
		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
		if ( $update_wallet ) {
			$payment_method   = esc_html__( 'Manually done', 'wallet-system-for-woocommerce' );
			$currency         = get_woocommerce_currency();
			$transaction_type = esc_html__( 'Wallet is credited', 'wallet-system-for-woocommerce' );
			if ( ! empty( $order_id ) ) {
				$order = wc_get_order( $order_id );
				if ( $order ) {
					$payment_method = $order->get_payment_method();
					if ( 'wps_wcb_wallet_payment_gateway' === $payment_method || 'wallet' === $payment_method ) {
						$payment_method = esc_html__( 'Wallet Payment', 'wallet-system-for-woocommerce' );
					}
					$currency         = $order->get_currency();
					$transaction_type = __( 'Wallet credited through purchasing ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
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
				'transaction_type_1' => 'credit',
				'order_id'         => $order_id,
				'note'             => '',
			);
			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
			$balance   = $currency . ' ' . $amount;

			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user       = get_user_by( 'id', $user_id );
				$name       = $user->first_name . ' ' . $user->last_name;
				$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . ",\r\n";
				$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
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
						$user       = get_user_by( 'id', $user_id );
						$currency  = get_woocommerce_currency();
						$balance_mail = $balance;
						$user_name       = $user->first_name . ' ' . $user->last_name;
						$customer_email->trigger( $user_id, $user_name, $balance_mail, '' );
					}
				} else {

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
			}
			return true;
		} else {
			return false;
		}


	}
}


if ( ! function_exists( 'wps_is_wallet_rechargeable_order' ) ) {

	/**
	 * Check if order contains rechargeable product
	 *
	 * @param WC_Order object $order is the order object.
	 * @return boolean
	 */
	function wps_is_wallet_rechargeable_order( $order ) {
		$wps_is_wallet_rechargeable_order = false;
		$wallet_recharge_id  = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		if ( $order instanceof WC_Order ) {
			foreach ( $order->get_items( 'line_item' ) as $item ) {
				$product_id = $item['product_id'];
				if ( $product_id == $wallet_recharge_id ) {
					$wps_is_wallet_rechargeable_order = true;
					break;
				}
			}
		}
		return apply_filters( 'wps_wallet_is_wallet_rechargeable_order', $wps_is_wallet_rechargeable_order, $order );
	}
}


if ( ! function_exists( 'get_order_partial_payment_amount' ) ) {
	/**
	 * Get total partial payment amount from an order.
	 *
	 * @param Int $order_id Is the order id.
	 * @return Number
	 */
	function get_order_partial_payment_amount( $order_id ) {
		$via_wallet = 0;
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$line_items_fee = $order->get_items( 'fee' );
			foreach ( $line_items_fee as $item_id => $item ) {
				if ( is_partial_payment_order_item( $item_id, $item ) ) {
					$via_wallet += $item->get_total( 'edit' ) + $item->get_total_tax( 'edit' );
				}
			}
		}
		return apply_filters( 'wps_wallet_order_partial_payment_amount', abs( $via_wallet ), $order_id );
	}
}

if ( ! function_exists( 'is_partial_payment_order_item' ) ) {
	/**
	 * Check if order item is partial payment instance.
	 *
	 * @param Int               $item_id Is the id of order item.
	 * @param WC_Order_Item_Fee $item Is the object of order item.
	 * @return boolean
	 */
	function is_partial_payment_order_item( $item_id, $item ) {
		if ( get_metadata( 'order_item', $item_id, '_legacy_fee_key', true ) && '_via_wallet_partial_payment' === get_metadata( 'order_item', $item_id, '_legacy_fee_key', true ) ) {
			return true;
		} else if ( 'via_wallet' === strtolower( str_replace( ' ', '_', $item->get_name( 'edit' ) ) ) ) {
			return true;
		}
		return false;
	}
}


if ( ! function_exists( 'wps_wallet_wc_price_args' ) ) {

	/**
	 * Wallet price args.
	 *
	 * @param string $user_id Is the current user id.
	 * @return mixed
	 */
	function wps_wallet_wc_price_args( $user_id = '' ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$args = apply_filters(
			'wps_wallet_wc_price_args',
			array(
				'ex_tax_label' => false,
				'currency' => '',
				'decimal_separator' => wc_get_price_decimal_separator(),
				'thousand_separator' => wc_get_price_thousand_separator(),
				'decimals' => wc_get_price_decimals(),
				'price_format' => get_woocommerce_price_format(),
			),
			$user_id
		);
		return $args;
	}
}





if ( ! function_exists( 'wps_wsfw_get_referral_link_wallet' ) ) {
	/**
	 * Referral code for wallet.
	 *
	 * @param [type] $user_id is the current user id.
	 * @return mixed
	 */
	function wps_wsfw_get_referral_link_wallet( $user_id ) {

		$get_referral        = get_user_meta( $user_id, 'wps_points_referral', true );
		$get_referral_invite = get_user_meta( $user_id, 'wps_points_referral_invite', true );
		if ( empty( $get_referral ) && empty( $get_referral_invite ) ) {
			$referral_key = '';

				$referral_key = wps_wsfw_create_referral_code_wallet();
				$referral_invite = 0;
				update_user_meta( $user_id, 'wps_points_referral', $referral_key );
				update_user_meta( $user_id, 'wps_points_referral_invite', $referral_invite );

		}
		$referral_link = get_user_meta( $user_id, 'wps_points_referral', true );
		return $referral_link;
	}
}


if ( ! function_exists( 'wps_wsfw_create_referral_code_wallet' ) ) {

	/**
	 * Get referral Code function.
	 *
	 * @return string
	 */
	function wps_wsfw_create_referral_code_wallet() {

		$length      = 10;
		$pkey        = '';
		$alphabets   = range( 'A', 'Z' );
		$numbers     = range( '0', '9' );
		$final_array = array_merge( $alphabets, $numbers );

		while ( $length-- ) {
			$key   = array_rand( $final_array );
			$pkey .= $final_array[ $key ];
		}
		return $pkey;
	}
}



