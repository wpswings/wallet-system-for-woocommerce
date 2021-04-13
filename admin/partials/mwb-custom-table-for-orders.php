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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script>
        $('.bulkactions').append('<input id="searchFrom" class="searchInput" type="text" placeholder="From d/m/y"/><input id="searchTo" class="searchInput" type="text" placeholder="To" >');
        $(".searchInput").on("input", function() {
            var from = stringToDate($("#searchFrom").val());
            var to = stringToDate($("#searchTo").val());

            $(".walletrechargeorders tr").each(function() {
                var row = $(this);
                var date = stringToDate(row.find("td").eq(3).text());
                
                //show all rows by default
                var show = true;

                //if from date is valid and row date is less than from date, hide the row
                if (from && date < from)
                show = false;
                
                //if to date is valid and row date is greater than to date, hide the row
                if (to && date > to)
                show = false;

                if (show)
                row.show();
                else
                row.hide();
            });

            var rowCount = $(".walletrechargeorders tbody tr").length;
            var count = 0;
            $('.walletrechargeorders > tbody  > tr').each(function(index, tr) { 
                var ColumnName = $(tr).attr("style");
                if ( ColumnName == 'display: none;' ) {
                    count++;
                }
            });
            rowCount -= count;
            if ( rowCount == 0 || rowCount == 1 ) {
                $('.tablenav .displaying-num').html(rowCount + ' item');
            } else {
                $('.tablenav .displaying-num').html(rowCount + ' items');
            }
        });

        //parse entered date. return NaN if invalid
        function stringToDate(s) {
        var ret = NaN;
        var parts = s.split("/");
        date = new Date(parts[2], parts[0], parts[1]);
        if (!isNaN(date.getTime())) {
            ret = date;
        }
        return ret;
        }
        </script>

    </div>
</div>


