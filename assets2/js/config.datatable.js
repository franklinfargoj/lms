var initTable = function (table,columns) {
    /*
     * Initialize DataTables, with no sorting on the 'details' column
     */
    var oTable = table.DataTable({
        "columnDefs": [{
            "orderable": false,
            "targets": columns
        }],
        "order": [
            [0, 'asc']
        ],
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
            oTable.column($(this).parent().index() + ':visible').search(this.value).draw();
        });
    });
}