@include('layouts/partials.front.header-inner')
<!-- about section start -->

<style>

  .blog-section .blog-inner-sec:hover h2 {
    color: #f16022;
</style>

<div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h1 style="font-family: &quot;Montserrat&quot;,sans-serif;margin: 90px 0 30px;font-weight: bold;color: #fff;text-transform: uppercase;font-size: 36px;">
          Blogs </h1>
      </div>
    </div>
  </div>
</div>

<div class="blog-section">
  <div class="container">
    <div class="row">
      <!--  <div class="col-sm-12">
         <h2>Blogs</h2>
         <p>Latest Articles</p>
       </div> -->
      <div class="col-sm-4">
        <a href="{{ route('sales-tracking-app-in-nepal') }}" target="_blank" class="blog-inner-sec">
          <img src="{{ asset('assets/front/images/sales-tracking-app-in-nepal.jpg') }}"
               alt="sales-tracking-app-in-nepal" title="Sales Tracking App in Nepal">
          <h2 style="font-size: 18px;padding: 10px 20px;margin-top: 10px;margin-bottom: 10px;font-weight: 549;font-family:initial;">
            Sales Tracking App in Nepal</h2>
          <!--  <h4 style="font-family: initial;">Sales Tracking App in Nepal</h4> -->
          <span style="font-family: inherit;font-style: initial;">August 1, 2018</span>
          <p style="text-transform:  initial;">Digital Technology has built business to the global market in the world
            and many companies in Nepal are also trying to globalize their business for productive result.</p>
        </a>
      </div>
      <div class="col-sm-4">
        <a href="{{ route('lead-management-software-in-nepal') }}" target="_blank" class="blog-inner-sec">
          <img src="{{ asset('assets/front/images/lead-management-software-in-nepal.jpeg') }}"
               alt="lead-management-software-in-nepal" title="Lead Management Software in Nepal">
          <h2 style="font-size: 18px;padding: 10px 20px;margin-top: 10px;margin-bottom: 10px;font-weight: 549;font-family:initial;">
            Lead Management Software </h2>
          <span style="font-family: inherit;font-style: initial;">August 10, 2018</span>
          <p style="text-transform:  initial;">Leads are the business opportunity where someone shows interest in your
            product or services whereas sales are the result of converting lead into you profitable business.</p>
        </a>
      </div>
      <div class="col-sm-4">
        <a href="{{ route('performance-measuring-software-in-nepal') }}" target="_blank" class="blog-inner-sec">
          <img src="{{ asset('assets/front/images/performance-measuring-software-in-nepal.png') }}"
               alt="performance-measuring-software-in-nepal" title="Performance Measuring Software in Nepal">
          <h2 style="font-size: 18px;padding: 10px 20px;margin-top: 10px;margin-bottom: 10px;font-weight: 549;font-family:initial;">
            Performance Measuring Software </h2>
          <span style="font-family: inherit;font-style: initial;">August 22, 2017</span>
          <p style="text-transform:  initial;">The sales profession moves faster than ever today. In the blink of an
            eye, new competitors emerge, similar products are released, and before you know</p>
        </a>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <a href="{{ route('field-sales-management-software-in-nepal') }}" target="_blank" class="blog-inner-sec">
          <img src="{{ asset('assets/front/images/field-sales-management-software-in-nepal.png') }}"
               alt="field-sales-management-software-in-nepal" title="Field Sales Management Software in Nepal">
          <h2 style="font-size: 18px;padding: 10px 20px;margin-top: 10px;margin-bottom: 10px;font-weight: 549;font-family:initial;">
            Field Sales Management Software </h2>
          <span style="font-family: inherit;font-style: initial;">August 27, 2018</span>
          <p style="text-transform:  initial;">Sales app is also the field force tracking app which includes the
            performance of on-field sales and support staff, which makes the field </p>
        </a>
      </div>
      <div class="col-sm-4">
        <a href="{{ route('sales-management-software-in-nepal') }}" target="_blank" class="blog-inner-sec">
          <img src="{{ asset('assets/front/images/sales-management-software-in-nepal.png') }}"
               alt="sales-management-software-in-nepal" title="Sales Management Software in Nepal">
          <h2 style="font-size: 18px;padding: 10px 20px;margin-top: 10px;margin-bottom: 10px;font-weight: 549;font-family:initial;">
            Sales Management Software </h2>
          <span style="font-family: inherit;font-style: initial;">August 30, 2018</span>
          <p style="text-transform:  initial;">Sales management software is designed to help companies to combine their
            sales procedures and tasks, starting with leads and quotes and moving to conversions, along with reports</p>
        </a>
      </div>
      <div class="col-sm-4">
        <a href="{{ route('manage-order-in-nepal') }}" target="_blank" class="blog-inner-sec">
          <img src="{{ asset('assets/front/images/manage-order-in-nepal.jpg') }}" alt="manage-order-in-nepal"
               title="Manage Order in Nepal">
          <h2 style="font-size: 18px;padding: 10px 20px;margin-top: 10px;margin-bottom: 10px;font-weight: 549;font-family:initial;">
            Manage Order </h2>
          <span style="font-family: inherit;font-style: initial;">September 02, 2018</span>
          <p style="text-transform:  initial;">Managing order has always been one of the important factor to achieve
            success in business so every organization is required to have order management systeem.</p>
        </a>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <a href="{{ route('best-sales-tracking-application') }}" target="_blank" class="blog-inner-sec">
          <img src="{{ asset('assets/front/images/best-sales-tracking-application.png') }}"
               alt="best-sales-tracking-application" title="Best Sales Tracking Application">
          <h2 style="font-size: 18px;padding: 10px 20px;margin-top: 10px;margin-bottom: 10px;font-weight: 549;font-family:initial;">
            Best Sales Tracking Application</h2>
          <span style="font-family: inherit;font-style: initial;">September 16, 2018</span>
          <p style="text-transform:  initial;">In today's world, business is a passion and a source of income. But to
            run the business successfully, one must think on multiple aspects affecting it. </p>
        </a>
      </div>
    </div>


  </div>
</div>

@include('layouts/partials.front.footer')

<!-- <div class="col-sm-12">
            <a href="blog.html" class="view-all">View All</a>
          </div> -->