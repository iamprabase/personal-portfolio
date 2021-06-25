<div class="col-xs-12">
  <div class="form-group @if ($errors->has('allow_phonepicture_in_visit')) has-error @endif">
    {!! Form::label('allow_phonepicture_in_visit', 'Allow Pictures from Phone Gallery in Visit') !!}
    <span class="checkbox" style="margin-top: 2px;">
      <label style="padding: 0px;">
        {{ Form::radio('allow_phonepicture_in_visit', '0' , ($clientSettings->allow_phonepicture_in_visit==0)?true:false, ['class'=>'minimal']) }} No
      </label>
      <label>
        {{ Form::radio('allow_phonepicture_in_visit', '1' , ($clientSettings->allow_phonepicture_in_visit==1)?true:false, ['class'=>'minimal']) }}
        Yes
      </label>
    </span>
  </div>

</div>

<div class="col-xs-12 mb-20 form-inline">
  <div class="form-group @if ($errors->has('max_visit_radius_with_client')) has-error @endif">
    <span style="margin: 0px 5px;">
      <input type="checkbox" name="enable_max_visit_radius_with_client" id="enable_max_visit_radius_with_client" value="0" style="height: auto;" {{$setting->enable_max_visit_radius_with_client? "checked": ""}}>
    </span>
    
    {!! Form::label('max_visit_radius_with_client', 'Allow marking visit only if salesman is within radius of ') !!}
    {!! Form::number('max_visit_radius_with_client', isset($setting->max_visit_radius_with_client)? $setting->max_visit_radius_with_client:null , ['class' => 'form-control', 'placeholder' => '', "min" => 100, "style"=>"width: 70px;", 'onFocusout' => 'validatePositiveNumber(this, 100)']) !!}
    {!! Form::label('max_visit_radius_with_client', 'meters from party.') !!}
    @if ($errors->has('max_visit_radius_with_client')) <p class="help-block has-error">{{ $errors->first('max_visit_radius_with_client') }}</p> @endif
  </div>
</div>

<div class="col-xs-12 form-inline">
  <div class="form-group @if ($errors->has('auto_finish_visit_radius')) has-error @endif">
    <span style="margin: 0px 5px;">
      <input type="checkbox" name="enable_auto_finish_visit_radius" id="enable_auto_finish_visit_radius" value="0" style="height: auto;" {{$setting->enable_auto_finish_visit_radius? "checked": ""}}>
    </span>
    {!! Form::label('auto_finish_visit_radius', 'Auto-complete visit if salesman is') !!}
    {!! Form::number('auto_finish_visit_radius', isset($setting->auto_finish_visit_radius)? $setting->auto_finish_visit_radius:null , ['class' => 'form-control', 'placeholder' => '', "min" => 200, "style"=>"width: 70px;", 'onFocusout' => 'validatePositiveNumber(this, 200)']) !!}
    {!! Form::label('auto_finish_visit_radius', 'meters away from party for more than 5 minutes.') !!}
    @if ($errors->has('auto_finish_visit_radius')) <p class="help-block has-error">{{ $errors->first('auto_finish_visit_radius') }}</p> @endif
  </div>
</div>

<div class="col-xs-12">
<button id="btnpartyVisitSetupUpdate" type="button" style="position: relative;background-color: #0b7676!important;border-color: #0b7676!important;margin-top: 25px;"class="btn btn-primary pull-right">Update
        </button>
</div>