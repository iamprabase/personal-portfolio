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
  }id="collaterals-detail"
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
  <div class="row">
    @if (session()->has('active'))
      <?php $active = session()->get('active'); ?>
    @else
      <?php $active = 'profile' ?>
    @endif
    @include('company.settingsnew.settingheader')
  </div>
  <div class="row">
    <div class="col-xs-12" id="collaterals-detail">
       @include('company.settingsnew.collaterals_main')
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
 
@endsection