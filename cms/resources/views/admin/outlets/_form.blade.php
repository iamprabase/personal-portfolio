<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('outlet_name')) has-error @endif">
      {!! Form::label('outlet_name', 'Outlet Name') !!}<span style="color: red">*</span>
      {!! Form::text('outlet_name', isset($outlet)?$outlet->outlet_name:"", ['class' => 'form-control', 'placeholder' => 'Name of the outlet']) !!}
      @if ($errors->has('outlet_name')) <p class="help-block has-error">{{ $errors->first('outlet_name') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('contact_name')) has-error @endif">
      {!! Form::label('contact_name', 'Conatct Person Name') !!}<span style="color: red">*</span>
      {!! Form::text('contact_name', isset($outlet)?$outlet->contact_person:"", ['class' => 'form-control',
      'placeholder' => 'Contact Person Name']) !!}
      @if ($errors->has('contact_name')) <p class="help-block has-error">{{ $errors->first('contact_name') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('unique_code')) has-error @endif">
      {!! Form::label('unique_code', 'Unique Secret Code') !!}<span style="color: red">*</span>
      <small>A unique secret code for the outlet.</small>
      {!! Form::text('unique_code', $unique_code, ['class' => 'form-control', isset($company->unique_code)?
      'readonly':'readonly']) !!}
      @if ($errors->has('unique_code')) <p class="help-block has-error">{{ $errors->first('domain') }}</p> @endif
      <label for="update_code">
        Update Secret Code?
      </label>
      <input type="checkbox" name="update_secret_code" id="update_secret_code">
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('contact_email')) has-error @endif">
      {!! Form::label('contact_email', 'Email') !!}<span style="color: red">*</span>
      <small>Will be used as User Name</small>
      {!! Form::text('contact_email', isset($outlet)?$outlet->email:"", ['class' => 'form-control', 'placeholder' =>
      'Email', 'readonly'=>'readonly']) !!}
      @if ($errors->has('contact_email')) <p class="help-block has-error">{{ $errors->first('contact_email') }}</p>
      @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-4">
    <div class="form-group">
      {!! Form::label('country', 'Country') !!}<span style="color: red">*</span>
      @if(isset($outlet))
        {!! Form::select('country', $countries, $outlet->country, ['class' => 'form-control', 'placeholder'=>'Select Country', 'required'=>true]) !!}
      @else
        {!! Form::select('country', $countries, null, ['class' => 'form-control', 'placeholder'=>'Select City', 'required'=>true]) !!}
      @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group">
      {!! Form::label('state', 'State') !!}<span style="color: red">*</span>
      @if(isset($outlet))
      {!! Form::select('state', $states, $state, ['class' => 'form-control', 'placeholder'=>'Select
      State', 'required'=>true]) !!}
      @else
      {!! Form::select('state', array(), null, ['class' => 'form-control', 'placeholder'=>'Select State',
      'required'=>true]) !!}
      @endif
    </div>
  </div>
  <div class="col-xs-4">
    <div class="form-group">
      {!! Form::label('city', 'City') !!}
      @if(isset($outlet))
        {!! Form::select('city', $cities, $outlet->city, ['class' => 'form-control', 'placeholder'=>'Select City', 'required'=>true]) !!}
      @else
        {!! Form::select('city', array(), null, ['class' => 'form-control', 'placeholder'=>'Select City', 'required'=>true]) !!}
      @endif
    </div>
  </div>
  <div class="col-xs-2">
    <div class="form-group">
      {!! Form::label('extNo', 'Country Code') !!}
      {{-- {!! Form::text('extNo', isset($outlet)?$outlet->phone_ext:"", ['class' => 'form-control', 'placeholder' => 'Ext.
      No.']) !!} --}}
      @if(isset($outlet))
        {!! Form::select('extNo', array($outlet->phone_ext=>$outlet->phone_ext), $outlet->phone_ext, ['class' => 'form-control', 'placeholder'=>'Extension', 'required'=>true]) !!}
      @else
        {!! Form::select('extNo', array(), null, ['class' => 'form-control', 'placeholder'=>'Extension', 'required'=>true]) !!}
      @endif
    </div>
  </div>
  <div class="col-xs-4">

    <div class="form-group @if ($errors->has('contact_phone')) has-error @endif">

      {!! Form::label('contact_phone', 'Phone') !!}<span style="color: red">*</span>

      {!! Form::text('contact_phone', isset($outlet)?$outlet->phone:"", ['class' => 'form-control', 'placeholder' => 'Phone No.']) !!}

      @if ($errors->has('contact_phone')) <p class="help-block has-error">{{ $errors->first('contact_phone') }}</p>
      @endif

    </div>

  </div>
  <div class="col-xs-6">
    <div class="form-group">
      {!! Form::label('status', 'Status') !!}
      @if(isset($outlet))
      {!! Form::select('status', array('Active' => 'Active', 'Disabled' => 'Disabled'), $outlet->status, ['class' =>
      'form-control']) !!}
      @else
      {!! Form::select('status', array('Active' => 'Active', 'Disabled' => 'Disabled'), 'Active', ['class' =>
      'form-control']) !!}
      @endif
    </div>
  </div>
</div>

  <div class="row">
    
  </div>
  
  <div class="row">
    <div class="col-xs-12">
      <div class="form-group">
        {!! Form::label('location' ,'Location') !!}<span style="color: red">*</span>
        @if(isset($outlet->gps_location))
        {!! Form::text('location' , $outlet->gps_location, ['class' => 'form-control', 'placeholder' => 'Enter Location',
        'id'=>"search_addr",'name'=>'address','required']) !!}
        {!! Form::hidden('lat' , $outlet->latitude, ['class' => 'form-control', 'name'=>'lat', 'id'=>'search_latitude'])
        !!}
        {!! Form::hidden('lng' , $outlet->longitude, ['class' => 'form-control', 'name'=>'lng','id'=>'search_longitude'])
        !!}
        @else
        {!! Form::text('location' , null, ['class' => 'form-control', 'placeholder' => 'Enter Location',
        'id'=>"search_addr",'name'=>'address','required']) !!}
        {!! Form::hidden('lat' , null, ['class' => 'form-control', 'name'=>'lat', 'id'=>'search_latitude']) !!}
        {!! Form::hidden('lng' , null, ['class' => 'form-control', 'name'=>'lng','id'=>'search_longitude']) !!}
        @endif
        @if ($errors->has('address')) <p class="help-block has-error">{{ $errors->first('address') }}</p> @endif
        @if ($errors->has('lat')) <p class="help-block has-error">{{ $errors->first('lat') }}</p> @endif
        @if ($errors->has('lng')) <p class="help-block has-error">{{ $errors->first('lng') }}</p> @endif
      </div>
      <div class="form-group" id="geomap" style="width: 100%; height: 400px;"></div>
    </div>
  
  </div>
  {{-- <div class="row">
    <div class="col-xs-6">
    
      <div class="form-group @if ($errors->has('password')) has-error @endif">
    
        {!! Form::label('password', 'Password') !!}<span style="color: red">*</span>
    
        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) !!}
    
        @if ($errors->has('password')) <p class="help-block has-error">{{ $errors->first('password') }}</p> @endif
    
      </div>
    
    </div>
    
    <div class="col-xs-6">
    
      <div class="form-group @if ($errors->has('c_password')) has-error @endif">
    
        {!! Form::label('c_password', 'Confirm Password') !!}<span style="color: red">*</span>
    
        {!! Form::password('c_password', ['class' => 'form-control', 'placeholder' => 'Confirm Password No.']) !!}
    
        @if ($errors->has('c_password')) <p class="help-block has-error">{{ $errors->first('c_password') }}</p> @endif
    
      </div>
    
    </div>

  </div> --}}


{{-- <div class="row">

  <div class="col-xs-6">

    <div class="form-group">

      {!! Form::label('fax', 'Fax No.') !!}

      {!! Form::text('fax', null, ['class' => 'form-control', 'placeholder' => 'Fax No.']) !!}

    </div>

  </div>

  <div class="col-xs-6">

    <div class="form-group">

      {!! Form::label('pan', 'PAN/VAT') !!}

      {!! Form::text('pan', null, ['class' => 'form-control', 'placeholder' => 'PAN/VAT']) !!}

    </div>

  </div>

</div> --}}


{{-- <div class="row">

  <div class="col-xs-4">

    <div class="form-group @if ($errors->has('plan')) has-error @endif">

      {!! Form::label('plan', 'Plan') !!}

      @if(isset($company))
      <select name="plan" class="form-control">
        @foreach($plans as $plan)
        <option @if($company->plan_id==$plan->id) selected="selected" @endif value="{{$plan->id}}">{{$plan->name}}
          {{($plan->custom!=0)?" (Custom)":""}}</option>
        @endforeach
      </select>
      @else
      <select name="plan" class="form-control">
        @foreach($plans as $plan)
        <option value="{{$plan->id}}">{{$plan->name}} {{($plan->custom!=0)?" (Custom)":""}}</option>
        @endforeach
      </select>
      @endif

      @if ($errors->has('plan')) <p class="help-block has-error">{{ $errors->first('plan') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-4">

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

  <div class="row">

  </div>

</div> --}}


{{-- <div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('start_date')) has-error @endif">

      {!! Form::label('start_date', 'Start Date') !!}<span style="color: red">*</span>

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>

        {!! Form::text('start_date', null, ['class' => 'form-control pull-right', 'id' => 'start_date', 'placeholder' =>
        'Start Date']) !!}

      </div>


      @if ($errors->has('start_date')) <p class="help-block has-error">{{ $errors->first('start_date') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('end_date')) has-error @endif">

      {!! Form::label('end_date', 'End Date') !!}<span style="color: red">*</span>

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>

        {!! Form::text('end_date', null, ['class' => 'form-control pull-right', 'id' => 'end_date', 'placeholder' =>
        'End Date']) !!}

      </div>

      @if ($errors->has('end_date')) <p class="help-block has-error">{{ $errors->first('end_date') }}</p> @endif

    </div>

  </div>

</div> --}}


<!-- Text body Form Input -->

{{-- <div class="form-group">

  {!! Form::label('aboutCompany', 'About Company') !!}

  {!! Form::textarea('aboutCompany', null, ['class' => 'form-control ckeditor', 'id=aboutCompany', 'placeholder' =>
  'Something about company...']) !!}

</div> --}}
