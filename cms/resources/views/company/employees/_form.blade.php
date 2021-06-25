<div class="row">
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Basic Info</div>
      <div class="panel-body">
        <div class="form-group @if ($errors->has('name')) has-error @endif" id="alertEmpName">
          {!! Form::label('name', 'Name') !!}<span style="color: red">*</span>
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'EMPLOYEE NAME','required','id'=>'empName']) !!}
          @if(isset($employee->id))
          <input type="text" name="id" value="{{$employee->id}}" hidden> 
          @endif
          @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('b_date')) has-error @endif">
          {!! Form::label('b_date', 'Birth Date') !!}
          @if(config('settings.ncal')==1)
          <input type="text" id="englishDate" name="englishDate" @if(isset($employee->b_date))
          value="{{$employee->b_date}}" @endif hidden />
          @endif
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('b_date', null, ['class' => 'form-control pull-right', 'id' => 'b_date',
            'autocomplete'=>'off', 'placeholder' => 'BIRTH DATE']) !!}
          </div>
          @if ($errors->has('b_date')) <p class="help-block has-error">{{ $errors->first('b_date') }}</p> @endif
        </div>

        <div class="form-group" style="margin-bottom: 30px;">
          {!! Form::label('gender', 'Gender') !!}
          <div>
            <label style="margin-right: 10px;">
              {{ Form::radio('gender', 'Male', old('gender')=="Male" ? 'true=' : 'true' ,['class'=>'minimal']) }}
              Male
            </label>
            <label>
              {{ Form::radio('gender', 'Female', old('gender')=="Female" ? 'true=' : '' ,['class'=>'minimal']) }}
              Female
            </label>
          </div>
        </div>

        <div class="form-group @if ($errors->has('father_name')) has-error @endif">
          {!! Form::label('father_name', "Father/Spouse Name") !!}
          {!! Form::text('father_name', null, ['class' => 'form-control', 'placeholder' => 'FATHER\' NAME/ SPOUSE NAME']) !!}
          @if ($errors->has('father_name')) <p class="help-block has-error">{{ $errors->first('father_name') }}</p>
          @endif
        </div>

        <div class="form-group @if ($errors->has('image')) has-error @endif">
          <label>Photo</label>
          <small> Size of image should not be more than 2MB.</small>
          <div class="input-group" style="margin-bottom:10px;">
            <span class="input-group-btn">
              <span class="btn btn-default btn-file imagefile">
                Browse… {!! Form::file('image', ['id'=>'imgInp']) !!}
              </span>
            </span>
            <input type="text" class="form-control" readonly>
          </div>

          <img id='img-upload' class="img-responsive" src="@if(isset($employee->image_path)) {{ URL::asset('cms/'.$employee->image_path) }} @endif" />
        </div>
        
        @if(Auth::user()->can('employee-status'))
        @if(!$childExists)
        <div class="form-group">
          {!! Form::label('status', 'Status') !!}
          @if(isset($employee->status))
          @if($employee->status == 'Archived')
          {!! Form::select('status', array('Archived' => 'Archived'), $employee->status, ['class' => 'form-control',
          'disabled' ,'id'=>'status']) !!}
          @else
          {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $employee->status, ['class'
          => 'form-control','id'=>'status']) !!}
          @endif
          @else
          {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), 'Active', ['class' =>
          'form-control','id'=>'status']) !!}
          @endif
          <small id="archive-warning" style="display: none; color: red">Warning: Archived users cannot be active again
          </small>
        </div>
        @else
        <input name="status" type="hidden" value="Active">
        @endif
        @endif
      </div>
    </div>
  </div>

  {{-- Account section --}}
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Account Login</div>
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group @if ($errors->has('country_code')) has-error @endif">
              <label for="country_code">Phone Code</label><span style="color: red">*</span>
              <select name="country_code" class="form-control select2" required>
                @foreach($country_codes as $country_code)
                <option value="{{$country_code->phonecode}}"
                  {{isset($employee)? (($employee->country_code == $country_code->phonecode)? 'selected=selected':''):(($country_code->phonecode == $setting->phonecode)? 'selected=selected':'')}}>
                  {{$country_code->name}}
                  , +{{$country_code->phonecode}}</option>
                @endforeach
              </select>
              @if ($errors->has('country_code')) <p class="help-block has-error">{{ $errors->first('country_code') }}
              </p> @endif
            </div>
          </div>
          <div class="col-sm-8">
            <div class="form-group @if ($errors->has('phone')) has-error @endif" id="alertPhoneNo">
              {!! Form::label('phone', 'Primary Phone No.') !!}<span style="color: red">*</span>
              {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'PHONE NO.','required','id'=>'empPhone']) !!}
              @if ($errors->has('phone')) <p class="help-block has-error">{{ $errors->first('phone') }}</p> @endif
            </div>
          </div>
        </div>

        <div class="form-group @if ($errors->has('email')) has-error @endif" id="alertEmail">

          {!! Form::label('email', 'Email:') !!}<span style="color: green;"> (If email id is not filled, mobile number can be used for login.) </span>

          {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'EMAIL','id'=>'empEmail']) !!}

          @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif

        </div>

        <div class="form-group @if ($errors->has('password')) has-error @endif" id="alertPassword">

          {!! Form::label('password', 'Password') !!}<span style="color: red;">*</span><span style="color:green;">(minimum 8 character required)</span>
          <input type="password" value="{{(isset($employee->password))?$employee->password:""}}" class="form-control" placeholder="PASSWORD" name="password" id="empPassword" />
          @if ($errors->has('password')) <p class="help-block has-error">{{ $errors->first('password') }}</p> @endif

        </div>

        <div class="form-group @if ($errors->has('c_password')) has-error @endif" id="alertConfirmPassword">

          {!! Form::label('c_password', 'Confirm Password') !!}<span style="color: red;">*</span>
          <input type="password" value="{{(isset($employee->password))?$employee->password:""}}" class="form-control" placeholder="PASSWORD" name="c_password" id="empConfirmPassword" />
          @if ($errors->has('c_password')) <p class="help-block has-error">{{ $errors->first('c_password') }}</p> @endif

        </div>

      </div>
    </div>
  </div>
  {{-- Account Ending --}}
</div>
<div class="row">
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Company Details</div>
      <div class="panel-body">
        <div class="form-group @if ($errors->has('employee_code')) has-error @endif" id="alertEmpCode">
          {!! Form::label('employee_code', 'Employee Code ') !!}
          <small>A unique code for individual employee</small>

          {!! Form::text('employee_code', null, ['class' => 'form-control', 'placeholder' => 'A UNIQUE CODE FOR EMPLOYEE','id'=>'empCode']) !!}

          @if ($errors->has('employee_code')) <p class="help-block has-error">{{ $errors->first('employee_code') }}</p>
          @endif
        </div>

        <div class="form-group @if($errors->has('role')) has-error @endif" id="alertRole">
          {!! Form::label('role', 'Accessibility') !!}<span style="color: red">*</span>
          <select name="role" class="form-control select2" @if(!(Auth::user()->can('role-update')) || (isset($isManager) && $isManager=="true") ) disabled @endif >
          @if(isset($employee->role))
            @foreach($roles as $role)
              <option @if($employee->role==$role->id) selected="selected" @endif value='{{$role->id}}'>{{$role->name}}</option>
            @endforeach
          @else
          @foreach($roles as $role)
              <option @if($role->name=='Limited Access') selected='selected' @endif value='{{$role->id}}'>{{$role->name}}</option>
          @endforeach
          @endif
          </select>
        </div>

        <div class="form-group">
          <div class="form-group @if ($errors->has('designation')) has-error @endif" id="alertDesignation">
            {!! Form::label('designation', 'Designation') !!}<span style="color: red;">*</span><span style="color: green;"> (Add designation from setting.) </span>
            @if(isset($employee) && $employee->is_admin==1)
            <input type="text" name="designation" value="{{$employee->designation}}" hidden>
            <select class="form-control" disabled="disabled"><option>Admin</option></select>
            @else
              @if(isset($childExists))
              <input type="text" name="designation" value="{{$employee->designation}}" hidden/>
              <select name="designation" class="form-control select2" disabled="disabled">
              @else
              <select name="designation" class="form-control select2" id="designation">
              @endif
              <option val="null">Select Designation</option>
              @if(isset($employee->designation))
                @if(isset($empDesignationName) && Auth::user()->isCompanyEmployee() &&
                Auth::user()->EmployeeId()==$employee->id )
                  <option>{{$empDesignationName}}</option>
                @endif

                @foreach($designations as $designation)
                  <option @if($designation->id==$employee->designation) selected="selected" @endif
                  value="{{$designation->id}}">{{$designation->name}}</option>
                @endforeach
              @else
                @foreach($designations as $designation)
                  <option value="{{$designation->id}}">{{$designation->name}}</option>
                @endforeach
              @endif
            </select>
            @endif

            @if ($errors->has('designation'))
            <p class="help-block has-error">{{ $errors->first('designation') }}</p> @endif
          </div>
        </div>        

        <div class="form-group @if ($errors->has('superior')) has-error @endif">
          {!! Form::label('Superior', 'Superior') !!}<span id="spanSuperior" style="color: red;">*</span>
          @if(isset($employee) && $employee->is_admin==1)
            <select class="form-control" disabled="disabled"><option></option></select>
          @else
            @if(isset($childExists))
              <select class="form-control select2" name="superior" id="superior" disabled="disabled" >
            @else
              <select class="form-control select2" name="superior" id="superior" >
            @endif
            </select> 
          @endif
          @if ($errors->has('superior')) <p class="help-block has-error">{{ $errors->first('superior') }}</p> @endif
        </div>

        <div class="form-group hide" id="designationchoice">
          {!! Form::label('designationchoice', 'Promote Option') !!}
          <div class="row">
              <div class="col-xs-6">
                <div class="radio">
                  <label><input type="radio" name="promoteOption" checked value="null">New</label>
                </div>
              </div>
              <div class="col-xs-6">
                <div class="radio">
                  <label><input type="radio" name="promoteOption" value="replace">Replace</label>
                </div>                
              </div>
          </div>
          <div class="form-group hide" id="existingUsers">
            {!! Form::label('ExistingUsers', 'Existing Users') !!}
            <select name="existingusers" class="form-control select2" id="eusers"></select>
          </div>
        </div>

        @if(config('settings.party')==1)
        <div class="form-group @if ($errors->has('clients')) has-error @endif" id="partyAssignforms">
          <label for="clients">Assign Parties</label>
          <br />
          <span><label for="list_type" style="font-size:12px;">Party Listing Type:-</label>
            <label style="padding-left: 5px;font-size:12px;">
              {{ Form::radio('list_type', '0' , true ,['class'=>'list_type']) }}
              Beat Wise
            </label>
            <label style="padding-left: 5px;font-size:12px;">
              {{ Form::radio('list_type', '1', false, ['class'=>'list_type']) }}
              Party Type Wise
            </label>
          </span>
          <label class="nonePartyAssigned hidden" style="color: #f59600 !important;">No parties assigned to the superior.</label>
          @if(getClientSetting()->beat==1)
          <select name="clients[]" id="employeeId-enableClickableOptGroups" multiple="multiple" class="form-control">
            @if(isset($handles))
              @foreach($beats as $beat)
              <optgroup label="{{ $beat['name']}}" value="{{ $beat['id']}}"
                {{ (((isset($employee->user_id)) && Auth::user()->id==$employee->user_id) || $employee->is_admin==1)?'disabled':'' }}>
                @foreach($beat['clients'] as $key => $value)
                <option @if(in_array($key, $handles)) selected="selected" @endif value="{{ $key }}" @if(in_array($key, json_decode($getJuniorParties))) disabled @endif>{{ $value }}</option>
                @endforeach
              </optgroup>
              @endforeach
              @else
              @foreach($beats as $beat)
              <optgroup label="{{ $beat['name']}}" value="{{ $beat['id']}}"
                {{ ((isset($employee->user_id)) && Auth::user()->id==$employee->user_id)?'disabled':'' }}>
                @foreach($beat['clients'] as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </optgroup>
              @endforeach
            @endif
          </select>
          @else
          <select name="clients[]" id="employeeId-enableClickableOptGroups" multiple="multiple" class="form-control">
            @if(isset($handles))
              @foreach($beats as $beat)
                @foreach($beat['clients'] as $key => $value)
                <option @if(in_array($key, $handles)) selected="selected" @endif value="{{ $key }}" @if($employee->is_admin==1) disabled @elseif(in_array($key, json_decode($getJuniorParties))) disabled @endif>{{ $value }}</option>
                @endforeach
              @endforeach
            @else
              @foreach($beats as $beat)
                @foreach($beat['clients'] as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              @endforeach
            @endif
          </select>
          @endif
        </div>
        <input type="hidden" id="beatId-optGroups" type="hidden" name="beatIds">
        @endif

        <div class="form-group @if ($errors->has('employeegroup')) has-error @endif" id="alertEmpGroup">
          {!! Form::label('employeegroup', 'Employee Group') !!}
          {!! Form::select('employeegroup', $employeegroups, isset($employee)? $employee->employeegroup:1,['placeholder' => 'Select a group...', 'class' => 'form-control select2','id'=>'empGroup']) !!}
          @if ($errors->has('employeegroup')) <p class="help-block has-error">{{ $errors->first('employeegroup') }}</p>
          @endif
        </div>

        <div class="form-group @if ($errors->has('total_salary')) has-error @endif">
          {!! Form::label('total_salary', 'Total Salary ') !!}
          {!! Form::text('total_salary', null, ['class' => 'form-control', 'placeholder' => 'TOTAL SALARY']) !!}
          @if ($errors->has('total_salary')) <p class="help-block has-error">{{ $errors->first('total_salary') }}</p>
          @endif
        </div>

        <div class="form-group @if ($errors->has('permitted_leave')) has-error @endif">
          {!! Form::label('permitted_leave', 'Permitted Leaves ') !!}
          {!! Form::text('permitted_leave', null, ['class' => 'form-control', 'placeholder' => 'PERMITTED LEAVE']) !!}
          @if ($errors->has('permitted_leave')) <p class="help-block has-error">{{ $errors->first('permitted_leave') }}
          </p> @endif
        </div>

        <div class="form-group @if ($errors->has('doj')) has-error @endif">
          @if(config('settings.ncal')==1)
          <input type="text" id="englishDoj" name="englishDoj" @if(isset($employee->doj)) value="{{$employee->doj}}"
          @endif hidden />
          @endif
          {!! Form::label('doj', 'Date of Joining ') !!}
          {!! Form::text('doj', (isset($employee->doj)?getDeltaDateFormat($employee->doj):null), ['class' =>'form-control', 'placeholder' => 'DATE OF JOINING','id'=>'doj','autocomplete'=>'off']) !!}
          @if ($errors->has('doj')) <p class="help-block has-error">{{ $errors->first('doj') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('lwd')) has-error @endif">
          @if(config('settings.ncal')==1)
          <input type="text" id="englishLwd" name="englishLwd" @if(isset($employee->lwd)) value="{{$employee->lwd}}"
          @endif hidden />
          @endif
          {!! Form::label('lwd', 'Last Working Date ') !!}
          {!! Form::text('lwd', (isset($employee->lwd)?getDeltaDateFormat($employee->lwd):null), ['class' =>'form-control', 'placeholder' => 'LAST WORKING DATE','id'=>'lwd','autocomplete'=>'off']) !!}
          @if ($errors->has('lwd')) <p class="help-block has-error">{{ $errors->first('lwd') }}</p> @endif
        </div>
      </div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Contact Details</div>
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-4">
            <div class="form-group @if ($errors->has('country_code')) has-error @endif">
              <label for="country_code">Phone Code</label>
              <select name="alt_country_code" class="form-control select2">
                @foreach($country_codes as $country_code)
                <option value="{{$country_code->phonecode}}"
                  {{isset($employee)? (($employee->alt_country_code == $country_code->phonecode)? 'selected=selected':''):(($country_code->phonecode == $setting->phonecode)? 'selected=selected':'')}}>
                  {{$country_code->name}}
                  , +{{$country_code->phonecode}}</option>
                @endforeach
              </select>
              @if ($errors->has('country_code')) <p class="help-block has-error">{{ $errors->first('country_code') }}
              </p> @endif
            </div>
          </div>
          <div class="col-sm-8">
            <div class="form-group @if ($errors->has('a_phone')) has-error @endif">
              {!! Form::label('a_phone', 'Alternate Phone No.') !!}
              {!! Form::text('a_phone', null, ['class' => 'form-control', 'placeholder' => 'ALTERNATE PHONE NO.']) !!}
              @if ($errors->has('a_phone')) <p class="help-block has-error">{{ $errors->first('a_phone') }}</p> @endif
            </div>
          </div>
        </div>
        <div class="form-group @if ($errors->has('local_add')) has-error @endif">

          {!! Form::label('local_add', 'Local Address') !!}

          {!! Form::text('local_add', null, ['class' => 'form-control', 'id=local_add', 'placeholder' => 'LOCAL ADDRESS']) !!}

          @if ($errors->has('local_add')) <p class="help-block has-error">{{ $errors->first('local_add') }}</p> @endif

        </div>
        <div class="form-group @if ($errors->has('per_add')) has-error @endif">

          {!! Form::label('per_add', 'Permanent Address') !!}

          {!! Form::text('per_add', null, ['class' => 'form-control', 'id=per_add', 'placeholder' => 'PERMANENT ADDRESS']) !!}

          @if ($errors->has('per_add')) <p class="help-block has-error">{{ $errors->first('per_add') }}</p> @endif

        </div>
        <h4 style="color:#0b7676;">Emergency Contact Detail</h4>
        <div class="form-group @if ($errors->has('e_name')) has-error @endif">
          {!! Form::label('e_name', 'Name') !!}
          {!! Form::text('e_name', null, ['class' => 'form-control', 'id=e_name', 'placeholder' => 'NAME']) !!}
          @if ($errors->has('e_name')) <p class="help-block has-error">{{ $errors->first('e_name') }}</p> @endif
        </div>
        <div class="form-group @if ($errors->has('e_relation')) has-error @endif">
          {!! Form::label('e_relation', 'Relation to you(Example:Spouse,Father,Mother etc.)') !!}
          {!! Form::text('e_relation', null, ['class' => 'form-control', 'id=e_relation', 'placeholder' => 'RELATION TO YOU']) !!}
          @if ($errors->has('e_relation')) <p class="help-block has-error">{{ $errors->first('e_relation') }}</p> @endif
        </div>
        <div class="form-group @if ($errors->has('e_phone')) has-error @endif">
          <label for="country_code">Phone Code</label>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group @if ($errors->has('country_code')) has-error @endif">
                <select name="e_country_code" class="form-control select2" required>
                  @foreach($country_codes as $country_code)
                  <option value="{{$country_code->phonecode}}"
                    {{isset($employee)? (($employee->e_country_code == $country_code->phonecode)? 'selected=selected':''):(($country_code->phonecode == $setting->phonecode)? 'selected=selected':'')}}>
                    {{$country_code->name}}
                    , +{{$country_code->phonecode}}</option>
                  @endforeach
                </select>
                @if ($errors->has('country_code')) <p class="help-block has-error">{{ $errors->first('country_code') }}
                </p> @endif
              </div>
            </div>
            <div class="col-sm-8">
              <div class="form-group @if ($errors->has('e_phone')) has-error @endif">
                {!! Form::text('e_phone', null, ['class' => 'form-control', 'placeholder' => 'CONTACT NO.']) !!}
                @if ($errors->has('e_phone')) <p class="help-block has-error">{{ $errors->first('e_phone') }}</p> @endif
              </div>
            </div>
          </div>
          @if ($errors->has('e_phone')) <p class="help-block has-error">{{ $errors->first('e_phone') }}</p> @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Bank Account Details</div>
      <div class="panel-body">
        <div class="form-group @if ($errors->has('acc_holder')) has-error @endif">
          {!! Form::label('acc_holder', 'Account Holder Name ') !!}
          {!! Form::text('acc_holder', null, ['class' => 'form-control', 'placeholder' => 'ACCOUNT HOLDER NAME']) !!}
          @if ($errors->has('acc_holder')) <p class="help-block has-error">{{ $errors->first('acc_holder') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('acc_number')) has-error @endif">
          {!! Form::label('acc_number', 'Account Number ') !!}
          {!! Form::text('acc_number', null, ['class' => 'form-control', 'placeholder' => 'ACCOUNT NUMBER']) !!}
          @if ($errors->has('acc_number')) <p class="help-block has-error">{{ $errors->first('acc_number') }}</p> @endif
        </div>

        <div class="form-group">
          <div class="form-group @if ($errors->has('bank_id')) has-error @endif">

            {!! Form::label('bank_id', 'Bank') !!}

            {!! Form::select('bank_id', $banks, isset($employee)? $employee->bank:'', ['placeholder' => 'Select a bank',
            'class' => 'form-control select2']) !!}

            @if ($errors->has('bank_id')) <p class="help-block has-error">{{ $errors->first('bank_id') }}</p> @endif
          </div>

        </div>

        <div class="form-group @if ($errors->has('ifsc_code')) has-error @endif">
          {!! Form::label('ifsc_code', 'IFSC Code ') !!}
          {!! Form::text('ifsc_code', null, ['class' => 'form-control', 'placeholder' => 'IFSC Code']) !!}
          @if ($errors->has('ifsc_code')) <p class="help-block has-error">{{ $errors->first('ifsc_code') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('pan')) has-error @endif">
          {!! Form::label('pan', 'PAN Number (Optional) ') !!}
          {!! Form::text('pan', null, ['class' => 'form-control', 'placeholder' => 'PAN Number']) !!}
          @if ($errors->has('pan')) <p class="help-block has-error">{{ $errors->first('pan') }}</p> @endif
        </div>

        <div class="form-group @if ($errors->has('pan')) has-error @endif">
          {!! Form::label('branch', 'Branch') !!}
          {!! Form::text('branch', null, ['class' => 'form-control', 'placeholder' => 'BRANCH']) !!}
          @if ($errors->has('branch')) <p class="help-block has-error">{{ $errors->first('branch') }}</p> @endif
        </div>

      </div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="panel panel-success">
      <div class="panel-heading">Documents</div>
      <div class="panel-body">
        <div class="form-group @if ($errors->has('resume')) has-error @endif">
          {!! Form::label('resume', 'Resume') !!}
          @if(isset($employee->resume))
          <a class="pull-right" href="{{ URL::asset('cms'.$employee->resume) }}">View/download</a>
          @endif
          <div class="form-group">
            <div class="input-group input-file" name="resume">
              <span class="input-group-btn">
                <button class="btn btn-default btn-choose" type="button">Choose</button>
              </span>
              <input type="text" name="resume" class="form-control" placeholder='Choose a file...' />
              <span class="input-group-btn">
                <button class="btn btn-danger btn-reset" type="button">Remove</button>
              </span>
            </div>
          </div>
          @if ($errors->has('resume')) <p class="help-block has-error">{{ $errors->first('resume') }}</p> @endif
        </div>
        <div class="form-group @if ($errors->has('offer_letter')) has-error @endif">
          {!! Form::label('offer_letter', 'Offer Letter') !!}
          @if(isset($employee->offer_letter))
          <a class="pull-right" href="{{ URL::asset('cms'.$employee->offer_letter) }}">View/download</a>
          @endif
          <div class="form-group">
            <div class="input-group input-file" name="offer_letter">
              <span class="input-group-btn">
                <button class="btn btn-default btn-choose" type="button">Choose</button>
              </span>
              <input type="text" name="offer_letter" class="form-control" placeholder='Choose a file...' />
              <span class="input-group-btn">
                <button class="btn btn-danger btn-reset" type="button">Remove</button>
              </span>
            </div>
          </div>
          @if ($errors->has('offer_letter')) <p class="help-block has-error">{{ $errors->first('offer_letter') }}</p>
          @endif
        </div>
        <div class="form-group @if ($errors->has('joining_letter')) has-error @endif">
          {!! Form::label('joining_letter', 'Joining Letter') !!}
          @if(isset($employee->joining_letter))
          <a class="pull-right" href="{{ URL::asset('cms'.$employee->joining_letter) }}">View/download</a>
          @endif
          <div class="input-group input-file" name="joining_letter">
            <span class="input-group-btn">
              <button class="btn btn-default btn-choose" type="button">Choose</button>
            </span>
            <input type="text" name="joining_letter" class="form-control" placeholder='Choose a file...' />
            <span class="input-group-btn">
              <button class="btn btn-danger btn-reset" type="button">Remove</button>
            </span>
          </div>
          @if ($errors->has('joining_letter')) <p class="help-block has-error">{{ $errors->first('joining_letter') }}
          </p> @endif
        </div>
        <div class="form-group @if ($errors->has('contract')) has-error @endif">
          {!! Form::label('contract', 'Contract And Agreement') !!}
          @if(isset($employee->contract))
          <a class="pull-right" href="{{ URL::asset('cms'.$employee->contract) }}">View/download</a>
          @endif
          <div class="input-group input-file" name="contract">
            <span class="input-group-btn">
              <button class="btn btn-default btn-choose" type="button">Choose</button>
            </span>
            <input type="text" name="contract" class="form-control" placeholder='Choose a file...' />
            <span class="input-group-btn">
              <button class="btn btn-danger btn-reset" type="button">Remove</button>
            </span>
          </div>
          @if ($errors->has('contract')) <p class="help-block has-error">{{ $errors->first('contract') }}</p> @endif
        </div>
        <div class="form-group @if ($errors->has('id_proof')) has-error @endif">
          {!! Form::label('id_proof', 'ID Proof') !!}
          @if(isset($employee->id_proof))
          <a class="pull-right" href="{{ URL::asset('cms'.$employee->id_proof) }}">View/download</a>
          @endif
          <div class="input-group input-file" name="id_proof">
            <span class="input-group-btn">
              <button class="btn btn-default btn-choose" type="button">Choose</button>
            </span>
            <input type="text" name="id_proof" class="form-control" placeholder='Choose a file...' />
            <span class="input-group-btn">
              <button class="btn btn-danger btn-reset" type="button">Remove</button>
            </span>
          </div>
          @if ($errors->has('id_proof')) <p class="help-block has-error">{{ $errors->first('id_proof') }}</p> @endif
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="DT_EMP_FILTER" class="DT_EMP_FILTER">