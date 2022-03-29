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

		// on clicking element change the input type password to text or vice-versa
		$(document).on( 'click', '.wps-password-hidden', function() {
            if ($('.wps-form__password').attr('type') == 'text') {
                $('.wps-form__password').attr('type', 'password');
            } else {
                $('.wps-form__password').attr('type', 'text');
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
				$( '#export_user_wallet' ).after('<span style="color:red;" >' + wsfw_admin_param.wsfw_ajax_error + '</span>');	
			});
		});

		// Download the user's wallet csv file on clicking button
		function download(filename, text) {
			var element = document.createElement('a');
			element.setAttribute('href', text);
			element.setAttribute('download', filename);
		
			element.style.display = 'none';
			document.body.appendChild(element);
			// automatically run the click event for anchor tag
			element.click();
		
			document.body.removeChild(element);
			

		}

		$(document).on( 'blur','#wsfw_wallet_amount_for_users', function(){
			var amount = $('#wsfw_wallet_amount_for_users').val();
			if( amount == '' ) {
				$('.error').hide();
				$('#update_wallet').prop('disabled', false);
			} else if ( amount <= 0 ) {
				$(this).parent().after('<p class="error">' + wsfw_admin_param.wsfw_amount_error + '</p>');
				$('.error').show();

				$('#update_wallet').prop('disabled', true);
			} else {
				$('.error').hide();
				$('#update_wallet').prop('disabled', false);
			}
			
		
		});
		$(document).on( 'click', '#update_wallet', function(e) {
			e.preventDefault(e);
			$('.wps_wallet-update--popupwrap').show();
		});
		$(document).on("click", "#confirm_updatewallet", function(){
			$('.wps_wallet-update--popupwrap').hide();
		});
	
		$(document).on("click", "#cancel_walletupdate", function(){
			$('.wps_wallet-update--popupwrap').hide();
		});

		$(document).on("click", ".edit_wallet", function(e){
			e.preventDefault(e);
			var userid = $(this).attr('data-userid');
			$('.wps_wallet-edit--popupwrap').show();
			$('.wps_wallet-edit--popupwrap').find('.wps_wallet-edit-popup-btn').before('<input class="userid" type="hidden" name="user_id" value="'+userid+'">');
		});

		$(document).on("click", "#close_wallet_form", function(e) {
			$('.wps_wallet-edit-popup-fill').val('');
			$('.error').html('');
			$('.wps_wallet-edit--popupwrap').find('.userid').remove();
			$('.wps_wallet-edit--popupwrap').hide();

		});

		// update wallet and status on changing status of wallet request
		$(document).on( 'change', 'select#wps-wpg-gen-table_status', function() {
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
					nonce: wsfw_admin_param.nonce,
					withdrawal_id: withdrawal_id,
					user_id: user_id,
					status: status,
					
				},
				datatType: 'JSON',
				success: function( response ) {
					$( '.wps-wpg-withdrawal-section-table' ).before('<div class="notice notice-' + response.msgType + ' is-dismissible wps-errorr-8"><p>' + response.msg + '</p></div>');		
					loader.hide();
					setTimeout(function () {
						location.reload();
					}, 1000);
					

				},

			})
			.fail(function ( response ) {
				$( '.wps-wpg-withdrawal-section-table' ).before('<div class="notice notice-error is-dismissible wps-errorr-8"><p>' + wsfw_admin_param.wsfw_ajax_error + '</p></div>');		
				loader.hide();
			});
		});

		// update wallet and status on changing status of wallet request
		$(document).on( 'change', '.wsfw_restrict_user', function() {
			debugger;
			var user_id='';
			if ( $(this).length > 0 ) {
				var user_name = $(this)[0].id;
				var user_id = jQuery('#'+user_name).attr('user_id');
			}
		var restriction_status = jQuery('#'+user_name).attr('aria-checked');
			var loader = $(this).siblings('#overlay');
			loader.show();
			$.ajax({
				type: 'POST',
				url: wsfw_admin_param.ajaxurl,
				data: {
					action: 'restrict_user_from_wallet_access',
					nonce: wsfw_admin_param.nonce,
					user_id: user_id,
					restriction_status:restriction_status,
					
				},
				datatType: 'JSON',
				success: function( response ) {
				debugger;
				loader.hide();
				},

			})
			.fail(function ( response ) {
				loader.hide();
			});
		});


		$('#search_in_table').keyup(function(){
			var table = $('#wps-wpg-gen-table').DataTable();
			table.search($(this).val()).draw() ;
		});

		$(document).on('click', '#clear_table', function(){
			$('#search_in_table').val('');
			$('#min').val('');
			$('#max').val('');
			$('#filter_status').prop('selectedIndex',0);
			var table = $('.wps-wpg-gen-section-table').DataTable();
			table.search( '' ).columns().search( '' ).draw();

		});

		$('#wps_wallet-edit-popup-input').keyup(function() {
			$('.error').hide();
			$('span.error-keyup-1').hide();
			var inputVal = $(this).val();
			var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
			if(!numericReg.test(inputVal)) {
				$('.error').show();
				$('.error').html(wsfw_admin_param.wsfw_amount_error);
			}
		});

	});
	

	$(window).load(function(){
		// add select2 for multiselect.
		if( $(document).find('.wps-defaut-multiselect').length > 0 ) {
			$(document).find('.wps-defaut-multiselect').select2();
		}
	});

})( jQuery );
