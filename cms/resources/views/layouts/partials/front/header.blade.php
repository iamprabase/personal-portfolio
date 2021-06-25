<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <link rel="alternate" href="http://deltasalesapp.com/" hreflang="en_np"/>
  <meta name="author" content="DeltaSalesApp - GPS Sales Tracking App | A Product of Delta Tech Nepal | Nepal">
  <meta name="msvalidate.01" content="A39FC71E08414D8330F859FE56EF8103"/>
  <META NAME="robots" CONTENT="noindex,nofollow">

  @if(Request::segment(1)=='')
    <title>Sales Tracking and Management Application with GPS | Delta Sales Rep Tracking Software</title>
    <meta name="description"
          content="Best sales / salesman tracking and management application in Nepal. Delta Sales app - Employee / Sales Rep Tracking Software is an Android based salesmen tracking system with real time GPS tracking, maintain attendance, manage sales expense, measure sales performance, leave application, tasks assignment, manage clients, mark client location, manage enquiries, manage collections, manage orders, works offline, nepal, india"/>
    <meta name="keywords"
          content="delta sales app, sales tracking app nepal, salesman tracking application, sales rep tracking app, sales tracking software, field sales management software nepal, mobile sales app, salesmen tracking app nepal, gps tracking for sales reps, sales management app, salesman tracking software nepal, salesperson tracking app, sales crm nepal, field force sales tracker, sales track, sales crm, gps sales tracking app nepal, field sales tracking app, gps tracking in nepal, sales app in nepal, salesmen tracking software india, sales management app,sales employee tracking application, Sales representative location tracker india"/>

  @endif

  <link rel="canonical" href="http://deltasalesapp.com/">
  <meta property="og:locale" content="en_US"/>
  <meta property="og:type" content="website"/>
  <meta property="og:title" content="Sales Employee Tracking Application with GPS | Delta Sales Rep Tracking Software"/>
  <meta property="og:description"
        content="Delta Sales app - Employee / Sales Rep Tracking Software is an Android based salesmen tracking system with real time GPS tracking, maintain attendance, manage sales expense, measure sales performance, leave application, tasks assignment, manage clients, mark client location, manage enquiries, manage collections, announcements, manage products, manage orders, add daily remarks, works offline"/>
  <meta property="og:keywords"
        content="delta sales app, sales tracking app nepal, salesman tracking application, sales rep tracking app, sales tracking software, field sales management software nepal, mobile sales app, salesmen tracking app nepal, gps tracking for sales reps, sales management app, salesman tracking software nepal, salesperson tracking app, sales crm nepal, field force sales tracker, sales track, sales crm, gps sales tracking app nepal, field sales tracking app, gps tracking in nepal, sales app in nepal, salesmen tracking software india, sales management app,sales employee tracking application, Sales representative location tracker, nepal, india"/>


  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/front/images/favicon.png') }}"/>
  <!-- Bootstrap -->
  <link href="{{ asset('assets/front/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/front/css/style.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
        integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
  <!-- <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">  -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
  <link href="{{ asset('assets/front/css/owl.carousel.css')}}" rel="stylesheet">
  <link href="{{ asset('assets/front/css/owl.theme.css') }}" rel="stylesheet">
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-123214554-1"></script>

  <style>
    .banner h1 {
      font-family: "Montserrat", sans-serif;
      margin: 90px 0 30px;
      font-weight: bold;
      color: #fff;
      text-transform: uppercase;
      font-size: 36px;
    }
  </style>

  <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
          dataLayer.push(arguments);
      }

      gtag('js', new Date());

      gtag('config', 'UA-123214554-1');
  </script>
</head>
<body>
<!-- banner start -->

<div class="herder-sec" data-spy="affix" data-offset-top="200">
  <nav class="navbar navbar-default nav-sec">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <div class="logo-bg"><a href="."> <img src="{{ asset('assets/front/images/logo.png') }}"
                                               alt="deltasalesapp-logo" title="Delta Sales App"></a></div>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right navigation-sec">
          <li><a href=".">Home</a></li>
          <li><a href="{{ route('feature') }}">Features</a></li>
          <li><a href="{{ route('pricing') }}">Pricing</a></li>
          <li><a href="{{ route('request-demo') }}">Request Demo </a></li>
          <!--     <li><a href="blog.php">Blog</a></li>  -->
          <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
          <li><a href="{{ route('login') }}">Login</a></li>
        </ul>
      </div>
    </div>
  </nav>
</div>
<!--<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="col-lg-12">
            <div class="form-group list-select ">
              <input class="form-control input-area" placeholder="Email " type="text">
            </div>
          </div>
          <div class="col-lg-12">
            <div class="form-group list-select ">
              <input class="form-control input-area" placeholder="Password" type="password">
            </div>
          </div>
          <div class="col-lg-6">
            <div class="checkbox checkme loginChe">
               <input class="input-assumpte" id="1" type="checkbox">
               <label for="">Stay signed in</label>
              <a href="forgot-password.php" class="forget">Forgot Password</a>
            </div>
          </div>
          <div class="col-lg-6"> <span class="no-ac">No Account ? <a href="request-demo.php">Sign Up</a></span> </div>
          <div class="col-lg-12">
          <button type="submit" class="send">SEND</button>
          </div>
         <div class="col-lg-12">
          <section class="seperator text-center or-sec"><span> OR </span></section>
          <h4 class="signwith">Sign In with</h4>
          </div>
          <div class="col-lg-12 login-for-social">
            <a href="" class="googlelogin"> <i class="icon ion-social-googleplus-outline"></i> Googleplus</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>-->
<div class="banner">
  <div class="container ">
    <div class="row">
      <div class="col-lg-12 col-md-10 col-md-offset-1">
        <h1 class="slideanim reveal">Android Based Salesmen Tracking Application</h1>
        <h2 class="slideanim reveal"
            style="margin: 0px;font-size: 25px;/* padding-bottom: 12px; */font-family: inherit;text-transform:  unset;margin-bottom:  20px;">
          Manage your salesmen the smarter way & make your company grow !</h2>
        <p class="slideanim reveal">DeltaSalesApp is a solution for tracking field sales employees and managing sales
          for industries like FMCG, Field service, Automobiles, Pharmaceuticals, Footwear, Garments & many more.
        <p>
          <!-- <h3 class="org">Get Organized. Keep Track. Save more. Sell more.</h3> -->
          <a href="{{ route('request-demo') }}" class="account slideanim">Sales App Demo</a>
          <a href="javascript:void(Tawk_API.toggle())" class="account download slideanim">Buy Now</a>
      </div>
    </div>
  </div>
</div>
<!-- banner end -->