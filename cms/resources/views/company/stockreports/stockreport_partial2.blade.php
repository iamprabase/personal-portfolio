<section class="content">
  <div class="row">
    <div id="loader2" style="top:50%; left:60%;" hidden>
      <img src="{{asset('assets/dist/img/loader2.gif')}}" />
      <p
        style="position: absolute;top: 40%;left: 50%;transform: translate(-50%, -50%);text-align: center;font-weight: bolder;">
        Report is being generated.<br /> Depending upon the internet connection it may take several minutes.<br />
        Please come back in a moment.
      </p>
    </div>
    <div class="box" id="party_wise_hist_box">
      <div class="box-header">
        <h3 class="box-title">Single Party Historical Stock Report </h3>
      </div>
      <div class="box-body">
        <div class="col-xs-12">
          <form action="{{domain_route('company.admin.stockreports2')}}" method="post" id="stockreports2"
            style="margin-bottom:10px">
            @csrf
            <div class="row">
              <div class="col-xs-3">
                <label>
                  Select Parties
                </label>
                <select id="party_id2" name="party_id2" class="select2" style="width: 100%;" required>
                  <option></option>
                  @php $decoded_party = json_decode($parties); @endphp
                  {{-- @foreach($decoded_party as $party)
                            <option value="{{$party->id}}"
                  data-client_type="{{$party->client_type}}">{{$party->company_name}}</option>
                  @endforeach --}}
                  {{-- @forelse($decoded_party as $party)
                        <option value="{{$party->id}}"
                  data-client_type="{{$party->client_type}}">{{$party->company_name}}</option>
                  @empty --}}
                  {{-- <option>NULL</option> --}}
                  {{-- @endforelse --}}
                </select>
              </div>
              <div class="col-xs-3">
                <button type="submit" class="btn btn-default" id="getReport2" style="width:100%;margin-top:25px;">
                  <i class="fa fa-book"></i> Get Report
                </button>
              </div>
            </div>
          </form>
        </div>

        <table id="salesman_wise" class="table table-bordered table-striped table-responsive" style="width: 100%;">
          <thead>
            <tr>
              {{-- <th>#</th>
                <th>Product Name</th>
                <th>Variant</th>
                <th>Unit</th>
                <th>Latest Stock Date</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Salesman</th> --}}
              {{-- <th class="hidden">#</th> --}}
              <th>Date Generated</th>
              <th>Report Type</th>
              {{-- <th>Party Name</th> --}}
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @if($single_reports_generated)
            @foreach($single_reports_generated as $reports)
            <tr>
              <td>{{getDeltaDateFormat($reports->date_range)}}</td>
              {{-- <td>{{$reports->report_type}}</td> --}}
              @php $gtClient= getClient($reports->party_id); @endphp
              <td>{{isset($gtClient)?$gtClient->company_name:NULL}} {{$reports->report_type}}</td>
              <td>
                <a href="{{ $reports->download_link}}" download="{{ urldecode($reports->filename)}}">
                  <i class="fa fa-download" aria-hidden="true"></i>
                </a>
              </td>
            </tr>
            @endforeach
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>