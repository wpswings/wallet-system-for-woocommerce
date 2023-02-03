
jQuery(document).ready(function(){
  

    if (wps_wallet_admin_order_param.is_refundable) {
        jQuery('.refund-actions .do-manual-refund').before('<button type="button" class="button button-primary do-wallet-refund">' + wps_wallet_admin_order_param.i18n.refund + ' <span class="wps-wc-order-refund-amount">' + wps_wallet_admin_order_param.default_price + '</span> ' + wps_wallet_admin_order_param.i18n.via_wallet + '</button>');
    }

    jQuery(document).on( 'click', '.refund-partial-payment', function() {
        if (window.confirm(woocommerce_admin_meta_boxes.i18n_do_refund)) {
            
            var data = {
                action: 'wps_wallet_refund_partial_payment',
                order_id: woocommerce_admin_meta_boxes.post_id
            };
            jQuery.post(woocommerce_admin_meta_boxes.ajax_url, data, function (response) {
                 
                if (true === response.success) {
                    // Redirect to same page for show the refunded status
                    window.location.href = window.location.href;
                }
            });
        }
    });



    jQuery('#woocommerce-order-items').on( 'change', '#refund_amount', function() {

    $refund_amount_val = jQuery('#refund_amount').val();
        jQuery('.do-wallet-refund').html(wps_wallet_admin_order_param.i18n.refund + ' <span class="wps-wc-order-refund-amount"> <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol"> ' + wps_wallet_admin_order_param.currency_symbol + '</span>' + $refund_amount_val + ' </bdi></span></span> ' + wps_wallet_admin_order_param.i18n.via_wallet );
    });

    jQuery('#woocommerce-order-items').on( 'click', '.refund-actions .do-wallet-refund', function() {
		
        if (window.confirm(woocommerce_admin_meta_boxes.i18n_do_refund)) {
        var refund_amount = jQuery('input#refund_amount').val();
        var refund_reason = jQuery('input#refund_reason').val();
          //Get line item refunds
                var line_item_qtys = {};
                var line_item_totals = {};
                var line_item_tax_totals = {};
                jQuery('.refund input.refund_order_item_qty').each(function (index, item) {
                    if (jQuery(item).closest('tr').data('order_item_id')) {
                        if (item.value) {
                            line_item_qtys[ jQuery(item).closest('tr').data('order_item_id') ] = item.value;
                        }
                    }
                });

                jQuery('.refund input.refund_line_total').each(function (index, item) {
                    if (jQuery(item).closest('tr').data('order_item_id')) {
                        line_item_totals[ jQuery(item).closest('tr').data('order_item_id') ] = accounting.unformat(item.value, woocommerce_admin.mon_decimal_point);
                    }
                });

                jQuery('.refund input.refund_line_tax').each(function (index, item) {
                    if (jQuery(item).closest('tr').data('order_item_id')) {
                        var tax_id = jQuery(item).data('tax_id');

                        if (!line_item_tax_totals[ jQuery(item).closest('tr').data('order_item_id') ]) {
                            line_item_tax_totals[ jQuery(item).closest('tr').data('order_item_id') ] = {};
                        }

                        line_item_tax_totals[ jQuery(item).closest('tr').data('order_item_id') ][ tax_id ] = accounting.unformat(item.value, woocommerce_admin.mon_decimal_point);
                    }
                });
                var data = {
                                action: 'wps_wallet_order_refund_action',
                                order_id: woocommerce_admin_meta_boxes.post_id,
                                refund_amount: refund_amount,
                                refund_reason: refund_reason,
                                line_item_qtys: JSON.stringify(line_item_qtys, null, ''),
                                line_item_totals: JSON.stringify(line_item_totals, null, ''),
                                line_item_tax_totals: JSON.stringify(line_item_tax_totals, null, ''),
                                api_refund: jQuery(this).is('.do-api-refund'),
                                restock_refunded_items: jQuery('#restock_refunded_items:checked').length ? 'true' : 'false',
                                security: woocommerce_admin_meta_boxes.order_item_nonce
                            };
	          

    jQuery.post(woocommerce_admin_meta_boxes.ajax_url, data, function (response) {
        if (true === response.success) {
           // wpss_wallet_order_items.reload_items();

            if ('fully_refunded' === response.data.status) {
                // Redirect to same page for show the refunded status
                window.location.href = window.location.href;
            }
            window.location.href = window.location.href;
        } else {
            window.alert(response.data.error);
     
        }
    });
} else {
  
}
});


});