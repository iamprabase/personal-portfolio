@php($i = 0)
          
  @foreach($business_types as $business)
    @php($i++)
    <tr>
      
      <td>{{ $i }}</td>
      
      <td>{{ $business->business_name}}</td>
      <td>
        <a class="btn btn-primary btn-sm edit-business_type" data-id="{{ $business->id }}" data-name="{{$business->business_name}}"
           data-url="{{ domain_route('company.admin.business_type.update', [$business->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
              class="fa fa-edit"></i></a>
        @if($business->clients->count()==0)
        <a class="btn btn-danger btn-sm delete-business_type" data-id="{{ $business->id }}"
           data-url="{{ domain_route('company.admin.business_type.destroy', [$business->id]) }}" data-toggle="modal"
           data-target="#delete" style="padding: 3px 6px; height: auto !important;"><i
              class="fa fa-trash-o"></i></a>
        @endif
      </td>
    </tr>
@endforeach