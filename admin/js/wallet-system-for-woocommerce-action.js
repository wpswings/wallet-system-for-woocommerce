  jQuery(document).ready(function() {
    debugger;
    jQuery('.wps-wsfw-number').append('<input type="hidden" id="user_check_box_ids" name="user_check_box_ids" value="" />')
    jQuery( "#wps_sfw_subscription_interval" ).change(function() {
       
       var wps_sfw_subscription_interval = jQuery( "#wps_sfw_subscription_interval" ).val();        
        jQuery('#wps_sfw_subscription_expiry_interval').val(wps_sfw_subscription_interval).attr("selected", "selected");
      });


});

function set_checked_value(obj){
debugger;

var existing_array = jQuery('#user_check_box_ids').val();

if ( existing_array == '' && jQuery(obj).prop('checked') == true ) {
  jQuery('#user_check_box_ids').val(jQuery(obj).val()+',');
} else {
  var new_item = jQuery(obj).val();

  if (jQuery(obj).prop('checked') == true ) {
    jQuery('#user_check_box_ids').val(existing_array+new_item+',');
  }

  if (jQuery(obj).prop('checked') == false ) {
if ( existing_array != '' ) {
  var array_list = existing_array.split(',');
  var new_assigned_array=[];
  for (let index = 0; index < array_list.length; index++) {
    if (new_item == array_list[index]){
     
    }else{
      new_assigned_array.push(array_list[index]);
    }
    
  }
  jQuery('#user_check_box_ids').val(new_assigned_array);


}

  }

}


}