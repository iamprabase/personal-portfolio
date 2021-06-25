<div class="row">
  <div class="col-xs-12">
    @if (\Session::has('success'))
      <div class="alert alert-success">
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
        <span id="grandTotalEAmount"></span>
        <span id="expenseexports" class="pull-right"></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body table-fix">
        <table id="expense" class="table table-bcollectioned table-striped">
          <thead>
          @if( !$expenses->isEmpty() )
            <tr>
              <th>#</th>
              <!-- <th>Party Name</th> -->
              <th>Date</th>
              <th>Amount</th>
              <th>Added By</th>
              <th>Approved/Cancelled By</th>
              <th>Status</th>
              <th>Action</th>
              <th style="display:none;">EAmount</th>
            </tr>
          </thead>
          <tbody>
          @php($i = 0)
          @foreach($expenses as $expense)
            @php($i++)
            <tr>
              <td>{{ $i }}</td>
            <!-- <td>{{ getClient($expense->client_id)['company_name']}}</td> -->
              <td>{{ getDeltaDate(date("Y-m-d",strtotime($expense->created_at)))  }}</td>
              <td>{{ config('settings.currency_symbol')}}{{ number_format((float)$expense->amount,2)}}</td>
              <td>
                @if(isset($expense->employee_id) && isset($expense->employee->name))
                    <a href="{{domain_route('company.admin.employee.show',[$expense->employee_id])}}" datasalesman="{{ $expense->employee->name}}">{{getEmployee($expense->employee_id)['name']}}</a>
                @endif
              </td>
              <td>
                @if(isset($expense->approved_by))
                  @if($expense->approved_by==0)
                  {{ Auth::user()->managerName($expense->company_id)->name.' (Admin)' }}
                  @else
                  @if(in_array($expense->approved_by,$allSup))
                  <a href="#" class="alert-modal"> {{isset($expense->approvedBy->name)?$expense->approvedBy->name:''}}</a>
                  @else
                  <a href="{{domain_route('company.admin.employee.show',[$expense->approved_by])}}"> {{isset($expense->approvedBy->name)?$expense->approvedBy->name:''}}</a>
                  @endif
                  @endif
                @endif
              </td>


              @if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin())
                  <td>
                    @if(Auth::user()->can('expense-status'))
                    <a href="#" class="edit-modal" data-id="{{$expense->id}}" data-status="{{$expense->status}}" data-id="{{$expense->id}}" data-status="{{$expense->status}}" data-remark="{{$expense->remark}}">
                    @else
                    <a href="#" class="alert-modal" data-id="{{$expense->id}}" data-status="{{$expense->status}}" data-id="{{$expense->id}}" data-status="{{$expense->status}}" data-remark="{{$expense->remark}}">
                    @endif
                      @if($expense->status =='Approved')
                        <span class="label label-success">{{ $expense->status}}</span>
                      @elseif($expense->status =='Pending')
                        <span class="label label-warning">{{ $expense->status}}</span>
                      @else
                        <span class="label label-danger">{{ $expense->status}}</span>
                      @endif
                    </a>
                  </td>
                  @else
                  <td>
                    @if(Auth::user()->can('expense-status'))
                    <a href="#" class="{{ ((getEmployee($expense->employee_id)['superior'])==Auth::user()->EmployeeId())?'edit-modal':'alert-modal'}}" data-id="{{$expense->id}}"
                       data-status="{{$expense->status}}" data-remark="{{$expense->remark}}">
                    @else
                    <a href="#" class="alert-modal" data-id="{{$expense->id}}"
                       data-status="{{$expense->status}}" data-remark="{{$expense->remark}}">
                    @endif
                      @if($expense->status =='Approved')
                        <span class="label label-success">{{ $expense->status}}</span>
                      
                      @elseif($expense->status =='Pending')
                        <span class="label label-warning">{{ $expense->status}}</span>

                      @else
                        <span class="label label-danger">{{ $expense->status}}</span>

                      @endif
                    </a>
                  </td>
                  @endif
              <td>
                <a href="{{ domain_route('company.admin.expense.show',[$expense->id]) }}"
                   class="btn btn-success btn-sm" style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a>
                @if(Auth::user()->can('expense-update'))
                <a href="{{ domain_route('company.admin.expense.edit',[$expense->id]) }}"
                   class="btn btn-warning btn-sm" style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>
                @endif
                @if(Auth::user()->can('expense-delete'))
                <a class="btn btn-danger btn-sm delete" data-mid="{{ $expense->id }}"
                   data-url="{{ domain_route('company.admin.expense.destroy', [$expense->id]) }}"
                   data-toggle="modal" data-target="#delete" style="padding: 3px 6px;"><i
                      class="fa fa-trash-o"></i></a>
                @endif
              </td>
              <td style="display: none;">{{ $expense->amount }}</td>
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

<div id="myExpenseModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" id="changeStatus" method="POST"
                action="{{URL::to('admin/expense/changeStatus')}}">
            {{csrf_field()}}
            <input type="hidden" name="expense_id" id="expense_id" value="">
            <div class="form-group">
              <label class="control-label col-xs-2" for="id">Remark</label>
              <div class="col-xs-10">
                <textarea class="form-control" id="remark" placeholder="Your Remark.." name="remark" cols="50"
                          rows="5"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-xs-2" for="name">Status</label>
              <div class="col-xs-10">
                <select class="form-control" id="status" name="status">
                  <option value="Pending">Pending</option>
                  <option value="Approved">Approved</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn actionBtn" id="btn_change_status">
                <span id="footer_action_button" class='glyphicon'> </span> Change
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal">
                <span class='glyphicon glyphicon-remove'></span> Close
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>