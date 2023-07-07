<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


<<<<<<< HEAD
$referral_link = wps_wsfw_get_referral_link_wallet( $user_id );
=======
$referral_link = wps_wpr_get_referral_link_wallet( $user_id );
>>>>>>> 53efcbfb95861080ab5b45d9a4a99f18d98fc569


$wps_pages_ids = '';
$wps_pages     = get_pages();
if ( isset( $wps_pages ) && ! empty( $wps_pages ) && is_array( $wps_pages ) ) {
	foreach ( $wps_pages as $pagedata ) {
		if ( 'My account' == $pagedata->post_title ) {
			$pagedata->post_title;
			$wps_pages_ids = $pagedata->ID;
		}
	}
}


<<<<<<< HEAD
$wps_wsfw_page_url      = $wps_pages_ids;
if ( ! empty( $wps_wsfw_page_url ) ) {
	$wps_wsfw_page_url = get_page_link( $wps_wsfw_page_url );
} else {
	$wps_wsfw_page_url = site_url();
}

$site_url = apply_filters( 'wps_wpr_referral_link_url', $wps_wsfw_page_url );
=======
$wps_wpr_page_url      = $wps_pages_ids;
if ( ! empty( $wps_wpr_page_url ) ) {
	$wps_wpr_page_url = get_page_link( $wps_wpr_page_url );
} else {
	$wps_wpr_page_url = site_url();
}

$site_url = apply_filters( 'wps_wpr_referral_link_url', $wps_wpr_page_url );
>>>>>>> 53efcbfb95861080ab5b45d9a4a99f18d98fc569
$wallet_bal = get_user_meta( $user_id, 'wps_wallet', true );
$wallet_bal = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
$wallet_bal = apply_filters( 'wps_wsfw_show_converted_price', $wallet_bal );
$wps_wsfw_wallet_action_registration_amount          = get_option( 'wps_wsfw_wallet_action_referal_amount' );


?>

<div class='content active'>

<div class="wps-wallet-referral-wrapper">
<div class="wps-wallet-referral-heading">
  <h4> <?php __( 'Wallet Referral', 'wallet-system-for-woocommerce' ); ?></h4>
</div>
		<div class="wps-wallet-popup-right-rewards wps-wallet-popup-right-rewards--login">
<<<<<<< HEAD
	<input type="hidden" id="wps_wsfw_copy" name="custId" value="<?php echo wp_kses_post( $site_url . '?pkey=' . $referral_link ); ?>" readonly="">
=======
	<input type="hidden" id="wps_wpr_copy" name="custId" value="<?php echo wp_kses_post( $site_url . '?pkey=' . $referral_link ); ?>" readonly="">
>>>>>>> 53efcbfb95861080ab5b45d9a4a99f18d98fc569
	  <div class="wps-wallet-popup-rewards-right-content-wallet">
		<div id="wps_notify_user_copy"><code><?php echo wp_kses_post( $site_url . '?pkey=' . $referral_link ); ?></code></div>
	  </div>
	  <div class="wps-wallet-popup-rewards-right-content-wallet">
<<<<<<< HEAD
		<button onclick="copyshareurl()" class="wps_wsfw_btn_copy wps_tooltip" data-clipboard-target="#wps_notify_user_copy" aria-label="copied">
		<span  class="wps_tooltiptext"><?php esc_html_e( 'Copy', 'wallet-system-for-woocommerce' ); ?></span>
=======
		<button onclick="copyshareurl()" class="wps_wpr_btn_copy wps_tooltip" data-clipboard-target="#wps_notify_user_copy" aria-label="copied">
		<span  class="wps_tooltiptext"><?php esc_html_e( 'Copy', 'ultimate-woocommerce-points-and-rewards' ); ?></span>
>>>>>>> 53efcbfb95861080ab5b45d9a4a99f18d98fc569
		<img src="<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'public/images/copy.png'; ?>" alt="Copy to clipboard"></button>
		<span class="wps_tooltiptext_scl" id="myTooltip_referral"></span>
	  </div>
	</div>
	<div class="wps-wallet-referral-notification">  <?php echo esc_html__( 'You will get ', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency() ) . ( esc_html( $wps_wsfw_wallet_action_registration_amount ) ) . esc_html__( ' amount to refer a friend', 'wallet-system-for-woocommerce' ); ?></div>
</div>

</div>


