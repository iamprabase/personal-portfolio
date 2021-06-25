@extends('layouts.company')

@section('title', 'Create Announcement')

@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
  <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}"/>
  <style>
    .multiselect-item.multiselect-group label input{
      height:auto;
    }
    .panel-heading{
      color: #fff!important;
      background-color: #0b7676!important;
    }
  </style>

@endsection



@section('content')

  <section class="content">


    <!-- SELECT2 EXAMPLE -->

    <div class="box box-default">

      <div class="box-header with-border">

        <h3 class="box-title">Create Announcement</h3>


        <div class="box-tools pull-right">

          <div class="col-xs-7 page-action text-right">

            <a href="{{ domain_route('company.admin.announcement') }}" class="btn btn-default btn-sm"> <i
                  class="fa fa-arrow-left"></i> Back</a>

          </div>

        </div>

      </div>

      <!-- /.box-header -->

      <div class="box-body">

      {!! Form::open(array('url' => url(domain_route("company.admin.announcement.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true)) !!}



      @include('company.announcements._form')

      <!-- Submit Form Button -->

        {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right', 'id'=>'create_new_entry']) !!}

        {!! Form::close() !!}

      </div>

    </div>


  </section>





@endsection



@section('scripts')

  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
  <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>

  <script>

      $('#employees').multiselect({
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true,
          enableFullValueFiltering: true,
          enableClickableOptGroups: true,
          includeSelectAllOption: true, 
          enableCollapsibleOptGroups : true,
          selectAllNumber: false,
          nonSelectedText:"Select Employees",
      });

  </script>



@endsection