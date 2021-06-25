@extends('layouts.company')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
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
        <h3 class="box-title">Edit Leads</h3>

        <div class="box-tools pull-right">
          <div class="col-md-7 page-action text-right">
            <a href="{{ domain_route('company.admin.lead') }}" class="btn btn-default btn-sm"> <i
                  class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
      {!! Form::model($lead, array('url' => url(domain_route('company.admin.lead.update',[$lead->id])) , 'method' => 'PATCH', 'files'=> true)) !!}
      @include('company.leads._form')
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
  
  <script>
      $(function () {
          $("#payment_date").datepicker({
              format: "yyyy-mm-dd"
          });    // Here the current date is set
      });

  </script>

@endsection