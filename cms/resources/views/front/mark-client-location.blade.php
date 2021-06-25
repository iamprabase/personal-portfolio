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
              class="fas fa-location-arrow"></i> Mark Client Location</h1>
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
        <strong><p style="text-align:center;"> Clients are the real business maker of your company and knowing about
            details is always a plus point for any compnay. Are you having harder times tracking your possible client
            location?</p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with knowing client location,
          Delta Sales App is the solution. DeltaSalesApp offer complete location services of clients including their
          contact person name, address, phone number, email and many more.</p>
        <h2>Benefits of Marking Client Location</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Reach out client location easily</li>
          <li><i class="far fa-hand-point-right"></i> Make your business more successful</li>
          <li><i class="far fa-hand-point-right"></i> Bulid strong business client relationship</li>
          <li><i class="far fa-hand-point-right"></i> Establish environment of trust and comfort</li>
          <li><i class="far fa-hand-point-right"></i> Improve customer service</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time to know client location ? </h2>
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