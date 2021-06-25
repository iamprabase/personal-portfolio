{!! Form::model($clientSettings, array('url' => url(route('app.company.setting.updateEmail',[$clientSettings->id])) , 'method' => 'PATCH', 'files'=> true)) !!}
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('smtp_host')) has-error @endif">
  {!! Form::label('smtp_host', 'SMTP Host') !!}<!-- <span style="color: green">*</span> -->
    {!! Form::text('smtp_host', null, ['class' => 'form-control', 'placeholder' => 'SMTP Host', isset($clientSettings->smtp_host)? 'readonly':'']) !!}
    @if ($errors->has('smtp_host')) <p class="help-block has-error">{{ $errors->first('smtp_host') }}</p> @endif
  </div>
</div>
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('smtp_username')) has-error @endif">
  {!! Form::label('smtp_username', 'SMTP Username') !!}<!-- <span style="color: green">*</span> -->
    {!! Form::text('smtp_username', null, ['class' => 'form-control', 'placeholder' => 'SMTP Username']) !!}
    @if ($errors->has('smtp_username')) <p
        class="help-block has-error">{{ $errors->first('smtp_username') }}</p> @endif
  </div>
</div>
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('smtp_password')) has-error @endif">
    {{-- {!! Form::label('smtp_password', 'SMTP Password') !!} --}}
    <label for="smtp_password">SMTP Password</label>
    <input type="password" name="smtp_password" id="smtp_password" placeholder="SMTP password"
           class="form-control">
    {{-- {!! Form::password('smtp_password', null, ["class" => "form-control", "placeholder" => "SMTP Password"]) !!} --}}
    @if ($errors->has('smtp_password')) <p
        class="help-block has-error">{{ $errors->first('smtp_password') }}</p> @endif
  </div>
</div>
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('smtp_port')) has-error @endif">
  {!! Form::label('smtp_port', 'SMTP Port') !!}<!-- <span style="color: green">*</span> -->
    {!! Form::text('smtp_port', null, ['class' => 'form-control', 'placeholder' => 'SMTP Port', isset($clientSettings->smtp_port)? 'readonly':'']) !!}
    @if ($errors->has('smtp_port')) <p class="help-block has-error">{{ $errors->first('smtp_port') }}</p> @endif
  </div>
</div>
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('invoice_mail_from')) has-error @endif">
  {!! Form::label('invoice_mail_from', 'Invoice Mail From') !!}<!-- <span style="color: green">*</span> -->
    {!! Form::text('invoice_mail_from', null, ['class' => 'form-control', 'placeholder' => 'Invoice Mail From', isset($clientSettings->invoice_mail_from)? 'readonly':'']) !!}
    @if ($errors->has('invoice_mail_from')) <p
        class="help-block has-error">{{ $errors->first('invoice_mail_from') }}</p> @endif
  </div>
</div>
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('recovery_mail_from')) has-error @endif">
  {!! Form::label('recovery_mail_from', 'Recovery Mail From') !!}<!-- <span style="color: green">*</span> -->
    {!! Form::text('recovery_mail_from', null, ['class' => 'form-control', 'placeholder' => 'Recovery Mail From', isset($clientSettings->recovery_mail_from)? 'readonly':'']) !!}
    @if ($errors->has('recovery_mail_from')) <p
        class="help-block has-error">{{ $errors->first('recovery_mail_from') }}</p> @endif
  </div>
</div>
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('other_mail_from')) has-error @endif">
    {!! Form::label('other_mail_from', 'Other Mail From') !!}
    {!! Form::text('other_mail_from', null, ['class' => 'form-control', 'placeholder' => 'Other Mail From', isset($clientSettings->other_mail_from)? 'readonly':'']) !!}
    @if ($errors->has('other_mail_from')) <p
        class="help-block has-error">{{ $errors->first('other_mail_from') }}</p> @endif
  </div>
</div>

<!-- Submit Form Button -->
{!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
{!! Form::close() !!}