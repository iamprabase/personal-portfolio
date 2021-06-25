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
              class="fas fa-calculator"></i> Travel Distance Calculator </h1>
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
        <strong><p style="text-align:center;"> Don't you know the start and end point of your salesman ? Still unable to
            calulate the distance travelled by your salesman ? <br/>
            No worries ! </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with travel distance calculation
          of salesman, Delta Sales App is the solution. DeltaSalesApp auto calculate the distance travelled by your
          salesman and let you know the start and end point of travel. </p>
        <h2>Benefits of Travel Distance Calculation </h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Know the start and end point</li>
          <li><i class="far fa-hand-point-right"></i> Be more efficient with tracking</li>
          <li><i class="far fa-hand-point-right"></i> Know where your salesman visited</li>
          <li><i class="far fa-hand-point-right"></i> Improve customer service</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time calculating distance travelled by salesman ? </h2>
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