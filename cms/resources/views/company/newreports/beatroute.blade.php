@if(isset($beat_route))
@php $i=1 @endphp
@foreach($beat_route as $emp_id=>$routreport)
<tr>
  <td>{{$routreport[0]}}</td>
  <td data-name="{{implode(',',$routreport[1])}}" data-title="Target Parties" class="view"
    style="color: blue;cursor: pointer;">{{count($routreport[1])}}
  </td>
  <td data-name="{{implode(',',$routreport[2])}}" data-title="Actual Visited Parties" class="view"
    style="color: blue;cursor: pointer;">{{count($routreport[2])}}
  </td>
  <td data-name="{{implode(',',$routreport[3])}}" data-title="Parties with Effective Calls" class="view"
    style="color: blue;cursor: pointer;">{{count($routreport[3])}}
  </td>
  <td data-name="{{implode(',',$routreport[4])}}" data-title="Parties with Unscheduled  Effective Calls" class="view"
    style="color: blue;cursor: pointer;">{{count($routreport[4])}}
  </td>
  <td data-name="{{implode(',',$routreport[5])}}" data-title="Parties with Non-effective Calls" class="view"
    style="color: blue;cursor: pointer;">{{count($routreport[5])}}
  </td>
  <td data-name="{{implode(',',$routreport[6])}}" data-title="Parties with Unscheduled Non-Effective Calls" class="view"
    style="color: blue;cursor: pointer;">{{count($routreport[6])}}
  </td>
  <td data-name="{{implode(',',$routreport[7])}}" data-title="Parties Not Covered" class="view"
    style="color: blue;cursor: pointer;">{{count($routreport[7])}}
  </td>
  <td>
    <span data-name="{{implode(',',$routreport[8])}}">{{implode(',',$routreport[8])}}
  </td>
  <td>
    @if($routreport[9]!=NULL)
    <span data-name="{{implode(',',$routreport[9])}}">{{implode(',',$routreport[9])}}</span>
    @else
    <span></span>
    @endif
  </td>
  <td>
    <a href='{{domain_route("company.admin.gpscomparison")}}'>
      <span data-ename="{{$routreport[0]}}" data-id="{{$emp_id}}"
        data-effective_calls="@if(!empty($clients_ids[$emp_id][0])){{$clients_ids[$emp_id][0]}}@endif"
        data-non_effective_calls="@if(!empty($clients_ids[$emp_id][1])){{$clients_ids[$emp_id][1]}}@endif"
        data-not_covered="@if(!empty($clients_ids[$emp_id][2])){{$clients_ids[$emp_id][2]}}@endif" class="getLocations">
        <i class="fa fa-map-marker" aria-hidden="true"></i>
      </span>
    </a>
  </td>
</tr>
@php ++$i @endphp
@endforeach
@elseif(isset($emp))
@php $i=1 @endphp
@foreach($emp as $date=>$routreport)
<input type="hidden" value="{{$emp_name}}" id="rep_salesman_name">
<tr>
  {{-- <td> {{$i}} </td> --}}
  @if(config('settings.ncal')==0)
  <td>{{$date}}</td>
  @else
  <td>{{getDeltaDateFormat($date)}}</td>
  @endif
  <td data-name="{{implode(',',$routreport['employee_target_visits'])}}" data-title="Target Parties" class="viewsingle"
    style="color: blue;cursor: pointer;">{{count($routreport['employee_target_visits'])}}
  </td>
  <td data-name="{{implode(',',$routreport['employee_actual_visits'])}}" data-title="Actual Visited Parties"
    class="viewsingle" style="color: blue;cursor: pointer;">{{count($routreport['employee_actual_visits'])}}
  </td>
  <td data-name="{{implode(',',$routreport['employee_effective_calls'])}}" data-title="Parties with Effective Calls"
    class="viewsingle" style="color: blue;cursor: pointer;">{{count($routreport['employee_effective_calls'])}}
  </td>
  <td data-name="{{implode(',',$routreport['unscheduled_effective_calls'])}}"
    data-title="Parties with Unscheduled  Effective Calls" class="viewsingle" style="color: blue;cursor: pointer;">
    {{count($routreport['unscheduled_effective_calls'])}}
  </td>
  <td data-name="{{implode(',',$routreport['employee_non_effective_calls'])}}"
    data-title="Parties with Non-effective Calls" class="viewsingle" style="color: blue;cursor: pointer;">
    {{count($routreport['employee_non_effective_calls'])}}
  </td>
  <td data-name="{{implode(',',$routreport['unscheduled_non_effective_calls'])}}"
    data-title="Parties with Unscheduled Non-Effective Calls" class="viewsingle" style="color: blue;cursor: pointer;">
    {{count($routreport['unscheduled_non_effective_calls'])}}
  </td>
  <td data-name="{{implode(',',$routreport['not_covered'])}}" data-title="Parties Not Covered" class="viewsingle"
    style="color: blue;cursor: pointer;">{{count($routreport['not_covered'])}}
  </td>
  <td data-name="{{implode(',',$routreport['beats'])}}">{{implode(',',$routreport['beats'])}}
  </td>
  <td data-name="{{implode(',',$routreport['beat_names'])}}">{{implode(',',$routreport['beat_names'])}}
  </td>
  <td><a href='{{domain_route("company.admin.gpscomparison")}}'>
      <span data-ename="{{$emp_name}}" data-id="{{$emp_id}}" data-date="{{$date}}"
        data-effective_calls="@if(!empty($clients_ids[$date][0])){{$clients_ids[$date][0]}}@endif"
        data-non_effective_calls="@if(!empty($clients_ids[$date][1])){{$clients_ids[$date][1]}}@endif"
        data-not_covered="@if(!empty($clients_ids[$date][2])){{$clients_ids[$date][2]}}@endif"
        class="singleGetLocations"> <i class="fa fa-map-marker" aria-hidden="true"></i>
      </span>
    </a>
  </td>
</tr>
@php ++$i @endphp
@endforeach
@endif