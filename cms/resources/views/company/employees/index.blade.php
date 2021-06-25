@extends('layouts.company')
@section('title', 'Employees')
@section('stylesheets')
  <link rel="stylesheet"
    href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  <style>
    #importBtn{
      margin-right: 5px;
      border-radius: 0px;
    }
    .select2.select2-container.select2-container--default,.select2.select2-container.select2-container--default.select2-container--focus{
      position: absolute;
      /* width: 50% !important; */
    }
    .box-loader{
      opacity: 0.5;
    }
    .direct-chat-img{
      padding:0px;
    }
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

    .select-2-sec{
      display: flex;
    }

    .mr-5{
      margin-right: 5px;
    }
  </style>
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        @if (\Session::has('alert'))
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4><br/>
            {{ \Session::get('alert') }}
          </div>
        @elseif (\Session::has('message'))
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Alert!</h4>
            {{\Session::get('message')}}
          </div>
        @endif

        @if (\Session::has('success'))
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p>Success!</p>
            {{ \Session::get('success') }}
          </div>
        @endif

        @if (\Session::has('some_juniors_has_client'))
          <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p>Info!</p>
            {{ \Session::get('some_juniors_has_client') }}
          </div>
        @endif

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Employee List</h3>
            @if(Auth::user()->can('employee-create'))
            @if(!session()->has('message'))
              @if($subDesignationExists || Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin())
              <a href="{{ domain_route('company.admin.employee.create') }}" class="btn btn-primary pull-right"
               style="margin-left: 5px;"><i class="fa fa-plus"></i> Create New
              </a>
              @endif
            @endif
            @endif
            <span id="employeeexports" class="pull-right"></span>
          </div>
          <div id="loader1" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <div class="box-body table-responsive" id="mainBox">

            <div class="row">
              <div class="col-xs-2"></div>
              <div class="col-xs-7">
                <div class="row">
                  <div class="select-2-sec">
                    <div class="col-xs-4 mr-5">
                      <div class="employeegroupsDiv hidden" style="margin-top:10px;">
                        <select name="employeegroups" id="employeegroups">
                          <option value="">Select Employee Group</option>
                          @foreach($employeegroups as $id=>$employeegroup)
                          <option value="{{$id}}">{{$employeegroup}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-xs-4 mr-5">
                      <div class="designationsDiv hidden" style="margin-top:10px;">
                        <select name="designations" id="designations">
                          <option value="">Select Designation</option>
                          @foreach($designations as $id=>$designation)
                          <option value="{{$id}}">{{$designation}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-xs-4 mr-5">
                      <div class="rolesDiv hidden" style="margin-top:10px;">
                        <select name="roles" id="roles">
                          <option value="">Select Accessibility</option>
                          @foreach($roles as $id=>$role)
                          <option value="{{$id}}">{{$role}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xs-2"></div>
            </div>
            <table id="employee" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="max-width:30px;">S.No.</th>
                  <th>Employee Name</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Group</th>
                  <th>Designation</th>
                  <th>Accessibility</th>
                  <th>Status</th>
                  <th>Last Action</th>
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
  </section>

  <form method="post" action="{{domain_route('company.admin.employee.customPdfExport')}}" class="pdf-export-form hidden"
    id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
  </form>
  
  <div class="modal modal-default fade" id="addparties" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">
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

            <input type="hidden" name="employee_id" id="c_id" value="">

          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
            <button type="submit" class="btn btn-warning">Yes, Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
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

            <input type="hidden" name="employee_id" id="c_id" value="">

          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
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
            {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
          </div>
      </div>
    </div>
  </div>
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      Modal content
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" id="changeStatus" method="POST"
                action="{{URL::to('admin/employee/changeStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="employee_id" id="employee_id" value="">
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Status</label>
              <div class="col-xs-10">
                <select class="form-control" id="status" name="status">
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
            <span class="max-warning" style="color: red;display: none">Warning: Maximum number of users already reached. Please change status of other users to inactive to make this user active again</span>
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
  <script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
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
      let targetHiddenColumns = new Array()
      if(sessionStorage.getItem('DT_Colvis_Hide_Emp')){
        targetHiddenColumns = JSON.parse(sessionStorage.getItem('DT_Colvis_Hide_Emp'))
      }else{
        targetHiddenColumns = new Array(4,5,6)
        sessionStorage.setItem('DT_Colvis_Hide_Emp', JSON.stringify(targetHiddenColumns));
      }
    $(function () {
      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        // $(".remove-record-model").attr("action",url);
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
        modal.find('#myModalLabel').html('Delete Confirmation');
        modal.find('.modal-body #m_id').val(mid);
      });
    });

    $(document).ready(function () {
      @if (strpos(URL::previous(), domain_route('company.admin.employee')) === false)  
        var activeRequestsTable = $('#employee').DataTable();
        activeRequestsTable.state.clear();
        activeRequestsTable.destroy();
      @endif
      let eIdPara = null; let empGroup = null; let empDesignationPara = "{{$filtered}}";
      @if (\Session::has('DT_EMP_filters'))
        let filtersSel = @json(\Session::get('DT_EMP_filters'));
        sessionStorage.setItem('DT_EMP_filters', filtersSel);
      @else
        sessionStorage.setItem('DT_EMP_filters', "");
      @endif
      if(sessionStorage.getItem('DT_EMP_filters')!="" || !sessionStorage.getItem('DT_EMP_filters')==undefined ){
        let filterValue = JSON.parse(sessionStorage.getItem('DT_EMP_filters'));
        if(filterValue){
          empGroup = filterValue.employeegroups; 
          empDesignationPara = filterValue.designation;
          sessionStorage.setItem('DT_EMP_filters', "");
          $('body').find('#designations').val(empDesignationPara);
          $('body').find('#employeegroups').val(empGroup);
        }
      }else{
        sessionStorage.setItem('DT_EMP_filters', "");
      }
      initializeDT(eIdPara, empGroup, empDesignationPara);
      $('#employee').on( 'column-visibility.dt', function ( e, settings, column, state ) {
      
      let currentCols = JSON.parse(sessionStorage.getItem('DT_Colvis_Hide_Emp'))
      if(!state){// Checked
        currentCols = [...currentCols, column]
      }else {//Unchecked
        currentCols = currentCols.filter(function(col){
          return col!=column
        })
      }
      sessionStorage.setItem('DT_Colvis_Hide_Emp', JSON.stringify(currentCols));
    });

    $('.employeegroupsDiv').removeClass('hidden');
    $('#employeegroups').select2();
    $('body').on("change", "#employeegroups",function () {
      var employeegroups = $(this).find('option:selected').val();
      var designation = $('#designations').find('option:selected').val();
      
      
      $('#employee').DataTable().destroy();
      if(employeegroups=="" && designation==""){
        sessionStorage.setItem('DT_EMP_filters', "");
      }else{
        sessionStorage.setItem('DT_EMP_filters', JSON.stringify({employeegroups:employeegroups, designation:designation}));
      }
      initializeDT(empl=null, employeegroups, designation);
      
    });
    $('.designationsDiv').removeClass('hidden');
    $('#designations').select2();
    $('body').on("change", "#designations",function () {
      var designation = $(this).find('option:selected').val();
      var employeegroups = $('#employeegroups').find('option:selected').val();
      
      
      $('#employee').DataTable().destroy();
      if(employeegroups=="" && designation==""){
        sessionStorage.setItem('DT_EMP_filters', "");
      }else{
        sessionStorage.setItem('DT_EMP_filters', JSON.stringify({employeegroups:employeegroups, designation:designation}));
      }
      initializeDT(empl=null, employeegroups, designation);
    });
    $('.rolesDiv').removeClass('hidden');
    $('#roles').select2();
    $('body').on("change", "#roles",function () {
      var designation = $('#designations').find('option:selected').val();
      var employeegroups = $('#employeegroups').find('option:selected').val();
      
      $('#employee').DataTable().destroy();
      if(employeegroups=="" && designation==""){
        sessionStorage.setItem('DT_EMP_filters', "");
      }else{
        sessionStorage.setItem('DT_EMP_filters', JSON.stringify({employeegroups:employeegroups, designation:designation}));
      }
      initializeDT(empl=null, employeegroups, designation);
    });

    if(empDesignationPara!="") {
      @if($filtered!="")
        $('body').find('#designations').select2("destroy").val({{$filtered}}).select2();//.trigger("change");
      @endif
    }
      // var empSelect = "<select id='employee_filters' class='employee_filters'><option></option>@foreach($employees as                         $employee)<option value='{{$employee->name}}'>{{$employee->name}}</option>@endforeach</select>";
      
      function initializeDT(empVal=null, employeegroup=null, designation=null){
        var table = $('#employee').DataTable({
          "order": [[ 0, "desc" ]],
          "columnDefs": [
            {
              "orderable": false,
              "targets":[-1],
            },
            {
              "width": 60,
              "targets": [-1],
            },
            {"targets":targetHiddenColumns, visible:false },
          ],
          "processing": true,
          "serverSide": true,
          "stateSave": true,
          "ajax":{
            "url": "{{ domain_route('company.admin.employee.ajaxDatatable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              empVal: empVal,
              employeegroup: employeegroup,
              designation: designation,
              role: $('#roles').find('option:selected').val()

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
            { "data": "phone" },
            { "data": "email" },
            { "data": "employeegroup" },
            { "data": "designations" },
            { "data": "accessibility" },
            { "data": "status" },
            { "data": "last_action" },
            { "data": "action" },
          ],
          "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
          "<'row'<'col-xs-6'><'col-xs-6'>>" +
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
          buttons: [
              {
                  extend: 'colvis',
                  order: 'alpha',
                  className: 'dropbtn',
                  columns:[0,1,2,3,4,5,6,7,8],
                  text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                  columnText: function ( dt, idx, title ) {
                      return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                  }
              },
              @if(Auth::user()->can('import-view') && Auth::user()->can('employee-create') && config('settings.import') == 1)
              { 
                text: 'Import',
                attr: { id: 'importBtn'},
                action: function ( e, dt, node, config ) {
                    onclick (window.location.href='{{ domain_route('company.admin.import.employees') }}')
                }
              },
              @endif
              {
                extend: 'pdfHtml5', 
                title: 'Employee List', 
                
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                orientation:'landscape',
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
              },
              {
                extend: 'excelHtml5', 
                title: 'Employee List', 
                
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
              },
              {
                extend: 'print', 
                title: 'Employee List', 
                
                exportOptions: {
                  columns: ':visible:not(:last-child)'
                },
                action: function ( e, dt, node, config ) {
                  newExportAction( e, dt, node, config );
                }
              },
          ],
        });
        table.buttons().container()
            .appendTo('#employeeexports');
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
          data.length = {{$employeesCount}};
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
                settings.json.data[key].id = key+1;
                settings.json.data[key].name = $(settings.json.data[key].name)[1].textContent;
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

      // $('.dataTables_length').append(empSelect);
      $('.employee_filters').select2({
        "placeholder": "Select Employee",
      });
      $('body').on("change", ".employee_filters",function () {
        var empVal = $(this).find('option:selected').val();
        if(empVal != ''){
          $('#employee').DataTable().destroy();
          initializeDT(empVal);
          // $('.dataTables_length').append(empSelect);
          $('#employee_filters').val(empVal);
          $('.employee_filters').select2({
            "placeholder": "Select Employee",
          });
        }
      });    
    });

    $(document).on('click', '.edit-modal', function () {
      // $('#footer_action_button').text(" Change");
      $('#footer_action_button').addClass('glyphicon-check');
      $('#footer_action_button').removeClass('glyphicon-trash');
      $('.actionBtn').addClass('btn-success');
      $('.actionBtn').removeClass('btn-danger');
      $('.actionBtn').addClass('edit');
      $('.modal-title').text('Change Status');
      $('.deleteContent').hide();
      $('.form-horizontal').show();
      $('#employee_id').val($(this).data('id'));
      $('#remark').val($(this).data('remark'));
      $('#status').val($(this).data('status'));
      $('.max-warning').hide();
      if ($(this).data('status') == "Archived") {
          $('#status').disabled();
      }
      $('#myModal').modal('show');
    });

    $('#employee').on('click','.alert-modal',function(){
        $('#alertModal').modal('show');
    });

    $('#status').change(function () {
      var stat = $('option:selected', this).val();
      var empcount = {{json_encode($employeescount)}};//<?php echo json_encode($employeescount); ?>;
      var users = {{$planUsers}};//<?php echo config('settings.users'); ?>;
      if (stat == 'Active') {
        if (empcount >= users) {
          $('.max-warning').show();
        }
      } else if (stat == 'Inactive') {
        $('.max-warning').hide();
      }
    });

    function confirmation() {
      var result = confirm('Confirm to change the status?');
      if (result == true) {
        $('#changeStatus').submit();
      } else if (result == false) {
        $('#myModal').modal('hide');
      }
    }

    var client_list = new Array();

    function empty_list(i) {
      client_list = [];
      $("#party-list" + i).empty();
    }

    function chooseClient(i) {
      var emp_id = i;
      var client_id = $("option:selected", "#client_id" + i).val();
      var client_name = $("option:selected", "#client_id" + i).text();
      if (client_list.includes(client_id) == false && client_id != 0) {
        var newentry = "<li id='client" + client_id + "'><input name='client[]' type='hidden' value='" + client_id + "' class='client_input'>" + client_name + "<a class='btn btn-danger btn-xs pull-right' style='height:18px;' onclick='popClient(" + client_id + ")'>X</a></li>";
        $("#party-list" + i).append(newentry);
        client_list.push(client_id);
      }
    }

    function popClient(c_id) {
      client_list = jQuery.grep(client_list, function (value) {
          return value != c_id;
      });
      $('#client' + c_id).empty();
      $('#client' + c_id).remove();
    }

    function addClient(id) {
      var employee_id = id;
      var map_type = 1;
      var csrf_token = "{{ csrf_token() }}";
      var add_url = "{{URL::to('admin/employee/addClient')}}";

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "POST",
        url: add_url,
        data: {"employee_id": employee_id, "client_list": client_list, "map_type": map_type},
        success: function (data) {
          $("#showHandlers" + id).load(" #showHandlers" + id);
          empty_list(employee_id);
        },
      });
    }
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