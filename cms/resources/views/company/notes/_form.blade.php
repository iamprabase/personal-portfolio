<div class="row">
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">
      {!! Form::label('employee_id', 'Salesman') !!}<span style="color: red">*</span>
      @if(isset($note))
      <input type="text" class="form-control" value="{{$note->employee_name}}" disabled>
      @else
      <input type="text" class="form-control" value="{{Auth::user()->name}}" disabled>
      @endif
      @if ($errors->has('employee_id')) <p class="help-block has-error">{{ $errors->first('employee_id') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('client_id')) has-error @endif">
      {!! Form::label('client_id', 'Party Name') !!}
      @if(isset($note))
      @if(isset($clients->id))
      <input type="text" name="client_id" value="{{$clients->id}}" hidden>
      <input type="text" value="{{$clients->company_name}}" class="form-control" readonly>
      @else
      <select name="client_id" class="form-control select2" id="client_id" required>
        <option value="">Select a Party</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}" {{(($note->client_id == $client->id)? 'selected':'' )}} >{{$client->company_name}}</option>
        @endforeach
      </select>
      @endif
      @else
      @if(isset($clients->id))
      <input type="text" name="client_id" value="{{$clients->id}}" hidden>
      <input type="text" value="{{$clients->company_name}}" class="form-control" readonly>
      @else
      <select name="client_id" class="form-control select2" id="client_id" required>
        <option value="">Select a Party</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}">{{$client->company_name}}</option>
        @endforeach
      </select>
      @endif
      @endif
      @if ($errors->has('client_id')) <p class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
    </div>
  </div>
</div>
<div class="row" hidden>
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('note_date')) has-error @endif">
      {!! Form::label('note_date', 'Date') !!}<span style="color: red">*</span>
      {!! Form::text('note_date', date('Y-m-d'), ['class' => 'form-control datepicker', 'placeholder' => 'Date', 'id'=>'note_date', 'autocomplete'=>'off', 'required'=>'required']) !!}
      @if ($errors->has('note_date')) <p class="help-block has-error">{{ $errors->first('note_date') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-6 bootstrap-timepicker">
    <div class="form-group @if ($errors->has('note_time')) has-error @endif">
      {!! Form::label('note_time', 'Time') !!}<span style="color: red">*</span>
      {!! Form::text('note_time', null, ['class' => 'form-control timepicker', 'placeholder' => 'Time', 'autocomplete'=>'off', 'required'=>'required']) !!}
      <input type="text" name="previous_url" value="{{URL::previous()}}" hidden/> 
      @if ($errors->has('note_time')) <p class="help-block has-error">{{ $errors->first('note_time') }}</p> @endif
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="form-group @if ($errors->has('description')) has-error @endif"">
      {!! Form::label('description', 'Notes') !!}<span style="color: red">*</span>
      {!! Form::textarea('description', (isset($note->remark))?$note->remark:null, ['class' => 'form-control', 'placeholder' => 'Notes',  'required'=>'required']) !!}
      @if ($errors->has('description')) <p class="help-block has-error">{{ $errors->first('description') }}</p> @endif
    </div>
  </div>
  <div class="col-xs-12">
    <div id="imggroup" class="form-group @if ($errors->has('receipt')) has-error @endif">
      {!! Form::label('Image', 'Image ') !!}
      <i>( max number of images: 4 )
      {{isset($image_count)? '('. $image_count .' uploaded, '. (3 - $image_count) .' remaining)':'' }}</i>
      <div class="row">
        @if(isset($images))
        @foreach($images as $image)
        <div class="col-xs-3 imgUp">
          <div class="imagePreview imageExistsPreview"><img @if(isset($image->image_path)) src="{{ URL::asset('cms'.$image->image_path) }}" @endif /></div>
          <label class="btn btn-primary"> Upload<input id="receipt" type="file" name="receipt[]" class="uploadFile img" value="cms/{{$image->image_path}}" style="width: 0px;height: 0px;overflow: hidden;"><span hidden><input type="text" name="img_ids[]" value="{{$image->id}}" hidden /></span>
          </label><i class="fa fa-times del"></i>
        </div><!-- col-2 -->
        @endforeach
        <i class="fa fa-plus imgAdd"></i>
        @else
        <div class="col-xs-3 imgUp">
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
