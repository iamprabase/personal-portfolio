@extends('layouts.company')
@section('title', 'Generate Path')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 22px;
  }
  

  button,
  input,
  select,
  textarea {
    height: 40px;
  }

  .select-2-sec {
    margin-top: -10px;
    position: absolute;
    z-index: 99;
  }

  .select2-container .select2-selection--single {
    height: 40px;
    padding: 12px 5px;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow b {
    margin-top: 3px;
  }

  .box-loader {
    opacity: 1.5;
  }

  .fa.fa-info-circle {
    padding-left: inherit;
    cursor: pointer;
    color: #4c8c16;
  }

  .tooltip-inner {
    max-width: 500px !important;
    background-color: aliceblue;
    color: black;
    max-height: -webkit-fill-available;
  }

  .increaseOpacity {
    opacity: 0.3;
  }

  div.ndp-corner-all-party {
    top: 211px;
    left: 1128.25px !important;
  }

  @media screen and (max-width: 425px) {
    #salesmanOrPartyLabel {
      padding-bottom: 20px;
    }
  }

  .no-lt-pd{
    padding-left: 0px;
  }

  .no-margin{
    margin: 0px;
  }

  .flex-disp{
    display: inline-flex;
    justify-content: space-around;
  }

  .reportrange {
    width: 95%;
    position: relative!important;
  }

  .ndp-nepali-calendar{
    padding: 3px!important;
  }

  #nepCalDiv{
    padding-right: 50px;
  }

  .pd-top{
    padding-top: 10px;
  }

        #map {
        width: 500px;
        height: 400px;
        margin-top: 10px;
      }
</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      
      <!-- Form Selection Box -->
      <div class="box">
        @if (\Session::has('success'))
            <div class="alert alert-success">
              <p>{{ \Session::get('success') }}</p>
            </div>
            <br/>
          @endif
          @if (\Session::has('error'))
            <div class="alert alert-warning">
              <p>{{ \Session::flash('error') }}</p>
            </div>
            <br/>
          @endif
        <div id="loader2" hidden>
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
        <form action="" id="generatePath">
          @csrf
          <div class="box-header">
            <h3 class="box-title"> Generate Path </h3>
          
          <div class="page-action pull-right">
               <a class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal" id="mailBtn"> <i class="fa fa-envelope"></i> Mail</a>
                <!-- <button class="btn btn-default btn-sm" onclick="printmap();" id="printBtn"><i class="fa fa-print"></i> Print
                </button> -->
                
              </div>
              </div>
          <!-- /.box-header -->
          <div class="box-body">
            @if(session('successful_message'))
            <div class="alert alert-success">
              {{ session('successful_message') }}
            </div>
          @endif

          @if(session('error_message'))
            <div class="alert alert-danger">
              {{ session('error_message') }}
            </div>
          @endif
            <div class="box-body no-margin">
              <div class="col-xs-4 no-lt-pd">
                <label id="salesmanOrPartyLabel">
                  Beats
                </label><span style="color: red">*</span>
                <select id="beats" name="beats" class="select2" required>
                  <option value="">Select Beat</option>
                  @foreach($allbeats as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                  @endforeach
                </select>
                <p class="help-block has-error party_salesman" style="color:red;"></p>
              </div>

              <div class="col-xs-4 no-lt-pd">
                <label>
                  Parties
                </label>
                <select id="beatparties" name="beatparties[]" class="multi order_status_select" multiple required>
                  {{-- <option value="">Select Parties</option> --}}
                  
              </select>
                <p class="help-block has-error report_type" style="color:red;"> </p>
              </div>
              <div class="col-xs-4 no-lt-pd">
                <label id="salesmanOrPartyLabel">
                  &nbsp;
                </label>
                <button type="submit" class="btn btn-default input-group" id="getPath" style="width:100%;">
                  <i class="fa fa-book"></i> Generate Path
                </button>
              </div>
            </div>     
          </div>
        </form>
      </div>
          <div id="map_canvus" style="width: 100%; height: 500px;"></div>
    </div>
  </div>

<div class="modal fade" id="myModal" role="document">
  <div class="modal-dialog" style="width:400px;margin:90px auto;">
    {!! Form::open(array('url' => url(domain_route("company.admin.mail")), 'method' => 'post','id'=>'sendmail','files'=> true)) !!}
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-send" style="padding-right:10px;"></i>Send as Mail</h4>
      </div>
      <div class="modal-body">
        {!! Form::label('Send To:') !!}
        {!! Form::text('email',null, ['class'=>'form-control', 'placeholder'=>'Enter Email Address',]) !!}
        <div id="errors">
          @if ($errors->has('email')) <p class="help-block has-error">{{ $errors->first('email') }}</p> @endif
        </div>
        <textarea class="form-control" id="mapurl" name="mapurl" style="visibility:hidden"></textarea>
      </div>
      <div class="modal-footer">
        <button class='btn btn-primary' type='submit' value='submit' id="send_mail">
          <i class='fa fa-send'> </i> Send
        </button>
        {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
    {!! Form::close() !!}
  </div>
</div>

</section>
@endsection

@section('scripts')
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>


<script type="text/javascript">
  $('#printBtn').hide();
  $('#mailBtn').hide();
  $('document').ready(function(){
   
    $('#beats').select2({
      placeholder: "Select Beat",
    });

    $('#beatparties').multiselect({
      placeholder: "Select Parties",
      search:true,
      placeholder: "Select",
      selectAll: true,
      required: true,

    });
  });
  function showLoader(){
    $('#loader2').removeAttr('hidden');
  }

  function hideLoader(){
    $('#loader2').attr('hidden', 'hidden');
  }
  $('#beats').change(function(){
    let sel_beats = $('#beats').val();
  
    if(sel_beats.length>0){
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ domain_route('company.admin.getpartiesfrombeat') }}",
        type: "GET",
        data:
        {
          beat : sel_beats,
        },
        beforeSend:function(){
          showLoader();
        },
        success:function(data) {
          data = JSON.parse(data);
        // alert(data);
          $('select[id="beatparties"]').empty();
          $('select[id="beatparties"]').multiselect('reload');
          data.forEach(element => {
            $('select[id="beatparties"]').append('<option value="'+ element['id'] +'" selected>'+ element['company_name'] +'</option>');
          });
          $('select[id="beatparties"]').multiselect('reload');
          hideLoader();
        },
        error:function(xhr, textStatus){
          hideLoader();
          alert(textStatus);
        }
      });
    }else{
      $('select[id="beatparties"]').empty();
      $('select[id="beatparties"]').multiselect('reload');
    }
  });

  $('#generatePath').on('submit', function (e) {
    e.preventDefault();
    let beatparties = $('#beatparties').val();
    if(beatparties.length<=1){
      alert("please select at least two parties");
      return false;
    }
    $('#loading').html('<img src="loading.gif"> loading...');
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },

      type: "POST",
      url: "{{ domain_route('company.admin.getclientspath') }}",
      data:
      {
        toclient: beatparties,
      },
      beforeSend: function () {
        // Show image container
        $("#loader").show();
      },
      success: function (data) {

        fromclientlat = data.toclient[0].latitude;
        fromclientlng = data.toclient[0].longitude;
        var locations=[];
        $.each(data.toclient, function (index, item) {
          locations.push(item);
        });

        console.log(locations);

      
        initialize(locations);
        google.maps.event.addDomListener(window, "load", initialize);
      },
      complete: function (data) {
        // Hide image container
        $("#loader").hide();
        google.maps.event.addDomListener(window, "load", initialize); 
        //$("#livetrackmodal").modal('show');
      }
    });
  });

  function initialize(locations) {

    var geocoder;
    var map;
    var directionsDisplay;
    var origin;
    var destination;
    var waypoints=[];
    var directionsService = new google.maps.DirectionsService();
    directionsDisplay = new google.maps.DirectionsRenderer();

    var companylat = "{{config('settings.latitude')}}";
    var companylng = "{{config('settings.longitude')}}";

    var map = new google.maps.Map(document.getElementById('map_canvus'), {
      zoom: 5,
      center: new google.maps.LatLng(companylat, companylng),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    directionsDisplay.setMap(map);
    var infowindow = new google.maps.InfoWindow();

    var marker, i;
    var request = {
      travelMode: google.maps.TravelMode.DRIVING
    };

    for (i = 0; i < locations.length; i++) {
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i]['latitude'], locations[i]['longitude']),
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i]['company_name']);
          infowindow.open(map, marker);
        }
      })(marker, i));

      if (i == 0) {
        request.origin = marker.getPosition();
        var originlat_lang=marker.getPosition().lat()+','+marker.getPosition().lng();
        origin=originlat_lang;

      }
      else if (i == locations.length-1) {
        request.destination = marker.getPosition();
        var destinationlat_lang=marker.getPosition().lat()+','+marker.getPosition().lng();
        destination=destinationlat_lang;
      }
      else {

        if (!request.waypoints){ 
          request.waypoints = [];
        }
        request.waypoints.push({
          location: marker.getPosition(),
          stopover: true
        });
          //alert(marker.getPosition())
        var lat_lang=marker.getPosition().lat()+','+marker.getPosition().lng();
        waypoints.push(lat_lang);
      }
      // waypoints.push(request.waypoints['location']);

        //https://www.google.com/maps/dir/?api=1&origin=12.909227,77.6343&destination=12.909228,77.6343&travelmode=driving&waypoints=12.909188,77.6323|12.91044,77.632507|12.911389,77.632912

    }

    //waypoints2=waypoints.replace('),(','|')

    //alert(waypoints);
    var waypointsstring=waypoints.join('|');
    //alert(waypointsstring);

    var url='https://www.google.com/maps/dir/?api=1&origin='+origin+'&destination='+destination+'&travelmode=driving&waypoints='+waypointsstring;
    $('#mapurl').val(url);
    $('#printBtn').show();
      $('#mailBtn').show();
    //alert(url);
    $('.genlink h5').append(url);
    directionsService.route(request, function(result, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(result);
      }
    });
  }

  function printmap() {
    var print_div = document.getElementById("map_canvus");
    var print_area = window.open();
    print_area.document.write(print_div.innerHTML);
    print_area.document.close();
    print_area.focus();
    print_area.print();
    print_area.close();
  }

  $("#sendmail").on('submit', function (e) {
    e.preventDefault();
    //alert($(this).attr('action'));
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      "url": $(this).attr('action'),
      "type": "POST",
      "data": new FormData(this),
      "contentType": false,
      "cache": false,
      "processData": false,
      beforeSend: function () {
        $('#send_mail').html('Please wait...');
        $('#send_mail').attr('disabled');
      },
      success: function (data) {
        $('#sendmail')[0].reset();
        $('#myModal').modal('hide');
      },
      error: function (xhr, status, error) {
        $('#errors').html('');
        for (var error in xhr.responseJSON.errors) {
          $('#errors').html("<p style='padding-top:5px;color:red;'>E-mail is a required field.</p>");
        }
      },
      complete: function () {
        $('#send_mail').html("<i class='fa fa-send'> </i>Send");
        $('#send_mail').removeAttr('disabled');
      }
    });//ajax
  });

</script>
@endsection