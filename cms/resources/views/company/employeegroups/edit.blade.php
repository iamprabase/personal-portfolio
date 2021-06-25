@extends('layouts.company')
@section('title', 'Edit Group')

@section('stylesheets')
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
@endsection

@section('content')
<section class="content">
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Edit Group</h3>
      <div class="box-tools pull-right">

        <div class="col-xs-7 page-action text-right">

          <a href="{{ domain_route('company.admin.employeegroup') }}" class="btn btn-default btn-sm"> <i
              class="fa fa-arrow-left"></i> Back</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      {!! Form::model($employeegroup, array('url' =>
      url(domain_route('company.admin.employeegroup.update',[$employeegroup->id])) , 'method' => 'PATCH', 'files'=>
      true)) !!}
      @include('company.employeegroups._form')
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

@endsection