@extends('layouts.company')
@section('title', 'Employee Details')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}"  media="print">
<link rel="stylesheet" type="text/css" href="{{asset('assets/plugins/zoomImage/zoomer.css')}}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<style>
/*tree view*/
#tree1.tree, .tree ul {
  margin: 0;
  padding: 0;
  list-style: none
}
#tree1.tree ul {
  margin-left: 1em;
  position: relative
}
#tree1.tree ul ul {
  margin-left: .5em
}
#tree1.tree ul:before {
  content: "";
  display: block;
  width: 0;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  border-left: 1px solid
}
#tree1.tree li {
  margin: 0;
  padding: 0 1em;
  line-height: 2em;
  color: #369;
  font-weight: 700;
  position: relative
}
#tree1.tree ul li:before {
  content: "";
  display: block;
  width: 10px;
  height: 0;
  border-top: 1px solid;
  margin-top: -1px;
  position: absolute;
  top: 1em;
  left: 0
}
#tree1.tree ul li:last-child:before {
  background: #fff;
  height: auto;
  top: 1em;
  bottom: 0
}
#tree1.indicator {
  margin-right: 5px;
}
#tree1.tree li a {
  text-decoration: none;
  color: #369;
}
#tree1.tree li button, .tree li button:active, .tree li button:focus {
  text-decoration: none;
  color: #369;
  border: none;
  background: transparent;
  margin: 0px 0px 0px 0px;
  padding: 0px 0px 0px 0px;
  outline: 0;
}
#tree1 li .btn{
  font-size: 16px!important;
  padding: 0px 1px 10px 5px;
}
.ms-options-wrap > .ms-options > ul input[type="checkbox"] {
  margin: 15px 0px 0 0;
  /* position: absolute; */
  left: 4px;
  top: -5px;
}
.ms-options-wrap > .ms-options {
  position: absolute;
  left: 15px;
  width: unset;
}
.ms-options{
  min-height: fit-content !important ;
}
/*end tree view*/
.list_type{
  height: fit-content;
}
.dropdown-menu>.disabled>a, .dropdown-menu>.disabled>a:focus, .dropdown-menu>.disabled>a:hover{
  color: #fff !important;
  background-color: #337ab7;
}
.multiselect-item.multiselect-group label input{
  height:auto;
}
.nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus {
    border-top-color: #0b7676;
}
.nav-pills>li.active>a, .nav-pills>li.active>a:focus, .nav-pills>li.active>a:hover {
    color: #fff;
    background-color: #0b7676;
}
.nav-pills > li > a {
    border-radius: 0;
    border-top: 3px solid transparent;
    color: #0b7676;
    background-color: #ecf0f5;
    padding: 10px 17px !important;
}
.table-fix{
    margin-top: -60px;
}

div.dataTables_wrapper div.dataTables_filter {
     text-align: left; 
}

.box-header{
  position: static;
  padding: none;
}

.bottom-border{
  border:none;
  border-bottom:1px solid #ccc; 
  padding-bottom: 5px;
}

#grandTotalAmount,#grandTotalCAmount,#grandTotalEAmount{
  margin-left: 60%;
  line-height: 3;
}

.nav-tabs-custom {
  box-shadow: none;
}

.nav-pills > li.active > a {
    font-weight: 400;
}

#ActivateUpdate {
  margin-right: 10px;
  display: inline-block;
  float: right;
}

.fullwidth{
  width: 100%!important;
}

.fc-time{
  display: none;
}    

.panel-heading{
  padding:1px 15px;
}
.check{
  height: 16px;
}

.btn-red{
  color: red;
}

.box-loader{
  opacity: 0.5;
}

.small-box > .inner > h3 {
  font-size: 18px;
}
.disabled.active{background-color: aliceblue !important;}

.dropdown-menu>.disabled>a, .dropdown-menu>.disabled>a:focus, .dropdown-menu>.disabled>a:hover {
  color: #080808 !important;
  background-color: #eef1ec;
}
#employeetabs ul li a {
    padding: 10px 20px;
}
.small-box > .inner > h3 {
  font-size: 18px;
}

/*design for activity checkbox*/
.round {
    position: relative;
}

.round label {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 50%;
    cursor: pointer;
    height: 28px;
    left: 0;
    position: absolute;
    top: 0px;
    width: 28px;
}

.round label:after {
    border: 2px solid #fff;
    border-top: none;
    border-right: none;
    content: "";
    height: 6px;
    left: 7px;
    opacity: 0;
    position: absolute;
    top: 8px;
    transform: rotate(-45deg);
    width: 12px;
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
.logged_in{
  margin-top: 30px;
  padding-left: 20px;
  letter-spacing: 1px;
}
.logged_in a{
  text-decoration: underline;
}
/*End activity checkbox design*/
</style>

<script type="text/javascript">
    $(document).ready(function(){
        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#employeetabs a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
@endsection

@section('content')
  <section class="content">

    <div class="box box-default" style="border-top: none;">
    <div class="row">
      <div class="col-xs-1">
        @if(isset($employee->image_path))
          <img id="p_pic" src="{{ URL::asset('cms'.$employee->image_path) }}" class="emp-show-profile-pic display-imglists" alt="{{$employee->name}}"> 
        @else
          @if($employee->gender=='Male')
            <img id="p_pic" class="emp-show-profile-pic display-imglists" src="{{ URL::asset('cms/storage/app/public/uploads/default_m.png') }}" alt="User profile picture">
          @else
            <img id="p_pic" class="emp-show-profile-pic display-imglists" src="{{ URL::asset('cms/storage/app/public/uploads/default_f.png') }}" alt="User profile picture">
          @endif
        @endif
      </div>
      <div class="col-xs-10">
        <span class="text-delta"><h4 style="line-height:3;">{{$employee->name}}</h4></span>
      </div>
      <div class="col-xs-1">
        <div class="box-tools pull-right" style="margin-top: 10px;">
          <div class="col-xs-7 page-action text-right">
            <a href=" {{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      
      <!-- /.col -->
      <div class="col-xs-12">
        <div id="loader1" hidden>
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
        <div class="nav-tabs-custom client-tab" id="employeetabs">
          <ul class="nav nav-pills" id="tabs">
            <li class="active"><a href="#details-tab" name="details-tab" data-toggle="tab">DETAILS</a></li>
            @if(Auth::user()->can('order-view') && config('settings.orders')==1 )
            <li><a href="#orders" name="orders" data-toggle="tab">ORDERS</a></li>
            @endif
            @if(Auth::user()->can('zeroorder-view') && config('settings.zero_orders')==1)
            <li><a href="#zeroorders" data-toggle="tab">ZERO ORDERS</a></li>
            @endif
            @if(Auth::user()->can('collection-view') && config('settings.collections')==1)
            <li><a href="#collection-tab" name="collection-tab" data-toggle="tab">COLLECTIONS</a></li>
            @endif
            @if(Auth::user()->can('activity-view') && config('settings.activities')==1)
            <li><a href="#activity-tab" name="activity-tab" data-toggle="tab">ACTIVITIES</a></li>
            @endif
            @if(Auth::user()->can("expense-view") && config('settings.expenses')==1)
            <li><a href="#expense-tab" name="expense-tab" data-toggle="tab">EXPENSES</a></li>
            @endif
            @if(Auth::user()->can("leave-view") && config('settings.leaves')==1)
            <li><a href="#leave-tab" name="leave-tab" data-toggle="tab">LEAVES</a></li>
            @endif
            @if(Auth::user()->can("dayremark-view") && config('settings.remarks')==1)
            <li><a href="#dayremark-tab" name="dayremark-tab" data-toggle="tab">DAY REMARKS</a></li>
            @endif
            @if(config('settings.party')==1)
            <li><a href="#party-tab" name="party-tab" data-toggle="tab">PARTY HANDLING</a></li>
            @endif
            @if(config('settings.attendance')==1)
            <li><a href="#attendance-tab" name="attendance-tab" data-toggle="tab">ATTENDANCE</a></li>
            @endif
            @if(config('settings.visit_module')==1 && config('settings.party')==1 && Auth::user()->can("PartyVisit-view"))
            <li><a href="#party-visit-tab" name="party-visit-tab" data-toggle="tab">VISITS</a></li>
            @endif
            @if(config('settings.analytics')==1)
            <li><a href="#summary-tab" name="summary-tab" data-toggle="tab">SUMMARY</a></li>
            @endif
          </ul>
          <div class="tab-content">
            <div class="active tab-pane" id="details-tab">
              <ul class="nav nav-tabs" id="subtabs">
                <li class="active"><a href="#general-info-tab" name="general-info-tab" data-toggle="tab">Basic Details</a></li>
                <li><a href="#contact-tab" name="contact-tab" data-toggle="tab">Contact Details</a></li>
                <li><a href="#company_details_tab" name="company_details_tab" data-toggle="tab">Company Details</a></li>
                <li><a href="#bank-Info-tab" name="bank-Info-tab" data-toggle="tab">Bank Account Details</a></li>               
                <li><a href="#document-tab" name="document-tab" data-toggle="tab">Documents</a></li>               
                <li><a href="#Account-Info-tab" name="Account-Info-tab" data-toggle="tab">Account Login</a></li>     
                @if($employee->id!=Auth::user()->EmployeeId() && $employee->is_admin==0 && Auth::user()->can('employee-update'))          
                <li><a href="#transfer-Info-tab" name="transfer-Info-tab" data-toggle="tab">Transfer</a></li>
                @endif
              </ul>
              <div class="tab-content">
                <div class="active tab-pane" id="general-info-tab">
                  @include('company.employees.partial_show.basic')
                </div>
                <div class="tab-pane" id="contact-tab">
                  @include('company.employees.partial_show.contact')
                </div>
                <div class="tab-pane" id="company_details_tab">
                  @include('company.employees.partial_show.company')
                </div>
                <div class="tab-pane" id="bank-Info-tab">
                  @include('company.employees.partial_show.bank')
                </div>
                <div class="tab-pane" id="document-tab">
                  @include('company.employees.partial_show.document')
                </div>
                <div class="tab-pane" id="Account-Info-tab">
                  @include('company.employees.partial_show.account')
                </div>
                @if($employee->id!=Auth::user()->EmployeeId() && $employee->is_admin==0 && Auth::user()->can('employee-update'))  
                <div class="tab-pane" id="transfer-Info-tab">
                  @include('company.employees.partial_show.transfer')
                </div>
                @endif
              </div>
              
            </div>
            @if(Auth::user()->can('order-view') && config('settings.orders')==1)
            <div class="tab-pane" id="orders">
              @include('company.employees.partial_show.orders')
            </div>
            @endif
            @if(Auth::user()->can('zeroorder-view') && config('settings.zero_orders')==1)
            <div class="tab-pane" id="zeroorders">
              @include('company.employees.partial_show.zeroorders')
            </div>
            @endif
            @if(Auth::user()->can('collection-view') && config('settings.collections')==1)
            <div class="tab-pane" id="collection-tab">
              @include('company.employees.partial_show.collections')
            </div>
            @endif
            @if(Auth::user()->can('activity-view') && config('settings.activities')==1)
             <div class="tab-pane" id="activity-tab">
              @include('company.employees.partial_show.activity')
            </div> 
            @endif
            @if(Auth::user()->can('expense-view') && config('settings.expenses')==1)
            <div class="tab-pane" id="expense-tab">
             @include('company.employees.partial_show.expense')
            </div>
            @endif
            @if(Auth::user()->can('leave-view') && config('settings.leaves')==1)
            <div class="tab-pane" id="leave-tab">
              @include('company.employees.partial_show.leave')
            </div>
            @endif
            @if(Auth::user()->can('dayremark-view') && config('settings.remarks')==1)
            <div class="tab-pane" id="dayremark-tab">
              @include('company.employees.partial_show.dayremark')
            </div>
            @endif
            @if(Auth::user()->can('party-view') && config('settings.party')==1)
            <div class="tab-pane" id="party-tab">
              @include('company.employees.partial_show.party')
            </div>
            @endif

            {{-- End of Party Handling  tab --}}
            @if(config('settings.ncal')==0)
              @if(config('settings.attendance')==1)
              <div class="tab-pane" id="attendance-tab">
                @include('company.employees._attandance')
              </div>
              @endif
            @else
              @if(config('settings.attendance')==1)
              <div class="tab-pane" id="attendance-tab">
                @include('company.employees._attandance2')
              </div>
              @endif
            @endif

            @if(config('settings.visit_module')==1 && config('settings.party')==1 && Auth::user()->can("PartyVisit-view"))
              <div class="tab-pane" id="party-visit-tab">
                @include('company.employees.partial_show.party-visit')
              </div>
            @endif  
          
          {{-- End of Attendance Tab --}}

          <!-- Summary Tab -->
          @if(config('settings.analytics')==1)
          <div class="tab-pane" id="summary-tab">
            @include('company.employees.partial_show.summary')
          </div>
          @endif
          <!-- /. End of summary tab -->

          <!-- /.tab-pane -->
          </div>
          <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
      </div>
      <!-- /.col -->
    </div>
    </div>
    <!-- /.row -->

    <div id="myModal" class="modal custommodal">
      <span class="close zoom-close">&times;</span>
      <img class="modal-content zoom-modal-content" id="img01">
      <div id="caption"></div>
    </div>

    <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-keyboard="false" data-backdrop="static">

      <div class="modal-dialog" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
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

              <input type="hidden" name="client_id" id="c_id" value="">


            </div>

            <div class="modal-footer">

              <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>

              <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>

            </div>

          </form>

        </div>

      </div>

    </div>

    <div class="modal modal-default fade" id="deleteDocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-keyboard="false" data-backdrop="static">

      <div class="modal-dialog" role="document">

        <div class="modal-content">

          <div class="modal-header">

            <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
            </button>

            <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>

          </div>

          <form id="delDoc" method="post" class="remove-record-model">

            <div class="modal-body">

              <p class="text-center">

                Are you sure you want to delete this?

              </p>
              <input type="hidden" name="client_id" id="c_id" value="">
              <input type="text" id="doc_type" name="doc_type" hidden/>

            </div>

            <div class="modal-footer">

              <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>

              <button type="submit" class="btn btn-warning delDocbtn">Yes, Delete</button>

            </div>

          </form>

        </div>

      </div>

    </div>

    <div class="modal modal-default fade" id="workhours" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div id="hourdetails"></div>

          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

     <div id="myLeaveModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" id="changeStatus" method="POST"
                action="{{URL::to('admin/leave/changeStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="leave_id" id="leave_id" value="">
            <div class="form-group">
              <label class="control-label col-xs-2" for="id">Remark</label>
              <div class="col-xs-10">
                <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                          rows="5"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Status</label>
              <div class="col-xs-10">
                <select class="form-control" id="status" name="status" required>
                  <option value="Approved">Approved</option>
                  <option value="Rejected">Rejected</option>
                  <option value="Pending">Pending</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button id="btn_status_change" type="submit" class="btn actionBtn">
                <span id="footer_action_button" class='glyphicon'></span> Save
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div id="myOrderModal" class="modal fade" role="dialog">
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
              <label class="control-label col-xs-2" for="name">Status</label>
              <div class="col-xs-10">
                <select class="form-control" id="delivery_status" name="delivery_status" required="true">
                  @foreach($orderStatus as $orderSts)
                  <option value="{{$orderSts->id}}">{{$orderSts->title}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @if(getClientSetting()->order_approval==1)
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Dispatch Date</label>
              <div class="col-xs-10">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  @if(config('settings.ncal')==0)
                  {!! Form::text('delivery_date', null, ['class' => 'form-control pull-right', 'id' => 'delivery_datenew',
                  'autocomplete'=>'off', 'placeholder' => 'Start Date','required']) !!}
                  @else
                  <input type="text" autocomplete="off" class="form-control pull-right" id="delivery_ndate"
                    placeholder="Dispatch Date" required />
                  <input type="text" id="delivery_edate" name="delivery_date" hidden />
                  @endif
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Dispatch Place</label>
              <div class="col-xs-10">
                {!! Form::text('delivery_place', null, ['class' => 'form-control', 'id=delivery_place', 'placeholder' =>
                'Delivery Place']) !!}
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Transport Name</label>
              <div class="col-xs-10">
                {!! Form::text('transport_name', null, ['class' => 'form-control', 'id=transport_name', 'placeholder' =>
                'Transport Name']) !!}
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Transport Number</label>
              <div class="col-xs-10">
                {!! Form::text('transport_number', null, ['class' => 'form-control', 'id=transport_number', 'placeholder'
                => 'Transport Number']) !!}
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="name"> Bilty Number</label>
              <div class="col-xs-10">
                {!! Form::text('billty_number', null, ['class' => 'form-control', 'id=billty_number', 'placeholder' =>
                'Bilty Number']) !!}
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Dispatch Note</label>
              <div class="col-xs-10">
                {!! Form::textarea('delivery_note', null, ['class' => 'form-control', 'rows="5"', 'id=delivery_note',
                'placeholder' => 'Delivery Notes']) !!}
              </div>
            </div>
            @endif
            <div class="modal-footer">
              <button id="btn_status_change" type="submit" class="btn actionBtn">
                <span id="footer_action_button" class='glyphicon'> </span> Change
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div id="myExpenseModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" id="changeStatus" method="POST"
                action="{{URL::to('admin/expense/changeStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="expense_id" id="expense_id" value="">
            <div class="form-group">
              <label class="control-label col-xs-2" for="id">Remark</label>
              <div class="col-xs-10">
                <textarea class="form-control" id="remarkExpense" placeholder="Your Remark.." name="remark" cols="50"
                          rows="5"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Status</label>
              <div class="col-xs-10">
                <select class="form-control" id="statusExpense" name="status" required>
                  <option value="Pending">Pending</option>
                  <option value="Approved">Approved</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn actionBtn" id="btn_change_status">
                <span id="footer_action_button" class='glyphicon'> </span> Change
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
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
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
        </div>
          <div class="modal-body">
            <p class="text-center">
              Sorry! You are not authorized to update the status for the selected record.
            </p>
            <input type="hidden" name="expense_id" id="c_id" value="">
            <input type="text" id="accountType" name="account_type" hidden/>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
          </div>
      </div>
    </div>
  </div>
  <div class="modal modal-default fade" id="alertUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
        </div>
          <div class="modal-body">
            <p class="text-center">
              Sorry! You are not authorized to view this user information.
            </p>
            <input type="hidden" name="expense_id" id="c_id" value="">
            <input type="text" id="accountType" name="account_type" hidden/>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
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
          <input type="hidden" name="expense_id" id="c_id" value="">
          <input type="text" id="accountType" name="account_type" hidden />
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <form method="post" action="{{domain_route('company.admin.employee.show.pdfexports', [$employee->id])}}" class="pdf-export-form hidden" id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <input type="text" name="moduleName" class="moduleName" id="moduleName">
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
  </form>
  </section>

@endsection

@section('scripts')
  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
  <script src="{{asset('assets/plugins/zoomImage/zoomer.js')}}"></script>
  {{-- <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script> --}}
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
   {{-- <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script> --}}
  <script src="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  @yield('analytics-scripts')
  @if(config('settings.ncal')==1)
    <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
    <script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  @else
    <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  @endif
  <script>
    // Tree view
    // Tree view
    $.fn.extend({
      treed: function (o) {
        var openedClass = 'glyphicon-minus-sign';
        var closedClass = 'glyphicon-plus-sign';
        if (typeof o != 'undefined') {
          if (typeof o.openedClass != 'undefined') {
            openedClass = o.openedClass;
          }
          if (typeof o.closedClass != 'undefined') {
            closedClass = o.closedClass;
          }
        }
        ;
        /* initialize each of the top levels */
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul").each(function () {
          var branch = $(this);
          branch.prepend("");
          branch.addClass('branch');
          branch.on('click', function (e) {
            if (this == e.target) {
              var icon = $(this).children('i:first');
              icon.toggleClass(openedClass + " " + closedClass);
              $(this).children().children().toggle();
            }
          })
          branch.children().children().toggle();
        });
        /* fire event from the dynamically added icon */
        tree.find('.branch .indicator').each(function () {
          $(this).on('click', function () {
            $(this).closest('li').click();
          });
        });
        /* fire event to open branch if the li contains an anchor instead of text */
        tree.find('.branch>a').each(function () {
          $(this).on('click', function (e) {
            $(this).closest('li').click();
            e.preventDefault();
          });
        });
        /* fire event to open branch if the li contains a button instead of text */
        tree.find('.branch>button').each(function () {
          $(this).on('click', function (e) {
            $(this).closest('li').click();
            e.preventDefault();
          });
        });
      }
    });
    $('.power-off').click(function(){
      let currentEl = $(this);
      // if(currentEl.hasClass('logged_out')){
      //   alert("Employee is not logged in.");
      //   return false;
      // }
      let empid = $(this).data("empid");
      let logoutUrl = "{{ domain_route('company.admin.employee.logout', ['empid']) }}";
      logoutUrl.replace('empid', empid);
      $.ajax({
        "url": logoutUrl,
        "dataType": "json",
        "type": "POST",
        "data":{
          _token: "{{csrf_token()}}",
          empid:empid,
        },
        beforeSend: function(){
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
        },
        success: function(response){
          if(response.status==200){
            currentEl.removeClass(response.remove_class);
            currentEl.parent().addClass(response.class)
            alert(response.msg);
          }else{
            alert(response.msg);
          }
        },
        error: function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        },
        complete: function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        }
      });
      $('#mainBox').addClass('box-loader');
      $('#loader1').removeAttr('hidden');
    });    
    $('#tree1').treed();
      $(".alert button.close").click(function (e) {
        $(this).parent().fadeOut('slow');
      });

      @if(Auth::user()->EmployeeId()==$employee->id || $employee->is_admin == 1)
        $('#employeeId-enableClickableOptGroups').multiselect({
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true,
          enableFullValueFiltering: true,
          enableClickableOptGroups: false,
          includeSelectAllOption: false, 
          enableCollapsibleOptGroups : true,
          selectAllNumber: false,
          nonSelectedText:"Select Parties",
          disableIfEmpty:true,
          onSelectAll:function(element,option) {
            console.log(element);
          }
        });
      @else
        $('#employeeId-enableClickableOptGroups').multiselect({
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true,
          enableFullValueFiltering: true,
          enableClickableOptGroups: true,
          includeSelectAllOption: true, 
          enableCollapsibleOptGroups : true,
          selectAllNumber: false,
          nonSelectedText:"Select Parties",
          disableIfEmpty:true,
          onSelectAll:function(element,option) {
            console.log(element);
          }
          // onChange :function(option, checked) {
          //   if(!checked){
          //     let unselParty = option.val();
          //     let juniorParties = JSON.parse("{{$getJuniorParties}}");
          //     let currentSelectedParties = $('#employeeId-enableClickableOptGroups').val();
          //     if(juniorParties.includes(parseInt(unselParty))){
          //       let intConverted = [];
          //       $.each(currentSelectedParties, function(key, currentSelectedParty){
          //         intConverted.push(parseInt(currentSelectedParty));
          //       });
          //       intConverted.push(unselParty);
          //       $(this)[0].lastToggledInput[0].checked = true;
          //       $('#employeeId-enableClickableOptGroups').val(intConverted);
          //       alert("Cannot remove party since it has been assigned to juniors.");
          //     }
          //   }
          // },
          // onSelectAll:function(event) {
          //   debugger;
          // }
        });
      @endif

      $(document).on("click", ".clientLinks", function(e){
        if($(this).data('viewable')==""){
          e.preventDefault();
          $('#alertClientModal').modal('show');
          // $('#alertModalText').html('Sorry! You are not authorized to view this user details.');
        }
      });
      
      $("#employeeId-enableClickableOptGroups").change(function(){
        let selected = [];
        $(this).find("option:selected").each(function(){
          let optgroup_val = $(this).parent().attr("value");
          if(!selected.includes(optgroup_val)){
            selected.push(optgroup_val);
          }
        });
        $('#beatId-optGroups').val(selected);
      });

      $('.select2').select2();

      $(document).on('click','.alert_party_model',function(){
          $('#alertPartyModal').modal('show');
      });


      @if(config('settings.ncal')==0)
      $("#delivery_datenew").datepicker({
          format: "yyyy-mm-dd",
          startDate: new Date(),
          autoclose: true,
      });
      $(document).on('click', '.edit-modal-order', function () {
        $('#footer_action_button').addClass('glyphicon-check');
        $('#footer_action_button').removeClass('glyphicon-trash');
        $('.actionBtn').addClass('btn-success');
        $('.actionBtn').removeClass('btn-danger');
        $('.actionBtn').addClass('edit');
        $('.modal-title').text('Change Status');
        $('.deleteContent').hide();
        $('.form-horizontal').show();
        $('#order_id').val($(this).data('id'));
        $('#delivery_status').val($(this).data('status'));
        $('#transport_name').val($(this).data('transport_name'));
        $('#transport_number').val($(this).data('transport_number'));
        $('#billty_number').val($(this).data('billty_number'));
        $('#delivery_datenew').val($(this).data('orderdate'));
        $('#delivery_place').val($(this).data('place'));
        $('#delivery_note').val($(this).data('note'));
        $('#myOrderModal').modal('show');
      });   
      $('.datepicker').datepicker({
        format:"yyyy-mm-dd",
      });
      $('#doj').datepicker({
        endDate: '+0d',
      });

      $('#lwd').datepicker({
       endDate: '+0d',
      });
      @else
      var today = moment().format('YYYY-MM-DD');
      var ntoday = AD2BS(today);
      var ntoday= ntoday.split('-');
      ntoday = ntoday[1]+'/'+ntoday[2]+'/'+ntoday[0];
      $('#delivery_ndate').nepaliDatePicker({
        onChange:function(){
          $('#delivery_edate').val(BS2AD($('#delivery_ndate').val()));
        }
      });
      $(document).on('click', '.edit-modal-order', function () {
        // $('#footer_action_button').text(" Change");
        $('#footer_action_button').addClass('glyphicon-check');
        $('#footer_action_button').removeClass('glyphicon-trash');
        $('.actionBtn').addClass('btn-success');
        $('.actionBtn').removeClass('btn-danger');
        $('.actionBtn').addClass('edit');
        $('.modal-title').text('Change Status');
        $('.deleteContent').hide();
        $('.form-horizontal').show();
        $('#order_id').val($(this).data('id'));
        // $('#remark').val($(this).data('remark'));
        $('#delivery_status').val($(this).data('status'));
        $('#transport_name').val($(this).data('transport_name'));
        $('#transport_number').val($(this).data('transport_number'));
        $('#billty_number').val($(this).data('billty_number'));
        $('#delivery_edate').val(($(this).data('orderdate')));
        $('#delivery_ndate').val(AD2BS($(this).data('orderdate')));
        $('#delivery_place').val($(this).data('place'));
        $('#delivery_note').val($(this).data('note'));
        $('#myOrderModal').modal('show');
      });
      $('#empDOBBox').nepaliDatePicker({
        npdMonth: true,
        npdYear: true,
        npdDate: false,
        disableAfter: ntoday,
        ndpEnglishInput: 'englishDate',
      });

      $('#doj').nepaliDatePicker({
        npdMonth: true,
        npdYear: true,
        npdDate: false,
        disableAfter: ntoday,
        ndpEnglishInput: 'englishDate',
      });

      $('#lwd').nepaliDatePicker({
        npdMonth: true,
        npdYear: true,
        npdDate: false,
        disableAfter: ntoday,
        ndpEnglishInput: 'englishDate',
      });

      $('.datepicker').nepaliDatePicker({
        npdMonth: true,
        npdYear: true,
        npdDate: false,
        disableAfter: ntoday,
        ndpEnglishInput: 'englishDate',
      });

      @endif

      $(function () {

          $('#changeDeliveryStatus,#changeStatus').on('submit',function(){
            $('.actionBtn').attr('disabled',true);
          });

          $('#delete').on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget);
              var mid = button.data('mid');
              var url = button.data('url');
              $(".remove-record-model").attr("action", url);
              var modal = $(this);
              modal.find('.modal-body #m_id').val(mid);
          });
      });
      var partytable = $('#tbl_partyHandling').DataTable({
            pagingType: "simple",
            "dom": "<'row'<'col-xs-6'l><'col-xs-6'f>>"+
              "<'row'<'col-xs-6'><'col-xs-6'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",     
           
      });
      

      // Orders DT section
      // Define columns for Data Table
      @if(config('settings.order_with_amt')==0)
        var columns = [{ "data": "id" },
        { "data": "orderno" },
        { "data": "orderdate" },
        { "data": "partyname" },
        { "data": "grandtotal" },
        { "data": "delivery_status" },
        { "data": "action" }];
      @else
        var columns = [{ "data": "id" },
        { "data": "orderno" },
        { "data": "orderdate" },
        { "data": "partyname" },
        { "data": "delivery_status" },
        { "data": "action" }];
      @endif
      @if(Auth::user()->can('order-view'))
      function initializeODT(empID){
        ordertable = $('#order').DataTable({
          "stateSave": true,
          "stateSaveParams": function (settings, data) {
          data.search.search = "";
          },
          "order": [[ 2, "desc" ]],
          "serverSide": true,
          "processing": false,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
            {
              "orderable": false,
              "targets":-1,
            },],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Order List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Order List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Order List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.employee.empOrderTable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              empID:empID,
            },
            beforeSend:function(){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
            },
            error:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            },
            complete:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": columns,
          @if(config('settings.order_with_amt')==0)
          drawCallback:function(settings)
          {
            $('#grandTotalAmount').html('<b>Total Orders: '+settings.json.total+'</b>');
          }
          @endif
        });
        ordertable.buttons().container()
            .appendTo('#orderexports');
        var orderOldExportAction = function (self, e, dt, button, config) {
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

        var orderNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$ordersCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].partyname = $(settings.json.data[key].partyname)[0].textContent;
                  settings.json.data[key].delivery_status = $(settings.json.data[key].delivery_status)[0].textContent; 
                });
                properties = JSON.stringify(["id", "orderno", "orderdate", "partyname", "grandtotal", "delivery_status"]);
                columns = JSON.stringify(["S.No.", "Order No.", "Order Date", "Party Name", "Grand Total", "Order Status"]);customExportAction(config, settings.json.data, 'employee-order', properties, columns);
              }else{
                orderOldExportAction(self, e, dt, button, config);
              }
              // orderOldExportAction(self, e, dt, button, config);
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
      }
      var empID = '{{$employee->id}}';
      initializeODT(empID);
      @endif

      
      @if(Auth::user()->can('leave-view'))
      @if( !$employee->leaves->isEmpty() )
      $(document).ready(function () {
          var leavetable = $('#leave').DataTable({
              "columnDefs": [ {
                "targets": 7,
                "orderable": false
              }],
              dom:  "<f>" +
                    "<'row'<'col-xs-12'tr>>" +
                    "<'row'<'col-xs-4'li><'col-xs-8'p>>",
              stateSave: true,
              "stateSaveParams": function (settings, data) {
              data.search.search = "";
              },
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Leave List of {{$employee->name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5,6]
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Leave List of {{$employee->name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5,6]
                      },
                      action: function ( e, dt, node, config ) {
                        leaveNewExportAction( e, dt, node, config );
                      }
                  },
                  {
                      extend: 'print',
                      title: 'Leave List of {{$employee->name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5,6]
                      }
                  },
              ]
          });

          leavetable.buttons().container()
              .appendTo('#leaveexports');
        var leaveNewExportAction = function (e, dt, button, config) {
          var self = this;
          var data = [];
          var count = 0;
          leavetable.rows({"search":"applied" }).every( function () {
            var row = {};
            row["id"] = ++count;
            row["from"] = this.data()[1];
            row["to"] = this.data()[2];
            row["total_days"] = this.data()[3];
            row["reason"] = this.data()[4];
            row["type"] = this.data()[5];
            row["status"] = this.data()[6].replace(/<[^>]+>/g, '').trim();
            data.push(row);
          });
          properties = JSON.stringify(["id", "from", "to", "total_days", "reason", "type", "status"]);
          columns = JSON.stringify(["S.No.", "From", "To", "Total Days", "Reason", "Type", "Status"]);
          customExportAction(config, data, 'employee-leave', properties, columns);
        };
      });
      @endif
      @endif

      @if(Auth::user()->can('expense-view'))
      @if( !$employee->expenses->isEmpty())
      $(document).ready(function () {
          var expensetable = $('#expense').DataTable({
              "columnDefs": [ {
                "targets": 6,
                "orderable": false
              },
              @if(config('settings.party')==0)
              {
                "targets": [ 3 ],
                "visible": false,
                "searchable": false
              },
              @endif
              ],
              dom:  "<f>" +
                    "<'row'<'col-xs-12'tr>>" +
                    "<'row'<'col-xs-4'li><'col-xs-8'p>>",
              stateSave: true,
              "stateSaveParams": function (settings, data) {
              data.search.search = "";
              },
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Expense List of {{$employee->name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Expense List of {{$employee->name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      },
                      action: function ( e, dt, node, config ) {
                        expenseNewExportAction( e, dt, node, config );
                      }
                  },
                  {
                      extend: 'print',
                      title: 'Expense List of {{$employee->name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      }
                  },
              ],
              footerCallback: function (row, data, start, end, display) {
                  var api = this.api(), data;

                  // Remove the formatting to get integer data for summation
                  var intVal = function (i) {
                      return typeof i === 'string' ?
                          i.replace(/[\$,]/g, '') * 1 :
                          typeof i === 'number' ?
                              i : 0;
                  };

                  // Total over all pages
                  total = api
                      .column(7)
                      .data()
                      .reduce(function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0);

                  // Total over this page
                  pageTotal = api
                      .column( 7,{ search: 'applied',page:'all'})
                      .data()
                      .reduce(function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0);

                  // Update footer
                  $('#grandTotalEAmount').html(
                      '<b>Total Expenses: '+"{{ config('settings.currency_symbol')}} " + (pageTotal).toLocaleString("en")+'</b>'
                  );
              }
          });

          expensetable.buttons().container()
              .appendTo('#expenseexports');

        var expenseNewExportAction = function (e, dt, button, config) {
          var self = this;
          var data = [];
          var count = 0;
          expensetable.rows({"search":"applied" }).every( function () {
            var row = {};
            row["id"] = ++count;
            row["date"] = this.data()[1];
            row["amount"] = this.data()[2];
            row["party_name"] = this.data()[3].replace(/<[^>]+>/g, '').trim();
            row["approved_by"] = this.data()[4];
            row["status"] = this.data()[5].replace(/<[^>]+>/g, '').trim();
            data.push(row);
          });
          properties = JSON.stringify(["id", "date", "amount", "party_name", "approved_by", "status"]);
          columns = JSON.stringify(["S.No.", "Date", "Amount", "Party Name", "Approved/Cancelled By", "Status"]);
          customExportAction(config, data, 'employee-expense', properties, columns);
        };
      });
      @endif
      @endif

      @if(Auth::user()->can('collection-view'))
      function initializeCDT(empID){
        var collectiontable = $('#collection').DataTable({
          "language": {
            search: "_INPUT_",
            searchPlaceholder: "Search"
          },
          "stateSave": true,
          "stateSaveParams": function (settings, data) {
          data.search.search = "";
          },
          "order": [[ 0, "desc" ]],
          "serverSide": true,
          "processing": false,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
            {
              orderable: false,
              targets:-1,
            }, 
            { 
              width: 20, 
              targets: [0],
            },
            { 
              width: 100, 
              targets: [-1],
            },
          ],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Collections List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3,4],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                collectionNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Collections List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3,4],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                collectionNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Collections List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3,4],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                collectionNewExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.employee.empCollectionTable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              empID : empID,
            },
            beforeSend:function(){
              $('#mainBox2').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
            },
            error:function(){
              $('#mainBox2').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            },
            complete:function(){
              $('#mainBox2').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": [
            {"data" : "id"},
            {"data" : "payment_date"},
            {"data" : "company_name"},
            {"data" : "payment_received"},
            {"data" : "payment_method"},
            {"data" : "action"},
          ],
          drawCallback:function(settings)
          {
            $('#grandTotalCAmount').html('<b>Total Collections: '+settings.json.total+'</b>');
          }
        });
        collectiontable.buttons().container()
            .appendTo('#collectionexports');
        var collectionOldExportAction = function (self, e, dt, button, config) {
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

        var collectionNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$collectionsCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].company_name = $(settings.json.data[key].company_name)[0].textContent;
                });
                properties = JSON.stringify(["id", "payment_date", "company_name", "payment_received", "payment_method"]);
                columns = JSON.stringify(["S.No.", "Date", "Party Name", "Amount", "Payment Mode"]);
                customExportAction(config, settings.json.data, 'employee-collection', properties, columns);
              }else{
                collectionOldExportAction(self, e, dt, button, config);
              }
              // collectionOldExportAction(self, e, dt, button, config);
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
      };
      initializeCDT(empID);
      @endif

      @if(Auth::user()->can('activity-view'))
        var activityColumns = [{ "data": "id" },
        { "data": "completion_datetime" },
        { "data": "title" },
        { "data": "PartyName" },
        { "data": "TypeName" },
        { "data": "AssignedByName" },
        { "data": "AssignedToName" },
        { "data": "completed" },
        { "data": "action" },];
      @if( $activities->count()>0)
      $(document).ready(function () {
          // var activitytable = $('#activity').DataTable({
          //     "columnDefs": [ {
          //       "targets": 8,
          //       "orderable": false
          //     },
          //     @if(config('settings.party')==0)
          //     {
          //         "targets": [ 3 ],
          //         "visible": false,
          //         "searchable": false
          //     },  
          //     @endif
          //     ],
          //     dom:  "<f>" +
          //           "<'row'<'col-xs-12'tr>>" +
          //           "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          //     stateSave: true,
          //     "stateSaveParams": function (settings, data) {
          //     data.search.search = "";
          //     },
          //     buttons: [
          //         {
          //             extend: 'excelHtml5',
          //             title: 'Activities List of {{$employee->name}}',
          //             exportOptions:{
          //               @if(config('settings.party')==0)
          //               columns: [0,1,2,4,5,6]
          //               @else
          //               columns: [0,1,2,3,4,5,6]
          //               @endif
          //             }
          //         },
          //         {
          //             extend: 'pdfHtml5',
          //             title: 'Activities List of {{$employee->name}}',
          //             exportOptions:{
          //               @if(config('settings.party')==0)
          //               columns: [0,1,2,4,5,6]
          //               @else
          //               columns: [0,1,2,3,4,5,6]
          //               @endif
          //             },
          //             action: function ( e, dt, node, config ) {
          //               activitiesNewExportAction( e, dt, node, config );
          //             },
          //         },
          //         {
          //             extend: 'print',
          //             title: 'Activities List of {{$employee->name}}',
          //             exportOptions:{
          //               @if(config('settings.party')==0)
          //               columns: [0,1,2,4,5,6]
          //               @else
          //               columns: [0,1,2,3,4,5,6]
          //               @endif
          //             }
          //         },
          //     ]
          // });

          var activitytable = $('#activity').DataTable({
            "order": [[ 1, "desc" ]],
            "processing": false,
            "serverSide": true,
            "ajax":{
              "url": "{{ domain_route('company.admin.employee.empActivityTable') }}",
              "dataType": "json",
              "type": "POST",
              "data":{ 
                _token: "{{csrf_token()}}",
                allSup: @json($allSup),
                create_assign_id: "{{$employee->id}}"
              },            
              beforeSend:function(){
                $('#mainBox').addClass('box-loader');
                $('#loader1').removeAttr('hidden');
              },
              error:function(){
                $('#mainBox').removeClass('box-loader');
                $('#loader1').attr('hidden', 'hidden');
              },
              complete:function(){
                $('#mainBox').removeClass('box-loader');
                $('#loader1').attr('hidden', 'hidden');
              },
            },         
            "columnDefs": [ {
                "targets": [-1,-2],
                "orderable": false
              },
              @if(config('settings.party')==0)
              {
                  "targets": [ 3 ],
                  "visible": false,
                  "searchable": false
              },  
              @endif
              ],
              "columns": activityColumns,
              "dom": "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +"<'row'<'col-xs-12'tr>>" +"<'row'<'col-xs-4'li><'col-xs-8'p>>",
              stateSave: true,
              "stateSaveParams": function (settings, data) {
              data.search.search = "";
              },
              "buttons": [
                {
                  extend: 'pdfHtml5', 
                  title: 'Activities List of {{$employee->name}}', 
                  exportOptions: {

                    @if(config('settings.party')==0)
                      columns: [0, 1, 2, 3, 4, 6,7,8]
                    @else
                      columns: [0, 1, 2, 3, 4, 5, 6,7,8]
                        @endif
                  },
                  action: function ( e, dt, node, config ) {
                    activitiesNewExportAction( e, dt, node, config );
                  }
                },
                {
                  extend: 'excelHtml5', 
                  title: 'Activities List of {{$employee->name}}', 
                  exportOptions: {

                    @if(config('settings.party')==0)
                      columns: [0, 1, 2, 3, 4, 6,7,8]
                    @else
                      columns: [0, 1, 2, 3, 4, 5, 6,7,8]
                        @endif
                  },
                  action: function ( e, dt, node, config ) {
                    activitiesNewExportAction( e, dt, node, config );
                  }
                },
                {
                  extend: 'print', 
                  title: 'Activities List of {{$employee->name}}', 
                  exportOptions: {

                    @if(config('settings.party')==0)
                      columns: [0, 1, 2, 3, 4, 6,7,8]
                    @else
                      columns: [0, 1, 2, 3, 4, 5, 6,7,8]
                        @endif
                  },
                  action: function ( e, dt, node, config ) {
                    activitiesNewExportAction( e, dt, node, config );
                  }
                },
              ],

            });

          activitytable.buttons().container()
              .appendTo('#activityexports');
        //   var activitiesNewExportAction = function (e, dt, button, config) {
        //   var self = this;
        //   var data = [];
        //   var count = 0;
        //   activitytable.rows({"search":"applied" }).every( function () {
        //     var row = {};
        //     row["id"] = ++count;
        //     row["date"] = this.data()[1];
        //     row["title"] = this.data()[2];
        //     row["party_name"] = this.data()[3].replace(/<[^>]+>/g, '').trim();
        //     row["type"] = this.data()[4];
        //     row["assigned_by"] = this.data()[5].replace(/<[^>]+>/g, '').trim();
        //     row["assigned_to"] = this.data()[6].replace(/<[^>]+>/g, '').trim();
        //     if($(this.data()[7]).find('input').first().is(":checked")){
        //       row["complete"] = "Yes";
        //     }else{
        //       row["complete"] = "No";  
        //     }
        //     data.push(row);
        //   });
        //   properties = JSON.stringify(["id", "date", "title", "party_name", "type", "assigned_by", "assigned_to", "complete"]);
        //   columns = JSON.stringify(["S.No.", "Date", "Title", "Party Name", "Type", "Assigned By", "Assigned To", "Complete"]);
        //   customExportAction(config, data, 'employee-activities', properties, columns);
        // };
        var activitiesOldExportAction = function (self, e, dt, button, config) {
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

        var activitiesNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$activities->count()}};
            dt.one('preDraw', function (e, settings) {
              // if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
              //   $.each(settings.json.data, function(key, htmlContent){
              //     settings.json.data[key].id = key+1;
              //     settings.json.data[key].company_name = $(settings.json.data[key].company_name)[0].textContent;
              //   });
              //   properties = JSON.stringify(["id", "payment_date", "company_name", "payment_received", "payment_method"]);
              //   columns = JSON.stringify(["S.No.", "Date", "Party Name", "Amount", "Payment Mode"]);
              //   customExportAction(config, settings.json.data, 'employee-collection', properties, columns);
              // }else{
                activitiesOldExportAction(self, e, dt, button, config);
              // }
              // collectionOldExportAction(self, e, dt, button, config);
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
      });
      @endif
      @endif

      @if(Auth::user()->can('dayremark-view'))
      function initializeDayDT(empID){
        const dayremarktable = $('#dayremarktbl').DataTable({
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search"
          },
          "order": [[ 2, "desc" ]],
          "serverSide": true,
          "processing": false,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
            {
              "orderable": false,
              "targets":-1,
            }, 
            { 
              width: 20, 
              targets: [0],
            },
            { 
              width: 100, 
              targets: [-1],
            },
          ],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Day Remarks of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                dayRemarkNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Day Remarks of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                dayRemarkNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Day Remarks of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                dayRemarkNewExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.employee.empDayremarksTable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}",
              empID : empID,
            },
            beforeSend:function(){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
            },
            error:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            },
            complete:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": [
            {"data" : "id"},
            {"data" : "remark_date"},
            {"data" : "remark_datetime"},
            {"data" : "remarks"},
            {"data" : "action"},
          ],
        });
        dayremarktable.buttons().container()
            .appendTo('#dayremarksexports');
        var dayRemarkOldExportAction = function (self, e, dt, button, config) {
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

        var dayRemarkNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$dayRemarksCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                });
                properties = JSON.stringify(["id", "remark_date", "remark_datetime", "remarks"]);
                columns = JSON.stringify(["S.No.", "Date", "Time", "Remark"]);
                customExportAction(config, settings.json.data, 'employee-dayRemark', properties, columns);
              }else{
                dayRemarkOldExportAction(self, e, dt, button, config);
              }
              // dayRemarkOldExportAction(self, e, dt, button, config);
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
        $('#reportrange').removeClass('hidden');
      }; // Data Table initialize 
      var empID = "{{$employee->id}}";
      initializeDayDT(empID);
      @endif

      var employee_list = new Array();

      function empty_list() {
          employee_list = [];
          $('#employee-list').empty();
      }

      function chooseEmployee() {
          var employee_id = $("#employee_id option:selected").val();
          var employee_name = $("#employee_id option:selected").text();
          // var client_id = id;
          if (employee_list.includes(employee_id) == false && employee_id != 0) {
              var newentry = "<li id='employee" + employee_id + "'><input name='employee[]' type='hidden' value='" + employee_id + "'>" + employee_name + "<a class='btn btn-danger btn-xs pull-right' style='height:18px;' onclick='popEmployee(" + employee_id + ")'>X</a></li>";
              $("#employee-list").append(newentry);
              employee_list.push(employee_id);
          }
      }

      function popEmployee(id) {
          employee_list = jQuery.grep(employee_list, function (value) {
              return value != id;
          });
          $('#employee' + id).empty();
          $('#employee' + id).remove();
      }

      $(document).on('click', '.edit-modal-expense', function () {
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Change Status');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#expense_id').val($(this).data('id'));
          $('#remarkExpense').val($(this).data('remark'));
          $('#statusExpense').val($(this).data('status'));
          $('#myExpenseModal').modal('show');
      });

      $(document).on('click', '.edit-modal-leave', function () {
          // $('#footer_action_button').text(" Save");
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Edit');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#leave_id').val($(this).data('id'));
          $('#remark').val($(this).data('remark'));
          $('#status').val($(this).data('status'));
          $('#myLeaveModal').modal('show');
      });

      // $(document).on('click', '.edit-modal-order', function () {
      //     // $('#footer_action_button').text(" Change");
      //     $('#footer_action_button').addClass('glyphicon-check');
      //     $('#footer_action_button').removeClass('glyphicon-trash');
      //     $('.actionBtn').addClass('btn-success');
      //     $('.actionBtn').removeClass('btn-danger');
      //     $('.actionBtn').addClass('edit');
      //     $('.modal-title').text('Change Delivery Status');
      //     $('.deleteContent').hide();
      //     $('.form-horizontal').show();
      //     $('#order_id').val($(this).data('id'));
      //     // $('#remark').val($(this).data('remark'));
      //     $('#delivery_status').val($(this).data('status'));
      //     $('#delivery_datenew').val($(this).data('orderdate'));
      //     $('#delivery_place').val($(this).data('place'));
      //     $('#delivery_note').val($(this).data('note'));
      //     $('#myOrderModal').modal('show');
      // });

      $('#activity').on('click','.check',function () {
        var id = $(this).val();
        if($(this). prop("checked") == true){
          var checked = true;
          var myaudio = new Audio();
          myaudio.src = "{{asset('assets/plugins/sweetalert2/ting.wav')}}";
          myaudio.play();
        }else{
          var checked = false;
        }
        $(this).prop('disabled',true);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{url(domain_route("company.admin.activities.updateMark"))}}',
            type: "POST",
            data: {
              'csrf_token':'{{csrf_token()}}',
              'id':id,
              'checked':checked,
            },
            success: function (data) {
                if(data['result']==true){
                  if(data['ticked']==true){
                    $('.check_'+id).prop("checked",true);
                  }else{
                    $('.check_'+id).prop("checked",false);
                  }
                }else{
                  if(checked==true){
                    $('.check_'+id).prop("checked",false);
                  }else{
                    $('.check_'+id).prop("checked",true);
                  }
                }
                $('.check_'+id).prop("disabled",false);
            },
            error: function (xhr) {
              $('.check_'+id).prop("disabled",false);
              console.log('internal server error or ajax failed');
            },
            complete: function () {
                //   $('#btnSave').val('Add Activity');
                //  $('#btnSave').removeAttr('disabled');
            }
        });
      });

      $(document).on('click','.no_check',function(){
        if($(this).is(':checked')){
          $(this).prop('checked',false);
        }else{
          $(this).prop('checked',true);
        }
        $('#textComplete').html('complete');
        $('#alertCompleteModal').modal('show');
      });

      $(document).on('click','.no_uncheck',function(){
        if($(this).is(':checked')){
          $(this).prop('checked',false);
        }else{
          $(this).prop('checked',true);
        }
        $('#textComplete').html('incomplete');
        $('#alertCompleteModal').modal('show');
      });

  </script>

  <script>
    $(function(){

      $('#subtabs').on('click','li',function(e){
        e.preventDefault();
        $('.text-display').attr('hidden',false);
        $('.text-form').attr('hidden',true);
        $('#ActivateEdit').removeClass('hide');
        $('#ActivateContactEdit').removeClass('hide');
        $('#ActivateCompanyEdit').removeClass('hide');
        $('#ActivateBankEdit').removeClass('hide');
        $('#ActivateDocumentEdit').removeClass('hide');
        $('#ActivateAccountEdit').removeClass('hide');
        $('#ActivateUpdate').addClass('hide');
        $('#ActivateContactUpdate').addClass('hide');
        $('#ActivateCompanyUpdate').addClass('hide');
        $('#ActivateBankUpdate').addClass('hide');
        $('#ActivateDocumentUpdate').addClass('hide');
        $('#ActivateAccountUpdate').addClass('hide');
        $('#ActivateCancel').addClass('hide');
        $('#ActivateContactCancel').addClass('hide');
        $('#ActivateCompanyCancel').addClass('hide');
        $('#ActivateBankCancel').addClass('hide');
        $('#ActivateDocumentCancel').addClass('hide');
        $('#ActivateAccountCancel').addClass('hide');
        $('#lblChange').addClass('hide');
      });

      $('#ActivateEdit').click(function(){
        $('.text-display').attr('hidden',true);
        $('.text-form').attr('hidden',false);
        $(this).addClass('hide');
        $('#lblChange').removeClass('hide');
        $('#ActivateUpdate').removeClass('hide');
        $('#ActivateCancel').removeClass('hide');
      });

      $('#ActivateCancel').click(function(){
        $('.text-display').attr('hidden',false);
        $('.text-form').attr('hidden',true);
        $('#ActivateEdit').removeClass('hide');
        $('#ActivateUpdate').addClass('hide');
        $('#lblChange').addClass('hide');
        $(this).addClass('hide');
      });

      $('#ActivateContactEdit').click(function(){
        $('.text-display').attr('hidden',true);
        $('.text-form').attr('hidden',false);
        $(this).addClass('hide');
        $('#ActivateContactUpdate').removeClass('hide');
        $('#ActivateContactCancel').removeClass('hide');
      });

      $('#ActivateContactCancel').click(function(){
        $('.text-display').attr('hidden',false);
        $('.text-form').attr('hidden',true);
        $('#ActivateContactEdit').removeClass('hide');
        $('#ActivateContactUpdate').addClass('hide');
        $(this).addClass('hide');
      });

      $('#ActivateCompanyEdit').click(function(){
        $('.text-display').attr('hidden',true);
        $('.text-form').attr('hidden',false);
        $(this).addClass('hide');
        $('#ActivateCompanyUpdate').removeClass('hide');
        $('#ActivateCompanyCancel').removeClass('hide');
      });

      $('#ActivateCompanyCancel').click(function(){
        $('.text-display').attr('hidden',false);
        $('.text-form').attr('hidden',true);
        $('#ActivateCompanyEdit').removeClass('hide');
        $('#ActivateCompanyUpdate').addClass('hide');
        $(this).addClass('hide');
      });

      $('#ActivateBankEdit').click(function(){
        $('.text-display').attr('hidden',true);
        $('.text-form').attr('hidden',false);
        $(this).addClass('hide');
        $('#ActivateBankUpdate').removeClass('hide');
        $('#ActivateBankCancel').removeClass('hide');
      });

      $('#ActivateBankCancel').click(function(){
        $('.text-display').attr('hidden',false);
        $('.text-form').attr('hidden',true);
        $('#ActivateBankEdit').removeClass('hide');
        $('#ActivateBankUpdate').addClass('hide');
        $(this).addClass('hide');
      });

      $('#ActivateDocumentEdit').click(function(){
        $('.text-display').attr('hidden',true);
        $('.text-form').attr('hidden',false);
        $(this).addClass('hide');
        $('#ActivateDocumentUpdate').removeClass('hide');
        $('#ActivateDocumentCancel').removeClass('hide');
      });

      $('#ActivateDocumentCancel').click(function(){
        $('.text-display').attr('hidden',false);
        $('.text-form').attr('hidden',true);
        $('#ActivateDocumentEdit').removeClass('hide');
        $('#ActivateDocumentUpdate').addClass('hide');
        $(this).addClass('hide');
      });

      $('#ActivateAccountEdit').click(function(){
        $('.text-display').attr('hidden',true);
        $('.text-form').attr('hidden',false);
        $(this).addClass('hide');
        $('#ActivateAccountUpdate').removeClass('hide');
        $('#ActivateAccountCancel').removeClass('hide');
      });

      $('#ActivateAccountCancel').click(function(){
        $('.text-display').attr('hidden',false);
        $('.text-form').attr('hidden',true);
        $('#ActivateAccountEdit').removeClass('hide');
        $('#ActivateAccountUpdate').addClass('hide');
        $(this).addClass('hide');
      });

      $('#UpdateProfileDetail').on('submit',function(e){
        e.preventDefault();
        var url = "{{domain_route('company.admin.employee.ajaxBasicUpdate')}}";
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
                $('#ActivateUpdate').children().first().attr('disabled',true);
              },
              success: function (data) {
                if(data.code===200){
                    alert(data.success);
                    $('#ActivateUpdate').children().first().attr('disabled',false);
                    $('#ActivateUpdate').addClass('hide');
                    $('#ActivateCancel').addClass('hide');
                    $('#lblChange').addClass('hide');
                    $('#ActivateEdit').removeClass('hide');
                    $('.text-form').attr('hidden',true);
                    $('.text-display').attr('hidden',false);
                    if(data.empData['name']){
                      $('#empName').html(data.empData['name']);
                    }else{
                      $('#empName').html('N/A');                      
                    }
                    if(data.empData['father_name']){
                      $('#empFather').html(data.empData['father_name']);
                    }else{
                      $('#empFather').html('N/A');                      
                    }
                    @if(config('settings.ncal')==1)
                      if(data.empData['b_date']){
                        var birthDate = AD2BSFixed(data.empData['b_date']);
                        birthDate = birthDate.split('-');
                        birthDate = birthDate[2]+' '+getNepaliMonthsName(birthDate[1])+' '+birthDate[0];
                      }else{
                        var birthDate = 'N/A';
                      }
                    @else
                      if(data.empData['b_date']){
                        var birthDate = data.empData['b_date'];
                      }else{
                        var birthDate = 'N/A';
                      }
                    @endif
                    $('#empDOB').html(birthDate);
                    $('#empGender').html(data.empData['gender']);
                    $('#empStatus').html(data.empData['status']);
                    $('#p_pic').empty();
                    $('#p_pic').attr("src","{{URL::asset('cms')}}"+data.empData['image_path']);

                }else if(data.code===201){
                  alert(data.error);
                }else{
                  alert("Some issue that is not being handeled");
                }           
              },
              error:function(){
                $('#ActivateUpdate').children().first().attr('disabled',false);
                alert('Sorry, something went wrong.')
              }
          });
      });

      $('#UpdateContactDetail').on('submit',function(e){
        e.preventDefault();
        var url = "{{domain_route('company.admin.employee.ajaxContactUpdate')}}";
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('#ActivateContactUpdate').children().first().attr('disabled',true);
              },
              success: function (data) {
                if(data.code===200){
                    alert(data.success);
                    $('#ActivateContactUpdate').children().first().attr('disabled',false);
                    $('#ActivateContactUpdate').addClass('hide');
                    $('#ActivateContactCancel').addClass('hide');
                    $('#ActivateContactEdit').removeClass('hide');
                    $('.text-form').attr('hidden',true);
                    $('.text-display').attr('hidden',false);
                    if(data.empData['a_phone']){
                      $('#empAMob').html('+'+data.empData['country_code']+'-'+data.empData['a_phone']);
                    }else{
                      $('#empAMob').html('N/A');
                    }
                    if(data.empData['local_add']){
                      $('#empLAdd').html(data.empData['local_add']);
                    }else{
                      $('#empLAdd').html('N/A');
                    }
                    if(data.empData['per_add']){
                      $('#empPAdd').html(data.empData['per_add']);
                    }else{
                      $('#empPAdd').html('N/A');
                    }
                    if(data.empData['e_name']){
                      $('#empEName').html(data.empData['e_name']);
                    }else{
                      $('#empEName').html('N/A');
                    }
                    if(data.empData['e_relation']){
                      $('#empERelation').html(data.empData['e_relation']);
                    }else{
                      $('#empERelation').html('N/A');
                    }
                    if(data.empData['e_phone']){
                      $('#empEContact').html(data.empData['e_phone']);
                    }else{
                      $('#empEContact').html('N/A');
                    }
                }else if(data.code===201){
                  alert(data.error);
                }else{
                  alert("Some issue that is not being handeled");
                }           
              },
              error:function(){
                $('#ActivateContactUpdate').children().first().attr('disabled',false);
                alert('Sorry, something went wrong.')
              }
          });
      });

      $('#UpdateCompanyDetail').on('submit',function(e){
        e.preventDefault();
        var url = "{{domain_route('company.admin.employee.ajaxCompanyUpdate')}}";
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('#ActivateCompanyUpdate').children().first().attr('disabled',true);
              },
              success: function (data) {
                if(data.code===200){
                    alert(data.success);
                    $('#ActivateCompanyUpdate').children().first().attr('disabled',false);
                    $('#ActivateCompanyUpdate').addClass('hide');
                    $('#ActivateCompanyCancel').addClass('hide');
                    $('#ActivateCompanyEdit').removeClass('hide');
                    $('.text-form').attr('hidden',true);
                    $('.text-display').attr('hidden',false);
                    if(data.empData['employee_code']){
                      $('#empCode').html(data.empData['employee_code']);
                    }else{
                      $('#empCode').html('N/A');                      
                    }
                    if(data.groupname['name']){
                      $('#empGroup').html(data.groupname['name']);
                    }else{
                      $('#empGroup').html('N/A');                      
                    }
                    if(data.role['name']){
                      $('#empRole').html(data.role['name']);
                    }else{
                      $('#empRole').html('N/A');                      
                    }

                    if(data.empData['designation']){
                      $('#empDesignation').html(data.designationname['name']);
                    }else{
                      $('#empDesignation').html('N/A');
                    }
                    if(data.superior){
                      $('#empSuperior').html(data.superior['name']);
                    }else{
                      $('#empSuperior').html('N/A');
                    }
                    if(data.empData['total_salary']){
                      $('#empTS').html(data.empData['total_salary']);
                    }else{
                      $('#empTS').html('N/A');
                    }
                    if(data.empData['permitted_leave']){
                      $('#empPL').html(data.empData['permitted_leave']);
                    }else{
                      $('#empPL').html('N/A');
                    }
                    @if(config('settings.ncal')==1)
                      if(data.empData['doj']){
                        var doj = AD2BS(data.empData['doj']);
                        doj = doj.split('-');
                        doj = doj[2]+' '+getNepaliMonthsName(doj[1])+' '+doj[0];
                        doj = data.empData['formatted_doj'];
                      }else{
                        var doj = 'N/A';
                      }
                    @else
                      if(data.empData['doj']){
                        var doj = data.empData['doj'];
                            doj = data.empData['formatted_doj'];
                      }else{
                        var doj = 'N/A';
                      }
                    @endif
                    @if(config('settings.ncal')==1)
                      if(data.empData['lwd']){
                        var lwd = AD2BS(data.empData['lwd']);
                        lwd = lwd.split('-');
                        lwd = lwd[2]+' '+getNepaliMonthsName(lwd[1])+' '+lwd[0];
                        lwd = data.empData['formatted_lwd'];
                      }else{
                        var lwd = 'N/A';
                      }
                    @else
                      if(data.empData['lwd']){
                        var lwd = data.empData['lwd'];
                            lwd = data.empData['formatted_lwd'];
                      }else{
                        var lwd = 'N/A';
                      }
                    @endif
                    $('#empDOJ').html(doj);
                    $('#empLWD').html(lwd);
                }else if(data.code===201){
                  alert(data.error);
                }else{
                  alert("Some issue that is not being handeled");
                }           
              },
              error:function(){
                $('#ActivateCompanyUpdate').children().first().attr('disabled',false);
                alert('Sorry, something went wrong.')
              }
          });
      });

      $('#UpdateBankDetail').on('submit',function(e){
        e.preventDefault();
        var url = "{{domain_route('company.admin.employee.ajaxBankUpdate')}}";
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('#ActivateBankUpdate').children().first().attr('disabled',true);
              },
              success: function (data) {
                if(data.code===200){
                    alert(data.success);
                    $('#ActivateBankUpdate').children().first().attr('disabled',false);
                    $('#ActivateBankUpdate').addClass('hide');
                    $('#ActivateBankCancel').addClass('hide');
                    $('#ActivateBankEdit').removeClass('hide');
                    $('.text-form').attr('hidden',true);
                    $('.text-display').attr('hidden',false);
                    if(data.empData['acc_holder']){
                      $('#empAccHolder').html(data.empData['acc_holder']);
                    }else{
                      $('#empAccHolder').html('N/A');
                    }
                    if(data.empData['acc_number']){
                      $('#empAccNumber').html(data.empData['acc_number']);
                    }else{
                      $('#empAccNumber').html('N/A');
                    }
                    if(data.empData['bank_name']){
                      $('#empBankName').html(data.empData['bank_name']);
                    }else{
                      $('#empBankName').html('N/A');
                    }
                    if(data.empData['ifsc_code']){
                      $('#empIFSC').html(data.empData['ifsc_code']);
                    }else{
                      $('#empIFSC').html('N/A');
                    }
                    if(data.empData['pan']){
                      $('#empPAN').html(data.empData['pan']);
                    }else{
                      $('#empPAN').html('N/A');
                    }
                    if(data.empData['branch']){
                      $('#empBranch').html(data.empData['branch']);
                    }else{
                      $('#empBranch').html('N/A');
                    }
                }else if(data.code===201){
                  alert(data.error);
                }else{
                  alert("Some issue that is not being handeled");
                }           
              },
              error:function(){
                $('#ActivateBankUpdate').children().first().attr('disabled',false);
                alert('Sorry, something went wrong.')
              }
          });
      });

      $('#UpdateDocumentDetail').on('submit',function(e){
        e.preventDefault();
        var url = "{{domain_route('company.admin.employee.ajaxDocumentUpdate')}}";
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
                $('#ActivateDocumentUpdate').children().first().attr('disabled',true);
              },
              success: function (data) {
                if(data.code===200){
                    alert(data.success);
                    $('#ActivateDocumentUpdate').children().first().attr('disabled',false);
                    $('#ActivateDocumentUpdate').addClass('hide');
                    $('#ActivateDocumentCancel').addClass('hide');
                    $('#ActivateDocumentEdit').removeClass('hide');
                    $('.text-form').attr('hidden',true);
                    $('.text-display').attr('hidden',false);
                    if(data.empData.resume_url){
                      $('#empResume').html(data.empData.resume_url);
                    }else{
                      $('#empResume').html('N/A');                      
                    }
                    if(data.empData['offer_letter_url']){
                      $('#empOL').html(data.empData['offer_letter_url']);
                    }else{
                      $('#empOL').html('N/A');
                    }
                    if(data.empData['joining_letter_url']){
                      $('#empJL').html(data.empData['joining_letter_url']);
                    }else{
                      $('#empJL').html('N/A');
                    }
                    if(data.empData['contract']){
                      $('#empCA').html(data.empData['contract_url']);
                    }else{
                      $('#empCA').html('N/A');
                    }
                    if(data.empData['id_proof']){
                      $('#empIDP').html(data.empData['id_proof_url'])
                    }

                }else if(data.code===201){
                  alert(data.error);
                }else{
                  alert("Some issue that is not being handeled");
                }           
              },
              error:function(){
                $('#ActivateDocumentUpdate').children().first().attr('disabled',false);
                alert('Sorry, something went wrong.')
              }
          });
      });

      $('#UpdateAccountDetail').on('submit',function(e){
        e.preventDefault();
        var url = "{{domain_route('company.admin.employee.ajaxAccountUpdate')}}";
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('#ActivateAccountUpdate').children().first().attr('disabled',true);
              },
              success: function (data) {
                if(data.code===200){
                    alert(data.success);
                    $('#ActivateAccountUpdate').children().first().attr('disabled',false);
                    $('#ActivateAccountUpdate').addClass('hide');
                    $('#ActivateAccountCancel').addClass('hide');
                    $('#ActivateAccountEdit').removeClass('hide');
                    $('.text-form').attr('hidden',true);
                    $('.text-display').attr('hidden',false);
                    if(data.empData['email']){
                      $('#empEmail').html(data.empData['email']);
                    }else{
                      $('#empEmail').html('N/A');
                    }
                    if(data.empData['phone']){
                      $('#empMob').html('+'+data.empData['country_code']+'-'+data.empData['phone']);
                    }else{
                      $('#empMob').html('N/A');
                    }
                }else if(data.code===201){
                  alert(data.error);
                }else{
                  alert("Some issue that is not being handeled");
                }           
              },
              error:function(){
                $('#ActivateAccountUpdate').children().first().attr('disabled',false);
                alert('Sorry, something went wrong.')
              }
          });
      });

      $('#TransferUser').on('submit',function(e){
        e.preventDefault();
        var url = "{{domain_route('company.admin.employee.transferUser')}}";
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
                $('#transfer').attr("disabled", "disabled");
              },
              success: function (data) {
                if(data['result']==true){
                  alert('Transfered Successfully');
                  location.reload();
                }else{
                  alert('Transfered Failed!');
                }
                
                $('#transfer').attr("disabled", false);
              },
              error:function(){
                console.log('found some 500 internal server issues');
                
                $('#transfer').attr("disabled", false);
              }
          });
      });

      $('.btn-red').on('click',function(e){
        e.preventDefault(e);
        $('#doc_type').val($(this).data('type'))
        $('#deleteDocument').modal('show');
      });

      $('#delDoc').on('submit',function(e){
        e.preventDefault();
        var doc_type=$('#doc_type').val();
        var emp_id = '{{$employee->id}}';
        var url = '{{domain_route('company.admin.employee.removeDoc')}}';
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'doc_type':doc_type,
                  'emp_id':emp_id,
              },
              beforeSend:function(){
                $('.delDocbtn').attr('disabled',true);
              },
              success: function (data) {
                $('#deleteDocument').modal('hide');
                $('.delDocbtn').attr('disabled',false);
                if(data['result']==true){
                  alert('Document successfully Removed');
                  if(data['doc_type']=="resume"){
                    $('#empResume').html('N/A');
                  }else if(data['doc_type']=="offer_letter"){
                    $('#empOL').html('N/A');
                  }else if(data['doc_type']=="joining_letter"){
                    $('#empJL').html('N/A');                    
                  }else if(data['doc_type']=="contract"){
                    $('#empCA').html('N/A');   
                  }else if(data['doc_type']=="id_proof"){
                    $('#empIDP').html('N/A');   
                  }
                }else{
                  alert('Document deletion failed');
                }
              },
              error:function(){
                $('#deleteDocument').modal('hide');
                $('.delDocbtn').attr('disabled',false);
                alert('Sorry, something went wrong.')
              }
          });

      });

      /* initialize the external events
           -----------------------------------------------------------------*/
          function init_events(ele) {
              ele.each(function () {

                  // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                  // it doesn't need to have a start or end
                  var eventObject = {
                      title: $.trim($(this).text()) // use the element's text as the event title
                  }

                  // store the Event Object in the DOM element so we can get to it later
                  $(this).data('eventObject', eventObject)

                  // make the event draggable using jQuery UI
                  // $(this).draggable({
                  //   zIndex        : 1070,
                  //   revert        : true, // will cause the event to go back to its
                  //   revertDuration: 0  //  original position after the drag
                  // })

              })
          }

          init_events($('#external-events div.external-event'))

          /* initialize the calendar
           -----------------------------------------------------------------*/
          //Date for the calendar events (dummy data)
          var date = new Date()
          var d = date.getDate(),
              m = date.getMonth(),
              y = date.getFullYear()
          $('#calendar').fullCalendar({
              header: {
                  left: 'prev,next today',
                  center: 'title',
                  right: 'month,agendaWeek,agendaDay'
              },

              buttonText: {
                  today: 'today',
                  month: 'month',
                  week: 'week',
                  day: 'day'
              },
              dayClick: function (date, jsEvent, view) {
                
              },
              eventMouseover: function (event, jsEvent, view) {
                  $(this).attr('title', event.title);
              },
              events: [
                @foreach($employee->attendances as $attendance)
                {
                  title          : 'Present',
                  start          : '{{$attendance->adate}}',
                  end            : '{{$attendance->adate}}',
                  backgroundColor:  '#48c065', //red
                  borderColor    :  '#48c065', //red
                  id             : '{{$attendance->id}}', 
                  allDay         :  true,                 
                },
                @endforeach    
                @foreach($holidays as $holiday)
                {
                  title          : '{{$holiday->name}}',
                  start          : '{{$holiday->start_date}}',
                  end            : '{{$data['nextday_end'][$holiday->id]}}',
                  backgroundColor:  '#de3535', //red
                  borderColor    :  '#de3535', //red
                  id             : '{{$holiday->id}}', 
                  allDay         :  true,   
                },
                @endforeach  
                @foreach($beforeTodays as $attendance)
                {
                  title          : 'Absent',
                  start          : '{{$attendance}}',
                  end            : '{{$attendance}}',
                  backgroundColor:  '#f58641', //red
                  borderColor    :  '#f58641', //red 
                  allDay         :  true,                 
                },
                @endforeach                             
              ],
              editable: false,
              droppable: false, // this allows things to be dropped onto the calendar !!!
              
          });


          $('#order').addClass('fullwidth');
          $('#collection').addClass('fullwidth');
          $('#expense').addClass('fullwidth');
          $('#orders').addClass('fullwidth');
          $('#orders').addClass('fullwidth');

          @if(config('settings.ncal')==1)

          var today = moment().format('YYYY-MM-DD');
          var currentYear = moment().year();
          var currentMonth = moment().month()+1;
          var currentDay = moment().date();
          var Weekday = moment().day();

          var NepaliCurrentDate = AD2BS(today);
          var nepaliDateData = NepaliCurrentDate.split('-');
          var nepaliCurrentYear = nepaliDateData[0];
          var nepaliCurrentMonth = nepaliDateData[1];
          var nepaliCurrentDay = nepaliDateData[2];

          

          $('.fc-next-button').click(function(e){
            e.preventDefault(e);
            var getMonth = parseInt($('#calNepaliMonth').val())+1;
            var getYear = parseInt($('#calNepaliYear').val());
            if(getMonth>12){
              getMonth = 1;
              getYear = getYear+1;
            }
            if(getMonth<10){
              getMonth = '0'+getMonth;
            }
            var firstEnd = getFirstDateEndDate(getYear,getMonth);
            engFirstDate = BS2AD(firstEnd[0]);
            engLastDate = BS2AD(firstEnd[11]);
            currentmnepalidates = getCurrentMonthFirstEndDates(getYear,getMonth);
            var engCMFDate = BS2AD(currentmnepalidates[0]);
            var engCMLDate = BS2AD(currentmnepalidates[1]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.employee.getAttendances')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'id':{{$employee->id}},
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                    'engCMFDate' : engCMFDate,                    
                    'engCMFDate':engCMFDate,
                    'engCMLDate':engCMLDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateAttendanceEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateAttendanceEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateAttendanceEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateAttendanceEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateAttendanceEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateAttendanceEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                    $('#TNAbsDays').html('Absent: '+data['absents'].length+' days');
                    $('#TNPreDays').html('Present: '+data['presents'].length+' days');
                    // $('#NWOff').html('Weekly Off: '+data['WOff']+' days');
                    // $('#TNHolidays').html('Holidays: '+data['hollyDays']+' days');
                }
            });
          });




          $('.fc-prev-button').click(function(e){
            e.preventDefault(e);
            getMonth = parseInt($('#calNepaliMonth').val())-1;
            getYear = parseInt($('#calNepaliYear').val());
            if(getMonth<1){
              getMonth = 12;
              getYear = getYear-1;
            }
            if(getMonth<10){
              getMonth = '0'+getMonth;
            }
            var firstEnd = getFirstDateEndDate(getYear,getMonth);
            engFirstDate = BS2AD(firstEnd[0]);
            engLastDate = BS2AD(firstEnd[11]);
            currentmnepalidates = getCurrentMonthFirstEndDates(getYear,getMonth);
            var engCMFDate = BS2AD(currentmnepalidates[0]);
            var engCMLDate = BS2AD(currentmnepalidates[1]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.employee.getAttendances')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'id':{{$employee->id}},
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                    'engCMFDate':engCMFDate,
                    'engCMLDate':engCMLDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateAttendanceEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateAttendanceEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateAttendanceEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateAttendanceEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateAttendanceEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateAttendanceEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                    $('#TNAbsDays').html('Absent: '+data['absents'].length+' days');
                    $('#TNPreDays').html('Present: '+data['presents'].length+' days');
                    // $('#NWOff').html('Weekly Off: '+data['WOff']+' days');
                    // $('#TNHolidays').html('Holidays: '+data['hollyDays']+' days');
                }
            });
          });

          $('#todayMonth').click(function(e){
            e.preventDefault(e);
            getMonth = nepaliCurrentMonth;
            getYear = nepaliCurrentYear;
            if(getMonth<1){
              getMonth = 12;
              getYear = getYear-1;
            }
            var firstEnd = getFirstDateEndDate(getYear,getMonth);
            engFirstDate = BS2AD(firstEnd[0]);
            engLastDate = BS2AD(firstEnd[11]);
            currentmnepalidates = getCurrentMonthFirstEndDates(getYear,getMonth);
            var engCMFDate = BS2AD(currentmnepalidates[0]);
            var engCMLDate = BS2AD(currentmnepalidates[1]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.employee.getAttendances')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'id':{{$employee->id}},
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                    'engCMFDate':engCMFDate,
                    'engCMLDate':engCMLDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateAttendanceEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateAttendanceEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateAttendanceEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateAttendanceEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateAttendanceEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateAttendanceEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                    $('#TNAbsDays').html('Absent: '+data['absents'].length+' days');
                    $('#TNPreDays').html('Present: '+data['presents'].length+' days');
                    // $('#NWOff').html('Weekly Off: '+data['WOff']+' days');
                    // $('#TNHolidays').html('Holidays: '+data['hollyDays']+' days');
                }
            });
          });
          $('#todayMonth').click();
          @else
            $('.fc-today-button').on('click',function(){
                var tglCurrent = $('#calendar').fullCalendar('getDate');
                var date = moment(tglCurrent);
                var engStartDate=date.startOf('Month').format('YYYY-MM-DD');
                var engEndDate=date.endOf('Month').format('YYYY-MM-DD');
                var year = date.year();
                var month = date.month();
                $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{domain_route('company.admin.employee.getAttendancesCount')}}",
                  type: "POST",
                  data: {
                      '_token': '{{csrf_token()}}',
                      'id':{{$employee->id}},
                      'getMonth': month,
                      'getYear': year,
                      'engCMFDate':engStartDate,
                      'engCMLDate':engEndDate,
                  },
                  success: function (data) {                  
                      $('#TAbsDays').html('Absent: '+data['absents'].length+' days');
                      $('#TPreDays').html('Present: '+data['presents']+' days');
                      // $('#WOff').html('Weekly Off: '+data['WOff']+' days');
                      // $('#THolidays').html('Holidays: '+data['hollyDays']+' days');
                    }
                });
            });
            $('.fc-today-button').click();
            $('.fc-prev-button').on('click',function(){
                var tglCurrent = $('#calendar').fullCalendar('getDate');
                var date = moment(tglCurrent);
                var engStartDate=date.startOf('Month').format('YYYY-MM-DD');
                var engEndDate=date.endOf('Month').format('YYYY-MM-DD');
                var year = date.year();
                var month = date.month();
                $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{domain_route('company.admin.employee.getAttendancesCount')}}",
                  type: "POST",
                  data: {
                      '_token': '{{csrf_token()}}',
                      'id':{{$employee->id}},
                      'getMonth': month,
                      'getYear': year,
                      'engCMFDate':engStartDate,
                      'engCMLDate':engEndDate,
                  },
                  success: function (data) {                  
                      $('#TAbsDays').html('Absent: '+data['absents'].length+' days');
                      $('#TPreDays').html('Present: '+data['presents']+' days');
                      // $('#WOff').html('Weekly Off: '+data['WOff']+' days');
                      // $('#THolidays').html('Holidays: '+data['hollyDays']+' days');
                    }
                });              
            });
            $('.fc-next-button ').on('click',function(){
               var tglCurrent = $('#calendar').fullCalendar('getDate');
                var date = moment(tglCurrent);
                var engStartDate=date.startOf('Month').format('YYYY-MM-DD');
                var engEndDate=date.endOf('Month').format('YYYY-MM-DD');
                var year = date.year();
                var month = date.month();
                $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: "{{domain_route('company.admin.employee.getAttendancesCount')}}",
                  type: "POST",
                  data: {
                      '_token': '{{csrf_token()}}',
                      'id':{{$employee->id}},
                      'getMonth': month,
                      'getYear': year,
                      'engCMFDate':engStartDate,
                      'engCMLDate':engEndDate,
                  },
                  success: function (data) {                  
                      $('#TAbsDays').html('Absent: '+data['absents'].length+' days');
                      $('#TPreDays').html('Present: '+data['presents']+' days');
                      // $('#WOff').html('Weekly Off: '+data['WOff']+' days');
                      // $('#THolidays').html('Holidays: '+data['hollyDays']+' days');
                    }
                });    
            });
          @endif 

          $('select[name="designation"]').on('change', function () {
          var designation = $(this).val();
            if (designation) {
                $.ajax({
                    url: '/admin/employee/getsuperiorlist?designation='+designation,
                    type: "GET",
                    dataType: "json",
                    cache: false,
                    success: function (data) {
                        $("#superior").empty();
                        var emp_id = '{{$employee->id}}';
                        var superior = '{{$employee->superior}}';
                        $.each(data, function (i, item) {
                            $('<optgroup />').prop('label', i).appendTo('#superior');
                            $.each(item, function (key, value) {
                              if(emp_id!=value.id){
                                if(superior==value.id){
                                  $('<option></option>').val(value.id).text(value.name).appendTo('#superior').appendTo('#superior').prop('selected', true);

                                }else{
                                  $('<option></option>').val(value.id).text(value.name).appendTo('#superior');

                                }
                              }
                            });
                        });

                    }
                });
              } else {
                  $('#superior').empty();
              }
          });

          $('select[name="designation"]').change();

          function bs_input_file() {
            $(".input-file").before(
              function() {
                if ( ! $(this).prev().hasClass('input-ghost') ) {
                  var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
                  element.attr("name",$(this).attr("name"));
                  element.change(function(){
                    element.next(element).find('input').val((element.val()).split('\\').pop());
                  });
                  $(this).find("button.btn-choose").click(function(){
                    element.click();
                  });
                  $(this).find("button.btn-reset").click(function(){
                    element.val(null);
                    $(this).parents(".input-file").find('input').val('');
                  });
                  $(this).find('input').css("cursor","pointer");
                  $(this).find('input').mousedown(function() {
                    $(this).parents('.input-file').prev().click();
                    return false;
                  });
                  return element;
                }
              }
            );
          }
          $(function() {
            bs_input_file();
          });

          $(function() {
              $(document).on("change",".uploadFile", function()
              {
                  $(this).closest(".imgUp").find('.imagePreview').empty();
                  var uploadFile = $(this);
                  var files = !!this.files ? this.files : [];
                  if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
           
                  if (/^image/.test( files[0].type)){ // only image file
                      var reader = new FileReader(); // instance of the FileReader
                      reader.readAsDataURL(files[0]); // read the local file
           
                      reader.onloadend = function(){ // set image data as background of div
                      uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url("+this.result+")").addClass('display-imglists').attr('src',this.result);
                      }
                  }
                
              });

          });

          $('.alert-modal').on('click',function(){
            $('#alertModal').modal('show');
          });
          $('.alert-user-modal').on('click',function(){
            $('#alertUserModal').modal('show');
          });

      $('input[name=list_type]').on("click", function() {
        var superior = $("#superior").val();
        var currentVal = $('#employeeId-enableClickableOptGroups').val();
        if(superior==null){
          superior = "{{Auth::user()->EmployeeId()}}";
        }
        var listType = $("input[name='list_type']:checked").val();
        @if($employee->user_id == Auth::user()->id)
          currentVal = "{{json_encode($handles)}}";
        @elseif($employee->user_id != Auth::user()->id && $employee->is_admin != 1)
          let disbaledInputs = $('#employeeId-enableClickableOptGroups')[0].selectedOptions;
          if(disbaledInputs.length>0){
            for (let item of disbaledInputs) {
              currentVal.push(item.value.toString());
            }
          }
        @endif
        getSuperiorParty(superior, listType, currentVal);
      });
      
      function getSuperiorParty(superior, listType, currentVal){
        let juniorParties = "{{$getJuniorParties}}";//JSON.parse("{{$getJuniorParties}}");
        if(superior!=null){
          $.ajax({
            url: "{{domain_route('company.admin.employee.getsuperiorparties')}}",
            type: "GET",
            data:{
              'superior': superior,
              'listType': listType
            },
            cache: false,
            success: function (data) {
              if(data!=""){
                var beatParties = data; 
                $('#employeeId-enableClickableOptGroups').multiselect('destroy');
                $('#employeeId-enableClickableOptGroups').empty();

                $.each(beatParties, function (i, item) {
                  var optgrouping = "<optgroup label='"+item['name']+"' value='"+item['id']+"'></optgroup>";
                  var options = [];
                  var clients = item['clients'];
                  $.each(clients,function(id, name){
                    @if(Auth::user()->EmployeeId() == $employee->id)
                      if(currentVal.includes(id)){
                        options.push("<option value='"+ id +"' selected disabled>"+name+"</option>");
                      }
                    @else
                      if(currentVal.includes(id)){
                        if(juniorParties.includes(parseInt(id))){
                          options.push("<option value='"+ id +"' selected disabled>"+name+"</option>");
                        }else{
                          options.push("<option value='"+ id +"' selected>"+name+"</option>");
                        }
                      }else{
                        options.push("<option value='"+ id +"' @if($employee->is_admin==1) selected disabled @endif>"+name+"</option>");
                      }
                    @endif
                  });
                  var grouping = $(optgrouping).html(options.join(''));
                  $('#employeeId-enableClickableOptGroups').append(grouping);
                });

                  @if(Auth::user()->EmployeeId()==$employee->id || $employee->is_admin == 1)
                    $('#employeeId-enableClickableOptGroups').multiselect({
                      enableFiltering: true,
                      enableCaseInsensitiveFiltering: true,
                      enableFullValueFiltering: true,
                      enableClickableOptGroups: false,
                      includeSelectAllOption: false,
                      enableCollapsibleOptGroups : true,
                      selectAllNumber: false,
                      nonSelectedText:"Select Parties",
                      disableIfEmpty:true,
                    });
                  @else
                    $('#employeeId-enableClickableOptGroups').multiselect({
                      enableFiltering: true,
                      enableCaseInsensitiveFiltering: true,
                      enableFullValueFiltering: true,
                      enableClickableOptGroups: true,
                      includeSelectAllOption: true,
                      enableCollapsibleOptGroups : true,
                      selectAllNumber: false,
                      nonSelectedText:"Select Parties",
                      disableIfEmpty:true,
                    });
                  @endif

              }else{
                $('#employeeId-enableClickableOptGroups').multiselect('destroy');
                $('#employeeId-enableClickableOptGroups').empty();
                $('#employeeId-enableClickableOptGroups').multiselect({
                  enableFiltering: true,
                  enableCaseInsensitiveFiltering: true,
                  enableFullValueFiltering: true,
                  enableClickableOptGroups: true,
                  includeSelectAllOption: true,
                  enableCollapsibleOptGroups : true,
                  selectAllNumber: false,
                  nonSelectedText:"Select Parties",
                  disableIfEmpty:true,
                });
              }
            }
          });
        }
      }
  });

  $(document).ready(function(){
    @if(empty($beats))
      $('.nonePartyAssigned').removeClass('hidden');
    @endif
  })
  $(document).on('click','.alert-modal',function(){
    $('#alertModal').modal('show');
  });
  function customExportAction(config, exportData, modName, propertiesArray, colsArray){
    $('#exportedData').val(JSON.stringify(exportData));
    $('#pageTitle').val(config.title);
    $('#moduleName').val(modName);
    $('#columns').val(colsArray);
    $('#properties').val(propertiesArray);
    $('#pdf-generate').submit();
  }

  @if(Auth::user()->can('zeroorder-view'))
      var zeroOrderColumns = [
                            {"data" : "id"},
                            // {"data" : "contact_person"},
                            // {"data" : "party_type"},
                            // {"data" : "contact_number"},
                            // {"data" : "address"},
                            {"data" : "date"},
                            {"data" : "remark"},
                            {"data" : "client_name"},
                            {"data": "action"}
                            ];
      function initializeZODT(employeeID){
        ordertable = $('#zero_order').DataTable({
          "stateSave": true,
          "stateSaveParams": function (settings, data) {
          data.search.search = "";
          },
          "order": [[ 1, "desc" ]],
          "serverSide": true,
          "processing": false,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
              { "width": "5%", "targets": 0 },
              { "width": "15%", "targets": 1 },
              { "width": "60%", "targets": 2 },
              { "width": "20%", "targets": 3 },
          ],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Zero Order List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Zero Order List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Zero Order List of {{$employee->name}}', 
              exportOptions: {
                columns: [0,1,2,3],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.employee.employeeZeroOrdersTable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              employeeID:employeeID,
            },
            beforeSend:function(){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
            },
            error:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            },
            complete:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": zeroOrderColumns,
        });
        ordertable.buttons().container()
            .appendTo('#zeroOrderexports');
        var orderOldExportAction = function (self, e, dt, button, config) {
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

        var orderNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$ordersCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].client_name = $(settings.json.data[key].client_name)[0].textContent;
                });
                properties = JSON.stringify(["id", "date", "remark", "client_name"]);
                columns = JSON.stringify(["S.No.", "Date", "Remark", "Party Name"]);
                customExportAction(config, settings.json.data, 'client-noorder', properties, columns);
              }else{
                orderOldExportAction(self, e, dt, button, config);
              }
              // orderOldExportAction(self, e, dt, button, config);
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
      }
      var employeeid = '{{$employee->id}}';
      initializeZODT(employeeid);
      $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
        $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
      } );
      @endif
  </script>
  @if(Auth::user()->can('PartyVisit-view'))
    @include('company.employees.customjs.party-visit')
  @endif
@endsection