<div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('bank_id')) has-error @endif">

      {!! Form::label('bank_id', 'Bank Name') !!}<span style="color: red">*</span>

      {!! Form::select('bank_id', [null => 'Select Bank'] + $banks, isset($cheque)? $cheque->bank_id:null,  ['class' => 'form-control select2','required']) !!}

      @if ($errors->has('band_id')) <p class="help-block has-error">{{ $errors->first('band_id') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('cheque_no')) has-error @endif">
      {!! Form::label('cheque_no', 'Cheque No') !!}
      {!! Form::text('cheque_no', null, ['class' => 'form-control', 'placeholder' => 'Cheque No']) !!}
      @if ($errors->has('cheque_no')) <p class="help-block has-error">{{ $errors->first('cheque_no') }}</p> @endif
    </div>
  </div>

</div>
<div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">

      {!! Form::label('employee_id', 'Employee Name') !!}<span style="color: red">*</span>

      @if(Auth::user()->isCompanyEmployee())

      <input type="text" name="employee_id" class="form-control" value="{{(isset($cheque)?$cheque->employees->name:'')}}" disabled>

      @else

      {!! Form::select('employee_id', [null => 'Select Employee'] + $employees, isset($cheque)? $cheque->employee_id:null,  ['class' => 'form-control select2','required']) !!}

      @endif

      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('payment_received')) has-error @endif">
      {!! Form::label('payment_received', 'Amount') !!}<span style="color: red">*</span>
      {!! Form::text('payment_received',isset($cheque)? $cheque->payment_received:null, ['class' => 'form-control onlynumber', 'placeholder' => 'Payment Received','required']) !!}
      @if ($errors->has('payment_received')) <p
      class="help-block has-error">{{ $errors->first('payment_received') }}</p> @endif
    </div>
  </div>

</div>

<div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('cheque_date')) has-error @endif">

      {!! Form::label('cheque_date', 'Cheque Date') !!}<span style="color: red">*</span>

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>
        @if(config('settings.ncal')==0)
        {!! Form::text('cheque_date', null, ['class' => 'form-control pull-right datepicker', 'placeholder' => 'Cheque Date','required']) !!}
        @else
        <input type="text" class="form-control pull-right" id="cheque_date_np"/> 

        {!! Form::text('cheque_date', null, ['id' => 'cheque_date_eng', 'placeholder' => 'Cheque Date','required','hidden']) !!}
        @endif

      </div>

      @if ($errors->has('start_date')) <p class="help-block has-error">{{ $errors->first('cheque_date') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('receive_date')) has-error @endif">

      {!! Form::label('receive_date', 'Receive Date') !!}<span style="color: red">*</span>

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>

        @if(config('settings.ncal')==0)

        {!! Form::text('receive_date', isset($cheque)? $cheque->payment_date:null, ['class' => 'form-control pull-right datepicker', 'id' => 'receive_date', 'placeholder' => 'Payment Date','required']) !!}

        @else

        <input type="text" class="form-control pull-right" id="receive_date_np"/> 
        {!! Form::text('receive_date', isset($cheque)? $cheque->payment_date:null, [ 'id' => 'receive_date_eng','required','hidden']) !!}

        @endif

      </div>

      @if ($errors->has('receive_date')) <p class="help-block has-error">{{ $errors->first('receive_date') }}</p> @endif

    </div>

  </div>

</div>


<div class="row">

  <div class="col-xs-12">

    <div class="form-group">

      {!! Form::label('notes', 'Notes') !!}

      {!! Form::textarea('payment_status_note', isset($cheque)? $cheque->payment_status_note:null, ['class' => 'form-control', 'id=remarks', 'placeholder' => 'Notes...']) !!}

    </div>

  </div>

</div>
<input type="hidden" name="DT_Cheq_FILTER" class="DT_Cheq_FILTER">
