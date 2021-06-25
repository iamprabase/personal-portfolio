<div class="box-body">
  <form id="UpdateContactDetail">
  <div class="col-xs-12">
      @if(Auth::user()->can('party-update'))
      @if(checkpartytypepermission($client->client_type,'update'))
        <span id="ActivateContactEdit" class="btn btn-default btn-sm pull-right partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
        @endif
        <span id="ActivateContactCancel" class="btn btn-default btn-sm pull-right hide partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
        <span id="ActivateContactUpdate" class="hide"><button class="btn btn-default btn-sm pull-right updateBasicPartyDetails partyactionbtn" type="submit"><i class="fa fa-edit"></i>Update</button></span>
      @endif
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
      <div class="media-body"><h4 class="media-heading">Phone Code</h4>
        <p class="text-display" id="cpphonecode">{{ ($client->phonecode)?$client->phonecode:'NA' }}</p>
        <p class="text-form" hidden><input id="cpphonecode_value" readonly="true" name="phone" class="form-control" type="text" value="{{ ($client->phonecode)?$client->phonecode:'' }}" /></p>
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
  <input type="text" name="client_id" value="{{$client->id}}" hidden />
</form>
</div>