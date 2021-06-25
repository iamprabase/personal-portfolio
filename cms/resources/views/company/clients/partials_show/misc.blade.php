<div class="box-body">
  <form id="UpdateMiscDetail">
  <div class="row">
    <div class="col-xs-12">
        @if(Auth::user()->can('party-update'))
        @if(checkpartytypepermission($client->client_type,'update'))
          <span id="ActivateMiscEdit" class="btn btn-default btn-sm pull-right partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
          @endif
          <span id="ActivateMiscCancel" class="btn btn-default btn-sm pull-right hide partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
          <span id="ActivateMiscUpdate" class="hide"><button class="btn btn-default btn-sm pull-right updateBasicPartyDetails partyactionbtn" type="submit"><i class="fa fa-edit"></i>Update</button></span>
        @endif
    </div>
  </div> 
 

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-check-square-o icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Status</h4>
        <p class="text-display" id="cpstatus">{{ (isset($client->status))?$client->status:'N/A' }}</p>
        <p class="text-form" hidden>
          <select name="status" class="form-control">
            <option @if($client->status=="Active") selected="selected" @endif value="Active">Active</option>
            <option @if($client->status=="Inactive") selected="selected" @endif value="Inactive">Inactive</option>
          </select>
        </p>
      </div>
    </div>
  </div>
  @if(config('settings.party_wise_rate_setup')==1)
  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-money icon-size"></i>
      </div>
      <div class="media-body">
        <h4 class="media-heading">Order Rates</h4>
        <p class="text-display" id="cprate">{{ (isset($client->rate_id))?$client->rates->name:'Default' }}</p>
        <p class="text-form" hidden>
          <select name="rate_id" class="form-control">
            <option value="">Default</option>
            @foreach($rates as $rate_id=>$rate_name)
              <option value="{{$rate_id}}" @if($rate_id==$client->rate_id) selected @endif>{{$rate_name}}</option>
            @endforeach
          </select>
        </p>
      </div>
    </div>
  </div>

  @endif

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user fa-briefcase icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Added By</h4>
        <p class="text-display" id="createdBy">@if($client->created_by == 0)
            {{ Auth::user()->name.' (Admin)' }}
          @else
            {{ getEmployee($client->created_by)['name'] }}
          @endif
        </p>
        <p class="text-form" hidden>
          @if($client->created_by == 0)
            <select class="form-control select2" name="employee_id" disabled>
              @if(Auth::user()->isCompanyManager())
              <option value="0" selected="selected">{{ Auth::user()->name.' (Admin)' }}</option>
              @else
              <option></option>
              @endif
              @foreach($employees as $employee)
              <option value="{{$employee->id}}">{{$employee->name}}</option>
              @endforeach
            </select>
          @else
            <select class="form-control select2" name="employee_id" disabled>
              <option value="0">{{ Auth::user()->name.' (Admin)' }}</option>
              @foreach($employees as $employee)
              <option @if($client->created_by==$employee->id) selected="selected" @endif value="{{$employee->id}}">{{$employee->name}}</option>
              @endforeach
            </select>
          @endif
        </p>
      </div>
    </div>
  </div>

  
  @if(config('settings.category_wise_rate_setup')==1)
  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-money icon-size"></i>
      </div>
      <div class="media-body">
        <h4 class="media-heading">Category Rates</h4>
        <p class="text-display" id="cprate">{{ count($current_category_rates_name) > 0?implode(',', $current_category_rates_name):'Default' }}</p>
        <p class="text-form" hidden>
        <select name="category_rates[]" class="form-control" id="categoryRates" multiple>
              
            @foreach($category_with_rates as $category_with_rate)
              <optgroup label="{{$category_with_rate['name']}}">
                @foreach($category_with_rate['categoryrates'] as $rate)
                  <option value="{{$rate['id']}}" data-categoryid="{{$category_with_rate['id']}}">
                  {{$rate['name']}}
                  </option>
                @endforeach
              </optgroup>
            @endforeach
            
            
          </select>
        </p>
      </div>
    </div>
  </div>

  @endif
  <input type="text" name="client_id" value="{{$client->id}}" hidden />  
</form>
</div>