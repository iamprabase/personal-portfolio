@include('layouts/partials.front.header')
<!-- about section start -->
<div class="tracking-section">
  <div class="container ">
    <div class="row">
      <div class="col-sm-12 slideanim">
        <h2 class="feature-title">Features</h2>
        <!-- <p>All of these you will get just for FREE</p> -->
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-map-marker-alt tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('real-time-gps-tracking') }}" style="text-decoration:double;">Real time GPS tracking</a>
          </h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-user-clock tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('maintain-attendance') }}" style="text-decoration:double;">Maintain attendance</a></h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-chart-line tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('manage-sales-expense') }}" style="text-decoration:double;">Manage sales expense</a>
          </h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-balance-scale tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('measure-sales-performance') }}" style="text-decoration:double;">Measure sales
              performance</h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="far fa-file-alt tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('leave-application') }}" style="text-decoration:double;">Leave application</h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fa fa-tasks tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('tasks-assignment') }}" style="text-decoration:double;">Tasks assignment</h3>
        </div>
      </div>

      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-address-book tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('manage-clients') }}" style="text-decoration:double;">Manage clients</h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-location-arrow tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('mark-client-location') }}" style="text-decoration:double;">Mark client location</h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="far fa-edit tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('manage-enquiries') }}" style="text-decoration:double;">Manage enquiries</h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-coins tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('manage-collections') }}" style="text-decoration:double;">Manage collections</h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-clipboard-list tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('manage-orders') }}" style="text-decoration:double;">Manage orders</h3>
        </div>
      </div>
      <div class="col-sm-3 slideanim">
        <div class="tracking tracking-home">
          <i class="fas fa-exclamation-triangle tracking-01 icon-mobile-view"></i>
          <h3><a href="{{ route('works-offline') }}" style="text-decoration:double;">Works offline</h3>
        </div>
      </div>
      <div class="col-sm-12 slideanim">
        <a href="{{ route('feature') }}" class="view-more">View More</a>
      </div>
    </div>
  </div>
</div>
<!-- about section end -->
<!-- how it work section start -->
<div class="cover-one coverphoto">
  <div class="work-section">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 slideanim">
          <h2>Why use sales app?</h2>
          <ul class="sales-app">
            <li> Monitor your salesmen by tracking their location</li>
            <li> Auto calculation of distance travelled by salesmen</li>
            <li> Efficient time management of salesmen</li>
            <li> Measure performance of salesmen by comparing with sales target</li>
            <li> Manage attendance with ease</li>
            <li> Share updates instantly thereby improving company sales</li>
            <li> Convert leads into sales</li>
            <li> No paperwork, eliminate printing cost</li>
          </ul>
          <!-- <a href="">Visit wenpanel demo</a> -->
        </div>
      </div>
    </div>
  </div>
</div>
<!-- client section start -->
<!-- our client start -->
<div class="our-clients-section our-client-sec">
  <div class="container slideanim">
    <div class="row">
      <div class="col-sm-12">
        <h2>Our Clients</h2>
      </div>
      <div class="col-sm-4 col-sm-offset-4">
        <div class="row">
          <div class="col-xs-6 col-sm-6">
            <div class="client-sec">
              <img src="{{ asset('assets/front/images/same-logo.png') }}">
            </div>
          </div>
          <div class="col-xs-6 col-sm-6">
            <div class="client-sec">
              <img src="{{ asset('assets/front/images/dommy.png') }}">
            </div>
          </div>
        </div>
      </div>
      <!-- <div  class="owl-carousel slideanim client-slider">
          <div class="item">
            <div class="client-sec">
                 <img src="images/same-logo.png">
            </div>
          </div>
          <div class="item">
            <div class="client-sec">
                 <img src="images/dommy.png">
            </div>
          </div>
          <div class="item">
            <div class="client-sec">
               <img src="images/same-logo.png">
            </div>
          </div>
          <div class="item">
            <div class="client-sec">
                 <img src="images/dommy.png">
            </div>
          </div>
          <div class="item">
            <div class="client-sec">
               <img src="images/same-logo.png">
            </div>
          </div>
          <div class="item">
            <div class="client-sec">
                 <img src="images/dommy.png">
            </div>
          </div>
           <div class="item">
            <div class="client-sec">
               <img src="images/same-logo.png">
            </div>
          </div>
          <div class="item">
            <div class="client-sec">
                 <img src="images/dommy.png">
            </div>
          </div>
        </div>-->
    </div>
  </div>
</div>
<!-- our client end -->
<div class="our-customer-section">
  <div class="container ">
    <div class="row">
      <div class="col-sm-12 col-md-8 col-md-offset-2 slideanim">
        <h2>Our Happy Customers</h2>
        <div class="owl-carousel slideanim">
          <div class="item">
            <div class="customer-sec">
              <img src="{{ asset('assets/front/images/user.png') }}">
              <h4>- Pankaj Agarwal -</h4>
              <p class="client"><i class="fas fa-quote-left"></i> DeltaSalesApp has helped our company in reducing
                expenses and increasing sales. Managing our sales employees has never been so easy. <i
                    class="fas fa-quote-right"></i></p>
            </div>
          </div>
          <div class="item">
            <div class="customer-sec">
              <img src="{{ asset('assets/front/images/user.png') }}">
              <h4>- Rahul Das -</h4>
              <p class="client"><i class="fas fa-quote-left"></i> I have been using this app to manage 20 salesman and
                I'm quite happy with it. <i class="fas fa-quote-right"></i></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- blog section end -->
@include('layouts/partials.front.footer')