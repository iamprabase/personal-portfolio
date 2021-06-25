<div class="col-xs-12">
  <h3 class="site-tital"> Manage Area </h3>
</div>
<div class="info">
  <div class="row">
    <div class="col-xs-6">
      <ul id="tree2">
        @foreach($marketareas as $marketarea)
          <li>
            {{ $marketarea->name }} 
            @if($marketarea->childs->count() == 0) <a data-name="{{$marketarea->name}}" data-id="{{$marketarea->id}}" superior-id="{{$marketarea->parent_id}}" edit-url="{{route('app.company.setting.editMarketArea',[$marketarea->id])}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></a>  @endif
             <span area-id="{{$marketarea->id}}" destroy-url="{{route('app.company.setting.removeMarketArea',[$marketarea->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
            @if(count($marketarea->childs))
              @include('manageChild',['childs' => $marketarea->childs])
            @endif
          </li>
        @endforeach
      </ul>
    </div>
    <div class="col-xs-6">
      <h3>Add New Area</h3>


      {!! Form::open(['route' => ['app.company.setting.addmarketarea'],'id'=>'frmAddArea' ]) !!}

      <input type="hidden" name="company_id" value="{{$setting->id}}">
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


      <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
        {!! Form::label('Area:') !!}
        {{-- {!! Form::select('parent_id',$allMarketareas, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Area']) !!} --}}
        <select name="parent_id" class="form-control" placeholder="Select Area">
          <option>Select Area</option>
          @foreach($allMarketareas as $area)
            <option value="{{$area['id']}}">{{$area['name']}}</option>
          @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('parent_id') }}</span>
      </div>


      <div class="form-group">
        <button id="btnAddArea" class="btn btn-success">Add New</button>
      </div>


      {!! Form::close() !!}


    </div>
  </div>
</div>