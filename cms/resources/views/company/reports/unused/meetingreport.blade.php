@extends('layouts.company')

@section('stylesheets')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
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
            <!-- <h3 class="box-title">Data Table With Full Features</h3> -->
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            {!! Form::open(array('url' => url(domain_route("company.admin.meetingreport.filter", ["domain" => request("subdomain")])), 'method' => 'post')) !!}

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

              <div class="col-sm-2 salesman1">
                <div class="form-group @if ($errors->has('from_date')) has-error @endif">
                  {!! Form::label('from_date', 'From:') !!}
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('from_date', null, ['class' => 'form-control pull-right', 'id' => 'from_date', 'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
                  </div>

                  @if ($errors->has('from_date')) <p
                      class="help-block has-error">{{ $errors->first('from_date') }}</p> @endif
                </div>
              </div>

              <div class="col-sm-2 salesman1">
                <div class="form-group @if ($errors->has('to_date')) has-error @endif">
                  {!! Form::label('to_date', 'To:') !!}
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('to_date', null, ['class' => 'form-control pull-right', 'id' => 'to_date', 'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
                  </div>

                  @if ($errors->has('to_date')) <p
                      class="help-block has-error">{{ $errors->first('to_date') }}</p> @endif
                </div>
              </div>
              <div class="col-sm-2">
                <button value="get_report" name="search" type="submit" class="btn btn-info btn"
                        style="margin-top: 25px;">Search
                </button>
              </div>
            </div>

            </form>

            <br>
            <br>
            <table id="example1" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>S. No.</th>
                <th>Employee Name</th>
                <th>Party Name</th>
                <th>CheckIn Time</th>
                <th>Meeting Date</th>
                <th>GPS Location</th>
                <th>Remark</th>
              </tr>
              </thead>
              <tbody>
              @foreach($meetings as $meeting)
                <tr>
                  <td>1</td>
                  <td>{{ getEmployee($meeting->employee_id)['name']}} </td>
                  <td>{{ getClient($meeting->client_id)['name']}}  </td>
                  <td>{{$meeting->checkintime}} </td>
                  <td> {{$meeting->meetingdate}} </td>
                  <td>
                    <button value="211" type="button" class="btn btn-info btn-md view" data-toggle="modal"
                            data-target="#modal-default">Map
                    </button>
                  </td>
                  <td>
                    <a data-toggle="modal" data-target="#modal-default1" class="btn btn-info"><i class="fa fa-eye"></i></a>
                  
                  </td>
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


      });

      $(document).ready(function () {

          $(document).on('change', '.employee_id', function () {

              employeeTotalOrders();
              //        var disctype=$(this).val();
              // alert(disctype);
              // alert('hi');
          });


      });


      function employeeTotalOrders() {

          var start = $('input#from_date').data('datepicker').startDate.format('YYYY-MM-DD');
          var end = $('input#to_date').data('datepicker').endDate.format('YYYY-MM-DD');

          var data_expense = {
              employee_id: $('select#employee_id').val(),
              start_date: start,
              end_date: end
          }

          $('span#sr_total_sales').html(__fa_awesome());

          $.ajax({
              method: "GET",
              url: '/reports/sales-representative-total-sell',
              dataType: "json",
              data: data_expense,
              success: function (data) {
                  $('span#sr_total_sales').html(__currency_trans_from_en(data.total_sell_exc_tax, true));
              }
          });
      }

      function employeeTotalCollections() {

          var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

          var data_expense = {
              created_by: $('select#sr_id').val(),
              location_id: $('select#sr_business_id').val(),
              start_date: start,
              end_date: end
          }

          $('span#sr_total_sales').html(__fa_awesome());

          $.ajax({
              method: "GET",
              url: '/reports/sales-representative-total-sell',
              dataType: "json",
              data: data_expense,
              success: function (data) {
                  $('span#sr_total_sales').html(__currency_trans_from_en(data.total_sell_exc_tax, true));
              }
          });
      }

      function employeeTotalMeetings() {

          var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

          var data_expense = {
              created_by: $('select#sr_id').val(),
              location_id: $('select#sr_business_id').val(),
              start_date: start,
              end_date: end
          }

          $('span#sr_total_sales').html(__fa_awesome());

          $.ajax({
              method: "GET",
              url: '/reports/sales-representative-total-sell',
              dataType: "json",
              data: data_expense,
              success: function (data) {
                  $('span#sr_total_sales').html(__currency_trans_from_en(data.total_sell_exc_tax, true));
              }
          });
      }

  </script>

@endsection