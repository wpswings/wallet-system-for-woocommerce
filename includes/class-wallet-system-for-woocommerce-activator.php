<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Wallet_System_For_Woocommerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function wallet_system_for_woocommerce_activate() {

		// create wallet metakey in usermeta of users
		$users = get_users();
		foreach ( $users as $user ) {
			$user_id = $user->ID;
			$wallet = get_user_meta( $user_id, 'mwb_wallet', true );
			if ( empty( $wallet ) ) {
				$wallet = update_user_meta( $user_id, 'mwb_wallet', 0 );
			}
			
		}

		// create product named as wallet topup 
		$product = array(
			'post_title'  => 'Rechargeable Wallet Product',
			'post_type'   => 'product',
			'post_status' => 'publish',
			'post_author' => 1,
		);

		$product_id = wp_insert_post( $product );
		// update price and visibility of product
		if ( $product_id ) {
			update_post_meta( $product_id, '_regular_price', 0 );
			update_post_meta( $product_id, '_price', 0 );
			$productdata = wc_get_product( $product_id );
			$productdata->set_catalog_visibility( 'hidden' );
			$productdata->save();

			update_option( 'PC_rechargeable_product_id', $product_id );

		}

		// create custom table named wp-db-prefix_PC_wallet_transaction
		global $wpdb;
		$table_name = $wpdb->prefix . 'PC_wallet_transaction';
		$wpdb_collate = $wpdb->collate;
		$sql = "CREATE TABLE {$table_name} (
			Id bigint(20) unsigned NOT NULL auto_increment,
			user_id bigint(20) unsigned NULL,
			amount double,
			transaction_type varchar(200) NULL,
			payment_method varchar(50) NULL,
			transaction_id varchar(50) NULL,
			note varchar(500) Null,
			date datetime,
			PRIMARY KEY  (Id),
			KEY user_id (user_id)
			)
			COLLATE {$wpdb_collate}";
	
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql );


	}

}
