<div class="form-group @if ($errors->has('name')) has-error @endif">

  {!! Form::label('name', 'Name') !!}<span style="color: red">*</span>

  {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Brand Name','required']) !!}

  @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif

</div>


<div class="form-group">

  {!! Form::label('status', 'Status') !!}

  @if(isset($brand->status))

    {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $brand->status, ['class' => 'form-control']) !!}

  @else

    {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), 'Active', ['class' => 'form-control']) !!}

  @endif

</div>

	

	







