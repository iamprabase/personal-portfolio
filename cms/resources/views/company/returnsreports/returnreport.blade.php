@extends('layouts.company')
@section('title', 'Return Report')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/dist/css/delta.css') }}" />
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
    width: fit-content;
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

  #loader4 {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 99;
    width: 900px;
  }

  .highcharts-container{
    outline: none;
  }

  .highcharts-credits{
    display: none;
  }

  .sendBtn{
    position: absolute;
    top: 80px;
  }
  .sendBtnProdParty{
    position: relative;
    margin-top: 24px;
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
    <ul class="nav nav-tabs" id="reportstabs">
      <li class="active"><a href="#pi-plot" data-toggle="tab"> Reason For Most Returned Products</a></li>
      <li><a href="#bar-plot" data-toggle="tab">Top 10 Most Returned Products</a></li>
      <li><a href="#party-wise" data-toggle="tab"> Party-wise Returns Report</a></li>
      <li><a href="#product-party-wise" data-toggle="tab"> Product-Party-wise Returns Report</a></li>
    </ul>
    <div class="tab-content">
      <div class="active tab-pane" id="pi-plot">
        @include('company.returnsreports.pi-plotreturnreports')
      </div>
      <div class="tab-pane" id="bar-plot">
        @include('company.returnsreports.bar-plotreturnreports')
      </div>
      <div class="tab-pane" id="party-wise">
        @include('company.returnsreports.excelreturnreports')
      </div>
      <div class="tab-pane product-party-wise" id="product-party-wise">
        @include('company.returnsreports.productpartywisereturnreports')
      </div>
    </div>
  </div>
  <form method="post" action="{{domain_route('company.admin.returnsReport.customPdfExport')}}"
      class="pdf-export-form hidden" id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <input type="text" name="reportName" class="reportName" id="reportName">
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
  </form>
  <input type="hidden" id="salesman_export" value="">
  <input type="hidden" id="date_export" value="">
</section>

<div class="modal fade" id="viewDetails" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xs small-modal" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Product Name Goes Here</h4>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
<script src="{{asset('assets/plugins/highcharts/code/highchartsv8.js')}}"></script>
<script src="{{asset('assets/plugins/highcharts/code/data.js')}}"></script>
<script src="{{asset('assets/plugins/highcharts/modules/drilldown.js')}}"></script>
<script src="{{asset('assets/plugins/highcharts/modules/exporting.js')}}"></script>
<script src="{{asset('assets/plugins/highcharts/modules/export-data.js')}}"></script>
<script src="{{asset('assets/plugins/highcharts/code/accessibility.js')}}"></script>
<script src="{{asset('assets/plugins/chartjs/chartjs.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if(activeTab){
        $('#reportstabs a[href="' + activeTab + '"]').tab('show');
    }
    initializeOrderDT();
  });

  $('[data-toggle="tooltip"]').tooltip({
    placement : 'right',
    container: 'body'
  });
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
        "url": "{{ domain_route('company.admin.returnreportsdt') }}",
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

  $('#reportFormSubmit').on('submit', function (e) {
    event.preventDefault();
    let current = $(this);
    var party_ids = $('#handled_party_id').val();  
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
    //   url: "{{ domain_route('company.admin.postReturnsReport') }}",
    //   data:{
    //     party_id: party_ids,
    //     report_type: report_type,
    //     startDate : startDate,
    //     endDate : endDate,
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
    //     if(data['status']==200){
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
      $(".daterangepicker").addClass("returnreportdateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("returnreportdateposition");
    }
    else {   
      $(".daterangepicker").removeClass("returnreportdateposition");
    }
  });
</script>
<script type="text/javascript">
  $('[data-toggle="tooltip"]').tooltip({
    placement : 'right',
    container: 'body'
  });

  $('#barPlotPartyType').multiselect({
    placeholder: "Select Party Type",
    selectAll: true,
  });

  $('#barPlotHandledPartyId').multiselect({
    placeholder: "Select Parties",
    selectAll: true,
    search: true,
  });

  $('#barPlotPartyType').change(function(){
    let sel_party_types = $('#barPlotPartyType').val();
    
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
          $('select[id="barPlotHandledPartyId"]').empty();
          $('select[id="barPlotHandledPartyId"]').multiselect('reload');
          data.forEach(element => {
            $('select[id="barPlotHandledPartyId"]').append('<option value="'+ element['id'] +'" data-client_type="'+ element['client_type'] +'" selected>'+ element['company_name'] +'</option>');
          });
          $('select[id="barPlotHandledPartyId"]').multiselect('reload');
        }
      });
    }else{
      $('select[id="barPlotHandledPartyId"]').empty();
      $('select[id="barPlotHandledPartyId"]').multiselect('reload');
    }
  });

  @if(config('settings.ncal')==0)
    
    $('document').ready(function () {
      var start = moment().subtract(29, 'days');
      var end = moment();
  
      function cb(start, end) {
          $('#barPlotReportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
          $('#startdate').val(start.format('MMMM D, YYYY'));
          $('#enddate').val(end.format('MMMM D, YYYY'));
      }
  
      $('#barPlotReportRange').daterangepicker({
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
    $('#barPlotStartNdate').val(lastmonthdate);
    $('#barPlotEndNdate').val(ntoday);
    $('#barPlotStartEdate').val(BS2AD($('#barPlotStartNdate').val()));
    $('#barPlotEndEdate').val(BS2AD($('#barPlotEndNdate').val()));
    $('#barPlotStartNdate').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange:function(){
        $('#barPlotStartEdate').val(BS2AD($('#barPlotStartNdate').val()));
        if($('#barPlotStartNdate').val()>$('#barPlotEndNdate').val()){
          $('#barPlotEndNdate').val($('#barPlotStartNdate').val());
          $('#barPlotEndEdate').val(BS2AD($('#barPlotStartNdate').val()));
        }
      }
    });
    $('#barPlotEndNdate').nepaliDatePicker({
      onChange:function(){
        $('#barPlotEndEdate').val(BS2AD($('#barPlotEndNdate').val()));
        if($('#barPlotEndNdate').val()<$('#barPlotStartNdate').val()){
          $('#barPlotStartNdate').val($('#barPlotEndNdate').val());
          $('#barPlotStartEdate').val(BS2AD($('#barPlotEndNdate').val()));
        }
      }
    });
  @endif
  //responsive 
  $('#barPlotReportRange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("returnreportdateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("returnreportdateposition");
    }
    else {   
      $(".daterangepicker").removeClass("returnreportdateposition");
    }
  });

  $('#barPlotForm').on('submit', function (e) {
    event.preventDefault();
    let party_ids = JSON.stringify($('#barPlotHandledPartyId').val());  
    @if(config('settings.ncal')==0)
      let startDate = $('#barPlotReportRange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      let endDate = $('#barPlotReportRange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    @else
      let startDate = $('#barPlotStartEdate').val();
      let endDate = $('#barPlotEndEdate').val();
    @endif
    generateBarChart(party_ids, startDate, endDate);
  });
   $('#barPlotGetReport').click();
  // logic to get new data
  function generateBarChart(partyIds, getStartDate, getEndDate) {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      type: "POST",
      url: "{{ domain_route('company.admin.barplotreturnsReport') }}",
      data:{
        party_id: partyIds,
        startDate : getStartDate,
        endDate : getEndDate,
      },
      beforeSend: function (url, data) { 
        $('#barPlotGetReport').text('Please wait ...');
        $('#barPlotGetReport').attr('disabled', true);
        $('#barPlotLoader').removeAttr('hidden');
        $('#bar_plot_ltst_box').addClass('box-opacity');
      },
      success: function (data) {
        $('#barPlotGetReport').attr('disabled', false);
        $('#barPlotGetReport').html("<i class='fa fa-book'></i> Get Report");
        $('#barPlotLoader').attr('hidden', 'hidden');
        $('#bar_plot_ltst_box').removeClass('box-opacity');
        
        // myChart.destroy();
        window.myBar.destroy();
        product_names = (data['product_names'].length>0)?data['product_names']: [];
        product_qty_sum = (JSON.parse(data['sum']).length>0)?JSON.parse(data['sum']): [0, 10];
        units = (data['units'].length>0)?data['units']: [];

        barChartData = {
          labels: product_names,
          datasets: [{
              label: 'Quantity',
              backgroundColor: "rgba(14,118,118,1)",
              data: product_qty_sum,
              barPercentage: 0.5,
              barThickness: 6,
              maxBarThickness: 8,
              minBarLength: 2,
          },]
        };

        let ctx = document.getElementById("mycanvas").getContext("2d");
        window.myBar = new Chart(ctx, {
          type: 'bar',
          data: barChartData,
          options: {
            tooltips: {
              mode: 'label',
                callbacks: {
                label: function(tooltipItem, data) {
                  return data.datasets[tooltipItem.datasetIndex].label + ": " + tooltipItem.yLabel+" "+units[tooltipItem.index];
                },
              }
            },
            elements: {
              rectangle: {
                  borderWidth: 2,
                  borderColor: 'rgb(0, 255, 0)',
                  borderSkipped: 'bottom'
              }
            },
            responsive: true,
            title: {
              display: true,
              text: 'Top 10 Most Retuned Products',
              fontStyle: 'bold',
              fontSize: 20,
            },
            scales: {
              xAxes: [{
                display: true,
                scaleLabel: {
                  display: true,
                  labelString: 'Product Names, Variant',
                  fontStyle: "bold",
                  fontSize: 20,
                }
              }],
              yAxes: [{
                display: true,
                scaleLabel: {
                  display: true,
                  labelString: 'Quantity',
                  fontStyle: "bold",
                  fontSize: 20,
                },
                ticks: {
                  beginAtZero: true,
                  steps: product_qty_sum[product_qty_sum.length-1]*10,
                  max: product_qty_sum[0] + 300,
                }
              }],
            },
            pointLabels :{
              fontStyle: "bold",
            },
          }
        });

      },
      error: function (jqXHR) {
        $('#barPlotGetReport').attr('disabled', false);
        $('#barPlotGetReport').html("<i class='fa fa-book'></i> Get Report");
        $('#barPlotLoader').attr('hidden', 'hidden');
        $('#party_wise_ltst_box').removeClass('box-opacity');
      },
      complete: function (data) {
        $('#barPlotGetReport').attr('disabled', false);
        $('#barPlotGetReport').html("<i class='fa fa-book'></i> Get Report");
        $('#barPlotLoader').attr('hidden', 'hidden');
        $('#party_wise_ltst_box').removeClass('box-opacity');
      }
    });
  };
</script>
<script type="text/javascript">
  $('[data-toggle="tooltip"]').tooltip({
    placement : 'right',
    container: 'body'
  });

  $('#piPlotPartyType').multiselect({
    placeholder: "Select Party Type",
    selectAll: true,
  });

  $('#piPlotHandledPartyId').multiselect({
    placeholder: "Select Parties",
    selectAll: true,
    search: true,
  });

  $('#piPlotPartyType').change(function(){
    let sel_party_types = $('#piPlotPartyType').val();
    
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
          $('select[id="piPlotHandledPartyId"]').empty();
          $('select[id="piPlotHandledPartyId"]').multiselect('reload');
          data.forEach(element => {
            $('select[id="piPlotHandledPartyId"]').append('<option value="'+ element['id'] +'" data-client_type="'+ element['client_type'] +'" selected>'+ element['company_name'] +'</option>');
          });
          $('select[id="piPlotHandledPartyId"]').multiselect('reload');
        }
      });
    }else{
      $('select[id="piPlotHandledPartyId"]').empty();
      $('select[id="piPlotHandledPartyId"]').multiselect('reload');
    }
  });

  @if(config('settings.ncal')==0)
    
    $('document').ready(function () {
      var start = moment().subtract(29, 'days');
      var end = moment();
  
      function cb(start, end) {
          $('#piPlotReportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
          $('#startdate').val(start.format('MMMM D, YYYY'));
          $('#enddate').val(end.format('MMMM D, YYYY'));
      }
  
      $('#piPlotReportRange').daterangepicker({
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
    $('#piPlotStartNdate').val(lastmonthdate);
    $('#piPlotEndNdate').val(ntoday);
    $('#piPlotStartEdate').val(BS2AD($('#piPlotStartNdate').val()));
    $('#piPlotEndEdate').val(BS2AD($('#piPlotEndNdate').val()));
    $('#piPlotStartNdate').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange:function(){
        $('#piPlotStartEdate').val(BS2AD($('#piPlotStartNdate').val()));
        if($('#piPlotStartNdate').val()>$('#piPlotEndNdate').val()){
          $('#piPlotEndNdate').val($('#piPlotStartNdate').val());
          $('#piPlotEndEdate').val(BS2AD($('#piPlotStartNdate').val()));
        }
      }
    });
    $('#piPlotEndNdate').nepaliDatePicker({
      onChange:function(){
        $('#piPlotEndEdate').val(BS2AD($('#piPlotEndNdate').val()));
        if($('#piPlotEndNdate').val()<$('#piPlotStartNdate').val()){
          $('#piPlotStartNdate').val($('#piPlotEndNdate').val());
          $('#piPlotStartEdate').val(BS2AD($('#piPlotEndNdate').val()));
        }
      }
    });
  @endif
  //responsive 
  $('#piPlotReportRange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("returnreportdateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("returnreportdateposition");
    }
    else {   
      $(".daterangepicker").removeClass("returnreportdateposition");
    }
  });

  $('#piPlotForm').on('submit', function (e) {
    event.preventDefault();
    let party_ids = JSON.stringify($('#piPlotHandledPartyId').val());  
    @if(config('settings.ncal')==0)
      let startDate = $('#piPlotReportRange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      let endDate = $('#piPlotReportRange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    @else
      let startDate = $('#piPlotStartEdate').val();
      let endDate = $('#piPlotEndEdate').val();
    @endif
    generatepiChart(party_ids, startDate, endDate);
  });
</script>
<script>

  // Create the chart
  window.piePlottedData = new Highcharts.chart('mycanvas2', {
    chart: {
      type: 'pie'
    },
    title: {
      text: 'Count Analysis of Product Returns with their Reason'
    },
    subtitle: {
      text: 'Click the slices to view product details of each reason.'
    },
    plotOptions: {
      series: {
        dataLabels: {
          enabled: true,
          format: '{point.name}: {point.y}'
        }
      }
    },

    tooltip: {
      headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
      pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}<br/>'
    },

    series: [
      {
        name: "Count",
        colorByPoint: true,
        data: @json($productReturnsReasonCountQuery, JSON_NUMERIC_CHECK)
      }
    ],
    drilldown: {
      series: @json($productReturnsDetailsCountQuery, JSON_NUMERIC_CHECK)
    },
    exporting: {
    buttons: {
      contextButton: {
        menuItems: ["printChart",
                    "separator",
                    "downloadPNG",
                    "downloadJPEG",
                    "downloadPDF"]
      }
    }
  }
  });

  function generatepiChart(partyIds, getStartDate, getEndDate) {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      type: "POST",
      url: "{{ domain_route('company.admin.piplotreturnsReport') }}",
      data:{
        party_id: partyIds,
        startDate : getStartDate,
        endDate : getEndDate,
      },
      beforeSend: function (url, data) { 
        $('#piPlotGetReport').text('Please wait ...');
        $('#piPlotGetReport').attr('disabled', true);
        $('#piPlotLoader').removeAttr('hidden');
        $('#pi_plot_ltst_box').addClass('box-opacity');
      },
      success: function (data) {
        $('#piPlotGetReport').attr('disabled', false);
        $('#piPlotGetReport').html("<i class='fa fa-book'></i> Get Report");
        $('#piPlotLoader').attr('hidden', 'hidden');
        $('#pi_plot_ltst_box').removeClass('box-opacity');
        
        // myChart.destroy();
        window.piePlottedData.destroy();
        window.piePlottedData = new Highcharts.chart('mycanvas2', {
          chart: {
            type: 'pie'
          },
          title: {
            text: 'Count Analysis of Product Returns with their Reason'
          },
          subtitle: {
            text: 'Click the slices to view product details of each reason.'
          },
          plotOptions: {
            series: {
              dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y}'
              }
            }
          },

          tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}<br/>'
          },

          series: [
            {
              name: "Count",
              colorByPoint: true,
              data: JSON.parse(data['productReturnsReasonCountQuery'])
            }
          ],
          drilldown: {
            series: JSON.parse(data['productReturnsDetailsCountQuery'])
          }
        });
        

      },
      error: function (jqXHR) {
        $('#piPlotGetReport').attr('disabled', false);
        $('#piPlotGetReport').html("<i class='fa fa-book'></i> Get Report");
        $('#piPlotLoader').attr('hidden', 'hidden');
        $('#party_wise_ltst_box').removeClass('box-opacity');
      },
      complete: function (data) {
        $('#piPlotGetReport').attr('disabled', false);
        $('#piPlotGetReport').html("<i class='fa fa-book'></i> Get Report");
        $('#piPlotLoader').attr('hidden', 'hidden');
        $('#party_wise_ltst_box').removeClass('box-opacity');
      }
    });
  };
  
  $('#piPlotGetReport').click();
</script>
<script type="text/javascript">

  $('#product_party_wise_partytype').multiselect({
    placeholder: "Select Party Type",
    selectAll: true,
  });

  $('#product_party_wise_partylist').multiselect({
    placeholder: "Select Parties",
    selectAll: true,
    search: true,
  });

  $('#product_party_wise_partytype').change(function(){
    let sel_product_party_wise_partytypes = $('#product_party_wise_partytype').val();
    
    if(sel_product_party_wise_partytypes.length>0){
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ domain_route('company.admin.fetchpartylist') }}",
        type: "GET",
        data:
        {
          sel_party_types : sel_product_party_wise_partytypes,
        },
        success:function(data) {
          data = JSON.parse(data);
          $('select[id="product_party_wise_partylist"]').empty();
          $('select[id="product_party_wise_partylist"]').multiselect('reload');
          data.forEach(element => {
            $('select[id="product_party_wise_partylist"]').append('<option value="'+ element['id'] +'" data-client_type="'+ element['client_type'] +'" selected>'+ element['company_name'] +'</option>');
          });
          $('select[id="product_party_wise_partylist"]').multiselect('reload');
        }
      });
    }else{
      $('select[id="product_party_wise_partylist"]').empty();
      $('select[id="product_party_wise_partylist"]').multiselect('reload');
    }
  });

  $('#product-party-wise-submit').on('click', function (e) {
    $('#product-party-wise-table').DataTable().destroy();
    initializeDT();
  });

  @if(config('settings.ncal')==0)
    $('.fromDate').datepicker({
      format: "yyyy-mm-dd",
      endDate: new Date(),
      autoclose: true,
      orientation: 'bottom'
    });
    // $('document').ready(function () {
      var start = moment().subtract(29, 'days');
      var end = moment();
      $('#product_party_wise_ltst_box').find('#productpartywise_hidden_startdate').val(start.format('YYYY-MM-DD'));
      $('#product_party_wise_ltst_box').find('#productpartywise_hidden_enddate').val(end.format('YYYY-MM-DD'));
  
      function cb(start, end) {
          $('#product_party_wise_reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
          $('#product_party_wise_ltst_box').find('#productpartywise_hidden_startdate').val(start.format('YYYY-MM-DD'));
          $('#product_party_wise_ltst_box').find('#productpartywise_hidden_enddate').val(end.format('YYYY-MM-DD'));
      }
  
      $('#product_party_wise_reportrange').daterangepicker({
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
  
    // });
  @else
    var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
    var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
    $('#productpartywise_start_ndate').val(lastmonthdate);
    $('#productpartywise_end_ndate').val(ntoday);
    $('#productpartywise_hidden_start_edate').val(BS2AD($('#productpartywise_start_ndate').val()));
    $('#productpartywise_hidden_end_edate').val(BS2AD($('#productpartywise_end_ndate').val()));
    $('#productpartywise_start_ndate').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange:function(){
        $('#productpartywise_hidden_start_edate').val(BS2AD($('#productpartywise_start_ndate').val()));
        if($('#productpartywise_start_ndate').val()>$('#productpartywise_end_ndate').val()){
          $('#productpartywise_end_ndate').val($('#productpartywise_start_ndate').val());
          $('#productpartywise_hidden_end_edate').val(BS2AD($('#productpartywise_start_ndate').val()));
        }
      }
    });
    $('#productpartywise_end_ndate').nepaliDatePicker({
      onChange:function(){
        $('#productpartywise_hidden_end_edate').val(BS2AD($('#productpartywise_end_ndate').val()));
        if($('#productpartywise_end_ndate').val()<$('#productpartywise_start_ndate').val()){
          $('#productpartywise_start_ndate').val($('#productpartywise_end_ndate').val());
          $('#productpartywise_hidden_start_edate').val(BS2AD($('#productpartywise_end_ndate').val()));
        }
      }
    });
  @endif
  initializeDT();

  function initializeDT(){
    var party_ids = $('#product_party_wise_partylist').val();  
    @if(config('settings.ncal')==0)
      var startDate = $('#product_party_wise_ltst_box').find('#productpartywise_hidden_startdate').val();
      var endDate = $('#product_party_wise_ltst_box').find('#productpartywise_hidden_enddate').val();
    @else
      var startDate = $('#productpartywise_hidden_start_edate').val();
      var endDate = $('#productpartywise_hidden_end_edate').val();
    @endif
    var table = $('#product-party-wise-table').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [[ 1, "desc" ]],
      "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
      "<'row'<'col-xs-6'><'col-xs-6'>>" +
      "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
      buttons: [
        {
          extend: 'pdfHtml5', 
          title: 'Product-Party-wise Returns Report', 
          orientation:'portrait',
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'excelHtml5', 
          title: 'Product-Party-wise Returns Report',
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'print', 
          title: 'Product-Party-wise Returns Report',
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.productpartywisereturnsreport') }}",
        "dataType": "json",
        "type": "POST",
        "data":{ 
          _token: "{{csrf_token()}}", 
          party_id: party_ids,
          startDate : startDate,
          endDate : endDate,
        },
        beforeSend:function(){
          $('#product-party-wise-submit').text('Please wait ...');
          $('#product-party-wise-submit').attr('disabled', true);
          $('#loader4').removeAttr('hidden');
          $('#product_party_wise_ltst_box').addClass('box-opacity');
        },
        error:function(){
          $('#product-party-wise-submit').attr('disabled', false);
          $('#product-party-wise-submit').html("<i class='fa fa-book'></i> Get Report");
          $('#loader4').attr('hidden', 'hidden');
          $('#product_party_wise_ltst_box').removeClass('box-opacity');
        },
        complete:function(){
          $('#product-party-wise-submit').attr('disabled', false);
          $('#product-party-wise-submit').html("<i class='fa fa-book'></i> Get Report");
          $('#loader4').attr('hidden', 'hidden');
          $('#product_party_wise_ltst_box').removeClass('box-opacity');
        }
      },
      "columns": [
        { "data": "product_name" },
        { "data": "quantity" },
      ],
    });
    table.buttons().container()
        .appendTo('#product_party_wiseexports');
  }

  var oldExportAction = function (self, e, dt, button, config) {
    if(button[0].className.indexOf('buttons-excel') >= 0) {
      if($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
      }else{
        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
      }
    }else if(button[0].className.indexOf('buttons-pdf') >= 0) {
      if($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
      }else{
        $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
      }
    }else if(button[0].className.indexOf('buttons-print') >= 0) {
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
      data.length = 9999;
      dt.one('preDraw', function (e, settings) {
        if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
          // customExportAction(dt, data, config, settings);
          var columnsArray = [];
          columnsArray.push("Product Name", "Quantity");
          var columns = JSON.stringify(columnsArray);
          $.each(settings.json.data, function(key, htmlContent){
            settings.json.data[key].id = key+1;
            settings.json.data[key].quantity = $(settings.json.data[key].quantity)[0].textContent;
          });
          var propertiesArray = [];
          propertiesArray.push("id","product_name", "quantity");
          customExportAction(config, settings.json.data, columns, 'party-wise-returns', propertiesArray);
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
  //responsive 
  $('#product_party_wise_reportrange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("returnreportdateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("returnreportdateposition");
    }
    else {   
      $(".daterangepicker").removeClass("returnreportdateposition");
    }
  });

  $('#product-party-wise-table').on('click', '.detailView', function(){
    let currentEl = $(this);
    let quantity = currentEl.text();
    let productName = currentEl.data('product_name');
    let productId = currentEl.data('product_id');
    let startDate = currentEl.data('startdate');
    let endDate = currentEl.data('enddate');
    let selClientIds = JSON.stringify(currentEl.data('clientids'));

    $.ajax({
      "url": "{{domain_route('company.admin.returnsreport.getDetailView')}}",
      "method": "POST",
      "data":{
        _token: "{{csrf_token()}}",
        "productId": productId,
        "startDate": startDate,
        "endDate": endDate,
        "clientIds": selClientIds,
      },
      beforeSend:function(){
        $('#product-party-wise-submit').attr('disabled', true);
        $('#loader4').removeAttr('hidden');
        $('#product_party_wise_ltst_box').addClass('box-opacity');
      },
      success: function(data){
        $('#product_party_wise_ltst_box').removeClass('box-opacity');
        $('#viewDetails').modal('show');
        $('.modal-title').html(productName);
        $('.modal-body').html(data['view']);
        $('.modal-body').find('.box-title').html(`Total Quantity: ${quantity}`);
        let dTable = $('.detail-view-table').DataTable({
          dom: "Bfrtip",
          responsive: true,
          "order": [[ 2, "desc" ]],
          buttons: [
            {
              extend: 'pdfHtml5', 
              title: productName +'-Party-wise Returns Report', 
              action: function ( e, dt, node, config ) {
                oldExportAction( e, dt, node, config );
              },
              orientation:'portrait',
            },
            {
              extend: 'excelHtml5', 
              title: productName +'-Party-wise Returns Report',
            },
            {
              extend: 'print', 
              title: productName +'-Party-wise Returns Report',
            },
          ],
        }); 
        dTable.buttons().container()
        .appendTo('.detail-view-exports');
        $('#product-party-wise-submit').attr('disabled', false);
        $('#loader4').attr('hidden', 'hidden');
      },
      error:function(xhr, textStatus){
        $('#product-party-wise-submit').attr('disabled', false);
        $('#loader4').attr('hidden', 'hidden');
        $('#product_party_wise_ltst_box').removeClass('box-opacity');
      },
      complete:function(){
        $('#product-party-wise-submit').attr('disabled', false);
        $('#loader4').attr('hidden', 'hidden');
        $('#product_party_wise_ltst_box').removeClass('box-opacity');
      }
    });
  });

  var oldExportAction = function (e, dt, button, config) {
    var self = this;
    var data = [];
    var count = 0;
    var columnsArray = [];
    columnsArray.push("Variant Name", "Returned By", "Date", "Quantity", "Reason");
    var columns = JSON.stringify(columnsArray);
    $('#detail-view-table').DataTable().rows({"search":"applied" }).every( function () {
      var row = {};
      row["id"] = ++count;
      row["variant_name"] = this.data()[0];
      row["returned_by"] = this.data()[1];
      row["date"] = this.data()[2];
      row["quantity"] = this.data()[3];
      row["reason"] = this.data()[4];
      data.push(row);
    });
    var propertiesArray = [];
    propertiesArray.push("id","variant_name", "returned_by", "date", "quantity", "reason");
    customExportAction(config, data, columns, 'returnsDetailView', propertiesArray);
  };

  function customExportAction(config, data, cols, reportName, propArray){
    $('#exportedData').val(JSON.stringify(data));
    $('#pageTitle').val(config.title);
    $('#reportName').val(reportName);
    $('#columns').val(cols);
    var properties = JSON.stringify(propArray);
    $('#properties').val(properties);
    $('#pdf-generate').submit();
  }
</script>
@endsection