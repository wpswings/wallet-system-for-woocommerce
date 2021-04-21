$(document).ready(function() {
    $('.bulkactions').append('<input id="searchFrom" class="searchInput" type="text" placeholder="From"/><input id="searchTo" class="searchInput" type="text" placeholder="To" ><input type="button" id="clear_datefilter" class="button" value="Clear">');
    
    $('.searchInput').datepicker({
        dateFormat: "mm/dd/yy",
        onSelect: function () {
            $(".searchInput").trigger('keyup')
        }
    });

    $(".searchInput").keyup(function() {
        var from = $("#searchFrom").val();
        var to = $("#searchTo").val();
        $(".walletrechargeorders tr").each(function() {
            var row = $(this);
            var date = row.find("td").eq(3).text();
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

        var rowCount = $(".walletrechargeorders tbody tr").length;
        var count = 0;
        $('.walletrechargeorders > tbody  > tr').each(function(index, tr) { 
            var ColumnName = $(tr).attr("style");
            if ( ColumnName == 'display: none;' ) {
                count++;
            }
        });
        rowCount -= count;
        if ( rowCount == 0 || rowCount == 1 ) {
            $('.tablenav .displaying-num').html(rowCount + ' item');
        } else {
            $('.tablenav .displaying-num').html(rowCount + ' items');
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

    $(document).on('click', '#clear_datefilter', function(){
        $("#searchFrom").val('');
        $('#searchTo').val('');
        $(".walletrechargeorders tr").each(function() {
        }
    });

});
	

