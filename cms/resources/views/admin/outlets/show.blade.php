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
          <h3 class="box-title">About Outlet</h3>
          <h3 class="box-title pull-right" style="color:#f16022">
          </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          @if($image_path)
            <img class="profile-user-img img-responsive img-circle" src="{{ $image_path }}" alt="User profile picture">
          @else
            <img src="{{asset('assets/dist/img/avatar5.png')}}" class="profile-user-img img-responsive img-circle" alt="User profile picture" >
          @endif

          {{-- <strong><i class="fa fa-book margin-r-5"></i> About Outlet:</strong> --}}

          {{-- <p class="text-muted">
            {{ ($company->aboutCompany)?strip_tags($company->aboutCompany):'NA' }}
          </p> --}}

          <ul class="list-group list-group-unbordered">
            <li class="list-group-item">
              <b>Outlet Name</b> <a class="pull-right">{{ ($outlet->outlet_name)?$outlet->outlet_name:'NA' }}</a>
            </li>

            <li class="list-group-item">
              <b>Number of Suppliers Connected</b> <a class="pull-right">{{ $count_connected_suppliers}}</a>
            </li>

            <li class="list-group-item">
              <b> Suppliers Connected</b> <a class="pull-right">{{ $connected_suppliers}}</a>
            </li>
            
            <li class="list-group-item">
              <b>Contact Person</b> <a class="pull-right">{{ ($outlet->contact_person)?$outlet->contact_person:'NA' }}</a>
            </li>

            <li class="list-group-item">
              <b>Secret Code</b> <a class="pull-right">{{ ($outlet->unique_code)?$outlet->unique_code:'NA' }}</a>
            </li>

            <li class="list-group-item">
              <b>Contact Email</b> <a
                class="pull-right">{{ ($outlet->email)?$outlet->email:'NA' }}</a>
            </li>
            
            <li class="list-group-item">
              <b>Phone No.</b> <a class="pull-right">{{ ($outlet->phone)?$outlet->phone_ext.'-'.$outlet->phone:'NA' }}</a>
            </li>

            <li class="list-group-item">
              <b>Country</b> <a class="pull-right">{{ $country_name }}</a>
            </li>

            <li class="list-group-item">
              <b>State</b> <a class="pull-right">{{ $state }}</a>
            </li>

            <li class="list-group-item">
              <b>City</b> <a class="pull-right">{{ $city_name }}</a>
            </li>

            <li class="list-group-item">
              <b>GPS Address</b> <a class="pull-right">{{ ($outlet->gps_location)?$outlet->gps_location:'NA' }}</a>
            </li>

            <li class="list-group-item">
              <b>Address</b> <a class="pull-right">{{ ($outlet->address)?$outlet->address:'NA' }}</a>
            </li>

            <li class="list-group-item">
              <b>Status</b> <a class="pull-right">{{ ($outlet->status)?$outlet->status:'NA' }}</a>
            </li>

            <li class="list-group-item">
              <b>Registered Date</b> <a class="pull-right">{{ ($outlet->registered_date)?date('d M Y', strtotime($outlet->registered_date)):'NA' }}</a>
            </li>
            {{-- <li class="list-group-item">
              <b>Mobile No.</b> <a class="pull-right">{{ ($company->mobile)?$company->mobile:'NA' }}</a>
            </li> --}}
            {{-- <li class="list-group-item">
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
            </li> --}}
            {{-- <li class="list-group-item">
                <b>ID</b> <a class="pull-right">{{ $company->id }}</a>
            </li>
            <li class="list-group-item">
              <b>Token</b> <a class="pull-right">{{ $company->verify_token }}</a>
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