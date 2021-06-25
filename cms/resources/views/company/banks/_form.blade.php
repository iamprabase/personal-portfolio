<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('companyName')) has-error @endif">
      {!! Form::label('companyName', 'Company Name') !!}<span style="color: red">*</span>
      {!! Form::text('companyName', null, ['class' => 'form-control', 'placeholder' => 'Name of the company']) !!}
      @if ($errors->has('companyName')) <p class="help-block has-error">{{ $errors->first('companyName') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('alias')) has-error @endif">
      {!! Form::label('alias', 'Alias ') !!}<span style="color: red">*</span>
      <small>A unique name. Will be used as sub domain</small>
      {!! Form::text('alias', null, ['class' => 'form-control', 'placeholder' => 'Alias-A unique name for sub-domain']) !!}
      @if ($errors->has('alias')) <p class="help-block has-error">{{ $errors->first('alias') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('name')) has-error @endif">
      {!! Form::label('name', 'Conatct Person Name') !!}<span style="color: red">*</span>
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Contact Person Name']) !!}
      @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('email')) has-error @endif">
      {!! Form::label('email', 'Email') !!}<span style="color: red">*</span>
      <small>Will be used as User Name</small>
      {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
      @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('password')) has-error @endif">
      {!! Form::label('password', 'Password') !!}<span style="color: red">*</span>
      {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) !!}
      @if ($errors->has('password')) <p class="help-block has-error">{{ $errors->first('password') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('c_password')) has-error @endif">
      {!! Form::label('c_password', 'Confirm Password') !!}<span style="color: red">*</span>
      {!! Form::password('c_password', ['class' => 'form-control', 'placeholder' => 'Confirm Password No.']) !!}
      @if ($errors->has('c_password')) <p class="help-block has-error">{{ $errors->first('c_password') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('phone')) has-error @endif">
      {!! Form::label('phone', 'Phone') !!}<span style="color: red">*</span>
      {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Phone No.']) !!}
      @if ($errors->has('phone')) <p class="help-block has-error">{{ $errors->first('phone') }}</p> @endif
    </div>
  </div>
  <div class="col-md-2">
    <div class="form-group">
      {!! Form::label('extNo', 'Ext. No.') !!}
      {!! Form::text('extNo', null, ['class' => 'form-control', 'placeholder' => 'Ext. No.']) !!}
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('mobile')) has-error @endif">
      {!! Form::label('mobile', 'Mobile') !!}<span style="color: red">*</span>
      {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => 'Mobile No.']) !!}
      @if ($errors->has('mobile')) <p class="help-block has-error">{{ $errors->first('mobile') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      {!! Form::label('fax', 'Fax No.') !!}
      {!! Form::text('fax', null, ['class' => 'form-control', 'placeholder' => 'Fax No.']) !!}
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      {!! Form::label('pan', 'PAN/VAT') !!}
      {!! Form::text('pan', null, ['class' => 'form-control', 'placeholder' => 'PAN/VAT']) !!}
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('plan')) has-error @endif">
      {!! Form::label('plan', 'Plan') !!}
      {!! Form::select('plan', $plans, isset($company) ? $company->plans->pluck('id')->toArray() : null,  ['class' => 'form-control']) !!}
      @if ($errors->has('plan')) <p class="help-block has-error">{{ $errors->first('plan') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
			<span class="checkbox" style="margin-top: 30px;">
                  <label>
                  	{{ Form::checkbox('whitelabel', 'Yes', isset($company->whitelabel),['class'=>'minimal']) }}
                  	 White Label
                    
                  </label>
                  <label>
                  	{{ Form::checkbox('customize', 'Yes', isset($company->customize),['class'=>'minimal']) }}
                  	 Customize
                  	
                  </label>
                </span>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      {!! Form::label('status', 'Status') !!}
      @if(isset($company->status))
        {!! Form::select('status', array('Active' => 'Active', 'Disabled' => 'Disabled', 'Banned' => 'Banned'), $company->status, ['class' => 'form-control']) !!}
      @else
        {!! Form::select('status', array('Active' => 'Active', 'Disabled' => 'Disabled', 'Banned' => 'Banned'), 'Active', ['class' => 'form-control']) !!}
      @endif
    </div>


  </div>

</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('startdate')) has-error @endif">
      {!! Form::label('startdate', 'Start Date') !!}<span style="color: red">*</span>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        {!! Form::text('startdate', null, ['class' => 'form-control pull-right', 'id' => 'startdate', 'placeholder' => 'Start Date']) !!}
      </div>

      @if ($errors->has('startdate')) <p class="help-block has-error">{{ $errors->first('startdate') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('enddate')) has-error @endif">
      {!! Form::label('enddate', 'End Date') !!}<span style="color: red">*</span>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        {!! Form::text('enddate', null, ['class' => 'form-control pull-right', 'id' => 'enddate', 'placeholder' => 'End Date']) !!}
      </div>
      @if ($errors->has('enddate')) <p class="help-block has-error">{{ $errors->first('enddate') }}</p> @endif
    </div>
  </div>
</div>
<!-- Text body Form Input -->
<div class="form-group">
  {!! Form::label('aboutCompany', 'About Company') !!}
  {!! Form::textarea('aboutCompany', null, ['class' => 'form-control ckeditor', 'id=aboutCompany', 'placeholder' => 'Something about company...']) !!}
</div>