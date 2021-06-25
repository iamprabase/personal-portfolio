@extends('layouts.company')
@section('title', 'Edit Party')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}" />
<style>
  #img-upload {
    width: 80%;
    height: 80%;
  }
  .panel-heading{
    color: #fff!important;
    background-color: #0b7676!important;
  }
  .del-img {
    position: absolute;
    right: 32px;
    width: 30px;
    height: 30px;
    text-align: center;
    line-height: 30px;
    background-color: rgba(255,255,255,0.6);
    cursor: pointer;
  }
  .imagePreview2 {
    background: url(/cms/storage/app/public/uploads/nopartyimage.png);
    background-color: grey;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center center;
  }
  .clientImageExists{
    background:none;
    background-color:grey;
    background-size: contain; 
    background-repeat: no-repeat; 
    background-position: center center;
  }
    .box-body .btn-primary {
    background-color: #079292!important;
    border-color: #079292!important;
    color: #fff!important;
}
.btn-primary:hover, .btn-primary:active, .btn-primary.hover {
    background-color: #0b7676!important;
    border-color: #0b7676!important;
}
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ccc;
}

.multiselect-selected-text {
    margin-right: 90px;
    color: #333 !important;
  }
  .multiselect-selected-text{
    margin-right: 0px;
  }

  .multiselect.dropdown-toggle.btn.btn-default .caret {
    position: relative;
    margin-top: 10px;
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

@section('content')
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      @if (\Session::has('phone-number-error'))
      <div class="alert alert-warning">
        <p>{{ \Session::get('phone-number-error') }}</p>
      </div><br />
      @endif
    </div>
  </div>

  <div class="box box-default">

    <div class="box-header with-border">
      <h3 class="box-title">Party Information</h3>
      <div class="box-tools pull-right">
        <div class="col-md-7 page-action text-right">
          <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"> <i class="fa fa-arrow-left"></i> Back</a>
        </div>
      </div>
    </div>

    <div class="box-body">

      {!! Form::model($client, array('url' => url(domain_route('company.admin.client.update',[$client->id])) , 'method'
      => 'PATCH', 'files'=> true)) !!}

      @include('company.clients._form')
      
      {!! Form::submit('Save Changes', ['class' => 'btn btn-primary pull-right keySubmit']) !!}
      {!! Form::close() !!}

    </div>

  </div>

</section>
@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>
<script>
  function validateCompanyName(companyName, el){
    let field = el.attr("id")
      let formData
    if(field == "company_name") formData = {"company_name": companyName}
    else  formData = {"mobile": companyName}
    $.ajax({
      "url": "{{domain_route('company.admin.client.validateCompanyName')}}",
      "dataType": "json",
      "type": "POST",
      "data":{
          _token: "{{csrf_token()}}",
          id: "{{$client->id}}",
          field_name: field,
          ...formData
      },
      beforeSend:function(url, data){
        $(el).parent().find('.has-error').html('');
        $('.keySubmit').attr('disabled', true);
        el.parent().removeClass('has-error');
      },
      success: function(res){
        $(el).parent().find('.has-error').html('');
        $('.keySubmit').attr('disabled', false);
        el.parent().removeClass('has-error');
      },
      error:function(xhr){
        if(xhr.status==422){
            let msg = xhr.responseJSON.errors.company_name? xhr.responseJSON.errors.company_name[0]: xhr.responseJSON.errors.mobile[0];
            $(el).parent().find('.has-error').html(msg);
          el.parent().addClass('has-error');
        }
        $('.keySubmit').attr('disabled', true);
      },
    });
  }
  $(document).on("focusout", '#company_name', function(e)  {
    let current = $(this);
    let companyName = current.val();
    if(companyName){
      validateCompanyName(companyName, current);
    }
  });
  $(document).on("focusout", '#mobile', function(e)  {
      let current = $(this);
      let mobile = current.val();
      if(mobile){
          validateCompanyName(mobile, current);
      }
  });
  //----defining google maps variables------
  var geocoder;
  var map;
  var marker;

  /* initializing google map*/
  function initialize() {
      var initialLat = $('#search_latitude').val();
      var initialLong = $('#search_longitude').val();
      var focusLat = $('#search_latitude').val();
      var focusLong = $('#search_longitude').val();
      initialLat = initialLat?initialLat:{{config('settings.latitude')}};
      initialLong = initialLong?initialLong:{{config('settings.longitude')}};

      var latlng = new google.maps.LatLng(initialLat, initialLong);
      var options = {
          zoom: 16,
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

      map.addListener('click', function(event) {
        addMarker(event.latLng);
        marker.setVisible(true);
      });

      function addMarker(location) {
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
      console.log(focusLat);
      console.log(focusLong);

      if(focusLat == "" || focusLong == ""){
        marker.setVisible(false);
      }

  }

  $(document).ready(function () {
      //load google map
      initialize();
      // autocomplete location search
      const autocomplete = new google.maps.places.Autocomplete(document.getElementById('search_addr'));
      // Bind the map's bounds (viewport) property to the autocomplete object,
      // so that the autocomplete requests use the current map bounds for the
      // bounds option in the request.
      autocomplete.bindTo("bounds", map);
      const infowindow = new google.maps.InfoWindow();
      const infowindowContent = document.getElementById("infowindow-content");
      infowindow.setContent(infowindowContent);
      autocomplete.addListener("place_changed", () => {
        infowindow.close();
        marker.setVisible(false);
        const place = autocomplete.getPlace();

        $('#search_addr').val(place.name);
        $('#search_latitude').val(place.geometry.location.lat());
        $('#search_longitude').val(place.geometry.location.lng());
        var latlng = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
        marker.setPosition(latlng);
        initialize();
      });
      
      // var PostCodeid = '#search_addr';
      // $(function () {
      //     $(PostCodeid).autocomplete({
      //         source: function (request, response) {
      //             geocoder.geocode({
      //                 'address': request.term
      //             }, function (results, status) {
      //                 response($.map(results, function (item) {
      //                     return {
      //                         label: item.formatted_address,
      //                         value: item.formatted_address,
      //                         lat: item.geometry.location.lat(),
      //                         lon: item.geometry.location.lng()
      //                     };
      //                 }));
      //             });
      //         },
      //         select: function (event, ui) {
      //             $('#search_addr').val(ui.item.value);
      //             $('#search_latitude').val(ui.item.lat);
      //             $('#search_longitude').val(ui.item.lon);
      //             var latlng = new google.maps.LatLng(ui.item.lat, ui.item.lon);
      //             marker.setPosition(latlng);
      //             initialize();
      //         }
      //     });
      // });

      /*
       * Point location on google map
       */
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
  });

  $(function () {

      // initializeMap();

      $('.select2').select2();

      $('#party_type').select2({
        placeholder: "Select Party Type"
      });
      $('#business_id').select2({
        placeholder: "Select Business Type"
      });
      $('#dob').datepicker({

          format: 'yyyy-mm-dd',

          autoclose: true,

      });

      $('#doj').datepicker({

          format: 'yyyy-mm-dd',

          autoclose: true,

      });

      $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({

          checkboxClass: 'icheckbox_minimal-blue',

          radioClass: 'iradio_minimal-blue'

      });

      // CKEDITOR.replace('about');

  });

  $(document).on("change",".uploadFile", function(e)
  {
      e.preventDefault();
      if(this.files[0].size/1024/1024>2){
        alert('File Size cannot be more than 2MB');
        $(this).val(null);
        return;
      }

      var uploadFile = $(this);
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

      if (/^image/.test( files[0].type)){ // only image file
          $(this).closest(".imgUp").find('.imagePreview').empty();
          var reader = new FileReader(); // instance of the FileReader
          reader.readAsDataURL(files[0]); // read the local file

          reader.onloadend = function(){ // set image data as background of div
          uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url("+this.result+")").addClass('display-imglists').attr('src',this.result);
          $('#clearImage').removeClass('hide');
          }
      }else{
        alert('Only jpeg, jpg, png, svg file types are accepted.');
        $(this).val(null);
        return;
      }        
  });

  $('#clearImage').click( function(){
    $(this).addClass('hide');
    $('.uploadFile').val('');
    $('#confirmremove').val('true');
    $('.imagePreview').html('');
    $('.imagePreview').removeAttr('src');
    $('.imagePreview').removeAttr('style');
    $('.imagePreview').removeClass('clientImageExists');
    $('.imagePreview').css('background:url("../../../cms/storage/app/public/uploads/addPhoto.png")');
  });

  $('select[name="country"]').on('change', function () {
      var countryId = $(this).val();
      $('#state').append($('<option selected="selected"></option>').html('Loading...'));
      if (countryId) {
          $.ajax({
              url: '/get-state-list?country_id=' + countryId,
              type: "GET",
              dataType: "json",
              success: function (data) {
                  $("#state").empty();
                  $('#city').empty();
                  $("#city").append('<option value="">Select a City</option>');
                  $("#state").append('<option value="">Select a State</option>');
                  $.each(data, function (key, value) {
                      $("#state").append('<option value="' + key + '">' + value + '</option>');
                  });
                  $.ajax({
                      url: '/clients/phonecode/get/' + countryId,
                      type: "GET",
                      success: function (data) {
                          $('#phonecode').val(data);
                      }
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
      var stateId = $(this).val();
      $('#city').append($('<option selected="selected"></option>').html('Loading...'));
      if (stateId) {
          $.ajax({
              url: '/get-city-list?state_id=' + stateId,
              type: "GET",
              dataType: "json",
              success: function (data) {
                  $("#city").empty();
                  $("#city").append('<option value="">Select a City</option>');
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
  const hasChild = "{{hasClientChild($client->id)}}";

  $(document).ready(function () {
      if(hasChild) $('#party_type').prop("disabled", true);
      var client_type = '{{ $client->client_type }}';
      var client_id = '{{ $client->id }}';
      var superior = @if($client->superior!=NULL){{ $client->superior}}@else @php echo "' '"; @endphp @endif;
      if (client_type) {
          $.ajax({
              url: '/admin/client/getsuperiorlist',
              type: "GET",
                data: {client_type: client_type, cid: client_id},
                 dataType: "json",
              cache: false,
              success: function (data) {
                  $("#superior").empty();
                  $('<option></option>').val("").text("{{$companyName}}").appendTo('#superior');
                  if(data.length==0 || hasChild) $("#superior").prop('disabled', true);
                  else $("#superior").prop('disabled', false);
                  $.each(data, function (i, item) {

                      $('<optgroup />').prop('label', i).appendTo('#superior');
                      $.each(item, function (key, value) {
                          if (value.id == superior) {
                              $('<option></option>').val(value.id).text(value.company_name).appendTo('#superior').prop('selected', true);
                              //  $('<option/>').prop('selected', true);

                          } else {
                            if(value.status=="Active") $('<option></option>').val(value.id).text(value.company_name).appendTo('#superior');
                          }
                      });
                  });

              }
          });
      } else {
          $('#superior').empty();
      }
  });

  $('select[name="client_type"]').on('change', function () {
      var client_type = $(this).val();
      var client_id = '{{ $client->id }}';
      if (client_type) {
          $.ajax({
              url: '/admin/client/getsuperiorlist',
              type: "GET",
               data: {client_type: client_type, cid: client_id},
              dataType: "json",
              cache: false,
              success: function (data) {
                  $("#superior").empty();
                  $('<option></option>').val("").text("{{$companyName}}").appendTo('#superior');
                  if(data.length==0 || hasChild) $("#superior").prop('disabled', true);
                  else $("#superior").prop('disabled', false);
                  $.each(data, function (i, item) {

                      $('<optgroup />').prop('label', i).appendTo('#superior');
                      $.each(item, function (key, value) {
                        if(value.status=="Active") $('<option></option>').val(value.id).text(value.company_name).appendTo('#superior');
                      });
                  });

              }
          });
      } else {
          $('#superior').empty();
      }
  });

  $('#city').on("change", function(){
    let selVal = $(this).val();
    if(selVal=="Loading..."){
      selVal = "";
    }
    if(selVal!=""){
      $.ajax({
        "url": "{{domain_route('company.admin.client.fetchcitywisebeats')}}",
        "method": "GET",
        "data": {
          "selCity": selVal, 
        },
        beforeSend: function(){

        },
        success: function(data){
          let beats = JSON.parse(data);
          $('#beat').select2('destroy');
          $('#beat').empty();
          $('#beat').append(`<option value="" selected="selected">Select a City</option>`);
          $.each(beats, function(i, beat){
            $('#beat').append(`<option value="${beat['id']}">${beat['name']}</option>`);
          });
          $('#beat').select2({
            "placeholder": "Select Beat",
          });
        },
        error: function(jqXhr, textStatus){

        },
        complete: function(){

        }
      });
    }else if(selVal==""){
      let beats = @json($beats);
      $('#beat').select2('destroy');
      $('#beat').empty();
      $('#beat').append(`<option>Select Beat</option>`);
      $.each(beats, function(i, beat){
        $('#beat').append(`<option value="${beat['id']}">${beat['name']}</option>`);
      });
      $('#beat').select2({
        "placeholder": "Select Beat",
      });
    }
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

  $(document).on('click','.custom_image_remove',function(){
    var deletedField = $(this).data('action');
    var originalField = $(this).data('field');
    var valOriginal = $('#'+originalField+'-original').data('value');
    console.log(valOriginal);
    var valOriginal = valOriginal-1;
    $('#'+originalField+'-original').data('value',valOriginal);
    if($('#'+deletedField).val()==""){
      $('#'+deletedField).val($(this).data('name'));
    }else{
      $('#'+deletedField).val($('#'+deletedField).val()+','+$(this).data('name'));
    }
    $(this).parent().remove();
  });

  // $(document).on('change','.custom_field_files',function(e){
  //     e.preventDefault();
  //     let oldNumber = $(this).data('value');
  //     let currentNumber = this.files.length;
  //     let totalNumber = oldNumber + currentNumber;
  //     let flag = true;
  //     if(totalNumber>3){
  //       alert('Max 3 files allowed');
  //       $(this).val(null);
  //       return;
  //     }
  //     $.each(this.files,function(k,v){
  //       if(v.size/1024/1024>2){
  //         flag = false;
  //       }
  //     });
  //     if(flag==false){
  //       alert('File Size cannot be more than 2MB');
  //       $(this).val(null);
  //       return;
  //     }
  // });

  function makeRed(phoneBox){
    phoneBox.css('border-color','red');
  }

  function makeGreen(phoneBox){
    phoneBox.css('border-color','green');
  }

  $(document).on('keyup','.phone_numbers',function(e){
    e.preventDefault();
    let phoneBox  = $(this);
    let val       = $(this).val();
    let length    = val.length;
    let i         = 0;
    let startText = 'red';
    let midText   = 'green';
    let endText   = 'red';
    if((val[0]=='+') || (parseInt(val[0])>=0 && parseInt(val[0])<=9) ){
      startText = 'green';
    }
    if(val[val.length-1]>=0 && val[val.length-1]<=9 ){
      endText ='green';
    }

    for(i=1;i<val.length;i++){
      if(val[i]=='-' || (val[i]>=0 && val[i]<=9) ){
        // console.log('mid text ok');
      }else{
        midText = 'red';
      }
    }

    if(startText == 'green' && midText == 'green' && endText == 'green'){
      makeGreen(phoneBox);
        $('.keySubmit').removeAttr('disabled');
    }else{
      if(val.length==0){
        makeGreen(phoneBox);
        $('.keySubmit').removeAttr('disabled');
      }else{
        $('.keySubmit').attr('disabled','true');
        makeRed(phoneBox);
      }
    }
  });
      $(".multiselect").select2({
    placeholder: "Please Select"
});


// var numItems = $('.multiimg').length;
// alert(numItems);
$( ".form-group .multiimg" ).each(function( index ) {
   var imgdivid = $( this ).attr('id');
   var Imgcount = $("#"+imgdivid+" .imgUp").length;
  if(Imgcount >= 3){
    $("#"+ imgdivid +" .imgAdd").hide();
  }
});
 
  $(".imgAdd").click(function(){
    var imggroupid=$(this).attr('data-id');
   var Imgcount = $("#"+ imggroupid +" .imgUp").length;
   var inputname=$(this).attr('data-name');
    if(Imgcount < 3){
      if(Imgcount == 2){
         $("#"+ imggroupid +" .imgAdd").hide();
      }
      $(this).closest(".form-group").find('.imgAdd').before('<div class="col-xs-4 imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input name="'+inputname+'" type="file" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fa fa-times del" data-id="'+imggroupid+'"></i></div>');          
    }else{
      $(".imgAdd").hide();
    }
  });
  $(document).on("click", "i.del" , function() {
    var imggroupid=$(this).attr('data-id');
    //var Imgcount = $("#imggroup .imgUp").length;
    var Imgcount = $("#"+ imggroupid +" .imgUp").length;
    if(Imgcount<3){
     // $("#"+ imggroupid +" .imgAdd").show();
     $("#"+ imggroupid +" .imgAdd").show();
    }

     var deletedField = $(this).data('action');
    var originalField = $(this).data('field');
    var valOriginal = $('#'+originalField+'-original').data('value');
    console.log(valOriginal);
    var valOriginal = valOriginal-1;
    $('#'+originalField+'-original').data('value',valOriginal);
    if($('#'+deletedField).val()==""){ //alert('nnnnn');
    // alert($(this).data('name'));
      $('#'+deletedField).val($(this).data('name'));
    }else{ //alert('yyyyy');
    // alert($(this).data('name'));
      $('#'+deletedField).val($('#'+deletedField).val()+','+$(this).data('name'));
    }
    $(this).parent().remove();
    var Imgcount = $("#"+ imggroupid +" .imgUp").length;
    if(Imgcount<3){
     // $("#"+ imggroupid +" .imgAdd").show();
     $("#"+ imggroupid +" .imgAdd").show();
    }
   // alert(imggroupid);
   //$(this).closest('.imgAdd').show();
    // $("#"+ imggroupid +" .imgAdd").show();

  });
  $(document).on("click", "label.upload" , function() {
    var deletedField = $(this).data('action');
    var originalField = $(this).data('field');
    var valOriginal = $('#'+originalField+'-original').data('value');
    //console.log(valOriginal);
    var valOriginal = valOriginal-1;
    $('#'+originalField+'-original').data('value',valOriginal);
    if($('#'+deletedField).val()==""){ //alert('nnnnn');
    // alert($(this).data('name'));
      $('#'+deletedField).val($(this).data('name'));
    }else{ //alert('yyyyy');
    // alert($(this).data('name'));
      $('#'+deletedField).val($('#'+deletedField).val()+','+$(this).data('name'));
    }
  });
  $(function() {
    $(document).on("change",".uploadFile", function()
    {
      $(this).closest(".imgUp").find('.imagePreview').empty();
      $(this).closest(".imgUp").find('span').empty();
      var uploadFile = $(this);
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

      if (/^image/.test( files[0].type)){ // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file

        reader.onloadend = function(){ // set image data as background of div
          uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url("+this.result+")");
        }
      }
      
    });
  });

  function bs_input_file() {
        $(".input-file").before(
          function() {
            if ( ! $(this).prev().hasClass('input-ghost') ) {
              var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
              element.attr("name",$(this).attr("name"));
              element.change(function(){
                var filePath = this.files[0].name;
                var allowedExtensions = /(\.pdf|\.csv|\.xls|\.xlsx|\.doc|\.docx|\.png|\.jpe?g)$/i;
                if(!allowedExtensions.exec(filePath)){
                    alert('Please upload file having extensions .pdf, .csv, .xls , .xlsx, .doc, .docx, .png, .jpg, .jpeg only.');
                    fileInput.value = '';
                    return false;
                }
               
                 if(this.files[0].size/1024/1024>2){
            alert('File Size cannot be more than 2MB');
            $(this).child(".input-file").find('input').val('');
            return;
          }
                element.next(element).find('input').val((element.val()).split('\\').pop());
              });
              $(this).find("button.btn-choose").click(function(){
                element.click();
              });
              $(this).find("button.btn-reset").click(function(){
                element.val(null);
                $(this).parents(".input-file").find('input').val('');
              });
              $(this).find('input').css("cursor","pointer");
              $(this).find('input').mousedown(function() {
                $(this).parents('.input-file').prev().click();
                return false;
              });
              return element;
            }
          }
        );
      }
      $(function() {
        bs_input_file();
      });

      function initMap() {
    var input = document.getElementById('searchMapInput');
  
    var autocomplete = new google.maps.places.Autocomplete(input);
   
    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
    });
}

@if(config('settings.category_wise_rate_setup') == 1)
var checkedCategoryId = @json($current_categoryid);


$('#categoryRates').multiselect({
  enableFiltering: true,
  enableCaseInsensitiveFiltering: true,
  enableFullValueFiltering: false,
  enableClickableOptGroups: false,
  includeSelectAllOption: false,
  enableCollapsibleOptGroups : true,
  selectAllNumber: false,
  numberDisplayed: 1,
  nonSelectedText:"Select Category Rates",
  allSelectedText:"All Selected",
  onChange:function(element, isChecked){
    let categoryId = element.data("categoryid")
    if(isChecked){
      // if($.inArray(categoryId, checkedCategoryId) == -1){ 
      //   checkedCategoryId = [...checkedCategoryId, categoryId]
      // }
      // else{ 
      //   $('#categoryRates').multiselect('deselect', [element.val()])
      //   alert("Cannot apply multiple rates of same categories."); 
      // }
      let arr = {}
      $('#categoryRates').multiselect('deselect', checkedCategoryId[categoryId])
      arr[categoryId] = element.val()
      checkedCategoryId = {...checkedCategoryId, ...arr}
      
      $('#categoryRates').multiselect('select', element.val())
    }else{
      // checkedCategoryId = checkedCategoryId.filter((el, ind) => el != categoryId)
      delete checkedCategoryId[categoryId]
    }
  }
});
$('#categoryRates').multiselect('select', @json($current_category_rates_id))
@endif   
</script>
@endsection