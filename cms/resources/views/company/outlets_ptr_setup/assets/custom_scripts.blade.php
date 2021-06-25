<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables-buttons/buttons.flash.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script>
  let table;
  let editor;
  $('document').ready(()=>{
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });

    initializeDT();

    $('.settings-update-form').removeClass('hidden');

    $('.showin_app').removeClass('hidden');
  });

  $('.tooltip').tooltip({
    placement: "right",
    trigger: "focus"
  });

  $('.fa-info-circle').tooltip({
    placement: "bottom"
  });
  let hidden = " hidden";
  let exportColumns = [0,1,2,3,4,5,7];
  @if(config('settings.product_level_discount')==1)
    hidden = "";
    exportColumns = [0,1,2,3,4,5,6,7]
  @endif
    let columns = [
      { "data": "id" },
      { "data": "product_name" },
      { "data": "variant_name" },
      { "data": "unit" },
      { "data": "mrp" },
      // { "data": "ptr" },
      { "data": "moq", className: 'moqCell' },
      { "data": "discount", className: 'discountCell'+hidden },
      { "data": "show_in_app" },
      // { "data": "action" },
    ];

  function initializeDT(){
 
    table = $('#ptrseup').DataTable({
      "stateSave": false,
      // language: { search: "" },
      "order": [[ 1, "asc" ]],
      "serverSide": true,
      "processing": true,
      "paging": true,
      "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" + "<'row'<'col-xs-6'><'col-xs-6'>>" + "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
      "columnDefs": [
        {
          "orderable": false,
          "targets":[-1, -2],
        },
      ],
      "buttons": [
        {
          extend: 'pdfHtml5', 
          title: 'Order Setup', 
          exportOptions: {
            columns: exportColumns,
            stripNewlines: false,
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'excelHtml5', 
          title: 'Order Setup', 
          exportOptions: {
            columns: exportColumns,
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'print', 
          title: 'Order Setup', 
          exportOptions: {
            columns: exportColumns,
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.outlets.ptr.fechData') }}",
        "dataType": "json",
        "type": "GET",
        "data":{ 
          _token: "{{csrf_token()}}", 
        },
        beforeSend:function(url, data){
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
        },
        error:function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        },
        complete:function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        }
      },
      "columns": columns,
      drawCallback:function(settings)
      {
        
      },
    });
    table.buttons().container().appendTo('#ptrexports');
    var oldExportAction = function (self, e, dt, button, config) {
      if (button[0].className.indexOf('buttons-excel') >= 0) {
        if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
        } else {
            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
        }
      } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
        if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
        } else {
            $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
        }
      } else if (button[0].className.indexOf('buttons-print') >= 0) {
        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
      }
    };

    var newExportAction = function (e, dt, button, config) {
      var self = this;
      var oldStart = dt.settings()[0]._iDisplayStart;
      dt.one('preXhr', function (e, s, data) {
        $('#mainBox').addClass('box-loader');
        $('#loader1').removeAttr('hidden');
        data.start = 0;
        data.length = -1;
        dt.one('preDraw', function (e, settings) {
          // if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
          //   customExportAction(config, settings);
          // }else{
          //   oldExportAction(self, e, dt, button, config);
          // }
          oldExportAction(self, e, dt, button, config);
          dt.one('preXhr', function (e, s, data) {
              settings._iDisplayStart = oldStart;
              data.start = oldStart;
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
          });
          setTimeout(dt.ajax.reload, 0);
          return false;
        });
      });
      dt.ajax.reload();
    }
  }; // Data Table initialize

  $(document).on("change",".show_in_app_check_box" , function() {
    let value;
    let ischecked= $(this).is(':checked');
    if(!ischecked) value = 0;
    else value = 1;
    let product_id = $(this).val();
    let variant_id = $(this).data('variant_id');
    let current = $(".show_all_in_app_check_box");
    let url = "{{domain_route('company.admin.outlets.ptr.changeAppVisibility')}}";
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":{ 
        _token: "{{csrf_token()}}",
        visibility: value,
        product_id: product_id,
        variant_id: variant_id,
      },
      beforeSend:function(url, data){
        $('#mainBox').addClass('box-loader');
        $('#loader1').removeAttr('hidden');
      },
      success:function(response){
        alert(response.message);
        if(response.show_all==0){ 
          $('.show_all_in_app_check_box').prop("checked", true);
          current.removeData('check-uncheck');
          current.attr('data-check-uncheck', 1);
        }
        else{ 
          $('.show_all_in_app_check_box').prop("checked", false);
          current.removeData('check-uncheck');
          current.attr('data-check-uncheck', 0);
        }
        // table.ajax.reload();
      },
      error:function(xhr, textStatus){
        $('#mainBox').removeClass('box-loader');
        $('#loader1').attr('hidden', 'hidden');
      },
      complete:function(){
        $('#mainBox').removeClass('box-loader');
        $('#loader1').attr('hidden', 'hidden');
      }
    });
  }); 

  $(document).on("click",".show_all_in_app_check_box" , function() {
    let current = $(this);
    let value= $(this).data('check-uncheck');
    let changedAttr = value==0?1:0;
    
    let url = "{{domain_route('company.admin.outlets.ptr.changeAppVisibility')}}";
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":{ 
        _token: "{{csrf_token()}}",
        visibility: changedAttr,
        show_hide_all: true
      },
      beforeSend:function(url, data){
        $('#mainBox').addClass('box-loader');
        $('#loader1').removeAttr('hidden');
      },
      success:function(response){
        alert(response.message);
        current.removeData('check-uncheck');
        current.attr('data-check-uncheck', changedAttr);
        table.ajax.reload(null, false);
      },
      error:function(xhr, textStatus){
        $('#mainBox').removeClass('box-loader');
        $('#loader1').attr('hidden', 'hidden');
      },
      complete:function(){
        $('#mainBox').removeClass('box-loader');
        $('#loader1').attr('hidden', 'hidden');
      }
    });
  }); 

  $('#ptrseup').on('click', '.moqCell', function(){
    $(this).find('.moqInput').removeClass("hidden");
    $(this).find('.moqInput').focus();
    $(this).find('.moqtext').addClass("hidden");
  });

  $('#ptrseup').on('click', '.discountCell', function(){
    $(this).find('.discountInput').removeClass("hidden");
    $(this).find('.discountInput').focus();
    $(this).find('.discountText').addClass("hidden");
  });

  $('#ptrseup').on('focusout', '.moqInput', function(){
    $(this).addClass("hidden");
    $(this).val($(this).data("value"));
    $(this).parent().find('.moqtext').removeClass("hidden");
  });

  $('#ptrseup').on('focusout', '.discountInput', function(){
    $(this).addClass("hidden");
    $(this).val($(this).data("value"));
    $(this).parent().find('.discountText').removeClass("hidden");
  });

  // $("#ptrseup").on("keyup", ".discountInput", function (e) {
  //   let currentVal = $(this).val();
  //   let mrp = $(this).data('mrp');
  //   let originalVal = $(this).data('value');
    // const floatvalRegexPattern = /^\d*(\.\d{2})?$/;
    // const alphabetRegexPattern = /^[a-zA-Z]+$/;
    // if(currentVal!=""){
    //   $(this).val(parseFloat(currentVal).toFixed(2));
      // if(alphabetRegexPattern.test(currentVal)){
      //   $(this).val("");
      // }
    // }
  //   if(parseFloat(currentVal)>parseFloat(mrp)){
  //     $(this).val(originalVal);
  //     alert("Discount cannot be greater than Applied Rate.");
  //   }
  // });

  $('#ptrseup').on('keypress', '.moqInput', function(event){
    if(event.keyCode == 13){
      let url = "{{domain_route('company.admin.outlets.ptr.productUpdate')}}";
      let product_id = $(this).data('product_id');
      let variant_id = $(this).data('variant_id');
      let moq = $(this).val();
      let discount = $(this).data('discount');
      let visibility = $(this).data('visibility');

      if(parseInt(moq)<=0){
        return false;
      }

      updateMoqDiscount(moq, discount, product_id, variant_id, visibility);
    }
  });

  $('#ptrseup').on('keypress', '.discountInput', function(event){
    const alphabetRegexPattern = /^[a-zA-Z]/;
    const floatvalRegexPattern = /^\d*\.?\d*$/;
    let discount = $(this).val();
    let originalVal = $(this).data('value');

    if(alphabetRegexPattern.test(event.key) && event.key!="Enter"){
      return false;
    }

    if(!floatvalRegexPattern.test(event.key) && event.key!="Enter"){
      return false;
    }

    if(discount!=""){
      if(!floatvalRegexPattern.test(parseFloat(discount).toFixed(2))){
        $(this).val(originalVal);
        alert("Discount must be in numbers and cannot be greater than 100.");
        return false;
      }
    }

    if(parseFloat(discount)>100){
      $(this).val(originalVal);
      alert("Discount cannot be greater than 100%.");
      return false;
    }

    if(event.keyCode == 13){
      let product_id = $(this).data('product_id');
      let variant_id = $(this).data('variant_id');
      let moq = $(this).data('moq');
      let visibility = $(this).data('visibility');
      discount = parseFloat(discount).toFixed(2);

      let mrp = $(this).data('mrp');

      updateMoqDiscount(moq, discount, product_id, variant_id, visibility);
    }
  });

  function updateMoqDiscount(moq, discount, product_id, variant_id, visibility){
    let url = "{{domain_route('company.admin.outlets.ptr.productUpdate')}}";
    $.ajax({
      "stateSave": true,
      "stateSaveParams": function (settings, data) {
      data.search.search = "";
      },
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":{ 
        _token: "{{csrf_token()}}",
        _method: "PATCH",
        product_id: product_id,
        variant_id: variant_id,
        moq: moq,
        discount: discount,
        visibility: visibility
      },
      beforeSend:function(url, data){
        // $('#update-product-details').attr('disabled', true);
        // $('#update-product-details').html('Loading...');
        $('#mainBox').addClass('box-loader');
        $('#loader1').removeAttr('hidden');
      },
      success:function(response){
        if(response.status==200){
          // $('#productUpdateModal').modal('hide');
          // $(this).trigger('focusout');
          alert(response.message);
          table.ajax.reload(null, false);
          // console.log($(this).val());
        }else{
          $('.'+response.append_to).append('<p class="help-block has-error">'+response.message+'</p>');
        }
      },
      error:function(xhr, textStatus){
        // $('#update-product-details').attr('disabled', false);
        // $('#update-product-details').html('Submit');
        // $('.err_div').each(function(){
        //   $(this).html("");
        // });
        // $.each(xhr.responseJSON.errors, function(key,value) {
        //   $('.'+key).append('<p class="help-block has-error">'+value+'</p>');
        // });
        $('#mainBox').removeClass('box-loader');
        $('#loader1').attr('hidden', 'hidden');
      },
      complete:function(){
        // $('#update-product-details').attr('disabled', false);
        // $('#update-product-details').html('Submit');
        $('#mainBox').removeClass('box-loader');
        $('#loader1').attr('hidden', 'hidden');
      }
    });
  }

  $('#settings-update-form').submit(function(e) {
    e.preventDefault();
    let url = $(this)[0].action;
    let formData = $(this).serializeArray();
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":formData,
      beforeSend:function(url, data){
        $('#settings-product-details').attr('disabled', true);
        $('#settings-product-details').html('Loading...');
      },
      success:function(response){
        if(response.status==200){
          alert(response.message);
          $('#settings-update-form').find('#min_order_value').val(response.min_order_value);
          $('input:radio[name="order_with_amt_qty"]').filter('[value="'+response.order_with_amt_qty+'"]').iCheck('check');
          $('input').iCheck('update');
        }
      },
      error:function(xhr, textStatus){
        $('#settings-product-details').attr('disabled', false);
        $('#settings-product-details').html('Submit');
        $('#settings-update-form').find('.err_div').each(function(){
          $(this).html("");
        });
        $.each(xhr.responseJSON.errors, function(key,value) {
          $('.'+key).append('<p class="help-block has-error">'+value+'</p>');
        });
      },
      complete:function(){
        $('#settings-product-details').attr('disabled', false);
        $('#settings-product-details').html('Submit');
      }
    });
  });

  $("#settings-update-form").on("focusout", "#min_order_value", function (e) {
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
  });

  // $(document).on("click", "#product-edit", function(){
  //   $('#productUpdateModal').modal('show');
  //   let discount = $(this).data('discount');
  //   if(!discount) discount = "0.00";
  //   let moq = $(this).data('moq');
  //   if(!moq) moq = 1;
  //   let productId = $(this).data('product_id');
  //   let variantId = $(this).data('variant_id');
  //   let visibility = $(this).data('visibility');

  //   $('#product-update-form')[0].reset();
  //   $('#product-update-form').find('#discount').val(discount);
  //   $('#product-update-form').find('#moq').val(moq);
  //   $('#product-update-form').find('#product_id').val(productId);
  //   $('#product-update-form').find('#variant_id').val(variantId);
  //   $('#product-update-form').find('#visibility').val(visibility);
  // });
  
  // $('#productUpdateModal').on('hidden.bs.modal', function (e) {
  //   $('#product-update-form')[0].reset();
  //   $('.err_div').each(function(){
  //     $(this).html("");
  //   });
  // });

  // $('#product-update-form').submit(function(e) {
  //   e.preventDefault();
  //   let url = $(this)[0].action;
  //   let product_id = $(this).find('#product_id').val();
  //   let variant_id = $(this).find('#variant_id').val();
  //   let moq = $(this).find('#moq').val();
  //   let discount = $(this).find('#discount').val();
  //   let visibility = $(this).find('#visibility').val();

  //   $.ajax({
  //     "url": url,
  //     "dataType": "json",
  //     "type": "POST",
  //     "data":{ 
  //       _token: "{{csrf_token()}}",
  //       _method: "PATCH",
  //       product_id: product_id,
  //       variant_id: variant_id,
  //       moq: moq,
  //       discount: discount,
  //       visibility: visibility
  //     },
  //     beforeSend:function(url, data){
  //       $('#update-product-details').attr('disabled', true);
  //       $('#update-product-details').html('Loading...');
  //     },
  //     success:function(response){
  //       if(response.status==200){
  //         $('#productUpdateModal').modal('hide');
  //         alert(response.message);
  //         table.ajax.reload();
  //       }else{
  //         $('.'+response.append_to).append('<p class="help-block has-error">'+response.message+'</p>');
  //       }
  //     },
  //     error:function(xhr, textStatus){
  //       $('#update-product-details').attr('disabled', false);
  //       $('#update-product-details').html('Submit');
  //       $('.err_div').each(function(){
  //         $(this).html("");
  //       });
  //       $.each(xhr.responseJSON.errors, function(key,value) {
  //         $('.'+key).append('<p class="help-block has-error">'+value+'</p>');
  //       });
  //     },
  //     complete:function(){
  //       $('#update-product-details').attr('disabled', false);
  //       $('#update-product-details').html('Submit');
  //     }
  //   });
  // });

  // $("#productUpdateModal").on("focusout", "#discount", function (e) {
  //   let currentVal = $(this).val();
  //   const floatvalRegexPattern = /^\d*(\.\d{2})?$/;
  //   const alphabetRegexPattern = /^[a-zA-Z]+$/;
  //   if(currentVal!=""){
  //     // if(floatvalRegexPattern.test(parseFloat(currentVal))){
  //       $(this).val(parseFloat(currentVal).toFixed(2));
  //     // }else{
  //       if(alphabetRegexPattern.test(currentVal)){
  //         $(this).val("");
  //       }
  //     // }
  //   }
  // });
</script>