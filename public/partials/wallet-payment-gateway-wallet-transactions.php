<?php
/**
 * Exit if accessed directly
 *
 * @package wallet-payment-gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class='content active'>
    <div class="mwb-wallet-transaction-container">
        <table class="mwb-wallet-field-table dt-responsive" id="transactions_table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php esc_html_e( 'Transaction Id', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Method', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Type (Debit/Credit)', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'PC_wallet_transaction';
                $transactions = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY Id DESC" );
                if ( ! empty( $transactions ) && is_array($transactions ) ) {
                    $i = 1;
                    foreach ( $transactions as $transaction ) {
                        $user = get_user_by( 'id', $transaction->user_id );
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $transaction->Id;  ?></td>
                            <td><?php echo wc_price( $transaction->amount ); ?></td>
                            <td><?php esc_html_e( $transaction->payment_method, 'wallet-system-for-woocommerce' ); ?></td>
                            <td class="details" ><?php echo html_entity_decode( $transaction->transaction_type ); ?></td>
                            <td><?php $date = date_create($transaction->date);
                            esc_html_e( date_format( $date,"Y/m/d"), 'wallet-system-for-woocommerce' );
                            ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
            
                ?>
            </tbody>
        </table>
    </div>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

   <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script> 

            

    <script>
    jQuery( "#transactions_table tr td" ).each(function( index ) {
        var details = jQuery( this ).html();
        var patt = new RegExp("<a");
        var res = patt.test(details);
        if ( res ) {
            jQuery(this).children('a').removeAttr("href");
        }
    });
    // jQuery.fn.dataTable.ext.search.push(
    //     function (settings, data, dataIndex) {
    //         var min = jQuery('#min').datepicker("getDate");
    //         var max = jQuery('#max').datepicker("getDate");   
    //         var startDate = new Date(data[4]);
    //         if (min == null && max == null) { return true; }
    //         if (min == null && startDate <= max) { return true;}
    //         if(max == null && startDate >= min) {return true;}
    //         if (startDate <= max && startDate >= min) { return true; }
    //         return false;
    //     }
    // );
    // jQuery(document).ready(function(){
    //     //jQuery('#transactions_table').DataTable();
    //     var table = jQuery('#transactions_table').DataTable({
    //         "order": [[ 0, "desc" ]]
    //     });
    //     jQuery("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
    //     jQuery("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
        
    //     jQuery('#min, #max').change(function () {
    //         table.draw();
    //     });
        
    // });
    </script>
</div>   
    