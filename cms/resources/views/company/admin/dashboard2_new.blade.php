
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
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
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

</style>

<!-- <link rel="stylesheet" href="{{ asset('assets/dist/css/drag-muuri.css') }}"> -->
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

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
            <div class="col-xs-6">
              <div id="reportrange" name="reportrange">
                Select DateRange:&nbsp;&nbsp;&nbsp;<i class="fa fa-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
              </div>
            </div>
          @else
            <div class="col-xs-6">
              Select DateRange:&nbsp;&nbsp;&nbsp;
              <div class="input-group" id="nepCalDiv">
                <input id="start-ndate" class="form-control nepali-date" type="text" name="start_ndate" placeholder="Start Date" autocomplete="off" />
                <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                <input id="end-ndate" class="form-control nepali-date" type="text" name="end_ndate" placeholder="End Date" autocomplete="off" />
              </div>
            </div>
            <div class="col-xs-6">
              <button type="button" class="btn btn-sm btn-success" id="submit-nepali-date">Submit</button>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12 col-sm-6 col-md-3" id="ticker1">
      <span class="pull-right">
        <select id="opt1" style="width:20px;background-color:white;border:white;">
          <option value="0" oth="ses">TOTAL ORDERS</option>
          <option value="0">NEW PARTIES <br>ADDED</option>
          <option value="0">CHEQUES TO BE <br> DEPOSITED</option>
        </select>
      </span>
      <div class="info-box">
        <span class="info-box-icon bg-aqua elevation-1"><i class="fas fa-cog"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col1name">Total Orders</span>
          <span class="info-box-number" id="col1val">0</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3" id="ticker2">
      <span class="pull-right">
        <select id="opt2" style="width:20px;background-color:white;border:white;">
          <option value="0">NO. OF ZERO<br>ORDERS</option>
          <option value="0">PRODUCT SOLD</option>
          <option value="0">TOTAL TIME<br>SPENT ON VISIT</option>
        </select>
      </span>
      <div class="info-box mb-3">
        <span class="info-box-icon bg-green elevation-1"><i class="fas fa-thumbs-up"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col2name">No. of Zero<br>Orders</span>
          <span class="info-box-number" id="col2val">0</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3" id="ticker3">
      <span class="pull-right">
        <select id="opt3" style="width:20px;background-color:white;border:white;">
          <option value="0">Total Complete<br> Orders</option>
          <option value="0">TOTAL RETURNS</option>
          <option value="0">TOTAL VISITS</option>
        </select>
      </span>
      <div class="info-box mb-3">
        <span class="info-box-icon bg-maroon elevation-1"><i class="fa fa-shopping-cart"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col4name">Total<br>Collection</span>
          <span class="info-box-number" id="col4val">0</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3" id="ticker4">
      <span class="pull-right">
        <select id="opt4" style="width:20px;background-color:white;border:white;">
          <option value="0">Orders</option>
          <option value="0">TOTAL<br>COLLECTION</option>
          <option value="0">TOTAL VISITS</option>
        </select>
      </span>
      <div class="info-box mb-3">
        <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-users"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col4name">Orders</span>
          <span class="info-box-number" id="col4val">0</span>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon bg-navy elevation-1"><i class="fas fa-cog"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">New Parties<br>Added</span>
          <span class="info-box-number">
          'NA'
          </span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-orange elevation-1"><i class="fas fa-thumbs-up"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Product Sold</span>
          <span class="info-box-number"></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-maroon elevation-1"><i class="fas fa-shopping-cart"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Returns</span>
          <span class="info-box-number"></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-light-blue elevation-1"><i class="fas fa-users"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Visits</span>
          <span class="info-box-number"></span>
        </div>
      </div>
    </div>
  </div> -->


  <!-- <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon" style="background-color:#184d47;color:white;"><i class="fas fa-cog"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Cheques to be<br>Deposited</span>
          <span class="info-box-number"></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon elevation-1" style="background-color:#29bb89;color:white;"><i class="fas fa-thumbs-up"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total time <br>spent on visit</span>
          <span class="info-box-number"></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon elevation-1" style="background-color:#9ede73;color:white;"><i class="fas fa-shopping-cart"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Visits</span>
          <span class="info-box-number"></span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon elevation-1" style="background-color:#cdc733;color:white;"><i class="fas fa-users"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Visits</span>
          <span class="info-box-number"></span>
        </div>
      </div>
    </div>
  </div> -->

  <div class="row">
    <div class="col-md-9">
      <!-- MAP & BOX PANE -->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Visitors Summary</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding" style="min-height:470px;">
          <div class="row">
            <div class="col-md-9 col-sm-8">
              <div id="visitormap" style="height:470px;width: 105%;border:1px solid #98ddca;"></div>
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-4">
              <div class="pad box-pane-right bg-green" style="min-height: 470px">
                <div class="description-block margin-bottom" style="margin-top:55px;">
                  <div class="sparkbar pad" data-color="#fff"><canvas width="34" height="10" style="display: inline-block; width: 34px; height: 70px; vertical-align: top;"></canvas></div>
                  <h5 class="description-header" id="today_visits">0</h5>
                  <span class="description-text">Total Visits (Today)</span>
                </div>
                <!-- /.description-block -->
                <div class="description-block margin-bottom">
                  <h5 class="description-header" id="yesterday_visits">0</h5>
                  <span class="description-text">Total Visits (Yesterday)</span>
                </div>
                <!-- /.description-block -->
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
      </div>
    </div>

    <div class="col-md-3">
      <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Activites</h3>
        </div>
        <div class="box-body no-padding">
            <ul class="nav nav-pills nav-stacked" id="sideticker2">
                <li style="font-weight:bold;"><a href="#"><i class="fa fa-text"></i>Scheduled
                  <span class="label label-primary pull-right">0</span></a></li>
                <li style="font-weight:bold;"><a href="#"><i class="fa fa-envelope"></i>OverDue
                  <span class="label label-primary pull-right">0</span></a></li>
                <li style="font-weight:bold;"><a href="#"><i class="fa fa-filter"></i>Completed
                  <span class="label label-primary pull-right">0</span></a></li>
                <li style="font-weight:bold;"><a href="#"><i class="fa fa-inbox"></i>Today
                  <span class="label label-primary pull-right">0</span></a></li>
            </ul>
          </div>
      </div>

      <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Information</h3>
        </div>
        <div class="box-body no-padding">
            <ul class="nav nav-pills nav-stacked" id="sideticker">
              <li style="font-weight:bold;"><a href="#"><i class="fa fa-file-text"></i> Present Today
                  <span class="label label-primary pull-right">0</span></a></li>
              <li style="font-weight:bold;"><a href="#"><i class="fa fa-inbox"></i>Productivity %
                  <span class="label label-primary pull-right">0</span></a></li>
            </ul>
          </div>
      </div>
    </div>

    <!-- <div class="col-md-4">
      <div class="info-box bg-yellow">
        <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>

        <div class="info-box-content">
          <span class="info-box-text"><a href="#" target="_blank" style="color:white;pointer:cursor;">Leave</a></span>
          <span class="info-box-number">{{$data['leaves']}}</span>

          <div class="progress">
            <div class="progress-bar" style="width: 50%"></div>
          </div>
          <span class="progress-description">
                50% Increase in 30 Days
              </span>
        </div>
      </div>
      <div class="info-box bg-green">
        <span class="info-box-icon"><i class="ion ion-ios-heart-outline"></i></span>

        <div class="info-box-content">
          <span class="info-box-text"><a href="#" target="_blank" style="color:white;pointer:cursor;">Expenses</a></span>
          <span class="info-box-number">{{$data['expenses']}}</span>

          <div class="progress">
            <div class="progress-bar" style="width: 20%"></div>
          </div>
          <span class="progress-description">
                20% Increase in 30 Days
              </span>
        </div>
      </div>
      <div class="info-box bg-red">
        <span class="info-box-icon"><i class="ion ion-ios-cloud-download-outline"></i></span>

        <div class="info-box-content">
          <span class="info-box-text"><a href="#" target="_blank" style="color:white;pointer:cursor;">Tours</a></span>
          <span class="info-box-number">{{$data['tours']}}</span>

          <div class="progress">
            <div class="progress-bar" style="width: 70%"></div>
          </div>
          <span class="progress-description">
                25% Increase in 30 Days
              </span>
        </div>
      </div>
      <div class="info-box bg-aqua">
        <span class="info-box-icon"><i class="ion-ios-chatbubble-outline"></i></span>

        <div class="info-box-content">
          <span class="info-box-text"><a href="#" target="_blank" style="color:white;pointer:cursor;">Company Targets</a></span>
          <span class="info-box-number">{{$data['companytargets']}}</span>

          <div class="progress">
            <div class="progress-bar" style="width: 40%"></div>
          </div>
          <span class="progress-description">
                40% Increase in 30 Days
              </span>
        </div>
      </div>
    </div> -->

  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Employees&nbsp;&nbsp;<span class='label label-success' id="employee_count"></span></h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All</a>
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

    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Parties&nbsp;&nbsp;<span class='label label-success' id="parties_count"></span></h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All</a>
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

    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Products</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All</a>
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
                <tr>
                  <td>Brands</td>
                  <td><span class="label label-success">0</span></td>
                </tr>
                <tr>
                  <td>Categories</td>
                  <td><span class="label label-success">0</span></td>
                </tr>
                <tr>
                  <td>Products</td>
                  <td><span class="label label-success">0</span></td>
                </tr>
                <tr>
                  <td>Units</td>
                  <td><span class="label label-success">0</span></td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.table-responsive -->
        </div>
        <!-- /.box-body -->
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Latest Orders&nbsp;&nbsp;<span class='label label-success' id="latest_orders_count"></span></h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All Orders</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive" style="min-height:185px;max-height:370px;">
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

    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Latest Collection&nbsp;&nbsp;<span class='label label-success' id="lastest_coll_count"></span></h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive" style="min-height:185px;max-height:370px;">
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
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">New Parties&nbsp;&nbsp;<span class='label label-success' id="new_parties_count"></span></h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive" style="min-height:185px;max-height:370px;">
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


    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Recent Visits&nbsp;&nbsp;<span class='label label-success' id="recent_visit_count"></span></h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive" style="min-height:185px;max-height:370px;">
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
  </div>

  <!-- <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top 10 Sold Products</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>S.NO</th>
                <th>Item Name</th>
                <th>Sold Quantity</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td><a href="pages/examples/invoice.html">1</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">235</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">2</a></td>
                <td>Samsung Smart TV</td>
                <td><span class="label label-warning">178</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">3</a></td>
                <td>iPhone 6 Plus</td>
                <td><span class="label label-danger">163</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">4</a></td>
                <td>Samsung Smart TV</td>
                <td><span class="label label-info">154</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">5</a></td>
                <td>Samsung Smart TV</td>
                <td><span class="label label-warning">124</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">6</a></td>
                <td>iPhone 6 Plus</td>
                <td><span class="label label-danger">105</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">7</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">97</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">8</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">79</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">9</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">72</span></td>
              </tr>
              <tr>
                <td><a href="pages/examples/invoice.html">10</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">45</span></td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top 10 Returned Products</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>S.NO</th>
                <th>Order code</th>
                <th>Item Name</th>
                <th>Ordered Quantity</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>1</td>
                <td><a href="pages/examples/invoice.html">OR1848</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">235</span></td>
              </tr>
              <tr>
                <td>2</td>
                <td><a href="pages/examples/invoice.html">OR1854</a></td>
                <td>Samsung Smart TV</td>
                <td><span class="label label-warning">178</span></td>
              </tr>
              <tr>
                <td>3</td>
                <td><a href="pages/examples/invoice.html">OR1978</a></td>
                <td>iPhone 6 Plus</td>
                <td><span class="label label-danger">163</span></td>
              </tr>
              <tr>
                <td>4</td>
                <td><a href="pages/examples/invoice.html">OR1920</a></td>
                <td>Samsung Smart TV</td>
                <td><span class="label label-info">154</span></td>
              </tr>
              <tr>
                <td>5</td>
                <td><a href="pages/examples/invoice.html">OR1201</a></td>
                <td>Samsung Smart TV</td>
                <td><span class="label label-warning">124</span></td>
              </tr>
              <tr>
                <td>6</td>
                <td><a href="pages/examples/invoice.html">OR2741</a></td>
                <td>iPhone 6 Plus</td>
                <td><span class="label label-danger">105</span></td>
              </tr>
              <tr>
                <td>7</td>
                <td><a href="pages/examples/invoice.html">OR6472</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">97</span></td>
              </tr>
              <tr>
                <td>8</td>
                <td><a href="pages/examples/invoice.html">OR1029</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">79</span></td>
              </tr>
              <tr>
                <td>9</td>
                <td><a href="pages/examples/invoice.html">OR4283</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">72</span></td>
              </tr>
              <tr>
                <td>10</td>
                <td><a href="pages/examples/invoice.html">OR1821</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">45</span></td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div> -->

  <!-- <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Employee Visits for March</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <canvas id="emp_visitsbargraph" style="height:300px !important;"></canvas>
        </div>
      </div>
    </div>
  </div> -->

  <!-- <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Orders(This Month)</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <canvas id="barChart" style="height: 300px;"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Expenses</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <canvas id="pieChart" style="height: 300px;"></canvas>
        </div>
      </div>
    </div>
  </div> -->

  <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Products(Order)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="top_products" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Products(Amount)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="top_products_amount" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Brands(Order)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="top_brands" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Brands(Amount)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="top_brands_amount" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Collection</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="collection_qty" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Collection(Amount)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="collection_amount" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div> -->

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Collection 2</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="collection_all" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Performing Beats(Order)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="top_perform_beats" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Performing Beats(Amount)</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="top_perform_beats_amount" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Total Order Vs No-Order</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div id="total_noorder" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div> -->

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Line Graphs</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div id="prod_totalcalls" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Total Time Spent on Visits</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="time_visit" style="height: 300px;max-width:1075px !important;" ></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">New Parties Added</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div id="new_parties" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Parties</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="top_parties" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Outlets</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="outlets_graph" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Total Order Vs Zero Order</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="orders_pie" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Expenses</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div id="expenses" style="height: 300px;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Targets</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <div class="row" style="margin-bottom:40px;">
            <div class="col-md-4">
              <div id="noof_order" style="height: 350px;"></div>
            </div>
            <div class="col-md-4">
              <div id="noof_coll" style="height: 350px;"></div>
            </div>
            <div class="col-md-4">
              <div id="noof_visit" style="height:350px;"></div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div id="golden_calls" style="height:400px;"></div>
            </div>
            <div class="col-md-3">
              <div id="prod_calls" style="height:400px;"></div>
            </div>
            <div class="col-md-3">
              <div id="value_order" style="height:400px;"></div>
            </div>
            <div class="col-md-3">
              <div id="value_coll" style="height:400px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  

  
</section>
<!-- /.content -->
<!-- <div class="dsa-loader">Loading..</div> -->
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

<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>


<script>


    $('.multClass').multiselect({
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      enableFullValueFiltering: false,
      enableClickableOptGroups: false,
      includeSelectAllOption: false,
      enableCollapsibleOptGroups : true,
      selectAllNumber: false,
    });

    function changeTicker1(){
        var selectedtext = $("#opt1 :selected").text();
        var selectedtext = $("#opt1 :selected").text();
        $("#col1name").text($("#opt1 :selected").text()); 
        $("#col1val").text($("#opt1").val()); 
    }

    function changeTicker2(){
      console.log('tset');
      var selectedtext = $("#opt2 :selected").text();
      var selectedtext = $("#opt2 :selected").text();
      $("#col2name").text($("#opt2 :selected").text()); 
      $("#col2val").text($("#opt2").val()); 
    }

    function changeTicker3(){
      var selectedtext = $("#opt3 :selected").text();
      var selectedtext = $("#opt3 :selected").text();
      $("#col3name").text($("#opt3 :selected").text()); 
      $("#col3val").text($("#opt3").val()); 
    }

    function changeTicker4(){
      var selectedtext = $("#opt4 :selected").text();
      var selectedtext = $("#opt4 :selected").text();
      $("#col4name").text($("#opt4 :selected").text()); 
      $("#col4val").text($("#opt4").val()); 
    }


    $(document).on('click',".applyBtn",function () {
      date = $(".drp-selected").html();
      dateArray = date.split(" - ");
      $('#reportrange span').html(moment(dateArray[0]).format('MMM D, YYYY') + ' - ' + moment(dateArray[1]).format('MMM D, YYYY'));
      startDate = moment(dateArray[0]).format("YYYY-MM-DD");
      endDate = moment(dateArray[1]).format("YYYY-MM-DD");
      countParameters(startDate, endDate, type="");
      getInfoBars(startDate, endDate, type="");
      getGraphs(startDate, endDate, type="");
      addClientVisitMarker();
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
        start_Date = moment(moment().subtract(1, 'month').startOf('month')).format('YYYY-MM-DD');
        end_Date = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
        countParameters(start_Date, end_Date, type="");
        getInfoBars(start_Date, end_Date, type="");
        getGraphs(start_Date,end_Date,type="");
        addClientVisitMarker();
      @else

      @endif

    });    

    

    jQuery(function($) {
      
      @if(config('settings.ncal')==0)
        let start = moment(),
            end = moment();

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
              'Today': [moment(), null],
              'Yesterday': [moment().subtract(1, 'days'), null],
              'Last 7 Days': [moment().subtract(6, 'days'), moment()],
              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
              'This Month': [moment().startOf('month'), moment().endOf('month')],
              'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, dateFilter);
        var check = true;

        dateFilter(start, end,check);

        $(".ranges ul li").on('click', function() {
          let dates;
          let dataRange = $(this).attr('data-range-key');

          switch (dataRange) {

            case "Today":
              startDate = moment().format("YYYY-MM-DD");
              endDate = moment().format("YYYY-MM-DD")
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              addClientVisitMarker();
              break;

            case "Yesterday":
              startDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
              endDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              addClientVisitMarker();
              break;

            case "Last 7 Days":
              startDate = moment(moment().subtract(6, 'days')).format('YYYY-MM-DD');
              endDate = moment().format('YYYY-MM-DD');
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              addClientVisitMarker();
              break;

            case "Last 30 Days":
              startDate = moment(moment().subtract(29, 'days')).format('YYYY-MM-DD');
              endDate = moment().format('YYYY-MM-DD');
              countParameters(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "This Month":
              startDate = moment(moment().startOf('month')).format('YYYY-MM-DD');
              endDate = moment().format("YYYY-MM-DD");
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              addClientVisitMarker();
              break;

            case "Last Month":
              startDate = moment(moment().subtract(1, 'month').startOf('month')).format('YYYY-MM-DD');
              endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
              countParameters(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              addClientVisitMarker();
              break;

            case "Custom Range":
              $(".applyBtn").click(function () {
                date = $(".drp-selected").html();
                dateArray = date.split(" - ");
                startDate = moment(dateArray[0]).format("YYYY-MM-DD");
                endDate = moment(dateArray[1]).format("YYYY-MM-DD");
                countParameters(startDate, endDate, type="");
                getInfoBars(startDate, endDate, type="");
                getGraphs(startDate, endDate, type="");
                addClientVisitMarker();
              });
              break;
          
            default:
              break;
          }
        });

      @else
        //nepali calendaar

      @endif


    });

    function daysDiff(d1, d2) {
      const diffTime = Math.abs(d2 - d1);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      return diffDays;
    }

    function countParameters(startDate, endDate, type){
      $.post("{{ route('fetchtickercountdata') }}",{start_date: startDate, end_date: endDate, type},function(res){
        if(res!='' || res!='undefined' && (typeof(res.data)=='object')){
          $('#ticker1').empty().html(res.data.ticker1);
          $('#ticker2').empty().html(res.data.ticker2);
          $('#ticker3').empty().html(res.data.ticker3);
          $('#ticker4').empty().html(res.data.ticker4);
          $("#sideticker").empty().html(res.data.sideticker);
          $("#sideticker2").empty().html(res.data.sideticker2);
        }
      })
    }

    function getInfoBars(startDate, endDate, type){
      $.post("{{ route('fetchinfobarsdatas') }}",{start_date: startDate, end_date: endDate, type},function(res){
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
          $("#latest_orders_count").empty().html(res.data.lateset_orders_count);
          $("#lastest_coll_count").empty().html(res.data.latest_collection_count);
          $("#new_parties_count").empty().html(res.data.new_addedparties_count);
          $("#recent_visit_count").empty().html(res.data.recent_visits_count);
        }
      })
    }

    function addClientVisitMarker(){
      var today_locations = yesterday_locations = [];
      $.ajax({
        method: 'get',
        dataType: 'json',
        url: "{{ route('fetchtvisitlocations') }}",
        success:function(res){
          td_location = res.data.today_location;
          yt_location = res.data.yesterday_location;
          $("#today_visits").html('').html(res.data.todayvisit_count);
          $("#yesterday_visits").html('').html(res.data.yesterdayvisit_count);
          if(td_location.length>0){
            tdlocation_qty = td_location.length
            for(var t=0;t<tdlocation_qty;t++){
              marker = new google.maps.Marker({
                  position: new google.maps.LatLng((td_location[t]).latitude, (td_location[t]).longitude),
                  map: map2,
                  icon: {
                      url: "/assets/dist/img/markers/Official_7.png",
                      scaledSize: new google.maps.Size(25, 40),
                  },
                  title:  (td_location[t]).name,
              });
            }
          }

          if(yt_location.length>0){
            tdlocation_qty = yt_location.length
            for(var t=0;t<tdlocation_qty;t++){
              marker = new google.maps.Marker({
                  position: new google.maps.LatLng((yt_location[t]).latitude, (yt_location[t]).longitude),
                  map: map2,
                  icon: {
                      url: "/assets/dist/img/markers/Official_7.png",
                      scaledSize: new google.maps.Size(25, 40),
                  },
                  title:  (yt_location[t]).name,
              });
            }
          }

        },
        beforeSend:function(){
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
        },
        complete:function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        }
      })
    }

    function getGraphs(startDate, endDate, type) {
      let productsDates = productsValues = [];
      let productsAmtDates = productsAmtValues = [];

      let brandDates = brandValues = [];
      let brandAmtDates = brandAmtValues = [];

      let collectionQtyDates = collectionQtyValues = [];
      let collectionAmtDates = collectionAmtValues = [];


      let topPerformDates = topPerformValues = [];
      let topPerformAmtDates = topPerformAmtValues = [];

      let timeSpentDates = timeSpentValues = [];
      let newPartiesDates = newPartiesValues = [];

      let topPartiesDates = topPartiesValues = [];
      let topOutletsDates = topOutletsValues = [];
      let expenseDates = expenseValues = [];

      let totOrderDates = totOrderValues = [];
      let noOrderDates = noOrderValues = [];
      let totOrdNoOrdDates = [];

      let prodCallsDates = prodCallsValues = [];
      let totCallsDates = totCallsValues = [];
      let totProdTotDates = [];

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
      
      $.post("{{ route('fetchtopproductsorder') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          productsDates = response.data.dates;
          productsValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            productsDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              productsDates.push(AD2BS(element));
            }
          }
        }
        productsValues = response.data.values;
        productsOrderGraph(productsDates, productsValues);
      });

      $.post("{{ route('fetchtopproductsamount') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          productsAmtDates = response.data.dates;
          productsAmtValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            productsAmtDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              productsAmtDates.push(AD2BS(element));
            }
          }
        }
        productsAmtValues = response.data.values;
        productsAmountGraph(productsAmtDates, productsAmtValues);
      });

      $.post("{{ route('fetchtopbrandsorder') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          brandDates = response.data.dates;
          brandValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            brandDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              brandDates.push(AD2BS(element));
            }
          }
        }
        brandValues = response.data.values;
        brandsOrderGraph(brandDates, brandValues);
      });

      $.post("{{ route('fetchtopbrandsamount') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          brandAmtDates = response.data.dates;
          brandAmtValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            brandAmtDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              brandAmtDates.push(AD2BS(element));
            }
          }
        }
        brandAmtValues = response.data.values;
        brandsAmountGraph(brandAmtDates, brandAmtValues);
      });

      $.post("{{ route('fetchtopperformbeatsorder') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          topPerformDates = response.data.dates;
          topPerformValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            topPerformDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              topPerformDates.push(AD2BS(element));
            }
          }
        }
        topPerformValues = response.data.values;
        topBeatsOrderGraph(topPerformDates, topPerformValues);
      });

      $.post("{{ route('fetchtopperformbeatsamt') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          topPerformAmtDates = response.data.dates;
          topPerformAmtValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            topPerformAmtDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              topPerformAmtDates.push(AD2BS(element));
            }
          }
        }
        topPerformAmtValues = response.data.values;
        topBeatsAmountGraph(topPerformAmtDates, topPerformAmtValues);
      });

      $.post("{{ route('fetchtimespentvisit') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
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
        timeSpentVistGraph(timeSpentDates, timeSpentValues);
      });

      $.post("{{ route('fetchnewparties') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          newPartiesDates = response.data.dates;
          newPartiesValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            newPartiesDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              newPartiesDates.push(AD2BS(element));
            }
          }
        }
        newPartiesValues = response.data.values;
        newPartiesGraph(newPartiesDates, newPartiesValues);
      });

      $.post("{{ route('fetchtopparties') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          topPartiesDates = response.data.dates;
          topPartiesValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            topPartiesDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              topPartiesDates.push(AD2BS(element));
            }
          }
        }
        topPartiesValues = response.data.values;
        topPartiesGraph(topPartiesDates, topPartiesValues);
      });

      $.post("{{ route('fetchtopexpenses') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          expenseDates = response.data.dates;
          expenseValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            expenseDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              expenseDates.push(AD2BS(element));
            }
          }
        }
        expenseValues = response.data.values;
        counter = 0;
        for(var y=0;y<expenseValues.length;y++){
          if(expenseValues[y]==0){
            counter = counter+1;
          }
        }
        if(counter!=expenseValues.length){
          expenseGraph(expenseDates, expenseValues);
        }
      });

      $.post("{{ route('fetchzero_order') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
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
      });

      $.post("{{ route('fetchtotal_order') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
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
        //totalOrderZeroGraph(totOrderDates, totOrderValues, noOrderValues);
      });

      $.post("{{ route('fetchproductivecalls') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          prodCallsDates = response.data.dates;
          prodCallsValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            prodCallsDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              prodCallsDates.push(AD2BS(element));
            }
          }
        }
        prodCallsValues = response.data.values;
      });

      $.post("{{ route('fetchtotalcalls') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
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
        //totalProdTotCallsGraph(totCallsDates, totCallsValues, prodCallsValues);
        plotLineGraphs(totOrderDates, totOrderValues, noOrderValues, totCallsValues, prodCallsValues);
      });

      $.post("{{ route('fetchtotalzeroorder') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          totOrderDatesPie = response.data.dates;
          totOrderValuesPie = response.data.values;
        } else {
          if (diffInDays > 45) {
            totOrderDatesPie = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              totOrderDatesPie.push(AD2BS(element));
            }
          }
        }
        totOrderValuesPie = response.data.values;
        counter = 0;
        for(var y=0;y<totOrderValuesPie.length;y++){
          if(totOrderValuesPie[y]==0){
            counter = counter+1;
          }
        }
        if(counter!=totOrderValuesPie.length){
          orderPieGraph(totOrderDatesPie, totOrderValuesPie);
        }
      });

      $.post("{{ route('fetchtopoutlets') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          topOutletsDates = response.data.dates;
          topOutletsValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            topOutletsDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              topOutletsDates.push(AD2BS(element));
            }
          }
        }
        topOutletsValues = response.data.values;
        topOutletsGraph(topOutletsDates, topOutletsValues);
      });

      $.post("{{ route('fetchtargetsnoorder') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        noOrderGraph(response.data.targetsnoorder_achieved,response.data.targetsnoorder_toachieve);
        noCollGraph(response.data.targetsnocoll_achieved,response.data.targetsnocoll_toachieve);
        noVisitGraph(response.data.targetsnovisit_achieved,response.data.targetsnovisit_toachieve);
        callsGoldenGraph(response.data.targetgoldencalls_achieved,response.data.targetgoldencalls_toachieve);
        callsTotalGraph(response.data.totalcalls_achieved,response.data.totalcalls_toachieve);
        valueOrderGraph(response.data.targetsvalueorder_achieved,response.data.targetvalueorder_toachieve);
        valueCollGraph(response.data.targetsvaluecoll_achieved,response.data.targetvaluecoll_toachieve);
      });

      $.post("{{ route('fetchcollectionqty') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          collectionQtyDates = response.data.dates;
          collectionQtyValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            collectionQtyDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              collectionQtyDates.push(AD2BS(element));
            }
          } 
        }
        collectionQtyValues = response.data.values;
        // collectionQtyGraph(collectionQtyDates, collectionQtyValues);
      });

      $.post("{{ route('fetchcollectionamt') }}", {start_date: startDate, end_date: endDate, type}, function(response) {
        if (type == undefined || type == "") {
          collectionAmtDates = response.data.dates;
          collectionAmtValues = response.data.values;
        } else {
          if (diffInDays > 45) {
            collectionAmtDates = response.data.dates; 
          } else {
            for (let index = 0; index < response.data.dates.length; index++) {
              const element = response.data.dates[index];
              collectionAmtDates.push(AD2BS(element));
            }
          } 
        }
        collectionAmtValues = response.data.values;
        // collectionamtGraph(collectionAmtDates, collectionAmtValues);
        collectionall(collectionAmtDates,collectionQtyValues,collectionAmtValues);
      });


      addClientVisitMarker();

    }

    function productsOrderGraph(dates, values) {
      var options = {
        series: [{
            name: 'Order',
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
            text: 'Orders',
          },
        },
        yaxis: {
          title: {
            text: 'Product',
          },
        },
      };
      $("#top_products").empty();
      var chart = new ApexCharts(document.querySelector("#top_products"), options);
      chart.render();
    }

    function productsAmountGraph(dates, values) {
      var options = {
        series: [{
            name: 'Amount',
            data: values
        }],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: dates,
          tickPlacement: 'on',
          title: {
            text: 'Amount',
          },
        },
        yaxis: {
          title: {
            text: 'Product',
          },
        },
      };
      $("#top_products_amount").empty();
      var chart = new ApexCharts(document.querySelector("#top_products_amount"), options);
      chart.render();
    }

    function brandsOrderGraph(dates, values) {
      var options = {
        series: [{
            name: 'Amount',
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
            text: 'Order',
          },
        },
        yaxis: {
          title: {
            text: 'Brand',
          },
        },
      };
      $("#top_brands").empty();
      var chart = new ApexCharts(document.querySelector("#top_brands"), options);
      chart.render();
    }

    function brandsAmountGraph(dates, values) {
      var options = {
        series: [{
            name: 'Amount',
            data: values
        }],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: dates,
          tickPlacement: 'on',
          title: {
            text: 'Amount',
          },
        },
        yaxis: {
          title: {
            text: 'Brand',
          },
        },
      };
      $("#top_brands_amount").empty();
      var chart = new ApexCharts(document.querySelector("#top_brands_amount"), options);
      chart.render();
    }

    function topBeatsOrderGraph(dates, values) {
      var options = {
        series: [{
          name: 'Order',
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
          text: 'Orders',
        },
      },
      fill: {
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 0.85,
            opacityTo: 0.85,
            stops: [50, 0, 100]
          },
        }
      };
      $("#top_perform_beats").empty();
      var chart = new ApexCharts(document.querySelector("#top_perform_beats"), options);
      chart.render();
    }

    function topBeatsAmountGraph(dates, values) {
      var options = {
        series: [{
          name: 'Amount',
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
          text: 'Amount',
        },
      },
      fill: {
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 0.85,
            opacityTo: 0.85,
            stops: [50, 0, 100]
          },
        }
      };
      $("#top_perform_beats_amount").empty();
      var chart = new ApexCharts(document.querySelector("#top_perform_beats_amount"), options);
      chart.render();
    }

    function timeSpentVistGraph(dates, values) {
      var options = {
        series: [{
          name: 'Time(in seconds)',
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
          text: 'Visited Date',
        },
        categories: dates,
        tickPlacement: 'on'
      },
      yaxis: {
        title: {
          text: 'Time(in seconds)',
        },
      },
      fill: {
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 0.85,
            opacityTo: 0.85,
            stops: [50, 0, 100]
          },
        }
      };
      $("#time_visit").empty();
      var chart = new ApexCharts(document.querySelector("#time_visit"), options);
      chart.render();
    }

    function newPartiesGraph(dates, values) {
      var options = {
        series: [{
          name: 'Parties added',
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
          text: 'No.of Parties',
        },
      },
      fill: {
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 0.85,
            opacityTo: 0.85,
            stops: [50, 0, 100]
          },
        }
      }
      $("#new_parties").empty();
      var chart = new ApexCharts(document.querySelector("#new_parties"), options);
      chart.render();
    }

    function topPartiesGraph(dates, values) {
      var options = {
          series: [{
            name: 'Total Orders',
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
            text: 'Total No. of Orders',
          },
        },
        yaxis: {
          title: {
            text: 'Parties',
          },
        },

      };
      $("#top_parties").empty();
      var chart = new ApexCharts(document.querySelector("#top_parties"), options);
      chart.render();
    }

    function expenseGraph(dates, values) {
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
            return "Expense On: " + expense_label[opts.seriesIndex]
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
      $("#expenses").empty();
      var chart = new ApexCharts(document.querySelector("#expenses"), options);
      chart.render();
    }

    function plotLineGraphs(dates, totalorder, zeroorder, totalcalls, productivecalls){
      var options = {
          series: [
            {
              name: 'Total Orders',
              data: totalorder
            },
            {
              name: 'Zero Orders',
              data: zeroorder
            },
            {
              name: 'Total Calls',
              data: totalcalls
            },
            {
              name: 'Productive Calls',
              data: productivecalls
            }
          ],
        chart: {
          height: 350,
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
            text: 'Order Date',
          },
          categories: dates,
          tickPlacement: 'on'
        },
        yaxis: {
          title: {
            text: 'Order Quantity',
          },
        },
        legend: {
          position: 'top',
          horizontalAlign: 'right',
          offsetY: 5,
          offsetX: -50
        }
      };
      $("#prod_totalcalls").empty();
      var chart = new ApexCharts(document.querySelector("#prod_totalcalls"), options);
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
          enabled: true
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
      $("#orders_pie").empty();
      var chart = new ApexCharts(document.querySelector("#orders_pie"), options);
      chart.render();
    }

    function topOutletsGraph(dates, values) {
      var options = {
          series: [{
            name: 'Total Orders',
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
            text: 'Total No. of Orders',
          },
        },
        yaxis: {
          title: {
            text: 'Outlets',
          },
        },

      };
      $("#outlets_graph").empty();
      var chart = new ApexCharts(document.querySelector("#outlets_graph"), options);
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
      a = Math.ceil(targets_toachieve/2);
      b = parseInt(a + targets_toachieve/3);
      c = targets_toachieve;
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


    function collectionQtyGraph(dates, values) {
      var options = {
        series: [{
            name: 'Total collections',
            data: values
        }],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: dates,
          tickPlacement: 'on',
          title: {
            text: 'Collection Date',
          },
        },
        yaxis: {
          title: {
            text: 'No. of Collection',
          },
        },
      };
      $("#collection_qty").empty();
      var chart = new ApexCharts(document.querySelector("#collection_qty"), options);
      chart.render();
    }

    function collectionamtGraph(dates, values) {
      var options = {
        series: [{
            name: 'Amount',
            data: values
        }],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: dates,
          tickPlacement: 'on',
          title: {
            text: 'Collection Date',
          },
        },
        yaxis: {
          title: {
            text: 'Amount',
          },
        },
      };
      $("#collection_amount").empty();
      var chart = new ApexCharts(document.querySelector("#collection_amount"), options);
      chart.render();
    }


    function collectionall(dates,collqty, collamt){
      console.log(dates,collqty,collamt);
      Highcharts.chart('collection_all', {
        series: [{
            name: 'Collection',
            type: 'spline',
            yAxis: 1,
            data: collqty,
        }, {
            name: 'Collection(Amount)',
            type: 'spline',
            data: collamt,
        }],
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: 'Average Monthly Weather Data for Tokyo',
            align: 'left'
        },
        xAxis: [{
            categories: dates,
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                style: {
                    color: Highcharts.getOptions().colors[2]
                }
            },
            title: {
                text: 'Collection(Amount)',
                style: {
                    color: Highcharts.getOptions().colors[2]
                }
            },
            opposite: true

        }, { // Secondary yAxis
            gridLineWidth: 0,
            title: {
                text: 'Collection',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            }

        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 80,
            verticalAlign: 'top',
            y: 55,
            floating: true,
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || // theme
                'rgba(255,255,255,0.25)'
        },

    });
    }
    
    // function totalOrderZeroGraph(dates, values1, values2){
    //   var options = {
    //       series: [  
    //         {
    //           name: 'Total Orders',
    //           data: values1
    //         },
    //         {
    //           name: 'Zero Orders',
    //           data: values2
    //         }
    //       ],
    //     chart: {
    //       height: 350,
    //       type: 'line',
    //     },
    //     plotOptions: {
    //       stroke: {
    //         width: 4,
    //         curve: 'smooth'
    //       },
    //     },
    //     dataLabels: {
    //       enabled: false
    //     },
    //     stroke: {
    //       width: 2
    //     },
    //     subtitle: {
    //       align: 'center',
    //       margin: 30,
    //       offsetY: 40,
    //       style: {
    //         color: '#222',
    //         fontSize: '24px',
    //       }
    //     },
    //     grid: {
    //       row: {
    //         colors: ['#fff', '#f2f2f2']
    //       }
    //     },
    //     xaxis: {
    //       labels: {
    //         rotate: -45
    //       },
    //       title: {
    //         text: 'Order Date',
    //       },
    //       categories: dates,
    //       tickPlacement: 'on'
    //     },
    //     yaxis: {
    //       title: {
    //         text: 'Order Quantity',
    //       },
    //     },
    //     legend: {
    //       position: 'top',
    //       horizontalAlign: 'right',
    //       offsetY: 5,
    //       offsetX: -50
    //     }
    //   };
    //   $("#total_noorder").empty();
    //   var chart = new ApexCharts(document.querySelector("#total_noorder"), options);
    //   chart.render();
    // }

    // function totalProdTotCallsGraph(dates, values1, values2){
    //   var options = {
    //       series: [
    //         {
    //           name: 'Total Calls',
    //           data: values1
    //         },
    //         {
    //           name: 'Productive Calls',
    //           data: values2
    //         }
    //       ],
    //     chart: {
    //       height: 350,
    //       type: 'line',
    //     },
    //     plotOptions: {
    //       stroke: {
    //         width: 4,
    //         curve: 'smooth'
    //       },
    //     },
    //     dataLabels: {
    //       enabled: false
    //     },
    //     stroke: {
    //       width: 2
    //     },
    //     subtitle: {
    //       align: 'center',
    //       margin: 30,
    //       offsetY: 40,
    //       style: {
    //         color: '#222',
    //         fontSize: '24px',
    //       }
    //     },
    //     grid: {
    //       row: {
    //         colors: ['#fff', '#f2f2f2']
    //       }
    //     },
    //     xaxis: {
    //       labels: {
    //         rotate: -45
    //       },
    //       title: {
    //         text: 'Order Date',
    //       },
    //       categories: dates,
    //       tickPlacement: 'on'
    //     },
    //     yaxis: {
    //       title: {
    //         text: 'Order Quantity',
    //       },
    //     },
    //     legend: {
    //       position: 'top',
    //       horizontalAlign: 'right',
    //       offsetY: 5,
    //       offsetX: -50
    //     }
    //   };
    //   $("#prod_totalcalls").empty();
    //   var chart = new ApexCharts(document.querySelector("#prod_totalcalls"), options);
    //   chart.render();
    // }





</script>




@endsection