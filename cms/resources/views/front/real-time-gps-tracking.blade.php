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
              class="fas fa-map-marker-alt"></i> Real Time GPS Tracking</h1>
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
        <strong><p style="text-align:center;">Are you having a problem with tracking your salesman during office hour ?
            Constantly worried about your sales people location, path, distance......Not anymore ! </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with tracking salesman, Delta
          Sales App is the solution. DeltaSalesApp tracks your sales people in real time and gives you exact location
          where your salesman are. You can know the path and distance traveled by them along with gps coordinates.</p>
        <h2>Benefits of Real time GPS tracking </h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Useful to follow your salesman, employee</li>
          <li><i class="far fa-hand-point-right"></i> Track and monitor your salesman in real time</li>
          <li><i class="far fa-hand-point-right"></i> Know where your salesman are</li>
          <li><i class="far fa-hand-point-right"></i> Delivery more value to your client/parties</li>
          <li><i class="far fa-hand-point-right"></i> Manage salesman and work flow efficiently</li>
          <li><i class="far fa-hand-point-right"></i> Works offline</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time tracking your salesman in real time ? </h2>
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