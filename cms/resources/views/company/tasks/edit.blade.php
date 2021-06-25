@extends('layouts.company')
@section('title', 'Edit Task')
@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  <style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 22px;
    }

    .select2-container .select2-selection--single {
      height: 40px;
      padding: 12px 5px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
      margin-top: 3px;
    }

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
        <h3 class="box-title">Tasks Information</h3>

        <div class="box-tools pull-right">
          <div class="col-md-7 page-action text-right">
            <a href="{{ domain_route('company.admin.task') }}" class="btn btn-default btn-sm"> <i
                  class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">

      {!! Form::model($task, array('url' => url(domain_route('company.admin.task.update',[$task->id])) , 'method' => 'PATCH', 'files'=> true)) !!}
      @include('company.tasks._form')
      <!-- Submit Form Button -->
        {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
        {!! Form::close() !!}

      </div>
    </div>

  </section>


@endsection

@section('scripts')

  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  
  
  <script>
      $(function () {
          $('.select2').select2();
          $('#due_date').datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
              startDate: '-0d',
          });
          $("#start_date").datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
              startDate: '-0d',
          }).on('changeDate', function (selected) {
              var startDate = new Date(selected.date.valueOf());
              $('#end_date').datepicker('setStartDate', startDate);
          }).on('clearDate', function (selected) {
              $('#end_date').datepicker('setStartDate', null);
          });

          $("#end_date").datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
              startDate: '-0d',
          }).on('changeDate', function (selected) {
              var endDate = new Date(selected.date.valueOf());
              $('#start_date').datepicker('setEndDate', endDate);
          }).on('clearDate', function (selected) {
              $('#start_date').datepicker('setEndDate', null);
          });
          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
              checkboxClass: 'icheckbox_minimal-blue',
              radioClass: 'iradio_minimal-blue'
          });
          CKEDITOR.replace('about');
      });

      $(document).on('change', '.btn-file :file', function () {
          var input = $(this),
              label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
          input.trigger('fileselect', [label]);
      });

      $('.btn-file :file').on('fileselect', function (event, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = label;

          if (input.length) {
              input.val(log);
          } else {
              if (log) alert(log);
          }

      });

      function readURL(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();

              reader.onload = function (e) {
                  $('#img-upload').attr('src', e.target.result);
              }

              reader.readAsDataURL(input.files[0]);
          }
      }

      $("#imgInp").change(function () {
          readURL(this);
      });
  </script>

@endsection