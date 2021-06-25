@extends('layouts.company')
@section('title', 'Scheme Edit')

@section('stylesheets')
    <link rel="stylesheet"
          href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
    <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}"/>

    @if(config('settings.ncal')==1)
        <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
    @else
        <link rel="stylesheet"
              href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    @endif
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

    </style>
@endsection

@section('content')
    @if(count($errors))
        <div class="form-group">
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">{{$error}}</div>
            @endforeach
        </div>
    @endif

    <section class="content">
        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Scheme</h3>
                    <div class="box-tools pull-right">
                        <div class="col-xs-7 page-action text-right">
                            <a href="{{  domain_route('company.admin.scheme')  }}" class="btn btn-default btn-sm"> <i
                                        class="fa fa-arrow-left"></i> Back</a>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <form action="{{route('company.admin.scheme.update',['domain' => request('subdomain'),'id' => $scheme->id])}}"
                          method="post" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" class="form-control" name="name"
                                   maxlength="255" placeholder="Enter Name of scheme" required
                                   value="{{$scheme->name}}" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="description">
                                Description
                            </label>
                            <textarea type="text" class="form-control" name="description" id="description"
                                      placeholder="Enter Description">{{$scheme->description}}</textarea>
                        </div>

                        {{--                        <div class="form-group ">--}}
                        {{--                            <label for="validity-date">--}}
                        {{--                                Validity Dates--}}
                        {{--                            </label>--}}

                        {{--                            <input type="text" class="form-control" id="validity-date" name="validity_date"--}}
                        {{--                                   placeholder="Enter Validity Date Range" required--}}
                        {{--                                   value="{{$scheme->start_date. 'to' . $scheme->end_date}}"--}}
                        {{--                            >--}}
                        {{--                        </div>--}}

                        <div class="form-group">
                            <label for="validity-date">
                                Validity Dates
                                <span>*</span>
                            </label>


                            @if(config('settings.ncal')==0)
                                <div id="reportrange" name="reportrange" class="hidden reportrange form-control"
                                     style="margin-top: 10px; width: 100%">
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
                                           style="padding: 0 0 0 2px;"/>
                                    <input id="start_edate" type="text" name="start_edate"
                                           placeholder="Start Date" hidden/>
                                    <span class="input-group-addon" aria-readonly="true"><i
                                                class="glyphicon glyphicon-calendar"></i></span>
                                    <input id="end_ndate" class="form-control" type="text"
                                           name="end_ndate" placeholder="End Date"
                                           autocomplete="off" style="padding: 0 0 0 2px;"/>
                                    <input id="end_edate" type="text" name="end_edate"
                                           placeholder="End Date" hidden/>
                                    <button id="filterTable" style="color:#0b7676!important;" hidden><i
                                                class="fa fa-filter"
                                                aria-hidden="true"></i></button>
                                </div>
                            @endif
                        </div>

                        {{--                        <div class="form-group @if ($errors->has('image')) has-error @endif">--}}
                        {{--                            <label>Photo</label>--}}
                        {{--                            <small> Size of image should not be more than 2MB.</small>--}}
                        {{--                            <div class="input-group" style="margin-bottom:10px;">--}}
                        {{--                                <span class="input-group-btn">--}}
                        {{--                                  <span class="btn btn-default btn-file imagefile">--}}
                        {{--                                    Browse… {!! Form::file('image', ['id'=>'imgInp']) !!}--}}
                        {{--                                  </span>--}}
                        {{--                                </span>--}}
                        {{--                                <input type="text" class="form-control" readonly>--}}
                        {{--                            </div>--}}

                        {{--                            <img id='img-upload' class="img-responsive"--}}
                        {{--                                 @if(isset($scheme->image))--}}
                        {{--                                 src=" {{ URL::asset('cms//storage/app/public/uploads/party/schema/'.$scheme->image) }} "--}}
                        {{--                                 style="height: 100px; width: 100px--}}
                        {{--                                 @endif"/>--}}
                        {{--                        </div>--}}

                        <div class="form-group">
                            <label for="image">Image <small style="color: grey"> &nbsp; &nbsp; Size of image should not
                                    be more than 2MB.</small></label>

                            <div class="input-group input-file" name="image">
                              <span class="input-group-btn">
                                <button class="btn btn-default btn-choose" type="button">Choose</button>
                              </span>
                                <input type="text" name="image" id="image" class="form-control fileupload"
                                       placeholder="Choose a file..." value="{{$scheme->image}}"/>

                                <span class="input-group-btn">
                                 <button class="btn btn-danger btn-reset" type="button">Remove</button>
                                </span>
                            </div>
                            <img id='img-upload' class="img-responsive"
                                 @if(isset($scheme->image))
                                 src=" {{ URL::asset('cms//storage/app/public/uploads/party/schema/'.$scheme->image) }} "
                                 style="height: 100px; width: 100px
                                 @endif"/>

                        </div>

                        @if(!$is_scheme_used)
                            <div class="form-group">
                                <label>Select Parties <span> *</span> </label>
                                <select name="party[]" id="employeeId-enableClickableOptGroups" multiple="multiple"
                                        class="form-control">
                                    @if(isset($clients))
                                        @foreach($clients as $key => $client)
                                            <option value="{{ $client['id'] }}"
                                                    @if(!is_null($scheme->parties))@if(in_array($client['id'],json_decode($scheme->parties))) selected @endif @endif
                                            >{{ $client['company_name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @endif

                        <input hidden name="is_scheme_used" value="{{$is_scheme_used}}"/>

                        @if(!$is_scheme_used)
                            <div class="form-group">
                                <label for="Choose Schema Types">Scheme Types <span> *</span></label>
                                <select name="scheme_type_id" required class="form-control schema_option"
                                        id="scheme_option">
                                    <option value="">Select Option</option>
                                    @foreach($scheme_types as $type)
                                        <option value="{{$type->id}}"
                                                @if($scheme->scheme_type_id == $type->id) selected
                                                @endif   @if(getClientSetting()->order_with_amt == 1 && $type->id != 1) disabled @endif >{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="main_section" hidden>
                                {{-- choosing-product-option --}}
                                <div class="row ">
                                    <div class="form-group col-md-9" id="custom-select2">
                                        <label for="product">Products<span>*</span></label>
                                        <select name="product[]" id="product" class="form-control select2 product"
                                                multiple
                                                required>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3 unit" hidden>
                                        <label for="unit">Quantity <span>*</span></label>
                                        <input type="number" name="qty" id="no_of_unit" class="form-control no-of-unit"
                                               min="1">
                                    </div>

                                    <div class="form-group col-md-3 amount" hidden>
                                        <label for="amount">Amount <span>*</span></label>
                                        <input type="number" id="min_amount" name="amount"
                                               class="form-control total-amount"
                                               min="1">
                                    </div>
                                </div>
                                {{-- This section contains form fields to create rules--}}
                                <div class="free_product_section" hidden>
                                    <h3>Rule Section</h3>
                                    <div class="row">
                                        <div class="form-group col-md-3 free_product" hidden>
                                            <label for="product">Offered Product <span
                                                >*</span></label>
                                            <select name="offered_product" id="product_free"
                                                    class="form-control select2 product_free">
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3 free_unit" hidden>
                                            <label for="unit">Offered Quantity <span>*</span></label>
                                            <input type="number" name="offered_qty" id="no_of_free_unit"
                                                   class="form-control no-of-unit"
                                                   min="1">
                                        </div>

                                        <div class="form-group col-md-3 discount_amount" hidden>
                                            <label for="discount_amount">Discount Amount <span
                                                >*</span></label>
                                            <input type="number" name="discount_amount" class="form-control" min="1"
                                                   id="amount_discount">
                                        </div>

                                        <div class="form-group col-md-3 percentage_off" hidden>
                                            <label for="percentage_off">Percentage off <span
                                                >*</span></label>
                                            <input type="number" name="percentage_off" class="form-control" step="0.01"
                                                   min="0" max="100" id="discount_percentage">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status">
                                <option value="1" @if($scheme->status == 'Active') selected @endif>Active</option>
                                <option value="0" @if($scheme->status == 'Inactive') selected @endif>Inactive</option>
                            </select>
                        </div>

                        <button class="btn btn-primary pull-right keySubmit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
    <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>

    @if(config('settings.ncal')==1)
        <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
    @else
        <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    @endif

    <script>

        @if(config('settings.ncal')==0)

        let min_date = "{{$scheme->min_date}}"; // this is achived using acessor
        var start = "{{\Carbon\Carbon::parse($scheme->start_date)->format('M d,Y')}}";
        var end = "{{\Carbon\Carbon::parse($scheme->end_date)->format('M d,Y')}}";

        function cb(start, end) {
            $('#reportrange span').html(moment(start).format('MMM D, YYYY') + ' to ' + moment(end).format('MMM D, YYYY'));
            $('#startdate').val(moment(start).format('MMMM D, YYYY'));
            $('#enddate').val(moment(end).format('MMMM D, YYYY'));
            $('#start_edate').val(moment(start).format('Y-MM-DD'));
            $('#end_edate').val(moment(end).format('Y-MM-DD'));
        }


        $('#reportrange').daterangepicker({
            minDate: min_date,
            startDate: start,
            endDate: end,
            locale: {
                format: 'MMMM D, YYYY',
                separator: " to "
            }

        }, cb);

        cb(start, end);

        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $('#start_edate').val(start);
            $('#end_edate').val(end);
        });

        $('#reportrange').removeClass('hidden');

        @else

        var start = "{{\Carbon\Carbon::parse($scheme->start_date)->format('Y-m-d')}}";
        var end = "{{\Carbon\Carbon::parse($scheme->end_date)->format('Y-m-d')}}";

        var lastmonthdate = AD2BS(start);
        var ntoday = AD2BS(end);
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

            }
        });

        $('#end_ndate').nepaliDatePicker({
            onChange: function () {
                $('#end_edate').val(BS2AD($('#end_ndate').val()));
                if ($('#end_ndate').val() < $('#start_ndate').val()) {
                    $('#start_ndate').val($('#end_ndate').val());
                    $('#start_edate').val(BS2AD($('#end_ndate').val()));
                }

                var start = $('#start_edate').val();
                var end = $('#end_edate').val();
                if (start == "") {
                    start = end;
                }

            }
        });
        @endif

        let product_data = @json($products);
        let offered_product = @json($offered_product);
        var schema_option = {{$scheme->scheme_type_id}};

        $(document).ready(function () {
            if (schema_option === '') {
                $(".main_section").hide()
            } else {
                $(".first_option").show()
                $(".main_section").show()
            }
            switch (schema_option) {
                case 1:
                    $('.free_product_section').show();
                    $('.free_product').show();
                    $('.free_unit').show();
                    $('.discount_amount').hide();
                    $('.percentage_off').hide();
                    $('.unit').show()
                    $('.amount').hide()
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', true);
                    $('#min_amount').prop('required', false);
                    $('#product_free').prop('required', true)
                    $('#no_of_free_unit').prop('required', true)
                    $('#discount_percentage').prop('required', false)
                    $('#discount_percentage').val("");
                    $('#amount_discount').prop('required', false)
                    $('#amount_discount').val("");
                    for (const [key, value] of Object.entries(product_data)) {
                        $('.product').append(`<option value=${key} selected="selected">${value}</option>`)
                    }
                    $('.product_free').append(`<option value=${Object.keys(offered_product)[0]}>${Object.values(offered_product)[0]}</option>`)
                    $('#no_of_unit').val({{$scheme->qty}});
                    $('#no_of_free_unit').val({{$scheme->offered_qty}})
                    break;
                case 2:
                    $('.free_product_section').show();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').hide();
                    $('.percentage_off').show();
                    $('.unit').show()
                    $('.amount').hide()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', true)
                    $('#min_amount').prop('required', false);
                    $(".product").val("").trigger('change')
                    $('#discount_percentage').prop('required', true)

                    $('#amount_discount').prop('required', false)
                    $('#amount_discount').val("");
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)

                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', false)

                    for (const [key, value] of Object.entries(product_data)) {
                        $('.product').append(`<option value=${key} selected="selected">${value}</option>`)
                    }
                    $('#no_of_unit').val({{$scheme->qty}});
                    $('#discount_percentage').val({{$scheme->percentage_off}});
                    break;
                case 3:
                    $('.free_product_section').show();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').show();
                    $('.percentage_off').hide();
                    $('.unit').show()
                    $('.amount').hide()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', true)
                    $('#min_amount').prop('required', false);
                    $(".product").val("").trigger('change')
                    $('#discount_percentage').prop('required', false)
                    $('#discount_percentage').val("");

                    $('#amount_discount').prop('required', true)
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)
                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', false)
                    for (const [key, value] of Object.entries(product_data)) {
                        $('.product').append(`<option value=${key} selected="selected">${value}</option>`)
                    }
                    $('#no_of_unit').val({{$scheme->qty}});
                    $('#amount_discount').val({{$scheme->discount_amount}});
                    break;
                case 4:
                    $('.free_product_section').show();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').hide();
                    $('.percentage_off').show();
                    $('.unit').hide()
                    $('.amount').show()
                    $('#no_of_unit').val('')

                    $('#no_of_unit').prop('required', false)
                    $('#min_amount').prop('required', true);
                    $(".product").val("").trigger('change')
                    $(".product_free").val("").trigger('change')
                    $('#discount_percentage').val("");
                    $('#discount_percentage').prop('required', true)
                    $('#amount_discount').prop('required', false)
                    $('#amount_discount').val("");
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)
                    $('#product_free').prop('required', false)
                    for (const [key, value] of Object.entries(product_data)) {
                        $('.product').append(`<option value=${key} selected="selected">${value}</option>`)
                    }
                    $('#min_amount').val({{$scheme->amount}})
                    $('#discount_percentage').val({{$scheme->percentage_off}});
                    break;
                case 5:
                    $('.free_product_section').show();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').show();
                    $('.percentage_off').hide();
                    $('.unit').hide()
                    $('.amount').show()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', false)
                    $('#min_amount').prop('required', true);
                    $(".product").val("").trigger('change');
                    $('#discount_percentage').prop('required', false)
                    $('#discount_percentage').val("");
                    $('#amount_discount').val("");
                    $('#amount_discount').prop('required', true)
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)
                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', false)

                    for (const [key, value] of Object.entries(product_data)) {
                        $('.product').append(`<option value=${key} selected="selected">${value}</option>`)
                    }
                    $('#min_amount').val({{$scheme->amount}})
                    $('#amount_discount').val({{$scheme->discount_amount}});
                    break;
                case 6:
                    $('.free_product_section').show();
                    $('.free_product').show();
                    $('.free_unit').show();
                    $('.discount_amount').hide();
                    $('.percentage_off').hide();
                    $('.unit').hide()
                    $('.amount').show()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', false)
                    $('#min_amount').prop('required', true);
                    $(".product").val("").trigger('change');
                    $('#discount_percentage').prop('required', false);
                    $('#discount_percentage').prop('required', false);
                    $('#discount_percentage').val("");
                    $('#amount_discount').val("");
                    $('#amount_discount').prop('required', false)
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', true)
                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', true)
                    for (const [key, value] of Object.entries(product_data)) {
                        $('.product').append(`<option value=${key} selected="selected">${value}</option>`)
                    }
                    $('#min_amount').val({{$scheme->amount}})
                    $('.product_free').append(`<option value=${Object.keys(offered_product)[0]}>${Object.values(offered_product)[0]}</option>`)
                    $('#no_of_free_unit').val({{$scheme->offered_qty}})
                    break;
                default:
                    $('.free_product_section').hide();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').hide();
                    $('.percentage_off').hide();
                    $('.unit').hide()
                    $('.amount').show()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', false)
                    $('#min_amount').prop('required', false);
                    $(".product").val("").trigger('change');
                    $('#discount_percentage').prop('required', false)
                    $('#discount_percentage').val("");
                    $('#amount_discount').val("");
                    $('#amount_discount').prop('required', false)
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)
                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', false)
                    break;

            }
        });


        $('.product').select2({
            placeholder: 'Search Product',
            allowHtml: true,
            allowClear: true,
            dropdownAutoWidth: true,
            closeOnSelect: false,
            minimumInputLength: 1,
            width: 'auto',
            language: {
                inputTooShort: function () {
                    return 'Please type';
                }
            },
            ajax: {
                type: "post",
                url: "{{domain_route('company.admin.filter.products.search') }}",
                dataType: 'json',
                data: function (params) {

                    if (params.term.trim().length < 2) {
                        throw false;
                    }
                    return {
                        q: params.term.trim(),
                        scheme_id: {{$scheme->id}},
                        'party': $('#employeeId-enableClickableOptGroups').val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.product_name,
                                id: item.id,
                                disabled: item.disabled
                            }
                        })
                    };
                },
            },

        })

        $(".product_free").select2({
            placeholder: 'Select Free Product',
            minimumInputLength: 1,
            width: 'auto',
            language: {
                inputTooShort: function () {
                    return 'Please type';
                }
            },
            ajax: {
                type: "post",
                url: "{{domain_route('company.admin.filter.offered.products') }}",
                dataType: 'json',
                data: function (params) {
                    if (params.term.trim().length < 2) {
                        throw false;
                    }
                    return {
                        q: params.term.trim(),
                    };
                },

                processResults: function (data) {
                    return {
                        results: $.map(data, function (item, key) {
                            return {
                                text: item,
                                id: key,
                            }
                        })
                    };
                },
            }

        })

        //main schemes selection and its event
        //emptying all input field if another one is selected is must
        $(document).on("change", ".schema_option", function () {
            schema_option = $(this).val();

            if (schema_option === '') {
                $(".main_section").hide()
            } else {
                $(".first_option").show()
                $(".main_section").show()
            }
            switch (schema_option) {
                case '1':
                    $('.free_product_section').show();
                    $('.free_product').show();
                    $('.free_unit').show();
                    $('.discount_amount').hide();
                    $('.percentage_off').hide();
                    $('.unit').show()
                    $('.amount').hide()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', true);
                    $('#min_amount').prop('required', false);
                    $(".product").val("").trigger('change');
                    $('#product_free').prop('required', true)
                    $('.product_free').val("").trigger('change');
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', true)
                    $('#discount_percentage').prop('required', false)
                    $('#discount_percentage').val("");
                    $('#amount_discount').prop('required', false)
                    $('#amount_discount').val("");
                    break;
                case '2':
                    $('.free_product_section').show();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').hide();
                    $('.percentage_off').show();
                    $('.unit').show()
                    $('.amount').hide()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', true)
                    $('#min_amount').prop('required', false);
                    $(".product").val("").trigger('change')
                    $('#discount_percentage').prop('required', true)
                    $('#discount_percentage').val("");
                    $('#amount_discount').prop('required', false)
                    $('#amount_discount').val("");
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)

                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', false)
                    break;
                case '3':
                    $('.free_product_section').show();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').show();
                    $('.percentage_off').hide();
                    $('.unit').show()
                    $('.amount').hide()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', true)
                    $('#min_amount').prop('required', false);
                    $(".product").val("").trigger('change')
                    $('#discount_percentage').prop('required', false)
                    $('#discount_percentage').val("");
                    $('#amount_discount').val("");
                    $('#amount_discount').prop('required', true)
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)
                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', false)
                    break;
                case '4':
                    $('.free_product_section').show();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').hide();
                    $('.percentage_off').show();
                    $('.unit').hide()
                    $('.amount').show()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', false)
                    $('#min_amount').prop('required', true);
                    $(".product").val("").trigger('change')
                    $(".product_free").val("").trigger('change')
                    $('#discount_percentage').val("");
                    $('#discount_percentage').prop('required', true)
                    $('#amount_discount').prop('required', false)
                    $('#amount_discount').val("");
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)
                    $('#product_free').prop('required', false)
                    break;
                case '5':
                    $('.free_product_section').show();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').show();
                    $('.percentage_off').hide();
                    $('.unit').hide()
                    $('.amount').show()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', false)
                    $('#min_amount').prop('required', true);
                    $(".product").val("").trigger('change');
                    $('#discount_percentage').prop('required', false)
                    $('#discount_percentage').val("");
                    $('#amount_discount').val("");
                    $('#amount_discount').prop('required', true)
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)
                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', false)
                    break;
                case '6':
                    $('.free_product_section').show();
                    $('.free_product').show();
                    $('.free_unit').show();
                    $('.discount_amount').hide();
                    $('.percentage_off').hide();
                    $('.unit').hide()
                    $('.amount').show()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', false)
                    $('#min_amount').prop('required', true);
                    $(".product").val("").trigger('change');
                    $('#discount_percentage').prop('required', false);
                    $('#discount_percentage').prop('required', false);
                    $('#discount_percentage').val("");
                    $('#amount_discount').val("");
                    $('#amount_discount').prop('required', false)
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', true)
                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', true)
                    break;
                default:
                    $('.free_product_section').hide();
                    $('.free_product').hide();
                    $('.free_unit').hide();
                    $('.discount_amount').hide();
                    $('.percentage_off').hide();
                    $('.unit').hide()
                    $('.amount').show()
                    $('#no_of_unit').val('')
                    $('#min_amount').val('')
                    $('#no_of_unit').prop('required', false)
                    $('#min_amount').prop('required', false);
                    $(".product").val("").trigger('change');
                    $('#discount_percentage').prop('required', false)
                    $('#discount_percentage').val("");
                    $('#amount_discount').val("");
                    $('#amount_discount').prop('required', false)
                    $('#no_of_free_unit').val('')
                    $('#no_of_free_unit').prop('required', false)
                    $('.product_free').val("").trigger('change');
                    $('#product_free').prop('required', false)
                    break;

            }
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
                    $('#img-upload').css('height', '100px');
                    $('#img-upload').css('width', '100px');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function () {
            readURL(this);
        });

        {{--let min_date = "{{$scheme->min_date}}"; // this is achived using acessor--}}
        {{--let start_date = "{{\Carbon\Carbon::parse($scheme->start_date)->format('M d Y')}}";--}}
        {{--let end_date = "{{\Carbon\Carbon::parse($scheme->end_date)->format('M d Y')}}";--}}

        {{--$('#validity-date').daterangepicker({--}}
        {{--    startDate: start_date,--}}
        {{--    endDate: end_date,--}}
        {{--    minDate: min_date,--}}
        {{--    locale: {--}}
        {{--        format: 'MMMM D, YYYY',--}}
        {{--        separator: " to "--}}
        {{--    }--}}
        {{--});--}}

        $('#employeeId-enableClickableOptGroups').multiselect({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            enableFullValueFiltering: true,
            enableClickableOptGroups: true,
            includeSelectAllOption: true,
            enableCollapsibleOptGroups: true,
            selectAllNumber: false,
            nonSelectedText: "Select Parties",
            disableIfEmpty: true,
        });


        $("#employeeId-enableClickableOptGroups").change(function () {
            // $("#product").val('').trigger('change')

            if ($(this).val().length > 0) {
                $.ajax({
                    type: 'POST',
                    url: "{{ domain_route('company.admin.check.party.schemes') }}",
                    data: {
                        scheme_id: {{$scheme->id}},
                        'party': $(this).val(),
                        'products' : $("#product").val()
                    },
                    success: function (data) {
                        if (data.status === 201) {
                            $("#product").val('').trigger('change')

                            // for (const [key, value] of Object.entries(data.sameVariants)) {
                            //     console.log(value + '-' + key);
                            //     let id = value + '-' + key;
                            //     $("#product option[value=' " + id + " ']').remove();
                            // }
                        }
                    }
                });
            }


        });


        function bs_input_file() {
            $(".input-file").before(
                function () {
                    if (!$(this).prev().hasClass('input-ghost')) {
                        var element = $("<input type='file' class='input-ghost fileupload' style='visibility:hidden; height:0'>");
                        element.attr("name", $(this).attr("name"));
                        element.change(function () {
                            var filePath = this.files[0].name;
                            var allowedExtensions = /(\.png|\.jpe?g)$/i;
                            if (!allowedExtensions.exec(filePath)) {
                                alert('Please upload file having extensions.png, .jpg, .jpeg only.');
                                fileInput.value = '';
                                return false;
                            }

                            if (this.files[0].size / 1024 / 1024 > 2) {
                                alert('File Size cannot be more than 2MB');
                                $(this).child(".input-file").find('input').val('');
                                return;
                            }
                            element.next(element).find('input').val((element.val()).split('\\').pop());
                            readURL(this);
                            $('#img-upload').show();
                        });
                        $(this).find("button.btn-choose").click(function () {
                            element.click();

                        });
                        $(this).find("button.btn-reset").click(function () {
                            element.val(null);
                            $(this).parents(".input-file").find('input').val('');
                            $('#img-upload').removeAttr('src');
                            $('#img-upload').removeAttr('style');
                            $('#img-upload').css('display', 'none')

                        });
                        $(this).find('input').css("cursor", "pointer");
                        $(this).find('input').mousedown(function () {
                            $(this).parents('.input-file').prev().click();
                            return false;
                        });
                        return element;
                    }
                }
            );
        }

        $(function () {
            bs_input_file();
        });


    </script>
@endsection
