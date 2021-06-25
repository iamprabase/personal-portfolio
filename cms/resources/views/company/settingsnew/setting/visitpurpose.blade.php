<div class="col-xs-12">
  <div class="box-header">
    <h3 class="box-title">Visit Purpose List</h3>
  </div>
</div>
<div class="info">
  <div class="col-xs-6">
    <div class="">
      
      <div class="box-header">
      </div>

      <div class="box-body">  
        <table id="tbl_visitpurpose" class="table table-bordered table-striped">
          <thead>
          <tr>
            <th>S.No.</th>
            <th>Title</th>
            <th style="min-width: 65px;">Action</th>
          </tr>
          </thead>

          <tbody id="tbody_visitpurpose">
            @include('company.settingsnew.setting.ajaxVisitPurpose', $visit_purposes)
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-xs-6">
    <div class="box-body">
      
      <div class="box-header">
        <h3 class="box-title">Create Visit Purpose Type</h3>
        <span id="userexports" class="pull-right"></span>
      </div>

      <div class="box-body">
        {!! Form::open(array('url' => url(domain_route("company.admin.visitpurpose.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> false,'id'=>'addNewVisitPurpose')) !!}
        <div class="form-group @if ($errors->has('title')) has-error @endif">
          {!! Form::label('title', 'Visit Purpose') !!}<span style="color: red">*</span>
          {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Visit Purpose', 'required']) !!}
          <p class="help-block has-error visit-purpose-title-error"></p>
        </div>
        {!! Form::submit('Add Visit Purpose', ['class' => 'btn btn-primary pull-right addNewVisitPurposeBtn']) !!}
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
<div class="modal modal-default fade" id="modalDeleteVisitPurpose" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog small-modal" unit="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
      </div>
      <form id="frmDelVisitPurpose" method="post" class="remove-record-model">
        {{csrf_field()}}
        <div class="modal-body">
          <p class="text-center">
            Are you sure you want to delete this?
          </p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning removeVisitPurposeBtn">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="editVisitPurpose" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog small-modal" unit="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center" id="myModalLabel">Update Visit Purpose</h4>
      </div>
      <form id="formEditVisitPurpose" method="post" class="edit-record-model">
        {{csrf_field()}}
        <div class="modal-body">
          <input id="editVisitPurposeTitle" type="text" name="title" class="form-control" placeholder="Visit Purpose">    
          <p class="help-block has-error edit-visit-purpose-title-error"></p>
          
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary primary-button updateVisitPurposeBtn">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>