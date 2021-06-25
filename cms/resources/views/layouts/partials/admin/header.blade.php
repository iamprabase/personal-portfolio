<header class="main-header">

  <!-- Logo -->

  <a href="{{ route('app.home') }}" class="logo">

    <!-- mini logo for sidebar mini 50x50 pixels -->

    <span class="logo-mini"><img src="{{ config('settings.small_logo_path') }}"></span>

    <!-- logo for regular state and mobile devices -->

    <span class="logo-lg"><img src="{{ config('settings.logo_path') }}"></span>

  </a>

  <!-- Header Navbar: style can be found in header.less -->

  <nav class="navbar navbar-static-top">

    <!-- Sidebar toggle button-->

    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">

      <span class="sr-only">Toggle navigation</span>

      <span class="icon-bar"></span>

      <span class="icon-bar"></span>

      <span class="icon-bar"></span>

    </a>


    <div class="navbar-custom-menu">

      <ul class="nav navbar-nav">

        <!-- Notifications: style can be found in dropdown.less -->

        <li class="dropdown notifications-menu">

          <a href="#" class="dropdown-toggle" data-toggle="dropdown">

            <i class="fa fa-bell-o"></i>

            <span class="label label-warning">10</span>

          </a>

          <ul class="dropdown-menu">

            <li class="header">You have 10 notifications</li>

            <li>

              <!-- inner menu: contains the actual data -->

              <ul class="menu">

                <li>

                  <a href="#">

                    <i class="fa fa-users text-aqua"></i> 5 new members joined today

                  </a>

                </li>

              </ul>

            </li>

            <li class="footer"><a href="#">View all</a></li>

          </ul>

        </li>

        <li class="dropdown user user-menu">

          <a href="#" class="dropdown-toggle" data-toggle="dropdown">

            <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="user-image" alt="User Image">

            <span class="hidden-xs">{{ Auth::user()->name }}</span>

          </a>

          <ul class="dropdown-menu user-menu menu">

            <li style="border-bottom: 1px solid #f16022;border-top: 1px solid #f16022;">

              <a href="javascript:void(0)">

                <i class="menu-icon fa fa-user bg-red"></i>

                <div class="menu-info">

                  <h4 class="control-sidebar-subheading">Profile</h4>

                </div>

              </a>

            </li>
            <li style="border-bottom: 1px solid #f16022;border-top: 1px solid #f16022;">

              <a href="{{route('app.password.change')}}">

                <i class="menu-icon fa fa-gear bg-blue"></i>

                <div class="menu-info">

                  <h4 class="control-sidebar-subheading">Change Password</h4>

                </div>

              </a>

            </li>

            <li>

              <a href="{{ route('app.logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                <i class="menu-icon fa fa-sign-out bg-yellow"></i>

                <div class="menu-info">

                  <h4 class="control-sidebar-subheading">Sign Out</h4>

                </div>

              </a>

              <form id="logout-form" action="{{ route('app.logout') }}" method="POST"
                    style="display: none;">{{ csrf_field() }}</form>

            </li>

          </ul>


        </li>

      </ul>

    </div>

  </nav>

</header>