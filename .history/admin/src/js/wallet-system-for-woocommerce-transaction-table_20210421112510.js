jQuery.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
        var min = jQuery('#min').datepicker("getDate");
        var max = jQuery('#max').datepicker("getDate");   
        var startDate = new Date(data[5]);
        if (min == null && max == null) { return true; }
        if (min == null && startDate <= max) { return true;}
        if(max == null && startDate >= min) {return true;}
        if (startDate <= max && startDate >= min) { return true; }
        return false;
    }
);
jQuery(document).ready(function(){
    var table1 = jQuery('#mwb-wpg-gen-table').DataTable();   //pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
    jQuery('#search_in_table').keyup(function(){
        table1.search(jQuery(this).val()).draw() ;
    });
    jQuery("#min").datepicker({ onSelect: function () { table1.draw(); }, changeMonth: true, changeYear: true });
    jQuery("#max").datepicker({ onSelect: function () { table1.draw(); }, changeMonth: true, changeYear: true });
    
    jQuery('#min, #max').change(function () {
        table1.draw();
    });

    var table2 = jQuery('.mwb-wpg-gen-table-all-transaction').DataTable();   //pay attention to capital D, which is mandatory to retrieve "api" datatables' object, as @Lionel said
    jQuery('#search_in_table').keyup(function(){
        table2.search(jQuery(this).val()).draw() ;
    });
    jQuery("#min").datepicker({ onSelect: function () { table2.draw(); }, changeMonth: true, changeYear: true });
    jQuery("#max").datepicker({ onSelect: function () { table2.draw(); }, changeMonth: true, changeYear: true });
    
    jQuery('#min, #max').change(function () {
        table2.draw();
    });

});