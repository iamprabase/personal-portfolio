@extends('layouts.company')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}">

@endsection

@section('content')
  <section class="content">

    <!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Create Attendance</h3>

        <div class="box-tools pull-right">
          <div class="col-md-7 page-action text-right">
            <a href="{{ domain_route('company.admin.attendance') }}" class="btn btn-default btn-sm"> <i
                  class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
      {!! Form::open(array('url' => url(domain_route("company.admin.attendance.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true)) !!}
      @include('company.attendance._form')
      <!-- Submit Form Button -->
        {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right']) !!}
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
      });

      var count = 1;
      $('#checkin' + count).timepicker({
          showInputs: false,
          showMeridian: false,
      });
      $('#checkout' + count).timepicker({
          showInputs: false,
          showMeridian: false
      });
      var filled = false;
      $(document).on('click', '#addmore', function () {
          $(".time").each(function () {
              if ($(this).val()) {
                  filled = true;
              } else {
                  filled = false;
                  return filled;
              }
          });
          if (filled) {
              count++;
              check_in_input = "<br class='breaker" + count + "'><div class='input-group bootstrap-timepicker' id='check_in_div" + count + "'><input type='text' name='check_in[]' id='checkin" + count + "' class='form-control time' placeholder='Check In Time'  autocomplete='off' required><div class='input-group-addon'><i class='fa fa-clock-o'></i></div></div>";
              check_out_input = "<br class='breaker" + count + "'><div class='input-group bootstrap-timepicker' id='check_out_div" + count + "'><input type='text' name='check_out[]' id='checkout" + count + "' class='form-control time' placeholder='Check Out Time' autocomplete='off' required><div class='input-group-addon'><i class='fa fa-clock-o'></i></div></div>";
              dismiss = "<button type='button' id='remove" + count + "' value='" + count + "' class='btn btn-danger btn_remove'style='margin-top: 20px;'>Remove</button>";
              $("#check_in_time").append(check_in_input);
              $("#check_out_time").append(check_out_input);
              $("#action_button").append(dismiss);
              $('#checkin' + count).timepicker({
                  showInputs: false,
                  showMeridian: false,
              });
              $('#checkout' + count).timepicker({
                  showInputs: false,
                  showMeridian: false,
              });
          } else {
              alert('Please fill the Check In/Check Out time first');
          }
      });
      $(document).on('click', '.btn_remove', function () {
          var i = $(this).val();
          $("#check_in_div" + i).remove();
          $("#check_out_div" + i).remove();
          $(".breaker" + i).remove();
          $("#remove" + i).remove();
      });
  </script>

@endsection