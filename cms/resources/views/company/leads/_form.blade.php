<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('company')) has-error @endif">
      {!! Form::label('company', 'Company Name ') !!}<span style="color: red">*</span>
      {!! Form::text('company', null, ['class' => 'form-control', 'placeholder' => 'Company Name']) !!}
      @if ($errors->has('company')) <p class="help-block has-error">{{ $errors->first('company') }}</p> @endif
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group @if ($errors->has('name')) has-error @endif">
      {!! Form::label('name', 'Contact Person Name ') !!}<span style="color: red">*</span>
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Contact Person Name']) !!}
      @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
    </div>
  </div>

</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('email')) has-error @endif">
      {!! Form::label('email', 'Email ') !!}
      {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
      @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('phone')) has-error @endif">
      {!! Form::label('phone', 'Phone') !!}<span style="color: red">*</span>
      {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Phone']) !!}
      @if ($errors->has('phone')) <p class="help-block has-error">{{ $errors->first('phone') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('source')) has-error @endif">
      {!! Form::label('source', 'Lead Source') !!}<span style="color: red">*</span>
      {!! Form::select('source', $leadssources, isset($lead)? $lead->source:null,  ['class' => 'form-control']) !!}
      @if ($errors->has('source')) <p class="help-block has-error">{{ $errors->first('source') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">
      {!! Form::label('employee_id', 'Employee Name') !!}<span style="color: red">*</span>
      {!! Form::select('employee_id', $employees, isset($lead)? $lead->employee_id:null,  ['class' => 'form-control']) !!}
      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('status')) has-error @endif">
      {!! Form::label('status', 'Status') !!}

      {!! Form::select('status', array('Hot' => 'Hot', 'Cold' => 'Cold', 'Warm' => 'Warm'), isset($lead)?$lead->status:'Cold', ['class' => 'form-control']) !!}
    </div>
  </div>
  <div class="col-md-6">

  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('description')) has-error @endif">
      {!! Form::label('description', 'Description') !!}
      {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Payment Notes']) !!}
      @if ($errors->has('description')) <p class="help-block has-error">{{ $errors->first('description') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">


    </div>
  </div>
</div>
