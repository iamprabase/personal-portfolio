@extends('layouts.company')

@section('title', 'Settings')

@section('stylesheets')

<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}"/>
<link href="{{asset('assets/plugins/settings/css/colorpicker.min.css') }}" rel="stylesheet">

<link rel="stylesheet" href="{{asset('assets/plugins/timepicker/bootstrap-timepicker.css')}}">
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">


<link rel="stylesheet" href="{{asset('assets/plugins/settings/css/partytypes.css') }}">
<link rel="stylesheet" href="{{asset('assets/plugins/settings/css/customfield.css') }}">
<link rel="stylesheet" href="{{asset('assets/dist/css/settings.css') }}">

<link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}" media="print">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{ asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@endif
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<style>
  .headerTab{
    background-color:#0b7676!important;
  }
  .op-4{
    opacity: 0.4;
  }

  .table .btn-warning,.table .fa-edit,#tree1 .fa-edit {
    color: #f0ad4e !important;
    background-color: transparent !important;
    border-color: transparent !important;
  }

  .box-bodys {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
    padding: 10px;
  }
</style>
@endsection

@section('content')
  <section class="content">
    <div class="row">
      @include('company.settingsnew.settingheader')
    </div>
    <div class="row">
        @php $active = "orderstatus" @endphp
      <div id="loader1">
        <img src="{{asset('assets/dist/img/loader2.gif')}}" />
      </div>
      <div class="bs-example bs-example-tabs op-4" data-example-id="togglable-tabs" style="margin-top:20px;">
        <div class="col-xs-3 right-pd">
      		 <ul class="nav nav-tabs" id="myTabs" role="tablist">
            @if(config('settings.orders')==1)
            <li role="presentation" class="{{($active == 'orderstatus')? 'active':''}}"><a href="#order_status-detail" role="tab" id="order_status-detail-tab" data-toggle="tab"aria-controls="order_status-detail" aria-expanded="false">Order Status</a></li>
            @endif

            @if(config('settings.collections')==1)
            <li role="presentation" class="{{($active == 'bank')? 'active':''}}"><a href="#bank-detail" role="tab" id="bank-detail-tab" data-toggle="tab" aria-controls="bank-detail" aria-expanded="false">Banks</a></li>
            @endif

            
            @if(config('settings.party')==1)
            <li role="presentation" class="{{($active == 'beat')? 'active':''}}"><a href="#beats-detail" role="tab" id="beats-tab" data-toggle="tab" aria-controls="beats" aria-expanded="false">Beats</a></li>
            <li role="presentation" class="{{($active == 'businesstype')? 'active':''}}"><a href="#business-types" role="tab" id="business-tab" data-toggle="tab" aria-controls="business" aria-expanded="false">Business Types</a></li>
            @endif

            @if(config('settings.expenses')==1)
            <li role="presentation" class="{{($active == 'expensetype')? 'active':''}}"><a href="#expense-types" role="tab" id="expense-tab" data-toggle="tab" aria-controls="expense" aria-expanded="false">Expense Category</a></li>
            @endif

            @if(config('settings.leaves')==1)
            <li role="presentation" class="{{($active == 'leavetype')? 'active':''}}"><a href="#leave-types" role="tab" id="leave-tab" data-toggle="tab" aria-controls="leave" aria-expanded="false">Leave Types</a></li>
            @endif

            @if(config('settings.visit_module')==1 && config('settings.party')==1)
              <li role="presentation" class="{{($active == 'visit-purpose')? 'active':''}}"><a href="#visit-purpose" role="tab" id="visit-purpose-tab" data-toggle="tab" aria-controls="visit-purpose" aria-expanded="false">Visit Purpose</a></li>
            @endif

            @if(config('settings.party')==1)
            <li role="presentation" class="{{($active == 'partytype')? 'active':''}}"><a href="#party-types" role="tab" id="partytype-tab" data-toggle="tab" aria-controls="party" aria-expanded="false">Party Types</a></li>
            @endif

            <li role="presentation" class="{{($active == 'designation')? 'active':''}}"><a href="#designations-detail" role="tab" id="designations-detail-tab" data-toggle="tab" aria-controls="designations-detail" aria-expanded="false">Designation</a></li>
            
            @if(config('settings.returns')==1)
            <li role="presentation" class="{{($active == 'returnreasons')? 'active':''}}"><a href="#returnreasons-detail" role="tab"
                id="returnreasons-detail-tab" data-toggle="tab" aria-controls="returnreasons-detail"
                aria-expanded="false">Return Reasons</a></li>
            @endif

            @if(config('settings.ncal')==1)
              <li role="presentation" class="{{($active == 'holiday')? 'active':''}}"><a href="#Nholiday-detail" role="tab" id="nholiday-detail-tab" data-toggle="tab" aria-controls="Nholiday-detail" aria-expanded="false">Holidays</a></li>
            @else
              <li role="presentation" class="{{($active == 'holiday')? 'active':''}}"><a href="#holiday-detail" role="tab" id="holiday-detail-tab" data-toggle="tab" aria-controls="holiday-detail" aria-expanded="false">Holidays</a></li>
            @endif
          
          </ul>
    		</div>
    		@include('company.settingsnew._settingtabs')
      </div>
    </div>
  </section>
@endsection

@section('scripts')
  <script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatables-buttons/js/buttons.flash.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
  <script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>

  <script src="{{ asset('assets/bower_components/moment/moment.js') }}"></script>
  <script src="{{asset('assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
  <script src="{{ asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>
  @if(config('settings.ncal')==1)
  <script src="{{ asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
  @endif
  
  <script src="{{asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
  <script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
  <script src="{{asset('assets/plugins/settings/colorpicker.min.js')}}"></script>

  <script>
    function showLoader(){
      $('.bs-example-tabs').addClass("op-4");
      $('#loader1').removeClass("hidden");
    }

    function hideLoader(){
      $('.bs-example-tabs').removeClass("op-4");
      $('#loader1').addClass("hidden");
    }

    $('.tab-content form').on("submit", function (e) {
        $(".edit_setting").prop('disabled', true);
    });

    $('.timepicker').timepicker({
        showInputs: false,
        showMeridian: false,
    });
    
    $('#delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var mid = button.data('mid')
        var url = button.data('url');
        $(".remove-record-model").attr("action", url);
        var modal = $(this)
        modal.find('.modal-body #m_id').val(mid);
    });
  </script>


  <!-- Business Types -->
  @if(config('settings.party')==1)          
  <script type="text/javascript" src="{{asset('assets/plugins/settings/business.js')}}"></script>
  @endif     
  
  @if(config('settings.expenses')==1)
  <script type="text/javascript" src="{{asset('assets/plugins/settings/expensetypes.js')}}"></script>
  @endif

  <!-- Visit Purpose -->
  @if(config('settings.visit_module')==1 && config('settings.party')==1)
  @include('company.settingsnew.customjs.visit_purpose')
  @endif

  <script>
  showLoader()
  

  @if(config('settings.orders')==1)
  // <!-- Order Status -->
    var table;
    function initializeOrderStatusDT(){
      table = $('#orderstatus').DataTable({
        "columnDefs": [ {
        "targets": -1,
        "orderable": false
        } ],
        "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
          "<'row'<'col-xs-6'><'col-xs-6'>>" +
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
          buttons: [
              
          ]
  
      });
    }
    $(document).ready(function(){
      initializeOrderStatusDT()

      $('#color').colorpicker({
        format: 'hex'
      });
      $('#edit_color_pick').colorpicker({
        format: 'hex'
      });


      $('#addNewStatus').on('submit', function (event) {
        event.preventDefault();
        var currentElement = $(this);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: currentElement.attr('action'),
          type: "POST",
          data: new FormData(this),
          processData: false,
          contentType: false,
          cache: false,
          beforeSend:function(data){
            $('#addkeystatus').attr('disabled',true);
          },
          success: function (data) {
            $('#errlabel').html('');
            $('#addkeystatus').attr('disabled',false);
            $('#addNewStatus')[0].reset();
            alert('Created Successfully');
            $('#AddOrderStatus').modal('hide');
            var btn = '<a class="btn btn-warning btn-sm rowEditOrderStatus" moduleAttribute-id="'+data["id"]+'" moduleAttribute-name="'+data["title"]+'" moduleAttribute-color="'+data['color']+'" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteOrderStatus"  moduleAttribute-id="'+data["id"]+'" moduleAttribute-name="'+data["title"]+'" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>';
            if(data["order_amt_flag"]==1){
              data["order_amt_flag"] = "Yes";
            }else{
              data["order_amt_flag"] = "No";
            }
            if(data["order_edit_flag"]==1){
              data["order_edit_flag"] = "<i class='fa fa-check'><span hidden>Yes</span>";
            }else{
              data["order_edit_flag"] = "<i class='fa fa-times'><span hidden>No</span>";
            }
            if(data["order_delete_flag"]==1){
              data["order_delete_flag"] = "<i class='fa fa-check'><span hidden>Yes</span>";
            }else{
              data["order_delete_flag"] = "<i class='fa fa-times'><span hidden>No</span>";
            }
            table.row.add( [
              data["title"],
              data["order_amt_flag"],
              data["order_edit_flag"],
              data["order_delete_flag"],
              btn,
            ] ).draw();
          },
          error:function(jqXHR, textStatus, errorThrown){
            var err = JSON.parse(jqXHR.responseText);
            $('#errlabel').html('<span>'+err['errors']['name'][0]+'</span>');
            $('#addkeystatus').attr('disabled',false);
          },
          complete: function(){
          }
        });
      });

      $('#orderstatus').on('click', '.rowEditOrderStatus',function () {
          $('#edit_id').val($(this).attr('moduleAttribute-id'));
          if($(this).attr('moduleAttribute-name')=="Approved" || $(this).attr('moduleAttribute-name')=="Pending"){
            $('#edit_name').val($(this).attr('moduleAttribute-name')).prop('readonly', 'readonly');
          }else{
            $('#edit_name').val($(this).attr('moduleAttribute-name')).prop('readonly', false);
          }
          $('#edit_color_pick').removeClass('hidden');
          $('#aP_edit_color_pick').addClass('hidden');
          $('#edit_color').val($(this).attr('moduleAttribute-color')).prop('readonly', false);
          $('#aPedit_color').val($(this).attr('moduleAttribute-color')).prop('readonly', 'readonly');
          if($(this).attr('moduleAttribute-order_amt_flag')==1){
            $('#ed_order_amt_flag').prop('checked', true);
          }else{
            $('#ed_order_amt_flag').prop('checked', false);
          }
          if($(this).attr('moduleAttribute-order_edit_flag')==1){
            $('#ed_os_editable_flag').prop('checked', true);
          }else{
            $('#ed_os_editable_flag').prop('checked', false);
          }
          if($(this).attr('moduleAttribute-order_delete_flag')==1){
            $('#ed_os_deleteable_flag').prop('checked', true);
          }else{
            $('#ed_os_deleteable_flag').prop('checked', false);
          }
          $('#edit_color_pick').find('#color_span').children().css("background-color", $(this).attr('moduleAttribute-color'));
          $('#ederrlabel').html('');
          $('#EditOrderStatus').modal('show');
      });

      $('#editOrderStatus').on('submit', function (event) {
          event.preventDefault();
          var edit_id = $('#edit_id').val();
          var url = "{{domain_route('company.admin.orderstatus.update')}}";
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: new FormData(this),
              processData: false,
              contentType: false,
              cache: false,
              beforeSend:function(){
                $('#editkey').attr('disabled',true);
              },
              success: function (data) {
                  $('#orderstatus tbody').empty();
                  $('#orderstatus').DataTable().clear().destroy();
                  $('#orderstatus tbody').html(data);
                  initializeOrderStatusDT()
                  $('#ederrlabel').html('');
                  $('#editkey').attr('disabled',false);
                  $('#editOrderStatus')[0].reset();
                  alert('Updated Successfully');
                  $('#EditOrderStatus').modal('hide');
              },
              error:function(jqXHR, textStatus, errorThrown){ 
                var err = JSON.parse(jqXHR.responseText);
                if(err['message']!='The given data was invalid.'){
                  $('#ederrlabel').html('<span>'+err['message']+'</span>');
                }else{
                  $('#ederrlabel').html('<span>'+err['errors']['name'][0]+'</span>');
                }
                $('#editkey').attr('disabled',false);
              },
              complete:function(){
              }
          });
      });

      $('#orderstatus').on('click', '.rowDeleteOrderStatus',function () {
          $('#delete_id').val($(this).attr('moduleAttribute-id'));
          $('#delete_name').val($(this).attr('moduleAttribute-name'));
          $('#DeleteOrderStatus').modal('show');
          $('#del_title').html($(this).attr('moduleAttribute-name'));
      });

      $('#deleteExistingOrderStatus').on('submit', function (event) {
          event.preventDefault();
          var edit_id = $('#edit_id').val();
          var url = "{{domain_route('company.admin.orderstatus.delete')}}";
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: new FormData(this),
              processData: false,
              contentType: false,
              cache: false,
              beforeSend:function(){
                $('#delkey').attr('disabled',true);
              },
              success: function (data) {
                $('#orderstatus tbody').empty();
                $('#orderstatus').DataTable().clear().destroy();
                $('#orderstatus tbody').html(data['view']);
                initializeOrderStatusDT()
                $('#delkey').attr('disabled',false);
                alert(data['msg']);
                $('#DeleteOrderStatus').modal('hide');
              },
              error:function(jqXHR, textStatus, errorThrown){ 
                alert(textStatus);
                $('#editkey').attr('disabled',false);
              },
              complete:function(){
              }

          });
      });

    })

  
  @endif

  // <!-- Holiday -->
  @if(config('settings.ncal')==1)
  
    $(function(){
        var today = moment().format('YYYY-MM-DD');
        var currentYear = moment().year();
        var currentMonth = moment().month()+1;
        var currentDay = moment().date();
        var Weekday = moment().day();

        var NepaliCurrentDate = AD2BS(today);

        var nepaliDateData = NepaliCurrentDate.split('-');
        var nepaliCurrentYear = nepaliDateData[0];
        var nepaliCurrentMonth = nepaliDateData[1];
        var nepaliCurrentDay = nepaliDateData[2];

        $('.fc-next-button').click(function(e){
          e.preventDefault(e);
          var getMonth = parseInt($('#calNepaliMonth').val())+1;
          var getYear = parseInt($('#calNepaliYear').val());
          if(getMonth>12){
            getMonth = 1;
            getYear = getYear+1;
          }
          if(getMonth<10){
            getMonth = '0'+getMonth;
          }
          var firstEnd = getFirstDateEndDate(getYear,getMonth);
          engFirstDate = BS2AD(firstEnd[0]);
          engLastDate = BS2AD(firstEnd[11]);
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{domain_route('company.admin.holidays.getCalendar')}}",
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'getMonth': getMonth,
                  'getYear': getYear,
                  'engFirstDate': engFirstDate,
                  'engLastDate': engLastDate,
              },
              success: function (data) {
                  $('#calNepaliYear').val(data['year']);
                  $('#calNepaliMonth').val(data['month']);
                  var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                  $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                  $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                  $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              }
          });
        });
        $('.fc-prev-button').click(function(e){
          e.preventDefault(e);
          getMonth = parseInt($('#calNepaliMonth').val())-1;
          getYear = parseInt($('#calNepaliYear').val());
          if(getMonth<1){
            getMonth = 12;
            getYear = getYear-1;
          }
          if(getMonth<10){
            getMonth = '0'+getMonth;
          }
          var firstEnd = getFirstDateEndDate(getYear,getMonth);
          engFirstDate = BS2AD(firstEnd[0]);
          engLastDate = BS2AD(firstEnd[11]);
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{domain_route('company.admin.holidays.getCalendar')}}",
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'getMonth': getMonth,
                  'getYear': getYear,
                  'engFirstDate': engFirstDate,
                  'engLastDate': engLastDate,
              },
              success: function (data) {
                  $('#calNepaliYear').val(data['year']);
                  $('#calNepaliMonth').val(data['month']);
                  var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                  $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                  $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                  $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              }
          });
        });

        $('#todayMonth').click(function(e){
          e.preventDefault(e);
          getMonth = nepaliCurrentMonth;
          getYear = nepaliCurrentYear;
          if(getMonth<1){
            getMonth = 12;
            getYear = getYear-1;
          }
          var firstEnd = getFirstDateEndDate(getYear,getMonth);
          engFirstDate = BS2AD(firstEnd[0]);
          engLastDate = BS2AD(firstEnd[11]);
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{domain_route('company.admin.holidays.getCalendar')}}",
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'getMonth': getMonth,
                  'getYear': getYear,
                  'engFirstDate': engFirstDate,
                  'engLastDate': engLastDate,
              },
              success: function (data) {
                  $('#calNepaliYear').val(data['year']);
                  $('#calNepaliMonth').val(data['month']);
                  var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                  $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                  $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                  $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                  $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                  $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                  $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                  $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                  $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
              }
          });
        });
        $('#todayMonth').click();
    });
    
    $('#delete_event').on('submit', function (event) {
        event.preventDefault();
        var del_id = $('#del_id').val();
        var del_url = '{{domain_route('company.admin.holidays.delete')}}';
        var getMonth = parseInt($('#calNepaliMonth').val());
        var getYear = parseInt($('#calNepaliYear').val());
        var firstEnd = getFirstDateEndDate(getYear,getMonth);
        engFirstDate = BS2AD(firstEnd[0]);
        engLastDate = BS2AD(firstEnd[11]);
        if(getMonth<10){
          getMonth = '0'+getMonth;
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: del_url,
            type: "POST",
            data: {
              '_token': '{{csrf_token()}}',
              'del_id': del_id,
              'getMonth': getMonth,
              'getYear': getYear,
              'engFirstDate': engFirstDate,
              'engLastDate': engLastDate,
            },
            beforeSend:function(){
              $('#keyDeleteHoliday').attr('disabled',true);
            },
            success: function (data) {
                alert('Holiday Deleted Successfully');
                $('#del_event_modal').modal('hide');
                $('#keyDeleteHoliday').attr('disabled',false);
                $('#calNepaliYear').val(data['year']);
                $('#calNepaliMonth').val(data['month']);
                var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                var firstEnd = getFirstDateEndDate(data['year'],data['month']);
                $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
            },
            error:function(){
              $('#keyDeleteHoliday').attr('disabled',false);
              alert('Oops! Something went Wrong');
            }
        });
    });

    $('#AddNewHoliday').on('submit', function (event) {
        event.preventDefault();
        var edit_url = '{{domain_route('company.admin.holidays.store')}}';
        var getMonth = parseInt($('#calNepaliMonth').val());
        var getYear = parseInt($('#calNepaliYear').val());
        var name = $('#addHName').val(); 
        var description = $('#editHName').val(); 
        var start_date = $('#add_start_dateAD').val();
        var end_date = $('#add_end_dateAD').val();
        var firstEnd = getFirstDateEndDate(getYear,getMonth);
        engFirstDate = BS2AD(firstEnd[0]);
        engLastDate = BS2AD(firstEnd[11]);
        if(getMonth<10){
          getMonth = '0'+getMonth;
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: edit_url,
            type: "POST",
            data: {
              '_token': '{{csrf_token()}}',
              'getMonth': getMonth,
              'getYear': getYear,
              'start_date':start_date,
              'end_date':end_date,
              'engFirstDate': engFirstDate,
              'engLastDate': engLastDate,
              'name':name,
              'description':description,
            },
            beforeSend:function(){
              $('#btn_add_holiday').attr('disabled',true);
            },
            success: function (data) {
                alert('Holiday Created Successfully');
                $('#modalNewHoliday').modal('hide');
                $('#calNepaliYear').val(data['year']);
                $('#calNepaliMonth').val(data['month']);
                $('#btn_add_holiday').attr('disabled',false);
                var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                var firstEnd = getFirstDateEndDate(data['year'],data['month']);
                $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
            },
            error:function(){
              $('#btn_add_holiday').attr('disabled',false);
              alert('Oops! Something went Wrong');
            }
        });
    });

    $('#EditHoliday').on('submit', function (event) {
        event.preventDefault();
        var edit_id = $('#edit_id').val();
        var edit_url = '{{domain_route('company.admin.holidays.edit')}}';
        var getMonth = parseInt($('#calNepaliMonth').val());
        var getYear = parseInt($('#calNepaliYear').val());
        var name = $('#edit_hname').val(); 
        var description = $('#edit_description').val(); 
        var start_date = $('#fromDate').val();
        var end_date = $('#to_date').val();
        var firstEnd = getFirstDateEndDate(getYear,getMonth);
        engFirstDate = BS2AD(firstEnd[0]);
        engLastDate = BS2AD(firstEnd[11]);
        if(getMonth<10){
          getMonth = '0'+getMonth;
        }
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: edit_url,
            type: "POST",
            data: {
              '_token': '{{csrf_token()}}',
              'edit_id': edit_id,
              'getMonth': getMonth,
              'getYear': getYear,
              'start_date':start_date,
              'end_date':end_date,
              'engFirstDate': engFirstDate,
              'engLastDate': engLastDate,
              'name':name,
              'description':description,
            },
            beforeSend:function(){
              $('#keyEditHoliday').attr('disabled',true);
            },
            success: function (data) {
                alert('Holiday Updated Successfully');
                $('#fullCalModal').modal('hide');
                $('#calNepaliYear').val(data['year']);
                $('#calNepaliMonth').val(data['month']);
                $('#keyEditHoliday').attr('disabled',false);
                var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
                $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
                $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
                var firstEnd = getFirstDateEndDate(data['year'],data['month']);
                $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
                $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
                $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
                $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
                $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
                $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
            },
            error:function(){
              $('#keyEditHoliday').attr('disabled',false);
              alert('Oops! Something went Wrong');
            }
        });
    });

    $('#populate').click(function (e) {
        e.preventDefault();
        $('#PopulateModal').modal('show');
    });

    $('#populates').click(function () {
        var NepaliEndDates = [ 
        [2001,31,31,32,31,31,31,30,29,30,29,30,30],
        [2002,31,31,32,32,31,30,30,29,30,29,30,30],
        [2003,31,32,31,32,31,30,30,30,29,29,30,31],
        [2004,30,32,31,32,31,30,30,30,29,30,29,31],
        [2005,31,31,32,31,31,31,30,29,30,29,30,30],
        [2006,31,31,32,32,31,30,30,29,30,29,30,30],
        [2007,31,32,31,32,31,30,30,30,29,29,30,31],
        [2008,31,31,31,32,31,31,29,30,30,29,29,31],
        [2009,31,31,32,31,31,31,30,29,30,29,30,30],
        [2010,31,31,32,32,31,30,30,29,30,29,30,30],
        [2011,31,32,31,32,31,30,30,30,29,29,30,31],
        [2012,31,31,31,32,31,31,29,30,30,29,30,30],
        [2013,31,31,32,31,31,31,30,29,30,29,30,30],
        [2014,31,31,32,32,31,30,30,29,30,29,30,30],
        [2015,31,32,31,32,31,30,30,30,29,29,30,31],
        [2016,31,31,31,32,31,31,29,30,30,29,30,30],
        [2017,31,31,32,31,31,31,30,29,30,29,30,30],
        [2018,31,32,31,32,31,30,30,29,30,29,30,30],
        [2019,31,32,31,32,31,30,30,30,29,30,29,31],
        [2020,31,31,31,32,31,31,30,29,30,29,30,30],
        [2021,31,31,32,31,31,31,30,29,30,29,30,30],
        [2022,31,32,31,32,31,30,30,30,29,29,30,30],
        [2023,31,32,31,32,31,30,30,30,29,30,29,31],
        [2024,31,31,31,32,31,31,30,29,30,29,30,30],
        [2025,31,31,32,31,31,31,30,29,30,29,30,30],
        [2026,31,32,31,32,31,30,30,30,29,29,30,31],
        [2027,30,32,31,32,31,30,30,30,29,30,29,31],
        [2028,31,31,32,31,31,31,30,29,30,29,30,30],
        [2029,31,31,32,31,32,30,30,29,30,29,30,30],
        [2030,31,32,31,32,31,30,30,30,29,29,30,31],
        [2031,30,32,31,32,31,30,30,30,29,30,29,31],
        [2032,31,31,32,31,31,31,30,29,30,29,30,30],
        [2033,31,31,32,32,31,30,30,29,30,29,30,30],
        [2034,31,32,31,32,31,30,30,30,29,29,30,31],
        [2035,30,32,31,32,31,31,29,30,30,29,29,31],
        [2036,31,31,32,31,31,31,30,29,30,29,30,30],
        [2037,31,31,32,32,31,30,30,29,30,29,30,30],
        [2038,31,32,31,32,31,30,30,30,29,29,30,31],
        [2039,31,31,31,32,31,31,29,30,30,29,30,30],
        [2040,31,31,32,31,31,31,30,29,30,29,30,30],
        [2041,31,31,32,32,31,30,30,29,30,29,30,30],
        [2042,31,32,31,32,31,30,30,30,29,29,30,31],
        [2043,31,31,31,32,31,31,29,30,30,29,30,30],
        [2044,31,31,32,31,31,31,30,29,30,29,30,30],
        [2045,31,32,31,32,31,30,30,29,30,29,30,30],
        [2046,31,32,31,32,31,30,30,30,29,29,30,31],
        [2047,31,31,31,32,31,31,30,29,30,29,30,30],
        [2048,31,31,32,31,31,31,30,29,30,29,30,30],
        [2049,31,32,31,32,31,30,30,30,29,29,30,30],
        [2050,31,32,31,32,31,30,30,30,29,30,29,31],
        [2051,31,31,31,32,31,31,30,29,30,29,30,30],
        [2052,31,31,32,31,31,31,30,29,30,29,30,30],
        [2053,31,32,31,32,31,30,30,30,29,29,30,30],
        [2054,31,32,31,32,31,30,30,30,29,30,29,31],
        [2055,31,31,32,31,31,31,30,29,30,29,30,30],
        [2056,31,31,32,31,32,30,30,29,30,29,30,30],
        [2057,31,32,31,32,31,30,30,30,29,29,30,31],
        [2058,30,32,31,32,31,30,30,30,29,30,29,31],
        [2059,31,31,32,31,31,31,30,29,30,29,30,30],
        [2060,31,31,32,32,31,30,30,29,30,29,30,30],
        [2061,31,32,31,32,31,30,30,30,29,29,30,31],
        [2062,30,32,31,32,31,31,29,30,29,30,29,31],
        [2063,31,31,32,31,31,31,30,29,30,29,30,30],
        [2064,31,31,32,32,31,30,30,29,30,29,30,30],
        [2065,31,32,31,32,31,30,30,30,29,29,30,31],
        [2066,31,31,31,32,31,31,29,30,30,29,29,31],
        [2067,31,31,32,31,31,31,30,29,30,29,30,30],
        [2068,31,31,32,32,31,30,30,29,30,29,30,30],
        [2069,31,32,31,32,31,30,30,30,29,29,30,31],
        [2070,31,31,31,32,31,31,29,30,30,29,30,30],
        [2071,31,31,32,31,31,31,30,29,30,29,30,30],
        [2072,31,32,31,32,31,30,30,29,30,29,30,30],
        [2073,31,32,31,32,31,30,30,30,29,29,30,31],
        [2074,31,31,31,32,31,31,30,29,30,29,30,30],
        [2075,31,31,32,31,31,31,30,29,30,29,30,30],
        [2076,31,32,31,32,31,30,30,30,29,29,30,30],
        [2077,31,32,31,32,31,30,30,30,29,30,29,31],
        [2078,31,31,31,32,31,31,30,29,30,29,30,30],
        [2079,31,31,32,31,31,31,30,29,30,29,30,30],
        [2080,31,32,31,32,31,30,30,30,29,29,30,30],
        [2081,31,31,32,32,31,30,30,30,29,30,30,30],
        [2082,30,32,31,32,31,30,30,30,29,30,30,30],
        [2083,31,31,32,31,31,30,30,30,29,30,30,30],
        [2084,31,31,32,31,31,30,30,30,29,30,30,30],
        [2085,31,32,31,32,30,31,30,30,29,30,30,30],
        [2086,30,32,31,32,31,30,30,30,29,30,30,30],
        [2087,31,31,32,31,31,31,30,30,29,30,30,30],
        [2088,30,31,32,32,30,31,30,30,29,30,30,30],
        [2089,30,32,31,32,31,30,30,30,29,30,30,30],
        [2090,30,32,31,32,31,30,30,30,29,30,30,30]
      ];

      var today = moment().format('YYYY-MM-DD');
      var currentYear = moment().year();
      var currentMonth = moment().month()+1;
      var currentDay = moment().date();
      var Weekday = moment().day();

      var NepaliCurrentDate = AD2BS(today);

      var nepaliDateData = NepaliCurrentDate.split('-');
      var nepaliCurrentYear = nepaliDateData[0];
      var nepaliCurrentMonth = nepaliDateData[1];
      var nepaliCurrentDay = nepaliDateData[2];

      var offtype = $('#offtype').val();
      var rangetype = $('#rangetype').val();
      var nYear = $('#calNepaliYear').val(); 
      var nMonth = $('#calNepaliMonth').val(); 
      var firstEnd = getFirstDateEndDate(getYear,getMonth);
      engFirstDate = BS2AD(firstEnd[0]);
      engLastDate = BS2AD(firstEnd[11]);
      for(i=0;i<=89;i++){
        if(NepaliEndDates[i][0]==nYear){
          var selectedYear =  NepaliEndDates[i];
        }            
      }
      var lastMonthEndDate = selectedYear[12];
      var yearEndDate = BS2AD(nYear+'-12-'+lastMonthEndDate);
      if(nYear == nepaliCurrentYear){
        start = moment().format('YYYY-MM-DD');
      }else if(nYear > NepaliCurrentDate){
        start = BS2AD(nYear+'-01-01');
      }else{
        alert("Can't populate past years");
      } 
      var r = confirm("Are you sure you want to populate current Year?");
      if (r == true) {
          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{domain_route('company.admin.holidays.populate')}}",
            type: "POST",
            data: {
                '_token': '{{csrf_token()}}',
                'start': start,
                'yearEndDate':yearEndDate,
                'offtype': offtype,
                'rangetype': rangetype,
                'getYear':nYear,
                'getMonth':nMonth,
                'engFirstDate': engFirstDate,
                'engLastDate': engLastDate,
            },
            success: function (data) {
              alert(data['result']);
              $('#calNepaliYear').val(data['year']);
              $('#calNepaliMonth').val(data['month']);
              $('#PopulateModal').modal('hide');
              var ajaxYear = convertNos(data['year'][0])+convertNos(data['year'][1])+convertNos(data['year'][2])+convertNos(data['year'][3]);
              $('#monthYear').html(getNepaliMonth(data['month']-1)+' '+ajaxYear);
              $('#calendarBody').html(getNepaliCalendar(data['year'],data['month']));
              var firstEnd = getFirstDateEndDate(data['year'],data['month']);
              $('#calrowbody1').html(populateEvent(firstEnd[0],firstEnd[1],data['holidays'],data['holidays'].length));
              $('#calrowbody2').html(populateEvent(firstEnd[2],firstEnd[3],data['holidays'],data['holidays'].length));
              $('#calrowbody3').html(populateEvent(firstEnd[4],firstEnd[5],data['holidays'],data['holidays'].length));
              $('#calrowbody4').html(populateEvent(firstEnd[6],firstEnd[7],data['holidays'],data['holidays'].length));
              $('#calrowbody5').html(populateEvent(firstEnd[8],firstEnd[9],data['holidays'],data['holidays'].length));
              $('#calrowbody6').html(populateEvent(firstEnd[10],firstEnd[11],data['holidays'],data['holidays'].length));
            }
          });
      } else {
          alert('populating canceled');
      }
    });

    $('#BtnAddNewHoliday').on('click',function(e){
      e.preventDefault();
      $('#modalNewHoliday').modal('show');
      $('#btn_add_holiday').removeAttr('disabled');
    });

    var lastMonthNepaliDate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
    var pdates = lastMonthNepaliDate.split("-");
    var pdate = pdates[1]+'/'+pdates[2]+'/'+pdates[0];
    $('#add_start_date').nepaliDatePicker({
      disableBefore: pdate,
      onChange: function(){
        $('#add_start_dateAD').val(BS2AD($('#add_start_date').val()));
        if($('#add_end_date').val()<$('#add_start_date').val()){
          $('#add_end_date').val($('#add_start_date').val());
        }
      }
    });

    $('#add_end_date').nepaliDatePicker({
      disableBefore:pdate,
      onChange: function(){
        $('#add_end_dateAD').val(BS2AD($('#add_end_date').val()));
        if($('#add_start_date').val()>$('#add_end_date').val()){
          $('#add_start_date').val($('#add_end_date').val());
        }
      }
    });

    $('#calendar').on('click','.fa-edit',function(e){
      e.preventDefault();
      $('#edit_id').val($(this).attr('data-id'));
      $('#fullCalModal').modal('show');
      $('#edit_hname').val($(this).attr('data-name'));
      $('#edit_description').val($(this).attr('data-desc'));
      $('#edit_start_date').val(AD2BS($(this).attr('data-start')));
      $('#edit_end_date').val(AD2BS($(this).attr('data-end')));
      $('#edit_end_date').removeAttr('disabled');
    });
    $('#edit_start_date').nepaliDatePicker({
      disableBefore:pdate,
      onChange: function(){
        $('#fromDate').val(BS2AD($('#edit_start_date').val()));
        if($('#edit_end_date').val()<$('#edit_start_date').val()){
          $('#edit_end_date').val($('#edit_start_date').val());
        }
      }
    });
    $('#edit_end_date').nepaliDatePicker({
      disableBefore:pdate,
      onChange: function(){
        $('#to_date').val((BS2AD($('#edit_end_date').val())));
        if($('#edit_start_date').val()>$('#edit_end_date').val()){
          $('#edit_start_date').val($('#edit_end_date').val());
        }
      }
    });
    $('#calendar').on('click','.fa-trash',function(e){
      e.preventDefault();
      $('#del_id').val($(this).attr('data-id'));
      $('#del_calYear').val($('#calNepaliYear').val());
      $('#del_calMonth').val($('#calNepaliMonth').val());        
      $('#del_event_modal').modal('show');
    });  

    $('#calendarBody').on('click','td',function(){
      if(typeof $(this).attr('data-date')!="undefined"){
        $('#modalNewHoliday').modal('show');
        $('#add_start_date').val($(this).attr('data-date'));
        $('#add_start_dateAD').val(BS2AD($(this).attr('data-date')));
        $('#add_end_date').val($(this).attr('data-date'));
        $('#add_end_dateAD').val(BS2AD($(this).attr('data-date')));
      }
    });


  
  @else
  
      // Holiday Section js

      $(function () {

        $('#populate').click(function (e) {
            e.preventDefault();
            $('#PopulateModal').modal('show');
        });

        $('#populates').click(function () {
            var offtype = $('#offtype').val();
            var rangetype = $('#rangetype').val();
            var moment = $('#calendar').fullCalendar('getDate').format('Y-M-D');
            var momentYear = $('#calendar').fullCalendar('getDate').format('Y');
            var today = new Date();
            var currentDate = today.getFullYear() + "-" + (today.getMonth() + 1) + "-" + today.getDate()
            var currentYear = today.getFullYear();
            if (momentYear == currentYear) {
                moment = currentDate;
            }
            var r = confirm("Are you sure you want to populate current Year?");
            if (r == true) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{domain_route('company.admin.holidays.populate')}}",
                    type: "POST",
                    data: {
                        '_token': '{{csrf_token()}}',
                        'currentDate': moment,
                        'offtype': offtype,
                        'rangetype': rangetype,
                    },
                    success: function (data) {
                        alert(data['result']);
                        window.location = "{{domain_route('company.admin.settingnew.customization')}}";
                    }
                });
            } else {
                alert('populating canceled');
            }
        });

        var monthago = moment();
        monthago = monthago.subtract(30,'days');
        monthago = monthago.format('YYYY-MM-DD');

        $('.fromdate').datepicker({
            startDate: monthago,
            setDate: new Date(),
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
        });

        $('.todate').datepicker({
            startDate: monthago,
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
        }).attr('disabled');

        $('.fromdate').datepicker('setDate', new Date());
        $('.todate').datepicker('setDate', new Date());

        $('.fromdate').change(function (event) {
            event.preventDefault();
            var newdate = $(this).val();
            $('.todate').datepicker('remove');
            if ($('#edit_end_date').val() < $('#edit_start_date').val()) {
                $('#edit_end_date').val(newdate);
            }
            if ($('#add_end_date').val() < $('#add_start_date').val()) {
                $('.todate').val(newdate);
            }
            $('.todate').datepicker({
                startDate: newdate,
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
            }).removeAttr('disabled');
            $('.todate').datepicker('setDate', $('#edit_end_date').val());
            $('.todate').datepicker('setDate', $('#add_end_date').val());
        });

        $('.createholiday').click(function () {
            $('#AddNewHoliday')[0].reset();
            $('#exampleModalCenter').modal('show');
            $('.todate').prop('disabled', 'true');
        });

        $('#AddNewHoliday').on('submit', function (event) {
            event.preventDefault();
            var currentElement = $(this);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: currentElement.attr('action'),
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend:function(){
                  $('#btn_add_holiday').attr('disabled','disabled');
                },
                success: function (data) {
                    if (data['result']) {
                        alert(data['result']);
                        $('#exampleModalCenter').modal('hide');
                        $('#AddNewHoliday').trigger('reset');
                        var eventObject={
                          title          : data['name'],
                          start          : data['start_date'],
                          end            : data['nextday'],
                          backgroundColor: '#f56954', //red
                          borderColor    : '#f56954', //red
                          id             : data['id'],
                          allDay         :  true,
                          description    : data['description'],
                          begin          : data['start_date'],
                          finish         : data['end_date'],
                        }
                        var calendar = $('#calendar').fullCalendar('renderEvent', eventObject,true);
                        $('#btn_add_holiday').removeAttr('disabled');
                    }
                },
                error:function(){
                  $('#btn_add_holiday').removeAttr('disabled');
                  alert('Oops! Something went Wrong');
                }
            });
        });


        $('#EditHoliday').on('submit', function (event) {
            event.preventDefault();
            var edit_id = $('#edit_eid').val();
            var title = $('#edit_ename').val();
            var description = $('#edit_description').val();
            var edit_start_date = $('#edit_start_date').val();
            var edit_end_date = $('#edit_end_date').val();
            $('#fullCalModal').modal('hide');
            var currentElement = $(this);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: currentElement.attr('action'),
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend:function(){
                  $('#keyEditHoliday').attr('disabled',true);
                },
                success: function (data) {
                    if (data['result']) {                        
                        alert(data['result']);
                        $('#exampleModalCenter').modal('hide');
                        $('#EditHoliday').trigger('reset');
                        var eventObject={
                          title          : title,
                          start          : edit_start_date,
                          end            : data['nextday'],
                          backgroundColor: '#f56954', //red
                          borderColor    : '#f56954', //red
                          id             : edit_id,
                          description    : description,
                          begin          : edit_start_date,
                          finish         : edit_end_date,
                        }
                        $('#calendar').fullCalendar('removeEvents', edit_id);
                        $('#calendar').fullCalendar('renderEvent', eventObject,true);
                    } else {
                        alert('Holiday Updated Failed');                          
                    }
                    $('#keyEditHoliday').attr('disabled',false);
                },
                error:function(){
                    $('#keyEditHoliday').attr('disabled',false);
                    alert('Oops! Something went Wrong');
                }
            });
        });

        $('#delete_event').on('submit', function (event) {
            event.preventDefault();
            var event_del_id = $('#del_id').val();
            var del_url = '{{domain_route('company.admin.holidays.delete')}}';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: del_url,
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend:function(){
                  $('#keyDeleteHoliday').attr('disabled',true);
                },
                success: function (data) {
                    alert('Holiday Deleted Successfully');
                    $('#del_event_modal').modal('hide');
                    $('#calendar').fullCalendar('removeEvents', event_del_id);
                    $('#keyDeleteHoliday').attr('disabled',false);
                },
                error:function(){
                  $('#keyDeleteHoliday').attr('disabled',false);
                  alert('Oops! Something went Wrong');
                }
            });
        });

        /* initialize the external events
        -----------------------------------------------------------------*/
        function init_events(ele) {
            ele.each(function () {

                // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                // it doesn't need to have a start or end
                var eventObject = {
                    title: $.trim($(this).text()) // use the element's text as the event title
                }

                // store the Event Object in the DOM element so we can get to it later
                $(this).data('eventObject', eventObject)

                // make the event draggable using jQuery UI
                // $(this).draggable({
                //   zIndex        : 1070,
                //   revert        : true, // will cause the event to go back to its
                //   revertDuration: 0  //  original position after the drag
                // })

            })
        }

        init_events($('#external-events div.external-event'))

        /* initialize the calendar
        -----------------------------------------------------------------*/
        //Date for the calendar events (dummy data)
        var date = new Date()
        var d = date.getDate(),
            m = date.getMonth(),
            y = date.getFullYear()
        var calendar = $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },

            buttonText: {
                today: 'today',
                month: 'month',
                week: 'week',
                day: 'day'
            },
            dayClick: function (date, jsEvent, view) {
                if (monthago <= date.format()) {
                    $('#AddNewHoliday')[0].reset();
                    $('.fromdate').datepicker('destroy');
                    $('.fromdate').datepicker({
                        startDate: monthago,
                        format: 'yyyy-mm-dd',
                        todayHighlight: true,
                        autoclose: true,
                    });
                    $('.fromdate').datepicker('setDate', date.format());
                    $('#add_start_date').val(date.format('YYYY-MM-DD'));
                    $('#add_end_date').val(date.format('YYYY-MM-DD'));
                    $('#exampleModalCenter').modal();
                }else{
                  alert("Can't create holiday before a month ago");
                }
            },
            eventMouseover: function (event, jsEvent, view) {
                $(this).attr('title', event.title);
            },
            eventRender: function (event, element, view) {
                var j = document.createElement('i');
                j.className = 'fa';
                j.classList.add("fa-edit");
                j.classList.add("btn");
                j.classList.add("grey-mint");
                j.classList.add("btn-xs");
                j.addEventListener("click", function () {
                  if (monthago <= event.start.format()) {
                    $('#edit_eid').val(event.id);
                    $('#fullCalModal').modal('show');
                    $('#edit_ename').val(event.title);
                    $('#edit_description').val(event.description);
                    $('#edit_start_date').val(event.begin);
                    $('#edit_end_date').val(event.finish);
                    $('.fromdate').datepicker('destroy');
                    $('.todate').datepicker('destroy');
                    $('.fromdate').datepicker({
                        startDate: monthago,
                        format: 'yyyy-mm-dd',
                        todayHighlight: true,
                        autoclose: true,
                    });
                    $('.todate').datepicker({
                        startDate: monthago,
                        format: 'yyyy-mm-dd',
                        todayHighlight: true,
                        autoclose: true,
                    });
                    $('.fromdate').datepicker('setDate', event.begin);
                    $('.todate').datepicker('setDate', event.finish);
                    $('#edit_end_date').removeAttr('disabled');
                  }else{
                    alert('Can not edit older dates');
                  }
                });
                element.find('div.fc-content span.fc-title').prepend(j);
                var i = document.createElement('i');
                i.className = 'fa';
                i.classList.add("fa-trash");
                // i.classList.add("pull-right");
                i.classList.add("btn");
                i.classList.add("grey-mint");
                i.classList.add("btn-xs");
                i.addEventListener("click", function () {
                    $('#del_id').val(event.id);
                    $('#del_event_modal').modal('show');
                });
                element.find('div.fc-content span.fc-title').prepend(i);
            },
            //Random default events
            events: [
                @foreach($holidays as $holiday)
                {
                    title: '{{$holiday->name}}',
                    start: '{{$holiday->start_date}}',
                    end: '{{$data['nextday_end'][$holiday->id]}}',
                    backgroundColor: '#f56954', //red
                    borderColor: '#f56954', //red
                    id: '{{$holiday->id}}',
                    allDay:  true,
                    description: '{{$holiday->description}}',
                    begin          : '{{$holiday->start_date}}',
                    finish: '{{$holiday->end_date}}',
                },
              @endforeach
            ],
            editable: false,
            droppable: false, // this allows things to be dropped onto the calendar !!!

        });

        /* ADDING EVENTS */
        var currColor = '#3c8dbc' //Red by default
        //Color chooser button
        var colorChooser = $('#color-chooser-btn')
        $('#color-chooser > li > a').click(function (e) {
            e.preventDefault()
            //Save color
            currColor = $(this).css('color')
            //Add color effect to button
            $('#add-new-event').css({'background-color': currColor, 'border-color': currColor})
        })
        $('#add-new-event').click(function (e) {
          e.preventDefault()
          //Get value and make sure it is not null
          var val = $('#new-event').val()
          if (val.length == 0) {
              return
          }

          //Create events
          var event = $('<div />')
          event.css({
              'background-color': currColor,
              'border-color': currColor,
              'color': '#fff'
          }).addClass('external-event')
          event.html(val)
          $('#external-events').prepend(event)

          //Add draggable funtionality
          init_events(event)

          //Remove event from text input
          $('#new-event').val('')
        });
      });
  
  @endif

  @if(config('settings.collections')==1)
  // <!-- Banks -->
  
    $('#addNewBank').on('submit',function(e){
      e.preventDefault();
      var url = $(this).attr('action');
      var data = $(this).serialize();
      $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "POST",
            data: {
                '_token': '{{csrf_token()}}',
                'data':data,
            },
            beforeSend:function(){
              $('.addNewBank').attr('disabled',true);
              showLoader();
            },
            success: function (data) {
                if(data['result']==true){
                  $('#addNewBank')[0].reset();
                  $('#tbl_banks').empty();
                  $('#tbl_banks').html(data['banks']);
                  alert('Bank Added Successfully')
                }else{
                  alert('Bank name already exists.')
                }
                $('.addNewBank').attr('disabled',false);
            },
            error:function(){
              $('.addNewBank').attr('disabled',false);
              alert('Oops! Something went wrong...');
            },
            complete:function(){
              hideLoader()
            }
        });
    });

    $('#tblbanks').on('click','.edit-bank',function(event){
      event.preventDefault();
      $('#editBank').modal('show');
      var name = $(this).attr('data-name');
      $('#editbankname').val(name);
      var url = $(this).attr('data-url');
      $('#formEditBank').attr('action',url);
    });
    $('#formEditBank').on('submit',function(e){
      e.preventDefault();
      var url = $(this).attr('action');
      var data = $(this).serialize();
      $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "POST",
            data: {
                '_token': '{{csrf_token()}}',
                'data':data,
            },
            beforeSend:function(){
              $('.updateBank').attr('disabled',true);
              showLoader();
            },
            success: function (data) {
                if(data['result']==true){
                  $('#tbl_banks').empty();
                  $('#tbl_banks').html(data['banks']);
                  $('.updateBank').attr('disabled',false);
                  $('#editBank').modal('hide');  
                  alert("Bank Updated");                  
                }else{
                  alert("Bank's name exists");
                }
                $('.delete-button').removeAttr('disabled');
            },
          error:function(){
            $('.delete-button').attr('disabled',false);
            alert('Oops! Something went wrong...');
          },
          complete:function(){
            hideLoader()
          }
        });
    });

    $('#tblbanks').on('click','.delete-bank',function(event){
        event.preventDefault();
        $('#modalDeleteBank').modal('show');
        var url = $(this).attr('data-url');
        $('#frmDelBank').attr('action',url);
        $('#btn-beatDelete-key').removeAttr('disabled');
    });
    $('#frmDelBank').on('submit',function(e){
      e.preventDefault();
      var url = $(this).attr('action');
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: "POST",
          data: {
              '_token': '{{csrf_token()}}',
          },
          beforeSend:function(){
            $('.removeBankKey').attr('disabled',true);
            showLoader();
          },
          success: function (data) {
              if(data['result']==true){
                alert("Bank Successfully Deleted");
                $('#tbl_banks').empty();
                $('#tbl_banks').html(data['banks']);
              }else{
                alert("Bank can't be deleted since cheques found under it.")
              }
              $('#modalDeleteBank').modal('hide'); 
              $('.removeBankKey').attr('disabled',false); 
                $('#delkey').attr('disabled',false);                 
          },
          error:function(){
            $('.removeBankKey').attr('disabled',false);
            alert('Oops! Something went wrong...');
          },
          complete:function(){
            hideLoader()
          }

      });
    });

  
  @endif

  // <!-- Beats -->
  
  @if(config('settings.party')==1)
    function checkAssignParties(){
    let selCity = $('#beatcity').val();
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ domain_route('company.admin.beat.assignedParties') }}",
      type: "GET",
      async:false,
      success: function (data) {
        $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').attr('checked',false);
        $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').prop('disabled',false);
        $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').parent().css('cursor','pointer');
        $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').parent().css('color','#333');
        for(let count =0 ; count < data.length; count++){
          $('#beats-detail').find('#ms-list-1 input[value="'+data[count]+'"]').attr('checked','checked');
          $('#beats-detail').find('#ms-list-1 input[value="'+data[count]+'"]').prop('disabled',true);
            $('#beats-detail').find('#ms-list-1 input[value="'+data[count]+'"]').parent().css('background-color','#efefef');
          let label_id = $('#beats-detail').find('#ms-list-1 input[value="'+data[count]+'"]').attr("id");
          $('label[for="'+label_id+'"]').css('color','gray');
          $('label[for="'+label_id+'"]').css('cursor','not-allowed');
        }
        $('#partyId').multiselect('refresh');
        $('#partyId').multiselect({
          columns: 1,
          placeholder: 'Select party',
          search: true,
          selectAll: true
        });
      },
      error:function(xhr){

      },
    });
  }

    $('document').ready(function(){
      // checkAssignParties();
      $('#partyId').multiselect({
          columns: 1,
          placeholder: 'Select party',
          search: true,
          selectAll: true,
      });
      $('#employeeId').multiselect({
          columns: 1,
          placeholder: 'Select Employee',
          search: true,
          selectAll: true
      });

      
      
    })

    $('#tbl_beats').on('click','.beat-delete',function(e){
      $('#deletebeat').modal('show');
      $('#ajaxRemoveBeat').attr('action',$(this).attr('data-url'));
    });
    $('#beatcity').select2({
        placeholder: 'Select City',
      });

      $('#addNewBeat').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.addBeat').attr('disabled','disabled');
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#tbody_beats').empty();
                    $('#tbody_beats').html(data['beats']);
                    $('#addNewBeat')[0].reset();
                    $('#ms-list-1').find('span').empty();
                    $('#ms-list-1').find('li').removeClass('selected');
                    $('#ms-list-1').removeClass('ms-active');
                    $('#ms-list-1').removeClass('ms-has-selections');
                    checkAssignParties();
                    $('#ms-list-1').multiselect('reload');
                    $('.addBeat').attr('disabled',false);
                    $("#addNewBeat").find("#beatcity").select2("destroy");
                    $("#addNewBeat").find("#beatcity").select2({
                      placeholder: 'Select City',
                    });
                    alert('Beat Successfully Created');
                  }else{
                    alert('Beat Already Exists or Beat with empty party given');                    
                  }
                  $('.addBeat').attr('disabled',false);
              },
              error:function(){
                $('.addBeat').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });
      });

      $('#tbl_beats').on('click','.beat-view',function(event){
          event.preventDefault();
          var url = $(this).attr('data-url');
          let title = $(this).data('name'); 
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "GET",
              data: {
                  '_token': '{{csrf_token()}}',
              },
              beforeSend:function(){
                showLoader()
              },
              success: function (data) {
                  if(data['result'] == "No parties available"){
                    alert(data['result']);
                  }else{
                    var j=0;
                    $("#beat_parties ul").empty();
                    $.each(data['name'], function(){ 
                      $('<li></li>').val(data['name'][j]).text(data['name'][j]).appendTo('#beat_parties ul');
                      j++;
                    })                  
                    $('#modalShowBeat').modal('show');
                    $('#modalShowBeat').find('.show-beat-name').html('<span>'+title+'</span>');                    
                  }
              },
              complete:function(){
                hideLoader()
              },
          });

      });

      $('#tbl_beats').on('click','.beat-edit',function(event){
          event.preventDefault();
          var url = $(this).attr('data-edit-url');
          var update_url = $(this).attr('data-url');
          let cityVal = $(this).data('city');
          let beatId = $(this).data('bid');
          $('#editBeatName').val($(this).attr('data-name'));
          $('#updateBeatSettings').find('#editbeat_id').val(beatId);
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "GET",
              data: {
                  '_token': '{{csrf_token()}}',
                  'cityVal': cityVal
              },
              beforeSend:function(){
                showLoader()
              },
              success: function (data) {
                  $('#modalEditBeat').modal('show');
                  $(".edit_beatcity").val(cityVal);
                  $("#edit_beatcity").select2({
                    dropdownParent: $("#modalEditBeat"),
                    placeholder: 'Select City',
                  });
                  $('#assignPartyId').empty();
                  $.each(data['all_beat_party'],function(i,v){
                    if(data['selected_beat_party'].includes(v.id)){
                      $('<option></option>').val(v.id).text(v.company_name).attr('selected','selected').appendTo('#assignPartyId');
                    }else{
                      $('<option></option>').val(v.id).text(v.company_name).appendTo('#assignPartyId');
                    }
                  });
                  $('#updateBeatSettings').attr('action',update_url);
                  $('#updateBeatSettings #assignPartyId').multiselect('reload');
                  $('#updateBeatSettings #assignPartyId').multiselect({
                    columns: 1,
                    placeholder: 'Select party',
                    search: true,
                    selectAll: true,
                  });
                  $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').parent().css('cursor','pointer');
                  $.each(data['other_beat_party'],function(i,v){
                    $('#updateBeatSettings').find('#ms-list-2 input[value="'+v+'"]').prop('disabled',true);
                    let label_id = $('#updateBeatSettings').find('#ms-list-2 input[value="'+v+'"]').attr("id");
                    $('label[for="'+label_id+'"]').css('cursor','not-allowed');
                    $('label[for="'+label_id+'"]').css('background-color','#efefef');
                    $('label[for="'+label_id+'"]').css('color','grey');
                  });
                  $.each(data['selected_beat_party'],function(i,v){
                    let label_id = $('#updateBeatSettings').find('#ms-list-2 input[value="'+v+'"]').attr("id");
                    $('label[for="'+label_id+'"]').css('color','#800080');
                  });
                  $('#updateBeatSettings #assignPartyId').multiselect('refresh');
              },

              complete:function(){
                hideLoader()
              },
          });
      });

      $('#updateBeatSettings').on('submit',function(event){
        event.preventDefault();
        var beat_id = $('#editbeat_id').val();
        var data = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data'  : data, 
              },
              beforeSend:function(){
                $('.updateBeat').attr('disabled','disabled');
                // showLoader()
              },
              success: function (data) {
                if(data['result']==true){
                  checkAssignParties();
                  alert('Beat Updated');
                  $('#tbody_beats').empty();
                  $('#tbody_beats').html(data['beats']);                 
                }else{
                  alert("Can't Update Beat. Beat Already Exists or Beat with empty party given.");
                }
                $('.updateBeat').attr('disabled',false);
                
                $('#modalEditBeat').modal('hide'); 
              },
              error:function(jqXHR){
                $('.updateBeat').attr('disabled',false);
                alert('Oops! Something went wrong...');
              },
              complete:function(){
                // hideLoader()
              }
          });
      });

      $('#ajaxRemoveBeat').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: "POST",
          data: {
              '_token': '{{csrf_token()}}',
          },
          beforeSend:function(){
            $('#btn-beatDelete-key').attr('disabled',true);
              },
          success: function (data) {
            if(data['result']==false){
              alert('Beat could not be deleted');
            }else{
              $('#deletebeat').modal('hide');
              $('#tbody_beats').empty();              
              checkAssignParties();
              $('#tbody_beats').html(data['beats']);
              $('#btn-beatDelete-key').attr('disabled',false); 
              alert('Beat deleted successfully');             
            }
          },
          error:function(){
             $('#btn-beatDelete-key').attr('disabled',false); 
            alert('Oops! Something went Wrong');
          }
        });
      });

        
      $('#beatcity').change(function(){
        let selCity = $(this).val();
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ domain_route('company.admin.beat.fetchCityParties') }}",
          type: "GET",
          data:{
            "city": selCity,
            "beatId": ""
          },
          beforeSend:function(){
            showLoader()
          },
          success: function(data){
            $("#addNewBeat").find("#partyId").html('');
            $("#addNewBeat").find("#partyId").multiselect('destroy');
            let parties = data['parties'];
            $.each(parties, function(id, value){
              $("#addNewBeat").find("#partyId").append(`<option value=${value['id']}>${value['company_name']}</option>`);
            });
            $("#addNewBeat").find("#partyId").multiselect('reload');

            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').attr('checked',false);
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').prop('disabled',false);
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').parent().css('cursor','pointer');
            $('#beats-detail').find('#ms-list-1 input[type="checkbox"]').parent().css('color','#333');
            for(let count =0 ; count < data['assignedParties'].length; count++){
              $('#beats-detail').find('#ms-list-1 input[value="'+data['assignedParties'][count]+'"]').attr('checked','checked');
              $('#beats-detail').find('#ms-list-1 input[value="'+data['assignedParties'][count]+'"]').prop('disabled',true);
              $('#beats-detail').find('#ms-list-1 input[value="'+data['assignedParties'][count]+'"]').parent().css('background-color','#efefef');
              let label_id = $('#beats-detail').find('#ms-list-1 input[value="'+data['assignedParties'][count]+'"]').attr("id");
              $('label[for="'+label_id+'"]').css('color','gray');
              $('label[for="'+label_id+'"]').css('cursor','not-allowed');
            }
            $('#partyId').multiselect('refresh');
            $('#partyId').multiselect({
              columns: 1,
              placeholder: 'Select party',
              search: true,
              selectAll: true,
            });
            checkAssignParties();
          },
          complete:function(){
            hideLoader()
          },          
        })
      });

      $('#edit_beatcity').change(function(){
    let selCity = $(this).val();
    let beatId = $('#updateBeatSettings').find('#editbeat_id').val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ domain_route('company.admin.beat.fetchCityParties') }}",
      type: "GET",
      data:{
        "city": selCity,
        "beatId": beatId,
      },
      success: function(data){
        $("#updateBeatSettings").find("#assignPartyId").html('');
        $("#updateBeatSettings").find("#assignPartyId").multiselect('destroy');
        let parties = data['parties'];
        let assignedPartiesNotInThisBeat = data['assignedParties'];
        let beatParties = data['beatParties'];
        $.each(parties, function(id, value){
          if($.inArray(value['id'].toString(), beatParties)>=0){
            $("#updateBeatSettings").find("#assignPartyId").append(`<option value=${value['id']} selected>${value['company_name']}</option>`);
          }else{
            $("#updateBeatSettings").find("#assignPartyId").append(`<option value=${value['id']}>${value['company_name']}</option>`);

          }
        });
        $("#updateBeatSettings").find("#assignPartyId").multiselect('reload');
        $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').attr('checked',false);
        $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').prop('disabled',false);
        $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').parent().css('cursor','pointer');
        $('#updateBeatSettings').find('#ms-list-2 input[type="checkbox"]').parent().css('color','#333');
        for(let count =0 ; count < parties.length; count++){
          if($.inArray(parties[count]["id"], beatParties) >= 0){
            $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').attr('checked','checked');
            $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').parent().css('background-color','#efefef');
            $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').parent().css('color','rgb(128, 0, 128)');
          }else if($.inArray(parties[count]["id"], assignedPartiesNotInThisBeat) >= 0){
            $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').prop('disabled',true);
            let label_id = $('#updateBeatSettings').find('#ms-list-2 input[value="'+parties[count]["id"]+'"]').attr("id");
            $('label[for="'+label_id+'"]').css('color','gray');
            $('label[for="'+label_id+'"]').css('cursor','not-allowed');
          }
        }

        $("#updateBeatSettings").find("#assignPartyId").val(beatParties);

      },          
    })
  });

  @endif
  
  @if(config('settings.leaves')==1)          
  // <!-- Leave Type -->
  
    $('#tblleavetype').on('click','.edit-leavetype',function(event){
          event.preventDefault();
          $('#editLeaveType').modal('show');
          var name = $(this).attr('data-name');
          $('#editleavetypename').val(name);
          var url = $(this).attr('data-url');
          $('#formEditLeavetype').attr('action',url);
      });

      $('#tblleavetype').on('click','.delete-leavetype',function(event){
          event.preventDefault();
          $('#modalDeleteLeaveType').modal('show');
          var url = $(this).attr('data-url');
          $('#frmDelLeaveType').attr('action',url);
      });

      $('#addNewleaveType').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.addNewleaveType').attr('disabled',true);
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#addNewleaveType')[0].reset();
                    $('#tbl_leavetypes').empty();
                    $('#tbl_leavetypes').html(data['leavetypes']);
                    alert('Leave Type Added Successfully');
                  }else{
                    alert('Leave Type already exists.')
                  }
                  $('.addNewleaveType').attr('disabled',false);
              },
              error:function(){
                $('.addNewleaveType').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });
      });

      $('#formEditLeavetype').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.updateLeaveType').attr('disabled',true);
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#tbl_leavetypes').empty();
                    $('#tbl_leavetypes').html(data['leaveTypes']);
                    $('.updateLeaveType').attr('disabled',false);
                    $('#editLeaveType').modal('hide');  
                    alert("Leave Type Updated");                  
                  }else{
                    alert("Leave Type already exists");
                  }
                  $('.delete-button').removeAttr('disabled');
              },
            error:function(){
              $('.delete-button').attr('disabled',false);
              alert('Oops! Something went wrong...');
            }
          });
      });

      $('#frmDelLeaveType').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "POST",
            data: {
                '_token': '{{csrf_token()}}',
            },
            beforeSend:function(){
              $('.removeLeaveTypeKey').attr('disabled',true);
            },
            success: function (data) {
                if(data['result']==true){
                  alert("LeaveType Successfully Deleted");
                  $('#tbl_leavetypes').empty();
                  $('#tbl_leavetypes').html(data['leaveTypes']);
                }else{
                  alert("LeaveType can't be deleted since Leaves found under it.")
                }
                $('#modalDeleteLeaveType').modal('hide'); 
                $('.removeLeaveTypeKey').attr('disabled',false);                 
            },
            error:function(){
              $('.removeLeaveTypeKey').attr('disabled',false);
              alert('Oops! Something went wrong...');
            }
        });
      });

  
  @endif

  // <!-- Party Types -->
  @if(config('settings.party')==1)
  
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
  
  $('#tree1').treed();
  //Party Type Section
  $('#tree1').on('click','span', function(){
    $('#modalDeletePartyType').modal('show');
    $('#delPartyType').attr('action',$(this).attr('destroy-url'));
  });
  $('#tree1').on('click','a', function(){
    var superior_id = $(this).attr('superior-id');
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
    var url = "{{domain_route('company.admin.clientsettings.getpartytypes')}}";
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
        $.each(data, function (i, v) {
          if(v.id == superior_id){
            $('<option selected></option>').val(v.id).text(v.name).appendTo('#party_parent');
          }else{
            $('<option></option>').val(v.id).text(v.name).appendTo('#party_parent');
          }
        });
      }
    });
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
      type: "POST",
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
        $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
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
      type: "POST",
      data: {
        company_id: company_id,
        party_type: party_type,
        party_parent: party_parent,
        party_type_short_name: party_type_short_name,
        display_status: display_status,
      },
      beforeSend: function () {
        $("#update-party-type").attr("disabled", true);
      },
      success: function (data) {
        $("#modalEditPartyType").modal("hide");
        $("#tree1").html(data["tree"]);
        $("#tree1").treed();
        $("#select_party_types").empty();
        $("<option></option>")
          .text("Select Party Type")
          .appendTo("#select_party_types");
        $.each(data["partytypes"], function (i, v) {
          $("<option></option>")
            .val(v.id)
            .text(v.name)
            .appendTo("#select_party_types");
        });
        alert("Party type has been updated successfully.");
        $("#update-party-type").attr("disabled", false);
      },
      error: function(error){
        if(error.status == 422) {
          if(error.responseJSON.two_party_level_exceeds) alert(error.responseJSON.two_party_level_exceeds);
          if(error.responseJSON.errors){
            if(error.responseJSON.errors.party_type){
              alert(error.responseJSON.errors.party_type[0])
            }
          }
        } 
        $("#update-party-type").attr("disabled", false);
      }
    });
  });
  $('#delPartyType').on('submit',function(event){
    event.preventDefault();
    var url = $(this).attr('action');
    var company_id = $('#del_company_id').val();
    $.ajax({
      url: url,
      type: "POST",
      data: {
        company_id: company_id,
      },
      beforeSend: function () {
        $("#del-party-type").attr("disabled", true);
      },
      success: function (data) {
        $("#modalDeletePartyType").modal("hide");
        if (data.status == false) {
          alert(data.message);
        } else {
          $("#tree1").html(data["tree"]);
          $("#tree1").treed();
          $("#select_party_types").empty();
          $("<option></option>")
            .text("Select Party Type")
            .appendTo("#select_party_types");
          $.each(data["partytypes"], function (i, v) {
            $("<option></option>")
              .val(v.id)
              .text(v.name)
              .appendTo("#select_party_types");
          });
          $("#del-party-type").attr("disabled", false);
          alert("Party Type Deleted Successfully");
        }
      },
      error: function(){
        $("#del-party-type").attr("disabled", false);
      }
    });
  });
  
  
  $('#frmAddNewPartyType').on('submit',function(event){
    event.preventDefault();
    var url = $(this).attr('action');
    $.ajax({
      url: url,
      type: "POST",
      data:new FormData(this),
      contentType: false,
      cache: false,
      processData: false,
      beforeSend:function(){
        $('#btnAddParty').attr('disabled','disabled');
        $('.refreshing').removeClass('hide');
      },
      success: function (data) {
        $('#frmAddNewPartyType')[0].reset();
        $('.refreshing').addClass('hide');
        $('#btnAddParty').removeAttr('disabled');
        if(data.status == false){
          alert(data.message);
        }else{
          $('#tree1').html(data['tree']);
          $('#tree1').treed();
          $('#select_party_types').empty();
          $('<option></option>').text('Select Party Type').appendTo('#select_party_types');
          $.each(data['partytypes'],function(i,v){
            $('<option></option>').val(v.id).text(v.name).appendTo('#select_party_types');
          });
          alert("Party type has been added successfully. Please setup roles and permissions for this party type.");
        }
      },
      error:function(error){
        $('.refreshing').addClass('hide');
        $('#btnAddParty').removeAttr('disabled');
        if(error.status == 422) {
          if(error.responseJSON.two_party_level_exceeds) alert(error.responseJSON.two_party_level_exceeds);

          if(error.responseJSON.errors){
            if(error.responseJSON.errors.name){
              alert(error.responseJSON.errors.name[0])
            }
          }
        }      
      }
    });
  });
  
  @endif

  // <!-- Designation -->
  
    $('#AddNewDesignation').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
                  'data':data,
              },
              beforeSend:function(){
                $('.addDesignation').attr("disabled","disabled");
              },
              success: function (data) {
                  if(data['result']==true){
                    $('#tbody_designation').empty();
                    $('#tbody_designation').html(data['designations']);
                    $('#AddNewDesignation')[0].reset();
                    $('#ajaxDesignationlist').empty();
                    $.each(data.alldesignations,function(k,v){
                      $('#ajaxDesignationlist').append('<option value="'+v.id+'">'+v.name+'</option>')
                    });
                    alert('Designation Added Successfully');
                  }else{
                    alert(data['message']);                
                  }
                  $('.addDesignation').attr("disabled",false );
              },
              error:function(){
                $('.addDesignation').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });

      });

      $('#tbldesignation').on('click','.deleteBtnDesignation',function(event){
          event.preventDefault();
          $('#deleteDesignation').modal('show');
          var url = $(this).attr('data-url');
          $("#frmRemoveDesignation").attr("action", url);
      });

      $('#tbldesignation').on('click','.editBtnDesignation',function(event){
          event.preventDefault();
          $('#editDesignation').modal('show');
          var url = $(this).attr('data-url');
          $("#frmEditDesignation").attr("action", url);
          $('#designation_name').val($(this).attr('data-name'));
      });

      $('#frmEditDesignation').on('submit',function(e){
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
                $('.editDesignationKey').attr('disabled',true);
              },
              success: function (data) {
                  alert(data['result']);
                  $('#editDesignation').modal('hide');
                  $('#tbody_designation').empty();
                  $('#tbody_designation').html(data['designations']);
                  $('#ajaxDesignationlist').empty();                
                  $.each(data.alldesignations,function(k,v){
                    $('#ajaxDesignationlist').append('<option value="'+v.id+'">'+v.name+'</option>');
                  });
                  $('.editDesignationKey').attr('disabled',false);
              },
              error:function(){
                $('.removeDesignationKey').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });

      });

      $('#frmRemoveDesignation').on('submit',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: {
                  '_token': '{{csrf_token()}}',
              },
              beforeSend:function(){
                $('.removeDesignationKey').attr('disabled',true);
              },
              success: function (data) {
                  alert(data['result']);
                  $('#deleteDesignation').modal('hide');
                  $('#tbody_designation').empty();
                  $('#tbody_designation').html(data['designations']);
                  $('#ajaxDesignationlist').empty();                
                  $.each(data.alldesignations,function(k,v){
                    $('#ajaxDesignationlist').append('<option value="'+v.id+'">'+v.name+'</option>');
                  });
                  $('.removeDesignationKey').attr('disabled',false);
              },
              error:function(){
                $('.removeDesignationKey').attr('disabled',false);
                alert('Oops! Something went wrong...');
              }
          });

      });

      
  

  // <!-- Return Reasons -->
  @if(config('settings.returns')==1)
  
    $(function () {
      initializeDT();
      $('#retTableView').on('click','.rowEditReturnReason', function () {
          $('#editreturn_reason_id').val($(this).attr('returnreason-id'));
          $('#editreturn_reason_name').val($(this).attr('returnreason-name'));
          $('#EditReturnReason').modal('show');
      });

      $('#retTableView').on('click','.rowDeleteReturnReason', function () {
          $('#delete_return_reason_id').val($(this).attr('returnreason-id'));
          $('#delete_return_reason_name').val($(this).attr('returnreason-name'));
          $('#DeleteReturnReason').modal('show');
          $('#del_title').html($(this).attr('returnreason-name'));
      });
      var returnTable;
      function initializeDT(){
        returnTable = $('#returnreason').DataTable({
          "columnDefs": [
            { "orderable": false, "targets": [-1] } // Applies the option to all columns
            ],
            "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
              "<'row'<'col-xs-6'><'col-xs-6'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
            buttons: [
            ]

        });
      }

      returnTable.buttons().container().appendTo('#returnreasonsexports');

      $('#addNewReturnReason').on('submit', function (event) {
          event.preventDefault();
          var currentElement = $(this);
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: currentElement.attr('action'),
              type: "POST",
              data: new FormData(this),
              processData: false,
              contentType: false,
              cache: false,
              beforeSend:function(){
                $('#addkey').attr('disabled',true);
              },
              success: function (data) {
                  if(data['result']==true){
                    alert('Return Reason created Successfully.');
                    returnTable.clear().draw();
                    $('#AddReturnReason').modal('hide');
                    $('#addNewReturnReason').trigger('reset');
                    let reasonData = data.reasonData;
                    for (i = 0; i < data['count']; i++) { 
                      var editurl="{{ domain_route('company.admin.returnreason.edit',['id']) }}" ;
                      editurl=editurl.replace('id', data['returnReason'][i]['id']); 
                      var delurl="{{ domain_route('company.admin.returnreason.destroy',['id']) }}" ; delurl=delurl.replace('id',data['returnReason'][i]['id']); 
                      var submiturl="$(" + "'#" + data['returnReason'][i]['id'] + "').submit();" ;
                      if(reasonData.includes(String(data['returnReason'][i]['id']))){
                        returnTable.row.add([ 
                          i + 1,
                        data['returnReason'][i]['name'], 
                        '<a  class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="' +
                        data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']
                        + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>' , ]).draw();
                      }else{
                        returnTable.row.add([ 
                          i + 1,
                        data['returnReason'][i]['name'], 
                        '<a  class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="' +
                        data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']
                        + '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteReturnReason" returnreason-id="'
                        + data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']
                        + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>' , ]).draw();
                      }
                    }
                  }else{
                    alert('Sorry! Return Reason already exists.');
                  }
                  $('#addkey').attr('disabled',false);
                  $('#AddReturnReasons').modal('hide');

              },
          });

      });

      $('#editReturnReason').on('submit', function (event) {
          event.preventDefault();
          var edit_id = $('#edit_id').val();
          var url = "{{domain_route('company.admin.returnreason.update')}}";
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              type: "POST",
              data: new FormData(this),
              processData: false,
              contentType: false,
              cache: false,
              beforeSend:function(){
                $('#updatekey').attr('disabled',true);
              },
              success: function (data) {
                if(data['result']==true){
                  alert('Updated Successfully');
                  returnTable.clear().draw();
                  $('#EditReturnReason').modal('hide');
                  $('#editExistingActivityType').trigger('reset');
                  
                  for (i = 0; i < data['count']; i++) { 
                    var editurl="{{ domain_route('company.admin.returnreason.edit',['id']) }}" ;
                    editurl=editurl.replace('id', data['returnReason'][i]['id']); 
                    var delurl="{{ domain_route('company.admin.returnreason.destroy',['id']) }}" ; delurl=delurl.replace('id',data['returnReason'][i]['id']); 
                    var submiturl="$(" + "'#" + data['returnReason'][i]['id'] + "').submit();" ;
                    let reasonData = data.reasonData;
                    if(reasonData.includes(String(data['returnReason'][i]['id']))){
                      returnTable.row.add([
                      i + 1,
                      data['returnReason'][i]['name'],'<a class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="' +data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']+ '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>' , ]).draw();
                    }else{
                      returnTable.row.add([
                      i + 1,
                      data['returnReason'][i]['name'], 
                      '<a  class="btn btn-warning btn-sm rowEditReturnReason" returnreason-id="' +data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name']+ '" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-sm delete rowDeleteReturnReason" returnreason-id="'+ data['returnReason'][i]['id'] + '" returnreason-name="' + data['returnReason'][i]['name'] + '" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>' , ]).draw();
                    }
                  }
                }else{
                  alert('Sorry! Activity Type already exists.')
                }
                $('#updatekey').attr('disabled',false);
                $('#EditReturnReason').modal('hide');
              },
          });
      });    
      $('#deleteReturnReason').on('submit', function (event) {
        event.preventDefault();
        var edit_id = $('#delete_return_reason_id').val();
        var url = "{{domain_route('company.admin.returnreason.delete')}}";
        $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: url,
          type: "POST",
          data: new FormData(this),
          processData: false,
          contentType: false,
          cache: false,
          beforeSend:function(){
            $('#delreturnreasonkey').attr('disabled',true);
          },
          success: function (data) {
            $('#delreturnreasonkey').attr('disabled',false);
            $('#DeleteReturnReason').modal('hide');
            if(data==false){
              alert('Failed Deleting');
            }else{
              returnTable.destroy();
              $('#retTableView').html('');
              $('#retTableView').html(data);
              initializeDT();
              alert("Return Reason deleted successfully");
            }
          }
        });
      });
    });


  //var customfieldtable=$('#party_custom_fields').DataTable();
  function editField(object, element) {
    $("div[id^='innerfield-modal']").each(function (i, obj) {
      var temp = $(obj).find('h5').html();
      if (temp == 'Multiple options') {
        temp = "Multiple options";
      } else if (temp == 'Contact') {
                      temp = 'Person';
                  }
      if (temp == object.type) {
        $(obj).modal('show');
        $(obj).find('input').val(object.title);
        $(obj).find('textarea').val('');
        if (object.type == "Single option" || object.type == "Multiple options") {
          var new_html = '';
        // alert(object.options);
          JSON.parse(object.options).forEach(function (item) {
              new_html += (item) + '\n';
          });
          $(obj).find('textarea').val(new_html);
        }

        $(obj).find('form').on('submit', function (e) {
          e.preventDefault();
          var dataid = object.id;
          var url = "{{domain_route('company.admin.customfields.custom_field')}}";
          data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            title: $(this).find('input').val(),
            id:dataid
          };
          if (object.type == "Single option" || object.type == "Multiple options") {
            var avalue = $(this).find('textarea').val();
          var newVal = avalue.replace(/^\s*[\r\n]/gm, '');
          var options = newVal.split(/\n/);
            //var options=$(this).find('textarea').val().split(/\n/);
          //s alert(options);
            data = {
              _token: $('meta[name="csrf-token"]').attr('content'),
              title: $(this).find('input').val(),
              id:dataid,
              options: options
            };
          }
          $.post(url, data, function (data) {

            if(data.errors)
            {
              $('.alert-danger').html('');

              $.each(data.errors, function(key, value){
                $('.alert-danger').show();
                $('.alert-danger').append('<li>'+value+'</li>');
              });
            }else{
            //alert(response);
            $('.alert-danger').hide();
            $('.modal').modal('hide');
            //customfieldtable.reload();
            $('#party_custom_fields').DataTable().destroy();
            $('#party_custom_fields').find('tbody').first().html(data);
                  initializeDataTable();
                }
          });
        });
      }
    });         
  };

  
  @endif
  window.addEventListener('load', function () {
    @if(config('settings.party')==1)
      checkAssignParties()
    @endif
    hideLoader()
  })
  </script>
@endsection