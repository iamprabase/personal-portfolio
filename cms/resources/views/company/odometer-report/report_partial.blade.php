<table id="odometerreport" class="table table-bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Date</th>
        @if(config('settings.odometer_distance_unit') == 1)
            <th>KM</th>
        @else
            <th>Mile</th>
        @endif
        <th>Reimbursement</th>
        <th class="noPrint">Action</th>
    </tr>
    </thead>


    <tbody>
    @foreach($data as $key => $item)
        <tr>
            <td>{{$key+1}}</td>
            <td>{!! $item['name'] !!}</td>
            <td>{{$item['date']}}</td>
            @if(config('settings.odometer_distance_unit') == 1)
                <td>{{$item['km']}}</td>
            @else
                <td>{{$item['mile']}}</td>
            @endif
            <td>{{config('settings.currency_symbol')}} {{$item['amount']}}</td>
            <td class="no-print">{!! $item['action'] !!}</td>
        </tr>
    @endforeach
    </tbody>

</table>

<form method="post" action="{{domain_route('company.admin.downloadPdf')}}" class="pdf-export-form hidden"
      id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>
<script>

    var table = $('#odometerreport').DataTable({
        "processing": true,
        "serverSide": false,
        "order": [[2, "desc"]],
        "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
            "<'row'<'col-xs-6'><'col-xs-6'>>" +
            "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
        "buttons": [
            // {
            //     extend: 'colvis',
            //     order: 'alpha',
            //     className: 'dropbtn',
            //     columns: [':visible :not(:last-child)'],
            //     text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
            //     columnText: function (dt, idx, title) {
            //         return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col" + idx + "' class='check' type='checkbox'><label for='col" + idx + "'></label></div></div><div class='col-xs-9 pad-left'>" + title + "</div></div>";
            //     }
            // },
            {
                extend: 'excelHtml5',
                title: "Odometer Report",
                exportOptions: {
                    columns: [':visible :not(:last-child)'],
                },

            },
            {
                extend: 'pdfHtml5',
                title: "Odometer Report",

                orientation: 'landscape',

                action: function (e, dt, node, config) {
                    newExportAction(e, dt, node, config);
                },
            },
            {
                extend: 'print',
                title: function () {
                    return "Odometer Report";
                },
                exportOptions: {
                    columns: [':visible :not(:last-child)'],
                },

            },
        ],
    });
    table.buttons().container().appendTo('#buttonsPlacement');


    var newExportAction = function (e, dt, button, config) {

        var self = this;
        var data = [];
        var count = 0;
        var tableColumns = table.columns()[0].length - 1;
        var title = config['title'];


        table.rows({"search": "applied"}).every(function () {
            var row = {};
            row["id"] = ++count;
            row["name"] = this.data()[1];
            row['date'] = this.data()[2];
            @if(config('settings.odometer_distance_unit') == 1)
                row['km'] = this.data()[3];
            @else
                row['mile'] = this.data()[3];
            @endif
                row['amount'] = this.data()[4];
            data.push(row);
        });
        var columns = [];
        columns.push("Id");
        columns.push("Name")
        columns.push("Date")
        @if(config('settings.odometer_distance_unit') == 1)
        columns.push("Km")
        @else
        columns.push("Mile")
        @endif
        columns.push("Reimbursement")
        var propertiesArray = [];
        propertiesArray.push("id");
        propertiesArray.push("name");
        propertiesArray.push("date");
        @if(config('settings.odometer_distance_unit') == 1)
        propertiesArray.push("km");
        @else
        propertiesArray.push("mile");
        @endif
        propertiesArray.push("amount");
        var exportDat = {};
        exportDat['data'] = data;
        customExportAction(title, exportDat, 'OdometerReport', columns, propertiesArray);
    };

    function customExportAction(title, exportData, modName, cols, propArray) {
        $('#exportedData').val(JSON.stringify(exportData));
        $('#pageTitle').val(title);
        $('#reportName').val(modName);
        $('#columns').val(JSON.stringify(cols));
        var properties = JSON.stringify(propArray);
        $('#properties').val(properties);
        $('#pdf-generate').submit();
    }

</script>
