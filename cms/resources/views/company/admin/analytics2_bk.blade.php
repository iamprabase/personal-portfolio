
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
<!-- <link rel="stylesheet" href="{{asset('assets/bower_components/apexchart/apexcharts.css') }}"/> -->
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

  #qtyloader1, #amtloader1, #partiesamtloader1, #beatsloader1, #piechartloader1, #linegraph1_loader, #linegraph2_loader, #timespentvst_loader, #expenseloader, #prod_partiesloader, #visitormaploader{
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

  ul > li {
    cursor:pointer;
  }

  .box-header {
    display: flex;
    align-items: center;
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


@if(config('settings.orders')==1 || config('settings.party')==1 || config('settings.collections')==1 || config('settings.zero_orders')==1 || config('settings.product')==1 || config('settings.visit_module')==1)
  @if(Auth::user()->can('order-view') || Auth::user()->can('party-view') || Auth::user()->can('collection-view') || Auth::user()->can('zeroorder-view') || Auth::user()->can('product-view') || Auth::user()->can('PartyVisit-view'))
    <div class="row">
      <div class="col-12 col-sm-6 col-md-3" id="ticker1">
        <span class="pull-right">
          <select id="opt1" style="width:20px;background-color:white;border:white;cursor:pointer;">
            <option value="0" oth="ses">TOTAL ORDERS</option>
          </select>
        </span>
        <div class="info-box">
          <span class="info-box-icon bg-aqua elevation-1"><i class="fa fa-shopping-cart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text" id="col1name">Total Orders</span>
            <span class="info-box-number" id="col1val">0</span>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3" id="ticker2">
        <span class="pull-right">
          <select id="opt2" style="width:20px;background-color:white;border:white;cursor:pointer;">
            <option value="0">NEW PARTIES <br>ADDED</option>
          </select>
        </span>
        <div class="info-box mb-3">
          <span class="info-box-icon bg-green elevation-1"><i class="fa fa-user-plus"></i></span>
          <div class="info-box-content">
            <span class="info-box-text" id="col2name">NEW PARTIES <br>ADDED</span>
            <span class="info-box-number" id="col2val">0</span>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3" id="ticker3">
        <span class="pull-right">
          <select id="opt3" style="width:20px;background-color:white;border:white;cursor:pointer;">
            <option value="0">CHEQUES TO BE <br> DEPOSITED</option>
          </select>
        </span>
        <div class="info-box mb-3">
          <span class="info-box-icon bg-maroon elevation-1"><i class="fa fa-fw fa-money"></i></span>
          <div class="info-box-content">
            <span class="info-box-text" id="col4name">CHEQUES TO BE <br> DEPOSITED</span>
            <span class="info-box-number" id="col4val">0</span>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3" id="ticker4">
        <span class="pull-right">
          <select id="opt4" style="width:20px;background-color:white;border:white;cursor:pointer;">
            <option value="0">NO. OF ZERO<br>ORDERS</option>
          </select>
        </span>
        <div class="info-box mb-3">
          <span class="info-box-icon bg-teal elevation-1"><i class="fa fa-cart-arrow-down"></i></span>
          <div class="info-box-content">
            <span class="info-box-text" id="col4name">NO. OF ZERO<br>ORDERS</span>
            <span class="info-box-number" id="col4val">0</span>
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
        <!-- MAP & BOX PANE -->
        <div class="box box-default" id="visitormap_maindiv">
          <!-- /.box-header -->
          <div class="box-body no-padding" style="min-height:480px;">
            <div id="visitormaploader" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <div id="visitormap" style="height:480px;width: 100%;border:1px solid #98ddca;"></div>
            <!-- /.row -->
          </div>
          <!-- /.box-body -->
        </div>
      </div>
    </div>
  @endif
@endif




@if(config('settings.product')==1 && config('settings.orders')==1)
  <div class="row">
    <div class="col-md-12">
      <div class="box box-default" id="qtygraph1">
        <div class="box-header with-border">
          <h3 class="box-title" id="qtytitle_opt1">Top Products By No. of Units Sold</h3>
            <div class="box-tools">
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
          <a class="btn btn-primary" style="margin-left: auto;" id="ordval_n" custattr="products" onclick="qtytypefind('ordval')">By value</a>
          <a class="btn btn-primary" style="margin-left: 5px;" id="unitssold_n" custattr="products" onclick="qtytypefind('unitssold')">By units sold</a>
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

@if(config('settings.party')==1 && config('settings.visit_module')==1)
  @if(Auth::user()->can('party-view') && Auth::user()->can('PartyVisit-view'))
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

@if((config('settings.party')==1 && config('settings.collections')==1) || (config('settings.party')==1 && config('settings.orders')==1))
  @if(Auth::user()->can('party-view'))
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default" id="parties_divcollordamt">
          <div class="box-header with-border">
            <h3 class="box-title">Top Parties&nbsp;(<span id="partiesgraph_typename">Order Value</span>)</h3>
            @if(config('settings.collections')==1)
              @if(Auth::user()->can('collection-view'))
                <a class="btn btn-primary pull-right" style="margin-left: auto;" id="parties_collvalamt" onclick="parties_collamt()">By Collection</a>
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

@if((config('settings.beat')==1 && config('settings.orders')==1) || (config('settings.beat')==1 && config('settings.collections')==1))
  @if(Auth::user()->can('beat-plan-view')) 
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default" id="beats_divcollordamt">
          <div class="box-header with-border">
            <h3 class="box-title">Top Performing Beats&nbsp;(<span id="beats_graphname">Order Value</span>)</h3>
            @if(config('settings.collections')==1 )
              @if(Auth::user()->can('collection-view'))
                <a class="btn btn-primary pull-right" style="margin-left: auto;" id="beats_collvalamt" onclick="topbeats(this)">By Collection</a>
              @endif
            @endif
            @if(config('settings.orders')==1 )
              @if(Auth::user()->can('order-view'))
                <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="beats_ordvalamt" onclick="topbeats(this)">By Order Value</a>
                <a class="btn btn-primary pull-right" style="margin-left: 5px;" id="beats_ordval" onclick="topbeats(this)">By No. of Orders</a>
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
  @if((config('settings.orders')==1 && config('settings.zero_orders')==1)) 
    @if((Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view'))) 
        <div class="col-md-6">
          <div class="box box-default" id="div_piechart">
            <div class="box-header with-border">
              <h3 class="box-title" id="piecharts_header">Order Vs Zero Order</h3>
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
              <div id="expenses" style="min-height:357px!important;max-height:357px!important;min-width:100%!important;"></div>
            </div>
          </div>
        </div>
    @endif
  @endif
</div>

<div class="row">
@if((config('settings.retailer_app')==1 && (config('settings.orders')==1)))
   @if((Auth::user()->can('outlet-view') && Auth::user()->can('order-view')))
    <div class="col-md-6">
      <div class="box box-default" id="prod_parties">
        <div class="box-header with-border">
          <h3 class="box-title">Productive Parties</h3>
        </div>
        <div class="box-body" id="prod_maindiv">
          <div id="prod_partiesloader" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <div id="prod_parties_maindiv" style="min-height:345px!important;max-height:345px!important;min-width:100%!important;" ></div>
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
<!-- <script src="{{ asset('assets/bower_components/apexchart/apexcharts.js') }}"></script> -->
<script src="{{ asset('assets/bower_components/highcharts/highchart.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/highcharts-more.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/exporting.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/export-data.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/accessibility.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/solid-gauge.js') }}"></script>


<script>   

    var markersArray = [];
    
    function changeTicker1(tk1,attrb){
      var selectedvalue = tk1.id;
      var selectedtext = tk1.text;
      var currentclass = $("#ticker1_icon i").attr('class');
      if(attrb=='totord'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cart-arrow-down');
        $("#iicons_1").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='newpart'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-user-plus');
        $("#iicons_1").empty();
      }else if(attrb=='chq2dep'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cc');
        $("#iicons_1").empty();
      }else if(attrb=='nozoord'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-opencart');
        $("#iicons_1").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Non-effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='prodsold'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-basket');
        $("#iicons_1").empty();
      }else if(attrb=='effoutl'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-adjust');
        $("#iicons_1").empty();
      }else if(attrb=='totvstm'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-clock-o');
        $("#iicons_1").empty();
      }else if(attrb=='totordat'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-random');
        $("#iicons_1").empty().append('<span style="margin-left:1px;" class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Orders + Zero Orders"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='ordval'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cart-plus');
        $("#iicons_1").empty();
      }else if(attrb=='totcoll'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-money');
        $("#iicons_1").empty();
      }else if(attrb=='totvis'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-thumb-tack');
        $("#iicons_1").empty();
      }
      $("#col1name").text(selectedtext); 
      $("#col1val").text(selectedvalue); 
    }

    function changeTicker2(tk2,attrb){
      var selectedvalue = tk2.id;
      var selectedtext = tk2.text;
      var currentclass = $("#ticker2_icon i").attr('class');
      if(attrb=='totord'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cart-arrow-down');
        $("#iicons_2").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='newpart'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-user-plus')
        $("#iicons_2").empty();;
      }else if(attrb=='chq2dep'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cc');
        $("#iicons_2").empty();
      }else if(attrb=='nozoord'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-opencart');
        $("#iicons_2").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Non-effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='prodsold'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-basket');
        $("#iicons_2").empty();
      }else if(attrb=='effoutl'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-adjust');
        $("#iicons_2").empty();
      }else if(attrb=='totvstm'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-clock-o');
        $("#iicons_2").empty();
      }else if(attrb=='totordat'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-random');
        $("#iicons_2").empty().append('<span class="fa fa-info-circle" style="margin-left:1px;" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Orders + Zero Orders"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='ordval'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cart-plus');
        $("#iicons_2").empty();
      }else if(attrb=='totcoll'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-money');
        $("#iicons_2").empty();
      }else if(attrb=='totvis'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-thumb-tack');
        $("#iicons_2").empty();
      }
      $("#col2name").text(selectedtext); 
      $("#col2val").text(selectedvalue); 
    }

    function changeTicker3(tk3,attrb){
      var selectedvalue = tk3.id;
      var selectedtext = tk3.text;
      var currentclass = $("#ticker3_icon i").attr('class');
      if(attrb=='totord'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cart-arrow-down');
        $("#iicons_3").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='newpart'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-user-plus');
        $("#iicons_3").empty();
      }else if(attrb=='chq2dep'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cc');
        $("#iicons_3").empty();
      }else if(attrb=='nozoord'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-opencart');
        $("#iicons_3").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Non-effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='prodsold'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-basket');
        $("#iicons_3").empty();
      }else if(attrb=='effoutl'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-adjust');
        $("#iicons_3").empty();
      }else if(attrb=='totvstm'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-clock-o');
        $("#iicons_3").empty();
      }else if(attrb=='totordat'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-random');
        $("#iicons_3").empty().append('<span class="fa fa-info-circle" style="margin-left:1px;" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Orders + Zero Orders"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='ordval'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cart-plus');
        $("#iicons_3").empty();
      }else if(attrb=='totcoll'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-money');
        $("#iicons_3").empty();
      }else if(attrb=='totvis'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-thumb-tack');
        $("#iicons_3").empty();
      }
      $("#col3name").text(selectedtext); 
      $("#col3val").text(selectedvalue); 
    }

    function changeTicker4(tk4,attrb){
      var selectedvalue = tk4.id;
      var selectedtext = tk4.text;
      var currentclass = $("#ticker4_icon i").attr('class');
      if(attrb=='totord'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cart-arrow-down');
        $("#iicons_4").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='newpart'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-user-plus');
        $("#iicons_4").empty();
      }else if(attrb=='chq2dep'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cc');
        $("#iicons_4").empty();
      }else if(attrb=='nozoord'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-opencart');
        $("#iicons_4").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Non-effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='prodsold'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-basket');
        $("#iicons_4").empty();
      }else if(attrb=='effoutl'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-adjust');
        $("#iicons_4").empty();
      }else if(attrb=='totvstm'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-clock-o');
        $("#iicons_4").empty();
      }else if(attrb=='totordat'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-random');
        $("#iicons_4").empty().append('<span class="fa fa-info-circle" style="margin-left:1px;" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Orders + Zero Orders"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='ordval'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-cart-plus');
        $("#iicons_4").empty();
      }else if(attrb=='totcoll'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-money');
        $("#iicons_4").empty();
      }else if(attrb=='totvis'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-thumb-tack');
        $("#iicons_4").empty();
      }
      $("#col4name").text(selectedtext); 
      $("#col4val").text(selectedvalue); 
    } 

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


    function countParameters(startDate, endDate, type){
      $.post("{{ domain_route('company.admin.fetchtickercountdata') }}",{start_date: startDate, end_date: endDate, type},function(res){
        if(res!='' || res!='undefined' && (typeof(res.data)=='object')){
          $('#ticker1').empty().html(res.data.ticker1);
          $('#ticker2').empty().html(res.data.ticker2);
          $('#ticker3').empty().html(res.data.ticker3);
          $('#ticker4').empty().html(res.data.ticker4);
          $("#sideticker").empty().html(res.data.sideticker);
          $("#sideticker2").empty().html(res.data.sideticker2);   
          
          @if(Auth::user()->can('order-view'))
            $("#ticker1").find(".changeTicker1").click();
          @endif
          @if(Auth::user()->can('zeroorder-view'))
            $("#ticker4").find(".changeTicker4_z").click();
          @endif
        }
      });
    } 

    function clearOverlays() {
      for (var i = 0; i < markersArray.length; i++ ) {
        markersArray[i].setMap(null);
      }
      markersArray.length = 0;
    }

    function numberWithCommas(x) {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function addClientVisitMarker(){
      var today_locations = yesterday_locations = [];
      var value_start = value_end = '00:00:00';
      var startDate = window.sessionStorage.getItem('startdate');
      var endDate = window.sessionStorage.getItem('enddate');
      var type = window.sessionStorage.getItem('caltype');
      $.ajax({
        method: 'post', 
        data:{start_date: startDate, end_date: endDate, type},
        url: "{{ domain_route('company.admin.fetchtvisitlocations') }}",
        success:function(res){
          td_location = res.data.client_visit;
          var currencysym = res.data.currency_symbol;
          clearOverlays();
          $.each(td_location,function(a,b){
            if(b.latitude!='' && b.longitude!=''){
              var marker, i;
              var infowindow = new google.maps.InfoWindow();
              var timespent = b.visittimespent;
              var totorders = parseInt(b.total_orders);
              var totnoorders = parseInt(b.noorder);
              var allorders = totorders+totnoorders;
              var totalvisits = b.visits;
              var totalordervalue = (!Number.isNaN(b.ordervalue) && b.ordervalue>0)?numberWithCommas(b.ordervalue):b.ordervalue;
              var totalcollection = (!Number.isNaN(b.collvalue) && b.collvalue>0)?numberWithCommas(b.collvalue):b.collvalue; 
              marker = new google.maps.Marker({
                  position: new google.maps.LatLng(b.latitude, b.longitude),
                  map: map2,
                  icon: {
                      url: "/assets/dist/img/markers/Official_7.png",
                      scaledSize: new google.maps.Size(25, 40),
                  },
                  title: b.clientname,
              });
              google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
                    return function() {
                      var partyalldetails = '';
                      let partyname = "<b><u>"+b.clientname+"</u></b>";
                      partyalldetails += partyname;
                      @if(config('settings.orders')==1)
                        @if(Auth::user()->can('order-view'))
                          let totorder_value = "<br/>Order Value: "+currencysym+' '+totalordervalue;   
                          partyalldetails += totorder_value;
                          let tot_order = "<br/>No. of Orders: "+totorders;
                          partyalldetails += tot_order;
                        @endif
                      @endif
                      @if(config('settings.zero_orders')==1)
                        @if(Auth::user()->can('zeroorder-view'))
                          let tot_noorder = "<br/>No. of Zero Orders: "+totnoorders;
                          partyalldetails += tot_noorder;
                        @endif
                      @endif
                      @if(config('settings.orders')==1 && config('settings.zero_orders')==1)
                        @if(Auth::user()->can('order-view') && Auth::user()->can('zeroorder-view'))
                          let all_orders = "<br/>Total Calls: "+allorders;        
                          partyalldetails += all_orders;   
                        @endif
                      @endif      
                      @if(config('settings.visit_module')==1)
                        @if(Auth::user()->can('PartyVisit-view'))
                          let total_visits = "<br/>Total Visits: "+totalvisits;  
                          partyalldetails += total_visits;                  
                          let time_spent = "<br/>Time Spent on Visits: "+timespent;
                          partyalldetails += time_spent;
                        @endif
                      @endif
                      @if(config('settings.collections')==1)
                        @if(Auth::user()->can('collection-view'))
                          let totcoll_value = "<br/>Collection: "+currencysym+' '+totalcollection; 
                          partyalldetails += totcoll_value;
                        @endif
                      @endif

                      infowindow.setContent(partyalldetails);
                      infowindow.open(map2, marker);
                    }
              })(marker, i));
              google.maps.event.addListener(marker, 'mouseout', function () {
                  infowindow.close();
              });
              markersArray.push(marker);
            }
          })
        }, 
        beforeSend:function(){
          $('#visitormap_maindiv').addClass('box-loader');
          $('#visitormaploader').removeAttr('hidden');
        },
        complete:function(){
          $('#visitormap_maindiv').removeClass('box-loader');
          $('#visitormaploader').attr('hidden', 'hidden');
        }
      })
    }
   

    

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
            $("#"+divid+"").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
            $("#"+divid+"").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
            $("#beats_collordamt").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
                $("#piechart_div").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
              $("#piechart_div").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
            $("#parties_collordamt").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
            $("#parties_collordamt").html("<span class='sptextalign'>No Data Available for selected period</span>");
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


      countParameters(startDate, endDate, type);
      addClientVisitMarker();

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
              // $("#ordzero_order").trigger('click');
            @endif
          @elseif(config('settings.retailer_app')==1) 
            @if(Auth::user()->can('outlet-view'))
              // $("#outlets").trigger('click');
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
            $("#time_visit").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
        url: "{{ domain_route('company.admin.fetchtopoutlets') }}",
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
            $("#prod_parties_maindiv").html("<span class='sptextalign'>No Data Available for selected period</span>");
          }else{
            $("#prod_parties_maindiv").empty();
            topOutletsGraph(expenseDates, expenseValues);
          }
        },
        beforeSend:function(){
          $('#prod_parties').addClass('box-loader');
          $('#prod_partiesloader').removeAttr('hidden');
        },
        complete:function(){
          $('#prod_parties').removeClass('box-loader');
          $('#prod_partiesloader').attr('hidden', 'hidden');
        }
      });


      $.ajax({
        method: 'post',
        url: "{{ domain_route('company.admin.fetchtotalzeroorder') }}",
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
            $("#piechart_div").html("<span class='sptextalign'>No Data Available for selected period</span>");
          }else{
            $("#piechart_div").empty();
            orderPieGraph(expenseDates, expenseValues);
          }
        },
        beforeSend:function(){
          $('#div_piechart').addClass('box-loader');
          $('#piechartloader1').removeAttr('hidden');
        },
        complete:function(){
          $('#div_piechart').removeClass('box-loader');
          $('#piechartloader1').attr('hidden', 'hidden');
        }
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
            $("#expenses").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
            $("#linegraphs1").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
            $("#linegraphs2").html("<span class='sptextalign'>No Data Available for selected period</span>");
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
      $("#"+divid).empty();
      values=values.map(Number);
      Highcharts.chart(divid, {
        series: [{
          name: ytext,
          data: values
        }],
        chart: {
          type: 'bar'
        },
        title: {
          text: ''
        },
        xAxis: {
          categories: dates,
           title: {
            text: xtext
          },
        },
        yAxis: {
          title: {
            text: ytext
          },
          allowDecimals: false,
        },
        legend: {
          enabled: false
        },
        plotOptions: {
          column: {
            pointWidth: 20,
            color: '#0b7676'
          }
        },
        responsive: {
          rules: [{
            condition: {
              maxWidth: 1366
            },
          }]
        },
        credits: {
            enabled: false
        },
        colors: ["#0b7676"]
      });
      
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
      $("#"+divid).empty();

      values=values.map(Number);
      Highcharts.chart(divid, {
        series: [{
          name: ytext+' '+currsymbol+' ',
          data: values
        }],
        chart: {
          type: 'bar'
        },
        title: {
          text: ''
        },
        xAxis: {
          categories: dates,
          title: {
            text: xtext
          },
        },
        yAxis: {
          title: {
            text: ytext
          },
          allowDecimals: false,
        },
        legend: {
          enabled: false
        },
        plotOptions: {
          column: {
            pointWidth: 20,
            color: '#0b7676'
          }
        },
        responsive: {
          rules: [{
            condition: {
              maxWidth: 1366
            },
          }]
        },
        credits: {
            enabled: false
        },
        colors: ["#0b7676"]
      });

    }

    function plotLineGraphs(dates, totalorder, zeroorder, totalcalls, totalvisits, totprodsold, partiesadded, totcollections){
      $("#linegraphs1").empty();
    Highcharts.setOptions({
              lang: {
                  thousandsSep: ','
              }
          });
      Highcharts.chart('linegraphs1', {
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
          title: {
              text: ''
          },
          chart: {
              type: 'line'
          },
          yAxis: {
              title: {
                  text: 'Number'
              }
          },
          xAxis: {
              categories: dates,
              title: {
                text: 'Date'
              },
          },
          legend: {
              layout: 'horizontal',
              align: 'right',
              verticalAlign: 'top',
              itemMarginTop: 10,
          },
          responsive: {
              rules: [{
                  condition: {
                      maxWidth: 500
                  },
                  chartOptions: {
                      legend: {
                          layout: 'horizontal',
                          align: 'center',
                          verticalAlign: 'bottom'
                      }
                  }
              }]
          },
        credits: {
            enabled: false
        },
      });

    }

    function plotLineGraphs2(dates, totalorder, totcoll, amtcurrency){
      $("#linegraphs2").empty();
      Highcharts.setOptions({
                lang: {
                    thousandsSep: ','
                }
            });
      Highcharts.chart('linegraphs2', {
          series: [
              @if(config('settings.orders')==1 )
              @if(Auth::user()->can('order-view'))
                {
                  name: 'Order Value ',
                  data: totalorder
                },
              @endif
            @endif
            @if(config('settings.collections')==1 )
              @if(Auth::user()->can('collection-view'))
                {
                  name: 'Payment Collection ',
                  data: totcoll
                },
              @endif
            @endif
          ],
          title: {
              text: ''
          },
          chart: {
              type: 'line'
          },
          yAxis: {
              title: {
                  text: 'Amount'
              }
          },
          xAxis: {
              categories: dates,
              title: {
                text: 'Date'
              },
          },
          tooltip: {
            pointFormat: '{series.name}: <b>'+amtcurrency+' {point.y}</b>'
          },
          legend: {
              layout: 'horizontal',
              align: 'right',
              verticalAlign: 'top',
              itemMarginTop: 10,
          },
          responsive: {
              rules: [{
                  condition: {
                      maxWidth: 500
                  },
                  chartOptions: {
                      legend: {
                          layout: 'horizontal',
                          align: 'center',
                          verticalAlign: 'bottom'
                      }
                  }
              }]
          },
        credits: {
            enabled: false
        },
      });

    }

    function timeSpentVistGraph(dates, values) {
      var tot_visittime = hours = minutes = second = '';
      $("#time_visit").empty();
      var tot_time = values;
      Highcharts.chart('time_visit', {
          series: [{
          name: 'Time',
          data: values
        }],
          title: {
              text: ''
          },
          chart: {
              type: 'line'
          },
          yAxis: {
              title: {
                  text: 'Time(in seconds)'
              }
          },
          xAxis: {
              categories: dates,
              title: {
                text: 'Date'
              },
          },
          tooltip: {
            formatter: function() {
                // If you want to see what is available in the formatter, you can
                // examine the `this` variable.
                    // console.log(this.y);
                tot_time = this.y;
                if(tot_time>=0 && tot_time<=60){
                  tot_visittime = tot_time+' Seconds';
                }else if(tot_time>60 && tot_time<=3600){ 
                  minutes = parseInt(tot_time/60)+' minutes ';
                  second = parseInt(tot_time%60)+' seconds';
                  if(parseInt(second)==0){
                    tot_visittime = minutes;
                  }else{
                    tot_visittime = minutes+' '+second;
                  }
                }else{
                  hours = parseInt(tot_time/3600)+' Hrs ';
                  minutes = parseInt(tot_time%60)+' minutes';
                  if(parseInt(minutes)==0){
                    tot_visittime = hours;
                  }else if(parseInt(hours)==0){
                    tot_visittime = minutes;
                  }else{
                    tot_visittime = hours+' '+minutes;
                  }
                }
                return '<b>Total Visit Time: </b>'+tot_visittime;
            }
          },
          legend: {
              layout: 'horizontal',
              align: 'right',
              verticalAlign: 'top',
              itemMarginTop: 10,
          },
          responsive: {
              rules: [{
                  condition: {
                      maxWidth: 500
                  },
                  chartOptions: {
                      legend: {
                          layout: 'horizontal',
                          align: 'center',
                          verticalAlign: 'bottom'
                      }
                  }
              }]
          },
        colors: ["#0b7676"],
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
      $("#parties_collordamt").empty();
      Highcharts.setOptions({
          lang: {
              thousandsSep: ','
          }
      });
      values=values.map(Number);
      Highcharts.chart('parties_collordamt', {
        chart: {
          type: 'column'
        },
        title: {
          text: ''
        },
        xAxis: {
          categories: dates,
          title: {
            text: 'Parties'
          },
        },
        yAxis: {
          title: {
            text: 'Amount'
          },
          allowDecimals: false,
        },
        legend: {
          enabled: false
        },
        plotOptions: {
          column: {
            pointWidth: 20,
            color: '#0b7676'
          }
        },
        series: [{
          name: seriesname+' '+currsymbol+' ',
          data: values
        }],
        responsive: {
          rules: [{
            condition: {
              maxWidth: 1366
            },
          }]
        },
        credits: {
            enabled: false
        },
      });

    }

    function expenseGraph(dates, values, currsymbol) {
      values=values.map(Number);
      var name = dates;
      var data = values;
      var finaldata = [];
      for(var i=0; i < name.length; i++) {
          finaldata.push({
              name: name[i],
              y: data[i]           
          });        
      }  
      $("#expenses").empty();
      Highcharts.setOptions({
          lang: {
              thousandsSep: ','
          }
      });
      Highcharts.chart('expenses', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        tooltip: {
          pointFormat: '{series.name}: <b>'+currsymbol+' {point.y} ({point.percentage:.1f} %)</b>'
        },
        accessibility: {
            point: {
                valueSuffix: currsymbol
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Expenses',
            colorByPoint: true,
            data: finaldata
        }],
        credits: {
            enabled: false
        },
      });
    }

    function orderPieGraph(dates, values) {
      values=values.map(Number);
      var name = dates;
      var data = values;
      var finaldata = [];
      for(var i=0; i < name.length; i++) {
          finaldata.push({
              name: name[i],
              y: data[i]           
          });        
      }
      $('#piechart_div').remove();
      $('#piechart_maindiv').append('<div id="piechart_div" style="min-height:345px!important;max-height:345px!important;min-width:100%!important;" ></div>');
      Highcharts.setOptions({
          lang: {
              thousandsSep: ','
          }
      });
      Highcharts.chart('piechart_div', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f} %)</b>'
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
          }
        },
        series: [{
            name: 'Orders',
            colorByPoint: true,
            data: finaldata
        }],
        credits: {
            enabled: false
        },
      });
    }

    function topOutletsGraph(dates, values) {
      values=values.map(Number);
      var mname = dates;
      var dat = values;
      var finaldata = [];
      for(var i=0; i < mname.length; i++) {
          finaldata.push({
              name: mname[i],
              y: dat[i]           
          });        
      }
      var chk = Math.round((dat[1]/dat[0])*100,2);
      if(chk==undefined || chk==Infinity || Number.isNaN(chk)){
        chk = 0;
      }
      $('#prod_parties_maindiv').remove();
      $('#prod_maindiv').append('<div id="prod_parties_maindiv" style="min-height:345px!important;max-height:345px!important;min-width:100%!important;" ></div>');


      Highcharts.chart('prod_parties_maindiv', {
        chart: {
          type: 'pie'
        },
        title: {
          text: ''
        },
        subtitle: {
          text: 'productive<br>parties: <span>'+chk+'%</san>',
          align: "center",
          verticalAlign: "middle",
          style: {
            "fontSize": "20px",
            "textAlign": "center"
          },
          x: 0,
          y: -2,
          useHTML: true
        },
        plotOptions: {
          pie: {
            shadow: false,
            center: ["50%", "50%"],
            dataLabels: {
              enabled: false
            },
            states: {
              hover: {
                enabled: false
              }
            },
            showInLegend: true,
            size: "100%",
            innerSize: "80%",
            borderColor: null,
            borderWidth: 10
          }

        },
        tooltip: {
          pointFormat: '<b>{point.y} ({point.percentage:.1f} %)</b>'
        },
        series: [{
          // innerSize: '80%',
          data: finaldata
        }],
        credits: {
          enabled: false
        },
      });




      // Highcharts.setOptions({
      //     lang: {
      //         thousandsSep: ','
      //     }
      // });
      // Highcharts.chart('prod_parties_maindiv', {
      //     chart: {
      //         plotBackgroundColor: null,
      //         plotBorderWidth: 0,
      //         plotShadow: false
      //     },
      //     title: {
      //         text: '',
      //     },
      //   tooltip: {
      //       pointFormat: '<b>{point.y} ({point.percentage:.1f} %)</b>'
      //   },
      //     accessibility: {
      //         point: {
      //             valueSuffix: '%'
      //         }
      //     },
      //     plotOptions: {
      //         pie: {
      //             dataLabels: {
      //                 enabled: true,
      //                 distance: -50,
      //                 style: {
      //                     fontWeight: 'bold',
      //                     color: 'white'
      //                 }
      //             },
      //             startAngle: -90,
      //             endAngle: 90,
      //             center: ['50%', '75%'],
      //             size: '110%'
      //         }
      //     },
      //     credits: {
      //       enabled: false
      //     },
      //     series: [{
      //         type: 'pie',
      //         innerSize: '50%',
      //         data: finaldata2
      //     }]
      // });
    
      // Highcharts.chart('piechart_div', {
      //   chart: {
      //       plotBackgroundColor: null,
      //       plotBorderWidth: null,
      //       plotShadow: false,
      //       type: 'pie'
      //   },
      //   title: {
      //       text: ''
      //   },
      //   tooltip: {
      //       pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f} %)</b>'
      //   },
      //   accessibility: {
      //       point: {
      //           valueSuffix: 'mmmm'
      //       }
      //   },
      //   plotOptions: {
      //     pie: {
      //       allowPointSelect: true,
      //       cursor: 'pointer',
      //       dataLabels: {
      //           enabled: false
      //       },
      //       showInLegend: true
      //     }
      //   },
      //   series: [{
      //       name: 'Orders',
      //       colorByPoint: true,
      //       data: finaldata
      //   }],
        // credits: {
        //     enabled: false
        // },
      // });
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
      $("#beats_collordamt").empty();
      values=values.map(Number);
      Highcharts.setOptions({
          lang: {
              thousandsSep: ','
          }
      });
      Highcharts.chart('beats_collordamt', {
        chart: {
          type: 'column'
        },
        title: {
          text: ''
        },
        xAxis: {
          categories: dates,
          title: {
            text: 'Beats'
          },
        },
        yAxis: {
          title: {
            text: yax_text
          },
          allowDecimals: false,
        },
        legend: {
          enabled: false
        },
        plotOptions: {
          column: {
            pointWidth: 20,
            color: '#0b7676'
          }
        },
        series: [{
          name: seriesname+' '+cursymb+' ',
          data: values
        }],
        responsive: {
          rules: [{
            condition: {
              maxWidth: 1366
            },
          }]
        },
        credits: {
            enabled: false
        },
      });
    }


</script>




@endsection