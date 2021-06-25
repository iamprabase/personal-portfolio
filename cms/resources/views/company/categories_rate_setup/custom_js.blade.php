<script>

  // $('#delete').on('show.bs.modal', function (event) {
  //   var button = $(event.relatedTarget);
  //   var mid = button.data('mid');
  //   var modal = $(this)
  //   $(modal).find('.modal-body #rate_title').text('').text(button.data('title'));
  //   modal.find('.modal-body #delete_id').val(mid);
  // });

  // let table_rat;
  // $('document').ready(()=>{

  //   initializeDT();
  // });
  // let columns = [
  //       { "data": "id" },
  //       { "data": "rate_name" },
  //       { "data": "action" },
  // ];

  // function initializeDT(){
 
  //   table_rat = $('#rates_table').DataTable({
  //     "stateSave": false,
  //     "order": [[ 1, "asc" ]],
  //     "serverSide": false,
  //     "paging": true,
  //     "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" + "<'row'<'col-xs-6'><'col-xs-6'>>" + "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
  //     "columnDefs": [
  //       {
  //         "orderable": false,
  //         "targets":[-1, -2],
  //       },
  //     ],
  //     "buttons": [
  //       {
  //         extend: 'pdfHtml5', 
  //         title: 'Custom Rate Setup', 
  //         exportOptions: {
  //           columns: [0,1],
  //           stripNewlines: false,
  //         },
  //         footer: true,
  //         action: function ( e, dt, node, config ) {
  //           newExportAction( e, dt, node, config );
  //         }
  //       },
  //       {
  //         extend: 'excelHtml5', 
  //         title: 'Custom Rate Setup', 
  //         exportOptions: {
  //           columns: [0,1],
  //         },
  //         footer: true,
  //         action: function ( e, dt, node, config ) {
  //           newExportAction( e, dt, node, config );
  //         }
  //       },
  //       {
  //         extend: 'print', 
  //         title: 'Custom Rate Setup', 
  //         exportOptions: {
  //           columns: [0,1],
  //         },
  //         footer: true,
  //         action: function ( e, dt, node, config ) {
  //           newExportAction( e, dt, node, config );
  //         }
  //       },
  //     ],
  //     "columns": columns,
  //   });
  //   table_rat.buttons().container().appendTo('#rates_export');
  //   var oldExportAction = function (self, e, dt, button, config) {
  //     if (button[0].className.indexOf('buttons-excel') >= 0) {
  //       if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
  //           $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
  //       } else {
  //           $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
  //       }
  //     } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
  //       if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
  //           $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
  //       } else {
  //           $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
  //       }
  //     } else if (button[0].className.indexOf('buttons-print') >= 0) {
  //       $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
  //     }
  //   };

  //   var newExportAction = function (e, dt, button, config) {
  //     var self = this;
  //     var oldStart = dt.settings()[0]._iDisplayStart;
  //     dt.one('preXhr', function (e, s, data) {
  //       $('#mainBox').addClass('box-loader');
  //       $('#loader1').removeAttr('hidden');
  //       data.start = 0;
  //       data.length = -1;
  //       dt.one('preDraw', function (e, settings) {
  //         // if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
  //         //   customExportAction(config, settings);
  //         // }else{
  //         //   oldExportAction(self, e, dt, button, config);
  //         // }
  //         oldExportAction(self, e, dt, button, config);
  //         dt.one('preXhr', function (e, s, data) {
  //             settings._iDisplayStart = oldStart;
  //             data.start = oldStart;
  //             $('#mainBox').removeClass('box-loader');
  //             $('#loader1').attr('hidden', 'hidden');
  //         });
  //         setTimeout(dt.ajax.reload, 0);
  //         return false;
  //       });
  //     });
  //     dt.ajax.reload();
  //   }
  // }; // Data Table initialize

  $('#addRateModal').on('show.bs.modal', function(e){
    $('#add_new_rate')[0].reset();
    let categoryId = e.relatedTarget.dataset.categoryId

    $('#add_new_rate').find('input[name=category_id]').val(categoryId)
    $('.errlabel').html('');
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

  

  $('#add_new_rate').submit(function(e){
    e.preventDefault();
    const formEl = $(this); 
    const url = formEl[0].action;
    const name = formEl.find('input[name=rate_name]').val();
    const category_id = formEl.find('input[name="category_id"]').val();
    const discount_percent = formEl.find('input[name="discount_percent"]').val();
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":{
        _token: "{{csrf_token()}}",
        name,
        category_id,
        discount_percent
      },
      beforeSend: function(){
        $('#addRateBtn').prop('disabled', true);
      },
      success:function(response){
        alert(response.msg);
        if(response.status){
          formEl[0].reset();
          $('#addRateBtn').prop('disabled', false);
          $('#addRateModal').modal('hide');
        }else{
          formEl[0].reset();
          $('#addRateBtn').prop('disabled', false);
          $('#addRateModal').modal('hide');
        }
      },
      error:function(errs){
        $.each(errs.responseJSON.errors, function(key,value) {
          formEl.find('.'+key+'_err').append('<p class="help-block has-error">'+value+'</p>');
        });
        $('#addRateBtn').prop('disabled', false);
      },
      complete:function(response){
        $('#addRateBtn').prop('disabled', false);
      }

    });
  });

</script>