<ul>
  @foreach($childs as $child)
    <li>
      {{ $child->name }}@if($child->childs->count() == 0) <a data-id="{{$child->id}}" data-name="{{$child->name}}" data-short-name="{{$child->short_name}}" superior-id="{{$child->parent_id}}" edit-url="{{route('app.company.setting.editPartyType',[$child->id])}}" data-ticked="{{$child->allow_salesman}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></a>
        <span area-id="{{$child->id}}" destroy-url="{{route('app.company.setting.removePartyType',[$child->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
      @else
      <p data-name="{{$child->name}}" data-short-name="{{$child->short_name}}" data-id="{{$child->id}}" superior-id="{{$child->parent_id}}" edit-url="{{route('app.company.setting.editPartyType',[$child->id])}}" data-ticked="{{$child->allow_salesman}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></p>
        @endif 
      @if(count($child->childs))
        @include('company.partytypes.managePartyChild',['childs' => $child->childs])
      @endif
    </li>
  @endforeach
</ul>