@php $i = 0 @endphp
@foreach($activities as $activity)
@php ($i++) @endphp
<tr>
  <td>{{ $i }}</td>
  <td>
    <input type="checkbox" class="check" name="status" value="{{$activity->id}}"
    {{ ($activity->completion_datetime!=NULL)?'checked="checked"':''}} >
  </td>
  <td>
    @if($activity->created_by==0)
    <span datasalesman="{{ucfirst(Auth::user()->managerName($activity->company_id)['name'])}}">{{ucfirst(Auth::user()->managerName($activity->company_id)['name'])}}</span>
    @elseif(isset($activity->createdByEmployee->name))
      @if(in_array($activity->created_by,$allSup))
        <a href="#" class="alert-modal" datasalesman="{{$activity->createdByEmployee->name}}">{{$activity->createdByEmployee->name}}</a>
      @else
        <a href="{{domain_route('company.admin.employee.show',[$activity->createdByEmployee->id])}}" datasalesman="{{$activity->createdByEmployee->name}}">{{$activity->createdByEmployee->name}}</a>
      @endif
    @else
    <span datasalesman="{{$activity->created_by}}" hidden>{{$activity->created_by}}</span>
    @endif
  </td>
  <td>
    @if($activity->assigned_to==0)
    <span datasalesman="ALL">ALL</span>
    @else
    @if(isset($activity->assignedTo->name))
    
      @if(in_array($activity->assigned_to,$allSup))
        <a datasalesman="{{$activity->assignedTo->name}}" href="#" class="alert-modal">{{$activity->assignedTo->name}}</a>
      @else
        <a datasalesman="{{$activity->assignedTo->name}}" href="{{domain_route('company.admin.employee.show',[$activity->assignedTo->id])}}">{{$activity->assignedTo->name}}</a>
      @endif
    @else
    <span datasalesman="{{$activity->assigned_to}}" hidden>{{$activity->assigned_to}}-activity_id={{$activity->id}}</span>
    @endif
    @endif
  </td>
  <td>{{ $activity->title}}</td>
  <td>
    @if($activity->client_id!=NULL)
    @if(isset($activity->client->company_name))
    <a datasalesman="{{$activity->client->company_name}}" href="{{domain_route('company.admin.client.show',[$activity->client_id])}}">{{$activity->client->company_name}}</a>
    @else
    <span datasalesman="{{$activity->client_id}}" class="hidden">{{$activity->client_id}}</span>
    @endif
    @else
    <span datasalesman="None">None</span>
    @endif
  </td>
  <td>{{ isset($activity->activityType)?$activity->activityType->name:NULL}}</td>

  <td><span class="text-red">{{ isset($activity->activityPriority)?$activity->activityPriority->name:NULL}}</span></td>
  <?php
  $time_1 = date('Y-m-d',strtotime($activity->start_datetime));
  if($time_1 < '1945-01-01'){
    $time_1 = '1945-01-01';
  }
  ?>
  <td>{{getDeltaDate($time_1)}} {{Carbon\Carbon::parse($activity->start_datetime)->format('H:i')}}</td>
  <td>
    <a style="color:green;font-size: 15px;margin-left:5px;  "
    href="{{ domain_route('company.admin.activities.show',[$activity->id]) }}" class="" style=""><i
    class="fa fa-eye"></i></a>
    <a style="color:#f0ad4e!important;font-size: 15px;margin-left:5px;  "
    href="{{ domain_route('company.admin.activities.edit',[$activity->id]) }}" class="" style=""><i
    class="fa fa-edit"></i></a>
    <a style="color:red;font-size: 15px;margin-left:5px;  " data-mid="{{ $activity->id }}"
      data-url="{{ domain_route('company.admin.activities.destroy', [$activity->id]) }}"
      data-toggle="modal" data-target="#delete" style=""><i class="fa fa-trash-o"></i></a>
    </td>
  </tr>
@endforeach