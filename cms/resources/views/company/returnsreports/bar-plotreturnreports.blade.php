<section class="content" id="bar-plot-content">
  <div class="box-body">
    <div class="row">
      <div class="box" id="bar_plot_ltst_box">
        <div class="box-header">
          <h3 class="box-title">Top 10 Most Returned Products</h3>
        </div>
        <div class="box-body">
          <form action="#" method="post" id="barPlotForm" style="margin-bottom:10px">
            @csrf
            <div class="row">
              <div class="col-xs-3">
                <label>
                  Select Party Type
                </label>
                <select id="barPlotPartyType" name="bar_plot_party_type" style="width: 100%;" class="parties" multiple>
                  @foreach($partytypes as $key=>$value)
                  <option value="{{$key}}" selected>{{$value}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-xs-3">
                <label>
                  Select Parties
                </label>
                <select id="barPlotHandledPartyId" name="barPlotHandledPartyId[]" class="barPlotHandledPartyId" multiple style="width: 100%;">
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
                  <span class="barPlotReportRange" id="barPlotReportRange" name="barPlotReportRange">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span><i class="fa fa-caret-down"></i>
                  </span>
                </div>
                <input type="hidden" name="barPlotStartDate" id="barPlotStartDate">
                <input type="hidden" name="barPlotEndDate" id="barPlotEndDate">
              @else
                <div class="col-xs-4">
                  <label>
                    Select Date Range
                  </label>
                  <div class="input-group" id="nepCalDiv">
                    <input id="barPlotStartNdate" class="form-control" type="text" name="barPlotStartNdate" placeholder="Start Date"
                      autocomplete="off" />
                    <input id="barPlotStartEdate" type="text" name="barPlotStartEdate" placeholder="Start Date" hidden />
                    <span class="input-group-addon" aria-readonly="true"><i
                        class="glyphicon glyphicon-calendar"></i></span>
                    <input id="barPlotEndNdate" class="form-control" type="text" name="barPlotEndNdate" placeholder="End Date"
                      autocomplete="off" />
                    <input id="barPlotEndEdate" type="text" name="barPlotEndEdate" placeholder="End Date" hidden />
                  </div>
                </div>
              @endif
              <button type="submit" class="btn btn-default sendBtn" id="barPlotGetReport">
                <i class="fa fa-book"></i> Get Report
              </button>
            </div>
            {{-- <div class="col-xs-4 col-xs-offset-4">
              <button type="submit" class="btn btn-default" id="barPlotGetReport" style="width:100%;margin-top:25px;">
                <i class="fa fa-book"></i> Get Report
              </button>
            </div> --}}
          </form>
        </div>
        <div class="box-body" id="canvasBoxBody">
          
          {{-- <div id="barPlotLoader" style="top:50%; left:60%;width: 100%;height: 100%;">
            <img src="{{asset('assets/dist/img/loader2.gif')}}" />
            <p
              style="position: absolute;top: 40%;left: 50%;transform: translate(-50%, -50%);text-align: center;font-weight: bolder;">
              Report is being generated.<br /> Depending upon the internet connection it may take several minutes.<br />
              Please come back in a moment.
            </p>
          </div> --}}
          {{-- <canvas id="mycanvas" class="canvas"></canvas> --}}
          @include('company.returnsreports._barplot')
        </div>
      </div>
    </div>
  </div>
</section>