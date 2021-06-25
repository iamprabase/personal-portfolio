<ul>
  @foreach($childs as $child)
    <li>
      {{ $child->name }} @if($child->childs->count() == 0) <a data-name="{{$child->name}}" data-id="{{$child->id}}" superior-id="{{$child->parent_id}}" edit-url="{{route('app.company.setting.editMarketArea',[$child->id])}}" data-ticked="{{$child->allow_salesman}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></a>
       <span area-id="{{$child->id}}" destroy-url="{{route('app.company.setting.removeMarketArea',[$child->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
      @else   
        <p data-name="{{$child->name}}" data-id="{{$child->id}}" superior-id="{{$child->parent_id}}" edit-url="{{route('app.company.setting.editPartyType',[$child->id])}}" data-ticked="{{$child->allow_salesman}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></p>
        @endif
      @if(count($child->childs))
        @include('manageChild',['childs' => $child->childs])
      @endif
    </li>
  @endforeach
</ul>