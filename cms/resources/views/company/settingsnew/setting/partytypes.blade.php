<div class="">
  <div class="box-header">
    <h3 class="box-title">Party Types</h3>
  </div>
  <!-- /.box-header -->
  <div class="" id="mainBox">
    <div class="row">
      <div class="col-xs-6">
        <ul id="tree1">
        @foreach($partytypes as $partytype)
          <li>
            {{ $partytype->name }} @if($partytype->childs->count() == 0) <a data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{domain_route('company.admin.partytype.update',[$partytype->id])}}" data-ticked="{{$partytype->allow_salesman}}" class="btn btn-sm button-blue"><i class="fa fa-edit"></i></a>
            <span area-id="{{$partytype->id}}" destroy-url="{{domain_route('company.admin.partytype.destroy',[$partytype->id])}}" class="btn btn-sm button-red"><i class="fa fa-trash" ></i></span>
            @else   
            <p data-name="{{$partytype->name}}" data-short-name="{{$partytype->short_name}}" data-id="{{$partytype->id}}" superior-id="{{$partytype->parent_id}}" edit-url="{{domain_route('company.admin.partytype.update',[$partytype->id])}}" data-ticked="{{$partytype->allow_salesman}}" class="btn btn-sm button-blue"><i class="fa fa-edit" ></i></p>
            @endif
            @if(count($partytype->childs))
              @include('company.partytypes.managePartyChild',['childs' => $partytype->childs])
            @endif
          </li>
        @endforeach
        </ul>
      </div>
      <div class="col-xs-6">
      <div class="box-header">
        <h3 class="box-title">
          Add New Party Type 
          <button class="success-btn-right refreshing hide"><i class="fa fa-refresh"></i></button>
        </h3>
      </div>
      <div class="box-bodys">

        {!! Form::open(array('url' => url(domain_route("company.admin.partytype.store", ["domain" => request("subdomain")])), 'method' => 'post', 'files'=> true,'id'=>'frmAddNewPartyType' ,'autocomplete'=>"off")) !!}
  
        <!-- <input type="hidden" name="pcompany_id" value="{{config('settings.company_id')}}"> -->
  
        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
          {!! Form::label('Name:') !!}
          {!! Form::text('name', old('name'), ['class'=>'form-control', 'placeholder'=>'Enter Name','required']) !!}
          <span class="text-danger">{{ $errors->first('name') }}</span>
        </div>
  
        <div class="form-group {{ $errors->has('sname') ? 'has-error' : '' }}">
          {!! Form::label('Short Name:') !!}
          {!! Form::text('short_name', old('short_name'), ['class'=>'form-control', 'placeholder'=>'Enter Short Name','maxlength'=>"3",'required']) !!}
          <span class="text-danger">{{ $errors->first('sname') }}</span>
        </div>
  
        <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}" @if(config('settings.allowed_party_type_levels') ==1 ) hidden @endif>
          {!! Form::label('Superior:') !!}
          {{-- {!! Form::select('parent_id',$partytypes, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Party Type']) !!} --}}
          <select id="select_party_types" name="parent_id" class="form-control" placeholder="Select Party Type">
            <option value="">Select Party Type</option>
            @foreach($allpartypes as $party)
              <option value="{{$party['id']}}">{{$party['name']}}</option>
            @endforeach
          </select>
          <span class="text-danger">{{ $errors->first('parent_id') }}</span>
        </div>
        <div class="form-group">
          <!-- <button id="btnAddParty" class="btn btn-custom-primary">Add New</button> -->

          {!! Form::submit('Add Party Type', ['class' => 'btn btn-primary pull-right btnAddParty', 'id'=> 'btnAddParty']) !!}

        </div>
  
        {!! Form::close() !!}
      </div>


      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>

<!-- Modal Section -->
    <div class="modal fade" id="modalEditPartyName" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
      <form id="editPartyName">
        @csrf
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 align="center" class="modal-title" id="exampleModalLongTitle">
              Update Party Type
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input id="party_edit_company_id" type="hidden" name="pecompany_id" value="{{config('settings.company_id')}}">
            <label>Name</label>
            <input id="party_type_nameonly" class="form-control" type="text" name="name">
            <label>Short Name</label>
            <input id="party_type_short_nameonly" class="form-control" type="text" name="short_name">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button> --}}
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </div>
      </div>
      </form>
    </div>

    <div class="modal fade" id="modalEditPartyType" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
      <form id="editPartyType">
        @csrf
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 align="center" class="modal-title" id="exampleModalLongTitle">
              Update Party Type
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input id="party_edit_company_id" type="hidden" name="company_id" value="{{config('settings.company_id')}}">
            <label>Name</label>
            <input id="party_type_name" class="form-control" type="text" name="name">
            <label>Short Name</label>
            <input id="party_type_short_name" class="form-control" type="text" name="short_name">
             <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}"  @if(config('settings.allowed_party_type_levels') ==1 ) hidden @endif>
              {!! Form::label('Superior:') !!}
              {{-- {!! Form::select('parent_id',$partytypes, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Party Type']) !!} --}}
              <select id="party_parent" name="parent_id" class="form-control" placeholder="Select Party Type">
                <option>Select Party Type</option>
                @foreach($allpartypes as $party)
                  <option value="{{$party['id']}}">{{$party['name']}}</option>
                @endforeach
              </select>
              <span class="text-danger">{{ $errors->first('parent_id') }}</span>
            </div>

          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button> --}}
            <button type="submit" class="btn btn-primary" id="update-party-type">Update</button>
          </div>
        </div>
      </div>
      </form>
    </div>

    <div class="modal fade" id="modalDeletePartyType" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
      <form id="delPartyType">
        @csrf
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 align="center" class="modal-title" id="exampleModalLongTitle">
              Are you sure you want to delete this party type?
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </h4>
          </div>
          <div class="modal-body">
            <input id="del_company_id" type="hidden" name="company_id" value="{{config('settings.company_id')}}">
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button> --}}
            <button type="submit" class="btn btn-warning" id="del-party-type">Yes,Delete</button>
          </div>
        </div>
      </div>
      </form>
    </div>
  <!-- End Modal Section -->