<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to show wallet orders.
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

require_once WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_PATH. 'admin/class-wallet-orders.php';
$wallet_orders = new Wallet_Orders_List();

?>
<div class="wrap">
    <h1 class="wp-heading-inline"> <?php esc_html_e( 'Wallet Recharge Orders', 'wallet-system-for-woocommerce' ); ?></h1>
    <div id="wrapper" class="mwb_wcb_all_trans_container meta-box-sortables ui-sortable wallet_shop_order">
        <form action="" method="POST">
        
            <?php 
            $wallet_orders->views();
            if( isset($_GET['s']) ){
                $wallet_orders->prepare_items($_GET['s']);
            } else {
                $wallet_orders->prepare_items();
            }
            $wallet_orders->search_box( __( 'Search Order', 'wallet-system-for-woocommerce' ), 'search_id' );
            //Table of elements
            $wallet_orders->display();
            ?>
        </form>
    </div>
</div>


