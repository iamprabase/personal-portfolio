{!! Form::open(array('url' => $form_url, 'method' => 'post', 'files'=> false, 'id'=>'rate-name-setup-form', 'class'=> 'rate-name-setup-form hidden')) !!}
  @method($method)
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('rate_name')) has-error @endif">
      {!! Form::label('rate_name', 'Name of Rate') !!}<span style="color: red">*</span>
      <div class="input-group" style="width: 100% !important">
        {!! Form::text('rate_name', $rate_name, ['class' => 'form-control pull-right', 'id' => 'rate_name', 'autocomplete'=>'off',
        'placeholder' => 'Name of Rate','required']) !!}
      </div>
      <input type="hidden" name="rate_id" value="{{$rate_id}}">
      @if ($errors->has('rate_name')) <p class="help-block has-error">{{ $errors->first('rate_name') }}</p> @endif
      <div class="rate_name err_div">

      </div>
    </div>
  </div>

  <div class="col-xs-4">
    {!! Form::label('', '') !!}
    <div>
      <!-- Submit Form Button -->
      {!! Form::submit('Update Name', ['class' => 'btn btn-default rate-name-submit', 'id'=>'rate-name-submit']) !!}
    </div>
  </div>

{!! Form::close() !!}