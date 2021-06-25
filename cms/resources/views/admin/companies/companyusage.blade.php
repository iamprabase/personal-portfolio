@extends('layouts.app')

@section('title', 'Companies Usage')

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

      </div><br />

      @endif

      <div class="box">

        <div class="box-header">

          <h3 class="box-title">Company Usage</h3>
{{-- 
          <a href="{{ route('app.company.create') }}" class="btn btn-primary pull-right" style="margin-right: 5px;">

            <i class="fa fa-plus"></i> Create New

          </a> --}}

          <span id="companyexports" class="pull-right"></span>

        </div>

        <!-- /.box-header -->

        <div class="box-body table-responsive">

          <table id="company" class="table table-bordered table-striped">

            <thead>

              @if( !$companies->isEmpty() )

              <tr>

                <th>#</th>

                <th>Company Name</th>

                <th>Active Users</th>

                <th>Last Activity Time</th>

                <th>Users who used app in Last 3 days</th>

                <th>Users who used app in Last 7 days</th>

              </tr>

            </thead>

            <tbody>

              @php($i = 0)

              @foreach($companies as $company)

              @php($i++)

              <tr>

                <td>{{ $i }}</td>

                <td>{{$company->company_name}}</td>

                <td>{{$company->active_users}}</td>

                <td>{{$company->last_activity_time}}</td>

                <td>{{$company->no_of_active_in_th_days}}</td>

                <td>{{$company->no_of_active_in_sv_days}}</td>

              </tr>

              @endforeach

            </tbody>

            @else

            <tr>

              <td colspan="10">No Record Found.</td>

            </tr>

            @endif

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

<div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

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

          <input type="hidden" name="company_id" id="c_id" value="">


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
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables-buttons/buttons.flash.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

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

          var table = $('#company').DataTable({

              //lengthChange: false,

              buttons: ['excel', 'pdf', 'print']

          });


          table.buttons().container()

              .appendTo('#companyexports');

      });


</script>



@endsection