@extends('layouts.company')
@section('title', 'Custom Rate Setup')
@section('stylesheets')
  @include('company.parties_rate_setup.assets.custom_css')
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      
      @include('company.parties_rate_setup.partials.session_messages')
      
      <div class="box">
        
        <div class="box-header">
          <h3 class="box-title"><span id="rateHeader">{{$rate_name}}</span> Rate Setup</h3>
          <a href=" {{ URL::previous() }}" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
          <span id="partiesratesettupexports" class="pull-right"></span>
          @if(Auth::user()->can('party_wise_rate_setup-update') || $createCheck)<img src="{{asset('assets/dist/img/bolticon.png')}}" class="quick-custom-rate-button pull-right"></img>@endif
        </div>
        <!-- /.box-header -->
        @if(Auth::user()->can('party_wise_rate_setup-update'))
          <div class="box-body">
            @include('company.parties_rate_setup.partials.settings_form')
          </div>
        @endif
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
            <table id="partiesratesettup" class="table table-bordered table-striped" style="width: 100% !important;">
              <thead>
                <tr>
                  <th>S.No</th>
                  <th>Product Name</th>
                  <th>Variant Name</th>
                  <th>Unit</th>
                  <th>Original Rate</th>
                  <th>Custom Rate<i class='fa fa-info-circle' title='Click on the cells and press Enter to Update.'></i></th>
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
  </div>
  <div class="modal fade" id="quickRateSetupModal" tabindex="-1" role="dialog">
    <form id="quickRateSetupForm" method="post" action="{{domain_route('company.admin.add_new_rate.quick_custom_rate_setup')}}">
      @csrf
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Quick Custom Rate Setup</h4>
          </div>
          <div class="modal-body">
            <div class="row custom_rate_form">
              <div class="col-xs-4" style="text-align: left;width: fit-content;">
                <label for="">Set Custom Rate to be</label> 
              </div>
              <div class="col-xs-4">
                <span class="input-group">
                  <input class="form-control quick_rate_setup_input" type="text" name="quick_rate_setup_input" required="">
                  <span class="input-group-addon">%</span>
                </span>
                <span class="quick_rate_setup_err errlabel" style="color:red">
                </span>
              </div>
              <div class="col-xs-4" style="text-align: left;width: fit-content;">
                <label> of Original Rate.</label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button id="quickCustomRateBtn" type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->
</section>
@endsection

@section('scripts')
  @include('company.parties_rate_setup.assets.custom_scripts')
@endsection