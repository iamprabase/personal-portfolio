@include('layouts/partials.front.header-inner')
<div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h2>Signup</h2>
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
          <div class="row">
            <form>
              <div class="col-sm-6">
                <div class="form-group list-select ">
                  <label for="">First Name</label>
                  <input class="form-control input-area" placeholder="First Name " type="text">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group list-select ">
                  <label for="">Last Name </label>
                  <input class="form-control input-area" placeholder="Last Name " type="text">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group list-select ">
                  <label for="">Email Adress </label>
                  <input class="form-control input-area" placeholder="Email Adress " type="email">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group  ">
                  <label for="">Phone Number </label>
                  <input class="form-control input-area" placeholder="Phone Number " type="text">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group  ">
                  <label for="">Password </label>
                  <input class="form-control input-area" placeholder="Password " type="password">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group list-select ">
                  <label for="">Conform Password </label>
                  <input class="form-control input-area" placeholder="Conform Password " type="password">
                </div>
              </div>
              <div class="col-sm-12 agreesec01">
                <div class="checkbox checkme loginChe">
                  <input class="input-assumpte" id="reg" type="checkbox">
                  <label for="reg" class="registeragree"> I agree </label>
                  <a href="" class="reg">Terms &amp; Conditions</a>
                </div>
              </div>
              <div class="col-sm-12">
                <button type="submit" class="send">SIGN UP</button>
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