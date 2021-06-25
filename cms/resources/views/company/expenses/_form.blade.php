<div class="row">
  @if(isset($expense->employee->id))
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('employee_id')) has-error @endif">
      {!! Form::label('employee_id', 'Employee Name') !!}<span style="color: red">*</span>
      {!! Form::text('employee_id', $expense->employee->name, ['class' => 'form-control', 'placeholder' => 'Name','required','disabled']) !!}
               
    </div>
  </div>
  @endif
  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('amount')) has-error @endif">
      {!! Form::label('amount', 'Amount ') !!}<span style="color: red">*</span>
      {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => 'Amount','required']) !!}
      @if ($errors->has('amount')) <p class="help-block has-error">{{ $errors->first('amount') }}</p> @endif
    </div>
  </div>

  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('expense_date')) has-error @endif">
      {!! Form::label('expense_date', 'Expense Incurred Date') !!}<span style="color: red">*</span>
        @if(config('settings.ncal')==0)
          <input class="form-control expenseDate" type="text" name="expense_date" value="{{isset($expense->expense_date)?$expense->expense_date:date('Y-m-d')}}">
        @else
          <input type="text" class="form-control" id="nexp_date" value="{{isset($expense->expense_date)?getDeltaDateFormat($expense->expense_date):getDeltaDateFormat(date('Y-m-d'))}}">
          <input class="form-control expenseDate hide" type="text" id="expenseDate" name="expense_date" value="{{isset($expense->expense_date)?$expense->expense_date:date('Y-m-d')}}">
        @endif
      @if ($errors->has('expense_date')) <p class="help-block has-error">{{ $errors->first('expense_date') }}</p> @endif
    </div>
  </div>

</div>
<div class="row">

  <div class="col-xs-6">
    <div class="form-group @if ($errors->has('expense_type_id')) has-error @endif">
      {!! Form::label('expense_type_id', 'Expense Category') !!}
      <select name="expense_type_id" class="form-control select2">
        <option value="">Select a Expense Category</option>
        @if(isset($expense))
          @foreach($expenseTypes as $expenseType)
            <option value="{{$expenseType->id}}" @if(isset($expense->expense_type_id)) {{(($expense->expense_type_id == $expenseType->id)? 'selected':'' )}} @endif >{{$expenseType->expensetype_name}}</option>
          @endforeach
        @else
          @foreach($expenseTypes as $expenseType)
            <option value="{{$expenseType->id}}">{{$expenseType->expensetype_name}}</option>
          @endforeach
        @endif
      </select>
      @if ($errors->has('expense_type_id')) <p class="help-block has-error">{{ $errors->first('expense_type_id') }}</p> @endif
    </div>
  </div>

  @if(config('settings.party')==1)
  <div class="col-xs-4">
    <div class="form-group @if ($errors->has('client_id')) has-error @endif">
      {!! Form::label('client_id', 'Party Name (Expense for party?)') !!}
      <select name="client_id" class="form-control select2">
        <option value="">Select a Party</option>
        @if(isset($expense))
          @foreach($clients as $client)
            <option
                value="{{$client->id}}" {{(($expense->client_id == $client->id)? 'selected':'' )}} >{{$client->company_name}}</option>
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
  @endif
</div>
<div class="form-group @if ($errors->has('description')) has-error @endif"">
{!! Form::label('description', 'Description') !!}<span style="color: red">*</span>
{!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'About Expense','required']) !!}
@if ($errors->has('description')) <p class="help-block has-error">{{ $errors->first('description') }}</p> @endif
</div>
<div class="row">
  {{-- <div class="col-xs-6">
    <div class="form-group">
      {!! Form::label('remark', 'Remark') !!}
      {!! Form::textarea('remark', null, ['class' => 'form-control', 'placeholder' => 'Remark']) !!}
    </div>
  </div> --}}
  {{-- <div class="col-xs-6">
    <input type="text" name="previous_url" value="{{URL::previous()}}" hidden/> 
    <div class="form-group @if ($errors->has('expense_photo')) has-error @endif">
      <label>Picture proof(Bill, Receipt, e.t.c)</label>
      <i>*max 3
        images {{isset($image_count)? '('. $image_count .' uploaded, '. (3 - $image_count) .' remaining)':'' }}</i>
      <input type="file" class="form-control" id="expense_photo" name="expense_photo[]" placeholder="Image" multiple>
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
    </div>
    @if ($errors->has('expense_photo')) <p class="help-block has-error">{{ $errors->first('expense_photo') }}</p> @endif
  </div> --}}
  <div class="col-xs-6">
    <input type="text" name="previous_url" value="{{URL::previous()}}" hidden />
    <div id="imggroup" class="form-group @if ($errors->has('expense_photo')) has-error @endif">
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
                <input type="file" id="expense_photo" name="expense_photo[]" class="uploadFile img" 
                value="cms/{{$image->image_path}}" style="width: 0px;height: 0px;overflow: hidden;">
                <span hidden>
                  <input type="text" name="expense_photo_id[]" value="{{$image->id}}" hidden />
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
              <input type="file" id="receipt" name="expense_photo[]" class="uploadFile img" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
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
<input type="hidden" name="DT_Exp_FILTER" class="DT_Exp_FILTER">