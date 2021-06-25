@extends('layouts.company')
@section('title', 'Salesman Target Report')

@section('stylesheets')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />

 
<style>
    .box-loader{
      opacity: 0.5;
    }
    table td {
      max-width: 300px;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }

    .close{
      font-size: 30px;
      color: #080808;
      opacity: 1;
    }
    .hide_column {
        display: none;
    }

    .round {
        position: relative;
        width: 15px;
    }

    .round label {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 50%;
        cursor: pointer;
        height: 15px;
        left: 0;
        position: absolute;
        top: 3px;
        width: 28px;
    }

    .round label:after {
        border: 2px solid #fff;
        border-top: none;
        border-right: none;
        content: "";
        height: 6px;
        left: 0px;
        opacity: 0;
        position: absolute;
        top: 3px;
        transform: rotate(-45deg);
        width: 12px;
    }

    .round input{
        height: 10px;
    }

    .round input[type="checkbox"] {
        visibility: hidden;
    }

    .round input[type="checkbox"]:checked + label {
        background-color: #66bb6a;
        border-color: #66bb6a;
    }

    .round input[type="checkbox"]:checked + label:after {
        opacity: 1;
    }

    .pad-left{
        padding-left: 0px;
    }


    .col-xs-5 .btn-primary, .col-xs-4 .btn-primary{
      background-color: #0b7676;
    }

    .flex-prop{
      display: flex;
    }

    .label-custom{
      margin-left: 8px;
    }

</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-xs-4">
              <h3 class="box-title">Salesman Target Report</h3>
            </div>
            <div class="col-md-offset-4 col-md-4">
              <span id="expenseexports" class="pull-right"></span>
            </div>
          </div>
        </div>
        <!-- /.box-header --> 
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-8">
              <div class="row">
                <div class="select-2-sec">
                  @if(Auth::user()->can('targets_rep-create'))
                    <div class="col-xs-4">
                      <div class="brandsDiv hidden" style="margin-top:10px;">
                          <select name="salesmanid[]" style="width: 100%;" id="salesmanid" multiple>
                            @if(count($data['allsalesman'])>0)
                              @foreach($data['allsalesman'] as $id=>$salesman)
                                <option selected value="{{$id}}">{{$salesman}}</option>
                              @endforeach
                            @endif
                          </select>
                      </div>
                    </div>
                  @endif
                  <div class="col-xs-1">
                    
                  </div>
                  <div class="col-xs-7" style="margin-top:10px;">
                    <!-- <span id="expenseexports"></span> -->
                    @if(config('settings.ncal')==0)  
                      <div class="col-md-5">
                        <input type="text" class="form-control form-control-1 input-xs hidden" id="datepicker">
                      </div>
                      <div class="col-xs-5">
                        <button class="btn btn-primary hidden" id="getReport" style="width: 100%;">
                          <span><i class="fa fa-book"></i> View Report</span>
                        </button>
                      </div> 
                    @else
                    <div class="col-md-3">
                      <select class="select2 hidden" id="year"
                        style="background: #fff; cursor: pointer;"></select>
                    </div>
                    <div class="col-md-3">
                      <select class="select2 hidden" id="month"
                        style="background: #fff; cursor: pointer;"></select>
                    </div>
                    <input type="hidden" id="nepDate" value="{{$getNepDate}}">
                    <div class="col-xs-5">
                      <button class="btn btn-primary hidden" id="getReport" style="width: 100%;">
                        <span><i class="fa fa-book"></i> View Report</span>
                      </button>
                    </div>   
                    @endif
                    <input type="hidden" id="year">
                    <input type="hidden" id="month">   
                  </div>
                </div>
              </div> 
            </div>
            <div class="col-xs-2">
            </div>
          </div>
          <div id="loader1" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <div id="mainBox">
          <style>
          .err_sp{
            background: #ef10101c;    
            margin-bottom: 7px;
            padding: 5px 7px;
            display: block;
            color: black;
            text-align: center;
          }
          .suc_sp{
            background: #2cda001c;    
            margin-bottom: 7px;
            padding: 5px 7px;
            display: block;
            color: black;
            text-align: center;
          }
          </style>
            <table id="expense" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th>S.No.</th>
                  <th>Salesman Name</th>
                  @if(config('settings.orders')==1)
                    <th>No. of Orders</th>
                  @endif
                  @if(config('settings.orders')==1)
                    <th>Value of Orders</th>
                  @endif
                  @if(config('settings.collections')==1)
                    <th>No. of Collections</th>
                  @endif
                  @if(config('settings.collections')==1)
                    <th>Value of Collections</th>
                  @endif
                  @if(config('settings.visit_module')==1)
                    <th>No. of Visits</th>
                  @endif
                  @if(config('settings.party')==1)
                    <th>Golden Calls (New Parties)</th>
                  @endif
                  @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
                    <th>Total Calls (No.of Orders+No. of Zero Orders)</th>
                  @endif
                </tr>
              </thead>
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

<input type="hidden" name="pageIds[]" id="pageIds">
<form method="post" action="{{domain_route('company.admin.salesmantargetreporpdf')}}" class="pdf-export-form hidden"
  id="pdf-generate">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>

 

@endsection

@section('scripts')
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
<!-- <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script> -->

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script> -->

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="{{asset('assets/dist/js/tether.min.js') }}"></script>

@if(config('settings.ncal')==1)
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
<script src="{{asset('assets/dist/js/jquery.nepaliDatePicker.min.js') }}"></script>
<script src="{{asset('assets/dist/js/datePickerSM.js') }}" type="text/javascript"></script>

@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>


<script>

  var noorder = valorder = nocoll = valcoll = novis = goldcall = totcall = 'npres';

  @if(config('settings.ncal')==0)
    $('document').ready(function(){
      var pickedYear = new Date().getFullYear();
      var pickedMonth = new Date().getMonth() + 1;
      $('#year').val(pickedYear);
      $('#month').val(pickedMonth);
    })
    $("#datepicker").datepicker( {
      format: "MM-yyyy",
      viewMode: "years", 
      minViewMode: "months",
      startDate: '-1y',
      endDate: '+1m',
      autoclose: true,
    }).datepicker("setDate", new Date());
    $("#datepicker").datepicker().on('changeDate', function (e) {
      var pickedYear = new Date(e.date).getFullYear();
      var pickedMonth = new Date(e.date).getMonth() + 1;
      $('#year').val(pickedYear);
      $('#month').val(pickedMonth);
    });
  @else
    var currentDate = new Date();
    var currentNepaliDate = calendarFunctions.getBsDateByAdDate(currentDate.getFullYear(), currentDate.getMonth() + 1, currentDate.getDate());
    var formatedNepaliDate = calendarFunctions.bsDateFormat("%M %y", currentNepaliDate.bsYear, currentNepaliDate.bsMonth, currentNepaliDate.bsDate);
    $("#datepicker").on('focus', function(){
      $("#datepicker").val('');
    })
    $("#datepicker").nepaliDatePicker({
      dateFormat: "%y %M, %d",
      closeOnDateSelect: true,
    });
    function changeMonth() {
      var selectYear = $("#year");
      var selectMonth = $("#month");
      var nepDate = "{{getDeltaDateFormat(date('Y-m-d'))}}";
      var currentNepYear = parseInt(nepDate.split("-")[0]);
      var currentNepMonth = parseInt(nepDate.split("-")[1]);
      const monthNames = ["Baishak", "Jestha", "Ashad", "Shrawn", "Bhadra", "Ashwin",
      "Kartik", "Mangshir", "Poush", "Magh", "Falgun", "Chaitra"
      ];
      var minTargetDate = "{{$getMinTargetReportDate}}";
      var qntYears = moment().format('YYYY')-moment(minTargetDate).format('YYYY'); 
    
      var selectDays = new Array();
      for (var y = 0; y <= qntYears; y++) {
        let date = new Date(new Date().getFullYear());
        var yearElem = document.createElement("option");
        yearElem.value = currentNepYear
        yearElem.textContent = calendarFunctions.getNepaliNumber(currentNepYear);
        selectYear.append(yearElem);
        currentNepYear--;
      }

      for (var m = 1; m <= 12; m++) {
        let monthNum = currentNepMonth-1;
        let month = monthNames[monthNum];
        var monthElem = document.createElement("option");
        monthElem.value = m;
        monthElem.textContent = calendarFunctions.bsDateFormat("%M", currentNepYear, m, 1);//monthNames[m-1];
        if (currentNepMonth == m) {
          monthElem.selected = 'selected';
        }
        selectMonth.append(monthElem);
      }
      return selectDays;
    }
    $(document).ready(function(){
      changeMonth();
      // initializeDT();

    });
  @endif

  $(document).ready(function(){
    $('#salesmanid').multiselect({
      placeholder: "Select Employee",
      selectAll: true,
      search: true,
    });
    @if(config('settings.ncal')==0)
      $('#datepicker').removeClass('hidden');
    @else 
      $(".select2").select2();
      $("#year,#month").removeClass("hidden");
    @endif
    $(".brandsDiv").removeClass("hidden");
    $("#getReport").removeClass("hidden");

    $("#getReport").on('click',function(){
      var year = $('#year').val();
      var month = $('#month').val();
      var En_startdate = En_enddate = '';
      @if(config('settings.ncal')==1)
        var todaydate = (AD2BS(moment().format('YYYY-MM-DD'))).split('-')[2];
        if(month<10){
          month = '0'+month;
        }
        var npyy = npmm = '';
        npyy = year;npmm =  month;
        var npstartdate = year+'-'+month+'-01';
        var engstartdate = BS2AD(npstartdate);
        var year = (engstartdate.split('-'))[0];
        var month = (engstartdate.split('-'))[1];
        var nepcurmonth_totaldays = NepaliFunctions.GetDaysInBsMonth(year,month);
        var npenddate = npyy+'-'+npmm+'-'+nepcurmonth_totaldays;
        En_startdate = engstartdate;
        En_enddate = BS2AD(npenddate);
      @endif
      var arr = []; var str = '';
      $("#salesmanid :selected").each(function(){
        arr.push(this.value);
        str = arr.join(',');
      });
      var empVal = str;
      if(empVal=="null"){
        empVal = null;
      }
      sessionStorage.setItem('DT_Exp_filters', JSON.stringify({
        "empVal": empVal,
        "year": year,
        "month": month,
      }));

      if(year != '' || month != '')
      {
        $('#expense').DataTable().destroy();
        initializeDT(empVal,year,month,En_startdate,En_enddate);
      }
    })

    var year = $('#year').val();
    var month = $('#month').val();
    var empVal = null;
    var En_startdate = En_enddate = '';
    @if(config('settings.ncal')==1)
      var todaydate = (AD2BS(moment().format('YYYY-MM-DD'))).split('-')[2];
      if(month<10){
        month = '0'+month;
      }
      var npyy = npmm = '';
      npyy = year;npmm =  month;
      var npstartdate = year+'-'+month+'-01';
      var engstartdate = BS2AD(npstartdate);
      var year = (engstartdate.split('-'))[0];
      var month = (engstartdate.split('-'))[1];
      var nepcurmonth_totaldays = NepaliFunctions.GetDaysInBsMonth(year,month);
      var npenddate = npyy+'-'+npmm+'-'+nepcurmonth_totaldays;
      En_startdate = engstartdate;
      En_enddate = BS2AD(npenddate);
    @endif
    initializeDT(empVal,year,month,En_startdate,En_enddate);

    function initializeDT(empVal=null,year,month,En_startdate='',En_enddate=''){
      noorder = valorder = nocoll = valcoll = novis = goldcall = totcall = 'npres';
      var table = $('#expense').DataTable({
        "order": [[ 0, "desc" ]],
        "columnDefs": [
          {
            "orderable": false,
            "targets":[],
          },
          {
            "width": "8%",
            "targets":[0],
          }
        ],
        "processing": true,
        "serverSide": true,
        "stateSave": false,
        "ajax":{
          "url": "{{ domain_route('company.admin.salesmantargetreportdt') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}",
            salesmanID:empVal,
            yearDate: year,
            monthDate: month,
            engstdt : En_startdate,
            engeddt : En_enddate,
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
            checkcolvis(empVal);
          },
        },
        "columns": [
          { "data": "id" },
          { "data": "salesman_name" },
          @if(config('settings.orders')==1)
            { "data": "noof_order",
              render : function(data, type, row) {
                if(data!=''){
                  noorder = 'pres';
                  var actdata = data.split('/');
                  realdata = actdata[0]+'/'+actdata[1];
                  if(actdata.length>=3){
                    return '<span class="err_sp">'+realdata+'</span>'
                  }else{
                    return '<span class="suc_sp">'+realdata+'</span>'
                  }
                }else{
                  noorder = 'npres';
                  return data;
                }
              } 
            },
          @endif
          @if(config('settings.orders')==1)
            { "data": "value_orders" ,
              render : function(data, type, row) {
                if(data!=''){
                  valorder = 'pres';
                  var actdata = data.split('/');
                  realdata = actdata[0]+'/'+actdata[1];
                  if(actdata.length>=3){
                    return '<span class="err_sp">'+realdata+'</span>'
                  }else{
                    return '<span class="suc_sp">'+realdata+'</span>'
                  }
                }else{
                  valorder = 'npres';
                  return data
                }
              }
            },
          @endif
          @if(config('settings.collections')==1)
            { "data": "noof_collections" ,
              render : function(data, type, row) {
                if(data!=''){
                  nocoll = 'pres';
                  var actdata = data.split('/');
                  realdata = actdata[0]+'/'+actdata[1];
                  if(actdata.length>=3){
                    return '<span class="err_sp">'+realdata+'</span>'
                  }else{
                    return '<span class="suc_sp">'+realdata+'</span>'
                  }
                }else{
                  nocoll = 'npres';
                  return data
                }
              }
            },
          @endif
          @if(config('settings.collections')==1)
            { "data": "value_collections" ,
              render : function(data, type, row) {
                if(data!=''){
                  valcoll = 'pres';
                  var actdata = data.split('/');
                  realdata = actdata[0]+'/'+actdata[1];
                  if(actdata.length>=3){
                    return '<span class="err_sp">'+realdata+'</span>'
                  }else{
                    return '<span class="suc_sp">'+realdata+'</span>'
                  }
                }else{
                  valcoll = 'npres';
                  return data
                }
              }
            },
          @endif
          @if(config('settings.visit_module')==1)
            { "data": "noof_visits" ,
              render : function(data, type, row) {
                if(data!=''){
                  novis = 'pres';
                  var actdata = data.split('/');
                  realdata = actdata[0]+'/'+actdata[1];
                  if(actdata.length>=3){
                    return '<span class="err_sp">'+realdata+'</span>'
                  }else{
                    return '<span class="suc_sp">'+realdata+'</span>'
                  }
                }else{
                  novis = 'npres';
                  return data
                }
              }
            },
          @endif
          @if(config('settings.party')==1)
            { "data": "golden_calls" ,
              render : function(data, type, row) {
                if(data!=''){
                  goldcall = 'pres';
                  var actdata = data.split('/');
                  realdata = actdata[0]+'/'+actdata[1];
                  if(actdata.length>=3){
                    return '<span class="err_sp">'+realdata+'</span>'
                  }else{
                    return '<span class="suc_sp">'+realdata+'</span>'
                  }
                }else{
                  goldcall = 'npres';
                  return data
                }
              }
            },
          @endif
          @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
            { "data": "total_calls" ,
              render : function(data, type, row) {
                if(data!=''){
                  totcall = 'pres';
                  var actdata = data.split('/');
                  realdata = actdata[0]+'/'+actdata[1];
                  if(actdata.length>=3){
                    return '<span class="err_sp">'+realdata+'</span>'
                  }else{
                    return '<span class="suc_sp">'+realdata+'</span>'
                  }
                }else{
                  totcall = 'npres';
                  return data
                }
              }
            },
          @endif
         
        ],  
        "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
        "<'row'<'col-xs-6'><'col-xs-6'>>" +
        "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
        buttons: [
            {
                extend: 'colvis',
                order: 'alpha',
                className: 'dropbtn',
                columns:columnCount(),
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    // return "<div class='row'><div class='col-xs-2'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-10 pad-left'>"+title+"</div></div>";
                    return "<div class='flex-prop'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div><div class='label-custom'>"+title+"</div></div>";
                }
            },

            {
                extend: 'excelHtml5',
                title: 'Target Report',
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                footer: true,
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Target Report',
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                orientation:'landscape',
                footer: true,
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
            },
            {
                extend: 'print',
                title: 'Target Report',
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                footer: true,
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
            },
        ]
      });
      table.buttons().container()
      .appendTo('#expenseexports');

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
          data.length = 10;
          dt.one('preDraw', function (e, settings) {
            if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
              var columnsArray = [];
              var visibleColumns = settings.aoColumns.map(setting => {
                                      if(setting.bVisible){
                                        columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                                      } 
                                    })    
              var columns = JSON.stringify(columnsArray);

              customExportAction(config, settings, columns);
            }else{
              oldExportAction(self, e, dt, button, config);
            }
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

    }

    function customExportAction(config, settings, cols){
      $('#exportedData').val(JSON.stringify(settings.json));
      $('#pageTitle').val(config.title);
      $('#columns').val(cols);
      var propertiesArray = [];
      var visibleColumns = settings.aoColumns.map(setting => {
                            if(setting.bVisible) propertiesArray.push(setting.data)
                          })
      var properties = JSON.stringify(propertiesArray);
      $('#properties').val(properties);
      $('#pdf-generate').submit();
    }



  });

  
  function checkcolvis(empval){
    var dts = $('#expense').DataTable();
    var fixCol = 1; var colChk = 0;
    @if(config('settings.orders')==1)
      dts.column(fixCol+colChk+1).visible(true);
      colChk++;
    @endif
    @if(config('settings.orders')==1)
      dts.column(fixCol+colChk+1).visible(true);
      colChk++;
    @endif
    @if(config('settings.collections')==1)
      dts.column(fixCol+colChk+1).visible(true);
      colChk++;
    @endif
    @if(config('settings.collections')==1)
      dts.column(fixCol+colChk+1).visible(true);
      colChk++;
    @endif
    @if(config('settings.visit_module')==1)
      dts.column(fixCol+colChk+1).visible(true);
      colChk++;
    @endif
    @if(config('settings.party')==1)
      dts.column(fixCol+colChk+1).visible(true);
      colChk++;
    @endif
    @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
      dts.column(fixCol+colChk+1).visible(true);
      colChk++;
    @endif
    var tempcount = 0;var chkCC = 'nempty';
    $("#expense > tbody > tr").each(function () {
        tempcount = tempcount+1;
    });
    if($("#expense > tbody > tr > td").hasClass('dataTables_empty')){
      chkCC = 'empty';
    }
    // if(empval!==null){
    if(tempcount==1 && chkCC=='nempty'){
      // var empids = empval.split(',');
      // if(empids.length==1){

        @if(config('settings.orders')==1)
          if(noorder=='npres'){
            var dt = $('#expense').DataTable();
            if(chkCC=='nempty'){
              dt.column(2).visible(false);
            }else{
              var dt = $('#expense').DataTable();
              dt.column(2).visible(true);
            }
          }
        @endif
        @if(config('settings.orders')==1)
          if(valorder=='npres'){
            var dt = $('#expense').DataTable();
            if(chkCC=='nempty'){
              dt.column(3).visible(false);
            }else{
              var dt = $('#expense').DataTable();
              dt.column(3).visible(true);
            }
          }
        @endif
        @if(config('settings.collections')==1)
          if(nocoll=='npres'){
            var dt = $('#expense').DataTable();
            if(chkCC=='nempty'){
              dt.column(4).visible(false);
            }else{
              var dt = $('#expense').DataTable();
              dt.column(4).visible(true);
            }
          }
        @endif
        @if(config('settings.collections')==1)
          if(valcoll=='npres'){
            var dt = $('#expense').DataTable();
            if(chkCC=='nempty'){
              dt.column(5).visible(false);
            }else{
              var dt = $('#expense').DataTable();
              dt.column(5).visible(true);
            }
          }
        @endif
        @if(config('settings.visit_module')==1)
          if(novis=='npres'){
            var dt = $('#expense').DataTable();
            if(chkCC=='nempty'){
              dt.column(6).visible(false);
            }else{
              var dt = $('#expense').DataTable();
              dt.column(6).visible(true);
            }
          }
        @endif
        @if(config('settings.party')==1)
          if(goldcall=='npres'){
            var dt = $('#expense').DataTable();
            if(chkCC=='nempty'){
              dt.column(7).visible(false);
            }else{
              var dt = $('#expense').DataTable();
              dt.column(7).visible(true);
            }
          }
        @endif
        @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
          if(totcall=='npres'){
            var dt = $('#expense').DataTable();
            if(chkCC=='nempty'){
              dt.column(8).visible(false);
            }else{
              var dt = $('#expense').DataTable();
              dt.column(8).visible(true);
            }
          }
        @endif
      // }
    }

  }



  $(document).on('click','.buttons-columnVisibility',function(){
      if($(this).hasClass('active')){
          $(this).find('input').first().prop('checked',true);
          console.log($(this).find('input').first().prop('checked'));
      }else{
          $(this).find('input').first().prop('checked',false);
          console.log($(this).find('input').first().prop('checked'));
      }
  });

  $(document).on('click','.buttons-colvis',function(e){
      var filterBox = $('.dt-button-collection');
      filterBox.find('li').each(function(k,v){
          if($(v).hasClass('active')){
              $(v).find('input').first().prop('checked',true);
          }else{
              $(v).find('input').first().prop('checked',false);
          }
      });
  });



  function columnCount(){
    var CCount = 2;
    @if(config('settings.orders')==1)
      CCount++;    
    @endif
    @if(config('settings.orders')==1)
      CCount++;
    @endif
    @if(config('settings.collections')==1)
      CCount++; 
    @endif
    @if(config('settings.collections')==1)
      CCount++;
    @endif
    @if(config('settings.visit_module')==1)
      CCount++;
    @endif
    @if(config('settings.party')==1)
      CCount++;
    @endif
    @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
      CCount++;
    @endif
    var CColumnsVal = [];
    for(var jk=0;jk<CCount;jk++){
      CColumnsVal.push(jk);
    }
    return CColumnsVal;
  }
</script>

@endsection