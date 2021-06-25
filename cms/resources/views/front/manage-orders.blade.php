@include('layouts/partials.front.header-inner')

<style>
  .list {
    list-style: none;
  }
</style>

<div class="banner pricing01">
  <div class="container">
    <div class="row slideanim">
      <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h1 style="font-family: &quot;Montserrat&quot;, sans-serif;font-weight: bold;color: #ffffff;font-size: 36px;"><i
              class="fas fa-clipboard-list"></i> Manage Orders</h1>
      </div>
    </div>
  </div>
</div>

<!-- banner end -->
<div class="listing-bg ">
  <div class="container">
    <div class="row contact-bg-sec">
      <!--       <div class="col-sm-12 col-md-10 col-md-offset-5"> -->
      <div class="col-md-12" style="font-family: &quot;Montserrat&quot;, sans-serif;font-size: 15px;color: #010e0e;">
        <strong><p style="text-align:center;"> Managing order can be surprisingly complex challenge. Are you having hard
            time managing track of your orders? </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with order management, Delta
          Sales App is the solution. DeltaSalesApp organizes sales orders and customers. It includes tracking of orders
          from customers, processing orders and notifies when reached to destination. </p>
        <h2>Benefits of Managing Orders</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Real time order status for better customer service</li>
          <li><i class="far fa-hand-point-right"></i> Quick delivery and resonse</li>
          <li><i class="far fa-hand-point-right"></i> Enhance customer relationship</li>
          <li><i class="far fa-hand-point-right"></i> Improvement in order tracking</li>
          <li><i class="far fa-hand-point-right"></i> Ensure accurate information</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time managing Orders ? </h2>
          <h3>Not Anymore!</h3>
          <a href="{{ route('request-demo') }}" class="view-more" style="text-transform:unset;font-size: 21px;">Start
            using DeltaSalesApp Now</a>
        </div>
      </div>
    </div>
  </div>
</div>
</div>


@include('layouts/partials.front.footer')