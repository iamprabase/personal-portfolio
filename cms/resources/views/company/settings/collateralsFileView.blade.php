<div id="loader1" hidden>
    <img src="{{asset('assets/dist/img/loader2.gif')}}" />
</div>
<div class="box-loaderClass">
    <div class="box-body">
        <table class="table table-bordered table-striped" id="collateralFilePartialTable">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>FileName</th>
                    <th>Owner</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php $i=0 @endphp
                @forelse($companyCollateralFiles as $companyCollateralFile)
                <tr>
                    <td>{{++$i}}</td>
                    <td>{{$companyCollateralFile->file_name}}.{{$companyCollateralFile->file_extension}}</td>
                    <td>{{(getUser($companyCollateralFile->uploaded_by))?getUser($companyCollateralFile->uploaded_by)->name:NULL}}</td>
                    <td>
                        <a href="{{$companyCollateralFile->server_path}}{{$companyCollateralFile->file_path}}" target="_blank" style=" padding: 3px 6px;" download="{{$companyCollateralFile->file_name}}.{{$companyCollateralFile->file_extension}}"><i
                                class="fa fa-download"></i></a>
                        <a href="{{domain_route('company.admin.setting.editCollateralsFiles')}}"  class="btn btn-warning btn-sm collateralFileEdit" file-id="{{$companyCollateralFile->id}}" file-name="{{$companyCollateralFile->file_name}}" file-extension="{{$companyCollateralFile->file_extension}}" folder-id="{{$companyCollateralFile->collateral_folder_id}}" server-path="{{$companyCollateralFile->server_path}}" file-path="{{$companyCollateralFile->file_path}}" file-size="{{$companyCollateralFile->file_size}}" style=" padding: 3px 6px;" onclick="editFile(this);"><i class="fa fa-edit"></i></a>
    
                        <a href="{{domain_route('company.admin.setting.deleteCollateralsFiles')}}" class="btn btn-danger btn-sm collateralFileDelete" file-id="{{$companyCollateralFile->id}}" file-name="{{$companyCollateralFile->file_name}}" file-extension="{{$companyCollateralFile->file_extension}}" folder-id="{{$companyCollateralFile->collateral_folder_id}}" server-path="{{$companyCollateralFile->server_path}}" file-path="{{$companyCollateralFile->file_path}}" style="padding: 3px 6px;" onclick="deleteFile(this);"><i class="fa fa-trash-o"></i></a>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>
