@extends('layouts.company')

@section('title', 'Meetings')

@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-md-offset-2 col-md-8">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Meeting Details</h3>

          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Employee Name</b>
                <span class="pull-right">
                  @if($meeting->employee_id == 0)
                    {{Auth::user()->name.' (Admin)'}}
                  @else
                    {{ getEmployee($meeting->employee_id)['name']}}
                  @endif
                  </span>
              </li>

              <li class="list-group-item">
                <b>Party Name</b><span
                    class="pull-right">{{ isset($meeting->client_id)? getClient($meeting->client_id)['company_name']:null }}</span>
              </li>

              <li class="list-group-item">
                <b>Meeting Date</b><span
                    class="pull-right">{{ isset($meeting->meetingdate)? date('d M Y', strtotime($meeting->meetingdate)):null }}</span>
              </li>

              <li class="list-group-item">
                <b>Check In Time</b><span
                    class="pull-right">{{ isset($meeting->checkintime)? $meeting->checkintime:null }}</span>
              </li>

              <li class="list-group-item">
                <b>Remark</b> <span class="pull-right">{{ isset($meeting->remark)? $meeting->remark:'NA' }}</span>
              </li>

              {{-- <li class="list-group-item">
                <b>Picture</b>
                @foreach($images as $image)
                  @if(isset($image->image_path))
                    <img class="img-responsive" @if(isset($image->image_path)) src="{{ URL::asset('cms'.$image->image_path) }}" @endif alt="Picture Displays here" style="max-height: 500px;"/>
                  @else
                    <span class="pull-right">N/A</span>
                  @endif
                @endforeach
              </li> --}}
            </ul>

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
              $("#meeting").DataTable();

              $('#delete').on('show.bs.modal', function (event) {
                  var button = $(event.relatedTarget)
                  var mid = button.data('mid')
                  var modal = $(this)
                  modal.find('.modal-body #m_id').val(mid);
              })
          });
      </script>

@endsection