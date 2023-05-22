<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function wps_wpr_get_referral_link_wallet( $user_id ) {

  $get_referral        = get_user_meta( $user_id, 'wps_points_referral', true );
  $get_referral_invite = get_user_meta( $user_id, 'wps_points_referral_invite', true );
  if ( empty( $get_referral ) && empty( $get_referral_invite ) ) {
  
      $referral_key = wps_wpr_create_referral_code_wallet();
      $referral_invite = 0;
      update_user_meta( $user_id, 'wps_points_referral', $referral_key );
      update_user_meta( $user_id, 'wps_points_referral_invite', $referral_invite );
  }
  $referral_link = get_user_meta( $user_id, 'wps_points_referral', true );
  return $referral_link;
  }
  $referral_link = wps_wpr_get_referral_link_wallet( $user_id );


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


$wps_wpr_page_url      = $wps_pages_ids ;
if ( ! empty( $wps_wpr_page_url ) ) {
$wps_wpr_page_url = get_page_link( $wps_wpr_page_url );
} else {
$wps_wpr_page_url = site_url();
}

$site_url = apply_filters( 'wps_wpr_referral_link_url', $wps_wpr_page_url );
$wallet_bal = get_user_meta( $user_id, 'wps_wallet', true );
$wallet_bal = ( ! empty( $wallet_bal ) ) ? $wallet_bal : 0;
$wallet_bal = apply_filters( 'wps_wsfw_show_converted_price', $wallet_bal );
function wps_wpr_create_referral_code_wallet() {

  $length      = 10;
  $pkey        = '';
  $alphabets   = range( 'A', 'Z' );
  $numbers     = range( '0', '9' );
  $final_array = array_merge( $alphabets, $numbers );
  
  while ( $length-- ) {
      $key   = array_rand( $final_array );
      $pkey .= $final_array[ $key ];
  }
  return $pkey;
  }
 
?>

<div class='content active'>

<div class="wps-wallet-referral-wrapper">
<div class="wps-wallet-referral-heading">
  <h4>referral link</h4>
</div>
		<div class="wps-wallet-popup-right-rewards wps-wallet-popup-right-rewards--login">
    <input type="hidden" id="wps_wpr_copy" name="custId" value="<?php echo wp_kses_post( $site_url . '?pkey=' . $referral_link ); ?>" readonly="">
      <div class="wps-wallet-popup-rewards-right-content-wallet">
        <div id="wps_notify_user_copy"><code><?php echo wp_kses_post( $site_url . '?pkey=' . $referral_link ); ?></code></div>
      </div>
      <div class="wps-wallet-popup-rewards-right-content-wallet">
        <button onclick="copyshareurl()" class="wps_wpr_btn_copy wps_tooltip" data-clipboard-target="#wps_notify_user_copy" aria-label="copied">
        <span  class="wps_tooltiptext"><?php esc_html_e( 'Copy', 'ultimate-woocommerce-points-and-rewards' ); ?></span>
        <img src="<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'public/images/copy.png'; ?>" alt="Copy to clipboard"></button>
        <span class="wps_tooltiptext_scl" id="myTooltip"></span>
      </div>
    </div>
    <div class="wps-wallet-referral-notification">You will get 1 points to refer a friend</div>
</div>

</div>


