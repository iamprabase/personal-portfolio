<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" aria-hidden="true">&times;</button>
        <p>{{ \Session::get('success') }}</p>
      </div><br/>
    @endif
    @if (\Session::has('alert'))
    <div class="alert alert-warning">
      <p>{{ \Session::get('alert') }}</p>
    </div><br/>           
    @endif
    <div class="box">
      <div class="box-header">
        <span id="leaveexports" class="pull-right"></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body   @if( !$employee->leaves->isEmpty() ) table-fix @endif">
        <table id="leave" class="table table-bcollectioned table-striped">
          <thead>
          @if( !$employee->leaves->isEmpty() )
            <tr>
              <th>#</th>
              <th>From</th>
              <th>To</th>
              <th>Total Days</th>
              <th style="width: 180px;">Reason</th>
              <th>Type</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          @php($i = 0)
          @foreach($employee->leaves as $leave)
            @php($i++)
            <tr>
              <td>{{ $i }}</td>
              <td>{{ getDeltaDate(date('Y-m-d', strtotime($leave->start_date))) }}</td>
              <td>{{ getDeltaDate(date('Y-m-d', strtotime($leave->end_date))) }}</td>
              <td>{{ getDays($leave->start_date,$leave->end_date) }}</td>
              <td>{{ $leave->leave_desc }}</td>
              <td>{{ getLeaveType($leave->leavetype)['name']}}</td>
              @if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin())
                  <td>
                    @if(Auth::user()->can('leave-status'))
                    <a href="#" class="edit-modal-leave" data-id="{{$leave->id}}" data-status="{{$leave->status}}" data-remark="{{$leave->remarks}}">
                    @else
                    <a href="#" class="alert-modal" data-id="{{$leave->id}}" data-status="{{$leave->status}}" data-remark="{{$leave->remarks}}">
                    @endif
                      @if($leave->status =='Approved')
                        <span class="label label-success">{{ $leave->status}}</span>

                      @elseif($leave->status =='Pending')
                        <span class="label label-warning">{{ $leave->status}}</span>

                      @else
                        <span class="label label-danger">{{ $leave->status}}</span>

                      @endif
                    </a>
                  </td>
                @else
                  <td>
                    @if(Auth::user()->can('leave-status'))
                    <a href="#" class="{{ ((getEmployee($leave->employee_id)['superior'])==Auth::user()->EmployeeId())?'edit-modal-leave':'alert-modal'}}" data-id="{{$leave->id}}"
                       data-status="{{$leave->status}}" data-remark="{{$leave->remarks}}">
                    @else
                    <a href="#" class="alert-modal" data-id="{{$leave->id}}"
                       data-status="{{$leave->status}}" data-remark="{{$leave->remarks}}">
                    @endif
                      @if($leave->status =='Approved')
                        <span class="label label-success">{{ $leave->status}}</span>

                      @elseif($leave->status =='Pending')
                        <span class="label label-warning">{{ $leave->status}}</span>

                      @else
                        <span class="label label-danger">{{ $leave->status}}</span>

                      @endif
                    </a>
                  </td>
                @endif
              <td>
                <a href="{{ domain_route('company.admin.leave.show',[$leave->id]) }}"
                   class="btn btn-success btn-sm" style="padding: 3px 6px;"><i class="fa fa-eye"></i></a>
                @if(Auth::user()->can('leave-update'))
                <a href="{{ domain_route('company.admin.leave.edit',[$leave->id]) }}"
                   class="btn btn-warning btn-sm" style="padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                @endif

                @if(Auth::user()->can('leave-delete') && Auth::user()->EmployeeId() == $leave->employee_id && $leave->status =='Pending') <a class="btn btn-danger btn-sm delete" data-mid="{{$leave->id}}"
                  data-url="{{domain_route('company.admin.leave.destroy',[$leave->id])}}" data-toggle="modal" data-target="#delete" style="padding: 3px 6px;"><i class="fa fa-trash-o"></i></a>
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
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>