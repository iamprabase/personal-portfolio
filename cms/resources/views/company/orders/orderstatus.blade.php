@extends('layouts.company')

@section('title', 'Dynamic Order Status')

@section('stylesheets')

  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/js/bootstrap-colorpicker.min.js"></script> 
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

            <h3 class="box-title">Dynamic Order Status</h3>

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

              @foreach($moduleAttributes as $moduleAttribute)

                @php($i++)

                <tr>

                  <td>{{ $i }}</td>

                  <td>{{ $moduleAttribute->title}}</td>

                  <td>

                      @if($moduleAttribute->title!="Approved")
                      <a class="btn btn-warning btn-sm rowEditActivityType" moduleAttribute-id="{{$moduleAttribute->id}}"
                          moduleAttribute-name="{{$moduleAttribute->title}}" @if($moduleAttribute->color) moduleAttribute-color="{{$moduleAttribute->color}}"@endif style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-danger btn-sm delete rowDeleteActivityType" moduleAttribute-id="{{$moduleAttribute->id}}"
                        moduleAttribute-name="{{$moduleAttribute->title}}" style="padding: 3px 6px;"><i
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
    <form id="addNewStatus" method="post"
          action="{{domain_route('company.admin.orderstatus.store')}}">@csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create New Order Status</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-2" style="text-align: right;">
                Name
              </div>
              <div class="col-xs-10">
                <input class="form-control" type="text" name="name" required="">
                <span id='errlabel' class="label" style="color:red">
                  <span></span>
                </span>
              </div>
            </div>
            <div class="row" style="padding-top:5px;">
                <div class="col-xs-2" style="text-align: right;">
                      Color
                </div>
                <div class="col-xs-10">
                  {{-- <input class="form-control" type="text" name="color" value="#00c8f0" id="color" required=""> --}}
                  <div id="color" class="input-group colorpicker-component">
                      <input type="text" name="color" value="#38a677" class="form-control"/>
                      <span class="input-group-addon"><i></i></span>
                  </div>
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
    <form id="editOrderStatus" method="post"
          action="{{domain_route('company.admin.orderstatus.update')}}">@csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" align="center">Update Order Status</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-2" style="text-align: right;line-height: 2.4em;">
                Name
              </div>
              <div class="col-xs-10">
                <input type="text" name="id" id="edit_id" hidden>
                <input class="form-control" id="edit_name" type="text" name="name" required="">
                <span id='ederrlabel' class="label" style="color:red">
                  <span></span>
                </span>
              </div>
            </div>
            <div class="row" style="padding-top:5px;">
                <div class="col-xs-2" style="text-align: right;">
                        Color
                </div>
                <div class="col-xs-10">
                    {{-- <input class="form-control" id="edit_color" type="color" name="color" required=""> --}}
                    {{-- <input class="form-control" type="text" name="color" id="edit_color" required="" value=""> --}}
                    <div id="edit_color_pick" class="input-group colorpicker-component">
                        <input class="form-control" type="text" name="color" id="edit_color" required value="">
                        <span id="color_span" class="input-group-addon"><i></i></span>
                    </div>
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
          action="{{domain_route('company.admin.orderstatus.delete')}}">@csrf
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
                  Are you sure you want to Delete Order Status ( <span id="del_title"></span> ) ?
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
      $('#color').colorpicker({});
      $('#edit_color_pick').colorpicker({});
      
      $(function () {

          $('.rowEditActivityType').on('click', function () {
              $('#edit_id').val($(this).attr('moduleAttribute-id'));
              $('#edit_name').val($(this).attr('moduleAttribute-name'));
              $('#edit_color').val($(this).attr('moduleAttribute-color'));
              $('#color_span').children().css("background-color", $(this).attr('moduleAttribute-color'));
              $('#EditActivityType').modal('show');
          });

          $('.rowDeleteActivityType').on('click', function () {
              $('#delete_id').val($(this).attr('moduleAttribute-id'));
              $('#delete_name').val($(this).attr('moduleAttribute-name'));
              $('#DeleteActivityType').modal('show');
              $('#del_title').html($(this).attr('moduleAttribute-name'));
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

        //   table.buttons().container().appendTo('#employeegroupexports');

          $('#addNewStatus').on('submit', function (event) {
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
                  beforeSend:function(data){
                    $('#addkey').attr('disabled',true);
                  },
                  success: function (data) {
                    // if(data['result']== true){
                      alert('Created Successfully');
                    // }
                    $('#AddActivityType').modal('hide');
                    window.location.href = "{{domain_route('company.admin.orderstatus')}}";
                  },
                  error:function(jqXHR, textStatus, errorThrown){
                    var err = JSON.parse(jqXHR.responseText);
                    $('#errlabel').html('<span>'+err['errors']['name'][0]+'</span>');
                    $('#addkey').attr('disabled',false);
                  },
              });
          });

          $('#editOrderStatus').on('submit', function (event) {
              event.preventDefault();
              var edit_id = $('#edit_id').val();
              var url = "{{domain_route('company.admin.orderstatus.update')}}";
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
                    //   if(data['result']== true){
                        alert('Updated Successfully');
                    //   }
                      $('#EditActivityType').modal('hide');
                      window.location.href = "{{domain_route('company.admin.orderstatus')}}";
                  },
                  error:function(jqXHR, textStatus, errorThrown){
                    var err = JSON.parse(jqXHR.responseText);
                    $('#ederrlabel').html('<span>'+err['errors']['name'][0]+'</span>');
                    $('#editkey').attr('disabled',false);
                  },
              });
          });

          $('#deleteExistingActivityType').on('submit', function (event) {
              event.preventDefault();
              var edit_id = $('#edit_id').val();
              var url = "{{domain_route('company.admin.orderstatus.delete')}}";
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
                      alert(data);
                      $('#DeleteActivityType').modal('hide');
                    window.location.href = "{{domain_route('company.admin.orderstatus')}}";
                  },
              });
          });

      });


  </script>
@endsection