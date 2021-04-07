<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for showing user's wallet transactions
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

if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
    $user_id = sanitize_text_field( $_GET['id'] );
}
$user = get_user_by( 'id', $user_id );

?>

<div class="wrap">
    <h2>
        <?php esc_html_e( 'Wallet Transactions: '.$user->user_login. '('.$user->user_email. ')', 'wallet-payment-gateway' );
        ?>
        <a style="text-decoration: none;" href="<?php echo esc_url( admin_url( "users.php" ) ); ?>"><span class="dashicons dashicons-editor-break" style="vertical-align: middle;"></span></a>
    </h2>
    <p>
    
    <table id="table_id"  class="display" >
        <thead>
            <tr>
                <th><?php esc_html_e( 'Transaction Id', 'wallet-payment-gateway' ); ?></th>
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
        $transactions = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id = $user_id" );
        if ( ! empty( $transactions ) && is_array($transactions ) ) {
            foreach ( $transactions as $transaction ) {
                ?>
                <tr>
                    <td><?php echo $transaction->Id;  ?></td>
                    <td><?php echo wc_price( $transaction->amount ); ?></td>
                    <td><?php esc_html_e( $transaction->payment_method, 'wallet-payment-gateway' ); ?></td>
                    <td><?php echo html_entity_decode( $transaction->transaction_type ); ?></td>
                    <td><?php $date = date_create($transaction->date);
                    esc_html_e( date_format( $date,"Y-m-d"), 'wallet-payment-gateway' );
                     ?></td>
                </tr>
                <?php
            }
        }
       
        ?>
        </tbody>
	</table>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
    <script>
    jQuery(document).ready(function(){
        jQuery('#table_id').DataTable({
            "order": [[ 0, "desc" ]]
        });
    });
    </script>
</div>


