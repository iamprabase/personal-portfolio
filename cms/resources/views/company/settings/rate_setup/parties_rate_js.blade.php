<script>
  let table_rat;
  $('document').ready(()=>{

    initializeDT();
  });
  let columns = [
        { "data": "id" },
        { "data": "rate_name" },
        { "data": "action" },
  ];

  function initializeDT(){
 
    table_rat = $('#rates_table').DataTable({
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
            columns: [0,1],
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
            columns: [0,1],
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
            columns: [0,1],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.get_rates_data') }}",
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
    });
    table_rat.buttons().container().appendTo('#rates_export');
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

  $('#addRateModal').on('show.bs.modal', function(){
    $('#add_new_rate')[0].reset();
    $('.errlabel').html('');
  });

  $('#add_new_rate').submit(function(e){
    e.preventDefault();
    const formEl = $(this); 
    const url = formEl[0].action;
    const name = formEl.find('.rate_name').val(); 
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":{
        _token: "{{csrf_token()}}",
        rate_name: name
      },
      beforeSend: function(){
        $('#addRateBtn').prop('disabled', true);
      },
      success:function(response){
        if(response.status){
          formEl[0].reset();
          table_rat.ajax.reload();
          $('#addRateBtn').prop('disabled', false);
          $('#addRateModal').modal('hide');
          window.open(response.next_page_url, "_self");
        }else{
          alert(response.msg);
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