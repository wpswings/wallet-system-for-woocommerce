<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'Wallet_Transactions_List' ) ) {
	/**
	 * Create wallet transaction list
	 */
	class Wallet_Transactions_List extends WP_List_Table {

		/** Class constructor */
		public function __construct() {

			parent::__construct(
				array(
					'singular' => __( 'User Wallet Transaction', 'wallet-system-for-woocommerce' ), // singular name of the listed records.
					'plural'   => __( 'User Wallet Transactions', 'wallet-system-for-woocommerce' ), // plural name of the listed records.
					'ajax'     => false, // should this table support ajax?

				)
			);

		}

		/**
		 * Define the columns that are going to be used in the table
		 *
		 * @return array $columns, the array of columns to use with the table
		 */
		public function get_columns() {
			$columns = array(
				'transaction_id' => __( 'Transaction ID', 'wallet-system-for-woocommerce' ),
				'name'           => __( 'Name', 'wallet-system-for-woocommerce' ),
				'email'          => __( 'Email', 'wallet-system-for-woocommerce' ),
				'amount'         => __( 'Amount', 'wallet-system-for-woocommerce' ),
				'action'         => __( 'Action', 'wallet-system-for-woocommerce' ),
				'method'         => __( 'Method', 'wallet-system-for-woocommerce' ),
				'date'           => __( 'Date', 'wallet-system-for-woocommerce' ),
			);
			return $columns;
		}

		/**
		 * Decide which columns to activate the sorting functionality on
		 *
		 * @return array $sortable, the array of columns that can be sorted by the user
		 */
		public function get_sortable_columns() {
			$sortable = array(
				'transaction_id' => 'Id',
				'name'           => 'user_id',
				'amount'         => 'amount',
				'date'           => 'date',
			);
			return $sortable;
		}

		/**
		 * Retrieves transaction data from database
		 *
		 * @return Array $data return transaction table data
		 */
		private function table_data() {
			global $wpdb;

			$table_name = $wpdb->prefix . 'wps_wsfw_wallet_transaction';

			$data = array();

			if ( isset( $_GET['s'] ) ) {

				$search = sanitize_text_field( wp_unslash( $_GET['s'] ) );

				$search = trim( $search );

				$transactions = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction WHERE `user_id` LIKE  %s  AND column_name_four = "value"', $search ) );

			} else {

				$transactions = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction' );
			}

			if ( ! empty( $transactions ) && is_array( $transactions ) ) {
				foreach ( $transactions as $transaction ) {
					$user   = get_user_by( 'id', $transaction->id );
					$data[] = array(
						'transaction_id' => $transaction->id,
						'name'           => $user->user_login,
						'email'          => $user->user_email,
						'amount'         => wc_price( $transaction->amount ),
						'action'         => $transaction->transaction_type,
						'method'         => $transaction->payment_method,
						'date'           => $transaction->date,
					);
				}
			}

			return $data;

		}

		/**
		 * Show list table
		 *
		 * @return void
		 */
		public function prepare_items() {

			global $wpdb;

			$columns = $this->get_columns();

			$sortable = $this->get_sortable_columns();

			$hidden = $this->get_hidden_columns();

			$this->process_bulk_action();

			$data = $this->table_data();

			$totalitems = count( $data );

			$user   = get_current_user_id();
			$screen = get_current_screen();

			$perpage = 10;

			$this->_column_headers = array( $columns, $hidden, $sortable );

			usort( $data, array( $this, 'usort_reorder' ) );

			$totalpages = ceil( $totalitems / $perpage );

			$current_page = $this->get_pagenum();

			$data = array_slice( $data, ( ( $current_page - 1 ) * $perpage ), $perpage );

			$this->set_pagination_args(
				array(

					'total_items' => $totalitems,

					'total_pages' => $totalpages,

					'per_page'    => $perpage,
				)
			);

			$this->items = $data;
		}

		/**
		 * Undocumented function
		 *
		 * @param Array $a first order.
		 * @param Array $b second order.
		 * @return int
		 */
		public function usort_reorder( $a, $b ) {

			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'Id'; // If no sort, default to title.

			$order   = ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc'; // If no order, default to asc.

			$result = strcmp( $a[ $orderby ], $b[ $orderby ] ); // Determine sort order.

			return ( 'asc' === $order ) ? $result : -$result; // Send final sort direction to usort.

		}

		/**
		 * Show data in default columns
		 *
		 * @param array  $item table item.
		 * @param string $column_name column name.
		 * @return string
		 */
		public function column_default( $item, $column_name ) {

			switch ( $column_name ) {

				case 'transaction_id':
				case 'name':
				case 'email':
				case 'amount':
				case 'action':
				case 'method':
				case 'date':
					return $item[ $column_name ];
			}

		}

		/**
		 * Setup Hidden columns
		 *
		 * @return array
		 */
		public function get_hidden_columns() {
			// Setup Hidden columns and return them.
			return array();
		}
	}

}
