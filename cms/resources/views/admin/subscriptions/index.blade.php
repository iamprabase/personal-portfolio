@extends('layouts.app')
@section('title', 'Subscriptions')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
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
        @if (\Session::has('error'))
          <div class="alert alert-error">
            <p>{{ \Session::get('error') }}</p>
          </div><br/>
        @endif
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Subscription</h3>
            <a href="{{ route('app.subscription.create') }}" class="btn btn-primary pull-right" style="margin-left: 5px;">
              <i class="fa fa-plus"></i> Create New
            </a>
            <span id="customSubscriptionExports" class="pull-right"></span>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="customSubscription" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Desc.</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
              
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
  <div class="modal modal-default fade" id="delete" tabindex="-1" plan="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" plan="document">
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
            <input type="hidden" name="plan_id" id="m_id" value="">

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button>
            <button type="submit" class="btn btn-warning">Yes, Delete</button>
          </div>
        </form>
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
      });

      $(document).ready(function () {
          var table = $('#customSubscription').DataTable({
              //lengthChange: false,
              buttons: ['excel', 'pdf', 'print']
          });

          table.buttons().container()
              .appendTo('#customSubscriptionExports');
      });

  </script>

@endsection