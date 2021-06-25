<div class="row">

    <div class="col-xs-12">
      <h3 class="site-tital">Custom Fields</h3>
    </div>

    <div class="col-xs-12">

    <!-- Custom Tabs -->
      <div class="custom-content">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Party</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">

            <button class="btn btn-primary pull-right addNewCustomField"
                    style="color:white;background-color: #0b7676!important;
                    border-color: #0b7676!important;margin-right:15px;"
                    data-module="Party">
                    <i class="fa fa-plus"></i>
                Create New
            </button>
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

                  <td style="width:280px;max-width: 280;cursor: pointer; color: #01a9ac;" onclick="editField({{$customField}} , $(this));">
                    {{$customField->title}}

                  </td>

                  <td>{{$customField->type}}</td>
                  <td>
                    <a href="#" class="edit-modal" data-id="{{$customField->id}}"
                       data-status="{{$customField->status}}">

                       @if($customField->status==1)
                        <span class="label label-success">Active</span>
                      @else
                       <span class="label label-danger">Inactive</span>
                      @endif
                    </a>
                    <!-- @if($customField->status==1)
                      <a href="#" class="statusupdate_customField" data-id="{{$customField->id}}">

              <span class="label label-success" style="font-size: 12px !important;margin: 7px 0px;
    display: inline-block;    padding: 5px;"> Active</span>
              </a>
               @else
              <a href="#" class="statusupdate_customField" data-id="{{$customField->id}}">
              <span class="label label-danger" style="font-size: 12px !important;margin: 7px 0px;
    display: inline-block;    padding: 5px;"> Inactive</span>
              </a>

          @endif -->
                      <!-- <button class="danger-btn-right deleteContent deletebtn_customField_{{$customField->id}}" data-id="{{$customField->id}}"><i class="fa fa-trash"></i></button>
                      <button class="primary-btn-right editContent editbtn_customField_{{$customField->id}}" data-id="{{$customField->id}}"><i class="fa fa-edit"></i></button> -->
                   <!--  <span class="customField_update_{{$customField->id}} hide">
                        <button class="danger-btn-right cancel_customfield" data-id="{{$customField->id}}"><i class="fa fa-times"></i></button>
                        <button class="success-btn-right update_customField" data-id="{{$customField->id}}"><i class="fa fa-check"></i></button>
                    </span> -->
                    <span class="customfield_refresh_{{$customField->id}} hide">
                        <button class="success-btn-right refreshing"><i class="fa fa-refresh"></i></button>
                    </span>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div>
    <!-- nav-tabs-custom -->
    </div>
    <span id="customUpdateUrl" class="hide" data-url="{{domain_route('company.admin.customfield.updateVisibility')}}">
    <span id="customTitleUpdateUrl" class="hide" data-url="{{domain_route('company.admin.customfield.updateTitle')}}">
    <span id="customFieldDeleteUrl" class="hide" data-url="{{domain_route('company.admin.customfield.destroy')}}">
    <span id="customStatusUpdateUrl" class="hide" data-url="{{domain_route('company.admin.customfield.updateStatus')}}">

</div>
<div class="modal modal-default fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
       data-keyboard="false" data-backdrop="static">
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

              {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">

                <span class='glyphicon glyphicon-remove'></span> Close

              </button> --}}

            </div>

          </form>

        </div>

      </div>

    </div>

  </div>

<!-- Main Modal -->
<div class="modal fade" id="customFieldModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true" data-url="{{domain_route('company.admin.customfield.addNew')}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-bgs">
                <div class="row">
                    <div class="col-xs-10">
                        <h4 class="modal-title" id="myModalLabel">Add a field form</h4>
                    </div>
                    <div class="col-xs-2">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <h4 class="dis">What type of field do you want to add?</h4>
                <div class="row">
                    <div class="col-xs-3">
                        <div class="text-field" id="signin1"
                           data-title="Text field is used to store texts up to 255 characters.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/1.png')}}" alt="">
                            </div>
                            <h5>Text</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin2"
                           data-title="Text field is used to store texts up to 255 characters and is searchable by all inserted options. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/2.png')}}" alt="">
                            </div>
                            <h5>Autocomplete</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin3"
                           data-title="Large text field is used to store texts longer than usual.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/3.png')}}" alt="">
                            </div>
                            <h5>Large text</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin4"
                           data-title="Numeric field is used to store data such as amount of commission or other custom numerical data ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/4.png')}}" alt="">
                            </div>
                            <h5>Numerical</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin5"
                           data-title="Monetary field is used to store data such as amount of commission ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/5.png')}}" alt="">
                            </div>
                            <h5>Monetary</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin6"
                           data-title="Multiple options field lets you predefine a list of values to choose from.">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/6.png')}}" alt="">
                            </div>
                            <h5>Multiple option</h5>
                       </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin7"
                           data-title="Single option field lets you predefine a list of values out of which one can be selected. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/7.png')}}" alt="">
                            </div>
                            <h5>Single option</h5>
                       </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin8"
                           data-title="User field can contain one user amongst users of your deltaSalesCRM account. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                            </div>
                            <h5>User</h5>
                        </div>
                    </div>
<!--                     <div class="col-xs-3">
                        <a href="" class="text-field" id="signin10"
                           data-title="Contact field can contain one contact out of all the people stored on your deltaSalesCRM account. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/10.png')}}" alt="">
                            </div>
                            <h5>Contact</h5>
                        </a>
                    </div> -->
                    <div class="col-xs-3">
                        <div class="text-field" id="signin11"
                           data-title="A phone number field can contain a phone number (naturally) or a Skype Name with a click-to-call functionality. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/11.png')}}" alt="">
                            </div>
                            <h5>Phone</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin12"
                           data-title="Time field is used to store times, picked from a handy inline timepicker. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/12.png')}}" alt="">
                            </div>
                            <h5>Time</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin13"
                           data-title="Time range field is used to store time ranges, picked from a handy inline timepickers. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/13.png')}}" alt="">
                            </div>
                            <h5>Time Range</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin14"
                           data-title="Date field is used to store dates, picked from a handy inline calendar. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/14.png')}}" alt="">
                            </div>
                            <h5>Date</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin15"
                           data-title="Date range field is used to store date ranges, picked from a handy inline calendars. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/15.png')}}" alt="">
                            </div>
                            <h5>Date range</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin16"
                           data-title="Address field is used to store addresses. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/16.png')}}" alt="">
                            </div>
                            <h5>Address</h5>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="text-field" id="signin17"
                           data-title="Multiple Images is used to store multiple images. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                            </div>
                            <h5>Multiple Images</h5>
                        </div>
                    </div>
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
                <p>Please bear in mind that customized fields are shared with all
                    users throughout your company.</p>
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
                            <h5>Text</h5>
                        </a>
                    </div>
<!--                     <div class="col-xs-3">
                        <a href="" class="text-field" id="signin2"
                           data-title="Text field is used to store texts up to 255 characters and is searchable by all inserted options. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/2.png')}}" alt="">
                            </div>
                            <h5>Autocomplete</h5>
                        </a>
                    </div> -->
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
                           data-title="User field can contain one user amongst users of your deltaSalesCRM account. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                            </div>
                            <h5>User</h5>
                        </a>
                    </div>
<!--                     <div class="col-xs-3">
                        <a href="" class="text-field" id="signin9"
                           data-title="Organization field can contain one organization out of all the organizations stored on your deltaSalesCRM account. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/9.png')}}" alt="">
                            </div>
                            <h5>Organization</h5>
                        </a>
                    </div> -->
                    <!-- <div class="col-xs-3">
                        <a href="" class="text-field" id="signin10"
                           data-title="Contact field can contain one contact out of all the people stored on your deltaSalesCRM account. ">
                            <div class="img-sec">
                                <img src="{{asset('assets/custom_field_icons/10.png')}}" alt="">
                            </div>
                            <h5>Contact</h5>
                        </a>
                    </div> -->
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
                <p>Please bear in mind that customized fields are shared with all
                    users throughout your company.</p>
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
                                    <h5>Text</h5>
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- 2 -->
    <div class="modal fade" id="innerfield-modal2" tabindex="-1" role="dialog"
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
                                <div class="text-field" id="signin2"
                                   data-title="Text field is used to store texts up to 255 characters and is searchable by all inserted options. ">
                                    <div class="img-sec">
                                        <img src="{{asset('assets/custom_field_icons/2.png')}}" alt="">
                                    </div>
                                    <h5>Autocomplete</h5>
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
                </div>
            </div>
        </div>
    </div>
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
                    <h4 class="dis">What type of field do you want to add?</h4>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                          name="options"></textarea>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                          name="options"></textarea>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                   data-title="User field can contain one user amongst users of your deltaSalesCRM account. ">
                                    <div class="img-sec">
                                        <img src="{{asset('assets/custom_field_icons/8.png')}}" alt="">
                                    </div>
                                    <h5>User</h5>
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- 9 -->
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
                                   title="Organization field can contain one organization out of all the organizations stored on your deltaSalesCRM account. ">
                                    <div class="img-sec">
                                        <img src="{{asset('assets/custom_field_icons/9.png')}}" alt="">
                                    </div>
                                    <h5>Organization</h5>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-xs-3 col-form-label">Name of the
                                field</label>
                            <div class="col-xs-6">
                                <input type="text" class="form-control"
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- 10 -->
    <div class="modal fade" id="innerfield-modal10" tabindex="-1" role="dialog"
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
                                <div class="text-field" id="signin10"
                                   data-title="People field can contain one contact out of all the people stored on your deltaSalesCRM account. ">
                                    <div class="img-sec">
                                        <img src="{{asset('assets/custom_field_icons/10.png')}}" alt="">
                                    </div>
                                    <h5>Contact</h5>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-xs-3 col-form-label">Name of the
                                field</label>
                            <div class="col-xs-6">
                                <input type="text" class="form-control"
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
                </div>
            </div>
        </div>
    </div>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                       placeholder="Field name" name="title">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                                        <img src="{{asset('assets/custom_field_icons/18.png')}}" alt="">
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

                    <p>Please bear in mind that customized fields are shared with all
                        users throughout your company.</p>
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
                        <div class="text-center">
                            Are you sure you want to delete this custom field?
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button id="btn_delete_customfield" type="button" class="btn btn-primary actionBtn" data-id="">
                            <span id="footer_action_button" class='glyphicon'></span> Delete
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Cancel
                        </button>
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
                        <div class="text-center">
                            Are you sure you want to delete this custom field?
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button id="btn_delete_customfield" type="button" class="btn btn-primary actionBtn" data-id="">
                            <span id="footer_action_button" class='glyphicon'></span> Delete
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Cancel
                        </button>
                    </div>
            </div>
        </div>
    </div>
</div>

