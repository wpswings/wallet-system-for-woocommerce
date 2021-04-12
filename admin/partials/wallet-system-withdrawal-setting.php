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

if ( isset( $_POST['save_withdrawn_settings'] ) && ! empty( $_POST['save_withdrawn_settings'] ) ) {
	unset( $_POST['save_withdrawn_settings'] );
	if ( empty( $_POST['wallet_withdraw_methods'] ) ) {
		$mwb_wsfw_error_text = esc_html__( 'Select any payment method.', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
	} else {
		foreach ( $_POST as $key => $value ) {
			if ( 'wallet_withdraw_methods' === $key ) {
				$method = array();
				foreach( $value as $method_key => $method_value ) {
					
					$method[$method_key]['name'] = $method_value;
					$method[$method_key]['value'] = 1;
				}
				
				update_option( $key, $method );
			} else {
				update_option( $key, $value );
			}
			
		}
		$mwb_wsfw_error_text = esc_html__( 'Save settings.', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
	}
	
}

if ( isset( $_POST['update_withdrawal_request'] ) && ! empty( $_POST['update_withdrawal_request'] ) ) {
	unset( $_POST['update_withdrawal_request'] );
	$update = true;
	if ( empty( $_POST['withdrawal_id'] ) ) {
		$mwb_wsfw_error_text = esc_html__( 'Withdrawal Id is not given', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
		$update = false;
	}
	if ( empty( $_POST['user_id'] ) ) {
		$msfw_wpg_error_text = esc_html__( 'User Id is not given', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
		$update = false;
	}
	if ( $update ) {
		$updated_status = sanitize_text_field( $_POST['mwb-wpg-gen-table_status'] );
		$withdrawal_id = sanitize_text_field( $_POST['withdrawal_id'] );
        $user_id = sanitize_text_field( $_POST['user_id'] );
		$withdrawal_request = get_post( $withdrawal_id );
		$request_status = $withdrawal_request->post_status;
		if ( 'approved' === $updated_status ) {
			$withdrawal_amount = get_post_meta( $withdrawal_id, 'mwb_wallet_withdrawal_amount', true );
			$payment_method = get_post_meta( $withdrawal_id, 'wallet_payment_method', true );
			if ( $user_id ) {
				$walletamount = get_user_meta( $user_id, 'mwb_wallet', true );
				if ( $walletamount < $withdrawal_amount ) {
					$walletamount = 0;
				} else {
					$walletamount -= $withdrawal_amount;
				}
				$update_wallet = update_user_meta( $user_id, 'mwb_wallet', $walletamount );
				delete_user_meta( $user_id, 'disable_further_withdrawal_request' );
				if ( $update_wallet ) {
					$withdrawal_request->post_status = 'approved';
					wp_update_post( $withdrawal_request );
				}
				$transaction_type = 'Wallet debited through user withdrawing request <a href="#" >#' . $withdrawal_id . '</a>';
				$transaction_data = array(
					'user_id'          => $user_id,
					'amount'           => $withdrawal_amount,
					'payment_method'   => $payment_method,
					'transaction_type' => htmlentities( $transaction_type ),
					'order_id'         => $withdrawal_id,
	
				);
				$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
				$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
				if ( $result ) {
					$msfw_wpg_error_text = esc_html__( 'Wallet withdrawan request is approved for user #'.$user_id, 'wallet-system-for-woocommerce' );
					$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'success' );
				} else {
					$msfw_wpg_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
					$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
				}
			};
		}
		if ( 'rejected' === $updated_status ) {
			$withdrawal_amount = get_post_meta( $withdrawal_id, 'mwb_wallet_withdrawal_amount', true );
			if ( $user_id ) {
				$withdrawal_request->post_status = 'rejected';
				wp_update_post( $withdrawal_request );
				delete_user_meta( $user_id, 'disable_further_withdrawal_request' );
				$msfw_wpg_error_text = esc_html__( 'Wallet withdrawan request is rejected for user #'.$user_id, 'wallet-system-for-woocommerce' );
				$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'success' );
			};
		}
	}
	
}

$wsfw_withdrawal_settings = apply_filters( 'wsfw_wallet_withdrawal_array', array() );


?>
<!--  template file for admin settings. -->

<form action="" method="POST" class="mwb-wpg-gen-section-form">
	<div class="wpg-secion-wrap">
    <h3><?php esc_html_e( 'Wallet Withdrawal Setting' , 'wallet-system-for-woocommerce' ); ?></h3>
		<?php
		$wsfw_general_html = $wsfw_mwb_wsfw_obj->mwb_wsfw_plug_generate_html( $wsfw_withdrawal_settings );
		echo esc_html( $wsfw_general_html );
		?>
	</div>
</form>

<div class="mwb-wpg-gen-section-table-wrap mwb-wpg-withdrawal-section-table">
	<h4><?php esc_html_e( 'Withdrawal Requests' , 'wallet-system-for-woocommerce' ); ?></h4>
	<div class="mwb-wpg-gen-section-table-container">
		<table id="mwb-wpg-gen-table" class="mwb-wpg-gen-section-table dt-responsive" style="width:100%">
			<thead>
				<tr>
					<th><?php esc_html_e( '#', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Withdrawal ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'User ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Withdrawal Amount', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Update', 'wallet-system-for-woocommerce' ); ?></th>
					<!-- <th>Note</th> -->
				</tr>
			</thead>
			<tbody>
				<?php
				$args = array(  
					'post_type'      => 'wallet_withdrawal',
					'posts_per_page' => -1,
					'order'          => 'DESC',
					'orderby'        => 'ID',
					'post_status'    => array( 'approved', 'pending', 'rejected' ),
				);
				$withdrawal_requests = get_posts( $args );
				$i = 1;
				if ( ! empty( $withdrawal_requests ) ) {
					foreach( $withdrawal_requests as $request ) {
						$withdrawal_amount = get_post_meta( $request->ID, 'mwb_wallet_withdrawal_amount', true );
						$user_id           = get_post_meta( $request->ID, 'wallet_user_id', true );
						?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php echo $request->ID; ?></td>
								<td><?php echo $user_id;  ?></td>
								<td><?php echo wc_price( $withdrawal_amount ); ?></td>
								<td>
									<form action="" method="POST">
										<select onchange="this.className=this.options[this.selectedIndex].className" name="mwb-wpg-gen-table_status" aria-controls="mwb-wpg-gen-section-table" class="<?php esc_html_e( $request->post_status, 'wallet-system-for-woocommerce' ); ?>">
											<option class="approved" value="approved" <?php selected( 'approved', $request->post_status, true); ?> ><?php esc_html_e( 'approved', 'wallet-system-for-woocommerce' ); ?></option>
											<option class="pending" value="pending" <?php selected( 'pending', $request->post_status, true); ?> ><?php esc_html_e( 'pending', 'wallet-system-for-woocommerce' ); ?></option>
											<option class="rejected" value="rejected" <?php selected( 'rejected', $request->post_status, true); ?> ><?php esc_html_e( 'rejected', 'wallet-system-for-woocommerce' ); ?></option>
										</select>
										<input type="hidden" name="withdrawal_id" value="<?php esc_attr_e( $request->ID, 'wallet-system-for-woocommerce' ); ?>" />
										<input type="hidden" name="user_id" value="<?php esc_attr_e( $user_id, 'wallet-system-for-woocommerce' ); ?>" />
										<input type="submit" class="update" name="update_withdrawal_request" value="Update" >
									</form>
								</td>
								<td><?php $date = date_create($request->post_date);
								esc_html_e( date_format( $date,"d/m/Y"), 'wallet-system-for-woocommerce' );
								?></td>					
								<td><?php $date = date_create($request->post_modified);
								esc_html_e( date_format( $date,"d/m/Y"), 'wallet-system-for-woocommerce' );
								?></td>					
								<!-- <td>Lorem ipsum dolor sit amet, </td> -->
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
