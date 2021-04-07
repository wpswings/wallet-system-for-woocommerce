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

if ( isset( $_POST['wallet_topup_setting'] ) && ! empty( $_POST['wallet_topup_setting'] ) ) {
	unset( $_POST['wallet_topup_setting'] );
	
	foreach ( $_POST as $key => $value ) {
		update_option( $key, $value );
	}
	if ( ! array_key_exists( 'wsfw_enable_wallet_recharge', $_POST ) ) {
		update_option( 'wsfw_enable_wallet_recharge', '' );
	}
	$mwb_wsfw_error_text = esc_html__( 'Save settings.', 'wallet-system-for-woocommerce' );
	$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
	
}

if ( isset( $_POST['import_wallets'] ) && ! empty( $_POST['import_wallets'] ) ) {
	unset( $_POST['import_wallets'] );
	if ( ! empty( $_FILES['import_wallet_for_users'] ) ) {
		$image_name = $_FILES['import_wallet_for_users']['name'];
		$image_size = $_FILES['import_wallet_for_users']['size'];
		$imageFileType = strtolower( pathinfo( $image_name, PATHINFO_EXTENSION ) );
		// Allow certain file formats
		if ( $imageFileType != "csv" ) {
			$mwb_wsfw_error_text = esc_html__( 'Sorry, only CSV file is allowed.', 'wallet-system-for-woocommerce' );
			$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
		} else{
			
			$file = fopen( $_FILES['import_wallet_for_users']['tmp_name'], 'r');
			$users_wallet = array();
			$first_row   = fgetcsv( $file );
			$id      = $first_row[0];
			$balance = $first_row[1];
			if ( 'User Id' != $id && 'Wallet Balance' != $balance ) {
				$mwb_wsfw_error_text = esc_html__( 'You have not selected correct file(fields are not matching)', 'wallet-system-for-woocommerce' );
				$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
			} else {
				while ( ! feof( $file ) ) {
					$user_data   = fgetcsv( $file );
	
					$id      = $user_data[0];
					$balance = $user_data[1];
					if ( 'User Id' == $id && 'Wallet Balance' == $balance ) {
						continue;
					} else {
						$user = get_user_by( 'id', $id );
						if ( $user ) {
							$current_balance = get_user_meta( $id, 'mwb_wallet', true );
							if ( $current_balance < $balance ) {
								$transaction_type =  'Wallet credited during importing wallet';
							} elseif ( $current_balance == $balance ) {
								$transaction_type =  'No money is added/deducted from wallet';
							} else {
								$transaction_type =  'Wallet debited during importing wallet';
							}
							update_user_meta( $id, 'mwb_wallet', $balance );

							$transaction_data = array(
								'user_id'          => $id,
								'amount'           => $balance,
								'payment_method'   => 'Through importing Wallet',
								'transaction_type' => $transaction_type,
								'order_id'         => '',
					
							);
							$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
							$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );


						}
					}
					
				}
				$mwb_wsfw_error_text = esc_html__( 'Updated wallet of users', 'wallet-system-for-woocommerce' );
				$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
			}
			
			fclose($file);

			
		}

	} else {
		$mwb_wsfw_error_text = esc_html__( 'Please select any CSV file', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
	}
}

if ( isset( $_POST['update_wallet'] ) && ! empty( $_POST['update_wallet'] ) ) {
	unset( $_POST['update_wallet'] );
	$update = true;
	if ( empty( $_POST['wsfw_wallet_amount_for_users'] ) ) {
		$mwb_wsfw_error_text = esc_html__( 'Please enter any amount', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
		$update = false;
	} 
	if ( empty( $_POST['wsfw_wallet_action_for_users'] ) ) {
		$mwb_wsfw_error_text = esc_html__( 'Please select any action', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
		$update = false;
	}
	if ( $update ) {
		$updated_amount = sanitize_text_field( $_POST['wsfw_wallet_amount_for_users'] );
		$wallet_action = sanitize_text_field( $_POST['wsfw_wallet_action_for_users'] );
		update_option( 'wsfw_wallet_amount_for_users', $updated_amount );
		update_option( 'wsfw_wallet_action_for_users', $wallet_action );
		$wallet_amount = get_option( 'wsfw_wallet_amount_for_users', '' );
		$wallet_option = get_option( 'wsfw_wallet_action_for_users', '' );
		if ( isset( $wallet_amount ) && ! empty( $wallet_amount ) ) {
			
			$users = get_users();
			foreach ( $users as $user ) {
				$user_id = $user->ID;
				$wallet = get_user_meta( $user_id, 'mwb_wallet', true );
				if ( 'credit' == $wallet_option ) { 
					$wallet += $wallet_amount;
					$wallet = update_user_meta( $user_id, 'mwb_wallet', $wallet );
					$transaction_type = 'Credited by admin';
				} elseif ( 'debit' == $wallet_option ) { 
					if ( $wallet < $wallet_amount ) {
						$wallet = 0;
					} else {
						$wallet -= $wallet_amount;
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
		
				);
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
				
			}
		}
	
		if ( $result ) {
            $mwb_wsfw_error_text = esc_html__( 'Updated wallet of users', 'wallet-system-for-woocommerce' );
		    $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
        } else {
            $mwb_wsfw_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
		    $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
        }
	}
	
}

$wsfw_wallet_topup_settings = apply_filters( 'wsfw_wallet_settings_array', array() );
$wsfw_update_wallet = apply_filters( 'wsfw_update_wallet_array', array() );
$wsfw_import_settings = apply_filters( 'wsfw_import_wallet_array', array() );


?>
<!--  template file for admin settings. -->

<form action="" method="POST" class="mwb-wpg-gen-section-form">
	<div class="wpg-secion-wrap">
    <h3><?php esc_html_e( 'Wallet TopUp Setting' , 'wallet-system-for-woocommerce' ); ?></h3>
		<?php
		$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_wallet_topup_settings );
		echo esc_html( $wsfw_general_html );
		?>
	</div>
</form>
<form action="" method="POST" class="mwb-wpg-gen-section-form" onsubmit="return confirm('Are you sure to update wallet of all users?');" >
	<div class="wpg-secion-wrap">
		<h3><?php esc_html_e( 'Edit wallet of all users at once' , 'wallet-system-for-woocommerce' ); ?></h3>
		<?php
		$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_update_wallet );
		echo esc_html( $wsfw_general_html );
		?>
	</div>
</form>

<button class="mdc-button mdc-button--raised mdc-ripple-upgraded" id="export_user_wallet" > <span class="mdc-button__ripple"></span>
	<span class="mdc-button__label"><?php esc_html_e( 'Export user\'s wallet' , 'wallet-system-for-woocommerce' ); ?></span>
</button>

<form action="" method="POST" class="mwb-wpg-gen-section-form" enctype="multipart/form-data">
	<div class="wpg-secion-wrap">
		<h3><?php esc_html_e( 'Import wallets for user' , 'wallet-system-for-woocommerce' ); ?></h3>
		<?php
		$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_import_settings );
		echo esc_html( $wsfw_general_html );
		?>
	</div>
</form>