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

if ( isset( $_POST['update_withdrawal_request'] ) && ! empty( $_POST['update_withdrawal_request'] ) ) {
	unset( $_POST['update_withdrawal_request'] );
	$update = true;
	if ( empty( $_POST['withdrawal_id'] ) ) {
		$mwb_wsfw_error_text = esc_html__( 'Withdrawal Id is not given', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
		$update = false;
	}
	if ( empty( $_POST['user_id'] ) ) {
		$mwb_wsfw_error_text = esc_html__( 'User Id is not given', 'wallet-system-for-woocommerce' );
		$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
		$update = false;
	}
	if ( $update ) {

		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$updated_status = sanitize_text_field( $_POST['mwb-wpg-gen-table_status'] );
		$withdrawal_id = sanitize_text_field( $_POST['withdrawal_id'] );
        $user_id = sanitize_text_field( $_POST['user_id'] );
		$withdrawal_request = get_post( $withdrawal_id );
		$request_status = $withdrawal_request->post_status;
		if ( 'approved' === $updated_status ) {
			$withdrawal_amount = get_post_meta( $withdrawal_id, 'mwb_wallet_withdrawal_amount', true );
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

					$send_email_enable = get_option( 'mwb_wsfw_enable_email_notification_for_wallet_update', '' );
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$user = get_user_by( 'id', $user_id );
						$name = $user->first_name . ' ' . $user->last_name;
						$mail_text = sprintf( "Hello %s,<br/>", $name );
						$mail_text .= __( wc_price( $withdrawal_amount ).' has been debited from wallet through your withdrawing request.', 'wallet-system-for-woocommerce' );
						$to = $user->user_email;
						$from = get_option( 'admin_email' );
						$subject = "Wallet updating notification";
						$headers = 'From: '. $from . "\r\n" .
							'Reply-To: ' . $to . "\r\n";
						$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
					}

				}
				$transaction_type = 'Wallet debited through user withdrawing request <a href="#" >#' . $withdrawal_id . '</a>';
				$transaction_data = array(
					'user_id'          => $user_id,
					'amount'           => $withdrawal_amount,
					'payment_method'   => 'Manually By Admin',
					'transaction_type' => htmlentities( $transaction_type ),
					'order_id'         => $withdrawal_id,
					'note'             => '',
	
				);

				$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
				if ( $result ) {
					$mwb_wsfw_error_text = esc_html__( 'Wallet withdrawan request is approved for user #'.$user_id, 'wallet-system-for-woocommerce' );
					$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
				} else {
					$mwb_wsfw_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
					$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'error' );
				}
			};
		}
		if ( 'rejected' === $updated_status ) {
			$withdrawal_amount = get_post_meta( $withdrawal_id, 'mwb_wallet_withdrawal_amount', true );
			if ( $user_id ) {
				$withdrawal_request->post_status = 'rejected';
				wp_update_post( $withdrawal_request );
				delete_user_meta( $user_id, 'disable_further_withdrawal_request' );
				$mwb_wsfw_error_text = esc_html__( 'Wallet withdrawan request is rejected for user #'.$user_id, 'wallet-system-for-woocommerce' );
				$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
			};
		}
		if ( 'pending' === $updated_status ) {
			$withdrawal_amount = get_post_meta( $withdrawal_id, 'mwb_wallet_withdrawal_amount', true );
			if ( $user_id ) {
				$withdrawal_request->post_status = 'pending';
				wp_update_post( $withdrawal_request );
				$mwb_wsfw_error_text = esc_html__( 'Wallet withdrawan request status is changed to pending for user #'.$user_id, 'wallet-system-for-woocommerce' );
				$wsfw_mwb_wsfw_obj->mwb_wsfw_plug_admin_notice( $mwb_wsfw_error_text, 'success' );
			};
		}
	}
	
}

?>
<!--  template file for admin settings. -->


<div class="mwb-wpg-withdrawal-section-search">

    <table>
            <tbody>
                <tr>
                    <th><?php esc_html_e( 'Search', 'wallet-system-for-woocommerce' ); ?></td>
                    <td><input type="text" id="search_in_table"></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Filter By:', 'wallet-system-for-woocommerce' ); ?></td>
                    <td>
						<select id="filter_status" >
							<option value=""><?php esc_html_e( 'status', 'wallet-system-for-woocommerce' ); ?></option>
							<option value="approved"><?php esc_html_e( 'approved', 'wallet-system-for-woocommerce' ); ?></option>
							<option value="pending"><?php esc_html_e( 'pending', 'wallet-system-for-woocommerce' ); ?></option>
							<option value="rejected"><?php esc_html_e( 'rejected', 'wallet-system-for-woocommerce' ); ?></option>
						</select>
					</td>
                </tr>
				<tr>
                    <td><span id="clear_table" ><?php esc_html_e( 'Clear', 'wallet-system-for-woocommerce' ); ?></span></td>
                </tr>
            </tbody>
        </table>


</div>

<div class="mwb-wpg-gen-section-table-wrap mwb-wpg-withdrawal-section-table">
	<h4><?php esc_html_e( 'Withdrawal Requests' , 'wallet-system-for-woocommerce' ); ?></h4>
	<div class="mwb-wpg-gen-section-table-container demo">
		<table id="mwb-wpg-gen-table1" class="mwb-wpg-gen-section-table dt-responsive" style="width:100%">
			<thead>
				<tr>
					<th><?php esc_html_e( '#', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Withdrawal ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'User ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Status1', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Withdrawal Amount', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
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
								<td><img src="<?php echo WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL; ?>admin/image/eva_close-outline.svg"><?php echo $i; ?></td>
								<td><?php echo $request->ID; ?></td>
								<td><?php echo $user_id;  ?></td>
								<td><?php esc_html_e( $request->post_status, 'wallet-system-for-woocommerce' ); ?></td>
								<td>
									<?php 
									$withdrawal_status = $request->post_status;
									if ( 'approved' === $withdrawal_status ) { ?>
										<span class="approved" ><?php esc_html_e( 'approved', 'wallet-system-for-woocommerce' ); ?></span>
										<?php 
									} elseif ( 'rejected' === $withdrawal_status ) { ?>
										<span class="rejected" ><?php esc_html_e( 'rejected', 'wallet-system-for-woocommerce' ); ?></span>
									<?php } else { ?> 
									<form action="" method="POST">
										<select onchange="this.className=this.options[this.selectedIndex].className" name="mwb-wpg-gen-table_status" id="mwb-wpg-gen-table_status" aria-controls="mwb-wpg-gen-section-table" class="<?php esc_html_e( $request->post_status, 'wallet-system-for-woocommerce' ); ?>">
											<option class="approved" value="approved" >&nbsp;&nbsp;<?php esc_html_e( 'approved', 'wallet-system-for-woocommerce' ); ?></option>
											<option class="pending" value="pending" <?php selected( 'pending', $request->post_status, true); ?> disabled  >&nbsp;&nbsp;<?php esc_html_e( 'pending', 'wallet-system-for-woocommerce' ); ?></option>
											<option class="rejected" value="rejected" >&nbsp;&nbsp;<?php esc_html_e( 'rejected', 'wallet-system-for-woocommerce' ); ?></option>
										</select>
										<input type="hidden" name="withdrawal_id" value="<?php esc_attr_e( $request->ID, 'wallet-system-for-woocommerce' ); ?>" />
										<input type="hidden" name="user_id" value="<?php esc_attr_e( $user_id, 'wallet-system-for-woocommerce' ); ?>" />
										<div id="overlay" style="display:none;width:69px;height:89px;">
											<img src='<?php echo WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL."admin/image/loader.gif"; ?>' width="64" height="64" /><br>Loading..
										</div>
									</form>
									<?php }
									?>
								</td>
								<td><?php echo wc_price( $withdrawal_amount ); ?></td>
								<td><?php $date_format = get_option( 'date_format', 'm/d/Y' ); $date = date_create($request->post_date);
								esc_html_e( date_format( $date, $date_format ), 'wallet-system-for-woocommerce' );
								?></td>					
								<td><?php esc_html_e( get_post_meta( $request->ID , 'mwb_wallet_note' , true ), 'wallet-system-for-woocommerce' );
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