<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br/>
    @endif
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Current Balance Details</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="col-xs-8">
          <div class="table-responsive" id="">
            <table class="table table-bordered">
              <tbody>
              <tr>
                <th>Amount From Delivered Orders</th>
                <td>{{$order_amount}}</td>
              </tr>
              <tr>
                <th>Amount Collected</th>
                <td>{{$collection_amount}}</td>
              </tr>
              <tr>
                <th>Total Balance</th>
                <td>
                  @if($order_amount > $collection_amount)
                    {{$order_amount - $collection_amount}}
                  @elseif($order_amount < $collection_amount)
                    {{$collection_amount - $order_amount}}
                  @else
                    0
                  @endif
                </td>
              </tr>
              <tr>
                <th>Status</th>
                <td>
                  @if($order_amount > $collection_amount)
                    Remaining to Collect
                  @elseif($order_amount < $collection_amount)
                    Collected in Advance
                  @else
                    Opening Balance
                  @endif</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>