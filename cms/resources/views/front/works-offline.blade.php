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
              class="fas fa-exclamation-triangle"></i> Works Offline</h1>
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
        <strong><p style="text-align:center;"> No Internet ? Not to worry !</p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">For working offline, Delta Sales App is the solution. If
          there is no internet, Delta Sales app intelligently saves all the transaction and syncs it whenever internet
          coverage returns.</p>
        <h2>Benefits of Working Offline</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Manage all the sales activities without internet connection</li>
          <li><i class="far fa-hand-point-right"></i> Also be used in areas of poor internet connectivity</li>
          <li><i class="far fa-hand-point-right"></i> Save money and increase working time</li>
          <li><i class="far fa-hand-point-right"></i> No data loss</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time without an internet ? </h2>
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