<section class="content" id="pi-plot-content">
  <div class="box-body">
    <div class="row">
      <div class="box" id="pi_plot_ltst_box">
        <div class="box-header">
          <h3 class="box-title">Reason For Most Returned Products </h3>
        </div>
        <div class="box-body">
          <form action="#" method="post" id="piPlotForm" style="margin-bottom:10px">
            @csrf
            <div class="row">
              <div class="col-xs-3">
                <label>
                  Select Party Type
                </label>
                <select id="piPlotPartyType" name="pi_plot_party_type" style="width: 100%;" class="parties" multiple>
                  @foreach($partytypes as $key=>$value)
                  <option value="{{$key}}" selected>{{$value}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-xs-3">
                <label>
                  Select Parties
                </label>
                <select id="piPlotHandledPartyId" name="piPlotHandledPartyId[]" class="piPlotHandledPartyId" multiple
                  style="width: 100%;">
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
                <span class="piPlotReportRange" id="piPlotReportRange" name="piPlotReportRange">
                  <i class="fa fa-calendar"></i>&nbsp;
                  <span></span><i class="fa fa-caret-down"></i>
                </span>
              </div>
              <input type="hidden" name="piPlotStartDate" id="piPlotStartDate">
              <input type="hidden" name="piPlotEndDate" id="piPlotEndDate">
              @else
              <div class="col-xs-4">
                <label>
                  Select Date Range
                </label>
                <div class="input-group" id="nepCalDiv">
                  <input id="piPlotStartNdate" class="form-control" type="text" name="piPlotStartNdate"
                    placeholder="Start Date" autocomplete="off" />
                  <input id="piPlotStartEdate" type="text" name="piPlotStartEdate" placeholder="Start Date" hidden />
                  <span class="input-group-addon" aria-readonly="true"><i
                      class="glyphicon glyphicon-calendar"></i></span>
                  <input id="piPlotEndNdate" class="form-control" type="text" name="piPlotEndNdate"
                    placeholder="End Date" autocomplete="off" />
                  <input id="piPlotEndEdate" type="text" name="piPlotEndEdate" placeholder="End Date" hidden />
                </div>
              </div>
              @endif
              <button type="submit" class="btn btn-default sendBtn" id="piPlotGetReport">
                <i class="fa fa-book"></i> Get Report
              </button>
            </div>
          </form>
        </div>
        <div class="box-body" id="canvasBoxBody">
          @include('company.returnsreports._piplot')
        </div>
        <b><small>The count represents the no of times product was returned due to the given reason.</small></b>
      </div>
    </div>
  </div>
</section>