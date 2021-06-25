@extends('layouts.company')
@section('title', 'Import Parties')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/dist/css/settings.css') }}">
@endsection
@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
      <a href="{{ domain_route('company.admin.import.parties') }}" class="btn btn-primary" style="margin-left: 5px;">Parties</a>
  <a href="#" class="btn btn-primary headerTab" style="margin-left: 5px;">Outstanding</a>
    <!-- Import Outstanding Starts Here -->
    <div class="row" style="margin-top: 30px;">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Import Parties OutStanding Amount</h3>
          </div>
          <div class="box-body" style="padding: 20px !important;">
            <div class="row">
  <div class="col-xs-12">
    <span class="text-dark" style="font-size: 16px;">** Please don't make any change in heading.Before uploading file please make sure the below points.
    <br>
    1. Please create Party before uploading file. To create Party <a href="{{domain_route('company.admin.client.create')}}">click here</a> or Import from above box.
    <br>
    2. Party Name or Party Code and Outstanding fields are compulsory.
    <br>
    You've to import the file in csv format, i.e. (file having extension *.csv). You can download a sample file format by clicking the download sample button below: </span>
  </div>
</div> 
@if($errors->any())
   <ul class="alert alert-danger" style="list-style: none;">
      @foreach ($errors->all() as $error)
           <li >{{ $error }}</li>
       @endforeach
    </ul>
@endif 
@if($message = Session::get('error'))
   <div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif 
   @if($message = Session::get('success'))
   <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif           <div class="row" style="margin-top: 20px;">
              <div class="col-xs-3"></div>
              <div class="col-xs-6 text-center">
                <a href="{{ asset('assets/files/sample_parties_outstanding.csv') }}" class="btn btn-default">
  <i class="fa fa-download fa fw"></i> Download Sample Format
</a>

             </div>
              <div class="col-xs-3"></div>
            </div>
            <hr>
            <div class="row" style="margin-top: 20px;">
              <div class="col-xs-12">
                {!! Form::open(array('url' => url(domain_route("company.admin.import.addclientsoutstanding")), 'method' => 'post', 'files'=> true)) !!}
                  <div class="form-group row">
    <div class="col-xs-4 text-right" style="margin-top: 10px;">
      <label for="spreadsheet">Select file to upload</label>
    </div>
    <div class="col-xs-4 text-center">
      <div class="input-group" style="margin-bottom:10px;">
                    <span class="input-group-btn">
                      <span class="btn btn-default btn-file imagefile">
                        Browse… <input id="importfile" name="import_file3" type="file">
                      </span>
                    </span>
                    <input type="text" class="form-control" readonly>
                  </div>
    </div>
    <div class="col-xs-4">
      
    </div>
  </div>
  <div class="row">
              <div class="col-xs-4 col-sm-4 col-md-4">
              </div>
              <div class="col-xs-4 col-sm-4 col-md-4">
                {!! Form::submit('Start Importing', ['class' => 'btn btn-primary pull-right', 'id' => 'create_new_entry2']) !!}
              </div>
              <div class="col-xs-4 col-sm-4 col-md-4">
              </div>
            </div>
</form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>





@endsection


@section('scripts')
 <script>

 $(document).on('change', '.btn-file :file', function () {

          var input = $(this),

              label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

          input.trigger('fileselect', [label]);

      });



      $('.btn-file :file').on('fileselect', function (event, label) {



          var input = $(this).parents('.input-group').find(':text'),

              log = label;



          if (input.length) {

              input.val(log);

          } else {

              if (log) alert(log);

          }



      });



      function readURL(input) {

          if (input.files && input.files[0]) {

              var reader = new FileReader();
              reader.onload = function (e) {

                  $('#img-upload').attr('src', e.target.result);

              }



              reader.readAsDataURL(input.files[0]);

          }

      }
      $("#importfile").change(function () {
          readURL(this);
      });
    </script>
@endsection