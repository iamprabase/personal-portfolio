@extends('layouts.company')
@section('title', 'Cheque')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
  @if(config('settings.ncal')==1)
    <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
    <link rel="stylesheet"
    href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif

  <style>
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
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Cheque List</h3>
          <span id="chequeexports" class="pull-right"></span>
          <span id="totalCAmount" style="line-height: 3;margin-right: 10px;" class="pull-right"></span>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7" id="hideSection" hidden>
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-3">
                    <div style="width:100%;margin-top: 10px;height: 40px;z-index: 1;">
                      <select id="ajax_client" class="party_filters select2">
                        <option value="null">Select Party</option>
                        @forelse($partiesWithCollections as $id=>$party_name )
                        <option value="{{$id}}">{{$party_name}}</option>
                        @empty
                        <option value=""></option>
                        @endforelse
                      </select>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div style="width:100%;margin-top:10px;height: 40px;z-index: 1;position: relative;">
                      <select id="ajax_cheque_status" multiple="true" class="ajaxMultiselect"
                        style="background: #fff;width:100% !important; cursor: pointer;position: relative;z-index: 999;"
                        hidden>
                        <option value="Pending" selected>Pending</option>
                        <option value="Overdue" selected>Overdue</option>
                        <option value="Deposited">Deposited</option>
                        <option value="Cleared">Cleared</option>
                        <option value="Bounced">Bounced</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    @if(config('settings.ncal')==0)
                        <div id="reportrange" name="reportrange" class="reportrange hidden"
                             style="margin-top: 10px;min-width: 215px;">
                          <i class="fa fa-calendar"></i>&nbsp;
                          <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                        <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                        <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                      @else
                        <div class="input-group hidden" id="nepCalDiv" style="margin-top: 10px;">
                          <input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date" autocomplete="off" style="width: auto;"/>
                          <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                          <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                          <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date" autocomplete="off" style="width: auto;"/>
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
          <div id="mainBox" class="box-loader">
            <table id="chequetbl" class="table table-bordered table-striped">
              <div id="loader1">
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Party Name</th>
                  <th>Bank Name</th>
                  <th>Employee Name</th>
                  <th>Cheque Date</th>
                  <th>Receive Date</th>
                  <th>Amount</th>
                  <th>Notes</th>
                  <th>Status</th>
                  <th style="min-width: 60px; width: 60px;">Action</th>
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
          action="{{domain_route('company.admin.chequeUpdateStatus')}}">
          {{csrf_field()}}
          <input type="hidden" name="cheque_id" id="cheque_id" value="">
          <div class="form-group">
            <label class="control-label col-sm-2" for="id">Note</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                rows="5"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Status</label>
            <div class="col-sm-10">
              <select class="form-control" id="status" name="status">
                <option value="Pending">Pending</option>
                <option value="Deposited">Deposited</option>
                <option value="Cleared">Cleared</option>
                <option value="Bounced">Bounced</option>
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

<div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Sorry! You are not authorized to update the status for the selected record.
        </p>
        <input type="hidden" name="expense_id" id="c_id" value="">
        <input type="text" id="accountType" name="account_type" hidden />
      </div>
      <div class="modal-footer">
        {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

          <input type="hidden" name="employee_id" id="c_id" value="">

        </div>
        <div class="modal-footer">
          {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
          <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal modal-default fade" id="alertClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Sorry! You are not authorized to view this party details.
        </p>
        <input type="hidden" name="expense_id" id="c_id" value="">
        <input type="text" id="accountType" name="account_type" hidden />
      </div>
      <div class="modal-footer">
        {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
  </div>
</div>
<form method="post" action="{{domain_route('company.admin.cheque.customPdfExport')}}" class="pdf-export-form hidden"
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
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
@if(config('settings.ncal')==1)
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script>
    $(document).ready(function () {
        var activeRequestsTable = $('#chequetbl').DataTable();
        activeRequestsTable.state.clear();
        activeRequestsTable.destroy();
    });
  $(function () {
    $('#ajax_cheque_status').removeAttr('hidden');

    $('#delete').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var mid = button.data('mid')
      var url = button.data('url');
      $(".remove-record-model").attr("action", url);
      var modal = $(this);
      modal.find('.modal-body #m_id').val(mid);
    });

    $(document).on("click", ".clientLinks", function(e){
      if($(this).data('viewable')==""){
        e.preventDefault();
        $('#alertClientModal').modal('show');
        // $('#alertModalText').html('Sorry! You are not authorized to view this user details.');
      }
    });

    $('.select2').select2();
    @if(config('settings.ncal')==0)

      var start = moment().subtract(29, 'days');
      var end = moment().add(3,'months');

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
      $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        $('#start_edate').val(start);
        $('#end_edate').val(end);
        var partyVal = $('.party_filters').find('option:selected').val();
        if(partyVal=="null"){
          partyVal = null;
        }
        var chequeStatus = $('.ajaxMultiselect').val();
        if(chequeStatus.length==0){
          chequeStatus = null;
        }
        
        var startD = $('#start_edate').val();
        var endD = $('#end_edate').val();
        sessionStorage.setItem('DT_Cheq_filters', JSON.stringify({
          "partyVal": partyVal,
          "chequeStatus": chequeStatus,
          "start": start,
          "end": end,
        }));
        if(startD != '' || endD != ''){
          $('#chequetbl').DataTable().destroy();
          initializeDT(partyVal, chequeStatus, start, end);
        }
      });
      $('#reportrange').removeClass('hidden');
    @else
      var nstartDate = AD2BS(moment().subtract(1,'months').format('YYYY-MM-DD'));
      var ntoday = AD2BS(moment().add(3,'months').format('YYYY-MM-DD'));
      $('#start_ndate').val(nstartDate);
      $('#end_ndate').val(ntoday);
      $('#start_edate').val(BS2AD(nstartDate));
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
          var partyVal = $('.party_filters').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          var chequeStatus = $('.ajaxMultiselect').val();
          if(chequeStatus.length==0){
            chequeStatus = null;
          }
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          if(end==""){
            end = start;
          }
          sessionStorage.setItem('DT_Cheq_filters', JSON.stringify({
            "partyVal": partyVal,
            "chequeStatus": chequeStatus,
            "start": start,
            "end": end,
          }));
          if(start != '' || end != '')
          {
            $('#chequetbl').DataTable().destroy();
            initializeDT(partyVal, chequeStatus, start, end);
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
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          var chequeStatus = $('.ajaxMultiselect').val();
          if(chequeStatus.length==0){
            chequeStatus = null;
          }
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          if(start==""){
            start = end;
          }
          sessionStorage.setItem('DT_Cheq_filters', JSON.stringify({
            "partyVal": partyVal,
            "chequeStatus": chequeStatus,
            "start": start,
            "end": end,
          }));
          if(start != '' || end != '')
          {
            $('#chequetbl').DataTable().destroy();
            initializeDT(partyVal, chequeStatus, start, end);
          }
        }
      });
    @endif

    var table;
    var start = $('#start_edate').val();
    var end = $('#end_edate').val();
    var chequeStatus = $('.ajaxMultiselect').val();
    var partyVal = null;
    @if (\Session::has('DT_Cheq_filters'))
      let filtersSel = @json(\Session::get('DT_Cheq_filters'));
      sessionStorage.setItem('DT_Cheq_filters', filtersSel);
    @else
      sessionStorage.setItem('DT_Cheq_filters', "");
    @endif
    if(sessionStorage.getItem('DT_Cheq_filters')!="" || !sessionStorage.getItem('DT_Cheq_filters')==undefined ){
      let filterValue = JSON.parse(sessionStorage.getItem('DT_Cheq_filters'));
      if(filterValue){
        partyVal = filterValue.partyVal;
        chequeStatus = filterValue.chequeStatus;
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
        if(partyVal!=null) $('.party_filters').val(partyVal).trigger('change');
        $('.ajaxMultiselect').val(chequeStatus).trigger('change');
        // sessionStorage.setItem('DT_Cheq_filters', "");
        sessionStorage.setItem('DT_Cheq_filters', JSON.stringify({
          "partyVal": partyVal,
          "chequeStatus": chequeStatus,
          "start": start,
          "end": end,
        }));
      }
    }else{
      sessionStorage.setItem('DT_Cheq_filters', "");
    }
    // Load Data Table on ready 
    initializeDT(partyVal, chequeStatus, start, end);

    $('body').on("change", ".party_filters",function () {
      var partyVal = $(this).find('option:selected').val();
      if(partyVal=="null"){
        partyVal = null;
      }
      var chequeStatus = $('.ajaxMultiselect').val();
      if(chequeStatus.length==0){
        chequeStatus = null;
      }
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      sessionStorage.setItem('DT_Cheq_filters', JSON.stringify({
        "partyVal": partyVal,
        "chequeStatus": chequeStatus,
        "start": start,
        "end": end,
      }));
      if(partyVal != '')
      {
        $('#chequetbl').DataTable().destroy();
        initializeDT(partyVal, chequeStatus, start, end);
      }
    });

    $('body').on("change", ".ajaxMultiselect",function () {
      var partyVal = $('.party_filters').find('option:selected').val();
      if(partyVal=="null"){
        partyVal = null;
      }
      var chequeStatus = $(this).val();
      if(chequeStatus.length==0){
        chequeStatus = null;
      }
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      sessionStorage.setItem('DT_Cheq_filters', JSON.stringify({
        "partyVal": partyVal,
        "chequeStatus": chequeStatus,
        "start": start,
        "end": end,
      }));
      if(chequeStatus != '')
      {
        $('#chequetbl').DataTable().destroy();
        initializeDT(partyVal, chequeStatus, start, end);
      }
    });

    function initializeDT(partyVal=null, chequeStatusVal=null, startD, endD){

      const table = $('#chequetbl').DataTable({
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search"
        },
        "order": [[ 0, "desc" ]],
        "serverSide": true,
        "processing": true,
        "paging": true,
        "stateSave": true,
        "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
            "<'row'<'col-xs-6'><'col-xs-6'>>" +
            "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
        "columnDefs": [
          {
            "orderable": false,
            "targets":-1,
          }, 
          { 
            width: 20, 
            targets: [0, -1],
          },
        ],
        "buttons": [
            {
                extend: 'colvis',
                order: 'alpha',
                className: 'dropbtn',
                columns:[0,1,2,3,4,5,6,7,8.9],
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                }
            },
          {
            extend: 'pdfHtml5', 
            title: 'Cheque List', 
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'excelHtml5', 
            title: 'Cheque List', 
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'print', 
            title: 'Cheque List', 
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            footer: true,
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
        ],
        "ajax":{
          "url": "{{ domain_route('company.admin.cheque.ajaxDatatable') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}", 
            partyVal : partyVal,
            chequeStatusVal : chequeStatusVal,
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
          {"data" : "company_name"},
          {"data" : "bank_name"},
          {"data" : "employee_name"},
          {"data" : "cheque_date"},
          {"data" : "payment_date"},
          {"data" : "payment_received"},
          {"data" : "payment_status_note"},
          {"data" : "payment_status"},
          {"data" : "action"},
        ],
        drawCallback:function(settings)
        {
          $('#totalCAmount').html(settings.json.total);
        }
      });
      table.buttons().container()
          .appendTo('#chequeexports');
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
          data.length = {{$collectionsCount}};
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
                settings.json.data[key].company_name = $(settings.json.data[key].company_name)[0].textContent;
                settings.json.data[key].employee_name = $(settings.json.data[key].employee_name)[0].textContent;
                settings.json.data[key].payment_status = $(settings.json.data[key].payment_status)[0].textContent;
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
      $('#reportrange').removeClass('hidden');
    };
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
    $('#reportrange').bind('DOMSubtreeModified', function (event) {
      table.draw();
    });

    $(document).on('click', '.edit-modal', function () {
      $('#footer_action_button').addClass('glyphicon-check');
      $('#footer_action_button').removeClass('glyphicon-trash');
      $('.actionBtn').addClass('btn-success');
      $('.actionBtn').removeClass('btn-danger');
      $('.actionBtn').addClass('edit');
      $('.modal-title').text('Update Cheque Payment Status');
      $('.deleteContent').hide();
      $('.form-horizontal').show();
      $('#cheque_id').val($(this).data('id'));
      $('#remark').val($(this).data('remark'));
      $('#status').val($(this).data('status'));
      $('#myModal').modal('show');
    });

    $('#chequetbl').on('click','.alert-modal',function(){
      $('#alertModal').modal('show');
    });

    $('#changeStatus').on('submit',function(){
        $('#btn_status_change').attr('disabled',true);
    });

    $('.ajaxMultiselect').multiselect({
        columns: 1,
        placeholder: 'Select Status',
        search: true,
        selectAll: true
    });

    $('#hideSection').removeAttr('hidden');

    //responsive 
    $('#reportrange').on('click',function(){
      if ($(window).width() <= 320) {   
        $(".daterangepicker").addClass("pdcdateposition");
        
      }
      else if ($(window).width() <= 768) {
        $(".daterangepicker").addClass("pdcdateposition");
      }
      else {   
        $(".daterangepicker").removeClass("pdcdateposition");
      }
    });
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