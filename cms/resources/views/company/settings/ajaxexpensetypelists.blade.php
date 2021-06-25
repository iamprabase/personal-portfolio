@php($i = 0)
          
  @foreach($expense_types as $expense)
    @php($i++)
    <tr>
      
      <td>{{ $i }}</td>
      
      <td>{{ $expense->expensetype_name}}</td>
      <td>
        <a class="btn btn-primary btn-sm edit-expense_type" data-id="{{ $expense->id }}" data-name="{{$expense->expensetype_name}}"
           data-url="{{ domain_route('company.admin.expense_type.update', [$expense->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
              class="fa fa-edit"></i></a>
        @if($expense->expenses->count()==0)
        <a class="btn btn-danger btn-sm delete-expense_type" data-id="{{ $expense->id }}"
           data-url="{{ domain_route('company.admin.expense_type.destroy', [$expense->id]) }}" data-toggle="modal"
           data-target="#delete" style="padding: 3px 6px; height: auto !important;"><i
              class="fa fa-trash-o"></i></a>
        @endif
      </td>
    </tr>
@endforeach