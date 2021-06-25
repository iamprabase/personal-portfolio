<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('title')) has-error @endif">
      {!! Form::label('title', 'Title') !!}<span style="color: red">*</span>
      {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title','required']) !!}
      @if ($errors->has('title')) <p class="help-block has-error">{{ $errors->first('title') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('due_date')) has-error @endif">
      {!! Form::label('due_date', 'Due Date') !!}<span style="color: red">*</span>
      <div class="input-group date">
        <div class="input-group-addon">
          <i class="fa fa-calendar"></i>
        </div>
        {!! Form::text('due_date', null, ['class' => 'form-control pull-right', 'id' => 'due_date', 'placeholder' => 'Due Date', 'autocomplete'=>'off','required']) !!}
      </div>

      @if ($errors->has('due_date')) <p class="help-block has-error">{{ $errors->first('due_date') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('assigned_from')) has-error @endif">
      {!! Form::label('assigned_from', 'Assigned From') !!}<span style="color: red">*</span>
      <select name="assigned_from" class="form-control select2" required>
        <option
            value="admin"{{isset($task)? (($task->assigned_from_type == 'Admin')? 'selected':''):null}}>{{Auth::user()->name.' (Admin)'}}</option>
        @foreach($employees as $employee)
          <option
              value="{{$employee->id}}" {{(isset($task)? (($task->assigned_from == $employee->id && $task->assigned_from_type == 'Employee')? 'selected':''):null)}}>{{$employee->name}}</option>
        @endforeach
      </select>
      {{-- {!! Form::select('assigned_from', $employees, isset($task)? $task->assigned_from:null,  ['class' => 'form-control']) !!} --}}
      @if ($errors->has('assigned_from')) <p
          class="help-block has-error">{{ $errors->first('assigned_from') }}</p> @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group @if ($errors->has('assigned_to')) has-error @endif">
      {!! Form::label('assigned_to', 'Assigned To') !!}<span style="color: red">*</span>
      <select name="assigned_to" class="form-control select2" required>
        @foreach($employees as $employee)
          <option
              value="{{$employee->id}}" {{(isset($task)? (($task->assigned_to == $employee->id)? 'selected':''):null)}}>{{$employee->name}}</option>
        @endforeach
      </select>
      {{-- {!! Form::select('assigned_to', $employees, isset($task)? $task->assigned_to:null,  ['class' => 'form-control']) !!} --}}
      @if ($errors->has('assigned_to')) <p class="help-block has-error">{{ $errors->first('assigned_to') }}</p> @endif
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      {!! Form::label('client_id', 'Choose Party (Party Related Task?)') !!}
      <select name="client_id" class="form-control select2">
        <option value=''>Select a Party</option>
        @foreach($clients as $client)
          <option
              value="{{$client->id}}" {{(isset($task)? (($task->client_id == $client->id)? 'selected':''):null)}}>{{ $client->company_name }}</option>
        @endforeach
      </select>
    </div>
    {{-- <div class="form-group @if ($errors->has('start_date')) has-error @endif">
        {!! Form::label('start_date', 'Start Date') !!}<span style="color: red">*</span>
        <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  {!! Form::text('start_date', null, ['class' => 'form-control pull-right', 'id' => 'start_date', 'placeholder' => 'Start Date','required']) !!}
                </div>

        @if ($errors->has('start_date')) <p class="help-block has-error">{{ $errors->first('start_date') }}</p> @endif
    </div> --}}
  </div>
  <div class="col-md-6">
    <div class="form-group">
      {!! Form::label('priority', 'Task Priority') !!}<span style="color: red">*</span>
      <select name="priority" class="form-control select2" required>
        <option value="High" {{isset($task)? (($task->priority == 'High')? 'selected':''):null}}>High</option>
        <option value="Medium" {{isset($task)? (($task->priority == 'Medium')? 'selected':''):null}}>Medium</option>
        <option value="Low" {{isset($task)? (($task->priority == 'Low')? 'selected':''):null}}>Low</option>
      </select>
    </div>
    {{-- <div class="form-group @if ($errors->has('end_date')) has-error @endif">
        {!! Form::label('end_date', 'End Date') !!}<span style="color: red">*</span>
         <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  {!! Form::text('end_date', null, ['class' => 'form-control pull-right', 'id' => 'end_date', 'placeholder' => 'End Date','required']) !!}
                </div>
        @if ($errors->has('end_date')) <p class="help-block has-error">{{ $errors->first('end_date') }}</p> @endif
    </div> --}}
  </div>
</div>
<!-- Text body Form Input -->
<div class="form-group @if ($errors->has('description')) has-error @endif"">
{!! Form::label('description', 'Description') !!}<span style="color: red">*</span>
{!! Form::textarea('description', null, ['class' => 'form-control ckeditor', 'id=description', 'placeholder' => 'About Task...','required']) !!}
@if ($errors->has('description')) <p class="help-block has-error">{{ $errors->first('description') }}</p> @endif
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      {!! Form::label('status', 'Status') !!}
      @if(isset($task->status))
        {!! Form::select('status', array('In Progress' => 'In Progress', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled'), $task->status, ['class' => 'form-control select2']) !!}
      @else
        {!! Form::select('status', array('In Progress' => 'In Progress', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled'), 'In Process', ['class' => 'form-control select2']) !!}
      @endif
    </div>


  </div>
</div>

