<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
    <div class="alert alert-success">
      <p>{{ \Session::get('success') }}</p>
    </div><br />
    @endif
    @if (\Session::has('alert'))
    <div class="alert alert-warning">
      <p>{{ \Session::get('alert') }}</p>
    </div><br/>           
    @endif
    <div class="box">
      <div class="box-header">
        <span id="activitiesexports" class="pull-right"></span>
      </div>
      <div class="box-body @if($activities->count()>0) table-fix @endif">
        <table id="activity" class="table table-bcollectioned table-striped">
          <thead>
            @if( $activities->count()>0 )
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Title</th>
              <th>Type</th>
              {{-- <th>Note</th> --}}
              <th>Assigned By</th>
              <th>Assigned To</th>
              <th>Is it Complete?</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @php($i = 0)
            @foreach($activities as $activity)
            @php($i++)
            <tr>
              <td>{{ $i }}</td>
              <td>{{getDeltaDate(Carbon\Carbon::parse($activity->start_datetime)->format('Y-m-d'))}}
                {{Carbon\Carbon::parse($activity->start_datetime)->format('H:i')}}</td>
              <td>{{ $activity->title }}</td>
              <td>
                @if(isset($activity->activityType->name))
                {{ $activity->activityType->name}}
                @else
                <span hidden>{{ $activity->type}}</span>
                @endif
              </td>
              {{-- <td>{{$activity->note}}</td> --}}
              <td>

                @if(isset($activity->created_by))
                    @if(Auth::user()->isCompanyManager() || in_array($activity->created_by,$juniors))
                      <a href="{{domain_route('company.admin.employee.show',[$activity->created_by])}}">
                        {{$activity->createdByEmployee?$activity->createdByEmployee->name:""}}
                      </a>
                    @else
                      <a href="#" class="alert-user-modal">{{$activity->createdByEmployee?$activity->createdByEmployee->name:""}}</a>
                    @endif
                @endif

              </td>
              <td>

                @if(isset($activity->assigned_to))
                    @if(Auth::user()->isCompanyManager() || in_array($activity->assigned_to,$juniors))
                      <a href="{{domain_route('company.admin.employee.show',[$activity->assigned_to])}}">
                        {{$activity->assignedTo()->withTrashed()->first()->name}}
                      </a>
                    @else
                      <a href="#" class="alert-user-modal">{{$activity->assignedTo()->withTrashed()->first()->name}}</a>
                    @endif
                @endif

              </td>
              <td>
                <?php
                  if($activity->completion_datetime!=NULL){
                    $checkedStatus = "no_uncheck";
                  }else{
                    $checkedStatus = "no_check";
                  } 
                ?>
                @if(Auth::user()->can('activity-status') && (Auth::user()->isCompanyManager() || Auth::user()->EmployeeId()==$activity->created_by || Auth::user()->EmployeeId()==$activity->assigned_to) )
                <div class="round"><input type="checkbox" id="act{{$activity->id}}" class="check check_{{$activity->id}}" name="status" value="{{$activity->id}}" {{ ($activity->completion_datetime!=NULL)?'checked="checked"':''}}>
                <label for="act{{$activity->id}}"></label>
                </div>
                @else
                <div class="round"><input type="checkbox" id="act{{$activity->id}}" readonly="readonly" class="{{$checkedStatus}}" name="status" value="{{$activity->id}}" {{ ($activity->completion_datetime!=NULL)?'checked="checked"':''}}>
                <label for="act{{$activity->id}}"></label>
                </div>
                @endif
              </td>
              <td>
                <a style="color:green;font-size: 15px;margin-left:5px;  "
                  href="{{ domain_route('company.admin.activities.show',[$activity->id]) }}" class="" style=""><i
                    class="fa fa-eye"></i></a>
                <?php $empId = Auth::user()->EmployeeId(); ?>
                @if(Auth::user()->can('activity-update'))
                  @if(Auth::user()->isCompanyManager() || $empId== $activity->created_by || $empId == $activity->assigned_to)
                  <a style="color:#f0ad4e!important;font-size: 15px;margin-left:5px;  "
                    href="{{ domain_route('company.admin.activities.edit',[$activity->id]) }}" class="" style=""><i
                      class="fa fa-edit"></i></a>
                  @endif
                @endif
                @if((Auth::user()->isCompanyManager() && Auth::user()->can('activity-delete')) || $activity->created_by==Auth::user()->EmployeeID() && Auth::user()->can('activity-delete'))
                  <a style="color:red;font-size: 15px;margin-left:5px;" data-mid="{{ $activity->id }}" data-url="{{ domain_route('company.admin.activities.destroy', [$activity->id]) }}" data-toggle="modal" data-target="#delete" style=""><i class="fa fa-trash-o"></i></a>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
          @else
          <tr>
            <td colspan="10">No Record Found.</td>
          </tr>
          @endif
        </table>

      </div>
    </div>

  </div>
</div>
<div class="modal modal-default fade" id="alertCompleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close cancel" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Alert!</h4>
        </div>
          <div class="modal-body">
            <p class="text-center">
              Sorry! Only Assignor or Assignee can mark activity as <span id="textComplete">complete</span>.
            </p>
            <input type="hidden" name="expense_id" id="c_id" value="">
            <input type="text" id="accountType" name="account_type" hidden/>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning delete-button" data-dismiss="modal">Close</button>
          </div>
      </div>
    </div>
</div>