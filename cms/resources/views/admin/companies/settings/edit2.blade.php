@extends('layouts.company')

@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="{{asset('assets/plugins/timepicker/bootstrap-timepicker.css')}}">
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
        <h3 class="box-title">Site Information</h3>

        <div class="box-tools pull-right">
          <div class="col-md-7 page-action text-right">

          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-6">
            <span>Plan Used: </span><b>{{ $plan->name }}</b><br>
            <span>Number of User Allowed: </span>{{ $plan->users }}<br>
            <span>Number of Active Users: </span>{{ $active_users }}<br>
            <span>Number of Inactive Users: </span>{{ $inactive_users }}<br>
            <span>Number of Archived Users: </span>{{ $archived_users }}<br>
            <span>
                        @if($plan->users - ($active_users+$inactive_users) <= 0 )
                You have reached maximum number of users
              @else
                You have {{$plan->users - ($active_users+$inactive_users) }} users left
              @endif
                    </span>
          </div>
          <br><br>
          <div class="col-md-6">
            <label style="font-size: 20px;">Note: </label><br>
            <span>Fields with <span style="color: red">*</span> are required</span><br>
            <span>Fields with <span style="color: green">*</span> can be set up only once</span><br>
          </div>
        </div>

      {!! Form::model($setting, array('url' => url(domain_route('company.admin.clientsetting.update',[$setting->id])) , 'method' => 'PATCH', 'files'=> true)) !!}
      @include('company.settings._form')
      <!-- Submit Form Button -->
        {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
        {!! Form::close() !!}

      </div>
    </div>

  </section>


@endsection

@section('scripts')

  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <!-- bootstrap time picker -->
  <script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
  <script>
      $('.timepicker').timepicker({
          showInputs: false,
          showMeridian: false,
      });

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
                      url: '/clients/states/get/' + countryId,
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
                      url: '/clients/cities/get/' + stateId,
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

          var i = 1;

          $('#add').click(function () {
              i++;
              $('#dynamic_field').append('<tr id="row' + i + '"><td><input name="tax_name[]" id="tax_name' + i + '" class="form-control"></td><td><input name="tax_percent[]" id="tax_percent' + i + '" class="form-control"></td><td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>');
              $("#tax_percent" + i).keydown(function (e) {
                  // Allow: backspace, delete, tab, escape, enter and .
                  if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                      // Allow: Ctrl+A, Command+A
                      (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                      // Allow: home, end, left, right, down, up
                      (e.keyCode >= 35 && e.keyCode <= 40)) {
                      // let it happen, don't do anything
                      return;
                  }
                  // Ensure that it is a number and stop the keypress
                  if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                      e.preventDefault();
                  }
              });
          });
          $(document).on('click', '.btn_remove', function () {
              var button_id = $(this).attr("id");
              $('#row' + button_id + '').remove();
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


      function removeTax(tax_id) {
          var csrf_token = "{{ csrf_token() }}";
          var tax_url = "{{URL::to('admin/setting/removeTax')}}";

          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              url: tax_url,
              data: {"tax_id": tax_id},
              success: function (data) {
                  $("#showTaxes").load(" #showTaxes");
              }
          });
      }

      $(document).on('change', '#default_currency', function () {
          var symbol = $('option:selected', this).attr('symbol');
          $('#currency_symbol').val(symbol);
      });
  </script>

@endsection