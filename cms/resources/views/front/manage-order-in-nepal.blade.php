@include('layouts/partials.front.header-inner')
<!-- about section start -->
<style>
  .blog-section .blog-inner-sec {
    width: 93% !important;
  }

  .list {
    list-style: none;
    margin-left: -14px;
  }
</style>

<!-- <div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h2>Android Based salesman Tracking Application</h2>
      </div>
    </div>
  </div>
</div> -->

<div class="tracking-section">
  <div class="container ">
    <div class="row">
      <div class="col-sm-12 slideanim" style="margin-top: -20px;">
        <h1 class="feature-title"
            style="font-size: 30px;font-family: &quot;Montserrat&quot;, sans-serif;text-transform: uppercase;font-weight: bold;">
          Manage Order in Nepal</h1>
        <!-- <p>All of these you will get just for FREE</p> -->
      </div>
      <div class="col-sm-12  slideanim" style="text-align: justify;font-size: 14px;font-family: sans-serif;">
        <div class="tracking tracking-home">
          <p><strong><a href="{{ route('manage-orders') }}">Managing Order</a></strong> has always been one of the
            important factors to achieve success in business so every organization is required to have an order
            management system. It is a system that facilitates and manages the execution of orders through app taken
            from salesman or sales manager. It should be implemented to enhance communication between salesman, sales
            manager, and parties/clients.</p>

          <p><strong><a href="{{ route('manage-orders') }}">Order management</a></strong> is all about keeping track of
            orders and managing the people, processes, and partnerships required to fill them. It involves keeping track
            of the order itself and managing data around the customer.</p>

          <p>The process include:</p>
          <div class="col-sm-12">
            <ul class="list">
              <li><i class="far fa-hand-point-right"></i> The salesman /Sales Manager input the order received from
                parties/client.
              </li>
              <li><i class="far fa-hand-point-right"></i> The Sales Manager discuss the order with the parties/client
                and enter the order into the system to be filled.
              </li>
              <li><i class="far fa-hand-point-right"></i> The warehouse that carries the inventory to fill the order.
              </li>
              <li><i class="far fa-hand-point-right"></i> Salesman are informed about the delivery and amount to collect
                from the same parties/client.
              </li>
              <li><i class="far fa-hand-point-right"></i> After receiving the details, salesman leave a message of the
                amount received through app along with a picture of the cheque.
              </li>
            </ul>
          </div>

          <p>This makes the order management process simple and efficient.</p>

          <p>A <strong><a href="{{ route('measure-sales-performance') }}">Sales performance measuring
                system</a></strong> is mostly used for better sales mechanism which includes managing and measuring
            sales performance for better result and to grow sales. Thus, field sales tracking app helps to track the
            fields executives very easily.</p>

          <p><strong><a href="http://deltasalesapp.com">DeltaSalesApp</a></strong> provides one centralized place to
            <strong><a href="{{ route('manage-orders') }}">manage orders</a></strong> from all sales channels.
            Centralizing this in one system is critical to delivering a superior customer experience by providing order
            status, on-time deliveries and meeting customer expectations for buy, fulfill and return anywhere.</p>

          <p>This maintain happy customer relationships, and by eliminating lost and incorrect orders, helps to drive
            more revenue and profitability from the ordering process.
          <p>

        </div>
      </div>

      <div class="slideanim">
        <div class="row">
          <div class="col-sm-12">
            <h2 style="text-transform:unset;">Are you having a hard time managing your salesman, monitoring their work
              progress? </h2>
            <h3>Not Anymore!</h3>
          </div>
          <div class="col-sm-12 slideanim reveal">
            <a href="{{ route('request-demo') }}" class="view-more" style="text-transform:unset;">Start using
              DeltaSalesApp Now</a>
          </div>
        </div>
      </div>
    </div>

    <div class="blog-section" style="background: white;">
      <p class="feature-title" style="font-size: 27px;color:  black;font-family:serif;">Recent Blogs</p>
      <div class="row">
        <!--        <p>Latest Articles</p> -->
        <div class="col-sm-4">
          <a href="{{ route('best-sales-tracking-application') }}" target="_blank" class="blog-inner-sec">
            <img src="{{ asset('assets/front/images/best-sales-tracking-application.png') }}"
                 alt="best-sales-tracking-application" title="Best Sales Tracking Application">
            <h2 style="font-size: 18px;padding: 10px 20px;margin-top: 10px;margin-bottom: 10px;font-weight: 549;font-family:initial;">
              Best Sales Tracking Application</h2>
            <span style="font-family: inherit;font-style: initial;">September 16, 2018</span>
            <p style="text-transform:  initial;margin-top: -17px;">In today's world, business is a passion and a source
              of income. But to run the business successfully, one must think on multiple aspects affecting it. </p>
          </a>
        </div>
        <div class="col-sm-4">
          <a href="{{ route('sales-tracking-app-in-nepal') }}" target="_blank" class="blog-inner-sec">
            <img src="{{ asset('assets/front/images/sales-tracking-app-in-nepal.jpg') }}"
                 alt="sales-tracking-app-in-nepal" title="Sales Tracking App in Nepal">
            <h4 style="font-family: initial;">SALES TRACKING APP IN NEPAL </h4>
            <span style="font-family: inherit;font-style: initial;">August 1, 2018</span>
            <p style="text-transform:  initial;">Digital Technology has built business to the global market in the world
              and many companies in Nepal are also trying to globalize their business for productive result.</p>
          </a>
        </div>
        <div class="col-sm-4">
          <a href="{{ route('lead-management-software-in-nepal') }}" target="_blank" class="blog-inner-sec">
            <img src="{{ asset('assets/front/images/lead-management-software-in-nepal.jpeg') }}"
                 alt="lead-management-software-in-nepal" title="Lead Management Software in Nepal">
            <h4 style="font-family: initial;">Lead Management Software</h4>
            <span style="font-family: inherit;font-style: initial;">August 10, 2018</span>
            <p style="text-transform:  initial;">Leads are the business opportunity where someone shows interest in your
              product or services whereas sales are the result of converting lead into you profitable business.</p>
          </a>
        </div>
      </div>
    </div>
    <div class="col-sm-12 slideanim reveal" style="text-align:center;">
      <a href="{{ route('blog') }}" class="view-more">View More</a>
    </div>

  </div>
</div>

@include('layouts/partials.front.footer')