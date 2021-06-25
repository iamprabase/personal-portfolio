<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
    <div class="alert alert-success">
      <p>{{ \Session::get('success') }}</p>
    </div><br />
    @endif

    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Employees for this client</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div id="loader1" hidden>
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
        <div class="row">
          <div class="col-xs-6 right-pd">
            <label>Currently Accessible By</label>
            <div class="table-responsive" id="showHandlers">
              <table id="accessible_by" class="table table-bordered">
                <thead>
                  <th>Employee Name</th>
                  {{-- <th>Action</th> --}}
                </thead>
                <tbody>
                  @if($handles)
                  @php $handles_uniq = array_unique($handles) @endphp
                  @foreach($handles_uniq as $handle)
                  @if(getActiveEmployee($handle))
                  @if(getActiveEmployee($handle)['status'] == 'Active')
                  <tr>
                    <td>{{getActiveEmployee($handle)['name']}}</td>
                  </tr>
                  @endif
                  @endif
                  @endforeach
                  @else
                  <tr>
                    <td>None</td>
                    {{-- <td>N/A</td> --}}
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-xs-6">
            <label>Add Employee</label><br>
            <form action="{{URL::to('admin/client/addEmployee')}}" method="POST">
              <div class="form-group">
                @if($handles)
                  <select name="employees[]" id="employee_id" class="form-control" multiple="multiple" style="width: 100%"
                    @if(Auth::user()->isCompanyEmployee()) readonly="true" @endif>
                    @foreach($employeesDesignations as $designation=>$employeesDesignation)
                    <optgroup label="{{$designation}}">
                      @foreach($employeesDesignation as $employee)
                      @if(!in_array($employee->id,$links))
                      <option
                        value="{{$employee->id}}"  @if(in_array($employee->id,$handles)) selected @endif @if(!Auth::user()->isCompanyManager()) @if((Auth::user()->EmployeeId() == $employee->id || in_array($employee->id, $userSuperiors))  || !in_array($employee->id, $userJuniors)) disabled="disabled" @endif @elseif(Auth::user()->isCompanyManager()) @if($employee->is_admin==1) disabled="disabled" @endif @endif>{{$employee->name}}</option>
                      @endif
                      @endforeach
                    </optgroup>
                    @endforeach
                  </select>
                
                  @if(!Auth::user()->isCompanyManager()) 
                    <select name="hiddenemployees[]" id="hiddenemployee_id" class="form-control" multiple="multiple" style="width: 100%;display: none;"
                      @if(Auth::user()->isCompanyEmployee()) readonly="true" @endif>
                      
                      @foreach($employeesDesignations as $designation=>$employeesDesignation)
                        @foreach($employeesDesignation as $employee)
                          @if(!in_array($employee->id,$links))
                            @if((Auth::user()->EmployeeId() == $employee->id || in_array($employee->id, $userSuperiors)) || !in_array($employee->id, $userJuniors))
                              @if(in_array($employee->id,$handles))
                              <option
                                value="{{$employee->id}}"   selected>{{$employee->name}}</option>
                              @endif
                            @endif
                          @endif
                        @endforeach
                      @endforeach
                    
                    </select>
                  @elseif(Auth::user()->isCompanyManager())
                    <select name="hiddenemployees[]" id="hiddenemployee_id" class="form-control" multiple="multiple" style="width: 100%;display: none;">
                      
                      @foreach($employeesDesignations as $designation=>$employeesDesignation)
                        @foreach($employeesDesignation as $employee)
                            @if($employee->is_admin==1 && in_array($employee->id, $handles))
                            <option
                              value="{{$employee->id}}"   selected>{{$employee->name}}</option>
                            @endif
                        @endforeach
                      @endforeach
                    
                    </select>
                  @endif
                
                @else
                
                <select name="employees[]" id="employee_id" class="form-control" multiple="multiple"
                  style="width: 100%">
                  @foreach($employeesDesignations as $designation=>$employeesDesignation)
                  <optgroup label="{{$designation}}">
                    @foreach($employeesDesignation as $employee)
                    @if(!in_array($employee->id,$links))
                    <option value="{{$employee->id}}" @if($employee->is_admin==1) selected @endif @if(!Auth::user()->isCompanyManager()) @if((Auth::user()->EmployeeId() == $employee->id || in_array($employee->id, $userSuperiors)) || !in_array($employee->id, $userJuniors)) disabled="disabled" @endif @elseif(Auth::user()->isCompanyManager()) @if($employee->is_admin==1) disabled="disabled" @endif @endif>{{$employee->name}}</option>
                    @endif
                    @endforeach
                  </optgroup>
                  @endforeach
                </select>

                @if(!Auth::user()->isCompanyManager()) 
                  <select name="hiddenemployees[]" id="hiddenemployee_id" class="form-control" multiple="multiple" style="width: 100%;display: none;"
                    
                    @foreach($employeesDesignations as $designation=>$employeesDesignation)
                    <optgroup label="{{$designation}}">
                      @foreach($employeesDesignation as $employee)
                        @if($employee->is_admin==1)
                        <option value="{{$employee->id}}" selected>{{$employee->name}}</option>
                        @endif
                      @endforeach
                    </optgroup>
                    @endforeach
                  
                  </select>
                @elseif(Auth::user()->isCompanyManager())
                  <select name="hiddenemployees[]" id="hiddenemployee_id" class="form-control" multiple="multiple" style="width: 100%;display: none;"
                    
                    @foreach($employeesDesignations as $designation=>$employeesDesignation)
                    <optgroup label="{{$designation}}">
                      @foreach($employeesDesignation as $employee)
                        @if($employee->is_admin==1)
                        <option value="{{$employee->id}}" selected>{{$employee->name}}</option>
                        @endif
                      @endforeach
                    </optgroup>
                    @endforeach
                  
                  </select>
                @endif

                @endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="client_id" value="{{$client->id}}">
              </div>
              <button type="submit" {{--  onclick="addEmployee()" --}} class="btn btn-success btn-sm client_access" id="saveEmployeeAssign">
                Save
              </button>
            </form>
          </div>

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
      @if(checkChildPartyType($client->id))
      <div class="box-body">
        <div class="row">
          <div class="col-xs-6 right-pd">
            <label>Link Accessibility</label>
            <div class="table-responsive" id="showHandlers">
              <table id="link_accessibility" class="table table-bordered">
                <thead>
                  <th>Employee Name</th>
                  {{-- <th>Action</th> --}}
                </thead>
                <tbody>
                  @if($links)
                  @foreach($links as $handle)
                  @if(getActiveEmployee($handle)['status'] == 'Active')
                  <tr>
                    <td>{{getActiveEmployee($handle)['name']}}</td>
                  </tr>
                  @endif
                  @endforeach
                  @else
                  <tr>
                    <td>None</td>
                    {{-- <td>N/A</td> --}}
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-xs-6">
            <label>Add Employee</label><br>
            <form action="{{URL::to('admin/client/addLinkEmployee')}}" method="POST">
              <div class="form-group">
                @if($links)
                  <select name="employees[]" id="employee_id2" class="form-control" multiple="multiple"
                    style="width: 100%">
                    @foreach($employeesDesignations as $designation=>$employeesDesignation)
                    <optgroup label="{{$designation}}" value="{{$designation}}">
                      @foreach($employeesDesignation as $employee)
                      @if(!in_array($employee->id,$handles))
                      <option
                        value="{{$employee->id}}" @if(in_array($employee->id,$links)) selected @endif @if(!Auth::user()->isCompanyManager()) @if((Auth::user()->EmployeeId() == $employee->id || in_array($employee->id, $userSuperiors)) || !in_array($employee->id, $userJuniors)) disabled="disabled" @endif @endif>{{$employee->name}}</option>
                      @endif
                      @endforeach
                    </optgroup>
                    @endforeach
                  </select>

                @if(!Auth::user()->isCompanyManager()) 
                  <select name="hiddenemployees[]" id="hiddenemployee_id2" class="form-control" multiple="multiple"
                    style="width: 100%; display:none;">

                    @foreach($employeesDesignations as $designation=>$employeesDesignation)
                      @foreach($employeesDesignation as $employee)
                        @if(!in_array($employee->id,$handles))
                          @if((Auth::user()->EmployeeId() == $employee->id || in_array($employee->id, $userSuperiors)) || !in_array($employee->id, $userJuniors)) 
                            @if(in_array($employee->id,$links))
                              <option value="{{$employee->id}}">{{$employee->name}}</option>
                            @endif
                          @endif
                        @endif
                      @endforeach
                    @endforeach
                  
                  </select>
                @endif

                @else

                  <select name="employees[]" id="employee_id2" class="form-control" multiple="multiple"
                    style="width: 100%;">
                    @foreach($employeesDesignations as $designation=>$employeesDesignation)
                    <optgroup label="{{$designation}}" value="{{$designation}}">
                      @foreach($employeesDesignation as $employee)
                      @if(!in_array($employee->id,$handles))
                      <option value="{{$employee->id}}"@if(!Auth::user()->isCompanyManager()) @if((Auth::user()->EmployeeId() == $employee->id || in_array($employee->id, $userSuperiors))  || !in_array($employee->id, $userJuniors)) disabled="disabled" @endif @endif>{{$employee->name}}</option>
                      @endif
                      @endforeach
                    </optgroup>
                    @endforeach

                  </select>
                  @if(!Auth::user()->isCompanyManager()) 
                    <select name="hiddenemployees[]" id="hiddenemployee_id2" class="form-control" multiple="multiple"
                      style="width: 100%; display:none;">
                      <option value=""></option>
                    </select>
                  @endif

                @endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="client_id" value="{{$client->id}}">
              </div>
              <button type="submit" {{--  onclick="addEmployee()" --}} class="btn btn-success btn-sm client_access">
                Save
              </button>
            </form>
          </div>

        </div>
        <!-- /.box-body -->
      </div>
      @endif
    </div>
    <!-- /.col -->
  </div>
</div>