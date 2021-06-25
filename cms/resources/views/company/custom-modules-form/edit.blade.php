@extends('layouts.company')
@section('title', $custom_fields->name)

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

        input[type="checkbox"]
        {
            vertical-align:middle;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

@section('content')
    <section class="content">
        @if (\Session()->has('message'))
            <div class="alert alert-success">
                <p>{{ \Session::get('message') }}</p>
            </div><br/>
        @endif
    @if(isset($custom_fields))
            <div class="col-xs-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{$custom_fields->name}}</h3>
                        <div class="box-tools pull-right">
                            <div class="col-xs-7 page-action text-right">
                                <a href="{{ domain_route('company.admin.custom.modules.form.index',['id' => $custom_fields->id]) }}" class="btn btn-default btn-sm"> <i
                                            class="fa fa-arrow-left"></i> Back</a>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <form action="{{route('company.admin.custom.modules.form.update',['domain' => request('subdomain'),'id'=>$custom_fields->id,'data_id' => $form_data->id])}}"
                              method="post" enctype="multipart/form-data" id="formId">
                            @csrf
                            @method('patch')
                            @foreach($custom_fields->customFields as $key => $field)
                                @includeIf('company.custom-fields.__'.( \Illuminate\Support\Str::slug(strtolower($field->type))),['field' => $field,'key' => $key])
                            @endforeach

                            <button class="btn btn-primary pull-right keySubmit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@section('scripts')
    <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    {{-- These config value are passed as js variable
    to dynamic-field.js as config value in js
    cannot be accessed directly --}}
    <script>

        var fields = [];
        @foreach($custom_fields->customFields as $key => $field)
        @if ($field->type == 'Check Box' && $field->required == 1)

        fields.push("{{$field->slug}}")
        @endif
        @endforeach




        if (fields.length > 0){

            for (let i = 0; i < fields.length; i++) {
                $('.' + fields[i]).on('click', function (){

                    if($('.' + fields[i]).filter(':checked').length === 0){

                        $('#'+fields[i]).show()
                        $('#'+fields[i]).text('Please select at least one value')

                    }else{
                        $('#'+fields[i]).hide()
                    }

                })
            }

            $('#formId').on('submit', function (e) {
                e.preventDefault();
                var isTrue = fields.every(checkValidation)

                if (isTrue) this.submit();
            });

            function checkValidation(element, index, array) {
                if($('.' + element).filter(':checked').length > 0){
                    $('#'+element).hide()
                    return true;

                }
                $('#'+element).show()
                $('#'+element).text('Please select at least one value')
                document.getElementById(element).scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
        }


        $('#keySubmit').on('submit', function (e) {
            $('#keySubmit').attr('disabled', true);
        });

        let latitude = {{config('settings.latitude')}};
        let longitude = {{config('settings.longitude')}};

        $( ".form-group .multiimg" ).each(function( index ) {
            var imgdivid = $( this ).attr('id');
            var Imgcount = $("#"+imgdivid+" .imgUp").length;
            if(Imgcount >= 3){
                $("#"+ imgdivid +" .imgAdd").hide();
            }
        });

        function validatePhoneNumber(phone, el) {
            let field = el.attr("id")
            let formData = {"phone": phone}
            $.ajax({
                "url": "{{domain_route('company.admin.custom.modules.form.validatePhone')}}",
                "dataType": "json",
                "type": "POST",
                "data": {
                    _token: "{{csrf_token()}}",
                    ...formData,
                    field_name: field
                },
                beforeSend: function (url, data) {
                    $(el).parent().find('.has-error').html('');
                    $('.keySubmit').attr('disabled', true);
                    el.parent().removeClass('has-error');
                },
                success: function (res) {
                    $(el).parent().find('.has-error').html('');
                    $('.keySubmit').attr('disabled', false);
                    el.parent().removeClass('has-error');
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let msg =xhr.responseJSON.errors.phone[0];
                        $(el).parent().find('.has-error').html(msg);
                        el.parent().addClass('has-error');
                    }
                    $('.keySubmit').attr('disabled', true);
                },
            });
        }

        $(document).on("keyup", '.phone_numbers', function (e) {
            let current = $(this);
            let phone = current.val();
                validatePhoneNumber(phone, current);
        });

        $(function () {
            $('.two-digits').keyup(function () {
                if ($(this).val().indexOf('.') !== -1) {
                    if ($(this).val().split(".")[1].length > 2) {
                        if (isNaN(parseFloat(this.value))) return;
                        this.value = parseFloat(this.value).toFixed(2);
                    }
                }
                return this; //for chaining
            });
        });
        $('.two-digits').on('change', function () {
            $(this).val(parseFloat($(this).val()).toString());
        });

    </script>

    <script src="{{asset('assets/plugins/custom-fields/dynamic-fields.js')}}"></script>


@endsection

