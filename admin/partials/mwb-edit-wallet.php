<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for showing wallet withdrawal setting
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
global $wsfw_mwb_wsfw_obj;


if ( isset( $_POST['update_wallet'] ) && ! empty( $_POST['update_wallet'] ) ) {
	unset( $_POST['update_wallet'] );
	$update = true;
	if ( empty( $_POST['wallet_amount'] ) ) {
		$msfw_wpg_error_text = esc_html__( 'Please enter any amount', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
		$update = false;
	} 
    if ( $_POST['wallet_amount'] < 0 ) {
		$msfw_wpg_error_text = esc_html__( 'Please enter amount in positive value.', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
		$update = false;
	}
	if ( empty( $_POST['action_type'] ) ) {
		$msfw_wpg_error_text = esc_html__( 'Please select any action', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
		$update = false;
	}
    if ( empty( $_POST['user_id'] ) ) {
		$msfw_wpg_error_text = esc_html__( 'User Id is not given', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
		$update = false;
	}
	if ( $update ) {
		$updated_amount = sanitize_text_field( $_POST['wallet_amount'] );
		$wallet_action = sanitize_text_field( $_POST['action_type'] );
        $user_id = sanitize_text_field( $_POST['user_id'] );
        $wallet = get_user_meta( $user_id, 'mwb_wallet', true );

        if ( 'credit' == $wallet_action ) { 
            $wallet += $updated_amount;
            $wallet = update_user_meta( $user_id, 'mwb_wallet', $wallet );
            $transaction_type = 'Credited by admin';

        } elseif ( 'debit' == $wallet_action ) { 
            if ( $wallet < $updated_amount ) {
                $wallet = 0;
            } else {
                $wallet -= $updated_amount;
            }
            $wallet = update_user_meta( $user_id, 'mwb_wallet', abs($wallet) );
            $transaction_type = 'Debited by admin';
            
        }
        $transaction_data = array(
            'user_id'          => $user_id,
            'amount'           => $updated_amount,
            'payment_method'   => 'Manually By Admin',
            'transaction_type' => $transaction_type,
            'order_id'         => '',
            'note'             => '',

        );
        $wallet_payment_gateway = new Wallet_System_For_Woocommerce();
        $result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
		if ( $result ) {
            $msfw_wpg_error_text = esc_html__( 'Updated wallet of user', 'wallet-system-for-woocommerce' );
		    $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'success' );
        } else {
            $msfw_wpg_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
		    $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
        }
		
	}
	
}
if ( isset( $_GET['id'] ) && ! empty( $_GET['id'] ) ) {
    $user_id = sanitize_text_field( $_GET['id'] );
}
$user = get_user_by( 'id', $user_id );
$wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );

?>
<div class="wrap">
    <h2>
        <?php esc_html_e( 'Edit User Wallet: '.$user->user_login. '('.$user->user_email. ')', 'wallet-system-for-woocommerce' );
        ?>
        <a style="text-decoration: none;" href="<?php echo esc_url( admin_url( "users.php" ) ); ?>"><span class="dashicons dashicons-editor-break" style="vertical-align: middle;"></span></a>
    </h2>
    <p>
    <?php esc_html_e( 'Current wallet balance: ', 'wallet-system-for-woocommerce' );
    echo wc_price($wallet_bal);
     ?> </p>
    <form method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="wallet_amount"><?php esc_html_e( 'Amount ( '. get_woocommerce_currency_symbol(). ' )', 'wallet-system-for-woocommerce' ); ?></label></th>
                    <td>
                        <input type="number" id="wallet_amount" name="wallet_amount" class="regular-text" required>
                        <p class="description"><?php esc_html_e( 'Enter amount you want to credit/debit', 'wallet-system-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="action_type"><?php esc_html_e( 'Action', 'wallet-system-for-woocommerce' ); ?></label></th>
                    <td>
                        <select class="regular-text" name="action_type" id="action_type" required>
                            <option value="credit"><?php esc_html_e( 'Credit', 'wallet-system-for-woocommerce' ); ?></option>
                            <option value="debit"><?php esc_html_e( 'Debit', 'wallet-system-for-woocommerce' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Whether want to add amount or deduct it from wallet', 'wallet-system-for-woocommerce' ); ?></p></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="hidden" name="user_id" value="<?php esc_attr_e( $user_id, 'walllet-payment-gateway' ); ?>">
        <p class="submit"><input type="submit" name="update_wallet" class="button button-primary mwb_wallet-update" value="Update Wallet"></p> 
   </form>
</div>