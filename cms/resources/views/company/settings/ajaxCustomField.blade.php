@foreach($custom_fields->where('for','Party') as $customField)
<tr>
  <td style="min-width:250px!important; cursor: pointer; color: #01a9ac;" onclick="editField({{$customField}} , $(this));">
                    {{$customField->title}}
  </td>
  <td>{{$customField->type}}</td>
  <td style="max-width:100px!important;">
   <a href="#" class="edit-modal" data-id="{{$customField->id}}"
                       data-status="{{$customField->status}}">
                       @if($customField->status==1)
                        <span class="label label-success">Active</span>
                      @else
                       <span class="label label-danger">Inactive</span>
                      @endif
                    </a>
    <!-- @if($customField->status==1)
      <a href="#" class="statusupdate_customField" data-id="{{$customField->id}}">
              <span class="label label-success" style="font-size: 12px !important;margin: 7px 0px;display: inline-block;    padding: 5px;"> Active</span>
              </a>
               @else
              <a href="#" class="statusupdate_customField" data-id="{{$customField->id}}">
              <span class="label label-danger" style="font-size: 12px !important;margin: 7px 0px; display: inline-block;    padding: 5px;"> Inactive</span>
              </a>
          
          @endif -->
      
    <span class="customfield_refresh_{{$customField->id}} hide">
        <button class="success-btn-right refreshing"><i class="fa fa-refresh"></i></button>
    </span>
  </td>
</tr>
@endforeach