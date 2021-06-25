@extends('layouts.app')
@section('title', 'Company Settings')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<!-- Bootstrap time Picker -->
<link rel="stylesheet" href="{{asset('assets/plugins/timepicker/bootstrap-timepicker.css')}}">
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
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
   .layoutLogo {
            width: 275px;
        }
        .layoutfavicon {
            width: 50px;
        }
  /**/
  .riw-item {
    width: 100%;
    padding: 20px 10px;
    background-color: #3494aa;
    float: left;
    margin-bottom: 20px;
    min-height: 115px;
  }
  .info .item-sec:nth-child(1) .riw-item {
    background-color: #20c5cb;
  }
  .info .item-sec:nth-child(2) .riw-item {
    background-color: #f4884a;
  }
  .info .item-sec:nth-child(3) .riw-item {
    background-color: #00b393;
  }
  .info .item-sec:nth-child(4) .riw-item {
    background-color: #8c8c8c;
  }
  .info .item-sec:nth-child(5) .riw-item {
    background-color: #f54646;
    padding-top: 50px;
  }
  .info .item-sec:nth-child(6) .riw-item {
    background-color: #56c16c;
  }
  .site-tital {
    margin-top: 0px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 10px;
    margin-bottom: 20px;
  }
  .note {
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 4px;
    background: #f5fffd;
    margin-bottom: 20px;
  }
  .note h3 {
    margin-top: 0px;
  }
  .input-group {
    position: relative;
    display: table;
    border-collapse: separate;
  }
  .input-group-btn {
    position: relative;
    font-size: 0;
    white-space: nowrap;
  }
  .input-group-btn > .btn {
    position: relative;
  }
  .btn-default {
    border: 1px solid #ccc !important;
  }
  .btn {
    padding: 9px 15px;
  }
  .brows {
    position: relative;
    overflow: hidden;
  }
  .btn.btn-file > input[type='file'] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    opacity: 0;
    filter: alpha(opacity=0);
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
  }
  input[type=file] {
    display: block;
  }
  button, input, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
  }
  #img-upload, #img-upload1, #img-upload2 {
    margin: 20px 0px;
  }
  #myTabContent {
    margin-top: 0px;
  }
  #myTabs li {
    width: 100%;
    border-bottom: 1px solid #ccc;
  }
  .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
    color: #555;
    cursor: default;
    background-color: #fff;
    border-left: 2px solid #20c5cb;
    border-bottom-color: transparent;
    border-right: 0px solid #ccc;
  }
  .nav-tabs > li.active:first-child > a {
    border-top: 1px solid transparent;
  }
  .nav-tabs > li > a:hover {
    border-color: transparent;
  }
  .nav > li > a:focus, .nav > li > a:hover {
    text-decoration: none;
    background-color: transparent;
  }
  .nav li a {
    border-left: 2px solid transparent;
  }
  .nav li.active a {
    text-decoration: none;
    /*background-color: #eee !important;*/
    margin-right: 0px;
    border-left: 2px solid #20c5cb;
    border-radius: 0px;
  }
  .tab-content {
    border: 1px solid #ccc;
    padding: 20px 20px 5px;
    border-radius: 4px;
    display: inline-block;
    width: 100%;
    background: #fff;
  }
  .nav-tabs {
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fff;
  }
  /**/
  a:hover {
    text-decoration: none;
  }
  .records-info-wrap .riw-item {
    width: 20%;
    padding: 10px 20px;
    background-color: #3494aa;
    float: left;
    border-radius: 4px;
  }
  .riw-item a, .riw-item span {
    color: #fff;
  }
  .riw-item span.riw-top {
    font-size: 31px;
    font-weight: 700;
  }
  .riw-item span.riw-middle {
    font-size: 19px;
    text-transform: uppercase;
  }
  .riw-item span.riw-bottom {
    font-size: 16px;
    margin-bottom: 5px;
    font-family: Montserrat, sans-serif !important;
  }
  .riw-item span {
    display: block;
    text-align: center;
  }
  .riw-item:hover {
    /*background-color: #43b8d4 !important;*/
  }
  .riw-item {
    -webkit-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
  }
  .tree, .tree ul {
    margin: 0;
    padding: 0;
    list-style: none
  }
  .tree ul {
    margin-left: 1em;
    position: relative
  }
  .tree ul ul {
    margin-left: .5em
  }
  .tree ul:before {
    content: "";
    display: block;
    width: 0;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    border-left: 1px solid
  }
  .tree li {
    margin: 0;
    padding: 0 1em;
    line-height: 2em;
    color: #369;
    font-weight: 700;
    position: relative
  }
  .tree ul li:before {
    content: "";
    display: block;
    width: 10px;
    height: 0;
    border-top: 1px solid;
    margin-top: -1px;
    position: absolute;
    top: 1em;
    left: 0
  }
  .tree ul li:last-child:before {
    background: #fff;
    height: auto;
    top: 1em;
    bottom: 0
  }
  .indicator {
    margin-right: 5px;
  }
  .tree li a {
    text-decoration: none;
    color: #369;
  }
  .tree li button, .tree li button:active, .tree li button:focus {
    text-decoration: none;
    color: #369;
    border: none;
    background: transparent;
    margin: 0px 0px 0px 0px;
    padding: 0px 0px 0px 0px;
    outline: 0;
  }
  .button-red i{
    display: block!important;
    color: red;
  }
  .button-red:active{
    -webkit-box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
    -moz-box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
    box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
  }
  .button-blue i{
    display: block!important;
    color: blue;
  }
  .button-blue:active{
    -webkit-box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
    -moz-box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
    box-shadow: inset 0 0px 0px rgba(0,0,0,0.125);
  }
  #tree1 li .btn{

    padding: 10px 1px 10px 5px;
  }
  #tree2 li .btn{

    padding: 10px 1px 10px 5px;
  }
  .ms-options-wrap > .ms-options > ul input[type="checkbox"] {
    margin: 15px 0px 0 0;
  }


  /* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 26px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.custom-panel-heading{
  background-color:#0b7676!important;
}
.panel-heading-text{
  margin-left: 15px;
  color:white;
}

</style>
@endsection
@section('content')
<section class="content">
  <div class="row ">
    @if (session()->has('active'))
    <?php $active = session()->get('active'); ?>
    @else
    <?php $active = 'profile' ?>
    @endif
    <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs">
      <div class="col-xs-3">
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
          <li role="presentation" class="{{($active == 'profile')? 'active':''}}"><a href="#company" id="compamy"
            role="tab" data-toggle="tab"
            aria-controls="company"
            aria-expanded="true">Profile</a>
          </li>
          <li role="presentation" class=""><a href="#company-details" role="tab" id="company-details-tab" data-toggle="tab"
              aria-controls="company-details" aria-expanded="false">Company Details</a>
          </li>
          {{-- <li role="presentation" class="{{($active == 'layout')? 'active':''}}"><a href="#admin" role="tab"
            id="admin-tab" data-toggle="tab"
            aria-controls="admin"
            aria-expanded="false">Admin
          Layout</a></li> --}}
          <!-- <li role="presentation" class="{{($active == 'email')? 'active':''}}"><a href="#email-setup" role="tab" id="email-setup-tab" data-toggle="tab" aria-controls="email-setup" aria-expanded="false">Email Setup</a></li> -->
          <li role="presentation" class="{{($active == 'other')? 'active':''}}"><a href="#setup" role="tab"
            id="setup-tab" data-toggle="tab"
            aria-controls="setup"
            aria-expanded="false">Setup</a>
          </li>
          <li role="presentation" class=""><a href="#plan-detail" role="tab" id="plan-detail-tab" data-toggle="tab"
            aria-controls="plan-detail" aria-expanded="false">Plan Detail</a></li>

          <!-- <li role="presentation" class=""><a href="#party-type" role="tab" id="party-type-tab" data-toggle="tab"
            aria-controls="party-type" aria-expanded="false">Manage Party Type</a>
          </li> -->
          <li role="presentation" class=""><a href="#modules" role="tab" id="modules-tab" data-toggle="tab"
            aria-controls="modules" aria-expanded="false">Plan and Modules</a>
          </li>
           <li role="presentation" class=""><a href="#modulesdisksize" role="tab" id="modulesdisksize-tab" data-toggle="tab"
            aria-controls="modulesdisksize" aria-expanded="false">Modules Disk Size</a>
          </li>
        </ul>
      </div>
      @include('admin.companies.settings._form')
    </div>
</div>
</section>
@endsection
@section('scripts')
<script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{asset('assets/plugins/settings/plans.js') }}"></script>
<!-- bootstrap time picker -->
<script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script>

  function validateMaxValue(el, maxVal, minVal){
    let value = el.value
    if(maxVal && value > maxVal){
      el.value = maxVal
      alert("Allowed Maximum of " + maxVal +" levels")
    }
    if(minVal && value<minVal){
      el.value = minVal
      alert("Minimum Level cannot be less than " + minVal +" levels.")
    } 
  }

  // function showLoader(){
  //   $("#loader1").removeAttr("hidden");
  //   $(".box-loaderClass").addClass("box-loader");
  // }
  // function hideLoader(){
  //   $("#loader1").attr("hidden", "hidden");
  //   $(".box-loaderClass").removeClass("box-loader");
  // }
  //New code for all modules setup
  <?php $customplan = $plans->where('custom','!=',0)->first(); ?>
  $('#moduleControl').on('submit',function(e){
    e.preventDefault();
      var url = $(this).attr('action');
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        beforeSend:function(){
            $('.moduleupdatekey').attr('disabled',true);
        },
        success: function (data) {
            $('.moduleupdatekey').attr('disabled',false);
            $('#choosePlan').empty();
            alert(data.message);
            @if(isset($customplan))
            var custom_plan = "{{$customplan->id}}";
            @else
            var custom_plan = "";
            @endif
            $.each(data.plans,function(k,v){
              if(custom_plan==v.id){
                $('#choosePlan').append('<option selected="selected" value="' + v.id + '">' + v.name + " (Custom)" + '</option>');
              }else{
                $('#choosePlan').append('<option value="' + v.id + '">' + v.name + '</option>');
              }
            });
        },
        error: function (xhr) {
          var errorReport = "Please ensure form matches following validation...\n";
          $.each(xhr.responseJSON.errors,function(k,v){
            errorReport = errorReport + "\n*"+k+": "+v;
          });
          alert(errorReport);
          $('.moduleupdatekey').removeAttr('disabled');
        },
    });
  });
  var totalModule =  '{{count($mainmodules)}}';
  // switches codes
  $(document).on('click','.toggle-all-switches',function(){
    if($(this).is(':checked')){
      $('.switches').prop('checked',true);
    }else{
      $('.switches').prop('checked',false);
    }
  });

  // $(document).on('click','.switches',function(){
  //   if($(this).hasClass('party') && !($(this).is(":checked"))){
  //     $('.orders').prop('checked',false);
  //     $('.party_files').prop('checked',false);
  //     $('.party_images').prop('checked',false);
  //     $('.notes').prop('checked',false);
  //     $('.collections').prop('checked',false);
  //     $('.pdcs').prop('checked',false);
  //     $('.beat').prop('checked',false);
  //     $('.returns').prop('checked',false);
  //     $('.stock_report').prop('checked',false);
  //     $('.dso').prop('checked',false);
  //     $('.dsobyunit').prop('checked',false);
  //     $('.ordersreport').prop('checked',false);
  //     $('.psoreport').prop('checked',false);
  //     $('.spwise').prop('checked',false);
  //     $('.dpartyreport').prop('checked',false);
  //     $('.dempreport').prop('checked',false);
  //     $('.accounting').prop('checked',false);
  //     $('.product').prop('checked',false);
  //     $('.analytics').prop('checked',false);
  //     $('.zero_orders').prop('checked',false);
  //     $('.ageing').prop('checked',false);
  //     $('.visit_module').prop('checked',false);
  //     $('.targets').prop('checked',false);
  //     $('.targets_rep').prop('checked',false);
  //   }
  //   if($(this).hasClass('livetracking') && !($(this).is(":checked"))){
  //     $('.gpsreports').prop('checked',false);
  //   }
  //   if($(this).hasClass('orders') && !($(this).is(":checked"))){
  //     $('.analytics').prop('checked',false);
  //     $('.accounting').prop('checked',false);
  //     $('.zero_orders').prop('checked',false);
  //     $('.ageing').prop('checked',false);
  //     $('.dso').prop('checked',false);
  //     $('.dsobyunit').prop('checked',false);
  //     $('.ordersreport').prop('checked',false);
  //     $('.psoreport').prop('checked',false);
  //     $('.spwise').prop('checked',false);
  //     $('.dpartyreport').prop('checked',false);
  //     $('.dempreport').prop('checked',false);
  //   }
  //   if($(this).hasClass('collections') && !($(this).is(":checked"))){
  //     $('.analytics').prop('checked',false);
  //     $('.accounting').prop('checked',false);
  //     $('.dpartyreport').prop('checked',false);
  //     $('.dempreport').prop('checked',false);
  //     $('.ageing').prop('checked',false);
  //   }
  //   if($(this).hasClass('product') && !($(this).is(":checked"))){
  //     $('.analytics').prop('checked',false);
  //     $('.orders').prop('checked',false);
  //     $('.accounting').prop('checked',false);
  //     $('.zero_orders').prop('checked',false);
  //     $('.ageing').prop('checked',false);
  //     $('.dso').prop('checked',false);
  //     $('.dsobyunit').prop('checked',false);
  //     $('.ordersreport').prop('checked',false);
  //     $('.psoreport').prop('checked',false);
  //     $('.spwise').prop('checked',false);
  //     $('.dpartyreport').prop('checked',false);
  //     $('.dempreport').prop('checked',false);
  //     $('.schemes').prop('checked',false);
  //   }
  //   if($(this).hasClass('accounting') && !($(this).is(":checked"))){
  //     $('.ageing').prop('checked',false);
  //   }
  //   if($(this).hasClass('beat') && !($(this).is(":checked"))){
  //     $('.analytics').prop('checked',false);
  //   }
  //   if($(this).hasClass('leaves') && !($(this).is(":checked"))){
  //     $('.analytics').prop('checked',false);
  //   }
  //   if($(this).hasClass('analytics') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //     $('.collections').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.beat').prop('checked',true);
  //     $('.leaves').prop('checked',true);
  //   }
  //   if($(this).hasClass('accounting') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //     $('.collections').prop('checked',true);
  //   }
  //   if($(this).hasClass('collections') && ($(this).is(":checked"))){
  //     $('.product').prop('checked',true);
  //     $('.party').prop('checked',true);
  //   }
  //   if($(this).hasClass('orders') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //   }
  //   if($(this).hasClass('notes') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //   }
  //   if($(this).hasClass('beat') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //   }
  //   if($(this).hasClass('returns') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //   }
  //   if($(this).hasClass('stock_report') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //   }
  //   if($(this).hasClass('visit_module') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //   }
  //   if(($(this).hasClass('party_files') || $(this).hasClass('party_images')) && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //   }
  //   if($(this).hasClass('dso') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //   }
  //   if($(this).hasClass('dsobyunit') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //   }
  //   if($(this).hasClass('ordersreport') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //   }
  //   if($(this).hasClass('psoreport') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //   }
  //   if($(this).hasClass('spwise') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //   }
  //   if($(this).hasClass('dpartyreport') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.collections').prop('checked',true);
  //   }
  //   if($(this).hasClass('dempreport') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.collections').prop('checked',true);
  //   }
  //   if($(this).hasClass('pdcs') && ($(this).is(":checked"))){
  //     $('.collections').prop('checked',true);
  //     $('.party').prop('checked',true);
  //   }
  //   if($(this).hasClass('gpsreports') && ($(this).is(":checked"))){
  //     $('.livetracking').prop('checked',true);
  //   }
  //   if($(this).hasClass('product') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //   }
  //   if($(this).hasClass('zero_orders') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //     $('.product').prop('checked',true);
  //   }
  //   if($(this).hasClass('ageing') && ($(this).is(":checked"))){
  //     $('.party').prop('checked',true);
  //     $('.orders').prop('checked',true);
  //     $('.collections').prop('checked',true);
  //     $('.product').prop('checked',true);
  //     $('.accounting').prop('checked',true);
  //   }

  //    if($(this).hasClass('targets') && ($(this).is(":checked"))){
  //       $('.party').prop('checked',true);
  //     }
  //     if($(this).hasClass('targets_rep') && ($(this).is(":checked"))){
  //       $('.targets').prop('checked',true);
  //       $('.party').prop('checked',true);
  //     }


  //   var counter = 0;
  //   $.each($('.switches'),function(k,v){
  //     if($(this).is(":checked")){
  //       counter++;
  //     }
  //   });
  //   if(counter==totalModule){
  //     $(".toggle-all-switches").prop('checked',true);
  //   }else{
  //     $(".toggle-all-switches").prop('checked',false);
  //   }
  // });

  $('#choosePlan').on('change',function(e){
    @if(isset($customplan))
    var custom_plan = "{{$customplan->id}}";
    @else
    var custom_plan = "";
    @endif
    if($(this).val()!="custom" && $(this).val()!=custom_plan ){
      $('.moduleupdatekey').addClass('hide');
      $('#changePlan').removeClass('hide');
      $('.switches').attr('disabled',true);
      $('.toggle-all-switches').attr('disabled',true);
      var url = $(this).attr('action');
      var plan_id = $(this).val();
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: "POST",
        data: {
          'plan_id':plan_id,
        },
        beforeSend:function(){
            // $('.moduleupdatekey').attr('disabled',true);
        },
        success: function (data) {
            $.each(data.data,function(k,v){
              if(v.enabled==1){
                $('.'+v.field).prop('checked',true);
              }else{
                $('.'+v.field).prop('checked',false);
              }
            });
        },
        error:function(){
            console.log('Oops! Something went wrong...');
        }
      });
    }else{
      if($(this).val()==custom_plan){
        var url = $(this).attr('action');
        var plan_id = $(this).val();
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: "POST",
          data: {
            'plan_id':plan_id,
          },
          beforeSend:function(){
              // $('.moduleupdatekey').attr('disabled',true);
          },
          success: function (data) {
              $.each(data.data,function(k,v){
                if(v.enabled==1){
                  $('.'+v.field).prop('checked',true);
                }else{
                  $('.'+v.field).prop('checked',false);
                }
              });
          },
          error:function(){
              console.log('Oops! Something went wrong...');
          }
        });
      }
      $('.moduleupdatekey').removeClass('hide');
      $('#changePlan').addClass('hide');
      $('.switches').attr('disabled',false);
      $('.toggle-all-switches').attr('disabled',false);      
    }
  });

  $('#choosePlan').change();

  $('#changePlan').on('click',function(e){
    var url = $(this).attr('action');
    var plan_id = $('#choosePlan').val();
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: url,
      type: "POST",
      data: {
        'plan_id':plan_id,
      },
      beforeSend:function(){
          // showLoader();
          $('#changePlan').attr('disabled',true);
      },
      success: function (data) {
        $('#changePlan').attr('disabled',false);
        alert(data.message);
      },
      error:function(){
          $('#changePlan').attr('disabled',false);
          console.log('Oops! Something went wrong...');
      },
      complete: function(){
        // hideLoader();
      }
    });
  });

  //Old Codes Not to change
  $('.timepicker').timepicker({
    showInputs: false,
    showMeridian: false,
  });
  $('.upload_types').multiselect({
    placeholder: "Select upload types for collaterals",
    selectAll: true,
  });
  $('.party_file_upload_types').multiselect({
    placeholder: "Select upload types for files",
    selectAll: true,
  });
  $('.party_image_upload_types').multiselect({
    placeholder: "Select upload types for images",
    selectAll: true,
  });
  $(function () {
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
  });
  $('select[name="country"]').on('change', function () {
    var countryId = $(this).val();
    $('#state').append($('<option selected="selected"></option>').html('Loading...'));
//alert(countryId);
if (countryId) {
  $.ajax({
    url: '/get-state-list?country_id=' + countryId,
    type: "GET",
    dataType: "json",
    success: function (data) {
//alert('hi');
$("#state").empty();
$('#city').empty();
$("#city").append('<option value>Select a City</option>');
$("#state").append('<option value>Select a State</option>');
$.each(data, function (key, value) {
  $("#state").append('<option value="' + key + '">' + value + '</option>');
});
}
});
} else {
  $('#state').empty();
  $('#city').empty();
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
          $("#city").append('<option value>Select a City</option>');
          $.each(data, function (key, value) {
            $("#city").append('<option value="' + key + '">' + value + '</option>');
          });
        }
      });
    } else {
      $('#city').empty();
    }
  });
  $(document).ready(function () {
    var i = 1;
    $('#add').click(function () {
      i++;
      $('#dynamic_field').append('<tr id="row' + i + '"><td><input type="text" name="tax_name['+i+']" id="tax_name' + i + '" class="form-control"></td><td><input type="text" name="tax_percent['+i+']" id="tax_percent' + i + '" class="form-control"></td><td><input type="checkbox" name="defaultTax['+i+']" class="defaultTax" id="defaultTax'+i+'" data-id='+i+'/></td><td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>');
      $("#tax_percent" + i).keydown(function (e) {
// Allow: backspace, delete, tab, escape, enter and .
if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
// Allow: Ctrl+A, Command+A
(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
// Allow: home, end, left, right, down, up
(e.keyCode >= 35 && e.keyCode <= 40)) {
// let it happen, don't do anything
return;
}
// Ensure that it is a number and stop the keypress
if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57 && e.keyCode != 190 && e.keyCode != 110)) && (e.keyCode < 96 || e.keyCode > 105)) {
  e.preventDefault();
}
});
    });
    $(document).on('click', '.btn_remove', function () {
      var button_id = $(this).attr("id");
      $('#row' + button_id + '').remove();
    });
  });
  $(document).on('change', '.logofile :file', function () {
    var input = $(this),
    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [label]);
  });
  $('.btn-file :file').on('fileselect', function (event, label) {
    var input = $(this).parents('.input-group').find(':text'),
    log = label;
    if (input.length) {
      input.val(log);
    } else {
      if (log) alert(log);
    }
  });
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $('#img-upload').attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#imgInp").change(function () {
    readURL(this);
  });
  $(document).on('change', '.smalllogofile :file', function () {
    var input = $(this),
    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [label]);
  });
  $('.smalllogofile :file').on('fileselect', function (event, label) {
    var input = $(this).parents('.input-group').find(':text'),
    log = label;
    if (input.length) {
      input.val(log);
    } else {
      if (log) alert(log);
    }
  });
  function readURL1(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $('#img-upload1').attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#imgInp1").change(function () {
    readURL1(this);
  });
  $(document).on('change', '#country', function () {
    var phonecode = $("option:selected", this).attr("phonecode");
    $("#phonecode").val(phonecode);
  });
  $(document).on('change', '.favicon :file', function () {
    var input = $(this),
    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [label]);
  });
  $('.favicon :file').on('fileselect', function (event, label) {
    var input = $(this).parents('.input-group').find(':text'),
    log = label;
    if (input.length) {
      input.val(log);
    } else {
      if (log) alert(log);
    }
  });
  function readURL2(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $('#img-upload2').attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#imgInp2").change(function () {
    readURL2(this);
  });
  function removeTaxAlert(tax_id){
    $('#deleteTax').modal('show');
    $('#deltax_id').val(tax_id);
  }
  $('#delTax').click(function(){
    removeTax($('#deltax_id').val());
  });
  function removeTax(tax_id) {
    var csrf_token = "{{ csrf_token() }}";
    var tax_url = "{{URL::to('company/setting/removeTax')}}";
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: "POST",
      url: tax_url,
      data: {"tax_id": tax_id, "_token": csrf_token},
      success: function (data) {
        // $("#showTaxes").load(" #showTaxes");
        if(data.code===200){
         $("#taxRow"+tax_id).remove(); 
        }
        alert(data.message);
        $('#deleteTax').modal('hide');
      }
    });
  }
  $('.update-tax-btn').click(function() {
    let currentEl = $(this);
    let taxId = currentEl.data("id");
    let tax_name = currentEl.data("name");
    let tax_percent = currentEl.data("percent");
    let modal = $('#updateTax'); 
    modal.modal('show');
    modal.find('#ed_tax_name').val(tax_name);
    modal.find('#ed_tax_percent').val(tax_percent);
    modal.find('#updateId').val(taxId);
  });
  $('#submit-update-btn').click(function(e){
    e.preventDefault();
    let modal = $('#updateTax');
    let taxId = modal.find('#updateId').val();
    let tax_name = modal.find('#ed_tax_name').val();
    let tax_percent = modal.find('#ed_tax_percent').val();
    let url = modal.find('.update-modal')[0].action;
    $.ajax({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{domain_route('app.company.updateTax', [$clientSettings->id])}}",
      type: "POST",
      "data":{"taxId": taxId, "tax_name": tax_name, "tax_percent": tax_percent},
      beforeSend: function(){
        $('.btn').prop('disabled', true);
      },
      success: function (data) {
        alert(data.message);
        $('.btn').prop('disabled', false);
        modal.modal('hide');
        location.reload();
      },
      error: function(xhr, responseJSON){
        $('.btn').prop('disabled', false);
        // modal.modal('hide');
      }
    });
  });
  $(document).on('change', '#default_currency', function () {
    var symbol = $('option:selected', this).attr('symbol');
    $('#currency_symbol').val(symbol);
  });
// Tree view
$.fn.extend({
  treed: function (o) {
    var openedClass = 'glyphicon-minus-sign';
    var closedClass = 'glyphicon-plus-sign';
    if (typeof o != 'undefined') {
      if (typeof o.openedClass != 'undefined') {
        openedClass = o.openedClass;
      }
      if (typeof o.closedClass != 'undefined') {
        closedClass = o.closedClass;
      }
    }
    ;
    /* initialize each of the top levels */
    var tree = $(this);
    tree.addClass("tree");
    tree.find('li').has("ul").each(function () {
      var branch = $(this);
      branch.prepend("");
      branch.addClass('branch');
      branch.on('click', function (e) {
        if (this == e.target) {
          var icon = $(this).children('i:first');
          icon.toggleClass(openedClass + " " + closedClass);
          $(this).children().children().toggle();
        }
      })
      branch.children().children().toggle();
    });
    /* fire event from the dynamically added icon */
    tree.find('.branch .indicator').each(function () {
      $(this).on('click', function () {
        $(this).closest('li').click();
      });
    });
    /* fire event to open branch if the li contains an anchor instead of text */
    tree.find('.branch>a').each(function () {
      $(this).on('click', function (e) {
        $(this).closest('li').click();
        e.preventDefault();
      });
    });
    /* fire event to open branch if the li contains a button instead of text */
    tree.find('.branch>button').each(function () {
      $(this).on('click', function (e) {
        $(this).closest('li').click();
        e.preventDefault();
      });
    });
  }
});
/* Initialization of treeviews */
$('#tree1').treed();
$('#tree2').treed();
$('#tree2').on('click','span', function(){
  $('#modalDeleteMarketArea').modal('show');
  $('#delMarketArea').attr('action',$(this).attr('destroy-url'));
});
// Market Area Section
$('#tree2').on('click','a', function(){
  var superior_id =  $(this).attr('superior-id');
  var area_id = $(this).attr('data-id');
  $('#modalEditMarketArea').modal('show');
  $('#editMarketArea').attr('action',$(this).attr('edit-url'));
  $('#market_area_name').val($(this).attr('data-name'));
  $('#area_parent option').removeAttr('selected');
  $('#area_parent').find('option').each(function(){
    if($(this).val() == superior_id){
      $(this).attr('selected','selected');
    }
    if($(this).val()==area_id){
      $(this).remove();
    }
  });
});
$('#editMarketArea').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  var company_id = $('#area_edit_company_id').val();
  var area_name = $('#market_area_name').val();
  var area_parent = $('#area_parent').val();
  $.ajax({
    url: url,
    type: "GET",
    data:
    {
      'company_id':company_id,
      'area_name':area_name,
      'area_parent':area_parent,
    },
    success: function (data) {
      $('#modalEditMarketArea').modal('hide');
      $('#tree2').html(data.view);
      $('#tree2').treed();
    }
  });
});
$('#delMarketArea').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  var company_id = $('#del_company_id').val();
  $.ajax({
    url: url,
    type: "GET",
    data:
    {
      'company_id':company_id,
    },
    success: function (data) {
      $('#modalDeleteMarketArea').modal('hide');
      $('#tree2').html(data.view);
      $('#tree2').treed();
    }
  });
});
//Party Type Section
$('#tree1').on('click','span', function(){
  $('#modalDeletePartyType').modal('show');
  $('#delPartyType').attr('action',$(this).attr('destroy-url'));
});
$('#tree1').on('click','a', function(){
  var superior_id =  $(this).attr('superior-id');
  var party_id = $(this).attr('data-id');
  $('#modalEditPartyType').modal('show');
  $('#editPartyType').attr('action',$(this).attr('edit-url'));
  $('#party_type_name').val($(this).attr('data-name'));
  $('#party_type_short_name').val($(this).attr('data-short-name'));
  $('#party_parent option').removeAttr('selected');
  if($(this).attr('data-ticked')==0){
    $('#partyType_display_status').attr('checked',false);
  }else{
    $('#partyType_display_status').attr('checked',true);
  }
  var url = "{{route('app.company.setting.getPartyTypeList')}}";
  var company_id = $('#party_edit_company_id').val();
  var myId = $(this).attr('data-id');
  $.ajax({
    url: url,
    type: "GET",
    data:
    {
      'company_id':company_id,
      'myId':myId,
    },
    success: function (data) {
      $('#modalEditPartyName').modal('hide');
      $('#party_parent').empty();
      $('<option></option>').text('Select Party Type').appendTo('#party_parent');
      $.each(data['partytypes'],function(i,v){
        if(v.id == superior_id){
          $('<option selected></option>').val(v.id).text(v.name).appendTo('#party_parent');
        }else{
          $('<option></option>').val(v.id).text(v.name).appendTo('#party_parent');
        }
      });
    }
  });
// $('#party_parent').find('option').each(function(){
//   if($(this).val() == superior_id){
//     $(this).attr('selected','selected');
//   }
//   if($(this).val()==party_id){
//     $(this).remove();
//   }
// });
});
$('#tree1').on('click','p', function(){
  var superior_id =  $(this).attr('superior-id');
  var party_id = $(this).attr('data-id');
  $('#modalEditPartyName').modal('show');
  $('#editPartyName').attr('action',$(this).attr('edit-url'));
  $('#party_type_nameonly').val($(this).attr('data-name'));
  $('#party_type_short_nameonly').val($(this).attr('data-short-name'));
  if($(this).attr('data-ticked')==0){
    $('#tickedSalemanAllowed').attr('checked',false);
  }else{
    $('#tickedSalemanAllowed').attr('checked',true);
  }
});
$('#editPartyName').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  var company_id = $('#party_edit_company_id').val();
  var party_type = $('#party_type_nameonly').val();
  var short_name = $('#party_type_short_nameonly').val();
  if($('#tickedSalemanAllowed').prop('checked')==true){
    display_status=1;
  }else{
    display_status=0;
  }
  $.ajax({
    url: url,
    type: "GET",
    data:
    {
      'company_id':company_id,
      'party_type':party_type,
      'party_type_short_name':short_name,
      'display_status':display_status,
    },
    success: function (data) {
      $('#modalEditPartyName').modal('hide');
      $('#tree1').html(data['tree']);
      $('#tree1').treed();
      $('#select_party_types').empty();
      $.each(data['partytypes'],function(i,v){
        $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
      });
      alert("Party type has been updated successfully.");
    }
  });
});

$('#editPartyType').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  var company_id = $('#party_edit_company_id').val();
  var party_type = $('#party_type_name').val();
  var party_type_short_name = $('#party_type_short_name').val();
  var party_parent = $('#party_parent').val();
  var display_status = $('#partyType_display_status').val();
  if($('#partyType_display_status').prop('checked')==true){
    display_status=1;
  }else{
    display_status=0;
  }
  $.ajax({
    url: url,
    type: "GET",
    data:
    {
      'company_id':company_id,
      'party_type':party_type,
      'party_parent':party_parent,
      'party_type_short_name':party_type_short_name,
      'display_status':display_status,
    },
    success: function (data) {
      $('#modalEditPartyType').modal('hide');
      $('#tree1').html(data['tree']);
      $('#tree1').treed();
      $('#select_party_types').empty();
      $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
      $.each(data['partytypes'],function(i,v){
        $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
      });
      alert("Party type has been updated successfully.");
    }
  });
});
$('#delPartyType').on('submit',function(event){
  event.preventDefault();
  var url = $(this).attr('action');
  var company_id = $('#del_company_id').val();
  $.ajax({
    url: url,
    type: "GET",
    data:
    {
      'company_id':company_id,
    },
    success: function (data) {
      $('#modalDeletePartyType').modal('hide');
      $('#tree1').html(data['tree']);
      $('#tree1').treed();
      $('#select_party_types').empty();
      $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
      $.each(data['partytypes'],function(i,v){
        $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
      });
      alert("Party Type Deleted Successfully");
    }
  });
});
$('#frmAddParty').on('submit',function(){
  $('#btnAddParty').attr('disabled',true);
});
$('#frmAddArea').on('submit',function(){
  $('#btnAddArea').attr('disabled',true);
});

$('.edit_defaultTax').change(function() {
  let currentEl = $(this);
  let taxId = currentEl.data("id");
  let allEl = $('.edit_defaultTax');
  let flagVal = 0;
  let checkedCounter = 0;
  if(this.checked){
    flagVal = 1;
  }else{
    $('.edit_defaultTax').each(function(){
      if(this.checked)
        checkedCounter += 1;
    });
    if(checkedCounter<1){
      alert("A tax type must at least be selected.");
      currentEl.prop("checked", true);
      return false;
    }
  }
  $.ajax({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: "{{domain_route('app.company.updateDefaultFlag', [$clientSettings->id])}}",
    type: "POST",
    "data":{flagVal:flagVal,taxId: taxId},
    beforeSend: function(){
      $('.btn').prop('disabled', true);
    },
    success: function (data) {
      alert(data.message);
      $('.btn').prop('disabled', false);
    },
    error: function(xhr, responseJSON){
      $('.btn').prop('disabled', false);
    }
  });
});
// $('#order_with_amt').on('ifChanged', function(event){
//   debugger;
// });
$('input[name="order_with_amt"]').on('ifClicked', function (event) {
  // alert("You clicked " + this.value);
  if(this.value==1){
    $('input[id="product_level_discount_1"]').prop('checked', false).iCheck('update');
    $('input[id="product_level_discount_0"]').prop('checked', true).iCheck('update');
    $('input[id="product_level_tax_1"]').prop('checked', false).iCheck('update');
    $('input[id="product_level_tax_0"]').prop('checked', true).iCheck('update');
    $('input[id="party_wise_rate_setup_1"]').prop('checked', false).iCheck('update');
    $('input[id="party_wise_rate_setup_0"]').prop('checked', true).iCheck('update');
  }
});
$('input[name="product_level_tax"]').on('ifClicked', function (event) {
  // alert("You clicked " + this.value);
  if(this.value==1){
    $('input[id="order_with_amt_1"]').prop('checked', false).iCheck('update');
    $('input[id="order_with_amt_0"]').prop('checked', true).iCheck('update');
  }
});
$('input[name="product_level_discount"]').on('ifClicked', function (event) {
  // alert("You clicked " + this.value);
  if(this.value==1){
    $('input[id="order_with_amt_1"]').prop('checked', false).iCheck('update');
    $('input[id="order_with_amt_0"]').prop('checked', true).iCheck('update');
  }
});

$('input[name="category_wise_rate_setup"]').on('ifClicked', function (event) {
  if(this.value==1){
    $('input[id="party_wise_rate_setup_1"]').prop('checked', false).iCheck('update');
    $('input[id="party_wise_rate_setup_0"]').prop('checked', true).iCheck('update');
  }
});
$('input[name="party_wise_rate_setup"]').on('ifClicked', function (event) {
  if(this.value==1){
    $('input[id="category_wise_rate_setup_1"]').prop('checked', false).iCheck('update');
    $('input[id="category_wise_rate_setup_0"]').prop('checked', true).iCheck('update');
  }
});
</script>
@endsection