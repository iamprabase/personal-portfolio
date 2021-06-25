<!-- Main Modal -->
<div class="modal fade" id="customFieldModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true"
     data-url="{{domain_route('company.admin.custom.modules.form.store')}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">
                <div class="row">
                    <div class="col-xs-10">
                        <h4 class="modal-title" id="myModalLabel">Add a field form</h4>
                    </div>
                    <div class="col-xs-2">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <div class="row">
                    {{-- Text --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin1"
                             data-title="Text field is used to store texts up to 255 characters.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/1.png')}}" alt="">
                            </div>
                            <h5 style="position: absolute; left: 35px;" >Text</h5>
                        </div>
                    </div>
                    {{-- TextArea/LargeText --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin3"
                             data-title="Large text field is used to store texts longer than usual.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/3.png')}}" alt="">
                            </div>
                            <h5>Large text</h5>
                        </div>
                    </div>
                    {{-- Numerical --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin4"
                             data-title="Numeric field is used to store data such as amount of commission or other custom numerical data ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/4.png')}}" alt="">
                            </div>
                            <h5>Numerical</h5>
                        </div>
                    </div>
                    {{-- Monetary --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin5"
                             data-title="Monetary field is used to store data such as amount of commission ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/5.png')}}" alt="">
                            </div>
                            <h5>Monetary</h5>
                        </div>
                    </div>
                    {{-- Multiple option --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin6"
                             data-title="Multiple options field lets you predefine a list of values to choose from.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                            </div>
                            <h5>Multiple option</h5>
                        </div>
                    </div>
                    {{-- Single option --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin7"
                             data-title="Single option field lets you predefine a list of values out of which one can be selected. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/7.png')}}" alt="">
                            </div>
                            <h5>Single option</h5>
                        </div>
                    </div>
                    {{--User--}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin8"
                             data-title="text-field.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/1.png')}}" alt="">
                            </div>
                            <h5>Text</h5>
                        </div>
                    </div>
                    {{--Client/Party--}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin9"
                             data-title="Client/Party">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/9.png')}}" alt="">
                            </div>
                            <h5>Party</h5>
                        </div>
                    </div>
                    {{-- Radio Button --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin19"
                             data-title="Radio button consist true or false options">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/radio.png')}}" alt="">
                            </div>
                            <h5>Radio Button</h5>
                        </div>
                    </div>
                    {{--CheckBox --}}
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin20"
                           data-title="CheckBox Consists of multiple Selectable Field">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/check-box.png')}}" alt="">
                            </div>
                            <h5>Check Box</h5>
                        </a>
                    </div>
                    {{-- Phone --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin11"
                             data-title="A phone number field can contain a phone number (naturally) or a Skype Name with a click-to-call functionality. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/11.png')}}" alt="">
                            </div>
                            <h5>Phone</h5>
                        </div>
                    </div>
                    {{-- Time --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin12"
                             data-title="Time field is used to store times, picked from a handy inline timepicker. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/12.png')}}" alt="">
                            </div>
                            <h5>Time</h5>
                        </div>
                    </div>
                    {{-- Time Range --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin13"
                             data-title="Time range field is used to store time ranges, picked from a handy inline timepickers. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/13.png')}}" alt="">
                            </div>
                            <h5>Time Range</h5>
                        </div>
                    </div>
                    {{--Date--}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin14"
                             data-title="Date field is used to store dates, picked from a handy inline calendar. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/14.png')}}" alt="">
                            </div>
                            <h5>Date</h5>
                        </div>
                    </div>
                    {{-- Date Range --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin15"
                             data-title="Date range field is used to store date ranges, picked from a handy inline calendars. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/15.png')}}" alt="">
                            </div>
                            <h5>Date range</h5>
                        </div>
                    </div>
                    {{-- Address --}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin16"
                             data-title="Address field is used to store addresses. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/16.png')}}" alt="">
                            </div>
                            <h5>Address</h5>
                        </div>
                    </div>
                    {{--Multiple Images--}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin17"
                             data-title="Multiple Images is used to store multiple images. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                            </div>
                            <h5>Multiple Images</h5>
                        </div>
                    </div>
                    {{--File--}}
                    <div class="col-xs-3">
                        <div class="text-field" id="signin18"
                             data-title="Multiple Files is used to store multiple files. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                            </div>
                            <h5>File</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end Modal -->
<!--All inner-modal-start -->
<!-- Modal 2-->
<div class="modal fade" id="inner-modal-main" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <div class="row">
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin1"
                           data-title="Text field is used to store texts up to 255 characters.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/1.png')}}" alt="">
                            </div>
                            <h5 style="position: absolute; left: 35px;" >Text</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin3"
                           data-title="Large text field is used to store texts longer than usual.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/3.png')}}" alt="">
                            </div>
                            <h5>Large text</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin4"
                           data-title="Numeric field is used to store data such as amount of commission or other custom numerical data ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/4.png')}}" alt="">
                            </div>
                            <h5>Numerical</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin5"
                           data-title="Monetary field is used to store data such as amount of commission ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/5.png')}}" alt="">
                            </div>
                            <h5>Monetary</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin6"
                           data-title="Multiple options field lets you predefine a list of values to choose from.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                            </div>
                            <h5>Multiple option</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin7"
                           data-title="Single option field lets you predefine a list of values out of which one can be selected. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/7.png')}}" alt="">
                            </div>
                            <h5>Single option</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin8"
                           data-title="test">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                            </div>
                            <h5>Employee</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin9"
                           data-title="Party">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/9.png')}}" alt="">
                            </div>
                            <h5>Party</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin19"
                           data-title="Radio button consists of true or false button">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/radio.png')}}" alt="">
                            </div>
                            <h5>Radio Button</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin20"
                           data-title="CheckBox Consists of multiple Selectable Field">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/check-box.png')}}" alt="">
                            </div>
                            <h5>Check Box</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin11"
                           data-title="A phone number field can contain a phone number (naturally) or a Skype Name with a click-to-call functionality. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/11.png')}}" alt="">
                            </div>
                            <h5>Phone</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin12"
                           data-title="Time field is used to store times, picked from a handy inline timepicker. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/12.png')}}" alt="">
                            </div>
                            <h5>Time</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin13"
                           data-title="Time range field is used to store time ranges, picked from a handy inline timepickers. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/13.png')}}" alt="">
                            </div>
                            <h5>Time Range</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin14"
                           data-title="Date field is used to store dates, picked from a handy inline calendar. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/14.png')}}" alt="">
                            </div>
                            <h5>Date</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin15"
                           data-title="Date range field is used to store date ranges, picked from a handy inline calendars. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/15.png')}}" alt="">
                            </div>
                            <h5>Date range</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin16"
                           data-title="Address field is used to store addresses. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/16.png')}}" alt="">
                            </div>
                            <h5>Address</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin17"
                           data-title="Multiple Images is used to store images. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/17.png')}}" alt="">
                            </div>
                            <h5>Multiple Images</h5>
                        </a>
                    </div>
                    <div class="col-xs-3">
                        <a href="" class="text-field" id="signin18"
                           data-title="Multiple Files is used to store Files. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/18.png')}}" alt="">
                            </div>
                            <h5>File</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 1 -->
<div class="modal fade" id="innerfield-modal1" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin1"
                                 data-toggle="tooltip"
                                 data-title="Text field is used to store texts up to 255 characters.">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/1.png')}}" alt="">
                                </div>
                                <h5 >Text</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="title" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" id="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" id="is_mandatory" style="margin-left: 63px">
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 2 -->
{{--<div class="modal fade" id="innerfield-modal2" tabindex="-1" role="dialog"--}}
{{--     aria-labelledby="myModalLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header modal-header-bgs">--}}

{{--                <h4 class="modal-title" id="myModalLabel">Edit field</h4>--}}
{{--                <button type="button" class="close" data-dismiss="modal"--}}
{{--                        aria-label="Close"><span aria-hidden="true">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <h4 class="dis">What type of field do you want to add?</h4>--}}
{{--                <form role="form" action='#' method="post">--}}
{{--                    <div class="form-group row">--}}
{{--                        <div class="col-xs-3">--}}
{{--                            <div class="text-field" id="signin2"--}}
{{--                                 data-title="Text field is used to store texts up to 255 characters and is searchable by all inserted options. ">--}}
{{--                                <div class="img-sec">--}}
{{--                                    <img src="{{asset('assets/custom_field_icons/2.png')}}" alt="">--}}
{{--                                </div>--}}
{{--                                <h5>Autocomplete</h5>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="alert alert-danger" style="display:none"></div>--}}
{{--                    <div class="form-group row">--}}
{{--                        <label for="" class="col-xs-3 col-form-label">Name of the--}}
{{--                            field</label>--}}
{{--                        <div class="col-xs-6">--}}
{{--                            <input type="text" class="form-control"--}}
{{--                                   placeholder="Field name" name="title">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-group row">--}}
{{--                        <div class="col-xs-3"></div>--}}
{{--                        <div class="col-xs-6">--}}
{{--                            <button type="button submit" class="btn btn-primary submit">--}}
{{--                                Save--}}
{{--                            </button>--}}
{{--                            <button type="button" class="btn btn-primary"--}}
{{--                                    data-dismiss="modal">Cancel--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<!-- 3 -->
<div class="modal fade" id="innerfield-modal3" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin3"
                                 data-title="Large text field is used to store texts longer than usual.">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/3.png')}}" alt="">
                                </div>
                                <h5>Large text</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 4 -->
<div class="modal fade" id="innerfield-modal4" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin4"
                                 data-title="Numeric field is used to store data such as amount of commission or other custom numerical data ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/4.png')}}" alt="">
                                </div>
                                <h5>Numerical</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>
                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 5 -->
<div class="modal fade" id="innerfield-modal5" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin5"
                                 data-title="Monetary field is used to store data such as amount of commission ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/5.png')}}" alt="">
                                </div>
                                <h5>Monetary</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>
                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 6 -->
<div class="modal fade" id="innerfield-modal6" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin6"
                                 data-title="Multiple options field lets you predefine a list of values to choose from.">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                                </div>
                                <h5>Multiple options</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Possible
                            values </label>
                        <div class="col-xs-6">
                                <textarea class="form-control" rows="3"
                                          name="options" required></textarea>
                        </div>
                        <div class="col-xs-3">
                            <span>Enter one per line, for example (about deal type): <br>
                            Consulting <br>
                            Training <br>
                            Speaking</span>
                        </div>
                    </div>

                    <div class="form-group row @if ($errors->has('title')) has-error @endif">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control @if ($errors->has('title')) has-error @endif"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 7 -->
<div class="modal fade" id="innerfield-modal7" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin7"
                                 data-title="Single option field lets you predefine a list of values out of which one can be selected. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/7.png')}}" alt="">
                                </div>
                                <h5>Single option</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Possible
                            values </label>
                        <div class="col-xs-6">
                                <textarea class="form-control" rows="3"
                                          name="options" required></textarea>
                        </div>
                        <div class="col-xs-3">
                            <span>Enter one per line, for example (about deal type):<br>
                            Consulting <br>
                            Training  <br>
                            Speaking</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 8 -->
<div class="modal fade" id="innerfield-modal8" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin8"
                                 data-toggle="tooltip"
                                 data-title="Text field is used to store texts up to 255 characters.">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                                </div>
                                <h5>Employee</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="title" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" id="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" id="is_mandatory" style="margin-left: 63px">
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="innerfield-modal9" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin9"
                                 data-toggle="tooltip"
                                 data-title="Party/client.">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/9.png')}}" alt="">
                                </div>
                                <h5>Party</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="title" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" id="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" id="is_mandatory" style="margin-left: 63px">
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{--<div class="modal fade" id="innerfield-modal10" tabindex="-1" role="dialog"--}}
{{--     aria-labelledby="myModalLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header modal-header-bgs">--}}

{{--                <h4 class="modal-title" id="myModalLabel">Edit field</h4>--}}
{{--                <button type="button" class="close" data-dismiss="modal"--}}
{{--                        aria-label="Close"><span aria-hidden="true">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <h4 class="dis">What type of field do you want to add?</h4>--}}
{{--                <form role="form" action='#' method="post">--}}
{{--                    <div class="form-group row">--}}
{{--                        <div class="col-xs-3">--}}
{{--                            <div class="text-field" id="signin10"--}}
{{--                                 data-title="People field can contain one contact out of all the people stored on your deltaSalesCRM account. ">--}}
{{--                                <div class="img-sec">--}}
{{--                                    <img src="{{asset('assets/custom_field_icons/10.png')}}" alt="">--}}
{{--                                </div>--}}
{{--                                <h5>Contact</h5>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-group row">--}}
{{--                        <label for="" class="col-xs-3 col-form-label">Name of the--}}
{{--                            field</label>--}}
{{--                        <div class="col-xs-6">--}}
{{--                            <input type="text" class="form-control"--}}
{{--                                   placeholder="Field name" name="title">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-group row">--}}
{{--                        <div class="col-xs-3"></div>--}}
{{--                        <div class="col-xs-6">--}}
{{--                            <button type="button submit" class="btn btn-primary submit">--}}
{{--                                Save--}}
{{--                            </button>--}}
{{--                            <button type="button" class="btn btn-primary"--}}
{{--                                    data-dismiss="modal">Cancel--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<!-- 11 -->
<div class="modal fade" id="innerfield-modal11" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin11"
                                 data-title="A phone number field can contain a phone number (naturally) or a Skype Name with a click-to-call functionality. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/11.png')}}" alt="">
                                </div>
                                <h5>Phone</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 12 -->
<div class="modal fade" id="innerfield-modal12" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin12"
                                 data-title="Time field is used to store times, picked from a handy inline timepicker. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/12.png')}}" alt="">
                                </div>
                                <h5>Time</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 13 -->
<div class="modal fade" id="innerfield-modal13" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin13"
                                 data-title="Time range field is used to store time ranges, picked from a handy inline timepickers. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/13.png')}}" alt="">
                                </div>
                                <h5>Time range</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 14 -->
<div class="modal fade" id="innerfield-modal14" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin14"
                                 data-title="Date field is used to store dates, picked from a handy inline calendar. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/14.png')}}" alt="">
                                </div>
                                <h5>Date</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 15 -->
<div class="modal fade" id="innerfield-modal15" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin15"
                                 data-title="Date range field is used to store date ranges, picked from a handy inline calendars. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/15.png')}}" alt="">
                                </div>
                                <h5>Date range</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 16 -->
<div class="modal fade" id="innerfield-modal16" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin16"
                                 data-title="Address field is used to store addresses. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/16.png')}}" alt="">
                                </div>
                                <h5>Address</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 17 -->
<div class="modal fade" id="innerfield-modal17" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin16"
                                 data-title="Multple images is used to store images. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/17.png')}}" alt="">
                                </div>
                                <h5>Multiple Images</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 18 -->
<div class="modal fade" id="innerfield-modal18" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin16"
                                 data-title="Multple Files is used to store files. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/check-box.png')}}" alt="">
                                </div>
                                <h5>File</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title">
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="innerfield-modal19" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin19"
                                 data-title="Radio Button contains yes and no options">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/radio.png')}}" alt="">
                                </div>
                                <h5>Radio Button</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Possible
                            values </label>
                        <div class="col-xs-6">
                                <textarea class="form-control" rows="3"
                                          name="options" required></textarea>
                        </div>
                        <div class="col-xs-3">
                            <span>Enter one per line, for example (about deal type):<br>
                            Consulting <br>
                            Training  <br>
                            Speaking</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="innerfield-modal20" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">

                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                    <div class="form-group row">
                        <div class="col-xs-3">
                            <div class="text-field" id="signin20"
                                 data-title="Single option field lets you predefine a list of values out of which one can be selected. ">
                                <div class="img-sec">
                                    <img src="{{asset('assets/custom_field_icons/check-box.png')}}" alt="">
                                </div>
                                <h5>Check Box</h5>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Possible
                            values </label>
                        <div class="col-xs-6">
                                <textarea class="form-control" rows="3"
                                          name="options" required></textarea>
                        </div>
                        <div class="col-xs-3">
                            <span>Enter one per line, for example (about deal type):<br>
                            Consulting <br>
                            Training  <br>
                            Speaking</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-xs-3 col-form-label">Name of the
                            field</label>
                        <div class="col-xs-6">
                            <input type="text" class="form-control"
                                   placeholder="Field name" name="title" required>
                        </div>

                        <div class="col-xs-6">
                            <label>
                                Mandatory?
                            </label>
                            <input type="checkbox" name="required" id="is_mandatory" style="margin-left: 63px">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-3"></div>
                        <div class="col-xs-6">
                            <button type="button submit" class="btn btn-primary submit">
                                Save
                            </button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>