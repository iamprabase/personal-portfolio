@extends('layouts.company')
@section('title', 'Schemes')

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


        input[type="checkbox"] {
            vertical-align: middle;
        }

        img {
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
            /* background-color: rgb(0,0,0); /* Fallback color */
            /* background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            /* -webkit-animation-name: zoom;
             -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;*/
        }

        @-webkit-keyframes zoom {
            from {
                -webkit-transform: scale(0)
            }
            to {
                -webkit-transform: scale(1)
            }
        }

        @keyframes zoom {
            from {
                transform: scale(0)
            }
            to {
                transform: scale(1)
            }
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
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

        @media only screen and (max-width: 700px) {
            .modal-content {
                width: 100%;
            }
        }

        .delete, .edit {
            font-size: 15px !important;
        }

        .fa-edit, .fa-trash-o {
            padding-left: 5px;
        }

        .btn-warning {
            margin-right: 2px !important;
            color: #fff !important;
            background-color: #ec971f !important;
            border-color: #d58512 !important;
        }

        .dropdown-menu > .disabled > a, .dropdown-menu > .disabled > a:focus, .dropdown-menu > .disabled > a:hover {
            color: white !important;
        }

        .dropdown-menu > .disabled > a, .dropdown-menu > .disabled > a:focus, .dropdown-menu > .disabled > a:hover {
            color: #0f0e0e !important;
        }

        .dropdown-menu > .active > a, .dropdown-menu > .active > a:focus, .dropdown-menu > .active > a:hover {
            color: #fff;
            text-decoration: none;
            background-color: white !important;
            outline: 0;
        }

    </style>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

@section('content')
    <section class="content">

        <div class="col-xs-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i
                                class="fa fa-arrow-left"></i> Back</a>
                    <div class="page-action pull-right">
                        {!!$action!!}
                    </div>
                </div>
                <div class="box-header with-border">
                    <h3 class="box-title">{{$scheme->name}}</h3>

                </div>


                <div class="box-body">
                    <table class="table table-responsive table-striped">
                        <tr>
                            <th scope="row">Scheme Type</th>
                            <td>{{$scheme->schemeTypes->name}}</td>
                        </tr>

                        <tr>
                            <th scope="row">Description
                            </td>
                            <td>{{$scheme->description}}</td>
                        </tr>

                        <tr>
                            <th scope="row">Validity
                            </td>
                            <td>

                                {{getDeltaDate(date('Y-m-d', strtotime($scheme->start_date)))}}
                                to {{getDeltaDate(date('Y-m-d', strtotime($scheme->end_date)))}}</td>
                        </tr>

                        <tr>
                            <th scope="row">Image
                            </td>
                            <td>
                                @if(isset($scheme->image))
                                    <img src="{{URL::asset('cms//storage/app/public/uploads/party/schema/'.$scheme->image)}}"
                                         alt="" style="height: 100px; width: 100px">
                                @else
                                    {{'Image Not uploaded'}}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Status
                            </td>
                            <td>{{$scheme->status}}</td>
                        </tr>

                        <tr>
                            <th scope="row">Schemes on Products with variants
                            </td>
                            <td>
                                @foreach($productNames as $name)
                                    {{$name}}
                                    @if(!$loop->last)
                                        {{','}}
                                    @endif
                                @endforeach
                            </td>
                        </tr>

                        @if(isset($scheme->qty))
                            <tr>
                                <th scope="row">Minimum order Quantity
                                </td>
                                <td>{{$scheme->qty}}</td>
                            </tr>
                        @else
                            <tr>
                                <th scope="row">Minimum order Amount
                                </td>
                                <td>{{$scheme->amount}}</td>
                            </tr>
                        @endif
                        @if(isset($scheme->offered_product))
                            <tr>
                                <th scope="row">Offered Product
                                </td>
                                <td>{{\App\Product::withTrashed()->find($scheme->offered_product)->product_name}} </td>
                            </tr>
                            @if(isset($scheme->offered_product_variant))
                                <tr>
                                    <th scope="row">Offered Variant
                                    </td>
                                    <td>{{\App\ProductVariant::withTrashed()->find($scheme->offered_product_variant)->variant }}</td>
                                </tr>
                            @endif
                            @if(isset($scheme->offered_qty))
                                <tr>
                                    <th scope="row">Offered Quantity
                                    </td>
                                    <td>{{$scheme->offered_qty}}</td>
                                </tr>
                            @endif

                        @endif

                        @if(isset($scheme->discount_amount))
                            <tr>
                                <th scope="row">Discount Amount
                                </td>
                                <td>{{$scheme->discount_amount}}</td>
                            </tr>
                        @endif

                        @if(isset($scheme->percentage_off))
                            <tr>
                                <th scope="row">Discount Percentage
                                </td>
                                <td>{{$scheme->percentage_off}}</td>
                            </tr>
                        @endif
                    </table>


                    <div class="row" style="margin-top: 5px">
                        <div class="col-md-4" style="margin-top: 9px">
                            <strong style="margin-left: 9px">Selected
                                Parties
                                @if(count($clients) != count($selected_parties))
                                   ({{count($selected_parties)}} of {{count($clients)}})
                                @endif</strong>
                        </div>
                        <div class="col-md-8">
                            <select name="party[]" id="employeeId-enableClickableOptGroups" multiple="multiple"
                                    class="form-control">
                                @if(isset($clients))
                                    @foreach($clients as $key => $client)
                                        @if($selected_parties->contains($client['id']))
                                            <option value="{{ $client['id'] }}" selected disabled
                                            >{{ $client['company_name'] }}</option>
                                        @else
                                            <option value="{{ $client['id'] }}" disabled
                                            >{{ $client['company_name'] }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal-default fade" id="delete" tabindex="-1" plan="dialog" aria-labelledby="myModalLabel"
         data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" plan="document">
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
                        <input type="hidden" name="plan_id" id="m_id" value="">
                        <input type="hidden" name="prev_url" value="{{URL::previous()}}">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success cancel" data-dismiss="modal">No, Cancel</button>
                        <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
    <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
    <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
    <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}"/>

    <script>

        $('#employeeId-enableClickableOptGroups').multiselect({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            enableFullValueFiltering: true,
            enableClickableOptGroups: true,
            enableCollapsibleOptGroups: true,
            selectAllNumber: true,
            nonSelectedText: "None selected",
            disableIfEmpty: true,
        });

        // Get the modal
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("img01");

        $('.display-imglists').on('click', function () {
            modal.style.display = "block";
            modalImg.src = this.src;
        });

        $('.close').on('click', function () {
            modal.style.display = "none";
        });

        $('#delete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var mid = button.data('mid')
            var url = button.data('url');
            $(".remove-record-model").attr("action", url);
            var modal = $(this)
            modal.find('.modal-body #m_id').val(mid);
        });

    </script>

@endsection

