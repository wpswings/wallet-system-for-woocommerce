
jQuery(document).ready(function(){

    var datatable_pagination_text = wsfw_admin_param.datatable_pagination_text;
	var datatable_info            = wsfw_admin_param.datatable_info	;
    var table1 = jQuery('#wps-wpg-gen-table_trasa').DataTable({

    	"dom": '<"">tr<"bottom"lip>', //extentions position
        "ordering": true, // enable ordering

		language: {
			"lengthMenu": datatable_pagination_text,
			"info": datatable_info, 

			paginate: {
				next: '<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.99984 0L0.589844 1.41L5.16984 6L0.589844 10.59L1.99984 12L7.99984 6L1.99984 0Z" fill="#8E908F"/></svg>',
				previous: '<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.00016 12L7.41016 10.59L2.83016 6L7.41016 1.41L6.00016 -1.23266e-07L0.000156927 6L6.00016 12Z" fill="#8E908F"/></svg>'
			}
		}
	});
    jQuery('#search_in_table').keyup(function(){
        table1.search(jQuery(this).val()).draw() ;
    });
    
    jQuery("#min").datepicker({ onSelect: function () { table1.draw(); }, changeMonth: true, changeYear: true });
    jQuery("#max").datepicker({ onSelect: function () { table1.draw(); }, changeMonth: true, changeYear: true });
    
    jQuery('#min, #max').change(function () {
        table1.draw();
    });

    
});
jQuery.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
        var min = jQuery('#min').datepicker("getDate");
        var max = jQuery('#max').datepicker("getDate");  
        var startDate = new Date(data[9]);
        if (min == null && max == null) { return true; }
        if (min == null && startDate <= max) { return true;}
        if(max == null && startDate >= min) {return true;}
        if (startDate <= max && startDate >= min) { return true; }
        return false;
    }
);

jQuery(document).ready(function(){
    jQuery("#wps-wpg-gen-table_trasa").wrap("<div class='wps_wsfwp_table_wrap'></div>");
});