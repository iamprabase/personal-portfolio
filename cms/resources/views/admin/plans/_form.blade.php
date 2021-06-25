<div class="form-group @if ($errors->has('name')) has-error @endif">

    {!! Form::label('name', 'Name') !!}<span style="color: red">*</span>

    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Plan Name']) !!}

    @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif

</div>

{{-- <div class="row">

  <div class="col-xs-3">

    <div class="form-group @if ($errors->has('users')) has-error @endif">

      {!! Form::label('users', 'No of users') !!}<span style="color: red">*</span>

      {!! Form::text('users', null, ['class' => 'form-control', 'placeholder' => 'No. of Users']) !!}

      @if ($errors->has('users')) <p class="help-block has-error">{{ $errors->first('users') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-3">

    <div class="form-group @if ($errors->has('duration')) has-error @endif">

      {!! Form::label('duration', 'Duration') !!}<span style="color: red">*</span>

      {!! Form::text('duration', null, ['class' => 'form-control', 'placeholder' => 'Duration']) !!}

      @if ($errors->has('duration')) <p class="help-block has-error">{{ $errors->first('duration') }}</p> @endif

    </div>

  </div>

  <div class="col-xs-3">

    <div class="form-group @if ($errors->has('duration_in')) has-error @endif">

      {!! Form::label('duration_in', 'Duration In') !!}<span style="color: red">*</span>

      {!! Form::select('duration_in', array('Days' => 'Days', 'Month' => 'Month', 'Year' => 'Year'), 'Month', ['class' => 'form-control']) !!}

      @if ($errors->has('duration_in')) <p class="help-block has-error">{{ $errors->first('duration_in') }}</p> @endif

    </div>

  </div>
  @if(isset($plan) && count($plan->companies)==0)
  <div class="col-xs-3">

    <div class="form-group">

      {!! Form::label('status', 'Status') !!}

      @if(isset($plan->status))

        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $plan->status, ['class' => 'form-control']) !!}

      @else

        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), 'Active', ['class' => 'form-control']) !!}

      @endif

    </div>

  </div>
  @endif

</div> --}}


<div class="form-group">

    {!! Form::label('Description','Description') !!}

    {!! Form::textarea('description',null,['class'=>'form-control']) !!}
    @if ($errors->has('description')) <p class="help-block has-error">{{ $errors->first('description') }}</p> @endif

</div>

<div class="form-group">
    {!! Form::label('Modules','Modules') !!}
    <table class="table table-striped">
        <tr style="background-color: #f16022;color: white;">
            <th>Name</th>
            <th>
                <?php $i = 0; ?>
                @foreach($mainmodules as $module)
                    @if($module->value==1)
                        <?php $i++; ?>
                    @endif
                @endforeach
                <label class="switch">
                    <input class="toggle-all-switches" type="checkbox"
                           @if(count($mainmodules)==$i) checked="checked" @endif>
                    <span class="slider round"></span>
                </label>
            </th>
        </tr>
        @foreach($mainmodules as $module)
            <tr>
                <td><b>{{$module->name}}</b></td>
                <td>
                    <label class="switch">
                        @if($module->field=="attendance")
                            <input class="{{$module->field}}" type="checkbox" checked="checked" disabled="disabled">
                            <input name="{{$module->field}}" id="{{str_replace(' ','_' , $module->field)}}"
                                   type="checkbox" checked="checked" class="hide">
                        @else
                            <input class="switches {{$module->field}}" name="{{$module->field}}"
                                   id="{{str_replace(' ','_' , $module->field)}}" type="checkbox"
                                   @if($module->value==1) checked="checked" @endif>
                        @endif
                        <span class="slider round"></span>
                    </label>
                </td>
            </tr>
        @endforeach
    </table>
</div>