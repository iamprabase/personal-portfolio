{!! Form::open(array('url' => url(domain_route("company.admin.outlets.settings.update", [$company_id])), 'method' => 'post', 'files'=> false, 'id'=>'settings-update-form', 'class'=> 'settings-update-form hidden')) !!}
  @method("patch")
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('min_order_value')) has-error @endif">
      {!! Form::label('min_order_value', 'Minimum Order Value') !!}<span style="color: red">*</span>
      <div class="input-group" style="width: 100% !important">
        <div class="input-group-addon">
          <i><b>{{$currency}}</b></i>
        </div>
        {!! Form::text('min_order_value', $min_order_value, ['class' => 'form-control pull-right', 'id' => 'min_order_value', 'autocomplete'=>'off',
        'placeholder' => 'Minimum Order Value','required', 'pattern'=>'\d+(\.\d{1,})?$', 'title'=>'Three letter country code']) !!}
      </div>
      @if ($errors->has('min_order_value')) <p class="help-block has-error">{{ $errors->first('min_order_value') }}</p> @endif
      <div class="min_order_value err_div">

      </div>
    </div>
  </div>
  
  {{-- <div class="col-xs-4">
    <div class="form-group @if ($errors->has('discount')) has-error @endif">
      {!! Form::label('order_with_amt_qty_label', 'Allow retailers to place orders with quantity only?') !!}<span style="color: red">*</span>
      <div id="mRadio">
        <label>
          {{ Form::radio('order_with_amt_qty', '1', $order_with_qty_amt==1 ? 'true=' : 'true' ,['class'=>'minimal']) }}
          Yes
        </label>
        <label>
          {{ Form::radio('order_with_amt_qty', '0', $order_with_qty_amt==0 ? 'true=' : '' ,['class'=>'minimal']) }}
          No
        </label>
      </div>
      
      @if ($errors->has('order_with_amt_qty')) <p class="help-block has-error">{{ $errors->first('order_with_amt_qty') }}</p> @endif
      <div class="order_with_amt_qty err_div">
      
      </div>
    </div>
  </div> --}}

  <div class="col-xs-4">
    {!! Form::label('', '') !!}
    <div>
      <!-- Submit Form Button -->
      {!! Form::submit('Submit', ['class' => 'btn btn-default settings-update-btn', 'id'=>'settings-product-details']) !!}
    </div>
  </div>

{!! Form::close() !!}