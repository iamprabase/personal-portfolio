
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

<style>
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
<section class="content">
  <!-- Info boxes -->

  <div class="row">
    <div class="col-md-12">
      <div class="box" style="border-color:white;">
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
          <option value="0">TOTAL<br>COLLECTION</option>
          <option value="0">TOTAL VISITS</option>
          <option value="0">TOTAL VISITS</option>
        </select>
      </span>
      <div class="info-box mb-3">
        <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-users"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col4name">Total<br>Collection</span>
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
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body no-padding">
          <div class="row">
            <div class="col-md-9 col-sm-8">
              <div id="visitormap">
              </div>
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-4">
              <div class="pad box-pane-right bg-green" style="min-height: 360px">
                <div class="description-block margin-bottom" style="margin-top:55px;">
                  <div class="sparkbar pad" data-color="#fff"><canvas width="34" height="10" style="display: inline-block; width: 34px; height: 10px; vertical-align: top;"></canvas></div>
                  <h5 class="description-header">370</h5>
                  <span class="description-text">Total Visits (Today)</span>
                </div>
                <!-- /.description-block -->
                <div class="description-block margin-bottom">
                  <h5 class="description-header">230</h5>
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
            <h3 class="box-title">Information</h3>
        </div>
        <div class="box-body no-padding">
            <ul class="nav nav-pills nav-stacked" id="sideticker">
              
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
    <div class="col-md-4">
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

    <div class="col-md-4">
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

    <div class="col-md-4">
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
          <div id="top_products" style="height: 400px;max-width:1075px !important;"></div>
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
          <div id="top_products_amount" style="height: 400px;max-width:1075px !important;"></div>
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
          <div id="top_brands" style="height: 400px;max-width:1075px !important;"></div>
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
          <div id="top_brands_amount" style="height: 400px;max-width:1075px !important;"></div>
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
          <div id="top_perform_beats" style="height: 375px;max-width:1075px !important;"></div>
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
          <div id="top_perform_beats_amount" style="height: 375px;max-width:1075px !important;"></div>
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
          <div id="prod_totalcalls" style="height: 375px;max-width:1075px !important;"></div>
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
          <div id="time_visit" style="height: 375px;max-width:1075px !important;" ></div>
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
          <div id="new_parties" style="height: 375px;max-width:1075px !important;"></div>
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
          <div id="top_parties" style="height: 400px;max-width:1075px !important;"></div>
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
          <div id="outlets_graph" style="height: 400px;max-width:1075px !important;"></div>
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
          <div id="orders_pie" style="height: 400px;max-width:1075px !important;"></div>
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
          <div id="expenses" style="height: 400px;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      
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
<!-- <script src="{{ asset('assets/bower_components/apexchart/apexcharts.js') }}"></script> -->
<script src="{{ asset('assets/bower_components/highcharts/highchart.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/exporting.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/export-data.js') }}"></script>
<script src="{{ asset('assets/bower_components/highcharts/accessibility.js') }}"></script>


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
      getGraphs(startDate, endDate, type="");
      countParameters(startDate, endDate, type="");
      getInfoBars(startDate, endDate, type="");

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
        getGraphs(start_Date,end_Date,type="");
        countParameters(start_Date, end_Date, type="");
        getInfoBars(start_Date, end_Date, type="");
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
              getGraphs(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "Yesterday":
              startDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
              endDate = moment(moment().subtract(1, 'days')).format("YYYY-MM-DD");
              countParameters(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "Last 7 Days":
              startDate = moment(moment().subtract(6, 'days')).format('YYYY-MM-DD');
              endDate = moment().format('YYYY-MM-DD');
              countParameters(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
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
              getGraphs(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "Last Month":
              startDate = moment(moment().subtract(1, 'month').startOf('month')).format('YYYY-MM-DD');
              endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
              countParameters(startDate, endDate, type="");
              getGraphs(startDate, endDate, type="");
              getInfoBars(startDate, endDate, type="");
              break;

            case "Custom Range":
              $(".applyBtn").click(function () {
                date = $(".drp-selected").html();
                dateArray = date.split(" - ");
                startDate = moment(dateArray[0]).format("YYYY-MM-DD");
                endDate = moment(dateArray[1]).format("YYYY-MM-DD");
                countParameters(startDate, endDate, type="");
                getGraphs(startDate, endDate, type="");
                getInfoBars(startDate, endDate, type="");
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
          $("#recent_visit_body").empty().html(res.data.recent_visits);
          $("#employee_count").empty().html(res.data.employee_count);
          $("#parties_count").empty().html(res.data.parties_count);
          $("#latest_orders_count").empty().html(res.data.lateset_orders_count);
          $("#lastest_coll_count").empty().html(res.data.latest_collection_count);
          $("#recent_visit_count").empty().html(res.data.recent_visits_count);
        }
      })
    }

    function getGraphs(startDate, endDate, type) {
      startDate = '2020-04-10'; endDate = '2021-02-10';
      let productsDates = productsValues = [];
      let productsAmtDates = productsAmtValues = [];

      let brandDates = brandValues = [];
      let brandAmtDates = brandAmtValues = [];

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


    }


    function productsOrderGraph(dates, values) {
      Highcharts.chart('top_products', {
        series: [{
            showInLegend: false,
            name: 'Order',
            data: values
        }],
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Top Products(Order)'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Products'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Orders',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function productsAmountGraph(dates, values) {
      Highcharts.chart('top_products_amount', {
        series: [{
            showInLegend: false,
            name: 'Order',
            data: values
        }],
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Top Products(Amount)'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Products'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Amount',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function brandsOrderGraph(dates, values) {
      Highcharts.chart('top_brands', {
        series: [{
            showInLegend: false,
            name: 'Order',
            data: values
        }],
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Top Products(Order)'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Brands'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Orders',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function brandsAmountGraph(dates, values) {
      Highcharts.chart('top_brands_amount', {
        series: [{
            showInLegend: false,
            name: 'Amount',
            data: values
        }],
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Top Products(Order)'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Brands'
            }
        },
        yAxis: {
            min: 0,
            format: '{values:.2f}',
            title: {
                text: 'Amount',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function topBeatsOrderGraph(dates, values) {
      Highcharts.chart('top_perform_beats', {
        series: [{
            showInLegend: false,
            name: 'Order',
            data: values
        }],
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top Products(Order)'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Products'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Orders',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function topBeatsAmountGraph(dates, values) {
      Highcharts.chart('top_perform_beats_amount', {
        series: [{
            showInLegend: false,
            name: 'Order',
            data: values
        }],
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top Products(Order)'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Products'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Orders',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function timeSpentVistGraph(dates, values) {
      Highcharts.chart('time_visit', {
        series: [{
            showInLegend: false,
            name: 'Time(in Seconds)',
            data: values
        }],
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top Products(Order)'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Visited Date'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Time(in Seconds)',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function newPartiesGraph(dates, values) {
      Highcharts.chart('new_parties', {
        series: [{
            showInLegend: false,
            name: 'Time(in Seconds)',
            data: values
        }],
        chart: {
            type: 'column'
        },
        title: {
            text: 'Total Time Spent on Visits'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Visited Date'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Time(in Seconds)',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function topPartiesGraph(dates, values) {
      Highcharts.chart('top_parties', {
        series: [{
            showInLegend: false,
            name: 'Order',
            data: values
        }],
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Top Parties'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Products'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Orders',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }

    function expenseGraph(dates, values) {
      Highcharts.chart('expenses', {
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        title: {
            text: 'Contents of Highsoft\'s weekly fruit delivery'
        },
        subtitle: {
            text: '3D donut in Highcharts'
        },
        plotOptions: {
            pie: {
                innerSize: 100,
                depth: 45
            }
        },
        series: [{
            name: 'Delivered amount',
            data: [
                ['Bananas', 8],
                ['Kiwi', 3],
                ['Mixed nuts', 1],
                ['Oranges', 6],
                ['Apples', 8],
                ['Pears', 4],
                ['Clementines', 4],
                ['Reddish (bag)', 1],
                ['Grapes (bunch)', 1]
            ]
        }]
      });
    }

    function plotLineGraphs(dates, totalorder, zeroorder, totalcalls, productivecalls){
      Highcharts.chart('prod_totalcalls', {
        series: [{
            name: 'Total Order',
            data: totalorder
          },{
            name: 'Zero Order',
            data: zeroorder
        },{
            name: 'Total Calls',
            data: totalcalls
        },{
            name: 'Productive Calls',
            data: productivecalls
        }],
        chart: {
            type: 'spline'
        },
        title: {
            text: 'Order & Calls'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'No. Of Orders'
            }
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },
        plotOptions: {
            spline: {
                marker: {
                    radius: 4,
                    lineColor: '#666666',
                    lineWidth: 1
                }
            }
        },
        credits: {
          enabled: false
        },
      });
    }

    function orderPieGraph(dates, values) {
      // Radialize the colors
      Highcharts.setOptions({
          colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
              return {
                  radialGradient: {
                      cx: 0.5,
                      cy: 0.3,
                      r: 0.7
                  },
                  stops: [
                      [0, color],
                      [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
                  ]
              };
          })
      });

      // Build the chart
      Highcharts.chart('orders_pie', {
          chart: {
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              type: 'pie'
          },
          title: {
              text: 'Browser market shares in January, 2018'
          },
          tooltip: {
              pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
          },
          accessibility: {
              point: {
                  valueSuffix: '%'
              }
          },
          plotOptions: {
              pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  dataLabels: {
                      enabled: true,
                      format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                      connectorColor: 'silver'
                  }
              }
          },
          series: [{
              name: 'Share',
              data: [
                
                  { name: 'Chrome', y: 61.41 },
                  { name: 'Internet Explorer', y: 11.84 },
                  { name: 'Firefox', y: 10.85 },
                  { name: 'Edge', y: 4.67 },
                  { name: 'Safari', y: 4.18 },
                  { name: 'Other', y: 7.05 }
              ]
          }]
      });
    }

    function topOutletsGraph(dates, values) {
      Highcharts.chart('outlets_graph', {
        series: [{
            showInLegend: false,
            name: 'Order',
            data: values
        }],
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Outlets'
        },
        xAxis: {
            categories: dates,
            title: {
                text: 'Outlets'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Orders',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
      });
    }







</script>




@endsection