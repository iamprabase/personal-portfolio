@extends('layouts.company')

@section('title', 'Return Reason')

@section('stylesheets')

  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">


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

            <h3 class="box-title">Return Reasons</h3>

            <a class="btn btn-primary pull-right" style="margin-left: 5px;" data-toggle="modal"
               data-target="#AddActivityType"> <i class="fa fa-plus"></i> Create New </a>

            <span id="employeegroupexports" class="pull-right"></span>


          </div>

          <!-- /.box-header -->

          <div class="box-body">

            <table id="employeegroups" class="table table-bordered table-striped">

              <thead>

              <tr>

                <th>S.No.</th>

                <th>Name</th>

                <th>Action</th>

              </tr>

              </thead>

              <tbody>

              @php($i = 0)

              @foreach($activityTypes as $activityType)

                @php($i++)

                <tr>

                  <td>{{ $i }}</td>

                  <td>{{ $activityType->name}}</td>

                  <td>

                    <a class="btn btn-warning btn-sm rowEditActivityType" activity-id="{{$activityType->id}}"
                       activity-name="{{$activityType->name}}" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>

                    
                    <a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="{{$activityType->id}}"
                       activity-name="{{$activityType->name}}" style="padding: 3px 6px;"><i
                          class="fa fa-trash-o"></i></a>
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
    <form id="addNewActivityType" method="post" action="{{domain_route('company.admin.returnreason.store')}}">@csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create New Return Reason</h4>
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
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button id="addkey" type="submit" class="btn btn-primary">Create</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->

  <div class="modal fade" id="EditActivityType" tabindex="-1" role="dialog">
    <form id="editExistingActivityType" method="post"
          action="{{domain_route('company.admin.returnreason.update')}}">@csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" align="center">Update Return Reason</h4>
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
            <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>
            <button id="updatekey" type="submit" class="btn btn-primary">Update</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->


  <div class="modal fade" id="DeleteActivityType" tabindex="-1" role="dialog">
    <form id="deleteExistingActivityType" method="post"
          action="{{domain_route('company.admin.returnreason.delete')}}">@csrf
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
                  Are you sure you want to Delete Return reason(<span id="del_title"></span>) ?
                </div>
                <input type="text" name="id" id="delete_id" hidden>
                <input hidden id="delete_name" type="text" name="name"/>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>
            <button id="delkey" type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->




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

          var table = $('#employeegroups').DataTable({
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Employee Group List',
                      exportOptions: {
                          columns: [0, 1]
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Employee Group List',
                      exportOptions: {
                          columns: [0, 1]
                      }
                  },
                  {
                      extend: 'print',
                      title: 'Employee Group List',
                      exportOptions: {
                          columns: [0, 1]
                      }
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
                      if(data['result']==true){
                        alert('Return Reason created Successfully.');
                      }else{
                        alert('Sorry! Return Reason already exists.');
                      }
                      window.location.href = "{{domain_route('company.admin.returnreason.index')}}";
                      table.clear().draw();
                      $('#AddActivityType').modal('hide');
                      $('#addNewActivityType').trigger('reset');

                      for (i = 0; i < data['count']; i++) {
                          var editurl = "{{ domain_route('company.admin.returnreason.edit',['id']) }}";
                          editurl = editurl.replace('id', data['activityType'][i]['id']);
                          var delurl = "{{ domain_route('company.admin.returnreason.destroy',['id']) }}";
                          delurl = delurl.replace('id', data['activityType'][i]['id']);
                          var submiturl = "$(" + "'#" + data['activityType'][i]['id'] + "').submit();";
                          table.row.add([
                              i + 1,
                              data['activityType'][i]['name'],
                              '<a  class="btn btn-warning btn-sm rowEditActivityType" activity-id="' + data['activityType'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="' + data['activityType'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>',
                          ]).draw();
                      }
                  },
              });

          });

          $('#editExistingActivityType').on('submit', function (event) {
              event.preventDefault();
              var edit_id = $('#edit_id').val();
              var url = "{{domain_route('company.admin.returnreason.update')}}";
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
                    $('#updatekey').attr('disabled',true);
                  },
                  success: function (data) {
                    if(data['result']==true){
                      alert('Updated Successfully');
                    }else{
                      alert('Sorry! Activity Type already exists.')
                    }
                    window.location.href = "{{domain_route('company.admin.returnreason.index')}}";
                      table.clear().draw();
                      $('#EditActivityType').modal('hide');
                      $('#editExistingActivityType').trigger('reset');

                      for (i = 0; i < data['count']; i++) {
                          var editurl = "{{ domain_route('company.admin.returnreason.edit',['id']) }}";
                          editurl = editurl.replace('id', data['activityType'][i]['id']);
                          var delurl = "{{ domain_route('company.admin.returnreason.destroy',['id']) }}";
                          delurl = delurl.replace('id', data['activityType'][i]['id']);
                          var submiturl = "$(" + "'#" + data['activityType'][i]['id'] + "').submit();";
                          table.row.add([
                              i + 1,
                              data['activityType'][i]['name'],
                              '<a  class="btn btn-warning btn-sm rowEditActivityType" activity-id="' + data['activityType'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="' + data['activityType'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>',
                          ]).draw();
                      }
                  },
              });
          });

          $('#deleteExistingActivityType').on('submit', function (event) {
              event.preventDefault();
              var edit_id = $('#edit_id').val();
              var url = "{{domain_route('company.admin.returnreason.delete')}}";
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
                        window.location.href = "{{domain_route('company.admin.returnreason.index')}}";
                      }else{
                      // table.clear().draw();
                      // $('#DeleteActivityType').modal('hide');
                      // $('#deleteExistingActivityType').trigger('reset');

                      // for (i = 0; i < data['count']; i++) {
                      //     var editurl = "{{ domain_route('company.admin.returnreason.edit',['id']) }}";
                      //     editurl = editurl.replace('id', data['activityType'][i]['id']);
                      //     var delurl = "{{ domain_route('company.admin.activities-type.destroy',['id']) }}";
                      //     delurl = delurl.replace('id', data['activityType'][i]['id']);
                      //     var submiturl = "$(" + "'#" + data['activityType'][i]['id'] + "').submit();";
                      //     table.row.add([
                      //         i + 1,
                      //         data['activityType'][i]['name'],
                      //         '<a  class="btn btn-warning btn-sm rowEditActivityType" activity-id="' + data['activityType'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteActivityType" activity-id="' + data['activityType'][i]['id'] + '" activity-name="' + data['activityType'][i]['name'] + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>',
                      //     ]).draw();
                      // }
                      }
                  },
              });
          });

      });


  </script>
@endsection