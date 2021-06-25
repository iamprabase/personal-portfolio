<div class="col-xs-12">
  <h3 class="site-tital">Designation List</h3>
</div>
<div class="info">
  <div class="col-xs-6">
    <div class="box">
	    <div class="box-header">
	    </div>
	    <div class="box-body">

	      <table id="tbldesignation" class="table table-bordered table-striped">

	        <thead>

	        <tr>

	          <th>id</th>

	          <th>Name</th>

	          <th>Parent</th>

	          <th style="min-width: 65px;">Action</th>

	        </tr>

	        </thead>

	        <tbody id="tbody_designation">

	        	<?php $i=1; ?>
	          @foreach($designations as $designation)
		        <tr>

		          <td>{{ $i }}</td>

		          <td>{{ $designation->name}}</td>
		          @if($designation->parent_id==0)
		          	<td>--</td>
		          @else
		          	<td>{{ $designation->parent->name}}</td>
		          @endif

		          <td>
			          <a class="btn btn-primary btn-sm editBtnDesignation" data-id="{{ $designation->id }}" data-url="{{ domain_route('company.admin.designation.update', [$designation->id]) }}" data-name="{{$designation->name}}" style="padding: 3px 6px; height: auto !important;"><i class="fa fa-edit"></i></a>

			          @if($designation->employees->count()==0 && $designation->parent_id!=0)

			          <a class="btn btn-danger btn-sm deleteBtnDesignation" data-id="{{ $designation->id }}" data-url="{{ domain_route('company.admin.designation.destroy', [$designation->id]) }}" style="padding: 3px 6px; height: auto !important;"><i class="fa fa-trash-o"></i></a>		       
			          @endif
		          </td>

		        </tr>
		        <?php $i++; ?>
	        @endforeach

	        </tbody>
	      </table>
	    </div>
	  </div>
	</div>
	<div class="col-xs-6">
	  <div class="box">
	    <div class="box-header">
	      <h3 class="box-title">Create Designation</h3>
	      <span id="userexports" class="pull-right"></span>
	    </div>
	    <div class="box-body">
	      {!! Form::open(array('url' => url(domain_route("company.admin.designation.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true,'id'=>'AddNewDesignation')) !!}
	      <div class="form-group @if ($errors->has('name')) has-error @endif">

	          {!! Form::label('name', 'Name') !!}<span style="color: red">*</span>

	          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Designation Name', 'required']) !!}

	          @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
	      </div>

	      <div class="form-group @if ($errors->has('superior')) has-error @endif">

	          {!! Form::label('name', 'Superior') !!}<span style="color: red">*</span>

	          <select name="superior" class="form-control select2" id="ajaxDesignationlist">
	          	@foreach($designations as $designation)
	          		<option value="{{$designation->id}}">{{$designation->name}}</option>
	          	@endforeach	          	
	          </select>

	          @if ($errors->has('superior')) <p class="help-block has-error">{{ $errors->first('superior') }}</p> @endif
	      </div>

	      {!! Form::submit('Add Designation', ['class' => 'btn btn-primary pull-right addDesignation']) !!}

        {!! Form::close() !!}
	    </div>
	  </div>
	  <div class="modal modal-default fade" id="deleteDesignation" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog small-modal" unit="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
			      </div>
	      		<form id="frmRemoveDesignation" method="post" class="remove-record-modelDesignation">
	          	{{csrf_field()}}
	        		<div class="modal-body">
	        			<p class="text-center">
	          		Are you sure you want to delete this?
	        			</p>
	        		</div>
			        <div class="modal-footer">
			          {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
			          <button type="submit" class="btn btn-warning removeDesignationKey">Yes, Delete</button>
			        </div>
	      		</form>
	    		</div>
	  		</div>
		</div>      
		<div class="modal modal-default fade" id="editDesignation" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog small-modal" unit="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title text-center" id="myModalLabel">Update Designation</h4>
			      </div>
		      		<form id="frmEditDesignation" method="post" class="remove-record-modelDesignation">
		        		<div class="modal-body">
		        			<div class="row">
		        				<div class="col-xs-12">
		        					<input type="text" class="form-control" name="name" id="designation_name" />
		        				</div>
		        			</div>
		        		</div>
				        <div class="modal-footer">
				          {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
				          <button type="submit" class="btn btn-warning editDesignationKey">Yes, Update</button>
				        </div>
		      		</form>
	    		</div>
	  		</div>
		</div>     
  </div>
</div>