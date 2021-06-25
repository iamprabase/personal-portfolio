<div class="col-xs-12">
  <h3 class="site-tital">Beats List</h3>
</div>
<div class="info">
  <div class="col-xs-6">
    <div class="box">
      <div class="box-header">
      </div>
      <div class="box-body">
        <table id="tbl_beats" class="table table-bordered table-striped">
          
          <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th style="min-width: 90px;">Action</th>
          </tr>
          </thead>
          <tbody id="tbody_beats">
          @php($i = 0)
          @foreach($beats as $beat)
            @php($i++)
            <tr>
              
              <td>{{ $i }}</td>
              
              <td>{{ $beat->name}}</td>
              
              <td>
                <a class="btn btn-success btn-sm beat-view" data-bid="{{ $beat->id }}"
                data-url="{{ domain_route('company.admin.beat.show', [$beat->id]) }}" data-name="{{  $beat->name }}"><i
                      class="fa fa-eye"></i></a>
                <a class="btn btn-primary btn-sm beat-edit" data-name="{{$beat->name}}" data-city="{{$beat->city_id}}" data-bid="{{ $beat->id }}" data-edit-url="{{ domain_route('company.admin.beat.edit', [$beat->id]) }}"
                   data-url="{{ domain_route('company.admin.beat.update', [$beat->id]) }}" ><i
                      class="fa fa-edit"></i></a>
                <?php $del=1; ?>
                @if(in_array($beat->id, $beatsArray))
                  <?php $del = 0; ?>
                @endif
                @if($beat->parties->count()>0)
                  <?php $del = 0; ?>
                @endif
                @if($del==1)
                <a class="btn btn-danger btn-sm beat-delete" data-bid="{{ $beat->id }}"
                   data-url="{{ domain_route('company.admin.beat.destroy', [$beat->id]) }}" ><i
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
        
        <h3 class="box-title">Create Beat</h3>
        
        <span id="userexports" class="pull-right"></span>
      
      
      </div>
      
      <div class="box-body">
        {!! Form::open(array('url' => url(domain_route("company.admin.beat.store", ["domain" => request("subdomain")])), 'method' => 'post','id'=>'addNewBeat')) !!}
        
        <div class="form-group @if ($errors->has('name')) has-error @endif">
          
          {!! Form::label('name', 'Name') !!}<span style="color: red">*</span>
          
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Beat Name', 'required']) !!}
          
          @if ($errors->has('name')) <p class="help-block has-error">{{ $errors->first('name') }}</p> @endif
        
        </div>

        <div class="form-group">
        
          {!! Form::label('city', 'City') !!}
        
          {!! Form::select('beatcity', ["null" => "Select City"] +$cities, null, ['class' => 'form-control select2', 'id'=>'beatcity']) !!}
        
        </div>
        
        <div class="form-group @if ($errors->has('party_id')) has-error @endif">
          {!! Form::label('party_id', 'Assign party') !!}<!-- <span style="color: red">*</span> -->
          <select name="partyId[]" multiple id="partyId" class="form-control">
            @foreach($partylist as $party)
              <option value="{{ $party->id }}">{{ $party->company_name }}</option>
            @endforeach
          </select>
          @if ($errors->has('party_id')) <p class="help-block has-error">{{ $errors->first('party_id') }}</p> @endif
        </div>
        {!! Form::submit('Add Beat', ['class' => 'btn btn-primary pull-right addBeat']) !!}
        
        {!! Form::close() !!}
      </div>
    </div>
  </div>

  <div class="modal modal-default fade" id="modalShowBeat" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog small-modal" unit="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center show-beat-name" id="myModalLabel"></h4>
        </div>
        <form method="post" class="remove-record-modelbeat">
          {{csrf_field()}}
          <div class="modal-body">
            <div id="beat_parties">
              <ul class="beatparties">
              </ul>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal modal-default fade" id="modalEditBeat" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog small-modal" unit="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">Edit Beat</h4>
        </div>
        <form method="post" class="remove-record-modelbeat" id="updateBeatSettings">
          {{csrf_field()}}
          <div class="modal-body">
            <label>Beat name</label>
            <input id="editBeatName" type="text" name="name" class="form-control" />
            <input id="editbeat_id" type="text" name="beat_id" hidden>
            <div class="form-group">
            
              {!! Form::label('city', 'City') !!}
            
              {!! Form::select('edit_beatcity', ["null" => "Select City"] +$cities, null, ['class' => 'form-control edit_beatcity', 'id'=>'edit_beatcity']) !!}
            
            </div>
            <label>Assign Party</label>
            <select name="partyId[]" multiple id="assignPartyId" class="form-control">
            </select>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn" data-dismiss="modal">No, Cancel</button> --}}
            <button type="submit" class="btn btn-primary updateBeat">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="modal modal-default fade" id="deletebeat" tabindex="-1" unit="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog small-modal" unit="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <form id="ajaxRemoveBeat" method="post" class="remove-record-modelbeat">
          {{csrf_field()}}
          <div class="modal-body">
            <p class="text-center">
              Are you sure you want to delete this?
            </p>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-success" data-dismiss="modal">No, Cancel</button> --}}
            <button id="btn-beatDelete-key" type="submit" class="btn btn-warning">Yes, Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>