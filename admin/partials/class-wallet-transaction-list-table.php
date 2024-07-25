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
			'action_user_trasaction'        => __( 'Action', 'wallet-system-for-woocommerce' ),
		);
		return $columns;
	}

	/**
	 * Bulk action for transaction.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'export_csv' => 'Export to CSV',
			'export_excel' => 'Export to Excel',

		);
		return $actions;
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

		$tranasction_symbol = '';
		if ( 'credit' == $item['details_amount'] ) {
			$tranasction_symbol = '+';
		} elseif ( 'debit' == $item['details_amount'] ) {
			$tranasction_symbol = '-';
		}
		switch ( $column_name ) {

			case 'user_id':
				return '<b>' . $item['user_id'] . '</b>';
			case 'user_name':
				return '<b>' . $item['user_name'] . '</b>';
			case 'user_email':
				return '<b>' . $item['user_email'] . '</b>';
			case 'user_amount':
				return '<b class="wps_wallet_' . esc_attr( $item['details_amount'] ) . '">' . $tranasction_symbol . wc_price( $item['user_amount'] ) . '</b>';
			case 'payment_method':
				return '<b>' . $item['payment_method'] . '</b>';
			case 'details':
				return '<b>' . ( html_entity_decode( $item['details'] ) ) . '</b>';
			case 'transaction_id':
				return '<b>' . $item['transaction_id'] . '</b>';
			case 'date':
				return '<b>' . $item['date'] . '</b>';
			case 'action_user_trasaction':
				$is_pro = false;
				$is_pro = apply_filters( 'wsfw_check_pro_plugin', $is_pro );
				if ( ! $is_pro ) {

					return '<span class="wps_wallet_delete_action wps_pro_settings " >&nbsp&nbsp&nbsp' . esc_html__( 'Delete', 'wallet-system-for-woocommerce' ) . '</span>';

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
					'action_user_trasaction'         => '',
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
		$limit_for_transaction = '10';

		if ( isset( $_POST['hidden_transaction_number'] ) || isset( $_POST['hidden_from_date'] ) ) {
			$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce ) ) {
				return false;
			}
		}

		if ( isset( $_POST['hidden_transaction_number'] ) && ! empty( $_POST['hidden_transaction_number'] ) ) {
			$limit_for_transaction      = ( isset( $_POST['hidden_transaction_number'] ) ) ? sanitize_text_field( wp_unslash( $_POST['hidden_transaction_number'] ) ) : '';
		}
		if ( ! empty( $limit_for_transaction ) ) {
			$per_page = $limit_for_transaction;
		}

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

		$secure_nonce      = wp_create_nonce( 'wps-wallet-list-table-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-wallet-list-table-nonce' );
		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'wallet-system-for-woocommerce' ) );
		}

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
		$per_page = 10;  // Number of rows per page.
		$offset = ( $current_page - 1 ) * $per_page;// Calculate the offset.
		$results = '';

		if ( isset( $_POST['hidden_transaction_number'] ) && ! empty( $_POST['hidden_transaction_number'] ) ) {
			$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce ) ) {
				return false;
			}
			if ( isset( $_POST['hidden_transaction_number'] ) && ! empty( $_POST['hidden_transaction_number'] ) ) {
				$per_page      = ( isset( $_POST['hidden_transaction_number'] ) ) ? sanitize_text_field( wp_unslash( $_POST['hidden_transaction_number'] ) ) : '10';
			}
			// SQL query.
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT *
				FROM {$wpdb->prefix}wps_wsfw_wallet_transaction  ORDER BY id DESC
				LIMIT %d OFFSET %d",
					$per_page,
					$offset
				),
				ARRAY_A
			);
		} elseif ( isset( $_POST['hidden_from_date'] ) && ! empty( $_POST['hidden_from_date'] ) ) {
			$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce ) ) {
				return false;
			}

			$date_from = ! empty( $_POST['hidden_from_date'] ) ? sanitize_text_field( wp_unslash( $_POST['hidden_from_date'] ) ) : '';
			$date_to   = ! empty( $_POST['hidden_to_date'] ) ? sanitize_text_field( wp_unslash( $_POST['hidden_to_date'] ) ) : '';
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT *
				FROM {$wpdb->prefix}wps_wsfw_wallet_transaction table1 JOIN {$wpdb->prefix}users table2 on table1.`user_id` =  table2.`ID` WHERE 
				table1.date BETWEEN %s AND %s
				ORDER BY table1.id DESC
				LIMIT %d OFFSET %d",
					$date_from . ' 00:00:00',
					$date_to . ' 23:59:59',
					$per_page,
					$offset
				),
				ARRAY_A
			);

		} elseif ( isset( $_REQUEST['s'] ) ) {
			$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce ) ) {
				return false;
			}
			$wps_request_search = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			$wps_request_search = '%' . $wps_request_search . '%';
			// SQL query.
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT *
				FROM {$wpdb->prefix}wps_wsfw_wallet_transaction table1 JOIN {$wpdb->prefix}users table2 on table1.`user_id` =  table2.`ID` WHERE (table1.`id` LIKE %s	 OR
				`user_id` LIKE %s OR
				`amount` LIKE %s OR
				`currency` LIKE %s OR
				`transaction_type` LIKE %s OR
				`payment_method` LIKE %s OR
				`note` LIKE %s OR
				`user_email` LIKE %s OR
				`display_name` LIKE %s OR
				`transaction_type_1` LIKE %s)
				ORDER BY table1.id DESC
				LIMIT %d OFFSET %d",
					$wps_request_search,
					$wps_request_search,
					$wps_request_search,
					$wps_request_search,
					$wps_request_search,
					$wps_request_search,
					$wps_request_search,
					$wps_request_search,
					$wps_request_search,
					$wps_request_search,
					$per_page,
					$offset
				),
				ARRAY_A
			);

		} else {

			// SQL query.
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT *
				FROM {$wpdb->prefix}wps_wsfw_wallet_transaction  ORDER BY id DESC
				LIMIT %d OFFSET %d",
					$per_page,
					$offset
				),
				ARRAY_A
			);

		}

		return $results;
	}
}
$date_from = '';
$date_to = '';



if ( isset( $_POST['action'] ) ) {
	$current_page  = 1;
	$reset_status  = '';
	$get_count = 10;
	$result = '';
	$update = false;
	// SQL query.
	global $wpdb;
	$transaction_count = $wpdb->get_results(
		"SELECT count(id) as transaction_count
			FROM {$wpdb->prefix}wps_wsfw_wallet_transaction",
	);

	if ( ! empty( $transaction_count ) ) {
		$transaction_count = $transaction_count[0];
		$transaction_count = $transaction_count->transaction_count;
	}


	if ( $transaction_count > $get_count ) {

		$get_count = $get_count;
		$loop_count = round( $transaction_count / $get_count ) + 1;
	} else {
		$get_count = $transaction_count;
		$loop_count = 1;
	}


	$data = array(
		'per_user_left'     => '',
		'csv_data'     => '',
	);
	if ( $loop_count > 0 ) {
		$index = 1;
		for ( $i = 0; $i <= $loop_count; $i++ ) {
			$user_count = intval( $i * 10 );
			if ( intval( $transaction_count ) >= intval( $user_count ) ) {
				$data = export_data_csv_for_all_transaction( $user_count, $transaction_count, $data['csv_data'] );
				$result  = false;
			} else {
				$result  = true;
			}
			$index ++;
		}
	}
	if ( 'export_csv' == $_POST['action'] ) {
		if ( $result ) {
			if ( ! empty( $data ) ) {
				$csv_data = $data['csv_data'];

				// Create a file pointer.
				$file = fopen( 'Transaction_Data.csv', 'w' );



				// Write data to the CSV file.
				foreach ( $csv_data as $row ) {
					$row_data = array();
					foreach ( $row as $key => $value ) {

						array_push( $row_data, strip_tags( $value ) );
					}
					fputcsv( $file, $row_data );

				}
				// Close the file pointer.
				fclose( $file );
				// Output a download link for the generated CSV file.
				echo '<a href="Transaction_Data.csv" id="transaction_data_csv_file" style="display:none"  download>Download Transaction CSV Data </a>';
				?>
				<script>
					
					const myAnchor = document.getElementById('transaction_data_csv_file');
					myAnchor.click();
				</script>
				<?php
			}
		}
	}
}

/**
 * Download all transaction into csv.
 *
 * @param [type] $user_count is the number of user.
 * @param [type] $current_page is the current page number.
 * @param string $csv_data is the csv data.
 * @return array
 */
function export_data_csv_for_all_transaction( $user_count, $current_page, $csv_data = '' ) {
	$args['number'] = $user_count;

	$limit = 10;
	$offset = $user_count;
	global $wpdb;
	$results_transaction = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT *
		FROM {$wpdb->prefix}wps_wsfw_wallet_transaction table1 JOIN {$wpdb->prefix}users table2 on table1.`user_id` =  table2.`ID`
		ORDER BY table1.id DESC
		LIMIT %d OFFSET %d",
			$limit,
			$offset
		),
		ARRAY_A
	);

	$zsdsd = array();
	if ( 0 == $user_count ) {
		$zsdsd[] = array( 'User Id', 'User Name', 'User Email', 'Amount', 'Transaction Type', 'Payment Method', 'Transaction Id' );
	}

	if ( ! empty( $results_transaction ) ) {
		foreach ( $results_transaction as $sort_id ) {

			$user          = get_userdata( $sort_id['user_id'] );
			$date = date_create( $sort_id['date'] );
			$transaction_data = esc_html( $date->getTimestamp() . $sort_id['id'] );
			$zsdsd[] = array( $sort_id['user_id'], $user->display_name, $user->user_email, $sort_id['amount'], html_entity_decode( $sort_id['transaction_type'] ), $sort_id['payment_method'], $transaction_data );
		}
	}

	if ( ! empty( $csv_data ) ) {
		$user_data_array  = array_merge( $csv_data, $zsdsd );
	} else {
		$user_data_array  = $zsdsd;
	}
		$data = array(
			'per_user_left'     => $user_count,
			'csv_data'     => $user_data_array,
		);
		return $data;
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
	<?php
	$limit_for_transaction = '10';
	if ( isset( $_POST['hidden_transaction_number'] ) && ! empty( $_POST['hidden_transaction_number'] ) ) {

		$nonce = ( isset( $_POST['updatenoncewallet_creation'] ) ) ? sanitize_text_field( wp_unslash( $_POST['updatenoncewallet_creation'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce ) ) {
			return false;
		}
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
							<td>
								<input type="text" id="fromdate_transaction" name="min"  data="min"  name="event_date" placeholder="<?php esc_html_e( 'Select From Date', 'wallet-system-for-woocommerce' ); ?>" value="<?php echo esc_attr( $date_from ); ?>" >
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" id="todate_transaction" name="max" data="max"  name="event_date" placeholder="<?php esc_html_e( 'Select To Date', 'wallet-system-for-woocommerce' ); ?>" value="<?php echo esc_attr( $date_to ); ?>" >
							</td>
						</tr>
						<tr>
							<td><span id="clear_table" class="btn button"><?php esc_html_e( 'Clear', 'wallet-system-for-woocommerce' ); ?></span></td>
						</tr>
					</tbody>
				</table>
		</form>
	</div>
	<div class="dataTables_length_wallet_custom_search_table">
	<form method="GET" class="wps_form_get_export_pdf">
		<input type="submit" class="btn button" name= "wps_wsfw_export_pdf" id="wps_wsfw_export_pdf" value="<?php esc_html_e( 'Export PDF', 'wallet-system-for-woocommerce' ); ?>">
		<?php
		$is_pro_plugin = false;
		$is_pro_plugin = apply_filters( 'wsfw_check_pro_plugin', $is_pro_plugin );

		if ( $is_pro_plugin ) {
			?>
				<input type="button" class="btn button" name= "wps_wsfw_export_csv" id="wps_wsfw_export_csv" value="<?php esc_html_e( 'Export CSV', 'wallet-system-for-woocommerce' ); ?>">
			<?php
		} else {
			?>
			<span class="button btn wps_demo_csv_button wps_pro_settings wps_pro_settings_tag" >&nbsp&nbsp&nbsp&nbsp<?php esc_html_e( 'Export CSV', 'wallet-system-for-woocommerce' ); ?></span>
			
			<?php
		}
		?>
		<input type="hidden" id="updatenoncewallet_pdf_dwnload" name="updatenoncewallet_pdf_dwnload" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
		
	</form>
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
