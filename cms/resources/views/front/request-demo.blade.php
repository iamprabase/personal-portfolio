@include('layouts/partials.front.header-inner')
<?php



session_start();
if (isset($_POST['submit'])) {

    if (isset($_POST['name'])) {
        $name = $_POST['name'];
    } else {
        $name = "";
    }

    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];

    if (isset($_POST['skype_id'])) {
        $skype_id = $_POST['skype_id'];
    } else {
        $skype_id = "";
    }

    $to = "info@deltatechnepal.com, sales@deltatechnepal.com";
    $subject = "DeltaSales App : Demo Request";

    $message = "
			<p><b>Name : </b> {$name} </p>
			<p><b>Company Name : </b> {$company_name} </p>
			<p><b>Email : </b> {$email} </p>
			<p><b>Mobile No : </b> {$phone_no} </p>
			<p><b>Skype Id : </b> {$skype_id} </p>
		";

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
          Request Demo </h1>
      </div>
    </div>
  </div>
</div>
<!-- banner end -->
<div class="listing-bg">
  <div class="container">
    <div class="row contact-bg-sec">
      <div class="col-sm-12 col-md-6 col-md-offset-3 for-small-size slideanim">
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
            <form action="{{route('requestquote')}}" method="post">
              {{ csrf_field() }}
              <div class="col-sm-12">
                <div class="form-group list-select ">
                  <label for="">Your Name*</label>
                  <input class="form-control input-area" placeholder="" type="text" name="name" required>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group list-select ">
                  <label for="">Company Name* </label>
                  <input class="form-control input-area" placeholder="" type="text" name="company_name" required>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group list-select ">
                  <label for="">Email Address* </label>
                  <input class="form-control input-area" placeholder=" " type="email" name="email" required>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group  ">
                  <label for="">Mobile Number (<span class="email-add">Include Country Code</span>)* </label>
                  <input class="form-control input-area" placeholder="" type="text" name="phone_no" required>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group  ">
                  <label for="">Skype </label>
                  <input class="form-control input-area" placeholder=" " type="text" name="skype_id">
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
                <button type="submit" name="submit" value="submit" class="send">SEND</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- blog section end -->
@include('layouts/partials.front.footer')