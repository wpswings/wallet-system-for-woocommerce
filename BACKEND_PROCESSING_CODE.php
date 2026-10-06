<?php
/**
 * Backend Processing Code for Bonus and Expiry
 *
 * This code should be added to the wallet update processing in:
 * admin/partials/class-wallet-user-table.php
 *
 * Add this code inside the "if ( 'credit' === $wallet_action )" block
 * after line 780 (after update_user_meta) and before the email notification code
 */

// Handle bonus amount and expiry if provided
$contains_bonus = isset( $_POST['wps_wallet_contains_bonus'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_contains_bonus'] ) ) : '';
$bonus_amount = isset( $_POST['wps_wallet-bonus-amount'] ) ? floatval( sanitize_text_field( wp_unslash( $_POST['wps_wallet-bonus-amount'] ) ) ) : 0;
$expiry_period = isset( $_POST['wps_wallet_expiry_period'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_expiry_period'] ) ) : 'none';
$custom_expiry_days = isset( $_POST['wps_wallet_custom_expiry_days'] ) ? absint( wp_unslash( $_POST['wps_wallet_custom_expiry_days'] ) ) : 0;

// Calculate expiry date if applicable
$expiry_date = null;
if ( 'none' !== $expiry_period ) {
	if ( function_exists( 'wps_wsfwrpa_calculate_expiry_date' ) ) {
		// Use rechargeable addon function if available
		$expiry_date = wps_wsfwrpa_calculate_expiry_date( $expiry_period, $custom_expiry_days );
	} else {
		// Fallback expiry calculation
		switch ( $expiry_period ) {
			case '1_month':
				$expiry_date = gmdate( 'Y-m-d H:i:s', strtotime( '+1 month' ) );
				break;
			case '3_months':
				$expiry_date = gmdate( 'Y-m-d H:i:s', strtotime( '+3 months' ) );
				break;
			case '6_months':
				$expiry_date = gmdate( 'Y-m-d H:i:s', strtotime( '+6 months' ) );
				break;
			case 'custom':
				if ( $custom_expiry_days > 0 ) {
					$expiry_date = gmdate( 'Y-m-d H:i:s', strtotime( "+{$custom_expiry_days} days" ) );
				}
				break;
		}
	}
}

// Store expiry and bonus info if rechargeable addon is active
if ( function_exists( 'wps_wsfwrpa_get_ledger_table' ) && $expiry_date ) {
	global $wpdb;

	// If bonus is enabled and amount is provided, that's what gets deducted
	// Otherwise, the full amount is deducted on expiry
	$deduct_amount = ( 'yes' === $contains_bonus && $bonus_amount > 0 ) ? $bonus_amount : floatval( $updated_amount );

	$wpdb->insert(
		wps_wsfwrpa_get_ledger_table(),
		array(
			'user_id'       => $user_id,
			'order_id'      => 0, // No order ID for manual credits
			'order_item_id' => 0,
			'product_id'    => 0,
			'amount'        => floatval( $updated_amount ),
			'deduct_amount' => $deduct_amount,
			'currency'      => get_woocommerce_currency(),
			'credited_date' => gmdate( 'Y-m-d H:i:s' ),
			'expiry_date'   => $expiry_date,
			'status'        => 'active',
		),
		array( '%d', '%d', '%d', '%d', '%f', '%f', '%s', '%s', '%s', '%s' )
	);

	// Add expiry info to transaction type for better tracking
	if ( $expiry_date ) {
		$expiry_formatted = date_i18n( get_option( 'date_format' ), strtotime( $expiry_date ) );
		if ( isset( $transaction_type ) ) {
			$transaction_type .= ' (Expires: ' . $expiry_formatted . ')';
		}
	}
}

/**
 * INSTALLATION INSTRUCTIONS:
 *
 * 1. Open: admin/partials/class-wallet-user-table.php
 * 2. Find line ~777: if ( 'credit' === $wallet_action ) {
 * 3. Find line ~780: $updated_wallet   = update_user_meta( $user_id, 'wps_wallet', $wallet );
 * 4. Find line ~781-785: the transaction_type setting code
 * 5. After line 785 (after the transaction_type else block)
 * 6. Before line 786 ($balance   = $currency . ' ' . $updated_amount;)
 * 7. Insert the above code (starting from "// Handle bonus amount...")
 *
 * The code should be inserted between the transaction_type setting and the $balance variable.
 */
?>
