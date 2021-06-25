@extends('layouts.company')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
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
  <style>
    .icheckbox_minimal-blue {
      margin-top: -2px;
      margin-right: 3px;
    }

    .checkbox label, .radio label {
      font-weight: bold;
    }

    .has-error {
      color: red;
    }
  </style>
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            {!! Form::open(array('url' => url(domain_route("company.admin.collectionreport.filter", ["domain" => request("subdomain")])), 'method' => 'post')) !!}
            <div class="row">
              <div class="col-sm-3">
                <div class="form-group" style="margin-left: 0px;">
                  {!! Form::label('client_id', 'Party Name') !!}
                  {!! Form::select('client_id', array('' => 'Please select party') + $clients, isset($client_id)?$client_id:null,  ['class' => 'form-control']) !!}
                  @if ($errors->has('client_id')) <p
                      class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
                </div>
              </div>

              <div class="col-sm-3">
                {!! Form::label('employee_id', 'Employee Name') !!}
                {!! Form::select('employee_id', array('' => 'Please select employee') +  $employees, isset($employee_id)?$employee_id:null,  ['class' => 'form-control']) !!}
                @if ($errors->has('employee_id')) <p
                    class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
              </div>
              {{-- <div class="col-sm-2 salesman1">
             <div class="form-group @if ($errors->has('from_date')) has-error @endif">
     {!! Form::label('from_date', 'From:') !!}
     <div class="input-group date">
               <div class="input-group-addon">
                 <i class="fa fa-calendar"></i>
               </div>
               {!! Form::text('from_date', null, ['class' => 'form-control pull-right', 'id' => 'from_date', 'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
             </div>

     @if ($errors->has('from_date')) <p class="help-block has-error">{{ $errors->first('from_date') }}</p> @endif
 </div>
</div> --}}

              {{-- <div class="col-sm-2 salesman1">
                <div class="form-group @if ($errors->has('to_date')) has-error @endif">
      {!! Form::label('to_date', 'To:') !!}
      <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                {!! Form::text('to_date', null, ['class' => 'form-control pull-right', 'id' => 'to_date', 'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
              </div>

      @if ($errors->has('to_date')) <p class="help-block has-error">{{ $errors->first('to_date') }}</p> @endif
  </div>
              </div> --}}

              <div class="col-sm-2">
                <button value="get_report" name="search" type="submit" class="btn btn-info btn"
                        style="margin-top: 25px;">Search
                </button>
              </div>
            </div>
            </form>
          </div>
          <div class="box-body">
            <div id="reportrange" name="reportrange" class="reportrange"
                 style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 28%;position: absolute;margin-left: 50%;z-index: 999;">
              <i class="fa fa-calendar"></i>&nbsp;
              <span></span> <i class="fa fa-caret-down"></i>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="collectiontable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>S.No.</th>
                <th>Party</th>
                <th>Salesman</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Mode</th>
                <th>Notes</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
              @foreach($collections as $collection)
                <tr>
                  <td>1</td>
                  <td>{{ getClient($collection->client_id)['company_name']}}</td>
                  <td>{{ getEmployee($collection->employee_id)['name']}}</td>
                  <td>{{ date('d M Y', strtotime($collection->payment_date))}}</td>
                  <td>{{ $collection->payment_received}}</td>
                  <td>{{ $collection->payment_method}}</td>
                  <td>{{ $collection->payment_note}}</td>
                  <td><a href="{{ domain_route('company.admin.order.showcollection',[$collection->id]) }}"
                         class="btn btn-success btn-sm" style="padding: 3px 6px;"><i class="fa fa-eye"></i></a></td>
                </tr>
              @endforeach
              </tbody>
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

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <script type="text/javascript">

      $(function () {
          $("#from_date").datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
          }).on('changeDate', function (selected) {
              var startDate = new Date(selected.date.valueOf());
              $('#to_date').datepicker('setStartDate', startDate);
          }).on('clearDate', function (selected) {
              $('#to_date').datepicker('setStartDate', null);
          });

          $("#to_date").datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
          }).on('changeDate', function (selected) {
              var endDate = new Date(selected.date.valueOf());
              $('#from_date').datepicker('setEndDate', endDate);
          }).on('clearDate', function (selected) {
              $('#from_date').datepicker('setEndDate', null);
          });

          var table = $('#collectiontable').DataTable({
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Announcement List'
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Announcement List'
                  },
                  {
                      extend: 'print',
                      title: 'Announcement List'
                  },
              ]
          });

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
                  var create_date = Date.parse(data[3]); // use data for the age column
                  if (create_date >= start_date && create_date <= end_date) {
                      return true;
                  }
                  return false;
              }
          );
          // table.buttons().container()
          //   .appendTo( '#announcementexports' );
      });


  </script>

@endsection