<section class="content" id="party-wise-content">
  <div class="box-body">
    <div class="row">
      <div id="loader4" hidden>
        <img src="{{asset('assets/dist/img/loader2.gif')}}" />
        <p
          style="position: absolute;top: 40%;left: 50%;transform: translate(-50%, -50%);text-align: center;font-weight: bolder;">
          Report is being generated.<br /> Depending upon the internet connection it may take several minutes.<br />
          Please come back in a moment.
        </p>
      </div>
      <div class="box" id="product_party_wise_ltst_box">
        <div class="box-header">
          <h3 class="box-title">Product-Party-wise Returns Report </h3>
          <span id="product_party_wiseexports" class="pull-right"></span>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-xs-3">
              <label>
                Select Party Type
              </label>
              <select id="product_party_wise_partytype" name="product_party_wise_partytype" style="width: 100%;" class="parties" multiple>
                @foreach($partytypes as $key=>$value)
                <option value="{{$key}}" selected>{{$value}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-xs-3">
              <label>
                Select Parties
              </label>
              <select id="product_party_wise_partylist" name="product_party_wise_partylist" style="width: 100%;" class="parties" multiple>
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
              <span class="reportrange" id="product_party_wise_reportrange" name="reportrange">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span><i class="fa fa-caret-down"></i>
              </span>
            </div>
            <input type="hidden" name="start_date" id="productpartywise_hidden_startdate">
            <input type="hidden" name="end_date" id="productpartywise_hidden_enddate">
            @else
            <div class="col-xs-4">
              <label>
                Select Date Range
              </label>
              <div class="input-group" id="nepCalDiv">
                <input id="productpartywise_start_ndate" class="form-control" type="text" name="start_ndate" placeholder="Start Date"
                  autocomplete="off" />
                <input id="productpartywise_hidden_start_edate" type="text" name="start_edate" placeholder="Start Date" hidden />
                <span class="input-group-addon" aria-readonly="true"><i class="glyphicon glyphicon-calendar"></i></span>
                <input id="productpartywise_end_ndate" class="form-control" type="text" name="end_ndate" placeholder="End Date"
                  autocomplete="off" />
                <input id="productpartywise_hidden_end_edate" type="text" name="end_edate" placeholder="End Date" hidden />
              </div>
            </div>
            @endif
            <button type="button" class="btn btn-default sendBtnProdParty" id="product-party-wise-submit">
              <i class="fa fa-book"></i> Get Report
            </button>
          </div>
        </div>
        <div class="box-body">
          <table id="product-party-wise-table" class="table table-bordered table-striped table-responsive" style="width: 100%;">
            <thead>
              <tr>
                <th>Product Name</th>
                <th>Quantity</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>