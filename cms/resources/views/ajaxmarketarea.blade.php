@foreach($marketareas as $marketarea)
<li>
  {{ $marketarea->name }} @if($marketarea->childs->count() == 0) <a data-name="{{$marketarea->name}}" data-id="{{$marketarea->id}}" superior-id="{{$marketarea->parent_id}}" edit-url="{{route('app.company.setting.editMarketArea',[$marketarea->id])}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></a>  @endif <span area-id="{{$marketarea->id}}" destroy-url="{{route('app.company.setting.removePartyType',[$marketarea->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
  @if(count($marketarea->childs)>0)
  	<ul>
	  @foreach($marketarea->childs as $child)
	    <li>
	      {{ $child->name }}
	      @if($child->childs->count() == 0) <a data-name="{{$child->name}}" data-id="{{$child->id}}" superior-id="{{$child->parent_id}}" edit-url="{{route('app.company.setting.editMarketArea',[$child->id])}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></a>  @endif
	       <span area-id="{{$child->id}}" destroy-url="{{route('app.company.setting.removePartyType',[$child->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
	      @if(count($child->childs)>0)
	        @include('manageChild',['childs' => $child->childs])
	      @endif
	    </li>
	  @endforeach
	</ul>
  @endif
</li>
@endforeach