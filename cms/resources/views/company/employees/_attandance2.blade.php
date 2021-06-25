<section class="content">
    <div class="box">
      <div class="row">
        <div class="col-xs-6"><span id="TNPreDays">Present:</span></div>
        <div class="col-xs-6"><span id="TNAbsDays">Absent:</span></div>
        {{-- <div class="col-xs-3"><span id="NWOff">Weekly Off: </span></div>
        <div class="col-xs-3"><span id="TNHolidays">Holidays: </span></div> --}}
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-body no-padding">
              <!-- THE CALENDAR -->
              {{-- start of manual calendar --}}
                <div id="ncalendar" class="fc fc-unthemed fc-ltr">
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