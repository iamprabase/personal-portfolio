<script>
  
  const clientvisitcolumns = [{ "data": "id" },
        { "data": "date" },
        { "data": "no_of_visits" },
        { "data": "view_detail" }];

  function initializePVDT(empID){
    partyvisittable = $('#partyvisit').DataTable({
      "stateSave": true,
      "stateSaveParams": function (settings, data) {
        data.search.search = "";
      },
      "order": [[ 1, "desc" ]],
      "serverSide": true,
      "processing": false,
      "paging": true,
      "searching": false,
      "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +"<'row'<'col-xs-12'tr>>" +"<'row'<'col-xs-4'li><'col-xs-8'p>>",
      "columnDefs": [
        {
          "orderable": false,
          "targets":-1,
        },
        { "width": "2%", "targets": 0 },
      ],
      "buttons": [
        {
          extend: 'pdfHtml5', 
          title: 'Visit List of {{$employee->name}}', 
          exportOptions: {
            columns: [0,1,2],
            stripNewlines: false,
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            clientVisitNewExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'excelHtml5', 
          title: 'Visit List of {{$employee->name}}', 
          exportOptions: {
            columns: [0,1,2],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            clientVisitNewExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'print', 
          title: 'Visit List of {{$employee->name}}', 
          exportOptions: {
            columns: [0,1,2],
          },
          footer: true,
          action: function ( e, dt, node, config ) {
            clientVisitNewExportAction( e, dt, node, config );
          }
        },
      ],
      "ajax":{
        "url": "{{ domain_route('company.admin.employee.empClientVisitTable') }}",
        "dataType": "json",
        "type": "POST",
        "data":{ 
          _token: "{{csrf_token()}}", 
          empID:empID,
        },
        beforeSend:function(){
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
      "columns": clientvisitcolumns,
    });
    partyvisittable.buttons().container()
        .appendTo('#partyvisitexports');
    var clientVisitOldExportAction = function (self, e, dt, button, config) {
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

    var clientVisitNewExportAction = function (e, dt, button, config) {
      var self = this;
      var oldStart = dt.settings()[0]._iDisplayStart;
      dt.one('preXhr', function (e, s, data) {
        $('#mainBox').addClass('box-loader');
        $('#loader1').removeAttr('hidden');
        data.start = 0;
        data.length = -1;
        dt.one('preDraw', function (e, settings) {
          if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
            customExportAction(config, settings);
          }else{
            clientVisitOldExportAction(self, e, dt, button, config);
          }
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

    function customExportAction(config, settings){
      let properties = ['date', 'no_of_visits' ];
      let columns = ['#',  'Date', 'Number of Visits' ];
      $('#party-visit-exportedData').val(JSON.stringify(settings.json));
      $('#party-visit-columns').val(JSON.stringify(columns));
      $('#party-visit-properties').val(JSON.stringify(properties));
      $('#party-visit-pageTitle').val(config.title);
      $('#pdf-generate-party-visit').submit();
    }
  }
  initializePVDT("{{$employee->id}}");
  
</script>