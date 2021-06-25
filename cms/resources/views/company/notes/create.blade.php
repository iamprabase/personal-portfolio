@extends('layouts.company')
@section('title', 'Create Note')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
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

    .datepicker{
      z-index:9999!important;
    }

    .imgAdd{
      margin-left: 10px;
    }

  </style>

@endsection

@section('content')
  <section class="content">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Create Note</h3>
        <div class="box-tools pull-right">
          <div class="col-xs-7 page-action text-right">
            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i
                  class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <div class="box-body">
      {!! Form::open(array('url' => url(domain_route("company.admin.notes.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true)) !!}
      @include('company.notes._form')
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
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script>
  $(function () {
    $('.select2').select2();
    $('#note_date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      endDate: new Date(),
    });

    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });

    $('#assigned_to').on('change',function(e){
      e.preventDefault();
      var employee_id = $(this).val();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{domain_route('company.admin.employee.getEmployeeHandles')}}",
        type: "POST",
        data: {
          "employee_id":employee_id,
        },
        beforeSend: function () {
        },
        success: function (data) {
                  var j=0;
                  $("#client_id").empty();
                  $.each(data, function(){
                    $('<option></option>').val(data[j]['clientID']).text(data[j]['company_name']).appendTo('#client_id');
                    j++;
                  });   
                  $('.select2').select2();
                },
                error: function (xhr) {
                },
                complete: function () {
                }
              });
    });

  });

  $(".imgAdd").click(function(){
    var Imgcount = $("#imggroup .imgUp").length;
    if(Imgcount < 4){
      if(Imgcount == 3){
        $(".imgAdd").hide();
      }
      $(this).closest(".row").find('.imgAdd').before('<div class="col-xs-3 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="receipt[]" type="file" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fa fa-times del"></i></div>');          
    }else{
      $(".imgAdd").hide();
    }
  });
  $(document).on("click", "i.del" , function() {
    var Imgcount = $("#imggroup .imgUp").length;
    if(Imgcount<5){
      $(".imgAdd").show();
    }
    $(this).parent().remove();
  });
  $(function() {
    $(document).on("change",".uploadFile", function()
    {
      var uploadFile = $(this);
      var files = !!this.files ? this.files : [];
            if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

            if (/^image/.test( files[0].type)){ // only image file
                var reader = new FileReader(); // instance of the FileReader
                reader.readAsDataURL(files[0]); // read the local file

                reader.onloadend = function(){ // set image data as background of div
                  uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url("+this.result+")");
                }
              }

            });
  });
</script>



@endsection