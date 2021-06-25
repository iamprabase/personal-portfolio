<table id="dailyempsheet" class="table table-bordered" >
  <thead>
  <tr>
    <th>Employee Name</th>
    @php

      $i=0;

    @endphp

    @foreach($dates as $r)

      @php

        $i++;

      @endphp
      @if(config('settings.ncal')==0)
        <th>{{ $i }}</th>
      @else
        <th>{{ $i }}</th>
      @endif

    @endforeach

    <th>Present Days</th>

    <th>Leave Days</th>

  </tr>

  </thead>

  <tbody>
@if($employees_only)
  @forelse($employees_only as $employee)

  {{-- @php dump($employee) @endphp --}}

    <tr>

      <td>{{ $employee->name }}</td>

      @foreach($dates as $r)

        @if($employee->$r == "W")

          <td style="color:blue;">W</td>

        @elseif($employee->$r == "H")

          <td style="color:blue;">H</td>

        @elseif($employee->$r == "P" || $employee->$r == "W(P)")

            <td style="color:green;">{{$employee->$r}}</td>

        @elseif($employee->$r == "H(P)")

            <td style="color:green;">{{$employee->$r}}</td>

        @elseif($employee->$r == "A" || $employee->$r == "L")

          <td style="color:red;">{{$employee->$r}}</td>

        @else

          <td style="color:gray;">{{$employee->$r}}</td>

        @endif

      @endforeach

      <td>{{ $employee->Present_Days }}</td>

      <td>{{ $employee->Absent_Days }}</td>

    </tr>

  @empty

  @endforelse

  @endif

  </tbody>

</table>

<div class='pull-left' style="margin-bottom:15px;">

  <span><b>*P</b> = Present,</span>

  <span><b>*A</b> = Absent,</span>

  <span><b>*L</b> = Approved Leave,</span>

  <span><b>*H</b> = Scheduled Holiday,</span>

  <span><b>*H(P)</b> = Present on Holiday,</span>

  <span><b>*W</b>= Week Off,</span>

  <span><b>*W(P)</b>= Present on Week Off</span>

</div>
<form method="post" action="{{domain_route('company.admin.reports.customPdfExport')}}" class="pdf-export-form"
  id="pdf-generate" style="display:none;">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="reportName" class="reportName" id="reportName">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>
<script>

    var table = $('#dailyempsheet').DataTable({

        "ordering": false,

         "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
              "<'row'<'col-xs-6'><'col-xs-6'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 

        buttons: [

            {

                extend: 'excelHtml5',

                title: function () {
                  @if(config('settings.ncal')==0)

                    var getYearVal = $("#datepicker").val();
                  
                  @else

                    var getYearVal = $('#nepDate').val();

                  @endif

                    var setTitle = "Monthly Attendance for " + getYearVal;

                    return setTitle;

                },

            },

            {

                extend: 'pdfHtml5',

                title: function () {

                  @if(config('settings.ncal')==0)

                    var getYearVal = $("#datepicker").val();

                  @else

                    var getYearVal = $('#nepDate').val();

                  @endif

                  var setTitle = "Monthly Attendance for " + getYearVal;
                  
                  return setTitle;

                },

                orientation: 'landscape',

                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                },

            },

            {

                extend: 'print',

                title: function () {

                  @if(config('settings.ncal')==0)

                    var getYearVal = $("#datepicker").val();

                  @else

                    var getYearVal = $('#nepDate').val();

                  @endif

                  var setTitle = "Monthly Attendance for " + getYearVal;

                    return setTitle;

                },

                orientation: 'landscape'

            },



        ],

        "searching": true,



    });

    table.buttons().container().appendTo('#dailyempsheet_filter');
      
    var newExportAction = function (e, dt, button, config) {
      var self = this;
      var data = [];
      var count = 0;
      var tableColumns = table.columns()[0].length-3;
      var title = config.title();
      table.rows({"search":"applied" }).every( function () {
        var row = {};
        row["id"] = ++count;
        row["name"] = this.data()[0];
        for(len=1;len<=tableColumns; len++ ){
          row[len] = this.data()[len];
        }
        row["present_days"] = this.data().slice('-1')[0];
        row["leave_days"] = this.data().pop();
        data.push(row);
      });
      var columns = [];
      columns.push("Name");
      var propertiesArray = [];
      propertiesArray.push("id");
      propertiesArray.push("name");
      for(len=1;len<=tableColumns; len++ ){
        columns.push(len);
        propertiesArray.push(len);
      }
      columns.push("Present Days", "Leave Days");
      propertiesArray.push("present_days", "leave_days");
      var exportDat = {};
      exportDat['data']=  data;
      customExportAction(title, exportDat, 'monthlyattendancereport', columns, propertiesArray);
    };

    function customExportAction(title, exportData, modName, cols, propArray){
      $('#exportedData').val(JSON.stringify(exportData));
      $('#pageTitle').val(title);
      $('#reportName').val(modName);
      $('#columns').val(JSON.stringify(cols));
      var properties = JSON.stringify(propArray);
      $('#properties').val(properties);
      $('#pdf-generate').submit();
    }

</script>

