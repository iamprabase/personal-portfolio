@foreach($childs as $child)
	<?php 
        $stringName = str_replace(' ','-',$child->name).'-'.'create';
        $childpermission = \Spatie\Permission\Models\Permission::where('company_id',config('settings.company_id'))->where('name',$stringName)->first();
        $hasPartyChildPermission = false;
        if(isset($client->id)){
          if($childpermission){
            $hasPartyChildPermission = $child->id==$client->client_type?true:Auth::user()->hasPermissionTo($childpermission->id);
          }else{
            $hasPartyChildPermission = $child->id==$client->client_type?true:false;
          }
        }else{
          $hasPartyChildPermission = $childpermission?Auth::user()->hasPermissionTo($childpermission->id):false;
        }
    ?>
    @if($hasPartyChildPermission)
    @if(isset($client->id))
        <option {{ $client->client_type != $child->id ?: 'selected' }} value="{{$child->id}}">{{$child->name}}</option>
    @else
        <option {{ old('client_type') != $child->id ?: 'selected' }}  value="{{$child->id}}">{{$child->name}}</option>
    @endif
    @endif
    @if(count($child->childs)>0)
      @include('company.clients.partials_show.partyChilds',['childs' => $child->childs])
    @endif
@endforeach


