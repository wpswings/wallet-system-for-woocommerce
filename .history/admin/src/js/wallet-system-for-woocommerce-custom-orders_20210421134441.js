jQuery(document).ready(function() {
    jQuery('.bulkactions').append('<input id="searchFrom" class="searchInput" type="text" placeholder="From"/><input id="searchTo" class="searchInput" type="text" placeholder="To" ><input type="button" id="clear_datefilter" class="button" value="Clear">');
    
    jQuery('.searchInput').datepicker({
        dateFormat: "mm/dd/yy",
        changeMonth: true,
        changeYear: true,
        onSelect: function () {
            jQuery(".searchInput").trigger('keyup')
        }
    });

    jQuery(".searchInput").keyup(function() {
        var from = jQuery("#searchFrom").val();
        var to = jQuery("#searchTo").val();
        jQuery(".walletrechargeorders tbody tr").each(function() {
            var row = jQuery(this);
            var date = row.find("td").eq(3).text();
            var show = true;

            if (from && date < from)
            show = false;
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

    jQuery(document).on('click', '#clear_datefilter', function(){
        jQuery("#searchFrom").val('');
        jQuery('#searchTo').val('');
        var count = 0;
        jQuery(".walletrechargeorders tbody tr").each(function() {
            var row = jQuery(this).attr('style');
            if ( row == 'display: none;' ) {
                jQuery(this).removeAttr('style');
            }
            count++;
        });
        if ( count == 0 || count == 1 ) {
            jQuery('.tablenav .displaying-num').html(count + ' item');
        } else {
            jQuery('.tablenav .displaying-num').html(count + ' items');
        }
    });

});
	

