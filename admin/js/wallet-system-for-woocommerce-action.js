  jQuery(document).ready(function() {
  


    
    flatpickr('#fromdate_transaction', { dateFormat: 'Y-m-d'});
    flatpickr('#todate_transaction', { dateFormat: 'Y-m-d'});
    
    if (wsfw_admin_action_param.is_pro_plugin != 1){
    jQuery('#action_user_trasaction').addClass('wps_pro_settings_tag');
    jQuery('#action_user_trasaction').html('&nbsp;&nbsp'+wsfw_admin_action_param.is_action);

    jQuery('#user_transaction_action').addClass('wps_pro_settings_tag');
    jQuery('#user_transaction_action').html('&nbsp;&nbsp'+wsfw_admin_action_param.is_action);

    }


    var wsfw_wallet_recharge_order_status_checkout = jQuery('#wsfw_wallet_recharge_order_status_checkout').prop('checked');

    if (wsfw_wallet_recharge_order_status_checkout == true){
      jQuery(jQuery('#wps_wsfw_wallet_order_auto_process').parent().parent().parent()).show();
    } else{
      jQuery(jQuery('#wps_wsfw_wallet_order_auto_process').parent().parent().parent()).hide();
    }

    jQuery(document).on( 'click', '#wsfw_wallet_recharge_order_status_checkout', function() {
		
      var wsfw_wallet_recharge_order_status_checkout = jQuery('#wsfw_wallet_recharge_order_status_checkout').prop('checked');

      if (wsfw_wallet_recharge_order_status_checkout == true){
        jQuery(jQuery('#wps_wsfw_wallet_order_auto_process').parent().parent().parent()).show();
      } else{
        jQuery(jQuery('#wps_wsfw_wallet_order_auto_process').parent().parent().parent()).hide();
      }
      });


    var payment_gateway_charge_type = jQuery('#wps_wsfwp_payment_gateway_charge_fee_type').val();

      if ( payment_gateway_charge_type == 'percent' ) {
    
        jQuery('.wps_payment_gateway_charge_textbox').attr('max',100);
  
      } else{
        jQuery('.wps_payment_gateway_charge_textbox').attr('max','');
      }

    jQuery('.bulkactions').hide();


    
    jQuery(document).on( 'click', '#wps_wsfw_export_csv', function() {
		
    jQuery('#bulk-action-selector-top').val('export_csv');
      const myAnchor = document.getElementById('doaction');
				myAnchor.click();
		});
    
    jQuery( "#wps_sfw_subscription_interval" ).change(function() {
       
    var wps_sfw_subscription_interval = jQuery( "#wps_sfw_subscription_interval" ).val();        
      jQuery('#wps_sfw_subscription_expiry_interval').val(wps_sfw_subscription_interval).attr("selected", "selected");
    });

  jQuery(document).on( 'click', '#wsfw_button_wallet_withdrawal_paypal_tab_option', function(e) {
    e.preventDefault(e);
    jQuery('.wps_wallet_lite_go_pro_popup_wrap').addClass('wps_wallet_lite_go_pro_popup_show');
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

      is_from = jQuery('#fromdate_transaction').val();
      to_from = jQuery('#todate_transaction').val();
     if ( is_from == '' ) {
      jQuery('#todate_transaction').val('');
      jQuery('#fromdate_transaction').focus();
      return;
     } else if( to_from == '' ){
      jQuery('#todate_transaction').focus();
      return;
     }
    
    jQuery('#hidden_from_date').val(is_from);
    jQuery('#hidden_to_date').val(to_from);
     
    
     
      jQuery('#wps_wsfw_data_number').trigger('click');
    });



    jQuery(document).on( 'click', '#clear_table', function() {
      jQuery('#fromdate_transaction').val('');
      jQuery('#todate_transaction').val('');
      jQuery('#wps_wsfw_data_number').trigger('click');
		});

    jQuery(document).on( 'change', '#wps_wsfw_cashback_type', function() {
      
      var cashback_type = jQuery('#wps_wsfw_cashback_type').val();

      if ( cashback_type == 'percent' ) {
    
        jQuery('#wps_wsfw_cashback_amount').attr('max',100);
  
      } else{
        jQuery('#wps_wsfw_cashback_amount').attr('max','');
      }
		});

    jQuery(document).on( 'change', '#wps_wsfwp_payment_gateway_charge_fee_type', function() {
      
      var payment_gateway_charge_type = jQuery('#wps_wsfwp_payment_gateway_charge_fee_type').val();

      if ( payment_gateway_charge_type == 'percent' ) {
    
        jQuery('.wps_payment_gateway_charge_textbox').attr('max',100);
  
      } else{
        jQuery('.wps_payment_gateway_charge_textbox').attr('max','');
      }
		});


    

    var cashback_type = jQuery('#wps_wsfw_cashback_type').val();

    if ( cashback_type == 'percent' ) {
  
      jQuery('#wps_wsfw_cashback_amount').attr('max',100);

    } else{
      jQuery('#wps_wsfw_cashback_amount').attr('max','');
    }

    jQuery(document).on( 'blur', '#wps_wsfw_subscriptions_expiry_per_interval', function() {
      
      var subscription_per_interval = jQuery('#wps_wsfw_subscriptions_per_interval').val();
      var subscription_expiry_interval = jQuery('#wps_wsfw_subscriptions_expiry_per_interval').val();

      if ( subscription_per_interval != '' ) {
        if ( parseInt(subscription_per_interval) > parseInt( subscription_expiry_interval ) ) {
    
          jQuery('#wps_wsfw_subscriptions_expiry_per_interval').val('');
          jQuery(jQuery('#wps_wsfw_subscriptions_expiry_per_interval').parent().parent()).append('<div class=" wps_subscription_expiry error">'+wsfw_admin_action_param.subscription_exipry+'</div>');
        }else{
          jQuery('.wps_subscription_expiry').remove();
        }
      }
     
		});

    jQuery(document).on( 'blur', '#wps_wsfw_subscriptions_per_interval', function() {
      
      var subscription_per_interval = jQuery('#wps_wsfw_subscriptions_per_interval').val();
      var subscription_expiry_interval = jQuery('#wps_wsfw_subscriptions_expiry_per_interval').val();
      if ( subscription_expiry_interval != '' ) {
      if ( parseInt(subscription_per_interval) > parseInt( subscription_expiry_interval ) ) {
    
        jQuery('#wps_wsfw_subscriptions_per_interval').val('');
        jQuery(jQuery('#wps_wsfw_subscriptions_per_interval').parent().parent()).append('<div class="wps_subscription_interval error">'+wsfw_admin_action_param.subscription_interval+'</div>');
  
      }else{
        jQuery('.wps_subscription_interval').remove();
  
      }
    }
		});
});
