var initTable = function (table,columns,chkbox) {
    /*
     * Initialize DataTables, with no sorting on the 'details' column
     */
    var oTable = table.DataTable({
        "columnDefs": [{
            "orderable": false,
            "targets": columns
        }],
        // "order": [
        //     [0, 'asc']
        // ],
        "order": [],
        "lengthMenu": [
            [5, 15, 20, -1],
            [5, 15, 20, "All"] // change per page values here
        ],
        // set the initial value
        "pageLength": 10,
    });

    // Apply the search
    oTable.columns().every(function (index) {
        table.find('thead tr:eq(0) th:eq(' + index + ') input').on('keyup change', function () {
            oTable.column($(this).parent().index() + ':visible').search(this.value).draw();
        });
         table.find('thead tr:eq(0) th:eq(' + index + ') select').on('change', function () {
            var term = '';
            if(this.value == ''){
                term = this.value;
            }else{
                term = "^"+this.value+"$";
            }
            console.log(term);
            oTable.column($(this).parent().index() + ':visible').search(term,true, false).draw();
            
        });
        if(chkbox == 1) {
            oTable.on('order.dt search.dt', function () {
                oTable.column(1, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        }else{
            oTable.on('order.dt search.dt', function () {
                oTable.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        }
    });
}
