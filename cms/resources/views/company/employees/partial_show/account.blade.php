<div class="box-body">
  <form id="UpdateAccountDetail">
    <div class="row">
      @if(Auth::user()->isCompanyEmployee() && Auth::user()->EmployeeId()==$employee->id)
      @elseif(!($isManager=='true') && $employee->is_owner==1)
      @else
      @if(Auth::user()->can('employee-update'))
      <span id="ActivateAccountEdit" class="btn btn-default btn-sm pull-right" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
      <span id="ActivateAccountCancel" class="btn btn-default btn-sm pull-right hide" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
      <span id="ActivateAccountUpdate" class="hide"><button style="margin-right: 10px;" class="btn btn-default btn-sm pull-right" type="submit"><i class="fa fa-edit"></i>Update</button></span>
      @endif
      @endif
    </div>
    {{-- @if(Auth::user()->employee->is_admin==1 && $is_logged_in)
    <div class="row">
      <h4 class="text-center logged_in"><b>User is currently logged in.</b><a href='#' class="power-off" aria-hidden="true" data-empid="{{$employee->id}}">Log user out?</a> </h4>
    </div>
    @endif --}}
    <div class="row">
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-mobile icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Mobile No.</h4>
            <p id="empMob" class="text-display">{{ ($employee->phone)? '+'.$employee->country_code.'-'.$employee->phone:'N/A' }}</p>
            <div class="text-form" hidden>
              <div class="row">
                <div class="col-xs-4">
                  <select name="country_code" class="form-control select2" >
                    @foreach($country_codes as $country_code)
                      <option value="{{$country_code->phonecode}}" {{isset($employee)? (($employee->country_code == $country_code->phonecode)? 'selected=selected':''):(($country_code->phonecode == $setting->phonecode)? 'selected=selected':'')}}>{{$country_code->name}}
                        , +{{$country_code->phonecode}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-xs-8">
                  <input name="phone" class="form-control" type="text" value="{{ ($employee->phone)? $employee->phone:'' }}" /></div>
              </div>
              
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-at icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Email</h4>
            <input type="text" name="employee_id" value="{{$employee->id}}" hidden>
            <p id="empEmail" class="text-display">{{ ($employee->email)?$employee->email:'NA' }}</p>
            <p class="text-form" hidden><input name="email" class="form-control" type="text" value="{{ ($employee->email)?$employee->email:'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-key icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Password</h4>
            <p id="empPassword" class="text-display">{{ ($employee->password)?'*********':'NA' }}</p>
            <p class="text-form" hidden><input name="password" class="form-control" type="password" value="{{($employee->password)?$employee->password:''}}" /></p>
          </div>
        </div>
      </div>
      @if(Auth::user()->employee->is_admin==1 && $is_logged_in)
      <div class="col-xs-6">
        <div class="row">
          <p class="logged_in"><b>User is currently logged in.</b><a href='#' class="power-off" aria-hidden="true"
              data-empid="{{$employee->id}}"> Log user out?</a> </p>
        </div>
      </div>
      @endif
    </div>
  </form>
</div>