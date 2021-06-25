
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
          <option value="{{ $data['new_parties'] }}">NEW PARTIES <br>ADDED</option>
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
            <ul class="nav nav-pills nav-stacked">
              <li style="font-weight:bold;"><a href="#"><i class="fa fa-inbox"></i> Leaves
                <span class="label label-primary pull-right">{{$data['leaves']}}</span></a></li>
              <li style="font-weight:bold;"><a href="#"><i class="fa fa-envelope"></i>Expenses
                <span class="label label-primary pull-right">{{$data['expenses']}}</span></a></li>
              <li style="font-weight:bold;"><a href="#"><i class="fa fa-file-text"></i>Tours
                <span class="label label-primary pull-right">{{$data['tours']}}</span></a></li>
              <li style="font-weight:bold;"><a href="#"><i class="fa fa-filter"></i> Targets 
                <span class="label label-primary pull-right">{{$data['companytargets']}}</span></a></li>
              <li style="font-weight:bold;"><a href="#"><i class="fa fa-trash"></i> Trash</a></li>
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
    <div class="col-md-6">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Top Products</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
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
          <h3 class="box-title">Top Brands</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div id="top_brands" style="height: 300px;max-width:1075px !important;"></div>
        </div>
      </div>
    </div>
  </div>

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
          <div id="top_perform_beats" style="height: 300px;max-width:1075px !important;"></div>
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
          <div id="lineCharts" style="height: 300px;max-width:1075px !important;"></div>
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
          <div id="lineCharts2" style="height: 300px;max-width:1075px !important;" ></div>
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
          <div id="barChart" style="height: 300px;max-width:1075px !important;"></div>
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
          <div id="lineCharts_calls" style="height: 300px;max-width:1075px !important;"></div>
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
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div id="emp_visitsbargraph" style="height: 300px;max-width:1075px !important;"></div>
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
          <div id="pieChart" style="height: 300px;"></div>
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

<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>


<script>

    var colorArr_bg = ['rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(75, 192, 192, 0.2)',
'rgba(153, 102, 255, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(75, 192, 192, 0.2)','rgba(153, 102, 255, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(75, 192, 192, 0.2)','rgba(153, 102, 255, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(75, 192, 192, 0.2)',
'rgba(153, 102, 255, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(75, 192, 192, 0.2)','rgba(153, 102, 255, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(75, 192, 192, 0.2)','rgba(153, 102, 255, 0.2)','rgba(255, 159, 64, 0.2)'];
    var colorArr_brd = ['rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(75, 192, 192, 1)','rgba(153, 102, 255, 1)','rgba(255, 159, 64, 1)','rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(75, 192, 192, 1)','rgba(153, 102, 255, 1)','rgba(255, 159, 64, 1)','rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(75, 192, 192, 1)','rgba(153, 102, 255, 1)','rgba(255, 159, 64, 1)','rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(75, 192, 192, 1)','rgba(153, 102, 255, 1)','rgba(255, 159, 64, 1)','rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(75, 192, 192, 1)','rgba(153, 102, 255, 1)','rgba(255, 159, 64, 1)'];

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

    

    $(document).ready(function(){

    //top performing beats rgb(11,118,118)
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
    var options = {
          series: [{
            name: 'Total Orders',
            data: [<?php print_r($beatsvalue); ?>]
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
          categories: [<?php print_r($beatsindex); ?>],
          tickPlacement: 'on'
        },
        yaxis: {
          title: {
            text: 'Order Quantity',
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
      var chart = new ApexCharts(document.querySelector("#top_perform_beats"), options);
      chart.render();


    //Total Order Vs No-Order
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
    var options = {
          series: [
            {
              name: 'Total Orders',
              data: [<?php print_r($totalorder_value); ?>]
            },
            {
              name: 'Zero Orders',
              data: [<?php print_r($zeroorder_value); ?>]
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
          categories: [<?php print_r($totalorder_index); ?>],
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
      var chart = new ApexCharts(document.querySelector("#lineCharts"), options);
      chart.render();


    //time spent on visits
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
    var options = {
          series: [{
            name: 'Time(in seconds)',
            data: [<?php print_r($timespent_value); ?>]
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
          categories: [<?php print_r($timespent_index); ?>],
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
      var chart = new ApexCharts(document.querySelector("#lineCharts2"), options);
      chart.render();



    //new parties added
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
        var options = {
          series: [{
            name: 'No. of Added Parties',
            data: [<?php print_r($totalparties_value); ?>]
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
            text: 'Added Date',
          },
          categories: [<?php print_r($totalparties_index); ?>],
          tickPlacement: 'on'
        },
        yaxis: {
          title: {
            text: 'No. of Added Parties',
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
      var chart = new ApexCharts(document.querySelector("#barChart"), options);
      chart.render();


    //Productive Calls Vs Total Calls
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
var options = {
          series: [
            {
              name: 'Total Calls',
              data: [<?php print_r($totalcalls_value); ?>]
            },
            {
              name: 'Productive Calls',
              data: [<?php print_r($prodcalls_value); ?>]
            }
          ],
        chart: {
          height: 350,
          type: 'area',
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
          categories: [<?php print_r($totalcalls_index); ?>],
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
      var chart = new ApexCharts(document.querySelector("#lineCharts_calls"), options);
      chart.render();


    //Top Parties
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
    var options = {
          series: [{
            name: 'Total Orders',
            data: [<?php print_r($topparties_value); ?>]
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
          categories: [<?php print_r($topparties_index); ?>],
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
    var chart = new ApexCharts(document.querySelector("#emp_visitsbargraph"), options);
    chart.render();




    <?php
      $expense_index = $expense_value = '';
      $totexpense = count($graph['expenses']);
      if($totexpense>0){
        $j = 0;
        foreach($graph['expenses'] as $ytph=>$typh){
          if(($totexpense-1)>$j){
            $expense_index .= "'".$ytph."',";
            $expense_value .= count($typh).',';
          }else{
            $expense_index .= "'".$ytph."'";
            $expense_value .= count($typh);
          }
          $j++;
        }
      }
    ?>
    var options = {
          series: [<?php print_r($expense_value); ?>],
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
        legend: {
          formatter: function(val, opts) {
            expense_label = [<?php print_r($expense_index); ?>];
            return "Expense On: " + expense_label[opts.seriesIndex]
          }
        },
        title: {
          text: 'Expenses'
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

        var chart = new ApexCharts(document.querySelector("#pieChart"), options);
        chart.render();


    //Top products
    <?php
    $topproducts_index = $topproducts_value = '';$j = 0;
    $totcount_prod = count($graph['topproducts']);
    if($totcount_prod>0){
      foreach($graph['topproducts'] as $ytpe=>$type){
        if(($totcount_prod-1)>$j){
          $topproducts_index .= "'".$ytpe."',";
          $topproducts_value .= $type.',';
        }else{
          $topproducts_index .= "'".$ytpe."'";
          $topproducts_value .= $type;
        }
        $j++;
      }
    }
    ?>
    var options = {
          series: [{
            name: 'Total Orders',
            data: [<?php print_r($topproducts_value); ?>]
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
          categories: [<?php print_r($topproducts_index); ?>],
          tickPlacement: 'on',
          title: {
            text: 'Total No. of Orders',
          },
        },
        yaxis: {
          title: {
            text: 'Products',
          },
        },

    };
    var chart = new ApexCharts(document.querySelector("#top_products"), options);
    chart.render();



    //Top brands
    <?php
    $topbrands_index = $topbrands_value = '';$j = 0;
    $totcount_brands = count($graph['topbrands']);
    if($totcount_brands>0){
      foreach($graph['topbrands'] as $ytper=>$typer){
        if(($totcount_brands-1)>$j){
          $topbrands_index .= "'".$ytper."',";
          $topbrands_value .= $typer.',';
        }else{
          $topbrands_index .= "'".$ytper."'";
          $topbrands_value .= $typer;
        }
        $j++;
      }
    }
    ?>
    var options = {
          series: [{
            name: 'Total Orders',
            data: [<?php print_r($topbrands_value); ?>]
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
          categories: [<?php print_r($topbrands_index); ?>],
          tickPlacement: 'on',
          title: {
            text: 'Total No. of Orders',
          },
        },
        yaxis: {
          title: {
            text: 'Brands',
          },
        },

    };
    var chart = new ApexCharts(document.querySelector("#top_brands"), options);
    chart.render();


    //visitor map

    



    })

    //visitor map
    // function initMap() {
    //     var companylat = 26.4439023;
    //     var companylng = 87.2838812;
    //     map = new google.maps.Map(
    //         document.getElementById('visitormap'),
    //         {
    //             zoom: 10,
    //             center: new google.maps.LatLng(companylat, companylng),
    //             mapTypeId: google.maps.MapTypeId.ROADMAP
    //         });
    // }
    // mapAddress('visitormap',['26.4439023','87.2838812']);

    // function mapAddress(mapElement, address) {
    //   var geocoder = new google.maps.Geocoder();

    //   geocoder.geocode({ 'address': address }, function (results, status) {
    //       if (status == google.maps.GeocoderStatus.OK) {
    //           var mapOptions = {
    //               zoom: 14,
    //               center: results[0].geometry.location,
    //               disableDefaultUI: true
    //           };
    //           var map = new google.maps.Map(document.getElementById(mapElement), mapOptions);
    //           var marker = new google.maps.Marker({
    //               map: map,
    //               position: results[0].geometry.location
    //           });
    //       } else {
    //           alert("Geocode was not successful for the following reason: " + status);
    //       }
    //   });
    // }


</script>




@endsection