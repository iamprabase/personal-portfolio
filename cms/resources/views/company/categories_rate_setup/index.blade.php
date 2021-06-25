@extends('layouts.company')
@section('title', 'Category Rate Setup')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
  #rates_table .btn-success{
    color: #00da76!important;
    font-size: 15px;
    background: none !important;
    border: none;
  }
  .close{
    font-size: 30px;
    color: #080808;
    opacity: 1;
  }

  #rates_table .btn-danger {
    color: #d9534f;
    font-size: 15px;
    background: none!important;
    border: none!important;
  }

  #rates_table .btn-warning {
    color: #e08e0b;
    font-size: 15px;
    background: none!important;
    border: none!important;
  }

</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br />
      @endif

      @if (\Session::has('error'))
      <div class="alert alert-warning">
        <p>{{ \Session::get('error') }}</p>
      </div><br />
      @endif

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Category Rate Setup </h3>
         <a class="btn btn-primary pull-right" style="margin-left: 5px;" data-toggle="modal"
            data-target="#addRateModal"> <i class="fa fa-plus"></i> Create New </a>
          <span id="rates_export" class="pull-right"></span>
        </div>
        <!-- /.box-header -->

        <div class="box-body" id="mainBox">
          <div id="loader1" hidden>
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
          </div>
          <table id="rates_table" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>
              @php $i=0 @endphp
              @foreach($categories as $id=>$category)
                <tr>
                  <td>{{++$i}}</td>
                  <td>{{$category}}</td>
                  <td>
                    <a data-toggle='modal' data-target='#addRateModal' class='btn btn-default btn-sm' style="border-color: #fff!important;" data-category-id="{{$id}}"><i class='fa fa-plus'></i>Add Rate</a>
                    <a href="{{domain_route('company.admin.category.rates.show', [$id])}}" class='btn btn-success btn-sm rate_show_details'><i class='fa fa-eye'></i></a>
                  </td>
                </tr>
              @endforeach()
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
@include('company.categories_rate_setup.rate_modal')

<div class="modal fade" id="delete" tabindex="-1" role="dialog">
  <form id="delete_rate_form" method="post" action="{{domain_route('company.admin.add_new_rate.delete')}}">
    @csrf
    @method('delete')
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" align="center">Deletion Confirmation</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div align="center">
                Are you sure you want to Delete Current Selected Rate ( <strong><span id="rate_title"></span></strong> ) ?
              </div>
              <input type="text" name="id" id="delete_id" hidden>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="delRateBtn" type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->
@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
@include('company.categories_rate_setup.custom_js')
@endsection