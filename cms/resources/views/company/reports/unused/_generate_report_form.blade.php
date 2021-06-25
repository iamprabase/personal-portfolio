<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('client_id')) has-error @endif">
      {!! Form::label('client_id', 'Party Name') !!}
      {!! Form::select('client_id', $clients, isset($collection)? $collection->client_id:null,  ['class' => 'form-control select2']) !!}
      @if ($errors->has('client_id')) <p class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">
      {!! Form::label('employee_id', 'Employee Name') !!}
      <select name="employee_id" class="form-control select2">
        <option
            value="admin" {{isset($collection)? (($collection->employee_type == 'Admin')? 'selected':''):null}}>{{Auth::user()->name.' (Admin)'}}</option>
        @foreach($employees as $employee)
          <option
              value="{{$employee->id}}" {{ isset($collection)? (($collection->employee_type == "Employee" && $employee->id == $collection->employee_id)? 'selected':''):null }}>{{$employee->name}}</option>
        @endforeach
      </select>
      {{-- {!! Form::select('employee_id', $employees, isset($collection)? $collection->employee_id:null,  ['class' => 'form-control']) !!} --}}
      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('payment_received')) has-error @endif">
      {!! Form::label('payment_received', 'Payment Received ') !!}<span style="color: red">*</span>
      <div class="input-group">
        <div class="input-group-addon rs-symbol">
          {{getClientSetting(Auth::user()->id)['currency_symbol']}}
        </div>
        {!! Form::text('payment_received', null, ['class' => 'form-control', 'placeholder' => 'Payment Received','required']) !!}
      </div>
      @if ($errors->has('payment_received')) <p
          class="help-block has-error">{{ $errors->first('payment_received') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('payment_date')) has-error @endif">
      {!! Form::label('payment_date', 'Payment Date') !!}
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        {!! Form::text('payment_date', null, ['class' =>'form-control pull-right', 'id' => 'payment_date', 'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
      </div>

      @if ($errors->has('payment_date')) <p class="help-block has-error">{{ $errors->first('payment_date') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('payment_note')) has-error @endif">
      {!! Form::label('payment_note', 'Payment Notes') !!}
      {!! Form::textarea('payment_note', null, ['class' => 'form-control', 'placeholder' => 'Payment Notes']) !!}
      @if ($errors->has('payment_note')) <p class="help-block has-error">{{ $errors->first('payment_note') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      {!! Form::label('payment_method', 'Payment Mode') !!}

      {!! Form::select('payment_method', array('Cash' => 'Cash', 'Cheque' => 'Cheque', 'Bank Transfer' => 'Bank Transfer'), isset($collection)?$collection->payment_method:'Cash', ['class' => 'form-control select2']) !!}

    </div>
    <div id="bankdetails">
      <div class="form-group">
        {!! Form::label('bank', 'Bank Name') !!}<span style="color: red">*</span>

        <select name="bank" class="form-control">
          <option value="">Select Bank</option>
          @foreach($banks as $bank)
            <option
                value="{{$bank->id}}" {{ isset($collection)? (($bank->id == $collection->bank_id)? 'selected':''):null }}>{{$bank->name}}</option>
          @endforeach
        </select>

      </div>
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
        {!! Form::label('cheque_date', 'Cheque Date') !!} <span style="color: red">*</span>
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          {!! Form::text('cheque_date', null, ['class' =>'form-control pull-right', 'id' => 'cheque_date', 'autocomplete'=>'off', 'placeholder' => 'Start Date']) !!}
        </div>

        @if ($errors->has('cheque_date')) <p class="help-block has-error">{{ $errors->first('cheque_date') }}</p> @endif
      </div>
    </div>
    <div class="form-group @if ($errors->has('receipt')) has-error @endif">
      {!! Form::label('Image', 'Image ') !!}
      <i>*max 3
        images {{isset($image_count)? '('. $image_count .' uploaded, '. (3 - $image_count) .' remaining)':'' }}</i>
      <input type="file" class="form-control" id="receipt" name="receipt[]" placeholder="Image" multiple>
      @if(isset($images))
        @foreach($images as $image)
          @if(isset($image->image_path))
            <img @if(isset($image->image_path)) src="{{ URL::asset('cms'.$image->image_path) }}"
                 @endif alt="Picture Displays here" style="max-height: 100px;"/>&emsp;
          @else
            <span class="pull-right">N/A</span>
          @endif
        @endforeach
      @endif
      <div id="images">
      </div>
      {{-- <input type="file" class="form-control" name="receipt[]" placeholder="Image" multiple> --}}
      {{-- {!! Form::file('receipt[]', null, ["class" => "form-control", "placeholder" => "Image", "multiple" => "multiple"]) !!} --}}
      @if ($errors->has('receipt')) <p class="help-block has-error">{{ $errors->first('receipt') }}</p> @endif
    </div>
  </div>
</div>