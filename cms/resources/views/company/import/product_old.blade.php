@extends('layouts.company')
@section('stylesheets')

@endsection
@section('content')
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        @if (\Session::has('success'))
          <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
          </div><br/>
        @endif
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Import Products</h3>
            <a href="{{ asset('assets/files/products_sample.csv') }}" class="btn btn-primary btn-sm pull-right" style="margin-left: 5px;">
              <i class="fa fa-plus"></i> Download Sample
            </a>
            <span id="bankexports" class="pull-right"></span>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            {!! Form::open(array('url' => url(domain_route("company.admin.import.addproducts", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true)) !!}
              <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4">
              </div>
              <div class="col-xs-4 col-sm-4 col-md-4">
                <div class="form-group ">
                  <label>Import File:</label>
                  <!-- <small> Size of image should not be more than 2MB.</small> -->
                  <div class="input-group" style="margin-bottom:10px;">
                    <span class="input-group-btn">
                      <span class="btn btn-default btn-file imagefile">
                        Browse… <input id="importfile" name="import_file" type="file">
                      </span>
                    </span>
                    <input type="text" class="form-control" readonly>
                  </div>
                </div>              
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
          </div>
           {!! Form::close() !!}
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
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