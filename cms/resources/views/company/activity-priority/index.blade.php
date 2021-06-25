@extends('layouts.company')

@section('title', 'Activities Priorities')

@section('stylesheets')

  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
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
          <?php if (session()->has('message')) {
              echo '<div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-ban"></i> Alert!</h4>';
              echo session()->get('message');
              echo '</div>';
          }?>

        <div class="box">

          <div class="box-header">

            <h3 class="box-title">Activities Priorities</h3>

            <a class="btn btn-primary pull-right" style="margin-left: 5px;" data-toggle="modal"
               data-target="#AddActivityType"> <i class="fa fa-plus"></i> Create New </a>

            <span id="employeegroupexports" class="pull-right"></span>


          </div>

          <!-- /.box-header -->

          <div class="box-body">

            <table id="employeegroups" class="table table-bordered table-striped">

              <thead>

              <tr>

                <th>#</th>

                <th>Name</th>

                <th>Action</th>

              </tr>

              </thead>

              <tbody>

              @php($i = 0)

              @foreach($activityPriority as $activityType)

                @php($i++)

                <tr>

                  <td>{{ $i }}</td>

                  <td>{{ $activityType->name}}</td>

                  <td>

                    <a class="btn btn-warning btn-sm rowEditActivityType" activity-id="{{$activityType->id}}"
                       activity-name="{{$activityType->name}}" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>

                    @if($activityType->activities->count() ==0 )
                    <a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="{{$activityType->id}}"
                       activity-name="{{$activityType->name}}" style="padding: 3px 6px;"><i
                          class="fa fa-trash-o"></i></a>
                    @endif
                  </td>

                </tr>

              @endforeach

              </tbody>

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
  <!-- Button trigger modal -->
  <!-- Modal -->

  <div class="modal fade" id="AddActivityType" tabindex="-1" role="dialog">
    <form id="addNewActivityType" method="post"
          action="{{domain_route('company.admin.activities-priority.store')}}">@csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create New Activity Priority</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-2" style="text-align: right;">
                Name
              </div>
              <div class="col-xs-10">
                <input class="form-control" type="text" name="name" required="">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
            <button id="addkey" type="submit" class="btn btn-primary">Create</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->

  <div class="modal fade" id="EditActivityType" tabindex="-1" role="dialog">
    <form id="editExistingActivityType" method="post"
          action="{{domain_route('company.admin.activityPriority.update')}}">@csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" align="center">Update Activity Priority</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-2" style="text-align: right;line-height: 2.4em;">
                Name
              </div>
              <div class="col-xs-10">
                <input type="text" name="id" id="edit_id" hidden>
                <input class="form-control" id="edit_name" type="text" name="name" required="">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
            <button id="editkey" type="submit" class="btn btn-primary">Update</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->


  <div class="modal fade" id="DeleteActivityType" tabindex="-1" role="dialog">
    <form id="deleteExistingActivityType" method="post"
          action="{{domain_route('company.admin.activityPriority.delete')}}">@csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" align="center">Deletion Confirmation</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
                <div align="center">
                  Are you sure you want to Delete Activity Priority ( <span id="del_title"></span> ) ?
                </div>
                <input type="text" name="id" id="delete_id" hidden>
                <input hidden id="delete_name" type="text" name="name" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
            <button id="delkey" type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->

  <form method="post" action="{{domain_route('company.admin.activityType.customPdfExport')}}"
    class="pdf-export-form hidden" id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
  </form>


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

    var table;

      $(function () {

          $('.rowEditActivityType').on('click', function () {
              $('#edit_id').val($(this).attr('activity-id'));
              $('#edit_name').val($(this).attr('activity-name'));
              $('#EditActivityType').modal('show');
          });

          $('.rowDeleteActivityType').on('click', function () {
              $('#delete_id').val($(this).attr('activity-id'));
              $('#delete_name').val($(this).attr('activity-name'));
              $('#DeleteActivityType').modal('show');
              $('#del_title').html($(this).attr('activity-name'));
          });


          table = $('#employeegroups').DataTable({
              "columnDefs": [ {
                "targets": 2,
                "orderable": false
                }],
                "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
          "<'row'<'col-xs-6'><'col-xs-6'>>" +
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
              buttons: [
                  {
                      extend: 'colvis',
                      order: 'alpha',
                      className: 'dropbtn',
                      columns:[0,1],
                      text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                      columnText: function ( dt, idx, title ) {
                          return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                      }
                  },
                  {
                      extend: 'excelHtml5',
                      title: 'Activity Priorities List',
                      exportOptions: {
                        columns: ':visible:not(:last-child)'
                      },
                  },
                  {
                      extend: 'pdfHtml5',
                      action: function ( e, dt, node, config ) {
                        newExportAction( e, dt, node, config );
                      },
                      title: 'Activity Priorities List',
                      exportOptions: {
                        columns: ':visible:not(:last-child)'
                      },
                  },
                  {
                      extend: 'print',
                      title: 'Activity Priorities List',
                      exportOptions: {
                        columns: ':visible:not(:last-child)'
                      },
                  },
              ]

          });

          table.buttons().container().appendTo('#employeegroupexports');

          $('#addNewActivityType').on('submit', function (event) {
              event.preventDefault();
              var currentElement = $(this);
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: currentElement.attr('action'),
                  type: "POST",
                  data: new FormData(this),
                  processData: false,
                  contentType: false,
                  cache: false,
                  beforeSend:function(){
                    $('#addkey').attr('disabled',true);
                  },
                  success: function (data) {
                    if(data['result']== true){
                      alert('Created Successfully');
                    }else{
                      alert('Priority already exists');                      
                    }
                      window.location.href = "{{domain_route('company.admin.activities-priority.index')}}";
                      // table.clear().draw();
                      // $('#AddActivityType').modal('hide');
                      // $('#addNewActivityType').trigger('reset');

                      // for (i = 0; i < data['count']; i++) {
                      //     var editurl = "{{ domain_route('company.admin.activities-priority.edit',['id']) }}";
                      //     editurl = editurl.replace('id', data['activityType'][i]['id']);
                      //     var delurl = "{{ domain_route('company.admin.activities-priority.destroy',['id']) }}";
                      //     delurl = delurl.replace('id', data['activityType'][i]['id']);
                      //     var submiturl = "$(" + "'#" + data['activityType'][i]['id'] + "').submit();";
                      //     table.row.add([
                      //         i + 1,
                      //         data['activityType'][i]['name'],
                      //         '<a  class="btn btn-warning btn-sm rowEditActivityType" activity-id="' + data['activityType'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="' + data['activityType'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>',
                      //     ]).draw();
                      // }
                  },
              });

          });

          $('#editExistingActivityType').on('submit', function (event) {
              event.preventDefault();
              var edit_id = $('#edit_id').val();
              var url = "{{domain_route('company.admin.activityPriority.update')}}";
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  processData: false,
                  contentType: false,
                  cache: false,
                  beforeSend:function(){
                    $('#editkey').attr('disabled',true);
                  },
                  success: function (data) {
                      if(data['result']== true){
                        alert('Updated Successfully');
                      }else{
                        alert('Priority already exists');                      
                      }
                      window.location.href = "{{domain_route('company.admin.activities-priority.index')}}";
                      // table.clear().draw();
                      // $('#EditActivityType').modal('hide');
                      // $('#editExistingActivityType').trigger('reset');

                      // for (i = 0; i < data['count']; i++) {
                      //     var editurl = "{{ domain_route('company.admin.activities-priority.edit',['id']) }}";
                      //     editurl = editurl.replace('id', data['activityType'][i]['id']);
                      //     var delurl = "{{ domain_route('company.admin.activities-priority.destroy',['id']) }}";
                      //     delurl = delurl.replace('id', data['activityType'][i]['id']);
                      //     var submiturl = "$(" + "'#" + data['activityType'][i]['id'] + "').submit();";
                      //     table.row.add([
                      //         i + 1,
                      //         data['activityPriority'][i]['name'],
                      //         '<a  class="btn btn-warning btn-sm rowEditActivityType" activity-id="' + data['activityPriority'][i]['id'] + '" activity-name="' + data['activityPriority'][i]['name'] + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="' + data['activityPriority'][i]['id'] + '" activity-name="' + data['activityPriority'][i]['name'] + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>',
                      //     ]).draw();
                      // }
                  },
              });
          });

          $('#deleteExistingActivityType').on('submit', function (event) {
              event.preventDefault();
              var edit_id = $('#edit_id').val();
              var url = "{{domain_route('company.admin.activityPriority.delete')}}";
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  processData: false,
                  contentType: false,
                  cache: false,
                  beforeSend:function(){
                    $('#delkey').attr('disabled',true);
                  },
                  success: function (data) {
                    if(data['result']==true){
                      alert('Deleted Successfully');
                    }else{
                      alert('Sorry! Something went wrong');
                    }
                      window.location.href = "{{domain_route('company.admin.activities-priority.index')}}";
                      // table.clear().draw();
                      // $('#DeleteActivityType').modal('hide');
                      // $('#deleteExistingActivityType').trigger('reset');

                      // for (i = 0; i < data['count']; i++) {
                      //     var editurl = "{{ domain_route('company.admin.activities-priority.edit',['id']) }}";
                      //     editurl = editurl.replace('id', data['activityPriority'][i]['id']);
                      //     var delurl = "{{ domain_route('company.admin.activities-priority.destroy',['id']) }}";
                      //     delurl = delurl.replace('id', data['activityPriority'][i]['id']);
                      //     var submiturl = "$(" + "'#" + data['activityPriority'][i]['id'] + "').submit();";
                      //     table.row.add([
                      //         i + 1,
                      //         data['activityPriority'][i]['name'],
                      //         '<a  class="btn btn-warning btn-sm rowEditActivityType" activity-id="' + data['activityPriority'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="' + data['activityPriority'][i]['id'] + '" activity-name="' + data['activityPriority'][i]['name'] + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>',
                      //     ]).draw();
                      // }
                  },
              });
          });

      });

      function customExportAction(config, data, cols, props){
        $('#exportedData').val(JSON.stringify(data));
        $('#pageTitle').val(config.title);
        $('#columns').val(cols);
        $('#properties').val(props);
        $('#pdf-generate').submit();
      }
      
      var newExportAction = function (e, dt, button, config) {
        var self = this;
        var data = [];
        var count = 0;
        // table.rows({"search":"applied" }).every( function () {
        //   var row = {};
        //   row["id"] = ++count;
        //   row["name"] = this.data()[1];
        //   data.push(row);
        // });
        var columnsArray = [];
        var propertiesArray = [];
        var visibleColumns = dt.settings()[0].aoColumns.map(setting => {
                                if(setting.bVisible){
                                  let title = setting.sTitle.replace(/<[^>]*>?/gm, '');
                                  columnsArray.push(title);
                                  switch(title){
                                    case "#":
                                      propertiesArray.push("id")
                                      break;
                                    case "Name":
                                      propertiesArray.push("name")
                                      break;
                                    default:
                                      propertiesArray.push("action")
                                      break;
                                  }
                                } 
                              })    
        columnsArray.pop("Action")
        propertiesArray.pop("action")
        table.rows({"search":"applied" }).every( function () {
          var row = {};          
          if($.inArray("id", propertiesArray) != -1) row["id"] = ++count;
          if($.inArray("name", propertiesArray) != -1) row["name"] = this.data()[1];
          data.push(row);
        });
        var properties = JSON.stringify(propertiesArray);
        var columns = JSON.stringify(columnsArray);

        customExportAction(config, data, columns, properties);
      };

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