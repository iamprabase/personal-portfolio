@extends('layouts.app')
@section('title', 'General Settings')
@section('stylesheets')
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
    .layoutLogo {
            width: 275px;
        }
        .layoutfavicon {
            width: 50px;
        }
  </style>
@endsection

@section('content')
  <section class="content">

    <!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Site Information</h3>

        <div class="box-tools pull-right">
          <div class="col-md-7 page-action text-right">

          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">

      {!! Form::model($setting, ['method' => 'PATCH', 'files'=> true , 'route' => ['app.setting.update',  $setting->id ] ]) !!}
      @include('admin.settings._form')
      <!-- Submit Form Button -->
        {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
        {!! Form::close() !!}

      </div>
    </div>

  </section>


@endsection

@section('scripts')

  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <script>
      $(function () {
          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
              checkboxClass: 'icheckbox_minimal-blue',
              radioClass: 'iradio_minimal-blue'
          });
      });

      $(document).ready(function () {
          $('select[name="country"]').on('change', function () {
              var countryId = $(this).val();
              //alert(countryId);
              if (countryId) {
                  $.ajax({
                      url: '/setting/states/get/' + countryId,
                      type: "GET",
                      dataType: "json",
                      success: function (data) {

                          //alert('hi');

                          $("#state").empty();
                          $('#city').empty();
                          $("#city").append('<option>--City--</option>');
                          $("#state").append('<option>Select State</option>');

                          $.each(data, function (key, value) {

                              $("#state").append('<option value="' + key + '">' + value + '</option>');

                          });
                      }

                  });
              } else {
                  $('#state').empty();
                  $('#city').empty();
              }

          });

          $('select[name="state"]').on('change', function () {
              var stateId = $(this).val();
              // alert(stateId);
              if (stateId) {
                  $.ajax({
                      url: '/setting/cities/get/' + stateId,
                      type: "GET",
                      dataType: "json",
                      success: function (data) {

                          //alert('hi');

                          $("#city").empty();
                          $("#city").append('<option>Select City</option>');

                          $.each(data, function (key, value) {

                              $("#city").append('<option value="' + key + '">' + value + '</option>');

                          });
                      }
                  });
              } else {

                  $('#city').empty();
              }

          });
      });

      $(document).on('change', '.logofile :file', function () {
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

      $(document).on('change', '.smalllogofile :file', function () {
          var input = $(this),
              label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
          input.trigger('fileselect', [label]);
      });

      $('.smalllogofile :file').on('fileselect', function (event, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = label;

          if (input.length) {
              input.val(log);
          } else {
              if (log) alert(log);
          }

      });

      function readURL1(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();

              reader.onload = function (e) {
                  $('#img-upload1').attr('src', e.target.result);
              }

              reader.readAsDataURL(input.files[0]);
          }
      }

      $("#imgInp1").change(function () {
          readURL1(this);
      });


      $(document).on('change', '.favicon :file', function () {
          var input = $(this),
              label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
          input.trigger('fileselect', [label]);
      });

      $('.favicon :file').on('fileselect', function (event, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = label;

          if (input.length) {
              input.val(log);
          } else {
              if (log) alert(log);
          }

      });

      function readURL2(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();

              reader.onload = function (e) {
                  $('#img-upload2').attr('src', e.target.result);
              }

              reader.readAsDataURL(input.files[0]);
          }
      }

      $("#imgInp2").change(function () {
          readURL2(this);
      });

  </script>

@endsection