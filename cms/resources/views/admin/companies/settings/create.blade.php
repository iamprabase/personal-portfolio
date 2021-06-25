@extends('layouts.master')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <style>
    .icheckbox_minimal-blue {
      margin-top: -2px;
      margin-right: 3px;
    }

    .checkbox label, .radio label {
      font-weight: bold;
    }

    .has-error {
      color: red;
    }
  </style>
@endsection

@section('content')
  <section class="content">

    <!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Company Information</h3>

        <div class="box-tools pull-right">
          <div class="col-md-7 page-action text-right">
            <a href="{{ route('user') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">

      {!! Form::open(['route' => ['user.store'] ]) !!}
      @include('superadmins._form')
      <!-- Submit Form Button -->
        {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right']) !!}
        {!! Form::close() !!}

      </div>
    </div>

  </section>


@endsection

@section('scripts')

  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
  
  <script>
      $(function () {
          $('#startdate').datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
          });
          $('#enddate').datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
          });
          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
              checkboxClass: 'icheckbox_minimal-blue',
              radioClass: 'iradio_minimal-blue'
          });
          CKEDITOR.replace('about');
      });
  </script>

@endsection