(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	 $(document).ready(function() {

		const MDCText = mdc.textField.MDCTextField;
        const textField = [].map.call(document.querySelectorAll('.mdc-text-field'), function(el) {
            return new MDCText(el);
        });
        const MDCRipple = mdc.ripple.MDCRipple;
        const buttonRipple = [].map.call(document.querySelectorAll('.mdc-button'), function(el) {
            return new MDCRipple(el);
        });
        const MDCSwitch = mdc.switchControl.MDCSwitch;
        const switchControl = [].map.call(document.querySelectorAll('.mdc-switch'), function(el) {
            return new MDCSwitch(el);
        });

        $('.mwb-password-hidden').click(function() {
            if ($('.mwb-form__password').attr('type') == 'text') {
                $('.mwb-form__password').attr('type', 'password');
            } else {
                $('.mwb-form__password').attr('type', 'text');
            }
        });

		// on clicking call ajax for getting user's wallet details
		$(document).on( 'click', '#export_user_wallet', function() {
			$.ajax({
				type: 'POST',
				url: wsfw_admin_param.ajaxurl,
				data: {
					action: 'export_users_wallet',

				},
				datatType: 'JSON',
				success: function( response ) {
					console.log(response);
					var filename = 'users_wallet.csv';
					let csvContent = "data:text/csv;charset=utf-8,";
					response.forEach(function(rowArray) {
						let row = rowArray.join(",");
						csvContent += row + "\r\n";
					});
					
					var encodedUri = encodeURI(csvContent);
					download(filename, encodedUri);
				}

			})
			.fail(function ( response ) {
				$( '#export_user_wallet' ).after('<span style="color:red;" >An error occured!</span>');		
			});
		});

		// Download the user's wallet csv file on clicking button
		function download(filename, text) {
			var element = document.createElement('a');
			element.setAttribute('href', text);
			element.setAttribute('download', filename);
		
			element.style.display = 'none';
			document.body.appendChild(element);
		
			element.click();
		
			document.body.removeChild(element);
			

		}

		$(document).on( 'blur','#wsfw_wallet_amount_for_users', function(){
			var amount = $('#wsfw_wallet_amount_for_users').val();
			if( amount == '' ) {
				$('.error').hide();
				$('#update_wallet').prop('disabled', false);
			} else if ( amount <= 0 ) {
				console.log(amount);
				$(this).parent().after('<p class="error">Enter amount greater than 0</p>');
				$('.error').show();

				$('#update_wallet').prop('disabled', true);
			} else {
				$('.error').hide();
				$('#update_wallet').prop('disabled', false);
			}
			
		
		});
		$(document).on( 'click', '#update_wallet', function(e) {
			e.preventDefault(e);
			$('.mwb_wallet-update--popupwrap').show();
		});
		$(document).on("click", "#confirm_updatewallet", function(){
			$('.mwb_wallet-update--popupwrap').hide();
		});
	
		$(document).on("click", "#cancel_walletupdate", function(){
			$('.mwb_wallet-update--popupwrap').hide();
		});

		$(document).on("click", ".edit_wallet", function(e){
			e.preventDefault(e);
			var userid = $(this).attr('data-userid');
			$('.mwb_wallet-edit--popupwrap').show();
			$('.mwb_wallet-edit--popupwrap').find('.mwb_wallet-edit-popup-btn').before('<input class="userid" type="hidden" name="user_id" value="'+userid+'">');
		});

		$(document).on("click", "#close_wallet_form", function(e){
			$('.mwb_wallet-edit-popup-fill').val('');
			$('.error').html('');
			$('.mwb_wallet-edit--popupwrap').find('.userid').remove();
			$('.mwb_wallet-edit--popupwrap').hide();

		});

		// update wallet and status on changing status of wallet request
		$(document).on( 'change', 'select#mwb-wpg-gen-table_status', function() {
			var withdrawal_id = $(this).siblings('input[name=withdrawal_id]').val();
			var user_id = $(this).siblings('input[name=user_id]').val();
			var status = $(this).find(":selected").val();
			var loader = $(this).siblings('#overlay');
			loader.show();
			$.ajax({
				type: 'POST',
				url: wsfw_admin_param.ajaxurl,
				data: {
					action: 'change_wallet_withdrawan_status',
					withdrawal_id: withdrawal_id,
					user_id: user_id,
					status: status,
					
				},
				datatType: 'JSON',
				success: function( response ) {
					$( '.mwb-wpg-withdrawal-section-table' ).before('<div class="notice notice-' + response.msgType + ' is-dismissible mwb-errorr-8"><p>' + response.msg + '</p></div>');		
					loader.hide();
					setTimeout(function () {
						location.reload();
					}, 1000);
					

				},

			})
			.fail(function ( response ) {
				$( '.mwb-wpg-withdrawal-section-table' ).before('<div class="notice notice-error is-dismissible mwb-errorr-8"><p>An error occured !</p></div>');		
				loader.hide();
			});
		});




		$('#search_in_table').keyup(function(){
			var table = $('#mwb-wpg-gen-table').DataTable();
			table.search($(this).val()).draw() ;
		});

		$(document).on('click', '#clear_table', function(){
			$('#search_in_table').val('');
			$('#min').val('');
			$('#max').val('');
			$('#filter_status').prop('selectedIndex',0);
			var table = $('.mwb-wpg-gen-section-table').DataTable();
			table.search( '' ).columns().search( '' ).draw();

		});

		$('#mwb_wallet-edit-popup-input').keyup(function() {
			$('.error').hide();
			$('span.error-keyup-1').hide();
			var inputVal = $(this).val();
			var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
			if(!numericReg.test(inputVal)) {
				$('.error').show();
				$('.error').html('Enter amount greater than 0');
			}
		});

	});
	

	$(window).load(function(){
		// add select2 for multiselect.
		if( $(document).find('.mwb-defaut-multiselect').length > 0 ) {
			$(document).find('.mwb-defaut-multiselect').select2();
		}
	});

})( jQuery );
