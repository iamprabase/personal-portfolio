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
              class="fas fa-coins"></i> Manage Collections</h1>
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
        <strong><p style="text-align:center;"> Are you facing harder times to manage collection? </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with collection management,
          Delta Sales App is the solution. In order to meet the emerging need of company, we offer collection
          management. DeltaSalesApp can help you to understand the total payment received and due, form a given client
          along with the amount of collection made by an salesman.</p>
        <h2>Benefits of Managing Collections</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Reduction of fraudulent activities</li>
          <li><i class="far fa-hand-point-right"></i> Instant confirmation of payment received</li>
          <li><i class="far fa-hand-point-right"></i> Create invoice quickly</li>
          <li><i class="far fa-hand-point-right"></i> Ensure accurate information</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time managing collections ? </h2>
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