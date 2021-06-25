@extends('layouts.company')
@section('title', 'Day Remarks')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/delta.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<style>
  .multiselect-item.multiselect-group label input{
    height:auto;
  }
  #dayremarktbl .btn-warning {
    background-color: transparent!important;
    border-color: transparent!important;
    color: #e08e0b!important;
  }
  #dayremarktbl .btn-danger {
    background-color: transparent!important;
    border-color: transparent!important;
    color: #dd4b39!important;
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

</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
      @endif
      
      @if (\Session::has('error'))
      <div class="alert alert-warning">
        <p>{{ \Session::get('error') }}</p>
      </div><br />
      @endif
      @if ($errors->first())
      <div class="row">
        <div class="col-xs-12">
          <div class="alert alert-warning alert-dismissible" show" role="alert">
            <strong>{{ $errors->first('parsed_remark') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
      </div>
      @endif
      
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Day Remarks</h3>
          @if(Auth::user()->can('dayremark-create'))
          <a href="#" class="btn btn-primary pull-right" style="margin-left: 5px;" id="addRemark">
            <i class="fa fa-plus"></i> Create New
          </a>
          @endif
          <span id="dayremarksexports" class="pull-right"></span>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>

            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-6">
                    <div style="margin-top:10px;height: 40px;z-index: 999 " id="empFilters"></div>
                  </div>
                  <div class="col-xs-6">
                    @if(config('settings.ncal')==0)
                    <div id="reportrange" name="reportrange" class="reportrange hidden" style="margin-top: 10px; ">
                      <i class="fa fa-calendar"></i>&nbsp;
                      <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                    <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                    <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                    @else
                    <div class="input-group hidden" id="nepCalDiv" style="margin-top: 10px;">
                      <input id="start_ndate" class="form-control" type="text" name="start_ndate"
                        placeholder="Start Date" autocomplete="off" />
                      <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                      <span class="input-group-addon" aria-readonly="true"><i
                          class="glyphicon glyphicon-calendar"></i></span>
                      <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                        autocomplete="off" />
                      <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                      <button id="filterTable" style="color:#0b7676!important;" hidden><i class="fa fa-filter"
                          aria-hidden="true"></i></button>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-3"></div>
          </div>
          <div id="mainBox">
            <table id="dayremarktbl" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Employee Name</th>
                  <th>Date</th>
                  <th>Remarks</th>
                  <th>Action</th>
                </tr>
              </thead>
              <div id="loader1" hidden>
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
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

<div id="editModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal editDayRemarkForm" method="POST">
          {{csrf_field()}}
          <input type="hidden" name="dayremark_id" id="dayremark_id">
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Date: </label>
            <div class="col-sm-10">
              @if(config('settings.ncal')==0)
              <input class="form-control edit_date" type="text" name="start_date" id="edit_start_date" autocomplete="off"
                required>
              @else
              <input class="form-control edit_date" type="text" name="start_date" id="edit_ndate" autocomplete="off"
                required>
              @endif
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Remark</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                rows="5" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button id="btn_status_change" type="submit" class="btn btn-primary actionBtn">
              <span id="footer_action_button" class='glyphicon'></span> Save
            </button>
            {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
              <span class='glyphicon glyphicon-remove'></span> Cancel
            </button> --}}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="addModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="addDayRemark" method="POST">
          {{csrf_field()}}
          
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Date: </label>
            <div class="col-sm-10">
              @if(config('settings.ncal')==0)
                <input class="form-control add_date" type="text" name="start_date" id="add_start_date" autocomplete="off" required>
              @else
                <input class="form-control add_date" type="text" id="add_ndate" autocomplete="off" required readonly="true">
                <input class="form-control hidden" type="text" name="start_date" id="hidden_add_ndate" autocomplete="off">
              @endif
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Remark</label>
            <div class="col-sm-10">
              <textarea class="form-control ckeditor" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                rows="5" required></textarea>
              <input type="hidden" name="parsed_remark" class="parsed_remark">
            </div>
          </div>
          <div class="modal-footer">
            <button id="btn_status_change" type="submit" class="btn btn-primary actionBtn">
              <span id="footer_action_button" class='glyphicon'></span> Save
            </button>
            {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
              <span class='glyphicon glyphicon-remove'></span> Cancel
            </button> --}}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="deleteModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal delDayRemark" role="form" method="POST">
          {{csrf_field()}}
          <div class="form-group">
            <div class="col-sm-12">
              Are you sure you want to delete this day remark?
            </div>
          </div>
          <div class="modal-footer">
            <button id="btn_status_change" type="submit" class="btn btn-primary actionBtn">
              <span id="footer_action_button" class='glyphicon'></span> Delete
            </button>
            {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
              <span class='glyphicon glyphicon-remove'></span> Cancel
            </button> --}}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<form method="post" action="{{domain_route('company.admin.dayremarks.customPdfExport')}}" class="pdf-export-form hidden"
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
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
<script>
  $(function () {
      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
        modal.find('.modal-body #m_id').val(mid);
      });

      @if(config('settings.ncal')==0)
        $(".edit_date").attr('readonly','readonly');
        $(".add_date").attr('readonly','readonly');
        $(".add_date").val(moment().format('Y-MM-DD'));
        var start = moment().subtract(30, 'days');
        var end = moment().add(30, 'days');

        function cb(start, end) {
          $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
          $('#startdate').val(start.format('MMMM D, YYYY'));
          $('#enddate').val(end.format('MMMM D, YYYY'));
          $('#start_edate').val(start.format('Y-MM-DD'));
          $('#end_edate').val(end.format('Y-MM-DD'));
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
            var to_date = Date.parse(data[4]);
            if (from_date >= start_date && to_date <= end_date) {
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
          var empVal = $('.employee_filters').val();
          if(empVal=="null"){
            empVal = null;
          }
          
          var startD = $('#start_edate').val();
          var endD = $('#end_edate').val();
          if(startD != '' || endD != ''){
            $('#dayremarktbl').DataTable().destroy();
            initializeDT(empVal, start, end);
          }
        });

        $('#reportrange').removeClass('hidden');
      @else
        var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
        var ntoday = AD2BS(moment().add(30,'days').format('YYYY-MM-DD'));
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
            var empVal = $('.employee_filters').val();
            if(empVal=="null"){
              empVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(end==""){
              end = start;
            }
            if(start != '' || end != '')
            {
              $('#dayremarktbl').DataTable().destroy();
              initializeDT(empVal, start, end);
            }
          }
        });
        $('#end_ndate').nepaliDatePicker({
          onChange:function(){
            $('#end_edate').val(BS2AD($('#end_ndate').val()));
            if($('#end_ndate').val()<$('#start_ndate').val()){
              $('#start_ndate').val($('#end_ndate').val());
              $('#start_edate').val(BS2AD($('#end_ndate').val()));
            }
            var empVal = $('.employee_filters').val();
            if(empVal=="null"){
              empVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(end==""){
              end = start;
            }
            if(start != '' || end != '')
            {
              $('#dayremarktbl').DataTable().destroy();
              initializeDT(empVal, start, end);
            }
          }
        });

        $('#edit_start_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          onChange:function(){
            if($('#edit_start_ndate').val()>$('#edit_end_ndate').val()){
              $('#edit_end_ndate').val($('#edit_start_ndate').val());
            }
          }
        });
        $('#edit_end_ndate').nepaliDatePicker({
          onChange:function(){
            if($('#edit_end_ndate').val()<$('#edit_start_ndate').val()){
              $('#edit_start_ndate').val($('#edit_end_ndate').val());
            }
          }
        });
        let dtoday = moment().format('YYYY-MM-DD');
        let dtodayPlus = moment().add(0,'days').format('YYYY-MM-DD');
        let dntoday = AD2BS(dtodayPlus);
        let cToday = dntoday;
        dntoday= dntoday.split('-');
        dntoday = dntoday[1]+'/'+dntoday[2]+'/'+dntoday[0];
        $('#add_ndate').val(cToday);
        $('#edit_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          disableBefore: dntoday,
          disableAfter: dntoday
        });
      @endif

      var empSelect = "<select sname='employee[]' id='employee_filters' class='employee_filters' multiple>@forelse($employeesWithDayRemarks as $id=>$employee)<option value='{{$id}}' selected>{{$employee}}</option>@empty<option></option>@endforelse</select>";
      $('#empFilters').append(empSelect);
      // $('#employee_filters').select2({
      //   "placeholder": "Select Employee",
      // });
      $('#employee_filters').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: true,
        includeSelectAllOption: true,	
        nonSelectedText:"Select Employees",
        disableIfEmpty:true,
      });

      var table;
      var startD = $('#start_edate').val();
      var endD = $('#end_edate').val();
      initializeDT(empVal=$('.employee_filters').val(), startD, endD)
    });

    function initializeDT(empVal=null, startD, endD){
      const table = $('#dayremarktbl').DataTable({
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search"
        },
        "order": [[ 2, "desc" ]],
        "serverSide": true,
        "processing": true,
        "paging": true,
        "dom":  "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
                "<'row'<'col-xs-6'><'col-xs-6'>>" +
                "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
        "columnDefs": [
          {
            "orderable": false,
            "targets":-1,
          }, 
          { 
            width: 20, 
            targets: [0],
          },
          { 
            width: 100, 
            targets: [-1],
          },
        ],

        "buttons": [
            {
                extend: 'colvis',
                order: 'alpha',
                className: 'dropbtn',
                columns:[0,1,2,3],
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                }
            },
          {
            extend: 'pdfHtml5', 
            title: 'Day Remarks', 
            exportOptions: {
              columns: ':visible:not(:last-child)',
              stripNewlines: false,
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'excelHtml5', 
            title: 'Day Remarks', 
            exportOptions: {
              columns: ':visible:not(:last-child)',
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'print', 
            title: 'Day Remarks', 
            exportOptions: {
              columns: ':visible:not(:last-child)',
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
        ],
        "ajax":{
          "url": "{{ domain_route('company.admin.dayremarks.ajaxDatatable') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}", 
            empVal : empVal,
            startDate: startD,
            endDate: endD, 
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
          }
        },
        "columns": [
          {"data" : "id"},
          {"data" : "employee_name"},
          {"data" : "remark_date"},
          {"data" : "remarks"},
          {"data" : "action"},
        ],
      });
      table.buttons().container()
          .appendTo('#dayremarksexports');
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
          data.length = {{$dayRemarksCount}};
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
                settings.json.data[key].employee_name = $(settings.json.data[key].employee_name)[0].textContent;
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
      $('#reportrange').removeClass('hidden');
    }; // Data Table initialize 

    $('body').on("change", ".employee_filters",function () {
      // var empVal = $(this).find('option:selected').val();
      var empVal = $(this).val();
      if(empVal=="null"){
        empVal = null;
      }
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      if(empVal != '')
      {
        $('#dayremarktbl').DataTable().destroy();
        initializeDT(empVal, start, end);
      }
    });

    $('#dayremarktbl').on('click','.alert-modal',function(){
      $('#alertModal').modal('show');
    });

    $('#changeStatus').on('submit',function(){
      $('#btn_status_change').attr('disabled',true);
    });

    // after loading show
    $(function () {
      $('#reportrange').removeClass('hidden');
    });

    //responsive 
    $('#reportrange').on('click',function(){
      if ($(window).width() <= 320) {   
        $(".daterangepicker").addClass("dayremarkdateposition");
        
      }
      else if ($(window).width() <= 768) {
        $(".daterangepicker").addClass("dayremarkdateposition");
      }
      else {   
        $(".daterangepicker").removeClass("dayremarkdateposition");
      }
    });

    $(document).on('click','#addRemark',function(){
      $('#addModal').modal('show');
      $('.modal-title').text('Add Remarks');
    });

    $(document).on('click','.updateRemark',function(){
      $('#editModal').modal('show');
      $('.modal-title').text('Update Remark');
      $('#dayremark_id').val($(this).data('id'));
      @if(config('settings.ncal')==0)
        $('#edit_start_date').val($(this).data('date'));
      @else
        $('#edit_ndate').val(AD2BS($(this).data('date')));
      @endif
      $('#remark').val($(this).data('remarks'));
      $('.editDayRemarkForm').attr('action',$(this).attr('editurl'));
    });

    $(document).on('click','.deleteRemark',function(){
      $('#deleteModal').modal('show');
      $('.modal-title').text('Delete Remark');
      $('.delDayRemark').attr('action',$(this).attr('delurl'));
    });

    $('#addDayRemark').on('submit', function(){
      const current = $(this);
      current[0].action = "{{domain_route('company.admin.dayremarks.store')}}";
      @if(config('settings.ncal')==1)
        current.find('#hidden_add_ndate').val(BS2AD($('#add_ndate').val()));
      @endif
      $('.actionBtn').attr('disabled','disabled');
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
   $('form[id="addDayRemark"]').on('submit', function (e) {
      e.preventDefault();
      let current = $(this);
      let remark = jQuery(CKEDITOR.instances['remark'].getData()).text().trim()
      $('.parsed_remark').val(remark);
      current.unbind("submit").submit();
    });
</script>

@endsection