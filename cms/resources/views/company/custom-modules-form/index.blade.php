@extends('layouts.company')
@section('title', $custom_fields->name)

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/delta.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    @if(config('settings.ncal')==1)
        <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
    @else
        <link rel="stylesheet"
              href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    @endif
    <style>
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

        .direct-chat-gotimg {
            border-radius: 50%;
            float: left;
            width: 40px;
            padding: 0px 0px;
            height: 42px;
            background-color: grey;
        }


    </style>
@endsection

@section('content')

    <section class="content">
        <div class="row">
            <div class="col-xs-12">

                @if (\Session()->has('success'))
                    <div class="alert alert-success">
                        <p>{{ \Session::get('success') }}</p>
                    </div><br/>
                @endif

                @if (session()->has('message'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                        <p>{{ \Session::get('message') }}</p>
                    </div>
                @endif

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{$custom_fields->name}}</h3>
                        @if(auth()->user()->can(str_replace('_','-',$custom_fields->table_name) . '-create'))
                            <a href="{{ domain_route('company.admin.custom.modules.form.create',['id' => $custom_fields->id]) }}"
                               class="btn btn-primary pull-right"
                               style="margin-left: 5px;">
                                <i class="fa fa-plus"></i> Create New
                            </a>
                        @endif
                        <span id="customFormModule" class="pull-right"></span>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" id="mainBox">
                        <div class="row">
                            <div class="col-xs-2"></div>
                            <div class="col-xs-7">
                                <div class="row">
                                    <div class="select-2-sec">
                                        <div class="col-xs-3">
                                            <div style="margin-top:10px; " id="userFilter"></div>
                                        </div>
                                        <div class="col-xs-3">
                                            @if(config('settings.ncal')==0)
                                                <div id="reportrange" name="reportrange" class="hidden reportrange"
                                                     style="margin-top: 10px;min-width: 215px;">
                                                    <i class="fa fa-calendar"></i>&nbsp;
                                                    <span></span> <i class="fa fa-caret-down"></i>
                                                </div>
                                                <input id="start_edate" type="text" name="start_edate"
                                                       placeholder="Start Date" hidden/>
                                                <input id="end_edate" type="text" name="end_edate"
                                                       placeholder="End Date" hidden/>
                                            @else
                                                <div class="input-group hidden" id="nepCalDiv"
                                                     style="margin-top: 10px;">
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
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-3"></div>
                        </div>
                        <div class="table-responsive">
                            <table id="employee" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>S.No</th>
                                    @foreach($custom_field_title as $title)
                                        <th>{{$title}}</th>
                                    @endforeach
                                    <th>Action</th>

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

    <form method="post" action="{{domain_route('company.admin.custom.modules.form.custompdfdexport')}}"
          class="pdf-export-form hidden"
          id="pdf-generate">
        {{csrf_field()}}
        <input type="text" name="exportedData" class="exportedData" id="exportedData">
        <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
        <input type="text" name="columns" class="columns" id="columns">
        <input type="text" name="properties" class="properties" id="properties">
        <button type="submit" id="genrate-pdf">Generate PDF</button>
    </form>

    <div class="modal modal-default fade" id="delete" tabindex="-1" custom_module="dialog"
         aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" custom_module="document">
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
                        <input type="hidden" name="custom_module_id" id="m_id" value="">

                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button> --}}
                        <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
                    </div>
                </form>
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

    <div class="modal modal-default fade" id="partyModal" tabindex="-1" role="dialog" aria-labelledby="myClientModalLabel"
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
                        Sorry! You are not authorized to view this Party info.
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

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @if(config('settings.ncal')==1)
        <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
    @else
        <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    @endif

    <script>
        $(document).ready(function () {
            $('#delete').on('show.bs.modal', function (event) {

                var button = $(event.relatedTarget)

                var mid = button.data('mid')

                var url = button.data('url');

                $(".remove-record-model").attr("action", url);

                var modal = $(this)

                modal.find('#myModalLabel').html('Delete Confirmation');

                modal.find('.modal-body #m_id').val(mid);

            });
        });


        @if(config('settings.ncal')==0)

        var start = moment().subtract(29, 'days');
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
            var empVal = $('.user_filters').find('option:selected').val();
            if (empVal == "null") {
                empVal = null;
            }
            var startD = $('#start_edate').val();
            var endD = $('#end_edate').val();
            if (startD != '' || endD != '') {
                $('#employee').DataTable().destroy();
                initializeDT(empVal, start, end);
            }
        });

        $('#reportrange').removeClass('hidden');
        @else

        var lastmonthdate = AD2BS(moment().subtract(30, 'days').format('YYYY-MM-DD'));
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
                var empVal = $('.user_filters').find('option:selected').val();
                if (empVal == "null") {
                    empVal = null;
                }

                var start = $('#start_edate').val();
                var end = $('#end_edate').val();
                if (end == "") {
                    end = start;
                }
                if (start != '' || end != '') {
                    $('#employee').DataTable().destroy();
                    initializeDT(empVal, start, end);
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
                    $('#employee').DataTable().destroy();
                    initializeDT(empVal, start, end);
                }
            }
        });
        @endif

        var userSelect = "<select name='user_id' id='user_filters' class='user_filters'><option></option><option value=null>All</option> @foreach($employeesWithDatas as $id=>$employee)<option value='{{$id}}'>{{$employee}}</option>@endforeach </select>";

        $('#userFilter').append(userSelect);

        $('#user_filters').select2({
            "placeholder": "Select Creator",
        });

        var table;
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        // Load Data Table on ready
        initializeDT(null, start, end);

        $('body').on("change", ".user_filters", function () {
            var empVal = $(this).find('option:selected').val();
            if (empVal === "null") {
                empVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if (empVal !== '') {
                $('#employee').DataTable().destroy();
                initializeDT(empVal, start, end);
            }
        });
        // console.log()

        function initializeDT(empVal = null, startD, endD) {
            {{--console.log({{ count($custom_field_slug) }});--}}
            {{--var columnCount = {{ count($custom_field_slug) }};--}}

            const table = $('#employee').removeAttr('width').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[({{count($custom_field_slug)}}-1), "desc"]],
                "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
                    "<'row'<'col-xs-6'><'col-xs-6'>>" +
                    "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
                "buttons": [
                    {
                        extend: 'colvis',
                        order: 'alpha',
                        className: 'dropbtn',
                        columns: [':visible :not(:last-child)'],
                        text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                        columnText: function (dt, idx, title) {
                            return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col" + idx + "' class='check' type='checkbox'><label for='col" + idx + "'></label></div></div><div class='col-xs-9 pad-left'>" + title + "</div></div>";
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: "{{$custom_fields->name}}",
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        },
                        action: function ( e, dt, node, config ) {
                            newExportAction( e, dt, node, config );
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: "{{$custom_fields->name}}",
                        exportOptions: {
                            columns: ':visible :not(:last-child)',
                        },
                        action: function (e, dt, node, config) {
                            newExportAction(e, dt, node, config);
                        }
                    },
                    {
                        extend: 'print',
                        title: "{{$custom_fields->name}}",
                        exportOptions: {
                            columns: [':visible :not(:last-child)'],
                        },
                        action: function (e, dt, node, config) {
                            newExportAction(e, dt, node, config);
                        }
                    },
                ],
                "ajax":
                    {
                        "url": "{{domain_route('company.admin.custom.modules.form.ajaxDatatable') }}",
                        "dataType": "json",
                        "type": "POST",
                        "data": {
                            _token: "{{csrf_token()}}",
                            id: {{$custom_fields->id}},
                            empVal: empVal,
                            startDate: startD,
                            endDate: endD,
                        },
                        beforeSend: function () {
                            $('#mainBox').addClass('box-loader');
                            $('#loader1').removeAttr('hidden');
                        },
                        error: function () {
                            $('#mainBox').removeClass('box-loader');
                            $('#loader1').attr('hidden', 'hidden');
                        },
                        complete: function () {
                            $('#mainBox').removeClass('box-loader');
                            $('#loader1').attr('hidden', 'hidden');
                        }
                    },
                "columns": [
                    {
                        "data": "id"
                    },
                        @foreach($custom_field_slug as $column)
                    {
                        "data": "{{ $column }}"
                    },
                        @endforeach
                    {
                        "data": "action"
                    }
                ],
            });
            table.buttons().container().appendTo('#customFormModule');
        }

        var oldExportAction = function (self, e, dt, button, config) {
            if(button[0].className.indexOf('buttons-excel') >= 0) {
                if($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                }else{
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                }
            }else if(button[0].className.indexOf('buttons-pdf') >= 0) {
                if($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
                }else{
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                }
            }else if(button[0].className.indexOf('buttons-print') >= 0) {
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
                data.length = {{$form_data}};
                dt.one('preDraw', function (e, settings) {
                    if(button[0].className==="btn btn-default buttons-pdf buttons-html5"){
                        var columnsArray = [];
                        var visibleColumns = settings.aoColumns.map(setting => {
                            if(setting.bVisible){
                                columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                            }
                        })
                        columnsArray.pop("Action")
                        var columns = JSON.stringify(columnsArray);

                        $.each(settings.json.data, function(key, htmlContent){
                            console.log(settings.json.data);
                            settings.json.data[key].id = key+1;
                        });
                        customExportAction(config, settings, columns);
                    }else{
                        oldExportAction(self, e, dt, button, config);
                    }
                    // oldExportAction(self, e, dt, button, config);
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

        function customExportAction(config, settings, cols){
            $('#exportedData').val(JSON.stringify(settings.json));
            $('#pageTitle').val(config.title);
            $('#columns').val(cols);
            var propertiesArray = [];
            var visibleColumns = settings.aoColumns.map(setting => {
                if(setting.bVisible) propertiesArray.push(setting.data)
            })
            propertiesArray.pop("action")
            // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
            var properties = JSON.stringify(propertiesArray);
            $('#properties').val(properties);
            $('#pdf-generate').submit();
        }


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

        $(document).on('click', '.party-modal', function () {
            $('#partyModal').modal('show');
        });

    </script>
@endsection