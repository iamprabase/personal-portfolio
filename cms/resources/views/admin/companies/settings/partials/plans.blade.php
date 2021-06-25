<div class="col-xs-12">
  <h3 class="site-tital">Site Information</h3>
</div>
<div class="row info">
  <div class="col-xs-4 item-sec">
    <div class="riw-item">
      <span class="riw-top">{{ $plan->name }}</span>
      <span class="riw-bottom">Plan Used</span>
    </div>
  </div>
  <div class="col-xs-4 item-sec">
    <div class="riw-item">
      <span class="riw-top">{{ $company->num_users }}</span>
      <span class="riw-bottom">Number of User Allowed</span>
    </div>
  </div>
  <div class="col-xs-4 item-sec">
    <div class="riw-item">
      <span class="riw-top">{{ $active_users }}</span>
      <span class="riw-bottom">Number of Active Users</span>
    </div>
  </div>
  <div class="col-xs-4 item-sec">
    <div class="riw-item">
      <span class="riw-top">{{ $inactive_users }}</span>
      <span class="riw-bottom"> Number of Inactive Users</span>
    </div>
  </div>
  {{-- <div class="col-xs-4 item-sec">
    <div class="riw-item">
        <span class="riw-top">18</span>
        <span class="riw-bottom"> Number of Archived Users</span>
    </div>
  </div> --}}
  <div class="col-xs-4 item-sec">
    <div class="riw-item"
         style="@if($company->num_users - ($active_users) <= 0 ) 'background: lightgreen;' @else 'background: red;' @endif ">
      <!--<span class="riw-top">0</span>-->
      <span class="riw-bottom"> @if($company->num_users - ($active_users) <= 0 )
          You have reached maximum number of users
        @else
          You have {{$company->num_users - ($active_users) }} users left
        @endif</span>
    </div>
  </div>
</div>