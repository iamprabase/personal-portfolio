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
              class="fa fa-tasks"></i> Tasks Assignment </h1>
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
        <strong><p style="text-align:center;"> Creating a task assignment is not as easy as we assume. If
            responsibilities are not assigned, the outcome of the project becomes unclear and often incomplete
            sometimes.</p></strong>

        <p style="margin: 31px 0px 0px 0px;text-align:justify;">Whatever be the problem with tasks assignment, Delta
          Sales App is the solution. DeltaSalesApp allows a unique way of task management where your sales team can
          easily setup a project, assign and share task. Having a clear understanding of task helps to achieve
          organizational goals in short period of time.</p>
        <h2>Benefits of Tasks Assignment</h2>
        <ul class="list">
          <li><i class="far fa-hand-point-right"></i> Assign responsibility to sales person</li>
          <li><i class="far fa-hand-point-right"></i> Prioritize task / team collaboration</li>
          <li><i class="far fa-hand-point-right"></i> Reduce stress</li>
          <li><i class="far fa-hand-point-right"></i> Ensure customer satisfaction</li>
          <li><i class="far fa-hand-point-right"></i> Improves productivity</li>
          <li><i class="far fa-hand-point-right"></i> Increase Efficiency</li>
          <li><i class="far fa-hand-point-right"></i> Reduced time and energy spent on paperwork</li>
        </ul>

        <div class="col-sm-12 slideanim reveal" style="text-align:center;font-family: serif;">
          <h2 style="text-transform:unset;">Having a hard time with assigning tasks ? </h2>
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