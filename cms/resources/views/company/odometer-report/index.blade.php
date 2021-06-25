@extends('layouts.company')
@section('title', 'Odometer Report')

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/delta.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>

    @if(config('settings.ncal')==1)
        <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
    @else
        <link rel="stylesheet"
              href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    @endif
    <style>

        .btn-primary {
            background-color: #079292 !important;
            border-color: #079292 !important;
            color: #fff !important;
        }

        .btn-primary:hover, .btn-primary:active, .btn-primary.hover {
            background-color: #0b7676 !important;
            border-color: #0b7676 !important;
        }

        .close {
            font-size: 30px;
            color: #080808;
            opacity: 1;
        }

        #totalCollectionAmt {
            line-height: 3;
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

        #getReport{
            width: 100%;
            margin-top: 30px;
            margin-left: 10px;
        }

        @media print {
            tr > td:last-of-type {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Odometer Report</h3>
                        <span id="buttonsPlacement" class="pull-right"></span>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" id="mainBox">
                        <div class="row">
                            <div class="col-xs-2"></div>
                            <div class="col-xs-7">
                                <div class="row">
                                    <div class="select-2-sec">
                                        <div class="col-xs-3">
                                            <div style="margin-top:30px; " id="userFilter" hidden></div>
                                        </div>
                                        <div class="col-xs-4">
                                            @if(config('settings.ncal')==0)
                                                <div id="reportrange" name="reportrange" class="hidden reportrange"
                                                     style="margin-top: 30px ;min-width: 215px; display:none;" >
                                                    <i class="fa fa-calendar"></i>&nbsp;
                                                    <span></span> <i class="fa fa-caret-down"></i>
                                                </div>
                                                <input id="start_edate" type="text" name="start_edate"
                                                       placeholder="Start Date" hidden/>
                                                <input id="end_edate" type="text" name="end_edate"
                                                       placeholder="End Date" hidden/>
                                            @else
                                                <div class="input-group hidden" id="nepCalDiv"
                                                     style="margin-top: 30px; display:none;">
                                                    <input id="start_ndate" class="form-control" type="text"
                                                           name="start_ndate"
                                                           placeholder="Start Date" autocomplete="off"
                                                           style="width: 85px;padding: 0 0 0 2px;"/>
                                                    <input id="start_edate" type="text" name="start_edate"
                                                           placeholder="Start Date" hidden/>
                                                    <span class="input-group-addon" aria-readonly="true"><i
                                                                class="glyphicon glyphicon-calendar"></i></span>
                                                    <input id="end_ndate" class="form-control" type="text"
                                                           name="end_ndate" placeholder="End Date"
                                                           autocomplete="off" style="width: 85px;padding: 0 0 0 2px;"/>
                                                    <input id="end_edate" type="text" name="end_edate"
                                                           placeholder="End Date" hidden/>
                                                    <button id="filterTable" style="color:#0b7676!important;" hidden><i
                                                                class="fa fa-filter"
                                                                aria-hidden="true"></i></button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-xs-3">
                                            <button class="btn btn-primary" id="getReport" style="display:none;">
                                                <span><i class="fa fa-book"></i> View Report</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-3"></div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div id="loader1">
                            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
                        </div>
                        <div class="container-fluid" style="width:auto;">
                            <div class="tablediv table-responsive">

                            </div>
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

{{--    <form method="post" action="{{domain_route('company.admin.downloadPdf')}}" class="pdf-export-form hidden"--}}
{{--          id="pdf-generate">--}}
{{--        {{csrf_field()}}--}}
{{--        <input type="text" name="columns" class="columns" id="columns">--}}
{{--        <input type="text" name="properties" class="properties" id="properties">--}}
{{--        <input type="text" name="exportedData" class="exportedData" id="exportedData">--}}
{{--        <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">--}}
{{--        <button type="submit" id="genrate-pdf">Generate PDF</button>--}}
{{--    </form>--}}

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
                        Sorry! You are not authorized to view this user info.
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
    <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @if(config('settings.ncal')==1)
        <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
    @else
        <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    @endif

    <script>

        var userSelect = "<select name='user_ids[]' id='user_filters' class='user_filters' multiple> @foreach($salesMens as $id=>$men)<option value='{{$men->id}}'>{{$men->name}}</option>@endforeach </select>";

        $('#userFilter').append(userSelect);

        $('#user_filters').multiselect({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            enableFullValueFiltering: false,
            enableClickableOptGroups: false,
            includeSelectAllOption: true,
            selectAllText: 'All',
            enableCollapsibleOptGroups: true,
            selectAllNumber: false,
            nonSelectedText: "Select users",
            disableIfEmpty: true,
            numberDisplayed: 1,
        });

        $('#user_filters').multiselect('selectAll', false);
        $('#user_filters').multiselect('updateButtonText');

        $(document).on('click', '.buttons-columnVisibility', function () {
            if ($(this).hasClass('active')) {
                $(this).find('input').first().prop('checked', true);
                console.log($(this).find('input').first().prop('checked'));
            } else {
                $(this).find('input').first().prop('checked', false);
                console.log($(this).find('input').first().prop('checked'));
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

        $(document).on('click', '.alert-modal', function () {
            $('#alertModal').modal('show');
        });

        $('document').ready(function(){
            $('#getReport').click();
        })

        @if(config('settings.ncal')==0)

        var start = moment().startOf('month');
        var end = moment();


        function cb(start, end) {
            $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            $('#startdate').val(start.format('MMMM D, YYYY'));
            $('#enddate').val(end.format('MMMM D, YYYY'));
            $('#start_edate').val(start.format('Y-MM-DD'));
            $('#end_edate').val(end.format('Y-MM-DD'));
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

        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $('#start_edate').val(start);
            $('#end_edate').val(end);

            var start = $('#start_edate').val();
            var end = $('#end_edate').val();

            // console.log(start,end);
            // if (startD != '' || endD != '') {
            //     // $('#employee').DataTable().destroy();
            //     // initializeDT(empVal, start, end);
            // }
        });

        $('#reportrange').removeClass('hidden');
        @else

        var lastmonthdate = AD2BS(moment().startOf('month').format('YYYY-MM-DD'));
        var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
        $('#start_ndate').val(lastmonthdate);
        $('#end_ndate').val(ntoday);
        $('#start_edate').val(BS2AD(lastmonthdate));
        $('#end_edate').val(BS2AD(ntoday));
        $('#nepCalDiv').removeClass('hidden');

        $('#start_ndate').nepaliDatePicker({
            ndpEnglishInput: 'englishDate',
            onChange: function () {
                $('#start_edate').val(BS2AD($('#start_ndate').val()));
                if ($('#start_ndate').val() > $('#end_ndate').val()) {
                    $('#end_ndate').val($('#start_ndate').val());
                    $('#end_edate').val(BS2AD($('#start_ndate').val()));
                }


                var start = $('#start_edate').val();
                var end = $('#end_edate').val();
                // console.log(start, end)
                if (end == "") {
                    end = start;
                }
                if (start != '' || end != '') {
                    // $('#employee').DataTable().destroy();
                    // initializeDT(empVal, start, end);
                }
            }
        });

        $('#end_ndate').nepaliDatePicker({
            onChange: function () {
                $('#end_edate').val(BS2AD($('#end_ndate').val()));
                if ($('#end_ndate').val() < $('#start_ndate').val()) {
                    $('#start_ndate').val($('#end_ndate').val());
                    $('#start_edate').val(BS2AD($('#end_ndate').val()));
                }
                var empVal = $('.user_filters').find('option:selected').val();
                if (empVal == "null") {
                    empVal = null;
                }

                var start = $('#start_edate').val();
                var end = $('#end_edate').val();
                if (start == "") {
                    start = end;
                }
                if (start != '' || end != '') {
                    // $('#employee').DataTable().destroy();
                    // initializeDT(empVal, start, end);
                }
            }
        });
        @endif

        var start = $('#start_edate').val();
        var end = $('#end_edate').val();

        $('#getReport').on('click', function () {
            $('#buttonsPlacement').html('');
                submitRequest();
        });

        var ncal = "{{config('settings.ncal')}}";

        function submitRequest(){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{domain_route('company.admin.odometer.report.ajaxDatatable') }}",
                data:
                    {
                        empVal: $('#user_filters').val(),
                        startDate: $('#start_edate').val(),
                        endDate: $('#end_edate').val(),
                    },
                beforeSend: function (url, data) {
                    $('#mainBox').addClass('box-loader');
                    $('#getReport').attr('disabled',true);
                    $('#loader1').removeAttr('hidden');
                },
                success: function (data) {
                    $('.tablediv').html(data);
                    @if(config('settings.ncal')==0)
                    $('#mYear').html('<b>'+$('#datepicker').val() +'</b>');
                    @else
                    $('#mYear').html('<b>'+ $('#nepDate').val() +'</b>');
                    @endif
                    $('#loader1').attr('hidden','hidden');
                    $('#mainBox').removeClass('box-loader');
                    $('#getReport').attr('disabled',false);
                    $('#userFilter').removeAttr('hidden',false)
                    $('#getReport').show()

                    if (ncal === '0'){
                        $('#reportrange').show()
                    }else {
                        $('#nepCalDiv').show()
                    }


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert("No records found. ");
                },
            });
        }

        // window.addEventListener("load", function(){
        //     $("#userFilter").removeAttr('hidden',false);
        // });


    </script>
@endsection