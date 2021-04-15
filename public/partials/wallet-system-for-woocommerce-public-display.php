<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/public/partials
 */
global $wp;
$current_url = home_url( add_query_arg( array(), $wp->request ).'/' );
if ( isset( $_POST['mwb_recharge_wallet'] ) && ! empty( $_POST['mwb_recharge_wallet'] )) {
    
    unset( $_POST['mwb_recharge_wallet'] );
    
    
    if ( empty( $_POST['mwb_wallet_recharge_amount'] ) ) {
        show_message_on_form_submit( 'Please enter amount greater than 0', 'woocommerce-error' );
    } else {
        $recharge_amount = sanitize_text_field( $_POST['mwb_wallet_recharge_amount'] );
        if ( ! empty( $_POST['user_id'] ) ) {
            $user_id = sanitize_text_field( $_POST['user_id'] );

        }
        $product_id = sanitize_text_field( $_POST['product_id'] );
        WC()->session->set( 'wallet_recharge', array( 'userid' => $user_id, 'rechargeamount' => $recharge_amount, 'productid' => $product_id ) );
        WC()->session->set( 'recharge_amount', $recharge_amount );
        echo '<script>window.location.href = "' . home_url( '/cart/') . '";</script>';
    }
    
}
if ( isset( $_POST['mwb_proceed_transfer'] ) && ! empty( $_POST['mwb_proceed_transfer'] )) {
    unset( $_POST['mwb_proceed_transfer'] );
    $update = true; 
    if ( ! empty( $_POST['current_user_id'] ) ) {
        $user_id = sanitize_text_field( $_POST['current_user_id'] );
    }
    
    $wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );
    $another_user_email = ! empty( $_POST['mwb_wallet_transfer_user_email'] ) ? sanitize_text_field( $_POST['mwb_wallet_transfer_user_email'] ) : '';
    $transfer_note = ! empty( $_POST['mwb_wallet_transfer_note'] ) ? sanitize_text_field( $_POST['mwb_wallet_transfer_note'] ) : '';
    $user = get_user_by( 'email', $another_user_email );
    if ( $user ) {
        $another_user_id = $user->ID;
    } else {
        show_message_on_form_submit( 'Email Id does not exist.', 'woocommerce-error' );
        $update = false; 
    }
    if ( empty( $_POST['mwb_wallet_transfer_amount'] ) ) {
        show_message_on_form_submit( 'Please enter amount greater than 0', 'woocommerce-error' );
        $update = false; 
    } elseif ( $wallet_bal < $_POST['mwb_wallet_transfer_amount'] ) {
        show_message_on_form_submit( 'Please enter amount less than or equal to wallet balance', 'woocommerce-error' );  
        $update = false; 
    } 
    if ( $update ) {
        $transfer_amount = sanitize_text_field( $_POST['mwb_wallet_transfer_amount'] );
        $user_wallet_bal = get_user_meta( $another_user_id, 'mwb_wallet', true );
        $user_wallet_bal += $transfer_amount;
        $returnid = update_user_meta( $another_user_id , 'mwb_wallet', $user_wallet_bal );
        
        if ( $returnid ) {
            $transaction_type = 'Wallet credited by user #'. $user_id . ' to user #' . $another_user_id;
            $wallet_transfer_data = array(
                'user_id'          => $another_user_id,
                'amount'           => $transfer_amount,
                'payment_method'   => 'Wallet Transfer',
                'transaction_type' => $transaction_type,
                'order_id'         => '',
                'note'             => $transfer_note,
    
            );

            $wallet_payment_gateway = new Wallet_System_For_Woocommerce();
            $wallet_payment_gateway->insert_transaction_data_in_table( $wallet_transfer_data );

            $wallet_bal -= $transfer_amount;
            $update_user = update_user_meta( $user_id, 'mwb_wallet', abs( $wallet_bal ) );
            if ( $update_user ) {
                
                $transaction_type = 'Wallet debited from user #'. $user_id . ' wallet, transferred to user #' . $another_user_id;
                $transaction_data = array(
                    'user_id'          => $user_id,
                    'amount'           => $transfer_amount,
                    'payment_method'   => 'Wallet Transfer',
                    'transaction_type' => $transaction_type,
                    'order_id'         => '',
                    'note'             => $transfer_note,
        
                );
    
                $result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
                show_message_on_form_submit( 'Amount is transferred successfully', 'woocommerce-message' );
                // echo '<script>alert("Amount is transferred successfully");
                // window.location.href = "' . $current_url . '";</script>';
            } else {
                show_message_on_form_submit( 'Amount is not transferred', 'woocommerce-error' );
            }
        } else {
            show_message_on_form_submit( 'No user  found.', 'woocommerce-error' );
        }
        

    }
    
}

if ( isset( $_POST['mwb_withdrawal_request'] ) && ! empty( $_POST['mwb_withdrawal_request'] )) {   
    unset( $_POST['mwb_withdrawal_request'] );
    if ( ! empty( $_POST['wallet_user_id'] ) ) {
        $user_id = sanitize_text_field( $_POST['wallet_user_id'] );
        $user = get_user_by( 'id', $user_id );
        $username  = $user->user_login;

    }

    $args = array(
        'post_title'  => $username,
        'post_type'   => 'wallet_withdrawal',
        'post_status' => 'pending',
    );
    $withdrawal_id = wp_insert_post( $args );
    if ( ! empty( $withdrawal_id ) ) {

        foreach ( $_POST as $key => $value ) {
            if ( ! empty( $value ) ) {
                $value = sanitize_text_field($value);
                update_post_meta( $withdrawal_id, $key, $value );

            }
    
        }
        //update_post_meta( $withdrawal_id, 'withdrawal_request_status', 'Pending' );
        update_user_meta( $user_id, 'disable_further_withdrawal_request', true );
 
        echo '<script>window.location.href = "' . $current_url . '";</script>';
    }


}    

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$main_url = esc_url( wc_get_endpoint_url( 'mwb-wallet' ) );
$topup_url = esc_url( wc_get_endpoint_url( 'mwb-wallet' ) ).'wallet-topup/';
$wallet_url = esc_url( wc_get_endpoint_url( 'mwb-wallet' ) ).'wallet-transfer/';
$withdrawal_url = esc_url( wc_get_endpoint_url( 'mwb-wallet' ) ).'wallet-withdrawal/';
$transaction_url = esc_url( wc_get_endpoint_url( 'mwb-wallet' ) ).'wallet-transactions/';
$enable_wallet_recharge = get_option( 'wsfw_enable_wallet_recharge', '' );
$product_id = get_option( 'mwb_wsfw_rechargeable_product_id', '' );
$user_id = get_current_user_id();
$wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );

if ( empty( $wallet_bal ) ) {
    $wallet_bal = 0;
}

$wallet_tabs = array();

if ( ! empty( $product_id ) && ! empty( $enable_wallet_recharge ) ) {
    $wallet_tabs['wallet_recharge'] = array(
        'title'     => 'Add Balance',
        'url'       => $topup_url,
        'icon'      => '<path d="M28 10V4C28 3.46957 27.7893 2.96086 27.4142 2.58579C27.0391 2.21071 26.5304 2 26 2H6C4.93913 2 3.92172 2.42143 3.17157 3.17157C2.42143 3.92172 2 4.93913 2 6M2 6C2 7.06087 2.42143 8.07828 3.17157 8.82843C3.92172 9.57857 4.93913 10 6 10H30C30.5304 10 31.0391 10.2107 31.4142 10.5858C31.7893 10.9609 32 11.4696 32 12V18M2 6V27.5M32 26V32C32 32.5304 31.7893 33.0391 31.4142 33.4142C31.0391 33.7893 30.5304 34 30 34H8" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
<circle cx="8.5" cy="27.5" r="6.5" stroke="#1D201F" stroke-width="2.5"/>
<path d="M9.75 25.3333C9.75 24.643 9.19036 24.0833 8.5 24.0833C7.80964 24.0833 7.25 24.643 7.25 25.3333H9.75ZM7.25 29.6666C7.25 30.357 7.80964 30.9166 8.5 30.9166C9.19036 30.9166 9.75 30.357 9.75 29.6666H7.25ZM7.25 25.3333V29.6666H9.75V25.3333H7.25Z" fill="#1D201F"/>
<path d="M10.6666 28.75C11.357 28.75 11.9166 28.1904 11.9166 27.5C11.9166 26.8096 11.357 26.25 10.6666 26.25L10.6666 28.75ZM6.33329 26.25C5.64294 26.25 5.08329 26.8096 5.08329 27.5C5.08329 28.1904 5.64294 28.75 6.33329 28.75L6.33329 26.25ZM10.6666 26.25L6.33329 26.25L6.33329 28.75L10.6666 28.75L10.6666 26.25Z" fill="#1D201F"/>
<path d="M34 18.0001V26.0001H26C24.9391 26.0001 23.9217 25.5786 23.1716 24.8285C22.4214 24.0783 22 23.0609 22 22.0001C22 20.9392 22.4214 19.9218 23.1716 19.1716C23.9217 18.4215 24.9391 18.0001 26 18.0001H34Z" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'public/partials/wallet-payment-gateway-wallet-recharge.php',
    );
}

$wallet_tabs['wallet_transfer'] = array(
    'title'     => 'Transfer',
    'url'       => $wallet_url,
    'icon'      => '<rect x="2" y="12" width="32" height="15.5458" rx="1.5" stroke="#1D201F" stroke-width="2.5"/>
<path d="M28 17V22M8 22V17" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
<circle cx="18.1246" cy="19.5" r="3.5" stroke="#1D201F" stroke-width="2.5"/>
<path d="M14.2556 34.1923L12.0164 31.9204L24.1429 31.9204" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M21.7444 5.80768L23.9836 8.0796L11.8571 8.0796" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
    'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'public/partials/wallet-payment-gateway-wallet-transfer.php',
);

$wallet_tabs['wallet_withdrawal'] = array(
    'title'     => 'Wallet Withdrawal Request',
    'url'       => $withdrawal_url,
    'icon'      => '<path d="M25.826 6.5L25.826 30.5652C25.826 31.3936 25.1545 32.0652 24.326 32.0652L11.1044 32.0652C10.2759 32.0652 9.60437 31.3936 9.60437 30.5652L9.60437 6.5" stroke="#1D201F" stroke-width="2.5"/>
<path d="M6 5.77173C5.30964 5.77173 4.75 6.33137 4.75 7.02173C4.75 7.71208 5.30964 8.27173 6 8.27173V5.77173ZM30 8.27173C30.6904 8.27173 31.25 7.71208 31.25 7.02173C31.25 6.33137 30.6904 5.77173 30 5.77173V8.27173ZM6 8.27173H30V5.77173H6V8.27173Z" fill="#1D201F"/>
<path d="M20.6086 25.8043L15.3913 25.8043" stroke="#1D201F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
<circle cx="18" cy="15.4996" r="3.65217" transform="rotate(90 18 15.4996)" stroke="#1D201F" stroke-width="2.5"/>
<path d="M25 13H32C33.1046 13 34 12.1046 34 11V4C34 2.89543 33.1046 2 32 2H4C2.89543 2 2 2.89543 2 4V11C2 12.1046 2.89543 13 4 13H10" stroke="#1D201F" stroke-width="2.5"/>',
    'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'public/partials/wallet-payment-gateway-wallet-withdrawal.php',
);
$wallet_tabs['wallet_transactions'] = array(
    'title'     => 'Transaction',
    'url'       => $transaction_url,
    'icon'      => '<path d="M2 7C2 4.23858 4.23858 2 7 2H23C25.7614 2 28 4.23858 28 7V28.6227C28 30.476 25.6972 31.3325 24.4861 29.9296L22.4665 27.5901C21.7195 26.7249 20.4005 26.6606 19.5729 27.4491L16.1765 30.6854C15.404 31.4215 14.1897 31.4215 13.4172 30.6854L10.067 27.4931C9.22232 26.6883 7.87085 26.7743 7.1351 27.6799L5.55223 29.628C4.36484 31.0894 2 30.2498 2 28.3668V7Z" stroke="#1D201F" stroke-width="2.5"/>
<path d="M19 9.25C19.6904 9.25 20.25 8.69036 20.25 8C20.25 7.30964 19.6904 6.75 19 6.75V9.25ZM11 6.75C10.3096 6.75 9.75 7.30964 9.75 8C9.75 8.69036 10.3096 9.25 11 9.25V6.75ZM19 6.75H11V9.25H19V6.75Z" fill="#1D201F"/>
<path d="M23 15.25C23.6904 15.25 24.25 14.6904 24.25 14C24.25 13.3096 23.6904 12.75 23 12.75V15.25ZM7 12.75C6.30964 12.75 5.75 13.3096 5.75 14C5.75 14.6904 6.30964 15.25 7 15.25V12.75ZM23 12.75H7V15.25H23V12.75Z" fill="#1D201F"/>
<path d="M21 21.25C21.6904 21.25 22.25 20.6904 22.25 20C22.25 19.3096 21.6904 18.75 21 18.75V21.25ZM9 18.75C8.30964 18.75 7.75 19.3096 7.75 20C7.75 20.6904 8.30964 21.25 9 21.25V18.75ZM21 18.75H9V21.25H21V18.75Z" fill="#1D201F"/>',
    'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'public/partials/wallet-payment-gateway-wallet-transactions.php',
);
$flag = false;
if ( ( $current_url == $main_url )  ) {
    $flag = true;
}

function show_message_on_form_submit( $wpg_message, $type = 'error' ) {
    $wpg_notice  = '<div class="woocommerce"><p class="' . esc_attr( $type ) . '">' . $wpg_message . '</p>	</div>';
    echo wp_kses_post( $wpg_notice );
}

?>

<div class="mwb_wcb_wallet_display_wrapper">
	<div class="mwb_wcb_wallet_balance_container"> 
		<h4><?php esc_html_e( 'Wallet Balance', 'wallet-system-for-woocommerce' ); ?></h4>
		<p><?php echo wc_price( $wallet_bal ); ?></p>
	</div>
	<div class="mwb_wcb_main_tabs_template">
		<div class="mwb_wcb_body_template">
			<div class="mwb_wcb_content_template">

                <nav class="wallet-tabs">
                    <ul class='tabs'>
                        
                        <?php
                        foreach ( $wallet_tabs as $key => $tab ) { 
                            if ( $flag ) { ?>
                                <li <?php  if (  $key == array_key_first ( $wallet_tabs ) ) { echo 'class="active"'; }  ?> ><a href="<?php esc_attr_e( $tab['url'], 'wallet-system-for-woocommerce' ); ?>"><svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><?php echo $tab['icon']; ?></svg></a><h3><?php esc_html_e( $tab['title'], 'wallet-system-for-woocommerce' ); ?></h3></li>
                            <?php } else { ?>
                                <li <?php  if ( $current_url ==  $tab['url'] ) { echo 'class="active"'; } ?> ><a href="<?php esc_attr_e( $tab['url'], 'wallet-system-for-woocommerce' ); ?>"><svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><?php echo $tab['icon']; ?></svg></a><h3><?php esc_html_e( $tab['title'], 'wallet-system-for-woocommerce' ); ?></h3></li>
                            <?php }
                        } ?>
                    </ul>
                </nav>

                <div class='content-section'>

                <?php
                foreach ( $wallet_tabs as $key => $tab ) { 
                    if ( $flag ) {
                        if (  $key == array_key_first ( $wallet_tabs ) ) {
                            include_once $tab['file-path'];
                        } 
                    } else { 
                        if ( $current_url ==  $tab['url'] ) {
                            include_once $tab['file-path'];
                        }
                    }
                } ?> 
                </div>
			</div>
		</div>
	</div>
</div>

