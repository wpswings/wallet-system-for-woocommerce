jQuery(document).ready(function() {
	var datatable_pagination_text = wsfw_admin_param.datatable_pagination_text;
	var datatable_info            = wsfw_admin_param.datatable_info	;
	jQuery('#mwb-wpg-gen-table').DataTable({

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

	jQuery(document).on( 'click', '#update_wallet', function() {
	//jQuery('#update_wallet').click(function(){
		jQuery('.mwb_wallet-update--popupwrap').addClass('active');
	});

	var table = jQuery('#mwb-wpg-gen-table1').DataTable({
      	order: [],
		dom:'<"">tr<"bottom"lip>',
		ordering:!0,
		language:{lengthMenu:datatable_pagination_text,info:datatable_info,
		paginate:{next:'<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.99984 0L0.589844 1.41L5.16984 6L0.589844 10.59L1.99984 12L7.99984 6L1.99984 0Z" fill="#8E908F"/></svg>',previous:'<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.00016 12L7.41016 10.59L2.83016 6L7.41016 1.41L6.00016 -1.23266e-07L0.000156927 6L6.00016 12Z" fill="#8E908F"/></svg>'}},
      	columnDefs: [
			{ 
				"visible": false, "targets": [3]},
				{render: function(data, type, full, meta){
					if(type === 'filter' || type === 'sort'){
						var api = new jQuery.fn.dataTable.Api(meta.settings);
						var td = api.cell({row: meta.row, column: meta.col}).node();
						var input = jQuery('select, input', td);
						if(input.length && input.is('select')){
						data = jQuery('option:selected', input).text();
						} else {                   
						data = input.val();
						}
					}

					return data;
				}
			}
      	]  

   	});
	 

   	jQuery('#search_in_table').keyup(function(){
        table.search(jQuery(this).val()).draw() ;
    });

    jQuery('#filter_status').change(function () {
		//var status = jQuery(this).find(":selected").text();
		table.columns(3).search(jQuery(this).val()).draw() ;
    });

});


