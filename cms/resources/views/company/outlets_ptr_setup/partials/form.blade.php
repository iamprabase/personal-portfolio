  
  {!! Form::open(array('url' => url(domain_route("company.admin.outlets.ptr.productUpdate")), 'method' => 'post', 'files'=> false, 'id'=>'product-update-form')) !!}
  @method("patch")
  <div class="col-xs-12">
    <div class="form-group @if ($errors->has('moq')) has-error @endif">
      {!! Form::label('moq', 'Maximum Order Quantity for the Product') !!}<span style="color: red">*</span>
      <div class="input-group" style="width: 100% !important">
        {!! Form::number('moq', null, ['class' => 'form-control pull-right', 'id' => 'moq', 'autocomplete'=>'off',
        'placeholder' => 'Maximum Order Quantity','required']) !!}
      </div>
      @if ($errors->has('moq')) <p class="help-block has-error">{{ $errors->first('moq') }}</p> @endif
      <div class="moq err_div">

      </div>
    </div>
  </div>
  
  <div class="col-xs-12">
    <div class="form-group @if ($errors->has('discount')) has-error @endif">
      {!! Form::label('discount', 'Product Discount') !!}<span style="color: red">*</span>
      <div class="input-group" style="width: 100% !important">
        {!! Form::text('discount', null, ['class' => 'form-control pull-right', 'id' => 'discount', 'autocomplete'=>'off', 'placeholder' => 'Product Discount','required']) !!}
      </div>
  
      @if ($errors->has('discount')) <p class="help-block has-error">{{ $errors->first('discount') }}</p> @endif
      <div class="discount err_div">
      
      </div>
    </div>
  </div>
  {!! Form::hidden('product_id', null, ['id' => 'product_id','required']) !!}
  {!! Form::hidden('variant_id', null, ['id' => 'variant_id','required']) !!}
  {!! Form::hidden('visibility', null, ['id' => 'visibility','required']) !!}

  <div class="col-xs-12">
    <!-- Submit Form Button -->
    {!! Form::submit('Submit', ['class' => 'btn btn-default product-update-btn pull-right', 'id'=>'update-product-details']) !!}
    {!! Form::button('Close', ['class' => 'btn btn-warning pull-right', 'data-dismiss'=>'modal'])
    !!}
  </div>

  {!! Form::close() !!}