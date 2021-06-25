@extends('layouts.app')

@section('title', 'Update Company')
@section('stylesheets')
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<style>
  .icheckbox_minimal-blue {
    margin-top: -2px;
    margin-right: 3px;
  }

  .checkbox label,
  .radio label {
    font-weight: bold;
  }

  .has-error {
    color: red;
  }

</style>
@endsection

@section('content')
<section class="content">

  <!-- SELECT2 EXAMPLE -->
  <div class="box box-default">
    <div class="box-header with-border">
      <h3 class="box-title">Update Outlet</h3>

      <div class="box-tools pull-right">
        <div class="col-md-7 page-action text-right">
          <a href="{{ route('app.outlets') }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i>
            Back</a>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      {!! Form::open(['route' => ['app.outlets.update', $outlet->id],'autocomplete'=>'off' ]) !!}
      @include('admin.outlets._form')
      <!-- Submit Form Button -->
      {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right']) !!}
      {!! Form::close() !!}

    </div>
  </div>

</section>


@endsection

@section('scripts')

<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
<script>
  var geocoder;
  var map;
  var marker;

  //Initialization of google map with clickable marker and dragable 
  function initialize() {

    var initialLat = $('#search_latitude').val();
    var initialLong = $('#search_longitude').val();
    // initialLat = initialLat ? initialLat : {{config('settings.latitude')}};
    // initialLong = initialLong ? initialLong : {{config('settings.longitude')}};

    var latlng = new google.maps.LatLng(initialLat, initialLong);
    var options = {
        zoom: 7,
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

    map.addListener('click', function(event) {
      addMarker(event.latLng);
    });

    function addMarker(location) {
      marker.setMap(map);
      marker.setPosition(location);
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

  }

  //load google map
  $(function(){
    initialize();
    // autocomplete location search
    var PostCodeid = '#search_addr';
    $(function () {
        $(PostCodeid).autocomplete({
            source: function (request, response) {
                geocoder.geocode({
                    'address': request.term
                }, function (results, status) {
                    response($.map(results, function (item) {
                        return {
                            label: item.formatted_address,
                            value: item.formatted_address,
                            lat: item.geometry.location.lat(),
                            lon: item.geometry.location.lng()
                        };
                    }));
                });
            },
            select: function (event, ui) {
                $('#search_addr').val(ui.item.value);
                $('#search_latitude').val(ui.item.lat);
                $('#search_longitude').val(ui.item.lon);
                var latlng = new google.maps.LatLng(ui.item.lat, ui.item.lon);
                marker.setPosition(latlng);
                initialize();
            }
        });
    });

    /** Point location on google map */
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

    // marker.setMap(null);
  });

  $('select[name="country"]').on('change', function () {
    let countryId = $(this).val();
    if (countryId) {
      $.ajax({
        url: "{{domain_route('app.outlets.fetchPhoneState')}}",
        type: "GET",
        data:{
          countryId:  countryId,
        },
        beforeSend:function(){
          $('#state').append($('<option selected="selected"></option>').html('Loading...'));
        },
        success: function (data) {
          $("#state").empty();
          $('#city').empty();
          $('#extNo').empty();
          $("#city").append('<option value="" selected="selected">Select a City</option>');
          $("#state").append('<option value="" selected="selected">Select a State</option>');
          $("#extNo").append('<option value="'+data['phone_extension']+'" selected="selected">'+ data['phone_extension'] +'</option>');
          $.each(data['states'], function (key, value) {
            $("#state").append('<option value="' + key + '">' + value + '</option>');
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
    let stateId = $(this).val();
    $('#city').append($('<option selected="selected"></option>').html('Loading...'));
    if (stateId) {
      $.ajax({
        url: '/get-city-list?state_id=' + stateId,
        type: "GET",
        dataType: "json",
        data:{
          stateId: stateId
        },
        beforeSend:function(){
          $('#city').append($('<option selected="selected"></option>').html('Loading...'));
        },
        success: function (data) {
          $("#city").empty();
          $("#city").append('<option value="" selected="selected">Select a City</option>');
          $.each(data, function (key, value) {
              $("#city").append('<option value="' + key + '">' + value + '</option>');
          });
        }
      });
    } else {
      $('#city').empty();
    }
    $('#city').trigger('change');
  });
</script>
@endsection