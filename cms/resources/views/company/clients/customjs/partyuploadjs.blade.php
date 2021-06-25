<script>
const globalSetting = @json($globalSettings);
/**
 * Size is in Kb
 */
const filesAllowedUploadTypes = globalSetting.party_file_upload_types.split(',');
const imagesAllowedUploadTypes = globalSetting.party_image_upload_types.split(',');
const filesAllowedUploadSize = globalSetting.party_file_upload_size;
const imagesAllowedUploadSize = globalSetting.party_image_upload_size;

var filesChoosenPrev = [];
function initializeDT(className, folderId, type){
  let columns = [{ "data": "id" },
                { "data": "file_name" },
                { "data": "last_modified" },
                { "data": "added_by" },
                { "data": "action" }];
  let action = "{{ domain_route('company.admin.client.getFolderDetailsDT', ['clientId', 'folderId']) }}";
  action = action.replace("clientId", "{{$client->id}}");
  action = action.replace("folderId", folderId);
  $(`.${className}`).DataTable({
    "columnDefs": [
    { "orderable": false, "targets": [0,-1] }
    ],
    language: { search: "" },
    "order": [[ 0, "desc" ]],
    "serverSide": true,
    "processing": true,
    "paging": true,
    "ajax":{
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      "url": action,
      "data": {
        type
      },
      "dataType": "json",
      "type": "POST",
      beforeSend:function(url, data){
        showLoader();
      },
      error:function(){
        hideLoader();
      },
      complete:function(data){
        hideLoader();
      }
    },
    "columns": columns,
  });
}  

$(document).on("click", ".display-img", function(e){
  $(modal).find(".titleCaption").html("");
  $(modal).find(".actionButtons").html("");
  modal.style.display = "block";
  modalImg.src = this.src;
  $(modal).find(".titleCaption").append("<span>"+this.dataset.name+"</span>");
  $(modal).find(".titleCaption").append("<span>"+this.dataset.lastUpdated+"</span> <br/> ");
  $(modal).find(".actionButtons").append("<div>"+this.dataset.downloadaction+"</div> ");
  $(modal).find(".actionButtons").append("<div>"+this.dataset.deleteaction+"</div> ");
  let id = e.target.parentElement.parentElement.dataset.key;
  $(modal).find(".previous").attr("key",id);
  $(modal).find(".next").attr("key",id);
});

$(document).on('close.bs.modal', '.custommodal', function (e) {
	document.removeEventListener("onkeydown");
});


function showNextOrPrev(key){
  let el = $('.gallery').find(`[data-key=${key}]`).children().find('.img-responsive')[0];
  let src = $('.gallery').find(`[data-key=${key}]`).children().find('.img-responsive')[0].currentSrc;
  let dataset = el.dataset;
  let deleteaction = dataset.deleteaction;
  let downloadaction = dataset.downloadaction;
  let lastUpdated = dataset.lastUpdated;
  let name = dataset.name;

  $(modal).find(".titleCaption").html("");
  $(modal).find(".actionButtons").html("");
  modal.style.display = "block";
  modalImg.src = src;
  $(modal).find(".titleCaption").append("<span>"+name+"</span>");
  $(modal).find(".titleCaption").append("<span>"+lastUpdated+"</span> <br/> ");
  $(modal).find(".actionButtons").append("<div>"+downloadaction+"</div> ");
  $(modal).find(".actionButtons").append("<div>"+deleteaction+"</div> ");

  $(modal).find(".previous").attr("key",key);
  $(modal).find(".next").attr("key",key);
}

$(document).on("click", ".next", function(e){
  let key = e.target.attributes.key.nodeValue;
  var newKey = $('.gallery').find(`[data-key=${key}]`).next().data("key");
  if(!newKey){
    newKey = $($('.gallery-item')[0]).data("key");
  }
  showNextOrPrev(newKey);
});
$(document).on("click", ".previous", function(e){
  let key = e.target.attributes.key.nodeValue;
  var newKey = $('.gallery').find(`[data-key=${key}]`).prev().data("key");
  if(!newKey){
    newKey = $($('.gallery-item')[$('.gallery-item').length-1]).data("key");
  }
  showNextOrPrev(newKey);
});

function renderImages(addItem, folderId, type){
  
  // let imageViewOpeningTags = `<div class="col-xs-4">
  //                 <div class="imagePreview imageExistsPreview"
  //                   style="background-color: #fff;background-position: center center;background-size: contain;background-repeat: no-repeat;margin-right:2px;box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.2);">`;
  // let imageViewClosingTags = `</div></div>`;
  let action = "{{ domain_route('company.admin.client.getImagesDetails', ['clientId', 'folderId']) }}";
  action = action.replace("clientId", "{{$client->id}}");
  action = action.replace("folderId", folderId);
  if(addItem){
    // showLoader();
    addItem.forEach(element => {
      let id = element.id;
      let imageViewOpeningTags = `<div class="gallery-item" data-key='${id}'>
                  <div class="gcontent">`;
      let imageViewClosingTags = `</div></div>`;
      let imgSrc = element.url;
      let addedBy = "Uploaded By: "+element.added_by;
      let lastUpdated = "Last Updated: "+element.last_updated;
      let delBtn = element.delete_action;
      let downloadBtn = element.download_action;
      let imgBody = `<img class='img-responsive display-img' src='${imgSrc}' data-last-updated='${lastUpdated}' data-name='${addedBy}' data-downloadaction="${downloadBtn}" data-deleteaction="${delBtn}" alt='Picture Displays here' style='width: 100%;'/>`;
      let el = `<div class="imgBlock" data-key='${id}'>${imageViewOpeningTags} ${imgBody} ${imageViewClosingTags}</div>`;
      $('.imagesView').prepend(el);
    });
    // hideLoader();
  }else{
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      "url": action,
      "data": {
        type
      },
      "dataType": "json",
      "type": "GET",
      beforeSend:function(url, data){
        showLoader();
      },
      success: function(response){
        showLoader();
        let publicPath = @json(\Storage::disk('s3'));
        if(response.status===200){
          let images = response.data;
          if(images.length > 0){
            images.forEach(element => {
              let id = element.id;
              let imageViewOpeningTags = `<div class="gallery-item" data-key='${id}'>
                  <div class="gcontent" >`;
              let imageViewClosingTags = `</div></div>`;
              let imgSrc = element.url;
              let addedBy = "Uploaded By: "+element.added_by;
              let lastUpdated = "Last Updated: "+element.last_updated;
              let delBtn = element.delete_action;
              let downloadBtn = element.download_action;
              let imgBody = `<img class='img-responsive display-img' src='${imgSrc}' data-last-updated='${lastUpdated}' data-name='${addedBy}' data-downloadaction="${downloadBtn}" data-deleteaction="${delBtn}" alt='Picture Displays here' style='width:100%;'/>`;
              let el = `${imageViewOpeningTags} ${imgBody} ${imageViewClosingTags}`;
              $('.imagesView').append(el);
            });
          }
        }
        hideLoader(); 
      },
      error:function(xhr){
        hideLoader();
        alert(xhr.responseText);
      },
      complete:function(data){
        hideLoader();
      }
    });
  }

  return false;
}

$(document).on('show.bs.modal', '.folderCreateModal', function (e) {
	$(this).find('.createFolders').find('.folderNameErr').html('');
	$(this).find('.addFolder').attr('disabled', false);
});

$(document).on('click', '.partyUploadsfolderEdit', function (e) {
  let name = $(this).data('folder_name');
  let type = $(this).data('type');
  let folderId = $(this).data('id');
  let el = $(`#${type}FolderEditModal`);
  el.modal();
	el.find('.editFolders').find('.folderNameErr').html('')
  el.find('.updateFolder').attr('disabled', false);
  el.find('.folder_name').val(name);
  el.find('.folder_id').val(folderId);
});

$(document).on('click', '.partyUploadsfolderDelete', function (e) {
  let type = $(this).data('type');
  let folderId = $(this).data('id')
  let el = $(`#${type}FolderDeleteModal`);
  el.modal();
  el.find('.deleteFolder').attr('disabled', false);
  el.find('.folder_id').val(folderId);
});

$(document).on('click', '.partyUploadsfileEdit', function (e) {
  let type = $(this).data('type');
  let fileName = $(this).data('file_name');
  let fileId = $(this).data('file-id');
  let el = $(`#${type}FileEditModal`);
  el.modal();
	el.find('.editFolders').find('.fileNameErr').html('')
  el.find('.imagePreview').html("<div style='margin: 25% 0;font-size: x-large;width: 100%;color: #fff;font-size: 18px;background: #5d5757;'>"+ fileName +"</div>")
  el.find('.updateFile').attr('disabled', false);
  el.find('.file_name').val(fileName);
  el.find('.file_id').val(fileId);
});

$(document).on('click', '.partyUploadsfileDelete', function (e) {
  let type = $(this).data('type');
  let fileId = $(this).data('file-id');
  let el = $(`#${type}FileDeleteModal`);
  el.modal();
  el.find('.deleteFile').attr('disabled', false);
  el.find('.file_id').val(fileId);
});

function createFolder(folderDetails, type, isnew) {
  let updated_date = "{{getDeltaDate(date('Y-m-d'))}}";
  let action = '{{domain_route("company.admin.client.getFolderDetails", ["clientId", "folderId"])}}';
  action = action.replace("clientId", "{{$client->id}}");
  action = action.replace("folderId", folderDetails.id);
  if(type=='files'){ @php $typeP = 'file' @endphp console.log(type);}
  else{ @php $typeP = 'image' @endphp console.log(type);}
  let view = `<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
      <div class="panel">
          <div id="partyUploadsFolder${folderDetails.id}">
              <div class="panel-body" style="padding:0px 50px 0px 50px;">
                  <div class="text-center">
                      <a href='#' data-id="${folderDetails.id}" data-type="${type}" data-folder_name="${folderDetails.name}"
                          data-href="${action}"
                          class="folderTitleClick" onClick="openFiles(this); return false;">
                          <i class="fa fa-folder-open" style="font-size: 100px;color: #ffd500a8;"></i>
                      </a>
                      @if(Auth::user()->can(\Illuminate\Support\Str::singular($typeP).'uploads-update'))
                      <a class="partyUploadsfolderEdit" data-target="${type}FolderCreateModal" data-type="${folderDetails.type}" data-id="${folderDetails.id}" data-folder_name="${folderDetails.name}" style="word-break: break-word;">
                          <i class="fa fa-edit"></i>
                      </a>
                      @endif
                      @if(Auth::user()->can(\Illuminate\Support\Str::singular($typeP).'uploads-delete'))
                        <a class="partyUploadsfolderDelete" data-type="${folderDetails.type}" href="#" data-id="${folderDetails.id}" data-folder_name="${folderDetails.name}" style="word-break: break-word;">
                            <i class="fa fa-trash"></i>
                        </a>
                      @endif
                  </div>
              </div>
          </div>
          <div class="panel-heading" role="tab" id="heading${folderDetails.id}" style="display:flex;align-items: center;justify-content: center;height: 37px !important;    color: #000 !important;background-color: #ffffff !important;">
              <span class="panel-title" id="folderNameField${folderDetails.id}">
              <a href='#' data-id="${folderDetails.id}" data-type="${type}" data-folder_name="${folderDetails.name}" data-href="${action}" class="folderTitleClick" style="word-break: break-word;" onClick="openFiles(this); return false;"><span id="folderNameSpan" class="folderNameSpan">${folderDetails.name}</a>
              </span>
          </div>
          <i><small>Last updated at: ${updated_date} </small></i>
      </div>
  </div>`
  if($(`.${type}foldersView`).children().hasClass('noFolderView')) $('.noFolderView').remove(); 
  if(isnew){ $(`.${type}foldersView`).prepend(
    `<div class="col-xs-3 folderContainer" data-id="${folderDetails.id}">${view}</div>`
  )}
  else{ $(`.${type}foldersView`).find(`[data-id='${folderDetails.id}']`).html(view); }
}

function removeFolder(folderDetails, type, isnew) {
  $(`.${type}foldersView`).find(`[data-id='${folderDetails.id}']`).remove();
}

$(document).on("submit", '.createFolders', function(e){
  e.preventDefault()
  let el = $(this)
  let url = el[0].action
  
  if(folder_name.length>190){
    alert("Long Folder Name")

    return;
  }
  $.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		},
		url: url,
		type: 'POST',
		data: new FormData(this),
		processData: false,
		contentType: false,
		beforeSend: function () {
      el.find('.folderNameErr').html('')
			showLoader()
      el.find('.addFolder').attr('disabled', 'disabled')
		},
		success: function (response) {
			hideLoader();
			el.find('.addFolder').attr('disabled', false)
			el.find('#folder_name').val('')
      alert(response.message)
      if (response.status==200){
        el.parent().parent().modal('hide')
        if(el.find('.type').val() == "files") createFolder(response.data, el.find('.type').val(), true)
        else  $('.imagesFolderTable').DataTable().ajax.reload(); 
      } 
		},
		error(xhr) {
			hideLoader();
			el.find('.addFolder').attr('disabled', false)
			el.find('#folder_name').val('')
			el.find('.folderNameErr').html('')
			$.each(xhr.responseJSON.errors, function(key,value) {
			  el.find('.folderNameErr').append(value)
			});
		},
	})
});

$(document).on("submit", '.editFolders', function (e) {
	e.preventDefault()
	let el = $(this)
	let url = el[0].action

	if (folder_name.length > 190) console.log('Long Folder Name')
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		},
		url: url,
		type: 'POST',
		data: new FormData(this),
		processData: false,
		contentType: false,
		beforeSend: function () {
			el.find('.folderNameErr').html('')
			showLoader();
			el.find('.updateFolder').attr('disabled', 'disabled')
		},
		success: function (response) {
			hideLoader();
			el.find('.updateFolder').attr('disabled', false)
			el.find('#folder_name').val('')
			alert(response.message)
			if (response.status == 200) {
				el.parent().parent().modal('hide')
        if(el.find('.type').val() == "files") createFolder(response.data, el.find('.type').val(), false)
        else  $('.imagesFolderTable').DataTable().ajax.reload();
			}
		},
		error(xhr) {
			hideLoader();
			el.find('.updateFolder').attr('disabled', false)
			el.find('#folder_name').val('')
			el.find('.folderNameErr').html('')
			$.each(xhr.responseJSON.errors, function (key, value) {
				el.find('.folderNameErr').append(value)
			})
		},
	})
});

$(document).on("submit", '.deleteFolders', function (e) {
	e.preventDefault()
	let el = $(this)
	let url = el[0].action
  
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		},
		url: url,
		type: 'POST',
		data: new FormData(this),
		processData: false,
		contentType: false,
		beforeSend: function () {
			showLoader()
			el.find('.deleteFolder').attr('disabled', 'disabled')
		},
		success: function (response) {
			hideLoader();
			el.find('.deleteFolder').attr('disabled', false)
			alert(response.message)
			if (response.status == 200) {
				el.parent().parent().modal('hide')
			  el.find('.folder_id').val('')
        if(el.find('.type').val() == "files") removeFolder(response.data, el.find('.type').val(), false)
        else  $('.imagesFolderTable').DataTable().ajax.reload();
			}
		},
		error(xhr) {
			hideLoader()
			el.find('.deleteFolder').attr('disabled', false)
			$.each(xhr.responseJSON.errors, function (key, value) {
				alert(value)
			})
		},
	})
});

function openFiles(el){
  let folderId = el.dataset.id
  let type = el.dataset.type
  let action = el.dataset.href

  loadView(folderId, type, action, false)
}

function loadFolderView(el){
  let folderId = null
  let type = el.dataset.type
  let action = el.href

  loadView(folderId, type, action, false)
}

function initializeFolderDT(folderId, type, action, firstLoad){
  let columns = [{ "data": "id" },
                { "data": "folder_name" },
                { "data": "updated_at" },
                { "data": "action" }];
  
  $('.imagesFolderTable').DataTable({
    "columnDefs": [
    { "orderable": false, "targets": [-1] }
    ],
    language: { search: "" },
    "order": [[ 0, "desc" ]],
    "serverSide": true,
    "processing": true,
    "paging": true,
    "ajax":{
      "url": action,
      "data": {
        folderId,
        type,
        firstLoad
      },
      "dataType": "json",
      "type": "GET",
      beforeSend:function(url, data){
        showLoader();
      },
      error:function(){
        hideLoader();
      },
      complete:function(data){
        hideLoader();
      },
    },
    "columns": columns,
    drawCallback: function(settings){
      if($('#filePartial').children().find('.dataTables_filter').children().children().after()[1]){
        $('#filePartial').children().find('.dataTables_filter').children().children().after()[1].remove();
      }
      $('#filePartial').children().find('.dataTables_filter').children().children().after("<small style='padding-left: 10px;color: #524b4b;letter-spacing: 0.2px;'> "+ settings.json.total_size + " GB of " + settings.json.total_upload_allowed + " GB used / " + settings.json.percent_used  +"% of total allocated space</small>")
    }
  });
}  

function loadView(folderId, type, action, firstLoad){
  if(type == "images" && folderId == null && firstLoad){
    initializeFolderDT(folderId, type, action, firstLoad)
  }else{
    $.ajax({
      // headers: {
      // 	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      // },
      url: action,
      data: {
        folderId,
        type
      },
      type: 'GET',
      beforeSend: function () {
        showLoader();
      },
      success: function (response) {
        hideLoader();
        if(response.status === 200){
          let type = response.type
          $(`#${type}uploads`).html('');
          $(`#${type}uploads`).html(response.view);
          if(type!="images") initializeDT(`${type}PartialTable`, folderId, type); 
          else renderImages(null, folderId, type);  
          if(type == "images" && folderId == null){
            initializeFolderDT(folderId, type, action, true)
          }
        }
      },
      error(xhr) {
        hideLoader();
        $.each(xhr.responseJSON.errors, function (key, value) {
          alert(value)
        })
      },
    })
  }
} 

initializeFolderDT(null, "images", "{{domain_route('company.admin.client.getFolderView', [$client->id])}}", true)

$('.tab-pane').on("click", ".imgAdd", function(e){
  let type = e.target.dataset.type;
  if(parseInt($(`.${type}Count`).val()) + $(`.${type}uploadForm`).find('.imgUp').length > 20){
    if(type=="images"){
      $(`.${type}uploadForm`).find('.uploadFile').prop('disabled', 'disabled')
    }else{
      $(`.${type}uploadForm`).find('.btn-primary').prop('disabled', 'disabled')
    }
    $(`.${type}uploadForm`).find('.errValue').html('');
    $(`.${type}uploadForm`).find('.errValue').html('<p style="color:red;">Cannot upload more than 20 items</p>');
    return;
  }else{
    if(type=="images"){
      $(`.${type}uploadForm`).find('.uploadFile').prop('disabled', false)
    }else{
      $(`.${type}uploadForm`).find('.btn-primary').prop('disabled', false)
    }
    $(`.${type}uploadForm`).find('.errValue').html('');
  }
  let reqUploads = type == "files" ? "newChosenUpload": "chosenUpload";
  let addRow =  `<div class="col-xs-3 imgUp">
                    <div class="imagePreview" style="height: 150px;width:100%">
                    </div>
                    <label class="btn btn-default">Select File 
                    <input name="${reqUploads}[]" type="file" class="uploaderInput img" value="Upload Photo" data-type="${type}" style="width:0px;height:0px;overflow:hidden;">
                    </label>
                    <i class="fa fa-times del" data-type="${type}"></i>
                  </div>`;
  var imgcount = $(`.${type}uploadForm`).children().children().children().find(".imgUp").length;

  if(imgcount < 4){
    if(imgcount == 3){
        $(`.${type}uploadForm`).find('.modal-body').children().find('.imgAdd').hide();
    }
    $(`.${type}uploadForm`).find('.modal-body').children().find('.imgAdd').before(addRow);          
  }else{
    $(`.${type}uploadForm`).find('.modal-body').children().find('.imgAdd').hide();
  }
});

function reloadIndexes(files){
  $.each(files, function(ind, file){
    var reader = new FileReader(); // instance of the FileReader
    reader.readAsDataURL(files[ind]); // read the local file
    if (/^image/.test( files[ind].type)){ // only image file
      reader.onloadend = function(){ // set image data as background of div
        // uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url("+this.result+")");
        renderImageDiv(this.result, ind, files[ind]);
      }
    }else{
      alert("Only Images allowed");
    }
  });
}

$(document).on("click", "i.del" , function(e) {
  let type = e.target.dataset.type;
  if(type=="images"){
    filesChoosenPrev.splice(e.target.dataset.index , 1 );
    $('.imgUp').remove();
    reloadIndexes(filesChoosenPrev)
  }
  if($(`.${type}uploadForm`).children().children().children().find(".imgUp").length==1){  
    $(this).parent().next().trigger("click");
    e.currentTarget.parentElement.remove();
    return;
  }
  e.currentTarget.parentElement.remove();
  var imgcount = $(`.${type}uploadForm`).children().children().children().find(".imgUp").length;
  if(imgcount<4){
    $(`.${type}uploadForm`).find('.modal-body').children().find('.imgAdd').show();
  }
  if(parseInt($(`.${type}Count`).val()) + $(`.${type}uploadForm`).find('.imgUp').length > 20){
    if(type=="images"){
      $(`.${type}uploadForm`).find('.uploadFile').prop('disabled', 'disabled')
    }else{
      $(`.${type}uploadForm`).find('.btn-primary').prop('disabled', 'disabled')
    }
    $(`.${type}uploadForm`).find('.errValue').html('');
    $(`.${type}uploadForm`).find('.errValue').html('<p style="color:red;">Cannot upload more than 20 items</p>');
  }else{
    if(type=="images"){
      $(`.${type}uploadForm`).find('.uploadFile').prop('disabled', false)
    }else{
      $(`.${type}uploadForm`).find('.btn-primary').prop('disabled', false)
    }
    $(`.${type}uploadForm`).find('.errValue').html('');
  }
  if($('.upErr').length) $(`.${type}uploadForm`).find('.uploadFile').attr('disabled', 'disabled')
  else $(`.${type}uploadForm`).find('.uploadFile').attr('disabled', false)
});
$(document).on("click", "i.editDel" , function(e) {
  let type = e.target.dataset.type;
  // e.currentTarget.parentElement.children[0].removeAttribute("style")
  e.currentTarget.parentElement.remove();
  let addRow =  `<div class="col-xs-3 imgUp">
                    <div class="imagePreview renderImages(null, folderId, type);" style="height: 150px;width:100%"></div>  
                    <label class="btn btn-default">Select File 
                    <input name="chosenUpload" type="file" class="uploaderInput img" value="Upload Photo" data-type="${type}" style="width:0px;height:0px;overflow:hidden;">
                    </label>
                    <i class="fa fa-times editDel" data-type="${type}"></i>
                  </div>`;
  $(`.${type}eduploadForm`).find('.modal-body').find('.form-group:nth-child(2)').html(addRow).fadeIn(); 
  $(`.${type}eduploadForm`).find('.imagePreview').html("<div style='margin: 25% 0;font-size: x-large;width: 100%;color: #fff;font-size: 18px;background: #5d5757;'>"+ $(`.${type}eduploadForm`).find('.modal-body').find('.file_name').val() +"</div>")
});

function renderImageDiv(backgrondResult, index, fileObj = null){
  let type = "images";
  let addRow;
  if(backgrondResult){
    if(fileObj){
      if((fileObj.size / 1024) > imagesAllowedUploadSize || !imagesAllowedUploadTypes.includes(fileObj.type.split("/")[1]) ) 
      {
        imgCaption = `<p style="color:red;">${fileObj.name}</p>`;
        $('.imagesuploadForm').find('.errValue').html('');
        if((fileObj.size / 1024) > imagesAllowedUploadSize) error = `<p class="upErr" style="float: left;color:red;">Image must be less than or equal to ${imagesAllowedUploadSize} Kb. </p>`; 
        else error = `<p class="upErr" style="float: left;color:red;">Image must be one of the mentioned types: ${imagesAllowedUploadTypes.join(',')} </p>`;
      }
      else{
        imgCaption = `<p style="color:green;">${fileObj.name}</p>`;
        error = '';
      } 
    } 
    addRow =  `<div class="col-xs-3 imgUp">
                      <div class="imagePreview" style="height: 150px;width:100%;background-image:url(${backgrondResult}) ">
                      </div>
                      <i class="fa fa-times del" data-type="${type}" data-index="${index}"></i>
                      ${imgCaption} 
                      ${error}
                    </div>`;
  }else{
    addRow =  `<div class="col-xs-3 imgUp">
                      <div class="imagePreview" style="height: 150px;width:100%;">
                      </div>
                      <i class="fa fa-times del" data-type="${type}" data-index="${index}"></i>
                    </div>`;

  }
  var imgcount = $(`.${type}uploadForm`).children().children().children().find(".imgUp").length;

  // if(imgcount < 4){
  //   if(imgcount == 3){
  //       $(`.${type}uploadForm`).find('.modal-body').children().find('.imgAdd').hide();
  //   }
    $(`.${type}uploadForm`).find('.modal-body').children().find('.imgPreview').after(addRow);          
  // }else{
  //   $(`.${type}uploadForm`).find('.modal-body').children().find('.imgAdd').hide();
  // }
  let disabled = false;
  if(parseInt($(`.${type}Count`).val()) + $(`.${type}uploadForm`).find('.imgUp').length > 20){
    if(type=="images"){
      $(`.${type}uploadForm`).find('.uploadFile').prop('disabled', 'disabled')
    }else{
      $(`.${type}uploadForm`).find('.btn-primary').prop('disabled', 'disabled')
    }
    $(`.${type}uploadForm`).find('.errValue').html('');
    $(`.${type}uploadForm`).find('.errValue').html('<p style="color:red;">Cannot upload more than 20 items</p>');
    return;
  }else{
    if(type=="images"){
      $(`.${type}uploadForm`).find('.uploadFile').prop('disabled', false)
    }else{
      $(`.${type}uploadForm`).find('.btn-primary').prop('disabled', false)
    }
    $(`.${type}uploadForm`).find('.errValue').html('');
  }
  if($('.upErr').length) $('.imagesuploadForm').find('.uploadFile').attr('disabled', 'disabled')
  else $('.imagesuploadForm').find('.uploadFile').attr('disabled', false)
  
}

$(function() {
  $(document).on("change",".uploaderInput", function(e){
    $('.imagesuploadForm').find('.imgUp').remove();
    var uploadFile = $(this);
    var files = !!this.files ? this.files : [];
    if(uploadFile[0].dataset.type=="images") {
      if(files.length + filesChoosenPrev.length > 20){
        alert("Cannot upload more than 20 items.")
        files = filesChoosenPrev
      }else{
        filesChoosenPrev.forEach(file => files =  [...files, file]);
      }
      filesChoosenPrev = []
      if(files.length === 0){
        $('.imagesuploadForm')[0].reset();
        // renderImageDiv(null, 0);
      }
    }else if(files.length > 20 && uploadFile[0].dataset.type=="files"){
      alert("Cannot upload more than 20 items.")
      return;
    }
    if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
    $.each(files, function(ind, file){
      filesChoosenPrev.push(file);
      var reader = new FileReader(); // instance of the FileReader
      reader.readAsDataURL(files[ind]); // read the local file
      if(uploadFile[0].dataset.type=="images"){
        if (/^image/.test( files[ind].type)){ // only image file
          reader.onloadend = function(){ // set image data as background of div
            // uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url("+this.result+")");
            renderImageDiv(this.result, ind, files[ind]);
          }
        }else{
          alert("Only Images allowed");
        }
      }
      else if(uploadFile[0].dataset.type=="files"){
        // if (/^application/.test( files[ind].type) || /^text/.test( files[ind].type)){ // only image file
          reader.onloadend = function(){ // set image data as background of div
            let divEl = "<div style='margin: 25% 0;font-size: x-large;width: 100%;color: #fff;font-size: 18px;background: #5d5757;'>"+ files[ind].name +"</div>";
            uploadFile.closest(".imgUp").find('.imagePreview').html(divEl);
            if((files[ind].size / 1024) > filesAllowedUploadSize || !filesAllowedUploadTypes.includes(files[ind].type.split("/")[1]) ) 
            {
              if((files[ind].size / 1024) > filesAllowedUploadSize){
                error = `<p class="upErr" style="float: left;color:red;">File must be less than or equal to ${filesAllowedUploadSize} Kb. </p>`; 
              } 
              else{
                if(files[ind].type == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"){ 
                  error = "";
                }else if(files[ind].type == "application/vnd.oasis.opendocument.text"){
                  error = "";
                }else if(files[ind].type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                  error = "";
                }else if(files[ind].type == "text/plain"){
                  error = "";
                }else{
                  error = `<p class="upErr" style="float: left;color:red;">File must be one of the mentioned types: ${filesAllowedUploadTypes.join(',')} </p>`;
                }
              } 
            }
            else{
              error = '';
            } 
            uploadFile.closest(".imgUp").find('.upErr').remove();
            uploadFile.closest(".imgUp").append(error);
            if($('.upErr').length) $('.filesuploadForm').find('.uploadFile').attr('disabled', 'disabled')
            else $('.filesuploadForm').find('.uploadFile').attr('disabled', false)
          }
        // }else{
        //   alert("Only Documents allowed");
        // }
      }
    });
      
  });
});

$(document).on("submit", '.createFiles', function (e) {
	e.preventDefault()
	let el = $(this)
  let url = el[0].action
  let type = el.find('.type').val()
  let folderId = el.find('.folder_id').val()
  let formData = new FormData(this);
  if(type == "images"){
    for (var i = 0, len = filesChoosenPrev.length; i < len; i++) {
      formData.append(`newChosenUpload[${i}]`, filesChoosenPrev[i]);
    }
  }
	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		},
		url: url,
		type: 'POST',
		data: formData,
		cache:false,
    contentType: false,
    processData: false,
		beforeSend: function () {
			el.find('.errValue').html('')
			showModalLoader();
			el.find('.uploadFile').attr('disabled', 'disabled')
		},
		success: function (response) {
			hideModalLoader();
			el.find('.uploadFile').attr('disabled', false)
			if (response.status == 200) {
        el.find('.imgUp').remove();
        if(type=="images"){
          // filesChoosenPrev = [];
          $('.img-form-group').append(`<div class="col-xs-3 imgUp" style="width: max-content;">
            <label class="btn btn-default"> Select File 
              <input id="chosenUpload" type="file" name="chosenUpload[]" class="uploaderInput img" data-type="${type}" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;" multiple required>
            </label>
          </div>`);
        }
        if(type!="images"){
          $(`.${type}uploadForm`).find('.modal-body').children().find('.imgAdd').show();
          $(`.${type}PartialTable`).DataTable().ajax.reload(); 

        } 
        else renderImages(response.data, folderId, type);  
        $(`.${type}Count`).val(response.objectCount);
        if($(`.${type}Count`).val() == 20){
          $(`.${type}Link`).addClass("hidden");
          $(`.${type}maxLimit`).removeClass("hidden");
        }else{
          $(`.${type}Link`).removeClass("hidden");
          $(`.${type}maxLimit`).addClass("hidden");
        }
        alert(response.message);
        el.parent().parent().modal('hide');
			}
      filesChoosenPrev = [];
		},
		error(xhr) {
			hideModalLoader();
			el.find('.uploadFile').attr('disabled', false)
			el.find('.errValue').html('')
			$.each(xhr.responseJSON.errors, function (key, value) {
				el.find('.errValue').append(`<p style="float: left;">${value}</p><br/>`);
			})
		},
	})
});

$(document).on("submit", '.editFiles', function (e) {
	e.preventDefault()
	let el = $(this)
  let fileId = el.find('.file_id').val()
  let type = el.find('.type').val()
	let action = "{{domain_route('company.admin.client.updateFile', ['clientId', 'fileId'])}}"
  action = action.replace("clientId", "{{$client->id}}");
  action = action.replace("fileId", fileId);

	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		},
		url: action,
		type: 'POST',
		data: new FormData(this),
		processData: false,
		contentType: false,
		beforeSend: function () {
			el.find('.fileNameErr').html('')
			showModalLoader();
			el.find('.updateFile').attr('disabled', 'disabled')
		},
		success: function (response) {
			hideModalLoader();
			el.find('.updateFile').attr('disabled', false)
			el.find('.file_name').val('')
      el.find('.imgUp').find('.editDel').click()
			if (response.status == 200) {
        $(`.${type}PartialTable`).DataTable().ajax.reload(); 
        $(`.${type}Count`).val(response.objectCount);
        if($(`.${type}Count`).val() == 20){
          $(`.${type}Link`).addClass("hidden");
          $(`.${type}maxLimit`).removeClass("hidden");
        }else{
          $(`.${type}Link`).removeClass("hidden");
          $(`.${type}maxLimit`).addClass("hidden");
        }
        alert(response.message)
				el.parent().parent().modal('hide')
			}
      filesChoosenPrev = [];
		},
		error(xhr) {
			hideModalLoader();
			el.find('.updateFile').attr('disabled', false)
			el.find('.file_name').val('')
			el.find('.fileNameErr').html('')
			$.each(xhr.responseJSON.errors, function (key, value) {
				el.find('.fileNameErr').append(value)
			})
		},
	})
});

$(document).on("submit", '.deleteFile', function (e) {
	e.preventDefault()
	let el = $(this)
  let fileId = el.find('.file_id').val()
  let type = el.find('.type').val()
	let action = "{{domain_route('company.admin.client.deleteFile', ['clientId', 'fileId'])}}"
  action = action.replace("clientId", "{{$client->id}}");
  action = action.replace("fileId", fileId);

	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		},
		url: action,
		type: 'POST',
		data: new FormData(this),
		processData: false,
		contentType: false,
		beforeSend: function () {
			showModalLoader();
			el.find('.deleteFile').attr('disabled', 'disabled')
		},
		success: function (response) {
			hideModalLoader();
			el.find('.deleteFile').attr('disabled', false)
			el.find('.file_id').val('')
			if (response.status == 200) {
        if(type!="images"){
          $(`.${type}PartialTable`).DataTable().ajax.reload(); 
        } 
        else{
          $(".imagesView").find(`[data-key='${response.data.id}']`).remove()
          $(".custommodal").find(".actionButtons").html("");
          $(".imagesView").html('');
          renderImages(null, response.folderId, type);
        };
        $(`.${type}Count`).val(response.objectCount);
        if($(`.${type}Count`).val() == 20){
          $(`.${type}Link`).addClass("hidden");
          $(`.${type}maxLimit`).removeClass("hidden");
        }else{
          $(`.${type}Link`).removeClass("hidden");
          $(`.${type}maxLimit`).addClass("hidden");
        }
        alert(response.message)
        el.parent().parent().modal('hide')
        $('#myModal').find('.close').click()
			}
		},
		error(xhr) {
			hideModalLoader();
			el.find('.deleteFile').attr('disabled', false)
			el.find('.file_id').val('')
			$.each(xhr.responseJSON.errors, function (key, value) {
				alert(value)
			})
		},
	})
});

$(document).on('keyup', '.folderSearchBar', function(){
  let type = $(this).data("type");
  let searchInput = $(this).val().toLowerCase();
  $(`.${type}foldersView`).find('.folderContainer').each(function(){
    let folderName = $(this).find('#folderNameSpan').text().toLowerCase();
    if(folderName.includes(searchInput)){
      $(this).removeClass("hidden");
    }else{
      $(this).addClass("hidden");
    }

    if(searchInput == ""){
      if($(this).hasClass("hidden")){
        $(this).removeClass("hidden")
      }
    }
  });
});
$(document).on('show.bs.modal', '#filesFileCreateModal', function (e) {
	$('.filesuploadForm')[0].reset();
  $('.filesuploadForm').find('.imgUp').remove();
  $('.filesuploadForm').find('.errValue').html('');
  $('#filesFileCreateModal').find('.uploadFile').prop('disabled', false)

});
$(document).on("hidden.bs.modal", "#filesFileCreateModal", function(){
  filesChoosenPrev = [];
  $('.filesuploadForm')[0].reset();
  $('.filesuploadForm').find('.imgUp').remove();
  $('.filesuploadForm').find('.errValue').html('');
  // $('.filesuploadForm').find('.imgUp').remove();
})
$(document).on('show.bs.modal', '#imagesFileCreateModal', function (e) {
	$('.imagesuploadForm')[0].reset();
  $('.imagesuploadForm').find('.imgUp').remove();
  $('.imagesuploadForm').find('.errValue').html('');
  $('#imagesFileCreateModal').find('.uploadFile').prop('disabled', false)

});
$(document).on("hidden.bs.modal", "#imagesFileCreateModal", function(){
  filesChoosenPrev = [];
  $('.imagesuploadForm')[0].reset();
  $('.imagesuploadForm').find('.imgUp').remove();
  $('.imagesuploadForm').find('.errValue').html('');
  // $('.imagesuploadForm').find('.imgUp').remove();
  renderImageDiv(null, 0);
})

$(document).on("hidden.bs.modal", ".fileEditModal", function(){
  $('.editFiles').find('.editDel').trigger("click")

})

// var gallery = document.querySelector('#gallery');
// var getVal = function (elem, style) { return parseInt(window.getComputedStyle(elem).getPropertyValue(style)); };
// var getHeight = function (item) { return item.querySelector('.content').getBoundingClientRect().height; };
// var resizeAll = function () {
//     var altura = getVal(gallery, 'grid-auto-rows');
//     var gap = getVal(gallery, 'grid-row-gap');
//     gallery.querySelectorAll('.gallery-item').forEach(function (item) {
//         var el = item;
//         el.style.gridRowEnd = "span " + Math.ceil((getHeight(item) + gap) / (altura + gap));
//     });
// };
// gallery.querySelectorAll('img').forEach(function (item) {
//     item.classList.add('byebye');
//     if (item.complete) {
//         console.log(item.src);
//     }
//     else {
//         item.addEventListener('load', function () {
//             var altura = getVal(gallery, 'grid-auto-rows');
//             var gap = getVal(gallery, 'grid-row-gap');
//             var gitem = item.parentElement.parentElement;
//             gitem.style.gridRowEnd = "span " + Math.ceil((getHeight(gitem) + gap) / (altura + gap));
//             item.classList.remove('byebye');
//         });
//     }
// });
// window.addEventListener('resize', resizeAll);
// gallery.querySelectorAll('.gallery-item').forEach(function (item) {
//     item.addEventListener('click', function () {        
//         item.classList.toggle('full');        
//     });
// });
window.addEventListener('keyup', function(e){
  if($('.custommodal').is(':visible')) {
    var key = e.keyCode;
    if (key == 37) {  
      $('.custommodal').find('.previous').click()
    }else if(key == 39){
      $('.custommodal').find('.next').click()
    }
  }
});
</script>
