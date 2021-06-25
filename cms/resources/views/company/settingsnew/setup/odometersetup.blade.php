


@php $clientSettings = getClientSetting(); @endphp
<div class="col-xs-6">
  <div class="form-group @if ($errors->has('odometer_rate')) has-error @endif">
    {!! Form::label('odometer rate', 'Reimbursement Rate per unit') !!}
    <div class="form-group">
      <input type="number" id="odometer_rate" min="0.01" class="form-control" required
        value="{{$setting->odometer_rate}}">
    </div>

    @if ($errors->has('odometer_rate')) <p class="help-block has-error">{{ $errors->first('odometer_rate') }}</p> @endif
  </div>
</div>
<div class="col-xs-6">
  <div class="form-group @if ($errors->has('odometer_distance')) has-error @endif">
    {!! Form::label('odometer_distance_unit', 'Odometer Unit') !!}
    <span class="checkbox" style="margin-top: 2px;">
      <label style="padding: 0px;">
        {{ Form::radio('odometer_distance_unit', '1' , ($clientSettings->odometer_distance_unit==1)?true:false,
        ['class'=>'minimal']) }} KM
      </label>
      <label>
        {{ Form::radio('odometer_distance_unit', '0' , ($clientSettings->odometer_distance_unit==0)?true:false,
        ['class'=>'minimal']) }} Mile
      </label>
    </span>
  </div>
</div>
<div class="col-xs-12">
<button id="btnOdometerSetupUpdate" type="button" style="color:white!important;background-color: #0b7676!important;border-color: #0b7676!important;" class="btn btn-primary pull-right">Update</button>
</div>
