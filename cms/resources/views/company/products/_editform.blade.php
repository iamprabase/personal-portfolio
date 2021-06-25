<div class="row">
  <div class="{{ getClientSetting()->brand==1?'col-xs-4':'col-xs-6' }}">
    <div class="form-group @if ($errors->has('product_name')) has-error @endif">
      {!! Form::label('product_name', 'Product Name') !!}<span style="color: red">*</span>
      {!! Form::text('product_name', null, ['class' => 'form-control', 'placeholder' => 'Product Name','required']) !!}
      @if ($errors->has('product_name')) <p class="help-block has-error">{{ $errors->first('product_name') }}</p> @endif
    </div>
  </div>
  @if(getClientSetting()->brand==1)
    <div class="col-xs-4">
      <div class="form-group @if ($errors->has('brand')) has-error @endif">
        {!! Form::label('brand', 'Brand') !!}
        {!! Form::select('brand', [null => 'Select a Brand'] +$brands, isset($product->brand)?$product->brand:'null',
        ['class' => 'form-control select2']) !!}
        @if ($errors->has('brand')) <p class="help-block has-error">{{ $errors->first('brand') }}</p> @endif
      </div>
    </div>
  @endif
  <div class="{{ getClientSetting()->brand==1?'col-xs-4':'col-xs-6' }}">
    <div class="form-group @if ($errors->has('category_id')) has-error @endif">
      {!! Form::label('category_id', 'Category') !!}
      {!! Form::select('category_id', [null => 'Select a Category'] +$categories,
      isset($product->category_id)?$product->category_id:'null', ['class' => 'form-control select2']) !!}
      @if ($errors->has('category_id')) <p class="help-block has-error">{{ $errors->first('category_id') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-2">
    <div class="form-group" style="margin-top: 8px;">
      All prices are in
    </div>
  </div>
  <div class="col-xs-1">
    <div class="form-group">
      {!! Form::text('default_currency', $settings->default_currency, ['class' => 'form-control', 'disabled']) !!}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-2">
    <div class="form-group" style="margin-top: 8px;">
      <strong>Variant (ON/OFF)</strong>
    </div>
  </div>
  <div class="col-xs-2">
    <div class="form-group">
      <label class="switch">
        <input type="checkbox" id="varONOFF" class="@if($product->variant_flag==" 1")ON @else OFF @endif"
          @if($product->variant_flag=="1")checked @endif>
        <span class="slider round"></span>
      </label>
    </div>
  </div>
</div>
<div class="">
  <div class="table-responsive">
    <table class="table table-bordered" id="dynamic_field">
      <thead>
        <tr>
          <th>Variant</th>
          @if(getClientSetting()->var_colors==1)
            <th>Choose Colors</th>
          @endif
          <th>MRP<span style="color: red">*</span></th>
          <th>Unit<span style="color: red">*</span></th>
          <th>Short Description</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @php $index=-1 @endphp
        @if($product->variant_flag==1)
          @foreach($product_variant as $product)
            <tr id="rowElement{{++$index}}" class="rowElement delRow existEd" data-id="{{$index}}">
              {{-- <td>{!! Form::text('variant[$index]',isset($product->variant)?$product->variant:'',['class'=>'variantClass
                form-control','id'=>'variant'.$index.'','placeholder' => 'Variant Name', 'required']) !!} </td> --}}
              <td>
                <input class="variantClass form-control" id="variant1" placeholder="Variant Name" required="" name="variant[{{$index}}]" type="text" value="{{$product->variant}}">
              </td>
              @if(getClientSetting()->var_colors==1)
                <td>
                  <select class='form-control multiselect colorClass' name='var_colors[{{$index}}][]' id='colors{{$index}}' data-id='{{$index}}' multiple><option value="0" @if(!isset($product->variant_colors)) selected @endif>No Colors Selected</option>@foreach($colors as $hexcode=>$color) <option value="{{$hexcode}}" @if(isset($product->variant_colors)) @if(in_array($hexcode, json_decode($product->variant_colors))) selected @endif @endif>{{$color}}</option>@endforeach</select>
                </td>
              @endif
              {{-- <td> {!! Form::text('mrp[$index]', $product->mrp, ['class' => 'form-control onlynumber', 'placeholder' =>
                'MRP','required','id'=>'mrp'.$index.'', 'onkeypress'=>"return isNumberKey(event)"]) !!} </td> --}}
              <td>
                <input class="form-control onlynumber" placeholder="MRP" required="" id="mrp{{$index}}" onkeypress="return isNumberKey(event)"
                  name="mrp[{{$index}}]" type="text" value="{{$product->mrp}}"> 
              </td>
              {{-- <td>{!! Form::select('unit[{{$index}}]', [null => 'Select a Unit'] +$units, isset($product->unit)?$product->unit:'null',
                ['class' => 'form-control select2','id'=>'unit'.$index.'','required']) !!}</td> --}}
              <td>
                <select class='form-control select2' name='unit[{{$index}}]' id='unit{{$index}}' data-id='{{$index}}' required>
                  <option></option>@foreach($units as $val=>$unit) <option value="{{$val}}" @if(isset($product->unit)) @if($val==$product->unit) selected @endif @endif>{{$unit}}</option>@endforeach
                </select>
              </td>
              {{-- <td>{!!Form::text('short_desc[$index]',isset($product->short_desc)?$product->short_desc:'',['class'=>'form-control','id'=>'short_desc'.$index.'','rows'
                => 1, 'cols' => 54, 'style' => 'resize:none', 'maxlength'=>60,'placeholder' => 'Short Description']) !!}
              </td> --}}
              <td>
                <input class="form-control" id="short_desc{{$index}}" rows="{{$index}}" cols="54" style="resize:none" maxlength="60" placeholder="Short Description" name="short_desc[{{$index}}]" type="text" value="{{$product->short_desc}}">
              </td>

              <td @if(getOrderDetails($product->id)!=0)class="hasOrders" @else class="hasNoOrders" @endif>
                @if($index==$product_variant->count())
                @if(getOrderDetails($product->id)==0)
                <a class="btn btn-danger pull-right" id="remove_entry_del" data-eid="{{$index}}" data-vid="{{$product->id}}"
                  data-url="{{ domain_route('company.admin.product.destroy', [$product->id]) }}" data-toggle="modal"
                  data-target="#deletevar" style="width: 50% !important">X</a>
                @endif
                <input type="button"
                  class="btn btn-primary pull-right add_moreEntry @if(getOrderDetails($product->id)!=0)hasOrders @endif"
                  id="add_more_entry" data-id="{{$index}}" style="width: 100%" value="+" />
                @else
                @if(getOrderDetails($product->id)==0)
                <a class="btn btn-danger pull-right" id="remove_entry_del" data-vid="{{$product->id}}" data-eid="{{$index}}"
                  data-url="{{ domain_route('company.admin.product.destroy', [$product->id]) }}" data-toggle="modal"
                  data-target="#deletevar" style="width: 50% !important">X</a>
                @endif
                @endif
              </td>
              <input type="hidden" name="product_variant_id[{{$index}}]" value="{{ $product->id }}" />
            </tr>

          @endforeach
          <input type="hidden" value="0" id="exist_ed" name="exist_ed" />
        @else

          <tr id="rowElement{{++$index}}" class="rowElement delRow" data-id="{{$index}}">
            <td>{!! Form::text('variant[]',isset($product->variant)?$product->variant:'',['class'=>'variantClass
              form-control','id'=>'variant'.$index.'','placeholder' => 'Variant Name', 'required']) !!} </td>
            @if(getClientSetting()->var_colors==1)
              <td>
                <p class="@if(!isset($product->variant_colors)) hidden @endif" id="hiddenText">Turn On the Variant Field to choose colors.</p>
                <select class='form-control multiselect colorClass' name='var_colors[{{$index}}][]' id='colors{{$index}}' data-id='{{$index}}'multiple><option value="0" @if(!isset($product->variant_colors)) selected @endif>No Colors Selected</option>@foreach($colors as $hexcode=>$color) <option value="{{$hexcode}}" @if(isset($product->
                  variant_colors)) @if(in_array($hexcode, json_decode($product->variant_colors))) selected @endif @endif>{{$color}}
                </option>@endforeach</select>
              </td>
            @endif
            <td> {!! Form::text('mrp[]', $product->mrp, ['class' => 'form-control onlynumber', 'placeholder' =>
              'MRP','required','id'=>'mrp'.$index.'', 'onkeypress'=>"return isNumberKey(event)"]) !!} </td>
            <td>{!! Form::select('unit[]', [null => 'Select a Unit'] +$units, isset($product->unit)?$product->unit:'null',
              ['class' => 'form-control select2','id'=>'unit'.$index.'','required']) !!}</td>
            <td>{!!
              Form::text('short_desc[]',isset($product->short_desc)?$product->short_desc:'',['class'=>'form-control','id'=>'short_desc'.$index.'','rows'
              => 1, 'cols' => 54, 'style' => 'resize:none', 'maxlength'=>60,'placeholder' => 'Short Description']) !!}
            </td>

            <td @if(getproductOrderDetails($product->id)!=0)class="hasOrders" @else class="hasNoOrders" @endif>
              @if(getproductOrderDetails($product->id)==0)
              <a class="btn btn-danger pull-right" id="remove_entry_del" data-vid="{{$product->id}}" data-eid="{{$index}}"
                data-url="{{ domain_route('company.admin.product.destroy', [$product->id]) }}" data-toggle="modal"
                data-target="#deletevar" style="width: 50% !important">X</a>
              @endif
              <input type="button"
                class="btn btn-primary pull-right add_moreEntry @if(getproductOrderDetails($product->id)!=0)hasOrders @endif"
                id="add_more_entry" data-id="{{$index}}" style="width: 100%" value="+" />
            </td>

            <input type="hidden" name="product_id" value="{{ $product->id }}" />

          </tr>
          <input type="hidden" value="1" id="exist_ed" name="exist_ed" />
        @endif

        <input type="hidden" @if($product->variant_flag=="1") value='1' @else value='0' @endif name="varFlag" class="varFlag" id="varFlag"/>
        <input type="hidden" value="0" id="new_el_count" name="new_el_count" />
      </tbody>
    </table>
  </div>
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
  <div class="col-xs-6">
    <div class="form-group">
      {!! Form::label('status', 'Status') !!}
      {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'),
      isset($product->status)?$product->status:'Active', ['class' => 'form-control']) !!}
    </div>
  </div>
  <div class="col-xs-12">
    <div class="form-group">
      {!! Form::label('details', 'About Product') !!}
      {!! Form::textarea('details', null, ['class' => 'form-control ckeditor', 'id=details', 'placeholder' => 'Something
      about Product...']) !!}
    </div>
  </div>
</div>

<script>
  $(function () {
    $('#deletevar').on('show.bs.modal', function (event) {
      if($('.delRow').length==1 ){
        var button_eid = $(event.relatedTarget);
        let eid = button_eid.data("eid");
        return false;
      }
      var button = $(event.relatedTarget);
      var url = button.data('url');
      var vid = button.data('vid');
      $('#p_vid').val(vid);
      $(".remove-record-model").attr("action", url);
      var modal = $(this);
    });
  });
  $('document').ready(function(){
    if($('.delRow').length==1){
      $('#remove_entry_del').hide();
    }
  });
</script>