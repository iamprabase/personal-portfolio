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

    initializeDT();

    $('.rate-name-setup-form').removeClass('hidden');
  });
  let columns = [
        { "data": "id" },
        { "data": "product_name" },
        { "data": "variant_name" },
        { "data": "unit" },
        { "data": "original_mrp" },
        { "data": "custom_mrp", className: 'mrpCell' },
  ];

  function initializeDT(){
 
    table = $('#partiesratesettup').DataTable({
      "stateSave": false,
      language: { search: "" },
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
          title: 'Custom Rate Setup', 
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7],
            stripNewlines: false,
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'excelHtml5', 
          title: 'Custom Rate Setup', 
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'print', 
          title: 'Custom Rate Setup', 
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.add_new_rate.fetch_products_data') }}",
        "dataType": "json",
        "type": "GET",
        "data":{ 
          _token: "{{csrf_token()}}", 
          rate_id: {{$rate_id}}
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
    });
    table.buttons().container().appendTo('#partiesratesettupexports');
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
  @if(Auth::user()->can('party_wise_rate_setup-update') || $createCheck)
  $('#partiesratesettup').on('click', '.mrpCell', function(){
    $(this).find('.mrpInput').removeClass("hidden");
    $(this).find('.mrpInput').focus();
    $(this).find('.mrpText').addClass("hidden");
  });

  $('#partiesratesettup').on('focusout', '.mrpInput', function(){
    $(this).addClass("hidden");
    $(this).val($(this).data("value"));
    $(this).parent().find('.mrpText').removeClass("hidden");
  });

  // $("#partiesratesettup").on("keyup", ".mrpInput", function (e) {
  //   let currentVal = $(this).val();
  //   const floatvalRegexPattern = /^\d*(\.\d{2})?$/;
  //   const alphabetRegexPattern = /^[a-zA-Z]+$/;
  //   if(currentVal!=""){
  //     $(this).val(parseFloat(currentVal).toFixed(2));
  //     if(alphabetRegexPattern.test(currentVal)){
  //       $(this).val("");
  //     }
  //   }
  // });

  $('#partiesratesettup').on('keypress', '.mrpInput', function(event){
    const alphabetRegexPattern = /^[a-zA-Z]/;
    const floatvalRegexPattern = /^\d*\.?\d*$/;
    let mrp = $(this).val();
    let originalMrp = $(this).data('original_mrp');
    
    if(alphabetRegexPattern.test(event.key) && event.key!="Enter"){
      return false;
    }
    
    if(!floatvalRegexPattern.test(event.key) && event.key!="Enter"){
      return false;
    }

    if(event.keyCode == 13){
      let url = "{{domain_route('company.admin.rate_setup_page.update_mrp', [$rate_id])}}";
      let product_id = $(this).data('product_id');
      let variant_id = $(this).data('variant_id');

      if(parseFloat(originalMrp)==parseFloat(mrp)){
        return false;
      }

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
          rate_id: {{$rate_id}},
          mrp: mrp
        },
        beforeSend:function(url, data){
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
        },
        success:function(response){
          alert(response.msg);
          table.ajax.reload();
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

    }
  });

  $('.quick-custom-rate-button').click(function(){
    $('#quickRateSetupModal').modal();
  });

  $('.quick_rate_setup_input').on('keypress', function(event){
    const alphabetRegexPattern = /^[a-zA-Z]/;
    let rateVal = $(this).val();
    
    if(alphabetRegexPattern.test(event.key)){
      return false;
    }
    
  });

  $('.quick_rate_setup_input').on('focusout', function(event){
    let rateVal = $(this).val();
    if(rateVal!=""){
      const floatvalRegexPattern = /^\d*\.?\d*$/;
      if(rateVal>500){
        $(this).val("");
        alert("Please add value less than or equal to 500.")  
        return false;
      }
      if(!floatvalRegexPattern.test(rateVal)){
        $(this).val("");
        alert("Please add value greater than or equal to 0.")
        return false;
      }
      $(this).val(parseFloat(rateVal).toFixed(2));
    }
    
  });
  $('#quickRateSetupForm').submit(function(e){
    e.preventDefault();
    let el = $(this);
    let form = el[0];
    let discount_percent = el.find('.quick_rate_setup_input').val();
    let action = form.action;
    if(discount_percent>=0){
      $.ajax({
        "url": action,
        "dataType": "json",
        "type": "POST",
        "data":{
          discount_percent: discount_percent,
          'rate_id': "{{$rate_id}}"
        },
        beforeSend:function(url, data){
          $('#quickCustomRateBtn').attr('disabled', true);
          $('#quickCustomRateBtn').html('Please Wait...');
        },
        success:function(response){
          if(response.status){
            alert(response.msg);
            el.find('.quick_rate_setup_input').val("");
            el.find('.quick_rate_setup_err').html('');
            $('#quickCustomRateBtn').attr('disabled', false);
            $('#quickCustomRateBtn').html('Submit');
            table.ajax.reload();
            $('#quickRateSetupModal').modal('hide');
          }
        },
        error:function(xhr, textStatus){
          $('#quickCustomRateBtn').attr('disabled', false);
          $('#quickCustomRateBtn').html('Submit');
          el.find('.quick_rate_setup_err').html("");
          $.each(xhr.responseJSON.errors, function(key,value) {
            $('.quick_rate_setup_err').append('<p class="help-block has-error">'+value+'</p>');
          });
        },
        complete:function(){
          $('#rate-name-submit').attr('disabled', false);
          $('#rate-name-submit').html('Submit');
        }
      });
    }
  });
  @endif

  $('.tooltip').tooltip({
    placement: "right",
    trigger: "focus"
  });

  $('.fa-info-circle').tooltip({
    placement: "bottom"
  });

  $('.rate-name-setup-form').submit(function(e) {
    e.preventDefault();
    let url = $(this)[0].action;
    let formData = $(this).serializeArray();
    
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":formData,
      beforeSend:function(url, data){
        $('#rate-name-submit').attr('disabled', true);
        $('#rate-name-submit').html('Please Wait...');
      },
      success:function(response){
        if(response.status){
          alert(response.msg);
          $('.rate-name-setup-form').find('#rate_name').val(response.rate_name);
          $('#rateHeader').html(response.rate_name);
          $('#rate-name-setup-form').find('.err_div').each(function(){
            $(this).html("");
          });
        }
      },
      error:function(xhr, textStatus){
        $('#rate-name-submit').attr('disabled', false);
        $('#rate-name-submit').html('Update Name');
        $('#rate-name-setup-form').find('.err_div').each(function(){
          $(this).html("");
        });
        $.each(xhr.responseJSON.errors, function(key,value) {
          $('.'+key).append('<p class="help-block has-error">'+value+'</p>');
        });
      },
      complete:function(){
        $('#rate-name-submit').attr('disabled', false);
        $('#rate-name-submit').html('Submit');
      }
    });
  });
</script>