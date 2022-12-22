<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/public/partials
 */

if ( ! function_exists( 'show_message_on_form_submit' ) ) {
	/**
	 * Show message on form submit
	 *
	 * @param string $wpg_message message to be shown on form submission.
	 * @param string $type error type.
	 * @return void
	 */
	function show_message_on_form_submit( $wpg_message, $type = 'error' ) {
		$wpg_notice = '<div class="woocommerce"><p class="' . esc_attr( $type ) . '">' . $wpg_message . '</p>	</div>';
		echo wp_kses_post( $wpg_notice );
	}
}
global $wp;
$logged_in_user = wp_get_current_user();
if ( ! empty( $logged_in_user ) ) {
	$current_user_email = $logged_in_user->user_email ? $logged_in_user->user_email : '';
} else {
	$current_user_email = '';
}
$current_currency = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );
$http_host        = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
$request_url      = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
$current_url      = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $http_host . $request_url;
if ( isset( $_POST['wps_recharge_wallet'] ) && ! empty( $_POST['wps_recharge_wallet'] ) ) {
	$nonce = ( isset( $_POST['verifynonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['verifynonce'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		unset( $_POST['wps_recharge_wallet'] );

		if ( empty( $_POST['wps_wallet_recharge_amount'] ) ) {
			show_message_on_form_submit( esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			$recharge_amount = sanitize_text_field( wp_unslash( $_POST['wps_wallet_recharge_amount'] ) );
			$recharge_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $recharge_amount );
			if ( ! empty( $_POST['user_id'] ) ) {
				$user_id = sanitize_text_field( wp_unslash( $_POST['user_id'] ) );

			}
			$product_id = ( isset( $_POST['product_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
			WC()->session->set(
				'wallet_recharge',
				array(
					'userid'         => $user_id,
					'rechargeamount' => $recharge_amount,
					'productid'      => $product_id,
				)
			);
			WC()->session->set( 'recharge_amount', $recharge_amount );
			echo '<script>window.location.href = "' . esc_url( wc_get_cart_url() ) . '";</script>';
		}
	} else {
		show_message_on_form_submit( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
	}
}
if ( isset( $_POST['wps_proceed_transfer'] ) && ! empty( $_POST['wps_proceed_transfer'] ) ) {
	unset( $_POST['wps_proceed_transfer'] );
	$update = true;
	// check whether $_POST key 'current_user_id' is empty or not.
	if ( ! empty( $_POST['current_user_id'] ) ) {
		$user_id = sanitize_text_field( wp_unslash( $_POST['current_user_id'] ) );
	}

	$wallet_bal             = get_user_meta( $user_id, 'wps_wallet', true );
	$wallet_bal             = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
	$wps_current_user_email = ! empty( $_POST['wps_current_user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_current_user_email'] ) ) : '';
	$another_user_email     = ! empty( $_POST['wps_wallet_transfer_user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_transfer_user_email'] ) ) : '';
	$transfer_note          = ! empty( $_POST['wps_wallet_transfer_note'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_transfer_note'] ) ) : '';
	$user                   = get_user_by( 'email', $another_user_email );
	$transfer_amount        = ! empty( $_POST['wps_wallet_transfer_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_transfer_amount'] ) ) : 0;
	$wallet_transfer_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $transfer_amount );
	if ( $user ) {
		$another_user_id = $user->ID;
	} else {
		$invitation_link = apply_filters( 'wsfw_add_invitation_link_message', '' );
		if ( ! empty( $invitation_link ) ) {
			global $wp_session;
			$wp_session['wps_wallet_transfer_user_email'] = $another_user_email;
			$wp_session['wps_wallet_transfer_amount']     = $wallet_transfer_amount;
		}
		show_message_on_form_submit( 'Email Id does not exist. ' . $invitation_link, 'woocommerce-error' );
		$update = false;
	}
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
	if ( $update ) {
		$user_wallet_bal  = get_user_meta( $another_user_id, 'wps_wallet', true );
		$user_wallet_bal  = ( ! empty( $user_wallet_bal ) ) ? $user_wallet_bal : 0;
		$user_wallet_bal += $wallet_transfer_amount;
		$returnid         = update_user_meta( $another_user_id, 'wps_wallet', $user_wallet_bal );

		if ( $returnid ) {
			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
			$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
			// first user.
			$user1 = get_user_by( 'id', $another_user_id );
			$name1 = $user1->first_name . ' ' . $user1->last_name;

			$user2 = get_user_by( 'id', $user_id );
			$name2 = $user2->first_name . ' ' . $user2->last_name;
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {

				$mail_text1  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name1 ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
				$mail_text1 .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $transfer_amount, array( 'currency' => $current_currency ) ) . __( ' through wallet transfer by ', 'wallet-system-for-woocommerce' ) . $name2;
				$to1         = $user1->user_email;
				$from        = get_option( 'admin_email' );
				$subject     = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
				$headers1    = 'MIME-Version: 1.0' . "\r\n";
				$headers1   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
				$headers1   .= 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $to1 . "\r\n";

				$wallet_payment_gateway->send_mail_on_wallet_updation( $to1, $subject, $mail_text1, $headers1 );

			}

			$transaction_type     = __( 'Wallet credited by user ', 'wallet-system-for-woocommerce' ) . $user2->user_email . __( ' to user ', 'wallet-system-for-woocommerce' ) . $user1->user_email;
			$wallet_transfer_data = array(
				'user_id'          => $another_user_id,
				'amount'           => $transfer_amount,
				'currency'         => $current_currency,
				'payment_method'   => __( 'Wallet Transfer', 'wallet-system-for-woocommerce' ),
				'transaction_type' => $transaction_type,
				'order_id'         => '',
				'note'             => $transfer_note,

			);

			$wallet_payment_gateway->insert_transaction_data_in_table( $wallet_transfer_data );

			$wallet_bal -= $wallet_transfer_amount;
			$update_user = update_user_meta( $user_id, 'wps_wallet', abs( $wallet_bal ) );
			if ( $update_user ) {

				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
					$mail_text2  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name2 ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
					$mail_text2 .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . wc_price( $transfer_amount, array( 'currency' => $current_currency ) ) . __( ' through wallet transfer to ', 'wallet-system-for-woocommerce' ) . $name1;
					$to2         = $user2->user_email;
					$headers2    = 'MIME-Version: 1.0' . "\r\n";
					$headers2   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
					$headers2   .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to2 . "\r\n";

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to2, $subject, $mail_text2, $headers2 );
				}
				$transaction_type = __( 'Wallet debited from user ', 'wallet-system-for-woocommerce' ) . $user2->user_email . __( ' wallet, transferred to user ', 'wallet-system-for-woocommerce' ) . $user1->user_email;
				$transaction_data = array(
					'user_id'          => $user_id,
					'amount'           => $transfer_amount,
					'currency'         => $current_currency,
					'payment_method'   => __( 'Wallet Transfer', 'wallet-system-for-woocommerce' ),
					'transaction_type' => $transaction_type,
					'order_id'         => '',
					'note'             => $transfer_note,

				);

				$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
				show_message_on_form_submit( esc_html__( 'Amount is transferred successfully', 'wallet-system-for-woocommerce' ), 'woocommerce-message' );

			} else {
				show_message_on_form_submit( esc_html__( 'Amount is not transferred', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
			}
		} else {
			show_message_on_form_submit( esc_html__( 'No user found.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		}
	}
}

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
		wp_register_script( 'wps-public-shortcode', false, array(), '1.0.0', false );
		wp_enqueue_script( 'wps-public-shortcode' );
		wp_add_inline_script( 'wps-public-shortcode', 'window.location.href = "' . $current_url . '"' );
	}
}


if ( isset( $_POST['wps_coupon_wallet'] ) && ! empty( $_POST['wps_coupon_wallet'] ) ) {
	unset( $_POST['wps_coupon_wallet'] );
	if ( ! empty( $_POST['user_id'] ) ) {
		$user_id  = sanitize_text_field( wp_unslash( $_POST['user_id'] ) );
		$user     = get_user_by( 'id', $user_id );
		$username = $user->user_login;
		$wps_wsfw_coupon_code = ! empty( $_POST['wps_wsfw_coupon_code'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wsfw_coupon_code'] ) ) : '';
		apply_filters( 'wps_wsfw_wallet_coupon_before_saving', $wps_wsfw_coupon_code );
	}
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$page_id = get_the_ID();
if ( function_exists( 'is_shop' ) ) {
	if ( is_shop() ) {
		$page_id = wc_get_page_id( 'shop' );
	}
}
$page_url = get_permalink( $page_id );

$main_url        = wc_get_endpoint_url( 'wps-wallet' );
$topup_url       = add_query_arg( 'wps-wallet', 'wallet-topup', $page_url );
$wallet_url      = add_query_arg( 'wps-wallet', 'wallet-transfer', $page_url );
$withdrawal_url  = add_query_arg( 'wps-wallet', 'wallet-withdrawal', $page_url );
$transaction_url = add_query_arg( 'wps-wallet', 'wallet-transactions', $page_url );

$enable_wallet_recharge = get_option( 'wsfw_enable_wallet_recharge', '' );
$product_id             = get_option( 'wps_wsfw_rechargeable_product_id', '' );
$user_id                = get_current_user_id();
$wallet_bal             = get_user_meta( $user_id, 'wps_wallet', true );
$is_user_restricted     = get_user_meta( $user_id, 'user_restriction_for_wallet', true );
$is_user_restricted  = apply_filters( 'wsfw_user_restrict_pro_check', $is_user_restricted );

$wallet_restrict_topup = apply_filters( 'wallet_restrict_topup', $user_id );
$wallet_restrict_transfer = apply_filters( 'wallet_restrict_transfer', $user_id );
$wallet_restrict_withdrawal = apply_filters( 'wallet_restrict_withdrawal', $user_id );
$wallet_restrict_coupon = apply_filters( 'wallet_restrict_coupon', $user_id );
$wallet_restrict_transaction = apply_filters( 'wallet_restrict_transaction', $user_id );

if ( empty( $wallet_bal ) ) {
	$wallet_bal = 0;
}

$wallet_tabs = array();
if ( 'restricted' !== $is_user_restricted ) {

	if ( ! empty( $product_id ) && ! empty( $enable_wallet_recharge ) ) {
		if ( 'on' != $wallet_restrict_topup ) {
			$wallet_tabs['wallet_recharge'] = array(
				'title'     => esc_html__( 'Add Balance', 'wallet-system-for-woocommerce' ),
				'url'       => $topup_url,
				'icon'      => '<path d="M28 10V4C28 3.46957 27.7893 2.96086 27.4142 2.58579C27.0391 2.21071 26.5304 2 26 2H6C4.93913 2 3.92172 2.42143 3.17157 3.17157C2.42143 3.92172 2 4.93913 2 6M2 6C2 7.06087 2.42143 8.07828 3.17157 8.82843C3.92172 9.57857 4.93913 10 6 10H30C30.5304 10 31.0391 10.2107 31.4142 10.5858C31.7893 10.9609 32 11.4696 32 12V18M2 6V27.5M32 26V32C32 32.5304 31.7893 33.0391 31.4142 33.4142C31.0391 33.7893 30.5304 34 30 34H8" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
								<circle cx="8.5" cy="27.5" r="6.5" stroke="#1D201F" stroke-width="2.5"/>
								<path d="M9.75 25.3333C9.75 24.643 9.19036 24.0833 8.5 24.0833C7.80964 24.0833 7.25 24.643 7.25 25.3333H9.75ZM7.25 29.6666C7.25 30.357 7.80964 30.9166 8.5 30.9166C9.19036 30.9166 9.75 30.357 9.75 29.6666H7.25ZM7.25 25.3333V29.6666H9.75V25.3333H7.25Z" fill="#1D201F"/>
								<path d="M10.6666 28.75C11.357 28.75 11.9166 28.1904 11.9166 27.5C11.9166 26.8096 11.357 26.25 10.6666 26.25L10.6666 28.75ZM6.33329 26.25C5.64294 26.25 5.08329 26.8096 5.08329 27.5C5.08329 28.1904 5.64294 28.75 6.33329 28.75L6.33329 26.25ZM10.6666 26.25L6.33329 26.25L6.33329 28.75L10.6666 28.75L10.6666 26.25Z" fill="#1D201F"/>
								<path d="M34 18.0001V26.0001H26C24.9391 26.0001 23.9217 25.5786 23.1716 24.8285C22.4214 24.0783 22 23.0609 22 22.0001C22 20.9392 22.4214 19.9218 23.1716 19.1716C23.9217 18.4215 24.9391 18.0001 26 18.0001H34Z" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
				'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-recharge.php',
			);
		}
	}

	if ( 'on' != $wallet_restrict_transfer ) {
		$wallet_tabs['wallet_transfer'] = array(
			'title'     => esc_html__( 'Wallet Transfer', 'wallet-system-for-woocommerce' ),
			'url'       => $wallet_url,
			'icon'      => '<rect x="2" y="12" width="32" height="15.5458" rx="1.5" stroke="#1D201F" stroke-width="2.5"/>
							<path d="M28 17V22M8 22V17" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
							<circle cx="18.1246" cy="19.5" r="3.5" stroke="#1D201F" stroke-width="2.5"/>
							<path d="M14.2556 34.1923L12.0164 31.9204L24.1429 31.9204" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M21.7444 5.80768L23.9836 8.0796L11.8571 8.0796" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
			'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-transfer.php',
		);
	}

	if ( 'on' != $wallet_restrict_withdrawal ) {
		$wallet_tabs['wallet_withdrawal'] = array(
			'title'     => esc_html__( 'Wallet Withdrawal Request', 'wallet-system-for-woocommerce' ),
			'url'       => $withdrawal_url,
			'icon'      => '<path d="M25.826 6.5L25.826 30.5652C25.826 31.3936 25.1545 32.0652 24.326 32.0652L11.1044 32.0652C10.2759 32.0652 9.60437 31.3936 9.60437 30.5652L9.60437 6.5" stroke="#1D201F" stroke-width="2.5"/>
							<path d="M6 5.77173C5.30964 5.77173 4.75 6.33137 4.75 7.02173C4.75 7.71208 5.30964 8.27173 6 8.27173V5.77173ZM30 8.27173C30.6904 8.27173 31.25 7.71208 31.25 7.02173C31.25 6.33137 30.6904 5.77173 30 5.77173V8.27173ZM6 8.27173H30V5.77173H6V8.27173Z" fill="#1D201F"/>
							<path d="M20.6086 25.8043L15.3913 25.8043" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
							<circle cx="18" cy="15.4996" r="3.65217" transform="rotate(90 18 15.4996)" stroke="#1D201F" stroke-width="2.5"/>
							<path d="M25 13H32C33.1046 13 34 12.1046 34 11V4C34 2.89543 33.1046 2 32 2H4C2.89543 2 2 2.89543 2 4V11C2 12.1046 2.89543 13 4 13H10" stroke="#1D201F" stroke-width="2.5"/>',
			'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-withdrawal.php',
		);
	}

	if ( 'on' != $wallet_restrict_coupon ) {

		$wallet_tabs = apply_filters( 'wps_wsfw_add_wallet_tabs_before_transaction', $wallet_tabs, WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH );
	}
}
if ( 'on' != $wallet_restrict_transaction ) {

	$wallet_tabs['wallet_transactions'] = array(
		'title'     => esc_html__( 'Transactions', 'wallet-system-for-woocommerce' ),
		'url'       => $transaction_url,
		'icon'      => '<path d="M2 7C2 4.23858 4.23858 2 7 2H23C25.7614 2 28 4.23858 28 7V28.6227C28 30.476 25.6972 31.3325 24.4861 29.9296L22.4665 27.5901C21.7195 26.7249 20.4005 26.6606 19.5729 27.4491L16.1765 30.6854C15.404 31.4215 14.1897 31.4215 13.4172 30.6854L10.067 27.4931C9.22232 26.6883 7.87085 26.7743 7.1351 27.6799L5.55223 29.628C4.36484 31.0894 2 30.2498 2 28.3668V7Z" stroke="#1D201F" stroke-width="2.5"/>
						<path d="M19 9.25C19.6904 9.25 20.25 8.69036 20.25 8C20.25 7.30964 19.6904 6.75 19 6.75V9.25ZM11 6.75C10.3096 6.75 9.75 7.30964 9.75 8C9.75 8.69036 10.3096 9.25 11 9.25V6.75ZM19 6.75H11V9.25H19V6.75Z" fill="#1D201F"/>
						<path d="M23 15.25C23.6904 15.25 24.25 14.6904 24.25 14C24.25 13.3096 23.6904 12.75 23 12.75V15.25ZM7 12.75C6.30964 12.75 5.75 13.3096 5.75 14C5.75 14.6904 6.30964 15.25 7 15.25V12.75ZM23 12.75H7V15.25H23V12.75Z" fill="#1D201F"/>
						<path d="M21 21.25C21.6904 21.25 22.25 20.6904 22.25 20C22.25 19.3096 21.6904 18.75 21 18.75V21.25ZM9 18.75C8.30964 18.75 7.75 19.3096 7.75 20C7.75 20.6904 8.30964 21.25 9 21.25V18.75ZM21 18.75H9V21.25H21V18.75Z" fill="#1D201F"/>',
		'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-transactions.php',
	);
}

$flag = false;
if ( ( $current_url == $main_url ) || ( $current_url == $page_url ) ) {
	$flag = true;
}
$wallet_keys = array_keys( $wallet_tabs );
do_action( 'wps_wcb_wallet_display_wrapper_for_qr' );
?>
<div class="wps_wcb_wallet_display_wrapper">
	<div class="wps_wcb_wallet_balance_container"> 
		<h4><?php esc_html_e( 'Wallet Balance', 'wallet-system-for-woocommerce' ); ?></h4>
		<p>
		<?php
		$wallet_bal = apply_filters( 'wps_wsfw_show_converted_price', $wallet_bal );
		echo wp_kses_post( wc_price( $wallet_bal, array( 'currency' => $current_currency ) ) );
		?>
		</p>
	</div>
	<?php
	if ( ( 'on' === $wallet_restrict_topup ) || ( 'on' === $wallet_restrict_transfer ) || ( 'on' === $wallet_restrict_withdrawal ) || ( 'on' === $wallet_restrict_coupon ) || ( 'on' === $wallet_restrict_transaction ) ) {
		?>
		<div class="wsfw_show_user_restriction_notice">
			<?php
				esc_html_e( 'Some functionality are restricted by Admin !!', 'wallet-system-for-woocommerce' );
			?>
		</div>
		<?php
	}
	?>
	<?php
	if ( 'restricted' === $is_user_restricted ) {
		?>
		<div class="wsfw_show_user_restriction_notice">
			<?php
				esc_html_e( 'Some functionality are restricted by Admin !!', 'wallet-system-for-woocommerce' );
			?>
		</div>
		<?php
	}
	?>
	<div class="wps_wcb_main_tabs_template">
		<div class="wps_wcb_body_template">
			<div class="wps_wcb_content_template">

				<nav class="wallet-tabs">
					<ul class='tabs'>
						<?php
						$allowed_html = wps_wsfw_lite_allowed_html();
						$wallet_script_option = get_option( 'wsfw_wallet_script_for_account_enabled' );
						$wallet_link_enabled = '';
						if ( 'on' == $wallet_script_option ) {
							$wallet_link_enabled = 'onclick=enable_wallet_link(this)';
						}
						foreach ( $wallet_tabs as $key => $wallet_tab ) {
							if ( $flag ) {
								if ( $key === $wallet_keys[0] ) {
									$class = 'active';
								} else {
									$class = '';
								}
								echo '<li ' . esc_attr( $wallet_link_enabled ) . " class='" . esc_html( $class ) . "'><a href='" . esc_url( $wallet_tab['url'] ) . "'><svg width='36' height='36' viewBox='0 0 36 36' fill='none' xmlns='http://www.w3.org/2000/svg'>" . wp_kses( $wallet_tab['icon'], $allowed_html ) . '</svg></a><h3>' . esc_html( $wallet_tab['title'] ) . '</h3></li>';
							} else {
								if ( $current_url === $wallet_tab['url'] ) {
									$class = 'active';
								} else {
									$class = '';
								}
								echo '<li ' . esc_attr( $wallet_link_enabled ) . " class='" . esc_html( $class ) . "'><a href='" . esc_url( $wallet_tab['url'] ) . "'><svg width='36' height='36' viewBox='0 0 36 36' fill='none' xmlns='http://www.w3.org/2000/svg'>" . wp_kses( $wallet_tab['icon'], $allowed_html ) . '</svg></a><h3>' . esc_html( $wallet_tab['title'] ) . '</h3></li>';
							}
						}
						?>
					</ul>
				</nav>

				<div class='content-section'>

				<?php
				foreach ( $wallet_tabs as $key => $wallet_tab ) {
					if ( $flag ) {
						if ( $key === $wallet_keys[0] ) {
							include_once $wallet_tab['file-path'];
						}
					} else {
						if ( $current_url === $wallet_tab['url'] ) {
							include_once $wallet_tab['file-path'];
						}
					}
				}
				?>
				</div>
			</div>
		</div>
	</div>
</div>
