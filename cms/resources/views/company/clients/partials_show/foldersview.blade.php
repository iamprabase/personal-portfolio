<div class="col-xs-12 site-tital">
  <div class="col-xs-4">
      <h3>{{$title}} Uploads</h3>
  </div>
  <div class="col-xs-4 pull-right" style="margin-top:25px;">
      <span>
      @if(Auth::user()->can(\Illuminate\Support\Str::singular($type).'uploads-create'))
        <a href="#" data-toggle="modal" data-target="#{{$type}}FolderCreateModal">
          <i class="fa fa-plus"></i>
              <strong>Create New Folder</strong>
        </a>
      @endif
      </span>
  </div>
</div>
<div class="info col-xs-12" style="height: 700px;overflow-y: scroll;">
  @if($type=="files")
  <div class="box-header searchBarFolders">
      <div class="searchBar text-center">
        <i id="iconSearch" class="fa fa-search"></i>
        <input type="text" placeholder="Search" id="folderSearchBar" class="folderSearchBar" data-type="{{$type}}">
        <small style='padding-left: 10px;color: #524b4b;letter-spacing: 0.2px;'>  {{$total_size}} GB of {{$total_upload_allowed}} GB used / {{$percent_used}} % of total allocated space</small>
      </div>
  </div>
  @endif
  <div class="box-body">
      <div id= "folderPartial" class="{{$type}}foldersView">
          @if($type=="files")
          @include('company.clients.partials_show.foldersviewpartial', ['folders' => $folders])
          @else
          @include('company.clients.partials_show.imagefoldersviewpartial', ['folders' => $folders])
          @endif
      </div>
  </div>
</div>  

<!-- Modal -->
<div class="modal fade folderCreateModal" id="{{$type}}FolderCreateModal" role="dialog">
  <div class="modal-dialog modal-sm small-modal">
    {!! Form::open(array('url' => url(domain_route('company.admin.client.createUploadFolders', [$client->id])), 'method' => 'post', 'class'=>'createFolders')) !!}
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Create New Folder</h4>
      </div>
      <div class="modal-body">
          <div class="form-group @if ($errors->has('name')) has-error @endif">    
              {!! Form::label('folder_name', 'Folder Name') !!}<span style="color: red">*</span>
              {!! Form::text('folder_name', null, ['class' => 'form-control', 'placeholder' => 'Folder Name',
              'required']) !!}
              <p class="help-block has-error">
                <span class="folderNameErr"></span>
              </p>
              <input type="hidden" name="type" class="type" value="{{$type}}">
          </div>
      </div>
      <div class="modal-footer">
          {!! Form::submit('Add Folder', ['class' => 'btn btn-primary pull-right addFolder']) !!}
      </div>
    </div>
    {!! Form::close() !!}
  </div>
</div>

<!-- Modal -->
<div class="modal fade folderEditModal" id="{{$type}}FolderEditModal" role="dialog">
  <div class="modal-dialog modal-sm small-modal">
    {!! Form::open(array('url' => url(domain_route('company.admin.client.updateUploadFolders', [$client->id])), 'method' => 'post', 'class'=>'editFolders')) !!}
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Update Folder</h4>
      </div>
      <div class="modal-body">
          <div class="form-group @if ($errors->has('name')) has-error @endif">    
              {!! Form::label('folder_name', 'Folder Name') !!}<span style="color: red">*</span>
              {!! Form::text('folder_name', null, ['class' => 'form-control folder_name', 'placeholder' => 'Folder Name',
              'required']) !!}
              <p class="help-block has-error">
                <span class="folderNameErr"></span>
              </p>
              <input type="hidden" name="type" class="type" value="{{$type}}">
              <input type="hidden" name="folderId" class="folder_id">
          </div>
      </div>
      <div class="modal-footer">
          {!! Form::submit('Update Folder', ['class' => 'btn btn-primary pull-right updateFolder']) !!}
      </div>
    </div>
    {!! Form::close() !!}
  </div>
</div>

<div class="modal modal-default fade folderDeleteModal" id="{{$type}}FolderDeleteModal" tabindex="-1" unit="dialog"
      aria-labelledby="myModalLabel">
  <div class="modal-dialog" unit="document">
    {!! Form::open(array('url' => url(domain_route('company.admin.client.deleteUploadFolders', [$client->id])), 'method' => 'post', 'class'=>'deleteFolders')) !!}
      @method('delete')
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
              <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <div class="modal-body">
              <p class="text-center">
                  Are you sure you want to delete the folder? Deleting the folder will permanently delete all the files inside it?
              </p>
              <input type="hidden" name="type" class="type" value="{{$type}}">
              <input type="hidden" name="folderId" class="folder_id">
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-warning deleteFolder">Yes, Delete</button>
          </div>
      </div>
    {!! Form::close() !!}
  </div>
</div>