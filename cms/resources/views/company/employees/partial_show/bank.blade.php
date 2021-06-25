<div class="box-body">
  <form id="UpdateBankDetail">
    <div class="row">
      @if(Auth::user()->isCompanyEmployee() && Auth::user()->EmployeeId()==$employee->id)
      @elseif(!($isManager=='true') && $employee->is_owner==1)
      @else
      @if(Auth::user()->can('employee-update'))
      <span id="ActivateBankEdit" class="btn btn-default btn-sm pull-right" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Edit</span>
      <span id="ActivateBankCancel" class="btn btn-default btn-sm pull-right hide" style="margin-right: 10px;"> <i class="fa fa-edit"></i> Cancel</span>
      <span id="ActivateBankUpdate" class="hide"><button style="margin-right: 10px;" class="btn btn-default btn-sm pull-right" type="submit"><i class="fa fa-edit"></i>Update</button></span>
      @endif
      @endif
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-user-o icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Account Holder Name</h4>
            <input type="text" name="employee_id" value="{{$employee->id}}" hidden>
            <p id="empAccHolder" class="text-display">{{ ($employee->acc_holder)?$employee->acc_holder:'N/A' }}</p>
            <p class="text-form" hidden><input name="acc_holder" class="form-control" type="text" value="{{ ($employee->acc_holder)?$employee->acc_holder:'' }}" /></p>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-qrcode icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Account Number</h4>
            <p id="empAccNumber" class="text-display">{{ ($employee->acc_number)?$employee->acc_number:'N/A' }}</p>
            <p class="text-form" hidden><input name="acc_number" class="form-control" type="text" value="{{ ($employee->acc_number)?$employee->acc_number:'' }}" /></p>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-bank icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Bank</h4>
            <p id="empBankName" class="text-display">{{ ($employee->bank_id)?$employee->bank_name:'N/A' }}</p>
            <p class="text-form" hidden>
              {!! Form::select('bank_id', $banks, isset($employee)? $employee->bank_id:'',  ['placeholder' => 'Select a bank', 'class' => 'form-control select2']) !!}
            </p>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-qrcode icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">IFSC</h4>
            <p id="empIFSC" class="text-display">{{ ($employee->ifsc_code)?$employee->ifsc_code:'N/A' }}</p>
            <p class="text-form" hidden>
              {!! Form::text('ifsc_code',($employee->ifsc_code)?$employee->ifsc_code:'' , ['class' => 'form-control', 'placeholder' => 'IFSC Code']) !!}
            </p>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-qrcode icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">PAN Number(optional)</h4>
            <p id="empPAN" class="text-display">{{ ($employee->pan)?$employee->pan:'N/A' }}</p>
            <p class="text-form" hidden>
              {!! Form::text('pan', ($employee->pan)?$employee->pan:'' , ['class' => 'form-control', 'placeholder' => 'PAN Number']) !!}
            </p>
          </div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="media left-list bottom-border">
          <div class="media-left">
            <i class="fa fa-map-marker icon-size"></i>
          </div>
          <div class="media-body"><h4 class="media-heading">Branch</h4>
            <p id="empBranch" class="text-display">{{ ($employee->branch)?$employee->branch:'N/A' }}</p>
            <p class="text-form" hidden>
              {!! Form::text('branch', ($employee->branch)?$employee->branch:'', ['class' => 'form-control', 'placeholder' => 'BRANCH']) !!}
            </p>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>