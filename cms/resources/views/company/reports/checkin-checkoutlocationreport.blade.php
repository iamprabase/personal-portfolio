@extends('layouts.company')
@section('title', 'Checkin-Checkout Location Report')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  @if(config('settings.ncal')==1)
  <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet"
    href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
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

    #dailyempreport_filter, #dailyempreport_paginate {
      float: right;
    }

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
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Checkin-Checkout Location Report</h3>
            <span id="checkin_checkoutlocationexport" class="pull-right">
        </span>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            @if(config('settings.ncal')==0)
              <div id="reportrange" name="reportrange" class="reportrange hidden"
                  style="background: #fff; cursor: pointer; padding: 5px 0px; border: 1px solid #ccc;position: absolute;margin-left: 40%;z-index: 999;width: 220px;">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
                <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
              </div>
            @else
              <div class="input-group hidden" id="nepCalDiv"  style="background: #fff; cursor: pointer; position: absolute;margin-left: 40%;z-index: 999;width:30%;">
                <input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date" autocomplete="off"/>
                <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date" autocomplete="off"/>
                <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
              </div>
            @endif
            <div id="empfilter"
              style="background: #fff; cursor: pointer;position: absolute;margin-left: 15%;z-index: 999;width:250px;">
              <select sname='employee' id='employee_filters' class='employee_filters hidden'>
                <option></option>
                <option value=null>All</option>
                @forelse($employeesWithAttendances as $id=>$employee)
                <option value='{{$id}}'>{{$employee}}</option>
                @empty
                <option></option>
                @endforelse
              </select>
            </div>
            <div id="salesmfilter"></div>
            <div id="mainBox">
              <table id="checkin_checkoutlocationreport" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>S.No.</th>
                    <th>Employee Name</th>
                    <th>Date</th>
                    <th>Check-in Time</th>
                    <th>Check-in Location</th>
                    <th>Last Check-out Time</th>
                    <th>Last Check-out Location</th>
                  </tr>
                </thead>
                <div id="loader1">
                  <img src="{{asset('assets/dist/img/loader2.gif')}}" />
                </div>
              </table>
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

  <div class="modal modal-default fade" id="workhours" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div id="hourdetails"></div>

        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-default fade" id="disttravel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div id="traveldetails"></div>

        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Location Map: Mr. DHEENADHAYALAN SELVARAJ 03-04-2018</h4>
        </div>
        <div class="modal-body">
          <div id="devmap" style="height:350px; width: 570px;"></div>

        </div>
        <div class="modal-footer">
          <p id="total-distance" style="float:left;font-weight: bold;"></p>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
    <form method="post" action="{{domain_route('company.admin.reports.customPdfExport')}}" class="pdf-export-form hidden"
      id="pdf-generate">
      {{csrf_field()}}
      <input type="text" name="exportedData" class="exportedData" id="exportedData">
      <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
      <input type="text" name="reportName" class="reportName" id="reportName">
      <input type="text" name="columns" class="columns" id="columns">
      <input type="text" name="properties" class="properties" id="properties">
      <button type="submit" id="genrate-pdf">Generate PDF</button>
    </form>
  </div>
@endsection

@section('scripts')
  <script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
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
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  @if(config('settings.ncal')==1)
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  @else
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  @endif

  <script type="text/javascript">
    $(function () {
        $('#employee_filters').select2({
        "placeholder": "Select Employee",
        });
      @if(config('settings.ncal')==0)
        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
          $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
          $('#startdate').val(start.format('MMMM D, YYYY'));
          $('#enddate').val(end.format('MMMM D, YYYY'));
          $('#start_edate').val(start.format('Y-MM-DD'));
          $('#end_edate').val(end.format('Y-MM-DD'));
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
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
          var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          $('#start_edate').val(start);
          $('#end_edate').val(end);
          var empVal = $('.employee_filters').find('option:selected').val();
          if(empVal=="null"){
            empVal = null;
          }

          var startD = $('#start_edate').val();
          var endD = $('#end_edate').val();
          if(startD != '' || endD != ''){
            $('#checkin_checkoutlocationreport').DataTable().destroy();
            initializeDT(empVal, start, end);
          }
        });

        $('#reportrange').removeClass('hidden');
      @else
        $('#nepCalDiv').removeClass('hidden');
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
            var empVal = $('.employee_filters').find('option:selected').val();
            if(empVal=="null"){
              empVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(end==""){
              end = start;
            }
            if(start != '' || end != '')
            {
              $('#checkin_checkoutlocationreport').DataTable().destroy();
              initializeDT(empVal, start, end);
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
            var empVal = $('.employee_filters').find('option:selected').val();
            if(empVal=="null"){
              empVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(start==""){
              start = end;
            }
            if(start != '' || end != '')
            {
              $('#checkin_checkoutlocationreport').DataTable().destroy();
              initializeDT(empVal, start, end);
            }
          }
        });
      @endif

      var table;
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      // Load Data Table on ready 
      initializeDT(null, start, end);
        //responsive 
        $('#reportrange').on('click',function(){
          if ($(window).width() <= 320) {   
            $(".daterangepicker").addClass("cicodateposition");
            
          }
          else if ($(window).width() <= 768) {
            $(".daterangepicker").addClass("cicodateposition");
          }
          else {   
            $(".daterangepicker").removeClass("cicodateposition");
          }
        });
    });
    $('body').on("change", ".employee_filters",function () {
      var empVal = $('.employee_filters').find('option:selected').val();
      var employeeVal = $(this).find('option:selected').val();
      if(employeeVal=="null"){
        employeeVal = null;
      }
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      if(employeeVal != '')
      {
        $('#checkin_checkoutlocationreport').DataTable().destroy();
        initializeDT(employeeVal, start, end);
      }
    });
    function initializeDT(empVal=null, startD, endD){
      const table = $('#checkin_checkoutlocationreport').DataTable({
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search"
        },
        "order": [[ 2, "desc" ]],
        "serverSide": true,
        "processing": true,
        "paging": true,
        "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
                "<'row'<'col-xs-6'><'col-xs-6'>>" +
                "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
        "columnDefs": [ 
          { 
            width: 20, 
            targets: [0],
          },
        ],
        "buttons": [
          {
            extend: 'pdfHtml5', 
            title: 'Checkin-Checkout Location Report', 
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'excelHtml5', 
            title: 'Checkin-Checkout Location Report', 
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'print', 
            title: 'Checkin-Checkout Location Report', 
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
        ],
        "ajax":{
          "url": "{{ domain_route('company.admin.checkin_checkoutReportDataTable') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}", 
            empVal : empVal,
            startDate: startD,
            endDate: endD, 
          },
          beforeSend:function(){
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
          },
          error:function(){
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
          },
          complete:function(){
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
          }
        },
        "columns": [
          {"data" : "id"},
          {"data" : "employee_name"},
          {"data" : "date"},
          {"data" : "checkin_time"},
          {"data" : "checkin_address"},
          {"data" : "checkout_time"},
          {"data" : "checkout_address"},
        ],
      });
      table.buttons().container()
          .appendTo('#checkin_checkoutlocationexport');
      var oldExportAction = function (self, e, dt, button, config) {
        if (button[0].className.indexOf('buttons-excel') >= 0) {
          if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
              $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
          } else {
              $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
          }
        } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
          if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
              $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
          } else {
              $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
          }
        } else if (button[0].className.indexOf('buttons-print') >= 0) {
          $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
        }
      };

      var newExportAction = function (e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function (e, s, data) {
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
          data.start = 0;
          data.length = 10000;
          dt.one('preDraw', function (e, settings) {
            if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
              var moduleName = "checkin-checkout";
              var columns = JSON.stringify(["Employee Name", "Date", "Check-In Time", "Check-In Locaiton", "Last Check-Out Time", "Last Check-Out Location"]);
                $.each(settings.json.data, function(key, htmlContent){
                settings.json.data[key].id = key+1;
                settings.json.data[key].employee_name = $(settings.json.data[key].employee_name)[0].textContent;
              });
              customExportAction(config, settings, moduleName, columns);
            }else{
              oldExportAction(self, e, dt, button, config);
            }
            // oldExportAction(self, e, dt, button, config);
            dt.one('preXhr', function (e, s, data) {
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
                $('#mainBox').removeClass('box-loader');
                $('#loader1').attr('hidden', 'hidden');
            });
            setTimeout(dt.ajax.reload, 0);
            return false;
          });
        });
        dt.ajax.reload();
      }
      function customExportAction(config, settings, modName, cols){
        $('#exportedData').val(JSON.stringify(settings.json));
        $('#pageTitle').val(config.title);
        $('#reportName').val(modName);
        $('#columns').val(cols);
        var properties = JSON.stringify(["id", "employee_name", "date", "checkin_time", "checkin_address", "checkout_time", "checkout_address"]);
        $('#properties').val(properties);
        $('#pdf-generate').submit();
      }
      $('.hidden').removeClass('hidden');
    }; // Data Table initialize 
  </script>
@endsection
