<script>
 function searchColumsDataTable(datatable) {
    datatable.api().columns([1, 4, 5]).every(function() {
        var column = this;
        var input = document.createElement("input");
        input.setAttribute('class', 'form-control');
        if (column.selector.cols == 5 ) {
            input.setAttribute('type', 'date');
        } else if (column.selector.cols == 4) {
            input = document.createElement("select");
            createSelectColumnUniqueDatatableAll(input, @json($status));
        }

        input.setAttribute('placeholder', 'Nhập từ khóa');
        $(input).appendTo($(column.footer()).empty())
            .on('change', function() {
                column.search($(this).val(), false, false, true).draw();
            });
    });
}

    $(document).ready(function() {
        // define columns for the datatables
        columns = window.LaravelDataTables["NotifyTable"].columns();
        toggleColumnsDatatable(columns);
    });
</script>


{{-- NotifyTable --}}
