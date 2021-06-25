<div class="form-group @if ($errors->has('title')) has-error @endif">
  {!! Form::label('title', 'Title') !!}<span style="color: red">*</span>
  {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title', 'required'=>'required']) !!}
  @if ($errors->has('title')) <p class="help-block has-error">{{ $errors->first('title') }}</p> @endif
</div>

<div class="form-group">
  {!! Form::label('description', 'Description') !!}<span style="color: red">*</span>
  {!! Form::textarea('description', null, ['class' => 'form-control ckeditor', 'id' => 'description', 'placeholder' => 'About Announcement...','required'=>'required']) !!}
  @if ($errors->has('description')) <p class="help-block has-error">{{ $errors->first('description') }}</p> @endif
</div>

<div class="row">
  <div class="col-xs-6">
    <b>Select Employees</b>
    <select id="employees" name="employees[]" class="form-control select2" multiple="true">
      @foreach ($empData as $key => $value)
      <optgroup label="{{$key}}" value="{{$key}}">
        @foreach($value as $k => $v)
        <option value="{{$v['id']}}" @if($v['id']==$empId) selected="selected" disabled="disabled" @endif>{{$v['emp_name']}}</option>
        @endforeach
      </optgroup>  
      @endforeach    
    </select>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      {!! Form::label('status', 'Status') !!}
      {!! Form::select('status', array('1' => 'Active'), null, ['class' => 'form-control']) !!}
    </div>
  </div>


</div>
