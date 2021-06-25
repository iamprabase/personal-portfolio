<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<head>
    <title>{{ config('settings.title') }}::@yield('title')</title>
    <link rel="shortcut icon" type="image/png" href="{{ URL::asset('cms'.config('settings.favicon_path')) }}"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <META NAME="robots" CONTENT="noindex,nofollow">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/delta.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/skins/_all-skins.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.css') }}">
     <style>
    .button-bt {
      width: 100%;
      height: 120px;
      text-align: center;
      padding: 30px 10px;
      transition: 0.5s;
      margin-bottom: 25px;
      box-shadow: 0px 4px 6px #4a4a4a;
    }
    
    .emp-icon {
      color: #fff;
      font-size: 20px;
    }
    
    .button-bt p {
      color: #fff;
      margin-top: 5px;
    }
    
    .employee-bg {
      background: #fb9215;
      /* border-left: 5px solid #c36a00; */
    }
    
    .employee-bg:hover {
      background: #c36a00;
    }
    
    .product-bgs {
      background: #53ac59;
      /* border-left: 5px solid #038c0c; */
    }
    
    .product-bgs:hover {
      background: #038c0c;
    }
    
    .parties-bgs {
      background: #f23e41;
      /* border-left: 5px solid #a50204; */
    }
    
    .parties-bgs:hover {
      background: #a50204;
    }
    
    .order-bgs {
      background: #17b1d4;
      /* border-left: 5px solid #068492; */
    }
    
    .order-bgs:hover {
      background: #068492;
    }
    
    .collection-bgs {
      background: #942aae;
      /* border-left: 5px solid #73068e; */
    }
    
    .collection-bgs:hover {
      background: #73068e;
    }
    
    .expenses-bgs {
      background: #555555;
      /* border-left: 5px solid #2d2b2b; */
    }
    
    .expenses-bgs:hover {
      background: #2d2b2b;
    }
    
    .tasks-bgs {
      background: #009688;
      /* border-left: 5px solid #02635a; */
    }
    
    .tasks-bgs:hover {
      background: #02635a;
    }
    
    .announcements-bgs {
      background: #db6741;
      /* border-left: 5px solid #8e2604; */
    }
    
    .announcements-bgs:hover {
      background: #8e2604;
    }
         /*Master Search Box Css */
         .master-search{
             margin-top:5px;
             margin-left: 10px;
             padding: 10px;
         }
  </style>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato|Montserrat" rel="stylesheet">
    <script src="{{ asset('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>

    <!-- Firebase -->
    <script src="https://www.gstatic.com/firebasejs/4.12.1/firebase.js"></script>
    <script>

        // fbConfig = {
        //     apiKey: "AIzaSyAuTEw0i5xIt5Cw3j1oK4MB8CBiqCQnPzg",
        //     authDomain: "local-deltasalesapp.firebaseapp.com",
        //     databaseURL: "https://local-deltasalesapp.firebaseio.com",
        //     projectId: "local-deltasalesapp",
        //     storageBucket: "local-deltasalesapp.appspot.com",
        //     messagingSenderId: "473577038478"
        // };
        
        fbConfig = {
          apiKey: "AIzaSyB3TymwAcW_6w76hGHVybjH1aI6B6mXYec",
          authDomain: "stagingdeltasalesapp.firebaseapp.com",
          databaseURL: "https://stagingdeltasalesapp.firebaseio.com",
          projectId: "stagingdeltasalesapp",
          storageBucket: "stagingdeltasalesapp.appspot.com",
          messagingSenderId: "1054633145586"
        };
    </script>


<script src="{{ asset('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    @yield('stylesheets')
</head>
<body class="hold-transition skin-green-light sidebar-mini">

<div class="wrapper">
    @include('layouts/partials.company.header')
    @include('layouts/partials.company.sidebar')

    <div class="content-wrapper">
        @yield('content')
        <div id='loader'
             style='display:none;width:100%;height:100%;border:0px solid black;position:absolute;padding:2px;     z-index: 9999;'>
            <img src='{{ asset('assets/dist/img/sales.gif') }}' width='100px' height='100px'>
        </div>
    </div>
    @include('layouts/partials.company.footer')
</div>


<script src="{{ asset('assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/fastclick/lib/fastclick.js') }}"></script>
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('assets/dist/js/demo.js') }}"></script>
<script src="{{ asset('assets/dist/js/custom.js') }}"></script>
<script src="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.js') }}"></script>

{{-- <script src="http://maps.google.com/maps/api/js?key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8&region=NP" type="text/javascript"></script> --}}
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8&region=NP&callback=initMap"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier/1.0.3/oms.js"></script>

<script>



    var totalLength = 409.5, notificationStatus = 0;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });

    $(document).ready(function() {
        $.ajax({

                url: "{{ domain_route('company.admin.clientsettings.getcounts') }}",
                type: "GET",
                dataType: "json",

                success:function(response){
                    $('#ordercount').html(response[0].orderCount);
                    $('#expensecount').html(response[0].expenseCount);
                    $('#leavecount').html(response[0].leaveCount);

                     //$('#ordercount span').html(data.orderCount);
                    
                }})
        });
    $(document).ready(function() {
        var party_pt={{getPartyWithoutPartyTypes()}};
        $.ajax({

                url: "{{ domain_route('company.admin.clientsettings.getpartytypes') }}",
                type: "GET",
                dataType: "json",

                success:function(response){
                if(response!=''){
                    $('#partytypes').html('');
                    $('#partytypes').addClass("treeview");
                    var ptype='<a href="#"><i class="fa fa-user-secret"></i><span>Parties</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a><ul class="treeview-menu">';
                    //console.log(response);
                   $.each(response, function(key, value) {
                    var plink = '/admin/client/subclients/'+value.id;
                    ptype = ptype + '<li> <a href='+plink+'><i class="fa fa-circle-o"></i>'+value.name+'</a> </li>';
                 }) 
                   if(party_pt>0){ 
                    var plink2 = '/admin/client';
                    var ptype2='<li><a href='+plink2+'><i class="fa fa-user-secret"></i> <span>Unspecified</span></a><li>';
                     ptype = ptype+ptype2;
                   }
                   ptype = ptype + '</ul>';
                  $('#partytypes').append(ptype); 
                }else{
                    $('#partytypes').html('');
                    var plink = '/admin/client';
                    var ptype='<a href='+plink+'><i class="fa fa-user-secret"></i> <span>Parties</span></a>';
                     $('#partytypes').append(ptype); 
            
                } 
                }
            })
        });


    $(document).ready(function() {
        //loading the first 10 notification
        $.ajax({
            type: 'post',
            url: "{{ domain_route('company.admin.notification.get') }}",
            dataType: 'json',
            success: function (data) {
                notification = "";
                $.each(data.notificationData, function (key, value) {
                    imageUrl = "";
                    if (value.image_path) {
                        imageUrl = '/cms'+value.image_path;
                    } else {
                        if (value.gender = 'Male')
                            imageUrl = '/cms/storage/app/public/uploads/default_m.png"';
                        else
                            imageUrl = '/cms/storage/app/public/uploads/default_f.png"'
                    }
                    // detail = JSON.parse(value.data);
                    if(value.data_type=="noorders" || value.data_type == "")
                        link="#";
                    else if(value.data_type=="beatplan")
                        link="/admin/beatplan/show/"+value.employee_id;
                    else
                        link = '/admin/notification/'+value.id;
                        // imageUrl = imageUrl.replace('//storage/app/public/', '/storage/');
                    
                    notification = notification + '<li> <a href="'+link+'"><div class="pull-left"> <img src="'+imageUrl+'" class="img-circle" alt="User Image"> </div><h4> '+value.name+'  </h4><p>'+value.title+'</p><small><i class="fa fa-clock-o"></i>'+value.date+'</small> </a> </li>';
                });
                $('#notification_list').append(notification);
                $('#n_count_1').html(' ' + data.count + ' ').data('notificationcounter', data.count);
                $('#n_count_2').html(' ' + data.count + ' ');
            }
        });

        setInterval(function () {
            updateNotification()
        }, 30000);


    });

    $('#notification_list').scroll(function (event) {
        event.preventDefault();
        var scroll = $(this).scrollTop();
        if (scroll > totalLength) {
            loadMoreNotification()
        }
    });

    $('.timepicker').timepicker({
        showInputs: false,
        showMeridian: false,
        minuteStep: 5,
        showSeconds: true,
    });

    $(".remove-record-model").on("submit", function (e) {
        $(".delete-button").prop('disabled', true);
    });

    $('.delete').on('click', function () {
        this.setAttribute("disabled", true);
    });

    $('.cancel').on('click', function () {
        $('.delete').removeAttr('disabled');
    });

    $(".box-body form").on("submit", function (e) {
        $("#create_new_entry").prop('disabled', true);
        $(this).submit(function () {
            return false;
        });
    });

    function updateNotification() {
        $.ajax({
            type: 'post',
            url: "{{ domain_route('company.admin.notification.new') }}",
            dataType: 'json',
            success: function (data) {
                notification = "";
                newNotification = 0;
                notificationCount = $('#n_count_1').data('notificationcounter');
                if (data.length > 0) {
                    $.each(data, function (key, value) {
                        newNotification++;
                        imageUrl = "";
                        if (value.image_path) {
                        imageUrl = '/cms'+value.image_path;
                    } else {
                        if (value.gender = 'Male')
                            imageUrl = '/cms/storage/app/public/uploads/default_m.png"';
                        else
                            imageUrl = '/cms/storage/app/public/uploads/default_f.png"'
                    }

                       // imageUrl = imageUrl.replace('/storage/app/public/', '/storage/');
                        // detail = JSON.parse(value.data);
                        if(value.data_type=="noorders" || value.data_type == "")
                            link="#";
                        else if(value.data_type=="beatplan")
                            link="/admin/beatplan/show/"+value.employee_id;
                        else
                            link = '/admin/notification/'+value.id;
                        notification = notification + '<li> <a href="'+link+'"><div class="pull-left"> <img src="'+imageUrl+'" class="img-circle" alt="User Image"> </div><h4> '+value.name+  '</h4><p>'+value.title+'</p><small><i class="fa fa-clock-o"></i>'+value.date+'</small> </a> </li>';
                    });
                    notificationCount = notificationCount + newNotification
                    $('#n_count_1').html(' ' + notificationCount + ' ').data('notificationCounter', notificationCount);
                    $('#n_count_2').html(' ' + notificationCount + ' ');
                    $('#notification_list').prepend(notification);
                }
            }
        });

    }

    function loadMoreNotification() {
        if (notificationStatus == 0) {
            notificationStatus = 1;
            $('#notification_list').append('<div class="more-notification" style=" text-align: center;"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>');
            $.ajax({
                type: 'post',
                url: "{{ domain_route('company.admin.notification.getmore') }}",
                dataType: 'json',
                success: function (data) {
                    notification = "";
                    $.each(data, function (key, value) {
                        imageUrl = "";
                        if (value.image_path) {
                        imageUrl = '/cms'+value.image_path;
                    } else {
                        if (value.gender = 'Male')
                            imageUrl = '/cms/storage/app/public/uploads/default_m.png"';
                        else
                            imageUrl = '/cms/storage/app/public/uploads/default_f.png"'
                    }
                    if(value.data_type=="noorders")
                        link="#";
                    else if(value.data_type=="beatplan")
                        link="/admin/beatplan/show/"+value.employee_id;
                    else
                        link = '/admin/notification/'+value.id;
                        // imageUrl = imageUrl.replace('/storage/app/public/', '/storage/');
                        // detail = JSON.parse(value.data);
                        notification = notification + '<li><a href="'+link+'"><div class="pull-left"><img src="'+imageUrl+'" class="img-circle" alt="User Image"></div><h4> '+value.name+'</h4><p>'+value.title+'</p><small><i class="fa fa-clock-o"></i>' +value.date+'</small> </a> </li>';
                    });
                    $('.more-notification').remove();
                    $('#notification_list').append(notification);
                    notificationStatus = 0;
                }
            });
            totalLength = totalLength + 409.5;

        }
    }




    employeeMarkers = [];
    employees = [];
    var map;
	function initMap() {
	    map = new google.maps.Map(
	    	document.getElementById('map'), 
	    	{
	        zoom: 10,
	        center: new google.maps.LatLng(26.4673609, 87.2840125),
	        mapTypeId: google.maps.MapTypeId.ROADMAP
	    });
	    
	}

    function addEmployeeMarker(data) {


	    var markerPosition = { lat: data.latitude, lng: data.longitude };
	    var unixTimeToDate = new Date(data.unix_timestamp);
        var formattedDate = unixTimeToDate.toLocaleString();
        
        var content = employees[data.employee_id] + "<br/>" + formattedDate;
        var infowindow = new google.maps.InfoWindow();

	    var marker = new google.maps.Marker({
	        position: markerPosition,
	        map: map,
	        icon: { 
	            url: "/assets/dist/img/markers/employee.png",
                scaledSize: new google.maps.Size(25, 40),
                // scale: (0.1, 0.1),

	        },
	        title: employees[data.employee_id] + "  ," + formattedDate,
	        content: content
	    });


        var key = "empid_"+data.employee_id;
        employeeMarkers[key] = marker;

        google.maps.event.addListener(marker, 'spider_click', (function (marker, content, infowindow) { 
		            	return function () { 
		            		infowindow.setContent(content); 
		            		infowindow.open(map, marker);
		            	};
		                })(marker, marker.content, infowindow)
		            );
		oms.trackMarker(marker);
	}


	$('.livetracker').click(function () {

	  $('#loading').html('<img src="loading.gif"> loading...');

	  $.ajax({
		type: 'GET',
		dataType: 'json',
		url: "{{ domain_route('company.admin.reports.recentlocation') }}",
		beforeSend: function () {
		  // Show image container
		  $("#loader").show();
		},
		success: function (data) {

			initMap();

            if(!firebase.apps.length){

                firebase.initializeApp(fbConfig);
            }
			
			var companyRef = firebase.database().ref('recent_location/'+window.location.host.split('.')[0]);

			companyRef.on('child_changed', function (data) {

                var employeeID = data.val().employee_id;

                var tempEmployee = employees[employeeID];
                var tempAccuracy = data.val().accuracy;

                if(tempEmployee && tempAccuracy <60) {

                    employeeMarkers[data.key].setMap(null);
                    addEmployeeMarker(data.val());
                }
            });

		    companylat=<?php echo  config('settings.latitude') ?>;
		    companylng=<?php echo  config('settings.longitude') ?>;
		    var locations = [];
		    var partyloc=JSON.parse(JSON.stringify(data.partyLoc));
		  
		    $.each(data.emploc, function (index, item) {
		        locations.push(item);
		        employees[item.emp_id] = item.name;
		    });

		    if(locations.length !== 0) {


		        oms = new OverlappingMarkerSpiderfier(map,
		        {
		            markersWontMove: true,
		            markersWontHide: true,
		            keepSpiderfied: true,
		            ignoreMapClick: false,
		            circleSpiralSwitchover: 40
		        });

		        var infowindow = new google.maps.InfoWindow();
		        var marker, i;
		        for(i = 0; i < locations.length; i++) {

		        	var tempLocation = locations[i];

		            var content = '';
		            var unixTimeToDate = new Date(tempLocation['unix_timestamp']);
		            var formattedDate = unixTimeToDate.toLocaleString();
		            marker = new google.maps.Marker({
		                position: new google.maps.LatLng(tempLocation['lat'], tempLocation['lng']),
		                map: map,
		                icon: { 

		                    url: "/assets/dist/img/markers/employee.png",
                            scaledSize: new google.maps.Size(25, 40),
                            // scale: (0.1, 0.1),

		                },
		                title: tempLocation['name'] + "  ," + formattedDate,
		                content: tempLocation['name'] + "<br/>" + formattedDate
		            });

		            var key = "empid_"+tempLocation['emp_id'];

		            employeeMarkers[key] = marker;

		            google.maps.event.addListener(marker, 'spider_click', (function (marker, content, infowindow) { 
		            	return function () { 
		            		infowindow.setContent(content); 
		            		infowindow.open(map, marker);
		            	};
		                })(marker, marker.content, infowindow)
		            );
		            oms.trackMarker(marker);
		        }
		    } else{

		        map = new google.maps.Map(
		            document.getElementById('map'), 
		            {   zoom: 8,
		                center: new google.maps.LatLng(companylat, companylng),
		                mapTypeId: google.maps.MapTypeId.ROADMAP
		            }
		        );

		        var infowindow = new google.maps.InfoWindow();
		        var marker, i;

		        $("#nouser").html("No Active User found.");
		  	}
            var goldStar = {
                path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
                fillColor: 'red',
                fillOpacity: 0.7,
                scale: (0.05, 0.05),
                strokeColor: 'yellow',
                strokeWeight: 0.4
            };
			$.each(partyloc, function(index, item) {

				marker = new google.maps.Marker({
					position: new google.maps.LatLng(item.latitude,item.longitude),
                    icon: goldStar, 
                    // icon: { 
					// 	url: "/assets/dist/img/markers/Official_8.png",
                    //     scaledSize: new google.maps.Size(20, 20),
					// },
					title:item.company_name,
					//label:item.company_name,
					map: map
				});

				google.maps.event.addListener(marker, 'click', (function(marker, i) {
					return function() {
						infowindow.setContent(item.company_name);
						infowindow.open(map, marker);
					}
				})(marker, i));
      });
      var address = "{{getCompanyAddress(config('settings.country'), config('settings.state'), config('settings.city'))}}";
      geocoder = new google.maps.Geocoder();
      geocoder.geocode( { 'address': address}, function(results, status) {
        map.setCenter(results[0].geometry.location);
      });

		},
		complete: function (data) {
		    // Hide image container
		    $("#loader").hide();
		    $("#livetrackmodal").modal('show');
		}
	    });
	});

// Ajax call for Master search
    $(document).ready(function(){
        $('#search').keyup(function(){
            let query = $(this).val();
            // console.log(query);
            if(query != ''){
                var _token = $('input[name="_token"]').val();
                //  console.log(_token);
                $.ajax({
                    url:"{{ domain_route('company.admin.home.p') }}",
                    method:"POST",
                    dataType: 'json',
                    data:{query:query, _token:_token},
                    success:function(data){
                            // document.getElementById("demo").innerHTML = data.party;
                        // console.log(data.table_data);
                        // alert(data.value1);
                        $('#search-list').fadeIn();
                        // // $('#search-list').html('<h1>Test</h1>');
                        $('#search-list').html(data.emp_data);
                        
                        $(".nav-tabs a").click(function(){
                            $(this).tab('show');
                        });
                        
                        
                    }
                });

            }
            // if query fields is empty this code be execute
            $('#search-list').empty();
            // end 
        });
        // $(document).on('click', 'li', function(){
        //     $('#search').val($(this).text());
        //     // $('#search-list').fadeOut();
        // });
        
    });


</script>

@yield('scripts')
</body>
</html>
