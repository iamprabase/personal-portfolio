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
              class="fas fa-signal"></i> Monthly Sales Target </h1>
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
        <strong><p style="text-align:center;">Setting specific sales target is simple but achieving it, is not an easy
            job. Are you not able to figure your sales goal ? </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with trageting sales, Delta
          Sales App is the solution. DeltaSalesApp helps achieving sales target by setting your sales goals, identifying
          the profit you need to make from each sale and the volume of sales you anticipate. Business that target the
          market clearly and accurately are more likely to achieve good sales figures.</p>
        <h2>Benefits of Setting Targets </h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Helps to achieve budgeted profit</li>
          <li><i class="far fa-hand-point-right"></i> Keeps sales team focused on achieving goals</li>
          <li><i class="far fa-hand-point-right"></i> Helps to choose right type of target</li>
          <li><i class="far fa-hand-point-right"></i> Increase employees performance</li>
          <li><i class="far fa-hand-point-right"></i> Maintain sustainable growth</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time targeting you salesman ? </h2>
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