<div class="col-xs-12">
  <div class="box-header">
    <h3 class="box-title">Client Settings</h3>
    <button class="btn btn-primary pull-right addnewsetting" data-toggle="modal" data-target="exampleModalCenterclient">
      <i class="fa fa-plus"></i> Create New
    </button>
  </div>
</div>
<div class="box-body">
  
  <table id="clientSettings" class="table table-bordered table-striped">
    
    <thead>
    
    <tr>
      <th>#</th>
      <th>Company</th>
      <th>Option</th>
      <th>Value</th>
      <th>Action</th>
    </tr>
    
    </thead>
    
    <tbody>
    
    @php($i = 0)
    
    @foreach($client_settings_new as $setting)
      @php($i++)
      <tr>
        
        <td>{{ $i }}</td>
        
        <td>{{ $setting->company->company_name}}</td>
        <td>{{ $setting->option}}</td>
        <td>{{ $setting->value}}</td>
        <td>
          <a class="btn btn-primary btn-sm editSetting" data-option="{{$setting->option}}"
             data-value="{{$setting->value}}" data-company="{{$setting->company_id}}" data-id="{{ $setting->id }}"
             style="padding: 3px 6px; height: auto !important;"><i class="fa fa-edit"></i></a>
          <a class="btn btn-danger btn-sm deleteSetting" data-id="{{ $setting->id }}"
             style="padding: 3px 6px; height: auto !important;"><i class="fa fa-trash-o"></i></a>
        </td>
      </tr>
    
    @endforeach
    
    </tbody>
  
  </table>

</div>

<div class="modal fade" id="exampleModalCenterclient" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  {!! Form::open(array('url' => url(domain_route("company.admin.setting.addNewSettings")), 'method' => 'post','id'=>'AddNewClientSettings', 'files'=> true)) !!}
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" align="center" id="exampleModalLongTitle">Add New Client Settings
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right">Company</label></div>
          <div class="col-xs-6">
            <select name="company_id" class="form-control select2">
              @foreach($companies as $company)
                <option value="{{$company->id}}">{{$company->company_name}}</option>
              @endforeach
            </select>
            {{-- <input required class="form-control" type="text" name="name"> --}}
          </div>
        </div>
        <br/>
        <div class="row">
          
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right">Option</label></div>
          <div class="col-xs-6"><input placeholder="Enter Option" required class="form-control" type="text"
                                       name="option"></div>
        </div>
        <br/>
        <div class="row">
          
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right">Value</label></div>
          <div class="col-xs-6"><input placeholder="Enter Value" required class="form-control" type="text" name="value">
          </div>
        </div>
        <br/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Add</button>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</div>

<div class="modal fade" id="editModalCenterclient" tabindex="-1" role="dialog" aria-labelledby="editModalCenterclient"
     aria-hidden="true">
  {!! Form::open(array('url' => url(domain_route("company.admin.setting.editNewSettings")), 'method' => 'post','id'=>'EditClientSettings', 'files'=> true)) !!}
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" align="center" id="exampleModalLongTitle">Update Client Settings
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right">Company</label></div>
          <div class="col-xs-6">
            <input type="text" id="edit_settings_id" name="settingId" hidden/>
            <select id="edit_settings_company" name="company_id" class="form-control select2">
              {{--               <option>test 1</option>
                            <option>test 12</option>
                            <option selected="selected">test 13</option>
                            <option>test 15</option> --}}
              @foreach($companies as $company)
                <option value="{{$company->id}}">{{$company->company_name}}</option>
              @endforeach
            </select>
            {{-- <input required class="form-control" type="text" name="name"> --}}
          </div>
        </div>
        <br/>
        <div class="row">
          
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right">Option</label></div>
          <div class="col-xs-6"><input id="edit_settings_option" placeholder="Enter Option" required
                                       class="form-control" type="text" name="option"></div>
        </div>
        <br/>
        <div class="row">
          
          <div class="col-xs-1"></div>
          <div class="col-xs-3"><label class="pull-right">Value</label></div>
          <div class="col-xs-6"><input id="edit_settings_value" placeholder="Enter Value" required class="form-control"
                                       type="text" name="value"></div>
        </div>
        <br/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</div>


<div class="modal fade" id="deleteModalCenterclient" tabindex="-1" role="dialog" aria-labelledby="editModalCenterclient"
     aria-hidden="true">
  {!! Form::open(array('url' => url(domain_route("company.admin.setting.deleteNewSettings")), 'method' => 'post','id'=>'deleteClientSettings', 'files'=> true)) !!}
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" align="center" id="exampleModalLongTitle">Are you sure want to delete this Client
          Settings??
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <input type="text" id="delete_settings_id" name="settingId" hidden/>
          </div>
        </div>
        <br/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning">Delete</button>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</div>