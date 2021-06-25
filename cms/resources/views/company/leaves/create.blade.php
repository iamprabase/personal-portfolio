@extends('layouts.company')

@section('title', 'Create Leave')

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
    z-index: 999999!important;
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
      </div><br/>
    @endif



    <!-- SELECT2 EXAMPLE -->

    <div class="box box-default">

      <div class="box-header with-border">

        <h3 class="box-title">Create Leave</h3>



        <div class="box-tools pull-right">

          <div class="col-xs-7 page-action text-right">

            <a href="{{ domain_route('company.admin.leave') }}" class="btn btn-default btn-sm"> <i

                  class="fa fa-arrow-left"></i> Back</a>

          </div>

        </div>

      </div>

      <!-- /.box-header -->

      <div class="box-body">



      {!! Form::open(array('url' => url(domain_route("company.admin.leave.store", ["domain" => request("subdomain")])), 'method' => 'post', 'autocomplete' => 'off', 'files'=> true)) !!}

      @include('company.leaves._form')

      <!-- Submit Form Button -->

        {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right', 'id'=>'create_new_entry']) !!}

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

  <script type="text/javascript" src="{{asset('assets/bower_components/moment/moment.js')}}"></script>

  {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> --}}

  <script>

      $(function () {

          $('.select2').select2();

          @if(config('settings.ncal')==0)

          $("#start_date").datepicker({

            format: 'yyyy-mm-dd',

            autoclose: true,
            orientation: "below" ,

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

          $('#start_date_np').nepaliDatePicker({
            ndpEnglishInput: 'start_date_eng',
            onChange:function(){
              if($('#start_date_np').val()>=$('#end_date_np').val()){
                $('#end_date_np').val($('#start_date_np').val());
                $('#end_date_eng').val(BS2AD($('#start_date_np').val()));
              }
            }
          });
          $('#end_date_np').nepaliDatePicker({
            ndpEnglishInput: 'end_date_eng',
            onChange:function(){
              if($('#end_date_np').val()<=$('#start_date_np').val()){
                $('#start_date_np').val($('#end_date_np').val());
                $('#start_date_eng').val(BS2AD($('#end_date_np').val()));
              }
            }
          });
          @endif

          // disableBefore: moment(getNepaliDate()).format('MM/DD/Y'),

          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({

              checkboxClass: 'icheckbox_minimal-blue',

              radioClass: 'iradio_minimal-blue'

          });



          //CKEDITOR.replace('companydesc');

      });

  </script>



@endsection