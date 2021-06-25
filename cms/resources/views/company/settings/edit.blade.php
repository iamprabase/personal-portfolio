@extends('layouts.company')
@section('title', 'Settings')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<!-- Bootstrap time Picker -->
<link rel="stylesheet" href="{{asset('assets/plugins/timepicker/bootstrap-timepicker.css')}}">
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}"/>
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}"
      media="print">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/settings/css/partytypes.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/settings/css/customfield.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{ asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@endif
<style>

  .close{
    font-size: 30px!important;
    color: #080808;
    opacity: 1;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 22px;
    width: 258px !important;
  }

  .select2-container .select2-selection--single {
    height: 40px;
    padding: 12px 5px;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow b {
    margin-top: 3px;
  }

  #calendar table tbody tr td:last-child a {
    float: initial;
  }

  .fc-time {
    display: none;
  }

  .fc-content {
    color: white;
  }

  .btn-xs {
    padding: 1px 1px !important;
    font-size: 12px;
    border-radius: 3px;
  }

  #calendar .btn {
    height: auto !important;
  }

  #clientSettings .btn.btn-primary {
    color: blue !important;
  }

  #clientSettings .btn.btn-primary:hover {
    color: white !important;
  }

  #clientSettings .btn.btn-danger {
    color: red !important;
    background-color: white !important;
    border: none;
  }

  #clientSettings .btn.btn-danger:hover {
    color: white !important;
    background-color: red !important;
  }


  /*users*/

  #tblbanks  .btn.btn-primary {
    color: blue !important;
    background-color: transparent !important;
    border: none;
  }

  #tblbanks  .btn.btn-primary:hover {
    background-color: transparent !important;
  }

  #tbldesignation  .btn.btn-primary {
    color: blue !important;
    background-color: transparent !important;
    border: none;
  }

  #tbldesignation  .btn.btn-primary:hover {
    background-color: transparent !important;
  }

  #tblbanks  .btn.btn-danger {
    color: red !important;
    background-color: transparent !important;
    border: none;
  }

  #tblbanks  .btn.btn-danger:hover {
    background-color: transparent !important;
  }

  #tblbusiness_type  .btn.btn-primary {
    color: blue !important;
    background-color: transparent !important;
    border: none;
  }

  #tblbusiness_type  .btn.btn-primary:hover {
    background-color: transparent !important;
  }

  #tblbusiness_type  .btn.btn-danger {
    color: red !important;
    background-color: transparent !important;
    border: none;
  }

  #tblbusiness_type  .btn.btn-danger:hover {
    background-color: transparent !important;
  }

  /*begin paste*/
  #tblexpense_type  .btn.btn-primary {
    color: blue !important;
    background-color: transparent !important;
    border: none;
  }

  #tblexpense_type  .btn.btn-primary:hover {
    background-color: transparent !important;
  }

  #tblexpense_type  .btn.btn-danger {
    color: red !important;
    background-color: transparent !important;
    border: none;
  }

  #tblexpense_type  .btn.btn-danger:hover {
    background-color: transparent !important;
  }
  /*end paste*/

  #tblleavetype  .btn.btn-primary {
    color: blue !important;
    background-color: transparent !important;
    border: none;
  }

  #tblleavetype  .btn.btn-primary:hover {
    background-color: transparent !important;
  }

  #tblleavetype  .btn.btn-danger {
    color: red !important;
    background-color: transparent !important;
    border: none;
  }

  #tblleavetype  .btn.btn-danger:hover {
    background-color: transparent !important;
  }

  #tbl_beats  .btn.btn-primary {
    color: blue !important;
    background-color: transparent !important;
    border: none;
  }

  #tbl_beats  .btn.btn-primary:hover {
    background-color: transparent !important;
  }

  #tbl_beats  .btn.btn-danger {
    color: red !important;
    background-color: transparent !important;
    border: none;
  }

  #tbl_beats  .btn.btn-danger:hover {
    background-color: transparent !important;
  }

  #tbldesignation  .btn.btn-danger {
    color: red !important;
    background-color: transparent !important;
    border: none;
  }

  #tbldesignation  .btn.btn-danger:hover {
    background-color: transparent !important;
  }

  .external-events {
    cursor: pointer !important;
  }

  #external-events .btn-success {
    background-color: #00da76 !important;
    cursor: default !important;
  }

  .fc-today {
    color: blue;
  }

  #myTabContent {
    margin-top: 0px;
  }

  #myTabs li {
    width: 100%;
    border-bottom: 1px solid #ccc;
  }

  .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
    color: #555;
    cursor: default !important;
    background-color: #fff;
    border-left: 2px solid #20c5cb;
    border-bottom-color: transparent;
    border-right: 0px solid #ccc;
  }

  .tab-content {
    border: 1px solid #ccc;
    padding: 20px 20px 5px;
    border-radius: 4px;
    display: inline-block;
    width: 100%;
    background: #fff;
  }

  .nav-tabs {
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fff;
  }

  .nav-tabs.holiday-tab {
    border-top: 3px solid #337ab7;
  }

  .holidayes-title {
    background: transparent !important;
  }

  .nav-tabs li.holidayes-title a {
    color: #0b7676 !important;
    font-size: 20px;
  }

  .nav-tabs li.holidayes-title a:hover {
    background: transparent;
    border-color: transparent;
    cursor: initial;
    color: #0b7676 !important;
    font-size: 20px;
  }

  .nav-tabs.holiday-tab li a:hover {
    background: transparent !important;
    border-color: transparent !important;
    color: #333 !important;
    cursor: initial;
  }

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

  /**/
  .riw-item {
    width: 100%;
    padding: 20px 10px;
    background-color: #3494aa;
    float: left;
    margin-bottom: 20px;
    min-height: 115px;
  }

  .info .item-sec:nth-child(1) .riw-item {
    background-color: #20c5cb;
  }

  .info .item-sec:nth-child(2) .riw-item {
    background-color: #f4884a;
  }

  .info .item-sec:nth-child(3) .riw-item {
    background-color: #00b393;
  }

  .info .item-sec:nth-child(4) .riw-item {
    background-color: #8c8c8c;
  }

  .info .item-sec:nth-child(5) .riw-item {
    background-color: #f54646;
    padding-top: 50px;
  }

  .info .item-sec:nth-child(6) .riw-item {
    background-color: #56c16c;
    padding-top: 50px;
  }

  .site-tital {
    margin-top: 0px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 10px;
    margin-bottom: 20px;
  }

  .note {
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 4px;
    background: #f5fffd;
    margin-bottom: 20px;
  }

  .note h3 {
    margin-top: 0px;
  }

  .input-group {
    position: relative;
    display: table;
    border-collapse: separate;
  }

  .input-group-btn {
    position: relative;
    font-size: 0;
    white-space: nowrap;
  }

  .input-group-btn > .btn {
    position: relative;
  }

  .btn-default {
    border: 1px solid #ccc !important;
  }

  .btn {
    padding: 9px 15px;
  }

  .brows {
    position: relative;
    overflow: hidden;
  }

  .btn.btn-file > input[type='file'] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    opacity: 0;
    filter: alpha(opacity=0);
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
  }

  input[type=file] {
    display: block;
  }

  button, input, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
  }

  #img-upload, #img-upload1, #img-upload2 {
    margin: 20px 0px;
  }

  #myTabContent {
    margin-top: 0px;
  }

  #myTabs li {
    width: 100%;
    border-bottom: 1px solid #ccc;
  }

  .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
    color: #555;
    cursor: default;
    background-color: #fff;
    border-left: 2px solid #20c5cb;
    border-bottom-color: transparent;
    border-right: 0px solid #ccc;
  }

  .nav-tabs > li.active:first-child > a {
    border-top: 1px solid transparent;
  }

  .nav-tabs > li > a:hover {
    border-color: transparent;
  }

  .nav > li > a:focus, .nav > li > a:hover {
    text-decoration: none;
    background-color: transparent;
  }

  .nav li a {
    border-left: 2px solid transparent;
  }

  .nav li.active a {
    text-decoration: none;
    /*background-color: #eee !important;*/
    margin-right: 0px;
    border-left: 2px solid #20c5cb;
    border-radius: 0px;
  }

  .tab-content {
    border: 1px solid #ccc;
    padding: 20px 20px 5px;
    border-radius: 4px;
    display: inline-block;
    width: 100%;
    background: #fff;
  }

  .nav-tabs {
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fff;
  }

  /**/
  a:hover {
    text-decoration: none;
  }

  .records-info-wrap .riw-item {
    width: 20%;
    padding: 10px 20px;
    background-color: #3494aa;
    float: left;
    border-radius: 4px;
  }

  .riw-item a, .riw-item span {
    color: #fff;
  }

  .riw-item span.riw-top {
    font-size: 31px;
    font-weight: 700;
  }

  .riw-item span.riw-middle {
    font-size: 19px;
    text-transform: uppercase;
  }

  .riw-item span.riw-bottom {
    font-size: 16px;
    margin-bottom: 5px;
    font-family: Montserrat, sans-serif !important;
  }

  .riw-item span {
    display: block;
    text-align: center;
  }

  .riw-item:hover {
    /*background-color: #43b8d4 !important;*/
  }

  .riw-item {
    -webkit-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
  }

  .hover-end {
    padding: 0;
    margin: 0;
    font-size: 75%;
    text-align: center;
    position: absolute;
    bottom: 0;
    width: 100%;
    opacity: .8
  }

  #populate {
    margin-right: 10px;
  }

  .modal-label {
    line-height: 2.5em;
  }

  .fc-widget-content:hover{
    cursor: pointer;
  }

  .fc-event-container a:hover{
    cursor: default;
  }

  #calendar .fc-scroller{
    height: auto!important;
  }

  .beatparties li{
    width: 100%;
    list-style: none;
    padding: 5px 0px;
  }

  #location_accuracy label{
    margin-right: 10px;
  }

  #location_accuracy input{
    height:12px !important;
    margin-right: 6px;
  }

  #location_accuracy .tooltip{
    font-size: 14px;
  }

  #orderstatus  .btn.btn-primary {
    color: blue !important;
    background-color: transparent !important;
    border: none;
  }

  #orderstatus  .btn.btn-primary:hover {
    background-color: transparent !important;
  }

  #orderstatus  .btn.btn-danger {
    color: red !important;
    background-color: transparent !important;
    border: none;
  }

  #orderstatus  .btn.btn-danger:hover {
    background-color: transparent !important;
  }

  .btn-file {
    position: relative;
    overflow: hidden;
  }

  .btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
  }

  .box-body .btn-danger{
    background-color: transparent;
    border-color: transparent;
    color: #d9534f;
  }

  .box-loader{
    opacity: 0.4;
  }

  .setting-tab .employee-tab .left-tab li {
    width: 100%;
    padding-bottom: 0px;
    border-top: 1px solid #f4f4f4;
  }
  .setting-tab .employee-tab .nav-pills .nav-link.active, .setting-tab .employee-tab .nav-pills .show>.nav-link {
    color: #fff;
    background-color: #01a9ac;
    border-bottom: 0px solid #01a9ac;
    border-radius: 0px;
    padding: 10px 20px;
  }
  .setting-tab .employee-tab .nav-pills .nav-item a.nav-link{
    padding: 10px 20px;
    border-top: 0px solid transparent;
  }
  .setting-tab .employee-tab .nav-pills .nav-item.active a.nav-link{
    color: #fff;
    background-color: #01a9ac;
    border-bottom: 0px solid #01a9ac;
    border-radius: 0px;
    padding: 10px 20px;
    border-top: 0px solid transparent;
  }

  .setting-tab .employee-tab .nav-pills .nav-item a.nav-link:hover{
    color: #fff;
    background-color: #01a9ac;
    border-bottom: 0px solid #01a9ac;
    border-radius: 0px;
    padding: 10px 20px;
    border-top: 0px solid transparent;
  }
  .setting-tab .employee-tab .nav-pills .nav-item{
    margin-left: 0px;
  }
  .card-title2 {
    border-bottom: 1px solid #dfdfdf;
    padding-bottom: 25px;
    font-size: 16px;
    font-weight: bold;
  }
  .card-title {
    margin: 5px 0px 0px;
    color: #01a9ac;
  }
  .edit-roles {
    float: right;
    font-size: 14px;
    color: #009688;
  }
  .roletabs {
    border: 1px solid #f9f9f9!important;
    margin-top: 22px;
    border-bottom: none!important;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 4px;
  }
  .roletabs > ul > li.nav-item {
    position: relative;
    padding-bottom: 0;
    border-bottom: 1px solid #f1f1f1!important;
  }
  .left-tab li {
    width: 100%;
    padding-bottom: 10px;
  }
  .roletabs > ul > li{
    margin-left: 0px;
  }
  .roletabs > ul > li > span.actionstyles {
    position: absolute;
    top: 10px;
    right: 3px;
  }
  .roletabs > ul > li.active a{
    background-color: #009688!important;
    color: #fff!important;
  }
  .roletabs > ul > li a:hover{
    background-color: #009688;
    color: #fff!important;
  }
  .roletabs > ul > li > span >a.edit {
    background-color: #fff;
    -moz-border-radius: 50px;
    -webkit-border-radius: 50px;
    -ms-border-radius: 50px;
    border-radius: 50px;
    text-align: center;
    margin-right: 6px;
    display: inline-block;
    width: 26px;
    height: 26px;
    font-size: 12px;
    line-height: 23px;
    color: #009688!important;
    border: 1px solid #009688!important;
  }
  .roletabs > ul > li.active a.edit{
    background-color: #fff!important;
  }
  span.actionstyles a.edit {
    background-color: #fff!important;
  }
  .roletabs > ul > li > span > a.edit:hover {
    background: #d7feff!important;
  }
  /*switch start*/
  .switch {
    position: relative;
    display: inline-block;
    width: 30px;
    height: 17px;
  }
  .switch input { 
    opacity: 0;
    width: 0;
    height: 0;
  }
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
    height: 14px;
    width: 14px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }
  input:checked + .slider {
    background-color: #009688;
  }
  input:focus + .slider {
    box-shadow: 0 0 1px #009688;
  }
  input:checked + .slider:before {
    -webkit-transform: translateX(12px);
    -ms-transform: translateX(12px);
    transform: translateX(12px);
  }
  .slider.round {
    border-radius: 34px;
  }
  .slider.round:before {
    border-radius: 50%;
  }
  #rollssetting .btn-primary{
    color:#fff!important;
    background-color: #089c9c!important;
  }
  #rollssetting .btn-primary:hover{
    background-color: #01dbdf!important;
  }

  .panel-title{
    font-size: 13px;
    height: inherit;
    overflow: hidden;
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

  .collateralSearchBar{
    height: 80px;
    text-align: center;
  }

  .tooltiptext {
    visibility: hidden;
    width: 120px;
    background-color: black;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px 0;

    /* Position the tooltip */
    position: absolute;
    z-index: 1;
    display: block;
  }

  .folderNameSpan:hover .tooltiptext{
    visibility: visible;
  }
  .panel-group .panel{
    box-shadow: 1px 1px 1px rgba(156, 137, 137, 0.1);
  }

  /* #rates_table .btn-success{
    color: #00da76!important;
    background: none !important;
    border: none;
  } */

  .dataTables_scrollHeadInner{
      box-sizing: 
      content-box; 
      min-width: 708px; 
      padding-right: 17px;
  }
  #party_custom_fields_filter{
    float:left;
  }

   .btn-primary {
    background-color: #079292!important;
    border-color: #079292!important;
    color: #fff!important;
}
.btn-primary:hover, .btn-primary:active, .btn-primary.hover {
    background-color: #0b7676!important;
    border-color: #0b7676!important;
}

.modal .btn {
    display: inline-block;
    border-radius: 2px;
    text-transform: capitalize;
    font-size: 14px;
    padding: 8px 18px;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    font-size: 1rem;
    line-height: 1.25;
    border-radius: .25rem;
    transition: all .15s ease-in-out;
}

</style>
@endsection

@section('content')
  <section class="content">
    <div class="row ">
      @if (session()->has('active'))
            <?php $active = session()->get('active'); ?>
      @else
            <?php $active = 'profile' ?>
      @endif
      <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
      @if (\Session::has('success'))
          <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
          </div><br/>           
      @endif
      @if (\Session::has('alert'))
          <div class="alert alert-warning">
            <p>{{ \Session::get('alert') }}</p>
          </div><br/>           
      @endif
        <div class="col-xs-3 right-pd">
          <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li role="presentation" class="{{($active == 'profile')? 'active':''}}"><a href="#company" id="compamy"
                                                                                       role="tab" data-toggle="tab"
                                                                                       aria-controls="company"
                                                                                       aria-expanded="true">Profile</a>
            </li>
            <li role="presentation" class="{{($active == 'layout')? 'active':''}}"><a href="#admin" role="tab"
                                                                                      id="admin-tab" data-toggle="tab"
                                                                                      aria-controls="admin"
                                                                                      aria-expanded="false">Admin
                Layout</a></li>
            {{-- <li role="presentation" class="{{($active == 'email')? 'active':''}}"><a href="#email-setup" role="tab" id="email-setup-tab" data-toggle="tab" aria-controls="email-setup" aria-expanded="false">Email Setup</a></li> --}}
            <li role="presentation" class="{{($active == 'other')? 'active':''}}"><a href="#setup" role="tab"
                                                                                     id="setup-tab" data-toggle="tab"
                                                                                     aria-controls="setup"
                                                                                     aria-expanded="false">Setup</a>
            </li>
            <li role="presentation" class="{{($active == 'plan')? 'active':''}}"><a href="#plan-detail" role="tab" id="plan-detail-tab" data-toggle="tab" aria-controls="plan-detail" aria-expanded="false">Plan Detail</a></li>
            @if(config('settings.orders')==1)
            <li role="presentation" class="{{($active == 'orderstatus')? 'active':''}}"><a href="#order_status-detail" role="tab" id="order_status-detail-tab" data-toggle="tab"aria-controls="order_status-detail" aria-expanded="false">Order Status</a></li>
            @endif
            @if(config('settings.ncal')==1)
            <li role="presentation" class="{{($active == 'holiday')? 'active':''}}"><a href="#Nholiday-detail" role="tab" id="nholiday-detail-tab" data-toggle="tab" aria-controls="Nholiday-detail" aria-expanded="false">Holidays</a>
            </li>
            @else
            <li role="presentation" class="{{($active == 'holiday')? 'active':''}}"><a href="#holiday-detail" role="tab" id="holiday-detail-tab" data-toggle="tab" aria-controls="holiday-detail" aria-expanded="false">Holidays</a>
            </li>
            @endif

            @if(config('settings.collections')==1)
            <li role="presentation" class="{{($active == 'bank')? 'active':''}}"><a href="#bank-detail" role="tab" id="bank-detail-tab" data-toggle="tab" aria-controls="bank-detail" aria-expanded="false">Banks</a></li>
            @endif


            @if(config('settings.beat')==1)
              <li role="presentation" class="{{($active == 'beat')? 'active':''}}"><a href="#beats-detail" role="tab" id="beats-tab" data-toggle="tab" aria-controls="beats" aria-expanded="false">Beats</a></li>
            @endif

            @if(config('settings.party')==1)
            <li role="presentation" class="{{($active == 'businesstype')? 'active':''}}"><a href="#business-types" role="tab" id="business-tab" data-toggle="tab" aria-controls="business" aria-expanded="false">Business Types</a></li>
            @endif

            <li role="presentation" class="{{($active == 'customfield')? 'active':''}}"><a href="#custom-fields" role="tab" id="customfield-tab" data-toggle="tab" aria-controls="customfield" aria-expanded="false">Custom Fields</a></li>

            @if(config('settings.expenses')==1)
            <li role="presentation" class="{{($active == 'expensetype')? 'active':''}}"><a href="#expense-types" role="tab" id="expense-tab" data-toggle="tab" aria-controls="expense" aria-expanded="false">Expense Category</a></li>
            @endif

            @if(config('settings.leaves')==1)
            <li role="presentation" class="{{($active == 'leavetype')? 'active':''}}"><a href="#leave-types" role="tab" id="leave-tab" data-toggle="tab" aria-controls="leave" aria-expanded="false">Leave Types</a></li>
            @endif

            @if(config('settings.visit_module')==1)
              <li role="presentation" class="{{($active == 'visit-purpose')? 'active':''}}"><a href="#visit-purpose" role="tab" id="visit-purpose-tab" data-toggle="tab" aria-controls="visit-purpose" aria-expanded="false">Visit Purpose</a></li>
            @endif

            @if(config('settings.party')==1)
            <li role="presentation" class="{{($active == 'partytype')? 'active':''}}"><a href="#party-types" role="tab" id="partytype-tab" data-toggle="tab" aria-controls="party" aria-expanded="false">Party Types</a></li>
            @endif

            <li role="presentation" class="{{($active == 'designation')? 'active':''}}"><a href="#designations-detail" role="tab" id="designations-detail-tab" data-toggle="tab" aria-controls="designations-detail" aria-expanded="false">Designation</a></li>
            <li role="presentation" class="{{($active == 'roles')? 'active':''}}"><a href="#roles-detail" role="tab" id="roles-detail-tab" data-toggle="tab" aria-controls="roles-detail" aria-expanded="false">Roles & Permissions</a></li>

            @if(config('settings.returns')==1)
            <li role="presentation" class="{{($active == 'returnreasons')? 'active':''}}"><a href="#returnreasons-detail" role="tab"
                id="returnreasons-detail-tab" data-toggle="tab" aria-controls="returnreasons-detail"
                aria-expanded="false">Return Reasons</a></li>
            @endif
            @if(config('settings.collaterals')==1)
            <li role="presentation" class="{{($active == 'collaterals')? 'active':''}}"><a href="#collaterals-detail" role="tab"
                id="collaterals-detail-tab" data-toggle="tab" aria-controls="collaterals-detail"
                aria-expanded="false">Collaterals</a></li>
            @endif
            {{-- @if(config('settings.party_wise_rate_setup')==1 && Auth::user()->can('party_wise_rate_setup-view'))
            <li role="presentation" class="{{($active == 'parties-rate-setup')? 'active':''}}"><a href="#parties-rate-setup-detail" role="tab"
            id="parties-rate-setup-detail-tab" data-toggle="tab" aria-controls="parties-rate-setup-detail" aria-expanded="false">Party-wise Rate Setup</a></li>
            @endif --}}
          
          </ul>
        </div>

        <span id="getPartyTypes" data-url="{{domain_route('company.admin.clientsettings.getpartytypes')}}"></span>
        
        @include('company.settings._form')
      
      </div>
    </div>
  
  
  </section>


@endsection

@section('scripts')
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <!-- bootstrap time picker -->
  <script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
  <script src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
  @if(config('settings.ncal')==1)
  <script src="{{ asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  @endif
  {{-- <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script> --}}
  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatables-buttons/js/buttons.flash.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/js/bootstrap-colorpicker.min.js"></script>
  <script src="{{asset('assets/plugins/settings/partytype.js')}}"></script> 
  <script type="text/javascript" src="{{asset('assets/plugins/settings/permissions.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/plugins/settings/business.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/plugins/settings/expensetypes.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/plugins/settings/creditdays.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/plugins/settings/dateformat.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/plugins/settings/customfield.js')}}"></script>
  <script>
      $('.tab-content form').on("submit", function (e) {
          $(".edit_setting").prop('disabled', true);
      });

      $('.timepicker').timepicker({
          showInputs: false,
          showMeridian: false,
      });

      $('#accuracyLow,#accuracyMedium,#accuracyHigh,#accuracyNone').tooltip({
        placement:'top',
      });

      $('#rolesTabs').on('click','a',function(){
        $('#updateRole').attr('action',$(this).data('action'));
        $('#deleteRole').attr('action',$(this).data('action'));
        $('#updateRoleName').val($(this).data('name'));
      });

      $(function () {
          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
              checkboxClass: 'icheckbox_minimal-blue',
              radioClass: 'iradio_minimal-blue'
          });
      });

      $('#location_accuracy').on('click','input',function(){
        var setAccuracy = confirm('Are you sure want to change accuracy?');
        if(setAccuracy == true){
        var val = $(this).val();
        var url = "{{domain_route('company.admin.setting.updateLocAccuracy')}}";
        if(val != $('#current_accuracy').val()){
          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:url,
            type:"POST",
            data:{
              "accuracy_val":val,
            },
            success:function(data){
              if(data['result'] == true){
                alert('location accuracy updated successfully');
                $('#current_accuracy').attr('value',val);
                $('#location_accuracy')[0].reset();
                $('#location_accuracy').find('input[value='+val+']').prop('checked',true);
              }else{
                alert('Sorry something went wrong');
                $('#location_accuracy')[0].reset();
                $('#location_accuracy').find('input[value='+val+']').prop('checked',true);
              }
            },
            error:function(data){
                alert('Sorry something went wrong');
                $('#location_accuracy')[0].reset();
                $('#location_accuracy').find('input[value='+val+']').prop('checked',true);
            }
          });
        }
        }else{
          var val = $('#current_accuracy').val();
          alert('Accuracy update cancelled!!!');
          $('#location_accuracy')[0].reset();
          $('#location_accuracy').find('input[value='+val+']').prop('checked',true);
        }
      });

      $('select[name="country"]').on('change', function () {
          var countryId = $(this).val();
          $('#state').append($('<option selected="selected"></option>').html('Loading...'));
          if (countryId) {
              $.ajax({
                  url: '/get-state-list?country_id=' + countryId,
                  type: "GET",
                  dataType: "json",
                  success: function (data) {

                      //alert('hi');

                      $("#state").empty();
                      $('#city').empty();
                      $("#city").append('<option value>Select a City</option>');
                      $("#state").append('<option value>Select a State</option>');

                      $.each(data, function (key, value) {

                          $("#state").append('<option value="' + key + '">' + value + '</option>');

                      });
                  }

              });
          } else {
              $('#state').empty();
              $('#city').empty();
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
                      $("#city").append('<option value>Select a City</option>');

                      $.each(data, function (key, value) {

                          $("#city").append('<option value="' + key + '">' + value + '</option>');

                      });
                  }
              });
          } else {

              $('#city').empty();
          }

      });

      $(document).ready(function () {

          var i = 1;

          $('#add').click(function () {
              i++;
              $('#dynamic_field').append('<tr id="row' + i + '"><td><input type="text" name="tax_name[]" id="tax_name' + i + '" class="form-control"></td><td><input type="text" name="tax_percent[]" id="tax_percent' + i + '" class="form-control"></td><td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>');
              $("#tax_percent" + i).keydown(function (e) {
                  if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                      // Allow: Ctrl+A, Command+A
                      (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                      // Allow: home, end, left, right, down, up
                      (e.keyCode >= 35 && e.keyCode <= 40)) {
                      // let it happen, don't do anything
                      return;
                  }
                  // Ensure that it is a number and stop the keypress
                  if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57 && e.keyCode != 190 && e.keyCode != 110)) && (e.keyCode < 96 || e.keyCode > 105)) {
                      e.preventDefault();
                  }
              });
          });
          $(document).on('click', '.btn_remove', function () {
              var button_id = $(this).attr("id");
              $('#row' + button_id + '').remove();
          });
      });

      $(document).on('change', '.logofile :file', function () {
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

      $(document).on('change', '.smalllogofile :file', function () {
          var input = $(this),
              label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
          input.trigger('fileselect', [label]);
      });

      $('.smalllogofile :file').on('fileselect', function (event, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = label;

          if (input.length) {
              input.val(log);
          } else {
              if (log) alert(log);
          }

      });

      function readURL1(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();

              reader.onload = function (e) {
                  $('#img-upload1').attr('src', e.target.result);
              }

              reader.readAsDataURL(input.files[0]);
          }
      }

      $("#imgInp1").change(function () {
          readURL1(this);
      });

      $(document).on('change', '#country', function () {
          var phonecode = $("option:selected", this).attr("phonecode");
          $("#phonecode").val(phonecode);
      });


      $(document).on('change', '.favicon :file', function () {
          var input = $(this),
              label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
          input.trigger('fileselect', [label]);
      });

      $('.favicon :file').on('fileselect', function (event, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = label;

          if (input.length) {
              input.val(log);
          } else {
              if (log) alert(log);
          }

      });

      function readURL2(input) {
          if (input.files && input.files[0]) {
              var reader = new FileReader();

              reader.onload = function (e) {
                  $('#img-upload2').attr('src', e.target.result);
              }

              reader.readAsDataURL(input.files[0]);
          }
      }

      $("#imgInp2").change(function () {
          readURL2(this);
      });


      function removeTax(tax_id) {
          var csrf_token = "{{ csrf_token() }}";
          var tax_url = "{{URL::to('admin/setting/removeTax')}}";

          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              url: tax_url,
              data: {"tax_id": tax_id},
              success: function (data) {
                  $("#showTaxes").load(" #showTaxes");
              }
          });
      }

      $(document).on('change', '#default_currency', function () {
          var symbol = $('option:selected', this).attr('symbol');
          $('#currency_symbol').val(symbol);
      });

      $('#delete').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget)
          var mid = button.data('mid')
          var url = button.data('url');
          $(".remove-record-model").attr("action", url);
          var modal = $(this)
          modal.find('.modal-body #m_id').val(mid);
      });

      $('#tbl_beats').on('click','.beat-delete',function(e){
        $('#deletebeat').modal('show');
        $('#ajaxRemoveBeat').attr('action',$(this).attr('data-url'));
      });

      $('#AddNewDesignation').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.addDesignation').attr("disabled","disabled");
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#tbody_designation').empty();
                    $('#tbody_designation').html(data['designations']);
                    $('#AddNewDesignation')[0].reset();
                    $('#ajaxDesignationlist').empty();
                    $.each(data.alldesignations,function(k,v){
                      $('#ajaxDesignationlist').append('<option value="'+v.id+'">'+v.name+'</option>')
                    });
                    alert('Designation Added Successfully');
                  }else{
                    alert("Please check if given designation already exists or empty");
                  }
                  $('.addDesignation').attr("disabled",false );
              },
              error:function(){
                $('.addDesignation').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });

      });

      $('#tbldesignation').on('click','.deleteBtnDesignation',function(event){
          event.preventDefault();
          $('#deleteDesignation').modal('show');
          var url = $(this).attr('data-url');
          $("#frmRemoveDesignation").attr("action", url);
      });

      $('#tbldesignation').on('click','.editBtnDesignation',function(event){
          event.preventDefault();
          $('#editDesignation').modal('show');
          var url = $(this).attr('data-url');
          $("#frmEditDesignation").attr("action", url);
          $('#designation_name').val($(this).attr('data-name'));
      });

      $('#frmEditDesignation').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
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
                $('.editDesignationKey').attr('disabled',true);
              },
              success: function (data) {
                  alert(data['result']);
                  $('#editDesignation').modal('hide');
                  $('#tbody_designation').empty();
                  $('#tbody_designation').html(data['designations']);
                  $('#ajaxDesignationlist').empty();
                  $.each(data.alldesignations,function(k,v){
                    $('#ajaxDesignationlist').append('<option value="'+v.id+'">'+v.name+'</option>');
                  });
                  $('.editDesignationKey').attr('disabled',false);
              },
              error:function(){
                $('.removeDesignationKey').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });

      });

      $('#frmRemoveDesignation').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
              },
              beforeSend:function(){
                $('.removeDesignationKey').attr('disabled',true);
              },
              success: function (data) {
                  alert(data['result']);
                  $('#deleteDesignation').modal('hide');
                  $('#tbody_designation').empty();
                  $('#tbody_designation').html(data['designations']);
                  $('#ajaxDesignationlist').empty();
                  $.each(data.alldesignations,function(k,v){
                    $('#ajaxDesignationlist').append('<option value="'+v.id+'">'+v.name+'</option>');
                  });
                  $('.removeDesignationKey').attr('disabled',false);
              },
              error:function(){
                $('.removeDesignationKey').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });

      });

      $('#tblbanks').on('click','.edit-bank',function(event){
          event.preventDefault();
          $('#editBank').modal('show');
          var name = $(this).attr('data-name');
          $('#editbankname').val(name);
          var url = $(this).attr('data-url');
          $('#formEditBank').attr('action',url);
      });

      $('#tblbanks').on('click','.delete-bank',function(event){
          event.preventDefault();
          $('#modalDeleteBank').modal('show');
          var url = $(this).attr('data-url');
          $('#frmDelBank').attr('action',url);
          $('#btn-beatDelete-key').removeAttr('disabled');
      });

      $('#addNewBank').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.addNewBank').attr('disabled',true);
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#addNewBank')[0].reset();
                    $('#tbl_banks').empty();
                    $('#tbl_banks').html(data['banks']);
                    alert('Bank Added Successfully')
                  }else{
                    alert('Bank name already exists.')
                  }
                  $('.addNewBank').attr('disabled',false);
              },
              error:function(){
                $('.addNewBank').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });
      });

      $('#formEditBank').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.updateBank').attr('disabled',true);
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#tbl_banks').empty();
                    $('#tbl_banks').html(data['banks']);
                    $('.updateBank').attr('disabled',false);
                    $('#editBank').modal('hide');
                    alert("Bank Updated");
                  }else{
                    alert("Bank's name exists");
                  }
                  $('.delete-button').removeAttr('disabled');
              },
            error:function(){
              $('.delete-button').attr('disabled',false);
              alert('Oops! Something went wrong...');
            }
          });
      });

      $('#frmDelBank').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "POST",
            data: {
                '_token': '{{csrf_token()}}',
            },
            beforeSend:function(){
              $('.removeBankKey').attr('disabled',true);
            },
            success: function (data) {
                if(data['result']==true){
                  alert("Bank Successfully Deleted");
                  $('#tbl_banks').empty();
                  $('#tbl_banks').html(data['banks']);
                }else{
                  alert("Bank can't be deleted since cheques found under it.")
                }
                $('#modalDeleteBank').modal('hide');
                $('.removeBankKey').attr('disabled',false);
            },
            error:function(){
              $('.removeBankKey').attr('disabled',false);
              alert('Oops! Something went wrong...');
            }
        });
      });

      $('#tblleavetype').on('click','.edit-leavetype',function(event){
          event.preventDefault();
          $('#editLeaveType').modal('show');
          var name = $(this).attr('data-name');
          $('#editleavetypename').val(name);
          var url = $(this).attr('data-url');
          $('#formEditLeavetype').attr('action',url);
      });

      $('#tblleavetype').on('click','.delete-leavetype',function(event){
          event.preventDefault();
          $('#modalDeleteLeaveType').modal('show');
          var url = $(this).attr('data-url');
          $('#frmDelLeaveType').attr('action',url);
      });

      $('#addNewleaveType').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.addNewleaveType').attr('disabled',true);
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#addNewleaveType')[0].reset();
                    $('#tbl_leavetypes').empty();
                    $('#tbl_leavetypes').html(data['leavetypes']);
                    alert('Leave Type Added Successfully');
                  }else{
                    alert('Leave Type already exists.')
                  }
                  $('.addNewleaveType').attr('disabled',false);
              },
              error:function(){
                $('.addNewleaveType').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });
      });

      $('#formEditLeavetype').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.updateLeaveType').attr('disabled',true);
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#tbl_leavetypes').empty();
                    $('#tbl_leavetypes').html(data['leaveTypes']);
                    $('.updateLeaveType').attr('disabled',false);
                    $('#editLeaveType').modal('hide');
                    alert("Leave Type Updated");
                  }else{
                    alert("Leave Type already exists");
                  }
                  $('.delete-button').removeAttr('disabled');
              },
            error:function(){
              $('.delete-button').attr('disabled',false);
              alert('Oops! Something went wrong...');
            }
          });
      });

      $('#frmDelLeaveType').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "POST",
            data: {
                '_token': '{{csrf_token()}}',
            },
            beforeSend:function(){
              $('.removeLeaveTypeKey').attr('disabled',true);
            },
            success: function (data) {
                if(data['result']==true){
                  alert("LeaveType Successfully Deleted");
                  $('#tbl_leavetypes').empty();
                  $('#tbl_leavetypes').html(data['leaveTypes']);
                }else{
                  alert("LeaveType can't be deleted since Leaves found under it.")
                }
                $('#modalDeleteLeaveType').modal('hide');
                $('.removeLeaveTypeKey').attr('disabled',false);
            },
            error:function(){
              $('.removeLeaveTypeKey').attr('disabled',false);
              alert('Oops! Something went wrong...');
            }
        });
      });

      $('#beatcity').select2({
        placeholder: 'Select City',
      });

      // $("#updateBeatSettings").find(".edit_beatcity").select2({
      //   placeholder: 'Select City',
      // });


      $('#addNewBeat').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.addBeat').attr('disabled','disabled');
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#tbody_beats').empty();
                    $('#tbody_beats').html(data['beats']);
                    $('#addNewBeat')[0].reset();
                    $('#ms-list-1').find('span').empty();
                    $('#ms-list-1').find('li').removeClass('selected');
                    $('#ms-list-1').removeClass('ms-active');
                    $('#ms-list-1').removeClass('ms-has-selections');
                    checkAssignParties();
                    $('#ms-list-1').multiselect('reload');
                    $('.addBeat').attr('disabled',false);
                    $("#addNewBeat").find("#beatcity").select2("destroy");
                    $("#addNewBeat").find("#beatcity").select2({
                      placeholder: 'Select City',
                    });
                    alert('Beat Successfully Created');
                  }else{
                    alert('Beat Already Exists or Beat with empty party given');
                  }
                  $('.addBeat').attr('disabled',false);
              },
              error:function(){
                $('.addBeat').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });
      });

      $('#tbl_beats').on('click','.beat-view',function(event){
          event.preventDefault();
          var url = $(this).attr('data-url');
          let title = $(this).data('name');
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "GET",
              data: {
                  '_token': '{{csrf_token()}}',
              },
              success: function (data) {
                  if(data['result'] == "No parties available"){
                    alert(data['result']);
                  }else{
                    var j=0;
                    $("#beat_parties ul").empty();
                    $.each(data['name'], function(){
                      $('<li></li>').val(data['name'][j]).text(data['name'][j]).appendTo('#beat_parties ul');
                      j++;
                    })
                    $('#modalShowBeat').modal('show');
                    $('#modalShowBeat').find('.show-beat-name').html('<span>'+title+'</span>');
                  }
              }
          });

      });

      $('#tbl_beats').on('click','.beat-edit',function(event){
          event.preventDefault();
          var url = $(this).attr('data-edit-url');
          var update_url = $(this).attr('data-url');
          let cityVal = $(this).data('city');
          let beatId = $(this).data('bid');
          $('#editBeatName').val($(this).attr('data-name'));
          $('#updateBeatSettings').find('#editbeat_id').val(beatId);
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "GET",
              data: {
                  '_token': '{{csrf_token()}}',
                  'cityVal': cityVal
              },
              success: function (data) {
                  $('#modalEditBeat').modal('show');
                  $(".edit_beatcity").val(cityVal);
                  $("#edit_beatcity").select2({
                    dropdownParent: $("#modalEditBeat"),
                    placeholder: 'Select City',
                  });
                  $('#assignPartyId').empty();
                  $.each(data['all_beat_party'],function(i,v){
                    if(data['selected_beat_party'].includes(v.id)){
                      $('<option></option>').val(v.id).text(v.company_name).attr('selected','selected').appendTo('#assignPartyId');
                    }else{
                      $('<option></option>').val(v.id).text(v.company_name).appendTo('#assignPartyId');
                    }
                  });
                  $('#updateBeatSettings').attr('action',update_url);
                  $('#updateBeatSettings #assignPartyId').multiselect('reload');
                  $('#updateBeatSettings #assignPartyId').multiselect({
                    columns: 1,
                    placeholder: 'Select party',
                    search: true,
                    selectAll: true,
                  });
                  $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').parent().css('cursor','pointer');
                  $.each(data['other_beat_party'],function(i,v){
                    $('#updateBeatSettings').find('#ms-list-2 input[value="'+v+'"]').prop('disabled',true);
                    let label_id = $('#updateBeatSettings').find('#ms-list-2 input[value="'+v+'"]').attr("id");
                    $('label[for="'+label_id+'"]').css('cursor','not-allowed');
                    $('label[for="'+label_id+'"]').css('background-color','#efefef');
                    $('label[for="'+label_id+'"]').css('color','grey');
                  });
                  $.each(data['selected_beat_party'],function(i,v){
                    let label_id = $('#updateBeatSettings').find('#ms-list-2 input[value="'+v+'"]').attr("id");
                    $('label[for="'+label_id+'"]').css('color','#800080');
                  });
                  $('#updateBeatSettings #assignPartyId').multiselect('refresh');
              }
          });
      });

      $('#updateBeatSettings').on('submit',function(event){
        event.preventDefault();
        var beat_id = $('#editbeat_id').val();
        var data = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data'  : data,
              },
              beforeSend:function(){
                $('.updateBeat').attr('disabled','disabled');
              },
              success: function (data) {
                if(data['result']==true){
                  checkAssignParties();
                  alert('Beat Updated');
                  $('#tbody_beats').empty();
                  $('#tbody_beats').html(data['beats']);
                }else{
                  alert("Can't Update Beat. Beat Already Exists or Beat with empty party given.");
                }
                $('#modalEditBeat').modal('hide');
                $('.updateBeat').attr('disabled',false);
              },
              error:function(jqXHR){
                debugger;
                $('.updateBeat').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });
      });

      $('#ajaxRemoveBeat').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: "POST",
          data: {
              '_token': '{{csrf_token()}}',
          },
          beforeSend:function(){
            $('#btn-beatDelete-key').attr('disabled',true);
              },
          success: function (data) {
            if(data['result']==false){
              alert('Beat could not be deleted');
            }else{
              $('#deletebeat').modal('hide');
              $('#tbody_beats').empty();
              checkAssignParties();
              $('#tbody_beats').html(data['beats']);
              $('#btn-beatDelete-key').attr('disabled',false);
              alert('Beat deleted successfully');
            }
          },
          error:function(){
             $('#btn-beatDelete-key').attr('disabled',false);
            alert('Oops! Something went Wrong');
          }
        });
      });


  </script>

  {{-- @if(config('settings.party_wise_rate_setup')==1)
    @include('company.settings.rate_setup.parties_rate_js')
  @endif --}}

  @if(config('settings.ncal')==1)
    <script>
      $(function(){
          var today = moment().format('YYYY-MM-DD');
          var currentYear = moment().year();
          var currentMonth = moment().month()+1;
          var currentDay = moment().date();
          var Weekday = moment().day();

          var NepaliCurrentDate = AD2BS(today);

          var nepaliDateData = NepaliCurrentDate.split('-');
          var nepaliCurrentYear = nepaliDateData[0];
          var nepaliCurrentMonth = nepaliDateData[1];
          var nepaliCurrentDay = nepaliDateData[2];

          $('.fc-next-button').click(function(e){
            e.preventDefault(e);
            var getMonth = parseInt($('#calNepaliMonth').val())+1;
            var getYear = parseInt($('#calNepaliYear').val());
            if(getMonth>12){
              getMonth = 1;
              getYear = getYear+1;
            }
            if(getMonth<10){
              getMonth = '0'+getMonth;
            }
            var firstEnd = getFirstDateEndDate(getYear,getMonth);
            engFirstDate = BS2AD(firstEnd[0]);
            engLastDate = BS2AD(firstEnd[11]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.holidays.getCalendar')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                }
            });
          });
          $('.fc-prev-button').click(function(e){
            e.preventDefault(e);
            getMonth = parseInt($('#calNepaliMonth').val())-1;
            getYear = parseInt($('#calNepaliYear').val());
            if(getMonth<1){
              getMonth = 12;
              getYear = getYear-1;
            }
            if(getMonth<10){
              getMonth = '0'+getMonth;
            }
            var firstEnd = getFirstDateEndDate(getYear,getMonth);
            engFirstDate = BS2AD(firstEnd[0]);
            engLastDate = BS2AD(firstEnd[11]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.holidays.getCalendar')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                }
            });
          });

          $('#todayMonth').click(function(e){
            e.preventDefault(e);
            getMonth = nepaliCurrentMonth;
            getYear = nepaliCurrentYear;
            if(getMonth<1){
              getMonth = 12;
              getYear = getYear-1;
            }
            var firstEnd = getFirstDateEndDate(getYear,getMonth);
            engFirstDate = BS2AD(firstEnd[0]);
            engLastDate = BS2AD(firstEnd[11]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.holidays.getCalendar')}}",
                type: "POST",
                data: {
                    '_token': '{{csrf_token()}}',
                    'getMonth': getMonth,
                    'getYear': getYear,
                    'engFirstDate': engFirstDate,
                    'engLastDate': engLastDate,
                },
                success: function (data) {
                    $('#calNepaliYear').val(data['year']);
                    $('#calNepaliMonth').val(data['month']);
                    var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                    $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                    $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                    $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                    $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                    $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                    $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                    $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                    $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
                }
            });
          });
          $('#todayMonth').click();
      });

      $('#delete_event').on('submit', function (event) {
          event.preventDefault();
          var del_id = $('#del_id').val();
          var del_url = '{{domain_route('company.admin.holidays.delete')}}';
          var getMonth = parseInt($('#calNepaliMonth').val());
          var getYear = parseInt($('#calNepaliYear').val());
          var firstEnd = getFirstDateEndDate(getYear,getMonth);
          engFirstDate = BS2AD(firstEnd[0]);
          engLastDate = BS2AD(firstEnd[11]);
          if(getMonth<10){
            getMonth = '0'+getMonth;
          }
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: del_url,
              type: "POST",
              data: {
                '_token': '{{csrf_token()}}',
                'del_id': del_id,
                'getMonth': getMonth,
                'getYear': getYear,
                'engFirstDate': engFirstDate,
                'engLastDate': engLastDate,
              },
              beforeSend:function(){
                $('#keyDeleteHoliday').attr('disabled',true);
              },
              success: function (data) {
                  alert('Holiday Deleted Successfully');
                  $('#del_event_modal').modal('hide');
                  $('#keyDeleteHoliday').attr('disabled',false);
                  $('#calNepaliYear').val(data['year']);
                  $('#calNepaliMonth').val(data['month']);
                  var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                  $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                  $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                  var firstEnd = getFirstDateEndDate(data['year'],data['month']);
                  $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              },
              error:function(){
                $('#keyDeleteHoliday').attr('disabled',false);
                alert('Oops! Something went Wrong');
              }
          });
      });

      $('#AddNewHoliday').on('submit', function (event) {
          event.preventDefault();
          var edit_url = '{{domain_route('company.admin.holidays.store')}}';
          var getMonth = parseInt($('#calNepaliMonth').val());
          var getYear = parseInt($('#calNepaliYear').val());
          var name = $('#addHName').val();
          var description = $('#editHName').val();
          var start_date = $('#add_start_dateAD').val();
          var end_date = $('#add_end_dateAD').val();
          var firstEnd = getFirstDateEndDate(getYear,getMonth);
          engFirstDate = BS2AD(firstEnd[0]);
          engLastDate = BS2AD(firstEnd[11]);
          if(getMonth<10){
            getMonth = '0'+getMonth;
          }
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: edit_url,
              type: "POST",
              data: {
                '_token': '{{csrf_token()}}',
                'getMonth': getMonth,
                'getYear': getYear,
                'start_date':start_date,
                'end_date':end_date,
                'engFirstDate': engFirstDate,
                'engLastDate': engLastDate,
                'name':name,
                'description':description,
              },
              beforeSend:function(){
                $('#btn_add_holiday').attr('disabled',true);
              },
              success: function (data) {
                  alert('Holiday Created Successfully');
                  $('#modalNewHoliday').modal('hide');
                  $('#calNepaliYear').val(data['year']);
                  $('#calNepaliMonth').val(data['month']);
                  $('#btn_add_holiday').attr('disabled',false);
                  var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                  $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                  $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                  var firstEnd = getFirstDateEndDate(data['year'],data['month']);
                  $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              },
              error:function(){
                $('#btn_add_holiday').attr('disabled',false);
                alert('Oops! Something went Wrong');
              }
          });
      });

      $('#EditHoliday').on('submit', function (event) {
          event.preventDefault();
          var edit_id = $('#edit_id').val();
          var edit_url = '{{domain_route('company.admin.holidays.edit')}}';
          var getMonth = parseInt($('#calNepaliMonth').val());
          var getYear = parseInt($('#calNepaliYear').val());
          var name = $('#edit_hname').val();
          var description = $('#edit_description').val();
          var start_date = $('#fromDate').val();
          var end_date = $('#to_date').val();
          var firstEnd = getFirstDateEndDate(getYear,getMonth);
          engFirstDate = BS2AD(firstEnd[0]);
          engLastDate = BS2AD(firstEnd[11]);
          if(getMonth<10){
            getMonth = '0'+getMonth;
          }
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: edit_url,
              type: "POST",
              data: {
                '_token': '{{csrf_token()}}',
                'edit_id': edit_id,
                'getMonth': getMonth,
                'getYear': getYear,
                'start_date':start_date,
                'end_date':end_date,
                'engFirstDate': engFirstDate,
                'engLastDate': engLastDate,
                'name':name,
                'description':description,
              },
              beforeSend:function(){
                $('#keyEditHoliday').attr('disabled',true);
              },
              success: function (data) {
                  alert('Holiday Updated Successfully');
                  $('#fullCalModal').modal('hide');
                  $('#calNepaliYear').val(data['year']);
                  $('#calNepaliMonth').val(data['month']);
                  $('#keyEditHoliday').attr('disabled',false);
                  var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                  $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                  $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                  var firstEnd = getFirstDateEndDate(data['year'],data['month']);
                  $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              },
              error:function(){
                $('#keyEditHoliday').attr('disabled',false);
                alert('Oops! Something went Wrong');
              }
          });
      });

      $('#populate').click(function (e) {
          e.preventDefault();
          $('#PopulateModal').modal('show');
      });

      $('#populates').click(function () {
          var NepaliEndDates = [
          [2001,31,31,32,31,31,31,30,29,30,29,30,30],
          [2002,31,31,32,32,31,30,30,29,30,29,30,30],
          [2003,31,32,31,32,31,30,30,30,29,29,30,31],
          [2004,30,32,31,32,31,30,30,30,29,30,29,31],
          [2005,31,31,32,31,31,31,30,29,30,29,30,30],
          [2006,31,31,32,32,31,30,30,29,30,29,30,30],
          [2007,31,32,31,32,31,30,30,30,29,29,30,31],
          [2008,31,31,31,32,31,31,29,30,30,29,29,31],
          [2009,31,31,32,31,31,31,30,29,30,29,30,30],
          [2010,31,31,32,32,31,30,30,29,30,29,30,30],
          [2011,31,32,31,32,31,30,30,30,29,29,30,31],
          [2012,31,31,31,32,31,31,29,30,30,29,30,30],
          [2013,31,31,32,31,31,31,30,29,30,29,30,30],
          [2014,31,31,32,32,31,30,30,29,30,29,30,30],
          [2015,31,32,31,32,31,30,30,30,29,29,30,31],
          [2016,31,31,31,32,31,31,29,30,30,29,30,30],
          [2017,31,31,32,31,31,31,30,29,30,29,30,30],
          [2018,31,32,31,32,31,30,30,29,30,29,30,30],
          [2019,31,32,31,32,31,30,30,30,29,30,29,31],
          [2020,31,31,31,32,31,31,30,29,30,29,30,30],
          [2021,31,31,32,31,31,31,30,29,30,29,30,30],
          [2022,31,32,31,32,31,30,30,30,29,29,30,30],
          [2023,31,32,31,32,31,30,30,30,29,30,29,31],
          [2024,31,31,31,32,31,31,30,29,30,29,30,30],
          [2025,31,31,32,31,31,31,30,29,30,29,30,30],
          [2026,31,32,31,32,31,30,30,30,29,29,30,31],
          [2027,30,32,31,32,31,30,30,30,29,30,29,31],
          [2028,31,31,32,31,31,31,30,29,30,29,30,30],
          [2029,31,31,32,31,32,30,30,29,30,29,30,30],
          [2030,31,32,31,32,31,30,30,30,29,29,30,31],
          [2031,30,32,31,32,31,30,30,30,29,30,29,31],
          [2032,31,31,32,31,31,31,30,29,30,29,30,30],
          [2033,31,31,32,32,31,30,30,29,30,29,30,30],
          [2034,31,32,31,32,31,30,30,30,29,29,30,31],
          [2035,30,32,31,32,31,31,29,30,30,29,29,31],
          [2036,31,31,32,31,31,31,30,29,30,29,30,30],
          [2037,31,31,32,32,31,30,30,29,30,29,30,30],
          [2038,31,32,31,32,31,30,30,30,29,29,30,31],
          [2039,31,31,31,32,31,31,29,30,30,29,30,30],
          [2040,31,31,32,31,31,31,30,29,30,29,30,30],
          [2041,31,31,32,32,31,30,30,29,30,29,30,30],
          [2042,31,32,31,32,31,30,30,30,29,29,30,31],
          [2043,31,31,31,32,31,31,29,30,30,29,30,30],
          [2044,31,31,32,31,31,31,30,29,30,29,30,30],
          [2045,31,32,31,32,31,30,30,29,30,29,30,30],
          [2046,31,32,31,32,31,30,30,30,29,29,30,31],
          [2047,31,31,31,32,31,31,30,29,30,29,30,30],
          [2048,31,31,32,31,31,31,30,29,30,29,30,30],
          [2049,31,32,31,32,31,30,30,30,29,29,30,30],
          [2050,31,32,31,32,31,30,30,30,29,30,29,31],
          [2051,31,31,31,32,31,31,30,29,30,29,30,30],
          [2052,31,31,32,31,31,31,30,29,30,29,30,30],
          [2053,31,32,31,32,31,30,30,30,29,29,30,30],
          [2054,31,32,31,32,31,30,30,30,29,30,29,31],
          [2055,31,31,32,31,31,31,30,29,30,29,30,30],
          [2056,31,31,32,31,32,30,30,29,30,29,30,30],
          [2057,31,32,31,32,31,30,30,30,29,29,30,31],
          [2058,30,32,31,32,31,30,30,30,29,30,29,31],
          [2059,31,31,32,31,31,31,30,29,30,29,30,30],
          [2060,31,31,32,32,31,30,30,29,30,29,30,30],
          [2061,31,32,31,32,31,30,30,30,29,29,30,31],
          [2062,30,32,31,32,31,31,29,30,29,30,29,31],
          [2063,31,31,32,31,31,31,30,29,30,29,30,30],
          [2064,31,31,32,32,31,30,30,29,30,29,30,30],
          [2065,31,32,31,32,31,30,30,30,29,29,30,31],
          [2066,31,31,31,32,31,31,29,30,30,29,29,31],
          [2067,31,31,32,31,31,31,30,29,30,29,30,30],
          [2068,31,31,32,32,31,30,30,29,30,29,30,30],
          [2069,31,32,31,32,31,30,30,30,29,29,30,31],
          [2070,31,31,31,32,31,31,29,30,30,29,30,30],
          [2071,31,31,32,31,31,31,30,29,30,29,30,30],
          [2072,31,32,31,32,31,30,30,29,30,29,30,30],
          [2073,31,32,31,32,31,30,30,30,29,29,30,31],
          [2074,31,31,31,32,31,31,30,29,30,29,30,30],
          [2075,31,31,32,31,31,31,30,29,30,29,30,30],
          [2076,31,32,31,32,31,30,30,30,29,29,30,30],
          [2077,31,32,31,32,31,30,30,30,29,30,29,31],
          [2078,31,31,31,32,31,31,30,29,30,29,30,30],
          [2079,31,31,32,31,31,31,30,29,30,29,30,30],
          [2080,31,32,31,32,31,30,30,30,29,29,30,30],
          [2081,31,31,32,32,31,30,30,30,29,30,30,30],
          [2082,30,32,31,32,31,30,30,30,29,30,30,30],
          [2083,31,31,32,31,31,30,30,30,29,30,30,30],
          [2084,31,31,32,31,31,30,30,30,29,30,30,30],
          [2085,31,32,31,32,30,31,30,30,29,30,30,30],
          [2086,30,32,31,32,31,30,30,30,29,30,30,30],
          [2087,31,31,32,31,31,31,30,30,29,30,30,30],
          [2088,30,31,32,32,30,31,30,30,29,30,30,30],
          [2089,30,32,31,32,31,30,30,30,29,30,30,30],
          [2090,30,32,31,32,31,30,30,30,29,30,30,30]
        ];

        var today = moment().format('YYYY-MM-DD');
        var currentYear = moment().year();
        var currentMonth = moment().month()+1;
        var currentDay = moment().date();
        var Weekday = moment().day();

        var NepaliCurrentDate = AD2BS(today);

        var nepaliDateData = NepaliCurrentDate.split('-');
        var nepaliCurrentYear = nepaliDateData[0];
        var nepaliCurrentMonth = nepaliDateData[1];
        var nepaliCurrentDay = nepaliDateData[2];

        var offtype = $('#offtype').val();
        var rangetype = $('#rangetype').val();
        var nYear = $('#calNepaliYear').val();
        var nMonth = $('#calNepaliMonth').val();
        var firstEnd = getFirstDateEndDate(getYear,getMonth);
        engFirstDate = BS2AD(firstEnd[0]);
        engLastDate = BS2AD(firstEnd[11]);
        for(i=0;i<=89;i++){
          if(NepaliEndDates[i][0]==nYear){
            var selectedYear =  NepaliEndDates[i];
          }
        }
        var lastMonthEndDate = selectedYear[12];
        var yearEndDate = BS2AD(nYear+'-12-'+lastMonthEndDate);
        if(nYear == nepaliCurrentYear){
          start = moment().format('YYYY-MM-DD');
        }else if(nYear > NepaliCurrentDate){
          start = BS2AD(nYear+'-01-01');
        }else{
          alert("Can't populate past years");
        }
        var r = confirm("Are you sure you want to populate current Year?");
        if (r == true) {
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{domain_route('company.admin.holidays.populate')}}",
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'start': start,
                  'yearEndDate':yearEndDate,
                  'offtype': offtype,
                  'rangetype': rangetype,
                  'getYear':nYear,
                  'getMonth':nMonth,
                  'engFirstDate': engFirstDate,
                  'engLastDate': engLastDate,
              },
              success: function (data) {
                alert(data['result']);
                $('#calNepaliYear').val(data['year']);
                $('#calNepaliMonth').val(data['month']);
                $('#PopulateModal').modal('hide');
                var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                var firstEnd = getFirstDateEndDate(data['year'],data['month']);
                $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              }
            });
        } else {
            alert('populating canceled');
        }
      });

      $('#BtnAddNewHoliday').on('click',function(e){
        e.preventDefault();
        $('#modalNewHoliday').modal('show');
        $('#btn_add_holiday').removeAttr('disabled');
      });

      var lastMonthNepaliDate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
      var pdates = lastMonthNepaliDate.split("-");
      var pdate = pdates[1]+'/'+pdates[2]+'/'+pdates[0];
      $('#add_start_date').nepaliDatePicker({
        disableBefore: pdate,
        onChange: function(){
          $('#add_start_dateAD').val(BS2AD($('#add_start_date').val()));
          if($('#add_end_date').val()<$('#add_start_date').val()){
            $('#add_end_date').val($('#add_start_date').val());
          }
        }
      });

      $('#add_end_date').nepaliDatePicker({
        disableBefore:pdate,
        onChange: function(){
          $('#add_end_dateAD').val(BS2AD($('#add_end_date').val()));
          if($('#add_start_date').val()>$('#add_end_date').val()){
            $('#add_start_date').val($('#add_end_date').val());
          }
        }
      });

      $('#calendar').on('click','.fa-edit',function(e){
        e.preventDefault();
        $('#edit_id').val($(this).attr('data-id'));
        $('#fullCalModal').modal('show');
        $('#edit_hname').val($(this).attr('data-name'));
        $('#edit_description').val($(this).attr('data-desc'));
        $('#edit_start_date').val(AD2BS($(this).attr('data-start')));
        $('#edit_end_date').val(AD2BS($(this).attr('data-end')));
        $('#edit_end_date').removeAttr('disabled');
      });
      $('#edit_start_date').nepaliDatePicker({
        disableBefore:pdate,
        onChange: function(){
          $('#fromDate').val(BS2AD($('#edit_start_date').val()));
          if($('#edit_end_date').val()<$('#edit_start_date').val()){
            $('#edit_end_date').val($('#edit_start_date').val());
          }
        }
      });
      $('#edit_end_date').nepaliDatePicker({
        disableBefore:pdate,
        onChange: function(){
          $('#to_date').val((BS2AD($('#edit_end_date').val())));
          if($('#edit_start_date').val()>$('#edit_end_date').val()){
            $('#edit_start_date').val($('#edit_end_date').val());
          }
        }
      });
      $('#calendar').on('click','.fa-trash',function(e){
        e.preventDefault();
        $('#del_id').val($(this).attr('data-id'));
        $('#del_calYear').val($('#calNepaliYear').val());
        $('#del_calMonth').val($('#calNepaliMonth').val());
        $('#del_event_modal').modal('show');
      });

      $('#calendarBody').on('click','td',function(){
        if(typeof $(this).attr('data-date')!="undefined"){
          $('#modalNewHoliday').modal('show');
          $('#add_start_date').val($(this).attr('data-date'));
          $('#add_start_dateAD').val(BS2AD($(this).attr('data-date')));
          $('#add_end_date').val($(this).attr('data-date'));
          $('#add_end_dateAD').val(BS2AD($(this).attr('data-date')));
        }
      });


    </script>

  @else
  <script>
      // Holiday Section js

      $(function () {

          $('#populate').click(function (e) {
              e.preventDefault();
              $('#PopulateModal').modal('show');
          });

          $('#populates').click(function () {
              var offtype = $('#offtype').val();
              var rangetype = $('#rangetype').val();
              var moment = $('#calendar').fullCalendar('getDate').format('Y-M-D');
              var momentYear = $('#calendar').fullCalendar('getDate').format('Y');
              var today = new Date();
              var currentDate = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate()
              var currentYear = today.getFullYear();
              if (momentYear == currentYear) {
                  moment = currentDate;
              }
              var r = confirm("Are you sure you want to populate current Year?");
              if (r == true) {
                  $.ajax({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: "{{domain_route('company.admin.holidays.populate')}}",
                      type: "POST",
                      data: {
                          '_token': '{{csrf_token()}}',
                          'currentDate': moment,
                          'offtype': offtype,
                          'rangetype': rangetype,
                      },
                      success: function (data) {
                          alert(data['result']);
                          window.location = '{{domain_route('company.admin.setting')}}';
                      }
                  });
              } else {
                  alert('populating canceled');
              }
          });

          var monthago = moment();
          monthago = monthago.subtract(30,'days');
          monthago = monthago.format('YYYY-MM-DD');

          $('.fromdate').datepicker({
              startDate: monthago,
              setDate: new Date(),
              format: 'yyyy-mm-dd',
              todayHighlight: true,
              autoclose: true,
          });

          $('.todate').datepicker({
              startDate: monthago,
              format: 'yyyy-mm-dd',
              todayHighlight: true,
              autoclose: true,
          }).attr('disabled');

          $('.fromdate').datepicker('setDate', new Date());
          $('.todate').datepicker('setDate', new Date());

          $('.fromdate').change(function (event) {
              event.preventDefault();
              var newdate = $(this).val();
              $('.todate').datepicker('remove');
              if ($('#edit_end_date').val() < $('#edit_start_date').val()) {
                  $('#edit_end_date').val(newdate);
              }
              if ($('#add_end_date').val() < $('#add_start_date').val()) {
                  $('.todate').val(newdate);
              }
              $('.todate').datepicker({
                  startDate: newdate,
                  format: 'yyyy-mm-dd',
                  todayHighlight: true,
                  autoclose: true,
              }).removeAttr('disabled');
              $('.todate').datepicker('setDate', $('#edit_end_date').val());
              $('.todate').datepicker('setDate', $('#add_end_date').val());
          });

          $('.createholiday').click(function () {
              $('#AddNewHoliday')[0].reset();
              $('#exampleModalCenter').modal('show');
              $('.todate').prop('disabled', 'true');
          });

          $('#AddNewHoliday').on('submit', function (event) {
              event.preventDefault();
              var currentElement = $(this);
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: currentElement.attr('action'),
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#btn_add_holiday').attr('disabled','disabled');
                  },
                  success: function (data) {
                      if (data['result']) {
                          alert(data['result']);
                          $('#exampleModalCenter').modal('hide');
                          $('#AddNewHoliday').trigger('reset');
                          var eventObject={
                            title          : data['name'],
                            start          : data['start_date'],
                            end            : data['nextday'],
                            backgroundColor: '#f56954', //red
                            borderColor    : '#f56954', //red
                            id             : data['id'],
                            allDay         :  true,
                            description    : data['description'],
                            begin          : data['start_date'],
                            finish         : data['end_date'],
                          }
                          var calendar = $('#calendar').fullCalendar('renderEvent', eventObject,true);
                          $('#btn_add_holiday').removeAttr('disabled');
                      }
                  },
                  error:function(){
                    $('#btn_add_holiday').removeAttr('disabled');
                    alert('Oops! Something went Wrong');
                  }
              });
          });


          $('#EditHoliday').on('submit', function (event) {
              event.preventDefault();
              var edit_id = $('#edit_eid').val();
              var title = $('#edit_ename').val();
              var description = $('#edit_description').val();
              var edit_start_date = $('#edit_start_date').val();
              var edit_end_date = $('#edit_end_date').val();
              $('#fullCalModal').modal('hide');
              var currentElement = $(this);
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: currentElement.attr('action'),
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#keyEditHoliday').attr('disabled',true);
                  },
                  success: function (data) {
                      if (data['result']) {
                          alert(data['result']);
                          $('#exampleModalCenter').modal('hide');
                          $('#EditHoliday').trigger('reset');
                          var eventObject={
                            title          : title,
                            start          : edit_start_date,
                            end            : data['nextday'],
                            backgroundColor: '#f56954', //red
                            borderColor    : '#f56954', //red
                            id             : edit_id,
                            description    : description,
                            begin          : edit_start_date,
                            finish         : edit_end_date,
                          }
                          $('#calendar').fullCalendar('removeEvents', edit_id);
                          $('#calendar').fullCalendar('renderEvent', eventObject,true);
                      } else {
                          alert('Holiday Updated Failed');
                      }
                      $('#keyEditHoliday').attr('disabled',false);
                  },
                  error:function(){
                      $('#keyEditHoliday').attr('disabled',false);
                      alert('Oops! Something went Wrong');
                  }
              });
          });

          $('#delete_event').on('submit', function (event) {
              event.preventDefault();
              var event_del_id = $('#del_id').val();
              var del_url = '{{domain_route('company.admin.holidays.delete')}}';
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: del_url,
                  type: "POST",
                  data: new FormData(this),
                  contentType: false,
                  cache: false,
                  processData: false,
                  beforeSend:function(){
                    $('#keyDeleteHoliday').attr('disabled',true);
                  },
                  success: function (data) {
                      alert('Holiday Deleted Successfully');
                      $('#del_event_modal').modal('hide');
                      $('#calendar').fullCalendar('removeEvents', event_del_id);
                      $('#keyDeleteHoliday').attr('disabled',false);
                  },
                  error:function(){
                    $('#keyDeleteHoliday').attr('disabled',false);
                    alert('Oops! Something went Wrong');
                  }
              });
          });

          /* initialize the external events
           -----------------------------------------------------------------*/
          function init_events(ele) {
              ele.each(function () {

                  // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                  // it doesn't need to have a start or end
                  var eventObject = {
                      title: $.trim($(this).text()) // use the element's text as the event title
                  }

                  // store the Event Object in the DOM element so we can get to it later
                  $(this).data('eventObject', eventObject)

                  // make the event draggable using jQuery UI
                  // $(this).draggable({
                  //   zIndex        : 1070,
                  //   revert        : true, // will cause the event to go back to its
                  //   revertDuration: 0  //  original position after the drag
                  // })

              })
          }

          init_events($('#external-events div.external-event'))

          /* initialize the calendar
           -----------------------------------------------------------------*/
          //Date for the calendar events (dummy data)
          var date = new Date()
          var d = date.getDate(),
              m = date.getMonth(),
              y = date.getFullYear()
          var calendar = $('#calendar').fullCalendar({
              header: {
                  left: 'prev,next today',
                  center: 'title',
                  right: 'month,agendaWeek,agendaDay'
              },

              buttonText: {
                  today: 'today',
                  month: 'month',
                  week: 'week',
                  day: 'day'
              },
              dayClick: function (date, jsEvent, view) {
                  if (monthago <= date.format()) {
                      $('#AddNewHoliday')[0].reset();
                      $('.fromdate').datepicker('destroy');
                      $('.fromdate').datepicker({
                          startDate: monthago,
                          format: 'yyyy-mm-dd',
                          todayHighlight: true,
                          autoclose: true,
                      });
                      $('.fromdate').datepicker('setDate', date.format());
                      $('#add_start_date').val(date.format('YYYY-MM-DD'));
                      $('#add_end_date').val(date.format('YYYY-MM-DD'));
                      $('#exampleModalCenter').modal();
                  }else{
                    alert("Can't create holiday before a month ago");
                  }
              },
              eventMouseover: function (event, jsEvent, view) {
                  $(this).attr('title', event.title);
              },
              eventRender: function (event, element, view) {
                  var j = document.createElement('i');
                  j.className = 'fa';
                  j.classList.add("fa-edit");
                  j.classList.add("btn");
                  j.classList.add("grey-mint");
                  j.classList.add("btn-xs");
                  j.addEventListener("click", function () {
                    if (monthago <= event.start.format()) {
                      $('#edit_eid').val(event.id);
                      $('#fullCalModal').modal('show');
                      $('#edit_ename').val(event.title);
                      $('#edit_description').val(event.description);
                      $('#edit_start_date').val(event.begin);
                      $('#edit_end_date').val(event.finish);
                      $('.fromdate').datepicker('destroy');
                      $('.todate').datepicker('destroy');
                      $('.fromdate').datepicker({
                          startDate: monthago,
                          format: 'yyyy-mm-dd',
                          todayHighlight: true,
                          autoclose: true,
                      });
                      $('.todate').datepicker({
                          startDate: monthago,
                          format: 'yyyy-mm-dd',
                          todayHighlight: true,
                          autoclose: true,
                      });
                      $('.fromdate').datepicker('setDate', event.begin);
                      $('.todate').datepicker('setDate', event.finish);
                      $('#edit_end_date').removeAttr('disabled');
                    }else{
                      alert('Can not edit older dates');
                    }
                  });
                  element.find('div.fc-content span.fc-title').prepend(j);
                  var i = document.createElement('i');
                  i.className = 'fa';
                  i.classList.add("fa-trash");
                  // i.classList.add("pull-right");
                  i.classList.add("btn");
                  i.classList.add("grey-mint");
                  i.classList.add("btn-xs");
                  i.addEventListener("click", function () {
                      $('#del_id').val(event.id);
                      $('#del_event_modal').modal('show');
                  });
                  element.find('div.fc-content span.fc-title').prepend(i);
              },
              //Random default events
              events: [
                  @foreach($holidays as $holiday)
                  {
                      title: '{{$holiday->name}}',
                      start: '{{$holiday->start_date}}',
                      end: '{{$data['nextday_end'][$holiday->id]}}',
                      backgroundColor: '#f56954', //red
                      borderColor: '#f56954', //red
                      id: '{{$holiday->id}}',
                      allDay:  true,
                      description: '{{$holiday->description}}',
                      begin          : '{{$holiday->start_date}}',
                      finish: '{{$holiday->end_date}}',
                  },
                @endforeach
              ],
              editable: false,
              droppable: false, // this allows things to be dropped onto the calendar !!!

          });

          /* ADDING EVENTS */
          var currColor = '#3c8dbc' //Red by default
          //Color chooser button
          var colorChooser = $('#color-chooser-btn')
          $('#color-chooser > li > a').click(function (e) {
              e.preventDefault()
              //Save color
              currColor = $(this).css('color')
              //Add color effect to button
              $('#add-new-event').css({'background-color': currColor, 'border-color': currColor})
          })
          $('#add-new-event').click(function (e) {
              e.preventDefault()
              //Get value and make sure it is not null
              var val = $('#new-event').val()
              if (val.length == 0) {
                  return
              }

              //Create events
              var event = $('<div />')
              event.css({
                  'background-color': currColor,
                  'border-color': currColor,
                  'color': '#fff'
              }).addClass('external-event')
              event.html(val)
              $('#external-events').prepend(event)

              //Add draggable funtionality
              init_events(event)

              //Remove event from text input
              $('#new-event').val('')
          });
      });
</script>
@endif

<script>

      $('#partyId').multiselect({
          columns: 1,
          placeholder: 'Select party',
          search: true,
          selectAll: true,
      });
      $('#employeeId').multiselect({
          columns: 1,
          placeholder: 'Select Employee',
          search: true,
          selectAll: true
      });

      $('document').ready(function(){
        checkAssignParties();
      })

      $('#beatcity').change(function(){
        let selCity = $(this).val();
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ domain_route('company.admin.beat.fetchCityParties') }}",
          type: "GET",
          data:{
            "city": selCity,
            "beatId": ""
          },
          success: function(data){
            $("#addNewBeat").find("#partyId").html('');
            $("#addNewBeat").find("#partyId").multiselect('destroy');
            let parties = data['parties'];
            $.each(parties, function(id, value){
              $("#addNewBeat").find("#partyId").append(`<option value=${value['id']}>${value['company_name']}</option>`);
            });
            $("#addNewBeat").find("#partyId").multiselect('reload');

            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').attr('checked',false);
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').prop('disabled',false);
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').parent().css('cursor','pointer');
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').parent().css('color','#333');
            for(let count =0 ; count < data['assignedParties'].length; count++){
              $('#beats-detail').find('#ms-list-1 input[value="'+data['assignedParties'][count]+'"]').attr('checked','checked');
              $('#beats-detail').find('#ms-list-1 input[value="'+data['assignedParties'][count]+'"]').prop('disabled',true);
              $('#beats-detail').find('#ms-list-1 input[value="'+data['assignedParties'][count]+'"]').parent().css('background-color','#efefef');
              let label_id = $('#beats-detail').find('#ms-list-1 input[value="'+data['assignedParties'][count]+'"]').attr("id");
              $('label[for="'+label_id+'"]').css('color','gray');
              $('label[for="'+label_id+'"]').css('cursor','not-allowed');
            }
            $('#partyId').multiselect('refresh');
            $('#partyId').multiselect({
              columns: 1,
              placeholder: 'Select party',
              search: true,
              selectAll: true,
            });
            checkAssignParties();
          },
        })
      });

      $('#edit_beatcity').change(function(){
        let selCity = $(this).val();
        let beatId = $('#updateBeatSettings').find('#editbeat_id').val();

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ domain_route('company.admin.beat.fetchCityParties') }}",
          type: "GET",
          data:{
            "city": selCity,
            "beatId": beatId,
          },
          success: function(data){
            $("#updateBeatSettings").find("#assignPartyId").html('');
            $("#updateBeatSettings").find("#assignPartyId").multiselect('destroy');
            let parties = data['parties'];
            let assignedPartiesNotInThisBeat = data['assignedParties'];
            let beatParties = data['beatParties'];
            $.each(parties, function(id, value){
              if($.inArray(value['id'].toString(), beatParties)>=0){
                $("#updateBeatSettings").find("#assignPartyId").append(`<option value=${value['id']} selected>${value['company_name']}</option>`);
              }else{
                $("#updateBeatSettings").find("#assignPartyId").append(`<option value=${value['id']}>${value['company_name']}</option>`);

              }
            });
            $("#updateBeatSettings").find("#assignPartyId").multiselect('reload');
            $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').attr('checked',false);
            $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').prop('disabled',false);
            $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').parent().css('cursor','pointer');
            $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').parent().css('color','#333');
            for(let count =0 ; count < parties.length; count++){
              if($.inArray(parties[count]["id"], beatParties) >= 0){
                $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').attr('checked','checked');
                $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').parent().css('background-color','#efefef');
                $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').parent().css('color','rgb(128, 0, 128)');
              }else if($.inArray(parties[count]["id"], assignedPartiesNotInThisBeat) >= 0){
                $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').prop('disabled',true);
                let label_id = $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').attr("id");
                $('label[for="'+label_id+'"]').css('color','gray');
                $('label[for="'+label_id+'"]').css('cursor','not-allowed');
              }
            }

            $("#updateBeatSettings").find("#assignPartyId").val(beatParties);

          },
        })
      });

      function checkAssignParties(){
        let selCity = $('#beatcity').val();
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ domain_route('company.admin.beat.assignedParties') }}",
          type: "GET",
          success: function (data) {
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').attr('checked',false);
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').prop('disabled',false);
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').parent().css('cursor','pointer');
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').parent().css('color','#333');
            for(let count =0 ; count < data.length; count++){
              $('#beats-detail').find('#ms-list-1 input[value="'+data[count]+'"]').attr('checked','checked');
              $('#beats-detail').find('#ms-list-1 input[value="'+data[count]+'"]').prop('disabled',true);
               $('#beats-detail').find('#ms-list-1 input[value="'+data[count]+'"]').parent().css('background-color','#efefef');
              let label_id = $('#beats-detail').find('#ms-list-1 input[value="'+data[count]+'"]').attr("id");
              $('label[for="'+label_id+'"]').css('color','gray');
              $('label[for="'+label_id+'"]').css('cursor','not-allowed');
            }
            $('#partyId').multiselect('refresh');
            $('#partyId').multiselect({
              columns: 1,
              placeholder: 'Select party',
              search: true,
              selectAll: true
            });
          },
          error:function(xhr){

          },
        });
      }

  </script>
   <script>
    $('#color').colorpicker({
      format: 'hex'
    });
    $('#edit_color_pick').colorpicker({
      format: 'hex'
    });

    $(function () {

        var table = $('#orderstatus').DataTable({
          "columnDefs": [ {
          "targets": -1,
          "orderable": false
          } ],
          "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
            "<'row'<'col-xs-6'><'col-xs-6'>>" +
            "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Order Status List',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Order Status List',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
                {
                    extend: 'print',
                    title: 'Order Status List',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
            ]

        });

        table.buttons().container().appendTo('#orderstatusexports');

        $('#addNewStatus').on('submit', function (event) {
            event.preventDefault();
            var currentElement = $(this);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: currentElement.attr('action'),
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                beforeSend:function(data){
                  $('#addkey').attr('disabled',true);
                },
                success: function (data) {
                  // if(data['result']== true){
                    alert('Created Successfully');
                  // }
                  $('#addkey').attr('disabled',false);
                  $('#addNewStatus')[0].reset();
                  $('#errlabel').html('');
                  $('#AddOrderStatus').modal('hide');
                  var btn = '<a class="btn btn-primary btn-sm rowEditOrderStatus" moduleAttribute-id="'+data["id"]+'" moduleAttribute-name="'+data["title"]+'" moduleAttribute-color="'+data['color']+'" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteOrderStatus"  moduleAttribute-id="'+data["id"]+'" moduleAttribute-name="'+data["title"]+'" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';
                  if(data["order_amt_flag"]==1){
                    data["order_amt_flag"] = "Yes";
                  }else{
                    data["order_amt_flag"] = "No";
                  }
                  if(data["order_edit_flag"]==1){
                    data["order_edit_flag"] = "<i class='fa fa-check'><span hidden>Yes</span>";
                  }else{
                    data["order_edit_flag"] = "<i class='fa fa-times'><span hidden>No</span>";
                  }
                  if(data["order_delete_flag"]==1){
                    data["order_delete_flag"] = "<i class='fa fa-check'><span hidden>Yes</span>";
                  }else{
                    data["order_delete_flag"] = "<i class='fa fa-times'><span hidden>No</span>";
                  }
                  table.row.add( [
                    // ++counter,
                    data["title"],
                    data["order_amt_flag"],
                    data["order_edit_flag"],
                    data["order_delete_flag"],
                    btn,
                  ] ).draw();
                  // window.location.href = "{{domain_route('company.admin.orderstatus')}}";
                },
                error:function(jqXHR, textStatus, errorThrown){
                  var err = JSON.parse(jqXHR.responseText);
                  $('#errlabel').html('<span>'+err['errors']['name'][0]+'</span>');
                  $('#addkey').attr('disabled',false);
                },
            });
        });

        $('#editOrderStatus').on('submit', function (event) {
            event.preventDefault();
            var edit_id = $('#edit_id').val();
            var url = "{{domain_route('company.admin.orderstatus.update')}}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                beforeSend:function(){
                  $('#editkey').attr('disabled',true);
                },
                success: function (data) {
                    $('#orderstatus tbody').empty();
                    $('#orderstatus').DataTable().clear().destroy();
                    $('#orderstatus tbody').html(data);
                    table = $('#orderstatus').DataTable({
                        "columnDefs": [ {
                        "targets": -1,
                        "orderable": false
                        } ],
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                title: 'Order Status List',
                                exportOptions: {
                                    columns: [0, 1]
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                title: 'Order Status List',
                                exportOptions: {
                                    columns: [0, 1]
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Order Status List',
                                exportOptions: {
                                    columns: [0, 1]
                                }
                            },
                        ]

                    });
                    alert('Updated Successfully');
                    $('#editkey').attr('disabled',false);
                    $('#EditOrderStatus').modal('hide');
                    $('#ederrlabel').html('');
                    $('#editOrderStatus')[0].reset();
                    // window.location.href = "{{domain_route('company.admin.orderstatus')}}";
                },
                error:function(jqXHR, textStatus, errorThrown){
                  var err = JSON.parse(jqXHR.responseText);
                  if(err['message']!='The given data was invalid.'){
                    $('#ederrlabel').html('<span>'+err['message']+'</span>');
                  }else{
                    $('#ederrlabel').html('<span>'+err['errors']['name'][0]+'</span>');
                  }
                  $('#editkey').attr('disabled',false);
                },
            });
        });

        $('#deleteExistingOrderStatus').on('submit', function (event) {
            event.preventDefault();
            var edit_id = $('#edit_id').val();
            var url = "{{domain_route('company.admin.orderstatus.delete')}}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                beforeSend:function(){
                  $('#delkey').attr('disabled',true);
                },
                success: function (data) {
                    alert(data['msg']);
                    $('#orderstatus tbody').empty();
                    $('#orderstatus').DataTable().clear().destroy();
                    $('#orderstatus tbody').html(data['view']);
                    table = $('#orderstatus').DataTable({
                        "columnDefs": [ {
                          "targets": -1,
                          "orderable": false
                        } ],
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                title: 'Order Status List',
                                exportOptions: {
                                    columns: [0, 1]
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                title: 'Order Status List',
                                exportOptions: {
                                    columns: [0, 1]
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Order Status List',
                                exportOptions: {
                                    columns: [0, 1]
                                }
                            },
                        ]

                    });
                    $('#delkey').attr('disabled',false);
                    $('#DeleteOrderStatus').modal('hide');
                    // window.location.href = "{{domain_route('company.admin.setting')}}";
                },
            });
        });

        $('#orderstatus').on('click', '.rowEditOrderStatus',function () {
            $('#edit_id').val($(this).attr('moduleAttribute-id'));
            if($(this).attr('moduleAttribute-name')=="Approved" || $(this).attr('moduleAttribute-name')=="Pending"){
            //   $('#aP_edit_color_pick').removeClass('hidden');
            //   $('#edit_color_pick').addClass('hidden');
              $('#edit_name').val($(this).attr('moduleAttribute-name')).prop('readonly', 'readonly');
            //   $('#edit_color').val($(this).attr('moduleAttribute-color')).prop('readonly', 'readonly');
            //   $('#aPedit_color').val($(this).attr('moduleAttribute-color')).prop('readonly', 'readonly');
            }else{
              $('#edit_name').val($(this).attr('moduleAttribute-name')).prop('readonly', false);
            }
            $('#edit_color_pick').removeClass('hidden');
            $('#aP_edit_color_pick').addClass('hidden');
            $('#edit_color').val($(this).attr('moduleAttribute-color')).prop('readonly', false);
            $('#aPedit_color').val($(this).attr('moduleAttribute-color')).prop('readonly', 'readonly');
            if($(this).attr('moduleAttribute-order_amt_flag')==1){
              $('#ed_order_amt_flag').prop('checked', true);
            }else{
              $('#ed_order_amt_flag').prop('checked', false);
            }
            if($(this).attr('moduleAttribute-order_edit_flag')==1){
              $('#ed_os_editable_flag').prop('checked', true);
            }else{
              $('#ed_os_editable_flag').prop('checked', false);
            }
            if($(this).attr('moduleAttribute-order_delete_flag')==1){
              $('#ed_os_deleteable_flag').prop('checked', true);
            }else{
              $('#ed_os_deleteable_flag').prop('checked', false);
            }
            $('#edit_color_pick').find('#color_span').children().css("background-color", $(this).attr('moduleAttribute-color'));
            $('#ederrlabel').html('');
            $('#EditOrderStatus').modal('show');
        });

        $('#orderstatus').on('click', '.rowDeleteOrderStatus',function () {
            $('#delete_id').val($(this).attr('moduleAttribute-id'));
            $('#delete_name').val($(this).attr('moduleAttribute-name'));
            $('#DeleteOrderStatus').modal('show');
            $('#del_title').html($(this).attr('moduleAttribute-name'));
        });

    });


</script>
<script>
  $(function () {
    initializeDT();
    $('#retTableView').on('click','.rowEditReturnReason', function () {
        $('#editreturn_reason_id').val($(this).attr('returnreason-id'));
        $('#editreturn_reason_name').val($(this).attr('returnreason-name'));
        $('#EditReturnReason').modal('show');
    });

    $('#retTableView').on('click','.rowDeleteReturnReason', function () {
        $('#delete_return_reason_id').val($(this).attr('returnreason-id'));
        $('#delete_return_reason_name').val($(this).attr('returnreason-name'));
        $('#DeleteReturnReason').modal('show');
        $('#del_title').html($(this).attr('returnreason-name'));
    });
    var returnTable;
    function initializeDT(){
      returnTable = $('#returnreason').DataTable({
        "columnDefs": [
          { "orderable": false, "targets": [-1] } // Applies the option to all columns
          ],
          "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
            "<'row'<'col-xs-6'><'col-xs-6'>>" +
            "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
          buttons: [
              {
                  extend: 'excelHtml5',
                  title: 'Return Reason',
                  exportOptions: {
                      columns: [0, 1]
                  }
              },
              {
                  extend: 'pdfHtml5',
                  title: 'Return Reason',
                  exportOptions: {
                      columns: [0, 1]
                  }
              },
              {
                  extend: 'print',
                  title: 'Return Reason',
                  exportOptions: {
                      columns: [0, 1]
                  }
              },
          ]

      });
    }

    returnTable.buttons().container().appendTo('#returnreasonsexports');

    $('#addNewReturnReason').on('submit', function (event) {
        event.preventDefault();
        var currentElement = $(this);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: currentElement.attr('action'),
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            cache: false,
            beforeSend:function(){
              $('#addkey').attr('disabled',true);
            },
            success: function (data) {
                if(data['result']==true){
                  alert('Return Reason created Successfully.');
                  returnTable.clear().draw();
                  $('#AddReturnReason').modal('hide');
                  $('#addNewReturnReason').trigger('reset');
                  let reasonData = data.reasonData;
                  for (i = 0; i < data['count']; i++) {
                    var editurl="{{ domain_route('company.admin.returnreason.edit',['id']) }}" ;
                    editurl=editurl.replace('id', data['returnReason'][i]['id']);
                    var delurl="{{ domain_route('company.admin.returnreason.destroy',['id']) }}" ; delurl=delurl.replace('id',data['returnReason'][i]['id']);
                    var submiturl="$(" + "'#" + data['returnReason'][i]['id'] + "').submit();" ;
                    if(reasonData.includes(String(data['returnReason'][i]['id']))){
                      returnTable.row.add([
                        i + 1,
                      data['returnReason'][i]['name'],
                      '<a  class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="' +
                      data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']
                      + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>' , ]).draw();
                    }else{
                      returnTable.row.add([
                        i + 1,
                      data['returnReason'][i]['name'],
                      '<a  class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="' +
                      data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']
                      + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteReturnReason" returnreason-id="'
                      + data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']
                      + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>' , ]).draw();
                    }
                  }
                }else{
                  alert('Sorry! Return Reason already exists.');
                }
                $('#addkey').attr('disabled',false);
                $('#AddReturnReasons').modal('hide');

            },
        });

    });

    $('#editReturnReason').on('submit', function (event) {
        event.preventDefault();
        var edit_id = $('#edit_id').val();
        var url = "{{domain_route('company.admin.returnreason.update')}}";
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            cache: false,
            beforeSend:function(){
              $('#updatekey').attr('disabled',true);
            },
            success: function (data) {
              if(data['result']==true){
                alert('Updated Successfully');
                returnTable.clear().draw();
                $('#EditReturnReason').modal('hide');
                $('#editExistingActivityType').trigger('reset');

                for (i = 0; i < data['count']; i++) {
                  var editurl="{{ domain_route('company.admin.returnreason.edit',['id']) }}" ;
                  editurl=editurl.replace('id', data['returnReason'][i]['id']);
                  var delurl="{{ domain_route('company.admin.returnreason.destroy',['id']) }}" ; delurl=delurl.replace('id',data['returnReason'][i]['id']);
                  var submiturl="$(" + "'#" + data['returnReason'][i]['id'] + "').submit();" ;
                  let reasonData = data.reasonData;
                  if(reasonData.includes(String(data['returnReason'][i]['id']))){
                    returnTable.row.add([
                    i + 1,
                    data['returnReason'][i]['name'],'<a class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="' +data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']+ '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>' , ]).draw();
                  }else{
                    returnTable.row.add([
                    i + 1,
                    data['returnReason'][i]['name'],
                    '<a  class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="' +data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']+ '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteReturnReason" returnreason-id="'+ data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name'] + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>' , ]).draw();
                  }
                }
              }else{
                alert('Sorry! Activity Type already exists.')
              }
              $('#updatekey').attr('disabled',false);
              $('#EditReturnReason').modal('hide');
            },
        });
    });
    $('#deleteReturnReason').on('submit', function (event) {
      event.preventDefault();
      var edit_id = $('#delete_return_reason_id').val();
      var url = "{{domain_route('company.admin.returnreason.delete')}}";
      $.ajax({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        cache: false,
        beforeSend:function(){
          $('#delreturnreasonkey').attr('disabled',true);
        },
        success: function (data) {
          $('#delreturnreasonkey').attr('disabled',false);
          $('#DeleteReturnReason').modal('hide');
          if(data==false){
            alert('Failed Deleting');
          }else{
            returnTable.destroy();
            $('#retTableView').html('');
            $('#retTableView').html(data);
            initializeDT();
            alert("Return Reason deleted successfully");
          }
        }
      });
    });
  });

  //Roles and Permissions
  @foreach($roles as $role)
  @foreach($permission_categories as $permission_category)
    var category = "{{$permission_category->name}}";
    var role = "{{$role->id}}";
    var max = '{{count($permission_category->permissions->where('enabled',1))}}';
    var totalmax = '{{count($permission_categories)}}';
    @if(config('settings.beat')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.tour_plans')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.stock_report')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.returns')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.collaterals')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.party')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.product')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.orders')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.collections')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.notes')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.activities')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.expenses')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.leaves')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.announcement')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.remarks')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.accounting')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.party_wise_rate_setup')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.retailer_app')==0)
    totalmax = totalmax-1;
    @endif
    @if(config('settings.visit_module')==0)
    totalmax = totalmax-1;
    @endif
    toggleChecker(category,role,max);
    categoryChecker(role,totalmax);
  @endforeach
  @endforeach
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

$('#party_custom_fields').on('click', '.alert-modal',function(){
          console.log('clicked');
          $('#alertModal').modal('show');
        });
</script>
@include('company.settings.customjs.visit_purpose')
@endsection