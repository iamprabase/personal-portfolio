@extends('layouts.company')
@section('title', 'Schemes')

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <style>
        .select2.select2-container.select2-container--default, .select2.select2-container.select2-container--default.select2-container--focus {
            position: absolute;
            /* width: 50% !important; */
        }

        .box-loader {
            opacity: 0.5;
        }

        .direct-chat-img {
            padding: 0px;
        }

        .close {
            font-size: 30px;
            color: #080808;
            opacity: 1;
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
                @if (\session()->has('error'))
                    <div class="alert alert-error">
                        <p>{{ \Session::get('error') }}</p>
                    </div>
                    <br/>
                @endif

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Schemes</h3>
                        <a href="{{ domain_route('company.admin.scheme.create') }}" class="btn btn-primary pull-right"
                           style="margin-left: 5px;">
                            <i class="fa fa-plus"></i> Create New
                        </a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div id="mainBox">
                            <table id="employee" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Validity Dates</th>
                                    <th>Created on</th>
                                    <th>Status</th>
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
    <!-- Modal -->

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
                          action="{{URL::to('admin/scheme/changeStatus')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="schema_id" id="schema_id" value="">
                        <div class="form-group">
                            <label class="control-label col-xs-2" for="name">Status</label>
                            <div class="col-xs-10">
                                <select class="form-control" id="status" name="status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
{{--                        <p class="text-center" style="color:red;display: none" id="warning">--}}
{{--                            Warning: Changing the Module status to Inactive will turn all element of module to Inactive.--}}
{{--                        </p>--}}
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


@endsection

@section('scripts')
    <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
    <script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
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

        $(document).ready(function () {

            @if (strpos(URL::previous(), domain_route('company.admin.scheme')) === false)
            var activeRequestsTable = $('#custom_modules').DataTable();
            activeRequestsTable.state.clear();  // 1a - Clear State
            activeRequestsTable.destroy();   // 1b - Destroy
            @endif

            initializeDT();
        });

        function initializeDT() {
            const table = $('#employee').removeAttr('width').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[4, "desc"]],

                "dom": "<'row'<'col-xs-6'l><'col-xs-6'Bf>>" +
                    "<'row'<'col-xs-6'><'col-xs-6'>>" +
                    "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
                "buttons": [],
                "ajax":
                    {
                        "url": "{{domain_route('company.admin.scheme.ajaxDatatable') }}",
                        "dataType": "json",
                        "type": "POST",
                        "data": {
                            _token: "{{csrf_token()}}",
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
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "description"},
                    {"data": "start_date"},
                    {"data": "created_at"},
                    {"data": "status"},
                    {"data": "action"},
                ],
            });
        }


        $(document).on('click', '.edit-modal', function () {
            $('#footer_action_button').addClass('glyphicon-check');
            $('#footer_action_button').removeClass('glyphicon-trash');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('edit');
            $('.modal-title').text('Change Status');
            $('.deleteContent').hide();
            $('.form-horizontal').show();
            $('#schema_id').val($(this).data('id'));
            $('#remark').val($(this).data('remark'));
            $('#status').val($(this).data('status'));
            $('#warning').hide();
            $('#myModal').modal('show');
        });

        $(document).on('click', '.edit-name-modal', function () {
            $('#footer_action_button').addClass('glyphicon-check');
            $('#footer_action_button').removeClass('glyphicon-trash');
            $('.actionBtn').addClass('btn-success');
            $('.actionBtn').removeClass('btn-danger');
            $('.actionBtn').addClass('edit');
            $('.modal-title').text('Edit Custom Module Name');
            $('.deleteContent').hide();
            $('#module_id').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#changeNameModal').modal('show');
        });

        function confirmation() {
            var result = confirm('Confirm to change the status?');
            if (result == true) {
                $('#changeStatus').submit();
            }
        }

        $(document).on('change', '#status', function () {
            if ($('#status option:selected').val() == 'Inactive')
                $("#warning").show();
            else
                $("#warning").hide();
        });
    </script>

@endsection