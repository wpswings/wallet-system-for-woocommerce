<?php
/**
 * POS wallet ledger functions: atomic balance read/deduct/refund for POS sales.
 *
 * @link       https://wpswings.com/
 * @since      2.8.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All balance mutations go through with_wallet_lock() so two POS registers
 * (or a register and an online purchase) can't race the same user's wallet.
 * Ledger rows are written via the existing insert_transaction_data_in_table()
 * method used by every other wallet flow, tagged with source = 'pos'.
 */
class Wallet_System_For_Woocommerce_Pos_Wallet {

	const LOCK_TIMEOUT = 10;

	/**
	 * Runs $callback while holding a MySQL named lock scoped to this user's
	 * wallet.
	 *
	 * @param int      $user_id  WP user id.
	 * @param callable $callback Receives no args, returns array|WP_Error.
	 * @return mixed|WP_Error
	 */
	public static function with_wallet_lock( $user_id, $callback ) {
		global $wpdb;
		$lock_name = 'wsfw_wallet_lock_' . absint( $user_id );

		$acquired = $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $lock_name, self::LOCK_TIMEOUT ) );

		if ( '1' !== $acquired ) {
			return new WP_Error( 'wsfw_pos_wallet_busy', __( 'Wallet is busy, please try again.', 'wallet-system-for-woocommerce' ) );
		}

		try {
			return call_user_func( $callback );
		} finally {
			$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock_name ) );
		}
	}

	/**
	 * Reads the current wallet balance.
	 *
	 * @param int $user_id WP user id.
	 * @return float
	 */
	public static function get_balance( $user_id ) {
		return floatval( get_user_meta( $user_id, 'wps_wallet', true ) );
	}

	/**
	 * Deducts an amount from a user's wallet for a POS sale.
	 *
	 * @param int        $user_id             WP user id.
	 * @param float      $amount              Amount to deduct.
	 * @param int|string $order_id            WC order id, if already created.
	 * @param int|null   $register_session_id POS register session id.
	 * @return array|WP_Error {balance, transaction_id}
	 */
	public static function deduct( $user_id, $amount, $order_id = '', $register_session_id = null ) {
		$amount = floatval( $amount );

		if ( $amount <= 0 ) {
			return new WP_Error( 'wsfw_pos_invalid_amount', __( 'Deduction amount must be greater than zero.', 'wallet-system-for-woocommerce' ) );
		}

		return self::with_wallet_lock(
			$user_id,
			function () use ( $user_id, $amount, $order_id, $register_session_id ) {
				$balance = self::get_balance( $user_id );

				if ( $balance < $amount ) {
					return new WP_Error( 'wsfw_pos_insufficient_balance', __( 'Insufficient wallet balance.', 'wallet-system-for-woocommerce' ) );
				}

				$new_balance = $balance - $amount;
				update_user_meta( $user_id, 'wps_wallet', $new_balance );

				$currency = get_woocommerce_currency();
				$order    = ! empty( $order_id ) ? wc_get_order( $order_id ) : false;
				if ( $order ) {
					$currency = $order->get_currency();
				}

				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$transaction_id         = $wallet_payment_gateway->insert_transaction_data_in_table(
					array(
						'user_id'             => $user_id,
						'amount'              => $amount,
						'currency'            => $currency,
						'payment_method'      => __( 'Wallet Payment', 'wallet-system-for-woocommerce' ),
						'transaction_type'    => __( 'Wallet debited through POS purchase', 'wallet-system-for-woocommerce' ),
						'transaction_type_1'  => 'debit',
						'order_id'            => $order_id,
						'note'                => '',
						'source'              => 'pos',
						'register_session_id' => $register_session_id,
					)
				);

				do_action( 'wallet_balance_updated', $user_id, $new_balance, 'debit', 'pos' );

				return array(
					'balance'        => $new_balance,
					'transaction_id' => $transaction_id,
				);
			}
		);
	}

	/**
	 * Refunds an amount back to a user's wallet for a POS sale, capped at
	 * the amount originally paid via wallet on that order (minus anything
	 * already refunded to wallet for it), unless a site explicitly opts in
	 * to exceeding that via the wsfw_pos_allow_refund_exceeding_wallet_paid
	 * filter.
	 *
	 * @param int        $user_id             WP user id.
	 * @param int|string $order_id            WC order id being refunded.
	 * @param float      $amount              Amount to refund.
	 * @param int|null   $register_session_id POS register session id.
	 * @return array|WP_Error {balance, transaction_id}
	 */
	public static function refund( $user_id, $order_id, $amount, $register_session_id = null ) {
		$amount = floatval( $amount );

		if ( $amount <= 0 ) {
			return new WP_Error( 'wsfw_pos_invalid_amount', __( 'Refund amount must be greater than zero.', 'wallet-system-for-woocommerce' ) );
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return new WP_Error( 'wsfw_pos_invalid_order', __( 'Order not found.', 'wallet-system-for-woocommerce' ) );
		}

		$refund_limit = self::get_wallet_paid_amount( $order_id ) - self::get_wallet_refunded_amount( $order );

		if ( $amount > $refund_limit && ! apply_filters( 'wsfw_pos_allow_refund_exceeding_wallet_paid', false, $user_id, $order_id ) ) {
			return new WP_Error( 'wsfw_pos_refund_exceeds_wallet_paid', __( 'Refund amount exceeds the wallet-paid portion of this order.', 'wallet-system-for-woocommerce' ) );
		}

		return self::with_wallet_lock(
			$user_id,
			function () use ( $user_id, $order, $order_id, $amount, $register_session_id ) {
				$new_balance = self::get_balance( $user_id ) + $amount;
				update_user_meta( $user_id, 'wps_wallet', $new_balance );

				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$transaction_id         = $wallet_payment_gateway->insert_transaction_data_in_table(
					array(
						'user_id'             => $user_id,
						'amount'              => $amount,
						'currency'            => $order->get_currency(),
						'payment_method'      => __( 'Wallet Refund', 'wallet-system-for-woocommerce' ),
						'transaction_type'    => __( 'Wallet credited through POS refund', 'wallet-system-for-woocommerce' ),
						'transaction_type_1'  => 'credit',
						'order_id'            => $order_id,
						'note'                => '',
						'source'              => 'pos',
						'register_session_id' => $register_session_id,
					)
				);

				$already_refunded = (float) $order->get_meta( '_wsfw_pos_wallet_refunded_total', true );
				$order->update_meta_data( '_wsfw_pos_wallet_refunded_total', $already_refunded + $amount );
				$order->save();

				do_action( 'wallet_balance_updated', $user_id, $new_balance, 'credit', 'pos' );

				return array(
					'balance'        => $new_balance,
					'transaction_id' => $transaction_id,
				);
			}
		);
	}

	/**
	 * Sums wallet debit entries recorded against an order, regardless of
	 * channel — this is "how much wallet money was actually taken" for it.
	 *
	 * @param int|string $order_id WC order id.
	 * @return float
	 */
	private static function get_wallet_paid_amount( $order_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'wps_wsfw_wallet_transaction';

		return (float) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COALESCE(SUM(amount), 0) FROM {$table} WHERE transaction_id = %s AND transaction_type_1 = 'debit'",
				$order_id
			)
		);
	}

	/**
	 * Reads how much has already been refunded to wallet for an order.
	 * Tracked as order meta (not by scanning the transaction table) since
	 * transaction_type_1 = 'credit' is shared with future POS cashback
	 * entries and can't be used to identify refunds specifically.
	 *
	 * @param WC_Order $order Order object.
	 * @return float
	 */
	private static function get_wallet_refunded_amount( $order ) {
		return (float) $order->get_meta( '_wsfw_pos_wallet_refunded_total', true );
	}
}

if ( ! function_exists( 'wallet_get_balance' ) ) {
	/**
	 * Returns a user's current wallet balance.
	 *
	 * @param int $user_id WP user id.
	 * @return float
	 */
	function wallet_get_balance( $user_id ) {
		return Wallet_System_For_Woocommerce_Pos_Wallet::get_balance( $user_id );
	}
}

if ( ! function_exists( 'wallet_pos_deduct' ) ) {
	/**
	 * Deducts an amount from a user's wallet for a POS sale.
	 *
	 * @param int        $user_id             WP user id.
	 * @param float      $amount              Amount to deduct.
	 * @param int|string $order_id            WC order id.
	 * @param int|null   $register_session_id POS register session id.
	 * @return array|WP_Error
	 */
	function wallet_pos_deduct( $user_id, $amount, $order_id = '', $register_session_id = null ) {
		return Wallet_System_For_Woocommerce_Pos_Wallet::deduct( $user_id, $amount, $order_id, $register_session_id );
	}
}

if ( ! function_exists( 'wallet_pos_refund' ) ) {
	/**
	 * Refunds an amount to a user's wallet for a POS refund.
	 *
	 * @param int        $user_id             WP user id.
	 * @param int|string $order_id            WC order id being refunded.
	 * @param float      $amount              Amount to refund.
	 * @param int|null   $register_session_id POS register session id.
	 * @return array|WP_Error
	 */
	function wallet_pos_refund( $user_id, $order_id, $amount, $register_session_id = null ) {
		return Wallet_System_For_Woocommerce_Pos_Wallet::refund( $user_id, $order_id, $amount, $register_session_id );
	}
}
