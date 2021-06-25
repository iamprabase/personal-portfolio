@include('layouts/partials.front.header-inner')
<style type="text/css">

</style>
<div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h2>Login</h2>
      </div>
    </div>
  </div>
</div>
<!-- banner end -->
<div class="listing-bg">
  <div class="container">
    <div class="row contact-bg-sec">

      <div class=" loging-sec">
        <div class="col-sm-12">
          <h2>Log in to your DeltaSalesApp account</h2>
          <!--<h4>Please enter your DeltaSalesApp domain name</h4>-->
        </div>
        <div class="col-sm-12 col-md-6 col-md-offset-3 for-small-size slideanim">
          <div class="row">
            <form method="POST" class="form-inline" id="go_to_app">
              <div class="col-xs-12 col-sm-12">
                {{ csrf_field() }}
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-addon glove"><i class="fas fa-globe"></i></div>
                    <div class="group">
                      <input type="text" id="subdomain" name="subdomain" required>
                      <span class="highlight"></span>
                      <span class="bar"></span>
                      <label>Your Domain Name</label>
                    </div>
                    <div class="input-group-addon">.deltasalesapp.com</div>
                  </div>
                  <span id="errormessage" style="display: none;color: red;"><small>The domain you have entered does not exist.</small></span>
                </div>
                <button type="button" class="send" id="submit">PROCEED &nbsp; <i id="loader"
                                                                                 class="fa fa-circle-notch fa-spin"
                                                                                 style="display: none;"></i></button>
              </div>
            </form>
            <div class="col-sm-12 forget-domain">
              <!--<a href="">Forgot your Deltateam domain name?</a>-->
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- blog section end -->
@include('layouts/partials.front.footer')

<script>
    $("#submit").click(function () {
        $("#errormessage").hide();
        var subdomain = $('#subdomain').val();
        var csrf_token = "{{ csrf_token() }}";
        if (subdomain.length > 0) {
            $("#loader").show();
            $.ajax({
                type: "POST",
                url: "{{route('checksubdomain')}}",
                data: {"subdomain": subdomain, "_token": csrf_token},
                success: function (data) {
                    $("#loader").hide();
                    if (data == 1) {
                        $("#errormessage").hide();
                        // $('#go_to_app').submit();
                        window.location.assign("http://" + subdomain + ".{{config('app.domain')}}/login");
                    } else if (data == 0) {
                        $("#errormessage").show();
                    }
                }
            });
        }
    });
    $("#subdomain").keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
</script>