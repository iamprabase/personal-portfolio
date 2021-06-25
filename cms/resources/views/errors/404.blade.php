<!DOCTYPE html>

<html>

<head>

  <meta charset="utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>Something Went Wrong</title>

  <!-- Tell the browser to be responsive to screen width -->

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Bootstrap 3.3.7 -->

  <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">

  <!-- Font Awesome -->

  <link rel="stylesheet" href="{{ asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}">

  <!-- Ionicons -->

  <link rel="stylesheet" href="{{ asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}">

  <!-- Theme style -->

  <link rel="stylesheet" href="{{ asset('assets/dist/css/AdminLTE.min.css') }}">


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->

  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

  <!--[if lt IE 9]>

  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>

  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

  <![endif]-->


  <!-- Google Font -->

  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">


  <style>
    body {
      display: block;
      margin: 0px;
    }

    #notfound {
      position: relative;
      height: 100vh;
    }

    #notfound .notfound-bg {
      position: absolute;
      width: 100%;
      height: 100%;
      background-image: url({{ asset('assets/dist/img/bg.jpg') }});
      background-size: cover;
    }

    #notfound .notfound-bg:after {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background-color: rgba(9, 140, 84, 0.7);
    }

    #notfound .notfound {
      position: absolute;
      left: 50%;
      top: 50%;
      -webkit-transform: translate(-50%, -50%);
      -ms-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
    }

    .notfound {
      max-width: 910px;
      width: 100%;
      line-height: 1.4;
      text-align: center;
    }

    .notfound .notfound-404 {
      position: relative;
      height: 200px;
    }

    .notfound .notfound-404 h1 {
      font-family: 'Montserrat', sans-serif;
      position: absolute;
      left: 50%;
      top: 50%;
      -webkit-transform: translate(-50%, -50%);
      -ms-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
      font-size: 220px;
      font-weight: 900;
      margin: 0px;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 10px;
    }

    .notfound h2 {
      font-family: 'Montserrat', sans-serif;
      font-size: 22px;
      font-weight: 700;
      /*text-transform: uppercase;*/
      color: #fff;
      margin-top: 20px;
      margin-bottom: 15px;
    }

    .notfound .home-btn, .notfound .contact-btn {
      font-family: 'Montserrat', sans-serif;
      display: inline-block;
      font-weight: 700;
      text-decoration: none;
      background-color: transparent;
      border: 2px solid transparent;
      text-transform: uppercase;
      padding: 13px 25px;
      font-size: 18px;
      border-radius: 40px;
      margin: 7px;
      -webkit-transition: 0.2s all;
      transition: 0.2s all;
    }

    .notfound .home-btn:hover, .notfound .contact-btn:hover {
      opacity: 0.9;
    }

    .notfound .home-btn {
      color: rgba(255, 0, 36, 0.7);
      background: #fff;
    }

    .notfound .contact-btn {
      border: 2px solid rgba(255, 255, 255, 0.9);
      color: rgba(255, 255, 255, 0.9);
    }

    .notfound-social {
      margin-top: 25px;
    }

    .notfound-social > a {
      display: inline-block;
      height: 40px;
      line-height: 40px;
      width: 40px;
      font-size: 14px;
      color: rgba(255, 255, 255, 0.9);
      margin: 0px 6px;
      -webkit-transition: 0.2s all;
      transition: 0.2s all;
    }

    .notfound-social > a:hover {
      color: rgba(255, 0, 36, 0.7);
      background-color: #fff;
      border-radius: 50%;
    }</style>
</head>

<body>

<div id="notfound">
  <div class="notfound-bg"></div>
  <div class="notfound">
    <div class="notfound-404">
      <h1><img src="{{ asset('assets/dist/img/error.png') }}" alt="" style="width: 200px;"></h1>
    </div>
    <h2><!--<i class="fa fa-warning text-yellow"></i>-->
    @if($exception->getMessage()) 
      {{ $exception->getMessage() }}
    @else
      We could not find the page you were looking for.
    @endif
  </h2>
    <p></p>
    <!-- <a href="#" class="home-btn">Go Home</a> -->
    <!--<a href="#" class="contact-btn">report it here</a>-->
  </div>
</div>

</body>

</html>

