jQuery(document).ready(function() {

    // count wallet recharge processing order.
    let walletCount = wsfw_recharge_param.wallet_count;
	jQuery.each( jQuery('a[href="admin.php?page=wallet_shop_order"]'), function( key, value ) {
		jQuery( this ).append('<span class="awaiting-mod update-plugins count-' + walletCount + '"><span class="processing-count">' + walletCount + '</span></span>');
	});

	
	jQuery(document).on( 'click', '#search-submit', function(e) {
		debugger;
		e.preventDefault(e);
		var text = jQuery('#search_id-search-input').val();
		var url = window.location.href+'&s='+text;
		window.location.href=url;
	});
});
	

