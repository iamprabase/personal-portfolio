@extends('layouts.company')
@section('title', 'Edit Product')
@section('stylesheets')
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link href="{{asset ('assets/bower_components/bootstrap-toggle.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}" />
<style>
  .action-Btn {
    width: 45%;
  }
  .make-no-reponsive{
    overflow-x: unset !important;
  }

  .row{
    margin-right: 0px; 
    margin-left: 0px;
  }

  .width-adjust{
    width: auto;
  }
</style>
@endsection

@section('content')
<section class="content">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Edit Product</h3>
      <div class="page-action pull-right">
        <a href="{{ domain_route('company.admin.product') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i>
          Back</a>
      </div>
    </div>
    <div class="box-body switch-edit">
      {!! Form::open(array('url' => url(domain_route("company.admin.product.update", [$product->id])), 'method' => 'PATCH', 'id'=>'formSubmit','files'=> true)) !!}
      @include('company.products._form')
      {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right','id'=>'submitBtn']) !!}
      {!! Form::close() !!}
    </div>
  </div>
</section>
<div class="modal modal-default fade" id="deleteVariantOrders" tabindex="-1" role="dialog"
  aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Are you sure you want to delete this?
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success cancel" data-dismiss="modal" id="deleteCancel">No,
          Cancel</button>
        <button type="submit" class="btn btn-warning delete-button" id="deleteConfirm">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
<script>
  $(function () {
    $('.select2').select2();
    $('.unitClass').select2({
      'placeholder': 'Select a Unit',
    });
    @if($getClientSetting->product_level_tax==1)
      $('#taxtype').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: true,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Select Tax Types",
      });
    @endif
    @if($getClientSetting->var_colors==1)
      $('.colorClass').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: true,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Select Attributes",
      });
      @if($productVariants->count()==0)
        $('.colorClass').next().addClass('hidden');
        $('.hiddenText').removeClass('hidden');
      @endif
    @endif

    @if($getClientSetting->unit_conversion==1)
      $('#unit_conversion').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: false,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Select Conversions",
        numberDisplayed: 7,
        onChange: function(option, checked, select) {
          if(checked){
            if(!validateContradiction(option, checked)){
              checked=false;
              option[0].selected=false;
              $('#unit_conversion').multiselect("refresh");
              alert("Cannot select multiple conversion for same units.");
            } 
          }
        }
      });

      function validateContradiction(currentOption, checkedStatus){
        let allCheckedInstances = $('.unit_conversion option:selected');
        let filterThisCheckedInstances = allCheckedInstances.filter(function(instance) {
          return $(this).val() != currentOption.val();
        });
        filterThisCheckedInstances.push(currentOption);
        let chosenConversion = {};
        let relationalConversion = [];
        let returnVal = true;
        $.each(filterThisCheckedInstances, function(){
          let currentEl = $(this);
          let attribute = currentEl.data('ids').split(',');
          let conversionUnitId = attribute[0];
          let convertedUnitId = attribute[1];

          // Validate For Unique Conversion Rows
          if(conversionUnitId in chosenConversion){
            if(chosenConversion[conversionUnitId] == convertedUnitId){
              returnVal = false;
              return returnVal;
            }
          }else{
            chosenConversion[conversionUnitId] = convertedUnitId;
          }
          if(Object.keys(chosenConversion).length>1){
            if(convertedUnitId in chosenConversion){
              if(chosenConversion[convertedUnitId] == conversionUnitId){
                returnVal = false;
                return returnVal;
              }
            }else{
              chosenConversion[convertedUnitId] = conversionUnitId;
            }
          }
          relationalConversion.push(attribute);
        });
        if(relationalConversion.length>1){
          for(let loopInd = 0;loopInd < relationalConversion.length; loopInd++){
            let currentLoopEl = relationalConversion[loopInd];
            
            let relationalEl = currentLoopEl[1];
            let currentEl = currentLoopEl[0];
            let treeIteration = validateRelationalUnits(loopInd, relationalConversion, relationalEl, currentEl, returnVal);
            returnVal = treeIteration;
            if(!returnVal) return returnVal;

            relationalEl = currentLoopEl[0];
            currentEl = currentLoopEl[1];
            treeIteration = validateRelationalUnits(loopInd, relationalConversion, relationalEl, currentEl, returnVal);
            returnVal = treeIteration;
            if(!returnVal) return returnVal;

            relationalEl = currentLoopEl[1];
            currentEl = currentLoopEl[1];
            treeIteration = validateRelationalUnits(loopInd, relationalConversion, relationalEl, currentEl, returnVal);
            returnVal = treeIteration;
            if(!returnVal) return returnVal;

            relationalEl = currentLoopEl[0];
            currentEl = currentLoopEl[0];
            treeIteration = validateRelationalUnits(loopInd, relationalConversion, relationalEl, currentEl, returnVal);
            returnVal = treeIteration;
            if(!returnVal) return returnVal;

          }
        }
        return returnVal; 
      }

      function validateRelationalUnits(loopInd, relationalConversion, relationalEl, currentEl, returnVal){
        let relationalConversionClone = relationalConversion;
        let filteredCurrentEl = relationalConversionClone.filter(function(currentValue, index, arr){
                                  return index!=loopInd; 
                                });
        let noOfRelations = filteredCurrentEl.filter(function(currentValue, index, arr){
                                return currentValue[0]==relationalEl||currentValue[1]==relationalEl; 
                              });
        for(let i =0; i<noOfRelations.length; i++){
          for(let nloopInd = 0;nloopInd < filteredCurrentEl.length; nloopInd++){
            
            let ncurrentLoopEl = filteredCurrentEl[nloopInd];
            if(ncurrentLoopEl[0] == relationalEl || ncurrentLoopEl[1] == relationalEl){
              
              if(ncurrentLoopEl[0] == relationalEl){ relationalEl = ncurrentLoopEl[1];} 
              else if(ncurrentLoopEl[1] == relationalEl){ relationalEl = ncurrentLoopEl[0];}
              
              if(relationalEl==currentEl){ returnVal= false; return returnVal;}
              if(filteredCurrentEl.length==nloopInd) return returnVal;
              
              validateRelationalUnits(nloopInd, filteredCurrentEl, relationalEl, currentEl, returnVal);

            }
          }
          filteredCurrentEl = relationalConversionClone.filter(function(currentValue, index, arr){
                                  return noOfRelations[i][0]!=currentValue[0] && noOfRelations[i][1]!=currentValue[1]; 
                                });
        }
        return returnVal;
      }
    @endif

    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    
    $(document).on('change', '.btn-file :file', function () {
      var input = $(this),
          label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
      input.trigger('fileselect', [label]);
    });


    $('.btn-file :file').on('fileselect', function (event, label) {
      var input = $(this).parents('.input-group').find(':text'),
          log = label;
      if (input.length) {
        input.val(log);
      } else {
        if (log) alert(log);
      }
    });

    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#img-upload').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }


    $("#imgInp").change(function () {
      readURL(this);
    });

    $('body').on('click','#remove_entry_del', function(){

      if($('.rowElement').length ==1 && $('#varONOFF').hasClass('OFF')){
        return false;
      }

      if($('.delRow').length ==1 && $('.newrowElement').length >0){
        alert("At least a variant must exist for the product to remove.");
        return false;
      }

    });

    function isNumberKey(evt){
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
      return true;
    } 
  });

  // $(document).ready(function () {
  //   var text_existing = $('#short_desc1').val().length;
  //   var text_max = 60;
  //   var text_remaining = text_max - text_existing;
  //   $('#short_desc_feedback').html(text_remaining + ' characters remaining');
  //   $('#short_desc').keyup(function () {
  //     var text_length = $('#short_desc').val().length;
  //     var text_remaining = text_max - text_length;
  //     $('#short_desc_feedback').html(text_remaining + ' characters remaining');
  //   });
  // });

  $(".onlynumber").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46,190, 8, 9, 27, 13, 110]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
  });

  $('#dynamic_field').on('click', '.btn-add', function(e){
    let currentRowId = $(this).data('id');
    if($('#varONOFF').hasClass('ON') && $(`#variant${currentRowId}`).val() == ""){
      alert("Variant Field cannot be empty.");
      return false;
    }
    
    let rowId = currentRowId+1;
    let nextVariantField = `<input name="newrow_variant[${rowId}]" type="text" class="form-control variantClass" id="variant${rowId}" placeholder="Variant..." required>`;
// <option value="0" selected="selected">No Attributes Selected</option>
    @if($getClientSetting->var_colors == 1)
      let colorsField = `<select class="form-control colorClass" id="colors${rowId}" data-id="${rowId}" multiple name="newrow_var_colors[${rowId}][]">@forelse($colors as $key=>$color)<option value="{{$key}}">{{$color}}</option>@empty<option value="0"></option> @endforelse</select><span class="hidden hiddenText" id="hiddenText${rowId}">Only Variants can have attributes.</span>`;
    @endif
    
    let mrpField = `<input class="form-control onlynumber" placeholder="Rate" id="mrp${rowId}" data-id="${rowId}" required name="newrow_mrp[${rowId}]" type="text" @if(config('settings.order_with_amt')==1) value="0" @endif>`;

    let unitField = `<select class="form-control unitClass" id="unit${rowId}" data-id="${rowId}" name="newrow_unit[${rowId}]" required><option></option>@if(!empty($units)) @foreach($units as $id=>$unit)<option value="{{$id}}">{{$unit}}</option>@endforeach @endif</select>`;

    let shortDescField = `<input class="form-control" id="short_desc${rowId}" rows="${rowId}" cols="54" style="resize:none" maxlength="60" placeholder="Short Description" name="newrow_short_desc[${rowId}]" type="text">`;

    let actionBtn = `<button class="btn btn-danger btn-remove action-Btn" id="remove_entry${rowId}" data-id="${rowId}" type="button">X</button><button class="btn btn-primary pull-right btn-add action-Btn" id="add_entry${rowId}" data-id="${rowId}" type="button">+</button>`;

    @if($getClientSetting->var_colors == 1)
      $('#dynamic_field').append(`<tr class="rowElement" id="rowElement${rowId}" data-row_id="${rowId}"><input type="hidden" name="newrow_numofRows[]" value="${rowId}"><td>${nextVariantField}</td><td>${colorsField}</td><td @if(config('settings.order_with_amt')==1) hidden @endif>${mrpField}</td><td>${unitField}</td><td>${shortDescField}</td><td>${actionBtn}</td></tr>`);
    @else
      $('#dynamic_field').append(`<tr class="rowElement" id="rowElement${rowId}" data-row_id="${rowId}"><input type="hidden" name="newrow_numofRows[]" value="${rowId}"><td>${nextVariantField}</td><td @if(config('settings.order_with_amt')==1) hidden @endif>${mrpField}</td><td>${unitField}</td><td>${shortDescField}</td><td>${actionBtn}</td></tr>`);
    @endif

    $(`#remove_entry${currentRowId}`).removeClass("hidden");
    $(`#add_entry${currentRowId}`).addClass("hidden");

    $('.unitClass').select2({
      'placeholder': 'Select a Unit',
    });
    @if($getClientSetting->var_colors==1)
      
      $('.colorClass').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: true,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Select Attributes",
      });
    @endif
  });

  $('#dynamic_field').on('click','.btn-remove', function(e){
    let rowId = $(this).data('id');
    let productVariantId = $(this).data('variantid');
    let productId = $(this).data('productid');
    let lastRowIdBeforeRemove = $("#dynamic_field").find("tr").last().data('row_id');
    var returnResponse = 0;
    if(productVariantId){
      $('#deleteVariantOrders').modal('show');
      $('#deleteConfirm').off().click(function(e){
        let deleteUrl = "{{ domain_route('company.admin.product.destroy',['requestId']) }}";
        deleteUrl = deleteUrl.replace('requestId', productVariantId);
        // $.ajaxSetup({async:false});
        $.ajax({
          url: deleteUrl,
          type: "POST",
          data: {
            "_method": 'DELETE',
            "_token": "{{ csrf_token() }}",
            "productVariantId": productVariantId,
            "productId": productId,
          },
          beforeSend: function(){
            $('.btn-remove').attr("disabled", true);
            $('#deleteConfirm').attr("disabled", true);
          },
          success: function (response) {
            if(response.statuscode==200){
              $('#dynamic_field').find(`#rowElement${rowId}`).remove();
              $('#deleteVariantOrders').modal('hide');
              alert(response.message);
              let rowCount = $('.rowElement').length; 
              let lastRowIdAfterRemove = $("#dynamic_field").find("tr").last().data('row_id');
              if(rowCount==1){
                $(`#remove_entry${lastRowIdAfterRemove}`).addClass("hidden");
                $(`#add_entry${lastRowIdAfterRemove}`).removeClass("hidden");
                return false;
              }

              if(lastRowIdBeforeRemove==rowId){
                $(`#remove_entry${lastRowIdAfterRemove}`).removeClass("hidden");
                $(`#add_entry${lastRowIdAfterRemove}`).removeClass("hidden");
              }
              location.reload();
            }else if(response.statuscode==400){
              returnResponse = 400;
              $('#deleteVariantOrders').modal('hide');
              // location.reload();
              alert(response.message);
            }
            $('.btn-remove').attr("disabled", false);
            $('#deleteConfirm').attr("disabled", false);
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $(this).attr("disabled", true);
            $('#deleteVariantOrders').modal('hide');
            $('#deleteConfirm').attr("disabled", false);
            alert(textStatus);
            // location.reload();
          },
        });
      });
    }else{
      $('#dynamic_field').find(`#rowElement${rowId}`).remove();
      let rowCount = $('.rowElement').length; 
      let lastRowIdAfterRemove = $("#dynamic_field").find("tr").last().data('row_id');
      if(rowCount==1){
        $(`#remove_entry${lastRowIdAfterRemove}`).addClass("hidden");
        $(`#add_entry${lastRowIdAfterRemove}`).removeClass("hidden");
        return false;
      }

      if(lastRowIdBeforeRemove==rowId){
        $(`#remove_entry${lastRowIdAfterRemove}`).removeClass("hidden");
        $(`#add_entry${lastRowIdAfterRemove}`).removeClass("hidden");
      }
    }
  });

  $('#varONOFF').on('change',function(e){
    let currentElement = $(this);
    let rowCount = $('.rowElement').length;

    if(currentElement.hasClass('OFF')){
      currentElement.addClass('ON');
      currentElement.removeClass('OFF');
      let prodClass = $('.variantClass');
      $.each(prodClass, function(){
        $(this).attr('required', 'required');
      });
      $('#varFlag').val("1");
      $('.variantClass').attr('readonly', false);
      $('.btn-add').attr('disabled',false);
      $('.colorClass').next('.btn-group.width-adjust').removeClass("hidden");
      $('.hiddenText').addClass("hidden");
    }else if(currentElement.hasClass('ON')){
      if(rowCount==1){
        // if($('.allow-edit-variant')) return false;
        currentElement.addClass('OFF');
        currentElement.removeClass('ON');
        let prodClass = $('.variantClass');
        $.each(prodClass, function(){
          $(this).removeAttr('required');
        });
        $('#varFlag').val("0");
        $('.variantClass').val("");
        $('.variantClass').attr('readonly', true);
        $('.btn-add').attr('disabled',true);
        $('.colorClass').next('.btn-group.width-adjust').addClass("hidden");
        $('.hiddenText').removeClass("hidden");
      }else{
        $('#varONOFF').prop('checked',true);
        alert("Please remove variants fields to turn OFF the flag.");
        return false;
      }
    }
  });

  $('#star_productBTN').on('change',function(e){
    let currentElement = $(this);

    if(currentElement.hasClass('OFF')){
      currentElement.addClass('ON');
      currentElement.removeClass('OFF');
      $('#star_product').val("1");
    }else if(currentElement.hasClass('ON')){
      currentElement.addClass('OFF');
      currentElement.removeClass('ON');
      $('#star_product').val("0");
    }
  });

  window.onload = () => $("#varONOFF").removeAttr("disabled");
  // window.onload = () => $("#star_productBTN").removeAttr("disabled");

  $('#formSubmit').on('submit', function(e){
    let rowCount = $('.rowElement').length;
    if(rowCount==0 || rowCount<1){
      e.preventDefault();
      alert("You Cannot remove all the table rows. Please add some fields to continue.");
    }
  });

</script>
@endsection