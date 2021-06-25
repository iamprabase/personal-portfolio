<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('title')) has-error @endif">
      {!! Form::label('title', 'Title') !!}
      {!! Form::text('title', isset($setting->title)? $setting->title:null, ['class' => 'form-control', 'placeholder' => 'Name of the company', isset($setting->title)? 'disabled':'','required']) !!}
      @if ($errors->has('title')) <p class="help-block has-error">{{ $errors->first('title') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('email')) has-error @endif">
      {!! Form::label('email', 'Email') !!}{{-- <span style="color: red">*</span> --}}
      <small>&emsp;(Used for Login)</small>
      <input class="form-control" type="text" name="email" value="{{Auth::user()->email}}" disabled>
      @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('country')) has-error @endif">
      {!! Form::label('country', 'Country') !!}{{-- <span style="color: red">*</span> --}}
      {!! Form::text('country', isset($setting->country)? getCountryName($setting->country)->name:null , ['class'=>'form-control', 'id'=>'country', 'readonly'])!!}
      @if ($errors->has('country')) <p class="help-block has-error">{{ $errors->first('country') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('state')) has-error @endif">
      {!! Form::label('state', 'State') !!}{{-- <span style="color: red">*</span> --}}
      {!! Form::text('state', isset($setting->state)? getStateName($setting->state)->name:null, ['class'=>'form-control', 'id'=>'state', 'readonly'])!!}
      @if ($errors->has('state')) <p class="help-block has-error">{{ $errors->first('state') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('city')) has-error @endif">
      {!! Form::label('city', 'City') !!}{{-- <span style="color: red">*</span> --}}
      {!! Form::text('city', isset($setting->city)? getCityName($setting->city):null, ['class'=>'form-control', 'id'=>'city', 'readonly'])!!}
      @if ($errors->has('city')) <p class="help-block has-error">{{ $errors->first('city') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('address_1')) has-error @endif">
      {!! Form::label('address_1', 'Address Line 1') !!}
      {!! Form::text('address_1', isset($setting->address_1)? $setting->address_1:null, ['class' => 'form-control', 'placeholder' => 'Address Line 1', 'readonly']) !!}
      @if ($errors->has('address_1')) <p class="help-block has-error">{{ $errors->first('address_1') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('address_2')) has-error @endif">
      {!! Form::label('address_2', 'Address Line 2') !!}
      {!! Form::text('address_2', isset($setting->address_2)? $setting->address_2:null, ['class' => 'form-control', 'placeholder' => 'Address Line 2', 'readonly']) !!}
      @if ($errors->has('address_2')) <p class="help-block has-error">{{ $errors->first('address_2') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('country')) has-error @endif">
      {!! Form::label('zip', 'Zip') !!}
      {!! Form::text('zip', isset($setting->zip)? $setting->zip:null, ['class' => 'form-control', 'placeholder' => 'Zip', 'readonly']) !!}
      @if ($errors->has('zip')) <p class="help-block has-error">{{ $errors->first('zip') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('phonecode')) has-error @endif">
      {!! Form::label('phonecode', 'Phone Code') !!}{{-- <span style="color: red">*</span> --}}
      {!! Form::text('phonecode', isset($setting->phonecode)? $setting->phonecode:null, ['class' => 'form-control', 'id' => 'phonecode', 'placeholder' => 'Phone Code', 'readonly', 'required'=> 'required']) !!}
      @if ($errors->has('phonecode')) <p class="help-block has-error">{{ $errors->first('phonecode') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('phone')) has-error @endif">
      {!! Form::label('phone', 'Phone') !!}{{-- <span style="color: red">*</span> --}}
      {!! Form::text('phone', isset($setting->state)? $setting->phone:null, ['class' => 'form-control', 'placeholder' => 'Phone No.', 'required'=> 'required', 'readonly']) !!}
      @if ($errors->has('phone')) <p class="help-block has-error">{{ $errors->first('phone') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group">
      {!! Form::label('ext_no', 'Ext. No.') !!}
      {!! Form::text('ext_no', isset($setting->ext_no)? $setting->ext_no:null, ['class' => 'form-control', 'placeholder' => 'Ext. No.', 'readonly']) !!}
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('mobile')) has-error @endif">
      {!! Form::label('mobile', 'Mobile') !!}{{-- <span style="color: red">*</span> --}}
      {!! Form::text('mobile', isset($setting->mobile)? $setting->mobile:null, ['class' => 'form-control', 'placeholder' => 'Mobile No.', 'required'=> 'required', 'readonly']) !!}
      @if ($errors->has('mobile')) <p class="help-block has-error">{{ $errors->first('mobile') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('opening_time')) has-error @endif">
      {!! Form::label('opening_time', 'Opening Time') !!}
      {!! Form::text('opening_time', isset($setting->opening_time)? $setting->opening_time:null, ['class' => 'form-control timepicker', 'placeholder' => 'Opening Time', 'readonly']) !!}
      @if ($errors->has('opening_time')) <p class="help-block has-error">{{ $errors->first('opening_time') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-3">
    <div class="form-group @if ($errors->has('closing_time')) has-error @endif">
      {!! Form::label('closing_time', 'Closing Time') !!}
      {!! Form::text('closing_time', isset($setting->closing_time)? $setting->closing_time:null, ['class' => 'form-control timepicker', 'placeholder' => 'Closing Time', 'readonly']) !!}
      @if ($errors->has('closing_time')) <p class="help-block has-error">{{ $errors->first('closing_time') }}</p> @endif
    </div>
  </div>
</div>