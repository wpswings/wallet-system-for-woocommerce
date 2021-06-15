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
		$(function(){
			$( 'body' )
			.on( 'updated_checkout', function() {

				$( '#partial_payment_wallet' ).on( 'click', function() {
					if ( $('#partial_payment_wallet:checked').val() == 'enable' ) {
						if ($('.partial_amount').length === 0) {
							$( '.partial_payment' ).after('<tr class="partial_amount" ><td colspan="2"><p class="ajax_msg"></p><div class="discount_box"><p class="wallet-amount">' + wsfw_public_param.wsfw_partial_payment_msg + '</p><p class="wallet-amount form-row form-row-first"><input type="number" class="input-text" name="wallet_amount" id="wallet_amount"></p><p class="form-row form-row-last"><button type="button" class="button" id="apply_wallet" name="apply_wallet" value="Apply coupon">' + wsfw_public_param.wsfw_apply_wallet_msg + '</button></p></div></td></tr>');
						}
					} else {
						$( '.partial_amount' ).remove();
						$( '.woocommerce-checkout-review-order-table .fee' ).remove();
						
						$(document.body).trigger('update_checkout');

					}
					
				});

			});

			
		});
		
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
						$(document.body).trigger('update_checkout');
					}
	
				}) .fail(function ( response ) {
					$( '.ajax_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
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
					if ( response.status == true ) {
						$( '.ajax_msg' ).html(response.message);
						$(document.body).trigger('update_checkout');
					} else {
						$( '.ajax_msg' ).html(response.message);
						$( '.woocommerce-checkout-review-order-table .order-total' ).siblings('.fee').remove();
					}
				}

			})
			.fail(function ( response ) {
				$( '.ajax_msg' ).html('<span style="color:red;" >' + wsfw_public_param.wsfw_ajax_error + '</span>');		
			});

		});

	});

	$(document).on( 'blur','#mwb_wallet_recharge', function(){
		var amount = $(this).val();
		var minamount = $(this).data('min');
		var maxamount = $(this).data('max');
		minamount = parseInt(minamount);
		maxamount = parseInt(maxamount);
		if ( amount <= 0 ) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_amount_error);
			$('#mwb_recharge_wallet').prop('disabled', true);
		} else if ( amount > maxamount ) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_recharge_maxamount_error + maxamount);
			$('#mwb_recharge_wallet').prop('disabled', true);
		} else if ( amount < minamount) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_recharge_minamount_error + minamount);
			$('#mwb_recharge_wallet').prop('disabled', true);
		} else {
			$('.error').hide();
			$('#mwb_recharge_wallet').prop('disabled', false);
		}
	});

	$(document).on( 'blur','#mwb_wallet_transfer_amount', function(){
		var amount = $(this).val();
		var maxamount = $(this).data('max');
		if ( amount <= 0 ) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_amount_error);
			$('#mwb_proceed_transfer').prop('disabled', true);
		} else if ( amount > maxamount ) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_transfer_amount_error);
			$('#mwb_proceed_transfer').prop('disabled', true);
		} else {
			$('.error').hide();
			$('#mwb_proceed_transfer').prop('disabled', false);
		}
	});

	$(document).on( 'blur','#mwb_wallet_withdrawal_amount', function(){
		var amount = $(this).val();
		var maxamount = $(this).data('max');
		if ( amount <= 0 ) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_amount_error);
			$('#mwb_withdrawal_request').prop('disabled', true);
		} else if ( amount > maxamount ) {
			$('.error').show();
			$('.error').html(wsfw_public_param.wsfw_withdrawal_amount_error);
			$('#mwb_withdrawal_request').prop('disabled', true);
		} else {
			$('.error').hide();
			$('#mwb_withdrawal_request').prop('disabled', false);
		}
		
	});

})( jQuery );
