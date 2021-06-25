{!! Form::model($clientSettings, array('url' => url(route('app.company.setting.updateLayout',[$clientSettings->id])) , 'method' => 'PATCH', 'files'=> true)) !!}

<div class="col-xs-12">
  <div class="form-group @if ($errors->has('login_title')) has-error @endif">
    {!! Form::label('login_title', 'Login Title') !!}
    {!! Form::text('login_title', null, ['class' => 'form-control', 'placeholder' => 'Login Title']) !!}
    @if ($errors->has('login_title')) <p
        class="help-block has-error">{{ $errors->first('login_title') }}</p> @endif
  </div>
</div>
<div class="col-xs-12">
  <div class="form-group @if ($errors->has('login_description')) has-error @endif">
    {!! Form::label('login_description', 'Login Description') !!}
    {!! Form::textarea('login_description', null, ['class' => 'form-control', 'id=login_description', 'placeholder' => 'Login Description']) !!}
    @if ($errors->has('login_description')) <p
        class="help-block has-error">{{ $errors->first('login_description') }}</p> @endif
  </div>
</div>

<!-- Submit Form Button -->
{!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
{!! Form::close() !!}