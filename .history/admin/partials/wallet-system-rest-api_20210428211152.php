<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to enable wallet, set min and max value for recharging wallet 
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

if ( isset( $_POST['generate_api_key'] ) && ! empty( $_POST['generate_api_key'] ) ) {
    unset( $_POST['generate_api_key'] );
    $api_keys = array();
    for ($i = 0; $i < 2; $i++) {
        $random = rand();
        $api_keys[] = md5( $random );
    }
    $wallet_api_keys['consumer_key']    = $api_keys[0];
    $wallet_api_keys['consumer_secret'] = $api_keys[1];
    $result = update_option( 'mwb_wsfw_wallet_rest_api_keys', $wallet_api_keys );  
    if ( $result ) {
        $msfw_wpg_error_text = esc_html__( 'API Key generated successfully.', 'wallet-system-for-woocommerce' );
        $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'success' );
    } else {
        $msfw_wpg_error_text = esc_html__( 'API Key is not created', 'wallet-system-for-woocommerce' );
        $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
    }
		
}


?>
 <div class="mwb-wsfw-gen-section-form mwb-wsfw-wallet-system-rest-api"> 
    <h4><?php esc_html_e( 'REST API keys' , 'wallet-system-for-woocommerce' ); ?></h4> 
    <?php 
    $rest_api_keys = get_option( 'mwb_wsfw_wallet_rest_api_keys', '' );
    if ( empty( $rest_api_keys )  || ! is_array( $rest_api_keys ) ){ ?>
        <form action="" method="POST" > 
            <div class="wpg-secion-wrap">
                <div class="mwb-form-group">
                    <div class="mwb-form-group__control">

            <p><?php esc_html_e( 'REST API allows external apps to view and manage wallet. Access is granted only to those with valid API keys.' , 'wallet-system-for-woocommerce' ); ?></p>
                        <input type="submit" class="mwb-btn mwb-btn__filled" name="generate_api_key"  value="Generate API key">
                    </div>
                </div>
            </div>
        </form>
    <?php
    } else { ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <label><?php esc_html_e( 'Consumer key', 'wallet-system-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="consumer_key" class="wsfw-number-class" value="<?php esc_attr_e( $rest_api_keys['consumer_key'], 'wallet-system-for-woocommerce' ); ?>" disabled >
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?php esc_html_e( 'Consumer secret', 'wallet-system-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="consumer_secret" class="wsfw-number-class" value="<?php esc_attr_e( $rest_api_keys['consumer_secret'], 'wallet-system-for-woocommerce' ); ?>" disabled >
                    </td>
                </tr>
            </tbody>
        </table>
        <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=wallet-system-rest-api' ) ); ?>&action=delete_api_keys" class="mwb-btn mwb-btn__filled" ><?php esc_html_e( 'Delete API Keys', 'wallet-system-for-woocommerce' ); ?></a></p>
    <?php }
    ?>
    
</div>
<div class="mwb-overview__wrapper">
    <div class="mwb-overview__banner">
        <img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>admin/image/Wallet for WooCommerce_Banner.png" alt="Overview banner image">
    </div>
    <div class="mwb-overview__content">
        <div class="mwb-overview__content-description">
            <h2><?php echo esc_html_e( 'What is Wallet System for WooCommerce Plugin? ', 'wallet-system-for-woocommerce' ); ?></h2>
            <p>
                <?php
                esc_html_e(
                    'Wallet System for WooCommerce is a digital wallet plugin. It allows your registered customers to create a digital wallet on your WooCommerce store. Customers can purchase your products and services using the digital wallet amount. The customers can add money to their WooCommerce wallet through the available payment methods. And also, see the list of Transactions made using the wallet money.',
                    'wallet-system-for-woocommerce'
                );
                ?>
            </p>
            <h3><?php esc_html_e( 'With our Wallet System for WooCommerce, You Can:', 'wallet-system-for-woocommerce' ); ?></h3>
            <ul class="mwb-overview__features">
                <li><?php esc_html_e( 'Add or remove funds to the wallets of your customers in bulk', 'wallet-system-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'Notify customers on every wallet transaction, wallet top-up, and wallet amount deduction through email notifications.', 'wallet-system-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'View the wallet transaction history and wallet balance of your customers.', 'wallet-system-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'View all wallet recharge orders (top-up by customers) in a separate order list.', 'wallet-system-for-woocommerce' ); ?></li>
                <li><?php esc_html_e( 'Allow your customers to transfer their wallet amount into other customers’ wallets.', 'wallet-system-for-woocommerce' ); ?></li>
                <li><?php _e( 'Have compatibility with the <a href="https://wordpress.org/plugins/invoice-system-for-woocommerce/" target="blank" >Invoice System for WooCommerce</a>.', 'wallet-system-for-woocommerce' ); ?></li>
            </ul>
        </div> 
        <h2> <?php esc_html_e( 'The Free Plugin Benefits', 'wallet-system-for-woocommerce' ); ?></h2>
        <div class="mwb-overview__keywords">
            <div class="mwb-overview__keywords-item">
                <div class="mwb-overview__keywords-card">
                    <div class="mwb-overview__keywords-image">
                        <img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Top-up_Payment_methods.jpg' ); ?>" alt="Top-up Payment methods">
                    </div>
                    <div class="mwb-overview__keywords-text">
                        <h3 class="mwb-overview__keywords-heading"><?php echo esc_html_e( 'Top-up Payment methods', 'wallet-system-for-woocommerce' ); ?></h3>
                        <p class="mwb-overview__keywords-description">
                            <?php
                            esc_html_e(
                                'Your customers can top-up funds into their WooCommerce wallets using any payment method allowed on your WooCommerce store. It provides flexibility to your customers as they can recharge their wallets using different payment methods.',
                                'wallet-system-for-woocommerce'
                            );
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="mwb-overview__keywords-item">
                <div class="mwb-overview__keywords-card">
                    <div class="mwb-overview__keywords-image">
                        <img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Wallet_Transaction_History_for_Customers.jpg' ); ?>" alt="Wallet Transaction">
                    </div>
                    <div class="mwb-overview__keywords-text">
                        <h3 class="mwb-overview__keywords-heading"><?php echo esc_html_e( 'Wallet Transaction History for Customers', 'wallet-system-for-woocommerce' ); ?></h3>
                        <p class="mwb-overview__keywords-description"><?php echo esc_html_e( 'The wallet system is secure and transparent. Customers can see their transactions made using the wallet. The transaction list contains debit and credit details. It allows your customers to track their spending and helps them check if any unauthorized transactions are made from their wallets. The wallet system is secure and transparent.', 'wallet-system-for-woocommerce' ); ?></p>
                    </div>
                </div>
            </div>
            <div class="mwb-overview__keywords-item">
                <div class="mwb-overview__keywords-card">
                    <div class="mwb-overview__keywords-image">
                        <img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Wallet_as_Payment_Method.jpg' ); ?>" alt="Wallet as a Payment Method">
                    </div>
                    <div class="mwb-overview__keywords-text">
                        <h3 class="mwb-overview__keywords-heading"><?php echo esc_html_e( 'Wallet as a Payment Method', 'wallet-system-for-woocommerce' ); ?></h3>
                        <p class="mwb-overview__keywords-description">
                            <?php
                            echo esc_html_e(
                                'Your Customers’ Wallet will work as a payment method only if the wallet amount is greater than the total order value. It will show in the payment method selection. It provides your customers a smooth shopping experience and reminds them to keep their wallets topped up.',
                                'wallet-system-for-woocommerce'
                            );
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="mwb-overview__keywords-item">
                <div class="mwb-overview__keywords-card mwb-card-support">
                    <div class="mwb-overview__keywords-image">
                        <img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Wallet_as_Discount.jpg' ); ?>" alt="Wallet as a Discount">
                    </div>
                    <div class="mwb-overview__keywords-text">
                        <h3 class="mwb-overview__keywords-heading"><?php echo esc_html_e( 'Wallet as a Discount', 'wallet-system-for-woocommerce' ); ?></h3>
                        <p class="mwb-overview__keywords-description">
                            <?php
                            esc_html_e(
                                "The wallet system provides benefits to customers even if their wallet amount is low. If your customers’ wallet amount is less than the total order value, then it will appear in the order details sections during the checkout, and customers can use it to get discounts.",
                                'wallet-system-for-woocommerce'
                            );
                            ?>
                        </p>
                    </div>
                    <a href="https://makewebbetter.com/contact-us/" title=""></a>
                </div>
            </div>
            <div class="mwb-overview__keywords-item">
                <div class="mwb-overview__keywords-card">
                    <div class="mwb-overview__keywords-image">
                        <img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL . 'admin/image/Icons_Wallet_Amount_Withdrawal.jpg' ); ?>" alt="Wallet Amount Withdrawal">
                    </div>
                    <div class="mwb-overview__keywords-text">
                        <h3 class="mwb-overview__keywords-heading"><?php echo esc_html_e( 'Wallet Amount Withdrawal', 'wallet-system-for-woocommerce' ); ?></h3>
                        <p class="mwb-overview__keywords-description">
                            <?php
                            echo esc_html_e(
                                'Customers can withdraw their wallet amount into their bank account. They have to file a withdrawal request and provide you their account details.',
                                'wallet-system-for-woocommerce'
                            );
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>