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
<script>
  let table;
  $('document').ready(()=>{
    $('.select2').select2({
      "placeholder": "Select Clients"
    });
    initializeDT();
  });
  $("#create-connection-btn").click(()=>{
    $('#connectionModal').modal('show');
  });

  $('#connectionModal').on('hidden.bs.modal', function (e) {
    $('#outlet-connection-form')[0].reset();
    $('#outlet-connection-form').find('#clients').val("").trigger('change');
    $('.err_div').each(function(){
      $(this).html("");
    });
  })

  $('#outlet-connection-form').submit(function(e) {
    e.preventDefault();
    let url = $(this)[0].action;
    let client_id = $(this).find('#clients option:selected').val();
    let secret_code = $(this).find('#secret_code').val();
    $.ajax({
      "url": url,
      "dataType": "json",
      "type": "POST",
      "data":{ 
        _token: "{{csrf_token()}}",
        client_id: client_id,
        secret_code: secret_code 
      },
      beforeSend:function(url, data){
        $('#connect-outlet').attr('disabled', true);
        $('#connect-outlet').html('Loading...');
      },
      success:function(response){
        if(response.status==200){
          $('#connectionModal').modal('hide');
          alert(response.message);
          table.ajax.reload();
        }else{
          $('.'+response.append_to).append('<p class="help-block has-error">'+response.message+'</p>');
        }
      },
      error:function(xhr, textStatus){
        $('#connect-outlet').attr('disabled', false);
        $('#connect-outlet').html('Submit');
        $('.err_div').each(function(){
          $(this).html("");
        });
        $.each(xhr.responseJSON.errors, function(key,value) {
          $('.'+key).append('<p class="help-block has-error">'+value+'</p>');
        });
      },
      complete:function(){
        $('#connect-outlet').attr('disabled', false);
        $('#connect-outlet').html('Submit');
      }
    });
  });

  // $(document).on("click", "#connection-edit", function(){
  //   $('#updateConnectionModal').modal('show');
  //   $('#update-outlet-connection-form')[0].reset();
  //   $('#update_client_id').val($(this).data('selclient'));
  //   $('#update-outlet-connection-form').find('#clients').val($(this).data('selclient')).trigger('change');
  //   $('#update-outlet-connection-form').find('#secret_code').val($(this).data('code'));
  // });
  // $('#updateConnectionModal').on('shown.bs.modal', function (e) {
  //   $('#update-outlet-connection-form').find('#secret_code').prop('readonly','readonly');
  //   $('.err_div').each(function(){
  //     $(this).html("");
  //   });
  // });

  // $('#outlet-connection-form').submit(function(e) {
  //   e.preventDefault();
  //   let url = $(this)[0].action;
  //   let client_id = $(this).find('#clients option:selected').val();
  //   let secret_code = $(this).find('#secret_code').val();
  //   $.ajax({
  //     "url": url,
  //     "dataType": "json",
  //     "type": "POST",
  //     "data":{ 
  //       _token: "{{csrf_token()}}",
  //       client_id: client_id,
  //       secret_code: secret_code 
  //     },
  //     beforeSend:function(url, data){
  //       $('#connect-outlet').attr('disabled', true);
  //       $('#connect-outlet').html('Loading...');
  //     },
  //     success:function(response){
  //       if(response.status==200){
  //         $('#connectionModal').modal('hide');
  //         alert(response.message);
  //         table.ajax.reload();
  //       }else{
  //         $('.'+response.append_to).append('<p class="help-block has-error">'+response.message+'</p>');
  //       }
  //     },
  //     error:function(xhr, textStatus){
  //       $('#connect-outlet').attr('disabled', false);
  //       $('#connect-outlet').html('Submit');
  //       $('.err_div').each(function(){
  //         $(this).html("");
  //       });
  //       $.each(xhr.responseJSON.errors, function(key,value) {
  //         $('.'+key).append('<p class="help-block has-error">'+value+'</p>');
  //       });
  //     },
  //     complete:function(){
  //       $('#connect-outlet').attr('disabled', false);
  //       $('#connect-outlet').html('Submit');
  //     }
  //   });
  // });

  // $(document).on("click", "#connection-delete-btn", function(){
  //   $('#deleteModal').modal('show');
  //   $('#remove-connection-modal')[0].reset();
  //   $('#del_client_id').val($(this).data('selclient'));
  //   let url = button.data('url');
  //   $("#remove-connection-modal").attr("action", url);
  // });

  $('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var selclient = button.data('selclient');
    var url = button.data('url');
    $(".remove-connection-modal").attr("action", url);
    var modal = $(this)
    modal.find('.modal-body #del_client_id').val(selclient);
  });
  
  let columns = [
        { "data": "id" },
        { "data": "outlet_name" },
        // { "data": "secret_code" },
        { "data": "partyname" },
        { "data": "action" }
  ];

  function initializeDT(){
    table = $('#outlets').DataTable({
      "stateSave": false,
      // language: { search: "" },
      "order": [[ 0, "desc" ]],
      "serverSide": true,
      "processing": true,
      "paging": true,
      "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" + "<'row'<'col-xs-6'><'col-xs-6'>>" + "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
      "columnDefs": [
        {
          "orderable": false,
          "targets":-1,
        },
      ],
      "buttons": [
        {
          extend: 'pdfHtml5', 
          title: 'Outlets List', 
          exportOptions: {
            columns: [0,1,2],
            stripNewlines: false,
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'excelHtml5', 
          title: 'Outlets List', 
          exportOptions: {
            columns: [0,1,2],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'print', 
          title: 'Outlets List', 
          exportOptions: {
            columns: [0,1,2],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.outlets.fechData') }}",
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
        
      }
    });
    table.buttons().container().appendTo('#outletsexports');
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
</script>