@extends('layouts.company')

@section('title', 'Tasks')

@section('stylesheets')

  <link rel="stylesheet"
        href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
  <style>
    .daterangepicker .calendar-table th, .daterangepicker .calendar-table td {
      min-width: 25px !important;
      width: 25px !important;
    }

    .table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th {
      padding: 3px !important;
    }

    .daterangepicker.ltr .drp-calendar.right {
      margin-left: 0;
      border-left: 1px solid #ccc !important;
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

            <h3 class="box-title">Task List</h3>

            <a href="{{ domain_route('company.admin.task.create') }}" class="btn btn-primary pull-right"
               style="margin-left: 5px;">

              <i class="fa fa-plus"></i> Create New

            </a>

            <span id="taskexports" class="pull-right"></span>

          </div>

          <!-- /.box-header -->

          <div class="box-body">
            <div id="reportrange" name="reportrange"
                 style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 28%;position: absolute;margin-left: 50%;z-index: 999;">
              <i class="fa fa-calendar"></i>&nbsp;
              <span></span> <i class="fa fa-caret-down"></i>
            </div>
            <table id="task" class="table table-bordered table-striped">

              <thead>


              <tr>

                <th>#</th>

                <th>Assigned From</th>

                <th>Assigned To</th>

                <th>Task Name</th>
                <th>Priority</th>
                <th>Date Created</th>
                <th>Due Date</th>

                <th>Status</th>

                <th>Action</th>

              </tr>

              </thead>

              <tbody>

              @php($i = 0)

              @foreach($tasks as $task)

                @php($i++)

                <tr>

                  <td>{{ $i }}</td>

                  <td>
                  <!-- @if($task->assigned_from_type == 'Admin')
                    {{ getCompany($task->assigned_from)['company_name'] }} Admin
                    @elseif($task->assigned_from_type == 'Employee')
                    {{ getEmployee($task->assigned_from)['name'] }}
                  @endif -->

                    @if($task->assigned_from_type == "Admin")
                      {{ Auth::user()->name.' (Admin)' }}
                    @elseif($task->assigned_from_type == "Employee")
                      {{ getEmployee($task->assigned_from)['name'] }}
                    @endif
                  </td>

                  <td>{{ getEmployee($task->assigned_to)['name'] }}</td>

                  <td>{{ $task->title}}</td>

                  <td>
                    @if($task->priority == 'High')
                      <span class="text-red">{{ $task->priority}}</span>

                    @elseif($task->priority == 'Medium')
                      <span class="text-green">{{ $task->priority }}</span>
                    
                    @elseif($task->priority == 'Low')
                      <span class="text-aqua">{{ $task->priority }}</span>
                    
                    @endif
                  </td>
                  <td>{{ date('d M Y', strtotime($task->created_at)) }}</td>
                  <td>{{ date('d M Y', strtotime($task->due_date)) }}</td>

                  <td>
                    <a href="#" class="edit-modal" data-id="{{$task->id}}"
                       data-status="{{$task->status}}">

                      @if($task->status =='Completed')

                        <span class="label label-success">{{ $task->status}}</span>



                      @elseif($task->status =='Incomplete')

                        <span class="label label-warning">{{ $task->status}}</span>



                      @else

                        <span class="label label-danger">{{ $task->status}}</span>



                      @endif
                    </a>

                  </td>

                  <td>

                    <a href="{{ domain_route('company.admin.task.show',[$task->id]) }}" class="btn btn-success btn-sm"
                       style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a>

                    <a href="{{ domain_route('company.admin.task.edit',[$task->id]) }}" class="btn btn-warning btn-sm"
                       style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>

                    <a class="btn btn-danger btn-sm delete" data-mid="{{ $task->id }}"
                       data-url="{{ domain_route('company.admin.task.destroy', [$task->id]) }}" data-toggle="modal"
                       data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>


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

  <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">

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

            <input type="hidden" name="task_id" id="c_id" value="">


          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>

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
                action="{{URL::to('admin/task/changeStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="task_id" id="task_id" value="">
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Status</label>
              <div class="col-sm-10">
                <select class="form-control" id="status" name="status">
                  <option value="Incomplete">Incomplete</option>
                  <option value="Completed">Completed</option>
                  <option value="Cancelled">Cancelled</option>
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

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <script>

      $(function () {


          $('#delete').on('show.bs.modal', function (event) {

              var button = $(event.relatedTarget)

              var mid = button.data('mid')

              var url = button.data('url');

              // $(".remove-record-model").attr("action",url);

              $(".remove-record-model").attr("action", url);

              var modal = $(this)

              modal.find('.modal-body #m_id').val(mid);

          })

          var table = $('#task').DataTable({
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Task List'
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Task List'
                  },
                  {
                      extend: 'print',
                      title: 'Task List'
                  },
              ]
          });

          table.buttons().container()
              .appendTo('#taskexports');

          $('#reportrange').bind('DOMSubtreeModified', function (event) {
              table.draw();
          });

          var start = moment().subtract(29, 'days');
          var end = moment();

          function cb(start, end) {
              $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
              $('#startdate').val(start.format('MMMM D, YYYY'));
              $('#enddate').val(end.format('MMMM D, YYYY'));
          }

          $('#reportrange').daterangepicker({
              startDate: start,
              endDate: end,
              ranges: {
                  'Today': [moment(), moment()],
                  'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                  'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                  'This Month': [moment().startOf('month'), moment().endOf('month')],
                  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              }
          }, cb);

          cb(start, end);

          $.fn.dataTable.ext.search.push(
              function (settings, data, dataIndex) {
                  var start2 = $('#reportrange').data('daterangepicker').startDate;
                  var end2 = $('#reportrange').data('daterangepicker').endDate;
                  var start_date = Date.parse(start2.format('MMMM D, YYYY'));
                  var end_date = Date.parse(end2.format('MMMM D, YYYY'));
                  var create_date = Date.parse(data[5]); // use data for the age column
                  if (create_date >= start_date && create_date <= end_date) {
                      return true;
                  }
                  return false;
              }
          );


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
          $('#task_id').val($(this).data('id'));
          $('#remark').val($(this).data('remark'));
          $('#status').val($(this).data('status'));
          $('#myModal').modal('show');
      });

  </script>



@endsection