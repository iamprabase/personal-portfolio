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

<link rel="stylesheet" href="{{asset('assets/dist/css/settings.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{ asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@endif
<style>
  .headerTab{
    background-color:#0b7676!important;
  }
</style>
@endsection

@section('content')
  <section class="content">
    <div class="row">
    	@if (session()->has('active'))
        <?php $active = session()->get('active'); ?>
      @else
        <?php $active = 'tally' ?>
      @endif
      @include('company.settingsnew.settingheader')
    </div>
    <div class="row">
      <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs" style="margin-top:20px;">
        <div class="col-xs-3 right-pd">
      		 <ul class="nav nav-tabs" id="myTabs" role="tablist">
            @if(config('settings.tally')==1)
            <li role="presentation" class="{{($active == 'tally')? 'active':''}}"><a href="#tally" role="tab" id="tally-tab" data-toggle="tab"aria-controls="tally" aria-expanded="false">Tally</a></li>
            @endif
             <!-- <li role="presentation" class="{{($active == 'quickbook')? 'active':''}}"><a href="#quickbook" role="tab" id="quickbook-tab" data-toggle="tab" aria-controls="quickbook" aria-expanded="false">Quickbook</a></li> -->
            
          </ul>
    		</div>
    		@include('company.settingsnew._integrationtabs')
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
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/js/bootstrap-colorpicker.min.js"></script>
  <script src="{{asset('assets/plugins/settings/partytype.js')}}"></script> 
  <script type="text/javascript" src="{{asset('assets/plugins/settings/business.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/plugins/settings/expensetypes.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/plugins/settings/creditdays.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/plugins/settings/dateformat.js')}}"></script>

@endsection