@extends('layouts.company')
@section('title', 'Import Products')
@section('stylesheets')

@endsection
@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Import Products</h3>
          </div>
          <div class="box-body" style="padding: 20px !important;">
            <div class="row">
  <div class="col-xs-12">
    <span class="text-dark" style="font-size: 16px;">** Please don't make any change in heading. For products without variant ProductName,MRP and Unit is compulsory and for variant VariantName.You've to import the file in csv format, i.e. (file having extension *.csv). You can download a sample file format by clicking the download sample button below: </span>
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
                @if(config('settings.party_wise_rate_setup')==1)
                <a href="{{ asset('assets/files/sample_productscustomrates.csv') }}" class="btn btn-default">
  <i class="fa fa-download fa fw"></i> Download Sample Format
</a>@else
<a href="{{ asset('assets/files/sample_products.csv') }}" class="btn btn-default">
  <i class="fa fa-download fa fw"></i> Download Sample Format
</a>
@endif

             </div>
              <div class="col-xs-3"></div>
            </div>
            <hr>
            <div class="row" style="margin-top: 20px;">
              <div class="col-xs-12">
                {!! Form::open(array('url' => url(domain_route("company.admin.import.addproducts")), 'method' => 'post', 'files'=> true)) !!}
                  <div class="form-group row">
    <div class="col-xs-4 text-right" style="margin-top: 10px;">
      <label for="spreadsheet">Select file to upload</label>
    </div>
    <div class="col-xs-4 text-center">
      <div class="input-group" style="margin-bottom:10px;">
                    <span class="input-group-btn">
                      <span class="btn btn-default btn-file imagefile">
                        Browse… <input id="importfile" name="import_file" type="file">
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
                {!! Form::submit('Start Importing', ['class' => 'btn btn-primary pull-right', 'id' => 'create_new_entry']) !!}
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