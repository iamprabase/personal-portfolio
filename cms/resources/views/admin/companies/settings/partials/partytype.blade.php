<div class="col-xs-12">
  <h3 class="site-tital"> Party Type</h3>
</div>
<div class="info">

  <div class="row">
    <div class="col-xs-6">
      <ul id="tree1">
        @foreach($partytypes as $partytype)
          <li>
            {{ $partytype->name }} @if($partytype->childs->count() == 0) <a data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{route('app.company.setting.editPartyType',[$partytype->id])}}" data-ticked="{{$partytype->allow_salesman}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></a> 
              <span area-id="{{$partytype->id}}" destroy-url="{{route('app.company.setting.removePartyType',[$partytype->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
            @else   
            <p data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{route('app.company.setting.editPartyType',[$partytype->id])}}" data-ticked="{{$partytype->allow_salesman}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></p>
            @endif 
            @if(count($partytype->childs))
              @include('managePartyChild',['childs' => $partytype->childs])
            @endif
          </li>
        @endforeach
      </ul>
    </div>
    <div class="col-xs-6">
      <h3>Add New Type</h3>


      {!! Form::open(['route' => ['app.company.setting.addpartytype'],'id'=>'frmAddParty' ]) !!}

      <input type="hidden" name="company_id" value="{{$clientSettings->id}}">
      @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>
        </div>
      @endif


      <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
        {!! Form::label('Name:') !!}
        {!! Form::text('name', old('name'), ['class'=>'form-control', 'placeholder'=>'Enter Name']) !!}
        <span class="text-danger">{{ $errors->first('name') }}</span>
      </div>

      <div class="form-group {{ $errors->has('sname') ? 'has-error' : '' }}">
        {!! Form::label('Short Name:') !!}
        {!! Form::text('short_name', old('short_name'), ['class'=>'form-control', 'placeholder'=>'Enter Short Name','maxlength'=>"3"]) !!}
        <span class="text-danger">{{ $errors->first('sname') }}</span>
      </div>


      <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
        {!! Form::label('Superior:') !!}
        {{-- {!! Form::select('parent_id',$allPartiestype, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Party Type']) !!} --}}
        <select id="select_party_types" name="parent_id" class="form-control" placeholder="Select Party Type">
          <option>Select Party Type</option>
          @foreach($allPartiestype as $party)
            <option value="{{$party['id']}}">{{$party['name']}}</option>
          @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
      </div>

      <div class="form-group">
        <button id="btnAddParty" class="btn btn-success">Add New</button>
      </div>
      
      {!! Form::close() !!}

    </div>
  </div>
</div>