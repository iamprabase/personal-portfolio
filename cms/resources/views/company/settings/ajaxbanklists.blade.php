@php($i = 0)
          
  @foreach($banks as $bank)
    @php($i++)
    <tr>
      
      <td>{{ $i }}</td>
      
      <td>{{ $bank->name}}</td>
      
      <td>
        <a class="btn btn-primary btn-sm edit-bank" data-id="{{ $bank->id }}" data-name="{{$bank->name}}"
           data-url="{{ domain_route('company.admin.bank.update', [$bank->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
              class="fa fa-edit"></i></a>
        @if($bank->cheques->count()==0)
        <a class="btn btn-danger btn-sm delete-bank" data-id="{{ $bank->id }}"
           data-url="{{ domain_route('company.admin.bank.destroy', [$bank->id]) }}" data-toggle="modal"
           data-target="#delete" style="padding: 3px 6px; height: auto !important;"><i
              class="fa fa-trash-o"></i></a>
        @endif
      
      </td>
    
    </tr>
  
@endforeach