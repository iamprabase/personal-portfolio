@php
  $i = 0   
@endphp
@foreach($visit_purposes as $visit_purpose)
  <tr>
    <td>{{++$i}}</td>
    <td>{{$visit_purpose['title']}}</td>
    <td>
      <a class="btn btn-primary btn-sm edit-visit-purpose-btn" data-id="{{$visit_purpose['id']}}" data-title="{{$visit_purpose['title']}}" data-url="{{ domain_route('company.admin.visitpurpose.update', [$visit_purpose['id']]) }}" style="padding: 3px 6px; height: auto !important;color: blue !important;background-color: transparent !important;border: none;"><i class="fa fa-edit"></i></a>                
      @if($visit_purpose['deleteable'])
        <a class="btn btn-danger btn-sm delete-visit-purpose-btn" data-id="{{$visit_purpose['id']}}" data-title="{{$visit_purpose['title']}}" data-url="{{ domain_route('company.admin.visitpurpose.destroy', [$visit_purpose['id']]) }}" style="padding: 3px 6px; height: auto !important;border: none;color: #fd1818;background-color: #ffffff00!important;"><i class="fa fa-trash-o"></i></a>
      @endif
    </td> 
  </tr>
@endforeach