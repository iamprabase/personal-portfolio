@extends('layouts.company')
@section('title', 'Monthly Attendance Report')

@section('stylesheets')
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/dist/css/nepaliDatePicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/dist/css/delta.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<style>
  .dt-buttons.btn-group {
    width: 40%;
    left: 10px;
  }

</style>
@endsection

@section('content')

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div id="mainBox" class="box box-loader">
        <div class="box-header">
          <div class="row">
            <div class="col-xs-6">
              <h3 class="box-title">Monthly Attendance Report (<span id="mYear"> </span>)</h3>
            </div>
            <div class="col-xs-6">
              <div class="row">
                @if(config('settings.ncal')==0)
                <div class="col-xs-4">
                  <div class="form-group">
                    <label>Select Month and Year</label>
                    @if(config('settings.ncal')==0)
                    <input type="text" class="form-control form-control-1 input-xs" id="datepicker">
                    @else
                    <input type="text" class="form-control form-control-1 input-xs" id="ndatepicker"
                      style="background-color: #fff">
                    @endif
                    <input type="hidden" id="year">
                    <input type="hidden" id="month">
                  </div>
                </div>
                @else
                <div class="col-xs-4">
                  <label>Select Year</label>
                  <select class="select2" id="year"
                    style="background: #fff; cursor: pointer; width: 100%;position: absolute;"></select>
                </div>
                <div class="col-xs-4">
                  <label>Select Month</label>
                  <select class="select2" id="month"
                    style="background: #fff; cursor: pointer; width: 100%;position: absolute;"></select>
                </div>
                <input type="hidden" id="nepDate" value="{{$getNepDate}}">
                @endif
                <div class="col-xs-4">
                  <button class="btn btn-primary" id="getReport" style="width: 100%;margin-top: 25px;">
                    <span><i class="fa fa-book"></i> View Report</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div id="loader1">
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <div class="container-fluid" style="width:auto;">
            <div class="tablediv table-responsive">

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
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
@if(config('settings.ncal')==1)
{{-- <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script> --}}
<script src="{{asset('assets/dist/js/jquery.nepaliDatePicker.min.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif

<script type="text/javascript">
  var minDate = "{{$getMinAttendanceDate}}"; 
  $('#loader1').attr('hidden','hidden');
  $('#mainBox').removeClass('box-loader');
  $('.select2').select2();
  @if(config('settings.ncal')==0)
    $('document').ready(function(){
      var pickedYear = new Date().getFullYear();
      var pickedMonth = new Date().getMonth() + 1;
      $('#year').val(pickedYear);
      $('#month').val(pickedMonth);
      $('#getReport').click();
    })
    $("#datepicker").datepicker( {
      format: "MM-yyyy",
      viewMode: "years", 
      minViewMode: "months",
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
    $("#ndatepicker").on('focus', function(){
      $("#ndatepicker").val('');
    })
    $("#ndatepicker").nepaliDatePicker({
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
      var minAttendance = "{{$getMinAttendanceDate}}";
      var qntYears = moment().format('YYYY')-moment(minAttendance).format('YYYY');

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
    $('document').ready(function(){
      changeMonth();
      submitRequest();
    });
  @endif

  $('#getReport').on('click', function () {
    submitRequest();
  });

  function submitRequest(){
    var formatted_date = [];
    @if(config('settings.ncal')==1)
      var getyear = 0;
      var getmonth = 0;
      var selNepYear = parseInt($('#year').val());
      var selNepMonth = parseInt($('#month').val());
      var getNumDays = calendarFunctions.getBsMonthDays(selNepYear, selNepMonth);
      for(var count=1; count<=getNumDays; count++){
        var engDate = moment(calendarFunctions.getAdDateByBsDate(selNepYear, selNepMonth, count)).format('YYYY-MM-DD');
        formatted_date.push(engDate);
      }
      $('#nepDate').val($('#month').select2('data')[0].text+ ' ' +$('#year option:selected').text());
    @else
      var getyear = $('#year').val();
      var getmonth = $('#month').val();
      getmonth_S = getmonth.toString();
      if (getmonth < 10) {
          getmonth_S = 0 + getmonth_S;
      }
      var getdays = new Date(getyear, getmonth, 0).getDate();
      for (var i = 1; i <= getdays; i++) {
          var date_formatted = getyear + "-" + getmonth_S + "-" + i.toString();
          date_formatted = new Date(date_formatted);
          var smnth = ("0" + (date_formatted.getMonth() + 1)).slice(-2);
          var sday = ("0" + date_formatted.getDate()).slice(-2);
          var test = [date_formatted.getFullYear(), smnth, sday].join("-");
          formatted_date.push(test);
      }
    @endif
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: "POST",
      url: "{{ domain_route('company.admin.getmonthlyattendancereport') }}",
      data:
        {
            format_date: formatted_date,
            year: getyear,
            month: getmonth,
        },
      beforeSend: function (url, data) {
          $('#mainBox').addClass('box-loader');
          $('#getReport').attr('disabled',true);
          $('#loader1').removeAttr('hidden');
      },
      success: function (data) {
          $('.tablediv').html(data);
          @if(config('settings.ncal')==0)
          $('#mYear').html('<b>'+$('#datepicker').val() +'</b>');
          @else
          $('#mYear').html('<b>'+ $('#nepDate').val() +'</b>');
          @endif
          $('#loader1').attr('hidden','hidden');
          $('#mainBox').removeClass('box-loader');
          $('#getReport').attr('disabled',false);
          
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert("No records found. ");
      },
    });
  }
</script>
@endsection