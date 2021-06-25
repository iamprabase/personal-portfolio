<div class="row">
<div class="col-xs-6">
  <div class="form-group @if ($errors->has('plan_name')) has-error @endif">
    {!! Form::label('name', 'Plan Name') !!}<span style="color: red">*</span>
    {!! Form::text('name', isset($plan)? $plan->name : old('name'), ['class' => 'form-control inputPlanName', 'placeholder' => 'Name of the plan', 'required' => true]) !!}
    @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
  </div>
</div>

<div class="col-xs-6">
  <div class="form-group @if ($errors->has('default_price')) has-error @endif">
    
    <div class="row">
      <div class="col-xs-4">
      {!! Form::label('currency', 'Currency ') !!}<span style="color: red">*</span>
      {!! Form::select('currency', $currency, isset($plan) ? $plan->currency_id : old('currency_id'), ['class' => 'form-control inputCurrency', 'placeholder' => 'Currency', 'required' => true]) !!}
      </div>
      <div class="col-xs-8">
      {!! Form::label('default_price', 'Default Price ') !!}<span style="color: red">*</span>
      {!! Form::text('default_price', isset($plan)? $plan->default_price : old('default_price'), ['class' => 'form-control inputDefaultPrice', 'placeholder' => 'Price', 'required' => true]) !!}
      </div>
    </div>

    
    @if ($errors->has('default_price')) <p class="help-block has-error">{{ $errors->first('default_price') }}</p> @endif
  </div>
</div>
</div>

<div class="row">
<div class="col-xs-12">
    {!! Form::label('Description','Description') !!}
    <div class="form-group">
    {!! Form::textarea('description', isset($plan)? $plan->description : old('description'), ['class'=>'form-control inputDescription']) !!}
    @if ($errors->has('description')) <p class="help-block has-error">{{ $errors->first('description') }}</p> @endif
    </div>
</div>
</div>

<div class="row">
<div class="col-xs-12">
  <div class="form-group">
    {!! Form::label('select_modules','Select Modules') !!} <span style="color: red">*</span>
    @if ($errors->has('module')) <p class="help-block has-error">{{ $errors->first('module') }}</p> @endif
    <div class="row">
      <div class="col-xs-offset-4 col-xs-4 mb-15">
        <span><input class="toggle-all-switches" type="checkbox"> </span><span style="margin: 0px 0px 0px 10px;"><label> All</label></span>
      </div>
    </div>
    <div class="moduleListing">
      <!--- Dynamic Plans Listing -->
    </div>
  </div>
</div>
</div>
