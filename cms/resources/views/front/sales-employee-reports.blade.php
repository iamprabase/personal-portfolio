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
              class="fas fa-user-edit"></i></i> Sales Employee Reports</h1>
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
        <strong><p style="text-align:center;">Do you receive the reports of your sales team in time ? Don’t you think
            it’s important to keep the record of your salesman ?</p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with sales employee reports,
          Delta Sales App is the solution. DeltaSalesApp helps you to provide the right reporting of salesman that
          showcase the most important information like working hours, distance travelled, GPS location, orders and
          collection. This report plays an important role in helping manager to oversee the success of the salesman,
          sales team and eventually the whole company.</p>
        <h2>Benefits of Sales Employee Reports </h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Quick review on salesman and employee reports</li>
          <li><i class="far fa-hand-point-right"></i> Gathering useful information in one place</li>
          <li><i class="far fa-hand-point-right"></i> Tracking of salesman activities</li>
          <li><i class="far fa-hand-point-right"></i> Improve analysis and decision-making</li>
          <li><i class="far fa-hand-point-right"></i> Manage workflow accordingly and efficiently</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time maintaining sales employee reports ? </h2>
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