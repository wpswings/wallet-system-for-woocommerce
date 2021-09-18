<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! class_exists( 'Elementor_Wallet_Widget' ) ) {
	/**
	 * Create wallet order list
	 */
	class Elementor_Wallet_Widget {

		/**
		 * Instance of the class.
		 *
		 * @var     object  $instance  Instance of the class.
		 * @since   1.0.0
		 */
		protected static $instance = null;

		/**
		 * Get instance of wallet class.
		 *
		 * @return Elementor_Wallet_Widget - Main instance.
		 */
		public static function get_instance() {
			if ( ! isset( static::$instance ) ) {
				static::$instance = new static;
			}

			return static::$instance;
		}

		/**
		 * Initialize the class and set its properties.
		 */
		protected function __construct() {
			require_once 'class-wallet-widget.php';
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
		}

		/**
		 * Register the widget for elementor.
		 *
		 * @return void
		 */
		public function register_widgets() {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Wallet_Widget() );
		}

	}
}
add_action( 'init', 'my_elementor_init' );
/**
 * Create instance.
 *
 * @return void
 */
function my_elementor_init() {
	Elementor_Wallet_Widget::get_instance();
}
