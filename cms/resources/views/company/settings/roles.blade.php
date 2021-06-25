<div class="party-details">
  <!-- pop up for edit and delete-->
  <div class="modal fade" id="exampleModal001" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog add-group-sub" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="exampleModalLabel">Add Group</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body add-group-sec">
          <form action="">
            <div class="form-group ">
              <div class="row">
                <div class="col-sm-12">
                  <label for="title1"> Group Name </label>
                  <div class="position-relative has-icon-left">
                    <input class="form-control" placeholder="Enter group name" id="title1" name="" type="text">
                    <div class="form-control-position">
                      <i class="fa fa-users" aria-hidden="true"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group ">
              <div class="row">
                <div class="col-sm-12">
                  <label for="" class="hover">Parent Group </label>
                  <div class="position-relative has-icon-left">
                    <select class="form-control" required="" name="status"><option value="">None</option><option value="Active">Parent</option><option value="Inactive">Child</option></select>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!--  -->
  <div class="modal fade" id="exampleModa2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog add-group-sub" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="exampleModalLabel">Add Group</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body popup-message">
          <span> Are you sure you want to delete the selected file</span>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary">Delete File</button>
          {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancle</button> --}}
        </div>
      </div>
    </div>
  </div>

  <!-- rolld start -->
  <div class="tab-pane active" id="rollssetting">
    <div class="content">
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">

                <h3 class="card-title card-title2">Roles <a href="#" class="edit-roles" data-toggle="modal" data-target="#exampleModal01"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add </a> </h3>
                <!-- role edit start -->
                <div class="modal fade" id="exampleModal01" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog add-group-sub" role="document">
                    <div class="modal-content">
                      <form action="{{ domain_route('company.admin.roles.store') }}" method="POST">
                        @csrf
                      <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Add Role</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body add-group-sec">
                        
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    <input type="text" name="name" class="form-control" placeholder="Name">
                                </div>
                            </div>
                        </div>
                        
                      </div>
                      <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}
                        <button type="submit" class="btn btn-primary">Save</button>
                      </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- roles edit end -->
                <div id="rolesTabs" class="roletabs">
                  <ul class="nav nav-pills left-tab">
                    <?php $i=0; ?>
                    @foreach($roles as $role)
                    @if (session()->has('role'))
                        <?php $activeRole = session()->get('role'); ?>
                              <li class="nav-item {{($role->id==$activeRole)?'active':''}}">
                        @else 
                              <li class="nav-item {{($i==0)?'active':''}}">
                        @endif
                      <?php $i++; ?>
                      <a class="nav-link " href="#tabsroles{{$role->id}}" data-toggle="tab">{{$role->name}}@if($role->name=="Limited Access") (Default) @endif</a> 
                      <span class="actionstyles">
                        @if($role->name!='Full Access' && $role->name!="Limited Access")
                        <a data-id="{{$role->id}}" data-name="{{$role->name}}" data-action="{{domain_route('company.admin.role.update',[$role->id])}}" href="#" class="edit edit-role" data-toggle="modal" data-target="#admin-edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                        @if(count($role->users)==0)          
                        <a data-id="{{$role->id}}" data-name="{{$role->name}}" data-action="{{domain_route('company.admin.role.destroy',[$role->id])}}" href="#" class="edit delete-role" data-toggle="modal" data-target="#admin-delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        @endif
                        @endif
                      </span>
                    </li>   
                    @endforeach                 
                  </ul>
                </div><!--.role tabs end-->

                {{-- Modal for editing --}}
                <div class="modal fade" id="admin-edit" tabindex="-1" role="dialog" aria-labelledby="admin-editLabel" aria-hidden="true">
                  <div class="modal-dialog  add-group-sub" role="document">
                    <div class="modal-content">
                      <form id="updateRole" method="POST">
                        @csrf
                      <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Edit Role</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body add-group-sec">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    <input id="updateRoleName" type="text" name="name" class="form-control" placeholder="Name">
                                </div>
                            </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}
                        <button type="submit" class="btn btn-primary">Update</button>
                      </div>
                    </form>
                    </div>
                  </div>
                </div>

                {{-- Modal for deleting roles --}}
                <div class="modal fade" id="admin-delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  {{-- {!! Form::open(['method' => 'DELETE','domain_route' => ['company.admin.roles.destroy', 'id'],'style'=>'display:inline']) !!} --}}
                  <form id="deleteRole" method="POST">
                  @csrf
                    {{-- {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!} --}}
                  <div class="modal-dialog add-group-sub" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Remove Role</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body popup-message">
                        <span> Are you sure you want to delete this role?</span>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Delete Role</button>
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}
                      </div>
                    </div>
                  </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="tab-content">
                  <?php $i=0; ?>
                  @foreach($roles as $role)
                  @if(session()->has('role'))
                        <?php $activeRole = session()->get('role'); ?>
                        <div class="tab-pane {{($role->id==$activeRole)?'active':''}}" id="tabsroles{{$role->id}}" data-session='{{($activeRole)?"$activeRole":'norole'}}'>
                  @else 
                        <div class="tab-pane {{ ($i==0)?'active':''}}" id="tabsroles{{$role->id}}">
                  @endif
                    <?php $i++; ?>
                    {!! Form::open(array('url' => url(domain_route("company.admin.role.permission.update", ["domain" => request("subdomain")])), 'method' => 'post', 'autocomplete' => 'off')) !!}
                    <div class="card-body notopspace"> 
                      <h3 class="card-title card-title2">Permission ({{$role->name}}) <input class="btn btn-primary pull-right" id="update-rolepermission" type="submit" value="Update"></h3>
                      <table class="table">
                        <tbody>
                          <tr>
                            <td><strong></strong></td>
                            <td>
                              <label class="switch">
                                <input type="checkbox" class="toggle-all toggle-all-{{$role->id}}" data-role="{{$role->id}}">
                                <span class="slider round"></span>
                              </label>

                              <?php $totalmax = count($permission_categories); ?>
                              @if(config('settings.beat')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.tour_plans')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.returns')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.stock_report')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.collaterals')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.party')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.product')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.orders')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.zero_orders')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.collections')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.notes')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.activities')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.expenses')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.leaves')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.announcement')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.remarks')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.accounting')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.gpsreports')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.monthly_attendance')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.cincout')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.dsobyunit')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.dso')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.ordersreport')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.psoreport')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.spwise')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.dpartyreport')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.dempreport')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.ageing')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.party_wise_rate_setup')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.retailer_app')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.visit_module')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.party_files')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                              @if(config('settings.party_images')==0)
                              <?php $totalmax = $totalmax-1; ?>
                              @endif
                            </td>
                            <td><strong>Add </strong></td>
                            <td><strong>View </strong></td>
                            <td><strong>Update </strong></td>
                            <td><strong>Delete </strong></td>
                            <td><strong>Status </strong></td>
                          </tr>
                          @foreach($permission_categories as $permission_category)
                          @if((config('settings.beat')==0 && $permission_category->name=='Beatplans') || (config('settings.tour_plans')==0 && $permission_category->name=='Tourplans') || (config('settings.returns')==0 && $permission_category->name=='Returns') || (config('settings.stock_report')==0 && $permission_category->name=='Stocks') || (config('settings.collaterals')==0 && $permission_category->name=='Collaterals') || (config('settings.visit_module')==0 && $permission_category->name=='Party_Visit') ||(
                          config('settings.party')==0 && $permission_category->name=='Parties') ||(
                          config('settings.product')==0 && $permission_category->name=='Products') ||(
                          config('settings.orders')==0 && $permission_category->name=='Orders') ||(
                          config('settings.zero_orders')==0 && $permission_category->name=='Zero_Orders') ||(
                          config('settings.collections')==0 && $permission_category->name=='Collections') ||(
                          config('settings.collections')==0 && $permission_category->name=='PDCs') ||(
                          config('settings.notes')==0 && $permission_category->name=='Notes') ||(
                          config('settings.activities')==0 && $permission_category->name=='Activities') ||(
                          config('settings.expenses')==0 && $permission_category->name=='Expenses') ||(
                          config('settings.leaves')==0 && $permission_category->name=='Leaves') ||(
                          config('settings.announcement')==0 && $permission_category->name=='Announcements') ||(
                          config('settings.accounting')==0 && $permission_category->name=='Accounting') ||(
                          config('settings.gpsreports')==0 && $permission_category->name=='salesman_gps_path') ||(
                          config('settings.monthly_attendance')==0 && $permission_category->name=='monthly_attendance') ||(
                          config('settings.cincout')==0 && $permission_category->name=='checkin_cout') ||(
                          config('settings.dsobyunit')==0 && $permission_category->name=='dsoreportbyunit') ||(
                          config('settings.dso')==0 && $permission_category->name=='dsoreport') ||(
                          config('settings.ordersreport')==0 && $permission_category->name=='oreport') ||(
                          config('settings.psoreport')==0 && $permission_category->name=='psoreport') ||(
                          config('settings.spwise')==0 && $permission_category->name=='spartywisereport') ||(
                          config('settings.dpartyreport')==0 && $permission_category->name=='dpartyreport') ||(
                          config('settings.dempreport')==0 && $permission_category->name=='dempreport') ||(
                          config('settings.beat')==0 && $permission_category->name=='beatplanreport') ||(
                          config('settings.stock_report')==0 && $permission_category->name=='stocks_report') ||(
                          config('settings.returns')==0 && $permission_category->name=='returns_report') ||(
                          config('settings.ageing')==0 && $permission_category->name=='ageing') ||(
                          config('settings.remarks')==0 && $permission_category->name=='Day_Remarks'||config('settings.party_wise_rate_setup')==0 && $permission_category->name=='Parties_Rate_Setup'||config('settings.retailer_app')==0 && $permission_category->name=='outlet'||config('settings.visit_module')==0 && $permission_category->name=='Party Visit' || config('settings.party_files')==0 && $permission_category->name=='File_Uploads' || config('settings.party_images')==0 && $permission_category->name=='Image_Uploads')
                          )
                          @else
                          <tr @if($permission_category->name=="Reports" || $permission_category->name=="PartyType" ) style="background-color:lightyellow;" @endif>
                            <td> {{$permission_category->display_name}} </td>
                            <?php $j=1; $max = count($permission_category->permissions->where('enabled','1')); ?>
                            <td>
                              <label class="switch">
                                <input @if($role->name=='Full Access' && $permission_category->name=="setting") disabled checked="true" @else class="switches switches-{{$role->id}} rowtoggler-{{$role->id}} {{$role->id}}-{{$permission_category->name}} @if($permission_category->permission_model=='PartyType')permission_partytype-{{$role->id}} {{$permission_category->name}}-permission_partytype-{{$role->id}} @endif" @endif data-toggle="true" data-categoryname="{{$permission_category->name}}" data-max="{{$max}}" data-role="{{$role->id}}" data-totalmax="{{$totalmax}}" type="checkbox">
                                <span class="slider round"></span>
                              </label>
                            </td>
                            @foreach($permission_category->permissions as $category)
                            <td> 
                              @if($category->enabled==1)
                                @if($role->name=='Full Access' && $permission_category->name=="setting")
                                  <label class="switch">
                                    <input disabled data-role="{{$role->id}}" data-categoryname="{{$permission_category->name}}" data-categoryid="{{$permission_category->id}}" data-max="{{$max}}" data-totalmax="{{$totalmax}}" data-index="{{$j}}" @if($role->hasPermissionTo($category->id)) checked="true" 
                                    @endif type="checkbox" name="permission[]" value="{{$category->id}}">
                                    <span class="slider round"></span>
                                    <input hidden data-role="{{$role->id}}" data-categoryname="{{$permission_category->name}}" data-categoryid="{{$permission_category->id}}" data-max="{{$max}}" data-totalmax="{{$totalmax}}" data-index="{{$j}}" @if($role->hasPermissionTo($category->id)) checked="true" 
                                    @endif type="checkbox" name="permission[]" value="{{$category->id}}">
                                  </label>
                                @else
                                  <label class="switch">
                                  <input class="switches switches-{{$role->id}} @if($permission_category->permission_model=='PartyType')permission_partytype-{{$role->id}} {{$permission_category->name}}-permission_partytype-{{$role->id}} {{$permission_category->name}}-permission_partytype-{{$role->id}}-{{$j}}@endif {{$permission_category->name}}-{{$role->id}} {{$permission_category->name}}-{{$role->id}}-{{$j}}" data-role="{{$role->id}}" data-categoryname="{{$permission_category->name}}@if($permission_category->permission_model=='PartyType')-permission_partytype @endif" data-categoryid="{{$permission_category->id}}" data-max="{{$max}}" data-totalmax="{{$totalmax}}" data-index="{{$j}}" @if($role->hasPermissionTo($category->id)) checked="true" 
                                  @endif type="checkbox" name="permission[]" value="{{$category->id}}">
                                  <span class="slider round"></span>
                                </label>
                                @endif
                              @else
                              -----
                              @endif
                            </td>
                            <?php $j++; ?>
                            @endforeach
                          </tr>
                          @endif
                          @endforeach
                        </tbody>
                      </table>
                      
                    </div>
                    <div class="text-center">
                      <input type="text" name="role_id" value="{{$role->id}}" hidden>
                      <input class="btn btn-primary" id="update-rolepermission" type="submit" value="Update">
                    </div>  
                    {!! Form::close() !!}
                  </div>
                  @endforeach
                </div><!--.div content end-->
              </div><!--.col-sm-9 end-->
            </div><!--.row end-->

          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- rolls end -->
</div>