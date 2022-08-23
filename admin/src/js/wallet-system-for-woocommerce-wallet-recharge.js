jQuery(document).ready(function($) {
    // count wallet recharge processing order.
    let walletCount = wsfw_recharge_param.wallet_count;
	jQuery.each( jQuery('a[href="admin.php?page=wallet_shop_order"]'), function( key, value ) {
		jQuery( this ).append('<span class="awaiting-mod update-plugins count-' + walletCount + '"><span class="processing-count">' + walletCount + '</span></span>');
	});
	

	jQuery('#doaction').parent().append('<select id="filter_member_status" ><option value="All">Filter By Status</option><option value="All">Show All</option><option value="processing">Processing</option><option value="checkout-draft">Checkout-Draft</option><option value="completed">Completed</option><option value="refunded">Refunded</option><option value="on-hold">On-Hold</option><option value="failed">Failed</option><option value="pending">Pending</option><option value="cancelled">Cancelled</option></select>');
	
	$(document).on('change', '#filter_member_status', function(e) {

		var filtered_status = jQuery('#filter_member_status').val();
		 var member_ststus_td = jQuery('.column-status mark');
		
		 for (let index = 0; index < member_ststus_td.length; index++) {
			 if (filtered_status == jQuery(jQuery('.column-status mark span')[index]).html() || filtered_status == 'All' ) {
				jQuery(jQuery('.column-status mark')[index]).parent().parent().show();
			 } else {
				jQuery(jQuery('.column-status mark')[index]).parent().parent().hide();
			 }
		 }
		
	});

});
	

