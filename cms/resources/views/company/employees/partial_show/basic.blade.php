<div class="box-body">
    <form id="UpdateProfileDetail">
    <div class="row">
      @if(Auth::user()->isCompanyEmployee() && Auth::user()->EmployeeId()==$employee->id)
      @elseif(!($isManager=='true') && $employee->is_owner==1)
      @else
      @if(Auth::user()->can('employee-update'))
      <span id="ActivateEdit" class="btn btn-default btn-sm pull-right" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
      <span id="ActivateCancel" class="btn btn-default btn-sm pull-right hide" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
      <span id="ActivateUpdate" class="hide"><button class="btn btn-default btn-sm pull-right" type="submit"><i class="fa fa-edit"></i>Update</button></span>
      @endif
      @endif
    </div>
    <div class="row">
      <div id="imggroup" class="form-group">
        <div class="col-xs-2 imgUp">  
            <div class="imagePreview imageExistsPreview"><img class="img-responsive display-imglists" @if(isset($employee->image_path)) src="{{ URL::asset('cms'.$employee->image_path) }}" @endif /></div>
            <label id="lblChange" class="btn btn-primary hide"> Change<input id="receipt" type="file" name="image" class="uploadFile img" value="cms/{{$employee->image_path}}" style="width: 0px;height: 0px;overflow: hidden;"></label>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-user-o icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Name</h4>
            <input type="text" name="employee_id" value="{{$employee->id}}" hidden>
            <p id="empName" class="text-display">{{ ($employee->name)?$employee->name:'N/A' }}</p>
            <p class="text-form" hidden><input name="employee_name" class="form-control" type="text" value="{{ ($employee->name)?$employee->name:'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-user-o icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Father/Spouse Name</h4>
            <p id="empFather" class="text-display">{{ ($employee->father_name)?$employee->father_name:'N/A' }}</p>
            <p class="text-form" hidden><input name="father_name" class="form-control" type="text" value="{{ ($employee->father_name)?$employee->father_name:'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-calendar  icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Birth Date</h4>
            <p id="empDOB" class="text-display">{{ ($employee->b_date)?getDeltaDate($employee->b_date):'N/A' }}</p>
            <p class="text-form" hidden><input autocomplete="off" id="empDOBBox" name="dob" class="form-control datepicker" type="text" value="{{ ($employee->b_date)?getDeltaDateFormat($employee->b_date):'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-venus-mars icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Gender</h4>
            <p id="empGender" class="text-display">{{ ($employee->gender)?$employee->gender:'N/A' }}</p>
            <p class="text-form" hidden>
              <select name="gender" class="form-control">
                <option value="Male" @if($employee->gender=="Male") selected="selected" @endif>Male</option>
                <option value="Female"  @if($employee->gender=="Female") selected="selected" @endif>Female</option>
              </select>
            </p>
          </div>
        </div>
      </div>
      @if(!$childExists)
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-check-square-o icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Status</h4>
            <p id="empStatus" class="text-display">{{ ($employee->status)?$employee->status:'N/A' }}</p>
            <p class="text-form" hidden>
              <select name="status" class="form-control">
                <option value="Active" @if($employee->status=='Active') selected="selected" @endif>Active</option>
                <option value="Inactive"  @if($employee->status=='Inactive') selected="selected" @endif>Inactive</option>
              </select>
            </p>
          </div>
        </div>
      </div>
      @endif

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-mobile icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Last Active Device</h4>
            <p class="text-display">{{ getObjectValue($employee,'device','N/A').'/'.getObjectValue($employee,'imei') }}</p>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>