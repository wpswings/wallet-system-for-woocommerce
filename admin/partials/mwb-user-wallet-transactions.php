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

<div class="mwb-wpg-gen-section-table-wrap mwb-wpg-transcation-section-table">
    <h4><?php esc_html_e( 'Wallet Transactions: '.$user->user_login. '('.$user->user_email. ')', 'wallet-system-for-woocommerce' );
        ?>
        <a style="text-decoration: none;" href="<?php echo esc_url( admin_url( "admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=wallet-system-wallet-setting" ) ); ?>"><span class="dashicons dashicons-editor-break" style="vertical-align: middle;"></span></a></h4>
    <div class="mwb-wpg-gen-section-table-container">
        <table id="mwb-wpg-gen-table" class="mwb-wpg-gen-section-table dt-responsive" style="width:100%">
            <thead>
                <tr>
                    <th><?php esc_html_e( '#', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Transaction ID', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Payment Method', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Action (Debit/Credit)', 'wallet-system-for-woocommerce' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'PC_wallet_transaction';
                $transactions = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id = $user_id ORDER BY `Id` DESC" );
                if ( ! empty( $transactions ) && is_array($transactions ) ) {
                    $i = 1;
                    foreach ( $transactions as $transaction ) {
                        ?>
                        <tr>
                            <td><?php echo $i;  ?></td>
                            <td><?php echo $transaction->Id;  ?></td>
                            <td><?php echo wc_price( $transaction->amount ); ?></td>
                            <td><?php esc_html_e( $transaction->payment_method, 'wallet-system-for-woocommerce' ); ?></td>
                            <td><?php echo html_entity_decode( $transaction->transaction_type ); ?></td>
                            <td><?php $date = date_create($transaction->date);
                            esc_html_e( date_format( $date,"d/m/Y"), 'wallet-system-for-woocommerce' );
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
