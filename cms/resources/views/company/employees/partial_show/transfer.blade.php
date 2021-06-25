<div class="box-body">
  <form id="TransferUser">
    <div class="row">
      <div class="col-xs-12">
        <div class="alert alert-danger">Warning! After transfering, this user will lose all it's juniors and party handling information</div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-user icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">From</h4>
            <input type="text" name="employee_id" value="{{$employee->id}}" hidden>
            <p>{{ ($employee->name)?$employee->name:'NA' }}</p>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-user icon-size"></i>
          </div>
          <div class="media-body">
            <div class="row">
              <div class="col-xs-1">
                <h4 class="media-heading">To</h4>
              </div>
              <div class="col-xs-9">
                <select class="form-control select2" name="transfer_to">
                  @foreach($chainTransfer as $key => $user)
                    <optgroup label="{{$key}}"></optgroup>
                    @foreach($user as $u)
                      <option value="{{$u['id']}}">{{$u['emp_name']}}</option>
                    @endforeach
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>      
    </div><br>
    <div class="row">
      <div class="col-xs-3">
        <button class="btn btn-default" id="transfer" style="background-color: #0b7676;color:#fff!important;">Transfer</button>
      </div>
    </div>
  </form>
</div>