$(document).ready(function() {
    $('.bulkactions').append('<input id="searchFrom" class="searchInput" type="text" placeholder="From d/m/y"/><input id="searchTo" class="searchInput" type="text" placeholder="To" >');
    
    
    $('.searchInput').datepicker({
        dateFormat: "mm/dd/yy",
        onSelect: function () {
            $("#searchInput").trigger('keyup')
        }
    });

    $(".searchInput").on("keyup ", function() {
        // var from = stringToDate($("#searchFrom").val());
        // var to = stringToDate($("#searchTo").val());

        $(".walletrechargeorders tr").each(function() {
            var row = $(this);
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

});
	

