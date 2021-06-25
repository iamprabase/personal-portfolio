
@extends('layouts.company') 
@section('title', 'Analytics')
@section('title', 'Analytics')
@section('stylesheets')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/apexchart/apexcharts.css') }}"/>
<style>

  .box-loader{
      opacity: 0.5;
    }
 
  .clickable-row {
    cursor: pointer;
  }

  .retimg {
    vertical-align: initial;
    width: 25px;
  }

  .small-box {
    background-color: #0b7676 !important;
    color: #ffffff;
  }

  .bg-info {
      background-color: #17a2b8!important;
  }

  #qtyloader1, #amtloader1, #partiesamtloader1, #beatsloader1, #piechartloader1, #linegraph1_loader, #linegraph2_loader, #timespentvst_loader, #expenseloader{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 99;
  }

  #nepCalDiv {
    width: 250px;
  }

  /* #lineCharts{
    width:1075px !important;
    min-height:285px !important;
    max-height:350px !important;
  }

  #lineCharts2{
    width:1075px !important;
    min-height:285px !important;
    max-height:350px !important;
  }

  #barChart{
    min-height:1075px !important;
    max-height:350px !important;
  }

  #pieChart{
    min-height:1075px !important;
    max-height:350px !important;
  } */

  /* #lineCharts #lineCharts2 #barChart #pieChart{
    width:1075px !important;
  } */

  /* .box-body #expenses {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
  } */

  .box-body .btn-primary {
    background-color: #0b7676;
    
  }
 
  .info-box-content__wrapper {
    display: flex;
    justify-content:space-between;
  }

  .info-box-content__wrapper .btn-group {
    width: auto;
    margin-left: 7px;
  }

  .info-box-content__wrapper .btn {
    border: none;
    padding: 5px;
    background: none;
  }

  .info-box-content__wrapper .btn .fa, .btn-group .btn .fa {
    font-size: 10px;
  }

  .info-box-content__wrapper .dropdown-menu > li > a:hover, .btn-group .dropdown-menu > li > a:hover {
    background-color: #00c0ef;
    color: #fff !important;
  }

  .info-box-content__wrapper .info-box-text {
    white-space: normal;
    overflow: visible;
  }

  .box-tools{
    display:flex;
  }

  .box-tools .btn{
    border: none;
    padding: 5px;
    background: none;
  }

  .sptextalign{
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
  }


</style>


@endsection

@section('content')
<!-- Main content -->
@if (\Session::has('alert'))
<div class="alert alert-warning">
  <p>{{ \Session::get('alert') }}</p>
</div><br />
@endif
<section class="content" id="mainBox">
  <!-- Info boxes -->

  <div class="row">
    <div class="col-md-12">
      <div class="box" style="border-color:white;">
        <div id="loader1" hidden>
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
        <div class="box-body box-padding">
          @if(config('settings.ncal')==0)
            <div class="col-md-offset-9 col-md-3 col-xs-6" >
              <div id="reportrange" name="reportrange" style="text-align:right;cursor:pointer;">
                <span></span> <i class="fa fa-caret-down"></i>
              </div>
            </div>
            <input id="start_edate" type="text" name="start_edate" hidden />
            <input id="end_edate" type="text" name="end_edate" hidden />
          @else
            <div class="row">
              <div class="col-md-offset-8 col-md-3 col-xs-3">
                <div class="input-group" id="nepCalDiv">
                  <input id="start_ndate" style="cursor:pointer;" class="form-control nepali-date" type="text" name="start_ndate" placeholder="Start Date" autocomplete="off" />
                  <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                  <input id="end_ndate" style="cursor:pointer;" class="form-control nepali-date" type="text" name="end_ndate" placeholder="End Date" autocomplete="off" />
                </div>
              </div>
              <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-primary" id="submit-nepali-date">Submit</button>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>


@if(config('settings.product')==1 && config('settings.orders')==1)
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default" id="qtygraph1">
        <div class="box-header with-border">
          <h3 class="box-title" id="qtytitle_opt1">Top Products By No. of Units Sold</h3>
          <div class="box-tools pull-right">
            <div class="btn-group">
              <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="fa fa-chevron-down"></span>
              <span class="sr-only">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu">
                @if(Auth::user()->can('product-view'))
                  <li><a onclick="qtyopt1graph('products')" id="qty_products">Products</a></li>
                @endif
                @if(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access")
                  <li><a onclick="qtyopt1graph('categories')" id="categories">Categories</a></li>
                  <li><a onclick="qtyopt1graph('brands')" id="brands">Brands</a></li>
                @endif
              </ul>
            </div>
          </div>
          <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="ordval_n" custattr="products" onclick="qtytypefind('ordval')">By value</a>
          <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="unitssold_n" custattr="products" onclick="qtytypefind('unitssold')">By units sold</a>
        </div>
        <div class="box-body">
          <div id="qtyloader1" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <div id="qty_bracatprod" style="height: 400px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>
@endif

@if(config('settings.orders')==1  || config('settings.zero_orders')==1 || config('settings.visit_module')==1 || config('settings.product')==1 || config('settings.party')==1)
  @if(Auth::user()->can('order-view') || Auth::user()->can('zeroorder-view') || Auth::user()->can('PartyVisit-view') || Auth::user()->can('product-view') || Auth::user()->can('party-view'))
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default" id="linegrdim1">
          <div class="box-header with-border">
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div id="linegraph1_loader" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <div id="linegraphs1" style="height: 400px;max-width:1075px !important;"></div>
          </div>
        </div>
      </div>
    </div>
  @endif
@endif

@if(config('settings.orders')==1  || config('settings.collections')==1)
  @if(Auth::user()->can('order-view') || Auth::user()->can('collection-view'))
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default" id="linegrdim2">
          <div class="box-header with-border">
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div id="linegraph2_loader" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <div id="linegraphs2" style="height: 400px;max-width:1075px !important;"></div>
          </div>
        </div>
      </div>
    </div>
  @endif
@endif

@if(config('settings.visit_module')==1)
  @if(Auth::user()->can('PartyVisit-view'))
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default" id="visittimespent">
          <div class="box-header with-border">
            <h3 class="box-title">Total Time Spent on Visits</h3>
          </div>
          <div class="box-body">
            <div id="timespentvst_loader" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <div id="time_visit" style="height:400px;max-width:1075px !important;" ></div>
          </div>
        </div>
      </div>
    </div>
  @endif
@endif

@if(config('settings.party')==1)
  @if(Auth::user()->can('party-view'))
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default" id="parties_divcollordamt">
          <div class="box-header with-border">
            <h3 class="box-title">Top Parties&nbsp;(<span id="partiesgraph_typename">Order Value</span>)</h3>
            @if(config('settings.collections')==1)
              @if(Auth::user()->can('collection-view'))
                <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="parties_collvalamt" onclick="parties_collamt()">By Collection</a>
              @endif
            @endif
            @if(config('settings.orders')==1)
              @if(Auth::user()->can('order-view'))
                <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="parties_ordvalamt" onclick="parties_ordamt()">By Order Value</a>
              @endif
            @endif
          </div>
          <div class="box-body">
            <div id="partiesamtloader1" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <div id="parties_collordamt" style="height:400px;max-width:1075px !important;" ></div>
          </div>
        </div>
      </div>
    </div>
  @endif
@endif

@if(config('settings.beat')==1)
  @if(Auth::user()->can('beat-plan-view')) 
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default" id="beats_divcollordamt">
          <div class="box-header with-border">
            <h3 class="box-title">Top Performing Beats&nbsp;(<span id="beats_graphname">Order Value</span>)</h3>
            @if(config('settings.collections')==1 )
              @if(Auth::user()->can('collection-view'))
                <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="beats_collvalamt" onclick="topbeats(this)">By Collection</a>
              @endif
            @endif
            @if(config('settings.orders')==1 )
              @if(Auth::user()->can('order-view'))
                <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="beats_ordvalamt" onclick="topbeats(this)">By Order Value</a>
                <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="beats_ordval" onclick="topbeats(this)">By no. of Orders</a>
              @endif
            @endif
          </div>
          <div class="box-body">
            <div id="beatsloader1" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <div id="beats_collordamt" style="height:400px;max-width:1075px !important;" ></div>
          </div>
        </div>
      </div>
    </div>
  @endif
@endif

<div class="row" style="min-height:380px">
  @if(config('settings.orders')==1 || config('settings.retailer_app')==1) 
    @if(Auth::user()->can('order-view') || Auth::user()->can('outlet-view')) 
        <div class="col-md-6">
          <div class="box box-default" id="div_piechart">
            <div class="box-header with-border">
              <h3 class="box-title" id="piecharts_header">Order Vs Zero Order</h3>
              @if(config('settings.retailer_app')==1) 
                @if(Auth::user()->can('outlet-view'))
                  <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="outlets" onclick="piecharts(this)">Productive Outlets</a>
                @endif
              @endif
              @if(config('settings.orders')==1 )
                @if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view'))
                  <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="ordzero_order" onclick="piecharts(this)">Order Vs Zero Order</a>
                @endif
              @endif
            </div>
            <div class="box-body" id="piechart_maindiv">
              <div id="piechartloader1" hidden>
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
              <div id="piechart_div" style="min-height:345px!important;max-height:345px!important;min-width:100%!important;" ></div>
            </div>
        </div>
      </div>
    @endif
  @endif

  @if(config('settings.expenses')==1)
    @if(Auth::user()->can('expense-view')) 
        <div class="col-md-6">
          <div class="box box-default" id="expenseld"> 
            <div class="box-header with-border">
              <h3 class="box-title">Expenses</h3>
            </div>
            <div class="box-body">
              <div id="expenseloader" hidden>
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
              <div id="expenses" style="min-height:345px!important;max-height:345px!important;min-width:100%!important;"></div>
            </div>
          </div>
        </div>
    @endif
  @endif
</div>

</section>

@endsection


@section('scripts')
<!-- <script src="{{ asset('assets/dist/js/muuri.js') }}"></script>
<script src="{{ asset('assets/dist/js/drag-muuri.js') }}"></script> -->
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{ asset('assets/bower_components/apexchart/apexcharts.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/highchart.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/highcharts-more.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/exporting.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/export-data.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/accessibility.js') }}"></script><!--  -->



<script>    

    $(document).on('click',".applyBtn",function () {
      date = $(".drp-selected").html();
      dateArray = date.split(" - ");
      $('#reportrange span').html(moment(dateArray[0]).format('MMM D, YYYY') + ' - ' + moment(dateArray[1]).format('MMM D, YYYY'));
      startDate = moment(dateArray[0]).format("YYYY-MM-DD");
      endDate = moment(dateArray[1]).format("YYYY-MM-DD");
      window.sessionStorage.setItem("startdate",startDate);
      window.sessionStorage.setItem("enddate",endDate);
      getOtherGraphs(startDate, endDate, type="");
      @if(config('settings.ncal')==0)
        window.sessionStorage.setItem("caltype",'');
      @else
        window.sessionStorage.setItem("caltype",'nepali');
      @endif
    });

    function dateFilter(start, end,check) {
      if(start.format('YYYY-MM-DD')>end.format('YYYY-MM-DD') || check==true){
        $('#reportrange span').html("Today");
        check=false;
      }else if(end.isValid() == false) {
        $('#reportrange span').html(start.format('MMM D, YYYY'));
      }else {
        $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
      }
    }


    $(document).ready(function(){
      @if(config('settings.ncal')==0)
        start_Date = moment().subtract(30, 'days').format('YYYY-MM-DD');
        end_Date = moment().format('YYYY-MM-DD');
        window.sessionStorage.setItem("startdate",start_Date);
        window.sessionStorage.setItem("enddate",end_Date);
        window.sessionStorage.setItem("caltype",'');
        getOtherGraphs(start_Date, end_Date, type="");
      @else
        startDate = BS2AD(AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD')));
        endDate = BS2AD(AD2BS(moment().format('YYYY-MM-DD')));
        window.sessionStorage.setItem("startdate",startDate);
        window.sessionStorage.setItem("enddate",endDate);
        window.sessionStorage.setItem("caltype",'nepali');
        getOtherGraphs(startDate, endDate, type="nepali");
      @endif

    });    

    

    jQuery(function($) {
      
      @if(config('settings.ncal')==0)
        let start = moment(),
            end = moment();

        var start_t = moment().subtract(30, 'days');
        var end_t = moment();
        $('#start_edate').val(start_t.format('YYYY-MM-DD'));
        $('#end_edate').val(end_t.format('YYYY-MM-DD'));
        function cb(start_t, end_t) {
          $('#reportrange span').html(start_t.format('MMM D, YYYY') + ' - ' + end_t.format('MMM D, YYYY'));
          $('#startdate').val(start_t.format('MMMM D, YYYY'));
          $('#enddate').val(end_t.format('MMMM D, YYYY'));
        }
        $('#reportrange').daterangepicker({
          startDate: start_t,
          endDate: end_t,
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          }
        }, cb);
        cb(start_t, end_t);

        $(".ranges ul li").on('click', function() {
          let dates;
          let dataRange = $(this).attr('data-range-key');

          switch (dataRange) {

            case "Today":
              startDate = moment().format("YYYY-MM-DD");
              endDate = moment().format("YYYY-MM-DD");
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              getOtherGraphs(startDate, endDate, type="");
              break;
            case "Yesterday":
              startDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
              endDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              getOtherGraphs(startDate, endDate, type="");
              break;
            case "Last 7 Days":
              startDate = moment(moment().subtract(6, 'days')).format('YYYY-MM-DD');
              endDate = moment().format('YYYY-MM-DD');
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              getOtherGraphs(startDate, endDate, type="");
              break;
            case "Last 30 Days":
              startDate = moment(moment().subtract(29, 'days')).format('YYYY-MM-DD');
              endDate = moment().format('YYYY-MM-DD');
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              getOtherGraphs(startDate, endDate, type="");
              break;
            case "This Month":
              startDate = moment(moment().startOf('month')).format('YYYY-MM-DD');
              endDate = moment().format("YYYY-MM-DD");
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              getOtherGraphs(startDate, endDate, type="");
              break;
            case "Last Month":
              startDate = moment(moment().subtract(1, 'month').startOf('month')).format('YYYY-MM-DD');
              endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              getOtherGraphs(startDate, endDate, type="");
              break;
            case "Custom Range":
              $(".applyBtn").click(function () {
                date = $(".drp-selected").html();
                dateArray = date.split(" - ");
                startDate = moment(dateArray[0]).format("YYYY-MM-DD");
                endDate = moment(dateArray[1]).format("YYYY-MM-DD");
                window.sessionStorage.setItem("startdate",startDate);
                window.sessionStorage.setItem("enddate",endDate);
                window.sessionStorage.setItem("caltype",'');
                getOtherGraphs(startDate, endDate, type="");
              });
              break;
          
            default:
              break;
          }
        });

      @else
        //nepali calendaar
        $(".nepali-date").nepaliDatePicker();
        var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
        var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
        $('#start_ndate').val(lastmonthdate);
        $('#end_ndate').val(ntoday);
        $("#submit-nepali-date").on('click', function() {
          startDate = BS2AD($("#start_ndate").val());
          endDate = BS2AD($("#end_ndate").val());
          window.sessionStorage.setItem("startdate",startDate);
          window.sessionStorage.setItem("enddate",endDate);
          window.sessionStorage.setItem("caltype",'nepali');
          getOtherGraphs(startDate, endDate, type="nepali");
        });
      @endif


    });

    function daysDiff(d1, d2) {
      const diffTime = Math.abs(d2 - d1);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      return diffDays;
    }


    function qtytypefind(qty1='products'){
      var ab = $("#ordval_n").attr('custattr');
      if(ab=='products' && qty1=='ordval'){
        amtopt1graph('products');
      }else if(ab=='products' && qty1=='unitssold'){
        qtyopt1graph('products');
      }else if(ab=='brands' && qty1=='ordval'){
        amtopt1graph('brands');
      }else if(ab=='brands' && qty1=='unitssold'){
        qtyopt1graph('brands');
      }else if(ab=='categories' && qty1=='ordval'){
        amtopt1graph('categories');
      }else if(ab=='categories' && qty1=='unitssold'){
        qtyopt1graph('categories');
      }
    }

    function qtyopt1graph(qty1='products'){
      var qty1value = '';
      $("#ordval_n").attr('custattr',qty1);
      $("#unitssold_n").attr('custattr',qty1); 
      if(qty1=='products'){
        qty1value = 'qtyprod';
      }else if(qty1=='categories'){
        qty1value = 'qtycat';
      }else if(qty1=='brands'){
        qty1value = 'qtybra';
      }
      var qttitle = {'qtyprod':'Top Products By No. of Units Sold','qtybra':'Top Brands By No. of Units Sold','qtycat':'Top Categories By No. of Units Sold','qtybeats':'Beats(Orders)'}
      var qtroutes = {'qtyprod':"{{domain_route('company.admin.fetchtopproductsorder')}}",'qtybra':"{{domain_route('company.admin.fetchtopbrandsorder')}}",'qtycat':"{{domain_route('company.admin.fetchtopcategoriesorder')}}",'qtybeats':"{{domain_route('company.admin.fetchtopperformbeatsorder')}}"}
      var graphtype = {'qtyprod':'prod','qtybra':'bra','qtycat':'cat','qtybeats':'beats'}
      $("#qtytitle_opt1").empty().text(qttitle[qty1value]);
      var startDate = window.sessionStorage.getItem('startdate');
      var endDate = window.sessionStorage.getItem('enddate');
      var type = window.sessionStorage.getItem('caltype');
      let qtyDates = qtyValues = [];
      var routeurl = qtroutes[qty1value]; 
      $.ajax({
        method: 'post',
        url: routeurl,
        data:{start_date: startDate, end_date: endDate, type},
        success:function(response){
          qtyDates = response.data.dates;
          qtyValues = response.data.values;
        },
        beforeSend:function(){
          $('#qtygraph1').addClass('box-loader');
          $('#qtyloader1').removeAttr('hidden');
        },
        complete:function(){
          let divid = 'qty_bracatprod';
          $("#"+divid+"").empty();
          var data_check = qtyValues.length;
          if(data_check>0){
            qtyProdBraCatGraph(divid,qtyDates,qtyValues,graphtype[qty1value]);
          }else{
            $("#"+divid+"").html("<span class='sptextalign'>No Data Available</span>");
          }
          $('#qtygraph1').removeClass('box-loader');
          $('#qtyloader1').attr('hidden', 'hidden');
        }
      });
    }

    function amtopt1graph(qty1='products'){
      var qty1value = '';
      if(qty1=='products'){
        qty1value = 'amtprod';
      }else if(qty1=='categories'){
        qty1value = 'amtcat';
      }else if(qty1=='brands'){
        qty1value = 'amtbra';
      }

      var qttitle = {'amtprod':'Top Products Sold By Value','amtbra':'Top Brands Sold By Value','amtcat':'Top Categories Sold By Value','amtbeats':'Categories(Amount)'}
      var qtroutes = {'amtprod':"{{domain_route('company.admin.fetchtopproductsamount')}}",'amtbra':"{{domain_route('company.admin.fetchtopbrandsamount')}}",'amtcat':"{{domain_route('company.admin.fetchtopcategoriesamount')}}",'amtbeats':"{{domain_route('company.admin.fetchtopperformbeatsamt')}}"}
      var graphtype = {'amtprod':'prod','amtbra':'bra','amtcat':'cat','amtbeats':'beats'}
      $("#qtytitle_opt1").empty().text(qttitle[qty1value]);
      var startDate = window.sessionStorage.getItem('startdate');
      var endDate = window.sessionStorage.getItem('enddate');
      var type = window.sessionStorage.getItem('caltype');
      let qtyDates = qtyValues = [];
      var routeurl = qtroutes[qty1value]; 
      let currsymbol = '';
      $.ajax({
        method: 'post',
        url: routeurl,
        data:{start_date: startDate, end_date: endDate, type},
        success:function(response){
          qtyDates = response.data.dates;
          qtyValues = response.data.values;
          currsymbol = response.data.currency_symbol;
        },
        beforeSend:function(){
          $('#qtygraph1').addClass('box-loader');
          $('#qtyloader1').removeAttr('hidden');
        },
        complete:function(){
          let divid = 'qty_bracatprod';
          $("#"+divid+"").empty();
          var data_check = qtyValues.length;
          if(data_check>0){
            amtProdBraCatGraph(divid,qtyDates,qtyValues,currsymbol,graphtype[qty1value]);
          }else{
            $("#"+divid+"").html("<span class='sptextalign'>No Data Available</span>");
          }
          $('#qtygraph1').removeClass('box-loader');
          $('#qtyloader1').attr('hidden', 'hidden');
        }
      });
    }
    
    function topbeats(qty1){
      var qttitle = {'beats_collvalamt':'Collection Value','beats_ordvalamt':'Order Value','beats_ordval':'Order'}
      var qtroutes = {'beats_collvalamt':"{{domain_route('company.admin.fetchtopperformbeatscollamt')}}",'beats_ordvalamt':"{{domain_route('company.admin.fetchtopperformbeatsamt')}}",'beats_ordval':"{{domain_route('company.admin.fetchtopperformbeatsorder')}}"}
      var graphtype = {'beats_collvalamt':'collamt','beats_ordvalamt':'ordamt','beats_ordval':'ordval'}
      var qty1value = qty1.id;
      $("#beats_graphname").empty().text(qttitle[qty1value]);
      var startDate = window.sessionStorage.getItem('startdate');
      var endDate = window.sessionStorage.getItem('enddate');
      var type = window.sessionStorage.getItem('caltype');
      let qtyDates = qtyValues = [];
      var routeurl = qtroutes[qty1value]; 
      let currsymbol = '';
      $.ajax({
        method: 'post',
        url: routeurl,
        data:{start_date: startDate, end_date: endDate, type},
        success:function(response){
          qtyDates = response.data.dates;
          qtyValues = response.data.values;
          currsymbol = response.data.currency_symbol;
        },
        beforeSend:function(){
          $('#beats_divcollordamt').addClass('box-loader');
          $('#beatsloader1').removeAttr('hidden');
        },
        complete:function(){
          $("#beats_collordamt").empty();
          var data_check = qtyValues.length;
          if(data_check>0){
            topBeatsAmtGraph(qtyDates,qtyValues,currsymbol,graphtype[qty1value]);
          }else{
            $("#beats_collordamt").html("<span class='sptextalign'>No Data Available</span>");
          }
          $('#beats_divcollordamt').removeClass('box-loader');
          $('#beatsloader1').attr('hidden', 'hidden');
        }
      });
    }

    function piecharts(qty1){
      var qttitle = {'outlets':'Productive Outlets','ordzero_order':'Order Vs Zero Order'}
      var qtroutes = {'outlets':"{{domain_route('company.admin.fetchtopoutlets')}}",'ordzero_order':"{{domain_route('company.admin.fetchtotalzeroorder')}}",}
      var graphtype = {'outlets':'outlets','ordzero_order':'ordzro'}
      var qty1value = qty1.id;
      $("#piecharts_header").empty().text(qttitle[qty1value]);
      var startDate = window.sessionStorage.getItem('startdate');
      var endDate = window.sessionStorage.getItem('enddate');
      var type = window.sessionStorage.getItem('caltype');
      let topOutletsDates = topOutletsValues = [];

      var routeurl = qtroutes[qty1value]; 
      let currsymbol = '';
      $.ajax({
        method: 'post',
        url: routeurl,
        data:{start_date: startDate, end_date: endDate, type},
        success:function(response){
          topOutletsDates = response.data.dates;
          topOutletsValues = response.data.values;
        },
        beforeSend:function(){
          $('#div_piechart').addClass('box-loader');
          $('#piechartloader1').removeAttr('hidden');
        },
        complete:function(){
          $("#piechart_div").empty();
          if(qty1value=='outlets'){
            if(Array.isArray(topOutletsDates,topOutletsValues)){
              if(topOutletsValues[0]==0 && topOutletsValues[1]==1){
                $("#piechart_div").html("<span class='sptextalign'>No Data Available</span>");
              }else{
                topOutletsGraph(topOutletsDates, topOutletsValues)
              }
            } 
          }else if(qty1value=='ordzero_order'){
            counter = 0;
            for(var y=0;y<topOutletsValues.length;y++){
              if(topOutletsValues[y]==0){
                counter = counter+1;
              }
            }
            if(counter!=topOutletsValues.length){
              orderPieGraph(topOutletsDates, topOutletsValues);
            }else{
              $("#piechart_div").html("<span class='sptextalign'>No Data Available</span>");
            }
          }
          $('#div_piechart').removeClass('box-loader');
          $('#piechartloader1').attr('hidden', 'hidden');
        }
      });
    }

    function parties_ordamt(){
      var startDate = window.sessionStorage.getItem('startdate');
      var endDate = window.sessionStorage.getItem('enddate');
      var type = window.sessionStorage.getItem('caltype');
      let qtyDates = qtyValues = [];
      let currsymbol = '';
      $.ajax({
        method: 'post',
        url: "{{ domain_route('company.admin.fetchtoppartiesordvalue') }}",
        data:{start_date: startDate, end_date: endDate, type},
        success:function(response){
          qtyDates = response.data.dates;
          qtyValues = response.data.values;
          currsymbol = response.data.currency_symbol;
        },
        beforeSend:function(){
          $('#parties_divcollordamt').addClass('box-loader');
          $('#partiesamtloader1').removeAttr('hidden');
        },
        complete:function(){
          $("#parties_collordamt").empty();
          $("#partiesgraph_typename").empty().text('Order Value');
          var data_check = qtyValues.length;
          if(data_check>0){
            topPartiesGraph('order',qtyDates,qtyValues,currsymbol);
          }else{
            $("#parties_collordamt").html("<span class='sptextalign'>No Data Available</span>");
          }
          $('#parties_divcollordamt').removeClass('box-loader');
          $('#partiesamtloader1').attr('hidden', 'hidden');
        }
      });
    }

    function parties_collamt(){
      var startDate = window.sessionStorage.getItem('startdate');
      var endDate = window.sessionStorage.getItem('enddate');
      var type = window.sessionStorage.getItem('caltype');
      let qtyDates = qtyValues = [];
      let currsymbol = '';
      $.ajax({
        method: 'post',
        url: "{{ domain_route('company.admin.fetchtoppartiescollamt') }}",
        data:{start_date: startDate, end_date: endDate, type},
        success:function(response){
          qtyDates = response.data.dates;
          qtyValues = response.data.values;
          currsymbol = response.data.currency_symbol;
        },
        beforeSend:function(){
          $('#parties_divcollordamt').addClass('box-loader');
          $('#partiesamtloader1').removeAttr('hidden');
        },
        complete:function(){
          $("#parties_collordamt").empty();
          $("#partiesgraph_typename").empty().text('Collection');
          var data_check = qtyValues.length;
          if(data_check>0){
            topPartiesGraph('coll', qtyDates,qtyValues,currsymbol);
          }else{
            $("#parties_collordamt").html("<span class='sptextalign'>No Data Available</span>");
          }
          $('#parties_divcollordamt').removeClass('box-loader');
          $('#partiesamtloader1').attr('hidden', 'hidden');
        }
      });
    }

    function getOtherGraphs(startDate, endDate, type=""){

      let timeSpentDates = timeSpentValues = [];
      let topOutletsDates = topOutletsValues = [];
      let expenseDates = expenseValues = [];

      let totOrderDates = totOrderValues = [];
      let noOrderDates = noOrderValues = [];
      let totCallsDates = totCallsValues = [];
      let totVisitsDates = totVisitsValues = [];
      let totProdSoldDates = totProdSoldValues = [];
      let noPartiesAddedDates = noPartiesAddedValues = [];
      let totCollDates = totCollValues = [];


      let totOrderAmtDates = totOrderAmtValues = [];
      let totCollCashDates = totCollCashValues = [];
      let totCollChequeDates = totCollChequeValues = [];
      let totCollBanktxDates = totCollBanktxValues = [];
      let totCollAmtDates = totCollAmtValues = [];
      let amtCurrency = '';
      let amtExpCurrency = '';

      let topbeatsDates = topbeatsValues = [];
      let topbeatsAmtDates = topbeatsAmtValues = [];
      let topbeatsCollDates = topbeatsCollValues = [];


      let totOrderDatesPie = totOrderValuesPie = [];
      let noOrderDatesPie = noOrderValuesPie = [];

      let noofordertargets_achieved = noofordertargets_toachieve = '';
      let noofcolltargets_achieved = noofcolltargets_toachieve = '';
      let noofvisittargets_achieved = noofvisittargets_toachieve = '';
      let valueordertargets_achieved = valueordertargets_toachieve = '';
      let valuecolltargets_achieved = valuecolltargets_toachieve = '';
      let goldencallstargets_achieved = goldencallstargets_toachieve = '';
      let prodcallstargets_achieved = prodcallstargets_toachieve = '';


      let diffInDays = daysDiff(new Date(startDate), new Date(endDate));

      @if(config('settings.product')==1 && config('settings.orders')==1)
        @if(Auth::user()->can('product-view'))
          qtyopt1graph('products');
        @elseif(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access")
          qtyopt1graph('categories');
        @endif
      @endif

      @if(config('settings.party')==1)
        @if(Auth::user()->can('party-view'))
          @if(config('settings.orders')==1)
            @if(Auth::user()->can('order-view'))
              $("#parties_ordvalamt").trigger('click');
            @endif
          @elseif(config('settings.collections')==1)
            @if(Auth::user()->can('collection-view'))
              $("#parties_collvalamt").trigger('click');
            @endif
          @endif
        @endif
      @endif

      @if(config('settings.beat')==1)
        @if(Auth::user()->can('beat-plan-view')) 
          @if(config('settings.orders')==1 )
            @if(Auth::user()->can('order-view'))
              $("#beats_ordval").trigger('click');
            @endif
          @elseif(config('settings.collections')==1 )
            @if(Auth::user()->can('collection-view'))
              $("#beats_collvalamt").trigger('click');
            @endif
          @endif
        @endif
      @endif

      @if(config('settings.order')==1 || config('settings.retailer_app')==1)
        @if(Auth::user()->can('order-view') || Auth::user()->can('outlet-view')) 
          @if(config('settings.orders')==1)
            @if(Auth::user()->can('order-view'))
              $("#ordzero_order").trigger('click');
            @endif
          @elseif(config('settings.retailer_app')==1) 
            @if(Auth::user()->can('outlet-view'))
              $("#outlets").trigger('click');
            @endif
          @endif
        @endif
      @endif

      $.post("{{ domain_route('company.admin.fetchzero_order') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if(response.data!='nok'){
          if (type == undefined || type == "") {
            noOrderDates = response.data.dates;
            noOrderValues = response.data.values;
          } else {
            if (diffInDays > 45) {
              noOrderDates = response.data.dates; 
            } else {
              for (let index = 0; index < response.data.dates.length; index++) {
                const element = response.data.dates[index];
                noOrderDates.push(AD2BS(element));
              }
            }
          }
          noOrderValues = response.data.values;
        }
      });

      $.post("{{ domain_route('company.admin.fetchtotalvisits') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if(response.data!='nok'){
          if (type == undefined || type == "") {
            totVisitsDates = response.data.dates;
            totVisitsValues = response.data.values;
          } else {
            if (diffInDays > 45) {
              totVisitsDates = response.data.dates; 
            } else {
              for (let index = 0; index < response.data.dates.length; index++) {
                const element = response.data.dates[index];
                totVisitsDates.push(AD2BS(element));
              }
            }
          }
          totVisitsValues = response.data.values;
        }
      });

      $.post("{{ domain_route('company.admin.fetchtotprodsold') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if(response.data!='nok'){
          if (type == undefined || type == "") {
            totProdSoldDates = response.data.dates;
            totProdSoldValues = response.data.values;
          } else {
            if (diffInDays > 45) {
              totProdSoldDates = response.data.dates; 
            } else {
              for (let index = 0; index < response.data.dates.length; index++) {
                const element = response.data.dates[index];
                totProdSoldDates.push(AD2BS(element));
              }
            }
          }
          totProdSoldValues = response.data.values;
        }
      });

      $.post("{{ domain_route('company.admin.fetchtotpartiesadded') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if(response.data!='nok'){
          if (type == undefined || type == "") {
            noPartiesAddedDates = response.data.dates;
            noPartiesAddedValues = response.data.values;
          } else {
            if (diffInDays > 45) {
              noPartiesAddedDates = response.data.dates; 
            } else {
              for (let index = 0; index < response.data.dates.length; index++) {
                const element = response.data.dates[index];
                noPartiesAddedDates.push(AD2BS(element));
              }
            }
          }
          noPartiesAddedValues = response.data.values;
        }
      });

      $.post("{{ domain_route('company.admin.fetchtotcollections') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          totCollDates = response.data.dates;
          totCollValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            totCollDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              totCollDates.push(AD2BS(element));
            }
          }
        }
        totCollValues = response.data.values;
      });

      $.post("{{ domain_route('company.admin.fetchtotal_order') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if(response.data!='nok'){
          if (type == undefined || type == "") {
            totOrderDates = response.data.dates;
            totOrderValues = response.data.values;
          } else {
            if (diffInDays > 45) {
              totOrderDates = response.data.dates; 
            } else {
              for (let index = 0; index < response.data.dates.length; index++) {
                const element = response.data.dates[index];
                totOrderDates.push(AD2BS(element));
              }
            }
          }
          totOrderValues = response.data.values;
        }
      });

      $.post("{{ domain_route('company.admin.fetchtotalorderamt') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          totOrderAmtDates = response.data.dates;
          totOrderAmtValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            totOrderAmtDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              totOrderAmtDates.push(AD2BS(element));
            }
          }
        }
        totOrderAmtValues = response.data.values;
      });
      
       $.ajax({
        method: 'post',
        url: "{{ domain_route('company.admin.fetchtimespentvisit') }}",
        data: {start_date: startDate, end_date: endDate, type},
        success:function(response){
          if (type == undefined || type == "") {
            timeSpentDates = response.data.dates;
            timeSpentValues = response.data.values;
          } else {
            if (diffInDays > 45) {
              timeSpentDates = response.data.dates; 
            } else {
              for (let index = 0; index < response.data.dates.length; index++) {
                const element = response.data.dates[index];
                timeSpentDates.push(AD2BS(element));
              }
            }
          }
          timeSpentValues = response.data.values;

          var data_check1 = timeSpentValues.length;
          if(data_check1>0){
            @if(config('settings.visit_module')==1)
              @if(Auth::user()->can('PartyVisit-view'))
                timeSpentVistGraph(timeSpentDates, timeSpentValues);
              @endif
            @endif
          }else{
            $("#time_visit").html("<span class='sptextalign'>No Data Available</span>");
          }
        },
        beforeSend:function(){
          $('#visittimespent').addClass('box-loader');
          $('#timespentvst_loader').removeAttr('hidden');
        },
        complete:function(){
          $('#visittimespent').removeClass('box-loader');
          $('#timespentvst_loader').attr('hidden', 'hidden');
        }
       });

      // $.post("{{ domain_route('company.admin.fetchtargetsnoorder') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
      //   noOrderGraph(response.data.targetsnoorder_achieved,response.data.targetsnoorder_toachieve);
      //   noCollGraph(response.data.targetsnocoll_achieved,response.data.targetsnocoll_toachieve);
      //   noVisitGraph(response.data.targetsnovisit_achieved,response.data.targetsnovisit_toachieve);
      //   callsGoldenGraph(response.data.targetgoldencalls_achieved,response.data.targetgoldencalls_toachieve);
      //   callsTotalGraph(response.data.totalcalls_achieved,response.data.totalcalls_toachieve);
      //   valueOrderGraph(response.data.targetsvalueorder_achieved,response.data.targetvalueorder_toachieve);
      //   valueCollGraph(response.data.targetsvaluecoll_achieved,response.data.targetvaluecoll_toachieve);
      // });

      $.post("{{ domain_route('company.admin.fetchtopperformbeatsorder') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          topbeatsDates = response.data.dates;
          topbeatsValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            topbeatsDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              topbeatsDates.push(AD2BS(element));
            }
          }
        }
        topbeatsValues = response.data.values;
        // topBeatsOrderGraph(topbeatsDates, topbeatsValues);
      });

      $.post("{{ domain_route('company.admin.fetchtopperformbeatscollamt') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        let currsymbol = response.data.currency_symbol; 
        if (type == undefined || type == "") {
          topbeatsCollDates = response.data.dates;
          topbeatsCollValues = response.data.values; 
        } else {
          if (diffInDays > 45) {
            topbeatsCollDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              topbeatsCollDates.push(AD2BS(element));
            }
          }
        }
        topbeatsCollValues = response.data.values;
        // topBeatsCollAmtGraph(topbeatsCollDates, topbeatsCollValues, currsymbol);
      });

      $.post("{{ domain_route('company.admin.fetchtopperformbeatsamt') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        let currsymbol = '';
        if (type == undefined || type == "") {
          topbeatsAmtDates = response.data.dates;
          topbeatsAmtValues = response.data.values; 
        } else {
          if (diffInDays > 45) {
            topbeatsAmtDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              topbeatsAmtDates.push(AD2BS(element));
            }
          }
        }
        currsymbol = response.data.currency_symbol;
        topbeatsAmtValues = response.data.values;
        // topBeatsAmtGraph(topbeatsAmtDates, topbeatsAmtValues, currsymbol);
      });

      $.ajax({
        method: 'post',
        url: "{{ domain_route('company.admin.fetchtopexpenses') }}",
        data: {start_date: startDate, end_date: endDate, type},
        success:function(response){
          var currsymbol = '';
          expenseDates = response.data.dates;
          expenseValues = response.data.values;
          currsymbol = response.data.currency_symbol;
          counter = 0;
          for(var y=0;y<expenseValues.length;y++){
            if(expenseValues[y]==0){
              counter = counter+1;
            }
          }
          if(counter==expenseValues.length){
            $("#expenses").html("<span class='sptextalign'>No Data Available</span>");
          }else{
            $("#expenses").empty();
            expenseGraph(expenseDates, expenseValues, currsymbol);
          }
        },
        beforeSend:function(){
          $('#expenseld').addClass('box-loader');
          $('#expenseloader').removeAttr('hidden');
        },
        complete:function(){
          $('#expenseld').removeClass('box-loader');
          $('#expenseloader').attr('hidden', 'hidden');
        }
      });


      $.ajax({
        method: 'post',
        url: "{{ domain_route('company.admin.fetchtotalcalls') }}",
        data: {start_date: startDate, end_date: endDate, type},
        success:function(response){
          if(response.data!='nok'){
            if (type == undefined || type == "") {
              totCallsDates = response.data.dates;
              totCallsValues = response.data.values;
            } else {
              if (diffInDays > 45) {
                totCallsDates = response.data.dates; 
              } else {
                for (let index = 0; index < response.data.dates.length; index++) {
                  const element = response.data.dates[index];
                  totCallsDates.push(AD2BS(element));
                }
              }
            }
            totCallsValues = response.data.values;
          }

          data_check1 = 0;
          for(var y=0;y<totOrderValues.length;y++){
            if(totOrderValues[y]==0){
              data_check1 = data_check1+1;
            }
          }
          data_check2 = 0;
          for(var y=0;y<noOrderValues.length;y++){
            if(noOrderValues[y]==0){
              data_check2 = data_check2+1;
            }
          }
          data_check3 = 0;
          for(var y=0;y<totCallsValues.length;y++){
            if(totCallsValues[y]==0){
              data_check3 = data_check3+1;
            }
          }
          data_check4 = 0;
          for(var y=0;y<totVisitsValues.length;y++){
            if(totVisitsValues[y]==0){
              data_check4 = data_check4+1;
            }
          }
          data_check5 = 0;
          for(var y=0;y<totProdSoldValues.length;y++){
            if(totProdSoldValues[y]==0){
              data_check5 = data_check5+1;
            }
          }
          data_check6 = 0;
          for(var y=0;y<noPartiesAddedValues.length;y++){
            if(noPartiesAddedValues[y]==0){
              data_check6 = data_check6+1;
            }
          }
        
          var graphDate = [];
          if(Array.isArray(totOrderDates) && totOrderDates.length>0){
            graphDate = totOrderDates;
          }else if(Array.isArray(noOrderDates) && noOrderDates.length>0){
            graphDate = noOrderDates;
          }else if(Array.isArray(totCallsDates) && totCallsDates.length>0){
            graphDate = totCallsDates;
          }else if(Array.isArray(totVisitsDates) && totVisitsDates.length>0){
            graphDate = totVisitsDates;
          }else if(Array.isArray(totProdSoldDates) && totProdSoldDates.length>0){
            graphDate = totProdSoldDates;
          }else if(Array.isArray(noPartiesAddedDates) && noPartiesAddedDates.length>0){
            graphDate = noPartiesAddedDates;
          }
        
          if(data_check1==totOrderValues.length && data_check2==noOrderValues.length && data_check3==totCallsValues.length && data_check4==totVisitsValues.length &&data_check5==totProdSoldValues.length && data_check6==noPartiesAddedValues.length){
            $("#linegraphs1").html("<span class='sptextalign'>No Data Available</span>");
          }else{
            plotLineGraphs(graphDate, totOrderValues, noOrderValues, totCallsValues, totVisitsValues, totProdSoldValues, noPartiesAddedValues, totCollValues);
          }
        },
        beforeSend:function(){
          $('#linegrdim1').addClass('box-loader');
          $('#linegraph1_loader').removeAttr('hidden');
        },
        complete:function(){
          $('#linegrdim1').removeClass('box-loader');
          $('#linegraph1_loader').attr('hidden', 'hidden');
        }
      })

  
      $.ajax({
        method: 'post',
        url: "{{ domain_route('company.admin.fetchcollectionamt') }}",
        data: {start_date: startDate, end_date: endDate, type},
        success:function(response){
          if (type == undefined || type == "") {
            totCollAmtDates = response.data.dates;
            totCollAmtValues = response.data.values;
          } else {
            if (diffInDays > 45) {
              totCollAmtDates = response.data.dates; 
            } else {
              for (let index = 0; index < response.data.dates.length; index++) {
                const element = response.data.dates[index];
                totCollAmtDates.push(AD2BS(element));
              }
            }
          }
          totCollAmtValues = response.data.values;
          amtCurrency = response.data.currency_symbol;

          data_check1 = 0;
          for(var y=0;y<totOrderAmtValues.length;y++){
            if(totOrderAmtValues[y]==0){
              data_check1 = data_check1+1;
            }
          }
          data_check2 = 0;
          for(var y=0;y<totCollAmtValues.length;y++){
            if(totCollAmtValues[y]==0){
              data_check2 = data_check2+1;
            }
          }
          if(data_check1==totOrderAmtValues.length && data_check2==totCollAmtValues.length){
            $("#linegraphs2").html("<span class='sptextalign'>No Data Available</span>");
          }else{
            plotLineGraphs2(totCollAmtDates, totOrderAmtValues, totCollAmtValues, amtCurrency);
          } 
        },
        beforeSend:function(response){
          $('#linegrdim2').addClass('box-loader');
          $('#linegraph2_loader').removeAttr('hidden');
        },
        complete:function(response){
          $('#linegrdim2').removeClass('box-loader');
          $('#linegraph2_loader').attr('hidden', 'hidden');
        }
      });


 
    }
 
    function qtyProdBraCatGraph(divid, dates, values,gtype) {
      var xtext = ytext = ''; 
      if(gtype=='prod'){
        xtext = 'Products'; ytext = 'Orders';
      }else if(gtype=='cat'){
        xtext = 'Categories'; ytext = 'Orders';
      }else if(gtype=='bra'){
        xtext = 'Brands'; ytext = 'Orders';
      }else if(gtype=='beats'){
        xtext = 'Beats'; ytext = 'Orders';
      }
      var options = {
        series: [{
            name: ytext,
            data: values
        }],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
            horizontal: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: dates,
          tickPlacement: 'on',
          title: {
            text: ytext,
          },
        },
        yaxis: {
          title: {
            text: xtext,
          },
        },
        colors: ["#0b7676"]
      };
      $("#"+divid).empty();
      var chart = new ApexCharts(document.querySelector("#"+divid), options);
      chart.render();
    }

    function amtProdBraCatGraph(divid, dates, values, currsymbol, gtype) {
      var xtext = ytext = '';
      if(gtype=='prod'){
        xtext = 'Products'; ytext = 'Amount';
      }else if(gtype=='cat'){
        xtext = 'Categories'; ytext = 'Amount';
      }else if(gtype=='bra'){
        xtext = 'Brands'; ytext = 'Amount';
      }else if(gtype=='beats'){
        xtext = 'Beats'; ytext = 'Amount';
      }
      var options = {
        series: [{
          name: ytext,
          data: values
        }],
      chart: {
        height: 350,
        type: 'bar',
      },
      plotOptions: {
        bar: {
          borderRadius: 10,
          columnWidth: '50%',
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2
      },
      
      grid: {
        row: {
          colors: ['#fff', '#f2f2f2']
        }
      },
      xaxis: {
        labels: {
          rotate: -45
        },
        title: {
          text: xtext,
        },
        categories: dates,
        tickPlacement: 'on'
      },
      yaxis: {
        title: {
          text: ytext,
        },
        labels: {
          formatter: function(val) {
            return currsymbol+' '+val.toLocaleString("en");
          },
        },
      },
      colors: ["#0b7676"]
      };
      $("#"+divid).empty();
      var chart = new ApexCharts(document.querySelector("#"+divid), options);
      chart.render();
    }

    function plotLineGraphs(dates, totalorder, zeroorder, totalcalls, totalvisits, totprodsold, partiesadded, totcollections){
      var options = {
          series: [
            @if(config('settings.orders')==1 )
              @if(Auth::user()->can('order-view'))
                {
                  name: 'Orders (Effective Calls)',
                  data: totalorder
                },
              @endif
            @endif
            @if(config('settings.zero_orders')==1 )
              @if(Auth::user()->can('zeroorder-view'))
                {
                  name: 'Zero Orders (Non-effective Calls)',
                  data: zeroorder
                },
              @endif
            @endif
            @if(config('settings.orders')==1 || config('settings.zero_orders')==1)
              @if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view'))
                {
                  name: 'Calls (Orders + Zero Orders) ',
                  data: totalcalls
                },
              @endif
            @endif
            @if(config('settings.visit_module')==1 )
              @if(Auth::user()->can('PartyVisit-view'))
                {
                  name: 'Visits',
                  data: totalvisits
                },
              @endif
            @endif
            @if(config('settings.product')==1 )
              @if(Auth::user()->can('product-view'))
                {
                  name: 'Products Sold',
                  data: totprodsold
                },
              @endif
            @endif
            @if(config('settings.party')==1 )
              @if(Auth::user()->can('party-view'))
                {
                  name: 'New Parties Added',
                  data: partiesadded
                },
              @endif
            @endif
          ],
        chart: {
          height: 400,
          type: 'line',
        },
        plotOptions: {
          stroke: {
            width: 4,
            curve: 'smooth'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: 2
        },
        subtitle: {
          align: 'center',
          margin: 30,
          offsetY: 40,
          style: {
            color: '#222',
            fontSize: '24px',
          }
        },
        grid: {
          row: {
            colors: ['#fff', '#f2f2f2']
          }
        },
        xaxis: {
          labels: {
            rotate: -45
          },
          title: {
            text: 'Date',
          },
          categories: dates,
          tickPlacement: 'on'
        },
        yaxis: {
          title: {
            text: 'Number',
          },
        },
        legend: {
          position: 'top',
          horizontalAlign: 'right',
          offsetY: 5,
          offsetX: -50
        }
      };
      $("#linegraphs1").empty();
      var chart = new ApexCharts(document.querySelector("#linegraphs1"), options);
      chart.render();
    }

    function plotLineGraphs2(dates, totalorder, totcoll, amtcurrency){
      var options = {
          series: [
            @if(config('settings.orders')==1 )
              @if(Auth::user()->can('order-view'))
                {
                  name: 'Order Value',
                  data: totalorder
                },
              @endif
            @endif
            @if(config('settings.collections')==1 )
              @if(Auth::user()->can('collection-view'))
                {
                  name: 'Payment Collection',
                  data: totcoll
                },
              @endif
            @endif
          ],
        chart: {
          height: 400,
          type: 'line',
        },
        plotOptions: {
          stroke: {
            width: 4,
            curve: 'smooth'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: 2
        },
        subtitle: {
          align: 'center',
          margin: 30,
          offsetY: 40,
          style: {
            color: '#222',
            fontSize: '24px',
          }
        },
        grid: {
          row: {
            colors: ['#fff', '#f2f2f2']
          }
        },
        xaxis: {
          labels: {
            rotate: -45
          },
          title: {
            text: 'Date',
          },
          categories: dates,
          tickPlacement: 'on'
        },
        yaxis: {
          title: {
            text: 'Amount',
          },
          labels: {
            formatter: function(val) {
              if(val == undefined){
                return amtcurrency+' '+val;
              }else{
                return amtcurrency+' '+val.toLocaleString("en");
              }
            },
          },
        },
        legend: {
          position: 'top',
          horizontalAlign: 'right',
          offsetY: 5,
          offsetX: -50
        }
      };
      $("#linegraphs2").empty();
      var chart = new ApexCharts(document.querySelector("#linegraphs2"), options);
      chart.render();
    }

    function timeSpentVistGraph(dates, values) {
      var tot_visittime = hours = minutes = second = '';
      var options = {
        series: [{
          name: 'Time',
          data: values
        }],
      chart: {
        height: 350,
        type: 'line',
      },
      plotOptions: {
        bar: {
          borderRadius: 10,
          columnWidth: '50%',
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2
      },
      
      grid: {
        row: {
          colors: ['#fff', '#f2f2f2']
        }
      },
      xaxis: {
        labels: {
          rotate: -45
        },
        title: {
          text: 'Date',
        },
        categories: dates,
        tickPlacement: 'on'
      },
      yaxis: {
        title: {
          text: 'Time',
        },
      },
      tooltip: {
          y: {
            formatter: function(tot_time) {
              if(tot_time>=0 && tot_time<=60){
                tot_visittime = tot_time+' Seconds';
              }else if(tot_time>60 && tot_time<=3600){ 
                minutes = Math.round((tot_time/60),2)+' minutes ';
                second = Math.round((tot_time%60),2)+' seconds';
                if(parseInt(second)==0){
                  tot_visittime = minutes;
                }else{
                  tot_visittime = minutes+' '+second;
                }
              }else{
                hours = Math.round((tot_time/3600),2)+' Hrs ';
                minutes = Math.round((tot_time%60),2)+' minutes';
                if(parseInt(minutes)==0){
                  tot_visittime = hours;
                }else if(parseInt(hours)==0){
                  tot_visittime = minutes;
                }else{
                  tot_visittime = hours+' '+minutes;
                }
              }
              return tot_visittime;
            },
            title: {
              formatter: function (seriesName) {
                return seriesName+': '
              }
            }
          }
        },
        colors: ["#0b7676"]
      };
      $("#time_visit").empty();
      var chart = new ApexCharts(document.querySelector("#time_visit"), options);
      chart.render();
    }

    function noOrderGraph(targets_achieved,targets_toachieve){
      var targetsachieve_val = '';
      var a = 60; var b = 85; c = 100;

      if(targets_achieved>=targets_toachieve){
        targetsachieve_val = targets_toachieve;
      }else{
        targetsachieve_val = targets_achieved;
      }

      Highcharts.chart('noof_order', {
        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false
        },

        title: {
            text: 'No.of Order'
        },

        pane: {
            startAngle: -120,
            endAngle: 120,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: targets_toachieve,
            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',
            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: 'Order'
            },
            plotBands: [{
                from: b,
                to: c,
                color: '#55BF3B' // green
            }, {
                from: a,
                to: b,
                color: '#DDDF0D' // yellow
            }, {
                from: 0,
                to: a,
                color: '#DF5353' // red
            }]
        },
        series: [{
            name: 'No. of Orders',
            data: [targetsachieve_val],
        }],
        credits: {
            enabled: false
        },

        });       
    }

    function noCollGraph(targets_achieved,targets_toachieve){
      var targetsachieve_val = '';
      var a = 60; var b = 85; c = 100;
      if(targets_achieved>=targets_toachieve){
        targetsachieve_val = targets_toachieve;
      }else{
        targetsachieve_val = targets_achieved;
      }
      a = Math.ceil(targets_toachieve/2);
      b = parseInt(a + targets_toachieve/3);
      c = targets_toachieve;
      Highcharts.chart('noof_coll', {
        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false
        },

        title: {
            text: 'No.of Collection'
        },

        pane: {
            startAngle: -120,
            endAngle: 120,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: targets_toachieve,
            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',
            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: 'Collection'
            },
            plotBands: [{
                from: b,
                to: c,
                color: '#55BF3B' // green
            }, {
                from: a,
                to: b,
                color: '#DDDF0D' // yellow
            }, {
                from: 0,
                to: a,
                color: '#DF5353' // red
            }]
        },
        series: [{
            name: 'No. of collections ',
            data: [targetsachieve_val],
        }],
        credits: {
            enabled: false
        },

        });   
    }

    function noVisitGraph(targets_achieved,targets_toachieve){
      var targetsachieve_val = '';
      var a = 60; var b = 85; c = 100;
      if(targets_achieved>=targets_toachieve){
        targetsachieve_val = targets_toachieve;
      }else{
        targetsachieve_val = targets_achieved;
      }
      a = Math.ceil(targets_toachieve/2);
      b = parseInt(a + targets_toachieve/3);
      c = targets_toachieve;
      Highcharts.chart('noof_visit', {
        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false
        },

        title: {
            text: 'No.of Visit'
        },

        pane: {
            startAngle: -120,
            endAngle: 120,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: targets_toachieve,
            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',
            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: 'Visit'
            },
            plotBands: [{
                from: b,
                to: c,
                color: '#55BF3B' // green
            }, {
                from: a,
                to: b,
                color: '#DDDF0D' // yellow
            }, {
                from: 0,
                to: a,
                color: '#DF5353' // red
            }]
        },
        series: [{
            name: 'No. of visits ',
            data: [targetsachieve_val],
        }],
        credits: {
            enabled: false
        },

        });   
    }

    function callsGoldenGraph(targets_achieved,targets_toachieve){
      var targetsachieve_val = '';
      var a = 60; var b = 85; c = 100;
      if(targets_achieved>=targets_toachieve){
        targetsachieve_val = targets_toachieve;
      }else{
        targetsachieve_val = targets_achieved;
      }
      a = Math.ceil(targets_toachieve/2);
      b = parseInt(a + targets_toachieve/3);
      c = targets_toachieve;
      Highcharts.chart('golden_calls', {
        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false
        },

        title: {
            text: 'Golden Calls'
        },

        pane: {
            startAngle: -120,
            endAngle: 120,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: targets_toachieve,
            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',
            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: 'Golden Calls'
            },
            plotBands: [{
                from: b,
                to: c,
                color: '#55BF3B' // green
            }, {
                from: a,
                to: b,
                color: '#DDDF0D' // yellow
            }, {
                from: 0,
                to: a,
                color: '#DF5353' // red
            }]
        },
        series: [{
            name: 'No. of Golden Calls ',
            data: [targetsachieve_val],
        }],
        credits: {
            enabled: false
        },

        });   
    }

    function callsTotalGraph(targets_achieved,targets_toachieve){
      var targetsachieve_val = '';
      var a = 60; var b = 85; c = 100;
      if(targets_achieved>=targets_toachieve){
        targetsachieve_val = targets_toachieve;
      }else{
        targetsachieve_val = targets_achieved;
      }
      a = Math.ceil(targets_toachieve/2);
      b = parseInt(a + targets_toachieve/3);
      c = targets_toachieve;
      Highcharts.chart('prod_calls', {
        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false
        },

        title: {
            text: 'Total Calls'
        },

        pane: {
            startAngle: -120,
            endAngle: 120,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: targets_toachieve,
            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',
            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: 'Total Calls'
            },
            plotBands: [{
                from: b,
                to: c,
                color: '#55BF3B' // green
            }, {
                from: a,
                to: b,
                color: '#DDDF0D' // yellow
            }, {
                from: 0,
                to: a,
                color: '#DF5353' // red
            }]
        },
        series: [{
            name: 'No. of Total Calls ',
            data: [targetsachieve_val],
        }],
        credits: {
            enabled: false
        },

        });   
    }

    function valueOrderGraph(targets_achieved,targets_toachieve){
      var targetsachieve_val = '';
      var a = 60; var b = 85; c = 100;
      if(targets_achieved>=targets_toachieve){
        targetsachieve_val = targets_toachieve;
      }else{
        targetsachieve_val = targets_achieved;
      }
      a = Math.ceil(targets_toachieve/2);
      b = parseInt(a + targets_toachieve/3);
      c = targets_toachieve;
      Highcharts.chart('value_order', {
        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false
        },

        title: {
            text: 'Order Amount'
        },

        pane: {
            startAngle: -120,
            endAngle: 120,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: targets_toachieve,
            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',
            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: 'Order Amount '
            },
            plotBands: [{
                from: b,
                to: c,
                color: '#55BF3B' // green
            }, {
                from: a,
                to: b,
                color: '#DDDF0D' // yellow
            }, {
                from: 0,
                to: a,
                color: '#DF5353' // red
            }]
        },
        series: [{
            name: 'Order Amount ',
            data: [targetsachieve_val],
        }],
        credits: {
            enabled: false
        },

        });   
    }

    function valueCollGraph(targets_achieved,targets_toachieve){
      var targetsachieve_val = '';
      var a = 60; var b = 85; c = 100;
      if(targets_achieved>=targets_toachieve){
        targetsachieve_val = targets_toachieve;
      }else{
        targetsachieve_val = targets_achieved;
      }
      a = Math.ceil(targets_toachieve/2);
      b = parseInt(a + targets_toachieve/3);
      c = targets_toachieve;
      Highcharts.chart('value_coll', {
        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false
        },

        title: {
            text: 'Collection Amount'
        },

        pane: {
            startAngle: -120,
            endAngle: 120,
            background: [{
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF'],
                        [1, '#333']
                    ]
                },
                borderWidth: 0,
                outerRadius: '109%'
            }, {
                backgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#333'],
                        [1, '#FFF']
                    ]
                },
                borderWidth: 1,
                outerRadius: '107%'
            }, {
                // default background
            }, {
                backgroundColor: '#DDD',
                borderWidth: 0,
                outerRadius: '105%',
                innerRadius: '103%'
            }]
        },

        // the value axis
        yAxis: {
            min: 0,
            max: targets_toachieve,
            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#666',
            tickPixelInterval: 30,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 10,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: 'Collection Amount'
            },
            plotBands: [{
                from: b,
                to: c,
                color: '#55BF3B' // green
            }, {
                from: a,
                to: b,
                color: '#DDDF0D' // yellow
            }, {
                from: 0,
                to: a,
                color: '#DF5353' // red
            }]
        },
        series: [{
            name: 'Collection Amount ',
            data: [targetsachieve_val],
        }],
        credits: {
            enabled: false
        },

        });   
    }

    function topPartiesGraph(graphtype, dates, values, currsymbol) {
      if(graphtype=='order'){
        seriesname = 'Order Value';
      }else{
        seriesname = 'Collecion Amount';
      }
      var options = {
        series: [{
          name: seriesname,
          data: values
        }],
      chart: {
        height: 350,
        type: 'bar',
      },
      plotOptions: {
        bar: {
          borderRadius: 10,
          columnWidth: '50%',
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2
      },
      
      grid: {
        row: {
          colors: ['#fff', '#f2f2f2']
        }
      },
      xaxis: {
        labels: {
          rotate: -45
        },
        title: {
          text: 'Parties',
        },
        categories: dates,
        tickPlacement: 'on'
      },
      yaxis: {
        title: {
          text: 'Amount',
        },
        labels: {
          formatter: function(val) {
            return currsymbol+' '+val.toLocaleString("en");
          },
        },
      },
      colors: ["#0b7676"]
      };
      $("#parties_collordamt").empty();
      var chart = new ApexCharts(document.querySelector("#parties_collordamt"), options);
      chart.render();
    }

    function expenseGraph(dates, values, currsymbol) {
      var options = {
          series: values,
          chart: {
            width: 500,
            type: 'pie',
          },
        plotOptions: {
          pie: {
            startAngle: -90,
            endAngle: 270
          }
        },
        dataLabels: {
          enabled: true
        },
        fill: {
          type: 'gradient',
        },
        labels: dates,
        legend: {
          formatter: function(val, opts) {
            expense_label = dates;
            return expense_label[opts.seriesIndex]
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }],
        tooltip: {
          y: {
            formatter: function(val) {
              return currsymbol+' '+val.toLocaleString("en")
            },
            title: {
              formatter: function (seriesName) {
                return seriesName+': '
              }
            }
          }
        },
      };
      $("#expenses").empty();
      var chart = new ApexCharts(document.querySelector("#expenses"), options);
      chart.render();
    }

    function orderPieGraph(dates, values) {
      var options = {
          series: values,
          chart: {
            width: 500,
            type: 'pie',
          },
        plotOptions: {
          pie: {
            startAngle: -90,
            endAngle: 270
          }
        },
        dataLabels: {
          enabled: true,
        },
        fill: {
          type: 'gradient',
        },
        labels: dates,
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
      };
      $('#piechart_div').remove();
      $('#piechart_maindiv').append('<div id="piechart_div" style="min-height:345px!important;max-height:345px!important;min-width:100%!important;" ></div>');
      var chart = new ApexCharts(document.querySelector("#piechart_div"), options);
      chart.render();
    }

    function topOutletsGraph(dates, values) {
      var options = {
          series: values,
          chart: {
            width: 500,
            type: 'donut',
          },
        plotOptions: {
          pie: {
            startAngle: -90,
            endAngle: 270
          }
        },
        dataLabels: {
          enabled: true
        },
        fill: {
          type: 'gradient',
        },
        labels: dates,
        legend: {
          formatter: function(val, opts) {
            expense_label = dates;
            return expense_label[opts.seriesIndex]
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
      };
      $('#piechart_div').remove();
      $('#piechart_maindiv').append('<div id="piechart_div" style="min-height:345px!important;max-height:345px!important;min-width:100%!important;" ></div>');
      var chart = new ApexCharts(document.querySelector("#piechart_div"), options);
      chart.render();
    }
    
    function topBeatsAmtGraph(dates, values, currsymbol, graphtype) {
      if(graphtype=='ordval'){
        yax_text = 'Number';
        seriesname = 'Order';
        cursymb = '';
      }else{
        yax_text = 'Amount';
        seriesname = 'Amount'
        cursymb = currsymbol;
      }
      var options = {
        series: [{
          name: seriesname,
          data: values
        }],
      chart: {
        height: 350,
        type: 'bar',
      },
      plotOptions: {
        bar: {
          borderRadius: 10,
          columnWidth: '50%',
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 2
      },
      
      grid: {
        row: {
          colors: ['#fff', '#f2f2f2']
        }
      },
      xaxis: {
        labels: {
          rotate: -45
        },
        title: {
          text: 'Beats',
        },
        categories: dates,
        tickPlacement: 'on'
      },
      yaxis: {
        title: {
          text: yax_text,
        },
        labels: {
          formatter: function(val) {
            return cursymb+' '+val.toLocaleString("en");
          },
        },
      },
      colors: ["#0b7676"]
      };
      $("#beats_collordamt").empty();
      var chart = new ApexCharts(document.querySelector("#beats_collordamt"), options);
      chart.render();
    }


</script>




@endsection