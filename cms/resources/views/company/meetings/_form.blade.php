<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">
      {!! Form::label('employee_id', 'Employee Name') !!}<span style="color: red">*</span>
      <select name="employee_id" class="form-control" required>
        <option
            value="0" {{isset($meeting)? (($meeting->employee_id == 0)? 'selected':''):null}}>{{Auth::user()->name.' (Admin)'}}</option>
        @if(isset($meeting))
          @foreach($employees as $employee)
            <option
                value="{{$employee->id}}" {{(($meeting->employee_id == $employee->id)? 'selected':'' )}} >{{$employee->name}}</option>
          @endforeach
        @else
          @foreach($employees as $employee)
            <option value="{{$employee->id}}">{{$employee->name}}</option>
          @endforeach
        @endif
      </select>
      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('client_id')) has-error @endif">
      {!! Form::label('client_id', 'Party Name') !!}
      <select name="client_id" class="form-control" required>
        <option value="">Select a Party</option>
        @if(isset($meeting))
          @foreach($clients as $client)
            <option
                value="{{$client->id}}" {{(($meeting->client_id == $client->id)? 'selected':'' )}} >{{$client->company_name}}</option>
          @endforeach
        @else
          @foreach($clients as $client)
            <option value="{{$client->id}}">{{$client->company_name}}</option>
          @endforeach
        @endif
      </select>
      {{-- {!! Form::select('employee_id', $employees, isset($meeting)? $meeting->employee_id:null,  ['class' => 'form-control']) !!} --}}
      @if ($errors->has('client_id')) <p class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-4 bootstrap-timepicker">
    <div class="form-group @if ($errors->has('checkintime')) has-error @endif">
      {!! Form::label('checkintime', 'Check In Time') !!}<span style="color: red">*</span>
      {!! Form::text('checkintime', null, ['class' => 'form-control timepicker', 'placeholder' => 'Check In Time', 'autocomplete'=>'off', 'required'=>'required']) !!}
      @if ($errors->has('checkintime')) <p class="help-block has-error">{{ $errors->first('checkintime') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('meetingdate')) has-error @endif">
      {!! Form::label('meetingdate', 'Meeting Date') !!}<span style="color: red">*</span>
      {!! Form::text('meetingdate', null, ['class' => 'form-control datepicker', 'placeholder' => 'Meeting Date', 'id'=>'meetingdate', 'autocomplete'=>'off', 'required'=>'required']) !!}
      @if ($errors->has('meetingdate')) <p class="help-block has-error">{{ $errors->first('meetingdate') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      {!! Form::label('comm_medium', 'Communication Medium') !!}

      {!! Form::select('comm_medium', array(
'Audio Call'=>'Audio Call' , 'Video Call'=>'Video Call' , 'Meeting'=>'Meeting' , 'Chat'=>'Chat' , 'Messages'=>'Messages' , 'Conference'=>'Conference' , 'Others'=>'Others'), isset($meeting)? $meeting->comm_medium:null, ['class' => 'form-control',  'required'=>'required']) !!}

    </div>
  </div>
</div>
<div class="form-group @if ($errors->has('remark')) has-error @endif"">
{!! Form::label('remark', 'Remark') !!}<span style="color: red">*</span>
{!! Form::textarea('remark', null, ['class' => 'form-control', 'placeholder' => 'Remark',  'required'=>'required']) !!}
@if ($errors->has('remark')) <p class="help-block has-error">{{ $errors->first('remark') }}</p> @endif
</div>
<div class="row">
  {{-- <div class="form-group @if ($errors->has('meeting_photo')) has-error @endif">
        <label>Picture proof(Bill, Receipt, e.t.c)</label>
        <i>*max 3 images {{isset($image_count)? '('. $image_count .' uploaded, '. (3 - $image_count) .' remaining)':'' }}</i>  <input type="file" class="form-control" id="meeting_photo" name="meeting_photo[]" placeholder="Image" multiple>
    </div>
     @if ($errors->has('meeting_photo')) <p class="help-block has-error">{{ $errors->first('meeting_photo') }}</p> @endif --}}

</div>
