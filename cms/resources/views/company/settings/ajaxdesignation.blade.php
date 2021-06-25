 <?php $i=1; ?>
 @foreach($designations as $designation)
    <tr>
      <td>{{ $i }}</td>

      <td>{{ $designation->name}}</td>
      @if($designation->parent_id==0)
        <td>--</td>
      @else
        <td>{{ $designation->parent->name}}</td>
      @endif

      <td>
        <a class="btn btn-primary btn-sm editBtnDesignation" data-id="{{ $designation->id }}" data-url="{{ domain_route('company.admin.designation.update', [$designation->id]) }}" data-name="{{$designation->name}}" style="padding: 3px 6px; height: auto !important;"><i class="fa fa-edit"></i></a>
        @if($designation->employees->count()==0 && $designation->parent_id!=0)
        <a class="btn btn-danger btn-sm deleteBtnDesignation" data-id="{{ $designation->id }}" data-url="{{ domain_route('company.admin.designation.destroy', [$designation->id]) }}" style="padding: 3px 6px; height: auto !important;"><i class="fa fa-trash-o"></i></a>           
        @endif
      </td>

    </tr>
 <?php $i++; ?>
@endforeach