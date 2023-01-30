<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * namespace wallet_system_for_woocommerce_public.
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/public
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wallet_System_For_Woocommerce_Public {

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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_public_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/src/scss/wallet-system-for-woocommerce-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wps-public-min', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/css/wps-public.min.css', array(), $this->version, 'all' );
		if ( is_account_page() ) {
			wp_enqueue_style( 'dashicons' );
		}
		global $wp_query;
		$is_endpoint = isset( $wp_query->query_vars['wps-wallet'] ) ? $wp_query->query_vars['wps-wallet'] : '';
		if ( ( ( 'wallet-transactions' === $is_endpoint || 'wallet-withdrawal' === $is_endpoint ) && is_account_page() ) || ( ( 'wallet-transactions' === $is_endpoint || 'wallet-withdrawal' === $is_endpoint ) ) ) {
			wp_enqueue_style( 'wps-datatable', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables/media/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/wallet-system-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'wsfw_public_param',
			array(
				'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
				'nonce'                     => wp_create_nonce( 'ajax-nonce' ),
				'datatable_pagination_text' => __( 'Rows per page _MENU_', 'wallet-system-for-woocommerce' ),
				'datatable_info'            => __(
					'_START_ - _END_ of _TOTAL_',
					'wallet-system-for-woocommerce'
				),
				'wsfw_ajax_error'               => __( 'An error occured!', 'wallet-system-for-woocommerce' ),
				'wsfw_amount_error'             => __( 'Enter amount greater than 0', 'wallet-system-for-woocommerce' ),
				'wsfw_partial_payment_msg'      => __( 'Amount want to use from wallet', 'wallet-system-for-woocommerce' ),
				'wsfw_apply_wallet_msg'         => __( 'Apply wallet', 'wallet-system-for-woocommerce' ),
				'wsfw_transfer_amount_error'    => __( 'Transfer amount should be less than or equal to wallet balance.', 'wallet-system-for-woocommerce' ),
				'wsfw_withdrawal_amount_error'  => __( 'Withdrawal amount should be less than or equal to wallet balance.', 'wallet-system-for-woocommerce' ),
				'wsfw_recharge_minamount_error' => __( 'Recharge amount should be greater than or equal to ', 'wallet-system-for-woocommerce' ),
				'wsfw_recharge_maxamount_error' => __( 'Recharge amount should be less than or equal to ', 'wallet-system-for-woocommerce' ),
				'wsfw_wallet_transfer'          => __( 'You cannot transfer amount to yourself.', 'wallet-system-for-woocommerce' ),
				'wsfw_unset_amount'             => __( 'Wallet Amount Removed', 'wallet-system-for-woocommerce' ),
			)
		);
		wp_enqueue_script( $this->plugin_name );
		global $wp_query;
		wp_enqueue_script( 'wps-datatable', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables/media/js/jquery.dataTables.min.js', array(), $this->version, true );
		wp_enqueue_script( 'wps-public-min', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/js/wps-public.min.js', array(), $this->version, 'all' );

		$wallet_script_option = get_option( 'wsfw_wallet_script_for_account_enabled', true );
		if ( 'on' == $wallet_script_option ) {
			wp_enqueue_script( 'wps-script-wallet', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/wallet-system-for-woocommerce-enable-link.js', array(), $this->version, true );
		}

	}


	/**
	 * Unset COD if wallet topup product in cart.
	 *
	 * @param array $available_gateways   all the available payment gateways.
	 */
	public function wps_wsfw_restrict_payment_gateway( $available_gateways ) {
		
		if ( isset( $available_gateways['wps_wcb_wallet_payment_gateway'] ) ) {

			$wps_cart_total = WC()->cart->total;
			$user_id        = get_current_user_id();
			$wallet_amount  = get_user_meta( $user_id, 'wps_wallet', true );
			$wallet_amount  = empty( $wallet_amount ) ? 0 : $wallet_amount;

			$wallet_amount  = apply_filters( 'wps_wsfw_show_converted_price', $wallet_amount );
			if ( ! empty( WC()->session ) ) {
				if ( WC()->session->__isset( 'is_wallet_partial_payment' ) ) {
					unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
				} elseif ( WC()->session->__isset( 'recharge_amount' ) ) {
					unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
					unset( $available_gateways['cod'] );
				} elseif ( isset( $wallet_amount ) && $wallet_amount >= 0 ) {
					if ( $wallet_amount < $wps_cart_total ) {
						unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
					}
				} elseif ( isset( $wallet_amount ) && $wallet_amount <= 0 ) {
					unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
				}
			}
		}
		return $available_gateways;
	}

	/**
	 * Show wallet as discount ( when wallet amount is less than cart total ) in review order table.
	 *
	 * @return void
	 */
	public function checkout_review_order_custom_field() {
		$wps_cart_total = WC()->cart->total;
		$user_id        = get_current_user_id();
		if ( $user_id ) {
			$wsfw_wallet_partial_payment_method_options = get_option( 'wsfw_wallet_partial_payment_method_options', 'manual_pay' );
			$wsfw_wallet_partial_payment_method_enable = get_option( 'wsfw_wallet_partial_payment_method_enabled', 'off' );

			if ( 'on' != $wsfw_wallet_partial_payment_method_enable ) {
				return;
			}
			$wallet_amount = get_user_meta( $user_id, 'wps_wallet', true );
			$wallet_amount = empty( $wallet_amount ) ? 0 : $wallet_amount;

			$wallet_amount = apply_filters( 'wps_wsfw_show_converted_price', $wallet_amount );
			if ( isset( $wallet_amount ) && $wallet_amount > 0 ) {
				if ( $wallet_amount < $wps_cart_total || $this->is_enable_wallet_partial_payment() ) {
					if ( ! WC()->session->__isset( 'recharge_amount' ) ) {
						?>	
					<tr class="partial_payment">
						<td><?php echo esc_html__( 'Pay by wallet (', 'wallet-system-for-woocommerce' ) . wp_kses_post( wc_price( $wallet_amount ) ) . ')'; ?></td>
						<td>
							<?php if ( 'manual_pay' === $wsfw_wallet_partial_payment_method_options ) { ?>
							<p class="form-row checkbox_field woocommerce-validated" id="partial_payment_wallet_field">
								<input type="checkbox" class="input-checkbox " name="partial_payment_wallet" id="partial_payment_wallet" value="enable" <?php checked( $this->is_enable_wallet_partial_payment(), true, true ); ?> data-walletamount="<?php echo esc_attr( $wallet_amount ); ?>" >
							</p>
								<?php
							} elseif ( 'total_pay' === $wsfw_wallet_partial_payment_method_options ) {
								?>
							<p class="form-row checkbox_field woocommerce-validated" id="partial_total_payment_wallet_field">
								<input type="checkbox" class="input-checkbox " name="partial_total_payment_wallet" id="partial_total_payment_wallet" value="total_enable" <?php checked( $this->is_enable_wallet_partial_payment(), true, true ); ?> data-walletamount="<?php echo esc_attr( $wallet_amount ); ?>" >
							</p>
								<?php
							}
							?>
						</td>
					</tr>
					<tr>
						<td>
							<span id="wps_wallet_show_total_msg"></span>
						</td>
					</tr>
						<?php
					}
				} elseif ( $wallet_amount >= $wps_cart_total ) {
					$wps_has_subscription = false;

					foreach ( WC()->cart->get_cart_contents() as $key => $values ) {
						if ( function_exists( 'wps_sfw_check_product_is_subscription' ) ) {
							if ( wps_sfw_check_product_is_subscription( $values['data'] ) ) {
								$wps_has_subscription = true;
								break;
							}
						}
					}

					if ( $wps_has_subscription ) {
						?>
							
					<tr class="partial_payment">
						<td><?php echo esc_html__( 'Pay by wallet (', 'wallet-system-for-woocommerce' ) . wp_kses_post( wc_price( $wallet_amount ) ) . ')'; ?></td>
						<td>
							<?php if ( 'manual_pay' === $wsfw_wallet_partial_payment_method_options ) { ?>
							<p class="form-row checkbox_field woocommerce-validated" id="partial_payment_wallet_field">
								<input type="checkbox" class="input-checkbox " name="partial_payment_wallet" id="partial_payment_wallet" value="enable" <?php checked( $this->is_enable_wallet_partial_payment(), true, true ); ?> data-walletamount="<?php echo esc_attr( $wallet_amount ); ?>" >
							</p>
								<?php
							} elseif ( 'total_pay' === $wsfw_wallet_partial_payment_method_options ) {
								?>
							<p class="form-row checkbox_field woocommerce-validated" id="partial_total_payment_wallet_field">
								<input type="checkbox" class="input-checkbox " name="partial_total_payment_wallet" id="partial_total_payment_wallet" value="total_enable" <?php checked( $this->is_enable_wallet_partial_payment(), true, true ); ?> data-walletamount="<?php echo esc_attr( $wallet_amount ); ?>" >
							</p>
								<?php
							}
							?>
						</td>
					</tr>
					<tr>
						<td>
							<span id="wps_wallet_show_total_msg"></span>
						</td>
					</tr>
						<?php
					}
				}
			}
		}

	}

	/**
	 * Remove all session set during partial payment and wallet recharge
	 *
	 * @param int $order_id order id.
	 * @return void
	 */
	public function remove_wallet_session( $order_id ) {
		$customer_id = get_current_user_id();
		if ( $customer_id > 0 ) {
			if ( ! empty( WC()->session ) ) {
				if ( WC()->session->__isset( 'custom_fee' ) ) {
					WC()->session->__unset( 'custom_fee' );
					WC()->session->__unset( 'is_wallet_partial_payment' );
				}

				if ( WC()->session->__isset( 'recharge_amount' ) ) {
					WC()->session->__unset( 'recharge_amount' );
				}
			}
		}
	}

	/**
	 * Change wallet amount on order status change
	 *
	 * @param object $order object.
	 * @return void
	 */
	public function wps_order_status_changed( $order ) {
		$order_id               = $order->get_id();
		$userid                 = $order->get_user_id();
		$payment_method         = $order->get_payment_method();
		$new_status             = $order->get_status();
		$order_items            = $order->get_items();
		$wallet_id              = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$walletamount           = get_user_meta( $userid, 'wps_wallet', true );
		$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
		$user                   = get_user_by( 'id', $userid );
		$name                   = $user->first_name . ' ' . $user->last_name;
		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
		foreach ( $order_items as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$total      = $item->get_total();

			if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {

				if ( 'completed' == $new_status ) {
					$amount          = $total;
					$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $amount );
					$wallet_userid   = apply_filters( 'wsfw_check_order_meta_for_userid', $userid, $order_id );
					if ( $wallet_userid ) {
						$update_wallet_userid = $wallet_userid;
					} else {
						$update_wallet_userid = $userid;
					}
					$transfer_note = apply_filters( 'wsfw_check_order_meta_for_recharge_reason', $order_id, '' );
					$walletamount  = get_user_meta( $update_wallet_userid, 'wps_wallet', true );
					$walletamount  = empty( $walletamount ) ? 0 : $walletamount;
					$wallet_user   = get_user_by( 'id', $update_wallet_userid );
					$walletamount += $credited_amount;
					update_user_meta( $update_wallet_userid, 'wps_wallet', $walletamount );

					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
						$mail_text  = sprintf( 'Hello %s,<br/>', $user_name );
						$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount, array( 'currency' => $order->get_currency() ) ) . __( ' through wallet recharging.', 'wallet-system-for-woocommerce' );
						$to         = $wallet_user->user_email;
						$from       = get_option( 'admin_email' );
						$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
						$headers    = 'MIME-Version: 1.0' . "\r\n";
						$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers   .= 'From: ' . $from . "\r\n" .
							'Reply-To: ' . $to . "\r\n";
						$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );

					}
					$transaction_type = __( 'Wallet credited through purchase ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
					$transaction_data = array(
						'user_id'          => $userid,
						'amount'           => $amount,
						'currency'         => $order->get_currency(),
						'payment_method'   => $payment_method,
						'transaction_type' => htmlentities( $transaction_type ),
						'order_id'         => $order_id,
						'note'             => $transfer_note,
					);
					$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

				}
			}
		}
		foreach ( $order->get_fees() as $item_fee ) {
			$fee_name    = $item_fee->get_name();
			$fee_total   = $item_fee->get_total();
			$wallet_name = __( 'Via wallet', 'wallet-system-for-woocommerce' );
			if ( $wallet_name === $fee_name ) {
				$payment_status = array( 'processing', 'completed' );
				if ( in_array( $new_status, $payment_status ) ) {
					$fees   = abs( $fee_total );
					$amount = $fees;
					$debited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $fees );
					if ( $walletamount < $debited_amount ) {
						$walletamount = 0;
					} else {
						$walletamount -= $debited_amount;
					}
					update_user_meta( $userid, 'wps_wallet', $walletamount );
					update_post_meta( $order_id, 'wps_wallet_update_on_thankyou', 'done' );

					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
						$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount, array( 'currency' => $order->get_currency() ) ) . __( ' from your wallet through purchasing.', 'wallet-system-for-woocommerce' );
						$to         = $user->user_email;
						$from       = get_option( 'admin_email' );
						$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
						$headers    = 'MIME-Version: 1.0' . "\r\n";
						$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers   .= 'From: ' . $from . "\r\n" .
							'Reply-To: ' . $to . "\r\n";
						$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );

					}

					$transaction_type = __( 'Wallet debited through purchasing ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>' . __( ' as discount', 'wallet-system-for-woocommerce' );

					$transaction_data = array(
						'user_id'          => $userid,
						'amount'           => $amount,
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

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items    All the items of the my account page.
	 */
	public function wps_wsfw_add_wallet_item( $items ) {
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		$items['wps-wallet']      = __( 'Wallet', 'wallet-system-for-woocommerce' );
		$items['customer-logout'] = $logout;
		return $items;
	}

	/**
	 *  Register new endpoint to use for My Account page.
	 */
	public function wps_wsfw_wallet_register_endpoint() {
		global $wp_rewrite;
		add_rewrite_endpoint( 'wps-wallet', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'wallet-topup', EP_PERMALINK | EP_PAGES );
		add_rewrite_endpoint( 'wallet-transfer', EP_PERMALINK | EP_PAGES );
		add_rewrite_endpoint( 'wallet-withdrawal', EP_PERMALINK | EP_PAGES );
		add_rewrite_endpoint( 'wallet-transactions', EP_PERMALINK | EP_PAGES );
		do_action( 'wps_wsfw_add_wallet_register_endpoint' );
		$wp_rewrite->flush_rules();

		add_shortcode( 'wps-wallet', array( $this, 'wps_wsfw_show_wallet' ) );

	}

	/**
	 *  Add new query var.
	 *
	 * @param array $vars    Query variable.
	 */
	public function wps_wsfw_wallet_query_var( $vars ) {
		$vars[] = 'wps-wallet';
		return $vars;
	}

	/**
	 * Add content to the new endpoint.
	 */
	public function wps_wsfw_display_wallet_endpoint_content() {
		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-public-display.php';
	}

	/**
	 * Show the wallet through shortcode.
	 */
	public function wps_wsfw_show_wallet() {
		ob_start();
		if ( ! is_user_logged_in() ) {
			echo '<div class="woocommerce">';
			wc_get_template( 'myaccount/form-login.php' );
			echo '</div>';
		} else {
			include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-shortcode.php';
		}
		return ob_get_clean();
	}

	/**
	 * Get WooCommerce cart total.
	 *
	 * @return number
	 */
	public function get_wpswallet_cart_total() {
		$wps_cart_total = WC()->cart->total;
		return $wps_cart_total;
	}

	/**
	 * Check if enable partial payment.
	 *
	 * @return Boolean
	 */
	public function is_enable_wallet_partial_payment() {
		$is_enable = false;
		if ( is_user_logged_in() && ( ( ! is_null( wc()->session ) && wc()->session->get( 'is_wallet_partial_payment', false ) ) ) ) {
			$is_enable = true;
		}
		return $is_enable;
	}

	/**
	 * Add wallet amount as fee in cart during partial payment
	 *
	 * @return void
	 */
	public function wsfw_add_wallet_discount() {

		if ( WC()->session->__isset( 'custom_fee' ) ) {

			$discount = (float) WC()->session->get( 'custom_fee' );
			if ( $discount ) {
				$fee = array(
					'id'     => 'via_wallet_partial_payment',
					'name'   => __( 'Via wallet', 'wallet-system-for-woocommerce' ),
					'amount' => (float) -1 * $discount,
				);
			}
		}

		if ( $this->is_enable_wallet_partial_payment() ) {
			if ( ! empty( $fee ) ) {
				wc()->cart->fees_api()->add_fee( $fee );
			}
		} else {
			$all_fees = wc()->cart->fees_api()->get_fees();
			if ( ! isset( $all_fees['via_wallet_partial_payment'] ) ) {
				unset( $all_fees['via_wallet_partial_payment'] );
				wc()->cart->fees_api()->set_fees( $all_fees );
			}
		}

	}

	/**
	 * Add wallet topup to cart
	 *
	 * @return void
	 */
	public function add_wallet_recharge_to_cart() {
		if ( WC()->session->__isset( 'wallet_recharge' ) ) {
			$wallet_recharge = WC()->session->get( 'wallet_recharge' );
			// check if product already in cart.
			if ( count( WC()->cart->get_cart() ) > 0 ) {
				$found = false;
				foreach ( WC()->cart->get_cart() as $cart_item ) {
					$product_in_cart = $cart_item['product_id'];
					if ( $product_in_cart == $wallet_recharge['productid'] ) {
						$found = true;
					}
				}
				// if product not found, add it.
				if ( ! $found ) {
					add_action( 'woocommerce_before_cart', array( $this, 'add_cart_custom_notice' ) );
					WC()->session->__unset( 'recharge_amount' );
				}
			} else {
				WC()->cart->empty_cart();
				// if no products in cart, add it.
				WC()->cart->add_to_cart( $wallet_recharge['productid'] );

				wp_safe_redirect( wc_get_checkout_url() );

			}
			WC()->session->__unset( 'wallet_recharge' );
		}
	}

	/**
	 * Add credit amount to cart data.
	 *
	 * @param array $cart_item_data  cart data.
	 * @param int   $product_id prduct id in cart.
	 */
	public function add_wallet_topup_product_in_cart( $cart_item_data, $product_id ) {
		if ( WC()->session->__isset( 'recharge_amount' ) ) {
			$wallet_recharge = WC()->session->get( 'recharge_amount' );
			if ( isset( $wallet_recharge ) && ! empty( $wallet_recharge ) ) {
				$cart_item_data['recharge_amount'] = $wallet_recharge;
			}
		}
		return $cart_item_data;
	}

	/**
	 * Add notice on cart page if cart is already added with products
	 *
	 * @return void
	 */
	public function add_cart_custom_notice() {
		wc_print_notice(
			sprintf(
				'<span class="subscription-reminder">' .
				__( 'Sorry we cannot recharge wallet with other products, either empty cart or recharge later when cart is empty', 'wallet-system-for-woocommerce' ) . '</span>',
				__( 'empty', 'wallet-system-for-woocommerce' )
			),
			'error'
		);
	}

	/**
	 * Add notice on cart page if cart is already added with wallet topup
	 *
	 * @param boolean $passed  check product can be add to cart.
	 * @param int     $product_id  product id.
	 * @return boolean
	 */
	public function show_message_addto_cart( $passed, $product_id ) {
		$wallet_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		if ( ! empty( $wallet_id ) ) {
			if ( ! WC()->cart->is_empty() ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->get_id() == $wallet_id ) {
						$passed = false;

						wc_add_notice(
							sprintf(
								'<span class="subscription-reminder">' .
								__( 'Sorry you cannot buy this product since wallet topup is added to cart. If you want to buy this product, please first remove wallet topup from cart.', 'wallet-system-for-woocommerce' ) . '</span>',
								__( 'empty', 'wallet-system-for-woocommerce' )
							),
							'error'
						);

					}
				}
			}
		}
		return $passed;
	}
	/**
	 * Returns converted price of wallet balance.
	 *
	 * @param float $wallet_bal wallet balance.
	 * @return float
	 */
	public function wps_wsfwp_show_converted_price( $wallet_bal ) {
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;

			$amount = $WOOCS->woocs_exchange_value( $wallet_bal );
			return $amount;
		} else {
			return $wallet_bal;
		}

	}
	/**
	 * Convert the amount into base currency amount.
	 *
	 * @param string $price price.
	 * @return string
	 */
	public function wps_wsfwp_convert_to_base_price( $price ) {

		$wps_sfw_active_plugins = get_option( 'active_plugins' );
		if ( in_array( 'woocommerce-currency-switcher/index.php', $wps_sfw_active_plugins ) ) {

			if ( class_exists( 'WOOCS' ) ) {
				global $WOOCS;
				$amount = '';
				if ( $WOOCS->is_multiple_allowed ) {
					 $currrent = $WOOCS->current_currency;
					if ( $currrent != $WOOCS->default_currency ) {
						$currencies = $WOOCS->get_currencies();
						$rate = $currencies[ $currrent ]['rate'];
						$amount = $price / ( $rate );
						return $amount;
					} else {
						return $price;
					}
				}
			}
		}

		return $price;
	}

	/**
	 * Update wallet top price in cart and checkout page
	 *
	 * @param object $cart_object cart object.
	 * @return void
	 */
	public function wps_update_price_cart( $cart_object ) {

		$wallet_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );

		$wps_wsfw_wallet_action_auto_topup_enable = get_option( 'wps_wsfw_wallet_action_auto_topup_enable', '' );
		$wps_sfw_subscription_interval = get_option( 'wps_sfw_subscription_interval', '' );
		$wps_wsfw_subscriptions_per_interval = get_option( 'wps_wsfw_subscriptions_per_interval', '' );
		$wps_sfw_subscription_expiry_interval = get_option( 'wps_sfw_subscription_expiry_interval', '' );
		$wps_wsfw_subscriptions_expiry_per_interval = get_option( 'wps_wsfw_subscriptions_expiry_per_interval', '' );

		$cart_items = $cart_object->cart_contents;
		if ( WC()->session->__isset( 'recharge_amount' ) ) {
			$wallet_recharge = WC()->session->get( 'recharge_amount' );
			$price           = $wallet_recharge;

			if ( ! empty( $cart_items ) ) {
				foreach ( $cart_items as $key => $value ) {

					if ( $value['product_id'] == $wallet_id ) {

						if ( ! empty( $wps_wsfw_wallet_action_auto_topup_enable ) && 'on' == $wps_wsfw_wallet_action_auto_topup_enable ) {
							$is_user_subscription = false;
							$is_user_subscription = apply_filters( 'wps_wsfw_get_user_choice_of_subscription', $is_user_subscription );

							if ( $is_user_subscription ) {
								$user_id = get_current_user_id();
								$user_choice = get_user_meta( $user_id, 'wps_wallet_recharge_as_subscription', true );

								if ( 'yes' == $user_choice ) {
									update_post_meta( $wallet_id, '_wps_sfw_product', 'yes' );

									update_post_meta( $wallet_id, 'wps_sfw_subscription_number', intval( $wps_wsfw_subscriptions_per_interval ) );
									update_post_meta( $wallet_id, 'wps_sfw_subscription_interval', $wps_sfw_subscription_interval );

									update_post_meta( $wallet_id, 'wps_sfw_subscription_expiry_number', intval( $wps_wsfw_subscriptions_expiry_per_interval ) );
									update_post_meta( $wallet_id, 'wps_sfw_subscription_expiry_interval', $wps_sfw_subscription_expiry_interval );
									update_post_meta( $wallet_id, '_regular_price', $price );

								} else {

									update_post_meta( $wallet_id, '_wps_sfw_product', 'off' );

									update_post_meta( $wallet_id, 'wps_sfw_subscription_number', '' );
									update_post_meta( $wallet_id, 'wps_sfw_subscription_interval', '' );

									update_post_meta( $wallet_id, 'wps_sfw_subscription_expiry_number', '' );
									update_post_meta( $wallet_id, 'wps_sfw_subscription_expiry_interval', '' );
									update_post_meta( $wallet_id, '_regular_price', '' );

								}
							} else {
								update_post_meta( $wallet_id, '_wps_sfw_product', 'yes' );

								update_post_meta( $wallet_id, 'wps_sfw_subscription_number', intval( $wps_wsfw_subscriptions_per_interval ) );
								update_post_meta( $wallet_id, 'wps_sfw_subscription_interval', $wps_sfw_subscription_interval );

								update_post_meta( $wallet_id, 'wps_sfw_subscription_expiry_number', intval( $wps_wsfw_subscriptions_expiry_per_interval ) );
								update_post_meta( $wallet_id, 'wps_sfw_subscription_expiry_interval', $wps_sfw_subscription_expiry_interval );
								update_post_meta( $wallet_id, '_regular_price', $price );

							}
						} else {

							update_post_meta( $wallet_id, '_wps_sfw_product', 'off' );

							update_post_meta( $wallet_id, 'wps_sfw_subscription_number', '' );
							update_post_meta( $wallet_id, 'wps_sfw_subscription_interval', '' );

							update_post_meta( $wallet_id, 'wps_sfw_subscription_expiry_number', '' );
							update_post_meta( $wallet_id, 'wps_sfw_subscription_expiry_interval', '' );
							update_post_meta( $wallet_id, '_regular_price', '' );
						}
					}

					$value['data']->set_price( $price );
				}
			}
		}

	}

	/**
	 * Unset session after wallet topup is removed from cart
	 *
	 * @param string $removed_cart_item_key removed cart item key.
	 * @param object $cart cart object.
	 * @return void
	 */
	public function after_remove_wallet_from_cart( $removed_cart_item_key, $cart ) {
		$line_item  = $cart->removed_cart_contents[ $removed_cart_item_key ];
		$product_id = $line_item['product_id'];
		$wallet_id  = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		if ( $wallet_id ) {
			if ( $product_id == $wallet_id ) {
				WC()->session->__unset( 'recharge_amount' );
			}
		}
		if ( WC()->session->__isset( 'custom_fee' ) ) {
			WC()->session->__unset( 'custom_fee' );
			WC()->session->__unset( 'is_wallet_partial_payment' );
		}
		do_action( 'wps_wsfw_remove_value_from_session', $removed_cart_item_key );

	}



	/**
	 * Change post type to wallet_shop_order if wallet is recharge during new order place
	 *
	 * @param int $order_id order id.
	 * @return void
	 */
	public function change_order_type( $order_id ) {
		$order     = wc_get_order( $order_id );
		$wallet_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		foreach ( $order->get_items() as $item ) {
			$product_id = $item->get_product_id();
			if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {
				$order_obj            = get_post( $order_id );
				$order_obj->post_type = 'wallet_shop_order';
				wp_update_post( $order_obj );

				echo '<style type="text/css">
				.woocommerce-order .woocommerce-customer-details {
					display:none;
				}
				</style>';

			}
		}
		$check_wallet_thankyou = get_post_meta( $order_id, 'wps_wallet_update_on_thankyou', true );
		if ( 'done' != $check_wallet_thankyou ) {
			$this->wps_order_status_changed( $order );
		}
	}

	/**
	 * Remove billing fields from checkout page for wallet recharge.
	 *
	 * @param array $fields checkout fields.
	 * @return array
	 */
	public function wps_wsfw_remove_billing_from_checkout( $fields ) {
		$wallet_product_id = get_option( 'wps_wsfw_rechargeable_product_id' );
		$only_virtual      = false;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = $cart_item['data'];
			if ( $_product->is_virtual() && ( $_product->get_id() == $wallet_product_id ) ) {
				$only_virtual = true;
			}
		}
		if ( $only_virtual ) {
			unset( $fields['billing']['billing_first_name'] );
			unset( $fields['billing']['billing_last_name'] );
			unset( $fields['billing']['billing_address_1'] );
			unset( $fields['billing']['billing_address_2'] );
			unset( $fields['billing']['billing_city'] );
			unset( $fields['billing']['billing_postcode'] );
			unset( $fields['billing']['billing_country'] );
			unset( $fields['billing']['billing_state'] );
			unset( $fields['billing']['billing_company'] );
			unset( $fields['billing']['billing_phone'] );
			unset( $fields['billing']['billing_email'] );
			add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
			echo '<style type="text/css">
			form.checkout .woocommerce-billing-fields h3 {
				display:none;
			}
			</style>';
		}
		return $fields;

	}

	/**
	 * Remove customer details from mail for wallet recharge.
	 *
	 * @param object $order order object.
	 * @return void
	 */
	public function wps_wsfw_remove_customer_details_in_emails( $order ) {
		$wallet_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		foreach ( $order->get_items() as $item ) {
			$product_id = $item->get_product_id();
			if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {
				$mailer = WC()->mailer();
				remove_action( 'woocommerce_email_customer_details', array( $mailer, 'customer_details' ), 10 );
				remove_action( 'woocommerce_email_customer_details', array( $mailer, 'email_addresses' ), 20 );

			}
		}

	}

	/**
	 * This function is used to show cashback notic on cart page.
	 *
	 * @return int
	 */
	public function wsfw_calculate_cashback_cart() {
		$cashback_amount         = 0;
		$cashback_amount_order   = 0;
		$wsfw_max_cashbak_amount = ! empty( get_option( 'wps_wsfw_cashback_amount_max' ) ) ? get_option( 'wps_wsfw_cashback_amount_max' ) : 20;
		$wsfw_cashbak_amount     = ! empty( get_option( 'wps_wsfw_cashback_amount' ) ) ? get_option( 'wps_wsfw_cashback_amount' ) : 10;
		$wsfw_cashbak_type       = get_option( 'wps_wsfw_cashback_type' );
		$wsfw_min_cart_amount    = ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : 10;
		$wps_wsfw_cashback_rule  = get_option( 'wps_wsfw_cashback_rule', '' );
		$update                  = false;

		if ( 'catwise' === $wps_wsfw_cashback_rule ) {

			if ( count( wc()->cart->get_cart() ) > 0 ) {
				foreach ( wc()->cart->get_cart() as $key => $cart_item ) {

					$product_id = $cart_item['product_id'];
					$product    = wc_get_product( $product_id );
					$price      = $product->get_price();
					$qty        = $cart_item['quantity'];
					if ( class_exists( 'Wallet_System_For_Woocommerce_Common' ) ) {
						$common_obj   = new Wallet_System_For_Woocommerce_Common( '', '' );
						$wps_cat_wise = $common_obj->wps_get_cashback_cat_wise( $product_id );
						if ( $wps_cat_wise ) {
							$cashback_amount_order += $common_obj->wsfw_get_calculated_cashback_amount( $cart_item['line_subtotal'], $product_id, $qty );
							$update = true;
						}
					}
				}
				if ( $update ) {
					$cashback_amount += $cashback_amount_order;
					if ( 'percent' === $wsfw_cashbak_type ) {
						if ( $cashback_amount <= $wsfw_max_cashbak_amount ) {
							$cashback_amount = $cashback_amount;
						} else {
							$cashback_amount = $wsfw_max_cashbak_amount;
						}
					} else {
						$cashback_amount = $cashback_amount_order;
					}
				}
			}
		} else {
			if ( wc()->cart->get_total( 'edit' ) > $wsfw_min_cart_amount ) {

				if ( 'percent' === $wsfw_cashbak_type ) {

					 $total                        = wc()->cart->get_total( 'edit' );
					$total                        = apply_filters( 'wps_wsfw_wallet_calculate_cashback_on_total_amount_order_atatus', wc()->cart->get_total( 'edit' ) );
					$wsfw_percent_cashback_amount = $total * ( $wsfw_cashbak_amount / 100 );

					if ( $wsfw_percent_cashback_amount < $wsfw_max_cashbak_amount ) {
						$cashback_amount += $wsfw_percent_cashback_amount;
					} else {
						$cashback_amount += $wsfw_max_cashbak_amount;
					}
				} else {
					if ( ! empty( wc()->cart->get_total( 'edit' ) ) ) {

						$cashback_amount += $wsfw_cashbak_amount;
					}
				}
			}
		}
		$cashback_amount = $this->wps_wsfwp_show_converted_price( $cashback_amount );
		return apply_filters( 'wps_wsfw_wallet_form_cart_cashback_amount', $cashback_amount );
	}

	/**
	 * This function is used to show price tag.
	 *
	 * @param string $user_id user id.
	 * @return string
	 */
	public function wsfw_wallet_price_args( $user_id = '' ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$args = apply_filters(
			'wsfw_wallet_price_args',
			array(
				'ex_tax_label'       => false,
				'currency'           => '',
				'decimal_separator'  => wc_get_price_decimal_separator(),
				'thousand_separator' => wc_get_price_thousand_separator(),
				'decimals'           => wc_get_price_decimals(),
				'price_format'       => get_woocommerce_price_format(),
			),
			$user_id
		);
		return $args;
	}

	/**
	 * This function is used to show cashback notice on cart page.
	 *
	 * @return void
	 */
	public function wsfw_woocommerce_before_cart_total_cashback_message() {
		if ( 'on' == get_option( 'wps_wsfw_enable_cashback' ) ) :
			$wallet_id          = get_option( 'wps_wsfw_rechargeable_product_id', '' );
			$is_wallet_recharge = false;
			if ( ! empty( WC()->cart->get_cart() ) ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$product    = $cart_item['data'];
					$product_id = $cart_item['product_id'];
					if ( $wallet_id == $product_id ) {
						$is_wallet_recharge = true;
					}
				}
			}
			if ( true == $is_wallet_recharge ) {
				return;
			}

			$cashback_amount        = $this->wsfw_calculate_cashback_cart();
			$wsfw_min_cart_amount   = ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : 10;
			$cart_total             = ! empty( wc()->cart->get_total( 'edit' ) ) ? wc()->cart->get_total( 'edit' ) : wc()->cart->get_subtotal();
			$cart_total             = apply_filters( 'wps_wsfw_wallet_cashback_on_total', $cart_total );
			$wps_wsfw_cashback_rule = get_option( 'wps_wsfw_cashback_rule', '' );

			if ( 'cartwise' === $wps_wsfw_cashback_rule ) {

				if ( floatval( $cart_total ) < floatval( $wsfw_min_cart_amount ) ) {
					?>
					<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
					<?php
					/* translators: %s: search term */
					echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Earn Cashback On Orders Above %s .', 'wallet-system-for-woocommerce' ), wc_price( $wsfw_min_cart_amount, $this->wsfw_wallet_price_args() ) ), $wsfw_min_cart_amount ) );
				} else {
					if ( is_user_logged_in() ) {
						if ( $cashback_amount > 0 ) {
							?>
							<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
							<?php
							/* translators: %s: search term */
							echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Upon placing this order a cashback of %s will be credited to your wallet.', 'wallet-system-for-woocommerce' ), wc_price( $cashback_amount, $this->wsfw_wallet_price_args() ) ), $cashback_amount ) );
						}
					} else {
						?>
						<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
						<?php
						/* translators: %s: search term */
						echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Please <a href="%1$s">log in</a> to avail %2$s cashback from this order.', 'wallet-system-for-woocommerce' ), esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ), wc_price( $cashback_amount, $this->wsfw_wallet_price_args() ) ), $cashback_amount ) );
					}
				}
			} elseif ( 'catwise' === $wps_wsfw_cashback_rule ) {
				if ( is_user_logged_in() ) {
					if ( $cashback_amount > 0 ) {
						?>
						<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
						<?php
						/* translators: %s: search term */
						echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Upon placing this order a cashback of %s will be credited to your wallet.', 'wallet-system-for-woocommerce' ), wc_price( $cashback_amount, $this->wsfw_wallet_price_args() ) ), $cashback_amount ) );
					}
				} else {
					?>
					<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
					<?php
					/* translators: %s: search term */
					echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Please <a href="%1$s">log in</a> to avail %2$s cashback from this order.', 'wallet-system-for-woocommerce' ), esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ), wc_price( $cashback_amount, $this->wsfw_wallet_price_args() ) ), $cashback_amount ) );
				}
			}
			?>
				</div>
			<?php
		endif;
	}

	/**
	 * This function is used to show cashback notice on shop / single page.
	 *
	 * @return void
	 */
	public function wsfw_display_category_wise_cashback_price_on_shop_page() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		if ( 'on' !== get_option( 'wps_wsfw_enable_cashback', '' ) ) {
			return;
		}
		$product_id             = get_the_ID();
		$wps_wsfw_cashback_rule = get_option( 'wps_wsfw_cashback_rule', '' );
		$wps_wsfw_cashback_type = get_option( 'wps_wsfw_cashback_type', '' );
		$product                = wc_get_product( $product_id );
		$product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
		$wps_wsfwp_cashback_amount = apply_filters( 'wsfw_wallet_cashback_using_catwise', $product_cats_ids, $product_id, 1 );

		if ( ! $product ) {
			return;
		}
		if ( ! empty( $wps_wsfw_cashback_rule ) && 'catwise' === $wps_wsfw_cashback_rule ) {
			if ( class_exists( 'Wallet_System_For_Woocommerce_Common' ) ) {
				$common_obj   = new Wallet_System_For_Woocommerce_Common( '', '' );
				$wps_cat_wise = $common_obj->wps_get_cashback_cat_wise( get_the_ID() );
				if ( $wps_cat_wise ) {
					$price           = $product->get_price();
					$price           = apply_filters( 'wsfw_category_wise_cashback_product_price', $price );
					$cashback_amount = $this->wsfw_calculate_category_wise_cashback( $price );
					if ( is_array( $wps_wsfwp_cashback_amount ) ) {
						$wps_wsfwp_cashback_amount = $cashback_amount;
					}
					$cashback_html   = '<span class="wps-show-cashback-notice-on-shop-page">' . wc_price( $cashback_amount, $this->wsfw_wallet_price_args() ) . __( ' Cashback', 'wallet-system-for-woocommerce' ) . '</span>';
					if ( $wps_wsfwp_cashback_amount ) {
						$cashback_html   = '<span class="wps-show-cashback-notice-on-shop-page">' . wc_price( $wps_wsfwp_cashback_amount, $this->wsfw_wallet_price_args() ) . __( ' Cashback', 'wallet-system-for-woocommerce' ) . '</span>';
					}
					echo wp_kses_post( apply_filters( 'wsfw_show_category_wise_cashback_amount_on_shop_page', $cashback_html ) );
				}
			}
		}
	}

	/**
	 * This function is used to calculate category wise cashback.
	 *
	 * @param int $price price.
	 * @return int
	 */
	public function wsfw_calculate_category_wise_cashback( $price ) {
		$cashback_amount         = 0;
		$wsfw_max_cashbak_amount = ! empty( get_option( 'wps_wsfw_cashback_amount_max' ) ) ? get_option( 'wps_wsfw_cashback_amount_max' ) : 20;
		$wsfw_cashbak_amount     = ! empty( get_option( 'wps_wsfw_cashback_amount' ) ) ? get_option( 'wps_wsfw_cashback_amount' ) : 10;
		$wsfw_cashbak_type       = get_option( 'wps_wsfw_cashback_type' );
		$wps_wsfw_cashback_rule  = get_option( 'wps_wsfw_cashback_rule', '' );

		if ( 'catwise' === $wps_wsfw_cashback_rule ) {
			if ( ! empty( $price ) && $price > 0 ) {
				if ( 'percent' === $wsfw_cashbak_type ) {
					$total                        = $price;
					$total                        = apply_filters( 'wps_wsfw_wallet_calculate_cashback_on_total_amount_order_atatus', $price );
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
		}
		return $cashback_amount;
	}

	/** Comment section start here */

	/**
	 * This function is used to show comment amount on single product page.
	 *
	 * @param string $comment_data comment data.
	 * @return string
	 */
	public function wps_wsfw_show_comment_notice( $comment_data ) {
		global $current_user,$post;
		if ( ! is_user_logged_in() ) {
			return $comment_data;
		}
		$args = array(
			'user_id' => $current_user->ID,
			'post_id' => $post->ID,
		);

		$wps_wsfw_wallet_action_comment_enable      = get_option( 'wps_wsfw_wallet_action_comment_enable', '' );
		$wps_wsfw_wallet_action_comment_amount      = ! empty( get_option( 'wps_wsfw_wallet_action_comment_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_comment_amount' ) : 1;
		$wps_wsfw_wallet_action_restrict_comment    = get_option( 'wps_wsfw_wallet_action_restrict_comment', '' );
		$wps_wsfw_wallet_action_comment_description = get_option( 'wps_wsfw_wallet_action_comment_description', '' );
		$user_id                                    = get_current_user_ID();
		$user_comment                               = get_comments( $args );

		WC()->session->set( 'w1', $user_comment );
		WC()->session->set( 'w2', $wps_wsfw_wallet_action_restrict_comment );

		if ( isset( $wps_wsfw_wallet_action_comment_enable ) && 'on' === $wps_wsfw_wallet_action_comment_enable ) {
			if ( count( $user_comment ) < $wps_wsfw_wallet_action_restrict_comment ) {
				$comment_data['comment_field'] .= '<p class="wsfw_comment_section_notice">' . esc_html( $wps_wsfw_wallet_action_comment_description ) . '</p>';
			}
		}
		return $comment_data;
	}

	/** New user sinup */

	/**
	 * This functions is used to show notice on account page.
	 *
	 * @return void
	 */
	public function wps_wsfw_show_signup_notice() {
		$wps_wsfw_wallet_action_registration_enable      = get_option( 'wps_wsfw_wallet_action_registration_enable', '' );
		$wps_wsfw_wallet_action_registration_amount      = ! empty( get_option( 'wps_wsfw_wallet_action_registration_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_registration_amount' ) : 1;
		$wps_wsfw_wallet_action_registration_description = ! empty( get_option( 'wps_wsfw_wallet_action_registration_description' ) ) ? get_option( 'wps_wsfw_wallet_action_registration_description' ) : __( 'You will Get 1 Points on a successful Sign Up.', 'wallet-system-for-woocommerce' );

		if ( isset( $wps_wsfw_wallet_action_registration_enable ) && 'on' === $wps_wsfw_wallet_action_registration_enable ) {
			?>
				<div class="woocommerce-message">
					<?php
					echo wp_kses_post( $wps_wsfw_wallet_action_registration_description );
					?>
				</div>
			<?php
		}
	}

	/**
	 * This function is used to give amount on new registration.
	 *
	 * @param int $customer_id customer id.
	 * @return void
	 */
	public function wps_wsfw_new_customer_registerd( $customer_id ) {
		if ( ! empty( $customer_id ) ) {
			$wps_wsfw_wallet_action_registration_enable      = get_option( 'wps_wsfw_wallet_action_registration_enable', '' );
			$wps_wsfw_wallet_action_registration_amount      = ! empty( get_option( 'wps_wsfw_wallet_action_registration_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_registration_amount' ) : 1;
			$current_currency                                = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );
			$updated                                         = false;
			if ( isset( $wps_wsfw_wallet_action_registration_enable ) && 'on' === $wps_wsfw_wallet_action_registration_enable ) {
				$walletamount           = get_user_meta( $customer_id, 'wps_wallet', true );
				$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
				$wallet_user            = get_user_by( 'id', $customer_id );
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );

				if ( $wps_wsfw_wallet_action_registration_amount > 0 ) {
					$amount          = $wps_wsfw_wallet_action_registration_amount;
					$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_wsfw_wallet_action_registration_amount );
					$walletamount    += $credited_amount;
					update_user_meta( $customer_id, 'wps_wallet', $walletamount );
					$updated = true;
				}
			}
		}
		if ( $updated ) {
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
				$mail_text  = sprintf( 'Hello %s,<br/>', $user_name );
				$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount, array( 'currency' => $current_currency ) ) . __( ' through successfully signup.', 'wallet-system-for-woocommerce' );
				$to         = $wallet_user->user_email;
				$from       = get_option( 'admin_email' );
				$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
				$headers    = 'MIME-Version: 1.0' . "\r\n";
				$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
				$headers   .= 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $to . "\r\n";
				$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
			}

			$transaction_type = __( 'Wallet credited through signup ', 'wallet-system-for-woocommerce' );
			$transaction_data = array(
				'user_id'          => $customer_id,
				'amount'           => $amount,
				'currency'         => $current_currency,
				'payment_method'   => 'Sigup',
				'transaction_type' => htmlentities( $transaction_type ),
				'order_id'         => '',
				'note'             => '',
			);
			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
		}
	}

	/**
	 * This functions is used to give amount on daily basis.
	 *
	 * @return void
	 */
	public function wps_wsfw_daily_visit_balance() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		$user_id                                  = get_current_user_id();
		$wps_wsfw_wallet_action_daily_enable      = get_option( 'wps_wsfw_wallet_action_daily_enable' );
		$wps_wsfw_wallet_action_daily_amount      = ! empty( get_option( 'wps_wsfw_wallet_action_daily_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_daily_amount' ) : 1;
		$current_currency                         = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );
		$updated                                  = false;
		if ( get_transient( 'wps_wsfw_wallet_site_visit_' . $user_id ) ) {
			return;
		}
		if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {
			set_transient( 'wps_wsfw_wallet_site_visit_' . $user_id, true, DAY_IN_SECONDS );
		}
		if ( 'on' === $wps_wsfw_wallet_action_daily_enable ) {
			$wallet_amount          = get_user_meta( $user_id, 'wps_wallet', true );
			$wallet_amount          = empty( $wallet_amount ) ? 0 : $wallet_amount;
			$wallet_user            = get_user_by( 'id', $user_id );
			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
			$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );

			if ( $wps_wsfw_wallet_action_daily_amount > 0 ) {
				$amount          = $wps_wsfw_wallet_action_daily_amount;
				$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_wsfw_wallet_action_daily_amount );
				$wallet_amount   += $credited_amount;
				update_user_meta( $user_id, 'wps_wallet', $wallet_amount );
				$updated = true;
			}
		}
		if ( $updated ) {
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
				$mail_text  = sprintf( 'Hello %s,<br/>', $user_name );
				$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount, array( 'currency' => $current_currency ) ) . __( ' through visiting site.', 'wallet-system-for-woocommerce' );
				$to         = $wallet_user->user_email;
				$from       = get_option( 'admin_email' );
				$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
				$headers    = 'MIME-Version: 1.0' . "\r\n";
				$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
				$headers   .= 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $to . "\r\n";
				$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
			}

			$transaction_type = __( 'Wallet credited through visiting site. ', 'wallet-system-for-woocommerce' );
			$transaction_data = array(
				'user_id'          => $user_id,
				'amount'           => $amount,
				'currency'         => $current_currency,
				'payment_method'   => 'Site visit',
				'transaction_type' => htmlentities( $transaction_type ),
				'order_id'         => '',
				'note'             => '',
			);
			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
		}
	}
	/**
	 * Fee html for cart total.
	 *
	 * @param array $cart_totals_fee_html as cart html.
	 * @param array $fees as fee.
	 * @return array
	 */
	public function wsfw_wallet_cart_totals_fee_html( $cart_totals_fee_html, $fees ) {

		foreach ( $fees as $key => $fee ) {

			if ( 'Via wallet' == $fee ) {
				// gets the data to recalculate the cart total.
				$cart_totals_fee_html = $fees->amount;
				return wc_price( $cart_totals_fee_html );
				break;
			}
		}
		return wc_price( $fees );
	}
	/**
	 * Fix cart total html.
	 *
	 * @param array $cart_totals_fee_html as cart total html.
	 * @return array
	 */
	public function wsfw_wallet_get_fee_taxes( $cart_totals_fee_html ) {

		if ( is_array( $cart_totals_fee_html ) && count( $cart_totals_fee_html ) > 0 ) {

			$cart_totals_fee_html[1] = floatval( 0 );
		}

		return $cart_totals_fee_html;
	}

	/**
	 * Fix total html via wallet.
	 *
	 * @param array $cart_totals_fee_html as total html via wallet.
	 * @return array
	 */
	public function wsfw_wallet_cart_total( $cart_totals_fee_html ) {

		$cart_total     = WC()->cart->get_total( '' );

		 $fees = WC()->cart->get_fees();
		 $fee_tax = 0;
		 $fee_tax_data = array();

		foreach ( $fees as $key => $fee ) {

			if ( 'Via wallet' == $fee->name ) {
					$fee_tax      = $fee->tax;
					$fee_tax_data = $fee->tax_data;
				unset( $fees[ $key ] );
				break;
			}
		}
		if ( ! empty( $fee_tax ) ) {
			$cart_total = $cart_total - $fee_tax;
		}

		return wc_price( $cart_total );
	}

	/**
	 * Fix html via wallet at order details
	 *
	 * @param array $order as order.
	 * @return void
	 */
	public function wsfw_wallet_add_order_detail( $order ) {
		$fee_name = '';
		$fee_total = '';
		$fee_total_tax = '';
		$order_fee_array = $order->get_items( 'fee' );

		foreach ( $order_fee_array as $item_id => $item_fee ) {

			if ( $item_fee->get_name() == 'Via wallet' ) {
				$fee_name = $item_fee->get_name();

				$fee_total = $item_fee->get_total();

				$fee_total_tax = $item_fee->get_total_tax();
				$order->remove_item( $item_id );
				break;
			}
		}
		if ( ! empty( $fee_total_tax ) ) {
			$order_id = $order->get_id();
			$order_tax = get_post_meta( $order_id, '_order_tax', true );

			$order_tax = ( floatval( $order_tax ) + abs( ( $fee_total_tax ) ) );

			update_post_meta( $order_id, '_order_tax', $order_tax );

			$_order_total = get_post_meta( $order_id, '_order_total', true );

			$_order_total = ( floatval( $_order_total ) + abs( ( $fee_total_tax ) ) );

			update_post_meta( $order_id, '_order_total', $_order_total );

			$order_total = $order->get_total();
			$order_total = $order_total + abs( $fee_total_tax );
			$order->set_total( $order_total );

			$item_fee = new WC_Order_Item_Fee();

			$item_fee->set_name( $fee_name );
			$item_fee->set_amount( $fee_total );
			$item_fee->set_tax_class( '' );
			$item_fee->set_tax_status( '' );
			$item_fee->set_total( $fee_total );
			$item_fee->set_total_tax( 0 );

			// Add Fee item to the order.
			$order->add_item( $item_fee );
			$order->save();
		}

	}

	/**
	 * Listing of wallet order in subscription order list
	 *
	 * @param bool $wps_wsfw_is_order is a wallet order or not.
	 * @param int  $parent_order_id is parent order id of subscription.
	 * @return bool
	 */
	public function wps_wsfw_check_parent_order_for_subscription_listing( $wps_wsfw_is_order, $parent_order_id ) {
		if ( ! empty( $parent_order_id ) ) {
			$check_order = get_post_type( $parent_order_id );
			if ( 'wallet_shop_order' == $check_order ) {
					$wps_wsfw_is_order = true;
			} else {
					$wps_wsfw_is_order = false;
			}
		}
		return $wps_wsfw_is_order;
	}




	/**
	 * Function to change currency.
	 *
	 * @param [type] $order_id is the id of wallet_shop_order.
	 * @return mixed
	 */
	public function wps_wsfw_woocommerce_thankyou_order_id( $order_id ) {

		$order = new WC_Order( $order_id );
		$check_wallet_thankyou = get_post_meta( $order_id, 'wps_wallet_update_on_thankyou', true );
		if ( 'done' != $check_wallet_thankyou ) {
			$order_id               = $order->get_id();
			$userid                 = $order->get_user_id();
			$order_items            = $order->get_items();
			$wallet_id              = get_option( 'wps_wsfw_rechargeable_product_id', '' );
			$walletamount           = get_user_meta( $userid, 'wps_wallet', true );
			$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
			$user                   = get_user_by( 'id', $userid );
			foreach ( $order_items as $item_id => $item ) {
				$product_id = $item->get_product_id();
				$total      = $item->get_total();

				if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {
					$_order_currency = get_post_meta( $order_id, '_woocs_order_base_currency', true );
					if ( ! empty( $_order_currency ) ) {

						 $total = $item->get_total();
						 $total = apply_filters( 'wps_wsfw_convert_to_base_price', $total );
						$subtotal = $item->get_subtotal();
						$subtotal = apply_filters( 'wps_wsfw_convert_to_base_price', $subtotal );
						$item->set_total( $total );
						$item->set_subtotal( $subtotal );
						$order->set_total( $total );
						$_order_currency = get_post_meta( $order_id, '_woocs_order_base_currency', true );

						update_post_meta( $order_id, '_order_currency', $_order_currency );

						$_woocs_order_base_currency = get_post_meta( $order_id, '_woocs_order_base_currency', true );

						update_post_meta( $order_id, '_order_currency', $_woocs_order_base_currency );
						$order->save();
						return $order_id;
					}
				}
			}
		}

		return $order_id;
	}

	/**
	 * Add wallet_djhop_order
	 *
	 * @param array $order_types is the order type.
	 * @param array $for is for listing.
	 * @return array
	 */
	public function wps_wsfw_wc_order_types_( $order_types, $for ) {

		array_push( $order_types, 'wallet_shop_order' );
		return $order_types;
	}

}
