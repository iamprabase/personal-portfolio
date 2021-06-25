<div class="box-body">
  <form id="UpdateProfileDetail">
    <div class="row">
      <span id="ActivateEdit" class="btn btn-default btn-sm pull-right" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
      <span id="ActivateCancel" class="btn btn-default btn-sm pull-right hide" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
      <span id="ActivateUpdate" class="hide"><button class="btn btn-default btn-sm pull-right" type="submit"><i class="fa fa-edit"></i>Update</button></span>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-user-o icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Name</h4>
            <input type="text" name="employee_id" value="{{$employee->id}}" hidden>
            <p id="empName" class="text-display">{{ ($employee->name)?$employee->name:'NA' }}</p>
            <p class="text-form" hidden><input name="employee_name" class="form-control" type="text" value="{{ ($employee->name)?$employee->name:'NA' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-qrcode icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Employee Code</h4>
            <p id="empCode" class="text-display">{{ ($employee->employee_code)?$employee->employee_code:'NA' }}</p>
            <p class="text-form" hidden><input name="employee_code" class="form-control" type="text" value="{{ ($employee->employee_code)?$employee->employee_code:'NA' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-mobile icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Mobile No.</h4>
            <p id="empMob" class="text-display">{{ ($employee->phone)? '+'.$employee->country_code.'-'.$employee->phone:'NA' }}</p>
            <p class="text-form" hidden><input name="phone" class="form-control" type="text" value="{{ ($employee->phone)? $employee->phone:'NA' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-envelope-o icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Email</h4>
            <p id="empEmail" class="text-display">{{ ($employee->email)?$employee->email:'NA' }}</p>
            <p class="text-form" hidden><input name="email" class="form-control" type="email" value="{{ ($employee->email)?$employee->email:'NA' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-calendar  icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Birth Date</h4>
            <p id="empDOB" class="text-display">{{ ($employee->b_date)?getDeltaDate(date('Y-m-d', strtotime($employee->b_date))):'N/A' }}</p>
            <p class="text-form" hidden><input autocomplete="off" id="empDOBBox" name="dob" class="form-control" type="text" value="{{ ($employee->b_date)?getDeltaDateFormat(date('Y-m-d', strtotime($employee->b_date))):'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-venus-mars icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Gender</h4>
            <p id="empGender" class="text-display">{{ ($employee->gender)?$employee->gender:'NA' }}</p>
            <p class="text-form" hidden>
              <select name="gender" class="form-control">
                <option value="Male" @if($employee->gender=="Male") selected="selected" @endif>Male</option>
                <option value="Female"  @if($employee->gender=="Female") selected="selected" @endif>Female</option>
              </select>
            </p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-map-marker  icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Local Address</h4>
            <p id="empLAdd" class="text-display">{{ ($employee->local_add)?$employee->local_add:'NA' }}</p>
            <p class="text-form" hidden><input name="local_address" class="form-control" type="text" value="{{ ($employee->local_add)?$employee->local_add:'NA' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-map-marker icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Permanent Address</h4>
            <p id="empPAdd" class="text-display">{{ ($employee->per_add)?$employee->per_add:'NA' }}</p>
            <p class="text-form" hidden><input name="permanent_address" class="form-control" type="text" value="{{ ($employee->per_add)?$employee->per_add:'NA' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-check-square-o icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Status</h4>
            <p id="empStatus" class="text-display">{{ ($employee->status)?$employee->status:'NA' }}</p>
            <p class="text-form" hidden>
              <select name="status" class="form-control">
                <option value="Active" @if($employee->status=='Active') selected="selected" @endif>Active</option>
                <option value="Inactive"  @if($employee->status=='Inactive') selected="selected" @endif>Inactive</option>
              </select>
            </p>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>