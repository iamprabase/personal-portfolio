<div class="col-xs-9 setting-content">
  <div class="tab-content" id="myTabContent">
    
    <div class="tab-pane fade {{($active == 'profile')? 'active in':''}}" role="tabpanel" id="company"
         aria-labelledby="compamy">
      @include('company.settingsnew.setup.profile')
    </div>

    <div class="tab-pane fade" role="tabpanel" id="location" aria-labelledby="location-tab">
    </div>

    <div class="tab-pane fade {{($active == 'layout')? 'active in':''}}" role="tabpanel" id="admin"
         aria-labelledby="admin-tab">
      @include('company.settingsnew.setup.admin_layout')
    </div>

    <div class="tab-pane fade {{($active == 'other')? 'active in':''}}" role="tabpanel" id="setup"
         aria-labelledby="setup-tab">
      @include('company.settingsnew.setup.setup')
    </div>

    <div class="tab-pane fade {{($active == 'order')? 'active in':''}}" role="tabpanel" id="ordersetup"
         aria-labelledby="ordersetup-tab">
        <ul class="nav nav-pills" id="tabs">
          <li class="active"><a href="#general-setup" data-toggle="tab">General Setup</a></li>
          @if(config('settings.product')==1 && config('settings.order_with_amt') == 0)
          <li><a href="#tax-setup" data-toggle="tab">Tax Setup</a></li>
          @endif
        </ul>
          
          @include('company.settingsnew.setup.ordersetup')
          
          <!-- /.nav-tabs-custom -->
    </div>

    <div class="tab-pane fade {{($active == 'partyvisit')? 'active in':''}}" role="tabpanel" id="partyvisit"
         aria-labelledby="partyvisit-tab">
      @include('company.settingsnew.setup.partyvisitsetup')
    </div>

    <div class="tab-pane fade {{($active == 'odometerreport')? 'active in':''}}" role="tabpanel" id="odometer_reportsetup"
         aria-labelledby="odometer_reportsetup-tab">
      @include('company.settingsnew.setup.odometersetup')
    </div>

    <div class="tab-pane fade {{($active == 'plan')? 'active in':''}}" role="tabpanel" id="plan-detail" aria-labelledby="plan-detail-tab">
      @include('company.settingsnew.setup.plan')
    </div>
  </div>
</div>