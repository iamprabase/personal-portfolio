@extends('layouts.company')

@section('title', 'Show Task')

@section('stylesheets')

  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">

@endsection



@section('content')

  <section class="content">

    <div class="row">

      <div class="col-md-12">

        <div class="box box-default">

          <div class="box-header with-border">

            <h3 class="box-title">About {{ ($task->title)?$task->title:'NA' }}</h3>

            <div class="page-action pull-right">
              <a href="{{ domain_route('company.admin.task') }}" class="btn btn-default btn-sm"> <i
                    class="fa fa-arrow-left"></i> Back</a>
            </div>

          </div>

          <!-- /.box-header -->

          <div class="box-body">


            <strong><i class="fa fa-book margin-r-5"></i> Description:</strong>


            <p class="text-muted">

              {{ ($task->description)?strip_tags($task->description):'NA' }}

            </p>


            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <colgroup>
                  <col class="col-xs-2">
                  <col class="col-xs-7">
                </colgroup>
                <tbody>
                <tr>
                  <th scope="row"> Assigned From</th>
                  <td>@if($task->assigned_from_type == 'Admin')
                      {{ getCompany($task->assigned_from)['company_name'] }} Admin
                    @elseif($task->assigned_from_type == 'Employee')
                      {{ getEmployee($task->assigned_from)['name'] }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <th scope="row">Assigned To</th>
                  <td>{{ getEmployee($task->assigned_to)->name }}</td>
                </tr>
                <tr>
                  <th scope="row"> Due Date</th>
                  <td>{{ date("d M Y", strtotime($task->due_date)) }} </td>
                </tr>
                <tr>
                  <th scope="row"> Priority</th>
                  <td> {{ $task->priority }} </td>
                </tr>
                <tr>
                  <th scope="row"> Related Party</th>
                  <td> {{ isset($task->client_id)? getClient($task->client_id)['company_name']:null }} </td>
                </tr>
                <tr>
                  <th scope="row"> Status</th>
                  <td><a class=""> @if($task->status =='Completed')

                        <span class="label label-success">{{ $task->status}}</span>



                      @elseif($task->status =='In Progress')

                        <span class="label label-warning">{{ $task->status}}</span>



                      @else

                        <span class="label label-danger">{{ $task->status}}</span>



                      @endif</a></td>
                </tr>
                </tbody>
              </table>
            </div>


          </div>

          <!-- /.box-body -->

        </div>

        <!-- /.box -->

      </div>

      <!-- ./col -->

      <!-- ./col -->

    </div>


    @endsection



    @section('scripts')

      <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>

      <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

      <script>

          $(function () {

              $("#task").DataTable();


              $('#delete').on('show.bs.modal', function (event) {

                  var button = $(event.relatedTarget)

                  var mid = button.data('mid')

                  var modal = $(this)

                  modal.find('.modal-body #m_id').val(mid);

              })

          });

      </script>



@endsection