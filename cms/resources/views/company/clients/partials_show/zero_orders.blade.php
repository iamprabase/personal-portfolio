<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
    <div class="alert alert-success">
      <p>{{ \Session::get('success') }}</p>
    </div><br />
    @endif
    @if (\Session::has('alert'))
    <div class="alert alert-warning">
      <p>{{ \Session::get('alert') }}</p>
    </div><br />
    @endif
    <div class="box">
      <div class="box-header">
        <span id="zeroOrdergrandTotalAmount"></span>
        <span id="zeroOrderexports" class="pull-right"></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body table-fix">
        <div id="mainBox">
          <table id="zero_order" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                {{-- <th>Contact Person Name</th>
                <th>Party Type</th>
                <th>Contact No.</th>
                <th>Address</th> --}}
                <th>Date</th>
                <th>Remark</th>
                <th>Added By</th>
                <th>Action</th>
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