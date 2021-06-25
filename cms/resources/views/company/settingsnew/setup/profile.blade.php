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
<form action="{{domain_route('company.admin.setting.updateProfile', [$setting->id])}}" method="post" enctype="multipart/form-data">
@csrf
@method('PATCH')
<div class="row">
  <div class="col-xs-12">
    <div class="form-group @if ($errors->has('title')) has-error @endif">
      {!! Form::label('title', 'Company Name') !!}
      {!! Form::text('title', isset($setting->title)? $setting->title:null, ['class' => 'form-control', 'placeholder' => 'Name of the company', 'required']) !!}
      @if ($errors->has('title')) <p class="help-block has-error">{{ $errors->first('title') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-12">
    <div class="form-group @if ($errors->has('email')) has-error @endif">
      {!! Form::label('email', 'Email') !!}
      <small>(Used for Login)</small>
      <input class="form-control" type="text" name="email" value="{{Auth::user()->email}}" disabled>
      @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('country')) has-error @endif">
      {!! Form::label('country', 'Country') !!}
      <!-- {!! Form::text('country', isset($setting->country)? getCountryName($setting->country)->name:null , ['class'=>'form-control', 'id'=>'country', 'readonly'])!!} -->

      <select class="form-control select2" name="country" id="country" required>
        <option value="">Select a country</option>
        @foreach($countries as $country)
          <option @if($country->id == $setting->country) selected @endif value="{{ $country->id }}"
                  phonecode="{{$country->phonecode}}">{{ $country->name }}</option>
        @endforeach
      </select>


      @if ($errors->has('country')) <p class="help-block has-error">{{ $errors->first('country') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('state')) has-error @endif">
      {!! Form::label('state', 'State') !!}
      <!-- {!! Form::text('state', isset($setting->state)? getStateName($setting->state)->name:null, ['class'=>'form-control', 'id'=>'state', 'readonly'])!!} -->
      <select name="state" class="form-control select2" id="state">
        <option value="" selected>--State--</option>
        @foreach($states as $state)
          <option @if($state->id == $setting->state) selected
                  @endif value="{{ $state->id }}">{{ $state->name }}</option>
        @endforeach
      </select>
  

      @if ($errors->has('state')) <p class="help-block has-error">{{ $errors->first('state') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('city')) has-error @endif">
      {!! Form::label('city', 'City') !!}
      <!-- {!! Form::text('city', isset($setting->city)? getCityName($setting->city):null, ['class'=>'form-control', 'id'=>'city', 'readonly'])!!} -->
      <select name="city" class="form-control select2" id="city">
        <option value="" selected>--City--</option>
        @foreach($cities as $city)
          <option @if($city->id == $setting->city) selected
                  @endif value="{{ $city->id }}">{{ $city->name }}</option>
        @endforeach
      </select>


      @if ($errors->has('city')) <p class="help-block has-error">{{ $errors->first('city') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('country')) has-error @endif">
      {!! Form::label('zip', 'Zip') !!}
      {!! Form::text('zip', isset($setting->zip)? $setting->zip:null, ['class' => 'form-control', 'placeholder' => 'Zip']) !!}
      @if ($errors->has('zip')) <p class="help-block has-error">{{ $errors->first('zip') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('address_1')) has-error @endif">
      {!! Form::label('address_1', 'Address Line 1') !!}
      {!! Form::text('address_1', isset($setting->address_1)? $setting->address_1:null, ['class' => 'form-control', 'placeholder' => 'Address Line 1']) !!}
      @if ($errors->has('address_1')) <p class="help-block has-error">{{ $errors->first('address_1') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('address_2')) has-error @endif">
      {!! Form::label('address_2', 'Address Line 2') !!}
      {!! Form::text('address_2', isset($setting->address_2)? $setting->address_2:null, ['class' => 'form-control', 'placeholder' => 'Address Line 2']) !!}
      @if ($errors->has('address_2')) <p class="help-block has-error">{{ $errors->first('address_2') }}</p> @endif
    </div>
  </div>  
</div>
<div class="row">
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('phonecode')) has-error @endif">
      {!! Form::label('phonecode', 'Phone Code') !!}
      {!! Form::text('phonecode', isset($setting->phonecode)? $setting->phonecode:null, ['class' => 'form-control', 'id' => 'phonecode', 'placeholder' => 'Phone Code', 'required'=> 'required']) !!}
      @if ($errors->has('phonecode')) <p class="help-block has-error">{{ $errors->first('phonecode') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3 hidden">
    <div class="form-group @if ($errors->has('phone')) has-error @endif">
      {!! Form::label('phone', 'Phone') !!}
      {!! Form::text('phone', isset($setting->phone)? $setting->phone:null, ['class' => 'form-control', 'placeholder' => 'Phone No.', 'required'=> 'required']) !!}
      @if ($errors->has('phone')) <p class="help-block has-error">{{ $errors->first('phone') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3 hidden">
    <div class="form-group">
      {!! Form::label('ext_no', 'Ext. No.') !!}
      {!! Form::text('ext_no', isset($setting->ext_no)? $setting->ext_no:null, ['class' => 'form-control', 'placeholder' => 'Ext. No.']) !!}
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('mobile')) has-error @endif">
      {!! Form::label('mobile', 'Mobile') !!}
      {!! Form::text('mobile', isset($setting->mobile)? $setting->mobile:null, ['class' => 'form-control', 'placeholder' => 'Mobile No.', 'required'=> 'required']) !!}
      @if ($errors->has('mobile')) <p class="help-block has-error">{{ $errors->first('mobile') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('opening_time')) has-error @endif">
      <div class="bootstrap-timepicker">
        {!! Form::label('opening_time', 'Opening Time') !!}
        <div class="position-relative has-icon-left">
          {!! Form::text('opening_time', isset($setting->opening_time)? $setting->opening_time:null, ['class' => 'form-control timepicker', 'placeholder' => 'Opening Time']) !!}
        </div>
      </div>
      @if ($errors->has('opening_time')) <p class="help-block has-error">{{ $errors->first('opening_time') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('closing_time')) has-error @endif">
      <div class="bootstrap-timepicker">
      {!! Form::label('closing_time', 'Closing Time') !!}
        <div class="position-relative has-icon-left">
          {!! Form::text('closing_time', isset($setting->closing_time)? $setting->closing_time:null, ['class' => 'form-control timepicker', 'placeholder' => 'Closing Time']) !!}
        </div>
      </div>
      @if ($errors->has('closing_time')) <p class="help-block has-error">{{ $errors->first('closing_time') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-4 hidden">
    <div class="form-group @if ($errors->has('small_logo')) has-error @endif">
      <div class="input-group">
        <span class="input-group-btn">
          <span class="btn btn-default btn-file imagefile">
            Browse… {!! Form::file('small_logo', ['class'=>'imgInpSmall', "accept" => "image/*"]) !!}
          </span>
        </span>
        <input type="text" class="form-control" readonly>
      </div>
      <label>Small Logo</label>
      <small> Recommended size 40x50 <br> (Used in top left corner(sidebar hidden)) <br> (filesize should not exceed
        250KB)
      </small>
      <div style="display: flex;">
      
        <img id='small-logo' class="img-responsive layoutLogo" src="@if($setting->small_logo_path){{ URL::asset('cms/'.$setting->small_logo_path) }}@endif"
            style="max-height: 150px;"/>
            <!-- <i class="fa fa-close" style="font-size: 20px;color: red;cursor: pointer;"></i> -->
      </div>
    </div>
    @if ($errors->has('small_logo')) <p class="help-block has-error">{{ $errors->first('small_logo') }}</p> @endif
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('logo')) has-error @endif">
      <div class="input-group">
        <span class="input-group-btn">
          <span class="btn btn-default btn-file imagefile">
            Browse… {!! Form::file('logo', ['class'=>'imgInp',"accept" => "image/*"]) !!}
          </span>
        </span>
        <input type="text" class="form-control" readonly>
      </div>
      <label>Logo</label>
      <small> Recommended size 196x38 <br> (Used in top left corner) <br> (filesize should not exceed 400KB)</small>
      <div style="display: flex;  margin-top: 10px;">
      <img id='logo-upload' class="img-responsive layoutLogo" src="@if($setting->logo_path) {{ URL::asset('cms/'.$setting->logo_path) }}@endif" style="max-height: 150px;"/>
      <!-- <i class="fa fa-close" style="font-size: 20px;color: red;cursor: pointer;"></i> -->
      </div>
    </div>
    @if ($errors->has('logo')) <p class="help-block has-error">{{ $errors->first('logo') }}</p> @endif
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('favicon')) has-error @endif">
      <div class="input-group">
        <span class="input-group-btn">
          <span class="btn btn-default btn-file imagefile">
            Browse… {!! Form::file('favicon', ['class'=>'imgInpFavicon',"accept" => "image/*"]) !!}
          </span>
        </span>
        <input type="text" class="form-control" readonly>
      </div>
      <label>Favicon</label>
      <small><br> (Used as page icon) <br>(filesize should not exceed 50KB)</small>
      <div style="display: flex; margin-top: 10px;">
      <img id='favicon' class="img-responsive layoutfavicon" src="@if(isset($setting->favicon_path)){{ URL::asset('cms/'.$setting->favicon_path) }} @endif"/>
      <!-- <i class="fa fa-close" style="font-size: 20px;color: red;cursor: pointer;"></i> -->
      </div>
    </div>
    @if ($errors->has('favicon')) <p class="help-block has-error">{{ $errors->first('favicon') }}</p> @endif
  </div>
</div>

<button id="btnProfile" style="position: relative;background-color: #0b7676!important;border-color: #0b7676!important;margin-top: 25px;" type="submit" class="btn btn-primary pull-right" >Update</button>
</form>