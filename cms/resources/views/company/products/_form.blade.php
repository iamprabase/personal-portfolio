<div class="row">
  <div class="{{ getClientSetting()->brand==1?'col-xs-6':'col-xs-6' }}">
    <div class="form-group @if ($errors->has('product_name')) has-error @endif">
      {!! Form::label('product_name', 'Product Name') !!}<span style="color: red">*</span>
      {!! Form::text('product_name', ($product->first())?$product->product_name:null, ['class' => 'form-control', 'placeholder' => 'Product Name','required']) !!}
      @if ($errors->has('product_name')) <p class="help-block has-error">{{ $errors->first('product_name') }}</p> @endif
    </div>
  </div>

  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('product_code')) has-error @endif">
      {!! Form::label('product_code', 'Product Code') !!}
      {!! Form::text('product_code', ($product->first())?$product->product_code:null, ['class' => 'form-control', 'placeholder' => 'Product Code']) !!}
      @if ($errors->has('product_code')) <p class="help-block has-error">{{ $errors->first('product_code') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  @if(getClientSetting()->brand==1)
    <div class="col-xs-6">
      <div class="form-group">
        {!! Form::label('brand', 'Brand') !!}
        {!! Form::select('brand', [null => 'Select a Brand'] +$brands, ($product->first())?$product->brand:'null', ['class' => 'form-control select2']) !!}
        @if ($errors->has('brand')) <p class="help-block has-error">{{ $errors->first('brand') }}</p> @endif
      </div>
    </div>
  @endif

  <div class="{{ getClientSetting()->brand==1?'col-xs-6':'col-xs-6' }}">
    <div class="form-group @if ($errors->has('category_id')) has-error @endif">
      {!! Form::label('category_id', 'Category') !!}
      {!! Form::select('category_id', [null => 'Select a Category'] +$categories, ($product->first())?$product->category_id:'null', ['class' => 'form-control select2']) !!}
      @if ($errors->has('category_id')) <p class="help-block has-error">{{ $errors->first('category_id') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-2">
    <div class="form-group" style="margin-top: 8px;">
      <strong>Mark as Starred Product</strong>
    </div>
  </div>
  <div class="col-xs-2">
    <div class="form-group" style="margin-top: 8px;">
      <label class="switch">
        @if($product->first())
          <input type="checkbox" id="star_productBTN" @if($product->star_product==1) class="ON" checked @else class="OFF" @endif><span class="slider round"></span>
          <input type="hidden" name="star_product" id="star_product" value="{{$product->star_product}}" @if($product->star_product==1) checked @endif>
        @else
          <input type="checkbox" id="star_productBTN" class="OFF"><span class="slider round"></span>
          <input type="hidden" id="star_product" name="star_product" value="0">
        @endif
      </label>
    </div>
  </div>
  @if(getClientSetting()->product_level_tax==1)
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('product_level_tax')) has-error @endif"
      style="display: inline-block; width: 100%;">
      {!! Form::label('product_level_tax', 'Select Tax Type', ['class' => 'col-xs-12 control-label']) !!}
      <div class="col-xs-12">
        {!! Form::select('tax_type[]', $tax_types, $default_taxes, ['class' => 'form-control col-xs-2 taxtype' , 'id' => 'taxtype','required'=>false, 'multiple'=> true]) !!}
      </div>
      @if ($errors->has('product_level_tax')) <p class="help-block has-error">
        {{ $errors->first('product_level_tax') }}</p> 
      @endif
    </div>
  </div>
  @endif
</div>
<div class="row">
  
</div>
<div class="row">
  <div>
    <div class="col-xs-2">
      <div class="form-group" style="margin-top: 8px;">
        <strong>Variant (ON/OFF)</strong>
      </div>
    </div>
    <div class="col-xs-2">
      <div class="form-group">
        <label class="switch">
          @if($product->first())
            @if($product->variant_flag==1)
              <input type="checkbox" id="varONOFF" class="ON" checked>
            @else
              <input type="checkbox" id="varONOFF" class="OFF" disabled>
            @endif
          @else
              <input type="checkbox" id="varONOFF" class="OFF" disabled>
          @endif
          <span class="slider round"></span>
        </label>
          @if($product->first())
            @if($product->variant_flag==1)
              <input type="hidden" name="varFlag" class="varFlag" id="varFlag" value='1'/>
            @else
              <input type="hidden" name="varFlag" class="varFlag" id="varFlag" value='0'/>
            @endif
          @else
            <input type="hidden" name="varFlag" class="varFlag" id="varFlag" value='0'/>
          @endif
      </div>
    </div>
  </div>
  @if(getClientSetting()->order_with_amt==1)
  <div class="text-center">
    <div class="col-xs-2">
      <div class="form-group" style="margin-top: 8px;">
        All prices are in
      </div>
    </div>
    <div class="col-xs-1">
      <div class="form-group">
        {!! Form::text('default_currency', getClientSetting()->default_currency, ['class' => 'form-control', 'disabled'])
        !!}
      </div>
    </div>
  </div>
  @endif
</div>
@if ($errors->has('variant.*')) <p class="help-block has-error">{{ $errors->first('variant.*') }}</p> @endif
@if ($errors->has('newrow_variant.*')) <p class="help-block has-error">{{ $errors->first('newrow_variant.*') }}</p> @endif
<div class="row">
  <div class="table-responsive make-no-reponsive col-xs-12">   
    <table class="table table-bordered" id="dynamic_field">
      <thead>
        <tr>
          <th>Variant</th>
          @if(getClientSetting()->var_colors==1)
            <th>Variant Attributes</th>
          @endif
          <th @if(getClientSetting()->order_with_amt==1) hidden @endif>Rate<span style="color: red">*</span></th>
          <th>Unit<span style="color: red">*</span></th>
          <th>Short Description</th>
          <th>Add/Remove</th>
        </tr> 
      </thead>
      <tbody>
        @if($product->first())
          @php $row=0 @endphp
          @if($productVariants->count()==0)
            @php $count=$product->count()-1 @endphp
            <tr class="rowElement" id="rowElement{{$row}}" data-row_id="{{$row}}">
              <input type="hidden" name="numofRows[]" value="{{$row}}" />
              <input type="hidden" name="productVariantIds[]" value="" />
              <td>{!! Form::text("variant[".$row."]", null,['class'=>'form-control variantClass','id'=>'variant'.$row,'placeholder' => 'Variant...', 'readonly'=>true, 'required'=>false]) !!} </td>
              @if(getClientSetting()->var_colors==1)
                <td>{!! Form::select("var_colors[".$row."][]", $colors, '', ['class' => 'form-control multiselect colorClass','id'=>'colors'.$row,'data-id'=>$row,'multiple']) !!}<span class="hidden hiddenText" id="hiddenText">Only Variants can have attributes.</span></td>
              @endif
              <td @if(config('settings.order_with_amt')==1) hidden @endif> {!! Form::text("mrp[".$row."]", $product->mrp, ['class' => 'form-control onlynumber', 'placeholder' => 'Rate', 'id'=>'mrp'.$row, 'data-id'=>$row, 'required']) !!} </td>
              <td>{!! Form::select("unit[".$row."]", [null=>null]+$units, $product->unit, ['class' => 'form-control unitClass','id'=>'unit'.$row,'data-id'=>$row,'required']) !!}</td>
              <td>{!! Form::text("short_desc[".$row."]", $product->short_desc, ['class'=>'form-control','id'=>'short_desc'.$row,'rows'=> 1, 'cols' => 54,'style' => 'resize:none', 'maxlength'=>60,'placeholder' => 'Short Description']) !!}</td>
              <td>{!! Form::button('X', ['class' => 'btn btn-danger btn-remove action-Btn hidden', 'id' =>'remove_entry'.$row,'data-id' => $row, 'data-variantid'=>null,'data-productid'=>$product->id, 'disabled'=>false]) !!}
              {!! Form::button('+', ['class' => 'btn btn-primary pull-right btn-add action-Btn', 'id' => 'add_entry'.$row, 'data-id'=>$row, 'disabled'=>true]) !!} </td>
            </tr>
          @else
            @php $count=$productVariants->count()-1 @endphp
            {{-- <input type="hidden" class="allow-edit-variant" value="1"> --}}
            @foreach($productVariants as $data)
              <tr class="rowElement" id="rowElement{{$row}}" data-row_id="{{$row}}">
                <input type="hidden" name="numofRows[]" value="{{$row}}" />
                <input type="hidden" name="productVariantIds[]" value="{{$data->id}}" />
                <td>{!! Form::text("variant[".$row."]", $data->variant, ['class'=>'form-control variantClass','id'=>'variant'.$row,'placeholder' => 'Variant...', 'readonly'=>false, 'required'=>true]) !!} </td>
                @if(getClientSetting()->var_colors==1)
                  <td>{!! Form::select("var_colors[".$row."][]", $colors, ($data->colors->count()>0)?$data->colors->pluck('id')->toArray():'', ['class' => 'form-control multiselect colorClass','id'=>'colors'.$row,'data-id'=>$row,'multiple']) !!}<span class="hidden hiddenText" id="hiddenText">Only Variants can have attributes.</span></td>
                @endif
                <td @if(config('settings.order_with_amt')==1) hidden @endif>{!! Form::text("mrp[".$row."]", $data->mrp, ['class' => 'form-control onlynumber', 'placeholder' => 'Rate', 'id'=>'mrp'.$row, 'data-id'=>$row, 'required']) !!} </td>
                <td>{!! Form::select("unit[".$row."]", [null=>null]+$units, $data->unit, ['class' => 'form-control unitClass','id'=>'unit'.$row,'data-id'=>$row,'required']) !!}</td>
                <td>{!! Form::text("short_desc[".$row."]", $data->short_desc, ['class'=>'form-control','id'=>'short_desc'.$row,'rows' => 1, 'cols' => 54,'style' => 'resize:none', 'maxlength'=>60,'placeholder' => 'Short Description']) !!}</td>
                @if($row == $count && $count!=0)
                  <td>{!! Form::button('X', ['class' => 'btn btn-danger btn-remove action-Btn', 'id' => 'remove_entry'.$row,'data-id' => $row, 'data-variantid'=>$data->id,'data-productid'=>$data->product_id, 'disabled'=>false]) !!}
                  {!! Form::button('+', ['class' => 'btn btn-primary pull-right btn-add action-Btn', 'id' => 'add_entry'.$row, 'data-id'=>$row, 'disabled'=>false]) !!} </td>
                @elseif($row == $count && $count==0)
                  <td>{!! Form::button('X', ['class' => 'btn btn-danger btn-remove action-Btn hidden', 'id' => 'remove_entry'.$row,'data-id' => $row, 'data-variantid'=>null,'data-productid'=>$data->product_id, 'disabled'=>false]) !!}
                  {!! Form::button('+', ['class' => 'btn btn-primary pull-right btn-add action-Btn', 'id' => 'add_entry'.$row, 'data-id'=>$row, 'disabled'=>false]) !!} </td>
                @else
                  <td>{!! Form::button('X', ['class' => 'btn btn-danger btn-remove action-Btn', 'id' => 'remove_entry'.$row,'data-id' => $row, 'data-variantid'=>$data->id,'data-productid'=>$data->product_id, 'disabled'=>false]) !!}
                  {!! Form::button('+', ['class' => 'btn btn-primary pull-right btn-add action-Btn hidden', 'id' => 'add_entry'.$row, 'data-id'=>$row, 'disabled'=>false]) !!} </td>
                @endif
              </tr>
            @php $row++ @endphp
            @endforeach
          @endif
        @else
          <tr class="rowElement" id="rowElement0" data-row_id="0">
            <input type="hidden" name="numofRows[]" value="0">
            <td>{!! Form::text('variant[0]',null,['class'=>'form-control variantClass','id'=>'variant0','placeholder' => 'Variant...', 'readonly'=>true]) !!} </td>
            @if(getClientSetting()->var_colors==1)
              <td>{!! Form::select('var_colors[0][]', $colors, '', ['class' => 'form-control multiselect colorClass','id'=>'colors0','data-id'=>'0','multiple']) !!}<span class="hidden hiddenText" id="hiddenText">Only Variants can have attributes.</span></td>
            @endif
            <td @if(getClientSetting()->order_with_amt==1) hidden @endif> {!! Form::text('mrp[0]', getClientSetting()->order_with_amt==1?0:null, ['class' => 'form-control onlynumber', 'placeholder' => 'Rate', 'id'=>'mrp0', 'data-id'=>'0', 'required']) !!} </td>
            <td>{!! Form::select('unit[0]', [null=>null]+$units, null, ['class' => 'form-control unitClass','id'=>'unit0','data-id'=>'0','required']) !!}</td>
            <td>{!! Form::text('short_desc[0]',null,['class'=>'form-control','id'=>'short_desc0','rows' => 1, 'cols' => 54, 'style' => 'resize:none', 'maxlength'=>60,'placeholder' => 'Short Description']) !!}</td>
            <td>{!! Form::button('X', ['class' => 'btn btn-danger btn-remove action-Btn hidden', 'id' => 'remove_entry0', 'data-id' => 0, 'disabled'=>false]) !!} 
            {!! Form::button('+', ['class' => 'btn btn-primary pull-right btn-add action-Btn', 'id' => 'add_entry0', 'data-id' => 0, 'disabled'=>true]) !!} </td>
          </tr>
        @endif
      
      </tbody>
    </table>
  </div>
  @if($getClientSetting->unit_conversion==1)
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('unit_conversion')) has-error @endif"
      style="display: inline-block; width: 100%;">
      {!! Form::label('unit_conversion', 'Select Conversions', ['class' => 'col-xs-12 control-label']) !!}
      <div class="col-xs-12">
        {!! Form::select('unit_conversion[]', $conversions[1], $product->first()?$product->conversions->pluck('id')->toArray():null, ['class' => 'form-control col-xs-2 unit_conversion' , 'id' => 'unit_conversion','required'=>false, 'multiple'=> true], $conversions[2]) !!}
      </div>
      @if ($errors->has('unit_conversion')) <p class="help-block has-error">
        {{ $errors->first('unit_conversion') }}</p>
      @endif
    </div>
  </div>
  @endif
</div>


<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('prodImage')) has-error @endif">
      <label>Image</label>
      <small> Size of image should not be more than 2MB.</small>
      <div class="input-group" style="margin-bottom:10px;">
        <span class="input-group-btn">
          <span class="btn btn-default btn-file prodImagefile">
            Browse… {!! Form::file('prodImage', ['id'=>'imgInp']) !!}
          </span>
        </span>
        <input type="text" class="form-control" readonly>
      </div>
      <img id='img-upload' class="img-responsive"
        src="@if(isset($product->image_path)){{ URL::asset('cms'.$product->image_path) }} @endif" />
    </div>
    @if ($errors->has('prodImage')) <p class="help-block has-error">{{ $errors->first('prodImage') }}</p> @endif
  </div>
  @if(Auth::user()->can('product-status'))
  <div class="col-xs-6">
    <div class="form-group">
      {!! Form::label('status', 'Status') !!}
      {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'),
      isset($product->status)?$product->status:'Active', ['class' => 'form-control']) !!}
    </div>
  </div>
  @endif
  <div class="col-xs-12">
    <div class="form-group">
      {!! Form::label('details', 'About Product') !!}
      {!! Form::textarea('details', ($product->first())?$product->details:null, ['class' => 'form-control ckeditor', 'id=details', 'placeholder' => 'Something
      about Product...']) !!}
    </div>

  </div>
</div>