@extends('layouts.company')
@section('title', 'Settings')
@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="{{asset('assets/plugins/timepicker/bootstrap-timepicker.css')}}">
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
      padding-top: 50px;
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
    
    .astrik {
      color: red;
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
        <div class="col-xs-3 right-pd">
          <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li role="presentation" class="active"><a href="#updateProfile" role="tab"
                                                                                       id="update-profile-tab"
                                                                                       data-toggle="tab"
                                                                                       aria-controls="updateProfile"
                                                                                       aria-expanded="false">Update
                Profile</a></li>

             @if(!in_array(config('settings.company_id'), array(130,20,14)))
            <li role="presentation" class=""><a href="#updatePassword" role="tab" id="update-password-tab"
                                                data-toggle="tab" aria-controls="updatePassword" aria-expanded="false">Update
                Password</a></li>
                @endif
              
          
          </ul>
        </div>
        
        @include('company.settings._updateform')
      
      </div>
    </div>
  
  
  </section>


@endsection

@section('scripts')
  
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <!-- bootstrap time picker -->
  <script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
  
  <script type="text/javascript">
      $('#image').change(function () {
          $('#preview').empty();
          var num_files = $(this)[0].files;
          $('#preview').append("<img src='" + URL.createObjectURL(event.target.files[0]) + "' alt='Image Displays here' style='height: 150px;width:150px;border-radius:50%'>&emsp;");
      });
      $("#changePassword").on('submit', function (e) {
          e.preventDefault();
          var current = $(this);
          var data = $("#changePassword").serialize();
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              "url": current.attr('action'),
              "type": "POST",
              "data": data,
              beforeSend: function () {
                  current.find('#btnSave').html('Please wait...');
                  current.find('#btnSave').attr('disabled', true);
              },
              success: function (data) {
                  //debugger;
                  alert(data.message);
                  window.location.href = data.url;
              },
              error: function (xhr, status, error) {
                  for (var error in xhr.responseJSON.errors) {
                      alert(xhr.responseJSON.errors[error]);
                  }
              },
              complete: function () {
                  current.find('#btnSave').html('Change Password');
                  current.find('#btnSave').removeAttr('disabled');
              }
          });//ajax
      });

      $("#updateprofile").on('submit', function (e) {
          e.preventDefault();
          // var current = $(this);
          // var data = $("#updateprofile").serialize();
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              "url": $(this).attr('action'),
              "type": 'POST',
              "data": new FormData(this),
              "contentType": false,
              "cache": false,
              "processData": false,
              beforeSend: function () {
                  $('#btnSave').html('Please wait...');
                  $('#btnSave').attr('disabled', true);
              },
              success: function (data) {
                  alert(data.message);
                  window.location.href = data.url;
              },
              error: function (xhr, status, error) {
                  for (var error in xhr.responseJSON.errors) {
                      alert(xhr.responseJSON.errors[error]);
                  }
              },
              complete: function () {
                  $('#btnSave').html('Update Profile');
                  $('#btnSave').removeAttr('disabled');
              }
          });//ajax
      });
  </script>
  
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <!-- bootstrap time picker -->
  <script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
  <script>

      $('.tab-content form').on("submit", function (e) {
          $(".edit_setting").prop('disabled', true);
      });

      $('.timepicker').timepicker({
          showInputs: false,
          showMeridian: false,
      });

      $(function () {
          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
              checkboxClass: 'icheckbox_minimal-blue',
              radioClass: 'iradio_minimal-blue'
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


      function removeTax(tax_id) {
          var csrf_token = "{{ csrf_token() }}";
          var tax_url = "{{URL::to('admin/setting/removeTax')}}";

          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              url: tax_url,
              data: {"tax_id": tax_id},
              success: function (data) {
                  $("#showTaxes").load(" #showTaxes");
              }
          });
      }

      $(document).on('change', '#default_currency', function () {
          var symbol = $('option:selected', this).attr('symbol');
          $('#currency_symbol').val(symbol);
      });

      $('#delete').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget)
          var mid = button.data('mid')
          var url = button.data('url');

          $(".remove-record-model").attr("action", url);
          var modal = $(this)
          modal.find('.modal-body #m_id').val(mid);
      })
  
  </script>



@endsection
