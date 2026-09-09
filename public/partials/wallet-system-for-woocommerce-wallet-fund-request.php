<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$wallet_bal = get_user_meta( $user_id, 'wps_wallet', true );
$wallet_bal = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
$wallet_bal = apply_filters( 'wps_wsfw_show_converted_price', $wallet_bal );
$check = false;
$check = apply_filters( 'wps_wsfwp_pro_plugin_check', $check );
$wps_wsfwp_wallet_withdrawal_fee_amount = '';
$wps_wsfwp_wallet_withdrawal_paypal_enable = get_option( 'wps_wsfwp_wallet_withdrawal_paypal_enable' );


?>

<div class='content active'>

		<?php
		$wsfw_restrict_wallet_fund_request_kyc   = get_option( 'wsfw_restrict_wallet_fund_request_kyc' );
		$wsfw_enable_wallet_kyc = get_option( 'wsfw_enable_wallet_kyc' );
		$kyc_not_approved = false;
		if ( 'on' === $wsfw_restrict_wallet_fund_request_kyc && 'on' == $wsfw_enable_wallet_kyc ) {
			$wps_wallet_kyc_status    = get_user_meta( $user_id, 'key_verification_status', true );
			if ( 'pending' == $wps_wallet_kyc_status || 'rejected' == $wps_wallet_kyc_status || '' == $wps_wallet_kyc_status ) {
				$kyc_not_approved = true;
			}
		}
		if ( $kyc_not_approved ) {
			show_message_on_form_submit( esc_html__( 'You must complete KYC verification to use wallet funds request.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
			return;
		} else {
			?>
				<span id="wps_wallet_transfer_form">

					<!-- Select Method -->
					<p class="wps-wallet-field-container form-row form-row-wide">
						<label for="wps_wallet_fund_request_another_method"><?php esc_html_e( 'Select Request Method', 'wallet-system-for-woocommerce' ); ?></label>
						<select id="wps_wallet_fund_request_another_method" name="wps_wallet_fund_request_another_method" class="wps-wallet-method">
							<option value="email" selected><?php esc_html_e( 'User Email', 'wallet-system-for-woocommerce' ); ?></option>
							<option value="wallet_id"><?php esc_html_e( 'User Wallet ID', 'wallet-system-for-woocommerce' ); ?></option>
						</select>
					</p>

					<!-- Email Field (default visible) -->
					<p class="wps-wallet-field-container form-row form-row-wide" id="wps_wallet_fund_request_another_email_wrap">
						<label for="wps_wallet_fund_request_another_user_email"><?php esc_html_e( 'Fund Requested User Email ID', 'wallet-system-for-woocommerce' ); ?></label>
						<input type="email" placeholder="Please enter request user email" class="wps-wallet-userselect" id="wps_wallet_fund_request_another_user_email" name="wps_wallet_fund_request_another_user_email" required>
					</p>

					<!-- Wallet ID Field (hidden by default) -->
					<p class="wps-wallet-field-container form-row form-row-wide" id="wps_wallet_fund_request_another_walletid_wrap" style="display:none;">
						<label for="wps_wallet_fund_request_another_user_walletid"><?php esc_html_e( 'Fund Requested User Wallet ID', 'wallet-system-for-woocommerce' ); ?></label>
						<input type="text" placeholder="Please enter request user wallet ID" class="wps-wallet-walletid" id="wps_wallet_fund_request_another_user_walletid" name="wps_wallet_fund_request_another_user_walletid">
					</p>

					<!-- Amount -->
					<p class="wps-wallet-field-container form-row form-row-wide">
						<label for="wps_wallet_fund_request_amount"><?php echo esc_html__( 'Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol( $current_currency ) ) . ')'; ?></label>
						<input type="number" step="0.01" min="0" id="wps_wallet_fund_request_amount" name="wps_wallet_fund_request_amount" required>
					</p>
					<p class="error"></p>


					<p class="wps-wallet-field-container form-row form-row-wide">
						<label for="wps_wallet_note"><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></label>
						<textarea id="wps_wallet_note" name="wps_wallet_note" required></textarea>
						<?php
						$show_withdrawal_message = apply_filters( 'wps_wsfw_show_withdrawal_message', '' );
						if ( ! empty( $show_withdrawal_message ) ) {
							echo '<span class="show-message" >' . wp_kses_post( $show_withdrawal_message ) . '</span>';
						}
						?>
					</p>
					<p class="wps-wallet-field-container form-row">
						<input type="hidden" name="wallet_user_id" value="<?php echo esc_attr( $user_id ); ?>">
						<input type="hidden" name="wps_current_user_email" value="<?php echo esc_attr( $current_user_email ); ?>">
						<input type="submit" class="wps-btn__filled button" id="wps_wallet_fund_request" name="wps_wallet_fund_request" value="<?php esc_html_e( 'Request For Fund', 'wallet-system-for-woocommerce' ); ?>" >
					</p>
				</span>
				<p>
				<?php
						$args               = array(
							'numberposts' => -1,
							'post_type'   => 'wallet_fund_request',
							'orderby'     => 'ID',
							'order'       => 'DESC',
							'post_status' => array( 'any' ),
						);
						$withdrawal_request = get_posts( $args );
						$count = 0;
						// Arrays to store user IDs.
						$all_requested_user_ids = array();
						$all_user_ids = array();

						foreach ( $withdrawal_request as $key => $pending ) {
							$request_id = $pending->ID;
							$userid     = get_post_meta( $request_id, 'wallet_user_id', true );
							$requested_user_id = get_post_meta( $request_id, 'requested_user_id', true );

							// Collect user IDs.
							$all_user_ids[] = $userid;
							$all_requested_user_ids[] = $requested_user_id;

							$date_format = get_option( 'date_format', 'm/d/Y' );
							if ( $userid == $user_id || $requested_user_id == $user_id ) { // check either current user present in request to user or request from user.
								$date = date_create( $pending->post_date );
								if ( 'pending1' === $pending->post_status && $requested_user_id == $user_id ) {
									$withdrawal_status = esc_html__( 'pending', 'wallet-system-for-woocommerce' );
									$count++;
								}
							}
						}

						?>

								<div class="wps_wcb_wallet_balance_container_fund">
								<div class="wps_wsfw_wallet_fund_request_wrapper">
								<div class="wps_view_fund"><span id="wps_fund_send_table_div" ><?php esc_html_e( 'View Send Fund Request', 'wallet-system-for-woocommerce' ); ?></span>
								</div>
							<?php

							if ( in_array( $user_id, $all_requested_user_ids ) ) {
								?>
								<div class="wps_wcb_wallet_balance_container_fund_in">
									<div class="wps_view_fund"><span id="wps_fund_recieve_table_div" ><?php esc_html_e( 'View Recieve Fund Request', 'wallet-system-for-woocommerce' ); ?><span class="show_pending_fund_request_count"><?php echo esc_html( $count ); ?></span></span>
									</div>
								</div>	
								<?php
							}
							?>
								</div>
					<!-- table to show all request related to send -->
					<div class="wps_fund_send_table">
							<div class="wps-wallet-transaction-container">
								<table class="wps-wsfw-wallet-field-table dt-responsive" id="transactions_table" >
									<thead>
										<tr>
											<th>#</th>
											<th><?php esc_html_e( 'ID', 'wallet-system-for-woocommerce' ); ?></th>
											<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
											<th><?php esc_html_e( 'Requested user mail id', 'wallet-system-for-woocommerce' ); ?></th>
											<th class="wps_wsfw_fund_request_status"><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
											<th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
											<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										$i = 1;
										foreach ( $withdrawal_request as $key => $pending ) {
											$request_id = $pending->ID;
											$userid     = get_post_meta( $request_id, 'wallet_user_id', true );
											$requested_user_id = get_post_meta( $request_id, 'requested_user_id', true );
											$date_format = get_option( 'date_format', 'm/d/Y' );
											if ( $userid == $user_id ) { // check either current user present in request to user or request from user.
												$date = date_create( $pending->post_date );
												if ( 'pending1' === $pending->post_status ) {
													$withdrawal_status = esc_html__( 'pending', 'wallet-system-for-woocommerce' );
												} else {
													$withdrawal_status = $pending->post_status;
												}
												$wps_wsfwp_wallet_withdrawal_fee_amount = get_post_meta( $request_id, 'wps_wsfwp_wallet_withdrawal_fee_amount', true );

												$withdrawal_balance = apply_filters( 'wps_wsfw_show_converted_price', get_post_meta( $request_id, 'wps_wallet_fund_request_amount', true ) );
												$wps_wallet_fund_request_another_user_email = get_post_meta( $request_id, 'wps_wallet_fund_request_another_user_email', true );
												if ( empty( $wps_wallet_fund_request_another_user_email ) ) {
													$wps_wallet_fund_request_another_user_email = get_userdata( $requested_user_id )->user_email;
												}

												$wps_current_user_email = get_post_meta( $request_id, 'wps_current_user_email', true );
												?>
												<tr>
												<td><?php echo esc_html( $i ); ?></td>
												<td><?php echo esc_html( $request_id ); ?></td>
												<td><?php echo wp_kses_post( wc_price( $withdrawal_balance, array( 'currency' => $current_currency ) ) ); ?></td>
												<?php
												if ( $requested_user_id == $user_id && 'pending1' == $pending->post_status ) {
													?>
													<td><?php echo esc_html( $wps_current_user_email ); ?></td>
													<td class="wps_wsfw_fund_request_status">
													<form action="" method="POST">
															<select onchange="this.className=this.options[this.selectedIndex].className" name="wps-wpg-gen-table_status" id="wps-wpg-gen-table_status" aria-controls="wps-wpg-gen-section-table" class="<?php echo esc_attr( $pending->post_status ); ?>">
																<option class="approved" value="approved" >&nbsp;&nbsp;<?php esc_html_e( 'approved', 'wallet-system-for-woocommerce' ); ?></option>
																<option class="pending1" value="pending1" <?php selected( 'pending1', $pending->post_status, true ); ?> disabled  >&nbsp;&nbsp;<?php esc_html_e( 'pending', 'wallet-system-for-woocommerce' ); ?></option>
																<option class="rejected" value="rejected" >&nbsp;&nbsp;<?php esc_html_e( 'rejected', 'wallet-system-for-woocommerce' ); ?></option>
															</select>
															<input type="hidden" name="withdrawal_id" value="<?php echo esc_attr( $request_id ); ?>" />
															<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>" />
															<div id="overlay">
																<img src='<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/loader.gif'; ?>' width="64" height="64" /><br>
															</div>
														</form>
														</td>
															<?php

												} else {
													?>
													<td><?php echo esc_html( $wps_wallet_fund_request_another_user_email ); ?></td>
													<td class="wps_wsfw_fund_request_status wps_wallet_widthdrawal_'.<?php echo esc_html( $withdrawal_status ); ?>.'"> <img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . '/public/images/' . esc_html( $withdrawal_status ); ?>'.svg" title= "<?php echo esc_html( $withdrawal_status ); ?>" ></td>

													<?php
												}
												?>
												<td><?php echo esc_html( get_post_meta( $request_id, 'wps_wallet_note', true ) ); ?></td>
												<td><?php echo esc_html( date_format( $date, $date_format ) ); ?></td>
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
					<!-- table to show all request related to send -->
					<div class="wps_fund_recieve_table">
							<div class="wps-wallet-transaction-container">
								<table class="wps-wsfw-wallet-field-table dt-responsive" id="transactions_table" >
									<thead>
										<tr>
											<th>#</th>
											<th><?php esc_html_e( 'ID', 'wallet-system-for-woocommerce' ); ?></th>
											<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
											<th><?php esc_html_e( 'Requesting user mail id', 'wallet-system-for-woocommerce' ); ?></th>
											<th class="wps_wsfw_fund_request_status"><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
											<th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
											<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										$i = 1;
										foreach ( $withdrawal_request as $key => $pending ) {
											$request_id = $pending->ID;
											$userid     = get_post_meta( $request_id, 'wallet_user_id', true );
											$requested_user_id = get_post_meta( $request_id, 'requested_user_id', true );
											$date_format = get_option( 'date_format', 'm/d/Y' );
											if ( $requested_user_id == $user_id ) { // check either current user present in request to user or request from user.
												$date = date_create( $pending->post_date );
												if ( 'pending1' === $pending->post_status ) {
													$withdrawal_status = esc_html__( 'pending', 'wallet-system-for-woocommerce' );
												} else {
													$withdrawal_status = $pending->post_status;
												}
												$wps_wsfwp_wallet_withdrawal_fee_amount = get_post_meta( $request_id, 'wps_wsfwp_wallet_withdrawal_fee_amount', true );

												$withdrawal_balance = apply_filters( 'wps_wsfw_show_converted_price', get_post_meta( $request_id, 'wps_wallet_fund_request_amount', true ) );
												$wps_wallet_fund_request_another_user_email = get_post_meta( $request_id, 'wps_wallet_fund_request_another_user_email', true );
												if ( empty( $wps_wallet_fund_request_another_user_email ) ) {
													$wps_wallet_fund_request_another_user_email = get_userdata( $userid )->user_email;
												}
												$wps_current_user_email = get_post_meta( $request_id, 'wps_current_user_email', true );
												?>
												<tr>
												<td><?php echo esc_html( $i ); ?></td>
												<td><?php echo esc_html( $request_id ); ?></td>
												<td><?php echo wp_kses_post( wc_price( $withdrawal_balance, array( 'currency' => $current_currency ) ) ); ?></td>
												<?php
												if ( $requested_user_id == $user_id && 'pending1' == $pending->post_status ) {
													?>
													<td><?php echo esc_html( $wps_current_user_email ); ?></td>
													<td class="wps_wsfw_fund_request_status">
													<form action="" method="POST">
															<select onchange="this.className=this.options[this.selectedIndex].className" name="wps-wpg-gen-table_status" id="wps-wpg-gen-table_status" aria-controls="wps-wpg-gen-section-table" class="<?php echo esc_attr( $pending->post_status ); ?>">
																<option class="approved" value="approved" >&nbsp;&nbsp;<?php esc_html_e( 'approved', 'wallet-system-for-woocommerce' ); ?></option>
																<option class="pending1" value="pending1" <?php selected( 'pending1', $pending->post_status, true ); ?> disabled  >&nbsp;&nbsp;<?php esc_html_e( 'pending', 'wallet-system-for-woocommerce' ); ?></option>
																<option class="rejected" value="rejected" >&nbsp;&nbsp;<?php esc_html_e( 'rejected', 'wallet-system-for-woocommerce' ); ?></option>
															</select>
															<input type="hidden" name="requesting_user_id" value="<?php echo esc_attr( $userid ); ?>" />
															<input type="hidden" name="requested_user_id" value="<?php echo esc_attr( $requested_user_id ); ?>" />
															<input type="hidden" name="withdrawal_balance" value="<?php echo esc_attr( $withdrawal_balance ); ?>" />
															<input type="hidden" name="request_id" value="<?php echo esc_attr( $request_id ); ?>" />
															<div id="overlay">
																<img src='<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/loader.gif'; ?>' width="64" height="64" /><br>
															</div>
														</form>
														</td>
															<?php

												} else {
													?>
													<td><?php echo esc_html( $wps_wallet_fund_request_another_user_email ); ?></td>
													<td class="wps_wsfw_fund_request_status wps_wallet_widthdrawal_'.<?php echo esc_html( $withdrawal_status ); ?>.'"> <img src="<?php echo esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . '/public/images/' . esc_html( $withdrawal_status ); ?>'.svg" title= "<?php echo esc_html( $withdrawal_status ); ?>" ></td>

													<?php
												}
												?>
												<td><?php echo esc_html( get_post_meta( $request_id, 'wps_wallet_note', true ) ); ?></td>
												<td><?php echo esc_html( date_format( $date, $date_format ) ); ?></td>
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
				</div>
				</p>
			<?php
		}
		?>
		
	


</div>
