@extends('layouts.company')

@section('title', 'Edit Leave')

@section('stylesheets')

@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<style>
  .datepicker{
    z-index: 9999999!important;
  }
  .select2-dropdown{
    z-index:99999999;
  }
</style>
@endsection

@section('content')

  <section class="content">
    @if (\Session::has('alert'))
    <div class="alert alert-warning">
      <p>{{ \Session::get('alert') }}</p>
    </div><br />
    @endif

    <!-- SELECT2 EXAMPLE -->

    <div class="box box-default">

      <div class="box-header with-border">

        <h3 class="box-title">Company Information</h3>


        <div class="box-tools pull-right">

          <div class="col-xs-7 page-action text-right">

            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i

                  class="fa fa-arrow-left"></i> Back</a>

          </div>

        </div>

      </div>

      <!-- /.box-header -->

      <div class="box-body">



      {!! Form::model($leave, array('url' => url(domain_route('company.admin.leave.update',[$leave->id])) , 'method' => 'PATCH', 'autocomplete' => 'off', 'files'=> true)) !!}

      @include('company.leaves._form')

      <!-- Submit Form Button -->

        {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right']) !!}

        {!! Form::close() !!}



      </div>

    </div>



  </section>





@endsection



@section('scripts')



  {{-- <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script> --}}

  @if(config('settings.ncal')==1)
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  <script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  @else
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  @endif
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>

  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

  

  <script>
      $('.DT_Leav_FILTER').val(sessionStorage.getItem('DT_Leav_filters'));

      $(function () {



          $('.select2').select2();





          // $("#start_date").datepicker({

          //     format: 'yyyy-mm-dd',

          //     autoclose: true,

          //     // startDate: '-0d',

          // }).on('changeDate', function (selected) {

          //     var startDate = new Date(selected.date.valueOf());

          //     $('#end_date').datepicker('setStartDate', startDate);

          // }).on('clearDate', function (selected) {

          //     $('#end_date').datepicker('setStartDate', null);

          // });



          // $("#end_date").datepicker({

          //     format: 'yyyy-mm-dd',

          //     autoclose: true,

          //     // startDate: '-0d',

          // }).on('changeDate', function (selected) {

          //     var endDate = new Date(selected.date.valueOf());

          //     $('#start_date').datepicker('setEndDate', endDate);

          // }).on('clearDate', function (selected) {

          //     $('#start_date').datepicker('setEndDate', null);

          // });
          @if(config('settings.ncal')==0)

          $("#start_date").datepicker({

            format: 'yyyy-mm-dd',

            autoclose: true,
            orientation: "below",

            // startDate: '-0d',

            }).on('changeDate', function (selected) {

            var startDate = new Date(selected.date.valueOf());

            $('#end_date').datepicker('setStartDate', startDate);

            }).on('clearDate', function (selected) {

            $('#end_date').datepicker('setStartDate', null);

            });



            $("#end_date").datepicker({

            format: 'yyyy-mm-dd',

            autoclose: true,

            // startDate: '-0d',

            }).on('changeDate', function (selected) {

            var endDate = new Date(selected.date.valueOf());

            $('#start_date').datepicker('setEndDate', endDate);

            }).on('clearDate', function (selected) {

            $('#start_date').datepicker('setEndDate', null);

            });
          @else
          tempchequedate = '{{$leave->start_date}}';
          if(tempchequedate==""){
            ntempdate = AD2BS(moment().format('YYYY-MM-DD'));
          }else{
            ntempdate = AD2BS(tempchequedate);
          }
          $('#start_date_np').val(ntempdate);
          $('#start_date_np').nepaliDatePicker({
            ndpEnglishInput: 'start_date_eng',
            onChange: function(){
              if($('#start_date_np').val()>=$('#end_date_np').val()){
                $('#end_date_np').val($('#start_date_np').val());
              }
            }
          });

          temppaymentdate = '{{$leave->end_date}}';
          if(temppaymentdate==""){
            ntempdate = AD2BS(moment().format('YYYY-MM-DD'));
          }else{
            ntempdate = AD2BS(temppaymentdate);
          }
          $('#end_date_np').val(ntempdate);
          $('#end_date_np').nepaliDatePicker({
            ndpEnglishInput: 'end_date_eng',
            onChange: function(){
              if($('#end_date_np').val()<=$('#start_date_np').val()){
                $('#start_date_np').val($('#end_date_np').val());
              }
            }
          });
          @endif



          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({

              checkboxClass: 'icheckbox_minimal-blue',

              radioClass: 'iradio_minimal-blue'

          });



          //CKEDITOR.replace('companydesc');

      });

  </script>



@endsection