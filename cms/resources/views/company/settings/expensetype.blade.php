<div class="col-xs-12">
  <h3 class="site-tital">Expense Category List</h3>
</div>
<div class="info">
  <div class="col-xs-6">
    <div class="box">
      
      <div class="box-header">
      </div>
      <div class="box-body">
        
        <table id="tblexpense_type" class="table table-bordered table-striped">
          
          <thead>
          
          <tr>
            
            <th>#</th>
            
            <th>Name</th>
            
            <th style="min-width: 65px;">Action</th>
          
          </tr>
          
          </thead>
          
          <tbody id="tbl_expense_types">
          
          @php($i = 0)
          
          @foreach($expense_types as $expense_type)
            @php($i++)
            <tr>
              
              <td>{{ $i }}</td>
              
              <td>{{ $expense_type->expensetype_name}}</td>
              
              <td>
                <a class="btn btn-primary btn-sm edit-expense_type" data-id="{{ $expense_type->id }}" data-name="{{$expense_type->expensetype_name}}"
                   data-url="{{ domain_route('company.admin.expense_type.update', [$expense_type->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
                      class="fa fa-edit"></i></a>                

                  @if($expense_type->expenses->count()==0)
                  <a class="btn btn-danger btn-sm delete-expense_type" data-id="{{ $expense_type->id }}"
                   data-url="{{ domain_route('company.admin.expense_type.destroy', [$expense_type->id]) }}" style="padding: 3px 6px; height: auto !important;"><i
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
        <h3 class="box-title">Create Expense Category</h3>
        <span id="userexports" class="pull-right"></span>
      </div>
      <div class="box-body">
        {!! Form::open(array('url' => url(domain_route("company.admin.expense_type.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true,'id'=>'addNewexpense_type')) !!}
        <div class="form-group @if ($errors->has('name')) has-error @endif">
          
          {!! Form::label('name', 'Name') !!}<span style="color: red">*</span>
          
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Expense Category Name', 'required']) !!}
          
          @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
        </div>
        {!! Form::submit('Add Expense Category', ['class' => 'btn btn-primary pull-right addNewexpense_type']) !!}
        
        {!! Form::close() !!}
      </div>
    </div>
    <div class="modal modal-default fade" id="modalDeleteexpense_type" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog small-modal" unit="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
          </div>
          <form id="frmDelexpense_type" method="post" class="remove-record-model">
            {{csrf_field()}}
            <div class="modal-body">
              <p class="text-center">
                Are you sure you want to delete this?
              </p>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
              <button type="submit" class="btn btn-warning removeexpense_typeKey">Yes, Delete</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal modal-default fade" id="editexpense_type" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog small-modal" unit="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                  aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center" id="myModalLabel">Update Expense Type</h4>
          </div>
          <form id="formEditexpense_type" method="post" class="edit-record-model">
            {{csrf_field()}}
            <div class="modal-body">
              <input id="editexpense_typename" type="text" name="name" class="form-control">              
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
              <button type="submit" class="btn btn-primary primary-button updateexpense_type">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>