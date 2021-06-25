@extends('layouts.company')
@section('title', 'Customs Form Edit')

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <style>

        .input-group-addon {
            padding: 0px 0px;
            font-size: 14px;
            font-weight: normal;
            line-height: 1;
            color: #555;
            text-align: center;
            background-color: #eee;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .box-body .btn-success {
            background-color: #00da76 !important;
            border-color: #00da76 !important;
            color: #fff !important;
        }

        .addCancelBtn {
            width: 40%;
            margin-left: 5px;
        }

        .select2-container--default {
            background-color: #fff;
            border: 1px solid #d2d6de;
            border-radius: 0px;
        }

        .input-group .input-group-addon {
            border-radius: 0;
            border-color: #fff;
            background-color: #fff;
            width: 220px;
        }

        .main-group {
            width: -webkit-fill-available;
        }

        .btnTd {
            width: 20%;
        }


        .errLabel {
            color: red;
        }


        .close {
            font-size: 30px;
            color: #080808;
            opacity: 1;
        }

        .btn-primary {
            background-color: #079292 !important;
            border-color: #079292 !important;
            color: #fff !important;
        }

        .btn-primary:hover, .btn-primary:active, .btn-primary.hover {
            background-color: #0b7676 !important;
            border-color: #0b7676 !important;
        }

        input[type="checkbox"] {
            vertical-align: middle;
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
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{$module->name}}</h3>

                        <button class="btn btn-primary pull-right addNewCustomField"
                                style="color:white;background-color: #0b7676!important;
                    border-color: #0b7676!important;margin-right:15px;"
                                data-module="Party">
                            <i class="fa fa-plus"></i>
                            Add New Fields
                        </button>
                        <div class="box-tools pull-right">
                            <div class="col-md-7 page-action text-right">
                                <a href="{{domain_route('company.admin.custom.modules')}}"
                                   class="btn btn-default btn-sm"> <i
                                            class="fa fa-arrow-left"></i> Back</a>
                            </div>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="mainBox">
                            <table id="employee" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Field Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($custom_modules_field as $key => $field)
                                    <tr class="row1" data-id="{{$field->id}}">
                                        <td>{{$key + 1}} <i class="fa fa-bars" id="handle-sort"></i></td>
                                        <td style="width:280px;max-width: 280px ;cursor: pointer; color: #01a9ac;"
                                            onclick="editField({{$field}} , $(this));">
                                            {{$field->title}} <span style="color: black">{{$field->required == 1 ? '*' : ''}}</span>

                                        </td>
                                        <td>{{$field->type}}</td>
                                        <td>
                                            @if($field->status == 'Active')
                                                <a href='#' class='edit-modal' data-id='{{$field->id}}'
                                                   data-status='{{$field->status}}'>
                                                    <span class='label label-success'>{{$field->status}}</span>
                                                </a>
                                            @elseif($field->status == 'Inactive')
                                                <a href='#' class='edit-modal' data-id='{{$field->id}}'
                                                   data-status='{{$field->status}}'>
                                                    <span class='label label-danger'>{{$field->status}}</span>
                                                </a>
                                            @endif

                                        </td>

                                        <td>
                                            <a class='btn btn-danger btn-sm delete' data-mid='{{$field->id}}'
                                               data-url='{{domain_route('company.admin.custom.modules.field.destroy', [$field->id])}}'
                                               data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i
                                                        class='fa fa-trash-o'></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
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
                          action="{{URL::to('admin/custom-module-form/change-status')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="custom_module_id" id="custom_module_id" value="">
                        <div class="form-group">
                            <label class="control-label col-xs-2" for="name">Status</label>
                            <div class="col-xs-10">
                                <select class="form-control" id="status" name="status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn actionBtn" onclick="confirmation()">
                                <span id="footer_action_button" class='glyphicon'> </span> Change
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="changeNameModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Change Name</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="changeName" method="POST"
                          action="{{URL::to('admin/formfields/update/name')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="module_id" value="{{$module->id}}">
                        <input type="hidden" name="field_id" id="field_id" value="">
                        <div class="form-group">
                            <div class="col-xs-10">
                                <label for="name">Change Name</label>
                                <input type="text" name="title" id="title" value="" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn actionBtn btn-success" onclick="confirms()">
                                <span id="footer_action_button" class='glyphicon glyphicon-check'> </span> Change
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    @include('company.custom-modules._custom-form-model')
@endsection

@section('scripts')
    <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('assets/plugins/settings/formfield.js')}}"></script>
    <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('assets/plugins/jQueryUI/jquery-ui.min.js')}}"></script>

    <script>
        function editField(object, element) {

            $("div[id^='innerfield-modal']").each(function (i, obj) {

                var temp = $(obj).find('h5').html();

                if (temp == 'Employee') {
                    temp = 'User';
                }

                if (temp == object.type) {

                    $(obj).modal('show');
                    $(obj).find('input').val(object.title);
                    if (object.required === 1) {
                        $(obj).find('#is_mandatory').prop("checked", true);
                    } else {
                        $(obj).find('#is_mandatory').prop("checked", false);
                    }
                    $(obj).find('textarea').val('');
                    if (object.type == "Single option" || object.type == "Multiple options" || object.type == "Check Box" || object.type == "Radio Button") {
                        var new_html = '';
                        JSON.parse(object.options).forEach(function (item) {
                            new_html += (item) + '\n';
                        });
                        $(obj).find('textarea').val(new_html);
                    }

                    $(obj).find('form').on('submit', function (e) {
                        e.preventDefault();
                        $('.submit').attr('disabled', 'true');
                        var dataid = object.id;
                        var url = "{{domain_route('company.admin.custom.modules.form.field.update')}}";
                        var is_mandatory = $(this).find('#is_mandatory').is(':checked');
                        data = {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            title: $(this).find('input').val(),
                            id: dataid,
                            is_mandatory: is_mandatory,
                            module_id: {{$module->id}},
                        };

                        if (object.type == "Single option" || object.type == "Multiple options" || object.type == "Check Box" || object.type == "Radio Button") {
                            var avalue = $(this).find('textarea').val();
                            var newVal = avalue.replace(/^\s*[\r\n]/gm, '');
                            var options = newVal.split(/\n/);
                            data = {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                title: $(this).find('input').val(),
                                id: dataid,
                                options: options,
                                type: object.type,
                                is_mandatory: is_mandatory,
                                module_id: {{$module->id}},
                            };
                        } else if (object.type === "Employee") {
                            type = "User";
                            data['type'] = type;
                        }
                        $.post(url, data, function (data) {
                            if (data.error === 'false') {
                                if (data.message.title === undefined) {
                                    alert(data.message);
                                } else {
                                    alert(data.message.title);
                                }

                                $('.submit').removeAttr('disabled');
                            } else {
                                $('.alert-danger').hide();
                                $('.modal').modal('hide');
                                location.reload();
                            }
                        });
                    });
                }
            });
        }
    </script>

    <script>
        initializeDT();
        $('.select2').select2();

        var module_id = {{$module->id}}
        $(function () {
            $('#delete').on('show.bs.modal', function (event) {

                var button = $(event.relatedTarget)

                console.log(button);

                var mid = button.data('mid')

                var url = button.data('url');

                $(".remove-record-model").attr("action", url);

                var modal = $(this)

                modal.find('#myModalLabel').html('Delete Confirmation');

                modal.find('.modal-body #m_id').val(mid);

            });
        });

        function initializeDT() {
            const table = $('#employee').removeAttr('width').DataTable({
                "processing": true,
                "serverSide": false,
                "order": [[0, "asc"]],
                'pageLength': {{count($custom_modules_field)}},
                "bPaginate": false,

                "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
                    "<'row'<'col-xs-6'><'col-xs-6'>>" +
                    "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
            });
        }

        $(document).on('click', '.edit-modal', function () {
            console.log('info');
            $('#footer_action_button').addClass('glyphicon-check');
            $('#footer_action_button').removeClass('glyphicon-trash');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('edit');
            $('.modal-title').text('Change Status');
            $('.deleteContent').hide();
            $('.form-horizontal').show();
            $('#custom_module_id').val($(this).data('id'));
            $('#remark').val($(this).data('remark'));
            $('#status').val($(this).data('status'));
            $('#warning').hide();
            $('#myModal').modal('show');
        });

        $(document).on('click', '.update-title', function () {
            // console.log($(this).data('id'), $(this).data('name'))
            $('#title').val($(this).data('title'));
            $('#field_id').val($(this).data('id'))
            $('#changeNameModel').modal('show');
        });

        function confirmation() {
            var result = confirm('Confirm to change the status?');
            if (result == true) {
                $('#changeStatus').submit();
            }
        }

        function confirms() {
            var result = confirm('Confirm to change Display Name?');
            if (result == true) {
                $('#changeName').submit();
            }
        }

        $(document).on('change', '#status', function () {
            if ($('#status option:selected').val() == 'Inactive')
                $("#warning").show();
            else
                $("#warning").hide();
        });
    </script>

    {{-- Ordering of the field--}}
    <script>
        $(function () {
            $("#employee").sortable({
                handle: "#handle-sort",
                items: "tr",
                opacity: 0.6,
                cursor: 'move',
                scroll: true,
                scrollSensitivity: 100,
                scrollSpeed: 0.5,
                update: function () {
                    sendOrdertoServer();
                },
                // sort: function(event, ui) {
                //     var currentScrollTop = $(window).scrollTop(),
                //         topHelper = ui.position.top,
                //         delta = topHelper - currentScrollTop;
                //     setTimeout(function() {
                //         $(window).scrollTop(currentScrollTop + delta);
                //     }, 5);
                // }
            });
            var position = [];

            function sendOrdertoServer() {
                $('tr.row1').each(function (index) {
                    position.push({
                        id: $(this).attr('data-id'),
                        order: index + 1
                    });
                    $("#custom_modules").sortable("disable");
                    //disabling the sortable so the update canbe implemented first
                });


                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "{{ route('company.admin.custom.modules.field.order',['domain' => request('subdomain')]) }}",
                    data: {
                        position: position,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        module_id: {{$module->id}}
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            location.reload()
                        }
                    },
                    error: (err) => {
                        console.log(err)
                    }
                });
            }
        });
    </script>
@endsection