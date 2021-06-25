@extends('layouts.company')
@section('title', 'Leaves')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  @if($nCal==1)
    <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
    <link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif
  <style>
    .box-loader{
      opacity: 0.5;
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
    .select-2-sec{
      display: flex;
      /* flex-wrap: wrap; */
    }
  </style>

@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        @if (\Session::has('success'))
          <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
          </div><br/>
        @endif
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Leave List</h3>
            @if(Auth::user()->can('leave-create'))
            <a href="{{ domain_route('company.admin.leave.create') }}" class="btn btn-primary pull-right"
               style="margin-left: 5px;">
              <i class="fa fa-plus"></i> Create New
            </a>
            @endif
            <span id="leaveexports" class="pull-right"></span>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
              <div class="col-xs-2"></div>
              <div class="col-xs-7">
                <div class="row">
                  <div class="select-2-sec">
                    <div class="col-xs" style="min-width: 200px;margin-right: 5px">
                      <div id="salesmfilter" style="margin-top: 10px;">
                        <select name='employee' id='employee_filters' class='employee_filters select2'>
                          <option value=null>All</option>
                          @foreach($employees as $id=>$employee)
                            <option value='{{$id}}'>{{$employee}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-xs" style="min-width: 200px;margin-right: 5px">
                      <div id="statusfilter" style="margin-top: 10px;">
                        <select name='status' id='status_filters' class='status_filters select2'>
                          <option value=null>All</option>
                          <option value="Approved">Approved</option>
                          <option value="Pending">Pending</option>
                          <option value="Rejected">Rejected</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-xs">
                      @if($nCal==0)
                        <div id="reportrange" name="reportrange" class="reportrange" style="margin-top: 10px; ">
                          <i class="fa fa-calendar"></i>&nbsp;
                          <span></span> <i class="fa fa-caret-down"></i>
                        </div> 
                        <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                        <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                      @else
                        <div class="input-group hidden" id="nepCalDiv" style="margin-top: 10px;">
                          <input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date" autocomplete="off"/>
                          <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                          <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                          <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date" autocomplete="off"/>
                          <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                          <button id="filterTable" style="color:#0b7676!important;" hidden><i class="fa fa-filter" aria-hidden="true"></i></button>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xs-3"></div>
            </div>
            <div id="loader1" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <div id="mainBox">
              <table id="leavetbl" class="table table-bordered table-striped">
                <thead>
                @if($leaves)
                  <tr>
                    <th>#</th>
                    <th>From</th>
                    <th>To</th>
                    <th>No. of Days</th>
                    <th>Type</th>
                    <th>Reason</th>
                    <th>Employee</th>
                    <th>Approved/Cancelled By</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                @else
                  <tr>
                    <td colspan="10">No Record Found.</td>
                  </tr>
                @endif
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

  <div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
        </div>
          <div class="modal-body">
            <p class="text-center">
              Sorry! You are not authorized to update the status for the selected record.
            </p>
            <input type="hidden" name="leave_id" id="c_id" value="">
            <input type="text" id="accountType" name="account_type" hidden/>
          </div>
          <div class="modal-footer">
            {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
          </div>
      </div>
    </div>
  </div>

  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" id="changeStatus" method="POST"
                action="{{URL::to('admin/leave/changeStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="leave_id" id="leave_id" value="">
            <div class="form-group">
              <label class="control-label col-sm-2" for="id">Remark</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                          rows="5"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Status</label>
              <div class="col-sm-10">
                <select class="form-control" id="status" name="status">
                  <option value="Approved">Approved</option>
                  <option value="Rejected">Rejected</option>
                  <option value="Pending">Pending</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button id="btn_status_change" type="submit" class="btn actionBtn">
                <span id="footer_action_button" class='glyphicon'></span> Save
              </button>
              {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button> --}}
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <form method="post" class="remove-record-model">
          {{method_field('delete')}}
          {{csrf_field()}}
          <div class="modal-body">
            <p class="text-center">
              Are you sure you want to delete this?
            </p>
            <input type="hidden" name="id" id="m_id" value="">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <form method="post" action="{{domain_route('company.admin.leaves.customPdfExport')}}" class="pdf-export-form hidden"
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
  <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

  @if($nCal==1)
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  <script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  @else
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  @endif
  <script>
      $(function () {

          $('#delete').on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget)
              var mid = button.data('mid')
              var url = button.data('url');
              $(".remove-record-model").attr("action", url);
              var modal = $(this)
              modal.find('.modal-body #m_id').val(mid);
          })
          @if($nCal==0)

            var start = moment().subtract(3, 'months');
            var end = moment().add(3,'months');

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
                $('#startdate').val(start.format('MMMM D, YYYY'));
                $('#enddate').val(end.format('MMMM D, YYYY'));
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

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var start2 = $('#reportrange').data('daterangepicker').startDate;
                    var end2 = $('#reportrange').data('daterangepicker').endDate;
                    var start_date = Date.parse(start2.format('MMMM D, YYYY'));
                    var end_date = Date.parse(end2.format('MMMM D, YYYY'));
                    var from_date = Date.parse(data[3]);
                    var to_date = Date.parse(data[3]);
                    if (from_date >= start_date && from_date <= end_date) {
                        return true;
                    }
                    return false;
                }
            );

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
              var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
              var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
              $('#start_edate').val(start);
              $('#end_edate').val(end);
              var empVal = $('.employee_filters').find('option:selected').val();
              if(empVal=="null"){
                empVal = null;
              }
              var start = $('#start_edate').val();
              var end = $('#end_edate').val();
              sessionStorage.setItem('DT_Leav_filters', JSON.stringify({
                "empVal": empVal,
                "start": start,
                "end": end,
              }));
              if(start != '' || end != '')
              {
                $('#leavetbl').DataTable().destroy();
                initializeDT(empVal, start, end);
              }
            });

          @else
            var lastmonthdate = AD2BS(moment().subtract(3,'months').format('YYYY-MM-DD'));
            var ntoday = AD2BS(moment().add(3,'months').format('YYYY-MM-DD'));
            $('#start_ndate').val(lastmonthdate);
            $('#end_ndate').val(ntoday);
            $('#start_edate').val(BS2AD(lastmonthdate));
            $('#end_edate').val(BS2AD(ntoday));
            $('#nepCalDiv').removeClass('hidden');

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
                sessionStorage.setItem('DT_Leav_filters', JSON.stringify({
                  "empVal": empVal,
                  "start": start,
                  "end": end,
                }));
                $('#leavetbl').DataTable().destroy();
                initializeDT(empVal, start, end);
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
                sessionStorage.setItem('DT_Leav_filters', JSON.stringify({
                  "empVal": empVal,
                  "start": start,
                  "end": end,
                }));
                $('#leavetbl').DataTable().destroy();
                initializeDT(empVal, start, end);
              }
            });

            $.fn.dataTable.ext.search.push(
              function (settings, data, dataIndex) {
                var start2 = $('#start_edate').val();
                var end2 = $('#end_edate').val();
                var create_date =data[9]; 
                var end_create_date =data[10]; 
                if (create_date >= start2 && end_create_date <= end2) {
                    return true;
                }
                return false;
              }
            );
          @endif
          function initializeDT(empVal=null, startD, endD){
            let status = $('.status_filters').find('option:selected').val();
            if(status=="null"){
              status = null;
            }
            var table = $('#leavetbl').DataTable({
            "order": [[ 1, "desc" ]],
            "processing": true,
            "serverSide": true,
            "ajax":{
               "url": "{{ domain_route('company.admin.leave.ajaxTable') }}",
               "dataType": "json",
               "type": "POST",
               "data":{ 
                _token: "{{csrf_token()}}",
                empVal:empVal,
                startDate: startD,
                endDate: endD,
                status: status
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
              },
             },
            "columnDefs": [ 
                {
                  "targets": [-1],
                  "orderable": false,
                },
                {
                  "targets": [5],
                  "width": "200px",
                },
              ],
            "columns": [
              { "data": "id" },
              { "data": "start_date" },
              { "data": "end_date" },
              { "data": "noDays" },
              { "data": "LeaveTypeName" },
              { "data": "remarks" },
              { "data": "AddedByName" },
              { "data": "ApprovedByName" },
              { "data": "status" },
              { "data": "action" },
            ],

            "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
            "<'row'<'col-xs-6'><'col-xs-6'>>" +
            "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
            buttons: [
                {
                    extend: 'colvis',
                    order: 'alpha',
                    className: 'dropbtn',
                    columns:[0,1,2,3,4,5,6,7,8],
                    text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                    columnText: function ( dt, idx, title ) {
                        return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: 'Employee Leave List',
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
                    title: 'Employee Leave List',
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
                    title: 'Employee Leave List',
                    exportOptions: {
                      columns: ':visible:not(:last-child)'
                    },
                    footer: true,
                    action: function ( e, dt, node, config ) {
                      newExportAction( e, dt, node, config );
                    }
                },
            ],
            });
            table.buttons().container()
            .appendTo('#leaveexports');

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
                data.length = {{$leavesCount}};
                dt.one('preDraw', function (e, settings) {
                  if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                    var columnsArray = [];
                    var visibleColumns = settings.aoColumns.map(setting => {
                                            if(setting.bVisible){
                                              columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                                            } 
                                          })    
                    columnsArray.pop("Action")
                    // columnsArray.push("S.No.", "Party Name", "Salesman", "Date", "Remark");
                    var columns = JSON.stringify(columnsArray);
                    $.each(settings.json.data, function(key, htmlContent){
                      settings.json.data[key].id = key+1;
                      if(settings.json.data[key].AddedByName!="")
                        settings.json.data[key].AddedByName = $(settings.json.data[key].AddedByName)[0].textContent;
                      if(settings.json.data[key].ApprovedByName!="")
                        settings.json.data[key].ApprovedByName = $(settings.json.data[key].ApprovedByName)[0].textContent;
                      settings.json.data[key].status = $(settings.json.data[key].status)[0].textContent;
                    });
                    customExportAction(config, settings, columns);
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

            function customExportAction(config, settings, cols){
              $('#exportedData').val(JSON.stringify(settings.json));
              $('#pageTitle').val(config.title);
              $('#columns').val(cols);
              var propertiesArray = [];
              var visibleColumns = settings.aoColumns.map(setting => {
                                    if(setting.bVisible) propertiesArray.push(setting.data)
                                  })
              propertiesArray.pop("action")
              // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
              var properties = JSON.stringify(propertiesArray);
              $('#properties').val(properties);
              $('#pdf-generate').submit();
            }

          }
          var start = moment().subtract(3, 'months').format('YYYY-MM-DD');
          var end = moment().add(3,'months').format('YYYY-MM-DD');
          var empVal = null;
          @if (\Session::has('DT_Leav_filters'))
            let filtersSel = @json(\Session::get('DT_Leav_filters'));
            sessionStorage.setItem('DT_Leav_filters', filtersSel);
          @else
            sessionStorage.setItem('DT_Leav_filters', "");
          @endif
          if(sessionStorage.getItem('DT_Leav_filters')!="" || !sessionStorage.getItem('DT_Leav_filters')==undefined ){
            let filterValue = JSON.parse(sessionStorage.getItem('DT_Leav_filters'));
            if(filterValue){
              empVal = filterValue.empVal;
              start = filterValue.start;
              end = filterValue.end;
              @if($nCal==0)
                $('#start_edate').val(moment(start).format('YYYY-MM-DD'));
                $('#end_edate').val(moment(end).format('YYYY-MM-DD'));
                cb(moment(start), moment(end));
              @else
                $('#start_ndate').val(AD2BS(moment(start).format('YYYY-MM-DD')));
                $('#end_ndate').val(AD2BS(moment(end).format('YYYY-MM-DD')));
                $('#start_edate').val(moment(start).format('YYYY-MM-DD'));
                $('#end_edate').val(moment(end).format('YYYY-MM-DD'));
              @endif
              if(empVal!=null) $('.employee_filters').val(empVal).trigger('change');
              // sessionStorage.setItem('DT_Leav_filters', "");
              sessionStorage.setItem('DT_Leav_filters', JSON.stringify({
                "empVal": empVal,
                "start": start,
                "end": end,
              }));
            }
          }else{
            sessionStorage.setItem('DT_Leav_filters', "");
          }
          initializeDT(empVal, start, end);
          $('.select2').select2();

          
          $('body').on("change", ".employee_filters",function () {
              var empVal = $('.employee_filters').find('option:selected').val();
              if(empVal=="null"){
                empVal = null;
              }
              var start = $('#start_edate').val();
              var end = $('#end_edate').val();
              sessionStorage.setItem('DT_Leav_filters', JSON.stringify({
                "empVal": empVal,
                "start": start,
                "end": end,
              }));
              if(empVal != '')
              {
                $('#leavetbl').DataTable().destroy();
                initializeDT(empVal, start, end);
              }
          });
          $('body').on("change", ".status_filters",function () {
              var empVal = $('.employee_filters').find('option:selected').val();
              if(empVal=="null"){
                empVal = null;
              }
              var start = $('#start_edate').val();
              var end = $('#end_edate').val();
              sessionStorage.setItem('DT_Leav_filters', JSON.stringify({
                "empVal": empVal,
                "start": start,
                "end": end,
              }));
              $('#leavetbl').DataTable().destroy();
              initializeDT(empVal, start, end);
          });
      });


      $(document).on('click', '.edit-modal', function () {
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Edit');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#leave_id').val($(this).data('id'));
          $('#remark').val($(this).data('remark'));
          $('#status').val($(this).data('status'));
          $('#myModal').modal('show');
      });

      $('#changeStatus').on('submit',function(){
          $('#btn_status_change').attr('disabled',true);
      });

      $('#leavetbl').on('click','.alert-modal',function(){
        $('#alertModal').modal('show');
      });

      //responsive 
      $('#reportrange').on('click',function(){
        if ($(window).width() <= 320) {   
          $(".daterangepicker").addClass("leavedateposition");
          
        }
        else if ($(window).width() <= 768) {
          $(".daterangepicker").addClass("leavedateposition");
        }
        else {   
          $(".daterangepicker").removeClass("leavedateposition");
        }
      });

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

  </script>

@endsection