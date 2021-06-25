<div class="footer-section">
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-4 footer-bts">
        <div class="contact-sec">
          <img src="{{ asset('assets/front/images/logo.png') }}" alt="deltasalesapp-logo" title="Sales CRM In Nepal">
          <p>Delta Sales App is a solution developed by <a class="hiper" href="http://www.deltatechnepal.com/"> Delta
              Tech </a> to track salesmen in different types of industries like Pharmaceuticals, FMCG, Field Service,
            Tractors, Footwear & Garment Manufacturers in Nepal.</p>
        </div>

      </div>
      <div class="col-xs-12 col-sm-6 col-md-4 footer-bts">
        <div class="contact-sec">
          <h3>Get in touch</h3>
          <ul class="list-unstyled contacts">
            <li class="delta-address"><a href="https://goo.gl/maps/F8HQyhDuvMN2" target="_blank">Chand Kripa, Tinpaini
                Biratnagar-2, Nepal </a></li>
            <li class="phone-no"><a href="tel:+977 9802753996">+977-9802753996 </a></li>
            <li class="email-add"><a href="emailto:info@deltatechnepal.com">info@deltatechnepal.com</a></li>
            <!-- <li class="google-map"><a href="https://goo.gl/maps/nVTGcgtKLmq" target="_blank">Google Map</a></li> -->
          </ul>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="contact-sec">
          <!-- <h3>Social link</h3> -->
          <ul class="social">
            <li><a href="https://www.facebook.com/deltasalesapp/?modal=admin_todo_tour" target="_blank"><i
                    class="fab fa-facebook-f social-icons"></i></a></li>
            <!-- <li> <a href=""><i class="fab fa-instagram social-icons"></i></a> </li> -->
            <li><a href="https://twitter.com/deltasalesapp" target="_blank"><i class="fab fa-twitter social-icons"></i></a>
            </li>
            <li><a href="https://www.linkedin.com/company/deltasalesapp/" target="_blank"><i
                    class="fab fa-linkedin-in social-icons"></i></a></li>
          </ul>
          <div class=" copyright-sec">
            <ul class="footer-nav">
              <li><a href=".">Home</a></li>
              <li><a href="{{ route('feature') }}">Features</a></li>
              <li><a href="{{ route('pricing') }}">Pricing</a></li>
              <li><a href="{{ route('blog') }}">Blog</a></li>
              <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
              <!-- <li><a href="request-demo.php">Demo </a></li>  -->
            </ul>
            <ul class="footer-nav">
              <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
            </ul>
          </div>
        </div>
        <!--<div class="contact-sec">
          <h3>SUBSCRIBE</h3>
          <form>
            <div class="input-group subs-sec">
              <input type="text" class="form-control input-sec" placeholder="Enter Email">
                <span class="input-group-btn">
                  <button class="btn btn-search" type="submit">Join</button>
                </span>
              </div>
            </form>
        </div>-->
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 copyright-sec copyright-p">
        <p>Copyright © <?php echo date('Y')?> Delta Sales App. All rights reserved.</p>
      </div>
    </div>
  </div>
</div>
<!-- <div class="copyright">
   <div class="container">
     <div class="row">
        <div class="col-sm-12">
         <div class="contact-sec">
           <ul class="social">
             <li> <a href=""><i class="fab fa-facebook-f social-icons"></i></a> </li>
             <li> <a href=""><i class="fab fa-instagram social-icons"></i></a> </li>
             <li> <a href=""><i class="fab fa-twitter social-icons"></i></a> </li>
             <li> <a href=""><i class="fab fa-linkedin-in social-icons"></i></a> </li>
           </ul>
         </div>
       </div>
       <div class="col-sm-12 copyright-sec">
         <p>Copyright © 2018 Delta Sales App. All rights reserved.</p>
       </div>
     </div>
   </div>-->
</div>
<!-- script -->
<script src="{{ asset('assets/front/js/jquery-1.9.1.min.js') }}"></script>
<script src="{{ asset('assets/front/js/owl.carousel.js') }}"></script>
<script src="{{ asset('assets/front/js/bootstrap.min.js') }}"></script>
<!-- <script defer src="https://use.fontawesome.com/releases/v5.0.1/js/all.js"></script> -->
<script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js"
        integrity="sha384-3LK/3kTpDE/Pkp8gTNp2gR/2gOiwQ6QaO7Td0zV76UFJVhqLl4Vl3KL1We6q6wR9"
        crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/822a3e7c3e.js"></script>
<script type="text/javascript" src="{{ asset('assets/front/js/parallax.js') }}"></script>
<script type="text/javascript">

    $(".btn-refresh").click(function () {
        $.ajax({
            type: 'GET',
            url: "{{route('refresh_captcha')}}",
            success: function (data) {
                console.log(data);
                $(".captcha span").html(data);
            }
        });
    });


</script>
<script>
    $(document).ready(function () {

        //Parallx Design
        $('.cover-one').parallax("50%", 0.3);
        $('.cover-two').parallax("50%", 0.3);
        $('.cover-three').parallax("50%", 0.3);
        $('.cover-four').parallax("50%", 0.3);
        $('.cover-five').parallax("50%", 0.3);
        $('.cover-six').parallax("50%", 0.3);
    })
</script>
<script>
    $(document).ready(function () {
        $(".our-client-sec .owl-carousel").owlCarousel({
            autoPlay: 6000, //Set AutoPlay to 3 seconds       
            items: 6,
            margin: 10,
            itemsDesktop: [1199, 1],
            itemsDesktopSmall: [979, 1]
        });
    });
</script>
<script>
    $(document).ready(function () {
        $(".our-customer-section .owl-carousel").owlCarousel({
            autoPlay: 5000, //Set AutoPlay to 3 seconds       
            items: 1,
            itemsDesktop: [1199, 1],
            itemsDesktopSmall: [979, 1]
        });
    });
</script>


<script type="text/javascript">
    var $animation_elements = $('.slideanim');
    var $window = $(window);

    function check_if_in_view() {
        var window_height = $window.height();
        var window_top_position = $window.scrollTop();
        var window_bottom_position = (window_top_position + window_height);

        $.each($animation_elements, function () {
            var $element = $(this);
            var element_height = $element.outerHeight();
            var element_top_position = $element.offset().top;
            var element_bottom_position = (element_top_position + element_height);

            //check to see if this current container is within viewport
            if ((element_bottom_position >= window_top_position) &&
                (element_top_position <= window_bottom_position)) {
                $element.addClass('reveal');
            } else {
                $element.removeClass('reveal');
            }
        });
    }

    $window.on('scroll resize', check_if_in_view);
    $window.trigger('scroll');
</script>


<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/5b5465f0df040c3e9e0bd74e/default';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>
<!--End of Tawk.to Script-->

</body>
</html>