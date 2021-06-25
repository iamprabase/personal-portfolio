@extends('layouts.company')
@section('title', 'Beat Report')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
  href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/plugins/iCheck/all.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">

<style>
  #legend1 {
    font-family: Arial, sans-serif;
    background: #fff;
    padding: 5px;
    margin: 15px;
  }

  #legend1 img {
    vertical-align: middle;
  }

  #legend2 {
    font-family: Arial, sans-serif;
    background: #fff;
    padding: 5px;
    margin: 15px;
  }

  #legend2 img {
    vertical-align: middle;
  }

  .view {
    color: blue,
    cursor:pointer,
  }

</style>
@endsection

@section('content')

<section class="content">
  <div class="nav-tabs-custom reportstab">
    <ul class="nav nav-tabs">
      <li class="active"><a href="#multiplebeats" id="multiplebeats_tab" data-toggle="tab">Beat Report by Date</a></li>
      <li><a href="#singlesalesmanbeat" id="singlesalesmanbeat_tab" data-toggle="tab">Beat Report by Salesman</a></li>
    </ul>
    <div class="tab-content">
      <div class="active tab-pane multiplebeats" id="multiplebeats">
        @include('company.newreports.multiplebeats')
      </div>
      <div class="tab-pane singlesalesmanbeat" id="singlesalesmanbeat">
        @include('company.newreports.custombeat')
      </div>
      <!-- /.tab-content -->
    </div>
    <!-- /.nav-tabs-custom -->
  </div>
</section>
@endsection

@section('scripts')
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
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script type="text/javascript">
  $('#loader1').attr('hidden','hidden');
  $('#mainBox').removeClass('box-loader');
  $('#loader2').attr('hidden','hidden');
  $('#mainBox2').removeClass('box-loader');
  $('.select2').select2();
  var table1 = $('#dateroute').DataTable({"dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6'>>"+
              "<'row'<'col-xs-4'><'col-xs-4'>>"+
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 'searching': false,});
  $('document').ready(function(){
    $('.dt-button').addClass('btn btn-default');
    $('#dateroute').parent().addClass('table-responsive');
    $('#salesmanroute').parent().addClass('table-responsive');
  });
  @if(config('settings.ncal')==0)
    $('.fromDate').datepicker({
      format: "yyyy-mm-dd",
      endDate: new Date(),
      autoclose: true,
      orientation: 'bottom'
    });
  @else
    $('#mstart_ndate').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange:function(){
        $('#mstart_edate').val(BS2AD($('#mstart_ndate').val()));
      }
    });
  @endif

  var map;
  function initMap() { 
    map = new google.maps.Map(
    document.getElementById('map'), 
    {
      zoom: 10,
      center: new google.maps.LatLng(26.4673609, 87.2840125),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
	}
  
  $('#reportForm').on('submit', function (e) {
    e.preventDefault();
    @if(config('settings.ncal')==0)
      let date = $('#fromDate').val();
    @else
      let date = $('#mstart_edate').val();
    @endif

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },

      type: "POST",
      url: "{{ domain_route('company.admin.beatroutereport') }}",
      data: {
        emp_id : null,
        startdate : date,
      },
      beforeSend: function (url, data) {
        $('#mainBox').addClass('box-loader');
        $('#getReport').attr('disabled',true);
        $('#loader1').removeAttr('hidden');
      },
      success: function (data) {
        $('#dateroute').DataTable().clear().destroy();
        $('#dateroute tbody').empty();
        $('#dateroute tbody').html(data);
        $('#loader1').attr('hidden','hidden');
        $('#mainBox').removeClass('box-loader');
        var table = $('#dateroute').DataTable({
          "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6'>>"+
              "<'row'<'col-xs-4'><'col-xs-4'>>"+
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
          "columnDefs": [
                { "width": "5%", "targets": [4,6] }
              ],
            searching: false,
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Beat Report by Date',
                    exportOptions: {
                      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    action: function ( e, dt, node, config ) {
                      newExportAction( e, dt, node, config );
                    },
                    title: 'Beat Report by Date',
                    orientation: 'landscape',
                    exportOptions: {
                      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                },
                {
                    extend: 'print',
                    title: 'Beat Report by Date',
                    orientation: 'landscape',
                    exportOptions: {
                      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    },
                    pageSize: 'A3'
                },
            ],
        });
        table.buttons().container().addClass('pull-right').css('width', 'fit-content').appendTo('#dateexports');
        $('#dateroute').parent().addClass('table-responsive');
      },
      error: function (jqXHR, textStatus, errorThrown, data) {
        $('#loader1').attr('hidden','hidden');
        $('#mainBox').removeClass('box-loader');
      },
      complete: function () {
          $('#getReport').attr('disabled', false);
      }
    });
  });

  $('body').on('click','.view',function(){
    if($(this).data("name")!=""){
      let parties = $(this).data("name").split(',');
      $('#list-parties').html('');
      $('#exampleModalLongTitle').html('');
      $('#exampleModalLongTitle').append('<b>'+$(this).data("title")+'</b>');
      parties.forEach(party => {
        $('#list-parties').append('<li class="list-group-item">'+ party +'</li>');
      });
      $('#viewPartiesModal').modal();
    }else{
      alert("No Parties");
    }
  });

  $('body').on('click','.getLocations',function(e){
    e.preventDefault();
    let url = $(this).parent()[0].href;
    let emp_id = $(this).data("id");
    @if(config('settings.ncal')==0)
      let date = $('#fromDate').val();
    @else
      let date = $('#mstart_ndate').val();
    @endif
    let emp_name = $(this).data('ename');
    let effective_clients = $(this).data('effective_calls');
    let non_effective_clients = $(this).data('non_effective_calls');
    let not_covered = $(this).data('not_covered');

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },

      type: "GET",
      url: url,
      data: {
        "date" : date,
        "emp_id" : emp_id,
        "effective_calls": effective_clients,
        "non_effective_calls": non_effective_clients,
        "not_covered": not_covered,
      },
      beforeSend: function (url, data) {
        $('#singlesalesmanbeat_tab').css('pointer-events','none');
        $('#mainBox').addClass('box-loader');
        $('#getReport').attr('disabled',true);
        $('#loader1').removeAttr('hidden');
      },
      success: function (data) {
        if(data!=null){
          $('#viewgpsreport').modal();
          $('#exampleModalLongTitle_name').html(' - <strong>'+emp_name+'</strong> '+' - ' +date);
          showMap(data);
          $('#loader1').attr('hidden','hidden');
          $('#mainBox').removeClass('box-loader');
          $('#singlesalesmanbeat_tab').css('pointer-events','unset');
        }
        $('#singlesalesmanbeat').css('pointer-events','unset');
      },
      error: function (jqXHR, textStatus, errorThrown, data) {
        $('#loader1').attr('hidden','hidden');
        $('#mainBox').removeClass('box-loader');
        $('#singlesalesmanbeat_tab').css('pointer-events','unset');
      },
      complete: function () {
        $('#singlesalesmanbeat_tab').css('pointer-events','unset');
        $('#loader1').attr('hidden','hidden');
        $('#mainBox').removeClass('box-loader');
        $('#getReport').attr('disabled', false);
      }
    });
  });
  
  function showMap(data) {
    var map;
    var infoWindow;
    var bounds = new google.maps.LatLngBounds();
    var markers =JSON.parse(data['visit_locations']);
    var recentPosition = "";
    if(markers){
      recentPosition = new google.maps.LatLng(markers[0][0]["latitude"], markers[0][0]["longitude"]);
    }else{
      recentPosition = new google.maps.LatLng('27.700769','85.300140');
    }
    var mapOptions = {
        zoom: 10, 
        mapTypeId: 'roadmap',
        center: recentPosition,
    };
                    
    map = new google.maps.Map(document.getElementById("map1"), mapOptions);
    map.setTilt(45);
    var infoWindow = new google.maps.InfoWindow(), marker, j;

    var legend1 = document.getElementById('lgnd');
    var legend = document.createElement('div');
    console.log(legend);
    $(legend).attr('id', 'legend1');
    legend1.append(legend);
    var iconBase = 'https://maps.google.com/mapfiles/ms/icons/';
    var legend_icons = {
      'EffectiveCalls': {
        'name': 'Effective Calls',
        'icon': iconBase + 'green-dot.png'
      },
      'NonEffectiveCalls': {
        'name': 'Non Effective Calls',
        'icon': iconBase + 'yellow-dot.png'
      },
      'NotCovered': {
        'name': 'Not Covered',
        'icon': iconBase + 'red-dot.png'
      },
    };

    for (var key in legend_icons) {
      var type = legend_icons[key];
      var name = '<strong>'+type.name+'</strong>';
      var icon = type.icon;
      var span = document.createElement('span');
      span.innerHTML = '<img src="' + icon + '"> ' + name;
      legend.append(span);
    }

    map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(legend);

    if(data['visit_locations']!=null){
      for( i = 0; i < markers.length; i++ ) {
        var path = [];
        infoWindow = new google.maps.InfoWindow(), marker, j;
        for(j = 0; j < markers[i].length; j++){
          var position = new google.maps.LatLng(markers[i][j]["latitude"], markers[i][j]["longitude"]);
          // bounds.extend(position);
          var marker_title = moment(markers[i][j]['unix_timestamp']).format('h:mm A');
          marker = new google.maps.Marker({
              position: position,
              map: map,
              title: marker_title,
              icon:{
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 2,
                strokeColor: '#1672c1',
                strokeOpacity: 1.9,
                strokeWeight: 1.5
              },
            });
          path.push({
            lat : markers[i][j]["latitude"],
            lng : markers[i][j]["longitude"]
          });
          
          google.maps.event.addListener(marker, 'mouseover', (function(marker, i,j) {
              return function() {
                  infoWindow.setContent(moment(markers[i][j]['unix_timestamp']).format('h:mm A'));
                  infoWindow.open(map, marker);
              }
          })(marker, i,j));
        }
        var routePath = new google.maps.Polyline({
          path: path,
          geodesic: true,
          strokeColor: '#1672c1',
          strokeOpacity: 0.6,
          strokeWeight: 1.8
        });
        routePath.setMap(map);
      }
    }

    if(data['eff_location']!=null){
      var markers3 = JSON.parse(data['eff_location']);
      for( i = 0; i < markers3.length; i++ ) {
        var position3 = new google.maps.LatLng(markers3[i]["latitude"], markers3[i]["longitude"]);
        // bounds.extend(position3);
        marker3 = new google.maps.Marker({
            position: position3,
            map: map,
            title: markers3[i][2],
            icon:{url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"}
        });
        google.maps.event.addListener(marker3, 'mouseover', (function(marker3, i) {
            return function() {
                infoWindow.setContent(markers3[i]["company_name"]);
                infoWindow.open(map, marker3);
            }
        })(marker3, i));
      }
    }

    if(data['non_eff_location']!=null){
      var markers2 = JSON.parse(data['non_eff_location']);

      for( i = 0; i < markers2.length; i++ ) {
        var position2 = new google.maps.LatLng(markers2[i]["latitude"], markers2[i]["longitude"]);
        // bounds.extend(position2);
        marker2 = new google.maps.Marker({
            position: position2,
            map: map,
            title: markers2[i][2],
            icon:{url: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png"}
        });
        google.maps.event.addListener(marker2, 'mouseover', (function(marker2, i) {
            return function() {
                infoWindow.setContent(markers2[i]["company_name"]);
                infoWindow.open(map, marker2);
            }
        })(marker2, i));
      }
    }

    if(data['not_covered_location']!=null){
      var markers1 = JSON.parse(data['not_covered_location']);
      for( i = 0; i < markers1.length; i++ ) {
        var position1 = new google.maps.LatLng(markers1[i]["latitude"], markers1[i]["longitude"]);
        // bounds.extend(position1);
        marker1 = new google.maps.Marker({
            position: position1,
            map: map,
            title: markers1[i][2],
            icon:{url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"}
        });
        google.maps.event.addListener(marker1, 'mouseover', (function(marker1, i) {
            return function() {
                infoWindow.setContent(markers1[i]["company_name"]);
                infoWindow.open(map, marker1);
            }
        })(marker1, i));
      }
    }

    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(10);
        google.maps.event.removeListener(boundsListener);
    });
  }


  var newExportAction = function (e, dt, button, config) {
    var self = this;
    var data = [];
    var count = 0;
    var columnsArray = [];
    columnsArray.push("Salesman Name", "TargetCalls/Visits", "Total Actual Calls/Visits", "Effective Calls", "Unscheduled Effective Calls", "Non-effective Calls", "Unscheduled Non-effective Calls", "Not Covered", "Planned Beats", "Actual Beats");
    var columns = JSON.stringify(columnsArray);
    $('#dateroute').DataTable().rows({"search":"applied" }).every( function () {
      var row = {};
      row["id"] = ++count;
      row["nameOrDate"] = this.data()[0];
      row["target_calls"] = this.data()[1];
      row["actual_calls"] = this.data()[2];
      row["eff_calls"] = this.data()[3];
      row["unsch_eff_calls"] = this.data()[4];
      row["non_eff_calls"] = this.data()[5];
      row["unsch_non_eff_calls"] = this.data()[6];
      row["not_covered"] = this.data()[7];
      row["planned_beats"] = this.data()[8].replace(/<[^>]+>/g, '').trim();
      row["actual_beats"] = this.data()[9].replace(/<[^>]+>/g, '').trim();
      data.push(row);
    });
    exportAction('.multiplebeats', config, data, columns);
  };

  var table = $('#salesmanroute').DataTable({"dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6'>>"+
              "<'row'<'col-xs-4'><'col-xs-4'>>"+
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 'searching': false,});
  $('body').on('click','.viewsingle',function(){
    if($(this).data("name")!=""){
      let parties = $(this).data("name").split(',');
      $('#list-parties-single').html('');
      $('#viewPartiesModalSingle').find('#exampleModalLongTitle').html('');
      $('#viewPartiesModalSingle').find('#exampleModalLongTitle').append('<b>'+$(this).data("title")+'</b>');
      parties.forEach(party => {
        $('#list-parties-single').append('<li class="list-group-item">'+ party +'</li>');
      });
      $('#viewPartiesModalSingle').modal();
    }else{
      alert("No Parties");
    }
  });
  @if(config('settings.ncal')==0)
    $('document').ready(function () {
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
              var create_date = Date.parse(data[6]); // use data for the age column
              if (create_date >= start_date && create_date <= end_date) {
                  return true;
              }
              return false;
          }
      );
    });
    $('document').ready(function () {
      var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
      $("#startdate").val(start_date);
      $("#enddate").val(end_date);
    });
  @else
    var lastmonthdate = AD2BS(moment().subtract(30,'days').format('YYYY-MM-DD'));
    var ntoday = AD2BS(moment().format('YYYY-MM-DD'));
    $('#start_ndate').val(lastmonthdate);
    $('#end_ndate').val(ntoday);
    $('#start_edate').val(BS2AD($('#start_ndate').val()));
    $('#end_edate').val(BS2AD($('#end_ndate').val()));
    $('#start_ndate').nepaliDatePicker({
      ndpEnglishInput: 'englishDate',
      onChange:function(){
        $('#start_edate').val(BS2AD($('#start_ndate').val()));
        if($('#start_ndate').val()>$('#end_ndate').val()){
          $('#end_ndate').val($('#start_ndate').val());
          $('#end_edate').val(BS2AD($('#start_ndate').val()));
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
      }
    });
  @endif
      
  $('#customreportForm').on('submit', function (e) {
    e.preventDefault();
    @if(config('settings.ncal')==0)
      let fromdate = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      let todate = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    @else
      let fromdate = $('#start_edate').val();;
      let todate = $('#end_edate').val();
    @endif
    let salesman_id = $('#employee_id').val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },

      type: "POST",
      url: "{{ domain_route('company.admin.custombeatroutereport') }}",
      data: {
          emp_id : salesman_id, 
          fromdate : fromdate,
          todate : todate,
      },
      beforeSend: function (url, data) {
        $('#mainBox2').addClass('box-loader');
        $('#getCustomReport').attr('disabled',true);
        $('#loader2').removeAttr('hidden');
      },
      success: function (data) {
          $('#salesmanroute').DataTable().clear().destroy();
          $('#salesmanroute tbody').html('');
          $('#salesmanroute tbody').html(data);
          $('#loader2').attr('hidden','hidden');
          $('#mainBox2').removeClass('box-loader');
          var salesman_name = $('#rep_salesman_name').val();
          var table = $('#salesmanroute').DataTable({
            "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6'>>"+
              "<'row'<'col-xs-4'><'col-xs-4'>>"+
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
            searching: false,
            sorting: false,
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Beat Report of '+salesman_name,
                    exportOptions: {
                      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    },
                },
                {
                    extend: 'pdfHtml5',
                    action: function ( e, dt, node, config ) {
                      oldExportAction( e, dt, node, config );
                    },
                    title: 'Beat Report of '+salesman_name,
                    orientation: 'landscape',
                    exportOptions: {
                      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    },
                    pageSize : 'LEGAL',
                    customize: function (doc) {
                      doc.content[1].table.widths =
                          Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    }
                },
                {
                    extend: 'print',
                    title: 'Beat Report of '+salesman_name,
                    orientation: 'landscape',
                    exportOptions: {
                      columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    },
                    pageSize : 'LEGAL',
                },
            ],
          });
        $('#container').css( 'display', 'block' );
        table.columns.adjust().draw();
        table.buttons().container().addClass('pull-right').css('width', 'fit-content').appendTo('#dailyempreportexports');
        $('#salesmanroute').parent().addClass('table-responsive');
      },
      error: function (jqXHR, textStatus, errorThrown, data) {
          alert(data.msg);
          alert(jqXHR.message);
          alert("AJAX error: " + textStatus + ' : ' + errorThrown);
      },
      complete: function () {
          $('#getCustomReport').attr('disabled', false);
      }
    });
  });

  var oldExportAction = function (e, dt, button, config) {
    var self = this;
    var data = [];
    var count = 0;
    var columnsArray = [];
    columnsArray.push("Date", "TargetCalls/Visits", "Total Actual Calls/Visits", "Effective Calls", "Unscheduled Effective Calls", "Non-effective Calls", "Unscheduled Non-effective Calls", "Not Covered", "Planned Beats", "Actual Beats");
    var columns = JSON.stringify(columnsArray);
    $('#salesmanroute').DataTable().rows({"search":"applied" }).every( function () {
      var row = {};
      row["id"] = ++count;
      row["nameOrDate"] = this.data()[0];
      row["target_calls"] = this.data()[1];
      row["actual_calls"] = this.data()[2];
      row["eff_calls"] = this.data()[3];
      row["unsch_eff_calls"] = this.data()[4];
      row["non_eff_calls"] = this.data()[5];
      row["unsch_non_eff_calls"] = this.data()[6];
      row["not_covered"] = this.data()[7];
      row["planned_beats"] = this.data()[8].replace(/<[^>]+>/g, '').trim();
      row["actual_beats"] = this.data()[9].replace(/<[^>]+>/g, '').trim();
      data.push(row);
    });
    exportAction('#singlesalesmanbeat',config, data, columns);
  };

  $('body').on('click','.singleGetLocations',function(e){
    e.preventDefault();
    let url = $(this).parent()[0].href;
    let emp_id = $(this).data("id");
    let date = $(this).data("date");
    let emp_name = $(this).data('ename');
    let effective_clients = $(this).data('effective_calls');
    let non_effective_clients = $(this).data('non_effective_calls');
    let not_covered = $(this).data('not_covered');

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },

      type: "GET",
      url: url,
      data: {
        "date" : date,
        "emp_id" : emp_id,
        "effective_calls": effective_clients,
        "non_effective_calls": non_effective_clients,
        "not_covered": not_covered,
      },
      beforeSend: function (url, data) {
          $('#multiplebeats_tab').css('pointer-events','none');
          $('#mainBox2').addClass('box-loader');
          $('#getCustomReport').attr('disabled',true);
          $('#loader2').removeAttr('hidden');
      },
      success: function (data) {
        $('#viewgpsreportsingle').modal();
        @if(config('settings.ncal')==1)
          date = AD2BS(date);
        @endif
        $('#viewgpsreportsingle').find('#exampleModalLongTitle_name').html(' - <strong>'+emp_name+'</strong> '+ ' - '+date);
        showMap2(data);
        $('#loader2').attr('hidden','hidden');
        $('#mainBox2').removeClass('box-loader');
        $('#multiplebeats_tab').css('pointer-events','unset');
      },
      error: function (jqXHR, textStatus, errorThrown, data) {
        $('#multiplebeats_tab').css('pointer-events','unset');
          alert(data.msg);
          alert(jqXHR.message);
          alert("AJAX error: " + textStatus + ' : ' + errorThrown);
      },
      complete: function () {
        $('#multiplebeats_tab').css('pointer-events','unset');
          $('#getCustomReport').attr('disabled', false);
      }
    });
  });

  function showMap2(data) {
    var map;
    var bounds = new google.maps.LatLngBounds();
    var markers = JSON.parse(data['visit_locations']);
    
    var recentPosition = "";
    if(markers){
      recentPosition = new google.maps.LatLng(markers[0][0]["latitude"], markers[0][0]["longitude"]);
    }else{
      recentPosition = new google.maps.LatLng('27.700769','85.300140');
    }
    var mapOptions = {
        zoom: 10, 
        mapTypeId: 'roadmap',
        center: recentPosition,
    };

    map = new google.maps.Map(document.getElementById("map2"), mapOptions);
    map.setTilt(45);
    var infoWindow = new google.maps.InfoWindow(), marker, j;

    if(data['visit_locations']!=null){
      for( i = 0; i < markers.length; i++ ) {
        var path = new Array();
        var infoWindow = new google.maps.InfoWindow(), marker, j;
        for(j = 0; j < markers[i].length; j++){
          var position = new google.maps.LatLng(markers[i][j]["latitude"], markers[i][j]["longitude"]);
          // bounds.extend(position);
          var mapTitle2 = moment(markers[i][j]["unix_timestamp"]).format('h:mm A');
          marker = new google.maps.Marker({
              position: position,
              map: map,
              title: mapTitle2,
              icon:{
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 2,
                strokeColor: '#1672c1',
                strokeOpacity: 0.8,
                strokeWeight: 1.5
              },
          });
          path.push({
            lat : markers[i][j]["latitude"],
            lng : markers[i][j]["longitude"]
          });
  
          google.maps.event.addListener(marker, 'mouseover', (function(marker, i,j) {
              return function() {
                  infoWindow.setContent(moment(markers[i][j]["unix_timestamp"]).format('h:mm A'));
                  infoWindow.open(map, marker);
              }
          })(marker, i,j));
        }
        var routePath = new google.maps.Polyline({
          path: path,
          geodesic: true,
          strokeColor: '#1672c1',
          strokeOpacity: 1.9,
          strokeWeight: 2.5
        });
        routePath.setMap(map);
      }
    }

    if(data['eff_location']!=null){
      var markers3 = JSON.parse(data['eff_location']);

      for( i = 0; i < markers3.length; i++ ) {
          var position3 = new google.maps.LatLng(markers3[i]["latitude"], markers3[i]["longitude"]);
          // bounds.extend(position3);
          marker3 = new google.maps.Marker({
              position: position3,
              map: map,
              title: markers3[i][2],
              icon:{url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"}
          });
          google.maps.event.addListener(marker3, 'mouseover', (function(marker3, i) {
              return function() {
                  infoWindow.setContent(markers3[i]["company_name"]);
                  infoWindow.open(map, marker3);
              }
          })(marker3, i));
      }
    }
    
    if(data['non_eff_location']!=null){
      var markers2 = JSON.parse(data['non_eff_location']);

      for( i = 0; i < markers2.length; i++ ) {
          var position2 = new google.maps.LatLng(markers2[i]["latitude"], markers2[i]["longitude"]);
          // bounds.extend(position2);
          marker2 = new google.maps.Marker({
              position: position2,
              map: map,
              title: markers2[i][2],
              icon:{url: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png"}
          });
          google.maps.event.addListener(marker2, 'mouseover', (function(marker2, i) {
              return function() {
                  infoWindow.setContent(markers2[i]["company_name"]);
                  infoWindow.open(map, marker2);
              }
          })(marker2, i));
      }
    }

    if(data['not_covered_location']!=null){
      var markers1 = JSON.parse(data['not_covered_location']);

      for( i = 0; i < markers1.length; i++ ) {
          var position1 = new google.maps.LatLng(markers1[i]["latitude"], markers1[i]["longitude"]);
          // bounds.extend(position1);
          marker1 = new google.maps.Marker({
              position: position1,
              map: map,
              title: markers1[i][2],
              icon:{url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"}
          });
          google.maps.event.addListener(marker1, 'mouseover', (function(marker1, i) {
              return function() {
                  infoWindow.setContent(markers1[i]["company_name"]);
                  infoWindow.open(map, marker1);
              }
          })(marker1, i));
      }
    }
    var legend1 = document.getElementById('lgnd2');
    var legend = document.createElement('div');
    $(legend).attr('id', 'legend2');
    var iconBase = 'https://maps.google.com/mapfiles/ms/icons/';
    var legend_icons = {
      'Effective Calls': {
        name: 'Effective Calls',
        icon: iconBase + 'green-dot.png'
      },
      'Non Effective Calls': {
        name: 'Non Effective Calls',
        icon: iconBase + 'yellow-dot.png'
      },
      'Not Covered': {
        name: 'Not Covered',
        icon: iconBase + 'red-dot.png'
      },
    };

    for (var key in legend_icons) {
      var type = legend_icons[key];
      var name = '<strong>'+type.name+'</strong>';
      var icon = type.icon;
      var div = document.createElement('span');
      div.innerHTML = '<img src="' + icon + '"> ' + name;
      legend.appendChild(div);
    }

    map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(legend);
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(10);
        google.maps.event.removeListener(boundsListener);
    });
  }

  //responsive 
  $('#reportrange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("beatreportdateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("beatreportdateposition");
    }
    else {   
      $(".daterangepicker").removeClass("beatreportdateposition");
    }
  });

  function exportAction(bodyClass, config, data, cols){
    $(bodyClass).find('#exportedData').val(JSON.stringify(data));
    $(bodyClass).find('#pageTitle').val(config.title);
    $(bodyClass).find('#reportName').val(bodyClass);
    $(bodyClass).find('#columns').val(cols);
    var propertiesArray = [];
    propertiesArray.push("id","nameOrDate", "target_calls", "actual_calls", "eff_calls", "unsch_eff_calls", "non_eff_calls", "unsch_non_eff_calls", "not_covered", "planned_beats", "actual_beats");
    var properties = JSON.stringify(propertiesArray);
    $(bodyClass).find('#properties').val(properties);
    $(bodyClass).find('#pdf-generate').submit();
  }

</script>
@endsection