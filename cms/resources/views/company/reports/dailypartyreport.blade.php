@extends('layouts.company')
@section('title', 'Daily Party report')
@section('stylesheets')
{{-- <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}" /> --}}
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
{{-- <style>
  
  .no-pd {
    padding: 0;
  }

  .fa-caret-down, .caret{
    position: absolute;
  }
</style> --}}
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <div class="col-xs-4">
            <h3 class="box-title">Daily Party Report</h3>
          </div>
          <div class="col-xs-4">
            <strong>
              <span id="orderTotal" class="pull-right"></span>
              <br />
              <span id="collectionTotal" class="pull-right"></span>
            </strong>
          </div>
          <div class="col-xs-4">
            <span id="dailypartyreportexports" class="pull-right"></span>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-4">
                    <div style="margin-top:10px;" id="partyfilter">
                      <select id="party_filter" class="party_filters select2 hidden">
                        <option></option>
                        <option value="null">All</option>
                        @forelse($partiesWithOrdersCollections as $id=>$party_name )
                          <option value="{{$id}}">{{$party_name}}</option>
                        @empty
                          <option value=""></option>
                        @endforelse
                      </select>
                    </div>
                  </div>
                  {{-- <div class="col-xs-3">
                    <div style="margin-top:10px;" id="stsfilter">
                      <select name="order_status" class="multi order_status_select" multiple>
                        @foreach($order_statuses as $key=>$value)
                          <option value="{{$key}}" selected>{{$value}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div> --}}
                  <div class="col-xs-6 no-pd">
                    @if(config('settings.ncal')==0)
                    {{-- <div class="col-xs-6"> --}}
                      <div id="reportrange" name="reportrange" class="reportrange hidden" style="margin-top: 10px;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                        <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                        <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                      </div>
                    {{-- </div> --}}
                    @else
                    {{-- <div class="col-xs-6" style="top: 10px;"> --}}
                      <div class="input-group hidden" id="nepCalDiv" style="align-items: ;top: 10px;">
                        <input id="start_ndate" class="form-control" type="text" name="start_ndate"
                          placeholder="Start Date" autocomplete="off" />
                        <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                        <span class="input-group-addon" aria-readonly="true"><i
                            class="glyphicon glyphicon-calendar"></i></span>
                        <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                          autocomplete="off" />
                        <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                      </div>
                    {{-- </div> --}}
                    @endif
                  </div>
                  <div class="col-xs-2 no-pd stsFilterDom">
                    {{-- <div style="margin-top:10px;" id="stsfilter">
                      <select name="order_status" class="multi order_status_select" multiple>
                        @foreach($order_statuses as $key=>$value)
                          <option value="{{$key}}" selected>{{$value}}</option>
                        @endforeach
                      </select>
                    </div> --}}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-3"></div>
          </div>

          <div id="mainBox">
            <table id="dailypartyreport" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Date</th>
                  <th>Party Name</th>
                  <th>Order Amount</th>
                  <th>Collection Amount</th>
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
          <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</section>
<form method="post" action="{{domain_route('company.admin.reports.customPdfExport')}}" class="pdf-export-form"
  id="pdf-generate" style="display:none;">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="reportName" class="reportName" id="reportName">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>
@endsection

@section('scripts')
{{-- <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script> --}}
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

<script type="text/javascript">
  $(document).ready(function () {
    // $('.order_status_select').multiselect({
    //   enableFiltering: true,
    //   enableCaseInsensitiveFiltering: true,
    //   enableFullValueFiltering: false,
    //   enableClickableOptGroups: false,
    //   includeSelectAllOption: true,
    //   enableCollapsibleOptGroups : true,
    //   selectAllNumber: false,
    //   numberDisplayed: 1,
    //   nonSelectedText:"Select Status",
    //   allSelectedText:"Select Status",
    //   onChange: function(option, checked, select) {
    //     changeStatusBox();
    //   },
    //   onSelectAll: function(option, checked, select) {
    //     changeStatusBox();
    //   },
    //   onDeselectAll: function (justVisible, triggerOnDeselectAll) {
    //     changeStatusBox();
    //   }
    // });
    $('#party_filter').select2({
      "placeholder": "Select Party",
    });

    $(document).on("click", ".clientLinks", function(e){
      if($(this).data('viewable')==""){
        e.preventDefault();
        $('#alertClientModal').modal('show');
        // $('#alertModalText').html('Sorry! You are not authorized to view this user details.');
      }
    });

    @if(config('settings.ncal')==0)
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
        var partyVal = $('.party_filters').find('option:selected').val();
        if(partyVal=="null"){
          partyVal = null;
        }
        
        var startD = $('#start_edate').val();
        var endD = $('#end_edate').val();
        if(startD != '' || endD != ''){
          $('#dailypartyreport').DataTable().destroy();
          initializeDT(partyVal, start, end);
        }
      });

        
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
          if($('#start_ndate').val()>$('#end_ndate').val()){
            $('#end_ndate').val($('#start_ndate').val());
            $('#end_edate').val(BS2AD($('#start_ndate').val()));
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
          if(start != '' || end != '')
          {
            $('#dailypartyreport').DataTable().destroy();
            initializeDT(partyVal, start, end);
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
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          if(end==""){
            end = start;
          }
          if(start != '' || end != '')
          {
            $('#dailypartyreport').DataTable().destroy();
            initializeDT(partyVal, start, end);
          }
        }
      });
    @endif

    var table;
    var start = $('#start_edate').val();
    var end = $('#end_edate').val();
    // Load Data Table on ready 
    initializeDT(null, start, end);
    
    
  });

  $('body').on("change", ".party_filters",function () {
    var empVal = $('.employee_filters').find('option:selected').val();
    var partyVal = $(this).find('option:selected').val();
    if(partyVal=="null"){
      partyVal = null;
    }
    var start = $('#start_edate').val();
    var end = $('#end_edate').val();
    if(partyVal != '')
    {
      $('#dailypartyreport').DataTable().destroy();
      initializeDT(partyVal, start, end);
    }
  });

  // function changeStatusBox() {
  //   var empVal = $('.employee_filters').find('option:selected').val();
  //   var partyVal = $(this).find('option:selected').val();
  //   if(partyVal=="null"){
  //     partyVal = null;
  //   }
  //   var start = $('#start_edate').val();
  //   var end = $('#end_edate').val();
  //   if(partyVal != '')
  //   {
  //     $('#dailypartyreport').DataTable().destroy();
  //     initializeDT(partyVal, start, end);
  //   }
  // }
  var columns = [
                    {"data" : "id"},
                    {"data" : "date"},
                    {"data" : "company_name"},
                    {"data" : "order_total_amount"},
                    {"data" : "collection_total_amount"},
                  ];

  function initializeDT(partyVal=null, startD, endD){
    const table = $('#dailypartyreport').DataTable({
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search"
      },
      "order": [[ 1, "desc" ]],
      "serverSide": true,
      "processing": true,
      "paging": true,
      "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
          "<'row'<'col-xs-6'><'col-xs-6'>>" +
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
      "buttons": [
        {
          extend: 'pdfHtml5', 
          title: 'Daily Party Report', 
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          },
        },
        {
          extend: 'excelHtml5', 
          title: 'Daily Party Report', 
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          },
        },
        {
          extend: 'print', 
          title: 'Daily Party Report', 
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          },
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.dailypartyreportDataTable') }}",
        "dataType": "json",
        "type": "POST",
        "data":{ 
          _token: "{{csrf_token()}}",
          partyVal : partyVal,
          startDate: startD,
          endDate: endD, 
          datacolumns: columns,
          // order_status_select: $('.order_status_select').val()
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
      drawCallback:function(settings)
      {
        $('#orderTotal').html(`Total Orders: ${settings.json.total[0]}`);
        $('#collectionTotal').html(`Total Collections: ${settings.json.total[1]}`);
      }
    });
    table.buttons().container().appendTo('#dailypartyreportexports');
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
            var moduleName = "dailypartyreport";
            var columnsArray = [];
            columnsArray.push("Date", "Party Name", "Order Amount", "Collection Amount");
            var columns = JSON.stringify(columnsArray);
            $.each(settings.json.data, function(key, htmlContent){
              settings.json.data[key].id = key+1;
              settings.json.data[key].company_name = $(settings.json.data[key].company_name)[0].textContent;
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
      propertiesArray.push("id","company_name", "date", "order_total_amount", "collection_total_amount");
      var properties = JSON.stringify(propertiesArray);
      $('#properties').val(properties);
      $('#pdf-generate').submit();
    }
    $('#reportrange').removeClass('hidden');
    $('#employee_filters').removeClass('hidden');
  }; // Data Table initialize 
  //responsive
  $('#reportrange').on('click',function(){
  if ($(window).width() <= 320) { $(".daterangepicker").addClass("derdateposition"); } else if ($(window).width() <=768) {
    $(".daterangepicker").addClass("derdateposition"); } else { $(".daterangepicker").removeClass("derdateposition"); }
  });

</script>
@endsection