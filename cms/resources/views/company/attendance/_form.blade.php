<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">
      {!! Form::label('employee_id', 'Employee Name') !!}
      {!! Form::select('employee_id', $employees, isset($attendance) ? $attendance->employee_id : null,  ['class' => 'form-control']) !!}
      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('adate')) has-error @endif">
      {!! Form::label('adate', 'Date') !!}<span style="color: red">*</span>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        {!! Form::text('adate', date('Y-m-d'), ['class' => 'form-control pull-right', 'id' => 'adate', 'placeholder' => 'Start Date', 'required']) !!}
      </div>

      @if ($errors->has('adate')) <p class="help-block has-error">{{ $errors->first('adate') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label for="check_in">Check In Time</label><span style="color: red">*</span>
      <div id="check_in_time">
        <div class="input-group bootstrap-timepicker">
          <input type="text" name="check_in[]" id="checkin1" class="form-control time" placeholder="Check In Time"
                 autocomplete="off" required>
          {{-- {!! Form::text('check_in', null, ['class' => 'form-control', 'id' => 'checkin', 'placeholder' => 'Check In Time']) !!} --}}
          <div class="input-group-addon">
            <i class="fa fa-clock-o"></i>
          </div>
        </div>
      </div>
      <!-- /.input group -->
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label for="check_out">Check Out Time</label><span style="color: red">*</span>
      <div id="check_out_time">
        <div class="input-group bootstrap-timepicker">
          <input type="text" name="check_out[]" id="checkout1" class="form-control time" placeholder="Check Out Time"
                 autocomplete="off" required>
          {{-- {!! Form::text('check_out', null, ['class' => 'form-control', 'id' => 'checkout', 'placeholder' => 'Check Out Time']) !!} --}}
          <div class="input-group-addon">
            <i class="fa fa-clock-o"></i>
          </div>
        </div>
      </div>
      <!-- /.input group -->
    </div>
    {{-- <div class="form-group">
            {!! Form::label('check_type', 'CheckIn/Out Type') !!}
      @if(isset($company->status))
        {!! Form::select('check_type', array('1' => 'CheckIn', '2' => 'CheckOut'), $attendance->check_type, ['class' => 'form-control']) !!}
      @else
        {!! Form::select('check_type', array('1' => 'CheckIn', '2' => 'CheckOut'), '1', ['class' => 'form-control']) !!}
      @endif
                  <!-- /.input group -->
                </div> --}}
  </div>
  <div class="col-md-1" id="action_button">
    <label for="action">Action</label>
    <button type="button" id="addmore" class="btn btn-success">Add More</button>
  </div>
</div>

{{-- <div class="form-group">
    {!! Form::label('remark', 'Remark') !!}
    {!! Form::textarea('remark', null, ['class' => 'form-control ckeditor', 'id=remark', 'placeholder' => 'Something about company...']) !!}
</div> --}}

