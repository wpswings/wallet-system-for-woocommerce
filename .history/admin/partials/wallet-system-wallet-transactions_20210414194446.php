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

?>

<div class="mwb-wpg-transcation-section-search">

    <table>
            <tbody>
                <tr>
                    <th>Search</td>
                    <td><input type="text" id="search_in_table" placeholder="Enter your Keyword"></td>
                </tr>
                <tr>
                    <td><input name="min" id="min" type="text" placeholder="From"></td>
                </tr>
                <tr>
                    <td><input name="max" id="max" type="text" placeholder="To"></td>
                </tr>
            </tbody>
        </table>


</div>


<div class="mwb-wpg-gen-section-table-wrap mwb-wpg-transcation-section-table">
    <h4>Transactions</h4>
    <div class="mwb-wpg-gen-section-table-container">
        <table id="mwb-wpg-gen-table" class="mwb-wpg-gen-section-table dt-responsive" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php esc_html_e( 'Name', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Email', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Role', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Payment Method', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Action', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Transaction ID', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'PC_wallet_transaction';
                $transactions = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY `Id` DESC" );
                if ( ! empty( $transactions ) && is_array($transactions ) ) {
                    $i = 1;
                    foreach ( $transactions as $transaction ) {
                        $user = get_user_by( 'id', $transaction->user_id );
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php esc_html_e( $user->display_name, 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php esc_html_e( $user->user_email, 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php esc_html_e( $user->roles[0], 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php echo wc_price( $transaction->amount ); ?></td>
                            <td><?php esc_html_e( $transaction->payment_method, 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php echo html_entity_decode( $transaction->transaction_type ); ?></td>
                            <td><?php echo $transaction->Id;  ?></td>
                            <td><?php $date = date_create($transaction->date);
                            esc_html_e( date_format( $date,"m/d/Y"), 'wallet-system-for-woocommerce' );
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
</div>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script> 
<script>
jQuery.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
        var min = jQuery('#min').datepicker("getDate");
        var max = jQuery('#max').datepicker("getDate");   
        var startDate = new Date(data[8]);
        if (min == null && max == null) { return true; }
        if (min == null && startDate <= max) { return true;}
        if(max == null && startDate >= min) {return true;}
        if (startDate <= max && startDate >= min) { return true; }
        return false;
    }
);
jQuery(document).ready(function(){
    var table = jQuery('#mwb-wpg-gen-table').DataTable();   //pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
    jQuery('#search_in_table').keyup(function(){
        table.search(jQuery(this).val()).draw() ;
    });
    jQuery("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
    jQuery("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
    
    jQuery('#min, #max').change(function () {
        table.draw();
    });
});

</script>