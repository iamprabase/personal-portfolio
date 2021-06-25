<div class="table-responsive">
<input type="text" name="previous_url" value="{{URL::previous()}}" hidden />
<div class="row">
  @php $col_gap = $partyTypeLevel ? 4 : 6 @endphp
  <div class="col-xs-{{$col_gap}}">
    <div class="form-group @if ($errors->has('client_id')) has-error @endif">
      {!! Form::label('client_id', 'Party Name') !!}<span style="color: red">*</span>
      <div class="input-group" style="width: 100% !important">
        <select name="client_id" class= "form-control select2" id="order_clients">
          <option selected disabled>Select Party Name</option>
          @foreach($clients as $client)
            <option value="{{$client['id']}}"  
              data-rate="{{$client['rate_id']}}"
              data-clienttype="{{$client['client_type']}}"
              data-superior="{{$client['superior']}}"
              {{isset($order)?$order->client_id==$client['id']?"selected":null:null}}
            >{{$client['company_name']}}</option>
          @endforeach
        </select>
      </div>
      @if ($errors->has('client_id')) <p class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
    </div>
  </div>
  @if($partyTypeLevel)
    <div class="col-xs-{{$col_gap}}">
      <div class="form-group @if ($errors->has('orderTo')) has-error @endif">
        {!! Form::label('orderTo', 'Order To') !!}
        <!-- <span style="color: red">*</span> -->
        <div class="input-group" style="width: 100% !important">

          <select name="orderTo" class= "form-control select2" id="orderTo">
            <option selected disabled>Select Order To</option>
          </select>
        </div>

      </div>
    </div>
  @endif
  <div class="col-xs-{{$col_gap}}">
    <div class="form-group @if ($errors->has('order_date')) has-error @endif">
      {!! Form::label('order_date', 'Order Date') !!}<span style="color: red">*</span>
      @if(config('settings.ncal')==1)
        <input type="hidden" id="englishDate" name="englishDate">
      @endif
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        {!! Form::text('order_date', /*$order->order_date*/null, ['class' => 'form-control pull-right', 'id' => 'order_datenew', 'autocomplete'=>'off', 'placeholder' => 'Order Date','required', isset($order)?'readonly':""]) !!}
      </div>

      @if ($errors->has('order_date')) <p class="help-block has-error">{{ $errors->first('order_date') }}</p> @endif
    </div>
  </div>
</div>
{{-- <div class="table-responsive"> --}}
  <table class="table table-bordered table-responsive" id="dynamic_field">
    <thead>
      <tr>
        <th>Product<span style="color: red">*</span></th>
        <th>Variant</th>
  
        @if($getClientSetting->var_colors==1)
        <th>Variant Attributes</th>
        @endif
  
        @if($getClientSetting->order_with_amt==0)
          <th>Rate</th>
          @if(!isset($order))
            <th>Qty.</th>
            
            @if($getClientSetting->product_level_discount==1)
            <th>Discount</th>
            @endif

            <th>Applied Rate</th>

            @if($getClientSetting->product_level_tax==1)
            <th>Tax Implied</th>
            @endif
          @elseif(isset($order))
            <th>Qty.</th>
  
            @if($order->product_level_discount_flag==1)
            <th>Discount</th>
            @endif

            <th>Applied Rate</th>
            
            @if($order->product_level_tax_flag==1)
            <th>Tax Implied</th>
            @endif
          @endif
          <th>Amount</th>
        @else
          <th>Qty.</th>
        @endif
  
        <th>Action</th>
      </tr>
    </thead>
    {{-- <tbody> --}}
      <div class="loaderDiv hidden">
        <img src="{{asset('assets/dist/img/loader2.gif')}}" />
      </div>
      @if(isset($orderdetails))
        @php $id=0 @endphp
        @php $countOrderdeatils= $orderdetails->count()-1; @endphp
        @foreach($orderdetails as $orderdetail)
        <tr class="prodrow oldprodrow" id="row{{$id}}" data-rowNum="{{$id}}">
          <td>
            <input type="hidden" name="orderproductsId[]" id="orderproductsId{{$id}}" value="{{$orderdetail->id}}">
            <input type="hidden" name="product_name[{{$orderdetail->id}}]" id="product_name{{$id}}"
              value="{{$orderdetail->product_name}}">
            <input type="hidden" name="product_variant_name[{{$orderdetail->id}}]" id="product_variant_name{{$id}}"
              value="{{$orderdetail->product_variant_name}}">
            <input type="hidden" name="brand[{{$orderdetail->id}}]" id="brand{{$id}}" value="{{$order->brand}}">
            <input type="hidden" name="short_desc[{{$orderdetail->id}}]" id="short_desc{{$id}}"
              value="{{$orderdetail->short_desc}}">
      
            <select class="form-control product select2" name="product_id[{{$orderdetail->id}}]" id="{{$id}}" required>
              <option></option>
                @foreach($products as $product)
                  @if($product->status=="Inactive" && $product->id!=$orderdetail->product_id) @continue; @endif
                @php $getColumn ="CONCAT(tax_types.name,' (',tax_types.percent,'%)') as name, tax_types.id, tax_types.percent,
                tax_types.default_flag"; @endphp
                <option value="{{$product->id}}" data-brand="{{isset($product->brand)?$product->brand:null}}"
                  data-product_name="{{$product->product_name}}" @if(count($product->product_variants)==0)
                  data-value="0"
                  data-short_desc ="@if(isset($product->short_desc)) {{$product->short_desc}}@endif"
                  mrp="{{$product->mrp}}"
                  unittype="{{getUnitName($product->unit)}}"
                  unit_id="{{$product->unit}}"
                  @else
                  data-value="{{$product->id}}"
                  @endif
                  @if($order->product_level_tax_flag==1)
                  @if($product->taxes->count()>0)
                  data-taxes="{{json_encode($product->taxes()->get([DB::raw($getColumn)])->toArray())}}"
                  @else
                  data-taxes="{{json_encode($product->taxes()->get([DB::raw($getColumn)])->toArray())}}"
                  @endif
                  @endif
                  @if($orderdetail->product_id == $product->id) selected @endif>
      
                  @if(strlen($product->product_name) > 50)
                  {{getShortName($product->product_name, 50)}}
                  @else
                  {{$product->product_name}}
                  @endif
                  @if(count($product->product_variants)==0)
                  @if(isset($product->short_desc))
                  ({{strlen($product->short_desc)>50?getShortName($product->short_desc, 50):$product->short_desc}})
                  @endif
                  @endif
      
                </option>
                @endforeach
              </select>
          </td>
          <td>
            <div class="input-group" style="display: initial;">
              <select class="form-control productvariant select2" name="product_variant[{{$orderdetail->id}}]" id="product_variant{{$id}}" data-id="{{$id}}" @if(!isset($orderdetail->product_variant_id)) disabled @endif>
                <option value=""></option>
                @if(isset($orderdetail->product_variant_id))
                @if($orderdetail->product->product_variants->count()==0)
                  <option value="{{$orderdetail->product_variant_id}}" selected>{{$orderdetail->product_variant_name}}</option>
                @else 
                  @php $flag=false @endphp
                  @forelse($variants as $variant)
                  @if($variant->product_id == $orderdetail->product_id)
                  <option value="{{$variant->id}}" data-variant_name="{{$variant->variant}}"
                    data-short_desc="@if(isset($variant->short_desc)) {{$variant->short_desc}}@endif" mrp="{{$variant->mrp}}"
                    unittype="{{getUnitName($variant->unit)}}" unit_id="{{$variant->unit}}"
                    colors="{{($variant->colors->count()>0)?json_encode($variant->colors->pluck('value')->toArray()): 'null'}}"
                    @if($orderdetail->product_variant_id == $variant->id) selected {{$flag=true}} @endif>
                    {{$variant->variant}}
                  </option>
                  @endif
                  @empty
                  <option value=""></option>
                  @endforelse
                  @if(!$flag) <option value="{{$orderdetail->product_variant_id}}" selected>{{$orderdetail->product_variant_name}}</option> @endif
                @endif
                @endif
              </select>
            </div>
          </td>
          @if($getClientSetting->var_colors==1)
          <td>
            <select class="variantColors" name="variant_colors[{{$orderdetail->id}}]" id="variant_colors{{$id}}" data-id="{{$id}}" @if(!isset($orderdetail->variant_colors)) disabled @endif>
              <option value=""></option>
              @if(!isset($orderdetail->variant_colors)) <option value="" selected>No Attributes</option> @endif
              @if(isset($orderdetail->variant_colors))
              @foreach($colors as $key=>$color)
              <option value="{{$key}}" @if($orderdetail->variant_colors == $key) selected @endif @if($orderdetail->first())
                @if($orderdetail->product_variant->first())
                @if(!in_array($key , $orderdetail->product_variant->first()->colors->pluck('value')->toArray()) &&
                $orderdetail->variant_colors!=$key) disabled @endif @endif
                @endif>
                {{$color}}
              </option>
              @endforeach
              @endif
            </select>
          </td>
          @endif
      
          @if($getClientSetting->order_with_amt==0)
            <td>
              <div class="input-group contentFit">
                <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
                  <input type="text" name="mrp[{{$orderdetail->id}}]" class="input-group-addon mrp_addon form-control name_list mrp" id="mrp{{$id}}"
                    data-id='{{$id}}' placeholder="Rate" readonly value="{{$orderdetail->mrp}}" />
                @if($getClientSetting->unit_conversion==0)
                  <span class="input-group-addon per-symbol" id="utype{{$id}}">Per {{$orderdetail->unit_name}}</span>
                  <input type="hidden" name="uid[{{$orderdetail->id}}]" id="u_id{{$id}}" value="{{$orderdetail->unit}}">
                @else
                  <input type="hidden" id="hidden_mrp{{$id}}" data-id='{{$id}}' value="{{$orderdetail->mrp, 2}}" />
                  <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection{{$id}}">
                    <select name="uid[{{$orderdetail->id}}]" class="unit_symbol_type_selection edit_unit_symbol_type_selection" id="unit_symbol_type_selection{{$id}}" data-id="{{$id}}" data-unit-id="{{$orderdetail->unit}}">
                      <option value="">Select Units</option>
                    </select>
                    <input type="hidden" id="hidden_u_id{{$id}}" value="{{$orderdetail->unit}}">
                    <input type="hidden" id="original_u_id{{$id}}" value="{{getOriginalUnit($orderdetail->product_id, $orderdetail->product_variant_id)}}">
                    <input type="hidden" id="original_u_mrp{{$id}}" value="{{getOriginalMrp($orderdetail->product_id, $orderdetail->product_variant_id)}}">
                  </span>
                @endif
              </div>
            </td>
  
            <td>
              <input type="text" name="quantity[{{$orderdetail->id}}]" class="form-control name_list qty onlynumber" id="qty{{$id}}"
                placeholder="Qty." data-id='{{$id}}' value="{{$orderdetail->quantity}}" required/>
              @if($getClientSetting->order_with_amt!=0)
              <input type="text" name="mrp[{{$orderdetail->id}}]" id="mrp{{$id}}" placeholder="Rate" class="mrp" hidden
                value="{{$orderdetail->mrp}}" />
              <input type="text" name="amount[{{$orderdetail->id}}]" id="amt{{$id}}" placeholder="amount" class="amt" hidden
                value="{{$orderdetail->amount}}" />
              @endif
            </td>
            
            @if($order->product_level_discount_flag==1)
              <td>
                {{-- <div class="input-group">
                  <span class="input-group-addon @if($orderdetail->pdiscount_type==" %") hidden @endif" id="discount-rs-symbol{{$id}}">{{$getClientSetting['currency_symbol']}}</span>
                  <input type="text" name="product_discount[{{$orderdetail->id}}]" id="product_discount{{$id}}" placeholder="Discount" class="form-control product_discount" value="{{$orderdetail->pdiscount}}" />
                  <span class="input-group-addon @if($orderdetail->pdiscount_type==" Amt") hidden @endif" id="discount-percent-symbol{{$id}}">%</span>
                  <input type="hidden" name="product_discount_type[{{$orderdetail->id}}]" class="product_discount_type" id="product_discount_type{{$id}}" value="{{$orderdetail->pdiscount_type}}" />
                </div> --}}
  
                <div class="input-group contentFit">
                  <span class="input-group-addon @if($orderdetail->pdiscount_type=="%") hidden @endif" id="discount-rs-symbol{{$id}}">{{$getClientSetting['currency_symbol']}}</span>
                  <span class="input-group-addon pdisinputaddon" id="pdisinputaddon{{$id}}">
                    <input type="text" name="product_discount[{{$orderdetail->id}}]" class="form-control product_discount validateFloatVal" id="product_discount{{$id}}" placeholder="Discount" data-id="{{$id}}" value="{{$orderdetail->pdiscount}}" {{($getClientSetting->non_zero_discount==1)?'required':null}}  />
                  </span>
                  <span class="input-group-addon @if($orderdetail->pdiscount_type=="Amt" || $orderdetail->pdiscount_type=="oAmt") hidden @endif" id="discount-percent-symbol{{$id}}">%</span>
                  <span class="input-group-addon discount-symbol-selection" id="discount-symbol-selection{{$id}}">
                    <select name="product_discount_type[{{$orderdetail->id}}]" class="select2 product_discount_type_selection" id="product_discount_type_selection{{$id}}" data-id="{{$id}}">
                      <option value="%" @if($orderdetail->pdiscount_type=="%")selected @endif>Percent</option>
                      <option value="Amt" @if($orderdetail->pdiscount_type=="Amt")selected @endif>Amount Per Unit</option>
                      <option value="oAmt" @if($orderdetail->pdiscount_type=="oAmt")selected @endif>Amount Overall</option>
                    </select>
                  </span>
                </div>
              </td>
            @endif
  
            <td>
              <div class="input-group">
                <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
                <input type="text" class="hidden-rate" id="hidden-rate{{$id}}" value="{{$orderdetail->mrp}}" hidden>
                <input type="text" name="rate[{{$orderdetail->id}}]" class="form-control name_list rate validateFloatVal" id="rate{{$id}}" placeholder="Applied Rate" data-id='{{$id}}' value="{{$orderdetail->rate}}" @if($order->product_level_discount_flag==1)readonly @endif/>
              </div>
            </td>
          @else
            <td>
              <span class="input-group contDisp">
                <input type="text" name="quantity[{{$orderdetail->id}}]" class="form-control name_list qty onlynumber" id="qty{{$id}}"
                placeholder="Qty." data-id='{{$id}}' value="{{$orderdetail->quantity}}" required />
                <input type="hidden" name="uid[{{$orderdetail->id}}]" id="u_id{{$id}}" value="{{$orderdetail->unit}}">
                @if($getClientSetting->order_with_amt!=0)
                <input type="text" name="mrp[{{$orderdetail->id}}]" id="mrp{{$id}}" placeholder="Rate" class="mrp" hidden
                  value="{{$orderdetail->mrp}}" />
                <input type="text" name="amount[{{$orderdetail->id}}]" id="amt{{$id}}" placeholder="amount" class="amt" hidden
                  value="{{$orderdetail->amount}}" />
                @endif
                @if($getClientSetting->unit_conversion==0)
                  <span class="input-group-addon per-symbol add-on-height" id="utype{{$id}}">{{$orderdetail->unit_name}}</span>
                  {{-- <input type="hidden" name="uid[{{$orderdetail->id}}]" id="u_id{{$id}}" value="{{$orderdetail->unit}}"> --}}
                @else
                  <input type="hidden" id="hidden_mrp{{$id}}" data-id='{{$id}}' value="{{$orderdetail->mrp, 2}}" />
                  <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection{{$id}}">
                    <select name="uid[{{$orderdetail->id}}]" class="unit_symbol_type_selection edit_unit_symbol_type_selection" id="unit_symbol_type_selection{{$id}}" data-id="{{$id}}" data-unit-id="{{$orderdetail->unit}}">
                      <option value="">Select Units</option>
                    </select>
                    <input type="hidden" id="hidden_u_id{{$id}}" value="{{$orderdetail->unit}}">
                    <input type="hidden" id="original_u_id{{$id}}" value="{{getOriginalUnit($orderdetail->product_id, $orderdetail->product_variant_id)}}">
                    <input type="hidden" id="original_u_mrp{{$id}}" value="{{getOriginalMrp($orderdetail->product_id, $orderdetail->product_variant_id)}}">
                  </span>
                @endif  
              </span>
            </td>
            <td style="display: none;">
              <input type="text" class="hidden-rate" id="hidden-rate{{$id}}" value="{{$orderdetail->mrp}}" hidden>
              <input type="text" name="rate[{{$orderdetail->id}}]" class="form-control name_list rate validateFloatVal" id="rate{{$id}}" placeholder="Applied Rate" data-id='{{$id}}' value="{{$orderdetail->rate}}" hidden />
            </td>
          @endif
  
          {{-- <td>
            <input type="text" name="quantity[{{$orderdetail->id}}]" class="form-control name_list qty onlynumber"
              id="qty{{$id}}" placeholder="Qty." data-id='{{$id}}' value="{{$orderdetail->quantity}}"/>
            <input type="hidden" name="uid[{{$orderdetail->id}}]" id="u_id{{$id}}" value="{{$orderdetail->unit}}">
            @if($getClientSetting->order_with_amt!=0)
            <input type="text" name="mrp[{{$orderdetail->id}}]" id="mrp{{$id}}" placeholder="Rate" class="mrp" hidden
              value="{{$orderdetail->mrp}}" />
            <input type="text" name="amount[{{$orderdetail->id}}]" id="amt{{$id}}" placeholder="amount" class="amt" hidden
              value="{{$orderdetail->amount}}" />
            @endif
          </td> --}}
          @if($getClientSetting->order_with_amt==0)
          @if($order->product_level_tax_flag==1)
            <td>
              <input type="hidden" name="product_tax[{{$orderdetail->id}}]" class="product_tax_hidden" id="product_tax_hidden{{$id}}" value="{{array_sum($orderdetail->taxes()->withTrashed()->pluck('tax_types.percent')->toArray())}}" />
              <select class="form-control product_tax" name="product_tax[{{$orderdetail->id}}][]" id="product_tax{{$id}}" data-id="{{$id}}" multiple>
                @php $taxesId=$orderdetail->taxes()->withTrashed()->pluck('tax_types.id')->toArray() @endphp 
                @foreach($orderdetail->product->taxes as $tax)
                  <option value="{{$tax->id}}" label="{{$tax->name.' ('.$tax->percent.'%)'}}" title="{{$tax->name.' ('.$tax->percent.'%)'}}" data-percent="{{$tax->percent}}" data-default_flag="{{$tax->default_flag}}" data-row_id="{{$id}}" data-aray="{{json_encode($taxesId)}}" @if(in_array($tax->id, $taxesId))selected @endif>{{$tax->name.' ('.$tax->percent.'%)'}}</option>
                @endforeach
              </select>
            </td>
          @endif
          <td>
            <div class="input-group">
              <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
              <input type="text" name="amount[{{$orderdetail->id}}]" id="amt{{$id}}" placeholder="amount" class="form-control name_list amt" value="{{$orderdetail->amount}}" readonly />
            </div>
          </td>
          @endif
          <td>
            <button type="button" class="btn btn-danger form-control addCancelBtn removeRowBtn @if($order->module_status->order_delete_flag == 0) hidden @endif" id="remove{{$id}}" data-orderNo={{{$orderdetail->id}}} data-rowNum="{{$id}}">X</button>
            <button type="button" class="btn btn-success form-control addCancelBtn addMoreOrder @if($countOrderdeatils!=$id) hidden @endif" id="add{{$id}}" data-orderNo={{{$orderdetail->id}}} data-rowNum="{{$id}}">+</button>
          </td>
          @php $id++ @endphp
        </tr>
        @endforeach
    
      @else
    
        <tr class="prodrow" id="row1" data-rownum="1">
          
          <td>
            <input type="hidden" name="orderproductsId[1]" id="orderproductsId1" value="1">
            <input type="hidden" name="product_name[1]" id="product_name1">
            <input type="hidden" name="product_variant_name[1]" id="product_variant_name1">
            <input type="hidden" name="brand[1]" id="brand1">
            <input type="hidden" name="short_desc[1]" id="short_desc1">
      
            <select class="form-control product select2" name="product_id[1]" id="1" required>
              <option></option>
              @foreach($products as $product)
                @php $getColumn ="CONCAT(tax_types.name,' (',tax_types.percent,'%)') as name, tax_types.id, tax_types.percent,
                tax_types.default_flag" @endphp
                <option value="{{$product->id}}" data-brand="{{isset($product->brand)?$product->brand:null}}"
                  data-product_name="{{$product->product_name}}" 
                  @if(count($product->product_variants)==0)
                  data-value="0"
                  data-short_desc ="@if(isset($product->short_desc)){{$product->short_desc}}@endif"
                  mrp="{{$product->mrp}}" unittype="{{getUnitName($product->unit)}}" unit_id="{{$product->unit}}"
                  @else
                  data-value="{{$product->id}}"
                  @endif
                  @if($getClientSetting->product_level_tax==1 && $taxes->count()>0)
                  @if($product->taxes->count()>0)
                  data-taxes="{{json_encode($product->taxes()->get([DB::raw($getColumn)])->toArray())}}"
                  @else
                  data-taxes="{{json_encode($product->taxes()->get([DB::raw($getColumn)])->toArray())}}"
                  @endif
                  @endif>
      
                  @if(strlen($product->product_name) > 50)
                  {{getShortName($product->product_name, 50)}}
                  @else
                  {{$product->product_name}}
                  @endif
                  @if(count($product->product_variants)==0)
                  @if(isset($product->short_desc))
                  ({{strlen($product->short_desc)>50?getShortName($product->short_desc, 50):$product->short_desc}})
                  @endif
                  @endif
      
                </option>
              @endforeach
            </select>
          </td>
          
          <td>
            <div class="input-group" style="display: initial;">
              <select class="form-control productvariant select2" name="product_variant[1]" id="product_variant1" data-id="1">
                <option value=""></option>
              </select>
            </div>
          </td>
      
          @if($getClientSetting->var_colors==1)
          <td>
            <select class="variantColors" name="variant_colors[1]" id="variant_colors1" data-id="1">
              <option value=""></option>
            </select>
          </td>
          @endif
      
          @if($getClientSetting->order_with_amt==0)
            <td>
              <div class="input-group contentFit">
                <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
                <input type="text" name="mrp[1]" class="input-group-addon mrp_addon form-control name_list mrp" id="mrp1" data-id='1' placeholder="Rate" readonly />
                @if($getClientSetting->unit_conversion==0)
                  <span class="input-group-addon per-symbol" id="utype1">Per Unit</span>
                  <input type="hidden" name="uid[1]" id="u_id1">
                @else
                  <input type="hidden" id="hidden_mrp1" data-id='1'/>
                  <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection1">
                    <select name="uid[1]" class="unit_symbol_type_selection"
                      id="unit_symbol_type_selection1" data-id="1">
                      <option value="">Select Units</option>
                    </select>
                    <input type="hidden" id="hidden_u_id1">
                  </span>
                @endif
              </div>
            </td>
            <td>
              <input type="text" name="quantity[1]" class="form-control name_list qty onlynumber" id="qty1" placeholder="Qty."
                data-id='1' required/>
              @if($getClientSetting->order_with_amt!=0)
              <input type="text" name="mrp[1]" id="mrp1" placeholder="Rate" class="mrp" hidden />
              <input type="text" name="amount[1]" id="amt1" placeholder="amount" class="amt" hidden />
              @endif
            </td>
            @if($getClientSetting->product_level_discount==1)
            <td>
              <div class="input-group contentFit">
                <span class="input-group-addon" id="discount-rs-symbol1">{{$getClientSetting['currency_symbol']}}</span>
                <span class="input-group-addon pdisinputaddon" id="pdisinputaddon1">
                  <input type="text" name="product_discount[1]" class="form-control product_discount validateFloatVal" id="product_discount1" placeholder="Discount" data-id="1" {{($getClientSetting->non_zero_discount==1)?'required':null}} />
                </span>
                <span class="input-group-addon hidden" id="discount-percent-symbol1">%</span>
                <span class="input-group-addon discount-symbol-selection" id="discount-symbol-selection1">
                  <select name="product_discount_type[1]" class="select2 product_discount_type_selection" id="product_discount_type_selection1" data-id="1">
                    <option value="%">Percent</option>
                    <option value="Amt" selected="selected">Amount Per Unit</option>
                    <option value="oAmt">Amount Overall</option>
                  </select>
                </span>
              </div>
            </td>
            @endif
            
            <td>
              <div class="input-group">
                <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
                <input type="text" class="hidden-rate" id="hidden-rate1" hidden>
                <input type="text" name="rate[1]" class="form-control name_list rate validateFloatVal" id="rate1" placeholder="Applied Rate" data-id='1' @if($getClientSetting->product_level_discount==1)readonly @endif/>
              </div>
            </td>
          @else
            <td>
              <span class="input-group contDisp">
                <input type="text" name="quantity[1]" class="form-control name_list qty onlynumber" id="qty1" placeholder="Qty."
                  data-id='1' required/>
                @if($getClientSetting->unit_conversion==0)
                  {{-- <input type="hidden" name="uid[1]" id="u_id1"> --}}
                  <span class="input-group-addon per-symbol add-on-height" id="utype1">Unit</span>
                @else
                <span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection1">
                  <select name="uid[1]" class="unit_symbol_type_selection"
                    id="unit_symbol_type_selection1" data-id="1">
                    <option value="">Select Units</option>
                  </select>
                  <input type="hidden" id="hidden_u_id1">
                </span>
                  <input type="hidden" id="hidden_mrp1" data-id='1'/>
                @endif
              </span>
              <input type="hidden" name="uid[1]" id="u_id1">
              @if($getClientSetting->order_with_amt!=0)
              <input type="text" name="mrp[1]" id="mrp1" placeholder="Rate" class="mrp" hidden />
              <input type="text" name="amount[1]" id="amt1" placeholder="amount" class="amt" hidden />
              @endif
            </td>
            <td style="display: none;">
              <input type="text" class="hidden-rate" id="hidden-rate1" hidden>
              <input type="text" name="rate[1]" class="form-control name_list rate validateFloatVal" id="rate1" placeholder="Applied Rate" data-id='1' hidden />
            </td>
          @endif
          
          {{-- <td>
            <input type="text" name="quantity[1]" class="form-control name_list qty onlynumber" id="qty1" placeholder="Qty." data-id='1' />
            <input type="hidden" name="uid[1]" id="u_id1">
            @if($getClientSetting->order_with_amt!=0)
            <input type="text" name="mrp[1]" id="mrp1" placeholder="Rate" class="mrp" hidden />
            <input type="text" name="amount[1]" id="amt1" placeholder="amount" class="amt" hidden />
            @endif
          </td> --}}
  
          @if($getClientSetting->order_with_amt==0)
      
          @if($getClientSetting->product_level_tax==1)
          <td>
            <input type="hidden" name="product_tax[1]" id="product_tax_hidden1" class="product_tax_hidden" />
            <select class="product_tax" name="product_tax[1][]" id="product_tax1" data-id="1" multiple>  
            </select>
          </td>
          @endif
          
          <td>
            <div class="input-group">
              <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
              <input type="text" name="amount[1]" id="amt1" placeholder="amount" class="form-control name_list amt" readonly />
            </div>
          </td>
          @endif
          <td>
            <button type="button" class="btn btn-danger form-control addCancelBtn removeRowBtn hidden" id="remove1" data-rowNum="1">X</button>
            <button type="button" class="btn btn-success form-control addCancelBtn addMoreOrder" id="add1" data-rownum="1">+</button>
          </td>
        </tr>
      @endif
    {{-- </tbody> --}}
  
  </table>
{{-- </div> --}}
<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('order_note')) has-error @endif">
      {!! Form::label('order_note', 'Order Notes') !!}
      {!! Form::textarea('order_note', isset($order)?$order->order_note:null, ['class' => 'form-control ckeditor', 'rows="19"', 'id=order_note', 'placeholder' => 'Order Notes']) !!}
      @if ($errors->has('order_note')) <p class="help-block has-error">{{ $errors->first('order_note') }}</p> @endif
    </div>
  </div>
  @if($getClientSetting->order_with_amt==0)
    <div class="col-xs-6">
      <div class="form-group @if ($errors->has('grand_total')) has-error @endif">
        {!! Form::label('total', 'Sub Total ', ['class' => ' control-label']) !!}

        <div class="input-group">
          <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
          {!! Form::text('subtotal', isset($order)?$order->tot_amount:null, ['class' => 'form-control subtotal', 'placeholder' => 'Sub Total', 'readonly'=>'readonly']) !!}
        </div>

        @if ($errors->has('grand_total')) <p class="help-block has-error">{{ $errors->first('grand_total') }}</p> @endif
      </div>

      <div class="form-group @if ($errors->has('discount')) has-error @endif" style="display: inline-block; width: 100%;">
        {!! Form::label('discount', 'Discount', ['class' => 'col-xs-12 control-label']) !!}
        <div class="col-xs-8">
          <div class="input-group" style="width: 100%;">
            <span class="input-group-addon rs-symbol" id="for_amount" @if(isset($order)) @if($order->discount_type=="%") style="display:none;" @endif @endif>{{$getClientSetting['currency_symbol']}}</span>
            @if(!isset($order))
            {!! Form::text('discount', isset($order)?$order->discount:null, ['class' => 'form-control discount validateFloatVal', 'id' =>'discount', 'placeholder' => 'Discount',  ($getClientSetting->non_zero_discount==1)?'required':null, 'readonly'=>($getClientSetting->product_level_discount==1)?true:false]) !!}
            @else
            {!! Form::text('discount', isset($order)?$order->discount:null, ['class' => 'form-control discount validateFloatVal',
            'id' =>'discount', 'placeholder' => 'Discount', ($getClientSetting->non_zero_discount==1)?'required':null,
            'readonly'=>($order->product_level_discount_flag==1)?true:false]) !!}
            @endif
          </div>
        </div>
        <div class="col-xs-4">
          {!! Form::select('discount_type', array('%' => 'Percent', 'Amt' => 'Amount'), isset($order)?$order->discount_type:'Amt',  ['class' => 'form-control col-xs-2 disctype' , 'id' => 'disctype', 'disabled'=>($getClientSetting->product_level_discount==1)?true:false]) !!}
        </div>
        @if ($errors->has('discount')) <p class="help-block has-error">{{ $errors->first('discount') }}</p> @endif
      </div>

      <div class="form-group" style="display: inline-block; width: 100%;">
        {!! Form::label('total', 'Total ', ['class' => 'col-xs-12 control-label']) !!}
        <div class="col-xs-12">
          <div class="input-group">
            <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
            {!! Form::text('total', isset($order)? $order->total:null, ['class' => 'form-control total', 'placeholder' => 'Total Amount', 'readonly'=>'readonly']) !!}
          </div>
        </div>
        @if ($errors->has('grand_total')) <p
            class="help-block has-error">{{ $errors->first('grand_total') }}</p> @endif
      </div>

      @if(!isset($order))
        @if($taxes->first())
          <div class="form-group @if ($errors->has('tax')) has-error @endif" style="display: inline-block; width: 100%;">
            @if($getClientSetting->product_level_tax==0)
              <div class="col-xs-12">
                {!! Form::label('product_level_tax', 'Add Tax Type', ['class' => 'control-label']) !!}
                {!! Form::select('added_tax_type[]', $tax_types, $default_taxes, ['class' => 'form-control col-xs-2 addtaxtype' , 'id' =>'addtaxtype','required'=>false,'multiple'=> true]) !!}
              </div>
            @endif
            
            <div class="col-xs-12 yes-label">
              <label> Taxes Implied </label><br>
            </div>
            <div class="col-xs-12 no-label hidden">
              <label> No Taxes Implied </label><br>
            </div>
            <div class="col-xs-12 tax_rows implied-tax">
              @if($getClientSetting->product_level_tax==0)
                @php $i = 0; @endphp
                @foreach($taxes as $tax)
                  @if(in_array($tax->id, $default_taxes))
                    @php ++$i @endphp
                    <div class="tax_row" id="tax_row{{$tax->id}}" data-id="{{$i}}">
                      {!! Form::label('product_level_tax', $tax->name, ['class' => 'col-xs-12 control-label']) !!}
                      <div class="col-xs-12">
                        <input type="hidden" name="tax_type_id[{{$i}}]" value="{{$tax->id}}">
                        <input type="hidden" name="tax_percents[{{$i}}]" class="tax_percents" value="{{$tax->percent}}">
                        <input type="text" name="tax[{{$i}}]" class="form-control tax" id="tax{{$i}}" placeholder="{{$tax->name}}" readonly>
                      </div>
                    </div>
                  @endif
                @endforeach
              @else
                <div id="implied-tax">
                </div>
                <div class="col-xs-12">
                  <label for="tax1">Total Tax </label>
                </div>
                <div class="col-xs-12">
                  <input type="text" name="tax" class="form-control total_tax" id="total_tax" placeholder="Total Tax" readonly>
                </div>
              @endif  
            </div>

            @if ($errors->has('tax')) <p class="help-block has-error">{{ $errors->first('tax') }}</p> @endif
          </div>
        @endif
      @else
        @if(!empty($tax_types))
          <div class="form-group @if ($errors->has('tax')) has-error @endif" style="display: inline-block; width: 100%;">
            @if($order->product_level_tax_flag==0)
              <div class="col-xs-12">
                {!! Form::label('product_level_tax', 'Add Tax Type', ['class' => 'control-label']) !!}
                {!! Form::select('added_tax_type[]', $tax_types, $order_taxes, ['class' => 'form-control col-xs-2 addtaxtype' , 'id'
                =>'addtaxtype','required'=>false,'multiple'=> true]) !!}
              </div>
            @endif
            
            <div class="col-xs-12 yes-label">
              <label> Taxes Implied </label><br>
            </div>
            <div class="col-xs-12 no-label hidden">
              <label> No Taxes Implied </label><br>
            </div>

            <div class="col-xs-12 tax_rows implied-tax">
              @if($order->product_level_tax_flag==0)
                @php $i = 0; @endphp
                @foreach($taxes as $tax)
                  @if(in_array($tax->id, $order_taxes))
                    @php ++$i @endphp
                    <div class="tax_row" id="tax_row{{$tax->id}}" data-id="{{$i}}">
                      {!! Form::label('product_level_tax', $tax->name, ['class' => 'col-xs-12 control-label']) !!}
                      <div class="col-xs-12">
                        <input type="hidden" name="tax_type_id[{{$i}}]" value="{{$tax->id}}">
                        <input type="hidden" name="tax_percents[{{$i}}]" class="tax_percents" value="{{$tax->percent}}">
                        <input type="text" name="tax[{{$i}}]" class="form-control tax" id="tax{{$i}}" placeholder="{{$tax->name}}" value="{{number_format(($tax->percent*$order->total)/100, 2)}}" readonly>
                      </div>
                    </div>
                  @endif
                @endforeach
              @elseif($order->product_level_tax_flag==1)
                <div id="implied-tax">
                </div>
                <div class="col-xs-12">
                  <label for="tax1">Total Tax </label>
                </div>
                <div class="col-xs-12">
                  <input type="text" name="tax" class="form-control total_tax" id="total_tax" placeholder="Total Tax" value="{{$order->tax}}" readonly>
                </div>
              @endif
            </div>
          </div>  
        @endif
      @endif
      <div class="form-group @if ($errors->has('grand_total')) has-error @endif" style="display: inline-block; width: 100%; margin-top: 15px; ">
        {!! Form::label('grand_total', 'Grand Total ', ['class' => ' control-label']) !!}

        <div class="input-group">
          <span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span>
          {!! Form::text('grand_total', null, ['class' => 'form-control grand_total', 'placeholder' => 'Grand Total', 'readonly'=>'readonly', 'required'=>true]) !!}
        </div>

        @if ($errors->has('grand_total')) <p class="help-block has-error">{{ $errors->first('grand_total') }}</p> @endif
      </div>
    </div>
  @endif
</div>
</div>
<input type="hidden" name="DT_Ord_FILTER" class="DT_Ord_FILTER">
