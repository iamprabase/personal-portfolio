@include('layouts/partials.front.header-inner')

<!-- START MAIL FUNCTION -->
<?php
session_start();
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];
    $message = $_POST['message'];

    $to = "info@deltatechnepal.com, sales@deltatechnepal.com";

    $subject = "DeltaSalesApp: Enquiry";

    $message .= "
			<p><b>Name : </b> {$name} </p>
			<p><b>Email : </b> {$email} </p>
			<p><b>Mobile No : </b> {$phone_no} </p>
		";
    $message .= "<p><b>Subject : </b>" . $_POST['subject'] . "</p>";

    $header = "From:{$email} \r\n";
    // $header .= "Cc:{$email} \r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";

    $retval = mail($to, $subject, $message, $header);

    if ($retval == true) {
        $_SESSION['success_message'] = "Message sent successfully!!";
    } else {
        $_SESSION['error_message'] = "Sorry, Please try again!!";
    }
} else {
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
}
?>
<!-- END OF MAIL FUNCTION -->

<div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h1 style="font-family: &quot;Montserrat&quot;,sans-serif;margin: 90px 0 30px;font-weight: bold;color: #fff;text-transform: uppercase;font-size: 36px;">
          Contact us </h1>
      </div>
    </div>
  </div>
</div>
<!-- banner end -->
<div class="listing-bg">
  <div class="container">
    <div class="row contact-bg-sec">
      <div class="col-sm-12 col-md-8 col-md-offset-2 for-small-size slideanim">
        <div class="contact-section-bg">
          @if(session()->has('success_message'))
            <div class="alert alert-success">
              {{session()->get('success_message')}}
            </div>
          @elseif(session()->has('error_message'))
            <div class="alert alert-danger">
              {{session()->get('error_message')}}
            </div>
          @endif

          <div class="row">
            <!-- <div class="col-sm-6"><a href="" class="call-us"><i class="fas fa-phone contact-"></i> <span> +21-531317/18 </span> </a></div>  -->
            <!-- <div class="col-sm-6"> <a href="" class="call-us"> <i class="fas fa-map-marker-alt  contact-"></i> Like on deltasales@app.com</a> </div>  -->
            <form action="{{route('contact_us')}}" method="post">
              {{ csrf_field() }}
              <div class="col-sm-6">
                <div class="form-group list-select ">
                  <label for="">Your Name* </label>
                  <input class="form-control input-area" placeholder="Your Name " type="text" name="name" required>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group list-select ">
                  <label for="">Email Address* </label>
                  <input class="form-control input-area" placeholder="Email Address " type="email" name="email"
                         required>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group list-select ">
                  <label for="">Mobile Number (<span class="email-add">Include Country Code</span>)* </label>
                  <input class="form-control input-area" placeholder="Mobile Number" type="text" name="phone_no"
                         required>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group list-select ">
                  <label for="">Subject*</label>
                  <input class="form-control input-area" placeholder="Subject" type="text" name="subject" required>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group list-select ">
                  <label for="">Your Message*</label>
                  <textarea class="form-control text-area" rows="5" placeholder="Your Message " name="message"
                            required></textarea>
                </div>
              </div>


              <div class="col-sm-12">
                <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }} list-select">
                  <label for="captcha">Captcha</label>
                  <div class="captcha" style="margin-bottom: 10px;">
                    <span>{!! captcha_img() !!}</span>
                    <button type="button" class="btn btn-success btn-refresh"><i class="fas fa-sync-alt"></i></button>
                  </div>
                  <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha"
                         autocomplete="off">
                  @if ($errors->has('captcha'))
                    <span class="help-block">
                                  <strong>{{ $errors->first('captcha') }}</strong>
                              </span>
                  @endif
                </div>
              </div>

              <div class="col-sm-12">
                <button type="submit" name="submit" value="submit" class="send">Send</button>
              </div>


            </form>
          </div>
        </div>
      </div>
      <!--<div class="col-sm-12 col-md-4 slideanim">
        <div class="map-sec">
            <h3> Like on Delta Sales App </h3>
            <img src="images/fb.png">
          </div>
        </div>-->
    </div>
  </div>
</div>
<!-- blog section end -->

@include('layouts/partials.front.footer')