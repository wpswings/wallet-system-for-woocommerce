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

		global $wp_query;
		$is_endpoint = isset( $wp_query->query_vars['wps-wallet'] ) ? $wp_query->query_vars['wps-wallet'] : '';
		wp_enqueue_style( 'wps-public-slick', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/slick/slick.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wps-public-min', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/css/wps-public.css', array(), $this->version, 'all' );
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

		global $wp_query;
		wp_enqueue_script( 'wps-silk-script', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/slick/slick.min.js', array( 'jquery' ), $this->version, false );
		wp_register_script( $this->plugin_name, WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/wallet-system-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
		$wps_wsfwp_wallet_withdrawal_paypal_enable = get_option( 'wps_wsfwp_wallet_withdrawal_paypal_enable' );
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
				'wsfw_ajax_error'                => __( 'An error occured!', 'wallet-system-for-woocommerce' ),
				'wsfw_amount_error'              => __( 'Enter amount greater than 0', 'wallet-system-for-woocommerce' ),
				'wsfw_min_wallet_withdrawal'     => __( 'Wallet Withdrawal Amount Must Be Greater Than', 'wallet-system-for-woocommerce' ),
				'wsfw_max_wallet_withdrawal'     => __( 'Wallet Withdrawal Amount Should Be Less Than', 'wallet-system-for-woocommerce' ),
				'wsfw_min_wallet_transfer'       => __( 'Wallet Transfer Amount Must Be Greater Than', 'wallet-system-for-woocommerce' ),
				'wsfw_max_wallet_transfer'       => __( 'Wallet Transfer Amount Should Be Less Than', 'wallet-system-for-woocommerce' ),
				'wsfw_partial_payment_msg'       => __( 'Amount want to use from wallet', 'wallet-system-for-woocommerce' ),
				'wsfw_apply_wallet_msg'          => __( 'Apply wallet', 'wallet-system-for-woocommerce' ),
				'wsfw_transfer_amount_error'     => __( 'Transfer amount should be less than or equal to wallet balance.', 'wallet-system-for-woocommerce' ),
				'wsfw_withdrawal_amount_error'   => __( 'Withdrawal amount should be less than or equal to wallet balance.', 'wallet-system-for-woocommerce' ),
				'wsfw_recharge_minamount_error'  => __( 'Recharge amount should be greater than or equal to ', 'wallet-system-for-woocommerce' ),
				'wsfw_recharge_maxamount_error'  => __( 'Recharge amount should be less than or equal to ', 'wallet-system-for-woocommerce' ),
				'wsfw_wallet_transfer'           => __( 'You cannot transfer amount to yourself.', 'wallet-system-for-woocommerce' ),
				'wsfw_unset_amount'              => __( 'Wallet Amount Removed', 'wallet-system-for-woocommerce' ),
				'wsfw_wallet_paypal'              => $wps_wsfwp_wallet_withdrawal_paypal_enable,
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
	 * Enque script for block.
	 *
	 * @return void
	 */
	public function wsfw_wps_enqueue_script_block_eheckout() {

		$block_data = $this->checkout_review_order_custom_field_block_checkout();
		$wallet_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$cart = WC()->cart;
		if ( ! empty( WC()->session->cart_totals ) ) {
			$total_tax  = WC()->session->cart_totals['cart_contents_tax'];
			WC()->session->set( 'is_wallet_partial_payment_cart_total_tax', $total_tax );
		}

		// Get cart items.
		if ( ! empty( WC()->cart ) ) {

			$cart_items = $cart->get_cart();
		}

		// Loop through each cart item.

		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				if ( $cart_item['product_id'] == $wallet_id ) {
					$block_data = '';
				}
			}
		}

		$block_wallet_partial_name = '';
		$user_id        = get_current_user_id();
		$wallet_amount = get_user_meta( $user_id, 'wps_wallet', true );
		$wallet_amount = empty( $wallet_amount ) ? 0 : $wallet_amount;

		$wallet_amount = apply_filters( 'wps_wsfw_show_converted_price', $wallet_amount );
		if ( ! empty( $block_data ) ) {
			$block_wallet_partial_name = esc_html__( 'Pay by wallet (', 'wallet-system-for-woocommerce' ) . ( wc_price( $wallet_amount ) ) . ')';
			;

		} else {
			$block_wallet_partial_name = '';
			$block_data = '';
		}
		$discount_ = '';
		$discount_amount = '';
		if ( WC()->session->__isset( 'is_wallet_partial_payment_checkout' ) ) {
			$discount_ = (bool) WC()->session->get( 'is_wallet_partial_payment_checkout' );
			$discount_amount = (float) WC()->session->get( 'is_wallet_partial_payment_block' );

		}

		wp_register_script( 'wallet-system-for-woocommerce-block-checkout', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/wallet-system-for-woocommerce-block-checkout.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			'wallet-system-for-woocommerce-block-checkout',
			'wsfw_public_param_block',
			array(
				'ajaxurl'                         => admin_url( 'admin-ajax.php' ),
				'nonce'                           => wp_create_nonce( 'ajax-nonce' ),
				'wsfw_unset_amount'               => __( 'Wallet Amount Removed', 'wallet-system-for-woocommerce' ),
				'partial_payment_data_html'       => $block_data,
				'partial_payment_data_html_name'  => $block_wallet_partial_name,
				'wsfw_partial_payment_msg'        => __( 'Amount want to use from wallet', 'wallet-system-for-woocommerce' ),
				'wsfw_apply_wallet_msg'           => __( 'Apply wallet', 'wallet-system-for-woocommerce' ),
				'wsfw_applied_wallet_amount'      => $discount_,
				'wsfw_applied_wallet_amount_data' => wc_price( $discount_amount ),
			)
		);
		wp_enqueue_script( 'wallet-system-for-woocommerce-block-checkout' );
	}


	/**
	 * Unset COD if wallet topup product in cart.
	 *
	 * @param array $available_gateways   all the available payment gateways.
	 */
	public function wps_wsfw_restrict_payment_gateway( $available_gateways ) {

		if ( isset( $available_gateways['wps_wcb_wallet_payment_gateway'] ) ) {
			$user_id        = get_current_user_id();
			$wps_cart_total = 0;
			$is_pro = false;
			$is_pro = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro );
			if ( $is_pro ) {
				$wps_wallet_restrict_wallet_gateway = get_user_meta( $user_id, 'wps_wallet_restrict_wallet_gateway', true );
				if ( 'on' == $wps_wallet_restrict_wallet_gateway ) {
					unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
					return $available_gateways;
				}
			}

			if ( ! empty( WC()->cart ) ) {
				$wps_cart_total = WC()->cart->total;
			}

			$wallet_amount  = get_user_meta( $user_id, 'wps_wallet', true );
			$wallet_amount  = empty( $wallet_amount ) ? 0 : $wallet_amount;

			$wallet_amount  = apply_filters( 'wps_wsfw_show_converted_price', $wallet_amount );

			if ( ! empty( WC()->session ) ) {

				if ( WC()->session->__isset( 'is_wallet_partial_payment' ) ) {

						unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
				} elseif ( WC()->session->__isset( 'recharge_amount' ) ) {

					unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );

				} elseif ( isset( $wallet_amount ) ) {

					if ( 'on' == get_option( 'wsfw_enable_wallet_negative_balance' ) ) {

						$limit = get_option( 'wsfw_enable_wallet_negative_balance_limit' );
						$order_number = get_user_meta( $user_id, 'wsfw_enable_wallet_negative_balance_limit_order', true );
						$order_limit = get_option( 'wsfw_enable_wallet_negative_balance_limit_order' );
						$is_pro = false;
						$is_pro = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro );
						if ( $is_pro ) {

							if ( intval( $order_number ) < intval( $order_limit ) ) {

								if ( ( $wallet_amount ) <= ( $wps_cart_total ) ) {

									unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
								}
							} elseif ( ( $wallet_amount ) <= ( $limit ) ) {
									$total_balance = $wallet_amount + $limit;
								if ( $total_balance < $wps_cart_total ) {

									unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
								}
									$user_id        = get_current_user_id();
							} elseif ( ( $wallet_amount ) >= ( $limit ) ) {
								$total_balance = intval( $wallet_amount ) + intval( $limit );
								if ( $total_balance < $wps_cart_total ) {

									unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
								}
							}
						} elseif ( $wallet_amount < $wps_cart_total ) {

								unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
						}
					} elseif ( $wallet_amount < $wps_cart_total ) {

							unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
					}
				} elseif ( isset( $wallet_amount ) && $wallet_amount <= 0 ) {
					unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
				}
			}
		}

		$wallet_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$cart = WC()->cart;
		$cart_items = '';
		// Get cart items.
		if ( ! empty( $cart ) ) {
			$cart_items = $cart->get_cart();
		}

		// Loop through each cart item.

		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				if ( $cart_item['product_id'] == $wallet_id ) {
					if ( isset( $available_gateways['wps_wcb_wallet_payment_gateway'] ) ) {
						unset( $available_gateways['wps_wcb_wallet_payment_gateway'] );
					}
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
	public function checkout_review_order_custom_field_block_checkout() {
		$block_checkout = '';
		$wps_cart_total = WC()->cart->get_total( 'edit' );

		$cart_fee = WC()->cart->get_fee_total();

		$wps_cart_total = intval( $wps_cart_total ) + abs( $cart_fee );

		$user_id        = get_current_user_id();
		$wallet_amount  = get_user_meta( $user_id, 'wps_wallet', true );
		$wallet_amount  = empty( $wallet_amount ) ? 0 : $wallet_amount;
		$limit = get_option( 'wsfw_enable_wallet_negative_balance_limit' );
		$order_number = get_user_meta( $user_id, 'wsfw_enable_wallet_negative_balance_limit_order', true );
		$order_limit = get_option( 'wsfw_enable_wallet_negative_balance_limit_order' );
		$total_balance = '';
		if ( $user_id ) {
			$wsfw_wallet_partial_payment_method_options = get_option( 'wsfw_wallet_partial_payment_method_options', 'manual_pay' );
			$wsfw_wallet_partial_payment_method_enable = get_option( 'wsfw_wallet_partial_payment_method_enabled', 'off' );
			$is_pro_plugin = false;
			$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
			if ( $is_pro_plugin ) {
				if ( 'on' == get_option( 'wsfw_enable_wallet_negative_balance' ) ) {

					if ( ! empty( $order_limit ) ) {
						if ( intval( $order_number ) >= intval( $order_limit ) ) {

							if ( ( intval( $wallet_amount ) ) <= intval( $limit ) ) {
								$total_balance = intval( $wallet_amount ) + intval( $limit );
								if ( $total_balance >= $wps_cart_total ) {
									return;
								}
							} elseif ( ( intval( $wallet_amount ) ) >= ( intval( $limit ) ) ) {
									$total_balance = intval( $wallet_amount ) + intval( $limit );
								if ( $total_balance >= $wps_cart_total ) {
									return;
								}
							}
						}
					}
				}
			}

			if ( 'on' != $wsfw_wallet_partial_payment_method_enable ) {
				return;
			}

			$wallet_amount = get_user_meta( $user_id, 'wps_wallet', true );
			$wallet_amount = empty( $wallet_amount ) ? 0 : $wallet_amount;

			$wallet_amount = apply_filters( 'wps_wsfw_show_converted_price', $wallet_amount );
			if ( isset( $wallet_amount ) && $wallet_amount > 0 ) {
				if ( $wallet_amount < $wps_cart_total || $this->is_enable_wallet_partial_payment() ) {

					if ( ! WC()->session->__isset( 'recharge_amount' ) ) {
						$is_checked_data = $this->is_enable_wallet_partial_payment();
						if ( $is_checked_data ) {
							$is_checked_data = "checked='checked'";
						}
						?>	
					<tr class="partial_payment">
						<td></td>
						<td>
							<?php
							if ( 'manual_pay' === $wsfw_wallet_partial_payment_method_options ) {

								$block_checkout = '<p class="form-row checkbox_field woocommerce-validated" id="partial_payment_wallet_field"> <input type="checkbox" class="input-checkbox " name="partial_total_payment_wallet" id="partial_payment_wallet" value="enable" ' . $is_checked_data . ' data-walletamount="' . esc_attr( $wallet_amount ) . '" > </p>';

							} elseif ( 'total_pay' === $wsfw_wallet_partial_payment_method_options ) {
								$block_checkout = '<p class="form-row checkbox_field woocommerce-validated" id="partial_total_payment_wallet_field"> <input type="checkbox" class="input-checkbox " name="partial_total_payment_wallet" id="partial_total_payment_wallet" value="total_enable" ' . $is_checked_data . ' data-walletamount="' . esc_attr( $wallet_amount ) . '" > </p>';

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
		return $block_checkout;
	}

	/**
	 * Show wallet as discount ( when wallet amount is less than cart total ) in review order table.
	 *
	 * @return void
	 */
	public function checkout_review_order_custom_field() {

		$wps_cart_total = WC()->cart->get_total( 'edit' );

		$cart_fee = WC()->cart->get_fee_total();

		$wps_cart_total = $wps_cart_total + abs( $cart_fee );

		// Remove currency symbol and get numeric value.

		$user_id        = get_current_user_id();
		$wallet_amount  = get_user_meta( $user_id, 'wps_wallet', true );
		$wallet_amount  = empty( $wallet_amount ) ? 0 : $wallet_amount;
		$limit = get_option( 'wsfw_enable_wallet_negative_balance_limit' );
		$order_number = get_user_meta( $user_id, 'wsfw_enable_wallet_negative_balance_limit_order', true );
		$order_limit = get_option( 'wsfw_enable_wallet_negative_balance_limit_order' );

		if ( $user_id ) {
			$wsfw_wallet_partial_payment_method_options = get_option( 'wsfw_wallet_partial_payment_method_options', 'manual_pay' );
			$wsfw_wallet_partial_payment_method_enable = get_option( 'wsfw_wallet_partial_payment_method_enabled', 'off' );
			$is_pro_plugin = false;
			$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
			if ( $is_pro_plugin ) {
				if ( 'on' == get_option( 'wsfw_enable_wallet_negative_balance' ) ) {

					if ( ! empty( $order_limit ) ) {
						if ( intval( $order_number ) <= intval( $order_limit ) ) {

							return;
						}
					}

					if ( ( $wallet_amount ) <= ( $limit ) ) {
						$total_balance = intval( $wallet_amount ) + intval( $limit );
						if ( $total_balance >= $wps_cart_total ) {

							return;
						}
					} elseif ( ( intval( $wallet_amount ) ) >= ( intval( $limit ) ) ) {
						$total_balance = intval( $wallet_amount ) + intval( $limit );
						if ( $total_balance >= $wps_cart_total ) {
							return;
						}
					}
				}
			}

			if ( 'on' != $wsfw_wallet_partial_payment_method_enable ) {
				return;
			}
			$wallet_amount = get_user_meta( $user_id, 'wps_wallet', true );
			$wallet_amount = empty( $wallet_amount ) ? 0 : $wallet_amount;

			$wallet_amount = apply_filters( 'wps_wsfw_show_converted_price', $wallet_amount );
			if ( isset( $wallet_amount ) && $wallet_amount > 0 ) {

				if ( intval( $wallet_amount ) <= intval( $wps_cart_total ) || $this->is_enable_wallet_partial_payment() ) {

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

		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );

		foreach ( $order_items as $item_id => $item ) {

			$product_id = $item->get_product_id();
			$total      = $item->get_total();

			if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {
				$wps_wsfw_wallet_order_auto_process       = get_option( 'wps_wsfw_wallet_order_auto_process' );
				$wps_wsfw_wallet_order_auto_process       = is_array( $wps_wsfw_wallet_order_auto_process ) && ! empty( $wps_wsfw_wallet_order_auto_process ) ? $wps_wsfw_wallet_order_auto_process : array( 'completed' );
				$is_pro_plugin = false;
				$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
				if ( ! $is_pro_plugin ) {
					$wps_wsfw_wallet_order_auto_process = array( 'completed' );
				}
				$is_currency_added_in_wallet = '';
				if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
					// HPOS usage is enabled.
					$is_currency_added_in_wallet = $order->get_meta( 'wps_order_recharge_executed', true );
				} else {
					$is_currency_added_in_wallet = get_post_meta( $order_id, 'wps_order_recharge_executed', true );
				}

				if ( 'done' == $is_currency_added_in_wallet ) {
					continue;
				}

				if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
					// HPOS usage is enabled.

					$order->update_meta_data( 'wps_wallet_recharge_order', 'yes' );
					$order->save();
				} else {
					update_post_meta( $order_id, 'wps_wallet_recharge_order', 'yes' );
				}
				if ( ! empty( $wps_wsfw_wallet_order_auto_process ) && in_array( $new_status, $wps_wsfw_wallet_order_auto_process ) ) {

					$amount          = $total;
					$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $amount );

					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$converted_ = $order->get_meta( 'wps_converted_currency_update' );
						$order->update_meta_data( 'wps_wallet_recharge_order', 'yes' );
						$order->save();
					} else {
						$converted_ = get_post_meta( $order_id, 'wps_converted_currency_update', true );
					}
					if ( ! empty( $converted_ ) ) {
						$credited_amount = $converted_;
					}
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

					$is_payment_gateway_cahrge = false;
					$credited_amount_payment_charge = '';
					if ( 'on' == get_option( 'wps_wsfwp_wallet_action_payment_gateway_charge' ) ) {

						$wsfw_payment_charge_type = get_option( 'wps_wsfwp_payment_gateway_charge_fee_type' );
						$_wps_wsfwp_payment_gateway_charge_type_bacs = get_option( 'wps_wsfwp_payment_gateway_charge_type_' . $payment_method );
						if ( 'percent' === $wsfw_payment_charge_type ) {
							$credited_amount_payment_charge = ( ( intval( $credited_amount ) * intval( $_wps_wsfwp_payment_gateway_charge_type_bacs ) ) / 100 );
						} else {
							$credited_amount_payment_charge = $_wps_wsfwp_payment_gateway_charge_type_bacs;
						}
					}
					if ( ! empty( $credited_amount_payment_charge ) ) {
						$is_payment_gateway_cahrge = true;
						$credited_amount = $credited_amount - $credited_amount_payment_charge;
						$walletamount += $credited_amount;
						$balance   = $credited_amount;
					} else {

						$walletamount += $credited_amount;
						$balance   = $order->get_currency() . ' ' . $amount;
					}

					update_user_meta( $update_wallet_userid, 'wps_wallet', $walletamount );
					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$order->update_meta_data( 'wps_order_recharge_executed', 'done' );
						$order->save();
					} else {
						update_post_meta( $order_id, 'wps_order_recharge_executed', 'done' );

					}
					$is_pro_plugin = false;
					$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
					if ( $is_pro_plugin ) {
						$is_auto_complete = get_option( 'wsfw_wallet_recharge_order_status_checkout', '' );

						if ( isset( $is_auto_complete ) && 'on' == $is_auto_complete ) {

							// Mark as on-hold (we're awaiting the payment).
							$order->update_status( 'completed', __( 'Wallet Recharge Payment Completed', 'wallet-system-for-woocommerce' ) );
							// Remove cart.
							WC()->cart->empty_cart();

						}
					}

					$balance   = $order->get_currency() . ' ' . $amount;
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
						$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
						$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $amount ) . __( ' through wallet recharging.', 'wallet-system-for-woocommerce' );
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
								$user       = get_user_by( 'id', $update_wallet_userid );
								$balance_mail = $balance;
								$user_name       = $user->first_name . ' ' . $user->last_name;
								$customer_email->trigger( $update_wallet_userid, $user_name, $balance_mail, '' );
							}
						} else {

							$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
						}
					}
					$transaction_type = __( 'Wallet credited through recharge purchase ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
					$transaction_data = array(
						'user_id'          => $userid,
						'amount'           => $amount,
						'currency'         => $order->get_currency(),
						'payment_method'   => $payment_method,
						'transaction_type' => htmlentities( $transaction_type ),
						'transaction_type_1' => 'credit',
						'order_id'         => $order_id,
						'note'             => $transfer_note,
					);
					$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

					if ( $is_payment_gateway_cahrge ) {

						$transaction_type = __( 'Wallet Recharge Amount Charged For Gateway ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
						$transaction_data = array(
							'user_id'          => $update_wallet_userid,
							'amount'           => $credited_amount_payment_charge,
							'currency'         => $order->get_currency(),
							'payment_method'   => $payment_method,
							'transaction_type' => htmlentities( $transaction_type ),
							'transaction_type_1' => 'debit',
							'order_id'         => $order_id,
							'note'             => $transfer_note,
						);
						$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

					}
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
		add_shortcode( 'wps-wallet-amount', array( $this, 'wps_wsfw_show_wallet_amount' ) );
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

		$wsfw_wallet_dashboard_template_css = get_option( 'wsfw_wallet_dashboard_template_css' );
		
		if( 'template1' == $wsfw_wallet_dashboard_template_css ){
			do_action( 'wsfw_pro_version_wallet_template_file' );
		} else {
			
			include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-public-display.php';
		}
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
	 * Show the wallet through shortcode.
	 */
	public function wps_wsfw_show_wallet_amount() {

		$customer_id = get_current_user_id();
		$walletamount = 0;
		if ( $customer_id > 0 ) {
			$walletamount = get_user_meta( $customer_id, 'wps_wallet', true );
			$walletamount = empty( $walletamount ) ? 0 : $walletamount;
			$walletamount = apply_filters( 'wps_wsfw_show_converted_price', $walletamount );
		}
		// custom work.
		$current_currency = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );
		$wps_wsfwp_wallet_user_currency_setting = get_option( 'wps_wsfwp_wallet_user_currency_setting' );
		if ( 'yes' == $wps_wsfwp_wallet_user_currency_setting ) {
			$wps_wallet_last_order_currency = get_user_meta( $customer_id, 'wps_wallet_last_order_currency', true );
			if ( $wps_wallet_last_order_currency == $current_currency ) {
				$walletamount = $walletamount;
			} else {
				$walletamount = 0;
			}
		} else if ( 'no' == $wps_wsfwp_wallet_user_currency_setting ) {
			$wps_wallet_order_geolocation_currency = get_user_meta( $customer_id, 'wps_wallet_order_geolocation_currency', true );
			if ( $wps_wallet_order_geolocation_currency == $current_currency ) {
				$walletamount = $walletamount;
			} else {
				$walletamount = 0;
			}
		} else {
			$walletamount = $walletamount;

		}
		// custom work.
		return wc_price( $walletamount );
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

			$customer_id = get_current_user_id();
			if ( $customer_id > 0 ) {
				$walletamount = get_user_meta( $customer_id, 'wps_wallet', true );
				$walletamount = empty( $walletamount ) ? 0 : $walletamount;
				$walletamount = apply_filters( 'wps_wsfw_show_converted_price', $walletamount );
			}

			if ( $discount ) {
				$fee = array(
					'id'     => 'via_wallet_partial_payment',
					'name'   => __( 'Via wallet', 'wallet-system-for-woocommerce' ),
					'amount' => (float) -1 * $discount,
					'taxable' => false,
					'tax_class' => 'zero-rate',
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

		$wallet_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$wsfw_enable_wallet_negative_balance_enabled_interest = get_option( 'wsfw_enable_wallet_negative_balance_enabled_interest', '' );

		if ( 'on' == $wsfw_enable_wallet_negative_balance_enabled_interest ) {

			if ( ! empty( $wallet_id ) ) {
				if ( ! WC()->cart->is_empty() ) {
					foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
						$_product = $values['data'];
						if ( $_product->get_id() == $wallet_id ) {
							$customer_id = get_current_user_id();
							if ( $customer_id > 0 ) {
								$walletamount = get_user_meta( $customer_id, 'wps_wallet', true );
								$walletamount = empty( $walletamount ) ? 0 : $walletamount;
								$walletamount = apply_filters( 'wps_wsfw_show_converted_price', $walletamount );
								if ( $walletamount < 0 ) {

									$wps_wsfw_intrest_amount_negative_balance = get_option( 'wps_wsfw_intrest_amount_negative_balance' );

									$wps_wsfw_intrest_type_amount_negative_balance = get_option( 'wps_wsfw_intrest_type_amount_negative_balance' );

									$cashback_amount = '';
									if ( ! empty( $wps_wsfw_intrest_type_amount_negative_balance ) ) {
										$wsfw_percent_cashback_amount = abs( $walletamount ) * ( $wps_wsfw_intrest_amount_negative_balance / 100 );

									}

									if ( 'percent' == $wps_wsfw_intrest_type_amount_negative_balance && $wps_wsfw_intrest_type_amount_negative_balance ) {

										$cashback_amount = $wsfw_percent_cashback_amount;

									} elseif ( $wps_wsfw_intrest_amount_negative_balance > 0 && ! ( is_array( $wps_wsfw_intrest_amount_negative_balance ) ) ) {
											$cashback_amount = $wps_wsfw_intrest_amount_negative_balance;
									} else if ( $wps_wsfw_intrest_type_amount_negative_balance > 0 ) {
										$cashback_amount = $wps_wsfw_intrest_type_amount_negative_balance;
									}

									if ( $cashback_amount ) {
										$all_fees = wc()->cart->fees_api()->get_fees();

										$wps_wsfw_intrest_text_name_amount_negative_balance = get_option( 'wps_wsfw_intrest_text_name_amount_negative_balance', 'Interest wallet' );
										WC()->cart->add_fee( $wps_wsfw_intrest_text_name_amount_negative_balance, abs( $cashback_amount ), true, 'zero-rate' );

									}
								}
							}
						}
					}
				}
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
					add_action( 'woocommerce_blocks_enqueue_cart_block_scripts_after', array( $this, 'add_cart_custom_notice' ) );

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
			global $WOOCS; // phpcs:ignore issues due to plugin compatibility.

			$amount = $WOOCS->woocs_exchange_value( $wallet_bal ); // phpcs:ignore issues due to plugin compatibility.

			return $amount;
		} else if ( function_exists( 'wmc_get_price' ) ) {

			$wallet_bal = wmc_get_price( $wallet_bal );
			return $wallet_bal;
		} else if ( class_exists( 'WOOMULTI_CURRENCY_Data' ) ) {
			$multi_currency_settings = WOOMULTI_CURRENCY_Data::get_ins();
			$wmc_currencies = $multi_currency_settings->get_list_currencies();
			$current_currency = $multi_currency_settings->get_current_currency();
			$current_currency_rate = floatval( $wmc_currencies[ $current_currency ]['rate'] );
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
				global $WOOCS; // phpcs:ignore issues due to plugin compatibility.
				$amount = '';
				if ( $WOOCS->is_multiple_allowed ) { // phpcs:ignore issues due to plugin compatibility.
					 $currrent = $WOOCS->current_currency; // phpcs:ignore issues due to plugin compatibility.
					if ( $currrent != $WOOCS->default_currency ) { // phpcs:ignore issues due to plugin compatibility.
						$currencies = $WOOCS->get_currencies(); // phpcs:ignore issues due to plugin compatibility.
						$rate = $currencies[ $currrent ]['rate'];
						$amount = $price / ( $rate );
						return $amount;
					} else {
						return $price;
					}
				}
			}
		}

		if ( function_exists( 'wmc_revert_price' ) ) {

			$price = wmc_revert_price( $price );
			return $price;
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
		$price = '';
		$cart_items = $cart_object->cart_contents;
		if ( WC()->session->__isset( 'recharge_amount' ) ) {
			$wallet_recharge = WC()->session->get( 'recharge_amount' );
			if ( ! empty( $wallet_recharge ) ) {
				$price           = $wallet_recharge;
			}
		}
		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $key => $value ) {
				if ( $value['product_id'] == $wallet_id ) {
					if ( empty( $price ) ) {
						$price = get_post_meta( $wallet_id, '_regular_price', true );
					}
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
						update_post_meta( $wallet_id, '_regular_price', $price );
						update_post_meta( $wallet_id, '_price', $price );
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
	 * Order in case of currency.
	 *
	 * @param [type] $order is the order object.
	 * @return void
	 */
	public function wps_wocuf_initate_upsell_orders_api_checkout_org( $order ) {

		$order_id               = $order->get_id();
		$this->wsfw_wallet_add_order_detail_api( $order );
		$userid                 = $order->get_user_id();
		$order_items            = $order->get_items();
		$wallet_id              = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$walletamount           = get_user_meta( $userid, 'wps_wallet', true );
		$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
		$user                   = get_user_by( 'id', $userid );

		if ( ! empty( get_option( 'wsfw_enable_wallet_negative_balance_limit_order' ) ) ) {
			$order_number = get_user_meta( $userid, 'wsfw_enable_wallet_negative_balance_limit_order', true );
			update_user_meta( $userid, 'wsfw_enable_wallet_negative_balance_limit_order', intval( $order_number ) + 1 );

		}

		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.

			$order_total            = $order->get_total();
			$order_total = apply_filters( 'wps_wsfw_convert_to_base_price', $order_total );
			$order_shipping = $order->get_shipping_total();
			$order_shipping = apply_filters( 'wps_wsfw_convert_to_base_price', $order_shipping );
			$order_total_tax = $order->get_total_tax();
			$order_total_tax = apply_filters( 'wps_wsfw_convert_to_base_price', $order_total_tax );
			$order_subtotal       = $order->get_subtotal();
			$order_subtotal = apply_filters( 'wps_wsfw_convert_to_base_price', $order_subtotal );
			if ( ! empty( $order_shipping ) ) {
				$order_total = $order_total - $order_shipping;
			}
			if ( ! empty( $order_total_tax ) ) {
				$order_total = $order_total - $order_total_tax;
			}

			$order->update_meta_data( 'wps_wsfw_order_total', $order_total );
			$order->update_meta_data( 'wps_wsfw_order_tax', $order_shipping );
			$order->update_meta_data( 'wps_wsfw_order_shipping', $order_total_tax );
			$order->update_meta_data( 'wps_wsfw_order_subtotal', $order_subtotal );
			$order->update_meta_data( 'is_block_initiated', 'done' );
			$order->save();

		} else {
			update_post_meta( $order_id, 'is_block_initiated', 'done' );
		}
		foreach ( $order_items as $item_id => $item ) {

			$product_id = $item->get_product_id();
			$total      = $item->get_total();
			if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {
					$amount          = $total;
					$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $amount );
				if ( $credited_amount != $amount ) {

					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$order->update_meta_data( 'wps_converted_currency_update', $credited_amount );
						$order->save();

					} else {
						update_post_meta( $order_id, 'wps_converted_currency_update', $credited_amount );
					}
				}
			}
		}
	}


	/** Order in case of currency.
	 *
	 * @param [type] $order_id is the current order id.
	 * @return void
	 */
	public function wps_wocuf_initate_upsell_orders( $order_id ) {
		$order     = wc_get_order( $order_id );
		$order_id               = $order->get_id();
		$userid                 = $order->get_user_id();
		$this->wsfw_wallet_add_order_detail_api( $order );
		$payment_method         = $order->get_payment_method();
		$new_status             = $order->get_status();
		$order_items            = $order->get_items();
		$wallet_id              = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$walletamount           = get_user_meta( $userid, 'wps_wallet', true );
		$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
		$user                   = get_user_by( 'id', $userid );
		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
		if ( ! empty( get_option( 'wsfw_enable_wallet_negative_balance_limit_order' ) ) ) {
			$order_number = get_user_meta( $userid, 'wsfw_enable_wallet_negative_balance_limit_order', true );
			update_user_meta( $userid, 'wsfw_enable_wallet_negative_balance_limit_order', intval( $order_number ) + 1 );

		}
		foreach ( $order_items as $item_id => $item ) {

			$product_id = $item->get_product_id();
			$total      = $item->get_total();

			if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {

					$amount          = $total;
					$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $amount );
				if ( $credited_amount != $amount ) {

					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$order->update_meta_data( 'wps_converted_currency_update', $credited_amount );
						$order->save();

					} else {
						update_post_meta( $order_id, 'wps_converted_currency_update', $credited_amount );
					}
				}
			}
		}
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
				echo '<style type="text/css">
					.woocommerce-order .woocommerce-customer-details {
						display:none;
					}
					</style>';
			}
		}

		$check_wallet_thankyou = get_post_meta( $order_id, 'wps_wallet_update_on_thankyou', true );
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$check_wallet_thankyou = $order->get_meta( 'wps_wallet_update_on_thankyou' );
		} else {
			$check_wallet_thankyou = get_post_meta( $order_id, 'wps_wallet_update_on_thankyou', true );
		}
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
		$enable_wallet_fields = get_option( 'wsfw_wallet_payment_checkout_field_checkout' );
		if ( 'on' == $enable_wallet_fields ) {
			return $fields;
		}

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
		$wsfw_max_cashbak_amount = ! empty( get_option( 'wps_wsfw_cashback_amount_max' ) ) ? get_option( 'wps_wsfw_cashback_amount_max' ) : 0;
		$wsfw_cashbak_amount     = ! empty( get_option( 'wps_wsfw_cashback_amount' ) ) ? get_option( 'wps_wsfw_cashback_amount' ) : 0;
		$wsfw_cashbak_type       = get_option( 'wps_wsfw_cashback_type' );
		$wsfw_min_cart_amount    = ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : 0;
		$wsfw_min_cart_amount = apply_filters( 'wps_wsfw_show_converted_price', $wsfw_min_cart_amount );
		$wps_wsfw_cashback_rule  = get_option( 'wps_wsfw_cashback_rule', '' );
		$update                  = false;

		if ( empty( $wsfw_cashbak_amount ) ) {
			return;
		}

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

						if ( ! empty( $wsfw_max_cashbak_amount ) ) {
							if ( $cashback_amount <= $wsfw_max_cashbak_amount ) {
								$cashback_amount = $cashback_amount;
							} else {
								$cashback_amount = $wsfw_max_cashbak_amount;
							}
						} else {
							$cashback_amount += $cashback_amount;
						}
					} else {
						$cashback_amount = $cashback_amount_order;
					}
				}
			}
		} elseif ( wc()->cart->get_subtotal() > $wsfw_min_cart_amount ) {

			if ( 'percent' === $wsfw_cashbak_type ) {

				$total                        = wc()->cart->get_subtotal();
				$total                        = apply_filters( 'wps_wsfw_wallet_calculate_cashback_on_total_amount_order_atatus', wc()->cart->get_subtotal() );
				$wsfw_percent_cashback_amount = $total * ( $wsfw_cashbak_amount / 100 );

				if ( ! empty( $wsfw_max_cashbak_amount ) ) {
					if ( $wsfw_percent_cashback_amount < $wsfw_max_cashbak_amount ) {
						$cashback_amount += $wsfw_percent_cashback_amount;
					} else {
						$cashback_amount += $wsfw_max_cashbak_amount;
					}
				} else {
					$cashback_amount += $wsfw_percent_cashback_amount;
				}
			} elseif ( ! empty( wc()->cart->get_subtotal() ) ) {

					$cashback_amount += $wsfw_cashbak_amount;
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

						if ( 'on' == get_option( 'wps_wsfw_cashback_wallet_recharge' ) ) {
							$is_wallet_recharge = false;
						} else {
							$is_wallet_recharge = true;
						}
					}
				}
			}
			if ( true == $is_wallet_recharge ) {
				return;
			}

			$cashback_amount        = $this->wsfw_calculate_cashback_cart();
			$wsfw_min_cart_amount   = ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : '';
			$wsfw_min_cart_amount = apply_filters( 'wps_wsfw_show_converted_price', $wsfw_min_cart_amount );
			$cart_total             = ! empty( wc()->cart->get_subtotal() ) ? wc()->cart->get_subtotal() : wc()->cart->get_subtotal();
			$cart_total             = apply_filters( 'wps_wsfw_wallet_cashback_on_total', $cart_total );
			$wps_wsfw_cashback_rule = get_option( 'wps_wsfw_cashback_rule', '' );

			if ( 'cartwise' === $wps_wsfw_cashback_rule ) {

				if ( floatval( $cart_total ) < floatval( $wsfw_min_cart_amount ) ) {
					?>
					<div class="woocommerce-message wps-woocommerce-message woocommerce-Message--info wps-woocommerce-info">
					<?php
					/* translators: %s: search term */
					echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Earn Cashback On Orders Above %s .', 'wallet-system-for-woocommerce' ), wc_price( $wsfw_min_cart_amount, $this->wsfw_wallet_price_args() ) ), $wsfw_min_cart_amount ) );
				} else {

					$is_hide_cart = get_option( 'wps_wsfw_hide_cashback_cart' );
					$is_hide_checkout = get_option( 'wps_wsfw_hide_cashback_checkout' );
					$is_pro_plugin = false;
					$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
					if ( $is_pro_plugin ) {
						if ( is_cart() ) {
							if ( 'on' == $is_hide_cart ) {
								return;
							}
						}
						if ( is_checkout() ) {
							if ( 'on' == $is_hide_checkout ) {
								return;
							}
						}
					}

					if ( is_user_logged_in() ) {
						if ( $cashback_amount > 0 ) {
							?>
							<div class="woocommerce-message wps-woocommerce-message woocommerce-Message--info wps-woocommerce-info">
							<?php
							/* translators: %s: search term */
							echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Upon placing this order a cashback of %s will be credited to your wallet.', 'wallet-system-for-woocommerce' ), wc_price( $cashback_amount, $this->wsfw_wallet_price_args() ) ), $cashback_amount ) );
						}
					} else {
						?>
						<div class="woocommerce-Message wps-woocommerce-message woocommerce-Message--info wps-woocommerce-info woocommerce-info">
						<?php
						/* translators: %s: search term */
						echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Please <a href="%1$s">log in</a> to avail %2$s cashback from this order.', 'wallet-system-for-woocommerce' ), esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ), wc_price( $cashback_amount, $this->wsfw_wallet_price_args() ) ), $cashback_amount ) );
					}
				}
			} elseif ( 'catwise' === $wps_wsfw_cashback_rule ) {
				if ( is_user_logged_in() ) {
					$is_hide_cart = get_option( 'wps_wsfw_hide_cashback_cart', true );
					$is_hide_checkout = get_option( 'wps_wsfw_hide_cashback_checkout', true );
					if ( is_cart() ) {
						if ( 'on' == $is_hide_cart ) {
							return;
						}
					}
					if ( is_checkout() ) {
						if ( 'on' == $is_hide_checkout ) {
							return;
						}
					}
					if ( $cashback_amount > 0 ) {
						?>
						<div class="woocommerce-message wps-woocommerce-message woocommerce-Message--info wps-woocommerce-info">
						<?php
						/* translators: %s: search term */
						echo wp_kses_post( apply_filters( 'wps_wsfw_cashback_notice_text', sprintf( __( 'Upon placing this order a cashback of %s will be credited to your wallet.', 'wallet-system-for-woocommerce' ), wc_price( $cashback_amount, $this->wsfw_wallet_price_args() ) ), $cashback_amount ) );
					}
				} else {
					?>
					<div class="woocommerce-Message wps-woocommerce-message woocommerce-Message--info wps-woocommerce-info woocommerce-info">
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
				} elseif ( $wsfw_cashbak_amount > 0 ) {
						$cashback_amount += $wsfw_cashbak_amount;
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
		global $current_user, $post;
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
				<div class="woocommerce-message wps-woocommerce-message">
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
		$amount = '';
		$updated = false;
		if ( ! empty( $customer_id ) ) {
			$wps_wsfw_wallet_action_registration_enable      = get_option( 'wps_wsfw_wallet_action_registration_enable', '' );
			$wps_wsfw_wallet_action_registration_amount      = ! empty( get_option( 'wps_wsfw_wallet_action_registration_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_registration_amount' ) : 1;
			$current_currency                                = apply_filters( 'wps_wsfw_get_current_currency', get_woocommerce_currency() );

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
		$balance   = $current_currency . ' ' . $amount;
		if ( $updated ) {
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
				$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
				;
				$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through successfully signup.', 'wallet-system-for-woocommerce' );
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
						$user       = get_user_by( 'id', $customer_id );
						$balance_mail = $balance;
						$user_name       = $user->first_name . ' ' . $user->last_name;
						$customer_email->trigger( $customer_id, $user_name, $balance_mail, '' );
					}
				} else {

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
			}

			$transaction_type = __( 'Wallet credited through signup ', 'wallet-system-for-woocommerce' );
			$transaction_data = array(
				'user_id'          => $customer_id,
				'amount'           => $amount,
				'currency'         => $current_currency,
				'payment_method'   => 'Sigup',
				'transaction_type' => htmlentities( $transaction_type ),
				'transaction_type_1' => 'credit',
				'order_id'         => '',
				'note'             => '',
			);
			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
		}

		if ( get_option( 'wps_wsfw_wallet_action_refer_friend_enable' ) == 'on' ) {
			$cookie_val   = isset( $_COOKIE['wps_wsfw_cookie_set'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wps_wsfw_cookie_set'] ) ) : '';
			$retrive_data = $cookie_val;
			if ( ! empty( $retrive_data ) ) {
				$args['meta_query'] = array(
					array(
						'key'     => 'wps_points_referral',
						'value'   => trim( $retrive_data ),
						'compare' => '==',
					),
				);
				$refere_data = get_users( $args );
				$refere_id = $refere_data[0]->data->ID;

				if ( ! empty( $refere_id ) ) {

					$walletamount           = get_user_meta( $refere_id, 'wps_wallet', true );
					$walletamount           = empty( $walletamount ) ? 0 : $walletamount;
					$wallet_user            = get_user_by( 'id', $refere_id );
					$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
					$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
					$wps_wsfw_wallet_action_registration_amount          = get_option( 'wps_wsfw_wallet_action_referal_amount' );
					if ( $wps_wsfw_wallet_action_registration_amount > 0 ) {
						$amount          = $wps_wsfw_wallet_action_registration_amount;
						$credited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $wps_wsfw_wallet_action_registration_amount );
						$walletamount    += $credited_amount;
						update_user_meta( $refere_id, 'wps_wallet', $walletamount );
						$updated = true;
					}

					if ( $updated ) {
						$balance   = $current_currency . ' ' . $amount;
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
							$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
							$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through successfully signup.', 'wallet-system-for-woocommerce' );
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
									$user       = get_user_by( 'id', $customer_id );
									$balance_mail = $balance;
									$user_name       = $user->first_name . ' ' . $user->last_name;
									$customer_email->trigger( $customer_id, $user_name, $balance_mail, '' );
								}
							} else {

								$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
							}
						}

						$transaction_type = __( 'Wallet credited through referral to a friend ', 'wallet-system-for-woocommerce' );
						$transaction_data = array(
							'user_id'          => $refere_id,
							'amount'           => $amount,
							'currency'         => $current_currency,
							'payment_method'   => 'Referral',
							'transaction_type' => htmlentities( $transaction_type ),
							'transaction_type_1' => 'credit',
							'order_id'         => '',
							'note'             => '',
						);
						$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
					}
				}
			}
		}
	}

	/**
	 * The function is used for set the cookie for referee
	 *
	 * @name wps_wsfw_referral_link_using_cookie
	 * @since 1.0.0
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 */
	public function wps_wsfw_referral_link_using_cookie() {

		if ( ! is_user_logged_in() ) {
			$wps_wsfw_ref_link_expiry = '';
			if ( empty( $wps_wsfw_ref_link_expiry ) ) {
				$wps_wsfw_ref_link_expiry = 365;
			}
			if ( isset( $_GET['pkey'] ) && ! empty( $_GET['pkey'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification
				$wps_referral_key = sanitize_text_field( wp_unslash( $_GET['pkey'] ) );// phpcs:ignore WordPress.Security.NonceVerification

				$referral_link = trim( $wps_referral_key );// phpcs:ignore WordPress.Security.NonceVerification

				if ( isset( $wps_wsfw_ref_link_expiry ) && ! empty( $wps_wsfw_ref_link_expiry ) && ! empty( $referral_link ) ) {
					setcookie( 'wps_wsfw_cookie_set', $referral_link, time() + ( 86400 * $wps_wsfw_ref_link_expiry ), '/' );
				}
			}
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
		$amount                                   = 0;
		if ( 'on' === $wps_wsfw_wallet_action_daily_enable ) {
			if ( get_transient( 'wps_wsfw_wallet_site_visit_' . $user_id ) ) {
				return;
			}

			if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {
				set_transient( 'wps_wsfw_wallet_site_visit_' . $user_id, true, DAY_IN_SECONDS );
			}
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

			$balance   = $current_currency . ' ' . $amount;
			if ( $updated ) {
				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
					$user_name  = $wallet_user->first_name . ' ' . $wallet_user->last_name;
					$mail_text  = sprintf( 'Hello %s', $user_name ) . ",\r\n";
					$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' through visiting site.', 'wallet-system-for-woocommerce' );
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

				$transaction_type = __( 'Wallet credited through visiting site. ', 'wallet-system-for-woocommerce' );
				$transaction_data = array(
					'user_id'          => $user_id,
					'amount'           => $amount,
					'currency'         => $current_currency,
					'payment_method'   => 'Site visit',
					'transaction_type' => htmlentities( $transaction_type ),
					'transaction_type_1' => 'credit',
					'order_id'         => '',
					'note'             => '',
				);
				$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
			}
		}
	}


	/**
	 * Fee html for cart total.
	 *
	 * @param [type] $cart_totals_fee_html as cart html.
	 * @param [type] $fees is the fees applied on checkout.
	 * @return mixed
	 */
	public function wsfw_wallet_cart_totals_fee_html( $cart_totals_fee_html, $fees ) {

		foreach ( $fees as $key => $fee ) {

			if ( 'via_wallet_partial_payment' == $fee ) {
				// gets the data to recalculate the cart total.
				$cart_totals_fee_html = $fees->amount;
				return wc_price( $cart_totals_fee_html );
				break;
			}
		}

		if ( is_object( $fees ) ) {
			return wc_price( $fees->amount );
		} else {
			return wc_price( $fees );
		}
	}

	/**
	 * Fix cart total html.
	 *
	 * @param array $cart_totals_fee_html as cart total html for hook woocommerce_cart_get_fee_taxes.
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

		return wc_price( $cart_total );
	}

	/**
	 * Update tax on thank you page.
	 *
	 * @return void
	 */
	public function wps_wsfw_woocommerce_thankyou_page() {
		$secure_nonce      = wp_create_nonce( 'wps-wallet-thankyou-order-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-thankyou-order-nonce' );
		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
		}
		$order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		if ( get_option( 'wsfw_check_thanks_page' ) != $order_key ) {
			$order_id = wc_get_order_id_by_order_key( $order_key );
			$this->wps_wsfw_woocommerce_thankyou_order_id( $order_id );
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			$http_host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
			$protocol = isset( $http_host ) && 'on' === $http_host ? 'https://' : 'http://';
			$current_page_u_r_l = $protocol . $http_host . $request_uri;
			update_option( 'wsfw_check_thanks_page', $order_key );
			wp_safe_redirect( $current_page_u_r_l );
			exit();
		}
	}

	/**
	 * Fix html via wallet at order details
	 *
	 * @param object $order as order.
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
				$fee_total_tax = abs( $item_fee->get_total_tax() );
				$order->remove_item( $item_id );
				break;
			}
		}

		if ( ! empty( $fee_total_tax ) ) {
			$order_id = $order->get_id();
			$order_tax = '';
			$order_tax = $order->get_total_tax();
			$order_tax = ( floatval( $order_tax ) + abs( ( $fee_total_tax ) ) );
			$order->set_cart_tax( $order_tax );
			$order->save();
			$order_tax = $order->get_total_tax();
			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				// HPOS usage is enabled.
				$_order_total = $order->get_meta( '_order_total', true );
			} else {
				$_order_total = get_post_meta( $order_id, '_order_total', true );
			}

			$_order_total = ( floatval( $_order_total ) + abs( ( $fee_total_tax ) ) );

			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				// HPOS usage is enabled.
				$order->update_meta_data( '_order_total', $_order_total );
				$order->save();

			} else {
				update_post_meta( $order_id, '_order_total', $_order_total );
			}

			$tax_display = get_option( 'woocommerce_tax_display_shop' );

			$item_fee = new WC_Order_Item_Fee();
			if ( WC()->session->__isset( 'is_wallet_partial_payment_block' ) ) {
				$fee_total = (float) WC()->session->get( 'is_wallet_partial_payment_block' );

			}
			$item_fee->set_name( $fee_name );
			$item_fee->set_amount( -( $fee_total ) );
			$item_fee->set_tax_class( '' );
			$item_fee->set_tax_status( '' );
			$item_fee->set_total( -( $fee_total ) );
			$item_fee->set_total_tax( 0 );

			// Add Fee item to the order.
			$order->add_item( $item_fee );
			$order->save();

		}
	}


	/**
	 * Fix html via wallet at order details
	 *
	 * @param object $order as order.
	 * @return void
	 */
	public function wsfw_wallet_add_order_detail_api( $order ) {

		$fee_name = '';
		$fee_total = '';
		$fee_total_tax = '';
		$order_fee_array = $order->get_items( 'fee' );
		foreach ( $order_fee_array as $item_id => $item_fee ) {

			if ( $item_fee->get_name() == 'Via wallet' ) {

				$fee_name = $item_fee->get_name();
				$fee_total = $item_fee->get_amount();
				$fee_total_tax = abs( $item_fee->get_total_tax() );
				if ( ! empty( $fee_total_tax ) ) {
					$order->remove_item( $item_id );
				}

				break;
			}
		}

		if ( ! empty( $fee_total ) ) {

			$userid        = $order->get_user_id();
			$order_id = $order->get_id();
			$walletamount = get_user_meta( $userid, 'wps_wallet', true );
			$walletamount = empty( $walletamount ) ? 0 : $walletamount;
			$user                   = get_user_by( 'id', $userid );
			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
			$payment_method         = $order->get_payment_method();
			if ( ! empty( $user ) ) {
				$name                   = $user->first_name . ' ' . $user->last_name;
			} else {
				$name = '';
			}
			$fees   = abs( $fee_total );
			$amount = $fees;
			$debited_amount = apply_filters( 'wps_wsfw_convert_to_base_price', $fees );

			if ( $walletamount < $debited_amount ) {

				if ( 'on' == get_option( 'wsfw_enable_wallet_negative_balance' ) ) {
					$walletamount = abs( $walletamount ) - abs( $debited_amount );

				} else {
					$debited_amount = $walletamount;
					$walletamount = '0';
					$order->add_order_note( 'Wallet partial amount is less than wallet amount for partial payment.' );
				}
			} else {
				$walletamount -= $debited_amount;

			}

			update_user_meta( $userid, 'wps_wallet', $walletamount );
			if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
				// HPOS usage is enabled.
				$order->update_meta_data( 'wps_wallet_update_on_thankyou', 'done' );
				$order->save();

			} else {
				update_post_meta( $order_id, 'wps_wallet_update_on_thankyou', 'done' );
			}

			$balance   = $order->get_currency() . ' ' . $amount;
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . ",\r\n";
				$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . esc_html( $balance ) . __( ' from your wallet through purchasing.', 'wallet-system-for-woocommerce' );
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
						$user       = get_user_by( 'id', $userid );
						$currency  = get_woocommerce_currency();
						$balance_mail = $balance;
						$user_name       = $user->first_name . ' ' . $user->last_name;
						$customer_email->trigger( $userid, $user_name, $balance_mail, '' );
					}
				} else {

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
			}

			$transaction_type = __( 'Wallet debited through purchasing ', 'wallet-system-for-woocommerce' ) . ' <a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>' . __( ' as discount', 'wallet-system-for-woocommerce' );

			$transaction_data = array(
				'user_id'          => $userid,
				'amount'           => $amount,
				'currency'         => $order->get_currency(),
				'payment_method'   => $payment_method,
				'transaction_type' => htmlentities( $transaction_type ),
				'transaction_type_1' => 'debit',
				'order_id'         => $order_id,
				'note'             => '',
			);

			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

		}

		if ( ! empty( $fee_total_tax ) ) {
			$order_id = $order->get_id();
			$order_tax = '';
			$order_tax = $order->get_total_tax();

			if ( WC()->session->__isset( 'is_wallet_partial_payment_cart_total_value' ) ) {
				$cart_total_after_partial_payment = (float) WC()->session->get( 'is_wallet_partial_payment_cart_total_value' );

			}
			if ( WC()->session->__isset( 'is_wallet_partial_payment_block' ) ) {
				$is_wallet_partial_payment_block = (float) WC()->session->get( 'is_wallet_partial_payment_block' );

			}
			if ( WC()->session->__isset( 'is_wallet_partial_payment_cart_total_tax' ) ) {
				$is_wallet_partial_payment_cart_total_tax = (float) WC()->session->get( 'is_wallet_partial_payment_cart_total_tax' );

			}

			$order->set_total( $cart_total_after_partial_payment );
			$order->save();
			$item_fee = new WC_Order_Item_Fee();

			$item_fee->set_name( $fee_name );
			$item_fee->set_amount( -( $is_wallet_partial_payment_block ) );
			$item_fee->set_tax_class( '' );
			$item_fee->set_tax_status( '' );
			$item_fee->set_total( -( $is_wallet_partial_payment_block ) );
			$item_fee->set_total_tax( 0 );

			// Add Fee item to the order.
			$order->add_item( $item_fee );
			$order->save();

			WC()->session->__unset( 'is_wallet_partial_payment_cart_total_tax' );
			WC()->session->__unset( 'is_wallet_partial_payment_block' );
			WC()->session->__unset( 'is_wallet_partial_payment_cart_total_value' );

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

		$this->wsfw_wallet_add_order_detail( $order );
		WC()->session->__unset( 'is_wallet_partial_payment' );
		$check_wallet_thankyou = '';
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$check_wallet_thankyou = $order->get_meta( 'wps_wallet_update_on_thankyou', true );
		} else {
			$check_wallet_thankyou = get_post_meta( $order_id, 'wps_wallet_update_on_thankyou', true );
		}
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
					$_order_currency = '';
					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$_order_currency = $order->get_meta( '_woocs_order_base_currency', true );
					} else {
						$_order_currency = get_post_meta( $order_id, '_woocs_order_base_currency', true );
					}
					// custom work.
					$valid = true;
					$wps_wsfw_custom_check = apply_filters( 'wps_check_order_currency_custom_work', $valid );
					// custom work.
					if ( ! empty( $_order_currency ) && $wps_wsfw_custom_check ) {

						// $total = $item->get_total();
						// $total = apply_filters( 'wps_wsfw_convert_to_base_price', $total );
						// $subtotal = $item->get_subtotal();
						// $subtotal = apply_filters( 'wps_wsfw_convert_to_base_price', $subtotal );
						// $item->set_total( $total );
						// $item->set_subtotal( $subtotal );
						// $order->set_total( $total );
						$_order_currency = '';
						$_woocs_order_base_currency = '';
						if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
							// HPOS usage is enabled.
							$_order_currency = $order->get_meta( '_woocs_order_base_currency', true );
							$_woocs_order_base_currency = $order->get_meta( '_woocs_order_base_currency', true );
							$order->set_currency( $_order_currency );
							$order->set_currency( $_woocs_order_base_currency );
							$order->save();
						} else {
							$_order_currency = get_post_meta( $order_id, '_woocs_order_base_currency', true );
							$_woocs_order_base_currency = get_post_meta( $order_id, '_woocs_order_base_currency', true );
							update_post_meta( $order_id, '_order_currency', $_order_currency );
							update_post_meta( $order_id, '_order_currency', $_woocs_order_base_currency );
						}

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



	/** Add wallet module function
	 *
	 * @param [type] $payment_mode payment method.
	 * @return mixed
	 */
	public function wsfw_admin_mvx_list_modules( $payment_mode ) {
		$payment_mode['wallet_payment'] = __( 'Wallet', 'wallet-system-for-woocommerce' );
		return $payment_mode;
	}

	/**
	 * Make Wallet Rechargable Product tax free.
	 *
	 * @param string $tax_class is tax class to make free.
	 * @param [type] $product is the product on which free tax will be applied.
	 * @return string
	 */
	public function wsfw_admin_recharge_product_tax_class( $tax_class, $product ) {

		$wallet_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );

		$_is_enabled_wallet_recharege = get_option( 'wsfw_enable_wallet_recharge_tax_free' );

		if ( 'on' == $_is_enabled_wallet_recharege ) {
			if ( ! empty( $product ) ) {
				if ( $product->get_id() == $wallet_id ) {
					$tax_class = 'zero-rate';
					$product->set_tax_class( 'zero-rate' );
				}
			}
		}
		return $tax_class;
	}



	/**
	 * Remove tax from partial payment for woocommerce_calculated_total.
	 *
	 * @param [type] $cart_total is the current cart total.
	 * @param [type] $cart is the whole cart data.
	 * @return mixed
	 */
	public function wps_wsfw_woocommerce_calculated_total_for_tax( $cart_total, $cart ) {

		$cart_tatal_tax  = '';
		$fees = $cart->fees_api()->get_fees();
		foreach ( $fees as $key => $fee ) {

			if ( 'via_wallet_partial_payment' == $fee->id ) {
				if ( WC()->session->__isset( 'is_wallet_partial_payment_cart_total_value' ) ) {
					$cart_total_after_partial_payment = (float) WC()->session->get( 'is_wallet_partial_payment_cart_total_value' );

				}
			}
		}
		if ( ! empty( $cart_total_after_partial_payment ) ) {
			return $cart_total_after_partial_payment;
		}

		if ( ! empty( WC()->cart->get_cart_shipping_total() ) ) {
			if ( WC()->cart->get_cart_shipping_total() != 'Free!' ) {
				$cart_total = $cart_total + floatval( WC()->cart->get_cart_shipping_total() );
			} else {
				$cart_total = WC()->cart->get_cart_subtotal();
			}
		}

		return $cart_total;
	}

	/**
	 * Wallet Recharge restriction.
	 *
	 * @param [type] $available_gateways are all gateways available.
	 * @return mixed
	 */
	public function wps_wsfwp_add_wallet_recharge_message_restriction( $available_gateways ) {

		$wallet_product_id = get_option( 'wps_wsfw_rechargeable_product_id' );
		$restrict_gatewaay  = get_option( 'wps_wsfw_multiselect_wallet_recharge_restrict' );
		$all_gateway = WC()->payment_gateways()->payment_gateways();

		if ( ! empty( $restrict_gatewaay ) ) {

			if ( ! empty( WC()->cart ) ) {

				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product = $cart_item['data'];
					if ( ( $_product->get_id() == $wallet_product_id ) ) {
						foreach ( $restrict_gatewaay as $key => $value ) {
							if ( 'yes' == $all_gateway[ $value ]->enabled ) {

								if ( ! empty( $value ) ) {
									unset( $available_gateways[ $value ] );
								}
							}
						}
					}
				}
			}
		}

		return $available_gateways;
	}
}
