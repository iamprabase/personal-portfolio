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
              class="fas fa-user-clock"></i> Maintain Attendance</h1>
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
        <strong><p style="text-align:center;">Tracking attendance of your salesman daily is very time consuming and any
            company can't keep on looking into the arrival and departure time of salemen. We have an appropriate
            solution ! </p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with maintaining attendance of
          your salesman, Delta Sales App is the best solution. DeltaSalesApp maintain effective attendace system and let
          you know the check in and check ouut time of your employees.You can also calculate the working hour throug
          this app in ease.</p>
        <h2>Benefits of Maintaining Attendance </h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Reliable and accurate data</li>
          <li><i class="far fa-hand-point-right"></i> Managers can identify potential attendance issues</li>
          <li><i class="far fa-hand-point-right"></i> Efficient planning and shift management</li>
          <li><i class="far fa-hand-point-right"></i> Attendance tracking ensures employees get paid in full and on time
          </li>
          <li><i class="far fa-hand-point-right"></i> Improve performance management and develop a strong company
            culture
          </li>
          <li><i class="far fa-hand-point-right"></i> Keep employees informed, satisfied and increase productivity</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time maintaining attendance of your salesman ? </h2>
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