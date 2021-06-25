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
              class="fas fa-bullhorn"></i> Announcement </h1>
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
        <strong><p style="text-align:center;">Lacking to notify your salesman about the official information ! Are your
            salesman able to receive the formal announcement given by the company ?</p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with announcement, Delta Sales
          App is the solution. DeltaSalesApp officially announces and declares the information about the company that
          has to be known by salesman, employee or any updates about anything. </p>
        <h2>Benefits of Announcement </h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Keep your saleman, employee up to date</li>
          <li><i class="far fa-hand-point-right"></i> Maintain transparency in a company</li>
          <li><i class="far fa-hand-point-right"></i> Improve communication</li>
          <li><i class="far fa-hand-point-right"></i> Go paperless</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time during announcement ? </h2>
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