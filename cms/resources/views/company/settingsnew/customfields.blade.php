@extends('layouts.company')

@section('title', 'Settings')

@section('stylesheets')

<link rel="stylesheet" href="{{asset('assets/plugins/settings/css/customfield.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/settings.css') }}">

@endsection

@section('content')
  <section class="content">
    <div class="row" style="margin-bottom: 25px;">
      <div class="col-xs-12">
        @include('company.settingsnew.settingheader')
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="site-tital">Custom Fields</h3>
          </div>
          <div class="box-body">
            <div class="custom-content">
              <ul class="nav nav-tabs">
                <li class="active">
                  <a href="#tab_1" data-toggle="tab" aria-expanded="true">Party</a>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                  <button class="btn btn-primary pull-right addNewCustomField" style="color:white;background-color: #0b7676!important;border-color: #0b7676!important;margin-right:15px;" data-module="Party"><i class="fa fa-plus"></i> Create New</button>
                  <table id="party_custom_fields" class="table">
                    <thead>
                      <tr>
                        <th style="min-width:250px!important;">Field Name</th>
                        <th style="min-width:200px!important;">Type</th>
                        <th style="max-width:100px!important;">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($customFields->where('for','Party') as $customField)
                        <tr>
                          <td style="width:280px;max-width: 280;cursor: pointer; color: #01a9ac;" onclick="editField({{$customField}} , $(this));">  {{$customField->title}}</td>
                          <td>{{$customField->type}}</td>
                          <td>
                            <a href="#" class="edit-modal" data-id="{{$customField->id}}" data-status="{{$customField->status}}">
                              @if($customField->status==1)
                                <span class="label label-success">Active</span>
                              @else
                              <span class="label label-danger">Inactive</span>
                              @endif
                            </a>
                            <span class="customfield_refresh_{{$customField->id}} hide">
                              <button class="success-btn-right refreshing">
                                <i class="fa fa-refresh"></i>
                              </button>
                            </span>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <span id="customUpdateUrl" class="hide" data-url="{{domain_route('company.admin.customfield.updateVisibility')}}">
          <span id="customTitleUpdateUrl" class="hide" data-url="{{domain_route('company.admin.customfield.updateTitle')}}">
          <span id="customFieldDeleteUrl" class="hide" data-url="{{domain_route('company.admin.customfield.destroy')}}">
          <span id="customStatusUpdateUrl" class="hide" data-url="{{domain_route('company.admin.customfield.updateStatus')}}">
        </div>
        <div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
              </div>
              <div class="modal-body">
                <p class="text-center">
                  Sorry! You are not authorized to update the status for the selected record.
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

        <div id="myModal" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
              </div>
              <div class="modal-body">
                <form class="form-horizontal" role="form" id="changeStatus" method="POST"
                  action="{{domain_route('company.admin.customfield.updateStatus')}}">
                  {{csrf_field()}}
                  <input type="hidden" name="customfield_id" id="customfield_id" value="">
                  <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Status</label>
                    <div class="col-sm-10">
                      <select class="form-control" id="status" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
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

        <!-- Main Modal -->
        <div class="modal fade" id="customFieldModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-url="{{domain_route('company.admin.customfield.addNew')}}">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <div class="row">
                  <div class="col-xs-10">
                    <h4 class="modal-title" id="myModalLabel">Add a field form</h4>
                  </div>
                  <div class="col-xs-2">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  </div>
                </div>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <div class="row">
                  <div class="col-xs-3">
                    <div class="text-field" id="signin1" data-title="Text field is used to store texts up to 255 characters.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/1.png')}}" alt="">
                      </div>
                      <h5>Text</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin2" data-title="Text field is used to store texts up to 255 characters and is searchable by all inserted options. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/2.png')}}" alt="">
                      </div>
                      <h5>Autocomplete</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin3" data-title="Large text field is used to store texts longer than usual.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/3.png')}}" alt="">
                      </div>
                      <h5>Large text</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin4" data-title="Numeric field is used to store data such as amount of commission or other custom numerical data ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/4.png')}}" alt="">
                      </div>
                      <h5>Numerical</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin5" data-title="Monetary field is used to store data such as amount of commission ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/5.png')}}" alt="">
                      </div>
                      <h5>Monetary</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin6" data-title="Multiple options field lets you predefine a list of values to choose from.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                      </div>
                      <h5>Multiple option</h5>
                   </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin7" data-title="Single option field lets you predefine a list of values out of which one can be selected. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/7.png')}}" alt="">
                      </div>
                      <h5>Single option</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin8" data-title="User field can contain one user amongst users of your deltaSalesCRM account. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                      </div>
                      <h5>User</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin11" data-title="A phone number field can contain a phone number (naturally) or a Skype Name with a click-to-call functionality. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/11.png')}}" alt="">
                      </div>
                      <h5>Phone</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin12" data-title="Time field is used to store times, picked from a handy inline timepicker. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/12.png')}}" alt="">
                      </div>
                      <h5>Time</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin13" data-title="Time range field is used to store time ranges, picked from a handy inline timepickers. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/13.png')}}" alt="">
                      </div>
                      <h5>Time Range</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin14" data-title="Date field is used to store dates, picked from a handy inline calendar. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/14.png')}}" alt="">
                      </div>
                      <h5>Date</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin15" data-title="Date range field is used to store date ranges, picked from a handy inline calendars.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/15.png')}}" alt="">
                      </div>
                      <h5>Date range</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin16" data-title="Address field is used to store addresses.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/16.png')}}" alt="">
                      </div>
                      <h5>Address</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin17" data-title="Multiple Images is used to store multiple images.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                      </div>
                      <h5>Multiple Images</h5>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <div class="text-field" id="signin18" data-title="Multiple Files is used to store multiple files. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                      </div>
                      <h5>File</h5>
                    </div>
                  </div>
                </div>
                <p>Please bear in mind that customized fields are shared with all
                    users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!--end Modal -->

        <!--All inner-modal-start -->

        <!-- Modal 2-->
        <div class="modal fade" id="inner-modal-main" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <div class="row">
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin1" data-title="Text field is used to store texts up to 255 characters.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/1.png')}}" alt="">
                      </div>
                      <h5>Text</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin3" data-title="Large text field is used to store texts longer than usual.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/3.png')}}" alt="">
                      </div>
                      <h5>Large text</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin4" data-title="Numeric field is used to store data such as amount of commission or other custom numerical data ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/4.png')}}" alt="">
                      </div>
                      <h5>Numerical</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin5" data-title="Monetary field is used to store data such as amount of commission ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/5.png')}}" alt="">
                      </div>
                      <h5>Monetary</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin6" data-title="Multiple options field lets you predefine a list of values to choose from.">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                      </div>
                      <h5>Multiple option</h5>
                  </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin7" data-title="Single option field lets you predefine a list of values out of which one can be selected. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/7.png')}}" alt="">
                      </div>
                      <h5>Single option</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin8" data-title="User field can contain one user amongst users of your deltaSalesCRM account. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                      </div>
                      <h5>User</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin11" data-title="A phone number field can contain a phone number (naturally) or a Skype Name with a click-to-call functionality. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/11.png')}}" alt="">
                      </div>
                      <h5>Phone</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin12" data-title="Time field is used to store times, picked from a handy inline timepicker. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/12.png')}}" alt="">
                      </div>
                      <h5>Time</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin13" data-title="Time range field is used to store time ranges, picked from a handy inline timepickers. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/13.png')}}" alt="">
                      </div>
                      <h5>Time Range</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin14" data-title="Date field is used to store dates, picked from a handy inline calendar. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/14.png')}}" alt="">
                      </div>
                      <h5>Date</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin15" data-title="Date range field is used to store date ranges, picked from a handy inline calendars. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/15.png')}}" alt="">
                      </div>
                      <h5>Date range</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin16" data-title="Address field is used to store addresses. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/16.png')}}" alt="">
                      </div>
                      <h5>Address</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin17" data-title="Multiple Images is used to store images. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/17.png')}}" alt="">
                      </div>
                      <h5>Multiple Images</h5>
                    </a>
                  </div>
                  <div class="col-xs-3">
                    <a href="" class="text-field" id="signin18" data-title="Multiple Files is used to store Files. ">
                      <div class="img-sec">
                        <img src="{{asset('assets/custom_field_icons/18.png')}}" alt="">
                      </div>
                      <h5>File</h5>
                    </a>
                  </div>
                </div>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 1 -->
        <div class="modal fade" id="innerfield-modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin1" data-toggle="tooltip" data-title="Text field is used to store texts up to 255 characters.">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/1.png')}}" alt="">
                        </div>
                        <h5>Text</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">Save </button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 2 -->
        <div class="modal fade" id="innerfield-modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin2" data-title="Text field is used to store texts up to 255 characters and is searchable by all inserted options. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/2.png')}}" alt="">
                        </div>
                        <h5>Autocomplete</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">Save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 3 -->
        <div class="modal fade" id="innerfield-modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin3" data-title="Large text field is used to store texts longer than usual.">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/3.png')}}" alt="">
                        </div>
                        <h5>Large text</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">Save </button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 4 -->
        <div class="modal fade" id="innerfield-modal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin4" data-title="Numeric field is used to store data such as amount of commission or other custom numerical data ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/4.png')}}" alt="">
                        </div>
                        <h5>Numerical</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">Save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 5 -->
        <div class="modal fade" id="innerfield-modal5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin5" data-title="Monetary field is used to store data such as amount of commission ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/5.png')}}" alt="">
                        </div>
                        <h5>Monetary</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">Save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 6 -->
        <div class="modal fade" id="innerfield-modal6" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin6" data-title="Multiple options field lets you predefine a list of values to choose from.">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                        </div>
                        <h5>Multiple options</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Possible values </label>
                    <div class="col-xs-6">
                      <textarea class="form-control" rows="3" ame="options"></textarea>
                    </div>
                    <div class="col-xs-3">
                      <span>Enter one per line, for example (about deal type): 
                        <br>Consulting <br>Training <br>Speaking
                      </span>
                    </div>
                  </div>
                  <div class="form-group row @if ($errors->has('title')) has-error @endif">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control @if ($errors->has('title')) has-error @endif" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">Save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 7 -->
        <div class="modal fade" id="innerfield-modal7" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin7" data-title="Single option field lets you predefine a list of values out of which one can be selected. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/7.png')}}" alt="">
                        </div>
                        <h5>Single option</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Possible values </label>
                    <div class="col-xs-6">
                      <textarea class="form-control" rows="3" name="options"></textarea>
                    </div>
                    <div class="col-xs-3">
                      <span>Enter one per line, for example (about deal type):
                        <br>Consulting <br>Training  <br>Speaking
                      </span>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">Save </button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 8 -->
        <div class="modal fade" id="innerfield-modal8" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin8" data-title="User field can contain one user amongst users of your deltaSalesCRM account. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                        </div>
                        <h5>User</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 9 -->
        <div class="modal fade" id="innerfield-modal9" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin9" title="Organization field can contain one organization out of all the organizations stored on your deltaSalesCRM account. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/9.png')}}" alt="">
                        </div>
                        <h5>Organization</h5>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 10 -->
        <div class="modal fade" id="innerfield-modal10" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin10" data-title="People field can contain one contact out of all the people stored on your deltaSalesCRM account.">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/10.png')}}" alt="">
                        </div>
                        <h5>Contact</h5>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 11 -->
        <div class="modal fade" id="innerfield-modal11" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin11" data-title="A phone number field can contain a phone number (naturally) or a Skype Name with a click-to-call functionality. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/11.png')}}" alt="">
                        </div>
                        <h5>Phone</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 12 -->
        <div class="modal fade" id="innerfield-modal12" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin12" data-title="Time field is used to store times, picked from a handy inline timepicker. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/12.png')}}" alt="">
                        </div>
                        <h5>Time</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 13 -->
        <div class="modal fade" id="innerfield-modal13" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin13" data-title="Time range field is used to store time ranges, picked from a handy inline timepickers. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/13.png')}}" alt="">
                        </div>
                        <h5>Time range</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 14 -->
        <div class="modal fade" id="innerfield-modal14" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin14" data-title="Date field is used to store dates, picked from a handy inline calendar. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/14.png')}}" alt="">
                        </div>
                        <h5>Date</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 15 -->
        <div class="modal fade" id="innerfield-modal15" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin15" data-title="Date range field is used to store date ranges, picked from a handy inline calendars. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/15.png')}}" alt="">
                        </div>
                        <h5>Date range</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 16 -->
        <div class="modal fade" id="innerfield-modal16" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin16" data-title="Address field is used to store addresses. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/16.png')}}" alt="">
                        </div>
                        <h5>Address</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6"> 
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 17 -->
        <div class="modal fade" id="innerfield-modal17" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin16" data-title="Multple images is used to store images. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/17.png')}}" alt="">
                        </div>
                        <h5>Multiple Images</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- 18 -->
        <div class="modal fade" id="innerfield-modal18" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header modal-header-bgs">
                <h4 class="modal-title" id="myModalLabel">Edit field</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <form role="form" action='#' method="post">
                  <div class="form-group row">
                    <div class="col-xs-3">
                      <div class="text-field" id="signin16" data-title="Multple Files is used to store files. ">
                        <div class="img-sec">
                          <img src="{{asset('assets/custom_field_icons/18.png')}}" alt="">
                        </div>
                        <h5>File</h5>
                      </div>
                    </div>
                  </div>
                  <div class="alert alert-danger" style="display:none"></div>
                  <div class="form-group row">
                    <label for="" class="col-xs-3 col-form-label">Name of the field</label>
                    <div class="col-xs-6">
                      <input type="text" class="form-control" placeholder="Field name" name="title">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-6">
                      <button type="button submit" class="btn btn-primary submit">save</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel </button>
                    </div>
                  </div>
                </form>
                <p>Please bear in mind that customized fields are shared with all users throughout your company.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- end -->

        <!--All inner-modal-end -->
        <div id="editCustomFieldModal" class="modal fade" role="dialog">
          <div class="modal-dialog">
          <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Delete Custom Field</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <div class="text-center">Are you sure you want to delete this custom field?</div>
                </div>
                <div class="modal-footer">
                  <button id="btn_delete_customfield" type="button" class="btn btn-primary actionBtn" data-id=""><span id="footer_action_button" class='glyphicon'></span> Delete </button>
                  <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cancel </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="deleteCustomFieldModal" class="modal fade" role="dialog">
          <div class="modal-dialog">
          <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Delete Custom Field</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <div class="text-center">Are you sure you want to delete this custom field?</div>
                </div>
                <div class="modal-footer">
                  <button id="btn_delete_customfield" type="button" class="btn btn-primary actionBtn" data-id=""><span id="footer_action_button" class='glyphicon'></span> Delete </button>
                  <button type="button" class="btn btn-warning" data-dismiss="modal"><span class='glyphicon glyphicon-remove'></span> Cancel </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  
<script type="text/javascript" src="{{asset('assets/plugins/settings/customfield.js')}}"></script>
<script>

//var customfieldtable=$('#party_custom_fields').DataTable();
function editField(object, element) {
  $("div[id^='innerfield-modal']").each(function (i, obj) {
    var temp = $(obj).find('h5').html();
    if (temp == 'Multiple options') {
      temp = "Multiple options";
    } else if (temp == 'Contact') {
                    temp = 'Person';
                }
    if (temp == object.type) {
      $(obj).modal('show');
      $(obj).find('input').val(object.title);
      $(obj).find('textarea').val('');
      if (object.type == "Single option" || object.type == "Multiple options") {
        var new_html = '';
       // alert(object.options);
        JSON.parse(object.options).forEach(function (item) {
            new_html += (item) + '\n';
        });
        $(obj).find('textarea').val(new_html);
      }

      $(obj).find('form').on('submit', function (e) {
        e.preventDefault();
        var dataid = object.id;
         var url = "{{domain_route('company.admin.customfields.custom_field')}}";
        data = {
          _token: $('meta[name="csrf-token"]').attr('content'),
          title: $(this).find('input').val(),
          id:dataid
        };
        if (object.type == "Single option" || object.type == "Multiple options") {
          var avalue = $(this).find('textarea').val();
        var newVal = avalue.replace(/^\s*[\r\n]/gm, '');
        var options = newVal.split(/\n/);
          //var options=$(this).find('textarea').val().split(/\n/);
         //s alert(options);
          data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            title: $(this).find('input').val(),
            id:dataid,
            options: options
          };
        }
            // debugger;
        $.post(url, data, function (data) {

          if(data.errors)
          {
            $('.alert-danger').html('');

            $.each(data.errors, function(key, value){
              $('.alert-danger').show();
              $('.alert-danger').append('<li>'+value+'</li>');
            });
          }else{
          //alert(response);
          $('.alert-danger').hide();
          $('.modal').modal('hide');
          //customfieldtable.reload();
          $('#party_custom_fields').DataTable().destroy();
          $('#party_custom_fields').find('tbody').first().html(data);
                initializeDataTable();
              }
        });
      //   $.ajax({
      //     headers: {
      //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      //     },
      //     url  : url,
      //     type : "POST",
      //     data : {
      //       "id":dataid,
      //       "title":value,
      //       "options":options,
      //   },
      //   beforeSend:function(){
      //       $('.customfield_refresh_'+id).removeClass('hide');
      //       $('.customField_update_'+id).addClass('hide');
      //   },
      //   success: function (data) {
      //      alert(data);
      //   },
      //   error:function(error){
      //       console.log('Oops! Something went Wrong'+error);
      //   }
      // });
      });
    }
  });         
};

</script>
@endsection