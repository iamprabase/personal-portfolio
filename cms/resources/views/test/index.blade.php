<?php ?>
        <!DOCTYPE html>
<html>
<head>
    <title></title>

    <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase-database.js"></script>
    <!-- <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script> -->

    <script src="http://maps.google.com/maps/api/js?key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8"
            type="text/javascript"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

    <script type="text/javascript">

        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
        };

        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }


    </script>


    <script>

        var config = {
            apiKey: "AIzaSyAuTEw0i5xIt5Cw3j1oK4MB8CBiqCQnPzg",
            authDomain: "local-deltasalesapp.firebaseapp.com",
            databaseURL: "https://local-deltasalesapp.firebaseio.com",
            projectId: "local-deltasalesapp",
            storageBucket: "local-deltasalesapp.appspot.com",
            messagingSenderId: "473577038478"
        };

        /*var config = {
          apiKey: "AIzaSyB3TymwAcW_6w76hGHVybjH1aI6B6mXYec",
          authDomain: "stagingdeltasalesapp.firebaseapp.com",
          databaseURL: "https://stagingdeltasalesapp.firebaseio.com",
          projectId: "stagingdeltasalesapp",
          storageBucket: "stagingdeltasalesapp.appspot.com",
          messagingSenderId: "1054633145586"
        };*/

        /*var config = {
          apiKey: "AIzaSyBauDm-ufwpat9p17NHHilRk7io3ZQYqTI",
          authDomain: "deltasalesapp.firebaseapp.com",
          databaseURL: "https://deltasalesapp.firebaseio.com",
          projectId: "deltasalesapp",
          storageBucket: "deltasalesapp.appspot.com",
          messagingSenderId: "1040440272726"
        };*/

        firebase.initializeApp(config);
    </script>

</head>

<body>


<div id="map" style="width: 100%; height: 500px;"></div>


<p id="total_count"></p>

<script>
    var employeeID = getUrlParameter("id");
    var date = (getUrlParameter("date") == undefined) ? new Date().toISOString().slice(0, 10) : getUrlParameter("date");
    var company = (getUrlParameter("company") == undefined) ? "sonatech" : getUrlParameter("company");
    var refPath = "locations/" + company + "/" + date + "/empid:" + employeeID;
    var lskey = window.location.hostname + "/" + refPath;
    var filter = getUrlParameter("filter");
    var ref = firebase.database().ref(refPath);
    console.log(refPath);
    var mdata = [];
    var finaldata = [];
    var tempArray = [];

    var prevData = window.localStorage.getItem(lskey);
    //if(prevData == null || prevData == undefined){
    if (true) {

        ref.on("value", function (snapshot) {
            mdata = [];
            finaldata = [];

            snapshot.forEach(function (childSnapshot) {
                var childData = childSnapshot.val();
                var latitude = childData.latitude;
                var longitude = childData.longitude;
                var datetime = childData.datetime;
                var still = childData.still;
                var push = true;
                var tempObj = {
                    lat: latitude,
                    lng: longitude,
                    accuracy: childData.accuracy,
                    dt: datetime,
                    still: still,
                    speed: childData.speed,
                    speed_accuracy: childData.speed_accuracy,
                    distance_from_last_gps: childData.distance_from_last_gps,
                };
                //debugger;
                finaldata.push(tempObj);

                if (filter == "true") {

                    var dis = childData.distance_from_last_gps;
                    var ac = childData.accuracy;
                    if ( dis < 10 || ac > 60) {

                        push = false;

                        /*tempArray.push(tempObj);
                        if (tempArray.length > 1) {
                            push = false;
                        }*/
                    } else {

                        console.log(dis+"/"+ac);

                        push = true;
                        //tempArray = [];
                    }
                }


                if (push) {
                    mdata.push(tempObj);
                }
            });

            console.log("finaldata:" + finaldata.length);
            console.log("mdata:" + mdata.length);
            $("#total_count").text("Total Data:" + mdata.length);


            if (mdata.length > 0) {

                //window.localStorage.setItem(lskey,JSON.stringify(mdata));


                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 14,
                    center: new google.maps.LatLng(mdata[0].lat, mdata[0].lng),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                var infowindow = new google.maps.InfoWindow();
                var marker, i;


                $.each(mdata, function (key, value) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(value.lat, value.lng),
                        map: map,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            fillColor: '#F70000',
                            fillOpacity: 0.6,
                            strokeColor: '#F70000',
                            strokeOpacity: 0.9,
                            strokeWeight: 1,
                            scale: 5
                        }
                    });

                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            /* var milis = parseInt(value.unix_timestamp);
                             var date = new Date(milis);

                             console.log(date);*/
                            infowindow.setContent(
                                "DateTime:" + value.dt + "<br>LatLng:" + value.lat + "," + value.lng + "<br>Location Accuracy:" + value.accuracy +"<br>Distance Dif:" + value.distance_from_last_gps + "<br>still:" + value.still + "<br>speed:" + value.speed + " m/s<br>Speed Accuracy:" + value.speed_accuracy
                            );
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                });
            }
        });

    } else {
        //use localstorage data

        mdata = JSON.parse(prevData);

        console.log("finaldata:" + finaldata.length);
        console.log("mdata:" + mdata.length);
        $("#total_count").text("Total Data:" + mdata.length);


        if (mdata.length > 0) {

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: new google.maps.LatLng(mdata[0].lat, mdata[0].lng),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var infowindow = new google.maps.InfoWindow();
            var marker, i;


            $.each(mdata, function (key, value) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(value.lat, value.lng),
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: '#F70000',
                        fillOpacity: 0.6,
                        strokeColor: '#F70000',
                        strokeOpacity: 0.9,
                        strokeWeight: 1,
                        scale: 5
                    }

                });

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        /* var milis = parseInt(value.unix_timestamp);
                         var date = new Date(milis);

                         console.log(date);*/
                        infowindow.setContent(
                            "DateTime:" + value.dt + "<br>LatLng:" + value.lat + "," + value.lng + "<br>Location Accuracy:" + value.accuracy + "<br>still:" + value.still + "<br>speed:" + value.speed + " m/s<br>Speed Accuracy:" + value.speed_accuracy
                        );
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            });
        }

    }


</script>
</body>
</html>