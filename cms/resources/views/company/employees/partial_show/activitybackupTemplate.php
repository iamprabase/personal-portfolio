@php($i = 0)
@foreach($activities as $activity)
@php($i++)
<tr>
  <td>{{ $i }}</td>
  <td>
    {{-- {{ isset($activity->start_datetime)?getDeltaDate(Carbon\Carbon::parse($activity->start_datetime)->format('Y-m-d')).' '.Carbon\Carbon::parse($activity->start_datetime)->format('H:i'):NULL}}
    --}}
    {{ isset($activity->start_datetime)?getDeltaDate(date('Y-m-d', strtotime($activity->start_datetime))).' '.date('H:i', strtotime($activity->start_datetime)) : NULL }}
  </td>
  <td>{{ $activity->title }}</td>
  <td>
    @if($activity->client_id!=NULL)
    @if(isset($activity->client->company_name))

    $access =
    \DB::table('handles')->where('company_id',config('settings.company_id'))->where('employee_id',Auth::user()->EmployeeId())->where('client_id',$activity->client_id)->first();
    ?>
    @if(isset($access))
    <a
      href="{{domain_route('company.admin.client.show',[$activity->client_id])}}">{{ $activity->client->company_name }}</a>
    @else
    <a href="#" class="alert_party_model">{{ $activity->client->company_name }}</a>
    @endif
    @else
    <span class="hidden">{{$activity->client_id}}</span>
    @endif
    @endif
  </td>
  <td>{{ isset($activity->activityType->name)?$activity->activityType->name:NULL}}</td>
  <td>
    @if($activity->created_by==0)
    {{ucfirst(Auth::user()->name)}}
    @elseif(isset($activity->createdByEmployee->name))
    @if(in_array($activity->created_by,$allSup))
    <a href="#" class="alert-user-modal"
      datasalesman="{{$activity->createdByEmployee->name}}">{{$activity->createdByEmployee->name}}</a>
    @else
    <a href="{{domain_route('company.admin.employee.show',[$activity->createdByEmployee->id])}}"
      datasalesman="{{$activity->createdByEmployee->name}}">{{$activity->createdByEmployee->name}}</a>
    @endif
    @else
    <span hidden>{{$activity->created_by}}</span>
    @endif
  </td>
  <td>
    @if($activity->assigned_to==0)
    ALL
    @else
    @if(isset($activity->assignedTo->name))
    @if(in_array($activity->assigned_to,$allSup))
    <a href="#" class="alert-user-modal">{{$activity->assignedTo->name}}</a>
    @else
    <a href="{{domain_route('company.admin.employee.show',[$activity->assigned_to])}}">
      {{$activity->assignedTo->name}}</a>
    @endif
    @else
    <span hidden>{{$activity->assigned_to}}-activity_id={{$activity->id}}</span>
    @endif
    @endif
  </td>
  <td>

    if($activity->completion_datetime!=NULL){
    $checkedStatus = "no_uncheck";
    }else{
    $checkedStatus = "no_check";
    }
    ?>
    @if(Auth::user()->can('activity-status') && (Auth::user()->isCompanyManager() ||
    Auth::user()->EmployeeId()==$activity->created_by || Auth::user()->EmployeeId()==$activity->assigned_to)
    )
    <div class="round"><input type="checkbox" id="act{{$activity->id}}" class="check check_{{$activity->id}}"
        name="status" value="{{$activity->id}}" {{ ($activity->completion_datetime!=NULL)?'checked="checked"':''}}>
      <label for="act{{$activity->id}}"></label>
    </div>
    @else
    <div class="round"><input type="checkbox" id="act{{$activity->id}}" readonly="readonly" class="{{$checkedStatus}}"
        name="status" value="{{$activity->id}}" {{ ($activity->completion_datetime!=NULL)?'checked="checked"':''}}>
      <label for="act{{$activity->id}}"></label>
    </div>
    @endif
  </td>
  <td>
    <a style="color:green;font-size: 15px;margin-left:5px;  "
      href="{{ domain_route('company.admin.activities.show',[$activity->id]) }}" class="" style=""><i
        class="fa fa-eye"></i></a>
    $empId = Auth::user()->EmployeeId(); ?>
    @if(Auth::user()->can('activity-update'))
    @if(Auth::user()->isCompanyManager() || $empId== $activity->created_by || $empId ==
    $activity->assigned_to)
    <a style="color:#f0ad4e!important;font-size: 15px;margin-left:5px;  "
      href="{{ domain_route('company.admin.activities.edit',[$activity->id]) }}" class="" style=""><i
        class="fa fa-edit"></i></a>
    @endif
    @endif
    @if((Auth::user()->isCompanyManager() && Auth::user()->can('activity-delete')) ||
    $activity->created_by==Auth::user()->EmployeeID() && Auth::user()->can('activity-delete'))
    <a style="color:red;font-size: 15px;margin-left:5px;" data-mid="{{ $activity->id }}"
      data-url="{{ domain_route('company.admin.activities.destroy', [$activity->id]) }}" data-toggle="modal"
      data-target="#delete" style=""><i class="fa fa-trash-o"></i></a>
    @endif
  </td>
</tr>
@endforeach