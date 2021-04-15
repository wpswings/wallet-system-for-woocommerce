jQuery(document).ready(function() {
	jQuery('#mwb-wpg-gen-table').DataTable({

    	"dom": '<"">tr<"bottom"lip>', //extentions position
        "ordering": true, // enable ordering

language: {
	"lengthMenu": "Rows per page _MENU_",
	"info": "_START_ - _END_ of _TOTAL_",

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
		language:{lengthMenu:"Rows per page _MENU_",info:"_START_ - _END_ of _TOTAL_",
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

	 

	jQuery.fn.dataTable.ext.search.push(
		function (settings, data, dataIndex) {
			var min = jQuery('#min').datepicker("getDate");
			var max = jQuery('#max').datepicker("getDate");   
			var startDate = new Date(data[8]);
			if (min == null && max == null) { return true; }
			if (min == null && startDate <= max) { return true;}
			if(max == null && startDate >= min) {return true;}
			if (startDate <= max && startDate >= min) { return true; }
			return false;
		}
	);

	var table = jQuery('#mwb-wpg-gen-table').DataTable();   //pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
	
	jQuery("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
	jQuery("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
	
	jQuery('#min, #max').change(function () {
		table.draw();
	});
	 

   	jQuery('#search_in_table').keyup(function(){
        table.search(jQuery(this).val()).draw() ;
    });

    jQuery('#filter_status').change(function () {
		//var status = jQuery(this).find(":selected").text();
		table.columns(3).search(jQuery(this).val()).draw() ;
    });

	// custom order js
	jQuery('.bulkactions').append('<input id="searchFrom" class="searchInput" type="text" placeholder="From d/m/y"/><input id="searchTo" class="searchInput" type="text" placeholder="To" >');
	jQuery(".searchInput").on("input", function() {
		var from = stringToDate(jQuery("#searchFrom").val());
		var to = stringToDate(jQuery("#searchTo").val());

		jQuery(".walletrechargeorders tr").each(function() {
			var row = jQuery(this);
			var date = stringToDate(row.find("td").eq(3).text());
			
			//show all rows by default
			var show = true;

			//if from date is valid and row date is less than from date, hide the row
			if (from && date < from)
			show = false;
			
			//if to date is valid and row date is greater than to date, hide the row
			if (to && date > to)
			show = false;

			if (show)
			row.show();
			else
			row.hide();
		});

		var rowCount = jQuery(".walletrechargeorders tbody tr").length;
		var count = 0;
		jQuery('.walletrechargeorders > tbody  > tr').each(function(index, tr) { 
			var ColumnName = jQuery(tr).attr("style");
			if ( ColumnName == 'display: none;' ) {
				count++;
			}
		});
		rowCount -= count;
		if ( rowCount == 0 || rowCount == 1 ) {
			jQuery('.tablenav .displaying-num').html(rowCount + ' item');
		} else {
			jQuery('.tablenav .displaying-num').html(rowCount + ' items');
		}
	});

	//parse entered date. return NaN if invalid
	function stringToDate(s) {
		var ret = NaN;
		var parts = s.split("/");
		date = new Date(parts[2], parts[0], parts[1]);
		if (!isNaN(date.getTime())) {
			ret = date;
		}
		return ret;
    }

});


