{{-- @extends('layouts.company')
@section('title', 'Return Report')

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
    width:fit-content;
  }

  .brandcat {
    margin: 0 8px 0 0;
    padding: 0.2em 1.6em 0.3em;
    border-radius: 10rem;
    font-size: x-small;
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

@section('content') --}}
<section class="content" id="party-wise-content">
  <div class="box-body">
    <div class="row">
      <div id="loader3" style="top:50%; left:60%;width: 100%;height: 100%;" hidden>
        <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        <p
          style="position: absolute;top: 40%;left: 50%;transform: translate(-50%, -50%);text-align: center;font-weight: bolder;">
          Report is being generated.<br /> Depending upon the internet connection it may take several minutes.<br />
          Please come back in a moment.
        </p>
      </div>
      <div class="box" id="party_wise_ltst_box">
        <div class="box-header">
          <h3 class="box-title">Party-wise Returns Report </h3>
        </div>
        <div class="box-body">
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
          <form action="{{domain_route('company.admin.postReturnsReport')}}" method="post" id="reportFormSubmit"
            style="margin-bottom:10px">
            @csrf
            <div class="row">
              <div class="col-xs-3">
                <label>
                  Select Party Type
                </label>
                <select id="party_type" name="party_type[]" style="width: 100%;" class="parties" multiple>
                  @foreach($partytypes as $key=>$value)
                  <option value="{{$key}}" selected>{{$value}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-xs-3">
                <label>
                  Select Parties
                </label>
                <select id="handled_party_id" name="party_id[]" class="parties" multiple style="width: 100%;">
                  @php $decoded_party = json_decode($parties); @endphp
                  @forelse($decoded_party as $party)
                  <option value="{{$party->id}}" data-client_type="{{$party->client_type}}" selected>
                    {{$party->company_name}}</option>
                  @empty
                  <option></option>
                  @endforelse
                </select>
              </div>
              @if(config('settings.ncal')==0)
              <div class="col-xs-3">
                <label>
                  Select Date Range
                </label>
                <span class="reportrange" id="reportrange" name="reportrange">
                  <i class="fa fa-calendar"></i>&nbsp;
                  <span></span><i class="fa fa-caret-down"></i>
                </span>
              </div>
              {{-- <input type="hidden" name="start_date" id="startdate">
              <input type="hidden" name="end_date" id="enddate"> --}}
              @else
              <div class="col-xs-4">
                <label>
                  Select Date Range
                </label>
                <div class="input-group" id="nepCalDiv">
                  <input id="start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date"
                    autocomplete="off" />
                  <input id="start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                  <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                  <input id="end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                    autocomplete="off" />
                  <input id="end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
                </div>
              </div>
              @endif
              <input type="hidden" name="startDate" id="startdate">
              <input type="hidden" name="endDate" id="enddate">
            </div>
            <div class="row">
              <div class="col-xs-8 col-xs-offset-3" style="padding-top:20px;">
                <div class="col-xs-4" style="width:max-content;">
                  <label>
                    Which report do you want?
                  </label>
                </div>
                <div class="col-xs-4" style="width:max-content;">
                  <label class="radio inline">
                    <input class="report_type" type="radio" name="reptype" value="brand" checked>By Brand
                  </label>
                </div>
                <div class="col-xs-4" style="width:max-content;">
                  <label class="radio inline">
                    <input class="report_type" type="radio" name="reptype" value="category">By Category
                  </label>
                </div>
                <div class="col-xs-4" style="width:max-content;">
                  <label class="radio inline">
                    <input class="report_type" type="radio" name="reptype" value="consolidated">Consolidated
                  </label>
                </div>
              </div>
            </div>
            <div class="col-xs-4 col-xs-offset-4">
              <button type="submit" class="btn btn-default" id="getReport3" style="width:100%;margin-top:25px;">
                <i class="fa fa-book"></i> Get Report
              </button>
            </div>
          </form>
  
          <table id="latest_by_date" class="table table-bordered table-striped table-responsive" style="width: 100%;">
            <thead>
              <tr>
                <th>Date Generated</th>
                <th>Report Type</th>
                <th>Date Range</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              {{-- @if($reports_generated->first())
              @foreach($reports_generated as $reports)
              <tr>
                <td>{{getDeltaDateFormat($reports->created_at->format('Y-m-d'))}}</td>
                <td>@if(count(json_decode($reports->party_id))>1) 
                Multiple Party 
                @else 
                  @php $gtClient = getClient(json_decode($reports->party_id)); @endphp 
                  {{isset($gtClient)?$gtClient->company_name:NULL}}
                @endif 
                {{ $reports->report_type }} 
                
                @if($reports->report_cat!="Multiple
                  Party"){{ $reports->report_cat }}
                @endif
                  @php $decode_party=json_decode($reports->party_id)@endphp
                  <span class="fa fa-info-circle" aria-hidden="true" data-html="true" data-toggle="tooltip"
                    data-original-title="<b>The report was generated for following parties and salesman:-</b><br/><b>Parties:-</b>@if(count(json_decode($reports->party_id))==1) {{isset(getClient(implode(',',json_decode($reports->party_id)))->company_name)?getClient(implode(',',json_decode($reports->party_id)))->company_name:'N/A'}} @else @foreach($decode_party as $id){{isset(getClient($id)->company_name)?getClient($id)->company_name.',':NULL}} @endforeach @endif <br/>">
                  </span>
                </td>
                @if(isset($reports->start_date) && isset($reports->end_date))
                @if($reports->start_date == $reports->end_date)
                <td>{{getDeltaDateFormat($reports->start_date)}}</td>
                @else
                <td>{{getDeltaDateFormat($reports->start_date)}} to {{getDeltaDateFormat($reports->end_date)}}</td>
                @endif
                @else
                <td>{{$reports->date_range}}</td>
                @endif
                <td>
                  @if(!empty($reports->download_link))
                  <a href="{{ $reports->download_link}}" id="download_button"
                    download="{{ urldecode($reports->filename)}}">
                    <i class="fa fa-download" aria-hidden="true"></i>
                  </a>
                  @else
                    @if($reports->processing==1)
                    <a href="#">
                      <i class="fa fa-spinner fa-pulse fa-fw"></i>Processing</a>
                    @else
                      <a href="#">
                      <i class="fa fa-spinner fa-pulse fa-fw"></i>Pending</a>
                    @endif 
                  @endif
                </td>
              </tr>
              @endforeach
              @endif --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
{{-- @endsection --}}
{{-- @section('scripts')
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
@if(config('settings.ncal')==1)
<script src="{{asset('assets/plugins/nepaliDate/nepali.datepicker.v2.2.min.js') }}"></script>
@else
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endif
<script type="text/javascript">
  $('[data-toggle="tooltip"]').tooltip({
    placement : 'right',
    container: 'body'
  });

  // Parties Latest Stock Report by Date
  $('#latest_by_date').DataTable({
     "dom": "<'row'<'col-xs-6 alignleft'l><'col-xs-6'>>"+
              "<'row'<'col-xs-4'><'col-xs-4'>>"+
              "<'row'<'col-xs-12't>><'row'<'col-xs-12'ip>>", 
    "columnDefs": [
    { "orderable": false, "targets": -1 }
    ],
    'searching':false,
    'sorting': false,
  });

  $('#party_type').multiselect({
    placeholder: "Select Party Type",
    selectAll: true,
  });

  $('#handled_party_id').multiselect({
    placeholder: "Select Parties",
    selectAll: true,
    search: true,
  });

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
          $('select[id="handled_party_id"]').empty();
          $('select[id="handled_party_id"]').multiselect('reload');
          data.forEach(element => {
            $('select[id="handled_party_id"]').append('<option value="'+ element['id'] +'" data-client_type="'+ element['client_type'] +'" selected>'+ element['company_name'] +'</option>');
          });
          $('select[id="handled_party_id"]').multiselect('reload');
        }
      });
    }else{
      $('select[id="handled_party_id"]').empty();
      $('select[id="handled_party_id"]').multiselect('reload');
    }
  });

  $('#reportFormSubmit').on('submit', function (e) {
    event.preventDefault();
    var party_ids = $('#handled_party_id').val();  
    @if(config('settings.ncal')==0)
      var startDate = $('#reportrange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#reportrange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    @else
      var startDate = $('#start_edate').val();
      var endDate = $('#end_edate').val();
    @endif
    var report_type = $('input[name=reptype]:checked').val();
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      type: "POST",
      url: "{{ domain_route('company.admin.postReturnsReport') }}",
      data:{
        party_id: party_ids,
        report_type: report_type,
        startDate : startDate,
        endDate : endDate,
      },
      beforeSend: function (url, data) { 
        $('#getReport3').text('Please wait ...');
        $('#getReport3').attr('disabled', true);
        $('#loader3').removeAttr('hidden');
        $('#party_wise_ltst_box').addClass('box-opacity');
      },
      success: function (data) {
        alert(data['msg']);
        $('#getReport3').attr('disabled', false);
        $('#getReport3').html("<i class='fa fa-book'></i> Get Report");
        $('#loader3').attr('hidden', 'hidden');
        $('#party_wise_ltst_box').removeClass('box-opacity');
        if(data['msg']!="No Records"){
          location.reload();
        }
      },
      error: function (jqXHR) {
        $('#getReport3').attr('disabled', false);
        $('#getReport3').html("<i class='fa fa-book'></i> Get Report");
        $('#loader3').attr('hidden', 'hidden');
        $('#party_wise_ltst_box').removeClass('box-opacity');
      },
      complete: function () {
        $('#getReport3').attr('disabled', false);
        $('#getReport3').html("<i class='fa fa-book'></i> Get Report");
        $('#loader3').attr('hidden', 'hidden');
        $('#party_wise_ltst_box').removeClass('box-opacity');
      }
    });
  });

  @if(config('settings.ncal')==0)
    $('.fromDate').datepicker({
      format: "yyyy-mm-dd",
      endDate: new Date(),
      autoclose: true,
      orientation: 'bottom'
    });
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
  //responsive 
  $('#reportrange').on('click',function(){
    if ($(window).width() <= 320) {   
      $(".daterangepicker").addClass("returnreportdateposition");
      
    }
    else if ($(window).width() <= 768) {
      $(".daterangepicker").addClass("returnreportdateposition");
    }
    else {   
      $(".daterangepicker").removeClass("returnreportdateposition");
    }
  });
</script>
@endsection --}}