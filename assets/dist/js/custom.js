$(function(){
	$('.mapgenerate').click(function() {

	  currentElem = $(this);
	  currentElem.find('.fa-map-marker').hide();
	  currentElem.find('.fa-spinner').show();
	  currentElem.attr('disabled',true);

	  var mapdate = $(this).data('date');
	  var nMapDate = $(this).data('ndate');
	  var emp = $(this).data('eid');
	  var rowid = $(this).data('rowid');
	  var title= "Location Map:"+ $(this).data('ename')+" "+ nMapDate;
	  gpsType = $(this).data('gps_type');
	  $.ajax({
	    type: 'GET',
	    dataType:'json',
	    url: ajaxUrls.getFileLocation,
	    data: {
	      'eid': emp,
	      'mapdate':mapdate,
	      'gpsType':gpsType
	    },
	    success: function(data) {
	    	locations = [];
	    	var fileloc=JSON.parse(data.fileloc);
	    	var partyloc=JSON.parse(JSON.stringify(data.partyLoc));

	      currentElem.find('.fa-map-marker').show();
	      currentElem.find('.fa-spinner').hide();
	      currentElem.attr('disabled', false);


	     // 
	      arrayGroupdedLocations = [];
	      arrayPaths = [];
	      totalDistance = 0;
	      totalFineDistance = 0;
	      var recentPosition; 
	      var infowindow = new google.maps.InfoWindow();
	      $.each(fileloc, function(index, item) {
	        arrayGroupdedLocations.push(item);
	        item.forEach(function(temp){
	        	recentPosition = new google.maps.LatLng(temp.latitude,temp.longitude);
	        });
	      });

	      if(gpsType == 'path' || gpsType == 'py_processed_path'){ //showing arrowed path

	        var map = new google.maps.Map(document.getElementById('devmap'), {
	          mapTypeId: google.maps.MapTypeId.ROADMAP,
	          scrollwheel: true,
	          zoom:16,
	          center: recentPosition
	        });
	        lineSymbol = {
	          path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
	        };

	        arrayGroupdedLocations.forEach(function(tempArray){
	        	var tempPathArray =[];
	        	tempArray.forEach(function(tempValue){
	        		marker = new google.maps.Marker({
	        		  position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
	        		  icon: lineSymbol,
	        		  map: map,
	        		  title:"",
	        		  label:""
	        		});

	        		google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
	        		  return function () {
	        		  	var tempDateTime = "DateTime :"+tempValue.datetime;
	        		  	var tempLoc = "<br>LatLng :"+tempValue.latitude+','+tempValue.longitude;
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

	        	totalFineDistance = totalFineDistance + getTotalDistanceFromLatLngArray(tempArray,false);

	        });


	      } else {

	        var map = new google.maps.Map(document.getElementById('devmap'), {
	          zoom: 15,
	          center: recentPosition,
	          mapTypeId: google.maps.MapTypeId.TERRAIN
	        });


	        var marker, i;

	        arrayGroupdedLocations.forEach(function(tempArray){
	        	tempArray.forEach(function(tempValue){
	        		marker = new google.maps.Marker({
	        		  position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
	        		  map: map,
	        		  icon: {
	        		    path: google.maps.SymbolPath.CIRCLE,
	        		    fillColor: '#3c763d',
	        		    fillOpacity: 0.6,
	        		    strokeColor: '#3c763d',
	        		    strokeOpacity: 0.9,
	        		    strokeWeight: 1,
	        		    scale: 5
	        		  }
	        		});
	        	});
	        	totalFineDistance =totalFineDistance + getTotalDistanceFromLatLngArray(tempArray,false);
	        });
		}
		
		var goldStar = {
			path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
			fillColor: 'red',
			fillOpacity: 0.7,
			scale: (0.05, 0.05),
			strokeColor: 'yellow',
			strokeWeight: 0.4
		};

		// if(gpsType=="raw"){
		// 	var party_icon = goldStar;
		// }else{
		// 	var party_icon = {url: "/assets/dist/img/markers/Official_8.png",scaledSize: new google.maps.Size(32, 32)};
		// }
	    $.each(partyloc, function(index, item) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(item.latitude,item.longitude),
					icon: goldStar, 
					// { url: "/assets/dist/img/markers/Official_8.png",
                    //         scaledSize: new google.maps.Size(32, 32)
                    //     },
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

	   
	    

	      $("#total-distance").html("Total Distance = "+totalFineDistance.toFixed(3)+ "KM");

	      $("#modal-default").find(".modal-title").text(title);       
	      $("#modal-default").modal('show');
	    },
	    error:function(e){
	    	currentElem.find('.fa-map-marker').show();
	    	currentElem.find('.fa-spinner').hide();
	    	currentElem.attr('disabled', false);
	    }
	  });
	});

	$('.hourdetail2').click(function () {
		var mapdate = $(this).data('mdate');
		var emp = $(this).data('mid');

		var rowDate = $(this).data('row_date');
		var employeeName = $(this).data('employee_name');
		$.ajax({
			type: 'GET',
			dataType: 'json',
			url: ajaxUrls.getWorkedHourDetails,
			data: {
				'eid': emp,
				'mapdate': mapdate,
			},
			success: function (data) {
				//$('#hourdetails').html(data2);
				var totalDistance = 0;
				var totalTimeDifference = 0;
				var html = "";

				data.reverse();
				$.each(data, function(index, item) {
					var tempDistance = getTotalDistanceFromLatLngArray(item.locations,false);
					var tempDifference = item.checkout - item.checkin;

					var checkOutTime = (tempDifference == 0) ?"<td><span class='label label-danger'> N/A </td>":"<td>"+item.cout_time+"</td>";
					html = html + "<tr> <td>"+item.cin_time+"</td>"+checkOutTime+"<td>"+tempDistance.toFixed(3)+ "&nbsp;KM</td> </tr>";
					totalDistance = totalDistance + tempDistance;
					totalTimeDifference = totalTimeDifference + tempDifference;
				});
				var totalHourString = "Total Worked Hour: "+getHourMinuteString(totalTimeDifference);
				var totalDistanceString = "Total Distance Travelled: "+totalDistance.toFixed(3)+" KM";
				$("#emp_name_date").html(employeeName+"("+rowDate+")");
				$("#total_hr_string").html(totalHourString);
				$("#total_distance_string").html(totalDistanceString);
				$("#cicolist").html(html);
				$("#workhours").modal('show');
			}
		});
	});

});

function getTotalDistanceString(totalDistance){
	var tempString = "";
	if(totalDistance != undefined){

		var temp = (totalDistance/1000).toFixed(3);
		tempString = "Total Distance = "+temp+" KM";
		
	}
	return tempString;
}

function getHourMinuteString(time){
	var totalMinutes = time/(1000*60);

	var hours = Math.floor(totalMinutes / 60);
	var minutes = totalMinutes % 60;
	return hours +" hr "+minutes.toFixed(0)+"min";
}

function getTotalDistanceFromLatLngArray(tempArray,returnString) {

	var totalDistanceStr = "";
	var totalDistance = 0;
	var R = 6371; // km (change this constant to get miles)
	var iteration = 0;
	var lat1,lon1,lat2,lon2;
	
	if(tempArray == null || tempArray == undefined)return (returnString == true)?totalDistanceStr:totalDistance;
	if(tempArray.length <2) return (returnString == true)?totalDistanceStr:totalDistance;


	tempArray.forEach(function(tempLocation){

		if(iteration == 0){
			lat1 = tempLocation.latitude;
			lon1 = tempLocation.longitude;
		}

		lat2 = tempLocation.latitude;
		lon2 = tempLocation.longitude;

		var dLat = (lat2-lat1) * Math.PI / 180;
		var dLon = (lon2-lon1) * Math.PI / 180;
		var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180 ) * Math.cos(lat2 * Math.PI / 180 ) * Math.sin(dLon/2) * Math.sin(dLon/2);
		var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
		var distance = R * c;
		totalDistance = totalDistance + distance;
		lat1 = lat2;
		lon1 = lon2;
		iteration++;
	});

	if(returnString){
		return "Total Distance = "+totalDistance.toFixed(3)+" KM";
	} else {
		return totalDistance;
	}
}
