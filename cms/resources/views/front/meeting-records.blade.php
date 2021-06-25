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
              class="fas fa-file-invoice"></i> Meeting Records</h1>
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
        <strong><p style="text-align:center;">It's very important to summarized your meetings in a productive way. Are
            you able to note an important feedbacks and convert them into sales ? </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with summarizing meeting
          records, Delta Sales App is the solution. DeltaSalesApp tracks your salesman meetings and enable them to
          summarize their meeting in better format, enhancing productivity.</p>
        <h2>Benefits of Meeting Records </h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Maintaining meeting records drive leads and sales</li>
          <li><i class="far fa-hand-point-right"></i> Gives direction to your plan</li>
          <li><i class="far fa-hand-point-right"></i> Helps in maintaining reports</li>
          <li><i class="far fa-hand-point-right"></i> Better understanding of clients / parties requirement</li>
          <li><i class="far fa-hand-point-right"></i> Enhace your follow up</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time maintaining meeting records ? </h2>
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