@extends('layouts.company')
@section('title', 'Edit Expense')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<style>
  .datepicker-orient-top{
    z-index: 9999!important;
  }
</style>
@endif
@endsection
@section('content')
<section class="content">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Expense Information</h3>
      <div class="box-tools pull-right">
        <div class="col-md-7 page-action text-right">
          <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
        </div>
      </div>
    </div>
    <div class="box-body">
      {!! Form::model($expense, array('url' => url(domain_route('company.admin.expense.update',[$expense->id])) , 'method' => 'PATCH', 'files'=> true)) !!}
      @include('company.expenses._form')
      {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
      {!! Form::close() !!}
    </div>
  </div>
</section>
@endsection
@section('scripts')
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
<script>
  $('.DT_Exp_FILTER').val(sessionStorage.getItem('DT_Exp_filters'));
  @if(config('settings.ncal')==0)
    $('.expenseDate').datepicker({
      format:'yyyy-mm-dd',
      autoclose:true,
      forceParse: false,
      startDate: moment().subtract(30,'days').format('YYYY-MM-DD'),
      endDate: moment().format('YYYY-MM-DD'),
    });
  @else
    function getNewNepaliFormat(date){
      date = date.split("-");
      date = date[1]+'/'+date[2]+'/'+date[0];
      return date;
    }
    var ntoday = getNewNepaliFormat(AD2BS(moment().subtract(1,'days').format('YYYY-MM-DD')));
    var n30daysAgo = getNewNepaliFormat(AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD')));

    $('#nexp_date').nepaliDatePicker({
      disableBefore: n30daysAgo,
      disableAfter: ntoday,
      onChange:function(){
        $('#expenseDate').val(BS2AD($('#nexp_date').val()));
        console.log($('#expenseDate').val());
      }
    });
  @endif

  $('.select2').select2();

  // $(document).on('change', '.logofile :file', function () {
  //   var input = $(this),
  //   label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
  //   input.trigger('fileselect', [label]);
  // });

  // $('.btn-file :file').on('fileselect', function (event, label) {
  //   var input = $(this).parents('.input-group').find(':text'),
  //   log = label;
  //   if (input.length) {
  //     input.val(log);
  //   } else {
  //     if (log) alert(log);
  //   }
  // });

  // function readURL(input) {
  //   if (input.files && input.files[0]) {
  //     var reader = new FileReader();
  //     reader.onload = function (e) {
  //       $('#img-upload').attr('src', e.target.result);
  //     }
  //     reader.readAsDataURL(input.files[0]);
  //   }
  // }
  // $("#imgInp").change(function () {
  //   readURL(this);
  // });

  // $('#expense_photo').change(function () {
  //   $('#images').empty();
  //   var files = $(this)[0].files;
  //   if (files.length > 3 - {{$image_count}}) {
  //     alert("You can upload max 3 images. "+{{$image_count}}+" already uploaded");
  //     $("#expense_photo").val('');
  //   } else if (files.length <= 3 - {{$image_count}}) {
  //     var total_file = document.getElementById("expense_photo").files.length;
  //     for (var i = 0; i < total_file; i++) {
  //       $('#images').append("<img src='" + URL.createObjectURL(event.target.files[i]) + "' alt='Picture Displays here' style='max-height: 100px;'>&emsp;");
  //     }
  //   }
  // });
  var Imgcount = $("#imggroup .imgUp").length;
  if(Imgcount >= 3){
    $('.imgAdd').hide();
  }
  $(".imgAdd").click(function(){
    var Imgcount = $("#imggroup .imgUp").length;
    if(Imgcount < 3){
      if(Imgcount == 2){
          $(".imgAdd").hide();
      }
      $(this).closest(".row").find('.imgAdd').before('<div class="col-xs-4 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="expense_photo[]" type="file" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fa fa-times del"></i></div>');          
    }else{
      $(".imgAdd").hide();
    }
  });
  $(document).on("click", "i.del" , function() {
    var Imgcount = $("#imggroup .imgUp").length;
    if(Imgcount<4){
      $(".imgAdd").show();
    }
    $(this).parent().remove();
  });
  $(function() {
    $(document).on("change",".uploadFile", function()
    {
      $(this).closest(".imgUp").find('.imagePreview').empty();
      $(this).closest(".imgUp").find('span').empty();
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