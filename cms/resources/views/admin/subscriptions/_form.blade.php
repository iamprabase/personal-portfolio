<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('plan_id')) has-error @endif">
      {!! Form::label('plan_id','Plan') !!}<span style="color: red">*</span>
      {!! Form::select('plan_id', $plans, isset($subscription) ? $subscription->plan_id : old('plan_id'), ['class' => 'form-control inputPaymentOption', 'placeholder' => 'Select Plan', 'required' => true]) !!}
      @if ($errors->has('plan_id')) <p class="help-block has-error">{{ $errors->first('plan_id') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('name')) has-error @endif">
      {!! Form::label('name', 'Subscription Name') !!}<span style="color: red">*</span>
      {!! Form::text('name', isset($subscription)? $subscription->name : old('name'), ['class' => 'form-control inputSubscriptionName', 'placeholder' => 'Name of the subscription', 'required' => true]) !!}
      @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('currency_id')) has-error @endif">
      {!! Form::label('currency_id','Currency') !!}<span style="color: red">*</span>
      {!! Form::select('currency_id', $currency, isset($subscription) ? $subscription->currency_id : old('currency_id'), ['class' => 'form-control inputCurrency', 'placeholder' => 'Select Currency', 'required' => true]) !!}
      @if ($errors->has('currency_id')) <p class="help-block has-error">{{ $errors->first('currency_id') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('domain')) has-error @endif">
      {!! Form::label('domain', 'Domain') !!}
      {!! Form::text('domain', isset($subscription)? $subscription->domain : old('domain'), ['class' => 'form-control inputSubscriptionDomain', 'placeholder' => 'Domain', 'required' => false]) !!}
      @if ($errors->has('domain')) <p class="help-block has-error">{{ $errors->first('domain') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('phone')) has-error @endif">
      {!! Form::label('phone', 'Phone') !!}<span style="color: red">*</span>
      <div class="row">
        <div class="col-xs-4">
        {!! Form::select('extension', $extension, isset($subscription) ? $subscription->extension : old('extension'), ['class' => 'form-control inputExtension', 'placeholder' => 'Extension', 'required' => true]) !!}
        </div>
        <div class="col-xs-8">
        {!! Form::text('phone', isset($subscription)? $subscription->phone : old('phone'), ['class' => 'form-control inputSubscriptionPhone', 'placeholder' => 'Phone', 'required' => true]) !!}
        </div>
      </div>
      @if ($errors->has('phone')) <p class="help-block has-error">{{ $errors->first('phone') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('email')) has-error @endif">
      {!! Form::label('email', 'Email') !!}
      {!! Form::text('email', isset($subscription)? $subscription->email : old('email'), ['class' => 'form-control inputSubscriptionEmail', 'placeholder' => 'Email', 'required' => false]) !!}
      @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('min_users')) has-error @endif">
      {!! Form::label('min_users', 'Minimum Users') !!}<span style="color: red">*</span>
      {!! Form::number('min_users', isset($subscription)? $subscription->min_users : old('min_users'), ['class' => 'form-control inputMinUsers', 'placeholder' => 'Users', 'required' => true, 'min' => 0]) !!}
      @if ($errors->has('min_users')) <p class="help-block has-error">{{ $errors->first('min_users') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('price_per_user')) has-error @endif">
      {!! Form::label('price_per_user', 'Price per User ') !!}<span style="color: red">*</span>
      {!! Form::text('price_per_user', isset($subscription)? $subscription->price_per_user : old('price_per_user'), ['class' => 'form-control inputSetupFee priceValidate', 'placeholder' => 'Price per USer', 'required' => true]) !!}
      @if ($errors->has('price_per_user')) <p class="help-block has-error">{{ $errors->first('price_per_user') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('payment_option')) has-error @endif">
      {!! Form::label('payment_option','Payment Option') !!}<span style="color: red">*</span>
      {!! Form::select('payment_option[]', $payment_option, isset($subscription) ? $subscription->payment_option : old('payment_option'), ['class' => 'form-control inputPaymentOption', 'placeholder' => 'Select Payment Option', 'required' => true, 'multiple' => true]) !!}
      @if ($errors->has('payment_option')) <p class="help-block has-error">{{ $errors->first('payment_option') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('setup_fee')) has-error @endif">
      {!! Form::label('setup_fee', 'Setup Fee ') !!}<span style="color: red">*</span>
      {!! Form::text('setup_fee', isset($subscription)? $subscription->setup_fee : old('setup_fee'), ['class' => 'form-control inputSetupFee priceValidate', 'placeholder' => 'Setup Price', 'required' => true]) !!}
      @if ($errors->has('setup_fee')) <p class="help-block has-error">{{ $errors->first('setup_fee') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('trial_days')) has-error @endif">
      {!! Form::label('trial_days', 'Trial Days') !!}<span style="color: red">*</span>
      {!! Form::number('trial_days', isset($subscription)? $subscription->trial_days : old('trial_days'), ['class' => 'form-control inputTrialDays', 'placeholder' => 'Users', 'required' => true, 'min' => 0]) !!}
      @if ($errors->has('trial_days')) <p class="help-block has-error">{{ $errors->first('trial_days') }}</p> @endif
    </div>
  </div>
  <div class="row">
    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('expiry_after_current_billing')) has-error @endif">
        {!! Form::label('expiry_after_current_billing', 'Renewal') !!}
        <span class="checkbox" style="margin-top: 2px;">
          <label style="padding: 0px;">
            {{ Form::radio('expiry_after_current_billing', '0' , isset($subscription)?($subscription->expiry_after_current_billing==0)?true:false:false, ['class'=>'minimal', 'id'=>'expiry_after_current_billing_0']) }}
            Auto-Renewed
          </label>
          <label>
            {{ Form::radio('expiry_after_current_billing', '1' , isset($subscription)?($subscription->expiry_after_current_billing==1)?true:false:false, ['class'=>'minimal', 'id'=>'expiry_after_current_billing_1']) }}
            Expire After Current Renewal
          </label>
        </span>
        @if ($errors->has('expiry_after_current_billing')) <p class="help-block has-error">{{ $errors->first('expiry_after_current_billing') }}</p> @endif
      </div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('auto_renewal_time')) has-error @endif autoRnTime hidden">
      {!! Form::label('auto_renewal_time', 'Auto Renewal Time (Days)') !!}<span style="color: red">*</span>
      {!! Form::number('auto_renewal_time', isset($subscription)? $subscription->auto_renewal_time : 0, ['class' => 'form-control inputAutoRenewalTime', 'placeholder' => 'Auto Renewal Time', 'required' => false]) !!}
      @if ($errors->has('auto_renewal_time')) <p class="help-block has-error">{{ $errors->first('auto_renewal_time') }}</p> @endif
    </div>
  </div>
  <!-- <div class="col-xs-6">
    <div class="form-group @if ($errors->has('price')) has-error @endif">
      {!! Form::label('price', 'Subscription Price ') !!}<span style="color: red">*</span>
      {!! Form::text('price', isset($subscription)? $subscription->price : old('price'), ['class' => 'form-control inputDefaultPrice priceValidate', 'placeholder' => 'Price', 'required' => true]) !!}
      @if ($errors->has('price')) <p class="help-block has-error">{{ $errors->first('price') }}</p> @endif
    </div>
  </div> -->
  <!-- <div class="col-xs-4 mt-30">
    <div class="form-group @if ($errors->has('auto_renewal')) has-error @endif">
      <span>
      {!! Form::checkbox('auto_renewal', 1) !!}
      <span style="margin: 0px 0px 0px 10px;"> 
      {!! Form::label('auto_renewal','Auto Renewal') !!} </span>
    </div>
  </div> -->
</div>

<div class="row">
  <h4>
    {!! Form::label('extra_charge','Extra Charges') !!}
  </h4>
  @if ($errors->has('charge_name')) <p class="help-block has-error">{{ $errors->first('charge_name') }}</p> @endif
  @if ($errors->has('charge_price')) <p class="help-block has-error">{{ $errors->first('charge_price') }}</p> @endif
  @if ($errors->has('price_type')) <p class="help-block has-error">{{ $errors->first('price_type') }}</p> @endif
</div>
<div class="row">
  <div class="form-group">
      <table class="table table-striped extraCharge">
        <thead>
            <tr style="background-color: #f16022;color: white;">
                <th>Name</th>
                <th>Price</th>
                <th>Price Type</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
          @if(old('chargeIndexes'))
          @foreach(old('chargeIndexes') as $free_feature => $val)
            <tr>
              <input type="hidden" name="chargeIndexes[]">
              <td>
                {!! Form::text("charge_name[]", old('charge_name')[$free_feature], ["class" => "form-control", "placeholder" => "Charge Title", "required" => true]) !!}
              </td>
              <td>
                {!! Form::text("charge_price[]", old('charge_price')[$free_feature], ["class" => "form-control priceValidate", "placeholder" => "Charge", "required" => true]) !!}
              </td>
              <td>
                @php $chargeType = old('price_type')[$free_feature] @endphp
                <select class="form-control" name="price_type[]" required>
                  <option value="">Select Charge Type</option>
                  <option value="Per User" @if($chargeType=="Per User") selected="selected" @endif>Per User</option>
                  <option value="Fixed" @if($chargeType=="Fixed") selected="selected" @endif>Fixed</option>
                </select>
              </td>
              <td>
                {!! Form::button("Remove Field", ["class" => "btn btn-danger pull-right removeFieldBtn", "onclick" => "removeField(this)"]) !!}
              </td>
            </tr>
            @endforeach
          @endif
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4">
            {!! Form::button('Add Charge Field', ['class' => 'btn btn-primary pull-right addFieldBtn' , 'onclick' => "addField()"]) !!}
            </td>
          </tr>
        </tfoot>
      </table>
  </div>
</div>

<div class="row">
  <h4>
    {!! Form::label('free_feature','Free Features') !!}
  </h4>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="form-group">
      <p class="flex">
      <span class="mr-15">Number of Users</span>
      <span class="mr-15">
      {!! Form::number('free_feature[free_num_users_months][users]', isset($subscription)? $subscription->free_feature : old('free_feature[free_num_users_months][users]'), ['class' => 'form-control inputNumFreeUsersMonthsU', 'placeholder' => '0', 'required' => false, 'min' => 0, 'value' => "0"]) !!}
      </span>
      <span class="mr-15">free for</span>
      <span class="mr-15">
      {!! Form::number('free_feature[free_num_users_months][months]', isset($subscription)? $subscription->free_feature : old('free_feature[free_num_users_months][months]'), ['class' => 'form-control inputNumFreeUsersMonthsM', 'placeholder' => '0', 'required' => false, 'min' => 0, 'value' => "0", 'step' => 0.1]) !!}
      </span>
      <span class="mr-15">months</span>
      </span>
      </p>
      @if ($errors->has('free_num_users_months')) <p class="help-block has-error">{{ $errors->first('free_num_users_months') }}</p> @endif  
      </span>
    </div>
  </div>
  <div class="col-xs-12">
    <div class="form-group">
      <p class="flex">
      <span class="mr-15">Number of Months</span>
      <span class="mr-15">
      {!! Form::number('free_feature[free_num_months_users][months]', isset($subscription)? $subscription->free_feature : old('free_feature[free_num_months_users][months]'), ['class' => 'form-control inputNumFreeMonthsUsersM', 'placeholder' => '0', 'required' => false, 'min' => 0, 'step' => 0.1]) !!}
      </span>
      <span class="mr-15">free for</span>
      <span class="mr-15">
      {!! Form::number('free_feature[free_num_months_users][users]', isset($subscription)? $subscription->free_feature : old('free_feature[free_num_months_users][users]'), ['class' => 'form-control inputNumFreeMonthsUsersU', 'placeholder' => '0', 'required' => false, 'min' => 0]) !!}
      </span>
      <span class="mr-15">users</span>
      </span>
      </p>
      @if ($errors->has('free_num_months_users')) <p class="help-block has-error">{{ $errors->first('free_num_months_users') }}</p> @endif  
      </span>
    </div>
  </div>
</div>