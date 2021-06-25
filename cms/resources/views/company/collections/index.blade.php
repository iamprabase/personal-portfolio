@extends('layouts.company')
@section('title', 'Collections')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.css')}}">
@if($nCal==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<style>
  #totalCollectionAmt {
    line-height: 3;
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

  .close{
    font-size: 30px;
    color: #080808;
    opacity: 1;
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
          <div class="row">
            <div class="col-xs-2">
              <h3 class="box-title">Collections List</h3>
            </div>
            <div class="col-xs-2"></div>
            <div class="col-xs-4">
              <strong><span class="pull-right" id="totalCollectionAmt"></span></strong>
            </div>
            <div class="col-xs-4">
              @if(Auth::user()->can('collection-create'))
              <a href="{{ domain_route('company.admin.collection.create') }}" class="btn btn-primary pull-right"
                style="margin-left: 5px;">
                <i class="fa fa-plus"></i> Create New
              </a>
              @endif
              <span id="planexports" class="pull-right"></span>
            </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-3">
                    <div style="margin-top:10px; " id="partyfilter"></div>
                  </div>
                  <div class="col-xs-3">
                    <div style="margin-top:10px;" id="salesmfilter"></div>
                  </div>
                  <div class="col-xs-3">
                    <div style="margin-top:10px;" id="paymentfilter"></div>
                  </div>
                  <div class="col-xs-3">
                    @if($nCal==0)
                    <div id="reportrange" name="reportrange" class="hidden reportrange" style="margin-top: 10px;min-width: 215px;">
                      <i class="fa fa-calendar"></i>&nbsp;
                      <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                    <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                    <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                    @else
                    <div class="input-group hidden" id="nepCalDiv" style="margin-top: 10px;">
                      <input id="start_ndate" class="form-control" type="text" name="start_ndate"
                        placeholder="Start Date" autocomplete="off" style="width: 85px;padding: 0 0 0 2px;" />
                      <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                      <span class="input-group-addon" aria-readonly="true"><i
                          class="glyphicon glyphicon-calendar"></i></span>
                      <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                        autocomplete="off" style="width: 85px;padding: 0 0 0 2px;" />
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
            <table id="collections" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Party Name</th>
                  <th>Payment Amount</th>
                  <th>Receive Date</th>
                  <th>Payment Mode</th>
                  <th>Note</th>
                  <th>Salesman</th>
                  <th>Action</th>
                  <th style="display: none;">Amount</th>
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
<!-- Modal -->
<div class="modal modal-default fade" id="delete" tabindex="-1" plan="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" plan="document">
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
          <input type="hidden" name="plan_id" id="m_id" value="">
          <input type="hidden" name="DT_Collec_FILTER" class="DT_Collec_FILTER">
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
<form method="post" action="{{domain_route('company.admin.collection.customPdfExport')}}" class="pdf-export-form hidden"
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
<script src="{{asset('assets/plugins/datatables-buttons/js/buttons.colVis.js')}}"></script>
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@if($nCal==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script>

    $(document).ready(function () {
              @if (strpos(URL::previous(), domain_route('company.admin.collection')) === false)
        var activeRequestsTable = $('#collections').DataTable();
        activeRequestsTable.state.clear();
        activeRequestsTable.destroy();
      @endif
    });

  $('.DT_Collec_FILTER').val(sessionStorage.getItem('DT_Collec_filters'));
  $(function () {
      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
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

      @if($nCal==0) 
        
        var start = moment().subtract(29, 'days');
        var end = moment();

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
          var empVal = $('.employee_filters').find('option:selected').val();
          if(empVal=="null"){
            empVal = null;
          }
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          var paymentmodeVal = $('.payment_mode').find('option:selected').val();
          if(paymentmodeVal=="null"){
            paymentmodeVal = null;
          }
          
          var startD = $('#start_edate').val();
          var endD = $('#end_edate').val();
          sessionStorage.setItem('DT_Collec_filters', JSON.stringify({
            "empVal": empVal,
            "partyVal": partyVal,
            "paymentVal": paymentmodeVal,
            "start": start,
            "end": end,
          }));
          if(startD != '' || endD != ''){
            $('#collections').DataTable().destroy();
            initializeDT(empVal, partyVal, paymentmodeVal, start, end);
          }
        });

        $('#reportrange').removeClass('hidden');
      @else

        var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
        var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
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
            var partyVal = $('.party_filters').find('option:selected').val();
            if(partyVal=="null"){
              partyVal = null;
            }
            var paymentmodeVal = $('.payment_mode').find('option:selected').val();
            if(paymentmodeVal=="null"){
              paymentmodeVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(end==""){
              end = start;
            }
            sessionStorage.setItem('DT_Collec_filters', JSON.stringify({
              "empVal": empVal,
              "partyVal": partyVal,
              "paymentVal": paymentmodeVal,
              "start": start,
              "end": end,
            }));
            if(start != '' || end != '')
            {
              $('#collections').DataTable().destroy();
              initializeDT(empVal, partyVal, paymentmodeVal, start, end);
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
            var empVal = $('.employee_filters').find('option:selected').val();
            if(empVal=="null"){
              empVal = null;
            }
            var partyVal = $('.party_filters').find('option:selected').val();
            if(partyVal=="null"){
              partyVal = null;
            }
            var paymentmodeVal = $('.payment_mode').find('option:selected').val();
            if(paymentmodeVal=="null"){
              paymentmodeVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(start==""){
              start = end;
            }
            sessionStorage.setItem('DT_Collec_filters', JSON.stringify({
              "empVal": empVal,
              "partyVal": partyVal,
              "paymentVal": paymentmodeVal,
              "start": start,
              "end": end,
            }));
            if(start != '' || end != '')
            {
              $('#collections').DataTable().destroy();
              initializeDT(empVal, partyVal, paymentmodeVal, start, end);
            }
          }
        });
      @endif

      var table;
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      var partyVal = null;
      var empVal = null;
      var paymentVal = null;
      @if (\Session::has('DT_Collec_filters'))
        let filtersSel = @json(\Session::get('DT_Collec_filters'));
        sessionStorage.setItem('DT_Collec_filters', filtersSel);
      @else
        sessionStorage.setItem('DT_Collec_filters', "");
      @endif
      if(sessionStorage.getItem('DT_Collec_filters')!="" || !sessionStorage.getItem('DT_Collec_filters')==undefined ){
        let filterValue = JSON.parse(sessionStorage.getItem('DT_Collec_filters'));
        if(filterValue){
          empVal = filterValue.empVal;
          partyVal = filterValue.partyVal;
          paymentVal = filterValue.paymentVal;
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
          // sessionStorage.setItem('DT_Collec_filters', "");
          sessionStorage.setItem('DT_Collec_filters', JSON.stringify({
            "empVal": empVal,
            "partyVal": partyVal,
            "paymentVal": paymentVal,
            "start": start,
            "end": end,
          }));
        }
      }else{
        sessionStorage.setItem('DT_Collec_filters', "");
      }
      // Load Data Table on ready 
      initializeDT(empVal, partyVal, paymentVal, start, end);

      
      var empSelect = "<select sname='employee' id='employee_filters' class='employee_filters'><option></option><option value=null>All</option> @foreach($employeesWithCollections as $id=>$employee)<option value='{{$id}}'>{{$employee}}</option>@endforeach </select>";
      var partySelect = "<select id='party_filters' class='party_filters'><option></option><option value=null>All</option> @foreach($partiesWithCollections as $id=>$parties)<option value='{{$id}}'>{{$parties}}</option> @endforeach</select>";
      var paymentModeSelect = "<select id='payment_mode' class='payment_mode'><option></option><option value=null>All</option> @foreach($paymentModes as $id=>$paymentMode)<option value='{{$paymentMode}}'>{{$paymentMode}}</option> @endforeach </select>";
      $('#salesmfilter').append(empSelect);
      $('#partyfilter').append(partySelect);
      $('#paymentfilter').append(paymentModeSelect);
      if(empVal!=null) $('#employee_filters').val(empVal);
      if(partyVal!=null) $('#party_filters').val(partyVal);
      if(paymentVal!=null) $('#payment_mode').val(paymentVal);
      $('#employee_filters').select2({
        "placeholder": "Select Employee",
      });
      $('#party_filters').select2({
        "placeholder": "Select Parties",
      });
      $('#payment_mode').select2({
        "placeholder": "Select Payment Mode",
      });

      $('body').on("change", ".employee_filters",function () {
        var empVal = $(this).find('option:selected').val();
        if(empVal=="null"){
          empVal = null;
        }
        var partyVal = $('.party_filters').find('option:selected').val();
        if(partyVal=="null"){
          partyVal = null;
        }
        var paymentmodeVal = $('.payment_mode').find('option:selected').val();
        if(paymentmodeVal=="null"){
          paymentmodeVal = null;
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        sessionStorage.setItem('DT_Collec_filters', JSON.stringify({
          "empVal": empVal,
          "partyVal": partyVal,
          "paymentVal": paymentmodeVal,
          "start": start,
          "end": end,
        }));
        if(empVal != '')
        {
          $('#collections').DataTable().destroy();
          initializeDT(empVal, partyVal, paymentmodeVal, start, end);
        }
      });

      $('body').on("change", ".party_filters",function () {
        var empVal = $('.employee_filters').find('option:selected').val();
        if(empVal=="null"){
          empVal = null;
        }
        var partyVal = $(this).find('option:selected').val();
        if(partyVal=="null"){
          partyVal = null;
        }
        var paymentmodeVal = $('.payment_mode').find('option:selected').val();
        if(paymentmodeVal=="null"){
          paymentmodeVal = null;
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        sessionStorage.setItem('DT_Collec_filters', JSON.stringify({
          "empVal": empVal,
          "partyVal": partyVal,
          "paymentVal": paymentmodeVal,
          "start": start,
          "end": end,
        }));
        if(partyVal != '')
        {
          $('#collections').DataTable().destroy();
          initializeDT(empVal, partyVal, paymentmodeVal, start, end);
        }
      });

      $('body').on("change", ".payment_mode",function () {
        var empVal = $('.employee_filters').find('option:selected').val();
        if(empVal=="null"){
          empVal = null;
        }
        var partyVal = $('.party_filters').find('option:selected').val();
        if(partyVal=="null"){
          partyVal = null;
        }
        var paymentmodeVal = $(this).find('option:selected').val();
        if(paymentmodeVal=="null"){
          paymentmodeVal = null;
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        sessionStorage.setItem('DT_Collec_filters', JSON.stringify({
          "empVal": empVal,
          "partyVal": partyVal,
          "paymentVal": paymentmodeVal,
          "start": start,
          "end": end,
        }));
        if(paymentmodeVal != '')
        {
          $('#collections').DataTable().destroy();
          initializeDT(empVal, partyVal, paymentmodeVal, start, end);
        }
      });
    });

    function initializeDT(empVal=null, partyVal=null, paymentModeVal=null, startD, endD){
      const table = $('#collections').DataTable({
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search"
        },
        "stateSave": true,
        "stateSaveParams": function (settings, data) {
        data.search.search = "";
        },
        "order": [[ 0, "desc" ]],
        "serverSide": true,
        "processing": true,
        "paging": true,
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
            columns:[0,1,2,3,4,5,6],
            text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
            columnText: function ( dt, idx, title ) {
                return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
            }
          },
          {
            extend: 'pdfHtml5', 
            title: 'Collections List', 
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
            title: 'Collections List', 
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
            title: 'Collections List', 
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
          "url": "{{ domain_route('company.admin.collection.ajaxDatatable') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}", 
            empVal : empVal,
            partyVal : partyVal,
            paymentModeVal : paymentModeVal,
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
          {"data" : "payment_received"},
          {"data" : "payment_date"},
          {"data" : "payment_method"},
          {"data" : "note"},
          {"data" : "employee_name"},
          {"data" : "action"},
        ],
        drawCallback:function(settings)
        {
          $('#totalCollectionAmt').html(settings.json.total);
        }
      });
      table.buttons().container()
          .appendTo('#planexports');
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
          data.length = -1;//{{$collectionsCount}};
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
    }; // Data Table initialize 

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

//responsive 
$('#reportrange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("dateposition");
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("dateposition");
    }
    else {   
      $(".daterangepicker").removeClass("dateposition");
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