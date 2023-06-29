



<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Extending Wp_List_Table class to create segment table.
 */
class Wallet_User_Table extends WP_List_Table {

	/**
	 * Prepare the items for the table to process.
	 *
	 * @return void
	 */
	public function prepare_items() {
		global $wpdb;
		$per_page     = 10;
		$columns      = $this->get_columns();
		$current_page = $this->get_pagenum();
		$data         = $this->table_data( $current_page, $per_page );
		$transactions = $wpdb->get_results( 'SELECT  count(id) as total  FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction' );
				
		if ( ! empty($transactions) ) {
			$total_items  = $transactions[0]->total;
		}
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;
	}

	/**
	 * This function is used to get columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
		
			'id'       => esc_html__( 'ID', 'wallet-system-for-woocommerce' ),
			'name'     => esc_html__( 'Name', 'wallet-system-for-woocommerce' ),
			'email'    => esc_html__( 'Email', 'wallet-system-for-woocommerce' ),
			'role'     => esc_html__( 'Role', 'wallet-system-for-woocommerce' ),
			'amount'   => esc_html__( 'Amount', 'wallet-system-for-woocommerce' ),
			'payment_method'   => esc_html__( 'Payment Method', 'wallet-system-for-woocommerce' ),
			'details'   => esc_html__( 'Details', 'wallet-system-for-woocommerce' ),
			'transaction_id'   => esc_html__( 'Transaction ID', 'wallet-system-for-woocommerce' ),
			'date' => esc_html__( 'Date', 'wallet-system-for-woocommerce' ),
		);
		return $columns;
	}

	/**
	 * This function is used to filter product.
	 *
	 * @return array
	 */
	public function table_data( $current_page, $per_page ) {

		$args = array(
			'number' => $per_page,
			'offset' => ( $current_page - 1 ) * $per_page,
			'fields' => 'ID',
		);

		if ( isset( $_REQUEST['s'] ) ) {
			$wps_request_search = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			$args['search']     = '*' . $wps_request_search . '*';
		}
		global $wpdb;
	
		$transactions = $wpdb->get_results( 'SELECT  count(id) as total  FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction' );
				
		if ( ! empty($transactions) ) {
			$total_items  = $transactions[0]->total;
		}

		if ( ! empty( $current_page ) ) {
			$min =  ($current_page-1)*10;
			$max =  $current_page*10;
		}


		global $wpdb;
		$table_name   = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
		$transactions = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wps_wsfw_wallet_transaction WHERE id >= '.$min.' AND id <= '.$max.' ORDER BY id LIMIT 100 ' );
		
		
		if ( ! empty( $transactions ) ) {
			foreach ( $transactions as $transaction ) {
				$user = get_user_by( 'id', $transaction->user_id );
				$tranasction_symbol = '';
				if ( 'credit' == $transaction->transaction_type_1 ) {
					$tranasction_symbol = '+';
				} elseif ( 'debit' == $transaction->transaction_type_1 ) {
					$tranasction_symbol = '-';
				}
				$x      = array(
					'id'       => $this->wsfw_get_id( $user ),
					'name'     => $this->wsfw_get_name( $user ),
					'email'    => $this->wsfw_get_email( $user ),
					'role'     => $this->wsfw_get_role( $user ),
					'amount'   => $this->wsfw_get_amount( $transaction ),
					'payment_method'   => $this->wsfw_get_payment_method( $transaction ),
					'details' => $this->wsfw_get_details( $transaction ),
					'transaction_id' => $this->wsfw_get_transaction_id( $transaction ),
					'date' => $this->wsfw_get_date( $transaction ),
				);
				$data[] = $x;
			}
		}
		return $data;
	}

	/**
	 * This function is used to show checkbox.
	 *
	 * @param int $item item id.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" onclick="set_checked_value(this)" id="wps_wallet_ids[]" name="wps_wallet_ids[]" value="%s" />',
			$item['id']
		);
	}

	/**
	 * This function is used to show columns.
	 *
	 * @param string $item item.
	 * @param string $column_name column name.
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		return sprintf( $item[ $column_name ], true );
	}

	/**
	 * Show user id.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_id( $user ) {
		return $user->ID;
	}

	/**
	 * This function is used to show user name.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_name( $user ) {
		if ( $user ) {
			$display_name = $user->display_name;
			
		} else {
			$display_name = '';
		
		}
		$display_name =! empty( esc_html( $display_name ) ) ? esc_html( $display_name ) : 'Guest#(' . esc_html( $user->user_id );
		return $display_name;
	}

	/**
	 * This function is used to show user email.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_email( $user ) {
		return $user->user_email;
	}

	/**
	 * This functions is used to show user role.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_role( $user ) {
		return ! empty( $user->roles[0] ) ? $user->roles[0] : '-';
	}

	/**
	 * This functions is used to show user role.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_transaction_id( $transaction ) {
		esc_html( $transaction->id );
		$date = date_create( $transaction->date );
		$transaction_data = esc_html( $date->getTimestamp() . $transaction->id );
		return $transaction_data;
	}


	/**
	 * This functions is used to show user role.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_date( $transaction ) {
		$date_data = '';
		$date_format = get_option( 'date_format', 'm/d/Y' );
		$date = date_create( $transaction->date );

					$wps_wsfw_time_zone = get_option( 'timezone_string' );
					if ( ! empty( $wps_wsfw_time_zone ) ) {
						$date = date_create( $transaction->date );
						echo esc_html( date_format( $date, $date_format ) );
						// extra code.( need validation if require).
						$date->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );
						// extra code.
						$date_data = ' ' . esc_html( date_format( $date, 'H:i:s' ) );
					} else {

						$date_data = esc_html( date_format( $date, $date_format ) );
						$date_data .= ' ' . esc_html( date_format( $date, 'H:i:s' ) );
					}
		return $date_data;
	}


	/**
	 * This function ia used to show user wallet amount.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_amount( $transaction ) {
		$tranasction_symbol = '';
		if ( 'credit' == $transaction->transaction_type_1 ) {
			$tranasction_symbol = '+';
		} elseif ( 'debit' == $transaction->transaction_type_1 ) {
			$tranasction_symbol = '-';
		}
		$amount = esc_html( $tranasction_symbol ) . wp_kses_post( wc_price( $transaction->amount, array( 'currency' => $transaction->currency ) ) );
		
		return $amount;
	}

	/**
	 * This function is to edit user wallet and show transactions.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_payment_method( $transaction ) {
		$data = wp_kses_post( $transaction->payment_method ); 
		return $data;
	}

	/**
	 * This function is used to restrict user.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_details( $transaction ) {
		$html= wp_kses_post( html_entity_decode( $transaction->transaction_type ) );
		return $html;
	}

}
?>
<form method="post">
	<?php
		$wallet_user_table = new Wallet_User_Table();
		$wallet_user_table->prepare_items();
		$wallet_user_table->search_box( __( 'Search', 'wallet-system-for-woocommerce' ), 'search_id' );
		$wallet_user_table->display();
	?>
</form>
</div>
