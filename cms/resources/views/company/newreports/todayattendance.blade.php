@extends('layouts.company')
@section('title', 'Today\'s Attendance')

@section('stylesheets')
<link rel="stylesheet" href="{{asset('assets/dist/css/multiselect.css') }}" />
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@if(config('settings.ncal')==1)
<link rel="stylesheet" href="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.css') }}">
@else
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet"
    href="{{asset('assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endif
<link rel="stylesheet" href="{{asset('assets/bower_components/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
<style>
    .dt-buttons.btn-group {
      padding-right: 10px;
    }

    .brandcat {
        margin: 0 8px 0 0;
        padding: 0.2em 1.6em 0.3em;
        border-radius: 10rem;
        font-size: x-xsall;
    }

    .cat {
        margin: 0 0 0 8px;
    }

    .tooltip-inner {
        max-width: 500px !important;
        background-color: aliceblue;
        color: black;
        max-height: -webkit-fill-available;
    }

    .fa.fa-info-circle {
        padding-left: inherit;
        cursor: pointer;
        color: #4c8c16;
    }

    .box-opacity {
        opacity: 0.4;
    }

    #loader3 {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 99;
    }

</style>
@endsection

@section('content')
<section class="content">
    <div class="nav-tabs-custom reportstab">
        <ul class="nav nav-tabs" id="employeetabs">
            <li class="active"><a href="#presenttoday" data-toggle="tab"> Present</a></li>
            <li><a href="#absenttoday" data-toggle="tab">Absent</a></li>
        </ul>
        <div class="tab-content">
            <div class="active tab-pane" id="presenttoday">
                @include('company.newreports.presenttoday')
            </div>
            <div class="tab-pane" id="absenttoday">
                @include('company.newreports.absenttoday')
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{asset('assets/dist/js/jquery.multiselect.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
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
{{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
<script src="{{asset('assets/plugins/nepaliDate/nepaliCalendar.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script type="text/javascript">
    $('[data-toggle="tooltip"]').tooltip({
      placement : 'right',
      container: 'body'
    });
    
    @if(config('settings.ncal')==1)
    @php $dt = date('Y-m-d'); @endphp
    var dToday = "{{getDeltaDate($dt)}}";
    @else
    dToday = moment().format("MMMM DD YYYY");
    @endif

    // Party-wise Latest Stock Report
    let table = $('#dailyemppresentreport').DataTable({
    "dom": `<'row'<'col-xs-6 alignleft'l><'col-xs-6'>>"
        "<'row'<'col-xs-4'><'col-xs-4'>>"
          "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>`,
    stateSave: true,
    "stateSaveParams": function (settings, data) {
    data.search.search = "";
    },
    "paging": true,
    // dom: 'Bfrtip',
    language: {
    search: "_INPUT_",
    searchPlaceholder: "Search"
    },
    buttons: [
    {
    extend: 'excelHtml5',
    title: 'Present Employees '+ dToday,
    },
    {
    extend: 'pdfHtml5',
    title: 'Present Employees '+ dToday,
    },
    {
    extend: 'print',
    title: 'Present Employees '+ dToday,
    },
    ],
    });
    table.buttons().container().appendTo('#dailyempreportexportspresent');
    var select = $('<select class="select2" style="background: #fff;width:100% !important; cursor: pointer;position: absolute;z-index: 999;"><option value="">Select Employees</option></select>')
    .appendTo($('#empfilter').empty())
    .on('change', function () {
    table.column(1)
    .search($(this).val())
    .draw();
    });
    table.column(1).data().unique().sort().each(function (d, j) {
    // if($(d)){
    select.append('<option value="' + d + '">' + d + '</option>')
    // }
    });
    // Single Party Historical Stock Report
    let table2 = $('#dailyempabsentreport').DataTable({
      "dom": `<'row'<'col-xs-6 alignleft'l>
        <'col-xs-6'>>"
          "<'row'<'col-xs-4'>
            <'col-xs-4'>>"
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>`,
    stateSave: true,
    "stateSaveParams": function (settings, data) {
    data.search.search = "";
    },
    "paging": true,
  // dom: 'Bfrtip',
    language: {
    search: "_INPUT_",
    searchPlaceholder: "Search"
    },
    buttons: [
    {
    extend: 'excelHtml5',
    title: 'Absent Employees '+ dToday,
    },
    {
    extend: 'pdfHtml5',
    title: 'Absent Employees '+ dToday,
    },
    {
    extend: 'print',
    title: 'Absent Employees '+ dToday,
    },
    ],
    });
    table2.buttons().container().appendTo('#dailyempreportexportsabsent');
    // var _select = $('<select class="select2"style="background: #fff;width:100% !important; cursor: pointer;position: absolute;z-index: 999;"><option value="">Select Employees</option></select>')
    // .appendTo($('#empfilterabsent').empty())
    // .on('change', function () {
    // table2.column(1)
    // .search($(this).val())
    // .draw();
    // });
    // table2.column(1).data().unique().sort().each(function (d, j) {
    // // if($(d)){
    // _select.append('<option value="' + d + '">' + d + '</option>')
    // // }
    // });


    $('.select2').select2();
</script>
@endsection