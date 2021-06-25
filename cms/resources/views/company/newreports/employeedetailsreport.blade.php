@extends('layouts.company')
@section('title', 'Employee Report')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<!-- <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/> -->
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<style> 
  .box-loader {
    opacity: 1.5; 
  }

  .increaseOpacity {
    opacity: 0.3;
  }

  .ms-options-wrap.ms-has-selections>button{
    outline: none!important;
  }

  .multiselect-item.multiselect-group label input{
    height:auto;
  }
  .multiselect-container.dropdown-menu{
    width:inherit;
  }

  .tooltip-inner {
    max-width: 500px !important;
    background-color: aliceblue;
    color: black;
    max-height: -webkit-fill-available;
  }

  .fa.fa-info-circle {
    padding-left: inherit;
    cursor: pointer;
    color: #4c8c16;
  }
  
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Employee Report</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body tablediv">
          @if(\Session::has('success'))
              <div class="alert alert-success">
                {{ \Session::get('success') }}
              </div>
            @endif

            @if(\Session::has('warning'))
              <div class="alert alert-warning">
                {{ \Session::get('warning') }}
              </div>
            @endif

            @if(session('error_message'))
              <div class="alert alert-danger">
                {{ session('error_message') }}
              </div>
            @endif
          <form action="{{domain_route('company.admin.generateemployeedetailsreport')}}" method="post" id="dailysalesmanreport">
            @csrf
            <input type="hidden" id="empids" name="empids">
            <input type="hidden" id="colids" name="colids">
            <div class="row hidden" id="maindatarowdiv">
              <div class="col-xs-3">
                <label>
                  Select Employee
                </label>
                <select id="employee_id" style="width: 100%;" class="parties" multiple>
                  @foreach($employee as $key=>$value)
                    <option value="{{$key}}" selected>{{$value}}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-xs-3">
                <label>
                  Choose Fields
                </label>
                <select id="columns_id" style="width: 100%;" class="parties" multiple>
                  @foreach($columns as $key=>$value)
                    <option value="{{$key}}">{{$value}}</option>
                  @endforeach
                </select>
              </div>

              @if(config('settings.ncal')==1)
                <div class="col-xs-3">
                  <label>
                    Select Date Range
                  </label>
                  <div class="input-group" id="nepCalDiv">
                    <input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date"
                      autocomplete="off" />
                    <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                    <span class="input-group-addon" aria-readonly="true"><i
                        class="glyphicon glyphicon-calendar"></i></span>
                    <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                      autocomplete="off" />
                    <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                  </div>
                </div>
              @else
                <div class="col-xs-3">
                  <label>
                    Select Date Range
                  </label><br />
                  <span id="reportrange" name="reportrange" class="reportrange">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span><i class="fa fa-caret-down"></i>
                  </span>
                </div>
              @endif
              <span>
                <input type="hidden" name="start_date" id="datestart">
                <input type="hidden" name="end_date" id="dateend">
              </span>
            
              <div class="col-xs-3">
                <div class="mx-auto" style="width:100%;padding:10px 0 0 0;margin-top:15px;">
                  <button type="submit" class="btn btn-default" id="getReport" style="width:100%;">
                    <i class="fa fa-book"></i> Get Report
                  </button>
                </div>
              </div>
            </div>
          </form>
          <div class="row tablediv table-responsive">

          </div>
        </div>
      </div>

      <div class="box">
        <div class="box-header">
          <h3 class="box-title" id="box-title">Generated Reports</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body" id="mainBox">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-3">
                    <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="partyfilter"></div>
                  </div>
                  <div class="col-xs-3">
                    <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="salesmfilter"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-3"></div>
          </div>
          <input type="hidden" name="next_refresh" id="next_refresh">
          <table id="order" class="table table-bordered table-striped table-responsive" style="width: 100%;">
            <thead>
              <tr>
                <th class="hidden">#</th>
                <th>Date Generated</th>
                <th>Date Range</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              {{-- @foreach ($reports_generated as $reports)
              <tr>
                <td class="hidden"></td>
                @if(config('settings.ncal')==0)
                <td>{{$reports->created_at->format('Y-m-d')}}</td>
                @else
                <td>{{getDeltaDateFormat($reports->created_at->format('Y-m-d'))}}</td>
                @endif
                <td>{{getEmployee($reports->employee_id)->name}}</td>
                <td>{{strtoupper($reports->report_cat)}}</td>
                @if(isset($reports->start_date) && isset($reports->end_date))
                @if($reports->start_date == $reports->end_date)
                <td>{{getDeltaDateFormat($reports->start_date)}}</td>
                @else
                <td>{{getDeltaDateFormat($reports->start_date)}} to {{getDeltaDateFormat($reports->end_date)}}</td>
                @endif
                @else
                <td>{{$reports->date_range}}</td>
                @endif
                <td>
                  @if(!empty($reports->download_link))
                  <a href="{{ $reports->download_link}}" id="download_button"
                    download="{{ $reports->filename}}">
                    <i class="fa fa-download" aria-hidden="true"></i>
                  </a>
                  @else
                    @if($reports->processing==1)
                      <a href="#">
                      <i class="fa fa-spinner fa-pulse fa-fw"></i>Processing</a>
                    @else
                      <a href="#">
                      <i class="fa fa-spinner fa-pulse fa-fw"></i>Pending</a>
                    @endif
                  @endif

                </td>
              </tr>
              @endforeach --}}
            </tbody>
            <tfoot></tfoot>
          </table>
          <div id="loader1" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            {{-- <p style="margin: 0 0 0 0; font-weight: bolder;">
              Report is being generated.<br /> Depending upon the internet connection it may take several
              minutes.<br /> Please come back in a moment.
            </p> --}}
          </div>
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
<!-- <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script> -->

<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<!-- <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script> -->


<script type="text/javascript">

    $('#employee_id').multiselect({
      placeholder: "Select Employee",
      selectAll: true,
      search: true,
    });

    $('#columns_id').multiselect({
      placeholder: "Select Columns",
      selectAll: true,
      search: true,
    });

    $("#maindatarowdiv").removeClass("hidden");

    $(document).ready(function(){
      $('#loader1').attr('hidden','hidden');
      $('#mainBox').removeClass('box-loader');

      initializeOrderDT();
    });

    @if(config('settings.ncal')==0)

      $('document').ready(function () {
        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
          $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
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
    @else
      var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
      var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
      $('#start_ndate').val(lastmonthdate);
      $('#end_ndate').val(ntoday);
      $('#start_edate').val(BS2AD($('#start_ndate').val()));
      $('#end_edate').val(BS2AD($('#end_ndate').val()));
      $('#start_ndate').nepaliDatePicker({
        ndpEnglishInput: 'englishDate',
        onChange:function(){
          $('#start_edate').val(BS2AD($('#start_ndate').val()));
          if($('#start_ndate').val()>$('#end_ndate').val()){
            $('#end_ndate').val($('#start_ndate').val());
            $('#end_edate').val(BS2AD($('#start_ndate').val()));
          }
        }
      });
      $('#end_ndate').nepaliDatePicker({
        onChange:function(){
          $('#end_edate').val(BS2AD($('#end_ndate').val()));
          if($('#end_ndate').val()<$('#start_ndate').val()){
            $('#start_ndate').val($('#end_ndate').val());
            $('#start_edate').val(BS2AD($('#end_ndate').val()));
          }
        }
      });
    @endif

    function getDates(start_date, end_date) {
      var dateArray = [];
      var currentDate = moment(start_date);
      var stopDate = moment(end_date);
      while (currentDate <= stopDate) {
        dateArray.push( moment(currentDate).format('YYYY-MM-DD') )
        currentDate = moment(currentDate).add(1, 'days');
      }
      return dateArray;
    }

    $('#dailysalesmanreport').on('submit', function (e) {
      event.preventDefault();
      $('#getReport').prop('disabled', true);
      let current = $(this);
      @if(config('settings.ncal')==0)
        var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
      @else
        var start_date = $('#start_edate').val();
        var end_date = $('#end_edate').val();
      @endif
      var salesman_id = $('#salesman_id').val();
      var report_type = $('#report_type').val();
      var all_dates = getDates(start_date, end_date);
      var order_status_select = $('.order_status_select').val();

      $('#datestart').val(start_date);
      $('#dateend').val(end_date);
      if(moment(end_date).diff(moment(start_date), "year") >= 1){
        alert("Please choose time range less than 1 year ");
        $('#getReport').prop('disabled', false);
        return false;
      }
      current.unbind("submit").submit();
    });
    var table;

    function initializeOrderDT(){
      table = $('#order').DataTable({
        "stateSave": true,
        language: { search: "" },
        "order": [[ 0, "desc" ]],
        "serverSide": true,
        "processing": false,
        'searching': false,
        "paging": true,
        "columnDefs": [
          {
            "orderable": false,
            "targets":[0,-1],
          },],
        "ajax":{
          "url": "{{ domain_route('company.admin.generateemployeedetailsreportdt') }}",
          "dataType": "json",
          "type": "POST",
          "data":{  
            _token: "{{csrf_token()}}", 
          },
          beforeSend:function(url, data){
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            $('.tips').tooltip();
          },
          error:function(){
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
            $('.tips').tooltip();
          },
          complete:function(data){
            $('.tips').tooltip();
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
          }
        },
        "columns": [{"data": "date_generated"},
          {"data": "date_range"},
          {"data": "action"},
        ],
        drawCallback:function(settings)
        {
          $('[data-toggle="tooltip"]').tooltip({
            placement : 'right',
            container: 'body'
          });
          $('#next_refresh').val(settings.json.next_refresh);
        }
      });
    }
    
    setInterval(() => {
      if($('#next_refresh').val() > 0){
        table.ajax.reload();
      }
    }, 30000);
    //responsive 
    $('#reportrange').on('click',function(){
      if ($(window).width() <= 320) {   
        $(".daterangepicker").addClass("spwreportsdateposition");
        
      }
      else if ($(window).width() <= 768) {
        $(".daterangepicker").addClass("spwreportsdateposition");
      }
      else {   
        $(".daterangepicker").removeClass("spwreportsdateposition");
      }
    });

    $("#getReport").on('click',function(){
      var arr = []; var str = '';
      $("#employee_id :selected").each(function(){
        arr.push(this.value);
        str = arr.join(',');
      });
      $('#empids').val(str);

      var arr2 = []; var str2 = '';
      $("#columns_id :selected").each(function(){
        arr2.push(this.value);
        str2 = arr2.join(',');
      });
      $('#colids').val(str2);
    });



</script>
@endsection