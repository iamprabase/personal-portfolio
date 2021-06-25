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
              class="fas fa-th"></i> Manage Products</h1>
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
        <strong><p style="text-align:center;">Every company must organize their goods and product. How good are you in
            managing your product? </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with managing products, Delta
          Sales App is the solution. DeltaSalesApp manages your product with name, picture, price, description and
          discount rate. It guides the company on how to manage and invest in limited resources to reach the business
          goals.</p>
        <h2>Benefits of Managing Products</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Carry your product along with you in DeltaSalesApp</li>
          <li><i class="far fa-hand-point-right"></i> Improve business strategy</li>
          <li><i class="far fa-hand-point-right"></i> Know your product well</li>
          <li><i class="far fa-hand-point-right"></i> Better understanding of customer needs</li>
          <li><i class="far fa-hand-point-right"></i> Increase livelihood of your products</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time managing your products ? </h2>
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