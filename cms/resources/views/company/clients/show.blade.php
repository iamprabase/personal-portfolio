@extends('layouts.company')
@section('title', 'Show Party')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="{{asset('assets/plugins/zoomImage/zoomer.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style type="text/css">
  .nav-pills>li.active>a,
  .nav-pills>li.active>a:hover,
  .nav-pills>li.active>a:focus {
    border-top-color: #0b7676;
  }

  .retimg{
    vertical-align: initial;
    width: 25px;
  }

  .multiselect-item.multiselect-group label input {
    height: auto;
  }

  .select2-selection--multiple{
    font-size: 12px!important;
  }

  .dropdown-menu>.disabled>a,
  .dropdown-menu>.disabled>a:focus,
  .dropdown-menu>.disabled>a:hover {
    color: #fff !important;
    background-color: #337ab7;
  }

  .panel-heading {
    color: #fff !important;
    background-color: #0b7676 !important;
  }

  .nav-pills>li.active>a,
  .nav-pills>li.active>a:focus,
  .nav-pills>li.active>a:hover {
    color: #fff;
    background-color: #0b7676;
  }

  .nav-pills>li>a {
    border-radius: 0;
    border-top: 3px solid transparent;
    color: #0b7676;
    background-color: #ecf0f5;
  }

  .select2-selection__choice {
    background-color: teal !important;
    border: #0b7676 !important;
    border-radius: 2px !important;
  }

  .select2-selection__choice__remove {
    color: white !important;
  }

  .select-8-hidden-accessible {
    border: 1px solid grey !important;
  }

  .ms-options-wrap>.ms-options {
    position: absolute !important;
  }

  .ms-options-wrap>button:focus,
  .ms-options-wrap>button {
    color: #444;
    padding: 5px 20px 5px 10px;
    font-size: 14px;
    border: 1px solid #d2d6de;
  }

  .ms-options-wrap>button:after {
    content: ' ';
    border: 4px solid rgba(0, 0, 0, 0);
    border-top-color: #333339;
  }

  #ms-list-1 {
    position: relative;
  }

  .nav-pills>li.active>a {
    font-weight: 400;
  }

  .nav-tabs-custom {
    box-shadow: none;
  }

  .table-fix {
    margin-top: -60px;
  }

  div.dataTables_wrapper div.dataTables_filter {
    text-align: left;
  }

  .box-header {
    position: static;
    padding: none;
  }

  .bottom-border {
    border: none;
    border-bottom: 1px solid #ccc;
    padding-bottom: 5px;
  }

  #grandTotalAmount,
  #grandTotalCAmount,
  #grandTotalEAmount {
    margin-left: 70%;
    line-height: 3;
  }

  #ActivateUpdate {
    margin-right: 10px;
    display: inline-block;
    float: right;
  }

  #clientgroups .btn.btn-primary {
    color: white !important;
    background-color: #089c9c;
    margin-right: 10px;
  }

  #clientgroups .active {
    color: white !important;
    background-color: #0baa76 !important;
  }

  .hide_table {
    display: none;
  }

  #subclient .btn-success {
    color: #5cb85c !important;
    background-color: transparent !important;
    border-color: transparent !important;
  }

  #subclient .btn-success:hover {
    background-color: transparent !important;
    border-color: transparent !important;
    color: #00da76 !important;
  }

  #subclient .btn-warning {
    color: #f0ad4e !important;
    background-color: transparent !important;
    border-color: transparent !important;
  }

  #subclient .btn-warning:hover {
    color: #f0ad4e !important;
    background-color: transparent !important;
    border-color: transparent !important;
  }

  #subclient .btn-danger {
    background-color: transparent !important;
    border-color: transparent !important;
    color: #d43f3a !important;
  }

  #subclient .btn-danger:hover {
    background-color: transparent !important;
    border-color: transparent !important;
    color: #d43f3a !important;
  }

  .check {
    height: 16px;
  }
  .box-loader{
      opacity: 0.5;
  }

  #UpdateBasicDetail>.row>#imggroup>.imgUp>.imagePreview{
    background-image: url(../../../cms/storage/app/public/uploads/nopartyimage.png);
  }

  /* .fileCreateModalBody>.form-group>.imgUp>.imagePreview{
    background-image: unset!important;
  } */

  .imagePreview {
    background-color: grey;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center center;
  }
  .cimage{
    background-color:grey;
    height: 60px;
    width: 60px;
    border-radius: 10px;
  }
  .emp-show-profile-pic {
    margin: 5px 5px;
  }
  .emp-show-profile-gotpic{
    border-radius: 10px;
    margin: 5px 5px;
    width: 60px;
    height: 60px;
  }

  .clientImageExists{
    background:none;
    background-color:grey;
    background-size: contain; 
    background-repeat: no-repeat; 
    background-position: center center;
  }

  #lblChange{
    background-color: #079292!important;
  }

  .ActivateEdit,.ActivateCancel,.ActivateUpdate{
    margin-top:205px;
  }
  .partyactionbtn{
    margin-right: 10px;
  }

  /*design for activity checkbox*/
  .round {
    position: relative;
  }

  .round label {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 50%;
    cursor: pointer;
    height: 28px;
    left: 0;
    position: absolute;
    top: 0px;
    width: 28px;
  }

  .round label:after {
    border: 2px solid #fff;
    border-top: none;
    border-right: none;
    content: "";
    height: 6px;
    left: 7px;
    opacity: 0;
    position: absolute;
    top: 8px;
    transform: rotate(-45deg);
    width: 12px;
  }

  .round input[type="checkbox"] {
    visibility: hidden;
  }

  .round input[type="checkbox"]:checked + label {
    background-color: #66bb6a;
    border-color: #66bb6a;
  }

  .round input[type="checkbox"]:checked + label:after {
    opacity: 1;
  }
  /*End activity checkbox design*/
  #loader1 {

    position: absolute;
    z-index: 99999;

  }
  #modalLoader {

  position: absolute;
  z-index: 99999;
  width: 50%;

  }
</style>
<style type="text/css">
    img{
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }    

    .modal#myModal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 1500; /* Sit on top */
      padding-top: 100px; /* Location of the box */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    #myModal .modal-content {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;  
      -webkit-animation-name: zoom;
      -webkit-animation-duration: 0.6s;
      animation-name: zoom;
      animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
      from {-webkit-transform:scale(0)} 
      to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
      from {transform:scale(0)} 
      to {transform:scale(1)}
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

    @media only screen and (max-width: 700px){
      .modal-content {
        width: 100%;
      }
    }
    .imgdiv{
      max-width: 200px;
      max-height: inherit;
    }

    .delete, .edit{
      font-size: 15px;
    }
    .fa-edit, .fa-trash-o{
      padding-left: 5px;
    }

    .btn-warning{
      margin-right: 2px;
      color: #fff;
      background-color: #ec971f;
      border-color: #d58512;
    }

    .close{
      font-size: 30px;
      color: #080808;
      opacity: 1;
    }

    /*
      Folders CSS
    */
    .searchBarFolders{
      min-height: 100px;
    }
    .folderTitleClick{
      display: block;
    }

    .folderTitleClick{
      padding: 3px 6px;
      color: #02a502;
    }
    
    .partyUploadsfolderDelete {
      word-break: break-word;
      color: #de460c;
      padding: 3px 6px;
    }

    .partyUploadsfolderEdit {
      word-break: break-word;
      color: #de9817;
      padding: 3px 6px;
      cursor: pointer;
    }
    .searchBar{
      position: absolute;
      top: 10px;
      width: 100%;
    }

    #folderSearchBar{
      padding-left: 30px;
      border-radius: 100px;
      outline: none;
      border: 2px solid #287676;
      width: 50%;
    }

    #iconSearch{
      /* width: 10%; */
      position: absolute;
      font-size: 20px;
      color: #287676;
      margin: 10px;
    }

    .folderNameSpan:hover .tooltiptext{
      visibility: visible;
    }
    .folderContainer>.panel-group>.panel{
      box-shadow: 1px 1px 1px rgba(156, 137, 137, 0.1);
    }

    .titleCaption{
      color: #fff;
      position: relative;
      text-align: center;
      bottom: 45px;
      display: grid;
    }

    .actionButtons{
      display: flex;
      justify-content: center;
    }
    .actionButtons>div{
      width: 5%;
    }

    .fileCreateModalBody>.form-group>.imgUp>.imagePreview{
      background-image: none;
    }

    .imagesView>.col-xs-4{
      padding:2px;
      width: max-content;
    }
  </style>

  <style>
    
.gallery {
  display: grid;
  grid-column-gap: 8px;
  grid-row-gap: 8px;
  grid-template-columns: repeat(auto-fill, minmax(225px, 1fr));
  grid-auto-rows: auto;
  align-items: center;
  background-color:  #06c32e0f;
}
.gallery img {
  max-width: 100%;
  border-radius: 8px;
  box-shadow: 0 0 16px #333;
  transition: all 1.5s ease;
}
.gallery img:hover {
  box-shadow: 8px 8px 5px #3333;
}
.gallery .gcontent {
  padding: 4px;
}
.gallery .gallery-item {
  transition: grid-row-start 300ms linear;
  transition: transform 300ms ease;
  transition: all 0.5s ease;
  cursor: pointer;
}
.gallery .gallery-item:hover {
  transform: scale(1.025);
}


@media (max-width: 375px) {
  .gallery {
    grid-template-columns: repeat(auto-fill, minmax(10%, 1fr));
  }
}
@media (max-width: 320px) {
  .gallery {
    grid-template-columns: repeat(auto-fill, minmax(20%, 1fr));
  }
}
@-moz-keyframes zoomin {
  0% {
    max-width: 50%;
    transform: rotate(-30deg);
    filter: blur(4px);
  }
  30% {
    filter: blur(4px);
    transform: rotate(-80deg);
  }
  70% {
    max-width: 50%;
    transform: rotate(45deg);
  }
  100% {
    max-width: 100%;
    transform: rotate(0deg);
  }
}
@-webkit-keyframes zoomin {
  0% {
    max-width: 50%;
    transform: rotate(-30deg);
    filter: blur(4px);
  }
  30% {
    filter: blur(4px);
    transform: rotate(-80deg);
  }
  70% {
    max-width: 50%;
    transform: rotate(45deg);
  }
  100% {
    max-width: 100%;
    transform: rotate(0deg);
  }
}
@-o-keyframes zoomin {
  0% {
    max-width: 50%;
    transform: rotate(-30deg);
    filter: blur(4px);
  }
  30% {
    filter: blur(4px);
    transform: rotate(-80deg);
  }
  70% {
    max-width: 50%;
    transform: rotate(45deg);
  }
  100% {
    max-width: 100%;
    transform: rotate(0deg);
  }
}
@keyframes zoomin {
  0% {
    max-width: 50%;
    transform: rotate(-30deg);
    filter: blur(4px);
  }
  30% {
    filter: blur(4px);
    transform: rotate(-80deg);
  }
  70% {
    max-width: 50%;
    transform: rotate(45deg);
  }
  100% {
    max-width: 100%;
    transform: rotate(0deg);
  }
}

  .round{
    color: #fff;
  }

  .multiselect-container.dropdown-menu{
    position: initial;
  }

  </style>
<script type="text/javascript">
  $(document).ready(function(){
    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if(activeTab){
        $('#clienttabs a[href="' + activeTab + '"]').tab('show');
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

@section('content')
<section class="content">
  <div class="box box-default" style="border-top: none;">
    <div class="row">
      <div class="col-xs-1">
        @if(isset($client->image_path))
          <img id="clientImage" src="{{ URL::asset('cms'.$client->image_path) }}" class="emp-show-profile-gotpic display-imglists" alt="{{$client->company_name}}"> 
        @else
        <div class="cimage"> 
          <img id="clientImage" class="emp-show-profile-pic display-imglists" src="{{ URL::asset('cms/storage/app/public/uploads/nopartyimage.png') }}" alt="User profile picture">
        </div>
        @endif
      </div>
      <div class="col-xs-10">
        <span class="text-delta">
          <h4 id="partytitle" style="line-height:3;margin-left: 10px;">{{ucfirst($client->company_name)}}</h4>
        </span>
      </div>
      <div class="col-xs-1">
        <div class="box-tools pull-right" style="margin-top: 10px;">
          <div class="col-xs-7 page-action text-right">
            <a href=" {{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <!-- /.col -->
      <div class="col-xs-12">
        <div id="loader1" hidden>
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
        <div class="nav-tabs-custom client-tab" id="clienttabs">
          <ul class="nav nav-pills" id="tabs">
            @if($details_tab_view)
              <li class="active"><a href="#details" data-toggle="tab">DETAILS</a></li>
            @endif
            @if(hasChild($client->id))
            <li @if(!$details_tab_view) class="active" @endif><a href="#parties" data-toggle="tab">PARTIES</a></li>
            @endif
            @if(config('settings.order_with_amt')==0 && Auth::user()->can('Accounting-view') && config('settings.accounting')==1 && $enabledAccounting)
            <li><a href="#accounting" data-toggle="tab">ACCOUNTING</a></li>
            @endif
            @if(Auth::user()->can('order-view') && config('settings.orders')==1)
            <li><a href="#orders" data-toggle="tab">ORDERS</a></li>
            @endif
            @if(Auth::user()->can('order-view') && config('settings.orders')==1 && isFirstLevelParty($client->client_type))
            <li><a href="#orders-received" data-toggle="tab">ORDERS RECEIVED</a></li>
            @endif
            @if(Auth::user()->can('zeroorder-view') && config('settings.zero_orders')==1)
            <li><a href="#zeroorders" data-toggle="tab">ZERO ORDERS</a></li>
            @endif
            @if(Auth::user()->can('collection-view') && config('settings.collections')==1)
            <li><a href="#collections" data-toggle="tab">COLLECTIONS</a></li>
            @endif
            @if(Auth::user()->can('activity-view') && config('settings.activities')==1)
            <li><a href="#activities" data-toggle="tab">ACTIVITIES</a></li>
            @endif
            <!--<li><a href="#balance" data-toggle="tab">Balance</a></li>-->
            @if(Auth::user()->can('expense-view') && config('settings.expenses')==1)
            <li><a href="#expenses" data-toggle="tab">EXPENSES</a></li>
            @endif
            {{-- <li><a href="#meetings" data-toggle="tab">Meetings</a></li> --}}
            @if(Auth::user()->can('note-view') && config('settings.notes')==1)
            <li><a href="#notes" data-toggle="tab">NOTES</a></li>
            @endif
            @if(Auth::user()->can('PartyVisit-view') && config('settings.visit_module')==1)
            <li><a href="#visit" data-toggle="tab">VISITS</a></li>
            @endif
            {{-- <li><a href="#reminder" data-toggle="tab">Reminder</a></li> --}}
            @if(config('settings.party_files')==1)
              @if(Auth::user()->can('fileuploads-view'))
              <li><a href="#filesuploadstab" data-toggle="tab">FILES</a></li>
              @endif
            @endif
            @if(config('settings.party_images')==1)
              @if(Auth::user()->can('imageuploads-view'))
              <li><a href="#imagesuploadstab" data-toggle="tab">IMAGES</a></li>
              @endif
            @endif
            @if(config('settings.analytics')==1)
            <li><a href="#summary" data-toggle="tab">SUMMARY</a></li>
            @endif
            <li><a href="#handles" data-toggle="tab">ACCESSIBLE BY</a></li>
          </ul>
          <div class="tab-content">
            @if($details_tab_view)
            <div class="active tab-pane" id="details">
              <ul class="nav nav-tabs" id="subtabs">
                <li class="active"><a href="#general-info-tab" name="general-info-tab" data-toggle="tab">Basic Details</a></li>
                <li><a href="#business-tab" name="business-tab" data-toggle="tab">Business Details</a></li>
                <li><a href="#contact-tab" name="contact-tab" data-toggle="tab">Contact Details</a></li>               
                <li><a href="#location_tab" name="location_tab" data-toggle="tab">Location Details</a></li>
                @if(config('settings.accounting')==1 && Auth::user()->can('Accounting-view') && $enabledAccounting)
                <li><a href="#accounting-tab" name="accounting-tab" data-toggle="tab">Accounting</a></li>
                @endif
                <li><a href="#miscellaneous-tab" name="miscellaneous-tab" data-toggle="tab">Miscellaneous</a></li>  
                @if(!($custom_fields->isEmpty()))
                <li><a href="#customfield-tab" name="customfield-tab" data-toggle="tab">Custom Fields</a></li>  
                @endif
              </ul>
              <div class="tab-content">
                <div class="active tab-pane" id="general-info-tab">
                  @include('company.clients.partials_show.basic')
                </div>
                <div class="tab-pane" id="business-tab">
                  @include('company.clients.partials_show.business')
                </div>
                <div class="tab-pane" id="contact-tab">
                  @include('company.clients.partials_show.contact')
                </div>
                <div class="tab-pane" id="location_tab">
                  @include('company.clients.partials_show.location')
                </div>
                @if(config('settings.accounting')==1 && Auth::user()->can('Accounting-view') && $enabledAccounting)
                <div class="tab-pane" id="accounting-tab">
                  @include('company.clients.partials_show.accounting')
                </div>
                @endif
                <div class="tab-pane" id="miscellaneous-tab">
                  @include('company.clients.partials_show.misc')
                </div>
                @if(!($custom_fields->isEmpty()))
                <div class="tab-pane" id="customfield-tab">
                  @include('company.clients.partials_show.custom')
                </div>
                @endif
              </div>
              <!-- include('company.clients.partials_show.details') -->
            </div>
            @endif
            @if(hasChild($client->id))
            <div class="@if(!$details_tab_view) active tab-pane @else tab-pane @endif" id="parties">
              @include('company.clients.partials_show.parties')
            </div>
            @endif
            @if(config('settings.order_with_amt')==0 && $enabledAccounting)
            <div class="@if(!$details_tab_view && !hasChild($client->id)) active tab-pane @else tab-pane @endif" id="accounting">
              @include('company.clients.partials_show.account')
            </div>
            @endif
            @if(Auth::user()->can('order-view') && config('settings.orders')==1)
            <div class="tab-pane" id="orders">
              @include('company.clients.partials_show.orders')
            </div>
            @endif
            
            @if(Auth::user()->can('order-view') && config('settings.orders')==1 && isFirstLevelParty($client->client_type))
            <div class="tab-pane" id="orders-received">
              @include('company.clients.partials_show.orders_received')
            </div>
            @endif

            @if(Auth::user()->can('zeroorder-view') && config('settings.zero_orders')==1)
            <div class="tab-pane" id="zeroorders">
              @include('company.clients.partials_show.zero_orders')
            </div>
            @endif
            @if(Auth::user()->can('collection-view') && config('settings.collections')==1)
            <div class="tab-pane" id="collections">
              @include('company.clients.partials_show.collections')
            </div>
            @endif
            <div class="tab-pane" id="balance">
              @include('company.clients.partials_show.balance')
            </div>
            @if(Auth::user()->can('activity-view') && config('settings.activities')==1)
            <div class="tab-pane" id="activities">
              @include('company.clients.partials_show.activities')
            </div>
            @endif
            @if(Auth::user()->can('expense-view') && config('settings.expenses')==1)
            <div class="tab-pane" id="expenses">
              @include('company.clients.partials_show.expenses')
            </div>
            @endif
            {{-- <div class="tab-pane" id="meetings">
              @include('company.clients.partials_show.meetings')
            </div> --}}
            @if(Auth::user()->can('note-view') && config('settings.notes')==1)
            <div class="tab-pane" id="notes">
              @include('company.clients.partials_show.notes')
            </div>
            @endif
            @if(Auth::user()->can('PartyVisit-view') && config('settings.visit_module')==1)
            <div class="tab-pane" id="visit">
              @include('company.clients.visit')
            </div>
            @endif
            <!-- /.tab-pane -->
            {{-- <div class="tab-pane" id="reminder">
              @include('company.clients.partials_show.reminder')
            </div> --}}
            <div id="modalLoader" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" style="margin: auto 50%;" />
            </div>
            @if(config('settings.party_files')==1)
              @if(Auth::user()->can('fileuploads-view'))
              <div class="tab-pane" id="filesuploadstab">
                @include('company.clients.partials_show.fileuploads')
              </div>
              @endif
            @endif
            @if(config('settings.party_images')==1)
              @if(Auth::user()->can('imageuploads-view'))
              <div class="tab-pane" id="imagesuploadstab">
                @include('company.clients.partials_show.imageuploads')
              </div>
              @endif
            @endif
            @if(config('settings.analytics')==1)
            <div class="tab-pane" id="summary">
              @include('company.clients.partials_show.summary')
            </div>
            @endif
            <div class="tab-pane" id="handles">
              @include('company.clients.partials_show.handles')
            </div>
            <!-- /.tab-pane -->
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
    </div>
  </div>
  <!-- /.row -->

  <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-keyboard="false" data-backdrop="static">

    <div class="modal-dialog" role="document">

      <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>

          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>

        </div>

        <form method="post" class="remove-record-model">

          {{method_field('delete')}}

          {{csrf_field()}}

          <div class="modal-body">

            <p class="text-center">

              Are you sure you want to delete this?

            </p>

            <input type="hidden" name="client_id" id="c_id" value="">
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

  <div id="myOrderModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" id="changeDeliveryStatus" method="POST"
            action="{{URL::to('admin/order/changeDeliveryStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="order_id" id="order_id" value="">
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Status</label>
              <div class="col-sm-10">
                <select class="form-control" id="delivery_status" name="delivery_status" required="true">
                  @if(getClientSetting()->order_approval==0)
                  @foreach($orderStatus as $orderSts)
                  <option value="{{$orderSts->id}}">{{$orderSts->title}}</option>
                  @endforeach
                  @else
                  @foreach($orderStatus as $orderSts)
                  <option value="{{$orderSts->id}}">{{$orderSts->title}}</option>
                  @endforeach
                  @endif
                </select>
              </div>
            </div>
            @if(getClientSetting()->order_approval==1)
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Dispatch Date</label>
              <div class="col-sm-10">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  @if(config('settings.ncal')==0)
                  {!! Form::text('delivery_date', null, ['class' => 'form-control pull-right', 'id' =>
                  'delivery_datenew',
                  'autocomplete'=>'off', 'placeholder' => 'Start Date','required']) !!}
                  @else
                  <input type="text" autocomplete="off" class="form-control pull-right" id="delivery_ndate"
                    placeholder="Dispatch Date" required />
                  <input type="text" id="delivery_edate" name="delivery_date" hidden />
                  @endif
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Dispatch Place</label>
              <div class="col-sm-10">
                {!! Form::text('delivery_place', null, ['class' => 'form-control', 'id=delivery_place', 'placeholder' =>
                'Delivery Place']) !!}
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Transport Name</label>
              <div class="col-sm-10">
                {!! Form::text('transport_name', null, ['class' => 'form-control', 'id=transport_name', 'placeholder' =>
                'Transport Name']) !!}
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Transport Number</label>
              <div class="col-sm-10">
                {!! Form::text('transport_number', null, ['class' => 'form-control', 'id=transport_number',
                'placeholder'
                => 'Transport Number']) !!}
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="name"> Bilty Number</label>
              <div class="col-sm-10">
                {!! Form::text('billty_number', null, ['class' => 'form-control', 'id=billty_number', 'placeholder' =>
                'Bilty Number']) !!}
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2" for="name">Dispatch Note</label>
              <div class="col-sm-10">
                {!! Form::textarea('delivery_note', null, ['class' => 'form-control', 'rows="5"', 'id=delivery_note',
                'placeholder' => 'Delivery Notes']) !!}
              </div>
            </div>
            @endif
            <div class="modal-footer">
              <button id="btn_status_change" type="submit" class="btn actionBtn">
                <span id="footer_action_button" class='glyphicon'> </span> Change
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button>
            </div>


          </form>
        </div>
      </div>
    </div>
  </div>

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
            Sorry! You are not authorized to update the status for the selected record.
          </p>
          <input type="hidden" name="expense_id" id="c_id" value="">
          <input type="text" id="accountType" name="account_type" hidden />
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal modal-default fade" id="alertUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
            Sorry! You are not authorized to view this user details.
          </p>
          <input type="hidden" name="expense_id" id="c_id" value="">
          <input type="text" id="accountType" name="account_type" hidden />
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-default fade" id="alertClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
          Sorry! You are not authorized to view this party details.
        </p>
      </div>
      <div class="modal-footer">
        {{-- <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
  </div>
</div>

  <div id="myModal" class="modal custommodal">
    <div class="titleCaption"></div>
    <span class="close zoom-close">&times;</span>
    <div style="display: flex;">
      <div href="#" class="previous round" style="cursor:pointer;font-size: 168px;margin: 50px 0;">‹</div>;
      <img class="modal-content zoom-modal-content" id="img01" style="bottom: 30px;">
      <div href="#" class="next round" style="cursor:pointer;font-size: 168px;margin: 50px 0;">›</div>;
    </div> 
    <div class="actionButtons"></div>
    <div id="caption"></div>
  </div>

  <form method="post" action="{{domain_route('company.admin.client.show.pdfexports', [$client->id])}}"
    class="pdf-export-form hidden" id="pdf-generate">
    {{csrf_field()}}
    <input type="text" name="exportedData" class="exportedData" id="exportedData">
    <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
    <input type="text" name="moduleName" class="moduleName" id="moduleName">
    <input type="text" name="columns" class="columns" id="columns">
    <input type="text" name="properties" class="properties" id="properties">
    <button type="submit" id="genrate-pdf">Generate PDF</button>
  </form>

</section>
<div id="myModal" class="modal" class="imgModal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
  </div>
@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{asset('assets/plugins/zoomImage/zoomer.js')}}"></script>
@yield('analytics-scripts')
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
<script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif

<script>
  var geocoder;
      var map;
      var marker;

      /*
       * Google Map with marker
       */
      function initialize() {
          var initialLat = $('#search_latitude').val();
          var initialLong = $('#search_longitude').val();
          var focusLat = $('#search_latitude').val();
          var focusLong = $('#search_longitude').val();
          initialLat = initialLat ? initialLat : {{config('settings.latitude')}};
          initialLong = initialLong ? initialLong : {{config('settings.longitude')}};

          var latlng = new google.maps.LatLng(initialLat, initialLong);
          var options = {
              zoom: 16,
              center: latlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
          };

          map = new google.maps.Map(document.getElementById("geomap"), options);

          geocoder = new google.maps.Geocoder();

          marker = new google.maps.Marker({
              map: map,
              draggable: true,
              position: latlng
          });

          map.addListener('click', function(event) {
            addMarker(event.latLng);
            marker.setVisible(true);
          });

          function addMarker(location) {
            marker.setPosition(new google.maps.LatLng(location.lat(), location.lng()));
            var point = marker.getPosition();
            map.panTo(point);
            geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    marker.setPosition(results[0].geometry.location);
                    $('#search_addr').val(results[0].formatted_address);
                    $('#search_latitude').val(marker.getPosition().lat());
                    $('#search_longitude').val(marker.getPosition().lng());
                }
            }); 
          }

          google.maps.event.addListener(marker, "dragend", function () {
              var point = marker.getPosition();
              map.panTo(point);
              geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
                  if (status == google.maps.GeocoderStatus.OK) {
                      map.setCenter(results[0].geometry.location);
                      marker.setPosition(results[0].geometry.location);
                      $('#search_addr').val(results[0].formatted_address);
                      $('#search_latitude').val(marker.getPosition().lat());
                      $('#search_longitude').val(marker.getPosition().lng());
                  }
              });
          });

          if(focusLat == "" || focusLong == ""){
            marker.setVisible(false);
          }

      }

      $(document).ready(function () {
          //load google map
          initialize();

          /*
           * autocomplete location search
           */

          // autocomplete location search
          const autocomplete = new google.maps.places.Autocomplete(document.getElementById('search_addr'));
          // Bind the map's bounds (viewport) property to the autocomplete object,
          // so that the autocomplete requests use the current map bounds for the
          // bounds option in the request.
          autocomplete.bindTo("bounds", map);
          const infowindow = new google.maps.InfoWindow();
          const infowindowContent = document.getElementById("infowindow-content");
          infowindow.setContent(infowindowContent);
          autocomplete.addListener("place_changed", () => {
            infowindow.close();
            marker.setVisible(false);
            const place = autocomplete.getPlace();

            $('#search_addr').val(place.name);
            $('#search_latitude').val(place.geometry.location.lat());
            $('#search_longitude').val(place.geometry.location.lng());
            var latlng = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
            marker.setPosition(latlng);
            initialize();
          });

          // var PostCodeid = '#search_addr';
          // $(function () {
          //     $(PostCodeid).autocomplete({
          //         source: function (request, response) {
          //             geocoder.geocode({
          //                 'address': request.term
          //             }, function (results, status) {
          //                 response($.map(results, function (item) {
          //                     return {
          //                         label: item.formatted_address,
          //                         value: item.formatted_address,
          //                         lat: item.geometry.location.lat(),
          //                         lon: item.geometry.location.lng()
          //                     };
          //                 }));
          //             });
          //         },
          //         select: function (event, ui) {
          //             $('#search_addr').val(ui.item.value);
          //             $('#search_latitude').val(ui.item.lat);
          //             $('#search_longitude').val(ui.item.lon);
          //             var latlng = new google.maps.LatLng(ui.item.lat, ui.item.lon);
          //             marker.setPosition(latlng);
          //             initialize();
          //         }
          //     });
          // });

          /*
           * Point location on google map
           */
          $('#get_map').click(function (e) {
              var address = $(PostCodeid).val();
              geocoder.geocode({'address': address}, function (results, status) {
                  if (status == google.maps.GeocoderStatus.OK) {
                      map.setCenter(results[0].geometry.location);
                      marker.setPosition(results[0].geometry.location);
                      $('#search_addr').val(results[0].formatted_address);
                      $('#search_latitude').val(marker.getPosition().lat());
                      $('#search_longitude').val(marker.getPosition().lng());
                  } else {
                      alert("Geocode was not successful for the following reason: " + status);
                  }
              });
              e.preventDefault();
          });

          //Add listener to marker for reverse geocoding
          google.maps.event.addListener(marker, 'drag', function () {
              geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
                  if (status == google.maps.GeocoderStatus.OK) {
                      if (results[0]) {
                          $('#search_addr').val(results[0].formatted_address);
                          $('#search_latitude').val(marker.getPosition().lat());
                          $('#search_longitude').val(marker.getPosition().lng());
                      }
                  }
              });
          });
      });


  
</script>	
<script>
  function customExportAction(config, exportData, modName, propertiesArray, colsArray){
    $('#exportedData').val(JSON.stringify(exportData));
    $('#pageTitle').val(config.title);
    $('#moduleName').val(modName);
    $('#columns').val(colsArray);
    $('#properties').val(propertiesArray);
    $('#pdf-generate').submit();
  }
  $('input[name=phone]').keypress(function (e) {
     var regex = new RegExp("^[-0-9,\-\/\+\(\)]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
  });
  $(document).on('click', '#ActivateCancel', function(){
    $(document).find('.errField').remove()
  })
  $(document).on('click', '#ActivateContactCancel', function(){
    $(document).find('.errField').remove()
  })
  $(document).on('click', '#subtabs>li>a', function(){
    $(document).find('.errField').remove()

  })
  function showLoader(){
    $('#mainBox').addClass('box-loader');
    $('#loader1').removeAttr('hidden');
  }

  function hideLoader(){
    $('#mainBox').removeClass('box-loader');
    $('#loader1').attr('hidden', 'hidden');
  }

  function showModalLoader(){
    // $('#mainBox').addClass('box-loader');
    $('#modalLoader').removeAttr('hidden');
  }

  function hideModalLoader(){
    // $('#mainBox').removeClass('box-loader');
    $('#modalLoader').attr('hidden', 'hidden');
  }


  function validateFieldValue(companyName, el, btnId){
    let inpEl = $(el);
    let field = inpEl[0].name
    let formData;
    if(field == "company_name") formData = {"company_name": companyName}
    else formData = {"mobile": companyName}
      $.ajax({
      "url": "{{domain_route('company.admin.client.validateCompanyName')}}",
      "dataType": "json",
      "type": "POST",
      "data":{
          _token: "{{csrf_token()}}",
          ...formData,
          id: "{{$client->id}}",
          field_name: field,
      },
      beforeSend:function(url, data){
        $(inpEl).parent().parent().find('.media-heading').find('.errField').remove()
        $(`#${btnId}`).children().closest('.partyactionbtn').attr('disabled', true);
      },
      success: function(res){
        $(`#${btnId}`).children().closest('.partyactionbtn').attr('disabled', false);
      },
      error:function(xhr){
        $(inpEl).parent().parent().find('.media-heading').find('.errField').remove()
        if(xhr.status==422){
          let msg = xhr.responseJSON.errors.company_name? xhr.responseJSON.errors.company_name[0]: xhr.responseJSON.errors.mobile[0];
          $(inpEl).parent().parent().find('.media-heading').append("&nbsp<span class='errField' style='color:red;font-sze:3px;'>"+ msg +"</span>")
        }
        $(`#${btnId}`).children().closest('.partyactionbtn').attr('disabled', false);
      },
    });
  }
  $(document).on("focusout", 'input[name=company_name]', function(e)  {
    let current = $(this);
    let companyName = current.val();
    if(companyName){
      validateFieldValue(companyName, current, 'ActivateUpdate');
    }
  });
  $(document).on("focusout", 'input[name=mobile]', function(e)  {
      let current = $(this);
      let mobile = current.val();
      if(mobile){
          validateFieldValue(mobile, current, 'ActivateContactUpdate');
      }
  });

  
</script>
@if((config('settings.party_files')==1 || config('settings.party_images')==1) && (Auth::user()->can('fileuploads-view') || Auth::user()->can('imageuploads-view')))
  {{-- <script src="{{asset('assets/pagesjs/partyupload.js') }}"></script> --}}
  @include('company.clients.customjs.partyuploadjs')
@endif
<script>
  const hasChild = "{{hasClientChild($client->id)}}";
  const handledClients = @json($viewable_clients);
  var modal = document.getElementById("myModal");
  var modalImg = document.getElementById("img01");
  $('.display-imglists').on('click',function(){
    $(modal).find(".titleCaption").html("");
    $(modal).find(".actionButtons").html("");
    modal.style.display = "block";
    modalImg.src = this.src;
  });

  $('.close').on('click',function(){
    modal.style.display = "none";
  });

  $('.client_access').click(function(e){
    $('.btn').prop('disabled', true);
    $(this).parent().submit();
  });
  
  $(function(){
        $('#client-type').select2({
          placeholder: "Select Party Type"
        });
        var ck_editor = CKEDITOR.replace('aboutCompany');
        ck_editor.on('change',function(){
          $('#aboutCOM').val(ck_editor.getData());
        });
      });
      var customKey=0;

      $('#subtabs').on('click','li',function(e){
        e.preventDefault();
        $('.text-display').attr('hidden',false);
        $('.text-form').attr('hidden',true);
        $('#ActivateEdit').removeClass('hide');
        $('#ActivateBusinessEdit').removeClass('hide');
        $('#ActivateContactEdit').removeClass('hide');
        $('#ActivateLocationEdit').removeClass('hide');
        $('#ActivateAccountingEdit').removeClass('hide');
        $('#ActivateMiscEdit').removeClass('hide');
        $('#ActivateCustomFieldEdit').removeClass('hide');
        $('#ActivateUpdate').addClass('hide');
        $('#ActivateBusinessUpdate').addClass('hide');
        $('#ActivateContactUpdate').addClass('hide');
        $('#ActivateLocationUpdate').addClass('hide');
        $('#ActivateAccountingUpdate').addClass('hide');
        $('#ActivateMiscUpdate').addClass('hide');
        $('#ActivateCustomFieldUpdate').addClass('hide');
        $('#ActivateCancel').addClass('hide');
        $('#ActivateBusinessCancel').addClass('hide');
        $('#ActivateContactCancel').addClass('hide');
        $('#ActivateLocationCancel').addClass('hide');
        $('#ActivateAccountingCancel').addClass('hide');
        $('#ActivateMiscCancel').addClass('hide');
        $('#ActivateCustomFieldCancel').addClass('hide');
        $('#lblChange').addClass('hide');
      });

      $('#employee_id').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: true,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Add Employees",
        onChange :function(option, checked) {
          if(checked){
            let checkEmp = option.attr('value');
            checkEmployeeSuperior(checkEmp);
          }
          else{
            let uncheckEmp = option.attr('value');
            checkEmployeeJunior(uncheckEmp);
          }
        },
      });
      $('#employee_id2').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: false,
        enableClickableOptGroups: false,
        includeSelectAllOption: true,
        enableCollapsibleOptGroups : true,
        selectAllNumber: false,
        nonSelectedText:"Add Employees",
      });

      function checkEmployeeSuperior(empID) {
          if(empID!=""){
            $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              url: "{{domain_route('company.admin.client.getEmployeeSuperior')}}",
              data: {"employee_id": empID},
              beforeSend: function(e){
                $('#saveEmployeeAssign').attr("disabled",true);
                $('#loader1').removeAttr('hidden');
              },
              success: function (data) {
                if(data.length>=1){
                  $.each( data, function( i, l ){
                    $('#employee_id').multiselect('select', l);
                  });
                }
                $('#saveEmployeeAssign').attr("disabled", false);
                $('#loader1').attr('hidden', 'hidden');
              },
              error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#saveEmployeeAssign').attr("disabled", false);
                $('#loader1').attr('hidden', 'hidden');
                  alert(textStatus);
              }
            });
          }
      }

      function checkEmployeeJunior(empID) {
          if(empID!=""){
            $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              url: "{{domain_route('company.admin.client.getEmployeeJuniors')}}",
              data: {"employee_id": empID},
              beforeSend: function(){
                $('#saveEmployeeAssign').attr("disabled",true);
                $('#loader1').removeAttr('hidden');
              },
              success: function (data) {
                if(data.length>=1){
                  var currentVal = $('#employee_id').val();
                  var juniors = data;
                  let intersection = currentVal.filter(x => juniors.includes(parseInt(x)));
                  if(intersection.length>0){
                    alert("Please untick juniors to remove access of the selected user from this party.");
                    $('#employee_id').multiselect('select', empID);
                  }
                  // $.each( intersection, function( i, l ){
                  //   $('#employee_id').multiselect('deselect', l);
                  // });
                }
                $('#saveEmployeeAssign').attr("disabled", false);
                $('#loader1').attr('hidden', 'hidden');
              },
              error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#saveEmployeeAssign').attr("disabled", false);
                  alert(textStatus);
                  $('#loader1').attr('hidden', 'hidden');
              }
            });
          }
      }

      function bs_input_file() {
        $(".input-file").before(
          function() {
            if ( ! $(this).prev().hasClass('input-ghost') ) {
              var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
              element.attr("name",$(this).attr("name"));
              element.change(function(){
                element.next(element).find('input').val((element.val()).split('\\').pop());
              });
              $(this).find("button.btn-choose").click(function(){
                element.click();
              });
              $(this).find("button.btn-reset").click(function(){
                element.val(null);
                $(this).parents(".input-file").find('input').val('');
              });
              $(this).find('input').css("cursor","pointer");
              $(this).find('input').mousedown(function() {
                $(this).parents('.input-file').prev().click();
                return false;
              });
              return element;
            }
          }
        );
      }
      $(function() {
        bs_input_file();
      });

      $(function() {
          $(document).on("change",".uploadFile", function(e)
          {
              e.preventDefault();
              if(this.files[0].size/1024/1024>2){
                alert('File Size cannot be more than 2MB');
                $(this).val(null);
                return;
              }

              var uploadFile = $(this);
              var files = !!this.files ? this.files : [];
              if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

              if (/^image/.test( files[0].type)){ // only image file
                  $(this).closest(".imgUp").find('.imagePreview').empty();
                  var reader = new FileReader(); // instance of the FileReader
                  reader.readAsDataURL(files[0]); // read the local file
       
                  reader.onloadend = function(){ // set image data as background of div
                  uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url("+this.result+")").addClass('display-imglists').attr('src',this.result);
                  $('#clearImage').removeClass('hide');
                  }
              }else{
                alert('Only jpeg, png file types are accepted.');
                $(this).val(null);
                return;
              }        
          });
      });

      $(document).on('click','.custom_image_remove',function(){
        var deletedField = $(this).data('action');
        var originalField = $(this).data('field');
        var valOriginal = $('#'+originalField+'-original').data('value');
        console.log(valOriginal);
        var valOriginal = valOriginal-1;
        $('#'+originalField+'-original').data('value',valOriginal);
        if($('#'+deletedField).val()==""){
          $('#'+deletedField).val($(this).data('name'));
        }else{
          $('#'+deletedField).val($('#'+deletedField).val()+','+$(this).data('name'));
        }
        $(this).parent().parent().remove();
      });

      $(document).on('change','.custom_field_files',function(e){
          e.preventDefault();
          let oldNumber = $(this).data('value');
          let currentNumber = this.files.length;
          let totalNumber = oldNumber + currentNumber;
          let flag = true;
          // if(totalNumber>3){
          //   alert('Max 3 files allowed');
          //   $(this).val(null);
          //   return;
          // }
          $.each(this.files,function(k,v){
            if(v.size/1024/1024>2){
              flag = false;
            }
          });
          if(flag==false){
            alert('File Size cannot be more than 2MB');
            $(this).val(null);
            return;
          }
      });

      function makeRed(phoneBox){
        phoneBox.css('border-color','red');
      }

      function makeGreen(phoneBox){
        phoneBox.css('border-color','green');
      }

      $(document).on('keyup','.phone_numbers',function(e){
        e.preventDefault();
        let phoneBox  = $(this);
        let val       = $(this).val();
        let length    = val.length;
        let i         = 0;
        let startText = 'red';
        let midText   = 'green';
        let endText   = 'red';
        if((val[0]=='+') || (parseInt(val[0])>=0 && parseInt(val[0])<=9) ){
          startText = 'green';
        }
        if(val[val.length-1]>=0 && val[val.length-1]<=9 ){
          endText ='green';
        }

        for(i=1;i<val.length;i++){
          if(val[i]=='-' || (val[i]>=0 && val[i]<=9) ){
            if(val[i]=='-' && val[i-1]=='-'){
              midText = 'red';
            }
          }else{
            midText = 'red';
          }
        }

        if(startText == 'green' && midText == 'green' && endText == 'green'){
          makeGreen(phoneBox);
            $('.keySubmit').removeAttr('disabled');
        }else{
          if(val.length==0){
            makeGreen(phoneBox);
            $('.keySubmit').removeAttr('disabled');
          }else{
            $('.keySubmit').attr('disabled','true');
            makeRed(phoneBox);
          }
        }
      });
      
      // $('#employee_id2').multiselect({
      //     columns: 1,
      //     placeholder: 'Add Employees',
      //     search: true,
      //     selectAll: true
      // });

      @if(!(Auth::user()->isCompanyManager()) || !(Auth::user()->isCompanyAdmin()))
        $('#ms-list-1').find('input[type="checkbox"]').prop('disabled',true);
        $('#ms-list-2').find('input[type="checkbox"]').prop('disabled',true);
      @endif
      $('.select2').select2();
      $('.business_id').select2({
        "placeholder": "Select Business Type"
      });
      $('#order').css("width","100%");
      $('#collection').css("width","100%");
      $('#expense').css("width","100%");

      $(function () {

          $('#delete').on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget)
              var mid = button.data('mid')
              var url = button.data('url');
              // $(".remove-record-model").attr("action",url);
              $(".remove-record-model").attr("action", url);
              var modal = $(this)
              modal.find('.modal-body #m_id').val(mid);
          })
      });

      $('#accessible_by').dataTable({
        "dom": "<'row'<'col-xs-6'l><'col-xs-6'f>>" +
          "<'row'<'col-xs-6'><'col-xs-6'>>" +
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
      });
      $('#link_accessibility').dataTable({
        "dom": "<'row'<'col-xs-6'l><'col-xs-6'f>>" +
          "<'row'<'col-xs-6'><'col-xs-6'>>" +
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
      });

      $('#changeDeliveryStatus').on('submit',function(){
        $('.actionBtn').attr('disabled',true);
      });

      @if(config('settings.order_with_amt')==0)
        var columns = [{ "data": "id" },
        { "data": "orderno" },
        { "data": "orderdate" },
        { "data": "empName" },
        { "data": "grandtotal" },
        { "data": "delivery_status" },
        { "data": "action" }];
      @else
        var columns = [{ "data": "id" },
        { "data": "orderno" },
        { "data": "orderdate" },
        { "data": "empName" },
        { "data": "delivery_status" },
        { "data": "action" }];
      @endif

      @if(Auth::user()->can('order-view'))
      function initializeODT(clientID){
        ordertable = $('#order').DataTable({
          "stateSave": true,
          "stateSaveParams": function (settings, data) {
          data.search.search = "";
          },
          "order": [[ 2, "desc" ]],
          "serverSide": true,
          "processing": false,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
            {
              "orderable": false,
              "targets":-1,
            },],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Order List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Order List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Order List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.client.partyOrdersTable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              clientID:clientID,
            },
            beforeSend:function(){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
            },
            error:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            },
            complete:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": columns,
          @if(config('settings.order_with_amt')==0)
            drawCallback:function(settings)
            {
              $('#grandTotalAmount').html('<b>Total Orders: '+settings.json.total+'</b>');
            }
          @endif
        });
        ordertable.buttons().container()
            .appendTo('#orderexports');
        var orderOldExportAction = function (self, e, dt, button, config) {
          if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
            if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
          }
        };

        var orderNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$ordersCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].empName = $(settings.json.data[key].empName)[0].textContent;
                  settings.json.data[key].delivery_status = $(settings.json.data[key].delivery_status)[0].textContent; 
                });
                properties = JSON.stringify(["id", "orderno", "orderdate", "empName", "grandtotal", "delivery_status"]);
                columns = JSON.stringify(["S.No.", "Order No.", "Order Date", "Employee Name", "Grand Total", "Order Status"]);customExportAction(config, settings.json.data, 'client-order', properties, columns);
              }else{
                orderOldExportAction(self, e, dt, button, config);
              }
              // orderOldExportAction(self, e, dt, button, config);
              dt.one('preXhr', function (e, s, data) {
                  settings._iDisplayStart = oldStart;
                  data.start = oldStart;
                  $('#mainBox').removeClass('box-loader');
                  $('#loader1').attr('hidden', 'hidden');
              });
              setTimeout(dt.ajax.reload, 0);
              return false;
            });
          });
          dt.ajax.reload();
        }

        var orderReceivedNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$ordersCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].empName = $(settings.json.data[key].empName)[0].textContent;
                  settings.json.data[key].partyName = $(settings.json.data[key].partyName)[0].textContent;
                  settings.json.data[key].delivery_status = $(settings.json.data[key].delivery_status)[0].textContent; 
                });
                properties = JSON.stringify(["id", "orderno", "partyname", "orderdate", "empName", "grandtotal", "delivery_status"]);
                columns = JSON.stringify(["S.No.", "Order No.", "Party Name", "Order Date", "Employee Name", "Grand Total", "Order Status"]);customExportAction(config, settings.json.data, 'client-order', properties, columns);
              }else{
                orderOldExportAction(self, e, dt, button, config);
              }
              // orderOldExportAction(self, e, dt, button, config);
              dt.one('preXhr', function (e, s, data) {
                  settings._iDisplayStart = oldStart;
                  data.start = oldStart;
                  $('#mainBox').removeClass('box-loader');
                  $('#loader1').attr('hidden', 'hidden');
              });
              setTimeout(dt.ajax.reload, 0);
              return false;
            });
          });
          dt.ajax.reload();
        }
      }
      var clientID = '{{$client->id}}';
      initializeODT(clientID)

      @if(config('settings.order_with_amt')==0)
        var odtrcolumns = [{ "data": "id" },
        { "data": "orderno" },
        { "data": "partyName" },
        { "data": "orderdate" },
        { "data": "empName" },
        { "data": "grandtotal" },
        { "data": "delivery_status" },
        { "data": "action" }];
      @else
        var odtrcolumns = [{ "data": "id" },
        { "data": "orderno" },
        { "data": "partyName" },
        { "data": "orderdate" },
        { "data": "empName" },
        { "data": "delivery_status" },
        { "data": "action" }];
      @endif

      function initializeODTR(clientID){
        ordertable = $('#orderReceived').DataTable({
          "stateSave": true,
          "stateSaveParams": function (settings, data) {
          data.search.search = "";
          },
          "order": [[ 2, "desc" ]],
          "serverSide": true,
          "processing": false,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
            {
              "orderable": false,
              "targets":-1,
            },],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Order Received List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderReceivedNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Order Received List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderReceivedNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Order Received List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4,5],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderReceivedNewExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.client.partyOrdersReceivedTable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              clientID:clientID,
            },
            beforeSend:function(){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
            },
            error:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            },
            complete:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": odtrcolumns,
          @if(config('settings.order_with_amt')==0)
            drawCallback:function(settings)
            {
              $('#grandTotalAmountReceived').html('<b>Total Orders: '+settings.json.total+'</b>');
            }
          @endif
        });
        ordertable.buttons().container()
            .appendTo('#orderexportsReceived');
        var orderReceivedOldExportAction = function (self, e, dt, button, config) {
          if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
            if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
          }
        };
        var orderReceivedNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$ordersCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].empName = $(settings.json.data[key].empName)[0].textContent;
                  settings.json.data[key].partyName = $(settings.json.data[key].partyName)[0].textContent;
                  settings.json.data[key].delivery_status = $(settings.json.data[key].delivery_status)[0].textContent; 
                });
                properties = JSON.stringify(["id", "orderno", "partyname", "orderdate", "empName", "grandtotal", "delivery_status"]);
                columns = JSON.stringify(["S.No.", "Order No.", "Party Name", "Order Date", "Employee Name", "Grand Total", "Order Status"]);customExportAction(config, settings.json.data, 'client-order', properties, columns);
              }else{
                orderReceivedOldExportAction(self, e, dt, button, config);
              }
              // orderOldExportAction(self, e, dt, button, config);
              dt.one('preXhr', function (e, s, data) {
                  settings._iDisplayStart = oldStart;
                  data.start = oldStart;
                  $('#mainBox').removeClass('box-loader');
                  $('#loader1').attr('hidden', 'hidden');
              });
              setTimeout(dt.ajax.reload, 0);
              return false;
            });
          });
          dt.ajax.reload();
        }
      }
      initializeODTR(clientID);

      @endif
      $(document).on("click", ".empLinks", function(e){
        if($(this).data('viewable')==""){
          e.preventDefault();
          $('#alertUserModal').modal('show');
          // $('#alertModalText').html('Sorry! You are not authorized to view this user details.');
        }
      });

      $(document).on("click", ".clientLinks", function(e){
        if($(this).data('viewable')==""){
          e.preventDefault();
          $('#alertClientModal').modal('show');
        }
      });

      @if(config('settings.category_wise_rate_setup') == 1)
    var checkedCategoryId = @json($current_categoryid)

    $('#categoryRates').multiselect({
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      enableFullValueFiltering: false,
      enableClickableOptGroups: false,
      includeSelectAllOption: false,
      enableCollapsibleOptGroups : true,
      selectAllNumber: false,
      numberDisplayed: 1,
      nonSelectedText:"Select Category Rates",
      allSelectedText:"All Selected",
      onChange:function(element, isChecked){
        let categoryId = element.data("categoryid")
        if(isChecked){
          // if($.inArray(categoryId, checkedCategoryId) == -1){ 
          //   checkedCategoryId = [...checkedCategoryId, categoryId]
          // }
          // else{ 
          //   $('#categoryRates').multiselect('deselect', [element.val()])
          //   alert("Cannot apply multiple rates of same categories."); 
          // }
          let arr = {}
          $('#categoryRates').multiselect('deselect', checkedCategoryId[categoryId])
          arr[categoryId] = element.val()
          checkedCategoryId = {...checkedCategoryId, ...arr}
          
          $('#categoryRates').multiselect('select', element.val())
        }else{
          // checkedCategoryId = checkedCategoryId.filter((el, ind) => el != categoryId)
          delete checkedCategoryId[categoryId]
        }
      }
    });
    $('#categoryRates').multiselect('select', @json($current_category_rates_id))
    @endif


      @if(Auth::user()->can('zeroorder-view'))
      var zeroOrderColumns = [
                            {"data" : "id"},
                            // {"data" : "contact_person"},
                            // {"data" : "party_type"},
                            // {"data" : "contact_number"},
                            // {"data" : "address"},
                            {"data" : "date"},
                            {"data" : "remark"},
                            {"data" : "employee_name"},
                            {"data": "action"}
                            ];
      function initializeZODT(clientID){
        ordertable = $('#zero_order').DataTable({
          "stateSave": true,
          "stateSaveParams": function (settings, data) {
          data.search.search = "";
          },
          "order": [[ 1, "desc" ]],
          "serverSide": true,
          "processing": false,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
              { "width": "5%", "targets": 0 },
              { "width": "15%", "targets": 1 },
              { "width": "60%", "targets": 2 },
              { "width": "20%", "targets": 3 },
          ],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Zero Order List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Zero Order List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Zero Order List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                orderNewExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.client.partyZeroOrdersTable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              clientID:clientID,
            },
            beforeSend:function(){
              $('#mainBox').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
            },
            error:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            },
            complete:function(){
              $('#mainBox').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": zeroOrderColumns,
        });
        ordertable.buttons().container()
            .appendTo('#zeroOrderexports');
        var orderOldExportAction = function (self, e, dt, button, config) {
          if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
            if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
          }
        };

        var orderNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$ordersCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].employee_name = $(settings.json.data[key].employee_name)[0].textContent;
                });
                properties = JSON.stringify(["id", "date", "remark", "employee_name"]);
                columns = JSON.stringify(["S.No.", "Date", "Remark", "Salesman"]);
                customExportAction(config, settings.json.data, 'client-noorder', properties, columns);
              }else{
                orderOldExportAction(self, e, dt, button, config);
              }
              // orderOldExportAction(self, e, dt, button, config);
              dt.one('preXhr', function (e, s, data) {
                  settings._iDisplayStart = oldStart;
                  data.start = oldStart;
                  $('#mainBox').removeClass('box-loader');
                  $('#loader1').attr('hidden', 'hidden');
              });
              setTimeout(dt.ajax.reload, 0);
              return false;
            });
          });
          dt.ajax.reload();
        }
      }
      var clientid = '{{$client->id}}';
      initializeZODT(clientid);
      $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
        $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
      } );
      @endif

      @if(Auth::user()->can('collection-view'))
      function initializeCDT(clientID){
        var collectiontable = $('#collection').DataTable({
          "language": {
            search: "_INPUT_",
            searchPlaceholder: "Search"
          },
          "stateSave": true,
          "stateSaveParams": function (settings, data) {
          data.search.search = "";
          },
          "order": [[ 1, "desc" ]],
          "serverSide": true,
          "processing": false,
          "paging": true,
          "dom":  "<'row'<'col-xs-6 alignleft'f><'col-xs-6 alignright'B>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
          "columnDefs": [
            {
              orderable: false,
              targets:-1,
            }, 
            { 
              width: 20, 
              targets: [0],
            },
            { 
              width: 100, 
              targets: [-1],
            },
          ],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Collections List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                collectionNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Collections List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                collectionNewExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Collections List of {{$client->company_name}}', 
              exportOptions: {
                columns: [0,1,2,3,4],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                collectionNewExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.client.partyCollectionTable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              clientID : clientID,
            },
            beforeSend:function(){
              $('#mainBox1').addClass('box-loader');
              $('#loader1').removeAttr('hidden');
            },
            error:function(){
              $('#mainBox1').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            },
            complete:function(){
              $('#mainBox1').removeClass('box-loader');
              $('#loader1').attr('hidden', 'hidden');
            }
          },
          "columns": [
            {"data" : "id"},
            {"data" : "payment_date"},
            {"data" : "empName"},
            {"data" : "payment_received"},
            {"data" : "payment_method"},
            {"data" : "action"},
          ],
          drawCallback:function(settings)
          {
            $('#grandTotalCAmount').html('<b>Total Collections: '+settings.json.total+'</b>');
          }
        });
        collectiontable.buttons().container()
            .appendTo('#collectionexports');
        var collectionOldExportAction = function (self, e, dt, button, config) {
          if (button[0].className.indexOf('buttons-excel') >= 0) {
            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
            if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
            } else {
                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            }
          } else if (button[0].className.indexOf('buttons-print') >= 0) {
            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
          }
        };

        var collectionNewExportAction = function (e, dt, button, config) {
          var self = this;
          var oldStart = dt.settings()[0]._iDisplayStart;
          dt.one('preXhr', function (e, s, data) {
            $('#mainBox').addClass('box-loader');
            $('#loader1').removeAttr('hidden');
            data.start = 0;
            data.length = {{$collectionsCount}};
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].empName = $(settings.json.data[key].empName)[0].textContent; 
                });
                properties = JSON.stringify(["id", "payment_date", "empName", "payment_received", "payment_method"]);
                columns = JSON.stringify(["S.No.", "Payment Date", "Employee Name", "Payment Received", "Payment Method"]);customExportAction(config, settings.json.data, 'client-collection', properties, columns);
              }else{
                collectionOldExportAction(self, e, dt, button, config);
              }
              // collectionOldExportAction(self, e, dt, button, config);
              dt.one('preXhr', function (e, s, data) {
                  settings._iDisplayStart = oldStart;
                  data.start = oldStart;
                  $('#mainBox').removeClass('box-loader');
                  $('#loader1').attr('hidden', 'hidden');
              });
              setTimeout(dt.ajax.reload, 0);
              return false;
            });
          });
          dt.ajax.reload();
        }
      };
      initializeCDT("{{$client->id}}");
      @endif

      @if(Auth::user()->can('expense-view'))
      @if( !$expenses->isEmpty() )
          var table = $('#expense').DataTable({
              "columnDefs": [ {
                "targets": 6,
                "orderable": false
              }],
              dom:  "<f>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'li><'col-sm-7'p>>",
              stateSave: true,
              "stateSaveParams": function (settings, data) {
              data.search.search = "";
              },
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Expense List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Expense List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      },
                      action: function ( e, dt, node, config ) {
                        expenseNewExportAction( e, dt, node, config );
                      },
                  },
                  {
                      extend: 'print',
                      title: 'Expense List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      }
                  },
              ],
              footerCallback: function (row, data, start, end, display) {
                  var api = this.api(), data;

                  // Remove the formatting to get integer data for summation
                  var intVal = function (i) {
                      return typeof i === 'string' ?
                          i.replace(/[\$,]/g, '') * 1 :
                          typeof i === 'number' ?
                              i : 0;
                  };

                  // Total over all pages
                  total = api
                      .column(7)
                      .data()
                      .reduce(function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0);

                  // Total over this page
                  pageTotal = api
                      .column( 7,{ search: 'applied',page:'all'})
                      .data()
                      .reduce(function (a, b) {
                          return intVal(a) + intVal(b);
                      }, 0);

                  // Update footer
                  $('#grandTotalEAmount').html(
                      '<b>Total Expenses: '+"{{ config('settings.currency_symbol')}} " + (pageTotal).toLocaleString("en")+'</b>'
                  );
              }
          });

          table.buttons().container()
              .appendTo('#expenseexports');

        var expenseNewExportAction = function (e, dt, button, config) {
          var self = this;
          var data = [];
          var count = 0;
          table.rows({"search":"applied" }).every( function () {
            var row = {};
            row["id"] = ++count;
            row["date"] = this.data()[1];
            row["amount"] = this.data()[2];
            row["added_by"] = this.data()[3].replace(/<[^>]+>/g, '').trim();
            row["approved_by"] = this.data()[4].replace(/<[^>]+>/g, '').trim();
            row["status"] = this.data()[5].replace(/<[^>]+>/g, '').trim();
            data.push(row);
          });
          properties = JSON.stringify(["id", "date", "amount", "added_by", "approved_by", "status"]);
          columns = JSON.stringify(["S.No.", "Date", "Amount", "Added By", "Approved/Cancelled By", "Status"]);
          customExportAction(config, data, 'employee-activities', properties, columns);
        };
      @endif
      @endif

      @if(Auth::user()->can('note-view'))
      @if( !$meetings->isEmpty() )
          var notestable = $('#notesTable').DataTable({
              "columnDefs": [ {
                "targets": 5,
                "orderable": false
              }],
              dom:  "<f>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'li><'col-sm-7'p>>",
              stateSave: true,
              "stateSaveParams": function (settings, data) {
              data.search.search = "";
              },
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Notes List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4]
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Notes List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4]
                      },
                      action: function ( e, dt, node, config ) {
                        notesNewExportAction( e, dt, node, config );
                      },
                  },
                  {
                      extend: 'print',
                      title: 'Notes List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4]
                      }
                  },
              ],
          });

          notestable.buttons().container()
              .appendTo('#notesexports');
        var notesNewExportAction = function (e, dt, button, config) {
          var self = this;
          var data = [];
          var count = 0;
          notestable.rows({"search":"applied" }).every( function () {
            var row = {};
            row["id"] = ++count;
            row["notes"] = this.data()[1];
            row["time"] = this.data()[2];
            row["date"] = this.data()[3];
            row["salesman"] = this.data()[4].replace(/<[^>]+>/g, '').trim();
            data.push(row);
          });
          properties = JSON.stringify(["id", "notes", "time", "date", "salesman"]);
          columns = JSON.stringify(["S.No.", "Notes", "Time", "Date", "Salesman"]);
          customExportAction(config, data, 'employee-notes', properties, columns);
        };
      @endif
      @endif

      @if(Auth::user()->can('activity-view'))
      @if( $activities->count()>0 )
      $(document).ready(function () {
          var table = $('#activity').DataTable({
              "columnDefs": [ {
                "targets": 7,
                "orderable": false
              }],
              dom:  "<f>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'li><'col-sm-7'p>>",
              stateSave: true,
              "stateSaveParams": function (settings, data) {
              data.search.search = "";
              },
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: 'Activities List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Activities List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      },
                      action: function ( e, dt, node, config ) {
                        activitiesNewExportAction( e, dt, node, config );
                      },
                  },
                  {
                      extend: 'print',
                      title: 'Activities List of {{$client->company_name}}',
                      exportOptions:{
                        columns: [0,1,2,3,4,5]
                      }
                  },
              ]
          });

          table.buttons().container()
              .appendTo('#activitiesexports');
        var activitiesNewExportAction = function (e, dt, button, config) {
          var self = this;
          var data = [];
          var count = 0;
          table.rows({"search":"applied" }).every( function () {
            var row = {};
            row["id"] = ++count;
            row["date"] = this.data()[1];
            row["title"] = this.data()[2];
            row["type"] = this.data()[3];
            row["assigned_by"] = this.data()[4].replace(/<[^>]+>/g, '').trim();
            row["assigned_to"] = this.data()[5].replace(/<[^>]+>/g, '').trim();
            if($(this.data()[6]).find('input').first().is(":checked")){
              row["complete"] = "Yes";
            }else{
              row["complete"] = "No";  
            }
            data.push(row);
          });
          properties = JSON.stringify(["id", "date", "title", "type", "assigned_by", "assigned_to", "complete"]);
          columns = JSON.stringify(["S.No.", "Date", "Title", "Type", "Assigned By", "Assigned To", "Complete"]);
          customExportAction(config, data, 'client-activities', properties, columns);
        };
      });
      @endif
      @endif
      var employee_list = new Array();

      function empty_list() {
          employee_list = [];
          $('#employee-list').empty();
      }

      function chooseEmployee() {
          var employee_id = $("#employee_id option:selected").val();
          var employee_name = $("#employee_id option:selected").text();
          if (employee_list.includes(employee_id) == false && employee_id != 0) {
              var newentry = "<li id='employee" + employee_id + "'><input name='employee[]' type='hidden' value='" + employee_id + "'>" + employee_name + "<a class='btn btn-danger btn-xs pull-right' style='height:18px;' onclick='popEmployee(" + employee_id + ")'>X</a></li>";
              $("#employee-list").append(newentry);
              employee_list.push(employee_id);
          }
      }

      function popEmployee(id) {
          employee_list = jQuery.grep(employee_list, function (value) {
              return value != id;
          });
          $('#employee' + id).empty();
          $('#employee' + id).remove();
      }

      function addEmployee() {
          // var employee_id = $('option:selected', '#employee_id').val();
          var client_id = {{$client->id}};
          var map_type = 2;
          var csrf_token = "{{ csrf_token() }}";
          var add_url = "{{URL::to('admin/client/addEmployee')}}";

          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              url: add_url,
              data: {"employee_list": employee_list, "client_id": client_id, "map_type": map_type},
              success: function (data) {
                  //debugger;
                  $("#showHandlers").load(" #showHandlers");
                  empty_list();
              },
              error: function (XMLHttpRequest, textStatus, errorThrown) {
                  alert("Employee Already Added");
              }
          });
      }

      function removeEmployee(handle_id, employee_id, client_id) {
          var csrf_token = "{{ csrf_token() }}";
          var remove_url = "{{URL::to('admin/client/removeEmployee')}}";
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              url: remove_url,
              data: {"handle_id": handle_id, "employee_id": employee_id, "client_id": client_id},
              success: function (data) {
                  $("#showHandlers").load(" #showHandlers");
              }
          });
      }

      $('#activity').on('click','.check',function () {
        var id = $(this).val();
        if($(this). prop("checked") == true){
          var checked = true;
          var myaudio = new Audio();
          myaudio.src = "{{asset('assets/plugins/sweetalert2/ting.wav')}}";
          myaudio.play();
        }else{
          var checked = false;
        }
        $(this).prop('disabled',true);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{url(domain_route("company.admin.activities.updateMark"))}}',
            type: "POST",
            data: {
              'csrf_token':'{{csrf_token()}}',
              'id':id,
              'checked':checked,
            },
            success: function (data) {
                if(data['result']==true){
                  if(data['ticked']==true){
                    $('.check_'+id).prop("checked",true);
                  }else{
                    $('.check_'+id).prop("checked",false);
                  }
                }else{
                  if(checked==true){
                    $('.check_'+id).prop("checked",false);
                  }else{
                    $('.check_'+id).prop("checked",true);
                  }
                }
                $('.check_'+id).prop("disabled",false);
            },
            error: function (xhr) {
              $('.check_'+id).prop("disabled",false);
              console.log('internal server error or ajax failed');
            },
            complete: function () {
                //   $('#btnSave').val('Add Activity');
                //  $('#btnSave').removeAttr('disabled');
            }
        });

      });

      $(document).on('click','.no_check',function(){
        if($(this).is(':checked')){
          $(this).prop('checked',false);
        }else{
          $(this).prop('checked',true);
        }
        $('#textComplete').html('complete');
        $('#alertCompleteModal').modal('show');
      });

      $(document).on('click','.no_uncheck',function(){
        if($(this).is(':checked')){
          $(this).prop('checked',false);
        }else{
          $(this).prop('checked',true);
        }
        $('#textComplete').html('incomplete');
        $('#alertCompleteModal').modal('show');
      });

      $(document).ready(function () {
          if(hasChild) $('#client-type').prop("disabled", true);
          var client_type = '{{ $client->client_type }}';
          var client_id = '{{ $client->id }}';
          var superior = @if($client->superior!=NULL){{ $client->superior}}@else <?php echo "' '"; ?> @endif;
          if (client_type) {
              $.ajax({
                  url: '/admin/client/getsuperiorlist',
                  type: "GET",
                    data: {client_type: client_type, cid: client_id},
                     dataType: "json",
                  cache: false,
                  success: function (data) {
                      $("#superior").empty();
                      $('<option></option>').val("").text("{{$companyName}}").appendTo('#superior');
                      if(data.length==0 || hasChild) $("#superior").prop('disabled', true);
                      else $("#superior").prop('disabled', false);
                      $('#cpsuperior').text("{{$companyName}}");
                      $.each(data, function (i, item) {
                          $('<optgroup />').prop('label', i).appendTo('#superior');
                          $.each(item, function (key, value) {
                              if (value.id == superior) {
                                  $('#cpsuperior').text(value.company_name);
                                  $('<option></option>').val(value.id).text(value.company_name).appendTo('#superior').prop('selected', true);
                                  //  $('<option/>').prop('selected', true);

                              } else {
                                if(value.status=="Active") if(value.status=="Active") $('<option></option>').val(value.id).text(value.company_name).appendTo('#superior');
                              }
                          });
                      });

                  }
              });
          } else {
              $('#superior').empty();
          }
      });

      $(function () {

           $('select[name="country"]').on('change', function () {
              var countryId = $(this).val();
              $('#state').append($('<option selected="selected"></option>').html('Loading...'));
              if (countryId) {
                  $.ajax({
                      url: '/get-state-list?country_id=' + countryId,
                      type: "GET",
                      dataType: "json",
                      success: function (data) {
                          $("#state").empty();
                          $('#city').empty();
                          $("#city").append('<option value="">Select a City</option>');
                          $("#state").append('<option value="">Select a State</option>');
                          $.each(data, function (key, value) {
                              $("#state").append('<option value="' + key + '">' + value + '</option>');
                          });
                          $.ajax({
                              url: '/clients/phonecode/get/' + countryId,
                              type: "GET",
                              success: function (data) {
                                  $('#phonecode').val(data);
                              }
                          });
                      }
                  });
              } else {
                  $('#state').empty();
                  $('#city').empty();
                  $('#phonecode').empty();
              }
          });
          $('select[name="state"]').on('change', function () {
              var stateId = $(this).val();
              $('#city').append($('<option selected="selected"></option>').html('Loading...'));
              if (stateId) {
                  $.ajax({
                      url: '/get-city-list?state_id=' + stateId,
                      type: "GET",
                      dataType: "json",
                      success: function (data) {
                          $("#city").empty();
                          $("#city").append('<option value="">Select a City</option>');
                          $.each(data, function (key, value) {
                              $("#city").append('<option value="' + key + '">' + value + '</option>');
                          });
                      }
                  });
              } else {
                  $('#city').empty();
              }
          });

          $('select[name="client_type"]').on('change', function () {
              var client_type = $(this).val();
              var client_id = '{{ $client->id }}';
              if (client_type) {
                  $.ajax({
                      url: '/admin/client/getsuperiorlist',
                      type: "GET",
                       data: {client_type: client_type, cid: client_id},
                      dataType: "json",
                      cache: false,
                      success: function (data) {
                          $("#superior").empty();
                          $('<option></option>').val("").text("{{$companyName}}").appendTo('#superior');
                          if(data.length==0 || hasChild) $("#superior").prop('disabled', true);
                          else $("#superior").prop('disabled', false);
                          if(data==""){
                            $('<option></option>').val("").text("").appendTo('#superior');
                          }else{
                            $.each(data, function (i, item) {

                                $('<optgroup />').prop('label', i).appendTo('#superior');
                                $.each(item, function (key, value) {
                                    $('<option></option>').val(value.id).text(value.company_name).appendTo('#superior');
                                });
                            });
                          }

                      }
                  });
              } else {
                  $('#superior').empty();
              }
          });

          $('#ActivateEdit').click(function(){
            $('.text-display').attr('hidden',true);
            $('.text-form').attr('hidden',false);
            $(this).addClass('hide');
            $('#clearImage').removeClass('hide');
            $('#lblChange').removeClass('hide');
            $('#ActivateUpdate').removeClass('hide');
            $('#ActivateCancel').removeClass('hide');
          });

          $('#ActivateCancel').click(function(){
            $('.text-display').attr('hidden',false);
            $('.text-form').attr('hidden',true);
            $('#lblChange').addClass('hide');
            $('#confirmremove').val('');
            $('#clearImage').addClass('hide');
            $('#ActivateEdit').removeClass('hide');
            $('#ActivateUpdate').addClass('hide');
            var clink = $('#clientImage').attr('src');
            $('.imagePreview').attr('src',clink);
            $('.imagePreview').addClass('display-imglists');
            $('.imagePreview').css('background',"url('"+clink+"')");
            $('.imagePreview').css('background-size',"contain");
            $('.imagePreview').css('background-repeat',"no-repeat");
            $('.imagePreview').css('background-position-x',"center");
            $('.imagePreview').css('background-color',"grey");
            $(this).addClass('hide');
          });

          $('#clearImage').click( function(){
            $(this).addClass('hide');
            $('.uploadFile').val('');
            $('#confirmremove').val('true');
            $('.imagePreview').html('');
            $('.imagePreview').removeClass('clientImageExists');
            $('.imagePreview').removeAttr('src');
            $('.imagePreview').removeAttr('style');
            $('.imagePreview').css('background:url("../../../cms/storage/app/public/uploads/addPhoto.png")');
            $('.imagePreview').attr('src','../../../cms/storage/app/public/uploads/nopartyimage.png');
            $('.emp-show-profile-pic').css('height','50px');
          });

          $('#ActivateBusinessEdit').click(function(){
            $('.text-display').attr('hidden',true);
            $('.text-form').attr('hidden',false);
            $(this).addClass('hide');
            $('#ActivateBusinessUpdate').removeClass('hide');
            $('#ActivateBusinessCancel').removeClass('hide');
          });

          $('#ActivateBusinessCancel').click(function(){
            $('.text-display').attr('hidden',false);
            $('.text-form').attr('hidden',true);
            $('#ActivateBusinessEdit').removeClass('hide');
            $('#ActivateBusinessUpdate').addClass('hide');
            $(this).addClass('hide');
          });

          $('#ActivateContactEdit').click(function(){
            $('.text-display').attr('hidden',true);
            $('.text-form').attr('hidden',false);
            $(this).addClass('hide');
            $('#ActivateContactUpdate').removeClass('hide');
            $('#ActivateContactCancel').removeClass('hide');
          });

          $('#ActivateContactCancel').click(function(){
            $('.text-display').attr('hidden',false);
            $('.text-form').attr('hidden',true);
            $('#ActivateContactEdit').removeClass('hide');
            $('#ActivateContactUpdate').addClass('hide');
            $(this).addClass('hide');
          });

          $('#ActivateLocationEdit').click(function(){
            $('.text-display').attr('hidden',true);
            $('.text-form').attr('hidden',false);
            $(this).addClass('hide');
            $('#ActivateLocationUpdate').removeClass('hide');
            $('#ActivateLocationCancel').removeClass('hide');
          });

          $('#ActivateLocationCancel').click(function(){
            $('.text-display').attr('hidden',false);
            $('.text-form').attr('hidden',true);
            $('#ActivateLocationEdit').removeClass('hide');
            $('#ActivateLocationUpdate').addClass('hide');
            $(this).addClass('hide');
          });

          $('#ActivateAccountingEdit').click(function(){
            $('.text-display').attr('hidden',true);
            $('.text-form').attr('hidden',false);
            $(this).addClass('hide');
            $('#ActivateAccountingUpdate').removeClass('hide');
            $('#ActivateAccountingCancel').removeClass('hide');
          });

          $('#ActivateAccountingCancel').click(function(){
            $('.text-display').attr('hidden',false);
            $('.text-form').attr('hidden',true);
            $('#ActivateAccountingEdit').removeClass('hide');
            $('#ActivateAccountingUpdate').addClass('hide');
            $(this).addClass('hide');
          });

          $('#ActivateMiscEdit').click(function(){
            $('.text-display').attr('hidden',true);
            $('.text-form').attr('hidden',false);
            $(this).addClass('hide');
            $('#ActivateMiscUpdate').removeClass('hide');
            $('#ActivateMiscCancel').removeClass('hide');
          });

          $('#ActivateMiscCancel').click(function(){
            $('.text-display').attr('hidden',false);
            $('.text-form').attr('hidden',true);
            $('#ActivateMiscEdit').removeClass('hide');
            $('#ActivateMiscUpdate').addClass('hide');
            $(this).addClass('hide');
          });

          $('#ActivateCustomFieldEdit').click(function(){
            $('.text-display').attr('hidden',true);
            $('.text-form').attr('hidden',false);
            $(this).addClass('hide');
            $('#ActivateCustomFieldUpdate').removeClass('hide');
            $('#ActivateCustomFieldCancel').removeClass('hide');
            if(customKey==0){
              $('.custom_datepicker').flatpickr({
                  altInput: true,
                  altFormat: "F j, Y",
                  dateFormat: "Y-m-d",
              });

              $('.custom_daterangepicker').flatpickr({
                  altInput: true,
                  altFormat: "F j, Y",
                  dateFormat: "Y-m-d",
                  mode: "range"
              });

              $('.custom_timepicker').flatpickr({
                  enableTime: true,
                  noCalendar: true,
                  dateFormat: "H:i",
              });
              customKey++;
            }

          });

          $('#ActivateCustomFieldCancel').click(function(){
            $('.text-display').attr('hidden',false);
            $('.text-form').attr('hidden',true);
            $('#ActivateCustomFieldEdit').removeClass('hide');
            $('#ActivateCustomFieldUpdate').addClass('hide');
            $(this).addClass('hide');
          });


          $('#UpdateBasicDetail').on('submit',function(e){
            e.preventDefault();
            $('.updateBasicPartyDetails').attr('disabled',true);
            var url = "{{domain_route('company.admin.client.ajaxBasicUpdate')}}";
            $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#ActivateUpdate').attr('disabled',true);
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden',false);
                  },
                  success: function (data) {
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    if(data.code===200){
                        alert(data.success);
                        $('#ActivateUpdate').attr('disabled',false);
                        $('#ActivateUpdate').addClass('hide');
                        $('#ActivateCancel').addClass('hide');
                        $('#lblChange').addClass('hide');
                        $('#clearImage').addClass('hide');
                        $('#ActivateEdit').removeClass('hide');
                        $('.text-form').attr('hidden',true);
                        $('.text-display').attr('hidden',false);
                        $('#cpname').html(data.clientData['name']);
                        $('#cparty_name').html(data.clientData['company_name']);
                        $('#partytitle').html(data.clientData['company_name']);

                        if(data.clientData['client_code']){
                          $('#cpPcode').html(data.clientData['client_code']);
                        }else{
                          $('#cpPcode').html("N/A");
                        }

                        if(data.clientData['about']){
                          $('#cpabout').html(data.clientData['about']);
                        }else{
                          $('#cpabout').html('N/A');
                        }

                        if(data.clientData['image_path']){
                          var path =  "{{URL::asset('cms')}}"+data.clientData['image_path'];
                          $('#clientImage').attr('src',path);
                        }else{
                          var path =  "{{URL::asset('cms')}}"+'/storage/app/public/uploads/nopartyimage.png';
                          $('#clientImage').attr('src',path);
                          $('#clientImage').css('background-color','grey');
                        }

                    }else if(data.code===201){                      
                      alert(data.error);
                    }else{
                      alert("Some issue that is not being handeled");
                    }           
                  },
                  error:function(){
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    $('#ActivateUpdate').attr('disabled',false);
                    alert('Sorry, something went wrong.');
                  }
              });
          });

          $('#UpdateBusinessDetail').on('submit',function(e){
            e.preventDefault();
            $('.updateBasicPartyDetails').attr('disabled',true);
            var url = "{{domain_route('company.admin.client.ajaxBusinessUpdate')}}";
            $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#ActivateBusinessUpdate').attr('disabled',true);
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden',false);
                  },
                  success: function (data) {
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    if(data.code===200){
                      alert(data.success);
                        $('#ActivateBusinessUpdate').attr('disabled',false);
                        $('#ActivateBusinessUpdate').addClass('hide');
                        $('#ActivateBusinessCancel').addClass('hide');
                        $('#ActivateBusinessEdit').removeClass('hide');
                        $('.text-form').attr('hidden',true);
                        $('.text-display').attr('hidden',false);
                        $('#cppartytype').html(data.clientData['partytype']);
                        if(data.clientData['superior_name']){
                          $('#cpsuperior').html(data.clientData['superior_name']);
                        }else{
                          $('#cpsuperior').html("{{$companyName}}");
                        }

                        if(data.clientData['pan']){
                          $('#cpPan').html(data.clientData['pan']);
                        }else{
                          $('#cpPan').html("N/A");
                        }

                        if(data.clientData['business_name']){
                          $('#cpbtype').html(data.clientData['business_name']);
                        }else{
                          $('#cpbtype').html("N/A");
                        }
                    }else if(data.code===201){                      
                      alert(data.error);
                    }else{
                      alert("Some issue that is not being handeled");
                    }           
                  },
                  error:function(){
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    $('#ActivateUpdate').attr('disabled',false);
                    alert('Sorry, something went wrong.');
                  }
              });
          });

          $('#UpdateContactDetail').on('submit',function(e){
            e.preventDefault();
            $('.updateBasicPartyDetails').attr('disabled',true);
            var url = "{{domain_route('company.admin.client.ajaxContactUpdate')}}";
            $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#ActivateContactUpdate').attr('disabled',true);
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden',false);
                  },
                  success: function (data) {
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    if(data.code===200){
                      alert(data.success);
                        $('#ActivateContactUpdate').attr('disabled',false);
                        $('#ActivateContactUpdate').addClass('hide');
                        $('#ActivateContactCancel').addClass('hide');
                        $('#ActivateContactEdit').removeClass('hide');
                        $('.text-form').attr('hidden',true);
                        $('.text-display').attr('hidden',false);
                        if(data.clientData['email']){
                          $('#cpemail').html(data.clientData['email']);
                        }else{
                          $('#cpemail').html("N/A");
                        }
                        if(data.clientData['phone']){
                          $('#cpphone').html(data.clientData['phone']);
                        }else{
                          $('#cpphone').html("N/A");
                        }
                        if(data.clientData['mobile']){
                          $('#cpmobile').html(data.clientData['mobile']);
                        }else{
                          $('#cpmobile').html("N/A");
                        }
                        if(data.clientData['website']){
                          $('#cpWebsite').html(data.clientData['website']);
                        }else{
                          $('#cpWebsite').html("N/A");
                        }
                    }else if(data.code===201){                      
                      alert(data.error);
                    }else{
                      alert("Some issue that is not being handeled");
                    }           
                  },
                  error:function(){
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    $('#ActivateUpdate').attr('disabled',false);
                    alert('Sorry, something went wrong.');
                  }
              });
          });

          $('#UpdateLocationDetail').on('submit',function(e){
            e.preventDefault();
            $('.updateBasicPartyDetails').attr('disabled',true);
            var url = "{{domain_route('company.admin.client.ajaxLocationUpdate')}}";
            $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#ActivateLocationUpdate').attr('disabled',true);
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden',false);
                  },
                  success: function (data) {
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    if(data.code===200){
                      alert(data.success);
                        $('#ActivateLocationUpdate').attr('disabled',false);
                        $('#ActivateLocationUpdate').addClass('hide');
                        $('#ActivateLocationCancel').addClass('hide');
                        $('#ActivateLocationEdit').removeClass('hide');
                        $('.text-form').attr('hidden',true);
                        $('.text-display').attr('hidden',false);
                        
                        $('#cpcountry').html(data.clientData['client_country_name']);
                        $('#cpstate').html(data.clientData['client_state_name']);
                        $('#cpcity').html(data.clientData['client_city_name']);
                        if(data.clientData['address_1']){
                          $('#cpaddress1').html(data.clientData['address_1']);
                        }else{
                          $('#cpaddress1').html("N/A");
                        }
                        if(data.clientData['address_2']){
                          $('#cpaddress2').html(data.clientData['address_2']);
                        }else{
                          $('#cpaddress2').html("N/A");
                        }
                        if(data.clientData['pin']){
                          $('#cppin').html(data.clientData['pin']);
                        }else{
                          $('#cppin').html("N/A");
                        }
                        if(data.clientData['beat_name']){
                          $('#cpbeat').html(data.clientData['beat_name']);
                        }else{
                          $('#cpbeat').html('N/A');
                        }
                        if(data.clientData['phonecode']){
                          $('#cpphonecode').html(data.clientData['phonecode']);
                          $('#cpphonecode_value').val(data.clientData['phonecode']);
                        }
                    }else if(data.code===201){                      
                      alert(data.error);
                    }else{
                      alert("Some issue that is not being handeled");
                    }           
                  },
                  error:function(){
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    $('#ActivateUpdate').attr('disabled',false);
                    alert('Sorry, something went wrong.');
                  }
              });
          });

          $('#UpdateAccountingDetail').on('submit',function(e){
            e.preventDefault();
            $('.updateBasicPartyDetails').attr('disabled',true);
            var url = "{{domain_route('company.admin.client.ajaxAccountingUpdate')}}";
            $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#ActivateAccountingUpdate').attr('disabled',true);
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden',false);
                  },
                  success: function (data) {
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    if(data.code===200){
                      alert(data.success);
                        $('#ActivateAccountingUpdate').attr('disabled',false);
                        $('#ActivateAccountingUpdate').addClass('hide');
                        $('#ActivateAccountingCancel').addClass('hide');
                        $('#ActivateAccountingEdit').removeClass('hide');
                        $('.text-form').attr('hidden',true);
                        $('.text-display').attr('hidden',false);
                        if(data.clientData['opening_balance'])
                          $('#c_opening_balance').html(data.clientData['opening_balance']);
                        else
                          $('#c_opening_balance').html('N/A');
                        if(data.clientData['credit_limit'])
                          $('#c_credit_limit').html(data.clientData['credit_limit']);
                        else
                          $('#c_credit_limit').html('N/A');
                        if(data.clientData['credit_days'])  
                          $('#c_credit_days').html(data.clientData['credit_days']);
                        else
                          $('#c_credit_days').html('N/A');
                    }else if(data.code===201){                      
                      alert(data.error);
                    }else{
                      alert("Some issue that is not being handeled");
                    }           
                  },
                  error:function(){
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    $('#ActivateUpdate').attr('disabled',false);
                    alert('Sorry, something went wrong.');
                  }
              });
          });

          $('#UpdateMiscDetail').on('submit',function(e){
            e.preventDefault();
            $('.updateBasicPartyDetails').attr('disabled',true);
            var url = "{{domain_route('company.admin.client.ajaxMiscellaneousUpdate')}}";
            $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#ActivateMiscUpdate').attr('disabled',true);
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden',false);
                  },
                  success: function (data) {
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    if(data.code===200){
                      alert(data.success);
                        $('#ActivateMiscUpdate').attr('disabled',false);
                        $('#ActivateMiscUpdate').addClass('hide');
                        $('#ActivateMiscCancel').addClass('hide');
                        $('#ActivateMiscEdit').removeClass('hide');
                        $('.text-form').attr('hidden',true);
                        $('.text-display').attr('hidden',false);
                        $('#cpstatus').html(data.clientData['status']);
                        @if(config('settings.party_wise_rate_setup')==1)
                          $('#cprate').html(data.clientData['rate_name']);
                        @endif
                        @if(config('settings.category_wise_rate_setup')==1)
                          let appliedRates = JSON.parse(data.categoryRates)
                          if(appliedRates.length > 0){
                            let name = new Array();
                            let newVal = new Array()
                            Object.keys(appliedRates).map((el, ind) => {
                              name.push(appliedRates[el]);
                              newVal.push(el)
                            })
                            $('#cprate').html(name.join(', '));
                            $('#categoryRates').multiselect('select', newVal)
                          }
                        @endif
                    }else if(data.code===201){                      
                      alert(data.error);
                    }else{
                      alert("Some issue that is not being handeled");
                    }           
                  },
                  error:function(){
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    $('#ActivateUpdate').attr('disabled',false);
                    alert('Sorry, something went wrong.');
                  }
              });
          });

          $('#UpdateCustomFieldDetail').on('submit',function(e){
            e.preventDefault();
            $('.UpdateCustomFieldDetails').attr('disabled',true);
            var url = "{{domain_route('company.admin.client.ajaxCustomFieldUpdate')}}";
            $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#ActivateCustomFieldUpdate').attr('disabled',true);
                    $('#mainBox').addClass('box-loader');
                    $('#loader1').removeAttr('hidden',false);
                  },
                  success: function (data) {
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateBasicPartyDetails').attr('disabled',false);
                    if(data.code===200){
                      alert(data.success);
                        $('#UpdateCustomFieldDetail').find('input[type="file"]').val('');
                        $('#ActivateCustomFieldUpdate').attr('disabled',false);
                        $('#ActivateCustomFieldUpdate').addClass('hide');
                        $('#ActivateCustomFieldCancel').addClass('hide');
                        $('#ActivateCustomFieldEdit').removeClass('hide');
                        $('.text-form').attr('hidden',true);
                        $('.text-display').attr('hidden',false);
                        $.each(data.fieldData,function(k,v){
                          $('#'+k).html(nl2br(v));
                          if('#'+k+'-editedImages'!='unidentified'){
                            $('#'+k+'-editedImages').html(v);
                          }
                          if('#'+k+'-editedFiles'!='unidentified'){
                            $('#'+k+'-editedFiles').html(v);
                          }
                        });
                    }else if(data.code===201){                      
                      alert(data.error);
                    }else{
                      alert("Some issue that is not being handeled");
                    }           
                  },
                  error:function(){
                    $('#mainBox').removeClass('box-loader');
                    $('#loader1').attr('hidden',true);
                    $('.updateCustomFieldDetails').attr('disabled',false);
                    $('#ActivateCustomFieldUpdate').attr('disabled',false);
                    alert('Sorry, something went wrong.');
                  }
              });
          });

          @if(config('settings.ncal')==0)
            $("#delivery_datenew").datepicker({
              format: "yyyy-mm-dd",
              startDate: new Date(),
              autoclose: true,
            });
            $(document).on('click', '.edit-modal-order', function () {
              $('#footer_action_button').addClass('glyphicon-check');
              $('#footer_action_button').removeClass('glyphicon-trash');
              $('.actionBtn').addClass('btn-success');
              $('.actionBtn').removeClass('btn-danger');
              $('.actionBtn').addClass('edit');
              $('.modal-title').text('Change Status');
              $('.deleteContent').hide();
              $('.form-horizontal').show();
              $('#order_id').val($(this).data('id'));
              $('#delivery_status').val($(this).data('status'));
              $('#transport_name').val($(this).data('transport_name'));
              $('#transport_number').val($(this).data('transport_number'));
              $('#billty_number').val($(this).data('billty_number'));
              $('#delivery_datenew').val($(this).data('orderdate'));
              $('#delivery_place').val($(this).data('place'));
              $('#delivery_note').val($(this).data('note'));
              $('#myOrderModal').modal('show');
            });
          @else
            $('#delivery_ndate').nepaliDatePicker({
              onChange:function(){
              $('#delivery_edate').val(BS2AD($('#delivery_ndate').val()));
              }
            });
            $(document).on('click', '.edit-modal-order', function () {
              // $('#footer_action_button').text(" Change");
              $('#footer_action_button').addClass('glyphicon-check');
              $('#footer_action_button').removeClass('glyphicon-trash');
              $('.actionBtn').addClass('btn-success');
              $('.actionBtn').removeClass('btn-danger');
              $('.actionBtn').addClass('edit');
              $('.modal-title').text('Change Status');
              $('.deleteContent').hide();
              $('.form-horizontal').show();
              $('#order_id').val($(this).data('id'));
              // $('#remark').val($(this).data('remark'));
              $('#delivery_status').val($(this).data('status'));
              $('#transport_name').val($(this).data('transport_name'));
              $('#transport_number').val($(this).data('transport_number'));
              $('#billty_number').val($(this).data('billty_number'));
              $('#delivery_edate').val(($(this).data('orderdate')));
              $('#delivery_ndate').val(AD2BS($(this).data('orderdate')));
              $('#delivery_place').val($(this).data('place'));
              $('#delivery_note').val($(this).data('note'));
              $('#myOrderModal').modal('show');
            });
          @endif

          // $("#delivery_datenew").datepicker({
          //     format: "yyyy-mm-dd",
          //     endDate: new Date(),
          //     autoclose: true,
          // }).datepicker("setDate", "0");

          var table = $('#subclient').DataTable({
              "columnDefs": [ {
                "targets": 7,
                "orderable": false
                }],
                dom:  "<f>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'li><'col-sm-7'p>>",
              //lengthChange: false,
              stateSave: true,
              "stateSaveParams": function (settings, data) {
              data.search.search = "";
              },
              buttons: [
                  {
                      extend: 'excelHtml5',
                      title: '{{ $client->company_name }} List',
                      exportOptions: {
                          columns: [0, 1, 2, 3, 4, 5, 6]
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: '{{ $client->company_name }} List',
                      exportOptions: {
                          columns: [0, 1, 2, 3, 4, 5, 6]
                      }
                  },
                  {
                      extend: 'print',
                      title: '{{ $client->company_name }} List',
                      exportOptions: {
                          columns: [0, 1, 2, 3, 4, 5, 6]
                      }
                  },
              ]

          });

        @if(hasChild($client->id))
        var btn1 = $('#clientgroups button').first().val();
        $('#pkey'+btn1).click(function(e){
          e.preventDefault();
          $('#clientgroups button').removeClass('active');
            $(this).addClass('active');
            var client_id = '{{$client->id}}';
            var party_type = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.client.getPartyTypeClients')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'client_id': client_id,
                    'party_type': party_type,
                },
                success: function (data) {
                  table.clear().draw();
                  for(i=0;i<data['count'];i++){

                    var client_id = data['clients'][i]['id'];
                    var showurl = "{{ domain_route('company.admin.client.show',['id']) }}";
                    showurl = showurl.replace('id', data['clients'][i]['id']);
                    // var showrurl = "{{ domain_route('company.admin.client.retailerslist',['id']) }}";
                    // showrurl = showurl.replace('id', data['clients'][i]['id']);
                    @if(Auth::user()->can('party-update'))
                    var editurl = "{{ domain_route('company.admin.client.edit',['id']) }}";
                    editurl = editurl.replace('id', data['clients'][i]['id']);
                    @else
                    editurl = "";
                    @endif

                    @if(Auth::user()->can('party-delete'))
                    var delurl = "{{ domain_route('company.admin.subclient.destroy',['id']) }}";
                    delurl = delurl.replace('id', data['clients'][i]['id']);
                    @else
                    delurl = "";
                    @endif

                    var formSubmitPage = "{{ domain_route('company.admin.client.show',['id']) }}";
                    formSubmitPage = formSubmitPage.replace('id', data['clients'][i]['id']);

                    // if(data['clients'][i]['childs']==true){
                    if($.inArray(data['clients'][i]['id'], handledClients) >= 0){
                      var partyname = '<td><a href="'+showurl+'">'+data['clients'][i]['company_name']+'</a></td>';                       
                    }else{                      
                      // var partyname = '<td>'+data['clients'][i]['company_name']+'</td>';
                      var partyname = '<td><a href="#" class="clientLinks" data-viewable="">'+data['clients'][i]['company_name']+'</a></td>';
                    }
                    var edModalClass = data['clients'][i]['canstatus'] ? 'edit-party-modal' : 'alert-modal';
                    var status = '<a href="#" class="'+edModalClass+'" data-id="'+data['clients'][i]['id']+'" data-status="'+data['clients'][i]['status']+'" data-action="'+formSubmitPage+'">';
                    if(data['clients'][i]['status'] =='Active'){
                      status=status+'<span class="label label-success">'+data['clients'][i]['status']+'</span>';
                    }else if(data['clients'][i]['status'] =='Inactive'){
                      status=status+'<span class="label label-warning">'+data['clients'][i]['status']+'</span>';
                    }else{
                      status=status+'<span class="label label-danger">'+data['clients'][i]['status']+'</span>';
                    }
                    status = status + '</a>';
                    var action = data['clients'][i]['canview'] ? '<a href="'+showurl+'" class="btn btn-success btn-sm" style="padding: 3px 6px;"><i class="fa fa-eye"></i></a>' : null;
                    if(data['clients'][i]['canedit']){
                      @if(Auth::user()->can('party-update'))
                      action = action + '<a href="'+editurl+'" class="btn btn-warning btn-sm" style="padding: 3px 6px;"><i class="fa fa-edit"></i></a>';
                      @endif
                    }
                    if(data['clients'][i]['candelete']){
                      @if(Auth::user()->can('party-delete'))
                      if(data['clients'][i]['partyactivity']==false){
                        action = action+'<a class="btn btn-danger btn-sm delete" data-mid="'+data['clients'][i]['id']+'" data-url="'+delurl+'" data-toggle="modal" data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';
                      }
                      @endif
                    }
                    table.row.add([
                      i + 1,
                      partyname,
                      data['clients'][i]['phone'],
                      data['clients'][i]['mobile'],
                      data['clients'][i]['email'],
                      data['clients'][i]['name'],
                      status,
                      action,
                    ]).draw();
                    
                  }
                }
            });
        });
        $('#pkey'+btn1).click();
      @endif

          $('#clientgroups').on('click','button',function(e){
            e.preventDefault();
            $('#clientgroups button').removeClass('active');
            $(this).addClass('active');
            var client_id = '{{$client->id}}';
            var party_type = $(this).val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.client.getPartyTypeClients')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'client_id': client_id,
                    'party_type': party_type,
                },
                success: function (data) {
                  table.clear().draw();
                  for(i=0;i<data['count'];i++){

                    var client_id = data['clients'][i]['id'];
                    var showurl = "{{ domain_route('company.admin.client.show',['id']) }}";
                    showurl = showurl.replace('id', data['clients'][i]['id']);
                    // var showrurl = "{{ domain_route('company.admin.client.retailerslist',['id']) }}";
                    // showrurl = showurl.replace('id', data['clients'][i]['id']);
                    @if(Auth::user()->can('party-update'))
                    var editurl = "{{ domain_route('company.admin.client.edit',['id']) }}";
                    editurl = editurl.replace('id', data['clients'][i]['id']);
                    @else
                    editurl = ""; 
                    @endif

                    @if(Auth::user()->can('party-delete'))
                    var delurl = "{{ domain_route('company.admin.client.destroy',['id']) }}";
                    delurl = delurl.replace('id', data['clients'][i]['id']);
                    @else
                    delurl = "";
                    @endif

                    // if(data['clients'][i]['childs']==true){
                    if($.inArray(data['clients'][i]['id'], handledClients) >= 0){
                      var partyname = '<td><a href="'+showurl+'">'+data['clients'][i]['company_name']+'</a></td>';                       
                    }else{                      
                      // var partyname = '<td>'+data['clients'][i]['company_name']+'</td>';
                      var partyname = '<td><a href="#" class="clientLinks" data-viewable="">'+data['clients'][i]['company_name']+'</a></td>';
                    }
                    
                    var status = '<a href="#" class="edit-party-modal" data-id="'+data['clients'][i]['id']+'" data-status="'+data['clients'][i]['status']+'">';
                    if(data['clients'][i]['status'] =='Active'){
                      status=status+'<span class="label label-success">'+data['clients'][i]['status']+'</span>';
                    }else if(data['clients'][i]['status'] =='Inactive'){
                      status=status+'<span class="label label-warning">'+data['clients'][i]['status']+'</span>';
                    }else{
                      status=status+'<span class="label label-danger">'+data['clients'][i]['status']+'</span>';
                    }
                    status = status + '</a>';
                    var action = data['clients'][i]['canview'] ? '<a href="'+showurl+'" class="btn btn-success btn-sm" style="padding: 3px 6px;"><i class="fa fa-eye"></i></a>' : null;
                    if(data['clients'][i]['canedit']){
                      @if(Auth::user()->can('party-update'))
                      action = action + '<a href="'+editurl+'" class="btn btn-warning btn-sm" style="padding: 3px 6px;"><i class="fa fa-edit"></i></a>';
                      @endif
                    }
                    if(data['clients'][i]['candelete']){
                      @if(Auth::user()->can('party-delete'))
                      if(data['clients'][i]['partyactivity']==true){
                        action = action+'<a class="btn btn-danger btn-sm delete" data-mid="'+data['clients'][i]['id']+'" data-url="'+delurl+'" data-toggle="modal" data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';                      
                      }
                      @endif
                    }
                    table.row.add([
                      i + 1,
                      partyname,
                      data['clients'][i]['phone'],
                      data['clients'][i]['mobile'],
                      data['clients'][i]['email'],
                      data['clients'][i]['name'],
                      status,
                      action,
                    ]).draw();
                    
                  }
                }
            });
          });

      });

  
      $(document).on('click', '.edit-modal', function () {
          // $('#footer_action_button').text(" Change");
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Change Status');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#expense_id').val($(this).data('id'));
          $('#remark').val($(this).data('remark'));
          $('#status').val($(this).data('status'));
          $('#myExpenseModal').modal('show');
      });


      $(document).on('click', '.edit-party-modal', function () {
          $('#footer_action_button').text(" Change");
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Change Status');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#client_id').val($(this).data('id'));
          $('#status').val($(this).data('status'));
          $('#myPartyModal').modal('show');
      });
      
      $('#changeStatus').on('submit',function(){
        $('#btn_change_status').attr('disabled','disabled');
      });

      // $('.alert-modal').on('click',function(){
      //   $('#alertModal').modal('show');
      // });

      $(document).on('click','.alert-modal',function(){
        $('#alertModal').modal('show');
      });


      $('.alert-user-modal').on('click',function(){
        $('#alertUserModal').modal('show');
      });

      // CKEDITOR.replace('about');

</script>

@if(Auth::user()->can('PartyVisit-view'))
  @include('company.clients.customjs.party-visit')
@endif

@if(config('settings.ageing')==1 && Auth::user()->can('ageing-view'))
<script>

    $(function() {

      $("#first-order-credit-table").DataTable({
        "autoWidth": false,
        "order": [[ 0, "asc" ]],
        "processing": false,
        "serverSide": false,
        "sPaginationType": "full_numbers",
        "bFilter": false,
        "bSearchable":false,
        "bInfo":false,
         "paging":   false,
        "ordering": false,
        "info":     false,
        "dom":  "<'row'<'col-xs-12 alignleft'f>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
        "ajax": {
          "url": "{{ domain_route('company.admin.get-order-on-credit-days') }}",
          "dataType": "json",
          "type": "POST",
          "data": {
            "id":{{$client->id}},
          },
        },
        "columns": [
          {"data": "orderNo"},
          {"data": "orderAmount"},
          {"data": "dueDays"},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
          let api = this.api();

          let intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
          };

          pageTotal = api.column(1,{ page: 'current'}).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
          },0);

          $(api.column(1).footer()).html('{{config('settings.currency_symbol')}}'+pageTotal.toLocaleString());
        }
      });

      $("#first-upcoming-payment-table").DataTable({
        "autoWidth": false,
        "order": [[ 0, "asc" ]],
        "processing": false,
        "serverSide": false,
        "sPaginationType": "full_numbers",
        "bFilter": false,
        "bSearchable":false,
        "bInfo":false,
         "paging":   false,
        "ordering": false,
        "info":     false,
        "dom":  "<'row'<'col-xs-12 alignleft'f>>" +
                "<'row'<'col-xs-12'tr>>" +
                "<'row'<'col-xs-4'li><'col-xs-8'p>>",
        "ajax": {
          "url": "{{ domain_route('company.admin.get-upcoming-payment-details') }}",
          "dataType": "json",
          "type": "POST",
          "data": {
            "id":{{$client->id}},
          },
        },
        "columns": [
          {"data": "orderNo"},
          {"data": "amount"},
          {"data": "date"},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
          let api = this.api();

          let intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
          };

          pageTotal = api.column(1,{ page: 'current'}).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
          },0);

          $(api.column(1).footer()).html('{{config('settings.currency_symbol')}}'+pageTotal.toLocaleString());
        }
      });

    });
  </script>
@endif

@endsection