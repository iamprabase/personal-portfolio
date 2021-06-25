var geocoder;
var map;
var marker;

//Initialization of google map with clickable marker and dragable
function initialize() {

    var initialLat = $('#search_latitude').val();
    var initialLong = $('#search_longitude').val();
    initialLat = initialLat ? initialLat : latitude;
    initialLong = initialLong ? initialLong : longitude;

    var latlng = new google.maps.LatLng(initialLat, initialLong);
    var options = {
        zoom: 15,
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

    map.addListener('click', function (event) {
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
$(function () {
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

    marker.setMap(null);
});


$(document).on('change', '.btn-file :file', function () {
    var input = $(this),
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [label]);
});

$(document).on("change", ".uploadFile", function (e) {
    e.preventDefault();
    if (this.files[0].size / 1024 / 1024 > 2) {
        alert('File Size cannot be more than 2MB');
        $(this).val(null);
        return;
    }

    var uploadFile = $(this);
    var files = !!this.files ? this.files : [];
    if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

    if (/^image/.test(files[0].type)) { // only image file
        $(this).closest(".imgUp").find('.imagePreview').empty();
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file

        reader.onloadend = function () { // set image data as background of div
            uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url(" + this.result + ")").addClass('display-imglists').attr('src', this.result);
            $('#clearImage').removeClass('hide');
        }
    } else {
        alert('Only jpeg, jpg, png, svg file types are accepted.');
        $(this).val(null);
        return;
    }
});

$('#clearImage').click(function () {
    $(this).addClass('hide');
    $('.uploadFile').val('');
    $('.imagePreview').removeAttr('src');
    $('.imagePreview').removeAttr('style');
    $('.imagePreview').css('background:url("../../../cms/storage/app/public/uploads/addPhoto.png")');
});

$('.select2').select2();
$('#party_type').select2({
    placeholder: "Select Party Type"
});

$('#business_id').select2({
    placeholder: "Select Business Type"
});

$('#startdate').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
});

$('#enddate').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
});

$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue'
});


$('#multiphone').keypress(function (e) {
    var regex = new RegExp("^[-0-9,\-\/\+\(\)]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
});

function makeRed(phoneBox) {
    phoneBox.css('border-color', 'red');
}

function makeGreen(phoneBox) {
    phoneBox.css('border-color', 'green');
}



$(".multiselect").select2({
    placeholder: "Please Select"
});


$(function () {
    $(document).on("change", ".uploadFile", function () {
        var uploadFile = $(this);
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

        if (/^image/.test(files[0].type)) { // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file

            reader.onloadend = function () { // set image data as background of div
                uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url(" + this.result + ")");
            }
        }

    });
});

function bs_input_file() {
    $(".input-file").before(
        function () {
            if (!$(this).prev().hasClass('input-ghost')) {
                var element = $("<input type='file' class='input-ghost fileupload' style='visibility:hidden; height:0'>");
                element.attr("name", $(this).attr("name"));
                element.change(function () {
                    var filePath = this.files[0].name;
                    var allowedExtensions = /(\.pdf|\.csv|\.xls|\.xlsx|\.doc|\.docx|)$/i;
                    if (!allowedExtensions.exec(filePath)) {
                        alert('Please upload file having extensions .pdf, .csv, .xls , .xlsx, .doc, .docx, .png, .jpg, .jpeg only.');
                        fileInput.value = '';
                        return false;
                    }

                    if (this.files[0].size / 1024 / 1024 > 2) {
                        alert('File Size cannot be more than 2MB');
                        $(this).child(".input-file").find('input').val('');
                        return;
                    }
                    element.next(element).find('input').val((element.val()).split('\\').pop());
                });
                $(this).find("button.btn-choose").click(function () {
                    element.click();

                });
                $(this).find("button.btn-reset").click(function () {
                    element.val(null);
                    $(this).parents(".input-file").find('input').val('');
                });
                $(this).find('input').css("cursor", "pointer");
                $(this).find('input').mousedown(function () {
                    $(this).parents('.input-file').prev().click();
                    return false;
                });
                return element;
            }
        }
    );
}

$(function () {
    bs_input_file();
});

function initMap() {
    var input = document.getElementById('searchMapInput');

    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        document.getElementById('location-snap').innerHTML = place.formatted_address;
        document.getElementById('lat-span').innerHTML = place.geometry.location.lat();
        document.getElementById('lon-span').innerHTML = place.geometry.location.lng();
    });
}
