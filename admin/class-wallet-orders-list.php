<?php
/**
 * Order Factory
 *
 * The WooCommerce order factory creating the right order objects.
 *
 * @version 2.5.0
 * @package Wallet_System_For_Woocommerce
 */

 use Automattic\WooCommerce\Utilities\OrderUtil;
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
$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
if ( ! $id_nonce_verified ) {
	wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
}
if ( ! class_exists( 'Wallet_Orders_List' ) ) {
	/**
	 * Create wallet order list
	 */
	class Wallet_Orders_List extends WP_List_Table {

		/** Class constructor */
		public function __construct() {

			parent::__construct(
				array(
					'singular' => __( 'Wallet Recharge Order', 'wallet-system-for-woocommerce' ), // singular name of the listed records.
					'plural'   => __( 'Wallet Recharge Orders', 'wallet-system-for-woocommerce' ), // plural name of the listed records.
					'ajax'     => false, // should this table support ajax?

				)
			);

		}


		/**
		 * Render the bulk edit checkbox
		 *
		 * @param array $item wp list item.
		 *
		 * @return string
		 */
		public function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="bulk-action[]" value="%s" />',
				$item['ID']
			);
		}

		/**
		 * Define the columns that are going to be used in the table
		 *
		 * @return array $columns, the array of columns to use with the table
		 */
		public function get_columns() {
			$columns = array(
				'cb'          => '<input type="checkbox" />',
				'ID'          => __( 'Order', 'wallet-system-for-woocommerce' ),
				'user'        => __( 'User', 'wallet-system-for-woocommerce' ),
				'status'      => __( 'Status', 'wallet-system-for-woocommerce' ),
				'order_total' => __( 'Total', 'wallet-system-for-woocommerce' ),
				'date1'       => __( 'Date1', 'wallet-system-for-woocommerce' ),
				'date'        => __( 'Date', 'wallet-system-for-woocommerce' ),
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
				'ID'          => array( 'ID', true ),
				'date'        => array( 'date', false ),
				'order_total' => array( 'order_total', false ),
			);
			return $sortable;
		}

		/**
		 * Add all, status list link above table
		 *
		 * @return array
		 */
		public function get_views() {
			global $wpdb;
			$views    = array();
			$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
			$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
			if ( ! $id_nonce_verified ) {
				wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
			}
			$current  = ( ! empty( $_REQUEST['post_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_status'] ) ) : 'all' );
			$rowcount = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'posts WHERE `post_type` = "wallet_shop_order" AND ( NOT post_status = "auto-draft" && NOT post_status = "trash" )' );

			// All link.
			$class        = ( 'all' === $current ? ' class="current"' : '' );
			$all_url      = remove_query_arg( 'post_status' );
			$all_url      = remove_query_arg( array( 'paged', 'orderby', 'order', 'bulk_action', 'changed' ), $all_url );
			$views['all'] = "<a href='{$all_url}' {$class} >All<span class='count'>($rowcount)</span></a>";

			$order_statuses = wc_get_order_statuses();
			foreach ( $order_statuses as $key => $order_status ) {
				$rowcount = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'posts WHERE post_type = "wallet_shop_order" AND post_status = %s', $key ) );
				if ( $rowcount > 0 ) {
					$url           = add_query_arg( 'post_status', $key );
					$url           = remove_query_arg( array( 'paged', 'orderby', 'order', 'bulk_action', 'changed' ), $url );
					$class         = ( $current === $key ? ' class="current"' : '' );
					$views[ $key ] = "<a href='{$url}' {$class} >$order_status<span class='count'>($rowcount)</span></a>";
				}
			}

			// Trash link.
			$rowcount1 = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'posts WHERE post_type = "wallet_shop_order" AND post_status = %s', 'trash' ) );
			if ( $rowcount1 > 0 ) {
				$class          = ( 'trash' === $current ? ' class="current"' : '' );
				$all_url1       = add_query_arg( 'post_status', 'trash' );
				$all_url1       = remove_query_arg( array( 'paged', 'orderby', 'order', 'bulk_action', 'changed' ), $all_url1 );
				$views['trash'] = "<a href='{$all_url1}' {$class} >Trash<span class='count'>($rowcount1)</span></a>";
			}

			return $views;
		}

		/**
		 * Display the table heading and search query, if any
		 */
		public function display_header() {
			$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
			$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
			if ( ! $id_nonce_verified ) {
				wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
			}
			if ( isset( $_REQUEST['s'] ) ) {
				echo '<span class="subtitle">' . sprintf( 'Search results for %s', esc_html( sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) ) ) . '</span>';
			}

		}

		/**
		 * Extract custom order type data from database
		 *
		 * @return array
		 */
		private function table_data() {
			global $wpdb;
			$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
			$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
			if ( ! $id_nonce_verified ) {
				wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
			}
			$post_status = isset( $_REQUEST['post_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_status'] ) ) : '';

			$table_post = $wpdb->prefix . 'posts';
			$table_pastmeta = $wpdb->prefix . 'posts';
			$table_post_id = $wpdb->prefix . 'posts.ID';
			$table_postmeta_id = $wpdb->prefix . 'posts.post_id';
			$data = array();
			if ( isset( $_REQUEST['s'] ) ) {

				$search = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );

				$search = trim( $search );

				if ( isset( $post_status ) && ! empty( $post_status ) ) {
					$orders = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'posts WHERE `post_type` = "wallet_shop_order" AND `post_status` = %s ORDER BY `ID` DESC', $post_status ) );
				} else {
					$orders = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'posts WHERE `post_type` = "wallet_shop_order" AND `post_status` = %s ORDER BY `ID` DESC', $post_status ) );
				}
			} else {

				if ( isset( $post_status ) && ! empty( $post_status ) ) {

					$orders = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'posts WHERE `post_type` = "wallet_shop_order" AND `post_status` = %s ORDER BY `ID` DESC', $post_status ) );
				} else {

					$orders = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'posts WHERE `post_type` = "wallet_shop_order" AND ( NOT `post_status` = "auto-draft" && NOT `post_status` = "trash" ) ORDER BY `ID` DESC' );
				}
			}

			if ( ! empty( $orders ) && is_array( $orders ) ) {
				foreach ( $orders as $order ) {
					$order_data = wc_get_order( $order->ID );
					$first_name = $order_data->get_billing_first_name();
					$last_name  = $order_data->get_billing_last_name();
					if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
						$billing_name = $first_name . ' ' . $last_name;
					} else {
						$user_id      = $order_data->get_customer_id();
						$customer     = new WC_Customer( $user_id );
						$billing_name = $customer->get_username();
					}
					$order_total = $order_data->get_total();

					$order_total = wc_price( $order_total, array( 'currency' => $order_data->get_currency() ) );
					$data[]      = array(
						'ID'          => $order->ID,
						'user'        => $billing_name,
						'status'      => $order_data->get_status(),
						'order_total' => $order_total,
						'date1'       => $order_data->get_date_created(),
						'date'        => $order_data->get_date_created(),
					);
				}
			}
			return $data;
		}

		/**
		 * Returns an associative array containing the bulk action
		 *
		 * @return array
		 */
		public function get_bulk_actions() {
			$actions = array();
			$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
			$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
			if ( ! $id_nonce_verified ) {
				wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
			}
			$current = ( ! empty( $_REQUEST['post_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_status'] ) ) : 'all' );

			if ( 'trash' === $current ) {
				$actions['untrash'] = esc_html__( 'Restore', 'wallet-system-for-woocommerce' );
				$actions['delete']  = esc_html__( 'Delete permanently', 'wallet-system-for-woocommerce' );
			} else {
				$actions['trash']  = esc_html__( 'Move to Trash', 'wallet-system-for-woocommerce' );
			}

			return $actions;
		}

		/**
		 * Process bulk actions
		 *
		 * @return void
		 */
		public function process_bulk_action() {
			$action    = $this->current_action();
			$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
			$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
			if ( ! $id_nonce_verified ) {
				wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
			}
			$order_ids = isset( $_REQUEST['bulk-action'] ) ? wp_parse_id_list( wp_unslash( $_REQUEST['bulk-action'] ) ) : array();

			if ( empty( $order_ids ) ) {
				return;
			}

			$count    = 0;
			$failures = 0;

			switch ( $action ) {
				case 'trash':
					foreach ( $order_ids as $order_id ) {
						$order        = wc_get_order( $order_id );
						$order_status = $order->get_status();
						if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
							// HPOS usage is enabled.
							$order->update_meta_data( 'wallet_order_status', $order_status );
							$order->save();

						} else {
							update_post_meta( $order_id, 'wallet_order_status', $order_status );
						}

						if ( wp_trash_post( $order_id ) ) {
							$count++;
						} else {
							$failures++;
						}
					}
					break;

				case 'untrash':
					foreach ( $order_ids as $order_id ) {
						$order        = wc_get_order( $order_id );

						if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
							// HPOS usage is enabled.
							$order_status = $order->get_meta( 'wallet_order_status', true );
						} else {
							$order_status = get_post_meta( $order_id, 'wallet_order_status', true );
						}
						if ( $order_status ) {
							$status = $order->update_status( $order_status );
							if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
								// HPOS usage is enabled.
								$order->delete_meta_data( 'wallet_order_status' );
								$order->save();

							} else {
								delete_post_meta( $order_id, 'wallet_order_status' );
							}
						}
						if ( $status ) {
							$count++;
						} else {
							$failures++;
						}
					}
					break;

				case 'delete':
					foreach ( $order_ids as $order_id ) {
						if ( wp_delete_post( $order_id, true ) ) {
							$count++;
						} else {
							$failures++;
						}
					}
					break;
			}

			wp_safe_redirect(
				add_query_arg(
					array(
						'bulk_action' => $action,
						'changed'     => $count,
					)
				)
			);
			exit;

		}

		/**
		 * Show order in custom wp list table
		 *
		 * @return void
		 */
		public function prepare_items() {

			global $wpdb;

			// Retrieve $post_status for use in query to get items.

			$columns = $this->get_columns();

			$sortable = $this->get_sortable_columns();

			$hidden = $this->get_hidden_columns();

			$this->process_bulk_action();

			$data = $this->table_data();

			$totalitems = count( $data );

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
		 * Compare the values of custom order table
		 *
		 * @param Array $a first item.
		 * @param Array $b second item.
		 * @return int
		 */
		public function usort_reorder( $a, $b ) {
			$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
			$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
			if ( ! $id_nonce_verified ) {
				wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
			}

			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'ID'; // If no sort, default to title.

			$order   = ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc'; // If no order, default to asc.

			$result = strnatcmp( $a[ $orderby ], $b[ $orderby ] ); // Determine sort order.

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

			$secure_nonce      = wp_create_nonce( 'wps-wallet-custom-order-nonce' );
			$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-custom-order-nonce' );
			if ( ! $id_nonce_verified ) {
				wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
			}

			switch ( $column_name ) {

				case 'ID':
					if ( isset( $_REQUEST['post_status'] ) && 'trash' === sanitize_text_field( wp_unslash( $_REQUEST['post_status'] ) ) ) {
						return '<strong>#' . $item[ $column_name ] . '</strong>';
					} else {

						return '<a href="' . admin_url( 'post.php?post=' . $item[ $column_name ] . ' &action=edit' ) . '" class="order-view"><strong>#' . $item[ $column_name ] . '</strong></a>';
					}
					break;
				case 'user':
					return $item[ $column_name ];
					break;
				case 'status':
					return '<mark class="wallet-status order-status status-' . $item[ $column_name ] . '"><span>' . $item[ $column_name ] . '</span></mark>';
					break;
				case 'order_total':
					return $item[ $column_name ];
					break;
				case 'date1':
					$date = date_create( $item[ $column_name ] );
					return date_format( $date, 'm/d/Y' );
					break;
				case 'date':
					$date        = date_create( $item[ $column_name ] );
					$date_format = get_option( 'date_format', 'm/d/Y' );
					return date_format( $date, $date_format );
			}

		}

		/**
		 * Text displayed when no order data is available
		 *
		 * @return void
		 */
		public function no_items() {
			esc_html_e( 'No order found.', 'wallet-system-for-woocommerce' );
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
