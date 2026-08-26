<?php
/**
 * Minimal WP-context CLI tool used by the Playwright E2E suite for test
 * setup/teardown and DB-level assertions that would be slow or flaky to
 * drive through the UI (creating fixed test customers, forcing a known
 * wallet balance before a money-flow test, reading the transaction ledger).
 *
 * Usage: php index.php <action> '<json-args>'
 * Always prints a single JSON line to stdout on success, exits 1 with a
 * message on stderr on failure.
 */

define( 'WP_USE_THEMES', false );
require dirname( __FILE__, 6 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/user.php';

function wps_e2e_out( $data ) {
	echo wp_json_encode( $data ) . "\n";
	exit( 0 );
}

function wps_e2e_err( $message ) {
	fwrite( STDERR, $message . "\n" );
	exit( 1 );
}

$action = $argv[1] ?? '';
$args   = isset( $argv[2] ) ? json_decode( $argv[2], true ) : array();

switch ( $action ) {

	case 'ensure_customer':
		$user = get_user_by( 'login', $args['login'] );
		if ( ! $user ) {
			$id = wp_insert_user(
				array(
					'user_login' => $args['login'],
					'user_email' => $args['email'],
					'user_pass'  => $args['password'],
					'role'       => 'customer',
				)
			);
			if ( is_wp_error( $id ) ) {
				wps_e2e_err( $id->get_error_message() );
			}
			$user = get_user_by( 'id', $id );
		} else {
			wp_set_password( $args['password'], $user->ID );
			$user = get_user_by( 'id', $user->ID );
		}
		wps_e2e_out(
			array(
				'id'    => $user->ID,
				'login' => $user->user_login,
				'email' => $user->user_email,
			)
		);
		break;

	case 'delete_user_by_login':
		$user = get_user_by( 'login', $args['login'] );
		if ( $user ) {
			wp_delete_user( $user->ID );
			wps_e2e_out( array( 'deleted' => true ) );
		}
		wps_e2e_out( array( 'deleted' => false ) );
		break;

	case 'get_user_by_login':
		$user = get_user_by( 'login', $args['login'] );
		wps_e2e_out( $user ? array( 'id' => $user->ID, 'email' => $user->user_email ) : null );
		break;

	case 'get_user_by_email':
		$user = get_user_by( 'email', $args['email'] );
		wps_e2e_out( $user ? array( 'id' => $user->ID, 'login' => $user->user_login ) : null );
		break;

	case 'set_wallet_balance':
		$user_id = (int) $args['user_id'];
		update_user_meta( $user_id, 'wps_wallet', (string) $args['amount'] );
		wps_e2e_out(
			array(
				'user_id' => $user_id,
				'balance' => get_user_meta( $user_id, 'wps_wallet', true ),
			)
		);
		break;

	case 'get_wallet_balance':
		$user_id = (int) $args['user_id'];
		$balance = get_user_meta( $user_id, 'wps_wallet', true );
		wps_e2e_out(
			array(
				'user_id' => $user_id,
				'balance' => '' === $balance ? '0' : $balance,
			)
		);
		break;

	case 'get_wallet_id':
		$user_id = (int) $args['user_id'];
		wps_e2e_out(
			array(
				'user_id'   => $user_id,
				'wallet_id' => get_user_meta( $user_id, 'wps_wallet_id', true ),
			)
		);
		break;

	case 'update_option':
		update_option( $args['name'], $args['value'] );
		wps_e2e_out(
			array(
				'name'  => $args['name'],
				'value' => get_option( $args['name'] ),
			)
		);
		break;

	case 'update_options':
		foreach ( $args['options'] as $name => $value ) {
			update_option( $name, $value );
		}
		wps_e2e_out( array( 'updated' => array_keys( $args['options'] ) ) );
		break;

	case 'get_option':
		wps_e2e_out(
			array(
				'name'  => $args['name'],
				'value' => get_option( $args['name'] ),
			)
		);
		break;

	case 'delete_transient':
		delete_transient( $args['name'] );
		wps_e2e_out( array( 'deleted' => $args['name'] ) );
		break;

	case 'last_transaction':
		global $wpdb;
		$user_id = (int) $args['user_id'];
		$table   = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
		$row     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d ORDER BY id DESC LIMIT 1", $user_id ), ARRAY_A ); // phpcs:ignore
		wps_e2e_out( $row );
		break;

	case 'count_transactions':
		global $wpdb;
		$user_id = (int) $args['user_id'];
		$table   = $wpdb->prefix . 'wps_wsfw_wallet_transaction';
		$count   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE user_id = %d", $user_id ) ); // phpcs:ignore
		wps_e2e_out( array( 'count' => (int) $count ) );
		break;

	case 'get_order_status':
		$order = wc_get_order( (int) $args['order_id'] );
		wps_e2e_out( $order ? array( 'status' => $order->get_status() ) : null );
		break;

	case 'set_order_status':
		$order = wc_get_order( (int) $args['order_id'] );
		if ( ! $order ) {
			wps_e2e_err( 'order not found' );
		}
		$order->update_status( $args['status'] );
		wps_e2e_out( array( 'status' => $order->get_status() ) );
		break;

	case 'get_latest_order_for_user':
		$orders = wc_get_orders(
			array(
				'customer_id' => (int) $args['user_id'],
				'limit'       => 1,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		$order = $orders[0] ?? null;
		wps_e2e_out( $order ? array( 'id' => $order->get_id(), 'status' => $order->get_status(), 'total' => $order->get_total() ) : null );
		break;

	case 'ensure_simple_product':
		$existing = get_page_by_title( $args['title'], OBJECT, 'product' );
		if ( $existing ) {
			$product = wc_get_product( $existing->ID );
		} else {
			$product = new WC_Product_Simple();
			$product->set_name( $args['title'] );
			$product->set_regular_price( (string) $args['price'] );
			$product->set_status( 'publish' );
			$product->set_catalog_visibility( 'visible' );
			$product->set_manage_stock( false );
			$product->set_virtual( true );
			$product->save();
		}
		wps_e2e_out(
			array(
				'id'    => $product->get_id(),
				'price' => $product->get_price(),
			)
		);
		break;

	case 'ensure_page':
		$existing = get_page_by_title( $args['title'], OBJECT, 'page' );
		if ( $existing ) {
			wp_update_post(
				array(
					'ID'           => $existing->ID,
					'post_content' => $args['content'],
					'post_status'  => 'publish',
				)
			);
			$post_id = $existing->ID;
		} else {
			$post_id = wp_insert_post(
				array(
					'post_title'   => $args['title'],
					'post_content' => $args['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);
		}
		wps_e2e_out(
			array(
				'id'  => $post_id,
				'url' => get_permalink( $post_id ),
			)
		);
		break;

	case 'clear_cart_sessions':
		wps_e2e_out( array( 'ok' => true ) );
		break;

	case 'get_user_meta':
		wps_e2e_out(
			array(
				'value' => get_user_meta( (int) $args['user_id'], $args['key'], true ),
			)
		);
		break;

	case 'update_user_meta':
		update_user_meta( (int) $args['user_id'], $args['key'], $args['value'] );
		wps_e2e_out( array( 'value' => get_user_meta( (int) $args['user_id'], $args['key'], true ) ) );
		break;

	// Creates a real WC order server-side (via WC's own APIs) so tests that
	// need "an existing order in state X" (e.g. to refund) don't have to
	// repeat a full UI checkout every time. The checkout flow itself is
	// still exercised end-to-end by the dedicated checkout specs.
	case 'create_paid_order':
		$order = wc_create_order( array( 'customer_id' => (int) $args['user_id'] ) );
		$product = wc_get_product( (int) $args['product_id'] );
		$order->add_product( $product, isset( $args['quantity'] ) ? (int) $args['quantity'] : 1 );
		$order->set_payment_method( $args['payment_method'] ?? 'cod' );
		$order->calculate_totals();
		$order->update_status( $args['status'] ?? 'processing' );
		$order->save();
		wps_e2e_out(
			array(
				'id'     => $order->get_id(),
				'total'  => $order->get_total(),
				'status' => $order->get_status(),
			)
		);
		break;

	case 'update_post_meta':
		update_post_meta( (int) $args['post_id'], $args['key'], $args['value'] );
		wps_e2e_out( array( 'value' => get_post_meta( (int) $args['post_id'], $args['key'], true ) ) );
		break;

	case 'create_wallet_coupon':
		$post_id = wp_insert_post(
			array(
				'post_title'  => $args['code'],
				'post_type'   => 'wps_cpt_coupons',
				'post_status' => 'publish',
			)
		);
		update_post_meta( $post_id, 'wps_wsfw_coupon_amount', (string) $args['amount'] );
		update_post_meta( $post_id, 'wps_wsfw_coupon_expiry', 'on' );
		if ( isset( $args['limit_per_coupon'] ) ) {
			update_post_meta( $post_id, 'wps_wsfw_limit_per_coupon', (string) $args['limit_per_coupon'] );
		}
		wps_e2e_out( array( 'id' => $post_id ) );
		break;

	case 'get_wallet_coupon_by_code':
		$posts = get_posts(
			array(
				'post_type'   => 'wps_cpt_coupons',
				'title'       => $args['code'],
				'post_status' => 'any',
				'numberposts' => 1,
			)
		);
		wps_e2e_out( $posts ? array( 'id' => $posts[0]->ID ) : null );
		break;

	case 'insert_unapproved_comment':
		$comment_id = wp_insert_comment(
			array(
				'comment_post_ID'      => (int) $args['post_id'],
				'user_id'              => (int) $args['user_id'],
				'comment_content'      => $args['content'] ?? 'E2E test review',
				'comment_approved'     => 0,
				'comment_type'         => 'review',
			)
		);
		wps_e2e_out( array( 'id' => $comment_id ) );
		break;

	case 'approve_comment':
		wp_set_comment_status( (int) $args['comment_id'], 'approve' );
		wps_e2e_out( array( 'status' => wp_get_comment_status( (int) $args['comment_id'] ) ) );
		break;

	case 'do_action':
		do_action( $args['hook'] );
		wps_e2e_out( array( 'fired' => $args['hook'] ) );
		break;

	case 'trash_post':
		wp_trash_post( (int) $args['id'] );
		wps_e2e_out( array( 'trashed' => (int) $args['id'] ) );
		break;

	default:
		wps_e2e_err( "Unknown action: {$action}" );
}
