<div class="box-body">
	<div class="btn-group" role="group" id="clientgroups">
	@foreach($subClientsArray as $key => $value)
  @if(array_key_exists('name', $value))
    <a href="#{{$key}}" data-toggle="tab"><button id="pkey{{$key}}" value="{{$key}}" type="button" class="btn btn-primary">{{$value['name'][0]}}</button></a>
  @endif
  @endforeach
  </div>

    <div class="table-responsive" style="margin-top: 10px;">
      <div class="table">
        <table id="subclient" class="table table-bordered table-striped">

              <thead>
                
                <tr>

                  <th>#</th>

                  <th>Party Name</th>

                  <th>Phone</th>

                  <th>Mobile</th>

                  <th>Email</th>
                  
                  <th>Person Name</th>

                  <th>Status</th>

                  <th>Action</th>

                </tr>

              </thead>

              <tbody></tbody>

          </table>

      </div>
    </div>

  {{-- Modals --}}
  <div id="myPartyModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" id="changePartyStatus" method="POST"
                action="#">
            {{csrf_field()}}
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="client_id" id="client_id" value="">
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Status</label>
              <div class="col-sm-10">
                <select class="form-control" id="status" name="status">
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn actionBtn">
                <span id="footer_action_button" class='glyphicon'> </span> Change
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>