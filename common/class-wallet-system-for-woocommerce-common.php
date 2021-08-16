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

	public function mwb_wsfw_wallet_shortcodes() {
		add_shortcode( 'MWB_WALLET_RECHARGE', array( $this, 'mwb_wsfw_show_wallat_recharge' ) );
		//add_shortcode( 'mwb-wallet', array( $this, 'mwb_wsfw_show_wallet' ) );
		//add_shortcode( 'mwb-wallet', array( $this, 'mwb_wsfw_show_wallet' ) );
		//add_shortcode( 'mwb-wallet', array( $this, 'mwb_wsfw_show_wallet' ) );
	}

	public function mwb_wsfw_show_wallat_recharge() {
		ob_start();
		require WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/wallet-system-for-woocommerce-wallet-recharge.php';
		return ob_get_clean();
	}

}
