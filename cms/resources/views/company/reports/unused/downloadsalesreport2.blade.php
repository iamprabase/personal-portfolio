@extends('layouts.company')
@section('title', 'Sales Reports')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
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

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 22px;
    }

    button, input, select, textarea {
      height: 26px;
    }

    .select-2-sec {
      margin-top: -10px;
      position: absolute;
      z-index: 99;
    }

    .select2-container .select2-selection--single {
      height: 40px;
      padding: 12px 5px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
      margin-top: 3px;
    }
  </style>

@endsection


@section('content')

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title"> Sales Report </h3>

          </div>
          <!-- /.box-header -->
          <div class="box-body tablediv">
            <div class="container-fluid" style="width:auto;">
              <div class="col-md-12">
                <form action="{{domain_route('company.admin.downloadvssalesreports')}}" method="post">
                  @csrf
                  <div class="row">
                    <div class="col-sm-4">
                      <select id="search_type" name="search_type" class="select2"
                              style="background: #fff; cursor: pointer; width:100%;padding: 5px 0px; border: 1px solid #ccc;position: relative;"
                              required>
                        <option value="">Search Report Type</option>
                        <option value="1"> Party vs Product(Individual)</option>
                        <option value="2"> Salesman vs Product(Individual)</option>
                        <option value="3"> Party vs Product(Combined)</option>
                        <option value="4"> Salesman vs Product(Combined)</option>
                      </select>
                    </div>
                    <div class="col-sm-4">
                      <select id="sales_party_select" name="sales_party_select"
                              style="background: #fff; cursor: pointer; width:100%;padding: 5px 5px; border: 1px solid #ccc;position: relative;"
                              class="select2">
                        <option value="">Search For...</option>
                      </select>
                    </div>
                    <div class="col-sm-4">
                  <span id="reportrange" name="reportrange"
                        style="background: #fff; cursor: pointer; padding: 5px 0px; border: 1px solid #ccc; width: 100%;position: absolute;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span><i class="fa fa-caret-down"></i>
                  </span>
                    </div>
                    <input type="hidden" name="start_date" id="datestart">
                    <input type="hidden" name="end_date" id="dateend">
                    <input type="hidden" name="report_type" id="report_type" value="vs_sales_report">
                  </div>
                  <div class="row">
                    <div class="mx-auto" style="width:100%;padding:20px 0 35px 0;">
                      
                      <button type="submit" class="btn btn-default" id="getReports"
                              style="background: #fff; cursor: pointer; padding: 5px 0px; border: 1px solid #ccc; width: 20%;position: absolute;margin-left: 32%;">
                        <i class="fa fa-book"></i> Get Report
                      </button>
                    </div>
                  </div>
                </form>

              </div>
              <div class="row tablediv table-responsive">

              </div>
            </div>
            <!-- <div class="container-fluid" style="width:auto;"> -->
            <!-- </div> -->
          </div>


        </div>
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Download Reports</h3>

          </div>
          <!-- /.box-header -->
          <div class="box-body table-responsive">
            <div class="row">
              <div class="col-sm-2"></div>
              <div class="col-sm-7">
                <div class="row">

                  <div class="select-2-sec">
                    <div class="col-sm-3">
                      <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="partyfilter"></div>
                    </div>
                    <div class="col-sm-3">
                      <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="salesmfilter"></div>
                    </div>

                  </div>
                </div>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <table id="order" class="table table-bordered table-striped">
              <thead>

              <tr>
                <th>Date Generated</th>
                <th>Salesman / Party Vs Products</th>
                <th>Date Range</th>

              </tr>
              </thead>

              <tbody>
              @foreach ($reports_generated as $reports)
                <tr>
                  <td>{{ $reports->created_at->format('Y-m-d')}}</td>
                  @if($reports->party_name != NULL)
                    <td><b>Party :- &nbsp;</b> {{ strtoupper($reports->party_name) }}</td>
                  @else
                    <td><b>Salesman :- &nbsp;</b>{{ strtoupper($reports->employee_name) }}</td>
                  @endif
                  <td>
                    <a href="{{ $reports->download_link}}">
                      <i class="fa fa-download" aria-hidden="true"></i>
                    </a>
                    <span>&nbsp;{{ $reports->date_range}} </span>
                  </td>
                </tr>
              @endforeach

              </tbody>
              <tfoot>

            </table>
          </div>
          <!-- /.box-body -->
        </div>

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
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>

  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
  <script type="text/javascript">
      $('.select2').select2();

      let table = $('#order').DataTable({
          'searching': false
      });


      $('document').ready(function () {
          var start = moment().subtract(29, 'days');
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

          $.fn.dataTable.ext.search.push(
              function (settings, data, dataIndex) {
                  var start2 = $('#reportrange').data('daterangepicker').startDate;
                  var end2 = $('#reportrange').data('daterangepicker').endDate;
                  var start_date = Date.parse(start2.format('MMMM D, YYYY'));
                  var end_date = Date.parse(end2.format('MMMM D, YYYY'));
                  var create_date = Date.parse(data[6]); // use data for the age column
                  if (create_date >= start_date && create_date <= end_date) {
                      return true;
                  }
                  return false;
              }
          );
      });

      $('select[id="search_type"]').on('change', function (event) {

          var vs_search_ID = $('select[id="search_type"] :selected').val();
          if (vs_search_ID) {
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{ domain_route('company.admin.fetchedrecords')}}",
                  type: "GET",
                  data:
                      {
                          search_type: vs_search_ID,
                      },
                  beforeSend: function (data, id) {
                  },
                  success: function (data) {
                      data = JSON.parse(data);
                      $('select[id="sales_party_select"]').empty();
                      $('select[id="sales_party_select"]').append('<option value="">Search For... </option>');
                      $.each(data, function (key, value) {
                          $('select[id="sales_party_select"]').append('<option value="' + value + '">' + key + '</option>');
                      });
                  }
              });
          } else {
              $('select[name="sales_party_select"]').empty();
          }

          if (vs_search_ID == 3 || vs_search_ID == 4) {
              $('#sales_party_select').attr('disabled', true);
          } else {
              $('#sales_party_select').attr('disabled', false);
          }
      });

      $('document').ready(function () {
          var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          $("#datestart").val(start_date);
          $("#dateend").val(end_date);
      });

      $('#reportrange').change(function () {
          var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          $("#datestart").val(start_date);
          $("#dateend").val(end_date);
      });

      $('#getReports').on('click', function (event) {
          event.preventDefault();
          if ($('#search_type').val() == "") {
              $('#search_type').focus();
              alert('Please select a report type.');
              event.preventDefault();
          }
          var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          var sales_party_select = $('#sales_party_select').val();
          var report_type = $('#report_type').val();
          var search_type = $('#search_type').val();
          if (search_type != "") {
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                  },

                  type: "POST",
                  url: "{{ domain_route('company.admin.downloadvssalesreports') }}",
                  data:
                      {
                          start_date: start_date,
                          end_date: end_date,
                          sales_party_select: sales_party_select,
                          report_type: report_type,
                          search_type: search_type,
                      },
                  beforeSend: function (url, data) {
                      $('#getReports').text('Please wait ...');
                      $('#getReports').attr('disabled', true);
                  },
                  success: function (data) {
                      location.reload();
                      alert(data.msg);
                  },
                  error: function (jqXHR, textStatus, errorThrown, data) {

                      alert(data.msg);
                      alert(jqXHR.message);
                      alert("AJAX error: " + textStatus + ' : ' + errorThrown);
                  },
                  complete: function () {
                      $('#getReports').attr('disabled', false);
                      $('#getReports').html("<i class='fa fa-book'></i> Get Report");
                  }
              });

          }
      })

  </script>

  <script>

  </script>
@endsection
