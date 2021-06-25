<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" aria-hidden="true">&times;</button>
      <p>{{ \Session::get('success') }}</p>
    </div><br />
    @endif
    <div class="box">
      <div class="box-body">
        <div class="row">
          <div class="col-xs-6 right-pd">
            <label>Currently Handles</label><small> *Note: Only Active Parties are listed below. </small>
            <div class="table-responsive" id="showHandlers">
              <table id="tbl_partyHandling" class="table table-bcollectioned table-striped table-bordered">
                <thead>
                  <tr>
                    <th>Party Name</th>
                  </tr>
                </thead>
                <tbody>
                  @if(!empty($relatedPaties))
                    @php($i = 0)
                    @foreach($relatedPaties as $party)
                      @php($i++)
                      <tr>
                        <td>{{ $party->company_name }}</td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-xs-6">
            <label>Add Parties</label>
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

            <form action="{{domain_route('company.admin.employee.addParties',[$employee->id])}}" method="POST">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <label class="nonePartyAssigned hidden" style="color: #f59600 !important;">No parties assigned to the superior.</label>
              <div class="form-group">
                @if(getClientSetting()->beat==1)

                  <select name="party[]" id="employeeId-enableClickableOptGroups" multiple="multiple" class="form-control">
                    @if(isset($handles))
                      @foreach($beats as $beat)
                      <optgroup label="{{ $beat['name']}}" value="{{ $beat['id']}}" {{ (((isset($employee->user_id)) && Auth::user()->id==$employee->user_id) || $employee->is_admin==1)?'disabled':'' }}>
                        @foreach($beat['clients'] as $key => $value)
                          @if(Auth::user()->EmployeeId() == $employee->id)
                            @if(in_array($key, $handles))
                              <option @if(in_array($key, $handles)) selected="selected" @endif value="{{ $key }}">{{ $value }}</option>
                            @endif
                          @else
                            <option @if(in_array($key, $handles)) selected="selected" @endif value="{{ $key }}" @if(in_array($key, json_decode($getJuniorParties))) disabled @endif>{{ $value }}</option>
                          @endif
                        @endforeach
                      </optgroup>
                      @endforeach
                    @else
                      @foreach($beats as $beat)
                        <optgroup label="{{ $beat['name']}}" value="{{ $beat['id']}}" {{ ((isset($employee->user_id)) && Auth::user()->id==$employee->user_id)?'disabled':'' }}>
                          @foreach($beat['clients'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                          @endforeach
                        </optgroup>
                      @endforeach
                    @endif
                  </select>

                @else

                  <select name="party[]" id="employeeId-enableClickableOptGroups" multiple="multiple" class="form-control">
                    @if(isset($handles))
                      @foreach($beats as $beat)
                        @foreach($beat['clients'] as $key => $value)
                          @if(Auth::user()->EmployeeId() == $employee->id)
                            @if(in_array($key, $handles))
                              <option value="{{ $key }}" selected="selected" {{ (((isset($employee->user_id)) && Auth::user()->id==$employee->user_id) || $employee->is_admin==1)?'disabled':'' }}>{{ $value }}</option>
                            @endif
                          @else
                            <option @if(in_array($key, $handles)) selected="selected" @endif value="{{ $key }}" @if(in_array($key, json_decode($getJuniorParties))) disabled @endif>{{ $value }}</option>
                          @endif
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
              @if(!empty($beats))
                <button type="submit" class="btn btn-success btn-sm" @if(!Auth::user()->isCompanyManager()) @if(Auth::user()->EmployeeId()==$employee->id) disabled @endif @endif @if($employee->is_admin ==1) disabled @endif>
                  Save
                </button>
              @endif
            </form>
          </div>

        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>