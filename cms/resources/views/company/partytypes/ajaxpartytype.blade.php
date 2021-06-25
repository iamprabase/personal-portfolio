 @foreach($partytypes as $partytype)
    <li>
      {{ $partytype->name }} @if($partytype->childs->count() == 0) <a data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{domain_route('company.admin.partytype.update',[$partytype->id])}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></a> 
        <span area-id="{{$partytype->id}}" destroy-url="{{domain_route('company.admin.partytype.destroy',[$partytype->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
      @else
      <p data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{domain_route('company.admin.partytype.update',[$partytype->id])}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></p>
      @endif 
      @if(count($partytype->childs))
        @include('company.partytypes.managePartyChild',['childs' => $partytype->childs])
      @endif
    </li>
@endforeach