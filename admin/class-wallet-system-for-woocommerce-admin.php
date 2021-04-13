<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Wallet_System_For_Woocommerce_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function wsfw_admin_enqueue_styles( $hook ) {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'makewebbetter_page_wallet_system_for_woocommerce_menu' == $screen->id ) {

			wp_enqueue_style( 'mwb-wsfw-select2-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/wallet-system-for-woocommerce-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-wsfw-meterial-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-wsfw-meterial-css2', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'mwb-wsfw-meterial-lite', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'mwb-wsfw-meterial-icons-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );

			wp_enqueue_style( $this->plugin_name . '-admin-global', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/scss/wallet-system-for-woocommerce-admin-global.css', array( 'mwb-wsfw-meterial-icons-css' ), time(), 'all' );

			wp_enqueue_style( $this->plugin_name, WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/scss/wallet-system-for-woocommerce-admin.scss', array(), $this->version, 'all' );
			wp_enqueue_style( 'mwb-admin-min-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/mwb-admin.min.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function wsfw_admin_enqueue_scripts( $hook ) {

		$screen = get_current_screen();

		if ( isset( $screen->id ) && 'makewebbetter_page_wallet_system_for_woocommerce_menu' == $screen->id ) {
			wp_enqueue_script( 'mwb-wsfw-select2', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/wallet-system-for-woocommerce-select2.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( 'mwb-wsfw-metarial-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-wsfw-metarial-js2', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'mwb-wsfw-metarial-lite', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );

			wp_register_script( $this->plugin_name . 'admin-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-admin.js', array( 'jquery', 'mwb-wsfw-select2', 'mwb-wsfw-metarial-js', 'mwb-wsfw-metarial-js2', 'mwb-wsfw-metarial-lite' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name . 'admin-js',
				'wsfw_admin_param',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'reloadurl' => admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ),
					'wsfw_gen_tab_enable' => get_option( 'wsfw_radio_switch_demo' ),
				)
			);

			wp_enqueue_script( $this->plugin_name . 'admin-js' );

			wp_enqueue_script( 'mwb-admin-min-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/js/mwb-admin.min.js', array(), time(), false );

		}
	}

	/**
	 * Adding settings menu for Wallet System for WooCommerce.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_options_page() {
		global $submenu;
		if ( empty( $GLOBALS['admin_page_hooks']['mwb-plugins'] ) ) {
			add_menu_page( __( 'MakeWebBetter', 'wallet-system-for-woocommerce' ), __( 'MakeWebBetter', 'wallet-system-for-woocommerce' ), 'manage_options', 'mwb-plugins', array( $this, 'mwb_plugins_listing_page' ), WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/MWB_Grey-01.svg', 15 );
			$wsfw_menus = apply_filters( 'mwb_add_plugins_menus_array', array() );
			if ( is_array( $wsfw_menus ) && ! empty( $wsfw_menus ) ) {
				foreach ( $wsfw_menus as $wsfw_key => $wsfw_value ) {
					add_submenu_page( 'mwb-plugins', $wsfw_value['name'], $wsfw_value['name'], 'manage_options', $wsfw_value['menu_link'], array( $wsfw_value['instance'], $wsfw_value['function'] ) );
				}
			}

			// // add custom post type Withdrawal Request as submenu
			// add_submenu_page( 'mwb-plugins', 'Withdrawal Request',  'Withdrawal Request', 'edit_posts', 'edit.php?post_type=wallet_withdrawal' );
			
			add_submenu_page( '', 'Edit User Wallet',  '', 'edit_posts', 'mwb-edit-wallet', array( $this, 'edit_wallet_of_user' ) );
			
			add_submenu_page( '', 'Edit User Wallet',  '', 'edit_posts', 'mwb-user-wallet-transactions', array( $this, 'show_users_wallet_transactions' ) ); 
			
			add_submenu_page( 'mwb-plugins', 'Wallet Recharge Orders', 'Wallet Recharge Orders', 'edit_posts', 'wallet_shop_order', array( $this, 'show_wallet_orders' ) ); 
			
		}
	}

	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since   1.0.0
	 */
	public function mwb_wsfw_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'mwb-plugins', $submenu ) ) {
			if ( isset( $submenu['mwb-plugins'][0] ) ) {
				unset( $submenu['mwb-plugins'][0] );
			}
		}
	}


	/**
	 * Wallet System for WooCommerce wsfw_admin_submenu_page.
	 *
	 * @since 1.0.0
	 * @param array $menus Marketplace menus.
	 */
	public function wsfw_admin_submenu_page( $menus = array() ) {
		$menus[] = array(
			'name'            => __( 'Settings', 'wallet-system-for-woocommerce' ),
			'slug'            => 'wallet_system_for_woocommerce_menu',
			'menu_link'       => 'wallet_system_for_woocommerce_menu',
			'instance'        => $this,
			'function'        => 'wsfw_options_menu_html',
		);
		return $menus;
	}


	/**
	 * Wallet System for WooCommerce mwb_plugins_listing_page.
	 *
	 * @since 1.0.0
	 */
	public function mwb_plugins_listing_page() {
		$active_marketplaces = apply_filters( 'mwb_add_plugins_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			require WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/welcome.php';
		}
	}

	/**
	 * Wallet System for WooCommerce admin menu page.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_options_menu_html() {

		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/wallet-system-for-woocommerce-admin-dashboard.php';
	}


	/**
	 * Wallet System for WooCommerce admin menu page.
	 *
	 * @since    1.0.0
	 * @param array $wsfw_settings_general Settings fields.
	 */
	public function wsfw_admin_general_settings_page( $wsfw_settings_general ) {

		$wsfw_settings_general = array(
			// enable wallet
			array(
				'title' => __( 'Enable', 'wallet-system-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'This is switch field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'name'	=> 'PC_enable',
				'id'    => 'PC_enable',
				'value' => 'on',
				'class' => 'wsfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no' => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			// array(
			// 	'title' => __( 'Wallet Recharge', 'wallet-system-for-woocommerce' ),
			// 	'type'  => 'checkbox',
			// 	'description'  => __( 'Enable to allow customers to recharge their wallet', 'wallet-system-for-woocommerce' ),
			// 	'name'  => 'wsfw_enable_wallet_recharge',
			// 	'id'    => 'wsfw_enable_wallet_recharge',
			// 	'value' => '1',
			// 	'data-value' => get_option( 'wsfw_enable_wallet_recharge', '' ),
			// 	'class' => 'wsfw-checkbox-class',
			// 	'placeholder' => __( 'Checkbox Demo', 'wallet-system-for-woocommerce' ),
			// ),
			array(
				'title' => __( 'Wallet Recharge', 'wallet-system-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'Enable to allow customers to recharge their wallet', 'wallet-system-for-woocommerce' ),
				'name'  => 'wsfw_enable_wallet_recharge',
				'id'    => 'wsfw_enable_wallet_recharge',
				'value' => 'on',
				'class' => 'wsfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no' => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Refund to wallet', 'wallet-system-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'This is switch field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'name'	=> 'PC_allow_refund_to_wallet',
				'id'    => 'PC_allow_refund_to_wallet',
				'value' => 'on',
				'class' => 'wsfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no' => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Send email on wallet amount update to customers', 'wallet-system-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'This is switch field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'name'	=> 'PC_enable_email_notification_for_wallet_update',
				'id'    => 'PC_enable_email_notification_for_wallet_update',
				'value' => '',
				'class' => 'wsfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no' => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
		
			array(
				'type'  => 'submit',
				'name' => 'wsfw_button_demo',
				'id'    => 'wsfw_button_demo',
				'button_text' => __( 'Save Settings', 'wallet-system-for-woocommerce' ),
				'class' => 'wsfw-button-class',
			),
		);
		return $wsfw_settings_general;
	}

	/**
	 * Wallet System for WooCommerce admin menu page.
	 *
	 * @since    1.0.0
	 * @param array $wsfw_settings_template Settings fields.
	 */
	public function wsfw_admin_template_settings_page( $wsfw_settings_template ) {
		$wsfw_settings_template = array(
			array(
				'title' => __( 'Text Field Demo', 'wallet-system-for-woocommerce' ),
				'type'  => 'text',
				'description'  => __( 'This is text field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_text_demo',
				'value' => '',
				'class' => 'wsfw-text-class',
				'placeholder' => __( 'Text Demo', 'wallet-system-for-woocommerce' ),
			),
			array(
				'title' => __( 'Number Field Demo', 'wallet-system-for-woocommerce' ),
				'type'  => 'number',
				'description'  => __( 'This is number field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_number_demo',
				'value' => '',
				'class' => 'wsfw-number-class',
				'placeholder' => '',
			),
			array(
				'title' => __( 'Password Field Demo', 'wallet-system-for-woocommerce' ),
				'type'  => 'password',
				'description'  => __( 'This is password field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_password_demo',
				'value' => '',
				'class' => 'wsfw-password-class',
				'placeholder' => '',
			),
			array(
				'title' => __( 'Textarea Field Demo', 'wallet-system-for-woocommerce' ),
				'type'  => 'textarea',
				'description'  => __( 'This is textarea field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_textarea_demo',
				'value' => '',
				'class' => 'wsfw-textarea-class',
				'rows' => '5',
				'cols' => '10',
				'placeholder' => __( 'Textarea Demo', 'wallet-system-for-woocommerce' ),
			),
			array(
				'title' => __( 'Select Field Demo', 'wallet-system-for-woocommerce' ),
				'type'  => 'select',
				'description'  => __( 'This is select field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_select_demo',
				'value' => '',
				'class' => 'wsfw-select-class',
				'placeholder' => __( 'Select Demo', 'wallet-system-for-woocommerce' ),
				'options' => array(
					'' => __( 'Select option', 'wallet-system-for-woocommerce' ),
					'INR' => __( 'Rs.', 'wallet-system-for-woocommerce' ),
					'USD' => __( '$', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Multiselect Field Demo', 'wallet-system-for-woocommerce' ),
				'type'  => 'multiselect',
				'description'  => __( 'This is multiselect field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_multiselect_demo',
				'value' => '',
				'class' => 'wsfw-multiselect-class mwb-defaut-multiselect',
				'placeholder' => '',
				'options' => array(
					'default' => __( 'Select currency code from options', 'wallet-system-for-woocommerce' ),
					'INR' => __( 'Rs.', 'wallet-system-for-woocommerce' ),
					'USD' => __( '$', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Checkbox Field Demo', 'wallet-system-for-woocommerce' ),
				'type'  => 'checkbox',
				'description'  => __( 'This is checkbox field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_checkbox_demo',
				'value' => '',
				'class' => 'wsfw-checkbox-class',
				'placeholder' => __( 'Checkbox Demo', 'wallet-system-for-woocommerce' ),
			),

			array(
				'title' => __( 'Radio Field Demo', 'wallet-system-for-woocommerce' ),
				'type'  => 'radio',
				'description'  => __( 'This is radio field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_radio_demo',
				'value' => '',
				'class' => 'wsfw-radio-class',
				'placeholder' => __( 'Radio Demo', 'wallet-system-for-woocommerce' ),
				'options' => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no' => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title' => __( 'Enable', 'wallet-system-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'This is switch field demo follow same structure for further use.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_radio_switch_demo',
				'value' => '',
				'class' => 'wsfw-radio-switch-class',
				'options' => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no' => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),

			array(
				'type'  => 'button',
				'id'    => 'wsfw_button_demo',
				'button_text' => __( 'Button Demo', 'wallet-system-for-woocommerce' ),
				'class' => 'wsfw-button-class',
			),
		);
		return $wsfw_settings_template;
	}

	/**
	* Wallet System for WooCommerce save tab settings.
	*
	* @since 1.0.0
	*/
	public function wsfw_admin_save_tab_settings() {
		global $wsfw_mwb_wsfw_obj;
		if ( isset( $_POST['wsfw_button_demo'] ) ) {
			$mwb_wsfw_gen_flag = false;
			$wsfw_genaral_settings = apply_filters( 'wsfw_general_settings_array', array() );
			$wsfw_button_index = array_search( 'submit', array_column( $wsfw_genaral_settings, 'type' ) );
			if ( isset( $wsfw_button_index ) && ( null == $wsfw_button_index || '' == $wsfw_button_index ) ) {
				$wsfw_button_index = array_search( 'button', array_column( $wsfw_genaral_settings, 'type' ) );
			}
			if ( isset( $wsfw_button_index ) && '' !== $wsfw_button_index ) {
				unset( $wsfw_genaral_settings[$wsfw_button_index] );
				if ( is_array( $wsfw_genaral_settings ) && ! empty( $wsfw_genaral_settings ) ) {
					foreach ( $wsfw_genaral_settings as $wsfw_genaral_setting ) {
						if ( isset( $wsfw_genaral_setting['id'] ) && '' !== $wsfw_genaral_setting['id'] ) {
							if ( isset( $_POST[$wsfw_genaral_setting['id']] ) ) {
								update_option( $wsfw_genaral_setting['id'], $_POST[$wsfw_genaral_setting['id']] );
							} else {
								update_option( $wsfw_genaral_setting['id'], '' );
							}
						}else{
							$mwb_wsfw_gen_flag = true;
						}
					}
				}
				if ( $mwb_wsfw_gen_flag ) {
					$mwb_wsfw_error_text = esc_html__( 'Id of some field is missing', 'wallet-system-for-woocommerce' );
					$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
				}else{
					$mwb_wsfw_error_text = esc_html__( 'Settings saved !', 'wallet-system-for-woocommerce' );
					$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
				}

				$enable = get_option( 'PC_enable', '' );
				$wallet_payment_enable = get_option( 'woocommerce_mwb_wsfw_wallet_payment_gateway_settings' );
				if ( isset( $enable ) && '' === $enable ) { 
					if ( $wallet_payment_enable ) {
						$wallet_payment_enable['enabled'] = 'no';
						update_option( 'woocommerce_mwb_wsfw_wallet_payment_gateway_settings', $wallet_payment_enable );
					}
				} else {
					if ( $wallet_payment_enable ) {
						$wallet_payment_enable['enabled'] = 'yes';
						update_option( 'woocommerce_mwb_wsfw_wallet_payment_gateway_settings', $wallet_payment_enable );
					}
				}
			}
		}
	}

	/**
	 * Add wallet edit fields in admin and user profile page
	 *
	 * @param object $user
	 * @return void
	 */
	public function  wsfw_add_user_wallet_field( $user ) {
		global  $woocommerce;
   		$currency = get_woocommerce_currency_symbol();
		$wallet_bal = get_user_meta( $user->ID, 'mwb_wallet', true );
		?>
		<h2><?php esc_html_e( 'Wallet Balance: ', 'wallet-system-for-woocommerce' );
		echo wc_price( $wallet_bal ); ?> </h2>
			<table class="form-table">
				<tr>
					<th><label for="mwb_wallet">Amount</label></th>
					<td>
						<input type="number" name="mwb_wallet" id="mwb_wallet">
						<span class="description">Add/deduct money to/from wallet</span>
						<p class="error" ></p>
					</td>
				</tr>
				<tr>
					<th><label for="mwb_wallet">Action</label></th>
					<td>
						<select name="mwb_edit_wallet_action" id="mwb_edit_wallet_action">
							<option value="credit"><?php esc_html_e( 'Credit', 'wallet-system-for-woocommerce' ); ?></option>
							<option value="debit"><?php esc_html_e( 'Debit', 'wallet-system-for-woocommerce' ); ?></option>
						</select>
						<span class="description"><?php esc_html_e( 'Whether want to add amount or deduct it from wallet', 'wallet-system-for-woocommerce' ); ?></span>
					</td>
				</tr>
			</table>
		<?php
	}

	/**
	 * Save wallet edited fields in usermeta for admin and users
	 *
	 * @param int $user_id
	 * @return void
	 */
	public function wsfw_save_user_wallet_field( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id) ) {
			$wallet_amount = sanitize_text_field( $_POST['mwb_wallet'] );
			$action = sanitize_text_field( $_POST['mwb_edit_wallet_action'] );
			$mwb_wallet = get_user_meta( $user_id, 'mwb_wallet' , true );
			if ( 'credit' === $action ) {
				$mwb_wallet = floatval( $mwb_wallet ) + floatval($wallet_amount );
				$transaction_type = 'Credited by admin';
			} elseif ( 'debit' === $action ) {
				if ( $mwb_wallet < $wallet_amount ) {
					$mwb_wallet = 0;
				} else {
					$mwb_wallet = floatval( $mwb_wallet ) -  floatval( $wallet_amount );
				}
				$transaction_type = 'Dedited by admin';
			}
			update_user_meta( $user_id, 'mwb_wallet', abs( $mwb_wallet ) );

			$transaction_data = array(
				'user_id'          => $user_id,
				'amount'           => $wallet_amount,
				'payment_method'   => 'Manually By Admin',
				'transaction_type' => $transaction_type,
				'order_id'         => '',
				'note'             => '',
	
			);
			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
			$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
			

		}
	}

	/**
	 * Add wallet column to user table.
	 *
	 * @param array $columns columns.
	 */
	public function wsfw_add_wallet_col_to_user_table( $columns ) {
		$new = array();
		foreach ( $columns as $key => $title ) {
			if ( 'posts' == $key ) {
				$new['mwb_wallet_bal'] = esc_html__( 'Wallet Balance', 'wallet-system-for-woocommerce' );
				$new['mwb_wallet_actions'] = esc_html__( 'Wallet Actions', 'wallet-system-for-woocommerce' );
			}
			$new[ $key ] = $title;
		}
		return $new;
	}

	/**
	 * Add wallet column to user table.
	 *
	 * @param string $val value.
	 * @param array  $column_name columns.
	 * @param string $user_id user id.
	 */
	public function wsfw_add_user_wallet_col_data( $value, $column_name, $user_id ) {
		$wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );
		if ( empty( $wallet_bal ) ) {
			$wallet_bal = 0;
		}
		if ( 'mwb_wallet_bal' === $column_name ) {
			return wc_price( $wallet_bal );
		}
		if ( 'mwb_wallet_actions' === $column_name ) {
			$html = '<p><a href="' . esc_url( admin_url( "?page=mwb-edit-wallet&id=$user_id" ) ). '" title="Edit Wallet" class="button wallet-manage"></a> 
			<a class="button view-transactions" href="' . esc_url( admin_url( "admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=mwb-user-wallet-transactions&id=$user_id" ) ). '" title="View Transactions" ></a></p>';
			return $html;
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
	public function wsfw_order_status_changed_admin( $order_id, $old_status, $new_status ) {
		$order  = wc_get_order( $order_id );
		$userid = $order->user_id;
		$order_items = $order->get_items();
		$payment_method = $order->payment_method;
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
					$transaction_type = 'Wallet debited through purchasing <a href="' . admin_url('post.php?post='.$order_id.'&action=edit') . '" >#' . $order_id . '</a>';
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
	 * Wallet Payment Gateway impoting wallet page.
	 *
	 * @since    1.0.0
	 * @param array $wsfw_settings_import_wallet Importing fields.
	 */
	public function wsfw_admin_import_wallets_page( $wsfw_settings_import_wallet ) {
		$wsfw_settings_import_wallet = array(
			
			array(
				'title'       => __( 'Import wallet balance from CSV file', 'wallet-system-for-woocommerce' ),
				'type'        => 'file',
				'description' => __( 'Upload CSV file for adding wallet balance to users', 'wallet-system-for-woocommerce' ),
				'name'        => 'import_wallet_for_users',
				'id'          => 'import_wallet_for_users',
				'value'       => '',
				'class'       => 'wsfw-number-class',
			),

			array(
				'type'        => 'submit',
				'name'        => 'import_wallets',
				'id'          => 'import_wallets',
				'button_text' => __( 'Import Wallet', 'wallet-system-for-woocommerce' ),
				'class'       => 'wsfw-button-class',
			),
		);
		return $wsfw_settings_import_wallet;
	}

	/**
	 * Settings for wallet withdrawal page
	 *
	 * @param array $wsfw_widthdrawal_setting
	 * @return void
	 */
	public function wsfw_admin_withdrawal_setting_page( $wsfw_widthdrawal_setting ) {

		$wallet_methods = get_option( 'wallet_withdraw_methods', '' );
		if ( ! empty( $wallet_methods ) && is_array( $wallet_methods ) ) {
			$bank_transfer = $wallet_methods['banktransfer']['value'];
			$paypal = $wallet_methods['paypal']['value'];
		}
		$wsfw_widthdrawal_setting = array(
			
			array(
				'title'       => __( 'Minimum Withdrawal Amount ( ', 'wallet-system-for-woocommerce' ).get_woocommerce_currency_symbol(). ' )',
				'type'        => 'number',
				'description' => __( 'Minimum amount needed to be withdrawal from wallet.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wallet_minimum_withdrawn_amount',
				'id'          => 'wallet_minimum_withdrawn_amount',
				'value'       => get_option( 'wallet_minimum_withdrawn_amount', '' ),
				'class'       => 'wsfw-number-class',
			),
			array(
				'title'       => __( 'Withdraw Methods', 'wallet-system-for-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( 'Direct Bank Transfer', 'wallet-system-for-woocommerce' ),
				'name'        => 'wallet_withdraw_methods[banktransfer]', 
				'id'          => 'enable_bank_transfer',
				'value'       => 'Bank Transfer',
				'data-value'  => $bank_transfer,
				'class'       => 'wsfw-checkbox-class',
			),
			array(
				'title'       => __( '', 'wallet-system-for-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( 'Paypal', 'wallet-system-for-woocommerce' ),
				'name'        => 'wallet_withdraw_methods[paypal]', 
				'id'          => 'enable_paypal',
				'value'       => 'PayPal',
				'data-value'  => $paypal,
				'class'       => 'wsfw-checkbox-class',
			),
			array(
				'type'        => 'submit',
				'name'        => 'save_withdrawn_settings',
				'id'	      => 'save_withdrawn_settings',
				'button_text' => __( 'Save Settings', 'wallet-system-for-woocommerce' ),
				'class'       => 'wsfw-button-class',
			),
		);
		return $wsfw_widthdrawal_setting;
	}

	/**
	 * Return array of users with wallet data
	 *
	 * @return void
	 */
	public function export_users_wallet() {
		
		$userdata = array();
		$userdata[0] = array( 'User Id', 'Wallet Balance' );
		$users = get_users();
		foreach ( $users as $key => $user ) {
			$user_id = $user->ID;
			$wallet_balance = get_user_meta( $user_id, 'mwb_wallet', true );
			if ( empty( $wallet_balance )) {
				$userdata[] = array( $user_id, 0 );
			} else {
				$userdata[] = array( $user_id, $wallet_balance );
			}

		}
		wp_send_json($userdata);

	}

	/**
	 * update wallet and status on changing status of wallet request
	 *
	 * @return void
	 */
	public function change_wallet_withdrawan_status() {
		$update = true;
		if ( empty( $_POST['withdrawal_id'] ) ) {
			$mwb_wsfw_error_text = esc_html__( 'Withdrawal Id is not given', 'wallet-system-for-woocommerce' );
			$message = array( 'msg' => $mwb_wsfw_error_text, 'msgType' => 'error' );
			$update = false;
		}
		if ( empty( $_POST['user_id'] ) ) {
			$mwb_wsfw_error_text = esc_html__( 'User Id is not given', 'wallet-system-for-woocommerce' );
			$message = array( 'msg' => $mwb_wsfw_error_text, 'msgType' => 'error' );
			$update = false;
		}
		if ( $update ) {
			$updated_status = sanitize_text_field( $_POST['status'] );
			$withdrawal_id = sanitize_text_field( $_POST['withdrawal_id'] );
			$user_id = sanitize_text_field( $_POST['user_id'] );
			$withdrawal_request = get_post( $withdrawal_id );
			if ( 'approved' === $updated_status ) {
				$withdrawal_amount = get_post_meta( $withdrawal_id, 'mwb_wallet_withdrawal_amount', true );
				if ( $user_id ) {
					$walletamount = get_user_meta( $user_id, 'mwb_wallet', true );
					if ( $walletamount < $withdrawal_amount ) {
						$walletamount = 0;
					} else {
						$walletamount -= $withdrawal_amount;
					}
					$update_wallet = update_user_meta( $user_id, 'mwb_wallet', $walletamount );
					delete_user_meta( $user_id, 'disable_further_withdrawal_request' );
					if ( $update_wallet ) {
						$withdrawal_request->post_status = 'approved';
						wp_update_post( $withdrawal_request );
					}
					$transaction_type = 'Wallet debited through user withdrawing request <a href="#" >#' . $withdrawal_id . '</a>';
					$transaction_data = array(
						'user_id'          => $user_id,
						'amount'           => $withdrawal_amount,
						'payment_method'   => 'Manually By Admin',
						'transaction_type' => htmlentities( $transaction_type ),
						'order_id'         => $withdrawal_id,
						'note'             => '',
		
					);
					$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
					$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
					if ( $result ) {
						$mwb_wsfw_error_text = esc_html__( 'Wallet withdrawan request is approved for user #'.$user_id, 'wallet-system-for-woocommerce' );
						$message = array( 'msg' => $mwb_wsfw_error_text, 'msgType' => 'success' );
					} else {
						$mwb_wsfw_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
						$message = array( 'msg' => $mwb_wsfw_error_text, 'msgType' => 'error' );
					}
				};
			}
			if ( 'rejected' === $updated_status ) {
				$withdrawal_amount = get_post_meta( $withdrawal_id, 'mwb_wallet_withdrawal_amount', true );
				if ( $user_id ) {
					$withdrawal_request->post_status = 'rejected';
					wp_update_post( $withdrawal_request );
					delete_user_meta( $user_id, 'disable_further_withdrawal_request' );
					$mwb_wsfw_error_text = esc_html__( 'Wallet withdrawan request is rejected for user #'.$user_id, 'wallet-system-for-woocommerce' );
					$message = array( 'msg' => $mwb_wsfw_error_text, 'msgType' => 'success' );
				};
			}
			if ( 'pending' === $updated_status ) {
				$withdrawal_amount = get_post_meta( $withdrawal_id, 'mwb_wallet_withdrawal_amount', true );
				if ( $user_id ) {
					$withdrawal_request->post_status = 'pending';
					wp_update_post( $withdrawal_request );
					$mwb_wsfw_error_text = esc_html__( 'Wallet withdrawan request status is changed to pending for user #'.$user_id, 'wallet-system-for-woocommerce' );
					$message = array( 'msg' => $mwb_wsfw_error_text, 'msgType' => 'success' );
				};
			}
		}
		wp_send_json( $message );
		
	}

	/**
	 * Register new custom post type wallet_withdrawal and custom post status
	 *
	 * @return void
	 */
	public function register_withdrawal_post_type() {
		register_post_type(
			'wallet_withdrawal',
			array(
				'labels'          => array(
					'name'               => __( 'Wallet Withdrawal Requests', 'wallet-system-for-woocommerce' ),
					'singular_name'      => __( 'Wallet Request', 'wallet-system-for-woocommerce' ),
					'all_items'          => __( 'Withdrawal Requests', 'wallet-system-for-woocommerce' ),
					'view_item'          => __( 'View Withdrawal Request', 'wallet-system-for-woocommerce' ),
					'edit_item'          => __( 'Edit Withdrawal Request', 'wallet-system-for-woocommerce' ),
					'update_item'        => __( 'Update Withdrawal Request', 'wallet-system-for-woocommerce' ),
					'search_items'       => __( 'Search', 'wallet-system-for-woocommerce' ),
					'not_found'          => __( 'Not Found Withdrawal Request', 'wallet-system-for-woocommerce' ),
					'not_found_in_trash' => __( 'Not found in Trash', 'wallet-system-for-woocommerce' ),
				),
				'description'     => 'Merchant can see all withdrawal request of users',
				'supports'        => array( 'title', 'custom-fields' ),
				'public'          => true,
				'rewrite'         => array( 'slug' => 'wallet_withdrawal' ),
				'menu_icon'       => 'dashicons-groups',
				'show_in_menu'    => false,
				'capability_type' => 'post',
				'show_ui'         => true,
			)
		);
		// register custom status rejected
		register_post_status('approved', array(
			'label'                     => _x( 'Approved', 'wallet-system-for-woocommerce' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Approved <span class="count">(%s)</span>', 'Approved <span class="count">(%s)</span>' ),
		) );
		// register custom status rejected
		register_post_status('rejected', array(
			'label'                     => _x( 'Rejected', 'wallet-system-for-woocommerce' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>' ),
		) );

	}

	public function wsfw_append_wallet_status_list() {
		global $post;
		$label = '';
		if( $post->post_type == 'wallet_withdrawal' ) {
			if( $post->post_status == 'approved' ){
				$complete = ' selected="selected"';
				$label = "<span id='post-status-display'> Approved</span>";
				$selected = 'selected';
			}
			if( $post->post_status == 'rejected' ){
				$label = "<span id='post-status-display'> Rejected</span>";
				$selected = 'selected';
			}

			echo '<script>
			jQuery(document).ready(function($){
				$(".misc-pub-post-status #post-status-display").append("<span id=\"post-status-display\"> '.$label.' </span>");
				$("select#post_status").append("<option value=\"approved\" >Approved</option><option value=\"rejected\" >Rejected</option>");
				
			});
			</script>
			';
		}
	}

	public function display_archive_state( $states ) {
		global $post;
		$arg = get_query_var( 'post_status' );
		if( $arg != 'approved' ){
			if($post->post_status == 'approved'){
				echo "<script>
				jQuery(document).ready( function() {
				jQuery( '#post-status-display' ).text( 'Approved' );
				});
				</script>";
				return array('Approved');
			}
		}
		if( $post->post_type == 'wallet_withdrawal') {
			if ( get_query_var( 'post_status' ) != 'approved' ) { // not for pages with all posts of this status
				if ( $post->post_status == 'approved' ) {
					return array( 'approved' );
				}
			}
		}
		return $states;
		}

	/**
	 * Add custom columns related to wallet withdrawal
	 *
	 * @param array $columns
	 * @return array
	 */
	public function wsfw_add_columns_to_withdrawal( $columns ) {
		unset( $columns['author'] );
		foreach ( $columns as $key => $column ) {
			if ( 'title' === $key ) {
				$columns[ 'withdrawal_id' ] = 'Withdrawal ID';
				$columns[ $key ] = 'Username';
			}
			if ( 'date' === $key ) {
				unset( $columns[$key] );
				$columns['email'] = esc_html__( 'Email', 'wallet-system-for-woocommerce' );
				$columns['withdrawal_amount'] = esc_html__( 'Amount', 'wallet-system-for-woocommerce' );
				$columns['payment_method'] = esc_html__( 'Payment Method', 'wallet-system-for-woocommerce' );
				$columns['status'] = esc_html__( 'Status', 'wallet-system-for-woocommerce' );
				$columns[ $key ] = $column;
			}		
		}
    	return $columns;
	}

	/**
	 * Show custom column data in withrawal request custom post type table list
	 *
	 * @param string $column_name
	 * @param int $post_id
	 * @return void
	 */
	public function wsfw_show_withdrawal_columns_data( $column_name, $post_id ) {
		
		switch ( $column_name ) {
			case 'withdrawal_id' :
				echo $post_id;
				break;
			case 'email' :
				$user_id = get_post_meta( $post_id , 'wallet_user_id' , true ); 
				if ( $user_id ) {
					$user = get_user_by( 'id', $user_id );
					$useremail = $user->user_email;
					esc_html_e( $useremail, 'wallet-system-for-woocommerce' );
				}
				break;
			case 'withdrawal_amount' :
				$withdrawal_amount = get_post_meta( $post_id, 'mwb_wallet_withdrawal_amount', true );
				if ( $withdrawal_amount ) {
					echo wc_price( $withdrawal_amount );
				}
				break;
			case 'payment_method' :
				esc_html_e( get_post_meta( $post_id , 'wallet_payment_method' , true ) , 'wallet-system-for-woocommerce' );
				break;
			case 'status' :
				$post = get_post( $post_id );
				esc_html_e(  $post->post_status, 'wallet-system-for-woocommerce' );
				//esc_html_e( get_post_meta( $post_id , 'withdrawal_request_status' , true ) , 'wallet-system-for-woocommerce' );
				break;	
		}
		
	}

	/**
	 * Update status of withdrawal requesting as approved
	 *
	 * @param int $post_id
	 * @param object $post
	 * @return void
	 */
	public function wsfw_enable_withdrawal_request( $post_id, $post ) {
		// echo $post->post_status;
		// die;
		$post_status = $post->post_status;
		if ( 'approved' === $post_status ) {
			$withdrawal_amount = get_post_meta( $post_id, 'mwb_wallet_withdrawal_amount', true );
			//update_post_meta( $post_id, 'withdrawal_request_status', 'Approved' );
			
			$user_id = get_post_meta( $post_id, 'wallet_user_id', true );
			$payment_method = get_post_meta( $post_id, 'wallet_payment_method', true );
			if ( $user_id ) {
				$walletamount = get_user_meta( $user_id, 'mwb_wallet', true );
				if ( $walletamount < $withdrawal_amount ) {
					$walletamount = 0;
				} else {
					$walletamount -= $withdrawal_amount;
				}
				update_user_meta( $user_id, 'mwb_wallet', $walletamount );
				delete_user_meta( $user_id, 'disable_further_withdrawal_request' );

				$transaction_type = 'Wallet debited through user withdrawing request <a href="#" >#' . $post_id . '</a>';
				$transaction_data = array(
					'user_id'          => $user_id,
					'amount'           => $withdrawal_amount,
					'payment_method'   => $payment_method,
					'transaction_type' => htmlentities( $transaction_type ),
					'order_id'         => $post_id,
					'note'             => '',
		
				);
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

			};
		}
	}

	/**
	 * Settings for wallet in frontend
	 *
	 * @param array $wsfw_settings_wallet
	 * @return array
	 */
	public function wsfw_admin_wallet_setting_page( $wsfw_settings_wallet ) {
		$wsfw_settings_wallet = array(

			
			// array(
			// 	'title' => __( 'Minimum Amount ( ', 'wallet-system-for-woocommerce' ).get_woocommerce_currency_symbol(). ' )',
			// 	'type'  => 'number',
			// 	'description'  => __( 'Minimum amount of Topup', 'wallet-system-for-woocommerce' ),
			// 	'name' => 'wsfw_wallet_min_topup_amount',
			// 	'id'    => 'wsfw_wallet_min_topup_amount',
			// 	'value' => get_option( 'wsfw_wallet_min_topup_amount', '' ),
			// 	'class' => 'wsfw-number-class',
			// 	'placeholder' => '',
			// ),
			// array(
			// 	'title' => __( 'Maximum Amount ( ', 'wallet-system-for-woocommerce' ).get_woocommerce_currency_symbol(). ' )',
			// 	'type'  => 'number',
			// 	'description'  => __( 'Maximum amount of Topup', 'wallet-system-for-woocommerce' ),
			// 	'name' => 'wsfw_wallet_max_topup_amount',
			// 	'id'    => 'wsfw_wallet_max_topup_amount',
			// 	'value' => get_option( 'wsfw_wallet_max_topup_amount', '' ),
			// 	'class' => 'wpg-number-class',
			// 	'placeholder' => '',
			// ),
			array(
				'type'  => 'submit',
				'name' => 'wallet_topup_setting',
				'id'    => 'wallet_topup_setting',
				'button_text' => __( 'Save Changes', 'wallet-system-for-woocommerce' ),
				'class' => 'wsfw-button-class', 'wsfw-update',
			),
		);
		return $wsfw_settings_wallet;
	}

	/**
	 * Fields for updating wallet of all users at bulk 
	 *
	 * @param array $wsfw_update_wallet
	 * @return array
	 */
	public function wsfw_admin_update_wallet_page( $wsfw_update_wallet ) {
		$wsfw_update_wallet = array(
			// amount field
			array(
				'title' => __( 'Amount ( ', 'wallet-system-for-woocommerce' ).get_woocommerce_currency_symbol(). ' )',
				'type'  => 'number',
				'description'  => __( 'Certain amount want to add/deduct from all users wallet', 'wallet-system-for-woocommerce' ),
				'name' => 'wsfw_wallet_amount_for_users',
				'id'    => 'wsfw_wallet_amount_for_users',
				'value' => '',
				'class' => 'wsfw-number-class',
				'placeholder' => '',
			),
			// wallet action
			array(
				'title' => __( 'Action', 'wallet-system-for-woocommerce' ),
				'type'  => 'oneline-radio',
				'description'  => __( 'Whether want to add/deduct certain amount from wallet of all users', 'wallet-system-for-woocommerce' ),
				'name'    => 'wsfw_wallet_action_for_users',
				'id'    => 'wsfw_wallet_action_for_users',
				'value' => '',
				'class' => 'wsfw-radio-class',
				'placeholder' => __( 'Radio Demo', 'wallet-system-for-woocommerce' ),
				'options' => array(
					'credit' => __( 'Credit', 'wallet-system-for-woocommerce' ),
					'debit' => __( 'Debit', 'wallet-system-for-woocommerce' ),
				),
			),
		
			array(
				'type'  => 'button',
				'name' => 'update_wallet',
				'id'    => 'update_wallet',
				'button_text' => __( 'Update Wallet', 'wallet-system-for-woocommerce' ),
				'class' => 'wsfw-button-class',
			),
		);
		return $wsfw_update_wallet;
	}
	
	/**
	 * Add css, add order button in admin panel
	 *
	 * @return void
	 */
	public function custom_code_in_head() {
		echo '<style type="text/css">
		.mwb_wallet_actions .wallet-manage::after {
			font-family: Dashicons;
			font-weight: 400;
			text-transform: none;
			-webkit-font-smoothing: antialiased;
			text-indent: 0;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			text-align: center;
			content: "\f111";
			margin: 0;
		}
		.mwb_wallet_actions .view-transactions::after {
			font-family: Dashicons;
			font-weight: 400;
			text-transform: none;
			-webkit-font-smoothing: antialiased;
			text-indent: 0;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			text-align: center;
			content: "\f177";
			margin: 0;
		}
		.wallet-status {
			text-transform:capitalize;
			display: inline-flex;
			line-height: 2.5em;
			color: #777;
			border-radius: 4px;
			border-bottom: 1px solid rgba(0,0,0,.05);
			margin: -.25em 0;
			cursor: inherit!important;
			white-space: nowrap;
			max-width: 100%;
		}	
		.wallet-status span {
			margin: 0 1em;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.column-status {
			text-transform: capitalize;
		}
		.order-status.status-on-hold {
			background: #f8dda7;
			color: #94660c;
		}
		.order-status.status-processing {
			background: #c6e1c6;
			color: #5b841b;
		}
		.order-status.status-completed {
			background: #c8d7e1;
			color: #2e4453;
		}
		.order-status.status-failed {
			background: #eba3a3;
			color: #761919;
		}
		.order-status.status-trash {
			background: #eba3a3;
			color: #761919;
		}
		.order-status.status-cancelled, .order-status.status-pending, .order-status.status-refunded  {
			background: #e5e5e5;
		}
		.wallet_shop_order .wp-list-table tbody .column-status {
			padding: 1.2em 10px;
			line-height: 26px;
		}
		.form-table td .error {
			color:red;
		}
		</style>
    	';

		global $current_screen;
		if ( 'makewebbetter_page_wallet_shop_order' == $current_screen->id ) {
			$url = admin_url( 'post-new.php?post_type=wallet_shop_order' );  ?>
			<script type="text/javascript">
				jQuery(document).ready( function($) {
					jQuery(jQuery(".wrap h1")[0]).append("<a href='<?php esc_attr_e( $url, 'wallet-system-for-woocommerce' ); ?>' class='add-new-h2'>Add Order</a>");
				});
			</script>
		<?php }

		if ( isset( $current_screen->id ) && ( 'profile' == $current_screen->id  || 'user-edit' == $current_screen->id ) ) { ?>
		<script>
		jQuery(document).ready(function() { 
			jQuery(document).on( 'blur','#mwb_wallet', function(){
				var amount = jQuery('#mwb_wallet').val();
				if ( amount <= 0 ) {
					jQuery('.error').show();
					jQuery('.error').html('Enter amount greater than 0');
				} else {
					jQuery('.error').hide();
				}
			
			});
		});
		</script>
		<?php }

	}

	/**
	 * Include template for wallet edit page
	 *
	 * @return void
	 */
	public function edit_wallet_of_user() {
		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'admin/partials/mwb-edit-wallet.php';
	}

	/**
	 * Includes user's wallet transactions template
	 *
	 * @return void
	 */
	public function show_users_wallet_transactions() {
		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'admin/partials/mwb-user-wallet-transactions.php';
	}

	/**
	 * Includes  wallet recharge relate custom table(WP_LIST)
	 *
	 * @return void
	 */
	public function show_wallet_orders() {
		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'admin/partials/mwb-custom-table-for-orders.php';
	}

	/**
	 * Register new order type (wallet_shop_order)
	 *
	 * @return void
	 */
	public function register_wallet_recharge_post_type() {
		if ( post_type_exists( 'wallet_shop_order' ) ) {
			return;
		}
		wc_register_order_type( 
			'wallet_shop_order',
			apply_filters(
				'woocommerce_register_post_type_wallet_shop_order',
				array(
					'labels' => array(
						'name'               => __( 'Wallet Recharge Orders', 'wallet-system-for-woocommerce' ),
						'singular_name'      => __( 'Wallet Recharge Order', 'wallet-system-for-woocommerce' ),
						'all_items'          => __( 'Wallet Recharge Orders', 'wallet-system-for-woocommerce' ),
						'add_new_item'        => __( 'Add New Order', 'wallet-system-for-woocommerce' ),
						'add_new'             => __( 'Add Order', 'wallet-system-for-woocommerce' ),
						'view_item'          => __( 'View Wallet Recharge Order', 'wallet-system-for-woocommerce' ),
						'edit_item'          => __( 'Edit Wallet Recharge Order', 'wallet-system-for-woocommerce' ),
						'update_item'        => __( 'Update Order', 'wallet-system-for-woocommerce' ),
						'search_items'       => __( 'Search orders', 'wallet-system-for-woocommerce' ),
						'not_found'          => __( 'Not Found Order', 'wallet-system-for-woocommerce' ),
						'not_found_in_trash' => __( 'Not found in Trash', 'wallet-system-for-woocommerce' ),
					),
					'description' => __('Merchant can see all wallet recharge orders.', 'wallet-system-for-woocommerce'),
					'public' => false,
					'show_ui' => true,
					'capability_type' => 'shop_order',
					'map_meta_cap' => true,
					'publicly_queryable' => false,
					'exclude_from_search' => true,
					'show_in_menu' => false,
					'hierarchical' => false,
					'show_in_nav_menus' => false,
					'rewrite' => false,
					'query_var' => false,
					'supports' => array('title', 'comments', 'custom-fields'),
					'has_archive' => false,
					'exclude_from_orders_screen' => true,
					'add_order_meta_boxes' => true,
					'exclude_from_order_count' => true,
					'exclude_from_order_views' => false,
					'exclude_from_order_webhooks' => false,
					'exclude_from_order_reports' => false,
					'exclude_from_order_sales_reports' => false,
					'class_name'                       => 'WC_Order',
				)
			)
		);
	}


}
