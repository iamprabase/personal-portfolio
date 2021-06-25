@extends('layouts.app')

@section('title', 'View Company')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-offset-2 col-xs-8">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">About Company</h3>
            <h3 class="box-title pull-right" style="color:#f16022">
              Plan: <!-- {{ $company->plans->implode('name', ', ') }} --></h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <img class="profile-user-img img-responsive img-circle" src="{{ asset('assets/dist/img/avatar5.png') }}"
                 alt="User profile picture">

            <strong><i class="fa fa-book margin-r-5"></i> About Company:</strong>

            <p class="text-muted">
              {{ ($company->aboutCompany)?strip_tags($company->aboutCompany):'NA' }}
            </p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Company Name</b> <a class="pull-right">{{ ($company->company_name)?$company->company_name:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Contact Email</b> <a
                    class="pull-right">{{ ($company->contact_email)?$company->contact_email:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Phone No.</b> <a class="pull-right">{{ ($company->contact_phone)?$company->contact_phone:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Mobile No.</b> <a class="pull-right">{{ ($company->mobile)?$company->mobile:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Fax</b> <a class="pull-right">{{ ($company->fax)?$company->fax:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>PAN/VAT</b> <a class="pull-right">{{ ($company->pan)?$company->pan:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>White Label</b> <a class="pull-right">{{ ($company->whitelabel)?$company->whitelabel:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Customization</b> <a class="pull-right">{{ ($company->customize)?$company->customize:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Validity</b> <a
                    class="pull-right">{{ ($company->start_date and $company->end_date)?date('d M Y', strtotime($company->start_date)).' TO '.date('d M Y', strtotime($company->end_date)):'NA' }}</a>
              </li>
              {{-- <li class="list-group-item">
                <b>ID</b> <a class="pull-right">{{ $company->id }}</a>
              </li>
              <li class="list-group-item">
                <b>Token</b> <a class="pull-right">{{ $company->verify_token }}</a>
              </li> --}}
              <li class="list-group-item">
                <b>Users</b><a class="pull-right">
                {{ $company->num_users}} {{ str_plural('User',$company->num_users)}}
              </li>
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
              $("#company").DataTable();

              $('#delete').on('show.bs.modal', function (event) {
                  var button = $(event.relatedTarget)
                  var mid = button.data('mid')
                  var modal = $(this)
                  modal.find('.modal-body #m_id').val(mid);
              })
          });
      </script>

@endsection