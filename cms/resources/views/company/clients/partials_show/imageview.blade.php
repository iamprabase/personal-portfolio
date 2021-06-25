<div class="col-xs-12 site-tital">
  <div class="col-xs-4">
  <a href="{{domain_route('company.admin.client.getFolderView', [$client_id])}}" onClick="loadFolderView(this);  return false;" data-type="{{$type}}" class="btn btn-default pull-left" style="position: relative;margin: 10px;">
        <i class="fa fa-arrow-left"></i>
          Back
      </a>
      <h3>{{$folder_title}}</h3>
  </div>
  <div class="col-xs-4 pull-right" style="margin-top:25px;">
      <span>
        <strong class="imagesmaxLimit {{$file_count<20 ? 'hidden': ''}}" style="color: #fbb002fa;">Maximum limit of 20 items reached.</strong>
        <input type="hidden" class="imagesCount" value="{{$file_count}}">
        @if(Auth::user()->can(\Illuminate\Support\Str::singular($type).'uploads-create'))  
        <a href="#" class="imagesLink {{$file_count>=20 ? 'hidden': ''}}" data-toggle="modal" data-target="#{{$type}}FileCreateModal">
          <i class="fa fa-plus"></i>
                <strong>Add New File</strong>
        </a>
        @endif
      </span>
  </div>
</div>
<div class="info col-xs-12" >
  <div class="box-body">
      <div id= "filePartial" class="{{$type}}View gallery">
       
      </div>
  </div>
</div>  

<!-- Modal -->
<div class="modal fade fileCreateModal" id="{{$type}}FileCreateModal" role="dialog">
  <div class="modal-dialog modal-sm small-modal">
    {!! Form::open(array('url' => url(domain_route('company.admin.client.uploadFileFolder', [$client_id, $folder_id])), 'method' => 'post','enctype'=>"multipart/form-data", 'files'=> true, 'class'=>'createFiles '. $type.'uploadForm')) !!}
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Upload {{ucwords($type)}}</h4>
      </div>
      <div class="modal-body fileCreateModalBody" style="max-height: calc(100% - 120px);overflow-y: scroll;">
          <div class="form-group @if ($errors->has('name')) has-error @endif">
            <div class="col-xs-3 imgPreview" style="width: max-content;">
              <label class="btn btn-default"> Select File 
                <input id="chosenUpload" type="file" name="chosenUpload[]" class="uploaderInput img" data-type="{{$type}}" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;"  accept="image/*"  multiple required>
              </label>
            </div>
            <!-- <i class="fa fa-plus imgAdd" data-type="{{$type}}"></i> -->
          </div>
          <input type="hidden" name="type" class="type" value="{{$type}}">
          <input type="hidden" name="folder_id" class="folder_id" value="{{$folder_id}}">
        </div>
        <div class="modal-footer">
          <div class="errValue" style="color:red;float: left;"></div>
          {!! Form::submit('Upload File', ['class' => 'btn btn-primary pull-right uploadFile']) !!}
      </div>
    </div>
    {!! Form::close() !!}
  </div>
</div>

<!-- Modal -->
<div class="modal fade fileEditModal" id="{{$type}}FileEditModal" role="dialog">
  <div class="modal-dialog modal-sm small-modal">
    {!! Form::open(array('url' => "#", 'method' => 'post', 'class'=>'editFiles '. $type.'eduploadForm')) !!}
    @method('patch')
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Update {{ucwords($type)}}</h4>
      </div>
      <div class="modal-body fileCreateModalBody" style="max-height: calc(100% - 120px);overflow-y: scroll;">
          <div class="form-group @if ($errors->has('name')) has-error @endif">    
              {!! Form::label('file_name', 'File Name') !!}<span style="color: red">*</span>
              {!! Form::text('file_name', null, ['class' => 'form-control file_name', 'placehile' => 'File Name', 'required']) !!}
              <p class="help-block has-error">
                <span class="fileNameErr"></span>
              </p>
              <input type="hidden" name="type" class="type" value="{{$type}}">
              <input type="hidden" name="fileId" class="file_id">
          </div>
          @if($type=="files")
          <div class="form-group @if ($errors->has('name')) has-error @endif">
            <div class="col-xs-3 imgUp">
              <div class="imagePreview" style="height: 150px;width:100%">

              </div>
              <label class="btn btn-default"> Select File 
                <input id="chosenUpload" type="file" name="chosenUpload" class="uploaderInput img" data-type="{{$type}}" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
              </label>
              <i class="fa fa-times editDel" data-type="{{$type}}"></i>
            </div><!-- col-2 -->
          </div>
          @endif

      </div>
      <div class="modal-footer">
          {!! Form::submit('Update File', ['class' => 'btn btn-primary pull-right updateFile']) !!}
      </div>
    </div>
    {!! Form::close() !!}
  </div>
</div>

<div class="modal modal-default fade fileDeleteModal" id="{{$type}}FileDeleteModal" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel" style="z-index: 1505;">
  <div class="modal-dialog" unit="document">
    {!! Form::open(array('url' => '#', 'method' => 'post', 'class'=>'deleteFile')) !!}
      @method('delete')
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                      aria-hidden="true">&times;</span></button>
              <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <div class="modal-body">
              <p class="text-center">
                  Are you sure you want to delete the file?
              </p>
              <input type="hidden" name="type" class="type" value="{{$type}}">
              <input type="hidden" name="fileId" class="file_id">
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-warning deleteFile">Yes, Delete</button>
          </div>
      </div>
    {!! Form::close() !!}
  </div>
</div>