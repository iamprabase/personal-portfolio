
    @php($i = 0)

    @foreach($moduleAttributes as $moduleAttribute)

      @php($i++)

      <tr>

        <td>{{ $moduleAttribute->title}}</td>
        @if($moduleAttribute->order_amt_flag==1)
        <td>Yes</td>
        @else
        <td>No</td>
        @endif
        
        @if($moduleAttribute->order_edit_flag==1)
        <td><i class="fa fa-check"></i></td>
        @else
        <td><i class="fa fa-times"></i></td>
        @endif
        
        @if($moduleAttribute->order_delete_flag==1)
        <td><i class="fa fa-check"></i></td>
        @else
        <td><i class="fa fa-times"></i></td>
        @endif
        <td>

            @if($moduleAttribute->title!="Approved" && $moduleAttribute->title!="Pending")
              <a class="btn btn-primary btn-sm rowEditOrderStatus" moduleAttribute-id="{{$moduleAttribute->id}}"
                moduleAttribute-name="{{$moduleAttribute->title}}" @if($moduleAttribute->color) moduleAttribute-color="{{$moduleAttribute->color}}"@endif moduleAttribute-order_amt_flag="{{$moduleAttribute->order_amt_flag}}" moduleAttribute-order_edit_flag="{{$moduleAttribute->order_edit_flag}}" moduleAttribute-order_delete_flag="{{$moduleAttribute->order_delete_flag}}" style=" padding: 3px 6px;"><i class="fa fa-edit"></i></a>
              <a class="btn btn-danger btn-sm delete rowDeleteOrderStatus" moduleAttribute-id="{{$moduleAttribute->id}}"moduleAttribute-name="{{$moduleAttribute->title}}" style="padding: 3px 6px;"><i
                class="fa fa-trash-o"></i></a>
            @elseif($moduleAttribute->title=="Approved" || $moduleAttribute->title=="Pending")
              <a class="btn btn-primary btn-sm rowEditOrderStatus" moduleAttribute-id="{{$moduleAttribute->id}}"
                moduleAttribute-name="{{$moduleAttribute->title}}" @if($moduleAttribute->color)
                moduleAttribute-color="{{$moduleAttribute->color}}"@endif
                moduleAttribute-order_amt_flag="{{$moduleAttribute->order_amt_flag}}" moduleAttribute-order_edit_flag="{{$moduleAttribute->order_edit_flag}}" moduleAttribute-order_delete_flag="{{$moduleAttribute->order_delete_flag}}" style=" padding: 3px 6px;"><i
                  class="fa fa-edit"></i></a>
            @endif
        </td>

      </tr>

    @endforeach