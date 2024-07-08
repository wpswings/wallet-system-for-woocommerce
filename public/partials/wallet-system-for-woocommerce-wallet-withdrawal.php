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
	$disable_withdrawal_request = get_user_meta( $user_id, 'disable_further_withdrawal_request', true );
	if ( $disable_withdrawal_request ) {
		show_message_on_form_submit( esc_html__( 'Your wallet\'s withdrawal request is in pending.', 'wallet-system-for-woocommerce' ), 'woocommerce-info' );
		$args               = array(
			'numberposts' => -1,
			'post_type'   => 'wallet_withdrawal',
			'orderby'     => 'ID',
			'order'       => 'DESC',
			'post_status' => array( 'any' ),
		);
		$withdrawal_request = get_posts( $args );
		?>
		<div class="wps-wallet-transaction-container">
			<table class="wps-wsfw-wallet-field-table dt-responsive" id="transactions_table" >
				<thead>
					<tr>
						<th>#</th>
						<th><?php esc_html_e( 'ID', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
						<?php

						if ( $check ) {
							?>
						<th>
							<?php
							esc_html_e( 'Withdrawal Fee', 'wallet-system-for-woocommerce' );
							?>
						</th>
							<?php
						}
						?>
						<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					foreach ( $withdrawal_request as $key => $pending ) {
						$request_id = $pending->ID;
						$userid     = get_post_meta( $request_id, 'wallet_user_id', true );
						if ( $userid == $user_id ) {
							$date = date_create( $pending->post_date );
							if ( 'pending1' === $pending->post_status ) {
								$withdrawal_status = esc_html__( 'pending', 'wallet-system-for-woocommerce' );
							} else {
								$withdrawal_status = $pending->post_status;
							}
							$wps_wsfwp_wallet_withdrawal_fee_amount = get_post_meta( $request_id, 'wps_wsfwp_wallet_withdrawal_fee_amount', true );

							$withdrawal_balance = apply_filters( 'wps_wsfw_show_converted_price', get_post_meta( $request_id, 'wps_wallet_withdrawal_amount', true ) );
							echo '<tr>
							<td>' . esc_html( $i ) . '</td>
                            <td>' . esc_html( $request_id ) . '</td>
                            <td>' . wp_kses_post( wc_price( $withdrawal_balance, array( 'currency' => $current_currency ) ) ) . '</td>
                            <td class="wps_wallet_widthdrawal_' . esc_html( $withdrawal_status ) . '"> <img src=" ' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . '/public/images/' . esc_html( $withdrawal_status ) . '.svg" title="' . esc_html( $withdrawal_status ) . '"></td>
                            <td>' . esc_html( get_post_meta( $request_id, 'wps_wallet_note', true ) ) . '</td>';

							if ( $check ) {

								echo '<td>' . wp_kses_post( wc_price( $wps_wsfwp_wallet_withdrawal_fee_amount ) ) . '</td>';

							}
							echo '<td>' . esc_html( date_format( $date, 'd/m/Y' ) ) . '</td>
                            </tr>';
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	} else {
		if ( $wallet_bal > 0 ) {
			$is_pro_plugin = false;
			$wsfwp_min_wallet_withdrawal_amount = 0;
			$wsfwp_max_wallet_withdrawal_amount = 0;
			$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
			if ( $is_pro_plugin ) {
				$wps_wsfwp_wallet_withdrawal_restriction_enable = get_option( 'wps_wsfwp_wallet_withdrawal_restriction_enable' );
				if ( 'on' == $wps_wsfwp_wallet_withdrawal_restriction_enable ) {
					$wsfwp_min_wallet_withdrawal_amount = get_option( 'wsfwp_min_wallet_withdrawal_amount' );
					$wsfwp_max_wallet_withdrawal_amount = get_option( 'wsfwp_max_wallet_withdrawal_amount' );
				}
			}
			?>
		<span id="wps_wallet_transfer_form">
			<p class="wps-wallet-field-container form-row form-row-wide">
				<label for="wps_wallet_withdrawal_amount"><?php echo esc_html__( 'Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol( $current_currency ) ) . ')'; ?></label>
				<input type="number" step="0.01" min="0" data-minwithdrawal="<?php echo esc_attr( $wsfwp_min_wallet_withdrawal_amount ); ?>" data-maxwithdrawal="<?php echo esc_attr( $wsfwp_max_wallet_withdrawal_amount ); ?>"data-max="<?php echo esc_attr( $wallet_bal ); ?>" id="wps_wallet_withdrawal_amount" name="wps_wallet_withdrawal_amount" required="">
			</p>
			<p class="error"></p>
			<?php
				$wallet_withdrawal_fee_html = apply_filters( 'wps_wsfw_show_wallet_withdrawal_fee_content', '' );
			if ( ! empty( $wallet_withdrawal_fee_html ) ) {
				wp_kses_post( $wallet_withdrawal_fee_html ); // phpcs:ignore
			}
			 $wps_wsfwp_wallet_withdrawal_paypal_dropdown = get_option( 'wps_wsfwp_wallet_withdrawal_paypal_dropdown' );

			 $wps_wsfwp_wallet_withdrawal_paypal_enable = get_option( 'wps_wsfwp_wallet_withdrawal_paypal_enable' );
			if ( 'on' == $wps_wsfwp_wallet_withdrawal_paypal_enable ) {

				if ( 'on' == $wps_wsfwp_wallet_withdrawal_paypal_dropdown ) {
					?>
			<p class="wps-wallet-field-container form-row form-row-wide">
				<label for="wps_wallet_withdrawal_option"><?php echo esc_html__( 'Select option for withdrawal.' ); ?></label>
				<select name="wps_wallet_withdrawal_option" id="wps_wallet_withdrawal_option">
						<option><?php esc_html_e( 'Select any', 'wallet-system-for-woocommerce' ); ?></option>
						<option value="manual"><?php esc_html_e( 'Manual', 'wallet-system-for-woocommerce' ); ?></option>
						<option value="paypal"><?php esc_html_e( 'Paypal', 'wallet-system-for-woocommerce' ); ?></option>
				</select>
			</p>
					<?php
				}
				?>
			<p class="wps-wallet-field-container form-row form-row-wide">
				<label for="wps_wallet_withdrawal_paypal_user_email"><?php esc_html_e( 'Paypal Mail Id', 'wallet-system-for-woocommerce' ); ?></label>
				<input type="email" placeholder="Please enter paypal mail id" class="wps-wallet-userselect" id="wps_wallet_withdrawal_paypal_user_email" name="wps_wallet_withdrawal_paypal_user_email" >
			</p>
				<?php

			}
			?>
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
				<input type="submit" class="wps-btn__filled button" id="wps_withdrawal_request" name="wps_withdrawal_request" value="<?php esc_html_e( 'Request For Withdrawal', 'wallet-system-for-woocommerce' ); ?>" >
			</p>
			</span>
		<p>
		<div class="wps_wcb_wallet_balance_container_withdrawal">
		<div class="wps_view_withdrawal"><span id="wps_withdrawal_table_div" ><?php esc_html_e( 'View Withdrawal Request', 'wallet-system-for-woocommerce' ); ?></span>
			</div>
			<div class="wps_withdrawal_table">
				<?php
				$args               = array(
					'numberposts' => -1,
					'post_type'   => 'wallet_withdrawal',
					'orderby'     => 'ID',
					'order'       => 'DESC',
					'post_status' => array( 'any' ),
				);
				$withdrawal_request = get_posts( $args );
				?>
		<div class="wps-wallet-transaction-container">
			<table class="wps-wsfw-wallet-field-table dt-responsive" id="transactions_table" >
				<thead>
					<tr>
						<th>#</th>
						<th><?php esc_html_e( 'ID', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
						<?php
						if ( $check ) {
							?>
						<th>
							<?php
							esc_html_e( 'Withdrawal Fee', 'wallet-system-for-woocommerce' );
							?>
						</th>
							<?php
						}
						?>
						<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					foreach ( $withdrawal_request as $key => $pending ) {
						$request_id = $pending->ID;
						$userid     = get_post_meta( $request_id, 'wallet_user_id', true );
						$date_format = get_option( 'date_format', 'm/d/Y' );
						if ( $userid == $user_id ) {
							$date = date_create( $pending->post_date );
							if ( 'pending1' === $pending->post_status ) {
								$withdrawal_status = esc_html__( 'pending', 'wallet-system-for-woocommerce' );
							} else {
								$withdrawal_status = $pending->post_status;
							}
							$wps_wsfwp_wallet_withdrawal_fee_amount = get_post_meta( $request_id, 'wps_wsfwp_wallet_withdrawal_fee_amount', true );

							$withdrawal_balance = apply_filters( 'wps_wsfw_show_converted_price', get_post_meta( $request_id, 'wps_wallet_withdrawal_amount', true ) );
							echo '<tr>
							<td>' . esc_html( $i ) . '</td>
                            <td>' . esc_html( $request_id ) . '</td>
                            <td>' . wp_kses_post( wc_price( $withdrawal_balance, array( 'currency' => $current_currency ) ) ) . '</td>
                            <td class="wps_wallet_widthdrawal_' . esc_html( $withdrawal_status ) . '"> <img src=" ' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . '/public/images/' . esc_html( $withdrawal_status ) . '.svg" title="' . esc_html( $withdrawal_status ) . '"></td>
                            <td>' . esc_html( get_post_meta( $request_id, 'wps_wallet_note', true ) ) . '</td>';

							if ( $check ) {

								echo '<td>' . wp_kses_post( wc_price( $wps_wsfwp_wallet_withdrawal_fee_amount ) ) . '</td>';

							}

							echo ' <td>' . esc_html( date_format( $date, $date_format ) ) . '</td>
                            </tr>';
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
		} else {
			show_message_on_form_submit( esc_html__( 'Your wallet amount is 0, you cannot withdraw money from wallet.', 'wallet-system-for-woocommerce' ), 'woocommerce-error' );
			?>
			<p>
			<div class="wps_wcb_wallet_balance_container_withdrawal">
			<div class="wps_view_withdrawal"><span id="wps_withdrawal_table_div" ><?php esc_html_e( 'View Withdrawal Request', 'wallet-system-for-woocommerce' ); ?></span>
				</div>
				<div class="wps_withdrawal_table">
					<?php
					$args               = array(
						'numberposts' => -1,
						'post_type'   => 'wallet_withdrawal',
						'orderby'     => 'ID',
						'order'       => 'DESC',
						'post_status' => array( 'any' ),
					);
					$withdrawal_request = get_posts( $args );
					?>
			<div class="wps-wallet-transaction-container">
				<table class="wps-wsfw-wallet-field-table dt-responsive" id="transactions_table" >
					<thead>
						<tr>
							<th>#</th>
							<th><?php esc_html_e( 'ID', 'wallet-system-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Amount', 'wallet-system-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
							<th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
							<?php
							if ( $check ) {
								?>
						<th>
								<?php
								esc_html_e( 'Withdrawal Fee', 'wallet-system-for-woocommerce' );
								?>
						</th>
								<?php
							}
							?>
						<th><?php esc_html_e( 'Date', 'wallet-system-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						foreach ( $withdrawal_request as $key => $pending ) {
							$request_id = $pending->ID;
							$userid     = get_post_meta( $request_id, 'wallet_user_id', true );
							$date_format = get_option( 'date_format', 'm/d/Y' );
							if ( $userid == $user_id ) {
								$date = date_create( $pending->post_date );
								if ( 'pending1' === $pending->post_status ) {
									$withdrawal_status = esc_html__( 'pending', 'wallet-system-for-woocommerce' );
								} else {
									$withdrawal_status = $pending->post_status;
								}
								$wps_wsfwp_wallet_withdrawal_fee_amount = get_post_meta( $request_id, 'wps_wsfwp_wallet_withdrawal_fee_amount', true );

								$withdrawal_balance = apply_filters( 'wps_wsfw_show_converted_price', get_post_meta( $request_id, 'wps_wallet_withdrawal_amount', true ) );
								echo '<tr>
								<td>' . esc_html( $i ) . '</td>
								<td>' . esc_html( $request_id ) . '</td>
								<td>' . wp_kses_post( wc_price( $withdrawal_balance, array( 'currency' => $current_currency ) ) ) . '</td>
								<td class="wps_wallet_widthdrawal_' . esc_html( $withdrawal_status ) . '"> <img src=" ' . esc_html( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . '/public/images/' . esc_html( $withdrawal_status ) . '.svg" title="' . esc_html( $withdrawal_status ) . '"></td>
								<td>' . esc_html( get_post_meta( $request_id, 'wps_wallet_note', true ) ) . '</td>';

								if ( $check ) {

									echo '<td>' . wp_kses_post( wc_price( $wps_wsfwp_wallet_withdrawal_fee_amount ) ) . '</td>';

								}

								echo ' <td>' . esc_html( date_format( $date, $date_format ) ) . '</td>
								</tr>';
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
	}
	?>

</div>
