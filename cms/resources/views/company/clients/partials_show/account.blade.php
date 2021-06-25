<div class="box-body">

  @if(getClientSetting()->order_with_amt==0)
    <p class="text-muted"></p>
    <div class="row" style="margin-bottom: 15px;">
      <div class="col-xs-6">
        <div class="media left-list top-list">
          @if(getClientSetting()->order_with_amt==0)
          <div class="media-left">
            <i class="fa fa-user fa-money icon-size"></i>
          </div>
        
          <div class="media-body">
            <!-- <h4 class="media-heading">Outstanding Amount</h4> -->
            <h4 class="media-heading">{{$due_or_overdue_text}}</h4>
          </div>
          @endif
        </div>
        <div class="media left-list top-list">
          @if(getClientSetting()->order_with_amt==0)
          <div class="media-body">
            <h4 class="media-heading">  {{ getClientSetting()->currency_symbol}} <span @if(getClientSetting()->currency_symbol=="﷼")style="float:left;"@endif> {{ getClientSetting()->outstanding_amt_calculation == 0 ? number_format(($client->opening_balance + $tot_order_amount - $tot_collection_amount),2) : number_format(($client->due_amount),2) }}</span></h4>
          </div>
          @endif
        </div>
      </div>
      <div class="col-xs-6">
        <div class="media left-list top-list">
          @if(getClientSetting()->order_with_amt==0)
          {{-- <div class="media-left">
                          <i class="fa fa-user fa-money icon-size"></i>
                        </div> --}}
        
          <div class="media-body">
            <h4 class="media-heading">Credit Limit</h4>
          </div>
          @endif
        </div>
        <div class="media left-list top-list">
          @if(getClientSetting()->order_with_amt==0)
          <div class="media-body">
            <h4 class="media-heading">
              {{ getClientSetting()->currency_symbol}} {{$client->credit_limit?number_format($client->credit_limit,2):'NA'}}</h4>
          </div>
          @endif
        </div>
      </div>
    </div>
    <table class="table op-blnc" style="font-size: 12px;">
      <tr>
        <td>Opening Balance:</td>
        <td>{{ getClientSetting()->currency_symbol}} {{number_format($client->opening_balance, 2)}}</td>
      </tr>
      {{-- <tr>
        <td>Credit Limit:</td>
        <td>{{ getClientSetting()->currency_symbol}} {{number_format($client->credit_limit,2)}}</td>
      </tr> --}}
      <tr>
        <td>Order Amount:</td>
        <td>{{ getClientSetting()->currency_symbol}} {{number_format($tot_order_amount,2)}}</td>
      </tr>
      <tr>
        <td>Collection Amount:</td>
        <td>{{ getClientSetting()->currency_symbol}} {{number_format($tot_collection_amount,2)}}</td>
      </tr>
      {{-- <tr>
        <td>Current Due Amount:</td>
        <td>{{ getClientSetting()->currency_symbol}}. {{number_format(1200,2)}}</td>
      </tr> --}}
      {{-- <tfoot>
        <tr>
          <td>
            <div class="media left-list top-list">
              @if(getClientSetting()->order_with_amt==0)
              <div class="media-left">
                <i class="fa fa-user fa-money icon-size"></i>
              </div>
        
              <div class="media-body">
                <h4 class="media-heading">Outstanding Amount</h4>
              </div>
              @endif
            </div>
          </td>
          <td>
            <div class="media left-list top-list">
              @if(getClientSetting()->order_with_amt==0)
              <div class="media-body">
                <h4 class="media-heading">{{ getClientSetting()->currency_symbol}}
                  <span @if(getClientSetting()->currency_symbol=="﷼")style="float:left;"@endif>{{ number_format(($client->opening_balance + $tot_order_amount - $tot_collection_amount),2) }}</span></h4>
              </div>
              @endif
            </div>
          </td>
        </tr>
      </tfoot> --}}
    </table>
  @endif
  <br>
  <hr>

  <!-- <div class="row">
    <div class="col-sm-12">
      <h4 class="text-center">Order Table based on Due Date</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered table-hover table-sm" id="first-order-table">
        <thead class="bg-primary">
          <tr>
            <th>#</th>
            <th>Orders</th>
            <th>Order amount</th>
            <th>Order date</th>
            <th>Due by</th>
          </tr>
        </thead>
        <tbody id="first-order-table-body">

        </tbody>
        <tfoot>
          <tr>
            <th></th>
            <th>Total</th>
            <th id="total"></th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div> -->

  <!-- <hr> -->

  {{-- <div class="row">
    <div class="col-sm-12">
      <h4 class="text-center">Payment Table</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered table-hover table-sm" id="first-payment-table">
        <thead class="bg-primary">
          <tr>
            <th>Payments</th>
            <th>Amount</th>
            <th>Order No</th>
          </tr>
        </thead>
        <tbody id="first-payment-table-body">

        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <hr> --}}
  @if(activateAgeingPayment($client->client_type))
  @if(config('settings.ageing')==1 && Auth::user()->can('ageing-view') )

  <div class="row">
    <div class="col-xs-6">
      <h4 class="text-center">Overdue beyond credit days</h4>
    </div>
    <div class="col-xs-6">
      <h4 class="text-center">Upcoming Payments</h4>
    </div>
  </div>
  <br>

  <div class="row">
    <div class="col-xs-6">
      <table class="table no-margin table-striped table-bordered" id="first-order-credit-table">
        <thead class="bg-primary">
          <tr>
            <th>Orders</th>
            <th>Amount ({{config('settings.currency_symbol')}})</th>
            <th>Overdue Period (days)</th>
          </tr>
        </thead>
        <tbody id="first-order-credit-table-body">
  
        </tbody>
        <tfoot>
          <tr>
            <th>Total to be paid immediately</th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <div class="col-xs-6">
      <table class="table no-margin table-striped table-bordered" id="first-upcoming-payment-table">
        <thead class="bg-primary">
          <tr>
            <th>Orders</th>
            <th>Amount ({{config('settings.currency_symbol')}})</th>
            <th>To be paid by</th>
          </tr>
        </thead>
        <tbody id="first-upcoming-payment-table-body">
  
        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  
  <br/>

  <div class="row">
    <div class="col-xs-12">
      <b>Note:</b></br>
      *We have considered first order payment comes first rule.</br> 
      *Opening balance is not considered.
    </div>
  </div>

  @endif
  @endif

  {{-- <div class="row">
    <div class="col-sm-12">
      <h4 class="text-center">Upcoming Payments</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered table-hover table-sm" id="first-upcoming-payment-table">
        <thead class="bg-primary">
          <tr>
            <th>orders</th>
            <th>Amount</th>
            <th>To be paid by</th>
          </tr>
        </thead>
        <tbody id="first-upcoming-payment-table-body">
  
        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div> --}}
</div>

