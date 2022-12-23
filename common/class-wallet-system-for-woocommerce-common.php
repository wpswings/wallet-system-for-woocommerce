<?php
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
	 * Register the stylesheets for the common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_common_enqueue_styles() {
		wp_enqueue_style( $this->plugin_name . 'common', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'common/src/scss/wallet-system-for-woocommerce-common.css', array(), $this->version, 'all' );
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
		if ( isset( $_POST['wps_recharge_wallet'] ) && ! empty( $_POST['wps_recharge_wallet'] ) ) {
			$nonce = ( isset( $_POST['verifynonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['verifynonce'] ) ) : '';
			if ( wp_verify_nonce( $nonce ) ) {
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
			} else {
				$this->show_message_on_wallet_form_submit( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
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
						$this->show_message_on_wallet_form_submit( esc_html__( 'Amount is transferred successfully', 'wallet-system-for-woocommerce' ), 'woocommerce-message' );
					} else {
						$this->show_message_on_wallet_form_submit( esc_html__( 'Amount is not transferred', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
					}
				} else {
					$this->show_message_on_wallet_form_submit( esc_html__( 'No user found.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
				}
			}
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
		if ( 'on' != get_option( 'wps_wsfw_enable_cashback' ) ) {
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
			$order_currency         = $order->get_currency();
			$walletamount           = get_user_meta( $userid, 'wps_wallet', true );
			$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
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
			$wsfw_cashbak_amount     = ! empty( get_option( 'wps_wsfw_cashback_amount' ) ) ? get_option( 'wps_wsfw_cashback_amount' ) : 10;

			$order_subtotal       = $order->get_subtotal();
			$wsfw_min_cart_amount = ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : 10;
			if ( floatval( $order_subtotal ) < floatval( $wsfw_min_cart_amount ) ) {
				return;
			}

			if ( ! empty( $cashback_process ) && in_array( $new_status, $cashback_process ) ) {

				if ( ! empty( $order_items ) ) {
					foreach ( $order_items as $item_id => $item ) {
						$product_id = $item->get_product_id();
						if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {
							$allow_refund = false;
						} else {
							$allow_refund = true;
						}
					}
				}

				if ( $allow_refund ) {
					$wps_cash_back_provided = get_post_meta( $order_id, 'wps_cash_back_provided', true );
					$wps_wsfw_cashback_rule = get_option( 'wps_wsfw_cashback_rule', '' );

					if ( ! isset( $wps_cash_back_provided ) || empty( $wps_cash_back_provided ) ) {
						if ( 'cartwise' === $wps_wsfw_cashback_rule ) {
							if ( $order_total > 0 ) {
								$cashback_amount_order = $this->wsfw_get_calculated_cashback_amount( $order_total, $product_id, 1 );
								if ( $cashback_amount_order > 0 ) {
									$credited_amount     = apply_filters( 'wps_wsfw_convert_to_base_price', $cashback_amount_order );
									$walletamount       += $credited_amount;
									update_user_meta( $userid, 'wps_wallet', $walletamount );
									update_post_meta( $order_id, 'wps_cashback_receive_amount', $credited_amount );
									update_post_meta( $order_id, 'wps_cash_back_provided', 'done' );
									$wps_send_mail = true;
								}
							}
						} else {
							if ( ! empty( $order_items ) ) {
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
									update_user_meta( $userid, 'wps_wallet', $walletamount );
									update_post_meta( $order_id, 'wps_cashback_receive_amount', $credited_amount );
									update_post_meta( $order_id, 'wps_cash_back_provided', 'done' );
									$wps_send_mail = true;
								}
							}
						}
					}
					if ( $wps_send_mail ) {
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
							$mail_text  = sprintf( 'Hello %s,<br/>', $user_name );
							$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $cashback_amount_order, array( 'currency' => $order->get_currency() ) ) . __( ' through cashback.', 'wallet-system-for-woocommerce' );
							$to         = $wallet_user->user_email;
							$from       = get_option( 'admin_email' );
							$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
							$headers    = 'MIME-Version: 1.0' . "\r\n";
							$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
							$headers   .= 'From: ' . $from . "\r\n" .
								'Reply-To: ' . $to . "\r\n";
							$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
						}
						$transaction_type = __( 'Wallet credited through cashback ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
						$transaction_data = array(
							'user_id'          => $userid,
							'amount'           => $cashback_amount_order,
							'currency'         => $order->get_currency(),
							'payment_method'   => $payment_method,
							'transaction_type' => htmlentities( $transaction_type ),
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
					$wps_cashback_receive_amount = get_post_meta( $order_id, 'wps_cashback_receive_amount', true );
					$updated                     = false;

					if ( $wps_cashback_receive_amount > 0 ) {
						$wps_cash_back_refunded = get_post_meta( $order_id, 'wps_cash_back_refunded', true );
						if ( ! isset( $wps_cash_back_refunded ) || empty( $wps_cash_back_refunded ) ) {
							$walletamount        = get_user_meta( $userid, 'wps_wallet', true );
							$walletamount        = empty( $walletamount ) ? 0 : $walletamount;
							$wps_cashback_amount = $walletamount - $wps_cashback_receive_amount;
							$debited_amount      = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_cashback_amount );
							update_user_meta( $userid, 'wps_wallet', $debited_amount );
							update_post_meta( $order_id, 'wps_cash_back_refunded', 'done' );
							$updated = true;
						}
					}

					if ( $updated ) {
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
							$mail_text  = sprintf( 'Hello %s,<br/>', $user_name );
							$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . wc_price( $wps_cashback_receive_amount, array( 'currency' => $order->get_currency() ) ) . __( ' through order refunded.', 'wallet-system-for-woocommerce' );
							$to         = $wallet_user->user_email;
							$from       = get_option( 'admin_email' );
							$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
							$headers    = 'MIME-Version: 1.0' . "\r\n";
							$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
							$headers   .= 'From: ' . $from . "\r\n" .
								'Reply-To: ' . $to . "\r\n";
							$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
						}
						$transaction_type = __( 'Wallet debited through ', 'wallet-system-for-woocommerce' ) . $new_status . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
						$transaction_data = array(
							'user_id'          => $userid,
							'amount'           => $wps_cashback_receive_amount,
							'currency'         => $order->get_currency(),
							'payment_method'   => $payment_method,
							'transaction_type' => htmlentities( $transaction_type ),
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
					$wps_cashback_receive_amount = get_post_meta( $order_id, 'wps_cashback_receive_amount', true );
					$updated                     = false;

					if ( $wps_cashback_receive_amount > 0 ) {
						$wps_cash_back_refunded = get_post_meta( $order_id, 'wps_cash_back_cancelled', true );
						if ( ! isset( $wps_cash_back_refunded ) || empty( $wps_cash_back_refunded ) ) {
							$walletamount        = get_user_meta( $userid, 'wps_wallet', true );
							$walletamount        = empty( $walletamount ) ? 0 : $walletamount;
							$wps_cashback_amount = $walletamount - $wps_cashback_receive_amount;
							$debited_amount      = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_cashback_amount );
							update_user_meta( $userid, 'wps_wallet', $debited_amount );
							update_post_meta( $order_id, 'wps_cash_back_cancelled', 'done' );
							$updated = true;
						}
					}

					if ( $updated ) {
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
							$mail_text  = sprintf( 'Hello %s,<br/>', $user_name );
							$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . wc_price( $wps_cashback_receive_amount, array( 'currency' => $order->get_currency() ) ) . __( ' through order cancelled.', 'wallet-system-for-woocommerce' );
							$to         = $wallet_user->user_email;
							$from       = get_option( 'admin_email' );
							$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
							$headers    = 'MIME-Version: 1.0' . "\r\n";
							$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
							$headers   .= 'From: ' . $from . "\r\n" .
								'Reply-To: ' . $to . "\r\n";
							$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
						}
						$transaction_type = __( 'Wallet debited through ', 'wallet-system-for-woocommerce' ) . $new_status . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
						$transaction_data = array(
							'user_id'          => $userid,
							'amount'           => $wps_cashback_receive_amount,
							'currency'         => $order->get_currency(),
							'payment_method'   => $payment_method,
							'transaction_type' => htmlentities( $transaction_type ),
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
		$wsfw_max_cashbak_amount = ! empty( get_option( 'wps_wsfw_cashback_amount_max' ) ) ? get_option( 'wps_wsfw_cashback_amount_max' ) : 20;
		$wsfw_cashbak_amount     = ! empty( get_option( 'wps_wsfw_cashback_amount' ) ) ? get_option( 'wps_wsfw_cashback_amount' ) : 10;
		$wsfw_cashbak_type       = get_option( 'wps_wsfw_cashback_type' );
		$wsfw_min_cart_amount    = ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : 10;
		$wps_wsfw_cashback_rule  = get_option( 'wps_wsfw_cashback_rule', '' );

		if ( 'cartwise' === $wps_wsfw_cashback_rule ) {
			if ( $order_total > $wsfw_min_cart_amount ) {

				if ( 'percent' === $wsfw_cashbak_type ) {
					
					$total                        = $order_total;
					$total                        = apply_filters( 'wps_wsfw_wallet_calculate_cashback_on_total_amount_order_atatus', $order_total );
					$wsfw_percent_cashback_amount = $total * ( $wsfw_cashbak_amount / 100 );

					if ( $wsfw_percent_cashback_amount <= $wsfw_max_cashbak_amount ) {
						$cashback_amount += $wsfw_percent_cashback_amount;
					} else {
						$cashback_amount += $wsfw_max_cashbak_amount;
					}
				} else {
					if ( $wsfw_cashbak_amount > 0 ) {
						$cashback_amount += $wsfw_cashbak_amount;
					}
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
						if ( $wps_wsfwp_cashback_amount <= $wsfw_max_cashbak_amount ) {
							$cashback_amount += $wps_wsfwp_cashback_amount;
						} else {
							$cashback_amount += $wsfw_max_cashbak_amount;
						}
					} else{
						$cashback_amount += $wsfw_percent_cashback_amount;
					}
				
					
				} else {
					if ( $wps_wsfwp_cashback_amount > 0 && ! ( is_array( $wps_wsfwp_cashback_amount ) ) ) {
						$cashback_amount += $wps_wsfwp_cashback_amount;
					} else if ( $wsfw_cashbak_amount > 0 ) {
						$cashback_amount += $wsfw_cashbak_amount;
					}
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

			$max_id = $terms[0]->term_id;
			$max_value = get_term_meta( $terms[0]->term_id, '_wps_wsfwp_category_rule', true );
			foreach ( $terms as $key => $value ) {
				$temp = get_term_meta( $value->term_id, '_wps_wsfwp_category_rule', true );
				if ( $max_value < $temp ) {
					$max_value = $temp;
					$max_id = $value->term_id;
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

			if ( 'on' === $wps_wsfw_enable && 'on' === $wps_wsfw_wallet_action_comment_enable ) {

				$walletamount           = get_user_meta( $user_id, 'wps_wallet', true );
				$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
				$wallet_user            = get_user_by( 'id', $user_id );
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
				$user_comment           = WC()->session->get( 'w1' );
				$wsfw_comment_limit     = WC()->session->get( 'w2' );

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
		if ( $updated ) {
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
				$mail_text  = sprintf( 'Hello %s,<br/>', $user_name );
				$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount, array( 'currency' => $current_currency ) ) . __( ' through product review.', 'wallet-system-for-woocommerce' );
				$to         = $wallet_user->user_email;
				$from       = get_option( 'admin_email' );
				$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
				$headers    = 'MIME-Version: 1.0' . "\r\n";
				$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
				$headers   .= 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $to . "\r\n";
				$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
			}

			$transaction_type = __( 'Wallet credited through ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'comment.php?action=editcomment&c=' . $comment_ids ) . '" >#' . $comment_ids . '</a>';
			$transaction_data = array(
				'user_id'          => $user_id,
				'amount'           => $amount,
				'currency'         => $current_currency,
				'payment_method'   => 'Product review',
				'transaction_type' => htmlentities( $transaction_type ),
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
				$user_comment           = WC()->session->get( 'w1' );
				$wsfw_comment_limit     = WC()->session->get( 'w2' );

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
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
				$mail_text  = sprintf( 'Hello %s,<br/>', $user_name );
				$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount, array( 'currency' => $current_currency ) ) . __( ' through product review.', 'wallet-system-for-woocommerce' );
				$to         = $wallet_user->user_email;
				$from       = get_option( 'admin_email' );
				$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
				$headers    = 'MIME-Version: 1.0' . "\r\n";
				$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
				$headers   .= 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $to . "\r\n";
				$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
			}

			$transaction_type = __( 'Wallet credited through ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'comment.php?action=editcomment&c=' . $comment_id ) . '" >#' . $comment_id . '</a>';
			$transaction_data = array(
				'user_id'          => $user_id,
				'amount'           => $amount,
				'currency'         => $current_currency,
				'payment_method'   => 'Product review',
				'transaction_type' => htmlentities( $transaction_type ),
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

}
