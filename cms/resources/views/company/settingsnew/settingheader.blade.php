<div class="col-xs-12">
  <a href="{{domain_route('company.admin.settingnew.setup')}}" class="btn btn-primary {{  (request()->is('admin/settingnew/setup') ) ? 'headerTab' : ''  }}" style="margin-left: 5px;">Setup</a>
  <a href="{{domain_route('company.admin.settingnew.customization')}}" class="btn btn-primary {{  (request()->is('admin/settingnew/customization') ) ? 'headerTab' : ''  }}" style="margin-left: 5px;">Customization</a>
  <a href="{{domain_route('company.admin.settingnew.userroles')}}" class="btn btn-primary {{  (request()->is('admin/settingnew/userroles') ) ? 'headerTab' : ''  }}" style="margin-left: 5px;">User Roles</a>
  @if(config('settings.party')==1)
  <a href="{{domain_route('company.admin.settingnew.customfields')}}" class="btn btn-primary {{  (request()->is('admin/settingnew/customfields') ) ? 'headerTab' : ''  }}" style="margin-left: 5px;">Custom Fields</a>
  @endif
  @if(Auth::user()->can('collateral-view') && config('settings.collaterals') == 1)
  <a href="{{domain_route('company.admin.settingnew.collaterals')}}" class="btn btn-primary {{  (request()->is('admin/settingnew/collaterals') ) ? 'headerTab' : ''  }}" style="margin-left: 5px;">Collaterals</a>
  @endif
  @if(config('settings.tally')==1)
  <a href="{{domain_route('company.admin.settingnew.integration')}}" class="btn btn-primary {{  (request()->is('admin/settingnew/collaterals') ) ? 'headerTab' : ''  }}" style="margin-left: 5px;">Integration</a>
  @endif
</div>