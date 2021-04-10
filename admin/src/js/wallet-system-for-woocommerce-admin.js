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

	});

	$(window).load(function(){
		// add select2 for multiselect.
		if( $(document).find('.mwb-defaut-multiselect').length > 0 ) {
			$(document).find('.mwb-defaut-multiselect').select2();
		}
	});

	})( jQuery );
