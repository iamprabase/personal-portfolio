@forelse($folders as $folder)
<div class="col-xs-3 folderContainer" data-id="{{$folder->id}}">
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
        <div class="panel">
            <div id="partyUploadsFolder{{$folder->id}}">
                <div class="panel-body" style="padding:0px 50px 0px 50px;">
                    <div class="text-center">
                        <a href='#' data-id="{{$folder->id}}"  data-type="{{$folder->type}}" data-folder_name="{{$folder->name}}"
                            data-href="{{domain_route('company.admin.client.getFolderDetails', [$client->id, $folder->id])}}"
                            class="folderTitleClick" onClick="openFiles(this); return false;">
                            <i class="fa fa-folder-open" style="font-size: 100px;color: #ffd500a8;"></i>
                        </a>
                        @if(Auth::user()->can(\Illuminate\Support\Str::singular($type).'uploads-update'))
                          <a class="partyUploadsfolderEdit" data-type="{{$folder->type}}" data-id="{{$folder->id}}" data-folder_name="{{$folder->name}}" style="word-break: break-word;">
                              <i class="fa fa-edit"></i>
                          </a>
                        @endif
                        @if(Auth::user()->can(\Illuminate\Support\Str::singular($type).'uploads-delete'))
                          <a class="partyUploadsfolderDelete" data-type="{{$folder->type}}" href="#" data-id="{{$folder->id}}" data-folder_name="{{$folder->name}}" style="word-break: break-word;">
                              <i class="fa fa-trash"></i>
                          </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="panel-heading" role="tab" id="heading{{$folder->id}}" style="display:flex;align-items: center;justify-content: center;height: 37px !important;color: #000 !important;background-color: #ffffff !important;">
               <span class="panel-title" id="folderNameField{{$folder->id}}">
               <a data-id="{{$folder->id}}" data-type="{{$folder->type}}" data-folder_name="{{$folder->name}}" href="{{domain_route('company.admin.client.getFolderDetails', [$client->id, $folder->id])}}" class="folderTitleClick" style="word-break: break-word;" onClick="openFiles(this); return false;"><span id="folderNameSpan" class="folderNameSpan">{{$folder->name}}</a>
               </span><br/>
            </div>
             <i><small>Last updated: {{getDeltaDate(date('Y-m-d', strtotime($folder->updated_at)))}} </small></i>
        </div>
    </div>
    
</div>
@empty
<div class="col-xs-6 noFolderView">
    <p> No Folders created till now.<p>
</div>
@endforelse