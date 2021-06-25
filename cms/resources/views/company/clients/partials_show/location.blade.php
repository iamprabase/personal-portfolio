<div class="box-body">
  <form id="UpdateLocationDetail">
  <div class="row">
    <div class="col-xs-12">
        @if(Auth::user()->can('party-update'))
        @if(checkpartytypepermission($client->client_type,'update'))
          <span id="ActivateLocationEdit" class="btn btn-default btn-sm pull-right partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
          @endif
          <span id="ActivateLocationCancel" class="btn btn-default btn-sm pull-right hide partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
          <span id="ActivateLocationUpdate" class="hide"><button class="btn btn-default btn-sm pull-right updateBasicPartyDetails partyactionbtn" type="submit"><i class="fa fa-edit"></i>Update</button></span>
        @endif
    </div>
  </div> 

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-flag icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Country</h4>
        <p class="text-display" id="cpcountry">{{ ($client->country)?$client->country_name:'N/A' }}</p>
        <p class="text-form" hidden>
          {!! Form::select('country', [null => 'Select a Country'] + $countries, isset($client)? ($client->country):((config('settings.ncal')==1)?153:null), ["class" => "form-control select2", "id" => "country",'required']) !!}
        </p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-flag icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">State</h4>
        <p class="text-display" id="cpstate">{{ $client->client_state_name }}</p>
        <p class="text-form" hidden>
          {!! Form::select('state', [null => 'Select a State']+$states, isset($client)? $client->state:null, ["class" => "form-control select2", "id" => "state"]) !!}
        </p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-flag icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">City</h4>
        <p class="text-display" id="cpcity">{{ $client->client_city_name}}</p>
        <p class="text-form" hidden>
          {!! Form::select('city', [null => 'Select a City']+$cities, isset($client)?$client->city:null, ["class" => "form-control select2", "id" => "city"]) !!}
        </p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-address-card icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Address Line 1</h4>
        <p class="text-display" id="cpaddress1">{{ ($client->address_1)?$client->address_1:'N/A' }}</p>
        <p class="text-form" hidden><input name="address_1" class="form-control" type="text" value="{{ ($client->address_1)?$client->address_1:'' }}" /></p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-address-card icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Address Line 2</h4>
        <p class="text-display" id="cpaddress2">{{ ($client->address_2)?$client->address_2:'N/A' }}</p>
        <p class="text-form" hidden><input name="address_2" class="form-control" type="text" value="{{ ($client->address_2)?$client->address_2:'' }}" /></p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-qrcode icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Pin Code</h4>
        <p class="text-display" id="cppin">{{ ($client->pin)?$client->pin:'N/A' }}</p>
        <p class="text-form" hidden><input name="pin" class="form-control" type="text" value="{{ ($client->pin)?$client->pin:'' }}" /></p>
      </div>
    </div>
  </div>

  @if(config('settings.beat')==1)
  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-tasks icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Beat</h4>
        <p class="text-display" id="cpbeat">{{ (isset($currentBeatName))?$currentBeatName:'N/A' }}</p>
        <p class="text-form" hidden>
            @if(isset($currentBeatID))
              <select name="beat" class="form-control select2">
                <option value="null">Select Beat</option>
                @foreach($beats as $beat)
                <option @if($beat->id == $currentBeatID) selected="selected" @endif value="{{$beat->id}}">{{$beat->name}}</option>
                @endforeach
              </select>
            @else
              <select name="beat" class="form-control select2">
                <option value="null">Select Beat</option>
                @foreach($beats as $beat)
                <option value="{{$beat->id}}">{{$beat->name}}</option>
                @endforeach
              </select>
            @endif
        </p>
      </div>
    </div>
  </div>
  @endif    

  <div class="col-xs-12">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-map-marker icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Location</h4>
        <p>
          @if(isset($client->location))
            {!! Form::text('location' , $client->location, ['class' => 'form-control', 'placeholder' => 'Enter Location', 'id'=>"search_addr"]) !!}
            {!! Form::hidden('lat' , $client->latitude, ['class' => 'form-control', 'name'=>'lat', 'id'=>'search_latitude']) !!}
            {!! Form::hidden('lng' , $client->longitude, ['class' => 'form-control', 'name'=>'lng','id'=>'search_longitude']) !!}
          @else
            {!! Form::text('location' , null, ['class' => 'form-control', 'placeholder' => 'Enter Location', 'id'=>"search_addr",'required']) !!}
            {!! Form::hidden('lat' , null, ['class' => 'form-control', 'name'=>'lat', 'id'=>'search_latitude']) !!}
            {!! Form::hidden('lng' , null, ['class' => 'form-control', 'name'=>'lng','id'=>'search_longitude']) !!}
          @endif
        </p>
        <div class="form-group" id="geomap" style="width: 100%; height: 400px;"></div>
      </div>
    </div>
  </div>
  <input type="text" name="client_id" value="{{$client->id}}" hidden />
</form>
</div>