@php($i = 0)
  @foreach($cheques as $cheque)
    @php($i++)
    <tr>
      <td>{{ $i }}</td>
      <td><a href="{{domain_route('company.admin.client.show',[$cheque->client_id])}}"> {{ getClient($cheque->client_id)['company_name']}}</a></td>
      <td>@if(isset($cheque->bank->name)){{ $cheque->bank->name }}@else <span hidden>{{$cheque->id}}</span> @endif</td>
      <td>
        @if($cheque->employee_type == "Admin")
          {{ Auth::user()->name.' (Admin)' }}
        @elseif($cheque->employee_type == "Employee")
          <a href="{{domain_route('company.admin.employee.show',[$cheque->employee_id])}}"> {{ getEmployee($cheque->employee_id)['name'] }}</a>
        @endif
      </td>
      <td>{{ getDeltaDate(date('Y-m-d', strtotime($cheque->cheque_date))) }}</td>
      <td>{{ getDeltaDate(date('Y-m-d', strtotime($cheque->payment_date))) }}</td>
      <td>{{ config('settings.currency_symbol')}} {{ number_format((float)$cheque->payment_received,2)}}</td>
      <td>{{ (strlen($cheque->payment_status_note)>15)?substr($cheque->payment_status_note, 0,15).'...':$cheque->payment_status_note }}</td>
      <td>
        <?php $current_date = Carbon\Carbon::now()->format('Y-m-d'); ?>
        @if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin())
          <a href="#" class="edit-modal" data-id="{{$cheque->id}}" data-status="{{$cheque->payment_status}}"
             data-remark="{{$cheque->payment_status_note}}">
            @if($cheque->payment_status == 'Pending')
              @if($cheque->cheque_date < $current_date)
                <span class="label label-primary">Overdue</span>
              @else
                <span class="label label-warning">Pending</span>
              @endif
            @elseif($cheque->payment_status == 'Deposited')
              <span class="label label-default">{{ $cheque->payment_status}}</span>

            @elseif($cheque->payment_status == 'Cleared')
              <span class="label label-success">{{ $cheque->payment_status}}</span>
            @elseif($cheque->payment_status == 'Bounced')
              <span class="label label-danger">{{ $cheque->payment_status}}</span>
            @else
              <span class="label label-danger">N/A</span>
            @endif
          </a>
          @else
          <a href="#" class="{{ ((getEmployee($cheque->employee_id)['superior'])==Auth::user()->EmployeeId())?'edit-modal':'alert-modal'}}" data-id="{{$cheque->id}}" data-status="{{$cheque->payment_status}}" data-remark="{{$cheque->payment_status_note}}">
            @if($cheque->payment_status == 'Pending')
              @if($cheque->cheque_date < $current_date)
                <span class="label label-primary">Overdue</span>
              @else
                <span class="label label-warning">Pending</span>
              @endif
            @elseif($cheque->payment_status == 'Deposited')
              <span class="label label-default">{{ $cheque->payment_status}}</span>

            @elseif($cheque->payment_status == 'Cleared')
              <span class="label label-success">{{ $cheque->payment_status}}</span>
            @elseif($cheque->payment_status == 'Bounced')
              <span class="label label-danger">{{ $cheque->payment_status}}</span>
            @else
              <span class="label label-danger">N/A</span>
            @endif
          </a>
          @endif
      </td>
      <td>
        <a href="{{ domain_route('company.admin.cheque.show',[$cheque->id]) }}"
           class="btn btn-success btn-sm" style="    padding: 3px 6px;"><i class="fa fa-eye"></i></a>
        <a href="{{ domain_route('company.admin.cheque.edit',[$cheque->id]) }}"
           class="btn btn-warning btn-sm" style="    padding: 3px 6px;"><i class="fa fa-edit"></i></a>
      </td>
      <td style="display: none;"> {{ $cheque->payment_received }}</td>
    </tr>
@endforeach