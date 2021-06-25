  @extends('layouts.company')
  @section('title', 'BeatPlan')
  @section('stylesheets')
    <link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
    <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}"/>
    @if(config('settings.ncal')==1)
      <link rel="stylesheet"
          href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
    @else
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    @endif
  <style>
    .fc-day-grid-event.fc-h-event.fc-event.fc-start.fc-end{
      cursor: pointer;
    }
    li.disabled.active a{
      color: #080808;
      background: none;
    }
    .fc-myCustomButton-button{
      height: 40px !important;
      color: white;
      background: #287676;
    }
    .fc-myCustomButton-button.fc-state-hover{
      background-color: #649696 !important;
    }

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
        <div class="col-sm-5 pull-left">
          <h3 class="box-title">
            <span id="appendName"></span>
          </h3>
        </div>
        <div class="col-xs-4 text-center">
          <select class="select2 employee_list">
            <option value=""></option>
            @foreach($employees as $employee)
              <option value={{$employee->id}}>{{$employee->name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-xs-3 pull-right">
          <a href="javascript:void(0)" class="pull-right btn btn-primary show-create">
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
                <button type="button" id="todayMonth"
                  class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right">today</button>
              </div>
              <div class="fc-right">
                <div class="fc-button-group">
                  <button type="button"
                    class="fc-month-button fc-button fc-state-default fc-corner-left fc-state-active">month</button>
                  <button type="button" class="fc-agendaWeek-button fc-button fc-state-default hidden">week</button>
                  <button type="button" class="fc-agendaDay-button fc-button fc-state-default fc-corner-right hidden">day</button>
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
  <div>
    @include('company.beatplans.create')
  </div>
  <div>
    @include('company.beatplans.delete')
  </div>
  <div id="edit_modal_single" class="editmodal modal fade">
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('assets/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
  <script src="{{asset('assets/bower_components/bootstrap-timepicker/js/bootstrap-timepicker.js')}}"></script>
  <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
  @if(config('settings.ncal')==1)
    <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
    <script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  @else
    <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  @endif
<script>

  $('document').ready(function(){
    var todayDate = "{{getDeltaDateFormat(date('Y-m-d'))}}";
    $('#loader1').attr('hidden','hidden');
    $('#mainBox').removeClass('box-loader');

    // On Create button
    $('.show-create').click(function(){     
      $('#AddNewBeatPlan')[0].reset();
      var getEmployeeName = $('.employee_list').val();
      $('#AddNewBeatPlan').find('#employee_list').val(getEmployeeName);
      $('#AddNewBeatPlan').find('#employee_list').trigger('change');
      $('.todate').prop('disabled','true');
      $('#exampleModalCenter').modal();
      @if(config('settings.ncal')==1)
        $("#start_date1").val(todayDate);
      @endif
    });

    $('.select2').select2({
      placeholder: 'Select Employee',
    });

    $('.multibeat').multiselect({
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      enableFullValueFiltering: true,
      enableClickableOptGroups: true,
      includeSelectAllOption: true,	
      enableCollapsibleOptGroups : true,
      selectAllNumber: false,
      nonSelectedText:"Assign Parties",
    });

    @if(config('settings.ncal')==0)
      $('.fromdate').datepicker({
        startDate: new Date(),
        format:'yyyy-mm-dd',
        autoclose:true,
      });
      $('#calendar').fullCalendar({
        customButtons: {
          myCustomButton: {
            text: 'Update this month\'s plans',
            click: function(date,start,jsEvent, view) {
              let year = moment($('#calendar').fullCalendar('getDate')).format('Y');
              let month = moment($('#calendar').fullCalendar('getDate')).format('MM');
              let empId = $('.employee_list').val();
              displayEvents(empId,year, month);
            },
          }
        },
        header    : {
          left  : 'prev,next today',
          center: 'title',
          right: 'myCustomButton',
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
        dayClick: function(date,start,jsEvent, view) {
          let dateEvent = IsDateHasEvent(date);
          let current_date = moment().format('YYYY-MM-DD');
          var getEmployeeName = $('.employee_list').val();
          if(dateEvent.length!=0){
            if(dateEvent[0]._id=="beats"){
              if($('.employee_list').val() != ""){
                if(current_date <= date.format() && dateEvent==0) {
                  $('#AddNewBeatPlan')[0].reset();
                  @if(config('settings.ncal')==0)
                    $('.fromdate').datepicker('destroy');  
                    $('.fromdate').datepicker({
                      startDate: new Date(),
                      format:'yyyy-mm-dd',
                      autoclose:true,
                      startDate: current_date,
                    });
                    $('.fromdate').datepicker('setDate',date.format());
                  @else
                    $('.fromdate').nepaliDatePicker({
                      ndpEnglishInput: 'englishDate',
                      disableBefore: moment(getNepaliDate()).format('MM/D/Y'),
                    });
                  @endif
                  $('#exampleModalCenter').modal();     
                  $('#AddNewBeatPlan').find('#employee_list').val(getEmployeeName);
                  $('#AddNewBeatPlan').find('#employee_list').trigger('change');
                }else if(dateEvent.length>0){
                  let id = dateEvent[0].employee;
                  let beat_id = dateEvent[0].beatvplan_id;
                  let fetch_id = dateEvent[0].id;
                  var url = "{{domain_route('company.admin.beatplan.editSingle', [':employee',':employeeid'])}}";
                  let get_url = url.replace(':employee',dateEvent[0].employee);
                  get_url = get_url.replace(':employeeid',id);
                  $.ajax({
                      headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: get_url,
                      type: "GET",
                      data: {
                        id: id,
                        beat_id: beat_id,
                        fetch_id: fetch_id
                      },
                      beforeSend:function(data,id){
                        $('#mainBox').addClass('box-loader');
                        $('#loader1').removeAttr('hidden');
                      },
                      success:function(data) {
                        $('#edit_modal_single').html(data);
                        $('#mainBox').removeClass('box-loader');
                        $('#loader1').attr('hidden','hidden');
                        $('#edit_modal_single').modal();
                        $('#edit_modal_single').find('#employee_list').val($('#edit_modal_single').find('#employee_list').data("id")).trigger('change');   
                      },
                      error:function(xhr){
                      }
                  });
                }
              }
            }else if(dateEvent[0]._id!="beats"){
              if(current_date <= date.format() && dateEvent==0) {
                @if(config('settings.ncal')==0)
                  $('.fromdate').datepicker('destroy');  
                  $('.fromdate').datepicker({
                    startDate: new Date(),
                    format:'yyyy-mm-dd',
                    autoclose:true,
                    startDate: current_date,
                  });
                  $('.fromdate').datepicker('setDate',date.format());
                @else
                  $('.fromdate').nepaliDatePicker({
                    ndpEnglishInput: 'englishDate',
                    disableBefore: moment(getNepaliDate()).format('MM/D/Y'),
                  });
                @endif
                $('#AddNewBeatPlan').find('#employee_list').val(getEmployeeName);
                $('#AddNewBeatPlan').find('#employee_list').trigger('change');
                $('#exampleModalCenter').modal(); 
              }else if(current_date <= date.format() && dateEvent[0]._id!="beats") {
                @if(config('settings.ncal')==0)
                  $('.fromdate').datepicker('destroy');  
                  $('.fromdate').datepicker({
                    startDate: new Date(),
                    format:'yyyy-mm-dd',
                    autoclose:true,
                    startDate: current_date,
                  });
                  $('.fromdate').datepicker('setDate',date.format());
                @else
                  $('.fromdate').nepaliDatePicker({
                    ndpEnglishInput: 'englishDate',
                    disableBefore: moment(getNepaliDate()).format('MM/D/Y'),
                  });
                @endif
                $('#AddNewBeatPlan').find('#employee_list').val(getEmployeeName);
                $('#AddNewBeatPlan').find('#employee_list').trigger('change');
                $('#exampleModalCenter').modal(); 
              }else{
                alert("Cannot create plans for past date.");  
              }
            }
          }else if(dateEvent.length==0){
            if(current_date <= date.format()) {
              @if(config('settings.ncal')==0)
                $('.fromdate').datepicker('destroy');  
                $('.fromdate').datepicker({
                  startDate: new Date(),
                  format:'yyyy-mm-dd',
                  autoclose:true,
                  startDate: current_date,
                });
                $('.fromdate').datepicker('setDate',date.format());
              @else
                $('.fromdate').nepaliDatePicker({
                  ndpEnglishInput: 'englishDate',
                  disableBefore: moment(getNepaliDate()).format('MM/D/Y'),
                });
              @endif
              $('#AddNewBeatPlan').find('#employee_list').val(getEmployeeName);
              $('#AddNewBeatPlan').find('#employee_list').trigger('change');
              $('#exampleModalCenter').modal(); 
            }else if(current_date > date.format()){
              alert("Cannot create plans for past date.");  
            }
          }
        },
        eventRender: function (event, element,date, view) {
          let current_date = moment().format('YYYY-MM-DD');
          // Ignore Holidays Events
          if(event._id == "beats"){
            element.popover({
              title: event.title,
              trigger: 'hover',
              placement: 'top',
              container: 'body'
            });
            if(current_date > event.start._i){
              element.click(function(){
                let id = event.employee;
                let beat_id = event.beatvplan_id;
                let fetch_id = event.id;

                var url = "{{domain_route('company.admin.beatplan.editSingle', [':employee',':employeeid'])}}";
                let get_url = url.replace(':employee',event.employee);
                get_url = get_url.replace(':employeeid',id);

                $.ajax({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: get_url,
                  type: "GET",
                  data: {
                    id: id,
                    beat_id: beat_id,
                    fetch_id: fetch_id
                  },
                  beforeSend:function(data,id){
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden');
                  },
                  success:function(data) {
                    $('#edit_modal_single').html(data);
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden','hidden');
                    $('#edit_modal_single').modal();
                    $('#edit_modal_single').find('#employee_list').val($('#edit_modal_single').find('#employee_list').data("id")).trigger('change');   
                  },
                  error:function(xhr){
                  }
                });
              });
            }else{
              let ed = document.createElement('i');
              ed.className = 'fa';
              ed.classList.add("fa-edit");
              ed.classList.add("btn");
              ed.classList.add("grey-mint");
              ed.classList.add("btn-xs");
              
              ed.addEventListener("click", function () {

                let id = event.employee;
                let beat_id = event.beatvplan_id;
                let fetch_id = event.id;

                var url = "{{domain_route('company.admin.beatplan.editSingle', [':employee',':employeeid'])}}";
                let get_url = url.replace(':employee',event.employee);
                get_url = get_url.replace(':employeeid',id);

                $.ajax({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: get_url,
                  type: "GET",
                  data: {
                    id: id,
                    beat_id: beat_id,
                    fetch_id: fetch_id
                  },
                  beforeSend:function(data,id){
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden');
                  },
                  success:function(data) {
                    $('#edit_modal_single').html(data);
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden','hidden');
                    $('#edit_modal_single').modal();
                    $('#edit_modal_single').find('#employee_list').val($('#edit_modal_single').find('#employee_list').data("id")).trigger('change');   
                  },
                  error:function(xhr){
                  }
                });
              });
              element.find('div.fc-content span.fc-title').prepend(ed);
              element.find('div.fc-content div.fc-title').prepend(ed);
              var del = document.createElement('i');
              del.className = 'fa';
              del.classList.add("fa-trash");
              del.classList.add("btn");
              del.classList.add("grey-mint");
              del.classList.add("btn-xs");
              del.addEventListener("click", function () {
                $('#del_id').val(event.id);
                $('#get_id').val(event.beatvplan_id);
                $('#empl_id').val(event.employee);
                $('#del_event_modal').modal('show');
              });
              element.find('div.fc-content span.fc-title').prepend(del);
              element.find('div.fc-content div.fc-title').prepend(del);
            }
          }
        },
        events    : [
          @foreach($beats_planned as $beat_planned)
          {

            id             :     '{{$beat_planned->id}}',

            _id            :      'beats',

            beatvplan_id   :     '{{$beat_planned->beatvplan_id}}',

            title          :     "{!!$beat_planned->title!!}", 

            @if($beat_planned->plan_from_time !="")

            start          :     '{{$beat_planned->plandate}}' + ' ' + '{{$beat_planned->plan_from_time}}',

            @else
            
            start          :     '{{$beat_planned->plandate}}',

            @endif

            @if($beat_planned->plan_to_time !="")

            end            :     '{{$beat_planned->plandate}}' + ' ' + '{{$beat_planned->plan_to_time}}',

            @else
            
            end            :     '{{$beat_planned->plandate}}',

            @endif

            finish         :     '{{$beat_planned->plandate}}',

            employee       :     '{{$beat_planned->employee_id}}',

            plan_from_time :     '{{$beat_planned->plan_from_time}}',

            plan_to_time   :     '{{$beat_planned->plan_to_time}}',

            beat_id        :     '{{$beat_planned->beat_id}}',

            party_name     :     '{{$beat_planned->client_id}}',

            remark         :     '{{$beat_planned->remark}}',

            status         :     '{{$beat_planned->status}}',

            @if(isset($beat_planned->plan_from_time) || isset($beat_planned->plan_to_time))
              allDay         :      false,
            @else
              allDay         :      true,
            @endif
            backgroundColor:     '#de3535', //red
            borderColor    :     '#de3535', //red
          },
          @endforeach

          @foreach($holidays as $holiday)
          {

            title          :     "{!!$holiday->name!!}",

            start          :     '{{$holiday->start_date}}',

            end            :     '{{$data1['nextday_end'][$holiday->id]}}',

            finish         :     '{{$holiday->end_date}}',

            id             :      '{{$holiday->id}}',
            allDay         :      true,

            backgroundColor:     '#f58641', //green
            borderColor    :     '#f58641', //green

          },
          @endforeach
        ],
        eventClick: function(calEvent, jsEvent, view) {
          $(this).css('border-color', 'green');
        },
        editable  : false,
        droppable : false,
      });

      openModalOnLoad();
      //Open modal on date Box Click
      function IsDateHasEvent(date) {
        var dt = moment(date).format("YYYY-MM-DD");
        var allEvents = [];
        allEvents = $('#calendar').fullCalendar('clientEvents');
        var event = $.grep(allEvents, function (v) {
          return +v.start === +dt;
        });
        return event;
      }
      //Decides whether or not trigger modal on page load
      function openModalOnLoad(){
        let eventClients = $('#calendar').fullCalendar('clientEvents');
        let beatEventExists = (eventClients._id=="beats");

        if(eventClients != "" && beatEventExists==true){
          $('.employee_list').val(eventClients[0].employee).trigger('change');
        }else if(beatEventExists==false){
          let pathName = window.location.pathname;
          let empId = pathName.match("[0-9]+$");
          $('.employee_list').val(empId).trigger('change');
        }
        let emp_name = $('.employee_list').select2("data")[0].text;
        
        $('#appendName').text(emp_name);
        beats = [];
        $.each(eventClients, function(i,v){
          beats.push(v._id);
        });
        if(beats.includes("beats")==false){
          let empId = window.location.pathname.match("[0-9]+$");
          $('#employee_list').val(empId).trigger('change');
          $('#exampleModalCenter').modal();   
        } 
      }
      //Display this month plan
      function displayEvents(empId, year, month){
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{domain_route('company.admin.beatplan.monthplans')}}",
          type: "GET",
          data: {
            empId: empId,
            year: year,
            month: month
          },
          beforeSend:function(data,id){
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
          },
          success:function(data) {
            if(data==1){
              alert("Cannot update past plan for this month.");
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden','hidden');
            }else{
              $('#edit_modal_single').html(data);
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden','hidden');
              $('#edit_modal_single').modal();
              $('#edit_modal_single').find('#employee_list').val($('#edit_modal_single').find('#employee_list').data("id")).trigger('change');   
            }
          },
          error:function(xhr){
          }
        });
      }
    @else
      $(function(){
        var today = moment().format('YYYY-MM-DD');
        var currentYear = moment().year();
        var currentMonth = moment().month()+1;
        var currentDay = moment().date();
        var Weekday = moment().day();
        var employee_id = "{{$employee_id}}";
        $('.employee_list').val(employee_id).trigger('change');
        let emp_name = $('.employee_list').select2("data")[0].text;
        
        $('#appendName').text(emp_name);
        var NepaliCurrentDate = AD2BS(today);

        var nepaliDateData = NepaliCurrentDate.split('-');
        var nepaliCurrentYear = nepaliDateData[0];
        var nepaliCurrentMonth = nepaliDateData[1];
        var nepaliCurrentDay = nepaliDateData[2];

        var beatPlanDateArray = new Array();
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
                  'employee_id': employee_id,
              },
              success: function (data) {
                beatPlanDateArray = new Array();
                $('#calNepaliYear').val(data['year']);
                $('#calNepaliMonth').val(data['month']);
                var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                // $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                // $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                // $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                // $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                // $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                // $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                if(!(data.holidays==null)){
                var key = "holidays";
                $('#calrowbody1').append(populateBeatPageEvent(key,firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                $('#calrowbody2').append(populateBeatPageEvent(key,firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                $('#calrowbody3').append(populateBeatPageEvent(key,firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                $('#calrowbody4').append(populateBeatPageEvent(key,firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                $('#calrowbody5').append(populateBeatPageEvent(key,firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                $('#calrowbody6').append(populateBeatPageEvent(key,firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              }
              if(!(data.beatplans==null)){
                var key = "beatplans";
                beatPlanDateArray = data.beatplans.map(function (el) { return AD2BS(el.start_date); });
                $('#calrowbody1').append(populateBeatPageEvent(key, firstEnd[0],firstEnd[1],data['beatplans'],data['beatplans'].length));
                $('#calrowbody2').append(populateBeatPageEvent(key, firstEnd[2],firstEnd[3],data['beatplans'],data['beatplans'].length));
                $('#calrowbody3').append(populateBeatPageEvent(key, firstEnd[4],firstEnd[5],data['beatplans'],data['beatplans'].length));
                $('#calrowbody4').append(populateBeatPageEvent(key, firstEnd[6],firstEnd[7],data['beatplans'],data['beatplans'].length));
                $('#calrowbody5').append(populateBeatPageEvent(key, firstEnd[8],firstEnd[9],data['beatplans'],data['beatplans'].length));
                $('#calrowbody6').append(populateBeatPageEvent(key, firstEnd[10],firstEnd[11],data['beatplans'],data['beatplans'].length));
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
                  'employee_id': employee_id,
              },
              success: function (data) {
                beatPlanDateArray = new Array();
                $('#calNepaliYear').val(data['year']);
                $('#calNepaliMonth').val(data['month']);
                var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                if(!(data.holidays==null)){
                  var key = "holidays";
                  $('#calrowbody1').append(populateBeatPageEvent(key,firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').append(populateBeatPageEvent(key,firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').append(populateBeatPageEvent(key,firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').append(populateBeatPageEvent(key,firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').append(populateBeatPageEvent(key,firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').append(populateBeatPageEvent(key,firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                }
                if(!(data.beatplans==null)){
                  var key = "beatplans";
                  beatPlanDateArray = data.beatplans.map(function (el) { return AD2BS(el.start_date); });
                  $('#calrowbody1').append(populateBeatPageEvent(key, firstEnd[0],firstEnd[1],data['beatplans'],data['beatplans'].length));
                  $('#calrowbody2').append(populateBeatPageEvent(key, firstEnd[2],firstEnd[3],data['beatplans'],data['beatplans'].length));
                  $('#calrowbody3').append(populateBeatPageEvent(key, firstEnd[4],firstEnd[5],data['beatplans'],data['beatplans'].length));
                  $('#calrowbody4').append(populateBeatPageEvent(key, firstEnd[6],firstEnd[7],data['beatplans'],data['beatplans'].length));
                  $('#calrowbody5').append(populateBeatPageEvent(key, firstEnd[8],firstEnd[9],data['beatplans'],data['beatplans'].length));
                  $('#calrowbody6').append(populateBeatPageEvent(key, firstEnd[10],firstEnd[11],data['beatplans'],data['beatplans'].length));
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
              'employee_id': employee_id,
            },
            success: function (data) {
              beatPlanDateArray = new Array();
              $('#calNepaliYear').val(data['year']);
              $('#calNepaliMonth').val(data['month']);
              var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
              $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
              $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
              if(!(data.holidays==null)){
                var key = "holidays";
                $('#calrowbody1').append(populateBeatPageEvent(key,firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                $('#calrowbody2').append(populateBeatPageEvent(key,firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                $('#calrowbody3').append(populateBeatPageEvent(key,firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                $('#calrowbody4').append(populateBeatPageEvent(key,firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                $('#calrowbody5').append(populateBeatPageEvent(key,firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                $('#calrowbody6').append(populateBeatPageEvent(key,firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              }
              if(!(data.beatplans==null)){
                var key = "beatplans";
                beatPlanDateArray = data.beatplans.map(function (el) { return AD2BS(el.start_date); });
                $('#calrowbody1').append(populateBeatPageEvent(key, firstEnd[0],firstEnd[1],data['beatplans'],data['beatplans'].length));
                $('#calrowbody2').append(populateBeatPageEvent(key, firstEnd[2],firstEnd[3],data['beatplans'],data['beatplans'].length));
                $('#calrowbody3').append(populateBeatPageEvent(key, firstEnd[4],firstEnd[5],data['beatplans'],data['beatplans'].length));
                $('#calrowbody4').append(populateBeatPageEvent(key, firstEnd[6],firstEnd[7],data['beatplans'],data['beatplans'].length));
                $('#calrowbody5').append(populateBeatPageEvent(key, firstEnd[8],firstEnd[9],data['beatplans'],data['beatplans'].length));
                $('#calrowbody6').append(populateBeatPageEvent(key, firstEnd[10],firstEnd[11],data['beatplans'],data['beatplans'].length));
              }
            }
          });
        });
        $('#todayMonth').click();
        $('#ncalendar').on('click','td',function(){
          if(typeof $(this).attr('data-date')!="undefined"){
            let dateVal = $(this).data('date');
            let includes = beatPlanDateArray.includes(dateVal); 
            if(includes!=true){
              if(BS2AD(dateVal)<moment().format('YYYY-MM-DD')){
                alert("Cannot create plans for past date.");
              }else{
                $('#AddNewBeatPlan')[0].reset();
                $('#exampleModalCenter').modal();  
                var getEmployeeName = $('.employee_list').val();
                $('#AddNewBeatPlan').find('#employee_list').val(getEmployeeName);
                $('#AddNewBeatPlan').find('#employee_list').trigger('change'); 
                $('.fromdate').val($(this).attr('data-date'));
              }
            }else{
              let id = "{{$employee_id}}";
              let beat_id = null;//element.data('id');
              let fetch_id = null;//element.data('beatdetailid');
              var url = "{{domain_route('company.admin.beatplan.editSingle', [':employee',':employeeid'])}}";
              let get_url = url.replace(':employee',id);
              get_url = get_url.replace(':employeeid',id);
            
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: get_url,
                type: "GET",
                data: {
                  id: id,
                  beat_id: beat_id,
                  fetch_id: fetch_id,
                  date: BS2AD(dateVal),
                },
                beforeSend:function(data,id){
                  $('#mainBox').addClass('box-loader');
                  $('#loader1').removeAttr('hidden');
                },
                success:function(data) {
                  $('#edit_modal_single').html(data);
                  $('#mainBox').removeClass('box-loader');
                  $('#loader1').attr('hidden','hidden');
                  $('#edit_modal_single').modal();
                  $('#edit_modal_single').find('#employee_list').val($('#edit_modal_single').find('#employee_list').data("id")).trigger('change');   
                },
                error:function(xhr){
                }
              });
            }
          }
          
        });
      });
      $('.fromdate').nepaliDatePicker({
        ndpEnglishInput: 'englishDate',
        disableBefore: moment(getNepaliDate()).format('MM/D/Y'),
      });
      $('#ncalendar').on('click','.fa-edit',function(e){
        let element = $(this);
        getBeatModal(element);
      });
      $('#ncalendar').on('click','.fa-trash',function(e){
        let id = $(this).data('beatdetailid');
        let beatvplan_id = $(this).data('id');
        $('#del_id').val(id);
        $('#get_id').val(beatvplan_id);
        $('#empl_id').val("{{$employee}}");
        $('#del_event_modal').modal('show');
      });
      // $('#ncalendar').on('click','div.fc-content.fc-beatcontent',function(e){
      //   let element = $(this);
      //   getBeatModal(element);
      // });
      function getBeatModal(element){
        let id = "{{$employee_id}}";
        let beat_id = null;//element.data('id');
        let fetch_id = element.data('beatdetailid');
        var url = "{{domain_route('company.admin.beatplan.editSingle', [':employee',':employeeid'])}}";
        let get_url = url.replace(':employee',id);
        get_url = get_url.replace(':employeeid',id);
      
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: get_url,
          type: "GET",
          data: {
            id: id,
            beat_id: beat_id,
            fetch_id: fetch_id
          },
          beforeSend:function(data,id){
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
          },
          success:function(data) {
            $('#edit_modal_single').html(data);
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden','hidden');
            $('#edit_modal_single').modal();
            $('#edit_modal_single').find('#employee_list').val($('#edit_modal_single').find('#employee_list').data("id")).trigger('change');   
          },
          error:function(xhr){
          }
        });
      }
    @endif
    
    // Adding Rows to Modal
    let numAddRow = 1;
    $('.addPlans').click(function(){
      ++numAddRow;
      let j = numAddRow-1;
      let data = '<tr class="appendedElement'+numAddRow+'" id="appendedElement'+numAddRow+'">'+
                  '<td>'+
                      '<div class="input-group" style="width: 100%;">'+
                          '<input class="form-control title" type="text" id="title'+numAddRow+'" name="title[]" required>'+
                      '</div>'+
                  '</td>'+
                  '<td>'+
                      '<div class="input-group beat_class" style="width:-webkit-fill-available">'+
                      '<select class="form-control multibeat beat_list" id="beat_list'+numAddRow+'" name="beat_list['+j+'][]" data-id="'+numAddRow+'" multiple>'+
                          '@foreach($beats_list as $beat_id=>$client_ids)'+
                          '<optgroup label="{{$client_ids["name"]}}">'+
                              '@foreach($client_ids["clients"] as $client_id=>$company_name)'+
                              '<option value="{{$beat_id}},{{ $client_id }}" >{{$company_name}}</option>'+
                              '@endforeach'+
                          '</optgroup>'+
                          '@endforeach'+
                      '</select>'+
                      '<span class="err" id="beat_lists'+j+'"></span>'+
                      '</div>'+
                  '</td>'+
                  '<td tyle="width:170px;">'+
                      '<div class="input-group" style="width:100%;">'+
                          '<input autocomplete="off" required class="form-control pd-left fromdate" type="text" id="start_date'+numAddRow+'" name="start_date[]" data-id="'+numAddRow+'">'+
                          '<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>'+                  
                      '</div>'+
                  '</td>'+
                  '<td>'+
                      '<div class="input-group" style="width:100%;">'+
                          '<textarea class="form-control remark" id="remark'+numAddRow+'" name="remark[]" style="height:40px;"></textarea>'+
                      '</div>'+
                  '</td>'+
                  '<input type="hidden" name="getCount[]" value="'+j+'">'+ 
                  '<td>'+
                      '<button type="button" name="removePlans" id="'+numAddRow+'" class="btn btn-danger form-control removePlans" style="background-color:red;color:white;">X</button>'+
                  '</td>'+
                '</tr>';
      $('#toAppend').append(data);
      $('.multibeat').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: true,
        enableClickableOptGroups: true,
        includeSelectAllOption: true,	
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Assign Parties",
      });
      @if(config('settings.ncal')==0)
        $('.fromdate').datepicker({
            startDate: new Date(),
            format:'yyyy-mm-dd',
            autoclose:true,
        });
      @else
        $('.fromdate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          disableBefore: moment(getNepaliDate()).format('MM/D/Y'),
        });
      $("#start_date"+numAddRow).val(todayDate);
      @endif
    });
    // Remove Rows from Modal
    $('body').on('click','.removePlans',function(){
      let removeButtonId = $(this).attr("id");
      $('.appendedElement'+removeButtonId+'').remove();  
    });

    $('.employee_list').on('select2:select',function(){
      let sel_employee_id = $('.employee_list').val();
      window.location.href = sel_employee_id;
    });

    $('#AddNewBeatPlan').find('#employee_list').on("select2:select", function() {
      let empId = $(this).val();
      window.location.href = empId;
    });

    $('#AddNewBeatPlan').on('submit',function(event){
      event.preventDefault();

      let currentElement = $(this);
      var options = $('#party_list1 > option:selected');

      let elem_length = currentElement.find('.fromdate').length;
      let count;
      let counts;
      let date_party_array = [];
      let party_array = [];
      let parties_array = [];
      let counter;
      let counters = true;
      
      for(count=1; count<=elem_length; count++ ){
        if(!($('#start_date'+count).val() in date_party_array)){
          let clients_id = $('#party_list'+count).val();
          date_party_array[$('#start_date'+count).val()] = clients_id;
          $.each(clients_id, function(index, id) {
            parties_array.push( id ); 
          });
        }else{
          counters = false;
        }
      }    

      if(counters){
        let beat_party_array = Array();

        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: currentElement.attr('action'),
          type: "POST",
          data: new FormData(this),
          contentType: false,
          cache: false,
          processData:false,
          beforeSend:function(data){
            $('.addBeatPlans').attr('disabled',true);
            $('.addPlans').attr('disabled',true);
          },
          success:function(data){
            if(data['result']){
              alert(data['result']);
              $('.addBeatPlans').attr('disabled',false);
              $('.addPlans').attr('disabled',false);
            }else{
              alert('Beat Plan Created.');
              window.location.href = data['employee_id'];
            }
          },
          error:function(xhr){
            $('.err').html('');
            $.each(xhr.responseJSON.errors, function(index,value) {
              let classSplit = index.split('.');
              let className = classSplit[0]+'s'+classSplit[1];
              $('#'+className+'').html('<span style="color:red;">'+ value[0] +'</span>');
            });
            $('.addBeatPlans').attr('disabled',false);
            $('.addPlans').attr('disabled',false);
          }
        });
      }else{
        alert("Cannot create multiple plans on same date.");
      }
    });

    $('body').on('submit','#EditBeatPlans',function(event){
      event.preventDefault();

      let currentElement = $(this);
      var options = $('#party_list1 > option:selected');

      let elem_length = currentElement.find('.fromdate').length;
      let count;
      let counts;
      let date_party_array = [];
      let party_array = [];
      let parties_array = [];
      let counter;
      let counters = true;
      
      for(count=1; count<=elem_length; count++ ){
        if(!($('#edit_start_date'+count).val() in date_party_array)){
          let clients_id = $("#EditBeatPlans").find('#party_list'+count).val();
          date_party_array[$('#edit_start_date'+count).val()] = clients_id;
          $.each(clients_id, function(index, id) {
            parties_array.push( id ); 
          });
        }else{
          // let client_ids = $("#EditBeatPlans").find('#party_list'+count).val();
          // $.each(client_ids, function(index, id) {
          //   party_array.push( id ); 
          // });
          
          // for(counts=0; counts<parties_array.length; counts++){
          //   for(counter=0; counter<party_array.length; counter++){
          //     if(parties_array[counts] == party_array[counter]){
                 counters = false;
          //     }
          //   }
          // }
        }
      }  

      if(counters){

        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: currentElement.attr('action'),
          type: "POST",
          data: new FormData(this),
          contentType: false,
          cache: false,
          processData: false,
          beforeSend:function(data){
            $('.updateBtn').attr('disabled',true);
          },
          success:function(data){
            if(data["msg"]){
              alert(data["msg"]);
              $('.updateBtn').attr('disabled',false);
            }else{
              alert(data["result"]);
              location.reload();
            }
          },
          error:function(xhr){
              $('.err').html('');
              $.each(xhr.responseJSON.errors, function(index,value) {
                let classSplit = index.split('.');
                let className = classSplit[0]+'s'+classSplit[1];
                $('#'+className+'').html('<span style="color:red;">'+ value[0] +'</span>');
              });
              $('.updateBtn').attr('disabled',false);
              $('.updateBtn').attr('disabled',false);
          }
        });
      }else{
        // alert("Cannot assign same party for same date.");
        alert("Cannot create multiple plans on same date.");
      }
    });

    $('#delete_event').on('submit',function(event){
      event.preventDefault();
      var event_del_id = $('#del_id').val();
      var del_url = "{{domain_route('company.admin.beatplan.delete')}}";
      let emp_id = "{{$employee_id}}";
      $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: del_url,
      type: "POST",
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData:false,
      beforeSend:function(){
        $('.delBtn').attr('disabled', true);
        $('.canclBtn').attr('disabled', true);
      },
      success:function(data){
        alert('Beat Plan Deleted Successfully');
        $('.delBtn').attr('disabled', false);
        $('.canclBtn').attr('disabled', false);
        $('#del_event_modal').modal('hide');
        window.location.href = emp_id;
        $('.employee_list').val(emp_id).trigger('change');
      },
      error:function(xhr){
        var i=0;
        for (var error in xhr.responseJSON.errors) {
          if(i==0)
          {
            $('#'+error).focus();
          }
          $('#'+error).parent().parent().parent().parent().removeClass('has-error');
          $('.'+error).remove();
          $('#'+error).parent().parent().parent().parent().addClass('has-error');
          $('#'+error).next().closest( "div").after('<span class="help-block '+error+'">'+xhr.responseJSON.errors[error]+'</span>');
          i++;
        }
        $('#create_new_entry').removeAttr('disabled');
      },
      });
    });
  });
</script>
@endsection
