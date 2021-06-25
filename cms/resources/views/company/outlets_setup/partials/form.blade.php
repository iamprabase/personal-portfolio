  
  {!! Form::open(array('url' => url(domain_route("company.admin.outlets.connection.store", ["domain" =>request("subdomain")])), 'method' => 'post', 'files'=> false, 'id'=>'outlet-connection-form')) !!}
  
  <div class="col-xs-12">
    <div class="form-group @if ($errors->has('client_id')) has-error @endif">
      {!! Form::label('client_id', 'Select Party') !!}<span style="color: red">*</span>
      <div class="input-group" style="width: 100% !important">
        {!! Form::select('client_id', $clients, null, ['class' => 'form-control select2', 'id' => 'clients', 'required'=>true, "placeholder" => ""]) !!}
      </div>
      @if ($errors->has('client_id')) <p class="help-block has-error">{{ $errors->first('client_id') }}</p> @endif
      <div class="client_id err_div">

      </div>
    </div>
  </div>
  
  <div class="col-xs-12">
    <div class="form-group @if ($errors->has('secret_code')) has-error @endif">
      {!! Form::label('secret_code', 'Outlet Secret Code') !!}<span style="color: red">*</span>
      <div class="input-group">
        <div class="input-group-addon">
          <i class="fa fa-key"></i>
        </div>
        {!! Form::text('secret_code', null, ['class' => 'form-control pull-right', 'id' => 'secret_code', 'autocomplete'=>'off', 'placeholder' => 'Outlet Secret Code','required']) !!}
      </div>
  
      @if ($errors->has('secret_code')) <p class="help-block has-error">{{ $errors->first('secret_code') }}</p> @endif
      <div class="secret_code err_div">
      
      </div>
    </div>
  </div>

  <div class="col-xs-12">
    <!-- Submit Form Button -->
    {!! Form::submit('Submit', ['class' => 'btn btn-default connect-outlet-btn pull-right', 'id'=>'connect-outlet']) !!}
    {{-- {!! Form::button('Close', ['class' => 'btn btn-warning connect-outlet-btn pull-right', 'data-dismiss'=>'modal'])!!} --}}
  </div>

  {!! Form::close() !!} 