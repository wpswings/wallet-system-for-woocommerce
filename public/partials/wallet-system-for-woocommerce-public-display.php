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
        echo '<script>alert("Please enter amount greater than 0");
        window.location.href = "' . $current_url . '";
        </script>';
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
    if ( ! empty( $_POST['current_user_id'] ) ) {
        $user_id = sanitize_text_field( $_POST['current_user_id'] );
    }
    
    $wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );
    $another_user_id = ! empty( $_POST['mwb_wallet_transfer_user_id'] ) ? sanitize_text_field( $_POST['mwb_wallet_transfer_user_id'] ) : '';
    
    if ( empty( $_POST['mwb_wallet_transfer_amount'] ) ) {
        echo '<script>alert("Please enter amount greater than 0");
        window.location.href = "' . $current_url . '";
        </script>';
    } elseif ( $wallet_bal < $_POST['mwb_wallet_transfer_amount'] ) {
        echo '<script>alert("Please enter amount less than or equal to wallet balance");
        window.location.href = "' . $current_url . '";
        </script>';
    } else {
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
        
                );
    
                $result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

                echo '<script>alert("Amount is transferred successfully");
                window.location.href = "' . $current_url . '";</script>';
            } else {
                echo '<script>alert("Amount is not transferred");
                window.location.href = "' . $current_url. '";</script>';
            }
        } else {
            echo '<script>alert("No user  found");
            window.location.href = "' . $current_url. '";</script>';
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
        update_post_meta( $withdrawal_id, 'withdrawal_request_status', 'Pending' );
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
$wallet_methods = get_option( 'wallet_withdraw_methods', '' );
$product_id = get_option( 'PC_rechargeable_product_id', '' );
$user_id = get_current_user_id();
$wallet_bal = get_user_meta( $user_id, 'mwb_wallet', true );

if ( empty( $wallet_bal ) ) {
    $wallet_bal = 0;
}

$wallet_tabs = array();

if ( ! empty( $product_id ) && ! empty( $enable_wallet_recharge ) ) {
    $wallet_tabs['wallet_recharge'] = array(
        'url'       => $topup_url,
        'icon'      => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL.'public/images/recharge.svg',
        'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'public/partials/wallet-payment-gateway-wallet-recharge.php',
    );
}
if( $wallet_bal > 0 ) {
    $wallet_tabs['wallet_transfer'] = array(
        'url'       => $wallet_url,
        'icon'      => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL.'public/images/wallet.png',
        'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'public/partials/wallet-payment-gateway-wallet-transfer.php',
    );
}
if( $wallet_bal > 0 && ! empty( $wallet_methods ) && is_array( $wallet_methods ) ) {
    $wallet_tabs['wallet_withdrawal'] = array(
        'url'       => $withdrawal_url,
        'icon'      => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL.'public/images/transaction.svg',
        'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'public/partials/wallet-payment-gateway-wallet-withdrawal.php',
    );
}
$wallet_tabs['wallet_transactions'] = array(
    'url'       => $transaction_url,
    'icon'      => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL.'public/images/wallet.png',
    'file-path' => WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH.'public/partials/wallet-payment-gateway-wallet-transactions.php',
);
$flag = false;
if ( ( $current_url == $main_url )  ) {
    $flag = true;
}

function show_message_on_widthdrawal_requesting( $wpg_message, $type = 'error' ) {
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
                                <li <?php  if (  $key == array_key_first ( $wallet_tabs ) ) { echo 'class="active"'; }  ?> ><a href="<?php esc_attr_e( $tab['url'], 'wallet-system-for-woocommerce' ); ?>"><img src="<?php esc_attr_e( $tab['icon'], 'wallet-system-for-woocommerce' ); ?>"></a></li>
                            <?php } else { ?>
                                <li <?php  if ( $current_url ==  $tab['url'] ) { echo 'class="active"'; } ?> ><a href="<?php esc_attr_e( $tab['url'], 'wallet-system-for-woocommerce' ); ?>"><img src="<?php esc_attr_e( $tab['icon'], 'wallet-system-for-woocommerce' ); ?>"></a></li>
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

