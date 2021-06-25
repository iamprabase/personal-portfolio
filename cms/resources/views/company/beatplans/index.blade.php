  @extends('layouts.company')
  @section('title', 'BeatPlan')
  @section('stylesheets')
    <link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <style>
      .select2-selection__placeholder{
        color: #000000 !important;
      }

      .text-center {
        left: 35%;
        position: absolute;
      }

      .close{
      font-size: 30px;
      color: #080808;
      opacity: 1;
    }
    </style>
  @endsection

  @section('content')
  <section class="content">
    <div class="box box-default box-loader" id="mainBox">
      <div class="box-header with-border">
        <div class="col-xs-3 pull-left">
          <h3 class="box-title">Plan Beats</h3>
        </div>
        <div class="col-xs-4 text-center">
          <select class="select2 employee_list" id="emp">
            <option value=""></option>
            @foreach($employees as $employee)
              <option value={{$employee->id}}>{{$employee->name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-xs-3 pull-right">
          <a href="javascript:void(0)" class="pull-right btn btn-primary pull-right show-create">
            Create New
          </a>
        </div>
      </div>
      <div class="box-body">
        @if(config('settings.ncal')==0)
          <div id="calendar"></div>
        @else
          <div id="ncalendar" class="fc fc-unthemed fc-ltr">
              <div class="fc-toolbar fc-header-toolbar">
                <div class="fc-left">
                  <div class="fc-button-group">
                    <button type="button" class="fc-prev-button fc-button fc-state-default fc-corner-left" aria-label="prev">
                      <span class="fc-icon fc-icon-left-single-arrow"></span>
                    </button>
                    <button type="button" class="fc-next-button fc-button fc-state-default fc-corner-right" aria-label="next">
                      <span class="fc-icon fc-icon-right-single-arrow"></span>
                    </button>
                  </div>
                  <button type="button" id="todayMonth" class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right">today</button>
                </div>
                <div class="fc-right">
                  <div class="fc-button-group">
                    <button type="button" class="fc-month-button fc-button fc-state-default fc-corner-left fc-state-active">month</button>
                    <button type="button" class="fc-agendaWeek-button fc-button fc-state-default">week</button>
                    <button type="button" class="fc-agendaDay-button fc-button fc-state-default fc-corner-right">day</button>
                  </div>
                </div>
                <div class="fc-center">
                  <h2 id="monthYear"></h2>
                  <input type="text" id="calNepaliYear" hidden>
                  <input type="text" id="calNepaliMonth" hidden>
                </div>
                <div class="fc-clear"></div>
              </div>
              <div class="fc-view-container" style="">
                <div class="fc-view fc-month-view fc-basic-view" style="">
                  <table class="">
                    <thead class="fc-head">
                      <tr>
                        <td class="fc-head-container fc-widget-header">
                          <div class="fc-row fc-widget-header">
                            <table class="">
                              <thead>
                                <tr>
                                  <th class="fc-day-header fc-widget-header fc-sun">
                                    <span>आइतवार</span>
                                  </th>
                                  <th class="fc-day-header fc-widget-header fc-mon">
                                    <span>सोमवार</span>
                                  </th>
                                  <th class="fc-day-header fc-widget-header fc-tue">
                                    <span>मङ्गलवार</span>
                                  </th>
                                  <th class="fc-day-header fc-widget-header fc-wed">
                                    <span>बुधवार</span>
                                  </th>
                                  <th class="fc-day-header fc-widget-header fc-thu">
                                    <span>बिहिवार</span>
                                  </th>
                                  <th class="fc-day-header fc-widget-header fc-fri">
                                    <span>शुक्रवार</span></th>
                                  <th class="fc-day-header fc-widget-header fc-sat">
                                    <span>शनिवार</span>
                                  </th>
                                </tr>
                              </thead>
                            </table>
                          </div>
                        </td>
                      </tr>
                    </thead>
                    <tbody class="fc-body">
                      <tr>
                        <td class="fc-widget-content">
                          <div class="fc-scroller fc-day-grid-container" style="overflow: hidden; height: 576px;">
                            <div class="fc-day-grid fc-unselectable" id="calendarBody">
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
          </div>
        @endif
        <div id="loader1">
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
      </div>

    </div>
  </section>
  @endsection

  @section('scripts')
  <script src="{{ asset('assets/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
  @if(config('settings.ncal')==1)
    <script src="{{ asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
    <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  @endif
<script>
  $('#loader1').attr('hidden','hidden');
  $('#mainBox').removeClass('box-loader');
  $('.select2').select2({
    'placeholder': "Select Employee",
  });

  @if(config('settings.ncal')==1)
    $(function(){
      var today = moment().format('YYYY-MM-DD');
      var currentYear = moment().year();
      var currentMonth = moment().month()+1;
      var currentDay = moment().date();
      var Weekday = moment().day();

      var NepaliCurrentDate = AD2BS(today);

      var nepaliDateData = NepaliCurrentDate.split('-');
      var nepaliCurrentYear = nepaliDateData[0];
      var nepaliCurrentMonth = nepaliDateData[1];
      var nepaliCurrentDay = nepaliDateData[2];

      $('.fc-next-button').click(function(e){
        e.preventDefault(e);
        var getMonth = parseInt($('#calNepaliMonth').val())+1;
        var getYear = parseInt($('#calNepaliYear').val());
        if(getMonth>12){
          getMonth = 1;
          getYear = getYear+1;
        }
        if(getMonth<10){
          getMonth = '0'+getMonth;
        }
        var firstEnd = getFirstDateEndDate(getYear,getMonth);
        engFirstDate = BS2AD(firstEnd[0]);
        engLastDate = BS2AD(firstEnd[11]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{domain_route('company.admin.beatplans.getCalendar')}}",
            type: "POST",
            data: {
                '_token': '{{csrf_token()}}',
                'getMonth': getMonth,
                'getYear': getYear,
                'engFirstDate': engFirstDate,
                'engLastDate': engLastDate,
                'employee_id': null,
            },
            success: function (data) {
                $('#calNepaliYear').val(data['year']);
                $('#calNepaliMonth').val(data['month']);
                var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                if(!(data.holidays==null)){
                  var key = "holidays";
                  $('#calrowbody1').html(populateBeatPageEvent(key,firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').html(populateBeatPageEvent(key,firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').html(populateBeatPageEvent(key,firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').html(populateBeatPageEvent(key,firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').html(populateBeatPageEvent(key,firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').html(populateBeatPageEvent(key,firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                }
            }
        });
      });
      $('.fc-prev-button').click(function(e){
        e.preventDefault(e);
        getMonth = parseInt($('#calNepaliMonth').val())-1;
        getYear = parseInt($('#calNepaliYear').val());
        if(getMonth<1){
          getMonth = 12;
          getYear = getYear-1;
        }
        if(getMonth<10){
          getMonth = '0'+getMonth;
        }
        var firstEnd = getFirstDateEndDate(getYear,getMonth);
        engFirstDate = BS2AD(firstEnd[0]);
        engLastDate = BS2AD(firstEnd[11]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{domain_route('company.admin.beatplans.getCalendar')}}",
            type: "POST",
            data: {
                '_token': '{{csrf_token()}}',
                'getMonth': getMonth,
                'getYear': getYear,
                'engFirstDate': engFirstDate,
                'engLastDate': engLastDate,
                'employee_id': null,
            },
            success: function (data) {
                $('#calNepaliYear').val(data['year']);
                $('#calNepaliMonth').val(data['month']);
                var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                if(!(data.holidays==null)){
                  var key = "holidays";
                  $('#calrowbody1').html(populateBeatPageEvent(key,firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').html(populateBeatPageEvent(key,firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').html(populateBeatPageEvent(key,firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').html(populateBeatPageEvent(key,firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').html(populateBeatPageEvent(key,firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').html(populateBeatPageEvent(key,firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                }
            }
        });
      });

      $('#todayMonth').click(function(e){
        e.preventDefault(e);
        getMonth = nepaliCurrentMonth;
        getYear = nepaliCurrentYear;
        if(getMonth<1){
          getMonth = 12;
          getYear = getYear-1;
        }
        var firstEnd = getFirstDateEndDate(getYear,getMonth);
        engFirstDate = BS2AD(firstEnd[0]);
        engLastDate = BS2AD(firstEnd[11]);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{domain_route('company.admin.beatplans.getCalendar')}}",
          type: "POST",
          data: {
            '_token': '{{csrf_token()}}',
            'getMonth': getMonth,
            'getYear': getYear,
            'engFirstDate': engFirstDate,
            'engLastDate': engLastDate,
            'employee_id': null,
          },
          success: function (data) {
            $('#calNepaliYear').val(data['year']);
            $('#calNepaliMonth').val(data['month']);
            var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
            $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
            $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
            if(!(data.holidays==null)){
              var key = "holidays";
              $('#calrowbody1').html(populateBeatPageEvent(key,firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
              $('#calrowbody2').html(populateBeatPageEvent(key,firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
              $('#calrowbody3').html(populateBeatPageEvent(key,firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
              $('#calrowbody4').html(populateBeatPageEvent(key,firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
              $('#calrowbody5').html(populateBeatPageEvent(key,firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
              $('#calrowbody6').html(populateBeatPageEvent(key,firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
            }
          }
        });
      });
      $('#todayMonth').click();
    });
  @else
    $(function () {
      $('.fromdate').datepicker({
        startDate: new Date(),
        format:'yyyy-mm-dd',
        autoclose:true,
      });    
      $('#calendar').fullCalendar({
        timeFormat: 'h:mmA',
        header    : {
          left  : 'prev,next today',
          center: 'title',
          // right : 'month,agendaWeek,agendaDay'
        },
        displayEventTime : false,
        timeFormat: 'h:mmA',

        buttonText: {
          today: 'This Month',
          month: 'month',
          // week : 'week',
          // day  : 'day'
        },
        dayClick: function(date, jsEvent, view) {
          var current_date = moment().format('YYYY-MM-DD');
          if(date.format() < current_date) {
            alert("Cannot create plans for past date.");
            return false;
          }else{
            alert("Please select an employee.");
          }
        },
        eventRender: function (event, element, view) { 
        },
        events    : [
          @foreach($holidays as $holiday)
          {

            title          :     '{{$holiday->name}}',
            start          :     '{{$holiday->start_date}}',
            end            :     '{{$data1['nextday_end'][$holiday->id]}}',
            finish         :     '{{$holiday->end_date}}',
            id             :      '{{$holiday->id}}',
            allDay         :      true,
            backgroundColor:     '#f58641', 
            borderColor    :     '#f58641', 
          },
          @endforeach
        ],
        editable  : false,
        droppable : false,
      })
    });
  @endif

  // when click Create without selecting employee
  $('.show-create').click(function(){
    if($('.employee_list').val() == ""){
      alert("Please select an employee.");
    }
  });

  // when selecting an employee redirect to respective employee pages
  $('.employee_list').on('select2:select',function(){
    let sel_employee_id = $('.employee_list').val();
    window.location.href = "beatplan/"+sel_employee_id;
  });
</script>
@endsection
