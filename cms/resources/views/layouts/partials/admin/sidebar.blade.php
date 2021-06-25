<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
      <li>
        <a href="{{ route('app.home') }}">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>
      <!--<li class="treeview">-->
      <!--  <a href="#">-->
      <!--    <i class="fa fa-dashboard"></i> <span>User Management</span>-->
      <!--    <span class="pull-right-container">-->
      <!--      <i class="fa fa-angle-left pull-right"></i>-->
      <!--    </span>-->
      <!--  </a>-->
      <!--  <ul class="treeview-menu">-->
      <!--    <li><a href=""><i class="fa fa-circle-o"></i> Employee</a></li>-->
      <!--    <li><a href=""><i class="fa fa-circle-o"></i> Roles</a></li>-->
      <!--  </ul>-->
      <!--</li>-->
      <li>
        <a href="{{ route('app.company') }}">
          <i class="fa fa-cart-plus"></i> <span>Company Management</span>
        </a>
      </li>

      <li>
        <a href="{{ route('app.outlets') }}">
          <i class="fa fa-cart-plus"></i> <span>Outlets</span>
        </a>
      </li>

      <li>
        <a href="{{ route('app.companyusagenew') }}">
          <i class="glyphicon glyphicon-dashboard"></i> <span>Company Usage</span>
        </a>
      </li>

      <li class="treeview">
        <a href="#">
          <i class="fa fa-dashboard"></i> <span>Settings</span>
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ route('app.plan') }}"><i class="fa fa-circle-o"></i> Plans</a></li>
          <li><a href="{{ route('app.setting') }}"><i class="fa fa-circle-o"></i> General Settings</a></li>
        </ul>
      </li>

      <!-- <li>
        <a href="{{ route('app.custom-plan.index') }}">
          <i class="glyphicon glyphicon-dashboard"></i> <span>Custom Plan</span>
        </a>
      </li>

      <li>
        <a href="{{ route('app.subscription.index') }}">
          <i class="glyphicon glyphicon-dashboard"></i> <span>Subscriptions</span>
        </a>
      </li> -->

    </ul>
  </section>
  <!-- /.sidebar -->
</aside>