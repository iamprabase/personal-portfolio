@extends('layouts.company')
@section('title', 'Parties')

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    @if(config('settings.ncal')==1)
        <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
    @else
        <link rel="stylesheet"
              href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/delta.css') }}">
    <style type="text/css" media="screen">
        #importBtn {
            margin-right: 5px;
            border-radius: 0px;
        }

        .direct-chat-gotimg {
            border-radius: 50%;
            float: left;
            width: 40px;
            padding: 0px;
            height: 42px;
            background-color: grey;
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

        .round input {
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

        .pad-left {
            padding-left: 0px;
        }

        .close {
            font-size: 30px;
            color: #080808;
            opacity: 1;
        }

        .partyStatusCheckBox {
            position: relative;
            margin-right: 5px !important;
            height: auto;
        }

        .partyImgName {
            display: flex;
        }

        #reportrange, #nepCalDiv {
            width: 215px;
            /* margin-left: 20px; */
        }

        #reportrangediv {
            padding-right: 0px;
            margin-right: 0px;
            width: auto;
        }

        .no-pd {
            padding: 0;
        }

        .ndp-nepali-calendar {
            width: 90px !important;
            padding: 2px;
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

        .pd-rt-0 {
            padding-right: 0px;
        }

        .pd-lt-0 {
            padding-left: 0px;
        }

        .btn-group.width-adjust {
            min-width: auto;
        }

        .multiselect-selected-text {
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

        .round input {
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

        .pad-left {
            padding-left: 0px;
        }

        .ordFilter {
            width: 125px;
            margin-top: 10px;
        }

        #reportrange, .select-2-sec {
            display: flex;
        }

        .col {
            margin-right: 10px;
        }

        .dt-button-collection.dropdown-menu {
            display: block !important;
            top: 40px !important;
            left: 0px !important;

        }

        .clickable {
            cursor: pointer;
        }

        .panel-heading span {
            margin-top: -20px;
            font-size: 15px;
        }

        .panel-primary > .panel-heading {
            color: #fff;
            background-color: #499e9c;
            border-color: #499e9c;
        }

        .panel-primary {
            border-color: #f5f3f3;
        }

        .panel-body {
            padding: 15px;
            height: 70%;
            height: 200px;
        }

        .info-box-content__wrapper {
          display: flex;
          justify-content:space-between;
        }

        .info-box-content__wrapper .btn-group {
          width: auto;
          margin-left: 7px;
        }

        .info-box-content__wrapper .btn {
          border: none;
          padding: 5px;
          background: none;
        }

        .info-box-content__wrapper .btn .fa, .btn-group .btn .fa {
          font-size: 10px;
        }

        .info-box-content__wrapper .dropdown-menu > li > a:hover, .btn-group .dropdown-menu > li > a:hover {
          background-color: #00c0ef;
          color: #fff !important;
        }

        .info-box-content__wrapper .info-box-text {
          white-space: normal;
          overflow: visible;
        }

        .panel-dropdown>li>a{
          cursor: pointer;
        }

        .nodeValue{
          font-size: 12px;
          font-weight: 100;
        }

        .partyNeverOrdered{
          margin-top: 20px;
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
                    </div>
                @endif
                @if (\Session::has('warning'))
                    <div class="alert alert-warning">
                        <p>{{ \Session::get('warning') }}</p>
                    </div>
                @endif
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{ $clienttype }} List
                        </h3>
                        
                        <span id="clientexports" class="pull-right"></span>
                        @if((Auth::user()->can('party-status') && checkpartytypepermission($party_type_id,'status')) || (Auth::user()->can('party-delete') && checkpartytypepermission($party_type_id,'delete')))
                            <div class="dropdown pull-right tips"
                                 title="Mass Actions(Change Status and Mass Delete)" style="margin-right: 5px;">
                                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">⋮
                                </button>
                                <ul class="dropdown-menu">
                                    @if( Auth::user()->can('party-status') && checkpartytypepermission($party_type_id,'status') )
                                        <li><a href="#" class="updateStatuses">Change Status</a></li>
                                    @endif
                                    @if( Auth::user()->can('party-delete') && checkpartytypepermission($party_type_id,'delete') )
                                        <li><a href="#" class="mass_action" id="mass_delete" data-type="massdelete">Mass
                                                Delete</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body table-responsive" id="mainBox">
                        <div class="container-fluid">
                            <!-- <div class="row" id="infoBoxes">
                            </div> -->
                            <!-- <a
                              class="btn btn-primary panel-collapsed clickable" style="margin-bottom:5px;">
                                <i class="fa fa-cogs"></i> Filter Results
                            </a> -->
                            <div class="row">
                                <div class="col-xs-12 mt-40">
                                    <!-- <div class="panel panel-primary hidden">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><label>Filter By</label></h4>
                                            <span class="pull-right panel-collapsed"><i class="fa fa-arrow-down"></i></span>
                                        </div>
                                        <div class="panel-body" style="display: none;">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <div class="col-xs-3">
                                                            <div style="margin-top:10px; " id="businessFilter">
                                                                <label for="business">Business</label>
                                                                <select name="business"
                                                                        class="select2 businessId hidden" id="business">
                                                                    <option value=""></option>
                                                                    <option value="0">Select All &nbsp&nbsp</option>
                                                                    @foreach($business_types as $id=>$business)
                                                                        <option value="{{$id}}">{{$business}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-3">
                                                            <div style="margin-top:10px;" id="createdByFilter">
                                                                <label for="created_by">Created By</label>
                                                                <select name="created_by"
                                                                        class="select2 createdBy hidden" id="">
                                                                    <option value=""></option>
                                                                    <option value="0">Select All &nbsp&nbsp</option>
                                                                    @foreach($created_by as $id=>$creator)
                                                                        <option value="{{$id}}">{{$creator}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-4" id="reportrangediv"
                                                             style="margin-top: 10px; ">

                                                            <label for="date_range">Party Created Date </label>
                                                            @if(config('settings.ncal')==0)
                                                                <div id="reportrange" name="reportrange"
                                                                     class="reportrange hidden">
                                                                    <i class="fa fa-calendar"></i>&nbsp;
                                                                    <span></span> <i class="fa fa-caret-down"></i>
                                                                </div>
                                                                <input id="start_edate" type="text" name="start_edate"
                                                                       hidden/>
                                                                <input id="end_edate" type="text" name="end_edate"
                                                                       hidden/>
                                                            @else
                                                                <div class="input-group hidden" id="nepCalDiv">
                                                                    <span class="input-group-addon ndateaddon" aria-readonly="true"><input id="start_ndate" class="form-control" type="text" name="start_ndate"  placeholder="Start Date" autocomplete="off"/></span>
                                                                    <span class="input-group-addon"
                                                                          aria-readonly="true"><i
                                                                                class="glyphicon glyphicon-calendar"></i></span>
                                                                    <input id="end_ndate" class="form-control"
                                                                           type="text" name="end_ndate"
                                                                           placeholder="End Date"
                                                                           autocomplete="off"/>
                                                                    <input id="start_edate" type="text"
                                                                           name="start_edate" placeholder="Start Date"
                                                                           hidden/>
                                                                    <input id="end_edate" type="text" name="end_edate"
                                                                           placeholder="End Date" hidden/>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="row">
                                                <div class="col-xs-2 pull-right">
                                                    <button type="button" id="searchFilter"
                                                            class="btn btn-primary pull-right"
                                                            style="margin-top: 35px;">
                                                        Submit</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div> -->
                                </div>
                                <div class="row">
                                    <div class="col-xs-2">
                                    </div>
                                </div>
                            </div>
                            <div id="mainBox">

                                <table id="client" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@if(Auth::user()->can('party-status') && checkpartytypepermission($party_type_id,'status') || Auth::user()->can('party-delete') && checkpartytypepermission($party_type_id,'delete'))
                                                <input type='checkbox' id='selectthispage' name='selectthispage'
                                                       style="height: max-content;margin-right: 10px;"> @endif #
                                        </th>
                                        <th>Party Name</th>
                                        <th>Person Name</th>
                                        <th>Phone</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Address Line1</th>
                                        <th>Address Line2</th>
                                        <th>Business Type</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th style="min-width: 80px;">Action</th>
                                    </tr>
                                    </thead>
                                    <div id="loader1" hidden>
                                        <img src="{{asset('assets/dist/img/loader2.gif')}}"/>
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
                    {{csrf_field()}}
                    {{method_field('delete')}}
                    <div class="modal-body">
                        <p class="text-center">
                            Are you sure you want to delete this?
                        </p>
                        <input type="hidden" name="client_id" id="c_id" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal modal-default fade" id="mass-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
                </div>
                <form method="post" class="remove-record-model" id="massDeleteForm">
                    {{method_field('delete')}}
                    {{csrf_field()}}
                    <div class="modal-body">
                        <p class="text-center">
                            Are you sure you want to delete selected items?
                        </p>
                    </div>
                    <input type="hidden" name="party_id[]" id="party_ids" value="">

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
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
                    <form class="form-horizontal" role="form" id="changeStatuses" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" name="client_id[]" id="party_ids" value="">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Status</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="status" name="status" required="true">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="btn_statuses_change" type="submit" class="btn btn-primary actionBtnStatuses">
                                <span id="footer_action_button" class='glyphicon'> </span> Update Statuses
                            </button>
                            {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
                                  <span class='glyphicon glyphicon-remove'></span> Close
                                </button> --}}
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
                    <input type="text" id="accountType" name="account_type" hidden/>
                </div>
                <div class="modal-footer">
                    {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="changeStatus" method="POST"
                          action="{{URL::to('admin/client/changeStatus')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="client_id" id="client_id" value="">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="name">Status</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="status" name="status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn actionBtn">
                                <span id="footer_action_button" class='glyphicon'> </span> Change
                            </button>
                            {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
                              <span class='glyphicon glyphicon-remove'></span> Close
                            </button> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-default fade" id="alertUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                        Sorry! You are not authorized to view this user details.
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
    <form method="post" action="{{domain_route('company.admin.client.customPdfExport')}}" class="pdf-export-form hidden"
          id="pdf-generate">
        {{csrf_field()}}
        <input type="text" name="exportedData" class="exportedData" id="exportedData">
        <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
        <input type="text" name="columns" class="columns" id="columns">
        <input type="text" name="properties" class="properties" id="properties">
        <button type="submit" id="genrate-pdf">Generate PDF</button>
    </form>
@endsection

@section('scripts')
    <script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    @if(config('settings.ncal')==1)
        <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
        <script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
    @else
        <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    @endif
    <script>

        $(document).on('click', '.clickable', function (e) {
            var $this = $(this);
            if (!$this.hasClass('panel-collapsed')) {
                $('.panel').find('.panel-body').slideUp();
                $this.addClass('panel-collapsed');
                // $this.find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                setTimeout(() => {
                  $('.panel-primary').addClass('hidden');
                }, 300);
            } else {
                $('.panel-primary').removeClass('hidden');
                $('.panel').find('.panel-body').slideDown();
                $this.removeClass('panel-collapsed');
                // $this.find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
            }
        })
        $(document).on("click", ".empLinks", function (e) {
            if ($(this).data('viewable') == "") {
                e.preventDefault();
                $('#alertUserModal').modal('show');
                // $('#alertModalText').html('Sorry! You are not authorized to view this user details.');
            }
        });
        let table;
        $(function () {
            $('#delete').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var mid = button.data('mid');
                var url = button.data('url');
                $(".remove-record-model").attr("action", url);
                var modal = $(this);
                modal.find('#myModalLabel').html('Delete Confirmation');
                modal.find('.modal-body #m_id').val(mid);
            });

            $('.createdBy').select2({
                "placeholder": "Select Creator",
            });

            $('.businessId').select2({
                "placeholder": "Select Business Type",
            });
            $(document).on('click', '.updateStatuses', function () {
                $('#footer_acti, on_button').addClass('glyphicon-check');
                $('.modal-title').text('Update Multiple Status');
                $('#party_id').val($(this).data('id'));
                const partyIds = $('#pageIds').val();
                if (partyIds == "") {
                    alert("Please Select Parties.");
                } else {
                    $('#upateStatuses').modal('show');
                    $('#upateStatuses').children().find('#changeStatuses')[0].action = "{{ domain_route('company.admin.client.changeStatus') }}";
                    $('#upateStatuses').children().find('#formMethod').remove();
                    $('#upateStatuses').children().find('#party_ids').val(partyIds);
                }
            });
            $(document).on('click', '#mass_delete', function () {
                $('#footer_action_button').addClass('glyphicon-check');
                $('.modal-title').text('Delete Multiple Party.');
                $('#party_id').val($(this).data('id'));
                const partyIds = $('#pageIds').val();
                if (partyIds == "") {
                    alert("Please Select Parties.");
                } else {
                    $('#mass-delete').modal('show');
                    let action = "{{ domain_route('company.admin.party.massdestroy') }}";
                    $('#mass-delete').children().find('#massDeleteForm')[0].action = action;

                    // $('#mass-delete').children().find('#formMethod').remove();
                    // $("<input>").attr({
                    //   name: "_method",
                    //   type: "hidden",
                    //   value: "DELETE" ,
                    //   id: "formMethod"
                    // }).appendTo($('#mass-delete').children().find('#changeStatuses'));
                    $('#mass-delete').children().find('#party_ids').val(partyIds);
                }
            });
        });

        $(document).ready(function () {
            $('.select2').removeClass('hidden')
            @if (strpos(URL::previous(), domain_route('company.admin.client')) === false)
            var activeRequestsTable = $('#client').DataTable();
            activeRequestsTable.state.clear();  // 1a - Clear State
            activeRequestsTable.destroy();   // 1b - Destroy
            @endif
            initializeDT();
            $('#client').on('column-visibility.dt', function (e, settings, column, state) {

                let currentCols = JSON.parse(sessionStorage.getItem('DT_Colvis_Hide_PT' + "{{$party_type_id}}"))
                if (!state) {// Checked
                    currentCols = [...currentCols, column]
                } else {//Unchecked
                    currentCols = currentCols.filter(function (col) {
                        return col != column
                    })
                }
                sessionStorage.setItem('DT_Colvis_Hide_PT' + "{{$party_type_id}}", JSON.stringify(currentCols));
            });

            $('#searchFilter').click(function(){
                table.destroy()
                initializeDT()
            })

          
        });

        function getSelVal() {
            return $('#pageIds').val();
        }

        let targetHiddenColumns = new Array()
        if (sessionStorage.getItem('DT_Colvis_Hide_PT' + "{{$party_type_id}}")) {
            targetHiddenColumns = JSON.parse(sessionStorage.getItem('DT_Colvis_Hide_PT' + "{{$party_type_id}}"))
        } else {
            targetHiddenColumns = new Array(6, 7, 8, 9, 10, 11)
            sessionStorage.setItem('DT_Colvis_Hide_PT' + "{{$party_type_id}}", JSON.stringify(targetHiddenColumns));
        }

        function initializeDT() {
            var start_date = $(document).find('#start_edate').val();
            var end_date = $(document).find('#end_edate').val();
            var created_by = $('.createdBy').val();
            var business_id = $('.businessId').val();
            table = $('#client').removeAttr('width').DataTable({
                "processing": true,
                "serverSide": true,
                "stateSave": false,
                "order": [[0, "desc"]],
                "columnDefs": [
                    {
                        "orderable": false,
                        "targets": [0, -1],
                    },
                    {"targets": targetHiddenColumns, visible: false},
                    // {
                    //   "targets": [ 6,7,8 ],
                    //   "visible": false,
                    //   "searchable": false
                    // },
                    // {
                    //   width: 20,
                    //   targets: [0,7]
                    // },
                    {
                        "width": "6%",
                        "targets": [0],
                    }
                ],

                "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
                    "<'row'<'col-xs-6'><'col-xs-6'>>" +
                    "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
                "buttons": [
                    {
                        extend: 'colvis',
                        order: 'alpha',
                        className: 'dropbtn',
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                        columnText: function (dt, idx, title) {
                            return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col" + idx + "' class='check colCheck' type='checkbox' data-colno='" + idx + "' onclick='colvisChecked(" + idx + ")'><label for='col" + idx + "'></label></div></div><div class='col-xs-9 pad-left'>" + title + "</div></div>";
                        }
                    },
                  {
                        extend: 'excelHtml5',
                        title: '{{$clienttype}} List',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        action: function (e, dt, node, config) {
                            newExportAction(e, dt, node, config);
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: '{{$clienttype}} List',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        },
                        orientation: "landscape",
                        pageSize: 'LEGAL',
                        action: function (e, dt, node, config) {
                            newExportAction(e, dt, node, config);
                        }
                    },
                    {
                        extend: 'print',
                        title: '{{$clienttype}} List',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        action: function (e, dt, node, config) {
                            newExportAction(e, dt, node, config);
                        }
                    },
                ],
                "ajax":
                    {
                        "url": "{{ domain_route('company.admin.client.ajaxDatatable') }}",
                        "dataType": "json",
                        "type": "POST",
                        "data": {
                            _token: "{{csrf_token()}}",
                            client_type_id: "{{ $party_type_id }}",
                            selIds: getSelVal,
                            clients_count: "{{$clients}}",
                            analytics_filter: true,
                            dateRange: @json($dateRange),
                            type: "{{$type}}"
                        },
                        beforeSend: function () {
                            $('#mainBox').addClass('box-loader');
                            $('#loader1').removeAttr('hidden');
                        },
                        error: function () {
                            $('#mainBox').removeClass('box-loader');
                            $('#loader1').attr('hidden', 'hidden');
                        },
                        complete: function (data) {
                            if (data.status == 200) {
                                let tdata = data.responseJSON;
                                if (tdata.data.length > 0) {
                                    $("#selectthispage").prop("checked", tdata.selectThisPageCheckBox);
                                }
                            }
                            $('#mainBox').removeClass('box-loader');
                            $('#loader1').attr('hidden', 'hidden');
                        }
                    },
                lengthMenu: [5, 10, 20, 50, 100, 200, 500, 1000, 2000],
                "columns": [
                    {"data": "id"},
                    {"data": "company_name"},
                    {"data": "name"},
                    {
                        "data": 'phone',
                        render: function (data, type, row) {
                            if (data) {
                                var dateSplit = data.split(',');
                                return dateSplit[0];
                            } else {
                                return data;
                            }
                        }
                    },
                    {"data": "mobile"},
                    {"data": "email"},
                    {"data": "address_line1"},
                    {"data": "address_line2"},
                    {"data": "location"},
                    {"data": "business_type"},
                    {"data": "added_by"},
                    {"data": "created_at"},
                    {"data": "status"},
                    {"data": "action"},
                ],
            });
            table.buttons().container().appendTo('#clientexports');
        }

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
                data.length = {{$clients}};
                dt.one('preDraw', function (e, settings) {
                    if (button[0].className == "btn btn-default buttons-pdf buttons-html5") {

                        var columnsArray = [];
                        var visibleColumns = settings.aoColumns.map(setting => {
                            if (setting.bVisible) columnsArray.push(setting.sTitle.replace(/<[^>]+>/g, '').trim())
                        })
                        columnsArray.pop("Action")
                        // columnsArray.push("S.No.", "Party Name", "Salesman", "Date", "Remark");
                        var columns = JSON.stringify(columnsArray);

                        $.each(settings.json.data, function (key, htmlContent) {
                            settings.json.data[key].company_name = $(settings.json.data[key]?.company_name)[0]?.textContent;
                            settings.json.data[key].status = $(settings.json.data[key].status)[0]?.textContent;
                            settings.json.data[key].id = key + 1;
                        });
                        customExportAction(config, settings, columns);
                    } else {
                        oldExportAction(self, e, dt, button, config);
                    }
                    // oldExportAction(self, e, dt, button, config);
                    dt.one('preXhr', function (e, s, data) {
                        setting, s._iDisplayStart = oldStart;
                        data.start = oldStart;
                        $('#mainBox').removeClass('box-loader');
                        $('#loader1').attr('hidden', 'hidden');
                    });
                    setTimeout(dt.ajax.reload, 0);
                    return false;
                });
            });
            dt.ajax.reload();
        };

        function customExportAction(config, settings, cols) {
            $('#exportedData').val(JSON.stringify(settings.json));
            $('#pageTitle').val(config.title);
            $('#columns').val(cols);
            var propertiesArray = [];
            var visibleColumns = settings.aoColumns.map(setting => {
                if (setting.bVisible) propertiesArray.push(setting.data)
            })
            propertiesArray.pop("action");
            // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
            var properties = JSON.stringify(propertiesArray);
            $('#properties').val(properties);
            $('#pdf-generate').submit();
        }

        function pushClientIds() {
            let party_ids = [];
            $.each($("input[name='update_party_status']:checked"), function () {
                party_ids.push($(this).val());
            });
            return party_ids;
        }

        $('body').on('change', '.partyStatusCheckBox', function () {
            if (this.checked) {
                let currentVal = $('#pageIds').val();
                let getCheckedIds = pushClientIds();
                if (currentVal != "") {
                    currentVal = currentVal.split(',');
                    $.each(currentVal, function (ind, val) {
                        if (!getCheckedIds.includes(val)) {
                            getCheckedIds.push(val);
                        }
                    });
                }
                $('#pageIds').val(getCheckedIds);
                if ($("input[name='update_party_status']").not(':checked').length == 0) $("#selectthispage").prop("checked", true);

            } else {
                let uncheckVal = $(this).val();
                let currentVal = $('#pageIds').val().split(',');
                let newVal = currentVal.filter(function (value, index, arr) {
                    return value != uncheckVal;
                });
                $('#pageIds').val(newVal);
                $("#selectthispage").prop("checked", false);
            }
        });
        $('#selectthispage').click(function (event) {
            event.stopPropagation();
            if ($("input[name='update_party_status']").length == 0) $("#selectthispage").prop("checked", false);
            if (this.checked) {
                $("input[name='update_party_status']").prop("checked", true);
                let currentVal = $('#pageIds').val();
                let getCheckedIds = pushClientIds();
                if (currentVal != "") {
                    currentVal = currentVal.split(',');
                    $.each(currentVal, function (ind, val) {
                        if (!getCheckedIds.includes(val)) {
                            getCheckedIds.push(val);
                        }
                    });
                }
                $('#pageIds').val(getCheckedIds);
            } else {
                $("input[name='update_party_status']").prop("checked", false);
                let uncheckedBoxes = $("input[name='update_party_status']").not(':checked');
                let uncheckVal = [];
                $.each($("input[name='update_party_status']").not(':checked'), function () {
                    uncheckVal.push($(this).val());
                });
                let currentVal = $('#pageIds').val().split(',');
                let newVal = currentVal.filter(function (value, index, arr) {
                    return !uncheckVal.includes(value);
                });
                $('#pageIds').val(newVal);
                $("#selectthispage").prop("checked", false);
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
            $('#client_id').val($(this).data('id'));
            $('#remark').val($(this).data('remark'));
            $('#status').val($(this).data('status'));
            $('#myModal').modal('show');
        });

        $(document).on('click', '.alert-modal', function () {
            $('#alertModal').modal('show');
        });


        $(".imgAdd").click(function () {
            var Imgcount = $("#imggroup .imgUp").length;
            if (Imgcount < 3) {
                if (Imgcount == 2) {
                    $(".imgAdd").hide();
                }
                $(this).closest(".row").find('.imgAdd').before('<div class="col-xs-4 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="expense_photo[]" type="file" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fa fa-times del"></i></div>');
            } else {
                $(".imgAdd").hide();
            }
        });
        $(document).on("click", "i.del", function () {
            var Imgcount = $("#imggroup .imgUp").length;
            if (Imgcount < 4) {
                $(".imgAdd").show();
            }
            $(this).parent().remove();
        });
        $(function () {
            $(document).on("change", ".uploadFile", function () {
                var uploadFile = $(this);
                var files = !!this.files ? this.files : [];
                if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

                if (/^image/.test(files[0].type)) { // only image file
                    var reader = new FileReader(); // instance of the FileReader
                    reader.readAsDataURL(files[0]); // read the local file

                    reader.onloadend = function () { // set image data as background of div
                        uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url(" + this.result + ")");
                    }
                }

            });
        });

        $(document).on('click', '.buttons-columnVisibility', function () {
            if ($(this).hasClass('active')) {
                $(this).find('input').first().prop('checked', true);
            } else {
                $(this).find('input').first().prop('checked', false);
            }
        });

        $(document).on('click', '.buttons-colvis', function (e) {
            var filterBox = $('.dt-button-collection');
            filterBox.find('li').each(function (k, v) {
                if ($(v).hasClass('active')) {
                    $(v).find('input').first().prop('checked', true);
                } else {
                    $(v).find('input').first().prop('checked', false);
                }
            });
        });


    </script>
@endsection