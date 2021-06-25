<section class="content">
    <div class="">
      <div class="row">
        <div class="col-md-12">
          <div class="box-header">
            <h3 class="box-title">Holidays</h3>
            <button class="btn btn-primary pull-right addnew" id="BtnAddNewHoliday"><i
                  class="fa fa-plus"></i> Create New
            </button>
            <button id="populate" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Populate Weekly Holidays
            </button>
          </div>
        </div>
        <div class="col-md-12">
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

<div class="modal fade" id="modalNewHoliday" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
  {!! Form::open(array('url' => url(domain_route("company.admin.holidays.store")), 'method' => 'post','id'=>'AddNewHoliday', 'files'=> true)) !!}
  <div class="modal-dialog modal-dialog-centered small-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center"  id="exampleModalLongTitle">Add New Holiday
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Title</label></div>
          <div class="col-xs-6"><input id="addHName" required class="form-control" type="text" name="name"></div>
        </div>
        <br/>
        <div class="row">
          
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Description</label></div>
          <div class="col-xs-6"><input id="editHName" required class="form-control" type="text" name="description"></div>
        </div>
        <br/>
        <div class="row">
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Start Date</label></div>
          <div class="col-xs-6"><input id="add_start_date" autocomplete="off" required class="form-control fromdate" type="text" name="start_date"><input id="add_start_dateAD" type="text" name="fromDate" hidden>

          </div>
        </div>
        <br/>
        <div class="row">
          <div class="col-xs-4"><label class="pull-right modal-label">End Date</label></div>
          <div class="col-xs-6">
            <input id="add_end_date" autocomplete="off" required class="form-control todate" type="text" name="end_date">
            <input id="add_end_dateAD" type="text" name="to_date" hidden>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button> --}}
        <button type="submit" class="btn btn-primary" id="btn_add_holiday">Add</button>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</div>

<div class="modal fade" id="del_event_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
  {!! Form::open(array('url' => url(domain_route("company.admin.holidays.delete")), 'method' => 'post','id'=>'delete_event', 'files'=> true)) !!}
  <div class="modal-dialog modal-dialog-centered small-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center" id="exampleModalLongTitle">
          Are you sure you want to delete this event?
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>
      <div class="modal-body">
        <input type="text" hidden name="del_id" id="del_id" value="">
        <input type="text" hidden name="year" id="del_calYear" value="">
        <input type="text" hidden name="month" id="del_calMonth" value="">
      </div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button> --}}
        <button id="keyDeleteHoliday" type="submit" class="btn btn-warning">Yes,Delete</button>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</div>

<div id="fullCalModal" class="modal fade">
  {!! Form::open(array('url' => url(domain_route("company.admin.holidays.edit")), 'method' => 'post','id'=>'EditHoliday', 'files'=> true)) !!}
  <div class="modal-dialog modal-dialog-centered small-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center" id="exampleModalLongTitle">Update Holiday
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Title</label></div>
          <div class="col-xs-6"><input id="edit_hname" required class="form-control" type="text" name="name"><input hidden type="text" id="edit_id" name="edit_id"/></div>
        </div>
        <br/>
        <div class="row">          
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Description</label></div>
          <div class="col-xs-6"><input id="edit_description" required class="form-control" type="text" name="description"></div>
        </div>
        <br/>
        <div class="row">
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Start Date</label></div>
          <div class="col-xs-6"><input id="edit_start_date" autocomplete="off" required class="form-control fromdate" type="text" name="fromdate"><input  id="fromDate" type="text" name="start_date" hidden></div>
        </div>
        <br/>
        <div class="row">
          <div class="col-xs-4"><label class="pull-right modal-label">End Date</label></div>
          <div class="col-xs-6">
            <input id="edit_end_date" autocomplete="off" required class="form-control todate" type="text"
                   name="to_date">
            <input id="to_date" type="text" name="end_date" hidden>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button> --}}
        <button id="keyEditHoliday" type="submit" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</div>


<div id="PopulateModal" class="modal fade">
  <div class="modal-dialog modal-dialog-centered small-modal" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center"  id="exampleModalLongTitle">Populate Weekly Off
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-4"><label class="pull-right modal-label">Select weekly OFF Type</label></div>
          <div class="col-xs-6">
            <select class="form-control" id="offtype" name="offtype">
              <option value="sunday">Sunday Only</option>
              <option value="saturday">Saturday Only</option>
              <option value="both">Both</option>
            </select>
          </div>
        </div>
        <br/>
        <input type="text" name="rangetype" id="rangetype" value="end_of_year" hidden/>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button> --}}
          <button id="populates" type="submit" class="btn btn-primary">Populate</button>
        </div>
      </div>
    </div>
  </div>
</div>