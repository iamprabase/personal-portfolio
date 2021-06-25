@if (\Session::has('success'))
<div class="alert alert-success">
  <p>{{ \Session::get('success') }}</p>
</div>
<br />
@endif
@if (\Session::has('warning'))
  <div class="alert alert-warning">
    <p>{{ \Session::get('warning') }}</p>
  </div>
  <br />
@endif
<form action="{{domain_route('company.admin.update.adminLayout')}}" method="post" enctype="multipart/form-data">
@csrf

<div class="col-sm-12">
  <div class="form-group @if ($errors->has('login_title')) has-error @endif">
    {!! Form::label('login_title', 'Login Title') !!}
    {!! Form::text('login_title', isset($setting->login_title)? $setting->login_title:null, ['class' => 'form-control', 'placeholder' => 'Login Title']) !!}
    @if ($errors->has('login_title')) <p class="help-block has-error">{{ $errors->first('login_title') }}</p> @endif
  </div>
</div>
<div class="col-sm-12">
  <div class="form-group @if ($errors->has('login_description')) has-error @endif">
    {!! Form::label('login_description', 'Login Description') !!}
    {!! Form::textarea('login_description', isset($setting->state)? $setting->login_description:null, ['class' => 'form-control', 'id=login_description', 'placeholder' => 'Login Description']) !!}
    @if ($errors->has('login_description')) <p
        class="help-block has-error">{{ $errors->first('login_description') }}</p> @endif
  </div>
</div>
<button id="btnLayout" style="position: relative;background-color: #0b7676!important;border-color: #0b7676!important;margin-top: 25px;" type="submit" class="btn btn-primary pull-right" >Update</button>
</form>
