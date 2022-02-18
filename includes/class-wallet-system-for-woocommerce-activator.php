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
				self::wsfw_upgrade_wp_postmeta();
				self::wsfw_upgrade_wp_usermeta();
				self::wsfw_upgrade_wp_options();
				restore_current_blog();
			}
		} else {
			self::create_table_and_product();
			self::wsfw_upgrade_wp_postmeta();
			self::wsfw_upgrade_wp_usermeta();
			self::wsfw_upgrade_wp_options();
		}
	}

	/**
	 * Create transaction table and product on new blog creation.
	 *
	 * @return void
	 */
	public static function create_table_and_product() {
		// create wallet metakey in usermeta of users.
		$users = get_users();
		foreach ( $users as $user ) {
			$user_id = $user->ID;
			$wallet  = get_user_meta( $user_id, 'wps_wallet', true );
			if ( empty( $wallet ) ) {
				$wallet = update_user_meta( $user_id, 'wps_wallet', 0 );
			}
		}
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

		// create custom table named wp-db-prefix_wps_wsfw_wallet_transaction.
		global $wpdb;
		$table_name   = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
		$wpdb_collate = $wpdb->collate;
		$sql          = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) unsigned NOT NULL auto_increment,
			user_id bigint(20) unsigned NULL,
			amount double,
			currency varchar( 20 ) NOT NULL,
			transaction_type varchar(200) NULL,
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

	/**
	 * Update post meta keys.
	 *
	 * @return void
	 */
	public static function wsfw_upgrade_wp_postmeta() {

		$post_meta_keys = array(
			'mwb_wallet_withdrawal_amount',
			'mwb_wallet_note',
		);

		foreach ( $post_meta_keys as $key => $meta_keys ) {
			$products = get_posts(
				array(
					'numberposts' => -1,
					'post_status' => 'approved',
					'fields'      => 'ids', // return only ids.
					'meta_key'    => $meta_keys, //phpcs:ignore
					'post_type'   => 'wallet_withdrawal',
					'order'       => 'ASC',
				)
			);

			if ( ! empty( $products ) && is_array( $products ) ) {
				foreach ( $products as $k => $product_id ) {
					$value   = get_post_meta( $product_id, $meta_keys, true );
					$new_key = str_replace( 'mwb_', 'wps_', $meta_keys );

					if ( ! empty( get_post_meta( $product_id, $new_key, true ) ) ) {
						continue;
					}
					update_post_meta( $product_id, $new_key, $value );
				}
			}
		}
	}

	/**
	 * Upgrade user meta.
	 *
	 * @return void
	 */
	public static function wsfw_upgrade_wp_usermeta() {

		$all_users = get_users();
		if ( ! empty( $all_users ) && is_array( $all_users ) ) {
			foreach ( $all_users as $user ) {
				$user_id       = $user->ID;
				$wallet_amount = get_user_meta( $user_id, 'mwb_wallet', true );
				if ( ! empty( $wallet_amount ) ) {
					update_user_meta( $user_id, 'wps_wallet', $wallet_amount );
				}
			}
		}
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public static function wsfw_upgrade_wp_options() {
		$wp_options = array(
			'mwb_all_plugins_active'                                 => '',
			'mwb_wsfw_rechargeable_product_id'                       => '',
			'mwb_wsfw_enable'                                        => '',
			'mwb_wsfw_allow_refund_to_wallet'                        => '',
			'mwb_wsfw_enable_email_notification_for_wallet_update'   => '',
			'mwb_wsfw_wallet_rest_api_keys'                          => '',
			'mwb_wsfw_onboarding_data_sent'                          => '',
			'mwb_wsfw_onboarding_data_skipped'                       => '',
			'mwb_wsfw_updated_transaction_table'                     => '',
			'mwb_sfw_enable_wallet_on_renewal_order'		         => '',
			'mwb_sfw_amount_type_wallet_for_renewal_order'           => '',
			'mwb_sfw_amount_deduct_from_wallet_during_renewal_order' => '',
		);

		foreach ( $wp_options as $key => $value ) {
			$new_key = str_replace( 'mwb_', 'wps_', $key );
			if ( ! empty( get_option( $new_key ) ) ) {
				continue;
			}
			$new_value = get_option( $key, $value );
			update_option( $new_key, $new_value );
		}
	}

}
