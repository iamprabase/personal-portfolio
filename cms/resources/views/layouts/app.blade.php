<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('settings.title') }} ::@yield('title')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewportsasdas">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <META NAME="robots" CONTENT="noindex,nofollow">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap/dist/css/bootstrap.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('assets/dist/css/AdminLTE.min.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('assets/dist/css/skins/_all-skins.min.css') }}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link rel="shortcut icon" href="favicon.ico"/>
  <link rel="shortcut icon" type="image/png" href="#"/>
  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  @yield('stylesheets')

  <style>
    .logo-lg {
      color: #f16022;
    }

    .logo-lg b {
      color: #017676;
    }

    .box.box-default, .box {
      border-top-color: #017676;
    }

    .skin-black-light .main-header, {
      border-bottom: 1px solid #f16022;
    }

    .skin-black-light .main-header .navbar > .sidebar-toggle {
      color: #f16022;
      border-right: 1px solid #f16022;
    }

    .skin-black-light .main-header {
      border-bottom: 1px solid #f16022;
    }

    .skin-black-light .main-header > .logo {
      background-color: #fff;
      color: #f16022;
      border-bottom: 0 solid transparent;
      border-right: 1px solid #f16022;
    }

    .skin-black-light .main-header .navbar .navbar-custom-menu .navbar-nav > li > a, .skin-black-light .main-header .navbar .navbar-right > li > a {
      border-left: 1px solid #f16022;
      border-right-width: 0;
    }

    /*.skin-black-light .main-header .navbar .nav>li>a {
        color: #f16022;
    }*/

    .skin-black-light .main-sidebar {
      border-right: 1px solid #f16022;
    }

    .sidebar-menu li > a > .fa-angle-left, .sidebar-menu li > a > .pull-right-container > .fa-angle-left {
      color: #f16022;
    }

    .skin-black-light .sidebar-menu > li.header {
      color: #fff;
      background: #f16022;
    }

    .sidebar-menu li.header {
      padding: 5px 25px;
      font-size: 18px;
    }

    .content-header > .breadcrumb > li > a {
      color: #f16022;
    }

    .breadcrumb > .active {
      color: #017676;
    }

    .content-header > .breadcrumb > li + li:before {
      color: #f16022;
    }

    .content-header {
      position: relative;
      padding: 40px 15px 0 15px;
    }

    .btn-primary {
      background-color: #017676;
      border-color: #017676;
    }

    .btn-default {
      background-color: #f16022;
      color: #fff;
      border-color: #ddd;
    }

    .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover, .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover {
      z-index: 3;
      color: #fff;
      cursor: default;
      background-color: #017676;
      border-color: #017676;
    }

    .navbar-nav > .user-menu > .dropdown-menu > .user-footer .btn-default {
      color: #fff;
    }

    .skin-black-light .main-header li.user-header {
      background-color: #f16022;
    }

    .dropdown-menu {
      box-shadow: none;
      border-color: #f16022;
    }

    .user-menu .menu-icon {
      float: left;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      text-align: center;
      line-height: 20px;
      margin-top: 8px;
    }

    .user-menu .menu-info {
      margin-left: 30px;
      margin-top: 0px;
    }

    .navbar-nav > .user-menu > .dropdown-menu {
      border-top-right-radius: 0;
      border-top-left-radius: 0;
      padding: 0px 0 0 0;
      border-top-width: 0;
      width: 160px;
    }

  </style>
</head>
<body class="hold-transition skin-black-light sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  @include('layouts/partials.admin.header')
  @include('layouts/partials.admin.sidebar')

  <div class="content-wrapper">


    @yield('content')

  </div>

@include('layouts/partials.admin.footer')



<!-- jQuery 3 -->
  <script src="{{ asset('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="{{ asset('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
  <!-- SlimScroll -->
  <script src="{{ asset('assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
  <!-- FastClick -->
  <script src="{{ asset('assets/bower_components/fastclick/lib/fastclick.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="{{ asset('assets/dist/js/demo.js') }}"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDR6v2elDctrDptLyvTjpTBEs6z7CLSfW8&region=NP&callback=initMap">
  </script>
  <script>
      $(document).ready(function () {
          $('.sidebar-menu').tree()
      });
  </script>
@yield('scripts')
</body>
</html>
