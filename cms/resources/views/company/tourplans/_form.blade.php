<div class="row">

  <div class="col-md-6">

    <div class="form-group @if ($errors->has('leavetype')) has-error @endif">

      {!! Form::label('leavetype', 'Leave Type') !!}<span style="color: red">*</span>

      {!! Form::select('leavetype', [null => 'Select a Leave Type'] + $leavetypes, isset($leave)? $leave->leavetype:null,  ['class' => 'form-control select2','required']) !!}

      @if ($errors->has('leavetype')) <p class="help-block has-error">{{ $errors->first('leavetype') }}</p> @endif

    </div>

  </div>

  <div class="col-md-6">

    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">

      {!! Form::label('employee_id', 'Employee Name') !!}<span style="color: red">*</span>

      {!! Form::select('employee_id', [null => 'Select Employee'] + $employees, isset($expense)? $expense->employee_id:null,  ['class' => 'form-control select2','required']) !!}

      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif

    </div>

  </div>

</div>

<div class="row">

  <div class="col-md-6">

    <div class="form-group @if ($errors->has('start_date')) has-error @endif">

      {!! Form::label('start_date', 'From Date') !!}<span style="color: red">*</span>

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>
        <input type="text" name="previous_url" value="{{URL::previous()}}" hidden>

        {!! Form::text('start_date', null, ['class' => 'form-control pull-right', 'id' => 'start_date', 'placeholder' => 'Start Date','required']) !!}

      </div>


      @if ($errors->has('start_date')) <p class="help-block has-error">{{ $errors->first('start_date') }}</p> @endif

    </div>

  </div>

  <div class="col-md-6">

    <div class="form-group @if ($errors->has('end_date')) has-error @endif">

      {!! Form::label('end_date', 'To Date') !!}<span style="color: red">*</span>

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>

        {!! Form::text('end_date', null, ['class' => 'form-control pull-right', 'id' => 'end_date', 'placeholder' => 'End Date','required']) !!}

      </div>

      @if ($errors->has('end_date')) <p class="help-block has-error">{{ $errors->first('end_date') }}</p> @endif

    </div>

  </div>

</div>

<!-- Text body Form Input -->

<div class="row">

  <div class="col-md-12">

    <div class="form-group">

      {!! Form::label('leave_desc', 'Reason') !!}

      {!! Form::textarea('leave_desc', null, ['class' => 'form-control', 'id=leave_desc', 'placeholder' => 'About Leave...']) !!}

    </div>

  </div>

</div>

<div class="row">

  <div class="col-md-6">

    <div class="form-group">

      {!! Form::label('status', 'Status') !!}

      @if(isset($leave->status))

        {!! Form::select('status', array('Approved' => 'Approved', 'Rejected' => 'Rejected', 'Pending' => 'Pending'), $leave->status, ['class' => 'form-control select2']) !!}

      @else

        {!! Form::select('status', array('Approved' => 'Approved', 'Rejected' => 'Rejected', 'Pending' => 'Pending'), 'Pending', ['class' => 'form-control select2']) !!}

      @endif

    </div>

  </div>

</div>
<input type="hidden" name="DT_Tour_FILTER" class="DT_Tour_FILTER">
