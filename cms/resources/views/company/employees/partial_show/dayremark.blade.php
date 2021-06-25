<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success alert-dismissable" role="alert">
        <button type="button" class="close" aria-hidden="true">&times;</button>
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
        <span id="grandTotalCAmount"></span>
        <span id="dayremarksexports" class="pull-right"></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body table-fix">
        <div id="mainBox2">
          <table id="dayremarktbl" class="table table-bcollectioned table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Time</th>
                <th style="width:500px;">Remarks</th>
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