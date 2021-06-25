<style>
    .btn-file {
        position: relative;
        overflow: hidden;
    }

    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }

    .fileList {
        padding-top: 10px;
        list-style-type: square;
        line-height: 2.5;
        word-break: break-all;
    }
    .edit_fileList {
        padding-top: 10px;
        list-style-type: square;
        line-height: 2.5,
        word-break: break-all,
    }

    .edit_fileList {
      word-break: break-all,
    }
</style>

<div class="col-xs-12 site-tital">
    <div class="col-xs-6">
        <h3>{{$folder_name}}</h3>
    </div>
    <div class="col-xs-4" style="margin-top:25px;">
        <span style="padding-left: 80px;width: fit-content;">
            <a href="#" data-toggle="modal" data-target="#myFileUploadModal">
                <i class="fa fa-plus"></i>
                <strong>Upload File</strong>
            </a>
        </span>
    </div>
    <div class="col-xs-2 pull-right" style="top: 15px;">
        <a href="{{ domain_route('company.admin.setting.viewCollateralsFolderHome') }}" class="btn btn-default btn-sm"
            onClick="loadFolderView(this);"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
</div>
<div class="info col-xs-12" style="max-height: auto;overflow-y: scroll;">
    <div class="box-header">
    </div>
    <div class="box-body">
        <div class="row appendTableView">
            @include('company.settings.collateralsFileView')
        </div>
    </div>
</div>

<div class="modal fade" id="myFileUploadModal" role="dialog">
    <div class="modal-dialog modal-md">
        {!! Form::open(array('url' => url(domain_route("company.admin.setting.uploadCollateralsFiles")), 'method' => 'post', 'enctype'=>"multipart/form-data", 'files'=> true, 'id'=>'collateralFileUpload')) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Select Files to Upload</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="files" id="files1">
                        <span class="btn btn-default btn-file" style="margin: 0px 100px 0px 150px;width: 230px;">
                            Browse <input type="file" name="files1" multiple style="width: inherit;" />
                        </span>
                        <br />
                        <ul class="fileList"></ul>
                    </div>
                    <p class="help-block has-error">
                        <span class="fileNameErr"></span>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit('Upload Files', ['class' => 'btn btn-primary pull-right', 'id'=>'addFiles']) !!}

            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<div class="modal fade" id="editFileUploadModal" role="dialog">
    <div class="modal-dialog modal-md">
        {!! Form::open(array('url' => url(domain_route("company.admin.setting.editCollateralsFiles")),
        'method' => 'post', 'enctype'=>"multipart/form-data", 'files'=> true, 'id'=>'editCollateralFileUpload')) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit File:- </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('file-name', 'File Name:- ') !!}<br/>
                    <span style="color: red">If none default file name will be used.</span>
                    
                    {!! Form::text('file-name', null, ['class' => 'form-control', 'placeholder' => 'File Name', 'id'=>'edit-fileName']) !!}
                    <input type="hidden" id="edit-hiddenFileName">
                    <input type="hidden" id="edit-hiddenFileId">
                </div>
                <div class="form-group">
                    {!! Form::label('existing-file', 'Existing File:- ') !!}<br />
                    <ul class="edit_fileList1">
                
                    </ul>
                </div>
                <div class="form-group">
                    <div class="edit_files" id="edit_files1">
                        <span class="btn btn-default btn-file" style="margin: 0px 100px 0px 150px;width: 230px;">
                            Browse <input type="file" name="files1" style="width: inherit;" />
                        </span>
                        <br />
                        <ul class="edit_fileList" style="word-break: break-all;"></ul>
                    </div>
                    <p class="help-block has-error">
                        <span class="edit_fileNameErr"></span>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::submit('Submit', ['class' => 'btn btn-primary pull-right', 'id'=>'editFiles']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<div class="modal modal-default fade" id="deleteFileUploadModal" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" unit="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
            </div>
            {{-- <form id="deleteCollateralFileUpload" method="post"> --}}
                <div class="modal-body">
                    <p class="text-center">
                        Are you sure you want to delete the file?
                    </p>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
                    <button id="deleteFile" type="submit" class="btn btn-warning">Yes, Delete</button>
                </div>
            {{-- </form> --}}
        </div>
    </div>
</div>
<script>
    function showLoader(){
        $("#loader1").removeAttr("hidden");
        $(".box-loaderClass").addClass("box-loader");
    }
    function hideLoader(){
        $("#loader1").attr("hidden", "hidden");
        $(".box-loaderClass").removeClass("box-loader");
    }
    $('#collateralFilePartialTable').DataTable({"columnDefs": [
    { "orderable": false, "targets": [-1] } // Applies the option to all columns
    ]});
    $.fn.fileUploader = function (filesToUpload, sectionIdentifier) {
        var fileIdCounter = 0;
        this.closest(".files").change(function (evt) {
            var output = [];
    
            for (var i = 0; i < evt.target.files.length; i++) { 
                fileIdCounter++; 
                var file=evt.target.files[i]; 
                var fileId=sectionIdentifier + fileIdCounter; 
                filesToUpload.push({ id: fileId, file: file }); 
                var removeLink="<a class=\" removeFile\" href=\"#\" data-fileid=\"" + fileId + "\">Remove</a>";
                output.push("<li><strong>", escape(file.name), "</strong> - ", file.size, " bytes. &nbsp; &nbsp; ", removeLink, "</li> ");
            };
            $(this).children(".fileList").append(output.join(""));
            //reset the input to null - nice little chrome bug!
            evt.target.value = null;
        });
        $(this).on("click", ".removeFile", function (e) {
            e.preventDefault();
            var fileId = $(this).parent().children("a").data("fileid");
            // loop through the files array and check if the name of that file matches FileName
            // and get the index of the match
            for (var i = 0; i < filesToUpload.length; ++i) {
                if (filesToUpload[i].id===fileId) filesToUpload.splice(i, 1); 
            }
            $(this).parent().remove(); 
        }); 
        this.clear=function () { 
            for (var i=0; i < filesToUpload.length; ++i) { 
                if(filesToUpload[i].id.indexOf(sectionIdentifier)>= 0)
                    filesToUpload.splice(i, 1);
            }
            $(this).children(".fileList").empty();
        }
        return this;
    };
    
    (function () {
        var filesToUpload = [];

        var files1Uploader = $("#files1").fileUploader(filesToUpload, "files1");
           
        $("#addFiles").click(function (e) {
            e.preventDefault();
            $('.fileNameErr').html("");
            var folderName = "{!! $folder_name !!}";
            var folderId = "{{$folder_id}}";
            var formData = new FormData();
    
            for (var i = 0, len = filesToUpload.length; i < len; i++) { 
                formData.append("files[]", filesToUpload[i].file);
            }
            formData.append("folderName",folderName);
            formData.append("folderId",folderId);
            $.ajax({ 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.setting.uploadCollateralsFiles')}}", 
                data: formData, 
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                type: "POST", 
                beforeSend:function(data, url){
                    $("#addFiles").attr('disabled', true);
                    showLoader();
                },
                success: function (data) {
                    hideLoader(); 
                    $("#addFiles").attr('disabled', false);
                    files1Uploader.clear(); 
                    $('#myFileUploadModal').modal('hide');
                    alert(data.message);
                    $('.appendTable').html('');
                    $('.appendTableView').html(data.view);
                    $('#collateralFilePartialTable').DataTable({"columnDefs": [
                    { "orderable": false, "targets": [-1] } // Applies the option to all columns
                    ]});
                }, 
                error: function (xhr) {
                    hideLoader();
                    $("#addFiles").attr('disabled', false);
                    $.each(xhr.responseJSON.errors, function(key,value) {
                        console.log(value[0]);
                        $('.fileNameErr').append('<li>'+value[0]+'</li>'); 
                    });
                }
            });
        });
    })();

    $('#myFileUploadModal').on('show.bs.modal', function () {
        $('.fileNameErr').html('');
    });

    function loadFolderView(identifier){
        event.preventDefault();
        let url = $(identifier)[0].href;

        $.ajax({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: "GET",
            data: {
            '_token': '{{csrf_token()}}',
            },
            beforeSend:function(data, url){
                showLoader();
            },
            success:function(data){
                $('#collaterals-detail').html(data);
            },
            error(xhr){
                hideLoader();
            }
        });
    }

    function editFile(identifier){
        event.preventDefault();
        $('#editFileUploadModal').modal();
        $("#editCollateralFileUpload")[0].reset();
        $(".edit_fileList1").html('');
        let url = $(identifier)[0].href;
        let folderId = $(identifier).attr('folder-id');
        let fileId = $(identifier).attr('file-id');
        let filePath = $(identifier).attr('server-path')+$(identifier).attr('file-path');
        let fileName = $(identifier).attr('file-name');
        let fileExtension = $(identifier).attr('file-extension');
        let fileSize = $(identifier).attr('file-size');
        $("#editCollateralFileUpload").find('#edit-fileName').val(fileName);
        $(".edit_fileList1").append("<li><strong>"+ fileName+"."+ fileExtension +"</strong>"+"- "+ fileSize +" bytes.</li>");
        $("#edit-hiddenFileName").val(fileName);
        $("#edit-hiddenFileId").val(fileId);
    }

    $.fn.fileUploader = function (filesToUpload, sectionIdentifier) {
        var fileIdCounter = 0;
        this.closest(".edit_files").change(function (evt) {
            var output = [];
            $(".edit_fileList").html("");
            for (var i = 0; i < evt.target.files.length; i++) { 
                $('#edit-fileName').val("");
                fileIdCounter++; 
                var file=evt.target.files[i]; 
                $('.edit_fileList1').children().css('text-decoration', 'line-through');
                var fileId=sectionIdentifier + fileIdCounter; 
                filesToUpload.push({ id: fileId, file: file }); 
                var removeLink="<a class=\" edit_removeFile\" href=\"#\" data-fileid=\"" + fileId + "\">Remove</a>";
                output.push("<li><strong>", escape(file.name), "</strong> - ", file.size, " bytes. &nbsp; &nbsp; ", removeLink, "</li> ");
            };
            $(this).children(".edit_fileList").append(output.join(""));
            //reset the input to null - nice little chrome bug!
            evt.target.value = null;
        });
        $(this).on("click", ".edit_removeFile", function (e) {
            e.preventDefault();
            $('#edit-fileName').val("");
            $('#edit-fileName').val($("#edit-hiddenFileName").val());
            $('.edit_fileList1').children().css('text-decoration', 'none');
            var fileId = $(this).parent().children("a").data("fileid");
            // loop through the files array and check if the name of that file matches FileName
            // and get the index of the match
            for (var i = 0; i < filesToUpload.length; ++i) {
                if (filesToUpload[i].id===fileId) filesToUpload.splice(i, 1); 
            }
            $(this).parent().remove(); 
        }); 
        this.clear=function () { 
            for (var i=0; i < filesToUpload.length; ++i) { 
                if(filesToUpload[i].id.indexOf(sectionIdentifier)>= 0)
                    filesToUpload.splice(i, 1);
            }
            $(this).children(".edit_fileList").empty();
        }
        return this;
    };
    
    (function () {
        var filesToUpload = [];

        var files1Uploader = $("#edit_files1").fileUploader(filesToUpload, "files1");
           
        $("#editFiles").click(function (e) {
            e.preventDefault();
            $('.edit_fileNameErr').html("");
            var folderName = "{!! $folder_name !!}";
            var fileName = $("#editCollateralFileUpload").find('#edit-fileName').val();
            var fileId = $("#editCollateralFileUpload").find('#edit-hiddenFileId').val();
            var folderId = "{{$folder_id}}";
            var formData = new FormData();
    
            for (var i = 0, len = filesToUpload.length; i < len; i++) { 
                formData.append("files", filesToUpload[i].file);
            }
            formData.append("folderName",folderName);
            formData.append("folderId",folderId);
            formData.append("fileName",fileName);
            formData.append("fileId",fileId);
            $.ajax({ 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{domain_route('company.admin.setting.editCollateralsFiles')}}", 
                data: formData, 
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                type: "POST", 
                beforeSend:function(data, url){
                    $("#editFiles").attr('disabled', true);
                    showLoader();
                },
                success: function (data) { 
                    hideLoader();
                    $("#editFiles").attr('disabled', false);
                    files1Uploader.clear(); 
                    $('#editFileUploadModal').modal('hide');
                    alert(data.message);
                    $('.appendTable').html('');
                    $('.appendTableView').html(data.view);
                    $('#collateralFilePartialTable').DataTable({"columnDefs": [
                    { "orderable": false, "targets": [-1] } // Applies the option to all columns
                    ]});
                }, 
                error: function (xhr) {
                    hideLoader();
                    $("#editFiles").attr('disabled', false);
                    $.each(xhr.responseJSON.errors, function(key,value) {
                        $('.edit_fileNameErr').html(value); 
                    });
                }
            });
        });
    })();

    function deleteFile(identifier){
        event.preventDefault();
        let url = $(identifier)[0].href;
        let folderId = $(identifier).attr('folder-id');
        let fileId = $(identifier).attr('file-id');
        $('#deleteFileUploadModal').modal();
        event.preventDefault();
        $("#deleteFile").click(function (e) {
            e.preventDefault();
            $.ajax({ 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url, 
                data: {
                    "folderId": folderId,
                    "fileId": fileId
                }, 
                type: "POST", 
                beforeSend:function(data, url){
                    showLoader();
                    $("#deleteFile").attr('disabled', true);
                },
                success: function (data) { 
                    hideLoader();
                    $("#deleteFile").attr('disabled', false);
                    $('#deleteFileUploadModal').modal('hide');
                    alert(data.message);
                    $('.appendTable').html('');
                    $('.appendTableView').html(data.view);
                    $('#collateralFilePartialTable').DataTable({"columnDefs": [
                    { "orderable": false, "targets": [-1] } // Applies the option to all columns
                    ]});
                }, 
                error: function (xhr) {
                    hideLoader();
                    $("#deleteFile").attr('disabled', false);
                    $.each(xhr.responseJSON.errors, function(key,value) {
                        alert(value); 
                    });
                }
            });
        });
    }

</script>