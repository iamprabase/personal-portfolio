<div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('leavetype')) has-error @endif">

      {!! Form::label('leavetype', 'Leave Type') !!}<span style="color: red">*</span>

      {!! Form::select('leavetype', [null => 'Select a Leave Type'] + $leavetypes, isset($leave)? $leave->leavetype:null,  ['class' => 'form-control select2','required']) !!}

      @if ($errors->has('leavetype')) <p class="help-block has-error">{{ $errors->first('leavetype') }}</p> @endif

    </div>

  </div>
   @if(isset($leave->employee_id))
  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">

      {!! Form::label('employee_id', 'Employee Name') !!}<span style="color: red">*</span>
      {!! Form::text('employee_id', $leave->employee->name, ['class' => 'form-control', 'placeholder' => 'Amount','required','disabled']) !!}

      

      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif

    </div>

  </div>
  @endif
</div>

<div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('start_date')) has-error @endif">

      {!! Form::label('start_date', 'From Date') !!}<span style="color: red">*</span>
      @if(config('settings.ncal')==1)
      <input type="hidden" id="englishDate" name="englishDate">
      @endif

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>
        <input type="text" name="previous_url" value="{{URL::previous()}}" hidden>

        @if(config('settings.ncal')==0)

        {!! Form::text('start_date', null, ['class' => 'form-control pull-right datepicker', 'placeholder' => 'Start Date','id'=>'start_date','required']) !!}

        @else
        <input type="text" id="start_date_np" class="form-control pull-right" />
        {!! Form::text('start_date', (isset($leave->start_date)?$leave->start_date:""), ['id' => 'start_date_eng', 'placeholder' => 'Start Date','required','hidden']) !!}
        @endif

      </div>


      @if ($errors->has('start_date')) <p class="help-block has-error">{{ $errors->first('start_date') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('end_date')) has-error @endif">

      {!! Form::label('end_date', 'To Date') !!}<span style="color: red">*</span>
      @if(config('settings.ncal')==1)
      <input type="hidden" id="endenglishDate" name="endenglishDate">
      @endif

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>
        @if(config('settings.ncal')==0)

        {!! Form::text('end_date', null, ['class' => 'form-control pull-right', 'id' => 'end_date', 'placeholder' => 'End Date','required']) !!}

        @else
        <input type="text" id="end_date_np" class="form-control pull-right" />
        <!-- {!! Form::text('end_date', null, ['id' => 'end_date_eng', 'placeholder' => 'End Date','required','hidden']) !!} -->
        <input type="text" name="end_date" id="end_date_eng" value="{{(isset($leave->end_date)?$leave->end_date:'')}}" hidden />
        @endif

      </div>

      @if ($errors->has('end_date')) <p class="help-block has-error">{{ $errors->first('end_date') }}</p> @endif

    </div>

  </div>

</div>

<!-- Text body Form Input -->

<div class="row">

  <div class="col-xs-12">

    <div class="form-group">

      {!! Form::label('leave_desc', 'Reason') !!}

      {!! Form::textarea('leave_desc', null, ['class' => 'form-control ckeditor', 'id=leave_desc', 'placeholder' => 'About Leave...','required']) !!}

      @if ($errors->has('leave_desc')) <p class="help-block has-error">{{ $errors->first('leave_desc') }}</p> @endif

    </div>

  </div>

</div>
<input type="hidden" name="DT_Leav_FILTER" class="DT_Leav_FILTER">
