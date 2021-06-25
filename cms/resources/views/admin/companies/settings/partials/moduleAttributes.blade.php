<div class="tab-pane fade" role="tabpanel" id="set-dynamic-type" aria-labelledby="set-dynamic-status-tab">
  <div class="col-xs-12">
    <h3>Modules</h3>
  </div>
  <div class="info">
    <div class="col-xs-12"> 
        {{-- {!! Form::open(['route' => ['app.company.setting.addpartytype'],'id'=>'frmAddParty' ]) !!} --}}
      {{-- <input type="hidden" name="company_id" value="{{$setting->id}}">
      @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>
        </div>
      @endif
      <div class="form-group {{ $errors->has('modules') ? 'has-error' : '' }}">
        {!! Form::label('Select Modules') !!} --}}
        {{-- {!! Form::select('parent_id',$allPartiestype, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Party Type']) !!} --}}
        {{-- <select id="modules" name="modules" class="form-control" placeholder="Select Party Type">
          <option>Select Modules</option> --}}
          {{-- @foreach($allmodules as $party)
            <option value="{{$party['id']}}">{{$party['name']}}</option>
          @endforeach --}}
          {{-- <option value="1">Orders</option>
          <option value="2">Products</option>
          <option value="3">Employees</option>
        </select>
      </div>

      <div class="form-group {{ $errors->has('module_status') ? 'has-error' : '' }}">
        {!! Form::label('Select Status') !!} --}}
        {{-- {!! Form::select('parent_id',$allPartiestype, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Party Type']) !!} --}}
        {{-- <select id="module_status" name="module_status" class="form-control" placeholder="Select Party Type">
          <option>Select Status</option> --}}
          {{-- @foreach($allmodules as $party)
            <option value="{{$party['id']}}">{{$party['name']}}</option>
          @endforeach --}}
          {{-- <option value="Active">Active</option>
          <option value="In-active">In-active</option>
        </select>
      </div>

      <div class="form-group">
        <button id="btnAddParty" class="btn btn-success">Add New</button>
      </div> --}}

        {{-- {!! Form::close() !!} --}}
    {{-- </div>
  </div>
</div>