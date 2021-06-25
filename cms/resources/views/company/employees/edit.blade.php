@extends('layouts.company')
@section('title', 'Edit Employee')
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
    .dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover {
    color: #fff;
    text-decoration: none;
    background-color: #337ab7;
    outline: 0;
  }

  .list_type{
    height: fit-content;
  }

</style>

  <style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 22px;
    }

    .select2-container .select2-selection--single {
      height: 40px;
      padding: 12px 5px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
      margin-top: 3px;
    }

    #ms-list-1 button {
      padding: 0px 15px;
    }


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

    #img-upload {
      width: 200px;
    }

    .select2-selection__choice {
      background-color: teal !important;
      border: #0b7676 !important;
      border-radius: 2px !important;
    }

    .select2-selection__choice__remove {
      color: white !important;
    }

    .select-8-hidden-accessible {
      border: 1px solid grey !important;
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
        @endif
      </div>
    </div>
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Employee Information</h3>

        <div class="box-tools pull-right">
          <div class="col-xs-7 page-action text-right">
            <a href="{{ domain_route('company.admin.employee') }}" class="btn btn-default btn-sm"> <i
                  class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">


      {!! Form::model($employee, array('url' => url(domain_route('company.admin.employee.update',[$employee->id])) , 'method' => 'POST', 'files'=> true,'id'=>'updateEmployee', 'onSubmit' => "checkForm(this);")) !!}



      @include('company.employees._form')
      <!-- Submit Form Button -->
        {!! Form::submit('Update', ['class' => 'btn btn-primary pull-right','id'=>'create_new_entry']) !!}
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
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/ckeditor/ckeditor.js') }}"></script>
  <!-- Select2 -->
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{asset('assets/dist/js/bootstrap-multiselect.js') }}"></script>

  <script>
      $('.DT_EMP_FILTER').val(sessionStorage.getItem('DT_EMP_filters'));
      $(document).ready(function () {

          var selected = <?php echo json_encode($handles) ?>;
          $('.select2').select2();
          var superiorVal = @if($employee->superior!=NULL){{ $employee->superior}}@else <?php echo "' '"; ?> @endif;
      });
      $(document).on('change', '#status', function () {
          var status = $(this).val();
          if (status == 'Archived')
              $('#archive-warning').show();
          else
              $('#archive-warning').hide();
      });

      $(function () {
          @if(config('settings.ncal')==0)
          
          $("#b_date").datepicker({
              format: "yyyy-mm-dd",
              endDate: new Date(),
              autoclose: true,
          });    // Here the current date is set
          $("#doj").datepicker({
              format: "yyyy-mm-dd",
              endDate: '+0d',
              autoclose: true,
          }); 

          $("#lwd").datepicker({
              format: "yyyy-mm-dd",
              startDate: $("#doj").val(),
              endDate: '+0d',
              autoclose: true,
          }); 
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
          // CKEDITOR.replace('about');
      });

      @if(config('settings.ncal')==1)
      $('document').ready(function(){
        let birthDate = $('#b_date').val();
        if(birthDate!=""){
          $('#b_date').val(AD2BS(birthDate));
        }
      });
      @endif

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
      
      $('select[name="designation"]').on('change', function () {
          var designation = $(this).val();
          var employee_id = '{{$employee->id}}';
          var empDesignation = '{{$employee->designation}}';
          var url =  '{{domain_route('company.admin.employee.getEmployeeSuperiors')}}';
          if(designation != empDesignation){
            $('#designationchoice').removeClass('hide');
          }else{
            $('#designationchoice').addClass('hide');
          }
          if($(this).find('option:selected').html()=="Admin"){
            $('#superior').prop('disabled',true);
            var admin = true;
          }else{
            $('#superior').prop('disabled',false);
            var admin = false;
          }

          if (designation) {
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  url: url,
                  type: "POST",
                  data:{
                    designation:designation,
                    employee_id:employee_id,
                  },
                  dataType: "json",
                  cache: false,
                  success: function (data) {
                      $("#superior").empty();
                      var superior = '{{$employee->superior}}';
                      @if(isset($childExists)) 
                      $('#superior').prop   ('disabled',true)
                      @endif
                      if(admin==false){
                        $.each(data,function(i,item){
                          $('<optgroup />').prop('label', i).appendTo('#superior');
                          $.each(item,function(key,value){
                            if(superior==value.id){
                              $('<option></option>').val(value.id).text(value.emp_name).appendTo('#superior').appendTo('#superior').prop('selected', true);
                            }else{
                              $('<option></option>').val(value.id).text(value.emp_name).appendTo('#superior').appendTo('#superior');
                            }
                          });
                        });
                      }

                  },
              });
          } else {
              $('#superior').empty();
          }
      });
      $('select[name="designation"]').change();


      $('input[name="promoteOption"]').on('click',function(){
        var checked = $("input[name='promoteOption']:checked").val();
        if(checked=='replace'){
          var superior = $('#superior').val();
          var designation = $('#designation').val();
          alert('Warning! Previous User Data information will be removed and forward to selected user');
          $('#existingUsers').removeClass('hide');
          $('#partyAssignforms').addClass('hide');
          var url =  '{{domain_route('company.admin.employee.getChainDesignationEmployees')}}';
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data:{
                designation:designation,
                superior:superior,
              },
              dataType: "json",
              cache: false,
              success: function (data) {
                  $("#eusers").empty();
                  var superior = '{{$employee->superior}}';
                  $.each(data,function(k,v){
                    $('<option></option>').val(v.id).text(v.name).appendTo('#eusers');
                  });
              },
          });

        }else{
          $('#existingUsers').addClass('hide');
          $('#partyAssignforms').removeClass('hide');
        }
      });

      $('select[name="superior"]').on('change',function(){
        var checked = $("input[name='promoteOption']:checked").val();
        if(checked=='replace'){
          var superior = $('#superior').val();
          var designation = $('#designation').val();
          alert('Warning! Previous User Data information will be removed and forward to selected user');
          $('#existingUsers').removeClass('hide');
          $('#partyAssignforms').addClass('hide');
          var url =  '{{domain_route('company.admin.employee.getChainDesignationEmployees')}}';
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data:{
                designation:designation,
                superior:superior,
              },
              dataType: "json",
              cache: false,
              success: function (data) {
                  $("#eusers").empty();
                  var superior = '{{$employee->superior}}';
                  $.each(data,function(k,v){
                    $('<option></option>').val(v.id).text(v.name).appendTo('#eusers');
                  });
              },
          });

        }else{
          $('#existingUsers').addClass('hide');
          $('#partyAssignforms').removeClass('hide');
        }
      });

      @if(getClientSetting()->beat==1)
        @if(isset($employee->superior))
        $(function(){
          var superior = '{{$employee->superior}}';
        });
        @endif
      @endif
      $('select[name="superior"]').on('change', function () {
        var currentVal = $('#employeeId-enableClickableOptGroups').val();
        var superior = $(this).val();
        if(superior==null){
          superior = "{{Auth::user()->EmployeeId()}}";
        }
        var listType = $("input[name=list_type]:checked").val();
        getSuperiorParty(superior, listType,currentVal);
      });

      $('input[name=list_type]').on("click", function() {
        var currentVal = $('#employeeId-enableClickableOptGroups').val();
        var superior = $("#superior").val();
        if(superior==null){
          superior = "{{Auth::user()->EmployeeId()}}";
        }
        var listType = $("input[name='list_type']:checked").val();
        @if($employee->user_id != Auth::user()->id && $employee->is_admin != 1)
          let disbaledInputs = $('#employeeId-enableClickableOptGroups')[0].selectedOptions;
          if(disbaledInputs.length>0){
            for (let item of disbaledInputs) {
              currentVal.push(item.value.toString());
            }
          }
        @endif
        getSuperiorParty(superior, listType, currentVal);
      });
      function getSuperiorParty(superior, listType, currentVal){
        let juniorParties = "{{$getJuniorParties}}";//JSON.parse("{{$getJuniorParties}}");
        // console.log(juniorParties);
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
                if(!$('.nonePartyAssigned').hasClass('hidden'))
                  $('.nonePartyAssigned').addClass('hidden');
                $('#employeeId-enableClickableOptGroups').multiselect('destroy');
                $('#employeeId-enableClickableOptGroups').empty();

                $.each(beatParties, function (i, item) {
                  var optgrouping = "<optgroup label='"+item['name']+"' value='"+item['id']+"'></optgroup>";
                  var options = [];
                  var clients = item['clients'];
                  $.each(clients,function(id, name){
                    if(currentVal.includes(id)){
                      // console.log(id);
                      if(juniorParties.includes(parseInt(id))){
                          options.push("<option value='"+ id +"' selected disabled>"+name+"</option>");
                        }else{
                          options.push("<option value='"+ id +"' selected>"+name+"</option>");
                        }
                    }else{
                      options.push("<option value='"+ id +"' @if($employee->is_admin==1) selected disabled @endif>"+name+"</option>");
                    }
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
      @if(Auth::user()->id==$employee->user_id)
      $('#employeeId-enableClickableOptGroups').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        enableFullValueFiltering: true,
        enableClickableOptGroups: true,
        includeSelectAllOption: false, 
        enableCollapsibleOptGroups : false,
        selectAllNumber: false,
        nonSelectedText:"Select Parties",
        disableIfEmpty:true,
        });
      @else
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
      @endif
      
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
        var url = '{{domain_route('company.admin.employee.ajaxUpdateValidate')}}';
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: new FormData($('#updateEmployee')[0]),
              contentType: false,
              cache: false,
              processData: false,
              beforeSend:function(){
                $('#create_new_entry').prop('disabled',true);
              },
              success: function (data) {
                  request = null;
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
                console.log('Oops! Something went wrong...');
              }
          });
      }
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

      $(document).ready(function(){
          @if(empty($beats))
            $('.nonePartyAssigned').removeClass('hidden');
          @endif
      });
      ajaxValidate();

  </script>

@endsection
