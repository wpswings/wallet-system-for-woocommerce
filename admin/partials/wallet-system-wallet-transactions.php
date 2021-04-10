<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to show wallet transactions.
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

//require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH. 'admin/class-user-wallet-transactions.php';
//$wallet_transactions = new Wallet_Transactions_List();

?>
<div id="wrapper" class="mwb_wcb_all_trans_container">
	<h3> <?php esc_html_e( 'All Wallet Transactions', 'wallet-payment-gateway' ); ?></h3>
    
    <table id="table_id"  class="display" >
        <thead>
            <tr>
                <th><?php esc_html_e( 'Transaction Id', 'wallet-payment-gateway' ); ?></th>
                <th><?php esc_html_e( 'Username', 'wallet-payment-gateway' ); ?></th>
                <th><?php esc_html_e( 'Email', 'wallet-payment-gateway' ); ?></th>
                <th><?php esc_html_e( 'Amount', 'wallet-payment-gateway' ); ?></th>
                <th><?php esc_html_e( 'Payment Method', 'wallet-payment-gateway' ); ?></th>
                <th><?php esc_html_e( 'Details', 'wallet-payment-gateway' ); ?></th>
                <th><?php esc_html_e( 'Date', 'wallet-payment-gateway' ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'PC_wallet_transaction';
        $transactions = $wpdb->get_results( "SELECT * FROM $table_name" );
        if ( ! empty( $transactions ) && is_array($transactions ) ) {
            foreach ( $transactions as $transaction ) {
                $user = get_user_by( 'id', $transaction->user_id );
                ?>
                <tr>
                    <td><?php echo $transaction->Id;  ?></td>
                    <td><?php esc_html_e( $user->user_login, 'wallet-payment-gateway' ); ?></td>
                    <td><?php esc_html_e( $user->user_email, 'wallet-payment-gateway' ); ?></td>
                    <td><?php echo wc_price( $transaction->amount ); ?></td>
                    <td><?php esc_html_e( $transaction->payment_method, 'wallet-payment-gateway' ); ?></td>
                    <td><?php echo html_entity_decode( $transaction->transaction_type ); ?></td>
                    <td><?php $date = date_create($transaction->date);
                    esc_html_e( date_format( $date,"Y/m/d"), 'wallet-payment-gateway' );
                     ?></td>
                </tr>
                <?php
            }
        }
       
        ?>
        </tbody>
	</table>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script>
    jQuery('#table_id').DataTable( {
        "dom": '<lf<"toolbar"><t>ip>',
        "order": [[ 0, "desc" ]]
        
    } );
    jQuery("div.toolbar").html(' <table><tbody><tr><td>Minimum Date:</td><td><input name="min" id="min" type="text"></td><td>Maximum Date:</td><td><input name="max" id="max" type="text"></td> </tr></tbody></table>');
    jQuery.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = jQuery('#min').datepicker("getDate");
            var max = jQuery('#max').datepicker("getDate");  
            var startDate = new Date(data[6]);
            if (min == null && max == null) { return true; }
            if (min == null && startDate <= max) { return true;}
            if(max == null && startDate >= min) {return true;}
            if (startDate <= max && startDate >= min) { return true; }
            return false;
        }
    );
    jQuery(document).ready(function(){

        // jQuery('#table_id').DataTable({
        //     "order": [[ 0, "desc" ]]
        // });
        var table = jQuery('#table_id').DataTable();
        jQuery("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
        jQuery("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
        
        jQuery('#min, #max').change(function () {
            table.draw();
        });
    });
    </script>



    <!-- <form action="" method="POST">
		<?php 
        // if( isset($_GET['s']) ){
        //     $wallet_transactions->prepare_items($_GET['s']);
        // } else {
        //     $wallet_transactions->prepare_items();
        // }
        // $wallet_transactions->search_box( __( 'Search User', 'wallet-system-for-woocommerce' ), 'search_id' );
		// //Table of elements
        // $wallet_transactions->display();
        ?>
	</form> -->
</div>


