@extends('layouts.company')

@section('title', 'Edit Leave')

@section('stylesheets')

  <link rel="stylesheet"

        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

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



  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>

  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

  

  <script>
      $('.DT_Tour_FILTER').val(sessionStorage.getItem('DT_Tour_filters'));

      $(function () {



          $('.select2').select2();





          $("#start_date").datepicker({

              format: 'yyyy-mm-dd',

              autoclose: true,

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



          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({

              checkboxClass: 'icheckbox_minimal-blue',

              radioClass: 'iradio_minimal-blue'

          });



          //CKEDITOR.replace('companydesc');

      });

  </script>



@endsection