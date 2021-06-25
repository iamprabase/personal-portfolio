@extends('layouts.company')
@section('title', 'Orders')
@section('stylesheets')
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@if($nCal==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}" />
<style>
  /* .box-loader { 
    opacity: 0.5;
  } */

  #reportrange,#nepCalDiv {
    width: 215px;
    /* margin-left: 20px; */
  }

  #reportrangediv{
    padding-right: 0px;
    margin-right: 0px;
    width: auto;
  }

  .orderStatusCheckBox {
    position: relative;
    margin-right: 10px;
    height: auto;
  }

  input[type="radio"],
  input[type="checkbox"].orderStatusCheckBox {
    margin: 10px;
    margin-top: 1px \9;
    line-height: normal;
  }

  .btn-warning {
    background-color: #e08e0b;
    border-color: transparent;
    color: #ffffff;
    margin-right: 5px;
  }

  #selectthispage {
    height: fit-content;
  }

  .tooltip-inner {
    max-width: 350px;
  }

  .modal-footer .btn-danger {
    background-color: #f02424 !important;
    border-color: #f02424 !important;
    color: #fff !important;
  }

  .modal-footer {
    padding: 8px 0px;
    text-align: right;
    border-top: 1px solid #e5e5e5;
  }

  .no-pd {
    padding: 0;
  }

  .ndp-nepali-calendar {
    width: 90px !important;
    padding: 2px;
  }

  .fa.fa-caret-down,
  .caret {
    position: absolute;
  }

  /* #stsfilter {
    margin-left: 20px;
  } */

  .btn-group.width-adjust {
    min-width: 200px;
  }

  .ndateaddon {
    padding: 0;
    border: none;
  }

  .ncalstatusIcon {
    height: 40px;
  }

  .ncalstatusInput {
    width: 533px !important;
    position: absolute;
  }

  .dataTables_filter input {
    width: 120px !important;
  }

  .select2-selection__placeholder {
    color: #333 !important;
  }

  .multiselect-selected-text {
    margin-right: 90px;
    color: #333 !important;
  }

  .close {
    font-size: 30px;
    color: #080808;
    opacity: 1;
  }

  /* img {
    vertical-align: initial;
    width: 25px;
  } */

  .pd-rt-0{
    padding-right: 0px;
  }

  .pd-lt-0{
    padding-left: 0px;
  }

  .btn-group.width-adjust{
    min-width: auto;
  }

  .multiselect-selected-text{
    margin-right: 0px;
  }

  .multiselect.dropdown-toggle.btn.btn-default .caret {
    position: relative;
    margin-top: 10px;
  }
  .hide_column {
    display: none;
  }

  .round {
    position: relative;
    width: 15px;
  }

  .round label {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 50%;
    cursor: pointer;
    height: 15px;
    left: 0;
    position: absolute;
    top: 3px;
    width: 28px;
  }

  .round label:after {
    border: 2px solid #fff;
    border-top: none;
    border-right: none;
    content: "";
    height: 6px;
    left: 0px;
    opacity: 0;
    position: absolute;
    top: 3px;
    transform: rotate(-45deg);
    width: 12px;
  }

  .round input{
    height: 10px;
  }

  .round input[type="checkbox"] {
    visibility: hidden;
  }

  .round input[type="checkbox"]:checked + label {
    background-color: #66bb6a;
    border-color: #66bb6a;
  }

  .round input[type="checkbox"]:checked + label:after {
    opacity: 1;
  }

  .pad-left{
    padding-left: 0px;
  }

  .ordFilter{
    width: 125px;
    margin-top: 10px;
  }

  .select-2-sec{
    display:flex;
    justify-content: space-between;
  }
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      @include('layouts.partials.flashmessage')
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Order List</h3>
          @if(Auth::user()->can('order-create'))
            <a href="{{ domain_route('company.admin.order.create') }}" class="btn btn-primary pull-right"
              style="margin-left: 5px;">
              <i class="fa fa-plus"></i> Create New
            </a>
          @endif
          <span id="orderexports" class="pull-right"></span>
          <div class="dropdown pull-right tips"
            title="Mass Actions(Mail,Download,Change Delivery Status and Mass Delete)" style="margin-right: 5px;">
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">⋮</button>
            <ul class="dropdown-menu">
              <li><a href="#" class="mass_action" data-type="massdownload">Download Invoices</a></li>
              <li><a href="#" class="mass_action" data-type="massmail">Mail Invoices</a></li>
              <li><a href="#" class="mass_action" data-type="massmaildownload">Mail And Download Invoices</a></li>
              <li><a href="#" class="updateStatuses">Change Delivery Status</a></li>
              <li><a href="#" class="mass_action" data-type="massdelete">Mass Delete</a></li>
            </ul>
          </div>
          @if(getClientSetting()->order_with_amt==0)
            <div class="pull-right" id="totalCollectionAmt" style="
                  margin: 10px;
              "><strong>Total Orders: <span id="totalValue"></span></strong>
            </div>
          @endif
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-xs-10">
                <div class="select-2-sec">
                  <div class="col no-pd">
                    <div style="margin-top:10px; " id="partyfilter"></div>
                  </div>
                  <div class="col no-pd ordFilter">
                    <div id="salesmfilter"></div>
                  </div>
                  @if($partyTypeLevel)
                  <div class="col no-pd ordFilter"><div id="orderTofilter"></div></div>
                  @endif
                  <div class="col no-pd stsFilterDom hidden">
                    <div style="margin-top:10px;" id="beatfilter">
                      <select name="beatfilter" id="beatfilterdrop" multiple>
                        @foreach($beatsWithOrders as $id=>$beat)
                        <option value="{{$id}}" selected>{{$beat}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col no-pd stsFilterDom hidden">
                    <div style="margin-top:10px;" id="stsfilter">
                      <select name="stsfilter" id="stsfilterdrop" multiple>
                        @foreach($orderStatus as $status)
                        <option value="{{$status->id}}" selected>{{$status->title}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col no-pd" id="reportrangediv">
                    @if($nCal==0)
                    <div id="reportrange" name="reportrange" class="reportrange hidden" style="margin-top: 10px; ">
                      <i class="fa fa-calendar"></i>&nbsp;
                      <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                    <input id="start_edate" type="text" name="start_edate" hidden />
                    <input id="end_edate" type="text" name="end_edate" hidden />
                    @else
                    <div class="input-group hidden" id="nepCalDiv" style="margin-top: 10px;">
                      <span class="input-group-addon ndateaddon" aria-readonly="true">
                        <input id="start_ndate" class="form-control" type="text" name="start_ndate"
                          placeholder="Start Date" autocomplete="off" />
                      </span>
                      <span class="input-group-addon" aria-readonly="true"><i
                          class="glyphicon glyphicon-calendar"></i></span>
                      <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                        autocomplete="off" />
                      <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                      <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-xs-2">
              </div>
            </div>
          </div>
          <div id="mainBox">
            <table id="order" class="table table-bordered table-striped" style="width: 100% !important;">
              <thead>
                <tr>
                  <th>
                    <input type='checkbox' id='selectthispage' name='selectthispage' style="height: max-content;margin-right: 10px;">
                    S.No 
                  </th>
                  <th>Order No.</th>
                  <th>Order Date</th>
                  <th>Party Name</th>
                  <th>Created By</th>
                  @if($partyTypeLevel)
                  <th>Ordered To</th>
                  @endif
                  @if(getClientSetting()->order_with_amt==0)
                  <th>Grand Total</th>
                  @endif
                  <th>Order Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <div id="loader1" hidden>
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
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
</section>

<input type="hidden" name="pageIds[]" id="pageIds">
<!-- Modal -->
<div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <form method="post" class="remove-record-model">
        {{method_field('delete')}}
        {{csrf_field()}}
        <div class="modal-body">
          <p class="text-center">
            Are you sure you want to delete this?
          </p>
          <input type="hidden" name="order_id" id="c_id" value="">

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<form method="post" action="{{domain_route('company.admin.order.customPdfExport')}}" class="pdf-export-form hidden"
  id="pdf-generate">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="reportName" class="reportName" id="reportName">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>


<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="changeDeliveryStatus" method="POST"
          action="{{URL::to('admin/order/changeDeliveryStatus')}}">
          {{csrf_field()}}
          <input type="hidden" name="order_id" id="order_id" value="">
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Status</label>
            <div class="col-sm-10">
              <select class="form-control" id="delivery_status_id" name="delivery_status" required="required">
                @foreach($orderStatus as $orderSts)
                <option value="{{$orderSts->id}}">{{$orderSts->title}}</option>
                @endforeach
              </select>
            </div>
          </div>
          @if(getClientSetting()->order_approval==1)
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Dispatch Date</label>
            <div class="col-sm-10">
              <div class="input-group date">
                <span class="input-group-addon @if($nCal==1) ncalstatusIcon @endif">
                  <i class="fa fa-calendar"></i>
                </span>
                @if($nCal==0)
                {!! Form::text('delivery_date', null, ['class' => 'form-control pull-right', 'id' => 'delivery_datenew',
                'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
                @else
                <input type="text" autocomplete="off" class="form-control pull-right ncalstatusInput" style="width: 432px !important;" 
                  id="delivery_ndate" placeholder="Dispatch Date" />
                <input type="text" id="delivery_edate" name="delivery_date" hidden />
                @endif
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Dispatch Place</label>
            <div class="col-sm-10">
              {!! Form::text('delivery_place', null, ['class' => 'form-control', 'id=delivery_place', 'placeholder' =>
              'Delivery Place']) !!}
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Transport Name</label>
            <div class="col-sm-10">
              {!! Form::text('transport_name', null, ['class' => 'form-control', 'id=transport_name', 'placeholder' =>
              'Transport Name']) !!}
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Transport Number</label>
            <div class="col-sm-10">
              {!! Form::text('transport_number', null, ['class' => 'form-control', 'id=transport_number', 'placeholder'
              => 'Transport Number']) !!}
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="name"> Bilty Number</label>
            <div class="col-sm-10">
              {!! Form::text('billty_number', null, ['class' => 'form-control', 'id=billty_number', 'placeholder' =>
              'Bilty Number']) !!}
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Dispatch Note</label>
            <div class="col-sm-10">
              {!! Form::textarea('delivery_note', null, ['class' => 'form-control', 'rows="5"', 'id=delivery_note',
              'placeholder' => 'Delivery Notes']) !!}
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Include Delivery Details</label>
            <div class="col-sm-10">
              {!! Form::checkbox('include_delivery_details', null, ['class' => 'form-control', 'id' => 'include_delivery_details']) !!}
            </div>
          </div>
          @endif
          <div class="modal-footer">
            <button id="btn_status_change" type="submit" class="btn actionBtn">
              <span id="footer_action_button" class='glyphicon'> </span> Change
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Sorry! You are not authorized to change status.
        </p>
        <input type="hidden" name="expense_id" id="c_id" value="">
        <input type="text" id="accountType" name="account_type" hidden />
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="alertClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
      </div>
      <div class="modal-body">
        <p class="text-center">
          Sorry! You are not authorized to view this party details.
        </p>
      </div>
      <div class="modal-footer">
        {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
  </div>
</div>
<div class="modal modal-default fade" id="massModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center" id="massTitle"></h4>
      </div>
      <form class="form-horizontal" id="massForm" action="{{domain_route('company.admin.order.massActions')}}">
        <div class="modal-body">
          {{csrf_field()}}
          <div style="color: green;" class="text-center" id="massdetails"></div>
          <input type="text" name="order_id" id="mass_order_ids" hidden>
          <div id="massemail" class="hide">
            Email <span style="color:red;">*<span id="mass_email_alert" class="hide">( Please give valid email
                address... )</span></span>
            <input type="text" class="form-control" name="email" placeholder="Type your email here...">
          </div>
          <input type="text" name="mass_order_type" id="mass_order_type" hidden>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary massbuttons key_massmail hide" data-type="massmail"><i
                class="fa fa-send"></i> Mail</button>
            <button type="submit" class="btn btn-primary massbuttons key_massdownload hide" data-type="massdownload"><i
                class="fa fa-download"></i> Download</button>
            <button type="submit" class="btn btn-primary massbuttons key_massmaildownload hide"
              data-type="massmaildownload"><i class="fa fa-tasks"></i> Mail and Download</button>
            <button type="submit" class="btn btn-danger massbuttons key_massdelete hide" data-type="massdelete"><i
                class="fa fa-trash"></i> Mass Delete</button>
            {{-- <button type="button" class="btn btn-warning delete-button" data-dismiss="modal">Cancel</button> --}}
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="upateStatuses" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" id="changeDeliveryStatuses" method="POST"
          action="{{URL::to('admin/order/changeDeliveryStatus')}}">
          {{csrf_field()}}
          <input type="hidden" name="order_id[]" id="order_ids" value="">
          <div class="form-group">
            <label class="control-label col-sm-2" for="name">Status</label>
            <div class="col-sm-10">
              <select class="form-control" id="delivery_status" name="delivery_status" required="true">
                @foreach($orderStatus as $orderSts)
                <option value="{{$orderSts->id}}">{{$orderSts->title}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-4" for="name" style="margin-top: 8px !important ">Include Delivery Details</label>
            <div class="col-sm-8">
              {!! Form::checkbox('include_delivery_details', null, ['class' => 'form-control deliverycheck']) !!}
            </div>
          </div>
          <div class="modal-footer">
            <button id="btn_statuses_change" type="submit" class="btn btn-primary actionBtnStatuses">
              <span id="footer_action_button" class='glyphicon'> </span> Update Statuses
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables-buttons/buttons.flash.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@if($nCal==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
<script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
<script>
  $('document').ready(function () {
            @if (strpos(URL::previous(), domain_route('company.admin.order')) === false)
            var activeRequestsTable = $('#order').DataTable();
            activeRequestsTable.state.clear();
            activeRequestsTable.destroy();
          @endif
      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var mid = button.data('mid');
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
        modal.find('.modal-body #m_id').val(mid);
      });
      // Define columns for Data Table
      @if($partyTypeLevel)

        @if(getClientSetting()->order_with_amt==0)
          var columns = [{ "data": "id" },
          { "data": "orderno" },
          { "data": "orderdate" },
          { "data": "partyname" },
          { "data": "createdby" },
          { "data": "ordered_to" },
          { "data": "grandtotal" },
          { "data": "orderstatus" },
          { "data": "action" }]
        @else
          var columns = [{ "data": "id" },
          { "data": "orderno" },
          { "data": "orderdate" },
          { "data": "partyname" },
          { "data": "createdby" },
          { "data": "ordered_to" },
          { "data": "orderstatus" },
          { "data": "action" }]
        @endif

      @else
        @if(getClientSetting()->order_with_amt==0)
          var columns = [{ "data": "id" },
          { "data": "orderno" },
          { "data": "orderdate" },
          { "data": "partyname" },
          { "data": "createdby" },
          { "data": "grandtotal" },
          { "data": "orderstatus" },
          { "data": "action" }]
        @else
          var columns = [{ "data": "id" },
          { "data": "orderno" },
          { "data": "orderdate" },
          { "data": "partyname" },
          { "data": "createdby" },
          { "data": "orderstatus" },
          { "data": "action" }]
        @endif
      @endif
      // Permission alert for non-viewable clients
      $(document).on("click", ".clientLinks", function(e){
        if($(this).data('viewable')==""){
          e.preventDefault();
          $('#alertClientModal').modal('show');
        }
      });

      $('#stsfilterdrop').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: true,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        numberDisplayed: 1,
        nonSelectedText:"Select Status",
        allSelectedText:"Select Status",
        onChange: function(option, checked, select) {
          let stsValues = $('#stsfilterdrop').val();
          var beatfilter = $('#beatfilterdrop').val();
          var element = $('.employee_filters').find('option:selected'); 
          var empVal = element.val();
          var outlet_search = 1;
          if(empVal=="null"){
            empVal = null;
          }

          if(element.data('outlet')!=undefined){
            outlet_search = 0;
          }
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          @if($partyTypeLevel)
          var orderTo = $('.orderToFilters').find('option:selected').val();
          if(orderTo=="null"){
            orderTo = null;
          }
          @else
          var orderTo = null;
          @endif
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          $('#order').DataTable().destroy();
          sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
            "empSel": empVal,
            "partySel": partyVal,
            "start": start,
            "end": end,
            "stsVal": stsValues,
            "outlet_search": outlet_search,
            "beatVal": beatfilter
          }));
          initializeDT(empVal, partyVal, start, end, stsValues, outlet_search, beatfilter, orderTo);
        },
        onSelectAll: function(option, checked, select) {
          let stsValues = $('#stsfilterdrop').val();
          var beatfilter = $('#beatfilterdrop').val();
          var element = $('.employee_filters').find('option:selected'); 
          var empVal = element.val();
          var outlet_search = 1;
          if(empVal=="null"){
            empVal = null;
          }

          if(element.data('outlet')!=undefined){
            outlet_search = 0;
          }
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          @if($partyTypeLevel)
          var orderTo = $('.orderToFilters').find('option:selected').val();
          if(orderTo=="null"){
            orderTo = null;
          }
          @else
          var orderTo = null;
          @endif
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          $('#order').DataTable().destroy();
          sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
            "empSel": empVal,
            "partySel": partyVal,
            "start": start,
            "end": end,
            "stsVal": stsValues,
            "outlet_search": outlet_search,
            "beatVal": beatfilter
          }));
          initializeDT(empVal, partyVal, start, end, stsValues, outlet_search, beatfilter, orderTo);
        },
        onDeselectAll: function (justVisible, triggerOnDeselectAll) {
          let stsValues = $('#stsfilterdrop').val();
          var beatfilter = $('#beatfilterdrop').val();
          let element = $('.employee_filters').find('option:selected'); 
          var empVal = element.val();
          var outlet_search = 1;
          if(empVal=="null"){
            empVal = null;
          }

          if(element.data('outlet')!=undefined){
            outlet_search = 0;
          }
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          @if($partyTypeLevel)
          var orderTo = $('.orderToFilters').find('option:selected').val();
          if(orderTo=="null"){
            orderTo = null;
          }
          @else
          var orderTo = null;
          @endif
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          $('#order').DataTable().destroy();
          sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
            "empSel": empVal,
            "partySel": partyVal,
            "start": start,
            "end": end,
            "stsVal": stsValues,
            "outlet_search": outlet_search,
            "beatVal": beatfilter
          }));
          initializeDT(empVal, partyVal, start, end, stsValues, outlet_search, beatfilter, orderTo);
        },
      });

      $('#beatfilterdrop').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: true,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        numberDisplayed: 1,
        nonSelectedText:"Select Beats",
        allSelectedText:"Select Beats",
        onChange: function(option, checked, select) {
          let stsValues = $('#stsfilterdrop').val();
          var beatfilter = $('#beatfilterdrop').val();
          var element = $('.employee_filters').find('option:selected'); 
          var empVal = element.val();
          var outlet_search = 1;
          if(empVal=="null"){
            empVal = null;
          }

          if(element.data('outlet')!=undefined){
            outlet_search = 0;
          }
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          @if($partyTypeLevel)
          var orderTo = $('.orderToFilters').find('option:selected').val();
          if(orderTo=="null"){
            orderTo = null;
          }
          @else
          var orderTo = null;
          @endif
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          $('#order').DataTable().destroy();

          sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
            "empSel": empVal,
            "partySel": partyVal,
            "start": start,
            "end": end,
            "stsVal": stsValues,
            "outlet_search": outlet_search,
            "beatVal": beatfilter
          }));

          initializeDT(empVal, partyVal, start, end, stsValues, outlet_search, beatfilter, orderTo);
        },
        onSelectAll: function(option, checked, select) {
          let stsValues = $('#stsfilterdrop').val();
          var beatfilter = $('#beatfilterdrop').val();
          var element = $('.employee_filters').find('option:selected'); 
          var empVal = element.val();
          var outlet_search = 1;
          if(empVal=="null"){
            empVal = null;
          }

          if(element.data('outlet')!=undefined){
            outlet_search = 0;
          }
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          @if($partyTypeLevel)
          var orderTo = $('.orderToFilters').find('option:selected').val();
          if(orderTo=="null"){
            orderTo = null;
          }
          @else
          var orderTo = null;
          @endif
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          $('#order').DataTable().destroy();

          sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
            "empSel": empVal,
            "partySel": partyVal,
            "start": start,
            "end": end,
            "stsVal": stsValues,
            "outlet_search": outlet_search,
            "beatVal": beatfilter
          }));

          initializeDT(empVal, partyVal, start, end, stsValues, outlet_search, beatfilter, orderTo);
        },
        onDeselectAll: function (justVisible, triggerOnDeselectAll) {
          let stsValues = $('#stsfilterdrop').val();
          var beatfilter = $('#beatfilterdrop').val();
          let element = $('.employee_filters').find('option:selected'); 
          var empVal = element.val();
          var outlet_search = 1;
          if(empVal=="null"){
            empVal = null;
          }

          if(element.data('outlet')!=undefined){
            outlet_search = 0;
          }
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          @if($partyTypeLevel)
          var orderTo = $('.orderToFilters').find('option:selected').val();
          if(orderTo=="null"){
            orderTo = null;
          }
          @else
          var orderTo = null;
          @endif
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          $('#order').DataTable().destroy();

          sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
            "empSel": empVal,
            "partySel": partyVal,
            "start": start,
            "end": end,
            "stsVal": stsValues,
            "outlet_search": outlet_search,
            "beatVal": beatfilter
          }));

          initializeDT(empVal, partyVal, start, end, stsValues, outlet_search, beatfilter, orderTo);
        },
      });

      @if($nCal==0)
        var start = moment().subtract(30, 'days');
        var end = moment();
        $('#start_edate').val(start.format('YYYY-MM-DD'));
        $('#end_edate').val(end.format('YYYY-MM-DD'));
        function cb(start, end) {
          $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
          $('#startdate').val(start.format('MMMM D, YYYY'));
          $('#enddate').val(end.format('MMMM D, YYYY'));
        }
        $('#reportrange').daterangepicker({
          startDate: start,
          endDate: end,
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          }
        }, cb);
        cb(start, end);
        $('#delivery_datenew').datepicker({
          format: 'yyyy-mm-dd',
          todayHighlight: true,
          autoclose: true,
        });
        $.fn.dataTable.ext.search.push(
          function (settings, data, dataIndex) {
            var start2 = $('#reportrange').data('daterangepicker').startDate;
            var end2 = $('#reportrange').data('daterangepicker').endDate;
            var start_date = Date.parse(start2.format('MMMM D, YYYY'));
            var end_date = Date.parse(end2.format('MMMM D, YYYY'));
            var create_date = Date.parse(data[2]); // use data for the age column
            if (create_date >= start_date && create_date <= end_date) {
              return true;
            }
            return false;
          }
        );
        $(document).on('click', '.edit-modal', function () {
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Change Status');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#myModal').modal('show');
          $('#order_id').val($(this).data('id'));
          $('#delivery_status_id').val($(this).data('status'));
          $('#transport_name').val($(this).data('transport_name'));
          $('#transport_number').val($(this).data('transport_number'));
          $('#billty_number').val($(this).data('billty_number'));
          $('#delivery_datenew').val($(this).data('orderdate'));
          $('#delivery_place').val($(this).data('place'));
          $('#delivery_note').val($(this).data('note'));
          if($(this).data('include_delivery_details')){
            $('input[name=include_delivery_details]').prop('checked', true);
          }else{
            $('input[name=include_delivery_details]').prop('checked', false);
          }
        });
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
          var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          $('#start_edate').val(start);
          $('#end_edate').val(end);
          var element = $('.employee_filters').find('option:selected');
          var outlet_search = 1;
          var empVal = element.val();
          if(empVal=="null"){
            empVal = null;
          }

          if(element.data('outlet')!=undefined){
            outlet_search = 0;
          }
          var partyVal = $('.party_filters').find('option:selected').val();
          if(partyVal=="null"){
            partyVal = null;
          }
          var start = $('#start_edate').val();
          var end = $('#end_edate').val();
          var stsfilter = $('#stsfilterdrop').val();
          var beatfilter = $('#beatfilterdrop').val();
          @if($partyTypeLevel)
          var orderTo = $('.orderToFilters').find('option:selected').val();
          if(orderTo=="null"){
            orderTo = null;
          }
          @else
          var orderTo = null;
          @endif
          if(start != '' || end != '')
          {
            $('#order').DataTable().destroy();

            sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
              "empSel": empVal,
              "partySel": partyVal,
              "start": start,
              "end": end,
              "stsVal": stsfilter,
              "outlet_search": outlet_search,
              "beatVal": beatfilter
            }));

            initializeDT(empVal, partyVal, start, end, stsfilter, outlet_search, beatfilter, orderTo);
          }
          if(stsfilter.length>0 ) $('#stsfilterdrop').val(stsfilter);
        });
        $("#delivery_datenew").datepicker({
          format: "yyyy-mm-dd",
          startDate: new Date(),
          autoclose: true,
        });
        $('#reportrange').removeClass('hidden');
      @else
        $('#delivery_ndate').nepaliDatePicker({
          onChange:function(){
            $('#delivery_edate').val(BS2AD($('#delivery_ndate').val()));
          }
        });
        $(document).on('click', '.edit-modal', function () {
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Change Status');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#myModal').modal('show');
          $('#order_id').val($(this).data('id'));
          $('#delivery_status_id').val($(this).data('status'));
          $('#transport_name').val($(this).data('transport_name'));
          $('#transport_number').val($(this).data('transport_number'));
          $('#billty_number').val($(this).data('billty_number'));
          $('#delivery_edate').val(BS2AD($(this).data('orderdate')));
          $('#delivery_ndate').val($(this).data('nodate'));
          $('#delivery_place').val($(this).data('place'));
          $('#delivery_note').val($(this).data('note'));
          if($(this).data('include_delivery_details')){
            $('input[name=include_delivery_details]').prop('checked', true);
          }else{
            $('input[name=include_delivery_details]').prop('checked', false);
          }
        });
        var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
        var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
        $('#start_ndate').val(lastmonthdate);
        $('#end_ndate').val(ntoday);
        $('#nepCalDiv').removeClass('hidden');
        $('#start_edate').val(BS2AD($('#start_ndate').val()));
        $('#end_edate').val(BS2AD($('#end_ndate').val()));
        $('#start_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          onChange:function(){
            $('#start_edate').val(BS2AD($('#start_ndate').val()));
            if($('#start_ndate').val()>$('#end_ndate').val()){
              $('#end_ndate').val($('#start_ndate').val());
              $('#end_edate').val(BS2AD($('#start_ndate').val()));
            }
            var element = $('.employee_filters').find('option:selected');
            var empVal = element.val();
            var outlet_search = 1;
            if(empVal=="null"){
              empVal = null;
            }

            if(element.data('outlet')!=undefined){
              outlet_search = 0;
            }
            var partyVal = $('.party_filters').find('option:selected').val();
            if(partyVal=="null"){
              partyVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            var stsfilter = $('#stsfilterdrop').val();
            var beatfilter = $('#beatfilterdrop').val();
            @if($partyTypeLevel)
            var orderTo = $('.orderToFilters').find('option:selected').val();
            if(orderTo=="null"){
              orderTo = null;
            }
            @else
            var orderTo = null;
            @endif
            if(end==""){
              end = start;
            }
            if(start != '' || end != '')
            {
              $('#order').DataTable().destroy();

              sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
                "empSel": empVal,
                "partySel": partyVal,
                "start": start,
                "end": end,
                "stsVal": stsfilter,
                "outlet_search": outlet_search,
                "beatVal": beatfilter
              }));

              initializeDT(empVal, partyVal, start, end, stsfilter, outlet_search, beatfilter, orderTo);
            }
          }
        });
        $('#end_ndate').nepaliDatePicker({
          onChange:function(){
            $('#end_edate').val(BS2AD($('#end_ndate').val()));
            if($('#end_ndate').val()<$('#start_ndate').val()){
              $('#start_ndate').val($('#end_ndate').val());
              $('#start_edate').val(BS2AD($('#end_ndate').val()));
            }
            var outlet_search = 1;
            var element = $('.employee_filters').find('option:selected');
            var empVal = element.val();
            if(empVal=="null"){
              empVal = null;
            }

            if(element.data('outlet')!=undefined){
              outlet_search = 0;
            }
            var partyVal = $('.party_filters').find('option:selected').val();
            if(partyVal=="null"){
              partyVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            var stsfilter = $('#stsfilterdrop').val();
            var beatfilter = $('#beatfilterdrop').val();
            @if($partyTypeLevel)
            var orderTo = $('.orderToFilters').find('option:selected').val();
            if(orderTo=="null"){
              orderTo = null;
            }
            @else
            var orderTo = null;
            @endif
            if(start==""){
              start = end;
            }
            if(start != '' || end != '')
            {
              $('#order').DataTable().destroy();

              sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
                "empSel": empVal,
                "partySel": partyVal,
                "start": start,
                "end": end,
                "stsVal": stsfilter,
                "outlet_search": outlet_search,
                "beatVal": beatfilter
              }));

              initializeDT(empVal, partyVal, start, end, stsfilter, outlet_search, beatfilter, orderTo);
            }
          }
        });
      @endif
      let table;
      let empSel = null;
      let outEmpSelVal = empSel;
      let outlet_search = 1;
      let partySel = null;
      let beatVal = $('#beatfilterdrop').val();
      
      var orderTo = null;
      start = $('#start_edate').val();
      end = $('#end_edate').val();
      let stsVal = $('#stsfilterdrop').val();
      
      @if (\Session::has('DT_Ord_filters'))
        let filtersSel = @json(\Session::get('DT_Ord_filters'));
        sessionStorage.setItem('DT_Ord_filters', filtersSel);
      @else
        sessionStorage.setItem('DT_Ord_filters', "");
      @endif

      if(sessionStorage.getItem('DT_Ord_filters')!="" || !sessionStorage.getItem('DT_Ord_filters')==undefined ){
        let filterValue = JSON.parse(sessionStorage.getItem('DT_Ord_filters'));
        if(filterValue){
          empSel = filterValue.empSel;
          outEmpSelVal = empSel;
          partySel = filterValue.partySel;
          outlet_search = filterValue.outlet_search;
          if(outlet_search==0){
            outEmpSelVal = null;
          }
          start = filterValue.start;
          end = filterValue.end;
          @if($nCal==0)
            $('#start_edate').val(moment(start).format('YYYY-MM-DD'));
            $('#end_edate').val(moment(end).format('YYYY-MM-DD'));
            cb(moment(start), moment(end));
          @else
            $('#start_ndate').val(AD2BS(moment(start).format('YYYY-MM-DD')));
            $('#end_ndate').val(AD2BS(moment(end).format('YYYY-MM-DD')));
            $('#start_edate').val(moment(start).format('YYYY-MM-DD'));
            $('#end_edate').val(moment(end).format('YYYY-MM-DD'));
          @endif
          beatVal = filterValue.beatVal;
          stsVal = filterValue.stsVal;
          $('#beatfilterdrop').val(beatVal).multiselect("refresh");
          $('#stsfilterdrop').val(stsVal).multiselect("refresh");
          // sessionStorage.setItem('DT_Ord_filters', "");
          sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
            "empSel": empSel,
            "partySel": partySel,
            "start": start,
            "end": end,
            "stsVal": stsVal,
            "outlet_search": outlet_search,
            "beatVal": beatVal
          }));
        }
      }else{
        sessionStorage.setItem('DT_Ord_filters', "");
      }

      // Load Data Table on ready 
      initializeDT(outEmpSelVal, partySel, start, end, stsVal, outlet_search, beatVal, orderTo);
      
      $('.stsFilterDom').removeClass("hidden");
      
      @if(!(empty($employeesWithOrders)))
        var empSelect = "<select id='employee_filters' class='employee_filters'><option></option><option value=null>All</option><optgroup label='Employee'>@foreach($employeesWithOrders as $id=>$employee)<option value='{{$id}}'>{{$employee}}</option>@endforeach</optgroup> @if(!empty($outlet_contacts)) <optgroup label='Outlet Contact Person'>@foreach($outlet_contacts as $id=>$contact_person)<option value='{{$id}}' data-outlet='{{0}}'>{{$contact_person}}</option>@endforeach</optgroup> @endif</select>";
      @endif
      $('#salesmfilter').append(empSelect);
      if(empSel!=""){
        $('#employee_filters').val(empSel);
      };
      $('#employee_filters').select2({
        "placeholder": "Select Creator",
      });

      @if(!(empty($partiesWithOrders)))
        var partySelect = "<select id='party_filters' class='party_filters'><option></option><option value=null>All</option><optgroup label='Parties'>@foreach($partiesWithOrders as $id=>$parties)<option value='{{$id}}'>{{$parties}}</option>@endforeach</optgroup><optgroup label='Beats'></optgroup></select>";
      @endif
      $('#partyfilter').append(partySelect);
      if(partySel!="") $('#party_filters').val(partySel);
      $('#party_filters').select2({
        "placeholder": "Select Parties",
      });
      @if($partyTypeLevel)
        @if(!(empty($orderToParties)))
          var orderTSelect = "<select id='orderToFilters' class='orderToFilters'><option></option><option value=null>All</option><optgroup label='Company'><option value='0'>{{Auth::user()->companyName($company_id)->company_name}}</option></optgroup><optgroup label='Parties'>@foreach($orderToParties as $id=>$parties)<option value='{{$id}}'>{{$parties}}</option>@endforeach</optgroup></select>";
        @endif
        $('#orderTofilter').append(orderTSelect);
        
        $('#orderToFilters').select2({
          "placeholder": "Ordered To ",
        });
      @endif

      function getSelVal(){
        return $('#pageIds').val();
      }

      function initializeDT(empVal=null, partyVal=null, startD, endD, stsFilters, outlet_search, beatFilters, orderTo){
        table = $('#order').DataTable({
          "stateSave": true,
          "stateSaveParams": function (settings, data) {
            data.search.search = "";
          },
          "language": { search: "" },
          "order": [[ 2, "desc" ]],
          "serverSide": true,
          "processing": true,
          "searching": true,
          "paging": true,
          "dom":  "<'col-xs-12'<'row'<'col-xs-10 alignleft'><'col-xs-2 alignright'Bf>>>" 
                  +"<'row'<'col-xs-12'tr>>" +
                  "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
            {
              "orderable": false,
              "targets":[0,-1],
            },],

          "buttons": [
              {
                  extend: 'colvis',
                  order: 'alpha',
                  className: 'dropbtn',
                  columns:[0,1,2,3,4,5,6],
                  text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                  columnText: function ( dt, idx, title ) {
                      return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                  }
              },

            {
              extend: 'pdfHtml5', 
              title: 'Order List', 
              
              exportOptions: {
                columns: ':visible:not(:last-child)'
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Order List', 
              
              exportOptions: {
                columns: ':visible:not(:last-child)'
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Order List', 
              
              exportOptions: {
                columns: ':visible:not(:last-child)'
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.order.ajaxDatatable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              empVal : empVal,
              partyVal : partyVal,
              orderTo:orderTo,
              startDate: startD,
              endDate: endD, 
              stsFilters: stsFilters,
              selIds: getSelVal,
              orderCount: '{{$ordersCount}}',
              outlet_search: outlet_search,
              beat_ids: beatFilters,
            },
            beforeSend:function(url, data){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
              $('.tips').tooltip();
            },
            error:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
              $('.tips').tooltip();
            },
            complete:function(data){
              if(data.status==200){
                let tdata = data.responseJSON;
                if(tdata.data.length>0){
                  $("#selectthispage").prop("checked", tdata.selectThisPageCheckBox);
                }
              }
              $('.tips').tooltip();
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": columns,
          drawCallback:function(settings)
          {
            $('#totalValue').html(settings.json.total);
            $('#pageIds').html(settings.json.prevSelVal);
          }
        });
        table.buttons().container()
            .appendTo('#orderexports');
        var oldExportAction = function (self, e, dt, button, config) {
          if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
            if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
          }
        };

        var newExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$ordersCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                var columnsArray = [];
                var visibleColumns = settings.aoColumns.map(setting => {
                                        if(setting.bVisible){
                                          columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                                        } 
                                      })    
                columnsArray.pop("Action")
                @if(!$partyTypeLevel)
                columnsArray = columnsArray.filter(column => column!="Ordered To")
                @endif
                @if(getClientSetting()->order_with_amt==1)
                columnsArray = columnsArray.filter(column => column!="Grand Total")
                @endif
                // columnsArray.push("S.No.", "Party Name", "Salesman", "Date", "Remark");
                var columns = JSON.stringify(columnsArray);
                if("{{config('settings.company_id')}}" == 184){
                  oldExportAction(self, e, dt, button, config);
                }else{
                  $.each(settings.json.data, function(key, htmlContent){
                    settings.json.data[key].id = key+1;
                    settings.json.data[key].orderno = $(settings.json.data[key].orderno)[0].textContent;
                    settings.json.data[key].partyname = $(settings.json.data[key].partyname)[0].textContent;
                    settings.json.data[key].ordered_to = $(settings.json.data[key].ordered_to)[0].textContent;
                    settings.json.data[key].createdby = $(settings.json.data[key].createdby)[0].textContent;
                    settings.json.data[key].orderstatus = $(settings.json.data[key].orderstatus)[0].textContent; 
                  });
                  customExportAction(config, settings,columns);
                }
              }else{
                oldExportAction(self, e, dt, button, config);
              }
              dt.one('preXhr', function (e, s, data) {
                  settings._iDisplayStart = oldStart;
                  data.start = oldStart;
                  $('#mainBox').removeClass('box-loader');
                  $('#loader1').attr('hidden', 'hidden');
              });
              setTimeout(dt.ajax.reload, 0);
              return false;
            });
          });
          dt.ajax.reload();
        }
      } // Data Table initialize 
      
      function customExportAction(config, settings, cols){
        $('#exportedData').val(JSON.stringify(settings.json));
        $('#pageTitle').val(config.title);
        // $('#reportName').val(modName);
        $('#columns').val(cols);
        var propertiesArray = [];
        var visibleColumns = settings.aoColumns.map(setting => {
                              if(setting.bVisible) propertiesArray.push(setting.data)
                            })
        propertiesArray.pop("action")
        @if(!$partyTypeLevel)
        propertiesArray = propertiesArray.filter(property => property!="ordered_to")
        @endif
        @if(getClientSetting()->order_with_amt==1)
        propertiesArray = propertiesArray.filter(property => property!="grandtotal")
        @endif
        // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
        var properties = JSON.stringify(propertiesArray);
        $('#properties').val(properties);
        $('#pdf-generate').submit();
      }

      $('body').on("change", ".employee_filters",function () {
        var element = $(this).find('option:selected'); 
        var empVal = element.val();
        var outlet_search = 1;
        if(empVal=="null"){
          empVal = null;
        }

        if(element.data('outlet')!=undefined){
          outlet_search = 0;
        }
        var partyVal = $('.party_filters').find('option:selected').val();
        if(partyVal=="null"){
          partyVal = null;
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        var stsfilter = $('#stsfilterdrop').val();
        var beatfilter = $('#beatfilterdrop').val();
        @if($partyTypeLevel)
        var orderTo = $('.orderToFilters').find('option:selected').val();
        if(orderTo=="null"){
          orderTo = null;
        }
        @else
        var orderTo = null;
        @endif
        sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
          "empSel": empVal,
          "partySel": partyVal,
          "start": start,
          "end": end,
          "stsVal": stsfilter,
          "outlet_search": outlet_search,
          "beatVal": beatfilter
        }));
        
        if(empVal != '')
        {
          $('#order').DataTable().destroy();
          initializeDT(empVal, partyVal, start, end, stsfilter, outlet_search, beatfilter, orderTo);
        }
      });

      $('body').on("change", ".party_filters",function () {
        var element = $('.employee_filters').find('option:selected');
        var empVal = element.val();
        var outlet_search = 1;
        if(empVal=="null"){
          empVal = null;
        }

        if(element.data('outlet')!=undefined){
          outlet_search = 0;
        }
        var partyVal = $(this).find('option:selected').val();
        if(partyVal=="null"){
          partyVal = null;
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        var stsfilter = $('#stsfilterdrop').val();
        var beatfilter = $('#beatfilterdrop').val();
        @if($partyTypeLevel)
        var orderTo = $('.orderToFilters').find('option:selected').val();
        if(orderTo=="null"){
          orderTo = null;
        }
        @else
        var orderTo = null;
        @endif
        sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
          "empSel": empVal,
          "partySel": partyVal,
          "start": start,
          "end": end,
          "stsVal": stsfilter,
          "outlet_search": outlet_search,
          "beatVal": beatfilter
        }));

        if(partyVal != '')
        {
          $('#order').DataTable().destroy();
          initializeDT(empVal, partyVal, start, end, stsfilter, outlet_search, beatfilter, orderTo);
        }
      });
      @if($partyTypeLevel)
      $('body').on("change", ".orderToFilters",function () {
        var element = $('.employee_filters').find('option:selected');
        var empVal = element.val();
        var outlet_search = 1;
        if(empVal=="null"){
          empVal = null;
        }

        if(element.data('outlet')!=undefined){
          outlet_search = 0;
        }
        var partyVal = $('.party_filters').find('option:selected').val();
        if(partyVal=="null"){
          partyVal = null;
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        var stsfilter = $('#stsfilterdrop').val();
        var beatfilter = $('#beatfilterdrop').val();

        var orderTo = $('.orderToFilters').find('option:selected').val();
        if(orderTo=="null"){
          orderTo = null;
        }
        
        sessionStorage.setItem('DT_Ord_filters', JSON.stringify({
          "empSel": empVal,
          "partySel": partyVal,
          "start": start,
          "end": end,
          "stsVal": stsfilter,
          "outlet_search": outlet_search,
          "beatVal": beatfilter
        }));

        if(orderTo != '')
        {
          $('#order').DataTable().destroy();
          initializeDT(empVal, partyVal, start, end, stsfilter, outlet_search, beatfilter, orderTo);
        }
      });
      @endif
    });

    $('#changeDeliveryStatus').on('submit',function(){
      $('#btn_status_change').attr('disabled',true);
    });
    //responsive 
    $('#reportrange').on('click',function(){
      if ($(window).width() <= 320) {   
        $(".daterangepicker").addClass("orderdateposition");
        
      }
      else if ($(window).width() <= 768) {
        $(".daterangepicker").addClass("orderdateposition");
      }
      else {   
        $(".daterangepicker").removeClass("orderdateposition");
      }
    });

    $(document).on('click', '.updateStatuses', function () {
      $('#footer_action_button').addClass('glyphicon-check');
      $('.modal-title').text('Update Multiple Status');
      $('#order_id').val($(this).data('id'));
      const orderIds = $('#pageIds').val();
      if(orderIds==""){
        alert("Please Select Orders.");
      }else{
        $('#upateStatuses').modal('show');
        $('#order_ids').val(orderIds);
      }
    });

    function pushOrderIds(){
      let order_ids = [];
      $.each($("input[name='update_order_status']:checked"), function(){
        order_ids.push($(this).val());
      });
      return order_ids;
    }

    $('body').on('change', '.orderStatusCheckBox',function(){
      if(this.checked){
        let currentVal = $('#pageIds').val();
        let getCheckedIds = pushOrderIds();
        if(currentVal!=""){
          currentVal = currentVal.split(',');
          $.each(currentVal, function(ind, val){
            if(!getCheckedIds.includes(val)){
              getCheckedIds.push(val);
            }
          });
        }
        $('#pageIds').val(getCheckedIds);
        if($("input[name='update_order_status']").not(':checked').length==0) $("#selectthispage").prop("checked", true);
      
      }else{
        let uncheckVal = $(this).val();
        let currentVal = $('#pageIds').val().split(',');
        let newVal = currentVal.filter(function(value, index, arr){
                        return value != uncheckVal;
                    });
        $('#pageIds').val(newVal);
        $("#selectthispage").prop("checked", false);
      }
    });
    $('#selectthispage').click(function(event){
      event.stopPropagation();
      if($("input[name='update_order_status']").length==0) $("#selectthispage").prop("checked", false);
      if(this.checked){
        $("input[name='update_order_status']").prop("checked", true);
        let currentVal = $('#pageIds').val();
        let getCheckedIds = pushOrderIds();
        if(currentVal!=""){
          currentVal = currentVal.split(',');
          $.each(currentVal, function(ind, val){
            if(!getCheckedIds.includes(val)){
              getCheckedIds.push(val);
            }
          });
        }
        $('#pageIds').val(getCheckedIds);
      }else{
        $("input[name='update_order_status']").prop("checked", false);
        let uncheckedBoxes = $("input[name='update_order_status']").not(':checked');
        let uncheckVal = [];
        $.each($("input[name='update_order_status']").not(':checked'), function(){
          uncheckVal.push($(this).val());
        });
        let currentVal = $('#pageIds').val().split(',');
        let newVal = currentVal.filter(function(value, index, arr){
                      return !uncheckVal.includes(value);
                    });
        $('#pageIds').val(newVal);
        $("#selectthispage").prop("checked", false);
      }
    });

    $(document).on('click','.alert-modal',function(){
      $('#alertModal').modal('show');
    });

    $(document).on('click','.mass_action',function(){
      $('#mass_email_alert').addClass('hide');
      $('#massForm')[0].reset();
      const orderIds = $('#pageIds').val();
      var data_type = $(this).data('type');
      if(orderIds==""){
        alert("Please Select Orders.");
      }else{
        $('#massModal').modal('show');
        if(data_type=="massdownload"){

          $('#massTitle').html('Download Invoices.');
          $('#massdetails').html('Press download to continue downloading invoices for selected orders.');
          $('#massemail').addClass('hide');
          $('.key_massdownload').removeClass('hide');
          $('.key_massmail').addClass('hide');
          $('.key_massmaildownload').addClass('hide');
          $('.key_massdelete').addClass('hide');

        }else if(data_type=="massmail"){

          $('#massTitle').html('Mail Invoices.');
          $('#massdetails').html('');
          $('#massemail').removeClass('hide');
          $('.key_massmail').removeClass('hide');
          $('.key_massdownload').addClass('hide');
          $('.key_massmaildownload').addClass('hide');
          $('.key_massdelete').addClass('hide');          
        
        }else if(data_type == "massmaildownload"){

          $('#massTitle').html('Mail and Download Invoices');
          $('#massdetails').html('');
          $('#massemail').removeClass('hide');
          $('.key_massmaildownload').removeClass('hide');
          $('.key_massmail').addClass('hide');
          $('.key_massdownload').addClass('hide');
          $('.key_massdelete').addClass('hide');

        }else if(data_type == "massdelete"){
          $('#massTitle').html('Are you sure you want to delete these orders?');
          $('#massdetails').html('Press mass delete to continue deleting invoices for selected orders.');
          $('#massemail').addClass('hide');
          $('.key_massdelete').removeClass('hide');
          $('.key_massdownload').addClass('hide');
          $('.key_massmail').addClass('hide');
          $('.key_massmaildownload').addClass('hide');
        }
        $('#mass_order_ids').val(orderIds);
      }
    });

    $(document).on('click','.massbuttons',function(){
      $('#mass_order_type').val($(this).data('type'));
    });

    function download_file(fileURL, fileName) {
      // for non-IE
      if (!window.ActiveXObject) {
        var save = document.createElement('a');
        save.href = fileURL;
        save.target = '_blank';
        var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
        save.download = fileName || filename;
         if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
        document.location = save.href; 
        // window event not working here
        }else{
              var evt = new MouseEvent('click', {
                  'view': window,
                  'bubbles': true,
                  'cancelable': false
              });
              save.dispatchEvent(evt);
              (window.URL || window.webkitURL).revokeObjectURL(save.href);
        } 
      }

      // for IE < 11
      else if ( !! window.ActiveXObject && document.execCommand)     {
          var _window = window.open(fileURL, '_blank');
          _window.document.close();
          _window.document.execCommand('SaveAs', true, fileName || fileURL)
          _window.close();
      }
    }

    $('#massForm').on('submit',function(e){
      e.preventDefault();
      var url = $(this).attr('action');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: "POST",
          data: new FormData(this),
          contentType: false,
          cache: false,
          processData: false,
          beforeSend:function(){
            $('.massbuttons').attr('disabled',true);
            console.log('ajax_begun');
          },
          success: function (data) {
            if(data.status==true){
              if(data.type=="massmail" || data.type=="massmaildownload"){
                $('#massForm')[0].reset();
                alert('Mail Sent Successfully');
              }
              if(data.type=="massdelete"){
                var message="";
                if(data.deletedIDs.length>0){
                  message = 'Order: '+data.deletedIDs.toString()+' have been successfully deleted.';
                }
                if(data.safeIDs.length>0){
                  var message = message+' Order:'+data.safeIDs.toString()+' could not be deleted';
                }
                alert(message);
                if(data.deletedIDs.length>0){
                  window.location="{{domain_route('company.admin.order')}}";
                }
              }
              if(data.type=="massdownload" || data.type=="massmaildownload"){
                var downloadurl = "{{ URL::asset('')}}/"+data.urls;
                download_file(downloadurl);
              }
            $('#massModal').modal('hide');
            }
            if(data.status==false){
              if(data.type=="massmail" || data.type=="massmaildownload"){
                $('#mass_email_alert').removeClass('hide');
              }
            }
            console.log('ajax_success');
            $('.massbuttons').attr('disabled',false);
          },
          error:function(){
            $('.massbuttons').attr('disabled',false);
            console.log('ajax_failed');
          }
        });
    });

  $(document).on('click','.buttons-columnVisibility',function(){
      if($(this).hasClass('active')){
          $(this).find('input').first().prop('checked',true);
          console.log($(this).find('input').first().prop('checked'));
      }else{
          $(this).find('input').first().prop('checked',false);
          console.log($(this).find('input').first().prop('checked'));
      }
  });

  $(document).on('click','.buttons-colvis',function(e){
      var filterBox = $('.dt-button-collection');
      filterBox.find('li').each(function(k,v){
          if($(v).hasClass('active')){
              $(v).find('input').first().prop('checked',true);
          }else{
              $(v).find('input').first().prop('checked',false);
          }
      });
  });

</script>

@endsection