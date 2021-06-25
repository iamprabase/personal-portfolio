@extends('layouts.company')
@section('stylesheets')
  <link rel="stylesheet"
        href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}"
        media="print">
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <link rel="stylesheet" href="{{ asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  <style type="text/css">
    #calendar table tbody tr td:last-child a {
      float: initial;
    }

    .fc-time {
      display: none;
    }

    .fc-content {
      color: white;
    }

    .btn-xs {
      padding: 1px 5px;
      font-size: 12px;
      border-radius: 3px;
    }

    #calendar .btn {
      height: auto !important;
    }

    .external-events {
      cursor: pointer !important;
    }

    #external-events .btn-success {
      background-color: #00da76 !important;
      cursor: default !important;
    }

    .fc-today {
      color: blue;
    }

    #myTabContent {
      margin-top: 0px;
    }

    #myTabs li {
      width: 100%;
      border-bottom: 1px solid #ccc;
    }

    .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
      color: #555;
      cursor: default !important;
      background-color: #fff;
      border-left: 2px solid #20c5cb;
      border-bottom-color: transparent;
      border-right: 0px solid #ccc;
    }

    .tab-content {
      border: 1px solid #ccc;
      padding: 20px 20px 5px;
      border-radius: 4px;
      display: inline-block;
      width: 100%;
      background: #fff;
    }

    .nav-tabs {
      border: 1px solid #ddd;
      border-radius: 4px;
      background: #fff;
    }

    .nav-tabs.holiday-tab {
      border-top: 3px solid #337ab7;
    }

    .holidayes-title {
      background: transparent !important;
    }

    .nav-tabs li.holidayes-title a {
      color: #0b7676 !important;
      font-size: 20px;
    }

    .nav-tabs li.holidayes-title a:hover {
      background: transparent;
      border-color: transparent;
      cursor: initial;
      color: #0b7676 !important;
      font-size: 20px;
    }

    .nav-tabs.holiday-tab li a:hover {
      background: transparent !important;
      border-color: transparent !important;
      color: #333 !important;
      cursor: initial;
    }
  </style>

@endsection

@section('content')
  <section class="content">
    <div class="box">
      <div class="row">
        <div class="col-sm-12">
          <div class="box-header">
            <h3 class="box-title">Holidays</h3>
            <button class="btn btn-primary pull-right addnew" data-toggle="modal" data-target="exampleModalCenter"><i
                  class="fa fa-plus"></i> Create New
            </button>
            <button id="populate" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Populate Weekly Holidays
            </button>
          </div>
        </div>

        <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
          <div class="col-sm-3 right-pd">
            <ul class="nav nav-tabs holiday-tab" id="myTabs" role="tablist">
                <?php $i = 0; ?>
              <li role="presentation" class="holidayes-title"><a href="#" role="tab" id="plan-detail-tab" data-toggle="tab" aria-controls="plan-detail" aria-expanded="false">Upcoming Holidays</a></li>
              @foreach($upcomingHolidays as $holiday)
                <li role="presentation"><a href="#" role="tab" id="plan-detail-tab" data-toggle="tab" aria-controls="plan-detail" aria-expanded="false">{{$holiday->name}}<span
                        class="pull-right">{{$data['days_diff'][$i]}}
                      @if($data['days_diff'][$i]==1)
                        day&nbsp;&nbsp;
                      @else
                        days
                      @endif
              </span></a></li>
                    <?php $i++; ?>
              @endforeach
            </ul>
          </div>
        </div>

        <!-- /. box -->
        <!-- /.col -->
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-body no-padding">
              <!-- THE CALENDAR -->

              {{-- start of manual calendar --}}
                <div id="calendar" class="fc fc-unthemed fc-ltr">
                  <div class="fc-toolbar fc-header-toolbar">
                    <div class="fc-left">
                      <div class="fc-button-group">
                        <button type="button" class="fc-prev-button fc-button fc-state-default fc-corner-left" aria-label="prev">
                          <span class="fc-icon fc-icon-left-single-arrow"></span>
                        </button>
                        <button type="button" class="fc-next-button fc-button fc-state-default fc-corner-right" aria-label="next">
                          <span class="fc-icon fc-icon-right-single-arrow"></span>
                        </button></div>
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
              {{-- end of manual calendar --}}
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->

  <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
       aria-hidden="true">
    {!! Form::open(array('url' => url(domain_route("company.admin.holidays.store")), 'method' => 'post','id'=>'AddNewHoliday', 'files'=> true)) !!}
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" align="center" id="exampleModalLongTitle">Add New Holiday
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-3"><label class="pull-right">Title</label></div>
            <div class="col-sm-6"><input required class="form-control" type="text" name="name"></div>
          </div>
          <br/>
          <div class="row">

            <div class="col-sm-1"></div>
            <div class="col-sm-3"><label class="pull-right">Description</label></div>
            <div class="col-sm-6"><input required class="form-control" type="text" name="description"></div>
          </div>
          <br/>
          <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-3"><label class="pull-right">Start Date</label></div>
            <div class="col-sm-6"><input autocomplete="off" required class="form-control fromdate" type="text"
                                         id="add_start_date" name="start_date"><input type="text" name="fromDate"
                                                                                      hidden></div>
          </div>
          <br/>
          <div class="row">
            <div class="col-sm-4"><label class="pull-right">End Date</label></div>
            <div class="col-sm-6">
              <input autocomplete="off" disabled required class="form-control todate" type="text" name="end_date">
              <input type="text" name="to_date" hidden>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </div>
    </div>
    {!! Form::close() !!}
  </div>

  <div class="modal fade" id="del_event_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
       aria-hidden="true">
    {!! Form::open(array('url' => url(domain_route("company.admin.holidays.delete")), 'method' => 'post','id'=>'delete_event', 'files'=> true)) !!}
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 align="center" class="modal-title" id="exampleModalLongTitle">
            Are you sure you want to delete this event?
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </h4>
        </div>
        <div class="modal-body">
          <input type="text" hidden name="del_id" id="del_id" value="">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button>
          <button type="submit" class="btn btn-warning">Yes,Delete</button>
        </div>
      </div>
    </div>
    {!! Form::close() !!}
  </div>

  <div id="fullCalModal" class="modal fade">
    {!! Form::open(array('url' => url(domain_route("company.admin.holidays.edit")), 'method' => 'post','id'=>'EditHoliday', 'files'=> true)) !!}
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" align="center" id="exampleModalLongTitle">Update Holiday
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-3"><label class="pull-right">Title</label></div>
            <div class="col-sm-6"><input id="edit_name" required class="form-control" type="text" name="name"><input
                  hidden type="text" id="edit_id" name="edit_id"/></div>
          </div>
          <br/>
          <div class="row">

            <div class="col-sm-1"></div>
            <div class="col-sm-3"><label class="pull-right">Description</label></div>
            <div class="col-sm-6"><input id="edit_description" required class="form-control" type="text"
                                         name="description"></div>
          </div>
          <br/>
          <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-3"><label class="pull-right">Start Date</label></div>
            <div class="col-sm-6"><input id="edit_start_date" autocomplete="off" required class="form-control fromdate"
                                         type="text" name="start_date"><input type="text" name="fromDate" hidden></div>
          </div>
          <br/>
          <div class="row">
            <div class="col-sm-4"><label class="pull-right">End Date</label></div>
            <div class="col-sm-6">
              <input id="edit_end_date" disabled autocomplete="off" required class="form-control todate" type="text"
                     name="end_date">
              <input type="text" name="to_date" hidden>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </div>
    </div>
    {!! Form::close() !!}
  </div>
@endsection

<!-- jQuery 3 -->

@section('scripts')
  <script src="{{ asset('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
  <script
      src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  <!-- Page specific script -->
  <script>
      $(function () {

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

          function getFirstDateEndDate(year,month){
              var nepalifirstDate = year+'-'+month+'-01';
              var nepalifirstNepaliToEnglishDate = BS2AD(nepalifirstDate);
              var nepaliFirstDay = moment(nepalifirstNepaliToEnglishDate).day();
              if(nepaliFirstDay==0){
            var datesrow1 = [nepalifirstDate,AD2BS(moment(nepalifirstNepaliToEnglishDate).add(1,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(5,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(6,'days').format('YYYY-MM-DD'))];
            var datesrow2 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(7,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(8,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(9,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(10,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(11,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(12,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(13,'days').format('YYYY-MM-DD'))];
            var datesrow3 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(14,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(15,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(16,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(17,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(18,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(19,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(20,'days').format('YYYY-MM-DD'))];
            var datesrow4 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(21,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(22,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(23,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(24,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(25,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(26,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(27,'days').format('YYYY-MM-DD'))];
            var datesrow5 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(28,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(29,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(30,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(31,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(32,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(33,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(34,'days').format('YYYY-MM-DD'))];
            var datesrow6 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(35,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(36,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(37,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(38,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(39,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(40,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(41,'days').format('YYYY-MM-DD'))];
            
          }else if(nepaliFirstDay==1){
            var datesrow1 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(1,'days').format('YYYY-MM-DD')),nepalifirstDate,AD2BS(moment(nepalifirstNepaliToEnglishDate).add(1,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(5,'days').format('YYYY-MM-DD'))];
            var datesrow2 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(6,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(7,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(8,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(9,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(10,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(11,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(12,'days').format('YYYY-MM-DD'))];
            var datesrow3 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(13,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(14,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(15,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(16,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(17,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(18,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(19,'days').format('YYYY-MM-DD'))];
            var datesrow4 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(20,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(21,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(22,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(23,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(24,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(25,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(26,'days').format('YYYY-MM-DD'))];
            var datesrow5 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(27,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(28,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(29,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(20,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(31,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(32,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(33,'days').format('YYYY-MM-DD'))];
            var datesrow6 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(34,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(35,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(36,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(37,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(38,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(39,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(40,'days').format('YYYY-MM-DD'))];

           
          }else if(nepaliFirstDay==2){
            var datesrow1 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(1,'days').format('YYYY-MM-DD')),nepalifirstDate,AD2BS(moment(nepalifirstNepaliToEnglishDate).add(1,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(4,'days').format('YYYY-MM-DD'))];
            var datesrow2 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(5,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(6,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(7,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(8,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(9,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(10,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(11,'days').format('YYYY-MM-DD'))];
            var datesrow3 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(12,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(13,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(14,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(15,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(16,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(17,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(18,'days').format('YYYY-MM-DD'))];
            var datesrow4 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(19,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(20,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(21,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(22,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(23,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(24,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(25,'days').format('YYYY-MM-DD'))];
            var datesrow5 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(26,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(27,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(28,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(29,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(30,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(31,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(32,'days').format('YYYY-MM-DD'))];
            var datesrow6 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(33,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(34,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(35,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(36,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(37,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(38,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(39,'days').format('YYYY-MM-DD'))];
           
          }else if(nepaliFirstDay==3){
            var datesrow1 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(1,'days').format('YYYY-MM-DD')),nepalifirstDate,AD2BS(moment(nepalifirstNepaliToEnglishDate).add(1,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(3,'days').format('YYYY-MM-DD'))];
            var datesrow2 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(5,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(6,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(7,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(8,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(9,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(10,'days').format('YYYY-MM-DD'))];
            var datesrow3 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(11,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(12,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(13,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(14,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(15,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(16,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(17,'days').format('YYYY-MM-DD'))];
            var datesrow4 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(18,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(19,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(20,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(21,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(22,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(23,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(24,'days').format('YYYY-MM-DD'))];
            var datesrow5 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(25,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(26,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(27,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(28,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(29,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(30,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(31,'days').format('YYYY-MM-DD'))];
            var datesrow6 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(32,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(33,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(34,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(35,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(36,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(37,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(38,'days').format('YYYY-MM-DD'))];
            
          }else if(nepaliFirstDay==4){
            var datesrow1 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(1,'days').format('YYYY-MM-DD')),nepalifirstDate,AD2BS(moment(nepalifirstNepaliToEnglishDate).add(1,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(2,'days').format('YYYY-MM-DD'))];
            var datesrow2 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(5,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(6,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(7,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(8,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(9,'days').format('YYYY-MM-DD'))];
            var datesrow3 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(10,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(11,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(12,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(13,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(14,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(15,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(16,'days').format('YYYY-MM-DD'))];
            var datesrow4 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(17,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(18,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(19,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(20,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(21,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(22,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(23,'days').format('YYYY-MM-DD'))];
            var datesrow5 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(24,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(25,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(26,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(27,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(28,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(29,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(30,'days').format('YYYY-MM-DD'))];
            var datesrow6 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(31,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(32,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(33,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(34,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(35,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(36,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(37,'days').format('YYYY-MM-DD'))];
            
          }else if(nepaliFirstDay==5){
            var datesrow1 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(5,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(1,'days').format('YYYY-MM-DD')),nepalifirstDate,AD2BS(moment(nepalifirstNepaliToEnglishDate).add(1,'days').format('YYYY-MM-DD'))];
            var datesrow2 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(5,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(6,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(7,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(8,'days').format('YYYY-MM-DD'))];
            var datesrow3 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(9,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(10,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(11,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(12,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(13,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(14,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(15,'days').format('YYYY-MM-DD'))];
            var datesrow4 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(16,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(17,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(18,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(19,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(20,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(21,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(22,'days').format('YYYY-MM-DD'))];
            var datesrow5 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(23,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(24,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(25,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(26,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(27,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(28,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(29,'days').format('YYYY-MM-DD'))];
            var datesrow6 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(30,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(31,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(32,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(33,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(34,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(35,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(36,'days').format('YYYY-MM-DD'))];
            
            
          }else if(nepaliFirstDay==6){
            var datesrow1 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(6,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(5,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).subtract(1,'days').format('YYYY-MM-DD')),nepalifirstDate];
            var datesrow2 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(1,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(2,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(3,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(4,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(5,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(6,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(7,'days').format('YYYY-MM-DD'))];
            var datesrow3 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(8,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(9,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(10,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(11,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(12,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(13,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(14,'days').format('YYYY-MM-DD'))];
            var datesrow4 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(15,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(16,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(17,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(18,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(19,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(20,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(21,'days').format('YYYY-MM-DD'))];
            var datesrow5 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(22,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(23,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(24,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(25,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(26,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(27,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(28,'days').format('YYYY-MM-DD'))];
            var datesrow6 = [AD2BS(moment(nepalifirstNepaliToEnglishDate).add(29,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(30,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(31,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(32,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(33,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(34,'days').format('YYYY-MM-DD')),AD2BS(moment(nepalifirstNepaliToEnglishDate).add(35,'days').format('YYYY-MM-DD'))];
          }
          firstLastDates = [datesrow1[0],datesrow1[6],datesrow2[0],datesrow2[6],datesrow3[0],datesrow3[6],datesrow4[0],datesrow4[6],datesrow5[0],datesrow5[6],datesrow6[0],datesrow6[6]];
            return firstLastDates;
          }

          function getNepaliIntegerDay(day){
            if(parseInt(day)<10){
              day = convertNos(day[1]);
            }else{
              day = convertNos(day[0])+convertNos(day[1]);
            }
            return day;
          }

          function getDateStatus(date,nepalifirstDate,currentNepaliMonthEndDate){
            var status;
            if(date<nepalifirstDate){
              status = "fc-other-month fc-past";
            }else if(date>currentNepaliMonthEndDate){
              status = "fc-other-month fc-future";

            }else if(BS2AD(date)==moment().format("YYYY-MM-DD")){
              status = "fc-today";
            }else if(BS2AD(date)<moment().format('YYYY-MM-DD'))
            {
              status = "fc-past";
            }else if(BS2AD(date)>moment().format('YYYY-MM-DD'))
            {
              status = "fc-future";
            }
            return status;
          }


          function populateEvent(startDate, endDate, holidays,maxCount){
            var col = [0,0,0,0,0,0,0];
            var rowcol = [0,0,0,0,0,0,0];
            var bodyrow;
            var hollyarray =holidays;
            var ND = [startDate,AD2BS(moment(BS2AD(startDate)).add(1,'days').format('YYYY-MM-DD')),AD2BS(moment(BS2AD(startDate)).add(2,'days').format('YYYY-MM-DD')),AD2BS(moment(BS2AD(startDate)).add(3,'days').format('YYYY-MM-DD')),AD2BS(moment(BS2AD(startDate)).add(4,'days').format('YYYY-MM-DD')),AD2BS(moment(BS2AD(startDate)).add(5,'days').format('YYYY-MM-DD')),endDate];
            var colspan = 0;
            bodyrow = '<tr>';
            for(i=0;i<7;i++){
              for(j=0;j<maxCount;j++){
                if(AD2BS(holidays[j]['start_date'])>=ND[i] && AD2BS(holidays[j]['start_date'])<=ND[6]){
                  if(AD2BS(holidays[j]['start_date'])==ND[i]){
                    for(k=0;k<i;k++){
                      if(col[k]==0){
                        bodyrow=bodyrow+'<td></td>';
                      }
                      col[k] = col[k]+1;
                    }
                    if(col[i]==0){
                      bodyrow = bodyrow + '<td class="fc-event-container" colspan="'+(parseInt(moment.duration(moment(holidays[j]['end_date']).diff(moment(holidays[j]['start_date']))).asDays())+parseInt(1))+'"><a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end" style="background-color:#f56954;border-color:#f56954"><div class="fc-content"><span class="fc-time">12a</span><span class="fc-title"><i class="fa fa-trash btn grey-mint btn-xs"></i><i class="fa fa-edit btn grey-mint btn-xs"></i>'+holidays[j]['name']+'</span></div></a></td>';
                      colspan=colspan+(parseInt(moment.duration(moment(holidays[j]['end_date']).diff(moment(holidays[j]['start_date']))).asDays())+parseInt(1));
                      hollyarray.splice(j,1);
                      maxCount = maxCount-1;
                      for(k=i;k<(i+colspan);k++){
                        col[k]=col[k]+1;
                        rowcol[k]= rowcol[k]+1;
                      }
                    }
                  }
                }
              }
            }
            bodyrow = bodyrow + '</tr>';
            if(rowcol[0]!=0 || rowcol[1]!=0 ||rowcol[2]!=0 ||rowcol[3]!=0 ||rowcol[4]!=0 ||rowcol[5]!=0 ||rowcol[6]!=0){
              bodyrow=bodyrow+populateEvent(startDate, endDate, hollyarray,maxCount);
              for(i=0;i<7;i++){
                rowcol[i]=rowcol[i]-1;
              }
            }
            return bodyrow;
          }

          $('#populate').click(function () {
              var moment = $('#calendar').fullCalendar('getDate').format('Y-M-D');
              var r = confirm("Are you sure you want to populate current Year?");
              if (r == true) {
                  $.ajax({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{domain_route('company.admin.holidays.populate')}}",
                      type: "POST",
                      data: {
                          '_token': '{{csrf_token()}}',
                          'currentDate': moment,
                      },
                      success: function (data) {
                          alert(data['result']);
                          window.location = '{{domain_route('company.admin.holidays.index')}}';
                      }
                  });
              } else {
                  alert('populating canceled');
              }
          });


          $('.fromdate').datepicker({
              defaultDate: new Date(),
              setDate: new Date(),
              format: 'yyyy-mm-dd',
              autoclose: true,
          });

          $('.todate').datepicker({
              startDate: new Date(),
              format: 'yyyy-mm-dd',
              autoclose: true,
          }).attr('disabled');


          $('.fromdate').change(function (event) {
              event.preventDefault();
              var newdate = $(this).val();
              $('.todate').datepicker('remove');
              $('.todate').val(newdate);
              $('.todate').datepicker({
                  startDate: newdate,
                  format: 'yyyy-mm-dd',
                  autoclose: true,
              }).removeAttr('disabled');
          });

          $('.addnew').click(function () {
              $('#AddNewHoliday')[0].reset();
              $('#exampleModalCenter').modal('show');
              $('.todate').prop('disabled', 'true');
          });


          $('#AddNewHoliday').on('submit', function (event) {
              event.preventDefault();
              var currentElement = $(this);
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
                  success: function (data) {
                      if (data['result']) {
                          alert(data['result'])
                      } else {
                          alert('Holiday Created Successfully');
                          window.location = '{{domain_route('company.admin.holidays.index')}}';
                      }
                  }
              });
          });


          $('#EditHoliday').on('submit', function (event) {
              event.preventDefault();
              var currentElement = $(this);
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
                  success: function (data) {
                      if (data['result']) {
                          alert(data['result']);
                          window.location = '{{domain_route('company.admin.holidays.index')}}';
                      } else {
                          alert('Holiday Updated Failed');

                      }
                  }
              });
          });

          $('#delete_event').on('submit', function (event) {
              event.preventDefault();
              var event_del_id = $('#del_id').val();
              var del_url = '{{domain_route('company.admin.holidays.delete')}}';
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: del_url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  success: function (data) {
                      alert('Holiday Deleted Successfully');
                      window.location = '{{domain_route('company.admin.holidays.index')}}';
                      $('#del_event_modal').modal('hide');
                      $('#calendar').fullCalendar('removeEvents', event_del_id);
                  },
                  error: function (xhr) {
                      var i = 0;
                      for (var error in xhr.responseJSON.errors) {
                          if (i == 0) {
                              $('#' + error).focus();
                          }
                          $('#' + error).parent().parent().parent().parent().removeClass('has-error');
                          $('.' + error).remove();
                          $('#' + error).parent().parent().parent().parent().addClass('has-error');
                          $('#' + error).next().closest("div").after('<span class="help-block ' + error + '">' + xhr.responseJSON.errors[error] + '</span>');
                          i++;
                      }
                      $('#create_new_entry').removeAttr('disabled');
                  },
              });
          });
          var currColor = '#3c8dbc' //Red by default
          //Color chooser button
          var colorChooser = $('#color-chooser-btn')
          $('#color-chooser > li > a').click(function (e) {
              e.preventDefault()
              //Save color
              currColor = $(this).css('color')
              //Add color effect to button
              $('#add-new-event').css({'background-color': currColor, 'border-color': currColor})
          })
          $('#add-new-event').click(function (e) {
              e.preventDefault()
              //Get value and make sure it is not null
              var val = $('#new-event').val()
              if (val.length == 0) {
                  return
              }

              //Create events
              var event = $('<div />')
              event.css({
                  'background-color': currColor,
                  'border-color': currColor,
                  'color': '#fff'
              }).addClass('external-event')
              event.html(val)
              $('#external-events').prepend(event)

              //Add draggable funtionality
              init_events(event)

              //Remove event from text input
              $('#new-event').val('')
          });
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
                url: "{{domain_route('company.admin.holidays.getCalendar')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
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
                url: "{{domain_route('company.admin.holidays.getCalendar')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
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
                url: "{{domain_route('company.admin.holidays.getCalendar')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                }
            });
          });
          $('#todayMonth').click();
          $('#calendar').on('click','.fa-edit',function(e){
          });
          $('#calendar').on('click','.fc-day',function(e){
            alert('You clicked'+$(this).attr('data-date'));
          });
      });
  </script>
@endsection