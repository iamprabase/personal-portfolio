@extends('layouts.company')

@section('title', 'Settings')

@section('stylesheets')

<link rel="stylesheet" href="{{asset('assets/dist/css/settings.css') }}">

@endsection

@section('content')
  <section class="content">
    <div class="row">
    	@if (session()->has('active'))
        <?php $active = session()->get('active'); ?>
      @else
        <?php $active = 'profile' ?>
      @endif
      @include('company.settingsnew.settingheader')
    </div>
    <div class="row">
      <div class="bs-example bs-example-tabs" id="collaterals-detail" data-example-id="togglable-tabs" style="margin-top:20px;">
        
    		<div id="loader1" hidden>
    <img src="{{asset('assets/dist/img/loader2.gif')}}" />
</div>
<div class="box-loaderClass"> 
    <div class="col-xs-12 site-tital">
        <div class="col-xs-4">
            <h3>Collaterals</h3>
        </div>
        {{-- <div class="col-xs-4">
          <div class="searchBar">
            <i id="iconSearch" class="fa fa-search"></i>
            <input type="text" placeholder="Search" id="folderSearchBar" class="folderSearchBar">
          </div>
        </div> --}}
        <div class="col-xs-4 pull-right" style="margin-top:25px;">
            <span>
                <a href="#" data-toggle="modal" data-target="#myFileCreateModal">
                <i class="fa fa-plus"></i>
                    <strong>Create New Folder</strong>
                </a>
            </span>
        </div>
    </div>
    <div class="info col-xs-12" style="max-height: 550px;overflow-y: scroll;">
        <div class="box-header collateralSearchBar">
          {{-- <div class="col-xs-4"> --}}
            <div class="searchBar">
              <i id="iconSearch" class="fa fa-search"></i>
              <input type="text" placeholder="Search" id="folderSearchBar" class="folderSearchBar">
            </div>
          {{-- </div> --}}
        </div>
        <div class="box-body">
            <div id= "collateralsPartial">
                @include('company.settingsnew.collateralsPartial')
            </div>
        </div>
    </div>  
  </div>
  <!-- Modal -->
  <div class="modal fade" id="myFileCreateModal" role="dialog">
      <div class="modal-dialog modal-sm small-modal">
          {!! Form::open(array('url' => url(domain_route("company.admin.settingnew.createCollateralsFolder", ["domain" =>
          request("subdomain")])), 'method' => 'post', 'files'=> true,'id'=>'createFolders', 'class'=>'createFolders')) !!}
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">Create New Folder</h4>
                  </div>
                  <div class="modal-body">
                      <div class="form-group @if ($errors->has('name')) has-error @endif">
                              
                          {!! Form::label('folder_name', 'Folder Name') !!}<span style="color: red">*</span>
                          
                          {!! Form::text('folderName', null, ['class' => 'form-control', 'placeholder' => 'Folder Name',
                          'required']) !!}
                      
                          <p class="help-block has-error">
                              <span class="folderNameErr"></span>
                          </p>
                      </div>
                  </div>
                  <div class="modal-footer">
                      {!! Form::submit('Add Folder', ['class' => 'btn btn-primary pull-right addFolder','id'=>"addFolder"]) !!}
                      
                  </div>
              </div>
          {!! Form::close() !!}
      </div>
  </div>

  <div class="modal fade" id="editMyModal" role="dialog">
      <div class="modal-dialog modal-sm small-modal">
          {!! Form::open(array('url' => url(domain_route("company.admin.settingnew.editCollateralsFolder")), 'method' => 'post', 'id'=>'editFolders', 'class'=>'editFolders'))
          !!}
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Edit Folder Name</h4>
              </div>
              <div class="modal-body">
                  <div class="form-group">
  
                      {!! Form::label('edit_folder_name', 'Folder Name') !!}<span style="color: red">*</span>
  
                      {!! Form::text('editFolderName', null, ['class' => 'form-control','id'=>'editFolderName', 'placeholder' => 'Folder Name','required']) !!}

                      {!! Form::hidden('editFolderId', null, ['class' => 'form-control','id'=>'editFolderId', 'placeholder' => 'Folder ID', 'required']) !!}
  
                      <p class="help-block has-error">
                          <span class="editfolderNameErr"></span>
                      </p>
                  </div>
              </div>
              <div class="modal-footer">
                  {!! Form::submit('Update Folder Name', ['class' => 'btn btn-primary pull-right editFolder','id'=>"editFolder"])
                  !!}
  
              </div>
          </div>
          {!! Form::close() !!}
      </div>
  </div>
  <div class="modal modal-default fade" id="deleteMyModal" tabindex="-1" unit="dialog"
      aria-labelledby="myModalLabel">
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
                      Are you sure you want to delete the folder? Deleting the folder will permanently delete all the files inside it?
                  </p>
              </div>
              <div class="modal-footer">
                  {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
                  <button id="deleteFolder" type="submit" class="btn btn-warning">Yes, Delete</button>
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
    $('document').ready(function(){
        $(".createFolders").on("submit",function(e){
            e.preventDefault();
            var element = $(this)[0];
            var url = element.action;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                beforeSend:function(){
                    showLoader();
                    $('#addFolder').attr('disabled','disabled');
                },
                success: function (data) {
                    hideLoader();
                    $('#addFolder').attr('disabled',false);
                    element.reset();
                    alert('Folder has been created');
                    $('#collateralsPartial').html('');
                    $('#collateralsPartial').html(data);
                    $('.keepcollapse').click();
                    $('#myFileCreateModal').modal('hide');
                },
                error(xhr){
                  hideLoader();
                  $('#addFolder').attr('disabled',false);
                  element.reset();
                  $('.folderNameErr').html('');
                  $.each(xhr.responseJSON.errors, function(key,value) {
                    $('.folderNameErr').append(value);
                  });
                }
            });
        });
        $(".editFolders").on("submit",function(e){
            e.preventDefault();
            var element = $(this)[0];
            var url = element.action;
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                beforeSend:function(){
                    showLoader();
                    $('#editFolder').attr('disabled','disabled');
                },
                success: function (data) {
                    hideLoader();
                    $('#editFolder').attr('disabled',false);
                    element.reset();
                    alert('Folder Name has been updated');
                    $('#collateralsPartial').html('');
                    $('#collateralsPartial').html(data);
                    $('#editMyModal').modal('hide');
                },
                error(xhr){
                    hideLoader();
                    $('#editFolder').attr('disabled',false);
                    $('.editfolderNameErr').html('');
                    $.each(xhr.responseJSON.errors, function(key,value) {
                        $('.editfolderNameErr').append(value);
                    });
                }
            });
        });
    });
  $('#myFileCreateModal').on('show.bs.modal', function () {
    $('.folderNameErr').html('');
    $(".createFolders")[0].reset();
  });

  function loadFileView(identifier){
    event.preventDefault();
    let folderID = $(identifier).data('id');
    let folderName = $(identifier).data('folder_name');
    let url = $(identifier)[0].href;

    $.ajax({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: url,
      type: "POST",
      // dataType: 'text',
      // processData: false,
      // contentType: false,
      // cache: false,
      data: {
      '_token': '{{csrf_token()}}',
      'folder_id': folderID,
      'folder_name':folderName
      },
      beforeSend:function(){
        showLoader();
        },
      success:function(data){
          hideLoader();
        // $('#collaterals-detail').html('');
        $('#collaterals-detail').html(data);
      }
    });
  }

  function editFolderName(identifier){
    event.preventDefault();
    let folderID = $(identifier).data('id');
    let folderName = $(identifier).data('folder_name');
    $("#editMyModal").modal();
    $('.editfolderNameErr').html('');
    $(".editFolders")[0].reset();
    $("#editFolderName").val(folderName);
    $("#editFolderId").val(folderID);
  }

    $('#editMyModal').on('show.bs.modal', function () {
        $('.editfolderNameErr').html('');
        $(".editFolders")[0].reset();
    });

    function deleteFolderName(identifier){
        event.preventDefault();
        let url = $(identifier)[0].href;
        let folderId = $(identifier).data('id');
        $('#deleteMyModal').modal();
        event.preventDefault();
        $("#deleteFolder").click(function (e) {
            e.preventDefault();
            $.ajax({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            data: {
            "folderId": folderId
            },
            type: "POST",
            beforeSend:function(data, url){
                showLoader();
                $("#deleteFolder").attr('disabled', true);
            },
            success: function (data) {
                hideLoader();
                alert('Folder has been deleted.');
                $('#collateralsPartial').html('');
                $('#collateralsPartial').html(data);
                $("#deleteFolder").attr('disabled', false);
                $('#deleteMyModal').modal('hide');
            },
            error: function (xhr) {
                hideLoader();
                $("#deleteFolder").attr('disabled', false);
                $.each(xhr.responseJSON.errors, function(key,value) {
                    alert(value);
                });
            }
            });
        });
    }

    $('#folderSearchBar').keyup(function(){
      let searchInput = $(this).val().toLowerCase();
      $('.folderContainer').each(function(){
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
      // debugger;
    });
</script>
      </div>
    </div>
  </section>
@endsection

@section('scripts')

@endsection