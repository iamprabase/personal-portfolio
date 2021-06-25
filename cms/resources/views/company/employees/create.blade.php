@extends('layouts.company')

@section('title', 'Create Employee')

@section('stylesheets')

@if(config('settings.ncal')==1)
<link rel="stylesheet"
      href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet"
      href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif

  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">

  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">

  <link rel="stylesheet" href="{{asset('assets/dist/css/bootstrap-multiselect.css') }}"/>
  <link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}"/>
<style>
    .multiselect-item.multiselect-group label input{
      height:auto;
    }
    .panel-heading{
      color: #fff!important;
      background-color: #0b7676!important;
    }
    .list_type{
      height: fit-content;
    }
</style>

@endsection



@section('content')

  <section class="content">

    <div class="row">

      <div class="col-xs-12">

        @if (\Session::has('phone-number-error'))

          <div class="alert alert-warning">

            <p>{{ \Session::get('phone-number-error') }}</p>

          </div><br/>

        @elseif (\Session::has('maxreached'))

          <div class="alert alert-danger">

            <p>{{ \Session::get('maxreached') }}</p>

          </div><br/>

        @endif

      </div>

    </div>

    <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">

        <div class="box-header with-border">

          <h3 class="box-title">Create Employee</h3>

          <div class="box-tools pull-right">

            <div class="col-xs-7 page-action text-right">

              <a href="{{ domain_route('company.admin.employee') }}" class="btn btn-default btn-sm"> <i

                    class="fa fa-arrow-left"></i> Back</a>

            </div>

          </div>

        </div>

        <!-- /.box-header -->

        <div class="box-body">

        {!! Form::open(array('url' => url(domain_route("company.admin.employee.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true,'id'=>'createEmployee', 'onSubmit' => "checkForm(this);")) !!}

        @include('company.employees._form')

        <!-- Submit Form Button -->

          {!! Form::submit('Create', ['class' => 'btn btn-primary pull-right', 'id' => 'create_new_entry']) !!}

          {!! Form::close() !!}


        </div>

      </div>


  </section>


@endsection



@section('scripts')


<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
<script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
@else
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif

  {{-- <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script> --}}

  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>

  <!-- Select2 -->

  <!-- <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script> -->
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

  <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>




  <script>

      $('#employeeId').multiselect({

          columns: 1,

          placeholder: 'Select Parties',

          search: true,

          selectAll: true

      });

      $('#employeeId2').multiselect({
          columns: 1,
          placeholder: 'Select Parties',
          search: true,
          selectAll: true,
          selectGroup : true,

      });

      $(document).on('change', '#status', function () {

          var status = $(this).val();

          if (status == 'Archived')

              $('#archive-warning').show();

          else

              $('#archive-warning').hide();

      });

      
      $(function () {

          $('.select2').select2();


          @if(config('settings.ncal')==0)

          $("#b_date").datepicker({
              format: "yyyy-mm-dd",
              endDate: new Date(),
              autoclose: true,
          }).datepicker("setDate", "0"); 

          $("#doj").datepicker({
              format: "yyyy-mm-dd",
              endDate: new Date(),
              autoclose: true,
          }).datepicker("setDate", "0"); 

          $("#lwd").datepicker({
              format: "yyyy-mm-dd",
              endDate: new Date(),
              autoclose: true,
          }).datepicker("setDate", "0"); 

          @else

          var today = moment().format('YYYY-MM-DD');
          var ntoday = AD2BS(today);
          var ntoday= ntoday.split('-');
          ntoday = ntoday[1]+'/'+ntoday[2]+'/'+ntoday[0];
          $('#b_date').nepaliDatePicker({
            npdMonth: true,
            npdYear: true,
            npdDate: false,
            disableAfter: ntoday,
            ndpEnglishInput: 'englishDate',
          });

          $('#doj').nepaliDatePicker({
            npdMonth: true,
            npdYear: true,
            npdDate: false,
            disableAfter: ntoday,
            ndpEnglishInput: 'englishDoj',
          });

          $('#lwd').nepaliDatePicker({
            npdMonth: true,
            npdYear: true,
            npdDate: false,
            disableAfter: ntoday,
            ndpEnglishInput: 'englishLwd',
          });
          
          @endif
         

          $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({

              checkboxClass: 'icheckbox_minimal-blue',

              radioClass: 'iradio_minimal-blue'

          });



          //CKEDITOR.replace('companydesc');

      });



      $(document).on('change', '.btn-file :file', function () {

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
      @if(Auth::user()->isCompanyEmployee())
      $('select[name="designation"]').on('change', function () {
          if($(this).find('option:selected').html()=="Admin"){
            $('#superior').prop('disabled',true);
          }else{
            $('#superior').prop('disabled',false);
          }
          var designation = $(this).val();
          var url = '{{domain_route('company.admin.employee.getEmployeeSuperiors')}}';
          if (designation) {
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data:{
                    'csrf_token':'{{csrf_token()}}',
                    designation:designation,
                  },
                  success: function (data) {
                      $("#superior").empty();
                      $.each(data,function(k,v){
                        $('<optgroup />').prop('label', k).appendTo('#superior');
                        $.each(v, function (key, value) {
                            $('<option></option>').val(value.id).text(value.emp_name).appendTo('#superior');
                        });
                      });
                      $('select[name="superior"]').trigger('change');
                  }
              });
          } else {
              $('#superior').empty();
          }
      });
      @else
      $('select[name="designation"]').on('change', function () {
          
          if($(this).find('option:selected').html()=="Admin"){
            $('#spanSuperior').addClass('hide');
            $('#superior').prop('disabled',true);
          }else{
            $('#spanSuperior').removeClass('hide');
            $('#superior').prop('disabled',false);
          }
          if($(this).find('option:selected').html()=="Admin"){
            $('#superior').prop('disabled',true);
            var admin = true;
          }else{
            $('#superior').prop('disabled',false);
            var admin = false;
          }
          var url =  '{{domain_route('company.admin.employee.getEmployeeSuperiors')}}';
          var designation = $(this).val();
          if (designation) {
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data:{
                    designation:designation,
                  },
                  dataType: "json",
                  cache: false,
                  success: function (data) {
                      $("#superior").empty();
                      if(admin==false){
                        $.each(data,function(i,item){
                          $('<optgroup />').prop('label', i).appendTo('#superior');
                          $.each(item,function(key,value){
                              $('<option></option>').val(value.id).text(value.emp_name).appendTo('#superior').appendTo('#superior');
                          });
                        });
                      }
                  }
              });
          } else {
              $('#superior').empty();
          }
      });
      @endif

      $('select[name="designation"]').change();
      
      $('#employeeId-enableClickableOptGroups').multiselect({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            enableFullValueFiltering: true,
            enableClickableOptGroups: true,
            includeSelectAllOption: true,	
            enableCollapsibleOptGroups : true,
            selectAllNumber: false,
            nonSelectedText:"Select Parties",
            disableIfEmpty:true,
      });
      @if(getClientSetting()->beat==1)  
      $("#employeeId-enableClickableOptGroups").change(function(){
        let selected = [];
        $(this).find("option:selected").each(function(){
          let optgroup_val = $(this).parent().attr("value");
          if(!selected.includes(optgroup_val)){
            selected.push(optgroup_val);
          }
        });
        $('#beatId-optGroups').val(selected);
      });
      @endif

      $('select[name="superior"]').on('change', function () {
        var superior = $(this).val();
        if(superior==null){
          superior = "{{Auth::user()->EmployeeId()}}";
        }
        var listType = $("input[name=list_type]:checked").val();
        getSuperior(superior, listType);
      });

      $('input[name=list_type]').on("click", function() {
        var superior = $("#superior").val();
        if(superior==null){
          superior = "{{Auth::user()->EmployeeId()}}";
        }
        var listType = $("input[name='list_type']:checked").val();
        getSuperior(superior, listType);
      });
      
      function getSuperior(superior, listType){
        if(superior!=null){
          $.ajax({
            url: "{{domain_route('company.admin.employee.getsuperiorparties')}}",
            type: "GET",
            data:{
              'superior': superior,
              'listType': listType
            },
            cache: false,
            success: function (data) {
              if(data!=""){
                var beatParties = data; 
                $('#employeeId-enableClickableOptGroups').multiselect('destroy');
                $('#employeeId-enableClickableOptGroups').empty();
                if(!$('.nonePartyAssigned').hasClass('hidden'))
                  $('.nonePartyAssigned').addClass('hidden');

                $.each(beatParties, function (i, item) {
                  var optgrouping = "<optgroup label='"+item['name']+"' value='"+item['id']+"'></optgroup>";
                  var options = [];
                  var clients = item['clients'];
                  $.each(clients,function(id, name){
                    options.push("<option value='"+ id +"'>"+name+"</option>");
                  });
                  var grouping = $(optgrouping).html(options.join(''));
                  $('#employeeId-enableClickableOptGroups').append(grouping);
                });

                $('#employeeId-enableClickableOptGroups').multiselect({
                  enableFiltering: true,
                  enableCaseInsensitiveFiltering: true,
                  enableFullValueFiltering: true,
                  enableClickableOptGroups: true,
                  includeSelectAllOption: true,
                  enableCollapsibleOptGroups : true,
                  selectAllNumber: false,
                  nonSelectedText:"Select Parties",
                  disableIfEmpty:true,
                });

              }else{
                if($('.nonePartyAssigned').hasClass('hidden'))
                  $('.nonePartyAssigned').removeClass('hidden');
                $('#employeeId-enableClickableOptGroups').multiselect('destroy');
                $('#employeeId-enableClickableOptGroups').empty();
                $('#employeeId-enableClickableOptGroups').multiselect({
                  enableFiltering: true,
                  enableCaseInsensitiveFiltering: true,
                  enableFullValueFiltering: true,
                  enableClickableOptGroups: true,
                  includeSelectAllOption: true,
                  enableCollapsibleOptGroups : true,
                  selectAllNumber: false,
                  nonSelectedText:"Select Parties",
                  disableIfEmpty:true,
                });
              }
            }
          });
        }
      }


      function bs_input_file() {
        $(".input-file").before(
          function() {
            if ( ! $(this).prev().hasClass('input-ghost') ) {
              var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0'>");
              element.attr("name",$(this).attr("name"));
              element.change(function(){
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

      function ajaxValidate(){
        var url = '{{domain_route('company.admin.employee.ajaxValidate')}}';
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: new FormData($('#createEmployee')[0]),
              contentType: false,
              cache: false,
              processData: false,
              beforeSend:function(){
                $('#create_new_entry').prop('disabled',true);
              },
              success: function (data) {
                  if(data['result']==false){
                    if(data.error.name){
                      $('#alertEmpName').addClass('has-error');
                    }else{
                      $('#alertEmpName').removeClass('has-error');
                    }
                    if(data.error.phone){
                      $('#alertPhoneNo').addClass('has-error');
                    }else{
                      $('#alertPhoneNo').removeClass('has-error');
                    }
                    if(data.error.password){
                      $('#alertPassword').addClass('has-error');
                    }else{
                      $('#alertPassword').removeClass('has-error');
                    }
                    if(data.error.c_password){
                      $('#alertConfirmPassword').addClass('has-error');
                    }else{
                      $('#alertConfirmPassword').removeClass('has-error');
                    }
                    if(data.error.employeegroup){
                      $('#alertEmpGroup').addClass('has-error');
                    }else{
                      $('#alertEmpGroup').removeClass('has-error');
                    }
                    if(data.error.email){
                      $('#alertEmail').addClass('has-error');
                    }else{
                      $('#alertEmail').removeClass('has-error');
                    }
                    if(data.error.employee_code){
                      $('#alertEmpCode').addClass('has-error');
                    }else{
                      $('#alertEmpCode').removeClass('has-error');
                    }
                    if(data.error.designation){
                      $('#alertDesignation').addClass('has-error');
                    }else{
                      $('#alertDesignation').removeClass('has-error');
                    }
                    $('#create_new_entry').prop('disabled',true);
                  }else{
                    $('#alertEmpName').removeClass('has-error');
                    $('#alertPhoneNo').removeClass('has-error');
                    $('#alertPassword').removeClass('has-error');
                    $('#alertConfirmPassword').removeClass('has-error');
                    $('#alertEmpGroup').removeClass('has-error');
                    $('#alertEmail').removeClass('has-error');
                    $('#alertEmpCode').removeClass('has-error');
                    $('#alertDesignation').removeClass('has-error');
                    $('#create_new_entry').prop('disabled',false);
                  }
              },
              error:function(){
                console.log('ajax failed');
              }
          });
      }
      // ajaxValidate();

      var myVar;

      function myFunction() {
        myVar = setTimeout(function(){ console.log("ajax attempted"); ajaxValidate(); }, 3000);
      }

      function myStopFunction() {
        clearTimeout(myVar);
      }

      $('#empName,#empPhone,#empEmail,#empPassword,#empConfirmPassword,#empCode').on('keyup',function(){
        $('#create_new_entry').attr('disabled',true);
        myStopFunction();
        myFunction();
      });

      $('#empGroup,#designation').on('change',function(){
        $('#create_new_entry').attr('disabled',true);
        myStopFunction();
        myFunction();
      });

      $('#create_new_entry').prop('disabled',true);

      function checkForm(form)
      {
        $(form).find('#create_new_entry').attr('disabled', true)
      }
  </script>



@endsection

