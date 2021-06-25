<div class="col-xs-9 setting-content">
  <div class="tab-content" id="myTabContent">
    
    <div class="tab-pane fade {{($active == 'tally')? 'active in':''}}" role="tabpanel" id="tally" aria-labelledby="tally">
      @include('company.settingsnew.integration._tally')
    </div>
    
    <div class="tab-pane fade {{($active == 'quickbook')? 'active in':''}}" role="tabpanel" id="quickbook" aria-labelledby="quickbook">
      @include('company.settingsnew.integration._quickbook')
    </div>
      

  </div>
</div>