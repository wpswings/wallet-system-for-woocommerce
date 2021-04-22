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

        $wallet_payment_gateway = new Wallet_System_For_Woocommerce();

		$updated_amount = sanitize_text_field( $_POST['wallet_amount'] );
		$wallet_action = sanitize_text_field( $_POST['action_type'] );
        $user_id = sanitize_text_field( $_POST['user_id'] );
        $wallet = get_user_meta( $user_id, 'mwb_wallet', true );

        if ( 'credit' == $wallet_action ) { 
            $wallet += $updated_amount;
            $wallet = update_user_meta( $user_id, 'mwb_wallet', $wallet );
            $transaction_type = 'Credited by admin';
            $mail_message     = __( 'Merchant has credited your wallet by '. wc_price( $updated_amount ), 'wallet-system-for-woocommerce' );

        } elseif ( 'debit' == $wallet_action ) { 
            if ( $wallet < $updated_amount ) {
                $wallet = 0;
            } else {
                $wallet -= $updated_amount;
            }
            $wallet = update_user_meta( $user_id, 'mwb_wallet', abs($wallet) );
            $transaction_type = 'Debited by admin';
            $mail_message     = __( 'Merchant has deducted '. wc_price( $updated_amount ). ' from your wallet.', 'wallet-system-for-woocommerce' );
            
        }

        $send_email_enable = get_option( 'mwb_wsfw_enable_email_notification_for_wallet_update', '' );
        if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
            $user = get_user_by( 'id', $user_id );
            $name = $user->first_name . ' ' . $user->last_name;
            $mail_text = sprintf( "Hello %s,<br/>", $name );
            $mail_text .= $mail_message;
            $to = $user->user_email;
            $from = get_option( 'admin_email' );
            $subject = "Wallet updating notification";
            $headers = 'From: '. $from . "\r\n" .
                'Reply-To: ' . $to . "\r\n";

            $wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers ); 
        }

        $transaction_data = array(
            'user_id'          => $user_id,
            'amount'           => $updated_amount,
            'payment_method'   => 'Manually By Admin',
            'transaction_type' => $transaction_type,
            'order_id'         => '',
            'note'             => '',

        );

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
                        <input type="number" id="wallet_amount" step="0.01" name="wallet_amount" class="regular-text" required>
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