<div class="col-xs-12">
  <div class="box-header">
    <h3 class="box-title">Business Type List</h3>
  </div>
</div>
<div class="info">
  <div class="col-xs-6">
    <div class="">
      
      <div class="box-header">
      </div>
      <div class="box-body">
        
        <table id="tblbusiness_type" class="table table-bordered table-striped">
          
          <thead>
          
          <tr>
            
            <th>#</th>
            
            <th>Name</th>
            
            <th style="min-width: 65px;">Action</th>
          
          </tr>
          
          </thead>
          
          <tbody id="tbl_business_types">
          
          @php($i = 0)
          
          @foreach($business_types as $business_type)
            @php($i++)
            <tr>
              
              <td>{{ $i }}</td>
              
              <td>{{ $business_type->business_name}}</td>
              
              <td>
                <a class="btn btn-primary btn-sm edit-business_type" data-id="{{ $business_type->id }}" data-name="{{$business_type->business_name}}"
                   data-url="{{ domain_route('company.admin.business_type.update', [$business_type->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
                      class="fa fa-edit"></i></a>                

                  @if($business_type->clients->count()==0)
                  <a class="btn btn-danger btn-sm delete-business_type" data-id="{{ $business_type->id }}"
                   data-url="{{ domain_route('company.admin.business_type.destroy', [$business_type->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
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
    <div class="box-body">
      <div class="box-header">
        <h3 class="box-title">Create Business Type</h3>
        <span id="userexports" class="pull-right"></span>
      </div>
      <div class="box-bodys">
        {!! Form::open(array('url' => url(domain_route("company.admin.business_type.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true,'id'=>'addNewbusiness_type')) !!}
        <div class="form-group @if ($errors->has('name')) has-error @endif">
          
          {!! Form::label('name', 'Name') !!}<span style="color: red">*</span>
          
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Business Type Name','id'=>'businessname', 'required']) !!}
          
          @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
        </div>
        {!! Form::submit('Add Business Type', ['class' => 'btn btn-primary pull-right addNewbusiness_type']) !!}
        
        {!! Form::close() !!}
      </div>
    </div>
    <div class="modal modal-default fade" id="modalDeletebusiness_type" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog small-modal" unit="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <form id="frmDelbusiness_type" method="post" class="remove-record-model">
            {{csrf_field()}}
            <div class="modal-body">
              <p class="text-center">
                Are you sure you want to delete this?
              </p>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
              <button type="submit" class="btn btn-warning removebusiness_typeKey">Yes, Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal modal-default fade" id="editbusiness_type" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog small-modal" unit="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center" id="myModalLabel">Update Business Type</h4>
          </div>
          <form id="formEditbusiness_type" method="post" class="edit-record-model">
            {{csrf_field()}}
            <div class="modal-body">
              <input id="editbusiness_typename" type="text" name="name" class="form-control">              
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
              <button type="submit" class="btn btn-primary primary-button updatebusiness_type">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>