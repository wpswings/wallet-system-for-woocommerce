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
						$( '.partial_payment' ).after('<tr class="partial_amount" ><td colspan="2"><p class="ajax_msg"></p><div class="discount_box"><p class="wallet-amount">Amount want to use from wallet</p><p class="wallet-amount form-row form-row-first"><input type="number" class="input-text" name="wallet_amount" id="wallet_amount" placeholder="Amount want to use from wallet"></p><p class="form-row form-row-last"><button type="button" class="button" id="apply_wallet" name="apply_wallet" value="Apply coupon">Apply wallet</button></p></div></td></tr>');

						//$( '#partial_payment_wallet_field' ).after('<p class="ajax_msg"></p><div class="discount_box" ><p class="wallet-amount" >Amount want to use from wallet</p><p class="wallet-amount form-row form-row-first"><input type="number" class="input-text" name="wallet_amount" id="wallet_amount" placeholder="Amount want to use from wallet" ></p><p class="form-row form-row-last"><button type="button" class="button" id="apply_wallet" name="apply_wallet" value="Apply coupon">Apply wallet</button></p></div>');
						
					} else {
						$( '.partial_amount' ).remove();
						//$( '.partial_payment .ajax_msg' ).remove();
						$( '.woocommerce-checkout-review-order-table .fee' ).remove();
						
						$(document.body).trigger('update_checkout');

					}
					
				});

			});

			
		});
		
		$(document).on( 'click','#partial_payment_wallet', function(){ 
			var checked = $( '#partial_payment_wallet' ).is(':checked');
			if ( ! checked ) {
				console.log(checked);
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
					$( '.ajax_msg' ).html('<span style="color:red;" >An error occured!</span>');		
				});
			}
			
		});

		$(document).on( 'click','#apply_wallet', function(){
			var wallet_amount = $( '#partial_payment_wallet' ).data('walletamount');
			var amount = $( '#wallet_amount' ).val();
			var checked = $( '#partial_payment_wallet' ).is(':checked');
			console.log(wallet_amount);
			console.log(amount);
			console.log(checked);

			$.ajax({
				type: 'POST',
				url: wsfw_public_param.ajaxurl,
				data: {
					action: 'calculate_amount_after_wallet',
					wallet_amount: wallet_amount,
					amount: amount,
					checked: checked,

				},
				dataType: 'JSON',
				success: function( response ) {
				
					
					if ( response.message !== 'Please enter amount less than or equal to wallet balance' ) {
						console.log('update cart');
						$( '.ajax_msg' ).html(response.message);
						$(document.body).trigger('update_checkout');
					} else {
						$( '.ajax_msg' ).html(response.message);
						$( '.woocommerce-checkout-review-order-table .order-total' ).siblings('.fee').remove();
						//$(document.body).trigger('update_checkout');
					}
				}

			})
			.fail(function ( response ) {
				$( '.ajax_msg' ).html('<span style="color:red;" >An error occured!</span>');		
			});

		});
		$('.mwb-wallet-userselect2').select2({

			ajax: {
				url: wsfw_public_param.ajaxurl,
				dataType: 'json',
				delay: 200,
				data: function(params){

					return{

						email: params.term,

						action: 'mwb_search_for_user'
					};
				},

				processResults: function(data){
					var users = [];
					if(data)
					{
						$.each(data, function(index,text){

							text[2] += '(' + text[1] + ')';

							users.push({id:text[0], text:text[2]});
						});
					}

					return{

						results: users
					};
				},
				cache:true
			},

			minimumInputLength: 4
		});

		$('#wallet_payment_method').click(function() {
			
			var method = $(this).val();
			console.log(method);
			if ( method == 'Bank Transfer' ) {
				$('.show-on-bank-transfer').show();
				$('.show-on-paypal').hide();
				$('.error').hide();
			} else if ( method == 'PayPal' ) {
				$('.show-on-paypal').show();
				$('.show-on-bank-transfer').hide();
				$('.error').hide();
			} else {
				$('.show-on-bank-transfer').hide();
				$('.show-on-paypal').hide();
				$('.error').hide();
				$('#mwb_withdrawal_request').prop('disabled', true);
			}

		});

		$('#mwb_wallet_paypal_email').blur(function() {
			var email = $(this).val();
			if ( email == '' ) {
				$('.error').show();
				$('.error').html('Please enter an email id');
				$('#mwb_withdrawal_request').prop('disabled', true);
			} else {
				$('.error').hide();
				$('#mwb_withdrawal_request').prop('disabled', false);
			}
		});

		$('#mwb_wallet_bank_account_name').blur(function() {
			var accountname = $(this).val();
			if ( accountname == '' ) {
				$('.error').show();
				$('.error').html('Please enter your account name');
				$('#mwb_withdrawal_request').prop('disabled', true);
			} else {
				$('.error').hide();
				$('#mwb_withdrawal_request').prop('disabled', false);
			}
		});
		$('#mwb_wallet_bank_account_no').blur(function() {
			var accountno = $(this).val();
			if ( accountno == '' ) {
				$('.error').show();
				$('.error').html('Please enter your account number');
				$('#mwb_withdrawal_request').prop('disabled', true);
			} else {
				$('.error').hide();
				$('#mwb_withdrawal_request').prop('disabled', false);
			}
		});
		$('#mwb_wallet_bank_sort_code').blur(function() {
			var sortcode = $(this).val();
			if ( sortcode == '' ) {
				$('.error').show();
				$('.error').html('Please enter sort code');
				$('#mwb_withdrawal_request').prop('disabled', true);
			} else {
				$('.error').hide();
				$('#mwb_withdrawal_request').prop('disabled', false);
			}
		});


	});

	$(document).on( 'blur','#mwb_wallet_recharge', function(){
		var amount = $('#mwb_wallet_recharge').val();
		if ( amount <= 0 ) {
			$('.error').show();
			$('.error').html('Enter amount greater than 0');
			$('#mwb_recharge_wallet').prop('disabled', true);
			console.log('disable');
		} else {
			$('.error').hide();
			$('#mwb_recharge_wallet').prop('disabled', false);
			console.log('enable');
		}
		
	});

	$(document).on( 'blur','#mwb_wallet_transfer_amount', function(){
		var amount = $('#mwb_wallet_transfer_amount').val();
		if ( amount <= 0 ) {
			$('.error').show();
			$('.error').html('Enter amount greater than 0');
			$('#mwb_proceed_transfer').prop('disabled', true);
		} else {
			$('.error').hide();
			$('#mwb_proceed_transfer').prop('disabled', false);
		}
		
	});

})( jQuery );
