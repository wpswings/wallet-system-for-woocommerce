<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
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
 * @author     makewebbetter <webmaster@makewebbetter.com>
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
		wp_enqueue_style( 'mwb-public-min', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/css/mwb-public.min.css', array(), $this->version, 'all' );
	
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/wallet-system-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'wsfw_public_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name );
		wp_enqueue_script( 'mwb-public-min', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'public/js/mwb-public.min.js', array(), $this->version, 'all' );

	}


	/**
	 * Unset COD if wallet topup product in cart.
	 *
	 * @param array $available_gateways   all the available payment gateways.
	 */
	public function mwb_wsfw_restrict_payment_gateway( $available_gateways ) {
		if ( isset( $available_gateways['mwb_wsfw_wallet_payment_gateway'] ) ) {
			
			$mwb_cart_total = WC()->cart->total;
			$user_id        = get_current_user_id();
			$wallet_amount  = get_user_meta( $user_id, 'mwb_wallet', true );
			
			$wallet_amount  = empty( $wallet_amount ) ? 0 : $wallet_amount;

			if (  WC()->session->__isset( 'is_wallet_partial_payment' ) ) {
				unset( $available_gateways['mwb_wsfw_wallet_payment_gateway'] );	
			} elseif ( WC()->session->__isset( 'recharge_amount' ) ) {
				unset( $available_gateways['mwb_wsfw_wallet_payment_gateway'] );
				unset( $available_gateways['cod'] );
			} elseif ( isset( $wallet_amount ) && $wallet_amount >= 0 ) {
				if ( $wallet_amount < $mwb_cart_total ) {
					unset( $available_gateways['mwb_wsfw_wallet_payment_gateway'] );
				}
			} elseif ( isset( $wallet_amount ) && $wallet_amount <= 0 ) {
				unset( $available_gateways['mwb_wsfw_wallet_payment_gateway'] );
			} 
		}
		return $available_gateways;
	}

	/**
	 * Show wallet as discount ( when wallet amount is less than cart total ) in review order table
	 *
	 * @return void
	 */
	public function checkout_review_order_custom_field() {
		$mwb_cart_total = WC()->cart->total;
		$user_id        = get_current_user_id();
		if ( $user_id ) {
			$wallet_amount  = get_user_meta( $user_id, 'mwb_wallet', true );
		
			$wallet_amount  = empty( $wallet_amount ) ? 0 : $wallet_amount;
			if ( isset( $wallet_amount ) && $wallet_amount > 0 ) {
				if ( $wallet_amount < $mwb_cart_total || $this->is_enable_wallet_partial_payment() ) { ?>
					<tr class="partial_payment">
						<td><?php echo esc_html( 'Pay by wallet (', 'wallet-system-for-woocommerce' ). wc_price( $wallet_amount ) . ')'; ?></td>
						<td>
							<p class="form-row checkbox_field woocommerce-validated" id="partial_payment_wallet_field">
								<input type="checkbox" class="input-checkbox " name="partial_payment_wallet" id="partial_payment_wallet" value="enable" <?php checked( $this->is_enable_wallet_partial_payment(), true, true ) ?> data-walletamount="<?php esc_html_e( $wallet_amount ); ?>" >
							</p>
						</td>
					</tr>
				<?php }
			}


		}
		
	}

	/**
	 * Remove all session set during partial payment and wallet recharge
	 *
	 * @param int $order_id
	 * @return void
	 */
	public function remove_wallet_session( $order_id ) {
		$customer_id = get_current_user_id();
		if ( $customer_id > 0 ) {
			$walletamount = get_user_meta( $customer_id, 'mwb_wallet', true );

			if (  WC()->session->__isset( 'custom_fee' ) ) {
				WC()->session->__unset( 'custom_fee' );
				WC()->session->__unset( 'is_wallet_partial_payment' );	
			}

			if ( WC()->session->__isset( 'recharge_amount' ) ) {
				WC()->session->__unset( 'recharge_amount' );
			}
		}

	}

	/**
	 * Change wallet amount on order status change
	 *
	 * @param int $order_id
	 * @param string $old_status
	 * @param string $new_status
	 * @return void
	 */
	public function mwb_order_status_changed( $order_id, $old_status, $new_status ) {
		$order  = wc_get_order( $order_id );
		$userid = $order->user_id;
		$payment_method = $order->payment_method;
		$order_items = $order->get_items();
		$wallet_id = get_option( 'PC_rechargeable_product_id', '' );
		$walletamount = get_user_meta( $userid, 'mwb_wallet', true );
		foreach ( $order_items as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$total = $item->get_total();
			
			if ( isset( $product_id ) && ! empty( $product_id ) &&  $product_id == $wallet_id ) {

				$order_status = array( 'pending', 'on-hold', 'processing' );
				if ( in_array( $old_status, $order_status ) &&  'completed' == $new_status ) {
					$amount = $total;
					$walletamount += $total;
					update_user_meta( $userid, 'mwb_wallet', $walletamount );

					$transaction_type = 'Wallet credited through purchase <a href="' . admin_url('post.php?post='.$order_id.'&action=edit') . '" >#' . $order_id . '</a>';
					$transaction_data = array(
						'user_id'          => $userid,
						'amount'           => $amount,
						'payment_method'   => $payment_method,
						'transaction_type' => htmlentities( $transaction_type ),
						'order_id'         => $order_id,
						'note'             => '',
					);
					$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
					$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

				}
			}
		}
		
		foreach ( $order->get_fees() as $item_fee ) {
			$fee_name = $item_fee->get_name();
			$fee_total = $item_fee->get_total(); 
			if ( 'Via wallet' == $fee_name ) {
				$order_status = array( 'pending', 'on-hold' );
				$payment_status = array( 'processing', 'completed' );
				if ( in_array( $old_status, $order_status ) &&  in_array( $new_status, $payment_status ) ) {
					$fees = abs( $fee_total );
					$amount = $fees;
					if ( $walletamount < $fees ) {
						$walletamount = 0;
					} else {
						$walletamount -= $fees;
					}
					update_user_meta( $userid, 'mwb_wallet', $walletamount );
					$transaction_type = 'Wallet debited through purchasing <a href="' . admin_url('post.php?post='.$order_id.'&action=edit') . '" >#' . $order_id . '</a> as discount';

					$transaction_data = array(
						'user_id'          => $userid,
						'amount'           => $amount,
						'payment_method'   => $payment_method,
						'transaction_type' => htmlentities( $transaction_type ),
						'order_id'         => $order_id,
						'note'             => '',
			
					);
					$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
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
	public function mwb_wsfw_add_wallet_item( $items ) {
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		$items['mwb-wallet']  = __( 'Wallet', 'wallet-system-for-woocommerce' );
		$items['customer-logout'] = $logout;
		return $items;
	}
	
	/**
	 *  Register new endpoint to use for My Account page.
	 */
	public function mwb_wsfw_wallet_register_endpoint() {
		global $wp_rewrite;
		add_rewrite_endpoint( 'mwb-wallet', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'wallet-topup', EP_PERMALINK | EP_PAGES );
		add_rewrite_endpoint( 'wallet-transfer', EP_PERMALINK | EP_PAGES );
		add_rewrite_endpoint( 'wallet-withdrawal', EP_PERMALINK | EP_PAGES );
		add_rewrite_endpoint( 'wallet-transactions', EP_PERMALINK | EP_PAGES );
		$wp_rewrite->flush_rules();
	}

	/**
	 *  Add new query var.
	 *
	 * @param array $vars    Query variable.
	 */
	public function mwb_wsfw_wallet_query_var( $vars ) {
		$vars[] = 'mwb-wallet';
		return $vars;
	}
	
	/**
	 * Add content to the new endpoint.
	 */
	public function mwb_wsfw_display_wallet_endpoint_content() {
		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-public-display.php';
	}

	/**
     * Get WooCommerce cart total.
     * @return number
     */
    public function get_mwbwallet_cart_total() {
		$mwb_cart_total = WC()->cart->total;
        return $mwb_cart_total;
    }
	
	/**
	 * Check if enable partial payment.
	 * @return Boolean
	 */
	public function is_enable_wallet_partial_payment() {
		$is_enable = false;
		if ( is_user_logged_in() && ( ( ! is_null( wc()->session) && wc()->session->get( 'is_wallet_partial_payment', false ) ) ) ) {
			$is_enable = true;
		}
		return $is_enable;
	}

	/**
	 * Add wallet amount as fee in cart during partial payment
	 * @return void
	 */
	public function wsfw_add_wallet_discount( ) {
		
		if (  WC()->session->__isset( 'custom_fee' ) ) {
			
			$discount = (float) WC()->session->get( 'custom_fee' );
		}
		if ( $discount ) {
			$fee = array(
				'id' => 'via_wallet_partial_payment',
				'name' => __('Via wallet', 'wallet-system-for-woocommerce'),
				'amount' => (float) -1 * $discount,
			);
		}
		
		if ( $this->is_enable_wallet_partial_payment()  ) {
			wc()->cart->fees_api()->add_fee($fee);
		} else {
			$all_fees = wc()->cart->fees_api()->get_fees();
			if (isset($all_fees['via_wallet_partial_payment'])) {
				unset($all_fees['via_wallet_partial_payment']);
				wc()->cart->fees_api()->set_fees($all_fees);
			}
		}

	}

	/**
	 * Add wallet topup to cart
	 *
	 * @return void
	 */
	public function add_wallet_recharge_to_cart(){
		if (  WC()->session->__isset( 'wallet_recharge' ) ) {
			
			$wallet_recharge = WC()->session->get( 'wallet_recharge' );
			//check if product already in cart
			if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
				$found = false;
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->id == $wallet_recharge['productid'] ) {
						$found = true;
					}
					
				}
				// if product not found, add it
				if ( ! $found ) {
					add_action( 'woocommerce_before_cart', array( $this, 'add_cart_custom_notice' ) );
					WC()->session->__unset( 'recharge_amount' );
				}
					
			} else {
				// if no products in cart, add it
				WC()->cart->add_to_cart( $wallet_recharge['productid'] );
			}
			WC()->session->__unset( 'wallet_recharge' );
		}
	}

	/**
	 * Add notice on cart page if cart is already added with products
	 *
	 * @return void
	 */
	public function add_cart_custom_notice() {
		wc_print_notice( sprintf( '<span class="subscription-reminder">' .
			__('Sorry we cannot recharge wallet with other products, either %s cart or recharge later when cart is empty', 'wallet-system-for-woocommerce') . '</span>',
			__( 'empty', 'wallet-system-for-woocommerce')
		), 'error' );
	}

	/**
	 * Add notice on cart page if cart is already added with wallet topup
	 *
	 * @return void
	 */
	public function show_message_addto_cart( $passed, $product_id ) {
		$wallet_id = get_option( 'PC_rechargeable_product_id', '' );
		if ( ! empty( $wallet_id)  ) {
			if ( ! WC()->cart->is_empty() ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$_product = $values['data'];
					if ( $_product->id == $wallet_id ) {
						$passed = false;

						wc_add_notice( sprintf( '<span class="subscription-reminder">' .
							__('Sorry you cannot buy this product since wallet topup is added to cart. If you want to buy this product, please first remove wallet topup from cart.', 'wallet-system-for-woocommerce') . '</span>',
							__('empty', 'wallet-system-for-woocommerce')
						), 'error' );


					}
					
				}
			}
		}
		return $passed;
	}

	/**
	 * Update wallet top price in cart and checkout page
	 *
	 * @param object $cart_object
	 * @return void
	 */
	public function mwb_update_price_cart( $cart_object ) {
		$cart_items = $cart_object->cart_contents;
		if (  WC()->session->__isset( 'recharge_amount' ) ) {
			$wallet_recharge = WC()->session->get( 'recharge_amount' );
			$price = $wallet_recharge;
			
			if ( ! empty( $cart_items ) ) {
				foreach ( $cart_items as $key => $value ) {
					$value['data']->set_price( $price );
					//wc_delete_product_transients( $value['product_id'] );
				}
		  	}
		}
		
	}

	/**
	 * Unset session after wallet topup is removed from cart
	 *
	 * @param string $removed_cart_item_key
	 * @param object $cart
	 * @return void
	 */
	public function after_remove_wallet_from_cart( $removed_cart_item_key, $cart ) {
		$line_item = $cart->removed_cart_contents[ $removed_cart_item_key ];
		$product_id = $line_item[ 'product_id' ];
		$wallet_id = get_option( 'PC_rechargeable_product_id', '' );
		if ( $wallet_id ) {
			if ( $product_id == $wallet_id ) {
				WC()->session->__unset( 'recharge_amount' );
			}
		}
		
	}

	public function change_order_type( $order_id ) {
        $order     = wc_get_order( $order_id );
		$wallet_id = get_option( 'PC_rechargeable_product_id', '' );
        foreach ( $order->get_items() as $item ) {
            $product_id = $item->get_product_id();
            if ( isset( $product_id ) && ! empty( $product_id ) &&  $product_id == $wallet_id ) {
				$order_obj            = get_post( $order_id );
				$order_obj->post_type = 'wallet_shop_order';
				wp_update_post( $order_obj );
            }
        }
    }


}
