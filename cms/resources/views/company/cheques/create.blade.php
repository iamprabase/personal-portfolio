@extends('layouts.company')

@section('title', 'Create Cheque')

@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">

  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
  @if(config('settings.ncal')==1)
  <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
  <link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif

@endsection

@section('content')

  <section class="content">

    <div class="box box-default">

      <div class="box-header with-border">

        <h3 class="box-title">Create Cheque</h3>

        <div class="box-tools pull-right">

          <div class="col-md-7 page-action text-right">

            <a href="{{ domain_route('company.admin.cheque.index') }}" class="btn btn-default btn-sm"> <i

                  class="fa fa-arrow-left"></i> Back</a>

          </div>

        </div>

      </div>

      <!-- /.box-header -->

      <div class="box-body">

      {!! Form::open(array('url' => url(domain_route("company.admin.cheque.store", ["domain" => request("subdomain")])), 'method' => 'post', 'autocomplete' => 'off', 'files'=> true)) !!}

      @include('company.cheques._form')

      <!-- Submit Form Button -->

        {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right', 'id'=>'create_new_entry']) !!}

        {!! Form::close() !!}

      </div>

    </div>

  </section>

@endsection

@section('scripts')
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  @if(config('settings.ncal')==1)
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  <script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  @else
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  @endif
  <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script> 
  <script>
  $(function () {

      $('.select2').select2();
      $("#due_date").datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
      });

      $("#cheque_date").datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
      });

      $("#receive_date").datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
      });

      $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass: 'iradio_minimal-blue'
      });

  });
  </script>
@endsection