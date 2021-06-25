<div class="row">
  <form role="form" id="tallydetails" method="POST"
                  action="{{domain_route('company.admin.settingnew.storetally')}}">
                  {{csrf_field()}}
                  <input type="hidden" name="id" value="{{$clientSettings->id}}">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('username')) has-error @endif">
      {!! Form::label('username', 'UserName') !!}
      {!! Form::text('username', isset($clientSettings->username)? $clientSettings->username:null, ['class' => 'form-control', 'placeholder' => 'UserName','required']) !!}
      @if ($errors->has('username')) <p class="help-block has-error">{{ $errors->first('username') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('password')) has-error @endif">
      {!! Form::label('password', 'Password') !!}
      <input type="password" value="{{isset($clientSettings->password)? $clientSettings->password:null}}" class="form-control" placeholder="Password" name="password" id="password" required />    
      @if ($errors->has('password')) <p class="help-block has-error">{{ $errors->first('password') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('url')) has-error @endif">
      {!! Form::label('url', 'URL') !!}
      {!! Form::text('url', isset($clientSettings->url)? $clientSettings->url:null, ['class' => 'form-control', 'placeholder' => 'URL','required']) !!}
      @if ($errors->has('url')) <p class="help-block has-error">{{ $errors->first('url') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('fetchduration')) has-error @endif">
      {!! Form::label('fetchduration', 'Sync. Duration') !!}
      <div class="form-group">
     <select class="select2 form-control" id="fetchduration" name="fetchduration" style="width:70%">
        <option @if($clientSettings->duration=='Every 15 minute') selected="selected"  @endif value="Every 15 minute">Every 15 minute</option>
        <option @if($clientSettings->duration=='Every 30 minute') selected="selected"  @endif value="Every 30 minute">Every 30 minute</option>
        <option @if($clientSettings->duration=='Every 45 minute') selected="selected" @endif value="Every 45 minute">Every 45 minute
        </option>
        <option @if($clientSettings->duration=='Hourly') selected="selected" @endif value="Hourly">Hourly
        </option>
        <option @if($clientSettings->duration=='Daily') selected="selected" @endif value="Daily">Every Day
        </option>
      </select>
      @if ($errors->has('fetchduration')) <p class="help-block has-error">{{ $errors->first('fetchduration') }}</p> @endif
    </div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      <label for="gender" class="">Tally To DSA</label>
      <div class="checkbox" style="margin-top: 2px;">
        <label style="margin-right: 10px;" class="">
          <div class="icheck-primary d-inline">
            <input type="checkbox" id="product_inward" class="minimal" name="product_inward" {{$clientSettings->product_inward==0?'':'checked'}}>Add & Edit Products 
          </div>
        </label>
        <label class="">
          <div class="icheck-primary d-inline">
            <input type="checkbox" id="party_inward" name="party_inward" class="minimal" {{$clientSettings->party_inward==0?'':'checked'}}>Add & Edit Parties
          </div>
        </label>
      </div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      <label for="gender" class="">DSA To Tally</label>
      <div class="checkbox" style="margin-top: 2px;">
        <label style="margin-right: 10px;" class="">
          <div class="icheck-primary d-inline">
            <input type="checkbox" id="order_outward" name="order_outward" class="minimal" {{$clientSettings->order_outward==0?'':'checked'}}> Add Orders 
          </div>
        </label>
      </div>
    </div>
  </div>
  <input class="btn btn-primary pull-right" type="submit" value="Save Changes">
</form>
</div>
