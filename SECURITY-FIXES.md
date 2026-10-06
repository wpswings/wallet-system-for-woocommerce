# Security fixes — wallet-system-for-woocommerce

Applied to v2.8.0 of this install (version number left unchanged). Covers three
confirmed findings reported against 2.7.10 (refs 48196, 45347, 49655). All three
share one root cause: a nonce created with `wp_create_nonce()` / checked with
`wp_verify_nonce()` **without an action argument**, combined with **no capability
check** and **no check that the account named in the request belongs to the
requester**. Use this doc to port the same fixes into another branch/version of
the plugin — match the surrounding code first, since line numbers will differ.

---

## 1. Store-wide transaction export (ref 48196)

**File:** `admin/class-wallet-system-for-woocommerce-admin.php`
**Method:** `wps_wsfw_download_pdf_file_callback()`

### 1a. Bind the nonce and add a capability check

Before:
```php
$nonce = ( isset( $_POST['updatenoncewallet_pdf_dwnload'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_pdf_dwnload'] ) ) : '';
if ( wp_verify_nonce( $nonce ) ) {

	if ( isset( $_POST['wps_wsfw_export_pdf'] ) ) {
```

After:
```php
$nonce = ( isset( $_POST['updatenoncewallet_pdf_dwnload'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_pdf_dwnload'] ) ) : '';
if ( wp_verify_nonce( $nonce, 'wps_wsfw_export_transactions' ) && $this->wps_wsfw_current_user_can_manage_wallet() ) {

	if ( isset( $_POST['wps_wsfw_export_pdf'] ) ) {
```

`wps_wsfw_current_user_can_manage_wallet()` already exists on this class
(`current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' )`)
— it's the same guard used by the (already-correct) `export_users_wallet()`
method above this one. If the target version doesn't have this helper, add it
or inline the `current_user_can()` check.

### 1b. Stop writing the CSV into the web root

The CSV branch wrote to a **relative path**, which resolves against the entry
script's directory (`wp-admin/`) and is publicly downloadable with no auth at
all, and persists across requests.

Before:
```php
if ( ! empty( $data ) ) {
		$csv_data = $data['csv_data'];

		// Create a file pointer.
		$file = fopen( 'Transaction_Data.csv', 'w' );

		// Write data to the CSV file.
		foreach ( $csv_data as $row ) {
			$row_data = array();
			foreach ( $row as $key => $value ) {

				array_push( $row_data, strip_tags( $value ) );
			}
			fputcsv( $file, $row_data );

		}
		// Close the file pointer.
		fclose( $file );
		// Output a download link for the generated CSV file.
		echo '<a href="Transaction_Data.csv" id="transaction_data_csv_file" style="display:none"  download>Download Transaction CSV Data </a>';
		?>
			<script>
			   
				const myAnchor = document.getElementById('transaction_data_csv_file');
				myAnchor.click();
			   
			</script>
		<?php
	}
```

After:
```php
if ( ! empty( $data ) ) {
		$csv_data = $data['csv_data'];

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="Transaction_Data.csv"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );

		// Stream the CSV directly to the browser instead of writing it to a web-accessible file.
		$output_stream = fopen( 'php://output', 'w' );

		foreach ( $csv_data as $row ) {
			$row_data = array();
			foreach ( $row as $key => $value ) {

				array_push( $row_data, strip_tags( $value ) );
			}
			fputcsv( $output_stream, $row_data );

		}
		fclose( $output_stream );
		exit;
	}
```

This mirrors the PDF branch immediately above it in the same function, which
already streamed its output correctly.

**Important — cleanup on every install you patch:** if the CSV export was ever
used (by an admin or an attacker) before this fix, check for and delete a
leftover `Transaction_Data.csv` in the site's `wp-admin/` directory. It is
world-readable with no authentication.

### 1c. Bind the matching nonce action where it's printed

**File:** `admin/partials/class-wallet-transaction-list-table.php`

Before:
```php
<input type="hidden" id="updatenoncewallet_pdf_dwnload" name="updatenoncewallet_pdf_dwnload" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
```

After:
```php
<input type="hidden" id="updatenoncewallet_pdf_dwnload" name="updatenoncewallet_pdf_dwnload" value="<?php echo esc_attr( wp_create_nonce( 'wps_wsfw_export_transactions' ) ); ?>" />
```

### 1d. Fix the withdrawal-approval sufficiency check to include the fee

Not part of the export finding, but flagged in the same report (see §3 below)
and lives in the same admin file — fix both together.

**Method:** `change_wallet_withdrawan_status()`

Before:
```php
$walletamount = get_user_meta( $user_id, 'wps_wallet', true );
$withdrawal_amount = get_post_meta( $withdrawal_id, 'wps_wallet_withdrawal_amount', true );
$updated_status     = ( isset( $_POST['status'] ) ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
if ( 'approved' === $updated_status ) {
	if ( $walletamount < $withdrawal_amount ) {
		$wps_wsfw_error_text = esc_html__( 'Wallet Amount is not sufficient for widthdrawal.', 'wallet-system-for-woocommerce' );
		$message             = array(
			'msg'     => $wps_wsfw_error_text,
			'msgType' => 'error',
		);
		$update = false;
	}
}
```

After:
```php
$walletamount = get_user_meta( $user_id, 'wps_wallet', true );
$withdrawal_amount = get_post_meta( $withdrawal_id, 'wps_wallet_withdrawal_amount', true );
$updated_status     = ( isset( $_POST['status'] ) ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
if ( 'approved' === $updated_status ) {
	$sufficiency_is_manual_fees = true;
	$sufficiency_withdrawal_option = get_option( 'wps_wsfwp_wallet_withdrawal_paypal_enable' );
	if ( 'on' == $sufficiency_withdrawal_option ) {
		$sufficiency_withdrawal_option = get_post_meta( $withdrawal_id, 'wps_wallet_withdrawal_option', true );
		if ( 'manual' != $sufficiency_withdrawal_option ) {
			$sufficiency_is_manual_fees = false;
		}
	}
	$sufficiency_fee_amount = $sufficiency_is_manual_fees ? get_post_meta( $withdrawal_id, 'wps_wsfwp_wallet_withdrawal_fee_amount', true ) : 0;
	$sufficiency_fee_amount = ! empty( $sufficiency_fee_amount ) ? $sufficiency_fee_amount : 0;

	if ( $walletamount < ( $withdrawal_amount + $sufficiency_fee_amount ) ) {
		$wps_wsfw_error_text = esc_html__( 'Wallet Amount is not sufficient for widthdrawal.', 'wallet-system-for-woocommerce' );
		$message             = array(
			'msg'     => $wps_wsfw_error_text,
			'msgType' => 'error',
		);
		$update = false;
	}
}
```

This mirrors the fee-resolution logic that already exists a little further down
in the same method (the `$is_manual_fees` block used when actually debiting the
wallet) — the check now uses the same rule the debit itself uses.

---

## 2. Wallet balance transfer out of another user's account (ref 45347)

Duplicated in two files with identical logic — apply the same edit to both:
- `public/partials/wallet-system-for-woocommerce-public-display.php`
- `public/partials/wallet-system-for-woocommerce-shortcode.php`

### 2a. Bind the shared nonce to a named action

Before:
```php
$nonce = ( isset( $_POST['wps_verifynonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wps_verifynonce'] ) ) : '';
if ( wp_verify_nonce( $nonce ) ) {
```

After:
```php
$nonce = ( isset( $_POST['wps_verifynonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wps_verifynonce'] ) ) : '';
if ( wp_verify_nonce( $nonce, 'wps_wsfw_wallet_account_action' ) ) {
```

Then find every place in the same two files that prints this nonce field
(there are 3 per file: recharge form, transfer form, withdrawal form) and
change:
```php
value="<?php echo esc_attr( wp_create_nonce() ); ?>"
```
to:
```php
value="<?php echo esc_attr( wp_create_nonce( 'wps_wsfw_wallet_account_action' ) ); ?>"
```
(id/name stays `wps_verifynonce` in both files — only the nonce action changes.)

### 2b. Add the ownership check to the transfer handler

Inside the `if ( isset( $_POST['wps_proceed_transfer'] ) ... )` block, the
source wallet's owner (`current_user_id`) is taken straight from POST and never
compared against the logged-in user. The fix adds ownership as the **first**
condition in the existing validation `elseif` chain, so it short-circuits
before the balance-vs-amount comparison — this also closes an oracle: an
attacker could otherwise binary-search a victim's exact balance by watching
which of the two error messages came back, without ownership blocking the
comparison from running.

Before:
```php
if ( empty( $_POST['wps_wallet_transfer_amount'] ) ) {
	show_message_on_form_submit( esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	$update = false;
} elseif ( $wallet_bal < $wallet_transfer_amount ) {
	show_message_on_form_submit( esc_html__( 'Please enter amount less than or equal to wallet balance', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	$update = false;
} elseif ( $another_user_email == $wps_current_user_email ) {
	show_message_on_form_submit( esc_html__( 'You cannot transfer amount to yourself.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	$update = false;
}
```

After:
```php
if ( (int) $user_id !== get_current_user_id() ) {
	show_message_on_form_submit( esc_html__( 'You are not allowed to transfer funds from this wallet.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	$update = false;
} elseif ( empty( $_POST['wps_wallet_transfer_amount'] ) ) {
	show_message_on_form_submit( esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	$update = false;
} elseif ( $wallet_bal < $wallet_transfer_amount ) {
	show_message_on_form_submit( esc_html__( 'Please enter amount less than or equal to wallet balance', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	$update = false;
} elseif ( $another_user_email == $wps_current_user_email ) {
	show_message_on_form_submit( esc_html__( 'You cannot transfer amount to yourself.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	$update = false;
}
```

`$user_id` at this point in the handler is the value read from
`$_POST['current_user_id']` a few lines above (the source wallet). This check
applies identically whether the recipient was chosen by email or by wallet ID
— both paths funnel into this same validation chain, so one guard covers both.

---

## 3. Forged withdrawal request against another user's wallet (ref 49655)

Same two files as §2, same duplication — apply to both.

Inside the `if ( isset( $_POST['wps_withdrawal_request'] ) ... )` block, the
target wallet (`wallet_user_id`) is taken from POST with no ownership check,
**and** every submitted `$_POST` field is written back as post meta on the
withdrawal request (including a `wps_wsfwp_wallet_withdrawal_fee_amount` field
that the legitimate form never submits — it's supposed to be computed
server-side from `get_option( 'wps_wsfwp_wallet_withdrawal_fee_amount' )` in
the Pro plugin, not taken from the client).

Before (`public/partials/wallet-system-for-woocommerce-public-display.php`
shown; `shortcode.php` is the same shape, only the enqueued script handle
differs — `wps-public-shortcode-dis` vs `wps-public-shortcode`):
```php
if ( isset( $_POST['wps_withdrawal_request'] ) && ! empty( $_POST['wps_withdrawal_request'] ) ) {
	unset( $_POST['wps_withdrawal_request'] );


	if ( ! empty( $_POST['wallet_user_id'] ) ) {
		$user_id  = sanitize_text_field( wp_unslash( $_POST['wallet_user_id'] ) );
		$user     = get_user_by( 'id', $user_id );
		$username = $user->user_login;

	}

			$args          = array(
				'post_title'  => $username,
				'post_type'   => 'wallet_withdrawal',
				'post_status' => 'publish',
			);
			$withdrawal_id = wp_insert_post( $args );
			if ( ! empty( $withdrawal_id ) ) {
				wp_update_post(
					array(
						'ID'          => $withdrawal_id,
						'post_status' => 'pending1',
					)
				);
				foreach ( $_POST as $key => $value ) {
					if ( ! empty( $value ) ) {
						$value = sanitize_text_field( $value );
						if ( 'wps_wallet_withdrawal_amount' === $key ) {
							$withdrawal_bal = apply_filters( 'wps_wsfw_convert_to_base_price', $value );
							update_post_meta( $withdrawal_id, $key, $withdrawal_bal );
						} else {
							update_post_meta( $withdrawal_id, $key, $value );
						}
					}
				}
				update_user_meta( $user_id, 'disable_further_withdrawal_request', true );

				wp_register_script( 'wps-public-shortcode-dis', false, array(), '1.0.0', false );
				wp_enqueue_script( 'wps-public-shortcode-dis' );
				wp_add_inline_script( 'wps-public-shortcode-dis', 'window.location.href = "' . $current_url . '"' );
			}
}
```

After:
```php
if ( isset( $_POST['wps_withdrawal_request'] ) && ! empty( $_POST['wps_withdrawal_request'] ) ) {
	unset( $_POST['wps_withdrawal_request'] );

	$user_id = ! empty( $_POST['wallet_user_id'] ) ? absint( wp_unslash( $_POST['wallet_user_id'] ) ) : 0;

	if ( $user_id !== get_current_user_id() ) {
		show_message_on_form_submit( esc_html__( 'You are not allowed to request a withdrawal for this wallet.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	} else {
		$user     = get_user_by( 'id', $user_id );
		$username = $user ? $user->user_login : '';

		$args          = array(
			'post_title'  => $username,
			'post_type'   => 'wallet_withdrawal',
			'post_status' => 'publish',
		);
		$withdrawal_id = wp_insert_post( $args );
		if ( ! empty( $withdrawal_id ) ) {
			wp_update_post(
				array(
					'ID'          => $withdrawal_id,
					'post_status' => 'pending1',
				)
			);

			$withdrawal_meta_allowlist = array(
				'wallet_user_id',
				'wps_wallet_withdrawal_amount',
				'wps_wallet_withdrawal_option',
				'wps_wallet_withdrawal_paypal_user_email',
			);

			foreach ( $withdrawal_meta_allowlist as $key ) {
				if ( ! empty( $_POST[ $key ] ) ) {
					$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
					if ( 'wps_wallet_withdrawal_amount' === $key ) {
						$withdrawal_bal = apply_filters( 'wps_wsfw_convert_to_base_price', $value );
						update_post_meta( $withdrawal_id, $key, $withdrawal_bal );
					} else {
						update_post_meta( $withdrawal_id, $key, $value );
					}
				}
			}
			update_user_meta( $user_id, 'disable_further_withdrawal_request', true );

			wp_register_script( 'wps-public-shortcode-dis', false, array(), '1.0.0', false );
			wp_enqueue_script( 'wps-public-shortcode-dis' );
			wp_add_inline_script( 'wps-public-shortcode-dis', 'window.location.href = "' . $current_url . '"' );
		}
	}
}
```

(In `shortcode.php`, keep the script handle as `wps-public-shortcode` — only
copy the ownership check and the allowlist, not the handle name.)

> **Before reusing the allowlist elsewhere:** it was built from the fields the
> free plugin's own `wallet-system-for-woocommerce-wallet-withdrawal.php` form
> actually submits (`wallet_user_id`, `wps_wallet_withdrawal_amount`,
> `wps_wallet_withdrawal_option`, `wps_wallet_withdrawal_paypal_user_email`). If
> the target version's withdrawal form has additional legitimate fields (e.g.
> bank transfer details added by a later version or the Pro plugin), add those
> field names to the allowlist too — but never add
> `wps_wsfwp_wallet_withdrawal_fee_amount`; that one must stay server-computed.

---

## Out of scope (noted by the reporter, not fixed here)

The report explicitly says the recharge, coupon, and KYC blocks in these same
two public partials show the same shape (POST-derived user id, blind
`foreach ( $_POST ...)` meta writes) but were **not reproduced/confirmed**.
They were left untouched in this pass — review them separately before treating
them as fixed.
