<html>
<head></head>
<body style="background-color: #dddddd;">
<center>
  <div
      style="width: 550px; background-color: rgba(9, 140, 84, 0.5);color: white; border: 1px solid black; border-radius: 5px;">
    <img src="https://www.deltasalesapp.com/images/logo.png" style="max-width: 50%, padding-top: 20px;">
    <h1>Password Changed</h1>
    <p>Password has been changed to <b>{{$token}} for following account details.</b></p><br>
    @if(isset($company))<p>company: {{$company->company_name}}</p><br>@endif
    @if(isset($user))
    @if(isset($user->email))<p>email: {{$user->email}}</p><br> @endif
    @if(isset($user->phone))<p>phone: {{$user->phone}}</p><br> @endif
    @endif
    <p><strong><a href="https://deltasalesapp.com/" target="_blank">DeltaSalesApp</a>.</strong></p>
  </div>
</center>
</body>
</html>