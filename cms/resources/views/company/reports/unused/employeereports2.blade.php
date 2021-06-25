@extends('layouts.company')

@section('stylesheets')
  <link rel="stylesheet"
        href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <style>
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

    #map {
      width: 850px;
      height: 500px;
    }
  </style>

@endsection


@section('content')


  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            {!! Form::open(array('url' => url(domain_route("company.admin.employeereport.filter", ["domain" => request("subdomain")])), 'method' => 'post')) !!}

            <div class="row">

              <div class="col-sm-3">
                {!! Form::label('employee_id', 'Employee Name') !!}
                {!! Form::select('employee_id', array('' => 'Please select employee') +  $employees, null,  ['class' => 'form-control']) !!}
                @if ($errors->has('employee_id')) <p
                    class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
              </div>

              <div class="col-sm-3 salesman1">
                <div class="form-group @if ($errors->has('from_date')) has-error @endif">
                  {!! Form::label('from_date', 'From:') !!}
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('from_date', null, ['class' => 'form-control pull-right', 'id' => 'from_date', 'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
                  </div>

                  @if ($errors->has('from_date')) <p
                      class="help-block has-error">{{ $errors->first('from_date') }}</p> @endif
                </div>
              </div>

              <div class="col-sm-3 salesman1">
                <div class="form-group @if ($errors->has('to_date')) has-error @endif">
                  {!! Form::label('to_date', 'To:') !!}
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('to_date', null, ['class' => 'form-control pull-right', 'id' => 'to_date', 'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
                  </div>

                  @if ($errors->has('to_date')) <p
                      class="help-block has-error">{{ $errors->first('to_date') }}</p> @endif
                </div>
              </div>
              <div class="col-sm-3">
                <button value="get_report" name="search" type="submit" class="btn btn-info" style="margin-top: 25px;">
                  Search
                </button>
              </div>
            </div>


            </form>
          </div>
          <!-- /.box-header -->

          <div class="box-body">
            <table id="empattendance" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>S.No.</th>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Time/Working Hours</th>
                <th>Traveled(KM)</th>
                <th>GPS Location</th>
                <th>Order AMT</th>
                <th>Collection AMT</th>
              </tr>
              </thead>
              <tbody>
              @php($i = 0)
              @foreach($attendances as $attendance)
                @php($i++)
                <tr id="{{ $i }}" class="prodrow">
                  <td>{{ $i }}</td>
                  <td>{{ getEmployee($attendance->employee_id)['name']}}</td>
                  <td>{{date('Y-m-d',strtotime($attendance->check_datetime))}} </td>
                  <td id="tottime{{$i}}">-- / -- / --</td>
                  <td><a href="#" id="travel{{$i}}" class="btn btn-info btn-md">0.00 km</a></td>
                  <td>
                    <a class="btn btn-primary mapgenerate"
                       data-rowid={{$i}} data-date="{{date('Y-m-d',strtotime($attendance->check_datetime))}}"
                       data-eid="{{$attendance->employee_id}}" class="btn btn-primary"><i class="fa fa-map-marker"></i></a>
                  </td>
                  <td>{{ getOrders(date('Y-m-d',strtotime($attendance->check_datetime)),$attendance->employee_id)}}</td>
                  <td>{{ getCollections(date('Y-m-d',strtotime($attendance->check_datetime)),$attendance->employee_id)}}</td>

                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>

  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Location Map: Mr. DHEENADHAYALAN SELVARAJ 03-04-2018</h4>
        </div>
        <div class="modal-body">
          <div id="map" style="height:350px; width: 570px;"> jsdfkdsafas</div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">close</button>
        </div>
      </div>
      <!-- /.modal-content
    </div>
     /.modal-dialog -->
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeE7MEUW62uvMGKoBrhqKla3YLfL6jGvE"></script>



  <script type="text/javascript">

      $(function () {
          $("#from_date").datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
          }).on('changeDate', function (selected) {
              var startDate = new Date(selected.date.valueOf());
              $('#to_date').datepicker('setStartDate', startDate);
          }).on('clearDate', function (selected) {
              $('#to_date').datepicker('setStartDate', null);
          });

          $("#to_date").datepicker({
              format: 'yyyy-mm-dd',
              autoclose: true,
          }).on('changeDate', function (selected) {
              var endDate = new Date(selected.date.valueOf());
              $('#from_date').datepicker('setEndDate', endDate);
          }).on('clearDate', function (selected) {
              $('#from_date').datepicker('setEndDate', null);
          });


      });

  </script>



  <script>

      $(document).ready(function () {

          $('.prodrow').each(function () {
              var mapdate = $(this).find('.mapgenerate').data('date');
              var emp = $(this).find('.mapgenerate').data('eid');
              var rowid = $(this).find('.mapgenerate').data('rowid');
              //alert(mapdate);alert(emp);alert(rowid);

              $.ajax({
                  type: 'GET',
                  dataType: 'html',
                  url: "{{ domain_route('company.admin.reports.totalhours') }}",
                  data: {
                      'eid': emp,
                      'mapdate': mapdate,
                  },
                  success: function (data2) {
                      //alert(data2);
                      $('#tottime' + rowid).html(data2);

                  }
              });


              $.ajax({
                  type: 'GET',
                  dataType: 'json',
                  url: "{{ domain_route('company.admin.reports.location') }}",
                  data: {
                      'eid': emp,
                      'mapdate': mapdate,
                  },
                  success: function (data) {

                      var trackedData = [];
                      $.each(data, function (index, item) {
                          trackedData.push(item);
                          //alert(item['lat']);
                      });
                      var ptcount = trackedData.length;
                      //alert(ptcount);
                      // alert(trackedData[0].lat+','+trackedData[0].lng);
                      var i;
                      var totdist = 0;
                      for (i = 1; i < ptcount; i++) {
                          var distyravelled = distance(trackedData[i - 1].lat, trackedData[i - 1].lng, trackedData[i].lat, trackedData[i].lng, 'K');
                          totdist = totdist + distyravelled;
                      }
                      // alert(totdist);
                      $('#travel' + rowid).html(totdist.toFixed(2) + ' km');

                  }
              });


          });


          $('.mapgenerate').click(function () {
              //   var mapdate = $(this).data('date');
              //   var emp = $(this).data('eid');
              //   var rowid = $(this).data('rowid');

              //  // alert(mapdate);
              //   $.ajax({
              //         type: 'GET',
              //         dataType:'json',
              //         url: "{{ domain_route('company.admin.reports.location') }}",
              //         data: {
              //           'eid': emp,
              //           'mapdate':mapdate,
              //         },
              //         success: function(data) {

              //         //   var stops = [];
              //         //   $.each(data, function(index, item) {
              //         //       stops.push(item);
              //         //       //alert(item['Latitude']);
              //         //   });

              var stops = [{"Latitude": 26.4672404, "Longitude": 87.2805553}, {
                  "Latitude": 26.4674214,
                  "Longitude": 87.2838593
              }, {"Latitude": 26.4673938, "Longitude": 87.2838766}, {
                  "Latitude": 26.4673775,
                  "Longitude": 87.2839147
              }, {"Latitude": 26.4672404, "Longitude": 87.2805553}, {
                  "Latitude": 26.467298,
                  "Longitude": 87.28068
              }, {"Latitude": 26.4672888, "Longitude": 87.2806931}, {
                  "Latitude": 26.4672648,
                  "Longitude": 87.2807274
              }, {"Latitude": 26.4672553, "Longitude": 87.2809771}, {
                  "Latitude": 26.4672592,
                  "Longitude": 87.282891
              }, {"Latitude": 26.467027, "Longitude": 87.2824327}];
              var map = new window.google.maps.Map(document.getElementById("map"));

              // new up complex objects before passing them around
              var directionsDisplay = new window.google.maps.DirectionsRenderer();
              var directionsService = new window.google.maps.DirectionsService();

              Tour_startUp(stops);

              window.tour.loadMap(map, directionsDisplay);
              window.tour.fitBounds(map);

              if (stops.length > 1)
                  window.tour.calcRoute(directionsService, directionsDisplay);

              $("#modal-default").modal('show');

              // }
              // });

          });
      });

      function distance(lat1, lon1, lat2, lon2, unit) {
          //alert('kdfkl');
          var radlat1 = Math.PI * lat1 / 180
          var radlat2 = Math.PI * lat2 / 180
          var theta = lon1 - lon2
          var radtheta = Math.PI * theta / 180
          var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
          if (dist > 1) {
              dist = 1;
          }
          dist = Math.acos(dist)
          dist = dist * 180 / Math.PI
          dist = dist * 60 * 1.1515
          if (unit == "K") {
              dist = dist * 1.609344
          }
          if (unit == "N") {
              dist = dist * 0.8684
          }
          return dist
      }

      function Tour_startUp(stops) {
          if (!window.tour) window.tour = {
              updateStops: function (newStops) {
                  stops = newStops;
              },
              // map: google map object
              // directionsDisplay: google directionsDisplay object (comes in empty)
              loadMap: function (map, directionsDisplay) {
                  var myOptions = {
                      zoom: 13,
                      center: new window.google.maps.LatLng(26.4672404, 87.2805553), // default to London
                      mapTypeId: window.google.maps.MapTypeId.ROADMAP
                  };
                  map.setOptions(myOptions);
                  directionsDisplay.setMap(map);
              },
              fitBounds: function (map) {
                  var bounds = new window.google.maps.LatLngBounds();

                  // extend bounds for each record
                  jQuery.each(stops, function (key, val) {
                      var myLatlng = new window.google.maps.LatLng(val.Latitude, val.Longitude);
                      bounds.extend(myLatlng);
                  });
                  map.fitBounds(bounds);
              },
              calcRoute: function (directionsService, directionsDisplay) {
                  var batches = [];
                  var itemsPerBatch = 10; // google API max = 10 - 1 start, 1 stop, and 8 waypoints
                  var itemsCounter = 0;
                  var wayptsExist = stops.length > 0;

                  while (wayptsExist) {
                      var subBatch = [];
                      var subitemsCounter = 0;

                      for (var j = itemsCounter; j < stops.length; j++) {
                          subitemsCounter++;
                          subBatch.push({
                              location: new window.google.maps.LatLng(stops[j].Latitude, stops[j].Longitude),
                              stopover: true
                          });
                          if (subitemsCounter == itemsPerBatch)
                              break;
                      }

                      itemsCounter += subitemsCounter;
                      batches.push(subBatch);
                      wayptsExist = itemsCounter < stops.length;
                      // If it runs again there are still points. Minus 1 before continuing to
                      // start up with end of previous tour leg
                      itemsCounter--;
                  }

                  // now we should have a 2 dimensional array with a list of a list of waypoints
                  var combinedResults;
                  var unsortedResults = [{}]; // to hold the counter and the results themselves as they come back, to later sort
                  var directionsResultsReturned = 0;

                  for (var k = 0; k < batches.length; k++) {
                      var lastIndex = batches[k].length - 1;
                      var start = batches[k][0].location;
                      var end = batches[k][lastIndex].location;

                      // trim first and last entry from array
                      var waypts = [];
                      waypts = batches[k];
                      waypts.splice(0, 1);
                      waypts.splice(waypts.length - 1, 1);

                      var request = {
                          origin: start,
                          destination: end,
                          waypoints: waypts,
                          travelMode: window.google.maps.TravelMode.WALKING
                      };
                      (function (kk) {
                          directionsService.route(request, function (result, status) {
                              if (status == window.google.maps.DirectionsStatus.OK) {

                                  var unsortedResult = {order: kk, result: result};
                                  unsortedResults.push(unsortedResult);

                                  directionsResultsReturned++;

                                  if (directionsResultsReturned == batches.length) // we've received all the results. put to map
                                  {
                                      // sort the returned values into their correct order
                                      unsortedResults.sort(function (a, b) {
                                          return parseFloat(a.order) - parseFloat(b.order);
                                      });
                                      var count = 0;
                                      for (var key in unsortedResults) {
                                          if (unsortedResults[key].result != null) {
                                              if (unsortedResults.hasOwnProperty(key)) {
                                                  if (count == 0) // first results. new up the combinedResults object
                                                      combinedResults = unsortedResults[key].result;
                                                  else {
                                                      // only building up legs, overview_path, and bounds in my consolidated object. This is not a complete
                                                      // directionResults object, but enough to draw a path on the map, which is all I need
                                                      combinedResults.routes[0].legs = combinedResults.routes[0].legs.concat(unsortedResults[key].result.routes[0].legs);
                                                      combinedResults.routes[0].overview_path = combinedResults.routes[0].overview_path.concat(unsortedResults[key].result.routes[0].overview_path);

                                                      combinedResults.routes[0].bounds = combinedResults.routes[0].bounds.extend(unsortedResults[key].result.routes[0].bounds.getNorthEast());
                                                      combinedResults.routes[0].bounds = combinedResults.routes[0].bounds.extend(unsortedResults[key].result.routes[0].bounds.getSouthWest());
                                                  }
                                                  count++;
                                              }
                                          }
                                      }
                                      directionsDisplay.setDirections(combinedResults);
                                  }
                              }
                          });
                      })(k);
                  }
              }
          };
      }


  </script>

@endsection