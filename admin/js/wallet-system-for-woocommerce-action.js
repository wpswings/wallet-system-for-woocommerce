  jQuery(document).ready(function() {
   
   jQuery( "#wps_sfw_subscription_interval" ).change(function() {
       
       var wps_sfw_subscription_interval = jQuery( "#wps_sfw_subscription_interval" ).val();        
        jQuery('#wps_sfw_subscription_expiry_interval').val(wps_sfw_subscription_interval).attr("selected", "selected");
      });


});

