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

global $wp;
$logged_in_user = wp_get_current_user();
if ( ! empty( $logged_in_user ) ) {
	$current_user_email = $logged_in_user->user_email ? $logged_in_user->user_email : '';
} else {
	$current_user_email = '';
}
$current_currency = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );

$http_host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

$request_url = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
$current_url = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $http_host . $request_url;



$nonce = ( isset( $_POST['wps_verifynonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wps_verifynonce'] ) ) : '';
if ( wp_verify_nonce( $nonce ) ) {

	if ( isset( $_POST['wps_recharge_wallet'] ) && ! empty( $_POST['wps_recharge_wallet'] ) ) {
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
		$transfer_note          = ! empty( $_POST['wps_wallet_transfer_note'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_transfer_note'] ) ) : '';
		$transfer_amount        = ! empty( $_POST['wps_wallet_transfer_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_transfer_amount'] ) ) : 0;
		$wallet_transfer_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $transfer_amount );



		$transfer_method = ! empty( $_POST['wps_wallet_transfer_method'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_transfer_method'] ) ) : 'email';

		$another_user_id = 0;
		$another_user_email = '';

		if ( 'email' === $transfer_method ) {
			// ✅ Transfer via Email
			$another_user_email = ! empty( $_POST['wps_wallet_transfer_user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_transfer_user_email'] ) ) : '';
			$user = get_user_by( 'email', $another_user_email );

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
		} elseif ( 'wallet_id' === $transfer_method ) {
			// ✅ Transfer via Wallet ID
			$wallet_id_input = ! empty( $_POST['wps_wallet_transfer_user_walletid'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_transfer_user_walletid'] ) ) : '';

			if ( ! empty( $wallet_id_input ) ) {
				// Find user by wallet_id meta.
				$user_query = new WP_User_Query(
					array(
						'meta_key'   => 'wps_wallet_id',
						'meta_value' => $wallet_id_input,
						'number'     => 1,
						'fields'     => 'all',
					)
				);

				if ( ! empty( $user_query->results ) ) {
					$user = $user_query->results[0];
					$another_user_id    = $user->ID;
					$another_user_email = $user->user_email;
				} else {
					show_message_on_form_submit( esc_html__( 'Wallet ID does not exist.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
					$update = false;
				}
			} else {
				show_message_on_form_submit( esc_html__( 'Please enter a Wallet ID.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
				$update = false;
			}
		}


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
				$balance   = $current_currency . ' ' . $transfer_amount;
				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {

					$mail_text1  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name1 ) . ",\r\n";
					$mail_text1 .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through wallet transfer by ', 'wallet-system-for-woocommerce' ) . $name2;
					$to1         = $user1->user_email;
					$from        = get_option( 'admin_email' );
					$subject     = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
					$headers1    = 'MIME-Version: 1.0' . "\r\n";
					$headers1   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
					$headers1   .= 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $to1 . "\r\n";

					if ( key_exists( 'wps_wswp_wallet_credit', WC()->mailer()->emails ) ) {

						$customer_email = WC()->mailer()->emails['wps_wswp_wallet_credit'];
						if ( ! empty( $customer_email ) ) {
							$user       = get_user_by( 'id', $another_user_id );
							$currency  = get_woocommerce_currency();
							$balance_mail = $balance;
							$user_name       = $user->first_name . ' ' . $user->last_name;
							$email_status = $customer_email->trigger( $another_user_id, $user_name, $balance_mail, '' );
						}
					} else {

						$wallet_payment_gateway->send_mail_on_wallet_updation( $to1, $subject, $mail_text1, $headers1 );
					}
				}

				$transaction_type     = __( 'Wallet credited by user ', 'wallet-system-for-woocommerce' ) . $user2->user_email . __( ' to user ', 'wallet-system-for-woocommerce' ) . $user1->user_email;
				$wallet_transfer_data = array(
					'user_id'          => $another_user_id,
					'amount'           => $transfer_amount,
					'currency'         => $current_currency,
					'payment_method'   => __( 'Wallet Transfer', 'wallet-system-for-woocommerce' ),
					'transaction_type' => $transaction_type,
					'transaction_type_1' => 'credit',
					'order_id'         => '',
					'note'             => $transfer_note,

				);

				$wallet_payment_gateway->insert_transaction_data_in_table( $wallet_transfer_data );

				$wallet_bal -= $wallet_transfer_amount;
				$update_user = update_user_meta( $user_id, 'wps_wallet', abs( $wallet_bal ) );
				if ( $update_user ) {
					$balance   = $current_currency . ' ' . $transfer_amount;
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$mail_text2  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name2 ) . ",\r\n";
						$mail_text2 .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through wallet transfer to ', 'wallet-system-for-woocommerce' ) . $name1;
						$to2         = $user2->user_email;
						$headers2    = 'MIME-Version: 1.0' . "\r\n";
						$headers2   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers2   .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to2 . "\r\n";
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

							$wallet_payment_gateway->send_mail_on_wallet_updation( $to2, $subject, $mail_text2, $headers2 );
						}
					}

					$transaction_type = __( 'Wallet debited from user ', 'wallet-system-for-woocommerce' ) . $user2->user_email . __( ' wallet, transferred to user ', 'wallet-system-for-woocommerce' ) . $user1->user_email;
					$transaction_data = array(
						'user_id'          => $user_id,
						'amount'           => $transfer_amount,
						'currency'         => $current_currency,
						'payment_method'   => __( 'Wallet Transfer', 'wallet-system-for-woocommerce' ),
						'transaction_type' => $transaction_type,
						'transaction_type_1' => 'debit',
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

	// kyc saving.
	if ( isset( $_POST['wps_submit_kyc'] ) && ! empty( $_POST['wps_submit_kyc'] ) ) {

		$update = true;

		if ( ! empty( $_POST['current_user_id'] ) ) {
			$user_id = intval( sanitize_text_field( wp_unslash( $_POST['current_user_id'] ) ) );
		} else {
			show_message_on_form_submit( __( 'Invalid request. User not found.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
			$update = false;
		}

		if ( $update ) {
			$uploaded_files = array();
			$errors = array();

			// ✅ Load upload handling functions
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			if ( ! empty( $_FILES['wps_wallet_kyc_document']['name'] ) ) {
				foreach ( $_FILES['wps_wallet_kyc_document']['name'] as $key => $name ) {
					if ( ! empty( $name ) ) {
						$file = array(
							'name'     => $_FILES['wps_wallet_kyc_document']['name'][ $key ],
							'type'     => $_FILES['wps_wallet_kyc_document']['type'][ $key ],
							'tmp_name' => $_FILES['wps_wallet_kyc_document']['tmp_name'][ $key ],
							'error'    => $_FILES['wps_wallet_kyc_document']['error'][ $key ],
							'size'     => $_FILES['wps_wallet_kyc_document']['size'][ $key ],
						);

						$upload = wp_handle_upload( $file, array( 'test_form' => false ) );

						if ( isset( $upload['url'] ) && ! isset( $upload['error'] ) ) {
							$uploaded_files[] = $upload['url'];
						} else {
							$errors[] = sprintf( __( 'Error uploading document: %s', 'wallet-system-for-woocommerce' ), $upload['error'] );
						}
					}
				}
			}

			if ( empty( $errors ) && ! empty( $uploaded_files ) ) {
				update_user_meta( $user_id, 'wps_wallet_kyc_documents', $uploaded_files );
				update_user_meta( $user_id, 'key_verification_status', 'pending' );

				show_message_on_form_submit(
					__( 'KYC documents uploaded successfully. Your verification is pending.', 'wallet-system-for-woocommerce' ),
					'woocommerce-message'
				);
			} else {
				foreach ( $errors as $error ) {
					show_message_on_form_submit( $error, 'woocommerce-error' );
				}
			}
		}
	}




	// kyc saving.

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



	if ( isset( $_POST['wps_wallet_fund_request'] ) && ! empty( $_POST['wps_wallet_fund_request'] ) ) {

		$update = true;

		$fund_request_method = ! empty( $_POST['wps_wallet_fund_request_another_method'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_fund_request_another_method'] ) ) : 'email';

		$wps_wallet_fund_request_amount = ! empty( $_POST['wps_wallet_fund_request_amount'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_fund_request_amount'] ) ) : 0;
		$wps_wallet_note                = ! empty( $_POST['wps_wallet_note'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_note'] ) ) : '';
		$wallet_user_id                 = ! empty( $_POST['wallet_user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wallet_user_id'] ) ) : 0;

		$wps_current_user_email         = ! empty( $_POST['wps_current_user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_current_user_email'] ) ) : '';

		$another_user_id    = 0;
		$another_user_email = '';

		// ------------------ Check method ------------------
		if ( 'email' === $fund_request_method ) {
			$wps_wallet_fund_request_another_user_email = ! empty( $_POST['wps_wallet_fund_request_another_user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_fund_request_another_user_email'] ) ) : '';
			$user = get_user_by( 'email', $wps_wallet_fund_request_another_user_email );

			if ( $user ) {
				$another_user_id    = $user->ID;
				$another_user_email = $user->user_email;
			} else {
				$invitation_link = apply_filters( 'wsfw_add_invitation_link_message', '' );
				if ( ! empty( $invitation_link ) ) {
					global $wp_session;
					$wp_session['wps_wallet_transfer_user_email'] = $wps_wallet_fund_request_another_user_email;
					$wp_session['wps_wallet_transfer_amount']     = $wps_wallet_fund_request_amount;
				}
				show_message_on_form_submit( 'Email Id does not exist. ' . $invitation_link, 'woocommerce-error' );
				$update = false;
			}
		} elseif ( 'wallet_id' === $fund_request_method ) {
			$wps_wallet_fund_request_another_user_walletid = ! empty( $_POST['wps_wallet_fund_request_another_user_walletid'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet_fund_request_another_user_walletid'] ) ) : '';

			if ( ! empty( $wps_wallet_fund_request_another_user_walletid ) ) {
				$user_query = new WP_User_Query(
					array(
						'meta_key'   => 'wps_wallet_id',
						'meta_value' => $wps_wallet_fund_request_another_user_walletid,
						'number'     => 1,
						'fields'     => 'all',
					)
				);

				if ( ! empty( $user_query->results ) ) {
					$user               = $user_query->results[0];
					$another_user_id    = $user->ID;
					$another_user_email = $user->user_email;
				} else {
					show_message_on_form_submit( esc_html__( 'Wallet ID does not exist.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
					$update = false;
				}
			} else {
				show_message_on_form_submit( esc_html__( 'Please enter a Wallet ID.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
				$update = false;
			}
		}
		// ------------------ End method check ------------------

		// Basic validations.
		if ( empty( $wps_wallet_fund_request_amount ) ) {
			show_message_on_form_submit( esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
			$update = false;
		} elseif ( $another_user_email == $wps_current_user_email ) {
			show_message_on_form_submit( esc_html__( 'You cannot request fund to yourself.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
			$update = false;
		}

		if ( $update ) {
			if ( ! empty( $wallet_user_id ) ) {
				$user     = get_user_by( 'id', $wallet_user_id );
				$username = $user->user_login;
			}

			$args          = array(
				'post_title'  => $username,
				'post_type'   => 'wallet_fund_request',
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
						if ( 'wps_wallet_fund_request_amount' === $key ) {
							$withdrawal_bal = apply_filters( 'wps_wsfw_convert_to_base_price', $value );
							update_post_meta( $withdrawal_id, $key, $withdrawal_bal );
						} else {
							update_post_meta( $withdrawal_id, $key, $value );
						}
					}
				}
				update_post_meta( $withdrawal_id, 'requested_user_id', $another_user_id );

				wp_enqueue_script( 'wps-public-shortcode-dis' );
				wp_add_inline_script( 'wps-public-shortcode-dis', 'window.location.href = "' . $current_url . '"' );
			}
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

	if ( isset( $_POST['wps_wallet_generate_wallet_id'] ) && ! empty( $_POST['wps_wallet_generate_wallet_id'] ) ) {
		$assign_wallet_id  = ! empty( $_POST['assign_wallet_id'] ) ? sanitize_text_field( wp_unslash( $_POST['assign_wallet_id'] ) ) : '';
		$existing_wallet_id = get_user_meta( $assign_wallet_id, 'wps_wallet_id', true );

		if ( empty( $existing_wallet_id ) ) {
			$common_obj  = new Wallet_System_For_Woocommerce_Common( 'wallet system for woocommerce', WALLET_SYSTEM_FOR_WOOCOMMERCE_VERSION );
			$wallet_id = $common_obj->wps_wsfw_generate_unique_wallet_id( $assign_wallet_id );
			update_user_meta( $assign_wallet_id, 'wps_wallet_id', $wallet_id );
		}
	}
}





?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$main_url               = wc_get_endpoint_url( 'wps-wallet' );
$topup_url              = wc_get_endpoint_url( 'wps-wallet', 'wallet-topup' );
$wallet_url             = wc_get_endpoint_url( 'wps-wallet', 'wallet-transfer' );
$wallet_referal_url     = wc_get_endpoint_url( 'wps-wallet', 'wallet-referral' );
$withdrawal_url         = wc_get_endpoint_url( 'wps-wallet', 'wallet-withdrawal' );
$transaction_url        = wc_get_endpoint_url( 'wps-wallet', 'wallet-transactions' );

$enable_wallet_recharge = get_option( 'wsfw_enable_wallet_recharge', '' );
$product_id             = get_option( 'wps_wsfw_rechargeable_product_id', '' );
$user_id                = get_current_user_id();
$wallet_bal             = get_user_meta( $user_id, 'wps_wallet', true );
$is_pro_plugin = false;
$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
$is_user_restricted     = get_user_meta( $user_id, 'user_restriction_for_wallet', true );
$wps_wallet_restrict_wallet_id     = get_user_meta( $user_id, 'wps_wallet_restrict_wallet_id', true );
if ( $is_pro_plugin ) {
	$is_user_restricted = '';
}

if ( empty( $wallet_bal ) ) {
	$wallet_bal = 0;
}
$wallet_restrict_topup = apply_filters( 'wallet_restrict_topup', $user_id );
$wallet_restrict_transfer = apply_filters( 'wallet_restrict_transfer', $user_id );
$wallet_restrict_withdrawal = apply_filters( 'wallet_restrict_withdrawal', $user_id );
$wallet_restrict_coupon = apply_filters( 'wallet_restrict_coupon', $user_id );
$wallet_restrict_fund_request = apply_filters( 'wallet_restrict_fund_request', $user_id );
$wallet_restrict_transaction = apply_filters( 'wallet_restrict_transaction', $user_id );
$wallet_restrict_referral = apply_filters( 'wallet_restrict_referral', $user_id );
$wallet_restrict_qrcode = apply_filters( 'wallet_restrict_qrcode', $user_id );

$wps_wsfw_enable_cashback = get_option( 'wps_wsfw_enable_cashback' );
$wps_wallet_cashback_bal = get_user_meta( $user_id, 'wps_wallet_cashback_bal', true );
$wps_wallet_cashback_bal = empty( $wps_wallet_cashback_bal ) ? 0 : $wps_wallet_cashback_bal;


$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
$wps_wallet_restrict_message_to_user = 'on';
$wps_wallet_restrict_message_for = '';
if ( $is_pro_plugin ) {
	$wps_wallet_restrict_message_to_user = apply_filters( 'wps_wallet_restrict_message_to_user', $user_id );
	$wps_wallet_restrict_message_for = apply_filters( 'wps_wallet_restrict_message_for', $user_id );
}

$kyc_verification_url   = wc_get_endpoint_url( 'wps-wallet', 'wallet-kyc-verification' );
$wsfw_enable_wallet_kyc = get_option( 'wsfw_enable_wallet_kyc' );

$wallet_tabs = array();

if ( 'restricted' !== $is_user_restricted ) {

	if ( ! empty( $product_id ) && ! empty( $enable_wallet_recharge ) ) {
		if ( 'on' != $wallet_restrict_topup ) {
			$wallet_tabs['wallet_recharge'] = array(
				'title'     => esc_html__( 'Add Balance', 'wallet-system-for-woocommerce' ),
				'url'       => $topup_url,
				'className' => 'wps_wallet_recharge_tab',
				'icon'      => '<path fill-rule="evenodd" clip-rule="evenodd" d="M31.8202 20C31.8202 13.4719 26.5281 8.17985 20 8.17985C13.4719 8.17985 8.17983 13.4719 8.17983 20C8.17983 26.5281 13.4719 31.8202 20 31.8202C26.5281 31.8202 31.8202 26.5281 31.8202 20ZM20 5.71429C27.8898 5.71429 34.2857 12.1102 34.2857 20C34.2857 27.8898 27.8898 34.2857 20 34.2857C12.1102 34.2857 5.71428 27.8898 5.71428 20C5.71428 12.1102 12.1102 5.71429 20 5.71429Z" fill="black"/>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M19.9999 12.9659C20.6807 12.9659 21.2327 13.5178 21.2327 14.1987V25.8013C21.2327 26.4821 20.6807 27.0341 19.9999 27.0341C19.319 27.0341 18.7671 26.4821 18.7671 25.8013V14.1987C18.7671 13.5178 19.319 12.9659 19.9999 12.9659Z" fill="#483DE0"/>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M12.9659 20.0001C12.9659 19.3193 13.5178 18.7674 14.1987 18.7674H25.8013C26.4821 18.7674 27.034 19.3193 27.034 20.0001C27.034 20.681 26.4821 21.2329 25.8013 21.2329H14.1987C13.5178 21.2329 12.9659 20.681 12.9659 20.0001Z" fill="#483DE0"/>',
				'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-recharge.php',
			);
		}
	}


	if ( 'on' != $wallet_restrict_transfer ) {
		$wallet_tabs['wallet_transfer'] = array(
			'title'     => esc_html__( 'Wallet Transfer', 'wallet-system-for-woocommerce' ),
			'url'       => $wallet_url,
			'className' => 'wps_wallet_transfer_tab',
			'icon'      => '<rect width="40" height="40" rx="6" fill="#F6F5FD"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M19.827 18.9013C19.2591 18.3334 19.2591 17.4126 19.827 16.8447L29.5051 7.16658C30.073 6.59867 30.9938 6.59867 31.5617 7.16658C32.1296 7.73449 32.1296 8.65526 31.5617 9.22318L21.8836 18.9013C21.3157 19.4692 20.3949 19.4692 19.827 18.9013Z" fill="#483DE0"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M23.0331 6.98231C23.0331 6.17916 23.6842 5.52808 24.4873 5.52808L30.0685 5.52808C31.8165 5.52808 33.2335 6.94514 33.2335 8.69318L33.2335 14.2456C33.2335 15.0488 32.5825 15.6999 31.7793 15.6999C30.9762 15.6999 30.3251 15.0488 30.3251 14.2456L30.3251 8.69318C30.3251 8.55144 30.2102 8.43655 30.0685 8.43655L24.4873 8.43655C23.6842 8.43655 23.0331 7.78546 23.0331 6.98231Z" fill="#483DE0"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M14.0119 7.90847C11.0355 7.90847 8.6227 10.3213 8.6227 13.2977V25.2737C8.6227 28.2501 11.0355 30.663 14.0119 30.663H25.988C28.9644 30.663 31.3772 28.2501 31.3772 25.2737V19.1635C31.3772 18.3604 32.0283 17.7093 32.8314 17.7093C33.6346 17.7093 34.2857 18.3604 34.2857 19.1635V25.2737C34.2857 29.8564 30.5707 33.5714 25.988 33.5714H14.0119C9.42923 33.5714 5.71423 29.8564 5.71423 25.2737V13.2977C5.71423 8.715 9.42924 5 14.0119 5H18.839C19.6422 5 20.2932 5.65108 20.2932 6.45423C20.2932 7.25739 19.6422 7.90847 18.839 7.90847H14.0119Z" fill="black"/>',
			'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-transfer.php',
		);
	}

	if ( 'on' != $wallet_restrict_withdrawal ) {
		$wallet_tabs['wallet_withdrawal'] = array(
			'title'     => esc_html__( 'Wallet Withdrawal Request', 'wallet-system-for-woocommerce' ),
			'url'       => $withdrawal_url,
			'className' => 'wps_wallet_withdrawal_tab',
			'icon'      => '<rect width="40" height="40" rx="6" fill="#E5E3FA"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M19.9162 19.0872C19.0883 19.0872 18.4171 19.7584 18.4171 20.5864L18.4171 34.6957C18.4171 35.5237 19.0883 36.1948 19.9162 36.1948C20.7442 36.1948 21.4154 35.5237 21.4154 34.6957L21.4154 20.5864C21.4154 19.7584 20.7442 19.0872 19.9162 19.0872Z" fill="#483DE0"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M13.5653 30.112C12.9798 30.6974 12.9798 31.6466 13.5653 32.2321L17.6335 36.3003C18.9077 37.5745 20.9736 37.5745 22.2478 36.3003L26.2952 32.253C26.8806 31.6675 26.8806 30.7183 26.2952 30.1329C25.7097 29.5475 24.7605 29.5475 24.1751 30.1329L20.1278 34.1803C20.0244 34.2836 19.8569 34.2836 19.7536 34.1803L15.6854 30.112C15.0999 29.5265 14.1507 29.5265 13.5653 30.112Z" fill="#483DE0"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M14.6252 24.3783C11.557 24.3783 9.06965 21.891 9.06965 18.8228V11.7681C9.06965 8.69984 11.557 6.21253 14.6252 6.21253H26.0891C29.1573 6.21253 31.6446 8.69984 31.6446 11.7681V18.8228C31.6446 21.891 29.1573 24.3783 26.0891 24.3783H24.0104C23.1825 24.3783 22.5113 25.0495 22.5113 25.8774C22.5113 26.7054 23.1825 27.3766 24.0104 27.3766H26.0891C30.8132 27.3766 34.6428 23.5469 34.6428 18.8228V11.7681C34.6428 7.04396 30.8132 3.2143 26.0891 3.2143H14.6252C9.90108 3.2143 6.07141 7.04396 6.07141 11.7681V18.8228C6.07141 23.5469 9.90108 27.3766 14.6252 27.3766H15.7905C16.6184 27.3766 17.2896 26.7054 17.2896 25.8774C17.2896 25.0495 16.6184 24.3783 15.7905 24.3783H14.6252Z" fill="black"/>',
			'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-withdrawal.php',
		);
	}


	$is_refer_option = get_option( 'wps_wsfw_wallet_action_refer_friend_enable' );
	if ( 'on' == $is_refer_option ) {
		$wallet_tabs['wallet_refer_friend'] = array(
			'title'     => esc_html__( 'Walet Referral', 'wallet-system-for-woocommerce' ),
			'url'       => $wallet_referal_url,
			'className' => 'wps_wallet_referral_tab',
			'icon'      => '<path fill-rule="evenodd" clip-rule="evenodd" d="M6.40263 9.52276C8.21174 6.39535 11.5966 4.28571 15.4762 4.28571H24.5238C30.3097 4.28571 35 8.97598 35 14.7619V23.8095C35 29.5954 30.3097 34.2857 24.5238 34.2857H15.4762C9.69028 34.2857 5 29.5954 5 23.8095V19.2857C5 18.4967 5.63959 17.8571 6.42857 17.8571C7.21755 17.8571 7.85714 18.4967 7.85714 19.2857V23.8095C7.85714 28.0175 11.2682 31.4286 15.4762 31.4286H24.5238C28.7318 31.4286 32.1429 28.0175 32.1429 23.8095V14.7619C32.1429 10.5539 28.7318 7.14285 24.5238 7.14285H15.4762C12.6578 7.14285 10.1953 8.67244 8.87578 10.9534C8.48072 11.6364 7.60683 11.8697 6.92388 11.4747C6.24094 11.0796 6.00756 10.2057 6.40263 9.52276Z" fill="#1E1E1E"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M19.9996 11.0717C20.7885 11.0717 21.4281 11.7112 21.4281 12.5002V18.694L25.5335 22.7994C26.0914 23.3573 26.0914 24.2618 25.5335 24.8197C24.9756 25.3776 24.0711 25.3776 23.5132 24.8197L18.9894 20.2959C18.7215 20.028 18.571 19.6646 18.571 19.2857V12.5002C18.571 11.7112 19.2106 11.0717 19.9996 11.0717Z" fill="#483DE0"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M7.48138 3.93726C8.26561 4.02374 8.83124 4.72959 8.74476 5.51381L8.36239 8.98116L11.8297 9.36352C12.614 9.45001 13.1796 10.1559 13.0931 10.9401C13.0066 11.7243 12.3008 12.2899 11.5166 12.2035L6.62926 11.6645C5.84503 11.578 5.2794 10.8722 5.36588 10.0879L5.90483 5.20064C5.99131 4.41641 6.69716 3.85078 7.48138 3.93726Z" fill="#1E1E1E"/>',
			'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-referral.php',
		);
	}


	if ( 'on' != $wallet_restrict_coupon ) {
		$wallet_tabs = apply_filters( 'wps_wsfw_add_wallet_tabs_before_transaction', $wallet_tabs, WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH );
	}
	if ( 'on' != $wallet_restrict_fund_request ) {
		$wallet_tabs = apply_filters( 'wps_wsfw_add_wallet_tabs_wallet_fund_request', $wallet_tabs, WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH );
	}
}
if ( 'on' != $wallet_restrict_transaction ) {

	$wallet_tabs['wallet_transactions'] = array(
		'title'     => esc_html__( 'Transactions', 'wallet-system-for-woocommerce' ),
		'url'       => $transaction_url,
		'className' => 'wps_wallet_transactions_tab',
		'icon'      => '<path fill-rule="evenodd" clip-rule="evenodd" d="M6.40263 9.52276C8.21174 6.39535 11.5966 4.28571 15.4762 4.28571H24.5238C30.3097 4.28571 35 8.97598 35 14.7619V23.8095C35 29.5954 30.3097 34.2857 24.5238 34.2857H15.4762C9.69028 34.2857 5 29.5954 5 23.8095V19.2857C5 18.4967 5.63959 17.8571 6.42857 17.8571C7.21755 17.8571 7.85714 18.4967 7.85714 19.2857V23.8095C7.85714 28.0175 11.2682 31.4286 15.4762 31.4286H24.5238C28.7318 31.4286 32.1429 28.0175 32.1429 23.8095V14.7619C32.1429 10.5539 28.7318 7.14285 24.5238 7.14285H15.4762C12.6578 7.14285 10.1953 8.67244 8.87578 10.9534C8.48072 11.6364 7.60683 11.8697 6.92388 11.4747C6.24094 11.0796 6.00756 10.2057 6.40263 9.52276Z" fill="#1E1E1E"/>
		<path fill-rule="evenodd" clip-rule="evenodd" d="M19.9996 11.0717C20.7885 11.0717 21.4281 11.7112 21.4281 12.5002V18.694L25.5335 22.7994C26.0914 23.3573 26.0914 24.2618 25.5335 24.8197C24.9756 25.3776 24.0711 25.3776 23.5132 24.8197L18.9894 20.2959C18.7215 20.028 18.571 19.6646 18.571 19.2857V12.5002C18.571 11.7112 19.2106 11.0717 19.9996 11.0717Z" fill="#483DE0"/>
		<path fill-rule="evenodd" clip-rule="evenodd" d="M7.48138 3.93726C8.26561 4.02374 8.83124 4.72959 8.74476 5.51381L8.36239 8.98116L11.8297 9.36352C12.614 9.45001 13.1796 10.1559 13.0931 10.9401C13.0066 11.7243 12.3008 12.2899 11.5166 12.2035L6.62926 11.6645C5.84503 11.578 5.2794 10.8722 5.36588 10.0879L5.90483 5.20064C5.99131 4.41641 6.69716 3.85078 7.48138 3.93726Z" fill="#1E1E1E"/>',
		'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-transactions.php',
	);
}

if ( 'on' != $wallet_restrict_referral ) {

	$wallet_tabs['wallet_referral'] = array(
		'title'     => esc_html__( 'Wallet Referral', 'wallet-system-for-woocommerce' ),
		'url'       => $wallet_referal_url,
		'className' => 'wps_wallet_transactions_tab',
		'icon'      => '<path fill-rule="evenodd" clip-rule="evenodd" d="M6.40263 9.52276C8.21174 6.39535 11.5966 4.28571 15.4762 4.28571H24.5238C30.3097 4.28571 35 8.97598 35 14.7619V23.8095C35 29.5954 30.3097 34.2857 24.5238 34.2857H15.4762C9.69028 34.2857 5 29.5954 5 23.8095V19.2857C5 18.4967 5.63959 17.8571 6.42857 17.8571C7.21755 17.8571 7.85714 18.4967 7.85714 19.2857V23.8095C7.85714 28.0175 11.2682 31.4286 15.4762 31.4286H24.5238C28.7318 31.4286 32.1429 28.0175 32.1429 23.8095V14.7619C32.1429 10.5539 28.7318 7.14285 24.5238 7.14285H15.4762C12.6578 7.14285 10.1953 8.67244 8.87578 10.9534C8.48072 11.6364 7.60683 11.8697 6.92388 11.4747C6.24094 11.0796 6.00756 10.2057 6.40263 9.52276Z" fill="#1E1E1E"/>
		<path fill-rule="evenodd" clip-rule="evenodd" d="M19.9996 11.0717C20.7885 11.0717 21.4281 11.7112 21.4281 12.5002V18.694L25.5335 22.7994C26.0914 23.3573 26.0914 24.2618 25.5335 24.8197C24.9756 25.3776 24.0711 25.3776 23.5132 24.8197L18.9894 20.2959C18.7215 20.028 18.571 19.6646 18.571 19.2857V12.5002C18.571 11.7112 19.2106 11.0717 19.9996 11.0717Z" fill="#483DE0"/>
		<path fill-rule="evenodd" clip-rule="evenodd" d="M7.48138 3.93726C8.26561 4.02374 8.83124 4.72959 8.74476 5.51381L8.36239 8.98116L11.8297 9.36352C12.614 9.45001 13.1796 10.1559 13.0931 10.9401C13.0066 11.7243 12.3008 12.2899 11.5166 12.2035L6.62926 11.6645C5.84503 11.578 5.2794 10.8722 5.36588 10.0879L5.90483 5.20064C5.99131 4.41641 6.69716 3.85078 7.48138 3.93726Z" fill="#1E1E1E"/>',
		'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-referral.php',
	);
}

if ( 'on' === $wsfw_enable_wallet_kyc ) {
	$wallet_tabs['wallet_kyc_verification'] = array(
		'title'     => esc_html__( 'KYC Verification', 'wallet-system-for-woocommerce' ),
		'url'       => $kyc_verification_url,
		'className' => 'wps_kyc_verification_tab',
		'icon'      => '<path xmlns="http://www.w3.org/2000/svg" d="M20.5616 5C21.0048 5.00004 21.5204 5.12237 22.0167 5.27637C22.535 5.43721 23.1219 5.6621 23.7383 5.92188C24.9722 6.44188 26.383 7.12943 27.7061 7.80859C29.0322 8.48928 30.2846 9.16871 31.2042 9.67773C31.6641 9.93233 32.0418 10.145 32.3047 10.2939C32.436 10.3683 32.5392 10.4267 32.6094 10.4668C32.6446 10.4869 32.6722 10.5032 32.6905 10.5137C32.6994 10.5188 32.7063 10.5227 32.711 10.5254C32.7134 10.5267 32.7156 10.5276 32.7169 10.5283L32.7178 10.5293H32.7188L33.171 10.79L33.2159 11.3115V11.3174C33.2162 11.3209 33.2173 11.3258 33.2178 11.332C33.2188 11.3444 33.22 11.362 33.2217 11.3848C33.2252 11.4305 33.2298 11.4972 33.2354 11.583C33.2467 11.7548 33.2616 12.0043 33.2755 12.3203C33.3031 12.9521 33.3268 13.8523 33.3145 14.9326C33.2899 17.0882 33.1197 19.9863 32.5274 22.918C31.877 26.1373 29.533 29.2565 27.1622 31.5264C25.9629 32.6745 24.7175 33.6432 23.6055 34.332C23.0499 34.6762 22.5126 34.9598 22.0196 35.1602C21.5434 35.3537 21.0359 35.5 20.5616 35.5C20.0868 35.5 19.5797 35.354 19.1046 35.1602C18.6129 34.9596 18.0781 34.6754 17.5255 34.3311C16.4192 33.6417 15.182 32.6723 13.9932 31.5234C11.6435 29.2525 9.32842 26.1309 8.70905 22.9092C8.14547 19.9771 8.00344 17.0784 8.00006 14.9229C7.99838 13.8425 8.03167 12.9423 8.06549 12.3105C8.08241 11.9945 8.09938 11.745 8.11237 11.5732C8.11886 11.4874 8.12407 11.4208 8.12799 11.375C8.12994 11.3522 8.13176 11.3346 8.13287 11.3223C8.13343 11.3161 8.13353 11.3111 8.13385 11.3076C8.134 11.306 8.13473 11.3047 8.13483 11.3037V11.3018L8.18268 10.7939L8.62213 10.5352V10.5342H8.62408C8.62528 10.5335 8.62673 10.5316 8.62897 10.5303C8.63358 10.5276 8.64066 10.5237 8.64948 10.5186C8.66736 10.508 8.69428 10.4927 8.72858 10.4727C8.79715 10.4325 8.89715 10.3733 9.02545 10.2988C9.28225 10.1499 9.65118 9.93735 10.1006 9.68262C10.9991 9.17342 12.2234 8.49356 13.5215 7.8125C14.8167 7.13299 16.2004 6.44456 17.4141 5.92383C18.0203 5.66375 18.5993 5.43965 19.1124 5.27832C19.6042 5.12369 20.1171 5 20.5616 5ZM20.5616 7C20.4331 7 20.1586 7.0451 19.712 7.18555C19.2866 7.31929 18.775 7.51596 18.2022 7.76172C17.0578 8.25273 15.7272 8.91454 14.4512 9.58398C13.1782 10.2519 11.9736 10.9203 11.087 11.4229C10.678 11.6547 10.337 11.8504 10.0889 11.9941C10.081 12.1153 10.0711 12.2568 10.0626 12.417C10.0304 13.0179 9.99845 13.8808 10.0001 14.9189C10.0033 17.0006 10.1411 19.7645 10.6729 22.5312C11.1783 25.1604 13.1528 27.9287 15.3839 30.085C16.4855 31.1496 17.612 32.0287 18.5831 32.6338C19.0689 32.9366 19.5015 33.1612 19.8604 33.3076C20.2359 33.4608 20.4634 33.5 20.5616 33.5C20.6603 33.4999 20.8891 33.4611 21.2667 33.3076C21.6278 33.1609 22.0625 32.9349 22.5518 32.6318C23.5297 32.0261 24.6666 31.1474 25.7794 30.082C28.0328 27.9246 30.0348 25.1531 30.5665 22.5215C31.1255 19.7545 31.2918 16.9907 31.3155 14.9092C31.3273 13.871 31.3037 13.0081 31.2774 12.4072C31.2707 12.253 31.2615 12.1162 31.2549 11.998C31.0009 11.8544 30.6526 11.6587 30.2354 11.4277C29.3273 10.9251 28.0947 10.256 26.793 9.58789C25.4884 8.91821 24.1286 8.25633 22.962 7.76465C22.378 7.51855 21.8565 7.32077 21.4239 7.18652C20.9698 7.04561 20.6914 7.00005 20.5616 7Z" fill="#0B0925"/>
		<path xmlns="http://www.w3.org/2000/svg" d="M25.4064 16.5596C25.8134 16.1864 26.4462 16.2142 26.8195 16.6211C27.1927 17.0281 27.1658 17.6609 26.759 18.0342L19.6994 24.5108C19.317 24.8617 18.7293 24.8617 18.3469 24.5108L15.0432 21.4795C14.6363 21.1062 14.6086 20.4734 14.9816 20.0664C15.355 19.6595 15.9878 19.6325 16.3947 20.0059L19.0226 22.416L25.4064 16.5596Z" fill="#FC9918"/>',
		'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-kyc-verification.php',
	);
}


$wallet_tabs = apply_filters( 'wps_wsfw_add_wallet_tabs', $wallet_tabs );
$flag = false;
if ( $current_url == $main_url ) {
	$flag = true;
}
$wallet_keys = array_keys( $wallet_tabs );
/**
 * Show message on form submit.
 *
 * @param string $wpg_message message to be shown on form submission.
 * @param string $type error type.
 * @return void
 */
function show_message_on_form_submit( $wpg_message, $type = 'error' ) {
	$wpg_notice = '<div class="woocommerce"><p class="' . esc_attr( $type ) . '">' . $wpg_message . '</p>	</div>';
	echo wp_kses_post( $wpg_notice );
}

?>
<div class="wps_wcb_wallet_display_wrapper">
	<div class="wps_wcb_wallet_display_wrapper_with_qr">
		<div class="wps_wcb_wallet_balance_container"> 
			<div>
			<h4 class="wallet-balance-title"><?php esc_html_e( 'Wallet Balance', 'wallet-system-for-woocommerce' ); ?>
			<?php
			  $wps_wallet_kyc_status    = get_user_meta( $user_id, 'key_verification_status', true );
			if ( 'approved' == $wps_wallet_kyc_status ) {
				// Show the KYC approved icon.
				?>
				  <span title="<?php esc_html_e( 'KYC Approved', 'wallet-system-for-woocommerce' ); ?>" class="wps_kyc_approved_icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
					<path d="M20.25 6C28.1201 6 34.5 12.3799 34.5 20.25C34.5 28.1201 28.1201 34.5 20.25 34.5C12.3799 34.5 6 28.1201 6 20.25C6 12.3799 12.3799 6 20.25 6ZM27.0879 16.5137C26.7109 16.1104 26.0783 16.0891 25.6748 16.4658L18.7305 22.9521L15.8438 20.2559C15.4403 19.8789 14.8077 19.9004 14.4307 20.3037C14.0536 20.7073 14.075 21.3398 14.4785 21.7168L18.0479 25.0518C18.4322 25.4105 19.0298 25.4107 19.4141 25.0518L27.04 17.9268C27.4433 17.5498 27.4646 16.9172 27.0879 16.5137Z" fill="white"/>
					</svg>
				  </span>
				  <?php
			}
			?>
			</h4>
			<p>
			<?php
			$wallet_bal = apply_filters( 'wps_wsfw_show_converted_price', $wallet_bal );
			// custom.
			$wps_wsfwp_wallet_user_currency_setting = get_option( 'wps_wsfwp_wallet_user_currency_setting' );
			if ( 'yes' == $wps_wsfwp_wallet_user_currency_setting ) {

				$wps_wallet_last_order_currency = get_user_meta( $user_id, 'wps_wallet_last_order_currency', true );
				if ( $wps_wallet_last_order_currency == $current_currency ) {

					echo wp_kses_post( wc_price( $wallet_bal, array( 'currency' => $current_currency ) ) );

				} else {

					$wallet_bal = 0;
					echo wp_kses_post( wc_price( $wallet_bal, array( 'currency' => $current_currency ) ) );

				}
			} else if ( 'no' == $wps_wsfwp_wallet_user_currency_setting ) {

				$wps_wallet_order_geolocation_currency = get_user_meta( $user_id, 'wps_wallet_order_geolocation_currency', true );
				if ( $wps_wallet_order_geolocation_currency == $current_currency ) {

					echo wp_kses_post( wc_price( $wallet_bal, array( 'currency' => $current_currency ) ) );

				} else {

					$wallet_bal = 0;
					echo wp_kses_post( wc_price( $wallet_bal, array( 'currency' => $current_currency ) ) );
				}
			} else {

				echo wp_kses_post( wc_price( $wallet_bal, array( 'currency' => $current_currency ) ) );

			}
			?>
		</p>
		</div>
		<?php
		if ( 'on' == $wps_wsfw_enable_cashback ) {
			?>
				<div class="wps_wcb_wallet_cashback_wrap">
				<h4><?php esc_html_e( 'Cashback Earned', 'wallet-system-for-woocommerce' ); ?></h4>
				<?php
				echo wp_kses_post( wc_price( $wps_wallet_cashback_bal, array( 'currency' => $current_currency ) ) );
				?>
				</div>
				<?php
		}
		?>
			<!-- custom work. -->
		<?php if ( 'on' != $wallet_restrict_transaction ) { ?>
			<div class="wps_wcb_wallet_view_transaction"><a href="<?php echo esc_url( $transaction_url ); ?>"><h4><?php esc_html_e( 'View Transactions', 'wallet-system-for-woocommerce' ); ?> </h4></a>
		</div>
			<?php
		}
		$wps_wallet_id = get_user_meta( $user_id, 'wps_wallet_id', true );
		if ( empty( $wps_wallet_id ) ) {
			$wps_wallet_id = 'Not Generated';
		}
		if ( $is_pro_plugin ) {
			if ( 'on' != $wps_wallet_restrict_wallet_id ) {
				?>
				<div class="wps_wsfw_wallet_user_id">
					<h4><?php esc_html_e( 'wallet id - ', 'wallet-system-for-woocommerce' ); ?><strong><?php echo esc_html( $wps_wallet_id ); ?></strong></h4>
				
				<?php
				if ( 'Not Generated' == $wps_wallet_id ) {
					?>
					<form method="post" action="">
						<input type="hidden" name="assign_wallet_id" value="<?php echo esc_attr( $user_id ); ?>">
						<input type="hidden" id="wps_verifynonce" name="wps_verifynonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
						<input type="submit" class=" button" id="wps_wallet_generate_wallet_id" name="wps_wallet_generate_wallet_id" value="<?php esc_html_e( 'Generate Wallet ID', 'wallet-system-for-woocommerce' ); ?>" >
					</form>
					<?php
				}
				?>
				</div>
				<?php
			}
		} else {
			?>
			<div class="wps_wsfw_wallet_user_id">
				<h4><?php esc_html_e( 'wallet id - ', 'wallet-system-for-woocommerce' ); ?><strong><?php echo esc_html( $wps_wallet_id ); ?></strong></h4>
			
			<?php
			if ( 'Not Generated' == $wps_wallet_id ) {
				?>
				<form method="post" action="">
					<input type="hidden" name="assign_wallet_id" value="<?php echo esc_attr( $user_id ); ?>">
					<input type="hidden" id="wps_verifynonce" name="wps_verifynonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
					<input type="submit" class=" button" id="wps_wallet_generate_wallet_id" name="wps_wallet_generate_wallet_id" value="<?php esc_html_e( 'Generate Wallet ID', 'wallet-system-for-woocommerce' ); ?>" >
				</form>
				<?php
			}
			?>
			</div>
			<?php

		}
		?>
		
	</div>
		<?php
		if ( 'on' != $wallet_restrict_qrcode ) {
			do_action( 'wallet_qr_vode' );
		}
		?>
	</div>
	<?php

	if ( 'on' == $wps_wallet_restrict_message_to_user ) {
		if ( ( 'on' === $wallet_restrict_topup ) || ( 'on' === $wallet_restrict_transfer ) || ( 'on' === $wallet_restrict_withdrawal ) || ( 'on' === $wallet_restrict_coupon ) || ( 'on' === $wallet_restrict_transaction ) || ( 'on' === $wallet_restrict_fund_request ) ) {
			?>
			<div class="wsfw_show_user_restriction_notice">
				<?php
				if ( ! empty( $wps_wallet_restrict_message_for ) ) {
					echo esc_html( $wps_wallet_restrict_message_for );

				} else {
					esc_html_e( 'Some functionalities are restricted by Admin but you can use your wallet amount !!', 'wallet-system-for-woocommerce' );
				}
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
				if ( ! empty( $wps_wallet_restrict_message_for ) ) {
					echo esc_html( $wps_wallet_restrict_message_for );
				} else {
					esc_html_e( 'Some functionalities are restricted by Admin but you can use your wallet amount !!', 'wallet-system-for-woocommerce' );
				}
				?>
			</div>
			<?php
		}
	}
	?>
	<div class="wps_wcb_main_tabs_template">
		<div class="wps_wcb_body_template">
			<div class="wps_wcb_content_template">
				<nav class="wallet-tabs">
					<ul class='tabs'>
						<?php
						$wallet_script_option = get_option( 'wsfw_wallet_script_for_account_enabled' );
						$wallet_link_enabled = '';
						if ( 'on' == $wallet_script_option ) {
							$wallet_link_enabled = 'onclick=enable_wallet_link(this)';
						}
						$allowed_html = wps_wsfw_lite_allowed_html();
						foreach ( $wallet_tabs as $key => $wallet_tab ) {
							if ( 'wallet_transactions' == $key ) {
								continue;
							}
							if ( 'wallet_referral' == $key ) {
								continue;
							}
							if ( 'wallet_giftcard' == $key ) {
								$wallet_tab['className'] = 'none';
							}

							if ( $flag ) {
								if ( $key === $wallet_keys[0] ) {
									$class = 'active';
								} else {
									$class = '';
								}
								echo '<li ' . esc_attr( $wallet_link_enabled ) . " class='" . esc_html( $class ) . "'><a href='" . esc_url( $wallet_tab['url'] ) . "'><svg class='" . wp_kses( $wallet_tab['className'], $allowed_html ) . "' width='40' height='40' viewBox='0 0 40 40' fill='none' xmlns='http://www.w3.org/2000/svg'>" . wp_kses( $wallet_tab['icon'], $allowed_html ) . '</svg><h3>' . esc_html( $wallet_tab['title'] ) . '</h3></a></li>';
							} else {
								if ( $current_url === $wallet_tab['url'] ) {
									$class = 'active';
								} else {
									$class = '';
								}
								echo '<li ' . esc_attr( $wallet_link_enabled ) . " class='" . esc_html( $class ) . "'><a href='" . esc_url( $wallet_tab['url'] ) . "'><svg class='" . wp_kses( $wallet_tab['className'], $allowed_html ) . "' width='40' height='40' viewBox='0 0 40 40' fill='none' xmlns='http://www.w3.org/2000/svg'>" . wp_kses( $wallet_tab['icon'], $allowed_html ) . '</svg><h3>' . esc_html( $wallet_tab['title'] ) . '</h3></a></li>';
							}
						}
						?>
					</ul>
				</nav>
				
				<script type="text/javascript">

setInterval(function time(){
  var d = new Date();

  var hours = 24 - d.getHours();
  var min = 60 - d.getMinutes();
  if((min + '').length == 1){
	min = '0' + min;
  }
  var sec = 60 - d.getSeconds();
  if((sec + '').length == 1){
		sec = '0' + sec;
  }
  jQuery('#the-final-countdown').html(hours+'h:'+min+'m:'+sec+'s')
}, 1000);
					
				</script>
	<?php wp_cache_set( 'wps_upsell_countdown_timer', 'true' ); ?>

	<?php

	$is_wallet_recharge_enabled = get_option( 'wps_wsfwp_wallet_promotion_tab_enable' );
	$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
	if ( ! $is_pro_plugin ) {
		$is_wallet_recharge_enabled = false;
	}
	if ( 'on' == $is_wallet_recharge_enabled ) {
		?>


				<div class="wallet-promotion-tab">
					<div class="wps-wsfw__prom-tab-head">
						<h3><span class="wps-pr-title"><?php echo esc_html__( 'Wallet Promotion :', 'wallet-system-for-woocommerce' ); ?></span></h3>
						<?php

						$is_wallet_recharge_enabled = get_option( 'wps_wsfwp_wallet_promotion_tab_limited_offer_enable' );
						if ( 'on' == $is_wallet_recharge_enabled ) {
							?>
						<p class="wps-pr-sub"><?php echo esc_html__( 'Limited Time Only:', 'wallet-system-for-woocommerce' ); ?> <span  class="wps-pr-time" id="the-final-countdown"></span></p>
							<?php

						}
						?>
					</div>
					<div class="wps-wsfw__prom-tab-wrap">


		<?php

					$wallet_promotions_data_title = get_option( 'wallet_promotions_data_title' );

			$wallet_promotions_data_content = get_option( 'wallet_promotions_data_content' );

		if ( ! empty( $wallet_promotions_data_title ) && is_array( $wallet_promotions_data_title ) ) {
			if ( '' == $wallet_promotions_data_title[0] ) {
				$wallet_promotions_data_title = array();
			}
		} else {
			$wallet_promotions_data_title = array();
		}
		if ( ! empty( $wallet_promotions_data_content ) && is_array( $wallet_promotions_data_content ) ) {
			if ( '' == $wallet_promotions_data_content[0] ) {
				$wallet_promotions_data_content = array();
			}
		} else {
			$wallet_promotions_data_content = array();
		}
				$wps_wallet_recharge_tab_cashback_type = get_option( 'wps_wallet_recharge_tab_cashback_type' );

		if ( ! empty( $wallet_promotions_data_title ) && is_array( $wallet_promotions_data_title ) ) {
			$index = 0;
			$count_data = count( $wallet_promotions_data_title );
			if ( $count_data > 0 ) {


				for ( $i = 0; $i < $count_data; $i++ ) {
					?>
				<div class="wps-wsfw__prom-tab-item wps-active">
							<div class="wps-pr__item-wrap">
								<span class="wps-pr-offer"><?php echo esc_html( $wallet_promotions_data_title[ $i ] ); ?></span>
								<p class="wps-pr-offer-desc"><?php echo esc_html( $wallet_promotions_data_content[ $i ] ); ?></p>
							</div>
						</div>
					<?php
				}
			}
		}
		?>
								
					</div>
				</div>

				<?php
	}
	?>
	<form method="post" action="" id="wps_wallet_main_tab_form" enctype="multipart/form-data" >
				<div class='content-section'>

				<?php
				foreach ( $wallet_tabs as $key => $wallet_tab ) {
					if ( $flag ) {
						if ( $key === $wallet_keys[0] ) {
							include_once $wallet_tab['file-path'];
						}
					} elseif ( $current_url === $wallet_tab['url'] ) {

							include_once $wallet_tab['file-path'];
					}
				}
				?>
				<input type="hidden" id="wps_verifynonce" name="wps_verifynonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
				</div>
			</form>
			</div>
		</div>
	</div>
</div>
