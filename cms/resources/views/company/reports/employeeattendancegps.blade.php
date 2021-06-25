@extends('layouts.company')
@section('title', 'Salesman GPS Path')

@section('stylesheets')
  <link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
  @if(config('settings.ncal')==1)
  <link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
  @else
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet"
    href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  @endif
  <link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
   <style>
  #devmap {
        width:100%;
        min-height:400px;
}
.modal-footer {
  text-align: left;
}
  </style>
@endsection

@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Salesman GPS Path</h3>
          <span id="dailyempreportexports" class="pull-right"></span>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">
                <div class="select-2-sec">
                  <div class="col-xs-4">
                    <div style="margin-top:10px; " id="empfilter"></div>
                  </div>
                  @if(config('settings.ncal')==0)
                    <div class="col-xs-6">
                      <div id="reportrange" name="reportrange" class="reportrange hidden" style="margin-top: 10px;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                      </div>
                      <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden/>
                      <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                    </div>
                  @else
                    <div class="col-xs-6">
                      <div class="row" style="margin-top: 10px;">
                        <div class="input-group hidden" id="nepCalDiv">
                          <input id="start_ndate" class="form-control" type="text" name="start_ndate"
                            placeholder="Start Date" autocomplete="off" />
                          <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                          <span class="input-group-addon" aria-readonly="true"><i
                              class="glyphicon glyphicon-calendar"></i></span>
                          <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                            autocomplete="off" />
                          <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-xs-3"></div>
          </div>
          <div class="" id="mainBox">
            <table id="dailyempreport" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Employee Name</th>
                  <th>Date</th>
                  <th>Processed Path</th>
                  <th>Raw Path</th>
                  <th>View Details</th>
                </tr>
              </thead>
              <div id="loader1" hidden>
                <img src="{{asset('assets/dist/img/loader2.gif')}}" />
              </div>
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

<div class="modal modal-default fade" id="workhours" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div id="hourdetails">
        <div class='modal-header'>
          <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span
              aria-hidden='true'>&times;</span></button>
          <h4 class='modal-title text-center' id='myModalLabel'>CheckIN-CheckOut Details</h4>
          <p class='text-center' id="emp_name_date"></p>
          <p class='text-center'><b id="total_hr_string"></b></p>
          <p class='text-center'><b id="total_distance_string"></b></p>
        </div>
        <div class='modal-body'>
          <div class='box box-info'>
            <div class='box-body'>
              <div class='table-responsive'>
                <table class='table no-margin'>
                  <thead>
                    <tr>
                      <th>CheckIn Time</th>
                      <th>CheckOut Time</th>
                      <th>Distance travelled</th>
                    </tr>
                  </thead>
                  <tbody id="cicolist"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-success" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="disttravel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div id="traveldetails"></div>

      <div class="modal-footer">
        {{-- <button type="button" class="btn btn-success" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Location Map: Mr. DHEENADHAYALAN SELVARAJ 03-04-2018</h4>
      </div>
      <div class="modal-body">
        <div id="devmap"></div>

      </div>
      <div class="modal-footer">
        <p id="total-distance" style="float:left;font-weight: bold;"></p>
        {{-- <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button> --}}
      </div>
    </div>
  </div>
</div>
<form method="post" action="{{domain_route('company.admin.reports.customPdfExport')}}" class="pdf-export-form"
  id="pdf-generate" style="display:none;">
  {{csrf_field()}}
  <input type="text" name="exportedData" class="exportedData" id="exportedData">
  <input type="text" name="pageTitle" class="pageTitle" id="pageTitle">
  <input type="text" name="reportName" class="reportName" id="reportName">
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
  <script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  @if(config('settings.ncal')==1)
  <script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
  @else
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif

  <script type="text/javascript">
    $(document).ready(function () {
      $('.select2').select2();
      ajaxUrls = {
        getLocation: "{{ domain_route('company.admin.reports.location') }}",
        getFileLocation: "{{ domain_route('company.admin.reports.filelocation') }}",
        getRawLocation: "{{ domain_route('company.admin.reports.getrawlocation') }}",
        getWorkedHourDetails: "{{ domain_route('company.admin.reports.gethoursdetails') }}",
      };
      $('[data-toggle="tooltip"]').tooltip();

      @if(config('settings.ncal')==0)
        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
          $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
          $('#startdate').val(start.format('MMMM D, YYYY'));
          $('#enddate').val(end.format('MMMM D, YYYY'));
          $('#start_edate').val(start.format('Y-MM-DD'));
          $('#end_edate').val(end.format('Y-MM-DD'));
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

        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
          var start = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var end = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          $('#start_edate').val(start);
          $('#end_edate').val(end);
          var empVal = $('.employee_filters').find('option:selected').val();
          if(empVal=="null"){
            empVal = null;
          }
          
          var startD = $('#start_edate').val();
          var endD = $('#end_edate').val();
          if(startD != '' || endD != ''){
            $('#dailyempreport').DataTable().destroy();
            initializeDT(empVal, start, end);
          }
        });

        $('#reportrange').removeClass('hidden');
      @else
        var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
        var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
        $('#start_ndate').val(lastmonthdate);
        $('#end_ndate').val(ntoday);
        $('#start_edate').val(BS2AD($('#start_ndate').val()));
        $('#end_edate').val(BS2AD($('#end_ndate').val()));
        $('#nepCalDiv').removeClass('hidden');
        $('#start_ndate').nepaliDatePicker({
          ndpEnglishInput: 'englishDate',
          onChange:function(){
            $('#start_edate').val(BS2AD($('#start_ndate').val()));
            if($('#start_ndate').val()>$('#end_ndate').val()){
              $('#end_ndate').val($('#start_ndate').val());
              $('#end_edate').val(BS2AD($('#start_ndate').val()));
            }
            if($('#start_ndate').val()>$('#end_ndate').val()){
              $('#end_ndate').val($('#start_ndate').val());
              $('#end_edate').val(BS2AD($('#start_ndate').val()));
            }
            var empVal = $('.employee_filters').find('option:selected').val();
            if(empVal=="null"){
              empVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(end==""){
              end = start;
            }
            if(start != '' || end != '')
            {
              $('#dailyempreport').DataTable().destroy();
              initializeDT(empVal, start, end);
            }
          }
        });
        $('#end_ndate').nepaliDatePicker({
          onChange:function(){
            $('#end_edate').val(BS2AD($('#end_ndate').val()));
            if($('#end_ndate').val()<$('#start_ndate').val()){
              $('#start_ndate').val($('#end_ndate').val());
              $('#start_edate').val(BS2AD($('#end_ndate').val()));
            }
            var empVal = $('.employee_filters').find('option:selected').val();
            if(empVal=="null"){
              empVal = null;
            }
            var start = $('#start_edate').val();
            var end = $('#end_edate').val();
            if(end==""){
              end = start;
            }
            if(start != '' || end != '')
            {
              $('#dailyempreport').DataTable().destroy();
              initializeDT(empVal, start, end);
            }
          }
        });
      @endif

      var start = $('#start_edate').val();
      var end = $('#end_edate').val();
      // Load Data Table on ready 
      initializeDT(null, start, end);
      // $('body').on("click", ".hourdetail2",function () {
      //   var currentEl = $(this);
      //   hourDetails(currentEl);
      // });

      var empSelect = "<select sname='employee' id='employee_filters' class='employee_filters'><option></option><option value=null>All</option> @foreach($employeesWithAttendances as $id=>$employee)<option value='{{$id}}'>{{$employee}}</option>@endforeach </select>";
      
      $('#empfilter').append(empSelect);
      $('#employee_filters').select2({
        "placeholder": "Select Employee",
      });

      function initializeDT(empVal=null, startD, endD){
        const table = $('#dailyempreport').DataTable({
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search"
          },
          "order": [[ 2, "desc" ]],
          "serverSide": true,
          "processing": true,
          "paging": true,
          "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6 alignright'Bf>>" +
              "<'row'<'col-xs-6'><'col-xs-6'>>" +
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
          "columnDefs": [
            {
              "orderable": false,
              "targets":-1,
            },
            {
              "orderable": false,
              "targets":-2,
            }, 
            { 
              width: 20, 
              targets: [0],
            },
            { 
              width: 200, 
              targets: [-1],
            },
          ],
          "buttons": [
            {
              extend: 'pdfHtml5', 
              title: 'Salesman GPS Path', 
              exportOptions: {
                columns: [0,1,2],
                stripNewlines: false,
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'excelHtml5', 
              title: 'Salesman GPS Path', 
              exportOptions: {
                columns: [0,1,2],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
            {
              extend: 'print', 
              title: 'Salesman GPS Path', 
              exportOptions: {
                columns: [0,1,2],
              },
              footer: true,
              action: function ( e, dt, node, config ) {
                newExportAction( e, dt, node, config );
              }
            },
          ],
          "ajax":{
            "url": "{{ domain_route('company.admin.salesmangpspath.ajaxDatatable') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ 
              _token: "{{csrf_token()}}", 
              empVal : empVal,
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
            }
          },
          "columns": [
            {"data" : "id"},
            {"data" : "name"},
            {"data" : "check_datetime"},
            {"data" : "processed_path"},
            {"data" : "raw_path"},
            {"data" : "path_details"},
          ],
        });
        table.buttons().container()
            .appendTo('#dailyempreportexports');
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
            data.length = -1;
            dt.one('preDraw', function (e, settings) {
              if(button[0].className=="btn btn-default buttons-pdf buttons-html5"){
                var moduleName = "salesmangps";
                var columns = JSON.stringify(["Employee Name", "Date"]);
                $.each(settings.json.data, function(key, htmlContent){
                  settings.json.data[key].id = key+1;
                  settings.json.data[key].name = $(settings.json.data[key].name)[0].textContent;
                });
                customExportAction(config, settings, moduleName, columns);
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

        function customExportAction(config, settings, modName, cols){
          $('#exportedData').val(JSON.stringify(settings.json));
          $('#pageTitle').val(config.title);
          $('#reportName').val(modName);
          $('#columns').val(cols);
          var properties = JSON.stringify(["id", "name", "check_datetime"]);
          $('#properties').val(properties);
          $('#pdf-generate').submit();
        }
        $('#reportrange').removeClass('hidden');
      };   
      $('body').on("change", ".employee_filters",function () {
        var empVal = $(this).find('option:selected').val();
        if(empVal=="null"){
          empVal = null;
        }
        var start = $('#start_edate').val();
        var end = $('#end_edate').val();
        if(empVal != '')
        {
          $('#dailyempreport').DataTable().destroy();
          initializeDT(empVal, start, end);
        }
      });
      $('.traveldetail').click(function (e) {
          var tdate = $(this).data('tdate');
          var empid = $(this).data('empid');
          $.ajax({
              type: 'GET',
              dataType: 'html',
              url: "{{ domain_route('company.admin.reports.getdistancetravelled') }}",
              data: {
                  'empid': empid,
                  'tdate': tdate,
              },
              success: function (data3) {
                  $('#traveldetails').html(data3);
                  $("#disttravel").modal('show');
              }
          });
          e.preventDefault();
      });

      $('#reportrange').removeClass('hidden');
    });
    $(function(){
      $('body').on('click', '.mapgenerate',function() {
        currentElem = $(this);
        currentElem.find('.fa-map-marker').hide();
        currentElem.find('.fa-spinner').show();
        currentElem.attr('disabled',true);
        var mapdate = currentElem.data('date');
        var nMapDate = currentElem.data('ndate');
        var emp = currentElem.data('eid');
        var rowid = currentElem.data('rowid');
        var title = "Location Map:"+ currentElem.data('ename')+" "+ nMapDate;
        var companylat =  {{ config('settings.latitude') }};
        var companylng = {{ config('settings.longitude') }};
        var gpsType = currentElem.data('gps_type');

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
            if(data.status==404){
              alert(data.message);
              currentElem.find('.fa-map-marker').show();
              currentElem.find('.fa-spinner').hide();
              currentElem.attr('disabled',false);
              return false;
            }
            locations = [];
            var fileloc=JSON.parse(data.fileloc);
            var partyloc=JSON.parse(JSON.stringify(data.partyLoc));
            currentElem.find('.fa-map-marker').show();
            currentElem.find('.fa-spinner').hide();
            currentElem.attr('disabled', false);
            arrayGroupdedLocations = [];
            arrayPaths = [];
            totalDistance = 0;
            totalFineDistance = 0;
            var recentPosition; 
            var infowindow = new google.maps.InfoWindow();

            console.log(fileloc[0]);
            
            if(fileloc.length==0 || fileloc[0]==null){
              alert('No travel path found');
              return;
            }
            if(fileloc.length!==0 && fileloc[0]!=null){
              console.log(fileloc);
              $.each(fileloc, function(index, item) {
            
                arrayGroupdedLocations.push(item);
                item.forEach(function(temp){
                  recentPosition = new google.maps.LatLng(temp.latitude,temp.longitude);
                });
              
              });
              console.log(arrayGroupdedLocations);
            }else{
              recentPosition = new google.maps.LatLng(companylat,companylng);
            }

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
              var totalLocationCount = arrayGroupdedLocations[0].length;
              var startDots = 0;
              arrayGroupdedLocations.forEach(function(tempArray){
                  tempArray.forEach(function(tempValue){
                    if(startDots == 0){
                      marker = new google.maps.Marker({
                        position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
                        map: map,
                        icon: {
                          path: google.maps.SymbolPath.CIRCLE,
                          fillColor: '#006400',
                          fillOpacity: 0.9,
                          strokeColor: '#ADFF2F',
                          strokeOpacity: 0.9,
                          strokeWeight: 1,
                          scale: 5
                        }
                      });
                    }else if(startDots==totalLocationCount-1){
                      marker = new google.maps.Marker({
                        position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
                        map: map,
                        icon: {
                          path: google.maps.SymbolPath.CIRCLE,
                          fillColor: '#FF0000',
                          fillOpacity: 0.6,
                          strokeColor: '#FFD700',
                          strokeOpacity: 0.9,
                          strokeWeight: 1,
                          scale: 5
                        }
                      });
                    }else{
                      marker = new google.maps.Marker({
                        position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
                        map: map,
                        icon: {
                          path: google.maps.SymbolPath.CIRCLE,
                          fillColor: '#FF8C00',
                          fillOpacity: 0.8,
                          strokeColor: '#FFD700',
                          strokeOpacity: 0.9,
                          strokeWeight: 1,
                          scale: 5
                        }
                      });
                    }
                    startDots++;
                  });
                totalFineDistance =totalFineDistance + getTotalDistanceFromLatLngArray(tempArray,false);
              });
            }
        
            var goldStar = {
              path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
              fillColor: '#800080',
              fillOpacity: 0.7,
              scale: (0.05, 0.05),
              strokeColor: '#FF00FF',
              strokeWeight: 0.4
            };
            $.each(partyloc, function(index, item) {
              marker = new google.maps.Marker({
                  position: new google.maps.LatLng(item.latitude,item.longitude),
                  icon: goldStar, title:item.company_name,map: map
              });

              google.maps.event.addListener(marker, 'click', (function(marker, i) {
                  return function() {
                      infowindow.setContent(item.company_name);
                      infowindow.open(map, marker);
                  }
              })(marker, i));
            });
            var accuracy = data.accuracy;
            if(!data.is_checked_in){
              if(accuracy >= 70){
                var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-success'> Good</span></p>";
              }else if(accuracy <= 40){
                var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-danger'> Poor</span></p>";
              }else if(accuracy > 40 && accuracy < 70 ){
                var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-warning'> Average</span></p>";
              }
            }else{
              var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-info'> NA</span></p>";
            }
            var dataReceived = data.dataReceived;
            var percentAccuratePoints = data.percentAccuratePoints;
            var footerText = "";
            if(accuracy < 70 && !data.is_checked_in){
              if(dataReceived < 50){
                footerText = `<span>Less points have been received from the device due to: </span><br/>
                              <span>- Low Battery level  </span><br/>
                              <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                              <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>`;
              }else{
                if(dataReceived >= 50 && dataReceived <= 90 && percentAccuratePoints >= 60){
                  footerText = `<span>Received less data points from device due to: </span><br/>
                              <span>- Low Battery level  </span><br/>
                              <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                              <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>`;
                }else if(dataReceived >= 50 && dataReceived <= 90 && percentAccuratePoints < 60){
                  footerText = `<span>Received few data points from device but mostly inaccurate due to: </span><br/>
                              <span>- Low Battery level  </span><br/>
                              <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                              <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>
                              <span>- User was indoor </span><br/>
                              <span>- User was not moving </span><br/>`;
                }else if(dataReceived > 90 && percentAccuratePoints > 50 && percentAccuratePoints < 90 ){
                  footerText = `<span>Data received from device but accuracy is average because: </span><br/>
                              <span>- User was indoor </span><br/>
                              <span>- User was not moving </span><br/>
                              <span>- Battery level was low </span><br/>`;
                }else if(dataReceived > 90 && percentAccuratePoints < 50 ){
                  footerText = `<span>Data received from device but accuracy is poor because: </span><br/>
                              <span>- User was indoor </span><br/>
                              <span>- User was not moving </span><br/>
                              <span>- Battery level was low </span><br/>`;
                }
              }
            }
            $("#total-distance").html("Total Distance = "+totalFineDistance.toFixed(3)+ "KM");
            var total_distance = "<br/><span style='font-size: 14px;'>Total Distance = "+totalFineDistance.toFixed(3)+ "KM</span>";
            $("#modal-default").find(".modal-title").html(title + total_distance + accuracy_label );        
            $("#modal-default").find(".modal-footer").html(footerText); 
            $("#modal-default").modal('show');
            // var totalDistance = 0;
            // var totalTimeDifference = 0;
            // var html = "";

            // data.reverse();
            // $.each(data, function(index, item) {
            //   var tempDistance = getTotalDistanceFromLatLngArray(item.locations,false);
            //   var tempDifference = item.checkout - item.checkin;

            //   var checkOutTime = (tempDifference == 0) ?"<td><span class='label label-danger'> N/A </td>":"<td>"+item.cout_time+"</td>";
            //   html = html + "<tr> <td>"+item.cin_time+"</td>"+checkOutTime+"<td>"+tempDistance.toFixed(3)+ "&nbsp;KM</td> </tr>";
            //   totalDistance = totalDistance + tempDistance;
            //   totalTimeDifference = totalTimeDifference + tempDifference;
            // });
            // var totalHourString = "Total Worked Hour: "+getHourMinuteString(totalTimeDifference);
            // var totalDistanceString = "Total Distance Travelled: "+totalDistance.toFixed(3)+" KM";
            // $("#emp_name_date").html(employeeName+"("+rowDate+")");
            // $("#total_hr_string").html(totalHourString);
            // $("#total_distance_string").html(totalDistanceString);
            // $("#cicolist").html(html);
            // $("#workhours").modal('show');
          },
          error:function(xhr){
            if(xhr.status===524){
              alert("We are currently optimizing this feature. Please check back in a few hours");
            }
            currentElem.find('.fa-map-marker').show();
            currentElem.find('.fa-spinner').hide();
            currentElem.attr('disabled', false);
          }
        });
      });

      $('body').on('click', '.hourdetail2', function () {
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
            var totalDistance = 0;
            var totalTimeDifference = 0;
            var html = "";
            let isLocationNull = true;

            data.reverse();
            $.each(data, function(index, item) {
              var tempDistance = getTotalDistanceFromLatLngArray(item.locations,false);
              if(item.locations) isLocationNull = false;
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
            $("#total_distance_string").html("");
            $("#total_distance_string").html(totalDistanceString);
            if(isLocationNull) $("#total_distance_string").append("<br/><span style='color: #a58100d9;'>We are currently optimizing this feature. Please check back in a few hours</span>")
            $("#cicolist").html(html);
            $("#workhours").modal('show');
          },
          error: function(xhr){
            if(xhr.status===524){
              alert("We are currently optimizing this feature. Please check back in a few hours");
            }
          }
        });
      });

      $('body').on('click', '.raw_location',function() {
        currentElem = $(this);
        currentElem.find('.fa-map-marker').hide();
        currentElem.find('.fa-spinner').show();
        currentElem.attr('disabled',true);
        var mapdate = currentElem.data('date');
        var nMapDate = currentElem.data('ndate');
        var emp = currentElem.data('eid');
        var title = "<h4> Raw Location Map:" + currentElem.data('ename')+ " "+ nMapDate +"</h4>";
        var companylat =  {{ config('settings.latitude') }};
        var companylng = {{ config('settings.longitude') }};

        $.ajax({
          type: 'GET',
          dataType:'json',
          url: ajaxUrls.getRawLocation,
          data: {
            'eid': emp,
            'mapdate':mapdate
          },
          success: function(data) {
            if(data.status === 404) {
              alert(data.message);
              return;
            }else if(data.status === 200){
              var recentPosition; 
              var infowindow = new google.maps.InfoWindow();
              var rawLocations = data.locations;
              var partyLocations = data.partyLocations;
              $.each(rawLocations, function(index, item) {
                recentPosition = new google.maps.LatLng(item.latitude,item.longitude);
              });
              var map = new google.maps.Map(document.getElementById('devmap'), {
                zoom: 15,
                center: recentPosition,
                mapTypeId: google.maps.MapTypeId.TERRAIN
              });
              var marker, i;
              var startDots = 0;
              var totalLocationCount = rawLocations.length;
              rawLocations.forEach(function(tempValue){
                if(Math.floor(tempValue.accuracy) > 0 && Math.floor(tempValue.accuracy) < 60){
                  var fillColor = "#95ec0e";
                }else{
                  var fillColor = "#efff00";
                }

                marker = new google.maps.Marker({
                  position: new google.maps.LatLng(tempValue.latitude, tempValue.longitude),
                  map: map,
                  icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: fillColor,
                    fillOpacity: 0.9,
                    strokeColor: '#ADFF2F',
                    strokeOpacity: 0.9,
                    strokeWeight: 1,
                    scale: 5
                  }
                });

                google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
                    return function() {
                      let dateTime = "DateTime: " + tempValue.datetime;
                      let acctext = Math.ceil(tempValue.accuracy) == 0 ? 'NA' : Math.ceil(tempValue.accuracy)  +" m";
                      let accuracy = "<br/ >Accuracy: " + acctext;
                      let btryLevel = "<br/ >Battery Level: " + tempValue.battery_level + " %";
                      
                      infowindow.setContent(dateTime + accuracy + btryLevel);
                      infowindow.open(map, marker);
                    }
                })(marker, i));
                startDots++;
              });
            }

            $.each(partyLocations, function(index, item) {
              marker = new google.maps.Marker({
                  position: new google.maps.LatLng(item.latitude,item.longitude),
                  title:item.company_name,map: map
              });

              google.maps.event.addListener(marker, 'click', (function(marker, i) {
                  return function() {
                    infowindow.setContent(item.company_name);
                    infowindow.open(map, marker);
                  }
              })(marker, i));
            });

            var accuracy = data.accuracy;
            if(!data.is_checked_in){
              if(accuracy >= 70){
                var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-success'> Good</span></p>";
              }else if(accuracy <= 40){
                var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-danger'> Poor</span></p>";
              }else if(accuracy > 40 && accuracy < 70 ){
                var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-warning'> Average</span></p>";
              }
            }else{
              var accuracy_label = "<p style='font-size: 14px;'>Accuracy Level: <span class='label label-info'> NA</span></p>";
            }
            var dataReceived = data.dataReceived;
            var percentAccuratePoints = data.percentAccuratePoints;
            var footerText = "";
            if(accuracy < 70 && !data.is_checked_in){
              if(dataReceived < 50){
                footerText = `<span>Less points have been received from the device due to: </span><br/>
                              <span>- Low Battery level  </span><br/>
                              <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                              <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>`;
              }else{
                if(dataReceived >= 50 && dataReceived <= 90 && percentAccuratePoints >= 60){
                  footerText = `<span>Received less data points from device due to: </span><br/>
                              <span>- Low Battery level  </span><br/>
                              <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                              <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>`;
                }else if(dataReceived >= 50 && dataReceived <= 90 && percentAccuratePoints < 60){
                  footerText = `<span>Received few data points from device but mostly inaccurate due to: </span><br/>
                              <span>- Low Battery level  </span><br/>
                              <span>- The phone automatically switched off the location service to save battery & improve performance  </span><br/>
                              <span>- User's GPS/location was switched off (either from App setting or phone)</span><br/>
                              <span>- User was indoor </span><br/>
                              <span>- User was not moving </span><br/>`;
                }else if(dataReceived > 90 && percentAccuratePoints > 50 && percentAccuratePoints < 90 ){
                  footerText = `<span>Data received from device but accuracy is average because: </span><br/>
                              <span>- User was indoor </span><br/>
                              <span>- User was not moving </span><br/>
                              <span>- Battery level was low </span><br/>`;
                }else if(dataReceived > 90 && percentAccuratePoints < 50 ){
                  footerText = `<span>Data received from device but accuracy is poor because: </span><br/>
                              <span>- User was indoor </span><br/>
                              <span>- User was not moving </span><br/>
                              <span>- Battery level was low </span><br/>`;
                }
              }
            }
            
            $("#modal-default").find(".modal-title").html(title + accuracy_label);  
            $("#modal-default").find(".modal-footer").html(footerText);       
            $("#modal-default").modal('show');
          },
          error:function(e){
            currentElem.find('.fa-map-marker').show();
            currentElem.find('.fa-spinner').hide();
            currentElem.attr('disabled', false);
          },
          complete: function(e){
            currentElem.find('.fa-map-marker').show();
            currentElem.find('.fa-spinner').hide();
            currentElem.attr('disabled',false);
          }
        });
      });

      //responsive 
      $('#reportrange').on('click',function(){
        if ($(window).width() <= 320) {   
          $(".daterangepicker").addClass("eagpsdateposition");
          
        }
        else if ($(window).width() <= 768) {
          $(".daterangepicker").addClass("eagpsdateposition");
        }
        else {   
          $(".daterangepicker").removeClass("eagpsdateposition");
        }
      });

    });
  </script>
@endsection