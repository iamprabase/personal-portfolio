<br>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label style="font-size: 20px;">Company Profile</label>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('title')) has-error @endif">
      {!! Form::label('title', 'Title') !!}<span style="color: red">*</span><span style="color: green">*</span>
      {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Name of the company', isset($setting->title)? 'readonly':'']) !!}
      @if ($errors->has('title')) <p class="help-block has-error">{{ $errors->first('title') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('email')) has-error @endif">
      {!! Form::label('email', 'Email') !!}{{-- <span style="color: red">*</span> --}}
      <small>&emsp;(Used for Login)</small>
      <input class="form-control" type="text" name="email" value="{{Auth::user()->email}}" disabled>
      {{-- {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!} --}}
      @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
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
      {!! Form::label('ext_no', 'Ext. No.') !!}
      {!! Form::text('ext_no', null, ['class' => 'form-control', 'placeholder' => 'Ext. No.']) !!}
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
  <div class="col-md-4">
    <div class="bootstrap-timepicker">
      <div class="form-group @if ($errors->has('opening_time')) has-error @endif">
        {!! Form::label('opening_time', 'Opening Time') !!}
        {!! Form::text('opening_time', null, ['class' => 'form-control timepicker', 'placeholder' => 'Opening Time']) !!}
        @if ($errors->has('opening_time')) <p
            class="help-block has-error">{{ $errors->first('opening_time') }}</p> @endif
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="bootstrap-timepicker">
      <div class="form-group @if ($errors->has('closing_time')) has-error @endif">
        {!! Form::label('closing_time', 'Closing Time') !!}
        {!! Form::text('closing_time', null, ['class' => 'form-control timepicker', 'placeholder' => 'Closing Time']) !!}
        @if ($errors->has('closing_time')) <p
            class="help-block has-error">{{ $errors->first('closing_time') }}</p> @endif
      </div>
    </div>
  </div>
</div>

<br>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label style="font-size: 20px;">Location Info</label>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('country')) has-error @endif">
      {!! Form::label('country', 'Country') !!}
      <select class="form-control" name="country" id="country">
        <option value="">Select a country</option>
        @foreach($countries as $country)
          <option @if($country->id == $setting->country) selected
                  @endif value="{{ $country->id }}">{{ $country->name }}</option>
        @endforeach
      </select>
      @if ($errors->has('country')) <p class="help-block has-error">{{ $errors->first('country') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('city')) has-error @endif">
      {!! Form::label('state', 'State') !!}
      <select name="state" class="form-control" id="state">
        <option selected>--State--</option>
        @foreach($states as $state)
          <option @if($state->id == $setting->state) selected
                  @endif value="{{ $state->id }}">{{ $state->name }}</option>
        @endforeach

      </select>
      @if ($errors->has('city')) <p class="help-block has-error">{{ $errors->first('city') }}</p> @endif
    </div>

  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('city')) has-error @endif">
      {!! Form::label('city', 'City') !!}
      <select name="city" class="form-control" id="city">
        <option selected>--City--</option>
        @foreach($cities as $city)
          <option @if($city->id == $setting->city) selected @endif value="{{ $city->id }}">{{ $city->name }}</option>
        @endforeach
      </select>
      @if ($errors->has('city')) <p class="help-block has-error">{{ $errors->first('city') }}</p> @endif

    </div>

  </div>

</div>
<div class="row">
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('address_1')) has-error @endif">
      {!! Form::label('address_1', 'Address Line 1') !!}
      {!! Form::text('address_1', null, ['class' => 'form-control', 'placeholder' => 'Address Line 1']) !!}
      @if ($errors->has('address_1')) <p class="help-block has-error">{{ $errors->first('address_1') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('address_2')) has-error @endif">
      {!! Form::label('address_2', 'Address Line 1') !!}
      {!! Form::text('address_2', null, ['class' => 'form-control', 'placeholder' => 'Address Line 2']) !!}
      @if ($errors->has('address_2')) <p class="help-block has-error">{{ $errors->first('address_2') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('country')) has-error @endif">
      {!! Form::label('zip', 'Zip') !!}
      {!! Form::text('zip', null, ['class' => 'form-control', 'placeholder' => 'Zip']) !!}
      @if ($errors->has('zip')) <p class="help-block has-error">{{ $errors->first('zip') }}</p> @endif
    </div>
  </div>
</div>


<br>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label style="font-size: 20px;">Admin Layout</label>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('logo')) has-error @endif">
      <label>Logo</label>
      <small> Recommended size 196x38 <br> (Used as Sidebar and Login icon) <br> (filesize should not exceed 400KB)
      </small>
      <div class="input-group" style="margin-bottom:10px;">
            <span class="input-group-btn">
                <span class="btn btn-default btn-file logofile">
                    Browse… {!! Form::file('logo', ['id'=>'imgInp']) !!}
                </span>
            </span>
        <input type="text" class="form-control" readonly>
      </div>
      <img id='img-upload' class="img-responsive" src="{{ URL::asset('cms'.$setting->logo_path) }}"
           style="max-height: 150px;"/>
    </div>
    @if ($errors->has('logo')) <p class="help-block has-error">{{ $errors->first('logo') }}</p> @endif
  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('small_logo')) has-error @endif">
      <label>Small Logo</label>
      <small> Recommended size 40x50 <br> (Used as diminished Sidebar icon) <br> (filesize should not exceed 250KB)
      </small>
      <div class="input-group" style="margin-bottom:10px;">
            <span class="input-group-btn">
                <span class="btn btn-default btn-file smalllogofile">
                    Browse… {!! Form::file('small_logo', ['id'=>'imgInp1']) !!}
                </span>
            </span>
        <input type="text" class="form-control" readonly>
      </div>
      <img id='img-upload1' class="img-responsive"
           src="@if(isset($setting->small_logo_path)){{ URL::asset('cms'.$setting->small_logo_path) }} @endif"
           style="max-height: 150px;"/>
    </div>
    @if ($errors->has('small_logo')) <p class="help-block has-error">{{ $errors->first('small_logo') }}</p> @endif
  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('favicon')) has-error @endif">
      <label>Favicon</label>
      <small><br><br>(filesize should not exceed 50KB)</small>
      <div class="input-group" style="margin-bottom:10px;">
            <span class="input-group-btn">
                <span class="btn btn-default btn-file favicon">
                    Browse… {!! Form::file('favicon', ['id'=>'imgInp2']) !!}
                </span>
            </span>
        <input type="text" class="form-control" readonly>
      </div>
      <img id='img-upload2' class="img-responsive"
           src="@if(isset($setting->favicon_path)){{ URL::asset('cms'.$setting->favicon_path) }} @endif"
           style="max-height: 150px;"/>
    </div>
    @if ($errors->has('favicon')) <p class="help-block has-error">{{ $errors->first('favicon') }}</p> @endif
  </div>
</div>

<div class="row">
<!-- <div class="col-md-6">
		<div class="form-group @if ($errors->has('login_logo')) has-error @endif">
		    {!! Form::label('login_logo', 'Login Logo') !!}
{!! Form::text('login_logo', null, ['class' => 'form-control', 'placeholder' => 'Login Logo']) !!}
@if ($errors->has('login_logo')) <p class="help-block has-error">{{ $errors->first('login_logo') }}</p> @endif
    </div>
  </div> -->
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('login_title')) has-error @endif">
      {!! Form::label('login_title', 'Login Title') !!}
      {!! Form::text('login_title', null, ['class' => 'form-control', 'placeholder' => 'Login Title']) !!}
      @if ($errors->has('login_title')) <p class="help-block has-error">{{ $errors->first('login_title') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('login_description')) has-error @endif">
      {!! Form::label('login_description', 'Login Description') !!}
      {!! Form::textarea('login_description', null, ['class' => 'form-control', 'id=login_description', 'placeholder' => 'Login Description']) !!}
      @if ($errors->has('login_description')) <p
          class="help-block has-error">{{ $errors->first('login_description') }}</p> @endif
    </div>
  </div>

  <div class="row">

  </div>
  {{-- <div class="col-md-6">
    <div class="form-group @if ($errors->has('copyright_text')) has-error @endif">
    {!! Form::label('copyright_text', 'Copyright Text') !!}
    {!! Form::textarea('copyright_text', null, ['class' => 'form-control', 'id=copyright_text', 'placeholder' => 'Copyright Text...']) !!}
    @if ($errors->has('copyright_text')) <p class="help-block has-error">{{ $errors->first('copyright_text') }}</p> @endif
</div>
  </div> --}}
</div>

<br>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label style="font-size: 20px;">Email Setup</label>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-3">
    <div class="form-group @if ($errors->has('smtp_host')) has-error @endif">
      {!! Form::label('smtp_host', 'SMTP Host') !!}<span style="color: green">*</span>
      {!! Form::text('smtp_host', null, ['class' => 'form-control', 'placeholder' => 'SMTP Host', isset($setting->smtp_host)? 'readonly':'']) !!}
      @if ($errors->has('smtp_host')) <p class="help-block has-error">{{ $errors->first('smtp_host') }}</p> @endif
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group @if ($errors->has('smtp_username')) has-error @endif">
      {!! Form::label('smtp_username', 'SMTP Username') !!}<span style="color: green">*</span>
      {!! Form::text('smtp_username', null, ['class' => 'form-control', 'placeholder' => 'SMTP Username', isset($setting->smtp_username)? 'readonly':'']) !!}
      @if ($errors->has('smtp_username')) <p
          class="help-block has-error">{{ $errors->first('smtp_username') }}</p> @endif
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group @if ($errors->has('smtp_password')) has-error @endif">
      {{-- {!! Form::label('smtp_password', 'SMTP Password') !!} --}}
      <label for="smtp_password">SMTP Password</label>
      <input type="password" name="smtp_password" id="smtp_password" placeholder="SMTP password" class="form-control">
      {{-- {!! Form::password('smtp_password', null, ["class" => "form-control", "placeholder" => "SMTP Password"]) !!} --}}
      @if ($errors->has('smtp_password')) <p
          class="help-block has-error">{{ $errors->first('smtp_password') }}</p> @endif
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group @if ($errors->has('smtp_port')) has-error @endif">
      {!! Form::label('smtp_port', 'SMTP Port') !!}<span style="color: green">*</span>
      {!! Form::text('smtp_port', null, ['class' => 'form-control', 'placeholder' => 'SMTP Port', isset($setting->smtp_port)? 'readonly':'']) !!}
      @if ($errors->has('smtp_port')) <p class="help-block has-error">{{ $errors->first('smtp_port') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('invoice_mail_from')) has-error @endif">
      {!! Form::label('invoice_mail_from', 'Invoice Mail From') !!}<span style="color: green">*</span>
      {!! Form::text('invoice_mail_from', null, ['class' => 'form-control', 'placeholder' => 'Invoice Mail From', isset($setting->invoice_mail_from)? 'readonly':'']) !!}
      @if ($errors->has('invoice_mail_from')) <p
          class="help-block has-error">{{ $errors->first('invoice_mail_from') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('recovery_mail_from')) has-error @endif">
      {!! Form::label('recovery_mail_from', 'Recovery Mail From') !!}<span style="color: green">*</span>
      {!! Form::text('recovery_mail_from', null, ['class' => 'form-control', 'placeholder' => 'Recovery Mail From', isset($setting->recovery_mail_from)? 'readonly':'']) !!}
      @if ($errors->has('recovery_mail_from')) <p
          class="help-block has-error">{{ $errors->first('recovery_mail_from') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('other_mail_from')) has-error @endif">
      {!! Form::label('other_mail_from', 'Other Mail From') !!}
      {!! Form::text('other_mail_from', null, ['class' => 'form-control', 'placeholder' => 'Other Mail From', isset($setting->other_mail_from)? 'readonly':'']) !!}
      @if ($errors->has('other_mail_from')) <p
          class="help-block has-error">{{ $errors->first('other_mail_from') }}</p> @endif
    </div>
  </div>
</div>

<br>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label style="font-size: 20px;">Other Setup</label>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('default_currency')) has-error @endif">
      {!! Form::label('default_currency', 'Default Currency') !!}<span style="color: red">*</span><span
          style="color: green">*</span>
      <select name="default_currency" id="default_currency"
              class="form-control" {{isset($setting->default_currency)? 'readonly':''}}>
        <option value="">Select a default currency</option>
        @foreach($currencies as $currency)
          <option value="{{$currency->code}}"
                  symbol="{{$currency->symbol}}" {{isset($setting->default_currency)? (($setting->default_currency == $currency->code)? 'selected':''):null}}>{{$currency->country}}
            , {{$currency->code}}</option>
        @endforeach
      </select>
      {{-- {!! Form::text('default_currency', null, ['class' => 'form-control', 'placeholder' => 'default_currency', isset($setting->default_currency)? 'readonly':'']) !!} --}}
      @if ($errors->has('default_currency')) <p
          class="help-block has-error">{{ $errors->first('default_currency') }}</p> @endif
    </div>
  </div>
  {{-- <div class="col-md-4">
    <div class="form-group @if ($errors->has('currency_format')) has-error @endif">
        {!! Form::label('currency_format', 'Currency Format') !!}<span style="color: red">*</span>
        {!! Form::text('currency_format', null, ['class' => 'form-control', 'placeholder' => 'Currency Format', isset($setting->default_currency)? 'readonly':'']) !!}
        @if ($errors->has('currency_format')) <p class="help-block has-error">{{ $errors->first('currency_format') }}</p> @endif
    </div>
  </div> --}}
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('currency_symbol')) has-error @endif">
      {!! Form::label('currency_symbol', 'Currency Symbol') !!}<span style="color: red">*</span><span
          style="color: green">*</span>
      <input type="text" name="currency_symbol" id="currency_symbol" class="form-control" placeholder="Currency Symbol"
             value="{{isset($setting->currency_symbol)? $setting->currency_symbol:null}}" readonly>
      {{-- {!! Form::text('currency_symbol', null, ['class' => 'form-control', 'placeholder' => 'Currency Symbol', 'readonly']) !!} --}}
      @if ($errors->has('currency_symbol')) <p
          class="help-block has-error">{{ $errors->first('currency_symbol') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group @if ($errors->has('time_zone')) has-error @endif">
      {!! Form::label('time_zone', 'Time Zone') !!}<span style="color: red">*</span><span style="color: green">*</span>
      {{ Form::select('time_zone', $timezonelist, 'Asia/Kathmandu', ['class' => 'form-control', isset($setting->time_zone)? 'disabled':'']) }}
      @if ($errors->has('time_zone')) <p class="help-block has-error">{{ $errors->first('time_zone') }}</p> @endif
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group @if ($errors->has('date_format')) has-error @endif">
      {!! Form::label('date_format', 'Date Format') !!}<span style="color: red">*</span><span
          style="color: green">*</span>
      {!! Form::select('date_format', array('yyyy-mm-dd' => 'yyyy-mm-dd (2016-12-31)', 'dd-mm-yyyy' => 'dd-mm-yyyy (31-12-2016)','dd-M-yyyy' => 'dd-M-yyyy (31 Dec 2016)', 'yyyy-M-dd' => 'yyyy-M-dd (2016 Dec 31)'), $setting->date_format, ['class' => 'form-control', 'placeholder' => 'Date Format', isset($setting->date_format)? 'disabled':'']) !!}
      @if ($errors->has('date_format')) <p class="help-block has-error">{{ $errors->first('date_format') }}</p> @endif
    </div>
  </div>
  {{-- <div class="col-md-4">
    <div class="form-group">
    <label for="gender">Date Type</label>
    <span class="checkbox" style="margin-top: 2px;">
                <label style="padding: 0px;">
                  {{ Form::radio('date_type', 'English' , true, ['class'=>'minimal', isset($setting->date_type)? 'readonly':'']) }} English
                </label>
                <label>
                  {{ Form::radio('date_type', 'Nepali' , false, ['class'=>'minimal', isset($setting->date_type)? 'readonly':'']) }} Nepali
                </label>
              </span>
  </div>
</div> --}}
</div>


<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('order_prefix')) has-error @endif">
      {!! Form::label('order_prefix', 'Order Prefix') !!}<span style="color: green">*</span>
      {!! Form::text('order_prefix', null, ['class' => 'form-control', 'placeholder' => 'Order Prefix', isset($setting->order_prefix)? 'readonly':'']) !!}
      @if ($errors->has('order_prefix')) <p class="help-block has-error">{{ $errors->first('order_prefix') }}</p> @endif
    </div>
  </div>
  {{-- <div class="col-md-4">
    <div class="form-group @if ($errors->has('vat_percent')) has-error @endif">
        {!! Form::label('vat_percent', 'VAT Percentage') !!}
        {!! Form::text('vat_percent', null, ['class' => 'form-control', 'placeholder' => 'VAT Percentage']) !!}
        @if ($errors->has('vat_percent')) <p class="help-block has-error">{{ $errors->first('vat_percent') }}</p> @endif
    </div>
  </div> --}}
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('title')) has-error @endif">
      {!! Form::label('invoice_prefix', 'Invoice Prefix') !!}<span style="color: green">*</span>
      {!! Form::text('invoice_prefix', null, ['class' => 'form-control', 'placeholder' => 'Invoice Prefix', isset($setting->invoice_prefix)? 'readonly':'']) !!}
      @if ($errors->has('invoice_prefix')) <p
          class="help-block has-error">{{ $errors->first('invoice_prefix') }}</p> @endif
    </div>
  </div>
  <div class="col-md-4">
    <label>Types of Taxes Implied on Orders</label>
    <div class="table-responsive">
      <table class="table table-bordered" id="dynamic_field">
        <tr>
          <th>Tax Name</th>
          <th>Percentage</th>
          <th></th>
        </tr>
        {{-- <tr>
          <td><input name="tax_name[]" class="form-control"></td>
          <td><input name="tax_percent[]" class="form-control"></td>
        </tr> --}}
      </table>
      <button type="button" name="add" id="add" class="btn btn-success btn-xs">Add</button>
    </div>
  </div>
  <div class="col-md-4">
    <label>Currently Implied Taxes</label>
    <div class="table-responsive" id="showTaxes">
      <table class="table table-bordered">
        <thead>
        <th>Tax Name</th>
        <th>Percentage</th>
        <th>Action</th>
        </thead>
        <tbody>
        @foreach($taxes as $tax)
          <tr>
            <td>{{$tax->name}}</td>
            <td>{{$tax->percent}}</td>
            <td>
              <a id="removeTax_{{$tax->id}}" onclick="removeTax({{$tax->id}})" class="btn btn-danger btn-xs">Remove</a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>





