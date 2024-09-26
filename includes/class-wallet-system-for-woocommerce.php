<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wallet_System_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wallet_System_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $wsfw_onboard    To initializsed the object of class onboard.
	 */
	protected $wsfw_onboard;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area,
	 * the public-facing side of the site and common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'WALLET_SYSTEM_FOR_WOOCOMMERCE_VERSION' ) ) {

			$this->version = WALLET_SYSTEM_FOR_WOOCOMMERCE_VERSION;
		} else {

			$this->version = '2.5.17';
		}

		$this->plugin_name = 'wallet-system-for-woocommerce';

		$this->wallet_system_for_woocommerce_dependencies();
		$this->wallet_system_for_woocommerce_locale();
		if ( is_admin() ) {
			$this->wallet_system_for_woocommerce_admin_hooks();
		} else {
			$this->wallet_system_for_woocommerce_public_hooks();
		}
		$this->wallet_system_for_woocommerce_common_hooks();

		$this->wallet_system_for_woocommerce_api_hooks();

		// custom function for ajax.
		$this->wallet_system_for_woocommerce_ajax_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wallet_System_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Wallet_System_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Wallet_System_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Wallet_System_For_Woocommerce_Common. Defines all hooks for the common area.
	 * - Wallet_System_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wallet_system_for_woocommerce_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-wallet-system-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-wallet-system-for-woocommerce-i18n.php';

		if ( is_admin() ) {

			// The class responsible for defining all actions that occur in the admin area.
			require_once plugin_dir_path( __DIR__ ) . 'admin/class-wallet-system-for-woocommerce-admin.php';

			// The class responsible for on-boarding steps for plugin.
			if ( is_dir( plugin_dir_path( __DIR__ ) . 'onboarding' ) && ! class_exists( 'Wallet_System_For_Woocommerce_Onboarding_Steps' ) ) {
				require_once plugin_dir_path( __DIR__ ) . 'includes/class-wallet-system-for-woocommerce-onboarding-steps.php';
			}

			if ( class_exists( 'Wallet_System_For_Woocommerce_Onboarding_Steps' ) ) {
				$wsfw_onboard_steps = new Wallet_System_For_Woocommerce_Onboarding_Steps();
			}
		} else {

			// The class responsible for defining all actions that occur in the public-facing side of the site.
			require_once plugin_dir_path( __DIR__ ) . 'public/class-wallet-system-for-woocommerce-public.php';

		}

		require_once plugin_dir_path( __DIR__ ) . 'includes/class-wallet-system-for-woocommerce-dependency.php';

		/**
		 * The class responsible for handling ajax requests.
		 */

		require_once plugin_dir_path( __DIR__ ) . 'includes/class-wallet-system-ajaxhandler.php';
		require_once plugin_dir_path( __DIR__ ) . 'package/rest-api/class-wallet-system-for-woocommerce-rest-api.php';

		/**
		 * This class responsible for defining common functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'common/class-wallet-system-for-woocommerce-common.php';

		$enable = get_option( 'wps_wsfw_enable', '' );
		if ( isset( $enable ) && 'on' === $enable ) {

			/**
			 * The class responsible for creating the wallet payment method.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'public/class-wallet-credit-payment-gateway.php';

			if ( class_exists( 'WCMp' ) ) {
				require_once plugin_dir_path( __DIR__ ) . 'marketplace/multivendor-wcmarketplace/class-wcmp-gateway-wps-wallet.php';
				require_once plugin_dir_path( __DIR__ ) . 'marketplace/multivendor-wcmarketplace/class-wallet-system-for-woocommerce-wcmp.php';
			}
		}

		$this->loader = new Wallet_System_For_Woocommerce_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wallet_System_For_Woocommerce_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wallet_system_for_woocommerce_locale() {

		$plugin_i18n = new Wallet_System_For_Woocommerce_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wallet_system_for_woocommerce_admin_hooks() {

		$wsfw_plugin_admin = new Wallet_System_For_Woocommerce_Admin( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $wsfw_plugin_admin, 'wsfw_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $wsfw_plugin_admin, 'wsfw_admin_enqueue_scripts' );

		// Add settings menu for Wallet System for WooCommerce.
		$this->loader->add_action( 'admin_menu', $wsfw_plugin_admin, 'wsfw_options_page' );
		$this->loader->add_action( 'admin_menu', $wsfw_plugin_admin, 'wps_wsfw_remove_default_submenu', 50 );

		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'wps_add_plugins_menus_array', $wsfw_plugin_admin, 'wsfw_admin_submenu_page', 15 );
		$this->loader->add_filter( 'wsfw_wallet_action_settings_registration_array', $wsfw_plugin_admin, 'wsfw_admin_wallet_action_registration_settings_page', 10 );
		$this->loader->add_filter( 'wsfw_wallet_action_settings_daily_visit_array', $wsfw_plugin_admin, 'wsfw_admin_wallet_action_daily_visit_settings_page', 10 );
		$this->loader->add_filter( 'wsfw_wallet_action_settings_payment_gateway_charge_array', $wsfw_plugin_admin, 'wsfw_admin_wallet_payment_gateway_charge_page', 10 );

		$this->loader->add_action( 'wsfw_wallet_action_settings_comment_array', $wsfw_plugin_admin, 'wsfw_admin_wallet_action_settings_comment_array', 10 );
		$this->loader->add_filter( 'wsfw_wallet_action_settings_auto_topup_array', $wsfw_plugin_admin, 'wsfw_admin_wallet_action_auto_topup_settings_page', 10 );
		$this->loader->add_filter( 'wsfw_wallet_action_settings_submit_button_array', $wsfw_plugin_admin, 'wsfw_wallet_action_settings_submit_button_setting_page', 10 );

		$this->loader->add_filter( 'wsfw_general_settings_array', $wsfw_plugin_admin, 'wsfw_admin_general_settings_page', 10 );
		$this->loader->add_filter( 'wsfw_cashback_settings_array', $wsfw_plugin_admin, 'wsfw_admin_cashback_settings_page', 10 );

		$this->loader->add_filter( 'wsfw_update_wallet_array', $wsfw_plugin_admin, 'wsfw_admin_update_wallet_page', 10 );
		// for importing wallet.
		$this->loader->add_filter( 'wsfw_import_wallet_array', $wsfw_plugin_admin, 'wsfw_admin_import_wallets_page', 10 );
		// wallet withdrawal settings for user.
		$this->loader->add_filter( 'wsfw_wallet_withdrawal_array', $wsfw_plugin_admin, 'wsfw_admin_withdrawal_setting_page', 10 );
		$this->loader->add_action( 'wps_wsfw_before_common_settings_form', $wsfw_plugin_admin, 'wsfw_admin_save_tab_settings' );

		$enable = get_option( 'wps_wsfw_enable', '' );
		if ( isset( $enable ) && 'on' === $enable ) {
			$this->loader->add_filter( 'manage_users_columns', $wsfw_plugin_admin, 'wsfw_add_wallet_col_to_user_table' );
			$this->loader->add_filter( 'manage_users_custom_column', $wsfw_plugin_admin, 'wsfw_add_user_wallet_col_data', 10, 3 );
			// add custom columns to Wallet Withdrawal post type.
			$this->loader->add_filter( 'manage_wallet_withdrawal_posts_columns', $wsfw_plugin_admin, 'wsfw_add_columns_to_withdrawal' );
			$this->loader->add_action( 'manage_wallet_withdrawal_posts_custom_column', $wsfw_plugin_admin, 'wsfw_show_withdrawal_columns_data', 10, 2 );
			// enable wallet withdrawal for user on status approved(publish).
			$this->loader->add_action( 'admin_footer-post.php', $wsfw_plugin_admin, 'wsfw_append_wallet_status_list' );

			$this->loader->add_action( 'show_user_profile', $wsfw_plugin_admin, 'wsfw_add_user_wallet_field', 10, 1 );
			$this->loader->add_action( 'edit_user_profile', $wsfw_plugin_admin, 'wsfw_add_user_wallet_field', 10, 1 );
			$this->loader->add_action( 'personal_options_update', $wsfw_plugin_admin, 'wsfw_save_user_wallet_field', 10, 1 );
			$this->loader->add_action( 'edit_user_profile_update', $wsfw_plugin_admin, 'wsfw_save_user_wallet_field', 10, 1 );

			$this->loader->add_action( 'admin_head', $wsfw_plugin_admin, 'custom_code_in_head' );
			$this->loader->add_action( 'woocommerce_email_customer_details', $wsfw_plugin_admin, 'wps_wsfw_remove_customer_details_in_emails', 5, 1 );
			$this->loader->add_action( 'wsfw_general_settings_before', $wsfw_plugin_admin, 'wsfw_general_settings_before_action' );
		}

		$this->loader->add_action( 'init', $wsfw_plugin_admin, 'register_withdrawal_post_type', 20 );
		$this->loader->add_action( 'init', $wsfw_plugin_admin, 'register_wallet_recharge_post_type', 30 );

		$this->loader->add_action( 'wp_ajax_export_users_wallet', $wsfw_plugin_admin, 'export_users_wallet' );

		 $this->loader->add_action( 'woocommerce_order_status_changed', $wsfw_plugin_admin, 'wsfw_order_status_changed_admin', 30, 3 );
		$this->loader->add_action( 'wp_ajax_change_wallet_withdrawan_status', $wsfw_plugin_admin, 'change_wallet_withdrawan_status' );
		$this->loader->add_action( 'wp_ajax_restrict_user_from_wallet_access', $wsfw_plugin_admin, 'restrict_user_from_wallet_access' );

		$this->loader->add_action( 'wp_ajax_wps_wallet_order_refund_action', $wsfw_plugin_admin, 'wps_wallet_order_refund_action' );
		$this->loader->add_action( 'wp_ajax_wps_wallet_refund_partial_payment', $wsfw_plugin_admin, 'wps_wallet_refund_partial_payment' );
		$this->loader->add_action( 'wp_ajax_wps_wallet_delete_user_tranasactions', $wsfw_plugin_admin, 'wps_wallet_delete_user_tranasactions' );

		$this->loader->add_action( 'woocommerce_after_order_fee_item_name', $wsfw_plugin_admin, 'woocommerce_after_order_fee_item_name_callback', 10, 2 );
		// Adding Upsell Orders column in Orders table in backend.
		$this->loader->add_filter( 'manage_edit-shop_order_columns', $wsfw_plugin_admin, 'wps_wsfw_wallet_add_columns_to_admin_orders', 11 );
		// Populating Upsell Orders column with Single Order or Upsell order.
		$this->loader->add_action( 'manage_shop_order_posts_custom_column', $wsfw_plugin_admin, 'wps_wocuf_pro_populate_wallet_order_column', 10, 2 );

		$this->loader->add_action( 'woocommerce_shop_order_list_table_custom_column', $wsfw_plugin_admin, 'wps_wocuf_pro_populate_wallet_order_column', 10, 2 );
		$this->loader->add_filter( 'woocommerce_shop_order_list_table_columns', $wsfw_plugin_admin, 'wps_wsfw_wallet_add_columns_to_admin_orders', 99 );
		$this->loader->add_action( 'wp_ajax_wps_wsfw_filter_chart_data', $wsfw_plugin_admin, 'wps_wsfw_filter_chart_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_wsfw_filter_chart_data', $wsfw_plugin_admin, 'wps_wsfw_filter_chart_data' );

		// download Pdf.
		$this->loader->add_action( 'init', $wsfw_plugin_admin, 'wps_wsfw_download_pdf_file_callback' );

		if ( function_exists( 'wps_sfw_check_plugin_enable' ) ) {
			if ( wps_sfw_check_plugin_enable() ) {
				$this->loader->add_filter( 'wsfw_general_extra_settings_array', $wsfw_plugin_admin, 'wps_wsfw_extra_settings_sfw', 30, 1 );
				$this->loader->add_action( 'wps_sfw_renewal_order_creation', $wsfw_plugin_admin, 'wps_sfw_renewal_order_creation', 10, 2 );
			}
		}

		$is_pro = false;
		$is_pro = apply_filters( 'wsfw_check_pro_plugin', $is_pro );
		if ( ! $is_pro ) {
			$this->loader->add_filter( 'wsfwp_wallet_action_settings_withdrawal_array', $wsfw_plugin_admin, 'wps_wsfws_admin_wallet_action_withdrawal_settings_page_org', 10 );
			$this->loader->add_filter( 'wsfwp_wallet_action_settings_transfer_array', $wsfw_plugin_admin, 'wps_wsfws_admin_wallet_action_transfer_settings_page_org', 10 );
			$this->loader->add_action( 'wsfw_wallet_action_settings_refer_friend_array', $wsfw_plugin_admin, 'wsfw_admin_wallet_action_settings_refer_friend_array_org', 10 );
			$this->loader->add_action( 'wsfw_wallet_action_different_layout_settings_array', $wsfw_plugin_admin, 'wsfw_admin_wallet_different_layout_settings_array_org', 10 );
			$this->loader->add_filter( 'wsfw_wallet_restriction_withdrawal_array_org', $wsfw_plugin_admin, 'wps_wsfw_admin_wallet_withdrawal_restriction_settings_page_org', 10 );
			$this->loader->add_filter( 'wsfw_wallet_restriction_transfer_array_org', $wsfw_plugin_admin, 'wps_wsfw_admin_wallet_transfer_restriction_settings_page_org', 10 );
			$this->loader->add_filter( 'wsfw_wallet_restriction_recharge_array_org', $wsfw_plugin_admin, 'wps_wsfw_admin_wallet_recharge_restriction_settings_page_org', 10 );
			$this->loader->add_action( 'wsfw_wallet_action_recharge_enable_settings_org', $wsfw_plugin_admin, 'wsfw_wallet_action_recharge_enable_settings_tab_org', 10 );
			$this->loader->add_action( 'wsfw_wallet_action_promotions_enable_settings_org', $wsfw_plugin_admin, 'wsfw_wallet_action_promotion_enable_settings_tab_org', 10 );

			$this->loader->add_filter( 'wsfw_wallet_action_withdrawal_settings', $wsfw_plugin_admin, 'wsfw_wallet_withdrawal_enable_settings_tab', 10 );

		}
		$this->loader->add_action( 'woocommerce_new_order', $wsfw_plugin_admin, 'wps_wsfw_wallet_payment_on_order_create' );

		/*cron for notification*/
		$this->loader->add_action( 'admin_init', $wsfw_plugin_admin, 'wps_wsfw_set_cron_for_plugin_notification' );
		$this->loader->add_action( 'wps_wgm_check_for_notification_update', $wsfw_plugin_admin, 'wps_wsfw_save_notice_message' );
		$this->loader->add_action( 'wp_ajax_wps_wsfw_dismiss_notice_banner', $wsfw_plugin_admin, 'wps_wsfw_dismiss_notice_banner_callback' );
	}

	/**
	 * Register all of the hooks related to the common functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wallet_system_for_woocommerce_common_hooks() {

		$wsfw_plugin_common = new Wallet_System_For_Woocommerce_Common( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $wsfw_plugin_common, 'wsfw_common_enqueue_scripts' );

		$this->loader->add_filter( 'woocommerce_is_purchasable', $wsfw_plugin_common, 'wps_wsfw_wallet_recharge_product_purchasable', 1, 2 );
		// cashback hook.
		$this->loader->add_action( 'woocommerce_order_status_changed', $wsfw_plugin_common, 'wsfw_cashback_on_complete_order', 10, 3 );
		// comment hook.
		if ( self::is_enbale_usage_tracking() ) {
			$this->loader->add_action( 'wpswings_tracker_send_event', $wsfw_plugin_common, 'wsfw_wpswings_wallet_tracker_send_event' );
		}
		$this->loader->add_action( 'comment_post', $wsfw_plugin_common, 'wps_wsfw_comment_amount_function', 10, 2 );
		$this->loader->add_action( 'transition_comment_status', $wsfw_plugin_common, 'wps_wsfw_give_amount_on_comment', 10, 3 );

		$enable = get_option( 'wps_wsfw_enable', '' );
		if ( isset( $enable ) && 'on' === $enable ) {
			$this->loader->add_filter( 'mvx_vendor_payment_mode', $wsfw_plugin_common, 'wsfw_admin_mvx_list_mxfdxfodules' );
			$this->loader->add_filter( 'mvx_parent_order_to_vendor_order_statuses_to_sync', $wsfw_plugin_common, 'wsfw_mvx_parent_order_to_vendor_order_statuses_to_sync', 10, 1 );
			$this->loader->add_filter( 'woocommerce_order_status_changed', $wsfw_plugin_common, 'wsfw_wsfw_commission_ordeer_status_change', 10, 3 );
		}
		// woocommerce block code for wallet payment.
		$this->loader->add_action( 'woocommerce_blocks_loaded', $wsfw_plugin_common, 'wsp_wsfw_woocommerce_gateway_wallet_woocommerce_block_support' );
		$this->loader->add_filter( 'woocommerce_order_get_tax_totals', $wsfw_plugin_common, 'wps_wsfw_woocommerce_order_get_tax_totals', 99999, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wallet_system_for_woocommerce_public_hooks() {

		$wsfw_plugin_public = new Wallet_System_For_Woocommerce_Public( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$enable = get_option( 'wps_wsfw_enable', '' );
		if ( isset( $enable ) && 'on' === $enable ) {
			$this->loader->add_action( 'wp_enqueue_scripts', $wsfw_plugin_public, 'wsfw_public_enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $wsfw_plugin_public, 'wsfw_public_enqueue_scripts' );
			$this->loader->add_action( 'init', $wsfw_plugin_public, 'wps_wsfw_wallet_register_endpoint' );
			$this->loader->add_action( 'query_vars', $wsfw_plugin_public, 'wps_wsfw_wallet_query_var' );
			$this->loader->add_action( 'woocommerce_account_wps-wallet_endpoint', $wsfw_plugin_public, 'wps_wsfw_display_wallet_endpoint_content', 20 );
			$this->loader->add_action( 'woocommerce_account_menu_items', $wsfw_plugin_public, 'wps_wsfw_add_wallet_item' );
			$this->loader->add_filter( 'woocommerce_available_payment_gateways', $wsfw_plugin_public, 'wps_wsfw_restrict_payment_gateway', 10, 1 );
			$this->loader->add_action( 'woocommerce_review_order_after_order_total', $wsfw_plugin_public, 'checkout_review_order_custom_field' );
			$this->loader->add_action( 'woocommerce_new_order', $wsfw_plugin_public, 'remove_wallet_session', 10, 1 );
			$this->loader->add_action( 'woocommerce_cart_calculate_fees', $wsfw_plugin_public, 'wsfw_add_wallet_discount', 20 );
			$this->loader->add_action( 'template_redirect', $wsfw_plugin_public, 'add_wallet_recharge_to_cart' );
			$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $wsfw_plugin_public, 'show_message_addto_cart', 10, 2 );
			$this->loader->add_action( 'woocommerce_before_calculate_totals', $wsfw_plugin_public, 'wps_update_price_cart', 10, 1 );
			$this->loader->add_action( 'woocommerce_cart_item_removed', $wsfw_plugin_public, 'after_remove_wallet_from_cart', 10, 2 );

			$this->loader->add_filter( 'woocommerce_checkout_fields', $wsfw_plugin_public, 'wps_wsfw_remove_billing_from_checkout' );
			$this->loader->add_action( 'woocommerce_thankyou', $wsfw_plugin_public, 'change_order_type', 20, 1 );
			$this->loader->add_action( 'woocommerce_email_customer_details', $wsfw_plugin_public, 'wps_wsfw_remove_customer_details_in_emails', 5, 1 );
			$this->loader->add_action( 'woocommerce_before_cart_table', $wsfw_plugin_public, 'wsfw_woocommerce_before_cart_total_cashback_message', 10 );
			$this->loader->add_action( 'woocommerce_before_checkout_form', $wsfw_plugin_public, 'wsfw_woocommerce_before_cart_total_cashback_message', 10 );

			$this->loader->add_action( 'woocommerce_blocks_enqueue_cart_block_scripts_after', $wsfw_plugin_public, 'wsfw_woocommerce_before_cart_total_cashback_message', 10 );
			$this->loader->add_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_before', $wsfw_plugin_public, 'wsfw_woocommerce_before_cart_total_cashback_message', 10 );

			// show cashback notice on shop page.
			$this->loader->add_action( 'woocommerce_after_shop_loop_item_title', $wsfw_plugin_public, 'wsfw_display_category_wise_cashback_price_on_shop_page', 15 );
			$this->loader->add_action( 'woocommerce_single_product_summary', $wsfw_plugin_public, 'wsfw_display_category_wise_cashback_price_on_shop_page', 15 );
			// show comment notice.
			$this->loader->add_filter( 'woocommerce_product_review_comment_form_args', $wsfw_plugin_public, 'wps_wsfw_show_comment_notice', 1000, 1 );
			// new user registration notice.
			$this->loader->add_action( 'woocommerce_before_customer_login_form', $wsfw_plugin_public, 'wps_wsfw_show_signup_notice' );
			$this->loader->add_action( 'user_register', $wsfw_plugin_public, 'wps_wsfw_new_customer_registerd', 10, 1 );
			// daily visit balance.
			$this->loader->add_action( 'wp', $wsfw_plugin_public, 'wps_wsfw_daily_visit_balance', 100 );
			$this->loader->add_filter( 'woocommerce_cart_totals_fee_html', $wsfw_plugin_public, 'wsfw_wallet_cart_totals_fee_html', 10, 2 );
			$this->loader->add_filter( 'wps_wsfw_check_parent_order', $wsfw_plugin_public, 'wps_wsfw_check_parent_order_for_subscription_listing', 10, 2 );
			$this->loader->add_filter( 'woocommerce_thankyou_order_id', $wsfw_plugin_public, 'wps_wsfw_woocommerce_thankyou_order_id', 99999 );
			$this->loader->add_action( 'woocommerce_thankyou', $wsfw_plugin_public, 'wps_wsfw_woocommerce_thankyou_page', 99999 );
			$this->loader->add_filter( 'wc_order_types', $wsfw_plugin_public, 'wps_wsfw_wc_order_types_', 20, 2 );
			$this->loader->add_filter( 'wps_wsfw_show_converted_price', $wsfw_plugin_public, 'wps_wsfwp_show_converted_price', 10, 1 );
			$this->loader->add_filter( 'wps_wsfw_convert_to_base_price', $wsfw_plugin_public, 'wps_wsfwp_convert_to_base_price', 10, 1 );
			$this->loader->add_action( 'woocommerce_checkout_order_processed', $wsfw_plugin_public, 'wps_wocuf_initate_upsell_orders', 90 );
			$this->loader->add_action( 'wp_loaded', $wsfw_plugin_public, 'wps_wsfw_referral_link_using_cookie' );
			$this->loader->add_filter( 'mvx_available_payment_gateways', $wsfw_plugin_public, 'wsfw_admin_mvx_list_modules', 10 );
			$this->loader->add_filter( 'woocommerce_product_get_tax_class', $wsfw_plugin_public, 'wsfw_admin_recharge_product_tax_class', 10, 2 );
			$this->loader->add_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_before', $wsfw_plugin_public, 'wsfw_wps_enqueue_script_block_eheckout', 10 );
			$this->loader->add_action( 'woocommerce_store_api_checkout_order_processed', $wsfw_plugin_public, 'wps_wocuf_initate_upsell_orders_api_checkout_org', 90 );
			$this->loader->add_filter( 'woocommerce_available_payment_gateways', $wsfw_plugin_public, 'wps_wsfwp_add_wallet_recharge_message_restriction', 10, 1 );
		}
	}

	/**
	 * Register all of the hooks related to the api functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wallet_system_for_woocommerce_api_hooks() {

		$wsfw_plugin_api = new Wallet_System_For_Woocommerce_Rest_Api( $this->wsfw_get_plugin_name(), $this->wsfw_get_version() );

		$this->loader->add_action( 'rest_api_init', $wsfw_plugin_api, 'wps_wsfw_add_endpoint' );
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function wsfw_run() {
		$this->loader->wsfw_run();
	}

	/**
	 * Check is usage tracking is enable
	 *
	 * @version 1.0.0
	 * @name is_enbale_usage_tracking
	 */
	public static function is_enbale_usage_tracking() {
		$check_is_enable = get_option( 'wsfw_enable_tracking', false );
		return ! empty( $check_is_enable ) ? true : false;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function wsfw_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wallet_System_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function wsfw_get_loader() {
		return $this->loader;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wallet_System_For_Woocommerce_Onboard    Orchestrates the hooks of the plugin.
	 */
	public function wsfw_get_onboard() {
		return $this->wsfw_onboard;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function wsfw_get_version() {
		return $this->version;
	}

	/**
	 * Predefined default wps_wsfw_plug tabs.
	 *
	 * @return  Array       An key=>value pair of Wallet System for WooCommerce tabs.
	 */
	public function wps_wsfw_plug_default_tabs() {

		$wsfw_default_tabs = array();
		$wsfw_default_tabs['wallet-system-for-woocommerce-general'] = array(
			'title' => esc_html__( 'General', 'wallet-system-for-woocommerce' ),
			'name'  => 'wallet-system-for-woocommerce-general',
		);
		$wsfw_default_tabs = apply_filters( 'wps_wsfw_wsfw_plugin_standard_admin_settings_tabs', $wsfw_default_tabs );

		// added tab for importing wallet of users through button.
		$wsfw_default_tabs['class-wallet-user-table'] = array(
			'title' => esc_html__( 'Wallet', 'wallet-system-for-woocommerce' ),
			'name'  => 'class-wallet-user-table',
		);

		$wsfw_default_tabs['class-wallet-transaction-list-table'] = array(
			'title' => esc_html__( 'Wallet Transactions', 'wallet-system-for-woocommerce' ),
			'name'  => 'class-wallet-transaction-list-table',
		);

		// added tab for wallet withdrawal settings.
		$wsfw_default_tabs['wallet-system-withdrawal-setting'] = array(
			'title' => esc_html__( 'Withdrawal Request', 'wallet-system-for-woocommerce' ),
			'name'  => 'wallet-system-withdrawal-setting',
		);
		$wsfw_default_tabs = apply_filters( 'wps_wsfw_plugin_standard_admin_settings_tabs_after_wallet_action', $wsfw_default_tabs );

		// added tab for wallet withdrawal Cashback.
		$wsfw_default_tabs['wallet-system-for-woocommerce-cashback'] = array(
			'title' => esc_html__( 'Wallet Cashback', 'wallet-system-for-woocommerce' ),
			'name'  => 'wallet-system-for-woocommerce-cashback',
		);
		$wsfw_default_tabs = apply_filters( 'wps_wsfw_plugin_standard_admin_settings_tabs_cashback', $wsfw_default_tabs );

		// added tab for wallet withdrawal Actions.
		$wsfw_default_tabs['wallet-system-for-woocommerce-wallet-actions'] = array(
			'title' => esc_html__( 'Wallet Actions', 'wallet-system-for-woocommerce' ),
			'name'  => 'wallet-system-for-woocommerce-wallet-actions',
		);
		$is_pro = false;
		$is_pro = apply_filters( 'wsfw_check_pro_plugin', $is_pro );

		if ( ! $is_pro ) {
			$wsfw_default_tabs['wallet-system-for-woocommerce-org-wallet-withdrawal-settings'] = array(
				'title'     => esc_html__( 'Withdrawal Settings', 'wallet-system-for-woocommerce' ),
				'name'      => 'wallet-system-for-woocommerce-org-wallet-withdrawal-settings',
			);
			$wsfw_default_tabs['wallet-system-for-woocommerce-org-wallet-restriction'] = array(
				'title'     => esc_html__( 'Wallet Regulation', 'wallet-system-for-woocommerce' ),
				'name'      => 'wallet-system-for-woocommerce-org-wallet-restriction',
			);
			$wsfw_default_tabs['wallet-system-for-woocommerce-org-wallet-promotions'] = array(
				'title'     => esc_html__( 'Wallet Promotions', 'wallet-system-for-woocommerce' ),
				'name'      => 'wallet-system-for-woocommerce-org-wallet-promotions',
			);
			$wsfw_default_tabs['wallet-system-for-woocommerce-org-wallet-recharge-tab'] = array(
				'title'     => esc_html__( 'Wallet Quick Recharge', 'wallet-system-for-woocommerce' ),
				'name'      => 'wallet-system-for-woocommerce-org-wallet-recharge-tab',
			);
		}

		$wsfw_default_tabs['wallet-system-rest-api'] = array(
			'title' => esc_html__( 'REST API', 'wallet-system-for-woocommerce' ),
			'name'  => 'wallet-system-rest-api',
		);

		$wsfw_default_tabs['wallet-system-for-woocommerce-system-status'] = array(
			'title' => esc_html__( 'System Status', 'wallet-system-for-woocommerce' ),
			'name'  => 'wallet-system-for-woocommerce-system-status',
		);

		$wsfw_default_tabs['wallet-system-for-woocommerce-overview']      = array(
			'title' => esc_html__( 'Overview', 'wallet-system-for-woocommerce' ),
			'name'  => 'wallet-system-for-woocommerce-overview',
		);

		$wsfw_default_tabs = apply_filters( 'wps_wsfw_plug_extra_tabs', $wsfw_default_tabs );

		return $wsfw_default_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since   1.0.0
	 * @param string $path path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function wps_wsfw_plug_load_template( $path, $params = array() ) {

		$wsfw_file_path = WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . $path;
		$wsfw_file_path = apply_filters( 'wps_wsfw_template_path', $wsfw_file_path );

		if ( file_exists( $wsfw_file_path ) ) {

			include $wsfw_file_path;
		} else {

			/* translators: %s: file path */
			$wsfw_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'wallet-system-for-woocommerce' ), $wsfw_file_path );
			$this->wps_wsfw_plug_admin_notice( $wsfw_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $wsfw_message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0
	 */
	public static function wps_wsfw_plug_admin_notice( $wsfw_message, $type = 'error' ) {

		$wsfw_classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$wsfw_classes .= 'updated is-dismissible';
				break;

			case 'update-nag':
				$wsfw_classes .= 'update-nag is-dismissible';
				break;

			case 'success':
				$wsfw_classes .= 'notice-success is-dismissible';
				break;

			default:
				$wsfw_classes .= 'notice-error is-dismissible';
		}

		$wsfw_notice  = '<div class="' . esc_attr( $wsfw_classes ) . ' wps-errorr-8">';
		$wsfw_notice .= '<p>' . esc_html( $wsfw_message ) . '</p>';
		$wsfw_notice .= '</div>';

		echo wp_kses_post( $wsfw_notice );
	}


	/**
	 * Show WordPress and server info.
	 *
	 * @return  Array $wsfw_system_data       returns array of all WordPress and server related information.
	 * @since  1.0.0
	 */
	public function wps_wsfw_plug_system_status() {
		global $wpdb;
		$wsfw_system_status    = array();
		$wsfw_wordpress_status = array();
		$wsfw_system_data      = array();

		// Get the web server.
		$wsfw_system_status['web_server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		// Get PHP version.
		$wsfw_system_status['php_version'] = function_exists( 'phpversion' ) ? phpversion() : __( 'N/A (phpversion function does not exist)', 'wallet-system-for-woocommerce' );

		// Get the server's IP address.
		$wsfw_system_status['server_ip'] = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';

		// Get the server's port.
		$wsfw_system_status['server_port'] = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';

		// Get the uptime.
		$wsfw_system_status['uptime'] = function_exists( 'exec' ) ? @exec( 'uptime -p' ) : __( 'N/A (make sure exec function is enabled)', 'wallet-system-for-woocommerce' );

		// Get the server path.
		$wsfw_system_status['server_path'] = defined( 'ABSPATH' ) ? ABSPATH : __( 'N/A (ABSPATH constant not defined)', 'wallet-system-for-woocommerce' );

		// Get the OS.
		$wsfw_system_status['os'] = function_exists( 'php_uname' ) ? php_uname( 's' ) : __( 'N/A (php_uname function does not exist)', 'wallet-system-for-woocommerce' );

		// Get WordPress version.
		$wsfw_wordpress_status['wp_version'] = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( 'N/A (get_bloginfo function does not exist)', 'wallet-system-for-woocommerce' );

		// Get and count active WordPress plugins.
		$wsfw_wordpress_status['wp_active_plugins'] = function_exists( 'get_option' ) ? count( get_option( 'active_plugins' ) ) : __( 'N/A (get_option function does not exist)', 'wallet-system-for-woocommerce' );

		// See if this site is multisite or not.
		$wsfw_wordpress_status['wp_multisite'] = function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'wallet-system-for-woocommerce' ) : __( 'No', 'wallet-system-for-woocommerce' );

		// See if WP Debug is enabled.
		$wsfw_wordpress_status['wp_debug_enabled'] = defined( 'WP_DEBUG' ) ? __( 'Yes', 'wallet-system-for-woocommerce' ) : __( 'No', 'wallet-system-for-woocommerce' );

		// See if WP Cache is enabled.
		$wsfw_wordpress_status['wp_cache_enabled'] = defined( 'WP_CACHE' ) ? __( 'Yes', 'wallet-system-for-woocommerce' ) : __( 'No', 'wallet-system-for-woocommerce' );

		// Get the total number of WordPress users on the site.
		$wsfw_wordpress_status['wp_users'] = function_exists( 'count_users' ) ? count_users() : __( 'N/A (count_users function does not exist)', 'wallet-system-for-woocommerce' );

		// Get the number of published WordPress posts.
		$wsfw_wordpress_status['wp_posts'] = wp_count_posts()->publish >= 1 ? wp_count_posts()->publish : __( '0', 'wallet-system-for-woocommerce' );

		// Get PHP memory limit.
		$wsfw_system_status['php_memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'wallet-system-for-woocommerce' );

		// Get the PHP error log path.
		$wsfw_system_status['php_error_log_path'] = ! ini_get( 'error_log' ) ? __( 'N/A', 'wallet-system-for-woocommerce' ) : ini_get( 'error_log' );

		// Get PHP max upload size.
		$wsfw_system_status['php_max_upload'] = function_exists( 'ini_get' ) ? (int) ini_get( 'upload_max_filesize' ) : __( 'N/A (ini_get function does not exist)', 'wallet-system-for-woocommerce' );

		// Get PHP max post size.
		$wsfw_system_status['php_max_post'] = function_exists( 'ini_get' ) ? (int) ini_get( 'post_max_size' ) : __( 'N/A (ini_get function does not exist)', 'wallet-system-for-woocommerce' );

		// Get the PHP architecture.
		if ( PHP_INT_SIZE == 4 ) {
			$wsfw_system_status['php_architecture'] = '32-bit';
		} elseif ( PHP_INT_SIZE == 8 ) {
			$wsfw_system_status['php_architecture'] = '64-bit';
		} else {
			$wsfw_system_status['php_architecture'] = 'N/A';
		}

		// Get server host name.
		$wsfw_system_status['server_hostname'] = function_exists( 'gethostname' ) ? gethostname() : __( 'N/A (gethostname function does not exist)', 'wallet-system-for-woocommerce' );

		// Show the number of processes currently running on the server.
		$wsfw_system_status['processes'] = function_exists( 'exec' ) ? @exec( 'ps aux | wc -l' ) : __( 'N/A (make sure exec is enabled)', 'wallet-system-for-woocommerce' );

		// Get the memory usage.
		$wsfw_system_status['memory_usage'] = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) : 0;

		// Get CPU usage.
		// Check to see if system is Windows, if so then use an alternative since sys_getloadavg() won't work.
		if ( stristr( PHP_OS, 'win' ) ) {
			$wsfw_system_status['is_windows']        = true;
			$wsfw_system_status['windows_cpu_usage'] = function_exists( 'exec' ) ? @exec( 'wmic cpu get loadpercentage /all' ) : __( 'N/A (make sure exec is enabled)', 'wallet-system-for-woocommerce' );
		}

		// Get the memory limit.
		$wsfw_system_status['memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'wallet-system-for-woocommerce' );

		// Get the PHP maximum execution time.
		$wsfw_system_status['php_max_execution_time'] = function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'N/A (ini_get function does not exist)', 'wallet-system-for-woocommerce' );

		// Get outgoing IP address, file_get_contents is used to get IP address.
		$api_url                           = 'http://ipecho.net/plain';
		$api_response                      = wp_remote_get( $api_url );
		$response_body                     = wp_remote_retrieve_body( $api_response );
		$wsfw_system_status['outgoing_ip'] = $response_body;

		$wsfw_system_data['php'] = $wsfw_system_status;
		$wsfw_system_data['wp']  = $wsfw_wordpress_status;

		return $wsfw_system_data;
	}

	/**
	 * Generate html components.
	 *
	 * @param  string $wsfw_components    html to display.
	 * @since  1.0.0
	 */
	public function wps_wsfw_plug_generate_html( $wsfw_components = array() ) {
		$subscription_duration = array(
			'day' => __( 'Days', 'wallet-system-for-woocommerce' ),
			'week' => __( 'Weeks', 'wallet-system-for-woocommerce' ),
		);
		$subscription_duration = apply_filters( 'wsfw_subscription_type__array', $subscription_duration );
		if ( is_array( $wsfw_components ) && ! empty( $wsfw_components ) ) {
			foreach ( $wsfw_components as $wsfw_component ) {
				$pro_group_tag = '';
				$is_pro = false;
				$is_pro = apply_filters( 'wsfw_check_pro_plugin', $is_pro );
				if ( ! $is_pro ) {

					if ( preg_match( "/\wps_pro_settings\b/", $wsfw_component['class'] ) ) :
						$pro_group_tag = 'wps_pro_settings_tag';
					endif;
				}

				if ( ! empty( $wsfw_component['type'] ) && ! empty( $wsfw_component['id'] ) ) {
					switch ( $wsfw_component['type'] ) {

						case 'hidden':
						case 'number':
						case 'email':
						case 'text':
							?>
														
						
						<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?> wps-wsfw-<?php echo esc_attr( $wsfw_component['type'] ); ?> ">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<?php if ( 'number' != $wsfw_component['type'] ) { ?>
												<!-- dynamic inline style will be added -->
												<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?></span>
											<?php } ?>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input
									class="mdc-text-field__input <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" 
									name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
									
									<?php

									if ( 'number' == $wsfw_component['type'] ) {

										if ( ! empty( $wsfw_component['min'] ) || 0 == $wsfw_component['min'] ) {

											?>
										min="<?php echo esc_attr( $wsfw_component['min'] ); ?>"
											<?php
										}
										if ( ! empty( $wsfw_component['max'] ) ) {
											?>
										max="<?php echo esc_attr( $wsfw_component['max'] ); ?>"
											<?php
										}
										if ( ! empty( $wsfw_component['step'] ) ) {
											?>
											step="<?php echo esc_attr( $wsfw_component['step'] ); ?>"
												<?php
										}
										?>
										<?php
									}
									?>
									type="<?php echo esc_attr( $wsfw_component['type'] ); ?>"
									value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?>"
									>
								</label><br>
								<div class="mdc-text-field-helper-line">
											<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'password':
							?>
						<div class="wps-form-group <?php esc_attr( $pro_group_tag ); ?>">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?> wps-form__password" 
									name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
									type="<?php echo esc_attr( $wsfw_component['type'] ); ?>"
									value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?>"
									>
									<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing wps-password-hidden" tabindex="0" role="button">visibility</i>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'textarea':
							?>
						<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
							<div class="wps-form-group__label">
								<label class="wps-form-label" for="<?php echo esc_attr( $wsfw_component['id'] ); ?>"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="wps-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"  	for="text-field-hero-input">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<span class="mdc-floating-label"><?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?></span>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<span class="mdc-text-field__resizer">
										<textarea class="mdc-text-field__input <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" rows="2" cols="25" aria-label="Label" name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>" id="<?php echo esc_attr( $wsfw_component['id'] ); ?>" placeholder="<?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?>"><?php echo ( isset( $wsfw_component['value'] ) ? esc_textarea( $wsfw_component['value'] ) : '' ); // WPCS: XSS ok. ?></textarea>
									</span>
								</label>

							</div>
						</div>

							<?php
							break;

						case 'select':
						case 'multiselect':
							?>
								<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
									<div class="wps-form-group__label">
										<label class="wps-form-label" for="<?php echo esc_attr( $wsfw_component['id'] ); ?>"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
									</div>
									<div class="wps-form-group__control">
										<div class="wps-form-select">
											<select id="<?php echo esc_attr( $wsfw_component['id'] ); ?>" name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : '' ); ?><?php echo ( 'multiselect' === $wsfw_component['type'] ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="mdl-textfield__input <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" <?php echo 'multiselect' === $wsfw_component['type'] ? 'multiple="multiple"' : ''; ?> >
										<?php
										foreach ( $wsfw_component['options'] as $wsfw_key => $wsfw_val ) {
											?>
													<option value="<?php echo esc_attr( $wsfw_key ); ?>"
												<?php
												if ( is_array( $wsfw_component['value'] ) ) {
													selected( in_array( (string) $wsfw_key, $wsfw_component['value'], true ), true );
												} else {
													selected( $wsfw_component['value'], (string) $wsfw_key );
												}
												?>
														>
												<?php echo esc_html( $wsfw_val ); ?>
													</option>
													<?php
										}
										?>
											</select>
										</div>
										<div class="mdc-text-field-helper-line">
											<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? wp_kses_post( $wsfw_component['description'] ) : '' ); ?></div>
										</div>
									</div>
								</div>
		
									<?php
							break;

						case 'checkbox':
							?>
						<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="wps-form-group__control wps-pl-4">
								<div class="mdc-form-field">
									<div class="mdc-checkbox">
										<input 
										name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
										type="checkbox"
										class="mdc-checkbox__native-control <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>"
										value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
										data-value="<?php echo esc_attr( $wsfw_component['data-value'] ); ?>"
										<?php checked( $wsfw_component['data-value'], '1' ); ?>
										/>
										<div class="mdc-checkbox__background">
											<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
												<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
											</svg>
											<div class="mdc-checkbox__mixedmark"></div>
										</div>
										<div class="mdc-checkbox__ripple"></div>
									</div>
									<label for="checkbox-1"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio':
							?>
						<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
							<div class="wps-form-group__label">
								<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="wps-form-group__control wps-pl-4">
								<div class="wps-flex-col">
									<?php
									foreach ( $wsfw_component['options'] as $wsfw_radio_key => $wsfw_radio_val ) {
										?>
										<div class="mdc-form-field">
											<div class="mdc-radio">
												<input
												name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
												value="<?php echo esc_attr( $wsfw_radio_key ); ?>"
												type="radio"
												class="mdc-radio__native-control <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>"
												<?php checked( $wsfw_radio_key, $wsfw_component['value'] ); ?>
												>
												<div class="mdc-radio__background">
													<div class="mdc-radio__outer-circle"></div>
													<div class="mdc-radio__inner-circle"></div>
												</div>
												<div class="mdc-radio__ripple"></div>
											</div>
											<label for="radio-1"><?php echo esc_html( $wsfw_radio_val ); ?></label>
										</div>	
										<?php
									}
									?>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'radio-switch':
							?>

						<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
							<div class="wps-form-group__label">
								<label for="" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="wps-form-group__control">
								<div>
									<div class="mdc-switch">
										<div class="mdc-switch__track"></div>
										<div class="mdc-switch__thumb-underlay">
											<div class="mdc-switch__thumb"></div>
											<input name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>" type="checkbox" id="<?php echo esc_html( $wsfw_component['id'] ); ?>" value="on" class="mdc-switch__native-control <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" role="switch" aria-checked="
												<?php
												if ( 'on' == $wsfw_component['value'] ) {
													echo 'true';
												} else {
													echo 'false';
												}
												?>
											"
											<?php checked( get_option( $wsfw_component['name'], '' ), 'on' ); ?>
											>
										</div>
									</div>
								</div>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
							<?php
							break;

						case 'button':
							?>
						<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
							<div class="wps-form-group__label"></div>
							<div class="wps-form-group__control">
								<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
									<span class="mdc-button__label <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>"><?php echo ( isset( $wsfw_component['button_text'] ) ? esc_html( $wsfw_component['button_text'] ) : '' ); ?></span>
								</button>
							</div>
						</div>

							<?php
							break;
						case 'subscription_select1':
							?>
							<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
								<div class="wps-form-group__label">
									<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								</div>
								<div class="wps-form-group__control wps-pl-4">
									<div class="mdc-form-field wps_subscription_mdc_form">
									

									<p class="form-field wps_sfw_subscription_number_field ">
										
										<label class="mdc-text-field mdc-text-field--outlined">
											<span class="mdc-notched-outline mdc-notched-outline--no-label">
											<span class="mdc-notched-outline__leading"></span>
											<span class="mdc-notched-outline__notch">
																					</span>
											<span class="mdc-notched-outline__trailing"></span>
										</span>
									<input class="mdc-text-field__input wws-text-class" name="wps_wsfw_subscriptions_per_interval" id="wps_wsfw_subscriptions_per_interval" min=0 step="0.01" type="number" value="<?php echo ! empty( get_option( 'wps_wsfw_subscriptions_per_interval' ) ) ? esc_attr( get_option( 'wps_wsfw_subscriptions_per_interval' ) ) : 1; ?>" placeholder="<?php esc_html_e( 'Enter Subscriptions Per Interval', 'wallet-system-for-woocommerce' ); ?>">
										</label>
										<select id="wps_sfw_subscription_interval" name="wps_sfw_subscription_interval" class="mdl-textfield__input wsfw-select-class" value="<?php echo esc_attr( get_option( 'wps_sfw_subscription_interval', 'day' ) ); ?>">
									<?php
									foreach ( $subscription_duration as $x => $x_value ) {

										echo '<option ' . ( get_option( 'wps_sfw_subscription_expiry_interval', 'day' ) == $x ? 'selected="selected"' : '' ) . ' value="' . esc_attr( $x ) . '">' . esc_attr( $x_value ) . '</option>';
									}
									?>
										</select>
										<span class="woocommerce-help-tip"></span>		</p>
										<div for="checkbox-1" class="wps_description_div"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
								
									</div>
								</div>
							</div>
								<?php
							break;
						case 'subscription_select2':
							?>
								<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
									<div class="wps-form-group__label">
										<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
									</div>
									<div class="wps-form-group__control wps-pl-4">
										<div class="mdc-form-field wps_subscription_mdc_form">
	
										<p class="form-field wps_sfw_subscription_number_field ">
											
											<label class="mdc-text-field mdc-text-field--outlined">
												<span class="mdc-notched-outline mdc-notched-outline--no-label">
												<span class="mdc-notched-outline__leading"></span>
												<span class="mdc-notched-outline__notch">i
																						</span>
												<span class="mdc-notched-outline__trailing"></span>
											</span>
										<input class="mdc-text-field__input wws-text-class" min=0 name="wps_wsfw_subscriptions_expiry_per_interval" id="wps_wsfw_subscriptions_expiry_per_interval" step="0.01" type="number" value="<?php echo ! empty( get_option( 'wps_wsfw_subscriptions_expiry_per_interval' ) ) ? esc_attr( get_option( 'wps_wsfw_subscriptions_expiry_per_interval' ) ) : 1; ?>" placeholder="Enter comment amount">
											</label>
											<select id="wps_sfw_subscription_expiry_interval" disabled="disabled" name="wps_sfw_subscription_expiry_interval" class="mdl-textfield__input wsfw-select-class" value="<?php echo esc_attr( get_option( 'wps_sfw_subscription_expiry_interval', 'day' ) ); ?>">
									<?php
									 $html_option = '';
									foreach ( $subscription_duration as $x => $x_value ) {

										echo '<option ' . ( get_option( 'wps_sfw_subscription_expiry_interval', 'day' ) == $x ? 'selected="selected"' : '' ) . ' value="' . esc_attr( $x ) . '">' . esc_attr( $x_value ) . '</option>';
									}

									?>
											</select>
											<span class="woocommerce-help-tip"></span>		</p>
											<label for="checkbox-1"  class="wps_description_div"> <?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></label>
										</div>
									</div>
								</div>
									<?php
							break;

						case 'multi':
							?>
							<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?> wps-wsfw-<?php echo esc_attr( $wsfw_component['type'] ); ?> ">
								<div class="wps-form-group__label">
									<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
									</div>
									<div class="wps-form-group__control">
									<?php
									foreach ( $wsfw_component['value'] as $component ) {
										?>
											<label class="mdc-text-field mdc-text-field--outlined">
												<span class="mdc-notched-outline">
													<span class="mdc-notched-outline__leading"></span>
													<span class="mdc-notched-outline__notch">
														<?php if ( 'number' != $component['type'] ) { ?>
															<!-- dynamic inline style will be added. -->
															<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?></span>
														<?php } ?>
													</span>
													<span class="mdc-notched-outline__trailing"></span>
												</span>
												<input 
												class="mdc-text-field__input <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" 
												name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
												id="<?php echo esc_attr( $component['id'] ); ?>"
												type="<?php echo esc_attr( $component['type'] ); ?>"
												value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
												placeholder="<?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?>"
												<?php echo esc_attr( ( 'number' === $component['type'] ) ? 'max=10 min=0' : '' ); ?>
												>
											</label>
								<?php } ?>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
								<?php
							break;
						case 'color':
						case 'date':
						case 'file':
							?>
							<div class="wps-form-group wps-wsfw-<?php echo esc_attr( $wsfw_component['type'] ); ?> <?php echo esc_attr( $pro_group_tag ); ?>">
								<div class="wps-form-group__label">
									<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="wps-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								</div>
								<div class="wps-form-group__control">
									<label class="mdc-text-field mdc-text-field--outlined">
										<input 
										class="mdc-text-field__input <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" 
										name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
										type="<?php echo esc_attr( $wsfw_component['type'] ); ?>"
										value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
										<?php
										// phpcs:ignore
										echo esc_html( ( 'date' === $wsfw_component['type'] ) ? 'max=' . gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d', mktime() ) . ' + 365 day' ) ) . 'min=' . gmdate( 'Y-m-d' ) . '' : '' );
										?>
										>
									</label>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
							<?php
							break;

						case 'submit':
							?>
						<tr valign="top">
							<td scope="row">
								<input type="submit" class="wps-btn wps-btn__filled" 
								name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
								id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
								class="<?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>"
								value="<?php echo esc_attr( $wsfw_component['button_text'] ); ?>"
								/>
							</td>
						</tr>
							<?php
							break;

						case 'oneline-radio':
							?>
							<div class="wps-form-group <?php echo esc_attr( $pro_group_tag ); ?>">
								<div class="wps-form-group__label">
									<label class="wps-form-label" for="<?php echo esc_attr( $wsfw_component['id'] ); ?>"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								</div>
								<div class="wps-form-group__control">
									<div class="wps-form-select">
										<?php
										foreach ( $wsfw_component['options'] as $wsfw_radio_key => $wsfw_radio_val ) {
											?>
											<div class="wps-form-select-card">
												<input
												type="radio"
												id="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
												name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
												value="<?php echo esc_attr( $wsfw_radio_key ); ?>"
												<?php checked( get_option( $wsfw_component['name'], '' ), $wsfw_radio_key ); ?> >
												<label for="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"><?php echo esc_attr( $wsfw_radio_val ); ?></label>
											</div>
											<?php
										}
										?>
									</div>
								</div>
							</div>

							<?php
							break;

						case 'import_submit':
							?>
							<div class="wps-form-group">
								<div class="wps-form-group__label"></div>
								<div class="wps-form-group__control">
									<input type="submit" class="wps-btn wps-btn__filled" 
									name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
									class="<?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>"
									value="<?php echo esc_attr( $wsfw_component['button_text'] ); ?>"
									/>
								</div>
							</div>
							<?php
							break;

						default:
							break;
					}
				}
			}
			include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/wallet-system-for-woocommerce-go-pro-data.php';

		}
	}

	/**
	 * Register all of the hooks related to ajax
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function wallet_system_for_woocommerce_ajax_hooks() {

		$wsfw_plugin_ajax = new Wallet_System_AjaxHandler();
	}

	/**
	 * Insert transaction related data in custom table
	 *
	 * @param array $transactiondata contains data for transaction table.
	 * @return string
	 */
	public function insert_transaction_data_in_table( $transactiondata ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wps_wsfw_wallet_transaction';

		// Check if table exists.
		if ( $wpdb->get_var( 'show tables like "' . $wpdb->prefix . 'wps_wsfw_wallet_transaction"' ) != $table_name ) :

			// if not, create the table.
			$sql = 'CREATE TABLE ' . $table_name . ' (
            (...)
            ) ENGINE=InnoDB;';

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		else :

			$insert_array = array(
				'user_id'          => $transactiondata['user_id'],
				'amount'           => apply_filters( 'wps_wsfw_convert_to_base_price', $transactiondata['amount'] ),
				'currency'         => $transactiondata['currency'],
				'transaction_type' => $transactiondata['transaction_type'],
				'payment_method'   => $transactiondata['payment_method'],
				'transaction_id'   => $transactiondata['order_id'],
				'note'             => $transactiondata['note'],
				'date'             => gmdate( 'Y-m-d H:i:s' ),
				'transaction_type_1'   => $transactiondata['transaction_type_1'],
			);

			$results        = $wpdb->insert(
				$table_name,
				$insert_array
			);
			$transaction_id = $wpdb->insert_id;
			if ( $results ) {
				return $transaction_id;
			} else {
				return false;
			}

		endif;
	}

	/**
	 * Send mail to user on wallet update
	 *
	 * @param string $to user email address.
	 * @param string $subject subject for mail.
	 * @param string $mail_message message for mail.
	 * @param string $headers data to be send in header.
	 * @return void
	 */
	public function send_mail_on_wallet_updation( $to, $subject, $mail_message, $headers ) {
		// Here put your Validation and send mail.
		$send_mail_through = get_option( 'wps_wsfw_enable_email_address_value_for_wallet_amount' );
		if ( get_option( 'wps_wsfw_enable_email_address_value_for_wallet_amount', true ) ) {
			$headers = 'From: ' . $send_mail_through . "\r\n" .
			'Reply-To: ' . $send_mail_through . "\r\n";
		}
		$flag = wc_mail( $to, $subject, $mail_message, $headers );
	}
}
