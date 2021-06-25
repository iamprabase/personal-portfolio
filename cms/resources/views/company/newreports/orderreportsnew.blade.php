@extends('layouts.company')
@section('title', 'Order Reports')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style> 
  .table-condensed>tbody>tr>td,
  .table-condensed>tbody>tr>th,
  .table-condensed>tfoot>tr>td,
  .table-condensed>tfoot>tr>th,
  .table-condensed>thead>tr>td,
  .table-condensed>thead>tr>th {
    padding: 3px !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 22px;
  }

  button,
  input,
  select,
  textarea {
    height: 40px;
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

  .box-loader {
    opacity: 1.5;
  }

  .fa.fa-info-circle {
    padding-left: inherit;
    cursor: pointer;
    color: #4c8c16;
  }

  .tooltip-inner {
    max-width: 500px !important;
    background-color: aliceblue;
    color: black;
    max-height: -webkit-fill-available;
  }

  .increaseOpacity {
    opacity: 0.3;
  }

  /* div.ndp-corner-all-party {
    top: 211px;
    left: 1128.25px !important;
  } */

  @media screen and (max-width: 425px) {
    #salesmanOrPartyLabel {
      padding-bottom: 20px;
    }
  }

  .no-lt-pd{
    padding-left: 0px;
  }

  .no-margin{
    margin: 0px;
  }

  .flex-disp{
    display: inline-flex;
    justify-content: space-around;
  }

  .reportrange {
    width: 95%;
    position: relative!important;
  }

  .ndp-nepali-calendar{
    padding: 3px!important;
  }

  #nepCalDiv{
    padding-right: 50px;
  }

  .pd-top{
    padding-top: 10px;
  }
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      
      <!-- Form Selection Box -->
      <div class="box">
        <div id="loader2" hidden>
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
        <form action="{{domain_route('company.admin.getvariantreports')}}" method="post" id="getorderreportsnew">
          @csrf
          <div class="box-header">
            <h3 class="box-title"> Order Reports </h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            @if(session('successful_message'))
              <div class="alert alert-success">
                {{ session('successful_message') }}
              </div>
            @endif

            @if(session('error_message'))
              <div class="alert alert-warning">
                {{ session('error_message') }}
              </div>
            @endif
            <div class="box-body no-margin">
              <div class="col-xs-3 no-lt-pd">
                <label id="salesmanOrPartyLabel">
                  Which report do you want?
                </label><span style="color: red">*</span>
                <select id="party_salesman" name="party_salesman" class="select2">
                  <option> </option>
                  <option value="custom_party">Parties Sales Report </option>
                  <option value="custom_salesman">Salesman Sales Report </option>
                  <option value="beat_wise">Beat-wise Sales Report </option>
                  <option value="order_status_wise">Order Status - wise Report </option>
                </select>
                @if($errors->has('party_salesman'))
                <p class="help-block has-error" style="color:red;">{{$errors->first('party_salesman')}}</p>
                @endif
              </div>

              <div class="col-xs-3 no-lt-pd" id="party_type_hs" hidden>
                <label>
                  Select Party Type
                </label><span style="color: red">*</span>
                <select id="party_type" name="party_type" style="width: 100%;" class="select2" multiple>
                  @foreach($partytypes as $key=>$value)
                    <option value="{{$key}}" selected>{{$value}}</option>
                  @endforeach
                  <option value="0" selected>Unspecified</option>
                </select>
              </div>
    
              <div class="col-xs-3 no-lt-pd">
                <label>
                  Select <span id="dynamicTitle"></span>
                </label><span style="color: red">*</span>
                <select id="emp_party_sel" name="emp_party_sel[]"
                  style="background: #fff; cursor: pointer; padding: 5px 0px; border: 1px solid #ccc; width: 100%;position: relative;"
                  class="multi" multiple>
    
                </select>
                @if($errors->has('emp_party_sel'))
                <p class="help-block has-error" style="color:red;">{{$errors->first('emp_party_sel')}}</p>
                @endif
              </div>
              
              <div class="col-xs-2 no-lt-pd">
                <label>
                  Select Order Status
                </label>
                <select name="order_status[]" class="multi order_status_select" multiple>
                  @foreach($order_statuses as $key=>$value)
                    <option value="{{$key}}" selected>{{$value}}</option>
                  @endforeach
                </select>
                @if($errors->has('order_status'))
                <p class="help-block has-error" style="color:red;">{{$errors->first('order_status')}}</p>
                @endif
              </div>
              
              @if(config('settings.ncal')==1)
                <div class="col-xs-4 no-lt-pd">
                  <label>
                    Select Date Range
                  </label><span style="color: red">*</span>
                  <div class="input-group" id="nepCalDiv">
                    <input id="start_ndate" class="form-control" type="text" name="start_ndate"
                      placeholder="Start Date" autocomplete="off" required />
                    <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                    <span class="input-group-addon" aria-readonly="true"><i
                        class="glyphicon glyphicon-calendar"></i></span>
                    <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                      autocomplete="off" />
                    <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />

                     <input type="hidden" name="start_date" id="datestart">
                  <input type="hidden" name="end_date" id="dateend">
                  </div>
                  @if($errors->has('start_date'))
                  <p class="help-block has-error" style="color:red;">{{$errors->first('start_date')}}</p>
                  @endif
                  @if($errors->has('end_date'))
                  <p class="help-block has-error" style="color:red;">{{$errors->first('end_date')}}</p>
                  @endif
                </div>
              @else
                <div class="col-xs-4 no-lt-pd">
                  <label>
                    Select Date Range
                  </label><span style="color: red">*</span><br />
                  <span class="reportrange" id="reportrange" name="reportrange"
                    style="background: #fff; cursor: pointer; padding: 5px 0px; border: 1px solid #ccc;position: absolute;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span><i class="fa fa-caret-down"></i>
                  </span>
                  <input type="hidden" name="start_date" id="datestart">
                  <input type="hidden" name="end_date" id="dateend">
                  @if($errors->has('start_date'))
                  <p class="help-block has-error" style="color:red;">{{$errors->first('start_date')}}</p>
                  @endif
                  @if($errors->has('end_date'))
                  <p class="help-block has-error" style="color:red;">{{$errors->first('end_date')}}</p>
                  @endif
                </div>
              @endif
            </div>
            
            <div class="box-body no-margin">
              <div class="col-xs-7 col-xs-offset-2 flex-disp pd-top">
                <div>
                  <label>
                    How do you want the reports to be split?
                  </label>
                </div>
                <div>
                  <label class="radio inline">
                    <input class="report_type" type="radio" name="report_type" value="brand" checked>By Brand
                  </label>
                </div>
                <div>
                  <label class="radio inline">
                    <input class="report_type" type="radio" name="report_type" value="category">By Category
                  </label>
                </div>
                <div>
                  <label class="radio inline">
                    <input class="report_type" type="radio" name="report_type" value="consolidated">Aggregated
                  </label>
                </div>
              </div>
            </div>

            
          </div>
          <div class="box-body no-margin">
            <div class="col-xs-4 col-xs-offset-4 pd-top">

              <button type="submit" class="btn btn-default input-group" id="getReport" style="width:100%;">
                <i class="fa fa-book"></i> Get Report
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Table Box  -->
      <div class="box">
        <div class="box-header">
          <h3 class="box-title" id="box-title">Generated Reports</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive" id="mainBox">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7"></div>
            <div class="col-xs-3"></div>
          </div>
          <input type="hidden" name="next_refresh" id="next_refresh">
          <table id="order" class="table table-bordered table-striped">
            <thead>

              <tr>
                <th class="hidden">#</th>
                <th>Date Generated</th>
                <th>Report Type</th>
                <th>Date Range</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>
              {{-- @foreach($generated_reports as $report)
              <tr>
                <td class="hidden"></td>
                @if(config('settings.ncal')==0)
                <td>{{$report->created_at->format('Y-m-d')}}</td>
                @else
                <td>{{getDeltaDate($report->created_at->format('Y-m-d'))}}</td>
                @endif
                <td>
                  {{$report->report_type}} -- Order Report -- {{ ($report->report_cat)}}
                  <span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip"
                    data-original-title="<b>The report was generated for following parties:-</b><br/><span>{{$report->tooltip_content}}</span>">
                  </span>
                </td>
                @if(isset($report->start_date) && isset($report->end_date))
                @if($report->start_date == $report->end_date)
                <td>{{getDeltaDate($report->start_date)}}</td>
                @else
                <td>{{getDeltaDate($report->start_date)}} to {{getDeltaDate($report->end_date)}}</td>
                @endif
                @else
                <td>{{$report->date_range}}</td>
                @endif
                <td>
                  @if(!empty($report->download_link))
                  <a href="{{ $report->download_link}}" id="download_button"
                    download="{{ urldecode($report->filename)}}">
                    <i class="fa fa-download" aria-hidden="true"></i>
                  </a>
                  @else
                    @if($report->processing==1)
                  <a href="#">
                    <i class="fa fa-spinner fa-pulse fa-fw"></i>Processing</a>
                    @elseif($report->processing==3)
                    No Record
                    @else
                    <a href="#">
                    <i class="fa fa-spinner fa-pulse fa-fw"></i>Pending</a>
                    @endif
                     
                  @endif
                </td>
              </tr>
              @endforeach --}}
              
            </tbody>
            <tfoot>
            </tfoot>
            <div id="loader1" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              {{-- <p style="margin: 0 0 0 0; font-weight: bolder;">
                Report is being generated.<br /> Depending upon the internet connection it may take several
                minutes.<br /> Please come back in a moment.
              </p> --}}
            </div>
          </table>
        </div>
        <!-- /.box-body -->
      </div>

    </div>
  </div>
</section>
@endsection

@section('scripts')
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script type="text/javascript">
  function showLoader(){
    $('#loader2').removeAttr('hidden');
  }

  function hideLoader(){
    $('#loader2').attr('hidden', 'hidden');
  }

  $('document').ready(function(){
    const sel_val = $("#party_salesman").val();
    if(sel_val!=""){
      $('select[id="party_salesman"]').trigger('change'); 
    }
    $('[data-toggle="tooltip"]').tooltip({
      placement : 'right',
      container: 'body'
    });
    $('.multi').multiselect({
      search:true,
      placeholder: "Select",
      selectAll: true,
      required: true
    });
    $('#party_type').multiselect({
      search:true,
      placeholder: "Select Party Type",
      selectAll: true
    });
    $('#emp_party_sel').multiselect({
      enableFiltering:true,
      placeholder: "Select Party Type",
      selectAll: true
    });
    $('#party_salesman').select2({
      placeholder: "Salesman or Party or Beat or Order Status",
    });
    $('#report_type').select2({
      placeholder: "Select Report Type",
    });
    initializeOrderDT();
    
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

      $('body').on('change','.daterangepicker',function() {

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
  });
  $('[data-toggle="tooltip"]').tooltip({
    placement : 'right',
    container: 'body'
  });
  // $('#order').DataTable({
  //   "columnDefs": [
  //   { "orderable": false, "targets": -1 }
  //   ],
  //   'searching': false,
  // });
  var tabl ;
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
        "url": "{{ domain_route('company.admin.orderreportsdt') }}",
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
        {"data":  "report_type"},
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
  } // Data Table initialize 

  $('#party_type').change(function(){
    let sel_party_types = $('#party_type').val();
    
    if(sel_party_types.length>0){
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ domain_route('company.admin.fetchpartylist') }}",
        type: "GET",
        data:
        {
          sel_party_types : sel_party_types,
        },
        beforeSend:function(){
          showLoader();
        },
        success:function(data) {
          data = JSON.parse(data);
          $('select[id="emp_party_sel"]').empty();
          $('select[id="emp_party_sel"]').multiselect('reload');
          data.forEach(element => {
            $('select[id="emp_party_sel"]').append('<option value="'+ element['id'] +'" data-client_type="'+ element['client_type'] +'" selected>'+ element['company_name'] +'</option>');
          });
          $('select[id="emp_party_sel"]').multiselect('reload');
          hideLoader();
        },
        error:function(xhr, textStatus){
          hideLoader();
          alert(textStatus);
        }
      });
    }else{
      $('select[id="emp_party_sel"]').empty();
      $('select[id="emp_party_sel"]').multiselect('reload');
    }
  });
  $('select[id="party_salesman"]').on('change', function (event) {
    var vs_search_ID = $('select[id="party_salesman"] :selected').val();
    if(vs_search_ID=='order_status_wise') $('.order_status_select').parent().attr('hidden', 'hidden');
    else $('.order_status_select').parent().removeAttr('hidden');
    
    if(vs_search_ID == "custom_party"){
      $('#dynamicTitle').html('Parties');
      $('#party_type_hs').css('display', 'block');
      $('.ndp-corner-all').addClass('ndp-corner-all-party');
    }else if(vs_search_ID == "custom_salesman"){
      $('#dynamicTitle').html('Salesmen');
      $('#party_type_hs').css('display', 'none');
      if($('.ndp-corner-all').hasClass('ndp-corner-all-party')){
        $('.ndp-corner-all').removeClass('ndp-corner-all-party');
      }
    }else if(vs_search_ID == "beat_wise"){
      $('#dynamicTitle').html('Beats');
      $('#party_type_hs').css('display', 'none');
      if($('.ndp-corner-all').hasClass('ndp-corner-all-party')){
        $('.ndp-corner-all').removeClass('ndp-corner-all-party');
      }
    }else if(vs_search_ID == "order_status_wise"){
      $('#dynamicTitle').html('Order Status');
      $('#party_type_hs').css('display', 'none');
      if($('.ndp-corner-all').hasClass('ndp-corner-all-party')){
        $('.ndp-corner-all').removeClass('ndp-corner-all-party');
      }
    }
    if (vs_search_ID) {
      if(vs_search_ID=='order_status_wise'){
        $('select[id="emp_party_sel"]').empty();
        $.each(@json($order_statuses), function (key, value) {
            $('select[id="emp_party_sel"]').append('<option value="' + key + '"selected>' + value + '</option>');
        });
        $('.multi').multiselect('reload');
        $('.multi').multiselect({
          placeholder: 'Select',
          columns: 1,
          search: true,
          selectAll: true,
          keepOrder: true,
          maxPlaceholderOpts : 2,
        });
      }else{
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
            showLoader();
          },
          success: function (data) {
              $('select[id="emp_party_sel"]').empty();
              $.each(data, function (key, value) {
                  $('select[id="emp_party_sel"]').append('<option value="' + key + '"selected>' + value + '</option>');
              });
              $('.multi').multiselect('reload');
              $('.multi').multiselect({
                placeholder: 'Select',
                columns: 1,
                search: true,
                selectAll: true,
                keepOrder: true,
                maxPlaceholderOpts : 2,
              });
              hideLoader();
          },
          error:function(xhr, textStatus){
            hideLoader();
            alert(textStatus);
          }
        });

      }
    } else {
        $('select[name="emp_party_sel"]').empty();
    }

    if (vs_search_ID == "party" || vs_search_ID == "salesman") {
      $('#emp_party_sel').attr('disabled', true);
    } else {
      $('#emp_party_sel').attr('disabled', false);
    }
  });
  $('#reportrange').on('change',function() {
    var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    $("#datestart").val(start_date);
    $("#dateend").val(end_date);
  });

  $('#getorderreportsnew').on('submit', function (e) {
    e.preventDefault();

    //alert('hi');
    if($('#emp_party_sel').val().length==0) {
      alert("Please select party/employee field.");
      return;
    }

    @if(config('settings.ncal')==0)
      var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    @else
      var start_date = $('#start_edate').val();
      var end_date = $('#end_edate').val();
    @endif

    $('#datestart').val(start_date);
    $('#dateend').val(end_date);
    if(moment(end_date).diff(moment(start_date), "year") >= 1){
      alert("Please choose time range less than 1 year ");
      return false;
    }

    $('#getReport').text('Please wait ...');

    document.getElementById('getorderreportsnew').submit();
    return false;
  });

  //responsive 
  $('#reportrange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("oreportsdateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("oreportsdateposition");
    }
    else {   
      $(".daterangepicker").removeClass("oreportsdateposition");
    }
  });
  setInterval(() => {
    if($('#next_refresh').val() > 0){
      table.ajax.reload();
    }
  }, 30000);
</script>
@endsection