@extends('layouts.company')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
@endsection
@section('content')
  <section class="content">
    <!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Company Information</h3>
        <div class="box-tools pull-right">
          <div class="col-md-7 page-action text-right">
            <a href="{{ domain_route('company.admin.announcement') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
      {!! Form::model($announcement, array('url' => url(domain_route('company.admin.announcement.update',[$announcement->id])) , 'method' => 'PATCH', 'files'=> true)) !!}
      @include('company.announcements._form')
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
          $('#dob').datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
          });
          $('#doj').datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
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
      var employee_list = new Array();

      var new_entry = new String;

      $(document).ready(function () {
          $('#previous_list > li').each(function () {
              var employee_id = $(this).attr('emp_id');
              employee_list.push(employee_id);
          });
      });

      $(document).on('change', '#employees', function () {

          var employee_id = $('option:selected', this).val();

          if (employee_id == 0) {

              $("#employee_list").empty();

              $("#employees > option").each(function () {

                  if ($(this).val() > 0) {

                      new_entry = "<li id='emp" + $(this).val() + "'><input type='hidden' name='employee_id[]' value='" + $(this).val() + "'>" + $(this).text() + "<a class='btn btn-danger btn-xs pull-right' style='height:18px;' onclick='popEmployee(" + $(this).val() + ")'>X</a></li>";

                      employee_list.push($(this).val());

                      $("#employee_list").append(new_entry);

                  }

              });

          }

          if (employee_list.includes(employee_id) == false && employee_id > 0) {

              employee_list.push(employee_id);

              var employee_name = $('option:selected', this).attr('emp_name');

              new_entry = "<li id='emp" + employee_id + "'><input type='hidden' name='employee_id[]' value='" + employee_id + "'>" + employee_name + "<a class='btn btn-danger btn-xs pull-right' style='height:18px;' onclick='popEmployee(" + $(this).val() + ")'>X</a></li>";

              $("#employee_list").append(new_entry);

          }

      });

      function popEmployee(id) {

          employee_list = jQuery.grep(employee_list, function (value) {
              return value != id;
          });

          $('#emp' + id).empty();

          $('#emp' + id).remove();

      }

  </script>
@endsection