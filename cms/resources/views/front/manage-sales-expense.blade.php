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
              class="fas fa-chart-line"></i> Manage Sales Expense</h1>
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
        <strong><p style="text-align:center;"> Does your salesman ask for more expenses than you expect? Are you facing
            loss rather than profit in your company? </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with manging sales expense,
          Delta Sales App is the solution. DeltaSalesApp tracks salesman daily, weekly, monthly and yearly expenses in
          real time and provide you the clear picture of expenses done by respective salesman. You can track the bills
          of sales and expenses too.</p>
        <h2>Benefits of Tracking Sales Expense</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Know where money is going</li>
          <li><i class="far fa-hand-point-right"></i> Best way to set and meet financial goals</li>
          <li><i class="far fa-hand-point-right"></i> Spend on your top priorities</li>
          <li><i class="far fa-hand-point-right"></i> Reveals bad spending habits of salesman</li>
          <li><i class="far fa-hand-point-right"></i> Awareness of fraud of unknown charge</li>
          <li><i class="far fa-hand-point-right"></i> Take control</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time tracking your sales expense ? </h2>
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