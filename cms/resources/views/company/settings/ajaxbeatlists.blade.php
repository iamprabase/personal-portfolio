@php($i = 0)
  @foreach($beats as $beat)
    @php($i++)
    <tr>
      
      <td>{{ $i }}</td>
      
      <td>{{ $beat->name}}</td>
      
      <td>
        <a class="btn btn-success btn-sm beat-view" data-name="{{$beat->name}}" data-city="{{$beat->city}}" data-bid="{{ $beat->id }}"
           data-url="{{ domain_route('company.admin.beat.show', [$beat->id]) }}" style="color:green!important;background-color:transparent!important;padding: 3px 6px; height: auto !important;border: none;"><i
              class="fa fa-eye"></i></a>
        <a class="btn btn-primary btn-sm beat-edit" data-name="{{$beat->name}}" data-city="{{$beat->city_id}}" data-bid="{{ $beat->id }}" data-edit-url="{{ domain_route('company.admin.beat.edit', [$beat->id]) }}"
           data-url="{{ domain_route('company.admin.beat.update', [$beat->id]) }}" style="color:blue!important;background-color:transparent!important;padding: 3px 6px; height: auto !important;border: none;"><i
              class="fa fa-edit"></i></a>
        <?php $del=1; ?>
        @if(in_array($beat->id, $beatsArray))
          <?php $del = 0; ?>
        @endif
        @if($beat->parties->count()>0)
          <?php $del = 0; ?>
        @endif
        @if($del==1)
        <a class="btn btn-danger btn-sm beat-delete" data-bid="{{ $beat->id }}"
           data-url="{{ domain_route('company.admin.beat.destroy', [$beat->id]) }}"  style="padding: 3px 6px; height: auto !important;"><i
              class="fa fa-trash-o"></i></a>
        @endif
      </td>
    </tr>
@endforeach