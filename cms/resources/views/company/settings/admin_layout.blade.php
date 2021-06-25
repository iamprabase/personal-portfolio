<div class="col-xs-4">
  <div class="form-group @if ($errors->has('logo')) has-error @endif">
    <label>Logo</label>
    <small> Recommended size 196x38 <br> (Used in top left corner) <br> (filesize should not exceed 400KB)</small>
    @if(!empty($setting->logo_path))
      <img id='img-upload' class="img-responsive" src="{{ URL::asset('cms'.$setting->logo_path) }}"
           style="max-height: 150px;"/>
    @endif
  </div>
  @if ($errors->has('logo')) <p class="help-block has-error">{{ $errors->first('logo') }}</p> @endif
</div>
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('small_logo')) has-error @endif">
    <label>Small Logo</label>
    <small> Recommended size 40x50 <br> (Used in top left corner(sidebar hidden)) <br> (filesize should not exceed
      250KB)
    </small>
    @if(!empty($setting->small_logo_path))
      <img id='img-upload1' class="img-responsive" src="{{ URL::asset('cms'.$setting->small_logo_path) }}"
           style="max-height: 150px;"/>
    @endif
  </div>
  @if ($errors->has('small_logo')) <p class="help-block has-error">{{ $errors->first('small_logo') }}</p> @endif
</div>
<div class="col-xs-4">
  <div class="form-group @if ($errors->has('favicon')) has-error @endif">
    <label>Favicon</label>
    <small><br> (Used as page icon) <br>(filesize should not exceed 50KB)</small>
    @if(!empty($setting->favicon_path))
      <img id='img-upload2' class="img-responsive"
           src="@if(isset($setting->favicon_path)){{ URL::asset('cms'.$setting->favicon_path) }} @endif"
           style="margin: 30px auto;"/>
    @endif
  </div>
  @if ($errors->has('favicon')) <p class="help-block has-error">{{ $errors->first('favicon') }}</p> @endif
</div>
<div class="col-sm-12">
  <div class="form-group @if ($errors->has('login_title')) has-error @endif">
    {!! Form::label('login_title', 'Login Title') !!}
    {!! Form::text('login_title', isset($setting->login_title)? $setting->login_title:null, ['class' => 'form-control', 'placeholder' => 'Login Title', 'readonly']) !!}
    @if ($errors->has('login_title')) <p class="help-block has-error">{{ $errors->first('login_title') }}</p> @endif
  </div>
</div>
<div class="col-sm-12">
  <div class="form-group @if ($errors->has('login_description')) has-error @endif">
    {!! Form::label('login_description', 'Login Description') !!}
    {!! Form::textarea('login_description', isset($setting->state)? $setting->login_description:null, ['class' => 'form-control', 'id=login_description', 'placeholder' => 'Login Description', 'readonly']) !!}
    @if ($errors->has('login_description')) <p
        class="help-block has-error">{{ $errors->first('login_description') }}</p> @endif
  </div>
</div>