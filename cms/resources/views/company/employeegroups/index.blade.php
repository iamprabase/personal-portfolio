@extends('layouts.company')
@section('title', 'Employee Groups')

@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/dist/css/delta.css') }}">
  <style>
    .close{
      font-size: 30px;
      color: #080808;
      opacity: 1;
    }
    .hide_column { 
      display: none;
    }

    .round {
      position: relative;
      width: 15px;
    }

    .round label {
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 50%;
      cursor: pointer;
      height: 15px;
      left: 0;
      position: absolute;
      top: 3px;
      width: 28px;
    }

    .round label:after {
      border: 2px solid #fff;
      border-top: none;
      border-right: none;
      content: "";
      height: 6px;
      left: 0px;
      opacity: 0;
      position: absolute;
      top: 3px;
      transform: rotate(-45deg);
      width: 12px;
    }

    .round input{
      height: 10px;
    }

    .round input[type="checkbox"] {
      visibility: hidden;
    }

    .round input[type="checkbox"]:checked + label {
      background-color: #66bb6a;
      border-color: #66bb6a;
    }

    .round input[type="checkbox"]:checked + label:after {
      opacity: 1;
    }

    .pad-left{
      padding-left: 0px;
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

        @if (session()->has('message'))
          <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h4><i class="icon fa fa-ban"></i> Alert!</h4>
            <p>{{ \Session::get('message') }}</p>
          </div>
        @endif

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Employee Groups</h3>
            <a href="{{ domain_route('company.admin.employeegroup.create') }}" class="btn btn-primary pull-right"
                style="margin-left: 5px;">
              <i class="fa fa-plus"></i> Create New
            </a>
            <span id="employeegroupexports" class="pull-right"></span>
          </div>
          <!-- /.box-header -->
          <div class="box-body"  id="mainBox">
            <table id="employeegroups" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>S.No.</th>
                <th>Name</th>
                <th>Desc.</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
              </thead>
              <div id="loader1" hidden>
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
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
  <form method="post" action="{{domain_route('company.admin.employeegroups.custompdfdexport')}}" class="pdf-export-form hidden"
    id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
  </form>
  <div class="modal modal-default fade" id="delete" tabindex="-1" employeegroup="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" employeegroup="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
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
            <input type="hidden" name="employeegroup_id" id="m_id" value="">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </form>
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
                action="{{URL::to('admin/employeegroup/changeStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="employeegroup_id" id="employeegroup_id" value="">
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Status</label>
              <div class="col-xs-10">
                <select class="form-control" id="status" name="status">
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
            <p class="text-center" style="color:red;display: none" id="warning">
              Warning: Changing the group status to Inactive will turn all the employees in the group to inactive
            </p>
            <div class="modal-footer">
              <button type="button" class="btn actionBtn" onclick="confirmation()">
                <span id="footer_action_button" class='glyphicon'> </span> Change
              </button>
              {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button> --}}
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
        
        modal.find('#myModalLabel').html('Delete Confirmation');

        modal.find('.modal-body #m_id').val(mid);

      });
    });
    $(document).ready(function () {

      @if (strpos(URL::previous(), domain_route('company.admin.employeegroup')) === false)  
        var activeRequestsTable = $('#employeegroups').DataTable();
        activeRequestsTable.state.clear();  // 1a - Clear State
        activeRequestsTable.destroy();   // 1b - Destroy
      @endif

      initializeDT();      
    });

    function initializeDT(){
      const table = $('#employeegroups').removeAttr('width').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [[ 1, "asc" ]],
        "columnDefs": [
          {
            "orderable": false,
            "targets":[-1],
          },
          { 
            width: 20, 
            targets: [0,4]
          },
          { 
            width: 150, 
            targets: 1 
          },
          { 
            width: 350, 
            targets: 1 
          },
          { 
            width: 20, 
            targets: 3 
          },
        ],

        "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
              "<'row'<'col-xs-6'><'col-xs-6'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
        "buttons": [
            {
                extend: 'colvis',
                order: 'alpha',
                className: 'dropbtn',
                columns:[0,1,2,3],
                text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                columnText: function ( dt, idx, title ) {
                    return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                }
            },
          {
            extend: 'excelHtml5',
            title: 'Employee Group List',
            
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'pdfHtml5',
            title: 'Employee Group List',
            
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
          {
            extend: 'print',
            title: 'Employee Group List',
            
            exportOptions: {
              columns: ':visible:not(:last-child)'
            },
            action: function ( e, dt, node, config ) {
              newExportAction( e, dt, node, config );
            }
          },
        ],
        "ajax":
        {
          "url": "{{ domain_route('company.admin.employeegroups.ajaxDatatable') }}",
          "dataType": "json",
          "type": "POST",
          "data":{ 
            _token: "{{csrf_token()}}",
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
          { "data": "name" },
          { "data": "description" },
          { "data": "status" },
          { "data": "action" },
        ],
      });
      table.buttons().container().appendTo('#employeegroupexports');
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
        data.length = {{$employeegroups->count()}};
        dt.one('preDraw', function (e, settings) {
          if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
            var columnsArray = [];
            var visibleColumns = settings.aoColumns.map(setting => {
                                    if(setting.bVisible){
                                      columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                                    } 
                                  })    
            columnsArray.pop("Action")
            var columns = JSON.stringify(columnsArray);
            $.each(settings.json.data, function(key, htmlContent){
              settings.json.data[key].status = $(settings.json.data[key].status)[0].textContent; 
            });
            customExportAction(config, settings, columns);
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

    function customExportAction(config, settings, cols){
      $('#exportedData').val(JSON.stringify(settings.json));
      $('#pageTitle').val(config.title);

      $('#columns').val(cols);
      var propertiesArray = [];
      var visibleColumns = settings.aoColumns.map(setting => {
                            if(setting.bVisible) propertiesArray.push(setting.data)
                          })
      propertiesArray.pop("action")
      // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
      var properties = JSON.stringify(propertiesArray);
      $('#properties').val(properties);

      $('#pdf-generate').submit();
    }

    $(document).on('click', '.edit-modal', function () {
      $('#footer_action_button').addClass('glyphicon-check');
      $('#footer_action_button').removeClass('glyphicon-trash');
      $('.actionBtn').addClass('btn-success');
      $('.actionBtn').removeClass('btn-danger');
      $('.actionBtn').addClass('edit');
      $('.modal-title').text('Change Status');
      $('.deleteContent').hide();
      $('.form-horizontal').show();
      $('#employeegroup_id').val($(this).data('id'));
      $('#remark').val($(this).data('remark'));
      $('#status').val($(this).data('status'));
      $('#warning').hide();
      $('#myModal').modal('show');
    });

    function confirmation() {
      var result = confirm('Confirm to change the status?');
      if (result == true) {
        $('#changeStatus').submit();
      }
    }

    $(document).on('change', '#status', function () {
      if ($('#status option:selected').val() == 'Inactive')
        $("#warning").show();
      else
        $("#warning").hide();
    });
    $(document).on('click','.buttons-columnVisibility',function(){
        if($(this).hasClass('active')){
            $(this).find('input').first().prop('checked',true);
            console.log($(this).find('input').first().prop('checked'));
        }else{
            $(this).find('input').first().prop('checked',false);
            console.log($(this).find('input').first().prop('checked'));
        }
    });

    $(document).on('click','.buttons-colvis',function(e){
        var filterBox = $('.dt-button-collection');
        filterBox.find('li').each(function(k,v){
            if($(v).hasClass('active')){
                $(v).find('input').first().prop('checked',true);
            }else{
                $(v).find('input').first().prop('checked',false);
            }
        });
    });

  </script>

@endsection