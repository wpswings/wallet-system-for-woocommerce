<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com/
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
 * @author     makewebbetter <webmaster@makewebbetter.com>
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

			$this->version = '1.0.0';
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wallet-system-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wallet-system-for-woocommerce-i18n.php';

		if ( is_admin() ) {

			// The class responsible for defining all actions that occur in the admin area.
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wallet-system-for-woocommerce-admin.php';

			// The class responsible for on-boarding steps for plugin.
			if ( is_dir(  plugin_dir_path( dirname( __FILE__ ) ) . 'onboarding' ) && ! class_exists( 'Wallet_System_For_Woocommerce_Onboarding_Steps' ) ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wallet-system-for-woocommerce-onboarding-steps.php';
			}

			if ( class_exists( 'Wallet_System_For_Woocommerce_Onboarding_Steps' ) ) {
				$wsfw_onboard_steps = new Wallet_System_For_Woocommerce_Onboarding_Steps();
			}
		} else {

			// The class responsible for defining all actions that occur in the public-facing side of the site.
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wallet-system-for-woocommerce-public.php';

		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'package/rest-api/class-wallet-system-for-woocommerce-rest-api.php';

		/**
		 * This class responsible for defining common functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-wallet-system-for-woocommerce-common.php';

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
		$this->loader->add_action( 'admin_menu', $wsfw_plugin_admin, 'mwb_wsfw_remove_default_submenu', 50 );

		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'mwb_add_plugins_menus_array', $wsfw_plugin_admin, 'wsfw_admin_submenu_page', 15 );
		$this->loader->add_filter( 'wsfw_template_settings_array', $wsfw_plugin_admin, 'wsfw_admin_template_settings_page', 10 );
		$this->loader->add_filter( 'wsfw_general_settings_array', $wsfw_plugin_admin, 'wsfw_admin_general_settings_page', 10 );

		// Saving tab settings.
		$this->loader->add_action( 'admin_init', $wsfw_plugin_admin, 'wsfw_admin_save_tab_settings' );

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

		$this->loader->add_action( 'wp_enqueue_scripts', $wsfw_plugin_common, 'wsfw_common_enqueue_styles' );

		$this->loader->add_action( 'wp_enqueue_scripts', $wsfw_plugin_common, 'wsfw_common_enqueue_scripts' );
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

		$this->loader->add_action( 'wp_enqueue_scripts', $wsfw_plugin_public, 'wsfw_public_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $wsfw_plugin_public, 'wsfw_public_enqueue_scripts' );

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

		$this->loader->add_action( 'rest_api_init', $wsfw_plugin_api, 'mwb_wsfw_add_endpoint' );

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
	 * Predefined default mwb_wsfw_plug tabs.
	 *
	 * @return  Array       An key=>value pair of Wallet System for WooCommerce tabs.
	 */
	public function mwb_wsfw_plug_default_tabs() {

		$wsfw_default_tabs = array();

		$wsfw_default_tabs['wallet-system-for-woocommerce-general'] = array(
			'title'       => esc_html__( 'General Setting', 'wallet-system-for-woocommerce' ),
			'name'        => 'wallet-system-for-woocommerce-general',
		);
		$wsfw_default_tabs = apply_filters( 'mwb_wsfw_plugin_standard_admin_settings_tabs', $wsfw_default_tabs );

		$wsfw_default_tabs['wallet-system-for-woocommerce-system-status'] = array(
			'title'       => esc_html__( 'System Status', 'wallet-system-for-woocommerce' ),
			'name'        => 'wallet-system-for-woocommerce-system-status',
		);
		$wsfw_default_tabs['wallet-system-for-woocommerce-template'] = array(
			'title'       => esc_html__( 'Templates', 'wallet-system-for-woocommerce' ),
			'name'        => 'wallet-system-for-woocommerce-template',
		);
		$wsfw_default_tabs['wallet-system-for-woocommerce-overview'] = array(
			'title'       => esc_html__( 'Overview', 'wallet-system-for-woocommerce' ),
			'name'        => 'wallet-system-for-woocommerce-overview',
		);

		return $wsfw_default_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since   1.0.0
	 * @param string $path path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function mwb_wsfw_plug_load_template( $path, $params = array() ) {

		$wsfw_file_path = WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . $path;

		if ( file_exists( $wsfw_file_path ) ) {

			include $wsfw_file_path;
		} else {

			/* translators: %s: file path */
			$wsfw_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'wallet-system-for-woocommerce' ), $wsfw_file_path );
			$this->mwb_wsfw_plug_admin_notice( $wsfw_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $wsfw_message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0
	 */
	public static function mwb_wsfw_plug_admin_notice( $wsfw_message, $type = 'error' ) {

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

		$wsfw_notice  = '<div class="' . esc_attr( $wsfw_classes ) . ' mwb-errorr-8">';
		$wsfw_notice .= '<p>' . esc_html( $wsfw_message ) . '</p>';
		$wsfw_notice .= '</div>';

		echo wp_kses_post( $wsfw_notice );
	}


	/**
	 * Show wordpress and server info.
	 *
	 * @return  Array $wsfw_system_data       returns array of all wordpress and server related information.
	 * @since  1.0.0
	 */
	public function mwb_wsfw_plug_system_status() {
		global $wpdb;
		$wsfw_system_status = array();
		$wsfw_wordpress_status = array();
		$wsfw_system_data = array();

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
			$wsfw_system_status['is_windows'] = true;
			$wsfw_system_status['windows_cpu_usage'] = function_exists( 'exec' ) ? @exec( 'wmic cpu get loadpercentage /all' ) : __( 'N/A (make sure exec is enabled)', 'wallet-system-for-woocommerce' );
		}

		// Get the memory limit.
		$wsfw_system_status['memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'wallet-system-for-woocommerce' );

		// Get the PHP maximum execution time.
		$wsfw_system_status['php_max_execution_time'] = function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'N/A (ini_get function does not exist)', 'wallet-system-for-woocommerce' );

		// Get outgoing IP address.
		$wsfw_system_status['outgoing_ip'] = function_exists( 'file_get_contents' ) ? file_get_contents( 'http://ipecho.net/plain' ) : __( 'N/A (file_get_contents function does not exist)', 'wallet-system-for-woocommerce' );

		$wsfw_system_data['php'] = $wsfw_system_status;
		$wsfw_system_data['wp'] = $wsfw_wordpress_status;

		return $wsfw_system_data;
	}

	/**
	 * Generate html components.
	 *
	 * @param  string $wsfw_components    html to display.
	 * @since  1.0.0
	 */
	public function mwb_wsfw_plug_generate_html( $wsfw_components = array() ) {
		if ( is_array( $wsfw_components ) && ! empty( $wsfw_components ) ) {
			foreach ( $wsfw_components as $wsfw_component ) {
				if ( ! empty( $wsfw_component['type'] ) &&  ! empty( $wsfw_component['id'] ) ) {
					switch ( $wsfw_component['type'] ) {

						case 'hidden':
						case 'number':
						case 'email':
						case 'text':
						?>
						<div class="mwb-form-group mwb-wsfw-<?php echo esc_attr($wsfw_component['type']); ?>">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<?php if ( 'number' != $wsfw_component['type'] ) { ?>
												<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?></span>
											<?php } ?>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input
									class="mdc-text-field__input <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" 
									name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
									type="<?php echo esc_attr( $wsfw_component['type'] ); ?>"
									value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?>"
									>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
						<?php
						break;

						case 'password':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?> mwb-form__password" 
									name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
									type="<?php echo esc_attr( $wsfw_component['type'] ); ?>"
									value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $wsfw_component['placeholder'] ) ? esc_attr( $wsfw_component['placeholder'] ) : '' ); ?>"
									>
									<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing mwb-password-hidden" tabindex="0" role="button">visibility</i>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
						<?php
						break;

						case 'textarea':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $wsfw_component['id'] ); ?>"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
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
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $wsfw_component['id'] ); ?>"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div class="mwb-form-select">
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
									<label class="mdl-textfield__label" for="octane"><?php echo esc_html( $wsfw_component['description'] ); ?><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>

						<?php
						break;

						case 'checkbox':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mdc-form-field">
									<div class="mdc-checkbox">
										<input 
										name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
										type="checkbox"
										class="mdc-checkbox__native-control <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>"
										value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
										<?php checked( $wsfw_component['value'], '1' ); ?>
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
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mwb-flex-col">
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

						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="" class="mwb-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div>
									<div class="mdc-switch">
										<div class="mdc-switch__track"></div>
										<div class="mdc-switch__thumb-underlay">
											<div class="mdc-switch__thumb"></div>
											<input name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>" type="checkbox" id="<?php echo esc_html( $wsfw_component['id'] ); ?>" value="on" class="mdc-switch__native-control <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" role="switch" aria-checked="<?php if ( 'on' == $wsfw_component['value'] ) echo 'true'; else echo 'false'; ?>"
											<?php checked( $wsfw_component['value'], 'on' ); ?>
											>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
						break;

						case 'button':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label"></div>
							<div class="mwb-form-group__control">
								<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
									<span class="mdc-button__label <?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>"><?php echo ( isset( $wsfw_component['button_text'] ) ? esc_html( $wsfw_component['button_text'] ) : '' ); ?></span>
								</button>
							</div>
						</div>

						<?php
						break;

						case 'multi':
							?>
							<div class="mwb-form-group mwb-isfw-<?php echo esc_attr( $wsfw_component['type'] ); ?>">
								<div class="mwb-form-group__label">
									<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
									</div>
									<div class="mwb-form-group__control">
									<?php
									foreach ( $wsfw_component['value'] as $component ) {
										?>
											<label class="mdc-text-field mdc-text-field--outlined">
												<span class="mdc-notched-outline">
													<span class="mdc-notched-outline__leading"></span>
													<span class="mdc-notched-outline__notch">
														<?php if ( 'number' != $component['type'] ) { ?>
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
										<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
								<?php
							break;
						case 'color':
						case 'date':
						case 'file':
							?>
							<div class="mwb-form-group mwb-isfw-<?php echo esc_attr( $wsfw_component['type'] ); ?>">
								<div class="mwb-form-group__label">
									<label for="<?php echo esc_attr( $wsfw_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $wsfw_component['title'] ) ? esc_html( $wsfw_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								</div>
								<div class="mwb-form-group__control">
									<label class="mdc-text-field mdc-text-field--outlined">
										<input 
										class="<?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>" 
										name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
										type="<?php echo esc_attr( $wsfw_component['type'] ); ?>"
										value="<?php echo ( isset( $wsfw_component['value'] ) ? esc_attr( $wsfw_component['value'] ) : '' ); ?>"
										<?php echo esc_html( ( 'date' === $wsfw_component['type'] ) ? 'max='. date( 'Y-m-d', strtotime( date( "Y-m-d", mktime() ) . " + 365 day" ) ) .' ' . 'min=' . date( "Y-m-d" ) . '' : '' ); ?>
										>
									</label>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $wsfw_component['description'] ) ? esc_attr( $wsfw_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
							<?php
						break;

						case 'submit':
						?>
						<tr valign="top">
							<td scope="row">
								<input type="submit" class="button button-primary" 
								name="<?php echo ( isset( $wsfw_component['name'] ) ? esc_html( $wsfw_component['name'] ) : esc_html( $wsfw_component['id'] ) ); ?>"
								id="<?php echo esc_attr( $wsfw_component['id'] ); ?>"
								class="<?php echo ( isset( $wsfw_component['class'] ) ? esc_attr( $wsfw_component['class'] ) : '' ); ?>"
								value="<?php echo esc_attr( $wsfw_component['button_text'] ); ?>"
								/>
							</td>
						</tr>
						<?php
						break;

						default:
						break;
					}
				}
			}
		}
	}
}
