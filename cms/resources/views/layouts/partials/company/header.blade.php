<header class="main-header">
    @if(Auth::user()->managers->first())
      @php $daysPending = getCompanyPendingDays(config('settings.company_id')) @endphp
      @if($daysPending['in_range'] && Auth::user()->managers->first()->is_owner==1)
      <p class="alert alert-warning stsAlertEnd text-center">
        @if($daysPending['num_days']>1)
          <b>Hi! Your subscription is about to expire in {{$daysPending['num_days']}} days. To continue using Delta Sales App
            after {{$daysPending["end_date"]}}, kindly make payment.</b>
        @elseif($daysPending['num_days']==0)
          <b>Hi, Your subscription is expiring today. To continue using Delta Sales App, kindly make payment.</b>
        @elseif($daysPending['num_days']==1)
          <b>Hi, Your subscription is expiring tomorrow. To continue using Delta Sales App, kindly make payment.</b>
        @endif
      </p>
      @elseif(getCompanySubscriptionDate(config('settings.company_id')) && Auth::user()->managers->first()->is_owner==1)
      <p class="alert alert-warning stsAlertEnd text-center">
        <b>Hi! Your subscription has ended. Please make payment.</b>
      </p>
      @endif
    @endif
    <a href="{{ domain_route('company.admin.home') }}" class="logo" style="background-color: #ecf0f5;">
        <span class="logo-mini">
            @if(config('settings.small_logo_path') && file_exists(URL::asset('cms'.config('settings.small_logo_path'))))
            <img src="{{ URL::asset('cms'.config('settings.small_logo_path')) }}"
                style="height: -webkit-fill-available;">
            @endif

        </span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg" style="color:#0b7676;">
            @if(config('settings.logo_path') && file_exists(URL::asset('cms'.config('settings.logo_path'))))
            <img src="{{ URL::asset('cms'.config('settings.logo_path')) }}" style="height: -webkit-fill-available;">
            @else
            {{ ucfirst(config('settings.title')) }}
            @endif</span>
    </a>

    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">

            <span class="sr-only">Toggle navigation</span>

        </a>


        {{-- <div class="search-bar" style="display:inline;float:right;">
          <div class="master-cross" style="display: inline-block;padding-left: 10px;">
            <i class="fa fa-search"></i>
          </div>
          <div class="mas">
            <i class="fa fa-close"></i>
          </div>
          <div class="loader">
              <img src="{{ asset('assets/dist/img/loader.gif')}}" height="40px">
          </div>
          <input type="text" name="search" id="search" placeholder="" size="50px" class="master-search" autocomplete="off">
          <div id="search-list" class="dropdown-menu" style="margin-left: 360px;position: absolute;">
        </div> --}}

        <div class="search-bar" style="display:inline;float:right;">
          <div class="master-cross" style="display: inline-block;padding-left: 10px;">
            <i class="fa fa-search"></i>
          </div>
          <input type="text" name="search" id="search" placeholder="" size="50px" class="master-search" autocomplete="off">
          <div class="mas">
            <i class="fa fa-close"></i>
          </div>
          <div class="loader">
              <img src="{{ asset('assets/dist/img/loader.gif')}}" height="40px">
          </div>
          <div id="search-list" class="dropdown-menu" style="position: absolute;left: unset!important;">
        </div>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
            <!-- <li>
                    <a href="#">💰 Earn Rewards
                    <span class="label label-danger" style="top: 11px; right:-5px">new</span>                    
                    </a>

                </li> -->

                <!-- <li>
                    <a href="#"
                        onclick="window.open('https://tawk.to/chat/5ba1fa69c9abba579677b00e/default','MyWindow','width=450,height=600');">{{-- <i class="fa fa-phone"></i> --}}
                        &nbsp;<i class="fa fa-question fa-lg"></i></a>

                </li> -->
                <li>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-plus"></i>
                    </a>
                    <ul class="dropdown-menu user-menu menu control-sidebar-menu">

                        <li>
                            @if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin() || Auth::user()->isLowestDesignation())
                            @if(Auth::user()->can('employee-create'))
                            <a href="{{domain_route('company.admin.employee.create')}}">
                                <i class="menu-icon fa fa-user emp-icon employee-bg"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Employee</h4>
                                </div>
                            </a>
                            @endif
                            @endif
                            @if(Auth::user()->can('product-create') && config('settings.product')==1)
                            <a href="{{domain_route('company.admin.product.create')}}">
                                <i class="menu-icon fa fa-th-large emp-icon product-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Product</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->can('party-create') && config('settings.party')==1)
                            <a href="{{domain_route('company.admin.client.create')}}">
                                <i class="menu-icon fa fa-user-secret emp-icon parties-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Party</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->can('activity-create') && config('settings.activities')==1)
                            <a href="{{domain_route('company.admin.activities.create')}}">
                                <i class="menu-icon fa fa-file emp-icon tasks-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Activity</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->can('order-create')  && config('settings.orders')==1)
                            <a href="{{domain_route('company.admin.order.create')}}">
                                <i class="menu-icon fa fa-cart-plus emp-icon order-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Order</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->can('zeroorder-create') && config('settings.zero_orders')==1)
                              <a href="{{domain_route('company.admin.zeroorder.create')}}">
                                <i class="menu-icon fa fa-cart-plus emp-icon order-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Zero Order</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->can('collection-create')  && config('settings.collections')==1)
                            <a href="{{domain_route('company.admin.collection.create')}}">
                                <i class="menu-icon fa fa-money emp-icon collection-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Collection</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->can('expense-create')  && config('settings.expenses')==1)
                            <a href="{{domain_route('company.admin.expense.create')}}">
                                <i class="menu-icon fa fa-delicious emp-icon expenses-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Expense</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin())
                            @if(Auth::user()->can('announcement-create')  && config('settings.announcement')==1)
                            <a href="{{domain_route('company.admin.announcement.create')}}">
                                <i class="menu-icon fa fa-volume-up emp-icon announcements-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Announcement</h4>
                                </div>
                            </a>
                            @endif
                            @endif
                            @if(Auth::user()->can('leave-create')  && config('settings.leaves')==1)
                            <a href="{{domain_route('company.admin.leave.create')}}">
                                <i class="menu-icon fa fa-tag emp-icon tasks-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Leave</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->can('tourplan-create') && config('settings.tour_plans')==1)
                              <a href="{{domain_route('company.admin.tours')}}">
                                <i class="menu-icon fa fa-location-arrow emp-icon announcements-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Tour Plans</h4>
                                </div>
                            </a>
                            @endif
                            @if(Auth::user()->can('dayremark-create') && config('settings.remarks')==1)
                              <a href="{{domain_route('company.admin.dayremarks')}}">
                                <i class="menu-icon fa fa-book emp-icon order-bgs"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Add Day Remarks</h4>
                                </div>
                            </a>
                            @endif
                        </li>
                    </ul>
                </li>
                @if(Auth::user()->isCompanyManager())
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">
                            <span class="rnoti" id="n_count_1" data-notificationcounter="0">0</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">
                            You have<span class='rnoti' id="n_count_2">0</span>notifications
                        </li>
                        <li>
                            <ul class="menu" id="notification_list">

                            </ul>
                        </li>

                        <li class="footer"><a href="{{ domain_route('company.admin.notification') }}">See All</a></li>
                    </ul>
                </li>
                @endif
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        @if(file_exists(URL::asset('/storage/'.Auth::user()->profile_imagePath))){
                        <img src="{{ URL::asset('/storage/'.Auth::user()->profile_imagePath) }}" class="user-image"
                            alt="User Image">
                        @else
                        <img src="{{ asset('assets/dist/img/admin-picture.png') }}" class="user-image" alt="User Image">
                        @endif

                        <span class="hidden-xs">{{{ Auth::user()->EmployeeName() }}}</span>
                    </a>
                    <ul class="dropdown-menu user-menu menu control-sidebar-menu">

                        <li>
                            <a href="{{domain_route('company.admin.setting.updateuserprofile')}}">
                                <i class="menu-icon fa fa-user  bg-yellow"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Update Profile</h4>
                                </div>
                            </a>
                           <!--  @if(Auth::user()->isCompanyManager() || Auth::user()->isCompanyAdmin())
                            <a href="{{domain_route('company.admin.setting')}}">
                                <i class="menu-icon fa fa-cogs bg-yellow"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Settings</h4>
                                </div>
                            </a>
                            @endif -->
                             @if(Auth::user()->employee->is_owner == 1 || Auth::user()->can('settings-view'))
                            <a href="{{domain_route('company.admin.settingnew.setup')}}">
                                <i class="menu-icon fa fa-cogs bg-yellow"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Settings</h4>
                                </div>
                            </a>
                            @endif
                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="menu-icon fa fa-sign-out bg-yellow"></i>
                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Sign Out</h4>
                                </div>
                            </a>
                            <form id="logout-form" action="{{ domain_route('company.logout') }}" method="POST"
                                style="display: none;">{{ csrf_field() }}</form>
                        </li>
                    </ul>

                </li>

            </ul>
        </div>

    </nav>
</header>

<div id="notification" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="notititle">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p id="notidesc">Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>