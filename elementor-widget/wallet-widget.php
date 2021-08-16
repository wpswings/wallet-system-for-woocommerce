<?php
namespace Elementor;

if ( ! class_exists( 'Widget_Base' ) ) {
	return;
}
/**
 * Elementor Wallet_Widget.
 *
 * Elementor widget that inserts wallet content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Wallet_Widget extends Widget_Base {

	/**
	 * Returns widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wallet-snippet';
	}

	/**
	 * Returns widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return 'Wallet Snippet';
	}

	/**
	 * Returns widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'fa fa-credit-card';
	}

	/**
	 * Returns widget category.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'basic' );
	}

	/**
	 * Register Wallet widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_title',
			array(
				'label' => __( 'Wallet Content', 'wallet-system-for-woocommerce' ),
			)
		);

		$this->add_control(
			'select_wallet_snippet',
			array(
				'label'              => __( 'Select Wallet Snippet', 'wallet-system-for-woocommerce' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '[MWB_WALLET_RECHARGE]',
				'options'            => array(
					'[MWB_WALLET_RECHARGE]'     => __( 'Wallet Recharge', 'wallet-system-for-woocommerce' ),
					'[MWB_WALLET_TRANSFER]'     => __( 'Wallet Transfer', 'wallet-system-for-woocommerce' ),
					'[MWB_WITHDRAWAL_REQUEST]'  => __( 'Wallet Withdrawal Request', 'wallet-system-for-woocommerce' ),
					'[MWB_WALLET_TRANSACTIONS]' => __( 'Wallet Transactions', 'wallet-system-for-woocommerce' ),
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render wallet widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		echo  "$settings[select_wallet_snippet]";	
	}

}
