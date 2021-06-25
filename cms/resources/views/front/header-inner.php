<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="author"
          content="Delta Sales App - GPS Tracking App | Sales Tracking Application | Nepal | A Product of Delta Tech Nepal">

    @if(Request::segment(1)=='feature')
    <title>Sales Performance Management and Evaluation Software | DeltaSalesApp - A solution to Grow your
        Company </title>
    <meta name="description"
          content=" Sales performance management software designed for sales manager and salesman with all in one dashboard to measure, evaluate, track and execute your sales and sales team to perform better. Features include real time tracking, maintain attendance, manage clients, manage orders, Salesmen reports..."/>
    <meta name="keywords"
          content="delta sales app features, features of sales tracking app in nepal, sales performance management software, sales management software, sales performance evaluation, manage sales team, execute sales team, tracking sales performance, salesmen app features, sales manager features"/>


    @elseif(Request::segment(1)=='pricing')
    <title>Best Sales performance Management App Pricing, Overview and Reviews | DeltaSalesApp - Sales Tracking
        Application at Affordable Rates</title>
    <meta name="description"
          content="All sales performance management features in one dashboard: real time gps tracking, travel distance calculator, mark attendance, manage clients, map client's location, manage enquiries, manage order, manage collection, leave application, add daily remark, manage salesmen expenses, assign tasks, assign sales target, announcements, manage products, maintain meeting records, sales employee reports, app works offline"/>
    <meta name="keywords"
          content="cheap sale app in nepal, price for sales tracking app in nepal, sales performance management software pricing, sales management software price, sales performance management software price,, sales management app price,  performance evaluation software price,  sales tracking software price"/>

    @elseif(Request::segment(1)=='request-demo')
    <title>Request a Demo | Delta Sales App - Sales Tracking App in Nepal</title>
    <meta name="description"
          content="Want to improve your communcation, save time and reduce cost. Request a Demo for Sales Tracking App - Delta Sales App. Request Delta Sales App demo for a free to manage sales people"/>
    <meta name="keywords"
          content="request demo for Sales tracking app in nepal, request for tracking app in nepal,best tracking software in nepal, demo for tracking app"/>
    @elseif(Request::segment(1)=='contact-us')
    <title>Contact | Delta Sales App - A Solution to Track your Salesmen in Nepal</title>
    <meta name="description"
          content="Want to increase efficiency of your salesmen and make your business, company grow. Use Delta Sales App - A Solution to Track your Salesmen in Nepal. Contact DeltaSalesApp @ +977-9802753996, info@deltatechnepal.com to manage your salesmen activities"/>
    <meta name="keywords"
          content="contact delta sales app, contact deltasalesapp, contact tracking application system"/>
    @elseif(Request::segment(1)=='blog')
    <title>Blogs | Delta Sales App - A Solution to Track your Salesmen in Nepal</title>
    <meta name="description"
          content="Get the latest blogs on Delta Sales App - track leads and sales, manage field sales employee, sales activities, gps sales tracking, tracking app in nepal"/>
    <meta name="keywords"
          content="software for lead management, tracking leads and sales application, measuring sales performance, how to grow business and company, managing sales activities software in nepal, field managing software in nepal, best sales tracking software, app and applicaion"/>
    @elseif(Request::segment(1)=='sales-tracking-app-in-nepal')
    <title>Sales Tracking App in Nepal | Delta Sales App - GPS Sales Tracking System</title>
    <meta name="description"
          content="DeltaSalesApp features most of the required parameters to tarck salesmen in nepal and it uses gps tracking system which makes it more complete in the field of tracking"/>
    <meta name="keywords"
          content="sales tracking app in nepal, android based salesmen tracking app, track gps location of employee, gps sales tracking system, gps employee tracking app"/>
    @elseif(Request::segment(1)=='lead-management-software-in-nepal')
    <title>Lead Management Software in Nepal | Delta Sales App - Track Leads and Sales in Nepal</title>
    <meta name="description"
          content="DeltaSalesApp is one of the best lead management software available in nepal that track leads and sales to improve communcation and business."/>
    <meta name="keywords"
          content="lead management software in nepal, track leads and sales in nepal, how to track lead and sales in nepal, track leads, tracks sales"/>

    @elseif(Request::segment(1)=='performance-measuring-software-in-nepal')
    <title>Performance Measuring Software in Nepal | Delta Sales App - Evaluate Sales Performance in Nepal
    </title>
    <meta name="description" content="Delta sales app is also useful for measuring sales performance of salesmen"/>
    <meta name="keywords"
          content="how to measure performance, evaluate sales performance in nepal, performance measuring software, performance tracking app"/>
    @elseif(Request::segment(1)=='field-sales-management-software')
    <title>Field Sales Management Software | Delta Sales App - Filed Sales Application in Nepal</title>
    <meta name="description"
          content="Field sales management software is used to tracking filed salesmen, employee or person of respective compnay in nepal."/>
    <meta name="keywords"
          content="field sales management software in nepal, field sales tracking application in nepal, filed sales application, filed sales software in nepal"/>
    @elseif(Request::segment(1)=='sales-management-software')
    <title>Sales Management Software - DeltaSalesApp</title>
    <meta name="description" content="Sales management software in nepal."/>
    <meta name="keywords" content="sales management app, sales management software in nepal, salesapp in nepal"/>
    @elseif(Request::segment(1)=='manage-order')
    <title>Manage Orders - DeltaSalesApp</title>
    <meta name="description" content="Manage order using Delta Sales App."/>
    <meta name="keywords" content="manage order, order management, order tracking app"/>
    @elseif(Request::segment(1)=='login')
    <title>Login | Delta Sales App - Know where your Salesmen Are </title>
    <meta name="description" content="Get started with gps sales tracking app in nepal"/>
    <meta name="keywords"
          content="login to delta sales app, how to use sales app, how to track salesmen in nepal, support for delta sales app"/>
    @elseif(Request::segment(1)=='forgot-password')
    <title>Forget Password | Delta Sales App - Better Way to Manage your Salesmen</title>
    <meta name="description" content="Reset your password with ease or contact at info@deltatechnepal.com"/>
    <meta name="keywords" content="forget password for sales app in nepal, reset pasasword for sales tracking app "/>
    @else
    <title>Salesmen Tracking and Management App with GPS | Delta Sales App - App that Manage your Salesmen</title>
    <meta name="description"
          content="Best sales tracking and management app in Nepal with real time GPS for Sales Manager and Salesman to easily track, measure and monitor Salesmen activity, assign tasks and target, know the routes and distance travelled by them, leads, expenses....."/>
    <meta name="keywords"
          content="deltasalesapp, delta sales app, field sales tracking app, sales management app, sales tracking system, sales gps tracking, salesman tracking software in nepal, salesmen tracking software in nepal, sales person tracking app, salesman tracking app, Salesmen tracking app in nepal, gps sales tracking app in nepal, gps tracking for sales rep, sales app in nepal, gps tracking in nepal, sales tracking app in nepal, salesmen tracking application, sales employee tracking software"/>
    @endid

    <link rel="shortcut icon" type="image/png" href="images/favicon.png"/>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
          integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">  -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link href="css/owl.carousel.css" rel="stylesheet">
    <link href="css/owl.theme.css" rel="stylesheet">
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-123214554-1"></script>
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

<div class="herder-sec" data-spy="affix" data-offset-top="200" data-offset-bottom="0">
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
                <div class="logo-bg"><a href="."> <img src="images/logo.png" alt=""> </a></div>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right navigation-sec">
                    <li><a href=".">Home</a></li>
                    <li><a href="{{ route('feature') }}">Features</a></li>
                    <li><a href="{{ route('pricing') }}">Pricing</a></li>
                    <li><a href="{{ route('request-demo') }}">Request Demo </a></li>
                    <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
                    <li><a href="{{ route('login') }}">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>