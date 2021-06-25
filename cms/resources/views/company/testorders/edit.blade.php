@extends('layouts.company')
@section('title', 'Edit Order')
@section('stylesheets')
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}" />
<style>
  .ms-options-wrap {
    min-width: 120px;
    z-index: 1;
  }

  .select2-selection__placeholder, .select2-selection__rendered {
    color: #000 !important;
  }

  .addCancelBtn{
    width: 45px;
  }

  .box-body .btn-success{
    background-color: #00da76!important;
    border-color: #00da76!important;
    color: #fff!important;
  }

  .caret{
    position: absolute;
    top: 20px;
  }

  .multiselect.dropdown-toggle.btn.btn-default .caret{
    margin-top: 0px;
  }

  .qty, .product_discount, .rate, .mrp, .amt{
    width: 80px !important;
  }

  .pdisinputaddon, .discount-symbol-selection, .unit-symbol-selection{
    padding: 0px 0px;
    font-size: 14px;
    font-weight: normal;
    line-height: 1;
    color: #555;
    text-align: center;
    background-color: #eee;
    border: 0px solid #ccc;
    border-radius: 0px;
  }

  .mrp_addon {
    padding: 0px 0px;
    font-size: 14px;
    font-weight: normal;
    line-height: 1;
    color: #555;
    text-align: center;
    background-color: #eeeeee !important;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  .amt{
    padding: 0px;
  }

  .contentFit{
    width: fit-content
  }

  .loaderDiv{
    position: absolute;z-index: 1;left: 30%;
  }
  
  .loaderOpacityControl{
    opacity: 0.4;
  }

  .contDisp{
    display: -webkit-inline-box;
  }
  .add-on-height{
    height: 40px !important;
  }
</style>
@endsection

@section('content')
<section class="content">

  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Edit Order</h3>
      <div class="page-action pull-right">
        <a href="{{ domain_route('company.admin.order') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i>
          Back</a>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      {!! Form::model($order, array('url' => url(domain_route('company.admin.order.update',[$order->id])) , 'method' => 'PATCH', 'autocomplete' => 'off', 'files'=> true, 'id'=>'orderForm')) !!}

      @include('company.orders._form')
      <div class="col-xs-12">
        <!-- Submit Form Button -->
        {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right', 'id'=>'submitOrder']) !!}
      </div>

      {!! Form::close() !!}

    </div>
  </div>

</section>

<div class="modal modal-default fade" id="deleteorderproducts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <form method="post" class="remove-record-model" id="formDeleteOrderProducts">
        {{method_field('delete')}}
        {{csrf_field()}}
        <div class="modal-body">
          <p class="text-center">
            Are you sure you want to delete this?
          </p>
          <input type="hidden" name="orderproductsId" id="orderproductsId">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success cancel" data-dismiss="modal" id="deleteCancel">No, Cancel</button>
          <button type="submit" class="btn btn-warning delete-button" id="deleteConfirm">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>


@endsection

@section('scripts')

@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
<script type="text/javascript">
  $('.DT_Ord_FILTER').val(sessionStorage.getItem('DT_Ord_filters'));
  const regexPattern = /^[0]+$/;
  let enablePartyRate = Boolean({{$getClientSetting->party_wise_rate_setup}});

  $(document).ready(function () {
    @if($getClientSetting->unit_conversion==1)
      $('.prodrow').each(function(){
        let el = $(this);
        let rowNum = el.data("rownum");
        let unitId = el.find('.unit_symbol_type_selection').data("unit-id");
        let product_id = $(this).find('.product option:selected').val();
        $("#unit_symbol_type_selection"+rowNum).empty();
        $.ajax({
          type: 'GET',
          url: "{{ domain_route('company.admin.unit.getrelatedunits') }}",
          data: {
            'unit_id': unitId,
            'product_id': product_id
          },
          beforeSend: function(){
            showLoader();
            $('#submitOrder').attr("disabled", true);
          },
          success: function (data) {
            if(data!=""){
              let parseData = JSON.parse(data);
              let state = false;
              $(`#unit_symbol_type_selection${rowNum}`).select2({
                "placeholder": "Select Unit"
              });
              $.each(parseData, function(value, text){
                if(value==unitId){
                  state=true;
                } else{
                  state= false;
                }
                var newState = new Option(text, value, state, state);
                // Append it to the select
                $(`#unit_symbol_type_selection${rowNum}`).append(newState);//.trigger('change');
                $(`#unit_symbol_type_selection${rowNum}`).select2('destroy');
                $(`#unit_symbol_type_selection${rowNum}`).select2({
                  "placeholder": "Select Unit"
                });
              });
            }
            hideLoader();
            $('#submitOrder').attr("disabled", false);
          },
          error:function(){
            hideLoader();
            $('#submitOrder').attr("disabled", false);
          }
        });
      });
    @endif
    update_amounts();
    (function initilizeSelectBoxesPlugins(){
      $('.select2').select2();
      $('.productvariant').select2({
        "placeholder": "Select Variant",
      });
      $('.product').select2({
        "placeholder": "Select Product",
      });
      @if($getClientSetting->var_colors==1)
        $('.variantColors').select2({
          placeholder: 'Select Attributes ',
        });
      @endif
    }());

    @if($order->product_level_tax_flag==0 && $taxes->first())
      let taxToPercents = @json($tax_percents);
      $('#addtaxtype').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: false,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Select Tax Types",
        onChange: function(option, checked, select) {
          let value = option[0].value;
          if(!checked) {
            let taxesLength = $('.tax_row').length;
            $('#tax_row'+value).remove();
            if($('#total').val()!="")
              update_amounts();
            // if(taxesLength>1){
            // }else{
            //   alert("At least a tax type is required.");
            //   $('#addtaxtype').val(value);
            //   $('#addtaxtype').multiselect("refresh");
            // }
            if(taxesLength==1){
              $('.no-label').removeClass("hidden");
              $('.yes-label').addClass("hidden");
            }
          }else{
            $('.no-label').addClass("hidden");
            $('.yes-label').removeClass("hidden");
            let optionText = option[0].text;
            let lastElId = ($('.tax_row').last().length==0)?1:$('.tax_row').last().data('id')+1;
            let optionPercent = taxToPercents[value];
            let el = `<div class="tax_row" id="tax_row${value}" data-id="${lastElId}">
              <label for="product_level_tax" class="col-xs-12 control-label">${optionText}</label>
              <div class="col-xs-12">
                <input type="hidden" name="tax_type_id[${lastElId}]" value="${value}">
                <input type="hidden" name="tax_percents[${lastElId}]" class="tax_percents" value="${optionPercent}">
                <input type="text" name="tax[${lastElId}]" class="form-control tax" id="tax${lastElId}" placeholder="${optionText}" readonly>
              </div>
            </div>`;
            $('.tax_rows').append(el);
            if($('#total').val()!="")
              update_amounts();
          }
        },
        onSelectAll: function(option, checked, select){
          console.log("here");
          debugger;
        },
        onDeselectAll: function(option, checked, select) {
          console.log("deselected");
        }
      });
    @elseif($order->product_level_tax_flag==1)
      $('.product_tax').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: false,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Select Tax Types",
        onChange: function(option, checked, select) {
          let value = option[0].value;
          let row_id = option.data('row_id');
          // if(!checked) {
          //   if($('#product_tax'+row_id).val().length==0){
          //     alert("At least a tax type is required.");
          //     $('#product_tax'+row_id).val(value);
          //     $('#product_tax'+row_id).multiselect("refresh");
          //   }
          // }
          update_hidden_tax_value_product(row_id);
        },
      });
      function update_hidden_tax_value_product(RowId){
        let options = $('#product_tax'+RowId+' option:selected');
        let totPercent = 0;
        options.each(function(index, opt){
          let percent = parseFloat($(opt).data("percent"));
          totPercent += percent;
        });
        $('#product_tax_hidden'+RowId).val(totPercent);
        update_amounts();
      }
    @endif

    $(document).on('click','.addMoreOrder',function () {
      let rowNum = $(this).data("rownum");
      let befIncrement = rowNum;
      $(this).addClass("hidden");
      rowNum++;

      @if($order->module_status->order_delete_flag == 1)
        let getCountRow = $(".prodrow").length;
        if(getCountRow<=1){ $(`#remove${befIncrement}`).removeClass("hidden"); }
      @endif
      
      let newRowHiddenInput = `<input type="hidden" name="newrow_orderproductsId[]" id="orderproductsId${rowNum}" value=${rowNum}>`;
      let productNameHiddenInput = `<input type="hidden" name="newrow_product_name[${rowNum}]" id="product_name${rowNum}">`;
      let prodVariantNameHiddenInput = `<input type="hidden" name="newrow_product_variant_name[${rowNum}]" id="product_variant_name${rowNum}">`;
      let brandHiddenInput = `<input type="hidden" name="newrow_brand[${rowNum}]" id="brand${rowNum}">`;
      let shortDescHiddenInput = `<input type="hidden" name="newrow_short_desc[${rowNum}]" id="short_desc${rowNum}">`;

      let selectProductHtml = `<select class="form-control product select2" name="newrow_product_id[${rowNum}]" id="${rowNum}" required>`;
      
      let optionProductList = `@forelse($products as $product) @if($product->status=="Inactive") @continue; @endif
        @php $getColumn ="CONCAT(tax_types.name,' (',tax_types.percent,'%)') as name, tax_types.id, tax_types.percent,
        tax_types.default_flag" @endphp
        <option value="{{$product->id}}" data-brand="{{isset($product->brand)?$product->brand:null}}" data-product_name="{{$product->product_name}}"
        @if(count($product->product_variants)==0)
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
      @empty
        <option></option>
      @endforelse`;
      let buildProductOptions = `${selectProductHtml}<option></option>${optionProductList}`;
      
      let variantSelectList = `<select class="form-control productvariant" name="newrow_product_variant[${rowNum}]" id="product_variant${rowNum}" data-id="${rowNum}"><option value=""></option></select>`;
      
      @if($getClientSetting->var_colors==1)
        let variantColorsList = `<select class="variantColors" name="newrow_variant_colors[${rowNum}]" id="variant_colors${rowNum}" data-id="${rowNum}"><option value=""></option></select>`;
      @endif

      let qtyInput = `<input type="text" name="newrow_quantity[${rowNum}]" class="form-control name_list qty onlynumber" id="qty${rowNum}" placeholder="Qty." data-id='${rowNum}' required/>@if($getClientSetting->order_with_amt!=0)<input type="text" name="mrp[${rowNum}]" id="mrp${rowNum}" placeholder="Rate" class="mrp" hidden /><input type="text" name="amount[${rowNum}]" id="amt${rowNum}" placeholder="amount" class="amt" hidden />@endif`;

      let actionBtn = `<button type="button" class="btn btn-danger form-control addCancelBtn removeRowBtn" id="remove${rowNum}"
        data-rowNum="${rowNum}" data-orderNo="">X</button><button type="button" name="add" id="add${rowNum}" class="btn btn-success form-control addCancelBtn addMoreOrder" data-rowNum="${rowNum}" data-orderNo="">+</button>`;

      @if($getClientSetting->order_with_amt==0)
        @if($getClientSetting->unit_conversion==0)
          let unitHtml = `<span class="input-group-addon per-symbol" id="utype${rowNum}">Per Unit</span><input type="hidden" name="newrow_uid[${rowNum}]" id="u_id${rowNum}">`;
        @else
          let unitHtml = `<input type="hidden" id="hidden_mrp${rowNum}" data-id='${rowNum}' /><span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection${rowNum}">
          <select name="newrow_uid[${rowNum}]" class="unit_symbol_type_selection" id="unit_symbol_type_selection${rowNum}"
            data-id="${rowNum}">
            <option value="">Select Units</option>
          </select>
          <input type="hidden" id="hidden_u_id${rowNum}">
        </span>`;
        @endif
        let mrpInput = `<div class="input-group contentFit"><span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span><input type="text" name="newrow_mrp[${rowNum}]" class="input-group-addon mrp_addon form-control name_list mrp" id="mrp${rowNum}" data-id='${rowNum}' placeholder="Rate" readonly />${unitHtml}</div>`;

        let rateInput = `<div class="input-group"><span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span><input type="text" class="hidden-rate" id="hidden-rate${rowNum}" hidden><input type="text" name="newrow_rate[${rowNum}]" class="form-control name_list rate validateFloatVal" id="rate${rowNum}" placeholder="Applied Rate" data-id='${rowNum}' /></div>`;

        let amountInput = `<div class="input-group"><span class="input-group-addon rs-symbol">{{$getClientSetting['currency_symbol']}}</span><input type="text" name="newrow_amount[${rowNum}]" id="amt${rowNum}" placeholder="amount" class="form-control name_list amt" readonly /></div>`;

        @if($order->product_level_discount_flag==1)
        // let prodLevelDiscountInput = `<td>
        //   <div class="input-group">
        //     <span class="input-group-addon" id="discount-rs-symbol${rowNum}">{{$getClientSetting['currency_symbol']}}</span>
        //     <input type="text" name="newrow_product_discount[${rowNum}]" id="product_discount${rowNum}" placeholder="Discount"
        //       class="form-control product_discount" readonly />
        //     <span class="input-group-addon hidden" id="discount-percent-symbol${rowNum}">%</span>
        //     <input type="hidden" name="newrow_product_discount_type[${rowNum}]" class="product_discount_type" id="product_discount_type${rowNum}" value="Amt" />
        //   </div>
        // </td>`;
        let prodLevelDiscountInput = `<td>
          <div class="input-group contentFit">
            <span class="input-group-addon" id="discount-rs-symbol${rowNum}">{{$getClientSetting['currency_symbol']}}</span>
            <span class="input-group-addon pdisinputaddon" id="pdisinputaddon${rowNum}">
              <input type="text" name="newrow_product_discount[${rowNum}]" id="product_discount${rowNum}" placeholder="Discount"
                class="form-control product_discount validateFloatVal" data-id="${rowNum}" {{$getClientSetting->non_zero_discount==1?"required":""}} />
            </span>
            <span class="input-group-addon hidden" id="discount-percent-symbol${rowNum}">%</span>
            <span class="input-group-addon discount-symbol-selection" id="discount-symbol-selection${rowNum}">
              <select class="select2 product_discount_type_selection" id="product_discount_type_selection${rowNum}"
                name="newrow_product_discount_type[${rowNum}]" data-id="${rowNum}">
                <option value="%">Percent</option>
                <option value="Amt" selected="selected">Amount Per Unit</option>
                @if(!$order->outlet_id)
                <option value="oAmt">Amount Overall</option>
                @endif
              </select>
            </span>
          </div>
        </td>`;
        @else
        let prodLevelDiscountInput = null;
        @endif
        
        @if($order->product_level_tax_flag==1)
        let prodLevelTaxInput = `<td><input type="hidden" name="newrow_product_tax[${rowNum}]" class="product_tax_hidden" id="product_tax_hidden${rowNum}" /><select class="form-control product_tax" name="newrow_product_tax[${rowNum}][]" id="product_tax${rowNum}" data-id="${rowNum}" multiple></select></td>`;
        @else
        let prodLevelTaxInput = null;
        @endif

        $('#dynamic_field').append(`<tr id="row${rowNum}" class="prodrow" data-rowNum="${rowNum}"><td>${productNameHiddenInput}${brandHiddenInput}${shortDescHiddenInput}${prodVariantNameHiddenInput}${newRowHiddenInput}${buildProductOptions}</td><td>${variantSelectList}</td>@if($getClientSetting->var_colors==1)<td>${variantColorsList}</td>@endif<td>${mrpInput}</td><td>${qtyInput}</td>${prodLevelDiscountInput}<td>${rateInput}</td>${prodLevelTaxInput}<td>${amountInput}</td><td>${actionBtn}</td></tr>`);
      @else
        @if($getClientSetting->unit_conversion==0)
          let unitHtml = `<span class="input-group-addon per-symbol add-on-height" id="utype${rowNum}">Unit</span>`;
        @else
          let unitHtml = `<input type="hidden" id="hidden_mrp${rowNum}" data-id='${rowNum}' /><span class="input-group-addon unit-symbol-selection" id="unit-symbol-selection${rowNum}">
          <select name="newrow_uid[${rowNum}]" class="unit_symbol_type_selection" id="unit_symbol_type_selection${rowNum}"
            data-id="${rowNum}">
            <option value="">Select Units</option>
          </select>
          <input type="hidden" id="hidden_u_id${rowNum}">
        </span>`;
        @endif
        let rateInput = `<input type="text" class="hidden-rate" id="hidden-rate${rowNum}" hidden><input type="text" name="newrow_rate[${rowNum}]" class="form-control name_list rate validateFloatVal" id="rate${rowNum}" placeholder="Applied Rate" data-id='${rowNum}' hidden /><input type="hidden" name="newrow_uid[${rowNum}]" id="u_id${rowNum}"><input type="hidden" name="newrow_mrp[${rowNum}]" class="input-group-addon mrp_addon form-control name_list mrp" id="mrp${rowNum}" data-id='${rowNum}' placeholder="Rate" readonly />`;

        $('#dynamic_field').append(`<tr id="row${rowNum}" class="prodrow" data-rowNum="${rowNum}"><td>${productNameHiddenInput}${brandHiddenInput}${shortDescHiddenInput}${prodVariantNameHiddenInput}${newRowHiddenInput}${buildProductOptions}</td><td>${variantSelectList}</td>@if($getClientSetting->var_colors==1)<td>${variantColorsList}</td>@endif<td hidden>${rateInput}</td><td><span class="input-group contDisp">${qtyInput}${unitHtml}</span></td><td>${actionBtn}</td></tr>`);
      @endif

      $("#qty" + rowNum).keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
          (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
          (e.keyCode >= 35 && e.keyCode <= 40)) {
          return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
          e.preventDefault();
        }
      });

      $('.productvariant').select2({
        "placeholder": "Select Variant",
      });
      $('.product').select2({
        "placeholder": "Select Product",
      });
      @if($getClientSetting->unit_conversion==1)
        $('.unit_symbol_type_selection').select2({
          placeholder: 'Select Unit',
        });
      @endif
      @if($getClientSetting->var_colors==1)
        $('.variantColors').select2({
          placeholder: 'Select Attributes',
        });
      @endif
      @if($order->product_level_tax_flag==1)
        $('.product_tax').multiselect({
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true,
          enableFullValueFiltering: false,
          enableClickableOptGroups: false,
          includeSelectAllOption: false,
          enableCollapsibleOptGroups : true,
          selectAllNumber: false,
          nonSelectedText:"Select Tax Types",
          onChange: function(option, checked, select) {
            let value = option[0].value;
            let row_id = option.data('row_id');
            // if(!checked) {
            //   if($('#product_tax'+row_id).val().length==0){
            //     alert("At least a tax type is required.");
            //     $('#product_tax'+row_id).val(value);
            //     $('#product_tax'+row_id).multiselect("refresh");
            //   }
            // }
            update_hidden_tax_value_product(row_id);
          },
        });
        function update_hidden_tax_value_product(RowId){
          let options = $('#product_tax'+RowId+' option:selected');
          let totPercent = 0;
          options.each(function(index, opt){
            let percent = parseFloat($(opt).data("percent"));
            totPercent += percent;
          });
          $('#product_tax_hidden'+RowId).val(totPercent);
          update_amounts();
        }
      @endif
      $('.product_discount_type_selection').select2();
      @if($order->product_level_discount_flag==1)
        // const regexPattern = /^[0]+$/;
        $('.product_discount_type_selection').change(function(){
          let rowId = $(this).data("id");
          let val = $(this).val();
          let trigger = true;
          if(val=="%"){
            $('#discount-percent-symbol'+rowId).removeClass('hidden');
            $('#discount-rs-symbol'+rowId).addClass('hidden');
            if($('#product_discount'+rowId).val()>100){
              $('#product_discount'+rowId).val("");
            }
          }else if(val=="Amt"){
            $('#discount-rs-symbol'+rowId).removeClass('hidden');
            $('#discount-percent-symbol'+rowId).addClass('hidden');
          }
          if(trigger)
            update_amounts();
        });
        $('.product_discount').focusout(function(){
          let rowId = $(this).data("id");
          let trigger = true;
          @if($getClientSetting->non_zero_discount==1)
            if($(this).val()!=""){
              if(regexPattern.test($(this).val()) || $(this).val()==0){
                alert("Discount cannot be zero.");
                $(this).val("");
              }
            }
          @endif
          if($(this).val()>100 && $('#discount-rs-symbol'+rowId).hasClass("hidden")){
            $(this).val("");
          }
          if(trigger)
            update_amounts();
        });
      @endif
    });

    function showLoader(){
      $(document).find('#dynamic_field').addClass('loaderOpacityControl');
      $(document).find('.loaderDiv').removeClass('hidden');
    }
    
    function hideLoader(){
      $(document).find('#dynamic_field').removeClass('loaderOpacityControl');
      $(document).find('.loaderDiv').addClass('hidden');
    }

    // if(enablePartyRate){
    //   $(".content").on("change", "#order_clients", function () {
    //     $('.product').each(function(){
    //       if($(this).val()!=""){
    //         $(this).trigger('change');
    //       }
    //     });
    //   });
    // }
    @if($getClientSetting->order_with_amt!=1 && $getClientSetting->accounting==1)
      $('form[id="orderForm"]').on('submit', function(e) {
        debugger;
        e.preventDefault();
        let current = $(this);
        validateCreditLimit(current);
      });

      function validateCreditLimit (formEl){
        let grandTotal = formEl.find('.grand_total').val();
        let clientId = formEl.find('#order_clients').val();
        $.ajax({
          type: 'POST',
          url: "{{ domain_route('company.admin.order.suffCreditLimit') }}",
          data: {
            'id': clientId,
            'grand_total': grandTotal,
            'order_id': {{$order->id}},
            'msg': 'Insufficient credit limit. Order could not be edited.'  
          },
          beforeSend: function(){
            showLoader();
          },
          success: function (data) {
            if(data.success){
              formEl.unbind("submit").submit();
              $('#submitOrder').attr("disabled", true);
            }else{
              alert(data.msg);
              $('#submitOrder').attr("disabled", false);
              return false;
            }
          },
          complete:function(){
            hideLoader();
          }
        });
      }
    @endif


    $(".table").on("change", ".prodrow .product", function () {
      let selClientId = $('#order_clients option:selected').val();
      let rateId = $('#order_clients option:selected').data('rate');

      let currentElement = $(this);
      let prod = parseInt(currentElement.val());
      let prodValue = currentElement.select2().find(":selected").data("value");
      var rowid = $(currentElement).attr("id");
      $(`#product_variant${rowid}`).html('');
      $(`#variant_colors${rowid}`).empty();
      let orderPlacedByRetailer = "{{($order->employee_id==0)?true:false}}";
      @if($order->product_level_tax_flag==1)
        let prodTax = currentElement.select2().find(":selected").data("taxes");
        let defaultTax = @json($getAllDefault_taxes);
        let options = [];
        let pushedIds = [];
        let totTaxInput = 0;
        $.each(prodTax, function(index){
          let option = {};
          option["label"] = prodTax[index]['name'];
          option["title"] = prodTax[index]['name'];
          option["value"] = prodTax[index]['id'];
          pushedIds.push(prodTax[index]['id']);
          option["selected"] = true;
          option["attributes"] = {};
          option["attributes"]["percent"] = prodTax[index]['percent'];
          totTaxInput += parseFloat(prodTax[index]['percent']);
          option["attributes"]["default_flag"] = prodTax[index]['default_flag'];
          option["attributes"]["row_id"] = rowid;
          options.push(option);
        });
        $.each(defaultTax, function(index){
          if($.inArray(defaultTax[index]['id'], pushedIds)==-1){
            let option = {};
            option["label"] = defaultTax[index]['name'];
            option["title"] = defaultTax[index]['name'];
            option["value"] = defaultTax[index]['id'];
            option["selected"] = false;
            option["attributes"] = {};
            option["attributes"]["percent"] = defaultTax[index]['percent'];
            option["attributes"]["default_flag"] = defaultTax[index]['default_flag'];
            option["attributes"]["row_id"] = rowid;
            options.push(option);
          }
        });
        $('#product_tax_hidden'+rowid).val(totTaxInput);
        $('#product_tax'+rowid).multiselect('dataprovider', options);
      @endif

      let qty = 1;
      let productName = $('option:selected', currentElement).data("product_name");
      $(`#product_name${rowid}`).val(productName);
      let brand = $('option:selected', currentElement).data("brand");
      $(`#brand${rowid}`).val(brand);

      if(prod == prodValue){
        $(`#product_variant${rowid}`).attr('disabled', false);
        $(`#variant_colors${rowid}`).attr('disabled', false);
        let mrp_amount = 0.00;
        @if($getClientSetting->order_with_amt==0) $(`#utype${rowid}`).text('Per Unit');
        @else $(`#utype${rowid}`).text('Unit'); @endif
        $(`#u_id${rowid}`).val("");
        $(`#qty${rowid}`).val(qty);
        // $(`#mrp${rowid}`).val(mrp_amount);
        // $(`#rate${rowid}`).val(mrp_amount);
        // $(`#hidden-rate${rowid}`).val(mrp_amount);
        // $(`#amt${rowid}`).val((qty * mrp_amount).toFixed(2));
        $(`#short_desc${rowid}`).val("");
        // $(`#product_variant_name${rowid}`).val("");
        // $('.subtotal').val((qty * mrp_amount).toFixed(2));
        // $('.total').val((qty * mrp_amount).toFixed(2));
        $.ajax({
          type: 'POST',
          url: "{{ domain_route('company.admin.get.productVariant') }}",
          data: {
            'id': prod,
            'enablePartyRate': enablePartyRate,
            'partyId': selClientId,
            'rateId': rateId
          },
          beforeSend: function(){
            showLoader();
          },
          success: function (data) {
            let prodVariantCount = data.length;
            if(prodVariantCount>0){
              let slicedShortDesc;
              data.forEach(element => {
                if(element.short_desc){
                  slicedShortDesc = `(${element.short_desc.substr(0, 20)})`;
                }else {
                  slicedShortDesc = "";
                }

                let variantColors;
                if(element.variant_colors){
                  variantColors = element.variant_colors;//JSON.parse(element.variant_colors);
                }else{
                  variantColors = "null";
                }

                $('#product_variant'+rowid+'').append(`<option value='${element.id}' data-value="${element.product_id}" mrp="${element.mrp}" unit_id="${element.unit}" unittype="${element.unit_types}" data-variant_name="${element.variant}" data-short_desc="${element.short_desc}" colors=${variantColors}>${element.variant}${slicedShortDesc}</option>`);
              });            
            }
            $(`#product_variant${rowid}`).trigger('change');
            update_amounts();
            hideLoader();
          },
          complete:function(){
            hideLoader();
          }
        });
      }else{
        let mrp_amount = $('option:selected', currentElement).attr("mrp");
        if(enablePartyRate && rateId){
          $.ajax({
            type: 'POST',
            async: true,
            url: "{{ domain_route('company.admin.get.productPartiesMrp') }}",
            data: {
              'id': prod,
              'enablePartyRate': enablePartyRate,
              'partyId': selClientId,
              'rateId': rateId,
              'default_mrp': mrp_amount
            },
            beforeSend: function(){
              showLoader();
            },
            success: function (data) {
              mrp_amount = data;
              
              $(`#product_variant${rowid}`).append(`<option value="null" selected>No Variants</option>`);
              $(`#variant_colors${rowid}`).append(`<option value="null" selected>No Attributes</option>`);
              $(`#product_variant${rowid}`).attr('disabled', true);
              $(`#variant_colors${rowid}`).attr('disabled', true);
              let unittype = $('option:selected', currentElement).attr("unittype");
              let unit_id = $('option:selected', currentElement).attr("unit_id");
              let short_desc = $('option:selected', currentElement).data("short_desc");

              $(`#mrp${rowid}`).val(mrp_amount);
              $(`#rate${rowid}`).val(mrp_amount);
              $(`#hidden-rate${rowid}`).val(mrp_amount);
              @if($getClientSetting->unit_conversion==0)
                @if($getClientSetting->order_with_amt==0) $(`#utype${rowid}`).text('Per ' + unittype);
                @else $(`#utype${rowid}`).text(unittype); @endif
                $(`#u_id${rowid}`).val(unit_id);
              @else
                $(`#hidden_u_id${rowid}`).val(unit_id);
                $(`#hidden_mrp${rowid}`).val(mrp_amount);
                $(`#u_id${rowid}`).val(unit_id);
                updateUnitSelection(unit_id, rowid, prod);
              @endif
              $(`#qty${rowid}`).val(qty);
              $(`#amt${rowid}`).val((qty * mrp_amount).toFixed(2));
              $(`#short_desc${rowid}`).val(short_desc);
              $(`#product_variant_name${rowid}`).val("");
              $('.subtotal').val((qty * mrp_amount).toFixed(2));
              $('.total').val((qty * mrp_amount).toFixed(2));
              update_amounts();
            },
            complete:function(){
              hideLoader();
            }
          });
        }else{
          $(`#product_variant${rowid}`).append(`<option value="null" selected>No Variants</option>`);
          $(`#variant_colors${rowid}`).append(`<option value="null" selected>No Attributes</option>`);
          $(`#product_variant${rowid}`).attr('disabled', true);
          $(`#variant_colors${rowid}`).attr('disabled', true);
          let unittype = $('option:selected', currentElement).attr("unittype");
          let unit_id = $('option:selected', currentElement).attr("unit_id");
          let short_desc = $('option:selected', currentElement).data("short_desc");

          $(`#mrp${rowid}`).val(mrp_amount);
          $(`#rate${rowid}`).val(mrp_amount);
          $(`#hidden-rate${rowid}`).val(mrp_amount);
          @if($getClientSetting->unit_conversion==0)
            @if($getClientSetting->order_with_amt==0) $(`#utype${rowid}`).text('Per ' + unittype);
            @else  $(`#utype${rowid}`).text(unittype); @endif
            $(`#u_id${rowid}`).val(unit_id);
          @else
            $(`#hidden_u_id${rowid}`).val(unit_id);
            $(`#hidden_mrp${rowid}`).val(mrp_amount);
            $(`#u_id${rowid}`).val(unit_id);
            updateUnitSelection(unit_id, rowid, prod);
          @endif
          $(`#qty${rowid}`).val(qty);
          $(`#amt${rowid}`).val((qty * mrp_amount).toFixed(2));
          $(`#short_desc${rowid}`).val(short_desc);
          $(`#product_variant_name${rowid}`).val("");
          $('.subtotal').val((qty * mrp_amount).toFixed(2));
          $('.total').val((qty * mrp_amount).toFixed(2));
          update_amounts();

        }
      }
    });
    
    $(".table").on("change", ".prodrow .productvariant", function () {
      let selClientId = $('#order_clients option:selected').val();
      let rateId = $('#order_clients option:selected').data('rate');
      let currentElement = $(this);
      let rowid = $(currentElement).attr("data-id");
      let prod = $(this).val();
      // let prodValue = $(this).find(":selected").data("value");
      let short_desc = $('option:selected', currentElement).data("short_desc");
      let product_variant_name = $('option:selected', currentElement).data("variant_name");
      $(`#product_variant_name${rowid}`).val(product_variant_name);
      
      let unittype = $('option:selected', currentElement).attr("unittype");
      let unit_id = $('option:selected', currentElement).attr("unit_id");
      let mrp_amount = $('option:selected', currentElement).attr("mrp");
      if(enablePartyRate && rateId){
        $.ajax({
          type: 'POST',
          async: true,
          url: "{{ domain_route('company.admin.get.variantPartiesMrp') }}",
          data: {
            'id': prod,
            'enablePartyRate': enablePartyRate,
            'partyId': selClientId,
            'rateId': rateId,
            'default_mrp': mrp_amount
          },
          beforeSend: function(){
            showLoader();
          },
          success: function (data) {
            mrp_amount = data;
            
            let qty = 1;
            let amt = 0.00;
            $(`#mrp${rowid}`).val(mrp_amount);
            $(`#rate${rowid}`).val(mrp_amount);
            $(`#hidden-rate${rowid}`).val(mrp_amount);
            @if($getClientSetting->unit_conversion==0)
              @if($getClientSetting->order_with_amt==0) $(`#utype${rowid}`).text('Per ' + unittype);
              @else $(`#utype${rowid}`).text(unittype); @endif
              $(`#u_id${rowid}`).val(unit_id);
            @else
              $(`#hidden_u_id${rowid}`).val(unit_id);
              $(`#hidden_mrp${rowid}`).val(mrp_amount);
              $(`#u_id${rowid}`).val(unit_id);
              updateUnitSelection(unit_id, rowid, prod);
            @endif
            $(`#qty${rowid}`).val(qty);
            $(`#amt${rowid}`).val((qty * mrp_amount).toFixed(2));
            $(`#short_desc${rowid}`).val(short_desc);
            $('.subtotal').val((qty * mrp_amount).toFixed(2));
            $('.total').val((qty * mrp_amount).toFixed(2));
            update_amounts();

            // $(`#variant_colors${rowid}`).empty().multiselect('reload');
            $(`#variant_colors${rowid}`).empty();

            let colors = $('option:selected', currentElement).attr("colors");
            
            if(colors!="null"){
              $(`#variant_colors${rowid}`).attr("disabled", false);
              colors = JSON.parse(colors);
              let colorsPassed = @json($colors);
              
              // $(`#variant_colors${rowid}`).append(`<option value="null">Select Attribute</option>`);
              colors.forEach(color => {
                $(`#variant_colors${rowid}`).append(`<option value=${color}>${colorsPassed[color]}</option>`);
              });
              // $(`#variant_colors${rowid}`).multiselect('reload');             
            }else{
              $(`#variant_colors${rowid}`).attr("disabled", true);
              $(`#variant_colors${rowid}`).append(`<option value="null">No Attributes</option>`);
            }
          },
          complete:function(){
            hideLoader();
          }
        });
      }else{
        let qty = 1;
        let amt = 0.00;
        $(`#mrp${rowid}`).val(mrp_amount);
        $(`#rate${rowid}`).val(mrp_amount);
        $(`#hidden-rate${rowid}`).val(mrp_amount);
        @if($getClientSetting->unit_conversion==0)
          @if($getClientSetting->order_with_amt==0) $(`#utype${rowid}`).text('Per ' + unittype);
          @else $(`#utype${rowid}`).text(unittype); @endif
        $(`#u_id${rowid}`).val(unit_id);
        @else
        $(`#hidden_u_id${rowid}`).val(unit_id);
        $(`#hidden_mrp${rowid}`).val(mrp_amount);
        $(`#u_id${rowid}`).val(unit_id);
        updateUnitSelection(unit_id, rowid, prod);
        @endif
        $(`#qty${rowid}`).val(qty);
        $(`#amt${rowid}`).val((qty * mrp_amount).toFixed(2));
        $(`#short_desc${rowid}`).val(short_desc);
        $('.subtotal').val((qty * mrp_amount).toFixed(2));
        $('.total').val((qty * mrp_amount).toFixed(2));
        update_amounts();
        
        // $(`#variant_colors${rowid}`).empty().multiselect('reload');
        $(`#variant_colors${rowid}`).empty();
        
        let colors = $('option:selected', currentElement).attr("colors");
        
        if(colors!="null"){
        $(`#variant_colors${rowid}`).attr("disabled", false);
        colors = JSON.parse(colors);
        let colorsPassed = @json($colors);
        
        // $(`#variant_colors${rowid}`).append(`<option value="null">Select Attribute</option>`);
        colors.forEach(color => {
        $(`#variant_colors${rowid}`).append(`<option value=${color}>${colorsPassed[color]}</option>`);
        });
        // $(`#variant_colors${rowid}`).multiselect('reload');
        }else{
        $(`#variant_colors${rowid}`).attr("disabled", true);
        $(`#variant_colors${rowid}`).append(`<option value="null">No Attributes</option>`);
        }
      }
    });


    @if($getClientSetting->unit_conversion==1)
      function updateUnitSelection(unit_id, rowid, product_id){
        $("#unit_symbol_type_selection"+rowid).empty();
        $.ajax({
          type: 'GET',
          url: "{{ domain_route('company.admin.unit.getrelatedunits') }}",
          data: {
            'unit_id': unit_id,
            'product_id': product_id
          },
          beforeSend: function(){
            showLoader();
            $('#submitOrder').attr("disabled", true);
          },
          success: function (data) {
            if(data!=""){
              let parseData = JSON.parse(data);
              let state = false;
              $.each(parseData, function(value, text){
                if(value==unit_id) state=true;
                else state=false;
                var newState = new Option(text, value, state, state);
                // Append it to the select
                $(`#unit_symbol_type_selection${rowid}`).append(newState);//.trigger('change');
              });
            }
            hideLoader();
            $('#submitOrder').attr("disabled", false);
          },
          error: function(){
            hideLoader();
            $('#submitOrder').attr("disabled", false);
          }
        });
      }

      $('#dynamic_field').on('change', '.unit_symbol_type_selection', function(){
        let rowid = $(this).data('id');
        let originalUnit = $(`#hidden_u_id${rowid}`).val();
        let selUnit = this.value;
        let product_id = $(this.parentElement.parentElement.parentElement.parentElement).find('.product option:selected').val();
        if(originalUnit==selUnit){
          $(`#mrp${rowid}`).val($(`#hidden_mrp${rowid}`).val());
          @if($order->product_level_discount_flag==0)
          $(`#rate${rowid}`).val($(`#hidden_mrp${rowid}`).val());
          @endif
          update_amounts();
        }else{
          if($(this).hasClass("edit_unit_symbol_type_selection")) return false;
          $.ajax({
            type: 'GET',
            url: "{{ domain_route('company.admin.unit.getunitsmrp') }}",
            data: {
              'originalUnit': originalUnit,
              'selUnit': selUnit,
              'product_id': product_id 
            },
            success: function (data) {
              let multipliedFactor = $(`#hidden_mrp${rowid}`).val()*data;
              $(`#mrp${rowid}`).val(multipliedFactor.toFixed(2));
              
             @if($order->product_level_discount_flag==0)
              $(`#rate${rowid}`).val(multipliedFactor.toFixed(2));
              @endif
              update_amounts();
            },
            error: function(jqXhr){
              debugger;
            }
          });
        }
      });

      $('#dynamic_field').on('change', '.edit_unit_symbol_type_selection', function(){
        let rowid = $(this).data('id');
        let originalUnit = $(`#original_u_id${rowid}`).val();
        let originalMrp = $(`#original_u_mrp${rowid}`).val();
        let selUnit = this.value;
        let product_id = $(this.parentElement.parentElement.parentElement.parentElement).find('.product option:selected').val();
        if(originalUnit==selUnit){
          $(`#mrp${rowid}`).val(originalMrp);
          @if($order->product_level_discount_flag==0)
          $(`#rate${rowid}`).val(originalMrp);
          @endif
          update_amounts();
        }else{
          $.ajax({
            type: 'GET',
            url: "{{ domain_route('company.admin.unit.getunitsmrp') }}",
            data: {
              'originalUnit': originalUnit,
              'selUnit': selUnit,
              'product_id': product_id 
            },
            success: function (data) {
              let multipliedFactor = originalMrp*data;
              $(`#mrp${rowid}`).val(multipliedFactor.toFixed(2));
              @if($getClientSetting->product_level_discount_flag==0)
              $(`#rate${rowid}`).val(multipliedFactor.toFixed(2));
              @endif
              update_amounts();
            },
            error: function(jqXhr){
              debugger;
            }
          });
        }
      });
    @endif
    $(document).on('click', '.removeRowBtn', function () {
      let orderRowCount = $('.oldprodrow').length;
      if(orderRowCount>1){
        let button_id = $(this).data("rownum");
        let orderNo = $(this).data("orderno");
        let lastRowIdBeforeRemove = $("#dynamic_field").find("tr").last().data('rownum');

        if(orderNo==""){
          $(`#row${button_id}`).remove();
          let lastRowIdAfterRemove = $("#dynamic_field").find("tr").last().data('rownum');
          if(lastRowIdBeforeRemove == lastRowIdAfterRemove){
            console.log("Keep Hidden.");
          }else{
            $(`#add${lastRowIdAfterRemove}`).removeClass("hidden");
          }
          @if($order->module_status->order_delete_flag==1)
            let getCountRow = $(".prodrow").length;
            if(getCountRow<=1){
              $(`#remove${lastRowIdAfterRemove}`).addClass("hidden");
            }
          @endif
          update_amounts();
        }else if(orderNo!=""){
          $('#deleteorderproducts').modal('show');
          $('#formDeleteOrderProducts').on("submit", function(e){
            e.preventDefault();
            let deleteUrl = "{{domain_route('company.admin.order.deleteOrderProducts', [':urlId'])}}";
            deleteUrl = deleteUrl.replace(':urlId', {{$order->id}});
            $.ajax({
              async: false,
              url: deleteUrl,
              type: "POST",
              cache: false,
              data: {
                "_method": 'delete',
                "_token": "{{ csrf_token() }}",
                "id": {{$order->id}},
                "orderProductsId": orderNo,
              },
              beforeSend: function(){
                $('.addCancelBtn').attr("disabled", true);
                $('#deleteConfirm').attr("disabled", true);
              },
              success: function (response) {
                if(response.statuscode==200){
                  $(`#row${button_id}`).remove();
                  update_amounts();
                  alert(response.message);
                  $(`#row${button_id}`).remove();
                  let lastRowIdAfterRemove = $("#dynamic_field").find("tr").last().data('rownum');
                  if(lastRowIdBeforeRemove == lastRowIdAfterRemove){
                    console.log("Keep Hidden.");
                  }else{
                    $(`#add${lastRowIdAfterRemove}`).removeClass("hidden");
                  }
                  @if($order->module_status->order_delete_flag==1)
                    let getCountRow = $(".prodrow").length;
                    if(getCountRow<=1){
                      $(`#remove${lastRowIdAfterRemove}`).addClass("hidden");
                    }
                  @endif
                  $('.addCancelBtn').attr("disabled", false);
                }else if(response.statuscode==400){
                  alert(response.message);
                }
                $('#deleteConfirm').attr("disabled", false);
                $('#deleteorderproducts').modal('hide');
                update_order_amounts({{$order->id}});
              },
              error: function(jqXHR, textStatus, errorThrown) {
                $('#deleteConfirm').attr("disabled", false);
                alert(textStatus);
                $('#deleteorderproducts').modal('hide');
                $('.addCancelBtn').attr("disabled", false);
              },
            });
          });
        }
      }else{
        alert("You Cannot remove all the orders. At least a field is compulsary.");
      }
    });

    function update_order_amounts(orderId){
      let subtotal = $('.subtotal').val();
      let grandtotal = $('.grand_total').val();
      let updateUrl = "{{domain_route('company.admin.order.updateorderamount', [':urlId'])}}";
          updateUrl = updateUrl.replace(':urlId', orderId);
      $.ajax({
        async: false,
        url: updateUrl,
        type: "POST",
        cache: false,
        data: {
          "_method": 'delete',
          "_token": "{{ csrf_token() }}",
          "id": orderId,
          "subtotal": subtotal,
          "grandtotal": grandtotal
        },
        beforeSend: function(){
          $('#submitOrder').attr("disabled", true);
        },
        success: function (response) {
          if(response.statuscode==200){
            $('#submitOrder').attr("disabled", false);
            return true;
          }else if(response.statuscode==400){
            $('#submitOrder').click();
            return true;
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $('#submitOrder').attr("disabled", false);
          alert(textStatus);
        },
      });
    }

    $(document).on('focusout', '.rate', function () {
      var dataid = $(this).data("id");
      var mrp = $('#mrp' + dataid).val();
      var rate = $('#rate' + dataid).val();
      if (rate < 0) {
        alert('Rate must be greater than or equals to 0');
        $('#rate' + dataid).val(mrp);
      }
      update_amounts();
    });

    $(document).on('keyup', '.qty', function () {
      var dataid = $(this).data("id");
      var qty = $('#qty' + dataid).val();
      if (qty < 0) {
        alert('Quantity must be greater than 0');
        $('#qty' + dataid).val(1);
      }
      update_amounts();
    });

    $(document).on('keyup', '.discount', function () {
      var disc = $(this).val();
      let subtotal = parseFloat($('.subtotal').val());
      if (disc < 0) {
          alert('Discount must be greater than 0');
          @if($getClientSetting->non_zero_discount==0)
          $('.discount').val(0);
          @endif
          if(disc>subtotal){
            $('.discount').val("");
            alert("Discount can't be greater than subtotal.");
          }
      }
      if ($("#disctype option:selected").val() == '%') {
        if (disc > 100) {
          alert('Discount percent must be lesser than 100');
          @if($getClientSetting->non_zero_discount==0)
            $('.discount').val(0);
          @else
            $('.discount').val('');
          @endif
        }
      }
      if ($("#disctype option:selected").val() == 'Amt') {
        var disc = $('.discount').val();
        let subtotal = parseFloat($('.subtotal').val());
        
        if(parseFloat(disc)>subtotal){
          $('.discount').val("");
          alert("Discount can't be greater than subtotal.");
        }
      }
      update_amounts();
    });

    $('#disctype').change(function(){
      if ($("#disctype option:selected").val() == '%') {
        if ($('#discount').val() > 100) {
          alert('Discount percent must be lesser than 100');
          @if($getClientSetting->non_zero_discount==0)
          $('.discount').val(0);
          @else
          $('.discount').val('');
          @endif
        }
        update_amounts();
      }else if ($("#disctype option:selected").val() == 'Amt') {
        if ($("#disctype option:selected").val() == 'Amt') {
          var disc = $('.discount').val();
          let subtotal = parseFloat($('.subtotal').val());
          
          if(parseFloat(disc)>subtotal){
            $('.discount').val("");
            alert("Discount can't be greater than subtotal.");
          }
        }
        if(parseFloat(disc)>subtotal){
          $('.discount').val("");
          alert("Discount can't be greater than subtotal.");
        }
        update_amounts();
      }
    });

    $(document).on('keyup', '.tax', function () {
        taxval = $('#tax').val();
        if (taxval > 100) {
          alert('Tax Value cannot exceed 100%');
          $('.tax').val(0);
          update_amounts();

        } else {
          update_amounts();
        }
    });

    $(document).on('change', '#disctype', function () {
      if ($("#disctype option:selected").val() == "%") {
        $("#for_amount").hide();
        @if($getClientSetting->non_zero_discount==0)
          $('.discount').val(0);
        @endif
      } else if ($("#disctype option:selected").val() == "Amt") {
        $("#for_amount").show();
        @if($getClientSetting->non_zero_discount==0)
          $('.discount').val(0);
        @endif
      }
      update_amounts();
      let disctype = $('#disctype').val();
    });

    @if($order->product_level_discount_flag==1)
      // const regexPattern = /^[0]+$/;
      $("#dynamic_field").on('change', '.product_discount_type_selection', function(){
        let rowId = $(this).data("id");
        let val = $(this).val();
        let trigger = true;
        if(val=="%"){
          $('#discount-percent-symbol'+rowId).removeClass('hidden');
          $('#discount-rs-symbol'+rowId).addClass('hidden');
          if($('#product_discount'+rowId).val()>100){
            $('#product_discount'+rowId).val("");
          }
        }else if(val=="Amt"){
          $('#discount-rs-symbol'+rowId).removeClass('hidden');
          $('#discount-percent-symbol'+rowId).addClass('hidden');
          $('#product_discount'+rowId).trigger('focusout');
        }else if(val=="oAmt"){
          $('#discount-rs-symbol'+rowId).removeClass('hidden');
          $('#discount-percent-symbol'+rowId).addClass('hidden');
          $('#product_discount'+rowId).trigger('focusout');
        }
        if(trigger)
          update_amounts();
      });
      $("#dynamic_field").on('focusout', '.product_discount', function(){
        let rowId = $(this).data("id");
        let trigger = true;
        let value = parseFloat($(this).val());
        let discountTypeSel = $('#product_discount_type_selection'+rowId).val();
        let mrpVal = parseFloat($('#mrp'+rowId).val());
        let amountVal = parseFloat($('#amt'+rowId).val());
        @if($getClientSetting->non_zero_discount==1)
          if(value!=""){
            if(regexPattern.test(value) || value==0){
              alert("Discount cannot be zero.");
              $(this).val("");
              trigger= false;
            }
          }
        @endif
        if(value>100 && discountTypeSel=="%"){
          $(this).val("");
          trigger= false;
        }else if(value>mrpVal && discountTypeSel=="Amt"){
          $(this).val("");
          trigger= true;
        }else if(value>amountVal && discountTypeSel=="oAmt"){
          $(this).val("");
          trigger= true;
        }
        if(trigger)
          update_amounts();
      });
    @endif

    function update_amounts() {
      let subtotal = total = discountTotAmount = taxTotalAmt = grandtot = 0.0;
      let buildTaxFields = [];
      $('.prodrow').each(function () {
        if($(this).find('.mrp').val()!=""){

          let qty = $(this).find('.qty').val();
          @if($order->product_level_discount_flag==1)
          // let rate = $(this).find('.mrp').val();//$(this).find('.hidden-rate').val();
          let rate = parseFloat($(this).find('.mrp').val());//parseFloat($(this).find('.hidden-rate').val());
          @else
          let rate = $(this).find('.rate').val();//$(this).find('.hidden-rate').val();
          @endif
          let qtyRate = qty * rate;
          subtotal += qtyRate;

          @if($order->product_level_discount_flag==1)
            let prodDiscount = $(this).find('.product_discount').val();
            if(prodDiscount!=""){ 
              prodDiscount = parseFloat(prodDiscount);
              let prodDiscountType = $(this).find('.product_discount_type_selection option:selected').val();
              if(prodDiscountType=='%'){
                let discountAmount = (prodDiscount*rate)/100;
                qtyRate = qty*(rate-discountAmount);
                $(this).find('.rate').val(rate-discountAmount);
                // qtyRate = qtyRate-discountAmount;
                // discountTotAmount = discountTotAmount+discountAmount;
                discountTotAmount = discountTotAmount+discountAmount*qty;
              }else if(prodDiscountType=='Amt'){
                if(rate-prodDiscount<0){
                  $(this).find('.product_discount').val("");
                  $(this).find('.rate').val(rate);
                  qtyRate = qty*rate;
                  discountTotAmount += 0;
                }else{
                  $(this).find('.rate').val(rate-prodDiscount);
                  qtyRate = qty*(rate-prodDiscount);
                  // qtyRate = qtyRate-prodDiscount;
                  // discountTotAmount = discountTotAmount+prodDiscount;
                  discountTotAmount = discountTotAmount+prodDiscount*qty;

                }
              }else if(prodDiscountType=='oAmt'){
                if(rate*qty-prodDiscount<0){
                  $(this).find('.product_discount').val("");
                  $(this).find('.rate').val(rate);
                  qtyRate = (qty*rate);
                  discountTotAmount += 0;
                }else{
                  $(this).find('.rate').val(rate);
                  qtyRate = (qty*rate)-prodDiscount;
                  // qtyRate = qtyRate-prodDiscount;
                  // discountTotAmount = discountTotAmount+prodDiscount;
                  discountTotAmount += prodDiscount;

                }
              }
              total += qtyRate;
              // $('#discount').val(discountTotAmount.toFixed(2));
            }else{
              qtyRate = qtyRate;
              total += qtyRate;
              $(this).find('.rate').val(rate);
            }
            $('#discount').val(discountTotAmount.toFixed(2));
            // $(this).find('.rate').val(qtyRate);
          @else
            total = subtotal;
          @endif
          
          @if($order->product_level_tax_flag==1)
            let taxPercent = parseInt($(this).find('.product_tax_hidden').val());
            let taxSelected = $(this).find('.product_tax option:selected');
            $.each(taxSelected, function(index, option){
              let optionList = {};
              optionList['value'] = option.value;
              optionList['text'] = option.text;
              let percent = parseFloat(option.dataset["percent"]);
              optionList['percent'] = percent;
              optionList['qtyRate'] = qtyRate;
              optionList['amount'] = parseFloat((percent*qtyRate)/100);
              buildTaxFields.push(optionList);
            });
            if(taxPercent>0){
              let taxAmount = (taxPercent/100)*qtyRate;
              let taxAddedAmount = qtyRate + taxAmount;
              qtyRate = taxAddedAmount;
              taxTotalAmt = taxTotalAmt+taxAmount;
            }
            $('#total_tax').val(taxTotalAmt.toFixed(2));
          @endif

          let amount = qtyRate;
          $(this).find('.amt').val(amount.toFixed(2));
        }else{
          $('#total_tax').val("");
          $('#discount').val("");
        }
      });
      @if($order->product_level_tax_flag==1)
      $('#implied-tax').html('');
      $.each(buildTaxFields, function(index, option){
        let optionId = option['value'];
        let optionText = option['text'];
        let optionPercent = option['percent'];
        let amount = option['amount'];
        
        let el = `<div class="tax_row${optionId}" id="tax_row${optionId}" data-id="${optionId}">
          <div class="col-xs-12">
            <label for="product_level_tax" class="col-xs-12 control-label">${optionText}</label>
          </div>
          <div class="col-xs-12">
            <input type="text" class="form-control tax" id="tax${optionId}" placeholder="${optionPercent}"
              value="" readonly>
          </div>
        </div>`;
        if($('.tax_rows').find(`.tax_row${optionId}`).length==0){
          $('#implied-tax').prepend(el);
          $(`#tax${optionId}`).val(parseFloat(amount).toFixed(2));
        }else{
          let currentVal = parseFloat($(`#tax${optionId}`).val()) + parseFloat(amount);
          $(`#tax${optionId}`).val(parseFloat(currentVal).toFixed(2));
        }
      });
      @endif
      $('.subtotal').val(subtotal.toFixed(2));
      @if($order->product_level_discount_flag==0)
        let discountAmt  = 0;
        let discount = $('#discount').val();
        if(discount!=""){
          let discountType = $('#disctype').val();
          if(discountType == '%'){
            discountAmt = (discount/100)*subtotal;
          }else{
            discountAmt = discount;
          }
          total = subtotal-discountAmt;
          if(subtotal<discountAmt){
            alert("Discount cannot be more than subtotal");
            $('#discount').val("");
          }
        }
      @endif
      $('#total').val(total.toFixed(2));
      @if($order->product_level_tax_flag==0)
      let taxesApplied = $('.tax_row');
      if(taxesApplied.length>0){
        taxesApplied.each(function(){
          let taxPercent = $(this).find('.tax_percents').val();
          let taxApplicable = (taxPercent*total)/100;
          $(this).find('.tax').val(taxApplicable.toFixed(2));
          taxTotalAmt += taxApplicable;
        });
      }
      @endif
      grandtot = total+taxTotalAmt;
      $('.grand_total').val(grandtot.toFixed(2));
    }

    $(".onlynumber").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $(".content").on("focusout", ".validateFloatVal", function (e) {
      let currentVal = $(this).val();
      const floatvalRegexPattern = /^\d*(\.\d{2})?$/;
      const alphabetRegexPattern = /^[a-zA-Z]+$/;
      if(currentVal!=""){
        // if(floatvalRegexPattern.test(parseFloat(currentVal))){
          $(this).val(parseFloat(currentVal).toFixed(2));
        // }else{
          if(alphabetRegexPattern.test(currentVal)){
            $(this).val("");
          }
        // }
      }
      update_amounts();
  });

    @if(config('settings.ncal')==1)
      $('#order_datenew').val(AD2BS("{{$order->order_date}}"));
      $('#englishDate').val("{{$order->order_date}}");
    @endif
   

    $('body').on('click', '#submitOrder', function(e){
      update_amounts();
      let getRowCount = $('.prodrow').length;
      if(getRowCount>1){
        let lastRow = $('.prodrow').last();
        if(lastRow.find('.product').val()==""){
          lastRow.remove();
          return true
        }
      }else if(getRowCount<1){
        e.preventDefault();
      }
    });

   @if($getClientSetting->non_zero_discount==1 )
      // const regexPattern = /^[0]+$/;
      $('#discount').focusout(function(){
        if(regexPattern.test($(this).val()) ){
            alert("Discount cannot be zero.");
            $(this).val("");
        }
      });
    @endif
  
  });
  
</script>


@endsection