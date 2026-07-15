<?php
/**
 * POS admin screen: register sessions, and recent POS sales.
 *
 * Registered as its own "POS Registers" entry under the existing shared
 * WP Swings menu, via the same wps_add_plugins_menus_array filter this
 * plugin already uses for its own "Wallet System" entry — additive only,
 * the existing dashboard page/file is untouched.
 *
 * @link       https://wpswings.com/
 * @since      2.8.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the POS Registers admin page.
 */
class Wallet_System_For_Woocommerce_Pos_Admin {

	const PER_PAGE = 20;

	/**
	 * Appends the POS Registers entry to the shared WP Swings submenu.
	 *
	 * @param array $menus Existing menu entries.
	 * @return array
	 */
	public static function add_menu( $menus = array() ) {
		$menus[] = array(
			'name'      => __( 'POS Registers', 'wallet-system-for-woocommerce' ),
			'slug'      => 'wsfw_pos_registers_menu',
			'menu_link' => 'wsfw_pos_registers_menu',
			'instance'  => __CLASS__,
			'function'  => 'render_page',
		);

		return $menus;
	}

	/**
	 * Renders the page: the POS Terminal enable/disable control, then two
	 * tabs — Recent POS Sales and Recent Register Sessions — each with its
	 * own search filter and pagination.
	 *
	 * @return void
	 */
	public static function render_page() {
		global $wpdb;
		$registers_table    = $wpdb->prefix . 'wps_wsfw_pos_registers';
		$sessions_table     = $wpdb->prefix . 'wps_wsfw_pos_register_sessions';
		$transactions_table = $wpdb->prefix . 'wps_wsfw_wallet_transaction';

		if ( isset( $_POST['wsfw_pos_force_close_session'] ) && current_user_can( 'manage_options' )
			&& check_admin_referer( 'wsfw_pos_force_close' ) ) {
			self::force_close_session( absint( $_POST['session_id'] ), $sessions_table, $registers_table );
		}

		if ( isset( $_POST['wsfw_pos_toggle_enabled'] ) && current_user_can( 'manage_options' )
			&& check_admin_referer( 'wsfw_pos_toggle_enabled' ) ) {
			if ( Wallet_System_For_Woocommerce_Pos_Page::is_enabled() ) {
				Wallet_System_For_Woocommerce_Pos_Page::disable();
			} else {
				Wallet_System_For_Woocommerce_Pos_Page::enable();
			}
		}

		$pos_enabled = Wallet_System_For_Woocommerce_Pos_Page::is_enabled();
		$pos_page_id = get_option( Wallet_System_For_Woocommerce_Pos_Page::OPTION_PAGE_ID );
		$active_tab  = isset( $_GET['tab'] ) && 'sessions' === $_GET['tab'] ? 'sessions' : 'sales'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page_slug   = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'wsfw_pos_registers_menu'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		self::render_styles();
		?>
		<div class="wrap wsfw-pos-admin">
			<div class="wsfw-pos-hero">
				<h1><?php esc_html_e( 'POS Registers', 'wallet-system-for-woocommerce' ); ?></h1>
				<p class="wsfw-pos-hero-subtitle"><?php esc_html_e( 'Manage your in-store POS terminal, and review sales and register activity.', 'wallet-system-for-woocommerce' ); ?></p>
			</div>

			<div class="wsfw-pos-panel">
				<h2><?php esc_html_e( 'POS Terminal', 'wallet-system-for-woocommerce' ); ?></h2>
				<p class="wsfw-pos-terminal-status">
					<?php if ( $pos_enabled && $pos_page_id ) : ?>
						<span class="wsfw-pos-badge wsfw-pos-badge-open"><?php esc_html_e( 'Enabled', 'wallet-system-for-woocommerce' ); ?></span>
						<?php esc_html_e( 'Terminal URL:', 'wallet-system-for-woocommerce' ); ?>
						<a href="<?php echo esc_url( get_permalink( $pos_page_id ) ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( get_permalink( $pos_page_id ) ); ?>
						</a>
					<?php else : ?>
						<span class="wsfw-pos-badge wsfw-pos-badge-closed"><?php esc_html_e( 'Disabled', 'wallet-system-for-woocommerce' ); ?></span>
					<?php endif; ?>
				</p>
				<form method="post">
					<?php wp_nonce_field( 'wsfw_pos_toggle_enabled' ); ?>
					<button type="submit" name="wsfw_pos_toggle_enabled" class="wsfw-pos-btn <?php echo esc_attr( $pos_enabled ? 'wsfw-pos-btn-outline' : 'wsfw-pos-btn-filled' ); ?>">
						<?php echo esc_html( $pos_enabled ? __( 'Disable POS Terminal', 'wallet-system-for-woocommerce' ) : __( 'Enable POS Terminal', 'wallet-system-for-woocommerce' ) ); ?>
					</button>
				</form>
			</div>

			<div class="wsfw-pos-panel wsfw-pos-panel-tabbed">
				<div class="wsfw-pos-tabs">
					<a href="<?php echo esc_url( add_query_arg( array( 'page' => $page_slug, 'tab' => 'sales' ), admin_url( 'admin.php' ) ) ); ?>" class="wsfw-pos-tab <?php echo 'sales' === $active_tab ? 'wsfw-pos-tab-active' : ''; ?>">
						<?php esc_html_e( 'Recent POS Sales', 'wallet-system-for-woocommerce' ); ?>
					</a>
					<a href="<?php echo esc_url( add_query_arg( array( 'page' => $page_slug, 'tab' => 'sessions' ), admin_url( 'admin.php' ) ) ); ?>" class="wsfw-pos-tab <?php echo 'sessions' === $active_tab ? 'wsfw-pos-tab-active' : ''; ?>">
						<?php esc_html_e( 'Recent Register Sessions', 'wallet-system-for-woocommerce' ); ?>
					</a>
				</div>

				<?php if ( 'sessions' === $active_tab ) : ?>
					<?php self::render_sessions_tab( $sessions_table, $registers_table, $page_slug ); ?>
				<?php else : ?>
					<?php self::render_sales_tab( $transactions_table, $page_slug ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders the Recent POS Sales tab: customer search + paginated table.
	 *
	 * @param string $transactions_table Wallet transaction table name.
	 * @param string $page_slug          Current admin page slug, for building URLs.
	 * @return void
	 */
	private static function render_sales_tab( $transactions_table, $page_slug ) {
		global $wpdb;
		$users_table = $wpdb->users;

		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$paged  = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$where  = "WHERE t.source = 'pos'";
		$params = array();

		if ( '' !== $search ) {
			$like     = '%' . $wpdb->esc_like( $search ) . '%';
			$where   .= ' AND (u.display_name LIKE %s OR u.user_email LIKE %s)';
			$params[] = $like;
			$params[] = $like;
		}

		$count_sql = "SELECT COUNT(*) FROM {$transactions_table} t LEFT JOIN {$users_table} u ON u.ID = t.user_id {$where}";
		$total     = (int) ( $params ? $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) ) : $wpdb->get_var( $count_sql ) );

		$offset      = ( $paged - 1 ) * self::PER_PAGE;
		$data_sql    = "SELECT t.* FROM {$transactions_table} t LEFT JOIN {$users_table} u ON u.ID = t.user_id {$where} ORDER BY t.id DESC LIMIT %d OFFSET %d";
		$data_params = array_merge( $params, array( self::PER_PAGE, $offset ) );
		$transactions = $wpdb->get_results( $wpdb->prepare( $data_sql, $data_params ) );

		?>
		<form method="get" class="wsfw-pos-filter-form">
			<input type="hidden" name="page" value="<?php echo esc_attr( $page_slug ); ?>" />
			<input type="hidden" name="tab" value="sales" />
			<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search by customer name or email…', 'wallet-system-for-woocommerce' ); ?>" />
			<button type="submit" class="wsfw-pos-btn wsfw-pos-btn-filled wsfw-pos-btn-sm"><?php esc_html_e( 'Search', 'wallet-system-for-woocommerce' ); ?></button>
			<?php if ( '' !== $search ) : ?>
				<a class="button-link wsfw-pos-clear" href="<?php echo esc_url( add_query_arg( array( 'page' => $page_slug, 'tab' => 'sales' ), admin_url( 'admin.php' ) ) ); ?>">
					<?php esc_html_e( 'Clear', 'wallet-system-for-woocommerce' ); ?>
				</a>
			<?php endif; ?>
		</form>

		<table class="widefat striped wsfw-pos-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Customer', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Type', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Order', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Register Session', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( empty( $transactions ) ) : ?>
				<tr><td colspan="7"><?php esc_html_e( 'No POS transactions found.', 'wallet-system-for-woocommerce' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $transactions as $txn ) : ?>
					<?php
					$txn_user     = get_user_by( 'id', $txn->user_id );
					$linked_order = ! empty( $txn->transaction_id ) ? wc_get_order( $txn->transaction_id ) : false;
					?>
					<tr>
						<td><?php echo esc_html( $txn->id ); ?></td>
						<td><?php echo esc_html( $txn_user ? $txn_user->display_name : $txn->user_id ); ?></td>
						<td><?php echo esc_html( $txn->currency . ' ' . $txn->amount ); ?></td>
						<td>
							<span class="wsfw-pos-badge wsfw-pos-badge-<?php echo esc_attr( $txn->transaction_type_1 ); ?>">
								<?php echo esc_html( ucfirst( $txn->transaction_type_1 ) ); ?>
							</span>
						</td>
						<td>
						<?php if ( $linked_order ) : ?>
							<a href="<?php echo esc_url( $linked_order->get_edit_order_url() ); ?>">#<?php echo esc_html( $txn->transaction_id ); ?></a>
						<?php else : ?>
							&#8212;
						<?php endif; ?>
						</td>
						<td><?php echo esc_html( $txn->register_session_id ? '#' . $txn->register_session_id : '—' ); ?></td>
						<td><?php echo esc_html( $txn->date ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>

		<?php self::render_pagination( $total, $paged ); ?>
		<?php
	}

	/**
	 * Renders the Recent Register Sessions tab: register/cashier search +
	 * paginated table with the force-close escape hatch.
	 *
	 * @param string $sessions_table  Sessions table name.
	 * @param string $registers_table Registers table name.
	 * @param string $page_slug       Current admin page slug, for building URLs.
	 * @return void
	 */
	private static function render_sessions_tab( $sessions_table, $registers_table, $page_slug ) {
		global $wpdb;
		$users_table = $wpdb->users;

		$register_search = isset( $_GET['register'] ) ? sanitize_text_field( wp_unslash( $_GET['register'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cashier_search   = isset( $_GET['cashier'] ) ? sanitize_text_field( wp_unslash( $_GET['cashier'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$paged            = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$where  = 'WHERE 1=1';
		$params = array();

		if ( '' !== $register_search ) {
			$where   .= ' AND r.register_name LIKE %s';
			$params[] = '%' . $wpdb->esc_like( $register_search ) . '%';
		}

		if ( '' !== $cashier_search ) {
			$where   .= ' AND u.display_name LIKE %s';
			$params[] = '%' . $wpdb->esc_like( $cashier_search ) . '%';
		}

		$joins     = "LEFT JOIN {$registers_table} r ON r.id = s.register_id LEFT JOIN {$users_table} u ON u.ID = s.cashier_id";
		$count_sql = "SELECT COUNT(*) FROM {$sessions_table} s {$joins} {$where}";
		$total     = (int) ( $params ? $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) ) : $wpdb->get_var( $count_sql ) );

		$offset      = ( $paged - 1 ) * self::PER_PAGE;
		$data_sql    = "SELECT s.*, r.register_name, u.display_name AS cashier_name FROM {$sessions_table} s {$joins} {$where} ORDER BY s.id DESC LIMIT %d OFFSET %d";
		$data_params = array_merge( $params, array( self::PER_PAGE, $offset ) );
		$sessions    = $wpdb->get_results( $wpdb->prepare( $data_sql, $data_params ) );

		?>
		<form method="get" class="wsfw-pos-filter-form">
			<input type="hidden" name="page" value="<?php echo esc_attr( $page_slug ); ?>" />
			<input type="hidden" name="tab" value="sessions" />
			<input type="search" name="register" value="<?php echo esc_attr( $register_search ); ?>" placeholder="<?php esc_attr_e( 'Search by register…', 'wallet-system-for-woocommerce' ); ?>" />
			<input type="search" name="cashier" value="<?php echo esc_attr( $cashier_search ); ?>" placeholder="<?php esc_attr_e( 'Search by cashier…', 'wallet-system-for-woocommerce' ); ?>" />
			<button type="submit" class="wsfw-pos-btn wsfw-pos-btn-filled wsfw-pos-btn-sm"><?php esc_html_e( 'Search', 'wallet-system-for-woocommerce' ); ?></button>
			<?php if ( '' !== $register_search || '' !== $cashier_search ) : ?>
				<a class="button-link wsfw-pos-clear" href="<?php echo esc_url( add_query_arg( array( 'page' => $page_slug, 'tab' => 'sessions' ), admin_url( 'admin.php' ) ) ); ?>">
					<?php esc_html_e( 'Clear', 'wallet-system-for-woocommerce' ); ?>
				</a>
			<?php endif; ?>
		</form>

		<table class="widefat striped wsfw-pos-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Session', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Register', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Cashier', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Cash Sales', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Wallet Sales', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Opened At', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Closed At', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Action', 'wallet-system-for-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( empty( $sessions ) ) : ?>
				<tr><td colspan="9"><?php esc_html_e( 'No sessions found.', 'wallet-system-for-woocommerce' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $sessions as $session ) : ?>
					<tr>
						<td>#<?php echo esc_html( $session->id ); ?></td>
						<td><?php echo esc_html( $session->register_name ? $session->register_name : '—' ); ?></td>
						<td><?php echo esc_html( $session->cashier_name ? $session->cashier_name : '—' ); ?></td>
						<td>
							<span class="wsfw-pos-badge wsfw-pos-badge-<?php echo esc_attr( $session->status ); ?>">
								<?php echo esc_html( ucfirst( $session->status ) ); ?>
							</span>
						</td>
						<td><?php echo esc_html( $session->cash_sales_total ); ?></td>
						<td><?php echo esc_html( $session->wallet_sales_total ); ?></td>
						<td><?php echo esc_html( $session->opened_at ); ?></td>
						<td><?php echo esc_html( $session->closed_at ? $session->closed_at : '—' ); ?></td>
						<td>
						<?php if ( 'open' === $session->status && current_user_can( 'manage_options' ) ) : ?>
							<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Force-close this session? Only do this if the terminal is stuck and never sent a close request.', 'wallet-system-for-woocommerce' ) ); ?>');">
								<?php wp_nonce_field( 'wsfw_pos_force_close' ); ?>
								<input type="hidden" name="session_id" value="<?php echo esc_attr( $session->id ); ?>" />
								<button type="submit" name="wsfw_pos_force_close_session" class="wsfw-pos-btn wsfw-pos-btn-outline wsfw-pos-btn-sm"><?php esc_html_e( 'Force Close', 'wallet-system-for-woocommerce' ); ?></button>
							</form>
						<?php else : ?>
							&#8212;
						<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>

		<?php self::render_pagination( $total, $paged ); ?>
		<?php
	}

	/**
	 * Renders WordPress-native pagination links for the current query args.
	 *
	 * @param int $total_items Total matching rows.
	 * @param int $current     Current page number.
	 * @return void
	 */
	private static function render_pagination( $total_items, $current ) {
		$total_pages = (int) ceil( $total_items / self::PER_PAGE );

		if ( $total_pages <= 1 ) {
			return;
		}

		echo '<div class="tablenav bottom"><div class="tablenav-pages">';
		echo '<span class="displaying-num">' . esc_html(
			/* translators: %d: total number of rows found */
			sprintf( _n( '%d item', '%d items', $total_items, 'wallet-system-for-woocommerce' ), $total_items )
		) . '</span>';
		echo wp_kses_post(
			paginate_links(
				array(
					'base'      => add_query_arg( 'paged', '%#%' ),
					'format'    => '',
					'current'   => $current,
					'total'     => $total_pages,
					'prev_text' => __( '&laquo;', 'wallet-system-for-woocommerce' ),
					'next_text' => __( '&raquo;', 'wallet-system-for-woocommerce' ),
				)
			)
		);
		echo '</div></div>';
	}

	/**
	 * Force-closes a stuck open session (admin escape hatch), mirroring the
	 * same close logic as the REST register/close endpoint.
	 *
	 * @param int    $session_id      Session id to close.
	 * @param string $sessions_table  Sessions table name.
	 * @param string $registers_table Registers table name.
	 * @return void
	 */
	private static function force_close_session( $session_id, $sessions_table, $registers_table ) {
		global $wpdb;

		$session = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$sessions_table} WHERE id = %d", $session_id ) );

		if ( ! $session || 'open' !== $session->status ) {
			return;
		}

		$now             = current_time( 'mysql' );
		$closing_balance = floatval( $session->opening_balance ) + floatval( $session->cash_sales_total );

		$wpdb->update(
			$sessions_table,
			array(
				'status'          => 'closed',
				'closed_at'       => $now,
				'closing_balance' => $closing_balance,
				'notes'           => __( 'Force-closed by admin.', 'wallet-system-for-woocommerce' ),
			),
			array( 'id' => $session_id )
		);

		$wpdb->update(
			$registers_table,
			array(
				'status'       => 'closed',
				'closed_at'    => $now,
				'closing_cash' => $closing_balance,
			),
			array( 'id' => $session->register_id )
		);

		echo '<div class="notice notice-success"><p>' . esc_html__( 'Session force-closed.', 'wallet-system-for-woocommerce' ) . '</p></div>';
	}

	/**
	 * Inline styles for this admin screen only — a dedicated stylesheet
	 * would be overkill for one page's polish.
	 *
	 * @return void
	 */
	private static function render_styles() {
		?>
		<style>
			/* Palette matches this plugin's own admin theme (Wallet System
			   dashboard) — purple/gold, not default WP admin blue/gray. */
			.wsfw-pos-admin { background: #f7f3ff; margin-right: 20px; padding: 20px 0 40px; }
			.wsfw-pos-admin h1,
			.wsfw-pos-admin h2 { color: #20143b; }
			.wsfw-pos-admin p,
			.wsfw-pos-admin .displaying-num { color: #76688f; }
			.wsfw-pos-admin a { color: #7c4bc2; }

			.wsfw-pos-hero { padding: 4px 4px 16px; }
			.wsfw-pos-hero h1 { font-size: 24px; margin-bottom: 4px; }
			.wsfw-pos-hero-subtitle { margin: 0; font-size: 14px; }

			.wsfw-pos-panel {
				background: #fff;
				border: 1px solid #eadffd;
				border-radius: 12px;
				box-shadow: 0 4px 16px rgba( 49, 23, 95, 0.08 );
				padding: 24px;
				margin-bottom: 20px;
			}
			.wsfw-pos-panel h2 { margin-top: 0; font-size: 16px; }
			.wsfw-pos-terminal-status { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

			.wsfw-pos-btn {
				display: inline-block;
				padding: 10px 20px;
				border-radius: 999px;
				font-size: 13px;
				font-weight: 600;
				border: 1px solid transparent;
				cursor: pointer;
				text-decoration: none;
				transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
			}
			.wsfw-pos-btn-sm { padding: 8px 16px; font-size: 12px; }
			.wsfw-pos-btn-filled { background: #050505; color: #fff; }
			.wsfw-pos-btn-filled:hover { background: #2d174f; color: #fff; }
			.wsfw-pos-btn-outline { background: #fff; color: #20143b; border-color: #eadffd; }
			.wsfw-pos-btn-outline:hover { background: #f7f3ff; border-color: #7c4bc2; }

			.wsfw-pos-tabs { display: flex; gap: 6px; border-bottom: 1px solid #eadffd; margin-bottom: 20px; padding-bottom: 0; }
			.wsfw-pos-tab {
				display: inline-block;
				padding: 10px 18px;
				border-radius: 999px 999px 0 0;
				font-size: 13px;
				font-weight: 600;
				color: #76688f;
				text-decoration: none;
				margin-bottom: -1px;
			}
			.wsfw-pos-tab:hover { color: #20143b; }
			.wsfw-pos-tab-active { background: #fff6df; color: #20143b; border: 1px solid #eadffd; border-bottom-color: #fff6df; }

			.wsfw-pos-filter-form { display: flex; align-items: center; gap: 8px; margin: 0 0 16px; flex-wrap: wrap; }
			.wsfw-pos-filter-form input[type="search"] {
				min-width: 240px;
				padding: 9px 14px;
				border: 1px solid #eadffd;
				border-radius: 999px;
				font-size: 13px;
			}
			.wsfw-pos-filter-form input[type="search"]:focus {
				outline: none;
				border-color: #7c4bc2;
				box-shadow: 0 0 0 3px rgba( 124, 75, 194, 0.12 );
			}
			.wsfw-pos-clear { margin-left: 4px; font-size: 13px; }

			.wsfw-pos-table { margin-top: 4px; border: 1px solid #eadffd; border-radius: 8px; overflow: hidden; }
			.wsfw-pos-table thead th { background: #f7f3ff; color: #20143b; font-weight: 600; border-bottom: 1px solid #eadffd; }
			.wsfw-pos-table th,
			.wsfw-pos-table td { padding: 12px; vertical-align: middle; border-color: #f0e8fb; }
			.wsfw-pos-table tbody tr:hover { background: #faf8ff; }

			.wsfw-pos-badge { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
			.wsfw-pos-badge-debit { background: #fcf0f1; color: #b32d2e; }
			.wsfw-pos-badge-credit { background: #fff6df; color: #8a6d00; }
			.wsfw-pos-badge-open { background: #fff6df; color: #8a6d00; }
			.wsfw-pos-badge-closed { background: #f0e8fb; color: #4b3a64; }

			.tablenav.bottom { margin-top: 14px; display: flex; align-items: center; gap: 12px; }
			.tablenav.bottom .page-numbers { border-color: #eadffd; color: #20143b; }
			.tablenav.bottom .page-numbers.current { background: #050505; border-color: #050505; color: #fff; }
		</style>
		<?php
	}
}
