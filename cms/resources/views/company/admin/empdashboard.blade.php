@extends('layouts.company')
@section('title', 'Dashboard')
@section('stylesheets')
  <style>
    .button-bt {
      width: 100%;
      height: 120px;
      text-align: center;
      padding: 30px 10px;
      transition: 0.5s;
      margin-bottom: 25px;
      box-shadow: 0px 4px 6px #4a4a4a;
    }
    
    .emp-icon {
      color: #fff;
      font-size: 20px;
    }
    
    .button-bt p {
      color: #fff;
      margin-top: 5px;
    }
    
    .employee-bg {
      background: #fb9215;
      /* border-left: 5px solid #c36a00; */
    }
    
    .employee-bg:hover {
      background: #c36a00;
    }
    
    .product-bgs {
      background: #53ac59;
      /* border-left: 5px solid #038c0c; */
    }
    
    .product-bgs:hover {
      background: #038c0c;
    }
    
    .parties-bgs {
      background: #f23e41;
      /* border-left: 5px solid #a50204; */
    }
    
    .parties-bgs:hover {
      background: #a50204;
    }
    
    .order-bgs {
      background: #17b1d4;
      /* border-left: 5px solid #068492; */
    }
    
    .order-bgs:hover {
      background: #068492;
    }
    
    .collection-bgs {
      background: #942aae;
      /* border-left: 5px solid #73068e; */
    }
    
    .collection-bgs:hover {
      background: #73068e;
    }
    
    .expenses-bgs {
      background: #555555;
      /* border-left: 5px solid #2d2b2b; */
    }
    
    .expenses-bgs:hover {
      background: #2d2b2b;
    }
    
    .tasks-bgs {
      background: #009688;
      /* border-left: 5px solid #02635a; */
    }
    
    .tasks-bgs:hover {
      background: #02635a;
    }
    
    .announcements-bgs {
      background: #db6741;
      /* border-left: 5px solid #8e2604; */
    }
    
    .announcements-bgs:hover {
      background: #8e2604;
    }
  </style>
@endsection

@section('title', 'Company Dashboard')

@section('content')
  <!-- Main content -->
  <section class="content">
    <!-- Info boxes -->
    <div class="row">
      <div class="col-xs-4 ">
        <div class="info-box">
          <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-user"></i></span>
          
          <div class="info-box-content">
            <span class="info-box-text">Products</span>
            <span class="info-box-number"> ####</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      <div class="col-xs-4">
        <div class="info-box">
          <span class="info-box-icon bg-red"><i class="fa fa-fw fa-user-secret"></i></span>
          
          <div class="info-box-content">
            <span class="info-box-text">Orders</span>
           <span class="info-box-number">####</span>
          
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
      
      <!-- /.col -->
      <div class="col-xs-4">
        <div class="info-box">
          <span class="info-box-icon bg-yellow"><i class="fa fa-fw fa-cart-plus"></i></span>
          
          <div class="info-box-content">
            <span class="info-box-text">Collections</span>
            <span class="info-box-number">####</span>
          </div>
          <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
      
  
  
  <!-- TABLE: LATEST ORDERS -->
    <div class="row">
      @if(!empty($recentorders))
        <div class="col-xs-6">
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
                    <tr>
                      <td><b>{{ ucwords(strtolower(getClient($recentorder['client_id'])['company_name'] )) }}</b>
                        <br>
                        @if($recentorder['employee_id'] == 0)
                          {{ Auth::user()->name.' (Admin)' }}
                        @else
                          {{ ucwords(strtolower(getEmployee($recentorder['employee_id'])['name'] ))}}
                        @endif / {{ getDeltaDate(date('Y-m-d',strtotime($recentorder['order_date']))) }}
                      </td>
                      
                      @if(getClientSetting()->order_with_amt==0)
                        <td>{{ $recentorder['grand_total'] }}</td>
                      @else
                        
                          @if(isset($recentorder['client_type']))
                        <td>
                            {{$recentorder['client_type']}}
                            {{ getPartyType($recentorder['client_type'])['name'] }}
                        </td>
                          @endif
                        
                        <td><a
                              href="{{ domain_route('company.admin.order.show',[$recentorder['id']]) }}">{{getClientSetting()->order_prefix }}{{ $recentorder['order_no'] }}</a>
                        </td>
                      @endif
                    
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      @endif
      @if(!empty($collectionamt))
        <div class="col-xs-6">
          <div class="box dashboard-list-box">
            <div class="box-header dash-header">
              <h3 class="box-title">Latest Collections</h3>
              <div class="box-tools pull-right">
                <a href="{{ domain_route('company.admin.collection') }}" class="btn btn-default btn-sm">View All Collections</a>
              
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
                    <tr>
                      <td><b>{{ ucwords(strtolower(getClient($recentcollection['client_id'])['company_name'] )) }}</b>
                        <br>
                        @if($recentcollection['employee_type'] == 'Admin')
                          {{ Auth::user()->name.' (Admin)' }}
                        @elseif($recentcollection['employee_type'] == 'Employee')
                          {{ ucwords(strtolower(getEmployee($recentcollection['employee_id'])['name'])) }}
                        @endif / {{ getDeltaDate(date('Y-m-d',strtotime($recentcollection['payment_date']))) }}
                      </td>
                      <td>{{ $recentcollection['payment_received'] }}</td>
                      <td>{{ getBankName($recentcollection['bank_id'])['name'] }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>
  </section>
  <!-- /.content -->
@endsection



@section('scripts')
@endsection