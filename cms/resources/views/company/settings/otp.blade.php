<div class="col-xs-12">
  <h3 class="site-tital">Generate OTP </h3>
</div>
<div class="info">
  <div class="col-xs-6">
    <div class="box">
      <div class="box-header">
      </div>
      <div class="box-body">
        <table id="users" class="table table-bordered table-striped">
          <thead>
          <tr>
            
            <th>#</th>
            
            <th>Name</th>
            <th>OTP</th>
          
          </tr>
          
          </thead>
          
          <tbody>
          
          @php($i = 0)
          
          @foreach($otps as $otp)
            @php($i++)
            <tr>
              <td>{{ $i }}</td>
              <td>{{ $otp->name}}</td>
              <td>{{ $otp->password}}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Generate OTP</h3>
        <span id="userexports" class="pull-right"></span>
      </div>
      <div class="box-body">
        {!! Form::open(array('url' => url(domain_route("company.admin.setting.generateotp", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true)) !!}
        <div class="form-group @if ($errors->has('name')) has-error @endif">
          
          {!! Form::label('name', 'OTP Name') !!}<span style="color: red">*</span>
          
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'OTP Name', 'required']) !!}
          
          @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
        
        </div>
        <div class="form-group @if ($errors->has('email')) has-error @endif">
          
          {!! Form::label('email', 'Send To') !!}<span style="color: red">*</span>
          
          {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Receipents Email', 'required']) !!}
          
          @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
        
        </div>
        <div class="form-group @if ($errors->has('party_id')) has-error @endif">
          {!! Form::label('employee_id', 'Employees') !!}<span style="color: red">*</span>
          <select name="employeeId[]" multiple id="employeeId" class="form-control">
            @foreach($employeelist as $employee)
              <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
          </select>
          @if ($errors->has('employee_id')) <p
              class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
        </div>
        {!! Form::submit('Generate', ['class' => 'btn btn-primary pull-right generateOtp']) !!}
        {!! Form::close() !!}
      </div>
    </div>
  </div>
  <div class="modal modal-default fade" id="deleteotp" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" unit="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <form method="post" class="remove-record-modelotp">
          {{method_field('post')}}
          {{csrf_field()}}
          <div class="modal-body">
            <p class="text-center">
              Are you sure you want to delete this?
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button>
            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>