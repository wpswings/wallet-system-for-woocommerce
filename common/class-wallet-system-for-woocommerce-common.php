<?php
/**
 * The common functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
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
 * @author     makewebbetter <webmaster@makewebbetter.com>
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
	public function mwb_wsfw_wallet_recharge_product_purchasable( $is_purchasable, $product ) {
		$product_id = get_option( 'mwb_wsfw_rechargeable_product_id', '' );
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
	public function mwb_wsfw_wallet_shortcodes() {
		add_shortcode( 'MWB_WALLET_RECHARGE', array( $this, 'mwb_wsfw_elementor_wallet_recharge' ) );
		add_shortcode( 'MWB_WALLET_TRANSFER', array( $this, 'mwb_wsfw_elementor_wallet_transfer' ) );
		add_shortcode( 'MWB_WITHDRAWAL_REQUEST', array( $this, 'mwb_wsfw_elementor_wallet_withdrawal' ) );
		add_shortcode( 'MWB_WALLET_TRANSACTIONS', array( $this, 'mwb_wsfw_elementor_wallet_transactions' ) );
	}

	/**
	 * Show wallet recharge page according to shortcode.
	 *
	 * @return string
	 */
	public function mwb_wsfw_elementor_wallet_recharge() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			$this->show_message_for_guest_user( esc_html__( 'You are not logged in, please log in first for recharging the wallet.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			include WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'elementor-widget/mwb-wsfw-elementor-wallet-recharge.php';
		}
		return ob_get_clean();
	}

	/**
	 * Show wallet transfer page according to shortcode.
	 *
	 * @return string
	 */
	public function mwb_wsfw_elementor_wallet_transfer() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			$this->show_message_for_guest_user( esc_html__( 'You are not logged in, please log in first for transferring the wallet amount.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			include WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'elementor-widget/mwb-wsfw-elementor-wallet-transfer.php';
		}
		return ob_get_clean();
	}

	/**
	 * Show wallet withdrawal page according to shortcode.
	 *
	 * @return string
	 */
	public function mwb_wsfw_elementor_wallet_withdrawal() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			$this->show_message_for_guest_user( esc_html__( 'You are not logged in, please log in first for requesting wallet withdrawal.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			include WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'elementor-widget/mwb-wsfw-elementor-wallet-withdrawal.php';
		}
		return ob_get_clean();
	}

	/**
	 * Show wallet transaction page according to shortcode.
	 *
	 * @return string
	 */
	public function mwb_wsfw_elementor_wallet_transactions() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			$this->show_message_for_guest_user( esc_html__( 'You are not logged in, please log in first to see wallet transactions.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
		} else {
			include WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'elementor-widget/mwb-wsfw-elementor-wallet-transactions.php';
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
	public function mwb_wsfw_save_wallet_public_shortcode() {
		if ( isset( $_POST['mwb_recharge_wallet'] ) && ! empty( $_POST['mwb_recharge_wallet'] ) ) {
			$nonce = ( isset( $_POST['verifynonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['verifynonce'] ) ) : '';
			if ( wp_verify_nonce( $nonce ) ) {
				unset( $_POST['mwb_recharge_wallet'] );
				if ( empty( $_POST['mwb_wallet_recharge_amount'] ) ) {
					$this->show_message_on_wallet_form_submit( esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
				} else {
					$recharge_amount = sanitize_text_field( wp_unslash( $_POST['mwb_wallet_recharge_amount'] ) );
					$recharge_amount = apply_filters( 'mwb_wsfw_convert_to_base_price', $recharge_amount );
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
		if ( isset( $_POST['mwb_withdrawal_request'] ) && ! empty( $_POST['mwb_withdrawal_request'] ) ) {
			unset( $_POST['mwb_withdrawal_request'] );
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
						if ( 'mwb_wallet_withdrawal_amount' === $key ) {
							$withdrawal_bal = apply_filters( 'mwb_wsfw_convert_to_base_price', $value );
							update_post_meta( $withdrawal_id, $key, $withdrawal_bal );
						} else {
							update_post_meta( $withdrawal_id, $key, $value );
						}
					}
				}
				update_user_meta( $user_id, 'disable_further_withdrawal_request', true );
				$http_host   = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
				$request_url = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
				$current_url = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $http_host . $request_url;
				wp_safe_redirect( $current_url );
				exit();
			}
		}
		if ( isset( $_POST['mwb_proceed_transfer'] ) && ! empty( $_POST['mwb_proceed_transfer'] ) ) {
			unset( $_POST['mwb_proceed_transfer'] );
			$current_currency = apply_filters( 'mwb_wsfw_get_current_currency', get_woocommerce_currency() );
			$update           = true;
			// check whether $_POST key 'current_user_id' is empty or not.
			if ( ! empty( $_POST['current_user_id'] ) ) {
				$user_id = sanitize_text_field( wp_unslash( $_POST['current_user_id'] ) );
			}
			$wallet_bal             = get_user_meta( $user_id, 'mwb_wallet', true );
			$wallet_bal             = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
			$another_user_email     = ! empty( $_POST['mwb_wallet_transfer_user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_wallet_transfer_user_email'] ) ) : '';
			$transfer_note          = ! empty( $_POST['mwb_wallet_transfer_note'] ) ? sanitize_text_field( wp_unslash( $_POST['mwb_wallet_transfer_note'] ) ) : '';
			$user                   = get_user_by( 'email', $another_user_email );
			$transfer_amount        = sanitize_text_field( wp_unslash( $_POST['mwb_wallet_transfer_amount'] ) );
			$wallet_transfer_amount = apply_filters( 'mwb_wsfw_convert_to_base_price', $transfer_amount );
			if ( $user ) {
				$another_user_id = $user->ID;
			} else {
				$invitation_link = apply_filters( 'wsfw_add_invitation_link_message', '' );
				if ( ! empty( $invitation_link ) ) {
					global $wp_session;
					$wp_session['mwb_wallet_transfer_user_email'] = $another_user_email;
					$wp_session['mwb_wallet_transfer_amount']     = $wallet_transfer_amount;
				}
				$this->show_message_on_wallet_form_submit( esc_html__( 'Email Id does not exist. ', 'wallet-system-for-woocommerce' ) . $invitation_link, 'woocommerce-error' );
				$update = false;
			}
			if ( empty( $_POST['mwb_wallet_transfer_amount'] ) ) {
				$this->show_message_on_wallet_form_submit( esc_html__( 'Please enter amount greater than 0', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
				$update = false;
			} elseif ( $wallet_bal < $wallet_transfer_amount ) {
				$this->show_message_on_wallet_form_submit( esc_html__( 'Please enter amount less than or equal to wallet balance', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
				$update = false;
			}
			if ( $update ) {
				$user_wallet_bal  = get_user_meta( $another_user_id, 'mwb_wallet', true );
				$user_wallet_bal  = ( ! empty( $user_wallet_bal ) ) ? $user_wallet_bal : 0;
				$user_wallet_bal += $wallet_transfer_amount;
				$returnid         = update_user_meta( $another_user_id, 'mwb_wallet', $user_wallet_bal );	
				if ( $returnid ) {
					$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
					$send_email_enable      = get_option( 'mwb_wsfw_enable_email_notification_for_wallet_update', '' );
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						// first user.
						$user1 = get_user_by( 'id', $another_user_id );
						$name1 = $user1->first_name . ' ' . $user1->last_name;

						$user2 = get_user_by( 'id', $user_id );
						$name2 = $user2->first_name . ' ' . $user2->last_name;

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
					$transaction_type     = __( 'Wallet credited by user #', 'wallet-system-for-woocommerce' ) . $user_id . __( ' to user #', 'wallet-system-for-woocommerce' ) . $another_user_id;
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
					$update_user = update_user_meta( $user_id, 'mwb_wallet', abs( $wallet_bal ) );
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
						$transaction_type = __( 'Wallet debited from user #', 'wallet-system-for-woocommerce' ) . $user_id . __( ' wallet, transferred to user #', 'wallet-system-for-woocommerce' ) . $another_user_id;
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

}
