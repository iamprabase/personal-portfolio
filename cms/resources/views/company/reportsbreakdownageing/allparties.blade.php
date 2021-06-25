@extends('layouts.company')
@section('title', 'Ageing Payment')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<style>
    #devmap {
        width:100%;
        min-height:400px;
    }
        
    table tfoot {
        display: table-row-group;
    }
    
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-xs-8">
                <h3 class="box-title">Ageing Breakdown Payment Reports</h3>
            </div>
            <div class="col-xs-4">
              <span id="ageingreportexports" class="pull-right"></span>
            </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-4">
                    <div style="margin-top:10px;height: 40px;z-index: 999 " id="assignedTo">
                      <select id="partyFilter" class="select2">
                        <option value="null">Party</option>
                        @foreach($parties as $party)
                        <option value="{{$party->id}}">{{$party->company_name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-2"></div>
            
          </div>
          <div id="mainBox">
            <table id="ageingreport" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Party</th>
                  <th>Order No</th>
                  <th>Amount</th>
                  <th>Overdue Period (Days)</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
              </tfoot>
            </table>
            <div id="loader1" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <div class="row">
              <div class="col-xs-12">
                <b>Note:</b></br>
                *We have considered first order payment comes first rule.</br> 
                *Opening balance is not considered.
              </div>
            </div>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>

<form method="post" action="{{domain_route('company.admin.ageing.custombreakdownpdfexports')}}" class="pdf-export-form"
  id="pdf-generate" style="display:none;">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="reportName" class="reportName" id="reportName">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>
@endsection

@section('scripts')
  <script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function () {
      $('.select2').select2();
      $('[data-toggle="tooltip"]').tooltip();

    function initializeDT(party)
    {
    	const table = $('#ageingreport').DataTable({
    		"processing": true,
    		"serverSide": true,
        "order": [[ 1, "asc" ],[2,"asc"]],
        "paging":true,
        "paging": true,
        "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
              "<'row'<'col-xs-6'><'col-xs-6'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
        "columnDefs": [{ "orderable":false, "targets":[3,4] }],
        "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Ageing Breakdown Payment Reports', 
              
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Ageing Breakdown Payment Reports', 
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Ageing Breakdown Payment Reports', 
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
          ],
    		"ajax":{
    			"url": "{{ domain_route('company.admin.ageing.ajaxBreakdownDatatable') }}",
    			"dataType": "json",
    			"type": "POST",
    			"data":{ 
    				_token: "{{csrf_token()}}",
            party:party,
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
    			},
    		},  
        "columns": [
          {"data" : "id"},
          {"data" : "party"},
          {"data" : "order_no"},
          {"data" : "amount"},
          {"data" : "overdueDays"},
        ],
         "footerCallback": function ( row, data, start, end, display ) {
               var api = this.api(), data;

               // Remove the formatting to get integer data for summation
               var intVal = function ( i ) {
                   return typeof i === 'string' ?
                       i.replace(/[\$,]/g, '')*1 :
                       typeof i === 'number' ?
                           i : 0;
               };

               // Total over all pages
               total = api
                   .column( 3 )
                   .data()
                   .reduce( function (a, b) {
                       return intVal(a) + intVal(b);
                   }, 0 );

               // Total over this page
              page3Total = api
                 .column( 3, { page: 'current'} )
                 .data()
                 .reduce( function (a, b) {
                    if(a=='-') { a = 0; }
                    if(b=='-') { b = 0; }
                     return intVal(a) + intVal(b);
                 }, 0 );

               // Update footer
               $( api.column( 3 ).footer() ).html(
                   '{{config('settings.currency_symbol')}} '+page3Total.toLocaleString()
               );
           },
    		});
        table.buttons().container().appendTo('#ageingreportexports');

        var oldExportAction = function (self, e, dt, button, config) {
          if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
              table.draw();
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
            table.draw();
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
            data.length = {{$ordersCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                // $.each(settings.json.data, function(key, htmlContent){
                //   settings.json.data[key].id = key+1;
                //   if($(htmlContent.completion_datetime).find('input').first().is(":checked")){
                //     settings.json.data[key].completion_datetime = "Yes";
                //   }else{
                //     settings.json.data[key].completion_datetime = "No";  
                //   }
                //   settings.json.data[key].PartyName = $(settings.json.data[key].PartyName)[0].textContent;
                //   settings.json.data[key].AssignedByName = $(settings.json.data[key].AssignedByName)[0].textContent;
                //   settings.json.data[key].AssignedToName = $(settings.json.data[key].AssignedToName)[0].textContent;
                // });
                customExportAction(config, settings);
              }else{
                oldExportAction(self, e, dt, button, config);
              }
              // oldExportAction(self, e, dt, button, config);
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
    }
    function customExportAction(config, settings){
      $('#exportedData').val(JSON.stringify(settings.json));
      $('#pageTitle').val(config.title);
      $('#pdf-generate').submit();
    }
    initializeDT(null);

    $('#partyFilter').on('change',function(){
      party = ($('#partyFilter').val()!='null')?$('#partyFilter').val():null;
      $('#ageingreport').DataTable().destroy();
      initializeDT(party);
    });

});
  </script>
@endsection