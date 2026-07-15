<?php
/**
 * Handles POS database schema creation and versioned upgrades.
 *
 * @link       https://wpswings.com/
 * @since      2.8.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates the POS registers/sessions tables and extends the wallet
 * transaction table for POS use, without altering existing wallet tables
 * or behaviour beyond adding new nullable columns.
 */
class Wallet_System_For_Woocommerce_Pos_Activator {

	/**
	 * Bump this whenever the POS schema changes so maybe_upgrade() re-runs.
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Runs the POS schema creation/upgrade if the stored version is stale.
	 *
	 * @return void
	 */
	public static function maybe_upgrade() {
		if ( get_option( 'wps_wsfw_pos_db_version' ) === self::DB_VERSION ) {
			return;
		}

		self::create_registers_table();
		self::create_register_sessions_table();
		self::add_wallet_transaction_columns();

		update_option( 'wps_wsfw_pos_db_version', self::DB_VERSION );
	}

	/**
	 * Creates the pos_registers table.
	 *
	 * @return void
	 */
	private static function create_registers_table() {
		global $wpdb;
		$table_name   = $wpdb->prefix . 'wps_wsfw_pos_registers';
		$wpdb_collate = $wpdb->collate;

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL auto_increment,
			register_name varchar(200) NOT NULL,
			location varchar(200) NULL,
			status varchar(20) NOT NULL DEFAULT 'closed',
			opened_by bigint(20) unsigned NULL,
			opened_at datetime NULL,
			closed_at datetime NULL,
			opening_cash decimal(20,4) NOT NULL DEFAULT 0,
			closing_cash decimal(20,4) NULL,
			PRIMARY KEY  (id),
			KEY status (status)
			) COLLATE {$wpdb_collate}";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Creates the pos_register_sessions table.
	 *
	 * @return void
	 */
	private static function create_register_sessions_table() {
		global $wpdb;
		$table_name   = $wpdb->prefix . 'wps_wsfw_pos_register_sessions';
		$wpdb_collate = $wpdb->collate;

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL auto_increment,
			register_id bigint(20) unsigned NOT NULL,
			cashier_id bigint(20) unsigned NOT NULL,
			opening_balance decimal(20,4) NOT NULL DEFAULT 0,
			closing_balance decimal(20,4) NULL,
			cash_sales_total decimal(20,4) NOT NULL DEFAULT 0,
			wallet_sales_total decimal(20,4) NOT NULL DEFAULT 0,
			card_sales_total decimal(20,4) NOT NULL DEFAULT 0,
			status varchar(20) NOT NULL DEFAULT 'open',
			notes varchar(500) NULL,
			opened_at datetime NULL,
			closed_at datetime NULL,
			PRIMARY KEY  (id),
			KEY register_id (register_id),
			KEY cashier_id (cashier_id)
			) COLLATE {$wpdb_collate}";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Adds `source` and `register_session_id` columns to the existing
	 * wallet transaction table, if not already present. Existing rows
	 * default to source = 'online' so historical data stays consistent.
	 *
	 * @return void
	 */
	private static function add_wallet_transaction_columns() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wps_wsfw_wallet_transaction';

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) !== $table_name ) {
			return;
		}

		$existing_columns = $wpdb->get_col( "DESCRIBE {$table_name}", 0 );

		if ( ! in_array( 'source', $existing_columns, true ) ) {
			$wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN source varchar(20) NULL DEFAULT 'online' AFTER transaction_type_1" );
		}

		if ( ! in_array( 'register_session_id', $existing_columns, true ) ) {
			$wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN register_session_id bigint(20) unsigned NULL AFTER source" );
		}
	}
}
