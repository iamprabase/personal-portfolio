<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('settings.title') }}::@yield('title')s</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=3, user-scalable=yes" name="viewport">
  <META NAME="robots" CONTENT="noindex,nofollow">
  <link rel="stylesheet" href="{{ asset('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/dist/css/AdminLTE.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/square/blue.css') }}">
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link
  rel="stylesheet"
  href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"
/>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');
  html {
    font-size: 62.5%; 
  }
  @media (max-width: 1199.98px) {
    html {
      font-size: 56.25%;
    }
  }
  @media (max-width: 991.98px) {
    html {
      font-size: 50%;
    }
  }
  .bg-img {
    background-color: #009688;
  }
  .custom-register-section {
    font-family: 'Poppins', sans-serif; 
    padding: 6rem 0;      
  }
  @media (min-width: 992px) {
    body {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .bg-img {
      background-image: url( "{{asset('assets/dist/img/bg.svg')}}");
      min-height: 380px;
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;           
    }
    .custom-register-section {
      padding: 0;     
    }
    .overlay {
      position: relative;
      z-index: 0;
    }
    .overlay::before {
      background-color: rgba(0,0,0,0.4);
    }
    .overlay::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }
  }

  @media (max-width: 991.98px) {
    .custom-register-section {
      position: relative;    
      z-index: 0;          
    }
    .custom-register-section::before {
      background-color: rgba(0,0,0,0.4);
    }
    .custom-register-section::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }
  }
  .custom-register-section .invoice {
    margin: 0;
    padding: 0;
    border: 0;
    background-clip: border-box;
    border-radius: 7px;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
  }
  .col-centered {
    float: none;
    margin: 0 auto;
  }      
  .custom-register-section .d-flex-bgi-size {
    display: flex;
    -webkit-flex: 1 1 auto;
    flex: 1 1 auto;
    min-width: 0;
    background-size: contain;
    background-position-x: center;
    background-position-y: bottom;
    background-repeat: no-repeat;
    min-height: 350px;
    margin-top: 4.2rem;
  }
  @media(min-width: 992px) {
    .custom-register-section .d-lg-flex {
        display: flex;
    }        
  }
  .row.no-gutters {
    margin-right: 0;
    margin-left: 0;
  }

  .invoice-left {
    background: #defffd;
    padding: 1.6rem 2.4rem;
    border-top-left-radius: 7px;
    border-bottom-left-radius: 7px;
  }      
  @media (max-width: 991.98px) {
    .invoice-left {
      border-top-right-radius: 7px;
      border-bottom-left-radius: 0;
    }
  }
  .invoice-left p {        
    font-size: 2rem;
    line-height: 3.3rem;
    margin-top: 2.7rem;
    margin-bottom: 1.1rem;
  }

  .invoice-left img,
  .invoice-right img {
      display: block;
      max-width: 200px;
      height: auto;
      margin: 2.7rem auto 0;
  }

  .invoice-right img {
      max-width: 70%;          
  }      

  .invoice-right h3 {
      font-size: 2.6rem;
      font-weight: 600;
      margin-bottom: 1.7rem;
      text-align: center;
  } 

  .invoice-right h1 {
      font-size: 3.4rem;
      font-weight: 500;
      margin-bottom: 1.7rem;
      text-align: center;
  }      

  .custom-register-section .invoice .form-control {
    padding: .8rem 11.6rem6px;
    border-radius: 4px;
    height: calc(1.6em + 1.5rem + 2px);
  }

  .custom-register-section .invoice .form-control.invalid {
      border-color: #dc3545;
  }
  .custom-register-section .invoice .form-control:invalid ~ .invalid-feedback {
      display: block;
  }
  .invalid-feedback {
      display: block;
      width: 100%;
      margin-top: 7px;
      font-size: 1.3rem;
      color: #dc3545;
  }

  .valid-feedback {
      display: block;
      width: 100%;
      margin-top: 7px;
      font-size: 1.3rem;
      color: #2c9b07;
  }

  .custom-register-section .invoice .invoice-right {
      padding: 1.6rem 6.5rem;
      align-self: center;
  }
  @media (max-width: 575.98px) {
    .custom-register-section .invoice .invoice-right {
      padding: 1.6rem;
    }
  }
  .invoice-right .icheckbox_flat-blue, .iradio_flat-blue {
      margin-right: 8px;
  }
  .d-flex-custom {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
  }      
  .custom-register-section .btn-primary {
    padding: .8rem 1.6rem;
    background-color: #079292;
    border-color: #079292;
    margin-bottom: 2.7rem;
  }       
  .btn-primary:active:focus, 
  .btn-primary:hover {
    background-color: #009688;
    border-color: #009688;
  }
  .alert{
    padding:3px;
  }
</style>
  @yield('stylesheets')
</head>
<body class="hold-transition login-page bg-img overlay">

    
@yield('content')


<script src="{{ asset('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>

<script src="{{ asset('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });

        
    });
</script>
@yield('scripts')
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5ba1fa69c9abba579677b00e/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
</body>
</html>
