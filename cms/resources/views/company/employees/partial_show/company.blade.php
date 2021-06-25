<div class="box-body">
  <form id="UpdateCompanyDetail">
    <div class="row">
      @if(Auth::user()->isCompanyEmployee() && Auth::user()->EmployeeId()==$employee->id)
      @elseif(!($isManager=='true') && $employee->is_owner==1)
      @else
      @if(Auth::user()->can('employee-update'))
      <span id="ActivateCompanyEdit" class="btn btn-default btn-sm pull-right" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
      <span id="ActivateCompanyCancel" class="btn btn-default btn-sm pull-right hide" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
      <span id="ActivateCompanyUpdate" class="hide" ><button style="margin-right: 10px;" class="btn btn-default btn-sm pull-right" type="submit"><i class="fa fa-edit"></i>Update</button></span>
      @endif
      @endif
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-qrcode icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Employee Code</h4>
            <input type="text" name="employee_id" value="{{$employee->id}}" hidden>
            <p id="empCode" class="text-display">{{ ($employee->employee_code)?$employee->employee_code:'N/A' }}</p>
            <p class="text-form" hidden><input name="employee_code" class="form-control" type="text" value="{{ ($employee->employee_code)?$employee->employee_code:'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-users icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Employee Group</h4>
            <p id="empGroup" class="text-display">{{isset($employee->employeegroup)? (( getEmployeeGroup($employee->employeegroup)['status'] == 'Active' )? getEmployeeGroup($employee->employeegroup)['name']:'N/A'):'N/A' }}</p>
            <p class="text-form" hidden>
              <select name="employee_group" class="form-control select2">
                <option>Select Employee Group</option>
                @foreach($employeegroups as $empgroup)
                  <option value="{{$empgroup->id}}" @if($empgroup->id==$employee->employeegroup) selected="selected" @endif>{{$empgroup->name}}</option>
                @endforeach              
              </select>
            </p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-user-secret icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Designation</h4>
            <p id="empDesignation" class="text-display">{{ ($employee->designation)?$employee->designation_name:'N/A' }}</p>
            <p class="text-form" hidden>
              @if($childExists==true || isset($employee->superior))
              {!! Form::select('designation', $designations, isset($employee)? $employee->designation:'',  ['placeholder' => 'Select a designation...', 'class' => 'form-control select2','required','disabled']) !!}
              @else
              {!! Form::select('designation', $designations, isset($employee)? $employee->designation:'',  ['placeholder' => 'Select a designation...', 'class' => 'form-control select2','required','disabled']) !!}
              @endif
            </p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-user-secret icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Superior</h4>
            <p id="empSuperior" class="text-display">{{ ($employee->superior)?$superior_name:'N/A' }}</p>
            <p class="text-form" hidden>
              {!! Form::select('superior', [null => 'Select Superior'] + $superiors, isset($employee->superior)?$employee->superior:null, ["class" => "form-control select2", "id" => "superior","disabled"]) !!}
            </p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-money icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Accessibility</h4>
            <p id="empRole" class="text-display">@if($employee->role) {{$role_name}} @else 'N/A' @endif</p>
            <p class="text-form" hidden>
              <select name="role" class="form-control select2" @if(!(Auth::user()->can('role-update')) || $employee->is_owner==1) disabled @endif>
              @if(isset($employee->role))
                @foreach($roles as $role)
                  <option @if($employee->role==$role->id) selected="selected" @endif value='{{$role->id}}'>{{$role->name}}</option>
                @endforeach
              @else
              @foreach($roles as $role)
                  <option value='{{$role->id}}'>{{$role->name}}</option>
              @endforeach
              @endif
              </select>
            </p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-money icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Total Salary</h4>
            <p id="empTS" class="text-display">{{ config('settings.currency_symbol')}} {{ ($employee->total_salary)?number_format((float)$employee->total_salary,2):'N/A' }}</p>
            <p class="text-form" hidden><input name="total_salary" class="form-control" type="text" value="{{ ($employee->total_salary)?$employee->total_salary:'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-calendar icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Permitted Leaves</h4>
            <p id="empPL" class="text-display">{{ ($employee->permitted_leave)?$employee->permitted_leave:'N/A' }}</p>
            <p class="text-form" hidden><input name="permitted_leave" class="form-control" type="text" value="{{ ($employee->permitted_leave)?$employee->permitted_leave:'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-calendar icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Date of Joining</h4>

            <p id="empDOJ" class="text-display">{{ ($employee->doj)?getDeltaDate($employee->doj):'N/A' }}</p>
            <p class="text-form" hidden><input name="doj" id="doj" class="form-control datepicker" autocomplete="off" type="text" value="{{ ($employee->doj)?getDeltaDateFormat(date('Y-m-d', strtotime($employee->doj))):'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-calendar icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Last Working Date</h4>
            <p id="empLWD" class="text-display">{{ ($employee->lwd)?getDeltaDate($employee->lwd):'N/A' }}</p>
            <p class="text-form" hidden><input name="lwd" id="lwd" autocomplete="off" class="form-control datepicker" type="text" value="{{ ($employee->lwd)?getDeltaDateFormat(date('Y-m-d', strtotime($employee->lwd))):'' }}" /></p>
          </div>
        </div>
      </div>

    </div>
  </form>
  @if(isset($childExists))
  <div class="row">
    <div class="col-xs-12">
      <h4 class="text-success">Employee Hierarchy</h4>
      <ul id="tree1">
        <li>
          <p data-id="{{$employee->id}}" class="btn btn-sm button-blue">
              {{$employee->name}} ( {{(isset($employee->designations->name)?$employee->designations->name:'')}} )
            </p>
        </li>
        <ul>
        @foreach($juniors as $junior)
          <li>
            <a data-id="{{$junior->id}}" class="btn btn-sm button-blue">
              {{$junior->employee_name}} ( {{$junior->designation_name}} )
            </a>
            @if(count($junior->childs))
              @include('company.employees.partial_show.manageEmployeeChild',['childs' => $junior->childs])
            @endif
          </li>
        @endforeach     
        </ul>
      </ul>
    </div>
  </div>
  @endif
</div>