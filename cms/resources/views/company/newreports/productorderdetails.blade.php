@extends('layouts.company')
@section('title', 'Product Order Details Reports')

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
  .box-loader {
    opacity: 1.5;
  }

  .fa.fa-info-circle {
    padding-left: inherit;
    cursor: pointer;
    color: #4c8c16;
  }

  .tooltip-inner {
    max-width: 500px !important;
    background-color: aliceblue;
    color: black;
    max-height: -webkit-fill-available;
  }

  .increaseOpacity {
    opacity: 0.3;
  }

  .no-margin{
    margin: 0px;
  }

  .no-lt-pd{
    padding-left: 0px;
  }

  .ms-options-wrap.ms-has-selections>button{
    outline: none!important;
  }

</style>

@endsection


@section('content')

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Order Breakdown Report </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body tablediv">
            @if(\Session::has('success'))
              <div class="alert alert-success">
                {{ \Session::get('success') }}
              </div>
            @endif

            @if(\Session::has('warning'))
              <div class="alert alert-warning">
                {{ \Session::get('warning') }}
              </div>
            @endif

            @if(session('error_message'))
              <div class="alert alert-danger">
                {{ session('error_message') }}
              </div>
            @endif
          <form action="{{domain_route('company.admin.report.productsalesorderdetailsreports')}}" method="post" id="productsalesreports">
            @csrf
            <div class="row no-margin">
              @if(config('settings.ncal')==1)
                <div class="col-xs-4 no-lt-pd">
                  <label>
                    Select Date Range
                  </label>
                  <div class="input-group" id="nepCalDiv">
                    <input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date"
                      autocomplete="off" />
                    <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                    <span class="input-group-addon" aria-readonly="true"><i
                        class="glyphicon glyphicon-calendar"></i></span>
                    <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                      autocomplete="off" />
                    <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                  </div>
                  @if($errors->has('start_date'))
                  <p class="help-block has-error" style="color:red;">{{$errors->first('start_date')}}</p>
                  @endif
                  @if($errors->has('end_date'))
                  <p class="help-block has-error" style="color:red;">{{$errors->first('end_date')}}</p>
                  @endif
                </div>
              @else
                <div class="col-xs-3 no-lt-pd">
                  <label>
                    Select Date Range
                  </label>
                  <span id="reportrange" name="reportrange" class="reportrange" style="width:100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span><i class="fa fa-caret-down"></i>
                  </span>
                  @if($errors->has('start_date'))
                  <p class="help-block has-error" style="color:red;">{{$errors->first('start_date')}}</p>
                  @endif
                  @if($errors->has('end_date'))
                  <p class="help-block has-error" style="color:red;">{{$errors->first('end_date')}}</p>
                  @endif
                </div>
              @endif
              <span>
                <input type="hidden" name="start_date" id="datestart">
                <input type="hidden" name="end_date" id="dateend">
              </span>
              <div @if(config('settings.ncal')==1) class="col-xs-2 no-lt-pd" @else class="col-xs-3 no-lt-pd" @endif>
                <label>
                  Select Party Type
                </label>
                <select id="party_type" style="width: 100%;" class="select2" multiple>
                  @foreach($partytypes as $key=>$value)
                  <option value="{{$key}}" selected>{{$value}}</option>
                  @endforeach
                <option value="0" selected>Unspecified</option>
                </select>
              </div>
              <div class="col-xs-3 no-lt-pd">
                <label>
                  Select Parties
                </label>
                <select id="party_id" class="select2" style="width: 100%;" multiple>
                  @php $decoded_party = json_decode($parties); @endphp
                  @foreach($decoded_party as $party)
                  <option value="{{$party->id}}" data-client_type="{{$party->client_type}}" selected>
                    {{$party->company_name}}</option>
                  @endforeach
                </select>
                @if($errors->has('party_ids'))
                <p class="help-block has-error" style="color:red;">{{$errors->first('party_ids')}}</p>
                @endif
                <input type="hidden" name="party_ids"  id="encoded_party_ids">
              </div>
              <div class="col-xs-3 no-lt-pd">
                <label>
                  Select Order Status
                </label>
                <select name="order_status_select[]" class="multi order_status_select" multiple>
                  @foreach($order_statuses as $key=>$value)
                    <option value="{{$key}}" selected>{{$value}}</option>
                  @endforeach
                </select>
                @if($errors->has('order_status_select'))
                <p class="help-block has-error" style="color:red;">{{$errors->first('order_status_select')}}</p>
                @endif
              </div>
            </div>

            <div class="row no-margin">
              
            </div>

            @if($client_order_approval == 1)
                <div class="row no-margin">
                  <div class="col-xs-6 col-xs-offset-3" style="padding-top:20px;">
                    <div class="col-xs-4" style="width:max-content;">
                      <label>
                        Do you want to include dispatch details ?
                      </label>
                    </div>
                    <div class="col-xs-4" style="width:max-content;">
                      <label class="radio inline">
                        <input class="include_dispatch_detail" name="include_dispatch_detail" type="radio" checked="checked" value="1">Yes
                      </label>
                    </div>
                    <div class="col-xs-4" style="width:max-content;">
                      <label class="radio inline">
                        <input class="include_dispatch_detail" name="include_dispatch_detail" type="radio" value="0">No
                      </label>
                    </div>
                  </div>
                </div>
            @endif

            <div class="row no-margin">
              <div class="col-xs-4 col-xs-offset-4">
                <div class="mx-auto" style="width:100%;padding:10px 0 0 0;">
                  <button type="submit" class="btn btn-default" id="getReport" style="width:100%;">
                    <i class="fa fa-book"></i> Get Report
                  </button>
                </div>
              </div>
            </div>
          </form>

          <div class="row tablediv table-responsive">

          </div>
        </div>


      </div>
      <div class="box" id="mainBox">
        <div class="box-header">
          <h3 class="box-title" id="box-title">Generated Reports</h3>

        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-xs-2"></div>
            <div class="col-xs-7">
              <div class="row">

                <div class="select-2-sec">
                  <div class="col-xs-3">
                    <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="partyfilter"></div>
                  </div>
                  <div class="col-xs-3">
                    <div style="width:150px;margin-top:10px;height: 40px;z-index: 999 " id="salesmfilter"></div>
                  </div>

                </div>
              </div>
            </div>
            <div class="col-xs-3"></div>
          </div>
          <input type="hidden" name="next_refresh" id="next_refresh">
          <table id="order" class="table table-bordered table-striped table-responsive" style="width: 100%;">
            <thead>

              <tr>
                <th class="hidden">#</th>
                <th>Date Generated</th>
                <th>Date Range</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>
              
            </tbody>
            <tfoot>
            </tfoot>
            <div id="loader1" hidden>
              <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            </div>
          </table>
        </div>
        <!-- /.box-body -->
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
@endsection

@section('scripts')
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
<script src="{{asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script type="text/javascript">

  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip({
      placement : 'right',
      container: 'body'
    });

    $('#party_id').multiselect({
      search: true,
      placeholder: "Select Parties",
      selectAll: true
    });

    $('#party_type').multiselect({
      search: true,
      placeholder: "Select Party Type",
      selectAll: true
    });
    $('.order_status_select').multiselect({
      search: true,
      placeholder: "Select Order Status",
      selectAll: true
    });
    
    initializeOrderDT();
  });
  // $('#order').DataTable({
  //   "columnDefs": [
  //   { "orderable": false, "targets": -1 }
  //   ],
  //   'searching': false,
  // });
  $('#loader1').attr('hidden','hidden');
  $('#mainBox').removeClass('box-loader');

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
        $("#datestart").val(start_date);
        $("#dateend").val(end_date);
    });

    $('#reportrange').change(function () {
        var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        $("#datestart").val(start_date);
        $("#dateend").val(end_date);
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
  
  function getDates(start_date, end_date, report_type) {
    var dateArray = [];
    var currentDate = moment(start_date);
    var stopDate = moment(end_date);
    while (currentDate <= stopDate) {
      dateArray.push( moment(currentDate).format('YYYY-MM-DD') )
      currentDate = moment(currentDate).add(1, 'days');
    }
    return dateArray;
  }

  $('#party_type').change(function(){
    let sel_party_types = $('#party_type').val();
    
    if(sel_party_types.length>0){
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ domain_route('company.admin.fetchpartylist') }}",
        type: "GET",
        data:
        {
          sel_party_types : sel_party_types,
        },
        success:function(data) {
          data = JSON.parse(data);
          $('select[id="party_id"]').empty();
          $('select[id="party_id"]').multiselect('reload');
          data.forEach(element => {
            $('select[id="party_id"]').append('<option value="'+ element['id'] +'" data-client_type="'+ element['client_type'] +'" selected>'+ element['company_name'] +'</option>');
            //<option value="{{--$party->id--}}" data-client_type="{{--$party->client_type--}}" selected>{{--$party->company_name--}}</option>
          });
          $('select[id="party_id"]').multiselect('reload');
        }
      });
    }else{
      $('select[id="party_id"]').empty();
      $('select[id="party_id"]').multiselect('reload');
    }
  });

  $('#productsalesreports').on('submit', function (event) {
      
      event.preventDefault();
      let current = $(this);
      @if(config('settings.ncal')==0)
        var start_date = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end_date = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
      @else
        var start_date = $('#start_edate').val();
        var end_date = $('#end_edate').val();
      @endif
      var report_type = $("input[name='dlywkly']:checked").val();
      var order_status_select = $('.order_status_select').val();
      
      if(start_date==end_date){
        let yr = moment(start_date).format('YYYY');
        let mnth = moment(start_date).format('MM');
        start_date1 = yr + '-' + mnth + '-' + '01';
        var dates = getDates(start_date, end_date, report_type);
      }else{
        var dates = getDates(start_date, end_date, report_type);
      }

      
      var party_ids = $('#party_id').val();
      $('#encoded_party_ids').val(JSON.stringify(party_ids))
      
      if(party_ids.length<=0){
        alert("Please select atleast one party");
        return false;
      }
      $('#datestart').val(start_date);
      $('#dateend').val(end_date);

      if(moment(end_date).diff(moment(start_date), "year") >= 1){
        alert("Please choose time range less than 1 year ");
        return false;
      }
      
      current.unbind("submit").submit();
  });

  var table;
  function initializeOrderDT(){
    table = $('#order').DataTable({
      "stateSave": true,
      language: { search: "" },
      "order": [[ 0, "desc" ]],
      "serverSide": true,
      "processing": false,
      'searching': false,
      "paging": true,
      "columnDefs": [
        {
          "orderable": false,
          "targets":[0,-1],
        },],
      "ajax":{
        "url": "{{ domain_route('company.admin.report.orderDetailsReportDT') }}",
        "dataType": "json",
        "type": "POST",
        "data":{  
          _token: "{{csrf_token()}}", 
        },
        beforeSend:function(url, data){
          $('#mainBox').addClass('box-loader');
          $('#loader1').removeAttr('hidden');
          $('.tips').tooltip();
        },
        error:function(){
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
          $('.tips').tooltip();
        },
        complete:function(data){
          $('.tips').tooltip();
          $('#mainBox').removeClass('box-loader');
          $('#loader1').attr('hidden', 'hidden');
        }
      },
      "columns": [{"data": "date_generated"},
        {"data": "date_range"},
        {"data": "action"},
      ],
      drawCallback:function(settings)
      {
        $('[data-toggle="tooltip"]').tooltip({
          placement : 'right',
          container: 'body'
        });
        $('#next_refresh').val(settings.json.next_refresh);
      }
    });
  }
  
  setInterval(() => {
    if($('#next_refresh').val() > 0){
      table.ajax.reload();
    }
  }, 30000);

</script>
@endsection