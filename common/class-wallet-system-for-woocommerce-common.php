<?php
/**
 * Order Factory
 *
 * The WooCommerce order factory creating the right order objects.
 *
 * @version 2.5.0
 * @package Wallet_System_For_Woocommerce
 */

use Automattic\WooCommerce\Utilities\OrderUtil;
/**
 * The common functionality of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/common
 */

/**
 * The common functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the common stylesheet and JavaScript.
 * namespace wallet_system_for_woocommerce_common.
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/common
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wallet_System_For_Woocommerce_Common {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the JavaScript for the common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_common_enqueue_scripts() {
		wp_register_script( $this->plugin_name . 'common', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'common/src/js/wallet-system-for-woocommerce-common.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name . 'common',
			'wsfw_common_param',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
		wp_enqueue_script( $this->plugin_name . 'common' );
	}


	/**
	 * Make rechargeable product purchasable
	 *
	 * @param boolean           $is_purchasable check product is purchasable or not.
	 * @param WC_Product object $product product object.
	 * @return boolean
	 */
	public function wps_wsfw_wallet_recharge_product_purchasable( $is_purchasable, $product ) {
		$product_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		if ( ! empty( $product_id ) ) {
			if ( $product_id == $product->get_id() ) {
				$is_purchasable = true;
			}
		}
		return $is_purchasable;
	}

	/**
	 * Show message for guest user.
	 *
	 * @param string $wpg_message message to be shown on form submission.
	 * @param string $type error type.
	 * @return void
	 */
	public function show_message_for_guest_user( $wpg_message, $type = 'error' ) {
		$wpg_notice = '<div class="woocommerce"><p class="' . esc_attr( $type ) . '">' . $wpg_message . '</p>	</div>';
		echo wp_kses_post( $wpg_notice );
	}

	/**
	 * Shortcodes for wallet.
	 *
	 * @return void
	 */
	public function wps_wsfw_wallet_shortcodes() {
		add_shortcode( 'WPS_WALLET_RECHARGE', array( $this, 'wps_wsfw_elementor_wallet_recharge' ) );
		add_shortcode( 'WPS_WALLET_TRANSFER', array( $this, 'wps_wsfw_elementor_wallet_transfer' ) );
		add_shortcode( 'WPS_WITHDRAWAL_REQUEST', array( $this, 'wps_wsfw_elementor_wallet_withdrawal' ) );
		add_shortcode( 'WPS_WALLET_TRANSACTIONS', array( $this, 'wps_wsfw_elementor_wallet_transactions' ) );
	}

	/**
	 * Show wallet recharge page according to shortcode.
	 *
	 * @return string
	 */
	public function wps_wsfw_elementor_wallet_recharge() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			$this->show_message_for_guest_user( esc_html__( 'You are not logged in, please log in first for recharging the wallet.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			include WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'elementor-widget/wps-wsfw-elementor-wallet-recharge.php';
		}
		return ob_get_clean();
	}

	/**
	 * Show wallet transfer page according to shortcode.
	 *
	 * @return string
	 */
	public function wps_wsfw_elementor_wallet_transfer() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			$this->show_message_for_guest_user( esc_html__( 'You are not logged in, please log in first for transferring the wallet amount.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			include WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'elementor-widget/wps-wsfw-elementor-wallet-transfer.php';
		}
		return ob_get_clean();
	}

	/**
	 * Show wallet withdrawal page according to shortcode.
	 *
	 * @return string
	 */
	public function wps_wsfw_elementor_wallet_withdrawal() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			$this->show_message_for_guest_user( esc_html__( 'You are not logged in, please log in first for requesting wallet withdrawal.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			include WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'elementor-widget/wps-wsfw-elementor-wallet-withdrawal.php';
		}
		return ob_get_clean();
	}

	/**
	 * Show wallet transaction page according to shortcode.
	 *
	 * @return string
	 */
	public function wps_wsfw_elementor_wallet_transactions() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			$this->show_message_for_guest_user( esc_html__( 'You are not logged in, please log in first to see wallet transactions.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			include WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'elementor-widget/wps-wsfw-elementor-wallet-transactions.php';
		}
		return ob_get_clean();
	}

	/**
	 * Show message on form submit
	 *
	 * @param string $wpg_message message to be shown on form submission.
	 * @param string $type error type.
	 * @return void
	 */
	public function show_message_on_wallet_form_submit( $wpg_message, $type = 'woocommerce-error' ) {
		$wpg_notice = '<div class="woocommerce"><p class="' . esc_attr( $type ) . '">' . $wpg_message . '</p>	</div>';
		echo wp_kses_post( $wpg_notice );
	}

	/**
	 * Add wallet to cart, request wallet withdrawal.
	 *
	 * @return void
	 */
	public function wps_wsfw_save_wallet_public_shortcode() {

		$nonce = ( isset( $_POST['wps_verifynonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wps_verifynonce'] ) ) : '';

		if ( wp_verify_nonce( $nonce ) ) {

			if ( isset( $_POST['wps_recharge_wallet'] ) && ! empty( $_POST['wps_recharge_wallet'] ) ) {

				unset( $_POST['wps_recharge_wallet'] );

				if ( empty( $_POST['wps_wallet_recharge_amount'] ) ) {
					$this->show_message_on_wallet_form_submit( esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
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
					wp_redirect( wc_get_cart_url() );
					exit();
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
					$http_host   = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
					$request_url = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
					$current_url = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $http_host . $request_url;
					wp_safe_redirect( $current_url );
					exit();
				}
			}
			if ( isset( $_POST['wps_proceed_transfer'] ) && ! empty( $_POST['wps_proceed_transfer'] ) ) {
				unset( $_POST['wps_proceed_transfer'] );

					$current_currency = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );
					$update           = true;
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
					$this->show_message_on_wallet_form_submit( esc_html__( 'Email Id does not exist. ', 'wallet-system-for-woocommerce' ) . $invitation_link, 'woocommerce-error' );
					$update = false;
				}
				if ( empty( $_POST['wps_wallet_transfer_amount'] ) ) {
					$this->show_message_on_wallet_form_submit( esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
					$update = false;
				} elseif ( $wallet_bal < $wallet_transfer_amount ) {
					$this->show_message_on_wallet_form_submit( esc_html__( 'Please enter amount less than or equal to wallet balance', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
					$update = false;
				} elseif ( $another_user_email == $wps_current_user_email ) {
					$this->show_message_on_wallet_form_submit( esc_html__( 'You cannot transfer amount to yourself.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
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
									$balance_mail = $balance;
									$user_name       = $user->first_name . ' ' . $user->last_name;
									$customer_email->trigger( $another_user_id, $user_name, $balance_mail, '' );
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
							$this->show_message_on_wallet_form_submit( esc_html__( 'Amount is transferred successfully', 'wallet-system-for-woocommerce' ), 'woocommerce-message' );
						} else {
							$this->show_message_on_wallet_form_submit( esc_html__( 'Amount is not transferred', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
						}
					} else {
						$this->show_message_on_wallet_form_submit( esc_html__( 'No user found.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
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
		} else {
			$this->show_message_on_wallet_form_submit( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		}
	}

	/** Cashback functionality start here */

	/**
	 * This function is used to give cashback on order complete
	 *
	 * @param int    $order_id order id.
	 * @param string $old_status old status.
	 * @param string $new_status new status.
	 * @return void
	 */
	public function wsfw_cashback_on_complete_order( $order_id, $old_status, $new_status ) {
		if ( ! is_user_logged_in() ) {
			return;
		}
		$order          = wc_get_order( $order_id );

		if ( 'on' != get_option( 'wps_wsfw_enable_cashback' ) ) {
			return;
		}

		$payment_method = $order->get_payment_method();
		$restrict_gatewaay  = ! empty( get_option( 'wps_wsfw_multiselect_cashback_restrict' ) ) ? get_option( 'wps_wsfw_multiselect_cashback_restrict' ) : array();
		if ( in_array( $payment_method, $restrict_gatewaay ) ) {
			return;
		}

		if ( $old_status != $new_status ) {

			$order                  = wc_get_order( $order_id );
			$userid                 = $order->get_user_id();

			if ( empty( $userid ) ) {
				return;
			}
			$order_items            = $order->get_items();
			$order_total            = $order->get_total();
			$order_shipping = $order->get_shipping_total();
			$order_total_tax = $order->get_total_tax();

			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				// HPOS usage is enabled.
				$order_total_meta = $order->get_meta( 'wps_wsfw_order_total', true );
				if ( ! empty( $order_total_meta ) ) {
					$order_total = $order_total_meta;
				}
			} else {
				$order_total_meta = get_post_meta( $order_id, 'wps_wsfw_order_total', true );
				if ( ! empty( $order_total_meta ) ) {
					$order_total = $order_total_meta;
				}
			}

			$order_currency         = $order->get_currency();
			$walletamount           = get_user_meta( $userid, 'wps_wallet', true );
			$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
			$wps_wallet_cashback_bal = get_user_meta( $userid, 'wps_wallet_cashback_bal', true );
			$wps_wallet_cashback_bal = empty( $wps_wallet_cashback_bal ) ? 0 : $wps_wallet_cashback_bal;
			$walletamount     = apply_filters( 'wps_wsfw_convert_to_base_price', $walletamount );

			$wallet_user            = get_user_by( 'id', $userid );
			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
			$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
			$payment_method         = $order->get_payment_method();
			$wallet_id              = get_option( 'wps_wsfw_rechargeable_product_id', '' );
			$cashback_process       = get_option( 'wps_wsfw_multiselect_category' );
			$cashback_process       = is_array( $cashback_process ) && ! empty( $cashback_process ) ? $cashback_process : array();
			$updated                = false;
			$cashback_amount_order  = 0;
			$credited_amount        = 0;
			$wps_send_mail          = false;
			$wsfw_cashbak_type      = get_option( 'wps_wsfw_cashback_type' );
			$wsfw_max_cashbak_amount = ! empty( get_option( 'wps_wsfw_cashback_amount_max' ) ) ? get_option( 'wps_wsfw_cashback_amount_max' ) : 20;
			$order_subtotal       = $order->get_subtotal();
			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				// HPOS usage is enabled.

				$order_subtotal_meta = $order->get_meta( 'wps_wsfw_order_subtotal', true );
				if ( ! empty( $order_subtotal_meta ) ) {
					$order_subtotal = $order_subtotal_meta;
				}
			} else {

				$order_subtotal_meta = get_post_meta( $order_id, 'wps_wsfw_order_subtotal', true );
				if ( ! empty( $order_subtotal_meta ) ) {
					$order_subtotal = $order_subtotal_meta;
				}
			}

			$wsfw_min_cart_amount = ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : '';
			if ( floatval( $order_subtotal ) < floatval( $wsfw_min_cart_amount ) ) {
				return;
			}

			if ( ! empty( $cashback_process ) && in_array( $new_status, $cashback_process ) ) {

				if ( ! empty( $order_items ) ) {
					foreach ( $order_items as $item_id => $item ) {
						$product_id = $item->get_product_id();
						if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {
							if ( 'on' == get_option( 'wps_wsfw_cashback_wallet_recharge' ) ) {
								$allow_refund = true;
							} else {
								$allow_refund = false;
							}
						} else {
							$allow_refund = true;
						}
					}
				}

				if ( $allow_refund ) {
					$wps_cash_back_provided = '';
					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$wps_cash_back_provided = $order->get_meta( 'wps_cash_back_provided', true );
					} else {
						$wps_cash_back_provided = get_post_meta( $order_id, 'wps_cash_back_provided', true );
					}

					$wps_wsfw_cashback_rule = get_option( 'wps_wsfw_cashback_rule', '' );

					if ( ! isset( $wps_cash_back_provided ) || empty( $wps_cash_back_provided ) ) {
						if ( 'cartwise' === $wps_wsfw_cashback_rule ) {
							if ( $order_total > 0 ) {
								$cashback_amount_order = $this->wsfw_get_calculated_cashback_amount( $order_total, $product_id, 1 );
								if ( $cashback_amount_order > 0 ) {
									$credited_amount     = apply_filters( 'wps_wsfw_convert_to_base_price', $cashback_amount_order );
									$walletamount       += $credited_amount;
									$wps_wallet_cashback_bal += $credited_amount;
									update_user_meta( $userid, 'wps_wallet', $walletamount );
									update_user_meta( $userid, 'wps_wallet_cashback_bal', $wps_wallet_cashback_bal );

									if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
										// HPOS usage is enabled.
										$order->update_meta_data( 'wps_cashback_receive_amount', $credited_amount );
										$order->update_meta_data( 'wps_cash_back_provided', 'done' );
										$order->save();

									} else {
										update_post_meta( $order_id, 'wps_cashback_receive_amount', $credited_amount );
										update_post_meta( $order_id, 'wps_cash_back_provided', 'done' );
									}
									$wps_send_mail = true;
								}
							}
						} elseif ( ! empty( $order_items ) ) {
							foreach ( $order_items as $order_key => $order_values ) {
								$product_id   = $order_values->get_product_id();
								$qty = $order_values->get_quantity();
								$wps_cat_wise = $this->wps_get_cashback_cat_wise( $product_id );
								if ( $wps_cat_wise ) {
									$product_obj = wc_get_product( $product_id );
									if ( is_object( $product_obj ) ) {
										$product_price         = $order->get_line_subtotal( $order_values );
										$cashback_amount_order = $this->wsfw_get_calculated_cashback_amount( $product_price, $product_id, $qty );
										if ( $cashback_amount_order > 0 ) {
											$credited_amount     += apply_filters( 'wps_wsfw_convert_to_base_price', $cashback_amount_order );
											$updated             = true;
										}
									}
								}
							}
							if ( $updated ) {
								if ( 'percent' === $wsfw_cashbak_type ) {
									if ( $credited_amount <= $wsfw_max_cashbak_amount ) {
										$credited_amount = $credited_amount;
									} else {
										$credited_amount = $wsfw_max_cashbak_amount;
									}
								} else {
									$credited_amount = $credited_amount;
								}
								$walletamount         += $credited_amount;
								$cashback_amount_order = $credited_amount;
								$wps_wallet_cashback_bal += $credited_amount;
								update_user_meta( $userid, 'wps_wallet', $walletamount );
								update_user_meta( $userid, 'wps_wallet_cashback_bal', $wps_wallet_cashback_bal );
								if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
									// HPOS usage is enabled.
									$order->update_meta_data( 'wps_cashback_receive_amount', $credited_amount );
									$order->update_meta_data( 'wps_cash_back_provided', 'done' );
									$order->save();

								} else {
									update_post_meta( $order_id, 'wps_cashback_receive_amount', $credited_amount );
									update_post_meta( $order_id, 'wps_cash_back_provided', 'done' );
								}
								$wps_send_mail = true;
							}
						}
					}

					if ( $wps_send_mail ) {
						$balance   = $order->get_currency() . ' ' . $cashback_amount_order;
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
							$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
							$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through cashback.', 'wallet-system-for-woocommerce' );
							$to         = $wallet_user->user_email;
							$from       = get_option( 'admin_email' );
							$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
							$headers    = 'MIME-Version: 1.0' . "\r\n";
							$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
							$headers   .= 'From: ' . $from . "\r\n" .
								'Reply-To: ' . $to . "\r\n";

							if ( key_exists( 'wps_wswp_wallet_credit_cashback', WC()->mailer()->emails ) ) {

								$customer_email = WC()->mailer()->emails['wps_wswp_wallet_credit_cashback'];

								if ( ! empty( $customer_email ) ) {
									$user       = get_user_by( 'id', $userid );
									$balance_mail = $balance;
									$user_name       = $user->first_name . ' ' . $user->last_name;
									$customer_email->trigger( $userid, $user_name, $balance_mail, '' );
								}
							} else {
								$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );

							}
						}
						$transaction_type = __( 'Wallet credited through cashback ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
						$transaction_data = array(
							'user_id'          => $userid,
							'amount'           => $cashback_amount_order,
							'currency'         => $order->get_currency(),
							'payment_method'   => $payment_method,
							'transaction_type' => htmlentities( $transaction_type ),
							'transaction_type_1' => 'credit',
							'order_id'         => $order_id,
							'note'             => '',
						);
						$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
					}
				}
			}

			/** Cashback refund process start here */

			if ( 'completed' == $old_status && 'refunded' == $new_status ) {

				if ( ! empty( $order_items ) ) {
					foreach ( $order_items as $item_id => $item ) {
						$product_id = $item->get_product_id();
						if ( isset( $product_id ) && ! empty( $product_id ) && $product_id != $wallet_id ) {
							$allow_refund = true;
						} else {
							$allow_refund = false;
						}
					}
				}

				if ( $allow_refund ) {
					$wps_cashback_receive_amount = '';
					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$wps_cashback_receive_amount = $order->get_meta( 'wps_cashback_receive_amount', true );
					} else {
						$wps_cashback_receive_amount = get_post_meta( $order_id, 'wps_cashback_receive_amount', true );
					}
					$updated                     = false;

					if ( $wps_cashback_receive_amount > 0 ) {
						$wps_cash_back_refunded = '';
						if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
							// HPOS usage is enabled.
							$wps_cash_back_refunded = $order->get_meta( 'wps_cash_back_refunded', true );
						} else {
							$wps_cash_back_refunded = get_post_meta( $order_id, 'wps_cash_back_refunded', true );
						}
						if ( ! isset( $wps_cash_back_refunded ) || empty( $wps_cash_back_refunded ) ) {
							$walletamount        = get_user_meta( $userid, 'wps_wallet', true );
							$walletamount        = empty( $walletamount ) ? 0 : $walletamount;
							$wps_cashback_amount = $walletamount - $wps_cashback_receive_amount;
							$debited_amount      = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_cashback_amount );
							update_user_meta( $userid, 'wps_wallet', $debited_amount );

							if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
								// HPOS usage is enabled.
								$order->update_meta_data( 'wps_cash_back_refunded', 'done' );
								$order->save();

							} else {
								update_post_meta( $order_id, 'wps_cash_back_refunded', 'done' );
							}
							$updated = true;
						}
					}

					if ( $updated ) {
						$balance   = $order->get_currency() . ' ' . $wps_cashback_receive_amount;
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
							$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
							;
							$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through order refunded.', 'wallet-system-for-woocommerce' );
							$to         = $wallet_user->user_email;
							$from       = get_option( 'admin_email' );
							$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
							$headers    = 'MIME-Version: 1.0' . "\r\n";
							$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
							$headers   .= 'From: ' . $from . "\r\n" .
								'Reply-To: ' . $to . "\r\n";

							if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) ) {

								$customer_email = WC()->mailer()->emails['wps_wswp_wallet_debit'];
								if ( ! empty( $customer_email ) ) {
									$user       = get_user_by( 'id', $userid );
									$balance_mail = $balance;
									$user_name       = $user->first_name . ' ' . $user->last_name;
									$customer_email->trigger( $userid, $user_name, $balance_mail, '' );
								}
							} else {

								$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
							}
						}
						$transaction_type = __( 'Wallet debited through ', 'wallet-system-for-woocommerce' ) . $new_status . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
						$transaction_data = array(
							'user_id'          => $userid,
							'amount'           => $wps_cashback_receive_amount,
							'currency'         => $order->get_currency(),
							'payment_method'   => $payment_method,
							'transaction_type' => htmlentities( $transaction_type ),
							'transaction_type_1' => 'debit',
							'order_id'         => $order_id,
							'note'             => '',
						);
						$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
					}
				}
			}

			/** Cashback return after order cancelation */

			$wsfw_array_ordr_status = array( 'processing', 'on-hold', 'pending', 'completed' );
			if ( in_array( $old_status, $wsfw_array_ordr_status ) && 'cancelled' == $new_status ) {
				if ( ! empty( $order_items ) ) {
					foreach ( $order_items as $item_id => $item ) {
						$product_id = $item->get_product_id();
						if ( isset( $product_id ) && ! empty( $product_id ) && $product_id != $wallet_id ) {
							$allow_refund = true;
						} else {
							$allow_refund = false;
						}
					}
				}

				if ( $allow_refund ) {
					$wps_cashback_receive_amount = '';
					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$wps_cashback_receive_amount = $order->get_meta( 'wps_cashback_receive_amount', true );
					} else {
						$wps_cashback_receive_amount = get_post_meta( $order_id, 'wps_cashback_receive_amount', true );
					}
					$updated                     = false;

					if ( $wps_cashback_receive_amount > 0 ) {
						$wps_cash_back_refunded = '';
						if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
							// HPOS usage is enabled.
							$wps_cash_back_refunded = $order->get_meta( 'wps_cash_back_cancelled', true );
						} else {
							$wps_cash_back_refunded = get_post_meta( $order_id, 'wps_cash_back_cancelled', true );
						}
						if ( ! isset( $wps_cash_back_refunded ) || empty( $wps_cash_back_refunded ) ) {
							$walletamount        = get_user_meta( $userid, 'wps_wallet', true );
							$walletamount        = empty( $walletamount ) ? 0 : $walletamount;
							$wps_cashback_amount = $walletamount - $wps_cashback_receive_amount;
							$debited_amount      = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_cashback_amount );
							update_user_meta( $userid, 'wps_wallet', $debited_amount );
							if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
								// HPOS usage is enabled.
								$order->update_meta_data( 'wps_cash_back_cancelled', 'done' );
								$order->save();

							} else {
								update_post_meta( $order_id, 'wps_cash_back_cancelled', 'done' );
							}

							$updated = true;
						}
					}

					if ( $updated ) {
						$balance   = $order->get_currency() . ' ' . $wps_cashback_receive_amount;
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
							$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
							;
							$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through order cancelled.', 'wallet-system-for-woocommerce' );
							$to         = $wallet_user->user_email;
							$from       = get_option( 'admin_email' );
							$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
							$headers    = 'MIME-Version: 1.0' . "\r\n";
							$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
							$headers   .= 'From: ' . $from . "\r\n" .
								'Reply-To: ' . $to . "\r\n";

							if ( key_exists( 'wps_wswp_wallet_debit', WC()->mailer()->emails ) ) {

								$customer_email = WC()->mailer()->emails['wps_wswp_wallet_debit'];
								if ( ! empty( $customer_email ) ) {
									$user       = get_user_by( 'id', $userid );
									$balance_mail = $balance;
									$user_name       = $user->first_name . ' ' . $user->last_name;
									$customer_email->trigger( $userid, $user_name, $balance_mail, '' );
								}
							} else {

								$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
							}
						}
						$transaction_type = __( 'Wallet debited through ', 'wallet-system-for-woocommerce' ) . $new_status . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
						$transaction_data = array(
							'user_id'          => $userid,
							'amount'           => $wps_cashback_receive_amount,
							'currency'         => $order->get_currency(),
							'payment_method'   => $payment_method,
							'transaction_type' => htmlentities( $transaction_type ),
							'transaction_type_1' => 'debit',
							'order_id'         => $order_id,
							'note'             => '',
						);
						$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
					}
				}
			}
		}
	}

	/**
	 * This function is used to calculate cashback.
	 *
	 * @param [type] $order_total contain order totol amount.
	 * @param [type] $product_id contain product id.
	 * @param [type] $qty contain quantity.
	 * @return int
	 */
	public function wsfw_get_calculated_cashback_amount( $order_total, $product_id, $qty ) {
		$cashback_amount         = 0;
		$wsfw_max_cashbak_amount = ! empty( get_option( 'wps_wsfw_cashback_amount_max' ) ) ? get_option( 'wps_wsfw_cashback_amount_max' ) : 0;
		$wsfw_cashbak_amount     = ! empty( get_option( 'wps_wsfw_cashback_amount' ) ) ? get_option( 'wps_wsfw_cashback_amount' ) : 0;
		$wsfw_cashbak_type       = get_option( 'wps_wsfw_cashback_type' );
		$wsfw_min_cart_amount    = ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : 0;
		$wps_wsfw_cashback_rule  = get_option( 'wps_wsfw_cashback_rule', '' );

		if ( 'cartwise' === $wps_wsfw_cashback_rule ) {
			if ( $order_total > $wsfw_min_cart_amount ) {

				if ( 'percent' === $wsfw_cashbak_type ) {

					$total                        = $order_total;
					$total                        = apply_filters( 'wps_wsfw_wallet_calculate_cashback_on_total_amount_order_atatus', $order_total );
					$wsfw_percent_cashback_amount = $total * ( $wsfw_cashbak_amount / 100 );

					if ( ! empty( $wsfw_max_cashbak_amount ) ) {
						if ( $wsfw_percent_cashback_amount <= $wsfw_max_cashbak_amount ) {
							$cashback_amount += $wsfw_percent_cashback_amount;
						} else {
							$cashback_amount += $wsfw_max_cashbak_amount;
						}
					} else {
						$cashback_amount += $wsfw_percent_cashback_amount;
					}
				} elseif ( $wsfw_cashbak_amount > 0 ) {
						$cashback_amount += $wsfw_cashbak_amount;
				}
			}
		} else {
			$product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
			$wps_wsfwp_cashback_amount = apply_filters( 'wsfw_wallet_cashback_using_catwise', $product_cats_ids, $product_id, $qty );
			if ( ! empty( $order_total ) ) {
				if ( 'percent' === $wsfw_cashbak_type ) {

					$total                        = $order_total;
					$total                        = apply_filters( 'wps_wsfw_wallet_calculate_cashback_on_total_amount_order_atatus', $order_total );
					$wsfw_percent_cashback_amount = $total * ( $wsfw_cashbak_amount / 100 );
					$wps_wsfwp_cashback_type = get_term_meta( $product_cats_ids, '_wps_wsfwp_cashback_type', true );
					if ( 'percent' == $wps_wsfwp_cashback_type && $wps_wsfwp_cashback_type ) {

						if ( ! empty( $wsfw_max_cashbak_amount ) ) {
							if ( $wps_wsfwp_cashback_amount <= $wsfw_max_cashbak_amount ) {
								$cashback_amount += $wps_wsfwp_cashback_amount;
							} else {
								$cashback_amount += $wsfw_max_cashbak_amount;
							}
						} else {
							$cashback_amount += $wps_wsfwp_cashback_amount;
						}
					} else {
						$cashback_amount += $wsfw_percent_cashback_amount;
					}
				} elseif ( $wps_wsfwp_cashback_amount > 0 && ! ( is_array( $wps_wsfwp_cashback_amount ) ) ) {
						$cashback_amount += $wps_wsfwp_cashback_amount;
				} else if ( $wsfw_cashbak_amount > 0 ) {
					$cashback_amount += $wsfw_cashbak_amount;
				}
			}
		}

		return $cashback_amount;
	}

	/**
	 * This funtion is used to give category wise cashback.
	 *
	 * @param int $product_id product id.
	 * @return bool
	 */
	public function wps_get_cashback_cat_wise( $product_id ) {
		if ( ! empty( $product_id ) ) {
			$terms                              = get_the_terms( $product_id, 'product_cat' );

			$max_id = '';
			if ( ! empty( $terms ) ) {
				$max_id = $terms[0]->term_id;
				$max_value = get_term_meta( $terms[0]->term_id, '_wps_wsfwp_category_rule', true );
				foreach ( $terms as $key => $value ) {
					$temp = get_term_meta( $value->term_id, '_wps_wsfwp_category_rule', true );
					if ( $max_value < $temp ) {
						$max_value = $temp;
						$max_id = $value->term_id;
					}
				}
			}

			$term_id = $max_id;
			$wps_wsfw_multiselect_category_rule = get_option( 'wps_wsfw_multiselect_category_rule', array() );
			$wps_wsfwp_category_rule = get_term_meta( $term_id, '_wps_wsfwp_category_rule', true );
			$check = false;
			$check = apply_filters( 'wsfw_check_pro_plugin_common', $check );
			if ( true == $check && ! empty( $wps_wsfwp_category_rule ) ) {
				$wps_wsfw_multiselect_category_rule = array();
				$wps_wsfw_multiselect_category_rule[] = $wps_wsfwp_category_rule;
			}
			$wps_wsfw_multiselect_category_rule = is_array( $wps_wsfw_multiselect_category_rule ) && ! empty( $wps_wsfw_multiselect_category_rule ) ? $wps_wsfw_multiselect_category_rule : array();
			$flag                               = false;
			if ( ! empty( $wps_wsfw_multiselect_category_rule ) && is_array( $wps_wsfw_multiselect_category_rule ) ) {
				if ( ! empty( $terms ) && is_array( $terms ) ) {
					foreach ( $terms as $terms_key => $terms_values ) {
						$product_cat_slug = $terms_values->name;
						if ( in_array( $product_cat_slug, $wps_wsfw_multiselect_category_rule ) ) {
							$flag = true;
						}
					}
				}
			}
		}
		return $flag;
		;
	}

	/** Comment feature start here */

	/**
	 * This function is used to give.
	 *
	 * @param int    $comment_ids comment id.
	 * @param string $comment_approved status.
	 * @return void
	 */
	public function wps_wsfw_comment_amount_function( $comment_ids, $comment_approved ) {

		$user_id = get_current_user_id();
		$updated = false;
		if ( 1 === $comment_approved ) {
			$wps_wsfw_enable                         = get_option( 'wps_wsfw_enable', '' );
			$wps_wsfw_wallet_action_comment_enable   = get_option( 'wps_wsfw_wallet_action_comment_enable', '' );
			$wps_wsfw_wallet_action_comment_amount   = ! empty( get_option( 'wps_wsfw_wallet_action_comment_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_comment_amount' ) : 1;
			$wps_wsfw_wallet_action_restrict_comment = get_option( 'wps_wsfw_wallet_action_restrict_comment', '' );
			$current_currency                        = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );
			$amount = '';
			if ( 'on' === $wps_wsfw_enable && 'on' === $wps_wsfw_wallet_action_comment_enable ) {

				$walletamount           = get_user_meta( $user_id, 'wps_wallet', true );
				$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
				$wallet_user            = get_user_by( 'id', $user_id );
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
				$user_comment           = WC()->session->get( 'w1' );
				$wsfw_comment_limit     = WC()->session->get( 'w2' );
				if ( ! empty( $user_comment ) ) {
					if ( count( $user_comment ) < $wsfw_comment_limit ) {
						$wps_wsfw_comment_done = get_option( $comment_ids . '_wps_wsfw_comment_done', 'not_done' );

						if ( 'not_done' === $wps_wsfw_comment_done ) {
							$amount          = $wps_wsfw_wallet_action_comment_amount;
							$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_wsfw_wallet_action_comment_amount );
							$walletamount    += $credited_amount;
							update_user_meta( $user_id, 'wps_wallet', $walletamount );
							update_option( $comment_ids . '_wps_wsfw_comment_done', 'done' );
							$updated = true;
						}
					}
				}
			}
		}
		if ( $updated ) {
			$balance   = $current_currency . ' ' . $amount;
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
				$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
				;
				$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through product review.', 'wallet-system-for-woocommerce' );
				$to         = $wallet_user->user_email;
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
						$balance_mail = $balance;
						$user_name       = $user->first_name . ' ' . $user->last_name;
						$customer_email->trigger( $user_id, $user_name, $balance_mail, '' );
					}
				} else {

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
			}

			$transaction_type = __( 'Wallet credited through ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'comment.php?action=editcomment&c=' . $comment_ids ) . '" >#' . $comment_ids . '</a>';
			$transaction_data = array(
				'user_id'          => $user_id,
				'amount'           => $amount,
				'currency'         => $current_currency,
				'payment_method'   => 'Product review',
				'transaction_type' => htmlentities( $transaction_type ),
				'transaction_type_1' => 'credit',
				'order_id'         => $comment_ids,
				'note'             => '',
			);
			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param string $new_status new status.
	 * @param string $old_status old status.
	 * @param string $comment comment.
	 * @return void
	 */
	public function wps_wsfw_give_amount_on_comment( $new_status, $old_status, $comment ) {
		global $current_user;
		$updated = false;
		if ( $old_status != $new_status ) {
			$comment_id                              = $comment->comment_ID;
			$user_id                                 = $comment->user_id;
			$wps_wsfw_enable                         = get_option( 'wps_wsfw_enable', '' );
			$wps_wsfw_wallet_action_comment_enable   = get_option( 'wps_wsfw_wallet_action_comment_enable', '' );
			$wps_wsfw_wallet_action_comment_amount   = ! empty( get_option( 'wps_wsfw_wallet_action_comment_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_comment_amount' ) : 1;
			$current_currency                        = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );
			if ( 'approved' == $new_status ) {

				$walletamount           = get_user_meta( $user_id, 'wps_wallet', true );
				$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
				$wallet_user            = get_user_by( 'id', $user_id );
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );

				if ( 'on' === $wps_wsfw_enable && 'on' === $wps_wsfw_wallet_action_comment_enable ) {
					$wps_wsfw_comment_done = get_option( $comment_id . '_wps_wsfw_comment_done', 'not_done' );
					if ( 'not_done' === $wps_wsfw_comment_done ) {

						$amount          = $wps_wsfw_wallet_action_comment_amount;
						$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_wsfw_wallet_action_comment_amount );
						$walletamount    += $credited_amount;
						update_user_meta( $user_id, 'wps_wallet', $walletamount );
						update_option( $comment_id . '_wps_wsfw_comment_done', 'done' );
						$updated = true;
					}
				}
			}
		}
		if ( $updated ) {
			$balance   = $current_currency . ' ' . $amount;
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
				$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
				;
				$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through product review.', 'wallet-system-for-woocommerce' );
				$to         = $wallet_user->user_email;
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
						$balance_mail = $balance;
						$user_name       = $user->first_name . ' ' . $user->last_name;
						$customer_email->trigger( $user_id, $user_name, $balance_mail, '' );
					}
				} else {

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
			}

			$transaction_type = __( 'Wallet credited through ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'comment.php?action=editcomment&c=' . $comment_id ) . '" >#' . $comment_id . '</a>';
			$transaction_data = array(
				'user_id'          => $user_id,
				'amount'           => $amount,
				'currency'         => $current_currency,
				'payment_method'   => 'Product review',
				'transaction_type' => htmlentities( $transaction_type ),
				'transaction_type_1' => 'credit',
				'order_id'         => $comment_id,
				'note'             => '',
			);
			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
		}
	}

	/**
	 * Function is used for the sending the track data
	 *
	 * @param bool $override is the bool value to override tracking value.
	 * @name wsfw_wpswings_wallet_tracker_send_event
	 * @since 1.0.0
	 */
	public function wsfw_wpswings_wallet_tracker_send_event( $override = false ) {
		require_once WC()->plugin_path() . '/includes/class-wc-tracker.php';

		$last_send = get_option( 'wpswings_tracker_last_send' );
		if ( ! apply_filters( 'wpswings_tracker_send_override', $override ) ) {
			// Send a maximum of once per week by default.
			$last_send = $this->wps_wsfw_last_send_time();
			if ( $last_send && $last_send > apply_filters( 'wpswings_tracker_last_send_interval', strtotime( '-1 week' ) ) ) {
				return;
			}
		} else {
			// Make sure there is at least a 1 hour delay between override sends, we don't want duplicate calls due to double clicking links.
			$last_send = $this->wps_wsfw_last_send_time();
			if ( $last_send && $last_send > strtotime( '-1 hours' ) ) {
				return;
			}
		}
		// Update time first before sending to ensure it is set.
		update_option( 'wpswings_tracker_last_send', time() );
		$params = WC_Tracker::get_tracking_data();
		$params['extensions']['wallet_system_for_woocommerce'] = array(
			'version' => WALLET_SYSTEM_FOR_WOOCOMMERCE_VERSION,
			'site_url' => home_url(),
			'wallet_active_users' => $this->wps_wsfw_wallet_active_users_count(),
		);
		$params = apply_filters( 'wpswings_tracker_params', $params );

		$api_url = 'https://tracking.wpswings.com/wp-json/mps-route/v1/mps-testing-data/';

		$sucess = wp_safe_remote_post(
			$api_url,
			array(
				'method'      => 'POST',
				'body'        => wp_json_encode( $params ),
			)
		);
	}



	/**
	 * Wallet active users count.
	 *
	 * @return int
	 */
	public function wps_wsfw_wallet_active_users_count() {
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => 'wps_wallet',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'wps_wallet',
				'compare' => 'NOT EXISTS',

			),
		);
		$user_data = new WP_User_Query( $args );
		$user_data = $user_data->get_results();
		$wps_wallet = array();
		if ( ! empty( $user_data ) ) {
			foreach ( $user_data as $all_user ) {
				$wps_wallet[] = get_user_meta( $all_user->ID, 'wps_wallet', true );
			}
		}
		$count = 0;
		foreach ( $wps_wallet as $key => $value ) {
			if ( $value > 0 ) {
				$count += count( $value );
			}
		}
		return $count;
	}

	/**
	 * Get the updated time.
	 *
	 * @name wps_wsfw_last_send_time
	 *
	 * @since 1.0.0
	 */
	public function wps_wsfw_last_send_time() {
		return apply_filters( 'wpswings_tracker_last_send_time', get_option( 'wpswings_tracker_last_send', false ) );
	}


	/**
	 * Add wallet for vendor module function.
	 *
	 * @param [type] $payment_mode is the payment method.
	 * @return mixed
	 */
	public function wsfw_admin_mvx_list_mxfdxfodules( $payment_mode ) {
		$payment_mode['wallet_payment'] = __( 'Wallet', 'multivendorx' );
		return $payment_mode;
	}

	/**
	 * Add status to order function
	 *
	 * @param [type] $payment_mode is the payment status.
	 * @return mixed
	 */
	public function wsfw_mvx_parent_order_to_vendor_order_statuses_to_sync( $payment_mode ) {
		$payment_mode = array( 'on-hold', 'pending', 'processing', 'cancelled', 'failed', 'completed' );
		return $payment_mode;
	}

	/**
	 * Add status to order function through multivendor
	 *
	 * @param [type] $order_id is the order id.
	 * @param [type] $old_status is the previous status.
	 * @param [type] $new_status is the new status.
	 * @return void
	 */
	public function wsfw_wsfw_commission_ordeer_status_change( $order_id, $old_status, $new_status ) {

		$parent_order_id = '';
		if ( function_exists( 'mvx_get_order' ) ) {
			$is_vendor_order = ( $order_id ) ? mvx_get_order( $order_id ) : false;
			$parent_order_id = wp_get_post_parent_id( $order_id );
			if ( ! empty( $is_vendor_order ) ) {
				if ( ! empty( $is_vendor_order->order ) ) {
					if ( ! empty( $is_vendor_order->order->parent_id ) ) {
						$parent_order_id = $is_vendor_order->order->parent_id;
					}
				}
			}
		}

		if ( $parent_order_id ) {

			if ( class_exists( 'MVX_Commission' ) ) {
				$wallet_paid = '';
				$order = wc_get_order( $order_id );
				if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
					// HPOS usage is enabled.
					$wallet_paid = $order->get_meta( '_paid_status_through_wallet', true );
				} else {
					$wallet_paid = get_post_meta( $order_id, '_paid_status_through_wallet', true );
				}
				if ( 'paid' == $wallet_paid ) {
					return;
				}

				$obj = new MVX_Commission();
				$commission_id = '';
				if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
					// HPOS usage is enabled.
					$commission_id = $order->get_meta( '_commission_id', true );
				} else {
					$commission_id = get_post_meta( $order_id, '_commission_id', true );
				}
				$commission = $obj->get_commission( $commission_id );

				$vendor = $commission->vendor;

				$commission_status = get_post_meta( $commission_id, '_paid_status', true );
				$commission_amount = get_post_meta( $commission_id, '_commission_amount', true );
				$payment_method = get_user_meta( $vendor->id, '_vendor_payment_mode', true );
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();

				update_post_meta( $commission_id, '_paid_status', 'paid' );

				if ( empty( $commission_amount ) ) {
					return;
				}

				if ( 'wallet_payment' == $payment_method || 'wallet' == $payment_method ) {
					$walletamount           = get_user_meta( $vendor->id, 'wps_wallet', true );
					$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
					$walletamount = $walletamount + $commission_amount;
					$order     = wc_get_order( $order_id );
					update_user_meta( $vendor->id, 'wps_wallet', $walletamount );
					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$order->update_meta_data( '_paid_status_through_wallet', 'paid' );
						$order->save();

					} else {
						update_post_meta( $order_id, '_paid_status_through_wallet', 'paid' );
					}

					$transaction_type = esc_html__( 'Wallet credited through Commission ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'comment.php?action=editcomment&c=' . $order_id ) . '" >#' . $order_id . '</a>';
					$transaction_data = array(
						'user_id'          => $vendor->id,
						'amount'           => $commission_amount,
						'currency'         => '',
						'payment_method'   => esc_html__(
							'Commission',
							'wallet-system-for
							-woocommerce'
						),
						'transaction_type' => htmlentities( $transaction_type ),
						'transaction_type_1' => 'credit',
						'order_id'         => '',
						'note'             => '',
					);
					$transaction_id = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
					$obj->add_commission_note( $commission_id, __( 'Commission paid to vendor through wallet', 'multivendorx' ), $vendor->id );

				}
			}
		}
	}
	/**
	 * Function to support Wallet Payment in Woocommerce Block.
	 *
	 * @return void
	 */
	public function wsp_wsfw_woocommerce_gateway_wallet_woocommerce_block_support() {

		if ( ! class_exists( 'Wallet_Credit_Payment_Gateway' ) ) {
			return;
		}

		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'includes/wcblocks/class-wc-gateway-wallet-system-payments-blocks-support.php';

			add_action(
				'woocommerce_blocks_payment_method_type_registration',
				function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
					$payment_method_registry->register( new WC_Gateway_Wallet_System_Payments_Blocks_Support() );
				}
			);
		}
	}


	/**
	 * This function is  used for tax in checkout block.
	 *
	 * @param [type] $tax_totals is the tax total of order.
	 * @param [type] $item is the order item.
	 * @return mixed
	 */
	public function wps_wsfw_woocommerce_order_get_tax_totals( $tax_totals, $item ) {

		$order_id = $item->get_id();

		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$check_wallet_thankyou = $item->get_meta( 'is_block_initiated', true );
		} else {
			$check_wallet_thankyou = get_post_meta( $order_id, 'is_block_initiated', true );
		}

		if ( 'done' == $check_wallet_thankyou ) {

			foreach ( $item->get_fees() as $item_fee ) {
				$fee_name    = $item_fee->get_name();
				$wallet_name = __( 'Via wallet', 'wallet-system-for-woocommerce' );
				$index = 0;
				if ( $wallet_name === $fee_name ) {

					foreach ( $tax_totals as $key => $value ) {

						if ( 0 == $index ) {

							$value->amount = $item->get_total_tax();
							$value->formatted_amount = wc_price( $item->get_total_tax() );
							$index++;
						}
					}
				}
			}
		}

		return $tax_totals;
	}
}
