@extends('layouts.app')@section('stylesheets')  <link rel="stylesheet"        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">  <style>    .icheckbox_minimal-blue {      margin-top: -2px;      margin-right: 3px;    }    .checkbox label, .radio label {      font-weight: bold;    }    .has-error {      color: red;    }  </style>@endsection@section('content')  <section class="content">    <!-- SELECT2 EXAMPLE -->    <div class="box box-default">      <div class="box-header with-border">        <h3 class="box-title">Edit Plan</h3>        <div class="box-tools pull-right">          <div class="col-md-7 page-action text-right">            <a href="{{ route('app.plan') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>          </div>        </div>      </div>      <!-- /.box-header -->      <div class="box-body">      {!! Form::model($plan, ['method' => 'PATCH', 'files'=> true , 'route' => ['app.plan.update',  $plan->id ] ]) !!}      @include('plans._form')      <!-- Submit Form Button -->        {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}        {!! Form::close() !!}      </div>    </div>  </section>@endsection@section('scripts')  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>  <script>      $(function () {          $('#dob').datepicker({              format: 'yyyy-mm-dd',              autoclose: true,          });          $('#doj').datepicker({              format: 'yyyy-mm-dd',              autoclose: true,          });          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({              checkboxClass: 'icheckbox_minimal-blue',              radioClass: 'iradio_minimal-blue'          });          CKEDITOR.replace('description');      });      $(document).on('change', '.btn-file :file', function () {          var input = $(this),              label = input.val().replace(/\\/g, '/').replace(/.*\//, '');          input.trigger('fileselect', [label]);      });      $('.btn-file :file').on('fileselect', function (event, label) {          var input = $(this).parents('.input-group').find(':text'),              log = label;          if (input.length) {              input.val(log);          } else {              if (log) alert(log);          }      });      function readURL(input) {          if (input.files && input.files[0]) {              var reader = new FileReader();              reader.onload = function (e) {                  $('#img-upload').attr('src', e.target.result);              }              reader.readAsDataURL(input.files[0]);          }      }      $("#imgInp").change(function () {          readURL(this);      });  </script>@endsection