<div class="box-body">
  <form id="UpdateBasicDetail">
  <div class="row">
    <div class="col-xs-12">
        @if(Auth::user()->can('party-update'))
        @if(checkpartytypepermission($client->client_type,'update'))
          <span id="ActivateEdit" class="btn btn-default btn-sm pull-right partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
          @endif
          <span id="ActivateCancel" class="btn btn-default btn-sm pull-right hide partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
          <span id="ActivateUpdate" class="hide"><button class="btn btn-default btn-sm pull-right updateBasicPartyDetails partyactionbtn" type="submit"><i class="fa fa-edit"></i>Update</button></span>
        @endif
    </div>
  </div>

  <div class="row">
    <div id="imggroup" class="form-group">
      <div class="col-xs-4 imgUp">  
          <div class="imagePreview imageExistsPreview @if(isset($client->image_path)) clientImageExists  @endif"><img class="img-responsive display-imglists" @if(isset($client->image_path)) src="{{ URL::asset('cms'.$client->image_path) }}" @endif /></div><i id="clearImage" class="fa fa-times hide del"></i>
          <label id="lblChange" class="btn btn-primary hide"> Choose<input id="receipt" type="file" name="image" class="uploadFile img" value="cms/{{$client->image_path}}" style="width: 0px;height: 0px;overflow: hidden;"></label>
          <input type="text" id="confirmremove" name="confirmremove" hidden>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="media left-list bottom-border">
      <div class="media-left">
        <i class="fa fa-user-secret margin-r-5 icon-size"></i>
      </div>
      <div class="media-body"><h4 class="media-heading">Party Name</h4>
        <p class="text-display" id="cparty_name">{{ ($client->company_name)?$client->company_name:'NA' }}</p>
        <p class="text-form" hidden>
          <input type="text" name="client_id" value="{{$client->id}}" hidden />
          <input name="company_name" class="form-control" type="text" value="{{ ($client->company_name)?$client->company_name:'NA' }}" /></p>
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
          <input name="name" class="form-control" type="text" value="{{ ($client->name)?$client->name:'NA' }}" /></p>
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