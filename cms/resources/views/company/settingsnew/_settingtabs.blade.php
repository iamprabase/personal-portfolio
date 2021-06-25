<div class="col-xs-9 setting-content">
  <div class="tab-content" id="myTabContent">
    
    @if(config('settings.orders')==1)
    <div class="tab-pane fade {{($active == 'orderstatus')? 'active in':''}}" role="tabpanel" id="order_status-detail" aria-labelledby="order_status-detail-tab">
      @include('company.settingsnew.setting.orderstatus')
    </div>
    @endif
    
    @if(config('settings.ncal')==1)
    <div class="tab-pane fade {{($active == 'holiday')? 'active in':''}}" role="tabpanel" id="Nholiday-detail" aria-labelledby="Nholiday-detail-tab">
      @include('company.settingsnew.setting.nholiday')
    </div>
    @else
    <div class="tab-pane fade {{($active == 'holiday')? 'active in':''}}" role="tabpanel" id="holiday-detail" aria-labelledby="holiday-detail-tab">
      @include('company.settingsnew.setting.holiday')
    </div>
    @endif
    
    @if(config('settings.collections')==1)
    <div class="tab-pane fade {{($active == 'bank')? 'active in':''}}" role="tabpanel" id="bank-detail" aria-labelledby="bank-detail-tab">
      @include('company.settingsnew.setting.bank')
    </div>
    @endif
    @if(config('settings.party')==1)
    <div class="tab-pane fade {{($active == 'beat')? 'active in':''}}" role="tabpanel" id="beats-detail" aria-labelledby="beats-tab">
      @include('company.settingsnew.setting.beat')
    </div>
    <div class="tab-pane fade {{($active == 'business-types')? 'active in':''}}" role="tabpanel" id="business-types" aria-labelledby="business-types-tab">
      @include('company.settingsnew.setting.business')
    </div>
    @endif

    @if(config('settings.expenses')==1)
    <div class="tab-pane fade {{($active == 'expense-types')? 'active in':''}}" role="tabpanel" id="expense-types" aria-labelledby="expense-types-tab">
      @include('company.settingsnew.setting.expensetype')
    </div>
    @endif

    @if(config('settings.leaves')==1)
    <div class="tab-pane fade {{($active == 'leave-types')? 'active in':''}}" role="tabpanel" id="leave-types" aria-labelledby="leave-tab">
      @include('company.settingsnew.setting.leave')
    </div>
    @endif

    @if(config('settings.visit_module')==1 && config('settings.party')==1)
    <div class="tab-pane fade {{($active == 'visit-purpose')? 'active in':''}}" role="tabpanel" id="visit-purpose" aria-labelledby="visit-purpose">
      @include('company.settingsnew.setting.visitpurpose')
    </div>
    @endif

    @if(config('settings.party')==1)
    <div class="tab-pane fade {{($active == 'party-types')? 'active in':''}}" role="tabpanel" id="party-types" aria-labelledby="partytype-tab">
      @include('company.settingsnew.setting.partytypes')
    </div>
    @endif

    <div class="tab-pane fade {{($active == 'designation')? 'active in':''}}" role="tabpanel" id="designations-detail" aria-labelledby="designations-tab">
      @include('company.settingsnew.setting.designation')
    </div>

    <div class="tab-pane fade {{($active == 'returnreasons')? 'active in':''}}" role="tabpanel" id="returnreasons-detail"
      aria-labelledby="returnreasons-tab">
      @include('company.settingsnew.setting.returns')
    </div>
  
  </div>
</div>