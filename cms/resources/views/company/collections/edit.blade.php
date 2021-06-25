@extends('layouts.company')
@section('title', 'Edit Collection')
@section('stylesheets')
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
@if(empty($collection->bank_id))
<style>
  #bankdetails {
    display: none;
  }
  .datepicker{
    z-index:9999!important;
  }
  .box.box-default{
    z-index: 1;
  }
</style>
@endif
@endsection

@section('content')
<section class="content">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Edit Collection</h3>

      <div class="box-tools pull-right">
        <div class="col-xs-7 page-action text-right">
          <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      {!! Form::model($collection, array('url' => url(domain_route('company.admin.collection.update',[$collection->id]))
      , 'method' => 'PATCH', 'files'=> true)) !!}
      @include('company.collections._form')
      <!-- Submit Form Button -->
      {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}
      {!! Form::close() !!}

    </div>
  </div>

</section>


@endsection

@section('scripts')
@if(config('settings.ncal')==1)
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  <script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
@else
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>

<script>
  $('.DT_Collec_FILTER').val(sessionStorage.getItem('DT_Collec_filters'));

  $(function () {
    $('.select2').select2();

    @if(config('settings.ncal')==0)
     $('#payment_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true,
      endDate:new Date(),
    });
    
    $('#cheque_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true,
    });

    @else        
      temppaymentdate = '{{$collection->payment_date}}';
      if(temppaymentdate==""){
        ntempdate = AD2BS(moment().format('YYYY-MM-DD'));
      }else{
        ntempdate = AD2BS(temppaymentdate);
      }
      var today = moment().subtract('1', 'days').format('YYYY-MM-DD');
      var ntoday = AD2BS(today);
      var ntoday= ntoday.split('-');
      ntoday = ntoday[1]+'/'+ntoday[2]+'/'+ntoday[0];
      $('#payment_date_np').val(ntempdate);
      $('#payment_date_np').nepaliDatePicker({
        ndpEnglishInput: 'englishDate',
        disableAfter: ntoday,
        onChange: function(){
          $('#payment_date_eng').val(BS2AD($('#payment_date_np').val()));
        }
      });
      tempchequedate = '{{$collection->cheque_date}}';
      if(tempchequedate==""){
        ntempdate2 = AD2BS(moment().format('YYYY-MM-DD'));
      }else{
        ntempdate2 = AD2BS(tempchequedate);
      }
      $('#cheque_date_np').val(ntempdate2);
      $('#cheque_date_np').nepaliDatePicker({
        onChange: function(){
          $('#cheque_date_eng').val(BS2AD($('#cheque_date_np').val()));
        }
      });
    @endif
  });

  var Imgcount = $("#imggroup .imgUp").length;
  if(Imgcount >= 3){
    $('.imgAdd').hide();
  }

  var current_payment_method = "{{$collection->payment_method}}";

  if(current_payment_method == 'Cheque'){
    $('#bank').prop('required', true);
    $('#cheque_no').prop('required', true);
    $('#cheque_date').prop('required', true);
    $('#bankdetails').show();
    $('#chequeDetails').show();
  }else if(current_payment_method == 'Bank Transfer'){
     $('#bankdetails').show();
     $('#chequeDetails').hide();
  }else{
      $('#bankdetails').hide();
      $('#bank').prop('required', false);
      $('#cheque_no').prop('required', false);
      $('#cheque_date').prop('required', false);
  }

  $('select[name=payment_method]').change(function () {
      if ($(this).val() == 'Cheque') {
          $('#bank').prop('required', true);
          $('#cheque_no').prop('required', true);
          $('#cheque_date').prop('required', true);
          $('#bankdetails').show();
          $('#chequeDetails').show();
      }else if($(this).val() == "Bank Transfer"){
          $('#bankdetails').show();
          $('#chequeDetails').hide();
      }else {
          $('#bankdetails').hide();
          $('#bank').prop('required', false);
          $('#cheque_no').prop('required', false);
          $('#cheque_date').prop('required', false);
      }
  });

  $(".imgAdd").click(function(){
    var Imgcount = $("#imggroup .imgUp").length;
    if(Imgcount < 3){
      if(Imgcount == 2){
          $(".imgAdd").hide();
      }
      $(this).closest(".row").find('.imgAdd').before('<div class="col-xs-4 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="receipt[]" type="file" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fa fa-times del"></i></div>');          
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