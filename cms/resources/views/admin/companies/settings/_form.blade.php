<div class="col-xs-9">
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade {{($active == 'profile')? 'active in':''}}" role="tabpanel" id="company" aria-labelledby="compamy">
      @include('admin.companies.settings.partials.basic')
    </div>
    <div class="tab-pane fade" role="tabpanel" id="company-details" aria-labelledby="party-type-tab">
      @include('admin.companies.settings.partials.company_details')
    </div>

   <!--  <div class="tab-pane fade" role="tabpanel" id="location" aria-labelledby="location-tab">
    </div> -->

{{-- 
    <div class="tab-pane fade {{($active == 'layout')? 'active in':''}}" role="tabpanel" id="admin"
         aria-labelledby="admin-tab">
      @include('admin.companies.settings.partials.layout')
    </div> --}}


    <div class="tab-pane fade {{($active == 'email')? 'active in':''}}" role="tabpanel" id="email-setup"
         aria-labelledby="email-setup-tab">
      @include('admin.companies.settings.partials.setup')
    </div>

    <div class="tab-pane fade {{($active == 'other')? 'active in':''}}" role="tabpanel" id="setup"
         aria-labelledby="setup-tab">
      @include('admin.companies.settings.partials.mainsetup')
    </div>


    <div class="tab-pane fade" role="tabpanel" id="plan-detail" aria-labelledby="plan-detail-tab">
      @include('admin.companies.settings.partials.plans')
    </div>

    <!-- <div class="tab-pane fade" role="tabpanel" id="party-type" aria-labelledby="party-type-tab">
      @include('admin.companies.settings.partials.partytype')
    </div> -->

    <div class="tab-pane fade" role="tabpanel" id="modules" aria-labelledby="party-type-tab">
      @include('admin.companies.settings.partials.mainmodules')
    </div>

    <div class="tab-pane fade" role="tabpanel" id="modulesdisksize" aria-labelledby="modulesdisksize-tab">
      @include('admin.companies.settings.partials.modulesdisksize')
    </div>

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
            <input id="party_edit_company_id" type="hidden" name="company_id" value="{{$clientSettings->id}}">
            <label>Name</label>
            <input id="party_type_nameonly" class="form-control" type="text" name="name">
            <label>Short Name</label>
            <input id="party_type_short_nameonly" class="form-control" type="text" name="short_name">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button>
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
            <input id="party_edit_company_id" type="hidden" name="company_id" value="{{$clientSettings->id}}">
            <label>Name</label>
            <input id="party_type_name" class="form-control" type="text" name="name">
            <label>Short Name</label>
            <input id="party_type_short_name" class="form-control" type="text" name="short_name">
             <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
              {!! Form::label('Superior:') !!}
              {{-- {!! Form::select('parent_id',$allPartiestype, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Party Type']) !!} --}}
              <select id="party_parent" name="parent_id" class="form-control" placeholder="Select Party Type">
                <option>Select Party Type</option>
                @foreach($allPartiestype as $party)
                  <option value="{{$party['id']}}">{{$party['name']}}</option>
                @endforeach
              </select>
              <span class="text-danger">{{ $errors->first('parent_id') }}</span>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button>
            <button type="submit" class="btn btn-primary">Update</button>
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
              Are you sure you want to delete this event?
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </h4>
          </div>
          <div class="modal-body">
            <input id="del_company_id" type="hidden" name="company_id" value="{{$clientSettings->id}}">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">No,Cancel</button>
            <button type="submit" class="btn btn-warning">Yes,Delete</button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>