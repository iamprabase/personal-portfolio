@include('layouts/partials.front.header-inner')
<div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h1 style="font-family: &quot;Montserrat&quot;,sans-serif;margin: 90px 0 30px;font-weight: bold;color: #fff;text-transform: uppercase;font-size: 36px;">
          Login </h1>
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
          <div class="row">
            <form>
              <div class="col-xs-12 col-sm-12">
                <div class="form-group list-select ">
                  <label for="">Username </label>
                  <input class="form-control input-area" placeholder="Username " type="text">
                </div>
              </div>
              <div class="col-xs-12 col-sm-12">
                <div class="form-group list-select ">
                  <label for="">Password </label>
                  <input class="form-control input-area" placeholder="Password" type="password">
                </div>
              </div>
              <div class="col-xs-6 col-sm-6">
                <div class="checkbox checkme loginChe">
                  <!-- <input class="input-assumpte" id="1" type="checkbox"> -->
                  <!-- <label for="">Stay signed in</label> -->
                  <a href="{{ route('forgot-password') }}" class="forget">Forgot Password</a>
                </div>
              </div>
              <div class="col-xs-6 col-sm-6"><span class="no-ac">No Account ? <a href="{{ route('request-demo') }}">Sign Up</a></span>
              </div>
              <div class="col-xs-12 col-sm-12">
                <button type="submit" class="send">LOGIN</button>
              </div>
              <!--<div class="col-sm-12">
              <section class="seperator text-center or-sec"><span> OR </span></section>
              <h4 class="signwith">Sign In with</h4>
              </div>
              <div class="col-sm-12 login-for-social">
                <a href="" class="googlelogin"> <i class="icon ion-social-googleplus-outline"></i> Googleplus</a>
              </div>-->
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- blog section end -->
@include('layouts/partials.front.footer')