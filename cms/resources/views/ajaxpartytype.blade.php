 @foreach($partytypes as $partytype)
    <li>
      {{ $partytype->name }} @if($partytype->childs->count() == 0) <a data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{route('app.company.setting.editPartyType',[$partytype->id])}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></a> 
        <span area-id="{{$partytype->id}}" destroy-url="{{route('app.company.setting.removePartyType',[$partytype->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
      @else   
      <p data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{route('app.company.setting.editPartyType',[$partytype->id])}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></p>

      @endif 
      @if(count($partytype->childs))
        @include('managePartyChild',['childs' => $partytype->childs])
      @endif
    </li>
@endforeach