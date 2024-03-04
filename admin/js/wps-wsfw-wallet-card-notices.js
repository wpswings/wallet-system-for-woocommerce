/**
 * All of the code for notices on your admin-facing JavaScript source
 * should reside in this file.
 *
 * @package           woo-gift-cards-lite
 */
jQuery( document ).ready(
    function($){
        $( document ).on(
            'click',
            '#dismiss-banner',
            function(e){
                e.preventDefault();
                var data = {
                    action:'wps_wsfw_dismiss_notice_banner',
                    wps_nonce:wps_wsfw_branner_notice.wps_wsfw_nonce
                };
                $.ajax(
                    {
                        url: wps_wsfw_branner_notice.ajaxurl,
                        type: "POST",
                        data: data,
                        success: function(response)
                        {
                            window.location.reload();
                        }
                    }
                );
            }
        );
        if (jQuery('.wps_wallet_shop_order-header-container').html() != undefined){
            jQuery( '<div class="wps_wallet_shop_order-header-container wps_wallet_shop_order-bg-white wps_wallet_shop_order-r-8">'+jQuery('.wps_wallet_shop_order-header-container').html()+'</div>' ).insertBefore( "#wpbody-content" );
    
        }
    }

   



);