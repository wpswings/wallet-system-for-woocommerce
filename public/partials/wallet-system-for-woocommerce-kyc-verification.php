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

?>

<div class='content active'>
	<?php
	if ( 'on' === $wsfw_enable_wallet_kyc ) {
		$wsfw_restrict_wallet_withdrawal_kyc   = get_option( 'wsfw_restrict_wallet_withdrawal_kyc' );
		$wsfw_restrict_wallet_transfer_kyc     = get_option( 'wsfw_restrict_wallet_transfer_kyc' );
		$wsfw_restrict_wallet_fund_request_kyc = get_option( 'wsfw_restrict_wallet_fund_request_kyc' );
		$is_pro_plugin = false;
		$is_pro_plugin = apply_filters( 'wps_wsfwp_pro_plugin_check', $is_pro_plugin );
		if ( $is_pro_plugin ) {
			$wsfw_number_of_documents_for_kyc = get_option( 'wsfw_number_of_documents_for_kyc' );
		} else {
			$wsfw_number_of_documents_for_kyc = 1;
		}
		$wps_wsfw_wallet_action_kyc_description = get_option( 'wps_wsfw_wallet_action_kyc_description', '' );

		if ( empty( $wsfw_number_of_documents_for_kyc ) ) {
			$wsfw_number_of_documents_for_kyc = 1;
		}

		// Show KYC instructions/description.


		$wps_wallet_kyc_documents = get_user_meta( $user_id, 'wps_wallet_kyc_documents', true );
		$wps_wallet_kyc_status    = get_user_meta( $user_id, 'key_verification_status', true );
		$kyc_admin_remark = get_user_meta( $user_id, 'kyc_admin_remark', true );
		if ( $wps_wallet_kyc_documents && is_array( $wps_wallet_kyc_documents ) && count( $wps_wallet_kyc_documents ) > 0 ) {
			?>
			<div class="wps-wallet-kyc-documents">
				<?php
				if ( 'pending' === $wps_wallet_kyc_status ) {
					echo '<h3 class="wps-wallet-kyc-status pending">' . esc_html__( 'Your KYC verification is currently pending. Please wait for admin approval.', 'wallet-system-for-woocommerce' ) . '</h3>';
				} elseif ( 'approved' === $wps_wallet_kyc_status ) {
					echo '<h3 class="wps-wallet-kyc-status approved">' . esc_html__( 'Your KYC verification has been approved. You can now access all wallet features.', 'wallet-system-for-woocommerce' ) . '</h3>';
				} elseif ( 'rejected' === $wps_wallet_kyc_status ) {
					echo '<h3 class="wps-wallet-kyc-status rejected">' . esc_html__( 'Your KYC verification was rejected. Please resubmit your documents.', 'wallet-system-for-woocommerce' ) . '</h3>';
				}
				?>
				<h4><?php esc_html_e( 'Here is Details : ', 'wallet-system-for-woocommerce' ); ?></h4>
				 <p><strong><?php esc_html_e( 'Submitted Documents', 'wallet-system-for-woocommerce' ); ?></strong></p>
				<ul>
					<?php
					foreach ( $wps_wallet_kyc_documents as $doc_url ) {
						if ( ! empty( $doc_url ) ) {
							echo '<li><a href="' . esc_url( $doc_url ) . '" target="_blank">' . esc_html( basename( $doc_url ) ) . '</a></li>';
						}
					}
					?>
				</ul>
				<p><strong><?php esc_html_e( 'KYC Status: ', 'wallet-system-for-woocommerce' ); ?></strong> <?php echo esc_html( ucfirst( $wps_wallet_kyc_status ) ); ?></p>
				<?php
				if ( ! empty( $kyc_admin_remark ) ) {
					echo '<p><strong>' . esc_html__( 'Admin Remark: ', 'wallet-system-for-woocommerce' ) . '</strong> ' . esc_html( $kyc_admin_remark ) . '</p>';
				}
				?>
			</div>
			<?php
		}
		if ( ( 'approved' !== $wps_wallet_kyc_status && 'pending' !== $wps_wallet_kyc_status ) || empty( $wps_wallet_kyc_status ) ) {
			if ( ! empty( $wps_wsfw_wallet_action_kyc_description ) ) {
				?>
			<div class="wps-wallet-kyc-instructions">
				<h3><?php esc_html_e( 'KYC Verification Instructions', 'wallet-system-for-woocommerce' ); ?></h3>
				<p><?php echo wp_kses_post( $wps_wsfw_wallet_action_kyc_description ); ?></p>
			</div>
				<?php
			}
			?>
		
			<form action="" method="post" id="wps_wallet_kyc_form" enctype="multipart/form-data" >
			<?php for ( $i = 1; $i <= $wsfw_number_of_documents_for_kyc; $i++ ) : ?>
					<p class="wps-wallet-field-container form-row form-row-wide">
						<label for="wps_wallet_kyc_document_<?php echo esc_attr( $i ); ?>">
							<?php printf( esc_html__( 'Upload Document %d', 'wallet-system-for-woocommerce' ), $i ); ?>
						</label>
						<span id="wps_wallet_kyc_document">
						<input type="file" name="wps_wallet_kyc_document[]" id="wps_wallet_kyc_document_<?php echo esc_attr( $i ); ?>" class="wps_wallet_kyc_document" required />
						</span>
					</p>
				<?php endfor; ?>

				<p class="wps-wallet-field-container form-row">
					<input type="hidden" name="current_user_id" value="<?php echo esc_attr( $user_id ); ?>">
					<input type="submit" class="wps-btn__filled button" id="wps_submit_kyc" name="wps_submit_kyc" value="<?php esc_html_e( 'Submit KYC Documents', 'wallet-system-for-woocommerce' ); ?>">
				</p>
			</form>
			<?php

		}
	} else {
		wc_print_notice( esc_html__( 'Wallet KYC verification is disabled by admin.', 'wallet-system-for-woocommerce' ), 'notice' );
	}
	?>
 
</div>
