@extends('layouts.company')
@section('title', 'Outlets Setup')
@section('stylesheets')
  @include('company.outlets_ptr_setup.assets.custom_css')
  <style>
    .box-body .btn-primary {
      background-color: #0b7676;
      border-color: transparent !important;
      color: #fff !important;
    }
  </style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      
      @include('company.outlets_ptr_setup.partials.session_messages')
      
      <div class="box">
        
        <div class="box-header">
          <h3 class="box-title">Order Setup</h3>
          <span id="ptrexports" class="pull-right"></span>
          {{-- <div class="dropdown pull-right tips" style="margin-right: 5px;">
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">⋮</button>
            <ul class="dropdown-menu">
              <li><a href="#" class="show_all_in_app_check_box" data-check-uncheck="{{$show_all}}">Show/Hide All Products In App</a></li>
            </ul>
          </div> --}}
        </div>
        <!-- /.box-header -->

        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-6">
                    {{-- <div class="col-xs-2" style="margin-top:10px;">
                      <input type='checkbox' class='show_all_in_app_check_box showin_app hidden' name='show_all_in_app_check_box' data-check-uncheck="{{$show_all}}" />
                    </div>
                    <div class="col-xs-10 show_all_text showin_app hidden">
                      <strong>Show/Hide All Products In App</strong> 
                    </div> --}}
                  </div>
                  <div class="col-xs-3">
                    <div style="margin-top:10px;"></div>
                  </div>

                  <div class="col-xs-4">
                  </div>
                  <div class="col-xs-2">
                  </div>

                </div>
              </div>
            </div>
            <div class="col-xs-2"></div>
          </div>
          <div id="loader1" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <div id="mainBox">
            <table id="ptrseup" class="table table-bordered table-striped" style="width: 100% !important;">
              <thead>
                <tr>
                  <th>S.No</th>
                  <th>Product Name</th>
                  <th>Variant Name</th>
                  <th>Unit</th>
                  <th>Rate</th>
                  {{-- <th>PTR</th> --}}
                  <th>MOQ<i class='fa fa-info-circle' title='Click on the cells and press Enter to Update.'></i></th>
                  <th {{(config('settings.product_level_discount')==1)?"":"hidden"}}>Discount<i class='fa fa-info-circle' title='Click on the cells and press Enter to Update.'></i></th>
                  <th><input type='checkbox' class='show_all_in_app_check_box showin_app hidden' name='show_all_in_app_check_box'
                    data-check-uncheck="{{$show_all}}" style="height: auto;margin-right: 5px;width: 18px;" {{$show_all==1?"checked":""}}/>Show In App</th>
                  {{-- <th></th> --}}
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <!-- /.box-body -->

        <div class="box-body">
          @include('company.outlets_ptr_setup.partials.settings_form')
        </div>
      
      </div>
      <!-- /.box -->

    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <div id="productUpdateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Update Details</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            @include('company.outlets_ptr_setup.partials.form')
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
  @include('company.outlets_ptr_setup.assets.custom_scripts')
@endsection