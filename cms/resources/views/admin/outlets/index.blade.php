@extends('layouts.app')
@section('title', 'Outlets')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
  #loader1{
    position: absolute;
    margin: auto 30%;
  }
</style>
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        @if (\Session::has('success'))
          <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
          </div><br/>
        @endif
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Outlets List</h3>
            {{-- <a href="{{ route('app.outlets.create') }}" class="btn btn-primary pull-right" style="margin-right: 5px;">
              <i class="fa fa-plus"></i> Create New
            </a> --}}
            <span id="outletsexports" class="pull-right"></span>
          </div>
          <!-- /.box-header -->
          <div class="box-body table-responsive" id="mainBox">
            <div id="loader1">
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
            <table id="outlet" class="table table-bordered table-striped">
              <thead>
              {{-- @if( !$outlets->isEmpty() ) --}}
                <tr>
                  <th>S.No.</th>
                  <th>Outlet Name</th>

                  <th>Contact Person</th>                  
                  <th>Phone</th>                  
                  <th>Email</th>                  
                  <th>Status</th>                  
                  <th>Number of Suppliers</th>                  
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              {{-- @php($i = 0)
              @foreach($outlets as $outlet)
                @php($i++)
                <tr>
                  <td>{{ $i }}</td>
                  <td>{{ $outlet->outlet_name}}</td>
                  <td>{{ $outlet->contact_person}}</td>
                  <td>{{ $outlet->phone}}</td>
                  <td>{{ $outlet->email}}</td>                  
                  <td>
                    <a href='#' class='update-status-modal' data-value='{{$outlet->status}}' data-outlet_id='{{$outlet->id}}' data-status-type='status' data-action='{{domain_route('app.oulets.updatestatus', [$outlet->id])}}'>
                    @if($outlet->status=="Incomplete")
                      <span class="label label-warning">{{$outlet->status}}</span>
                    @elseif($outlet->status=="Disabled")
                      <span class="label label-danger">{{$outlet->status}}</span>
                    @elseif($outlet->status=="New" || $outlet->status=="Active")
                      <span class="label label-success">{{$outlet->status}}</span>
                    @endif</a>
                  </td>
                  <td>
                    {{$outlet->suppliers->count()}}
                  </td>
                  <td>

                    <a href="{{ route('app.outlets.show',$outlet->id) }}" class="btn btn-success btn-sm"
                       style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a>

                    <a href="{{ route('app.outlets.edit',$outlet->id) }}" class="btn btn-warning btn-sm"
                       style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-danger btn-sm" data-mid="{{ $outlet->id }}" data-url="{!! URL::route('app.outlets.delete', $outlet->id) !!}" data-toggle="modal" data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>
                  </td>
                </tr>
              @endforeach --}}
              </tbody>
            {{-- @endif --}}
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>

  <!-- Modal -->
  <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
        </div>

        <form method="post" class="remove-record-model">
          {{method_field('delete')}}
          {{csrf_field()}}
          <div class="modal-body">
            <p class="text-center">
              Are you sure you want to delete this?
            </p>
            <input type="hidden" name="outlet_id" id="outlet_id" value="">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button>
            <button type="submit" class="btn btn-warning">Yes, Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal modal-default fade" id="updateStatuses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">  
    <div class="modal-dialog" role="document">  
      <div class="modal-content">  
        <div class="modal-header">  
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>  
          <h4 class="modal-title text-center" id="myModalLabel">Update <span id="updateLabel"></span> Status</h4>  
        </div>  
        <form method="post" class="update-status-model">
            {{method_field('patch')}}  
          {{csrf_field()}}
          <input type="hidden" name="field_type" id="field_type">
          <div class="modal-body">  
            <div class="form-group" id="status-option">  
            </div>    
          </div>  
          <div class="modal-footer">  
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>  
            <button type="submit" class="btn btn-warning">Submit</button>  
          </div>  
        </form>  
      </div>  
    </div>  
  </div>
@endsection

@section('scripts')

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

  <script>

    $(function () {
      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);

        var modal = $(this)

        modal.find('.modal-body #outlet_id').val(mid);
      });
    });

    const columns = [ { "data": "id" },
                      { "data": "outlet_name" },
                      { "data": "contact_person" },
                      { "data": "phone" },
                      { "data": "email" },
                      { "data": "status" },
                      { "data": "num_of_suppliers" },
                      { "data": "action" }
                    ];
    $(document).ready(function () {
      // var table = $('#outlet').DataTable({
      //   buttons: ['excel', 'pdf', 'print']
      // });

      // table.buttons().container()
      //     .appendTo('#outletsexports');
      initializeDT();
    });

    function initializeDT(filterDays=null, account_status=null, customer_status=null){
        table = $('#outlet').DataTable({
          "stateSave": false,
          // language: { search: "" },
          "order": [[0, "desc" ]],
          "serverSide": true,
          "processing": true,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +"<'row'<'col-xs-6'><'col-xs-6'>>" +"<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
          "columnDefs": [
            {
              "orderable": false,
              "targets":[-1, -2],
            },],
          "buttons": [
            {
              extend: 'pdfHtml5',
              orientation: 'landscape', 
              paperSize: 'A4',
              title: 'Outlets List', 
              exportOptions: {
                columns: [0,1,2,3,4,5,6],
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
                columns: [0,1,2,3,4,5,6],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              orientation: 'landscape',
              paperSize: 'A4',
              title: 'Outlets List', 
              exportOptions: {
                columns: [0,1,2,3,4,5,6],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('app.outlets.fetchData') }}",
            "dataType": "json",
            "type": "GET",
            "data":{ 
              _token: "{{csrf_token()}}", 
              // filterDays: filterDays,
              // account_status: account_status,
              // customer_status: customer_status
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
        table.buttons().container()
            .appendTo('#outletsexports');
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
              //   // customExportAction(dt, data, config, settings);
              //   $.each(settings.json.data, function(key, htmlContent){
              //     settings.json.data[key].id = key+1;
              //     settings.json.data[key].partyname = $(settings.json.data[key].partyname)[0].textContent;
              //     settings.json.data[key].createdby = $(settings.json.data[key].createdby)[0].textContent;
              //     settings.json.data[key].orderstatus = $(settings.json.data[key].orderstatus)[0].textContent; 
              //   });
              //   customExportAction(config, settings);
              // }else{
                oldExportAction(self, e, dt, button, config);
              // }
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

    $(document).on("click", '.update-status-modal', function()  {

      var selectOption;
      var url = $(this).data('action');
      var field_type = $(this).data('status-type');
      var currentValue = $(this).data('value');
      if(currentValue=="New" ||currentValue=="Incomplete" ) currentValue = "Active"; 
      $('#updateStatuses').modal('show');
      $(".update-status-model").attr("action", url);

      $(".update-status-model").find('#field_type').val(field_type);

      $(".update-status-model").find('#status-option').html("");
      $('#updateLabel').html("Status");
      selectOption = '<select name="chosen_status" class="form-control updateFields"><option value="Active" selected="selected">Active</option><option value="Disabled">Disabled</option></select>';

      $(".update-status-model").find('#status-option').html(selectOption);
      $('#status-option').find(".updateFields").val(currentValue);
    });

</script>
@endsection