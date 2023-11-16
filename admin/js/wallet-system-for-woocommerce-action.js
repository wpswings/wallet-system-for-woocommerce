  jQuery(document).ready(function() {
  
    if (wsfw_admin_action_param.is_pro_plugin != 1){
    jQuery('#action_user_trasaction').addClass('wps_pro_settings_tag');
    jQuery('#action_user_trasaction').html('&nbsp;&nbsp'+wsfw_admin_action_param.is_action);

    jQuery('#user_transaction_action').addClass('wps_pro_settings_tag');
    jQuery('#user_transaction_action').html('&nbsp;&nbsp'+wsfw_admin_action_param.is_action);

    }

    
    jQuery( "#wps_sfw_subscription_interval" ).change(function() {
       
    var wps_sfw_subscription_interval = jQuery( "#wps_sfw_subscription_interval" ).val();        
      jQuery('#wps_sfw_subscription_expiry_interval').val(wps_sfw_subscription_interval).attr("selected", "selected");
    });

    // on clicking element change the input type password to text or vice-versa
		jQuery(document).on( 'click', '.wps_pro_settings', function() {
			if (wsfw_admin_action_param.is_pro_plugin != 1){
        jQuery(this).prop("checked", false);
        jQuery('.wps_wallet_lite_go_pro_popup_wrap').addClass('wps_wallet_lite_go_pro_popup_show');
			}
		});

		jQuery(document).on( 'click', '.wps_wallet_lite_go_pro_popup_close', function() {
			jQuery('.wps_wallet_lite_go_pro_popup_wrap').removeClass('wps_wallet_lite_go_pro_popup_show');
		});
    jQuery(document).on( 'click', '.wps_wsfw_popup_shadow', function() {
			jQuery('.wps_wallet_lite_go_pro_popup_wrap').removeClass('wps_wallet_lite_go_pro_popup_show');
		});

    jQuery(document).on( 'change', '#wps-wsfw-wallet-trabsacstion-numbers-drodown', function() {
		
      jQuery('#hidden_transaction_number').val(jQuery('#wps-wsfw-wallet-trabsacstion-numbers-drodown').val());
      jQuery('#wps_wsfw_data_number').trigger('click');
		});
    jQuery(document).on( 'change', '#fromdate_transaction', function() {

      $is_from = jQuery('#fromdate_transaction').val();
      jQuery('#todate_transaction').attr('min', jQuery('#fromdate_transaction').val());
		});
    jQuery(document).on( 'change', '#todate_transaction', function() {

      $is_from = jQuery('#fromdate_transaction').val();
      $to_from = jQuery('#todate_transaction').val();
     if ( $is_from == '' ) {
      jQuery('#todate_transaction').val('');
      jQuery('#fromdate_transaction').focus();
      return;
     } else if( $to_from == '' ){
      jQuery('#todate_transaction').focus();
      return;
     }
    
    jQuery('#hidden_from_date').val($is_from);
    jQuery('#hidden_to_date').val($to_from);
     
    
     
      jQuery('#wps_wsfw_data_number').trigger('click');
		});

    jQuery(document).on( 'click', '#clear_table', function() {
      jQuery('#fromdate_transaction').val('');
      jQuery('#todate_transaction').val('');
      jQuery('#wps_wsfw_data_number').trigger('click');
		});
    

    
    
});

