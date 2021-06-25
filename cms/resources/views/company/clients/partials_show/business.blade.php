<div class="box-body">
  <form id="UpdateBusinessDetail">
  <div class="row">
    <div class="col-xs-12">
        @if(Auth::user()->can('party-update'))
           @if(checkpartytypepermission($client->client_type,'update'))
          <span id="ActivateBusinessEdit" class="btn btn-default btn-sm pull-right partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
          @endif
          <span id="ActivateBusinessCancel" class="btn btn-default btn-sm pull-right hide partyactionbtn" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
          <span id="ActivateBusinessUpdate" class="hide"><button class="btn btn-default btn-sm pull-right updateBasicPartyDetails partyactionbtn" type="submit"><i class="fa fa-edit"></i>Update</button></span>
        @endif
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
          <select name="client-type" class="form-control select2" id="client-type" disabled="disabled">
            <option></option>
            @foreach($partytypes as $partytype)
            <?php 
                $stringName = str_replace(' ','-',$partytype->name).'-'.'create';
                $permission = \Spatie\Permission\Models\Permission::where('company_id',config('settings.company_id'))->where('name',$stringName)->first();
                $hasPartyPermission = false;
                if($permission){
                  $hasPartyPermission = $partytype->id==$client->client_type?true:Auth::user()->hasPermissionTo($permission->id);
                }else{
                  $hasPartyPermission = $partytype->id==$client->client_type?true:false;
                }
              ?>
              @if($hasPartyPermission)
              <option @if($partytype->id==$client->client_type) selected="selected" @endif value="{{$partytype->id}}">{{$partytype->name}}</option>
              @endif
              @if(count($partytype->childs)>0)
                @include('company.clients.partials_show.partyChilds',['childs' => $partytype->childs])
              @endif
            @endforeach
          </select>
          @else
          <select name="client_type" class="form-control select2" id="client-type" required>
            <option></option>
            @foreach($partytypes as $partytype)
            <?php 
                $stringName = str_replace(' ','-',$partytype->name).'-'.'create';
                $permission = \Spatie\Permission\Models\Permission::where('company_id',config('settings.company_id'))->where('name',$stringName)->first();
                $hasPartyPermission = false;
                if(isset($client->id)){
                  if($permission){
                    $hasPartyPermission = $partytype->id==$client->client_type?true:Auth::user()->hasPermissionTo($permission->id);
                  }else{
                    $hasPartyPermission = $partytype->id==$client->client_type?true:false;
                  }
                }else{
                  $hasPartyPermission = $permission?Auth::user()->hasPermissionTo($permission->id):false;
                }
              ?>
              @if($hasPartyPermission)
              <option  @if(isset($client->id)) @if($partytype->id==$client->client_type) selected="selected" @endif @endif value="{{$partytype->id}}">{{$partytype->name}}</option>
              @endif
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
        <p class="text-display" id="cpsuperior">{{ ($client->superior)?(getClient($client->superior)?getClient($client->superior)->company_name:$companyName):$companyName }}</p>
        <p class="text-form" hidden>
          {{-- <input name="mobile" class="form-control" type="text" value="{{ ($client->mobile)?$client->mobile:'NA' }}" /> --}}
          {!! Form::select('superior', [null => $companyName] + $superiors, isset($client)?$client->superior:null, ["class" => "form-control select2", "id" => "superior"]) !!}
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
          <select name="business_id" class="form-control select2 business_id">
            <option></option>
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
      <div class="media-body"><h4 class="media-heading">PAN/VAT/GST no.</h4>
        <p class="text-display" id="cpPan">{{ ($client->pan)?$client->pan:'N/A' }}</p>
        <p class="text-form" hidden><input name="pan" class="form-control" type="text" value="{{ ($client->pan)?$client->pan:'' }}" /></p>
      </div>
    </div>
  </div>
  <input type="text" name="client_id" value="{{$client->id}}" hidden />
</form>
</div>