(function( $ ) {
	'use strict';

	 $(document).ready(function() {

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
