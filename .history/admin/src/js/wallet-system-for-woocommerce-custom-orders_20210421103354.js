(function( $ ) {
	'use strict';

	 $(document).ready(function() {

       
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
