<div class="col-xs-12">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('default_currency')) has-error @endif">
      {!! Form::label('default_currency', 'Currency') !!}
      <select name="default_currency" id="default_currency" class="form-control select2">
        <option value="">Select a currency</option>
        @foreach($currencies as $currency)
        <option value="{{$currency->code}}" symbol="{{$currency->symbol}}"
          {{($setting->default_currency == $currency->code)? 'selected':''}}>{{$currency->country}}
          , {{$currency->code}}</option>
        @endforeach
      </select>

      @if ($errors->has('default_currency')) <p class="help-block has-error">{{ $errors->first('default_currency') }}</p>
      @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('currency_symbol')) has-error @endif">
      {!! Form::label('currency_symbol', 'Currency Symbol') !!}
      <input type="text" name="currency_symbol" id="currency_symbol" class="form-control" placeholder="Currency Symbol"
        value="{{isset($setting->currency_symbol)? $setting->currency_symbol:null}}" readonly>
      @if ($errors->has('currency_symbol')) <p class="help-block has-error">{{ $errors->first('currency_symbol') }}</p>
      @endif
    </div>
  </div>
</div>
<div class="col-xs-12">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('time_zone')) has-error @endif">
      {!! Form::label('time_zone', 'Time Zone') !!}
  
      {{ Form::select('time_zone', $timezonelist, isset($setting->time_zone)? $setting->time_zone:null, ['class' => 'form-control select2']) }}
  
      
      @if ($errors->has('time_zone')) <p class="help-block has-error">{{ $errors->first('time_zone') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('date_format')) has-error @endif">
      {!! Form::label('date_format', 'Date Format') !!}
      <div class="form-group">
        <select class="form-control select2" id="date_format_settings" name="date_format">
          <option @if(config('settings.date_format')=='d M Y' ) selected="selected" @endif value="d M Y">d M Y
            ({{date('d M Y')}})
          </option>
          <option @if(config('settings.date_format')=='Y M d' ) selected="selected" @endif value="Y M d">Y M d
            ({{date('Y M d')}})
          </option>
          <option @if(config('settings.date_format')=='M d Y' ) selected="selected" @endif value="M d Y">M d Y
            ({{date('M d Y')}})
          </option>
          <option @if(config('settings.date_format')=='D, M j Y' ) selected="selected" @endif value="D, M j Y">D,
            M j Y ({{date('D, M j Y')}})
          </option>
          <option @if(config('settings.date_format')=='D, j M Y' ) selected="selected" @endif value="D, j M Y">D,
            j M Y
            ({{date('D, j M Y')}})
          </option>
          <option @if(config('settings.date_format')=='D, Y M j' ) selected="selected" @endif value="D, Y M j">D,
            Y M j
            ({{date('D, Y M j')}})
          </option>
          <option @if(config('settings.date_format')=='Y-m-d' ) selected="selected" @endif value="Y-m-d">Y-m-d
            ({{date('Y-m-d')}})
          </option>
          <option @if(config('settings.date_format')=='d-m-Y' ) selected="selected" @endif value="d-m-Y">d-m-Y
            ({{date('d-m-Y')}})
          </option>
          <option @if(config('settings.date_format')=='m-d-Y' ) selected="selected" @endif value="m-d-Y">m-d-Y
            ({{date('m-d-Y')}})
          </option>
          <option @if(config('settings.date_format')=='Y/m/d' ) selected="selected" @endif value="Y/m/d">Y/m/d
            ({{date('Y/m/d')}})
          </option>
          <option @if(config('settings.date_format')=='d/m/Y' ) selected="selected" @endif value="d/m/Y">d/m/Y
            ({{date('d/m/Y')}})
          </option>
          <option @if(config('settings.date_format')=='m/d/Y' ) selected="selected" @endif value="m/d/Y">m/d/Y
            ({{date('m/d/Y')}})
          </option>
        </select>
        
      </div>
  
      @if ($errors->has('date_format')) <p class="help-block has-error">{{ $errors->first('date_format') }}</p> @endif
    </div>
  </div>
  @if(config('settings.party')==1)
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('allow_party_duplication')) has-error @endif">
      {!! Form::label('allow_party_duplication', 'Allow Party Duplication') !!}{{-- <span style="color: green">*</span> --}}
      <span class="checkbox" style="margin-top: 2px;">
        <label style="padding: 0px;">
          {{ Form::radio('allow_party_duplication', '0' , config('settings.allow_party_duplication')==0?true:false, ['class'=>'minimal']) }} No
        </label>
        <label>
          {{ Form::radio('allow_party_duplication', '1' , config('settings.allow_party_duplication')==1?true:false, ['class'=>'minimal']) }} Yes
        </label>
      </span>
    </div>
  </div>
  @endif
</div>

@if(config('settings.livetracking')==1)
<div class="col-xs-12">
  <div class="col-xs-12">
  <div class="form-group">
   <form id="location_accuracy" method="post">
    <h4><label>Battery Consumption and Location Accuracy Setting</label></h4>
        <input type="text" id="current_accuracy" value="{{config('settings.loc_fetch_interval')}}" hidden />
        <label><input type="radio" name="loc_accuracy" value="15" @if(config('settings.loc_fetch_interval')==15) checked @endif>High Accuracy<i id="accuracyHigh" title="High Frequency GPS tracking, gives best possible accuracy in location; Battery may drain drastically" class="fa fa-info-circle"></i></label>
        <label><input type="radio" name="loc_accuracy" value="30" @if(config('settings.loc_fetch_interval')==30) checked @endif>Medium Accuracy [Recommended] <i id="accuracyMedium" title="Fetches GPS data with medium frequency; Average Battery consumption" class="fa fa-info-circle"></i></label>
        <label><input type="radio" name="loc_accuracy" value="60" @if(config('settings.loc_fetch_interval')==60) checked @endif>Low Accuracy <i id="accuracyLow" title="Fetches GPS data less frequently; Battery can be saved while still getting few location points" class="fa fa-info-circle"></i></label>
        <label><input type="radio" name="loc_accuracy" value="86400" @if(config('settings.loc_fetch_interval')==86400) checked @endif>Switch off GPS location <i id="accuracyNone" title="Battery life will be saved drastically, salesman’s GPS will not be tracked" class="fa fa-info-circle"></i></label>
  </form>
  </div>
  </div>
</div>
@endif
<div class="col-xs-12">
<button id="btnSetupUpdate" type="button" style="position: relative;background-color: #0b7676!important;border-color: #0b7676!important;margin-top: 25px;"class="btn btn-primary pull-right">Update
        </button>
</div>
