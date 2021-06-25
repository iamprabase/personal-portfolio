<ul>
  @foreach($childs as $child)
    <li>
      <a class="btn btn-sm button-blue">{{$child->name}} ( {{$child->designations->name}} ) </a>
      @if(count($child->childs))
        @include('company.employees.partial_show.manageEmployeeChild',['childs' => $child->childs])
      @endif
    </li>
  @endforeach
</ul>