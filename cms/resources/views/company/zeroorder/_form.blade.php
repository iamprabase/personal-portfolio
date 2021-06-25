<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('zero_order_date')) has-error @endif">
      {!! Form::label('zero_order_date_label', ' Date') !!}<span style="color: red">*</span>
        @if(config('settings.ncal')==0)
          <input class="form-control zero_order_date" type="text" name="zero_order_date" value="{{isset($zeroorder->date)?$zeroorder->date:date('Y-m-d')}}" readonly>
        @else
          <input type="text" class="form-control" id="zero_order_nep_date" value="{{isset($zeroorder->date)?getDeltaDateFormat($zeroorder->date):getDeltaDateFormat(date('Y-m-d'))}}" readonly>
          <input class="form-control zero_order_date hidden" type="text" id="zero_order_date" name="zero_order_date" value="{{isset($zeroorder->date)?$zeroorder->date:date('Y-m-d')}}">
        @endif
      @if ($errors->has('zero_order_date')) <p class="help-block has-error">{{ $errors->first('zero_order_date') }}</p> @endif
    </div>
  </div>
  
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('client_id')) has-error @endif">
      {!! Form::label('client_id', 'Party') !!}<span style="color: red">*</span>
      <select name="client_id" class="form-control select2" required>
        <option value="">Select a Party</option>
        @if(isset($zeroorder))
          @foreach($clients as $client)
          <option
          value="{{$client->id}}" {{(($zeroorder->client_id == $client->id)? 'selected':'' )}} >{{$client->company_name}}</option>
          @endforeach
        @else
          @foreach($clients as $client)
            <option value="{{$client->id}}">{{$client->company_name}}</option>
          @endforeach
        @endif
      </select>
      @if ($errors->has('client_id')) <p class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
    </div>
  </div>
</div>
<div class="form-group @if ($errors->has('remark')) has-error @endif"">
{!! Form::label('remark', 'Remark') !!}<span style="color: red">*</span>
{!! Form::textarea('remark', null, ['class' => 'form-control ckeditor', 'placeholder' => 'Remark','required']) !!}
@if ($errors->has('remark')) <p class="help-block has-error">{{ $errors->first('remark') }}</p> @endif
</div>
<div class="row">
  <div class="col-xs-6">
    <input type="text" name="previous_url" value="{{URL::previous()}}" hidden />
    <div id="imggroup" class="form-group @if ($errors->has('noorder_photo')) has-error @endif">
      {!! Form::label('Picture proof(Bill, Receipt, e.t.c)', 'Picture proof(Bill, Receipt, e.t.c) ') !!}
      <i>*max 3 images {{isset($image_count)? '('. $image_count .' uploaded, '. (3 - $image_count) .' remaining)':'' }}</i>
      <div class="form-group">
        @if(isset($images))
          @foreach($images as $image)
            <div class="col-xs-4 imgUp">
              <div class="imagePreview imageExistsPreview" @if(isset($image->image_path)) 
                style="background:url('{{ URL::asset('cms'.$image->image_path) }}');background-color: grey;background-position: center center;background-size: contain;background-repeat: no-repeat;" @endif>
              </div>
              <label class="btn btn-primary"> Upload
                <input type="file" id="noorder_photo" name="noorder_photo[]" class="uploadFile img" 
                value="cms/{{$image->image_path}}" style="width: 0px;height: 0px;overflow: hidden;">
                <span hidden>
                  <input type="text" name="noorder_photo_id[]" value="{{$image->id}}" hidden />
                </span>
              </label>
              <i class="fa fa-times del"></i>
            </div><!-- col-2 -->
          @endforeach
          <i class="fa fa-plus imgAdd"></i>
        @else
          <div class="col-xs-4 imgUp">
            <div class="imagePreview"></div>
            <label class="btn btn-primary"> Upload
              <input type="file" id="receipt" name="noorder_photo[]" class="uploadFile img" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
            </label>
            <i class="fa fa-times del"></i>
          </div><!-- col-2 -->
          <i class="fa fa-plus imgAdd"></i>
        @endif
      </div><!-- row -->
      @if ($errors->has('receipt')) <p class="help-block has-error">{{ $errors->first('receipt') }}</p> @endif
    </div>
  </div>
</div>
<input type="hidden" name="DT_ZeroOrd_FILTER" class="DT_ZeroOrd_FILTER">
