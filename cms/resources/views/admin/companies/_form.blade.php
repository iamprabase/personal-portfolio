<div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('company_name')) has-error @endif">

      {!! Form::label('company_name', 'Company Name') !!}<span style="color: red">*</span>

      {!! Form::text('company_name', null, ['class' => 'form-control', 'placeholder' => 'Name of the company']) !!}

      @if ($errors->has('company_name')) <p class="help-block has-error">{{ $errors->first('company_name') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('domain')) has-error @endif">

      {!! Form::label('domain', 'Domain ') !!}<span style="color: red">*</span>
      <small>A unique name. Will be used as sub domain</small>

      {!! Form::text('domain', null, ['class' => 'form-control', 'placeholder' => 'Alias-A unique name for sub-domain', isset($company->domain)? 'readonly':'']) !!}

      @if ($errors->has('domain')) <p class="help-block has-error">{{ $errors->first('domain') }}</p> @endif

    </div>

  </div>

</div>


<div class="row">

  <div class="col-xs-4">

    <div class="form-group @if ($errors->has('contact_phone')) has-error @endif">

      {!! Form::label('contact_phone', 'Phone') !!}<span style="color: red">*</span>

      {!! Form::text('contact_phone', null, ['class' => 'form-control', 'placeholder' => 'Phone No.']) !!}

      @if ($errors->has('contact_phone')) <p
          class="help-block has-error">{{ $errors->first('contact_phone') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-2">

    <div class="form-group">

      {!! Form::label('extNo', 'Ext. No.') !!}

      {!! Form::text('extNo', null, ['class' => 'form-control', 'placeholder' => 'Ext. No.']) !!}

    </div>

  </div>

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('contact_email')) has-error @endif">

      {!! Form::label('contact_email', 'Email') !!}<span style="color: red">*</span>
      <small>Will be used as User Name</small>

      {!! Form::text('contact_email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}

      @if ($errors->has('contact_email')) <p
          class="help-block has-error">{{ $errors->first('contact_email') }}</p> @endif

    </div>

  </div>

</div>


<div class="row">

  <div class="col-xs-4">

    <div class="form-group @if ($errors->has('plan')) has-error @endif">

      {!! Form::label('plan', 'Plan') !!}

      @if(isset($company))
        <select name="plan" class="form-control">
          @foreach($plans as $plan)
          <option @if($company->plan_id==$plan->id) selected="selected" @endif value="{{$plan->id}}">{{$plan->name}} {{($plan->custom!=0)?" (Custom)":""}}</option>
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
  
    <div class="form-group @if ($errors->has('num_users')) has-error @endif">
  
      {!! Form::label('num_users', 'No of users') !!}<span style="color: red">*</span>
  
      {!! Form::text('num_users', isset($company)?$company->num_users:null, ['class' => 'form-control', 'placeholder' => 'No. of Users']) !!}
  
      @if ($errors->has('num_users')) <p class="help-block has-error">{{ $errors->first('num_users') }}</p> @endif
  
    </div>
  
  </div>

  <div class="col-xs-4">
  
    <div class="form-group">
  
      {!! Form::label('pan', 'PAN/VAT') !!}
  
      {!! Form::text('pan', null, ['class' => 'form-control', 'placeholder' => 'PAN/VAT']) !!}
  
    </div>
  
  </div>
</div>

<div class="row">

  <div class="col-xs-6">

    <div class="form-group">

      {!! Form::label('status', 'Account Status') !!}

      @if(isset($company->is_active))

      {!! Form::select('is_active', array('2' => 'Active', '1' => 'Disabled', '0' => 'Expired'),
      $company->is_active, ['class' => 'form-control']) !!}

      @else

      {!! Form::select('is_active', array('2' => 'Active', '1' => 'Disabled', '0' => 'Expired'),
      'Active', ['class' => 'form-control']) !!}

      @endif

    </div>


  </div>

  <div class="col-xs-6">

    <div class="form-group">

      {!! Form::label('customer_status', 'Customer Status') !!}

      @if(isset($company->customer_status))

      {!! Form::select('customer_status', array('customer' => 'Customer', 'trial' => 'Trial'),
      $company->customer_status,
      ['class' => 'form-control']) !!}

      @else

      {!! Form::select('customer_status', array('customer' => 'Customer', 'trial' => 'Trial'), 'trial', ['class' =>
      'form-control']) !!}

      @endif

    </div>


  </div>


</div>

<div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('start_date')) has-error @endif">

      {!! Form::label('start_date', 'Start Date') !!}<span style="color: red">*</span>

      <div class="input-group date">

        <div class="input-group-addon">

          <i class="fa fa-calendar"></i>

        </div>

        {!! Form::text('start_date', null, ['class' => 'form-control pull-right', 'id' => 'start_date', 'placeholder' => 'Start Date']) !!}

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

        {!! Form::text('end_date', null, ['class' => 'form-control pull-right', 'id' => 'end_date', 'placeholder' => 'End Date']) !!}

      </div>

      @if ($errors->has('end_date')) <p class="help-block has-error">{{ $errors->first('end_date') }}</p> @endif

    </div>

  </div>

</div>


<!-- Text body Form Input -->

<div class="form-group">

  {!! Form::label('aboutCompany', 'About Company') !!}

  {!! Form::textarea('aboutCompany', null, ['class' => 'form-control ckeditor', 'id=aboutCompany', 'placeholder' => 'Something about company...']) !!}

</div>


<div class="row">

  <div class="col-xs-6">

    <div class="form-group @if ($errors->has('contact_name')) has-error @endif">

      {!! Form::label('contact_name', 'Conatct Person Name') !!}<span style="color: red">*</span>

    <!--  {!! Form::text('contact_name', null, ['class' => 'form-control', 'placeholder' => 'Contact Person Name']) !!} -->

      <input class="form-control" placeholder="Contact Person Name" name="contact_name" type="text" id="contact_name"
             value="{{ (isset($manager->contact_name))?$manager->contact_name:'' }}">

      @if ($errors->has('contact_name')) <p class="help-block has-error">{{ $errors->first('contact_name') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-6">
        <div class="form-group @if ($errors->has('time_zone')) has-error @endif">
          {!! Form::label('time_zone', 'Time Zone') !!} <span style="color: red">*</span>
          <select name="time_zone" class="form-control" required>
            <!-- <option value="Asia/Kathmandu">{{$timezonelist['Asia/Kathmandu']}}</option> -->
            @foreach (timezone_identifiers_list() as $timezone)
            <option value="{{ $timezone }}" @if(isset($current_time_zone)) {{ $timezone == $current_time_zone ? ' selected' : '' }} @endif>
              {{ $timezone }}</option>
            @endforeach
          </select>
          @if ($errors->has('time_zone')) <p class="help-block has-error">{{ $errors->first('time_zone') }}</p> @endif
        </div>
      </div>

  <div class="col-xs-6">

  <!-- <div class="form-group @if ($errors->has('email')) has-error @endif">

		    {!! Form::label('email', 'Email') !!}<span style="color: red">*</span><small>Will be used as User Name</small>

		    {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}

  @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif

      </div> -->

  </div>

</div>


<div class="row">

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

</div>
{{-- <div class="row">
  <div class="col-sm-6">
    <div class="form-group @if($errors->has('is_live')) has-error @endif">
      {!! Form::label('is_live', 'Account') !!}<span style="color: red">*</span>
      <div class="row">
        @if(isset($company))
        <div class="col-sm-4">
          <div class="radio">
            <label><input type="radio" name="is_live" @if($company->is_live==0) checked @endif value="0">Demo</label>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="radio">
            <label><input type="radio" name="is_live" @if($company->is_live==1) checked @endif value="1">Live</label>
          </div>
        </div>
        @else
        <div class="col-sm-4">
          <div class="radio">
            <label><input type="radio" name="is_live" checked value="0">Demo</label>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="radio">
            <label><input type="radio" name="is_live" value="1">Live</label>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-sm-6"></div>
</div> --}}