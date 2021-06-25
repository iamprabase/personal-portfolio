<div class="col-xs-12">
  <h3 class="site-tital">Leave Type List</h3>
</div>
<div class="info">
  <div class="col-xs-6">
    <div class="box">
      
      <div class="box-header">
      </div>
      <div class="box-body">
        
        <table id="tblleavetype" class="table table-bordered table-striped">
          
          <thead>
          
          <tr>
            
            <th>#</th>
            
            <th>Name</th>
            
            <th style="min-width: 65px;">Action</th>
          
          </tr>
          
          </thead>
          
          <tbody id="tbl_leavetypes">
          
          @php($i = 0)
          
          @foreach($leavetypes as $leavetype)
            @php($i++)
            <tr>
              
              <td>{{ $i }}</td>
              
              <td>{{ $leavetype->name}}</td>
              
              <td>
                <a class="btn btn-primary btn-sm edit-leavetype" data-id="{{ $leavetype->id }}" data-name="{{$leavetype->name}}"
                   data-url="{{ domain_route('company.admin.leavetype.update', [$leavetype->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
                      class="fa fa-edit"></i></a>                


                  @if($leavetype->leaves->count()==0)
                  <a class="btn btn-danger btn-sm delete-leavetype" data-id="{{ $leavetype->id }}"
                   data-url="{{ domain_route('company.admin.leavetype.destroy', [$leavetype->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
                      class="fa fa-trash-o"></i></a>         
                  @endif     
              
              </td>
            
            </tr>
          
          @endforeach
          
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Create Leave Type</h3>
        <span id="userexports" class="pull-right"></span>
      </div>
      <div class="box-body">
        {!! Form::open(array('url' => url(domain_route("company.admin.leavetype.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true,'id'=>'addNewleaveType')) !!}
        <div class="form-group @if ($errors->has('name')) has-error @endif">
          
          {!! Form::label('name', 'Name') !!}<span style="color: red">*</span>
          
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Leave Type Name', 'required']) !!}
          
          @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
        </div>
        {!! Form::submit('Add LeaveType', ['class' => 'btn btn-primary pull-right addNewleaveType']) !!}
        
        {!! Form::close() !!}
      </div>
    </div>
    <div class="modal modal-default fade" id="modalDeleteLeaveType" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog small-modal" unit="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <form id="frmDelLeaveType" method="post" class="remove-record-model">
            {{csrf_field()}}
            <div class="modal-body">
              <p class="text-center">
                Are you sure you want to delete this?
              </p>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
              <button type="submit" class="btn btn-warning removeLeaveTypeKey">Yes, Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal modal-default fade" id="editLeaveType" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog small-modal" unit="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center" id="myModalLabel">Update LeaveType</h4>
          </div>
          <form id="formEditLeavetype" method="post" class="edit-record-model">
            {{csrf_field()}}
            <div class="modal-body">
              <input id="editleavetypename" type="text" name="name" class="form-control">              
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
              <button type="submit" class="btn btn-primary primary-button updateLeaveType">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>