<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin
 */

use Dompdf\Dompdf;
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin
 * @author     WP Swings <webmaster@wpswings.com>
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
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function wsfw_admin_enqueue_styles( $hook ) {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'wp-swings_page_wallet_system_for_woocommerce_menu' == $screen->id ) {

			wp_enqueue_style( 'wps-wsfw-select2-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/wallet-system-for-woocommerce-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-wsfw-meterial-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-wsfw-meterial-css2', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-wsfw-meterial-lite', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-wsfw-meterial-icons-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );

			wp_enqueue_style( $this->plugin_name . '-admin-global', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/scss/wallet-system-for-woocommerce-admin-global.css', array( 'wps-wsfw-meterial-icons-css' ), time(), 'all' );

			wp_enqueue_style( 'wps--admin--min-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/wps-admin.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wps-datatable-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/datatables/media/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wps-wallet-action-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/wallet-system-for-woocommerce-wallet-action.css', array(), $this->version, 'all' );

		}
		if ( isset( $screen->id ) && 'wp-swings_page_home' == $screen->id ) {
			wp_enqueue_style( 'wps-wsfw-select2-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/wallet-system-for-woocommerce-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-wsfw-meterial-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-wsfw-meterial-css2', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-wsfw-meterial-lite', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-wsfw-meterial-icons-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps--admin--min-css', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/css/wps-admin-home.min.css', array(), $this->version, 'all' );
		}

		if ( isset( $screen->id ) && 'woocommerce_page_wallet_shop_order' == $screen->id ) {
			wp_enqueue_style( 'wallet-system-for-woocommerce-admin-global', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . '/admin/src/scss/wallet-system-for-woocommerce-go-pro.css', array(), time(), 'all' );

		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function wsfw_admin_enqueue_scripts( $hook ) {
		global $post;
		$screen = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( isset( $screen->id ) && 'wp-swings_page_wallet_system_for_woocommerce_menu' == $screen->id || 'wp-swings_page_home' == $screen->id ) {
			wp_enqueue_script( 'wps-wsfw-select2', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/select-2/wallet-system-for-woocommerce-select2.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( 'wps-wsfw-metarial-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-wsfw-metarial-js2', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-wsfw-metarial-lite', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );

			wp_register_script( $this->plugin_name . 'admin-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-admin.js', array( 'jquery', 'wps-wsfw-select2', 'wps-wsfw-metarial-js', 'wps-wsfw-metarial-js2', 'wps-wsfw-metarial-lite' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name . 'admin-js',
				'wsfw_admin_param',
				array(
					'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
					'nonce'                     => wp_create_nonce( 'wp_rest' ),
					'reloadurl'                 => admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ),
					'wsfw_gen_tab_enable'       => get_option( 'wps_wsfw_enable' ),
					'datatable_pagination_text' => __( 'Rows per page _MENU_', 'wallet-system-for-woocommerce' ),
					'datatable_info'            => __( '_START_ - _END_ of _TOTAL_', 'wallet-system-for-woocommerce' ),
					'wsfw_ajax_error'           => __( 'An error occured!', 'wallet-system-for-woocommerce' ),
					'wsfw_amount_error'         => __( 'Enter amount greater than 0', 'wallet-system-for-woocommerce' ),
					'wsfw_amount_error_debit'         => __( 'Enter amount less than or equal to ', 'wallet-system-for-woocommerce' ),
					'wsfw_partial_payment_msg'  => __( 'Amount want to use from wallet', 'wallet-system-for-woocommerce' ),
					'wsfw_is_subscription'      => $this->wps_wsfw_subscription_active_plugin(),
				)
			);

			wp_enqueue_script( $this->plugin_name . 'admin-js' );
			wp_enqueue_script( 'wps-admin-min-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/js/wps-admin.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-admin-wallet-action-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/js/wallet-system-for-woocommerce-action.js', array(), time(), false );

		}

		if ( isset( $screen->id ) && 'woocommerce_page_wallet_shop_order' == $screen->id ) {

			wp_register_script( 'wallet-recharge-admin-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-wallet-recharge.js', array( 'jquery' ), $this->version, false );

			wp_localize_script(
				'wallet-recharge-admin-js',
				'wsfw_recharge_param',
				array(
					'wallet_count'       => $this->wsfw_wallet_recharge_count(),
				)
			);

		}

		if ( in_array( $screen_id, array( 'shop_order' ) ) ) {
			wp_register_script( 'wallet-recharge-admin-js', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-order-shop.js', array( 'jquery' ), $this->version, false );
			global  $woocommerce;
			$currency_symbol = get_woocommerce_currency_symbol();
			$order = wc_get_order( $post->ID );
			wp_enqueue_script( 'wallet-recharge-admin-js' );
			$order_localizer = array(
				'order_id' => $post->ID,
				'payment_method' => $order->get_payment_method( 'edit' ),
				'default_price' => wc_price( 0 ),
				'currency_symbol' => $currency_symbol,
				'is_refundable' => apply_filters( 'wps_wallet_is_order_refundable', ( ! wps_is_wallet_rechargeable_order( $order ) && $order->get_payment_method( 'edit' ) != 'wallet' ) && $order->get_customer_id( 'edit' ), $order ),
				'i18n' => array(
					'refund' => __( 'Refund', 'wallet-system-for-woocommerce' ),
					'via_wallet' => __( 'to user wallet', 'wallet-system-for-woocommerce' ),
				),
			);
			wp_localize_script( 'wallet-recharge-admin-js', 'wps_wallet_admin_order_param', $order_localizer );
		}

		wp_enqueue_script( 'wallet-recharge-admin-js' );
	}


	/**
	 * Add refund button to WooCommerce order page.
	 *
	 * @param int    $item_id add order item.
	 * @param Object $item item of order.
	 */
	public function woocommerce_after_order_fee_item_name_callback( $item_id, $item ) {
		global $post, $thepostid;

		if ( ! is_partial_payment_order_item( $item_id, $item ) ) {
			return;
		}
		if ( ! is_int( $thepostid ) ) {
				$thepostid = $post->ID;
		}

		$order_id = $thepostid;
		if ( get_post_meta( $order_id, '_wps_wallet_partial_payment_refunded', true ) ) {
			$html = '<small class="refunded">' . __( 'Refunded', 'wallet-system-for-woocommerce' ) . '</small>';
		} else {
			$html = '<button type="button" class="button refund-partial-payment">' . __( 'Refund to Wallet', 'wallet-system-for-woocommerce' ) . '</button>';
		}

		echo wp_kses_post( $html );
	}


	/**
	 * Check subscription plugin is active or not.
	 *
	 * @since   1.0.0
	 */
	public function wps_wsfw_subscription_active_plugin() {
		$is_installed_msg = false;
		$plugin_text_domain = 'subscriptions-for-woocommerce';
		$installed_plugins = get_plugins();

		foreach ( $installed_plugins as $key => $value ) {
			if ( $value['TextDomain'] != $plugin_text_domain ) {
				$is_installed_msg = true;
			}
		}
		if ( false == $is_installed_msg ) {
			return true;
		}
		if ( ! is_plugin_active( 'subscriptions-for-woocommerce/subscriptions-for-woocommerce.php' ) ) {
			$is_installed_msg = true;
		} else {
			return false;
		}
		return $is_installed_msg;
	}


	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function wsfw_general_settings_before_action() {

		$is_installed_msg = '';
		$plugin_text_domain = 'subscriptions-for-woocommerce';
		$installed_plugins = get_plugins();
		$not_active = false;
		foreach ( $installed_plugins as $key => $value ) {

			if ( $value['TextDomain'] == $plugin_text_domain ) {
					$not_active = true;
			}
		}

		if ( false != $not_active ) {

			if ( ! is_plugin_active( 'subscriptions-for-woocommerce/subscriptions-for-woocommerce.php' ) ) {
				$is_installed_msg = __( 'To use this feature please activate Subscription Plugin', 'wallet-system-for-woocommerce' );
			}
		} else {
			$is_installed_msg = __( 'To use this feature please install Subscription Plugin', 'wallet-system-for-woocommerce' );

		}
		?>
			<div class="wps-c-modal">
				<div class="wps-c-modal__cover"></div>
				<div class="wps-c-modal__message">
					<span class="wps-c-modal__close">+</span>
					<div class="wps-c-modal__content">
						<span class="wps-c-modal__content-text">
							<?php
							echo esc_html( $is_installed_msg );
							if ( true != $not_active ) {
								?>
									<a href="https://wordpress.org/plugins/subscriptions-for-woocommerce/">
							 <?php esc_html_e( 'click here', 'wallet-system-for-woocommerce' ); } ?>  </a>   </span>
					</div>
					<div class="wps-c-modal__confirm">
					<span class="wps-c-modal__confirm-button wps-c-modal__yes">Close</span>
					</div>
				</div>
			</div>
			<?php
	}


	/**
	 * Get wallet recharge order count.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_wallet_recharge_count() {
		$wallet_orders = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'wallet_shop_order',
				'post_status' => 'wc-processing',
			)
		);
		$order_count = count( $wallet_orders );
		return $order_count;
	}

	/**
	 * Adding settings menu for Wallet System for WooCommerce.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_options_page() {
		global $submenu;

		if ( empty( $GLOBALS['admin_page_hooks']['wps-plugins'] ) ) {
			add_menu_page( 'WP Swings', 'WP Swings', 'manage_options', 'wps-plugins', array( $this, 'wps_plugins_listing_page' ), WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/wpswings_logo.png', 15 );

			add_submenu_page( 'wps-plugins', 'Home', 'Home', 'manage_options', 'home', array( $this, 'wpswings_welcome_callback_function' ), 1 );
			$wsfw_menus =
			// desc - filter for trial.
			apply_filters( 'wps_add_plugins_menus_array', array() );
			if ( is_array( $wsfw_menus ) && ! empty( $wsfw_menus ) ) {
				foreach ( $wsfw_menus as $mfw_key => $wsfw_value ) {
					add_submenu_page( 'wps-plugins', $wsfw_value['name'], $wsfw_value['name'], 'manage_options', $wsfw_value['menu_link'], array( $wsfw_value['instance'], $wsfw_value['function'] ) );
				}
			}
		} else {
			$is_home_exists = false;
			if ( ! empty( $submenu['wps-plugins'] ) ) {
				foreach ( $submenu['wps-plugins'] as $key => $value ) {
					if ( ! empty( $value ) && is_array( $value ) ) {
						if ( 'Home' == $value[0] ) {
							$is_home_exists = true;
						}
					}
				}

				if ( ! $is_home_exists ) {

					add_submenu_page( 'wps-plugins', 'Home', 'Home', 'manage_options', 'home', array( $this, 'wpswings_welcome_callback_function' ), 1 );
				}
			}
		}

		add_submenu_page( '', 'Edit User Wallet', '', 'edit_posts', 'wps-edit-wallet', array( $this, 'edit_wallet_of_user' ) );

		add_submenu_page( 'woocommerce', 'Wallet Recharge Orders', __( 'Wallet Recharge Orders', 'wallet-system-for-woocommerce' ), 'edit_posts', 'wallet_shop_order', array( $this, 'show_wallet_orders' ) );
	}



	/**
	 *
	 * Adding the default menu into the WordPress menu.
	 *
	 * @name wpswings_callback_function
	 * @since 1.0.0
	 */
	public function wpswings_welcome_callback_function() {
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wallet-system-for-woocommerce-welcome.php';
	}


	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since   1.0.0
	 */
	public function wps_wsfw_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'wps-plugins', $submenu ) ) {
			if ( isset( $submenu['wps-plugins'][0] ) ) {
				unset( $submenu['wps-plugins'][0] );
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
			'name'      => __( 'Wallet System', 'wallet-system-for-woocommerce' ),
			'slug'      => 'wallet_system_for_woocommerce_menu',
			'menu_link' => 'wallet_system_for_woocommerce_menu',
			'instance'  => $this,
			'function'  => 'wsfw_options_menu_html',
		);
		return $menus;
	}


	/**
	 * Wallet System for WooCommerce wps_plugins_listing_page.
	 *
	 * @since 1.0.0
	 */
	public function wps_plugins_listing_page() {
		$active_marketplaces = apply_filters( 'wps_add_plugins_menus_array', array() );
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

		$wsfw_settings_general   = apply_filters( 'wsfw_general_extra_settings_array_before_enable', $wsfw_settings_general );

		$wsfw_settings_general = array(
			// enable wallet.
			array(
				'title'       => __( 'Enable', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => '',
				'name'        => 'wps_wsfw_enable',
				'id'          => 'wps_wsfw_enable',
				'value'       => 'on',
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Wallet Recharge', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable to allow customers to recharge their wallet', 'wallet-system-for-woocommerce' ),
				'name'        => 'wsfw_enable_wallet_recharge',
				'id'          => 'wsfw_enable_wallet_recharge',
				'value'       => 'on',
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Refund To Wallet', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable to send refund amonut to wallet.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_allow_refund_to_wallet',
				'id'          => 'wps_wsfw_allow_refund_to_wallet',
				'value'       => 'on',
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Enable Wallet Script For My Account Wallet.', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable if unable to select wallet option in my account section', 'wallet-system-for-woocommerce' ),
				'name'        => 'wsfw_wallet_script_for_account_enabled',
				'id'          => 'wsfw_wallet_script_for_account_enabled',
				'value'       => '',
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Send Email On Wallet Amount Update to Customers', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable to send email to the user.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_enable_email_notification_for_wallet_update',
				'id'          => 'wps_wsfw_enable_email_notification_for_wallet_update',
				'value'       => '',
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Enable Wallet Partial Payment Method', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable to allow customers to pay amount partially from their wallet.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wsfw_wallet_partial_payment_method_enabled',
				'id'          => 'wsfw_wallet_partial_payment_method_enabled',
				'value'       => '',
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Select Partial Payment Option', 'wallet-system-for-woocommerce' ),
				'type'        => 'select',
				'name'        => 'wsfw_wallet_partial_payment_method_options',
				'description' => __( 'Select Value for Manual Method or Partial Method', 'wallet-system-for-woocommerce' ),
				'id'          => 'wsfw_wallet_partial_payment_method_options',
				'value'       => get_option( 'wsfw_wallet_partial_payment_method_options', 'manual_pay' ),
				'class'       => 'wsfw-select-class',
				'options'     => array(
					'total_pay'   => __( 'Total Wallet Amount', 'wallet-system-for-woocommerce' ),
					'manual_pay'  => __( 'Manual Wallet Amount', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Wallet Shortcode', 'wallet-system-for-woocommerce' ),
				'type'        => 'text',
				'id'          => 'wsfw_wallet_shortcode',
				'value'       => '[wps-wallet]',
				'attr'        => 'readonly',
				'class'       => 'wsfw-select-class',
				'placeholder' => __( 'ShortCode For Wallet', 'wallet-system-for-woocommerce' ),
			),
		);
		$wsfw_settings_general   = apply_filters( 'wsfw_general_extra_settings_array', $wsfw_settings_general );
		$wsfw_settings_general[] = array(
			'type'        => 'submit',
			'name'        => 'wsfw_button_demo',
			'id'          => 'wsfw_button_demo',
			'button_text' => __( 'Save Settings', 'wallet-system-for-woocommerce' ),
			'class'       => 'wsfw-button-class',
		);
		return $wsfw_settings_general;
	}

	/**
	 * This function is used to create daily visit html.
	 *
	 * @since    1.0.0
	 * @param array $wsfw_settings_template Settings fields.
	 */
	public function wsfw_admin_wallet_action_daily_visit_settings_page( $wsfw_settings_template ) {

		$wsfw_settings_template = array(
			array(
				'title'       => __( 'Enable Daily Visit Settings', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable auto credit amount on daily visit.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_wallet_action_daily_enable',
				'id'          => 'wps_wsfw_wallet_action_daily_enable',
				'value'       => get_option( 'wps_wsfw_wallet_action_daily_enable' ),
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Enter Daily Visit Amount', 'wallet-system-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Enter amount which will be credited to the user wallet on daily visit.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_wallet_action_daily_amount',
				'id'          => 'wps_wsfw_wallet_action_daily_amount',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_wallet_action_daily_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_daily_amount' ) : 1,
				'placeholder' => __( 'Enter daily visit amount', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
		);
		$wsfw_settings_template = apply_filters( 'wsfw_wallet_action_daily_extra_settings_array', $wsfw_settings_template );
		return $wsfw_settings_template;
	}

	/**
	 * This function is used to create new registration html.
	 *
	 * @since    1.0.0
	 * @param array $wsfw_settings_template Settings fields.
	 */
	public function wsfw_admin_wallet_action_registration_settings_page( $wsfw_settings_template ) {

		$wsfw_settings_template = array(
			array(
				'title'       => __( 'Enable Signup Settings', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => '',
				'name'        => 'wps_wsfw_wallet_action_registration_enable',
				'id'          => 'wps_wsfw_wallet_action_registration_enable',
				'value'       => get_option( 'wps_wsfw_wallet_action_registration_enable' ),
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Enter Signup Amount', 'wallet-system-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Enter amount which will be credited to the user wallet after new registration.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_wallet_action_registration_amount',
				'id'          => 'wps_wsfw_wallet_action_registration_amount',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_wallet_action_registration_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_registration_amount' ) : 1,
				'placeholder' => __( 'Enter signup amount', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
			array(
				'title'       => __( 'Enter Signup Description', 'wallet-system-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Wallet transaction description that will display in wallet section.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_wallet_action_registration_description',
				'id'          => 'wps_wsfw_wallet_action_registration_description',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_wallet_action_registration_description' ) ) ? get_option( 'wps_wsfw_wallet_action_registration_description' ) : 'Amount credited for becoming a member.',
				'placeholder' => __( 'Enter signup description', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
		);
		$wsfw_settings_template   = apply_filters( 'wsfw_wallet_action_registration_extra_settings_array', $wsfw_settings_template );
		return $wsfw_settings_template;
	}


	/**
	 * This function is used to create new registration html.
	 *
	 * @since    1.0.0
	 * @param array $wsfw_settings_template Settings fields.
	 */
	public function wsfw_admin_wallet_action_auto_topup_settings_page( $wsfw_settings_template ) {

		$wsfw_settings_template = array(
			array(
				'title'       => __( 'Enable Wallet Auto Top Up Settings', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => '',
				'name'        => 'wps_wsfw_wallet_action_auto_topup_enable',
				'id'          => 'wps_wsfw_wallet_action_auto_topup_enable',
				'value'       => get_option( 'wps_wsfw_wallet_action_auto_topup_enable' ),
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Enter Subscriptions Per Interval', 'wallet-system-for-woocommerce' ),
				'type'        => 'subscription_select1',
				'description' => __( 'Choose the subscriptions time interval for the product "for example 10 days".', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_subscriptions_per_interval',
				'id'          => 'wps_wsfw_subscriptions_per_interval',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_subscriptions_per_interval' ) ) ? get_option( 'wps_wsfw_subscriptions_per_interval' ) : 1,
				'placeholder' => __( 'Enter comment amount', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
			array(
				'title'       => __( 'Enter Subscriptions Expiry Interval', 'wallet-system-for-woocommerce' ),
				'type'        => 'subscription_select2',
				'description' => __( 'Choose the subscriptions expiry time interval for the product "leave empty for unlimited"', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_subscriptions_expiry_interval',
				'id'          => 'wps_wsfw_subscriptions_expiry_interval',
				'value'       => get_option( 'wps_wsfw_subscriptions_expiry_interval', 'days' ),
				'class'       => 'wsfw-radio-switch-class',
			),
		);

		$wsfw_settings_template   = apply_filters( 'wsfw_wallet_action_auto_topup_extra_settings_array', $wsfw_settings_template );
		return $wsfw_settings_template;
	}


	/**
	 * This is used to create comment html.
	 *
	 * @param array $wsfw_settings_template setting template.
	 * @return array
	 */
	public function wsfw_admin_wallet_action_settings_comment_array( $wsfw_settings_template ) {
		$wsfw_settings_template = array(
			array(
				'title'       => __( 'Enable Comments Settings', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Check this box to enable the Comment Amount when comment is approved..', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_wallet_action_comment_enable',
				'id'          => 'wps_wsfw_wallet_action_comment_enable',
				'value'       => get_option( 'wps_wsfw_wallet_action_comment_enable' ),
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Enter Comments Amount', 'wallet-system-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'The amount which new customers will get after their comments are approved..', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_wallet_action_comment_amount',
				'id'          => 'wps_wsfw_wallet_action_comment_amount',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_wallet_action_comment_amount' ) ) ? get_option( 'wps_wsfw_wallet_action_comment_amount' ) : 1,
				'placeholder' => __( 'Enter comment amount', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
			array(
				'title'       => __( 'User per post comment', 'wallet-system-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'This allow the limitation to the number of comment a user can have per post.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_wallet_action_restrict_comment',
				'id'          => 'wps_wsfw_wallet_action_restrict_comment',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_wallet_action_restrict_comment' ) ) ? get_option( 'wps_wsfw_wallet_action_restrict_comment' ) : 1,
				'placeholder' => __( 'User per post comment', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
			array(
				'title'       => __( 'Enter Comment Description', 'wallet-system-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Enter message for user that display on product page.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_wallet_action_comment_description',
				'id'          => 'wps_wsfw_wallet_action_comment_description',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_wallet_action_comment_description' ) ) ? get_option( 'wps_wsfw_wallet_action_comment_description' ) : 'You will get 1 points for product review',
				'placeholder' => __( 'Enter comment description', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
		);
		$wsfw_settings_template   = apply_filters( 'wsfw_wallet_action_comment_extra_settings_array', $wsfw_settings_template );
		$wsfw_settings_template[] = array(
			'type'        => 'submit',
			'name'        => 'wsfw_button_wallet_action',
			'id'          => 'wsfw_button_wallet_action',
			'button_text' => __( 'Save Settings', 'wallet-system-for-woocommerce' ),
			'class'       => 'wsfw-button-class',
		);
		return $wsfw_settings_template;
	}

	/**
	 * This function is used to
	 *
	 * @return array
	 */
	public function wsfw_admin_cashback_settings_page() {

		$args           = array(
			'taxonomy'     => 'product_cat',
			'orderby'      => 'name',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
			'title_li'     => '',
			'hide_empty'   => 0,
		);
		$all_categories    = get_categories( $args );
		$mwb_wsfw_cat_name = array();
		$wps_wsfw_multiselect_category_rule = get_option( 'wps_wsfw_multiselect_category_rule' );
		if ( empty( $wps_wsfw_multiselect_category_rule ) ) {
			$mwb_wsfw_cat_name[''] = __( 'Please Select', 'wallet-system-for-woocommerce' );
		}
		if ( ! empty( $all_categories ) && is_array( $all_categories ) ) {
			foreach ( $all_categories as $mwb_cat ) {
				$mwb_wsfw_cat_name[ $mwb_cat->name ] = $mwb_cat->name;
			}
		}

		$wsfw_settings_general = array(
			// enable wallet cashback.
			array(
				'title'       => __( 'Enable Wallet Cashback', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => '',
				'name'        => 'wps_wsfw_enable_cashback',
				'id'          => 'wps_wsfw_enable_cashback',
				'value'       => get_option( 'wps_wsfw_enable_cashback' ),
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			),
			array(
				'title'       => __( 'Process Wallet Cashback', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_multiselect_category',
				'type'        => 'multiselect',
				'description' => __( 'Select order status to apply Cashback.', 'wallet-system-for-woocommerce' ),
				'id'          => 'wps_wsfw_multiselect_category',
				'value'       => get_option( 'wps_wsfw_multiselect_category', array( 'completed' ) ),
				'class'       => 'wsfw-multiselect-class wps-defaut-multiselect',
				'placeholder' => '',
				'options' => apply_filters(
					'wps_wsfw_cashback_type_order',
					array(
						'pending' => __( 'Pending payment', 'wallet-system-for-woocommerce' ),
						'on-hold' => __( 'On hold', 'wallet-system-for-woocommerce' ),
						'processing' => __( 'Processing', 'wallet-system-for-woocommerce' ),
						'completed' => __( 'Completed', 'wallet-system-for-woocommerce' ),
					)
				),
			),
			array(
				'title'       => __( 'Wallet Cashback Rule', 'wallet-system-for-woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Select Cashback rule to apply Cashback.<br> <b>Note:</b> In the case of Catergory Wise, Cashback will be applied to each product of category', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_cashback_rule',
				'id'          => 'wps_wsfw_cashback_rule',
				'value'       => get_option( 'wps_wsfw_cashback_rule', 'cartwise' ),
				'class'       => 'wsfw-radio-switch-class',
				'options'     => apply_filters(
					'wsfw_cashback_type__array',
					array(
						'cartwise' => __( 'Cart Wise', 'wallet-system-for-woocommerce' ),
						'catwise'  => __( 'Category Wise', 'wallet-system-for-woocommerce' ),
					)
				),
			),

			array(
				'title'       => __( 'Select Product Category', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_multiselect_category_rule',
				'type'        => 'multiselect',
				'description' => __( 'Select any category to give Cashback.', 'wallet-system-for-woocommerce' ),
				'id'          => 'wps_wsfw_multiselect_category_rule',
				'value'       => get_option( 'wps_wsfw_multiselect_category_rule' ),
				'class'       => 'wsfw-multiselect-class wps-defaut-multiselect',
				'placeholder' => '',
				'options'     => $mwb_wsfw_cat_name,
			),

			array(
				'title'       => __( 'Wallet Cashback Type', 'wallet-system-for-woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Select Cashback type Percentage or Fixed', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_cashback_type',
				'id'          => 'wps_wsfw_cashback_type',
				'value'       => get_option( 'wps_wsfw_cashback_type', 'percent' ),
				'class'       => 'wsfw-radio-switch-class',
				'options'     => apply_filters(
					'wsfw_cashback_type__array',
					array(
						'percent' => __( 'Percentage', 'wallet-system-for-woocommerce' ),
						'fixed'   => __( 'Fixed', 'wallet-system-for-woocommerce' ),
					)
				),
			),
			array(
				'title'       => __( 'Enter Wallet Cashback Amount', 'wallet-system-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Give Cashback on Wallet when customer place order.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_cashback_amount',
				'id'          => 'wps_wsfw_cashback_amount',
				'value'       => ! empty( get_option( 'wps_wsfw_cashback_amount' ) ) ? get_option( 'wps_wsfw_cashback_amount' ) : 10,
				'placeholder' => __( 'Enter amount', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
			array(
				'title'       => __( 'Minimum Cart Amount', 'wallet-system-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Enter minimum cart amount.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_cart_amount_min',
				'id'          => 'wps_wsfw_cart_amount_min',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_cart_amount_min' ) ) ? get_option( 'wps_wsfw_cart_amount_min' ) : 10,
				'placeholder' => __( 'Enter amount', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
			array(
				'title'       => __( 'Maximum Wallet Cashback Amount', 'wallet-system-for-woocommerce' ),
				'type'        => 'number',
				'description' => __( 'Enter maximum Cashback amount.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_wsfw_cashback_amount_max',
				'id'          => 'wps_wsfw_cashback_amount_max',
				'step'        => '0.01',
				'value'       => ! empty( get_option( 'wps_wsfw_cashback_amount_max' ) ) ? get_option( 'wps_wsfw_cashback_amount_max' ) : 20,
				'placeholder' => __( 'Enter amount', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
		);
		$wsfw_settings_general   = apply_filters( 'wsfw_cashback_extra_settings_array', $wsfw_settings_general );
		$wsfw_settings_general[] = array(
			'type'        => 'submit',
			'name'        => 'wsfw_button_cashback',
			'id'          => 'wsfw_button_cashback',
			'button_text' => __( 'Save Settings', 'wallet-system-for-woocommerce' ),
			'class'       => 'wsfw-button-class',
		);
		return $wsfw_settings_general;
	}

	/**
	 * Wallet System for WooCommerce save tab settings.
	 *
	 * @since 1.0.0
	 */
	public function wsfw_admis_save_tab_settings_for_wallet_action() {

		global $wsfw_wps_wsfw_obj;
		if ( isset( $_POST['wsfw_button_wallet_action'] ) ) {

			$nonce = ( isset( $_POST['updatenoncewallet_action'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_action'] ) ) : '';
			if ( wp_verify_nonce( $nonce ) ) {

				$wps_wsfw_gen_flag     = false;
				$wsfw_settings_wallet_action_auto_topup = apply_filters( 'wsfw_wallet_action_settings_auto_topup_array', array() );
				// fee saving.
				$wsfwp_wallet_action_settings_withdrawal_array = apply_filters( 'wsfwp_wallet_action_settings_withdrawal_array', array() );
				$wsfwp_wallet_action_settings_transfer_array = apply_filters( 'wsfwp_wallet_action_settings_transfer_array', array() );
				// fee saving.
				$wsfw_settings_wallet_action_new_registration = apply_filters( 'wsfw_wallet_action_settings_registration_array', array() );
				$wsfw_wallet_action_settings_daily_visit      = apply_filters( 'wsfw_wallet_action_settings_daily_visit_array', array() );
				$wsfw_wallet_action_settings_comment_array    = apply_filters( 'wsfw_wallet_action_settings_comment_array', array() );
				update_option( 'wps_sfw_subscription_interval', ! empty( $_POST['wps_sfw_subscription_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_sfw_subscription_interval'] ) ) : '' );
				update_option( 'wps_wsfw_subscriptions_per_interval', ! empty( $_POST['wps_wsfw_subscriptions_per_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wsfw_subscriptions_per_interval'] ) ) : '' );
				update_option( 'wps_sfw_subscription_expiry_interval', ! empty( $_POST['wps_sfw_subscription_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_sfw_subscription_interval'] ) ) : '' );
				update_option( 'wps_wsfw_subscriptions_expiry_per_interval', ! empty( $_POST['wps_wsfw_subscriptions_expiry_per_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_wsfw_subscriptions_expiry_per_interval'] ) ) : '' );

				$wsfw_settings_wallet_action_new_registration = array_merge( $wsfw_settings_wallet_action_new_registration, $wsfw_wallet_action_settings_daily_visit );
				$wsfw_settings_wallet_action_new_registration = array_merge( $wsfw_settings_wallet_action_new_registration, $wsfw_settings_wallet_action_auto_topup );

				$wsfw_settings_wallet_action_new_registration = array_merge( $wsfw_settings_wallet_action_new_registration, $wsfw_wallet_action_settings_comment_array );

				$wsfw_settings_wallet_action_new_registration = array_merge( $wsfw_settings_wallet_action_new_registration, $wsfwp_wallet_action_settings_withdrawal_array );
				$wsfw_settings_wallet_action_new_registration = array_merge( $wsfw_settings_wallet_action_new_registration, $wsfwp_wallet_action_settings_transfer_array );

				$wsfw_button_index     = array_search( 'submit', array_column( $wsfw_settings_wallet_action_new_registration, 'type' ) );
				if ( isset( $wsfw_button_index ) && ( null == $wsfw_button_index || '' == $wsfw_button_index ) ) {
					$wsfw_button_index = array_search( 'button', array_column( $wsfw_settings_wallet_action_new_registration, 'type' ) );
				}
				$this->wsfw_admin_save_data( $wsfw_settings_wallet_action_new_registration, $wps_wsfw_gen_flag );

			} else {
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
			}
		}
	}


	/**
	 * Wallet System for WooCommerce save tab settings.
	 *
	 * @since 1.0.0
	 */
	public function wsfw_admis_save_tab_settings_for_cashback() {
		global $wsfw_wps_wsfw_obj;
		if ( isset( $_POST['wsfw_button_cashback'] ) ) {
			$nonce = ( isset( $_POST['updatenoncecashback'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncecashback'] ) ) : '';
			if ( wp_verify_nonce( $nonce ) ) {

				$wps_wsfw_gen_flag     = false;
				$wsfw_genaral_settings = apply_filters( 'wsfw_cashback_settings_array', array() );
				$wsfw_button_index     = array_search( 'submit', array_column( $wsfw_genaral_settings, 'type' ) );
				if ( isset( $wsfw_button_index ) && ( null == $wsfw_button_index || '' == $wsfw_button_index ) ) {
					$wsfw_button_index = array_search( 'button', array_column( $wsfw_genaral_settings, 'type' ) );
				}
				$this->wsfw_admin_save_data( $wsfw_genaral_settings, $wps_wsfw_gen_flag );

			} else {
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
			}
		}
	}

	/**
	 * This function is used to save wallet action tab data.
	 *
	 * @param array  $wsfw_genaral_settings wsfw_genaral_settings.
	 * @param string $wps_wsfw_gen_flag wps_wsfw_gen_flag.
	 * @return void
	 */
	public function wsfw_admin_save_data( $wsfw_genaral_settings, $wps_wsfw_gen_flag ) {
		global $wsfw_wps_wsfw_obj;
		if ( isset( $_POST['wsfw_button_wallet_action'] ) || isset( $_POST['wsfw_button_cashback'] ) ) {
			$nonce_action   = ( isset( $_POST['updatenoncewallet_action'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_action'] ) ) : '';
			$nonce_cashback = ( isset( $_POST['updatenoncecashback'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncecashback'] ) ) : '';
			if ( wp_verify_nonce( $nonce_action ) || wp_verify_nonce( $nonce_cashback ) ) {
				$wsfw_button_index     = array_search( 'submit', array_column( $wsfw_genaral_settings, 'type' ) );
				if ( isset( $wsfw_button_index ) && ( null == $wsfw_button_index || '' == $wsfw_button_index ) ) {
					$wsfw_button_index = array_search( 'button', array_column( $wsfw_genaral_settings, 'type' ) );
				}

				if ( isset( $wsfw_button_index ) && '' !== $wsfw_button_index ) {
					unset( $wsfw_genaral_settings[ $wsfw_button_index ] );
					if ( is_array( $wsfw_genaral_settings ) && ! empty( $wsfw_genaral_settings ) ) {
						foreach ( $wsfw_genaral_settings as $wsfw_genaral_setting ) {

							if ( isset( $wsfw_genaral_setting['id'] ) && '' !== $wsfw_genaral_setting['id'] ) {
								if ( isset( $_POST[ $wsfw_genaral_setting['id'] ] ) ) {
									update_option( $wsfw_genaral_setting['id'], map_deep( wp_unslash( $_POST[ $wsfw_genaral_setting['id'] ] ), 'sanitize_text_field' ) );
								} else {
									if ( isset( $_POST[ $wsfw_genaral_setting['id'] ] ) ) {
										update_option( $wsfw_genaral_setting['id'], sanitize_text_field( wp_unslash( $_POST[ $wsfw_genaral_setting['id'] ] ) ) );
									} else {
										update_option( $wsfw_genaral_setting['id'], '' );
									}
								}
							} else {
								$wps_wsfw_gen_flag = true;
							}
						}
					}
					if ( $wps_wsfw_gen_flag ) {
						$wps_wsfw_error_text = esc_html__( 'Id of some field is missing', 'wallet-system-for-woocommerce' );
						$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'error' );
					} else {
						$wps_wsfw_error_text = esc_html__( 'Settings saved !', 'wallet-system-for-woocommerce' );
						$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'success' );
					}
				}
			}
		} else {
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
		}
	}


	/**
	 * Wallet System for WooCommerce save tab settings.
	 *
	 * @since 1.0.0
	 */
	public function wsfw_admin_save_tab_settings() {
		global $wsfw_wps_wsfw_obj;

		if ( isset( $_POST['wsfw_button_demo'] ) ) {

			$nonce = ( isset( $_POST['updatenonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenonce'] ) ) : '';

			if ( wp_verify_nonce( $nonce ) ) {

				$screen = get_current_screen();
				if ( isset( $screen->id ) && 'wp-swings_page_home' === $screen->id ) {

					$enable_tracking = ! empty( $_POST['wsfw_enable_tracking'] ) ? sanitize_text_field( wp_unslash( $_POST['wsfw_enable_tracking'] ) ) : '';
					update_option( 'wsfw_enable_tracking', $enable_tracking );

					return;
				}
				$wps_wsfw_gen_flag     = false;
				$wsfw_genaral_settings = apply_filters( 'wsfw_general_settings_array', array() );
				$wsfw_button_index     = array_search( 'submit', array_column( $wsfw_genaral_settings, 'type' ) );
				if ( isset( $wsfw_button_index ) && ( null == $wsfw_button_index || '' == $wsfw_button_index ) ) {
					$wsfw_button_index = array_search( 'button', array_column( $wsfw_genaral_settings, 'type' ) );
				}
				if ( isset( $wsfw_button_index ) && '' !== $wsfw_button_index ) {
					unset( $wsfw_genaral_settings[ $wsfw_button_index ] );
					if ( is_array( $wsfw_genaral_settings ) && ! empty( $wsfw_genaral_settings ) ) {
						foreach ( $wsfw_genaral_settings as $wsfw_genaral_setting ) {
							if ( isset( $wsfw_genaral_setting['id'] ) && '' !== $wsfw_genaral_setting['id'] ) {
								if ( isset( $_POST[ $wsfw_genaral_setting['id'] ] ) ) {
									update_option( $wsfw_genaral_setting['id'], sanitize_text_field( wp_unslash( $_POST[ $wsfw_genaral_setting['id'] ] ) ) );
								} else {
									update_option( $wsfw_genaral_setting['id'], '' );
								}
							} else {
								$wps_wsfw_gen_flag = true;
							}
						}
					}
					if ( $wps_wsfw_gen_flag ) {
						$wps_wsfw_error_text = esc_html__( 'Id of some field is missing', 'wallet-system-for-woocommerce' );
						$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'error' );
					} else {
						$wps_wsfw_error_text = esc_html__( 'Settings saved !', 'wallet-system-for-woocommerce' );
						$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'success' );
					}

					$enable = get_option( 'wps_wsfw_enable', '' );
					$wallet_payment_enable = get_option( 'woocommerce_wps_wcb_wallet_payment_gateway_settings' );
					if ( isset( $enable ) && '' === $enable ) {
						if ( $wallet_payment_enable ) {
							$wallet_payment_enable['enabled'] = 'no';
							update_option( 'woocommerce_wps_wcb_wallet_payment_gateway_settings', $wallet_payment_enable );
						}
					} else {
						if ( $wallet_payment_enable ) {
							$wallet_payment_enable['enabled'] = 'yes';
							update_option( 'woocommerce_wps_wcb_wallet_payment_gateway_settings', $wallet_payment_enable );
						}
					}
				}
			} else {
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
			}
		}
	}

	/**
	 * Add wallet edit fields in admin and user profile page
	 *
	 * @param object $user user object.
	 * @return void
	 */
	public function wsfw_add_user_wallet_field( $user ) {
		global  $woocommerce;
		$currency   = get_woocommerce_currency_symbol();
		$wallet_bal = get_user_meta( $user->ID, 'wps_wallet', true );
		?>
		<h2>
		<?php
		esc_html_e( 'Wallet Balance: ', 'wallet-system-for-woocommerce' );
		echo wp_kses_post( wc_price( $wallet_bal ) );
		?>
		</h2>
		<table class="form-table">
			<tr>
				<th><label for="wps_wallet"><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></label></th>
				<td>
					<input type="number" step="0.01" name="wps_wallet" id="wps_wallet">
					<span class="description"><?php esc_html_e( 'Add/deduct money to/from wallet', 'wallet-system-for-woocommerce' ); ?></span>
					<p class="error" ></p>
				</td>
			</tr>
			<tr>
				<th><label for="wps_wallet">Action</label></th>
				<td>
					<select name="wps_edit_wallet_action" id="wps_edit_wallet_action">
						<option><?php esc_html_e( 'Select any', 'wallet-system-for-woocommerce' ); ?></option>
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
	 * @param int $user_id user id.
	 * @return void
	 */
	public function wsfw_save_user_wallet_field( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) ) {
			$update        = true;
			if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-user_' . $user_id ) ) {
				return;
			}

			$wallet_amount = ( isset( $_POST['wps_wallet'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wps_wallet'] ) ) : '';
			$action        = ( isset( $_POST['wps_edit_wallet_action'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wps_edit_wallet_action'] ) ) : '';
			if ( empty( $action ) || 'Select any' === $action || empty( $wallet_amount ) ) {
				$update = false;
			}
			if ( $update ) {
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$wps_wallet             = get_user_meta( $user_id, 'wps_wallet', true );
				$wps_wallet             = ( ! empty( $wps_wallet ) ) ? $wps_wallet : 0;
				if ( 'credit' === $action ) {
					$wps_wallet       = floatval( $wps_wallet ) + floatval( $wallet_amount );
					$transaction_type = esc_html__( 'Credited by admin', 'wallet-system-for-woocommerce' );
					$mail_message     = __( 'Merchant has credited your wallet by ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet_amount );
				} elseif ( 'debit' === $action ) {
					if ( $wps_wallet < $wallet_amount ) {
						$wps_wallet = 0;
					} else {
						$wps_wallet = floatval( $wps_wallet ) - floatval( $wallet_amount );
					}
					$transaction_type = esc_html__( 'Debited by admin', 'wallet-system-for-woocommerce' );
					$mail_message     = __( 'Merchant has deducted ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet_amount ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
				}
				update_user_meta( $user_id, 'wps_wallet', abs( $wps_wallet ) );

				$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
					$user       = get_user_by( 'id', $user_id );
					$name       = $user->first_name . ' ' . $user->last_name;
					$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
					$mail_text .= $mail_message;
					$to         = $user->user_email;
					$from       = get_option( 'admin_email' );
					$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
					$headers    = 'MIME-Version: 1.0' . "\r\n";
					$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
					$headers   .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to . "\r\n";

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}
				$transaction_data = array(
					'user_id'          => $user_id,
					'amount'           => $wallet_amount,
					'currency'         => get_woocommerce_currency(),
					'payment_method'   => esc_html__( 'Manually By Admin', 'wallet-system-for-woocommerce' ),
					'transaction_type' => $transaction_type,
					'order_id'         => '',
					'note'             => '',

				);

				$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
			}
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
				$new['wps_wallet_bal']     = esc_html__( 'Wallet Balance', 'wallet-system-for-woocommerce' );
				$new['wps_wallet_actions'] = esc_html__( 'Wallet Actions', 'wallet-system-for-woocommerce' );
			}
			$new[ $key ] = $title;
		}
		return $new;
	}

	/**
	 * Add wallet column to user table.
	 *
	 * @param string $value value.
	 * @param array  $column_name columns.
	 * @param string $user_id user id.
	 */
	public function wsfw_add_user_wallet_col_data( $value, $column_name, $user_id ) {
		$wallet_bal = get_user_meta( $user_id, 'wps_wallet', true );
		if ( empty( $wallet_bal ) ) {
			$wallet_bal = 0;
		}
		if ( 'wps_wallet_bal' === $column_name ) {
			return wc_price( $wallet_bal );
		}
		if ( 'wps_wallet_actions' === $column_name ) {
			$html = '<p><a href="' . esc_url( admin_url( "?page=wps-edit-wallet&id=$user_id" ) ) . '" title="Edit Wallet" class="button wallet-manage"></a> 
			<a class="button view-transactions" href="' . esc_url( admin_url( "admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=wps-user-wallet-transactions&id=$user_id" ) ) . '" title="View Transactions" ></a></p>';
			return $html;
		}
	}

	/**
	 * Change wallet amount on order status change
	 *
	 * @param int    $order_id order id.
	 * @param string $old_status order old status.
	 * @param string $new_status order new status.
	 * @return void
	 */
	public function wsfw_order_status_changed_admin( $order_id, $old_status, $new_status ) {
		$order          = wc_get_order( $order_id );
		$userid         = $order->get_user_id();
		$order_items    = $order->get_items();
		$order_total    = $order->get_total();
		$payment_method = $order->get_payment_method();
		$order_currency = $order->get_currency();
		$wallet_id      = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$walletamount   = get_user_meta( $userid, 'wps_wallet', true );
		$walletamount   = empty( $walletamount ) ? 0 : $walletamount;
		$user                   = get_user_by( 'id', $userid );
		$name                   = $user->first_name . ' ' . $user->last_name;
		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );

		$allow_refund_to_wallet = get_option( 'wps_wsfw_allow_refund_to_wallet', '' );
		if ( isset( $allow_refund_to_wallet ) && 'on' === $allow_refund_to_wallet ) {
			if ( 'refunded' === $new_status ) {
				foreach ( $order_items as $item_id => $item ) {
					$product_id = $item->get_product_id();
					if ( isset( $product_id ) && ! empty( $product_id ) && $product_id != $wallet_id ) {
						$allow_refund = true;
					} else {
						$allow_refund = false;
					}
				}

				if ( $allow_refund ) {
					$amount = $order_total;
					foreach ( $order->get_fees() as $item_fee ) {
						$fee_name    = $item_fee->get_name();
						$fee_total   = $item_fee->get_total();
						$wallet_name = __( 'Via wallet', 'wallet-system-for-woocommerce' );
						if ( $wallet_name === $fee_name ) {
							$fees   = abs( $fee_total );
							$amount += $fees;
							break;
						}
					}
					$credited_amount = apply_filters( 'wps_wsfw_update_wallet_to_base_price', $amount, $order_currency );
					$walletamount   += $credited_amount;
					update_user_meta( $userid, 'wps_wallet', $walletamount );

					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
						$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $amount, array( 'currency' => $order->get_currency() ) ) . __( ' through order refund.', 'wallet-system-for-woocommerce' );
						$to         = $user->user_email;
						$from       = get_option( 'admin_email' );
						$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
						$headers    = 'MIME-Version: 1.0' . "\r\n";
						$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers   .= 'From: ' . $from . "\r\n" .
							'Reply-To: ' . $to . "\r\n";
						$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );

					}

					$transaction_type = esc_html__( 'Wallet credited through order refund ', 'wallet-system-for-woocommerce' ) . '<a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
					$transaction_data = array(
						'user_id'          => $userid,
						'amount'           => $amount,
						'currency'         => $order->get_currency(),
						'payment_method'   => esc_html__( 'Manually by admin through refund', 'wallet-system-for-woocommerce' ),
						'transaction_type' => htmlentities( $transaction_type ),
						'order_id'         => $order_id,
						'note'             => '',
					);
					$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
				}
			}
		}

		foreach ( $order_items as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$total      = $item->get_total();
			if ( isset( $product_id ) && ! empty( $product_id ) && $product_id == $wallet_id ) {
				$order_status = array( 'pending', 'on-hold', 'processing' );
				if ( in_array( $old_status, $order_status ) && 'completed' == $new_status ) {
					$amount        = $total;
					$credited_amount = apply_filters( 'wps_wsfw_update_wallet_to_base_price', $amount, $order_currency );
					$wallet_userid = apply_filters( 'wsfw_check_order_meta_for_userid', $userid, $order_id );
					if ( $wallet_userid ) {
						$update_wallet_userid = $wallet_userid;
					} else {
						$update_wallet_userid = $userid;
					}
					$transfer_note = apply_filters( 'wsfw_check_order_meta_for_recharge_reason', $order_id, '' );
					$walletamount  = get_user_meta( $update_wallet_userid, 'wps_wallet', true );
					$walletamount  = ( ! empty( $walletamount ) ) ? $walletamount : 0;
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
						'user_id'          => $update_wallet_userid,
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
			$fee_name  = $item_fee->get_name();
			$fee_total = $item_fee->get_total();
			$wallet_name = __( 'Via wallet', 'wallet-system-for-woocommerce' );
			if ( $wallet_name === $fee_name ) {
				$order_status   = array( 'pending', 'on-hold' );
				$payment_status = array( 'processing', 'completed' );
				if ( in_array( $old_status, $order_status ) && in_array( $new_status, $payment_status ) ) {
					$fees   = abs( $fee_total );
					$amount = $fees;
					$debited_amount = apply_filters( 'wps_wsfw_update_wallet_to_base_price', $fees, $order_currency );
					if ( $walletamount < $debited_amount ) {
						$walletamount = 0;
					} else {
						$walletamount -= $debited_amount;
					}
					update_user_meta( $userid, 'wps_wallet', $walletamount );
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
				'description' => __( 'Upload CSV file for adding wallet balance to users. You can download csv file through icon', 'wallet-system-for-woocommerce' ),
				'name'        => 'import_wallet_for_users',
				'id'          => 'import_wallet_for_users',
				'value'       => '',
				'class'       => 'wsfw-number-class',
			),

			array(
				'type'        => 'import_submit',
				'name'        => 'import_wallets',
				'id'          => 'import_wallets',
				'button_text' => __( 'IMPORT WALLET', 'wallet-system-for-woocommerce' ),
				'class'       => 'wsfw-button-class',
			),
		);
		return $wsfw_settings_import_wallet;
	}

	/**
	 * Settings for wallet withdrawal page
	 *
	 * @param array $wsfw_widthdrawal_setting array for showing fields.
	 * @return array $wsfw_widthdrawal_setting return fields
	 */
	public function wsfw_admin_withdrawal_setting_page( $wsfw_widthdrawal_setting ) {
		array(
			'msg'     => $wps_wsfw_error_text,
			'msgType' => 'error',
		);
		$wallet_methods = get_option( 'wallet_withdraw_methods', '' );
		if ( ! empty( $wallet_methods ) && is_array( $wallet_methods ) ) {
			$bank_transfer = $wallet_methods['banktransfer']['value'];
			$paypal        = $wallet_methods['paypal']['value'];
		}
		$wsfw_widthdrawal_setting = array(

			array(
				'title'       => __( 'Minimum Withdrawal Amount ( ', 'wallet-system-for-woocommerce' ) . get_woocommerce_currency_symbol() . ' )',
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
				'title'       => __( 'Paypal', 'wallet-system-for-woocommerce' ),
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
				'id'          => 'save_withdrawn_settings',
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

		$userdata    = array();
		$userdata[0] = array( 'User Id', 'Wallet Balance' );
		$users       = get_users();
		foreach ( $users as $key => $user ) {
			$user_id        = $user->ID;
			$wallet_balance = get_user_meta( $user_id, 'wps_wallet', true );
			if ( empty( $wallet_balance ) ) {
				$userdata[] = array( $user_id, 0 );
			} else {
				$userdata[] = array( $user_id, $wallet_balance );
			}
		}
		wp_send_json( $userdata );

	}

		/**
		 * Update wallet and status on changing status of wallet request
		 *
		 * @return void
		 */
	public function restrict_user_from_wallet_access() {
		$update = true;
		check_ajax_referer( 'wp_rest', 'nonce' );
		$user_id            = ( isset( $_POST['user_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : '';
		$restriction_status = ( isset( $_POST['restriction_status'] ) ) ? sanitize_text_field( wp_unslash( $_POST['restriction_status'] ) ) : '';

		if ( ! empty( $user_id ) ) {

			if ( 'true' == $restriction_status ) {
				update_user_meta( $user_id, 'user_restriction_for_wallet', 'restricted', true );
			} else {
				delete_user_meta( $user_id, 'user_restriction_for_wallet' );
			}
		}
		$message       = array(
			'msg'     => 'success',
			'msgType' => 'success',
		);

		wp_send_json( $message );

	}

	/**
	 * Download Pdf Via Export Pdf Button function
	 *
	 * @return void
	 */
	public function wps_wsfw_download_pdf_file_callback() {
		if ( isset( $_GET['wps_wsfw_export_pdf'] ) ) {

			global $wpdb;
			$table_name   = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
			$transactions = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction ORDER BY Id DESC' );
			if ( ! empty( $transactions ) && is_array( $transactions ) ) {
				$i = 1;
				$pdf_html = '';
				$pdf_html .= '<table>';
				$pdf_html .= '<thead>';
				$pdf_html .= '<tr>';
				$pdf_html .= '<th>#</th>';
				$pdf_html .= '<th>Name</th>';
				$pdf_html .= '<th>Email</th>';
				$pdf_html .= '<th>Role</th>';
				$pdf_html .= '<th>Amount</th>';
				$pdf_html .= '<th>Payment Method</th>';
				$pdf_html .= '<th>Details</th>';
				$pdf_html .= '<th>Transaction ID</th>';
				$pdf_html .= '<th>Date</th>';
				$pdf_html .= '</tr>';
				$pdf_html .= '</thead>';
				$pdf_html .= '<tbody>';
				foreach ( $transactions as $transaction ) {
					$user = get_user_by( 'id', $transaction->user_id );
					if ( $user ) {
						$display_name = $user->display_name;
						$useremail    = $user->user_email;
						$user_role = '';
						if ( is_array( $user->roles ) && ! empty( $user->roles ) ) {
							$user_role    = $user->roles[0];
						}
					} else {
						$display_name = '';
						$useremail    = '';
						$user_role    = '';
					}

					$pdf_html .= '<tr>';
					$pdf_html .= '<td>' . $i . '</td>';
					$pdf_html .= '<td>' . $display_name . ' #' . $transaction->user_id . '</td>';
					$pdf_html .= '<td>' . $useremail . '</td>';
					$pdf_html .= '<td>' . $user_role . '</td>';
					$pdf_html .= '<td>' . get_woocommerce_currency() . ' ' . $transaction->amount . '</td>';
					$pdf_html .= '<td>' . $transaction->payment_method . '</td>';
					$pdf_html .= '<td>' . html_entity_decode( $transaction->transaction_type ) . '</td>';
					$pdf_html .= '<td>' . $transaction->id . '</td>';
					$date_format = get_option( 'date_format', 'm/d/Y' );
					$date        = date_create( $transaction->date );
					$pdf_html .= '<td>' . date_format( $date, $date_format ) . ' ' . esc_html( date_format( $date, 'H:i:s' ) ) . '</td>';
					$pdf_html .= '</tr>';
					$i++;
				}
				$pdf_html .= '</tbody></table>';
				require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'package/lib/dompdf/vendor/autoload.php';
				$dompdf = new Dompdf( array( 'enable_remote' => true ) );
				$dompdf->setPaper( 'A4', 'landscape' );
				$upload_dir_path = WALLET_SYSTEM_FOR_WOOCOMMERCE_UPLOAD_DIR . '/transaction_pdf';
				if ( ! is_dir( $upload_dir_path ) ) {
					wp_mkdir_p( $upload_dir_path );
					chmod( $upload_dir_path, 0775 );
				}
				$dompdf->loadHtml( $pdf_html );
				@ob_end_clean(); // phpcs:ignore
				$dompdf->render();
				$dompdf->set_option( 'isRemoteEnabled', true );
				$output = $dompdf->output();
				$generated_pdf = file_put_contents( $upload_dir_path . '/transaction.pdf', $output );
				$file = $upload_dir_path . '/transaction.pdf';
				if ( file_exists( $file ) ) {
					header( 'Content-Description: File Transfer' );
					header( 'Content-Type: application/octet-stream' );
					header( 'Content-Disposition: attachment; filename="' . basename( $file ) . '"' );
					header( 'Expires: 0' );
					header( 'Cache-Control: must-revalidate' );
					header( 'Pragma: public' );
					header( 'Content-Length: ' . filesize( $file ) );
					readfile( $file );
					exit;
				}
			}
		}
	}

	/**
	 * Update wallet and status on changing status of wallet request
	 *
	 * @return void
	 */
	public function change_wallet_withdrawan_status() {
		$update = true;
		check_ajax_referer( 'wp_rest', 'nonce' );
		if ( empty( $_POST['withdrawal_id'] ) ) {
			$wps_wsfw_error_text = esc_html__( 'Withdrawal Id is not given', 'wallet-system-for-woocommerce' );
			$message             = array(
				'msg'     => $wps_wsfw_error_text,
				'msgType' => 'error',
			);
			$update = false;
		}
		if ( empty( $_POST['user_id'] ) ) {
			$wps_wsfw_error_text = esc_html__( 'User Id is not given', 'wallet-system-for-woocommerce' );
			$message             = array(
				'msg'     => $wps_wsfw_error_text,
				'msgType' => 'error',
			);
			$update = false;
		}
		if ( $update ) {
			$updated_status     = ( isset( $_POST['status'] ) ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
			$withdrawal_id      = ( isset( $_POST['withdrawal_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['withdrawal_id'] ) ) : '';
			$user_id            = ( isset( $_POST['user_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : '';
			$withdrawal_request = get_post( $withdrawal_id );
			if ( 'approved' === $updated_status ) {
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$withdrawal_amount = get_post_meta( $withdrawal_id, 'wps_wallet_withdrawal_amount', true );
				$wps_wsfwp_wallet_withdrawal_fee_amount = get_post_meta( $withdrawal_id, 'wps_wsfwp_wallet_withdrawal_fee_amount', true );
				if ( $user_id ) {
					$walletamount = get_user_meta( $user_id, 'wps_wallet', true );
					$walletamount = ( ! empty( $walletamount ) ) ? $walletamount : 0;
					if ( $walletamount < $withdrawal_amount ) {
						$walletamount = 0;
					} else {
						if ( $wps_wsfwp_wallet_withdrawal_fee_amount > 0 ) {

							$walletamount -= $withdrawal_amount + $wps_wsfwp_wallet_withdrawal_fee_amount;
						} else {

							$walletamount -= $withdrawal_amount;
						}
					}
					$update_wallet = update_user_meta( $user_id, 'wps_wallet', $walletamount );
					delete_user_meta( $user_id, 'disable_further_withdrawal_request' );
					if ( $update_wallet ) {
						$withdrawal_request->post_status = 'approved';
						wp_update_post( $withdrawal_request );

						$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
						if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
							$user       = get_user_by( 'id', $user_id );
							$name       = $user->first_name . ' ' . $user->last_name;
							$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
							$mail_text .= wc_price( $withdrawal_amount ) . __( ' has been debited from wallet through your withdrawing request.', 'wallet-system-for-woocommerce' );
							$to         = $user->user_email;
							$from       = get_option( 'admin_email' );
							$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
							$headers    = 'MIME-Version: 1.0' . "\r\n";
							$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
							$headers   .= 'From: ' . $from . "\r\n" .
								'Reply-To: ' . $to . "\r\n";

							$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
						}
					}
					if ( $wps_wsfwp_wallet_withdrawal_fee_amount > 0 ) {
						$withdrawal_amount = $withdrawal_amount + $wps_wsfwp_wallet_withdrawal_fee_amount . __( '( inculding ', 'wallet-system-for-woocommerce' ) . $wps_wsfwp_wallet_withdrawal_fee_amount . __( ')', 'wallet-system-for-woocommerce' );
					}
					$transaction_type = __( 'Wallet debited through user withdrawing request ', 'wallet-system-for-woocommerce' ) . '<a href="#" >#' . $withdrawal_id . '</a>';
					if ( $wps_wsfwp_wallet_withdrawal_fee_amount ) {

						$transaction_type .= __( '( inculding Withdrawal Fee of ', 'wallet-system-for-woocommerce' ) . get_woocommerce_currency_symbol() . '' . $wps_wsfwp_wallet_withdrawal_fee_amount . __( ')', 'wallet-system-for-woocommerce' );
					}
					$transaction_data = array(
						'user_id'          => $user_id,
						'amount'           => $withdrawal_amount,
						'currency'         => get_woocommerce_currency(),
						'payment_method'   => esc_html__( 'Manually By Admin', 'wallet-system-for-woocommerce' ),
						'transaction_type' => htmlentities( $transaction_type ),
						'order_id'         => $withdrawal_id,
						'note'             => '',

					);

					$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
					if ( $result ) {
						$wps_wsfw_error_text = esc_html__( 'Wallet withdrawal request is approved for user #', 'wallet-system-for-woocommerce' ) . $user_id;
						$message             = array(
							'msg'     => $wps_wsfw_error_text,
							'msgType' => 'success',
						);
					} else {
						$wps_wsfw_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
						$message             = array(
							'msg'     => $wps_wsfw_error_text,
							'msgType' => 'error',
						);
					}
				};
			}
			if ( 'rejected' === $updated_status ) {
				$withdrawal_amount = get_post_meta( $withdrawal_id, 'wps_wallet_withdrawal_amount', true );
				if ( $user_id ) {
					$withdrawal_request->post_status = 'rejected';
					wp_update_post( $withdrawal_request );
					delete_user_meta( $user_id, 'disable_further_withdrawal_request' );
					$wps_wsfw_error_text = esc_html__( 'Wallet withdrawal request is rejected for user #', 'wallet-system-for-woocommerce' ) . $user_id;
					$message             = array(
						'msg'     => $wps_wsfw_error_text,
						'msgType' => 'success',
					);
				};
			}
			if ( 'pending1' === $updated_status ) {
				$withdrawal_amount = get_post_meta( $withdrawal_id, 'wps_wallet_withdrawal_amount', true );
				if ( $user_id ) {
					$withdrawal_request->post_status = 'pending1';
					wp_update_post( $withdrawal_request );
					$wps_wsfw_error_text = esc_html__( 'Wallet withdrawal request status is changed to pending for user #', 'wallet-system-for-woocommerce' ) . $user_id;
					$message             = array(
						'msg'     => $wps_wsfw_error_text,
						'msgType' => 'success',
					);
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
				'description'     => __( 'Merchant can see all withdrawal request of users', 'wallet-system-for-woocommerce' ),
				'supports'        => array( 'title', 'custom-fields' ),
				'public'          => true,
				'rewrite'         => array( 'slug' => 'wallet_withdrawal' ),
				'menu_icon'       => 'dashicons-groups',
				'show_in_menu'    => false,
				'capability_type' => 'post',
				'show_ui'         => true,
			)
		);
		// register custom status rejected.
		register_post_status(
			'approved',
			array(
				'label'                     => _x( 'Approved', 'wallet-system-for-woocommerce' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				/* translators: %s: search term */
				'label_count'               => _n_noop( 'Approved <span class="count">(%s)</span>', 'Approved <span class="count">(%s)</span>' ), // phpcs:ignore
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
			)
		);
		// register custom status rejected.
		register_post_status(
			'rejected',
			array(
				'label'                     => _x( 'Rejected', 'wallet-system-for-woocommerce' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				/* translators: %s: search term */
				'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>' ), // phpcs:ignore
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
			)
		);
		register_post_status(
			'pending1',
			array(
				'label'                     => _x( 'Pending', 'wallet-system-for-woocommerce' ),
				'public'                    => true,
				/* translators: %s: search term */
				'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ), // phpcs:ignore
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
			)
		);

		// Check transaction table is updated with new field or not.
		$updated_transaction_table = get_option( 'wps_wsfw_updated_transaction_table' );
		if ( ! $updated_transaction_table ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
			if ( $wpdb->get_var( 'show tables like "' . $wpdb->prefix . 'wps_wsfw_wallet_transaction"' ) == $table_name ) {
				$column = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'currency' ", DB_NAME, $table_name ) );

				if ( empty( $column ) ) {
					$alter_table = $wpdb->query( 'ALTER TABLE ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction ADD currency varchar( 20 ) NULL' );
					if ( $alter_table ) {
						$currency = get_woocommerce_currency();
						$wpdb->query( $wpdb->prepare( 'UPDATE ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction SET currency = %s', $currency ) );
						update_option( 'wps_wsfw_updated_transaction_table', 'true' );
					}
				}
			}
		}

	}

	/**
	 * Add custom post status in withdrawal posts
	 *
	 * @return void
	 */
	public function wsfw_append_wallet_status_list() {
		global $post;
		$label = '';
		if ( 'wallet_withdrawal' === $post->post_type ) {
			if ( 'approved' === $post->post_status ) {
				$complete = ' selected="selected"';
				$label    = 'Approved';
				$selected = 'selected';
			}
			if ( 'rejected' === $post->post_status ) {
				$label    = 'Rejected';
				$selected = 'selected';
			}

			echo '<script>
			jQuery(document).ready(function($){
				$(".misc-pub-post-status #post-status-display").append("<span id=\"post-status-display\"> ' . esc_html( $label ) . ' </span>");
				$("select#post_status").append("<option value=\"approved\" >Approved</option><option value=\"rejected\" >Rejected</option>");
				
			});
			</script>
			';
		}
	}

	/**
	 * Add custom columns related to wallet withdrawal
	 *
	 * @param array $columns wp list table columns.
	 * @return array
	 */
	public function wsfw_add_columns_to_withdrawal( $columns ) {
		// removing the author column from post listing table.
		unset( $columns['author'] );
		foreach ( $columns as $key => $column ) {
			if ( 'title' === $key ) {
				$columns['withdrawal_id'] = 'Withdrawal ID';
				$columns[ $key ]          = 'Username';
			}
			if ( 'date' === $key ) {
				unset( $columns[ $key ] );
				$columns['email']             = esc_html__( 'Email', 'wallet-system-for-woocommerce' );
				$columns['withdrawal_amount'] = esc_html__( 'Amount', 'wallet-system-for-woocommerce' );
				$columns['payment_method']    = esc_html__( 'Payment Method', 'wallet-system-for-woocommerce' );
				$columns['status']            = esc_html__( 'Status', 'wallet-system-for-woocommerce' );
				$columns[ $key ]              = $column;
			}
		}
		return $columns;
	}

	/**
	 * Show custom column data in withrawal request custom post type table list
	 *
	 * @param string $column_name wp list table column names.
	 * @param int    $post_id post id.
	 * @return void
	 */
	public function wsfw_show_withdrawal_columns_data( $column_name, $post_id ) {

		switch ( $column_name ) {
			case 'withdrawal_id':
				echo esc_html( $post_id );
				break;
			case 'email':
				$user_id = get_post_meta( $post_id, 'wallet_user_id', true );
				if ( $user_id ) {
					$user      = get_user_by( 'id', $user_id );
					$useremail = $user->user_email;
					echo esc_html( $useremail );
				}
				break;
			case 'withdrawal_amount':
				$withdrawal_amount = get_post_meta( $post_id, 'wps_wallet_withdrawal_amount', true );
				if ( $withdrawal_amount ) {
					echo wp_kses_post( wc_price( $withdrawal_amount ) );
				}
				break;
			case 'payment_method':
				echo esc_html( get_post_meta( $post_id, 'wallet_payment_method', true ) );
				break;
			case 'status':
				$post = get_post( $post_id );
				echo esc_html( $post->post_status );
				break;
		}

	}

	/**
	 * Update status of withdrawal requesting as approved
	 *
	 * @param int    $post_id post id.
	 * @param object $post post object.
	 * @return void
	 */
	public function wsfw_enable_withdrawal_request( $post_id, $post ) {
		$post_status = $post->post_status;
		if ( 'approved' === $post_status ) {

			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
			$withdrawal_amount      = get_post_meta( $post_id, 'wps_wallet_withdrawal_amount', true );

			$user_id        = get_post_meta( $post_id, 'wallet_user_id', true );
			$payment_method = get_post_meta( $post_id, 'wallet_payment_method', true );
			if ( $user_id ) {
				$walletamount = get_user_meta( $user_id, 'wps_wallet', true );
				$walletamount = ( ! empty( $walletamount ) ) ? $walletamount : 0;
				if ( $walletamount < $withdrawal_amount ) {
					$walletamount = 0;
				} else {
					$walletamount -= $withdrawal_amount;
				}
				update_user_meta( $user_id, 'wps_wallet', $walletamount );
				delete_user_meta( $user_id, 'disable_further_withdrawal_request' );

				$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
					$user       = get_user_by( 'id', $user_id );
					$name       = $user->first_name . ' ' . $user->last_name;
					$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
					$mail_text .= wc_price( $withdrawal_amount ) . __( 'has been debited from wallet through user withdrawing request.', 'wallet-system-for-woocommerce' );
					$to         = $user->user_email;
					$from       = get_option( 'admin_email' );
					$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
					$headers    = 'MIME-Version: 1.0' . "\r\n";
					$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
					$headers   .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to . "\r\n";

					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
				}

				$transaction_type = __( 'Wallet debited through user withdrawing request ', 'wallet-system-for-woocommerce' ) . '<a href="#" >#' . $post_id . '</a>';
				if ( $wps_wsfwp_wallet_withdrawal_fee_amount ) {

					$transaction_type .= __( '( inculding Withdrawal Fee of ', 'wallet-system-for-woocommerce' ) . get_woocommerce_currency_symbol() . ' ' . $wps_wsfwp_wallet_withdrawal_fee_amount . __( ')', 'wallet-system-for-woocommerce' );
				}
				$transaction_data = array(
					'user_id'          => $user_id,
					'amount'           => $withdrawal_amount,
					'currency'         => get_woocommerce_currency(),
					'payment_method'   => $payment_method,
					'transaction_type' => htmlentities( $transaction_type ),
					'order_id'         => $post_id,
					'note'             => '',

				);

				$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

			};
		}
	}

	/**
	 * Settings for wallet in frontend
	 *
	 * @param array $wsfw_settings_wallet array of fields.
	 * @return array
	 */
	public function wsfw_admin_wallet_setting_page( $wsfw_settings_wallet ) {
		$wsfw_settings_wallet = array(
			array(
				'type'        => 'submit',
				'name'        => 'wallet_topup_setting',
				'id'          => 'wallet_topup_setting',
				'button_text' => __( 'Save Changes', 'wallet-system-for-woocommerce' ),
				'class'       => 'wsfw-button-class',
				'wsfw-update',
			),
		);
		return $wsfw_settings_wallet;
	}

	/**
	 * Fields for updating wallet of all users at bulk
	 *
	 * @param array $wsfw_update_wallet array of fields.
	 * @return array
	 */
	public function wsfw_admin_update_wallet_page( $wsfw_update_wallet ) {
		$wsfw_update_wallet = array(
			// amount field.
			array(
				'title'       => __( 'Amount ( ', 'wallet-system-for-woocommerce' ) . get_woocommerce_currency_symbol() . ' )',
				'type'        => 'number',
				'description' => __( 'Certain amount want to add/deduct from all users wallet', 'wallet-system-for-woocommerce' ),
				'name'        => 'wsfw_wallet_amount_for_users',
				'id'          => 'wsfw_wallet_amount_for_users',
				'value'       => '',
				'class'       => 'wsfw-number-class',
				'placeholder' => '',
			),
			// wallet action.
			array(
				'title'       => __( 'Action', 'wallet-system-for-woocommerce' ),
				'type'        => 'oneline-radio',
				'description' => __( 'Whether want to add/deduct certain amount from wallet of all users', 'wallet-system-for-woocommerce' ),
				'name'        => 'wsfw_wallet_action_for_users',
				'id'          => 'wsfw_wallet_action_for_users',
				'value'       => '',
				'class'       => 'wsfw-radio-class',
				'placeholder' => __( 'Radio Demo', 'wallet-system-for-woocommerce' ),
				'options'     => array(
					'credit' => __( 'Credit', 'wallet-system-for-woocommerce' ),
					'debit'  => __( 'Debit', 'wallet-system-for-woocommerce' ),
				),
			),

			array(
				'title'       => __( 'Transaction Detail', 'wallet-system-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Enter the details you want to show to user', 'wallet-system-for-woocommerce' ),
				'name'        => 'wsfw_wallet_transaction_details_for_users',
				'id'          => 'wsfw_wallet_transaction_details_for_users',
				'value'       => '',
				'placeholder' => __( 'Transaction Detail', 'wallet-system-for-woocommerce' ),
				'class'       => 'wws-text-class',
			),
			array(
				'type'        => 'button',
				'name'        => 'update_wallet',
				'id'          => 'update_wallet',
				'button_text' => __( 'Update Wallet', 'wallet-system-for-woocommerce' ),
				'class'       => 'wsfw-button-class',
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

		$product_id = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		// custom css for accessing outside the plugin.
		echo '<style type="text/css">
		.wps_wallet_actions .wallet-manage::after{font-family:Dashicons;font-weight:400;text-transform:none;-webkit-font-smoothing:antialiased;text-indent:0;top:0;left:0;width:100%;height:100%;text-align:center;content:"\f111";margin:0}
		.wps_wallet_actions .view-transactions::after{font-family:Dashicons;font-weight:400;text-transform:none;-webkit-font-smoothing:antialiased;text-indent:0;top:0;left:0;width:100%;height:100%;text-align:center;content:"\f177";margin:0}
		.wallet-status{text-transform:capitalize;display:inline-flex;line-height:2.5em;color:#777;border-radius:4px;border-bottom:1px solid rgba(0,0,0,.05);margin:-.25em 0;cursor:inherit!important;white-space:nowrap;max-width:100%}	
		.wallet-status span{margin:0 1em;overflow:hidden;text-overflow:ellipsis}
		.column-status{text-transform:capitalize}
		.order-status.status-on-hold{background:#f8dda7;color:#94660c}
		.order-status.status-processing{background:#c6e1c6;color:#5b841b}
		.order-status.status-completed{background:#c8d7e1;color:#2e4453}
		.order-status.status-failed{background:#eba3a3;color:#761919}
		.order-status.status-trash{background:#eba3a3;color:#761919}
		.order-status.status-cancelled,.order-status.status-pending,.order-status.status-refunded{background:#e5e5e5}
		.wallet_shop_order .wp-list-table tbody .column-status{padding:1.2em 10px;line-height:26px}
		.form-table td .error {color:red;}
		.wp-list-table .type-product#post-' . esc_html( $product_id ) . ' {display:none;}
		.wallet_shop_order .bulkactions #clear_datefilter {margin-left:3px;}
		.woocommerce_page_wallet_shop_order #ui-datepicker-div {background: #fff;padding: 15px;font-size:16px;border-radius: 5px;}
		.woocommerce_page_wallet_shop_order #ui-datepicker-div .ui-datepicker-header{display:flex;flex-wrap:wrap;max-width:180px;justify-content:center}
		.woocommerce_page_wallet_shop_order #ui-datepicker-div .ui-datepicker-header .ui-corner-all{padding:5px;flex:0 0 40%}
		.woocommerce_page_wallet_shop_order #ui-datepicker-div .ui-datepicker-header .ui-datepicker-next{text-align:right}
		.woocommerce_page_wallet_shop_order #ui-datepicker-div .ui-datepicker-header .ui-datepicker-title select{padding:5px 20px 5px 15px!important;width:80px}
		.woocommerce_page_wallet_shop_order #ui-datepicker-div .ui-datepicker-calendar{margin:auto;}
		.woocommerce_page_wallet_shop_order #ui-datepicker-div .ui-datepicker-calendar td a{line-height:20px;text-decoration: none;}
		.wp-list-table.walletrechargeorders thead tr .column-date1, .wp-list-table.walletrechargeorders tbody tr .column-date1, .wp-list-table.walletrechargeorders tfoot tr .column-date1{display:none;}	
		.edit-user-wallet a{text-decoration: none;}
		.edit-user-wallet a span{vertical-align: middle;}
		</style>
    	';

		global $current_screen;
		if ( 'makewebbetter_page_wallet_shop_order' == $current_screen->id ) {
			$url = admin_url( 'post-new.php?post_type=wallet_shop_order' );
			?>
			<script type="text/javascript">
				jQuery(document).ready( function($) {
					jQuery(jQuery(".wrap h1")[0]).append("<a href='<?php echo esc_attr( $url ); ?>' class='add-new-h2'>Add Order</a>");
				});
			</script>
			<?php
		}

		if ( isset( $current_screen->id ) && ( 'profile' == $current_screen->id || 'user-edit' == $current_screen->id ) ) {
			?>
		<script>
		jQuery(document).ready(function() { 
			jQuery(document).on( 'blur','#wps_wallet', function(){
				var amount = jQuery('#wps_wallet').val();
				if ( amount <= 0 ) {
					jQuery('.error').show();
					jQuery('.error').html('Enter amount greater than 0');
				} else {
					jQuery('.error').hide();
				}	
			});
		});
		</script>
			<?php
		}
	}

	/**
	 * Include template for wallet edit page
	 *
	 * @return void
	 */
	public function edit_wallet_of_user() {
		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/wps-edit-wallet.php';
	}

	/**
	 * Includes user's wallet transactions template
	 *
	 * @return void
	 */
	public function show_users_wallet_transactions() {
		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/wps-user-wallet-transactions.php';
	}

	/**
	 * Includes  wallet recharge relate custom table(WP_LIST)
	 *
	 * @return void
	 */
	public function show_wallet_orders() {
		include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/wps-custom-table-for-orders.php';
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
						'add_new_item'       => __( 'Add New Order', 'wallet-system-for-woocommerce' ),
						'add_new'            => __( 'Add Order', 'wallet-system-for-woocommerce' ),
						'view_item'          => __( 'View Wallet Recharge Order', 'wallet-system-for-woocommerce' ),
						'edit_item'          => __( 'Edit Wallet Recharge Order', 'wallet-system-for-woocommerce' ),
						'update_item'        => __( 'Update Order', 'wallet-system-for-woocommerce' ),
						'search_items'       => __( 'Search orders', 'wallet-system-for-woocommerce' ),
						'not_found'          => __( 'Not Found Order', 'wallet-system-for-woocommerce' ),
						'not_found_in_trash' => __( 'Not found in Trash', 'wallet-system-for-woocommerce' ),
					),
					'description'                      => __( 'Merchant can see all wallet recharge orders.', 'wallet-system-for-woocommerce' ),
					'public'                           => false,
					'show_ui'                          => true,
					'capability_type'                  => 'shop_order',
					'map_meta_cap'                     => true,
					'publicly_queryable'               => false,
					'exclude_from_search'              => true,
					'show_in_menu'                     => false,
					'hierarchical'                     => false,
					'show_in_nav_menus'                => false,
					'rewrite'                          => false,
					'query_var'                        => false,
					'supports'                         => array( 'title', 'comments', 'custom-fields' ),
					'has_archive'                      => false,
					'exclude_from_orders_screen'       => true,
					'add_order_meta_boxes'             => true,
					'exclude_from_order_count'         => true,
					'exclude_from_order_views'         => false,
					'exclude_from_order_webhooks'      => false,
					'exclude_from_order_reports'       => false,
					'exclude_from_order_sales_reports' => false,
					'class_name'                       => 'WC_Order',
				)
			)
		);
	}

	/**
	 * Saving the plugin setting to new option name
	 *
	 * @return void
	 */
	public function wsfw_upgrade_completed() {

		// update user wallet.
		$users = get_users();
		foreach ( $users as $user ) {
			$user_id = $user->ID;
			$wallet  = get_user_meta( $user_id, 'wps_all_in_one_wallet', true );
			if ( ! empty( $wallet ) ) {
				$updated_wallet = update_user_meta( $user_id, 'wps_wallet', $wallet );
				if ( $updated_wallet ) {
					delete_user_meta( $user_id, 'wps_all_in_one_wallet' );
				}
			}
		}
		// update wallet product id in optin table.
		$product_id = get_option( 'wps_wcb_product_id' );
		if ( $product_id ) {
			$updated_wallet_id = update_option( 'wps_wsfw_rechargeable_product_id', $product_id );
			if ( $updated_wallet_id ) {
				delete_option( 'wps_wcb_product_id' );
			}

			// update post title of wallet product.
			$wallet_product = get_post( $product_id );
			$wallet_product->post_title = 'Rechargeable Wallet Product';
			wp_update_post( $wallet_product );
		}

		// update general settings of plugin.
		$wcb_general_values = get_option( 'wps_wcb_general' );
		if ( $wcb_general_values ) {
			$wps_wsfw_enable = $wcb_general_values['wenable'];
			$updated_general = update_option( 'wps_wsfw_enable', $wps_wsfw_enable );
			if ( $updated_general ) {
				delete_option( 'wps_wcb_general' );
			}
		}

		// update wallet recharge enable or not.
		$wps_topup_product = get_option( 'wps_wcb_topup_product' );
		if ( $wps_topup_product ) {
			$wps_topup_product_enable = $wps_topup_product['enable'];
			$enable_recharge          = update_option( 'wsfw_enable_wallet_recharge', $wps_topup_product_enable );
			if ( $enable_recharge ) {
				delete_option( 'wps_wcb_topup_product' );
			}
		}

		// create transcation table if not exist.
		global $wpdb;
		$table_name = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
		if ( $wpdb->get_var( 'SHOW TABLES LIKE "' . $wpdb->prefix . 'wps_wsfw_wallet_transaction"' ) != $table_name ) {
			$wpdb_collate = $wpdb->collate;
			$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
				id bigint(20) unsigned NOT NULL auto_increment,
				user_id bigint(20) unsigned NULL,
				amount double,
				currency varchar( 20 ) NOT NULL,
				transaction_type varchar(200) NULL,
				payment_method varchar(50) NULL,
				transaction_id varchar(50) NULL,
				note varchar(500) Null,
				date datetime,
				PRIMARY KEY  (Id),
				KEY user_id (user_id)
				)
				COLLATE {$wpdb_collate}";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

		}

		// update older transaction table data to new table.
		$older_table = $wpdb->prefix . 'wps_wcb_wallet_transactions';
		if ( $wpdb->get_var( 'SHOW TABLES LIKE "' . $wpdb->prefix . 'wps_wcb_wallet_transactions"' ) == $older_table ) {
			$user_transactions = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wps_wcb_wallet_transactions' );
			if ( ! empty( $user_transactions ) && is_array( $user_transactions ) ) {
				foreach ( $user_transactions as $user_transaction ) {

					$insert_array = array(
						'id'                => $user_transaction->transaction_id,
						'user_id'           => $user_transaction->user_id,
						'amount'            => $user_transaction->amount,
						'currency'          => $user_transaction->currency,
						'transaction_type'  => $user_transaction->details,
						'payment_method'    => '',
						'transaction_id'    => '',
						'note'              => '',
						'date'              => $user_transaction->date,
					);
					$wpdb->insert(
						$table_name,
						$insert_array
					);

				}

				$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wps_wcb_wallet_transactions' );

			}
		}

		update_option( 'wsfw_saved_older_walletkeys', 'true' );
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
	 * Add wallet amount as fee during subscription renewal.
	 *
	 * @param object $wps_new_order new order.
	 * @param int    $subscription_id subscription id.
	 * @return void
	 */
	public function wps_sfw_renewal_order_creation( $wps_new_order, $subscription_id ) {
		$wps_sfw_use_wallet = get_option( 'wps_sfw_enable_wallet_on_renewal_order', '' );
		if ( 'on' == $wps_sfw_use_wallet ) {
			$amount_type_for_wallet = get_option( 'wps_sfw_amount_type_wallet_for_renewal_order', '' );
			$amount_deduct_from_wallet = get_option( 'wps_sfw_amount_deduct_from_wallet_during_renewal_order', 0 );

			$fee = new WC_Order_Item_Fee();
			$fee->set_name( __( 'Via wallet', 'wallet-system-for-woocommerce' ) );
			$user_id       = $wps_new_order->get_user_id();
			$walletbalance = get_user_meta( $user_id, 'wps_wallet', true );
			$currency      = $wps_new_order->get_currency();

			if ( ! empty( $amount_deduct_from_wallet ) ) {
				$order_total = $wps_new_order->get_total();
				$order_total = apply_filters( 'wps_wsfw_update_wallet_to_base_price', $order_total, $currency );
				if ( 'fix' === $amount_type_for_wallet ) {
					if ( $amount_deduct_from_wallet >= $order_total ) {
						$amount_deduct = $order_total;
					} else {
						$amount_deduct = $amount_deduct_from_wallet;
					}
				} elseif ( 'percentage' === $amount_type_for_wallet ) {
					$converted_price = floatval( ( $order_total * $amount_deduct_from_wallet ) / 100 );
					if ( $converted_price >= $order_total ) {
						$amount_deduct = $order_total;
					} else {
						$amount_deduct = $converted_price;
					}
				}
				if ( ! empty( $amount_deduct ) ) {
					if ( ! empty( $walletbalance ) && false !== $walletbalance ) {
						$walletamount      = 0;
						$remaining_balance = 0;

						if ( $walletbalance >= $amount_deduct ) {
							$walletamount      = $amount_deduct;
							$remaining_balance = abs( $walletbalance - $amount_deduct );
						} else {
							$walletamount      = $walletbalance;
							$remaining_balance = abs( $walletbalance - $walletamount );
						}

						$fee->set_amount( -1 * $walletamount );
						$fee->set_total( -1 * $walletamount );
						$wps_new_order->add_item( $fee );
						$wps_new_order->calculate_totals();
						$order_id               = $wps_new_order->save();
						$update_wallet          = update_user_meta( $user_id, 'wps_wallet', $remaining_balance );
						$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
						$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
						if ( $update_wallet ) {
							$payment_method   = esc_html__( 'Automatically', 'wallet-system-for-woocommerce' );
							$transaction_type = esc_html__( 'Wallet is debited through subscription renewal', 'wallet-system-for-woocommerce' );
							if ( ! empty( $order_id ) ) {
								$order = wc_get_order( $order_id );
								if ( $order ) {
									$payment_method = $order->get_payment_method();
									if ( 'wps_wcb_wallet_payment_gateway' === $payment_method ) {
										$payment_method = esc_html__( 'Wallet Payment', 'wallet-system-for-woocommerce' );
									}
									$transaction_type = __( 'Wallet debited through subscription renewal ', 'wallet-system-for-woocommerce' ) . '<a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
								} else {
									$order_id = '';
								}
							}
							$transaction_data = array(
								'user_id'          => $user_id,
								'amount'           => $walletamount,
								'currency'         => $currency,
								'payment_method'   => $payment_method,
								'transaction_type' => htmlentities( $transaction_type ),
								'order_id'         => $order_id,
								'note'             => '',
							);
							$wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

							if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
								$user       = get_user_by( 'id', $user_id );
								$name       = $user->first_name . ' ' . $user->last_name;
								$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
								$mail_text .= __( 'Wallet debited by ', 'wallet-system-for-woocommerce' ) . wc_price( $walletamount, array( 'currency' => $currency ) ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
								$to         = $user->user_email;
								$from       = get_option( 'admin_email' );
								$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
								$headers    = 'MIME-Version: 1.0' . "\r\n";
								$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
								$headers   .= 'From: ' . $from . "\r\n" .
									'Reply-To: ' . $to . "\r\n";
								$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
							}
						}
					}
				}
			}
		}

	}

	/**
	 * WooCommerce Wallet System general seetings..
	 *
	 * @since    1.0.0
	 * @param array $wsfw_settings_general Settings fields.
	 */
	public function wps_wsfw_extra_settings_sfw( $wsfw_settings_general ) {
		if ( is_array( $wsfw_settings_general ) && ! empty( $wsfw_settings_general ) ) {
			$wsfw_settings_general[] = array(
				'title'       => __( 'Enable to use wallet amount on renewal order', 'wallet-system-for-woocommerce' ),
				'type'        => 'radio-switch',
				'description' => __( 'Enable to use wallet amount on renewal order', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_sfw_enable_wallet_on_renewal_order',
				'id'          => 'wps_sfw_enable_wallet_on_renewal_order',
				'value'       => 'on',
				'class'       => 'wsfw-radio-switch-class',
				'options'     => array(
					'yes' => __( 'YES', 'wallet-system-for-woocommerce' ),
					'no'  => __( 'NO', 'wallet-system-for-woocommerce' ),
				),
			);
			$wsfw_settings_general[] = array(
				'title'       => __( 'Apply amount type(Depending on order total)', 'wallet-system-for-woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Apply amount type', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_sfw_amount_type_wallet_for_renewal_order',
				'id'          => 'wps_sfw_amount_type_wallet_for_renewal_order',
				'value'       => get_option( 'wps_sfw_amount_type_wallet_for_renewal_order', '' ),
				'class'       => 'wpg-number-class',
				'options'     => array(
					'fix'        => __( 'Fix', 'wallet-system-for-woocommerce' ),
					'percentage' => __( 'Percentage', 'wallet-system-for-woocommerce' ),
				),
			);
			$wsfw_settings_general[] = array(
				'title'       => __( 'Enter the amount/percentage to be deducted  from wallet during order renewal( ', 'wallet-system-for-woocommerce' ) . get_woocommerce_currency_symbol() . ' )',
				'type'        => 'number',
				'description' => __( 'Enter the amount/percentage to be deducted  from wallet during order renewal.', 'wallet-system-for-woocommerce' ),
				'name'        => 'wps_sfw_amount_deduct_from_wallet_during_renewal_order',
				'id'          => 'wps_sfw_amount_deduct_from_wallet_during_renewal_order',
				'value'       => get_option( 'wps_sfw_amount_deduct_from_wallet_during_renewal_order', '' ),
				'class'       => 'wpg-number-class',
			);
		}
		return $wsfw_settings_general;
	}

	/** Migration code start from here */

	/**
	 * This function is used to migrate db keys.
	 *
	 * @return void
	 */
	public function wsfw_db_migrate_key() {
		self::wsfw_upgrade_wp_postmeta();
		self::wsfw_upgrade_wp_usermeta();
		self::wsfw_upgrade_wp_options();
		self::wsfw_rename_custom_table();
		self::wsfw_remove_pro_menus();
	}

	/**
	 * Update post meta keys.
	 *
	 * @return void
	 */
	public static function wsfw_upgrade_wp_postmeta() {
		$wsfw_upgrade_wp_postmeta_check = get_option( 'wsfw_upgrade_wp_postmeta_check', 'not_done' );
		if ( 'not_done' === $wsfw_upgrade_wp_postmeta_check ) {
			$post_meta_keys = array(
				'mwb_wallet_withdrawal_amount',
				'mwb_wallet_note',
			);

			foreach ( $post_meta_keys as $key => $meta_keys ) {
				$products = get_posts(
					array(
						'numberposts' => -1,
						'post_status' => 'approved',
						'fields'      => 'ids', // return only ids.
						'meta_key'    => $meta_keys, //phpcs:ignore
						'post_type'   => 'wallet_withdrawal',
						'order'       => 'ASC',
					)
				);

				if ( ! empty( $products ) && is_array( $products ) ) {
					foreach ( $products as $k => $product_id ) {
						$value   = get_post_meta( $product_id, $meta_keys, true );
						$new_key = str_replace( 'mwb_', 'wps_', $meta_keys );

						if ( ! empty( get_post_meta( $product_id, $new_key, true ) ) ) {
							continue;
						}
						update_post_meta( $product_id, $new_key, $value );
					}
				}
			}
			update_option( 'wsfw_upgrade_wp_postmeta_check', 'done' );
		}
	}

	/**
	 * Upgrade user meta.
	 *
	 * @return void
	 */
	public static function wsfw_upgrade_wp_usermeta() {
		$wsfw_upgrade_wp_usermeta_check = get_option( 'wsfw_upgrade_wp_usermeta_check', 'not_done' );
		if ( 'not_done' === $wsfw_upgrade_wp_usermeta_check ) {
			$all_users = get_users();
			if ( ! empty( $all_users ) && is_array( $all_users ) ) {
				foreach ( $all_users as $user ) {
					$user_id       = $user->ID;
					$wallet_amount = get_user_meta( $user_id, 'mwb_wallet', true );
					if ( ! empty( $wallet_amount ) ) {
						update_user_meta( $user_id, 'wps_wallet', $wallet_amount );
					}
				}
			}
			update_option( 'wsfw_upgrade_wp_usermeta_check', 'done' );
		}
	}

	/**
	 * Upgrade update options.
	 *
	 * @return void
	 */
	public static function wsfw_upgrade_wp_options() {
		$wsfw_upgrade_wp_options_check = get_option( 'wsfw_upgrade_wp_options_check', 'not_done' );
		if ( 'not_done' === $wsfw_upgrade_wp_options_check ) {
			$wp_options = array(
				'mwb_all_plugins_active'                                 => '',
				'mwb_wsfw_rechargeable_product_id'                       => '',
				'mwb_wsfw_enable'                                        => '',
				'mwb_wsfw_allow_refund_to_wallet'                        => '',
				'mwb_wsfw_enable_email_notification_for_wallet_update'   => '',
				'mwb_wsfw_wallet_rest_api_keys'                          => '',
				'mwb_wsfw_onboarding_data_sent'                          => '',
				'mwb_wsfw_onboarding_data_skipped'                       => '',
				'mwb_wsfw_updated_transaction_table'                     => '',
				'mwb_sfw_enable_wallet_on_renewal_order'                 => '',
				'mwb_sfw_amount_type_wallet_for_renewal_order'           => '',
				'mwb_sfw_amount_deduct_from_wallet_during_renewal_order' => '',
			);

			foreach ( $wp_options as $key => $value ) {
				$new_key = str_replace( 'mwb_', 'wps_', $key );
				if ( ! empty( get_option( $new_key ) ) ) {
					continue;
				}
				$new_value = get_option( $key, $value );
				update_option( $new_key, $new_value );
			}
			update_option( 'wsfw_upgrade_wp_options_check', 'done' );
		}
	}

	/**
	 * Rename custom table.
	 *
	 * @return void
	 */
	public static function wsfw_rename_custom_table() {
		global $wpdb;

		$wsfw_rename_custom_table_check = get_option( 'wsfw_rename_custom_table_check', 'not_done' );

		if ( 'not_done' === $wsfw_rename_custom_table_check ) {
			$table_name = $wpdb->prefix . 'mwb_wsfw_wallet_transaction';

			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) == $table_name ) {
				$old_table = $wpdb->prefix . 'mwb_wsfw_wallet_transaction';
				$new_table = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
				$rename = $wpdb->query( $wpdb->prepare( 'RENAME TABLE %1s TO %2s', $old_table, $new_table ) );

			}
			update_option( 'wsfw_rename_custom_table_check', 'done' );
		}
	}

	/**
	 * This function is used to upgrade shortcode.
	 *
	 * @return void
	 */
	public static function wsfw_replace_mwb_to_wps_in_shortcodes() {
		$wsfw_replace_mwb_to_wps_in_shortcodes_check = get_option( 'wsfw_replace_mwb_to_wps_in_shortcodes_check', 'not_done' );
		if ( 'not_done' === $wsfw_replace_mwb_to_wps_in_shortcodes_check ) {
			$all_post_ids = get_posts(
				array(
					'post_type'      => 'post',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
				)
			);

			$all_page_ids = get_posts(
				array(
					'post_type'      => 'page',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
				)
			);

			$result = array_merge( $all_post_ids, $all_page_ids );
			if ( ! empty( $result ) && is_array( $result ) ) {

				foreach ( $result as $id ) {
						$post    = get_post( $id );
						$content = $post->post_content;
						$array   = explode( ' ', $content );

					foreach ( $array as $key => $val ) {
						$html     = str_replace( 'MWB_', 'WPS_', $content );
						$html_two = str_replace( 'mwb-', 'wps-', $html );
						$my_post  = array(
							'ID'           => $id,
							'post_content' => $html_two,
						);
						wp_update_post( $my_post );
					}
				}
			}
			update_option( 'wsfw_replace_mwb_to_wps_in_shortcodes_check', 'done' );
		}
	}

	/**
	 * This function is used to remove pro plugin menus.
	 *
	 * @return void
	 */
	public static function wsfw_remove_pro_menus() {
		if ( ! empty( $GLOBALS['admin_page_hooks']['mwb-plugins'] ) ) {
			remove_menu_page( 'mwb-plugins' );
		}
	}

	/**
	 * To correct the data sy=tores in case of wallet recharge.
	 *
	 * @param [type] $data_stores are the variable which contains all data stores.
	 * @return string
	 */
	public function wsfw_admin_woocommerce_data_stores( $data_stores ) {
		if ( ! empty( $data_stores ) ) {

			$wallet_store = array( 'wallet_shop_order' => 'wallet_shop_order' );
			$data_stores  = array_merge( $wallet_store, $data_stores );

			return $data_stores;

		}
	}



	/**
	 * Process refund through wallet.
	 *
	 * @throws MyOtherException Value Handle.
	 * @throws Exception Handle.
	 */
	public function wps_wallet_order_refund_action() {

		ob_start();
		check_ajax_referer( 'order-item', 'security' );
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}
		$order_id = ! empty( $_POST['order_id'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) ) : '';
		$refund_amount = ! empty( $_POST['refund_amount'] ) ? wc_format_decimal( sanitize_text_field( wp_unslash( $_POST['refund_amount'] ) ), wc_get_price_decimals() ) : '';
		$refund_reason = ! empty( $_POST['refund_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['refund_reason'] ) ) : '';
		$line_item_qtys = ! empty( $_POST['line_item_qtys'] ) ? map_deep( wp_unslash( $_POST['line_item_qtys'] ), 'sanitize_text_field' ) : array();
		$line_item_totals = ! empty( $_POST['line_item_totals'] ) ? map_deep( wp_unslash( $_POST['line_item_totals'] ), 'sanitize_text_field' ) : array();
		$line_item_tax_totals = ! empty( $_POST['line_item_tax_totals'] ) ? map_deep( wp_unslash( $_POST['line_item_tax_totals'] ), 'sanitize_text_field' ) : array();

		$refund_api = ! empty( $_POST['api_refund'] ) ? sanitize_text_field( wp_unslash( $_POST['api_refund'] ) ) : '';
		$refund_restock = ! empty( $_POST['restock_refunded_items'] ) ? sanitize_text_field( wp_unslash( $_POST['restock_refunded_items'] ) ) : '';
		$api_refund = 'true' === $refund_api;
		$restock_refunded_items = 'true' === $refund_restock;
		$refund = false;
		$response_data = array();
		$order = wc_get_order( $order_id );
		$userid         = $order->get_user_id();
		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$wallet_id      = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$walletamount   = get_user_meta( $userid, 'wps_wallet', true );
		$walletamount   = empty( $walletamount ) ? 0 : $walletamount;
		$user                   = get_user_by( 'id', $userid );
		$name                   = $user->first_name . ' ' . $user->last_name;
		$order_currency = $order->get_currency();

		try {
			$order = wc_get_order( $order_id );
			$order_items = $order->get_items();
			$max_refund = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

			if ( ! $refund_amount || $max_refund < $refund_amount || 0 > $refund_amount ) {
				throw new exception( __( 'Invalid refund amount', 'wallet-system-for-woocommerce' ) );
			}
			// Prepare line items which we are refunding.
			$line_items = array();
			$item_ids = array_unique( array_merge( array_keys( $line_item_qtys, $line_item_totals ) ) );

			foreach ( $item_ids as $item_id ) {
				$line_items[ $item_id ] = array(
					'qty' => 0,
					'refund_total' => 0,
					'refund_tax' => array(),
				);
			}
			foreach ( $line_item_qtys as $item_id => $qty ) {
				$line_items[ $item_id ]['qty'] = max( $qty, 0 );
			}
			foreach ( $line_item_totals as $item_id => $total ) {
				$line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
			}
			foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
				$line_items[ $item_id ]['refund_tax'] = array_filter( array_map( 'wc_format_decimal', $tax_totals ) );
			}
			// Create the refund object.
			$refund = wc_create_refund(
				array(
					'amount' => $refund_amount,
					'reason' => $refund_reason,
					'order_id' => $order_id,
					'line_items' => $line_items,
					'refund_payment' => $api_refund,
					'restock_items' => $restock_refunded_items,
				)
			);
			if ( ! is_wp_error( $refund ) ) {

				$credited_amount = apply_filters( 'wps_wsfw_update_wallet_to_base_price', $refund_amount, $order_currency );
				$walletamount   += $credited_amount;
				$transaction_id = update_user_meta( $userid, 'wps_wallet', $walletamount );

				if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
					$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
					$mail_text .= __( 'Wallet credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $refund_amount, array( 'currency' => $order->get_currency() ) ) . __( ' through order refund.', 'wallet-system-for-woocommerce' );
					$to         = $user->user_email;
					$from       = get_option( 'admin_email' );
					$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
					$headers    = 'MIME-Version: 1.0' . "\r\n";
					$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
					$headers   .= 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $to . "\r\n";
					$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );

				}

				$transaction_type = esc_html__( 'Wallet credited through order refund ', 'wallet-system-for-woocommerce' ) . '<a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
				$transaction_data = array(
					'user_id'          => $userid,
					'amount'           => $refund_amount,
					'currency'         => $order->get_currency(),
					'payment_method'   => esc_html__( 'Manually by admin through refund', 'wallet-system-for-woocommerce' ),
					'transaction_type' => htmlentities( $transaction_type ),
					'order_id'         => $order_id,
					'note'             => '',
				);
				$transaction_id = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

				if ( ! $transaction_id ) {
					throw new Exception( __( 'Refund not credited to customer', 'wallet-system-for-woocommerce' ) );
				} else {
					do_action( 'wps_wallet_order_refund_actioned', $order, $refund, $transaction_id );
				}
			}

			if ( is_wp_error( $refund ) ) {
				throw new Exception( $refund->get_error_message() );
			}

			if ( did_action( 'woocommerce_order_fully_refunded' ) ) {
				$response_data['status'] = 'fully_refunded';
			}

			wp_send_json_success( $response_data );
		} catch ( Exception $ex ) {
			if ( $refund && is_a( $refund, 'WC_Order_Refund' ) ) {
				wp_delete_post( $refund->get_id(), true );
			}
			wp_send_json_error( array( 'error' => $ex->getMessage() ) );
		}
	}

		 /**
		  * Wallet partial payment refund.
		  */
	public function wps_wallet_refund_partial_payment() {

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}
		$order_id = absint( filter_input( INPUT_POST, 'order_id' ) );
		$order = wc_get_order( $order_id );
		$userid         = $order->get_user_id();
		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$wallet_id      = get_option( 'wps_wsfw_rechargeable_product_id', '' );
		$walletamount   = get_user_meta( $userid, 'wps_wallet', true );
		$walletamount   = empty( $walletamount ) ? 0 : $walletamount;

		$response = array( 'success' => false );

		$partial_payment_amount = get_order_partial_payment_amount( $order_id );
		$user                   = get_user_by( 'id', $userid );
		$name                   = $user->first_name . ' ' . $user->last_name;
		$order_currency = $order->get_currency();

		$send_email_enable      = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );

		$credited_amount = apply_filters( 'wps_wsfw_update_wallet_to_base_price', $partial_payment_amount, $order_currency );
				$walletamount   += $credited_amount;
				update_user_meta( $userid, 'wps_wallet', $walletamount );

		if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
			$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
			$mail_text .= __( 'Wallet Partial Payment credited by ', 'wallet-system-for-woocommerce' ) . wc_price( $partial_payment_amount, array( 'currency' => $order->get_currency() ) ) . __( ' through order refund.', 'wallet-system-for-woocommerce' );
			$to         = $user->user_email;
			$from       = get_option( 'admin_email' );
			$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
			$headers    = 'MIME-Version: 1.0' . "\r\n";
			$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
			$headers   .= 'From: ' . $from . "\r\n" .
			'Reply-To: ' . $to . "\r\n";
			$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );

		}

				$transaction_type = esc_html__( 'Wallet credited through order refund ', 'wallet-system-for-woocommerce' ) . '<a href="' . admin_url( 'post.php?post=' . $order_id . '&action=edit' ) . '" >#' . $order_id . '</a>';
				$transaction_data = array(
					'user_id'          => $userid,
					'amount'           => $partial_payment_amount,
					'currency'         => $order->get_currency(),
					'payment_method'   => esc_html__( 'Manually by admin through refund', 'wallet-system-for-woocommerce' ),
					'transaction_type' => htmlentities( $transaction_type ),
					'order_id'         => $order_id,
					'note'             => '',
				);
				$transaction_id = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

				add_action( 'wps_wallet_partial_order_refunded', $order_id, $transaction_id );
				if ( $transaction_id ) {
					$response = array( 'success' => true );
					// order refund data added to order notes.
					$text_order_note = wc_price( $partial_payment_amount, wps_wallet_wc_price_args( $order->get_customer_id() ) ) . esc_html__( 'refunded to customer wallet', 'wallet-system-for-woocommerce' );
					$order->add_order_note( $text_order_note );
					update_post_meta( $order_id, '_wps_wallet_partial_payment_refunded', true );
					update_post_meta( $order_id, '_partial_payment_refund_id', $transaction_id );
					add_action( 'wps_wallet_partial_order_refunded', $order_id, $transaction_id );
				}

				wp_send_json( $response );
	}

}
