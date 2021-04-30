<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to create rest api for viewing and managing wallet
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

if ( isset( $_GET['action'] ) && ( 'delete_api_keys' ===  $_GET['action'] ) ) {
    $result = delete_option( 'mwb_wsfw_wallet_rest_api_keys' );  
    if ( $result ) {
        wp_safe_redirect( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=wallet-system-rest-api&wsfw_tab=wallet-system-rest-api' ) );
        $msfw_wpg_error_text = esc_html__( 'API Key deleted successfully.', 'wallet-system-for-woocommerce' );
        $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'success' );
    } else {
        wp_safe_redirect( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=wallet-system-rest-api&wsfw_tab=wallet-system-rest-api' ) );
        $msfw_wpg_error_text = esc_html__( 'API Key is not deleted', 'wallet-system-for-woocommerce' );
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

            <p><?php esc_html_e( 'REST API allows external apps to view and manage wallet. Access is granted only to those with valid API keys.', 'wallet-system-for-woocommerce' ); ?></p>
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
                        <label><?php esc_html_e( 'Consumer Key', 'wallet-system-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="consumer_key" class="wsfw-number-class" value="<?php esc_attr_e( $rest_api_keys['consumer_key'], 'wallet-system-for-woocommerce' ); ?>" disabled >
                    </td>
                </tr>
                <tr>
                    <th>
                        <label><?php esc_html_e( 'Consumer Secret', 'wallet-system-for-woocommerce' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="consumer_secret" class="wsfw-number-class" value="<?php esc_attr_e( $rest_api_keys['consumer_secret'], 'wallet-system-for-woocommerce' ); ?>" disabled >
                    </td>
                </tr>
            </tbody>
        </table>
        <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu&wsfw_tab=wallet-system-rest-api' ) ); ?>&action=delete_api_keys" class="mwb-btn mwb-btn__filled delete_keys" ><?php esc_html_e( 'Delete API Keys', 'wallet-system-for-woocommerce' ); ?></a></p>
    <?php }
    ?>
    <h4><?php esc_html_e( 'REST API details', 'wallet-system-for-woocommerce' ); ?></h4>
    <p><?php _e( '<strong>Base Url for accesing customer wallet :</strong> {your_url}/wp-json/wsfw-route/v1/wallet/', 'wallet-system-for-woocommerce' ); ?></p>
    <p><?php esc_html_e( 'Example : https://www.example.com/wp-json/wsfw-route/v1/wallet/ ', 'wallet-system-for-woocommerce' ); ?></p>

    <h5><?php esc_html_e( 'Authentication', 'wallet-system-for-woocommerce' ); ?></h5> 
    <p><?php _e( 'For authentication you need Consumer Key  <strong>consumer_key</strong> and Consumer Secret  <strong>consumer_secret</strong> keys. Response on wrong api details:', 'wallet-system-for-woocommerce' ); ?></p>
    <?php
     _e( '<pre>

    {
    	"code": "rest_forbidden",
    	"message": "Sorry, your key details are incorrect.",
    	"data": {
    	    "status": 401
    	}
    }	
    </pre>', 'wallet-system-for-woocommerce' );
    ?>
	<h5><?php esc_html_e( 'Retrieve Users', 'wallet-system-for-woocommerce' ); ?></h5> 
    <p><?php esc_html_e( 'Retrieves all the users with their details. HTTP request is:', 'wallet-system-for-woocommerce' ); ?></p>
    <p><?php _e( '<strong><code>GET {your_url}/wp-json/wsfw-route/v1/wallet/users?consumer_key=XXXX&consumer_secret=XXXX</code></strong> where &lt;id&gt; is an user id of user.', 'wallet-system-for-woocommerce' ); ?><p>
    <p><?php esc_html_e( 'Example : https://www.example.com/wp-json/wsfw-route/v1/wallet/users?consumer_key=XXXX&consumer_secret=XXXX', 'wallet-system-for-woocommerce' ); ?> </p>
    <p><?php esc_html_e( 'JSON response example:', 'wallet-system-for-woocommerce' ); ?></p>
    <?php
     _e( '<pre>

    [
        {
            "user_id": 1,
            "user_name": "root",
            "user_email": "dev-email@gmail.com",
            "user_role": "administrator",
            "wallet_balance": "160.2"
        },
        {
            "user_id": 2,
            "user_name": "Demo",
            "user_email": "demo_user@gmail.com",
            "user_role": "customer",
            "wallet_balance": "225"
        }
    ]
	</pre>', 'wallet-system-for-woocommerce' );
    ?>
    <h5><?php esc_html_e( 'Retrieve particular user wallet amount', 'wallet-system-for-woocommerce' ); ?></h5> 
    <p><?php esc_html_e( 'Retrieves wallet balance an existing contact. HTTP request is:', 'wallet-system-for-woocommerce' ); ?></p>
    <p><?php _e( '<strong><code>GET {your_url}/wp-json/wsfw-route/v1/wallet/&lt;id&gt;?consumer_key=XXXX&consumer_secret=XXXX</code></strong> where &lt;id&gt; is an user id of user.', 'wallet-system-for-woocommerce' ); ?><p>
    <p><?php esc_html_e( 'Example : https://www.example.com/wp-json/wsfw-route/v1/wallet/1?consumer_key=XXXX&consumer_secret=XXXX', 'wallet-system-for-woocommerce' ); ?> </p>
    <p><?php esc_html_e( 'JSON response example:', 'wallet-system-for-woocommerce' ); ?></p>
    
    <?php
     _e( '<pre>

    "23.34"
	</pre>', 'wallet-system-for-woocommerce' );
    ?>
    <h5><?php esc_html_e( 'Retrieve particular user wallet transactions', 'wallet-system-for-woocommerce' ); ?></h5> 
    <p><?php esc_html_e( 'Retrieves all transactions related to wallet. HTTP request is:', 'wallet-system-for-woocommerce' ); ?></p>
    <p><?php _e( '<strong><code>GET {your_url}/wp-json/wsfw-route/v1/wallet/transactions/&lt;id&gt;?consumer_key=XXXX&consumer_secret=XXXX</code></strong> where &lt;id&gt; is an user id of user.', 'wallet-system-for-woocommerce' ); ?><p>
    <p><?php esc_html_e( 'Example : https://www.example.com/wp-json/wsfw-route/v1/wallet/transactions/1?consumer_key=XXXX&consumer_secret=XXXX ', 'wallet-system-for-woocommerce' ); ?></p>
    <p><?php esc_html_e( 'JSON response example:', 'wallet-system-for-woocommerce' ); ?></p>
    <?php
     _e( '<pre> 
    [
    	{
	    "Id": "90",
	    "user_id": "1",
	    "amount": "22",
	    "transaction_type": "Debited by admin",
	    "payment_method": "Manually By Admin",
	    "transaction_id": "",
	    "note": "",
	    "date": "2021-04-22 20:16:23"
        },
        {
            "Id": "94",
            "user_id": "1",
            "amount": "12",
            "transaction_type": "Wallet credited through purchase #159",
            "payment_method": "bacs",
            "transaction_id": "159",
            "note": "",
            "date": "2021-04-22 21:35:47"
        }
    ]
	</pre>', 'wallet-system-for-woocommerce' );
    ?>

	<h5><?php esc_html_e( 'Update wallet of user', 'wallet-system-for-woocommerce' ); ?></h5> 
    <p><?php esc_html_e( 'This allow you to update(credit/debit) wallet of particular user. HTTP request is:', 'wallet-system-for-woocommerce' ); ?></p>
    <p><?php _e( '<strong><code>PUT {your_url}/wp-json/wsfw-route/v1/wallet/&lt;id&gt;</code></strong> where &lt;id&gt; is an user id of user.', 'wallet-system-for-woocommerce' ); ?><p>
    <p><?php _e( '<strong>Required Headers</strong>', 'wallet-system-for-woocommerce' ); ?></p>
	<p><?php _e( '<code>Content-Type: application/json</code>', 'wallet-system-for-woocommerce' ); ?></p>
	<p><?php esc_html_e( 'Example : https://www.example.com/wp-json/wsfw-route/v1/wallet/1', 'wallet-system-for-woocommerce' ); ?></p>
    <p>
    <?php
     _e( "<pre>

    curl -X PUT -d 'amount=29&action=credit' 'https://www.example.com/wp-json/wsfw-route/v1/wallet/1' \
    --header 'Content-Type: application/json'
	</pre>", 'wallet-system-for-woocommerce' );
    ?>	
	</p>
	<p><strong><?php esc_html_e( 'Request Parameters', 'wallet-system-for-woocommerce' ); ?></strong></p>
	<table class="mwb-wsfw-rest-api-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Parameter', 'wallet-system-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Type', 'wallet-system-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Description', 'wallet-system-for-woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php esc_html_e( 'id', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'integer', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'Unique user id of user(required) will pass on url.' , 'wallet-system-for-woocommerce' ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'amount', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'number', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'Wallet transaction amount(required)', 'wallet-system-for-woocommerce' ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'action', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'string', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'Wallet transaction type(required) value will be either "credit" or "debit"', 'wallet-system-for-woocommerce' ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'consumer_key', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'string', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'Merchant Consumer Key(required)', 'wallet-system-for-woocommerce' ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'consumer_secret', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'string', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'Merchant Consumer Secret(required)', 'wallet-system-for-woocommerce' ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'transaction_detail', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'string', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'Wallet transaction details(required)', 'wallet-system-for-woocommerce' ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'payment_method', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'string', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'Payment method used', 'wallet-system-for-woocommerce' ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'note', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'string', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'Note during wallet transfer', 'wallet-system-for-woocommerce' ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'order_id', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'integer', 'wallet-system-for-woocommerce' ); ?></td>
				<td><?php esc_html_e( 'If amount is deducted when wallet used as payment gateway', 'wallet-system-for-woocommerce' ); ?></td>
			</tr>

		</tbody>
	</table>
	<p><?php _e( '<strong>Note:</strong> id is required in all api request.', 'wallet-system-for-woocommerce' ); ?></p>
	<p><?php esc_html_e( 'JSON response example:', 'wallet-system-for-woocommerce' ); ?></p>
    <?php
     _e( '<pre> 
    {
    	"response": "success",
    	"balance": "487.41",
    	"transaction_id": 156
    }
	</pre>', 'wallet-system-for-woocommerce' );
    ?>

</div>
