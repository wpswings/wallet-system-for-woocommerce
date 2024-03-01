

jQuery( document ).ready(function() {

    setTimeout(() => { 
        
        if (wsfw_public_param_block.partial_payment_data_html_name != undefined ){
          
                jQuery('.wc-block-components-totals-footer-item').append('<table id="partial_table_checkout"><tr class="partial_payment"><td>'+ wsfw_public_param_block.partial_payment_data_html_name +'</td><td>'+ wsfw_public_param_block.partial_payment_data_html +'</td></tr></table>');		 
        }
        if (wsfw_public_param_block.wsfw_applied_wallet_amount == "1"){
            jQuery('.wc-block-components-totals-fees__via_wallet_partial_payment .wc-block-components-formatted-money-amount').html('-'+wsfw_public_param_block.wsfw_applied_wallet_amount_data);
        }
    }, "1000");

    

    jQuery(document).on( 'click','#partial_payment_wallet', function(){
        if ( jQuery('#partial_payment_wallet:checked').val() == 'enable' ) {
            if (jQuery('.partial_amount').length === 0) {
                jQuery( '.partial_payment' ).after('<tr class="partial_amount" ><td colspan="2"><p class="ajax_msg"></p><div class="discount_box"><p class="wallet-amount">' + wsfw_public_param_block.wsfw_partial_payment_msg + '</p><p class="wallet-amount form-row form-row-first"><input type="number" class="input-text" name="wallet_amount" min="0" id="wallet_amount" ></p><p class="form-row form-row-last"><button type="button" class="button" id="apply_wallet" name="apply_wallet" value="Apply coupon">' + wsfw_public_param_block.wsfw_apply_wallet_msg + '</button></p></div></td></tr>');
            }
        } else {
            jQuery( '.partial_amount' ).remove();
            jQuery( '.woocommerce-checkout-review-order-table .fee' ).remove();
            
            jQuery(document.body).trigger('update_checkout');
        }

    });

    // Unset totally amount in partial payment.
    jQuery(document).on( 'click','#partial_total_payment_wallet', function(){
       
			var checked = jQuery( '#partial_total_payment_wallet' ).is(':checked');
			if ( ! checked ) {
				jQuery.ajax({
					type: 'POST',
					url: wsfw_public_param_block.ajaxurl,
					data: {
						action: 'unset_wallet_session',
						checked: checked,
	
					},
					success: function( response ) {
						jQuery('#wps_wallet_show_total_msg').show();
                        jQuery('#partial_table_checkout').append('<tr id="wps_tr_amount_checkout" clospan="2"> <td >'+ wsfw_public_param_block.wsfw_unset_amount +'  </td></tr>');
                       
						jQuery('#wps_tr_amount_checkout').css('color', 'red');
						 setTimeout(function(){
							jQuery(document.body).trigger('update_checkout');
						 }, 1000);
                         window.location.reload();
					}
	
				}) .fail(function ( response ) {
					jQuery('#wps_wallet_show_total_msg').show();
					jQuery('#wps_wallet_show_total_msg').css('color', 'red');
					jQuery( '#wps_wallet_show_total_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
				});
			}
		});


        // Totally partial payment.
        jQuery(document).on( 'click','#partial_total_payment_wallet', function(){
        if ( jQuery('#partial_total_payment_wallet:checked').val() == 'total_enable' ) {
            jQuery('#wps_wallet_show_total_msg').show();
            
            var wallet_amount = jQuery( '#partial_total_payment_wallet' ).data('walletamount');
            var amount        = jQuery( '#wallet_amount' ).val();
            var checked       = jQuery( '#partial_total_payment_wallet' ).is(':checked');
            jQuery.ajax({
                type: 'POST',
                url: wsfw_public_param_block.ajaxurl,
                data: {
                    action: 'calculate_amount_total_after_wallet',
                    nonce: wsfw_public_param_block.nonce, 
                    wallet_amount: wallet_amount,
                    amount: amount,
                    checked: checked,
                },
                dataType: 'JSON',
                success: function( response ) {
                     
                    if ( response.status == true ) {
                        jQuery('#partial_table_checkout').append('<tr id="wps_tr_amount_checkout" clospan="2"><td > '+ response.message +'  </td></tr>');
                        jQuery('#wps_tr_amount_checkout').css('color', 'green');
                        setTimeout(function(){
                            jQuery(document.body).trigger('update_checkout');
                         }, 1000);
                        window.location.reload();
                    } else {
                        jQuery('#wps_wallet_show_total_msg').css('color', 'red');
                        jQuery( '#wps_wallet_show_total_msg' ).html(response.message);
                        jQuery( '.woocommerce-checkout-review-order-table .order-total' ).siblings('.fee').remove();
                        jQuery.ajax({
                            type: 'POST',
                            url: wsfw_public_param_block.ajaxurl,
                            data: {
                                action: 'unset_wallet_session',
            
                            },
                            success: function( response ) {
                                jQuery('#wps_wallet_show_total_msg').css('color', 'red');
                                jQuery('#wps_wallet_show_total_msg').html(wsfw_public_param_block.wsfw_unset_amount);
                                setTimeout(function(){
                                    jQuery(document.body).trigger('update_checkout');
                                 }, 1000);
                                window.location.reload();
                            }
            
                        }) .fail(function ( response ) {
                            jQuery( '#wps_wallet_show_total_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
                        });
                    }
                }

            })
            .fail(function ( response ) {
                jQuery( '#wps_wallet_show_total_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
            });

        }
    });
    
});
