@extends('layouts.company')
@section('title', 'Visit Details')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">

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

    .order-dtl-bg{
      margin-bottom: 20px;
    }

    .detail-box{
      margin-bottom: 10px;
    }

    td{
      /* width: 70%; */
      width: auto;
    }

    .btn-sm{
      font-size: 14px;
    }

    .fa-map-marker{
      color:#098309;
      cursor: pointer;
    }

    #distancemap {
      width:100%;
      min-height:400px;
    }

    #distancemap2 {
      width:100%;
      min-height:400px;
    }

    .mw-160 {
      min-width: 160px!important;
    }

    .mw-70 {
      min-width: 70px!important;
    }

    .imageExistsPreview img {
      height: 100px;
      margin-right: auto;
      margin-left: auto;
    }
    .imagePreview {
      width: auto;
    }

    .imgDiv{
      display: flex;
      flex-wrap: wrap;
    }

    .col-xs-4{
      width: 90px;
    }

    .warning{
      color: #887206;
      float: left;
    }
    .modal-footer {
      text-align: left;
    }

    .dt-buttons.btn-group{
      margin-left: 5px;
    }
    .page-action{
      display: flex;
    }
  </style>
@endsection

@section('content')
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header with-border">
            <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
            <div class="page-action pull-right">
              {!!$action!!}
            </div>
          </div>
          <div class="box-header with-border">
            <h3 class="box-title">Visit Details</h3>
          </div>
          <!-- /.box-header -->
          @include('company.client-visits.partial_show.visit_detail_partial', [
            'action' => $action,
            'checkin' => $checkin, 
            'checkout' => $checkout, 
            'date' => $date, 
            'employee_name' => $employee_name,
            'employee_show' => $employee_show,  
            'empVisits' => $empVisits, 
            'total_duration' => $total_duration, 
            'total_distance' => $total_distance,
            'distance_travelled' => $distance_travelled
          ])
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
    </div>
    <iframe hidden id="printf" name="printf" src="{{domain_route('company.admin.employee.empClientVisitDetailPrint', ['id' => $employee_id, 'date' => $date])}}" frameborder="0"></iframe>
    {{-- <div class="modal modal-default fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
      data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <form method="post" class="remove-record-model">
            {{method_field('delete')}}
            {{csrf_field()}}
            <div class="modal-body">
              <p class="text-center">
                Are you sure you want to delete this?
              </p>
              <input type="hidden" name="expense_id" id="c_id" value="">
              <input type="text" id="accountType" name="account_type" hidden />
              <input type="hidden" name="prev_url" value="{{URL::previous()}}">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-warning delete-button">Yes, Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div> --}}
  </section>
  <div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
  </div>

  <div class="modal fade" id="modal-map">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div id="distancemap"></div>

        </div>
        <div class="modal-footer"style="padding-left: 50px;line-height: 10px;">
          <div class="row">
            <p class="warning">*Warning-Sometimes location may not be accurate. Reasons:</p>
          </div>
          <div class="row">
            <p class="warning">- Low battery</p>
          </div>
          <div class="row">
            <p class="warning">- User was indoor</p>
          </div>
          <div class="row">
            <p class="warning">- User was not moving.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal-map2">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div id="distancemap2"></div>

        </div>
        <div class="modal-footer">
          
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
          <input type="hidden" name="expense_id" id="c_id" value="">
          <input type="text" id="accountType" name="account_type" hidden />
        </div>
        {{-- <div class="modal-footer">
          <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
        </div> --}}
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatables-buttons/buttons.flash.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

<script>
  const companylat = "{{ config('settings.latitude') }}";
  const companylng = "{{ config('settings.longitude') }}";

  let data = JSON.parse(@json($total_distance));
  if(data.status==404){
    var fileloc = [];
  }else{
    var fileloc = JSON.parse(data.fileloc);
  }
  // console.log(fileloc);
  let partyloc = JSON.parse(@json($partyLoc));
  // console.log(partyloc);
  let arrayGroupdedLocations = [];
  if(fileloc.length>0){
    let counter = 0;
    fileloc.map(location => {
      counter += 1;
      location.map(loc => {
        loc.counter = counter;
        arrayGroupdedLocations.push(loc);
      });
    });
  }
  
  $(function () {
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
        // $('#alertModalText').html('Sorry! You are not authorized to view this user details.');
      }
    });
    $('.distance-travelled-map').click(function(){
      if(data.status===404){
        alert(data.message);

        return false;
      }
      let current = $(this);
      
      let recentPosition; 
      let infowindow = new google.maps.InfoWindow();
      if(fileloc.length==0 || arrayGroupdedLocations==undefined){
        recentPosition = new google.maps.LatLng(companylat,companylng);
      }else if(fileloc.length!==0 && arrayGroupdedLocations!=undefined){
        $.each(arrayGroupdedLocations, function(index, item) {
          recentPosition = new google.maps.LatLng(item.latitude,item.longitude);
        });
      }else{
        recentPosition = new google.maps.LatLng(companylat,companylng);
      }
      
      let map = new google.maps.Map(document.getElementById('distancemap2'), {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: true,
        zoom:16,
        center: recentPosition
      });
      var marker, i;

      lineSymbol = {
        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
      };
      let tempPathArray =[];

      if(arrayGroupdedLocations){
          // arrayGroupdedLocations.forEach(function(tempValue){
          //   marker = new google.maps.Marker({
          //     position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
          //     icon: lineSymbol,
          //     map: map,
          //     title:"",
          //     label:""
          //   });
        
          //   google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
          //     return function () {
          //       let tempDateTime = "DateTime :"+tempValue.datetime;
          //       let tempLoc = "<br />LatLng :"+tempValue.latitude+','+tempValue.longitude;
          //       infowindow.setContent(tempDateTime);
          //       infowindow.open(map, marker);
          //     }
          //   })(marker, i));
            
          //   tempPathArray.push(marker.getPosition());
            
          //   new google.maps.Polyline({
          //       icons: [{
          //         icon: {path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW},
          //         offset: '100%',
          //       }],
          //       map: map,
          //       path: tempPathArray,
          //       strokeColor: "red",
          //       strokeOpacity: 5.0,
          //       strokeWeight: 2,
          //     });
          // });
          // tempPathArray = [];
        fileloc.forEach(function(locations){ 
          locations.forEach(function(tempValue){
            marker = new google.maps.Marker({
              position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
              icon: lineSymbol,
              map: map,
              title:"",
              label:""
            });
        
            google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
              return function () {
                let tempDateTime = "DateTime :"+tempValue.datetime;
                // let accuracy = "<br />Accurate to "+Math.ceil(tempValue.accuracy) + " meters";
                // let tempLoc = "<br />LatLng :"+tempValue.latitude+','+tempValue.longitude;
                infowindow.setContent(tempDateTime);
                infowindow.open(map, marker);
              }
            })(marker, i));
            
            tempPathArray.push(marker.getPosition());
            
            new google.maps.Polyline({
                icons: [{
                  icon: {path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW},
                  offset: '100%',
                }],
                map: map,
                path: tempPathArray,
                strokeColor: "red",
                strokeOpacity: 5.0,
                strokeWeight: 2,
              });
          });
          tempPathArray = [];
        });
      }
      if(partyloc){
        var goldStar = {
          path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
          fillColor: '#800080',
          fillOpacity: 0.7,
          scale: (0.05, 0.05),
          strokeColor: '#FF00FF',
          strokeWeight: 0.4
        };
        $.each(partyloc, function(index, item) {
          marker = new google.maps.Marker({
              position: new google.maps.LatLng(item.latitude,item.longitude),
              //icon: goldStar, 
              title:item.company_name,map: map
          });

          google.maps.event.addListener(marker, 'click', (function(marker, i) {
              return function() {
                  infowindow.setContent(item.company_name);
                  infowindow.open(map, marker);
              }
          })(marker, i));
        });
      }
      // $("#modal-map").modal('show');
      // $("#modal-map").find(".modal-title").html('Location Covered');
      var accuracy = data.accuracy;
      if(!data.is_checked_in){
        if(accuracy >= 70){
          var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-success'> Good</span></p>";
        }else if(accuracy <= 40){
          var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-danger'> Poor</span></p>";
        }else if(accuracy > 40 && accuracy < 70 ){
          var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-warning'> Average</span></p>";
        }
      }else{
        var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-info'> NA</span></p>";
      }
      var dataReceived = data.dataReceived;
      var percentAccuratePoints = data.percentAccuratePoints;
      var footerText = "";
      if(accuracy < 70 && !data.is_checked_in){
        if(dataReceived < 50){
          footerText = `<span>Less points have been received from the device due to: </span><br/>
                        <span>- Low Battery level  </span><br/>
                        <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                        <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>`;
        }else{
          if(dataReceived >= 50 && dataReceived <= 90 && percentAccuratePoints >= 60){
            footerText = `<span>Received less data points from device due to: </span><br/>
                        <span>- Low Battery level  </span><br/>
                        <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                        <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>`;
          }else if(dataReceived >= 50 && dataReceived <= 90 && percentAccuratePoints < 60){
            footerText = `<span>Received few data points from device but mostly inaccurate due to: </span><br/>
                        <span>- Low Battery level  </span><br/>
                        <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                        <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>
                        <span>- User was indoor </span><br/>
                        <span>- User was not moving </span><br/>`;
          }else if(dataReceived > 90 && percentAccuratePoints > 50 && percentAccuratePoints < 90 ){
            footerText = `<span>Data received from device but accuracy is average because: </span><br/>
                        <span>- User was indoor </span><br/>
                        <span>- User was not moving </span><br/>
                        <span>- Battery level was low </span><br/>`;
          }else if(dataReceived > 90 && percentAccuratePoints < 50 ){
            footerText = `<span>Data received from device but accuracy is poor because: </span><br/>
                        <span>- User was indoor </span><br/>
                        <span>- User was not moving </span><br/>
                        <span>- Battery level was low </span><br/>`;
          }
        }
      }

      var total_distance = "<br/><span style='font-size: 14px;'>Total Distance = "+"{{$distance_travelled}}"+ "KM</span>";

      $("#modal-map2").find(".modal-title").html("Location Map: " + total_distance + accuracy_label);        
      $("#modal-map2").find(".modal-footer").html(footerText); 
      $("#modal-map2").modal('show');
      
    });

    $('.partial-marker').click(function(){
      let current = $(this);
      current.hide();
      current.siblings().show();
      let client_id = current.data("client-id");
      let start_time = current.data("start-time");
      let end_time = current.data("end-time");
      let checkInTime = "{{$date}}" + " " +start_time;
      let checkOutTime = "{{$date}}" + " " +end_time;
      if(fileloc.length==0 || arrayGroupdedLocations==undefined){
        alert("Could not fetch location during this visit. Possible Reasons:\n- Low Battery\n- Short check-in duration\n- GPS off\n- Phone went into optimization mode to save battery");
        current.show();
        current.siblings().hide();
        return;
      }else {
        $.ajax({
          type: 'GET',
          dataType:'json',
          url: "{{domain_route('company.admin.employee.employeePeriodicLocation')}}",
          data: {
            "start_time": start_time,
            "end_time": end_time,
            "date": "{{$date}}",
            "employee_id": "{{$empVisits->first()['employee_id']}}"
          },
          success: function(data) {
            if(data.length==0){
              alert("Could not fetch location during this visit. Possible Reasons:\n- Low Battery\n- Short check-in duration\n- GPS off\n- Phone went into optimization mode to save battery");
              current.show();
              current.siblings().hide();
              return;
            }
            let travelledBetweenTime = data;
        
            let recentPosition; 
            let infowindow = new google.maps.InfoWindow();

            if(fileloc.length!==0 && arrayGroupdedLocations!=undefined){
              $.each(travelledBetweenTime, function(index, item) {
              //   if((item.datetime<=checkInTime && item.datetime<=checkOutTime) ){
              //     travelledBetweenTime.push(item);
                  recentPosition = new google.maps.LatLng(item.latitude,item.longitude);
              //   }
              });
              console.log(recentPosition);
              if(!recentPosition) recentPosition = new google.maps.LatLng(companylat,companylng);
            }else{
              recentPosition = new google.maps.LatLng(companylat,companylng);
            }
            
            let map = new google.maps.Map(document.getElementById('distancemap'), {
              mapTypeId: google.maps.MapTypeId.ROADMAP,
              scrollwheel: true,
              zoom:16,
              center: recentPosition
            });
            var marker, i;

            lineSymbol = {
              // path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
              path: google.maps.SymbolPath.CIRCLE,
              fillColor: '#3c763d',
              fillOpacity: 0.6,
              strokeColor: '#3c763d',
              strokeOpacity: 0.9,
              strokeWeight: 1,
              scale: 5
            };
            let tempPathArray =[];

            if(travelledBetweenTime.length>0){
              travelledBetweenTime.forEach(function(tempValue){
                marker = new google.maps.Marker({
                  position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
                  icon: lineSymbol,
                  map: map,
                  title:"",
                  label:""
                });
            
                google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
                  return function () {
                    let tempDateTime = "DateTime :"+tempValue.datetime;
                    let acctext = Math.ceil(tempValue.accuracy) == 0 ? "<br />Accuracy "+ 'NA' : "<br />Accurate to " + Math.ceil(tempValue.accuracy)  +" meters";
                    let accuracy = acctext;
                    let tempLoc = "<br />LatLng :"+tempValue.latitude+','+tempValue.longitude;
                    infowindow.setContent(tempDateTime + accuracy);
                    infowindow.open(map, marker);
                  }
                })(marker, i));
                
                tempPathArray.push(marker.getPosition());
            
                // new google.maps.Polyline({
                //     icons: [{
                //       icon: {path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW},
                //       offset: '100%',
                //     }],
                //     map: map,
                //     path: tempPathArray,
                //     strokeColor: "red",
                //     strokeOpacity: 5.0,
                //     strokeWeight: 2,
                //   });
              });
            }
            if(partyloc){
              var goldStar = {
                path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
                fillColor: '#800080',
                fillOpacity: 0.7,
                scale: (0.05, 0.05),
                strokeColor: '#FF00FF',
                strokeWeight: 0.4
              };
              $.each(partyloc, function(index, item) {
                if(item.id == client_id){
                  marker = new google.maps.Marker({
                      position: new google.maps.LatLng(item.latitude,item.longitude),
                      //icon: goldStar, 
                      title:item.company_name,
                      map: map
                  });

                  google.maps.event.addListener(marker, 'click', (function(marker, i) {
                      return function() {
                          infowindow.setContent(item.company_name);
                          infowindow.open(map, marker);
                      }
                  })(marker, i));
                }
              });
            }
            $("#modal-map").modal('show');
            let formattedCheckIn = new Date(checkInTime).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
            let formattedCheckOut = new Date(checkOutTime).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
            $("#modal-map").find(".modal-title").html(`Raw GPS PATH ${formattedCheckIn} to ${formattedCheckOut}`);
            current.show();
            current.siblings().hide();
      
          },
          error:function(xhr, textStatus){
            current.show();
            current.siblings().hide();
      
          }
        });
      }
    });

    // function getTotalDistanceFromLatLngArray(tempArray,returnString) {

    //   let totalDistanceStr = "";
    //   let totalDistance = 0;
    //   let R = 6371; // km (change this constant to get miles)
    //   let iteration = 0;
    //   let lat1,lon1,lat2,lon2;
      
    //   if(tempArray == null || tempArray == undefined) return returnString ? totalDistanceStr : totalDistance;
    //   if(tempArray.length <2) return returnString ? totalDistanceStr : totalDistance;

    //   tempArray.forEach(function(tempLocation){

    //     if(iteration == 0){
    //       lat1 = tempLocation.latitude;
    //       lon1 = tempLocation.longitude;
    //     }

    //     lat2 = tempLocation.latitude;
    //     lon2 = tempLocation.longitude;

    //     let dLat = (lat2-lat1) * Math.PI / 180;
    //     let dLon = (lon2-lon1) * Math.PI / 180;
    //     let a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180 ) * Math.cos(lat2 * Math.PI / 180 ) * Math.sin(dLon/2) * Math.sin(dLon/2);
    //     let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    //     let distance = R * c;
    //     totalDistance = totalDistance + distance;
    //     lat1 = lat2;
    //     lon1 = lon2;
    //     iteration++;
    //   });

    //   return totalDistance.toFixed(3);
    // }
    // function getToDistanceTravelled(locations){
    //   let totalFineDistance = getTotalDistanceFromLatLngArray(locations,false);
    //   $("#total-distance").html(totalFineDistance.toFixed(3)+ " KM");
    // }
    // getToDistanceTravelled(arrayGroupdedLocations);

    // $('#delete').on('show.bs.modal', function (event) {
    //   var button = $(event.relatedTarget)
    //   var mid = button.data('mid')
    //   var url = button.data('url');
    //   $(".remove-record-model").attr("action", url);
    //   var modal = $(this)
    //   modal.find('.modal-body #m_id').val(mid);
    // });

    // $('.delete').on('click',function(){
    //   $('#accountType').val($(this).attr('data-type'));
    // });
  });

  let modal = document.getElementById("myModal");
  let modalImg = document.getElementById("img01");

  $('.display-imglists').on('click',function(){
    modal.style.display = "block";
    modalImg.src = this.src;
  });

  $('.close').on('click',function(){
    modal.style.display = "none";
  });
  function print() {
    window.frames["printf"].focus();
    window.frames["printf"].print();
  }

  $('.table').DataTable({
            "ordering": false,
 "buttons": [

          {
    extend: 'excelHtml5',
            title: 'Visit Details',

          exportOptions: {
    columns: ':visible:not(:last-child)'
  }}],}
  ).buttons().container()
          .appendTo('.page-action')
</script>
@endsection