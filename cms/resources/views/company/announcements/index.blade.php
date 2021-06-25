@extends('layouts.company')
@section('title', 'Announcements')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<style>
  .box-loader{
    opacity: 0.5;
  }

  .close{
    font-size: 30px;
    color: #080808;
    opacity: 1;
  }
  .hide_column {
      display: none;
  }
  .round {
      position: relative;
      width: 15px;
  }
  .round label {
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 50%;
      cursor: pointer;
      height: 15px;
      left: 0;
      position: absolute;
      top: 3px;
      width: 28px;
  }
  .round label:after {
      border: 2px solid #fff;
      border-top: none;
      border-right: none;
      content: "";
      height: 6px;
      left: 0px;
      opacity: 0;
      position: absolute;
      top: 3px;
      transform: rotate(-45deg);
      width: 12px;
  }
  .round input{
      height: 10px;
  }
  .round input[type="checkbox"] {
      visibility: hidden;
  }
  .round input[type="checkbox"]:checked + label {
      background-color: #66bb6a;
      border-color: #66bb6a;
  }
  .round input[type="checkbox"]:checked + label:after {
      opacity: 1;
  }
  .pad-left{
      padding-left: 0px;
  }

</style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      @if (\Session::has('success'))
      <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
      </div><br/>
      @endif
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Announcement List</h3>
          @if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin())
          <a href="{{ domain_route('company.admin.announcement.create') }}" class="btn btn-primary pull-right" style="margin-left: 5px;">
            <i class="fa fa-plus"></i> Create New
          </a>
          @endif
          <span id="announcementexports" class="pull-right"></span>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-6"></div>
                  <div class="col-xs-6">
                    @if(config('settings.ncal')==0)
                    <div id="reportrange" name="reportrange" class="reportrange" style="margin-top: 10px; ">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                  </div> 
                  <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                  <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                  @else
                  <div class="input-group hidden" id="nepCalDiv" style="margin-top: 10px;">
                    <input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date" autocomplete="off"/>
                    <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                    <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                    <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date" autocomplete="off"/>
                    <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                    <button id="filterTable" style="color:#0b7676!important;" hidden><i class="fa fa-filter" aria-hidden="true"></i></button>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-3"></div>
        </div>
        <div id="loader1" hidden>
          <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        </div>
        <div id="mainBox">
          <table id="announcement" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Created Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
</section>

<!-- Modal -->
<div class="modal fade" id="announcement-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="announcement_title"></h4>
      </div>
      <div class="modal-body">
        <strong><i class="fa fa-book margin-r-5"></i> Description:</strong>
        <p id="announcement_detail" class="text-muted"></p>
        <div class="table-responsive">
          <table id="announcement" class="table table-bordered table-striped">
            <colgroup>
              <col class="col-xs-2">
              <col class="col-xs-7">
            </colgroup>
            <tbody>
              <tr>
                <th scope="row"> Created On</th>
                <td id="announcement_createdOn"></td>
              </tr>
              <tr>
                <th scope="row"> Status</th>
                <td id="announcement_status">
                  <span class="label label-success">Active</span>
                </td>
              </tr>
              <tr>
                <th scope="row"> Announcement forwarded to</th>
                <td id="announcement_employees"> 
                  <span class=""></span><br>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-warning" data-dismiss="modal">
          <span class="glyphicon glyphicon-remove"></span> Close
        </button> --}}
      </div>
    </div>
  </div>
</div>
<form method="post" action="{{domain_route('company.admin.announcement.customPdfExport')}}" class="pdf-export-form hidden"
  id="pdf-generate">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="columns" class="columns" id="columns">
  <input type="text" name="properties" class="properties" id="properties">
  <button type="submit" id="genrate-pdf">Generate PDF</button>
</form>
@endsection

@section('scripts')
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{asset('assets/plugins/datatableButtons/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/jszip.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatableButtons/buttons.colVis.min.js')}}"></script>

@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
<script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endif
<script>
  $(function () {

    $('#delete').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var mid = button.data('mid')
      var url = button.data('url');
      $(".remove-record-model").attr("action", url);
      var modal = $(this)
      modal.find('.modal-body #m_id').val(mid);
    });

    function initializeDT(startD, endD){
            var table = $('#announcement').DataTable({
            "order": [[ 3, "desc" ]],
            "processing": true,
            "serverSide": true,
            "ajax":{
               "url": "{{ domain_route('company.admin.announcement.ajaxTable') }}",
               "dataType": "json",
               "type": "POST",
               "data":{ 
                _token: "{{csrf_token()}}",
                startDate: startD,
                endDate: endD,
              },            
              beforeSend:function(){
                $('#mainBox').addClass('box-loader');
                $('#loader1').removeAttr('hidden');
              },
              error:function(){
                $('#mainBox').removeClass('box-loader');
                $('#loader1').attr('hidden', 'hidden');
              },
              complete:function(){
                $('#mainBox').removeClass('box-loader');
                $('#loader1').attr('hidden', 'hidden');
              },
             },
            "columnDefs": [ {
              "targets": [-1],
              "orderable": false,},],
            "columns": [
              { "data": "id" },
              { "data": "title" },
              { "data": "description" },
              { "data": "created_at" },
              { "data": "status" },
              { "data": "action" },
            ],  
            "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
            "<'row'<'col-xs-6'><'col-xs-6'>>" +
            "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>",
            buttons: [
                {
                    extend: 'colvis',
                    order: 'alpha',
                    className: 'dropbtn',
                    columns:[0,1,2,3,4],
                    text: '<i class="fa fa-cog"></i>  <i class="fa fa-caret-down"></i>',
                    columnText: function ( dt, idx, title ) {
                        return "<div class='row'><div class='col-xs-3'><div class='round'><input id='col"+idx+"' class='check' type='checkbox'><label for='col"+idx+"'></label></div></div><div class='col-xs-9 pad-left'>"+title+"</div></div>";
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: 'Announcement List',
                    exportOptions: {
                      columns: ':visible:not(:last-child)'
                    },
                    footer: true,
                    action: function ( e, dt, node, config ) {
                      newExportAction( e, dt, node, config );
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Announcement List',
                    exportOptions: {
                      columns: ':visible:not(:last-child)'
                    },
                    orientation:'landscape',
                    footer: true,
                    action: function ( e, dt, node, config ) {
                      newExportAction( e, dt, node, config );
                    }
                },
                {
                    extend: 'print',
                    title: 'Announcement List',
                    exportOptions: {
                      columns: ':visible:not(:last-child)'
                    },
                    footer: true,
                    action: function ( e, dt, node, config ) {
                      newExportAction( e, dt, node, config );
                    }
                },
            ],
            });
            table.buttons().container()
            .appendTo('#announcementexports');

            var oldExportAction = function (self, e, dt, button, config) {
              if (button[0].className.indexOf('buttons-excel') >= 0) {
                if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                } else {
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                }
              } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
                } else {
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                }
              } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
              }
            };

            var newExportAction = function (e, dt, button, config) {
              var self = this;
              var oldStart = dt.settings()[0]._iDisplayStart;
              dt.one('preXhr', function (e, s, data) {
                $('#mainBox').addClass('box-loader');
                $('#loader1').removeAttr('hidden');
                data.start = 0;
                data.length = {{$announcementCount}};
                dt.one('preDraw', function (e, settings) {
                  if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                    var columnsArray = [];
                    var visibleColumns = settings.aoColumns.map(setting => {
                                            if(setting.bVisible){
                                              columnsArray.push(setting.sTitle.replace(/<[^>]*>?/gm, ''))
                                            } 
                                          })    
                    columnsArray.pop("Action")
                    // columnsArray.push("S.No.", "Party Name", "Salesman", "Date", "Remark");
                    var columns = JSON.stringify(columnsArray);
                    $.each(settings.json.data, function(key, htmlContent){
                      settings.json.data[key].id = key+1;
                      // debugger;
                      // settings.json.data[key].description = $(settings.json.data[key].description)[0].textContent;
                      settings.json.data[key].status = $(settings.json.data[key].status)[0].textContent;
                    });
                    customExportAction(config, settings, columns);
                  }else{
                    oldExportAction(self, e, dt, button, config);
                  }
                  // oldExportAction(self, e, dt, button, config);
                  dt.one('preXhr', function (e, s, data) {
                      settings._iDisplayStart = oldStart;
                      data.start = oldStart;
                      $('#mainBox').removeClass('box-loader');
                      $('#loader1').attr('hidden', 'hidden');
                  });
                  setTimeout(dt.ajax.reload, 0);
                  return false;
                });
              });
              dt.ajax.reload();
            }

            function customExportAction(config, settings, cols){
              $('#exportedData').val(JSON.stringify(settings.json));
              $('#pageTitle').val(config.title);
              $('#columns').val(cols);
              var propertiesArray = [];
              var visibleColumns = settings.aoColumns.map(setting => {
                                    if(setting.bVisible) propertiesArray.push(setting.data)
                                  })
              propertiesArray.pop("action")
              // propertiesArray.push("id","company_name", "employee_name", "date", "remark");
              var properties = JSON.stringify(propertiesArray);
              $('#properties').val(properties);
              $('#pdf-generate').submit();
            }

          }
    var start = moment().subtract(29, 'days').format('YYYY-MM-DD');
    var end = moment().format('YYYY-MM-DD');
    initializeDT(start, end);

    @if(config('settings.ncal')==0)

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
      $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
      $('#startdate').val(start.format('MMMM D, YYYY'));
      $('#enddate').val(end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
      startDate: start,
      endDate: end,
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
    }, cb);

    cb(start, end);

    $.fn.dataTable.ext.search.push(
      function (settings, data, dataIndex) {
        var start2 = $('#reportrange').data('daterangepicker').startDate;
        var end2 = $('#reportrange').data('daterangepicker').endDate;
        var start_date = Date.parse(start2.format('MMMM D, YYYY'));
        var end_date = Date.parse(end2.format('MMMM D, YYYY'));
        var create_date = Date.parse(data[3]); // use data for the age column
        if (create_date >= start_date && create_date <= end_date) {
          return true;
        }
        return false;
      }
    );

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
      var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
      $('#start_edate').val(start);
      $('#end_edate').val(end);
      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      if(start != '' || end != '')
      {
        $('#announcement').DataTable().destroy();
        initializeDT(start, end);
      }
    });

    @else
    var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
    var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
    $('#start_ndate').val(lastmonthdate);
    $('#end_ndate').val(ntoday);
    $('#start_edate').val(BS2AD(lastmonthdate));
    $('#end_edate').val(BS2AD(ntoday));
    $('#nepCalDiv').removeClass('hidden');


    $('#start_ndate').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange:function(){
        $('#start_edate').val(BS2AD($('#start_ndate').val()));
        if($('#start_ndate').val()>$('#end_ndate').val()){
          $('#end_ndate').val($('#start_ndate').val());
          $('#end_edate').val(BS2AD($('#start_ndate').val()));
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        $('#announcement').DataTable().destroy();
        initializeDT(start, end);
      }
    });
    $('#end_ndate').nepaliDatePicker({
      onChange:function(){
        $('#end_edate').val(BS2AD($('#end_ndate').val()));
        if($('#end_ndate').val()<$('#start_ndate').val()){
          $('#start_ndate').val($('#end_ndate').val());
          $('#start_edate').val(BS2AD($('#end_ndate').val()));
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        $('#announcement').DataTable().destroy();
        initializeDT(start, end);;
      }
    });
    $.fn.dataTable.ext.search.push(
      function (settings, data, dataIndex) {
        var start2 = $('#start_edate').val();
        var end2 = $('#end_edate').val();
        var create_date =data[3]; 
        if (create_date >= start2 && create_date <= end2) {
          return true;
        }
        return false;
      }
      );
    @endif
  });

  //responsive 
  $('#reportrange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("announcedateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("announcedateposition");
    }
    else {   
      $(".daterangepicker").removeClass("announcedateposition");
    }
  });

  $(document).on('click', '.edit-modal', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var url = '{{domain_route('company.admin.announcement.detail')}}';
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url:url,
      type: "POST",
      data: {
        '_token': '{{csrf_token()}}',
        'id': id,
      },
      success: function (data) {
        if(data['result']==true){
          $('#announcement-modal').modal('show');
          $('#announcement_title').html(data['announcement'].title);
          $('#announcement_createdOn').html(data['announcement'].date);
          if(data['announcement'].status==1){
            $('#announcement_status').html('<span class="label label-success">Active</span>')
          }else{
            $('#announcement_status').html('<span class="label label-warning">Inctive</span>')
          }
          $('#announcement_detail').html(data['announcement'].description);
          $('#announcement_employees').empty();
          $('#announcement_employees').append
          $.each(data['announcement'].employees,function(k,v){
            $('#announcement_employees').append('<span>'+v.name+'</span><br>')
          });
        }
      }
    });
  });

  $(document).on('click','.buttons-columnVisibility',function(){
      if($(this).hasClass('active')){
          $(this).find('input').first().prop('checked',true);
          // console.log($(this).find('input').first().prop('checked'));
      }else{
          $(this).find('input').first().prop('checked',false);
          // console.log($(this).find('input').first().prop('checked'));
      }
  });
  $(document).on('click','.buttons-colvis',function(e){
      var filterBox = $('.dt-button-collection');
      filterBox.find('li').each(function(k,v){
          if($(v).hasClass('active')){
              $(v).find('input').first().prop('checked',true);
          }else{
              $(v).find('input').first().prop('checked',false);
          }
      });
  });
</script>

@endsection