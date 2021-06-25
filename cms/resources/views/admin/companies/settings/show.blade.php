@extends('layouts.app')

@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-6">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">About Company</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">

            <strong><i class="fa fa-book margin-r-5"></i> About Company:</strong>

            <p class="text-muted">
              {{ ($member->c_about)?$member->c_about:'NA' }}
            </p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Name</b> <a class="pull-right">{{ ($member->c_name)?$member->c_name:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Email</b> <a class="pull-right">{{ ($member->email)?$member->email:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Contact No.</b> <a class="pull-right">{{ ($member->phone)?$member->phone:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Fax</b> <a class="pull-right">{{ ($member->fax)?$member->fax:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>PAN/VAT</b> <a class="pull-right">{{ ($member->pan)?$member->pan:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>White Label</b> <a class="pull-right">{{ ($member->whitelabel)?$member->whitelabel:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Customization</b> <a class="pull-right">{{ ($member->customize)?$member->customize:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Validity</b> <a
                    class="pull-right">{{ ($member->startdate and $member->enddate)?date('d M Y', strtotime($member->startdate)).' TO '.date('d M Y', strtotime($member->enddate)):'NA' }}</a>
              </li>
            </ul>

          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- ./col -->
      <div class="col-xs-6">
        <div class="box box-primary">
          <div class="box-body box-profile">
            <img class="profile-user-img img-responsive img-circle" src="{{ asset('assets/dist/img/avatar5.png') }}"
                 alt="User profile picture">

            <h3 class="profile-username text-center">{{ ($member->c_p_name)?$member->c_p_name:'' }}</h3>

            <p class="text-muted text-center">{{ ($member->c_p_designation)?$member->c_p_designation:'' }}</p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Email</b> <a class="pull-right">{{ ($member->c_p_email)?$member->c_p_email:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Contact No.</b> <a class="pull-right">{{ ($member->c_p_mobile)?$member->c_p_mobile:'NA' }}</a>
              </li>
              <li class="list-group-item">
                <b>Extention No.</b> <a class="pull-right">{{ ($member->ext_no)?$member->ext_no:'NA' }}</a>
              </li>
            </ul>

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