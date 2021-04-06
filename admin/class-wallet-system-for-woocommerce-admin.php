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
			'name'            => __( 'Wallet System for WooCommerce', 'wallet-system-for-woocommerce' ),
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
			array(
				'title' => __( 'Enable plugin', 'wallet-system-for-woocommerce' ),
				'type'  => 'radio-switch',
				'description'  => __( 'Enable plugin to start the functionality.', 'wallet-system-for-woocommerce' ),
				'id'    => 'wsfw_radio_switch_demo',
				'value' => get_option( 'wsfw_radio_switch_demo' ),
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
			}
		}
	}
}
