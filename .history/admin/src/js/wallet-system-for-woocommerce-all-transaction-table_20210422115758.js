jQuery.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
        var min = jQuery('#min').val();
        var max = jQuery('#max').val();   
        var startDate = new Date(data[8]);
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

    console.log(min);

    // jQuery("#min").datepicker({ onSelect: function () { table1.draw(); }, changeMonth: true, changeYear: true });
    // jQuery("#max").datepicker({ onSelect: function () { table1.draw(); }, changeMonth: true, changeYear: true });
    
    // jQuery('#min, #max').change(function () {
    //     table1.draw();
    // });

    $('#min, #max').keyup( function() {
        table.draw();
    } );
    // jQuery('input[type="date"]').change(function(){
    //     alert(this.value);         //Date in full format alert(new Date(this.value));
    //     var inputDate = new Date(this.value);
    // });
});