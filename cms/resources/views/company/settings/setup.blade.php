<div class="col-xs-6">
  <div class="form-group @if ($errors->has('default_currency')) has-error @endif">
    {!! Form::label('default_currency', 'Default Currency') !!}
    {!! Form::text('default_currency', isset($setting->default_currency)? getCurrencyInfo($setting->default_currency)->country.', '.getCurrencyInfo($setting->default_currency)->code:null ,['class'=>'form-control', 'id'=>'default_currency', 'readonly'] )!!}
    @if ($errors->has('default_currency')) <p
        class="help-block has-error">{{ $errors->first('default_currency') }}</p> @endif
  </div>
</div>
<div class="col-xs-6">
  <div class="form-group @if ($errors->has('currency_symbol')) has-error @endif">
    {!! Form::label('currency_symbol', 'Currency Symbol') !!}
    <input type="text" name="currency_symbol" id="currency_symbol" class="form-control" placeholder="Currency Symbol"
           value="{{isset($setting->currency_symbol)? $setting->currency_symbol:null}}" readonly>
    @if ($errors->has('currency_symbol')) <p
        class="help-block has-error">{{ $errors->first('currency_symbol') }}</p> @endif
  </div>
</div>
<div class="col-xs-6">
  <div class="form-group @if ($errors->has('time_zone')) has-error @endif">
    {!! Form::label('time_zone', 'Time Zone') !!}
    {!! Form::text('time_zone', isset($setting->time_zone)? $setting->time_zone:null, ['class'=>'form-control', 'id'=>'time_zone', 'readonly'])!!}
    @if ($errors->has('time_zone')) <p class="help-block has-error">{{ $errors->first('time_zone') }}</p> @endif
  </div>
</div>
<div class="col-xs-6">
  <div class="form-group @if ($errors->has('date_format')) has-error @endif">
    {!! Form::label('date_format', 'Date Format') !!}
    <div class="form-group">
      <select class="select2" id="date_format_settings" name="date_format" style="width:70%">
        <option @if(config('settings.date_format')=='d M Y') selected="selected"  @endif value="d M Y">d M Y ({{date('d M Y')}})</option>
        <option @if(config('settings.date_format')=='Y M d') selected="selected"  @endif value="Y M d">Y M d ({{date('Y M d')}})</option>
        <option @if(config('settings.date_format')=='M d Y' ) selected="selected" @endif value="M d Y">M d Y ({{date('M d Y')}})
        </option>
        <option @if(config('settings.date_format')=='D, M j Y' ) selected="selected" @endif value="D, M j Y">D, M j Y ({{date('D, M j Y')}})
        </option>
        <option @if(config('settings.date_format')=='D, j M Y' ) selected="selected" @endif value="D, j M Y">D, j M Y
          ({{date('D, j M Y')}})
        </option>
        <option @if(config('settings.date_format')=='D, Y M j' ) selected="selected" @endif value="D, Y M j">D, Y M j
          ({{date('D, Y M j')}})
        </option>
        <option @if(config('settings.date_format')=='Y-m-d' ) selected="selected" @endif value="Y-m-d">Y-m-d ({{date('Y-m-d')}})
        </option>
        <option @if(config('settings.date_format')=='d-m-Y' ) selected="selected" @endif value="d-m-Y">d-m-Y ({{date('d-m-Y')}})
        </option>
        <option @if(config('settings.date_format')=='m-d-Y' ) selected="selected" @endif value="m-d-Y">m-d-Y ({{date('m-d-Y')}})
        </option>
        <option @if(config('settings.date_format')=='Y/m/d' ) selected="selected" @endif value="Y/m/d">Y/m/d ({{date('Y/m/d')}})
        </option>
        <option @if(config('settings.date_format')=='d/m/Y' ) selected="selected" @endif value="d/m/Y">d/m/Y ({{date('d/m/Y')}})
        </option>
        <option @if(config('settings.date_format')=='m/d/Y' ) selected="selected" @endif value="m/d/Y">m/d/Y ({{date('m/d/Y')}})
        </option>
      </select>
      <button action="{{domain_route('company.admin.setting.updateDateFormat')}}" id="updateDateFormat" type="button" style="color:white!important;width: 25%;background-color: #0b7676!important;border-color: #0b7676!important;" class="btn btn-primary">Update</button>
    </div>

    @if ($errors->has('date_format')) <p class="help-block has-error">{{ $errors->first('date_format') }}</p> @endif
  </div>
</div>
@if(config('settings.orders')==1)
<div class="col-xs-6">
  <div class="form-group @if ($errors->has('order_prefix')) has-error @endif">
    {!! Form::label('order_prefix', 'Order Prefix') !!}{{-- <span style="color: green">*</span> --}}
    {!! Form::text('order_prefix', isset($setting->order_prefix)? $setting->order_prefix:null , ['class' => 'form-control', 'placeholder' => 'Order Prefix','readonly']) !!}
    @if ($errors->has('order_prefix')) <p class="help-block has-error">{{ $errors->first('order_prefix') }}</p> @endif
  </div>
</div>
@endif
@if(config('settings.livetracking')==1)
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
@endif
{{-- <div class="col-xs-12">
  <div class="form-group">
    <form id="calendarType" method="post">
      <h4><label>Calendar Type</label></h4>
      <label><input type="radio" name="cType" value="Gregorian">English Calendar</label>
      <label><input type="radio" name="cType" value="Gregorian">Nepali Calendar</label>
    </form>
  </div>
</div> --}}
@if(config('settings.orders')==1 && config('settings.product')==1)
<div class="col-xs-6">
  <h4><label>Currently Implied Taxes</label></h4>
  <div class="table-responsive" id="showTaxes">
    <div class="table-responsive" id="showTaxes">
      <table class="table table-bordered">
        <thead>
        <tr>
          <th>Tax Name</th>
          <th>Percentage</th>
          <th>Default</th>
        </tr>
        </thead>
        <tbody>
        @foreach($taxes as $tax)
          <tr>
            <td>{{$tax->name}}</td>
            <td>{{$tax->percent}}</td>
            <td>{{($tax->default_flag==1)?"Yes":"No"}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif
@if(config('settings.ageing')==1 && Auth::user()->can('ageing-view'))
<div class="col-xs-6">
  <h4><label style="padding-left: 10px;">Default Credit Days</label></h4>
  <div class="form-group">
    <div class="box-body">
      <input style="width: 30%;text-align: right;" oninput="validity.valid||(value='');" class="number" min="0" type="number" name="creditDays" id="creditDays" value="{{config('settings.credit_days')}}" /><button id="btncreditdays" style="width: 30%;background-color: #0b7676!important;border-color: #0b7676!important;" type="button" class="btn btn-primary" action="{{domain_route('company.admin.setting.updateCreditDays')}}">Update</button>
    </div>
  </div>
</div>
@endif