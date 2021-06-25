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
              class="fas fa-address-book"></i> Manage Clients</h1>
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
        <strong><p style="text-align:center;"> Customers are the real king of the market and fulfilling their needs and
            desires is our only goal. Are you failing to meet the customer’s expectation and losing them?</p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with client management, Delta
          Sales App is the solution. DeltaSalesApp won’t let you miss any clients. You can assign and track the client
          queries to the nearest salesman or employee. It also keeps the record of all the clients and update it if
          needed.</p>
        <h2>Benefits of Managing Clients</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Better client relationship</li>
          <li><i class="far fa-hand-point-right"></i> Quick and easy way to access clients</li>
          <li><i class="far fa-hand-point-right"></i> Better coordination and cooperation</li>
          <li><i class="far fa-hand-point-right"></i> Focused marketing efforts</li>
          <li><i class="far fa-hand-point-right"></i> Increase revenue and profitability</li>
          <li><i class="far fa-hand-point-right"></i> Better understanding of client requirement
          <li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time managing your clients ? </h2>
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