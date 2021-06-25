<tbody id="customModule">
@foreach($custom_modules_field as $key => $field)
    <tr class="row1" data-id="{{$field->id}}">
        <td>{{$key + 1}} <i class="fa fa-bars" id="handle-sort"></i></td>
        <td style="width:280px;max-width: 280px ;cursor: pointer; color: #01a9ac;"
            onclick="editField({{$field}} , $(this));">
            {{$field->title}} <span style="color: black">{{$field->required == 1 ? '*' : ''}}</span>

        </td>
        <td>{{$field->type}}</td>
        <td>
            @if($field->status == 'Active')
                <a href='#' class='edit-modal' data-id='{{$field->id}}'
                   data-status='{{$field->status}}'>
                    <span class='label label-success'>{{$field->status}}</span>
                </a>
            @elseif($field->status == 'Inactive')
                <a href='#' class='edit-modal' data-id='{{$field->id}}'
                   data-status='{{$field->status}}'>
                    <span class='label label-danger'>{{$field->status}}</span>
                </a>
            @endif

        </td>

        <td>
            <a class='btn btn-danger btn-sm delete' data-mid='{{$field->id}}'
               data-url='{{domain_route('company.admin.custom.modules.field.destroy', [$field->id])}}'
               data-toggle='modal' data-target='#delete' style='padding: 3px 6px;'><i
                        class='fa fa-trash-o'></i></a>
        </td>
    </tr>
@endforeach

</tbody>