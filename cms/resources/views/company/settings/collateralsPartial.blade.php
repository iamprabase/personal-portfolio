@forelse($companyCollateralFolders as $companyCollateralFolder)
<div class="col-xs-4 folderContainer">
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
        <div class="panel">
            <div id="companyCollateralFolder{{$companyCollateralFolder->id}}">
                <div class="panel-body" style="padding:0px 50px 0px 50px;">
                    <div>
                        <a data-id="{{$companyCollateralFolder->id}}" data-folder_name="{{$companyCollateralFolder->folder_name}}"
                            href="{{domain_route('company.admin.setting.viewCollateralsFolder',['id'=>$companyCollateralFolder->id])}}"
                            class="folderTitleClick" onClick="loadFileView(this);">
                            <i class="fa fa-folder-open" style="font-size: 100px;color: #ffd500a8;"></i>
                        </a>
                    </div>
                    <div>
                        <a class="btn btn-warning btn-sm collateralFolderEdit" data-id="{{$companyCollateralFolder->id}}" data-folder_name="{{$companyCollateralFolder->folder_name}}" style="word-break: break-word;" onClick="editFolderName(this);">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a class="btn btn-danger btn-sm collateralFolderDelete" href="{{domain_route('company.admin.setting.deleteCollateralsFolder')}}" data-id="{{$companyCollateralFolder->id}}" data-folder_name="{{$companyCollateralFolder->folder_name}}" style="word-break: break-word;" onClick="deleteFolderName(this);">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-heading" role="tab" id="heading{{$companyCollateralFolder->id}}" style="display:flex;align-items: center;justify-content: center;height: 37px !important;">
               <span class="panel-title" id="folderNameField{{$companyCollateralFolder->id}}">
               <a data-id="{{$companyCollateralFolder->id}}" data-folder_name="{{$companyCollateralFolder->folder_name}}" href="{{domain_route('company.admin.setting.viewCollateralsFolder',['id'=>$companyCollateralFolder->id])}}" class="folderTitleClick" style="word-break: break-word;" onClick="loadFileView(this);"><span id="folderNameSpan" class="folderNameSpan">{{$companyCollateralFolder->folder_name}}<span class="tooltiptext">{{$companyCollateralFolder->folder_name}}</span></span></a>
               </span>
            </div>
        </div>
    </div>
</div>
@empty
<div class="col-xs-6">
    <p> No Folders created till now.<p>
</div>
@endforelse
