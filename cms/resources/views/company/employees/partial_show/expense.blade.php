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
        <span id="grandTotalEAmount"></span>
        <span id="expenseexports" class="pull-right"></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body table-fix">
        <table id="expense" class="table table-bcollectioned table-striped">
          <thead>
          @if( !$employee->expenses->isEmpty() )
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Amount</th>
              @if(config('settings.party')==1)
              <th>Party</th>
              @endif
              <th>Approved/Cancelled By</th>
              <th>Status</th>
              <th>Action</th>
              <th style="display: none;">Eamount</th>
            </tr>
          </thead>
          <tbody>
          @php($i = 0)
          @foreach($employee->expenses as $expense)
            @php($i++)
            <tr>
              <td>{{ $i }}</td>
              <td>{{ getDeltaDate(date('Y-m-d',strtotime($expense->created_at)))  }}</td>
              <td>{{ config('settings.currency_symbol')}} {{ number_format((float)$expense->amount,2)}}</td>
              @if(config('settings.party')==1)
              <td>
                @if($expense->client_id == NULL)
                  --
                @else
                  @if(isset($expense->client->company_name))
                  <a class="clientLinks" href="{{in_array($expense->client_id, $handles)?domain_route('company.admin.client.show',[$expense->client_id]):''}}" data-viewable="{{in_array($expense->client_id, $handles)?domain_route('company.admin.client.show',[$expense->client_id]):''}}"> {{@$expense->client->company_name}}</a>
                  @else
                  {{$expense->client_id}}
                  @endif
                @endif
              </td>
              @endif
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
                    <a href="#" class="edit-modal-expense" data-id="{{$expense->id}}" data-status="{{$expense->status}}" data-id="{{$expense->id}}" data-status="{{$expense->status}}" data-remark="{{$expense->remark}}">
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
                    <a href="#" class="{{ ((getEmployee($expense->employee_id)['superior'])==Auth::user()->EmployeeId())?'edit-modal-expense':'alert-modal'}}" data-id="{{$expense->id}}"
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