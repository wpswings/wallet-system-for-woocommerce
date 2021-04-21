(function( $ ) {
	'use strict';

	 $(document).ready(function() {


	});
	

	$(window).load(function(){
		// add select2 for multiselect.
		if( $(document).find('.mwb-defaut-multiselect').length > 0 ) {
			$(document).find('.mwb-defaut-multiselect').select2();
		}
	});

})( jQuery );
