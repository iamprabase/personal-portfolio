@extends('layouts.app')
@section('title', 'Show Group')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <!-- ./col -->
      <div class="col-md-12 ">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">Companies with Plan {{ $plan->name }}</h3>

            <div class="box-tools pull-right">
              <div class="col-md-7 page-action text-right">
                <a href="{{ route('plan') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
              </div>
            </div>
          </div>
          <div class="box-body box-profile">

            Will Display all the companies in this plan. Coming Soon...
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- ./col -->
    </div>

    @endsection

    @section('scripts')
      <script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
      <script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
      <script>
          $(function () {
              $("#member").DataTable();

              $('#delete').on('show.bs.modal', function (event) {
                  var button = $(event.relatedTarget)
                  var mid = button.data('mid')
                  var modal = $(this)
                  modal.find('.modal-body #m_id').val(mid);
              })
          });
      </script>

@endsection