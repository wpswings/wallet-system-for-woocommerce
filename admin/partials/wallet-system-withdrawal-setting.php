<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for showing wallet withdrawal setting
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wsfw_wps_wsfw_obj;
$check = false;
$check = apply_filters( 'wsfw_check_pro_plugin', $check );

?>
<!--  template file for admin settings. -->


<div class="wps-wpg-withdrawal-section-search">

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

<div class="wps-wpg-gen-section-table-wrap wps-wpg-withdrawal-section-table">
	<h4><?php esc_html_e( 'Withdrawal Requests', 'wallet-system-for-woocommerce' ); ?></h4>
	<div class="wps-wpg-gen-section-table-container demo">
		<table id="wps-wpg-gen-table1" class="wps-wpg-gen-section-table dt-responsive">
			<thead>
				<tr>
					<th><?php esc_html_e( '#', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Withdrawal ID', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'User Name', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Status1', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wallet-system-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Withdrawal Amount', 'wallet-system-for-woocommerce' ); ?></th>
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
					<th><?php esc_html_e( 'Note', 'wallet-system-for-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$args = array(
					'post_type'      => 'wallet_withdrawal',
					'posts_per_page' => -1,
					'order'          => 'DESC',
					'orderby'        => 'ID',
					'post_status'    => array( 'approved', 'pending1', 'rejected' ),
				);
				$withdrawal_requests = get_posts( $args );
				$i                   = 1;
				if ( ! empty( $withdrawal_requests ) ) {
					foreach ( $withdrawal_requests as $request ) {
						$withdrawal_amount = get_post_meta( $request->ID, 'wps_wallet_withdrawal_amount', true );
						$wps_wsfwp_wallet_withdrawal_fee_amount = get_post_meta( $request->ID, 'wps_wsfwp_wallet_withdrawal_fee_amount', true );
						$user_id           = get_post_meta( $request->ID, 'wallet_user_id', true );
						$user_name         = get_the_author_meta( 'display_name', $user_id );

						?>
							<tr>
								<td><img src="<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>admin/image/eva_close-outline.svg"><?php echo esc_html( $i ); ?></td>
								<td><?php echo esc_html( $request->ID ); ?></td>
								<td><a href="<?php echo ! empty( $user_name ) ? esc_url( admin_url( 'user-edit.php?user_id=' . $user_id, 'http' ) ) : ''; ?>"><?php echo ! empty( esc_html( $user_name ) ) ? esc_html( $user_name ) : 'Guest#(' . esc_html( $user_id ) . ')'; ?></a></td>
								<td><?php echo esc_html( $request->post_status ); ?></td>
								<td>
									<?php
									$withdrawal_status = $request->post_status;
									if ( 'approved' === $withdrawal_status ) {
										?>
										<span class="approved" ><?php esc_html_e( 'approved', 'wallet-system-for-woocommerce' ); ?></span>
										<?php
									} elseif ( 'rejected' === $withdrawal_status ) {
										?>
										<span class="rejected" ><?php esc_html_e( 'rejected', 'wallet-system-for-woocommerce' ); ?></span>
									<?php } else { ?> 
									<form action="" method="POST">
										<select onchange="this.className=this.options[this.selectedIndex].className" name="wps-wpg-gen-table_status" id="wps-wpg-gen-table_status" aria-controls="wps-wpg-gen-section-table" class="<?php echo esc_attr( $request->post_status ); ?>">
											<option class="approved" value="approved" >&nbsp;&nbsp;<?php esc_html_e( 'approved', 'wallet-system-for-woocommerce' ); ?></option>
											<option class="pending1" value="pending1" <?php selected( 'pending1', $request->post_status, true ); ?> disabled  >&nbsp;&nbsp;<?php esc_html_e( 'pending', 'wallet-system-for-woocommerce' ); ?></option>
											<option class="rejected" value="rejected" >&nbsp;&nbsp;<?php esc_html_e( 'rejected', 'wallet-system-for-woocommerce' ); ?></option>
										</select>
										<input type="hidden" name="withdrawal_id" value="<?php echo esc_attr( $request->ID ); ?>" />
										<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>" />
										<div id="overlay">
											<img src='<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/loader.gif'; ?>' width="64" height="64" /><br>Loading..
										</div>
									</form>
										<?php
									}
									?>
								</td>
								<td><?php echo wp_kses_post( wc_price( $withdrawal_amount ) ); ?></td>
								<?php
								if ( $check ) {
									?>
											<td>
										<?php
										echo wp_kses_post( wc_price( $wps_wsfwp_wallet_withdrawal_fee_amount ) );
										?>
										</td>
										<?php
								}
								?>
									
							
								<td>
								<?php
								$date_format = get_option( 'date_format', 'm/d/Y' );
								$date        = date_create( $request->post_date );
								echo esc_html( date_format( $date, $date_format ) );
								?>
								</td>					
								<td>
								<?php
								echo esc_html( get_post_meta( $request->ID, 'wps_wallet_note', true ) );
								?>
								</td>	
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
