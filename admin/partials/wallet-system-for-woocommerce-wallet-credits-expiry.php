<?php
/**
 * Wallet Credits with Expiry Table View
 *
 * Displays all wallet credits with expiry information
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wsfw_wps_wsfw_obj;

// Check if rechargeable addon is active
if ( ! function_exists( 'wps_wsfwrpa_get_ledger_table' ) ) {
	?>
	<div class="notice notice-warning">
		<p><?php esc_html_e( 'Rechargeable Products Addon is required to view wallet credits with expiry data.', 'wallet-system-for-woocommerce' ); ?></p>
	</div>
	<?php
	return;
}

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Wallet Credits with Expiry', 'wallet-system-for-woocommerce' ); ?></h1>
	<p class="description">
		<?php esc_html_e( 'View all wallet credits with expiry information including bonus amounts and expiration dates.', 'wallet-system-for-woocommerce' ); ?>
	</p>

	<div class="wps-wpg-gen-section-table-wrap">

	<?php
	if ( ! class_exists( 'WP_List_Table' ) ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}

	/**
	 * Wallet Credits Expiry Table Class
	 */
	class Wallet_Credits_Expiry_Table extends WP_List_Table {

		/**
		 * Prepare the items for the table to process.
		 *
		 * @return void
		 */
		public function prepare_items() {
			$per_page     = 20;
			$columns      = $this->get_columns();
			$current_page = $this->get_pagenum();
			$data         = $this->table_data( $current_page, $per_page );
			$total_items  = $this->get_total_records();

			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
				)
			);

			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->items           = $data;
		}

		/**
		 * Get total number of records.
		 *
		 * @return int
		 */
		public function get_total_records() {
			global $wpdb;
			$table = wps_wsfwrpa_get_ledger_table();

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );

			return $count;
		}

		/**
		 * Get columns.
		 *
		 * @return array
		 */
		public function get_columns() {
			$columns = array(
				'id'                => esc_html__( 'ID', 'wallet-system-for-woocommerce' ),
				'user_info'         => esc_html__( 'User', 'wallet-system-for-woocommerce' ),
				'amount'            => esc_html__( 'Amount', 'wallet-system-for-woocommerce' ),
				'deduct_amount'     => esc_html__( 'Deduct Amount', 'wallet-system-for-woocommerce' ),
				'bonus_amount'      => esc_html__( 'Bonus Amount', 'wallet-system-for-woocommerce' ),
				'currency'          => esc_html__( 'Currency', 'wallet-system-for-woocommerce' ),
				'credited_date'     => esc_html__( 'Credited Date', 'wallet-system-for-woocommerce' ),
				'expiry_date'       => esc_html__( 'Expiry Date', 'wallet-system-for-woocommerce' ),
				'status'            => esc_html__( 'Status', 'wallet-system-for-woocommerce' ),
				'order_id'          => esc_html__( 'Order ID', 'wallet-system-for-woocommerce' ),
			);
			return $columns;
		}

		/**
		 * Get sortable columns.
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			return array(
				'id'            => array( 'id', false ),
				'user_info'     => array( 'user_id', false ),
				'amount'        => array( 'amount', false ),
				'credited_date' => array( 'credited_date', false ),
				'expiry_date'   => array( 'expiry_date', false ),
				'status'        => array( 'status', false ),
			);
		}

		/**
		 * Get table data.
		 *
		 * @param int $current_page Current page number.
		 * @param int $per_page     Items per page.
		 * @return array
		 */
		public function table_data( $current_page, $per_page ) {
			global $wpdb;
			$table = wps_wsfwrpa_get_ledger_table();

			$orderby = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'id';
			$order   = isset( $_REQUEST['order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'DESC';

			$offset = ( $current_page - 1 ) * $per_page;

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d",
					$per_page,
					$offset
				)
			);

			$data = array();
			if ( ! empty( $results ) ) {
				foreach ( $results as $row ) {
					$user = get_user_by( 'id', $row->user_id );

					$data[] = array(
						'id'            => $row->id,
						'user_info'     => $user ? $user->user_email : 'User #' . $row->user_id,
						'user_id'       => $row->user_id,
						'amount'        => $row->amount,
						'deduct_amount' => $row->deduct_amount,
						'bonus_amount'  => $row->deduct_amount,
						'currency'      => $row->currency,
						'credited_date' => $row->credited_date,
						'expiry_date'   => $row->expiry_date,
						'status'        => $row->status,
						'order_id'      => $row->order_id,
						'expired_date'  => isset( $row->expired_date ) ? $row->expired_date : null,
					);
				}
			}

			return $data;
		}

		/**
		 * Default column display.
		 *
		 * @param array  $item        Item data.
		 * @param string $column_name Column name.
		 * @return string
		 */
		public function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'id':
					return '<strong>#' . esc_html( $item['id'] ) . '</strong>';

				case 'user_info':
					$user = get_user_by( 'id', $item['user_id'] );
					if ( $user ) {
						return '<strong>' . esc_html( $user->display_name ) . '</strong><br>' .
						       '<small>' . esc_html( $user->user_email ) . '</small>';
					}
					return 'User #' . $item['user_id'];

				case 'amount':
					return wc_price( $item['amount'], array( 'currency' => $item['currency'] ) );

				case 'deduct_amount':
					return wc_price( $item['deduct_amount'], array( 'currency' => $item['currency'] ) );

				case 'bonus_amount':
					$bonus = $item['bonus_amount'];
					if ( $bonus > 0 ) {
						return '<span style="color: #2ea44f; font-weight: 600;">' .
						       wc_price( $bonus, array( 'currency' => $item['currency'] ) ) .
						       '</span>';
					}
					return '<span style="color: #999;">—</span>';

				case 'currency':
					return '<code>' . esc_html( $item['currency'] ) . '</code>';

				case 'credited_date':
					return $this->format_date( $item['credited_date'] );

				case 'expiry_date':
					if ( ! $item['expiry_date'] || 'NULL' === $item['expiry_date'] ) {
						return '<span style="color: #999;">Never</span>';
					}

					$expiry_time = strtotime( $item['expiry_date'] );
					$now         = current_time( 'timestamp' );

					if ( 'expired' === $item['status'] ) {
						return '<span style="color: #dc2626; font-weight: 600;">' .
						       $this->format_date( $item['expiry_date'] ) .
						       '</span><br><small style="color: #dc2626;">Expired</small>';
					} elseif ( $expiry_time < $now ) {
						return '<span style="color: #f59e0b; font-weight: 600;">' .
						       $this->format_date( $item['expiry_date'] ) .
						       '</span><br><small style="color: #f59e0b;">Pending Expiry</small>';
					} else {
						$days_left = floor( ( $expiry_time - $now ) / DAY_IN_SECONDS );
						return $this->format_date( $item['expiry_date'] ) .
						       '<br><small style="color: #2ea44f;">' . $days_left . ' days left</small>';
					}

				case 'status':
					return $this->get_status_badge( $item['status'] );

				case 'order_id':
					if ( $item['order_id'] > 0 ) {
						$order_url = admin_url( 'post.php?post=' . $item['order_id'] . '&action=edit' );
						return '<a href="' . esc_url( $order_url ) . '" target="_blank">#' . esc_html( $item['order_id'] ) . '</a>';
					}
					return '<span style="color: #999;">Manual Credit</span>';

				default:
					return isset( $item[ $column_name ] ) ? esc_html( $item[ $column_name ] ) : '';
			}
		}

		/**
		 * Format date for display.
		 *
		 * @param string $date Date string.
		 * @return string
		 */
		private function format_date( $date ) {
			if ( ! $date || 'NULL' === $date ) {
				return '—';
			}
			return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $date ) );
		}

		/**
		 * Get status badge HTML.
		 *
		 * @param string $status Status.
		 * @return string
		 */
		private function get_status_badge( $status ) {
			$badges = array(
				'active'  => '<span class="wps-status-badge wps-status-active">Active</span>',
				'expired' => '<span class="wps-status-badge wps-status-expired">Expired</span>',
				'voided'  => '<span class="wps-status-badge wps-status-voided">Voided</span>',
			);

			return isset( $badges[ $status ] ) ? $badges[ $status ] : '<span class="wps-status-badge">' . esc_html( ucfirst( $status ) ) . '</span>';
		}
	}

	$wallet_credits_table = new Wallet_Credits_Expiry_Table();
	$wallet_credits_table->prepare_items();
	?>

	<style>
		.wps-wpg-gen-section-table-wrap {
			background: #fff;
			padding: 20px;
			margin-top: 20px;
			border: 1px solid #ccd0d4;
			box-shadow: 0 1px 1px rgba(0,0,0,.04);
		}
		.wps-status-badge {
			display: inline-block;
			padding: 4px 12px;
			border-radius: 12px;
			font-size: 12px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		.wps-status-active {
			background: #d1fae5;
			color: #065f46;
		}
		.wps-status-expired {
			background: #fee2e2;
			color: #991b1b;
		}
		.wps-status-voided {
			background: #f3f4f6;
			color: #6b7280;
		}
		.wp-list-table th {
			font-weight: 600;
		}
		.wp-list-table td {
			vertical-align: middle !important;
		}
		.tablenav {
			margin-top: 15px;
		}
	</style>

	<form method="post">
		<?php
		$wallet_credits_table->display();
		?>
	</form>

	</div><!-- .wps-wpg-gen-section-table-wrap -->
</div><!-- .wrap -->
