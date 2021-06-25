<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br/>
    @endif
    @if (\Session::has('alert'))
    <div class="alert alert-warning">
      <p>{{ \Session::get('alert') }}</p>
    </div><br/>           
    @endif
    <div class="box">
      <div class="box-header">
        <span id="grandTotalAmount"></span>
        <span id="orderexports" class="pull-right"></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body table-fix">
        <div id="mainBox">
          <table id="order" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Order No</th>
                <th>Order Date</th>
                <th>Created By</th>
                @if(getClientSetting()->order_with_amt==0)
                  <th>Grand Total</th>
                @endif
                <th>Order Status</th>
                <th>Action</th>
                <th style="display: none;">GrandTotal</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>