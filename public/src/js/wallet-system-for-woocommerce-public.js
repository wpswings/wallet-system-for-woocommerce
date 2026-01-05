(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $(document).ready(function(){

		var wps_wsfw_notification_color = wsfw_public_param.wps_wsfw_notification_color;
		$(':root').css('--wallet-pc','#483de0');
		if( wps_wsfw_notification_color ){
			$(':root').css('--wallet-pc',wps_wsfw_notification_color);

		}
		
		$(function(){

			if ( window.history.replaceState ) {
				window.history.replaceState( null, null, window.location.href );
			  }
			$( 'body' )
			.on( 'updated_checkout', function() {

			});
			
		});

		if ( wsfw_public_param.wsfw_wallet_paypal == 'on' ) {

		
		var $option_withdrawal;
		$option_withdrawal = jQuery('#wps_wallet_withdrawal_option').val();
		if ($option_withdrawal != undefined ) {
			
			if ( $option_withdrawal == 'paypal' ) {
				jQuery(jQuery('#wps_wallet_withdrawal_paypal_user_email').parent()).show();
				jQuery(jQuery('#wps_wallet_withdrawal_fee').parent()).hide();
				jQuery('#wps_wallet_withdrawal_paypal_user_email').attr('required','required');

			} else{
				jQuery(jQuery('#wps_wallet_withdrawal_paypal_user_email').parent()).hide();
				jQuery(jQuery('#wps_wallet_withdrawal_fee').parent()).show();
				jQuery('#wps_wallet_withdrawal_paypal_user_email').attr('required',false);
			}
		} else{
			jQuery('#wps_wallet_withdrawal_paypal_user_email').attr('required','required');
			jQuery(jQuery('#wps_wallet_withdrawal_fee').parent()).hide();
		}
		
	}
		// Unset manually amount in partial payment.
		$(document).on( 'click','#wps_withdrawal_table_div', function(){
			jQuery('.wps_withdrawal_table').show();

		});

		$(document).on( 'click','#wps_fund_send_table_div', function(){
			jQuery('.wps_fund_send_table').show();
			jQuery('.wps_fund_recieve_table').hide();

		});
		$(document).on( 'click','#wps_fund_recieve_table_div', function(){
			jQuery('.wps_fund_recieve_table').show();
			jQuery('.wps_fund_send_table').hide();

		});

		// Unset manually amount in partial payment.
		$(document).on( 'change','#wps_wallet_withdrawal_option', function(){
			$option_withdrawal = jQuery('#wps_wallet_withdrawal_option').val();
		
			if ( $option_withdrawal == 'paypal' ) {
				jQuery(jQuery('#wps_wallet_withdrawal_paypal_user_email').parent()).show();
				jQuery(jQuery('#wps_wallet_withdrawal_fee').parent()).hide();
				jQuery('#wps_wallet_withdrawal_paypal_user_email').attr('required','required');

			} else{
				jQuery(jQuery('#wps_wallet_withdrawal_paypal_user_email').parent()).hide();
				jQuery(jQuery('#wps_wallet_withdrawal_fee').parent()).show();
				jQuery('#wps_wallet_withdrawal_paypal_user_email').attr('required',false);
			}
			
		});

		// Manually partial payment.
		$(document).on( 'click','#partial_payment_wallet', function(){					
			if ( $('#partial_payment_wallet:checked').val() == 'enable' ) {
				if ($('.partial_amount').length === 0) {
					$( '.partial_payment' ).after('<tr class="partial_amount" ><td colspan="2"><p class="ajax_msg"></p><div class="discount_box"><p class="wallet-amount">' + wsfw_public_param.wsfw_partial_payment_msg + '</p><p class="wallet-amount form-row form-row-first"><input type="number" class="input-text" name="wallet_amount"  min="0" id="wallet_amount"></p><p class="form-row form-row-last"><button type="button" class="button" id="apply_wallet" name="apply_wallet" value="Apply coupon">' + wsfw_public_param.wsfw_apply_wallet_msg + '</button></p></div></td></tr>');
				}
			} else {
				$( '.partial_amount' ).remove();
				$( '.woocommerce-checkout-review-order-table .fee' ).remove();
				
				
				$(document.body).trigger('update_checkout');
			}
		});
		

		// Totally partial payment.
		$(document).on( 'click','#partial_total_payment_wallet', function(){
			if ( $('#partial_total_payment_wallet:checked').val() == 'total_enable' ) {
				$('#wps_wallet_show_total_msg').show();

				var wallet_amount = $( '#partial_total_payment_wallet' ).data('walletamount');
				var amount        = $( '#wallet_amount' ).val();
				var checked       = $( '#partial_total_payment_wallet' ).is(':checked');
				$.ajax({
					type: 'POST',
					url: wsfw_public_param.ajaxurl,
					data: {
						action: 'calculate_amount_total_after_wallet',
						nonce: wsfw_public_param.nonce, 
						wallet_amount: wallet_amount,
						amount: amount,
						checked: checked,

					},
					dataType: 'JSON',
					success: function( response ) {
						 
						if ( response.status == true ) {
							
							$('#wps_wallet_show_total_msg').css('color', 'green');
							$( '#wps_wallet_show_total_msg' ).html(response.message);
							setTimeout(function(){
								$(document.body).trigger('update_checkout');
							 }, 1000);
						} else {
							$('#wps_wallet_show_total_msg').css('color', 'red');
							$( '#wps_wallet_show_total_msg' ).html(response.message);
							$( '.woocommerce-checkout-review-order-table .order-total' ).siblings('.fee').remove();
							$.ajax({
								type: 'POST',
								url: wsfw_public_param.ajaxurl,
								data: {
									action: 'unset_wallet_session',
				
								},
								success: function( response ) {
									$('#wps_wallet_show_total_msg').css('color', 'red');
									$('#wps_wallet_show_total_msg').html(wsfw_public_param.wsfw_unset_amount);
									setTimeout(function(){
										$(document.body).trigger('update_checkout');
									 }, 1000);
								}
				
							}) .fail(function ( response ) {
								$( '#wps_wallet_show_total_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
							});
						}
					}

				})
				.fail(function ( response ) {
					$( '#wps_wallet_show_total_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
				});

			}
		});
		
		// Unset manually amount in partial payment.
		$(document).on( 'click','#partial_payment_wallet', function(){
			var checked = $( '#partial_payment_wallet' ).is(':checked');
			if ( ! checked ) {
				$.ajax({
					type: 'POST',
					url: wsfw_public_param.ajaxurl,
					data: {
						action: 'unset_wallet_session',
						checked: checked,
	
					},
					success: function( response ) {
						window.location.reload();
						$(document.body).trigger('update_checkout');
					}
	
				}) .fail(function ( response ) {
					$( '.ajax_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
				});
			}
			
		});

		// Unset totally amount in partial payment.
		$(document).on( 'click','#partial_total_payment_wallet', function(){
			var checked = $( '#partial_total_payment_wallet' ).is(':checked');
			if ( ! checked ) {
				$.ajax({
					type: 'POST',
					url: wsfw_public_param.ajaxurl,
					data: {
						action: 'unset_wallet_session',
						checked: checked,
	
					},
					success: function( response ) {
						$('#wps_wallet_show_total_msg').show();
						$('#wps_wallet_show_total_msg').css('color', 'red');
						$('#wps_wallet_show_total_msg').html(wsfw_public_param.wsfw_unset_amount);
						setTimeout(function(){
							$(document.body).trigger('update_checkout');
						 }, 1000);
					}
	
				}) .fail(function ( response ) {
					$('#wps_wallet_show_total_msg').show();
					$('#wps_wallet_show_total_msg').css('color', 'red');
					$( '#wps_wallet_show_total_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
				});
			}
			
		});

		$(document).on( 'click','#apply_wallet', function(){

			var wallet_amount = $( '#partial_payment_wallet' ).data('walletamount');
			var amount = $( '#wallet_amount' ).val();
			var checked = $( '#partial_payment_wallet' ).is(':checked');
			$.ajax({
				type: 'POST',
				url: wsfw_public_param.ajaxurl,
				data: {
					action: 'calculate_amount_after_wallet',
					nonce: wsfw_public_param.nonce, 
					wallet_amount: wallet_amount,
					amount: amount,
					checked: checked,

				},
				dataType: 'JSON',
				success: function( response ) {
				
					if ( response.status == true ||  response.status == 200 ) {
						$( '.ajax_msg' ).html(response.message);

						$(document.body).trigger('update_checkout');
						window.location.reload();
					} else {
						$( '.ajax_msg' ).html(response.message);
						$( '.woocommerce-checkout-review-order-table .order-total' ).siblings('.fee').remove();
						$.ajax({
							type: 'POST',
							url: wsfw_public_param.ajaxurl,
							data: {
								action: 'unset_wallet_session',
			
							},
							success: function( response ) {
								$(document.body).trigger('update_checkout');
								
							}
			
						}) .fail(function ( response ) {
							$( '.ajax_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
						});
					}
				}

			})
			.fail(function ( response ) {
				if ( response.responseText != '' ) {
				//	$( '.ajax_msg' ).html(response.responseText);
					window.location.reload();
				} else{
					$( '.ajax_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
			
				}
			});

		});

	});

	$(document).on( 'blur','#wps_wallet_recharge', function(){
		var amount = $(this).val();
		var minamount = $(this).data('min');
		var maxamount = $(this).data('max');
		minamount = parseFloat(minamount);
		maxamount = parseFloat(maxamount);
		if ( amount <= 0 ) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_amount_error);
			$('#wps_recharge_wallet').prop('disabled', true);
		} else if ( amount > maxamount ) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_recharge_maxamount_error + maxamount);
			$('#wps_recharge_wallet').prop('disabled', true);
		} else if ( amount < minamount) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_recharge_minamount_error + minamount);
			$('#wps_recharge_wallet').prop('disabled', true);
		} else {
			$('.error').hide();
			$('#wps_recharge_wallet').prop('disabled', false);
		}
	});

	$(document).on( 'blur','#wps_wallet_transfer_user_email', function(){
		var user_email = $(this).val();
		var current_email = $( this ).data('current-email');
		if ( user_email == current_email ) {
			$('.transfer-error').show();
			$('.transfer-error').html(wsfw_public_param.wsfw_wallet_transfer);
			$('#wps_proceed_transfer').prop('disabled', true);
		} else {
			$('.transfer-error').hide();
			$('#wps_proceed_transfer').prop('disabled', false);
		}
	});

	$(document).on( 'blur','#wps_wallet_transfer_amount', function(){
		var amount = $(this).val();
		var maxamount = $(this).data('max');
		var min_transfer_amount = $(this).data('mintransfer');
		var max_transfer_amount = $(this).data('maxtransfer');

		if ( min_transfer_amount > amount ){
			if( min_transfer_amount !=0 ){

				$('.error').show();
				$('.error').html(wsfw_public_param.wsfw_min_wallet_transfer +' '+min_transfer_amount );
				$('#wps_proceed_transfer').prop('disabled', true);
			}

		} else if( amount > max_transfer_amount ) {
			if( max_transfer_amount !=0 ){

				$('.error').show();
				$('.error').html(wsfw_public_param.wsfw_max_wallet_transfer +' '+max_transfer_amount );
				$('#wps_proceed_transfer').prop('disabled', true);
			} else {

				if ( amount <= 0 ) {
					$('.error').show();
					$('.error').html(wsfw_public_param.wsfw_amount_error);
					$('#wps_proceed_transfer').prop('disabled', true);
				} else if ( amount > maxamount ) {
					$('.error').show();
					$('.error').html(wsfw_public_param.wsfw_transfer_amount_error);
					$('#wps_proceed_transfer').prop('disabled', true);
				} else {
					$('.error').hide();
					$('#wps_proceed_transfer').prop('disabled', false);
				}
			}
			 
		}else {
			$('.error').hide();
			$('#wps_proceed_transfer').prop('disabled', false);
		}
	});

	$(document).on( 'blur','#wps_wallet_withdrawal_amount', function(){
		var amount = $(this).val();
		amount = parseFloat( amount );
		var maxamount = $(this).data('max');

		var min_withdrwal_amount = $(this).data('minwithdrawal');
		var max_withdrwal_amount = $(this).data('maxwithdrawal');
		var data_max = $(this).data('data-max');

		if(amount > maxamount){
			$('.error').show();
					$('.error').html(wsfw_public_param.wsfw_withdrawal_amount_error);
					$('#wps_withdrawal_request').prop('disabled', true);
					return;
		} 

		if ( min_withdrwal_amount > amount ){
			if( min_withdrwal_amount !=0 ){

				$('.error').show();
				$('.error').html(wsfw_public_param.wsfw_min_wallet_withdrawal +' '+min_withdrwal_amount );
				$('#wps_withdrawal_request').prop('disabled', true);
			}

		} else if( amount > max_withdrwal_amount ) {
			if( max_withdrwal_amount !=0 ){

				$('.error').show();
				$('.error').html(wsfw_public_param.wsfw_max_wallet_withdrawal +' '+max_withdrwal_amount );
				$('#wps_withdrawal_request').prop('disabled', true);
			} else {

				if ( amount <= 0 ) {
					$('.error').show();
					$('.error').html(wsfw_public_param.wsfw_amount_error );
					$('#wps_withdrawal_request').prop('disabled', true);
				}else {
					$('.error').hide();
					$('#wps_withdrawal_request').prop('disabled', false);
				}
			}
			 
		}else{
			$('.error').hide();
			$('#wps_withdrawal_request').prop('disabled', false);
		}

		
	});


	//fund request feature.
	$(document).on( 'change', 'select#wps-wpg-gen-table_status', function() {
		var request_id = $(this).siblings('input[name=request_id]').val();
		var requesting_user_id = $(this).siblings('input[name=requesting_user_id]').val();
		var requested_user_id = $(this).siblings('input[name=requested_user_id]').val();
		var status = $(this).find(":selected").val();
		
		var withdrawal_balance = $(this).siblings('input[name=withdrawal_balance]').val();
		var loader = $(this).siblings('#overlay');
		loader.show();
		$.ajax({
			type: 'POST',
			url: wsfw_public_param.ajaxurl,
			data: {
				action: 'change_wallet_fund_request_status',
				nonce: wsfw_public_param.nonce,
				request_id: request_id,
				requesting_user_id: requesting_user_id,
				withdrawal_balance: withdrawal_balance,
				requested_user_id: requested_user_id,
				status: status,
				
			},
			datatType: 'JSON',
			success: function( response ) {
				console.log(response);
				$( '.wps-wpg-withdrawal-section-table' ).before('<div class="notice notice-' + response.msgType + ' is-dismissible wps-errorr-8"><p>' + response.msg + '</p></div>');		
			
				loader.hide();
				setTimeout(function () {
					location.reload();
				}, 2000);
				

			},

		})
		.fail(function ( response ) {
			$( '.wps-wpg-withdrawal-section-table' ).before('<div class="notice notice-error is-dismissible wps-errorr-8"><p>' + wsfw_public_param.wsfw_ajax_error + '</p></div>');		
			loader.hide();
		});
	});
	

})( jQuery );

function copyshareurl() {
    const pasteText = document.querySelector("#pasteText");
    // Get the text field.
    var copyText = jQuery( '#wps_wsfw_copy' ).html();
    /* Get the text field */
    var copyText = document.getElementById( "wps_wsfw_copy" );
    if (navigator.clipboard) {
		/* Prevent iOS keyboard from opening */
		copyText.readOnly = true;
		/* Change the input's type to text so its text becomes selectable */
		copyText.type = 'text';
		/* Select the text field */
		copyText.select();
		copyText.setSelectionRange( 0, 99999 ); /* For mobile devices */
		/* Copy the text inside the text field */
		navigator.clipboard.writeText( copyText.value );
		/* Replace the tooltip's text */
		var tooltip       = document.getElementById( "myTooltip_referral" );
		tooltip.innerHTML = "Copied: " + copyText.value;
		/* Change the input's type back to hidden */
		copyText.type     = 'hidden';
		var tooltip       = document.getElementById( "myTooltip_referral" );
		tooltip.innerHTML = "       Copied!";
		jQuery( '.wps_wsfw_btn_copy' ).hide();
		// Alert the copied text.
    } else {
        var textArea = document.createElement("textarea");
		// Set the text content to be copied.
		textArea.value = copyText.value;
		// Set the text area to be invisible.
		textArea.style.position = "fixed";
		textArea.style.top = "-9999px";
		// Append the text area to the document.
		document.body.appendChild(textArea);
		// Select the text content in the text area.
		textArea.select();
		try {
			// Execute the copy command.
			var successful = document.execCommand('copy');
			var message = successful ? 'Text copied to clipboard' : 'Unable to copy text';
			console.log(message);
		} catch (err) {
			console.error('Error in copying text:', err);
		}
		// Clean up - remove the temporary text area.
		document.body.removeChild(textArea);
		var tooltip       = document.getElementById( "myTooltip_referral" );
		tooltip.innerHTML = "Copied: " + copyText.value;
		/* Change the input's type back to hidden */
		copyText.type     = 'hidden';
		var tooltip       = document.getElementById( "myTooltip_referral" );
		tooltip.innerHTML = "       Copied!";
		jQuery( '.wps_wsfw_btn_copy' ).hide();
    }
}
jQuery(document).ready(function($) {
    var $methodSelect  = $('#wps_wallet_transfer_method');
    var $emailWrap     = $('#wps_wallet_transfer_email_wrap');
    var $walletIdWrap  = $('#wps_wallet_transfer_walletid_wrap');
    var $emailInput    = $('#wps_wallet_transfer_user_email');
    var $walletIdInput = $('#wps_wallet_transfer_user_walletid');

    // Run once on page load to set correct state
    toggleFields($methodSelect.val());

    // Change event
    $methodSelect.on('change', function() {
        var selectedVal = $(this).val();
        toggleFields(selectedVal);
    });

    function toggleFields(selectedVal) {
        if (selectedVal === 'wallet_id') {
            $emailWrap.hide();
            $walletIdWrap.show();
            $emailInput.removeAttr('required');
            $walletIdInput.attr('required', 'required');
        } else {
            $walletIdWrap.hide();
            $emailWrap.show();
            $walletIdInput.removeAttr('required');
            $emailInput.attr('required', 'required');
        }
    }
});


jQuery(document).ready(function($) {
    var $methodSelect   = $('#wps_wallet_fund_request_another_method');
    var $emailWrap      = $('#wps_wallet_fund_request_another_email_wrap');
    var $walletIdWrap   = $('#wps_wallet_fund_request_another_walletid_wrap');
    var $emailInput     = $('#wps_wallet_fund_request_another_user_email');
    var $walletIdInput  = $('#wps_wallet_fund_request_another_user_walletid');

    // Initialize state on page load
    toggleFields($methodSelect.val());

    $methodSelect.on('change', function() {
        toggleFields($(this).val());
    });

    function toggleFields(selectedVal) {
        if (selectedVal === 'wallet_id') {
            $emailWrap.hide();
            $walletIdWrap.show();
            $emailInput.removeAttr('required');
            $walletIdInput.attr('required', 'required');
        } else {
            $walletIdWrap.hide();
            $emailWrap.show();
            $walletIdInput.removeAttr('required');
            $emailInput.attr('required', 'required');
        }
    }
});

