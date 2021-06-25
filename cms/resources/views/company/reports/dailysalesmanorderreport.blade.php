@extends('layouts.company')
@section('title', 'Daily Salesman Order Report')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
{{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/> --}}
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
{{-- <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}"> --}}
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<style>
  .mt-10{
    margin-top: 15px;
  }

  .ms-options-wrap.ms-has-selections>button{
    outline: none!important;
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
      </div><br />
      @endif
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Daily Salesman Order Report</h3>

          <span id="orderexports" class="pull-right"></span>
        </div>

        <div class="box-header">
          <div class="row">
            <div class="col-xs-3 mt-10">
              <div style="margin-top:10px;height: 40px;z-index: 999 ">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </div>
                  {!! Form::select('employee_id', [null => 'Select a salesman'] + $employees, null, ['class' =>
                  'form-control select2','required', 'id' =>'employee_id']) !!}
                </div>
  
              </div>
            </div>
  
            <div class="col-xs-3">
              <label>
                Select Order Status
              </label>
              <select name="order_status" class="multi order_status_select" multiple>
                @foreach($order_statuses as $key=>$value)
                  <option value="{{$key}}" selected>{{$value}}</option>
                @endforeach
              </select>
              <p class="help-block has-error order_status" style="color:red;"> </p>
            </div>
  
            <div class="col-xs-3 mt-10">
              <div class="input-group date" style="margin-top: 10px;">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input autocomplete="off" type="text" name="order_date" class="form-control pull-right" id="order_date">
                @if(config('settings.ncal')==1)
                <input type="text" name="order_edate" id="order_edate" hidden />
                @endif
              </div>
            </div>
            <div class="col-xs-3 mt-10">
              <button type="submit" class="btn btn-primary" style="background: #fff;margin-top: 10px;"
                onclick="ajaxLoad('{{ domain_route('company.admin.searchdailysalesreportbysalesman') }}')">
                Search
              </button>
            </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">

          <table id="content" class="table table-bordered table-striped">
            <thead>

              <tr>
                <th>Product Name</th>
                @foreach($units as $unit)
                <th>{{ ucfirst(strtolower($unit->name)) }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="{{ count($units)+1 }}" class="text-center">Please select salesman and Date</td>
              </tr>
            </tbody>

            <tfoot>
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
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script>
  $('.select2').select2();

  $(function () {
    @if(config('settings.ncal')==0)
      $("#order_date").datepicker({
          format: "yyyy-mm-dd",
          endDate: new Date(),
          autoclose: true,
      }).datepicker("setDate", "0");    // Here the current date is set
    @else
      $('#order_date').nepaliDatePicker({
        ndpEnglishInput: 'englishDate',
        onChange:function(){
          $('#order_edate').val(BS2AD($('#order_date').val()));
        }
      });
    @endif
    $('.order_status_select').multiselect({
      search: true,
      placeholder: "Select Order Status",
      selectAll: true
    });
  });
    
  function ajaxLoad(filename) {
    var emp_id= $('#employee_id').val();
    @if(config('settings.ncal')==0)
    var orderdate= $('#order_date').val();
    @else
    var orderdate= $('#order_edate').val();
    @endif
    $('#content tbody').html('<td colspan="{{ count($units)+1 }}" class="text-center">loading..</td></tr>');
    $.ajax({
      type: "GET",
      url: filename,
      data: {employee_id: emp_id, order_date: orderdate, order_status_select: $('.order_status_select').val() },
      success: function (data) {
        $('#content tbody').html(data);
      },
      error: function (xhr, status, error) {
          alert(xhr.responseText);
      }
    });
  }
</script>

@endsection