<?php
/**
 * Provide a admin area view for show wallet orders
 *
 * This file is used to show wallet orders.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH. 'admin/class-wallet-orders.php';
$wallet_orders = new Wallet_Orders_List();

// message on applying bulk action
if ( ! empty ( $_REQUEST['bulk_action'] ) && ( 'trash' !== $_REQUEST['bulk_action'] && 'untrash' !== $_REQUEST['bulk_action'] && 'delete' !== $_REQUEST['bulk_action'] ) ) {
    $changed = $_REQUEST['changed'];
    printf(
        '<div id="message" class="updated notice is-dismissable"><p>' . _n(
            '%d order status changed.',
            '%d order status changed.',
            $changed
        ) . '</p></div>',
        $changed
    );
}
if ( ! empty ( $_REQUEST['bulk_action'] ) && ( 'trash' === $_REQUEST['bulk_action'] ) ) {
    $changed = $_REQUEST['changed'];
    printf(
        '<div id="message" class="updated notice is-dismissable"><p>' . _n(
            '%d order moved to trash.',
            '%d orders moved to trash.',
            $changed
        ) . '</p></div>',
        $changed
    );
}
if ( ! empty ( $_REQUEST['bulk_action'] ) && ( 'untrash' === $_REQUEST['bulk_action'] ) ) {
    $changed = $_REQUEST['changed'];
    printf(
        '<div id="message" class="updated notice is-dismissable"><p>' . _n(
            '%d order restored from the Trash.',
            '%d orders restored from the Trash.',
            $changed
        ) . '</p></div>',
        $changed
    );
}
if ( ! empty ( $_REQUEST['bulk_action'] ) && ( 'delete' === $_REQUEST['bulk_action'] ) ) {
    $changed = $_REQUEST['changed'];
    printf(
        '<div id="message" class="updated notice is-dismissable"><p>' . _n(
            '%d order permanently deleted',
            '%d orders permanently deleted',
            $changed
        ) . '</p></div>',
        $changed
    );
}

?>
<div class="wrap">

    <h1 class="wp-heading-inline"> <?php esc_html_e( 'Wallet Recharge Orders', 'wallet-system-for-woocommerce' ); ?></h1>
    <div id="wrapper" class="mwb_wcb_all_trans_container meta-box-sortables ui-sortable wallet_shop_order">
        <?php //$wallet_orders->custom_filter_date(); ?>
        <form action="" method="POST">
        
            <?php 
            $wallet_orders->display_header();
            $wallet_orders->views();

            if( isset($_GET['s']) ){
                $wallet_orders->prepare_items($_GET['s']);
            } else {
                $wallet_orders->prepare_items();
            }
            $wallet_orders->search_box( __( 'Search Order', 'wallet-system-for-woocommerce' ), 'search_id' );
            //Table of elements
            $wallet_orders->display();
            ?>
        </form>
        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> -->
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script> 


        <?php
        wp_enqueue_script( 'mwb-admin-custom-orders', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-custom-orders.js', array( 'jquery' ), $this->version, false );
        ?>

    </div>
</div>


