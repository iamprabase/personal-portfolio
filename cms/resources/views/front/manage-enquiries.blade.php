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
              class="far fa-edit"></i> Manage Enquiries</h1>
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
        <strong><p style="text-align:center;"> Converting an enquiry into real business often depends on how you handle
            it. Did you face problem managing the enquiries? Received many enquiries but still unable to connect to
            customers and convert them? </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with enquiry management, Delta
          Sales App is the solution. DeltaSalesApp can help you to manage your customer enquiries and complaints. It
          determines if the enquiry is genuine, filters the genuine enquiries, research about the company and ask for
          the details about the enquirer’s business.</p>
        <h2>Benefits of Managing Enquiries</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Better Communication</li>
          <li><i class="far fa-hand-point-right"></i> Keep better tracks of interaction online</li>
          <li><i class="far fa-hand-point-right"></i> Increase leads and sales</li>
          <li><i class="far fa-hand-point-right"></i> Increase customer base</li>
          <li><i class="far fa-hand-point-right"></i> Improve customer service</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time while managing enquiries ? </h2>
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