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
              class="fas fa-list-ol"></i> Add Daily Remarks </h1>
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
        <strong><p style="text-align:center;"> Facing problem with long chain of email and details ? Not Anymore</p>
        </strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with adding daily remarks, Delta
          Sale App is the solution. DeltaSalesApp eliminates writing long chain of email to your sales manager. This
          saves your time and enhaces your communication. </p>
        <h2>Benefits of Adding Daily Remarks </h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Useful to follow your salesmen, employee</li>
          <li><i class="far fa-hand-point-right"></i> Track and monitor your salesmen in real time</li>
          <li><i class="far fa-hand-point-right"></i> Know where your salesmen are</li>
          <li><i class="far fa-hand-point-right"></i> Delivery more value to your client/parties</li>
          <li><i class="far fa-hand-point-right"></i> Manage salesmen and work flow efficiently</li>
          <li><i class="far fa-hand-point-right"></i> Works offline</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time adding daily remarks ? </h2>
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