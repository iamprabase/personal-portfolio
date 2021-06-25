@extends('layouts.app')
@section('title', 'Create Plan')
@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
    <style>
        .icheckbox_minimal-blue {
            margin-top: -2px;
            margin-right: 3px;
        }

        .checkbox label, .radio label {
            font-weight: bold;
        }

        .has-error {
            color: red;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 26px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
    <section class="content">

        <!-- SELECT2 EXAMPLE -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Plans</h3>

                <div class="box-tools pull-right">
                    <div class="col-md-7 page-action text-right">
                        <a href="{{ route('app.plan') }}" class="btn btn-default btn-sm"> <i
                                    class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

            {!! Form::open(['route' => ['app.plan.store'],  'files'=> true ]) !!}
            @include('admin.plans._form')
            <!-- Submit Form Button -->
                {!! Form::submit('Add Plan', ['class' => 'btn btn-primary pull-right']) !!}
                {!! Form::close() !!}

            </div>
        </div>

    </section>


@endsection

@section('scripts')

    <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('assets/plugins/settings/plans.js') }}"></script>

    <script>
        //New code for all modules setup
        var totalModule = '{{count($mainmodules)}}';
        // switches codes
        $(document).on('click', '.toggle-all-switches', function () {
            if ($(this).is(':checked')) {
                $('.switches').prop('checked', true);
            } else {
                $('.switches').prop('checked', false);
            }
        });
        $(document).on('click', '.switches', function () {
            var counter = 0;
            $.each($('.switches'), function (k, v) {
                if ($(this).is(":checked")) {
                    counter++;
                }
            });
            if (counter == totalModule) {
                $(".toggle-all-switches").prop('checked', true);
            } else {
                $(".toggle-all-switches").prop('checked', false);
            }
        });

        $(function () {
            $('#dob').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            });
            $('#doj').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
            });
            $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            CKEDITOR.replace('description');
        });

        $(document).on('change', '.btn-file :file', function () {
            var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [label]);
        });

        $('.btn-file :file').on('fileselect', function (event, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = label;
            if (input.length) {
                input.val(log);
            } else {
                if (log) alert(log);
            }
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#img-upload').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function () {
            readURL(this);
        });

        var totalModule = '{{count($mainmodules)}}';
        // switches codes
        $(document).on('click', '.toggle-all-switches', function () {
            if ($(this).is(':checked')) {
                $('.switches').prop('checked', true);
            } else {
                $('.switches').prop('checked', false);
            }
        });

        // $(document).on('click', '.switches', function () {
        //     if ($(this).hasClass('party') && !($(this).is(":checked"))) {
        //         $('.orders').prop('checked', false);
        //         $('.notes').prop('checked', false);
        //         $('.party_files').prop('checked',false);
        //         $('.party_images').prop('checked',false);
        //         $('.collections').prop('checked', false);
        //         $('.pdcs').prop('checked', false);
        //         $('.beat').prop('checked', false);
        //         $('.returns').prop('checked', false);
        //         $('.stock_report').prop('checked', false);
        //         $('.dso').prop('checked', false);
        //         $('.dsobyunit').prop('checked', false);
        //         $('.ordersreport').prop('checked', false);
        //         $('.psoreport').prop('checked', false);
        //         $('.spwise').prop('checked', false);
        //         $('.dpartyreport').prop('checked', false);
        //         $('.dempreport').prop('checked', false);
        //         $('.accounting').prop('checked', false);
        //         $('.product').prop('checked', false);
        //         $('.analytics').prop('checked', false);
        //         $('.zero_orders').prop('checked', false);
        //         $('.ageing').prop('checked', false);
        //         $('.targets').prop('checked',false);
        //         $('.targets_rep').prop('checked',false);
        //     }
        //     if ($(this).hasClass('livetracking') && !($(this).is(":checked"))) {
        //         $('.gpsreports').prop('checked', false);
        //     }
        //     if ($(this).hasClass('orders') && !($(this).is(":checked"))) {
        //         $('.analytics').prop('checked', false);
        //         $('.accounting').prop('checked', false);
        //         $('.zero_orders').prop('checked', false);
        //         $('.ageing').prop('checked', false);
        //         $('.dso').prop('checked', false);
        //         $('.dsobyunit').prop('checked', false);
        //         $('.ordersreport').prop('checked', false);
        //         $('.psoreport').prop('checked', false);
        //         $('.spwise').prop('checked', false);
        //         $('.dpartyreport').prop('checked', false);
        //         $('.dempreport').prop('checked', false);
        //     }
        //     if ($(this).hasClass('collections') && !($(this).is(":checked"))) {
        //         $('.analytics').prop('checked', false);
        //         $('.accounting').prop('checked', false);
        //         $('.dpartyreport').prop('checked', false);
        //         $('.dempreport').prop('checked', false);
        //         $('.ageing').prop('checked', false);
        //     }
        //     if ($(this).hasClass('product') && !($(this).is(":checked"))) {
        //         $('.analytics').prop('checked', false);
        //         $('.orders').prop('checked', false);
        //         $('.accounting').prop('checked', false);
        //         $('.zero_orders').prop('checked', false);
        //         $('.ageing').prop('checked', false);
        //         $('.dso').prop('checked', false);
        //         $('.dsobyunit').prop('checked', false);
        //         $('.ordersreport').prop('checked', false);
        //         $('.psoreport').prop('checked', false);
        //         $('.spwise').prop('checked', false);
        //         $('.dpartyreport').prop('checked', false);
        //         $('.dempreport').prop('checked', false);
        //     }
        //     if ($(this).hasClass('accounting') && !($(this).is(":checked"))) {
        //         $('.ageing').prop('checked', false);
        //     }
        //     if ($(this).hasClass('beat') && !($(this).is(":checked"))) {
        //         $('.analytics').prop('checked', false);
        //     }
        //     if ($(this).hasClass('leaves') && !($(this).is(":checked"))) {
        //         $('.analytics').prop('checked', false);
        //     }
        //     if($(this).hasClass('visit_module') && ($(this).is(":checked"))){
        //       $('.party').prop('checked',true);
        //     }
        //     if ($(this).hasClass('analytics') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //         $('.collections').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.beat').prop('checked', true);
        //         $('.leaves').prop('checked', true);
        //     }
        //     if(($(this).hasClass('party_files') || $(this).hasClass('party_images')) && ($(this).is(":checked"))){
        //       $('.party').prop('checked',true);
        //     }
        //     if ($(this).hasClass('accounting') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //         $('.collections').prop('checked', true);
        //     }
        //     if ($(this).hasClass('collections') && ($(this).is(":checked"))) {
        //         $('.product').prop('checked', true);
        //         $('.party').prop('checked', true);
        //     }
        //     if ($(this).hasClass('orders') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //     }
        //     if ($(this).hasClass('notes') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //     }
        //     if ($(this).hasClass('beat') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //     }
        //     if ($(this).hasClass('returns') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //     }
        //     if ($(this).hasClass('stock_report') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //     }
        //     if ($(this).hasClass('dso') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //     }
        //     if ($(this).hasClass('dsobyunit') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //     }
        //     if ($(this).hasClass('ordersreport') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //     }
        //     if ($(this).hasClass('psoreport') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //     }
        //     if ($(this).hasClass('spwise') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //     }
        //     if ($(this).hasClass('dpartyreport') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.collections').prop('checked', true);
        //     }
        //     if ($(this).hasClass('dempreport') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.collections').prop('checked', true);
        //     }
        //     if ($(this).hasClass('pdcs') && ($(this).is(":checked"))) {
        //         $('.collections').prop('checked', true);
        //         $('.party').prop('checked', true);
        //     }
        //     if ($(this).hasClass('gpsreports') && ($(this).is(":checked"))) {
        //         $('.livetracking').prop('checked', true);
        //     }
        //     if ($(this).hasClass('product') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //     }
        //     if ($(this).hasClass('zero_orders') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //         $('.product').prop('checked', true);
        //     }
        //     if ($(this).hasClass('ageing') && ($(this).is(":checked"))) {
        //         $('.party').prop('checked', true);
        //         $('.orders').prop('checked', true);
        //         $('.collections').prop('checked', true);
        //         $('.product').prop('checked', true);
        //         $('.accounting').prop('checked', true);
        //     }
        //      if($(this).hasClass('targets') && ($(this).is(":checked"))){
        //       $('.party').prop('checked',true);
        //     }
        //     if($(this).hasClass('targets_rep') && ($(this).is(":checked"))){
        //       $('.targets').prop('checked',true);
        //       $('.party').prop('checked',true);
        //     }

        //     var counter = 0;
        //     $.each($('.switches'), function (k, v) {
        //         if ($(this).is(":checked")) {
        //             counter++;
        //         }
        //     });
        //     if (counter == totalModule) {
        //         $(".toggle-all-switches").prop('checked', true);
        //     } else {
        //         $(".toggle-all-switches").prop('checked', false);
        //     }
        // });
    </script>
@endsection