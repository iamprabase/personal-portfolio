@extends('layouts.company')
@section('title', 'Daily Sales report')
@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
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

    #clientexports .btn {
      padding: 10px 6px !important;
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
        @if (\Session::has('success'))
          <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
          </div><br/>
        @endif
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Custom Report</h3>
            {{-- <a href="{{ domain_route('company.admin.noorders') }}" class="btn btn-warning pull-right" style="margin-left: 5px;">
          <i class="fa fa-plus"></i> Create New
        </a> --}}
            <span id="orderexports" class="pull-right"></span>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            {!! Form::open(array('url' => url(domain_route("company.admin.generatnewreport", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true)) !!}
            <div class="row">
              <div class="col-md-12">
                <div class="col-md-4">
                  <div class="form-group @if ($errors->has('client_id')) has-error @endif">
                    {!! Form::label('client_id', 'Party Name') !!}
                    {!! Form::select('client_id', $clients, null,  ['class' => 'form-control select2']) !!}
                    @if ($errors->has('client_id')) <p
                        class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group @if ($errors->has('employee_id')) has-error @endif">
                    {!! Form::label('employee_id', 'Salesman Name') !!}
                    {!! Form::select('employee_id', $employees, null,  ['class' => 'form-control select2']) !!}
                    @if ($errors->has('employee_id')) <p
                        class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
                  </div>
                </div>
                <div class="col-md-4">
                  {!! Form::label('employee_id', 'Date Range') !!}
                  <div class="input-group">
                    <button type="button" class="btn btn-default pull-right" id="daterange-btn">
                    <span>
                      <i class="fa fa-calendar"></i> Select Date Range
                    </span>
                      <i class="fa fa-caret-down"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="row justify-content-center">
              <div class=" offset-md-4 col-md-4">
                
                {!! Form::submit('Generate', ['class' => 'btn btn-primary pull-right', 'id' => 'create_new_entry']) !!}

              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Custom Report</h3>

          </div>
          <!-- /.box-header -->
          <div class="box-body table-responsive">
            <div class="row">
              <div class="col-sm-2"></div>
              <div class="col-sm-7">
                <div class="row">

                  <div class="select-2-sec">


                  </div>
                </div>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <table id="order" class="table table-bordered table-striped">
              <thead>

              <tr>
                <th>Date Generated</th>
                <th>Download Link</th>

              </tr>
              </thead>

              <tbody>
              <tr>
                <td></td>
                <td></td>

              </tr>
              
              </tbody>
              <tfoot>

              {{--  <tr>
                   <th colspan="6" style="text-align:right">Total:</th>
                   <th colspan="3" style="text-align:left"></th>
               </tr> --}}

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


  <!-- Modal -->

@endsection

@section('scripts')
  <script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
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
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script>
      $(function () {


          $('#delete').on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget);
              var mid = button.data('mid');
              var url = button.data('url');
              // $(".remove-record-model").attr("action",url);
              $(".remove-record-model").attr("action", url);
              var modal = $(this)
              modal.find('.modal-body #m_id').val(mid);
          });


          var table = $('#order').DataTable({
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Order List'
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Order List'
                  },
                  {
                      extend: 'print',
                      title: 'Order List'
                  },
              ],
              order: [[5, "asc"]],
              footerCallback: function (row, data, start, end, display) {
                  var api = this.api(), data;

                  // Remove the formatting to get integer data for summation
                  var intVal = function (i) {
                      return typeof i === 'string' ?
                          i.replace(/[\$,]/g, '') * 1 :
                          typeof i === 'number' ?
                              i : 0;
                  };

              }
          });

          table.buttons().container()
              .appendTo('#orderexports');

          $('#reportrange').bind('DOMSubtreeModified', function (event) {
              table.draw();
          });

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

          $('#daterange-btn').daterangepicker(
              {
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

      $(document).ready(function () {
          var table = $('#order').DataTable();

          // $("#order tfoot th").each( function ( i ) {
          var select = $('<select class="select2" style="background: #fff;width:100% !important; cursor: pointer;position: absolute;z-index: 999;"><option value="">Search By Party</option></select>')
              .appendTo($('#partyfilter').empty())
              .on('change', function () {
                  table.column(0)
                      .search($(this).val())
                      .draw();
              });

          table.column(0).data().unique().sort().each(function (d, j) {
              select.append('<option value="' + d + '">' + d + '</option>')
          });
          // } );

          var select = $('<select class="select2" style="background: #fff;width:100% !important; cursor: pointer;position: absolute;z-index: 999;"><option value=""> Search By Salesman</option></select>')
              .appendTo($('#salesmfilter').empty())
              .on('change', function () {
                  table.column(5)
                      .search($(this).val())
                      .draw();
              });

          table.column(5).data().unique().sort().each(function (d, j) {
              select.append('<option value="' + d + '">' + d + '</option>')
          });


          $('.select2').select2();
      });

      $(document).on('click', '.edit-modal', function () {
          // $('#footer_action_button').text(" Change");
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Change Delivery Status');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#order_id').val($(this).data('id'));
          // $('#remark').val($(this).data('remark'));
          $('#delivery_status').val($(this).data('status'));
          $('#delivery_datenew').val($(this).data('orderdate'));
          $('#delivery_place').val($(this).data('place'));
          $('#delivery_note').val($(this).data('note'));
          $('#myModal').modal('show');
      });

      $(function () {
          $("#delivery_datenew").datepicker({
              format: "yyyy-mm-dd",
              startDate: new Date(),
              autoclose: true,
          });    // Here the current date is set
      });


  </script>

@endsection