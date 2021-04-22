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
                    <th><?php esc_html_e( 'Search', 'wallet-system-for-woocommerce' ); ?></td>
                    <td><input type="text" id="search_in_table" placeholder="Enter your Keyword"></td>
                </tr>
                <tr>
                    <td><input name="min" id="min" type="text" placeholder="From" ></td>
                </tr>
                <tr>
                    <td><input name="max" id="max" type="text" placeholder="To"></td>
                </tr>
                <tr>
                    <td><span id="clear_table" ><?php esc_html_e( 'Clear', 'wallet-system-for-woocommerce' ); ?></span></td>
                </tr>
            </tbody>
        </table>


</div>


<div class="mwb-wpg-gen-section-table-wrap mwb-wpg-transcation-section-table">
    <h4><?php esc_html_e( 'Transactions', 'wallet-system-for-woocommerce' ); ?> </h4>
    <div class="mwb-wpg-gen-section-table-container">
        <table id="mwb-wpg-gen-table" class="mwb-wpg-gen-section-table dt-responsive mwb-wpg-gen-table-all-transaction" style="width:100%">
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
                    <th class="hide_date" ><?php esc_html_e( 'Date1', 'wallet-system-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'mwb_wsfw_wallet_transaction';
                $transactions = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY `Id` DESC" );
                if ( ! empty( $transactions ) && is_array($transactions ) ) {
                    $i = 1;
                    foreach ( $transactions as $transaction ) {
                        $user = get_user_by( 'id', $transaction->user_id );
                        ?>
                        <tr>
                            <td><img src="<?php echo WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL; ?>admin/image/eva_close-outline.svg"><?php echo $i; ?></td>
                            <td><?php esc_html_e( $user->display_name, 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php esc_html_e( $user->user_email, 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php esc_html_e( $user->roles[0], 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php echo wc_price( $transaction->amount ); ?></td>
                            <td><?php esc_html_e( $transaction->payment_method, 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php echo html_entity_decode( $transaction->transaction_type ); ?></td>
                            <td><?php echo $transaction->Id;  ?></td>
                            <td><?php $date_format = get_option( 'date_format', 'm/d/Y' ); $date = date_create($transaction->date);
                            esc_html_e( date_format( $date, $date_format ), 'wallet-system-for-woocommerce' );
                            ?></td>
                            <td class="hide_date" ><?php $date = date_create($transaction->date);
                            esc_html_e( date_format( $date, 'Y-m-d' ), 'wallet-system-for-woocommerce' );
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

<?php
wp_enqueue_script( 'mwb-admin-all-transaction-table', WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/js/wallet-system-for-woocommerce-all-transaction-table.js', array( 'jquery' ), $this->version, false );
?>
