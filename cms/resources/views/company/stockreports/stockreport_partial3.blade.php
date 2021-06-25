<section class="content">
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
        <h3 class="box-title">Parties Latest Stock Report by Date </h3>
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
        <form action="{{domain_route('company.admin.stockreports3')}}" method="post" id="stockreport3"
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
              <select id="handled_party_id" class="parties" multiple style="width: 100%;">
                @php $decoded_party = json_decode($parties); @endphp
                {{-- @foreach($decoded_party as $party)
                        <option value="{{$party->id}}" data-client_type="{{$party->client_type}}"
                selected>{{$party->company_name}}</option>
                @endforeach --}}
                @forelse($decoded_party as $party)
                <option value="{{$party->id}}" data-client_type="{{$party->client_type}}" selected>
                  {{$party->company_name}}</option>
                @empty
                <option></option>
                @endforelse
              </select>
              <input type="hidden" class="hiddenPartyId" name="party_id">
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
            {{-- <input type="hidden" name="startDate" id="startdate">
            <input type="hidden" name="endDate" id="enddate"> --}}
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
            {{-- @if($multiple_reports_generated)
            @foreach($multiple_reports_generated as $reports)
            <tr>
              <td>{{getDeltaDateFormat($reports->created_at->format('Y-m-d'))}}</td>
              <td>@if(count(json_decode($reports->party_id))>1) Multiple Party @else @php $gtClient =
                getClient(json_decode($reports->party_id)); @endphp {{isset($gtClient)?$gtClient->company_name:NULL}}
                @endif {{ $reports->report_type }} @if($reports->report_cat!="Multiple
                Party"){{ $reports->report_cat }}@endif
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
                  <a href="{{ $reports->download_link}}" download="{{urldecode($reports->filename)}}">
                  <i class="fa fa-download" aria-hidden="true"></i>
                  </a>
                @else
                  @if($reports->processing==1)
                    <a href="#"><i class="fa fa-spinner fa-pulse fa-fw"></i>Processing</a>
                  @else
                    <a href="#"><i class="fa fa-spinner fa-pulse fa-fw"></i>Pending</a>
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
</section>