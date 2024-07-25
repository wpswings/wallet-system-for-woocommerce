<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wpswings.com/
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
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wallet_System_For_Woocommerce_Activator {

	/**
	 * Activation function.
	 *
	 * @since    1.0.0
	 * @param boolean $network_wide networkwide activate.
	 * @return void
	 */
	public static function wallet_system_for_woocommerce_activate( $network_wide ) {
		global $wpdb;
		if ( is_multisite() && $network_wide ) {
			// Get all blogs in the network and activate plugin on each one.
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::create_table_and_product();

				wp_clear_scheduled_hook( 'wpswings_tracker_send_event' );
				wp_schedule_event( time() + 10, apply_filters( 'wpswings_tracker_event_recurrence', 'daily' ), 'wpswings_tracker_send_event' );

				restore_current_blog();
			}
		} else {
			self::create_table_and_product();

			wp_clear_scheduled_hook( 'wpswings_tracker_send_event' );
			wp_schedule_event( time() + 10, apply_filters( 'wpswings_tracker_event_recurrence', 'daily' ), 'wpswings_tracker_send_event' );

		}
	}

	/**
	 * Create transaction table and product on new blog creation.
	 *
	 * @return void
	 */
	public static function create_table_and_product() {
		// create product named as wallet topup.
		if ( ! wc_get_product( get_option( 'wps_wsfw_rechargeable_product_id' ) ) ) {
			$product = array(
				'post_title'   => 'Rechargeable Wallet Product',
				'post_content' => 'This is the custom wallet topup product.',
				'post_type'    => 'product',
				'post_status'  => 'private',
				'post_author'  => 1,
			);

			$product_id = wp_insert_post( $product );
			// update price and visibility of product.
			if ( $product_id ) {
				update_post_meta( $product_id, '_regular_price', 0 );
				update_post_meta( $product_id, '_price', 0 );
				update_post_meta( $product_id, '_visibility', 'hidden' );
				update_post_meta( $product_id, '_virtual', 'yes' );

				$productdata = wc_get_product( $product_id );
				$productdata->set_catalog_visibility( 'hidden' );
				$productdata->save();

				update_option( 'wps_wsfw_rechargeable_product_id', $product_id );

			}
		}
		$wps_wsfw_cashback_amount_max = get_option( 'wps_wsfw_cashback_amount_max' );
		if ( empty( $wps_wsfw_cashback_amount_max ) ) {
			update_option( 'wps_wsfw_cashback_amount_max', 20 );
		}
		$wps_wsfw_cart_amount_min = get_option( 'wps_wsfw_cart_amount_min' );
		if ( empty( $wps_wsfw_cart_amount_min ) ) {
			update_option( 'wps_wsfw_cart_amount_min', 10 );
		}
		$wps_wsfw_cashback_amount = get_option( 'wps_wsfw_cashback_amount' );
		if ( empty( $wps_wsfw_cashback_amount ) ) {
			update_option( 'wps_wsfw_cashback_amount', 10 );
		}

		// create custom table named wp-db-prefix_wps_wsfw_wallet_transaction.
		global $wpdb;
		$table_name = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) !== $table_name ) {
			$table_name   = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
			$wpdb_collate = $wpdb->collate;
			$sql          = "CREATE TABLE IF NOT EXISTS {$table_name} (
				id bigint(20) unsigned NOT NULL auto_increment,
				user_id bigint(20) unsigned NULL,
				amount double,
				currency varchar( 20 ) NOT NULL,
				transaction_type varchar(200) NULL,
				transaction_type_1 varchar(200) NULL,
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
	}

}
