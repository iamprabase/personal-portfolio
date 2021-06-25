<div class="box-body">
    <form id="UpdateContactDetail">
    <div class="row">
      @if(Auth::user()->isCompanyEmployee() && Auth::user()->EmployeeId()==$employee->id)
      @elseif(!($isManager=='true') && $employee->is_owner==1)
      @else
      @if(Auth::user()->can('employee-update'))
      <span id="ActivateContactEdit" class="btn btn-default btn-sm pull-right" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
      <span id="ActivateContactCancel" class="btn btn-default btn-sm pull-right hide" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
      <span id="ActivateContactUpdate" class="hide"><button class="btn btn-default btn-sm pull-right" type="submit" style="margin-right: 10px;"><i class="fa fa-edit"></i>Update</button></span>
      @endif
      @endif
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-mobile icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Alternate Mobile No.</h4>
            <input type="text" name="employee_id" value="{{$employee->id}}" hidden>
            <p id="empAMob" class="text-display">{{ ($employee->a_phone)? '+'.$employee->alt_country_code.'-'.$employee->a_phone:'N/A' }}</p>
            <div class="text-form" hidden>

              <div class="row">
                <div class="col-xs-4">
                  <select name="alt_country_code" class="form-control select2" >
                    @foreach($country_codes as $country_code)
                      <option value="{{$country_code->phonecode}}" {{isset($employee)? (($employee->alt_country_code == $country_code->phonecode)? 'selected=selected':''):(($country_code->phonecode == $setting->phonecode)? 'selected=selected':'')}}>{{$country_code->name}}
                        , +{{$country_code->phonecode}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-xs-8">
                  <input name="a_phone" class="form-control" type="text" value="{{ ($employee->a_phone)? $employee->a_phone:'' }}" />
                </div>
              </div>
              
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-map-marker  icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Local Address</h4>
            <p id="empLAdd" class="text-display">{{ ($employee->local_add)?$employee->local_add:'N/A' }}</p>
            <p class="text-form" hidden><input name="local_address" class="form-control" type="text" value="{{ ($employee->local_add)?$employee->local_add:'' }}" /></p>
          </div>
        </div>
      </div>

      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-map-marker icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Permanent Address</h4>
            <p id="empPAdd" class="text-display">{{ ($employee->per_add)?$employee->per_add:'N/A' }}</p>
            <p class="text-form" hidden><input name="permanent_address" class="form-control" type="text" value="{{ ($employee->per_add)?$employee->per_add:'NA' }}" /></p>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="panel panel-success" style="margin-top: 10px;">
        <div class="panel-heading">
          <h4 style="color:#0b7676;margin-left: 15px;">Emergency Contact Detail</h4>
        </div>
        <div class="panel-body">

          <div class="col-xs-6">
            <div class="media left-list bottom-border">
              <div class="media-left">
                <i class="fa fa-user-o icon-size"></i>
              </div>
              <div class="media-body"><h4 class="media-heading">Name</h4>
                <p id="empEName" class="text-display">{{ ($employee->e_name)?$employee->e_name:'N/A' }}</p>
                <p class="text-form" hidden><input name="e_name" class="form-control" type="text" value="{{ ($employee->e_name)?$employee->e_name:'' }}" /></p>
              </div>
            </div>
          </div>

          <div class="col-xs-6">
            <div class="media left-list bottom-border">
              <div class="media-left">
                <i class="fa fa-user-o icon-size"></i>
              </div>
              <div class="media-body"><h4 class="media-heading">Relation to you(Example:Spouse,Father,Mother etc.)</h4>
                <p id="empERelation" class="text-display">{{ ($employee->e_relation)?$employee->e_relation:'N/A' }}</p>
                <p class="text-form" hidden><input name="e_relation" class="form-control" type="text" value="{{ ($employee->e_relation)?$employee->e_relation:'' }}" /></p>
              </div>
            </div>
          </div>

          <div class="col-xs-6">
            <div class="media left-list bottom-border">
              <div class="media-left">
                <i class="fa fa-user-o icon-size"></i>
              </div>
              <div class="media-body"><h4 class="media-heading">Contact No</h4>
                <p id="empEContact" class="text-display">{{ ($employee->e_phone)?$employee->e_phone:'N/A' }}</p>
                <div class="text-form" hidden>
                  <div class="row">
                    <div class="col-xs-4">
                      <select name="e_country_code" class="form-control select2" >
                        @foreach($country_codes as $country_code)
                          <option value="{{$country_code->phonecode}}" {{isset($employee)? (($employee->e_country_code == $country_code->phonecode)? 'selected=selected':''):(($country_code->phonecode == $setting->phonecode)? 'selected=selected':'')}}>{{$country_code->name}}
                            , +{{$country_code->phonecode}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-xs-8">
                      <input name="e_phone" class="form-control" type="text" value="{{ ($employee->e_phone)?$employee->e_phone:'' }}" />
                    </div>
                  </div>                  
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    </div>
  </form>
</div>