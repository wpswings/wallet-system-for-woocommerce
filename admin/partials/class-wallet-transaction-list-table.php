<?php
/**
 * Exit if accessed directly
 *
 * @since      1.0.0
 * @package    points-and-rewards-for-wooCommerce
 * @subpackage points-and-rewards-for-wooCommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * This is construct of class where all users point listed.
 *
 * @name Wallet_Transaction_List_Table
 * @since      1.0.0
 * @category Class
 * @author WP Swings <webmaster@wpswings.com>
 * @link https://www.wpswings.com/
 */
class Wallet_Transaction_List_Table extends WP_List_Table {

	/**
	 * This is variable which is used for the store all the data.
	 *
	 * @var array $example_data variable for store data.
	 */
	public $example_data;

	/**
	 * This is variable which is used for the store all the data.
	 *
	 * @var array $wps_total_counta variable for store data.
	 */
	public $wps_total_count;

	/**
	 * This construct colomns in point table.
	 *
	 * @name get_columns.
	 * @since      1.0.0
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 */
	public function get_columns() {

		$columns = array(
			'user_id'             => __( 'ID', 'wallet-system-for-woocommerce' ),
			'user_name'      => __( 'Name', 'wallet-system-for-woocommerce' ),
			'user_email'     => __( 'Email', 'wallet-system-for-woocommerce' ),
			'user_amount'    => __( 'Amount', 'wallet-system-for-woocommerce' ),
			'payment_method'           => __( 'Payment Method', 'wallet-system-for-woocommerce' ),
			'details' => __( 'Details', 'wallet-system-for-woocommerce' ),
			'transaction_id'         => __( 'Transaction ID', 'wallet-system-for-woocommerce' ),
			'date'        => __( 'Date', 'wallet-system-for-woocommerce' ),
			'action'        => __( 'Action', 'wallet-system-for-woocommerce' ),
			// 'date1_other'        => __( 'Date1', 'wallet-system-for-woocommerce' ),
		);
		return $columns;
	}

	/**
	 * This show points table list.
	 *
	 * @name column_default.
	 * @since      1.0.0
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 * @param array  $item  array of the items.
	 * @param string $column_name name of the colmn.
	 */
	public function column_default( $item, $column_name ) {

		$wps_user = get_user_by( 'id', $item['id'] );
		$points   = ! empty( get_user_meta( $item['id'], 'wps_wpr_points', true ) ) ? get_user_meta( $item['id'], 'wps_wpr_points', true ) : 0;

		switch ( $column_name ) {

			case 'user_id':
				return '<b>' . $item['user_id'] . '</b>';
			case 'user_name':
				return '<b>' . $item['user_name'] . '</b>';
			case 'user_email':
				return '<b>' . $item['user_email'] . '</b>';
			case 'user_amount':
				return '<b class="wps_wallet_' . esc_attr( $item['details_amount'] ) . '">' . wc_price( $item['user_amount'] ) . '</b>';
			case 'payment_method':
				return '<b>' . $item['payment_method'] . '</b>';
			case 'details':
				return '<b>' . ( html_entity_decode( $item['details'] ) ) . '</b>';
			case 'transaction_id':
				return '<b>' . $item['transaction_id'] . '</b>';
			case 'date':
				return '<b>' . $item['date'] . '</b>';
			case 'action':
				$is_pro = false;
				$is_pro = apply_filters( 'wsfw_check_pro_plugin', $is_pro );
				if ( ! $is_pro ) {

					return '<span class="wps_wallet_delete_action wps_pro_settings" >' . esc_html__( 'Delete', 'wallet-system-for-woocommerce' ) . '</span>';

				} else {

					return ' <span class="wps_wallet_delete_action" onclick="wps_wallet_delete_function(' . esc_attr( $item['id'] ) . ')">' . esc_html__( 'Delete', 'wallet-system-for-woocommerce' ) . '</span>';

				}

			default:
				return false;
		}
	}



	/**
	 * This construct update button on points table.
	 *
	 * @name view_html.
	 * @since      1.0.0
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 * @param int $user_id  user id of the user.
	 */
	public function view_html( $user_id ) {

		echo '<a  href="javascript:void(0)" class="wps_points_update button button-primary wps_wpr_save_changes" data-id="' . esc_html( $user_id ) . '">' . esc_html__( 'Update', 'wallet-system-for-woocommerce' ) . '</a>';
	}

	/**
	 * Returns an associative array containing the bulk action for sorting.
	 *
	 * @name get_sortable_columns.
	 * @since      1.0.0
	 * @return array
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 */
	public function get_sortable_columns() {

		$sortable_columns = array(
			'user_name'   => array( 'user_name', false ),
			'user_email'  => array( 'user_email', false ),
			'user_amount' => array( 'user_amount', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Undocumented function
	 *
	 * @param object $data data.
	 * @return array
	 */
	public function wps_wpr_sort_user_table( $data ) {

		$index = 1;
		$points_data = array();
		if ( ! empty( $data ) ) {
			foreach ( $data as $sort_id ) {

				$user          = get_userdata( $sort_id['user_id'] );
				$date = date_create( $sort_id['date'] );
				$transaction_data = esc_html( $date->getTimestamp() . $sort_id['id'] );
				$points_data[] = array(
					'user_id'        => $index,
					'id'             => $sort_id['id'],
					'user_name'      => $user->display_name,
					'user_email'     => $user->user_email,
					'user_amount'    => $sort_id['amount'],
					'payment_method' => $sort_id['payment_method'],
					'details'        => $sort_id['transaction_type'],
					'transaction_id' => $transaction_data,
					'date'           => $sort_id['date'],
					'action'         => '',
					// 'date1_other'    => $sort_id['date'],
					'details_amount'        => $sort_id['transaction_type_1'],
				);
				$index++;
			}
		}

		return $points_data;
	}


	/**
	 * Prepare items for sorting.
	 *
	 * @name prepare_items.
	 * @since      1.0.0
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 */
	public function prepare_items() {

		$per_page              = 10;

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$current_page          = $this->get_pagenum();
		$data = $this->get_users_wallet_transaction( $current_page, $per_page );
		global $wpdb;
		$transactions_total_count_data = '';
		$transactions_total_count = $wpdb->get_results( 'SELECT count(id) as total_count FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction' );
		if ( ! empty( $transactions_total_count ) ) {
			$transactions_total_count_data = $transactions_total_count[0]->total_count;
		}
		$sort_data = $this->wps_wpr_sort_user_table( $data );
		usort( $sort_data, array( $this, 'wps_wpr_usort_reorder' ) );
		$this->items = $sort_data;
		$total_items = $transactions_total_count_data;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$hidden                = array();
		$sortable              = array();
	}

	/**
	 * Return sorted associative array.
	 *
	 * @name wps_wpr_usort_reorder.
	 * @since      1.0.0
	 * @return array
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 * @param array $cloumna column of the points.
	 * @param array $cloumnb column of the points.
	 */
	public function wps_wpr_usort_reorder( $cloumna, $cloumnb ) {

		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'id';
		$order   = ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc';
		if ( is_numeric( $cloumna[ $orderby ] ) && is_numeric( $cloumnb[ $orderby ] ) ) {
			if ( $cloumna[ $orderby ] == $cloumnb[ $orderby ] ) {

				return 0;
			} elseif ( $cloumna[ $orderby ] < $cloumnb[ $orderby ] ) {

				$result = -1;
				return ( 'asc' === $order ) ? $result : -$result;
			} elseif ( $cloumna[ $orderby ] > $cloumnb[ $orderby ] ) {

				$result = 1;
				return ( 'asc' === $order ) ? $result : -$result;
			}
		} else {

			$result = strcmp( $cloumna[ $orderby ], $cloumnb[ $orderby ] );
			return ( 'asc' === $order ) ? $result : -$result;
		}
	}

	/**
	 * THis function is used for the add the checkbox.
	 *
	 * @name column_cb.
	 * @since      1.0.0
	 * @return array
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 * @param array $item array of the items.
	 */
	public function column_cb( $item ) {

		return sprintf(
			'<input type="checkbox" name="mpr_points_ids[]" value="%s" />',
			$item['id']
		);
	}

	/**
	 * This function gives points to user if he doesnot get points.
	 *
	 * @name get_users_wallet_transaction.
	 * @since      1.0.0
	 * @return array
	 * @author WP Swings <webmaster@wpswings.com>
	 * @link https://www.wpswings.com/
	 * @param int $current_page current page.
	 * @param int $per_page no of pages.
	 */
	public function get_users_wallet_transaction( $current_page, $per_page ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wps_wsfw_wallet_transaction'; // Replace 'your_custom_table' with your custom table name.
		$table_name2 = $wpdb->prefix . 'users';
		$per_page = 10;  // Number of rows per page.
		// Calculate the offset.
		$offset = ( $current_page - 1 ) * $per_page;
		$args = array(
			'number' => $per_page,
			'offset' => ( $current_page - 1 ) * $per_page,
			'fields' => 'ID',
		);

		if ( isset( $_POST['hidden_transaction_number'] ) && ! empty( $_POST['hidden_transaction_number'] ) ) {
			$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce ) ) {
				return false;
			}
			if ( isset( $_POST['hidden_transaction_number'] ) && ! empty( $_POST['hidden_transaction_number'] ) ) {
				$per_page      = ( isset( $_POST['hidden_transaction_number'] ) ) ? sanitize_text_field( wp_unslash( $_POST['hidden_transaction_number'] ) ) : '10';
			}
			// SQL query.
			$sql = $wpdb->prepare(
				"SELECT *
				FROM {$wpdb->prefix}wps_wsfw_wallet_transaction  ORDER BY id DESC
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			);
		} elseif ( isset( $_POST['hidden_from_date'] ) && ! empty( $_POST['hidden_from_date'] ) ) {
			$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce ) ) {
				return false;
			}

			$date_from = ! empty( $_POST['hidden_from_date'] ) ? sanitize_text_field( wp_unslash( $_POST['hidden_from_date'] ) ) : '';
			$date_to   = ! empty( $_POST['hidden_to_date'] ) ? sanitize_text_field( wp_unslash( $_POST['hidden_to_date'] ) ) : '';
			$sql = $wpdb->prepare(
				"SELECT *
				FROM {$wpdb->prefix}wps_wsfw_wallet_transaction table1 JOIN {$wpdb->prefix}users table2 on table1.`user_id` =  table2.`ID` WHERE 
				table1.date BETWEEN '{$date_from} 00:00:00' AND '{$date_to} 23:59:59'
				ORDER BY table1.id DESC
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			);
			
		} elseif ( isset( $_REQUEST['s'] ) ) {
			$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce ) ) {
				return false;
			}
			$wps_request_search = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			$wps_request_search = '%' . $wps_request_search . '%';

			// SQL query
			$sql = $wpdb->prepare(
				"SELECT *
				FROM {$wpdb->prefix}wps_wsfw_wallet_transaction table1 JOIN {$wpdb->prefix}users table2 on table1.`user_id` =  table2.`ID` WHERE (table1.`id` LIKE '$wps_request_search'	 OR
				`user_id` LIKE '$wps_request_search' OR
				`amount` LIKE '$wps_request_search' OR
				`currency` LIKE '$wps_request_search' OR
				`transaction_type` LIKE '$wps_request_search' OR
				`payment_method` LIKE '$wps_request_search' OR
				`note` LIKE '$wps_request_search' OR
				`user_email` LIKE '$wps_request_search' OR
				`display_name` LIKE '$wps_request_search' OR
				`transaction_type_1` LIKE '$wps_request_search')
				ORDER BY table1.id DESC
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			);

		} else {

			// SQL query
			$sql = $wpdb->prepare(
				"SELECT *
				FROM $table_name  ORDER BY id DESC
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			);

		}

		// Execute the query.
		$results = $wpdb->get_results( $sql, ARRAY_A );

		return $results;
	}
}


if ( isset( $_POST['hidden_from_date'] ) && ! empty( $_POST['hidden_from_date'] ) ) {

	$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce ) ) {
		return false;
	}
	$date_from = ! empty( $_POST['hidden_from_date'] ) ? sanitize_text_field( wp_unslash( $_POST['hidden_from_date'] ) ) : '';
	$date_to   = ! empty( $_POST['hidden_to_date'] ) ? sanitize_text_field( wp_unslash( $_POST['hidden_to_date'] ) ) : '';

}

?>




<div class="wps-wpg-gen-section-table-wrap wps-wpg-transcation-section-table">
	<h4><?php esc_html_e( 'Transactions', 'wallet-system-for-woocommerce' ); ?> </h4>
	<form method="GET">
		<input type="submit" class="btn button" name= "wps_wsfw_export_pdf" id="wps_wsfw_export_pdf" value="<?php esc_html_e( 'Export Pdf', 'wallet-system-for-woocommerce' ); ?>">
	</form>
	<?php

		$limit_for_transaction = '10';

	if ( isset( $_POST['hidden_transaction_number'] ) && ! empty( $_POST['hidden_transaction_number'] ) ) {

		$limit_for_transaction      = ( isset( $_POST['hidden_transaction_number'] ) ) ? sanitize_text_field( wp_unslash( $_POST['hidden_transaction_number'] ) ) : '';

	}


	?>
	<div class="wps-wpg-transcation-section-search">
		<div class="dataTables_length_wallet_custom_table" id="wps-wsfw-wallet-trabsacstion-numbers">
			<label><?php esc_html_e( 'Rows per page', 'wallet-system-for-woocommerce' ); ?>
				<select name="wps-wsfw-wallet-trabsacstion-numbers-drodown" id ="wps-wsfw-wallet-trabsacstion-numbers-drodown" aria-controls="wps-wpg-gen-table_trasa" >
					<option value="10" <?php echo ( '10' == $limit_for_transaction ? 'selected="selected"' : '' ); ?>>10</option>
					<option value="25" <?php echo ( '25' == $limit_for_transaction ? 'selected="selected"' : '' ); ?>>25</option>
					<option value="50" <?php echo ( '50' == $limit_for_transaction ? 'selected="selected"' : '' ); ?>>50</option>
					<option value="100" <?php echo ( '100' == $limit_for_transaction ? 'selected="selected"' : '' ); ?>>100</option>
				</select>
			</label>
		</div>
		<form method="post">
			<table>
					<tbody>
						<tr>
						</tr>
						<tr>
							<td><input type="date" id="fromdate_transaction" name="min" id="min"  placeholder="From" value="<?php echo esc_attr( $date_from ); ?>"  autocomplete="off"></td>
						</tr>
						<tr>
							<td><input type="date"  id="todate_transaction" name="max" id="max"  placeholder="To" value="<?php echo esc_attr( $date_to ); ?>" autocomplete="off"></td>
						</tr>
						<tr>
							<td><span id="clear_table" class="btn button"><?php esc_html_e( 'Clear', 'wallet-system-for-woocommerce' ); ?></span></td>
						</tr>
					</tbody>
				</table>
		</form>
	</div>
	<div class="dataTables_length_wallet_custom_search_table">
		<form method="post">
			<input type="submit"  class="btn button" name= "wps_wsfw_data_number" id="wps_wsfw_data_number" value="" >
			<input type="hidden" id="hidden_transaction_number" name="hidden_transaction_number" value=""/>
			<input type="hidden" id="hidden_from_date" name="hidden_from_date" />
			<input type="hidden" id="hidden_to_date" name="hidden_to_date" />
			<input type="hidden" id="updatenoncewallet_creation" name="updatenoncewallet_creation" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
		
			<input type="hidden" name="page" value="<?php esc_html_e( 'wallet_log_list_table', 'wallet-system-for-woocommerce' ); ?>">
			<?php wp_nonce_field( 'wallet-transaction-log', 'wallet-transaction-log' ); ?>
			<?php
			$mylisttable = new Wallet_Transaction_List_Table();
			$mylisttable->prepare_items();
			$mylisttable->search_box( __( 'Search', 'wallet-system-for-woocommerce' ), 'search_id' );
			$mylisttable->display();
			?>
		</form>
	</div>
</div>
	<?php

	include_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH . 'admin/partials/wallet-system-for-woocommerce-go-pro-data.php';
