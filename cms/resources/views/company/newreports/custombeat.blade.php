<div class="row">
  <div class="col-xs-12">

    <div class="box">
      <div class="box-header">
        <div class="col-xs-6">
        <h3 class="box-title"> Beat Report by Salesman </h3>
        </div>
        <div class="col-xs-6">
        <span id="dailyempreportexports" class="pull-right">
        </div>
      </div>
      
      <div class="box-body tablediv">
        <div id="loader2">
        <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
        <div class="container-fluid" style="width:auto;">
          <div class="row">
            <form action="{{domain_route('company.admin.custombeatroutereport')}}" method="post" id="customreportForm">
              @csrf
              <div class="col-xs-3">
                  <div class="input-group"style="width:100%">
                  <label> Select Salesman</label>
                  {!! Form::select('employee_id', [null => 'Select a salesman'] + $salesman, null,  ['class' => 'form-control select2','required', 'id' =>'employee_id']) !!}
                  </div>
              </div>
              @if(config('settings.ncal')==0)
                <div class="col-xs-4">
                  <label> Select Date Range</label>
                  <div class="input-group">
                    <div class="reportrange" id="reportrange" name="reportrange" style="width:100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span><i class="fa fa-caret-down"></i>
                    </div>
                  </div>
                </div>
                <input type="hidden" name="start_date" id="startdate">
                <input type="hidden" name="end_date" id="enddate">
              @else
                <div class="col-xs-6">
                  <label> Select Date Range</label>
                  <div class="input-group" id="nepCalDiv">
                    <input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date" autocomplete="off"/>
                    <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                    <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                    <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date" autocomplete="off" />
                    <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                  </div>
                </div>
              @endif
                      <div class="col-xs-2" @if(config('settings.ncal')==0)style="right:75px;"@endif>
                          <div class="input-group" style="top:25px;">
                          <button type="submit" class="btn btn-default" id="getCustomReport" style="background: #389c9c;color:white;">
                              <span  style="color:white;"><i class="fa fa-book"></i> View Report
                              </span>
                          </button>
                          </div>
                      </div>
            </form>
          </div>
        </div>
        
        <div class="box-body tablediv" id="customappendhere">
          <div id="mainBox2">
            <table id="salesmanroute" class="table table-bordered table-striped">
              <thead>
              <tr>
              <th>Date</th>
              <th>Target Calls/Visits</th>
              <th>Total Actual Calls/Visits</th>
              <th>Effective Calls</th>
              <th>Unscheduled Effective Calls</th>
              <th>Non-effective Calls</th>
              <th>Unscheduled Non-Effective Calls</th>
              <th>Not Covered</th>
              <th>Planned Beats</th>
              <th>Actual Beats</th>
              <th>GPS Beat Comparison</th>
              </tr>
              </thead>
              <tbody>

              </tbody>
            </table> 
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade bd-example-modal-xs" tabindex="-1" role="dialog" id="viewPartiesModalSingle">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h3 class="modal-title"><span id="exampleModalLongTitle"></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button></h3>
            </div>
            <div class="modal-body">
            <ul class="list-group" id="list-parties-single">
            </ul>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-xs" tabindex="-1" role="dialog" id="viewgpsreportsingle">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Beat Comparison<span id="exampleModalLongTitle_name"></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button></h3>
          </div>
          <div class="modal-body">
            <div id="map2"> </div>
            <div id="lgnd2"></div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <form method="post" action="{{domain_route('company.admin.beatroutereports.customPdfExport')}}"
    class="pdf-export-form hidden" id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <input type="text" name="reportName" class="reportName" id="reportName">
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
  </form>
</div>