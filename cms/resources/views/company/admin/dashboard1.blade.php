
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
    <div class="col-md-offset-9 col-md-3">
      <div class="box box-default" style="border-top:none;">
        <div class="form-group">
          <div class="input-group">
            <button type="button" class="btn btn-default pull-right" id="daterange-btn">
              <span>
                <i class="fa fa-calendar"></i> Date range picker
              </span>
              <i class="fa fa-caret-down"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <span class="pull-right">
        <select id="opt1" style="width:20px;background-color:white;border:white;">
          <option value="{{ $data['total_orders'] }}" oth="ses">TOTAL ORDERS</option>
          <option value="NA">NEW PARTIES <br>ADDED</option>
          <option value="{{$data['chq_2deposit']}}">CHEQUES TO BE <br> DEPOSITED</option>
        </select>
      </span>
      <div class="info-box">
        <span class="info-box-icon bg-aqua elevation-1"><i class="fas fa-cog"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col1name">Total Orders</span>
          <span class="info-box-number" id="col1val">{{ $data['total_orders'] }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <span class="pull-right">
        <select id="opt2" style="width:20px;background-color:white;border:white;">
          <option value="{{ $data['no_zero_orders'] }}">NO. OF ZERO<br>ORDERS</option>
          <option value="{{$data['products_sold']}}">PRODUCT SOLD</option>
          <option value="{{$data['tot_visittime']}}">TOTAL TIME<br>SPENT ON VISIT</option>
        </select>
      </span>
      <div class="info-box mb-3">
        <span class="info-box-icon bg-green elevation-1"><i class="fas fa-thumbs-up"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col2name">No. of Zero<br>Orders</span>
          <span class="info-box-number" id="col2val">{{ $data['no_zero_orders'] }}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <span class="pull-right">
        <select id="opt3" style="width:20px;background-color:white;border:white;">
          <option value="{{$data['total_comporders']}}">Total Complete<br> Orders</option>
          <option value="{{$data['total_returns']}}">TOTAL RETURNS</option>
          <option value="{{$data['total_visits']}}">TOTAL VISITS</option>
        </select>
      </span>
      <div class="info-box mb-3">
        <span class="info-box-icon bg-red elevation-1"><i class="fas fa-shopping-cart"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col3name">Total Complete<br> Orders</span>
          <span class="info-box-number" id="col3val">{{$data['total_comporders']}}</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
      <span class="pull-right">
        <select id="opt4" style="width:20px;background-color:white;border:white;">
          <option value="{{$data['total_collection']}}">TOTAL<br>COLLECTION</option>
          <option value="">TOTAL VISITS</option>
          <option value="">TOTAL VISITS</option>
        </select>
      </span>
      <div class="info-box mb-3">
        <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-users"></i></span>
        <div class="info-box-content">
          <span class="info-box-text" id="col4name">Total<br>Collection</span>
          <span class="info-box-number" id="col4val">{{$data['total_collection']}}</span>
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
    <div class="col-md-8">
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

    <div class="col-md-4">
      <!-- Info Boxes Style 2 -->
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
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
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
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
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
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
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
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Employees</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All Employees</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>S.NO</th>
                <th>Employee Type</th>
                <th>Quantity</th>
              </tr>
              </thead>
              <tbody>
                @php $totcount = count($data['employee']); $i=1; @endphp
                @if($totcount>0)
                  @foreach($data['employee'] as $ky=>$vl)
                    <tr>
                      <td>{{$i++}}</td>
                      <td>{{$vl['name']}}</td>
                      <td><span class="label label-success">@php echo count($vl['employees']) @endphp</span></td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="3">No Data Available</td>
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

    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Parties</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All Parties</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>S.NO</th>
                <th>Party Type</th>
                <th>Party Quantity</th>
              </tr>
              </thead>
              <tbody>
                @php $totcount = count($data['parties']); $i=1; @endphp
                @if($totcount>0)
                  @foreach($data['parties'] as $ky=>$vl)
                    <tr>
                      <td>{{$i++}}</td>
                      <td>{{$vl->name}}</td>
                      <td><span class="label label-success">{{$vl->clients}}</span></td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="3">No Data Available</td>
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

    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Products</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All Products</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>S.NO</th>
                <th>Product Name</th>
                <th>Product Quantity</th>
              </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Brands</td>
                  <td><span class="label label-success">{{$data['brands']}}</span></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Categories</td>
                  <td><span class="label label-success">{{$data['categories']}}</span></td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Products</td>
                  <td><span class="label label-success">{{$data['products']}}</span></td>
                </tr>
                <tr>
                  <td>4</td>
                  <td>Units</td>
                  <td><span class="label label-success">{{$data['units']}}</span></td>
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
          <h3 class="box-title">Latest Orders</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All Orders</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>S.No</th>
                <th>Order ID</th>
                <th>Order Total</th>
                <th>Status</th>
              </tr>
              </thead>
              <tbody>
                @php $totcount = count($data['latest_orders']); $i=1; @endphp
                @if($totcount>0)
                  @foreach($data['latest_orders'] as $ky=>$vl)
                    <tr>
                      <td>{{$i++}}</td>
                      <td><a href="pages/examples/invoice.html">{{$vl['order_no']}}</a></td>
                      <td>{{$vl['grand_total']}}</td>
                      <td><span class="label label-@if($vl['status']=='Cancel' || $vl['status']=='Close') danger @elseif($vl['status']=='Pending') warning @else info @endif">{{$vl['status']}}</span></td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="3">No Data Available</td>
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

    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Latest Collection</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All Colle...</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>S.No</th>
                <th>Order ID</th>
                <th>Payment Type</th>
                <th>Received Amount</th>
                <th>Status</th>
              </tr>
              </thead>
              <tbody>
                @php $totcount = count($data['latest_collection']); $i=1; @endphp
                @if($totcount>0)
                  @foreach($data['latest_collection'] as $ky=>$vl)
                    <tr>
                      <td>{{$i++}}</td>
                      <td><a href="pages/examples/invoice.html">{{$vl['order_id']}}</a></td>
                      <td>{{$vl['payment_method']}}</td>
                      <td>{{$vl['payment_received']}}</td>
                      <td><span class="label label-@if($vl['status']=='Cancel' || $vl['status']=='Close') danger @elseif($vl['status']=='Pending') warning @else info @endif">{{$vl['status']}}</span></td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="3">No Data Available</td>
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

    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Recent Visits</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
          <a href="javascript:void(0)" class="btn btn-sm btn-flat pull-right">View All Visits</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table class="table no-margin">
              <thead>
              <tr>
                <th>S.No.</th>
                <th>Client Name</th>
                <th>Visit Time</th>
                <th>Visit Date</th>
              </tr>
              </thead>
              <tbody>
                @php $totcount = count($data['total_visitsdata']); $i=1; @endphp
                @if($totcount>0)
                  @foreach($data['total_visitsdata'] as $ky=>$vl)
                    <tr>
                      <td>{{$i++}}</td>
                      <td>{{$vl['client']['name']}}</td>
                      <td>{{$vl['end_time']}}-{{$vl['start_time']}}</td>
                      <td>{{$vl['date']}}</td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="3">No Data Available</td>
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
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Performing Beats</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <canvas id="top_perform_beats" style="height: 300px;max-width:1075px !important;"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
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
          <canvas id="lineCharts" style="height: 300px;max-width:1075px !important;"></canvas>
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
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <canvas id="lineCharts2" style="height: 300px;max-width:1075px !important;" ></canvas>
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
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <canvas id="barChart" style="height: 300px;max-width:1075px !important;"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Productive Calls Vs Total Calls</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <canvas id="lineCharts_calls" style="height: 300px;max-width:1075px !important;"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Parties</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <canvas id="emp_visitsbargraph" style="height: 300px;max-width:1075px !important;"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
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
<script src="{{ asset('assets/bower_components/chart.js/Chart_1.js') }}"></script>
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

    // var allgraph_options = [];
    // $("#graph_selectopt option").each(function(a,b){
    //   allgraph_options.push($(b).val());
    // }).get();
    // $("#graph_selectopt").on('change',function(){
    //   var i = 0; var graph_selectedopt = [];
    //   $("#graph_selectopt :selected").map(function(a,b){
    //     i = i+1;
    //     var optionvalue = '';
    //     if(i<=4){
    //       optionvalue = $(b).val();
    //       graph_selectedopt.push($(b).val());
    //     }else{
    //       var difference_val = allgraph_options
    //              .filter(x => !graph_selectedopt.includes(x))
    //              .concat(graph_selectedopt.filter(x => !allgraph_options.includes(x)));
    //       for(var kk=0;kk<difference_val.length;kk++){
    //         console.log(difference_val[kk]);
    //         var inputr = $("#graph_selectopt option[value='"+difference_val[kk]+"']");
    //         inputr.prop('selected', true);
    //       }
    //     }
    //   }).get();
    // });

    $("#opt1").on('change',function(){
      var selectedtext = $("#opt1 :selected").text();
      var selectedtext = $("#opt1 :selected").text();
      console.log($("#opt1 :selected").attr('ses'));
      $("#col1name").text($("#opt1 :selected").text()); 
      $("#col1val").text($("#opt1").val()); 
    });

    $("#opt2").on('change',function(){
      var selectedtext = $("#opt2 :selected").text();
      var selectedtext = $("#opt2 :selected").text();
      $("#col2name").text($("#opt2 :selected").text()); 
      $("#col2val").text($("#opt2").val()); 
    });

    $("#opt3").on('change',function(){
      var selectedtext = $("#opt3 :selected").text();
      var selectedtext = $("#opt3 :selected").text();
      $("#col3name").text($("#opt3 :selected").text()); 
      $("#col3val").text($("#opt3").val()); 
    });

    $("#opt4").on('change',function(){
      var selectedtext = $("#opt4 :selected").text();
      var selectedtext = $("#opt4 :selected").text();
      $("#col4name").text($("#opt4 :selected").text()); 
      $("#col4val").text($("#opt4").val()); 
    });


    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        var startdate = start.format('YYYY-MM-DD');
        var enddate = end.format('YYYY-MM-DD');
        window.location.href = "{{ domain_route('company.admin.home.mod') }}/"+startdate+'/'+enddate;
      }
    )

    $("#daterange-btn").on('change',function(){
      var startdate = $('#daterange-btn').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var enddate = $('#daterange-btn').data('daterangepicker').endDate.format('YYYY-MM-DD');
      // console.log(startdate,enddate);
    })

    
    function initMap() {
        var companylat = 26.4439023;
        var companylng = 87.2838812;
        map = new google.maps.Map(
            document.getElementById('visitormap'),
            {
                zoom: 10,
                center: new google.maps.LatLng(companylat, companylng),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
    }

    $(document).ready(function(){

    // var areaChartData = {
    //   labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    //   datasets: [
    //     {
    //       label               : 'Electronics',
    //       fillColor           : 'rgba(210, 214, 222, 1)',
    //       strokeColor         : 'rgba(210, 214, 222, 1)',
    //       pointColor          : 'rgba(210, 214, 222, 1)',
    //       pointStrokeColor    : '#c1c7d1',
    //       pointHighlightFill  : '#fff',
    //       pointHighlightStroke: 'rgba(220,220,220,1)',
    //       data                : [65, 59, 80, 81, 56, 55, 40]
    //     },
    //     {
    //       label               : 'Digital Goods',
    //       fillColor           : 'rgba(60,141,188,0.9)',
    //       strokeColor         : 'rgba(60,141,188,0.8)',
    //       pointColor          : '#3b8bba',
    //       pointStrokeColor    : 'rgba(60,141,188,1)',
    //       pointHighlightFill  : '#fff',
    //       pointHighlightStroke: 'rgba(60,141,188,1)',
    //       data                : [28, 48, 40, 19, 86, 27, 90]
    //     }
    //   ]
    // }


    //Top Parties
    var topparties_index = topparties_value = '';
    <?php
    $topparties_index = $topparties_value = '';$j = 0;
    $totcount = count($graph['topparties']);
    if($totcount>0){
      foreach($graph['topparties'] as $ytp=>$typ){
        if(($totcount-1)>$j){
          $topparties_index .= "'".$ytp."',";
          $topparties_value .= $typ.',';
        }else{
          $topparties_index .= "'".$ytp."'";
          $topparties_value .= $typ;
        }
        $j++;
      }
    }
    ?>
    var empchartdata = {
      labels  : [<?php print_r($topparties_index); ?>],
      datasets: [
        {
          label               : 'Digital Goods',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [<?php print_r($topparties_value); ?>]
        }
      ]
    }
    
    var barChartCanvas                   = $("#emp_visitsbargraph").get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = empchartdata
    barChartData.datasets[0].fillColor   = '#00a65a'
    barChartData.datasets[0].strokeColor = '#00a65a'
    barChartData.datasets[0].pointColor  = '#00a65a'
    var barChartOptions                  = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero        : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - If there is a stroke on each bar
      barShowStroke           : true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth          : 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing         : 35,
      //Number - Spacing between data sets within X values
      barDatasetSpacing       : 2,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to make the chart responsive
      responsive              : true,
      maintainAspectRatio     : false,
    }
    barChartOptions.datasetFill = false
    barChart.Bar(barChartData, barChartOptions);



    //top performing beats
    var topbeats_index = topbeats_value = '';
    <?php
    $beatsindex = $beatsvalue = '';$j = 0;
    $totcount = count($graph['topbeats']);
    if($totcount>0){
      foreach($graph['topbeats'] as $yt=>$ty){
        if(($totcount-1)>$j){
          $beatsindex .= "'".$yt."',";
          $beatsvalue .= $ty.',';
        }else{
          $beatsindex .= "'".$yt."'";
          $beatsvalue .= $ty;
        }
        $j++;
      }
    }
    ?>
    var empchartdata = {
      labels  : [<?php print_r($beatsindex); ?>],
      datasets: [
        {
          label               : 'Top Performing Beats',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [<?php print_r($beatsvalue); ?>]
        }
      ]
    }
    var barChartCanvas                   = $("#top_perform_beats").get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = empchartdata
    barChartData.datasets[0].fillColor   = '#00a65a'
    barChartData.datasets[0].strokeColor = '#00a65a'
    barChartData.datasets[0].pointColor  = '#00a65a'
    var barChartOptions                  = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero        : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - If there is a stroke on each bar
      barShowStroke           : true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth          : 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing         : 35,
      //Number - Spacing between data sets within X values
      barDatasetSpacing       : 2,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to make the chart responsive
      responsive              : true,
      maintainAspectRatio     : false,
    }
    barChartOptions.datasetFill = false
    barChart.Bar(barChartData, barChartOptions);



    /*
     * DONUT CHART
     * -----------
     */
    //-------------
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieChart       = new Chart(pieChartCanvas)
    var colorArr = ['#f56954','#00a65a','#d2d6de','#3c8dbc','#f39c12','#00c0ef','#7b113a','#5b8a72','#864000','#96bb7c','#f7ea00','#e84545','#1f441e','#f0c929','#440a67','#00917c','#f14668'];
    var PieData        = [
      <?php
      $totexpense = count($graph['expenses']);
      if($totexpense>0){
        $j = 0;
        foreach($graph['expenses'] as $ytp=>$typ){
            $expense_index = $ytp;
            $expense_value = count($typ);
      ?>
        {
          value    : <?php echo $expense_index; ?>,
          color    : colorArr[<?php echo $j++; ?>],
          highlight: '#f56954',
          label    : <?php echo "'".$expense_value."'"; ?>
        },
      <?php
        }
      }
      ?>
    ]
    var pieOptions     = {
      //Boolean - Whether we should show a stroke on each segment
      segmentShowStroke    : true,
      //String - The colour of each segment stroke
      segmentStrokeColor   : '#fff',
      //Number - The width of each segment stroke
      segmentStrokeWidth   : 2,
      //Number - The percentage of the chart that we cut out of the middle
      percentageInnerCutout: 50, // This is 0 for Pie charts
      //Number - Amount of animation steps
      animationSteps       : 100,
      //String - Animation easing effect
      animationEasing      : 'easeOutBounce',
      //Boolean - Whether we animate the rotation of the Doughnut
      animateRotate        : true,
      //Boolean - Whether we animate scaling the Doughnut from the centre
      animateScale         : false,
      //Boolean - whether to make the chart responsive to window resizing
      responsive           : true,
      // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio  : true,
      //String - A legend template
      legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
    }
    //Create pie or douhnut chart
    pieChart.Doughnut(PieData, pieOptions)


    //-------------
    //- LINE CHART -
    //--------------
    var areaChartOptions = {
      //Boolean - If we should show the scale at all
      showScale               : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : false,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - Whether the line is curved between points
      bezierCurve             : true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension      : 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot                : false,
      //Number - Radius of each point dot in pixels
      pointDotRadius          : 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth     : 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius : 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke           : true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth      : 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill             : true,
      //String - A legend template
      // legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio     : false,
      //Boolean - whether to make the chart responsive to window resizing
      responsive              : true,
      legendCallback: function (chart) {             
            // Return the HTML string here.
            console.log(chart.data.datasets);
            var text = [];
            text.push('<ul class="' + chart.id + '-legend">');
            for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
                text.push('<li><span id="legend-' + i + '-item" style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '"   onclick="updateDataset(event, ' + '\'' + i + '\'' + ')">');
                if (chart.data.labels[i]) {
                    text.push(chart.data.labels[i]);
                }
                text.push('</span></li>');
            }
            text.push('</ul>');
            return text.join("");
        },
        scales: {
        yAxes: [{
          scaleLabel: {
            display: true,
            labelString: 'probability'
          }
        }]
      }     
    }
    var totalorder_index = totalorder_value = zeroorder_value = zeroorder_index = '';
    <?php
    $totalorder_index = $totalorder_value = $zeroorder_index = $zeroorder_value = '';$j = 0;
    $totcount = count($graph['totorder']);
    $totcount_zero = count($graph['zeroorder']);
    if($totcount>0){
      $j = 0;
      foreach($graph['totorder'] as $ytt=>$tyt){
        if(($totcount-1)>$j){
          $totalorder_index .= "'".$ytt."',";
          $totalorder_value .= count($tyt).',';
        }else{
          $totalorder_index .= "'".$ytt."'";
          $totalorder_value .= count($tyt);
        }
        $j++;
      }
    }
    if($totcount_zero){
      $j = 0;
      foreach($graph['zeroorder'] as $yty=>$tyy){
        if(($totcount_zero-1)>$j){
          $zeroorder_index .= "'".$yty."',";
          $zeroorder_value .= count($tyy).',';
        }else{
          $zeroorder_index .= "'".$yty."'";
          $zeroorder_value .= count($tyy);
        }
        $j++;
      }
    }
    ?>
    var areaChartData = {
      labels  : [<?php print_r($totalorder_index); ?>],
      datasets: [
        {
          label               : 'Total Order',
          fillColor           : 'rgba(210, 214, 222, 1)',
          strokeColor         : 'rgba(210, 214, 222, 1)',
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [<?php print_r($totalorder_value); ?>]
        },
        {
          label               : 'No-Order',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [<?php print_r($zeroorder_value); ?>]
        }
      ]
    }

    var lineChartCanvas          = $('#lineCharts').get(0).getContext('2d')
    var lineChart                = new Chart(lineChartCanvas)
    var lineChartOptions         = areaChartOptions
    lineChartOptions.datasetFill = false
    lineChart.Line(areaChartData, lineChartOptions)



    //-------------
    //- LINE CHART -
    //--------------
    var timespent_index = timespent_value = '';
    <?php
    $timespent_index = $timespent_value = '';$j = 0;
    $totcount = count($graph['timeonvisit']);
    if($totcount>0){
      foreach($graph['timeonvisit'] as $yt=>$ty){
        if(($totcount-1)>$j){
          $timespent_index .= "'".$yt."',";
          $timespent_value .= count($ty).',';
        }else{
          $timespent_index .= "'".$yt."'";
          $timespent_value .= count($ty);
        }
        $j++;
      }
    }
    ?>
    var collChartData = {
      labels  : [<?php print_r($timespent_index); ?>],
      datasets: [
        {
          label               : 'Electronics',
          fillColor           : 'rgb(11,118,118)',
          strokeColor         : 'rgb(11,118,118)',
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [<?php print_r($timespent_value); ?>]
        }
      ]
    }
    var lineChartCanvas          = $('#lineCharts2').get(0).getContext('2d')
    var lineChart                = new Chart(lineChartCanvas)
    var lineChartOptions         = areaChartOptions
    lineChartOptions.datasetFill = false
    lineChart.Line(collChartData, lineChartOptions)

    
    //Total calls line graph
    var totalcalls_index = totalcalls_value = prodcalls_value = prodcalls_index = '';
    <?php
    $totalcalls_index = $totalcalls_value = $prodcalls_value = $prodcalls_index = '';$j = 0;
    $totcalls = count($graph['totalcalls']);
    $prodcalls = count($graph['prodcalls']);
    if($totcalls>0){
      $j = 0;
      foreach($graph['totalcalls'] as $ytty=>$tyty){
        if(($totcalls-1)>$j){
          $totalcalls_index .= "'".$ytty."',";
          $totalcalls_value .= $tyty.',';
        }else{
          $totalcalls_index .= "'".$ytty."',";
          $totalcalls_value .= $tyty;
        }
        $j++;
      }
    }
    if($prodcalls){
      $j = 0;
      foreach($graph['prodcalls'] as $ytyt=>$tyyt){
        if(($prodcalls-1)>$j){
          $prodcalls_index .= "'".$ytyt."',";
          $prodcalls_value .= count($tyyt).',';
        }else{
          $prodcalls_index .= "'".$ytyt."'";
          $prodcalls_value .= count($tyyt);
        }
        $j++;
      }
    }
    ?>
    var collChartData = {
      labels  : [<?php print_r($totalcalls_index); ?>],
      datasets: [
        {
          label               : 'Total Order',
          fillColor           : 'rgba(210, 214, 222, 1)',
          strokeColor         : 'rgba(210, 214, 222, 1)',
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [<?php print_r($totalcalls_value); ?>]
        },
        {
          label               : 'No-Order',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [<?php print_r($prodcalls_value); ?>]
        }
      ]
    }
    var lineChartCanvas          = $('#lineCharts_calls').get(0).getContext('2d')
    var lineChart                = new Chart(lineChartCanvas)
    var lineChartOptions         = areaChartOptions
    lineChartOptions.datasetFill = false
    lineChart.Line(collChartData, lineChartOptions)


    //Total Parties added line graph
    var totalparties_index = totalparties_value = '';
    <?php
    $totalparties_index = $totalparties_value = '';$j = 0;
    $totcount = count($graph['newpartiesadded']);
    if($totcount>0){
      foreach($graph['newpartiesadded'] as $yt=>$ty){
        if(($totcount-1)>$j){
          $totalparties_index .= "'".$yt."',";
          $totalparties_value .= count($ty).',';
        }else{
          $totalparties_index .= "'".$yt."'";
          $totalparties_value .= count($ty);
        }
        $j++;
      }
    }
    ?>
    var collChartData = {
      labels  : [<?php print_r($totalparties_index); ?>],
      datasets: [
        {
          label               : 'Electronics',
          fillColor           : 'rgb(11,118,118)',
          strokeColor         : 'rgb(11,118,118)',
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [<?php print_r($totalparties_value); ?>]
        }
      ]
    }
    var lineChartCanvas          = $('#barChart').get(0).getContext('2d')
    var lineChart                = new Chart(lineChartCanvas)
    var lineChartOptions         = areaChartOptions
    lineChartOptions.datasetFill = false
    lineChart.Line(collChartData, lineChartOptions)



    })





</script>


@endsection