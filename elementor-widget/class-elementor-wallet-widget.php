<?php
if ( ! class_exists( 'Elementor_Wallet_Widget' ) ) {
	/**
	 * Create wallet order list
	 */
	class Elementor_Wallet_Widget {

		protected static $instance = null;

		/**
		 * GEt instance of wallet class.
		 *
		 * @return void
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
			require_once 'wallet-widget.php';
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
