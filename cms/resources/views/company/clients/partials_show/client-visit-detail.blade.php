@extends('layouts.company')
@section('title', 'Visit Details')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/datatables/dataTables.bootstrap.css') }}">
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
      width: 70%;
    }

    #distancemap {
      width:100%;
      min-height:400px;
    }

    .btn-sm{
      font-size: 14px;
    }

    .fa-map-marker{
      color:#098309;
    }

    .fa-map-marker{
      cursor: pointer; 
    }

    .warning{
      color: #887206;
      float: left;
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
              {{-- {!!$action!!} --}}
            </div>
          </div>
          <div class="box-header with-border">
            <h3 class="box-title">Visit Details</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body" id="printArea">
            <div class="col-xs-12">
              <div class="table-responsive">
                {{-- @foreach ($empVisits as $clientVisits) --}}
                  <div class="detail-box">
                    <table class="table table-bordered table-striped">
                      {{-- <thead>
                        <tr>
                          <th colspan="2" class="text-center"> {{ $clientVisits['start_time'] }} - {{ $clientVisits['end_time'] }} </th>
                        </tr>
                      </thead> --}}
                      <tbody>
                      <tr>
                        <th scope="row">Party Name</th>
                        <td>
                            {{ $client_name }}
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">Date</th>
                        <td>
                            {{ getDeltaDate($date) }}
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">Visit Time</th>
                        <td>
                            {{ $clientVisits['start_time'] }} - {{ $clientVisits['end_time'] }}
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">Employee Name</th>
                        <td>
                            {{ $clientVisits['employee_name'] }}
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">Time Spent</th>
                        <td>
                            {{ $clientVisits['total_duration'] }}
                        </td>
                      </tr>
                      <tr>
                        <th scope="scope">Visit Route</th>
                        <td>
                          <i class="fa fa-map-marker"></i> <i class="fa fa-spinner fa-pulse fa-fw" style="display:none;"></i>
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">Visit Purpose</th>
                        <td>{{ $clientVisits['visit_purpose'] }}</td>
                      </tr>
                      <tr>
                        <th scope="row">Comments</th>
                        <td>{{ $clientVisits['comments'] }}</td>
                      </tr>
                      @if($clientVisits['images']->first())
                        <tr>
                          <th scope="row"> Images</th>
                          <td>
                            <div class="imgDiv">
                              @foreach($clientVisits['images'] as $image)
                              <div class="col-xs-4">
                                <div class="imagePreview imageExistsPreview"
                                  style="background-color: #fff;background-position: center center;background-size: contain;background-repeat: no-repeat;">
                                  @if(isset($image['image_path']))
                                  <img class="img-responsive display-imglists" @if(isset($image['image_path']))
                                  src="{{ URL::asset('cms'.$image['image_path']) }}"
                                  @endif alt="Picture Displays here" style="max-height: 500px;"/>
                                  @else
                                  <span class="pull-right">N/A</span>
                                  @endif
                                </div>
                              </div>
                              @endforeach
                            </div>
                          </td>
                        </tr>
                      @endif
                      </tbody>
                    </table>

                  </div>
                {{-- @endforeach --}}
              </div>
            </div>

          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
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
  </section>
  <div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
  </div>
@endsection
@section('scripts')
<script src="{{asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script>
  const companylat = "{{ config('settings.latitude') }}";
  const companylng = "{{ config('settings.longitude') }}";
  $(function () {
      const fileloc = JSON.parse(@json($gps_path));
      const client_location = @json($client_location);
      const checkInTime = "{{$date}}" + " " +"{{ $clientVisits['start_time'] }}";
      const checkOutTime = "{{$date}}" + " " +"{{ $clientVisits['end_time'] }}";
      
      $('.fa-map-marker').click(function(){
        let current = $(this);
        current.hide(); 
        current.siblings().show();
        
        let recentPosition; 
        let infowindow = new google.maps.InfoWindow();

        if(fileloc.length==0){
         alert("Could not fetch location during this visit. Possible Reasons:\n- Low Battery\n- Short check-in duration\n- GPS off\n- Phone went into optimization mode to save battery");
          current.show(); 
          current.siblings().hide();
          return;
        }else if(fileloc.length!==0){
          $.each(fileloc, function(index, item) {
            recentPosition = new google.maps.LatLng(item.latitude,item.longitude);
          });
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

        if(fileloc.length>0){
          fileloc.forEach(function(tempValue){
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
                let accuracy = "<br />Accurate to "+Math.ceil(tempValue.accuracy)+" meters";
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
        if(client_location.latitude && client_location.longitude ){
          var goldStar = {
            path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
            fillColor: '#800080',
            fillOpacity: 0.7,
            scale: (0.05, 0.05),
            strokeColor: '#FF00FF',
            strokeWeight: 0.4
          };
          marker = new google.maps.Marker({
              position: new google.maps.LatLng(client_location.latitude,client_location.longitude),
              // icon: goldStar, 
              title:client_location.company_name,map: map
          });

          google.maps.event.addListener(marker, 'click', (function(marker, i) {
              return function() {
                  infowindow.setContent(client_location.company_name);
                  infowindow.open(map, marker);
              }
          })(marker, i));

          map.center =  new google.maps.LatLng(client_location.latitude,client_location.longitude);
        }
        $("#modal-map").modal('show');
        let formattedCheckIn = new Date(checkInTime).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
        let formattedCheckOut = new Date(checkOutTime).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
        
        $("#modal-map").find(".modal-title").html(`Raw GPS PATH ${formattedCheckIn} to ${formattedCheckOut}`);
        current.show(); 
        current.siblings().hide();
      });
      $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
        modal.find('.modal-body #m_id').val(mid);
      });

      $('.delete').on('click',function(){
        $('#accountType').val($(this).attr('data-type'));
      });
  });
  var modal = document.getElementById("myModal");
  var modalImg = document.getElementById("img01");

  $('.display-imglists').on('click',function(){
    modal.style.display = "block";
    modalImg.src = this.src;
  });

  $('.close').on('click',function(){
    modal.style.display = "none";
  });
  // function print() {
  //   window.frames["printf"].focus();
  //   window.frames["printf"].print();
  // }
</script>
@endsection