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
<div class="mwb-wpg-gen-section-form-wrapper">
	<form action="" method="POST" class="mwb-wpg-gen-section-form" onsubmit="return confirm('Are you sure to update wallet of all users?');" >
		<div class="wpg-secion-wrap">
			<h3><?php esc_html_e( 'Edit wallet of all users at once' , 'wallet-system-for-woocommerce' ); ?></h3>
			<?php
			$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_update_wallet );
			echo esc_html( $wsfw_general_html );
			?>
		</div>
		<div class="mwb_wallet-update--popupwrap">
			<div class="mwb_wallet-update-popup">
				<h3>Excepteur sint occaecat cupidatat non proident.</h3>
				<div class="mwb_wallet-update-popup-btn">
					<a href="#" class="mwb-btn mwb-btn__filled">Yes, I’m Sure</a>
					<a href="#">Not now</a>
				</div>
			</div>
		</div>
	</form>

	<button class="mdc-ripple-upgraded" id="export_user_wallet" > <img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/export.svg">
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
</div>
<div class="mwb_wallet-edit--popupwrap">
	<div class="mwb_wallet-edit-popup">
		<div class="mwb_wallet-edit-popup-content">
			<div class="mwb_wallet-edit-popup-amount">
				<div class="mwb_wallet-edit-popup-label">
					<label for="mwb_wallet-edit-popup-input" class="mwb_wallet-edit-popup-input">Select Amount ($):</label>
				</div>
				<div class="mwb_wallet-edit-popup-control">
					<input type="number" name="mwb_wallet-edit-popup-input" class="mwb_wallet-edit-popup-fill">
				</div>
			</div>
			<div class="mwb_wallet-edit-popup-amount">
				<div class="mwb_wallet-edit-popup-label">
					<label for="mwb_wallet-edit-popup-card" class="mwb_wallet-edit-popup-card">Select Card:</label>
				</div>
				<div class="mwb_wallet-edit-popup-control">
					<div class="mwb-form-select-card">
						<input type="radio" id="debit" name="card" value="debit">
						<label for="debit">Debit Card</label>
					</div>
					<div class="mwb-form-select-card">
						<input type="radio" id="credit" name="card" value="credit">
						<label for="credit">Credit Card</label>
					</div>
				</div>
			</div>
		</div>
		<div class="mwb_wallet-edit-popup-btn">
			<a href="#" class="mwb-btn mwb-btn__filled">Update</a>
		</div>
	</div>
</div>
<div class="mwb-wpg-gen-section-table-wrap">
	<h4>Wallet</h4>
	<div class="mwb-wpg-gen-section-table-container">
		<table id="mwb-wpg-gen-table" class="mwb-wpg-gen-section-table dt-responsive" style="width:100%">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Email</th>
					<th>Role</th>
					<th>Amount</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>2</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>3</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>4</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>5</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>6</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>7</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>7</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>7</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>7</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>7</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
				<tr>
					<td>7</td>
					<td>copy text</td>
					<td>123userdemo@mysite.com</td>
					<td>copy text</td>
					<td>123456</td>
					<td>
						<span>
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/edit.svg">
							<img src="http://localhost/wallet-system/wp-content/plugins/wallet-system-for-woocommerce/admin/image/eye.svg">
						</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>