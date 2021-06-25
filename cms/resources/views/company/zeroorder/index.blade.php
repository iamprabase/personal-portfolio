@extends('layouts.company')
@section('title', 'Zero Orders')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@if($nCal==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<style>
  .daterangepicker .calendar-table th,
  .daterangepicker .calendar-table td {
    min-width: 25px !important;
    width: 25px !important;
  } 

  .table-condensed>tbody>tr>td,
  .table-condensed>tbody>tr>th,
  .table-condensed>tfoot>tr>td,
  .table-condensed>tfoot>tr>th,
  .table-condensed>thead>tr>td,
  .table-condensed>thead>tr>th {
    padding: 3px !important;
  }

  .daterangepicker.ltr .drp-calendar.right {
    margin-left: 0;
    border-left: 1px solid #ccc !important;
  }

  #clientexports .btn {
    padding: 10px 6px !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 22px;
  }

  button,
  input,
  select,
  textarea {
    height: 26px;
  }

  #search{
    height: 40px;
  }

  .select-2-sec {
    margin-top: -10px;
    position: absolute;
    z-index: 99;
  }

  .select2-container .select2-selection--single {
    height: 40px;
    padding: 12px 5px;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow b {
    margin-top: 3px;
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
          <h3 class="box-title">Zero Orders</h3>
          @if(Auth::user()->can('zeroorder-create'))
          <a href="{{ domain_route('company.admin.zeroorder.create') }}" class="btn btn-primary pull-right"
             style="margin-left: 5px;">
            <i class="fa fa-plus"></i> Create New
          </a>
          @endif
{{--          
          <a href="{{ URL::previous() }}" class="btn btn-default pull-right"
            id="backBtn">
            <i class="fa fa-arrow-left"></i> Back
          </a> --}}
          <span id="zeroorderexports" class="pull-right" style="margin-right: 20px !important;"></span>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-3">
                    <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="partyfilter">
                      <select id="party_filter" class="party_filters select2">
                        <option ></option>
                        <option value="null">All</option>
                        @forelse($partiesWithNoOrders as $id=>$party_name )
                        <option value="{{$id}}">{{$party_name}}</option>
                        @empty
                        <option value=""></option>
                        @endforelse
                      </select>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="salesmfilter">
                       <select id="salesman_filter" class="employee_filters select2">
                        <option ></option>
                        <option value="null">All</option>
                        @forelse($employeesWithNoOrders as $id=>$employee_name )
                        <option value="{{$id}}">{{$employee_name}}</option>
                        @empty
                        <option value=""></option>
                        @endforelse
                      </select>
                    </div>
                  </div>
                  @if($nCal==0)
                    <div class="col-xs-6">
                      <div id="reportrange" class="reportrange hidden" name="reportrange" class="reportrange"
                        style="background: #fff;margin-top: 10px; cursor: pointer; border: 1px solid #ccc; width:100%;z-index: 999;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                        <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                        <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                      </div>
                    </div>
                  @else
                    <div class="col-xs-6" style="top: 10px;">
                      <div class="row">
                        <div class="input-group hidden" id="nepCalDiv" style="margin-left: 15px;">
                          <input id="start_ndate" class="form-control" type="text" name="start_ndate"
                            placeholder="Start Date" autocomplete="off" />
                          <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                          <span class="input-group-addon" aria-readonly="true"><i
                              class="glyphicon glyphicon-calendar"></i></span>
                          <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                            autocomplete="off" />
                          <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-xs-3"></div>
          </div>
          <div class="" id="mainBox">
            <table id="order" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Party Name</th>
                  {{-- <th>Contact person Name</th>
                  <th>Party Type</th>
                  <th>Contact No.</th>
                  <th>Address</th> --}}
                  <th>Salesman</th>
                  <th>Date</th>
                  <th>Remark</th>
                  <th>Action</th>
                </tr>
              </thead>
              <div id="loader1" hidden>
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
  
              {{-- <tbody>
                @foreach($aggrunitprod as $aup)
                <tr>
                  <td>{{ $aup['partyname'] }}</td>
                  <td>{{ $aup['contact_person_name'] }} </td>
                  <td>{{ getPartyTypeName($aup['party_type'])['name'] }} </td>
                  <td>{{ $aup['contactno'] }} </td>
                  <td>{{ $aup['address'] }}</td>
                  <td>{{ $aup['salesman'] }} </td>
                  <td data-order="{{ strtotime($aup['date']) }}">{{getDeltaDate(date("Y-m-d", strtotime($aup['date'])))}}
                  </td>
                  <td>{{ $aup['remark'] }} </td>
                  @if($nCal==1)
                  <td hidden>{{ date('Y-m-d',strtotime($aup['date'])) }}</td>
                  @endif
                </tr>
                @endforeach
              </tbody> --}}
              <tfoot>
            </table>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
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
              <input type="hidden" name="noorder_id" id="m_id" value="">
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
              <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <!-- /.row -->
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
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</section>
<form method="post" action="{{domain_route('company.admin.zeroorders.custompdfreport')}}" class="pdf-export-form"
  id="pdf-generate" style="display:none;">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="reportName" class="reportName" id="reportName">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>

<!-- Modal -->

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
        var activeRequestsTable = $('#order').DataTable();
        activeRequestsTable.state.clear();
        activeRequestsTable.destroy();
    });

  $(function () {

    $('#delete').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var mid = button.data('mid')
      var url = button.data('url');
      $(".remove-record-model").attr("action", url);
      var modal = $(this)
      modal.find('.modal-body #m_id').val(mid);
    })

    $('#salesman_filter').select2({
      "placeholder": "Select Employee",
    });
    $('#party_filter').select2({
      "placeholder": "Select Parties",
    });
    $(document).on("click", ".clientLinks", function(e){
      if($(this).data('viewable')==""){
        e.preventDefault();
        $('#alertClientModal').modal('show');
        // $('#alertModalText').html('Sorry! You are not authorized to view this user details.');
      }
    });
    @if($nCal==0)

      var start = moment().subtract(29, 'days');
      var end = moment();

      function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
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
        
        var startD = $('#start_edate').val();
        var endD = $('#end_edate').val();
        sessionStorage.setItem('DT_ZeroOrd_filters', JSON.stringify({
          "empVal": empVal,
          "partyVal": partyVal,
          "start": start,
          "end": end,
        }));
        if(startD != '' || endD != ''){
          $('#order').DataTable().destroy();
          initializeDT(empVal, partyVal, start, end);
        }
      });

      $('#reportrange').removeClass('hidden');  
    @else
      $('#nepCalDiv').removeClass('hidden');
      var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
      var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
      $('#start_ndate').val(lastmonthdate);
      $('#end_ndate').val(ntoday);
      $('#start_edate').val(BS2AD($('#start_ndate').val()));
      $('#end_edate').val(BS2AD($('#end_ndate').val()));
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
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          if(end==""){
            end = start;
          }
          sessionStorage.setItem('DT_ZeroOrd_filters', JSON.stringify({
            "empVal": empVal,
            "partyVal": partyVal,
            "start": start,
            "end": end,
          }));
          if(start != '' || end != '')
          {
            $('#order').DataTable().destroy();
            initializeDT(empVal, partyVal, start, end);
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
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          if(start==""){
            start = end;
          }
          sessionStorage.setItem('DT_ZeroOrd_filters', JSON.stringify({
            "empVal": empVal,
            "partyVal": partyVal,
            "start": start,
            "end": end,
          }));
          if(start != '' || end != '')
          {
            $('#order').DataTable().destroy();
            initializeDT(empVal, partyVal, start, end);
          }
        }
      });
    @endif
    var table;
    var start = $('#start_edate').val();
    var end = $('#end_edate').val();
    var empVal = null;
    var partyVal = null;
    @if (\Session::has('DT_ZeroOrd_filters'))
      let filtersSel = @json(\Session::get('DT_ZeroOrd_filters'));
      sessionStorage.setItem('DT_ZeroOrd_filters', filtersSel);
    @else
      sessionStorage.setItem('DT_ZeroOrd_filters', "");
    @endif
    if(sessionStorage.getItem('DT_ZeroOrd_filters')!="" || !sessionStorage.getItem('DT_ZeroOrd_filters')==undefined ){
      let filterValue = JSON.parse(sessionStorage.getItem('DT_ZeroOrd_filters'));
      if(filterValue){
        empVal = filterValue.empVal;
        partyVal = filterValue.partyVal;
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
        if(partyVal!=null) $('.party_filters').val(partyVal).trigger('change');
        // sessionStorage.setItem('DT_ZeroOrd_filters', "");
        sessionStorage.setItem('DT_ZeroOrd_filters', JSON.stringify({
          "empVal": empVal,
          "partyVal": partyVal,
          "start": start,
          "end": end,
        }));
      }
    }else{
      sessionStorage.setItem('DT_ZeroOrd_filters', "");
    }
    // Load Data Table on ready 
    initializeDT(empVal, partyVal, start, end);
  });
  var columns = [{"data" : "id"},
                {"data" : "company_name"},
                // {"data" : "contact_person"},
                // {"data" : "party_type"},
                // {"data" : "contact_number"},
                // {"data" : "address"},
                {"data" : "employee_name"},
                {"data" : "date"},
                {"data" : "remark"},
                {"data" : "action"},];
  $('body').on("change", ".employee_filters",function () {
    var empVal = $(this).find('option:selected').val();
    if(empVal=="null"){
      empVal = null;
    }
    var partyVal = $('.party_filters').find('option:selected').val();
    if(partyVal=="null"){
      partyVal = null;
    }
    var start = $('#start_edate').val();
    var end = $('#end_edate').val();
    sessionStorage.setItem('DT_ZeroOrd_filters', JSON.stringify({
      "empVal": empVal,
      "partyVal": partyVal,
      "start": start,
      "end": end,
    }));
    if(empVal != '')
    {
      $('#order').DataTable().destroy();
      initializeDT(empVal, partyVal, start, end);
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
    var start = $('#start_edate').val();
    var end = $('#end_edate').val();
    sessionStorage.setItem('DT_ZeroOrd_filters', JSON.stringify({
      "empVal": empVal,
      "partyVal": partyVal,
      "start": start,
      "end": end,
    }));
    if(partyVal != '')
    {
      $('#order').DataTable().destroy();
      initializeDT(empVal, partyVal, start, end);
    }
  });


  function initializeDT(empVal=null, partyVal=null, startD, endD){
    const table = $('#order').DataTable({
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search"
      },
      "order": [[ 3, "desc" ]],
      "stateSave": true,
      "columnDefs": [
        {
          "orderable": false,
          "targets":-1,
        },],
      "serverSide": true,
      "processing": true,
      "paging": true,
      "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
          "<'row'<'col-xs-6'><'col-xs-6'>>" +
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",

      "buttons": [
          {
              extend: 'colvis',
              order: 'alpha',
              className: 'dropbtn',
              columns:[0,1,2,3,4],
              text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
              columnText: function ( dt, idx, title ) {
                  return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
              }
          },
        {
          extend: 'pdfHtml5', 
          title: 'Zero Order List', 
          footer: true,
          orientation: 'landscape',
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          },
          exportOptions: {
            columns: ':visible:not(:last-child)'
          },
        },
        {
          extend: 'excelHtml5', 
          title: 'Zero Order List', 
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          },
          exportOptions: {
            columns: ':visible:not(:last-child)'
          },
        },
        {
          extend: 'print', 
          title: 'Zero Order List', 
          footer: true,
          orientation : 'landscape',
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          },
          exportOptions: {
            columns: ':visible:not(:last-child)'
          },
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.zeroorders.zeroordersDataTable') }}",
        "dataType": "json",
        "type": "POST",
        "data":{ 
          _token: "{{csrf_token()}}", 
          empVal : empVal,
          partyVal : partyVal,
          startDate: startD,
          endDate: endD, 
          datacolumns: columns,
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
      "columns": columns,
    });
    table.buttons().container().appendTo('#zeroorderexports');
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
        data.length = -1;
        dt.one('preDraw', function (e, settings) {
          if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
            var moduleName = "zeroorderlist";
            var columnsArray = [];
            var visibleColumns = settings.aoColumns.map(setting => {
                                    if(setting.bVisible) columnsArray.push(setting.sTitle)
                                  })
            columnsArray.pop("Action")
            // columnsArray.push("S.No.", "Party Name", "Salesman", "Date", "Remark");
            var columns = JSON.stringify(columnsArray);
            $.each(settings.json.data, function(key, htmlContent){
              settings.json.data[key].id = key+1;
              settings.json.data[key].company_name = $(settings.json.data[key].company_name)[0].textContent;
              settings.json.data[key].employee_name = $(settings.json.data[key].employee_name)[0].textContent;
            });
            customExportAction(config, settings, moduleName, columns);
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
    };
    function customExportAction(config, settings, modName, cols){
      $('#exportedData').val(JSON.stringify(settings.json));
      $('#pageTitle').val(config.title);
      $('#reportName').val(modName);
      $('#columns').val(cols);
      var propertiesArray = [];
      var visibleColumns = settings.aoColumns.map(setting => {
                            if(setting.bVisible) propertiesArray.push(setting.data)
                          })
      propertiesArray.pop("action");
      // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
      var properties = JSON.stringify(propertiesArray);
      $('#properties').val(properties);
      $('#pdf-generate').submit();
    }
    $('#reportrange').removeClass('hidden');
  }; // Data Table initialize

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