<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" aria-hidden="true">&times;</button>
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
        <span id="activityexports" class="pull-right"></span>
      </div>
      <div class="box-body @if($activities->count()>0) table-fix @endif ">
        <table id="activity" class="table table-bcollectioned table-striped">
          
          @if( $activities->count()>0)
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Title</th>
                <th>Party</th>
                <th>Type</th>
                <th>Assigned By</th>
                <th>Assigned To</th>
                <th>Is it Complete?</th>
                <th style="min-width: 60px;">Action</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          @else
            <thead style="margin-top:0;">
              <tr>
                <td colspan="10">No Record Found.</td>
              </tr>
            </thead>
          @endif
        </table>

      </div>
    </div>
  </div>
</div>
<div class="modal modal-default fade" id="alertPartyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Sorry! You are not authorized to view this party details.
        </p>
        <input type="hidden" name="expense_id" id="c_id" value="">
        <input type="text" id="accountType" name="account_type" hidden />
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal modal-default fade" id="alertCompleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Sorry! Only Assignor or Assignee can mark activity as <span id="textComplete">complete</span>.
        </p>
        <input type="hidden" name="expense_id" id="c_id" value="">
        <input type="text" id="accountType" name="account_type" hidden />
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>