
@extends('layouts.company')
@section('title', 'Dashboard')
@section('title', 'Company Dashboard')
@section('stylesheets')
<style>
  .clickable-row {
    cursor: pointer;
  }

  .retimg {
    vertical-align: initial;
    width: 25px;
  }


</style>

<link rel="stylesheet" href="{{ asset('assets/dist/css/drag-muuri.css') }}">
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
  <div class="row grid">
    @if(Auth::user()->can('employee-view'))
    <div class="col-md-4 col-sm-4 col-xs-12 grid-item">
      <div class="grid-item-content">
        <div class="info-box">        
          <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-user"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Employees ({{$employees_count}})</span>
            @if(config('settings.attendance')==1)
            <a href="{{domain_route('company.admin.todayattendancereport')}}">
              <div class="label label-primary" style="border-radius: 20px;font-size: 12px !important;margin: 7px 0px;
                                            display: block;padding: 5px;background-color:#12674E !important"> Present
                Today
                ({{$attendance['present_employees']}})</div>
            </a>
            @endif
            {{-- <span class="info-box-number">{{ $empcount }}</span> --}}
            @php
            $i=0
            @endphp
            @foreach($employee_breakdowns as $employee_breakdown)
            <a href="{{domain_route('company.admin.employee.filtered', [$employee_breakdown['id']])}}">
              <span class="label {{ $label_type[rand(0,6)] }}" style="font-size: 12px !important;margin: 7px 0px;
                    display: inline-block;padding: 5px;"> {{ $employee_breakdown['designation'] }}
                ({{ $employee_breakdown['count'] }})</span>
            </a>
            @endforeach
  
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
    </div>
    @endif
    <!-- /.col -->
    @if(Auth::user()->can('party-view') && config('settings.party')==1)
    <div class="col-xs-4 grid-item">
      <div class="grid-item-content">
        <div class="info-box">        
          <span class="info-box-icon bg-red"><i class="fa fa-fw fa-user-secret"></i></span>
  
          <div class="info-box-content">
            <span class="info-box-text">Parties @if($types)({{ $partiescount }})@endif</span>
            @if(!($types))
            <span class="info-box-number">{{ $partiescount }}</span>
            @endif
            @php
            $i=0
            @endphp
            @foreach($types as $ptype)
            @if($ptype->can)
            <a href="{{domain_route('company.admin.client.subclients', ['id'=>$ptype->ptypeid])}}">
              <span class="label {{ $label_type[rand(0,6)] }}" style="font-size: 12px !important;margin: 7px 0px;
      display: inline-block;    padding: 5px;"> {{ $ptype->name }} ({{ $ptype->count }})</span>
            </a>
            @endif
            @endforeach
  
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
    </div>
    @endif
    <!-- /.col -->

    @if(Auth::user()->can('product-view') && config('settings.product')==1)
    <!-- /.col -->
    <div class="col-xs-4 grid-item">
      <div class="grid-item-content">
        <div class="info-box">        
          <span class="info-box-icon bg-yellow"><i class="fa fa-fw fa-cart-plus"></i></span>
  
          <div class="info-box-content">
            <span class="info-box-text">Products</span>
            <span class="info-box-number">{{ $prodcount }}</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
    </div>
    <!-- /.col -->
    @endif

    <!-- TABLE: LATEST ORDERS -->
  <!-- <div class="row"> -->
  @if(Auth::user()->can('order-view') && config('settings.orders')==1)
    @if(!empty($recentorders))
    <div class="col-xs-6 grid-item">
      <div class="grid-item-content">
        <div class="box dashboard-list-box">
          <div class="box-header dash-header">
            <h3 class="box-title">Latest Orders</h3>            
            <div class="box-tools pull-right">
              <a href="{{ domain_route('company.admin.order') }}" class="btn btn-default btn-sm">View All Orders</a>
  
            </div>
          </div>
          <div class="box-body">
            <div class="responsive">
              <table id="example2" class="table no-margin table-striped">
                <thead>
                  <tr>
                    <th>Party Name</th>
                    @if(getClientSetting()->order_with_amt==0)
                    <th>Amount</th>
                    @else
                    @if(config('settings.company_id')==55 || config('settings.company_id')==56)
                    <th>Party Type</th>
                    @endif
                    <th>Order No.</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
  
                  @foreach ($recentorders as $recentorder)
                  @if(isset($recentorder['grand_total']))
                  <tr class='clickable-row'
                    data-href="{{domain_route('company.admin.order.showorder', [$recentorder['id']])}}">
                    <td><b>{{ ucwords(strtolower(getClient($recentorder['client_id'])['company_name'] )) }}</b>
                      <br>
                      @if($recentorder['employee_id'] == 0)
                      <span><img src="{{URL::asset('assets/dist/img/ret_logo.png')}}" class="retimg"></img>
                        {{ $recentorder['contact_person'] }}
                        @else
                        {{ ucwords(strtolower(getEmployee($recentorder['employee_id'])['name'] ))}}
                        @endif / {{ getDeltaDate(date('Y-m-d',strtotime($recentorder['order_date']))) }} </span>
                    </td>
  
                    @if(getClientSetting()->order_with_amt==0)
                    <td>{{config('settings.currency_symbol')}} {{ $recentorder['grand_total'] }}</td>
                    @else
  
                    @if(isset($recentorder['client_type']))
                    <td>
  
                      {{ getPartyType($recentorder['client_type'])['name'] }}
                    </td>
                    @endif
  
                    <td><a
                        href="{{ domain_route('company.admin.order.show',[$recentorder['id']]) }}">{{getClientSetting()->order_prefix }}{{ $recentorder['order_no'] }}</a>
                    </td>
                    @endif
  
                  </tr>
                  @endif
                  @endforeach
  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
    @endif

    @if(Auth::user()->can('collection-view') && config('settings.collections')==1)
    @if(!empty($collectionamt))
    <div class="col-xs-6 grid-item">
      <div class="grid-item-content">
        <div class="box dashboard-list-box">
          <div class="box-header dash-header">
            <h3 class="box-title">Latest Collections</h3>            
            <div class="box-tools pull-right">
              <a href="{{ domain_route('company.admin.collection') }}" class="btn btn-default btn-sm">View All
                Collections</a>
  
            </div>
          </div>
          <div class="box-body">
            <div class="responsive">
              <table id="example2" class="table no-margin">
                <thead>
                  <tr>
                    <th>Party Name</th>
                    <th>Amount</th>
                    <th>Bank Name</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($recentcollections as $recentcollection)
                  @if($recentcollection['client_id'])
                    <tr class='clickable-row'
                      data-href="{{domain_route('company.admin.collection.show', [$recentcollection['id']])}}">
  
                      <td><b>{{ ucwords(strtolower(getClient($recentcollection['client_id'])['company_name'] )) }}</b>
                        <br>
                        @if($recentcollection['employee_type'] == 'Admin')
                        {{ Auth::user()->name.' (Admin)' }}
                        @elseif($recentcollection['employee_type'] == 'Employee')
                        {{ ucwords(strtolower(getEmployee($recentcollection['employee_id'])['name'])) }}
                        @endif / {{ getDeltaDate(date('Y-m-d',strtotime($recentcollection['payment_date']))) }}
                      </td>
                      <td>{{config('settings.currency_symbol')}} {{ $recentcollection['payment_received'] }}</td>
                      <td>@if(isset($recentcollection->bank->name)) {{$recentcollection->bank->name}}@else Cash @endif </td>
                    </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
    @endif
  <!-- </div> -->
  </div>
  <!-- /.row -->
  {{-- <div class="row">
      <div class="col-xs-3">
        <a href="{{ domain_route('company.admin.employee.create') }}">
  <div class="button-bt employee-bg">
    <i class="fa fa-user emp-icon"></i>
    <p>Add Employee</p>
  </div>
  </a>
  </div>
  <div class="col-xs-3">
    <a href="{{ domain_route('company.admin.product.create') }}">
      <div class="button-bt product-bgs">
        <i class="fa fa-th-large emp-icon"></i>
        <p>Add Product</p>
      </div>
    </a>
  </div>
  <div class="col-xs-3">
    <a href="{{ domain_route('company.admin.client.create') }}">
      <div class="button-bt parties-bgs">
        <i class="fa fa-user-secret emp-icon"></i>
        <p>Add Parties</p>
      </div>
    </a>
  </div>
  <div class="col-xs-3">
    <a href="{{ domain_route('company.admin.activities.create') }}">
      <div class="button-bt tasks-bgs">
        <i class="fa fa-file emp-icon"></i>
        <p>Add Activities</p>
      </div>
    </a>
  </div>
  <div class="col-xs-3">
    <a href="{{ domain_route('company.admin.order.create') }}">
      <div class="button-bt order-bgs">
        <i class="fa fa-cart-plus emp-icon"></i>
        <p>Add Order</p>
      </div>
    </a>
  </div>
  <div class="col-xs-3">
    <a href="{{ domain_route('company.admin.collection.create') }}">
      <div class="button-bt collection-bgs">
        <i class="fa fa-money emp-icon"></i>
        <p>Add Collection</p>
      </div>
    </a>
  </div>
  <div class="col-xs-3">
    <a href="{{ domain_route('company.admin.expense.create') }}">
      <div class="button-bt expenses-bgs">
        <i class="fa fa-delicious emp-icon"></i>
        <p>Add Expenses</p>
      </div>
    </a>
  </div>
  <div class="col-xs-3">
    <a href="{{ domain_route('company.admin.announcement.create') }}">
      <div class="button-bt announcements-bgs">
        <i class="fa fa-volume-up emp-icon"></i>
        <p>Add Announcements</p>
      </div>
    </a>
  </div>
  </div> --}}
  <?php //print_r($orderamt); ?>
  
  <div id="alert-modal-extension" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          {{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
          <h4 class="modal-title">Subscription Ended</h4>
        </div>
        <div class="modal-body" id="alert-text">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-dismiss="modal">
            <span class='glyphicon glyphicon-remove'></span> Close
          </button>
        </div>
      </div>
    </div>
  </div>
  <div id="alert-modal-disabled" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          {{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
          <h4 class="modal-title">Subscription Expired</h4>
        </div>
        <div class="modal-body" id="disabled-alert-text">

        </div>
        {{-- <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">
              <span class='glyphicon glyphicon-remove'></span> Close
            </button>
          </div> --}}
      </div>
    </div>
  </div>
</section>
<!-- /.content -->
<div class="dsa-loader">Loading..</div>
@endsection



@section('scripts')
<script src="{{ asset('assets/dist/js/muuri.js') }}"></script>
<script src="{{ asset('assets/dist/js/drag-muuri.js') }}"></script>
<script>
  $(".clickable-row").click(function() {
    let href = $(this).data("href");
    window.open(href, '_blank');
  });
  @if($alert_msg!="")
    $('#alert-modal-extension').modal('show');
    $('#alert-text').html("{!!$alert_msg!!}");
  @endif

  @if ($disabled_msg!="")
    $('#alert-modal-disabled').modal({
      backdrop: 'static',
      keyboard: false
    });
    $('#alert-modal-disabled').modal('show');
    $('.stsAlertEnd').addClass('hidden');
    $('#disabled-alert-text').html("{!!$disabled_msg!!}");     

  @endif  
</script>
@endsection