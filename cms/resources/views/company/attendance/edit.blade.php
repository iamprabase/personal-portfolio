@extends('layouts.company')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}">

@endsection

@section('content')
  <section class="content">

    <!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Attendance Information</h3>

        <div class="box-tools pull-right">
          <div class="col-md-7 page-action text-right">
            <a href="{{ domain_route('company.admin.attendance') }}" class="btn btn-default btn-sm"> <i
                  class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">

      {!! Form::model($attendance, array('url' => url(domain_route('company.admin.attendance.update',[$attendance->id])) , 'method' => 'PATCH', 'files'=> true)) !!}
      @include('company.attendance._form')
      <!-- Submit Form Button -->
        {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
        {!! Form::close() !!}

      </div>
    </div>

  </section>


@endsection

@section('scripts')
  <script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
  
  <script>
      $(function () {
          $('#adate').datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
          });

          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
              checkboxClass: 'icheckbox_minimal-blue',
              radioClass: 'iradio_minimal-blue'
          });

          //CKEDITOR.replace('companydesc');

          $('#checkin').timepicker({
              showInputs: false,
              showMeridian: false
          })
          $('#checkout').timepicker({
              showInputs: false
          })
      });
  </script>

@endsection