@extends('layouts.company')
@section('title', 'Stock Report')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
  .dt-buttons.btn-group {
    width:fit-content;
  }

  .brandcat {
    margin: 0 8px 0 0;
    padding: 0.2em 1.6em 0.3em;
    border-radius: 10rem;
    font-size: x-small;
  }

  .cat {
    margin: 0 0 0 8px;
  }

  .tooltip-inner {
    max-width: 500px !important;
    background-color: aliceblue;
    color: black;
    max-height: -webkit-fill-available;
  }

  .fa.fa-info-circle {
    padding-left: inherit;
    cursor: pointer;
    color: #4c8c16;
  }

  .box-opacity {
    opacity: 0.4;
  }

  #loader3 {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 99;
  }

  .ms-options-wrap.ms-has-selections>button{
    outline: none!important;
  }
</style>
@endsection

@section('content')
<section class="content">
  <input type="hidden" name="next_refresh" id="next_refresh">
  <div class="nav-tabs-custom reportstab">
    <ul class="nav nav-tabs" id="employeetabs">
      <li class="active"><a href="#party-wise" data-toggle="tab"> Party-wise Latest Stock Report</a></li>
      <li><a href="#hist-wise" data-toggle="tab">Single Party Historical Stock Report</a></li>
      <li><a href="#parties-hist-wise" data-toggle="tab">Latest Stock by Date Report</a></li>
    </ul>
    <div class="tab-content">
      <div class="active tab-pane" id="party-wise">
        @include('company.stockreports.stockreport_partial')
      </div>
      <div class="tab-pane" id="hist-wise">
        @include('company.stockreports.stockreport_partial2')
      </div>
      <div class="tab-pane" id="parties-hist-wise">
        @include('company.stockreports.stockreport_partial3')
      </div>
    </div>
  </div>
  <input type="hidden" id="salesman_export" value="">
  <input type="hidden" id="date_export" value="">
</section>
@endsection
@section('scripts')
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
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
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script type="text/javascript">
  $(document).ready(function(){
    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if(activeTab){
        $('#employeetabs a[href="' + activeTab + '"]').tab('show');
    }
    initializeOrderDT();
  });
  $('[data-toggle="tooltip"]').tooltip({
    placement : 'right',
    container: 'body'
  });

  // Party-wise Latest Stock Report
  let party_wise_latest = $('#party_wise_latest').DataTable({
    'searching': true,
    'serverSide': false,
    'processing': false,
    'sorting': true,
    aaSorting: [[1, 'asc']],
    "paging":   true,
    "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
              "<'row'<'col-xs-4'><'col-xs-4'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
    buttons: [
      {
        extend: 'excelHtml5',
        exportOptions: {
          columns: [ 0, 2, 3, 4, 5, 6, 7]
        },
        title: function(){
          var date = $('#date_export').val();
          var name = $('#salesman_export').val();
          var header = 'Party-wise Latest Stock Report'+'\n'+'Last Stock Date:-'+date+'\t'+'Stock Taken By:- '+name;
          return header;
        },
        filename:function(){
          var name = $('#party_id option:selected').text();
          var header = name+' Latest Stock Report';
          return header;
        },
      },
      {
        extend: 'pdfHtml5',
        action: function ( e, dt, node, config ) {
          newExportAction( e, dt, node, config );
        },
        exportOptions: {
          columns: [ 0, 2, 3, 4, 5, 6, 7]
        },
        title: function(){
          var date = $('#date_export').val();
          var name = $('#salesman_export').val();
          var header = 'Party-wise Latest Stock Report'+'\n'+'Last Stock Date:-'+date+'\t'+'Stock Taken By:- '+name;
          return header;
        }, 
        filename:function(){
          var name = $('#party_id option:selected').text();
          var header = name+' Latest Stock Report';
          return header;
        },
      },
      {
        extend: 'print',
        exportOptions: {
          columns: [ 0, 2, 3, 4, 5, 6, 7]
        },
        title: function(){
          var date = $('#date_export').val();
          var name = $('#salesman_export').val();
          var header = 'Party-wise Latest Stock Report';
          return header;
        },
        customize: function ( win ) {
          $(win.document.body).find( 'thead' ).prepend('<div class="header-print container"><div class="row">Last Stock Date:- ' + $('#date_export').val()+'</div></div>');
          $(win.document.body).find( 'thead' ).prepend('<div class="header-print container"><div class="row">Stock Taken By:- '+ $('#salesman_export').val() + '</div></div>');
        },
        filename:function(){
          var name = $('#party_id option:selected').text();
          var header = name+' Latest Stock Report';
          return header;
        },
      },
    ],
    "columnDefs": [
      {
        "targets": [ 2 ],
        "visible": false,
      },
      {
        "targets": [ 5 ],
        "visible": false
      },
      {
        "targets": [ 6 ],
        "visible": false
      }
    ],
  });
  party_wise_latest.buttons().container().appendTo('#exportBtn');

  var newExportAction = function (e, dt, button, config) {
    var self = this;
    var data = [];
    var count = 0;
    var columnsArray = [];
    columnsArray.push("Product Name", "Variant", "Unit", "Brand", "Category", "Quantity");
    var columns = JSON.stringify(columnsArray);
    $('#party_wise_latest').DataTable().rows({"search":"applied" }).every( function () {
      var row = {};
      row["id"] = ++count;
      row["product_name"] = this.data()[2];
      row["variant"] = this.data()[3];
      row["unit"] = this.data()[4];
      row["brand"] = this.data()[5];
      row["category"] = this.data()[6];
      row["quantity"] = this.data()[7];
      data.push(row);
    });
    exportAction(config, data, columns);
  };

  function exportAction(config, data, cols){
    $('#exportedData').val(JSON.stringify(data));
    $('#pageTitle').val(config.title);
    $('#reportName').val('stockreport');
    $('#columns').val(cols);
    var propertiesArray = [];
    propertiesArray.push("id","product_name", "variant", "unit", "brand", "category", "quantity");
    var properties = JSON.stringify(propertiesArray);
    $('#properties').val(properties);
    $('#pdf-generate').submit();
  }

  $("#party_id").select2({
    placeholder: "Select Parties",
    allowClear: true,
    ajax: {
      url: "{{domain_route('company.admin.stockreport.loadparties')}}",
      dataType: 'json',
      data: function(params) {
        return {
          term: params.term || '',
          page: params.page || 1
        }
      },
      cache: true
    }
  });

  // Party-wise Latest Stock Report
  $('#stockreports').on('submit', function (e) {
    event.preventDefault();
    var party_ids = $('#party_id').val();
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      type: "POST",
      url: "{{ domain_route('company.admin.stockreports') }}",
      data:{party_id: party_ids,},
      beforeSend: function (url, data) {
        $('#getReport').text('Please wait ...');
        $('#getReport').attr('disabled', true);
        $('#loader1').removeAttr('hidden');
        $('#party_wise_latest_box').addClass('box-opacity');
      },
      success: function (data) {
        party_wise_latest.clear().draw();
        $('#stock_salesman').remove();
        $('#stock_date').remove();
        // let counter = 0;
        if(data['salesman_name']!=null || data['date']!=null){
          $( "#party_wise_latest_filter" ).prepend("<div class='col-xs-6' id='stock_salesman' style='top: 15px;'><b>Stock Taken By:- "+data['salesman_name']+"</b></div>" );
          $('#salesman_export').val(data['salesman_name']);
          $('#date_export').val(data['date']);
          $( "#party_wise_latest_length" ).children().after("<div class='col-xs-6 pull-right' id='stock_date' style='top: 15px;'><b>Last Stock Date:- "+data['date']+"</b></div>" );
        }else{
          $('#salesman_export').val('');
          $('#date_export').val('');
          $( "#party_wise_latest_filter" ).prepend("<div class='col-xs-6' id='stock_salesman' style='top: 15px;'><b>Stock Taken By:- </b></div>" );
          $( "#party_wise_latest_length" ).children().after("<div class='col-xs-6 pull-right' id='stock_date' style='top: 15px;'><b>Last Stock Date:- </b></div>" );
        }
        $.each( data['data'], function( key, value ) {
          $.each( value, function( key, value1 ) {
            if($(this)[0].brand==null){
              party_wise_latest.row.add( [
                // ++counter,
                value1["count"],
                '<p><strong>'+value1["product"]+'</strong></p>'+'<span class="label label-primary cat brandcat" style="font-weight: bolder;">'+value1["category"]+'</span>',
                value1["product"],
                value1["variant"],
                value1["unit"],
                value1["brand"],
                value1["category"],
                value1["quantity"],
              ] ).draw();
            }else if($(this)[0].category==null){
              party_wise_latest.row.add( [
                value1["count"],
                '<p><strong>'+value1["product"]+'</strong></p>'+'<span class="label label-success brand brandcat" style="font-weight: bolder;">'+value1["brand"]+'</span>',
                value1["product"],
                value1["variant"],
                value1["unit"],
                value1["brand"],
                value1["category"],
                value1["quantity"],
              ] ).draw();
            }else if($(this)[0].category!=null && $(this)[0].brand!=null){
              party_wise_latest.row.add( [
                value1["count"],
                '<p><strong>'+value1["product"]+'</strong></p>'+'<span class="label label-success brand brandcat" style="font-weight: bolder;">'+value1["brand"]+'</span>'+'<span class="label label-primary cat brandcat" style="font-weight: bolder;">'+value1["category"]+'</span>',
                value1["product"],
                value1["variant"],
                value1["unit"],
                value1["brand"],
                value1["category"],
                value1["quantity"],
              ] ).draw();
            }else{
              party_wise_latest.row.add( [
                value1["count"],
                '<p><strong>'+value1["product"]+'</strong></p>'+'<span class="label label-success brand brandcat" style="font-weight: bolder;">'+value1["brand"]+'</span>'+'<span class="label label-primary cat brandcat" style="font-weight: bolder;">'+value1["category"]+'</span>',
                value1["product"],
                value1["variant"],
                value1["unit"],
                value1["brand"],
                value1["category"],
                value1["quantity"],
              ] ).draw();
            }
          });
        });
        alert("Report has been generated. Please view below.");
        $('#loader1').attr('hidden','hidden');
        $('#party_wise_latest_box').removeClass('box-opacity');
      },
      error: function (jqXHR) {
        $('#loader1').attr('hidden','hidden');
        $('#party_wise_latest_box').removeClass('box-opacity');
        $('#getReport').attr('disabled', false);
        $('#getReport').html("<i class='fa fa-book'></i> Get Report");
      },
      complete: function () {
        $('#loader1').attr('hidden','hidden');
        $('#party_wise_latest_box').removeClass('box-opacity');
        $('#getReport').attr('disabled', false);
        $('#getReport').html("<i class='fa fa-book'></i> Get Report");
      }
    });
  });

  // Single Party Historical Stock Report
  let salesman_wise = $('#salesman_wise').DataTable({
    "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6'>>"+
              "<'row'<'col-xs-4'><'col-xs-4'>>"+
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
    "columnDefs": [
    { "orderable": false, "targets": -1 }
    ],
    'searching': false,
    'sorting': false,
  });

  $("#party_id2").select2({
    placeholder: "Select Parties",
    allowClear: true,
    ajax: {
      url: "{{domain_route('company.admin.stockreport.loadparties')}}",
      dataType: 'json',
      data: function(params) {
        return {
          term: params.term || '',
          page: params.page || 1
        }
      },
      cache: true
    }
  });

  // Single Party Historical Report 
  $('#stockreports2').on('submit', function (e) {
    event.preventDefault();
    var party_ids = $('#party_id2').val();  

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      type: "POST",
      url: "{{ domain_route('company.admin.stockreports2') }}",
      data:{party_id: party_ids,},
      beforeSend: function (url, data) 
      { 
        $('#getReport2').text('Please wait ...');
        $('#getReport2').attr('disabled', true);
        $('#loader2').removeAttr('hidden');
        $('#party_wise_hist_box').addClass('box-opacity');
      },
      success: function (data) {
        alert(data['msg']);
        if(data['row'] != null){
          location.reload();
        }
        $('#getReport2').attr('disabled', false);
        $('#getReport2').html("<i class='fa fa-book'></i> Get Report");
        $('#loader2').attr('hidden','hidden');
        $('#party_wise_hist_box').removeClass('box-opacity');
      },
      error: function (jqXHR) {
        $('#getReport2').attr('disabled', false);
        $('#getReport2').html("<i class='fa fa-book'></i> Get Report");
        $('#loader2').attr('hidden','hidden');
        $('#party_wise_hist_box').removeClass('box-opacity');
      },
      complete: function () {
        $('#getReport2').attr('disabled', false);
        $('#getReport2').html("<i class='fa fa-book'></i> Get Report");
        $('#loader2').attr('hidden','hidden');
        $('#party_wise_hist_box').removeClass('box-opacity');
      }
    });
  });

  // Parties Latest Stock Report by Date
  // $('#latest_by_date').DataTable({
  //    "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6'>>"+
  //             "<'row'<'col-xs-4'><'col-xs-4'>>"+
  //             "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
  //   "columnDefs": [
  //   { "orderable": false, "targets": -1 }
  //   ],
  //   'searching':false,
  //   'sorting': false,
  // });
  var reloadTable;
  function initializeOrderDT(){
    reloadTable = $('#latest_by_date').DataTable({
      "stateSave": true,
      language: { search: "" },
      "order": [[ 0, "desc" ]],
      "serverSide": true,
      "processing": false,
      'searching': false,
      "paging": true,
      "columnDefs": [
        {
          "orderable": false,
          "targets":[-1],
        },],
      "ajax":{
        "url": "{{ domain_route('company.admin.stockreportsdt') }}",
        "dataType": "json",
        "type": "POST",
        "data":{  
          _token: "{{csrf_token()}}", 
        },
        beforeSend:function(url, data){
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
          $('.tips').tooltip();
        },
        error:function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
          $('.tips').tooltip();
        },
        complete:function(data){
          $('.tips').tooltip();
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        }
      },
      "columns": [{"data": "date_generated"},
        {"data":  "report_type"},
        {"data": "date_range"},
        {"data": "action"},
      ],
      drawCallback:function(settings)
      {
        $('[data-toggle="tooltip"]').tooltip({
          placement : 'right',
          container: 'body'
        });
        $('#next_refresh').val(settings.json.next_refresh);
      }
    });
  }
  
  setInterval(() => {
    if($('#next_refresh').val() > 0){
      reloadTable.ajax.reload();
    }
  }, 30000);


  $('#party_type').multiselect({
    placeholder: "Select Party Type",
    selectAll: true,
  });

  $('#handled_party_id').multiselect({
    placeholder: "Select Parties",
    selectAll: true,
    search: true,
  });

  $('#party_type').change(function(){
    let sel_party_types = $('#party_type').val();
    
    if(sel_party_types.length>0){
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ domain_route('company.admin.fetchpartylist') }}",
        type: "GET",
        data:
        {
          sel_party_types : sel_party_types,
        },
        success:function(data) {
          data = JSON.parse(data);
          $('select[id="handled_party_id"]').empty();
          $('select[id="handled_party_id"]').multiselect('reload');
          data.forEach(element => {
            $('select[id="handled_party_id"]').append('<option value="'+ element['id'] +'" data-client_type="'+ element['client_type'] +'" selected>'+ element['company_name'] +'</option>');
          });
          $('select[id="handled_party_id"]').multiselect('reload');
        }
      });
    }else{
      $('select[id="handled_party_id"]').empty();
      $('select[id="handled_party_id"]').multiselect('reload');
    }
  });

  $('#stockreport3').on('submit', function (e) {
    e.preventDefault();
    let current = $(this);
    var party_ids = JSON.stringify($('#handled_party_id').val());  
    @if(config('settings.ncal')==0)
      var startDate = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    @else
      var startDate = $('#start_edate').val();
      var endDate = $('#end_edate').val();
    @endif
    var report_type = $('input[name=reptype]:checked').val();

    current.find('#startdate').val(startDate);
    current.find('#enddate').val(endDate);
    current.find('.hiddenPartyId').val(party_ids);
    if(moment(endDate).diff(moment(startDate), "year") >= 1){
      alert("Please choose time range less than 1 year ");
      return false;
    }
    current.unbind("submit").submit();
    // $.ajax({
    //   headers: {
    //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    //   },
    //   type: "POST",
    //   url: "{{ domain_route('company.admin.stockreports3') }}",
    //   data:{
    //     party_id: party_ids,
    //     startDate : startDate,
    //     endDate : endDate,
    //     report_type: report_type,
    //   },
    //   beforeSend: function (url, data) { 
    //     $('#getReport3').text('Please wait ...');
    //     $('#getReport3').attr('disabled', true);
    //     $('#loader3').removeAttr('hidden');
    //     $('#party_wise_ltst_box').addClass('box-opacity');
    //   },
    //   success: function (data) {
    //     alert(data['msg']);
    //     $('#getReport3').attr('disabled', false);
    //     $('#getReport3').html("<i class='fa fa-book'></i> Get Report");
    //     $('#loader3').attr('hidden', 'hidden');
    //     $('#party_wise_ltst_box').removeClass('box-opacity');
    //     if(data['status']){
    //       location.reload();
    //     }
    //   },
    //   error: function (jqXHR) {
    //     $('#getReport3').attr('disabled', false);
    //     $('#getReport3').html("<i class='fa fa-book'></i> Get Report");
    //     $('#loader3').attr('hidden', 'hidden');
    //     $('#party_wise_ltst_box').removeClass('box-opacity');
    //   },
    //   complete: function () {
    //     $('#getReport3').attr('disabled', false);
    //     $('#getReport3').html("<i class='fa fa-book'></i> Get Report");
    //     $('#loader3').attr('hidden', 'hidden');
    //     $('#party_wise_ltst_box').removeClass('box-opacity');
    //   }
    // });
  });

  /***
   * DO NOT DELETE BELOW CODE REQUIRED FOR FUTURE
  ***/
  // document.addEventListener('scroll', function (event) {
  //   if (event.target === $('#ms-list-2').children()[1]) { 
  //     let offsetCounter =0;
  //     $('#handled_party_id').change(function(){
  //     $('#offsetCounter').val(offsetCounter);
  //     if(true){
  //       $.ajax({
  //         headers: {
  //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //         },
  //         url: "{{domain_route('company.admin.stockreport.loadparties')}}",
  //         dataType: 'json',
  //         type: "GET",
  //         data:
  //         {
  //           page: offsetCounter,
  //           term: null,
  //         },
  //         success:function(data) {
  //           $('#offsetCounter').val(offsetCounter);
  //           // data = JSON.parse(data.results);
  //           $('select[id="handled_party_id"]').empty();
  //           $('select[id="handled_party_id"]').multiselect('reload');
  //           data.results.map(element => {
  //             $('select[id="handled_party_id"]').append('<option value="'+ element.id +'" selected>'+ element.text +'</option>');
  //           });
  //           $('select[id="handled_party_id"]').multiselect('reload');
  //         }
  //       });
  //     }else{
  //       $('select[id="handled_party_id"]').empty();
  //       $('select[id="handled_party_id"]').multiselect('reload');
  //     }
  //     });
  //   }
  // }, true);

  @if(config('settings.ncal')==0)
    $('.fromDate').datepicker({
      format: "yyyy-mm-dd",
      endDate: new Date(),
      autoclose: true,
      orientation: 'bottom'
    });
    $('document').ready(function () {
      var start = moment().subtract(29, 'days');
      var end = moment();
  
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
  
    });
  @else
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
      }
    });
    $('#end_ndate').nepaliDatePicker({
      onChange:function(){
        $('#end_edate').val(BS2AD($('#end_ndate').val()));
        if($('#end_ndate').val()<$('#start_ndate').val()){
          $('#start_ndate').val($('#end_ndate').val());
          $('#start_edate').val(BS2AD($('#end_ndate').val()));
        }
      }
    });
  @endif
  //responsive 
  $('#reportrange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("stockreportdateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("stockreportdateposition");
    }
    else {   
      $(".daterangepicker").removeClass("stockreportdateposition");
    }
  });
</script>
@endsection