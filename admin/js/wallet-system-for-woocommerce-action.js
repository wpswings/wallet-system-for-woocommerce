  jQuery(document).ready(function() {
   
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


});

