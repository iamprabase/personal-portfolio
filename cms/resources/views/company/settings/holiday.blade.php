<section class="content">
  <div class="box">
    <div class="row">
      <div class="col-md-12">
        <div class="box-header">
          <h3 class="box-title">Holidays</h3>
          <button class="btn btn-primary pull-right createholiday" data-toggle="modal" data-target="exampleModalCenter">
            <i class="fa fa-plus"></i> Create New
          </button>
          <button id="populate" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Populate Weekly Holidays
          </button>
        </div>
      </div>
      <!-- /. box -->
      <!-- /.col -->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-body no-padding">
            <!-- THE CALENDAR -->
            <div id="calendar"></div>
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

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
  {!! Form::open(array('url' => url(domain_route("company.admin.holidays.store")), 'method' => 'post','id'=>'AddNewHoliday', 'files'=> true)) !!}
  <div class="modal-dialog modal-dialog-centered small-modal" role="document">
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
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Title</label></div>
          <div class="col-xs-6"><input required class="form-control" type="text" name="name"></div>
        </div>
        <br/>
        <div class="row">
          
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Description</label></div>
          <div class="col-xs-6"><input required class="form-control" type="text" name="description"></div>
        </div>
        <br/>
        <div class="row">
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Start Date</label></div>
          <div class="col-xs-6"><input id="add_start_date" autocomplete="off" required class="form-control fromdate" type="text" id="add_start_date" name="start_date"><input type="text" name="fromDate" hidden>
          </div>
        </div>
        <br/>
        <div class="row">
          <div class="col-xs-4"><label class="pull-right modal-label">End Date</label></div>
          <div class="col-xs-6">
            <input id="add_end_date" autocomplete="off" disabled required class="form-control todate" type="text"
                   name="end_date">
            <input type="text" name="to_date" hidden>
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
        <h4 class="modal-title" align="center" id="exampleModalLongTitle">Update Holiday
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right modal-label">Title</label></div>
          <div class="col-xs-6"><input id="edit_ename" required class="form-control" type="text" name="name"><input hidden type="text" id="edit_eid" name="edit_id"/></div>
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
          <div class="col-xs-6"><input id="edit_start_date" autocomplete="off" required class="form-control fromdate" type="text" name="start_date"><input type="text" name="fromDate" hidden></div>
        </div>
        <br/>
        <div class="row">
          <div class="col-xs-4"><label class="pull-right modal-label">End Date</label></div>
          <div class="col-xs-6">
            <input id="edit_end_date" autocomplete="off" required class="form-control todate" type="text"
                   name="end_date">
            <input type="text" name="to_date" hidden>
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
        <h4 class="modal-title" align="center" id="exampleModalLongTitle">Populate Weekly Off
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
        {{--         <div class="row">
                  <div class="col-xs-4"><label class="pull-right modal-label">Populate Range Type</label></div>
                  <div class="col-xs-6">
                    <select class="form-control" id="rangetype" name="rangetype">
                      <option value="whole_year">Whole Year</option>
                      <option value="end_of_year">End of Year from Today</option>
                      <option value="end_of_month">Current Month from Today</option>
                      <option value="end_of_next_month">Next Month from Today</option>
                      <option value="end_of_next_two_month">Next Two Month from Today</option>
                    </select>
                  </div>
                </div><br/> --}}
        <input type="text" name="rangetype" id="rangetype" value="end_of_year" hidden/>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button> --}}
          <button id="populates" type="submit" class="btn btn-primary">Populate</button>
        </div>
      </div>
    </div>
  </div>
</div>