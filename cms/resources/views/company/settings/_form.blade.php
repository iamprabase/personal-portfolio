<div class="col-xs-9 setting-content">
  <div class="tab-content" id="myTabContent">
    
    <div class="tab-pane fade {{($active == 'profile')? 'active in':''}}" role="tabpanel" id="company"
         aria-labelledby="compamy">
      @include('company.settings.profile')
    </div>

    <div class="tab-pane fade" role="tabpanel" id="location" aria-labelledby="location-tab">
    </div>

    <div class="tab-pane fade {{($active == 'layout')? 'active in':''}}" role="tabpanel" id="admin"
         aria-labelledby="admin-tab">
      @include('company.settings.admin_layout')
    </div>

    <div class="tab-pane fade {{($active == 'email')? 'active in':''}}" role="tabpanel" id="email-setup"
         aria-labelledby="email-setup-tab">
      @include('company.settings.email_setup')
    </div>

    <div class="tab-pane fade {{($active == 'other')? 'active in':''}}" role="tabpanel" id="setup"
         aria-labelledby="setup-tab">
      @include('company.settings.setup')
    </div>

    @if(config('settings.orders')==1)
    <div class="tab-pane fade {{($active == 'orderstatus')? 'active in':''}}" role="tabpanel" id="order_status-detail" aria-labelledby="order_status-detail-tab">
      @include('company.settings.orderstatus')
    </div>
    @endif

    @if(config('settings.ncal')==1)
    <div class="tab-pane fade {{($active == 'holiday')? 'active in':''}}" role="tabpanel" id="Nholiday-detail" aria-labelledby="Nholiday-detail-tab">
      @include('company.settings.nholiday')
    </div>
    @else
    <div class="tab-pane fade {{($active == 'holiday')? 'active in':''}}" role="tabpanel" id="holiday-detail" aria-labelledby="holiday-detail-tab">
      @include('company.settings.holiday')
    </div>
    @endif

    <div class="tab-pane fade {{($active == 'plan')? 'active in':''}}" role="tabpanel" id="plan-detail" aria-labelledby="plan-detail-tab">
      @include('company.settings.plan')
    </div>

    @if(config('settings.collections')==1)
    <div class="tab-pane fade {{($active == 'bank')? 'active in':''}}" role="tabpanel" id="bank-detail" aria-labelledby="bank-detail-tab">
      @include('company.settings.bank')
    </div>
    @endif

    @if(config('settings.beat')==1)
    <div class="tab-pane fade {{($active == 'beat')? 'active in':''}}" role="tabpanel" id="beats-detail" aria-labelledby="beats-tab">
      @include('company.settings.beat')
    </div>
    @endif

    @if(config('settings.party')==1)
    <div class="tab-pane fade {{($active == 'business-types')? 'active in':''}}" role="tabpanel" id="business-types" aria-labelledby="business-types-tab">
      @include('company.settings.business')
    </div>
    @endif

    <div class="tab-pane fade {{($active == 'custom-fields')? 'active in':''}}" role="tabpanel" id="custom-fields" aria-labelledby="custom-fields-tab">
      @include('company.settings.customfield')
    </div>

    @if(config('settings.expenses')==1)
    <div class="tab-pane fade {{($active == 'expense-types')? 'active in':''}}" role="tabpanel" id="expense-types" aria-labelledby="expense-types-tab">
      @include('company.settings.expensetype')
    </div>
    @endif

    @if(config('settings.leaves')==1)
    <div class="tab-pane fade {{($active == 'leave-types')? 'active in':''}}" role="tabpanel" id="leave-types" aria-labelledby="leave-tab">
      @include('company.settings.leave')
    </div>
    @endif

    @if(config('settings.visit_module')==1)
    <div class="tab-pane fade {{($active == 'visit-purpose')? 'active in':''}}" role="tabpanel" id="visit-purpose" aria-labelledby="visit-purpose">
      @include('company.settings.visitpurpose')
    </div>
    @endif

    @if(config('settings.party')==1)
    <div class="tab-pane fade {{($active == 'party-types')? 'active in':''}}" role="tabpanel" id="party-types" aria-labelledby="partytype-tab">
      @include('company.settings.partytypes')
    </div>
    @endif

    <div class="tab-pane fade {{($active == 'designation')? 'active in':''}}" role="tabpanel" id="designations-detail" aria-labelledby="designations-tab">
      @include('company.settings.designation')
    </div>

    <div class="tab-pane fade {{($active == 'roles')? 'active in':''}}" role="tabpanel" id="roles-detail" aria-labelledby="roles-tab">
      @include('company.settings.roles')
    </div>

    <div class="tab-pane fade {{($active == 'collaterals')? 'active in':''}}" role="tabpanel" id="collaterals-detail" aria-labelledby="collaterals-tab">
      @include('company.settings.collaterals')
    </div>
     <div class="tab-pane fade {{($active == 'parties-rate-setup')? 'active in':''}}" role="tabpanel"
      id="parties-rate-setup-detail" aria-labelledby="parties-rate-setup-tab">
      @include('company.settings.rate_setup.parties_rate_setup')
    </div>
    <div class="tab-pane fade {{($active == 'returnreasons')? 'active in':''}}" role="tabpanel" id="returnreasons-detail"
      aria-labelledby="returnreasons-tab">
      @include('company.settings.returns')
    </div>
  
  </div>
</div>