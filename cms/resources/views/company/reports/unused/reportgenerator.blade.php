@extends('layouts.company')

@section('title', 'Report Generator')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <style>
    .daterangepicker .calendar-table th, .daterangepicker .calendar-table td {
      min-width: 25px !important;
      width: 25px !important;
    }

    .table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th {
      padding: 3px !important;
    }

    .daterangepicker.ltr .drp-calendar.right {
      margin-left: 0;
      border-left: 1px solid #ccc !important;
    }
  </style>
@endsection


@section('content')

  <section class="content">

    <div class="row">

      <div class="col-xs-12">

        @if (\Session::has('success'))

          <div class="alert alert-success">

            <p>{{ \Session::get('success') }}</p>

          </div><br/>

        @endif

        <div class="box">

          <div style="background: #fff; cursor: pointer;border: 1px solid #ccc;position: absolute;z-index: 999;">
            <select id="report-type">
              <option value="orderReportType1">Order Report Type 1</option>
              <option value="orderReportType2">Order Report Type 2</option>
            </select>
          </div>

          <div id="reportrange" name="reportrange"
               style="background: #fff; cursor: pointer;border: 1px solid #ccc; width: 28%;position: absolute;margin-left: 30%;z-index: 999;">
            <i class="fa fa-calendar"></i>&nbsp;
            <span></span> <i class="fa fa-caret-down"></i>
          </div>

          <span id="clientexports" class="pull-right">
            
            <div class="dt-buttons btn-group">
              <button class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="client"
                      type="button"><span>Download Excel</span></button>
            </div>
          </span>

          <!-- /.box-header -->

          <div class="box-body" style="">


            <table id="client" class="table table-bordered table-striped" style="">

              <thead>

              @if( !empty($data) )

                <tr>

                  <th>#</th>

                  <th>Party Name</th>

                  <th>Person Name</th>

                  <th>Phone</th>

                  <th>Mobile</th>

                  <th>Email</th>

                </tr>

              </thead>

              <tbody>

              @php($i = 0)

              @foreach($data as $tempValue)

                @php($i++)

                <tr>

                  <td>{{ $i }}</td>

                  <td>{{ ucfirst(strtolower(getArrayValue($tempValue,'company_name'))) }}</td>

                  <td>{{ ucfirst(strtolower(getArrayValue($tempValue,'name'))) }}</td>

                  <td>{{ getArrayValue($tempValue,'phone')}}</td>

                  <td>{{ getArrayValue($tempValue,'mobile')}}</td>

                  <td>{{ getArrayValue($tempValue,'email')}}</td>
                </tr>

              @endforeach

              </tbody>

              @else

                <tr>
                  {{-- <td colspan="10">No Record Found.</td> --}}
                </tr>

              @endif

            </table>

          </div>

          <!-- /.box-body -->

        </div>

        <!-- /.box -->

      </div>

      <!-- /.col -->

    </div>

    <!-- /.row -->

  </section>

@endsection



@section('scripts')

  <script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.4/xlsx.core.min.js"></script>
  <script src="{{asset('assets/dist/js/filesaver.min.js') }}"></script>
  <script src="{{asset('assets/dist/js/jhxlsx.min.js') }}"></script>

  <script>
      $(function () {

          ajaxUrls = {
              getRepordData: "{{ domain_route('company.admin.reports.getreportdata') }}"
          };


          $(".buttons-excel").click(function () {
              var reportType = $('#report-type').val();
              var start = $('#reportrange').data('daterangepicker').startDate;
              var end = $('#reportrange').data('daterangepicker').endDate;
              var startDate = start.format('YYYY-MM-DD');
              var endDate = end.format('YYYY-MM-DD');
              options = {
                  fileName: reportType + "_" + startDate + "to" + endDate
              };
              $.ajax({
                  type: 'GET',
                  dataType: 'json',
                  url: ajaxUrls.getRepordData,
                  data: {
                      'reportType': reportType,
                      'startDate': startDate,
                      'endDate': endDate
                  },
                  success: function (data) {
                      Jhxlsx.export(data, options);
                  },
                  error: function (e) {
                      console.log(e);
                  }
              });
          });


          var start = moment();
          var end = moment();

          function cb(start, end) {

              $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
              $('#startdate').val(start.format('MMMM D, YYYY'));
              $('#enddate').val(end.format('MMMM D, YYYY'));
          }

          $('#reportrange').daterangepicker({
              startDate: start,
              endDate: end,
              ranges: {
                  'Today': [moment(), moment()],
                  'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                  'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                  'This Month': [moment().startOf('month'), moment().endOf('month')],
                  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              }
          }, cb);

          cb(start, end);
      });
  </script>
@endsection