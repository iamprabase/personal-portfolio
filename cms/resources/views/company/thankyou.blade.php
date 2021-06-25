<html>
<head></head>
<body style="background-color: #dddddd;">
<center>
  <div style="width: 550px; background-color: rgba(9, 140, 84, 0.5);color: white; border: 1px solid black; border-radius: 5px;">
    <img src="https://www.deltasalesapp.com/images/logo.png" style="max-width: 50%, padding-top: 20px;">
    <h1>Thank You</h1>
    <p style="text-align: justify; padding: 10px 20px;">Your email has been verified for the url: "https://{{$domain}}.{{config('app.domain')}}". You may now proceed to login using the email and password given below.</p>
    <p style="text-align: justify; padding: 10px 20px;">
      Email: {{$email}} <br>
      Password: {{$password}}
    </p>
    <p style="text-align: justify; padding: 10px 20px;">To login, please <a
          href="https://{{$domain}}.{{config('app.domain')}}" target="_blank" style="color: rgb(173, 225, 255);">click
        here</a></p>
  </div>
</center>
</body>
</html>