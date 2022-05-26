jQuery(document).ready(function() {

    // count wallet recharge processing order.
    let walletCount = wsfw_recharge_param.wallet_count;
	jQuery.each( jQuery('a[href="admin.php?page=wallet_shop_order"]'), function( key, value ) {
		jQuery( this ).append('<span class="awaiting-mod update-plugins count-' + walletCount + '"><span class="processing-count">' + walletCount + '</span></span>');
	});

});
	

