<?php
/**
 * WC_Wallet_Shop_Order_Data_Store class file.
 *
 * @package WooCommerce\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Order Data Store: Stored in CPT.
 *
 * @version  3.0.0
 */
class WC_Wallet_Shop_Order_Data_Store {
	/**
	 * The name of the database table used to store book data.
	 *
	 * @var string
	 */
	protected $table_name = '';

	/**
	 * Initialize book data store.
	 */
	public function __construct() {
		$this->table_name = $this->get_table_name( 'wallet-shop-order' ); // Replace 'book' with your post type name.
	}

	/**
	 * Get the database table name.
	 *
	 * @param string $type Optional. The entity type. Default is empty.
	 * @return string
	 */
	public function get_table_name( $type = '' ) {
		global $wpdb;
		return $wpdb->prefix . '';
	}

	/**
	 * Create a book in the database.
	 *
	 * @param WC_Product_Book $wallet_shop_order Book data.
	 */
	public function create( $wallet_shop_order ) {
		global $wpdb;

		$data = array(
			'post_id' => $wallet_shop_order->get_id(),
			'author' => $wallet_shop_order->get_author(),
			'genre' => $wallet_shop_order->get_genre(),
		);

		$wpdb->insert( $this->table_name, $data );
	}
}
