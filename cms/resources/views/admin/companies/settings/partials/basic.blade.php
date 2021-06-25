{!! Form::model($clientSettings, array('url' => url(route('app.company.setting.updateProfile',[$clientSettings->id])) , 'method' => 'PATCH', 'files'=> true)) !!}
<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('title')) has-error @endif">
    {!! Form::label('title', 'Company Name') !!}<!-- <span style="color: red">*</span><span style="color: green">*</span> -->
      {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Name of the company','required']) !!}
      @if ($errors->has('title')) <p class="help-block has-error">{{ $errors->first('title') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('email')) has-error @endif">
      {!! Form::label('email', 'Email') !!}{{-- <span style="color: red">*</span> --}}
      <small>&emsp;(Used for Login)</small>
      <input class="form-control" type="text" name="email"
             value="{{getCompanyEmail($clientSettings->company_id)->contact_email}}" readonly>
      {{-- {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!} --}}
      @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('country')) has-error @endif">
    {!! Form::label('country', 'Country') !!}<!-- <span style="color: red">*</span> -->
      <select class="form-control" name="country" id="country" required>
        <option value="">Select a country</option>
        @foreach($countries as $country)
          <option @if($country->id == $clientSettings->country) selected @endif value="{{ $country->id }}"
                  phonecode="{{$country->phonecode}}">{{ $country->name }}</option>
        @endforeach
      </select>
      @if ($errors->has('country')) <p class="help-block has-error">{{ $errors->first('country') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('city')) has-error @endif">
      {!! Form::label('state', 'State') !!}{{-- <span style="color: red">*</span> --}}
      <select name="state" class="form-control" id="state">
        <option value="" selected>--State--</option>
        @foreach($states as $state)
          <option @if($state->id == $clientSettings->state) selected
                  @endif value="{{ $state->id }}">{{ $state->name }}</option>
        @endforeach
      </select>
      @if ($errors->has('state')) <p class="help-block has-error">{{ $errors->first('state') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('city')) has-error @endif">
      {!! Form::label('city', 'City') !!}{{-- <span style="color: red">*</span> --}}
      <select name="city" class="form-control" id="city">
        <option value="" selected>--City--</option>
        @foreach($cities as $city)
          <option @if($city->id == $clientSettings->city) selected
                  @endif value="{{ $city->id }}">{{ $city->name }}</option>
        @endforeach
      </select>
      @if ($errors->has('city')) <p class="help-block has-error">{{ $errors->first('city') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('address_1')) has-error @endif">
      {!! Form::label('address_1', 'Address Line 1') !!}
      {!! Form::text('address_1', null, ['class' => 'form-control', 'placeholder' => 'Address Line 1']) !!}
      @if ($errors->has('address_1')) <p class="help-block has-error">{{ $errors->first('address_1') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('address_2')) has-error @endif">
      {!! Form::label('address_2', 'Address Line 1') !!}
      {!! Form::text('address_2', null, ['class' => 'form-control', 'placeholder' => 'Address Line 2']) !!}
      @if ($errors->has('address_2')) <p class="help-block has-error">{{ $errors->first('address_2') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('country')) has-error @endif">
      {!! Form::label('zip', 'Zip') !!}
      {!! Form::text('zip', null, ['class' => 'form-control', 'placeholder' => 'Zip']) !!}
      @if ($errors->has('zip')) <p class="help-block has-error">{{ $errors->first('zip') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('phonecode')) has-error @endif">
    {!! Form::label('phonecode', 'Phone Code') !!}<!-- <span style="color: red">*</span> -->
      {!! Form::text('phonecode', null, ['class' => 'form-control', 'id' => 'phonecode', 'placeholder' => 'Phone Code', 'readonly', 'required'=> 'required']) !!}
      @if ($errors->has('phonecode')) <p class="help-block has-error">{{ $errors->first('phonecode') }}</p> @endif
    </div>
  </div>
  
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('mobile')) has-error @endif">
    {!! Form::label('mobile', 'Mobile') !!}<!-- <span style="color: red">*</span> -->
      {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => 'Mobile No.', 'required'=> 'required']) !!}
      @if ($errors->has('mobile')) <p class="help-block has-error">{{ $errors->first('mobile') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('opening_time')) has-error @endif">
      {!! Form::label('opening_time', 'Opening Time') !!}
      {!! Form::text('opening_time', null, ['class' => 'form-control timepicker', 'placeholder' => 'Opening Time']) !!}
      @if ($errors->has('opening_time')) <p
          class="help-block has-error">{{ $errors->first('opening_time') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('closing_time')) has-error @endif">
      {!! Form::label('closing_time', 'Closing Time') !!}
      {!! Form::text('closing_time', null, ['class' => 'form-control timepicker', 'placeholder' => 'Closing Time']) !!}
      @if ($errors->has('closing_time')) <p
          class="help-block has-error">{{ $errors->first('closing_time') }}</p> @endif
    </div>
  </div>

  <div class="col-xs-12">
    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('logo')) has-error @endif">
        <label>Logo</label>
        <small> Recommended size 196x38 <br> (Used in top left corner) <br> (filesize should not exceed 400KB)</small>
        <div class="input-group" style="margin-bottom:10px;">
                        <span class="input-group-btn">
                            <span class="btn btn-default btn-file logofile">
                                Browse… {!! Form::file('logo', ['id'=>'imgInp','class'=>'form-control']) !!}
                            </span>
                        </span>
          <!-- <input type="text" class="form-control" readonly> -->
        </div>
        <img id='img-upload' class="img-responsive layoutLogo" src="@if($clientSettings->logo_path) {{ URL::asset('cms'.$clientSettings->logo_path) }} @endif"
            style="max-height: 150px;"/>
      </div>
      @if ($errors->has('logo')) <p class="help-block has-error">{{ $errors->first('logo') }}</p> @endif
    </div>
  
    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('favicon')) has-error @endif">
        <label>Favicon</label>
        <small><br> (Used as page icon) <br>(filesize should not exceed 50KB)</small>
        <div class="input-group" style="margin-bottom:10px;">
                        <span class="input-group-btn">
                            <span class="btn btn-default btn-file favicon">
                                Browse… {!! Form::file('favicon', ['id'=>'imgInp2','class'=>'form-control']) !!}
                            </span>
                        </span>
          <!-- <input type="text" class="form-control" readonly> -->
        </div>
        <img id='img-upload2' class="img-responsive layoutfavicon"
            src="@if(isset($clientSettings->favicon_path)){{ URL::asset('cms'.$clientSettings->favicon_path) }} @endif"
            style="max-height: 150px;"/>
      </div>
      @if ($errors->has('favicon')) <p class="help-block has-error">{{ $errors->first('favicon') }}</p> @endif
    </div>
  </div>


</div>
<!-- Submit Form Button -->
{!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
{!! Form::close() !!}