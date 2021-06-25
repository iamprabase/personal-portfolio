@extends('layouts.company')
@section('title', 'Parties')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
      @endif
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{ $clienttype }} List</h3>
          @if(Auth::user()->can('party-create'))
          <a href="{{ domain_route('company.admin.client.create') }}" class="btn btn-primary pull-right"
            style="margin-left: 5px;">
            <i class="fa fa-plus"></i> Create New
          </a>
          @endif
          <span id="clientexports" class="pull-right"></span>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="client" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>S.No.</th>
                <th>Party Name</th>
                <th>Person Name</th>
                <th>Phone</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
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
<div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <form method="post" class="remove-record-model">
        {{method_field('delete')}}
        {{csrf_field()}}
        <div class="modal-body">
          <p class="text-center">
            Are you sure you want to delete this?
          </p>
          <input type="hidden" name="client_id" id="c_id" value="">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>
          <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
      </div>
        <div class="modal-body">
          <p class="text-center">
            Sorry! You are not authorized to change status.
          </p>
          <input type="hidden" name="expense_id" id="c_id" value="">
          <input type="text" id="accountType" name="account_type" hidden/>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="changeStatus" method="POST"
          action="{{URL::to('admin/client/changeStatus')}}">
          {{csrf_field()}}
          <input type="hidden" name="client_id" id="client_id" value="">
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Status</label>
            <div class="col-sm-10">
              <select class="form-control" id="status" name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn actionBtn">
              <span id="footer_action_button" class='glyphicon'> </span> Change
            </button>
            <button type="button" class="btn btn-warning" data-dismiss="modal">
              <span class='glyphicon glyphicon-remove'></span> Close
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
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
<script>
  $(function () {
    $('#delete').on('show.bs.modal', function (event) {

      var button = $(event.relatedTarget)

      var mid = button.data('mid')

      var url = button.data('url');

      $(".remove-record-model").attr("action", url);

      var modal = $(this)

      modal.find('.modal-body #m_id').val(mid);

    })

  });
  $(document).ready(function () {
    @if (strpos(URL::previous(), domain_route('company.admin.client')) === false)  
      var activeRequestsTable = $('#client').DataTable();
      activeRequestsTable.state.clear();  // 1a - Clear State
      activeRequestsTable.destroy();   // 1b - Destroy
    @endif
    const clientTypeId = {{ $party_type_id }};
    initializeDT(clientTypeId);
  });

  function initializeDT(partyTypeId){
    const table = $('#client').removeAttr('width').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [[ 0, "desc" ]],
      "columnDefs": [
        {
          "orderable": false,
          "targets":[-1],
        },
        { 
          width: 20, 
          targets: [0,7]
        },
      ],
      "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
            "<'row'<'col-xs-6'><'col-xs-6'>>" +
            "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
      "buttons": [
        {
          extend: 'excelHtml5',
          title: '{{$clienttype}} List',
          exportOptions: {
            columns: ':not(:last-child)'
          },
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'pdfHtml5',
          title: '{{$clienttype}} List',
          exportOptions: {
            columns: ':not(:last-child)'
          },
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
        {
          extend: 'print',
          title: '{{$clienttype}} List',
          exportOptions: {
            columns: ':not(:last-child)'
          },
          action: function ( e, dt, node, config ) {
            newExportAction( e, dt, node, config );
          }
        },
      ],
      "ajax":
      {
        "url": "{{ domain_route('company.admin.client.subclients.ajaxDatatable', [$party_type_id]) }}",
        "dataType": "json",
        "type": "POST",
        "data":{ 
          _token: "{{csrf_token()}}",
          client_type_id : partyTypeId,
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
      "columns": [
        { "data": "id" },
        { "data": "company_name" },
        { "data": "name" },
         { "data": 'phone',
    render: function ( data, type, row ) {
      if(data){
        var dateSplit = data.split(',');
        return dateSplit[0];
      }else {
      return data;
      }
    }
        },
        { "data": "mobile" },
        { "data": "email" },
        { "data": "status" },
        { "data": "action" },
      ],
    });
    table.buttons().container().appendTo('#clientexports');
  }

  var oldExportAction = function (self, e, dt, button, config) {
    if(button[0].className.indexOf('buttons-excel') >= 0) {
      if($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
      }else{
        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
      }
    }else if(button[0].className.indexOf('buttons-pdf') >= 0) {
      if($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
      }else{
        $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
      }
    }else if(button[0].className.indexOf('buttons-print') >= 0) {
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
      data.length = {{$clients}};
      dt.one('preDraw', function (e, settings) {
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
  }; 
  $(document).on('click', '.edit-modal', function () {
    $('#footer_action_button').addClass('glyphicon-check');
    $('#footer_action_button').removeClass('glyphicon-trash');
    $('.actionBtn').addClass('btn-success');
    $('.actionBtn').removeClass('btn-danger');
    $('.actionBtn').addClass('edit');
    $('.modal-title').text('Change Status');
    $('.deleteContent').hide();
    $('.form-horizontal').show();
    $('#client_id').val($(this).data('id'));
    $('#remark').val($(this).data('remark'));
    $('#status').val($(this).data('status'));
    $('#myModal').modal('show');
  });
  $('#client').on('click','.alert-modal',function(){
    $('#alertModal').modal('show');
  });
</script>
@endsection