@extends('layouts.company')
@section('title', 'Outlets Setup')
@section('stylesheets')
  @include('company.outlets_setup.assets.custom_css')
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      
      @include('company.outlets_setup.partials.session_messages')
      
      <div class="box">
        
        <div class="box-header">
          <h3 class="box-title">Outlet Connection</h3>
          @if(Auth::user()->can('outlet-create'))
          <a href="javascript:void(0)" class="btn btn-primary pull-right" id="create-connection-btn"
            style="margin-left: 5px;">
            <i class="fa fa-plus"></i> Create New
          </a>
          @endif
          <span id="outletsexports" class="pull-right"></span>
        </div>
        <!-- /.box-header -->
        
        {{-- <div class="box-body">
          @include('company.outlets_setup.partials.form')
        </div> --}}
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-3">
                    <div style="margin-top:10px;"></div>
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
            <table id="outlets" class="table table-bordered table-striped" style="width: 100% !important;">
              <thead>
                <tr>
                  <th>S.No</th>
                  <th>Outlet Name</th>
                  {{-- <th>Contact Person Code</th> --}}
                  <th>Connected Party</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <!-- /.box-body -->
      
      </div>
      <!-- /.box -->

    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <div id="connectionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Create Connection</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            @include('company.outlets_setup.partials.form')
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- <div id="updateConnectionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Update Connection</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            @include('company.outlets_setup.partials.edit')
          </div>
        </div>
      </div>
    </div>
  </div> --}}

  <div class="modal modal-default fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <form method="post" class="remove-connection-modal">
          {{method_field('delete')}}
          {{csrf_field()}}
          <div class="modal-body">
            <p class="text-center">
              Are you sure you want to remove the connection?
            </p>
            <input type="hidden" name="del_client_id" id="del_client_id" value="">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
            <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
  @include('company.outlets_setup.assets.custom_scripts')
@endsection