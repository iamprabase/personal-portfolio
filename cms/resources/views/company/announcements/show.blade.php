@extends('layouts.company')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
@endsection
@section('content')
  <section class="content">
    <div class="row">
      <div class="col-md-offset-2 col-md-8">
        <div class="box box-default">
          <div class="box-header with-border">

            <h3 class="box-title">About {{ ($announcement->title)?$announcement->title:'NA' }}</h3>

          </div>

          <!-- /.box-header -->

          <div class="box-body">

            <strong><i class="fa fa-book margin-r-5"></i> Description:</strong>
            <p class="text-muted">
              {{ ($announcement->description)?strip_tags($announcement->description):'NA' }}

            </p>
            <ul class="list-group list-group-unbordered">

              <li class="list-group-item">

                <b>Created On</b> <span
                    class="pull-right">{{ ($announcement->created_at)?$announcement->created_at:'NA' }}</span>

              </li>

              <li class="list-group-item">

                <b>Status</b> <a class="pull-right">

                  @if($announcement->status =='1')

                    <span class="label label-success">Active</span>

                  @elseif($announcement->status =='0')

                    <span class="label label-warning">Inactive</span>

                  @endif

                </a>

              </li>
              <li class="list-group-item">
                <b>Announcement forwarded to</b>
                @foreach($employees as $employee)
                  <span class="pull-right">{{getEmployee($employee->employee_id)['name']}}</span><br>
                @endforeach
              </li>

            </ul>
          <!--<div class="table-responsive">
                <table class="table table-bordered table-striped"> <colgroup> <col class="col-xs-2"> <col class="col-xs-7"> </colgroup> 
                 <tbody> 
                  <tr> <th scope="row"> Created On </th> <td>{{ ($announcement->created_at)?$announcement->created_at:'NA' }}</td> </tr> 
                  <tr> <th scope="row"> Status </th> <td><span class="pull-right">

                     @if($announcement->status =='1')

            <span class="label label-success">Active</span>

@elseif($announcement->status =='0')

            <span class="label label-warning">Inactive</span>

@endif
              </span></td> </tr>
             <tr> <th scope="row"> Announcement forwarded to</th>
               <td>@foreach($employees as $employee)
            <span class="">{{getEmployee($employee->employee_id)['name']}}</span><br>
                    @endforeach</td> </tr> 
                   </tbody> 
                </table> 
              </div>-->



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


              $("#announcement").DataTable();



              $('#delete').on('show.bs.modal', function (event) {

                  var button = $(event.relatedTarget)


                  var mid = button.data('mid')


                  var modal = $(this)


                  modal.find('.modal-body #m_id').val(mid);


              })

          });


      </script>

@endsection