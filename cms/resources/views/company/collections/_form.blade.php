<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('client_id')) has-error @endif">
      {!! Form::label('client_id', 'Party Name') !!}<span style="color: red">*</span>
      {!! Form::select('client_id', $clients, isset($collection)? $collection->client_id:null,  ['class' => 'form-control select2']) !!}
      @if ($errors->has('client_id')) <p class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
    </div>
  </div>
  @if(isset($collection->employee_id))
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">
      {!! Form::label('employee_id', 'Employee Name') !!}<span style="color: red">*</span>
      <input type="text" name="previous_url" value="{{URL::previous()}}" hidden/> 
      <select name="employee_id" class="form-control select2" disabled>
        @foreach($employees as $employee)
          <option value="{{$employee->id}}" {{ isset($collection)? (($employee->id == $collection->employee_id)? 'selected':''):null }}>{{$employee->name}}</option>
        @endforeach
      </select>
      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
    </div>
  </div>
  @endif
</div>

<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('payment_received')) has-error @endif">
      {!! Form::label('payment_received', 'Amount ') !!}<span style="color: red">*</span>
      <div class="input-group">
        <div class="input-group-addon rs-symbol">
          {{getClientSetting(Auth::user()->id)['currency_symbol']}}
        </div>
        {!! Form::text('payment_received', null, ['class' => 'form-control', 'placeholder' => 'Amount Received','required']) !!}
      </div>
      @if ($errors->has('payment_received')) <p
          class="help-block has-error">{{ $errors->first('payment_received') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('payment_date')) has-error @endif">
      {!! Form::label('payment_date', 'Received Date') !!}<span style="color: red">*</span>
      @if(config('settings.ncal')==1)
      <input type="hidden" id="englishDate" name="englishDate">
      @endif
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        

        @if(config('settings.ncal')==0)
        {!! Form::text('payment_date', null, ['class' =>'datepicker form-control pull-right', 'id' => 'payment_date', 'autocomplete'=>'off', 'placeholder' => 'Payment Date']) !!}
        @else
        <input type="text" id="payment_date_np" class="form-control pull-right">
        {!! Form::text('payment_date', null, ['id' => 'payment_date_eng', 'hidden']) !!}
        @endif
      </div>

      @if ($errors->has('payment_date')) <p class="help-block has-error">{{ $errors->first('payment_date') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('payment_status_note')) has-error @endif">
      {!! Form::label('payment_status_note', 'Notes') !!}
      {!! Form::textarea('payment_status_note', null, ['class' => 'form-control', 'placeholder' => 'Notes']) !!}
      @if ($errors->has('payment_status_note')) <p class="help-block has-error">{{ $errors->first('payment_status_note') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      {!! Form::label('payment_method', 'Mode') !!}

      {!! Form::select('payment_method', array('Cash' => 'Cash', 'Cheque' => 'Cheque', 'Bank Transfer' => 'Bank Transfer'), isset($collection)?$collection->payment_method:'Cash', ['class' => 'form-control select2']) !!}
      @if ($errors->has('bank')) <p class="help-block has-error">{{ $errors->first('bank') }}</p> @endif

    </div>
    <div id="bankdetails">
      <div class="form-group">
        {!! Form::label('bank', 'Bank Name') !!}<span style="color: red">*</span>

        <select id="bank" name="bank" class="form-control select2">
          <option value="">Select Bank</option>
          @foreach($banks as $bank)
            <option value="{{$bank->id}}" {{ isset($collection)? (($bank->id == $collection->bank_id)? 'selected':''):null }}>{{$bank->name}}</option>
          @endforeach
        </select>

      </div>
      <div id="chequeDetails">
      <div class="form-group @if ($errors->has('cheque_no')) has-error @endif">
        {!! Form::label('cheque_no', 'Cheque No. ') !!}<span style="color: red">*</span>
        <div class="input-group">
          <div class="input-group-addon rs-symbol">
            Cq.No
          </div>
          {!! Form::text('cheque_no', null, ['class' => 'form-control', 'placeholder' => 'Cheque No.']) !!}
        </div>
        @if ($errors->has('cheque_no')) <p class="help-block has-error">{{ $errors->first('cheque_no') }}</p> @endif
      </div>
      <div class="form-group @if ($errors->has('cheque_date')) has-error @endif">
        {!! Form::label('cheque_date', 'Cheque Date') !!}<span style="color: red">*</span>
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          @if(config('settings.ncal')==0)
          {!! Form::text('cheque_date', null, ['class' =>'form-control pull-right datepicker', 'id' => 'cheque_date', 'autocomplete'=>'off', 'placeholder' => 'Cheque Date']) !!}
          @else
          <input type="text" id="cheque_date_np" class="form-control pull-right">
          {!! Form::text('cheque_date', null, ['id' => 'cheque_date_eng', 'placeholder' => 'Cheque Date','hidden']) !!}
          @endif
        </div>

        @if ($errors->has('cheque_date')) <p class="help-block has-error">{{ $errors->first('cheque_date') }}</p> @endif
      </div>

      <div class="form-group">
        {!! Form::label('payment_status', 'Cheque Status') !!}<span style="color: red">*</span>
        @if(isset($collection->payment_status))
        <select name="payment_status" class="form-control">
          <option @if($collection->payment_status=="Pending") selected="selected" @endif value="Pending">Pending</option>               
          <option @if($collection->payment_status=="Deposited") selected="selected" @endif value="Deposited">Deposited</option>
          <option @if($collection->payment_status=="Cleared") selected="selected" @endif value="Cleared">Cleared</option>
          <option @if($collection->payment_status=="Bounced") selected="selected" @endif value="Bounced">Bounced</option>
        </select>
        @else
          <select name="payment_status" class="form-control">
            <option value="Pending">Pending</option>               
            <option value="Deposited">Deposited</option>
            <option value="Cleared">Cleared</option>
            <option value="Bounced">Bounced</option>
          </select>
        @endif
      </div>
      </div>
    </div>
    <div id="imggroup" class="form-group @if ($errors->has('receipt')) has-error @endif">
      {!! Form::label('Image', 'Image ') !!}
      <i>*max 3
        images {{isset($image_count)? '('. $image_count .' uploaded, '. (3 - $image_count) .' remaining)':'' }}</i>
      <div class="row">
      
      @if(isset($images))
        @foreach($images as $image)
          <div class="col-xs-4 imgUp">
            <div class="imagePreview imageExistsPreview" @if(isset($image->image_path)) style="background:url('{{ URL::asset('cms'.$image->image_path) }}');background-color: grey;background-position: center center;background-size: contain;background-repeat: no-repeat;" @endif></div>
          <label class="btn btn-primary"> Upload<input id="receipt" type="file" name="receipt[]" class="uploadFile img" value="cms/{{$image->image_path}}" style="width: 0px;height: 0px;overflow: hidden;"><span hidden><input type="text" name="img_ids[]" value="{{$image->id}}" hidden /></span>
          </label><i class="fa fa-times del"></i>
          </div><!-- col-2 -->
        @endforeach
        <i class="fa fa-plus imgAdd"></i>
      @else
        <div class="col-xs-4 imgUp">
          <div class="imagePreview"></div>
        <label class="btn btn-primary"> Upload<input id="receipt" type="file" name="receipt[]" class="uploadFile img" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
        </label><i class="fa fa-times del"></i>
        </div><!-- col-2 -->
        <i class="fa fa-plus imgAdd"></i>
      @endif
      </div><!-- row -->
      @if ($errors->has('receipt')) <p class="help-block has-error">{{ $errors->first('receipt') }}</p> @endif
    </div>
  </div>
</div>
<input type="hidden" name="DT_Collec_FILTER" class="DT_Collec_FILTER">
