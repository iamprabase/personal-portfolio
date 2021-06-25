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
              class="far fa-file-alt"></i> Leave Application</h1>
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
        <strong><p style="text-align:center;"> Managing employee leave application is very crucial for any
            manager.Employees generally need workoff, time and again due to their personal work or problem. Are you
            unknown of your employee availability and maintaining leave procedure? </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with leave application
          management, Delta Sales App is the solution. DeltaSalesApp allows salesman, employee to apply for leaves,
          check leave balance, approved leaves etc. It tracks leave data accurately which is used for automated payroll
          processing and tracking salesman leave.</p>
        <h2>Benefits of Leave Application Management</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Track leave of your salesman</li>
          <li><i class="far fa-hand-point-right"></i> Automated leave entries and records</li>
          <li><i class="far fa-hand-point-right"></i> Employees can plan their leave if approved</li>
          <li><i class="far fa-hand-point-right"></i> Know employee availability</li>
          <li><i class="far fa-hand-point-right"></i> Improve Communication</li>
          <li><i class="far fa-hand-point-right"></i> Encourages discipline in company</li>
          <li><i class="far fa-hand-point-right"></i> Reduced time and energy spent on paperwork</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time managining leave application ? </h2>
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