<section class="content">
  <div class="row">
    <div class="col-xs-12">
      @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
      @endif

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Custom Rate Setup</h3>
          @if(Auth::user()->can('party_wise_rate_setup-create'))<a class="btn btn-primary pull-right" style="margin-left: 5px;" data-toggle="modal"
            data-target="#addRateModal"> <i class="fa fa-plus"></i> Create New </a>@endif
          <span id="rates_export" class="pull-right"></span>
        </div>
        <!-- /.box-header -->

        <div class="box-body">
          <table id="rates_table" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>

<div class="modal fade" id="addRateModal" tabindex="-1" role="dialog">
  <form id="add_new_rate" method="post" action="{{domain_route('company.admin.add_new_rate.store')}}">
    @csrf
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Add New Rate</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-2" style="text-align: right;">
              Name
            </div>
            <div class="col-xs-10">
              <input class="form-control rate_name" type="text" name="rate_name" required="">
              <span class="rate_name_err errlabel" style="color:red">
                <span></span>
              </span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button id="addRateBtn" type="submit" class="btn btn-primary">Create</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->

<div class="modal fade" id="deleteRateModal" tabindex="-1" role="dialog">
  <form id="delete_rate_form" method="post" action="{{domain_route('company.admin.orderstatus.delete')}}">
    @csrf
    <div class="modal-dialog small-modal" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" align="center">Deletion Confirmation</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div align="center">
                Are you sure you want to Delete Current Selected Rate ( <span id="rate_title"></span> ) ?
              </div>
              <input type="text" name="id" id="delete_id" hidden>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>
          <button id="delRateBtn" type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->