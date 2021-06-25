@extends('layouts.company')
@section('title', $customModule->name)

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/plugins/zoomImage/zoomer.css')}}">

    <style>

        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
            background-color: #fff;
            opacity: 1;
        }

        #img-upload {
            width: 80%;
            height: 80%;
        }

        .panel-heading {
            color: #fff !important;
            background-color: #0b7676 !important;
        }

        .del-img {
            position: absolute;
            right: 32px;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            background-color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }


        .box-body .btn-primary {
            background-color: #079292 !important;
            border-color: #079292 !important;
            color: #fff !important;
        }

        .btn-primary:hover, .btn-primary:active, .btn-primary.hover {
            background-color: #0b7676 !important;
            border-color: #0b7676 !important;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ccc;
        }


        input[type="checkbox"] {
            vertical-align: middle;
        }

        img{
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1500; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        /* @-webkit-keyframes zoom {
          from {-webkit-transform:scale(0)}
          to {-webkit-transform:scale(1)}
        }

        @keyframes zoom {
          from {transform:scale(0)}
          to {transform:scale(1)}
        } */

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #000;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        @media only screen and (max-width: 700px){
            .modal-content {
                width: 100%;
            }
        }

        .delete, .edit{
            font-size: 15px !important;
        }
        .fa-edit, .fa-trash-o{
            padding-left: 5px;
        }
        .btn-warning{
            margin-right: 2px !important;
            color: #fff!important;
            background-color: #ec971f!important;
            border-color: #d58512!important;
        }
        .direct-chat-gotimg {
            float: left;
            width: 100px;
            padding: 0px 0px;
            height: 100px;
            background-color: grey;
        }

    </style>
@endsection

@section('content')

    <section class="content">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                        <div class="page-action pull-left">
                            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                        <div class="page-action pull-right">
                            {!!$action!!}
                        </div>
                </div>
                <div class="box-header with-border">
                    <h3 class="box-title">{{$customModule->name}} View</h3>

                    <div class="box-body">
                    <table class="table table-responsive table-striped">
                        @php $i = 0; @endphp
                        @foreach($datas as $key => $data)
                            <tr>
                                <th scope="row">{{$customFieldsTitle[$i++]}}</th>
                                @if(!is_array($data))
                                    <td>{!! $data !!} </td>
                                @else
                                    <td>
                                        @foreach($data as $value)
                                            @if(!$loop->last)
                                                {!! $value !!} ,
                                            @else
                                                {!! $value !!}
                                            @endif
                                        @endforeach
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        </div>
    </section>

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


@endsection

<div id="myModal" class="modal custommodal">
    <span class="close zoom-close">&times;</span>
    <img class="modal-content zoom-modal-content" id="img01">
    <div id="caption"></div>
</div>



@section('scripts')
    <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{asset('assets/plugins/zoomImage/zoomer.js')}}"></script>

    {{-- These config value are passed as js variable
     to dynamic-field.js as config value in js
     cannot be accessed directly --}}
    <script>
        let latitude = {{config('settings.latitude')}};
        let longitude = {{config('settings.longitude')}}
    </script>

    <script src="{{asset('assets/plugins/custom-fields/dynamic-fields.js')}}"></script>
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

        $(document).on('click', '.alert-modal', function () {
            $('#alertModal').modal('show');
        });
        $(document).on('click', '.party-modal', function () {
            $('#partyModal').modal('show');
        });
    </script>

@endsection

