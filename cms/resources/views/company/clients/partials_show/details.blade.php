<div class="box-body">
  <form id="UpdateClientDetail">
  <div class="row">
    <div id="imggroup" class="form-group">
      <div class="col-xs-4 imgUp">  
          <div class="imagePreview imageExistsPreview @if(isset($client->image_path)) clientImageExists  @endif"><img class="img-responsive display-imglists" @if(isset($client->image_path)) src="{{ URL::asset('cms'.$client->image_path) }}" @endif /></div><i id="clearImage" class="fa fa-times hide del"></i>
          <label id="lblChange" class="btn btn-primary hide"> Choose<input id="receipt" type="file" name="image" class="uploadFile img" value="cms/{{$client->image_path}}" style="width: 0px;height: 0px;overflow: hidden;"></label>
          <input type="text" id="confirmremove" name="confirmremove" hidden>
      </div>
      <div class="col-xs-8">
        @if(Auth::user()->can('party-update'))
          <span id="ActivateEdit" class="btn btn-default btn-sm pull-right ActivateEdit" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
          <span id="ActivateCancel" class="btn btn-default btn-sm pull-right hide ActivateCancel" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
          <span id="ActivateUpdate" class="hide"><button class="btn btn-default btn-sm pull-right updateBasicPartyDetails ActivateUpdate" type="submit"><i class="fa fa-edit"></i>Update</button></span>
        @endif
      </div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user margin-r-5 icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Contact Person Name</h4>
        <p class="text-display" id="cpname">{{ ($client->name)?$client->name:'NA' }}</p>
        <p class="text-form" hidden>
          <input type="text" name="client_id" value="{{$client->id}}" hidden />
          <input name="name" class="form-control" type="text" value="{{ ($client->name)?$client->name:'NA' }}" /></p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user fa-envelope icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Email</h4>
        <p class="text-display" id="cpemail">{{ ($client->email)?$client->email:'NA' }}</p>
        <p class="text-form" hidden><input name="email" class="form-control" type="text" value="{{ ($client->email)?$client->email:'' }}" /></p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user  fa-phone icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Phone</h4>
        <p class="text-display" id="cpphone">{{ ($client->phone)?$client->phone:'NA' }}</p>
        <p class="text-form" hidden><input name="phone" class="form-control" type="text" value="{{ ($client->phone)?$client->phone:'' }}" /></p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user fa-mobile icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Mobile</h4>
        <p class="text-display" id="cpmobile">{{ ($client->mobile)?$client->mobile:'NA' }}</p>
        <p class="text-form" hidden><input name="mobile" class="form-control" type="text" value="{{ ($client->mobile)?$client->mobile:'' }}" /></p>
      </div>
    </div>
  </div>

  @if(count($partytypes)>0)
  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user-secret icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Party Type</h4>
        <p class="text-display" id="cppartytype">{{ ($client->client_type)?$client->partytype_name:'NA' }}</p>
        <p class="text-form" hidden>
          @if(isset($client->id) && hasChild($client->id)) 
          <input type="text" name="client_type" value="{{$client->client_type}}" hidden>
          <select name="client-type" class="form-control select2" disabled="disabled">
            @foreach($partytypes as $partytype)
              <option @if($partytype->id==$client->client_type) selected="selected" @endif value="{{$partytype->id}}">{{$partytype->name}}</option>
              @if(count($partytype->childs)>0)
                @include('company.clients.partials_show.partyChilds',['childs' => $partytype->childs])
              @endif
            @endforeach
          </select>
          @else
          <select name="client_type" class="form-control select2" required>
            @foreach($partytypes as $partytype)
              <option  @if(isset($client->id)) @if($partytype->id==$client->client_type) selected="selected" @endif @endif value="{{$partytype->id}}">{{$partytype->name}}</option>
              @if(count($partytype->childs)>0)
                @include('company.clients.partials_show.partyChilds',['childs' => $partytype->childs])
              @endif
            @endforeach
          </select>
          @endif
        </p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user-secret icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Superior</h4>
        <p class="text-display" id="cpsuperior">{{ ($client->superior)?(getClient($client->superior)?getClient($client->superior)->company_name:'N/A'):'N/A' }}</p>
        <p class="text-form" hidden>
          {{-- <input name="mobile" class="form-control" type="text" value="{{ ($client->mobile)?$client->mobile:'NA' }}" /> --}}
          {!! Form::select('superior', [null => 'Select Superior'] + $superiors, isset($client)?$client->superior:null, ["class" => "form-control select2", "id" => "superior"]) !!}
        </p>
      </div>
    </div>
  </div>
  @endif

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user-secret icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Business Type</h4>
        <p class="text-display" id="cpbtype">{{($client->business_id)?$client->business->business_name:"N/A"}}</p>
        <p class="text-form" hidden>
          <select name="business_id" class="form-control select2">
            <option value="null">Select Business Type</option>
            @foreach($businessTypes as $businessType)
              <option @if( $businessType->id==$client->business_id ) selected="selected"  @endif value="{{$businessType->id}}">{{$businessType->business_name}}</option>
            @endforeach
          </select>
        </p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-qrcode icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Party Code</h4>
        <p class="text-display" id="cpPcode">{{ ($client->client_code)?$client->client_code:'N/A' }}</p>
        <p class="text-form" hidden><input name="client_code" class="form-control" type="text" value="{{ ($client->client_code)?$client->client_code:'' }}" /></p>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-internet-explorer icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Website</h4>
        <p class="text-display" id="cpWebsite">{{ ($client->website)?$client->website:'N/A' }}</p>
        <p class="text-form" hidden><input name="website" class="form-control" type="text" value="{{ ($client->website)?$client->website:'' }}" placeholder="website" /></p>
      </div>
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
        <i class="fa fa-qrcode icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">PAN/VAT</h4>
        <p class="text-display" id="cpPan">{{ ($client->pan)?$client->pan:'N/A' }}</p>
        <p class="text-form" hidden><input name="pan" class="form-control" type="text" value="{{ ($client->pan)?$client->pan:'' }}" /></p>
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

  <div class="col-xs-6 text-display">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user fa-calendar icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Added On</h4>
        <p>{{ ($client->created_at)? getDeltaDate(date("Y-m-d", strtotime($client->created_at))):'NA' }}
        </p>
      </div>
    </div>
  </div>

  <div class="col-xs-12">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-map-marker icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Location</h4>
{{--         <p class="text-display" id="cpmobile">{{ ($client->pin)?$client->pin:'N/A' }}</p> --}}
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

  <div class="col-xs-12">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-building icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">About Company</h4>
        <p class="text-display" id="cpabout">
          {{ ($client->about)?strip_tags($client->about):'' }}
        </p>
        <p class="text-form" hidden>
          <input type="text" name="about" id="aboutCOM" hidden/>
          <textarea class="form-control" id="aboutCompany">{{ ($client->about)?$client->about:'' }}</textarea>
        </p>
      </div>
    </div>
  </div>
</form>
</div>