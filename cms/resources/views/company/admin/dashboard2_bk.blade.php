
@extends('layouts.company') 
@section('title', 'Dashboard')
@section('title', 'Company Dashboard')
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

  #qtyloader1, #amtloader1, #partiesamtloader1, #beatsloader1, #piechartloader1{
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

  .box-body #expenses {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
  }

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

  ul > li {
    cursor:pointer;
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
    <!-- <div class="row">
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
    </div> -->
  @endif
@endif

  <div class="row">
    @if(config('settings.visit_module')==1)
      @if(Auth::user()->can('PartyVisit-view'))
        <!-- <div class="col-md-9">
          <div class="box box-default">
            <div class="box-body no-padding" style="min-height:480px;">
              <div class="row">
                <div class="col-md-9 col-sm-8" id="visitormapdiv">
                  <div id="visitormap" style="height:480px;width: 135%;border:1px solid #98ddca;"></div>
                </div>
              </div>
            </div>
          </div>
        </div> -->
      @endif
    @endif

    <div class="col-md-3">
      <div class="box box-default">
        <div class="box-body no-padding">
            <ul class="nav nav-pills nav-stacked" id="sideticker">
              <li style="font-weight:bold;"><a href="#"><i class="fa fa-file-text"></i> Present Today
                  <span class="label label-primary pull-right">0</span></a></li>
            </ul>
          </div>
      </div>

    @if(config('settings.activities')==1)
      @if(Auth::user()->can('activity-view'))
        <div class="box box-default">
          <div class="box-header with-border">
              <h3 class="box-title">Activities</h3>
          </div>
          <div class="box-body no-padding">
              <ul class="nav nav-pills nav-stacked" id="sideticker2">
                  <li style="font-weight:bold;"><a href="#"><i class="fa fa-text"></i>Scheduled
                    <span class="label label-primary pull-right">0</span></a></li>
                  <li style="font-weight:bold;"><a href="#"><i class="fa fa-envelope"></i>Overdue
                    <span class="label label-primary pull-right">0</span></a></li>
                  <li style="font-weight:bold;"><a href="#"><i class="fa fa-filter"></i>Completed
                    <span class="label label-primary pull-right">0</span></a></li>
                  <li style="font-weight:bold;"><a href="#"><i class="fa fa-inbox"></i>Due Today
                    <span class="label label-primary pull-right">0</span></a></li>
              </ul>
          </div>
        </div>
      @endif
    @endif
    </div>
  </div>

<div class="row">
  @if(Auth::user()->can('employee-view'))
      <div class="col-md-4">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Employees&nbsp;&nbsp;<span class='label label-success' id="employee_count"></span></h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
            </div>
            <a href="{{ domain_route('company.admin.employee') }}" class="btn btn-sm btn-flat pull-right">View All</a>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="table-responsive" style="max-height:185px;min-height:185px;">
              <table class="table no-margin">
                <!-- <thead>
                <tr>
                  <th>Employee Type</th>
                  <th>Quantity</th>
                </tr>
                </thead> -->
                <tbody id="employee_body">
                  <tr>
                      <td colspan="2">No Data Available</td>
                    </tr>
                </tbody>
              </table>
            </div>
            <!-- /.table-responsive -->
          </div>
          <!-- /.box-body -->
        </div>
      </div>
  @endif

  @if(config('settings.party')==1)
    @if(Auth::user()->can('party-view'))
        <div class="col-md-4">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Parties&nbsp;&nbsp;<span class='label label-success' id="parties_count"></span></h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <a href="{{ domain_route('company.admin.client') }}" id="partieschk_viewall" class="btn btn-sm btn-flat pull-right">View All</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive" style="min-height:185px;max-height:185px;">
                <table class="table no-margin">
                  <!-- <thead>
                  <tr>
                    <th>Party Type</th>
                    <th>Party Quantity</th>
                  </tr> -->
                  </thead>
                  <tbody id="parties_body">
                    <tr>
                        <td colspan="2">No Data Available</td>
                      </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>
        </div>
    @endif
  @endif

  @if(config('settings.product')==1)
    @if(Auth::user()->can('product-view'))
        <div class="col-md-4">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Products&nbsp;&nbsp;<span class='label label-success' id="products_count"></span></h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <a href="{{ domain_route('company.admin.product') }}" class="btn btn-sm btn-flat pull-right">View All</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive" style="min-height:185px;max-height:185px;">
                <table class="table no-margin">
                  <thead>
                  <!-- <tr>
                    <th>Product Name</th>
                    <th>Product Quantity</th>
                  </tr> -->
                  </thead>
                  <tbody id="products_body">
                  @if(Auth::user()->can('settings-view') || Auth::user()->employee()->first()->role()->first()->name=="Full Access")
                    <tr>
                      <td>Brands</td>
                      <td><span class="label label-success">0</span></td>
                    </tr>
                    <tr>
                      <td>Categories</td>
                      <td><span class="label label-success">0</span></td>
                    </tr>
                  @endif
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>
        </div>
    @endif
  @endif
</div>


<div class="row">
  @if(config('settings.orders')==1)
    @if(Auth::user()->can('order-view'))
      <div class="col-md-6">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Latest Orders</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
            </div>
            <a href="{{ domain_route('company.admin.order') }}" class="btn btn-sm btn-flat pull-right">View All Orders</a>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="table-responsive" style="min-height:370px;max-height:370px;">
              <table class="table no-margin">
                <!-- <thead>
                <tr>
                  <th>Order Total</th>
                  <th>Status</th>
                </tr> -->
                </thead>
                <tbody id="latest_orders">
                  <tr>
                      <td colspan="3">No Data Available</td>
                    </tr>
                </tbody>
              </table>
            </div>
            <!-- /.table-responsive -->
          </div>
          <!-- /.box-body -->
        </div>
      </div>
    @endif
  @endif

  @if(config('settings.collections')==1)
    @if(Auth::user()->can('collection-view'))
        <div class="col-md-6">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Latest Collections</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <a href="{{ domain_route('company.admin.collection') }}" class="btn btn-sm btn-flat pull-right">View All</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive" style="min-height:370px;max-height:370px;">
                <table class="table no-margin">
                  <!-- <thead>
                  <tr>
                    <th>Payment Type</th>
                    <th>Received Amount</th>
                    <th>Status</th>
                  </tr>
                  </thead> -->
                  <tbody id="latest_coll_body">
                      <tr>
                        <td colspan="3">No Data Available</td>
                      </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>
        </div> 
    @endif
  @endif
</div>

<div class="row">
  @if(config('settings.party')==1)
    @if(Auth::user()->can('party-view'))
        <div class="col-md-6">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Newly Added Parties</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive" style="min-height:370px;max-height:370px;">
                <table class="table no-margin">
                  <!-- <thead>
                  <tr>
                    <th>Client Name</th>
                    <th>Visit Time</th>
                    <th>Visit Date</th>
                  </tr>
                  </thead> -->
                  <tbody id="new_parties_body">
                      <tr>
                        <td colspan="3">No Data Available</td>
                      </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>
        </div>
    @endif
  @endif

  @if(config('settings.visit_module')==1 && config('settings.party')==1)
    @if(Auth::user()->can('PartyVisit-view') && Auth::user()->can('party-view'))
        <div class="col-md-6">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Recent Visits&nbsp;&nbsp;<span class='label label-success' id="recent_visit_count"></span></h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <a href="{{ domain_route('company.admin.clientvisit.index') }}" class="btn btn-sm btn-flat pull-right">View All</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive" style="min-height:370px;max-height:370px;">
                <table class="table no-margin">
                  <!-- <thead>
                  <tr>
                    <th>Client Name</th>
                    <th>Visit Time</th>
                    <th>Visit Date</th>
                  </tr>
                  </thead> -->
                  <tbody id="recent_visit_body">
                      <tr>
                        <td colspan="3">No Data Available</td>
                      </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
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

    var markersArray = [];

    function rowclicked(prp){
      var hrefval = prp.id;
      window.open(hrefval, '_blank');
    }


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
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-cart');
        $("#iicons_1").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Non-effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='prodsold'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-basket');
        $("#iicons_1").empty();
      }else if(attrb=='effoutl'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-volume-control-phone');
        $("#iicons_1").empty();
      }else if(attrb=='totvstm'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-clock-o');
        $("#iicons_1").empty();
      }else if(attrb=='totordat'){
        $("#ticker1_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-phone');
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
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-cart');
        $("#iicons_2").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Non-effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='prodsold'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-basket');
        $("#iicons_2").empty();
      }else if(attrb=='effoutl'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-volume-control-phone');
        $("#iicons_2").empty();
      }else if(attrb=='totvstm'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-clock-o');
        $("#iicons_2").empty();
      }else if(attrb=='totordat'){
        $("#ticker2_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-phone');
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
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-cart');
        $("#iicons_3").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Non-effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='prodsold'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-basket');
        $("#iicons_3").empty();
      }else if(attrb=='effoutl'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-volume-control-phone');
        $("#iicons_3").empty();
      }else if(attrb=='totvstm'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-clock-o');
        $("#iicons_3").empty();
      }else if(attrb=='totordat'){
        $("#ticker3_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-phone');
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
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-cart');
        $("#iicons_4").empty().append('<span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip" data-original-title="Non-effective Calls"></span>');
        $('[data-toggle="tooltip"]').tooltip();
      }else if(attrb=='prodsold'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-shopping-basket');
        $("#iicons_4").empty();
      }else if(attrb=='effoutl'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-volume-control-phone');
        $("#iicons_4").empty();
      }else if(attrb=='totvstm'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-clock-o');
        $("#iicons_4").empty();
      }else if(attrb=='totordat'){
        $("#ticker4_icon").find(".fa").removeClass(''+currentclass+'').addClass('fa fa-phone');
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
      countParameters(startDate, endDate, type="");
      getInfoBars(startDate, endDate, type="");
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

      localStorage.setItem('activeTab', '#visit');

      @if(config('settings.ncal')==0)
        start_Date = moment().subtract(30, 'days').format('YYYY-MM-DD');
        end_Date = moment().format('YYYY-MM-DD');
        window.sessionStorage.setItem("startdate",start_Date);
        window.sessionStorage.setItem("enddate",end_Date);
        window.sessionStorage.setItem("caltype",'');
        countParameters(start_Date, end_Date, type="");
        getInfoBars(start_Date, end_Date, type="");
      @else
        startDate = BS2AD(AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD')));
        endDate = BS2AD(AD2BS(moment().format('YYYY-MM-DD')));
        window.sessionStorage.setItem("startdate",startDate);
        window.sessionStorage.setItem("enddate",endDate);
        window.sessionStorage.setItem("caltype",'nepali');
        countParameters(startDate, endDate, type="nepali");
        getInfoBars(startDate, endDate, type="nepali");
      @endif

      
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
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "Yesterday":
              startDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
              endDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "Last 7 Days":
              startDate = moment(moment().subtract(6, 'days')).format('YYYY-MM-DD');
              endDate = moment().format('YYYY-MM-DD');
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "Last 30 Days":
              startDate = moment(moment().subtract(29, 'days')).format('YYYY-MM-DD');
              endDate = moment().format('YYYY-MM-DD');
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "This Month":
              startDate = moment(moment().startOf('month')).format('YYYY-MM-DD');
              endDate = moment().format("YYYY-MM-DD");
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "Last Month":
              startDate = moment(moment().subtract(1, 'month').startOf('month')).format('YYYY-MM-DD');
              endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
              window.sessionStorage.setItem("startdate",startDate);
              window.sessionStorage.setItem("enddate",endDate);
              window.sessionStorage.setItem("caltype",'');
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
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
                countParameters(startDate, endDate, type="");
                getInfoBars(startDate, endDate, type="");
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
          countParameters(startDate, endDate, type="nepali");
          getInfoBars(startDate, endDate, type="nepali");
        });
      @endif

      
      
    });

    function daysDiff(d1, d2) {
      const diffTime = Math.abs(d2 - d1);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      return diffDays;
    }

    function countParameters(startDate, endDate, type){
      $.post("{{ domain_route('company.admin.fetchtickercountdata') }}",{start_date: startDate, end_date: endDate, type},function(res){
        if(res!='' || res!='undefined' && (typeof(res.data)=='object')){
          // $('#ticker1').empty().html(res.data.ticker1);
          // $('#ticker2').empty().html(res.data.ticker2);
          // $('#ticker3').empty().html(res.data.ticker3);
          // $('#ticker4').empty().html(res.data.ticker4);
          $("#sideticker").empty().html(res.data.sideticker);
          $("#sideticker2").empty().html(res.data.sideticker2);   
          
          @if(Auth::user()->can('order-view'))
            // $("#ticker1").find(".changeTicker1").click();
          @endif
          @if(Auth::user()->can('zeroorder-view'))
            // $("#ticker4").find(".changeTicker4_z").click();
          @endif
        }
      });
    }

    function getInfoBars(startDate, endDate, type){
      var totparties_viewallchk = 0;
      $.post("{{ domain_route('company.admin.fetchinfobarsdatas') }}",{start_date: startDate, end_date: endDate, type},function(res){
        if(res!='' || res!='undefined' && (typeof(res.data)=='object')){
          $('#employee_body').empty().html(res.data.employee);
          $('#parties_body').empty().html(res.data.parties);
          $('#products_body').empty().html(res.data.products_all);
          $('#latest_orders').empty().html(res.data.lateset_orders);
          $("#latest_coll_body").empty().html(res.data.latest_collection);
          $("#new_parties_body").empty().html(res.data.new_addedparties);
          $("#recent_visit_body").empty().html(res.data.recent_visits);
          $("#employee_count").empty().html(res.data.employee_count);
          $("#parties_count").empty().html(res.data.parties_count);
          $("#products_count").empty().html(res.data.products_count);
          totparties_viewallchk = res.data.parties_viewall;
          if(totparties_viewallchk==1){
            $("#partieschk_viewall").show();
          }else{
            $("#partieschk_viewall").hide();
          }
        }
      })

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
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
        },
        complete:function(){
          $('#ticker1,#ticker2,#ticker3,#ticker4,#sideticker,#sideticker2').bind('DOMNodeInserted DOMSubtreeModified DOMNodeRemoved', function(event) {
            $('#mainBox').removeClass('box-loader');
            $('#loader1').attr('hidden', 'hidden');
          }); 
        }
      })
    }



 

</script>

@endsection